let CURR_PAGE = 1;

$(document).ready(function() {
  if($('#pageIndicator').is(":visible")){
    $('#pageIndicator').hide();
  }
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

setTable = (dataList, totalListCount, cPage) => {
  let html = "";
  dataList.map((item, idx)=>{
    let stockTotalCnt = Number(item.itemRemains) + Number(item.carRemains);
    let stockTotalPrice = Number(item.itemRemains * item.buyPrice) + Number(item.carRemains * item.buyPrice);
    let salesTotalCnt = Number(item.totalSales) + Number(item.totalOrder);
    let salesTotalPrice = Number(item.totalSales * item.buyPrice) + Number(item.totalOrder * item.buyPrice);

    html += "<tr>";
    html += "<td class='text-center'>"+item.issueCount+"</td>";
    html += "<td class='text-center'>"+item.mgmtType+"</td>";
    html += "<td class='text-center'>"+item.categoryName+"</td>";
    html += "<td class='text-left'>"+item.goodsName+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(item.buyPrice))+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(stockTotalCnt)+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(stockTotalPrice)+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(item.itemRemains))+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(item.itemRemains * item.buyPrice))+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(item.carRemains))+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(item.carRemains * item.buyPrice))+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(salesTotalCnt)+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(salesTotalPrice)+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(item.totalSales))+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(item.totalSales * item.buyPrice))+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(item.totalOrder))+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(item.totalOrder * item.buyPrice))+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(item.totalStockLost))+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(item.totalStockLost * item.buyPrice))+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(item.totalStockDisposal))+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(item.totalStockDisposal * item.buyPrice))+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(item.StockOutAverage3M))+"</td>";
    html += "<td class='text-right'>"+numberWithCommas(Number(item.stockOrderRequire))+"</td>";
    html += "<td class='text-right'>"+item.remainRatio+"%</td>";
    html += "<td class='text-center'><button type='button' class='btn btn-default' onclick='onClickOrderBtn(\""+item.partnerID+"\",\""+item.itemID+"\")'>발주</button></td>";
    html += "</tr>"
  });

  paging(totalListCount, cPage, 'getList'); 
  $('#dataTable').html(html);
  $('#dataTable').show();
}

getList = (page) => {
  let time = 1;
  currPage = (page == undefined ) ? currPage : page;
  let form = $("#searchForm")[0];
  let formData = new FormData(form);
  formData.append('REQ_MODE', CASE_LIST);
  formData.append('PER_PAGE', $("#perPage").val());
  formData.append('CURR_PAGE', currPage);

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
            setTable(res.data, res.total_item_cnt, currPage);
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

function getSortList(sortAsc, sortField){
  $('#sortField').val(sortField);
  $('#sortAsc').val(sortAsc);

  getList(1);
}

function onClickOrderBtn(partnerID, itemID){
  const url = '../../../900_shop/960_inout/965_inoutOrder/inoutWrite.php?partnerID='+partnerID+"&itemID="+itemID;
  openWindowPopup(url, 'inoutWrite', 1200, 860);
}

function excelDownload(){
  location.href="inoutStatusExcel.php?"+$("#searchForm").serialize();
}