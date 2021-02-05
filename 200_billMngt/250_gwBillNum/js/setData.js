getClaimYearOption = () => {
    const data = {
        REQ_MODE: CASE_LIST_SUB
    };
  
    callAjax(ajaxUrl, 'POST', data).then((result)=>{
        if(isJson(result)){
            const data = JSON.parse(result);
            if(data.status == OK){
                setClaimYearOption(data.data);
            }else{
                alert(ERROR_MSG_1100);
            }
        }else{
            alert(ERROR_MSG_1200);
        }
    });
}

/** 공업사정보 */
getGarageInfo = (objID) => {
    const data = {
        REQ_MODE: GET_GARGE_INFO,
    };
    callAjax(ajaxUrl, 'GET', data).then((result)=>{
        if(isJson(result)){
            const data = JSON.parse(result);
            if(data.status == OK){
                setGarageInfoOption(data, objID);
            }else{
                alert(ERROR_MSG_1100);
            }
        }else{
            alert(ERROR_MSG_1200);
        }
       
    });
}

/** 구분셀렉트 */
getBillDivisionOption = (objID, isMultiSelector) => {
    $.get('/common/searchOptions.php', {
        REQ_MODE: OPTIONS_DIVISION
    }, function(data, status) {
        const result = JSON.parse(data);
  
        let optionText = "<option value=''>=구분=</option>";
        if(result.length > 0){
            result.map((item, index)=>{
                optionText += '<option value="'+item.CSD_ID+'">'+item.CSD_NM+'</option>';
            });
        }
        $('#'+objID).append(optionText);
        if(isMultiSelector){
            $('#'+objID).selectpicker('refresh');
        }
  
    });
}