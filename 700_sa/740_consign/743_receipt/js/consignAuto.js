/*****************************  공업사 자동완성 *****************************/

onchangeGarage = (evt) => {
    const charCode = (evt.which) ? evt.which : event.keyCode;  
    
    if(charCode != 13){
        $('#MGG_ID').val("");
    }
}

resetGarage = () => {
    if($('#MGG_NM').val() == ''){
        $('#MGG_ID').val("");
    }
}

getGarageInfo = () => {
    const data = {
        REQ_MODE: GET_GARAGE_CLAIM_INFO,
    };
    callAjax(RECEIPT_URL, 'GET', data).then((result)=>{
        if(isJson(result)){
            const res = JSON.parse(result);
            autoCompleteGarage(res.data);
        }else{
            alert(ERROR_MSG_1100);
        }
       
    });
}


autoCompleteGarage = (dataArray) => {
    console.log('auto garage >>>',dataArray);
    $("#MGG_NM").autocomplete({
        minLength : 1,
        source: dataArray.map((item, index) => {
            if($('#MGG_ID').val() != '' && ($('#MGG_ID').val() == item.MGG_ID)){   // 공업사 값이 존재할 경우 등록/수정에 공업사 표시되게
                $('#MGG_ID').val(item.MGG_ID);
                $('#MGG_NM').val(item.MGG_NM);
            }
            return {
                lable : JSON.stringify({
                    MGG_ID : item.MGG_ID, 
                }),
                value : item.MGG_NM
            };
            
        }),
        focus: function(event, ui){ 
            return false;
        },
        select: function(event, ui) {
            let strArea = ui.item.value;
            const data = JSON.parse(ui.item.lable);

            $('#MGG_NM').val(strArea);
            $('#MGG_ID').val(data.MGG_ID);

            $("#MGG_NM").blur();
            $("#MGG_NM").autocomplete('close');
            return false;
        }
    }).on('focus', function(){ 
        $(this).autocomplete("search"); 
    });
}


/*****************************  영업담당 자동완성 *****************************/
onchangeAUU = (evt) => {
    const charCode = (evt.which) ? evt.which : event.keyCode;  
    
    if(charCode != 13){
        $('#AUU_ID').val("");
    }
}


resetAUU = () => {
    if($('#AUU_NM').val() == ''){
        $('#AUU_ID').val("");
    }
}

getSaInfo = () => {
    const data = {
        REQ_MODE: MODE_TARGET_SA,
    };
    callAjax(RECEIPT_URL, 'GET', data).then((result)=>{
        if(isJson(result)){
            const res = JSON.parse(result);
            autoCompleteSa(res.data);
            // setGarageSelectOption(res.data, 'MGG_ID', true); 
        }else{
            alert(ERROR_MSG_1100);
        }
       
    });
}

autoCompleteSa = (dataArray) => {
    console.log('auto sa >>>',dataArray);
    $("#AUU_NM").autocomplete({
        minLength : 1,
        source: dataArray.map((item, index) => {
            if($('#AUU_ID').val() != '' && ($('#AUU_ID').val() == item.AUU_ID)){   // 공업사 값이 존재할 경우 등록/수정에 공업사 표시되게
                $('#AUU_ID').val(item.AUU_ID);
                $('#AUU_NM').val(item.AUU_NM);
            }
            return {
                lable : JSON.stringify({
                    AUU_ID : item.AUU_ID, 
                }),
                value : item.AUU_NM
            };
            
        }),
        focus: function(event, ui){ 
            return false;
        },
        select: function(event, ui) {
            let strArea = ui.item.value;
            const data = JSON.parse(ui.item.lable);

            $('#AUU_NM').val(strArea);
            $('#AUU_ID').val(data.AUU_ID);

            $("#AUU_NM").blur();
            $("#AUU_NM").autocomplete('close');
            return false;
        }
    }).on('focus', function(){ 
        $(this).autocomplete("search"); 
    });
}

