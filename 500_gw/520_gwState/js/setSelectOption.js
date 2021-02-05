/**
 * 검색 selectbox 옵션 세팅
 */

setSelctOption = () => {

    getDivisionOption('CSD_ID_SEARCH', true);                   // 지역구분
    setCRRSelect('CRR_ID_SEARCH', true);
    /** 지역 검색 */
                                               
    getRefOptions(GET_REF_CONTRACT_TYPE, 'RCT_ID_SERACH', false, false);                       // 거래여부  
}

setCRRSelect = (targetID, isMultiSelector, isTotal) => {
    getCRRInfo($('#CSD_ID_SEARCH').selectpicker('val'), isTotal).then((result)=>{
        console.log(result);
        if(result.status == OK){
            const dataArray = result.data;
            if(dataArray.length > 0){
                let optionText = '';
        
                dataArray.map((item, index)=>{
                    let ITEM_ID = item.CRR_1_ID+","+item.CRR_2_ID+","+item.CRR_3_ID;
                    optionText += '<option value="'+ITEM_ID+'">'+item.CRR_NM+'</option>';
                });
    
                $('#'+targetID).html(optionText);
                
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

