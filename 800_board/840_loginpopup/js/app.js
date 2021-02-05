const pageName = "loginpopup.html";
const ajaxUrl = 'act/loginPopup_act.php';
let CURR_PAGE = 1;

const registPopupWidth = 995; 
const registPopupHeight = 700; 
let popupX = 0;
let popupY = 0;

$(document).ready(() =>{
  getList(1);

  // 편집 에디터
  $('.summernote').summernote({
    height: 300,                 // set editor height
    minHeight: null,             // set minimum height of editor
    maxHeight: null,             // set maximum height of editor
    focus: true                  // set focus to editable area after initializing summernote
  });

  $('#boardForm').submit(function(e){
    e.preventDefault();
    $('#NP_DTL').val($('.summernote').summernote('code'));

    if($('#REQ_MODE').val() == CASE_UPDATE){
      confirmSwalTwoButton('수정 하시겠습니까?','확인', '취소', false ,()=>updateData());
    }else{
      confirmSwalTwoButton('저장 하시겠습니까?','확인', '취소', false ,()=>addData());
    }
  
  
  });

  $( "#START_DT" ).datepicker(
    getDatePickerSetting('END_DT','minDate')
  );
  $( "#END_DT" ).datepicker(
      getDatePickerSetting('START_DT','maxDate')    
  );
  
  // /** 검색에서 기간을 눌렀을 때 날짜 초기화 */
  $('#SELECT_DT').change(()=>{
      if($('#SELECT_DT').val() == ''){
          $('#START_DT').val('');
          $('#END_DT').val('');
      }
  });
  $('#KEYWORD_SELECT').change(()=>{
      if($('#KEYWORD_SELECT').val() == ''){
          $('#KEYWORD_SEARCH').val('');
      }
  });


  // 팝업 위치 계산
  let position =   getPopupPosition(registPopupWidth, registPopupHeight);
  popupX = position.popupX;
  popupY = position.popupY;

  
});

/** 등록모달창일경우 */
openPopup = (req,id) => {
  window.open('./loginPopupModal.html?REQ_MODE='+req+'&NP_ID='+id, 
            'loginPopup', 
            'width='+registPopupWidth+',height='+registPopupHeight+',left='+ popupX + ', top='+ popupY);
}

onLoadPopupSetting = (req, id) => {
  $('#REQ_MODE').val(req);
  $('#NP_ID').val(id);
  if(req == CASE_CREATE){
    $('#submitBtn').html('등록');
  }else if(req == CASE_UPDATE){
    setUpdateData(id);
  }
}

/* 업데이트 모달창일경우 */
setUpdateData = (id) => {
  const param = { 
    REQ_MODE :CASE_READ,
    NP_ID: id
  }
  callAjax(ajaxUrl, 'GET', param).then((result)=>{
      const res = JSON.parse(result);
      if(res.status == OK){
        const data = res.data;
        console.log('LOGIN DATA >>>>',data);
        // $('#notice-view').hide();
        $('#btnDelete').show();
        $('#btnDelete').val(data.NP_ID);
        $('#submitBtn').html('수정');

        $('#NP_TITLE').val(data.NP_TITLE);
        $('#NP_DTL').val(data.NP_DTL);
        $('#NP_SHOW_YN').val(data.NP_SHOW_YN);
        $('.summernote').summernote('code', data.NP_DTL);

      }else{
          alert('팝업 정보를 불러올 수 없습니다. 재시도 바랍니다.');
          window.close();
      }
  });
}



/** 등록버튼 눌렀을 때 */
addData = () => {
      const data = {
          REQ_MODE: CASE_CREATE,

          NP_TITLE : $('#NP_TITLE').val(),
          NP_SHOW_YN : $('#NP_SHOW_YN').val(),
          NP_DTL : $('.summernote').summernote('code')
      };
      

      callAjax(ajaxUrl, 'POST', data).then((result)=>{
          const data = JSON.parse(result);
          if(data.status == OK){
              confirmSwalOneButton('등록 성공하였습니다.','확인',()=>{
                window.opener.getList();
                self.close();
              });
          }else if(data.status == ERROR){
              alert(data.message);
              $('#CPP_ID').focus();
              
          }else{
              alert("서버와의 연결이 원활하지 않습니다.");
          } 
      });     
  
}


/** 업데이트 */
updateData = () => {
    const data = {
        REQ_MODE: CASE_UPDATE,
        
        NP_ID : $('#btnDelete').val(),
        NP_TITLE : $('#NP_TITLE').val(),
        NP_SHOW_YN : $('#NP_SHOW_YN').val(),
        NP_DTL : $('.summernote').summernote('code')
        
    };

    console.log('updateData',data);
    callAjax(ajaxUrl, 'POST', data).then((result)=>{
        const data = JSON.parse(result);
        if(data.status == OK){
            confirmSwalOneButton('수정에 성공하였습니다.','확인',()=>{
              window.opener.getList();  
              self.close();
            });
        }else{
            alert("서버와의 연결이 원활하지 않습니다.");
        }   
    });

}


validationData = () => {
  if($('#MVV_ANSWER').val().trim()==''){
    alert("답변을 입력해주세요.");
    $('#MVV_ANSWER').focus();
    return false;
  }else{
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
      tableText += "<td class='text-left overText'>"+data.NP_TITLE+"</td>";
      tableText += "<td class='text-center'>"+(data.NP_SHOW_YN == "Y" ? "노출" : "숨김")+"</td>";
      tableText += "<td class='text-center'>"+data.WRT_DTHMS+"</td>";
      tableText += "<td class='text-center'>"+data.WRT_USERID+"</td>";
      if($("#AUTH_UPDATE").val() == YN_Y){
        tableText += "<td class='text-center'><button type='button' class='btn btn-default' onclick='openPopup("+CASE_UPDATE+","+data.NP_ID+")'>수정</button></td>";
      }
      tableText += "</tr>"
      
  });
  paging(totalListCount, currentPage, 'getList'); 
  $('#dataTable').html(tableText);
}

/** 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
  const tableText = "<tr><td class='text-center' colspan='5'>조회요청한 데이터가 존재하지 않습니다.</td></tr>";
  $('.pagination').html('');
  $('#dataTable').html(tableText);
}


/**리스트 */
getList = (currentPage) => {
  CURR_PAGE = (currentPage == undefined) ? CURR_PAGE : currentPage;

  const data = {
      REQ_MODE: CASE_LIST,
      PER_PAGE:  $("#perPage").val(),
      CURR_PAGE:  CURR_PAGE,

      /** 검색 */
      SELECT_DT: $('#SELECT_DT').val(),
      START_DT: $('#START_DT').val(),
      END_DT: $('#END_DT').val(),
      TITLE_SEARCH: $('#TITLE_SEARCH').val(),
      CONTENT_SEARCH: $('#CONTENT_SEARCH').val(),
      EXPOSURE_SEARCH: $('#EXPOSURE_SEARCH').val()
  };

  console.log(data);
  callAjax(ajaxUrl, 'GET', data).then((result)=>{
      const data = JSON.parse(result);
      console.log(data);
      if(data.status == OK){
          $('#indicator').hide();
          const item = data.data;
          setTable(item[1], item[0], CURR_PAGE);
      }else{
          setHaveNoDataTable();
      }
  });
  
}



delData = () => {
  const data = {
      REQ_MODE: CASE_DELETE,
      NP_ID : $('#btnDelete').val(),
  };
  callAjax(ajaxUrl, 'POST', data).then((result)=>{
      const data = JSON.parse(result);
      if(data.status == OK){
          confirmSwalOneButton('삭제에 성공하였습니다.','확인',()=>{
            window.opener.getList();  
            self.close();
          });
      }else{
          const resultText ='서버와의 연결이 원활하지 않습니다.' ;
          alert(resultText);
      } 
  });
}