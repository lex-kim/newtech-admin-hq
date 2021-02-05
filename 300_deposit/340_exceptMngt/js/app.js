
let EXCEPT_URL = 'act/exceptMngt_act.php';
let CURR_PAGE = 1;
let listArray = [];
let tdLength = 0;
let BASE_DEPOSIT_DATE = '';
const UNPAID = '40121';
const ALL = 'all';

const registPopupWidth = 500; 
const registPopupHeight = 430; 
let popupX = 0;
let popupY = 0;

$(document).ready(function() {
    tdLength = $('table tr th').length;
    setInitTableData(tdLength);

    getExcludeList('NBI_REASON_DTL_ID_SEARCH');
    setIwOptions('SEARCH_INSU_NM', undefined, '=보험사=');   //보험사
    getDivisionOption('CSD_ID_SEARCH', true);   
    setCRR_ID_Option('CSD_ID_SEARCH', 'CRR_ID_SEARCH', true); //지역구분
    setGwOptions('SEARCH_BIZ_NM', undefined , '=공업사='); //공업사
    getRefOptions(GET_REF_EXP_TYPE, 'RET_ID_SEARCH', true, false);                 //청구식
    getRefOptions(GET_REF_CLAIM_TYPE, 'RLT_ID_SEARCH', false, false);              //담보
    getRefOptions(REF_FAX_STATUS, 'NBI_SEND_FAX_YN_SEARCH', false, false);         //전송여부
    // setMiiInsuTeam();  //보험사팀

    $("#DT_KEY_SEARCH").datepicker(
        getDatePickerSetting()
    );
    $("#START_DT_SEARCH").datepicker(
        getDatePickerSetting('END_DT_SEARCH', 'minDate')
    );
    $("#END_DT_SEARCH").datepicker(
        getDatePickerSetting('START_DT_SEARCH', 'maxDate')
    );
    
     $("#NBI_OTHER_DT").datepicker({
        dateFormat: 'yy-mm-dd',
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


    $('#REASON_SELECT').change(function(){
      let selectReason = arrDecrement.filter(function(data){
          return $('#REASON_SELECT').val() == data.ID;
      });
      console.log('selectReason>>', selectReason[0]) ;
      if(selectReason[0].EXT1_YN == YN_Y ){
        $(".insu-hidetd").show();
      }else {
        $(".insu-hidetd").hide();
      }
      if(selectReason[0].EXT2_YN == YN_Y){
        $(".ratio-hidetd").show();
      }else {
        $(".ratio-hidetd").hide();
      }
      if(selectReason[0].EXT3_YN == YN_Y){
        $(".date-hidetd").show();
      }else {
        $(".date-hidetd").hide();
      }
      if(selectReason[0].EXT4_YN == YN_Y){
        $(".price-hidetd").show();
      }else {
        $(".price-hidetd").hide();
      }
      if(selectReason[0].EXT5_YN == YN_Y){
        $(".memo-hidetd").show();
      }else {
        $(".memo-hidetd").hide();
      }
      setOtherIw('NBI_OTHER_MII_ID');
      $('#NBI_OTHER_RATIO').val('');
      $('#NBI_OTHER_DT').val('');
      $('#NBI_OTHER_PRICE').val('');
      $('#NBI_OTHER_MEMO').val('');
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
        confirmSwalTwoButton('저장 하시겠습니까?',OK_MSG, CANCEL_MSG, false ,()=>setData());
    });


    /** 검색버튼 */
    $('#searchForm').submit(function(e){
        e.preventDefault();
        getList(1);
    });

    // 팝업 가로높이
    popupX = (screen.availWidth - registPopupWidth) / 2;
    if( window.screenLeft < 0){
        popupX += window.screen.width*-1;
    }
    else if ( window.screenLeft > window.screen.width ){
        popupX += window.screen.width;
    }
    popupY= (window.screen.height / 2) - (registPopupHeight / 2);
});

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
    callFormAjax(EXCEPT_URL, 'POST', formData).then((result)=>{
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
        tableText += "  <td class='text-center'>"+data.MGG_ID+"</td>";
        tableText += "  <td class='text-center'>"+data.NB_CLAIM_DT+"</td>";
        tableText += "  <td class='text-left'>"+data.MII_NM+"</td>";
        tableText += "  <td class='text-center'>"+nullChk(data.MIT_NM)+"</td>";
        tableText += "  <td class='text-left'>"+data.NIC_NM+"</td>";
        tableText += "  <td class='text-center'>"+(data.NIC_TEL1_AES != null ? phoneFormat(data.NIC_TEL1_AES): '-' )+"</td>";
        tableText += "  <td class='text-center'><button type='button' class='btn btn-default' onclick=openSMSPopup('"+data.NIC_TEL1_AES+"')>SMS</button></td>";
        tableText += "  <td class='text-center'>"+data.CSD_NM+"</td>";

        tableText += "  <td class='text-center'>"+data.CRR_NM+"</td>";
        tableText += "  <td class='text-center'>"+data.MGG_NM+"</td>";
        tableText += "  <td class='text-center'>"+bizNoFormat(data.MGG_BIZ_ID)+"</td>";
        tableText += "  <td class='text-center'>"+data.RET_NM+"</td>";
        tableText += "  <td class='text-center'>"+data.NBI_REGI_NUM+"</td>";
        tableText += "  <td class='text-center'>"+data.RLT_NM+"</td>";
        tableText += "  <td class='text-center'>"+data.NB_CAR_NUM+"</td>";
        tableText += "  <td class='text-center'>"+data.CCC_NM+"</td>";
        tableText += "  <td class='text-center'>"+numberWithCommas(data.NB_TOTAL_PRICE)+"</td>";
        tableText += "  <td class='text-center'>"+nullChk(data.NBI_REASON_DT)+"</td>";

        tableText += "  <td class='text-center'>"+nullChk(data.REASON_RMT_NM)+"</td>";
        tableText += "  <td class='text-center'>"+nullChk(data.NBI_REASON_DTL_NM)+"</td>";
        tableText += "  <td class='text-center'>"+nullChk(data.NBI_PROC_REASON_DT)+"</td>";
        tableText += "  <td class='text-center'>"+(data.NBI_PROC_USERID != null ? data.NBI_PROC_NM+"("+data.NBI_PROC_USERID +')': '-')+"</td>";
        tableText += "  <td class='text-center'>"+nullChk(data.HQ_MEMO)+"</td>";
        tableText += "  <td class='text-center'>"+nullChk(data.IW_MEMO)+"</td>";
        tableText += "  <td class='text-center'>"+data.RFS_NM+"</td>";
        tableText += "  <td class='text-center'><button type='button' class='btn btn-default' onclick='showDepositModal("+JSON.stringify(data)+")'>입금처리</button></td>";
        tableText += "  <td class='text-center'>"+nullChk(data.NBI_INSU_DEPOSIT_DT)+"</td>";
        tableText += "  <td class='text-center'>"+(data.NBI_INSU_DEPOSIT_YN == YN_N? '미입금': '입금')+"</td>";
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


openSMSPopup = (val) => {
  const formData = $('#sendForm');
  $('#NUM').val(val);
  window.open('', 
            'smswindow', 
            'width='+registPopupWidth+',height='+registPopupHeight+' ,left='+ popupX + ', top='+ popupY);
  formData.submit();
}

/** 입금처리 모달 
 * @param {Array} data 리스트 ROW DATA
 */
showDepositModal = (data) => {
    console.log('showDepositModal>>', data);
    $("#depositForm").each(function() {  
        this.reset();  
    });  

    $('#add-new').modal('toggle');
    $('#trDepositDetail').hide();


    setOtherIw('NBI_OTHER_MII_ID');

    $('#NB_ID').val(data.NB_ID);
    $('#NBI_RMT_ID').val(data.NBI_RMT_ID);
    $('#TOTAL_PRICE').val(data.NB_TOTAL_PRICE);
    $('#NB_VAT_DEDUCTION_YN').val(data.NB_VAT_DEDUCTION_YN);
    $('#NB_NEW_INSU_YN').val(data.NB_NEW_INSU_YN);

    /* 기본정보 */
    $('#NB_CLAIM_DT').html(data.NB_CLAIM_DT);
    $('#MII_NM').html(data.MII_NM != null ? data.MII_NM : '-');
    $('#NBI_REGI_NUM').html(data.NBI_REGI_NUM);
    $('#NB_CAR_NUM').html(data.NB_CAR_NUM);
    $('#NB_TOTAL_PRICE').html(numberWithCommas(Number(data.NB_TOTAL_PRICE))+'원');
    
    /* 입금처리*/
    getDepoiteeType('depoistType', data.RDET_ID ); //입금여부
    console.log('입금여부 >>>',data.RDET_ID);
    if( data.RDT_ID != null){
      console.log('there is RDT_D',data.RDT_ID);
      $('#trDepositDetail').show(); 
      getDepoitDetail('depositDetail', data.RDT_ID); //입금여부상세

      showDepositDetail(data.NBI_REASON_DTL_ID,data.RDET_ID);
    }else {
      console.log('else.....');
      getDepoitDetail('depositDetail');
      showDepositDetail(data.NBI_REASON_DTL_ID,data.RDET_ID);
    }
    if(data.NBI_INSU_ADD_DEPOSIT_DT != null || data.NBI_INSU_ADD_DEPOSIT_PRICE != null){
      $('#NBI_INSU_ADD_DEPOSIT_PRICE').attr('disabled', false);
      $('#NBI_INSU_ADD_DEPOSIT_DT').attr('disabled', false);
      $('#NBI_INSU_ADD_DEPOSIT_PRICE').val(data.NBI_INSU_ADD_DEPOSIT_PRICE);
      $('#NBI_INSU_ADD_DEPOSIT_DT').val(data.NBI_INSU_ADD_DEPOSIT_DT);

    }else {
      $('#NBI_INSU_ADD_DEPOSIT_PRICE').attr('disabled', true);
      $('#NBI_INSU_ADD_DEPOSIT_DT').attr('disabled', true);
    }
    


    $('#NBI_INSU_DEPOSIT_PRICE').val(data.NBI_INSU_DEPOSIT_PRICE);
    if(nullChk(data.NBI_INSU_DEPOSIT_DT,'-') != '-' ){
      $('#NBI_INSU_DEPOSIT_DT').val(data.NBI_INSU_DEPOSIT_DT);
    }else {
      $('#NBI_INSU_DEPOSIT_DT').val(BASE_DEPOSIT_DATE);
    }
    console.log('done_YN = ',data.NBI_INSU_DONE_YN);
    $('input:radio[name=RADIO_DONE]:input[value=' + data.NBI_INSU_DONE_YN + ']').attr("checked", true);
    /* 사유 */
    $('#NBI_REASON_DT').html(data.NBI_REASON_DT != null? data.NBI_REASON_DT : '-');
    $('#NBI_REASON_USER_NM').html(nullChk(data.REASON_RMT_NM)+'/'+nullChk(data.NBI_REASON_USER_NM,'') +'('+nullChk(data.NBI_REASON_USERID)+')');
    if(data.NBI_PROC_REASON_DT != null){
      $("input[name=NBI_PROC]").prop("checked", true);
    }
    $('#NBI_PROC_REASON_DT').html(data.NBI_PROC_REASON_DT != null ?data.NBI_PROC_REASON_DT : '-');
    $('#NBI_PROC_USERID').html(nullChk(data.NBI_PROC_NM, '')+'('+nullChk(data.NBI_PROC_USERID)+')');
    
    // 사유입력 > 추가입력사항 
    if(data.NBI_OTHER_MII_ID != '' ){
      console.log('NBI_OTHER_MII_ID');
      $(".insu-hidetd").show();
      setOtherIw('NBI_OTHER_MII_ID',data.NBI_OTHER_MII_ID);

    }else {
      $(".insu-hidetd").hide();
    }
    if(data.NBI_OTHER_RATIO != ''){
      $(".ratio-hidetd").show();
      $('#NBI_OTHER_RATIO').val(data.NBI_OTHER_RATIO);
      console.log('NBI_OTHER_RATIO');
    }else {
      $(".ratio-hidetd").hide();
    }
    if(data.NBI_OTHER_DT != ''){
      $(".date-hidetd").show();
      $('#NBI_OTHER_DT').val(data.NBI_OTHER_DT);
      console.log('NBI_OTHER_DT');
    }else {
      $(".date-hidetd").hide();
    }
    if(data.NBI_OTHER_PRICE != ''){
      $(".price-hidetd").show();
      $('#NBI_OTHER_PRICE').val(data.NBI_OTHER_PRICE);
      console.log('NBI_OTHER_PRICE');
    }else {
      $(".price-hidetd").hide();
    }
    if(data.NBI_OTHER_MEMO != ''){
      $(".memo-hidetd").show();
       $('#NBI_OTHER_MEMO').val(data.NBI_OTHER_MEMO);
       console.log('NBI_OTHER_MEMO');
    }else {
      $(".memo-hidetd").hide();
    }
    console.log();
    if(data.NBI_REQ_REASON_YN == YN_Y){
      $('#btnDecrement').hide();
    }else if($('#depoistType').val() == '' ){
      $('#btnDecrement').hide();
    }
    
    
    
}



/** 사유입력 셋팅 (지급제외사유 || 감액사유) 
 * @param {String} val 상세사유아이디 (NBI.NBI_REASON_DTL_ID)
 */
showDepositDetail= (val, rdt) =>{
    if (rdt == UNPAID && rdt != undefined){
      // console.log('else if rdt == unpaid',rdt);
      $('#trDepositDetail').hide();
      getExcludeList('REASON_SELECT',val);//지급제외사유 입력
    }else {
      // console.log('trDepositDetail hide!!!');
      $('#trDepositDetail').hide();
      getExcludeList('REASON_SELECT',val);//지급제외사유 입력
    }
}
/* 입금액에 따라서 DC율 연산 */
setDC = () =>{
    let price = Number($('#TOTAL_PRICE').val());
    let vat = 0;
    let newInsu = 0;
    if($('#NB_VAT_DEDUCTION_YN').val() == YN_Y){
      console.log('VAT:',vat );
      vat = 0.1;
    }
    if($('#NB_NEW_INSU_YN').val() == YN_Y){
      console.log('newInsu:',newInsu );
      newInsu = 0.2
    }
    
    let deposit = $('#NBI_INSU_DEPOSIT_PRICE').val() > 0? Number($('#NBI_INSU_DEPOSIT_PRICE').val()) : 0;
    let depositAdd = $('#NBI_INSU_ADD_DEPOSIT_PRICE').val() > 0 ? Number($('#NBI_INSU_ADD_DEPOSIT_PRICE').val()) : 0;
    let dc = (1-((deposit+depositAdd )/(price*(1-vat)*(1-newInsu))))*100 ; 
    console.log((price*(1-vat)*(1-newInsu)),'>>>>>>>>>',dc);
    $('#DC_RATIO').val(dc.toFixed(1) > Number($('#PIE_CUR_DECREMENT_RATIO').val()) ? dc.toFixed(1)  : '');
    console.log($('#PIE_CUR_DECREMENT_RATIO').val(),'dc>>>>>',$('#DC_RATIO').val());
    if($('#DC_RATIO').val() == '' ){
      console.log($('#DC_RATIO').val() ,'is null');
      $('#btnDecrement').show();
    }else {
      $('#btnDecrement').hide();
    }
  
}
/* 입금처리 저장 */
setData = () => {
  
    let rdt = $("input[name=RADIO_DEPOSIT_DETAIL]:checked").val() ;
    let form = $("#depositForm")[0];
    let formData = new FormData(form);
    formData.append('REQ_MODE', CASE_CREATE);
    formData.append('RDET_ID', $("input[name=RADIO_DEPOSIT]:checked").val());
    if( rdt != undefined && rdt != ''){
      formData.append('RDT_ID', rdt);
    }
    formData.append('NBI_INSU_DONE_YN', $("input[name=RADIO_DONE]:checked").val());
    formData.append('NBI_REASON_DTL_ID', $("#REASON_SELECT").val());
    formData.append('DC_RATIO', $("#DC_RATIO").val());

    // console.log('setData param>>>',form);
    callFormAjax(EXCEPT_URL, 'POST', formData).then((result)=>{

      // console.log('setData SQL>>', result);
      if(isJson(result)){
          confirmSwalOneButton(SAVE_SUCCESS_MSG,OK_MSG,()=>{$('#add-new').modal('toggle');getList();});
      }else{
          alert(ERROR_MSG_1200);
      }
  }); 

}






excelDownload = () =>{
    location.href="exceptMngtExcel.html?"+$("#searchForm").serialize();
}