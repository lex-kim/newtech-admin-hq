const CONFIRM_URL = 'act/comfirmBundle_act.php';
let CURR_PAGE = 1; 
let listArray = [];
let selectCarNum = '';
let selectIndex = 0;
let modalArray = [];

$(document).ready(function() {
    getList();
});

upload = () => {
    if(uploadForm.UPLOAD_CONFIRM.value=="") {
      alert('업로드할파일을 선택하세요.');
    } else {
      $("#uploadForm").attr("action", CONFIRM_URL);
      $('#REQ_MODE').val(CASE_CREATE);
      confirmSwalTwoButton(
        '파일을 업로드 하시겠습니까?',
        '확인',
        '취소',
        false,
        ()=>{
          uploadForm.submit();
        }
    );
    }
}


/////////////////////////////// 일괄입금 업로드 ///////////////////////////////

getList = (currentPage) => {
    CURR_PAGE = (currentPage == undefined) ? CURR_PAGE : currentPage;
    const data = {
      REQ_MODE: CASE_LIST,
      PER_PAGE:  $("#perPage").val(),
      CURR_PAGE:  CURR_PAGE
    };
  
    callAjax(CONFIRM_URL,'GET', data).then((result)=>{
      console.log('getList>>',result);
        const data = JSON.parse(result);
        console.log(data.status);
        if(data.status == OK){
          $('#indicator').hide();
          const item = data.data;
          setTable(item[1], item[0], CURR_PAGE);
        }else{
            setHaveNoDataTable();
        }
    });
  
  }
  

/** 리스트 테이블 동적으로 생성 
 * @param {dataArray : 리스트 데이터, currentPage : 현재페이지(페이징을 위해)} 
*/
setTable = (totalListCount, dataArray, currentPage) => {
    let tableText = '';
    listArray = dataArray;

    dataArray.map((data, index)=>{
        tableText += "<tr>";
        tableText += "<td class='text-center'>"+data.IDX+"</td>";
        // tableText += "<td class='text-left'><a href='confirmBundleDetail.html?idx="+data.NH_DTHMS_ID+"'>"+data.NH_DTHMS_ID+"</td>";
        tableText += "<td class='text-left'><a href='javascript:replacePage("+index+")'>"+data.NH_DTHMS_ID+"</a></td>";
        tableText += "<td class='text-center'>"+data.NH_TOTAL_CNT+"</td>";
        tableText += "<td class='text-center'>"+data.NH_PROC_CNT+"</td>";
        tableText += "<td class='text-center'>"+(data.WRT_USERID == null ? '' : data.USER_NM +'('+data.WRT_USERID+')')+"</td>";
        tableText += "</tr>"
        
    });
    paging(totalListCount, currentPage, 'getList'); 
    $('#dataTable').html(tableText);
}

replacePage= (idx) =>{
  console.log(listArray[idx]);
  console.log("/300_deposit/321_bundleDetail/confirmBundleDetail.html?idx="+listArray[idx].NH_DTHMS_ID);
  var newWindow = window.open("about:blank");
  newWindow.location.replace("/300_deposit/321_bundleDetail/confirmBundleDetail.html?idx="+listArray[idx].NH_DTHMS_ID);
}

/** 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
    const tableText = "<tr><td class='text-center' colspan='5'>조회요청한 데이터가 존재하지 않습니다.</td></tr>";
    $('.pagination').html('');
    $('#dataTable').html(tableText);
}


/////////////////////////////// 일괄입금 업로드 ///////////////////////////////
