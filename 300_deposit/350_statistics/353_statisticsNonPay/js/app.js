let NON_URL = '/300_deposit/350_statistics/353_statisticsNonPay/act/statisticsNon_act.php'; 
let CURR_PAGE = 1;
let arrChk = [];
let listArray = [];
const DEPOSIT_URL = '/300_deposit/310_depositMngt/act/depositMngt_act.php';
const UNPAID = '40121';
let BASE_DEPOSIT_DATE = '';

$(document).ready(function() {

    tdLength = $('table th').length;
    getList(1);
    
    getDivisionOption('CSD_ID_SEARCH', true);                                              //구분
    setCRR_ID_Option('CSD_ID_SEARCH', 'CRR_ID_SEARCH', true);
    getRefOptions(GET_REF_CLAIM_TYPE, 'RLT_ID_SEARCH', false, false);              //담보
    

    /* 구분에 따른 지역 변경 */
    $('#CSD_ID').change(()=>{
        if($('#CSD_ID').val() != ''){
            setCRR_ID('CSD_ID', 'CRR_ID', 'SELECT_CRR_ID', true);
        }else{
            $('#CRR_ID').val('');
            $('#SELECT_CRR_ID').val('');
            $('#CRR_ID').attr('disabled',true);
        }
    });

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
    
    $("#NBI_INSU_DEPOSIT_DT").datepicker({
        dateFormat: 'yy-mm-dd',
    });
    $("#NBI_INSU_ADD_DEPOSIT_DT").datepicker({
        dateFormat: 'yy-mm-dd',
    });
    $("#NBI_OTHER_DT").datepicker({
        dateFormat: 'yy-mm-dd',
    });

    setReasonSearch();
    //사유입력 별 추가입력항목
    $(".insu-hidetd").hide();
    $(".date-hidetd").hide();
    $(".ratio-hidetd").hide();
    $(".price-hidetd").hide();
    $(".memo-hidetd").hide();

    $('#REASON_SELECT').change(()=>{
        console.log('reason select arr >>>>', arrReason);
        let selectReason = arrReason.filter((data) => {
            return $('#REASON_SELECT').val() == data.ID;
        });
        console.log('selectReason>>', selectReason[0]) ;
        if(selectReason[0] == undefined){
            $("#NBI_OTHER_MII_ID").val('');
            $(".insu-hidetd").hide();
            $("#NBI_OTHER_RATIO").val('');
            $(".ratio-hidetd").hide();
            $("#NBI_OTHER_DT").val('');
            $(".date-hidetd").hide();
            $("#NBI_OTHER_PRICE").val('');
            $(".price-hidetd").hide();
            $("#NBI_OTHER_MEMO").val('');
            $(".memo-hidetd").hide();
        }else {
            if(selectReason[0].EXT1_YN == YN_Y ){
                $(".insu-hidetd").show();
            }else {
                $("#NBI_OTHER_MII_ID").val('');
                $(".insu-hidetd").hide();
            }
            if(selectReason[0].EXT2_YN == YN_Y){
                $(".ratio-hidetd").show();
            }else {
                $("#NBI_OTHER_RATIO").val('');
                $(".ratio-hidetd").hide();
            }
            if(selectReason[0].EXT3_YN == YN_Y){
                $(".date-hidetd").show();
            }else {
                $("#NBI_OTHER_DT").val('');
                $(".date-hidetd").hide();
            }
            if(selectReason[0].EXT4_YN == YN_Y){
                $(".price-hidetd").show();
            }else {
                $("#NBI_OTHER_PRICE").val('');
                $(".price-hidetd").hide();
            }
            if(selectReason[0].EXT5_YN == YN_Y){
                $(".memo-hidetd").show();
            }else {
                $("#NBI_OTHER_MEMO").val('');
                $(".memo-hidetd").hide();
            }
        }
    });


    /** 등록/수정  */
    $("#depositForm").submit(function(e){
        e.preventDefault();
        if($("input[name=RADIO_DEPOSIT]:checked").length == 0 ){
        alert('입금여부를 선택해주세요');
        return false;
        }else if($("input[name=RADIO_DEPOSIT]:checked").val() != '40121'){ //입금여부 : 입금
        if($('#NBI_INSU_DEPOSIT_PRICE').val() == '' ){
            alert('입금의 경우 지급액은 필수 입니다.');
            return false;
        }
        if($('#NBI_INSU_DEPOSIT_DT').val() == '' || $('#NBI_INSU_DEPOSIT_DT').val() == '0000-00-00' ){
            alert('입금의 경우 지급일은 필수 입니다.');
            return false;
        }
        }else if($('#NBI_INSU_DEPOSIT_PRICE').val() != '' && $('#NBI_INSU_DEPOSIT_DT').val() == '' ){
        alert('지급일은 필수 입니다.');
        return false;
        }else if($('#NBI_INSU_DEPOSIT_PRICE').val() == '' && ($('#NBI_INSU_DEPOSIT_DT').val() != '' || $('#NBI_INSU_DEPOSIT_DT').val() == '0000-00-00') ){
        alert('지급액은 필수 입니다.');
        return false;
        }
        confirmSwalTwoButton('저장 하시겠습니까?',OK_MSG, '취소', false ,()=>setData());
    });

});


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
              console.log("optionTextoptionText==>",optionText);
              console.log("optionTextoptionText==>",targetID);
              
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

excelDownload = () => {
  location.href="nonPaymentExcel.html?"+$("#searchForm").serialize();
}



/**
 * MAIN LIST 
 * @param {int} currentPage  조회하는 페이지
 */
getList = (currentPage) =>{

  
  CURR_PAGE = (currentPage == undefined) ? CURR_PAGE : currentPage;
  let form = $("#searchForm")[0];
  let formData = new FormData(form);
  formData.append('REQ_MODE', CASE_LIST);
  formData.append('PER_PAGE', $("#perPage").val());
  formData.append('CURR_PAGE', CURR_PAGE);
  
  callFormAjax(NON_URL, 'POST', formData).then((result)=>{
      console.log('getList SQL >>',result);
      const res = JSON.parse(result);
      console.log( 'getList >> ',res);
      if(res.status == OK){
          $(".indecator").hide();
          const item = res.data;
          setTable(item[1], item[0], CURR_PAGE);  
          
      }else{
          $(".indecator").hide();
          setHaveNoDataTable();

      }
     
  });  
}

/** 메인 리스트 테이블 동적으로 생성 
* @param {totalListCount: 총 리스트데이터 수, dataArray : 리스트 데이터, currentPage : 현재페이지(페이징을 위해)} 
*/
setTable = (totalListCount, dataArray, currentPage) => {
  let tableText = '';
  listArray = dataArray;
  dataArray.map((data, index)=>{
     
      tableText += "<tr>";
      tableText += "  <td class='text-center'><input type='checkbox' name='multi' value='"+index+"'></td>";
      tableText += "  <td class='text-center'>"+data.IDX+"</td>";
      tableText += "  <td class='text-center'>"+data.NB_CLAIM_DT+"</td>";
      tableText += "  <td class='text-center'>"+data.CSD_NM+"</td>";
      tableText += "  <td class='text-center'>"+data.CRR_NM+"</td>";
      tableText += "  <td class='text-center'>"+data.MGG_NM+"</td>";
      tableText += "  <td class='text-center'>"+bizNoFormat(data.MGG_BIZ_ID)+"</td>";
      tableText += "  <td class='text-left'>"+data.MII_NM+"</td>";
      tableText += "  <td class='text-center'>"+nullChk(data.MIT_NM)+"</td>";
      tableText += "  <td class='text-left'>"+data.NIC_NM+"</td>";

      tableText += "  <td class='text-center'>"+data.NBI_REGI_NUM+"</td>";
      tableText += "  <td class='text-center'>"+data.RLT_NM+"</td>";
      tableText += "  <td class='text-center'>"+data.NB_CAR_NUM+"</td>";
      tableText += "  <td class='text-center'>"+data.CCC_NM+"</td>";
      tableText += "  <td class='text-center'>"+numberWithCommas(data.NB_TOTAL_PRICE)+"</td>";
      tableText += "  <td class='text-center'><button type='button' class='btn btn-default' onclick='reSendFax("+index+")'>재전송</button></td>";
      tableText += "  <td class='text-center'><button type='button' class='btn btn-default' onclick='showTransitModal("+index+")'>이첩</button></td>";
      tableText += "  <td class='text-center'><button type='button' class='btn btn-default' onclick='showDepositModal("+JSON.stringify(data)+")'>입금</button></td>";

      tableText += "</tr>       ";
  });
  paging(totalListCount, currentPage, 'getList'); 
  
  $('#dataTable').html(tableText);
}

/** 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
   
  const tableText = "<tr><td class='text-center' colspan='"+tdLength+"'>조회된 데이터가 존재하지 않습니다.</tr></tr>";
  $('.pagination').html('');
  $('#dataTable').html(tableText);
}



/**
 * 일괄이첩
 */
showTransitModal = (idx) => { 
  let transInsuArray = [];
  if(idx == undefined){
      if($('input[name=multi]:checked').length > 0){
              $('input[name=multi]:checked').each(function(){
                  if($(this).val() != ''){
                      transInsuArray.push($(this).val());
                  }
              });
              setTransInsu(transInsuArray);
      }else{
          alert("선택된 항목이 없습니다.");
      }
  }else{
      transInsuArray.push(idx);
      setTransInsu(transInsuArray);
  } 
}


/** 이첩팝업 띄우기 */
setTransInsu = (transInsuArray) => {
  let form = $('#sendTransForm')[0];
  let htmlTxt = ''

  if(transInsuArray.length > 0){
      transInsuArray.map((idx)=>{
          const data = listArray[idx];
          console.log('transData>>>',data);
          htmlTxt += "<input type='hidden' name='NB_ID[]' value="+data.NB_ID+">";
          htmlTxt += "<input type='hidden' name='NBI_RMT_ID[]' value="+data.NBI_RMT_ID+">";
          htmlTxt += "<input type='hidden' name='NIC_ID[]' value="+data.NIC_ID+">";
          htmlTxt += "<input type='hidden' name='MII_ID[]' value="+data.MII_ID+">";
          htmlTxt += "<input type='hidden' name='INSU_TEAM_NM[]' value="+data.INSU_TEAM_NM+">";
          htmlTxt += "<input type='hidden' name='NBI_FAX_NUM[]' value="+data.NBI_FAX_NUM+">";
          htmlTxt += "<input type='hidden' name='NB_CLAIM_DT[]' value="+data.NB_CLAIM_DT+">";
          htmlTxt += "<input type='hidden' name='NBI_REGI_NUM[]' value="+data.NBI_REGI_NUM+">";
          htmlTxt += "<input type='hidden' name='MII_NM[]' value="+data.MII_NM+">";
          htmlTxt += "<input type='hidden' name='NBI_CHG_NM[]' value="+data.NBI_CHG_NM+">";
          htmlTxt += "<input type='hidden' name='NB_CAR_NUM[]' value="+data.NB_CAR_NUM+">";
          htmlTxt += "<input type='hidden' name='NB_TOTAL_PRICE[]' value="+data.NB_TOTAL_PRICE+">";
      });
      const popupHeight = transInsuArray.length == 1 ? 700 : 400;
      window.open('', 
          'transwindow', 
          'width=515,height='+popupHeight); 
      form.action = '/200_billMngt/220_sendList/sendListTransitPopup.html';
      form.target = 'transwindow';
      form.method = 'POST';
      $('#sendTransForm').html(htmlTxt);
      
      form.submit();
  }  
}



/**
 * 팩스재전송(일괄재전송)
 */
reSendFax = (idx) => {
  let sendFaxArrayIdx = [];
  if(idx == undefined){
      if($('input[name=multi]:checked').length > 0){
          const msg = "해당 "+$('input[name=multi][name=multi]:checked').length+"건을 재전송 하시겠습니까?";
          confirmSwalTwoButton(msg,'확인', '취소', false , ()=>{       
              $('input[name=multi]:checked').each(function(){
                  if($(this).val() != ''){
                      sendFaxArrayIdx.push($(this).val());
                  }
              });                         
              sendFax(sendFaxArrayIdx);
          });
      }else{
          alert("선택된 항목이 없습니다.");
      }
  }else{
      confirmSwalTwoButton("재전송 하시겠습니까?",'확인', '취소', false , ()=>{       
          sendFaxArrayIdx.push(idx);
          sendFax(sendFaxArrayIdx);
      });
     
  }
}




/** 팩스전송 */
sendFax = (sendFaxArray) => {
  let form = $("#faxForm")[0];
  let formData = new FormData(form);
  let sendResultCount = sendFaxArray.length;
  let sendResultSuccess = 0;
  let sendResultFail = 0;

  if(sendFaxArray.length > 0){
      sendFaxArray.map((idx)=>{
          const data = listArray[idx];

          formData.append('NB_ID', data.NB_ID);
          formData.append('MGG_ID', data.MGG_ID);
          formData.append('NBI_FAX_NUM', data.NBI_FAX_NUM);
          formData.append('NBI_FAX_RMT_ID', data.NBI_RMT_ID);
          // formData.append('NBI_FAX_NUM', '0221799114');
          formData.append('NBI_CHG_NM', data.NBI_CHG_NM);
          formData.append('FAX_RFS_ID', data.RFS_ID);
          formData.append('CAR_NUM', data.NB_CAR_NUM);
          formData.append('MII_NM', data.MII_NM);
          callFormAjax('../../../sendFAX.php', 'POST', formData).then((result)=>{
              if(result){
                  sendResultSuccess++;
              }else{
                  sendResultFail++;
              }

              if(sendResultCount == (sendResultSuccess+sendResultFail)){
                  const msg = "성공 : "+sendResultSuccess+"건 , 실패 : "+sendResultFail+"건 입니다.";
                   confirmSwalOneButton(msg, OK_MSG,()=>{
                      getList();
                  });
              }
          });
      });  
  }
  
}