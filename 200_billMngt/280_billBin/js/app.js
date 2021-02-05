const BILLBIN_URL ='act/billBin_act.php'
let CURR_PAGE = 1;
let listArray = [];
const ALL = 'all';

$(document).ready(function() {
    setInitTableData(44, 'billBinContents');

    /* 검색옵션 */
    getDivisionOption('CSD_ID_SEARCH', true);  //구분 
    setGwOptions('SEARCH_BIZ_NM', undefined ,'=공업사명='); //공업사
    setCRR_ID_Option('CSD_ID_SEARCH', 'CRR_ID_SEARCH', true); //지역구분
    setInsuTeam(); //보험사팀
    setIwOptions('SEARCH_INSU_NM'); //보험사
    getRefOptions(GET_REF_EXP_TYPE, 'SEARCH_RET_ID', true, false);  //청구식
    setCarType(); //차량급
    setPart(); //사용부품

    $( "#START_DT" ).datepicker(
        getDatePickerSetting('END_DT', 'minDate')
    );
    $( "#END_DT" ).datepicker(
        getDatePickerSetting('START_DT', 'maxDate')
    );
    $( "#MII_ENROLL_DT" ).datepicker(
        getDatePickerSetting()
    );

     /** 검색 */
     $("#searchForm").submit(function(e){
        e.preventDefault();

        getList(1);
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

    /* 보험사 변경에 따른 보험사 팀 동적 필터 */
    $('#SEARCH_INSU_NM').change(()=>{
        if($('#SEARCH_INSU_NM').selectpicker('val') != null){
            const selectInsuVal = $('#SEARCH_INSU_NM').selectpicker('val');
            console.log('selectInsuVal',selectInsuVal);
            INSUTEAM_LIST = [];
            selectInsuVal.map((insu)=>{
            console.log();
            INSUTEAM_LIST.push(INSUTEAM_ALL.filter((item, index)=>{
                    return item.MII_ID == insu;
                }));
            });
            selectInsu(INSUTEAM_LIST);
        }else {
        setInsuTeam();
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

    /** 지역검색 
     * @param {'':선택한 지역구, null: 시-군구 / 1: 시-군구-동 } 
     * */
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

    // 전체선택 체크
  $("#allcheck").click(function(){
    if($("#allcheck").prop("checked")){
        $("input[name=multi]").prop("checked", true);
    }else{
        $("input[name=multi]").prop("checked", false);
    }
});
    
});


/**
 * 인쇄/일괄인쇄
 */
billPrint = (idx) => {
    let printSendArray = [];
    confirmSwalTwoButton("출력 하시겠습니까?",'확인', '취소', false , ()=>{       
        printSendArray.push(idx);
        printBill(printSendArray);
    });
   
}

downloadImage = (idx) => {  
    const url = "../../../PDF_VIEW.html?NB_ID="+listArray[idx].NB_ID+"&MGG_ID="+listArray[idx].MGG_ID+"&TYPE=JPEG";
    $('#bill-imagedown').modal('hide');
    window.open(url, 
    'loginNoticePopup', 
    'width=1300,height=900');
}

/** 메인 리스트 테이블 동적으로 생성 
 * @param {dataArray : 리스트 데이터, currentPage : 현재페이지(페이징을 위해)} 
*/
setTable = (totalListCount, dataArray, currentPage) => {
  let tableText = '';
  listArray = dataArray;

  dataArray.map((data, index)=>{
        // 입금여부
        let  depositTxt = ""; //입금여부 표기
        if(data.NBI_INSU_DEPOSIT_YN == YN_N){
            depositTxt = '미입금';
        }else{
            depositTxt = '입금';
        }

        // 방청시간 시분 표기
        let workHour =''; //방청시간 분 표기 -> 시 표기
        if(parseInt(data.NB_WORK_TIME/60) > 0){
            workHour = parseInt(data.NB_WORK_TIME/60)+"시간 "+(data.NB_WORK_TIME%60)+"분 ";
        }else if(data.NB_WORK_TIME == null){
            workHour = '-';
        }else{
            workHour = (data.NB_WORK_TIME%60)+"분 ";
        }

        // 차주분입금여부
        let carownYN = '';
        if(data.NBI_CAROWN_DEPOSIT_YN == YN_Y){
            carownYN = '입금';
        }else if(data.NBI_CAROWN_DEPOSIT_YN == YN_N){
            carownYN = '미입금';
        }else{
            carownYN = '';
        }

        tableText += "<tr>";
        tableText += "<td class='text-center'><input type='checkbox' name='multi' id='"+data.NB_ID+"' value=''></td>";
        tableText += "<td class='text-center'>"+data.IDX+"</td>";
        tableText += "<td class='text-center'>"+data.MGG_ID+"</td>";
        tableText += "<td class='text-center'>"+data.NB_CLAIM_DT+"</td>";
        tableText += "<td class='text-left'>"+data.MII_NM+"</td>";
        tableText += "<td class='text-left'>"+data.MIT_NM+"</a></td>";
        tableText += "<td class='text-left'>"+data.NBI_TEAM_MEMO+"</td>";
        tableText += "<td class='text-center'>"+data.NBI_CHG_NM+"</td>";
        tableText += "<td class='text-center'>"+phoneFormat(data.NBI_FAX_NUM)+"</td>";
        tableText += "<td class='text-center'>"+phoneFormat(data.NIC_TEL1)+"</td>";
        tableText += "<td class='text-center'>"+data.CSD_NM+"</td>";
        tableText += "<td class='text-left'>"+data.RGN_NM+"</td>";
        tableText += "<td class='text-left'>"+data.MGG_NM+"</td>";
        tableText += "<td class='text-center'>"+bizNoFormat(data.MGG_BIZ_ID)+"</td>";
        tableText += "<td class='text-center'>"+data.RET_NM+"</td>";
        tableText += "<td class='text-center'>"+data.RPET_ID+"</td>";
        tableText += "<td class='text-center'>"+data.MGG_FRONTIER_NM+"</td>";
        tableText += "<td class='text-center'>"+data.MGG_BIZ_START_DT+"</td>";
        tableText += "<td class='text-center'>"+data.MGG_BIZ_END_DT+"</td>";
        tableText += "<td class='text-center'>"+data.NB_WRT_RMT+"</td>";
        tableText += "<td class='text-center'>"+data.NB_CLAIM_USERID+"</td>";
        tableText += "<td class='text-center'>"+data.INSU_BOTH_WRITER+"</td>";
        tableText += "<td class='text-center'>"+data.NBI_PRECLAIM_YN+"</td>";
        tableText += "<td class='text-center'>"+data.NBI_REGI_NUM+"</td>";
        tableText += "<td class='text-center'>"+data.RLT_NM+"</td>";
        tableText += "<td class='text-center'>"+data.NB_CAR_NUM+"</td>";
        tableText += "<td class='text-center'>"+data.CCC_CAR_NM+"</td>";
        tableText += "<td class='text-center'>"+data.RCT_NM+"</td>";
        tableText += "<td class='text-left'>"+data.BILL_PART_NMS+"</td>";
        tableText += "<td class='text-right'>"+workHour+"</td>";
        tableText += "<td class='text-right'>"+numberWithCommas(Number(data.NB_TOTAL_PRICE))+"</td>";
        tableText += "<td class='text-center'>"+data.NBI_INSU_DEPOSIT_DT+"</td>";
        tableText += "<td class='text-right'>"+numberWithCommas(Number(data.NBI_INSU_DEPOSIT_PRICE))+"</td>";
        tableText += "<td class='text-center'>"+depositTxt+"</td>";
        tableText += "<td class='text-center'>"+data.NB_DEL_DTHMS+"</td>";
        tableText += "<td class='text-center'>"+data.RFS_NM+"</td>";
        tableText += "<td class='text-center listbtn'><button type='button' class='btn btn-default' onclick='downloadImage("+index+");'>다운</button>";
        tableText += "<button type='button' class='btn btn-default' onclick='billPrint("+index+");'>인쇄</button></td>";
        tableText += "<td class='text-center'>"+data.NBI_FAX_SEND_DTHMS+"</td>";
        tableText += "<td class='text-right'>"+numberWithCommas(Number(data.NB_CAROWN_TOTAL_PRICE))+"</td>";
        tableText += "<td class='text-center'>"+data.NBI_CAROWN_DEPOSIT_DT+"</td>";
        tableText += "<td class='text-right'>"+numberWithCommas(Number(data.NBI_CAROWN_DEPOSIT_PRICE))+"</td>";
        tableText += "<td class='text-center'>"+carownYN+"</td>";
        tableText += "<td class='text-center'>"+data.NB_DEL_REASON+"</td>";
        tableText += "<td class='text-center listbtn'><button type='button' class='btn btn-primary' onclick='showRestore(0,\""+data.NB_ID+"\")'>복원</button></td>";
        tableText += "</tr>";
      
  });
  paging(totalListCount, currentPage, 'getList'); 
  $('#billBinContents').html(tableText);
}

/** 메인리스트 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
  const tableText = "<tr><td class='text-center' colspan='44'>조회요청한 데이터가 존재하지 않습니다.</td></tr>";
  $('.pagination').html('');
  $('#billBinContents').html(tableText);
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
        SEARCH_INSU_NM: $('#SEARCH_INSU_NM').val(),
        SEARCH_INSU_TEAM: $('#SEARCH_INSU_TEAM').val(),
        SEARCH_NIC_NM: $('#SEARCH_NIC_NM').val(),
        CSD_ID_SEARCH: $('#CSD_ID_SEARCH').val(),
        CRR_ID_SEARCH: $('#CRR_ID_SEARCH').val(),
        SELECT_CRR_ID: $('#SELECT_CRR_ID').val(),
        SEARCH_BIZ_NM: $('#SEARCH_BIZ_NM').val(),
        SEARCH_RET_ID: $('#SEARCH_RET_ID').val(),
        SEARCH_RMT_ID: $('#SEARCH_RMT_ID').val(),
        SEARCH_CLAIM_USERID: $('#SEARCH_CLAIM_USERID').val(),
        SEARCH_RLT_ID: $('#SEARCH_RLT_ID').val(),
        SEARCH_CAR_TYPE: $('#SEARCH_CAR_TYPE').val(),
        SEARCH_USE_COMP: $('#SEARCH_USE_COMP').val(),
        SEARCH_CAR_INFO: $('#SEARCH_CAR_INFO').val(),
        KEYWORD_CAR_INFO: $('#KEYWORD_CAR_INFO').val(),
        SEARCH_DEL_REASON: $('#SEARCH_DEL_REASON').val(),
    };

    callAjax(BILLBIN_URL, 'GET', data).then((result)=>{
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
let arrChk = [];
/**
 * 청구서 복원 확인
 * @param {String} type  일괄('all') or 단일('0') 
 * @param {String} id  청구서 아이디(NB_ID)
 */
showRestore = (type, id) => {
    let validation = true;
    if(type == ALL){
        arrChk = $("input[name=multi]:checked").map(function(){
            return $(this).attr('id');
        }).get();
        console.log('all selected>>' , arrChk.length);
        if(arrChk.length == 0 ){
          alert('일괄복원 할 청구서를 선택해 주세요.'); 
          validation =  false;
        }
    }else if(type != ALL && id == undefined){
        alert(ERROR_MSG_1400);
        validation =  false;
    }else{
        validation = true;
        
    }
    
    if(validation){
        confirmSwalTwoButton('청구서를 복원 하시겠습니까?','확인', '취소', false ,()=>updateData(type,id));
    }
}





/**
 * 청구서 복원
 * @param {String} type  일괄('all') or 단일('one') 
 * @param {String} id  청구서 아이디(NB_ID)
 */
updateData = (type,id) => {
    const data = {
        REQ_MODE: CASE_UPDATE,
        NB_ID : id,
        ALL_CHK: arrChk,
    }

    callAjax(BILLBIN_URL, 'POST', data).then((result)=>{
        const data = JSON.parse(result);
        if(data.status == OK){
            confirmText = '수정에 성공하였습니다.';
            confirmSwalOneButton(confirmText,'확인',()=>getList(CURR_PAGE))
        }else{
            alert("서버와의 연결이 원활하지 않습니다.");
        }   
    });
}

/**
 * 청구서 일괄삭제 확인
 * @param {String} type  일괄('all') or 단일('0') 
 * @param {String} id  청구서 아이디(NB_ID)
 */
showDelete = (type, id) => {
    let validation = true;
    if(type == ALL){
        arrChk = $("input[name=multi]:checked").map(function(){
            return $(this).attr('id');
        }).get();
        console.log('delete all selected>>' , arrChk.length);
        if(arrChk.length == 0 ){
          alert('완전삭제 할 청구서를 선택해 주세요.'); 
          validation =  false;
        }
    }else if(type != ALL && id == undefined){
        alert(ERROR_MSG_1400);
        validation =  false;
    }else{
        validation = true;
        
    }
    
    if(validation){
        confirmSwalTwoButton('청구서를 완전 삭제 하시겠습니까?','확인', '취소', true ,()=>delData(type,id), '삭제 후 복원힐 수 없습니다.');
    }
}

/* 삭제함수 */
function delData(type,id) {
    const data = {
        REQ_MODE: CASE_DELETE,
        NB_ID : id,
        ALL_CHK: arrChk,
    }
    callAjax(BILLBIN_URL,'POST', data).then((result)=>{
        const data = JSON.parse(result);
        if(data.status == OK){
            confirmSwalOneButton('삭제에 성공하였습니다.','확인',()=>{getList(1)});
        }else{
            const resultText ='서버와의 연결이 원활하지 않습니다.' ;
            alertSwal(resultText);
        } 
    });
  
  }

// 옵션불러오기


// /* 보험사Team 동적 생성 */
// setInsuTeam = () =>{
//   const data = {
//     REQ_MODE: GET_INSU_TEAM,
//   };
//   callAjax(BILLBIN_URL, 'GET', data).then((result)=>{
//       const data = JSON.parse(result);
//       if(data.status == OK){
//           const dataArray = JSON.parse(data.data); 
            
//           let optionText = '';
//           dataArray.map((item)=>{
//               optionText += '<option value="'+item.MIT_ID+'">'+item.MIT_NM+'</option>';
//           });
//           $('#SEARCH_INSU_TEAM').html(optionText);
//           $('#SEARCH_INSU_TEAM').selectpicker('refresh');
        
//       }
//   });
// }






  
  
  
  /* CAR TYPE SELECT */
  setCarType = () => {
      const data = {
          REQ_MODE: GET_CAR_TYPE
      };
      let headerTxt = '';
      callAjax(BILLBIN_URL, 'GET', data).then((result)=>{
          console.log('table_hedder >>',result)
          const res = JSON.parse(result);
          if(res.status == OK){
              const item = res.data;
              $("#SEARCH_CAR_TYPE option").remove();  // thead 하위 <tr>개체 초기화
              if(item.length > 0){
                  item.map((item, i)=>{
                      headerTxt += '<option value="'+item.RCT_CD+'">'+item.RCT_NM+'</option>';
                  })
  
                  $("#SEARCH_CAR_TYPE").html('<option value="">=차량급=</option>'+headerTxt);  // <SELECT> 객체에 반영
  
                  
              
              }
          }
      });
  }
  
  
  
  /* 사용부품 */
  setPart = () => {
      const data = {
          REQ_MODE: GET_PARTS_TYPE
      };
      let headerTxt = '';
      callAjax(BILLBIN_URL, 'GET', data).then((result)=>{
          // console.log('table_hedder >>',result)
          const res = JSON.parse(result);
          if(res.status == OK){
              const item = res.data;
              $("#SEARCH_USE_COMP option").remove();  // thead 하위 <tr>개체 초기화
              if(item.length > 0){
                  item.map((item, i)=>{
                      headerTxt += '<option value="'+item.CPPS_NM_SHORT+'">'+item.CPP_NM_SHORT+'-'+item.CPPS_NM+'</option>';
                  })
                  $("#SEARCH_USE_COMP").html('<option value="">=사용부품=</option>'+headerTxt);  // <SELECT> 객체에 반영            
              }
          }
      });
  }
  
  
/**엑셀다운 */
excelDownload = () => {
    location.href="billBinExcel.html?"+$("#searchForm").serialize()+"&REQ_MODE="+CASE_TOTAL_LIST;
}