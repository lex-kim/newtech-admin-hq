const DEPOSIT_URL = 'act/depositMngt_act.php';
const ajaxUrl = '/200_billMngt/220_sendList/act/sendList_act.php';
let CURR_PAGE = 1; 
let BASE_DEPOSIT_DATE = '';
const UNPAID = '40121';
const ALL = 'all';
let arrChk = [];
let listArray = [];
let clickDelBtnIdx = "";    // true이면 일괄삭제, false이면 그냥 삭제
let pdfPopup = null;   // 팝업 변수
let doc = null;        // pdf다운 변수

$(document).ready(function() {
  /** 검색 */
  $("#searchForm").submit(function(e){
      e.preventDefault();
          getList(1);
  });

  getDecrementRation();
  $(".hidetd").hide();
  /* 검색조건 */
  $("#DT_KEY_SEARCH").datepicker(
      getDatePickerSetting()
  );
  $("#START_DT_SEARCH").datepicker(
      getDatePickerSetting('END_DT_SEARCH', 'minDate')
  );
  $("#END_DT_SEARCH").datepicker(
      getDatePickerSetting('START_DT_SEARCH', 'maxDate')
  );
  $("#2ND_DT_KEY_SEARCH").datepicker(
      getDatePickerSetting()
  );
  $("#2ND_START_DT_SEARCH").datepicker(
      getDatePickerSetting('2ND_END_DT_SEARCH', 'minDate')
  );
  $("#2ND_END_DT_SEARCH").datepicker(
      getDatePickerSetting('2ND_START_DT_SEARCH', 'maxDate')
  );

  $("#DEPOSIT_DT").datepicker({
    dateFormat: 'yy-mm-dd',
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

  /** 기간키: 청구일, 기간: 오늘날짜 디폴트  */
  $('#DT_KEY_SEARCH').val('NB_CLAIM_DT');
  const date = new Date();
  const TODAY = dateFormat(date);
  const START_MONTH_DATE = date.getFullYear()+"-"+fillZero((date.getMonth()+1)+"",2)+"-"+"01"
  $("#START_DT_SEARCH").val(START_MONTH_DATE);
  $("#END_DT_SEARCH").val(TODAY);

  setCRR_ID_Option('CSD_ID_SEARCH', 'CRR_ID_SEARCH', true);
  getRefOptions(GET_REF_EXP_TYPE, 'RET_ID_SEARCH', true, false);                 //청구식
  setGwOptions('MGG_NM_SEARCH');
  getRefOptions(GET_REF_CONTRACT_TYPE, 'RCT_ID_SEARCH', false, false);           // 자가여부 
  setIwOptions('SEARCH_INSU_NM');   //보험사
  setInsuTeam();  //보험사팀
  // setInsuFao();   //보상담당
  // 개척자 불러오는 함수 필요할 수 있음
  setClaimId();   //청구요청자
  getDivisionOption('CSD_ID_SEARCH', true);   
  setGwOptions('MGG_NM_SEARCH');
  getRefOptions(GET_REF_PAY_EXP_TYPE, 'RPET_ID_SEARCH', true, false);                        // 지급식
  getRefOptions(GET_REF_CLAIM_TYPE, 'RLT_ID_SEARCH', false, false);                           //담보
  setCarType();   //차량급
  setPart();      //사용부품
  setDepoitSearch(); //입금여부상세
  setReasonSearch(); //사유입력
  getRefInfo(GET_REF_RECKON_TYPE, 'WORK_TYPE_SEARCH');
  getRefOptions(REF_FAX_STATUS, 'NBI_SEND_FAX_YN_SEARCH', false, false);  

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

  /* 보험사 변경에 따른 보험사 팀 동적 필터 */
  $('#SEARCH_INSU_NM').change(()=>{
    if($('#SEARCH_INSU_NM').selectpicker('val') != null){
        const selectInsuVal = $('#SEARCH_INSU_NM').selectpicker('val');
        console.log('selectInsuVal',selectInsuVal);
        INSUTEAM_LIST = [];
        selectInsuVal.map((insu)=>{
          console.log();
          INSUTEAM_LIST.push(INSUTEAM_ALL.filter((item, index)=>{
                return item.MII_ID == insu;
            }));
        });
        selectInsu(INSUTEAM_LIST);
    }else {
      setInsuTeam();
    }
});  

  /* 집계보기 */
  $(".totalbtn").click(function(){
      if($("#totalcheckbtn").is(":checked")){
          getSummary();
          $(".nwt-total").addClass("total-open");
      }else{
          $(".nwt-total").removeClass("total-open");
      }
  });

  // 전체선택
  $("#allcheck").click(function(){
      if($("#allcheck").prop("checked")){
          $("input[type=checkbox].listcheck").prop("checked", true);
      }else{
          $("input[type=checkbox].listcheck").prop("checked", false);
      }
  });


  //확장하기
   $("#extend").click(function(){
      if($("#extend").prop("checked")){
          $(".hidetd").show();
      }else{
          $(".hidetd").hide();
      }
  });


  // DT_KEY_SEARCH
  $('#DT_KEY_SEARCH').change(()=>{
    console.log($('#DT_KEY_SEARCH').val());
    if($('#DT_KEY_SEARCH').val() == 'NB_CLAIM_DT'){
      $('#2ND_DT_KEY_SEARCH').attr('disabled', false);
      $('#2ND_START_DT_SEARCH').attr('disabled', false);
      $('#2ND_END_DT_SEARCH').attr('disabled', false);
    }
  })
  
  
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

    $("input[name=DELETE_REASON]").change(()=>{
      let deleteReason = $("input[name=DELETE_REASON]:checked").val();
      if(deleteReason == 'unused'){
          $('#DELETE_REASON_TXT').val('미사용');
      }else if(deleteReason == 'other'){
          $('#DELETE_REASON_TXT').val('타업체사용');
      }else if(deleteReason == 'duplication'){
          $('#DELETE_REASON_TXT').val('중복');
      }else if(deleteReason == 'etc'){
          $('#DELETE_REASON_TXT').val('기타 ');
      }else{
          alert(ERROR_MSG_1400);
      }
  });

    $('#delete-modal').on('hide.bs.modal', function (event) {
      $('#DELETE_REASON_TXT').val('');
      $("input[name=DELETE_REASON]").prop("checked",false);
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

  /** 삭제모달 */
  $("#delForm").submit(function(e){
    e.preventDefault();
    deleteData();
  });

    const tdLength = $('table tr th').length;
    setInitTableData(tdLength);
    
});

/** 감액사유 자동요청 값  */
getDecrementRation = () => {
  const data = {
      REQ_MODE : CASE_READ
  };
  callAjax('../../../100_code/180_policy/183_reduce/act/reduce_act.php', 'GET', data).then((result)=>{
      const data = JSON.parse(result);
      if(data.status == OK){
          const item = data.data;
          $('#PIE_CUR_DECREMENT_RATIO').val(item.PIE_CUR_DECREMENT_RATIO); 
          // console.log('decrement_ration >>',$('#PIE_CUR_DECREMENT_RATIO').val());
      }else{
          alert(ERROR_MSG_1200);
      } 
  });
}


/**
 * MAIN LIST 
 * @param {int} currentPage  조회하는 페이지
 */
getList = (currentPage) =>{
  if($('#START_DT_SEARCH').val() !='' || $('#END_DT_SEARCH').val() != '' ){
    if($('#DT_KEY_SEARCH').val() == ''){
      alert('기간 키필드를 선택해주세요.');
      return false;
    }
  }
  
  CURR_PAGE = (currentPage == undefined) ? CURR_PAGE : currentPage;
  let form = $("#searchForm")[0];
  let formData = new FormData(form);
  formData.append('REQ_MODE', CASE_LIST);
  formData.append('PER_PAGE', $("#perPage").val());
  formData.append('CURR_PAGE', CURR_PAGE);
  $(".indecator").show();
  callFormAjax(DEPOSIT_URL, 'POST', formData).then((result)=>{
      if(isJson(result)){
          console.log('getList SQL >>',result);
          const data = JSON.parse(result);
          console.log( 'getList >> ',data);
          if(data.status == OK){
              $(".indecator").hide();
              const item = data.data;
              setTable(item[1], item[0], CURR_PAGE);  
              
          }else{
            $(".indecator").hide();
            setHaveNoDataTable();

          }
      }else{
          alert(ERROR_MSG_1200);
      }
     
  });  
  if($("#totalcheckbtn").is(":checked")){
    getSummary();
}
}

/* 집계보기 */
getSummary = () => {
  let form = $("#searchForm")[0];
  let formData = new FormData(form);
  formData.append('REQ_MODE', CASE_LIST_SUB);
  callFormAjax(DEPOSIT_URL, 'POST', formData).then((result)=>{
    if(isJson(result)){
      const res = JSON.parse(result);
      // console.log(res);
      if(res.status == OK){
          const item = res.data;
                    // console.log('item >>>',item);
          $('#TD_NB_CNT').html(numberWithCommas(item.CNT) +'(쌍:'+numberWithCommas(item.CNT_DOUBLE)+')');
          $('#TD_NB_PRICE').html( numberWithCommas(item.PRICE+'원'));
          $('#TD_DEPOSIT_CNT').html(numberWithCommas(item.CNT_DEPOSIT)+'(쌍:'+numberWithCommas(item.CNT_DEPOSIT_DOUBLE)+')');
          $('#TD_DEPOSIT_PRICE').html(numberWithCommas(item.DEPOSIT_PRICE)+'원');
          $('#TD_CNT_DEPOSIT_RATIO').html(item.CNT_DEPOSIT_RATIO+'%');
          $('#TD_PRICE_DEPOSIT_RATIO').html(item.PRICE_DEPOSIT_RATIO+'%');
          $('#TD_SEND').html(numberWithCommas(item.SEND)+'건');
          $('#TD_SEND_READY').html(numberWithCommas(item.SEND_READY)+'건');
          $('#TD_SEND_NOT').html(numberWithCommas(item.SEND_NOT)+'건');
          $('#TD_DUPLI').html(numberWithCommas(item.DUPLI)+'건');

          // console.log('decrement_ration >>',$('#PIE_CUR_DECREMENT_RATIO').val());
      }else{
          alert(ERROR_MSG_1200);
      } 
    }else{
      alert(ERROR_MSG_1200);
    }
  });
}


/** 차주분 입금처리 모달창
 * @param {Array} data 리스트 ROW DATA
 */
showCarownModal = (data) => {
  $("#NBI_CAROWN_DEPOSIT_DT").datepicker({
    dateFormat: 'yy-mm-dd',
  });
  $('#car-deposit').modal('toggle');
  $('#CAROWN_NB_ID').val(data.NB_ID);
  $('#CAROWN_NBI_RMT_ID').val(data.NBI_RMT_ID);

  $('#CAROWN_NB_CLAIM_DT').html(data.NB_CLAIM_DT);
  $('#CAROWN_NB_GARAGE_NM').html(data.MGG_NM);
  $('#CAROWN_NBI_REGI_NUM').html(data.NBI_REGI_NUM);
  $('#CAROWN_NB_CAR_NUM').html(data.NB_CAR_NUM);
  $('#CAROWN_NBI_CAROWN_PRICE').html(numberWithCommas(Number(data.NBI_CAROWN_PRICE))+'원');
  if(BASE_DEPOSIT_DATE != ''){
    $('#NBI_CAROWN_DEPOSIT_DT').val(BASE_DEPOSIT_DATE);
  }
  if(data.NBI_CAROWN_DEPOSIT_YN == YN_Y){
    $('#NBI_CAROWN_DEPOSIT_DT').val(data.NBI_CAROWN_DEPOSIT_DT);
    $('#NBI_CAROWN_DEPOSIT_PRICE').val(data.NBI_CAROWN_DEPOSIT_PRICE);
  }
}

/* 차주분 입금처리 */
setCarown = () => {
  confirmSwalTwoButton(
    '차주분 입금처리를 저장하시겠습니까?',
    OK_MSG,
    CANCEL_MSG,
    false,
    ()=> {
        const param ={
          REQ_MODE: CASE_UPDATE,
          NB_ID: $('#CAROWN_NB_ID').val(),
          NBI_RMT_ID: $('#CAROWN_NBI_RMT_ID').val(),
          NBI_CAROWN_DEPOSIT_DT: $('#NBI_CAROWN_DEPOSIT_DT').val(),
          NBI_CAROWN_DEPOSIT_PRICE: $('#NBI_CAROWN_DEPOSIT_PRICE').val(),
        }
        console.log('setCarown >>',param);
        callAjax(DEPOSIT_URL, 'POST', param).then((result)=>{
          console.log('setCarown SQL>>>',result);
            if(isJson(result)){
                const data = JSON.parse(result);
                if(data.status == OK){
                  confirmSwalOneButton(
                    SAVE_SUCCESS_MSG,
                    OK_MSG,
                    ()=>{
                      $('#car-deposit').modal('toggle');
                      getList();
                    }
                  );
                }else{
                    alert(ERROR_MSG_1100);
                } 
            }else{
                alert(ERROR_MSG_1200);
            }
        });
    }
);
}



/**
 * 감액사유 요청 
 * @param {String} type All || undefined
 */
reqDecrement = (type) => {
  if(type == ALL){
    arrChk = $("input[name=multi]:checked").map(function(){
        return $(this).attr('id');
    }).get();
    // console.log('all selected>>' , arrChk.length);
    if(arrChk.length == 0 ){
      alert('감액사유 일괄 요청할 청구서를 선택해 주세요.'); 
      return false;
    }
  }
  
  confirmSwalTwoButton(
      '감액 사유를 요청하시겠습니까?',
      OK_MSG,
      CANCEL_MSG,
      false,
      ()=> {
          const param ={
            REQ_MODE: CASE_UPDATE_REGION,
            ALL_CHK: arrChk,
            NB_ID: $('#NB_ID').val(),
            NBI_RMT_ID: $('#NBI_RMT_ID').val()
          }
          // console.log('reqDecrement >>',param);
          callAjax(DEPOSIT_URL, 'POST', param).then((result)=>{
            // console.log('setMemo SQL>>>',result);
              if(isJson(result)){
                  const data = JSON.parse(result);
                  if(data.status == OK){
                    confirmSwalOneButton(
                      '요청이 완료되었습니다.',
                      OK_MSG,
                      ()=>{
                        getList();
                        arrChk = [];
                      }
                    );
                  }else{
                      alert(ERROR_MSG_1100);
                  } 
              }else{
                  alert(ERROR_MSG_1200);
              }
          });
          if(type != ALL){
            $("#add-new").modal('toggle');
          }
      }
  );
}


/*입력여부상세에 따른 감액입금액 및 감액입금일 활성화 세팅 */
changeDepositDetail = () => {
  let selectInfo = arrDepotitDetail.filter((data) => {
      return $("input[name=RADIO_DEPOSIT_DETAIL]:checked").val()== data.ID;
  });

  if(selectInfo[0].RDT_EXT1_YN == YN_N){
    $('#NBI_INSU_ADD_DEPOSIT_PRICE').attr('disabled', true);
  }else {
    $('#NBI_INSU_ADD_DEPOSIT_PRICE').attr('disabled', false);
  }
  if(selectInfo[0].RDT_EXT2_YN == YN_N){
    $('#NBI_INSU_ADD_DEPOSIT_DT').attr('disabled', true);
  }else {
    $('#NBI_INSU_ADD_DEPOSIT_DT').attr('disabled', false);
  }
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
          htmlTxt += "<input type='hidden' name='NBI_CHG_NM[]' value="+data.NIC_NM+">";
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


/* 팀재분류 메모 모달 */
showTeamMemoModal = () => {
  
  arrChk = $("input[name=multi]:checked").map(function(){
      return $(this).attr('id');
  }).get();
  if(arrChk.length == 0 ){
    alert('팀재분류 메모를 작성할 청구서를 선택해 주세요.'); 
    return false;
  }
  $('#NBI_TEAM_MEMO').val('');
  $("#team-memo").modal('toggle');


}

/* 팀재분류 메모 저장 */
setMemo = () =>{
  confirmSwalTwoButton(
      arrChk.length+'개 청구서의 팀재분류메모를 저장 하시겠습니까?',
      OK_MSG,
      CANCEL_MSG,
      false,
      ()=> {
        const param ={
          REQ_MODE: CASE_UPDATE_TEAM,
          ARR_CHK: arrChk,
          NBI_TEAM_MEMO: $('#NBI_TEAM_MEMO').val()
        }
        callAjax(DEPOSIT_URL, 'POST', param).then((result)=>{
          // console.log('setMemo SQL>>>',result);
            if(isJson(result)){
                const data = JSON.parse(result);
                if(data.status == OK){
                  confirmSwalOneButton(
                    SAVE_SUCCESS_MSG,
                    OK_MSG,
                    ()=>{
                      $("#team-memo").modal('toggle');
                      getList();
                      arrChk=[];
                    }
                  );
                }else{
                    alert(ERROR_MSG_1100);
                    $("#team-memo").modal('toggle');
                } 
            }else{
                alert(ERROR_MSG_1200);
                $("#team-memo").modal('toggle');
            }
        });
      }
  );
  
}


/* 입금일 지정 */
setBasicDate = () => {
    BASE_DEPOSIT_DATE = $('#DEPOSIT_DT').val();
    // console.log('setBasicDate >',BASE_DEPOSIT_DATE);
    $('#DEPOSIT_MODAL').modal('toggle');
}



openBillPopup = (NB_ID, MGG_ID) => {
  window.open('/200_billMngt/210_writeBill/writeBill.html?NB_ID='+NB_ID+'&MGG_ID='+MGG_ID,  'newwindow', 'width=1500,height=1000');
}

/**
 * 삭제 확인 요청
 * @param {String} nb 청구서 아이디 (NB.`NB_ID`)
 * @param {String} rmt 청구구분(NBI.`NBI_RMT_ID`)
 * @param {String} type ALL || undefined
 */
openDeleteModal = (idx) => {
  if(idx == undefined){
      if($('input[name=multi]:checked').length > 0){
          clickDelBtnIdx = idx;
          $('#delete-modal').modal('show');
      }else{
          alert("선택된 항목이 없습니다.");
      }
  }else{
      clickDelBtnIdx = idx;
      $('#delete-modal').modal('show'); 
  }
}




/* 삭제함수 */
deleteData = () => {
  let delBillIdxArray = [];
  if(clickDelBtnIdx == undefined){
      const msg = "해당 "+$('input[name=multi][name=multi]:checked').length+"건을 일괄삭제 하시겠습니까?";
      confirmSwalTwoButton(msg, '삭제', '취소', true , ()=>{       
          $('input[name=multi]:checked').each(function(){
              if($(this).val() != ''){
                  delBillIdxArray.push($(this).val());
              }
          });                         
          delBillData(delBillIdxArray);
      });
  }else{
      confirmSwalTwoButton("삭제 하시겠습니까?",'확인', '취소', true , ()=>{       
          delBillIdxArray.push(clickDelBtnIdx);
          delBillData(delBillIdxArray);
      });
  }
}

/**
 * 청구서 삭제
 */
delBillData = (delDataIdxArray) => {
  let form = $("#delForm")[0];
  let formData = new FormData(form);
  formData.append('REQ_MODE', CASE_DELETE);

  if(delDataIdxArray.length > 0){
      delDataIdxArray.map((idx)=>{
          const data = listArray[idx];
          console.log(data);
          formData.append('NB_ID[]', data.NB_ID);
      });  

      callFormAjax(ajaxUrl, 'POST', formData).then((result)=>{
          if(isJson(result)){
              const data = JSON.parse(result);
              if(data.status == OK){
                  confirmSwalOneButton("삭제되었습니다.", OK_MSG,()=>{
                      getList();
                      $('#delete-modal').modal('hide');
                  });
              }else{
                  alert(ERROR_MSG_1100);
              }
          }else{
              alert(ERROR_MSG_1200);
          }
      });
  }
}



excelDownload = () =>{
    location.href="depositMngtExcel.html?"+$("#searchForm").serialize();
}



/**
 * 인쇄/일괄인쇄
 */
billPrint = (idx) => {
  let printSendArray = [];
  if(idx == undefined){
      if($('input[name=multi]:checked').length > 0){
          const msg = "해당 "+$('input[name=multi][name=multi]:checked').length+"건을 출력 하시겠습니까?";
          confirmSwalTwoButton(msg,'확인', '취소', false , ()=>{       
              $('input[name=multi]:checked').each(function(){
                  if($(this).val() != ''){
                      printSendArray.push($(this).val());
                  }
              });                         
              printBill(printSendArray);
          });
      }else{
          alert("선택된 항목이 없습니다.");
      }
  }else{
      confirmSwalTwoButton("출력 하시겠습니까?",'확인', '취소', false , ()=>{       
          printSendArray.push(idx);
          printBill(printSendArray);
      });
     
  }
 
}



printBill = (printIdxArray) => {
  if(printIdxArray.length > 0){
      let printHtml = '';
      let count = 0;

      printIdxArray.map((idx)=>{
          const popupUrl = '../../_SLF.html?NB_ID='+listArray[idx].NB_ID+'&MGG_ID='+listArray[idx].MGG_ID;
          const data = {
              REQ_MODE: CASE_LIST_SUB
          };
        
          callAjax(popupUrl, 'GET', data).then((result)=>{
              printHtml += result;  

              if(count == printIdxArray.length-1){
                  const strFeature = "width=1000, height=1200, all=no";
                  let objWin = window.open('', 'print', strFeature);
                  objWin.document.write(printHtml);
                  setTimeout(function() {
                      objWin.focus();
                      objWin.print();
                      objWin.close();
                  }, 250);
              }
              count ++;
          });
          
        
      });
     
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


downloadFile = (idx,type) => {  
  // location.href = "/200_billMngt/220_sendList/downloadPDF.php?NB_ID="+listArray[idx].NB_ID+"&MGG_ID="+listArray[idx].MGG_ID+"&TYPE="+type;
  const url = "../../../PDF_VIEW.html?NB_ID="+listArray[idx].NB_ID+"&MGG_ID="+listArray[idx].MGG_ID+"&TYPE="+type;
  pdfPopup =  window.open(url, 
    'loginNoticePopup', 
    'width=1300,height=900');
}

