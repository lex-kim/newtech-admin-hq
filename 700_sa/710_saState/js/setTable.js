/** 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
    const tdLength = $('table th').length;
    const tableText = "<tr><td class='text-center' colspan='"+tdLength+"'>조회된 데이터가 존재하지 않습니다.</tr></tr>";
    $('.pagination').html('');
    $('#dataTable').html(tableText);
}

function setHeaderTable(){
    const date = new Date();
    const year = date.getFullYear();
    const month = date.getMonth() + 1;

    $('#VISIT_THIS_YEAR').html(year);
    $('#VISIT_BEFORE_YEAR').html(year-1);

    $('.THIS_MONTH').html(month);
    $('.D1_MONTH').html((month-1 <= 0 ? 12 +(month-1 ) : month-1 ));
    $('.D2_MONTH').html((month-2 <= 0 ? 12 +(month-2 ) : month-2 ));
    $('.D3_MONTH').html((month-3 <= 0 ? 12 +(month-3 ) : month-3 ));
}

function setVisitTable(dataArray){
   
    let html = "";
    if(dataArray.length > 0){
        let MONTH_TOTAL_CNT = 0;
        let MONTH_2_CNT = 0;
        let MONTH_1_CNT = 0;
        let MONTH_ODD_CNT = 0;

        dataArray.map(function(item){
            MONTH_TOTAL_CNT += Number(item.MONTH_TOTAL_CNT);
            MONTH_2_CNT += Number(item.MONTH_2_CNT);
            MONTH_1_CNT += Number(item.MONTH_1_CNT);
            MONTH_ODD_CNT += Number(item.MONTH_ODD_CNT);

            html += "<tr>";
            html += "<td>"+item.CSD_NM+"</td>";
            html += "<td>"+item.MONTH_TOTAL_CNT+"</td>";
            html += "<td>"+item.MONTH_2_CNT+"</td>";
            html += "<td>"+item.MONTH_1_CNT+"</td>";
            html += "<td>"+item.MONTH_ODD_CNT+"</td>";
            html += "</tr>";
        });
        let totalHtml = " <tr>";
        totalHtml += "<td>총거래업체수</td>";
        totalHtml += "<td>"+MONTH_TOTAL_CNT+"</td>";
        totalHtml += "<td>"+MONTH_2_CNT+"</td>";
        totalHtml += "<td>"+MONTH_1_CNT+"</td>";
        totalHtml += "<td>"+MONTH_ODD_CNT+"</td>";
        totalHtml += "</tr>";
        $('#VISIT_CYCLE_TABLE').html(totalHtml+html);
    }else{
        const thCNT = $('.salestate-subtable table tr th').length;
        html = "<tr><td colspan='"+thCNT+"'>데이터가 존재하지 않습니다.</td></tr>"
        $('#VISIT_CYCLE_TABLE').html(html);
    }
    
    
}

function getClaimSize(CNT){
    let CLAIM_SIZE = "";    //청구규모
    if(CNT >=0 && CNT <10){
        CLAIM_SIZE = "소";
    }else if(CNT >=10 && CNT <40){
        CLAIM_SIZE = "중";
    }else{
        CLAIM_SIZE = "대";
    }
    return CLAIM_SIZE;
}

function setBackgroundColor(data){
    if(data == YN_Y){
        return "style=\"background-color:gray\"";
    }else{
        return '';
    }
}

function setGaragePartsTable(dataArray){
    if(dataArray.split('||').length > 0){
        let text = "";
        dataArray.split('||').map(function(item){
            text += item+'<br>';
        });
        return text;
    }else{
        return '';
    }
}

setTable = (totalListCount, dataArray, currentPage) => {
    let tableText = '';
    dataArray.map((data, index)=>{
        tableText += "<tr>";
        // tableText += '<td class="text-center"><input type="checkbox" value=""></td>';
        tableText += '<td class="text-center">'+data.IDX+'</td>';
        tableText += '<td class="text-center">'+data.MGG_ID+'</td>';
        tableText += '<td class="text-center">'+data.CSD_NM+'</td>';
        tableText += '<td class="text-center">'+data.CRR_NM+'</td>';
        tableText += '<td class="text-right">'+data.MGG_CIRCUIT_ORDER+'</td>';
        tableText += '<td class="text-center">'+data.MGG_NM+'</td>';
        tableText += '<td class="text-left">'+data.THIS_YEAR_VISIT+'</td>';
        tableText += '<td class="text-left">'+data.LAST_YEAR_VISIT+'</td>';
        tableText += '<td class="text-center">'+data.VISIT_CYCLE+'</td>';
        tableText += '<td class="text-right">'+data.THIS_VISIT_CNT+'</td>';
        tableText += '<td class="text-right">'+data.THIS_SUPPLY_CNT+'</td>';
        tableText += '<td class="text-right">'+data.D1_VISIT_CNT+'</td>';
        tableText += '<td class="text-right">'+data.D1_SUPPLY_CNT+'</td>';
        tableText += '<td class="text-right">'+data.D2_VISIT_CNT+'</td>';
        tableText += '<td class="text-right">'+data.D2_SUPPLY_CNT+'</td>';
        tableText += '<td class="text-center">'+data.RCT_NM+'</td>';
        tableText += '<td class="text-center">'+data.MGG_BIZ_START_DT+'</td>';
        tableText += '<td class="text-center">'+data.MGG_BIZ_END_DT+'</td>';
        tableText += '<td class="text-center">'+data.DIFF_CLAIM_DT+'</td>';
        tableText += '<td class="text-right">'+data.AVG_BILL_CNT+'</td>';
        tableText += '<td class="text-right">'+data.AVG_BILL_PRICE+'</td>';
        tableText += '<td class="text-right" '+setBackgroundColor(data.D1_BILL_BGCOLOR_YN)+'>'+numberWithCommas(data.D1_BILL_CNT)+'</td>';
        tableText += '<td class="text-right" '+setBackgroundColor(data.D1_BILL_BGCOLOR_YN)+'>'+numberWithCommas(data.D1_BILL_PRICE)+'</td>';
        tableText += '<td class="text-right" '+setBackgroundColor(data.D2_BILL_BGCOLOR_YN)+'>'+numberWithCommas(data.D2_BILL_CNT)+'</td>';
        tableText += '<td class="text-right" '+setBackgroundColor(data.D2_BILL_BGCOLOR_YN)+'>'+numberWithCommas(data.D2_BILL_PRICE)+'</td>';
        tableText += '<td class="text-right" '+setBackgroundColor(data.D3_BILL_BGCOLOR_YN)+'>'+numberWithCommas(data.D3_BILL_CNT)+'</td>';
        tableText += '<td class="text-right" '+setBackgroundColor(data.D3_BILL_BGCOLOR_YN)+'>'+numberWithCommas(data.D3_BILL_PRICE)+'</td>';
        tableText += '<td class="text-center">'+bizNoFormat(data.MGG_BIZ_ID)+'</td>';
        tableText += '<td class="text-center">'+data.RET_NM+'</td>';
        tableText += '<td class="text-center hidetd">'+data.MGG_RET_DT+'</td>';
        tableText += '<td class="text-center">'+data.RAT_NM+'</td>';
        tableText += '<td class="text-center hidetd">'+data.MGG_RAT_NM+'</td>';
        tableText += '<td class="text-left">'+data.MGG_AOS_MEMO+'</td>'; //청구프로그램 노출값
        tableText += '<td class="text-left">'+data.MGG_LOGISTICS_YN+'</td>';
        tableText += '<td class="text-center"></td>';
        tableText += '<td class="text-right"></td>';
        tableText += '<td class="text-left">'+data.MGG_ADDRESS_OLD+'</td>';
        tableText += '<td class="text-left">'+data.MGG_ADDRESS+'</td>';
        tableText += '<td class="text-center">'+phoneFormat(data.MGG_OFFICE_TEL)+'</td>';
        tableText += '<td class="text-center">'+phoneFormat(data.MGG_OFFICE_FAX)+'</td>';
        tableText += '<td class="text-center">'+data.MGG_CEO_NM_AES+'</td>';
        tableText += '<td class="text-center">'+phoneFormat(data.MGG_CEO_TEL_AES)+'</td>';
        tableText += '<td class="text-center hidetd">'+data.MGG_KEYMAN_NM_AES+'</td>';
        tableText += '<td class="text-center hidetd">'+phoneFormat(data.MGG_KEYMAN_TEL_AES)+'</td>';
        tableText += '<td class="text-center">'+data.MGG_CLAIM_NM_AES+'</td>';
        tableText += '<td class="text-center">'+phoneFormat(data.MGG_CLAIM_TEL_AES)+'</td>';
        tableText += '<td class="text-center hidetd">'+data.MGG_FACTORY_NM_AES+'</td>';
        tableText += '<td class="text-center hidetd">'+phoneFormat(data.MGG_FACTORY_TEL_AES)+'</td>';
        tableText += '<td class="text-center hidetd">'+data.MGG_PAINTER_NM_AES+'</td>';
        tableText += '<td class="text-center hidetd">'+phoneFormat(data.MGG_PAINTER_TEL_AES)+'</td>';
        tableText += '<td class="text-center hidetd">'+data.MGG_METAL_NM_AES+'</td>';
        tableText += '<td class="text-center hidetd">'+phoneFormat(data.MGG_METAL_TEL_AES)+'</td>';
        tableText += '<td class="text-center hidetd">'+data.MGG_WAREHOUSE_CNT+'</td>';
        tableText += '<td class="text-center hidetd">'+data.RMT_NM+'</td>';
        tableText += '<td class="text-center hidetd">'+data.ROT_NM+'</td>';
        tableText += '<td class="text-center">'+getClaimSize(data.AVG_BILL_CNT)+'</td>';
        tableText += '<td class="text-center">'+data.MGG_INSU_MEMO+'</td>';
        tableText += '<td class="text-center">'+data.MGG_MANUFACTURE_MEMO+'</td>';
        tableText += '<td class="text-center">'+data.MGG_BILL_MEMO+'</td>';
        tableText += '<td class="text-center">'+data.MGG_GARAGE_MEMO+'</td>';
        tableText += '<td class="text-center">'+data.MGG_FRONTIER_NM+'</td>';
        tableText += '<td class="text-center hidetd">'+data.MGG_COLLABO_NM+'</td>';
        tableText += '<td class="text-center">'+data.MGG_CONTRACT_DT+'</td>';
        tableText += '<td class="text-center hidetd">'+data.MGG_BANK_NUM_AES+'</td>';
        tableText += '<td class="text-center">'+data.RCT_ID+'</td>';
        tableText += '<td class="text-center">'+data.MGG_OTHER_BIZ_NM+'</td>';
        tableText += '<td class="text-right">'+data.SUPPLY_PARTS+'</td>';
        tableText += '<td class="text-right">'+data.CLAIM_PARTS+'</td>';
        tableText += '<td class="text-right">'+data.STOCK_PARTS+'</td>';
        tableText += '<td class="text-right" '+setBackgroundColor(data.OVER_STD_YN)+'>'+data.OVER_STD+'</td>';
        tableText += '<td class="text-left">'+setGaragePartsTable(data.CPP_NM_SHORT)+'</td>';
        tableText += '<td class="text-left">'+setGaragePartsTable(data.CPPS_NM_SHORT)+'</td>';
        tableText += '<td class="text-left">'+setGaragePartsTable(data.NGP_RATIO)+'</td>';
        tableText += '<td class="text-center hidetd">'+data.MGG_SALES_NM+'</td>';
        tableText += '<td class="text-center">'+data.RST_NM+'</td>';
        tableText += '<td class="text-center hidetd">'+data.MGG_SALES_MEET_LVL+'</td>';
        tableText += '<td class="text-center hidetd">'+data.MGG_SALES_MEET_NM_AES+'</td>';
        tableText += '<td class="text-center hidetd">'+phoneFormat(data.MGG_SALES_MEET_TEL_AES)+'</td>';
        tableText += '<td class="text-center">'+data.RRT_NM+'</td>';
        tableText += '<td class="text-center hidetd">'+data.MGG_SALES_WHYNOT+'</td>';
        tableText += '<td class="text-center">'+data.LAST_VISIT_DT+'</td>';
        tableText += "</tr>";
    });
   
    paging(totalListCount, currentPage, 'getList'); 
    $('#dataTable').html(tableText);

    if($('#extend').is(':checked')){
        $(".hidetd").show();
    }else{
        $(".hidetd").hide();
    }
}