const registPopupWidth = 490;            // 보상담당등록창 width 값
const GWCPST_SELECT_PHONE = '110';  // 검색에서 보험사정보 select에서 전화번호 관련 옵션을 선택했을 때
const GWCPST_SELECT_TEXT = '100';   // 검색에서 보험사정보 select에서 텍스트 관련 옵션을 선택했을 때
const ajaxUrl = 'act/gwCpst_act.php';
const PATH_NAME = '/500_gw/532_gwCpst/gwCpst.html'; 

let CURR_PAGE = 1;             // 현재페이지
let popupX = 0;
let popupY = 0;

$(document).ready(function() {
    popupX = (screen.availWidth - registPopupWidth) / 2;
    if( window.screenLeft < 0){
        popupX += window.screen.width*-1;
    }
    else if ( window.screenLeft > window.screen.width ){
        popupX += window.screen.width;
    }
    popupY= (window.screen.height / 2) - (680 / 2);
})

/**
 * 부모창 로드될때
 */
onParentLoad = () => {
    $('#indicator').hide();

    $( "#START_DT" ).datepicker(
        getDatePickerSetting('END_DT','minDate')
    );
    $( "#END_DT" ).datepicker(
        getDatePickerSetting('START_DT','maxDate')       
    );

    getDivisionOption('CSD_ID_SEARCH', true);                                      // 구분
    setCRRSelectOption('CRR_ID_SEARCH', true);                                     // 지역
    getInsuInfo('MII_ID_SEARCH', false, '=보험사=');                             // 보험사 정보
    getRefOptions(GET_REF_INSU_CHAGE_TYPE, 'RICT_ID_SEARCH', false, false);        // 반응  
    // getRefOptions(GET_REF_CLAIMANT_TYPE, 'RMT_ID_SEARCH', false, false);           // 등록주체

    const tdLength = $('table tr th').length;
    setInitTableData(tdLength);

    /** 구분 셀렉트 체인지 */
    $('#CSD_ID_SEARCH').change(()=>{
        if($('#CSD_ID_SEARCH').selectpicker('val') != null){
            setCRRSelectOption('CRR_ID_SEARCH', true);
        }else{
            $('#CSD_ID').selectpicker('val' , '');
            $('#CSD_ID').selectpicker('refresh');
            setCRRSelectOption('CRR_ID_SEARCH', true);

        }
    });

    /** 검색버튼 */
    $('#searchForm').submit(function(e){
        e.preventDefault();

        if(isSearchValidate()){
          getList(1);
        }
        
      
    });

    /** 검색조건 셀렉트 체인지 */
    $('#SELECT_DT').change(function(){
        if($(this).val() == '' ){
            $('#START_DT').val('');
            $('#END_DT').val('');
        }
    });


    /** 보험사 정보 바뀔 때 마다 번호양식인지 텍스트양식이니즐 구별해줘서 알맞은 이벤트걸어주기 */
    $('#NIC_SEARCH').change(function(){
        $('#NIC_SEARCH_TEXT').val('');
        if($(this).val() != ''){
          const selectOptionClass = $('#NIC_SEARCH option:selected').attr('class');
          if(selectOptionClass == GWCPST_SELECT_PHONE){
            $('#NIC_SEARCH_TEXT').attr("placeholder","연락처를 -없이 입력해주세요.");
            $('#NIC_SEARCH_TEXT').attr("onkeyup","isNumber(event)");
            $('#NIC_SEARCH_TEXT').attr("maxlength","11");
          }else{
            $('#NIC_SEARCH_TEXT').attr("placeholder","성명을 입력해주세요.");
            $('#NIC_SEARCH_TEXT').attr("onkeyup","");
            $('#NIC_SEARCH_TEXT').attr("maxlength","");
          }
        }else{
          $('#NIC_SEARCH_TEXT').val('');
        }
        
    });

    $("#allcheck").click(function(){
        if($("#allcheck").prop("checked")){
            $("input[type=checkbox]").prop("checked", true);
        }else{
            $("input[type=checkbox]").prop("checked", false);
        }
    });

}


 /**
 * 등록창에서 보험사정보 가져오기
 * @param {objID : 타겟 아이디}
 * @param {isMultiSelect : 멀티셀렉틍니지 아닌지}
 * @param {defaultText : 기본옵션 텍스트}
 */
getInsuInfo = (objID, isMultiSelect, defaultText) => {
    const data = {
        REQ_MODE: OPTIONS_IW,
    };
    callAjax('/common/searchOptions.php', 'GET', data).then((result)=>{
        if(isJson(result)){
            const res = JSON.parse(result);
            if(res != undefined || res.length > 0){
                insuArray = res;
                setSelectOption(res, objID, isMultiSelect, defaultText== undefined ? '선택' : defaultText);
            }
        }else{
            alert(ERROR_MSG_1200);
        }
    });
}


isSearchValidate = () => {
    if(($('#START_DT').val() != '' || $('#END_DT').val() != '') && $('#SELECT_DT').val() == ''){
        alert("검색조건을 선택해주세요.");
        $('#SELECT_DT').focus();

        return false;
    }else if($('#NIC_SEARCH_TEXT').val().trim() != '' && $('#NIC_SEARCH').val() == ''){
        alert("보상담당자 검색조건을 선택해주세요.");
        $('#NIC_SEARCH').focus();

        return false;
    }else{
        return true;
    }
}

/**
 * 선택된 공업사들의 아이디와 보험사담당 아이디를 배열에 저장해서 리턴
 */
isCheck = () => {
    let releaseArray = [];
    $('input[type=checkbox]').each(function(){
        /** 전체선택 부분 제외 */
        if($(this).attr('name') != undefined){
            if($(this).is(':checked')){
                const data = JSON.parse($(this).val());
                releaseArray.push(JSON.stringify({
                    MGG_ID : data.MGG_ID,
                    NIC_ID : data.NIC_ID,
                    RICT_ID : data.RICT_ID
                }));
            }    
        }
    });
  
    return releaseArray;
}

/**
 * 담당 확인 버튼을 눌렀을 때
 */
onClickConfirmBtn = () => {
    let releaseArray = isCheck();
    if(releaseArray.length > 0){
        confirmSwalTwoButton('보상담당자를 등록 하시겠습니까?','등록', CANCEL_MSG, false, ()=>onConfirm(releaseArray), null);
    }else{
        alert("공업사를 선택해주세요.")
    }   
}

/** table에서 미확인 버튼을 눌렀을 때
 * @param {mggId : 클릭한 버튼 공업사 아이디}
 * @param {nicId : 클릭한 버튼 보상담당자 아이디}
*/
onClickRealseBtn = (mggID, nicId, rictID) => {
    /** array에 넣어준 이유는 공통으로 사용하기 위해서.. */
    let releaseArray = [];
    releaseArray.push(JSON.stringify({
        MGG_ID : mggID,
        NIC_ID : nicId,
        RICT_ID : rictID
    }));

    confirmSwalTwoButton('보상담당자를 등록 하시겠습니까?','등록', CANCEL_MSG, false, ()=>onConfirm(releaseArray), null);
}

/**
 * 담당해제
 */
confirmDelData = () => {
    let releaseArray = isCheck();
    if(releaseArray.length > 0){
        confirmSwalTwoButton('보상담당자를 해제 하시겠습니까?','해제', CANCEL_MSG, true, ()=>delData(releaseArray), null);
    }else{
        alert("공업사를 선택해주세요.")
    }  
}

/** 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
    const tdLength = $('table tr th').length;
    const tableText = "<tr><td class='text-center' colspan='"+tdLength+"'>조회된 데이터가 존재하지 않습니다.</tr></tr>";
    $('.pagination').html('');
    $('#dataTable').html(tableText);
}

/*
* @param {dataArray : 리스트 데이터, currentPage : 현재페이지(페이징을 위해)} 
*/
setTable = (totalListCount, dataArray, currentPage) => {
    let tableText = '';
    
    dataArray.map((data, index)=>{
        tableText += "<tr>";
        tableText += "<td class='text-center'><input type='checkbox' name='"+JSON.stringify({MGG_ID : data.MGG_ID, NIC_ID:data.NIC_ID, RICT_ID : data.RICT_ID})+"' value='"+JSON.stringify({MGG_ID : data.MGG_ID, NIC_ID:data.NIC_ID,RICT_ID : data.RICT_ID})+"'></td>";
        tableText += "<td class='text-center ' name="+index+">"+data.IDX+"</td>";
        tableText += "<td class='text-center ' name="+index+">"+data.CSD_NM+"</td>";
        tableText += "<td class='text-left ' name="+index+">"+data.CRR_NM+"</td>";
        tableText += "<td class='text-left ' name="+index+">"+data.MGG_NM+"</td>";
        tableText += "<td class='text-center ' name="+index+">"+bizNoFormat(data.MGG_BIZ_ID)+"</td>";
        tableText += "<td class='text-center ' name="+index+">"+data.NOT_CNT+"</td>";
        tableText += "<td class='text-center ' name="+index+">"+data.MGG_CLAIM_NM_AES+"</td>";
        tableText += "<td class='text-center ' name="+index+">"+phoneFormat(data.MGG_OFFICE_TEL)+"</td>"
        tableText += "<td class='text-center ' name="+index+">"+phoneFormat(data.MGG_CLAIM_TEL_AES)+"</td>";
        tableText += "<td class='text-center ' name="+index+">"+data.MII_NM+"</td>";
        tableText += "<td class='text-center ' name="+index+">"+data.WRT_DTHMS+"</td>";
        tableText += "<td class='text-center ' name="+index+">"+data.LAST_DTHMS+"</td>";
        tableText += "<td class='text-center ' name="+index+">"+data.NIC_NM_AES+"</td>";
        tableText += "<td class='text-center ' name="+index+">"+data.RICT_NM+"</td>";
        tableText += "<td class='text-center ' name="+index+">"+phoneFormat(data.NIC_FAX_AES)+"</td>";
        tableText += "<td class='text-center ' name="+index+">"+phoneFormat(data.NIC_TEL1_AES)+"</td>";
        tableText += "<td class='text-center ' name="+index+">"+data.RMT_NM+"</td>";

        if(data.NGIC_CONFIRM_YN == YN_Y){
            tableText += "<td class='text-center ' name="+index+">확인</td>";
        }else{
            tableText += "<td class='text-center'><button type='button' onclick='onClickRealseBtn(\""+data.MGG_ID +"\",\"" +data.NIC_ID+"\",\"" +data.RICT_ID+"\");' class='btn btn-default'>미확인</button></td>";
        }
        if($("#AUTH_UPDATE").val() == YN_Y){
            tableText += "<td class='text-center'><button type='button' onclick='showUpdatePopup("+JSON.stringify(dataArray[index])+")' class='btn btn-default'>수정</button></td>";
        }
        tableText += "</tr>";
        
    });
    
    paging(totalListCount, currentPage, 'getList'); 
    $('#dataTable').html(tableText);

    
    
}

/**
 * 수정팝업
 * @param {dataArray : 클릭한 해당 공업사 정보 }
 */
showUpdatePopup = (dataArray) => {
    const selectData = dataArray;
    window.open('', 
        'rewardwindow', 
        'width='+registPopupWidth+',height=665, left='+ popupX + ', top='+ popupY); 

    const form = $('#updateGarageInsuForm')[0];
    form.action = '/500_gw/532_gwCpst/gwCpstRewardModal.html';
    form.target = 'rewardwindow';
    form.method = 'POST';

    let dataHtml =  '<input type="hidden" id="MGG_ID" name="MGG_ID" value="'+selectData.MGG_ID+'">';
    dataHtml +=  '<input type="hidden" id="MGG_NM" name="MGG_NM" value="'+selectData.MGG_NM+'">';
    dataHtml +=  '<input type="hidden" id="MGG_OFFICE_TEL" name="MGG_OFFICE_TEL" value='+phoneFormat(selectData.MGG_OFFICE_TEL)+'>';
    dataHtml +=  '<input type="hidden" id="MGG_CLAIM_NM_AES" name="MGG_CLAIM_NM_AES" value='+selectData.MGG_CLAIM_NM_AES+'>';
    dataHtml +=  '<input type="hidden" id="MGG_CLAIM_TEL_AES" name="MGG_CLAIM_TEL_AES" value='+phoneFormat(selectData.MGG_CLAIM_TEL_AES)+'>';
    dataHtml +=  '<input type="hidden" id="MII_ID" name="MII_ID" value="'+selectData.MII_ID+'">';
    dataHtml +=  '<input type="hidden" id="RICT_ID" name="RICT_ID" value="'+selectData.RICT_ID+'">';
    dataHtml +=  '<input type="hidden" id="NIC_NM_AES" name="NIC_NM_AES" value="'+selectData.NIC_NM_AES+'">';
    dataHtml +=  '<input type="hidden" id="NIC_FAX_AES" name="NIC_FAX_AES" value="'+phoneFormat(selectData.NIC_FAX_AES)+'">';
    dataHtml +=  '<input type="hidden" id="NIC_TEL1_AES" name="NIC_TEL1_AES" value="'+phoneFormat(selectData.NIC_TEL1_AES)+'">';
    dataHtml +=  '<input type="hidden" id="NIC_ID" name="NIC_ID" value="'+selectData.NIC_ID+'">';
    $('#updateGarageInsuForm').html(dataHtml);

    form.submit();
}

/**
 * 담당지역 확인(서버와의 통신)
 * @param {mggIdArray : 선택한 공업사 ID값들}
 * @param {type : 수정모드인지 담당확인 모드 인지 왜냐면 REQ_MODE가 수정과 동일}
 */
onConfirm = (releaseArray) => {
    const data = {
        REQ_MODE: CASE_UPDATE,
        ID_ARRAY : releaseArray,
        TYPE : true       
    };
    callAjax(ajaxUrl, 'POST', data).then((result)=>{
        if(isJson(result)){
            const data = JSON.parse(result);
            if(data.status == OK){
                confirmSwalOneButton('담당등록에 성공하였습니다.','확인',()=>{
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

excelDownload = () => {
    location.href="gwCpstExcel.html?"+$("#searchForm").serialize()+"&REQ_MODE="+CASE_TOTAL_LIST;
}

/**
 * 담당해제
 */
delData = (releaseArray) => {
    const data = {
        REQ_MODE: CASE_DELETE,
        ID_ARRAY : releaseArray,
    };
    callAjax(ajaxUrl, 'POST', data).then((result)=>{
        if(isJson(result)){
            const data = JSON.parse(result);
            if(data.status == OK){
                confirmSwalOneButton('담당해제에 성공하였습니다.','확인',()=>{
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

/** 리스트 */
getList = (currentPage) => {
    CURR_PAGE = (currentPage==undefined) ? CURR_PAGE : currentPage;

    let form = $("#searchForm")[0];
    let formData = new FormData(form);
    formData.append('REQ_MODE', CASE_LIST);
    formData.append('PER_PAGE', $("#perPage").val());
    formData.append('CURR_PAGE', CURR_PAGE);
  
    callFormAjax(ajaxUrl, 'POST', formData).then((result)=>{
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

/**
 * 보상담당드록 팝업
 */
openRewardPopup = () => {
    window.open('/500_gw/532_gwCpst/gwCpstRewardModal.html', 
                  'rewardwindow', 
                  'width='+registPopupWidth+',height=665,  left='+ popupX + ', top='+ popupY); 
}

/**
 * 보상담당자 신규등록 팝업
 */
openNewPopup = () => {
    const popupUrl = $('#MII_ID').val() != '' ? '/600_iw/630_iwFaoMngt/iwFaoMngtModal.html?MII_ID='+$('#MII_ID').val() : '/600_iw/630_iwFaoMngt/iwFaoMngtModal.html';
    window.open(popupUrl, 
                     'iwFaoMngtModalwindow', 
                     'width=500,height=580, left='+ (registPopupWidth+popupX) + ', top='+ popupY); 
 }
 

 
