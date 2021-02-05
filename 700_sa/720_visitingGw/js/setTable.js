/** 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
    const tdLength = $('table th').length;
    const tableText = "<tr><td class='text-center' colspan='"+tdLength+"'>조회된 데이터가 존재하지 않습니다.</tr></tr>";
    $('.pagination').html('');
    $('#dataTable').html(tableText);
}

onLinkGoggleMap = (addr) => {
    window.open('https://www.google.com/maps/place/'+addr, '_blank'); 
}

setTable = (totalListCount, dataArray, currentPage) => {
    let tableText = '';
    listArray = dataArray;
    dataArray.map((data, index)=>{
        tableText += "<tr>";
        tableText += "<td class='text-center'><input type='checkbox' class='listcheck' name='multi' value='"+index+"'></td>";
        tableText += "<td class='text-center'>"+data.IDX+"</td>";
        tableText += "<td class='text-center'>"+data.CSD_NM+"</td>";
        tableText += "<td class='text-center'>"+data.CRR_NM+"</td>";
        tableText += "<td class='text-center'>"+data.MGG_CIRCUIT_ORDER+"</td>";
        tableText += "<td class='text-center'>"+data.MGG_NM+"</td>";
        tableText += "<td class='text-center'>"+data.RET_NM+"</td>";
        tableText += "<td class='text-center'>"+data.RCT_NM+"</td>";
        tableText += "<td class='text-center'>"+data.WRT_USERID+"</td>";
        tableText += "<td class='text-center'>"+data.NSU_VISIT_DT+"</td>";
        tableText += "<td class='text-center'>"+data.NSU_SUPPLY_DT+"</td>";
        tableText += "<td class='text-center'>"+data.NSU_PROC_YN+"</td>";
        tableText += "<td class='text-center'>"+data.NSU_PROC_USERID+"</td>";
        tableText += "<td class='text-center'>"+data.NSU_PROC_DTHMS+"</td>";
        tableText += "<td class='text-center'>"+data.RSF_NM+"</td>";
        tableText += "<td class='text-center'><a href='javascript:onLinkGoggleMap(\""+data.NSU_ADDR+"\")'>위치확인</a></td>";
        tableText += "<td class='text-center'>";
        if($("#AUTH_UPDATE").val() == YN_Y){
            tableText += "<button type='button' class='btn btn-default' onclick='openGaragePopup(\""+data.NSU_ID+"\",\""+data.MGG_ID+"\");'>수정</button>";
        }
        if($("#AUTH_DELETE").val() == YN_Y){
            tableText += "<button type='button' class='btn btn-danger' onclick='openDeleteModal("+index+");'>삭제</button>";
        }
        tableText += "</td>";
        tableText += "</tr>"
        
    });
    paging(totalListCount, currentPage, 'getList'); 
    $('#dataTable').html(tableText);
}

