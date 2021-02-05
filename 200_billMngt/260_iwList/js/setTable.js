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
        tableText += "<td class='text-right'>"+numberWithCommas(Number(data.CLAIM_CNT))+"</td>";
        (data.INSU_ARRAY).map((item)=>{
            tableText += "<td class='text-right'>"+numberWithCommas(Number(item))+"</td>";
        });
        tableText += "<td class='text-center'>"+data.MGG_INSU_MEMO+"</td>";
        tableText += "<td class='text-center'>"+data.MGG_MANUFACTURE_MEMO+"</td>";
        
        tableText += "</tr>"
        
    });
    paging(totalListCount, currentPage, 'getList'); 
    $('#dataTable').html(tableText);
}

setHeaderTable = (insuArray) => {
    let tableText = '';
    const normalInsu = insuArray.filter((item)=>{
        return item.RIT_ID == '11110';
    });
    const dedlInsu = insuArray.filter((item)=>{
        return item.RIT_ID == '11111';
    });
    const rentInsu = insuArray.filter((item)=>{
        return item.RIT_ID == '11112';
    });

    tableText += "<tr>";
    tableText += '<tr class="active">';
    tableText += '<th class="text-center" rowspan="2">순번</th>';
    tableText += '<th class="text-center" rowspan="2">공업사코드</th>';
    tableText += '<th class="text-center" rowspan="2">구분</th>';
    tableText += '<th class="text-center" rowspan="2">지역</th>';
    tableText += '<th class="text-center" rowspan="2">공업사</th>';
    tableText += '<th class="text-center" rowspan="2">사업자번호</th>';
    tableText += '<th class="text-center" rowspan="2">청구식</th>';
    tableText += '<th class="text-center" rowspan="2">거래여부</th>';
    tableText += '<th class="text-center" rowspan="2">개시일</th>';
    tableText += '<th class="text-center" rowspan="2">종료일</th>';
    tableText += '<th class="text-center" rowspan="2">계</th>';
    tableText += '<th class="text-center" id="header_11110" colspan="'+normalInsu.length+'">일반</th>';
    tableText += '<th class="text-center" id="header_11111"  colspan="'+dedlInsu.length+'">공제</th>';
    tableText += '<th class="text-center" id="header_11112"  colspan="'+rentInsu.length+'">렌트</th>';
    tableText += '<th class="text-center" colspan="2">협력업체</th>';
    tableText += '</tr>';
    tableText += '<tr class="active">';
    insuArray.map((data, index)=>{
        tableText +=  '<th class="text-center" id='+data.MII_ID+'>'+data.MII_NM+'</th>';
        
    });
    tableText += '<th class="text-center">보험사</th>';
    tableText += '<th class="text-center">제조사</th>';
    tableText += '</tr>';

    $('#headerTable').html(tableText);

    if(normalInsu.length == 0){
        $('#header_11110').hide();
    }
    if(dedlInsu.length == 0){
        $('#header_11111').hide();
    }
    if(rentInsu.length == 0){
        $('#header_11112').hide();
    }
    const tdLength = $('table th').length;
    setInitTableData(tdLength);
}
