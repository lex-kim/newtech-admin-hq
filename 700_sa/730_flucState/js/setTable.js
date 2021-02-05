/** 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
    const tdLength = $('table th').length;
    const tableText = "<tr><td class='text-center' colspan='"+tdLength+"'>조회된 데이터가 존재하지 않습니다.</tr></tr>";
    $('.pagination').html('');
    $('#dataTable').html(tableText);
}
let tableText_1 = "";

function setDivisionHeaderTable(dataArray){
    let tableText = '';    //지역
    let tableText_1 = "";  // 소계.변동
    let tableText_2 = "";  // 거래처/신규/중단

    tableText += '<th class="text-center" rowspan="3">집계년월</th>';
    tableText += '<th class="text-center" rowspan="3">청구건수</th>';
    tableText += '<th class="text-center" rowspan="3">청구금액</th>';
    tableText += '<th class="text-center" colspan="4">총거래처수</th>';

    tableText_1 += '<th class="text-center" rowspan="2">소계</th>';
    tableText_1 += '<th class="text-center" colspan="3">거래처변동</th>';
    tableText_2 += '<th class="text-center">거래처</th>';
    tableText_2 += '<th class="text-center">신규</th>';
    tableText_2 += '<th class="text-center">중단</th>';

    dataArray.map((data, index)=>{
        tableText += '<th class="text-center" colspan="4">'+data.CSD_NM+'</th>';  
        tableText_1 += '<th class="text-center" rowspan="2">소계</th>';
        tableText_1 += '<th class="text-center" colspan="3">거래처변동</th>';
        tableText_2 += '<th class="text-center">거래처</th>';
        tableText_2 += '<th class="text-center">신규</th>';
        tableText_2 += '<th class="text-center">중단</th>';
       
    });

    $('#divisionTable').html(tableText);
    $('#divisionTable_1').html(tableText_1);
    $('#divisionTable_2').html(tableText_2);

    const tdLength = $('table th').length;
    setInitTableData(tdLength);
}

setTable = (totalListCount, dataArray, currentPage) => {
    let tableText = '';

    dataArray.map((data, index)=>{
        tableText += "<tr>";
        tableText += "<td class='text-center'>"+data.CLAIM_DT+"</td>";
        tableText += "<td class='text-right'>"+numberWithCommas(data.NB_CNT)+"</td>";
        tableText += "<td class='text-right'>"+numberWithCommas(data.NB_PRICE)+"</td>";
        tableText += "<td class='text-right'>"+numberWithCommas(data.TOTAL_CNT)+"</td>";
        tableText += "<td class='text-right'>"+numberWithCommas(data.TOTAL_ING)+"</td>";
        tableText += "<td class='text-right'>"+numberWithCommas(data.TOTAL_NEW)+"</td>";
        tableText += "<td class='text-right'>"+numberWithCommas(data.TOTAL_END)+"</td>";
        (data.DATA).map(function(divItem){
            tableText += "<td class='text-right'>"+numberWithCommas(divItem[0])+"</td>";
            tableText += "<td class='text-right'>"+numberWithCommas(divItem[1])+"</td>";
            tableText += "<td class='text-right'>"+numberWithCommas(divItem[2])+"</td>";
            tableText += "<td class='text-right'>"+numberWithCommas(divItem[3])+"</td>";
        });
        tableText += "</tr>"
        
    });
    paging(totalListCount, currentPage, 'getList'); 
    $('#dataTable').html(tableText);
}

