let carTypeOptionArray = [];
const pageName = "carmngt.html";
let CURR_PAGE = 1;

$(document).ready(() =>{
    $('#indicator').hide();
    const tdLength = $('table tr th').length;
    setInitTableData(tdLength);

    setCarTypeSelect();

    /** 검색 */
    $("#searchForm").submit(function(e){
        e.preventDefault();

        if(validation()){
            getList(1);
        }
    });
   
    $('#add-new').on('hide.bs.modal', function (event) {
        $('#CCC_CAR_TYPE_CD').val('');
        $('#CCC_MANUFACTURE_NM').val('');
        $('#CCC_CAR_NM').val('');
    });
});

/** 차량타입 */
setCarTypeSelect = () => {
    const data = {
        REQ_MODE: GET_CAR_TYPE,
    };
    callAjax('GET', data).then((result)=>{
        const data = JSON.parse(result);
        console.log(data);
        if(data.status == OK){
            const dataArray = data.data;
            let optionText = '<option value="">선택</option>';
            dataArray.map((item, index)=>{
                optionText += '<option value="'+item.RCT_CD+'">'+item.RCT_NM+'</option>';
            });
    
            carTypeOptionArray = optionText;
            $('#CCC_CAR_TYPE_CD').html(optionText);
        }else{
            alert("서버와의 연결이 원활하지 않습니다.");
        }
    });
}

callAjax = (method, data) => {
    $('#indicator').show();
     return new Promise(function (resolve, reject) {
        $.ajax({
            url:'act/carmngt_act.php',
            method: method,
            data: data,
            success: (res) => {
                console.log('res callAjax >>'+res);
                $('#indicator').hide();
                resolve(res);
            },
            error : (request, err) => {
                $('#indicator').hide();
                console.log('callAjax error ===> \n code:'+request.status+'\n'+'message:'+request.responseText+'\n'+'error:'+err);
                alert("오류가 발생했습니다. 관리자에게 문의해주세요.")
                return;
            }
        })
    });
}

/** 업데이트 모달창일경우 */
showUpdateModal = (data) => {
    $('#btnDelete').show();
    $('#submitBtn').html('수정');
    $('#submitBtn').attr('onclick', 'updateData()');

    $('#btnDelete').val(data.CCC_ID);

    $('#CCC_CAR_TYPE_CD').val(data.CCC_CAR_TYPE_CD);
    $('#CCC_MANUFACTURE_NM').val(data.CCC_MANUFACTURE_NM);
    $('#CCC_CAR_NM').val(data.CCC_CAR_NM);
  
    $('#add-new').modal();
}

/** 등록모달창일경우 */
showAddModal = () => {
    $('#btnDelete').hide();
    $('#submitBtn').html('등록');
    $('#submitBtn').attr('onclick', 'addData()');
    
    $('#add-new').modal();
}

excelDownload = () => {
    location.href="carmngtExcel.html?"+$("#searchForm").serialize()+"&REQ_MODE="+CASE_TOTAL_LIST;

}

/** 리스트 카운트 수 변경했을 때 리스트 불러오김 */
$('#perPage').change(()=>{
    getList(1);
});


validationData = () => {
    if($('#CCC_CAR_TYPE_CD').val()==''){
        alert("차량급을 선택해주세요.");
        $('#CCC_CAR_TYPE_CD').focus();
        return false;
    }else if($('#CCC_MANUFACTURE_NM').val().trim()==''){
        alert("제조사를 입력해주세요.");
        $('#CCC_MANUFACTURE_NM').focus();
        return false;
    }else if($('#CCC_CAR_NM').val().trim()==''){
        alert("차량명을 입력해주세요.");
        $('#CCC_CAR_NM').focus();
        return false;
    }else {
        return true;
    }
}

/** 리스트 테이블 동적으로 생성 
 * @param {dataArray : 리스트 데이터, currentPage : 현재페이지(페이징을 위해)} 
*/
setTable = (totalListCount, dataArray, currentPage) => {
    let tableText = '';

    dataArray.map((data, index)=>{
        tableText += "<tr>";
        tableText += "<td class='text-center'>"+data.IDX+"</td>";
        tableText += "<td class='text-center'>"+data.RCT_NM+"</td>";
        tableText += "<td class='text-center'>"+data.CCC_MANUFACTURE_NM+"</td>";
        tableText += "<td class='text-center'>"+data.CCC_CAR_NM+"</td>";
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

/** 검색버튼 눌렀을 때 */
validation = () => {
    if($('#CCC_SEARCH_TEXT').val().trim() != '' && $('#CCC_SEARCH_VALUE').val().trim() == ''){
        alert("제조사/차량명을 선택해주세요.");
        $("#CCC_SEARCH_VALUE").focus();

        return false;
    }else{
        return true;
    }
}

/**권한관리 리스트 */
getList = (currentPage) => {
    CURR_PAGE = (currentPage == undefined) ? CURR_PAGE : currentPage;
    const data = {
        REQ_MODE: CASE_LIST,
        PER_PAGE:  $("#perPage").val(),
        CURR_PAGE:  CURR_PAGE,

        /** 검색 */
        CCC_SEARCH_VALUE: $('#CCC_SEARCH_VALUE').val(),
        CCC_SEARCH_TEXT: $('#CCC_SEARCH_TEXT').val(),
    };
   

    callAjax('GET', data).then((result)=>{
        const data = JSON.parse(result);
        if(data.status == OK){
            const item = data.data;
            setTable(item[1], item[0], CURR_PAGE);
        }else{
            setHaveNoDataTable();
        }
    });
    
}

/** 전체 권한관리 리스트 가져오기(엑셀다운로드 용) */
getTotalList = () => {
    const data = {
        REQ_MODE: CASE_TOTAL_LIST,

         /** 검색 */
         CCC_SEARCH_VALUE: $('#CCC_SEARCH_VALUE').val(),
         CCC_SEARCH_TEXT: $('#CCC_SEARCH_TEXT').val(),
    };
    callAjax('GET', data).then((result)=>{
        const data = JSON.parse(result);
        getList(1);
    });
}

/** 권한관리 등록버튼 눌렀을 때 */
addData = () => {
    if(validationData()){
        const data = {
            REQ_MODE: CASE_CREATE,

            CCC_CAR_TYPE_CD : $("#CCC_CAR_TYPE_CD").val(),
            CCC_MANUFACTURE_NM : $('#CCC_MANUFACTURE_NM').val(),
            CCC_CAR_NM : $('#CCC_CAR_NM').val(),
        };

        callAjax('POST', data).then((result)=>{
            const data = JSON.parse(result);
            if(data.status == OK){
                confirmSwalOneButton('등록 성공하였습니다.','확인',()=>{
                        getList(1);
                        $('#add-new').modal('hide');
                });
            }else if(data.status == ERROR){
                alert(data.message);
                $('#CPP_ID').focus();
            }else{
                alert("서버와의 연결이 원활하지 않습니다.");
            } 
        });     
    }
    
}

/** 리스트에서 클릭해서 디테일 페이지 들어갔을 때 데이터 세팅 */
getData = (idx) => {
    const data = {
        REQ_MODE : CASE_READ,
        ALLM_ID : idx
    };
    callAjax('GET', data).then((result)=>{
        const data = JSON.parse(result);
        if(data.status == OK){
            const item = data.data;
            $('#selectdx').val(item.ALLM_ID);
            $('#ALLM_NM').val(item.ALLM_NM);   
        }else{
            const resultText ='서버와의 연결이 원활하지 않습니다.' ;
            alert(resultText);
        } 
    });
}

/** 권한관리 업데이트 */
updateData = () => {
    if(validationData()){
        const data = {
            REQ_MODE: CASE_UPDATE,
            CCC_ID : $('#btnDelete').val(),
            CCC_CAR_TYPE_CD : $("#CCC_CAR_TYPE_CD").val(),
            CCC_MANUFACTURE_NM : $('#CCC_MANUFACTURE_NM').val(),
            CCC_CAR_NM : $('#CCC_CAR_NM').val(),
        };
        callAjax('POST', data).then((result)=>{
            const data = JSON.parse(result);
            if(data.status == OK){
                confirmSwalOneButton('수정에 성공하였습니다.','확인',()=>{
                    getList();
                    $('#add-new').modal('hide');
                });
            }else if(data.status == ERROR){
                alert(data.message);
                $('#CPP_ID').focus();
            }else{
                alert("서버와의 연결이 원활하지 않습니다.");
            }   
        });
    }
}

delData = () => {
    const data = {
        REQ_MODE: CASE_DELETE,
        CCC_ID : $('#btnDelete').val(),
    };
    callAjax('POST', data).then((result)=>{
        const data = JSON.parse(result);
        if(data.status == OK){
            confirmSwalOneButton('삭제에 성공하였습니다.','확인',()=>{
                getList();
                $('#add-new').modal('hide');
            });
        }else{
            const resultText ='서버와의 연결이 원활하지 않습니다.' ;
            alert(resultText);
        } 
    });
}

