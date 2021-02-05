getSettingData = () => {
    console.log(BILL_URL);
    const param = {
        REQ_MODE:  CASE_READ,
        REQ_TARGET:  STOCK_BILL,
    }
    callAjax(BILL_URL, 'GET', param).then((result)=>{
        console.log('getSettingDATA SQL >> ');
        console.log(result);
        const res = JSON.parse(result);
        if(res.status == OK){
            console.log('getSettingData res >',res);
            const resData = res.data;
            $('#NIE_BILL_VS_STOCK_RATIO').val(resData[0]);
            $('#NIE_INDEPENDENT_STOCK_RATIO').val(resData[1]);
            getSubstitution(resData[2]);
        }else{
            alert(ERROR_MSG_1200);
        } 
       
    });
}

/* 대체정산식 출력 */
getSubstitution = (arrData) => {
    let tableTxt = '';
    arrData.map((arr, i)=>{
        tableTxt += "<tr>";
        tableTxt += "<td class='text-left'>"+arr.NIS_FROM_CPPS_NM+"</td>";
        tableTxt += "<td class='text-center'>=</td>";
        tableTxt += "<td class='text-left'>"+arr.NIS_TO_CPPS_NM+"</td>";
        tableTxt += "<td class='text-center'>X</td>";
        tableTxt += "<td class='text-right'>"+arr.NIS_RATIO+"</td>";
        if($("#AUTH_DELETE").val() == YN_Y){
            tableTxt += "<td class='text-center'><button type='button' class='btn btn-danger' id='btnDelete' onclick='delConfirmSubstitution("+JSON.stringify(arr)+")'>삭제</button></td>";
        }else{
            tableTxt += "<td></td>";
        }
        tableTxt += "</tr>";
    })
    $('#settingTable').html(tableTxt);
    

}
/* 저장 확인 */
setConfirm = (name, type) => {
    if(validationData(name, type)){
        confirmSwalTwoButton(name+'을 저장하시겠습니까?','확인', '취소', false ,()=>{ 
            setData();
        });
    }else {
        alert(ERROR_MSG_1400);
        return false;
    }
}


/* 데이터 내용 확인 */
validationData = (name, type) => {
    if(type == 'BILL'){
        if($('#NIE_BILL_VS_STOCK_RATIO').val() == ''){
            alert(name,'을 입력해주세요.');
            $('#NIE_BILL_VS_STOCK_RATIO').focus();
            return false;
        }else {
            $('#NIE_INDEPENDENT_STOCK_RATIO').val('');
            return true;
        }
    }else if(type == 'INDEPENDENT') {
        if($('#NIE_INDEPENDENT_STOCK_RATIO').val() == ''){
            alert(name,'을 입력해주세요.');
            $('#NIE_INDEPENDENT_STOCK_RATIO').focus();
            return false;
        }else {
            $('#NIE_BILL_VS_STOCK_RATIO').val('');
            return true;
        }
    }else if(type == 'SUBSTITUTION') {
        if($('#NIS_FROM_CPPS_ID').val() == '' || $('#NIS_TO_CPPS_ID').val() == ''){
            alert('부품구분을 입력해주세요.');
            return false;
        }else if($('#NIS_RATIO').val() == '') {
            alert('정산 비율을 입력해주세요.');
            $('#NIS_RATIO').focus();
            return false;
        }else {
            $('#NIE_BILL_VS_STOCK_RATIO').val('');
            $('#NIE_INDEPENDENT_STOCK_RATIO').val('');
            return true;
        }
    }else {
        alert(ERROR_MSG_1400);
        return false;
    }
}

/*데이터 저장 */
setData = () => {
    // console.log(BILL_URL);
    const param = {
        REQ_MODE:  CASE_UPDATE,
        REQ_TARGET: STOCK_BILL,
        NIE_BILL_VS_STOCK_RATIO: $('#NIE_BILL_VS_STOCK_RATIO').val(),
        NIE_INDEPENDENT_STOCK_RATIO: $('#NIE_INDEPENDENT_STOCK_RATIO').val(),
        NIS_FROM_CPPS_ID: $('#NIS_FROM_CPPS_ID').val(),
        NIS_TO_CPPS_ID: $('#NIS_TO_CPPS_ID').val(),
        NIS_RATIO: $('#NIS_RATIO').val(),
    }
    callAjax(BILL_URL, 'POST', param).then((result)=>{
        // console.log('setData SQL >> ');
        // console.log(result);
        const res = JSON.parse(result);
        if(res.status == OK){
            alert(SAVE_SUCCESS_MSG);
            getSettingData();
        }else{
            alert(ERROR_MSG_1200);
        } 
       
    });
}

/* 대체정산식 삭제*/
delConfirmSubstitution = (data) => {
    // console.log('delSub>>',data);
    confirmSwalTwoButton(
        CONFIRM_DELETE_MSG, 
        DEL_MSG, 
        CANCEL_MSG, 
        true,
        ()=>{delSubstitution(data)}
    );

}

delSubstitution = (data) => {
    const param = {
        REQ_MODE: CASE_DELETE,
        REQ_TARGET: STOCK_BILL,
        NIS_FROM_CPPS_ID: data.NIS_FROM_CPPS_ID,
        NIS_TO_CPPS_ID : data.NIS_TO_CPPS_ID,
    };
    // console.log('delSubstitu param>>',param);
    callAjax(BILL_URL, 'POST', param).then((result)=>{
        // console.log('delSub SQL>>',result);
        const res = JSON.parse(result);
            if(res.status == OK){
                confirmSwalOneButton('삭제에 성공하였습니다.','확인',()=>{
                    getSettingData();
                });
            }else{
                alert(ERROR_MSG_1200);
            } 
    });
}