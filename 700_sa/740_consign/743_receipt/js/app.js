let CURR_PAGE = 1;
const RECEIPT_URL = 'act/receipt_act.php';
let tdLength = 0 ;
let listArray = [];
let IS_SORT = undefined;        // 오름차순.내림차순 정렬여부
let ORDER_KEY = undefined;   // 월별청구건수정렬시 몇월달 인지

const registPopupWidth = 500; 
const registPopupHeight = 665; 
let popupX = 0;
let popupY = 0;

const SUPPLY = '70120';
const RECOVERY = '70130';
const LOSS = '70140';
const DISCARD = '70150';

$(document).ready(() =>{
    tdLength = $('table th').length;
    setInitTableData(tdLength);

    /** 검색 */
    $("#searchForm").submit(function(e){
        e.preventDefault();
            getList(1);
    });

    /* datepicker 설정 */
    $("#START_DT_SEARCH").datepicker(
        getDatePickerSetting('END_DT_SEARCH', 'minDate')
    );
    $("#END_DT_SEARCH").datepicker(
        getDatePickerSetting('START_DT_SEARCH', 'maxDate')
    );

    if($('#REQ_MODE').val() == undefined){
        getDivisionOption('CSD_ID_SEARCH', true);                                           //지역구분
        setCRR_ID_Option('CSD_ID_SEARCH', 'CRR_ID_SEARCH', true);
        setGwOptions('MGG_NM_SEARCH',undefined,'=공업사=');
        getRefOptions(GET_REF_CONTRACT_TYPE, 'RCT_ID_SEARCH', false, false);                //거래여부
        setConsignType('NCC_ID_SEARCH','=장비구분=');
        setConsign('NCP_NM_SEARCH','=장비=',undefined, true);
        getRefOptions(REF_CONSIGN_STATUS, 'RCS_ID_SEARCH', false, false);                   //장비상태
        getRefOptions(REF_CONSIGN_SPOT, 'RCP_ID_SEARCH', false, false);                     //장비위치
    }

    /* 위탁장비 입출고 수정 */
    $('#addForm').submit(function(e){
        e.preventDefault();
          confirmSwalTwoButton('저장 하시겠습니까?','확인', '취소', false ,()=>setData());
    });


    /** 검색에서 기간을 눌렀을 때 날짜 초기화 */
    $('#DT_KEY_SEARCH').change(()=>{
        if($('#DT_KEY_SEARCH').val() == ''){
            $('#START_DT_SEARCH').val('');
            $('#END_DT_SEARCH').val('');
        }
    });

    /* 구분에 따른 지역 변경 */
    $('#CSD_ID').change(()=>{
        if($('#CSD_ID').val() != ''){
            setCRR_ID('CSD_ID', 'CRR_ID', 'SELECT_CRR_ID', true);
        }else{
            $('#CRR_ID').val('');
            $('#SELECT_CRR_ID').val('');
            $('#CRR_ID').attr('disabled',true);
        }
    });

    $('#CSD_ID_SEARCH').change(()=>{
        if($('#CSD_ID_SEARCH').selectpicker('val') != null){
            setCRR_ID_Option('CSD_ID_SEARCH', 'CRR_ID_SEARCH', true, true);
        }else{
            $('#CSD_ID').selectpicker('val' , '');
            $('#CSD_ID').selectpicker('refresh');
            setCRR_ID_Option('CSD_ID_SEARCH', 'CRR_ID_SEARCH', true);
        }
    });

    // 전체선택
    $("#allcheck").click(function(){
        if($("#allcheck").prop("checked")){
            $("input[type=checkbox].listcheck").prop("checked", true);
        }else{
            $("input[type=checkbox].listcheck").prop("checked", false);
        }
    });


    // 팝업높이
    popupX = (screen.availWidth - registPopupWidth) / 2;
    if( window.screenLeft < 0){
        popupX += window.screen.width*-1;
    }
    else if ( window.screenLeft > window.screen.width ){
        popupX += window.screen.width;
    }
    popupY= (window.screen.height / 2) - (registPopupHeight / 2);

});


getList = (currentPage, IS_ASC, ORDER_KEY) => {
    CURR_PAGE = (currentPage == undefined) ? CURR_PAGE : currentPage;
    IS_SORT = (IS_ASC == undefined) ? '' : IS_ASC; 
  
    let form = $("#searchForm")[0];
    let formData = new FormData(form);
    formData.append('REQ_MODE', CASE_LIST);
    formData.append('PER_PAGE', $("#perPage").val());
    formData.append('CURR_PAGE', CURR_PAGE);
    formData.append('IS_SORT', IS_SORT);
    formData.append('ORDER_KEY', ORDER_KEY == undefined ? '' : ORDER_KEY);
  
    callFormAjax(RECEIPT_URL, 'POST', formData).then((result)=>{
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
  
  
  /** 빈값일 때 동적테이블처리 */
  setHaveNoDataTable = () => {
    const tableText = "<tr><td class='text-center' colspan='"+tdLength+"'>조회된 데이터가 존재하지 않습니다.</tr></tr>";
    $('.pagination').html('');
    $('#dataTable').html(tableText);
  }
  
  setTable = (totalListCount, dataArray, currentPage) => {
    let tableText = '';
    listArray = dataArray;
    dataArray.map((data, index)=>{
        tableText += "<tr ";
        if(data.RCS_ID == SUPPLY){
            tableText += "style='background-color: #e9e9e9;'";
        }else if(data.RCS_ID == RECOVERY){
            tableText += "style='background-color: #e2ecf6;'";
        }else if(data.RCS_ID == LOSS){
            tableText += "style='background-color: #f8edc2;'";
        }else if(data.RCS_ID == DISCARD){
            tableText += "style='background-color: #fae3e8;'";
        }
        tableText += ">";
        
       
        tableText += "<td class='text-center'><input type='checkbox' class='listcheck' name='multi' value='"+index+"'></td>";

        tableText += "<td class='text-center'>"+data.IDX+"</td>";
        tableText += "<td class='text-center'>"+nullChk(data.CSD_NM,'-')+"</td>";
        tableText += "<td class='text-center'>"+nullChk(data.CRR_NM,'-')+"</td>";
        tableText += "<td class='text-center'>"+nullChk(data.MGG_NM,'-')+"</td>";
        tableText += "<td class='text-center'>"+nullChk(data.AUU_NM,'-')+"</td>";
        tableText += "<td class='text-center'>"+nullChk(data.RCT_NM,'-')+"</td>";
        tableText += "<td class='text-center'>"+nullChk(data.MGG_BIZ_START_DT,'-')+"</td>";
        tableText += "<td class='text-center'>"+nullChk(data.MGG_BIZ_END_DT,'-')+"</td>";
        tableText += "<td class='text-center'>"+data.NCPD_STOCK_DT+"</td>";
        tableText += "<td class='text-center'>"+data.NCC_NM+"</td>";
        tableText += "<td class='text-center'>"+data.NCP_NM+"</td>";
        tableText += "<td class='text-center'>"+data.NCP_ID+"</td>";
        tableText += "<td class='text-center'>"+data.NCPD_ID+"</td>";
        tableText += "<td class='text-center'>"+data.NCP_MANUFACTURER+"</td>";
        tableText += "<td class='text-center'>"+numberWithCommas(data.NCP_PRICE)+"</td>";
        tableText += "<td class='text-center'>"+nullChk(data.RCS_NM)+"</td>";
        tableText += "<td class='text-center'>"+nullChk(data.NCPD_CHANGE_DT)+"</td>";
        tableText += "<td class='text-center'>"+nullChk(data.RCP_NM)+"</td>";
        tableText += "<td class='text-center'>"+nullChk(data.NCPD_MEMO_TXT)+"</td>";
        tableText += "<td class='text-center'>"+(data.WRT_USERID == null ? '-' : data.WRT_USER_NM+'('+data.WRT_USERID+')' )+"</td>";
        tableText += "<td class='text-center'>"+data.LAST_DTHMS+"</td>";
        if($("#AUTH_UPDATE").val() == YN_Y){
            tableText += "<td class='text-center'><button type='button' class='btn btn-default' onclick='openPopup("+index+")'>수정</button></td>"; 
        }
        tableText += "</tr>"
        
    });
    paging(totalListCount, currentPage, 'getList'); 
    $('#dataTable').html(tableText);
}

/** 수정팝업창 */
openPopup = (idx) => {
    let selectConsignArray = [];
    if(idx == undefined){
        if($('input[name=multi]:checked').length > 0){
                $('input[name=multi]:checked').each(function(){
                    if($(this).val() != ''){
                        selectConsignArray.push($(this).val());
                    }
                });
        }else{
            alert("선택된 항목이 없습니다.");
        }

    }else {
        selectConsignArray.push(idx);
    }

    let form = $('#sendForm')[0];
    let htmlTxt = ''
    if(selectConsignArray.length > 0){
        selectConsignArray.map((idx)=>{
            const data = listArray[idx];
            htmlTxt += "<input type='hidden' name='NCP_ID[]' value="+data.NCP_ID+">";
            htmlTxt += "<input type='hidden' name='NCPD_ID[]' value="+data.NCPD_ID+">";
           
        });
        const popupHeight = selectConsignArray.length == 1 ? 665 : 430;
        window.open('', 
            'receiptPopup', 
            'width=500,height='+popupHeight+' ,left='+ popupX + ', top='+ popupY); 
        form.action = './receiptPopup.html';
        form.target = 'receiptPopup';
        form.method = 'POST';
        $('#sendForm').html(htmlTxt);
        
        form.submit();
    }  
}

onLoadPopupSetting = (one) => {
    getGarageInfo();
    getSaInfo();
    
    $("#NCPD_CHANGE_DT").datepicker(
        getDatePickerSetting()
    );
    if(one){ //단일 수정일 경우
        setUpdateData($('#NCP_ID').val(), $('#NCPD_ID').val());
    }else{
        getRefOptions(REF_CONSIGN_STATUS, 'RCS_ID', false, false);                        //장비상태
        getRefOptions(REF_CONSIGN_SPOT, 'RCP_ID', false, false);                        //장비위치
    }
    
}

/* 업데이트 모달창일경우 */
setUpdateData = (ncp,ncpd) => {
    const param = { 
      REQ_MODE :CASE_READ,
      NCP_ID: ncp,
      NCPD_ID: ncpd
    }
    callAjax(RECEIPT_URL, 'POST', param).then((result)=>{
        const res = JSON.parse(result);
        if(res.status == OK){
          const data = res.data;
        //   console.log('setUpdateData>>',data);
            $('#MGG_NM').val(data.MGG_NM);
            $('#MGG_ID').val(data.MGG_ID);
            $('#AUU_NM').val(data.AUU_NM);
            $('#AUU_ID').val(data.AUU_ID);
            setConsignType('NCC_ID','=장비구분=',data.NCC_ID);
            setConsign('NCP_NM','=장비=',data.NCP_ID,false);
            $('#NCP_ID').val(data.NCP_ID);
            $('#NCPD_MEMO_TXT').val(data.NCPD_MEMO_TXT);
            $('#NCPD_STOCK_DT').val(data.NCPD_STOCK_DT);
            $('#NCPD_CHANGE_DT').val(data.NCPD_CHANGE_DT);
            getRefOptions(REF_CONSIGN_STATUS, 'RCS_ID', false, false, undefined, data.RCS_ID);                        //장비상태
            getRefOptions(REF_CONSIGN_SPOT, 'RCP_ID', false, false, undefined, data.RCP_ID);                        //장비위치
        }else{
            alert('정보를 불러올 수 없습니다. 재시도 바랍니다.');
            window.close();
        }
    });
}


setData = () => {
    const form = $("#addForm")[0];
    const formData = new FormData(form);
    formData.append('REQ_MODE',CASE_UPDATE);

    callFormAjax(RECEIPT_URL, 'POST', formData).then((result)=>{
        // console.log('setData SQL>>',result);
        const data = JSON.parse(result);
        if(data.status == OK){
            confirmSwalOneButton("저장되었습니다.",'확인',()=>{
                window.opener.getList();
                self.close();
            });
        }else{
            alert(data.message);
            self.close();
            window.opener.getList();
        } 
    });  
}


excelDownload = () => {
    location.href="receiptExcel.html?"+$("#searchForm").serialize();
}

