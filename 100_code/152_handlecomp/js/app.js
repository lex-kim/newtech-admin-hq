let modalType = '';      // 모달이 수정인지 등록인지
let CPP_ID_SELECTOR = [];
const ajaxUrl = 'act/handlecomp_act.php';
let CURR_PAGE = 1;
 
$(document).ready(() =>{
    $('#indicator').hide();
    setPartsTypeSelect();

    const tdLength = $('table tr th').length;
    setInitTableData(tdLength);

    /**등록모달에서 기존부품 선택했을 때 부품용량/길이 세팅 */
    $("#CPP_ID").change(function() {
        const selectIdx = ($('#CPP_ID option').index($('#CPP_ID option:selected')))-1;
        if(selectIdx >= 0){
            $('#CPP_UNIT').val(CPP_ID_SELECTOR[selectIdx ].CPP_UNIT);
        }else{
            $('#CPP_UNIT').val('');
        }    
    });
    
    $( "#CPPS_APPLY_DT" ).datepicker(
        getDatePickerSetting()
    );
    $( "#DATE_START").datepicker(
        getDatePickerSetting('DATE_END', 'minDate')
    );
    $( "#DATE_END").datepicker(
        getDatePickerSetting('DATE_START', 'maxDate')
    );
    $('#add-new').on('hide.bs.modal', function (event) {
        $('#oriOrderNum').val('');
        $('#IS_EXIST_ORDER_NUM').val('');
        $('#ORI_CPPS_ID').val('');
    });      

    $('#DATE_TYPE_SEARCH').change(function(){
        if($(this).val() == ''){
            $('#DATE_START').val('');
            $('#DATE_END').val('');
        }
    });
        /** 검색 */
    $("#searchForm").submit(function(e){
        e.preventDefault();

        if(isSearchValidation()){
            getList(1);
        }
    });

     /** 등록/수정 */
     $('#editForm').submit(function(e){
        e.preventDefault();

        if($('#ORI_CPPS_ID').val() == ''){
            if(validationData()){
                confirmSwalTwoButton('저장 하시겠습니까?','확인', '취소', false ,()=>addData());
            }
        }else{
            if(validationData()){
                confirmSwalTwoButton('수정 하시겠습니까?','확인', '취소', false ,()=>updateData());
              }
        }
        
       
    });
});


/** 부품타입 */
setPartsTypeSelect = () => {
    const data = {
        REQ_MODE: GET_PARTS_TYPE,
    };
    callAjax(ajaxUrl, 'GET', data).then((result)=>{
        // console.log('setPartsTypeSelect  SQL>>>>',result);
        if(isJson(result)){
            const data = JSON.parse(result);
            // console.log('setPartsTypeSelect data',data);
            if(data.status == OK){
                const dataArray = data.data; // index 0:부푸타입, 1:기준부품, 2:취급부품
                const partsTypeData = dataArray[0];
                CPP_ID_SELECTOR= dataArray[1];
                
                const handlecompTypeData = dataArray[2];
    
                let optionText = '<option value="">=부품타입=</option>';
                partsTypeData.map((item)=>{
                    optionText += '<option value="'+item.CCD_NM+'">'+item.CCD_NM+'</option>';
                });
                $('#CPP_PART_TYPE_SARCH').append(optionText);
    
                optionText = '<option value="">=기준부품명=</option>';
                CPP_ID_SELECTOR.map((item, index)=>{
                    optionText += '<option value="'+item.CPP_ID+'" name="'+index+'">'+item.CPP_NM+' ('+item.CPP_ID+')'+'</option>';
                });
                $('#CPP_NM_SEARCH').append(optionText);
    
                optionText = '<option value="">선택</option>';
                CPP_ID_SELECTOR.map((item)=>{
                    optionText += '<option class="multiSelectOption" value="'+item.CPP_ID+'">'+item.CPP_NM+'</option>';
                });
                $('#CPP_ID').append(optionText);
    
                optionText = '';
                handlecompTypeData.map((item)=>{
                    optionText += '<option value="'+item.CPPS_ID+'">'+item.CPPS_ID+'</option>';
                });
                $('#CPPS_ID_SEARCH').append(optionText);
                $('#CPPS_ID_SEARCH').selectpicker('refresh');
    
                optionText = '';
                handlecompTypeData.map((item)=>{
                    optionText += '<option class="multiSelectOption" value="'+item.CPPS_NM+'">'+item.CPPS_NM+'</option>';
                });
                $('#CPPS_NM_SEARCH').append(optionText);
                $('#CPPS_NM_SEARCH').selectpicker('refresh');
    
                optionText = '';
                handlecompTypeData.map((item)=>{
                    optionText += '<option class="multiSelectOption" value="'+item.CPPS_NM_SHORT+'">'+item.CPPS_NM_SHORT+'</option>';
                });
                $('#CPPS_NM_SHORT_SEARCH').append(optionText);
                $('#CPPS_NM_SHORT_SEARCH').selectpicker('refresh');
            }else{
                alert("서버와의 연결이 원활하지 않습니다.");
            }
        }else{
            alert(ERROR_MSG_1200);
        }
       
    });
}


/** 업데이트 모달창일경우 */
showUpdateModal = (data) => {
    modalType = CASE_UPDATE;
    $('#btnDelete').show();
    $('#submitBtn').html('수정');
    
    $('#btnDelete').val(data.CPPS_ID);
    $('#ORI_CPPS_ID').val(data.CPPS_ID);
    $('#ORI_CPPS_ORDER_NUM').val(data.CPPS_ORDER_NUM);

    $('#CPPS_ORDER_NUM').val(data.CPPS_ORDER_NUM);
    $('#CPP_ID').val(data.CPP_ID);
    $('#CPPS_ID').val(data.CPPS_ID);
    $('#CPP_UNIT').val(data.CPP_UNIT);
    $('#CPPS_NM').val(data.CPPS_NM);
    $('#CPPS_NM_SHORT').val(data.CPPS_NM_SHORT);
    $('#CPPS_UNIT').val(data.CPPS_UNIT);
    $('#CPPS_RATIO').val(data.CPPS_RATIO);
    $('#CPPS_GENUINE_YN').val(data.CPPS_GENUINE_YN);
    $('#CPPS_PRICE').val(data.CPPS_PRICE);
    $('#CPPS_APPLY_DT').val(data.CPPS_APPLY_DT);
    $('#CPPS_RECKON_YN').val(data.CPPS_RECKON_YN);
    $('#CPPS_INOUT_YN').val(data.CPPS_INOUT_YN);
    $('#CPPS_ORDER_NUM').val(data.CPPS_ORDER_NUM);

    $('#add-new').modal();
}

/** 등록모달창일경우 */
showAddModal = () => {
    modalType = CASE_CREATE;
    $('#btnDelete').hide();
    $('#submitBtn').html('등록');

    $('#add-new input').val('');
    $('#add-new select').val('');
    
    $('#add-new').modal();
}

/** 리스트 카운트 수 변경했을 때 리스트 불러오김 */
$('#perPage').change(()=>{
    getList();
});

/** 검색버튼 눌렀을 때 */
isSearchValidation = () => {
    if(($('#DATE_START').val() != '' || $('#DATE_END').val() != '') && $('#DATE_TYPE_SEARCH').val()==''){
        alert("기간을 선택해주세요.");
        $('#DATE_TYPE_SEARCH').focus();

        return false;
    }else{
        return true;
    }
}

validationData = () => {
    if($('#CPPS_ORDER_NUM').val()==''){
        alert("출력순서를 입력해주세요.");
        $('#CPPS_ORDER_NUM').focus();
        return false;
    }else if($('#CPP_ID').val().trim()==''){
        alert("기준부품코드를 입력해주세요.");
        $('#CPP_ID').focus();
        return false;
    }else if($('#CPPS_ID').val().trim()==''){
        alert("취급부품번호를 입력해주세요.");
        $('#CPPS_ID').focus();
        return false;
    }else if($('#CPPS_NM').val().trim()==''){
        alert("취급부품명을 입력해주세요.");
        $('#CPPS_NM').focus();
        return false;
    }else if($('#CPPS_NM_SHORT').val().trim()==''){
        alert("취급부품약어를 입력해주세요.");
        $('#CPPS_NM_SHORT').focus();
        return false;
    }else if($('#CPPS_UNIT').val().trim()==''){
        alert("취급부품 용량/길이를 선택해주세요.");
        $('#CPPS_UNIT').focus();
        return false;
    }else if($('#CPPS_RATIO').val().trim()==''){
        alert("환산사용량을 입력해주세요.");
        $('#CPPS_RATIO').focus();
        return false;
    }else if($('#CPPS_GENUINE_YN').val().trim()==''){
        alert("순정여부를 선택해주세요.");
        $('#CPPS_GENUINE_YN').focus();
        return false;
    }else if($('#CPPS_PRICE').val().trim()==''){
        alert("보험청구단가를 입력해주세요.");
        $('#CPPS_PRICE').focus();
        return false;
    }else if($('#CPPS_APPLY_DT').val().trim()==''){
        alert("변동적용일을 선택해주세요.");
        $('#CPPS_APPLY_DT').focus();
        return false;
    }else if($('#CPPS_RECKON_YN').val().trim()==''){
        alert("조건표적용여부를 선택해주세요.");
        $('#CPPS_RECKON_YN').focus();
        return false;
    }else if($('#CPPS_INOUT_YN').val().trim()==''){
        alert("입출고연동사용여부를 선택해주세요.");
        $('#CPPS_INOUT_YN').focus();
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
        tableText += "<td class='text-center'>"+data.CPPS_ORDER_NUM+"</td>";
        tableText += "<td class='text-center'>"+data.CPP_PART_TYPE+"</td>";
        tableText += "<td class='text-left'>"+data.CPP_NM+"</td>";
        tableText += "<td class='text-center'>"+data.CPP_NM_SHORT+"</td>";
        tableText += "<td class='text-right'>"+data.CPP_UNIT+"</td>";
        tableText += "<td class='text-center'>"+data.CPPS_ID+"</td>";
        tableText += "<td class='text-left'>"+data.CPPS_NM+"</td>";
        tableText += "<td class='text-center'>"+data.CPPS_NM_SHORT+"</td>";
        tableText += "<td class='text-right'>"+data.CPPS_UNIT+"</td>";
        tableText += "<td class='text-right'>"+data.CPPS_RATIO+"</td>";
        tableText += "<td class='text-center'>"+(data.CPPS_GENUINE_YN == "Y" ? "순정" :"비순정")+"</td>";
        tableText += "<td class='text-center'>"+(data.CPPS_RECKON_YN == "Y" ? "적용" :"미적용")+"</td>";
        tableText += "<td class='text-center'>"+(data.CPPS_INOUT_YN == "Y" ? "적용" :"미적용")+"</td>";
        tableText += "<td class='text-center'>"+data.WRT_DTHMS+"</td>";
        tableText += "<td class='text-center'>"+data.CPPS_APPLY_DT+"</td>";
        tableText += "<td class='text-right'>"+(numberWithCommas(data.CPPS_PRICE))+"</td>";
        tableText += "<td class='text-center'><button type='button' class='btn btn-default' onclick='openExchTablePopup("+JSON.stringify(data)+")'>이동</button></td>";
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
    const tdLength = $('table tr th').length;
    const tableText = "<tr><td class='text-center' colspan='"+tdLength+"'>조회된 데이터가 존재하지 않습니다.</tr></tr>";
    $('.pagination').html('');
    $('#dataTable').html(tableText);
}


/**권한관리 리스트 
 * @param {currentPage : 현재페이지}
*/
getList = (currentPage) => {
    CURR_PAGE = (currentPage == undefined) ? CURR_PAGE : currentPage;
    let form = $("#searchForm")[0];
    let formData = new FormData(form);
    formData.append('REQ_MODE', CASE_LIST);
    formData.append('PER_PAGE', $("#perPage").val());
    formData.append('CURR_PAGE', CURR_PAGE);

    callFormAjax(ajaxUrl, 'POST', formData).then((result)=>{
        if(isJson(result)){
            const data = JSON.parse(result);
            if(data.status == OK){
                const item = data.data;
                setTable(item[1], item[0], CURR_PAGE);
            }else{
                setHaveNoDataTable();
            }
        }else{
            alert(ERROR_MSG_1200);
        }
       
    });  
    
}

excelDownload = () => {
    location.href="handlecompExcel.html?"+$("#searchForm").serialize()+"&REQ_MODE="+CASE_TOTAL_LIST;
}

openExchTablePopup = (data) => {
    const params = "?CPP_ID="+data.CPP_ID+"&CPPS_ID="+data.CPPS_ID;
    window.open('/100_code/171_exchTable/exchTable.html'+params, 
    'exchTable', 
    'width=1780,height=700'); 
}

/**
 * 수정/등록 실제 기능해주는 함수(공용)
 */
setSetData = (resultText, reqMode) =>{
    let form = $("#editForm")[0];
    let formData = new FormData(form);
    formData.append('REQ_MODE', reqMode);

    callFormAjax(ajaxUrl, 'POST', formData).then((result)=>{
        if(isJson(result)){
            const data = JSON.parse(result);
            if(data.status == OK){
                confirmSwalOneButton(resultText,'확인',()=>{
                    if(reqMode == CASE_CREATE){
                        getList(1);
                    }else{
                        getList();
                    }
                    $('#add-new').modal('hide');
                });
            }else if(data.status == CONFLICT){
                confirmSwalOneButton(data.message,'확인',()=>{
                    $('#CPPS_ID').focus();
                });
            }else if(data.status == ERROR){
                confirmSwalTwoButton(data.message,'확인', '취소', false ,()=>{
                    $('#IS_EXIST_ORDER_NUM').val('true');
                    if(reqMode == CASE_CREATE){
                        addData();
                    }else{
                        updateData();
                    }
                }, '진행하시겠습니까?');
            }else{
                alert(ERROR_MSG_1100);
            }  
        }else{
            alert(ERROR_MSG_1200);
        }   
    });    
}

/** 등록버튼 눌렀을 때 */
addData = () => {
    setSetData('등록에 성공하였습니다.' , CASE_CREATE);
}

/** 리스트에서 클릭해서 디테일 페이지 들어갔을 때 데이터 세팅 */
getData = (idx) => {
    const data = {
        REQ_MODE : CASE_READ,
        ALLM_ID : idx
    };
    callAjax(ajaxUrl, 'GET', data).then((result)=>{
        // console.log('getData SQL>>',result);
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
    setSetData('수정에 성공하였습니다.' , CASE_UPDATE);
}

delData = () => {
    const data = {
        REQ_MODE: CASE_DELETE,
        CPPS_ID : $('#btnDelete').val(),
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