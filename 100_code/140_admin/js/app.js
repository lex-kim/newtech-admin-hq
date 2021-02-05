const ADMIN_URL =  'act/admin_act.php';
let CURR_PAGE = 1;

$(document).ready(() =>{
    $('#indicator').hide();
    
    const tdLength = $('table tr th').length;
    setInitTableData(tdLength,'admin_contents');

    $("#searchForm").submit(function(e){
        e.preventDefault();
        getList(1);
    });
});



/**
 * setAdminTable 테이블 동적 생성
 * @param {int} totalListCount 전체조회 수
 * @param {array} dataArray 동적으로 뿌려줄 데이터
 * @param {int} currentPage 조회하는 현재 페이지 수
 */
setAdminTable = (totalListCount, dataArray, currentPage) => {
    let tableText = '';

    dataArray.map((data, index)=>{
        tableText += "<tr>";
        tableText += " <td class='text-center'>"+data.IDX+"</td>";  
        tableText += " <td class='text-center'>"+data.ASA_USERID+"</td>";
        tableText += " <td class='text-center'>"+data.ASA_NM+"</td>";
        tableText += " <td class='text-center'>"+data.WRT_DTHMS+"</td>";
        tableText += " <td class='text-center'>"+data.WRT_USERID+"</td>";
        if($('#UPDATE_TABLE').is(":visible")){
            tableText += "<td class='text-center'>";
            tableText += "<button type='button' class='btn btn-default' onclick='showUpdateModal("+JSON.stringify(data)+")'>수정</button>";
            tableText += "</td>";
        }
 
        tableText += "</tr>";

    });
    paging(totalListCount, currentPage, 'getList'); 
    $('#admin_contents').html(tableText);
}

/* 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
    const tableText = "<tr><td class='text-center' colspan='5'>조회요청한 데이터가 존재하지 않습니다.</td></tr>";
    $('.pagination').html('');
    $('#admin_contents').html(tableText);
}


/** 
 * main 리스트
 * @param {int} currentPage 현재 페에지수 
 * */
getList = (currentPage) => {
    CURR_PAGE = (currentPage == undefined) ? CURR_PAGE : currentPage;

    const data = {
        REQ_MODE: CASE_LIST,
        PER_PAGE:  $("#perPage").val(),
        CURR_PAGE:  CURR_PAGE,
        SEARCH_ASA_USERID: $('#SEARCH_ASA_USERID').val()
    };

    callAjax(ADMIN_URL, 'GET', data).then((result)=>{       
        const res = JSON.parse(result);
        console.log('getList >>>',res);
        if(res.status == OK){
            const item = res.data;
            setAdminTable(item[1], item[0], CURR_PAGE);
        }else{
            setHaveNoDataTable();
        }
    });
}

/* 등록창 초기화 함수  */
resetData = () => {
    $("#REQ_MODE").val(CASE_CREATE);
    $("#ASA_USERID").val("");
    $("#ASA_USERID").attr("readonly", false);
    $("#ASA_NM").val("");
    $('#ASA_PASSWD').val("");
    $('#ASA_PASSWD').attr('placeholder','입력');
    $("#DIV_PASSWD_CH").show();
    $('#ASA_PASSWD_CHECK').val("");
    $('#submitBtn').html('등록');
    $('#submitBtn').attr('onclick', 'addData()');
    $("#btnDelete").hide();
   
    $("#add-new").modal();
}

/* 등록함수 */
addData = () => {
    /* validation */
    if($('#ASA_USERID').val().trim()==''){
        alert("관리자ID를 입력해주세요.");
        $('#ASA_USERID').focus();
        return false;
    }else if($('#ASA_NM').val().trim()==''){
        alert("관리자명을 입력해주세요.");
        $('#ASA_NM').focus();
        return false;
    }else if($('#ASA_PASSWD').val().trim()==''){
        alert("비밀번호를 입력해주세요.");
        $('#ASA_PASSWD').focus();
        return false;
    }else if($('#ASA_PASSWD_CHECK').val().trim()==''){
        alert("비밀번호 확인을 입력해주세요.");
        $('#ASA_PASSWD_CHECK').focus();
        return false;
    }else  if($('#ASA_PASSWD').val().trim() != $('#ASA_PASSWD_CHECK').val().trim()){
        alert("비밀번호가 일치하지 않습니다.");
        $("#ASA_PASSWD_CHECK").val("");
        $("#ASA_PASSWD_CHECK").focus();
        return  false;
    }

    const data = {
        REQ_MODE: CASE_CREATE,
        ASA_USERID: $("#ASA_USERID").val(),
        ASA_NM: $("#ASA_NM").val(),
        ASA_PASSWD: $("#ASA_PASSWD").val(),
    };
    callAjax(ADMIN_URL, 'POST', data).then((result)=>{
        console.log('add>>',result);
        const data = JSON.parse(result);
        if(data.status == OK){
            confirmSwalOneButton('등록에 성공하였습니다.','확인',()=>{$("#add-new").modal("toggle"); getList();});

        }else if(data.status == ERROR){
            alert("존재하는 아이디가 있습니다.");
            $('#ASA_USERID').focus();
        }else{
            alert("서버와의 연결이 원활하지 않습니다.");
        } 
    });

}
  

/**
 * 리스트에서 선택항 행의 상세 데이터 
 * @param {array} p_item 선택된 row 전체 데이터
 */
showUpdateModal = (p_item) => {
    // console.log(p_item);
    $("#REQ_MODE").val(CASE_UPDATE);
    $("#ASA_NM").val(p_item.ASA_NM);
    $("#ASA_ID").val(p_item.ASA_ID);
    $("#ASA_USERID").val(p_item.ASA_USERID);
    $("#ASA_USERID").attr("readonly", true);
    $('#ASA_PASSWD').val("");
    $('#ASA_PASSWD').attr('placeholder','비밀번호 입력시 비밀번호가 변경됩니다.');
    $("#DIV_PASSWD_CH").hide();
    $('#submitBtn').attr('onclick', 'updateData()');
    $('#submitBtn').html('수정');
    $("#btnDelete").show();
   
    $("#add-new").modal();
}

/* 업데이트 */
updateData = () =>{
    if(validationData()){
        confirmSwalTwoButton('수정 하시겠습니까?','확인', '취소', false ,()=>{
            /* validation */
            if($('#ASA_NM').val().trim()==''){
                alert("관리자명을 입력해주세요.");
                $('#ASA_NM').focus();
                return false;
            }
            const data = {
                REQ_MODE: CASE_UPDATE,
                ASA_ID: $("#ASA_ID").val(),
                ASA_NM: $("#ASA_NM").val(),
                ASA_USERID: $("#ASA_USERID").val(),
                ASA_PASSWD: $("#ASA_PASSWD").val(),
            };
            callAjax(ADMIN_URL, 'POST', data).then((result)=>{
                const data = JSON.parse(result);
                if(data.status == OK){
                    confirmSwalOneButton('수정에 성공하였습니다.','확인',()=>{$("#add-new").modal("toggle"); getList();});
                }else if(data.status == ERROR){
                    alert(data.message);
                }else{
                    alert("서버와의 연결이 원활하지 않습니다.");
                }   
            });
        });
    }
}
  
/* 리스트에서 선택한 데이터 삭제 */
delData = () => {
    const data = {
        REQ_MODE: CASE_DELETE,
        ASA_USERID : $('#ASA_USERID').val(),
    };
    callAjax(ADMIN_URL, 'POST', data).then((result)=>{
        const data = JSON.parse(result);
        if(data.status == OK){
            getList();
            $("#add-new").modal("toggle");
        }else{
            const resultText ='서버와의 연결이 원활하지 않습니다.' ;
            alert(resultText);
        } 
    });
}

/* 엑셀 다운로드 */
excelDownload = () => {
    location.href="adminExcel.html?"+$("#searchForm").serialize()+"&REQ_MODE="+CASE_TOTAL_LIST;
}


/** 수정상태에서 비밀번호 입력시 비밀번호 변경됨을 알려주고 값이 변경 되면 비밀번호 확인창을 보여준다. */
changePassword = () => {
    if($("#REQ_MODE").val() == CASE_UPDATE){
        if($('#ASA_PASSWD').val().trim()==''){
            $('#DIV_PASSWD_CH').hide();
            $('#ASA_PASSWD_CHECK').prop('required',false);
        }else{
            $('#DIV_PASSWD_CH').show();
            $('#ASA_PASSWD_CHECK').prop('required',true);
        }
    }
}


/** 모달에서 데이터들이 이상이 없는지 체크 */
validationData = () => {
    /** 비밀번호 확인창이 보였을 경우에만 패스워드 확인 하는걸로. */
    if($('#ASA_PASSWD_CHECK').is(':visible') && !isValidatePassword()){
        $('#ASA_PASSWD_CHECK').focus();
    }else{
        return true;
    }
}

isValidatePassword = () => {
    if($('#ASA_PASSWD').val().trim() == $('#ASA_PASSWD_CHECK').val().trim()){
        return  true;
    }else{
        alert("비밀번호가 일치하지 않습니다.");
        return  false;
    }
}