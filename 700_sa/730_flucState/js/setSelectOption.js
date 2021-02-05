function setClaimYearOption(data){
    let optionText = '<option value="">=해당년=</option>';
    let value = data.CURR_YEAR;

    while(value>= data.MIN_YEAR) {
        optionText += "<option value="+value+">"+value+"년</option>";
        value--;
    }

    $('#START_YEAR').html(optionText);
    $('#END_YEAR').html(optionText);
    $('.date').val(data.CURR_YEAR);
}

function setGarageInfoOption(data, objID){
    let optionText = "";
    if(data.data.length > 0){
        data.data.map((item, index)=>{
            optionText += '<option value="'+item.MGG_ID+'">'+item.MGG_NM+'</option>';
        });
    }
    $('#'+objID).append(optionText);
    $('#'+objID).selectpicker('refresh');

}
