const REGION_URL = 'act/region_act.php';
let CURR_PAGE = 1;

$(document).ready(() =>{
    /* 검색조건 옵션 넣기 */
    getDivisionOptions('SEARCH_DIVISION','=구분=',OPTIONS_DIVISION);
    setSearchArea('SEARCH_AREA');

    setInitTableData(8, 'region_contents');
    
    $("#searchForm").submit(function(e){
        e.preventDefault();
        getList(1);
        $('#SEARCH_AREA').val($('#SEARCH_AREA').val().trimEnd());
    });

   
});

/**
 * setRegionTable 테이블 동적 생성
 * @param {int} totalListCount 전체조회 수
 * @param {array} dataArray 동적으로 뿌려줄 데이터
 * @param {int} currentPage 조회하는 현재 페이지 수
 */
setRegionTable = (totalListCount, dataArray, currentPage) => {
    let tableText = '';

    dataArray.map((data, index)=>{
        tableText += "<tr>";
        tableText += " <td class='text-center'>"+data.CSD_IDX+"</td>";  
        tableText += " <td class='text-center'>"+(data.CRR_1_ID+"-"+data.CRR_2_ID+"-"+data.CRR_3_ID)+"</td>";
        tableText += " <td class='text-center'>"+data.CSD_NM+"</td>";
        // tableText += " <td class='text-center'>"+data.CRR_2_NM+"</td>";
        // tableText += " <td class='text-center'>"+data.CRR_3_NM+"</td>";
        tableText += " <td class='text-left'>"+data.CRR_NM+"</td>";
        tableText += " <td class='text-center'>"+data.CO_CNT+"</td>";
        tableText += " <td class='text-center'>"+data.LAST_DTHMS+"</td>";
        tableText += " <td class='text-center'>"+data.WRT_USERID+"</td>";
        if($('#UPDATE_TABLE').is(":visible")){
            tableText += "<td class='text-center'>";
            tableText += "<button type='button' class='btn btn-default' onclick='showUpdateModal("+JSON.stringify(data)+")'>수정</button>";
            tableText += "</td>";
        }
        // tableText += " <td class='text-center'><button type='button' class='btn btn-default' onclick='showUpdateModal("+JSON.stringify(data)+")'>수정</button></td>";
        tableText += "</tr>";
        
    });
    
    paging(totalListCount, currentPage, 'getList'); 
    
    $('#region_contents').html(tableText);
    $('#indicator').hide();
}

/** 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
    const tableText = "<tr><td class='text-center' colspan='8'>조회요청한 데이터가 존재하지 않습니다.</td></tr>";
    $('.pagination').html('');
    $('#region_contents').html(tableText);
    $('#indicator').hide();
}


/** 
 * 지역관리 리스트
 * @param {int} currentPage 현재 페에지수 
 * */
getList = (currentPage) => {
    CURR_PAGE = (currentPage == undefined) ? CURR_PAGE : currentPage;
    const data = {
        REQ_MODE: CASE_LIST,
        PER_PAGE:  $("#perPage").val(),
        CURR_PAGE:  CURR_PAGE,
        DIVISION: $('#SEARCH_DIVISION').val(),
        STR_AREA: $('#SEARCH_AREA').val()
    };

    callAjax(REGION_URL, 'GET', data).then((result)=>{       
        const res = JSON.parse(result);
        console.log('getList >>>',res);
        if(res.status == OK){
            const item = res.data;
            $('#SEARCH_AREA').blur();
            $('#SEARCH_AREA').autocomplete('close');
            
            setRegionTable(item[1], item[0], CURR_PAGE);
        }else{
            setHaveNoDataTable();
        }
    });
}


/**
 * 리스트에서 선택항 행의 상세 데이터 
 * @param {array} p_item 선택된 row 전체 데이터
 */
showUpdateModal = (p_item) => {

    // console.log(p_item);
    p_csdId = fillZero(p_item.CSD_ID, 3);
    getDivisionOptions('SELECT_DIVISION','==구분==',OPTIONS_DIVISION, p_csdId);
    // $("#SELECT_DIVISION").val(p_item.CSD_ID);
   
    $("#REQ_MODE").val(CASE_UPDATE);
    $("#CRR_1_ID").val(p_item.CRR_1_ID);
    // $("#CRR_1_NM").val(p_item.CRR_1_NM);
    $("#CRR_NM").val(p_item.CRR_NM);
    $("#CRR_NM").attr({"readonly": true});
    // $("#CRR_1_NM").attr({"readonly": true});
    $("#CRR_2_ID").val(p_item.CRR_2_ID);
    // $("#CRR_2_NM").val(p_item.CRR_2_NM);
    // $("#CRR_2_NM").attr({"readonly": true});
    $("#CRR_3_ID").val(p_item.CRR_3_ID);
    // $("#CRR_3_NM").val(p_item.CRR_3_NM);
    // $("#CRR_3_NM").attr({"readonly": true});
    $("#CRR_2_ALL").val('N');
    $("#CRR_3_ALL").val('N');
    $("#btnDelete").show();
    $('#submitBtn').attr('onclick', 'updateData()');

   
    $("#add-new").modal();
}
  

updateData = () =>{
    alertSwal($("#SELECT_DIVISION").val());
    let num_check=/^\d{3}$/;
     if(!num_check.test($("#SELECT_DIVISION").val()) || $("#SELECT_DIVISION").val() == '') {
        alertSwal('구분은 필수로 입력입니다.');
        $("#SELECT_DIVISION").focus();
    }else {
        if($("#CRR_2_ID").val() == '000' || $("#CRR_3_ID").val() == '00000'){
            confirmSwalTwoButton(
                '하위지역에도 동일한 구분을 지정하시겠습니까?',
                '확인',
                '진행',
                false,
                ()=>{
                    if($("#CRR_2_ID").val() == '000'){
                        console.log('2222');
                        $("#CRR_2_ALL").val('Y');
                    }else if($("#CRR_3_ID").val() == '00000') {
                        console.log('333');
                        $("#CRR_3_ALL").val('Y');
                    }
                    setData();
                },
                '',
                ()=>{
                    setData();
                }
            );
        }
        else {
            setData();
        } 
    } 

}

setData = () =>{
    const data = {
        REQ_MODE: CASE_UPDATE,
        SELECT_DIVISION: $("#SELECT_DIVISION").val(),
        CRR_1_ID: $("#CRR_1_ID").val(),
        CRR_1_NM: $("#CRR_1_NM").val(),
        CRR_2_ID: $("#CRR_2_ID").val(),
        CRR_2_NM: $("#CRR_2_NM").val(),
        CRR_3_ID: $("#CRR_3_ID").val(),
        CRR_3_NM: $("#CRR_3_NM").val(),
        CRR_2_ALL: $("#CRR_2_ALL").val(),
        CRR_3_ALL: $("#CRR_3_ALL").val(),
    };
    console.log('data >> ',data);
    callAjax(REGION_URL, 'POST', data).then((result)=>{
        const data = JSON.parse(result);
        if(data.status == OK){
            confirmSwalOneButton('수정에 성공하였습니다.','확인',()=>{$('#add-new').modal('toggle'); getList();});
        }else if(data.status == ERROR){
            alertSwal(data.message);
            $('#CPP_ID').focus();
        }else{
            alertSwal("서버와의 연결이 원활하지 않습니다.");
        }   
    });
}

/* 데이터 삭제함수 */
function delData() {
    const data = {
        REQ_MODE: CASE_DELETE,
        CRR_1_ID: $("#CRR_1_ID").val(),
        CRR_2_ID: $("#CRR_2_ID").val(),
        CRR_3_ID: $("#CRR_3_ID").val(),
    };
    callAjax(REGION_URL, 'POST', data).then((result)=>{
        const data = JSON.parse(result);
        if(data.status == OK){
            confirmSwalOneButton(
                '삭제에 성공하였습니다.',
                '확인',
                ()=>{
                getList();
                $("#add-new").modal("hide");
                }
            );
        }else{
            const resultText ='서버와의 연결이 원활하지 않습니다.' ;
            alertSwal(resultText);
        } 
    });
}


getTotalList = () => {
    const toURL="regionExcel.html?" +
        "DIVISION=" + $('#SEARCH_DIVISION').val() +
        "&STR_AREA=" + $('#SEARCH_AREA').val();
    console.log("toURL ==>", toURL);
    location.href=toURL;
}