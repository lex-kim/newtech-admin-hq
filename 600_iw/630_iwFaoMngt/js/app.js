const ajaxUrl = 'act/iwFaoMngt_act.php';
const FAOM_SELECT_PHONE = '110';  // 검색에서 보험사정보 select에서 전화번호 관련 옵션을 선택했을 때
const FAOM_SELECT_TEXT = '100';   // 검색에서 보험사정보 select에서 텍스트 관련 옵션을 선택했을 때
let CURR_PAGE = 1; 
let registPopupWidth = 460;
let registPopupHeight = 580;

$(document).ready(function() {
    $('#indicator').hide();
    $( "#START_DT" ).datepicker(
          getDatePickerSetting('END_DT','minDate')
    );
    $( "#END_DT" ).datepicker(
        getDatePickerSetting('START_DT','maxDate')
    );

    getInsuInfo('MII_ID_SEARCH', false, '=보험사=');    // 보험사 정보

    /** 검색버튼 */
    $('#searchForm').submit(function(e){
        e.preventDefault();

        if(isSearchValidate()){
          getList(1);
        }
        
      
    });

    /** 보험사 정보 바뀔 때 마다 번호양식인지 텍스트양식이니즐 구별해줘서 알맞은 이벤트걸어주기 */
    $('#NIC_AES_SEARCH').change(function(){
        if($(this).val() != ''){
          const selectOptionClass = $('#NIC_AES_SEARCH option:selected').attr('class');
          if(selectOptionClass == FAOM_SELECT_PHONE){
            $('#NIC_AES_INPUT_SEARCH').attr("placeholder","- 없이 입력해주세요.");
            $('#NIC_AES_INPUT_SEARCH').attr("onkeyup","isNumber(event)");
            $('#NIC_AES_INPUT_SEARCH').attr("maxlength","");
          }else{
            $('#NIC_AES_INPUT_SEARCH').attr("placeholder","입력해주세요.");
            $('#NIC_AES_INPUT_SEARCH').attr("onkeyup","");
            $('#NIC_AES_INPUT_SEARCH').attr("maxlength","");
          }
        }else{
          $('#NIC_AES_INPUT_SEARCH').val('');
        }
        
    });

    const tdLength = $('table tr th').length;
    setInitTableData(tdLength);

    let position =   getPopupPosition(registPopupWidth, registPopupHeight);
    popupX = position.popupX;
    popupY = position.popupY;
   
});

isSearchValidate = () => {
    if($('#NIC_AES_INPUT_SEARCH').val() != '' && $('#NIC_AES_SEARCH').val() == ''){
        alert('검색할 보험사 정보를 선택해주세요');
        $('#NIC_AES_SEARCH').focus();
        return false;
    }else{
      return true;
    }
}

excelDownload = () => {
  location.href="iwFaoExcel.html?"+$("#searchForm").serialize()+"&REQ_MODE="+CASE_TOTAL_LIST;
}

/** 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
  const tdLength = $('table tr th').length;
  const tableText = "<tr><td class='text-center' colspan='"+tdLength+"'>조회된 데이터가 존재하지 않습니다.</tr></tr>";
  $('.pagination').html('');
  $('#dataTable').html(tableText);
}


/** 리스트 테이블 동적으로 생성 
 * @param {dataArray : 리스트 데이터, currentPage : 현재페이지(페이징을 위해)} 
*/
setTable = (totalListCount, dataArray, currentPage) => {
    let tableText = '';
    
    dataArray.map((data, index)=>{
        tableText += "<tr>";
        tableText += "<td class='text-center'>"+data.IDX+"</td>";
        tableText += "<td class='text-center'>"+data.MII_NM+"</td>";
        tableText += "<td class='text-center'>"+data.MIT_NM+"</td>";
        tableText += "<td class='text-center'>"+data.WRT_DTHMS+"</td>";
        tableText += "<td class='text-center'>"+data.NIC_NM_AES+"</td>";
        tableText += "<td class='text-center'>"+phoneFormat(data.NIC_FAX_AES)+"</td>";
        tableText += "<td class='text-center'>"+phoneFormat(data.NIC_TEL1_AES)+"</td>"
        tableText += "<td class='text-center'>"+phoneFormat(data.NIC_TEL2_AES)+"</td>";
        tableText += "<td class='text-center'>"+data.NIC_EMAIL_AES+"</td>";
        tableText += "<td class='text-left'>"+data.NIC_NOUSE_REASON+"</td>";
        tableText += "<td class='text-left'>"+data.NIC_MEMO+"</td>";
        if($("#AUTH_UPDATE").val() == YN_Y){
          tableText += "<td class='text-center'><button type='button' onclick='showUpdatePopup("+JSON.stringify(data)+")' class='btn btn-default'>수정</button></td>";
        }
        tableText += "</tr>";
        
    });
    paging(totalListCount, currentPage, 'getList'); 
    $('#dataTable').html(tableText);
}

/** 리스트 */
getList = (currentPage) => {
  CURR_PAGE = (currentPage==undefined) ? CURR_PAGE : currentPage;
  let form = $("#searchForm")[0];
  let formData = new FormData(form);
  formData.append('REQ_MODE', CASE_LIST);
  formData.append('PER_PAGE', $("#perPage").val());
  formData.append('CURR_PAGE', CURR_PAGE);

  callFormAjax(ajaxUrl, 'POST', formData).then((result)=>{
    if(isJson(result)){
      const data = JSON.parse(result);
      if(data.status == OK){
          const item = data.data;
          setTable(item[1], item[0], CURR_PAGE);
      }else{
          setHaveNoDataTable();
      }
    }else{
      alert(ERROR_MSG_1200);
    }
     
  });  
}



/**
 * 수정 모달창
 * @param {data : 클릭된 해당 데이터}
 */
showUpdatePopup = (data) => {
  console.log('showUpdatePopup>>>',data);
  window.open('', 
  'iwFaoMngtModalwindow', 
  'width='+registPopupWidth+',height='+registPopupHeight+',left='+ popupX + ', top='+ popupY); 

  const paramForm = $('#updateInfoForm')[0];
  paramForm.action = '/600_iw/630_iwFaoMngt/iwFaoMngtModal.html';
  paramForm.target = 'iwFaoMngtModalwindow';
  paramForm.method = 'POST';

  let dataHtml =  '<input type="hidden" id="MII_ID" name="MII_ID" value="'+data.MII_ID+'">';
  dataHtml +=  '<input type="hidden" id="MIT_ID" name="MIT_ID" value="'+nullChk(data.MIT_ID,'')+'">';
  dataHtml +=  '<input type="hidden" id="MIT_NM" name="MIT_NM" value="'+nullChk(data.MIT_NM,'')+'">';
  dataHtml +=  '<input type="hidden" id="NIC_NM_AES" name="NIC_NM_AES" value='+data.NIC_NM_AES+'>';
  dataHtml +=  '<input type="hidden" id="NIC_FAX_AES" name="NIC_FAX_AES" value='+phoneFormat(data.NIC_FAX_AES)+'>';
  dataHtml +=  '<input type="hidden" id="NIC_TEL1_AES" name="NIC_TEL1_AES" value='+phoneFormat(data.NIC_TEL1_AES)+'>';
  dataHtml +=  '<input type="hidden" id="NIC_TEL2_AES" name="NIC_TEL2_AES" value='+phoneFormat(data.NIC_TEL2_AES)+'>';
  dataHtml +=  '<input type="hidden" id="NIC_EMAIL_AES" name="NIC_EMAIL_AES" value="'+data.NIC_EMAIL_AES+'">';
  dataHtml +=  '<input type="hidden" id="NIC_NOUSE_REASON" name="NIC_NOUSE_REASON" value="'+data.NIC_NOUSE_REASON+'">';
  dataHtml +=  '<input type="hidden" id="NIC_MEMO" name="NIC_MEMO" value="'+data.NIC_MEMO+'">';
  dataHtml +=  '<input type="hidden" id="NIC_ID" name="NIC_ID" value="'+data.NIC_ID+'">';

  $('#updateInfoForm').html(dataHtml);

  paramForm.submit();
  
} 



/**
 * 보상담당자 신규등록 팝업
 */
openNewPopup = () => {
    window.open('/600_iw/630_iwFaoMngt/iwFaoMngtModal.html', 
  'iwFaoMngtModalwindow', 
  'width='+registPopupWidth+',height='+registPopupHeight+',left='+ popupX + ', top='+ popupY); 
}

/**
 * 보상담당자 신규등록 후 콜백함수
 */
callbackAddData = () => {
  getList();
}