const pageName = "division.html";
const DIVISION_URL = "act/division_act.php";

$(document).ready(function() {
  getList();
});

validationData = () => {
  let num_check=/^\d{3}$/;
  if(!num_check.test($("#CSD_ID").val())) {
    alertSwal('지역코드는 3자리 숫자만 입력가능합니다.');
    $("#CSD_ID").val("");
    $("#CSD_ID").focus();
    return false;
  } else {
    return true;
  }
}

/* 선택한 데이터 조회 */
showUpdateModal = (data) => {
  
  /* 3자리수 맞춰주기 */
  p_csdId = fillZero(data.CSD_ID, 3);
  // console.log(p_csdId);

  $("#REQ_MODE").val(CASE_UPDATE);
  $("#CSD_ID").val(data.CSD_ID);
  $("#CSD_ID").attr({"readonly": true});
  $("#CSD_NM").val(data.CSD_NM);
  $("#CSD_ORDER_NUM").val(data.CSD_ORDER_NUM);

  $('#ORI_CSD_ORDER_NUM').val(data.CSD_ORDER_NUM);

  $("#btnDelete").show();
  $('#submitBtn').html('수정');
  $('#submitBtn').attr('onclick', 'updateData()');
  $("#add-new").modal();
 
}
/* 업데이트 */
updateData = () => {
  if (validationData()){
    const data = {
        REQ_MODE: CASE_UPDATE,
        CSD_ID : $('#CSD_ID').val(),
        CSD_NM : $("#CSD_NM").val(),
        CSD_ORDER_NUM : $('#CSD_ORDER_NUM').val(),
        IS_EXIST_ORDER_NUM : $('#IS_EXIST_ORDER_NUM').val(),
        ORI_CSD_ORDER_NUM : $('#ORI_CSD_ORDER_NUM').val()
    };
    callAjax(DIVISION_URL, 'POST', data).then((result)=>{
        const data = JSON.parse(result);
        if(data.status == OK){
            confirmSwalOneButton('수정에 성공하였습니다.','확인',()=>{location.replace(pageName);});
        }else if(data.status == CONFLICT){
          confirmSwalOneButton(data.message,'확인',()=>{
              $('#CSD_ID').focus();
          });
        }else if(data.status == ERROR){
          confirmSwalTwoButton(data.message,'확인', '취소', false ,()=>{
              $('#IS_EXIST_ORDER_NUM').val('false');
              updateData();
          }, '진행하시겠습니까?');
        }else{
            alertSwal("서버와의 연결이 원활하지 않습니다.");
        }   
    });
  }
}


/* 삭제함수 */
function delData() {
  const data = {
      REQ_MODE: CASE_DELETE,
      CSD_ID:   $("#CSD_ID").val()
  };
  callAjax(DIVISION_URL,'POST', data).then((result)=>{
      const data = JSON.parse(result);
      if(data.status == OK){
          confirmSwalOneButton('삭제에 성공하였습니다.','확인',()=>{location.replace(pageName);});
      }else{
          const resultText ='서버와의 연결이 원활하지 않습니다.' ;
          alertSwal(resultText);
      } 
  });

}
/** 리스트 테이블 동적으로 생성 
 * @param {dataArray : 리스트 데이터, currentPage : 현재페이지(페이징을 위해)} 
*/
setTable = (totalListCount, dataArray, currentPage) => {
  let tableText = '';

  dataArray.map((data, index)=>{
      tableText += "<tr>";
      tableText += " <td class='text-center'>"+data.CSD_IDX+"</td>";  
      tableText += " <td class='text-center'>"+data.CSD_ORDER_NUM+"</td>";
      tableText += " <td class='text-center'>"+data.CSD_ID+"</td>";
      tableText += " <td class='text-center'>"+data.CSD_NM+"</td>";
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
  $('#region_list_contents').html(tableText);
}

/** 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
  const tableText = "<tr><td class='text-center' colspan='6'>조회된 데이터가 존재하지 않습니다.</tr></tr>";
  $('.pagination').html('');
  $('#region_list_contents').html(tableText);
}


function getList(p_CurPAGE) {
  const data = {
    REQ_MODE: CASE_LIST,
    PerPage:  $("#perPage").val(),
    CurPAGE:  p_CurPAGE,
    CSD_ID:    $("#CSD_ID").val()
  };

  console.log(data);
  callAjax(DIVISION_URL,'GET', data).then((result)=>{
    console.log('getList>>',result);
      const data = JSON.parse(result);
      console.log(data.status);
      if(data.status == OK){
        $('#indicator').hide();
        const item = data.data;
        setTable(item[1], item[0], p_CurPAGE);
      }else{
          setHaveNoDataTable();
      }
  });

}

/* 모달 초기화 함수 */
function newForm() {
  $("#REQ_MODE").val(CASE_CREATE);
  $("#CSD_ID").val('');
  $("#CSD_NM").val('');
  $("#CSD_ORDER_NUM").val('');
  $("#btnDelete").hide();
  $("#CSD_ID").removeAttr("readonly");
  $('#submitBtn').html('등록');
  $('#submitBtn').attr('onclick', 'addData()');

  $('#ORI_CSD_ORDER_NUM').val('');
  $('#IS_EXIST_ORDER_NUM').val('');


  $("#add-new").modal({
    show: true
  });
}

/* 등록 */
addData = () => {

  if( $("#REQ_MODE").val() == ''){
    $("#REQ_MODE").val(CASE_CREATE);
  }

  if(validationData()){
      const data = {
          REQ_MODE: CASE_CREATE,
          CSD_ID : $('#CSD_ID').val(),
          CSD_NM : $("#CSD_NM").val(),
          CSD_ORDER_NUM : $('#CSD_ORDER_NUM').val(),
          IS_EXIST_ORDER_NUM : $('#IS_EXIST_ORDER_NUM').val(),
          ORI_CSD_ORDER_NUM : $('#ORI_CSD_ORDER_NUM').val()
      };

      callAjax(DIVISION_URL, 'POST', data).then((result)=>{
          const data = JSON.parse(result);
          if(data.status == OK){
              confirmSwalOneButton('등록 성공하였습니다.','확인',()=>{location.replace(pageName);});
          }else if(data.status == CONFLICT){
            confirmSwalOneButton(data.message,'확인',()=>{
                $('#CSD_ID').focus();
            });
          }else if(data.status == ERROR){
            confirmSwalTwoButton(data.message,'확인', '취소', false ,()=>{
              $('#IS_EXIST_ORDER_NUM').val('false');
              addData();
          }, '진행하시겠습니까?');
          }else{
              alertSwal("서버와의 연결이 원활하지 않습니다.");
          } 
      });     
  }
  
}


/** 전체 권한관리 리스트 가져오기(엑셀다운로드 용) */
getTotalList = () => {
  const data = {
      REQ_MODE: CASE_TOTAL_LIST,
  };
  callAjax(DIVISION_URL, 'GET', Object.assign(data)).then((result)=>{
      const data = JSON.parse(result);
      if(data.status == OK){
          const item = data.data;
          location.href = "divisionExcel.html?list="+JSON.stringify(item[0]);
      }else{
          alertSwal("데이터를 가져오는데 실패하였습니다.");
      }
       
  });
}

excelDownload = () =>{
  location.href='divisionExcel.html';
}