/** 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
    const tdLength = $('table th').length;
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
        tableText += "<td class='text-center'>"+data.MGG_ID+"</td>";
        tableText += "<td class='text-center'>"+data.CSD_NM+"</td>";
        tableText += "<td class='text-left'>"+data.CRR_NM+"</td>";
        tableText += "<td class='text-left'>"+data.MGG_NM+"</td>";
        tableText += "<td class='text-center'>"+bizNoFormat(data.MGG_BIZ_ID)+"</td>";
        tableText += "<td class='text-center'>"+data.RET_NM+"</td>";
        tableText += "<td class='text-center'>"+data.RCT_NM+"</td>";
        tableText += "<td class='text-center'>"+data.MGG_BIZ_START_DT+"</td>";
        tableText += "<td class='text-center'>"+data.MGG_BIZ_END_DT+"</td>";
        tableText += "<td class='text-center'>"+data.NB_CLAIM_DT+"</td>";
        tableText += "<td class='text-center'>"+phoneFormat(data.MGG_OFFICE_FAX)+"</td>";
        tableText += "<td class='text-center'>"+data.MGG_CLAIM_NM_AES+"</td>"
        tableText += "<td class='text-center'>"+phoneFormat(data.MGG_CLAIM_TEL_AES)+"</td>";
        tableText += "<td class='text-right'>"+data.STOCK_PRICE+"</td>";
        tableText += "<td class='text-right'>"+data.STOCK+"</td>";
        tableText += "<td class='text-right'>"+data.CLAIM_AVG+"</td>";
        tableText += "<td class='text-right'>"+data.CLAIM_CNT+"</td>";
        tableText += "<td class='text-right'>"+data.CLAIM_CNT_12+"</td>";
        tableText += "<td class='text-right'>"+data.CLAIM_CNT_11+"</td>";
        tableText += "<td class='text-right'>"+data.CLAIM_CNT_10+"</td>";
        tableText += "<td class='text-right'>"+data.CLAIM_CNT_9+"</td>";
        tableText += "<td class='text-right'>"+data.CLAIM_CNT_8+"</td>";
        tableText += "<td class='text-right'>"+data.CLAIM_CNT_7+"</td>";
        tableText += "<td class='text-right'>"+data.CLAIM_CNT_6+"</td>";
        tableText += "<td class='text-right'>"+data.CLAIM_CNT_5+"</td>";
        tableText += "<td class='text-right'>"+data.CLAIM_CNT_4+"</td>";
        tableText += "<td class='text-right'>"+data.CLAIM_CNT_3+"</td>";
        tableText += "<td class='text-right'>"+data.CLAIM_CNT_2+"</td>";
        tableText += "<td class='text-right'>"+data.CLAIM_CNT_1+"</td>";
        tableText += "<td class='text-center'>"+data.MGG_INSU_MEMO+"</td>";
        tableText += "<td class='text-center'>"+data.MGG_MANUFACTURE_MEMO+"</td>";
        tableText += "</tr>"
        
    });
    paging(totalListCount, currentPage, 'getList'); 
    $('#dataTable').html(tableText);
}
