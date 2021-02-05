const pageName = "operation.html";
const ajaxUrl = 'act/operation_act.php';
let oriFileFormHtml = '';
let uploadArray = [];   //업로드 파일 저장
let isDelArray = [];
let delIndexArray = [];
let CURR_PAGE = 1;
let tdLength = 0;

const registPopupWidth = 995; 
const registPopupHeight = 750; 
let popupX = 0;
let popupY = 0;

$(document).ready(() =>{
    $('#indicator').hide();

    /** 에디터 */
    $('.summernote').summernote({
        height: 300,                 // set editor height
        minHeight: null,             // set minimum height of editor
        maxHeight: null,             // set maximum height of editor
        focus: true                  // set focus to editable area after initializing summernote
    });

    $( "#DATE_START" ).datepicker(
        getDatePickerSetting('DATE_END','minDate')
    );
    $( "#DATE_END" ).datepicker(
        getDatePickerSetting('DATE_START','maxDate')    
    );

    
    $('#searchForm').submit(function(e){
        e.preventDefault();
        getList();   
    });

    $('#boardForm').submit(function(e){
        e.preventDefault();
        $('#MGB_TXT_DTL').val($('.summernote').summernote('code'));

        if($('#REQ_MODE').val() == CASE_UPDATE){
          confirmSwalTwoButton('수정 하시겠습니까?','확인', '취소', false ,()=>setData());
        }else{
          confirmSwalTwoButton('저장 하시겠습니까?','확인', '취소', false ,()=>setData());
        }
       
    });

    /** 게시판 구분타입 */
    setBoardType();
   
    tdLength = $('#boardTable th').length;
    setInitTableData(tdLength);
    
    // /** 검색에서 기간을 눌렀을 때 날짜 초기화 */
    $('#NMG_CTGY_SEARCH').change(()=>{
        if($('#NMG_CTGY_SEARCH').val() == ''){
            $('#DATE_START').val('');
            $('#DATE_END').val('');
        }
    });


    // 팝업 위치 계산
    let position =   getPopupPosition(registPopupWidth, registPopupHeight);
    popupX = position.popupX;
    popupY = position.popupY;

    
});

/** 파일선택 change 이벤트 */
onChangeFile = (event) => {
    const files = $(event).prop("files");
    $.each(files, (index, file)=> {
          uploadArray.push({MFF_ID : null, MFF_ORG_NM : file.name});
          delIndexArray.push(null);
          isDelArray.push('N');
    });

    const fileInput = $('input[type=file]');
    $.each(fileInput, (index, file)=> {
          $(file).hide();
    });

    $('#uploadSelectFile').append("<input type='file' name='CR_FILE_FN[]' class='form-control input-md'  multiple='multiple' onchange='onChangeFile(this)'//>");
    setUploadFileList();
}

/** 파일 업로드 전체삭제 버튼 클릭 이벤트 */
allDeleteFileList = () => {
  $('#uploadFileContainer').html(oriFileFormHtml);
  $('#uploadListContainer').html('');
  uploadArray = [];

  if($('#REQ_MODE').val() == CASE_UPDATE){
    $.each(isDelArray,(index)=>{
        isDelArray[index] = 'Y';
    });
  }else{
    isDelArray = [];
  }
}

/** 부품타입 */
setBoardType = () => {
    const data = {
        REQ_MODE: GET_PARTS_TYPE,
    };
    // console.log('boardTyp param>>>',data);
    callAjax(ajaxUrl, 'GET', data).then((result)=>{
      console.log('boardTyp SQL>>',result);
        const data = JSON.parse(result);
        console.log('data',data);
        if(data.status == OK){
            const dataArray = data.data; 

            let baseOption = '<option value="">=구분=</option>';
            let optionText = '';
            dataArray.map((item)=>{
                optionText += '<option value="'+item.MGC_ID+'">'+item.MGC_NM+'</option>';
            });
            
            $('#NMG_CTGY_SEARCH').append(baseOption+optionText);
            $('#MCG_ID').append(optionText);
            $('#MCG_ID').selectpicker('refresh');
            
            // if(objID = 'NMG_CTGY_SEARCH'){
            //   $('#'+objID).append(baseOption+optionText);
            // }else {
            //   $('#'+objID).append(optionText);
            //   $('#'+objID).selectpicker('refresh');
            // }
           
        }else{
            alert("서버와의 연결이 원활하지 않습니다.");
        }
    });
}

/** 업로드 리스트 세팅 */
setUploadFileList = () => {
  let uploadListHtml = '';  
  $.each(uploadArray, (index, file)=> {
      if(isDelArray[index]== 'N'){
        uploadListHtml += "<div class='uploadItem'>";
        uploadListHtml += "<span class='itemFileName'>"+file.MFF_ORG_NM+"</span>";
        uploadListHtml += "<button type='button' class='itemDelBtn' onclick='delUploadItem("+index+")'>&times;</button>";
        uploadListHtml += "</div>";
      } 
  });
  $('#uploadListContainer').html(uploadListHtml);
  $('#CR_FILE').val("");     // 초기화.
}

/** 업로드 리스트 삭제 */
delUploadItem = (index) => {
  isDelArray[index] = 'Y';
  setUploadFileList();
}


/** 리스트 클릭시 내용 보여주는 모달
 * @param {data : 클릭한 해당 데이터 정보}
 */
showViewModal = (boardIdx) => {
  console.log("boardIdx===>",boardIdx);
  getData(boardIdx, true).then((result)=>{
      const data = result.data;     //index 0:게시글정보 , 1:첨부파일정보
      let boadData = data;
      let uploadListHtml = ''; 

      if(data.length != undefined){
        boadData = data[0];
        $.each(data[1], (index, file)=> {
              uploadListHtml += "<div class='uploadItem'>";
              uploadListHtml += "<a href='"+ajaxUrl+"?REQ_MODE="+CASE_DOWNLOAD_FILE+"&FILE_NM="+file.MFF_FILE_NM+"&ORG_NM="+file.MFF_ORG_NM+"'>";
              uploadListHtml += "<span class='itemFileName_'>"+file.MFF_ORG_NM+"</span>";
              uploadListHtml += "</a>";
              uploadListHtml += "</div>";
        });
      }else{
        uploadListHtml = "<span class='itemFileName_'>첨부파일 없음</span>";
      }
      $('#BOARD_TITLE').html(boadData.MGB_TITLE);
      $('#BOARD_TYPE').html(boadData.MGC_NM);
      $('#BOARD_CONTENTS').html(boadData.MGB_TXT_DTL);

      $('#btnViewDelete').attr('onclick','confirmDel('+boadData.MGB_ID+')');
      $('#btnUpdate').attr('onclick','openPopup('+CASE_UPDATE+','+boadData.MGB_ID+')');
      
      $('#BOARD_FILE').html(uploadListHtml);
      $('#notice-view').modal();
  });
}


/* 등록모달창일경우 */
openPopup = (req,id) => {
  window.open('./operationPopup.html?REQ_MODE='+req+'&MGB_ID='+id, 
            'addPopup', 
            'width='+registPopupWidth+',height='+registPopupHeight+',left='+ popupX + ', top='+ popupY);
}

onLoadPopupSetting = (req, id) => {
  // setBoardType('MCG_ID');
  $('#REQ_MODE').val(req);
  $('#MGB_ID').val(id);
  if(req == CASE_CREATE){
    $('#submitBtn').html('등록');
  }else if(req == CASE_UPDATE){
    setUpdateData(id);
  }
}

/* 수정 팝업창일경우 */
setUpdateData = (boardIdx) => {
  getData(boardIdx).then((data)=>{
    const item = data.data;     //index 0:게시글정보 , 1:첨부파일정보
    let boadData = item;

    if(item.length != undefined){
      boadData = item[0];
    }

    $('#submitBtn').html('수정');
    $('#MGB_ID').val(boadData.MGB_ID);
    $('#MGB_TITLE').val(boadData.MGB_TITLE);
    $("#MCG_ID").val(boadData.MCG_ID);
    $("#MCG_ID").selectpicker("refresh");
    $('.summernote').summernote('code', boadData.MGB_TXT_DTL);

    if(item.length != undefined){
      uploadArray  = item[1];
      $.each(uploadArray, (index, item)=> {
        isDelArray.push('N');
        delIndexArray.push(item.MFF_ID);
      });
      setUploadFileList();
    }
  });
}

validationData = () => {
    if($('#MGB_TITLE').val().trim()==''){
      alert("제목을 입력해주세요.");
      $('#MGB_TITLE').focus();
      return false;
    }else if($('#MCG_ID').val()==''){
      alert("구분을 선택해주세요.");
      $('#MCG_ID').selectpicker('toggle');
      return false;
    }else if($('.summernote').summernote('code').trim()==''){
      alert("내용을 입력해주세요.");
      $('.summernote').summernote('focus');
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
        tableText += "<td class='text-center'>"+data.WRT_DTHMS+"</td>";
        tableText += "<td class='text-center'>"+data.MGC_NM+"</td>";
        tableText += "<td class='text-left overText'><a href='javascript:showViewModal("+data.MGB_ID+")'>"+data.MGB_TITLE+"</a></td>";
        tableText += "<td class='text-center'>"+data.WRT_USERID+"</td>";
        if(Number(data.FILE_COUNT) > 0 ){
          tableText += "<td class='text-center'><img src='/images/icon_download.gif'></img></td>";
        }else{
          tableText += "<td class='text-center'></td>";
        }
        tableText += "<td class='text-center'>";
        if($("#AUTH_UPDATE").val() == YN_Y){
          tableText += "<button type='button' class='btn btn-default' onclick='openPopup("+CASE_UPDATE+","+data.MGB_ID+")'>수정</button>";
        }
        if($("#AUTH_DELETE").val() == YN_Y){
          tableText += "<button type='button' class='btn btn-danger' onclick='confirmDel("+data.MGB_ID+")'>삭제</button>";
        }
        tableText += "</td>";
        tableText += "<td class='text-center'>"+data.MGB_VIEW_CNT+"</td>";
        tableText += "</tr>"
        
    });
    paging(totalListCount, currentPage, 'getList'); 
    $('#dataTable').html(tableText);
}

/** 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
    const tableText = "<tr><td class='text-center' colspan='"+tdLength+"'>조회요청한 데이터가 존재하지 않습니다.</td></tr>";
    $('.pagination').html('');
    $('#dataTable').html(tableText);
}

getSearchData = () => {
  const searchData = {
      /** 검색 */
      NMG_CTGY_SEARCH : $('#NMG_CTGY_SEARCH').val(),
      DATE_START : $('#DATE_START').val(),
      DATE_END : $('#DATE_END').val(),
      MGB_TITLE_SEARCH : $('#MGB_TITLE_SEARCH').val(),
      WRITER_SEARCH : $('#WRITER_SEARCH').val()
  }

  return searchData;
}

/**권한관리 리스트 */
getList = (currentPage) => {
    CURR_PAGE = (currentPage == undefined) ? CURR_PAGE : currentPage;

    const data = {
        REQ_MODE: CASE_LIST,
        PER_PAGE:  $("#perPage").val(),
        CURR_PAGE:  CURR_PAGE,
    };

    callAjax(ajaxUrl, 'GET', Object.assign(data, getSearchData())).then((result)=>{
        const data = JSON.parse(result);
        if(data.status == OK){
            const item = data.data;
            setTable(item[1], item[0], CURR_PAGE);
        }else{
            setHaveNoDataTable();
        }
    });
    
}


/** 등록/수정시 데이터 set 해주는 함수 */
setData = () => {
  if(validationData()){
      let isDelHtml = '';
      let isDelIdexHtml = '';
      $.each(isDelArray, (index, isDel)=> {
        isDelHtml += "<input type='hidden' id='IS_DEL_FILE' name='IS_DEL_FILE[]' value='"+isDel+"'/> ";
        isDelIdexHtml += "<input type='hidden' id='DEL_INDEX' name='DEL_INDEX[]' value='"+delIndexArray[index]+"'/> ";
      });

      $('#isDelField').html(isDelHtml);
      $('#isDelField').append(isDelIdexHtml);

      const form = $("#boardForm")[0];
      const formData = new FormData(form);

      callAjaxFile(ajaxUrl,  formData).then((result)=>{
          const data = JSON.parse(result);
          if(data.status == OK){
              let confirmText = "등록에 성공하였습니다.";
              if($('#REQ_MODE').val() == CASE_UPDATE){
                confirmText = '수정에 성공하였습니다.';
              }

              confirmSwalOneButton(confirmText,'확인',()=>{
                window.opener.getList();
                self.close();
                if($('#REQ_MODE').val() == CASE_UPDATE){
                  window.opener.$('#notice-view').modal('hide');
                }
              });
          }else{
              alert("서버와의 연결이 원활하지 않습니다.");
          } 
          
      }); 

  } 
}

/** 리스트에서 클릭해서 디테일 페이지 들어갔을 때 데이터 세팅 
 * @param {boardIdx : 해당게시판 유니크번호, isView : 뷰모드인지 아닌지. 이거에 따라 카운트 올려주고 안올려주고 결정}
*/
getData = (boardIdx, isView) => {
    const data = {
        REQ_MODE: CASE_READ,
        MGB_ID :  boardIdx,   
        IS_VIEW : (isView!=undefined) ? true : ''  
    };

    return new Promise(function (resolve, reject) {
       return callAjax(ajaxUrl, 'GET', data).then((result)=>{
                  const data = JSON.parse(result);
                  if(data.status == OK){
                    resolve(data);
                  }else{
                      alert("해당 게시판정보를 가져오는데 실패하였습니다.");
                  }
              });
    });
}


confirmDel = (delIdx) => {
  confirmSwalTwoButton('삭제하시겠습니까?','삭제','취소', true,()=>{delData(delIdx)});
}

delData = (delIdx) => {
    const data = {
        REQ_MODE: CASE_DELETE,
        MGB_ID : delIdx,
    };
    callAjax(ajaxUrl, 'POST', data).then((result)=>{
        const data = JSON.parse(result);
        if(data.status == OK){
            confirmSwalOneButton('삭제에 성공하였습니다.','확인',()=>{
                getList();
            });
        }else{
            const resultText ='서버와의 연결이 원활하지 않습니다.' ;
            alert(resultText);
        } 
    });
}


  