let CURR_PAGE = 1;
let addTrLength = 0;
let oriPayTypeHtml = "";    // 수정시 부과기준에 따른 하단 테이블 원래 html을 저장 
let selectPayTypeValue = "";

$(document).ready(function() {
    const tdLength = $('table th').length;
    setInitTableData(tdLength, 'deliveryList');

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

    $('#GW_PAYMENT_DT').datepicker({
        dateFormat : 'yy-mm-dd',
    })
   
    if($("input:radio[name=EC_DELIVERY_FEE]:checked").val() == EC_DELIVERY_FEE_FREE_PAY){
      dPrice = 0;
  }
    /** 결제구분 클릭이벤트 */
    $("input:radio[name=EC_DELIVERY_FEE]").change(function(){
        if($(this).val() == EC_DELIVERY_FEE_FREE_PAY){                   // 무료배송일경우
          $('#deliveryPrice').val(0);
          $("input:radio[name=EC_DELIVERY_FEE_TYPE]").each(function(){   
              if($(this).val() != EC_DELIVERY_FEE_TYPE_FIX){             // 부과기준 고정 아닌부분을 disabled처리
                  $(this).attr('disabled',true);
              }else{
                  $(this).prop('checked',true).trigger('change');           // 부과기준 고정 체크 후 클릭 트리거
              }
          });
          
        }else if($(this).val() == EC_DELIVERY_FEE_METHOD){               // 통합
          $("input:radio[name=EC_DELIVERY_FEE_TYPE]").each(function(){   
              if($(this).val() != EC_DELIVERY_FEE_TYPE_FIX){             // 부과기준 고정 아닌부분을 disabled처리
                  $(this).attr('disabled',true);
              }else{
                  $(this).prop('checked',true);                          // 부과기준 고정 체크 후 클릭 트리거
              }
          });
          $('#STANDARD_AREA').hide();
          $('#81624_container').hide();

        }else{
          if(!$('#STANDARD_AREA').is(":visible")){
            $("input:radio[name=EC_DELIVERY_FEE_TYPE]").each(function(){   // 착불/선불일 경우
                if($(this).is(":checked")){                                // 부과기준 클릭 트리거
                  $(this).trigger('change');
                }
                $(this).attr('disabled',false);                             // disabled 해제
            });
          }
        }
    });

    /** 부과기준 이벤트 */
    $("input:radio[name=EC_DELIVERY_FEE_TYPE]").change(function(){
        if($(this).val() == EC_DELIVERY_FEE_TYPE_FIX){     // 고정일경우
          if($("input:radio[name=EC_DELIVERY_FEE]:checked").val() == EC_DELIVERY_FEE_POST_PAY || 
              $("input:radio[name=EC_DELIVERY_FEE]:checked").val() == EC_DELIVERY_FEE_PRE_PAY ){ // 부과기준이 고정인데 결제구분이 착불/선불인 경우
                $('#deliveryPrice').attr('disabled',false);
          }else{
            $('#deliveryPrice').attr('disabled',true);
          }
          $('#STANDARD_AREA').hide();
          $('#81624_container').show();
          $('#deliveryPrice').focus();

        }else{                                             // 수량/금액 일 경우
          if(!$('#STANDARD_AREA').is(":visible")){
              $('#STANDARD_AREA').show();
              $('#81624_container').hide();
          }
          $('#STD_TEXT').html(getFeeTypeText());
          $('#STANDARD_AREA tbody').hide();
          
          $('#'+$(this).val()+"_container .changePlaceholder").each(function(idx){
             const oriPlaceholderValue = $(this).attr("placeholder").split(" ")[1];
             $(this).attr("placeholder", $(this).attr("placeholder").replace(oriPlaceholderValue, getFeeTypeText()));
          });
          $('#'+$(this).val()+"_container").show();
        }  
    });

    $("input:radio[name=EC_DELIVERY_FEE]:checked").trigger('change'); 
});


function onClickOverBtn(idx){
    if(!empty($('#startNumber_'+idx).val())){
      if($('#OVER_CHECK_'+idx).is(":checked")){
        $('#moreThanYN_'+idx).val("Y");
        $('#endNumber_'+idx).attr('disabled',true);
        $('#endNumber_'+idx).val('');
        $('#sectionPrice_'+idx).focus();
        const selectedValue = $("input:radio[name=EC_DELIVERY_FEE_TYPE]:checked").val();
        $('#'+selectedValue+"_container tr").each(function(){ // 이상 체크된 위에 테이블들은 삭제처리
            const trIdx = $(this).attr('id').split("_")[1];
            if(trIdx > idx){
                $(this).html("");
            }
        });

      }else{
        $('#moreThanYN_'+idx).val("N");
        $('#endNumber_'+idx).attr('disabled',false);
      }
    }else{
      alert("시작"+getFeeTypeText()+"을 입력해주셔야 합니다.");
      $('#startNumber_'+idx).focus();
      $('#OVER_CHECK_'+idx).prop("checked",false);
    }
}

/**
 * 착불/선불상황에서 행 삭제시
 * @param {삭제 idx} deleteTrID 
 */
function deleteFreeTypeTr(deleteTrID){
    $('#info_'+deleteTrID).remove();
}

/**
 * 부과기준이 고정일 때 동적 html
 * @param {부과금액} price 
 */
function getFixDeliveryPriceHtml(price){
    let dPrice = empty(price) ? (empty($('#deliveryPrice').val()) ? "0" : $('#deliveryPrice').val() ) : price;

    let html = "";
    html += '<div class="col-md-1"><label for="deliveryPrice">부과금액(VAT포함)</label></div>';
    html += '<div class="col-md-3">';
    html += '<input type="text" class="form-control text-right price" value="'+dPrice+'"  placeholder="숫자만 입력"';
    html += 'id="deliveryPrice"  placeholder="금액" onkeyup="isNumber(event)" ';
    html += ' />';
    html += '</div>';

    return html;
}



/** 선택된 부과기준에 따른 택스트 얻기 */
function getFeeTypeText(){
    const selectVal = $("input:radio[name=EC_DELIVERY_FEE_TYPE]:checked").val();

    if(selectVal == EC_DELIVERY_FEE_TYPE_NUM){
      typeText = "수량";
    }else if(selectVal == EC_DELIVERY_FEE_TYPE_PRRICE){
      typeText = "금액";
    }else{
      typeText = "";
    }
    return typeText;
}


/**
 * 수량 행 추가시 동적으로 생성
 * @param {수정 시 필요한 데이터} item 
 * @param {수정 시 필요한 행 index 수} idx 
 */
function getFreeTypeHtmlText(isInitialize, item){
    let html = "";
    addTrLength += 1;
    let typeText = empty(getFeeTypeText()) ? "수량" : getFeeTypeText();
    let overCheckboxCheckedText = "";            // 이상 체크박스 체크 처리
    let endNumberDisabledText = "";              // 이상 체크박스에 체크시 그 해당 endNumber disabled 처리
    if(!empty(item) && item.moreThanYN == "Y"){  // 수정일 경우 이상체크에 체크된 경우
      overCheckboxCheckedText = "checked";
      endNumberDisabledText = "disabled";
    }

    html += '<tr class="info_" id="info_'+addTrLength+'">';
    html += '<td class="text-center num-select">';
    html += '<input type="text" class="form-control text-right changePlaceholder" value="'+(empty(item)? "" : item.startNumber)+'" ';
    html += 'onkeyup="isNumber(event)"';
    html += 'id="startNumber_'+addTrLength+'"  placeholder="시작 '+typeText+'" />';
    html += '<input type="checkbox" class="moreThanYN" '+overCheckboxCheckedText+' id="OVER_CHECK_'+addTrLength+'" onclick="onClickOverBtn('+addTrLength+')" value="Y">';
    html += '<input type="hidden"   id="moreThanYN_'+addTrLength+'" value="'+(empty(item)? "N" : item.moreThanYN)+'">';
    html += '<label for="OVER_CHECK_'+addTrLength+'">이상</label>';
    html += '<div>~</div>';
    html += '<input type="text" class="form-control text-right changePlaceholder" value="'+(empty(item)? "" :item.endNumber)+'" ';
    html += 'onkeyup="isNumber(event)"';
    html += 'id="endNumber_'+addTrLength+'" '+endNumberDisabledText+'  placeholder="종료 '+typeText+'"/>';
    html += '</td>';
    html += '<td class="text-center">';
    html += '<input type="text" class="form-control text-right price" value="'+(empty(item)? "" :item.sectionPrice)+'" ';
    html += 'onkeyup="isNumber(event)"';
    html += 'id="sectionPrice_'+addTrLength+'"  placeholder="숫자만 입력" onkeyup="isNumber(event)" />';
    html += '</td>';
    html += '<td class="text-center">';
    if(empty(isInitialize)){
      html += '<button type="button" class="btn btn-danger" onclick="deleteFreeTypeTr('+addTrLength+')">삭제</button>';
    }
    html += '</td>';
    html += '</tr>';

    return html;
}


/** 부과기준 수량/금액에서 하단에 수량 행 추가 클릭시 동적으로 생성 */
function addFeeTypeRow(){
    let isOverChecked = true;
    let checkdIdx = "";
    $('.moreThanYN').each(function(){
        if($(this).is(":checked") && $(this).is(":visible")){
          checkdIdx = $(this).attr('id').split("_")[2];
          isOverChecked = false;
          return false;
        }
    });

    if(isOverChecked){
      $('#STANDARD_AREA tbody').each(function(){
        if($(this).is(":visible")){
          $(this).append(getFreeTypeHtmlText());
        }
      });
    }else{
      alert("이상버튼이 체크되어 있으면 행추가가 되지 않습니다.");
      $('#startNumber_'+checkdIdx).focus();
    }
}


setTable = (dataList, totalListCount, cPage) => {
  let html = "";
  dataList.map((data)=>{
      html += "<tr>";
      html += "<td class='text-center'>"+data.issueCount+"</td>";
      html += "<td class='text-center'>"+data.deliveryPolicyName+"</td>";
      html += "<td class='text-center'>"+data.payType+"</td>";
      html += "<td class='text-center'>"+data.priceType+"</td>";
      html += "<td class='text-center'>"+numberWithCommas(data.deliveryPrice)+"</td>";
      html += "<td class='text-center'>"+data.writeUser+"</td>";
      html += "<td class='text-center'>"+data.writeDatetime+"</td>";
      html += "<td class='text-center'>"+data.lastUser+"</td>";
      html += "<td class='text-center'>"+data.lastDatetime+"</td>";
      html += "<td class='text-center'><button type='button' class='btn btn-default' onclick='showUpdateModal("+JSON.stringify(data)+")'>수정</button></td>";
      html += "</tr>"
  });
  paging(totalListCount, cPage, 'getList'); 
  $('#deliveryList').html(html);
}

excelDownload = () => {
  location.href="deliveryExcel.php?"+$("#searchForm").serialize()+"&REQ_MODE="+CASE_TOTAL_LIST;
}


setData = (REQ_MODE) => {
  let form = $("#editForm")[0];
  let formData = new FormData(form);
  formData.append('REQ_MODE', REQ_MODE);

  callFormAjax(ERP_CONTROLLER_URL, 'POST', formData).then((result)=>{
    if(isJson(result)){
      const res = JSON.parse(result);
      if(res.status == OK){
        if($('#deliveryID').val() != ""){
          alert("수정이 완료되었습니다.");
        }else{
          alert("등록이 완료되었습니다.");
        }

        window.close();
        if($('#deliveryID').val() != ""){
          window.opener.getList();
        }else{
          window.opener.getList(1);
        }
      }else{
        alert(ERROR_MSG_1100);
      }
    }else{
        alert(ERROR_MSG_1200);
    }
  });  
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

delData = (delID) => {
  const data = {
      REQ_MODE: CASE_DELETE,
      deliveryPolicyID :  delID
  };
  callAjax(ERP_CONTROLLER_URL,'POST', data).then((result)=>{
      if(isJson(result)){
        const res = JSON.parse(result);
        if(res.status == OK){
           alert("삭제가 완료되었습니다.");
           window.close();
           window.opener.getList();
        }else{
          alert(ERROR_MSG_1100);
        }
      }else{
          alert(ERROR_MSG_1200);
      }
  }); 
}

/** 업데이트 모달창일경우 */
showUpdateModal = (data) => {
    const url = '/900_shop/910_mngt/918_delivery/deliveryModal.php?deliveryID='+data.deliveryPolicyID;
    openWindowPopup(url, 'deliveryModal', 1300, 450);
}

/* 등록 모달창*/ 
showAddModal = () => {
    const url = '/900_shop/910_mngt/918_delivery/deliveryModal.php';
    openWindowPopup(url, 'deliveryModal', 1300, 450);
}

/**통합배송정책 팝업창 오픈 */
showDeliverySetting = () =>{
  const url = '/900_shop/910_mngt/918_delivery/deliverySetting.php';
    openWindowPopup(url, 'deliveryModal', 630, 450);
}


onclickCancel=() =>{
  confirmSwalTwoButton(
    '취소하시겠습니까?','확인', '취소', false ,
    ()=>{}, '확인을 누르시면 해당 건을 취소합니다.'
  );
}