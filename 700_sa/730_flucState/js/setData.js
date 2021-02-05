function getClaimYearOption (){
    const data = {
        REQ_MODE: OPTIONS_BILL_YEAR
    };
  
    callAjax(COMMON_URI, 'POST', data).then((result)=>{
        if(isJson(result)){
            const data = JSON.parse(result);
            setClaimYearOption(data);
        }else{
            alert(ERROR_MSG_1200);
        }
    });
}

function getDivision (){
    const data = {
        REQ_MODE: OPTIONS_DIVISION
    };
  
    callAjax(COMMON_URI, 'POST', data).then((result)=>{
        if(isJson(result)){
            const data = JSON.parse(result);
            setDivisionHeaderTable(data);
        }else{
            alert(ERROR_MSG_1200);
        }
    });
}

