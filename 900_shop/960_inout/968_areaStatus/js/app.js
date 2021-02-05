let CURR_PAGE = 1;

$(document).ready(function() {
  if($('#pageIndicator').is(":visible")){
    $('#pageIndicator').hide();
  }

  $( "#START_DT" ).datepicker(
      getDatePickerSetting('END_DT', 'minDate')
  );
  $( "#END_DT" ).datepicker(
      getDatePickerSetting('START_DT', 'maxDate')
  );

  const tdLength = $('table th').length;
  setInitTableData(tdLength);

  $('#searchForm').submit(function(e){
      e.preventDefault();

      $('#sortAsc').val('');
      $('#sortField').val('');

      getList(1);
  });

  $('#categoryCode').selectpicker();
  $('.bs-select-all').detach();
  $(".bs-deselect-all").css("float", "right");
  $(".bs-deselect-all").width("78%");
  $(".bs-deselect-all").html("전체 선택해제");
});

/** 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
  const tdLength = $('table th').length;
  const tableText = "<tr><td class='text-center' colspan='"+tdLength+"'>조회된 데이터가 존재하지 않습니다.</tr></tr>";
  $('.pagination').html('');
  $('#dataTable').html(tableText);
}

function setEmployeeTable(divisionArray, salePrice){
  let divisionHtml = "";

  headerArray.map(function(header){
    let isExistHeaderData =  false;
    divisionArray.map(function(item){
      if(header.divisionID == item.divisionID){
        let totalSales = empty(item.totalSales) ? 0 : item.totalSales;
        let totalStockReturn = empty(item.totalStockReturn) ? 0 : item.totalStockReturn;

        divisionHtml += "<td class='text-right'>"+numberWithCommas(Number(totalSales))+"</td>";
        divisionHtml += "<td class='text-right'>"+numberWithCommas(Number(totalSales * salePrice))+"</td>";
        divisionHtml += "<td class='text-right'>"+numberWithCommas(Number(totalStockReturn))+"</td>";
        divisionHtml += "<td class='text-right'>"+numberWithCommas(Number(totalStockReturn * salePrice))+"</td>";

        isExistHeaderData = true;
      }
    });

    if(!isExistHeaderData){
      for(let i = 0; i<4 ; ++i){
        divisionHtml += "<td class='text-right'>-</td>";
      }
    }
  });

  return divisionHtml;
}

setTable = (dataList, totalListCount, cPage) => {
  let html = "";
  dataList.map((data, idx)=>{
    html += "<tr>";
    html += "<td class='text-center'>"+data.issueCount+"</td>";
    html += "<td class='text-center'>"+data.mgmtType+"</td>";
    html += "<td class='text-center'>"+(empty(data.categoryName) ? "-" : data.categoryName)+"</td>";
    html += "<td class='text-left'>"+data.goodsName+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(data.buyPrice))+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(data.salePrice))+"</td>";
    html += setEmployeeTable(data.divisionList, data.salePrice);
    html += "</tr>"
  });

  paging(totalListCount, cPage, 'getList'); 
  $('#dataTable').html(html);
  $('#dataTable').show();
}

getList = (page) => {
  let time = 1;
  CURR_PAGE = (page == undefined ) ? CURR_PAGE : page;
  let form = $("#searchForm")[0];
  let formData = new FormData(form);
  formData.append('REQ_MODE', CASE_LIST);
  formData.append('PER_PAGE', $("#perPage").val());
  formData.append('CURR_PAGE', CURR_PAGE);

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
            setTable(res.data, res.total_item_cnt, CURR_PAGE);
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

function setAutoComplete(dataArr){
  try{
    const dataArray = JSON.parse(dataArr);
    $("#selectItemID").autocomplete({
        minLength : 1,
        source: dataArray.map((data, index) => {
            return {
                id : data.itemID,
                value : data.itemID
                // value : data.goodsName +" ["+data.itemID+"]"
            };
        }),
        focus: function(event, ui){ 
            return false;
        },
        select: function(event, ui) {
            let strArea = ui.item.value;
            $("#selectItemID").val(strArea);
            $('#itemID').val(ui.item.id);

            return false;
        }
    }).on('focus', function(){ 
        $(this).autocomplete("search"); 
    });
  }catch{
    alert("자동완성 설정에 실패하였습니다. 관라자에게 문의해주세요.")
  }  
}

function onchangeItemID(){
  if(empty($('#selectItemID').val())){
    $('#itemID').val("");
  }  
}

function excelDownload(){
  location.href="divisionStatusExcel.php?"+$("#searchForm").serialize();
}
   