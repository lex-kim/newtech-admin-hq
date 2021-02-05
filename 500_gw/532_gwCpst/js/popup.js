/**
 * 보상담당등록 팝업 로드될때
 */

let dataArray = [];      // autocomplete 저장할 배열

onLoadRegistPopup = () => {
    setRegistPopSelectOPtion();       // select에 값 넣어주는 함수
    setUpdateData();                  // update시

     /** 보상담당등록창에서 공업사 눌렀을 때 */
     $('#MII_ID').change(function(){
            resetInsuChargeData();
            if($(this).val() != ''){
                getInsuManageInfo();
            }
    });
    
     /** 등록/수정 */
     $('#reward_form').submit(function(e){
        e.preventDefault();

        if($('#REQ_TYPE').val() != ''){
            if(validate()){   //등록
                resultSetData(CASE_CREATE, ADD_SUCCESS_MSG);
            }  
        }else{
            if(validate()){  //수정
                 confirmSwalTwoButton('수정 하시겠습니까?','확인', '취소', false ,()=>resultSetData(CASE_UPDATE, UPDATE_SUCCESS_MSG));
            }
        }
    });
    
}

/**
 * 보상담당자 변경 시 선택값을 저장하는 변수값을 초기화
 */
onChangeInsuNM = () => {
    $('#NIC_ID').val('');
}

/**
 * 수정/등록상황에 맞게 submit 텍스트 및 이벤트 수정
 */
setUpdateData = () => {
    if($('#MGG_ID').val() != '' && $('#REQ_TYPE').val() == ''){
        $('#add-modal-btn').html("수정");
        $("#NIC_NM_AES").attr('disabled',false);
    }else{
        $('#add-modal-btn').html("등록");
        $("#NIC_NM_AES").attr('disabled',true);
    }
}

/**
 * 보상담당자 정보 초기화
 */
resetInsuChargeData = () => {
    $('.insuCharge').each(function(){
        $(this).val('');
        $(this).attr('disabled', true);
    });
    $('#NIC_ID').val('');
    dataArray = [];
}

/**
 * 선택된 보험사에 등록된 보험사담당자 정보 가져오기
 */
getInsuManageInfo = () => {
    const data = {
        REQ_MODE: GET_INSU_CHARGE_INFO,
        MII_ID : $('#MII_ID').val()
    };
    callAjax(ajaxUrl, 'GET', data).then((result)=>{
        if(isJson(result)){
            const res = JSON.parse(result);

            if(res.status == OK){
                const data = res.data;
                if(data.length > 0){
                    $("#NIC_NM_AES").attr('disabled',false);
                    dataArray = data;
                    autoComplete();
                }else{
                    $("#NIC_NM_AES").attr('disabled',true);
                }
            }else{
                resetInsuChargeData();
            }
        }else{
            alert(ERROR_MSG_1200);
        }
    });
}

autoComplete = () => {
    $("#NIC_NM_AES").autocomplete({
        minLength : 1,
        source: dataArray.map((data, index) => {
            return {
                lable : JSON.stringify({
                    NIC_FAX_AES :  data.NIC_FAX_AES,
                    NIC_TEL1_AES : data.NIC_TEL1_AES,
                    NIC_NM : data.NIC_NM_AES,
                    NIC_ID : data.NIC_ID
                }),
                value : data.NIC_NM_AES+" ["+phoneFormat(data.NIC_FAX_AES)+"]"
            };
        }),
        focus: function(event, ui){ 
            return false;
        },
        select: function(event, ui) {
            let strArea = ui.item.value;
            const data = JSON.parse(ui.item.lable);

            // $(this).blur(); 
            $('#NIC_NM_AES').val(data.NIC_NM);
            $('#NIC_ID').val(data.NIC_ID);              // autocomplete에서 선택한 보상담당자 테이블 아이디
            $('#NIC_FAX_AES').val(phoneFormat(data.NIC_FAX_AES) ); 
            $('#NIC_TEL1_AES').val(phoneFormat(data.NIC_TEL1_AES) ); 

            $("#NIC_NM_AES").blur();
            $("#NIC_NM_AES").autocomplete('close');
            return false;
        }
    }).on('focus', function(){ 
        $(this).autocomplete("search"); 
    });
}

onchangeGarage = (evt) => {
    const charCode = (evt.which) ? evt.which : event.keyCode;  
    
    if(charCode != 13){
        $('#MGG_ID').val("");
    }
}

autoCompleteGarage = (dataArray) => {
    $("#MGG_NM").autocomplete({
        minLength : 1,
        source: dataArray.map((item, index) => {
            if($('#MGG_ID').val() != '' && ($('#MGG_ID').val() == item.MGG_ID)){   // 공업사 값이 존재할 경우 등록/수정에 공업사 표시되게
                $('#MGG_ID').val(item.MGG_ID);
                $('#MGG_OFFICE_TEL').val(phoneFormat(item.MGG_OFFICE_TEL));
                $('#MGG_CLAIM_NM_AES').val(item.MGG_CLAIM_NM_AES);
                $('#MGG_CLAIM_TEL_AES').val(phoneFormat(item.MGG_CLAIM_TEL_AES));
                $('#MGG_NM').val(item.MGG_NM);
            }
            return {
                lable : JSON.stringify({
                    MGG_ID : item.MGG_ID, 
                    MGG_TEL : item.MGG_OFFICE_TEL,
                    MGG_CLAIM_NM_AES : item.MGG_CLAIM_NM_AES,
                    MGG_CLAIM_TEL_AES : item.MGG_CLAIM_TEL_AES
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
            $('#MGG_OFFICE_TEL').val(phoneFormat(data.MGG_TEL));
            $('#MGG_CLAIM_NM_AES').val(data.MGG_CLAIM_NM_AES);
            $('#MGG_CLAIM_TEL_AES').val(phoneFormat(data.MGG_CLAIM_TEL_AES));

            $("#MGG_NM").blur();
            $("#MGG_NM").autocomplete('close');
            return false;
        }
    }).on('focus', function(){ 
        $(this).autocomplete("search"); 
    });
}

/**
  * 보상담당자 신규등록 후 콜백함수
  */
callbackAddData = () => {
    getInsuManageInfo();
}

validate = () => {
    if($('#NIC_NM_AES').attr('disabled')){
        alert('해당보험사에 보상담당자를 신규등록 후 선택해주셔야 됩니다.');
        openNewPopup($('#MII_ID').val());
        return false;
    }else{
        if($('#NIC_ID').val() == ''){
            alert('보상담당자를 선택해주셔야 됩니다.');
            $('#NIC_NM_AES').focus();

            return false;
        }else{
            return true;
        }
    }
}

/**
 * 수정/등록 실제 기능해주는 함수(공용)
 */
resultSetData = (reqMode, resultText) =>{
    let form = $("#reward_form")[0];
    let formData = new FormData(form);
    formData.append('REQ_MODE', reqMode);

    callFormAjax(ajaxUrl, 'POST', formData).then((result)=>{
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
