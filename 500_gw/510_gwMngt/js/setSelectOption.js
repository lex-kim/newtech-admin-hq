/**
 * 등록/수정 팝업에서 select 옵션 설정해주는 js
 * 
 */
let crrArray = [];
let loadingSelect = [];
 /**
  * body onload 이벤트로 인해 가장 먼저 호출
  */
setSelctOption = async() => {
    //  /** 멀티셀렉터 */
     $('.selectpicker').selectpicker({
        style: 'form-control',
    });

    /** 주소검색 버튼 동적으로 세팅(위치) */
    const addrLablePosition = $('#MGG_ZIP_OLD').offset().top - $('#addrPosition').offset().top ;
    $('.addrBtn').css('top',addrLablePosition);
   
    await getDivisionOption('CSD_ID', false);                                                 // 지역구분
    await getPointGrade('SLL_ID', '=쇼핑몰등급=');
    await getRefOptions(GET_REF_EXP_TYPE, 'RET_ID', false, false);                            // 청구식
    await getRefOptions(GET_REF_PAY_EXP_TYPE, 'RPET_ID', false, true);                        // 지급식
    await getRefOptions(GET_REF_NMI_INSU_TYPE, 'MII_ID', true, false);                        // 지급식 적용 보험사
    await getRefOptions(GET_REF_AOS_TYPE, 'RAT_ID', false, false);                            // AOS 권한
    await getRefOptions(GET_REF_MAIN_TARGET_TYPE, 'RMT_ID', false, true);                     // 주요정비
    await getRefOptions(GET_REF_OWN_TYPE, 'ROT_ID', false, true);                             // 자가여부  
    await getRefOptions(GET_REF_CONTRACT_TYPE, 'RCT_ID', false, false);                       // 거래여부  
    await getRefOptions(GET_REF_STOP_REASON_TYPE, 'RSR_ID', false, false);                    // 거래중단사유  
    await getRefOptions(GET_REF_SCALE_TYPE, 'RST_ID', false, true);                           // 규모  
    await getRefOptions(GET_REF_REACTION_TYPE, 'RRT_ID', false, false);                       // 반응  
    $('#mapLinkBtn').hide();
    
    loadingSelect = setInterval(function() {
        let isReadyLoading = true;
        $('.dynSelect').each(function(){
            if($('#'+$(this).attr('id')+" option").size() <= 1){
                isReadyLoading = false;
            }
        });
        if(isReadyLoading){
            clearInterval(loadingSelect);
            onLoadRegisterPopup();
        }    
     }, 250);   
    
}


/** 지역정보 값 초기화 (해당지여구분 데이터가 존재하지 않을경우)
 * @param {targetObj : 조회한 지역구분에 데이터가 있을 경우 disable 해제시켜줄 타겟 인풋}
 * @param {selectObj : autocomplete에서 선택한 값들을 저장할 변수}
*/
resetCRRValue = (targetObj, selectObj) => {
    $('#'+targetObj).attr('disabled',true);
    $('#'+targetObj).val('');
    $('#'+selectObj).val('');
    crrArray = [];
}



/** 지역 구분 클릭시 데이터가 있으면 disabled이 false 처리되고 검색 가능 
 * @param {obj : 선택한 지역구분 데이터 }
 * @param {targetObj : 조회한 지역구분에 데이터가 있을 경우 disable 해제시켜줄 타겟 인풋}
 * @param {selectObj : autocomplete에서 선택한 값들을 저장할 변수}
*/
setCRR_ID = (obj, targetObj ,selectObj, isTotal) => {
    getCRRInfo($('#'+obj).val(), isTotal).then((data)=>{
        if(data.status == OK){
            const dataArray = data.data;
            crrArray = dataArray;
            if(dataArray.length > 0){
                $('#'+targetObj).attr('disabled',false);
                autoCompleteCRR(targetObj, selectObj, dataArray);
            }else{
                resetCRRValue(targetObj, selectObj);
            }
        } else{
            resetCRRValue();
        }  
    });
     
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
                // console.log("optionTextoptionText==>",optionText);
                // console.log("optionTextoptionText==>",targetID);
                
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
