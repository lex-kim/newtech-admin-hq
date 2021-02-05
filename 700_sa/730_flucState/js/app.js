const COMMON_URI = '/common/searchOptions.php';
const ajaxUrl = 'act/flucState_act.php';
let CURR_PAGE = 1;

$(document).ready(function() {
    getDivision();          // 지역 동적으로 가져오기
    getClaimYearOption();   // 청구서 해당년

      /** 검색 */
      $("#searchForm").submit(function(e){
        e.preventDefault();
        getList(1);
       
    });
   
});
   

function getList(currentPage){
    CURR_PAGE = (currentPage == undefined) ? CURR_PAGE : currentPage;

    let form = $("#searchForm")[0];
    let formData = new FormData(form);
    formData.append('REQ_MODE', CASE_LIST);
    formData.append('PER_PAGE', $("#perPage").val());
    formData.append('CURR_PAGE', CURR_PAGE);

    callFormAjax(ajaxUrl, 'POST', formData).then((result)=>{
        if(isJson(result)){
            const data = JSON.parse(result);
            if(data.status == OK){
                const item = data.data;
                setTable(item[1], item[0], CURR_PAGE);
            }else{
                setHaveNoDataTable();
            }
        }else{
            alert(ERROR_MSG_1200);
        }
    });  

}

function excelDownload(){
  location.href="flucStateExcel.html?"+$("#searchForm").serialize()+"&REQ_MODE="+CASE_TOTAL_LIST;
}