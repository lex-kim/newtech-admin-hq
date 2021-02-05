const purchaseList1 = {"IDX":"1","purchaseNm":"뉴텍",  "quantity":"100", "price":"30000000", "WRT_DTHMS":"2017-04-12"};
const purchaseList2 = {"IDX":"2","purchaseNm":"뉴텍",  "quantity":"200", "price":"60000000", "WRT_DTHMS":"2017-04-12"};

const purchaseArray = new Array(purchaseList1, purchaseList2);
const jsonString = JSON.stringify(purchaseArray);
const jsonData = JSON.parse(jsonString);

$(document).ready(function() {

  setPurchaseList();
 
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
setPurchaseList = () =>{
  let historyDataText = '';

  if(jsonData.length > 0){
      jsonData.map((data)=>{
        console.log(data);
        historyDataText += "<tr>";
        historyDataText += "<td class='text-center'>"+data.IDX+"</td>";
        historyDataText += "<td class='text-center'>"+data.purchaseNm+"</td>";
        historyDataText += "<td class='text-right'>"+data.quantity+"</td>";
        historyDataText += "<td class='text-right'>"+numberWithCommas(data.price)+"</td>";
        historyDataText += "</tr>"
      });
  }else{
      historyDataText = "<tr><td class='text-center' colspan='9'>정보가 존재하지 않습니다.</td></tr>";
  }
  
  console.log(historyDataText);
  $('#purchaseList').html(historyDataText);
}
   