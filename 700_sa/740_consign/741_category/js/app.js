
const pageName = "category.html";
let CURR_PAGE = 1;
const CTGY_URL = 'act/category_act.php';

$(document).ready(() =>{
    $('#indicator').hide();
    setInitTableData(7, 'list_contents');

    /** 검색 */
    $("#searchForm").submit(function(e){
        e.preventDefault();
            getList(1);
    });

    $("#START_DT").datepicker(
        getDatePickerSetting('END_DT', 'minDate')
    );
    $("#END_DT").datepicker(
        getDatePickerSetting('START_DT', 'maxDate')
    );
   
   
});



/** 업데이트 모달창일경우 */
showUpdateModal = (data) => {
    $('#btnDelete').show();
    $('#submitBtn').html('수정');
    $('#submitBtn').attr('onclick', 'updateData()');

    $('#categoryForm').each(function(){
      this.reset();
    });

    $('#NCC_ORDER_NUM').val(data.NCC_ORDER_NUM);
    $('#NCC_ID').val(data.NCC_ID);
    $('#NCC_NM').val(data.NCC_NM);
    $('#NCC_USE_YN').val(data.NCC_USE_YN);
    $('#NCC_ID').attr({"readonly": true});
 
    $('#ORI_NCC_ORDER_NUM').val(data.NCC_ORDER_NUM);
  
    $('#add-new').modal();
}

/** 등록모달창일경우 */
showAddModal = () => {
    $('#btnDelete').hide();
    $('#submitBtn').html('등록');
    $('#submitBtn').attr('onclick', 'addData()');

    $("#NCC_ID").removeAttr("readonly");
    $('#categoryForm').each(function(){
      this.reset();
    });

    $('#ORI_NCC_ORDER_NUM').val('');
    $('#IS_EXIST_ORDER_NUM').val('');
    
    $('#add-new').modal();
}

excelDownload = () => {
    location.href="categoryExcel.html?"+$("#searchForm").serialize();

}




validationData = () => {
  if($('#NCC_ORDER_NUM').val().trim()==''){
      alert("출력순서를 입력해주세요.");
      $('#NCC_ORDER_NUM').focus();
      return false;
  }else if($('#NCC_ID').val().trim()==''){
      alert("장비구분코드를 입력해주세요.");
      $('#NCC_ID').focus();
      return false;
  }else if($('#NCC_NM').val().trim()==''){
      alert("장비구분을 입력해주세요.");
      $('#NCC_NM').focus();
      return false;
  }else if($('#NCC_USE_YN').val().trim()==''){
      alert("사용여부를 선택해주세요.");
      $('#NCC_USE_YN').focus();
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

    let nccUseYn = "";
      if(data.NCC_USE_YN == YN_Y ){
        nccUseYn ="사용";
      } else {
        nccUseYn ="미사용";
      }
      console.log(index,'>>>',data);
      tableText += "<tr>";
      tableText += "<td class='text-center'>"+data.IDX+"</td>";
      tableText += "<td class='text-center'>"+data.NCC_ORDER_NUM+"</td>";
      tableText += "<td class='text-center'>"+data.NCC_NM+"</td>";
      tableText += "<td class='text-center'>"+nccUseYn+"</td>";
      tableText += "<td class='text-center'>"+data.WRT_USERID+"</td>";
      tableText += "<td class='text-center'>"+data.WRT_DTHMS+"</td>";
      if($("#AUTH_UPDATE").val() == YN_Y){
        tableText += "<td class='text-center'><button type='button' class='btn btn-default' onclick='showUpdateModal("+JSON.stringify(data)+")'>수정</button></td>";
    }
      tableText += "</tr>"
        
    });
    paging(totalListCount, currentPage, 'getList'); 
    $('#list_contents').html(tableText);
}

/** 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
    const tableText = "<tr><td class='text-center' colspan='10'>조회요청한 데이터가 존재하지 않습니다.</td></tr>";
    $('.pagination').html('');
    $('#list_contents').html(tableText);
}

/** 검색버튼 눌렀을 때 */
// validation = () => {
//     if($('#CCC_SEARCH_TEXT').val().trim() != '' && $('#CCC_SEARCH_VALUE').val().trim() == ''){
//         alert("제조사/차량명을 선택해주세요.");
//         $("#CCC_SEARCH_VALUE").focus();

//         return false;
//     }else{
//         return true;
//     }
// }

/**권한관리 리스트 */
getList = (currentPage) => {
    CURR_PAGE = (currentPage == undefined) ? CURR_PAGE : currentPage;
    const data = {
        REQ_MODE: CASE_LIST,
        PER_PAGE:  $("#perPage").val(),
        CURR_PAGE:  CURR_PAGE,

        /** 검색 */
        START_DT: $('#START_DT').val(),
        END_DT: $('#END_DT').val(),
        NCC_NM_SEARCH: $('#NCC_NM_SEARCH').val(),
        NCC_USE_YN_SEARCH: $('#NCC_USE_YN_SEARCH').val()
    };
    console.log('data--->',data)

    callAjax(CTGY_URL, 'POST', data).then((result)=>{
        const data = JSON.parse(result);
        if(data.status == OK){
            const item = data.data;
            setTable(item[1], item[0], CURR_PAGE);
        }else{
            setHaveNoDataTable();
        }
    });
    
}



/**  등록버튼 눌렀을 때 */
addData = () => {
    if(validationData()){
        const data = {
            REQ_MODE: CASE_CREATE,

            NCC_ORDER_NUM : $("#NCC_ORDER_NUM").val(),
            NCC_ID : $("#NCC_ID").val(),
            NCC_NM : $("#NCC_NM").val(),
            NCC_USE_YN : $("#NCC_USE_YN").val(),

            ORI_NCC_ORDER_NUM: $('#ORI_NCC_ORDER_NUM').val(),
            IS_EXIST_ORDER_NUM: $('#IS_EXIST_ORDER_NUM').val()
        };

        callAjax(CTGY_URL, 'POST', data).then((result)=>{
            const data = JSON.parse(result);
            if(data.status == OK){
                confirmSwalOneButton('등록 성공하였습니다.','확인',()=>{
                        getList(1);
                        $('#add-new').modal('hide');
                });
            }else if(data.status == CONFLICT){
              confirmSwalOneButton(data.message,'확인',()=>{
                  $('#NCC_ID').focus();
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
    
}


/** 업데이트 */
updateData = () => {
    if(validationData()){
        const data = {
            REQ_MODE: CASE_UPDATE,
            NCC_ORDER_NUM : $("#NCC_ORDER_NUM").val(),
            NCC_ID : $("#NCC_ID").val(),
            NCC_NM : $("#NCC_NM").val(),
            NCC_USE_YN : $("#NCC_USE_YN").val(),

            ORI_NCC_ORDER_NUM: $('#ORI_NCC_ORDER_NUM').val(),
            IS_EXIST_ORDER_NUM: $('#IS_EXIST_ORDER_NUM').val()
        };
        callAjax(CTGY_URL, 'POST', data).then((result)=>{
            const data = JSON.parse(result);
            if(data.status == OK){
                confirmSwalOneButton('수정에 성공하였습니다.','확인',()=>{
                    getList();
                    $('#add-new').modal('hide');
                });
            }else if(data.status == CONFLICT){
              confirmSwalOneButton(data.message,'확인',()=>{
                  $('#NCC_ID').focus();
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
}

delData = () => {
    const data = {
        REQ_MODE: CASE_DELETE,
        NCC_ID : $('#NCC_ID').val(),
    };
    callAjax(CTGY_URL, 'POST', data).then((result)=>{
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