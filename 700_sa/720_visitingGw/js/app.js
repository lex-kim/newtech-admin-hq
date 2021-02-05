const COMMON_URI = '/common/searchOptions.php';
const ajaxUrl = 'act/visitingGw_act.php';
let CURR_PAGE = 1;
let IS_CLICK_SORT_BTN = false;    // sort버튼을 클릭했는지 아닌지..
let SORT_TYPE = undefined;        // 오름차순.내림차순 정렬여부
let clickDelBtnIdx = "";    // true이면 일괄삭제, false이면 그냥 삭제
let listArray = [];

$(document).ready(function() {
    $('#indicator').hide();
    $( "#START_DT" ).datepicker(
        getDatePickerSetting('END_DT', 'minDate')
    );
    $( "#END_DT" ).datepicker(
        getDatePickerSetting('START_DT', 'maxDate')
    );

    /** 기간키: 방문일, 기간: 오늘날짜 디폴트  */
    $('#SELECT_DT').val('NSU_VISIT_DT');
    const date = new Date();
    const TODAY = dateFormat(date);
    const START_MONTH_DATE = date.getFullYear()+"-"+fillZero((date.getMonth()+1)+"",2)+"-"+"01"
    $("#START_DT").val(START_MONTH_DATE);
    $("#END_DT").val(TODAY);
    
    getDivisionOption('CSD_ID_SEARCH', true,);  //구분
    getRefOptions(GET_REF_CONTRACT_TYPE, 'RCT_ID_SEARCH', false, false);           //거래여부 
    getRefOptions(GET_REF_SALES_FILE, 'RSF_ID_SELECT', false, false);          // 첨부파일     
    setCRRSelectOption('CRR_ID_SEARCH', true, undefined);

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

    const tdLength = $('table th').length;
    setInitTableData(tdLength);

    /** 검색 */
    $("#searchForm").submit(function(e){
            e.preventDefault();
            IS_CLICK_SORT_BTN = false;
            getList(1);
        
    });
});


function openGaragePopup(NSU_ID, MGG_ID){
    const formData = $('#gwForm');
    window.open('', 
                'newwindow', 
                'width=1500,height=1000');
    $('#MGG_VALUE').val(MGG_ID);
    $('#NSU_ID').val(NSU_ID);
    formData.submit();
}


function listData(currentPage, SORT_TYPE){
    CURR_PAGE = (currentPage == undefined) ? CURR_PAGE : currentPage;
    let form = $("#searchForm")[0];
    let formData = new FormData(form);
    formData.append('REQ_MODE', CASE_LIST);
    formData.append('PER_PAGE', $("#perPage").val());
    formData.append('CURR_PAGE', CURR_PAGE);
    if(SORT_TYPE != undefined){
        formData.append('SORT_TYPE', SORT_TYPE);
    }
    

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

function getSortList(sort){
    IS_CLICK_SORT_BTN = true;
    SORT_TYPE = (sort == undefined) ? SORT_TYPE : sort;
    getList(CURR_PAGE);
}

function getList(currentPage){
    CURR_PAGE = (currentPage == undefined) ? CURR_PAGE : currentPage;
   
    let form = $("#searchForm")[0];
    let formData = new FormData(form);
    formData.append('REQ_MODE', CASE_LIST);
    formData.append('PER_PAGE', $("#perPage").val());
    formData.append('CURR_PAGE', CURR_PAGE);

    if(IS_CLICK_SORT_BTN){
        formData.append('SORT_TYPE', SORT_TYPE);
    }
    
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


function excelDownload(){
    if(IS_CLICK_SORT_BTN){
        location.href="visitingGwExcel.html?"+$("#searchForm").serialize()+"&REQ_MODE="+CASE_TOTAL_LIST+"&SORT_TYPE="+SORT_TYPE;
    }else{
        location.href="visitingGwExcel.html?"+$("#searchForm").serialize()+"&REQ_MODE="+CASE_TOTAL_LIST;
    }
    
}


function openDeleteModal(idx) {
    let delIdxArray = [];
    if(idx == undefined){
        const msg = $('input[name=multi]:checked').length+"건을 일괄삭제 하시겠습니까?";
        confirmSwalTwoButton(msg, '삭제', '취소', true , ()=>{       
            $('input[name=multi]:checked').each(function(){
                if($(this).val() != ''){
                    delIdxArray.push($(this).val());
                }
            });                         
            delData(delIdxArray);
        });
    }else{
        confirmSwalTwoButton("삭제 하시겠습니까?",'확인', '취소', true , ()=>{       
            delIdxArray.push(idx);
            console.log('del idx>>>',idx);
            console.log('delIdxArray>>>',delIdxArray);
            delData(delIdxArray);
        });
    }
}

function delData(delDataIdxArray) {
    let form = $("#delForm")[0];
    let formData = new FormData(form);
    formData.append('REQ_MODE', CASE_DELETE);

    if(delDataIdxArray.length > 0){
        delDataIdxArray.map((idx)=>{
            const data = listArray[idx];
            console.log(data);
            formData.append('NSU_ID[]', data.NSU_ID);
        });  

        callFormAjax(ajaxUrl, 'POST', formData).then((result)=>{
            if(isJson(result)){
                const data = JSON.parse(result);
                if(data.status == OK){
                    confirmSwalOneButton("삭제되었습니다.", OK_MSG,()=>{
                        getList();
                  
                    });
                }else{
                    alert(ERROR_MSG_1100);
                }
            }else{
                alert(ERROR_MSG_1200);
            }
        });
    }
}

   