
/* 팀재분류메모 */
setInsuTeamMemo = () => {
    let form = $("#memo_form")[0];
    let formData = new FormData(form);
    formData.append('REQ_MODE', CASE_UPDATE_TEAM);
    $('input[name=sendCheck]:checked').each(function(){
        if($(this).val() != ''){
            const data = listArray[$(this).val()];
            formData.append('NB_ID[]', data.NB_ID);
        }
    });

    callFormAjax(ajaxUrl, 'POST', formData).then((result)=>{
        if(isJson(result)){
            const data = JSON.parse(result);
            if(data.status == OK){
                getList();
                $('#team-memo').modal('hide');
            }else{
                alert(ERROR_MSG_1100);
            }
        }else{
            alert(ERROR_MSG_1200);
        }
    });
}

/** 팩스전송 */
sendFax = (sendFaxArray, isGW) => {
    let form = $("#faxForm")[0];
    let formData = new FormData(form);
    let sendResultCount = sendFaxArray.length;
    let sendResultSuccess = 0;
    let sendResultFail = 0;

    if(sendFaxArray.length > 0){
        sendFaxArray.map((idx)=>{
            const data = listArray[idx];
            if(isGW){
                formData.append('NBI_FAX_NUM', data.MGG_OFFICE_FAX);
            }else {
                formData.append('NBI_FAX_NUM', data.NBI_FAX_NUM);
            }
            formData.append('NB_ID', data.NB_ID);
            formData.append('MGG_ID', data.MGG_ID);
            formData.append('NBI_FAX_RMT_ID', data.NBI_RMT_ID);
            // formData.append('NBI_FAX_NUM', '0221799114');
            formData.append('NBI_CHG_NM', data.NBI_CHG_NM);
            formData.append('FAX_RFS_ID', data.RFS_ID);
            formData.append('CAR_NUM', data.NB_CAR_NUM);
            formData.append('MII_NM', data.MII_NM);
            callFormAjax('../../../sendFAX.php', 'POST', formData).then((result)=>{
                if(result){
                    sendResultSuccess++;
                }else{
                    sendResultFail++;
                }

                if(isGW){
                    completeSendGw(idx);
                }else if(sendResultCount == (sendResultSuccess+sendResultFail)){
                    const msg = "성공 : "+sendResultSuccess+"건 , 실패 : "+sendResultFail+"건 입니다.";
                    confirmSwalOneButton(msg, OK_MSG,()=>{
                        getList();
                    });
                }
            });
        });  
    }
    
}

/** 이첩팝업 띄우기 */
setTransInsu = (transInsuArray) => {
    let form = $('#sendTransForm')[0];
    let htmlTxt = ''

    if(transInsuArray.length > 0){
        transInsuArray.map((idx)=>{
            const data = listArray[idx]
            htmlTxt += "<input type='hidden' name='NB_ID[]' value='"+data.NB_ID+"'>";
            htmlTxt += "<input type='hidden' name='MGG_ID[]' value='"+data.MGG_ID+"'>";
            htmlTxt += "<input type='hidden' name='MII_NM[]' value='"+data.MII_NM+"'>";
            htmlTxt += "<input type='hidden' name='CAR_NUM[]' value='"+data.NB_CAR_NUM+"'>";
            htmlTxt += "<input type='hidden' name='NBI_RMT_ID[]' value='"+data.NBI_RMT_ID+"'>";
            htmlTxt += "<input type='hidden' name='NIC_ID[]' value='"+data.NIC_ID+"'>";
            htmlTxt += "<input type='hidden' name='MII_ID[]' value='"+data.MII_ID+"'>";
            htmlTxt += "<input type='hidden' name='INSU_TEAM_NM[]' value='"+data.INSU_TEAM_NM+"'>";
            htmlTxt += "<input type='hidden' name='NBI_FAX_NUM[]' value='"+data.NBI_FAX_NUM+"'>";
            htmlTxt += "<input type='hidden' name='NB_CLAIM_DT[]' value='"+data.NB_CLAIM_DT+"'>";
            htmlTxt += "<input type='hidden' name='NBI_REGI_NUM[]' value='"+data.NBI_REGI_NUM+"'>";
            htmlTxt += "<input type='hidden' name='MII_NM[]' value='"+data.MII_NM+"'>";
            htmlTxt += "<input type='hidden' name='NBI_CHG_NM[]' value='"+data.NBI_CHG_NM+"'>";
            htmlTxt += "<input type='hidden' name='NB_CAR_NUM[]' value='"+data.NB_CAR_NUM+"'>";
            htmlTxt += "<input type='hidden' name='NB_TOTAL_PRICE[]' value='"+data.NB_TOTAL_PRICE+"'>";
        });
        const popupHeight = transInsuArray.length == 1 ? 700 : 400;
        window.open('', 
            'transwindow', 
            'width=515,height='+popupHeight); 
        form.action = 'sendListTransitPopup.html';
        form.target = 'transwindow';
        form.method = 'POST';
        $('#sendTransForm').html(htmlTxt);
        // console.log('일괄이첨 >>>',htmlTxt);
        form.submit();
    }  
}

/**
 * 청구서 삭제
 */
delBillData = (delDataIdxArray) => {
    let form = $("#delForm")[0];
    let formData = new FormData(form);
    formData.append('REQ_MODE', CASE_DELETE);

    if(delDataIdxArray.length > 0){
        delDataIdxArray.map((idx)=>{
            const data = listArray[idx];
            console.log(data);
            formData.append('NB_ID[]', data.NB_ID);
        });  

        callFormAjax(ajaxUrl, 'POST', formData).then((result)=>{
            if(isJson(result)){
                const data = JSON.parse(result);
                if(data.status == OK){
                    confirmSwalOneButton("삭제되었습니다.", OK_MSG,()=>{
                        getList();
                        $('#delete-modal').modal('hide');
                    });
                }else{
                    alert(ERROR_MSG_1100);
                }
            }else{
                alert(ERROR_MSG_1200);
            }
        });
    }
}

getTotalSummary = () => {
    const data = {
        REQ_MODE: CASE_LIST_SUB,
        DATA : $('#searchForm').serialize()
    };
  
    return callAjax(ajaxUrl, 'POST', $('#searchForm').serialize()+"&REQ_MODE="+CASE_LIST_SUB).then((result)=>{
        if(isJson(result)){
            const data = JSON.parse(result);
            if(data.status == OK){
                setSummaryTable(data.data);
            }else{
                alert(ERROR_MSG_1100);
            }
        }else{
            alert(ERROR_MSG_1200);
        }
    });
}

printBill = (printIdxArray) => {
    if(printIdxArray.length > 0){
        let printHtml = '';
        let count = 0;

        printIdxArray.map((idx)=>{
            const popupUrl = '../../_SLF.html?NB_ID='+listArray[idx].NB_ID+'&MGG_ID='+listArray[idx].MGG_ID;
            const data = {
                REQ_MODE: CASE_LIST_SUB
            };
          
            callAjax(popupUrl, 'GET', data).then((result)=>{
                printHtml += result;  

                if(count == printIdxArray.length-1){
                    const strFeature = "width=1000, height=1200, all=no";
                    let objWin = window.open('', 'print', strFeature);
                    objWin.document.write(printHtml);
                    setTimeout(function() {
                        objWin.focus();
                        objWin.print();
                        objWin.close();
                    }, 250);
                }
                count ++;
            });
            
          
        });
       
    }
    
}

/** 중복해제 */
function setReleaseDuplicateBill(NB_ID){
    const data = {
        REQ_MODE: CASE_CONFLICT_CHECK,
        NB_ID : NB_ID
    };
  
    callAjax(ajaxUrl, 'POST', data).then((result)=>{
        if(isJson(result)){
            const data = JSON.parse(result);
            if(data.status == OK){
                getList();
            }else{
                alert(ERROR_MSG_1100);
            }
        }else{
            alert(ERROR_MSG_1200);
        }
    });
}

getCPPSOptions = (objID, pText, pREQ_MODE, pSelectedVal) => {
    const data = {
        REQ_MODE: pREQ_MODE 
    };
    // console.log('setPartOptions >> ', data);
    const COMMON_AJAX_URL = '/common/searchOptions.php';
    callAjax(COMMON_AJAX_URL, 'GET', data).then((result)=>{
        const res = JSON.parse(result);
        let objOptionList = [];

        $("#" + objID + " option").remove();  // SELECT 개체 초기화
        objOptionList.push($("<option>", { text: pText }).attr("value","").attr("selected","selected")); //최초조건 추가
       
        res.map((item, i)=>{
            objOptionList.push($("<option>", { text: item.CPPS_NM_SHORT }).attr("value",item.CPPS_ID));
        })
        $("#" + objID).append(objOptionList);  // <SELECT> 객체에 반영

        if(pSelectedVal != undefined) $("#" + objID).val(pSelectedVal);
        
    
    });
  }