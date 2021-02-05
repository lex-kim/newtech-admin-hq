const ADDRESS_URL = 'act/autoAddress_act.php';
let CURR_PAGE = 1;
let tdLength = 0;

$(document).ready(() =>{
    $('#indicator').hide();
    tdLength = $('table tr th').length;
    setInitTableData(tdLength);

    /** 검색 */
    $("#searchForm").submit(function(e){
        e.preventDefault();
            getList(1);
    });

});


/**
 * MAIN LIST 
 * @param {int} currentPage  조회하는 페이지
 */
getList = (currentPage) =>{
    CURR_PAGE = (currentPage == undefined) ? CURR_PAGE : currentPage;
    let form = $("#searchForm")[0];
    let formData = new FormData(form);
    formData.append('REQ_MODE', CASE_LIST);
    formData.append('PER_PAGE', $("#perPage").val());
    formData.append('CURR_PAGE', CURR_PAGE);

    callFormAjax(ADDRESS_URL, 'POST', formData).then((result)=>{
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

    dataArray.map((data)=>{
        tableText += "<tr>";
        tableText += "  <td class='text-center'>"+data.IDX+"</td>";
        tableText += "  <td class='text-left'>"+data.NAA_NM+"</td>";
        tableText += "  <td class='text-center'>"+phoneFormat(data.NAA_PHONE)+"</td>";
        tableText += "  <td class='text-left'>"+data.NAA_CPY+"</td>";
        tableText += "  <td class='text-center'>"+data.NAA_ROLE+"</td>";
        tableText += "  <td class='text-center'>"+data.LAST_DTHMS+"</td>";
        tableText += "  <td class='text-center'>"+data.WRT_DTHMS+"</td>";
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


excelDownload = () => {
    location.href="autoaddressExcel.html?"+$("#searchForm").serialize();
}