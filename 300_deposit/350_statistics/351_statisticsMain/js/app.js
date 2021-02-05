let STAT_URL = 'act/statistics_act.php'; 
let CURR_PAGE = 1;
let IS_SORT = undefined;        // 오름차순.내림차순 정렬여부
let ORDER_KEY = undefined;   // 월별청구건수정렬시 몇월달 인지
let tdLength = 0;

$(document).ready(function() {

  /** 검색 */
  $("#searchForm").submit(function(e){
      e.preventDefault();
          getList(1);
  });

  tdLength = $('table th').length;
  setInitTableData(tdLength);
  setIwOptions('SEARCH_INSU_NM');   //보험사
  setInsuTeam();  //보험사팀
  getRefOptions(REF_COOP_TYPE, 'RPT_ID_SEARCH', false, false);           // 협조구분 
  getRefOptions(REF_COOP_REACTION, 'RPR_ID_SEARCH', false, false);           // 협조반응 

  /* datepicker 설정 */
  $("#START_DT_SEARCH").datepicker(
      getDatePickerSetting('END_DT_SEARCH', 'minDate')
  );
  $("#END_DT_SEARCH").datepicker(
      getDatePickerSetting('START_DT_SEARCH', 'maxDate')
  );

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

  
});


/* 보험사Team 동적 생성 */
// setInsuTeam = () =>{
//   const data = {
//     REQ_MODE: GET_INSU_TEAM,
//   };
//   callAjax(STAT_URL, 'GET', data).then((result)=>{
//       const res = JSON.parse(result);
//       if(res.status == OK){
//           const dataArray = res.data; 
            
//           let optionText = '';
//           dataArray.map((item)=>{
//               optionText += '<option value="'+item.MIT_ID+'">'+item.MIT_NM+'</option>';
//           });
//           $('#SEARCH_INSU_TEAM').html(optionText);
//           $('#SEARCH_INSU_TEAM').selectpicker('refresh');
        
//       }else{
//           alert("서버와의 연결이 원활하지 않습니다.");
//       }
//   });
// }




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

  callFormAjax(STAT_URL, 'POST', formData).then((result)=>{
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

  dataArray.map((data, index)=>{
    
      if(data.COOP_INFO != null){
        data.COOP_INFO = data.COOP_INFO.split('|');
      }
      let decrementLink = '';
      let unpaidLink = '';
      if(data.DECREMENT_CNT > 0){
        decrementLink = "<a href='javascript:openDecrementPopup("+JSON.stringify(data)+")'>"+data.DECREMENT_CNT+"</a>"; 
      }else {
        decrementLink = data.DECREMENT_CNT;
      }

      if(data.UNPAID_CNT > 0){
        unpaidLink = "<a href='javascript:openUnpaidPopup("+JSON.stringify(data)+")'>"+data.UNPAID_CNT+"</a>"; 
      }else {
        unpaidLink =data.UNPAID_CNT;
      }
      tableText += "<tr>";
      tableText += "<td class='text-center'>"+data.IDX+"</td>";
      tableText += "<td class='text-center'>"+data.MII_NM+"</td>";
      tableText += "<td class='text-center'>"+nullChk(data.MIT_NM,'-')+"</td>";
      tableText += "<td class='text-center'>"+nullChk(data.NIC_NM,'-')+"</td>";
      tableText += "<td class='text-center'>"+(data.NIC_PHONE != null? phoneFormat(data.NIC_PHONE):'-')+"</td>";
      
      tableText += "<td class='text-right'>"+data.NB_TOTAL_CNT+"</td>"; //청구건수
      tableText += "<td class='text-right'>"+data.NB_TOTAL_PRICE+"</td>"; //청구금액
      tableText += "<td class='text-right'>"+data.NBI_TOTAL_DEPOSIT_CNT+"</td>";  //지급건수
      tableText += "<td class='text-right'>"+data.NBI_TOTAL_DEPOSIT_BILL_PRICE+"</td>"; //지급건 청구액
      tableText += "<td class='text-right'>"+data.NBI_TOTAL_DEPOSIT_PRICE+"</td>"; //지급금액
      tableText += "<td class='text-right'>"+data.DECREMENT_PRICE+"</td>"; //감액금액 5
      
      tableText += "<td class='text-right'>"+decrementLink+"</td>"; //감액건지급건수 -> 감액 팝업
      tableText += "<td class='text-right'>"+data.DECREMENT_BILL_PRICE+"</td>"; //감액건청구금액
      tableText += "<td class='text-right'>"+data.DECREMENT_DEPOSIT_PRICE+"</td>"; //감액건지급금액
      tableText += "<td class='text-right'>"+data.DECREMENT_DECREMENT_PRICE+"</td>"; //감액건감액금액
      tableText += "<td class='text-center'>"+data.DECREMENT_DEPOSIT_DT+"</td>"; //감액건감액입금일
      tableText += "<td class='text-right'>"+data.DECREMENT_ADD_DEPOSIT_PRICE+"</td>"; //감액건추가입금액
      tableText += "<td class='text-right'>"+data.DECREMENT_DC_RATIO+"%</td>"; //감액건감액률 7
      
      tableText += "<td class='text-right'>"+data.UNPAID_DONE_CNT+"</td>"; //미입금종결건수
      tableText += "<td class='text-right'>"+data.UNPAID_DONE_PRICE+"</td>"; //미입금종결금액
      tableText += "<td class='text-right'>"+data.DEPOSIT_CNT_RATIO+"%</td>"; //지급률건수
      tableText += "<td class='text-right'>"+data.DEPOSIT_PRICE_RATIO+"%</td>"; //지급률금액
      tableText += "<td class='text-right'>"+data.TOTAL_DECREMENT_RATIO+"%</td>"; //전체감액률
      tableText += "<td class='text-right'>"+unpaidLink+"</td>"; //미지급건수 -> 미지급 팝업
      tableText += "<td class='text-right'>"+data.UNPAID_PRICE+"</td>"; //미지급액
      tableText += "<td class='text-right'>"+data.UNPAID_CNT_RATIO+"%</td>"; //미지급률(건수) 8

      tableText += "<td class='text-center'>"+(data.COOP_INFO != null ? data.COOP_INFO[0]: '-')+"</td>"; //협조구분
      tableText += "<td class='text-center'>"+(data.COOP_INFO != null ? data.COOP_INFO[1]: '-')+"</td>"; //협조요청일
      tableText += "<td class='text-center'>"+(data.COOP_INFO != null ? data.COOP_INFO[2]: '-')+"</td>"; //협조반응
      tableText += "<td class='text-center'><button type='button' class='btn btn-default' onclick='openHelpPopup("+JSON.stringify(data)+")'>요청</button></td>"; //협조요청
      tableText += "</tr>"
      
  });
  paging(totalListCount, currentPage, 'getList'); 
  $('#dataTable').html(tableText);
}


openDecrementPopup = (data) => {
  let form = $('#sendForm')[0];
  let htmlTxt = '';
  htmlTxt += "<input type='hidden' name='NIC_ID' value='"+data.NIC_ID+"'>";
  
  $('#sendForm').html(htmlTxt);
  window.open('', 
                'reduction', 
                'width=1780,height=500'); 
  form.action = '/300_deposit/350_statistics/352_statisticsReduce/reduction.html';
  form.target = 'reduction';
  form.method = 'POST';
  form.submit();
}

openUnpaidPopup = (data) => {
  let form = $('#sendForm')[0];
  let htmlTxt = '';
  htmlTxt += "<input type='hidden' name='NIC_ID' value='"+data.NIC_ID+"'>";
  
  $('#sendForm').html(htmlTxt);
  window.open('', 
  'non-payment', 
  'width=1230,height=500'); 
  form.action = '/300_deposit/350_statistics/353_statisticsNonPay/nonPayment.html';
  form.target = 'non-payment';
  form.method = 'POST';
  form.submit();
}

openHelpPopup = (data) =>{
  let form = $('#sendForm')[0];
  let htmlTxt = '';
  htmlTxt += "<input type='hidden' name='MII_ID' value='"+data.MII_ID+"'>";
  htmlTxt += "<input type='hidden' name='MII_NM' value='"+data.MII_NM+"'>";
  htmlTxt += "<input type='hidden' name='NIC_ID' value='"+data.NIC_ID+"'>";
  htmlTxt += "<input type='hidden' name='NIC_NM' value='"+data.NIC_NM+"'>";
  
  $('#sendForm').html(htmlTxt);
  // console.log('일괄이첨 >>>',htmlTxt);
  window.open('', 
            'requireshelp', 
            'width=995,height=550'); 
  form.action = '/300_deposit/350_statistics/354_statisticsHelp/requiresHelp.html';
  form.target = 'requireshelp';
  form.method = 'POST';
  form.submit();
}

excelDownload = () => {
  location.href="statisticsExcel.html?"+$("#searchForm").serialize();
}