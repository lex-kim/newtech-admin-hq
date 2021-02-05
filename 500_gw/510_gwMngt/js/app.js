const pageName = "gwMngt.html";
const ajaxUrl = 'act/gwMngt_act.php';

const CONTRACT_STOP = 10620;   // 거래중단
let isValidate = true;         // 등록창에서 submit 진행여부를 결정하는 변수
let isClickUpdate = false;     // 수정진행하겠냐는 컨펌에 클릭여부
let CURR_PAGE = 1;
let SELECT_CONTRACT = "";      // 거래중단 이전값 저장하기 위한 변수
let CODE_PARTS=null;
let NEW_CODE_PARTS=null;
$(document).ready(function() { 
    $("#stopTable").hide();
    setDate();       // datepicker 데이터 세팅
    getCodeParts(); //기준부품 조회

    //확장하기
    $("#extend").click(function(){
        if($("#extend").prop("checked")){
            $(".hidetd").show();
        }else{
            $(".hidetd").hide();
        }
    });

    // 거래중단 시 테이블보이기
    $("#RCT_ID").change(function(e){
        if ( $(this).val() == CONTRACT_STOP ) {
            if($('#MGG_ID').val() != ''){
                showConstractStopTable(true);
                $('#RSR_ID').attr('disabled', false);
                SELECT_CONTRACT = $(this).val();
            }else{
                alert('공업사코드를 먼저 입력해주세요.');
                $('#MGG_ID').focus();
                $('#RCT_ID').val(SELECT_CONTRACT);
                $("#stopTable").hide();
            }
        } else {
            $('#RSR_ID').attr('disabled', true);
            $('#RSR_ID').val('');
            SELECT_CONTRACT = $(this).val();
            $("#stopTable").hide();
        }
    });
    
    /** popup에 구분 셀렉터 이벤트 */
    $('#CSD_ID').change(()=>{
        if($('#CSD_ID').val() != ''){
            setCRR_ID('CSD_ID', 'CRR_ID', 'SELECT_CRR_ID', true);
        }else{
            $('#CRR_ID').val('');
            $('#SELECT_CRR_ID').val('');
            $('#CRR_ID').attr('disabled',true);

        }
        
    });
    
    $('#CSD_ID_SEARCH').change(()=>{
        if($('#CSD_ID_SEARCH').selectpicker('val') != null){
            setCRR_ID_Option('CSD_ID_SEARCH', 'CRR_ID_SEARCH', true, true);
        }else{
            $('#CSD_ID').selectpicker('val' , '');
            $('#CSD_ID').selectpicker('refresh');
            setCRR_ID_Option('CSD_ID_SEARCH', 'CRR_ID_SEARCH', true);

        }
        
    });

    /** 등록/수정  */
    $("#garageForm").submit(function(e){
        e.preventDefault();

        if(isValidate && validation()){
            isAddOrUpdate();
        }
    });

    /** 검색 */
    $("#searchForm").submit(function(e){
        e.preventDefault();

        if(searchValidate()){
            getList(1);
        }
    });
    

    /** 등록창에서 수정/등록 눌렀을 때 submit 이벤트에 앞서 validate후 submit 이벤트 발생 */
    $('#btnSubmit').click(function(){
        if($(".popup-body").hasClass("sub-hide")){
            $('.require').attr('required',false);
            isValidate = true;
            $('.require').each(function(){
                if($(this).val().trim() ==''){
                    $(".popup-body").toggleClass("sub-hide");
                    $("#bizbtn").html("숨기기");
                    $(this).attr('required',true);
                    $(this).focus();
                    isValidate = false;    
                    return false;
                }
            });
        }else{
            $('.require').attr('required',true);
            isValidate = true;
        }
    })

    /** 등록창에서 공금사 정보 부분 보이기/숨시기 */
    $("#bizbtn").click(function(){
        $(".popup-body").toggleClass("sub-hide");
          if($(".popup-body").hasClass("sub-hide") === true) {
            $("#bizbtn").html("보이기");
          }else{
            $("#bizbtn").html("숨기기");
          }
    });
  
    /** 등록창에서 공급 사용 부품 부분 보이기/숨기기 */
    $("#compbtn").click(function(){
        $(".popup-bottom").toggleClass("sub-hide");
        if($(".popup-bottom").hasClass("sub-hide") === true) {
            $("#compbtn").html("보이기");
        }else{
            $("#compbtn").html("숨기기");
        }
    });
    
    /** 등록에서 지역 인풋 변경시 선택값을 초기화*/
    // $('#CRR_ID').change(()=>{
    //     $('#SELECT_CRR_ID').val('');
    //     console.log("111===>",$('#SELECT_CRR_ID').val());
    // });

     /** 등록에서 지역 인풋 변경시 선택값을 초기화*/
     $('#CSD_ID_SEARCH').change(()=>{
        if($('#CSD_ID_SEARCH').selectpicker('val') == null){
            $('#CRR_ID_SEARCH').val('');
            $('#CRR_ID_SEARCH').html('');
            $('#CRR_ID_SEARCH').selectpicker('refresh');
        }
    });
    
    /** 검색에서 등록기간 셀렉트 이벤트 */
    $('#MY_BIZ_DT_SEARCH').change(()=>{
        if( $('#MY_BIZ_DT_SEARCH').val() == ''){
            $('#BIZ_START_DT_SEARCH').val('');
            $('#BIZ_END_DT_SEARCH').val('');
        }
    });
    
   
});
 
/** 위도경도 맵 링크  */
mapLink = (val) => {
    let addr = replaceAll(val, ' ', '+');
    const popupUrl = 'https://www.google.com/maps/search/'+addr;
    window.open(popupUrl, 
                     'mapwindow', 
                     'width=900,height=580'); 
}
/**
 * 메인창 로드 될때.
 */
onParentLoad = async() => {
    $(".hidetd").hide();
    setInitTableData(70);
    getDivisionOption('CSD_ID_SEARCH', true);                                      //지역구분
    setCRR_ID_Option('CSD_ID_SEARCH', 'CRR_ID_SEARCH', true);
    getRefOptions(GET_REF_EXP_TYPE, 'RET_ID_SEARCH', true, false);                 //청구식
    getRefOptions(GET_REF_STOP_REASON_TYPE, 'RSR_ID_SEARCH', true, false);         //거래중단사유
    getRefOptions(GET_REF_PAY_EXP_TYPE, 'RPET_ID_SEARCH', false, false);           //지급식
    getRefOptions(GET_REF_AOS_TYPE, 'RAT_ID_SEARCH', false, false); 
    getRefOptions(GET_REF_MAIN_TARGET_TYPE, 'RMT_ID_SEARCH', false, false);        //주요정비 
    getRefOptions(GET_REF_OWN_TYPE, 'ROT_ID_SEARCH', false, false);                // 자가여부 
    getRefOptions(GET_REF_CONTRACT_TYPE, 'RCT_ID_SEARCH', false, false);           // 자가여부 
}



openWindowPopup = (val) => {
    const formData = $('#gwForm');
    const position = getPopupPosition(1500, 1000);
        
    window.open('', 'newwindow', 
            'width='+1500+',height='+1000+', left='+ position.popupX + ', top='+ position.popupY+',toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no');
    $('#MGG_VALUE').val(val);
    formData.submit();
}

/**
 * 공업사 상태가 보이기인지 숨기기 인지
 * require 체크가 숨기기 상태에서는 안되기 때문에.
 */
isShowPopupBody = () => {
    if($(".popup-body").hasClass("sub-hide")){
        $(".popup-body").toggleClass("sub-hide");
        $("#bizbtn").html("숨기기");
    }
}

/**
 * 현재 페이시 상태가 add 인지 update 인지
 */
isAddOrUpdate = () => {
    if($('#MGG_NM').val().indexOf(',') != -1 || $('#MGG_ID').val().indexOf(',') != -1 ){ //공업사명에 콤마 사용 불가
        alert('공업사명/공업사코드에 콤마(,) 사용이 불가합니다.');
        return false;
    }
    if($('#ORI_MGG_ID').val() == ''){
        addData();
    }else{
        if(isClickUpdate){
            updateData();
        }else{
            confirmSwalTwoButton('수정 하시겠습니까?','확인', '취소', false ,()=>updateData());
        }
        
    }
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
    }else if($('#MGG_ZIP_NEW').val() == '' || $('#MGG_ADDRESS_NEW_1').val() == ''){
        alert("주소검색을 통해 신주소를 입력 선택해주세요.");
        isShowPopupBody();
        execPostCode('NEW');

        return false;
    }else if($('#MGG_BIZ_START_DT').val()==''){
        alert('거래개시일을 선택해주세요.');
        isShowPopupBody();
        $('#MGG_BIZ_START_DT').focus();

        return false;
    }else{
        if($('#RCT_ID').val() == CONTRACT_STOP && $('#RSR_ID').val() == ''){
            alert("거래중단사유를 선택해주세요.");
            isShowPopupBody();
            $('#RSR_ID').focus();

            return false;

        }else if($('#MGG_CONTRACT_START_DT').val() == '' && $('#MGG_CONTRACT_END_DT').val() != ''){
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

/** 수리공장명을 입력할때는 선택 value를 초기화. 입력하고 선택할수 있게 유도 */
resetSELECT_CRR_ID = (evt) => {
    const charCode = (evt.which) ? evt.which : event.keyCode;  
    
    if(charCode != 13){
        $('#SELECT_CRR_ID').val('');
    }   
}

setMoenyFormat = (type) => {
    if(type){
        $('#MGG_END_AVG_BILL_PRICE').val($('#MGG_END_AVG_BILL_PRICE').val().replace(/,/g, ''));
    }else{
        $('#MGG_END_AVG_BILL_PRICE').val(numberWithCommas($('#MGG_END_AVG_BILL_PRICE').val()));
    }
}

excelDownload = () => {
    location.href="gwMngtExcel.html?"+$("#searchForm").serialize()+"&REQ_MODE="+CASE_TOTAL_LIST;
    // $('#excelForm').attr("method","GET");
    // $('#excelForm').attr("action","gwMngtExcel.html");
    // $('#excelForm').submit();
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
            if(data.status == OK){
                const item = data.data;
                setTable(item[1], item[0], CURR_PAGE);  // 여기서만 index가 2번에 total 정보가 있음
            }else{
                setHaveNoDataTable();
            }
        }else{
            alert(ERROR_MSG_1200);
        }
       
    });  
}


/** 수정 */
updateData = () => {
    isClickUpdate = true;

    let form = $("#garageForm")[0];
    let formData = new FormData(form);
    formData.append('REQ_MODE', CASE_UPDATE);

    if($('#stopTable').is(':visible')){
        formData.append('MGG_END_AVG_BILL_CNT', $('#MGG_END_AVG_BILL_CNT').val());
        formData.append('MGG_END_AVG_BILL_PRICE', $('#MGG_END_AVG_BILL_PRICE').val().replace(/,/g,''));
        formData.append('MGG_END_OUT_OF_STOCK', $('#MGG_END_OUT_OF_STOCK').val().replace(/(?:\r\n|\r|\n)/g, '<br>'));
    }else{
        formData.append('MGG_END_AVG_BILL_CNT', "");
        formData.append('MGG_END_AVG_BILL_PRICE', "");
        formData.append('MGG_END_OUT_OF_STOCK', "");
    }

    callFormAjax(ajaxUrl, 'POST', formData).then((result)=>{
        if(isJson(result)){
            const data = JSON.parse(result);
            resultSetData('수정에 성공하였습니다.', data);
        }else{
            alert(ERROR_MSG_1200);
        }
    }); 
}

/** 등록버튼 눌렀을 때 */
addData = () => {
    let form = $("#garageForm")[0];
    let formData = new FormData(form);
    formData.append('REQ_MODE', CASE_CREATE);

    if($('#stopTable').is(':visible')){
        formData.append('MGG_END_AVG_BILL_CNT', $('#MGG_END_AVG_BILL_CNT').val());
        formData.append('MGG_END_AVG_BILL_PRICE', $('#MGG_END_AVG_BILL_PRICE').val().replace(/,/,''));
        formData.append('MGG_END_OUT_OF_STOCK', $('#MGG_END_OUT_OF_STOCK').val().replace(/(?:\r\n|\r|\n)/g, '<br>'));
    }else{
        formData.append('MGG_END_AVG_BILL_CNT', "");
        formData.append('MGG_END_AVG_BILL_PRICE', "");
        formData.append('MGG_END_OUT_OF_STOCK', "");
    }

    callFormAjax(ajaxUrl, 'POST', formData).then((result)=>{
        if(isJson(result)){
            const data = JSON.parse(result);
            resultSetData('등록에 성공하였습니다.', data);
        }else{
            alert(ERROR_MSG_1200);
        }
    });  
  
}

delData = () => {
    const data = {
        REQ_MODE: CASE_DELETE,
        MGG_ID : $('#ORI_MGG_ID').val(),
        NSU_ID : $('#ORI_NSU_ID').val(),
    };
    callAjax(ajaxUrl, 'POST', data).then((result)=>{
        if(isJson(result)){
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
        }else{
            alert(ERROR_MSG_1200);
        }
    });
}

/**
 * insert, update결과 처리 공용함수
 * @param {successMsg : 성공메세지}
 * @param {data : 결과 데이터}
 */
resultSetData = (successMsg, data) => {
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
    }else if(data.status == ALREADY_EXISTS){
        confirmSwalTwoButton(data.message,'확인', '취소', false ,()=>{
            $('#IS_EXIST_MGG_BIZ_ID').val('true');
            isAddOrUpdate();
        }, '진행하시겠습니까?');
    }else if(data.status == ERROR){
      confirmSwalTwoButton(data.message,'확인', '취소', false ,()=>{
          $('#IS_EXIST_CIRCUIT_ORDER').val('true');
          isAddOrUpdate();
      }, '진행하시겠습니까?');
    }else{
        alert("서버와의 연결이 원활하지 않습니다.");
    } 
}

/**
 * 주소검색
 * @param {objId : 구주소/신주소 고유 input 아이디(해당 값을 세팅해주기 위해.)}
 */
execPostCode = (objId) => {
    new daum.Postcode({
        oncomplete: function(data) {
          let fullAddr = ''; // 최종 주소 변수
          let extraAddr = ''; // 조합형 주소 변수
          // 사용자가 선택한 주소 타입에 따라 해당 주소 값을 가져온다.
          if (data.userSelectedType === 'R') { // 사용자가 도로명 주소를 선택했을 경우
              fullAddr = data.roadAddress;
          } else { // 사용자가 지번 주소를 선택했을 경우(J)
              fullAddr = data.jibunAddress;
          }
          // 사용자가 선택한 주소가 도로명 타입일때 조합한다.
          if(data.userSelectedType === 'R'){
              //법정동명이 있을 경우 추가한다.
              if(data.bname !== ''){
                  extraAddr += data.bname;
              }
              // 건물명이 있을 경우 추가한다.
              if(data.buildingName !== ''){
                  extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
              }
              // 조합형주소의 유무에 따라 양쪽에 괄호를 추가하여 최종 주소를 만든다.
              fullAddr += (extraAddr !== '' ? ' ('+ extraAddr +')' : '');
          }
          // 우편번호와 주소 정보를 해당 필드에 넣는다.
          $("#MGG_ZIP_"+objId).val(data.zonecode);
          $("#MGG_ADDRESS_"+objId+"_1").val(fullAddr);
          $("#MGG_ADDRESS_"+objId+"_2").focus();
          if(data.zonecode.length >= 5){
            $('#mapLinkBtn').show();
            $('#mapLinkBtn').attr('onclick', 'mapLink(\''+data.roadAddress+'\')');
          }
        }
    }).open();
}



imgZoomFilePopup = () => {
    // console.log('imgdata>>>',$('#NSU_FILE_IMG').attr('src'));
    const formData = $('#transImgForm');
    window.open('', 
    'newwindowFileImg', 
    'width=750,height=800');

    formData.submit();

}

imgZoomSignPopup = () => {
    const formData = $('#transSignForm');
    window.open('', 
    'newwindowSignImg', 
    'width=750,height=800');

    formData.submit();

}

/**
 * 기준부품 전체 정보 조회 
 */
getCodeParts = () => {
    const data = {
        REQ_MODE: GET_REF_DATA
    };

    callAjax(ajaxUrl, 'GET', data).then((result)=>{
        const data = JSON.parse(result);
        CODE_PARTS = data;
        NEW_CODE_PARTS = data;
    });
}
