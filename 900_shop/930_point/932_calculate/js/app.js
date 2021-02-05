let currPage = 1;

$(document).ready(function() {
  if($('#pageIndicator').is(":visible")){
      $('#pageIndicator').hide();
  }
  const tdLength = $('table th').length;
  setInitTableData(tdLength);

  $( "#startDate" ).datepicker(
      getDatePickerSetting('endDate', 'minDate')
  );
  $( "#endDate" ).datepicker(
      getDatePickerSetting('startDate', 'maxDate')
  );

  $('#searchForm').submit(function(e){
      e.preventDefault();

      getList(1);
  });
});


  
/** 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
  const tdLength = $('table th').length;
  const tableText = "<tr><td class='text-center' colspan='"+tdLength+"'>조회된 데이터가 존재하지 않습니다.</tr></tr>";
  $('.pagination').html('');
  $('#dataTable').html(tableText);
}

setTable = (dataList, totalListCount, cPage) => {
  let html = "";

  if(!empty(dataList.statusList) && dataList.statusList.length > 0){
    (dataList.statusList).map((item, idx)=>{
        html += "<tr>";
        html += "<td class='text-center'>"+item.issueCount+"</td>";
        html += "<td class='text-center'>"+(empty(item.issueDate)  ? "-" : item.issueDate)+"</td>";
        html += "<td class='text-right'>"+numberWithCommas(item.pointEarnExpect)+"</td>";
        html += "<td class='text-right'>"+numberWithCommas(item.pointEarn)+"</td>";
        html += "<td class='text-right'>"+numberWithCommas(item.pointRedeemExpect)+"</td>";
        html += "<td class='text-right'>"+numberWithCommas(item.pointRedeem)+"</td>";
        html += "<td class='text-right'>"+numberWithCommas(item.pointExpireExpect)+"</td>";
        html += "<td class='text-right'>"+numberWithCommas(item.pointExpire)+"</td>";
        html += "</tr>"
    });
  
    paging(totalListCount, cPage, 'getList'); 
  }

  if(!$('#totalBalanceTable').is(":visible")){                                    // 통계보여주는 테이블
    const summaryData = dataList.summary;

    $('#totalBalance').html(numberWithCommas(summaryData.pointBalance));
    $('#pointEarnExpect').html(numberWithCommas(summaryData.pointEarnExpect));
    $('#pointEarn').html(numberWithCommas(summaryData.pointEarn));
    $('#pointRedeemExpect').html(numberWithCommas(summaryData.pointRedeemExpect));
    $('#pointRedeem').html(numberWithCommas(summaryData.pointRedeem));
    $('#pointExpireExpect').html(numberWithCommas(summaryData.pointExpireExpect));
    $('#pointExpire').html(numberWithCommas(summaryData.pointExpire));
    $('#totalBalanceTable').show();
  }

  $('#dataTable').html(html);
  $('#dataTable').show();
}

getList = (page) => {
  let time = 1;
  currPage = (page == undefined ) ? currPage : page;
  let form = $("#searchForm")[0];
  let formData = new FormData(form);
  formData.append('REQ_MODE', CASE_LIST);
  formData.append('PER_PAGE', $("#perPage").val());
  formData.append('CURR_PAGE', currPage);

  let ajaxTimeInterver = setInterval(function(){
      time += 1;
      if(time > 2){
          $('#dataTable').hide();
          $('#indicator').show();
          $('#indicatorTable').show();

          clearInterval(ajaxTimeInterver);
      }
  },150);


  callFormAjax(ERP_CONTROLLER_URL, 'POST', formData).then((result)=>{
      if(isJson(result)){
          const res = JSON.parse(result);
          if(res.status == OK){
            if(res.data == HAVE_NO_DATA || res.data.length == 0){
                setHaveNoDataTable();
            }else{
                setTable(res.data, res.total_item_cnt, currPage);
            } 
          }else{
          alert((empty(res.message) ? ERROR_MSG_1100 : res.message));
          }
      }else{
          alert(ERROR_MSG_1200);
      }

      clearInterval(ajaxTimeInterver);
      if($('#indicatorTable').is(":visible")){
          $('#indicatorTable').hide();
      }
      $('#dataTable').show();
      
  });  
}

function excelDownload(){
  location.href="calculateExcel.php?"+$("#searchForm").serialize();
}
 