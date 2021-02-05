const ajaxUrl = 'act/gwBillNum_act.php';
let CURR_PAGE = 1;
let IS_SORT = undefined;        // 오름차순.내림차순 정렬여부
let SELECT_MONTH = undefined;   // 월별청구건수정렬시 몇월달 인지

$(document).ready(function(){
    $('#indicator').hide();

    const tdLength = $('table th').length;
    setInitTableData(tdLength);

    getClaimYearOption();
    getGarageInfo('MGG_ID_SEARCH');                                          
    getRefOptions(GET_REF_CONTRACT_TYPE, 'RCT_ID_SEARCH', false, false);                        //거래여부
    getRefOptions(GET_REF_EXP_TYPE, 'RET_ID_SEARCH', false, false);                             //청구식

    setCRRSelectOption('CRR_ID_SEARCH', true, undefined, "=지역=");
    getBillDivisionOption('CSD_ID_SEARCH', true);                                              //구분
     /** 구분 셀렉트 체인지 */
     $('#CSD_ID_SEARCH').change(()=>{
        if($('#CSD_ID_SEARCH').selectpicker('val') != null){
            setCRRSelectOption('CRR_ID_SEARCH', true, undefined, "=지역=");
        }else{
            $('#CSD_ID').selectpicker('val' , '');
            $('#CSD_ID').selectpicker('refresh');
            setCRRSelectOption('CRR_ID_SEARCH', true, undefined, "=지역=");

        }
    });

     /** 검색 */
     $("#searchForm").submit(function(e){
        e.preventDefault();
        getList(1,'');
    });

});

excelDownload = () => {
    location.href="gwBillNumberExcel.html?"+$("#searchForm").serialize()+"&REQ_MODE="+CASE_TOTAL_LIST+
                    "&IS_SORT="+IS_SORT+"&MONTH="+(SELECT_MONTH == undefined ? '' : SELECT_MONTH);
}

getList = (currentPage, IS_ASC, MONTH) => {
    CURR_PAGE = (currentPage == undefined) ? CURR_PAGE : currentPage;
    IS_SORT = (IS_ASC == undefined) ? IS_SORT : IS_ASC; 
    SELECT_MONTH = MONTH;

    let form = $("#searchForm")[0];
    let formData = new FormData(form);
    formData.append('REQ_MODE', CASE_LIST);
    formData.append('PER_PAGE', $("#perPage").val());
    formData.append('CURR_PAGE', CURR_PAGE);
    formData.append('IS_SORT', IS_SORT);
    formData.append('MONTH', SELECT_MONTH == undefined ? '' : SELECT_MONTH);

    callFormAjax(ajaxUrl, 'POST', formData).then((result)=>{
        if(isJson(result)){
            const data = JSON.parse(result);
            if(data.status == OK){
                const item = data.data;
                setTable(item[1], item[0], CURR_PAGE);
            }else{
                setHaveNoDataTable();
            }
        }else{
            alert(ERROR_MSG_1200);
        }
       
    });  
    
}

