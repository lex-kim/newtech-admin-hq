let modalType = ''; // 모달이 수정인지 등록인지
const pageName = "bbscategory.html";
let CURR_PAGE = 1;

$(document).ready(() =>{
  const pageType = $('#bbsType').val();
  getList();
});


callAjax = (method, data) => {
  $('#indicator').show();
   return new Promise(function (resolve, reject) {
      $.ajax({
          url:'act/bbsCategory_act.php',
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

/** 리스트 테이블 동적으로 생성 
* @param {dataArray : 리스트 데이터, currentPage : 현재페이지(페이징을 위해)} 
*/
setTable = (totalListCount, dataArray, currentPage) => {
  let tableText = '';

  dataArray.map((data, index)=>{
      tableText += "<tr>";
      tableText += "<td class='text-center'>"+data.IDX+"</td>";
      tableText += "<td class='text-center'>"+data.MGC_ORDER_NUM+"</td>";
      tableText += "<td class='text-center'>"+data.MGC_ID+"</td>";
      tableText += "<td class='text-center'>"+data.MGC_NM+"</a></td>";
      tableText += "<td class='text-center'>"+data.WRT_DTHMS+"</td>";
      tableText += "<td class='text-center'>"+data.WRT_USERID+"</td>";
      if($('#UPDATE_TABLE').is(":visible")){
          tableText += "<td class='text-center'>";
          tableText += "<button type='button' class='btn btn-default' onclick='showUpdateModal("+JSON.stringify(data)+")'>수정</button>";
          tableText += "</td>";
      }
      tableText += "</tr>"
      
  });
  paging(totalListCount, CURR_PAGE, 'getList'); 
  $('#bbs_list_contents').html(tableText);
}

/** 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
  const tableText = "<tr><td class='text-center' colspan='6'>조회요청한 데이터가 존재하지 않습니다.</td></tr>";
  $('.pagination').html('');
  $('#bbs_list_contents').html(tableText);
}

/** 리스트 */
getList = (currentPage) => {
  CURR_PAGE = (currentPage == undefined) ? CURR_PAGE : currentPage;
// console.log('CURR_PAGE>>',CURR_PAGE);
  const data = {
      REQ_MODE: CASE_LIST,
      PER_PAGE:  $("#perPage").val(),
      CURR_PAGE:  CURR_PAGE,
  };
  callAjax('GET', data).then((result)=>{
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

/* validation  */
validationData = () => {
  if($('#MGC_ORDER_NUM').val().trim()==''){
      alert("출력순서를 입력해주세요.");
      $('#MGC_ORDER_NUM').focus();
      return false;
  }else if($('#MGC_ID').val().trim()==''){
      alert("구분코드를 입력해주세요.");
      $('#MGC_ID').focus();
      return false;
  }else if($('#MGC_NM').val().trim()==''){
      alert("사내게시판 구분을 입력해주세요.");
      $('#MGC_NM').focus();
      return false;
  }else {
      return true;
  }
}


/** 업데이트 모달창일경우 */
showUpdateModal = (data) => {
  modalType = CASE_UPDATE;
  $("#btnDelete").show();
  $("#submitBtn").html('수정');
  $("#submitBtn").attr('onclick', 'updateData()');
  $("#btnDelete").val(data.MGC_ID);

  $('#MGC_ID').val(data.MGC_ID);
  $('#MGC_NM').val(data.MGC_NM);
  $('#MGC_ORDER_NUM').val(data.MGC_ORDER_NUM);
  $('#MGC_ID').attr({"readonly": true});

  $('#CPP_ORDER_NUM').val(data.CPP_ORDER_NUM);
  $('#ORI_MGC_ORDER_NUM').val(data.CPP_ORDER_NUM);

  $('#add-new').modal();
}

/** 등록모달창일경우 */
showAddModal = () => {
  modalType = CASE_CREATE;
  $("#btnDelete").hide();
  $("#submitBtn").html('등록');
  $("#submitBtn").attr('onclick', 'addData()');

  $("#MGC_ID").val("");
  $("#MGC_NM").val("");
  $("#MGC_ORDER_NUM").val("");
  $("#MGC_ID").removeAttr("readonly");

  $('#ORI_MGC_ORDER_NUM').val('');
  $('#IS_EXIST_ORDER_NUM').val('');

  $("#add-new").modal({
    show: true
  });

}

/** 등록버튼 눌렀을 때 */
addData = () => {
  if(validationData()){
    const data = {
        REQ_MODE: CASE_CREATE,
        MGC_ORDER_NUM : $("#MGC_ORDER_NUM").val(),
        MGC_ID : $("#MGC_ID").val(),
        MGC_NM : $("#MGC_NM").val(),
        IS_EXIST_ORDER_NUM : $('#IS_EXIST_ORDER_NUM').val(),
        ORI_MGC_ORDER_NUM : $('#ORI_MGC_ORDER_NUM').val()
    };
    callAjax('POST', data).then((result)=>{
        const data = JSON.parse(result);
        if(data.status == OK){
            confirmSwalOneButton('등록 성공하였습니다.','확인',()=>{location.replace(pageName)});
        }else if(data.status == ERROR){
          confirmSwalTwoButton(data.message,'확인', '취소', false ,()=>{
              $('#IS_EXIST_ORDER_NUM').val('false');
              addData();
          }, '진행하시겠습니까?');
        }else{
            const resultText ='서버와의 연결이 원활하지 않습니다.';
            alert(resultText);
        } 
    });
  }
}



/** 업데이트 */
updateData = () => {
  if(validationData()){
    const data = {
        REQ_MODE: CASE_UPDATE,
        MGC_ID : $("#MGC_ID").val(),
        MGC_NM : $("#MGC_NM").val(),
        MGC_ORDER_NUM : $("#MGC_ORDER_NUM").val(),
        IS_EXIST_ORDER_NUM : $('#IS_EXIST_ORDER_NUM').val(),
        ORI_MGC_ORDER_NUM : $('#ORI_MGC_ORDER_NUM').val()
    };
    callAjax('POST', data).then((result)=>{
        const data = JSON.parse(result);
        if(data.status == OK){
            confirmSwalOneButton('수정에 성공하였습니다.','확인',()=>{$('#add-new').modal('toggle');getList();});
        }else if(data.status == ERROR){
          confirmSwalTwoButton(data.message,'확인', '취소', false ,()=>{
              $('#IS_EXIST_ORDER_NUM').val('false');
              updateData();
          }, '진행하시겠습니까?');
        }else{
            const resultText ='서버와의 연결이 원활하지 않습니다.' ;
            alert(resultText);
        } 
      
    });
  }
}

/** 삭제*/
delData = () => {
  const data = {
      REQ_MODE: CASE_DELETE,
      MGC_ID:   $("#btnDelete").val()
  };
  callAjax('POST', data).then((result)=>{
      const data = JSON.parse(result);
      if(data.status == OK){
          confirmSwalOneButton('삭제에 성공하였습니다.','확인',()=>{location.replace(pageName)});
      }else{
          const resultText ='서버와의 연결이 원활하지 않습니다.' ;
          alert(resultText);
      } 
  });
}
