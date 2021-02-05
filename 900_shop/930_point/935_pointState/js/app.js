const POINTSTATE_URL = 'act/pointState_act.php';
let CURR_PAGE = 1;
let tdLength = 0;

$(document).ready(function(){
    tdLength = $('table th').length;
    setInitTableData(tdLength);
    getGrade();

    $( "#DT_KEY_SEARCH" ).datepicker(
        getDatePickerSetting()
    );
    $( "#START_DT_SEARCH").datepicker(
        getDatePickerSetting('END_DT_SEARCH', 'minDate')
    );
    $( "#END_DT_SEARCH").datepicker(
        getDatePickerSetting('START_DT_SEARCH', 'maxDate')
    );

    setCRR_ID_Option('CSD_ID_SEARCH', 'CRR_ID_SEARCH', true);
    getDivisionOption('CSD_ID_SEARCH', true); 
    setCRR_ID_Option('CSD_ID_SEARCH', 'CRR_ID_SEARCH', true);
    getRefOptions(GET_REF_EXP_TYPE, 'RET_ID_SEARCH', true, false);                               //청구식
    setGwOptions('MGG_NM_SEARCH',undefined,'=공업사=');
    getRefOptions(GET_REF_CONTRACT_TYPE, 'RCT_ID_SEARCH', false, false);                        //거래여부
    getRefOptions(GET_REF_AOS_TYPE, 'RAT_ID_SEARCH', false, false);                                    // AOS 권한

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

getList = (currentPage) => {

    CURR_PAGE = (currentPage == undefined) ? CURR_PAGE : currentPage;
    let form = $("#searchForm")[0];
    let formData = new FormData(form);
    formData.append('REQ_MODE', CASE_LIST);
    formData.append('PER_PAGE', $("#perPage").val());
    formData.append('CURR_PAGE', CURR_PAGE);

    callFormAjax(POINTSTATE_URL, 'POST', formData).then((result)=>{
        const res = JSON.parse(result);
        // console.log('getList >>>',res);
        if(res.status == OK){
            const item = res.data;
            setTable(item[1], item[0], CURR_PAGE);
        }else{
            setHaveNoDataTable();
        }
    });  


    // CURR_PAGE = (currentPage == undefined) ? CURR_PAGE : currentPage;
    // const data = {
    //     REQ_MODE: CASE_LIST,
    //     PER_PAGE:  $("#perPage").val(),
    //     CURR_PAGE:  CURR_PAGE
    // };

    // callAjax(POINTSTATE_URL, 'GET', data).then((result)=>{       
    //     const res = JSON.parse(result);
    //     console.log('getList >>>',res);
    //     if(res.status == OK){
    //         const item = res.data;
    //         setTable(item[1], item[0], CURR_PAGE);
    //     }else{
    //         setHaveNoDataTable();
    //     }
    // });
 
}

/** 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
    const tdLength = $('table th').length;
    const tableText = "<tr><td class='text-center' colspan='"+tdLength+"'>조회된 데이터가 존재하지 않습니다.</tr></tr>";
    $('.pagination').html('');
    $('#dataTable').html(tableText);
}

setTable = (totalListCount, dataArray, currentPage) => {
    let tableText = '';

    dataArray.map((data)=>{
        tableText += "<tr>";
        tableText += "<td class='text-center'>"+data.IDX+"</td>"; 
        tableText += "<td class='text-center'>"+data.CSD_NM+"</td>"; 
        tableText += "<td class='text-left'>"+data.CRR_NM+"</td>"; 
        tableText += "<td class='text-center'>"+bizNoFormat(data.MGG_BIZ_ID)+"</td>"; 
        tableText += "<td class='text-left'>"+data.MGG_NM+"</td>"; 
        tableText += "<td class='text-center'>"+nullChk(data.RET_NM)+"</td>"; 
        tableText += "<td class='text-center'>"+nullChk(data.RCT_NM)+"</td>"; 
        tableText += "<td class='text-center'>"+data.MGG_BIZ_START_DT+"</td>"; 
        tableText += "<td class='text-center'>"+nullChk(data.MGG_BIZ_END_DT)+"</td>"; 
        tableText += "<td class='text-center'>"+nullChk(data.SLL_NM)+"</td>"; 
        tableText += "<td class='text-center'>"+nullChk(data.RAT_NM)+"</td>"; 
        tableText += "<td class='text-left'>"+data.MGG_BAILEE_NM+"</td>"; 
        tableText += "<td class='text-left'>"+data.MGG_CEO_NM_AES+"</td>"; 
        tableText += "<td class='text-center'>"+phoneFormat(data.MGG_CEO_TEL_AES)+"</td>"; 
        tableText += "<td class='text-left'>"+nullChk(data.MGGS_ADDR)+"</td>"; 
        tableText += "<td class='text-center'>"+phoneFormat(data.MGG_OFFICE_TEL)+"</td>"; 
        tableText += "<td class='text-center'>"+phoneFormat(data.MGG_OFFICE_FAX)+"</td>"; 
        tableText += "<td class='text-right'>"+data.STOCK_PRICE+"</td>"; 
        tableText += "<td class='text-right'>"+data.POINT_60110+"</td>"; 
        tableText += "<td class='text-right'>"+data.POINT_60110_CANCEL+"</td>"; 
        tableText += "<td class='text-right'>"+data.POINT_60119+"</td>"; 
        tableText += "<td class='text-right'>"+data.POINT_60119_CANCEL+"</td>"; 
        tableText += "<td class='text-right'>"+data.POINT_60120+"</td>"; 
        tableText += "<td class='text-right'>"+data.POINT_60120_CANCEL+"</td>"; 
        tableText += "<td class='text-right'>"+data.POINT_60129+"</td>"; 
        tableText += "<td class='text-right'>"+data.POINT_60129_CANCEL+"</td>"; 
        tableText += "<td class='text-right'>"+data.POINT_60190+"</td>"; 
        tableText += "<td class='text-right'>"+data.POINT_60190_CANCEL+"</td>"; 
        tableText += "<td class='text-right'>"+data.POINT_60199+"</td>"; 
        tableText += "<td class='text-right'>"+data.POINT_60199_CANCEL+"</td>"; 
        tableText += "<td class='text-right'>"+data.NPB_POINT+"</td>"
        tableText += "</tr>"
    });
    paging(totalListCount, currentPage, 'getList'); 
    $('#dataTable').html(tableText);
}


excelDownload = () => {
    location.href="pointStateExcel.html";
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

/** 등급데이터 받아오기 */
getGrade = () =>{
    const data = {
        REQ_MODE: GET_REF_DATA,
    };
    callAjax(POINTSTATE_URL, 'GET', data).then((result)=>{
        const res = JSON.parse(result);
        
        let objOptionList = [];
        $("#SLL_ID_SEARCH" + " option").remove();  // SELECT 개체 초기화
        objOptionList.push($("<option>", { text: "=등급명=" }).attr("value","")); //최초조건 추가
        res.map((item, i)=>{
            objOptionList.push($("<option>", { text: item.SLL_NM }).attr("value",item.SLL_ID));
        })
        $("#SLL_ID_SEARCH").append(objOptionList);  // <SELECT> 객체에 반영

    
    });
}