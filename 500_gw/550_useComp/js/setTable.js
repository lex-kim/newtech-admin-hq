const TYPE_A_STR = '10210';
const TYPE_B_STR = '10220';
const TYPE_ETC_STR = '10230';
let partsArray = []; 

/** 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
    const tdLength = $('table tr th').length;
    const tableText = "<tr><td class='text-center' colspan='"+tdLength+"'>조회된 데이터가 존재하지 않습니다.</tr></tr>";
    $('.pagination').html('');
    $('#dataTable').html(tableText);
}

/**
 * 동적으로 사용부품/공급부품 데이터들을 테이블에 세팅
 */
setTableHeader = (data) => {
    let beforeId = '';
    let stdPartsHtml = '';     // 기준부품 테이블 td (A,B,기타) -> 공급/사용부품 동일
    let usePartsHtml = '';     // 사용부품 취급부품(카,테,등등)
    let usePartsHtml_ = '';    // 공급부품 취급부품(카, 테 , 등등)
    partsArray = data;

    /** 사용부품 */
    partsArray.map((item)=>{
        if(beforeId != (item.CCD_PREFIX_CD + item.CCD_VAL_CD)){
            stdPartsHtml += '<th class="text-center" colspan='+setCol((item.CCD_PREFIX_CD + item.CCD_VAL_CD))+';>'+item.CCD_NM+'</th>';
        }
        usePartsHtml += '<th class="text-center" name=USE_'+item.CPP_ORDER_NUM+' style="width:100px">'+item.CPP_NM_SHORT+'</th>';
        usePartsHtml_ += '<th class="text-center" name=SUP_'+item.CPP_ORDER_NUM+' style="width:100px">'+item.CPP_NM_SHORT+'</th>';
        beforeId = (item.CCD_PREFIX_CD + item.CCD_VAL_CD);
    });

    /** 사용부품/공급부품 td colspan 동적으로 세팅 */
    $('.partsHeader').attr('colspan',data.length);

     
    const totalUsePartsHtmlFooter = '<th class="text-center">보험사</th><th class="text-center">제조사</th>';   // 테이블 헤더 취급부품등록하고 마지마에 협력업체
    $('#partsHeaderSTD').html(stdPartsHtml+stdPartsHtml);
    $('#partsHeaderUSE').html(usePartsHtml+usePartsHtml_+totalUsePartsHtmlFooter);

    const tdLength = $('table tr th').length;
    setInitTableData(tdLength);
}

/**
 * 각 기준부품마다 colspan을 정해주기 위해서
 */
setCol = (id) => {
    if(id == TYPE_A_STR){
        const TYPE_A = partsArray.filter((item)=>{
            return (item.CCD_PREFIX_CD+item.CCD_VAL_CD) == TYPE_A_STR;
        });
        return TYPE_A.length;
    }else if(id == TYPE_B_STR){
        const TYPE_B = partsArray.filter((item)=>{
            return (item.CCD_PREFIX_CD+item.CCD_VAL_CD)  ==TYPE_B_STR;
        });
        return TYPE_B.length;
    }else{
        const TYPE_ETC = partsArray.filter((item)=>{
            return (item.CCD_PREFIX_CD+item.CCD_VAL_CD)  == TYPE_ETC_STR;
        });
        return TYPE_ETC.length;
    }
}

/** 리스트 테이블 동적으로 생성 
 * @param {dataArray : 리스트 데이터, currentPage : 현재페이지(페이징을 위해)} 
*/
setTable = (totalListCount, dataArray, currentPage) => {
    let tableText = '';
    
    dataArray.map((data, index)=>{
        tableText += "<tr>";
        tableText += "<td class='text-center'>"+data.IDX+"</td>";
        tableText += "<td class='text-center'>"+data.CSD_NM+"</td>";
        tableText += "<td class='text-left'>"+data.CRR_NM+"</td>";
        tableText += "<td class='text-left'>"+data.MGG_NM+"</td>";
        tableText += "<td class='text-center'>"+bizNoFormat(data.MGG_BIZ_ID)+"</td>";
        tableText += "<td class='text-center'>"+data.MGG_BIZ_START_DT+"</td>"
        tableText += "<td class='text-center'>"+data.MGG_BIZ_END_DT+"</td>";
        tableText += "<td class='text-right'>"+numberWithCommas(data.CLAIM_CNT)+"</td>";
        tableText += "<td class='text-right'>"+numberWithCommas(nullChk(data.CLAIM_PRICE,'0'))+"</td>";
        tableText += "<td class='text-right'>"+numberWithCommas(nullChk(data.STOCK_PRICE,'0'))+"</td>";
        data.USE_PARTS_ARRAY.map((item)=>{
            tableText += "<td class='text-left'>"+item+"</td>";
        });
        data.SUPP_PARTS_ARRAY.map((item)=>{
            tableText += "<td class='text-left'>"+item+"</td>";
        });
        // tableText += setUserPartsTD(data, index);
        tableText += "<td class='text-left'>"+data.MGG_INSU_MEMO+"</td>";
        tableText += "<td class='text-left'>"+data.MGG_MANUFACTURE_MEMO+"</td>";
        tableText += "</tr>";
        
    });
    paging(totalListCount, currentPage, 'getList'); 
    $('#dataTable').html(tableText);
}

/**
 * 사용부품/공급부품 동적으로 알맞게 세팅(출력순서 기준으로 설정)
 * @param {data : 해당 tr 데디터}
 * @param {idx : 몇번재 col인지.}
 */
// setUserPartsTD = (data, idx) => {
//     let tdHtml = '';

//     $('#partsHeaderUSE th').each(function(){
//         const name = $(this).attr('name');  
//         if(name != undefined){
//             tdHtml += "<td class='text-center' name="+idx+">";

//             (data.CPP_ORDER_NUM.split(",")).map((order, index)=>{
//                const tdOrder = name.split("_");
//                 if(order == tdOrder[1]){
//                     if(tdOrder[0] == 'USE'){
//                         if(data.NGP_DEFAULT_YN.split(",")[index] == YN_Y){
//                             tdHtml += data.CPPS_NM.split(",")[index]+" ["+(data.NGP_RATIO.split(","))[index]+"]<br>";
//                         }
//                     }else{
//                         tdHtml += data.CPPS_NM.split(",")[index]+"<br>";
//                     } 
//                 }else{
//                     tdHtml += "";
//                 }
//             });
//             tdHtml += "</td>";        
//         }
//     });
//     return tdHtml;
// }