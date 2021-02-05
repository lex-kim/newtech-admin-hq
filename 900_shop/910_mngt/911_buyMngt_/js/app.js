let CURR_PAGE = 1;

$(document).ready(function() {
  const tdLength = $('table th').length;
  setInitTableData(tdLength, 'buyMngtList');

  $('#editForm').submit(function(e){
      e.preventDefault();
      setData();
  });

  $('#searchForm').submit(function(e){
      e.preventDefault();
      getList(1);
  });

  /* datepicker 설정 */
  $(function() {
      $( "#START_DT" ).datepicker({
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
    $(function() {
      $( "#END_DT" ).datepicker({
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

    $('#SEARCH_PAYMENT_DATE, #GW_PAYMENT_DT').datepicker({
      dateFormat : 'yy-mm-dd',
    });
   
});

/** 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
  const tdLength = $('table th').length;
  const tableText = "<tr><td class='text-center' colspan='"+tdLength+"'>조회된 데이터가 존재하지 않습니다.</tr></tr>";
  $('.pagination').html('');
  $('#buyMngtList').html(tableText);
}
  
/** 동적으로 history table을 만든다 */
setBuyMngtList = () =>{
  let historyDataText = '';

  if(jsonData.length > 0){
      jsonData.map((data)=>{
        historyDataText += "<tr style='cursor : pointer' onclick='showUpdateModal("+JSON.stringify(data)+")'>";
        historyDataText += "<td class='text-center'>"+data.idx+"</td>";
        historyDataText += "<td class='text-center'>"+data.deal+"</td>";
        historyDataText += "<td class='text-center'>"+data.businessNum+"</td>";
        historyDataText += "<td class='text-center'>"+data.company+"</td>";
        historyDataText += "<td class='text-center'>"+data.store_business+"</td>";
        historyDataText += "<td class='text-center'>"+data.store_event+"</td>";
        historyDataText += "<td class='text-center'>"+data.address+"</td>";
        historyDataText += "<td class='text-center'>"+data.phoneNum+"</td>";
        historyDataText += "<td class='text-center'>"+data.faxNum+"</td>";
        historyDataText += "<td class='text-center'>"+data.ceoNm+"</td>";
        historyDataText += "<td class='text-center'>"+data.ceoPhone+"</td>";
        historyDataText += "<td class='text-center'>"+data.faoNm+"</td>";
        historyDataText += "<td class='text-center'>"+data.faoPhone+"</td>";
        historyDataText += "<td class='text-center'>"+data.faoTel+"</td>";
        historyDataText += "<td class='text-center'>"+data.faoEmail+"</td>";
        historyDataText += "<td class='text-center'>"+data.siteUrl+"</td>";
        historyDataText += "<td class='text-left'>"+data.goods+"</td>";
        historyDataText += "<td class='text-left'>"+data.memo+"</td>";
        historyDataText += "<td class='text-center'>"+data.dealState+"</td>";
        historyDataText += "<td class='text-center'>"+data.deal_DT+"</td>";
        historyDataText += "<td class='text-center'>"+data.dealBankNm+"</td>";
        historyDataText += "<td class='text-center'>"+data.bankNum+"</td>";
        historyDataText += "<td class='text-center'>"+data.bankUserNm+"</td>";
        if(data.gwPaper_YN == YN_Y){
          historyDataText += "<td class='text-center'>"+"<img src='/images/icon_download.gif'>"+"</td>";
        }else{
          historyDataText += "<td class='text-center'></td>";
        }
        if(data.bank_YN == YN_Y){
          historyDataText += "<td class='text-center'>"+"<img src='/images/icon_download.gif'>"+"</td>";
        }else{
          historyDataText += "<td class='text-center'></td>";
        }
        historyDataText += "<td class='text-center'>"+data.WRT_USERID+"</td>";
        historyDataText += "<td class='text-center'>"+data.WRT_DTHMS+"</td>";
        historyDataText += "</tr>"
      });
  }else{
      historyDataText = "<tr><td class='text-center' colspan='17'>보상팀관리 정보가 존재하지 않습니다.</td></tr>";
  }
  
  console.log(historyDataText);
  $('#buyMngtList').html(historyDataText);
}

/**
 * 주문상품 리스트 테이블 동적생성
 * @param {dataList : 동적리스트}
 * @param {totalListCount : 총 몇페이지}
 * @param {cPage : 현재페이지}
 */
setTable = (dataList, totalListCount, cPage) => {
  let html = "";
  dataList.map((data, idx)=>{
      html += "<tr style='cursor : pointer' onclick='showUpdateModal("+JSON.stringify(data)+")'>";
      html += "<td class='text-center'>"+data.idx+"</td>";
      html += "<td class='text-center'>"+data.deal+"</td>";
      html += "<td class='text-center'>"+data.businessNum+"</td>";
      html += "<td class='text-center'>"+data.company+"</td>";
      html += "<td class='text-center'>"+data.store_business+"</td>";
      html += "<td class='text-center'>"+data.store_event+"</td>";
      html += "<td class='text-center'>"+data.address+"</td>";
      html += "<td class='text-center'>"+data.phoneNum+"</td>";
      html += "<td class='text-center'>"+data.faxNum+"</td>";
      html += "<td class='text-center'>"+data.ceoNm+"</td>";
      html += "<td class='text-center'>"+data.ceoPhone+"</td>";
      html += "<td class='text-center'>"+data.faoNm+"</td>";
      html += "<td class='text-center'>"+data.faoPhone+"</td>";
      html += "<td class='text-center'>"+data.faoTel+"</td>";
      html += "<td class='text-center'>"+data.faoEmail+"</td>";
      html += "<td class='text-center'>"+data.siteUrl+"</td>";
      html += "<td class='text-left'>"+data.goods+"</td>";
      html += "<td class='text-left'>"+data.memo+"</td>";
      html += "<td class='text-center'>"+data.dealState+"</td>";
      html += "<td class='text-center'>"+data.deal_DT+"</td>";
      html += "<td class='text-center'>"+data.dealBankNm+"</td>";
      html += "<td class='text-center'>"+data.bankNum+"</td>";
      html += "<td class='text-center'>"+data.bankUserNm+"</td>";
      if(data.gwPaper_YN == YN_Y){
        html += "<td class='text-center'>"+"<img src='/images/icon_download.gif'>"+"</td>";
      }else{
        html += "<td class='text-center'></td>";
      }
      if(data.bank_YN == YN_Y){
        html += "<td class='text-center'>"+"<img src='/images/icon_download.gif'>"+"</td>";
      }else{
        html += "<td class='text-center'></td>";
      }
      html += "<td class='text-center'>"+data.WRT_USERID+"</td>";
      html += "<td class='text-center'>"+data.WRT_DTHMS+"</td>";
      html += "</tr>"
  });
  paging(totalListCount, cPage, 'getList'); 
  $('#buyMngtList').html(html);
}

getList = (page) => {
  CURR_PAGE = (page == undefined ) ? CURR_PAGE : page;
  let form = $("#searchForm")[0];
  let formData = new FormData(form);
  formData.append('REQ_MODE', CASE_LIST);
  formData.append('PER_PAGE', $("#perPage").val());
  formData.append('CURR_PAGE', CURR_PAGE);

  callFormAjax("./controller/AjaxController.php", 'POST', formData).then((result)=>{
      if(isJson(result)){
        const res = JSON.parse(result);
        if(res.status == OK){
          if(res.data == HAVE_NO_DATA || res.data.length == 0){
            setHaveNoDataTable();
          }else{
            setTable(res.data, res.totalCnt, CURR_PAGE);
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
      DEL_ID :  delID
  };
  callAjax(DIVISION_URL,'POST', data).then((result)=>{
      if(isJson(result)){
        const res = JSON.parse(result);
        if(res.status == OK){
           alert("삭제완료");
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

  callFormAjax(ERP_CONTROLLER_URL, 'POST', formData).then((result)=>{
    if(isJson(result)){
      const res = JSON.parse(result);
      if(res.status == OK){
        if(res.data == HAVE_NO_DATA || res.data.length == 0){
          setHaveNoDataTable();
        }else{
          alert("등록완료");
          // setTable(res.data, res.totalCnt, cPage);
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
  window.open('/900_shop/910_mngt/911_buyMngt/write/'+data.idx, 
  'buyMngtModal', 
  'width=700,height=795,scrollbars=yes'); 
}

onClickEdit = () =>{
  window.open('/900_shop/910_mngt/911_buyMngt/write', 
  'buyMngtModal', 
  'width=700,height=795,scrollbars=yes'); 
}