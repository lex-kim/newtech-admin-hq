let modalType = '';      // 모달이 수정인지 등록인지
let partsTypeOptionArray = [];
let selectId = '';
const pageName = "stdcomp.html";
const ajaxUrl = 'act/stdcomp_act.php';
let CURR_PAGE = 1;

$(document).ready(() =>{
    $('#indicator').hide();
    setPartsTypeSelect();

    const tdLength = $('table tr th').length;
    setInitTableData(tdLength);

    $("#searchForm").submit(function(e){
        e.preventDefault();
        getList(1);
    });

    $('#COMP_TYPE_SEARCH').change(function(){
        getList(1);
    });
    $('#add-new').on('hide.bs.modal', function (event) {
        $('#oriOrderNum').val('');
        selectId = '';
    });

    $("#addForm").submit(function(e){
        e.preventDefault();
        
        if(modalType == CASE_UPDATE){
            confirmSwalTwoButton('수정 하시겠습니까?','확인', '취소', false ,()=>updateData());
        }else{
            addData();
        }
    });
});

/** 부품타입 */
setPartsTypeSelect = () => {
    const data = {
        REQ_MODE: GET_PARTS_TYPE,
    };
    callAjax(ajaxUrl, 'GET', data).then((result)=>{
        const data = JSON.parse(result);
        console.log(data);
        if(data.status == OK){
            const dataArray = data.data;
            let optionText = '<option value="">=부품타입=</option>';
            dataArray.map((item, index)=>{
                optionText += '<option value="'+item.CCD_NM+'">'+item.CCD_NM+'</option>';
            });
    
            partsTypeOptionArray = optionText;
    
            $('#COMP_TYPE_SEARCH').append(optionText);
            $('#CPP_PART_TYPE').append(optionText);
        }else{
            alert("서버와의 연결이 원활하지 않습니다.");
        }
    });
}


/** 업데이트 모달창일경우 */
showUpdateModal = (data) => {
    modalType = CASE_UPDATE;
    $('#btnDelete').show();
    $('#submitBtn').html('수정');
    
    $('#btnDelete').val(data.CPP_ID);

    $('#CPP_ID').attr('readonly',true);
    $('#CPP_PART_TYPE').val(data.CPP_PART_TYPE);
    $('#CPP_ID').val(data.CPP_ID);
    $('#CPP_NM').val(data.CPP_NM);
    $('#CPP_NM_SHORT').val(data.CPP_NM_SHORT);
    $('#CPP_UNIT').val(data.CPP_UNIT);
    $('#CPP_USE_YN').val(data.CPP_USE_YN);
    $('#CPP_ORDER_NUM').val(data.CPP_ORDER_NUM);
    $('#ORI_CPP_ORDER_NUM').val(data.CPP_ORDER_NUM);

    $('#add-new').modal();
}

/** 등록모달창일경우 */
showAddModal = () => {
    modalType = CASE_CREATE;
    $('#btnDelete').hide();
    $('#submitBtn').html('등록');

    $('#CPP_ID').attr('readonly',false);
    $('#CPP_PART_TYPE').val('');
    $('#CPP_ID').val('');
    $('#CPP_NM').val('');
    $('#CPP_NM_SHORT').val('');
    $('#CPP_UNIT').val('');
    $('#CPP_USE_YN').val('');
    $('#CPP_ORDER_NUM').val('');
    $('#ORI_CPP_ORDER_NUM').val('');
    $('#IS_EXIST_ORDER_NUM').val('');

    $('#add-new').modal();
}

/** 리스트 카운트 수 변경했을 때 리스트 불러오김 */
$('#perPage').change(()=>{
    getList(1);
});

excelDownload = () => {
    location.href="stdcompExcel.html?"+$("#searchForm").serialize()+"&REQ_MODE="+CASE_TOTAL_LIST;
}

/** 리스트 테이블 동적으로 생성 
 * @param {dataArray : 리스트 데이터, currentPage : 현재페이지(페이징을 위해)} 
*/
setTable = (totalListCount, dataArray, currentPage) => {
    let tableText = '';

    dataArray.map((data, index)=>{
       const useYN = data.CPP_USE_YN == "Y" ? '사용' : '미사용';
        tableText += "<tr>";
        tableText += "<td class='text-center'>"+data.IDX+"</td>";
        tableText += "<td class='text-center'>"+data.CPP_ORDER_NUM+"</td>";
        tableText += "<td class='text-center'>"+data.CPP_PART_TYPE+"</td>";
        tableText += "<td class='text-center'>"+data.CPP_ID+"</td>";
        tableText += "<td class='text-left'>"+data.CPP_NM+"</td>";
        tableText += "<td class='text-left'>"+data.CPP_NM_SHORT+"</td>";
        tableText += "<td class='text-right'>"+data.CPP_UNIT+"</td>";
        tableText += "<td class='text-right'>"+data.CPPS_COUNT+"</td>";
        tableText += "<td class='text-center'>"+ useYN +"</td>";
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
        COMP_TYPE_SEARCH : $('#COMP_TYPE_SEARCH').val(),
    };

    callAjax(ajaxUrl, 'GET', data).then((result)=>{
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
    };
    callAjax(ajaxUrl, 'GET', data).then((result)=>{
        const data = JSON.parse(result);
        getList(1);
    });
}

/** 등록버튼 눌렀을 때 */
addData = () => {
    let form = $("#addForm")[0];
    let formData = new FormData(form);
    formData.append('REQ_MODE', CASE_CREATE);
    formData.append('IS_EXIST_ORDER_NUM', $('#IS_EXIST_ORDER_NUM').val());
    formData.append('ORI_CPP_ORDER_NUM', $('#ORI_CPP_ORDER_NUM').val());

    callFormAjax(ajaxUrl, 'POST', formData).then((result)=>{
        const data = JSON.parse(result);
        if(data.status == OK){
            confirmSwalOneButton('등록에 성공하였습니다.','확인',()=>{
                getList(1);
                $('#add-new').modal('hide');
            });
        }else if(data.status == CONFLICT){
            confirmSwalOneButton(data.message,'확인',()=>{
                $('#CPP_ID').focus();
            });
        }else if(data.status == ERROR){
            confirmSwalTwoButton(data.message,'확인', '취소', false ,()=>{
                $('#IS_EXIST_ORDER_NUM').val('false');
                addData();
            }, '진행하시겠습니까?');
        }else{
            alert("서버와의 연결이 원활하지 않습니다.");
        } 
    });  
    
}

/** 리스트에서 클릭해서 디테일 페이지 들어갔을 때 데이터 세팅 */
getData = (idx) => {
    const data = {
        REQ_MODE : CASE_READ,
        ALLM_ID : idx
    };
    callAjax(ajaxUrl, 'GET', data).then((result)=>{
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
    let form = $("#addForm")[0];
    let formData = new FormData(form);
    formData.append('REQ_MODE', CASE_UPDATE);
    formData.append('IS_EXIST_ORDER_NUM', $('#IS_EXIST_ORDER_NUM').val());
    formData.append('ORI_CPP_ORDER_NUM', $('#ORI_CPP_ORDER_NUM').val());

    callFormAjax(ajaxUrl, 'POST', formData).then((result)=>{
        const data = JSON.parse(result);
        if(data.status == OK){
            confirmSwalOneButton('수정에 성공하였습니다.','확인',()=>{
                getList();
                $('#add-new').modal('hide');
            });
        }else if(data.status == CONFLICT){
            confirmSwalOneButton(data.message,'확인',()=>{
                $('#CPP_ID').focus();
            });
        }else if(data.status == ERROR){
            confirmSwalTwoButton(data.message,'확인', '취소', false ,()=>{
                $('#IS_EXIST_ORDER_NUM').val('false');
                updateData();
            }, '진행하시겠습니까?');
        }else{
            alert("서버와의 연결이 원활하지 않습니다.");
        } 
    }); 
}

delData = () => {
    const data = {
        REQ_MODE: CASE_DELETE,
        CPP_ID : $('#btnDelete').val(),
    };
    callAjax(ajaxUrl, 'POST', data).then((result)=>{
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

