const SALES_URL = 'act/sales_act.php';

$(document).ready(() => {
    getData();
});

$(function(){
    $(".btn-update").click(function(){
        $("input[type=text]").removeAttr("readonly");
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

    if($('#PSE_SUPPLY_RATIO').val().trim()==''){
        alertSwal('청구대비 공급현황 배율을 입력해주세요.');
        $('#PSE_SUPPLY_RATIO').focus();
        return false;
    }else if(!num_check.test($('#PSE_SUPPLY_RATIO').val())){
        alertSwal('청구대비 공급현황 배율은 2자리 숫자만 입력가능합니다.');
        $('#PSE_SUPPLY_RATIO').focus();
        return false;
    }else if($('#PSE_STOCK_RATIO').val().trim()==''){
        alertSwal('청구대비 재고현황 배율을 입력해주세요.');
        $('#PSE_STOCK_RATIO').focus();
        return false;
    }else if($('#PSE_OVER_CNT').val().trim()==''){
        alertSwal('적정기준 초과공급수량을 입력해주세요.');
        $('#PSE_OVER_CNT').focus();
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
    callAjax(SALES_URL, 'GET', data).then((result)=>{
        const data = JSON.parse(result);
        if(data.status == OK){
            const item = data.data;
            $('#PSE_SUPPLY_RATIO').val(item.PSE_SUPPLY_RATIO);
            $('#PSE_STOCK_RATIO').val(item.PSE_STOCK_RATIO);    
            $('#PSE_OVER_CNT').val(item.PSE_OVER_CNT);    

            showUpdate();
        }else{
            const resultText ='서버와의 연결이 원활하지 않습니다.' ;
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

            PSE_SUPPLY_RATIO : $("#PSE_SUPPLY_RATIO").val(),
            PSE_STOCK_RATIO : $("#PSE_STOCK_RATIO").val(),
            PSE_OVER_CNT : $("#PSE_OVER_CNT").val()
        };
        callAjax(SALES_URL, 'POST', data).then((result)=>{
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
