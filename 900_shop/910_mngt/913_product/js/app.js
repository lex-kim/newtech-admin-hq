let optionTrLength = 0;
let CURR_PAGE = 1;

$(document).ready(function() {
    const tdLength = $('table th').length;
    setInitTableData(tdLength, 'productList');

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
    
    /** 에디터 */
    $('.summernote').summernote({
        height: 300,                 // set editor height
        minHeight: null,             // set minimum height of editor
        maxHeight: null,             // set maximum height of editor
        focus: false , 
    });

    /** 수정시 히든으로 저장된 textarea값을 확인후 값이 존재하면 에디터에 넣어준다. */
    $('.summernoteArea').each(function(){
        if(!empty($(this).val())){
            const id = $(this).attr("id").split("_")[0];
            $('#'+id+"_").summernote('code',$(this).val());
        }
    });

    // 판매가,적립금 문의 체크
    $(":input:checkbox[name=priceQuestionCheckbox]").click(function(){
        if($(this).is(":checked")){
          $("#salePrice").val(0);
          $("#earnPoint").val(0);
          $("#salePrice").prop('disabled', true);
          $("#earnPoint").prop('disabled', true);
        }else{
          $("#salePrice").val("");
          $("#earnPoint").val("");
          $("#salePrice").prop('disabled', false);
          $("#earnPoint").prop('disabled', false);
        }
    }); 

    $('input[name=goodsOption]').click(function(){
        if($(this).val() == "Y"){
          $('#optionTable input').prop('disabled',false);
          $('#productOptionContainer').show();
        }else{
          $('#optionTable input').prop('disabled',true);
          $('#productOptionContainer').hide();
        }
    });

    $('input[name=goodsOption]:checked').trigger('click'); // 옵션 사용 여부 체크 
    if($('#priceQuestionCheckbox').is(":checked")){        // 문의 체크 이벤트
        $('#priceQuestionCheckbox :checked').trigger('click');
    }
});

function onCheckPriceValue(){
    if(!empty($('#salePrice').val()) && $('#salePrice').val() == 0){
      $('#priceQuestionCheckbox').trigger('click');
    }
}

getFileHtml = (itemInfo, imgValue) => {
    let displayText = "";
    let fileName = NOT_SELECT_FILE_TEXT;
    let html = "";
    html += '<div class="row">';
    html += '<div class="col-md-12" style="display:flex; flex-direction:row; ">';
    html += "<button type='button' class='btn btn-default'  onclick='onClickFileBtn(\""+imgValue+"\")'>파일선택</button>";
    if(empty(itemInfo[imgValue+'OriginFilename'])){
      displayText = "style='display:none'";
    }else{
      fileName = itemInfo[imgValue+'OriginFilename'];
    }
    html += '<div class="fileContainer">';
    html += '<div class="fileSelectText" id='+imgValue+'_SELECT_TEXT" >'+fileName+'</div>';
    html += "<button type='button' class='itemDelBtn'  onclick='delUploadItem(\""+imgValue+"\")'";
    html += ' '+displayText+'>&times;</button>';
    html += '</div>';
    html += '<input type="hidden" name="'+imgValue+'URI" id='+imgValue+'URI" ';
    html += 'value="'+itemInfo[imgValue+'URI']+'"/>';
    html += '<input type="hidden" name='+imgValue+'OriginFilename" id='+imgValue+'OriginFilename"';
    html += 'value="'+itemInfo[imgValue+'OriginFilename']+'"/>';
    html += '<input type="hidden" name='+imgValue+'PhysicalFilename" id='+imgValue+'PhysicalFilename"';
    html += 'value="'+itemInfo[imgValue+'PhysicalFilename']+'"/>';
    html += '<input id="'+imgValue+'" name="'+imgValue+'" type="file" class="form-control input-md file"/>';
    html += '</div>';
    html += '</div>';
    return html;
}

getFileContainerHtml = (itemInfo, imgValue, title , isNecessary) =>{
    let html = "";
    let necessaryHtml = (empty(isNecessary) ? "" : '<span class="necessary">*</span>');
    html += '<div class="modal-unit">';
    html += '<div class="modal-label"><label for='+imgValue+'"_Title">'+title+' '+necessaryHtml+'</label></div>';
    html += '<div class="modal-select">';
    html += getFileHtml(itemInfo, imgValue)
    html += '</div>';
    html += '</div>';

    return html;
}

delOptionTable = (delIdx) =>{
  $('#option_'+delIdx).remove();
}

/** 옵션 동적 html 생성 */
getOptionHtmlText = (item, idx) => {
    optionTrLength = (empty(idx) ? optionTrLength+1 : idx);
    let optionY_Checked = (empty(item)? "checked" : (item.optionUseYN == "Y" ? "checked" :""));
    let optionN_Checked = (empty(item)? "" : (item.optionUseYN == "N" ? "checked" :""));

    let html = "";
    html += '<tr id="option_'+optionTrLength+'">';
    html += '<td class="text-left">';
    html += '<input type="text" class="form-control" value="'+(empty(item) ? "" :item.optionName)+'"'; 
    html += ' id="optionNM_'+optionTrLength+'" name="optionName[]" placeholder="입력" required/>';
    html += '</td>';
    html += '<td class="text-right">';
    html += '<input type="text" class="form-control" value="'+(empty(item) ? "" :item.optionPrice)+'"'; 
    html += ' onkeyup="isNumber(event)" id="optionPrice_'+optionTrLength+'" name="optionPrice[]" placeholder="숫자만 입력" required/>';
    html += '</td>';
    html += '<td class="text-left">';
    html += '<label class="radio-inline" for="option_Y_'+optionTrLength+'"><input type="radio" id="option_Y_'+optionTrLength+'" name="optionUseYN['+optionTrLength+']" '+optionY_Checked+' value="Y" required/>사용함</label>';
    html += '<label class="radio-inline" for="option_N_'+optionTrLength+'"><input type="radio" id="option_N_'+optionTrLength+'" name="optionUseYN['+optionTrLength+']" '+optionN_Checked+'  value="N" required/>사용안함</label>';
    html += '</td>';
    html += '<td>';
    if($('#optionTable tr').length > 0){
      html += "<button type='button' class='btn btn-danger option_subtract_btn' onclick='delOptionTable("+optionTrLength+")'>삭제</button>";
    }
    html += '</td>';
    html += '</tr>';

    return html;
}

/** 옵션 추가 버튼 클릭시  */
onClickAddOptionBtn = () => {
  if($('#optionTable tr').length < 10){
    let html = getOptionHtmlText();
    $('#optionTable').append(html);
  }else{
    alert("옵션은 최대 10개만 추가하실 수 있습니다.");
  }
  
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
    html += "<td class='text-center'>"+data.mgmtType+"</td>";
    html += "<td class='text-center'>"+data.categoryName+"</td>";
    html += "<td class='text-center'>"+data.buyerType+"</td>";
    html += "<td class='text-left'>"+data.partnerName+"</td>";
    html += "<td class='text-center'>"; 
    if(!empty(data.imgURI)){
      html += "<img src='/images/icon_download.gif'>";
    }
    html += "</td>";
    html += "<td class='text-center'>"+data.goodsCode+"</td>";
    html += "<td class='text-left'>"+data.goodsName+"</td>";
    html += "<td class='text-right'>"+data.goodsSpec+"</td>";
    html += "<td class='text-right'>"+data.unitPerBox+"</td>";
    html += "<td class='text-right'>"+data.minBuyCnt+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(data.buyPrice)+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(data.wholeSalePrice)+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(data.retailPrice)+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(data.salePrice)+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(data.earnPoint)+"</td>";
    html += "<td class='text-left'>"+data.deliveryPolicyName+"</td>";
    html += "<td class='text-left'>"+data.manufacturerName+"</td>";
    html += "<td class='text-left'>"+data.originName+"</td>";
    html += "<td class='text-left'>"+data.brandName+"</td>";
    html += "<td class='text-center'>"; 
    if(!empty(data.catalogURI)){
      html += "<img src='/images/icon_download.gif'>";
    }
    html += "</td>";
    html += "<td class='text-center'>"+data.soldOutYN+"</td>";
    html += "<td class='text-center'>"+data.stockRemains+"</td>";
    html += "<td class='text-center'>"+data.writeUser+"</td>";
    html += "<td class='text-center'>"+data.writeDatetime+"</td>";
    html += "<td class='text-center'>"+data.lastUser+"</td>";
    html += "<td class='text-center'>"+data.lastDatetime+"</td>";
    html += "<td class='text-center'><button type='button' class='btn btn-default' onclick='onClickOrderBtn("+JSON.stringify(data)+")'>발주</button></td>";
    html += "<td class='text-center'><button type='button' class='btn btn-default' onclick='showUpdateModify("+JSON.stringify(data)+")'>수정</button></td>";
    html += "</tr>"
  });
  paging(totalListCount, cPage, 'getList'); 
  $('#productList').html(html);
}

function onClickOrderBtn(data){
  const url = '../../../900_shop/960_inout/965_inoutOrder/inoutWrite.php?partnerID='+data.partnerID+"&itemID="+data.itemID;
  openWindowPopup(url, 'inoutWrite', 1200, 860);
}

excelDownload = () => {
  location.href="buyMngtExcel.php?"+$("#searchForm").serialize()+"&REQ_MODE="+CASE_TOTAL_LIST;
}

function onResetChildCategory(){
  $('#categoryCd2').prop("disabled", true);
  let html = "<option value=''>=하위카테고리=</option>";
  $('#categoryCd2').html(html);
}
function onChangeParentCtgy(){
  if(empty($('#categoryCd1').val())){
    onResetChildCategory();
  }else{
    $('#categoryCd2').prop("disabled", false);
    getChildCategory($('#categoryCd1').val());
  }
}

function getChildCategory(parentCtgy){
  const data = {
      REQ_MODE: CASE_LIST,
      parentCategoryID :  parentCtgy
  };

  callAjax(ERP_CONTROLLER_URL,'POST', data).then((result)=>{
      const res = JSON.parse(result);
      if(isJson(result)){
        if(res.status == OK){
          let html = "<option value=''>=하위카테고리=</option>";
          res.data.map((item)=>{
            html += "<option value='"+item.thisCategoryID+"'>"+item.thisCategory+"</option>";
          }); 
          $('#categoryCd2').prop("disabled", false);
          $('#categoryCd2').html(html);
        }else{
          onResetChildCategory();
          alert(res.message);
        }
      }else{
        onResetChildCategory();
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
          alert((empty(res.message) ? ERROR_MSG_1100 : res.message));
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

  $('.summernote').each(function(){
      const value = $(this).summernote('code').replace('<p><br></p>','');

      const id = $(this).attr("id").split("_")[0];
      formData.append(id, value);
  });

  callFormAjax(ERP_CONTROLLER_URL, 'POST', formData).then((result)=>{
    if(isJson(result)){
      const res = JSON.parse(result);
      if(res.status == OK){
        if($('#productID').val() != ""){
          alert("수정이 완료되었습니다.");
        }else{
          alert("등록이 완료되었습니다.");
        }

        window.close();
        if($('#productID').val() != ""){
          window.opener.getList();
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

delData = (delID) => {
  const data = {
      REQ_MODE: CASE_DELETE,
      itemID :  delID
  };
  callAjax(ERP_CONTROLLER_URL,'POST', data).then((result)=>{
      if(isJson(result)){
        const res = JSON.parse(result);
        if(res.status == OK){
           alert("삭제가 완료되었습니다.");
           window.close();
           window.opener.getList();
        }else{
          alert((empty(res.message) ? ERROR_MSG_1100 : res.message));
        }
      }else{
          alert(ERROR_MSG_1200);
      }
  }); 
}

// 등록
showAddModify = () => {
  const url = '/900_shop/910_mngt/913_product/productModify.php';
  openWindowPopup(url, 'productModify', 1000, 660);
}

// 수정
showUpdateModify = (data) => {
  const url = '/900_shop/910_mngt/913_product/productModify.php?itemID='+data.itemID
  openWindowPopup(url, 'productModify', 1000, 660);
}

excelDownload = () => {
  location.href="productExcel.php?"+$("#searchForm").serialize()+"&REQ_MODE="+CASE_TOTAL_LIST;
}