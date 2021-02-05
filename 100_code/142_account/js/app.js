let modalType = '';      // 모달이 수정인지 등록인지
let authorityOptionTextArray = [];
let selectId = '';
const pageName = "account.html";
const ajaxUrl = 'act/account_act.php';
let CURR_PAGE = 1;

$(document).ready(() =>{
    $('#indicator').hide();
    setAuthoritySelect();

    const tdLength = $('table tr th').length;
    setInitTableData(tdLength);

    $("#searchForm").submit(function(e){
        e.preventDefault();
        getList(1);
    });


    $('#add-new').on('hide.bs.modal', function (event) {
        selectId = '';
        $('#ALLM_ID').html("");
    });
});


isValidatePhone = () => {
    if(!validatePhone($('#AUU_PHONE_NUM').val().trim())){
        alert("번호양식에 맞지 않습니다. 다시입력해주세요.");
        return false;
    }else{
        return  true;
    }
}

isValidatePassword = () => {
    if($('#AUU_PASSWD').val().trim() == $('#AUU_PASSWD_CHECK').val().trim()){
        return  true;
    }else{
        alert("비밀번호가 일치하지 않습니다.");
        return  false;
    }
}

isValidateEmail = (email) => {
    if(!validateEmail($('#AUU_EMAIL').val().trim())){
        alert("이메일 양식이 올바르지 않습니다.");
        return false;
    }else{
        return true;
    }
}

/** 권한 리스트 가져온 */
setAuthoritySelect = () => {
    const data = {
        REQ_MODE: GET_AUTHORITY,
    };
    callAjax(ajaxUrl, 'GET', data).then((result)=>{
        const data = JSON.parse(result);
        
        if(data.status == OK){
            const dataArray = data.data;
            let optionText = '<option value="">=권한=</option>';
    
            dataArray.map((item, index)=>{
                optionText += '<option value="'+item.ALLM_ID+'">'+item.ALLM_NM+'</option>';
            });
    
            authorityOptionTextArray = optionText;

            $('#ALLM_ID_SEARCH').html(optionText);
        }    
    });
}

/** 수정상태에서 비밀번호 입력시 비밀번호 변경됨을 알려주고 값이 변경 되면 비밀번호 확인창을 보여준다. */
changePassword = () => {
    if(modalType == CASE_UPDATE){
        if($('#AUU_PASSWD').val().trim()==''){
            $('#AUU_PASSWD_CHECK_DIV').hide();
            $('#AUU_PASSWD_CHECK').prop('required',false);
        }else{
            $('#AUU_PASSWD_CHECK_DIV').show();
            $('#AUU_PASSWD_CHECK').prop('required',true);
        }
    }
}
/** 업데이트 모달창일경우 */
showUpdateModal = (accountItem) => {
    $('#ALLM_ID').html(authorityOptionTextArray);
    modalType = CASE_UPDATE;
    selectId = accountItem.AUU_ID;

    $('#btnDelete').val(accountItem.ALLM_ID);
    $('#ALLM_ID').val(accountItem.ALLM_ID);
    $('#AUU_LVL_NM').val(accountItem.AUU_LVL_NM);
    $('#AUU_NM').val(accountItem.AUU_NM);
    $('#AUU_NM').val(accountItem.AUU_NM);
    $('#AUU_USERID').val(accountItem.AUU_USERID);
    $('#AUU_EMAIL').val(accountItem.AUU_EMAIL);
    $('#AUU_PHONE_NUM').val(accountItem.AUU_PHONE_NUM);
    $('#AUU_PASSWD').val('');
    $('#AUU_PASSWD_CHECK_DIV').hide();
    $('#AUU_PASSWD_CHECK').prop('required',false);
    $('#AUU_PASSWD').prop('required',false);

    $('#AUU_PASSWD').attr('placeholder','비밀번호 입력시 비밀번호가 변경됩니다.');
    $('#btnDelete').show();
    $('#submitBtn').html('수정');
    $('#submitBtn').attr('onclick','confirmUpdate()');
    $('#add-new').modal();
}


confirmUpdate=()=>{
    confirmSwalTwoButton('수정 하시겠습니까?','확인', '취소', false ,()=>updateData());
}
confirmAdd=()=>{
    confirmSwalTwoButton('저장 하시겠습니까?','확인', '취소', false ,()=>addData());
}
/** 등록모달창일경우 */
showAddModal = () => {
    $('#btnDelete').hide();
    modalType = CASE_CREATE;
    
    $('#ALLM_ID').append(authorityOptionTextArray);
    $('#ALLM_ID').val('');
    $('#AUU_LVL_NM').val('');
    $('#AUU_NM').val('');
    $('#AUU_NM').val('');
    $('#AUU_USERID').val('');
    $('#AUU_EMAIL').val('');
    $('#AUU_PHONE_NUM').val('');
    $('#submitBtn').html('등록');
    $('#submitBtn').attr('onclick','confirmAdd()');
    $('#AUU_PASSWD').attr('placeholder','입력');
    $('#AUU_PASSWD_CHECK_DIV').show();
    $('#AUU_PASSWD_CHECK').prop('required',true);
    $('#AUU_PASSWD').prop('required',true);
    $('#add-new').modal();
}


/** 리스트 테이블 동적으로 생성 
 * @param {dataArray : 리스트 데이터, currentPage : 현재페이지(페이징을 위해)} 
*/
setTable = (totalListCount, dataArray, currentPage) => {
    let tableText = '';

    dataArray.map((data, index)=>{
        tableText += "<tr>";
        tableText += "<td class='text-center'>"+data.IDX+"</td>";
        tableText += "<td class='text-center'>"+data.ALLM_NM+"</td>";
        tableText += "<td class='text-center'>"+data.AUU_LVL_NM+"</td>";
        tableText += "<td class='text-center'>"+data.AUU_NM+"</td>";
        tableText += "<td class='text-center'>"+data.AUU_USERID+"</td>";
        tableText += "<td class='text-center'>"+data.AUU_EMAIL+"</td>";
        tableText += "<td class='text-center'>"+phoneFormat(data.AUU_PHONE_NUM)+"</td>";
        tableText += "<td class='text-center'>"+data.WRT_DTHMS+"</td>";
        tableText += "<td class='text-center'>"+data.WRT_USERID+"</td>";
        if($('#UPDATE_TABLE').is(":visible")){
            tableText += "<td class='text-center'>";
            tableText += "<button type='button' class='btn btn-default' onclick='showUpdateModal("+JSON.stringify(data)+")'>수정</button>";
            tableText += "</td>";
        }

        tableText += "</tr>"
        
    });
    paging(totalListCount, currentPage, 'getList'); 
    $('#dataTable').html(tableText);
}

/** 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
    const tableText = "<tr><td class='text-center' colspan='9'>조회요청한 데이터가 존재하지 않습니다.</td></tr>";
    $('.pagination').html('');
    $('#dataTable').html(tableText);
}

/**권한관리 리스트 */
getList = (currentPage) => {
    CURR_PAGE = (currentPage == undefined) ? CURR_PAGE : currentPage;

    const data = {
        REQ_MODE: CASE_LIST,
        PER_PAGE:  $("#perPage").val(),
        CURR_PAGE:  CURR_PAGE,

        /** 검색 */
        ALLM_ID_SEARCH : $('#ALLM_ID_SEARCH').val(),
        ACCOUNT_KEYWORD : $('#ACCOUNT_KEYWORD').val(),
        SEARCH_ACCOUNT : $('#SEARCH_ACCOUNT').val(),

    };
    callAjax(ajaxUrl, 'GET', data).then((result)=>{
        const data = JSON.parse(result);
        console.log(data);
        if(data.status == OK){
            const item = data.data;
            setTable(item[1], item[0], CURR_PAGE);
        }else{
            setHaveNoDataTable();
        }
    });
    
}

excelDownload = () => {
    location.href="accountExcel.html?"+$("#searchForm").serialize()+"&REQ_MODE="+CASE_TOTAL_LIST
}


/** 모달에서 데이터들이 이상이 없는지 체크 */
validationData = () => {
    /** 비밀번호 확인창이 보였을 경우에만 패스워드 확인 하는걸로. */
    if($('#AUU_PASSWD_CHECK_DIV').is(':visible') && !isValidatePassword()){
        $('#AUU_PASSWD_CHECK').focus();
    }else if(!isValidateEmail()){
        $('#AUU_EMAIL').focus();
    }else if(!isValidatePhone()){
        $('#AUU_PHONE_NUM').focus();
    }else{
        return true;
    }
}

/** 권한관리 등록버튼 눌렀을 때 */
addData = () => {
    if(validationData()){
        let form = $("#addForm")[0];
        let formData = new FormData(form);
        formData.append('REQ_MODE', modalType);

        callFormAjax(ajaxUrl, 'POST', formData).then((result)=>{
            const data = JSON.parse(result);
            if(data.status == OK){
                confirmSwalOneButton('등록에 성공하였습니다.','확인',()=>{
                    getList(1);
                    $('#add-new').modal('toggle');
                });
            }else if(data.status == ERROR){
                confirmSwalOneButton(data.message,'확인', ()=>$('#AUU_USERID').focus());
            }else{
                alert("서버와의 연결이 원활하지 않습니다.");
            } 
        });     
    }
}

/** 권한관리 업데이트 */
updateData = () => {
    if(validationData()){
        let form = $("#addForm")[0];
        let formData = new FormData(form);
        formData.append('REQ_MODE', CASE_UPDATE);
        formData.append('AUU_ID', selectId);

        callFormAjax(ajaxUrl, 'POST', formData).then((result)=>{
            const data = JSON.parse(result);
             if(data.status == OK){
                confirmSwalOneButton('수정에 성공하였습니다.','확인',()=>{
                    $('#add-new').modal('toggle');
                    getList();
                });
            }else if(data.status == ERROR){
                confirmSwalOneButton(data.message,'확인', ()=>$('#AUU_USERID').focus());
            }else{
                alert("서버와의 연결이 원활하지 않습니다.");
            }  
        });
    }
}

delData = () => {
    const data = {
        REQ_MODE: CASE_DELETE,
        AUU_ID : selectId,
    };
    callAjax(ajaxUrl, 'POST', data).then((result)=>{
        const data = JSON.parse(result);
        if(data.status == OK){
            confirmSwalOneButton('삭제에 성공하였습니다.','확인',()=>{
                // location.replace(pageName);
                $('#add-new').modal('toggle');
                getList();
            });
        }else{
            const resultText ='서버와의 연결이 원활하지 않습니다.' ;
            alert(resultText);
        }  
    });
}

