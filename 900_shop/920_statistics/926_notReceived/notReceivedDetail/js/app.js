let currPage = 1;

$(document).ready(function() {

  const tdLength = $('table th').length;
  setInitTableData(tdLength);

  $("#startDate").datepicker(
    getDatePickerSetting('endDate','minDate')
  ); 

  $("#endDate").datepicker(
    getDatePickerSetting('startDate','maxDate')
  ); 

  $('#searchForm').submit(function(e){
    e.preventDefault();
    getList(1);
    
  });

  const date = new Date();
  const month = date.getMonth();
  date.setMonth(month-1);

  setStartMonth(date);
  setLastMonth(date);

  getList(1);
 
});

function setStartMonth(myDate){
  $("#startDate").val(myDate.getFullYear() + '-' + ("0"+(myDate.getMonth()+1)).slice(-2) + '-01');
  $('#startDate').selectpicker('refresh');
}

function setLastMonth(myDate){
  let lastDate = new Date(myDate.getFullYear(), myDate.getMonth()+1, 0);
  $("#endDate").val(myDate.getFullYear() + '-' + ("0"+(myDate.getMonth()+1)).slice(-2) + '-'+lastDate.getDate());
  $('#endDate').selectpicker('refresh');
}

/** 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
  const tdLength = $('table th').length;
  const tableText = "<tr><td class='text-center' colspan='"+tdLength+"'>조회된 데이터가 존재하지 않습니다.</tr></tr>";
  $('.pagination').html('');
  $('#dataTable').html(tableText);
}

setTable = (dataList, totalListCount, cPage) => {
  let html = "";
  dataList.map((data, idx)=>{
    html += "<tr>";
    html += "<td class='text-center'>"+data.issueCount+"</td>";
    html += "<td class='text-center'>"+(empty(data.divisionName) ? "-":data.divisionName)+"</td>";
    html += "<td class='text-center'>"+(empty(data.regionName) ? "-":data.regionName)+"</td>";
    html += "<td class='text-center'>"+data.garageID+"</td>";
    html += "<td class='text-center'>"+data.garageName+"</td>";
    html += "<td class='text-center'>"+(empty(data.billFormulaName) ? "-":data.billFormulaName)+"</td>";
    html += "<td class='text-center'>"+data.contractStatusName+"</td>";
    html += "<td class='text-center'>"+data.chargeName+"</td>";
    html += "<td class='text-center'>"+phoneFormat(data.chargePhoneNum)+"</td>";
    html += "<td class='text-center'>"+phoneFormat(data.phoneNum)+"</td>";
    html += "<td class='text-center'>"+phoneFormat(data.faxNum)+"</td>";
    html += "<td class='text-center'>"+data.hqID+"</td>";
    html += "<td class='text-center'>"+data.orderID+"</td>";
    html += "<td class='text-center'>"+data.quotationID+"</td>";
    html += "<td class='text-center'>"+data.orderDetails+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(data.sellAmountWithoutVAT))+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(data.sellAmountVAT))+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(data.sellAmount))+"</td>";
    html += "<td class='text-center'>"+(empty(data.payDate) ? "-":data.payDate)+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(data.payAmountWithoutVAT))+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(data.payAmountVAT))+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(data.payAmount))+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(data.receivableAmountWithoutVAT))+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(data.receivableAmountVAT))+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(data.receivableAmount))+"</td>";
    html += "<td class='text-center'>";
    html += "<button type='button' class='btn btn-default' onclick='openUnpaidPopup("+JSON.stringify(data)+")'>결제</button>";
    html += "</td>";
    html += "</tr>";
  });

  paging(totalListCount, cPage, 'getList'); 
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
  formData.append('startDate', $('#startDate').val().replace(/-/g, ""));
  formData.append('endDate', $('#endDate').val().replace(/-/g, ""));

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
            setTable(res.data.data, res.data.totalCount, currPage);
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
   

function openUnpaidPopup(data){
  const url = '/900_shop/940_orderMngt/941_orderList/orderListDetail.php?orderID='+data.orderID;
  openWindowPopup(url, 'orderListDetail', 1220, 860);
}

function excelDownload(){
  location.href="nonPaymentDetailExcel.php?"+$("#searchForm").serialize();
}
