let CURR_PAGE = 1;

$(document).ready(function() {
  const tdLength = $('table th').length;
  setInitTableData(tdLength);
  
  $('#searchForm').submit(function(e){
      e.preventDefault();
      getList(1);
  });
  
  $('#orderTab li').click(function(e){
    $('#orderTab li').attr("class","");
    $(this).attr("class","active");
    $('#tabValue').val($(this).data('mode'));

    getList(1, $(this).data('mode'));
  });

  $( "#START_DT_SEARCH" ).datepicker(
      getDatePickerSetting('END_DT_SEARCH','minDate')
  );
  $( "#END_DT_SEARCH" ).datepicker(
      getDatePickerSetting('START_DT_SEARCH','maxDate')    
  );

  $('#phoneNum-modal').on('hidden.bs.modal', function () {
      $('#SEND_PHONE_NUM').val("");
  }); 
  /** 실시간 이체 모달에서 환불요청 했을 시 */
  $('#transferForm').submit(function(e){
      e.preventDefault();

      if($('#cancel-modal').is(":visible")){
        $('#cancel-modal').modal('hide');
      }
      onConfirmPgRefund('이체' , 33, $('#transferForm').serialize());
  });

  /** 실시간 이체 모달 클로즈 이벤트 */
  $('#transfer_modal').on('hidden.bs.modal', function () {
      $('#mgr_bank_cd').val("");
      $('#mgr_account').val("");
      $('#mgr_depositor').val("");
  });

  $('#cancel-modal').on('show.bs.modal', function () {
      $('input:radio[name=cancelTypeCode]').eq(0).prop("checked", true);
      $('#ORDER_CANCEL_TYPE').val("");
  });


  /** 입금여부 */
  if($("#depositTable").is(":visible")){
    $(":input:radio[name=DEPOSIT_YN]").click(function(){
      const checkedValue = $(":input:radio[name=DEPOSIT_YN]:checked").val();
  
      if(checkedValue == "Y"){
        $('#depositTable input[type=text]').attr("disabled",false);
      }else{
        $('#depositTable input[type=text]').attr("disabled",true);
      }
    });
    $('#depositTable input[type=radio]:checked').trigger("click");
  }

  /** 배송방법 */
  if($("#deliveryTable").is(":visible")){
    $(":input:radio[name=DELIVERY_METHOD]").click(function(){
      var checkedValue = $(":input:radio[name=DELIVERY_METHOD]:checked").val();
      if(checkedValue == EC_DELIVERY_METHOD){// 택배
        $('#deliveryTable input[type=text], #deliveryTable select').attr("disabled", false);
      }else { // 택배 외 경우
        $('#deliveryTable input[type=text], #deliveryTable select').attr("disabled", true);
      }
    });
    $('#deliveryTable input[type=radio]:checked').trigger("click");
  }

  /** 교환 및 반품 */
  if($("#cancelTable").is(":visible")){
    $(":input:radio[name=RETURN_REASON]").click(function(){
      const selectedValue = $(":input:radio[name=RETURN_REASON]:checked").val();
      if(selectedValue == EC_RETURN_TYPE_CHANGE){// 교환
        $(".resend *").attr("disabled", false)
        $(".cancel *").attr("disabled", false);
      }else{  // 반품
        $(".resend *").attr("disabled", true);
        $(".cancel *").attr("disabled", false);
      }
    });

    /** 반품방법-택배종류 */
    $(":input:radio[name=RETURN_METHOD]").click(function(){
      const selectedValue = $(":input:radio[name=RETURN_METHOD]:checked").val();
      if(selectedValue == EC_RETURN_METHOD_DELIVERY){
        $(".RETURN_FEE_INPUT").attr("disabled", false);
      }else{
        $(".RETURN_FEE_INPUT").attr("disabled", true);
      }
    });

    /** 재배송-택배종류 */
    $(":input:radio[name=REDELIVERY_METHOD]").click(function(){
      const selectedValue = $(":input:radio[name=REDELIVERY_METHOD]:checked").val();
      if(selectedValue == EC_RETURN_METHOD_DELIVERY){
        $(".REDELIVERY_FEE_INPUT").attr("disabled", false);
      }else{
        $(".REDELIVERY_FEE_INPUT").attr("disabled", true);
      }
    });

    $('#cancelTable input[type=radio]:checked').trigger("click");
    
  }
});


excelDownload = () => {
  location.href="orderListExcel.php?"+$("#searchForm").serialize()+"&REQ_MODE="+CASE_TOTAL_LIST;
}

function getPoint(usePoint){
  if(usePoint == 0){
    return usePoint;
  }else{
    return "-"+numberWithCommas(usePoint); 
  }
}

function getOrderPrint(dateTime, userName, userID){
  if(empty(dateTime)){
    return "-";
  }else{
    return dateTime+"<br>("+(empty(userName) ? userID : userName) +")";
  }
}

/** 배송추적 */
function getOrderDeliveryStatus(data){
  if(!empty(data.itemLogisticsURI)){
    window.open(data.itemLogisticsURI+data.itemLogisticsInvoiceNum, 'deliveryWindow');
  }else{
    alert("배송 상태가 아닙니다.");
  }
}

/** 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
  const tdLength = $('table th').length;
  const tableText = "<tr><td class='text-center' colspan='"+tdLength+"'>조회된 데이터가 존재하지 않습니다.</tr></tr>";
  $('.pagination').html('');
  $('#dataTable').html(tableText);
}
  
/** 동적으로 history table을 만든다 */
setTable = (dataList, totalListCount, cPage) => {
  let historyDataText = '';
  dataList.map((data)=>{
      let depositPrice = empty(data.depositPrice) ? 0 : data.depositPrice;
      historyDataText += "<tr>";
      historyDataText += "<td class='text-center'>"+data.issueCount+"</td>";
      historyDataText += "<td class='text-center'>"+data.writeDatetime+"</td>";
      historyDataText += "<td class='text-center'>"+data.orderID+"</td>";
      historyDataText += "<td class='text-center'>"+data.orderStatus+"</td>";
      historyDataText += "<td class='text-center'>"+data.contractStatus+"</td>";
      historyDataText += "<td class='text-center'>"+data.divisionName+"</td>";
      historyDataText += "<td class='text-left'>"+data.regionName+"</td>";
      historyDataText += "<td class='text-left'>"+data.garageName+"</td>";
      historyDataText += "<td class='text-left'>"+(empty(data.orderDetails)?"-":data.orderDetails)+"</td>";
      historyDataText += "<td class='text-right'>"+numberWithCommas(Number(data.orderPrice))+"</td>";
      historyDataText += "<td class='text-right'>"+numberWithCommas(Number(data.deliveryPrice))+"</td>";
      historyDataText += "<td class='text-right'>"+getPoint(data.pointRedeem)+"</td>";
      historyDataText += "<td class='text-right'>"+numberWithCommas(Number(data.totalPrice))+"</td>";
      historyDataText += "<td class='text-center'>"+(empty(data.depositDate)?"-":data.depositDate)+"</td>";
      historyDataText += "<td class='text-right'>"+numberWithCommas(depositPrice)+"</td>";
      historyDataText += "<td class='text-right'>"+numberWithCommas(data.totalPrice - depositPrice)+"</td>";
      historyDataText += "<td class='text-right'>"+(isNaN((depositPrice/data.totalPrice)*100) ? 0 : (100-(depositPrice/data.totalPrice)*100)).toFixed(2) +"%</td>";
      historyDataText += "<td class='text-center'>"+data.deliveryMethod;
      historyDataText += "</td>";
      historyDataText += "<td class='text-center'>"+(data.depositYN == "Y" ?"입금":"미입금" )+"</td>";
      historyDataText += "<td class='text-center'>"+data.payment+"</td>";
      historyDataText += "<td class='text-center'>"+(empty(data.cancelType)?"-":data.cancelType)+"</td>";
      historyDataText += "<td class='text-center'>"+(empty(data.cancelDetailType)?"-":data.cancelDetailType)+"</td>";
      historyDataText += "<td class='text-center'>"+(empty(data.billRequestDatetime)?"-":data.billRequestDatetime)+"</td>";
      historyDataText += "<td class='text-center'>"+getOrderPrint(data.specificationPrintDatetime, data.specificationPrintUserName, data.specificationPrintUserID)+"</td>";
      historyDataText += "<td class='text-left'>"+(empty(data.memo) ? "-" :data.memo.replaceAll("\r\n","<br>"))+"</td>";
      historyDataText += "<td class='text-center'>"+data.billType+"</td>";
      historyDataText += "<td class='text-center'>";
      if(data.paymentCode == EC_PAYMENT_TYPE_DEPOSIT && data.billTypeCode != EC_BILL_TYPE_NO){ // 무통장입금 이자 지출증빙이 신청안함이 아닌경우
        if(empty(data.billRequestDatetime)){
          historyDataText += "<button type='button' class='btn btn-default' id='add-new-btn' onclick='onPublishBill(\""+data.orderID+"\")'>발행</button>";
        }else{
          historyDataText += "<button type='button' class='btn btn-default' id='add-new-btn' onclick='onCancelBill(\""+data.orderID+"\")'>취소</button>";
        }
      }else{
        historyDataText += "-";
      }
      historyDataText += "</td>"
      historyDataText += "<td class='text-center'><button type='button' class='btn btn-default' id='add-new-btn' onclick='onClickEdit(\""+data.orderID+"\")'>조회</button>";
      if(data.orderStatusCode != EC_ORDER_STATUS_CANCEL){
        historyDataText += "<button type='button' class='btn btn-default' id='add-new-btn' onclick='onClickTransaction(\""+data.orderID+"\")'>거래명세서</button>";
      }
      historyDataText += "</tr>"
  });
  paging(totalListCount, cPage, 'getList'); 
  $('#dataTable').html(historyDataText);
}


getList = (page, tabMode) => {
  CURR_PAGE = (page == undefined ) ? CURR_PAGE : page;
  let form = $("#searchForm")[0];
  let formData = new FormData(form);
  formData.append('REQ_MODE', CASE_LIST);
  formData.append('PER_PAGE', $("#perPage").val());
  formData.append('CURR_PAGE', CURR_PAGE);
  formData.append('TAB_MODE', (tabMode == undefined ?  $('#tabValue').val() : tabMode));

  callFormAjax(ERP_CONTROLLER_URL, 'POST', formData).then((result)=>{
      if(isJson(result)){
          const res = JSON.parse(result);
          if(res.status == OK){
              if(res.data == HAVE_NO_DATA){
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
      
  });  
}

function setBillData(data, text){
  callAjax(ERP_CONTROLLER_URL, 'POST', data).then((result)=>{
    if(isJson(result)){
      const res = JSON.parse(result);
      if(res.status == OK){
        alert(text+"되었습니다.");

        if(opener != null){
          window.opener.getList();
          window.close();
        }else{
          getList();
        }
        
      }else{
        const errorCode = res.data.errorCode;
        if(errorCode == ERROR_DB_UPDATE_DATA){
          alert("이미 발행된 주문입니다.");
        }else{
          alert((empty(res.message) ? ERROR_MSG_1100 : res.message));
        }
      }
    }else{
      alert(ERROR_MSG_1200);
    } 
  });
}

function onCancelBill(orderID){
  confirmSwalTwoButton('발행취소 하시겠습니까?','확인','취소',true,function(){
    const data = {
      REQ_MODE : CASE_DELETE,
      orderID : orderID,
    };
    setBillData(data , "취소");
  }); 
}

function onPublishBill(orderID){
  confirmSwalTwoButton('발행하시겠습니까?','확인','취소',false,function(){
    const data = {
      REQ_MODE : CASE_CREATE,
      orderID : orderID,
      urlType : "Expenditure"
    };
    setBillData(data , "발행");
  }); 
}

function getFileName(orderId){
  const date = new Date();
  const year = date.getFullYear();
  const month = date.getMonth()+1;
  const day = date.getDate();
  const hour = date.getHours();

  return year+"/"+month+"/"+day+"/"+hour+"/"+orderId;
}

function pdfDownload(orderId){
  $('.bill-footer').hide();
  $('footer').hide();

  html2canvas($('body')[0]).then(function(canvas) {
    let imgData = canvas.toDataURL('image/png');

    let imgWidth = $('#printMode').val() == "p" ? 210 : 297; // 이미지 가로 길이(mm) A4 기준
    let pageHeight = $('#printMode').val() == "p" ? imgWidth * 1.414 : imgWidth / 1.414; ;  // 출력 페이지 세로 길이 계산 A4 기준
    let imgHeight = canvas.height * imgWidth / canvas.width;
    let heightLeft = imgHeight;

    let doc = new jsPDF($('#printMode').val(), 'mm' ,'a4');
    let position = 0;
    let xPosition = $('#printMode').val() == "p" ? 0 : 10;

    // 첫 페이지 출력
    doc.addImage(imgData, 'PNG', xPosition, position, imgWidth, imgHeight);
    heightLeft -= pageHeight;
    while (heightLeft >= 5) {
        position = heightLeft - imgHeight;
        doc.addPage();
        doc.addImage(imgData, 'PNG', xPosition, position, imgWidth, imgHeight);
        heightLeft -= pageHeight;
    }
    doc.save(getFileName(orderId)+'.pdf'); 
  });
  $('.bill-footer').show();
  $('footer').show();
}

function getParentList(){
  window.opener.getList();
}

function onPrint(orderID){
    if(!empty(orderID)){  // 거래명세서 출력
      const data = {
        REQ_MODE : CASE_CREATE,
        orderID : orderID,
      }
      callAjax(ERP_CONTROLLER_URL, 'POST', data).then((result)=>{
          if(isJson(result)){
              const res = JSON.parse(result);
              if(res.status == OK){
                try{
                   window.opener.getParentList();
                }catch{
                  window.opener.getList();
                }
                
                $('.bill-footer').hide();
                $('footer').hide();
                window.print();
                window.close();
                $('.bill-footer').show();
                $('footer').show();
              }else{
                  alert((empty(res.message) ? ERROR_MSG_1100 : res.message));
              }
          }else{
              alert(ERROR_MSG_1200);
          } 
      });   
    }else{  // 주문서 출력
      $('.bill-footer').hide();
      $('footer').hide();
      window.print();
      window.close();
      $('.bill-footer').show();
      $('footer').show();
    }
     
}

// 조회버튼 클릭
onClickEdit = (orderID) => {
  const url = '/900_shop/940_orderMngt/941_orderList/orderListDetail.php?orderID='+orderID;
  openWindowPopup(url, 'orderListDetail', 1220, 860);
}

// 주문서 버튼 클릭
onClickBillPrint = (orderID) => {
  const url = '/900_shop/940_orderMngt/941_orderList/orderBill.php?orderID='+orderID;
  openWindowPopup(url, 'orderBill', 1200, 860);
}


// 주문서작성 버튼 클릭
onClickOrder = (brandSeq) => {
  const url = '/900_shop/940_orderMngt/942_order/estimatePopup.php';
  openWindowPopup(url, 'estimatePopup', 1320, 860);
}

// 거래명세서 버튼 클릭
onClickTransaction = (orderID) => {
  const url = '/900_shop/940_orderMngt/941_orderList/transactionBill.php?orderID='+orderID;
  openWindowPopup(url, 'transactionBill', 800, 860);              
}




// 위탁발주 발주서작성 버튼 클릭
onClickConsigmentWrite = (partnerID, orderID, garageID) => {
  const url = '/900_shop/960_inout/965_inoutOrder/inoutWrite.php?partnerID='+partnerID+"&orderID="+orderID+"&garageID="+garageID;
  openWindowPopup(url, 'consignmentWrite', 1200, 860);
}
// 위탁발주 발주서조회 버튼 클릭
onClickConsigmentView = (data) => {
  if(!empty(data.stockOrderID)){
    const url = '/900_shop/960_inout/965_inoutOrder/inoutView.php?stockOrderID='+data.stockOrderID;
    openWindowPopup(url, 'transactionBill', 800, 860);   
  }else{
    alert("작성된 발주서가 존재하지 않습니다.");
  }
  
}

// 결제방법에 따른 결제하기버튼의 메세지 멘트
onClickSMSSend = () => {
  setTimeout(function(){
    $('#SEND_PHONE_NUM').focus();
  },300);
  $('#phoneNum-modal').modal();
}

// 결제방법에 따른 결제하기버튼의 메세지 멘트
onClickSMSPayment = () => {
  confirmSwalTwoButton('담당자('+phoneFormat($('#SEND_PHONE_NUM').val())+')로  결제정보를 전송하시겠습니까?','확인','취소',false,function(){
    console.log("전송전송");
  }); 
}

onClickOrderCancel = () => {
  isClickPaymentCancel = false;
  $('#cancel-modal #submitBtn').html("주문취소");
  $('#cancel-modal').modal();
}

function onDownloadDeliveryFormat(){
  location.href="deliveryCSV.php";
}