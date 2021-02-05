const DEPOSIT_URL = 'act/depositGw_act.php';
let CURR_PAGE = 1;
let tdLength = 0;

$(document).ready(function(){
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
});


/* 해당년 조회 */
getYearOption = () => {
    const data = {
        REQ_MODE: OPTIONS_YEAR
    };
  
    callAjax(DEPOSIT_URL, 'POST', data).then((result)=>{
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

    callFormAjax(DEPOSIT_URL, 'POST', formData).then((result)=>{
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
        tableText += "  <td class='text-center'>"+nullChk(data.MGG_BAILEE_NM)+"</td>";
        tableText += "  <td class='text-center'>"+data.RCT_NM+"</td>"; //거래여부
        tableText += "  <td class='text-center'>"+data.MGG_BIZ_START_DT+"</td>";
        tableText += "  <td class='text-center'>"+nullChk(data.MGG_BIZ_END_DT)+"</td>";
        tableText += "  <td class='text-center'>"+(data.MGG_CONTRACT_YN == YN_Y ? '계약' :'미계약')+"</td>"; //계약여부
        tableText += "  <td class='text-center'>"+(data.MGG_CONTRACT_START_DT != null ? data.MGG_CONTRACT_END_DT == null? data.MGG_CONTRACT_START_DT+"~" : data.MGG_CONTRACT_START_DT+"~"+data.MGG_CONTRACT_END_DT: '-' )+"</td>";
        tableText += "  <td class='text-right'>"+nullChk(data.MGG_RATIO, 0)+"%</td>";
        tableText += "  <td class='text-center'>"+nullChk(data.MGG_BANK_NM_AES)+"</td>";
        tableText += "  <td class='text-center'>"+nullChk(data.MGG_BANK_NUM_AES)+"</td>";
        tableText += "  <td class='text-center'>"+nullChk(data.MGG_BANK_OWN_NM_AES)+"</td>";

        //집계
        tableText += "  <td class='text-right'>"+numberWithCommas(data.NB_TOTAL_CNT)+"</td>";                  //총청구건수
        tableText += "  <td class='text-right'>"+numberWithCommas(data.NB_TOTAL_PRICE)+"</td>";                  //총청구금액
        tableText += "  <td class='text-right'>"+numberWithCommas(data.NBI_TOTAL_DEPOSIT_CNT)+"</td>";         //입금건수
        tableText += "  <td class='text-right'>"+numberWithCommas(data.NBI_TOTAL_DEPOSIT_BILL_PRICE)+"</td>";  //입금청구액
        tableText += "  <td class='text-right'>"+numberWithCommas(data.NBI_TOTAL_DEPOSIT_PRICE)+"</td>";       //입금금액
        tableText += "  <td class='text-right'>"+numberWithCommas(data.DECREMENT_PRICE)+"</td>";               //감액액
        tableText += "  <td class='text-right'>"+numberWithCommas(data.UNPAID_DONE_CNT)+"</td>";               //미입금종결건수
        tableText += "  <td class='text-right'>"+numberWithCommas(data.UNPAID_DONE_BILL_PRICE)+"</td>";        //미입금종결 청구금액
        tableText += "  <td class='text-right'>"+numberWithCommas(data.UNPAID_CNT)+"</td>";                    //미입금건수
        tableText += "  <td class='text-right'>"+numberWithCommas(data.UNPAID_PRICE)+"</td>";                  //미입금액

        tableText += "  <td class='text-right'>"+numberWithCommas(data.DEPOSIT_CNT_RATIO)+"%</td>";             //입금률 건수
        tableText += "  <td class='text-right'>"+numberWithCommas(data.DEPOSIT_PRICE_RATIO)+"%</td>";           //입금률 금액

        tableText += "  <td class='text-right'>"+numberWithCommas(data.DECREMENT_CNT_RATIO)+"%</td>";           //감액률 건수
        tableText += "  <td class='text-right'>"+numberWithCommas(data.DECREMENT_PRICE_RATIO)+"%</td>";         //감액률 금액

        tableText += "  <td class='text-right'>"+numberWithCommas(data.UNPAID_DONE_CNT_RATIO)+"%</td>";         //미입금 종결률 건수
        tableText += "  <td class='text-right'>"+numberWithCommas(data.UNPAID_DONE_PRICE_RATIO)+"%</td>";       //미입금 종결률 금액

        tableText += "  <td class='text-right'>"+numberWithCommas(data.UNPAID_CNT_RATIO)+"%</td>";              //미입금률 건수
        tableText += "  <td class='text-right'>"+numberWithCommas(data.UNPAID_PRICE_RATIO)+"%</td>";            //미입금률 금액

        tableText += "  <td class='text-right'>"+numberWithCommas(data.PAID_DONE_CNT)+"</td>";                 //입금 종결 건수
        tableText += "  <td class='text-right'>"+numberWithCommas(data.PAID_DONE_BILL_PRICE)+"</td>";          //입금 종결 청구 금액
        tableText += "  <td class='text-right'>"+numberWithCommas(data.PAID_DONE_DEPOSIT_PRICE)+"</td>";       //입금 종결 입금 금액
        tableText += "  <td class='text-right'>"+numberWithCommas(data.PAID_DONE_CNT_RATIO)+"%</td>";           //입금 종결률 건수
        tableText += "  <td class='text-right'>"+numberWithCommas(data.PAID_DONE_PRICE_RATIO)+"%</td>";            //입금 종결률 금액
        tableText += "</tr>       ";
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
    location.href="depositGwExcel.html?"+$("#searchForm").serialize();
}