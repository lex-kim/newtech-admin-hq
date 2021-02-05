const pageName = "limit.html";
const LIMIT_URL ='act/limit_act.php'

$(document).ready(function() {
    $('#indicator').hide();
    const tdLength = $('table tr th').length;
    setInitTableData(tdLength, 'limit_list_contents');

    /* datepicker 설정 */
    $('#SEARCH_START_DT').datepicker({
        dateFormat : 'yy-mm-dd',
    });
    $('#SEARCH_END_DT').datepicker({
        dateFormat : 'yy-mm-dd',
    });
    $('#PCE_SET_DT').datepicker({
        dateFormat : 'yy-mm-dd',
    });

    $("#searchForm").submit(function(e){
        e.preventDefault();
        getList(1);
    });

    
});

validationData = () => {
    
    if($('#PCE_NEW_LIMIT_PRICE').val().trim()==''){
        alert('변경 후 금액을 입력해주세요.');
        $('#PCE_NEW_LIMIT_PRICE').focus();
        console.log('return false');
        return false;
    }else {
        return true;
    }
}

/** 데이터 세팅 */
getData = () => {
    const data = {
        REQ_MODE : CASE_READ
    };
    callAjax(LIMIT_URL, 'GET', data).then((result)=>{
        const data = JSON.parse(result);
        if(data.status == OK){
            const item = data.data; 
            showUpdateModal(item);
        }else{
            const resultText ='서버와의 연결이 원활하지 않습니다.' ;
            alert(resultText);
        } 
    });
}

/* 업데이트 모달 */
showUpdateModal = (item) => {
    $('#REQ_MODE').val(CASE_UPDATE);
    $('#submitBtn').attr('onclick', 'updateData()');

    /* 보험사/담당자/책임자 정보 */
    $('#PCE_SET_DT').val(item.PCE_SET_DT);
    $('#PCE_CURRENT_LIMIT_PRICE').val(item.PCE_CURRENT_LIMIT_PRICE);
    $('#PCE_CURRENT_LIMIT_PRICE').attr('readonly', true);
    $('#PCE_NEW_LIMIT_PRICE').val('');

    $('#add-new').modal();
  }

/** 업데이트 */
updateData = () => {
    console.log('chk vali');
    if(validationData()){
        const data = {
            REQ_MODE: CASE_UPDATE,

            PCE_SET_DT : $("#PCE_SET_DT").val(),
            PCE_CURRENT_LIMIT_PRICE : $("#PCE_NEW_LIMIT_PRICE").val()
        };
        callAjax(LIMIT_URL, 'POST', data).then((result)=>{
            const data = JSON.parse(result);
            if(data.status == OK){
                confirmSwalOneButton('수정에 성공하였습니다.','확인',()=>{location.replace(pageName)});
            }else if(data.status == ERROR){
                alert(data.message);
                $('#PCE_NEW_LIMIT_PRICE').focus();
            }else{
                const resultText ='서버와의 연결이 원활하지 않습니다.' ;
                alert(resultText);
            }   
        });
    }
}




/** 리스트 카운트 수 변경했을 때 리스트 불러오김 */
$('#perPage').change(()=>{
    getList(1);
});



/** 리스트 테이블 동적으로 생성 
 * @param {dataArray : 리스트 데이터, currentPage : 현재페이지(페이징을 위해)} 
*/
setTable = (totalListCount, dataArray, currentPage) => {
    let tableText = '';

    dataArray.map((data, index)=>{
        tableText += "<tr>";
        tableText += "<td class='text-center'>"+data.IDX+"</td>";
        tableText += "<td class='text-center'>"+data.PCH_SET_DT+"</td>";
        tableText += "<td class='text-center'>"+numberWithCommas(Number(data.PCH_OLD_LIMIT_PRICE))+"</td>";
        tableText += "<td class='text-center'>"+numberWithCommas(Number(data.PCH_NEW_LIMIT_PRICE))+"</td>";
        tableText += "<td class='text-center'>"+data.WRT_DTHMS+"</td>";
        tableText += "<td class='text-center'>"+data.WRT_USERID+"</td>"
        tableText += "</tr>"
        
    });
    paging(totalListCount, currentPage, 'getList'); 
    $('#limit_list_contents').html(tableText);
}

/** 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
    const tableText = "<tr><td class='text-center' colspan='6'>조회요청한 데이터가 존재하지 않습니다.</td></tr>";
    $('.pagination').html('');
    $('#limit_list_contents').html(tableText);
}

/** 검색버튼 눌렀을 때 */
searchData = () => {
    if($('#SEARCH_START_DT').val().trim() != '' && $('#SEARCH_END_DT').val().trim() == ''){
        alert("조회를 원하는 기간을 설정해주세요.");
        $("#SEARCH_START_DT").focus();
    }else{
        getList(1);
    }
}

/**리스트 */
getList = (currentPage) => {
    const data = {
        REQ_MODE: CASE_LIST,
        PER_PAGE:  $("#perPage").val(),
        CURR_PAGE:  currentPage,

        /** 검색 */
        SEARCH_START_DT: $('#SEARCH_START_DT').val(),
        SEARCH_END_DT: $('#SEARCH_END_DT').val(),
    };

    console.log(data);
    callAjax(LIMIT_URL, 'GET', data).then((result)=>{
        const data = JSON.parse(result);
        console.log(data);
        if(data.status == OK){
            const item = data.data;
            setTable(item[1], item[0], currentPage);
        }else{
            setHaveNoDataTable();
        }
    });
    
}

/**엑셀다운 */
excelDownload = () => {
    // $('#searchForm').attr("method","GET");
    // $('#searchForm').attr("action","limitExcel.html");
    // $('#searchForm').submit();

    excelDownload = () => {
        location.href="limitExcel.html?"+$("#searchForm").serialize();
    }
}