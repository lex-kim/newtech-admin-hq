const CON_URL = '/700_sa/740_consign/741_category/act/category_act.php';

setConsignType = (objID, ptext, pSelectVal) =>{
    const param = {
        REQ_MODE : GET_PARTS_TYPE
    }

    callAjax(CON_URL, 'GET', param).then((result)=>{
        const res = JSON.parse(result);
        console.log('setConsignType',res);
        if(res.status == OK){
            let optionText = '<option value="">'+ptext+'</option>';
            res.data.map((item)=>{
                optionText += '<option value="'+item.ID+'">'+item.NM+'</option>';
            });
            $('#'+objID).html(optionText);

            if(pSelectVal != undefined){
                $('#'+objID).val(pSelectVal);
            }
        
        }else{
            console.log("위탁장비 구분을 조회할 수 없습니다.");
        }
    })

}

/**
 * 장비명/장비번호 검색
 * @param {String} objID 태그아이디
 * @param {String} ptext 초기텍스트
 * @param {String} pSelectVal 선택값
 * @param {boolean} isSearch 선택/입력 여부 (true:선택/입력 false:선택) 
 */
setConsign = (objID, ptext, pSelectVal, isSearch) => {
    const param = {
        REQ_MODE : OPTIONS_CPPS
    }

    callAjax(CON_URL, 'GET', param).then((result)=>{
        const res = JSON.parse(result);
        console.log('setConsign',res);
        if(res.status == OK){
            let optionText = '<option value="">'+ptext+'</option>';
            res.data.map((item)=>{
                if(isSearch){ 
                    optionText += '<option value="'+item.ID+'">'+item.NM+'['+item.ID+']'+'</option>';
                }else{
                    optionText += '<option value="'+item.ID+'">'+item.NM+'</option>';
                }
            });
            $('#'+objID).html(optionText);
            
            if(pSelectVal != undefined){
                $('#'+objID).val(pSelectVal);
            }
            if(isSearch){
                $('#'+objID).selectpicker('refresh');
            }
        
        }else{
            console.log("위탁장비를 조회할 수 없습니다.");
        }
    })

}


/**
 * 지역구분에 해다되는 주소 옵션
 * @param {objID : 조회할 지역구분 정보}
 * @param {targetID : 조회한 구분을 넣어줄 select ID}
 * @param {isMultiSelector : 멀티셀렉터 인지 아닌지}
 */
setCRR_ID_Option = (objId, targetID, isMultiSelector, isTotal) => {
    getCRRInfo($('#'+objId).val(), isTotal).then((data)=>{
        if(data.status == OK){
            const dataArray = data.data;
            if(dataArray.length > 0){
                let optionText = '';
        
                dataArray.map((item, index)=>{
                    let ITEM_ID = item.CRR_1_ID+","+item.CRR_2_ID+","+item.CRR_3_ID;
                    optionText += '<option value="'+ITEM_ID+'">'+item.CRR_NM+'</option>';
                });
    
                $('#'+targetID).html(optionText);
                console.log("optionTextoptionText==>",optionText);
                console.log("optionTextoptionText==>",targetID);
                
                if(isMultiSelector){
                    $('#'+targetID).selectpicker('refresh');
                }
            }else{
                $('#'+targetID).html('');
                
                if(isMultiSelector){
                    $('#'+targetID).selectpicker('refresh');
                }
            }
        }
    });
}