/**
 * 보상담당등록 팝업 로드될때
 */

let dataArray = [];      // autocomplete 저장할 배열

onLoadRegistPopup = () => {
    getGarageInfo();
    
     /** 등록/수정 */
    //  $('#reward_form').submit(function(e){
    //     e.preventDefault();

    //     if($('#REQ_TYPE').val() != ''){
    //         if(validate()){   //등록
    //             resultSetData(CASE_CREATE, ADD_SUCCESS_MSG);
    //         }  
    //     }else{
    //         if(validate()){  //수정
    //              confirmSwalTwoButton('수정 하시겠습니까?','확인', '취소', false ,()=>resultSetData(CASE_UPDATE, UPDATE_SUCCESS_MSG));
    //         }
    //     }
    // });
    
}


onchangeGarage = (evt) => {
    const charCode = (evt.which) ? evt.which : event.keyCode;  
    
    if(charCode != 13){
        $('#MGG_ID').val("");
    }
}



/**
 * 공업사(청구담당자 포함)정보 가져오기 
 */
getGarageInfo = () => {
    const data = {
        REQ_MODE: GET_GARAGE_CLAIM_INFO,
    };
    callAjax(POINT_URL, 'GET', data).then((result)=>{
        if(isJson(result)){
            const res = JSON.parse(result);
            autoCompleteGarage(res.data);
            // setGarageSelectOption(res.data, 'MGG_ID', true); 
        }else{
            alert(ERROR_MSG_1100);
        }
       
    });
}



autoCompleteGarage = (dataArray) => {
    $("#MGG_NM").autocomplete({
        minLength : 1,
        source: dataArray.map((item, index) => {
            if($('#MGG_ID').val() != '' && ($('#MGG_ID').val() == item.MGG_ID)){   // 공업사 값이 존재할 경우 등록/수정에 공업사 표시되게
                $('#MGG_ID').val(item.MGG_ID);
                $('#MGG_NM').val(item.MGG_NM);
            }
            return {
                lable : JSON.stringify({
                    MGG_ID : item.MGG_ID
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


/**
 * 수정/등록 실제 기능해주는 함수(공용)
 */
resultSetData = (reqMode, resultText) =>{
    let form = $("#reward_form")[0];
    let formData = new FormData(form);
    formData.append('REQ_MODE', reqMode);

    callFormAjax(POINT_URL, 'POST', formData).then((result)=>{
        if(isJson(result)){
            const data = JSON.parse(result);
            if(data.status == OK){
                confirmSwalOneButton(resultText, OK_MSG,()=>{
                    if(reqMode == CASE_CREATE){
                        if(opener.location.pathname == PATH_NAME){
                            window.opener.getList(1);
                        }else{
                            window.opener.getList();
                        }
                    }else{
                        window.opener.getList();
                    }
                   
                   
                    self.close();
                });
            }else if(data.status == CONFLICT){
                confirmSwalOneButton(data.message, OK_MSG,()=>{
                    $('#NIC_NM_AES').val('');
                    $('#NIC_NM_AES').focus();
                });
            }else{
                alert(ERROR_MSG_1100);
            } 
        }else{
            alert(ERROR_MSG_1200);
        }
        
    });   
}
