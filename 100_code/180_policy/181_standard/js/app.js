const STANDARD_URL = 'act/standard_act.php';

$(document).ready(() => {
    getData();
});

$(function(){
    $(".btn-update").click(function(){
        $("input[type=text]").removeAttr("readonly");
        $(".btn-back").show();
        $(".btn-save").show();
        $(".btn-update").hide();

        $('#PIE_LOW_PRICE').val(replaceAll($('#PIE_LOW_PRICE').val(),',',''));
        $('#PIE_HIGH_PRICE').val(replaceAll($('#PIE_HIGH_PRICE').val(),',',''));
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
    if($('#PIE_LOW_PRICE').val().trim()==''){
        alertSwal('소액 기준금액을 입력해주세요.');
        $('#PIE_LOW_PRICE').focus();
        return false;
    }else if($('#PIE_HIGH_PRICE').val().trim()==''){
        alertSwal('고액 기준금액을 입력해주세요.');
        $('#PIE_HIGH_PRICE').focus();
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
    callAjax(STANDARD_URL, 'GET', data).then((result)=>{
        const data = JSON.parse(result);
        if(data.status == OK){
            const item = data.data;
            $('#PIE_LOW_PRICE').val(numberWithCommas(item.PIE_LOW_PRICE));
            $('#PIE_HIGH_PRICE').val(numberWithCommas(item.PIE_HIGH_PRICE));    

            showUpdate();
        }else{
            const resultText ='서버와의 연결이 원활하지 않습니다.22' ;
            alert(resultText);
        } 
    });
}

showUpdate = () => {
    $(".btn-update").html('수정');
    $(".btn-back").hide();
    $(".btn-save").hide();
    $(".btn-save").attr('onclick', 'updateData()');
    $("input[type=text]").attr("readonly", true);

    $(".btn-update").show();
}

/** 권한관리 업데이트 */
updateData = () => {
    if(validationData()){
        const data = {
            REQ_MODE: CASE_UPDATE,

            PIE_LOW_PRICE : replaceAll($('#PIE_LOW_PRICE').val(),',',''),
            PIE_HIGH_PRICE : replaceAll($('#PIE_HIGH_PRICE').val(),',','')
        };
        callAjax(STANDARD_URL, 'POST', data).then((result)=>{
            const data = JSON.parse(result);
            if(data.status == OK){
                confirmSwalOneButton('수정에 성공하였습니다.','확인',()=>getData());
            }else{
                const resultText ='서버와의 연결이 원활하지 않습니다.' ;
                alert(resultText);
            }   
        });
    }
}

