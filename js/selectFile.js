$(document).ready(function(){
    /** 첨부파일 change 이벤트 */
    $('input[type="file"]').change(function(e){
        const fileName = e.target.files[0].name;
        const id = e.target.id;

        $('#'+id+"_ORI").val(fileName);
        $('#'+id+'_SELECT_TEXT').text(fileName);
        if(!$('#'+id+'_DELETE_BTN').is(":visible")){
            $('#'+id+'_DELETE_BTN').show();
        }
    });
});



/**
 * 클릭이벤트
 * @param {추가할 아이디} objID 
 */
function onClickFileBtn(objID){
    $('#'+objID).click();
}

/**
 * 첨부파일 삭제시
 * @param{objID : 삭제할 아이디}
 */
function delUploadItem(objID){
    $('#'+objID).val("");
    $('#'+objID+"_ORI").val("")
    $('#'+objID+"_PHYSICAL_ORI").val("");

    $('#'+objID+'_DELETE_BTN').hide();
    $('#'+objID+'_SELECT_TEXT').text(NOT_SELECT_FILE_TEXT);
}