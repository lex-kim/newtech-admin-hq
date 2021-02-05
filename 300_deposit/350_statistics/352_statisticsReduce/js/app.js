let REDUCE_URL = '/300_deposit/350_statistics/352_statisticsReduce/act/statisticsReduce_act.php'; 
let CURR_PAGE = 1;
const DEPOSIT_URL = '/300_deposit/310_depositMngt/act/depositMngt_act.php';
const UNPAID = '40121';
let IS_SORT = undefined;        // 오름차순.내림차순 정렬여부
let ORDER_KEY = undefined;   // 월별청구건수정렬시 몇월달 인지

$(document).ready(function() {
    tdLength = $('table th').length;
    getList(1); 

    getDivisionOption('CSD_ID_SEARCH', true);                                              //구분
    setCRR_ID_Option('CSD_ID_SEARCH', 'CRR_ID_SEARCH', true);
    getRefOptions(GET_REF_CLAIM_TYPE, 'RLT_ID_SEARCH', false, false);              //담보
    getSearchDecrementList();   

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
        console.log('감액건 조회!');
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
  location.href="reductionExcel.html?"+$("#searchForm").serialize();
}



getList = (currentPage, IS_ASC, ORDER_KEY) => {
  CURR_PAGE = (currentPage == undefined) ? CURR_PAGE : currentPage;
  IS_SORT = (IS_ASC == undefined) ? '' : IS_ASC; 

  let form = $("#searchForm")[0];
  let formData = new FormData(form);
  formData.append('REQ_MODE', CASE_LIST);
  formData.append('PER_PAGE', $("#perPage").val());
  formData.append('CURR_PAGE', CURR_PAGE);
  formData.append('IS_SORT', IS_SORT);
  formData.append('ORDER_KEY', ORDER_KEY == undefined ? '' : ORDER_KEY);

  callFormAjax(REDUCE_URL, 'POST', formData).then((result)=>{
    console.log('getList SQL>>',result);
      if(isJson(result)){
          const data = JSON.parse(result);
          if(data.status == OK){
              const item = data.data;
              setTable(item[1], item[0], CURR_PAGE);
          }else{
              setHaveNoDataTable();
          }
      }else{
          alert(ERROR_MSG_1200);
      }
     
  });  
  
}
/** 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
  const tableText = "<tr><td class='text-center' colspan='"+tdLength+"'>조회된 데이터가 존재하지 않습니다.</tr></tr>";
  $('.pagination').html('');
  $('#dataTable').html(tableText);
}

setTable = (totalListCount, dataArray, currentPage) => {
  let tableText = '';
  listArray = dataArray;

  dataArray.map((data, index)=>{
      tableText += "<tr>";
      tableText += "<td class='text-center'>"+data.IDX+"</td>";
      tableText += "<td class='text-center'>"+data.NB_CLAIM_DT+"</td>";
      tableText += "<td class='text-center'>"+data.CSD_NM+"</td>";
      tableText += "<td class='text-left'>"+data.CRR_NM+"</td>";
      tableText += "<td class='text-left'>"+data.MGG_NM+"</td>";
      tableText += "<td class='text-center'>"+bizNoFormat(data.MGG_BIZ_ID)+"</td>";
      tableText += "<td class='text-left'>"+data.MII_NM+"</td>";
      tableText += "<td class='text-left'>"+nullChk(data.MIT_NM)+"</td>";
      tableText += "<td class='text-left'>"+data.NIC_NM+"</td>";
      tableText += "<td class='text-center'>"+data.NBI_REGI_NUM+"</td>";
      tableText += "<td class='text-center'>"+data.RLT_NM+"</td>";
      tableText += "<td class='text-center'>"+data.NB_CAR_NUM+"</td>";
      tableText += "<td class='text-center'>"+data.CCC_NM+"</td>";

      tableText += "<td class='text-right'>"+numberWithCommas(data.NB_TOTAL_PRICE)+"</td>";
      tableText += "<td class='text-center'>"+nullChk(data.NBI_INSU_DEPOSIT_DT)+"</td>";
      tableText += "<td class='text-right'>"+numberWithCommas(nullChk(data.NBI_INSU_DEPOSIT_PRICE,0))+"</td>"; // 지급금액
      tableText += "<td class='text-right'>"+numberWithCommas(nullChk(data.DECREMENT_PRICE,0))+"</td>"; // 감액금액
      tableText += "<td class='text-center'>"+data.DC_UNPAID_YN+"</td>"; // 
      tableText += "<td class='text-center'>"+nullChk(data.NBI_INSU_ADD_DEPOSIT_DT)+"</td>";
      tableText += "  <td class='text-right'>"+numberWithCommas(nullChk(data.NBI_INSU_ADD_DEPOSIT_PRICE, 0))+"</td>";
      tableText += "  <td class='text-right'>"+nullChk(data.DECREMENT_RATIO, 0)+"%</td>";
      tableText += "  <td class='text-center'>"+(data.NBI_INSU_DEPOSIT_YN == YN_N? '미입금': '입금')+"</td>";
      tableText += "  <td class='text-center'>"+nullChk(data.RDT_NM)+"</td>";
      tableText += "  <td class='text-center'>"+nullChk(data.NBI_REASON_DT)+"</td>";
      tableText += "  <td class='text-center'>"+nullChk(data.NBI_REASON_DTL_NM)+"</td>";
      tableText += "  <td class='text-center'><button type='button' class='btn btn-default' onclick='showDepositModal("+JSON.stringify(data)+")'>입금</button></td>";
      tableText += "</tr>"
      
  });
  paging(totalListCount, currentPage, 'getList'); 
  $('#dataTable').html(tableText);
}



/**
 * 감액 사유
 * @param {String} pSelectedVal select tag value 
 */
getSearchDecrementList = () => {
  const param = {
      REQ_MODE : REF_DECREMENT_REASON
  }
  console.log('REDUCE_URL>>',REDUCE_URL);
  callAjax(REDUCE_URL, 'GET', param).then((result)=>{
      console.log('getExcludeList SQL >>',result);
      const res = JSON.parse(result);
      // console.log('getExcludeList>>',res);
      if(res.status == OK){
          
          let optionText = '';
          $('#REASON_SELECT_SEARCH option').remove();
          res.data.map((item)=>{
              optionText += '<option value="'+item.ID+'" >'+item.NM+'</option>';
          });
          $('#REASON_SELECT_SEARCH').append('<option value="" selected>=입금여부상세=</option>'+optionText);
          $('#REASON_SELECT_SEARCH').selectpicker('refresh');
          // if(pSelectedVal != undefined) {
          //     $("#REASON_SELECT_SEARCH").val(pSelectedVal);
          // }
      }else{
          alert(ERROR_MSG_1100);
      }
  });
}