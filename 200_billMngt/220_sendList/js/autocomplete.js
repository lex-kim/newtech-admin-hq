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
                value : data.CCC_CAR_NM+" ["+data.CCC_NM+"]"
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
                setGaragePartsData(ui.item.lable);         // 사용부품항목
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
    $('#NBI_CHG_NM'+objId).val(item.value);                                         
    $("#NBI_FAX_NUM"+objId).val(item.fax);                  // 2.보험사 선택 담당자 팩스
    $('#INSU_MANAGE_REGIST_DATE'+objId).html(item.time);    // 2.보험사 선택 담당등록일 
}

/** 보험사 autocomplete
 * @param {crrArray : autocomplete를 할 리스트들}
*/
autoCompleteInsu= (crrArray, objId) => {
    $("#"+objId).autocomplete({
        minLength : 1,
        source: crrArray.map((data, index) => {
            return {
                MII_ID : data.MII_ID,
                value : data.MII_NM
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