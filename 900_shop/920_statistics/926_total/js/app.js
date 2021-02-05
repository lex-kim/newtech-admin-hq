const totalList1 = {"store":"ㅇㅇ공업사", "totalPrice":"300,000", "priceTotal":"290,000", "cardPrice":"200,000", "bankbookPrice":"20,000", 
                      "transferPrice":"70,000", "ntTotal":"3000","ntPoint":"2000","ntMallPoint":"1000"};
const totalList2 = {"store":"ㅇㅇ공업사", "totalPrice":"300,000", "priceTotal":"290,000", "cardPrice":"200,000", "bankbookPrice":"20,000", 
                      "transferPrice":"70,000", "ntTotal":"3000","ntPoint":"2000","ntMallPoint":"1000"};

const totalArray = new Array(totalList1, totalList2);
const jsonString = JSON.stringify(totalArray);
const jsonData = JSON.parse(jsonString);

$(document).ready(function() {

  setList();
 
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
setList = () =>{
  let historyDataText = '';

  if(jsonData.length > 0){
      jsonData.map((data)=>{
        console.log(data);
        historyDataText += "<tr>";
        historyDataText += "<td class='text-left'>"+data.store+"</td>";
        historyDataText += "<td class='text-right'>"+data.totalPrice+"</td>";
        historyDataText += "<td class='text-right'>"+data.priceTotal+"</td>";
        historyDataText += "<td class='text-right'>"+data.cardPrice+"</td>";
        historyDataText += "<td class='text-right'>"+data.bankbookPrice+"</td>";
        historyDataText += "<td class='text-right'>"+data.transferPrice+"</td>";
        historyDataText += "<td class='text-right'>"+data.ntTotal+"</td>";
        historyDataText += "<td class='text-right'>"+data.ntPoint+"</td>";
        historyDataText += "<td class='text-right'>"+data.ntMallPoint+"</td>";
        historyDataText += "</tr>"
      });
  }else{
      historyDataText = "<tr><td class='text-center' colspan='7'>정보가 존재하지 않습니다.</td></tr>";
  }
  
  console.log(historyDataText);
  $('#productList').html(historyDataText);
}
   