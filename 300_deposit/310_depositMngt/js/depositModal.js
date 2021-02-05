
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
    $('#NBI_DEPOSIT_EXTRA1').val(data.NBI_DEPOSIT_EXTRA1);

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
    // console.log('show detail :checked >>',$("input[name=RADIO_DEPOSIT]:checked").val(), 'dtl_id>>',val);
    if($("input[name=RADIO_DEPOSIT]:checked").val() != UNPAID && rdt == undefined){ //미입금이 아니면 입금액,입금일 기입 하고 입금 상세사유 입력받기
      // console.log('not unpaid why 40121?????',$("input[name=RADIO_DEPOSIT]:checked").val(), '|', rdt);
      $('#trDepositDetail').show(); // 입금여부상세 입력받고
      getDecrementList(val);// 감액사유 입력
    }else if (rdt != UNPAID && rdt != undefined){
      // console.log('else if edt != unpaid',rdt);
      $('#trDepositDetail').show(); // 입금여부상세 입력받고
      getDecrementList(val);// 감액사유 입력
    }else if (rdt == UNPAID && rdt != undefined){
      // console.log('else if rdt == unpaid',rdt);
      $('#trDepositDetail').hide();
      getExcludeList(val);//지급제외사유 입력
    }else {
      // console.log('trDepositDetail hide!!!');
      $('#trDepositDetail').hide();
      getExcludeList(val);//지급제외사유 입력
    }
}



/* 입금처리 저장 */
setData = () => {
  
    $('#DC_RATIO').val();
    let rdt = $("input[name=RADIO_DEPOSIT_DETAIL]:checked").val() ;
    let form = $("#depositForm")[0];
    let formData = new FormData(form);
    formData.append('REQ_MODE', CASE_CREATE);
    formData.append('RDET_ID', $("input[name=RADIO_DEPOSIT]:checked").val());
    if( rdt != undefined && rdt != ''){
      formData.append('RDT_ID', rdt);
    }else {
      formData.append('RDT_ID', '40111');
    }
    formData.append('NBI_INSU_DONE_YN', $("input[name=RADIO_DONE]:checked").val());
    formData.append('NBI_REASON_DTL_ID', $("#REASON_SELECT").val());
    formData.append('DC_RATIO', $("#DC_RATIO").val());

    // console.log('setData param>>>',form);
    callFormAjax(DEPOSIT_URL, 'POST', formData).then((result)=>{

      // console.log('setData SQL>>', result);
      if(isJson(result)){
          confirmSwalOneButton(SAVE_SUCCESS_MSG,OK_MSG,()=>{$('#add-new').modal('toggle');getList();});
      }else{
          alert(ERROR_MSG_1200);
      }
  }); 

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
    $('#DC_RATIO').val(dc.toFixed(1) >= Number($('#PIE_CUR_DECREMENT_RATIO').val()) ? dc.toFixed(1)  : '');
    console.log($('#PIE_CUR_DECREMENT_RATIO').val(),'dc>>>>>',$('#DC_RATIO').val());
    if($('#DC_RATIO').val() == '' ){
      console.log($('#DC_RATIO').val() ,'is null');
      $('input:radio[name=RADIO_DEPOSIT_DETAIL]:input[value=40112]').attr("checked", false);
      $('#btnDecrement').show();
    }else {
      $('input:radio[name=RADIO_DEPOSIT_DETAIL]:input[value=40112]').attr("checked", true);
      $('#btnDecrement').hide();
    }
  
  }
  