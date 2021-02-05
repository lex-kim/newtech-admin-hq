
const pageName = "gradeMngt.html";
let CURR_PAGE = 1;
const GRADE_URL = 'act/gradeMngt_act.php';

$(document).ready(() =>{
    $('#indicator').hide();
    setInitTableData(10, 'gradeList');

    /** 검색 */
    $("#searchForm").submit(function(e){
        e.preventDefault();
            getList(1);
    });
   
   
});



/** 업데이트 모달창일경우 */
showUpdateModal = (data) => {
    $('#btnDelete').show();
    $('#submitBtn').html('수정');
    $('#submitBtn').attr('onclick', 'updateData()');

    $('#gradeForm').each(function(){
      this.reset();
    });

    $('#SLL_ID').val(data.SLL_ID);
    $('#SLL_NM').val(data.SLL_NM);
    $('#SLL_ORDER_NUM').val(data.SLL_ORDER_NUM);
    $('#SLL_BILL_RATE').val(data.SLL_BILL_RATE);
    $('#SLL_DISCOUNT_RATE').val(data.SLL_DISCOUNT_RATE);
    $('#SLL_BUY_RATE').val(data.SLL_BUY_RATE);
    $('#SLL_MEMO').val(data.SLL_MEMO);
    $('#SLL_ID').attr({"readonly": true});
 
    $('#ORI_SLL_ORDER_NUM').val(data.SLL_ORDER_NUM);
  
    $('#add-new').modal();
}

/** 등록모달창일경우 */
showAddModal = () => {
    $('#btnDelete').hide();
    $('#submitBtn').html('등록');
    $('#submitBtn').attr('onclick', 'addData()');

    $("#SLL_ID").removeAttr("readonly");
    $('#gradeForm').each(function(){
      this.reset();
    });

    $('#ORI_SLL_ORDER_NUM').val('');
    $('#IS_EXIST_ORDER_NUM').val('');
    
    $('#add-new').modal();
}

excelDownload = () => {
    location.href="gradeMngtExcel.html?"+$("#searchForm").serialize();

}




validationData = () => {
  if($('#SLL_ID').val().trim()==''){
      alert("등급코드를 입력해주세요.");
      $('#SLL_ID').focus();
      return false;
  }else if($('#SLL_NM').val().trim()==''){
      alert("등급명을 입력해주세요.");
      $('#SLL_NM').focus();
      return false;
  }else if($('#SLL_ORDER_NUM').val().trim()==''){
      alert("노출순서를 입력해주세요.");
      $('#SLL_ORDER_NUM').focus();
      return false;
  }else if($('#SLL_BILL_RATE').val().trim()==''){
      alert("포인트 적립율을 입력해주세요.");
      $('#SLL_BILL_RATE').focus();
      return false;
  }else if($('#SLL_DISCOUNT_RATE').val().trim()==''){
      alert("할인율을 입력해주세요.");
      $('#SLL_DISCOUNT_RATE').focus();
      return false;
  }else if($('#SLL_BUY_RATE').val().trim()==''){
      alert("구매적립률을 입력해주세요.");
      $('#SLL_BUY_RATE').focus();
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
      console.log(index,'>>>',data);
      tableText += "<tr>";
      tableText += "<td class='text-center'>"+data.IDX+"</td>";
      tableText += "<td class='text-center'>"+data.SLL_NM+"</td>";
      tableText += "<td class='text-center'>"+data.SLL_ORDER_NUM+"</td>";
      tableText += "<td class='text-right'>"+data.SLL_BILL_RATE+"</td>";
      tableText += "<td class='text-right'>"+data.SLL_DISCOUNT_RATE+"</td>";
      tableText += "<td class='text-right'>"+data.SLL_BUY_RATE+"</td>";
      tableText += "<td class='text-left'>"+data.SLL_MEMO+"</td>";
      tableText += "<td class='text-center'>"+data.WRT_USERID+"</td>";
      tableText += "<td class='text-center'>"+data.WRT_DTHMS+"</td>";
      tableText += "<td class='text-center'><button type='button' class='btn btn-default' onclick='showUpdateModal("+JSON.stringify(data)+")'>수정</button></td>";
      tableText += "</tr>"
        
    });
    paging(totalListCount, currentPage, 'getList'); 
    $('#gradeList').html(tableText);
}

/** 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
    const tableText = "<tr><td class='text-center' colspan='10'>조회요청한 데이터가 존재하지 않습니다.</td></tr>";
    $('.pagination').html('');
    $('#gradeList').html(tableText);
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
        START_PUR2: $('#START_PUR2').val(),
        START_PUR: $('#START_PUR').val(),
        END_PUR: $('#END_PUR').val(),
        CHANGE_KEY_SEARCH: $('#CHANGE_KEY_SEARCH').val(),
        CHANGE_KEYWORD_SEARCH: $('#CHANGE_KEYWORD_SEARCH').val()
    };
    console.log('data--->',data)

    callAjax(GRADE_URL, 'POST', data).then((result)=>{
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

            SLL_ORDER_NUM : $("#SLL_ORDER_NUM").val(),
            SLL_ID : $("#SLL_ID").val(),
            SLL_NM : $("#SLL_NM").val(),
            SLL_ORDER_NUM : $("#SLL_ORDER_NUM").val(),
            SLL_BILL_RATE : $("#SLL_BILL_RATE").val(),
            SLL_DISCOUNT_RATE : $("#SLL_DISCOUNT_RATE").val(),
            SLL_BUY_RATE : $("#SLL_BUY_RATE").val(),
            SLL_MEMO : $("#SLL_MEMO").val(),

            ORI_SLL_ORDER_NUM: $('#ORI_SLL_ORDER_NUM').val(),
            IS_EXIST_ORDER_NUM: $('#IS_EXIST_ORDER_NUM').val()
        };

        callAjax(GRADE_URL, 'POST', data).then((result)=>{
            const data = JSON.parse(result);
            if(data.status == OK){
                confirmSwalOneButton('등록 성공하였습니다.','확인',()=>{
                        getList(1);
                        $('#add-new').modal('hide');
                });
            }else if(data.status == CONFLICT){
              confirmSwalOneButton(data.message,'확인',()=>{
                  $('#SLL_ID').focus();
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
            SLL_ID : $("#SLL_ID").val(),
            SLL_NM : $("#SLL_NM").val(),
            SLL_ORDER_NUM : $("#SLL_ORDER_NUM").val(),
            SLL_BILL_RATE : $("#SLL_BILL_RATE").val(),
            SLL_DISCOUNT_RATE : $("#SLL_DISCOUNT_RATE").val(),
            SLL_BUY_RATE : $("#SLL_BUY_RATE").val(),
            SLL_MEMO : $("#SLL_MEMO").val(),

            ORI_SLL_ORDER_NUM: $('#ORI_SLL_ORDER_NUM').val(),
            IS_EXIST_ORDER_NUM: $('#IS_EXIST_ORDER_NUM').val()
        };
        callAjax(GRADE_URL, 'POST', data).then((result)=>{
            const data = JSON.parse(result);
            if(data.status == OK){
                confirmSwalOneButton('수정에 성공하였습니다.','확인',()=>{
                    getList();
                    $('#add-new').modal('hide');
                });
            }else if(data.status == CONFLICT){
              confirmSwalOneButton(data.message,'확인',()=>{
                  $('#SLL_ID').focus();
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
        SLL_ID : $('#SLL_ID').val(),
    };
    callAjax(GRADE_URL, 'POST', data).then((result)=>{
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