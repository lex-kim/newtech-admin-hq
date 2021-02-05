const RICT_ID_NORMAL = 11320;    // 담당구분 중 일반

setMainSelectOption = () => {

}

/**
 * 등록 팝어비 로드 됐을 때 옵션 세팅
 */
setRegistPopSelectOPtion = async() => {
    getGarageInfo();
    getNmiInsuInfo('MII_ID', false);
    getRefInsuTypeOptions(GET_REF_INSU_CHAGE_TYPE, 'RICT_ID', false, false);        // 반응 

}

/** 담당구분 */
getRefInsuTypeOptions = (type, objId) => {
    const data = {
        REQ_MODE: GET_REF_OPTION,
        REF_TYPE : type
    };
  
    const ajaxUrl = '/common/gwReference.php';
    callAjax(ajaxUrl, 'GET', data).then((result)=>{
        const data = JSON.parse(result);
        if(data.status == OK){
            const dataArray = data.data;
            if(dataArray.length > 0){
                let optionText = '';
        
                dataArray.map((item, index)=>{
                    console.log("$('#REQ_RICT_ID').val() ===>",$('#REQ_RICT_ID').val() );
                    const selectValue = $('#REQ_RICT_ID').val() == '' ? RICT_ID_NORMAL : $('#REQ_RICT_ID').val();
                    if(item.ID == selectValue){
                        optionText += '<option value="'+item.ID+'" selected>'+item.NM+'</option>';
                    }else{
                        optionText += '<option value="'+item.ID+'">'+item.NM+'</option>';
                    }
                   
                });
  
                $('#'+objId).append(optionText);
            }
        } 
       
    });
}

/**
 * 공업사(청구담당자 포함)정보 가져오기 
 */
getGarageInfo = () => {
    const data = {
        REQ_MODE: GET_GARAGE_CLAIM_INFO,
    };
    callAjax(ajaxUrl, 'GET', data).then((result)=>{
        if(isJson(result)){
            const res = JSON.parse(result);
            autoCompleteGarage(res.data);
            // setGarageSelectOption(res.data, 'MGG_ID', true); 
        }else{
            alert(ERROR_MSG_1100);
        }
       
    });
}

/**
 * 공업사 셀렉트 동적으로 세팅
 * @param {dataArray : 공업사 정보(공업사아이디, 공업사이름, 사무실번호, 청구담당자 이름, 청구담당자 번호)}
 * @param {objID : 동적으로 준비된 option을 넣어줄 셀렉트}
 * @param {isMultiSelect : 멀티셀레겉인지}
 * @param {defaultText : 기본텍스트 (undefined 이면 null 아니면 defaultText 값으로 세ㅣㅇ)}
 */
setGarageSelectOption = (dataArray, objID, isMultiSelect, defaultText) => {      
    let optionHtml = "<option value=''>=공업사=</option>";   
    if(defaultText != undefined){
        optionHtml = "<option value=''>"+defaultText+"</option>";
    }
    dataArray.map((item, index)=>{
        if($('#REQ_'+objID).val() != ''){
            $("#" + objID).val($('#REQ_'+objID).val()); 
        }else{

        }
        const checkedHtml = ($('#REQ_'+objID).val() == item.MGG_ID) ? 'selected' : '';   // 값이 있다면 선택되게..(수정모드)
        optionHtml += "<option "+checkedHtml+" value="+JSON.stringify({
            MGG_ID : item.MGG_ID, 
            MGG_TEL : item.MGG_OFFICE_TEL,
            MGG_CLAIM_NM_AES : item.MGG_CLAIM_NM_AES,
            MGG_CLAIM_TEL_AES : item.MGG_CLAIM_TEL_AES

        }) +">"+item.MGG_NM+"</option>";
    });
    
    $("#" + objID).html(optionHtml); 
    if(isMultiSelect){
        $("#" + objID).selectpicker('refresh');
    }
}

/**
 * 등록창에서 보험사정보 가져오기
 * @param {objID : 타겟 아이디}
 * @param {isMultiSelect : 멀티셀렉틍니지 아닌지}
 */
getNmiInsuInfo = (objID, isMultiSelect) => {
    const data = {
        REQ_MODE: OPTIONS_IW,
    };
    callAjax('/common/searchOptions.php', 'GET', data).then((result)=>{
        const res = JSON.parse(result);
        let optionHtml = '';
        if(res != undefined || res.length > 0){
           
            res.map((item, index)=>{
                const checkedHtml = ($('#REQ_'+objID).val() == item.MII_ID) ? 'selected' : '';     // 값이 있다면 선택되게..(수정모드)
                optionHtml += "<option "+checkedHtml+" value="+item.MII_ID+">"+item.MII_NM+"</option>";
            });
        }
        
        $("#" + objID).append(optionHtml); 

        if($('#REQ_'+objID).val()!=''){
            getInsuManageInfo();
        }

        if(isMultiSelect){
            $("#" + objID).selectpicker('refresh');
        }
    });
}

