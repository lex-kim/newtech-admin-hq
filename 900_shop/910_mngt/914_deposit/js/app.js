const depositist1 = {"idx":"182", "category":"언더코팅", "categoryCd":"under", "purchase":"시카코리아", "purchaseCd":"cika", "productImg" : "/images/icon_download.gif", 
                      "productCd":"NT-500", "productNm":"멀티실런트", "ibchul":"입고", "ibchulCd":"in", "price":"9600", "quantity":"10", "memo":"메모란 입니다.",
                      "UPDATE_DTHMS":"2017-04-12", "WRT_USERID":"관리자", "WRT_DTHMS":"2017-04-12"};
const depositist2 = {"idx":"182", "category":"언더코팅", "categoryCd":"under", "purchase":"시카코리아", "purchaseCd":"cika", "productImg" : "/images/icon_download.gif", 
                      "productCd":"NT-500", "productNm":"멀티실런트", "ibchul":"출고", "ibchulCd":"out", "price":"9600", "quantity":"10", "memo":"메모란 입니다.",
                      "UPDATE_DTHMS":"2017-04-12", "WRT_USERID":"관리자", "WRT_DTHMS":"2017-04-12"};

const depositArray = new Array(depositist1, depositist2);
const jsonString = JSON.stringify(depositArray);
const jsonData = JSON.parse(jsonString);

$(document).ready(function() {

  setDepositList();
  
  $( "#UPDATE_DTHMS, #START_DT, #END_DT" ).datepicker({
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
setDepositList = () =>{
  let historyDataText = '';

  if(jsonData.length > 0){
      jsonData.map((data)=>{
        console.log(data);
        historyDataText += "<tr style='cursor : pointer' onclick='showUpdateModal("+JSON.stringify(data)+")'>";
        historyDataText += "<td class='text-center'>"+data.idx+"</td>";
        historyDataText += "<td class='text-center'>"+data.category+"</td>";
        historyDataText += "<td class='text-center'>"+data.purchase+"</td>";
        historyDataText += "<td class='text-center'><img src='"+data.productImg+"'></td>";
        historyDataText += "<td class='text-center'>"+data.productCd+"</td>";
        historyDataText += "<td class='text-center'>"+data.productNm+"</td>";
        historyDataText += "<td class='text-center'>"+data.ibchul+"</td>";
        historyDataText += "<td class='text-center'>"+data.price+"</td>";
        historyDataText += "<td class='text-center'>"+data.quantity+"</td>";
        historyDataText += "<td class='text-center'>"+data.memo+"</td>";
        historyDataText += "<td class='text-center'>"+data.UPDATE_DTHMS+"</td>";
        historyDataText += "<td class='text-center'>"+data.WRT_USERID+"</td>";
        historyDataText += "<td class='text-center'>"+data.WRT_DTHMS+"</td>";
        historyDataText += "</tr>"
      });
  }else{
      historyDataText = "<tr><td class='text-center' colspan='13'>정보가 존재하지 않습니다.</td></tr>";
  }
  
  console.log(historyDataText);
  $('#depositList').html(historyDataText);
}
   

  /** 업데이트 모달창일경우 */
  showUpdateModal = (data) => {
    $('#btnDelete').show();
    $('#submitBtn').html('수정');
    $('#submitBtn').attr('onclick', 'updateData()');

    $('#btnDelete').val();

    $('#purchaseCd').val(data.purchaseCd);
    $('#productCd').val(data.productCd);
    $('#ibchulCd').val(data.ibchulCd);
    $('#price').val(data.price);
    $('#quantity').val(data.quantity);
    $('#UPDATE_DTHMS').val(data.UPDATE_DTHMS);
    $('#memo').val(data.memo);

    $('#add-new').modal();
  }

  /* 등록 모달창*/ 
  showAddModal = () => {
    $('#btnDelete').hide();
    $('#submitBtn').html('등록');
    $('#submitBtn').attr('onclick', 'addData()');

    $('#purchaseCd').val('');
    $('#productCd').val('');
    $('#ibchulCd').val('');
    $('#price').val('');
    $('#quantity').val('');
    $('#UPDATE_DTHMS').val('');
    $('#memo').val('');

    $('#add-new').modal();
  }
