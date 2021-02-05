/** 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
    const tdLength = $('table th').length;
    const tableText = "<tr><td class='text-center' colspan='"+tdLength+"'>조회된 데이터가 존재하지 않습니다.</tr></tr>";
    $('.pagination').html('');
    $('#dataTable').html(tableText);
}

setTable = (totalListCount, dataArray, currentPage) => {
    let tableText = '';

    dataArray.map((data, index)=>{
        tableText += "<tr>";
        tableText += "<td class='text-center'>"+data.NB_CLAIM_DT+"</td>";
        tableText += "<td class='text-center'>"+numberWithCommas(data.MGG_CNT)+"</td>";
        tableText += "<td class='text-center'>"+numberWithCommas(data.MGG_11110)+"</td>";
        tableText += "<td class='text-center'>"+numberWithCommas(data.MGG_11111)+"</td>";
        tableText += "<td class='text-center'>"+numberWithCommas(data.MGG_11112)+"</td>";
        tableText += "<td class='text-center'>"+numberWithCommas(data.CLAIM_CNT)+"</td>";
        tableText += "<td class='text-center'>"+numberWithCommas(data.CLAIM_11110)+"</td>";
        tableText += "<td class='text-center'>"+numberWithCommas(data.CLAIM_11111)+"</td>";
        tableText += "<td class='text-center'>"+numberWithCommas(data.CLAIM_11112)+"</td>";
        tableText += "<td class='text-center'>"+numberWithCommas(data.NB_TOTAL_PRICE)+"</td>";
        tableText += "<td class='text-center'>"+data.NB_TOTAL_PRICE_11110+"</td>";
        tableText += "<td class='text-center'>"+data.NB_TOTAL_PRICE_11111+"</td>";
        tableText += "<td class='text-center'>"+data.NB_TOTAL_PRICE_11112+"</td>";
        tableText += "<td class='text-center'>"+numberWithCommas(cuttingMoney(Math.floor(data.NB_TOTAL_PRICE/data.CLAIM_CNT)))+"</td>";
        tableText += "<td class='text-center'>"+data.NB_PRICE_11110+"</td>";
        tableText += "<td class='text-center'>"+data.NB_PRICE_11111+"</td>";
        tableText += "<td class='text-center'>"+data.NB_PRICE_11112+"</td>";
        // tableText += "<td class='text-center'>"+data.MGG_PER_TOTAL+"%</td>";
        tableText += "<td class='text-center'>"+data.MGG_PER_11110+"%</td>";
        tableText += "<td class='text-center'>"+data.MGG_PER_11111+"%</td>";
        tableText += "<td class='text-center'>"+data.MGG_PER_11112+"%</td>";
        tableText += "<td class='text-center'>"+data.TOTAL_PER_TOTAL+"%</td>";
        tableText += "<td class='text-center'>"+data.TOTAL_PER_11110+"%</td>";
        tableText += "<td class='text-center'>"+data.TOTAL_PER_11111+"%</td>";
        tableText += "<td class='text-center'>"+data.TOTAL_PER_11112+"%</td>";
        tableText += "<td class='text-center'>"+data.PRICE_PER_TOTAL+"%</td>";
        tableText += "<td class='text-center'>"+data.PRICE_PER_11110+"%</td>";
        tableText += "<td class='text-center'>"+data.PRICE_PER_11111+"%</td>";
        tableText += "<td class='text-center'>"+data.PRICE_PER_11112+"%</td>";
        
        tableText += "</tr>"
        
    });
    paging(totalListCount, currentPage, 'getList'); 
    $('#dataTable').html(tableText);
}


setTotal = (data) => {
    console.log('setTotal Data >>',data);
    let tableText = '';
    tableText += "<td class='text-center'>총 합계</td>";
    tableText += "<td class='text-center'>"+numberWithCommas(data.MGG_CNT)+"</td>";
    tableText += "<td class='text-center'>"+numberWithCommas(data.MGG_11110)+"</td>";
    tableText += "<td class='text-center'>"+numberWithCommas(data.MGG_11111)+"</td>";
    tableText += "<td class='text-center'>"+numberWithCommas(data.MGG_11112)+"</td>";
    tableText += "<td class='text-center'>"+numberWithCommas(data.CLAIM_CNT)+"</td>";
    tableText += "<td class='text-center'>"+numberWithCommas(data.CLAIM_11110)+"</td>";
    tableText += "<td class='text-center'>"+numberWithCommas(data.CLAIM_11111)+"</td>";
    tableText += "<td class='text-center'>"+numberWithCommas(data.CLAIM_11112)+"</td>";
    tableText += "<td class='text-center'>"+numberWithCommas(data.NB_TOTAL_PRICE)+"</td>";
    tableText += "<td class='text-center'>"+numberWithCommas(data.NB_TOTAL_PRICE_11110)+"</td>";
    tableText += "<td class='text-center'>"+numberWithCommas(data.NB_TOTAL_PRICE_11111)+"</td>";
    tableText += "<td class='text-center'>"+numberWithCommas(data.NB_TOTAL_PRICE_11112)+"</td>";
    tableText += "<td class='text-center'>"+numberWithCommas(cuttingMoney(Math.floor(data.NB_TOTAL_PRICE/data.CLAIM_CNT)))+"</td>";
    tableText += "<td class='text-center'>"+(data.CLAIM_11110 == 0 ? 0 : numberWithCommas(cuttingMoney(Math.floor(data.NB_TOTAL_PRICE_11110/data.CLAIM_11110))))+"</td>";
    tableText += "<td class='text-center'>"+(data.CLAIM_11111 == 0 ? 0 :numberWithCommas(cuttingMoney(Math.floor(data.NB_TOTAL_PRICE_11111/data.CLAIM_11111))))+"</td>";
    tableText += "<td class='text-center'>"+(data.CLAIM_11112 == 0 ? 0 :numberWithCommas(cuttingMoney(Math.floor(data.NB_TOTAL_PRICE_11112/data.CLAIM_11112))))+"</td>";
    // tableText += "<td class='text-center'>"+data.MGG_PER_TOTAL+"%</td>";
    tableText += "<td class='text-center'>"+data.MGG_PER_11110+"%</td>";
    tableText += "<td class='text-center'>"+data.MGG_PER_11111+"%</td>";
    tableText += "<td class='text-center'>"+data.MGG_PER_11112+"%</td>";
    tableText += "<td class='text-center'>"+data.TOTAL_PER_TOTAL+"%</td>";
    tableText += "<td class='text-center'>"+data.TOTAL_PER_11110+"%</td>";
    tableText += "<td class='text-center'>"+data.TOTAL_PER_11111+"%</td>";
    tableText += "<td class='text-center'>"+data.TOTAL_PER_11112+"%</td>";
    tableText += "<td class='text-center'>"+data.PRICE_PER_TOTAL+"%</td>";
    tableText += "<td class='text-center'>"+data.PRICE_PER_11110+"%</td>";
    tableText += "<td class='text-center'>"+data.PRICE_PER_11111+"%</td>";
    tableText += "<td class='text-center'>"+data.PRICE_PER_11112+"%</td>";
    $('#totalRow').html(tableText);
}
