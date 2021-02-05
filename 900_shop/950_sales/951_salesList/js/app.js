let CURR_PAGE = 1;

$(document).ready(function() {
  if($('#pageIndicator').is(":visible")){
    $('#pageIndicator').hide();
  }

  const tdLength = $('table th').length;
  setInitTableData(tdLength, 'dataTable');

  $("#issueDate, #depositDate, #START_DT, #END_DT").datepicker({
    dateFormat: 'yy-mm-dd',
  });
  
  /** 검색 */
  $("#searchForm").submit(function(e){
      e.preventDefault();
      getList(1);
  });

  $("#salesForm").submit(function(e){
      e.preventDefault();

      if(isValidation()){
        if(empty($('#salesID').val())){
          confirmSwalTwoButton('작성 하시겠습니까?','확인', '취소', false ,()=>{
              setData(CASE_CREATE);
          });
        }else{
          confirmSwalTwoButton('수정 하시겠습니까?','확인', '취소', false ,()=>{
              setData(CASE_UPDATE);
          });
        } 
      }   
  });

  $('input[name=DEPOSIT_YN]').change(function(){
    if($(this).val() == "Y"){
      $('.deposit').prop("disabled",false);
    }else{
      $('.deposit').prop("disabled",true);
    }
  });

  $('.date').change(function(e){
    var date_pattern = /^(19|20)\d{2}-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[0-1])$/; 
    if(!date_pattern .test($(this).val())){
      alert("yyyy-mm-dd 양식에 올바르지 않습니다.");
      $(this).val("");
      $(this).focus();
      return;
    }
  });

  if($('input[name=DEPOSIT_YN]').is(":visible")){
    $('input[name=DEPOSIT_YN]:checked').trigger("change");
  }
});

/**
 * autocomplete
 * @param {해당데이터} dataArray 
 * @param {autocomplete 적용시킬 input id} objID 
 * @param {data json id} id 
 * @param {data json value} value 
 */
function setAutoComplete(dataArray, objID, id, value){
  const dataInfo = JSON.parse(dataArray);
  $("#"+objID).autocomplete({
      minLength : 1,
      source: dataInfo.map((data, index) => {
          return {
              id : data[id],
              value : data[value]
          };
      }),
      focus: function(event, ui){ 
          return false;
      },
      change: function(event, ui){
        if(!empty(ui.item)){
          $('#'+objID+"Code").val(ui.item.id);
        }
        return false;
      },
      select: function(event, ui) {
          let strArea = ui.item.value;
          $("#"+objID).val(strArea);
          $('#'+objID+"Code").val(ui.item.id);
          return false;
      }
  }).on('focus', function(){ 
      $(this).autocomplete("search"); 
  });
}

function setUserAutocomplete(userInfoArr){
  try{
    setAutoComplete(userInfoArr, "userName", "userID", "userName");
  }catch{
    alert("직원정보를 가져오는데 실패하였습니다. 관라자에게 문의해주세요.");
  }
}


function setGarageAutocomplete(garageInfoArr){
  try{
    setAutoComplete(garageInfoArr, "garageName", "garageID", "garageName");
  }catch{
    alert("공업사정보를 가져오는데 실패하였습니다. 관라자에게 문의해주세요.");
  }
}

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

  $('#totalUnitPriceTable').html(priceData == 0 ? "" : numberWithCommas(priceData));
  $('#totalCntTable').html((unitPrice+vatData) == 0 ? "" : numberWithCommas(cntData));
  $('#totalPriceTable').html(unitPrice == 0 ? "" : numberWithCommas(unitPrice));
  $('#totalVatPriceTable').html(vatData == 0 ? "" :numberWithCommas(vatData));
  $('#totalItemPriceTable').html((unitPrice+vatData) == 0 ? "" :numberWithCommas(unitPrice+vatData));
  
  $('#PAYMENT_PRICE_WRITE').val((unitPrice+vatData));

}

function isValidation(){
  if(empty($('#garageNameCode').val())){
    alert("공업사를 선택해주세요.");
    $('#garageName').focus();
    return false;
  }else if(empty($('#userNameCode').val())){
    alert("직원을 선택해주세요.");
    $('#userName').focus();
    return false;
  }else{
    return true;
  }
}

function setData(REQ_MODE){
  let form = $("#salesForm")[0];
  let formData = new FormData(form);
  formData.append('REQ_MODE', REQ_MODE);
  
  $('.totalPriceClass').each(function(){
    formData.append('salePrice[]', ($(this).html()).replace(/,/g, ""));
  });

  callFormAjax(ERP_CONTROLLER_URL, 'POST', formData).then((result)=>{
    if(isJson(result)){
      const res = JSON.parse(result);
      if(res.status == OK){
        const BBILL_YN = $('input[name=BBILL_YN]:checked').val();
        if(REQ_MODE == CASE_CREATE){
          alert("등록 되었습니다.");
        }else{
          alert("수정 되었습니다.");
        }

        window.close();
        if(BBILL_YN == "Y"){
          onClickBillWrite(res.data.salesID, $('#garageNameCode').val());
        }else{
          window.opener.getList(1);
        }
      }else{
        alert((empty(res.message) ? ERROR_MSG_1100 : res.message));
      }
    }else{
        alert(ERROR_MSG_1200);
    }
  });  
}

excelDownload = () => {
  location.href="salesListExcel.php?"+$("#searchForm").serialize()+"&REQ_MODE="+CASE_TOTAL_LIST;
}

/** 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
  const tdLength = $('table th').length;
  const tableText = "<tr><td class='text-center' colspan='"+tdLength+"'>조회된 데이터가 존재하지 않습니다.</tr></tr>";
  $('.pagination').html('');
  $('#dataTable').html(tableText);
  $('#dataTable').show();
}

getList = (page) => {
  CURR_PAGE = (page == undefined ) ? CURR_PAGE : page;
  let form = $("#searchForm")[0];
  let formData = new FormData(form);
  formData.append('REQ_MODE', CASE_LIST);
  formData.append('PER_PAGE', $("#perPage").val());
  formData.append('CURR_PAGE', CURR_PAGE);

  $('#dataTable').hide();
  $('#indicatorTable').show();

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
     
  });  
}

setTable = (dataArray, totalListCount, currentPage) => {
  let tableText = '';
  
  dataArray.map((data)=>{
      let isBill = "N";

      tableText += "<tr>";
      tableText += "<td class='text-center'>"+data.issueCount+"</td>";
      tableText += "<td class='text-center'>"+data.issueDate+"</td>";
      tableText += "<td class='text-center'>"+data.salesID+"</td>";
      tableText += "<td class='text-center'>"+(empty(data.divisionName) ? "-" : data.divisionName)+"</td>";
      tableText += "<td class='text-left'>"+(empty(data.regionName) ? "-" : data.regionName)+"</td>";
      tableText += "<td class='text-left'>"+(empty(data.garageName) ? "-" : data.garageName)+"</td>";
      tableText += "<td class='text-center'>"+(empty(data.expName) ? "-" : data.expName)+"</td>";
      tableText += "<td class='text-center'>"+(empty(data.contractStatus) ? "-" : data.contractStatus)+"</td>";
      tableText += "<td class='text-center'>"+(empty(data.chargeName) ? "-" : data.chargeName)+"</td>";
      tableText += "<td class='text-center'>"+phoneFormat(data.officeTel)+"</td>";
      tableText += "<td class='text-center'>"+phoneFormat(data.chargePhoneNum)+"</td>";
      tableText += "<td class='text-left'>"+data.salesDetails+"</td>";
      tableText += "<td class='text-right'>"+numberWithCommas(data.totalPrice)+"</td>";
      tableText += "<td class='text-right'>"+numberWithCommas(data.totalVAT)+"</td>";
      tableText += "<td class='text-right'>"+numberWithCommas(data.totalSum)+"</td>";
      tableText += "<td class='text-right'>"+numberWithCommas(data.salePrice)+"</td>";
      tableText += "<td class='text-center'>"+(empty(data.employeeName) ? "-" : data.employeeName)+"</td>";
      tableText += "<td class='text-center'>"+data.depositDate+"</td>";
      tableText += "<td class='text-right'>"+numberWithCommas(data.depositPrice)+"</td>";
      tableText += "<td class='text-right'>"+numberWithCommas(data.unpaidPrice)+"</td>";
      tableText += "<td class='text-right'>"+data.dcRatio+"%</td>";
      tableText += "<td class='text-center'>"+data.depositYN+"</td>";
      tableText += "<td class='text-center'>"+data.paymentName+"</td>";
      tableText += "<td class='text-left'>"+data.memo+"</td>";
      tableText += "<td class='text-center'>"+data.processDatetime+"</td>";
      tableText += "<td class='text-center'>"+data.processUserName+"</td>";
      tableText += "<td class='text-center'>"+(empty(data.billPublishDate) ? "-" : data.billPublishDate)+"</td>";
      tableText += "<td class='text-center'>"+(empty(data.billPublishDate)? "N" :"Y")+"</td>";
      tableText += "<td class='text-center'>"+(empty(data.billType)? "-" : data.billType)+"</td>";
      tableText += "<td class='text-center'>"+(empty(data.billCancelDate) ? "-" : data.billCancelDate)+"</td>";
      tableText += "<td class='text-center'>"+(empty(data.billCancelUserID) ? "-" : data.billCancelUserID)+"</td>";
      if(empty(data.billCancelDate)){
        if(empty(data.billPublishDate)){
          tableText += "<td><button type='button' class='btn btn-default' onclick='onClickBillWrite(\""+data.salesID+"\",\""+data.garageID+"\")'>발행</button></td>";
        }else{
          isBill = "Y";
          tableText += "<td><button type='button' class='btn btn-default' onclick='onCancelBill(\""+data.salesID+"\",\""+data.billTypeCode+"\")'>취소</button></td>";
        }
      }else{
        tableText += "<td class='text-center'>-</td>";
      }
      
      tableText += "<td class='text-center'>"
      tableText += "<button type='button' class='btn btn-default' onclick='onClickWrite(\""+data.salesID+"\")'>수정</button>"
      tableText += "<button type='button' class='btn btn-danger' onclick='onDeleteSales(\""+data.salesID+"\",\""+isBill+"\")'>삭제</button>";
      tableText += "</td>";
      tableText += "</tr>";
  });
  paging(totalListCount, currentPage, 'getList'); 
  $('#dataTable').html(tableText);
  $('#dataTable').show();
}

function onCancelBill(salesID, billTypeCode){
  confirmSwalTwoButton(
    '발행 취소 하시겠습니까?','확인', '취소', true ,
    ()=>{
      if(billTypeCode == EC_BILL_TYPE_CASH){  // 현금영수증 취소
        delData(salesID, DEL_SALES_CASH_BILL, "CashBill");
      }else{                                  // 세금계산서 취소
        delData(salesID, DEL_SALES_TAX_BILL, "TaxBill");
      }
    }, ''
  );
}

function onDeleteSales(salesID, isBill){
  let subText = (isBill == "Y") ? "발행된 지출증빙건에 대해서는 홈텍스에서 별도 취소해주셔야 합니다." : "";

  confirmSwalTwoButton(
    '삭제하시겠습니까?','확인', '취소', true ,
    ()=>{
      delData(salesID)
    }, subText
  );
}

function delData(salesID, reqMode, urlType){
  const data = {
      REQ_MODE: CASE_DELETE,
      salesID :  salesID,
      reqMode : empty(reqMode) ? "" : reqMode,
      urlType : empty(urlType) ? "Info" : urlType

  };
  callAjax(ERP_CONTROLLER_URL,'POST', data).then((result)=>{
      if(isJson(result)){
        const res = JSON.parse(result);
        if(res.status == OK){
          if(empty(reqMode)){
            alert("삭제가 완료되었습니다.");
          }else{
            alert("취소가 완료되었습니다.");
          }

          if(!empty(window.opener)){
            window.opener.getList();
            window.close();
          }else{
            getList();
          }
          
        }else{
          alert((empty(res.message) ? ERROR_MSG_1100 : res.message));
        }
      }else{
          alert(ERROR_MSG_1200);
      }
  }); 
}

// 입금처리 입금버튼 클릭 클릭
onClickDepositEdit = () => {
  const url = '/900_shop/950_sales/951_salesList/depositModal.html';
  openWindowPopup(url, 'depositModal', 510, 570);
}

// 작성 버튼 클릭
onClickWrite = (salesID) => {
  let url = "";
  if(empty(salesID)){
     url = '/900_shop/950_sales/951_salesList/salesWrite.php';
  }else{
     url = '/900_shop/950_sales/951_salesList/salesWrite.php?salesID='+salesID;
  }
  openWindowPopup(url, 'inoutWrite', 1200, 860);
}

// 지출증빙 버튼 클릭
onClickBillWrite = (salesID, garageID) => {
  const url = '/900_shop/950_sales/951_salesList/billModal.php?salesID='+salesID+"&garageID="+garageID;
  openWindowPopup(url, 'billTypeModal', 510, 570);
}

