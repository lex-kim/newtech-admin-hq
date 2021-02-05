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
                }
                setSelectOption(res, objID, true , "", REQ_MII_ID);
                setSelectOption(res, 'BOTH_INSU_SEARCH', true , "쌍방작성", REQ_MII_ID);
                
                autoCompleteInsu(insuArray, 'MII_ID_TEXT_SEARCH');
            }
        }else{
            alert(ERROR_MSG_1200);
        }
    });
}
setInsuSelectOption = (dataArray, objID, isMultiSelect, defaultText, defaultValue) => {      
    let optionHtml = "<option value=''>선택</option>"; 

    dataArray.map((item, index)=>{
        optionHtml += "<option value="+item.MII_ID+">"+item.MII_NM+"</option>";
    });
    
    $("#" + objID).html(optionHtml); 

    if(isMultiSelect){
        if(defaultValue != undefined){
            $("#" + objID).selectpicker('val', defaultValue);
        }
        $("#" + objID).selectpicker('refresh');
    }else{
        if(defaultValue != undefined){
            $("#" + objID).val(defaultValue);
        }
    }   
}

/* 보험사Team 동적 생성 */
getInsuTeam = () =>{
    const data = {
      REQ_MODE: GET_INSU_TEAM,
    };
    callAjax(ajaxUrl, 'GET', data).then((result)=>{
        const data = JSON.parse(result);
        if(data.status == OK){
            const dataArray = JSON.parse(data.data); 
              
            let optionText = '';
            dataArray.map((item)=>{
                optionText += '<option value="'+item.MIT_ID+'">'+item.MIT_NM+'</option>';
            });
            $('#MII_ID_TEAM_SEARCH').html(optionText);
            $('#MII_ID_TEAM_SEARCH').selectpicker('refresh');
          
        }else{
            alert("서버와의 연결이 원활하지 않습니다.");
        }
    });
}

/** 지역셀렉트 */
setBillCRROption = () => {
    setCRRSelectOption('CRR_ID_SEARCH', true, undefined);
}

/** 구분셀렉트 */
getBillDivisionOption = (objID, isMultiSelector) => {
    $.get('/common/searchOptions.php', {
        REQ_MODE: OPTIONS_DIVISION
    }, function(data, status) {
        const result = JSON.parse(data);
  
        let optionText = "";
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

/** 공업사정보 */
getGarageInfo = (objID) => {
    const data = {
        REQ_MODE: GET_GARGE_INFO,
    };
    callAjax(ajaxUrl, 'GET', data).then((result)=>{
        if(isJson(result)){
            const data = JSON.parse(result);
            if(data.status == OK){
                let optionText = "";
                if(data.data.length > 0){
                    data.data.map((item, index)=>{
                        optionText += '<option value="'+item.MGG_ID+'">'+item.MGG_NM+'</option>';
                    });
                }
                $('#'+objID).append(optionText);
                $('#'+objID).selectpicker('refresh');
            }else{
                alert(ERROR_MSG_1100);
            }
        }else{
            alert(ERROR_MSG_1200);
        }
       
    });
}