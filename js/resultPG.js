/**
 * PG처리 결과 처리 함수
 * param {resultInfo : PG결과 데이터}
 * param {callback : 콜백함수 function(){}}
 */
function onResultKICC(resultInfo, callback){
    const data = {
        REQ_MODE : CASE_CREATE,
        pg_result : resultInfo,
    }

    callAjax("/common/commonAjaxController.php", 'GET', data).then(function (result){
        if(isJson(result)){
            var res = JSON.parse(result);
            if(res.status == OK){
                callback();
            }else{
                alert(ERROR_MSG_1100);
            }
        }else{
            alert(ERROR_MSG_1200);
        }
        
    });
}