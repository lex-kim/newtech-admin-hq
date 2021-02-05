/** 각 상품삭제 */
function onDeleteTable(index){
    $('#productTable_'+index).remove();
    setTotalPrice();
}

/** 행추가 클릭시 */
function addItemTable(){
    $('#itemDataTable').append(setProductTable());
    $('.selectpicker').selectpicker("render");
}

/** 상품 수량 변경이벤트 */
function onchangeCnt(index){
    if(is_number($('#cnt_'+index).val())){
        if(empty($('#cnt_'+index).val())){
            $('#cnt_'+index).val(1);
        }
        const cnt = empty($('#cnt_'+index).val()) ? 0 : Number($('#cnt_'+index).val());
        const unitPrice = empty($('#price_'+index).html()) ? 0 : Number($('#price_'+index).html().replace(/,/g, ''));
        const vatPrice = $('#vatPrice_'+index).val();
    
        $('#unitPrice_'+index).html(unitPrice == 0 ? "" : numberWithCommas(unitPrice * cnt));
        $('#vat_'+index).html(vatPrice == 0 ? "" : numberWithCommas(vatPrice*cnt));
        $('#totalPrice_'+index).html(unitPrice == 0 ? "" : numberWithCommas((unitPrice * cnt)+(vatPrice*cnt)));  
        
        setTotalPrice();
    }
    
}


/** 상품 선택
 *  @param {index : 몇번째 상품을 선택했는지}
 */
function onSelectItem(index){
    if(empty($('#item_'+index).val())){  // 빈값인 경우에는 초기화
        $('#cnt_'+index).val(1);
        $('#unit_'+index).html("");
        $('#price_'+index).html("");
        $('#unitPrice_'+index).html("");
        $('#vat_'+index).html("");
        $('#totalPrice_'+index).html("");
        $('#memo'+index).val("");
    }else{
        const optionID = $('#item_'+index+' :selected').attr('data');
        const selectedValue = productList.find(function(item){
            return item.itemID == $('#item_'+index).val();
        });
        let optionPrice = 0;
        let optionPriceVAT = 0;
        const cnt = Number($('#cnt_'+index).val());

        if(!empty(optionID)){  // 옵션이 있는 겨우
            optionPrice = (selectedValue.goodsOptions[optionID]).optionPriceWithoutVAT;
            optionPriceVAT = (selectedValue.goodsOptions[optionID]).optionPriceVAT;
            $('#option_'+index).val(optionID);
            $('#optionPrice_'+index).val((selectedValue.goodsOptions[optionID]).optionPrice);
        }
        let unitPrice = Number(selectedValue.buyPriceWithoutVAT) + Number(optionPrice);
        let vatPrice = Number(selectedValue.buyPriceVAT)+Number(optionPriceVAT);

        $('#itemPrice_'+index).val(selectedValue.buyPrice);
        $('#unit_'+index).html(selectedValue.goodsSpec);
        $('#price_'+index).html(numberWithCommas(unitPrice));
        $('#unitPrice_'+index).html(numberWithCommas(unitPrice * cnt));
        $('#vat_'+index).html(numberWithCommas(vatPrice*cnt));
        $('#vatPrice_'+index).val(vatPrice*cnt);
        $('#totalPrice_'+index).html(numberWithCommas((unitPrice * cnt)+(vatPrice*cnt)));
    }

    setTotalPrice();
}

/** 각 행에 있는 상품 select 동적 생성 */
function getProductList(index, productWidth){
    let html = '<select onchange="onSelectItem('+index+')" id="item_'+index+'" name="itemID[]"';
    html +=  'data-style="btn-default" class="selectpicker"'
    html +=  'data-live-search="true" data-live-search-placeholder="Search" data-width="'+productWidth+'px"  required>';
    html += "<option value=''>=선택=</option>";
    productList.map(function(item){
        if(item.goodsOptions != HAVE_NO_DATA){
        const optionArray = (item.goodsOptions).filter(function(subItem){
            return subItem.optionUseYN == "Y";
        });
        if(!empty(optionArray)){
            optionArray.map(function(optionItem){
                html += "<option data='"+optionItem.optionID+"' value='"+item.itemID+"'>"+item.goodsName+" ("+optionItem.optionName+" +"+numberWithCommas(optionItem.optionPrice)+")"+"</option>";
            });
        }else{
            html += "<option value='"+item.itemID+"'>"+item.goodsName+"</option>";
        }
        }else{
            html += "<option value='"+item.itemID+"'>"+item.goodsName+"</option>";
        }
    });
    html +='</select>';
    html +=  '<input type="hidden" id="option_'+index+'" value="-1" name="optionID[]"/>';
    html +=  '<input type="hidden" id="itemPrice_'+index+'" value="0" name="unitPrice[]"/>';
    html +=  '<input type="hidden" id="optionPrice_'+index+'" value="0" name="optionPrice[]"/>';
    html +=  '<input type="hidden" id="vatPrice_'+index+'" value="0"/>'; // 수량변화가 있을시 세액값의 변화를 주기 위해 hidden으로 원래 세액값을 저장

    return html;
}

/** 상품 테이블 동적 생성 */
function setProductTable(){
    productTableIdx += 1;
    const productWidth = $('#productTable').width();
    const unitWidth = $('#unitTable').width();

    let html = "<tr class='product' id='productTable_"+productTableIdx+"'>";
    html += "<td class='productColumn' style='width : "+productWidth+"px ; max-width : "+productWidth+"px ;'>";
    html += getProductList(productTableIdx, productWidth*0.95);
    html += "</td>";
    html += "<td class='text-center' style='max-width:"+unitWidth+"px; word-break: break-all;'><span id='unit_"+productTableIdx+"'></span></td>";
    html += "<td class='text-right'><span class='unitPriceClass'id='price_"+productTableIdx+"'></span></td>";
    html += "<td class='text-center' >";
    html += "<input onchange='onchangeCnt("+productTableIdx+")' class='form-control cntClass' name='quantity[]' id='cnt_"+productTableIdx+"' type='text' onKeyPress = 'getBeforeNumberText(event)' onkeyup='isNumber(event)' value='1'/>";
    html += "</td>";
    html += "<td class='text-right'><span class='priceClass' id='unitPrice_"+productTableIdx+"'></span></td>";
    html += "<td class='text-right'><span class='vatPriceClass' id='vat_"+productTableIdx+"'></span></td>";
    html += "<td class='text-right'><span class='totalPriceClass' id='totalPrice_"+productTableIdx+"'></span></td>";
    html += "<td class='text-left'>";
    html += "<input class='form-control' id='memo_"+productTableIdx+"' name='itemMemo[]' type='text'  value=''/>";
    html += "</td>";
    html += "<td class='text-center' id='del_"+productTableIdx+"'>";
    if($('.product').length > 0){
        html += "<button type='button' class='btn btn-danger btn-sm' onclick='onDeleteTable("+productTableIdx+")'>ㅡ</button>";
    }
    html += "</td>";
    html += "</tr>";

    
    return html;
}