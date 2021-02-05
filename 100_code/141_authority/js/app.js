const AUTHORITY_URL = 'act/authority_act.php';
let CURR_PAGE = 1;
let popupX = 0;
let popupY = 0;

$(document).ready(() =>{
    const pageType = $('#authorityType').val();
    $('#indicator').hide();

    if(pageType == 'main'){  
        const tdLength = $('table tr th').length;
        setInitTableData(tdLength,'authorityTable');

        // 엔터 키 이벤트
        $("#ALLM_NM_SEARCH").keypress(function(e) { 
            if (e.keyCode == 13){
                getList(1);
            }    
        });  
    }

    $("#searchForm").submit(function(e){
        e.preventDefault();
        getList(1);
    });

   

     /*로그인 팝업창 X,Y설정 */
     popupX = (screen.availWidth - 1300) / 2;
     if( window.screenLeft < 0){
         popupX += window.screen.width*-1;
     }
     else if ( window.screenLeft > window.screen.width ){
         popupX += window.screen.width;
     }
     popupY= (window.screen.height / 2) - (680 / 2);

});

/** 리스트 테이블 동적으로 생성 
 * @param {dataArray : 리스트 데이터, currentPage : 현재페이지(페이징을 위해)} 
*/
setTable = (totalListCount, dataArray, currentPage) => {
    let tableText = '';
    dataArray.map((data, index)=>{
        tableText += "<tr>";
        tableText += "<td class='text-center'>"+data.IDX+"</td>";
        tableText += "<td class='text-left'>"+data.ALLM_NM+"</td>";
        tableText += "<td class='text-center'>"+data.WRT_DTHMS+"</td>";
        tableText += "<td class='text-center'>"+data.WRT_USERID+"</td>";    
        if($('#UPDATE_TABLE').is(":visible")){
            tableText += "<td class='text-center'>";
            tableText += "<button type='button' class='btn btn-default' onclick='showUpdateModal("+JSON.stringify(data)+")'>수정</button>";
            tableText += "</td>";
        }
        
        tableText += "</tr>"
        
    });
    paging(totalListCount, currentPage, 'getList'); 
    $('#authorityTable').html(tableText);
}

/** 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
    const tableText = "<tr><td class='text-center' colspan='4'>조회요청한 데이터가 존재하지 않습니다.</td></tr>";
    $('.pagination').html('');
    $('#authorityTable').html(tableText);
}

/**권한관리 리스트 */
getList = (currentPage) => {
    CURR_PAGE = (currentPage == undefined) ? CURR_PAGE : currentPage;
    const data = {
        REQ_MODE: CASE_LIST,
        PER_PAGE:  $("#perPage").val(),
        CURR_PAGE:  CURR_PAGE,
        KEYWORD : $('#ALLM_NM_SEARCH').val()
    };

    callAjax(AUTHORITY_URL, 'GET', data).then((result)=>{
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

/* 등록창 */
showAddModal = () => {
    // location.replace("authorityModal.html?type=add");
    window.open('./authorityModal.html', 
                     'loginNoticePopup', 
                     'width=1300,height=900, left='+ popupX + ', top='+ popupY+',toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no'); 
}


/* 수정창 */
showUpdateModal = (data) => {
    window.open('./authorityModal.html?ALLM_ID='+data.ALLM_ID, 
    'loginNoticePopup', 
    'width=1300,height=900, left='+ popupX + ', top='+ popupY+',toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no');
}



onCancel = () => {
    location.replace("authority.html");
}