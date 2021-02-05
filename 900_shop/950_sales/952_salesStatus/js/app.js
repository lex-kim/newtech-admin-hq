let CURR_PAGE = 1;


$(document).ready(function() {
  if($('#pageIndicator').is(":visible")){
    $('#pageIndicator').hide();
  }

  $( "#START_DT" ).datepicker(
      getDatePickerSetting('END_DT', 'minDate')
  );
  $( "#END_DT" ).datepicker(
      getDatePickerSetting('START_DT', 'maxDate')
  );

  const tdLength = $('table th').length;
  setInitTableData(tdLength);

  $('#searchForm').submit(function(e){
      e.preventDefault();

      $('#sortAsc').val('');
      $('#sortField').val('');

      getList(1);
  });

  $('.selectpicker').selectpicker();
  $('.bs-select-all').detach();
  $(".bs-deselect-all").css("float", "right");
  $(".bs-deselect-all").width("78%");
  $(".bs-deselect-all").html("전체 선택해제");

});


  
/** 동적으로 history table을 만든다 */
setPointList = () =>{
  let historyDataText = '';

  if(jsonData.length > 0){
      jsonData.map((data)=>{
        console.log(data);
        historyDataText += "<tr>";
        historyDataText += "<td class='text-center'>"+data.idx+"</td>";
        historyDataText += "<td class='text-center'>"+data.division+"</td>";
        historyDataText += "<td class='text-left'>"+data.area+"</td>";
        historyDataText += "<td class='text-left'>"+data.gwNm+"</td>";
        historyDataText += "<td class='text-center'>"+data.claim+"</td>";
        historyDataText += "<td class='text-center'>"+data.deal_YN+"</td>";
        historyDataText += "<td class='text-center'>"+data.faoNm+"</td>";
        historyDataText += "<td class='text-center'>"+data.gwTel+"</td>";
        historyDataText += "<td class='text-center'>"+data.faoPhone+"</td>";
        historyDataText += "<td class='text-center'>"+data.salesDT+"</td>";
        historyDataText += "<td class='text-right'>"+numberWithCommas(Number(data.salesTotal))+"</td>";
        historyDataText += "<td class='text-center'>"+data.depositDT+"</td>";
        historyDataText += "<td class='text-right'>"+numberWithCommas(Number(data.depositTotal))+"</td>";
        historyDataText += "<td class='text-right'>"+numberWithCommas(Number(data.cashTotal))+"</td>";
        historyDataText += "<td class='text-right'>"+numberWithCommas(Number(data.cardTotal))+"</td>";
        historyDataText += "<td class='text-right'>"+numberWithCommas(Number(data.noteTotal))+"</td>";
        historyDataText += "<td class='text-right'>"+numberWithCommas(Number(data.checkTotal))+"</td>";
        historyDataText += "<td class='text-right'>"+numberWithCommas(Number(data.balance))+"</td>";
        historyDataText += "</tr>"
      });
  }else{
      historyDataText = "<tr><td class='text-center' colspan='12'>정보가 존재하지 않습니다.</td></tr>";
  }
  
  console.log(historyDataText);
  $('#dataTable').html(historyDataText);
}
   

  /** 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
  const tdLength = $('table th').length;
  const tableText = "<tr><td class='text-center' colspan='"+tdLength+"'>조회된 데이터가 존재하지 않습니다.</tr></tr>";
  $('.pagination').html('');
  $('#dataTable').html(tableText);
}


function setPaymentTable(paymentArray){
  let html = "";
  let totalPrice = 0;
  let subHtml = "";

  headerArray.map(function(header){
    let isExistHeaderData =  false; 
    
    paymentArray.map(function(item){
      if(header.code == item.paymentCode){
        totalPrice += Number(item.paymentAmount);
        // subHtml += "<td class='text-right'>"+numberWithCommas(item.paymentAmount)+"</td>";
        html += "<td class='text-right'>"+numberWithCommas(item.paymentAmount)+"</td>";
        isExistHeaderData = true;
      }
    });

    if(!isExistHeaderData){
      html += "<td class='text-right'>-</td>";
    }
  });
  // html += "<td class='text-right'>"+numberWithCommas(totalPrice)+"</td>"+subHtml;

  return html;
}

setTable = (dataList, totalListCount, cPage) => {
  let html = "";
  dataList.map((data, idx)=>{
    html += "<tr>";
    html += "<td class='text-center'>"+data.issueCount+"</td>";
    html += "<td class='text-center'>"+(empty(data.divisionName) ? "-" : data.divisionName)+"</td>";
    html += "<td class='text-center'>"+(empty(data.regionName) ? "-" : data.regionName)+"</td>";
    html += "<td class='text-center'>"+(empty(data.garageName) ? "-" : data.garageName)+"</td>";
    html += "<td class='text-center'>"+(empty(data.billFormulaName) ? "-" : data.billFormulaName)+"</td>";
    html += "<td class='text-center'>"+(empty(data.contractStatusName) ? "-" : data.contractStatusName)+"</td>";
    html += "<td class='text-center'>"+(empty(data.chargeName) ? "-" : data.chargeName)+"</td>";
    html += "<td class='text-center'>"+(empty(data.phoneNum) ? "-" : phoneFormat(data.phoneNum))+"</td>";
    html += "<td class='text-center'>"+(empty(data.chargePhoneNum) ? "-" : phoneFormat(data.chargePhoneNum))+"</td>";
    html += "<td class='text-center'>"+(empty(data.issueDate) ? "-" : (data.issueDate == DEFAULT_DATE_FORMAT ? "-" : data.issueDate))+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(data.totalSalesAmount))+"</td>";
    html += "<td class='text-center'>"+(empty(data.depositDate) ? "-" : (data.depositDate == DEFAULT_DATE_FORMAT ? "-" : data.depositDate))+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(data.totalDepositAmount)+"</td>";
    html += setPaymentTable(data.paymentList); 

    html += "<td class='text-right'>"+numberWithCommas(Number(data.totalReceivableAmount))+"</td>";
    html += "</tr>"
  });

  paging(totalListCount, cPage, 'getList'); 
  $('#dataTable').html(html);
  $('#dataTable').show();
}

getList = (page) => {
  let time = 1;
  CURR_PAGE = (page == undefined ) ? CURR_PAGE : page;
  let form = $("#searchForm")[0];
  let formData = new FormData(form);
  formData.append('REQ_MODE', CASE_LIST);
  formData.append('PER_PAGE', $("#perPage").val());
  formData.append('CURR_PAGE', CURR_PAGE);

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
            setTable(res.data, res.total_item_cnt, CURR_PAGE);
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
        focus: function(event, ui){ 
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
  location.href="salesStatusExcel.php?"+$("#searchForm").serialize();
}