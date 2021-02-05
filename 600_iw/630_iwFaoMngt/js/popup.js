/**
 * 신규등록하는 popup.js
 */

 onLoadRegistPopup = () => {
    /** 신규등록 팝업 등록 클릭시 */
    $('#faoForm').submit(function(e){
        e.preventDefault();
        if($('#REQ_MODE').val() == CASE_UPDATE){
            confirmSwalTwoButton('수정 하시겠습니까?','확인', '취소', false ,()=>addData());
        }else{
            confirmSwalTwoButton('저장 하시겠습니까?','확인', '취소', false ,()=>addData());
        }
         
    });

    /** 보험사 셀렉트 체인지 이벤트 */
    $('#MII_ID').change(function(){
        getInsuTeamInfo($('#MII_ID').val(),'MIT_ID', false);

        $('.require').each(function(){
            if($(this).val() != ''){
                sendCheckConfilct($(this).val(), $(this).attr('id'));
            }
        });
    });
    setUpdateData();
    getInsuInfo('MII_ID', false);    // 보험사 정보
    
 }

 /**
 * 수정/등록상황에 맞게 submit 텍스트 및 이벤트 수정
 */
setUpdateData = () => {
    if($('#NIC_ID').val() != ''){
        $('#modalTitle').html("보상담당수정");
        $('#modalSubmitBtn').html("수정");
        $('#REQ_MODE').val(CASE_UPDATE);
    }else{
        $('#modalTitle').html("신규등록");
        $('#modalSubmitBtn').html("등록");
        $('#REQ_MODE').val(CASE_CREATE);    
    }
}

 /**
 * 등록창에서 보험사정보 가져오기
 * @param {objID : 타겟 아이디}
 * @param {isMultiSelect : 멀티셀렉틍니지 아닌지}
 * @param {defaultText : 기본옵션 텍스트}
 */
getInsuInfo = (objID, isMultiSelect, defaultText) => {
    const data = {
        REQ_MODE: OPTIONS_IW,
    };
    callAjax('/common/searchOptions.php', 'GET', data).then((result)=>{
        if(isJson(result)){
            const res = JSON.parse(result);
            if(res != undefined || res.length > 0){
                insuArray = res;
                let REQ_MII_ID = undefined;
                if($('#REQ_MII_ID').val()!=''){
                    REQ_MII_ID = $('#REQ_MII_ID').val();
                    getInsuTeamInfo(REQ_MII_ID,'MIT_ID',false,$('#REQ_MIT_ID').val());
                }
                setSelectOption(res, objID, isMultiSelect, defaultText== undefined ? '선택' : defaultText, REQ_MII_ID);
            }
        }else{
            alert(ERROR_MSG_1200);
        }
    });
}


/**
 * 성명/팩스/휴대폰번호 중복여부 (onblur 이벤트)
 */
onCheckConfilt = (evt) => {
    const objTarget = evt.srcElement || evt.target;
    sendCheckConfilct($(objTarget).val(), $(objTarget).attr('id'), objTarget);
}

sendCheckConfilct = (value, id, objTarget) => {
    if(value.trim() != ''){
        let form = $("#faoForm")[0];
        let formData = new FormData(form);
        formData.append('REQ_MODE', CASE_CONFLICT_CHECK);
        formData.append('TARGET_ID', id);
        formData.append('TARGET_VALUE', value);
        formData.append('MII_ID', $('#MII_ID').val());
        
        callFormAjax(ajaxUrl, 'POST', formData).then((result)=>{
            $(objTarget).val(phoneFormat(value));
            if(isJson(result)){
                const data = JSON.parse(result);
                if(data.status == OK){
                    $('#CONFLICT_'+id).html("");
                }else{
                    $('#CONFLICT_'+id).html("이미 중복된 데이터가 존재합니다.");
                }
            }else{
                alert(ERROR_MSG_1200);
            }      
        });
    }
}


isValidate = () => {
    if($('#NIC_FAX_AES').val() != '' ){
        $('#NIC_FAX_AES').val(replaceAll($('#NIC_FAX_AES').val(),'-',''));
        phoneFormat($('#NIC_FAX_AES').val());
    }
    if($('#NIC_TEL1_AES').val() != '' ){
        $('#NIC_TEL1_AES').val(replaceAll($('#NIC_TEL1_AES').val(),'-',''));
    }
    if($('#NIC_TEL2_AES').val() != '' ){
        $('#NIC_TEL2_AES').val(replaceAll($('#NIC_TEL2_AES').val(),'-',''));
    }
    
    if(!validatePhone($('#NIC_TEL1_AES').val().trim())){
        alert(ERROR_VALIDATE_PHONE);
        $('#NIC_TEL1_AES').focus();

        return false;
    }else{
        if($('#NIC_TEL2_AES').val().trim() != '' && !validatePhone($('#NIC_TEL2_AES').val().trim())){
            alert(ERROR_VALIDATE_PHONE);
            $('#NIC_TEL2_AES').focus();

            return false;
        }else if($('#NIC_EMAIL_AES').val().trim() != '' && !validateEmail($('#NIC_EMAIL_AES').val().trim())){
            alert(ERROR_VALIDATE_EMAIL);
            $('#NIC_EMAIL_AES').focus();

            return false;
        }else{
            return true;
        }

    }
}

function onFocusPhone(evt){
    const objTarget = evt.srcElement || evt.target;
    const _value = event.srcElement.value;  
    $(objTarget).val(_value.replace(/-/g, ''));
}
/**
 * 성명/팩스/전화번호 키 입력시 중복입니다 라는 멘트 없애주는 용도
 */
onChangeRequireData = (evt) => {
    const objTarget = evt.srcElement || evt.target;
    $('#CONFLICT_'+$(objTarget).attr('id')).html("");
}

/**
 * 보상담당자 신규등록
 */
registInsuManager = () => {
    $('REQ_MODE').val(CASE_CREATE);
    console.log($('#faoForm').serialize());
}



/**
 * 보험사 선택시 팀 select옵션 세팅되게
 * @param {objID : 타겟 아이디}
 * @param {isMultiSelect : 멀티셀렉틍니지 아닌지}
 */
getInsuTeamInfo = (miiId,objID, isMultiSelect, defaultText) => {

    const data = {
        REQ_MODE: GET_INSU_TEAM,
        MII_ID : miiId
    };
    callAjax(ajaxUrl, 'GET', data).then((result)=>{
        if(isJson(result)){
            const res = JSON.parse(result);
            if(res.status == OK){
                let optionHtml = "";  
                res.data.map((item)=>{
                    optionHtml += "<option value="+item.MII_ID+">"+item.MII_NM+"</option>";
                }); 

                $("#" + objID).html("<option value=''>선택</option>"+optionHtml); 
                
                if(defaultText != undefined){
                    $("#" + objID).val(defaultText);
                }
                   
            }else{
                let optionHtml = "<option value=''>선택</option>";   
                 $("#" + objID).html(optionHtml); 
            }
        }else{
            alert(ERROR_MSG_1200);
        }
    });
}


/** 등록버튼 눌렀을 때 */
addData = () => {
    if(isValidate()){
        let form = $("#faoForm")[0];
        let formData = new FormData(form);
  
        callFormAjax(ajaxUrl, 'POST', formData).then((result)=>{
            const data = JSON.parse(result);
            console.log("insert===>",data);

            if(isJson(result)){
                if(data.status == OK){
                    confirmSwalOneButton(SAVE_SUCCESS_MSG, OK_MSG,()=>{
                        window.opener.callbackAddData();
                        window.close();
                    });
                }else{
                    alert(ERROR_MSG_1100);
                } 
            }else{
                alert(ERROR_MSG_1200);
            }
            
        });  
    }    
}



/* 데이터 삭제함수 */
delData = (targetId) => {
    const data = {
        REQ_MODE: CASE_DELETE,
        NIC_ID:   targetId != undefined ? targetId : $("#NIC_ID").val() 
    };
    callAjax(ajaxUrl, 'POST', data).then((result)=>{
        const data = JSON.parse(result);
        if(data.status == OK){
            confirmSwalOneButton(
              '삭제에 성공하였습니다.',
              '확인',
              ()=>{
                window.opener.getList();
                window.close();
              }
            );
        }else{
            const resultText ='서버와의 연결이 원활하지 않습니다.' ;
            alertSwal(resultText);
        } 
    });
  }
  