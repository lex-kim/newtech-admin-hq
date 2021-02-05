let listArray = [];
let shoppingList = [];
let currPage = 1;
let shoppingListIdx = 0;
let isChangeGarage = false;   // 공업사가 바뀌었는지를 위한 변수.True일 경우 getList 후 장바구니 배열을 확인후 재 배치
let searchCategory = "";      // getList시 선택한 카테고리
let searchText = "";          // getList시 상품명
let openerPageName = "";
const SOLD_OUT_TEXT_COLOR = "#ff0000";

$(document).ready(function() {
    $('#pageIndicator').hide();
    
    $('#searchText').keypress(function(e){
      if(e.which == 13){
        getList(1, null, $('#searchText').val());
      }
    });
    
    /** 검색 */
    $("#searchForm").submit(function(e){
        e.preventDefault();
        getList(1);
    });

    $('#garageID').change(function(e){
      if(!empty($(this).val())){
        isChangeGarage = true;
        getList(currPage, searchCategory, searchText);
      }
    });


    // nav 마우스 hover 시 아래 메뉴 오픈
    $('.dropdown').hover(function(){
      $(this).children("ul").css('display','block')
    }, function(){
      $(this).children("ul").css('display','none')
    });

});

/**
 * 주문 리스트 이미지(경로가 없을시 기본으로..)
 * @param{imgPath : 이미지 경로}
 */
getImgPath = (imgPath) => {
  if(!imgPath){
    return "img/product.jpg";
  }else{
    return imgPath;
  }
}

/**
 * option여부에 따라 option이 있으면 select로 처리 없으면 빈값으로
 * @param {optionList : 옵션 리스트(옵션이 있는지 없는지)}
 * @param {idx : 몇번째 상품 리스트 인지}
 */
getOption = (optionList, idx) => {
  const isExistOptionUse = optionList.find(function(item){  // 옵션 사용여부는 Y이나 정작 사용중인 옵션이 있는지 체크
    return item.optionUseYN == "Y";
  });

  if(empty(isExistOptionUse)){
    return "";
  }else{
    let html = '<select class="form-control" name="NCC_USE_YN" id="NCC_OPTIONS_'+idx+'">';
    html += '<option data-price="" value="">선택</option>';
    if(!empty(optionList)){
      optionList.map((item)=>{
        if(item.optionUseYN == "Y"){
          html += '<option data-price="'+item.optionPrice+'" value="'+item.optionID +'">'+item.optionName+" (+"+numberWithCommas(item.optionPrice)+")"+'</option>';
        }
      });
    }
    html += '</select>';
  
    return html;
  }
  
}

/**
 * 장바구니에 옵션 텍스트 보여줄.
 * @param{idx : 주문리스트에서 선택된 옵션}
 */
getOptionText = (idx) =>{
    let optionText = "";
    if(!empty($('#NCC_OPTIONS_'+idx).val())){
       optionText = "옵션 : "+$('#NCC_OPTIONS_'+idx+" option:checked").text();
    }

    return optionText;
}

/** 장바구니에 보여질 총 합계*/
setTotalPrice = () => {
 
  let totalPrice = 0;
  $('.totalPrice').each(function(){
    const price = Number($(this).html().replace(/,/g, ''));
    totalPrice += price;
  });

  $('#totalPrice').html(numberWithCommas(totalPrice));
}

/**
 * 장바구니에 금액과 수량 세팅
 * @param{item : 선택된 상품}
 * @param{cnt : 수량}
 */
setShoppingCntPrice = (item, cnt) => {
  let price = 0;
  $('#shoopintCnt_'+item.idx).val(cnt);
  $('#point_'+item.idx).html(numberWithCommas(item.savePoint*cnt));
  
  if($('#price_'+item.idx).is(":visible")){
    price = $('#price_'+item.idx).text().replace(/,/g,"");
  }else{
    price = $('#inputPrice_'+item.idx).val();
  }

  if(getSoldoutItem(item, cnt)){
    $('#itemText_'+item.idx).css("color", SOLD_OUT_TEXT_COLOR);
    $('#isSoldout_'+item.idx).val("Y");
    $('#soldOutImage_'+item.idx).show();
  }else{
    $('#isSoldout_'+item.idx).val("N");
    $('#itemText_'+item.idx).css("color","black");
    $('#soldOutImage_'+item.idx).hide();
  }

  $('#salePrice_'+item.idx).html(numberWithCommas(price*cnt));
  shoppingList.map((sItem)=>{      // 변경된 수량의 장바구니 수량을 업데이트
      if(sItem.idx == item.idx){
        sItem.cnt = cnt;
      }
  });
  
  setTotalPrice();
}

/**
 * 장바구니에서 삭제버튼 클릭시
 * @param{idx : 장바구니에서 삭제버튼을 누른 인덱스}
 */
onDeleteShoppingList = (idx) => {
  let remainArray = [];
  shoppingList.map((sItem, index)=>{
    if(sItem.idx != idx){
      remainArray.push(sItem);
    }
  });
  shoppingList = remainArray;
  setShoppingTable();
}

/**
 * 장바구니에서 수량 증가를 눌렀을 때
 */
increaseCnt = (item) => {
  const cnt = Number($('#shoopintCnt_'+item.idx).val())+1;
  setShoppingCntPrice(item,cnt);
}

/**
 * 장바구니에서 수량 감소를 눌렀을 때
 */
decreaseCnt = (item) => {
  const cnt = Number($('#shoopintCnt_'+item.idx).val())-1;
  if(cnt > 0){
    setShoppingCntPrice(item,cnt);
  }
 
}

/**
 * 장바구니에서 수량 변경 이벤트
 */
onchangeListCnt = (item, e) =>{
  let cnt = Number($('#shoopintCnt_'+item.idx).val());
  if(cnt < 1){
    cnt = 1;
  }
  setShoppingCntPrice(item,cnt);
 
}

onchangeItemPoint = (item) => {
  let point = Number( $('#earnPoint_'+item.idx).val());

  shoppingList.map((sItem)=>{     
      if(sItem.idx == item.idx){
        sItem.earnPoint = point;
      }
  });
}

onchangeItemPrice = (item) => {
  let cnt = Number($('#shoopintCnt_'+item.idx).val());
  let price = Number( $('#inputPrice_'+item.idx).val());

  if(!empty(item.optionPrice)){
    if(price < item.optionPrice){
        alert("옵션 가격보다 더 낮게 책정하실 수 없습니다.");
        $('#inputPrice_'+item.idx).val(item.optionPrice);
        $('#hidden_price_'+item.idx).val(0);
        return false;
    }
  }
  $('#salePrice_'+item.idx).html(numberWithCommas(price*cnt));
  $('#hidden_price_'+item.idx).val(price-item.optionPrice);
  shoppingList.map((sItem)=>{      // 변경된 수량의 장바구니 수량을 업데이트
      if(sItem.idx == item.idx){
        sItem.cnt = cnt;
        sItem.salePrice = price;
      }
  });
  setTotalPrice();
}

function getSoldoutItem(item, cnt){
  if(item.soldOutYN == "Y"){
    return true;
  }else{
    if(item.stockRemains >= cnt){
      return false;
    }else{
      return true;
    }
  }
}

/**
 * 장바구니 동적 생성 테이블
 */
setShoppingTable = () => {
  let htmlText = "";
  if(shoppingList.length == 0){
    htmlText = BASE_SHOPPING_LIST_TEXT;
  }else{
    const reverseList = shoppingList;
    reverseList.slice(0).reverse().map((item, index)=>{
      let price = (empty(item.optionPrice) ? item.salePrice : Number(item.salePrice) + Number(item.optionPrice));
      let cnt = (item.cnt == undefined) ? 1 : item.cnt;
      let isSoldout = getSoldoutItem(item, cnt);
      let soldoutCss = isSoldout ? "style='color:"+SOLD_OUT_TEXT_COLOR+"'" : "";
      let isShowSoldoutImages = isSoldout ? "" : "style='display:none'" ;

      htmlText +=  '<li class="order-item">';
      htmlText +=  "<input type='hidden' class='itemText' id='isSoldout_"+item.idx+"'  value='"+(isSoldout ? "Y" : "N")+"'/>";
      htmlText +=  "<input type='hidden' name='itemID[]'  value='"+item.itemID+"'/>";
      htmlText +=  "<input type='hidden' name='optionID[]' value='"+item.optionId+"'/>";
      htmlText +=  "<input type='hidden' name='optionPrice[]' value='"+item.optionPrice+"'/>";
      htmlText +=  '<div class="choice-title">';
      htmlText +=  '<div class="choice-title-goods" id="itemText_'+item.idx+'" '+soldoutCss+'>'+item.goodsName;
      htmlText +=  '<span '+isShowSoldoutImages+' id="soldOutImage_'+item.idx+'"  style="margin-left:5px"><img src="../../../images/soldout.png"/></span>';
      htmlText +=  '</div>';
      htmlText +=  '<div class="choice-title-option">'+item.optionText+'</div>';
      htmlText +=  '</div>';
      htmlText +=  '<div class="choice-num">';
      htmlText +=  "<input type='text' class='input-num' name='quantity[]' onchange='onchangeListCnt("+JSON.stringify(item)+",event)' id='shoopintCnt_"+item.idx +"' value='"+cnt+"'>";
      htmlText +=  '<div class="edit-num">';
      htmlText +=  "<button type='button' onclick='increaseCnt("+JSON.stringify(item)+")' class='btn-up'>증가</button>";
      htmlText +=  "<button type='button' onclick='decreaseCnt("+JSON.stringify(item)+")' class='btn-down'>감소</button>";
      htmlText +=  '</div>';
      htmlText +=  '</div>';
      htmlText +=  '<div class="choice-price">';
      if(item.salePriceInquiryYN == "Y" || (empty(item.salePriceInquiryYN) && item.salePrice == 0)){ // 문의변수가 Y 이거나 혹은 문의 변수는 없지만 가격이 0원이면 문의로 책정 
        htmlText += '<span class="col-md-3 choice-goods-point">';
        htmlText += "<input type='text' class='form-control text-right' name='earnPoint[]' id='earnPoint_"+item.idx+"' value='"+item.earnPoint+"' onchange='onchangeItemPoint("+JSON.stringify(item)+")'  onkeyup='isNumber(event)' placeholder='적립금' required/>"; //상품 문의 시 적립금 입력 인풋
        htmlText += '</span>';
        htmlText += '<span class="col-md-2 choice-cost-price">'
        htmlText += "<input type='hidden' name='unitPrice[]' id='hidden_price_"+item.idx +"' value='"+(price- item.optionPrice)+"' />";
        htmlText += "<input type='text' class='form-control text-right' value='"+price+"' id='inputPrice_"+item.idx+"' onchange='onchangeItemPrice("+JSON.stringify(item)+")' placeholder='단가' required/>";
        htmlText += '</span>'
      }else{     //문의가 아닌경우
        htmlText +=  '<span class="col-md-3 choice-goods-point"></span>';
        htmlText +=  '<input type="hidden"  name="earnPoint[]" value="'+item.earnPoint+'"/>';
        htmlText +=  '<input type="hidden" id="hidden_price_'+item.idx +'"  name="unitPrice[]" value="'+item.salePrice+'"/>';
        htmlText +=  '<span class="col-md-2 choice-cost-price" id="price_'+item.idx +'">'+numberWithCommas(price)+'</span>';
      }
      htmlText +=  '<span class="choice-selling-price totalPrice" id="salePrice_'+item.idx +'">'+numberWithCommas(price*cnt)+'</span>';
      htmlText +=  '</div>';
      htmlText +=  '<div class="choice-delete">';
      htmlText +=  "<button type='button' class='btn btn-danger' onclick='onDeleteShoppingList("+item.idx+")'>삭제</button>";
      htmlText +=  '</div>';
      htmlText +=  '</li>';
    });
  }
  $('#orderTable').html(htmlText);
  setTotalPrice();
}


/**
 * 동적으로 삽입된 견적 품목 리스트와 추가된 상품을 비교하여 있으면 갯수를 올려주고 아니면 데이터 삽입 후 테이블 동적 생성
 * @param {삽입될 데이터} data 
 * @param {선택된 데이터} selectedItem 
 */
function setShoppingData(data, selectedItem){
  if(shoppingList.length  == 0){
    shoppingList.push(data);
    setShoppingTable();
  }else{
    const existData = shoppingList.filter((item)=>{
      return item.itemID == selectedItem.itemID && item.optionId == data.optionId;
    });

    if(existData.length == 0){
      shoppingList.push(data);
      setShoppingTable();
    }else{
      const item = existData[0];
      increaseCnt(item);
    }
  }
}

/**
 * 주문상품리스트에서 상품 담기를 눌렀을 때.
 * 저장 후 견적 품목 리스트에 동적으로 삽입
 * @param{선택된 상품 index} idx
 */
onAddItem = (idx) => {
    let isAddValidation = false;
    if($('#NCC_OPTIONS_'+idx).is(":visible")){
      if(!empty($('#NCC_OPTIONS_'+idx).val())){
        isAddValidation = true;
      }else{
        alert("옵션을 선택해주세요.");
        $('#NCC_OPTIONS_'+idx).focus();
        isAddValidation = false;
      }
    }else{
      isAddValidation = true;
    }

    if(isAddValidation){
      let data = $.extend({}, listArray[idx], {
        idx : shoppingListIdx++,
        itemID : listArray[idx].itemID,
        optionId : empty($('#NCC_OPTIONS_'+idx).val())? -1 : $('#NCC_OPTIONS_'+idx).val()  ,   // 장바구니에 보여질 옵션 값
        optionText : getOptionText(idx),         //
        optionPrice:  empty($('#NCC_OPTIONS_'+idx+" option:selected").attr('data-price')) ? "" : $('#NCC_OPTIONS_'+idx+" option:selected").attr('data-price')
      });
  
      setShoppingData(data, listArray[idx]);
    }
    
}

/** 공업사 변경시 장바구니에 상품이 있는경우 해당 공업사대비 금액에 맞게 변경 */
function onChangeShoppingList(){
  let successCnt = 0; // 성공여부 카운트
  shoppingList.map(function(item, index){
    let data = {
      REQ_MODE : CASE_READ,
      itemID : item.itemID,
      garageID : $('#garageID').selectpicker('val'),
    };
    callAsyncAjax("../../910_mngt/913_product/controller/AjaxController.php", 'POST', data).then((result)=>{
        if(isJson(result)){
          const res = JSON.parse(result);
          if(res.status == OK){
            successCnt++;
            const data = res.data.data;
            item.salePrice = data.salePrice; 
          }
        }

        if(index == shoppingList.length-1){
          if(successCnt == shoppingList.length){
            setShoppingTable();
          }else{
            alert(ERROR_MSG_1100);
          }
        }
    }); 
    
  });
  
}

/**
 * 견적발행(요청 ->  발행)시 기존 저장된 데이터들을 알맞은 형태로 저장.
 * 저장 후 견적 품목 리스트에 동적으로 삽입
 * @param{저장된 견적 품목}eItem
 */
setShoppingList = (eItem) => {console.log(eItem);
  let data = {
    idx : shoppingListIdx++,
    itemID : eItem.itemID,
    optionId : empty(eItem.optionName) ? -1 : eItem.optionID  ,   // 장바구니에 보여질 옵션 값
    optionText :empty(eItem.optionName) ? "" : "옵션 : "+eItem.optionName+" (+"+numberWithCommas(eItem.optionPrice)+")",         //
    optionPrice:  empty(eItem.optionPrice) ? "" : eItem.optionPrice,
    goodsName : eItem.itemName,
    cnt : eItem.quantity,
    salePrice : eItem.unitPrice,
    earnPoint : eItem.unitPoint,
    stockRemains : eItem.stockRemains
  };
  setShoppingData(data, eItem);
}

/**
 * 해당 상품리스트에 품절상품이 존재하는지..
 */
function isHaveSoldOutItem(){
  let isHaveSolodOut = false;

  $('.itemText').each(function(item){
    if($(this).val() == "Y"){
      isHaveSolodOut = true;
    }
  });
  return isHaveSolodOut;
}

function setRoundsNumber(number){
    if(number % 10 < 5){
      return number - (number % 10);
    }else{
      return number - (number % 10)+10;
    }
}
/**
 * 주문상품 리스트 테이블 동적생성
 * @param {dataList : 동적리스트}
 * @param {totalListCount : 총 몇페이지}
 * @param {cPage : 현재페이지}
 */
setTable = (_dataList, totalListCount, cPage) => {
  let html = "";
  let dataList = _dataList;
  dataList.map((item, idx)=>{
      item.salePrice = setRoundsNumber(item.salePrice);           // 반올림된 값을 저장하기 위해서..
      html += "<tr>";
      html += '<td class="text-left goods-img">';
      html += '<img style="width:70px; height:70px" data-src="'+getImgPath(item.imgURI)+'" alt="" class="gocoder">';
      html += '</td>';
      html += '<td class="text-left goods-info">';
      html += '<div class="col-md-12 goods-title">';
      html += "<span><a onclick='showPopup(\""+item.itemID+"\")'>"+item.goodsName+"</a></span>";
    
      if(!empty(item.goodsSpec) || (!empty(item.unitPerBox) && item.unitPerBox > 0)){
        html += "<div>( ";
        if(!empty(item.goodsSpec)){
          html += "규격/용량 : "+item.goodsSpec;
          if(!empty(item.unitPerBox) && item.unitPerBox > 0){
            html += " , ";
          }
        }
        if(!empty(item.unitPerBox) && item.unitPerBox > 0){
          html += "포장단위 : "+item.unitPerBox;
        }
        html += ' )</div>';
      }
      html += '</div>';

      html += '<div class="col-md-7 goods-option">';
      if(!empty(item.catalogURI)){
        html += "<button type='button' class='btn btn-default' onclick='download(\""+item.catalogURI+"\")'>카다로그</button>";
      }
      if(item.goodsOptionUseYN == "Y"){
        html += getOption(item.goodsOptions, idx);
      }
      html += '</div>';
      html += '</td>';
      html += '<td class="text-right goods-price">';
      html += '<div class="price-info">';
      html += '<span class="selling-price">'+numberWithCommas(setRoundsNumber(item.salePrice))+'원</span>';
      if(item.soldOutYN == "Y"){
        html += '<div>( 최소주문 : '+item.minBuyCnt+', <span style="color:red">일시품절</span> )</div>';
      }else{
        if(item.buyerTypeCode == BUYER_TYPE_CODE_CON){                // 위탁상품 인 경우엔 수량은 무한
          item.stockRemains = INFINIT_CNT;
          html += '<div>(최소주문 : '+item.minBuyCnt+', 위탁상품)</div>';
        }else{
          html += '<div>(최소주문 : '+item.minBuyCnt+', 재고 : '+item.stockRemains	+')</div>';
        }
        
      }
      html += '</div>';
      html += '<div class="price-button">';
      html += '<button type="button" class="btn btn-success" onclick="onAddItem('+idx+')">담기</button>';
      html += '</div>';
      html += '</td>';
      html += '</tr>';
  });

  listArray = dataList;

  paging(totalListCount, cPage, 'getList'); 
  $('#dataTable').html(html);
  $('#dataTable').show();
  
  $('img.gocoder').lazyload({        // 이미지를 동적으로 호출 하도록
      placeholder : '데이터로딩중...',    // 로딩 이미지
      threshold: 0,                 // 접근 ~px 이전에 로딩을 시도한다.
      load : function(){              // 로딩시에 이벤트
          $(this).attr('alt',$(this).attr("data-src"));
      }
  });

  if(isChangeGarage && shoppingList.length > 0){
    onChangeShoppingList();
  }
  isChangeGarage = false;
}

getList = (page, searchCtgy, searchText_) => {
  let time = 1;
  currPage = (page == undefined ) ? currPage : page;
  let form = $("#searchForm")[0];
  let formData = new FormData(form);
  formData.append('REQ_MODE', CASE_LIST);
  formData.append('PER_PAGE', $("#perPage").val());
  formData.append('CURR_PAGE', currPage);
  searchCategory = searchCtgy;
  searchText = searchText_;

  let ajaxTimeInterver = setInterval(function(){
      time += 1;
      if(time > 2){
        $('#dataTable').hide();
        $('#indicator').show();
        $('#indicatorTable').show();

        clearInterval(ajaxTimeInterver);
      }
  },150);

  if(!empty(searchCtgy)){
    formData.append('categoryID', searchCtgy);
  }
  if(!empty(searchText_)){
    formData.append('SEARCH_PRODUCT_NM', searchText);
  }
  if(!empty($('#garageID').selectpicker('val'))){
    formData.append('garageID', $('#garageID').selectpicker('val'));
  }
 

  callFormAjax(ERP_CONTROLLER_URL, 'POST', formData).then((result)=>{
      if(isJson(result)){
        const res = JSON.parse(result);
        if(res.status == OK){
          if(res.data == HAVE_NO_DATA || res.data.length == 0){
            const tableText = "<tr><td class='text-center'>조회된 데이터가 존재하지 않습니다.</tr></tr>";
            $('.pagination').html('');
            $('#dataTable').html(tableText);
            $('#dataTable').show();
          }else{
            setTable(res.data, res.total_item_cnt, currPage);
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

setData = () => {
    callAjax(ERP_CONTROLLER_URL, 'POST', shoppingSerilize+"&onlyOrderYN=N").then((result)=>{
        if(isJson(result)){
          const res = JSON.parse(result);
          if(res.status == OK){
            if(!empty($('#quotationID').val())){
              alert("발행이 완료되었습니다.");
            }else{
              alert("등록이 완료되었습니다.");
            }

            window.close();
            if(window.opener.location.pathname.split("/").reverse()[0] == "estimateList.php"){
              if($('#quotationID').val() != ""){
                window.opener.getList();
              }else{
                window.opener.getList(1);
              }
            }
          
          }else{
            const data = res.data;
            if(data.errorCode == ERROR_EC_DELIVERY_VIOLATION){
              alert(data.errorMsg);
            }else{
              alert(ERROR_MSG_1100);
            } 
          }
        }else{
            alert(ERROR_MSG_1200);
        }
    });
}

// 위탁발주 발주서작성 버튼 클릭
onClickConsigment = () => {
  window.open('/900_shop/940_orderMngt/941_orderList/consignment.html', 
                'consignment', 
                'width=1200,height=860,scrollbars=yes'); 
}


function onClickWriteEstimate(){
  confirmSwalTwoButton('견적서 작성하시겠습니까?','확인','취소',false,function(){
    $('#estimate-info').modal();
  })  
  
}


// 단가 입력창이 있는 견적요청 시 저장 버튼
function onClickSaveEstimate(){
  confirmSwalOneButton('견적이 요청되었습니다. 확인 후 담당자가 연락드립니다.','확인',function(){showEstimateView();window.close();});
}

//견적서 조회 오픈
function showEstimateView(){
  const url = '/900_shop/940_orderMngt/943_estimateList/estimateListDetail.html';
  openWindowPopup(url, 'estimateListDetail', 1024, 800);
}

// 견적서 작성 내 주문 버튼
function onClickOrderEstimate(){
  callAjax(ERP_CONTROLLER_URL, 'POST', shoppingSerilize+"&REQ_MODE="+CASE_CREATE+"&onlyOrderYN=Y").then((result)=>{
      if(isJson(result)){
        const res = JSON.parse(result);
        const data = res.data;
        if(res.status == OK){
            $('#parentUrl').val(document.referrer.split("/").reverse()[0]);
            
            const quotationID = data.quotationID;
            const url = '/900_shop/940_orderMngt/942_order/orderSheetPopup.php?quotationID='+quotationID+"&garageID="+$('#garageID').selectpicker('val');
            openWindowPopup(url, 'goodsdetail', 1420, 860);
            
        }else{
          if(data.errorCode == ERROR_EC_DELIVERY_VIOLATION){
            alert(data.errorMsg);
          }else{
            alert(ERROR_MSG_1100);
          } 
        }
      }else{
          alert(ERROR_MSG_1200);
      }
  });
}

// 주문서의 주문하기 클릭
function onClickOrder(){
  confirmSwalTwoButton("주문하시겠습니까?",'확인', '취소', false ,function(){showOrderBill()});
}

// 주문서 오픈
function showOrderBill(){
  location.replace("/900_shop/940_orderMngt/941_orderList/orderBill.html");
}

// 상품디테일 화면 오픈
function showPopup(itemId){
  const url = '/900_shop/940_orderMngt/944_estimate/estimateProductDetail.php?itemID='+itemId;
  openWindowPopup(url, 'goodsdetail', 1300, 860);
}

function getParentList(){
  window.opener.getList(1);
}