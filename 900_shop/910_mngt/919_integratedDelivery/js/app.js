$(function(){

    $('#editForm').submit(function(e){
        e.preventDefault();
        setData();
    });
});



function setData(){
    let form = $("#editForm")[0];
    let formData = new FormData(form);
    formData.append('REQ_MODE', CASE_CREATE);
  
    callFormAjax(ERP_CONTROLLER_URL, 'POST', formData).then((result)=>{
        if(isJson(result)){
            const res = JSON.parse(result);
            if(res.status == OK){
                alert(res.data);
                location.reload();
            }else{
                alert(ERROR_MSG_1100);
            }
        }else{
            alert(ERROR_MSG_1200);
        }
    });  
}


