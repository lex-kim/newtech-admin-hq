
let CURR_PAGE = 1;
const DUPLICATE_ID_TEXT  = "중복된 아이디가 존재합니다.";
const AVAILABLE_ID_TEXT  = "사용가능한 아이디 입니다.";
let isAvailabledID       = false;                   // 사용가능한 아이디 인지..

$(document).ready(function() {
  if($('#pageIndicator').is(":visible")){
    $('#pageIndicator').hide();
  }
  const tdLength = $('table th').length;
  setInitTableData(tdLength);

  $('#searchForm').submit(function(e){
      e.preventDefault();
      getList(1);
  });
  
  $( "#startDate, #endDate" ).datepicker(
      getDatePickerSetting('END_DT','minDate')
  );

  $('.date').change(function(e){
    var date_pattern = /^(19|20)\d{2}-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[0-1])$/; 
    if(!date_pattern .test($(this).val())){
      alert("yyyy-mm-dd 양식에 올바르지 않습니다.");
      $(this).val("");
      $(this).focus();
      return;
    }
  });

  $('.modal').on('hidden.bs.modal', function () {
    // $('.modal input, select').val("");
  })
});

excelDownload = () => {
  location.href="gwMngtExcel.php?"+$("#searchForm").serialize()+"&REQ_MODE="+CASE_TOTAL_LIST;
}

/** 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
  const tdLength = $('table th').length;
  const tableText = "<tr><td class='text-center' colspan='"+tdLength+"'>조회된 데이터가 존재하지 않습니다.</tr></tr>";
  $('.pagination').html('');
  $('#dataTable').html(tableText);
}
  
/** 동적으로 history table을 만든다 */
setTable = (dataList, totalListCount, cPage) => {
  let historyDataText = '';
  dataList.map((data, index)=>{
    historyDataText += "<tr>";
    historyDataText += "<td class='text-center'>"+data.issueCount+"</td>";
    historyDataText += "<td class='text-center'>"+data.garageID+"</td>";
    historyDataText += "<td class='text-center'>"+(empty(data.divisionName) ? "-" : data.divisionName )+"</td>";
    historyDataText += "<td class='text-center'>"+data.rankName+"</td>";
    historyDataText += "<td class='text-center'>"+data.loginID+"</td>";
    historyDataText += "<td class='text-center'>"+bizNoFormat(data.companyRegID)+"</td>";
    historyDataText += "<td class='text-left'>"+data.garageName+"</td>";
    historyDataText += "<td class='text-left'>"+data.address1+" "+data.address2+"</td>";
    historyDataText += "<td class='text-center'>"+phoneFormat(data.phoneNum)+"</td>";
    historyDataText += "<td class='text-center'>"+phoneFormat(data.faxNum)+"</td>";
    historyDataText += "<td class='text-left'>"+data.companyEmail+"</td>";
    historyDataText += "<td class='text-center'>"+data.ceoName+"</td>";
    historyDataText += "<td class='text-center'>"+phoneFormat(data.ceoPhoneNum)+"</td>";
    historyDataText += "<td class='text-center'>"+data.chargeName+"</td>";
    historyDataText += "<td class='text-center'>"+phoneFormat(data.chargePhoneNum)+"</td>";
    historyDataText += "<td class='text-center'>"+data.contractStatusName+"<br>";
    historyDataText += "<button type='button' class='btn btn-warning' onclick='openGrantPopup(\""+data.garageID+"\")'>변경</button>";
    historyDataText += "</td>";
    historyDataText += "<td class='text-center'>"+(empty(data.contractStartDate) ? "-" : data.contractStartDate )+"</td>";
    historyDataText += "<td class='text-center'>"+(empty(data.imgURI) ? "-" : "<a target='_blank' href='"+data.imgURI+"'><img src='/images/icon_download.gif'></a>" )+"</td>";
    historyDataText += "<td class='text-center'>"+data.writeUser+"</td>";
    historyDataText += "<td class='text-center'>"+data.writeDatetime+"</td>";
    historyDataText += "<td class='text-center'>";
    historyDataText += "<button type='button' class='btn btn-default' onclick='openEditPopup(\""+data.garageID+"\")'>수정</button>";
    historyDataText += "</td>";
    historyDataText += "</tr>"
  });
  paging(totalListCount, cPage, 'getList'); 
  $('#dataTable').html(historyDataText);
}


getList = (page) => {
  var time = 1;
  var ajaxTimeInterver = setInterval(function(){
      time += 1;
      if(time > 2){
        $('#dataTable').hide();
        $('#indicator').show();
        $('#indicatorTable').show();

        clearInterval(ajaxTimeInterver);
      }
  },150);

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
                  setTable(res.data.data, res.data.totalCount, CURR_PAGE);
              } 
          }else{
               alert(ERROR_MSG_1100);
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

function setData(REQ_MODE) {
  let form = $("#garageForm")[0];
  let formData = new FormData(form);
  formData.append('REQ_MODE', REQ_MODE);
  
  callFormAjax(ERP_CONTROLLER_URL, 'POST', formData).then((result)=>{
    if(isJson(result)){
      const res = JSON.parse(result);
      if(res.status == OK){
        let title = "작성";
        if(!empty($('#garageID').val())){
           title = "수정";
        }
        alert(title+" 되었습니다.");

        if(!empty(opener)){
          window.close();
          opener.window.getList();
        }else{
          getList();
        }
      }else{
        alert(empty(res.message) ? ERROR_MSG_1100 : res.message);
      }
    }else{
        alert(ERROR_MSG_1200);
    }
  });  
}


function delData(garageID){
  const data = {
    REQ_MODE: CASE_DELETE,
    garageID :  garageID,
  };
  callAjax(ERP_CONTROLLER_URL,'POST', data).then((result)=>{
    if(isJson(result)){
      const res = JSON.parse(result);
      if(res.status == OK){
        alert("삭제가 완료되었습니다.");
        
        if(!empty(opener)){
          window.close();
          opener.window.getList();
        }else{
          getList();
        }
      }else{
        alert((empty(res.message) ? ERROR_MSG_1100 : res.message));
      }
    }else{
      alert(ERROR_MSG_1200);
    }
  }); 
}

function updateData(garageID){
  let form = $("#grantForm")[0];
  let formData = new FormData(form);
  formData.append('REQ_MODE', CASE_UPDATE);
  formData.append('garageID', garageID);
  formData.append('isGrant', "Y");

  callFormAjax(ERP_CONTROLLER_URL, 'POST', formData).then((result)=>{
    if(isJson(result)){
      const res = JSON.parse(result);
      if(res.status == OK){
        alert("처리가 완료되었습니다.");
        
        if(!empty(opener)){
          window.close();
          opener.window.getList();
        }else{
          getList();
        }
      }else{
        alert(ERROR_MSG_1100);
      }
    }else{
        alert(ERROR_MSG_1200);
    }
  });  
}



function isCheckDuplicateUserID(){
  const data = {
      REQ_MODE : CASE_CONFLICT_CHECK,
      loginID : $('#MGG_USERID').val()
  }
  callAjax(ERP_CONTROLLER_URL, 'POST', data).then(function(result){
      let guideText = "";
      let guideTextColor = "";

      if(isJson(result)){
        const res = JSON.parse(result);
        if(res.status == OK){
          isAvailabledID = true;
          guideText = AVAILABLE_ID_TEXT;
          guideTextColor = "green";
        }else{
          isAvailabledID = false;
          guideText = DUPLICATE_ID_TEXT;
          guideTextColor = "red";
        }
      }else{
        alert(ERROR_MSG_1200);
        isAvailabledID = false;
        guideText = DUPLICATE_ID_TEXT;
        guideTextColor = "red";
      } 

      $('#guideUserIdText').html(guideText);
      $('#guideUserIdText').css('color',guideTextColor);
      $('#guideUserIdText').show();
  });
}

function onChangeUserID(){
  if(empty($('#garageID').val())){
    if(!empty($('#MGG_USERID').val().trim())){
      isCheckDuplicateUserID();
    }else{
      isAvailabledID = false;
      $('#guideUserIdText').html("&nbsp;");
    }
  }
}

function onclickDelBtn(garageID){
  confirmSwalTwoButton(
    '삭제하시겠습니까?','확인', '취소', true ,
    ()=>{delData(garageID)}, '확인을 누르시면 해당 건을 삭제합니다.'
  );
}

/** 취소 */
function onClose(){
  window.close();
}

function openGrantPopup(garageID){
  const url = 'confirmPopup.php?garageID='+garageID;
  openWindowPopup(url, 'orderListDetail', 400, 480);
}

function addStore(){
  const url = 'gwRegister.html';
  openWindowPopup(url, 'orderListDetail', 850, 750);
}

/* 수정 모달창*/ 
openEditPopup = (garageID) => {
  const url = 'gwRegister.html?garageID='+garageID;
  openWindowPopup(url, 'orderListDetail', 850, 750);
}