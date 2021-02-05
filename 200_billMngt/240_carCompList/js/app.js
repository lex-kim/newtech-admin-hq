const CARCOMPLIST_URL ='act/carCompList_act.php'
let CURR_PAGE = 1;
let listArray = [];     

$(document).ready(function() {
    setInitTableData(24, 'carCompListContents');

    /* 검색옵션 */
    getDivisionOption('CSD_ID_SEARCH', true);  //구분
    setGwOptions('SEARCH_BIZ_NM', undefined, "=공업사="); //공업사
    setCRR_ID_Option('CSD_ID_SEARCH', 'CRR_ID_SEARCH', true); //지역구분
    setIwOptions('SEARCH_INSU_NM', undefined, "=보험사="); //보험사
    getRefOptions(GET_REF_EXP_TYPE, 'SEARCH_RET_ID', true, false);  //청구식

    $( "#START_DT" ).datepicker(
        getDatePickerSetting('END_DT', 'minDate')
    );
    $( "#END_DT" ).datepicker(
        getDatePickerSetting('START_DT', 'maxDate')
    );

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

     /** 지역검색 
   * @param {'':선택한 지역구, null: 시-군구 / 1: 시-군구-동 } */
  getCRRInfo('').then((data)=>{
    if(data.status == OK){
        const dataArray = data.data;
        if(dataArray.length > 0){
          let optionText = '';
          dataArray.map((item, index)=>{
              let ITEM_ID = item.CRR_1_ID+","+item.CRR_2_ID+","+item.CRR_3_ID;
              optionText += '<option value="'+ITEM_ID+'">'+item.CRR_NM+'</option>';
          });
          autoCompleteCRR('CRR_ID_SEARCH', 'SELECT_CRR_ID', dataArray);  
        }else{
          resetCRRValue('CRR_ID_SEARCH', 'SELECT_CRR_ID');
        }
    } else{
      resetCRRValue();
    }  
  });


    // /** 검색에서 기간을 눌렀을 때 날짜 초기화 */
    $('#SELECT_DT').change(()=>{
        if($('#SELECT_DT').val() == ''){
            $('#START_DT').val('');
            $('#END_DT').val('');
        }
    });

    $('#SEARCH_CAR_INFO').change(()=>{
        if($('#SEARCH_CAR_INFO').val() == ''){
            $('#KEYWORD_CAR_INFO').val('');
        }
    });

    /** 검색 */
    $("#searchForm").submit(function(e){
        e.preventDefault();

        getList(1);
    });


});




/** 메인 리스트 테이블 동적으로 생성 
 * @param {dataArray : 리스트 데이터, currentPage : 현재페이지(페이징을 위해)} 
*/
setTable = (totalListCount, dataArray, currentPage) => {
  let tableText = '';
  listArray  = dataArray;

  dataArray.map((data, index)=>{

        // 청구여부
        let nicYN = '';
        if(data.CLAIM_YN == YN_Y){
            nicYN = '청구';
        }else{
            nicYN = '미청구';
        }

        tableText += "<tr>";
        tableText += "<td class='text-center'>"+data.IDX+"</td>";
        tableText += "<td class='text-center'>"+data.MGG_ID+"</td>";
        tableText += "<td class='text-center'>"+data.WRT_DTHMS+"</td>";
        tableText += "<td class='text-center'>"+data.CSD_NM+"</td>";
        tableText += "<td class='text-left'>"+data.CRR_NM+"</td>";
        tableText += "<td class='text-left'>"+data.NQ_GARAGE_NM+"</td>";
        tableText += "<td class='text-center'>"+bizNoFormat(data.MGG_BIZ_ID)+"</td>";
        tableText += "<td class='text-center'>"+data.RET_ID+"</td>";
        tableText += "<td class='text-center'>"+phoneFormat(data.MGG_OFFICE_TEL)+"</td>";
        tableText += "<td class='text-center'>"+data.MII_NM+"</td>";
        tableText += "<td class='text-center'>"+data.MIT_NM+"</td>";
        tableText += "<td class='text-center'>"+data.NQI_CHG_NM+"</td>";
        tableText += "<td class='text-center'>"+phoneFormat(data.NIC_TEL1)+"</td>";
        tableText += "<td class='text-center'>"+data.NQI_REGI_NUM+"</td>";
        tableText += "<td class='text-center'>"+data.RLT_NM+"</td>";
        tableText += "<td class='text-center'>"+data.NQ_CAR_NUM+"</td>";
        tableText += "<td class='text-center'>"+data.CCC_CAR_NM+"</td>";
        tableText += "<td class='text-right'>"+numberWithCommas(Number(data.NQ_TOTAL_PRICE))+"</td>";
        tableText += "<td class='text-right'>"+numberWithCommas(Number(data.OWNER_PRICE))+"</td>";
        tableText += "<td class='text-center'>";
        tableText += "<button type='button' class='btn btn-default' onclick='confirmBillPrint("+index+");'>출력</button>";
        if(data.CLAIM_YN == YN_N){
            tableText += "<button type='button' class='btn btn-default' onclick='confirmSetData("+JSON.stringify(data)+")'>청구</button>";
        }
        if($("#AUTH_DELETE").val() == YN_Y){
            tableText += "<button type='button' class='btn btn-danger' onclick='confirmDelSA(\""+data.NQ_ID+"\")''>삭제</button>";
        }
        tableText += "</td>";
        tableText += "<td class='text-center'>"+nicYN+"</td>";
        tableText += "<td class='text-center'>"+data.NQ_CLAIM_DT+"</td>";
        tableText += "<td class='text-center'>"+data.MGG_INSU_MEMO+"</td>";
        tableText += "<td class='text-center'>"+data.MGG_MANUFACTURE_MEMO+"</td>";
        tableText += "</tr>";
      
  });
  paging(totalListCount, currentPage, 'getList'); 
  $('#carCompListContents').html(tableText);
}

/** 메인리스트 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
  const tableText = "<tr><td class='text-center' colspan='24'>조회요청한 데이터가 존재하지 않습니다.</td></tr>";
  $('.pagination').html('');
  $('#carCompListContents').html(tableText);
}


/* 메인 리스트 */
getList = (currentPage) => {
    CURR_PAGE = (currentPage == undefined) ? CURR_PAGE : currentPage;
    const data = {
        REQ_MODE: CASE_LIST,
        PER_PAGE:  $("#perPage").val(),
        CURR_PAGE:  CURR_PAGE,
        SEARCH_SELECT_DT: $('#SELECT_DT').val(),
        SEARCH_START_DT: $('#START_DT').val(),
        SEARCH_END_DT: $('#END_DT').val(),
        CSD_ID_SEARCH: $('#CSD_ID_SEARCH').val(),
        CRR_ID_SEARCH: $('#CRR_ID_SEARCH').val(),
        SEARCH_BIZ_NM: $('#SEARCH_BIZ_NM').val(),
        SEARCH_RET_ID: $('#SEARCH_RET_ID').val(),
        SEARCH_INSU_NM: $('#SEARCH_INSU_NM').val(),
        SEARCH_RLT_ID: $('#SEARCH_RLT_ID').val(),
        SEARCH_CLAIM_YN: $('#SEARCH_CLAIM_YN').val(),
        SEARCH_CAR_INFO: $('#SEARCH_CAR_INFO').val(),
        KEYWORD_CAR_INFO: $('#KEYWORD_CAR_INFO').val(),
    };

    callAjax(CARCOMPLIST_URL, 'GET', data).then((result)=>{
        const res = JSON.parse(result);
        
        if(res.status == OK){
            $('#indicator').hide();
            const item = res.data;
            setTable(item[1], item[0], currentPage);
        }else{
            setHaveNoDataTable();
        }
    });
  
}


confirmBillPrint = (idx) => {
    confirmSwalTwoButton("출력 하시겠습니까?",'확인', '취소', false , ()=>{   
        printBill(idx);
    });
}

confirmSetData = (item) => {
    confirmSwalTwoButton("해당 견적서를 청구하시겠습니까?",OK_MSG, CANCEL_MSG, false,()=>{setData(item)}, '청구시 팩스도 발송됩니다.');
}


printBill = (idx) => {
    const popupUrl = '../../_SLF.html?NB_ID='+listArray[idx].NQ_ID+'&MGG_ID='+listArray[idx].MGG_ID+"&SQL_TYPE=NQ";
    const data = {
        REQ_MODE: CASE_LIST_SUB
    };
    
    callAjax(popupUrl, 'GET', data).then((result)=>{
        const strFeature = "width=1000, height=1200, all=no";
        let objWin = window.open('', 'print', strFeature);
        objWin.document.write(result);
        setTimeout(function() {
            objWin.focus();
            objWin.print();
            objWin.close();
        }, 250);
    });
}

sendFax = (item) => {
    let form = $("#faxForm")[0];
    let formData = new FormData(form);
    let sendResultSuccess = 0;
    let sendResultFail = 0;

    const faxArray = listArray.filter((fax)=>{
        return fax.NQ_ID == item.NQ_ID;
    });

    let sendResultCount = faxArray.length;
    faxArray.map((data)=>{
        formData.append('NB_ID', data.NQ_ID);
        formData.append('MGG_ID', data.MGG_ID);
        formData.append('NBI_CHG_NM', data.NQ_GARAGE_NM);
        formData.append('NBI_FAX_NUM', data.NQI_FAX_NUM);
        formData.append('NBI_FAX_RMT_ID', data.NBI_RMT_ID);
        formData.append('MII_NM', data.MII_NM);
        formData.append('CAR_NUM', data.NQ_CAR_NUM);

        callFormAjax('../../../sendFAX.php', 'POST', formData).then((result)=>{
            if(result){
                sendResultSuccess++;
            }else{
                sendResultFail++;
            }

            if(sendResultCount == (sendResultSuccess+sendResultFail)){
                if(sendResultCount == sendResultSuccess){
                    confirmSwalOneButton("청구완료되었습니다.", OK_MSG,()=>{
                        getList();
                    });
                }else if(sendResultCount == sendResultFail){
                    confirmSwalOneButton("청구는 완료되었으나 팩스발송에 실패하였습니다.", OK_MSG,()=>{
                        getList();
                    });
                }else{
                    const msg = "성공 : "+sendResultSuccess+"건 , 실패 : "+sendResultFail+"건 입니다.";
                    confirmSwalOneButton(msg, OK_MSG,()=>{
                        getList();
                    });
                }
            }
        });
    });
}

/**
 * 청구서 
 */
setData = (item) => {
    console.log(item);
    const data = {
        REQ_MODE: CASE_CREATE,
        NQ_ID : item.NQ_ID,
        NBI_RMT_ID : item.NBI_RMT_ID,
    }

    callAjax(CARCOMPLIST_URL, 'POST', data).then((result)=>{
        if(isJson(result)){
            const data = JSON.parse(result);
            if(data.status == OK){
                sendFax(item);
            }else{
                alert(ERROR_MSG_1100);
            }
        }else{
            alert(ERROR_MSG_1200);
        }   
    });
}


/**엑셀다운 */
excelDownload = () => {
    location.href="carCompListExcel.html?"+$("#searchForm").serialize()+"&REQ_MODE="+CASE_TOTAL_LIST;
}

/**
 * 삭제 컨펌창
 * @param {detailText : 삭제 시 필요한 설명이 있을 경우에 보내준다.}
 */
confirmDelSA = (nqID) => {
    confirmSwalTwoButton(CONFIRM_DELETE_MSG, DEL_MSG, CANCEL_MSG, true,()=>{delData(nqID)});
}

/* 리스트에서 선택한 데이터 삭제 */
delData = (nqID) => {
    const data = {
        REQ_MODE: CASE_DELETE,
        NQ_ID : nqID,
    };
    callAjax(CARCOMPLIST_URL, 'POST', data).then((result)=>{
        const data = JSON.parse(result);
        if(data.status == OK){
            getList(CURR_PAGE);
        }else{
            const resultText ='서버와의 연결이 원활하지 않습니다.' ;
            alertSwal(resultText);
        } 
    });
}



// 옵션불러오기


  
  
 
