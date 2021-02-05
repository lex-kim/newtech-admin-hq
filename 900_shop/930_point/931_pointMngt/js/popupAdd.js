/**
 * 포인트 적립 및 사용 등록창 
 */

let dataArray = [];      // autocomplete 저장할 배열
const POINT_ADD ="60119";   //발생확정, 적립
const POINT_USE ="60129";   //사용확정, 사용

onLoadRegistPopup = () => {
    getGarageInfo();
    getPointStatus();
    getRefOptions(REF_POINT_TYPE, 'ADD_RPT_ID', false, false);           //포인트종류  

    $( "#UPDATE_DTHMS" ).datepicker(
        getDatePickerSetting()
    );

    /** 등록/수정 */
    $('#pointAddForm').submit(function(e){
        e.preventDefault();
        console.log('$("#ADD_MGG_ID").val()',$("#ADD_MGG_ID").val());
        if($("#ADD_MGG_ID").val() == '' || $("#ADD_MGG_ID").val() == undefined){
            alert('자동완성을 이용하여 공업사를 선택해 주세요.');
        }else {
            confirmSwalTwoButton('저장 하시겠습니까?','확인', '취소', false ,()=>resultSetData());
        }
        // if($('#REQ_TYPE').val() != ''){
        //     if(validate()){   //등록
        //         resultSetData(CASE_CREATE, ADD_SUCCESS_MSG);
        //     }  
        // }else{
        //     if(validate()){  //수정
        //     }
        // }
    });
    
}


onchangeGarage = (evt) => {
    const charCode = (evt.which) ? evt.which : event.keyCode;  
    
    if(charCode != 13){
        $('#ADD_MGG_ID').val("");
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
    $("#ADD_MGG_NM").autocomplete({
        minLength : 1,
        source: dataArray.map((item, index) => {
            if($('#ADD_MGG_ID').val() != '' && ($('#ADD_MGG_ID').val() == item.MGG_ID)){   // 공업사 값이 존재할 경우 등록/수정에 공업사 표시되게
                $('#ADD_MGG_ID').val(item.MGG_ID);
                $('#ADD_MGG_NM').val(item.MGG_NM);
            }
            return {
                lable : JSON.stringify({
                    MGG_ID : item.MGG_ID
                }),
                value : item.MGG_NM+"("+bizNoFormat(item.MGG_BIZ_ID)+")"
            };
            
        }),
        focus: function(event, ui){ 
            return false;
        },
        select: function(event, ui) {
            let strArea = ui.item.value;
            const data = JSON.parse(ui.item.lable);

            $('#ADD_MGG_NM').val(strArea);
            $('#ADD_MGG_ID').val(data.MGG_ID);

            $("#ADD_MGG_NM").blur();
            $("#ADD_MGG_NM").autocomplete('close');
            return false;
        }
    }).on('focus', function(){ 
        $(this).autocomplete("search"); 
    });
}

/**
 * 포인트 구분 가져오기
 */
getPointStatus = () => {
    const data = {
        REQ_MODE: REF_POINT_STATUS,
    };
    callAjax(POINT_URL, 'GET', data).then((res)=>{

        const resPoint = JSON.parse(res).data;
        // console.log("result>",resPoint);
        let optionHtml = "<option value=''>선택</option>"; 
        resPoint.map((item, i)=>{
            item.RPS_NM = item.RPS_NM.substring(0,2);
            optionHtml += "<option value="+item.RPS_ID+">"+item.RPS_NM+"</option>";
        });
        $("#ADD_RPS_ID").html(optionHtml); 

    });
}



/**
 * 수정/등록 실제 기능해주는 함수(공용)
 */
resultSetData = () =>{

    if( $("#ADD_RPS_ID").val() == POINT_USE){
        $("#ADD_NPH_POINT").val( $("#ADD_NPH_POINT").val()*-1) ;
        // console.log('$("ADD_NPH_POINT").val(): ',$("#ADD_NPH_POINT").val(), '////' ,typeof $("#ADD_NPH_POINT").val());
    }
    let form = $("#pointAddForm")[0];
    // console.log('formData',form);
    
    let formData = new FormData(form);
    formData.append('REQ_MODE', CASE_CREATE);

    callFormAjax(POINT_URL, 'POST', formData).then((result)=>{
        if(isJson(result)){
            const data = JSON.parse(result);
            if(data.status == OK){
                confirmSwalOneButton(SAVE_SUCCESS_MSG, OK_MSG,()=>{
                    window.opener.getList(1);
                    self.close();
                });
            }else{
                alert(ERROR_MSG_1100);
            } 
        }else{
            alert(ERROR_MSG_1200);
        }
        
    });   
}
