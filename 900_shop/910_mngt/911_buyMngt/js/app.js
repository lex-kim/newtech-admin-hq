let CURR_PAGE = 1;

$(document).ready(function() {
  const tdLength = $('table th').length;
  setInitTableData(tdLength, 'buyMngtList');

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

  $('#SEARCH_PAYMENT_DATE').datepicker({
    dateFormat : 'yy-mm-dd',
  });
   
});

/**
 * 리스트에 결제기일 세팅
 */
getPaymentDueDate = (payDate) => {
  if(payDate == 0){
      return "";  
  }else{
    return payDate+"일";
  }
}

/** 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
  const tdLength = $('table th').length;
  const tableText = "<tr><td class='text-center' colspan='"+tdLength+"'>조회된 데이터가 존재하지 않습니다.</tr></tr>";
  $('.pagination').html('');
  $('#buyMngtList').html(tableText);
}
  

/**
 * 주문상품 리스트 테이블 동적생성
 * @param {dataList : 동적리스트}
 * @param {totalListCount : 총 몇페이지}
 * @param {cPage : 현재페이지}
 */
setTable = (dataList, totalListCount, cPage) => {
  let html = "";
  dataList.map((data)=>{
      html += "<tr>";
      html += "<td class='text-center'>"+data.issueCount+"</td>";
      html += "<td class='text-center'>"+data.bizType+"</td>";//업체구분
      html += "<td class='text-center'>"+data.bizTaxType+"</td>";//과세구분
      html += "<td class='text-center'>"+data.partnerType+"</td>";
      html += "<td class='text-center'>"+bizNoFormat(data.companyRegID)+"</td>";
      html += "<td class='text-left'>"+data.companyName+"</td>";
      html += "<td class='text-center'>"+data.companyBiz+"</td>";
      html += "<td class='text-center'>"+data.companyPart+"</td>";
      html += "<td class='text-left'>"+(data.Address1+" "+data.Address2)+"</td>";
      html += "<td class='text-center'>"+phoneFormat(data.phoneNum)+"</td>";
      html += "<td class='text-center'>"+phoneFormat(data.faxNum)+"</td>";
      html += "<td class='text-center'>"+data.ceoName+"</td>";
      html += "<td class='text-center'>"+phoneFormat(data.ceoPhoneNum)+"</td>";
      html += "<td class='text-center'>"+data.chargeName+"</td>";
      html += "<td class='text-center'>"+phoneFormat(data.chargePhoneNum)+"</td>";
      html += "<td class='text-center'>"+phoneFormat(data.chargeDirectPhoneNum)+"</td>";
      html += "<td class='text-left'>"+data.chargeEmail+"</td>";
      html += "<td class='text-left'>"+data.homepage+"</td>";
      html += "<td class='text-left'>"+data.mainGoods+"</td>";
      html += "<td class='text-left'>"+data.memo+"</td>";
      html += "<td class='text-center'>"+data.contractStatus+"</td>";
      html += "<td class='text-center'>"+getPaymentDueDate(data.paymentDueDate)+"</td>";
      html += "<td class='text-center'>"+(empty(data.bankName)?"-":data.bankName)+"</td>";
      html += "<td class='text-center'>"+data.bankNum+"</td>";
      html += "<td class='text-center'>"+data.bankOwnName+"</td>";

      if(data.companyCertOriginFilename){
        html += "<td class='text-center'>"+"<a target='_blank' href='"+data.companyCertURI+"'><img src='/images/icon_download.gif'></a>"+"</td>";
      }else{
        html += "<td class='text-center'></td>";
      }
      if(data.companyBankOriginFilename){
        html += "<td class='text-center'>"+"<a target='_blank' href='"+data.companyBankURI+"'><img src='/images/icon_download.gif'></a>"+"</td>";
      }else{
        html += "<td class='text-center'></td>";
      }

      if(data.autoOrderYN == YN_Y){                                   //자동발주
        html += "<td class='text-center'>자동</td>";
      }else{
        html += "<td class='text-center'>수동</td>";
      }
      html += "<td class='text-center'>"+data.autoOrderMethod+"</td>";//자동발주방법
      html += "<td class='text-center'>"+data.writeUser+"</td>";
      html += "<td class='text-center'>"+data.writeDatetime+"</td>";
      html += "<td class='text-center'><button type='button' class='btn btn-default' onclick='showUpdateModal("+JSON.stringify(data)+")'>수정</button></td>";
      html += "</tr>"
  });
  paging(totalListCount, cPage, 'getList'); 
  $('#buyMngtList').html(html);
}

excelDownload = () => {
  location.href="buyMngtExcel.php?"+$("#searchForm").serialize()+"&REQ_MODE="+CASE_TOTAL_LIST;
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
      partnerID :  delID
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

  callFormAjax(ERP_CONTROLLER_URL, 'POST', formData).then((result)=>{
    if(isJson(result)){
      const res = JSON.parse(result);
      if(res.status == OK){
        if($('#PARTNER_ID').val() != ""){
          alert("수정이 완료되었습니다.");
        }else{
          alert("등록이 완료되었습니다.");
        }

        window.close();
        if($('#PARTNER_ID').val() != ""){
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

function onchangePaymentText(){
  const inputValue = Number($('#GW_PAYMENT_DT').val());

  if(inputValue < 1 || inputValue > 31){
    alert("1 ~ 31사이만 입력이 가능합니다.");
    $('#GW_PAYMENT_DT').val("");
    $('#GW_PAYMENT_DT').focus();
  }
}

function onchangePassword(){
  if(empty($('#partnerPasswd').val())){
    $('.pwdRequired').prop('required', false);
    $('#partnerPasswdConfirm').val("");
    $('#confirmPasswordContainer').hide();
    $('.required').hide();
  }else{
    $('.pwdRequired').prop('required', true);
    $('#confirmPasswordContainer').show();
    $('.required').show();
  }
}

/** 수정 팝업창 */
showUpdateModal = (data) => {
  const url = '/900_shop/910_mngt/911_buyMngt/buyMngtModal.php?partnerID='+data.partnerID;
  openWindowPopup(url, 'buyMngtModal', 1000, 795);
}

// 등록 팝업창
onClickEdit = () =>{
  const url = '/900_shop/910_mngt/911_buyMngt/buyMngtModal.php';
  openWindowPopup(url, 'buyMngtModal', 1000, 795);
}