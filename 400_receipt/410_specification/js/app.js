const TRANS_URL = 'act/specification_act.php';
let CURR_PAGE = 1; 
let tdLength = 0 ;
$(document).ready(function() {
    /* 검색조건 */
    $( "#DT_KEY_SEARCH" ).datepicker(
        getDatePickerSetting()
    );
    $( "#START_DT_SEARCH").datepicker(
        getDatePickerSetting('END_DT_SEARCH', 'minDate')
    );
    $( "#END_DT_SEARCH").datepicker(
        getDatePickerSetting('START_DT_SEARCH', 'maxDate')
    );
    getDivisionOption('CSD_ID_SEARCH', true);                                      //지역구분
    setCRR_ID_Option('CSD_ID_SEARCH', 'CRR_ID_SEARCH', true);
    getRefOptions(GET_REF_EXP_TYPE, 'RET_ID_SEARCH', true, false);                 //청구식
    setGwOptions('MGG_NM_SEARCH');
    getRefOptions(GET_REF_CONTRACT_TYPE, 'RCT_ID_SEARCH', false, false);           // 자가여부 

    $('#YEAR_SEARCH').change(function() {
        $('#START_DT_SEARCH').val($('#YEAR_SEARCH').val()+'-01-01');
        $('#END_DT_SEARCH').val($('#YEAR_SEARCH').val()+'-12-31');
    });

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
  
    // /** 검색에서 기간을 눌렀을 때 날짜 초기화 */
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

    tdLength = $('table tr th').length;
    setInitTableData(tdLength);

    /** 검색 */
    $("#searchForm").submit(function(e){
        e.preventDefault();
            getList(1);
    });

    // 이미지확대보기
    $("#receipt-img").click(function(){ 
        $(".imgZoom").css("display","block")
    })
    
    $("#zoom-close").click(function(){
        $(".imgZoom").css("display","none")
    })
});


getList = (currentPage) =>{
    CURR_PAGE = (currentPage==undefined) ? CURR_PAGE : currentPage;
    const param = {
        REQ_MODE: CASE_LIST,
        PER_PAGE:  $("#perPage").val(),
        CURR_PAGE:  CURR_PAGE,

        /** 검색 */
        DT_KEY_SEARCH: $('#DT_KEY_SEARCH').val(),
        START_DT_SEARCH: $('#START_DT_SEARCH').val(),
        END_DT_SEARCH: $('#END_DT_SEARCH').val(),
        RSF_ID_SEARCH: $('#RSF_ID_SEARCH').val(),
        CSD_ID_SEARCH: $('#CSD_ID_SEARCH').val(),
        CRR_ID_SEARCH: $('#CRR_ID_SEARCH').val(),
        MGG_NM_SEARCH: $('#MGG_NM_SEARCH').val(),
        RCT_ID_SEARCH: $('#RCT_ID_SEARCH').val(),
        RET_ID_SEARCH: $('#RET_ID_SEARCH').val(),
        NSU_PROC_YN_SEARCH: $('#NSU_PROC_YN_SEARCH').val(),
        NSU_TAKE_USERID_SEARCH: $('#NSU_TAKE_USERID_SEARCH').val(),
        NSU_PROC_USERID_SEARCH: $('#NSU_PROC_USERID_SEARCH').val()
    }
    // console.log('getList param >>>',param);
    callAjax(TRANS_URL, 'GET', param).then((result)=>{
        // console.log('getList RESULT>',result);
        const res = JSON.parse(result);
        // console.log('getList res>',res);
        if(res.status == OK){
          $('#indicator').hide();
            const item = res.data[0];
            let tableTxt = '';
            item.map((data, i)=>{
                let procUser = '-';
                let procbtn = '';
                if(data.NSU_PROC_USERID != ''){
                  procUser = data.PROC_USER_NAME+" ("+data.NSU_PROC_USERID+")" ;
                }
                if(data.NSU_PROC_YN == YN_N){
                  procbtn ="<button type='button' class='btn btn-default' id='btnProc' onclick='proc(\""+data.NSU_ID+"\",\""+data.MGG_NM+"\")'>미처리</button>";
                }else {
                  procbtn = '처리완료';
                }
                tableTxt += "<tr id='tr_"+data.MGG_ID+"'>";
                tableTxt += "<td class='text-center'>"+data.IDX+"</td>";
                tableTxt += "<td class='text-center'>"+data.CSD_NM+"</td>";
                tableTxt += "<td class='text-left'>"+data.CRR_NM+"</td>";
                tableTxt += "<td class='text-left'>"+data.MGG_NM+"</td>";
                tableTxt += "<td class='text-center'>"+data.RET_NM+"</td>";
                tableTxt += "<td class='text-center'>"+data.RCT_NM+"</td>";
                tableTxt += "<td class='text-center'>"+data.NSU_TAKE_DTHMS+"</td>";
                tableTxt += "<td class='text-center'>"+(data.NSU_TAKE_USERID != null ? data.TAKE_USER_NAME+" ("+data.NSU_TAKE_USERID+")":"-")+"</td>";
                tableTxt += "<td class='text-center'>"+procbtn+"</td>";
                tableTxt += "<td class='text-center'>"+procUser+"</td>";
                tableTxt += "<td class='text-center'>"+data.RSF_ID+"</td>";
                tableTxt += "<td class='text-center'>"+data.NSU_PROC_DTHMS+"</td>";
                if($("#AUTH_UPDATE").val() == YN_Y){
                    tableTxt += "<td><button type='button' class='btn btn-default' onclick='openWindowPopup(\""+data.MGG_ID+"\",\""+data.MGG_NM+"\",\""+data.NSU_FILE_IMG+"\")'>수정</button></td>";
                }
                tableTxt += "</tr>";
            })
            paging(res.data[1], CURR_PAGE, 'getList'); 
            $("#dataTable").html(tableTxt);
        }else{
            setHaveNoDataTable();
            // alert(ERROR_MSG_NO_DATA);
        }
    });

}

/** 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
    const tableText = "<tr><td class='text-center' colspan='"+tdLength+"'>조회요청한 데이터가 존재하지 않습니다.</td></tr>";
    $('.pagination').html('');
    $('#dataTable').html(tableText);
}



openWindowPopup = (id, nm, img) => {
    const formData = $('#transForm');
    window.open('', 
              'newwindow', 
              'width=1750,height=1000');
    $('#TRANS_MGG_ID').val(id);
    $('#TRANS_MGG_NM').val(nm);
    $('#TRANS_IMG').val(img);
    formData.submit();
}


imgZoomPopup = () => {
    console.log('img>>>',$('img').attr('src'));
    const formData = $('#transImgForm');
    window.open('', 
    'newwindowImg', 
    'width=750,height=800');
    $('#POPUP_IMG').val($('img').attr('src'));
    formData.submit();

}


/* 엑셀다운로드 */
excelDownload = () => {
    location.href="specificationExcel.html?"+$("#searchForm").serialize();
}


/* 처리 */
proc = (id, nm) => {
    confirmSwalTwoButton(nm+'공업사의 거래명세서 처리를 완료 하시겠습니까?','확인', '취소', false ,()=>{
      const param ={
        REQ_MODE: CASE_UPDATE_TEAM,
        NSU_ID: id
      }
      callAjax(TRANS_URL, 'POST', param).then((result)=>{
          const data = JSON.parse(result);
          if(data.status == OK){
              alert('처리되었습니다.');
              getList();
          }else{
              alert(ERROR_MSG_1100);
          } 
      });
    });
}