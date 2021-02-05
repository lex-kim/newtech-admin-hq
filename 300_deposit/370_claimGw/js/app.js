const CLAIM_URL = 'act/claimGw_act.php';
let CURR_PAGE = 1;
let tdLength = 0;

$(document).ready(function(){
    tdLength = $('table th').length;
    setInitTableData(tdLength);

    getYearOption(); 
    setGwOptions('MGG_ID_SEARCH',undefined,'=공업사=');                             
    getRefOptions(GET_REF_CONTRACT_TYPE, 'RCT_ID_SEARCH', false, false);                        //거래여부
    getRefOptions(GET_REF_EXP_TYPE, 'RET_ID_SEARCH', true, false);                             //청구식
    getRefOptions(GET_REF_AOS_TYPE, 'RAT_ID_SEARCH', false, false);                                    // 포토앱

    getDivisionOption('CSD_ID_SEARCH', true);                                              //구분
    setCRR_ID_Option('CSD_ID_SEARCH', 'CRR_ID_SEARCH', true);
    
    /* 구분에 따른 지역 변경 */
    $('#CSD_ID').change(()=>{
        if($('#CSD_ID').val() != ''){
            setCRR_ID('CSD_ID', 'CRR_ID', 'SELECT_CRR_ID', true);
        }else{
            $('#CRR_ID').val('');
            $('#SELECT_CRR_ID').val('');
            $('#CRR_ID').attr('disabled',true);
        }
    });

    $('#CSD_ID_SEARCH').change(()=>{
        if($('#CSD_ID_SEARCH').selectpicker('val') != null){
            setCRR_ID_Option('CSD_ID_SEARCH', 'CRR_ID_SEARCH', true, true);
        }else{
            $('#CSD_ID').selectpicker('val' , '');
            $('#CSD_ID').selectpicker('refresh');
            setCRR_ID_Option('CSD_ID_SEARCH', 'CRR_ID_SEARCH', true);
        }
    });

     /** 검색 */
     $("#searchForm").submit(function(e){
        e.preventDefault();
        getList(1);
    });

});

/* 해당년 조회 */
getYearOption = () => {
    const data = {
        REQ_MODE: OPTIONS_YEAR
    };
    callAjax(CLAIM_URL, 'POST', data).then((result)=>{
        if(isJson(result)){
            const res = JSON.parse(result);
            if(res.status == OK){
                let optionText = '<option value="">=해당년=</option>';
                let value = res.data.CURR_YEAR;

                while(value>= res.data.MIN_YEAR) {
                    if(value == res.data.CURR_YEAR){
                        optionText += "<option value="+value+" selected>"+value+"년</option>";
                    }else{
                        optionText += "<option value="+value+">"+value+"년</option>";
                    }
                    
                    value--;
                }

                $('#YEAR_SEARCH').html(optionText);
            }else{
                alert(ERROR_MSG_1100);
            }
        }else{
            alert("해당년을 조회할 수 없습니다.");
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

excelDownload = () => {
    location.href="claimGwExcel.html?"+$("#searchForm").serialize();
}

getList = (currentPage) => {
    CURR_PAGE = (currentPage == undefined) ? CURR_PAGE : currentPage;

    let form = $("#searchForm")[0];
    let formData = new FormData(form);
    formData.append('REQ_MODE', CASE_LIST);
    formData.append('PER_PAGE', $("#perPage").val());
    formData.append('CURR_PAGE', CURR_PAGE);


    callFormAjax(CLAIM_URL, 'POST', formData).then((result)=>{
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

/** 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
    const tableText = "<tr><td class='text-center' colspan='"+tdLength+"'>조회된 데이터가 존재하지 않습니다.</tr></tr>";
    $('.pagination').html('');
    $('#dataTable').html(tableText);
}

setTable = (totalListCount, dataArray, currentPage) => {
    let tableText = '';
    listArray = dataArray;

    dataArray.map((data, index)=>{
        tableText += "<tr>";
        tableText += "<td class='text-center'>"+data.IDX+"</td>";
        tableText += "<td class='text-center'>"+data.CSD_NM+"</td>";
        tableText += "<td class='text-left'>"+data.CRR_NM+"</td>";
        tableText += "<td class='text-left'>"+data.MGG_NM+"</td>";
        tableText += "<td class='text-center'>"+data.RET_NM+"</td>";
        
        tableText += "<td class='text-center'>"+nullChk(data.MGG_FRONTIER_NM)+"</td>";
        tableText += "<td class='text-center'>"+nullChk(data.MGG_COLLABO_NM)+"</td>";
        tableText += "<td class='text-center'>"+nullChk(data.MGG_BAILEE_NM)+"</td>";
        tableText += "<td class='text-center'>"+data.RCT_NM+"</td>";
        tableText += "<td class='text-center'>"+data.MGG_BIZ_START_DT+"</td>";
        tableText += "<td class='text-center'>"+nullChk(data.MGG_BIZ_END_DT)+"</td>";
        tableText += "<td class='text-center'>"+nullChk(data.RAT_NM)+"</td>";
        tableText += "<td class='text-center'>"+nullChk(data.MGG_BIZ_BEFORE_NM)+"</td>";
        tableText += "<td class='text-center'>"+nullChk(data.RSR_NM)+"</td>";
        tableText += "<td class='text-left'>"+nullChk(data.MGG_OTHER_BIZ_NM)+"</td>";
        
        tableText += "  <td class='text-center'>"+(data.MGG_CONTRACT_YN == YN_Y ? '계약' :'미계약')+"</td>"; //계약여부
        tableText += "  <td class='text-center'>"+(data.MGG_CONTRACT_START_DT != null ? data.MGG_CONTRACT_END_DT == null? data.MGG_CONTRACT_START_DT+"~" : data.MGG_CONTRACT_START_DT+"~"+data.MGG_CONTRACT_END_DT: '-' )+"</td>";
        tableText += "  <td class='text-right'>"+nullChk(data.MGG_RATIO, 0)+"%</td>";
        tableText += "  <td class='text-center'>"+nullChk(data.MGG_BANK_NM_AES)+"</td>";
        tableText += "  <td class='text-center'>"+nullChk(data.MGG_BANK_NUM_AES)+"</td>";
        tableText += "  <td class='text-center'>"+nullChk(data.MGG_BANK_OWN_NM_AES)+"</td>";
        
        tableText += "<td class='text-right'>"+numberWithCommas(cutThousand(data.STOCK_PRICE))+"</td>";
        tableText += "<td class='text-right'>"+numberWithCommas(data.STOCK)+"%</td>";
        tableText += "<td class='text-right'>"+numberWithCommas(data.AVG_CNT)+"</td>";
        tableText += "<td class='text-right'>"+numberWithCommas(cutThousand(data.AVG_PRICE))+"</td>";
        tableText += "<td class='text-right'>"+numberWithCommas(data.SUM_CNT)+"</td>";
        tableText += "<td class='text-right'>"+numberWithCommas(cutThousand(data.SUM_PRICE))+"</td>";

        tableText += "<td class='text-right'>"+numberWithCommas(data.CLAIM_CNT_12)+"</td>";
        tableText += "<td class='text-right'>"+numberWithCommas(cutThousand(data.CLAIM_PRICE_12))+"</td>";
        tableText += "<td class='text-right'>"+numberWithCommas(data.CLAIM_CNT_11)+"</td>";
        tableText += "<td class='text-right'>"+numberWithCommas(cutThousand(data.CLAIM_PRICE_11))+"</td>";
        tableText += "<td class='text-right'>"+numberWithCommas(data.CLAIM_CNT_10)+"</td>";
        tableText += "<td class='text-right'>"+numberWithCommas(cutThousand(data.CLAIM_PRICE_10))+"</td>";
        tableText += "<td class='text-right'>"+numberWithCommas(data.CLAIM_CNT_9)+"</td>";
        tableText += "<td class='text-right'>"+numberWithCommas(cutThousand(data.CLAIM_PRICE_9))+"</td>";
        tableText += "<td class='text-right'>"+numberWithCommas(data.CLAIM_CNT_8)+"</td>";
        tableText += "<td class='text-right'>"+numberWithCommas(cutThousand(data.CLAIM_PRICE_8))+"</td>";
        tableText += "<td class='text-right'>"+numberWithCommas(data.CLAIM_CNT_7)+"</td>";
        tableText += "<td class='text-right'>"+numberWithCommas(cutThousand(data.CLAIM_PRICE_7))+"</td>";
        tableText += "<td class='text-right'>"+numberWithCommas(data.CLAIM_CNT_6)+"</td>";
        tableText += "<td class='text-right'>"+numberWithCommas(cutThousand(data.CLAIM_PRICE_6))+"</td>";
        tableText += "<td class='text-right'>"+numberWithCommas(data.CLAIM_CNT_5)+"</td>";
        tableText += "<td class='text-right'>"+numberWithCommas(cutThousand(data.CLAIM_PRICE_5))+"</td>";
        tableText += "<td class='text-right'>"+numberWithCommas(data.CLAIM_CNT_4)+"</td>";
        tableText += "<td class='text-right'>"+numberWithCommas(cutThousand(data.CLAIM_PRICE_4))+"</td>";
        tableText += "<td class='text-right'>"+numberWithCommas(data.CLAIM_CNT_3)+"</td>";
        tableText += "<td class='text-right'>"+numberWithCommas(cutThousand(data.CLAIM_PRICE_3))+"</td>";
        tableText += "<td class='text-right'>"+numberWithCommas(data.CLAIM_CNT_2)+"</td>";
        tableText += "<td class='text-right'>"+numberWithCommas(cutThousand(data.CLAIM_PRICE_2))+"</td>";
        tableText += "<td class='text-right'>"+numberWithCommas(data.CLAIM_CNT_1)+"</td>";
        tableText += "<td class='text-right'>"+numberWithCommas(cutThousand(data.CLAIM_PRICE_1))+"</td>";

        tableText += "<td class='text-center'>"+data.MGG_INSU_MEMO+"</td>";
        tableText += "<td class='text-center'>"+data.MGG_MANUFACTURE_MEMO+"</td>";
        tableText += "</tr>"
        
    });
    paging(totalListCount, currentPage, 'getList'); 
    $('#dataTable').html(tableText);
}
