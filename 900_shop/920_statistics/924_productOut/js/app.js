const productList1 = {"IDX":"1", "store":"매출처", "bizPhone":"02-123-1234","bizFax":"02-123-1235","bizFao":"담당자", "bizFaoPhoneNum":"010-1234-1234",
                      "CTGY":"카테고리", "productCode":"ss-110" ,"productNm":"순정품731(310ml)", "purchaseQuantity":"100", "purchasePrice":"30000000", 
                      "salesQuantity":"200", "salesPrice":"200000000"};
const productList2 = {"IDX":"1", "store":"매출처", "bizPhone":"02-123-1234","bizFax":"02-123-1235","bizFao":"담당자", "bizFaoPhoneNum":"010-1234-1234",
                      "CTGY":"카테고리", "productCode":"ss-110" ,"productNm":"순정품731(310ml)", "purchaseQuantity":"100", "purchasePrice":"30000000", 
                      "salesQuantity":"200", "salesPrice":"200000000"};

const productArray = new Array(productList1, productList2);
const jsonString = JSON.stringify(productArray);
const jsonData = JSON.parse(jsonString);

$(document).ready(function() {

  setProductList();
  
  $( "#START_DT, #END_DT" ).datepicker({
    dateFormat: 'yy-mm-dd',
    prevText: '이전 달',
    nextText: '다음 달',
    monthNames: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
    monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
    dayNames: ['일','월','화','수','목','금','토'],
    dayNamesShort: ['일','월','화','수','목','금','토'],
    dayNamesMin: ['일','월','화','수','목','금','토'],
    showMonthAfterYear: true,
    changeMonth: true,
    changeYear: true,
    yearSuffix: '년'
  });
 
});


  
/** 동적으로 history table을 만든다 */
setProductList = () =>{
  let historyDataText = '';

  if(jsonData.length > 0){
      jsonData.map((data)=>{
        console.log(data);
        historyDataText += "<tr>";
        historyDataText += "<td class='text-center'>"+data.IDX+"</td>";
        historyDataText += "<td class='text-left'>"+data.store+"</td>";
        historyDataText += "<td class='text-center'>"+data.bizPhone+"</td>";
        historyDataText += "<td class='text-center'>"+data.bizFax+"</td>";
        historyDataText += "<td class='text-center'>"+data.bizFao+"</td>";
        historyDataText += "<td class='text-center'>"+data.bizFaoPhoneNum+"</td>";
        historyDataText += "<td class='text-center'>"+data.CTGY+"</td>";
        historyDataText += "<td class='text-center'>"+data.productCode+"</td>";
        historyDataText += "<td class='text-left'>"+data.productNm+"</td>";
        historyDataText += "<td class='text-right'>"+data.purchaseQuantity+"</td>";
        historyDataText += "<td class='text-right'>"+data.salesQuantity+"</td>";
        historyDataText += "<td class='text-right'>"+numberWithCommas(data.purchasePrice)+"</td>";
        historyDataText += "<td class='text-right'>"+numberWithCommas(data.salesPrice)+"</td>";
        historyDataText += "</tr>"
      });
  }else{
      historyDataText = "<tr><td class='text-center' colspan='9'>정보가 존재하지 않습니다.</td></tr>";
  }
  
  console.log(historyDataText);
  $('#productList').html(historyDataText);
}
   