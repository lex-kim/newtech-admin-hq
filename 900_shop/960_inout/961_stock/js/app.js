let stockTableTrIdx = 0;
let productItems = [];

$(document).ready(function() {
    const tdLength = $('table th').length;
    setInitTableData(tdLength);

    $('#searchForm').submit(function(e){
        e.preventDefault();
        getList(1);
    });

    $( "#START_DT" ).datepicker(
        getDatePickerSetting('END_DT','minDate')
    );
    $( "#END_DT" ).datepicker(
        getDatePickerSetting('START_DT','maxDate')    
    );
});

/**
 * 각 행에 토탈금액 설정
 * @param{몇번째 행인지}idx
 */
setStockTotalPrice = (idx) => {
  const price = empty($('#unitPrice_'+idx).val()) ? 0 : $('#unitPrice_'+idx).val();
  const unit = empty($('#quantity_'+idx).val()) ? 0 : $('#quantity_'+idx).val();

  $('#stockPrice_'+idx).val(numberWithCommas(price*unit))
}

/**
 * 동적으로 추가된 행 삭제 버튼클릭시
 * @param{삭제할 행인지}delIdx
 */
onClickInputDelBtn = (delIdx) => {
  $('#stock_'+delIdx).remove();
} 

/**
 * 동적으로 추가된 행에서 상품명 select 이벤트
 * @param{몇번째 선택된 행인지}idx
 */
onSelectStockItem = (idx)=>{
  const selectedIdx = $("#itemID_"+idx+" option").index($("#itemID_"+idx+" option:selected"));

  try{
    const selectedItem = productItems[selectedIdx-1];


    $('#itemCategory_'+idx).val(selectedItem.categoryName);
    $('#itemPartner_'+idx).val(selectedItem.partnerName);
    $('#itemCode_'+idx).val(selectedItem.goodsCode);
    $('#itemSpec_'+idx).val(selectedItem.goodsSpec);
    $('#unitPrice_'+idx).val(selectedItem.buyPrice);
    $('#stockPrice_'+idx).val(0);
    $('#quantity_'+idx).focus();
  
    setStockTotalPrice(idx);
  }catch{
    alert("상품명이 잘못 선택되었습니다. 관리자에게 문의해주세요.");
  }

  
}

/**
 * 동적으로 추가될 행에서 상품명 select를 동적으로 생성할 Html 리턴
 * @param{몇번째 선택된 행인지}idx
 */
getItemSelectHtml = (idx) => {
  let selectHtml = "<select name='itemID[]' id='itemID_"+idx+"' onchange='onSelectStockItem("+idx+")' class='form-control selectpicker'  data-live-search='true' required>";
  selectHtml += "<option value=''>=선택=</option>";
  productItems.map((item)=>{
    if(item.buyerTypeCode != EC_BUYER_TYPE_CONSIGN){
      selectHtml += "<option value='"+item.itemID+"'>"+item.goodsName+"</option>"
    }
  });
  selectHtml += "</select>";

  return selectHtml;
}

/**
 * 동적으로 추가될 테이블 htl
 * @param{몇번째 선택된 행인지}idx
 */
getStockInputTableHtml = () => {

  let html = "";
  html += '<tr id="stock_'+stockTableTrIdx+'">';
  html += '<td class="text-left">';
  html += '<input type="text" class="form-control" id="itemCategory_'+stockTableTrIdx+'" readonly/>';
  html += '</td>';
  html += '<td class="text-left">';
  html += '<input type="text" class="form-control" id="itemPartner_'+stockTableTrIdx+'"readonly/>';
  html += '</td>';
  html += '<td class="text-left">';
  html += '<input type="text" class="form-control" id="itemCode_'+stockTableTrIdx+'" readonly/>';
  html += '</td>';
  html += '<td class="text-left">';
  html += getItemSelectHtml(stockTableTrIdx);
  html += '</td>';
  html += '<td class="text-left">';
  html += '<input type="text" class="form-control text-right" id="itemSpec_'+stockTableTrIdx+'" readonly/>';
  html += '</td>';
  html += '<td class="text-left">';
  html += "<input type='text' class='form-control text-right' onchange='setStockTotalPrice(\""+stockTableTrIdx+"\")' id='unitPrice_"+stockTableTrIdx+"'  onkeyup='isNumber(event)' name='unitPrice[]' required/>";
  html += '</td>';
  html += '<td class="text-left">';
  html += "<input type='text' class='form-control text-right' onchange='setStockTotalPrice(\""+stockTableTrIdx+"\")' id='quantity_"+stockTableTrIdx+"'  onkeyup='isNumber(event)' name='quantity[]' required/>";
  html += '</td>';
  html += '<td class="text-left">';
  html += '<input type="text" class="form-control text-right" id="stockPrice_'+stockTableTrIdx+'" readonly/>';
  html += '</td>';
  html += '<td class="text-center">';
  if(stockTableTrIdx > 0){
    html += "<button type='button' class='btn btn-danger' onclick='onClickInputDelBtn(\""+stockTableTrIdx+"\")'>삭제</button>";
  }
  html += '</td>';
  html += '</tr>'

  stockTableTrIdx += 1;
  return html;
}  

/** 행 추가 클릭 시 */
onclickAddStockBtn = () => {
  let html = getStockInputTableHtml();

  $('#stockTable').append(html);
  $('.selectpicker').selectpicker("render");
}

/** 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
  const tdLength = $('table th').length;
  const tableText = "<tr><td class='text-center' colspan='"+tdLength+"'>조회된 데이터가 존재하지 않습니다.</tr></tr>";
  $('.pagination').html('');
  $('#productList').html(tableText);
}


setTable = (dataList, totalListCount, cPage) => {
  let html = "";
  dataList.map((data)=>{
      html += "<tr>";
      html += "<td class='text-center'>"+data.issueCount+"</td>";
      html += "<td class='text-center'>"+data.issueDate+"</td>";
      html += "<td class='text-center'>"+data.categoryName+"</td>";
      html += "<td class='text-left'>"+data.partnerName+"</td>";
      html += "<td class='text-center'>"+data.goodsCode+"</td>";
      html += "<td class='text-left'>"+data.goodsName+"</td>";
      html += "<td class='text-right'>"+data.goodsSpec+"</td>";
      html += "<td class='text-right'>"+data.unitPerBox+"</td>";
      html += "<td class='text-right'>"+numberWithCommas(Number(data.unitPrice))+"</td>";
      html += "<td class='text-right'>"+numberWithCommas(Number(data.quantity))+"</td>";
      html += "<td class='text-right'>"+numberWithCommas(Number(data.sumPrice))+"</td>";
      html += "<td class='text-left'>"+data.memo+"</td>";
      html += "<td class='text-center'>"+data.writeDatetime+"</td>";
      html += "<td class='text-center'>"+data.writeUser+"</td>";
      html += "<td class='text-center'>"+(empty(data.cancelDatetime) ? "-" : data.cancelDatetime)+"</td>";
      html += "<td class='text-center'>"+(empty(data.cancelUser) ? "-" : data.cancelUser)+"</td>";
      html += "<td class='text-center'>";
      if(empty(data.cancelYN)){
        html += "<button type='button' class='btn btn-default' onclick='onclickCancel("+JSON.stringify(data)+")'>취소</button>";
      }
      html += "</td>";
      html += "<td class='text-center'>";
      if(!empty(data.cancelYN)){
        html += "<button type='button' class='btn btn-danger' onclick='onclickDelBtn("+JSON.stringify(data)+")'>삭제</button>";
        
      }
      html += "</td>";
      html += "</tr>"
  });
  paging(totalListCount, cPage, 'getList'); 
  $('#dataTable').html(html);
}

excelDownload = () => {
  location.href="stockExcel.php?"+$("#searchForm").serialize()+"&REQ_MODE="+CASE_TOTAL_LIST;
}

getList = (page) => {
  CURR_PAGE = (page == undefined ) ? CURR_PAGE : page;
  let form = $("#searchForm")[0];
  let formData = new FormData(form);
  formData.append('REQ_MODE', CASE_LIST);
  formData.append('PER_PAGE', $("#perPage").val());
  formData.append('CURR_PAGE', CURR_PAGE);

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
     
  });  
}

setData = (REQ_MODE) => {
    let form = $("#editForm")[0];
    let formData = new FormData(form);
    formData.append('REQ_MODE', REQ_MODE);

    callFormAjax(ERP_CONTROLLER_URL, 'POST', formData).then((result)=>{
      if(isJson(result)){
        const res = JSON.parse(result);
        if(res.status == OK){
          alert("입고등록이 완료되었습니다.");

          window.close();
          window.opener.getList(1);
        }else{
          alert(ERROR_MSG_1100);
        }
      }else{
          alert(ERROR_MSG_1200);
      }
    });  
}

cancelData = (stockID, itemID) => {
    const data = {
        REQ_MODE: CASE_UPDATE,
        stockID :  stockID,
        itemID : itemID

    };
    callAjax(ERP_CONTROLLER_URL,'POST', data).then((result)=>{
        if(isJson(result)){
          const res = JSON.parse(result);
          if(res.status == OK){
            alert("입고취소가 완료되었습니다.");
            getList();
          }else{
            alert(ERROR_MSG_1100);
          }
        }else{
            alert(ERROR_MSG_1200);
        }
    });   
}

delData = (stockID, itemID) => {
  const data = {
      REQ_MODE: CASE_DELETE,
      stockID :  stockID,
      itemID : itemID

  };
  callAjax(ERP_CONTROLLER_URL,'POST', data).then((result)=>{
      if(isJson(result)){
        const res = JSON.parse(result);
        if(res.status == OK){
           alert("삭제가 완료되었습니다.");
           getList();
        }else{
          alert(ERROR_MSG_1100);
        }
      }else{
          alert(ERROR_MSG_1200);
      }
  }); 
}

/* 등록 모달창*/ 
showAddModal = () => {
  const url = '/900_shop/960_inout/961_stock/stockModal.php';
  openWindowPopup(url, 'stockModal', 1000, 400);
}


onclickCancel=(item) =>{
  confirmSwalTwoButton(
    '취소하시겠습니까?','확인', '취소', false ,
    ()=>{cancelData(item.stockID, item.itemID)}, '확인을 누르시면 해당 건을 취소합니다.'
  );
}

onclickDelBtn=(item) =>{
  confirmSwalTwoButton(
    '삭제하시겠습니까?','확인', '취소', true ,
    ()=>{delData(item.stockID, item.itemID)}, '확인을 누르시면 해당 건을 삭제합니다.'
  );
}


function onDownloadStockInFormat(){
  location.href="stockCSV.php";
}
