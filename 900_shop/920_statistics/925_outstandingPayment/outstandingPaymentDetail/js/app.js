let depositMaxPrice = 0;
let stockOrderID = "";

$(document).ready(function() {
  if($('#pageIndicator').is(":visible")){
    $('#pageIndicator').hide();
  }

  const tdLength = $('table th').length;
  setInitTableData(tdLength);

  $("#startDate").datepicker(
    getDatePickerSetting('endDate','minDate')
  ); 

  $("#endDate").datepicker(
    getDatePickerSetting('startDate','maxDate')
  ); 

  /** 입금일 */
  $("#MODAL_PAY_DT" ).datepicker(getDatePickerSetting());

  $('#PAY_MODAL').on('hidden.bs.modal', function () {
      $("#PAY_MODAL input").val("");
  });

  /** 입금처리*/
  $('#depositForm').submit(function(e){
      e.preventDefault();

      if(!empty(stockOrderID)){
        let form = $(this)[0];
        let formData = new FormData(form);
        formData.append('REQ_MODE', CASE_UPDATE);
        formData.append('stockOrderID', stockOrderID);
  
        confirmSwalTwoButton(
            '입금처리 하시겠습니까?','확인', '취소', false ,
            ()=>{
              setData(formData);
            }, ''
        );
      }
     
  });


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


function onchangeDepositPrice(data){
  const inputPriceValue = $('#MODAL_PAY_PRICE').val();
  const totalPrice = depositMaxPrice;

  if(!empty(inputPriceValue)){
    if((Number(inputPriceValue) >  Number(totalPrice)) && !empty(totalPrice)){
      $('#MODAL_PAY_PRICE').val(totalPrice);
    }
  }
}

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
    html += "<td class='text-center'>"+(empty(data.buyDate) ? "-":data.buyDate)+"</td>";
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
    html += "<td class='text-center'>"+(empty(data.orderDetails) ? "-":data.orderDetails)+"</td>"
    html += "<td class='text-right'>"+numberWithCommas(Number(data.amountWithoutVAT))+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(data.amountVAT))+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(data.amount))+"</td>";
    html += "<td class='text-center'>"+(empty(data.payDate) ? "-":data.payDate)+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(data.payWithoutVAT))+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(data.payVAT))+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(data.pay))+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(data.outstandAmountWithoutVAT))+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(data.outstandAmountVAT))+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(data.outstandAmount))+"</td>";
    html += "<td class='text-center'>";
    html += "<button type='button' class='btn btn-default' onclick='openUnpaidPopup("+JSON.stringify(data)+")'>결제</button>";
    html += "</td>";
    html += "</tr>";
  });

  paging(totalListCount, cPage, 'getList'); 
  $('#dataTable').html(html);
  $('#dataTable').show();
}

function openUnpaidPopup(data){
  depositMaxPrice = data.amount;
  stockOrderID = data.stockOrderID;

  setTimeout(function() { 
    $('#MODAL_PAY_PRICE').focus() ;
  }, 250);

  $('#PAY_MODAL').modal('show');
}

function setData(formData){
  callFormAjax(ERP_CONTROLLER_URL, 'POST', formData).then((result)=>{
    if(isJson(result)){
      const res = JSON.parse(result);
      if(res.status == OK){
        alert("처리 되었습니다.");

        getList();
        $('#PAY_MODAL').modal('hide');
      }else{
        alert((empty(res.message) ? ERROR_MSG_1100 : res.message));
      }
    }else{
        alert(ERROR_MSG_1200);
    }
  });  
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
  location.href="outstandingPaymentDetailExcel.php?"+$("#searchForm").serialize();
}
   