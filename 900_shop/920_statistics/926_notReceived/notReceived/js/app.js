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
  
/** 동적으로 history table을 만든다 */
setProductList = () =>{
  let historyDataText = '';

  if(jsonData.length > 0){
      jsonData.map((data)=>{
        console.log(data);
        historyDataText += "<tr>";
        historyDataText += "<td class='text-center'>"+data.IDX+"</td>";
        historyDataText += "<td class='text-center'>"+data.division+"</td>";
        historyDataText += "<td class='text-left'>"+data.area+"</td>";
        historyDataText += "<td class='text-left'>"+data.storeNm+"</td>";
        historyDataText += "<td class='text-center'>"+data.retID+"</td>";
        if(data.dealYN=="Y"){
          historyDataText += "<td class='text-center'>거래</td>";
        }else{
          historyDataText += "<td class='text-center'>미거래</td>";
        }
        historyDataText += "<td class='text-center'>"+data.storePhone+"</td>";
        historyDataText += "<td class='text-center'>"+data.storeFAX+"</td>";
        historyDataText += "<td class='text-center'>"+data.FaoNm+"</td>";
        historyDataText += "<td class='text-center'>"+data.FaoPhone+"</td>";
        historyDataText += "<td class='text-right'>"+numberWithCommas(Number(data.lastYearFinancialObligation))+"</td>";
        historyDataText += "<td class='text-right'>"+numberWithCommas(Number(data.purchasePrice))+"</td>";
        historyDataText += "<td class='text-right'>"+numberWithCommas(Number(data.purchaseTax))+"</td>";
        historyDataText += "<td class='text-right'>"+numberWithCommas(Number(data.purchaseTotal))+"</td>";
        historyDataText += "<td class='text-right'>"+numberWithCommas(Number(data.paymentPrice))+"</td>";
        historyDataText += "<td class='text-right'>"+numberWithCommas(Number(data.paymentTax))+"</td>";
        historyDataText += "<td class='text-right'>"+numberWithCommas(Number(data.paymentTotal))+"</td>";
        historyDataText += "<td class='text-right'>"+numberWithCommas(Number(data.paymentBalance))+"</td>";
        historyDataText += "<td class='text-center'>";
        historyDataText += "<button type='button' class='btn btn-default' onclick='openUnpaidPopup("+JSON.stringify(data)+")'>상세</button>";
        historyDataText += "<button type='button' class='btn btn-default' onclick='showUpdateModal("+JSON.stringify(data)+")'>수정</button>";
        historyDataText += "</td>";
        historyDataText += "</tr>"
      });
  }else{
      historyDataText = "<tr><td class='text-center' colspan='9'>정보가 존재하지 않습니다.</td></tr>";
  }
  
  console.log(historyDataText);
  $('#productList').html(historyDataText);
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
    html += "<td class='text-center'>"+(empty(data.divisionName) ? '-' : data.divisionName)+"</td>";
    html += "<td class='text-left'>"+(empty(data.regionName) ? '-' : data.regionName)+"</td>";
    html += "<td class='text-left'>"+data.garageID+"</td>";
    html += "<td class='text-left'>"+data.garageName+"</td>";
    html += "<td class='text-left'>"+(empty(data.billFormulaName) ? '-' : data.billFormulaName)+"</td>";
    html += "<td class='text-center'>"+data.contractStatusName+"</td>";
    html += "<td class='text-center'>"+(empty(data.chargeName) ? '-' : data.chargeName)+"</td>";
    html += "<td class='text-center'>"+phoneFormat(data.chargePhoneNum)+"</td>";
    html += "<td class='text-center'>"+phoneFormat(data.phoneNum)+"</td>";
    html += "<td class='text-center'>"+phoneFormat(data.faxNum)+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(data.totalSellCount))+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(data.totalSellAmountWithoutVAT))+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(data.totalSellAmountVAT))+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(data.totalSellAmount))+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(data.totalPayAmountWithoutVAT))+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(data.totalPayAmountVAT))+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(data.totalPayAmount))+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(data.totalReceivableAmountNowWithoutVAT))+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(data.totalReceivableAmountNowVAT))+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(data.totalReceivableAmountNow))+"</td>";
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

openUnpaidPopup = (data) => {
  const url = '/900_shop/920_statistics/926_notReceived/notReceivedDetail/notReceivedDetail.html?garageID='+data.garageID;
  openWindowPopup(url, 'notReceivedDetailPopup', 1280, 500);
}

function setAutoComplete(dataArr){
  try{
    const dataArray = JSON.parse(dataArr);
    
    $("#searchGarageID").autocomplete({
        minLength : 1,
        source: dataArray.map((data, index) => {
            return {
                id : data.garageID,
                value : data.garageID
                // value : data.goodsName +" ["+data.itemID+"]"
            };
        }),
        change: function(event, ui){
          if(!empty(ui.item)){
            $('#garageID').val(ui.item.id);
          }
          return false;
        },
        select: function(event, ui) {
            let strArea = ui.item.value;
            $("#searchGarageID").val(strArea);
            $('#garageID').val(ui.item.id);

            return false;
        }
    }).on('focus', function(){ 
        $(this).autocomplete("search"); 
    });
  }catch{
    alert("자동완성 설정에 실패하였습니다. 관라자에게 문의해주세요.")
  }  
}

function onchangeGarageID(){
  if(empty($('#searchGarageID').val())){
    $('#garageID').val('');
  }
}

function excelDownload(){
  location.href="nonPaymentExcel.php?"+$("#searchForm").serialize();
}
