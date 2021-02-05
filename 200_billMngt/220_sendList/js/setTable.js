let aTagAutobillPopup = null;

getWorkTime = (workTime) => {
    let reckonText;
    if(Math.floor(workTime/60) >= 1){
        reckonText = Math.floor(workTime/60)+"시간 "+workTime%60+"분";
    }else{
        reckonText = workTime+"분";
    }
    return  reckonText;
}

openBillPopup = (NB_ID, MGG_ID, NB_AB_VIEW_URI) => {
    if(NB_AB_VIEW_URI != undefined){
        let popupWidth = window.screen.width *0.65;
        let autoBillPopupWidth = (window.screen.width - popupWidth-10);
        if(window.screen.width > 2100){
          popupWidth = 1500;
          autoBillPopupWidth = 550;
        }
        window.open('/200_billMngt/210_writeBill/writeBill.html?NB_ID='+NB_ID+'&MGG_ID='+MGG_ID,  'newwindow', 'width='+popupWidth+',height=1000');
        if(aTagAutobillPopup != null){
            aTagAutobillPopup = null;
            aTagAutobillPopup.close();
        }
        window.open(NB_AB_VIEW_URI,  'autoBill', 'width='+autoBillPopupWidth+',height=1000, left='+(popupWidth+1));
       
       
    }else{
        window.open('/200_billMngt/210_writeBill/writeBill.html?NB_ID='+NB_ID+'&MGG_ID='+MGG_ID,  'newwindow', 'width=1500,height=1000');
    }
}

/** 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
    const tdLength = $('#sendListHeaderTable th').length;
    const tableText = "<tr><td class='text-center' colspan='"+tdLength+"'>조회된 데이터가 존재하지 않습니다.</tr></tr>";
    $('.pagination').html('');
    $('#dataTable').html(tableText);
    if($('#sendListTable').is(':visible')){
        getTotalSummary();
    }
}

function confirmReleaseDuplicate(NB_ID){
    confirmSwalTwoButton(
        '중복해제 하시겠습니까?','확인', '취소', false ,function(){
            setReleaseDuplicateBill(NB_ID);
        }
    );
}

function openAutoBillPopup(url){
    let popupWidth = window.screen.width *0.65;
    let autoBillPopupWidth = (window.screen.width - popupWidth-10);
    if(window.screen.width > 2100){
        popupWidth = 1500;
        autoBillPopupWidth = 550;
    }
    aTagAutobillPopup = window.open(url,  'autoBill_', 'width='+autoBillPopupWidth+',height=1000, left='+(popupWidth+1));
}

function getRLT_NM(RLT_ID, NBI_RMT_ID, RLT_NM){
    if(RLT_ID == RLT_BOTH_TYPE){
        let RLT_NM_TEXT = NBI_RMT_ID == '11520' ? RLT_NM+"(자차)" : RLT_NM+"(대물)";
        return  '<span class="blue">'+RLT_NM_TEXT+'</span>';
    }else {
        return RLT_NM;
    }
}

function getCSS_CHG_NM(NIC_ID, NBI_CHG_NM){
    if(NIC_ID == ''){
        return  '<span style="color:red">'+NBI_CHG_NM+'</span>';
    }else {
        return NBI_CHG_NM;
    }
}

setTable = (totalListCount, dataArray, currentPage) => {
    let tableText = '';
    listArray = dataArray;

    dataArray.map((data, index)=>{
        let cssDupli = '';
        if(data.NB_DUPLICATED_YN == YN_Y){ // 중복일때 
          cssDupli = "style='background-color: #ffdddd;'";
        }else if(data.RFS_ID == '90210'){ //미전송일때
            cssDupli = "style='background-color: #b1dcb6;'";
        }
       

        tableText += "<tr "+cssDupli+">";
        tableText += "<td class='text-center'><input type='checkbox' id='allcheck' name='sendCheck'  class='listcheck'";
        tableText += "value="+index+"></td>"
        tableText += "<td class='text-center'>"+data.IDX+"</td>";
        tableText += "<td class='text-center hidetd'>"+data.MGG_ID+"</td>";
        tableText += "<td class='text-center'>"+data.NB_CLAIM_DT+"</td>";
        tableText += "<td class='text-center'>"+data.NB_AB_REG_DATE+"</td>";
        tableText += "<td class='text-left'>"+data.MII_NM+"</td>";
        tableText += "<td class='text-left'>"+data.INSU_TEAM_NM+"</td>";
        tableText += "<td class='text-left hidetd'>"+data.NBI_TEAM_MEMO+"</td>";
        if(data.NB_AB_BILL_TYPE != "미구분"){
            tableText += "<td class='text-center'>"+getCSS_CHG_NM(data.NIC_ID, data.NBI_CHG_NM)+"</td>";
            tableText += "<td class='text-center'><a href='javascript:openAutoBillPopup(\""+data.NB_AB_VIEW_URI+"\");'>"+data.NB_AB_BILL_TYPE+"</a></td>";
        }else{
            tableText += "<td class='text-center'>"+data.NBI_CHG_NM+"</td>";
            tableText += "<td class='text-center'>"+data.NB_AB_BILL_TYPE+"</td>";
        }
        tableText += "<td class='text-center hidetd'>"+data.CLAIM_TYPE+"</td>";
        tableText += "<td class='text-center'>"+phoneFormat(data.NBI_FAX_NUM)+"</td>";
        tableText += "<td class='text-center'>"+phoneFormat(data.INSU_CHG_PHONE)+"</td>";
        tableText += "<td class='text-center'>"+data.CSD_NM+"</td>";
        tableText += "<td class='text-left'>"+data.CRR_NM+"</td>";
        tableText += "<td class='text-left'>"+data.MGG_NM+"</td>";
        tableText += "<td class='text-center'>"+bizNoFormat(data.MGG_BIZ_ID)+"</td>";
        tableText += "<td class='text-center'>"+data.RET_NM+"</td>";
        tableText += "<td class='text-center hidetd'>"+data.RPET_NM+"</td>";
        tableText += "<td class='text-center hidetd'>"+data.MGG_FRONTIER_NM+"</td>";
        tableText += "<td class='text-center'>"+data.MGG_BIZ_START_DT+"</td>";
        tableText += "<td class='text-center'>"+data.MGG_BIZ_END_DT+"</td>";
        tableText += "<td class='text-center hidetd'>"+data.RMT_NM.substring(0,1)+"</td>";
        tableText += "<td class='text-center hidetd'>"+(data.NB_CLAIM_USER_NM == '' ? data.WRT_USERID : data.NB_CLAIM_USER_NM )+"</td>";
        tableText += "<td class='text-center'>"+data.BOTH_INSU_NM+"</td>";
        tableText += "<td class='text-center'>"+data.NBI_PRECLAIM_YN+"</td>";
        tableText += "<td class='text-center'>"+data.NBI_REGI_NUM+"</td>";
        tableText += "<td class='text-center'>"+getRLT_NM(data.RLT_ID, data.NBI_RMT_ID, data.RLT_NM)+"</td>";
        tableText += "<td class='text-center'>"+data.NB_CAR_NUM+"</td>";
        tableText += "<td class='text-center'>"+data.CCC_NM+"</td>";
        tableText += "<td class='text-center hidetd'>"+data.CAR_TYPE_NM+"</td>";
        tableText += "<td class='text-left'>"+data.PARTS_NM+"</td>";
        tableText += "<td class='text-right'>"+getWorkTime(data.WORK_TIME)+"</td>";
        tableText += "<td class='text-right'>"+numberWithCommas(data.NB_TOTAL_PRICE)+"</td>";
        tableText += "<td class='text-center'>"+data.NBI_INSU_DEPOSIT_DT+"</td>";
        tableText += "<td class='text-right'>"+numberWithCommas(data.NBI_INSU_DEPOSIT_PRICE)+"</td>";
        tableText += "<td class='text-right'>"+(data.DC_RATIO != null ? data.DC_RATIO+'%' : '-')+"</td>";
        tableText += "<td class='text-center'>"+data.RDET_NM+"</td>";
        tableText += "<td class='text-center'>";
        if(data.NB_DUPLICATED_YN == YN_Y){
            tableText += "<br><span style='color:red'>중&nbsp;복</span></br>";
            tableText += "<a href='javascript:confirmReleaseDuplicate(\""+data.NB_ID+"\");'><span style='font-size:11px'>중복해제</span></a></br>";
        }else{
            tableText += data.RFS_NM+"";
        }
        if(data.NB_MEMO_TXT != ''){  // 메모 여부
            tableText += "&nbsp;"+ "<img src='../../images/iconmemo.png' width='15px' height='15px'/>";
        }
        tableText += "<button type='button' class='btn btn-default' onclick='openTransitModal("+index+");'>이첩</button></td>";
        tableText += "<td class='text-center'> ";
        if($("#AUTH_DELETE").val() == YN_Y){
            tableText += "<button type='button' class='btn btn-danger' onclick='openDeleteModal("+index+");'>삭제</button>";
        }

        tableText += '<button type="button" class="btn btn-default" onclick="reSendFax('+index+');">재전송</button>';
        tableText += "<button type='button' class='btn btn-primary' onclick='sendToGw("+JSON.stringify(data)+","+index+")'>공업사발송</button></td>";
        tableText += "<td class='text-center'><button type='button' class='btn btn-default' onclick='openSelectDowndloadType("+index+");'>다운</button>";
        // tableText += "<td class='text-center'><button type='button' class='btn btn-default' onclick='openSelectDowndloadType("+index+");'>다운</button>";
        tableText += '<button type="button" class="btn btn-default" onclick="billPrint('+index+');">인쇄</button></td>";'
        tableText += "<td class='text-center hidetd'>"+data.NBI_FAX_SEND_DTHMS+"</td>";
        tableText += "<td class='text-center hidetd'>"+data.NBI_GW_FAX_DTHMS+"</td>";
        tableText += "<td class='text-right hidetd'>"+numberWithCommas(data.OWNER_PRICE)+"</td>";
        tableText += "<td class='text-center hidetd'>"+data.NBI_CAROWN_DEPOSIT_DT+"</td>";
        tableText += "<td class='text-right hidetd'>"+numberWithCommas(data.NBI_CAROWN_DEPOSIT_PRICE)+"</td>";
        tableText += "<td class='text-center hidetd'>"+data.NBI_CAROWN_DEPOSIT_YN+"</td>";
        tableText += "<td class='text-center hidetd'>"+data.LAST_DTHMS+"</td>";
        if($("#AUTH_UPDATE").val() == YN_Y){
            if(data.NB_AB_BILL_TYPE != "미구분"){
                tableText += "<td class='text-center'> <button type='button' class='btn btn-default' onclick='openBillPopup(\""+data.NB_ID+"\",\""+data.MGG_ID+"\",\""+data.NB_AB_VIEW_URI+"\");'>수정</button>";
            }else{
                tableText += "<td class='text-center'> <button type='button' class='btn btn-default' onclick='openBillPopup(\""+data.NB_ID+"\",\""+data.MGG_ID+"\");'>수정</button>";
            }
        }
        tableText += "</tr>"
        
    });
    paging(totalListCount, currentPage, 'getList'); 
    $('#dataTable').html(tableText);
    
    if($('#extend').is(':checked')){
        $(".hidetd").show();
    }else{
        $(".hidetd").hide();
    }

    if($('#sendListTable').is(':visible')){
        getTotalSummary();
    }
}

setSummaryTable = (data) => {
    let totalCnt = data.CNT+"건 (쌍:"+data.CNT_DOUBLE+")";
    $('#totalCNT').html(totalCnt);
    $('#totalPrice').html(numberWithCommas(data.PRICE)+"원");
    $('#totalSend').html(data.SEND);
    $('#totalReady').html(data.SEND_READY);
    $('#totalNot').html(data.SEND_NOT);
    $('#dupli').html(data.DUPLI);
}