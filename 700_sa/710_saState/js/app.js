const COMMON_URI = '/common/searchOptions.php';
const ajaxUrl = 'act/saState_act.php';
let CURR_PAGE = 1;
let IS_CLICK_SEARCH_BTN = false;

$(document).ready(function(){
    $(".hidetd").hide();
    $(".changecol").attr("colspan", 2);

    setHeaderTable();

    const tdLength = $('table th').length;
    setInitTableData(tdLength);

    

    getDivisionOption('CSD_ID_SEARCH', true,);  //구분  
    setCRRSelectOption('CRR_ID_SEARCH', true, undefined);
    getRefOptions(GET_REF_CONTRACT_TYPE, 'RCT_ID_SEARCH', true, false);  //거래여부
    getRefOptions(GET_REF_STOP_REASON_TYPE, 'RSR_ID_SEARCH', true, false);                    // 거래중단사유  
    getRefOptions(GET_REF_EXP_TYPE, 'RET_ID_SEARCH', true, false);                             //청구식
    getRefOptions(GET_REF_SCALE_TYPE, 'RST_ID_SEARCH', false, false);      // 규모
    getRefOptions(GET_REF_REACTION_TYPE, 'RRT_ID_SEARCH', false, false);  //영업반응
    getRefOptions(GET_REF_AOS_TYPE, 'RAT_ID_SEARCH', false, false); 
    setEvent();   //setEvent.js

});

function excelDownload(){
    location.href="saStateExcel.html?"+$("#searchForm").serialize()+"&REQ_MODE="+CASE_TOTAL_LIST;
}

function getList(currentPage){
    if($('#SELECT_DT').val() == 'DELET_DAY'){
        alert('현판일기준 데이터가 존재하지 않아 조회가 불가합니다.');
    }else {
        CURR_PAGE = (currentPage == undefined) ? CURR_PAGE : currentPage;
        let form = $("#searchForm")[0];
        let formData = new FormData(form);
        formData.append('REQ_MODE', CASE_LIST);
        formData.append('PER_PAGE', $("#perPage").val());
        formData.append('CURR_PAGE', CURR_PAGE);
        
        callFormAjax(ajaxUrl, 'POST', formData).then((result)=>{
            if(isJson(result)){
                const data = JSON.parse(result);
                if(data.status == OK){
                    const item = data.data;
                    if($('#salestate-subtable').is(':visible')){
                        getVisitCycleData();
                    }
                    setTable(item[1], item[0], CURR_PAGE);
                }else{
                    setHaveNoDataTable();
                }
            }else{
                alert(ERROR_MSG_1200);
            }
        }); 
    }
}