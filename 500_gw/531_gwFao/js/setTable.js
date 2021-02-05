/** 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
    const tdLength = $('table tr th').length;
    const tableText = "<tr><td class='text-center' colspan='"+tdLength+"'>조회된 데이터가 존재하지 않습니다.</tr></tr>";
    $('.pagination').html('');
    $('#dataTable').html(tableText);
}

/** 리스트 테이블 동적으로 생성 
 * @param {dataArray : 리스트 데이터, currentPage : 현재페이지(페이징을 위해)} 
*/
setTable = (totalListCount, dataArray, currentPage) => {
    let tableText = '';
    
    dataArray.map((data)=>{
        tableText += "<tr>";
        tableText += "<td class='text-center'>"+data.IDX+"</td>";
        tableText += "<td class='text-center'>"+data.CSD_NM+"</td>";
        tableText += "<td class='text-left'>"+data.CRR_NM+"</td>";
        tableText += "<td class='text-left'>"+data.MGG_NM+"</td>";
        tableText += "<td class='text-center'>"+bizNoFormat(data.MGG_BIZ_ID)+"</td>";
        tableText += "<td class='text-center'>"+data.RCT_NM+"</td>";
        tableText += "<td class='text-center'>"+data.MGG_BIZ_START_DT+"</td>"
        tableText += "<td class='text-center'>"+data.MGG_BIZ_END_DT+"</td>";
        tableText += "<td class='text-center'>"+data.AVG_CNT+"</td>";
        tableText += "<td class='text-center'>"+data.AVG_PRICE+"</td>";
        tableText += "<td class='text-center'>"+data.LAST_DT+"</td>";
        tableText += "<td class='text-center'>"+phoneFormat(data.MGG_OFFICE_TEL)+"</td>";
        tableText += "<td class='text-center'>"+phoneFormat(data.MGG_OFFICE_FAX)+"</td>";
        tableText += "<td class='text-center'>"+data.MGG_CEO_NM_AES+"</td>";
        tableText += "<td class='text-center'>"+phoneFormat(data.MGG_CEO_TEL_AES)+"</td>";
        tableText += "<td class='text-center'>"+data.MGG_KEYMAN_NM_AES+"</td>";
        tableText += "<td class='text-center'>"+phoneFormat(data.MGG_KEYMAN_TEL_AES)+"</td>";
        tableText += "<td class='text-center'>"+data.MGG_FACTORY_NM_AES+"</td>";
        tableText += "<td class='text-center'>"+phoneFormat(data.MGG_FACTORY_TEL_AES)+"</td>";
        tableText += "<td class='text-center'>"+data.MGG_CLAIM_NM_AES+"</td>";
        tableText += "<td class='text-center'>"+phoneFormat(data.MGG_CLAIM_TEL_AES)+"</td>";
        tableText += "<td class='text-center'>"+data.MGG_PAINTER_NM_AES+"</td>";
        tableText += "<td class='text-center'>"+phoneFormat(data.MGG_PAINTER_TEL_AES)+"</td>";
        tableText += "<td class='text-center'>"+data.MGG_METAL_NM_AES+"</td>";
        tableText += "<td class='text-center'>"+phoneFormat(data.MGG_METAL_TEL_AES)+"</td>";
        if($("#AUTH_UPDATE").val() == YN_Y){
            tableText += "<td class='text-center'><button type='button' class='btn btn-default' onclick='showUpdateModal("+JSON.stringify(data)+")'>수정</button></td>";
        }
        tableText += "</tr>";
        
    });
    paging(totalListCount, currentPage, 'getList'); 
    $('#dataTable').html(tableText);
}

