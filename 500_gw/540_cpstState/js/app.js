const CAPST_URL = 'act/cpstState_act.php';
let CURR_PAGE = 1; 
let HEADER_INSU_LIST = [];
const registPopupWidth = 470;            // 보상담당등록창 width 값
let popupX = 0;
let popupY = 0;

$(document).ready(function() {  
  popupX = (screen.availWidth - registPopupWidth) / 2;
  if( window.screenLeft < 0){
      popupX += window.screen.width*-1;
  }
  else if ( window.screenLeft > window.screen.width ){
      popupX += window.screen.width;
  }
  popupY= (window.screen.height / 2) - (680 / 2);

  getDivisionOption('CSD_ID_SEARCH', true);                                      //지역구분
  setCRR_ID_Option('CSD_ID_SEARCH', 'CRR_ID_SEARCH', true);
  setGwOptions('MGG_NM_SEARCH', undefined, '=공업사=');
  getRefOptions(GET_REF_CONTRACT_TYPE, 'RCT_ID_SEARCH', false, false);           // 자가여부 
  getRefOptions(GET_REF_INSU_CHAGE_TYPE, 'RICT_ID_SEARCH', false, false);        // 반응  

  /** popup에 구분 셀렉터 이벤트 */
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

   /** 검색버튼 */
   $('#searchForm').submit(function(e){
        e.preventDefault();

        getList(1);
   });

  //확장하기
  $("#extend").click(function(){
      hidedHeaderCol();   // 값이 없는 보험사는 안보여주기위해
  });

  /* Table Header Setting  */
  setTableHeaderInfo() ;

});


   

hidedHeaderCol = () => {
  const normalInsu = getCol(HEADER_INSU_LIST, INSU_TYPE_NORMAL) ;
  const cooperInsu = getCol(HEADER_INSU_LIST, INSU_TYPE_COOPER) ;
  const rentInsu = getCol(HEADER_INSU_LIST, INSU_TYPE_RENT) ;

  if($('#extend').is(':checked')){
      $('.hidetd').show();
  }else{
      $('.hidetd').hide();
  }

  if(normalInsu > 0){
      $('.insu-normal').attr('colspan', normalInsu);
  }else{
      $('#insu-normal').hide();
  }
  if(cooperInsu > 0){
      $('.insu-cooper').attr('colspan', cooperInsu);
  }else{
      $('#insu-cooper').hide();
  }

  if(rentInsu > 0){
      $('.insu-rent').attr('colspan', rentInsu);
  }else{
      $('.insu-rent').hide();
  }

  
}


addInsuManager = (MGG_ID, MII_ID) => {
    const RICT_ID = $('#RICT_ID_SEARCH').val();
    window.open('', 
      'rewardwindow', 
      'width='+registPopupWidth+',height=665, left='+ popupX + ', top='+ popupY); 

    const form = $('#updateGarageInsuForm')[0];
    form.action = '/500_gw/532_gwCpst/gwCpstRewardModal.html';
    form.target = 'rewardwindow';
    form.method = 'POST';


    console.log("MGG_ID===>",MGG_ID);
    let dataHtml =  '<input type="hidden" id="MGG_ID" name="MGG_ID" value="'+MGG_ID+'">';
    dataHtml +=  '<input type="hidden" id="MII_ID" name="MII_ID" value="'+MII_ID+'">';
    dataHtml +=  '<input type="hidden" id="RICT_ID" name="RICT_ID" value="'+RICT_ID+'">';
    dataHtml +=  '<input type="hidden" id="TYPE" name="TYPE" value="NEW">';
    $('#updateGarageInsuForm').html(dataHtml);

    form.submit();
}


openModifyManage = (MGG_ID, MII_ID, RICT_ID) => {
    window.open('', 
        'rewardwindow', 
        'width='+registPopupWidth+',height=665, left='+ popupX + ', top='+ popupY); 

    const form = $('#updateGarageInsuForm')[0];
    form.action = '/500_gw/532_gwCpst/gwCpstRewardModal.html';
    form.target = 'rewardwindow';
    form.method = 'POST';

    let dataHtml =  '<input type="hidden" id="MGG_ID" name="MGG_ID" value="'+MGG_ID+'">';
    dataHtml +=  '<input type="hidden" id="MII_ID" name="MII_ID" value="'+MII_ID+'">';
    dataHtml +=  '<input type="hidden" id="RICT_ID" name="RICT_ID" value="'+RICT_ID+'">';
    $('#updateGarageInsuForm').html(dataHtml);

    form.submit();
}

/* 메인 리스트 */
getList = (currentPage) => {
  CURR_PAGE = (currentPage==undefined) ? CURR_PAGE : currentPage;
  let form = $("#searchForm")[0];
  let formData = new FormData(form);
  formData.append('REQ_MODE', CASE_LIST);
  formData.append('PER_PAGE', $("#perPage").val());
  formData.append('CURR_PAGE', CURR_PAGE);

  callFormAjax(CAPST_URL, 'POST', formData).then((result)=>{
      const res = JSON.parse(result);
      if(res.status == OK){
          const item = res.data;
          setTable(item[1], item[0], currentPage);
      }else{
          setHaveNoDataTable();
      }
  });  
}

/* 엑셀 다운로드 */
excelDownload = () => {
  location.href="cpstStateExcel.html?"+$("#searchForm").serialize()+"&REQ_MODE="+CASE_TOTAL_LIST;
}