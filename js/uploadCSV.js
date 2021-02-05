$('#csvUploadForm').submit(function(e){
    e.preventDefault();

    if(empty($('#csvUploadForm input[type=file]').val())){
      alert("업로드 할 csv 파일을 선택해주세요.");
    }else{
      uploadCSVFile($(document.activeElement).attr('data'), $(document.activeElement).attr('data-text'));
    }
    
});

function uploadCSVFile(objID, objText){
    let form = $("#csvUploadForm")[0];
    let formData = new FormData(form);
    formData.append('REQ_MODE', CASE_CREATE);
  
    callFormAjax(ERP_CONTROLLER_URL, 'POST', formData).then((result)=>{
      if(isJson(result)){
        const res = JSON.parse(result);
        try{
          const data = res.data;
          let failResultHtml = "";

          (data.failList).map(function(item, index){
              failResultHtml+="<tr>";
              failResultHtml+="<td>";
              failResultHtml+=item[objID];
              failResultHtml+="</td>";
              failResultHtml+="<td>";
              failResultHtml+=item.result;
              failResultHtml+="</td>";
              failResultHtml+="</tr>";
          });
  
          confirmSwalOneButton("성공 : "+data.successCount+"건 , 실패 : "+data.failCount+'건','확인',()=>{
            if(!empty(failResultHtml)){
              $('#resultTable').html(failResultHtml);
              $('#resultCol').html(objText);
              $('#uploadCSVResultModal').modal('show');
            }
            if(data.failCount < data.totalCount){
              getList(1);
            }
          });
        }catch{
          alert(ERROR_MSG_1100);
        }
      }else{
          alert(ERROR_MSG_1200);
      }
    });   
}