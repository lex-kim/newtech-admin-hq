const INVENTORY_URL ='act/gwInventory_act.php';
let CURR_PAGE = 1;
let tdLength = 0;
let listArray = [];
let sortArray = [];
let tableStatus = ERROR;
let resData = [];
$(document).ready(() =>{
    getDivisionOption('CSD_ID_SEARCH', true);                                      //지역구분
    setCRR_ID_Option('CSD_ID_SEARCH', 'CRR_ID_SEARCH', true);
    getRefOptions(GET_REF_EXP_TYPE, 'RET_ID_SEARCH', true, false);                 //청구식
    setGwOptions('MGG_NM_SEARCH',undefined,'=공업사명=');
    getRefOptions(GET_REF_CONTRACT_TYPE, 'RCT_ID_SEARCH', false, false);           //거래여부 
    setCPPSOptions('CPPS_ID_SEARCH','=부품=',OPTIONS_CPPS)                            //취급부품
    getSettingData();   //정산정보 설정
    setCartTable();     //통계테이블 셋팅
    setTableHeader();   //동적테이블 셋팅

    /** 검색 */
    $("#searchForm").submit(function(e){
        e.preventDefault();
        setCartTable(); 
        getList(1);
            
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
    
    $('#settingForm').each(function(){
        this.reset();
    });
    getSettingData();
    setCPPSOptions('NIS_FROM_CPPS_ID','=선택=', OPTIONS_CPPS);
    setCPPSOptions('NIS_TO_CPPS_ID','=선택=', OPTIONS_CPPS);

    $('#add-new').modal();
}

/* 독립정산 확인  */
chkIndependent = () =>{
    let arrChk = $("input[name=chkBox]:checked").map(function(){
        return $(this).attr('id');
    }).get();
    if(arrChk.length > 0 ){
        confirmSwalTwoButton(
            arrChk.length+'개 공업사의 독립정산을 진행하시겠습니까?',
            OK_MSG,
            CANCEL_MSG,
            false,
            ()=> setIndependent(arrChk)
        );
    }else {
        confirmSwalOneButton(
            '선택된 공업사가 없습니다.',
            OK_MSG,
            ()=>console.log('no select gw')
        );
    }
}

/** 
 * 독립정산 적용
 * @param {array} arr : 독립정산 대상 MGG_ID 
*/
setIndependent = (arr) => {
    // console.log('setIndependent arr>>',arr.length);
    let okCnt = 0;
    arr.map((data,index)=>{
        // console.log('71>>',data, index);
        const param ={
            REQ_MODE: STOCK_INDE,
            MGG_ID: data,
            NIE_INDEPENDENT_STOCK_RATIO: $('#NIE_INDEPENDENT_STOCK_RATIO').val()
        }
        callAjax(INVENTORY_URL, 'post', param).then((result)=>{
              console.log('setIndependent SQL >',result);
            const res = JSON.parse(result);
            //   console.log('setIndependent DATA>>',res);
            if(res.status == OK || res.status == HAVE_NO_DATA){   
                okCnt ++;
            } else{
                console.log(data.MGG_ID+'('+data.MGG_NM+')공업사 독립정산에 실패하였습니다. 재시도해주세요.');
                
            }  
        });
        if(arr.length == index+1){
            confirmSwalOneButton('독립정산이 적용되었습니다.', OK_MSG,()=>console.log(okCnt,' independent success'));
            getList();
        }
    });


    
}


/* 대체정산 확인  */
chkSubstitution  = () =>{
    let arrChk = $("input[name=chkBox]:checked").map(function(){
        return $(this).attr('id');
    }).get();
    if(arrChk.length > 0 ){
        confirmSwalTwoButton(
            arrChk.length+'개 공업사의 대체정산을 진행하시겠습니까?',
            OK_MSG,
            CANCEL_MSG,
            false,
            ()=> setSubstitution(arrChk)
        );
    }else {
        confirmSwalOneButton(
            '선택된 공업사가 없습니다.',
            OK_MSG,
            ()=>console.log('no select gw')
        )
    }

}


/** 
 * 대체정산 적용
 * @param {array} arr : 대체정산 대상 MGG_ID 
*/
setSubstitution = (arr) => {
    let okCnt = 0;
    arr.map((data, index)=> {
        const param ={
            REQ_MODE: STOCK_SUBST,
            MGG_ID: data,
            NIE_INDEPENDENT_STOCK_RATIO: $('#NIE_INDEPENDENT_STOCK_RATIO').val(),
            NIE_SUBSTITUTION: $('#SELECT_SUBSTITUTION').val()
        }
        callAjax(INVENTORY_URL, 'post', param).then((result)=>{
            // console.log('setSubstitution SQL >',result);
            const res = JSON.parse(result);
            //   console.log('setSubstitution DATA>>',res);
            if(res.status == OK || res.status == HAVE_NO_DATA){   
                okCnt ++;
                // setSubstitution($('#MGG_ID').val(), $('#MGG_NM').val());
            }else{
                console.log(data.MGG_ID+'('+data.MGG_NM+')공업사 대체정산에 실패하였습니다. 재시도해주세요.');

            }  
        });
        if(arr.length == index+1){
            confirmSwalOneButton('대체정산이 적용되었습니다.', OK_MSG,()=>console.log(okCnt,' substituttion success'));
            getList();
        }
    });
}

/** 
 * 대체정산식 출력 
 * @param {array} arrData : 대체정산식 list 
*/
getListSubstitution = (arrData) => {
    let tableTxt = '';
    $('#SELECT_SUBSTITUTION option').remove();
    arrData.map((arr, i)=>{
        // console.log(i, 'getSubstitution>>',arr);
        tableTxt += "<option value='"+arr.NIS_FROM_CPPS_ID+'_'+arr.NIS_TO_CPPS_ID+'_'+arr.NIS_RATIO+"'>"
        tableTxt += arr.NIS_FROM_CPPS_NM+' 대체정산식: ';
        tableTxt += arr.NIS_TO_CPPS_NM+' = '+arr.NIS_TO_CPPS_NM+" + ";
        tableTxt += "("+arr.NIS_FROM_CPPS_NM+" X "+arr.NIS_RATIO+")";
        tableTxt += "</option>";
        
    })
    $('#SELECT_SUBSTITUTION').html(tableTxt);
}


/* 테이블 헤더 셋팅 */
setTableHeader = () => {
    const data = {
      REQ_MODE: GET_PARTS_INFO,
    }
    callAjax(INVENTORY_URL, 'GET', data).then((result)=>{
        // console.log('HEADER_CNT RESULT>',result);
        const res = JSON.parse(result);
        // console.log('HEADER_CNT res>',res);
        if(res.status == OK){
            const itemCols = res.data;
            $('#dataHeader th').remove();
            let hederTxt ='';
            hederTxt += "<th class='text-center' rowspan='2'>선택</th>"
            hederTxt += "<th class='text-center' rowspan='2'>순번</th>";
            hederTxt += "<th class='text-center' rowspan='2'>구분</th>";
            hederTxt += "<th class='text-center' rowspan='2'>지역</th>";
            hederTxt += "<th class='text-center' rowspan='2'>공업사명</th>";
            hederTxt += "<th class='text-center' rowspan='2'>사업자번호</th>";
            hederTxt += "<th class='text-center' rowspan='2'>청구식</th>";
            hederTxt += "<th class='text-center' rowspan='2'>거래여부</th>";
            hederTxt += "<th class='text-center' rowspan='2'>개시일</th>";
            hederTxt += "<th class='text-center' rowspan='2'>종료일</th>";
            hederTxt += "<th class='text-center' rowspan='2'>당월 청구건수</th>";
            hederTxt += "<th class='text-center' rowspan='2'>당월 청구금액</th>";
            hederTxt += "<th class='text-center' rowspan='2'>재고금액</th>";
            itemCols.map((data, index)=>{
              hederTxt += "<th class='text-center' rowspan='2' id='"+data.CPPS_ID+"'>"+data.CPPS_NM_SHORT+"</th>";
            })
            hederTxt += "<th class='text-center' colspan='2'>협력업체</th>";
            hederTxt += "</tr>";
            $('#dataHeader').append(hederTxt);
            tdLength = $('.nwt-table table tr th').length;
            setInitTableData(tdLength);
            tableStatus = OK;
        }else{
          $('#dataHeader').delete();
          alert(ERROR_MSG_NO_DATA);
        }
    });
  }

/* 통계 출력 */
setCartTable = () =>{
    const param ={
        REQ_MODE: GET_PARTS_INFO,
        /** 검색조건 */
        CSD_ID_SEARCH: $('#CSD_ID_SEARCH').val(),
        CRR_ID_SEARCH: $('#CRR_ID_SEARCH').val(),
        MGG_NM_SEARCH: $('#MGG_NM_SEARCH').val(),
        MGG_BIZ_ID_SEARCH: $('#MGG_BIZ_ID_SEARCH').val(),
        RET_ID_SEARCH: $('#RET_ID_SEARCH').val(),
        RCT_ID_SEARCH: $('#RCT_ID_SEARCH').val(),
        CPPS_ID_SEARCH: $('#CPPS_ID_SEARCH').val()
    }
    callAjax(INVENTORY_URL, 'POST', param).then((result)=>{
        // console.log('setCartTable SQL>>',result);
        const res = JSON.parse(result);
        // console.log('setCartTable res>>',res);
            if(res.status == OK){
                $('#charHeader th').remove();
                $('#cartData td').remove();
                const item = res.data;
                let headerTxt = '';
                let chartTxt = '';
                item.map((data, index)=>{
                    headerTxt += "<th class='text-center'>"+data.CPPS_NM_SHORT+"</th>"   ;
                    chartTxt += "<td>"+Number(data.AMOUNT).toFixed(2)+"</td>";
                    
                });
                $('#chartHader').html(headerTxt);
                $('#cartData').html(chartTxt);
                if(tableStatus != OK){
                    tableStatus = ALREADY_EXISTS;
                    $('#indicator').show();  
                }else {
                    $('#indicator').hide(); 
                }
            }else{
                alert(ERROR_MSG_1200);
            } 
    });
}

getList = (currentPage) =>{
    $('#indicator').show();  
    sortArray = [];
    tableStatus = ERROR;
    CURR_PAGE = (currentPage==undefined) ? CURR_PAGE : currentPage;
    const param = {
        REQ_MODE: CASE_LIST,
        PER_PAGE:  $("#perPage").val(),
        CURR_PAGE:  CURR_PAGE,

        /** 검색 */
        CSD_ID_SEARCH: $('#CSD_ID_SEARCH').val(),
        CRR_ID_SEARCH: $('#CRR_ID_SEARCH').val(),
        MGG_NM_SEARCH: $('#MGG_NM_SEARCH').val(),
        MGG_BIZ_ID_SEARCH: $('#MGG_BIZ_ID_SEARCH').val(),
        RET_ID_SEARCH: $('#RET_ID_SEARCH').val(),
        RCT_ID_SEARCH: $('#RCT_ID_SEARCH').val(),
        CPPS_ID_SEARCH: $('#CPPS_ID_SEARCH').val()
    }
    // console.log('getList param >>>',param);
    callAjax(INVENTORY_URL, 'GET', param).then((result)=>{
        $('#indicator').show();  
        // console.log('getList RESULT>',result);
        const res = JSON.parse(result);
        console.log('getList res>',res);
        if(res.status == OK){
            listArray = res.data[0];
            sortArray = listArray;
            setTable(res.data[1]);
        }else{
            const tableText = "<tr><td class='text-center' colspan='"+tdLength+"'>조회요청한 데이터가 존재하지 않습니다.</td></tr>";
            $('.pagination').html('');
            $('#dataTable').html(tableText);
            tableStatus = OK;
            $('#indicator').hide();
        }
    });

}


setTable = (totalListCount)  =>{
    let tableTxt = '';
    let listIdx = totalListCount;
    sortArray.map((data,i)=>{
        // console.log(i+1,'perPage >>',$("#perPage").val(),'CurPage>>',CURR_PAGE);
        if($('#CPPS_ID_SEARCH').val() != ''){
            data.IDX = listIdx;
            let min = $("#perPage").val()*(CURR_PAGE-1);
            let max = $("#perPage").val()*(CURR_PAGE);
            if(Number(min) <= i && i < Number(max)){
                tableTxt +=makeTable(data);
            }
        }else {
            tableTxt +=makeTable(data);
        }
        listIdx --;
    });
    paging(totalListCount, CURR_PAGE, 'getList'); 
    $("#dataTable").html(tableTxt);
    tableStatus = OK;
    $('#indicator').hide();
}

makeTable = (data) => {
    let tableTxt = '';
    tableTxt += "<tr id='tr_"+data.MGG_ID+"'>";
    tableTxt += "<td class='text-center'><input type='checkbox' name='chkBox' id='"+data.MGG_ID+"'></td>";
    tableTxt += "<td class='text-center'>"+data.IDX+"</td>";
    tableTxt += "<td class='text-center'>"+data.CSD_NM+"</td>";
    tableTxt += "<td class='text-left'>"+data.CRR_NM+"</td>";
    tableTxt += "<td class='text-left'>"+data.MGG_NM+"</td>";
    tableTxt += "<td class='text-center'>"+bizNoFormat(data.MGG_BIZ_ID)+"</td>";
    tableTxt += "<td class='text-center'>"+data.RET_NM+"</td>";
    tableTxt += "<td class='text-center'>"+data.RCT_NM+"</td>";
    tableTxt += "<td class='text-center'>"+data.MGG_BIZ_START_DT+"</td>";
    tableTxt += "<td class='text-center'>"+data.MGG_BIZ_END_DT+"</td>";
    tableTxt += "<td class='text-right'>"+numberWithCommas(data.NB_CNT)+"</td>";
    tableTxt += "<td class='text-right'>"+numberWithCommas(data.NB_TOTAL_PRICE)+"</td>";
    let summaryTxt ='';
    let npsPrice = 0; //재고금액
    data.SUMMARY.map((item,i)=>{
        summaryTxt += "<td class='text-right'>"+item.NIS_AMOUNT+"</td>";
        npsPrice += Number(item.NPS_PRICE) ;
    });
    
    tableTxt += "<td class='text-right'>"+numberWithCommas(npsPrice)+"</td>";
    tableTxt += summaryTxt;
    tableTxt += "<td class='text-left'>"+data.MGG_INSU_MEMO+"</td>";
    tableTxt += "<td class='text-left'>"+data.MGG_MANUFACTURE_MEMO+"</td>";
    
    tableTxt += "</tr>";

    return tableTxt;
}



/**
 * 변동일자별 공업사 입출고 기록 조회
 * @param {array} data 공업사 기본 정보
 */
getSummaryTable = (data) => {
    let param ={
      REQ_MODE:CASE_LIST_SUB,
      MGG_ID: data.MGG_ID
    };
    // console.log('summary>>', param.MGG_ID, '/');
    callAjax(INVENTORY_URL, 'GET', param).then((result)=>{
    //   console.log('CASE_SUBLIST query',result);
      const res = JSON.parse(result);
    //   console.log('CASE_SUBLIST res>',res);
      
      if(res.status == OK){
        const arrItem = res.data;
        
        let summaryTxt ='';
        let npsPrice = 0;
        arrItem.map((item,i)=>{
          summaryTxt += "<td class='text-right'>"+item.NIS_AMOUNT+"</td>";
          npsPrice += Number(item.NPS_PRICE) ;
        });
        
        let tdPrice = "<td class='text-right'>"+numberWithCommas(npsPrice)+"</td>";
        summaryTxt += "<td class='text-left'>"+data.MGG_INSU_MEMO+"</td>";
        summaryTxt += "<td class='text-left'>"+data.MGG_MANUFACTURE_MEMO+"</td>";
       
        $('#tr_'+data.MGG_ID).append(tdPrice+summaryTxt);
        if(npsPrice == 0){
            $('#'+data.MGG_ID).attr('disabled',true);
        }
      }else{
        alert(data.MGG_NM+'의 재고가 올바르지 않습니다.');
      }
  
  
    });
  }

/* 엑셀 다운로드 */
excelDownload = () => {
    location.href="gwInventoryExcel.html?"+$("#searchForm").serialize();
}