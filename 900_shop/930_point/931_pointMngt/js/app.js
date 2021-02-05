const POINT_URL = 'act/pointMngt_act.php';
let CURR_PAGE = 1;
let tdLength = 0;
let listArray = [];
let setIdxArray = []; //선택한 인덱스
let RPT_ID_BILL = '60210'; //수리서 적립아이디

const registPopupWidth = 490;  
let popupX = 0;
let popupY = 0;

$(document).ready(function() {
  tdLength = $('table tr th').length;
  setInitTableData(tdLength);
  // setPointList();

  const date = new Date();
  const TODAY = dateFormat(date);
  const START_MONTH_DATE = date.getFullYear()+"-"+fillZero((date.getMonth()+1)+"",2)+"-"+"01"
  $("#START_DT_SEARCH").val(START_MONTH_DATE);
  $("#END_DT_SEARCH").val(TODAY);
  
  $( "#START_DT_SEARCH").datepicker(
      getDatePickerSetting('END_DT_SEARCH', 'minDate')
  );
  $( "#END_DT_SEARCH").datepicker(
      getDatePickerSetting('START_DT_SEARCH', 'maxDate')
  );
  setCRR_ID_Option('CSD_ID_SEARCH', 'CRR_ID_SEARCH', true);
  getDivisionOption('CSD_ID_SEARCH', true); 
  setCRR_ID_Option('CSD_ID_SEARCH', 'CRR_ID_SEARCH', true);


  if($('REQ_MODE').val() != CASE_CREATE){
    getRefOptions(GET_REF_CONTRACT_TYPE, 'RCT_ID_SEARCH', false, false);           //거래여부 
    getRefOptions(REF_POINT_TYPE, 'RPT_ID_SEARCH', false, false);           //포인트종류  
    getRefOptions(REF_POINT_STATUS, 'RPS_ID_SEARCH', false, false);           //포인트상태
    setGwOptions('MGG_NM_SEARCH',undefined,'=공업사=');
    /* 처리 모달  */
    getRefOptions(REF_POINT_STATUS, 'RPS_ID', false, false);           //포인트상태
  }

  $('#CSD_ID_SEARCH').change(()=>{
    if($('#CSD_ID_SEARCH').selectpicker('val') != null){
        setCRR_ID_Option('CSD_ID_SEARCH', 'CRR_ID_SEARCH', true, true);
    }else{
        $('#CSD_ID').selectpicker('val' , '');
        $('#CSD_ID').selectpicker('refresh');
        setCRR_ID_Option('CSD_ID_SEARCH', 'CRR_ID_SEARCH', true);
    }

  });

  /** 검색 */
  $("#searchForm").submit(function(e){
      e.preventDefault();
      getList(1);
  });

  // 전체선택
  $("#allcheck").click(function(){
      if($("#allcheck").prop("checked")){
          $("input[type=checkbox].listcheck").prop("checked", true);
      }else{
          $("input[type=checkbox].listcheck").prop("checked", false);
      }
  });

  popupX = (screen.availWidth - registPopupWidth) / 2;
  if( window.screenLeft < 0){
      popupX += window.screen.width*-1;
  }
  else if ( window.screenLeft > window.screen.width ){
      popupX += window.screen.width;
  }
  popupY= (window.screen.height / 2) - (550 / 2);

  /* 포인트 처리 */
  $('#procForm').submit(function(e){
    e.preventDefault();
    confirmSwalTwoButton("처리 하시겠습니까?",'확인', '취소', false , ()=>{       
      setData();
    });
  });

});


openPopup = () =>{
  window.open('./pointMngtModal.html?REQ_MODE='+CASE_CREATE, 
  'pointwindow', 
  'width='+registPopupWidth+', height=550,  left='+ popupX + ', top='+ popupY); 
}

getList = (currentPage) => {
  CURR_PAGE = (currentPage == undefined) ? CURR_PAGE : currentPage;
  let form = $("#searchForm")[0];
  let formData = new FormData(form);
  formData.append('REQ_MODE', CASE_LIST);
  formData.append('PER_PAGE', $("#perPage").val());
  formData.append('CURR_PAGE', CURR_PAGE);

  callFormAjax(POINT_URL, 'POST', formData).then((result)=>{
      // console.log('getList SQL >>',result);
      const res = JSON.parse(result);
      // console.log( 'getList >> ',res);
      if(res.status == OK){
          const item = res.data;
          setTable(item[1], item[0], CURR_PAGE);  
      }else{
          setHaveNoDataTable();
      }
  });  
}

  
setTable = (totalListCount, dataArray, currentPage) =>{
  let historyDataText = '';
  listArray = dataArray;
  dataArray.map((data,index)=>{
    // console.log('index>>>',index,' ',data );
        let chkBox ='';
        let procBtn ='';
        let billBtn ='';
        let cancelBtn ='';

        if(data.RPT_ID == RPT_ID_BILL){
          billBtn = "  <button type='button' class='btn btn-default' onclick='openBillPopup(\""+data.NB_ID+"\",\""+data.MGG_ID+"\")'>청구</button>";
        }
        if(data.RPS_ID.substring(data.RPS_ID.length, data.RPS_ID.length-1) == '0' && data.NPH_CANCEL_YN == YN_N){
          chkBox = "<input type='checkbox' class='listcheck' name='multi' value='"+index+"'>";
          procBtn = "<button type='button' class='btn btn-default' id='add-new-btn' onclick='onClickProcess("+index+")'>확정</button>";
        }
        if( data.NPH_CANCEL_YN == YN_N){
          cancelBtn ="<button type='button' class='btn btn-danger' id='add-new-btn' onclick='onCancel("+index+")'>취소</button>";
        }
        // console.log(data.NPH_CANCEL_YN,' ',cancelBtn);
        historyDataText += "<tr>";
        historyDataText += "<td class='text-center'>"+chkBox+"</td>";
        historyDataText += "<td class='text-center'>"+data.IDX+"</td>";
        historyDataText += "<td class='text-center'>"+data.RCT_NM+"</td>";
        historyDataText += "<td class='text-center'>"+data.CSD_NM+"</td>";
        historyDataText += "<td class='text-left'>"+data.CRR_NM+"</td>";
        historyDataText += "<td class='text-center'>"+bizNoFormat(data.MGG_BIZ_ID)+"</td>";
        historyDataText += "<td class='text-left'>"+data.MGG_NM+"</td>";
        historyDataText += "<td class='text-center'>"+nullChk(data.RET_NM,'-')+"</td>";
        historyDataText += "<td class='text-left'>"+data.MGGS_ADDR+"</td>";
        historyDataText += "<td class='text-center'>"+phoneFormat(data.MGG_OFFICE_TEL)+"</td>";
        historyDataText += "<td class='text-center'>"+phoneFormat(data.MGG_OFFICE_FAX)+"</td>";
        historyDataText += "<td class='text-center'>"+data.RPT_NM+billBtn+"</td>";
        historyDataText += "<td class='text-center'>"+data.RPS_NM+(data.NPH_CANCEL_YN == YN_Y? "(취소)" : "")+"</td>";
        historyDataText += "<td class='text-left'>"+nullChk(data.NPH_PARTS_MEMO)+"</td>";
        historyDataText += "<td class='text-right'>"+numberWithCommas(data.NPH_POINT)+"</td>";
        historyDataText += "<td class='text-right'>"+numberWithCommas((data.BALANCE))+"</td>";
        historyDataText += "<td class='text-left'>"+(data.NPH_CANCEL_MEMO != null ? "취소: "+data.NPH_CANCEL_MEMO : "")+(data.NPH_MEMO != null  && data.NPH_MEMO != ''? "<br>"+data.NPH_MEMO : "")+"</td>";
        historyDataText += "<td class='text-left'>"+(data.WRT_USER_NM != null?data.WRT_USER_NM+"("+data.WRT_USERID+")" : '-') +"</td>";
        historyDataText += "<td class='text-center'>"+nullChk(data.NPH_ISSUE_DT)+"</td>";
        historyDataText += "<td class='text-center'>"+nullChk(data.LAST_DTHMS)+"</td>";
        historyDataText += "<td class='text-center'>";
        historyDataText += procBtn;
        historyDataText += cancelBtn;
        historyDataText += "</td>";
        historyDataText += "</tr>"
      });
  
      paging(totalListCount, currentPage, 'getList'); 
  $('#dataTable').html(historyDataText);
}

/** 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
  const tableText = "<tr><td class='text-center' colspan='"+tdLength+"'>조회된 데이터가 존재하지 않습니다.</tr></tr>";
  $('.pagination').html('');
  $('#dataTable').html(tableText);
}

/* 처리버튼 클릭 */
onClickProcess = (idx) =>{
  $('#procForm').each(function(){
    this.reset();
  })
  $('.tmpInput').remove();
  setIdxArray = [];

  if(idx == undefined){
    if($('input[name=multi]:checked').length > 0){
      $('input[name=multi]:checked').each(function(){
          if($(this).val() != ''){
            setIdxArray.push($(this).val());
          }
      });
    }else {
      alert('선택된 포인트가 없습니다.');
      return false;
    }
  }else{
    $('#RPS_ID').val(listArray[idx].RPS_ID);
    setIdxArray.push(idx);
  }

  let htmlTxt = ''
  setIdxArray.map((idx)=>{
    htmlTxt += "<input type='hidden' class='tmpInput' name='NPH_ID[]' id='NPH_ID' value="+listArray[idx].NPH_ID+">";
    htmlTxt += "<input type='hidden' class='tmpInput' name='RPS_ID[]' id='RPS_ID' value="+listArray[idx].RPS_ID+">";
  })
  $('#procForm').append(htmlTxt);
  confirmSwalTwoButton("확정 하시겠습니까?",'확인', '취소', false , ()=>{       
    setData();
  });
  // $('#process-modal').modal();
}


setData = () =>{
  let form = $("#procForm")[0];
  let formData = new FormData(form);
  formData.append('REQ_MODE', CASE_UPDATE);
  callFormAjax(POINT_URL, 'POST', formData).then((result)=>{
      if(isJson(result)){
          const data = JSON.parse(result);
          if(data.status == OK){
              confirmSwalOneButton('포인트 처리가 완료되었습니다.', OK_MSG,()=>{
                  getList();
                  $('#process-modal').modal('hide');
              });
          }else{
              alert(ERROR_MSG_1100);
          } 
      }else{
          alert(ERROR_MSG_1200);
      }
  });   
}

onCancel = (idx) =>{
  $('#procForm').each(function(){
    this.reset();
  })
  $('.tmpInput').remove();
  setIdxArray = [];
  $('h4').html('포인트 취소');
  $('#MEMO_LABEL').html('취소메모');
  $('#DIV_RPS').hide();
  $('#process-modal').modal();
  let htmlTxt = ''
  // setIdxArray.map((idx)=>{
    htmlTxt += "<input type='hidden' class='tmpInput' name='NPH_ID[]' id='NPH_ID' value="+listArray[idx].NPH_ID+">";
    htmlTxt += "<input type='hidden' class='tmpInput' name='RPS_ID[]' id='RPS_ID' value="+listArray[idx].RPS_ID+">";
  // })
  $('#procForm').append(htmlTxt);
  $('#submitBtn').attr('onClick','cancelData()');

  
}

cancelData = (idx) =>{

  confirmSwalTwoButton("취소 하시겠습니까?",'확인', '취소', false , ()=>{       
    const param = {
      REQ_MODE: CASE_DELETE,
      NPH_ID: $('#NPH_ID').val(),
      NPH_CANCEL_MEMO: $('#NPH_MEMO').val(),
    }
    // console.log('cancel Param>>>',param);
    callAjax(POINT_URL, 'POST', param).then((result)=>{
          const data = JSON.parse(result);
          if(data.status == OK){
              confirmSwalOneButton('취소가 완료되었습니다.', OK_MSG,()=>{
                  getList();
                  $('#process-modal').modal('hide');
              });
          }else{
              alert(ERROR_MSG_1100);
          } 
    });  
  
  });
}

excelDownload = () => {
  location.href="pointMngtExcel.html?"+$("#searchForm").serialize();
}


openBillPopup = (NB_ID, MGG_ID) => {
  window.open('/200_billMngt/210_writeBill/writeBill.html?NB_ID='+NB_ID+'&MGG_ID='+MGG_ID,  'newwindow', 'width=1500,height=1000');
}


onClickAdd = () =>{
  window.open('/900_shop/930_point/931_pointMngt/pointMngtAddPopup.html', 
    'addPopup', 
    'width=515, height=700, left='+ popupX + ', top='+ popupY); 
}

/**
 * 지역구분에 해다되는 주소 옵션
 * @param {objID : 조회할 지역구분 정보}
 * @param {targetID : 조회한 구분을 넣어줄 select ID}
 * @param {isMultiSelector : 멀티셀렉터 인지 아닌지}
 */
setCRR_ID_Option = (objId, targetID, isMultiSelector, isTotal) => {
  getCRRInfo($('#'+objId).val(), isTotal).then((data)=>{
      if(data.status == OK){
          const dataArray = data.data;
          if(dataArray.length > 0){
              let optionText = '';
      
              dataArray.map((item, index)=>{
                  let ITEM_ID = item.CRR_1_ID+","+item.CRR_2_ID+","+item.CRR_3_ID;
                  optionText += '<option value="'+ITEM_ID+'">'+item.CRR_NM+'</option>';
              });
  
              $('#'+targetID).html(optionText);
              // console.log("optionTextoptionText==>",optionText);
              // console.log("optionTextoptionText==>",targetID);
              
              if(isMultiSelector){
                  $('#'+targetID).selectpicker('refresh');
              }
          }else{
              $('#'+targetID).html('');
              
              if(isMultiSelector){
                  $('#'+targetID).selectpicker('refresh');
              }
          }
      }
  });
}
