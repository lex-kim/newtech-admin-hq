const pageName = "gwMngt.html";
const ajaxUrl = 'act/gwFAO_act.php';
let CURR_PAGE = 1;

$(document).ready(function() {
    getDivisionOption('CSD_ID_SEARCH', true);                   // 지역구분
    setCRRSelect('CRR_ID_SEARCH', true);
    getRefOptions(GET_REF_CONTRACT_TYPE, 'RCT_ID_SERACH', false, false);                       // 거래여부  

    const tdLength = $('table tr th').length;
    setInitTableData(tdLength);

    /** 검색버튼 */
    $('#searchForm').submit(function(e){
        e.preventDefault();

        if(isSearchValidate()){
          getList(1);
        }
       
    });

    /**수정 */
    $('#editForm').submit(function(e){
        e.preventDefault();

        if(isValidate()){
          confirmSwalTwoButton('수정 하시겠습니까?','확인', '취소', false ,()=>updateData());
        }
       
    });

    $('#add-new').on('hide.bs.modal', function (event) {
        $('#MGG_ID').val('');
    });

    $('#CSD_ID_SEARCH').change(()=>{
        if($('#CSD_ID_SEARCH').selectpicker('val') != null){
            setCRRSelect('CRR_ID_SEARCH', true);
        }else{
            $('#CSD_ID').selectpicker('val' , '');
            $('#CSD_ID').selectpicker('refresh');
            setCRRSelect('CRR_ID_SEARCH', true);

        }
    });

    /**
     * 직원 성함/연락처 선택 변경시 이벤트 조절 및 placeholder 조절
     */
    $('#MGG_NM_TEL_SERACH').change(()=>{
        $('#MGG_NM_TEL_INPUT').val('');
        if($('#MGG_NM_TEL_SERACH').val() == 'TEL'){
          $('#MGG_NM_TEL_INPUT').attr("placeholder","연락처를 -없이 입력하세요.");
          $('#MGG_NM_TEL_INPUT').attr("onkeyup","isNumber(event)");
          $('#MGG_NM_TEL_INPUT').attr("maxlength","11");
        }else{
          $('#MGG_NM_TEL_INPUT').attr("placeholder","성명을 입력하세요.");
          $('#MGG_NM_TEL_INPUT').attr("onkeyup","");
          $('#MGG_NM_TEL_INPUT').attr("maxlength","");
        }
    });
    
    
});

excelDownload = () => {
  location.href="gwFaoExcel.html?"+$("#searchForm").serialize()+"&REQ_MODE="+CASE_TOTAL_LIST;
}

setCRRSelect = (targetID, isMultiSelector, isTotal) => {
  getCRRInfo($('#CSD_ID_SEARCH').selectpicker('val'), isTotal).then((result)=>{
      console.log(result);
      if(result.status == OK){
          const dataArray = result.data;
          if(dataArray.length > 0){
              let optionText = '';
      
              dataArray.map((item, index)=>{
                  let ITEM_ID = item.CRR_1_ID+","+item.CRR_2_ID+","+item.CRR_3_ID;
                  optionText += '<option value="'+ITEM_ID+'">'+item.CRR_NM+'</option>';
              });
  
              $('#'+targetID).html(optionText);
              
              if(isMultiSelector){
                  $('#'+targetID).selectpicker('refresh');
              }
          }else{
              $('#'+targetID).html('');
              
              if(isMultiSelector){
                  $('#'+targetID).selectpicker('refresh');
              }
          }
      }    
  }); 
}

/**
 * 수정 모달창
 * @param {data : 클릭된 해당 데이터}
 */
showUpdateModal = (data) => {
  $('#MGG_OFFICE_TEL').val(data.MGG_OFFICE_TEL);
  $('#MGG_OFFICE_FAX').val(data.MGG_OFFICE_FAX);
  $('#MGG_CEO_NM_AES').val(data.MGG_CEO_NM_AES);
  $('#MGG_CEO_TEL_AES').val(data.MGG_CEO_TEL_AES);
  $('#MGG_KEYMAN_NM_AES').val(data.MGG_KEYMAN_NM_AES);
  $('#MGG_KEYMAN_TEL_AES').val(data.MGG_KEYMAN_TEL_AES);
  $('#MGG_FACTORY_NM_AES').val(data.MGG_FACTORY_NM_AES);
  $('#MGG_FACTORY_TEL_AES').val(data.MGG_FACTORY_TEL_AES);
  $('#MGG_CLAIM_NM_AES').val(data.MGG_CLAIM_NM_AES);
  $('#MGG_CLAIM_TEL_AES').val(data.MGG_CLAIM_TEL_AES);
  $('#MGG_PAINTER_NM_AES').val(data.MGG_PAINTER_NM_AES);
  $('#MGG_PAINTER_TEL_AES').val(data.MGG_PAINTER_TEL_AES);
  $('#MGG_METAL_NM_AES').val(data.MGG_METAL_NM_AES);
  $('#MGG_METAL_TEL_AES').val(data.MGG_METAL_TEL_AES);
  $('#MGG_ID').val(data.MGG_ID);
  $('#add-new').modal();
} 


/**
 * 검색시 validateion
 */
isSearchValidate = () => {
  if($('#MGG_NM_TEL_INPUT').val().trim() != '' && $('#MGG_NM_TEL_SERACH').val()==''){
    alert("검색할 조건을 선택해주세요.");
    $('#MGG_NM_TEL_SERACH').focus();

    return false;
  }else{
    return true;
  }
}

/**
 * 수정클릭했을 경우 번호양식이 올바르게 입력되었는지 체크
 */
isValidate = () => {
  let isPhoneValidate = true;
  $('.phone').each(function(){
      if($(this).val().trim() != ''){
          if(!validatePhone($(this).val())){
              isPhoneValidate = false;
              alert('번호 양식에 올바르지 않습니다.');
              $(this).focus();
              return false;
          }
      }
  });

  return isPhoneValidate;
}

updateData = () => {
  let form = $("#editForm")[0];
  let formData = new FormData(form);
  formData.append('REQ_MODE', CASE_UPDATE);

  callFormAjax(ajaxUrl, 'POST', formData).then((result)=>{
      if(isJson(result)){
          const data = JSON.parse(result);
          if(data.status == OK){
            confirmSwalOneButton("수정에 성공하였습니다.",'확인',()=>{
              getList(1);
              $('#add-new').modal('hide');
            });
        }else{
            alert("서버와의 연결이 원활하지 않습니다.");
        }
      }else{
        alert(ERROR_MSG_1200);
      }
  });
}

/** 리스트 */
getList = (currentPage) => {
  CURR_PAGE = (currentPage == undefined) ? CURR_PAGE : currentPage;
  let form = $("#searchForm")[0];
  let formData = new FormData(form);
  formData.append('REQ_MODE', CASE_LIST);
  formData.append('PER_PAGE', $("#perPage").val());
  formData.append('CURR_PAGE', CURR_PAGE);

  callFormAjax(ajaxUrl, 'POST', formData).then((result)=>{
    if(isJson(result)){
      const data = JSON.parse(result);
      if(data.status == OK){console.log("1123123123123==>",data);
          const item = data.data;
          setTable(item[1], item[0], CURR_PAGE);
      }else{
          setHaveNoDataTable();
      }
    }else{
     
    }
      
  });  
}