let CAROWN_URL = 'act/depositCar_act.php';
let CURR_PAGE = 1;

$(document).ready(function() {
    /** 집계보기 버튼 동적으로 세팅(위치)  */
    const tableTopEtcPosition = $('#perPageLabel').width() + $('#perPage').width() + 55;
    $('.table-top-etc').css('left',tableTopEtcPosition);
    $(".totalbtn").click(function(){
        if($("#totalcheckbtn").is(":checked")){
            getSummary();
            $(".nwt-total").addClass("summary-open");
        }else{
            $(".nwt-total").removeClass("summary-open");
        }
    })

    tdLength = $('table tr th').length;
    setInitTableData(tdLength);

    $( "#DT_KEY_SEARCH" ).datepicker(
        getDatePickerSetting()
    );
    $( "#START_DT_SEARCH").datepicker(
        getDatePickerSetting('END_DT_SEARCH', 'minDate')
    );
    $( "#END_DT_SEARCH").datepicker(
        getDatePickerSetting('START_DT_SEARCH', 'maxDate')
    );

    getYearOption();
    getDivisionOption('CSD_ID_SEARCH', true);                                      //지역구분
    setCRR_ID_Option('CSD_ID_SEARCH', 'CRR_ID_SEARCH', true);
    getRefOptions(GET_REF_EXP_TYPE, 'RET_ID_SEARCH', true, false);                 //청구식
    setGwOptions('MGG_NM_SEARCH',undefined,'=공업사=');
    getRefOptions(GET_REF_CONTRACT_TYPE, 'RCT_ID_SEARCH', false, false);                        //거래여부


    $('#YEAR_SEARCH').change(function() {
        $('#START_DT_SEARCH').val($('#YEAR_SEARCH').val()+'-01-01');
        $('#END_DT_SEARCH').val($('#YEAR_SEARCH').val()+'-12-31');
    });

    $('#MONTH_SEARCH').change(function() {
        if($('#YEAR_SEARCH').val() == '' ){
            alert('해당년을 선택 후 설정하세요.');
        $('#MONTH_SEARCH').val('');
            return false;
        }else {
            $('#START_DT_SEARCH').val($('#YEAR_SEARCH').val()+'-'+$('#MONTH_SEARCH').val()+'-01');
            $('#END_DT_SEARCH').val($('#YEAR_SEARCH').val()+'-'+$('#MONTH_SEARCH').val()+'-'+new Date( $('#YEAR_SEARCH').val(), $('#MONTH_SEARCH').val(), 0).getDate());  
        }
    });

    /** 검색에서 기간을 눌렀을 때 날짜 초기화 */
    $('#DT_KEY_SEARCH').change(()=>{
        if($('#DT_KEY_SEARCH').val() == ''){
            $('#START_DT_SEARCH').val('');
            $('#END_DT_SEARCH').val('');
        }
    });

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

})

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

/* 집계보기 */
getSummary = () => {
    
    let form = $("#searchForm")[0];
    let formData = new FormData(form);
    formData.append('REQ_MODE', CASE_LIST_SUB);
    callFormAjax(CAROWN_URL, 'POST', formData).then((result)=>{
        console.log('getSummary SQL>>>>',result);
        const res = JSON.parse(result);
        console.log('getSummary',res);
        if(res.status == OK){
            const item = res.data;
            let summaryTxt ='';
            summaryTxt += '<tr>';
            summaryTxt += '<td class="text-center">'+numberWithCommas(Number(item.CNT))+'</td>';
            summaryTxt += '<td class="text-center">'+numberWithCommas(Number(item.BILL_PRICE))+'</td>';
            summaryTxt += '<td class="text-center">'+numberWithCommas(Number(item.PAID_CNT))+'</td>';
            summaryTxt += '<td class="text-center">'+numberWithCommas(Number(item.PAID_PRICE))+'</td>';
            summaryTxt += '<td class="text-center">'+numberWithCommas(Number(item.PAID_RATIO))+'%</td>';
            summaryTxt += '<td class="text-center">'+numberWithCommas(Number(item.DECREMENT_PRICE))+'</td>';
            summaryTxt += '<td class="text-center">'+numberWithCommas(Number(item.DECREMENT_RATIO))+'%</td>';
            summaryTxt += '<td class="text-center">'+numberWithCommas(Number(item.UNPAID_CNT))+'</td>';
            summaryTxt += '<td class="text-center">'+numberWithCommas(Number(item.UNPAID_PRICE))+'</td>';
            summaryTxt += '</tr>';
            $('#summaryTable').html(summaryTxt);
            
        }else{
            alert(ERROR_MSG_1200);
        } 
    });
  }


/* 해당년 조회 */
getYearOption = () => {
    const data = {
        REQ_MODE: OPTIONS_YEAR
    };
  
    callAjax(CAROWN_URL, 'POST', data).then((result)=>{
        if(isJson(result)){
            const res = JSON.parse(result);
            if(res.status == OK){
                // setClaimYearOption(res.data);
                let optionText = '<option value="">=해당년=</option>';
                let value = res.data.CURR_YEAR;

                while(value>= res.data.MIN_YEAR) {
                    optionText += "<option value="+value+">"+value+"</option>";
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
 * MAIN LIST 
 * @param {int} currentPage  조회하는 페이지
 */
getList = (currentPage) =>{
    if($('#START_DT_SEARCH').val() !='' || $('#END_DT_SEARCH').val() != '' ){
      if($('#DT_KEY_SEARCH').val() == ''){
        alert('기간 키필드를 선택해주세요.');
        return false;
      }
    }
    
    CURR_PAGE = (currentPage == undefined) ? CURR_PAGE : currentPage;
    let form = $("#searchForm")[0];
    let formData = new FormData(form);
    formData.append('REQ_MODE', CASE_LIST);
    formData.append('PER_PAGE', $("#perPage").val());
    formData.append('CURR_PAGE', CURR_PAGE);

    callFormAjax(CAROWN_URL, 'POST', formData).then((result)=>{
        console.log('getList SQL >>',result);
        const res = JSON.parse(result);
        console.log( 'getList >> ',res);
        if(res.status == OK){
            const item = res.data;
            setTable(item[1], item[0], CURR_PAGE);  
        }else{
            setHaveNoDataTable();
        }
    });  
    if($("#totalcheckbtn").is(":checked")){
        getSummary();
    }
  }

/** 메인 리스트 테이블 동적으로 생성 
 * @param {totalListCount: 총 리스트데이터 수, dataArray : 리스트 데이터, currentPage : 현재페이지(페이징을 위해)} 
*/
setTable = (totalListCount, dataArray, currentPage) => {
    let tableText = '';

    dataArray.map((data, index)=>{
        tableText += "<tr>";
        tableText += "  <td class='text-center'>"+data.IDX+"</td>";
        tableText += "  <td class='text-center'>"+data.CSD_NM+"</td>";
        tableText += "  <td class='text-center'>"+data.CRR_NM+"</td>";
        tableText += "  <td class='text-left'>"+data.MGG_NM+"</td>";
        tableText += "  <td class='text-center'>"+bizNoFormat(data.MGG_BIZ_ID)+"</td>";
        tableText += "  <td class='text-center'>"+data.RET_NM+"</td>"; //청구식

        //집계
        tableText += "  <td class='text-right'>"+numberWithCommas(Number(data.TOTAL_CNT))+"</td>";
        tableText += "  <td class='text-right'>"+numberWithCommas(Number(data.TOTAL_BILL_PRICE))+"</td>";
        tableText += "  <td class='text-right'>"+numberWithCommas(Number(data.DEPOSIT_CNT))+"</td>";
        tableText += "  <td class='text-right'>"+numberWithCommas(Number(data.DEPOSIT_BILL_PRICE))+"</td>";
        tableText += "  <td class='text-right'>"+numberWithCommas(Number(data.DEPOSIT_PRICE))+"</td>";
        tableText += "  <td class='text-right'>"+numberWithCommas(Number(data.DECREMENT_PRICE))+"</td>";
        tableText += "  <td class='text-right'>"+numberWithCommas(Number(data.DECREMENT_RATIO))+"</td>";
        tableText += "  <td class='text-right'>"+numberWithCommas(Number(data.UNPAID_CNT))+"</td>";
        tableText += "  <td class='text-right'>"+numberWithCommas(Number(data.UNPAID_PRICE))+"</td>";

        tableText += "  <td class='text-right'>"+numberWithCommas(Number(data.PAID_CNT_RATIO))+"%</td>";
        tableText += "  <td class='text-right'>"+numberWithCommas(Number(data.PAID_PRICE_RATIO))+"%</td>";
        tableText += "</tr> ";
    });
    paging(totalListCount, currentPage, 'getList'); 
    
    $('#dataTable').html(tableText);
}

/** 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
    const tableText = "<tr><td class='text-center' colspan='"+tdLength+"'>조회된 데이터가 존재하지 않습니다.</tr></tr>";
    $('.pagination').html('');
    $('#dataTable').html(tableText);
}



excelDownload = () =>{
    location.href="depositCarExcel.html?"+$("#searchForm").serialize();
}