const REDUCE_URL = 'act/reduce_act.php';

$(document).ready(() => {
    getData();
});

$(function(){
    $(".btn-update").click(function(){
        $("input[type=text]").removeAttr("readonly");
        $("input[type=text]#PIE_CUR_DECREMENT_RATIO").attr("readonly", true);
        $(".btn-back").show();
        $(".btn-save").show();
        $(".btn-update").hide();
    });

    $(".btn-back").click(function(){
        $("input[type=text]").attr("readonly", true);
        $(".btn-back").hide();
        $(".btn-save").hide();
        $(".btn-update").show();
    });

    $(".btn-back").hide();
    $(".btn-save").hide();
})

validationData = () => {
    let num_check=/^\d{2}$/;
    
    if($('#PIE_NEW_DECREMENT_RATIO').val().trim()==''){
        alertSwal('변경 감액률을 입력해주세요.');
        $('#PIE_NEW_DECREMENT_RATIO').focus();
        return false;
    }else if(!num_check.test($("#PIE_NEW_DECREMENT_RATIO").val())){
        alertSwal('감액률은 2자리 숫자만 입력가능합니다.');
        $('#PIE_NEW_DECREMENT_RATIO').focus();
        return false;
    }else if($('#PIE_CUR_DECREMENT_RATIO').val().trim()==''){
        alertSwal('현재 감액률을 입력해주세요.');
        $('#PIE_CUR_DECREMENT_RATIO').focus();
        return false;
    }else if(!num_check.test($("#PIE_CUR_DECREMENT_RATIO").val())){
        alertSwal('감액률은 2자리 숫자만 입력가능합니다.');
        $('#PIE_CUR_DECREMENT_RATIO').focus();
        return false;
    }else {
        return true;
    }
}


/** 데이터 세팅 */
getData = () => {
    const data = {
        REQ_MODE : CASE_READ
    };
    callAjax(REDUCE_URL, 'GET', data).then((result)=>{
        const data = JSON.parse(result);
        if(data.status == OK){
            const item = data.data;
            $('#PIE_CUR_DECREMENT_RATIO').val(item.PIE_CUR_DECREMENT_RATIO); 

            showUpdate();
        }else{
            const resultText ='서버와의 연결이 원활하지 않습니다.' ;
            alert(resultText);
        } 
    });
}

showUpdate = () => {
    $(".btn-back").hide();
    $(".btn-save").hide();
    $(".btn-save").attr('onclick', 'updateData()');
    $("#PIE_NEW_DECREMENT_RATIO").val('');
    $("input[type=text]").attr("readonly", true);

    $(".btn-update").show();
}


/** 권한관리 업데이트 */
updateData = () => {
    if(validationData()){
        const data = {
            REQ_MODE: CASE_UPDATE,

            PIE_OLD_DECREMENT_RATIO : $("#PIE_CUR_DECREMENT_RATIO").val(),
            PIE_CUR_DECREMENT_RATIO : $("#PIE_NEW_DECREMENT_RATIO").val()
        };
        callAjax(REDUCE_URL, 'POST', data).then((result)=>{
            const data = JSON.parse(result);
            if(data.status == OK){
                confirmSwalOneButton('수정에 성공하였습니다.','확인',()=>getData());
            }else if(data.status == ERROR){
                alert(data.message);
                $('#PIE_CUR_DECREMENT_RATIO').focus();
            }else{
                const resultText ='서버와의 연결이 원활하지 않습니다.' ;
                alert(resultText);
            }   
        });
    }
}

