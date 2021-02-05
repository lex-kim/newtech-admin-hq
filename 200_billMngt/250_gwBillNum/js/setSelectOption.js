function setClaimYearOption(data){
    let optionText = '<option value="">=연도=</option>';
    let value = data.CURR_YEAR;

    while(value>= data.MIN_YEAR) {
        if(value == data.CURR_YEAR){
            optionText += "<option value="+value+" selected>"+value+"년</option>";
        }else{
            optionText += "<option value="+value+">"+value+"년</option>";
        }
        
        value--;
    }

    $('#CLAIM_YEAR').html(optionText);
}

function setGarageInfoOption(data, objID){
    let optionText = "<option value=''>=공업사=</option>";
    if(data.data.length > 0){
        data.data.map((item, index)=>{
            optionText += '<option value="'+item.MGG_ID+'">'+item.MGG_NM+'</option>';
        });
    }
    $('#'+objID).append(optionText);
    $('#'+objID).selectpicker('refresh');

}