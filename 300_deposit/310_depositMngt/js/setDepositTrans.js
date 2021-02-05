onLoadPopupSetting = () =>{
    // console.log('showTransitModal>>', data);
    // $("#transForm").each(function() {  
    //     this.reset();  
    // });  

    // $('#NBIT_TO_MII_ID').change(()=>{
    //     getInsuManageInfo();
    // });
    // $('#NB_MII_ID').change(()=>{
    //     getInsuManageInfo();
    // });

    // getRefOptions(GET_REF_INSU_CHAGE_TYPE, 'RICT_ID', false, false);  
    // setIwOptions('NBIT_TO_MII_ID');   
    // getInsuManageInfo();

    getInsuInfoPopup();
    getRefOptions(GET_REF_INSU_CHAGE_TYPE, 'RICT_ID', false);        // 담당구분  

    $('#NBIT_TO_MII_ID').change(()=>{
        getInsuManageInfo();
    });

    /** 이첩저장 */
    $("#transForm").submit(function(e){
        e.preventDefault();
        confirmSwalTwoButton('이첩을 진행하시겠습니까?','확인', '취소', false , ()=>{
             addTransData();
        });
    });

}

/**
 * 선택된 보험사에 등록된 보험사담당자 정보 가져오기
 */
getInsuManageInfo = () => {
    const param = {
        REQ_MODE: GET_INSU_CHARGE_INFO,
        MII_ID : $('#NBIT_TO_MII_ID').val()
    };
    
    callAjax(DEPOSIT_URL, 'GET', param).then((result)=>{
        if(isJson(result)){
            const res = JSON.parse(result);

            if(res.status == OK){
                const data = res.data;
                if(data.length > 0){
                    $("#NBIT_TO_NIC_NM").attr('disabled',false);
                    autoCompleteInsuPopup(data, 'NBIT_TO_NIC_NM');
                    $("#NBIT_TO_NIC_NM").focus();
                }else{
                    $("#NBIT_TO_NIC_NM").attr('disabled',true);
                }
            }else{
                $("#NBIT_TO_NIC_NM").attr('disabled',true);
            }
        }else{
            alert(ERROR_MSG_1200);
        }
    });
}

getInsuInfoPopup = () => {
    const data = {
        REQ_MODE: OPTIONS_IW,
    };
    callAjax('/common/searchOptions.php', 'GET', data).then((result)=>{
        if(isJson(result)){
            const res = JSON.parse(result);
            if(res != undefined || res.length > 0){
                setSelectOption(res, 'NBIT_TO_MII_ID', false);
               
            }
        }else{
            alert(ERROR_MSG_1200);
        }
    });
}


function setAutocompleteInsuManagePopup(item, objId){
    $('#SELECT_NBI_CHG_NM'+objId).val(item.lable);    
    $('#RICT_ID'+objId).val(item.insuType);  
    $('#RICT_NM'+objId).html(item.type);    
    $('#INSU_MANAGE_COUNT'+objId).html(item.claimCount);      // 2.보험사 선택 당월청구건수                
    $('#NBI_CHG_NM'+objId).val(item.value);                                         
    $("#NBI_FAX_NUM"+objId).val(item.fax);                  // 2.보험사 선택 담당자 팩스
    $('#INSU_MANAGE_REGIST_DATE'+objId).html(item.time);    // 2.보험사 선택 담당등록일 
}

/** 보험사 autocomplete
 * @param {crrArray : autocomplete를 할 리스트들}
*/
autoCompleteInsuPopup= (crrArray, objId) => {
    resetInsuManager();
    $("#"+objId).autocomplete({
        minLength : 1,
        source: crrArray.map((data, index) => {
            return {
                NIC_FAX_AES : data.NIC_FAX_AES,
                NIC_TEL1_AES : data.NIC_TEL1_AES,
                RICT_ID : data.RICT_ID,
                NIC_ID : data.NIC_ID,
                NIC_NM : data.NIC_NM_AES,
                value : data.NIC_NM_AES +'('+data.RICT_NM+')'
            };
        }),
        focus: function(event, ui){ 
            return false;
        },
        select: function(event, ui) {
            setAutocompleteInsuManagePopup(ui.item, objId);
            $('#NBIT_TO_NIC_NM').blur(); 
            $('#NBIT_TO_NIC_NM').val(ui.item.value); 
            $('#NBIT_TO_NIC_NM_AES').val(ui.item.NIC_NM);
            $('#NBIT_TO_NIC_ID').val(ui.item.NIC_ID);              // autocomplete에서 선택한 보상담당자 테이블 아이디
            $('#NBIT_TO_NIC_FAX_AES').val(ui.item.NIC_FAX_AES); 
            $('#NBIT_TO_NIC_PHONE_AES').val(ui.item.NIC_TEL1_AES); 
            $('#RICT_ID').val(ui.item.RICT_ID); 

            if(ui.item.RICT_ID != ''){
                $('#RICT_ID').val(ui.item.RICT_ID);
            }
            return false;
        }
    }).on('focus', function(){ 
        $(this).autocomplete("search"); 
    });
}

resetInsuManager = () => {
    $('#NBIT_TO_NIC_NM').val(''); 
    $('#NBIT_TO_NIC_NM_AES').val('');
    $('#NBIT_TO_NIC_ID').val('');   
    $('#NBIT_TO_NIC_FAX_AES').val(''); 
    $('#NBIT_TO_NIC_PHONE_AES').val(''); 
    $('#RICT_ID').val(''); 
}

addTransData = () => {
    let form = $("#transForm")[0];
    let formData = new FormData(form);
    formData.append('REQ_MODE', CASE_CREATE);
    formData.append('REQ_TARGET', MODE_TARGET_IW);

    callFormAjax(DEPOSIT_URL, 'POST', formData).then((result)=>{
        if(isJson(result)){
            const data = JSON.parse(result);
            if(data.status == OK){
                confirmSwalOneButton('저장되었습니다.', OK_MSG ,()=>{
                    window.opener.getList();
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