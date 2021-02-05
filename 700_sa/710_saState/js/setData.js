function getVisitCycleData(){
    return callAjax(ajaxUrl, 'POST', $('#searchForm').serialize()+"&REQ_MODE="+CASE_LIST_SUB).then((result)=>{
        if(isJson(result)){
            const data = JSON.parse(result);
            if(data.status == OK){
                console.log("setVisitTable====>",data);
                setVisitTable(data.data);
            }else{
                alert(ERROR_MSG_1100);
            }
        }else{
            alert(ERROR_MSG_1200);
        }
    });
}