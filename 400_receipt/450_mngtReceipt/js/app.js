const MNGTRECEIPT_URL = 'act/mngtReceipt_act.php';
let CURR_PAGE = 1; 
let tdLength = 0;
let partCnt = 0;
$(document).ready(function() {
  /* 검색조건 */
  $( "#DT_KEY_SEARCH" ).datepicker(
      getDatePickerSetting()
  );
  $( "#START_DT_SEARCH").datepicker(
      getDatePickerSetting('END_DT_SEARCH', 'minDate')
  );
  $( "#END_DT_SEARCH").datepicker(
      getDatePickerSetting('START_DT_SEARCH', 'maxDate')
  );
  /** 기간키: 청구일, 기간: 오늘날짜 디폴트  */
  const date = new Date();
  const TODAY = dateFormat(date);
  const START_MONTH_DATE = date.getFullYear()+"-"+fillZero((date.getMonth()+1)+"",2)+"-"+"01"
  $("#START_DT_SEARCH").val(START_MONTH_DATE);
  $("#END_DT_SEARCH").val(TODAY);

  setStockYear('YEAR_SEARCH','=해당년=');        
  setStockMonth('MONTH_SEARCH','=해당월=');
  getDivisionOption('CSD_ID_SEARCH', true);                                      //지역구분
  setCRR_ID_Option('CSD_ID_SEARCH', 'CRR_ID_SEARCH', true);
  getRefOptions(GET_REF_EXP_TYPE, 'RET_ID_SEARCH', true, false);                 //청구식
  setGwOptions('MGG_NM_SEARCH',undefined,'=공업사=');
  getRefOptions(GET_STOCK_TYPE, 'RST_ID_SEARCH', false, false);           // 변동원인

  $('#YEAR_SEARCH').change(function() {
    $('#START_DT_SEARCH').val($('#YEAR_SEARCH').val()+'-01-01');
    $('#END_DT_SEARCH').val($('#YEAR_SEARCH').val()+'-12-31');
  });

  $('#MONTH_SEARCH').change(function() {
    if($('#YEAR_SEARCH').val() == '' ){
      alert('해당년을 선택 후 설정하세요.');
      $('#MONTH_SEARCH').val('');
      return false;
    }else {
      $('#START_DT_SEARCH').val($('#YEAR_SEARCH').val()+'-'+$('#MONTH_SEARCH').val()+'-01');
      $('#END_DT_SEARCH').val($('#YEAR_SEARCH').val()+'-'+$('#MONTH_SEARCH').val()+'-'+new Date( $('#YEAR_SEARCH').val(), $('#MONTH_SEARCH').val(), 0).getDate());  
    }
   })
  
  setTableHeader();

  // 전체선택 체크
  $("#allcheck").click(function(){
    if($("#allcheck").prop("checked")){
        $("input[name=approveBox]").prop("checked", true);
    }else{
        $("input[name=approveBox]").prop("checked", false);
    }
  });

  /** 검색 */
  $("#searchForm").submit(function(e){
      e.preventDefault();
      getList(1);          
  });

});

/* 테이블 헤더 셋팅 */
setTableHeader = () => {
  const data = {
    REQ_MODE: GET_HEADER_CNT,
  }
  callAjax(MNGTRECEIPT_URL, 'GET', data).then((result)=>{
      // console.log('HEADER_CNT RESULT>',result);
      const res = JSON.parse(result);
      // console.log('HEADER_CNT res>',res);
      if(res.status == OK){
        // $('#indicator').hide();
          const itemCols = res.data;
          // $('#dataHeader th').remove();
          let hederTxt ='';
          hederTxt += ""
          
          itemCols.map((data, index)=>{
            hederTxt += "<th class='text-center' id='"+data.CPPS_ID+"'>"+data.CPPS_NM_SHORT+"</th>";
          })
          hederTxt += "<th class='text-center'>승인/수정/삭제</th>";
          hederTxt += "<th class='text-center'>승인여부</th>";
          hederTxt += "<th class='text-center'>승인일</th>";
          $('#dataHeader').append(hederTxt);
          partCnt = itemCols.length;
          tdLength = 12 + partCnt;
          setInitTableData(tdLength);
      }else{
        $('#dataHeader').delete();
        alert(ERROR_MSG_NO_DATA);
      }
  });
}


/**
 * 메일리스트 조회
 * @param {int} currentPage  조회하는 페이지
 */
getList = (currentPage) =>{
  if($('#START_DT_SEARCH').val() !='' || $('#END_DT_SEARCH').val() != '' ){
    if($('#DT_KEY_SEARCH').val() == ''){
      alert('등록기간 키필드를 선택해주세요.');
      return false;
    }
  }
  if($('#DT_KEY_SEARCH').val() != ''){
    if($('#START_DT_SEARCH').val() == '' || $('#END_DT_SEARCH').val() == ''){
      alert('등록기간을 설정해주세요.');
      return false;
    }
  }

  CURR_PAGE = (currentPage==undefined) ? CURR_PAGE : currentPage;
  const param = {
    REQ_MODE: CASE_LIST,
    PER_PAGE:  $("#perPage").val(),
    CURR_PAGE:  CURR_PAGE,

    /** 검색 */
    DT_KEY_SEARCH: $('#DT_KEY_SEARCH').val(),
    START_DT_SEARCH: $('#START_DT_SEARCH').val(),
    END_DT_SEARCH: $('#END_DT_SEARCH').val(),
    CSD_ID_SEARCH: $('#CSD_ID_SEARCH').val(),
    CRR_ID_SEARCH: $('#CRR_ID_SEARCH').val(),
    MGG_NM_SEARCH: $('#MGG_NM_SEARCH').val(),
    MGG_BIZ_ID_SEARCH: $('#MGG_BIZ_ID_SEARCH').val(),
    RET_ID_SEARCH: $('#RET_ID_SEARCH').val(),
    RST_ID_SEARCH: $('#RST_ID_SEARCH').val(),
    EXCETION_SEARCH: $('#EXCETION_SEARCH').is(":checked") 
  }
  // console.log('getList param >>>',param);
  callAjax(MNGTRECEIPT_URL, 'GET', param).then((result)=>{
      console.log('getList RESULT>',result);
      const res = JSON.parse(result);
      // console.log('getList res>',res);
      if(res.status == OK){
        $('#indicator').hide();
        // $('#dataTable td').remove();
          const item = res.data[0];
          let tableTxt = '';
          item.map((data, i)=>{
            let summaryTxt ='';
            let approved = '승인';
            let chkTxt = '';
            let approvedBtn = '';

            if(data.NIH_APPROVED_YN == YN_N){
              approved = '미승인';
              chkTxt ="<input type='checkbox' id='"+data.MGG_ID+"_"+data.NIH_DT+"_"+data.RST_ID+"' name='approveBox'>";
              approvedBtn = "<button type='button' class='btn btn-primary' onclick='setApprove("+JSON.stringify(data)+", false)'>승인</button>";
            }

            tableTxt += "<tr id='tr_"+data.MGG_ID+"_"+data.NIH_DT+"_"+data.RST_ID+"'>";
            tableTxt += "<td>"+chkTxt+"</td>"; // 체크박스
            tableTxt += "<td class='text-center'>"+data.IDX+"</td>";
            tableTxt += "<td class='text-center'>"+data.NIH_DT+"</td>";
            tableTxt += "<td class='text-center'>"+data.CSD_NM+"</td>";
            tableTxt += "<td class='text-left'>"+data.CRR_NM+"</td>";
            tableTxt += "<td class='text-left'>"+data.MGG_NM+"</td>";
            tableTxt += "<td class='text-center'>"+bizNoFormat(data.MGG_BIZ_ID)+"</td>";
            tableTxt += "<td class='text-center'>"+(data.RET_NM == null ? '' : data.RET_NM)+"</td>";
            tableTxt += "<td class='text-center'>"+data.RST_NM+"</td>";

            if(data.SUMMARY.length ==  partCnt){
              data.SUMMARY.map((item,i)=>{
                tableTxt += "<td class='text-right'>"+item.NIS_AMOUNT+"</td>";
              });
            }else {
              tableTxt += "<td class='text-left' colspan='"+partCnt+"'>"+data.MGG_NM+"의 재고가 올바르지 않습니다.</td>";
            }
            
            tableTxt += "<td>"+approvedBtn;
            if($("#AUTH_UPDATE").val() == YN_Y){
              tableTxt += "<button type='button' class='btn btn-default' onclick='showUpdatePopup(\""+data.MGG_ID+"\",\""+data.MGG_NM+"\",\""+data.NIH_DT+"\",\""+data.RST_ID+"\")'>수정</button>";
            }
            if($("#AUTH_DELETE").val() == YN_Y){
              tableTxt += "<button type='button' class='btn btn-danger' onclick='delData("+JSON.stringify(data)+")'>삭제</button>";
            }
            tableTxt += "</td>";
            tableTxt += "<td>"+approved+"</td>";
            tableTxt += "<td>"+data.NIH_APPROVED_DTHMS+"</td>";
            


            tableTxt += "</tr>";
          })
          paging(res.data[1], CURR_PAGE, 'getList'); 
          $("#dataTable").html(tableTxt);
      }else{
        const tableText = "<tr><td class='text-center' colspan='"+tdLength+"'>조회요청한 데이터가 존재하지 않습니다.</td></tr>";
        $('.pagination').html('');
        $('#dataTable').html(tableText);
      }
  });

}


/**
 * 변동일자별 공업사 입출고 기록 조회
 * @param {array} data 공업사 기본 정보
 */
getSummaryTable = (data) => {
  let param ={
    REQ_MODE:CASE_LIST_SUB,
    MGG_ID: data.MGG_ID,
    NIH_DT: data.NIH_DT,
  };
  // console.log('summary>>', param.MGG_ID, '/', param.NIH_DT);
  callAjax(MNGTRECEIPT_URL, 'GET', param).then((result)=>{
    // console.log('CASE_SUBLIST query',result);
    const res = JSON.parse(result);
    // console.log('CASE_SUBLIST res>',res);
    
    if(res.status == OK){
      const arrItem = res.data;
      let summaryTxt ='';
      let approved = '승인';
      let chkTxt = '';
      arrItem.map((item,i)=>{
        summaryTxt += "<td class='text-right'>"+item.NIS_AMOUNT+"</td>";
      });
      
      summaryTxt += "<td>";
      if(data.NIH_APPROVED_YN == YN_N){
        approved = '미승인';
        chkTxt ="<input type='checkbox' id='"+data.MGG_ID+"_"+data.NIH_DT+"_"+data.RST_ID+"' name='approveBox'>";
        summaryTxt += "<button type='button' class='btn btn-primary' onclick='setApprove("+JSON.stringify(data)+", false)'>승인</button>";
      }
      if($("#AUTH_UPDATE").val() == YN_Y){
        summaryTxt += "<button type='button' class='btn btn-default' onclick='showUpdatePopup(\""+data.MGG_ID+"\",\""+data.MGG_NM+"\",\""+data.NIH_DT+"\",\""+data.RST_ID+"\")'>수정</button>";
      }
      if($("#AUTH_DELETE").val() == YN_Y){
        summaryTxt += "<button type='button' class='btn btn-danger' onclick='delData("+JSON.stringify(data)+")'>삭제</button>";
      }
      summaryTxt += "</td>";
      summaryTxt += "<td>"+approved+"</td>";
      summaryTxt += "<td>"+data.NIH_APPROVED_DTHMS+"</td>";
      summaryTxt += "<td>"+chkTxt+"</td>";
      $('#tr_'+data.MGG_ID+'_'+data.NIH_DT+'_'+data.RST_ID).append(summaryTxt);
    }else{
      alert(data.MGG_NM+'의 재고가 올바르지 않습니다.');
    }


  });
}

/**
 * 승인처리
 * @param {array} data 공업사 기본정보
 * @param {boolean} type 일괄처리 여부  true: 일괄처리 / false: 개별처리
 */
setApprove = (data, type) => {
  if(!type){ // 승인처리
    confirmSwalTwoButton(
      data.MGG_NM+' 입출고를 승인하시겠습니까?', 
      OK_MSG, 
      CANCEL_MSG, 
      false,
      ()=>{
        const param = {
            REQ_MODE: CASE_UPDATE,
            MGG_ID: data.MGG_ID,
            NIH_DT: data.NIH_DT,
            RST_ID: data.RST_ID
        };
        // console.log('del param>>',param);
        callAjax(MNGTRECEIPT_URL, 'POST', param).then((result)=>{
            // console.log('delSub SQL>>',result);
            const res = JSON.parse(result);
                if(res.status == OK){
                    confirmSwalOneButton('승인 되었습니다.','확인',()=>{
                        getList();
                    });
                }else{
                    alert(ERROR_MSG_1200);
                } 
        });
  
      }
    );
  }else { //일괄승인
    let arrChk = $("input[name=approveBox]:checked").map(function(){
      return $(this).attr('id');
    }).get();
    let updateCnt = 0;
    // console.log('arrChk length',arrChk.length);
    if(arrChk.length == 0){
      alert('선택된 입출고가 없습니다.');
    }else {
      confirmSwalTwoButton(
        '입출고를 일괄승인하시겠습니까?', 
        OK_MSG, 
        CANCEL_MSG, 
        false,
        ()=>{
          arrChk.map((item, i)=>{
            let strItemSplit = item.split('_');
            let mggId = strItemSplit[0];
            let nihId = strItemSplit[1];
            let rstId = strItemSplit[2];
      
            const param = {
                REQ_MODE: CASE_UPDATE,
                MGG_ID: mggId,
                NIH_DT: nihId,
                RST_ID: rstId
            };
          
            callAjax(MNGTRECEIPT_URL, 'GET', param).then((result)=>{
                const data = JSON.parse(result);
                if(data.status == OK){
                  updateCnt ++;
                  if(i+1 == arrChk.length){
                    alert(updateCnt+'개 입출고가 승인되었습니다.');
                    getList();
                    $("#allcheck").prop("checked", false);
                  }
                }else{
                    alert(ERROR_MSG_1100);
                } 
            });
          })
    
        }
      );
    }
  }
  
}

/* 입출고등록 팝업창 */
showAddPopup = () => {
  window.open('/400_receipt/401_enrolReceipt/enrolReceipt.html?type=add', 
                  'enrolReceipt', 
                  'width=1500,height=920'); 
}

/* 입출고등록 수정 팝업창 */
showUpdatePopup = (id,nm,nih,rst) => {
  window.open('/400_receipt/401_enrolReceipt/enrolReceipt.html?type=update&MGG_ID='+id+'&MGG_NM='+nm+'&RST_ID='+rst+'&NIH_DT='+nih, 
                  'enrolReceipt', 
                  'width=1500,height=920'); 
}

/**
 * 입출고 삭제
 * @param {array} data 공업사 기본정보
 */
delData = (data) =>{
  confirmSwalTwoButton(
    data.MGG_NM+' '+data.NIH_DT+' '+data.RST_NM+'데이터를 '+CONFIRM_DELETE_MSG, 
    DEL_MSG, 
    CANCEL_MSG, 
    true,
    ()=>{
      const param = {
          REQ_MODE: CASE_DELETE,
          MGG_ID: data.MGG_ID,
          NIH_DT: data.NIH_DT,
          RST_ID: data.RST_ID
      };
      // console.log('del param>>',param);
      callAjax(MNGTRECEIPT_URL, 'POST', param).then((result)=>{
          // console.log('delSub SQL>>',result);
          const res = JSON.parse(result);
              if(res.status == OK){
                  confirmSwalOneButton('삭제에 성공하였습니다.','확인',()=>{
                      getList();
                  });
              }else{
                  alert(ERROR_MSG_1200);
              } 
      });
    }
  );
}

/* 엑셀다운로드 */
excelDownload = () => {
  location.href="mngtReceiptExcel.html?"+$("#searchForm").serialize();

}