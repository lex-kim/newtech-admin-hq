/**
 * 헤더에서 일반/공제/렌트 colspan
 */
getCol = (dataArray, type) => {
    const array = dataArray.filter((item)=>{
        return item.RIT_ID == type;
    });
    return array.length*2;
}

setTableHeader = (dataArray) => {
    HEADER_INSU_LIST = dataArray;
    hidedHeaderCol();

    let headerHtml = '';
    let headerSubHtml = '';
    dataArray.map((item)=>{
        let hidetdHtml = '';
        if(item.RIT_ID != INSU_TYPE_NORMAL){
            hidetdHtml = ' hidetd';
        }
        headerHtml += '<th colspan="2" class="text-center '+hidetdHtml+'">'+item.MII_NM+'</th>';
        headerSubHtml += "<th class='text-center "+hidetdHtml+"'>등록</th><th class='text-center "+hidetdHtml+"'>등록일자</th>"
    });
    $("#insuHeader").append(headerHtml);
    $("#insuSubHeader").append(headerSubHtml);
    hidedHeaderCol();
    const tdLength = $('#mainTableCpst th').length;
    setInitTableData(tdLength);
}

setTable = (totalListCount, dataArray, currentPage) => {
    let tableText = '';
  
    dataArray.map((data, index)=>{
      tableText += "<tr id='tr_"+data.MGG_ID+"'>";
      tableText += "<td class='text-center'>"+data.IDX+"</td>";
      tableText += "<td class='text-center'>"+data.CSD_NM+"</td>";
      tableText += "<td class='text-left'>"+data.CRR_NM+"</td>";
      tableText += "<td class='text-left'>"+data.MGG_NM+"</td>";
      tableText += "<td class='text-center'>"+bizNoFormat(data.MGG_BIZ_ID)+"</td>";
      tableText += "<td class='text-center'>"+data.RCT_NM+"</td>"; 
      tableText += setInsuBodyTable(data.INSU_ARRAY, data.MGG_ID);
      tableText += "</tr> ";  
    });
    paging(totalListCount, currentPage, 'getList'); 
    $('#dataTable').html(tableText);
    hidedHeaderCol(); 
}

/** 메인리스트 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
    const tdLength = $('#mainTableCpst th').length;
    const tableText = "<tr><td class='text-center' colspan='"+tdLength+"'>조회요청한 데이터가 존재하지 않습니다.</td></tr>";
    $('.pagination').html('');
    $('#dataTable').html(tableText);
}

setInsuBodyTable = (insuArray, MGG_ID) => {
    let insuBodyHtml = "";
    insuArray.map((insu)=>{
        console.log("11231231==>",insu);
        let hidedHtml = "";
        if(insu.RIT_ID != INSU_TYPE_NORMAL){
            hidedHtml = " hidetd";
        }
        if(insu.DATA == ''){
            insuBodyHtml += '<td class="'+hidedHtml+'" colspan="2"><button type="button" class="btn btn-default" onclick="addInsuManager(\''+MGG_ID+'\',\''+insu.MII_ID+'\');">등록</button></td>';
        }else{
            const data = insu.DATA ;
            let spanHtml = "";
            if(data.split('||')[2] >= 12){
                spanHtml = "class='warning'";
            }
            insuBodyHtml += '<td class="'+hidedHtml+'">';
            insuBodyHtml += '<a href="javascript:openModifyManage(\''+MGG_ID+'\',\''+insu.MII_ID+'\',\''+data.split('||')[3]+'\')">';
            insuBodyHtml += '<span '+spanHtml+'>'+data.split('||')[0]+'</a></td>';   //이름
            insuBodyHtml += '<td class="'+hidedHtml+'"><span '+spanHtml+'>'+data.split('||')[1]+'</td>';   //등록날짜
        }
    });
    return insuBodyHtml;
}