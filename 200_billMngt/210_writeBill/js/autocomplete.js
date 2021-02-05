/**
 * autocomplete 함수 모아둔 js
 */

/**
 * 차량명 autocomplete
 * @param {carArray : 차량정보}
 */
autoCompleteCarInfo = (carArray) => {
    $("#CCC_NM").autocomplete({
        minLength : 1,
        source: carArray.map((data, index) => {
            return {
                carType : data.CCC_CAR_TYPE_CD,
                lable : data.CCC_ID,
                value : data.CCC_CAR_NM
            };
        }),
        focus: function(event, ui){ 
            return false;
        },
        select: function(event, ui) {
            let strArea = ui.item.value;
            $('#CCC_ID').val(ui.item.lable);                           //선택한 데이터 value를 저장할곳(hidden)
            $('#RCT_CD').val(ui.item.carType);
            $("#CCC_NM").val(strArea);                 //선택한 주소를 보여줄 input
            
            return false;
        }
    }).on('focus', function(){ 
        $(this).autocomplete("search"); 
    });
}


/** 수리공장명 autocomplete
 * @param {crrArray : autocomplete를 할 리스트들}
*/
autoCompleteGarageInfo= (crrArray) => {
    $("#NB_GARAGE_NM").autocomplete({
        minLength : 1,
        source: crrArray.map((data, index) => {
            return {
                name : data.MGG_NM,
                lable : data.MGG_ID,
                value : data.MGG_NM+" ["+data.MGG_ID+"]"
            };
        }),
        focus: function(event, ui){ 
            return false;
        },
        select: function(event, ui) {
            let strArea = ui.item.value;
            /** 다른 공업사를 눌렀을 경우에만 해당공업사 사용부품 가져올 수 있게. */
            if(ui.item.lable != $('#SELECT_NB_GARAGE_NM').val()){
                setGaragePartsData(ui.item.lable);                    // 사용부품항목
                $('.MII_ID_LIST').each(function(){                    // 선택된 보험사가 있을 경우 해당된 공업사에 보상담당자 여부 확인
                    if($(this).is(':visible')){
                        const id = $(this).attr('id').split("_")[3];
                        const type = (id == RLT_MY_CAR_TYPE) ? "" : "_";

                        if(!$('#MII_ID'+type).val() == ''){
                            getInsuManageInfo(type, ui.item.lable);     // 해당보험사 담당자 정보
                        }
                    }
                });
            }
            $('#SELECT_NB_GARAGE_NM').val(ui.item.name);  
            $('#SELECT_NB_GARAGE_ID').val(ui.item.lable);                           //선택한 데이터 value를 저장할곳(hidden)
            $('#REPAIRE_GARAGE_NM').val(strArea);                                   // 수리차량정보에 자동입력
            $("#NB_GARAGE_NM").val(strArea);                 //선택한 주소를 보여줄 input
            $('#workPartsListTable').html(workPartsTableHtml);

            return false;
        }
    }).on('focus', function(){ 
        $(this).autocomplete("search"); 
    });
}


function setAutocompleteInsuManage(item, objId){
    $('#SELECT_NBI_CHG_NM'+objId).val(item.lable);    
    $('#RICT_ID'+objId).val(item.insuType);  
    $('#RICT_NM'+objId).html(item.type);    
    $('#INSU_MANAGE_COUNT'+objId).html(item.claimCount);      // 2.보험사 선택 당월청구건수                
    $('#NBI_CHG_NM'+objId).val(item.name);                                         
    $("#NBI_FAX_NUM"+objId).val(phoneFormat(item.fax));                  // 2.보험사 선택 담당자 팩스
    $('#INSU_MANAGE_REGIST_DATE'+objId).html(item.time);    // 2.보험사 선택 담당등록일 
}

/** 보험사 담당자 autocomplete
 * @param {crrArray : autocomplete를 할 리스트들}
 * @param {objId : 자차인지 대물인지}
*/
autoCompleteInsuManage= (crrArray, objId) => {
    $("#NBI_CHG_NM"+objId).autocomplete({
        minLength : 1,
        source: crrArray.map((data, index) => {
            return {
                claimCount : data.CLAIM_CNT,
                insuType : data.RICT_ID,
                type : data.RICT_NM,
                time : data.WRT_DTHMS,
                fax : data.NIC_FAX_AES,
                lable : data.NIC_ID,
                name : data.NIC_NM_AES,
                value : data.NIC_NM_AES+" ["+phoneFormat(data.NIC_FAX_AES)+"]"
            };
        }),
        focus: function(event, ui){ 
            return false;
        },
        select: function(event, ui) {
            setAutocompleteInsuManage(ui.item, objId);
            return false;
        }
    }).on('focus', function(){ 
        $(this).autocomplete("search"); 
    });
}