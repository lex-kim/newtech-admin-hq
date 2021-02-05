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

    $('input:checkbox[name=extend]').change(function(){
        if($(this).is(':checked')){
          getList(1, "Y");
        }else{
          getList(1, "N");
        }
    });
});

excelDownload = () => {
  location.href="productCategoryExcel.php?"+$("#searchForm").serialize()+"&REQ_MODE="+CASE_TOTAL_LIST;
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
      html += "<td class='text-left'>"+(!empty(data.parentCategory) ? data.parentCategory : ""); 
      if(!empty(data.parentCategoryID)){
       html += " ("+data.parentCategoryID+")";
      }
      html += "</td>";
      html += "<td class='text-center'>";
      if(empty(data.parentCategoryID)){
        html += "<button type='button' class='btn btn-default' onclick='showAddModal("+JSON.stringify(data)+")'>추가</button>"; 
      }
      html += "</td>";
      html += "<td class='text-left'>"+data.thisCategory;
      html += " ("+data.thisCategoryID+")";
      html += "</td>";
      html += "<td class='text-center'>"+data.exposeYN+"</td>";
      html += "<td class='text-center'>"+data.orderNum+"</td>";
      html += "<td class='text-center'>"+(empty(data.writeUser) ? "-" : data.writeUser)+"</td>";
      html += "<td class='text-center'>"+(empty(data.writeDatetime) ? "-" : data.writeDatetime)+"</td>";
      html += "<td class='text-center'><button type='button' class='btn btn-default' onclick='showUpdateModal("+JSON.stringify(data)+")'>수정</button></td>";
      html += "</tr>"
  });
  paging(totalListCount, cPage, 'getList'); 
  $('#productList').html(html);
}

getList = (page, baseCategoryYN) => {
  CURR_PAGE = (page == undefined ) ? CURR_PAGE : page;
  let form = $("#searchForm")[0];
  let formData = new FormData(form);
  let isBaseCategory = "N";

  formData.append('REQ_MODE', CASE_LIST);
  formData.append('PER_PAGE', $("#perPage").val());
  formData.append('CURR_PAGE', CURR_PAGE);

  if(empty(baseCategoryYN)){
    if($('input:checkbox[name=extend]').is(":checked")){
      isBaseCategory = "Y"
    }
  }else{
    isBaseCategory = baseCategoryYN;
  }
  formData.append('baseCategoryYN', isBaseCategory);

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
      thisCategoryID :  delID
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

setData = (REQ_MODE) => {
  let form = $("#editForm")[0];
  let formData = new FormData(form);
  formData.append('REQ_MODE', REQ_MODE);
  formData.append('parentCategoryID', $('#parentCategoryID').selectpicker('val'));
  
  callFormAjax(ERP_CONTROLLER_URL, 'POST', formData).then((result)=>{
    if(isJson(result)){
      const res = JSON.parse(result);
      if(res.status == OK){
        if($('#thisCategoryID').val() != ""){
          alert("수정이 완료되었습니다.");
        }else{
          alert("등록이 완료되었습니다.");
        }

        window.close();
        if($('#editForm').val() != ""){
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

/** 업데이트 모달창일경우 */
showUpdateModal = (data) => {
  const url = '/900_shop/910_mngt/912_productCategory/productCategoryModal.php?ctgyID='+data.thisCategoryID;
  openWindowPopup(url, 'productCategoryModal', 500, 350); 
}

/* 등록 모달창*/ 
showAddModal = (data) => {
    let params = empty(data) ? "" : "?pCtgyID="+data.thisCategoryID;
    const url = '/900_shop/910_mngt/912_productCategory/productCategoryModal.php'+params;
    openWindowPopup(url, 'productCategoryModal', 500, 400); 
}