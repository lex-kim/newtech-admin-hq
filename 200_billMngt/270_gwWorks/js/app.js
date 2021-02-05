const ajaxUrl = 'act/getWorks_act.php';
let CURR_PAGE = 1;

$(document).ready(function(){
    $('#indicator').hide();

    getClaimYearOption();
    getRefOptions(GET_REF_CONTRACT_TYPE, 'RCT_ID_SEARCH', false, false);           //거래여부 
    getRefOptions(GET_REF_EXP_TYPE, 'RET_ID_SEARCH', true, false);                             //청구식
    getWorksList();
    setCRRSelectOption('CRR_ID_SEARCH', true, undefined);
    getBillDivisionOption('CSD_ID_SEARCH', true);                                              //구분
    getGarageInfo('MGG_ID_SEARCH');  

     /** 구분 셀렉트 체인지 */
     $('#CSD_ID_SEARCH').change(()=>{
        if($('#CSD_ID_SEARCH').selectpicker('val') != null){
            setCRRSelectOption('CRR_ID_SEARCH', true, undefined);
        }else{
            $('#CSD_ID').selectpicker('val' , '');
            $('#CSD_ID').selectpicker('refresh');
            setCRRSelectOption('CRR_ID_SEARCH', true, undefined);

        }
    });
    
    $( "#START_DT" ).datepicker(
        getDatePickerSetting()
    );
    $( "#END_DT" ).datepicker(
        getDatePickerSetting()
    );

    const date = new Date();
    $( "#START_DT").datepicker('setDate', date.getFullYear()+'-01-01'); 
    $( "#END_DT").datepicker('setDate', 'today'); 
    

     /** 검색 */
     $("#searchForm").submit(function(e){
        e.preventDefault();

        getList(1);
       
    });

    
});

getList = (currentPage) => {
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
                setTable(item[1], item[0], CURR_PAGE);
            }else{
                setHaveNoDataTable();
            }
        }else{
            alert(ERROR_MSG_1200);
        }
       
    });     
}

excelDownload = () => {
    location.href="gwWorksExcel.html?"+$("#searchForm").serialize()+"&REQ_MODE="+CASE_TOTAL_LIST;
}

onchangeYEAR = () => {
    const selectYEAR = $('#CLAIM_YEAR').val();
    const selectMONTH = $('#CLAIM_MONTH').val();
    if(selectYEAR != '' && selectMONTH == ''){
        $( "#START_DT").datepicker('setDate', selectYEAR+'-01-01'); 
        $( "#END_DT").datepicker('setDate', selectYEAR+'-12-31'); 
    }else if(selectYEAR != '' && selectMONTH != ''){
        const lastDay = (new Date(selectYEAR, selectMONTH, 0) ).getDate();
        $( "#START_DT").datepicker('setDate', selectYEAR+'-'+selectMONTH+'-01'); 
        $( "#END_DT").datepicker('setDate', selectYEAR+'-'+selectMONTH+'-'+lastDay); 
    }else{
        const date = new Date();
        $( "#START_DT").datepicker('setDate', date.getFullYear()+'-01-01'); 
        $( "#END_DT").datepicker('setDate', 'today'); 
    }
    
}

onchangeMONTH = () => {
    const selectYEAR = $('#CLAIM_YEAR').val();
    const selectMONTH = $('#CLAIM_MONTH').val();
    if(selectYEAR != '' && selectMONTH != ''){
        const lastDay = (new Date(selectYEAR, selectMONTH, 0) ).getDate();
        $( "#START_DT").datepicker('setDate', selectYEAR+'-'+selectMONTH+'-01'); 
        $( "#END_DT").datepicker('setDate', selectYEAR+'-'+selectMONTH+'-'+lastDay); 
    }
}