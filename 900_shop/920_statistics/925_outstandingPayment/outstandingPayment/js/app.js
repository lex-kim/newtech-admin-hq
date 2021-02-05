let currPage = 1;

$(document).ready(function() {

  if($('#pageIndicator').is(":visible")){
    $('#pageIndicator').hide();
  }

  $('.selectpicker').selectpicker();
  $('.bs-select-all').detach();
  $(".bs-deselect-all").css("float", "right");
  $(".bs-deselect-all").width("78%");
  $(".bs-deselect-all").html("전체 선택해제");

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

openUnpaidPopup = (data) => {
  const url = '/900_shop/920_statistics/925_outstandingPayment/outstandingPaymentDetail/outstandingPaymentDetail.html?partnerID='+data.partnerID;
  openWindowPopup(url, 'orderListDetail', 1280, 500);
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
    html += "<td class='text-center'>"+data.businessType+"</td>";
    html += "<td class='text-center'>"+data.partnerType+"</td>";
    html += "<td class='text-center'>"+data.buyType+"</td>";
    html += "<td class='text-center'>"+data.partnerID+"</td>";
    html += "<td class='text-center'>"+data.partnerName+"</td>";
    html += "<td class='text-center'>"+phoneFormat(data.phoneNum)+"</td>";
    html += "<td class='text-center'>"+phoneFormat(data.faxNum)+"</td>";
    html += "<td class='text-center'>"+data.chargeName+"</td>";
    html += "<td class='text-center'>"+phoneFormat(data.chargePhoneNum)+"</td>";
    html += "<td class='text-center'>"+phoneFormat(data.chargeTelNum)+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(data.totalBuyCount))+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(data.totalBuyAmountWithoutVAT))+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(data.totalBuyAmountVAT))+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(data.totalBuyAmount))+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(data.totalPayAmountWithoutVAT))+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(data.totalPayAmountVAT))+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(data.totalPayAmount))+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(data.outstandAmountNowWithoutVAT))+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(data.outstandAmountNowVAT))+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(data.outstandAmountNow))+"</td>";
    html += "<td class='text-center'>";
    html += "<button type='button' class='btn btn-default' onclick='openUnpaidPopup("+JSON.stringify(data)+")'>상세</button>";
    html += "</td>";
    html += "</tr>"
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

function onchangePartnerID(){
  if(empty($('#selectPartnerID').val())){
    $('#partnerID').val("");
  }  
}

function setAutoComplete(dataArr){
  try{
    const dataArray = JSON.parse(dataArr);
    $("#searchPartnerID").autocomplete({
        minLength : 1,
        source: dataArray.map((data, index) => {
            return {
                id : data.partnerID,
                value : data.partnerID
                // value : data.goodsName +" ["+data.itemID+"]"
            };
        }),
        change: function(event, ui){
          if(!empty(ui.item)){
            $('#partnerID').val(ui.item.id);
          }
          return false;
        },
        select: function(event, ui) {
            let strArea = ui.item.value;
            $("#searchPartnerID").val(strArea);
            $('#partnerID').val(ui.item.id);

            return false;
        }
    }).on('focus', function(){ 
        $(this).autocomplete("search"); 
    });
  }catch{
    alert("자동완성 설정에 실패하였습니다. 관라자에게 문의해주세요.")
  }  
}

function excelDownload(){
  location.href="outstandingPaymentExcel.php?"+$("#searchForm").serialize();
}
