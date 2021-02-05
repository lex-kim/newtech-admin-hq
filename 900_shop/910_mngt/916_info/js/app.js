
const oriHtml = $('#editForm').html();
$(function(){
    $("input[type=text], input[type=radio], select").attr("disabled", true);
    $(".btn-back").hide();
    $(".btn-save").hide();
   

    $('#editForm').submit(function(e){
        e.preventDefault();
        setData();
    });
});

function update(){
    $("input[type=text], input[type=radio], select").removeAttr("disabled");
    $(".btn-back").show();
    $(".btn-save").show();
    $(".btn-update").hide();
}

function back(){
    $('#editForm').html(oriHtml);

    $("input[type=text], select").attr("disabled", true);
    $(".btn-back").hide();
    $(".btn-save").hide();
    $(".btn-update").show();
}

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

isUpdatMode = (objID) => {
    if($(".btn-back").is(":visible")){
        execPostCode(objID);
    }else{
        alert("수정모드에서만 사용하실 수 있습니다.");
    }
}

execPostCode = (objID) => {
    new daum.Postcode({
        oncomplete: function(data) {
            let fullAddr = ''; // 최종 주소 변수
            let extraAddr = ''; // 조합형 주소 변수
            
            fullAddr = data.roadAddress;
            
            if(data.bname !== ''){
                extraAddr += data.bname;
            }
            
            if(data.buildingName !== ''){// 건물명이 있을 경우 추가한다.
                extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
            }
        
            fullAddr += (extraAddr !== '' ? ' ('+ extraAddr +')' : ''); // 조합형주소의 유무에 따라 양쪽에 괄호를 추가하여 최종 주소를 만든다.
            
            $('#'+objID+'_ZIP').val(data.zonecode);
            $('#'+objID+'_ADDRESS1_AES').val(fullAddr);
            $('#'+objID+'_ADDRESS2_AES').val('');
            $('#'+objID+'_ADDRESS2_AES').focus();
        }
    }).open();
    
}
   

