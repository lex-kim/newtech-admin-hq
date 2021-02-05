
let CURR_PAGE = 1;
let stockOrderList = [];
let stockOrderID = "";
let sendTitle = "";           //FAX , SMS 중 어느것을 보내는지에 대한 텍스트

$(document).ready(function() {
  if($('#pageIndicator').is(":visible")){
    $('#pageIndicator').hide();
  }
  const tdLength = $('table th').length;
  setInitTableData(tdLength);

 /** 리스트 탭 이벤트 */
  $('#orderTab li').click(function(e){
    $('#orderTab li').attr("class","");
    $(this).attr("class","active");
    $('#tabValue').val($(this).data('mode'));

    getList(1, $(this).data('mode'));
  });

  $('#searchForm').submit(function(e){
      e.preventDefault();
      getList(1);
  });

  /** FAX, SMS 발송 시 */
  $('#phoneForm').submit(function(e){
      e.preventDefault();

      confirmSwalTwoButton(''+phoneFormat($('#SEND_PHONE_NUM').val())+'로 '+sendTitle+' 발송 하시겠습니까?','확인','취소',false,function(){
        onClickSendSmsBtn();
      }); 
  });

  /** 발송 모달 닫기 이벤트 */
  $('#phoneNum-modal').on('hidden.bs.modal', function () {
    $('#SEND_PHONE_NUM').val("");
    sendTitle = "";
  });
  
  $( "#START_DT" ).datepicker(
      getDatePickerSetting('END_DT','minDate')
  );
  $( "#END_DT" ).datepicker(
      getDatePickerSetting('START_DT','maxDate')    
  );

  if($('#ORDER_DT').is(":visible")){
   
    $( "#ORDER_DT" ).datepicker(
        getDatePickerSetting('DELIVERY_DT','minDate')    
    );
    
    $( "#DELIVERY_DT" ).datepicker(
        getDatePickerSetting('ORDER_DT','maxDate')    
    );
    $("#ORDER_DT").datepicker('setDate', 'today');      // 기본 현재 날짜로
    
  }

  $('.date').change(function(e){
    var date_pattern = /^(19|20)\d{2}-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[0-1])$/; 
    if(!date_pattern .test($(this).val())){
      alert("yyyy-mm-dd 양식에 올바르지 않습니다.");
      $(this).val("");
      $(this).focus();
      return;
    }
  });

  $('.modal').on('hidden.bs.modal', function () {
    // $('.modal input, select').val("");
  });

  $('.stockOrderForm').submit(function(e){
      e.preventDefault();

      let form = $(this)[0];
      let formData = new FormData(form);
      formData.append('REQ_MODE', CASE_UPDATE);
      formData.append('stockOrderID', stockOrderID);

      if(!empty(stockOrderID)){
          updateData(formData);
      }
  });

  /** 배송방법 이벤트 */
  $('input:radio[name=DELIVERY_METHOD]').change(function(e){
      if($(this).val() == EC_DELIVERY_METHOD){
        $('.delivery').attr('disabled', false);
      }else{
        $('.delivery').val("");
        $('.delivery').attr('disabled', true);
      }
  });
  
  /** 입금 미입금 이벤트 */
  $('input:radio[name=DEPOSIT_YN]').change(function(e){
      if($(this).val() == "Y"){
        $('.deposit').attr('readonly', false);
        $('#MODAL_PAY_PRICE').focus() ;
      }else{
        $('.deposit').val("");
        $('.deposit').attr('readonly', true);
      }
  });

  /** 입금일 */
  $("#MODAL_DEPOSIT_DT" ).datepicker(
      getDatePickerSetting()
  );

  /** 입고일 */
  $("#MODAL_PAY_DT" ).datepicker(
      getDatePickerSetting()
  );
});

/** 합계금액, 최종결제금액, 각 항목별 합계를 처리 */
function setTotalPrice(){
  let priceData = 0;
  let cntData = 0;
  let vatData = 0;
  let unitPrice = 0;

  $('.unitPriceClass').each(function(){  // 단가합계
    let price = 0;
    if(!empty($(this).html())){
      price = Number($(this).html().replace(/,/g, ""));
    }
    priceData += price;  
  });
  
  $('.cntClass').each(function(){  // 수량합계
    let price = 0;
    if(!empty($(this).val())){
      price = Number($(this).val().replace(/,/g, ""));
    }
    cntData += price; 
  });
 
  $('.vatPriceClass').each(function(){ // 세액합계
    let price = 0;
    if(!empty($(this).html())){
      price = Number($(this).html().replace(/,/g, ""));
    }
    vatData += price;
  });

  $('.priceClass').each(function(){ // 공급가액 합계
    let price = 0;
    if(!empty($(this).html())){
      price = Number($(this).html().replace(/,/g, ""));
    }
    unitPrice += price;
  });

  const deliveryPrice = isNaN(Number($('#deliveryFeePrice').val())) ? 0 : Number($('#deliveryFeePrice').val()) ;
  $('#totalUnitPriceTable').html(priceData == 0 ? "" : numberWithCommas(priceData));
  $('#totalCntTable').html((unitPrice+vatData) == 0 ? "" : numberWithCommas(cntData));
  $('#totalPriceTable').html(unitPrice == 0 ? "" : numberWithCommas(unitPrice));
  $('#totalVatPriceTable').html(vatData == 0 ? "" :numberWithCommas(vatData));
  $('#totalItemPriceTable').html((unitPrice+vatData) == 0 ? "" :numberWithCommas(unitPrice+vatData));
  $('#hangleTotalPrice').html((unitPrice+vatData) == 0 ? "" :viewKorean(unitPrice+vatData)+" (₩"+numberWithCommas(unitPrice+vatData)+")" );
  $('#PAYMENT_PRICE_WRITE').html(numberWithCommas(deliveryPrice+(unitPrice+vatData)));

  if($('#IN_DELIVERY_WAY').val() == EC_DELIVERY_METHOD){ // 배송방법이 택배인 경우에는 상품이 선택되었을 때 배송비를 계산
    getDeliveryFeePrice();
  }
}

/** 배송비 체인지 이벤트 */
function onchangeDeliveryFeePrice(){
  const totalPrice = Number($('#totalItemPriceTable').html().replace(/,/g, ""));
  if(isNaN($('#deliveryFeePrice').val())){
    $('#deliveryFeePrice').val(0);
  }
  $('#PAYMENT_PRICE_WRITE').html(numberWithCommas(totalPrice+Number($('#deliveryFeePrice').val()))); 
}

function excelDownload() {
  location.href="inoutExcel.php?"+$("#searchForm").serialize()+"&REQ_MODE="+CASE_TOTAL_LIST;
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
  stockOrderList = dataList;

  let historyDataText = '';
  dataList.map((data, index)=>{
    historyDataText += "<tr>";
    // historyDataText += "<td class='text-center'><input type='checkbox' class='listcheck' name='multi' id='check_"+index+"' value='"+index+"'></td>";
    historyDataText += "<td class='text-center'>2020-11</td>";
    historyDataText += "<td class='text-center'>1지역</td>";
    historyDataText += "<td class='text-center'>서울시 강남구</td>";
    historyDataText += "<td class='text-left'>"+data.shipName+"</td>";
    historyDataText += "<td class='text-center'>거래</td>";
    historyDataText += "<td class='text-center'>"+data.shipChargeName+"</td>";
    historyDataText += "<td class='text-center'>"+phoneFormat(data.shipChargePhoneNum)+"</td>";
    historyDataText += "<td class='text-right'>전월미수누계</td>";
    historyDataText += "<td class='text-right'>주문횟수</td>";
    historyDataText += "<td class='text-right'>공급액</td>";
    historyDataText += "<td class='text-right'>세액</td>";
    historyDataText += "<td class='text-right'>"+numberWithCommas(data.totalPrice)+"</td>";
    historyDataText += "<td class='text-center'>확정여부</td>";
    historyDataText += "<td class='text-center'>"+data.depositYN+"</td>";
    historyDataText += "<td class='text-center'>"+(empty(data.depositDate)?"-":data.depositDate)+"</td>";
    historyDataText += "<td class='text-right'>"+(empty(data.depositPrice)?0:numberWithCommas(data.depositPrice))+"</td>";
    historyDataText += "<td class='text-right'>미입금액</td>";
    historyDataText += "<td class='text-center'>처리자</td>";
    historyDataText += "<td class='text-center'>2020-12-32 12:32:21</td>";
    historyDataText += "<td class='text-center'>";
    historyDataText += "<button type='button' class='btn btn-default' onclick='onClickInOutView(\""+data.stockOrderID+"\")'>조회</button>";
    historyDataText += "<button type='button' class='btn btn-default' onclick='onClickInOutWrite(\""+data.stockOrderID+"\")'>수정</button>";
    historyDataText += "<button type='button' class='btn btn-default' onclick='openPayDate(\""+data.stockOrderID+"\",\""+index+"\")'>입금</button>";
    if(data.depositYN = "Y"){ //임시로 넣어놓은 조건입니다
      historyDataText += "<button type='button' class='btn btn-danger' onclick='onClickStop(\""+data.stockOrderID+"\")'>중지</button>";
    }else{
      historyDataText += "<button type='button' class='btn btn-primary' onclick='onClickStop(\""+data.stockOrderID+"\")'>사용</button>";
    }
    historyDataText += "</td>";
    historyDataText += "</tr>"
  });
  paging(totalListCount, cPage, 'getList'); 
  $('#dataTable').html(historyDataText);
}


getList = (page, mode) => {
  var time = 1;
  var ajaxTimeInterver = setInterval(function(){
      time += 1;
      if(time > 2){
        $('#dataTable').hide();
        $('#indicator').show();
        $('#indicatorTable').show();

        clearInterval(ajaxTimeInterver);
      }
  },150);

  CURR_PAGE = (page == undefined ) ? CURR_PAGE : page;
  let form = $("#searchForm")[0];
  let formData = new FormData(form);
  formData.append('REQ_MODE', CASE_LIST);
  formData.append('PER_PAGE', $("#perPage").val());
  formData.append('CURR_PAGE', CURR_PAGE);
  formData.append('logisticsYN', (mode == undefined) ? $('#tabValue').val() : mode);

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
               alert(ERROR_MSG_1100);
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

function setData(REQ_MODE) {
  let form = $("#editForm")[0];
  let formData = new FormData(form);
  formData.append('REQ_MODE', REQ_MODE);
  formData.append('PAYMENT_PRICE_WRITE', $('#PAYMENT_PRICE_WRITE').html());
  
  callFormAjax(ERP_CONTROLLER_URL, 'POST', formData).then((result)=>{
    if(isJson(result)){
      const res = JSON.parse(result);
      if(res.status == OK){
        alert("작성 되었습니다.");

        try{
          if(window.opener.location.pathname.split("/").reverse()[0] == "inoutOrder.php"){
            window.opener.getList(1);
          }
          window.opener.onReloadListDetail();
        }catch{
          console.log("onReloadListDetail()함수가 존재하지 않습니다.");
        }
        window.close();
        const data = res.data;
        onClickInOutView(data.stockOrderID);
      }else{
        alert(ERROR_MSG_1100);
      }
    }else{
        alert(ERROR_MSG_1200);
    }
  });  
}

function onPrint(){
  $('.bill-footer').hide();
  window.print();
  // window.close();
  $('.bill-footer').show();
}

function getFileName(stockOrderID){
  const date = new Date();
  const year = date.getFullYear();
  const month = date.getMonth()+1;
  const day = date.getDate();
  const hour = date.getHours();

  return year+"/"+month+"/"+day+"/"+hour+"/"+stockOrderID;
}

function pdfDownload(stockOrderID){
  $('.bill-footer').hide();

  html2canvas($('body')[0]).then(function(canvas) {
    let imgData = canvas.toDataURL('image/png');

    let imgWidth = 210 ; // 이미지 가로 길이(mm) A4 기준
    let pageHeight = imgWidth * 1.414;  // 출력 페이지 세로 길이 계산 A4 기준
    let imgHeight = canvas.height * imgWidth / canvas.width;
    let heightLeft = imgHeight;

    let doc = new jsPDF("p", 'mm' ,'a4');
    let position = 0;

    // 첫 페이지 출력
    doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
    heightLeft -= pageHeight;

    // 한 페이지 이상일 경우 루프 돌면서 출력
    while (heightLeft >= 5) {
        position = heightLeft - imgHeight;
        doc.addPage();
        doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
        heightLeft -= pageHeight;
    }
    doc.save(getFileName(stockOrderID)+'.pdf'); 
  });
  $('.bill-footer').show();
}

function updateData(formData){
  callFormAjax(ERP_CONTROLLER_URL, 'POST', formData).then((result)=>{
    if(isJson(result)){
      const res = JSON.parse(result);
      if(res.status == OK){
        alert("처리가 완료되었습니다.");
        $('.modal').modal('hide');
        getList();
      }else{
        alert(ERROR_MSG_1100);
      }
    }else{
        alert(ERROR_MSG_1200);
    }
  });  
}

/** 원거래 주문서 상세 조회 */
function onOpenDeliveryPopup(orderID){
  const url = '../../../900_shop/940_orderMngt/941_orderList/orderListDetail.php?orderID='+orderID;
  openWindowPopup(url, 'orderListDetail', 1220, 860);
}

/* 입고버튼 지정 */
openDepositDate = (stockOrderID_) => {
  stockOrderID = stockOrderID_;
  setTimeout(function() { 
    $('#MODAL_DEPOSIT_DT').focus() ;
  }, 1000);
  
  
  $('#IN_MODAL').modal('toggle');
}

/* 입금버튼 */
openPayDate = (stockOrderID_, idx) => {
  stockOrderID = stockOrderID_;

  if(stockOrderList[idx].depositYN == "Y"){
    setTimeout(function() { 
      $('#MODAL_PAY_PRICE').focus() ;
    }, 1000);
  }
  $('#MODAL_PAY_PRICE').val(stockOrderList[idx].depositPrice);
  $('#MODAL_PAY_DT').val(stockOrderList[idx].depositDate);
  $('#MODAL_PAY_MEMO').val(stockOrderList[idx].depositMemo);

  $('input:radio[name=DEPOSIT_YN][value='+stockOrderList[idx].depositYN+']').prop("checked",true);
  $('input:radio[name=DEPOSIT_YN]:checked').trigger('change');
  $('#PAY_MODAL').modal('show');
}

/* 배송버튼 */
openDelivery = (stockOrderID_, idx) => {
  stockOrderID = stockOrderID_;
  if(stockOrderList[idx].deliveryMethodCode == EC_DELIVERY_METHOD){
    $('#ORDER_DELIVERY_NM').val(stockOrderList[idx].logisticsID);
    $('#MODAL_LOGISTICS_NUM').val(stockOrderList[idx].logisticsInvoiceNum);
  }
  $('input:radio[name=DELIVERY_METHOD][value='+stockOrderList[idx].deliveryMethodCode+']').prop('checked',true);
  $('input:radio[name=DELIVERY_METHOD]:checked').trigger('change');
  $('#LOGISTICS_MODAL').modal('toggle');
  
}


// 발주서작성 버튼 클릭
onClickInOutWrite = () => {
  const url = '/900_shop/970_calculate/971_calculate/inoutWrite.php';
  openWindowPopup(url, 'inoutWrite', 1200, 860);
}
// 발주서조회 버튼 클릭
onClickInOutView = (stockOrderID) => {
  const url = '/900_shop/970_calculate/971_calculate/inoutView.php?stockOrderID='+stockOrderID;
  openWindowPopup(url, 'transactionBill', 800, 860);   
}


// 중지처리 버튼 클릭
onClickStop = (stockOrderID) => {
  confirmSwalTwoButton('해당 가맹점을 중지 처리하시겠습니까?','확인','취소',true,function(){
    console.log("중지")
  },'중지처리는 가맹점의 로그인을 즉시 제한하는 기능입니다'); 
}


onClickOutBtn = () =>{
  location.href='inoutView.html';
}

/** 발송버튼 */
onClickSMSSend = (title, code) => {
  sendTitle = title;
  $('#sendTitle').html(title+" 발송");
  $('#sendMethodCode').val(code);
  $('#phoneNum-modal').modal();

  setTimeout(()=>{
    $('#SEND_PHONE_NUM').focus();
  },200);
  
}

/** 발송버튼 */
onClickSendSmsBtn = () => {
  let form = $("#phoneForm")[0];
  let formData = new FormData(form);
  formData.append('REQ_MODE', CASE_CREATE);

  callFormAjax(ERP_CONTROLLER_URL, 'POST', formData).then((result)=>{
      if(isJson(result)){
          const res = JSON.parse(result);
          if(res.status == OK){
            alert(sendTitle+"가 발송되었습니다.");
            window.location.reload();
            window.opener.getList();
          }else{
            alert(ERROR_MSG_1100);
          }
      }else{
        alert(ERROR_MSG_1200);
      }
      
  }); 
}

