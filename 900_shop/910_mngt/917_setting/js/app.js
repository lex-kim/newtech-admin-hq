const oriHtml = $('#editForm').html();
$(function(){
    $("input[type=text], input[type=radio]").attr("disabled", true);
    $(".btn-back").hide();
    $(".btn-save").hide();

    $('#editForm').submit(function(e){
        e.preventDefault();
        
        $('#processModal').css('display','flex');
        setTimeout(function(){ 
            setData()
        },200);
    });
});

function update(){
    $("input[type=text], input[type=radio]").removeAttr("disabled");
    $(".btn-back").show();
    $(".btn-save").show();
    $(".btn-update").hide();
}

function back(){
    $('#editForm').html(oriHtml);

    $("input[type=text], input[type=radio]").attr("disabled", true);
    $(".btn-back").hide();
    $(".btn-save").hide();
    $(".btn-update").show();
}


function setData(){
    let form = $("#editForm")[0];
    let formData = new FormData(form);
    formData.append('REQ_MODE', CASE_CREATE);
  
    $('#processModal').css('display','none');
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


