const NOTICE_URL = 'act/notice_act.php';
let CURR_PAGE = 1;
let modeArray = [];
let tdLength = 0 ;

const registPopupWidth = 995; 
const registPopupHeight = 860; 
let popupX = 0;
let popupY = 0;
let noticeArray = [];

/* 내용 에디터 */
$('.summernote').summernote({
  width: 400,
  height: 300,                 // set editor height
  minHeight: null,             // set minimum height of editor
  maxHeight: null,             // set maximum height of editor
  focus: true                  // set focus to editable area after initializing summernote
});

$(document).ready(() =>{
  if($('#REQ_MODE').val() == undefined){
    callModeData('SEARCH_MNN_MODE_TGT');
  }
  setIwOptions('SEARCH_IW','','=보험사=');
  setGwOptions('SEARCH_GW','','=공업사=');
  tdLength = $('table tr th').length;
  setInitTableData(tdLength, 'notice_contents');
  $( "#SEARCH_START_DT" ).datepicker(
    getDatePickerSetting('SEARCH_END_DT','minDate')
  );
  $( "#SEARCH_END_DT" ).datepicker(
    getDatePickerSetting('SEARCH_START_DT','maxDate')    
  );

  $('#indicator').hide();
  $('#DIV_ORDER').hide();
  

  /** 검색버튼 */
  $('#searchForm').submit(function(e){
      e.preventDefault();
        getList(1);
  });

  $("#noticeForm").submit(function(e){
    setTGT();
    e.preventDefault();
    if(validationData()){
      if($('#REQ_MODE').val() == CASE_CREATE){
        confirmSwalTwoButton('저장 하시겠습니까?','확인', '취소', false ,()=> addData());
      } else {
        confirmSwalTwoButton('수정 하시겠습니까?','확인', '취소', false ,()=>updateData());
  
      }
    }
    
  });

  let position =   getPopupPosition(registPopupWidth, registPopupHeight);
  popupX = position.popupX;
  popupY = position.popupY;

  

});

/* 등록창에서 글권한에 따라서 보험사, 공업사 동적으로 활성화  */
chkTGT = (data) => {
  const target = $("#MNN_MODE_TGT").val();
  if(target == MODE_TARGET_IW) {
    $('#DIV_TGT_IW').show();
    $('#DIV_TGT_GW').hide();
    if( data != undefined){
      setIwOptions('SELECT_IW',data.MII_ID);
    }else {
      setIwOptions('SELECT_IW');
    }
    
  }else if(target == MODE_TARGET_GW) {
    $('#DIV_TGT_IW').hide();
    $('#DIV_TGT_GW').show();
    if( data != undefined){
      setGwOptions('SELECT_GW',data.MGG_ID);
    }else {
      setGwOptions('SELECT_GW');
    }
  }else { // target == MODE_TARGET_ALL || target == MODE_TARGET_SA
    $('#DIV_TGT_IW').hide();
    $('#DIV_TGT_GW').hide();
  }

}

setTGT = () =>{
  const target = $("#MNN_MODE_TGT").val();
  if(target == MODE_TARGET_IW) {
    $('#MNN_TGT_ID').val($('#SELECT_IW').val());
  }else if(target == MODE_TARGET_GW) {
    $('#MNN_TGT_ID').val($('#SELECT_GW').val());
  }
  console.log('tgt_id>>>', $('#MNN_TGT_ID').val());
}

/* 게시글 고정 여부에 따라  */
noticeFix = () =>{
  if($("input:checkbox[id='MNN_MARK_YN']").is(":checked") == true){
     $('#DIV_ORDER').show();
   } else {
       $('#DIV_ORDER').hide();
       $('#MNN_ORDER_NUM').val('');
   }
}


/* main 리스트 */
getList = (currentPage) => {
  CURR_PAGE = (currentPage == undefined) ? CURR_PAGE : currentPage;

  const param = {
      REQ_MODE: CASE_LIST,
      PER_PAGE:  $("#perPage").val(),
      CURR_PAGE:  CURR_PAGE,

      /** 검색 */
      DT_KEY_SEARCH: $('#DT_KEY_SEARCH').val(),
      START_DT_SEARCH: $('#SEARCH_START_DT').val(),
      END_DT_SEARCH: $('#SEARCH_END_DT').val(),
      SEARCH_MNN_MODE_TGT: $('#SEARCH_MNN_MODE_TGT').val(),
      SEARCH_TITLE: $('#SEARCH_TITLE').val(),
      SEARCH_WRITER: $('#SEARCH_WRITER').val(),
      SEARCH_FILE: $('#SEARCH_FILE').val(),
      SEARCH_IW: $('#SEARCH_IW').val(),
      SEARCH_GW: $('#SEARCH_GW').val(),

  };
console.log('getList param',param);
  callAjax(NOTICE_URL, 'GET', param).then((result)=>{
      console.log('getLIST SQL >>',result);
      const data = JSON.parse(result);
      // console.log(data);
      if(data.status == OK){
          const item = data.data;
          setTable(item[1], item[0], CURR_PAGE);
      }else{
          setHaveNoDataTable();
      }
  });
  
}

callModeData = (objID) => {
  const param = { 
    REQ_MODE :GET_REF_MAIN_TARGET_TYPE
  }
  callAjax(NOTICE_URL, 'GET', param).then((result)=>{
      const res = JSON.parse(result);
      // console.log('MODE DATA >>>>',res);
      if(res.status == OK){
        modeArray = res.data;
        setModeTgt(objID,res.data);
      }else{
          alert('글권한 리스트를 불러올수 없습니다. 재시도 바랍니다.');
      }
  });
}

/* 글권한 동적 생성 */
setModeTgt = (objID,item) =>{
  let optionText = '';
  optionText += '<option value="" selected>=구분=</option>';
  mode = item == undefined ? modeArray : item;
  mode.map((data,index)=>{
    optionText += '<option value="'+data.CCD_ID+'">'+data.CCD_NM+'</option>';
  });
  // console.log('setModeTgt>>', objID);
  $('#'+objID).html(optionText);
  return optionText;
}

/** 리스트 테이블 동적으로 생성 
 * @param {dataArray : 리스트 데이터, currentPage : 현재페이지(페이징을 위해)} 
*/
setTable = (totalListCount, dataArray, currentPage) => {
  let tableText = '';

  dataArray.map((data, index)=>{
      // tableText += "<tr style='cursor : pointer' onclick='showViewModal("+JSON.stringify(data)+")'>";
      tableText += "<tr style='cursor : pointer' onclick='showViewModal("+index+")'>";
      tableText += "<td class='text-center'>"+data.IDX+"</td>";
      tableText += "<td class='text-center'>"+data.NOTICE_DT+"</td>";
      tableText += "<td class='text-center'>"+nullChk(data.MNN_MODE_TGT_NM,'-')+"</td>";
      tableText += "<td class='text-center'>"+nullChk(data.MII_NM,'-')+"</td>";
      tableText += "<td class='text-left'>"+nullChk(data.MGG_NM,'-')+"</td>";
      tableText += "<td class='text-center'>"+bizNoFormat(data.MGG_BIZ_ID)+"</td>";
      tableText += "<td class='text-left overText'>"+data.MNN_TITLE+"</td>";
      tableText += "<td class='text-center'>"+data.WRT_USERID+"</td>"
      if(data.FILE_YN == YN_Y){
        tableText += "<td class='text-center'>"+"<img src='/images/icon_download.gif'>"+"</td>";
      }else{
        tableText += "<td class='text-center'></td>";
      }
      tableText += "<td class='text-center'>"+data.MNN_VIEW_CNT+"</td>"
      tableText += "<td class='text-center'>"+data.WRT_DTHMS+"</td>"
      tableText += "</tr>"
      
  });
  noticeArray = dataArray;
  paging(totalListCount, currentPage, 'getList'); 
  $('#notice_contents').html(tableText);
}

/** 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
  const tableText = "<tr><td class='text-center' colspan='"+tdLength+"'>조회요청한 데이터가 존재하지 않습니다.</td></tr>";
  $('.pagination').html('');
  $('#notice_contents').html(tableText);
}


/* 등록모달창일경우 */
openPopup = (req,id) => {
  window.open('./noticePopup.html?REQ_MODE='+req+'&MNN_ID='+id, 
            'smswindow', 
            'width='+registPopupWidth+',height='+registPopupHeight+',left='+ popupX + ', top='+ popupY);
}

onLoadPopupSetting = (req, id) => {
  callModeData('MNN_MODE_TGT');
  if(req == CASE_CREATE){

    $('#noticeForm').each(function(){
      this.reset();
    });
    $('#REQ_MODE').val(CASE_CREATE);
    $('#DIV_MNN_TXT').hide();
    $('#DIV_FILE').hide();
    $('#btnDelete').hide();
    $('#submitBtn').html('등록');

    chkTGT();
    $( "#MMN_START_DT" ).datepicker(
      getDatePickerSetting('MMN_END_DT','minDate')
    );
    $( "#MMN_END_DT" ).datepicker(
      getDatePickerSetting('MMN_START_DT','maxDate')    
    );
    $('.summernote').summernote('code','');
  }else if(req == CASE_UPDATE){
    setUpdateData(id);
  }
}

validationData = () => {
  $("#MNN_TXT_DTL").val($('.summernote').summernote('code'));

  /* validation check */
  if($('#MNN_TITLE').val() == ''){
    alert('제목을 입력해주세요');
    $('#MNN_TITLE').focus();
    return false;
  }else if($('#MNN_TXT_DTL').val() == ''){
    alert('내용을 입력해주세요');
    $('#MNN_TXT_DTL').focus();
    return false;
  }else if($('#MMN_START_DT').val() == ''){
    alert('게시시작일을 입력해주세요');
    $('#MMN_START_DT').focus();
    return false;
  }else if($('#MMN_END_DT').val() == ''){
    alert('게시종료일을 입력해주세요');
    $('#MMN_END_DT').focus();
    return false;
  }
  else if($('#MNN_MARK_YN').is(":checked") && $('#MNN_ORDER_NUM').val() == ''){
    alert('게시글 고정시 출력순서는 필수입력입니다.');
    return false; 
  }else {
    return true;
  }
}

/* 등록버튼 눌렀을 때 */
addData = () => {
  let form = $("#noticeForm")[0];
  let formData = new FormData(form);
 
  callAjaxFile(NOTICE_URL, formData).then((result)=>{
    const data = JSON.parse(result);
    if(data.code == OK){
      confirmSwalOneButton('등록에 성공하였습니다.','확인',()=>{
        $('.summernote').summernote('destroy');
        window.opener.getList();
        window.close();
      });
    }else{
      console.log('callAjaxFile add error>>',data);
    }
  });

}

/* 게시글보기 모달창일경우 */
showViewModal = (idx) => {
  const data = noticeArray[idx];
  console.log('showViewModal',data);
  $('#notice-view').modal();
  $('#btnUpdate').attr('onclick', "showUpdateModal("+JSON.stringify(data)+")");
  $('#DIV_MNN_TXT').hide();
  $('#btnDelete').show();
  $('#MFF_FILE_NM').val('');
  $('#MNN_ID').val(data.MNN_ID);
  $('#NOTICE_TITLE').text(data.MNN_TITLE);
  $('#NOTICE_MODE').text(nullChk(data.MNN_MODE_TGT_NM,'-'));
  $('#NOTICE_TGT').html(data.MNN_MODE_TGT_NM);
  /**
   * 구분에 따라서 보험사, 공업사 보여주기. 
   * 보험사/공업사 추가 예정  */ 
  if(data.MNN_MODE_TGT_CD == MODE_TARGET_ALL || data.MNN_MODE_TGT_CD == MODE_TARGET_SA){ //전체 구분일때 보험사,공업사 숨기기
    $('#DIV_NOTICE_TGT').hide();
  }else if(data.MNN_MODE_TGT_CD == MODE_TARGET_IW){
    $('#DIV_NOTICE_TGT').show();
    $('#NOTICE_TGT_NM').html(data.MII_NM);
    $('#NOTICE_TGT_NM_LABEL').html('보험사');
  }else if(data.MNN_MODE_TGT_CD == MODE_TARGET_GW){
    $('#DIV_NOTICE_TGT').show();
    $('#NOTICE_TGT_NM').html(data.MGG_NM);
    $('#NOTICE_TGT_NM_LABEL').html('공업사');

  }
  if(data.MNN_MARK_YN == YN_N){
    $('#DIV_NOTICE_FIX').hide();
  }else{
    $('#NOTICE_FIX').attr('checked',true);
    $('#NOTICE_ORDER_NUM').val(data.MNN_ORDER_NUM);
  }
  $('#NOTICE_CONTENTS').html(data.MNN_TXT_DTL);

  

  if(data.FILE_YN == YN_Y){
    $('#DIV_FILE').show();
    // console.log('data.MFF_ORG_NM',data.MFF_ORG_NM,'/ ',data.MFF_FILE_NM);
    let strSpan = "<a href='act/notice_act.php?REQ_MODE="+CASE_DOWNLOAD_FILE+"&FILE_NM="+data.MFF_FILE_NM+"&ORG_NM="+data.MFF_ORG_NM+"' target='_blank'>";
    strSpan += " <span id='FILE_DOWN_NM' name='FILE_DOWN_NM' value='Y' >"+data.MFF_ORG_NM+"</span>";
    strSpan += "</a>";
    $('#NOTICE_FILE').html(strSpan);
  } else {
    console.log('have no file');
    $('#NOTICE_FILE').html('');
  }
  /* view_cnt +1 */
  const param ={
    REQ_MODE: CASE_READ,
    MNN_ID: data.MNN_ID
  }
  callAjax(NOTICE_URL, 'GET', param).then((result)=>{
    const res = JSON.parse(result);
    console.log('VIEW_CNT RESULT>>',res);
});

}


/* 업데이트 모달창일경우 */
setUpdateData = (id) => {
  const param = { 
    REQ_MODE :CASE_TOTAL_LIST,
    MNN_ID: id
  }
  callAjax(NOTICE_URL, 'GET', param).then((result)=>{
      const res = JSON.parse(result);
      if(res.status == OK){
        const data = res.data[0];
        console.log('Notice DATA >>>>',data);
        // $('#notice-view').hide();
        $('#REQ_MODE').val(CASE_UPDATE);
        $('#DIV_MNN_TXT').hide();
        $('#btnDelete').show();
        $('#submitBtn').html('수정');
        $('#MFF_FILE_NM').val('');
        
        $('#MNN_MODE_TGT').val(data.MNN_MODE_TGT_CD);
        chkTGT(data);
        if(data.MNN_MARK_YN == YN_N){
          $('#DIV_NOTICE_FIX').hide();
        }else{
          $('#DIV_ORDER').show();
          $('#MNN_MARK_YN').attr('checked',true);
          $('#MNN_ORDER_NUM').val(data.MNN_ORDER_NUM);
        }
      
        $('#MNN_ID').val(data.MNN_ID);
        $('#MNN_TITLE').val(data.MNN_TITLE);
      
        $('#MMN_START_DT').val(data.MMN_START_DT);
        $('#MMN_END_DT').val(data.MMN_END_DT);
        $('#MMN_START_DT').datepicker({
            dateFormat : 'yy-mm-dd',
        });
        $('#MMN_END_DT').datepicker({
            dateFormat : 'yy-mm-dd',
        });
      
        $("#MNN_TXT_DTL").val(data.MNN_TXT_DTL);
        $('.summernote').summernote('code',data.MNN_TXT_DTL);
        
        if(data.FILE_YN == YN_Y){
          $('#DIV_FILE').show();
          $('#FILE_DOWN_NM').html(data.MFF_ORG_NM);
          $('#MFF_ID').val(data.MFF_ID);
          $('#deleteFile').val(data.MNN_ID);
          $('#deleteFile').attr('onclick', 'delFile()');
        } else {
          $('#DIV_FILE').hide();
        }
      }else{
          alert('공지사항 정보를 불러올 수 없습니다. 재시도 바랍니다.');
          window.close();
      }
  });
}


/* 업데이트 */
updateData = () => {
  let form = $("#noticeForm")[0];
  let formData = new FormData(form);

  callAjaxFile(NOTICE_URL, formData).then((result)=>{
    const data = JSON.parse(result);
    if(data.code == OK){
      confirmSwalOneButton('수정에 성공하였습니다.','확인',()=>{
        $('.summernote').summernote('destroy');
        window.opener.getList();
        window.close();
        window.opener.$('#notice-view').modal('hide');
      });
    }else{
      console.log('callAjaxFile updatr error>>',data);
    }
  });
 
}

/* 첨부파일 삭제함수 */
delFile = () => {
  confirmSwalTwoButton('첨부파일을 삭제하시겠습니까?','삭제','취소',true,()=>{
    $('#FILE_DEL_YN').val('Y');
    $('#DIV_FILE').hide();
  });
}


/* 데이터 삭제함수 */
function delData() {
  const data = {
      REQ_MODE: CASE_DELETE,
      MNN_ID:   $("#MNN_ID").val()
  };
  callAjax(NOTICE_URL, 'POST', data).then((result)=>{
      const data = JSON.parse(result);
      if(data.status == OK){
          confirmSwalOneButton(
            '삭제에 성공하였습니다.',
            '확인',
            ()=>{
              if($('#REQ_MODE').val() == undefined){
                getList();
                $("#notice-view").modal("hide");
              }else {
                window.opener.getList()
                window.close();
                window.opener.$('#notice-view').modal('hide');
              }
            }
          );
      }else{
          const resultText ='서버와의 연결이 원활하지 않습니다.' ;
          alert(resultText);
      } 
  });
}