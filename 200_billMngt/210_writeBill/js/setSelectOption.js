
/**
 * 해당 보험사 과실율 옵션세팅
 */
setRatioOption = (type) => {
    const firstVal = 10;
    let ratioHtml = '<option value="">선택</option>';
    for(let i = 0; i <= 10 ; ++i){
        ratioHtml += "<option value='"+i*firstVal+"'>"+i*firstVal+"%</option>";
    }
    return ratioHtml;
}


 /**
 * 등록창에서 보험사정보 가져오기
 * @param {objID : 타겟 아이디}
 * @param {isMultiSelect : 멀티셀렉틍니지 아닌지}
 * @param {defaultText : 기본옵션 텍스트}
 */
getInsuInfo = (objID, isMultiSelect, defaultText) => {
    const data = {
        REQ_MODE: OPTIONS_IW,
    };
    callAjax('/common/searchOptions.php', 'GET', data).then((result)=>{
        if(isJson(result)){
            const res = JSON.parse(result);
            if(res != undefined || res.length > 0){
                insuArray = res;

                if($('#REQ_MII_ID').val()!=''){
                    REQ_MII_ID = $('#REQ_MII_ID').val();
                }

                let optionHtml = "<option value=''>선택</option>";   
                if(defaultText != undefined){
                    optionHtml = "<option value=''>"+defaultText+"</option>";
                }
                res.map((item, index)=>{
                    optionHtml += "<option value="+item.MII_ID+">"+item.MII_NM+"</option>";
                });
                
                $("#" + objID).html(optionHtml); 

                const idType = objID.split("_").length == 2 ? '' : '_';
                if($('#MII_ID_UPDATE'+idType).val() == ''){
                    $("#" + objID).val("");
                }else{
                    $("#" + objID).val($('#MII_ID_UPDATE'+idType).val());
                }
            }
        }else{
            alert(ERROR_MSG_1200);
        }
    });
}

/**
 * 선택된 보험사에 등록된 보험사담당자 정보 가져오기
 * @param {type : 자차인지 대물인지}
 * @param {IS_UPDATE : 수정인지 아닌지}
 */
getInsuManageInfo = (type, MGG_ID, IS_UPDATE) => {
    const id = (type == undefined) ? '' : type;
    const data = {
        REQ_MODE: GET_INSU_CHARGE_INFO,
        MII_ID : ($('#MII_ID'+id).val() == null) ? $('#MII_ID_UPDATE'+id).val()  : $('#MII_ID'+id).val() ,
        MGG_ID : (MGG_ID != undefined) ? MGG_ID : ''
    };
    $('#MII_NM'+id).val($('#MII_ID'+id+' option:selected').text());

    const garageInsuUrl = '../../500_gw/532_gwCpst/act/gwCpst_act.php';
    callAjax(garageInsuUrl, 'GET', data).then((result)=>{
        if(isJson(result)){
            const res = JSON.parse(result);
            if(res.status == OK){
                const data = res.data;
                $("#NBI_FAX_NUM"+id).attr('readonly',false);
                $("#NBI_CHG_NM"+id).attr('disabled',false);
                $("#NBI_CHG_NM"+id).attr('placeholder', '입력/자동입력');
                autoCompleteInsuManage(data, id);

                const garageInsuManage = data.find((item)=>{  // 공업사에 해당보상담당자가 있으면 그보상담당자로 보여주기
                    return item.NGIC_CONFIRM_YN == YN_Y;
                });
                if(garageInsuManage != undefined && IS_UPDATE == undefined){
                    const data = {
                        claimCount : garageInsuManage.CLAIM_CNT,
                        insuType : garageInsuManage.RICT_ID,
                        type : garageInsuManage.RICT_NM,
                        time : garageInsuManage.WRT_DTHMS,
                        fax : garageInsuManage.NIC_FAX_AES,
                        lable : garageInsuManage.NIC_ID,
                        name : garageInsuManage.NIC_NM_AES,
                        value : garageInsuManage.NIC_NM_AES +" ["+garageInsuManage.NIC_FAX_AES+"]"
                    }
                    
                    setAutocompleteInsuManage(data, id);
                }else{
                    if(IS_UPDATE == undefined){
                        $('#SELECT_NBI_CHG_NM'+id).val('');    
                        $('#RICT_ID'+id).val('');  
                        $('#RICT_NM'+id).html('');                 
                        $('#NBI_CHG_NM'+id).val('');                                         
                        $("#NBI_FAX_NUM"+id).val('');                  // 2.보험사 선택 담당자 팩스
                    }   
                }
            }else{
                $("#NBI_FAX_NUM"+id).attr('readonly',true);
                $("#NBI_CHG_NM"+id).attr('disabled',true);
                $("#NBI_CHG_NM"+id).attr('placeholder', '보험사를 선택해주세요.');
                $('#SELECT_NBI_CHG_NM'+type).val('');                          
                $('#NBI_CHG_NM'+id).val('');
            }
        }else{
            alert(ERROR_MSG_1200);
        }
    });
}

/** 4.2 기타부품선택 옵션 */
getEtcPartsOption = (dataArray, selectedValue) => {
    let optionHtml = '<option value="">사용안함</option>'
    dataArray.map((data)=>{
        if(data.NGP_DEFAULT_YN  != undefined){
            if(selectedValue == data.CPPS_ID){
                optionHtml += '<option selected name="'+data.CPPS_PRICE+'" value='+JSON.stringify({CPPS_ID : data.CPPS_ID, CPP_ID : data.CPP_ID})+'>'+data.CPPS_NM+'</option>';
            }else{
                optionHtml += '<option name="'+data.CPPS_PRICE+'" value='+JSON.stringify({CPPS_ID : data.CPPS_ID, CPP_ID : data.CPP_ID})+'>'+data.CPPS_NM+'</option>';
            }
             
        }
    });
    
    return optionHtml;
}