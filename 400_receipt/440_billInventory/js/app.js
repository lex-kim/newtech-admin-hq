const BILL_URL ='act/billInventory_act.php';
const INVENTORY_URL ='../430_gwInventory/act/gwInventory_act.php';
let CURR_PAGE = 1;
const TYPE_BILL = STOCK_BILL ;
let tdLength = 0;

$(document).ready(() =>{
    $( "#START_DT_SEARCH").datepicker(
        getDatePickerSetting('END_DT_SEARCH', 'minDate')
    );
    $( "#END_DT_SEARCH").datepicker(
        getDatePickerSetting('START_DT_SEARCH', 'maxDate')
    );

    setStatYearMonth() ;
    getDivisionOption('CSD_ID_SEARCH', true);                                      //지역구분
    setCRR_ID_Option('CSD_ID_SEARCH', 'CRR_ID_SEARCH', true);
    getRefOptions(GET_REF_EXP_TYPE, 'RET_ID_SEARCH', true, false);                 //청구식
    setGwOptions('MGG_NM_SEARCH',undefined,'=공업사명=');
    getRefOptions(GET_REF_CONTRACT_TYPE, 'RCT_ID_SEARCH', false, false);           //거래여부 
    setTableHeader();                                                              //동적테이블 셋팅  
    
    /* 해당년 선택시 기간 셋팅 */
    $('#YEAR_SEARCH').change(function() {
        $('#START_DT_SEARCH').val($('#YEAR_SEARCH').val()+'-01-01');
        $('#END_DT_SEARCH').val($('#YEAR_SEARCH').val()+'-12-31');
    });

    /* 해당월 선택시 기간 셋팅 */
    $('#MONTH_SEARCH').change(function() {
        if($('#YEAR_SEARCH').val() == '' ){
            alert('해당년을 선택 후 설정하세요.');
            $('#MONTH_SEARCH').val('');
            return false;
        }else {
            $('#START_DT_SEARCH').val($('#YEAR_SEARCH').val()+'-'+$('#MONTH_SEARCH').val()+'-01');
            $('#END_DT_SEARCH').val($('#YEAR_SEARCH').val()+'-'+$('#MONTH_SEARCH').val()+'-'+new Date( $('#YEAR_SEARCH').val(), $('#MONTH_SEARCH').val(), 0).getDate());  
        }
    })
    
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
});

/* 입출고등록 팝업창 */
showAddPopup = () => {
    window.open('/400_receipt/401_enrolReceipt/enrolReceipt.html?type=add', 
                    'enrolReceipt', 
                    'width=1500,height=920'); 
}

/* 재고정산 환경설정*/
showSettingModal = () => {
    setCPPSOptions('NIS_FROM_CPPS_ID','=선택=', OPTIONS_CPPS);
    setCPPSOptions('NIS_TO_CPPS_ID','=선택=', OPTIONS_CPPS);
    $('#add-new').modal();
    getSettingData();
}



/* 테이블 헤더 셋팅 */
setTableHeader = () => {
    const data = {
      REQ_MODE: GET_PARTS_INFO,
    }
    callAjax(BILL_URL, 'GET', data).then((result)=>{
        // console.log('HEADER_CNT RESULT>',result);
        const res = JSON.parse(result);
        // console.log('HEADER_CNT res>',res);
        if(res.status == OK){
            const itemCols = res.data;
            $('#dataHeader th').remove();
            let headerTxt ='';
            let subHeader = '';

            headerTxt += "<th class='text-center' rowspan='2'>순번</th>";
            headerTxt += "<th class='text-center' rowspan='2'>집계년월일</th>";
            headerTxt += "<th class='text-center' rowspan='2'>구분</th>";
            headerTxt += "<th class='text-center' rowspan='2'>지역</th>";
            headerTxt += "<th class='text-center' rowspan='2'>공업사명</th>";
            headerTxt += "<th class='text-center' rowspan='2'>사업자번호</th>";
            headerTxt += "<th class='text-center' rowspan='2'>청구식</th>";
            headerTxt += "<th class='text-center' rowspan='2'>거래여부</th>";
            headerTxt += "<th class='text-center' rowspan='2'>개시일</th>";
            headerTxt += "<th class='text-center' rowspan='2'>종료일</th>";
            headerTxt += "<th class='text-center' rowspan='2'>당월<br>청구건수</th>";
            headerTxt += "<th class='text-center' rowspan='2'>당월<br>청구금액</th>";
            headerTxt += "<th class='text-center' rowspan='2'>당월<br>공급금액</th>";
            headerTxt += "<th class='text-center' rowspan='2'>재고금액</th>";
            
            itemCols.map((data, index)=>{
                headerTxt += "<th class='text-center' colspan='3' id='"+data.CPP_ID+"'>"+data.CPP_NM_SHORT+"</th>";
                subHeader += '<th class="text-center">공급</th><th class="text-center">청구</th><th class="text-center">재고</th>';
            })
            headerTxt += "<th class='text-center' rowspan='2'>보험사협력업체</th>";
            headerTxt += "<th class='text-center' rowspan='2'>제조사협력업체 </th>";
            headerTxt += "</tr>";
            $('#dataHeader').append(headerTxt);
            $('#dataSubHeader').append(subHeader);
            tdLength = 16 + itemCols.length*3;
            setInitTableData(tdLength);
        }else{
          $('#dataHeader').delete();
          alert(ERROR_INSU_NO_DATA);
        }
    });
  }




getList = (currentPage) =>{
    CURR_PAGE = (currentPage==undefined) ? CURR_PAGE : currentPage;
    const param = {
        REQ_MODE: CASE_LIST,
        PER_PAGE:  $("#perPage").val(),
        CURR_PAGE:  CURR_PAGE,
    
        /** 검색 */
        START_DT_SEARCH: $('#START_DT_SEARCH').val(),
        END_DT_SEARCH: $('#END_DT_SEARCH').val(),
        CSD_ID_SEARCH: $('#CSD_ID_SEARCH').val(),
        CRR_ID_SEARCH: $('#CRR_ID_SEARCH').val(),
        MGG_NM_SEARCH: $('#MGG_NM_SEARCH').val(),
        RET_ID_SEARCH: $('#RET_ID_SEARCH').val(),
        RCT_ID_SEARCH: $('#RCT_ID_SEARCH').val()
    }
    // console.log('getList param >>>',param);
    callAjax(BILL_URL, 'GET', param).then((result)=>{
        // console.log('getList SQL>',result);
        const res = JSON.parse(result);
        // console.log('getList res>',res);
        if(res.status == OK){
          $('#indicator').hide();
            const item = res.data[0];
            let tableTxt = '';
            item.map((data, i)=>{
              tableTxt += "<tr id='tr_"+data.MGG_ID+"'>";
              tableTxt += "<td class='text-center'>"+data.IDX+"</td>";
              tableTxt += "<td class='text-center'>"+(data.NSB_ID == '' ? '-' : dateYYYYMMDD(data.NSB_ID ))+"</td>";
              tableTxt += "<td class='text-center'>"+data.CSD_NM+"</td>";
              tableTxt += "<td class='text-left'>"+data.CRR_NM+"</td>";
              tableTxt += "<td class='text-left'>"+data.MGG_NM+"</td>";
              tableTxt += "<td class='text-center'>"+bizNoFormat(data.MGG_BIZ_ID)+"</td>";
              tableTxt += "<td class='text-center'>"+data.RET_NM+"</td>";
              tableTxt += "<td class='text-center'>"+data.RCT_NM+"</td>";
              tableTxt += "<td class='text-center'>"+data.MGG_BIZ_START_DT+"</td>";
              tableTxt += "<td class='text-center'>"+data.MGG_BIZ_END_DT+"</td>";
              tableTxt += "<td class='text-right'>"+numberWithCommas(Number(data.NSB_BILL_CNT))+"</td>";
              tableTxt += "<td class='text-right'>"+numberWithCommas(Number(data.NSB_BILL_AMOUNT))+"</td>";
              tableTxt += "<td class='text-right'>"+numberWithCommas(Number(data.NSB_SUPPLY_AMOUNT))+"</td>";
              tableTxt += "<td class='text-right'>"+numberWithCommas(Number(data.NSB_STOCK_AMOUNT))+"</td>";
              
              data.SUMMARY.map((item,i)=>{
                tableTxt += "<td class='text-right'>"+item.NSBP_SUPPLY_CNT+"</td>";
                tableTxt += "<td class='text-right'>"+item.NSBP_BILL_CNT+"</td>";
                tableTxt += "<td class='text-right'>"+item.NSBP_STOCK_CNT+"</td>";
              });
              tableTxt += "<td class='text-center'>"+(empty(data.MGG_INSU_MEMO) ? "-" : data.MGG_INSU_MEMO)+"</td>";
              tableTxt += "<td class='text-center'>"+(empty(data.MGG_MANUFACTURE_MEMO) ? "-" : data.MGG_MANUFACTURE_MEMO)+"</td>";
              tableTxt += "</tr>";
            })
            paging(res.data[1], CURR_PAGE, 'getList'); 
            $("#dataTable").html(tableTxt);
        }else{
          const tableText = "<tr><td class='text-center' colspan='"+tdLength+"'>조회요청한 데이터가 존재하지 않습니다.</td></tr>";
          $('.pagination').html('');
          $('#dataTable').html(tableText);
          
        }
    });
  
  }
  

/**
 * 변동일자별 공업사 입출고 기록 조회
 * @param {array} data 공업사 기본 정보
 */
getStaticTable = (data) => {
  let param ={
    REQ_MODE: CASE_LIST_SUB,
    MGG_ID: data.MGG_ID,
    NSB_ID: data.NSB_ID,
  };
  // console.log('summary>>', param.MGG_ID, '/', param.NIH_DT);
  callAjax(BILL_URL, 'GET', param).then((result)=>{
    // console.log('CASE_SUBLIST query',result);
    const res = JSON.parse(result);
    // console.log('CASE_SUBLIST res>',res);
    
    if(res.status == OK){
      const arrItem = res.data;
      let summaryTxt ='';
      arrItem.map((item,i)=>{
        summaryTxt += "<td class='text-right'>"+item.NSBP_SUPPLY_CNT+"</td>";
        summaryTxt += "<td class='text-right'>"+item.NSBP_BILL_CNT+"</td>";
        summaryTxt += "<td class='text-right'>"+item.NSBP_STOCK_CNT+"</td>";
      });
      
      $('#tr_'+data.MGG_ID).append(summaryTxt);
    }else{
      alert(data.MGG_NM+'의 재고가 올바르지 않습니다.');
    }


  });
}  


/* 엑셀 다운로드 */
excelDownload = () => {
    $('#searchForm').attr("method","GET");
    $('#searchForm').attr("action","billInventoryExcel.html");
    $('#searchForm').submit();
}

 /* GET NT_STOCK.BILLSTOCK YEAR OF 'NSB_ID' */
 setStatYearMonth = () => {
  const data = {
      REQ_MODE: OPTIONS_YEAR 
  };
  // console.log('setPartOptions >> ', data);
  callAjax(BILL_URL, 'GET', data).then((result)=>{
      // console.log('partQuery>>',result);
      
      const res = JSON.parse(result);
      // console.log('setStatYearMonth res >>>',res);
      if(res.status == OK){
        let yearOptionList = [];
        let monthOptionList = [];
  
        $("#YEAR_SEARCH option").remove();  // YEAR_SEARCH 개체 초기화
        // $("#MONTH_SEARCH option").remove();  // MONTH_SEARCH 개체 초기화
        yearOptionList.push($("<option>", { text: "=해당년=" }).attr("value","").attr("selected","selected")); //최초조건 추가
        // monthOptionList.push($("<option>", { text: "=해당월=" }).attr("value","").attr("selected","selected")); //최초조건 추가
       
        res.data.map((item, i)=>{
            yearOptionList.push($("<option>", { text: item.NSB_YEAR }).attr("value",item.NSB_YEAR));
            // monthOptionList.push($("<option>", { text: item.NSB_MONTH }).attr("value",item.NSB_MONTH));
        })
        $("#YEAR_SEARCH").append(yearOptionList);  // <YEAR_SEARCH> 객체에 반영
        $("#MONTH_SEARCH" ).append(monthOptionList);  // <MONTH_SEARCH> 객체에 반영
      }else {
        alert('조회 가능한 데이터가 존재하지 않습니다.');
      }
      

  
  });
}
