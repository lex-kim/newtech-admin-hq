const ajaxUrl = 'act/sendList_act.php';
const path = '/200_billMngt/220_sendList/sendList.html';
let CURR_PAGE = 1;
let SORT_TYPE = undefined;
let IS_CLICK_SORT_BTN = false;    // sort버튼을 클릭했는지 아닌지..
let clickDelBtnIdx = "";    // true이면 일괄삭제, false이면 그냥 삭제
let clickDownloadIdx = '';  // 다운로드 클릭한 index
let listArray = [];
let pdfPopup = null;   // 팝업 변수
let doc = null;        // pdf다운 변수

$(document).ready(function() {
    if(path == $(location).attr('pathname')){
        $('#indicator').hide();
        
        const tdLength = $('#sendListHeaderTable th').length;
        setInitTableData(tdLength);
        setObjectEventFunction();   // 이벤트 설정
        setIwOptions('MII_ID_SEARCH'); 
        setIwOptions('BOTH_INSU_SEARCH',undefined,"=쌍방작성 보험사="); 
        setInsuTeam('MII_ID_TEAM_SEARCH');                                                                         //보험사팀
        getRefOptions(GET_REF_CLAIMANT_TYPE, 'NB_CLAIM_USERID_SEARCH', false, false);       // 반응  
        getRefOptions(GET_REF_EXP_TYPE, 'RET_ID_SEARCH', true, false);                             //청구식
        getRefOptions(GET_REF_PAY_EXP_TYPE, 'RPET_ID_SEARCH', false, false);                       //지급식
        getBillDivisionOption('CSD_ID_SEARCH', true);                                              //구분
        setBillCRROption();    
        getGarageInfo('MGG_NM_SEARCH');      
        getRefOptions(GET_REF_CLAIM_TYPE, 'RLT_ID_SEARCH', false, false);                           //청구식  
        getRefOptions(GET_REF_CAR_TYPE, 'RCT_CD_SEARCH', false, false);   
        getCPPSOptions('CPPS_ID_SEARCH','=사용부품=', OPTIONS_CPPS);
        getRefOptions(REF_FAX_STATUS, 'RFS_ID_SEARCH', false, false);  

        /** 기간키: 청구일, 기간: 오늘날짜 디폴트  */
        const date = new Date();
        const TODAY = dateFormat(date);
        const START_MONTH_DATE = date.getFullYear()+"-"+fillZero((date.getMonth()+1)+"",2)+"-"+"01"
        $("#START_DT").val(START_MONTH_DATE);
        $("#END_DT").val(TODAY);
        
    }
    
    /* 보험사 변경에 따른 보험사 팀 동적 필터 */
    $('#MII_ID_SEARCH').change(()=>{
        if($('#MII_ID_SEARCH').selectpicker('val') != null){
            const selectInsuVal = $('#MII_ID_SEARCH').selectpicker('val');
            INSUTEAM_LIST = [];
            selectInsuVal.map((insu)=>{
              INSUTEAM_LIST.push(INSUTEAM_ALL.filter((item, index)=>{
                    return item.MII_ID == insu;
                }));
            });
            selectInsu(INSUTEAM_LIST, 'MII_ID_TEAM_SEARCH');
        }else {
          setInsuTeam('MII_ID_TEAM_SEARCH');
        }
      
    });

    //확장하기
    $(".hidetd").hide();
    $("#extend").click(function(){
        if($("#extend").prop("checked")){
            $(".hidetd").show();
        }else{
            $(".hidetd").hide();
        }
    });

                 
});

/**
 * 일괄이첩
 */
openTransitModal = (idx) => { 
    let transInsuArray = [];
    if(idx == undefined){
        if($('input[name=sendCheck]:checked').length > 0){
                $('input[name=sendCheck]:checked').each(function(){
                    if($(this).val() != ''){
                        transInsuArray.push($(this).val());
                    }
                });
                setTransInsu(transInsuArray);
        }else{
            alert("선택된 항목이 없습니다.");
        }
    }else{
        transInsuArray.push(idx);
        setTransInsu(transInsuArray);
    } 
}

/**
 * 팩스재전송(일괄재전송)
 */
reSendFax = (idx) => {
    let sendFaxArrayIdx = [];
    if(idx == undefined){
        if($('input[name=sendCheck]:checked').length > 0){
            const msg = "해당 "+$('input[name=sendCheck][name=sendCheck]:checked').length+"건을 재전송 하시겠습니까?";
            confirmSwalTwoButton(msg,'확인', '취소', false , ()=>{       
                $('input[name=sendCheck]:checked').each(function(){
                    if($(this).val() != ''){
                        sendFaxArrayIdx.push($(this).val());
                    }
                });                         
                sendFax(sendFaxArrayIdx);
            });
        }else{
            alert("선택된 항목이 없습니다.");
        }
    }else{
        confirmSwalTwoButton("재전송 하시겠습니까?",'확인', '취소', false , ()=>{       
            sendFaxArrayIdx.push(idx);
            sendFax(sendFaxArrayIdx);
        });
       
    }
}

/**
 * 공업사 팩스발송
 */
sendToGw = (data,idx) => {
    let sendFaxArrayIdx = [];
        confirmSwalTwoButton(data.MGG_NM+"로 발송 하시겠습니까?",'확인', '취소', false , ()=>{       
            data.NBI_FAX_NUM = data.MGG_OFFICE_FAX;
            sendFaxArrayIdx.push(idx);
            sendFax(sendFaxArrayIdx,true);
        });
}

completeSendGw = (idx) => {
    const data = listArray[idx];

    const param ={
        REQ_MODE: CASE_UPDATE_REGION,
        NB_ID:  data.NB_ID,
      }
      callAjax(ajaxUrl, 'POST', param).then((result)=>{
        if(isJson(result)){
            const data = JSON.parse(result);
            if(data.status == OK){
                confirmSwalOneButton("발송 되었습니다.", OK_MSG,()=>{
                    getList();
                });
            }else{
                alert(ERROR_MSG_1100);
            }
        }else{
            alert(ERROR_MSG_1200);
        }
      });
}

openDeleteModal = (idx) => {
    if(idx == undefined){
        if($('input[name=sendCheck]:checked').length > 0){
            clickDelBtnIdx = idx;
            $('#delete-modal').modal('show');
        }else{
            alert("선택된 항목이 없습니다.");
        }
    }else{
        clickDelBtnIdx = idx;
        $('#delete-modal').modal('show'); 
    }
}

/**
 * 인쇄/일괄인쇄
 */
billPrint = (idx) => {
    let printSendArray = [];
    if(idx == undefined){
        if($('input[name=sendCheck]:checked').length > 0){
            const msg = "해당 "+$('input[name=sendCheck][name=sendCheck]:checked').length+"건을 출력 하시겠습니까?";
            confirmSwalTwoButton(msg,'확인', '취소', false , ()=>{       
                $('input[name=sendCheck]:checked').each(function(){
                    if($(this).val() != ''){
                        printSendArray.push($(this).val());
                    }
                });                         
                printBill(printSendArray);
            });
        }else{
            alert("선택된 항목이 없습니다.");
        }
    }else{
        confirmSwalTwoButton("출력 하시겠습니까?",'확인', '취소', false , ()=>{       
            printSendArray.push(idx);
            printBill(printSendArray);
        });
       
    }
   
}


/**
 * 삭제
 */
deleteData = () => {
    let delBillIdxArray = [];
    if(clickDelBtnIdx == undefined){
        const msg = "해당 "+$('input[name=sendCheck][name=sendCheck]:checked').length+"건을 일괄삭제 하시겠습니까?";
        confirmSwalTwoButton(msg, '삭제', '취소', true , ()=>{       
            $('input[name=sendCheck]:checked').each(function(){
                if($(this).val() != ''){
                    delBillIdxArray.push($(this).val());
                }
            });                         
            delBillData(delBillIdxArray);
        });
    }else{
        confirmSwalTwoButton("삭제 하시겠습니까?",'확인', '취소', true , ()=>{       
            delBillIdxArray.push(clickDelBtnIdx);
            delBillData(delBillIdxArray);
        });
    }
}


/**
 * 팀재분류메모
 */
openInsuTeamMemoModal = () => {   
    if($('input[name=sendCheck]:checked').length > 0){
        $('#team-memo').modal('show');
    }else{
        alert("선택된 항목이 없습니다.");
    }
}

excelDownload = () => {
    location.href="sendListExcel.html?"+$("#searchForm").serialize()+"&REQ_MODE="+CASE_TOTAL_LIST;
}

/** 
 * 보험사팀 정렬 
 * @param {IS_ASC : 오름/내림차순인지..}
*/
sortInsuTeam = (IS_ASC) => {
    if(listArray.length > 0){
        let sortArray = [];
        if(IS_ASC){
            sortArray = listArray.sort((a,b)=>{
                return a.INSU_TEAM_NM < b.INSU_TEAM_NM ? -1 : a.INSU_TEAM_NM  > b.INSU_TEAM_NM ? 1 : 0;
            });
        }else{
            sortArray = listArray.sort((a,b)=>{
                return a.INSU_TEAM_NM > b.INSU_TEAM_NM ? -1 : a.INSU_TEAM_NM  < b.INSU_TEAM_NM ? 1 : 0;
            });
        }
        setTable(sortArray.length, sortArray, CURR_PAGE);
    }
}

/**
 * 다운클릭했을 때 선택모달 뜨이ㅓ주기
 */
openSelectDowndloadType = (idx) => {
    clickDownloadIdx = idx;
    $('#bill-imagedown').modal();
}

download = () => {  
    const type = $('input[name=downloadType]:checked').val();
    const url = "../../../PDF_VIEW.html?NB_ID="+listArray[clickDownloadIdx].NB_ID+"&MGG_ID="+listArray[clickDownloadIdx].MGG_ID+"&TYPE="+type;
    $('#bill-imagedown').modal('hide');
    pdfPopup = window.open(url, 
    'loginNoticePopup', 
    'width=1300,height=900');
}


function getSortList(sort){
    IS_CLICK_SORT_BTN = true;
    SORT_TYPE = (sort == undefined) ? SORT_TYPE : sort;
    getList(CURR_PAGE);
}

getList = (currentPage) => {
    $('#allcheck').attr('checked',false);
    CURR_PAGE = (currentPage == undefined) ? CURR_PAGE : currentPage;
    let form = $("#searchForm")[0];
    let formData = new FormData(form);
    formData.append('REQ_MODE', CASE_LIST);
    formData.append('PER_PAGE', $("#perPage").val());
    formData.append('CURR_PAGE', CURR_PAGE);

    if(IS_CLICK_SORT_BTN){
        formData.append('SORT_TYPE', SORT_TYPE);
    }

    callFormAjax(ajaxUrl, 'POST', formData).then((result)=>{
        console.log('getList SQL >>>',result);
        if(isJson(result)){
            const data = JSON.parse(result);
            if(data.status == OK){
                const item = data.data;
                setTable(item[1], item[0], CURR_PAGE);
            }else{
                setHaveNoDataTable();
            }
        }else{
            alert(ERROR_MSG_1200);
        }
       
    });  
    
}