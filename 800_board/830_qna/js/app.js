const QNA_RECEIVE = "90110" //접수
const QNA_TEMP_SAVE = "90120" //임시저장
const QNA_COMPLETE = "90190" //처리완료

const pageName = "qna.html";
const ajaxUrl = 'act/qna_act.php';
let CURR_PAGE = 1;

//공업사방문주기 보이기, 숨기기
$(".subtable-btn").click(function(){
    if( $(this).html() == '공업사정보 숨기기' ) {
      $(this).html('공업사정보 보이기');
      $(".qna-subtable").hide();
    }
    else {
      $(this).html('공업사정보 숨기기');
      $(".qna-subtable").show();
    }
})

$(document).ready(() =>{
    $('#indicator').hide();
    setInitTableData(12, 'dataTable');


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

});


/** 업데이트 모달창일경우(해당 게시글 정보와 첨부파일 정보를 가져온다.)
 * 
*/
showUpdateModal = (data) => {
    $("#btnDelete").show();
    $("#tempBtn").attr('onclick', 'setData('+QNA_TEMP_SAVE+')');
    $('#btnDelete').val(data.MVV_ID);
    $("#submitBtn").attr('onclick', 'setData('+QNA_COMPLETE+')');
    if(data.MVV_ANSWER == ''){
        $("#submitBtn").html('응답');
    }else{
        $("#submitBtn").html('수정');
    }
    $('#REQ_MODE').val(CASE_UPDATE);
  
    $('#WRT_DTHMS').html(data.WRT_DTHMS);
    $('#MVV_PROC_STATUS_NM').html(data.MVV_PROC_STATUS_NM);
    $('#LAST_DTHMS').html(data.LAST_DTHMS);
    $('#WRT_USERID').html((data.MVV_ANSWER_USERID == null ? '' : data.MVV_ANSWER_USERID));

    $('#CSD_NM').html(data.CSD_NM);
    $('#MGG_NM').html(data.MGG_NM);
    $('#MGG_FRONTIER_NM').html(data.MGG_FRONTIER_NM);
    $('#MGG_BIZ_START_DT').html(data.MGG_BIZ_START_DT);
    $('#MGG_BIZ_END_DT').html(data.MGG_BIZ_END_DT);
    $('#RSR_NM').html(data.RSR_NM);
    $('#MGG_OTHER_BIZ_NM').html(data.MGG_OTHER_BIZ_NM);
    $('#MGG_BIZ_ID').html(bizNoFormat(data.MGG_BIZ_ID));
    $('#MGG_CEO_NM').html(data.MGG_CEO_NM);
    $('#MGG_CEO_TEL').html(phoneFormat(data.MGG_CEO_TEL));
    $('#MGG_OFFICE_TEL').html(phoneFormat(data.MGG_OFFICE_TEL));
    $('#MGG_OFFICE_FAX').html(phoneFormat(data.MGG_OFFICE_FAX));
    $('#MGG_INSU_MEMO').html(data.MGG_INSU_MEMO);
    $('#MGG_MANUFACTURE_MEMO').html(data.MGG_MANUFACTURE_MEMO);

    $('#MVV_QUESTION').val(data.MVV_QUESTION);
    $('#MVV_QUESTION').attr('readonly', true);

    $('#MVV_ANSWER').val('');
    $('#MVV_ANSWER').val(data.MVV_ANSWER);
    $('#MVV_DONE_DTHMS').html(data.MVV_DONE_DTHMS);
  
    $('#qna-view').modal();
}


/** 응답, 수정*/
setData = (tmp) => {

    confirmSwalTwoButton('저장 하시겠습니까?','저장', CANCEL_MSG, false, ()=>{
        if(validationData()){
    
            const data = {
                REQ_MODE:  $('#REQ_MODE').val(),
                MVV_ANSWER : $("#MVV_ANSWER").val(),
                MVV_ID : $("#btnDelete").val(),
                MVV_PROC_STATUS_CD: tmp
                
            };
            
            console.log(data);
            callAjax(ajaxUrl, 'post', data).then((result)=>{
                console.log(result);
                const data = JSON.parse(result);
                if(data.status == OK){
                    let confirmText = "등록에 성공하였습니다.";
                    if($('#REQ_MODE').val() == CASE_UPDATE){
                    confirmText = '수정에 성공하였습니다.';
                    }
                    confirmSwalOneButton(confirmText,'확인',()=>{
                        $('#qna-view').modal('hide');
                        getList(CURR_PAGE);

                    });
                }else{
                    alert("서버와의 연결이 원활하지 않습니다.");
                } 
                  
            }); 
        
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
        tableText += "<tr onclick='showUpdateModal("+JSON.stringify(data)+")'>";
        tableText += "<td class='text-center'>"+data.IDX+"</td>";
        tableText += "<td class='text-left'>"+data.MGG_NM+"</td>";
        tableText += "<td class='text-center'>"+data.RET_NM+"</td>";
        tableText += "<td class='text-center'>"+data.RCT_NM+"</td>";
        tableText += "<td class='text-center'>"+data.MVC_NM+"</td>";
        tableText += "<td class='text-left'>"+data.MVV_QUESTION+"</td>";
        tableText += "<td class='text-left'>"+(data.MVV_ANSWER == null ? '' : data.MVV_ANSWER)+"</td>";
        tableText += "<td class='text-center'>"+data.WRT_USERID+"</td>";
        tableText += "<td class='text-center'>"+data.WRT_DTHMS+"</td>";
        tableText += "<td class='text-center'>"+data.MVV_PROC_STATUS_NM+"</td>";
        tableText += "<td class='text-center'>"+(data.MVV_DONE_DTHMS == null ? '' : data.MVV_DONE_DTHMS)+"</td>";
        tableText += "<td class='text-center'>"+(data.MVV_ANSWER_USERID == null ? '' : data.MVV_ANSWER_USERNM +'('+data.MVV_ANSWER_USERID+')')+"</td>";
        tableText += "</tr>"
        
    });
    paging(totalListCount, currentPage, 'getList'); 
    $('#dataTable').html(tableText);
}

/** 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
    const tableText = "<tr><td class='text-center' colspan='12'>조회요청한 데이터가 존재하지 않습니다.</td></tr>";
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
        SEARCH_MVV_PROC_STATUS_CD: $('#SEARCH_MVV_PROC_STATUS_CD').val(),
        BIZ_NM_SEARCH: $('#BIZ_NM_SEARCH').val(),
        BIZ_NUM_SEARCH: $('#BIZ_NUM_SEARCH').val(),
        KEYWORD_SEARCH: $('#KEYWORD_SEARCH').val(),
        KEYWORD_SELECT: $('#KEYWORD_SELECT').val()
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
        MVV_ID : $('#btnDelete').val(),
    };
    callAjax(ajaxUrl, 'POST', data).then((result)=>{
        const data = JSON.parse(result);
        if(data.status == OK){
            confirmSwalOneButton('삭제에 성공하였습니다.','확인',()=>{
                $('#qna-view').modal('hide');
                getList(CURR_PAGE);
            });
        }else{
            const resultText ='서버와의 연결이 원활하지 않습니다.' ;
            alert(resultText);
        } 
    });
}
