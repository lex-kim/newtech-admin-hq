/** 리스트 테이블 동적으로 생성 
 * @param {dataArray : 리스트 데이터, currentPage : 현재페이지(페이징을 위해)} 
*/
setTable = (totalListCount, dataArray, currentPage) => {
    let tableText = '';
    tableText += '<tr class="border" id="dataFooterTable">';
    tableText +='</tr>';
    
    dataArray.map((data)=>{
        tableText += "<tr>";
        tableText += "<td class='text-center'>"+data.VMS_CSD_NM+"</td>";
        tableText += "<td class='text-left'>"+data.VMS_CRR_NM+"</td>";
        tableText += "<td class='text-center VMS_TOTAL'>"+data.VMS_TOTAL+"</td>";
        tableText += "<td class='text-center VMS_DEAL_TOTAL'>"+data.VMS_DEAL_TOTAL+"</td>";
        tableText += "<td class='text-center VMS_DEAL'>"+data.VMS_10610+"</td>";
        tableText += "<td class='text-center VMS_WILL_STOP'>"+data.VMS_10640+"</td>"
        tableText += "<td class='text-center VMS_STOP_TOTAL'>"+data.VMS_10620+"</td>";
        tableText += "<td class='text-center VMS_STOP_10'>"+data.VMS_STOP_10+"</td>";
        tableText += "<td class='text-center VMS_STOP_20'>"+data.VMS_STOP_20+"</td>";
        tableText += "<td class='text-center VMS_STOP_30'>"+data.VMS_STOP_30+"</td>";
        tableText += "<td class='text-center VMS_STOP_40'>"+data.VMS_STOP_40+"</td>";
        tableText += "<td class='text-center VMS_STOP_50'>"+data.VMS_STOP_50+"</td>";
        tableText += "<td class='text-center VMS_STOP_60'>"+data.VMS_STOP_60+"</td>";
        tableText += "<td class='text-center VMS_STOP_70'>"+data.VMS_STOP_70+"</td>";
        tableText += "<td class='text-center VMS_STOP_80'>"+data.VMS_STOP_80+"</td>";
        tableText += "<td class='text-center VMS_STOP_90'>"+data.VMS_STOP_90+"</td>";
        tableText += "<td class='text-center VMS_UNTRADED_TOTAL'>"+data.VMS_UNTRADED_TOTAL+"</td>";
        tableText += "<td class='text-center VMS_UNTRADED'>"+data.VMS_10650+"</td>";
        tableText += "<td class='text-center VMS_WILL_DEAL'>"+data.VMS_10630+"</td>";
        tableText += "<td class='text-center border'></td>";
        tableText += "<td class='text-center'></td>";
        tableText += "<td class='text-center'></td>";
        tableText += "<td class='text-center'></td>";
        tableText += "</tr>";
        
    });
    paging(totalListCount, currentPage, 'getList'); 
    $('#dataTable').html(tableText);
    setFooterTable();
}

/**
 * 총계를 계산해주는 함수.
 * 해당 class의 html()을 통해 합을 더한다.
 */
setFooterTableData = (classNM) => {
    let totalCount = 0;
    $('.'+classNM).each(function(){
        totalCount += Number($(this).html());
    });
    return totalCount;
}

/**
 * 통계테이블 동적 테이블 생성
 */
setFooterTable = () => {
    let footerHtml = '';
    footerHtml += "<td class='text-center' colspan='2'>총계</td>";
    footerHtml += "<td class='text-center'>"+setFooterTableData('VMS_TOTAL')+"</td>";
    footerHtml += "<td class='text-center'>"+setFooterTableData('VMS_DEAL_TOTAL')+"</td>";
    footerHtml += "<td class='text-center'>"+setFooterTableData('VMS_DEAL')+"</td>";
    footerHtml += "<td class='text-center'>"+setFooterTableData('VMS_WILL_STOP')+"</td>";
    footerHtml += "<td class='text-center'>"+setFooterTableData('VMS_STOP_TOTAL')+"</td>";
    footerHtml += "<td class='text-center'>"+setFooterTableData('VMS_STOP_10')+"</td>";
    footerHtml += "<td class='text-center'>"+setFooterTableData('VMS_STOP_20')+"</td>";
    footerHtml += "<td class='text-center'>"+setFooterTableData('VMS_STOP_30')+"</td>";
    footerHtml += "<td class='text-center'>"+setFooterTableData('VMS_STOP_40')+"</td>";
    footerHtml += "<td class='text-center'>"+setFooterTableData('VMS_STOP_50')+"</td>";
    footerHtml += "<td class='text-center'>"+setFooterTableData('VMS_STOP_60')+"</td>";
    footerHtml += "<td class='text-center'>"+setFooterTableData('VMS_STOP_70')+"</td>";
    footerHtml += "<td class='text-center'>"+setFooterTableData('VMS_STOP_80')+"</td>";
    footerHtml += "<td class='text-center'>"+setFooterTableData('VMS_STOP_90')+"</td>";
    footerHtml += "<td class='text-center'>"+setFooterTableData('VMS_UNTRADED_TOTAL')+"</td>";
    footerHtml += "<td class='text-center'>"+setFooterTableData('VMS_UNTRADED')+"</td>";
    footerHtml += "<td class='text-center'>"+setFooterTableData('VMS_WILL_DEAL')+"</td>";
    footerHtml += "<td class='text-center border'></td>";
    footerHtml += "<td class='text-center'></td>";
    footerHtml += "<td class='text-center'></td>";
    footerHtml += "<td class='text-center'></td>"
    $('#dataFooterTable').html(footerHtml);
}

/**
 * 고정 상위 테이블 TR 생성
 */
setTableTR = () => {
    let tableText = '';
    getStopInfo().then((dataArray) => {
        if(dataArray.length > 0){
            $('#stopTd').attr("colspan", dataArray.length+1);
            tableText += "<th class='text-center'>소계</th>";
            tableText += "<th class='text-center'>거래</th>";
            tableText += "<th class='text-center'>중단예정</th>";
            tableText += "<th class='text-center'>소계</th>";
            dataArray.map((data)=>{
                tableText += "<th class='text-center'>"+data.NM+"</th>";
            });
            tableText += "<th class='text-center'>소계</th>"
            tableText += "<th class='text-center'>미거래</th>";
            tableText += "<th class='text-center'>거래예정</th>";
            tableText += "<th class='text-center border'>소계</th>";
            tableText += "<th class='text-center'>거래</th>";
            tableText += "<th class='text-center'>중단</th>";
            tableText += "<th class='text-center'>미거래</th>";
        } 
        $('#dataTrTable').append(tableText);
    });

    
}


/**
 * 거래중단이유 리스트 가져오기
 */
getStopInfo = () => {
    const data = {
        REQ_MODE: GET_REF_OPTION,
        REF_TYPE : GET_REF_STOP_REASON_TYPE
    };
  
    const ajaxUrl = '/common/gwReference.php';
    return callAjax(ajaxUrl, 'GET', data).then((result)=>{
        const data = JSON.parse(result);
        if(data.status == OK){
            return data.data;
        }else{
            alert("거래중단사유 정보르 가져오는데 실패하였습니다.");
        }
       
    });
}