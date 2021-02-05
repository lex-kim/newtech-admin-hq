const pageName = "gwMngt.html";
const ajaxUrl = 'act/gwState_act.php';
const CONTRACT_STOP = 10620;   // 거래중단
let isValidate = true;         // 등록창에서 submit 진행여부를 결정하는 변수
let isClickUpdate = false;     // 수정진행하겠냐는 컨펌에 클릭여부
let CURR_PAGE = 1;

$(document).ready(function() { 
    setSelctOption();
    setTableTR();
    setInitTableData(70);

    $('#CSD_ID_SEARCH').change(()=>{
        if($('#CSD_ID_SEARCH').selectpicker('val') != null){
            setCRRSelect('CRR_ID_SEARCH', true);
        }else{
            $('#CSD_ID').selectpicker('val' , '');
            $('#CSD_ID').selectpicker('refresh');
            setCRRSelect('CRR_ID_SEARCH', true);

        }
    });

    $('#searchForm').submit(function(e){
        e.preventDefault();

        getList(1);
    });
});



/** 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
    const tableText = "<tr><td class='text-center' colspan='60'>조회요청한 데이터가 존재하지 않습니다.</td></tr>";
    $('.pagination').html('');
    $('#dataTable').html(tableText);
    $('#dataFooterTable').html('');
}

/** 검색 validate */
searchValidate = () => {
    if((($('#BIZ_START_DT_SEARCH').val() != '' || $('#BIZ_END_DT_SEARCH').val() !='')) && $('#MY_BIZ_DT_SEARCH').val() == ''){
        alert("검색 종류를 선택해주세요.");
        $('#MY_BIZ_DT_SEARCH').focus();

        return false;
    }else{
        return true;
    }
}


/** 주소 입력 및 거래기간선택 확인 
 * 번호양식 맞는지
*/
validation = () => {
    if($('#SELECT_CRR_ID').val() == ''){
        alert("지역검색을 입력후 선택해주셔야 됩니다.");
        isShowPopupBody();
        $('#CRR_ID').focus();

        return false;
    }else if($('#MGG_ZIP_OLD').val() == '' || $('#MGG_ADDRESS_OLD_1').val() == ''){
        alert("주소검색을 통해 구주소를 입력 선택해주세요.");
        isShowPopupBody();
        execPostCode('OLD');

        return false;
    }else if(($('#MGG_ZIP_OLD').val() != '' && $('#MGG_ADDRESS_OLD_1').val() != '') && $('#MGG_ADDRESS_OLD_2').val() == ''){
        alert("상세 구주소를 입력해주세요.");
        isShowPopupBody();
        $('#MGG_ADDRESS_OLD_2').focus();

        return false;
    }else if($('#MGG_ZIP_NEW').val() == '' || $('#MGG_ADDRESS_NEW_1').val() == ''){
        alert("주소검색을 통해 신주소를 입력 선택해주세요.");
        isShowPopupBody();
        execPostCode('NEW');

        return false;
    }else if(($('#MGG_ZIP_NEW').val() != '' && $('#MGG_ADDRESS_NEW_1').val() != '') && $('#MGG_ADDRESS_NEW_2').val() == ''){
        alert("상세 신주소를 입력해주세요.");
        isShowPopupBody();
        $('#MGG_ADDRESS_NEW_2').focus();

        return false;
    }else if($('#MGG_BIZ_START_DT').val()==''){
        alert('거래개시일을 선택해주세요.');
        isShowPopupBody();
        $('#MGG_BIZ_START_DT').focus();

        return false;
    }else{
        if($('#MGG_CONTRACT_START_DT').val() == '' && $('#MGG_CONTRACT_END_DT').val() != ''){
            alert('계약시작일을 선택해주세요.');
            isShowPopupBody();
            $('#MGG_CONTRACT_START_DT').focus();
    
            return false;
        }else if($('#MGG_CONTRACT_START_DT').val() != '' && $('#MGG_CONTRACT_END_DT').val() == ''){
            alert('계약종료일을 선택해주세요.');
            isShowPopupBody();
            $('#MGG_CONTRACT_END_DT').focus();
    
            return false;
        }else{
            let isPhoneValidate = true;
            $('.phone').each(function(){
                if($(this).val().trim() != ''){
                    if(!validatePhone($(this).val())){
                        isPhoneValidate = false;
                        alert('번호 양식에 올바르지 않습니다.');
                        isShowPopupBody();
                        $(this).focus();

                        return false;
                    }
                }
            });

            if(isPhoneValidate){
                return true;
            }else{
                return false;
            }
        }      
    }
   
}

excelDownload = () => {
    location.href="gwStateExcel.html?"+$("#searchForm").serialize()+"&REQ_MODE="+CASE_TOTAL_LIST;
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
        const data = JSON.parse(result);
        if(data.status == OK){
            const item = data.data;
            setTable(item[1], item[0], CURR_PAGE);
        }else{
            setHaveNoDataTable();
        }
    });  
}


/** 수정 */
updateData = () => {
    if(validation()){
        isClickUpdate = true;

        let form = $("#garageForm")[0];
        let formData = new FormData(form);
        formData.append('REQ_MODE', CASE_UPDATE);

        callFormAjax(ajaxUrl, 'POST', formData).then((result)=>{
            const data = JSON.parse(result);
            console.log("updateData===>",data);
            resultSetData('수정에 성공하였습니다.', data);
        }); 
    }
}

/** 등록버튼 눌렀을 때 */
addData = () => {
  if(validation()){
      let form = $("#garageForm")[0];
      let formData = new FormData(form);
      formData.append('REQ_MODE', CASE_CREATE);

      callFormAjax(ajaxUrl, 'POST', formData).then((result)=>{
          const data = JSON.parse(result);
          console.log("insert===>",data);
          resultSetData('등록에 성공하였습니다.', data);
      });  
  }
  
}

delData = () => {
    const data = {
        REQ_MODE: CASE_DELETE,
        MGG_ID : $('#ORI_MGG_ID').val(),
    };
    callAjax(ajaxUrl, 'POST', data).then((result)=>{
        const data = JSON.parse(result);
        if(data.status == OK){
            confirmSwalOneButton('삭제에 성공하였습니다.','확인',()=>{
                opener.location.reload();
                self.close();
            });
        }else{
            const resultText ='서버와의 연결이 원활하지 않습니다.' ;
            alert(resultText);
        } 
    });
}

/**
 * insert, update결과 처리 공용함수
 * @param {successMsg : 성공메세지}
 * @param {data : 결과 데이터}
 */
resultSetData = (successMsg, data) => {
    console.log(data);
    if(data.status == OK){
        confirmSwalOneButton(successMsg,'확인',()=>{
              window.opener.getList();
              self.close();
        });
    }else if(data.status == CONFLICT){
        confirmSwalOneButton(data.message,'확인',()=>{
            isShowPopupBody();
            $('#'+data.col).focus();
        });
    }else if(data.status == ERROR){
      confirmSwalTwoButton(data.message,'확인', '취소', false ,()=>{
          $('#IS_EXIST_CIRCUIT_ORDER').val('true');
          isAddOrUpdate();
      }, '진행하시겠습니까?');
    }else{
        alert("서버와의 연결이 원활하지 않습니다.");
    } 
}
