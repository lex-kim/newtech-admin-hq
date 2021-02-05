function openAutoBillPopup(url){
    let popupWidth = window.screen.width *0.65;
    let autoBillPopupWidth = (window.screen.width - popupWidth-10);
    if(window.screen.width > 2100){
        popupWidth = 1500;
        autoBillPopupWidth = 550;
    }
    window.open(url,  'autoBill', 'width='+autoBillPopupWidth+',height=1000, left='+(popupWidth+1));
}

setUpdateData = (dataArray) => {
    const billInfo = dataArray[0][0];
    const billInsuInfo = dataArray[0];
    const billPartsInfo = dataArray[2].filter((item)=>{return item.CPPS_ID != '미사용'});
    const billWorkPartsInfo = dataArray[3];
    console.log("update===>",dataArray);
    partsChartArray = dataArray[1];

    if(billInfo.NB_AB_BILL_YN == YN_Y){   //신보적용
        $('#NB_NSRSN_TABLE').show();
        if(billInfo.NB_AB_NSRSN.split(NSRSN_SEPARATOR).length > 0){
            let html = "";
            billInfo.NB_AB_NSRSN.split(NSRSN_SEPARATOR).map(function(item){
                html += "<option value='"+item+"'>"+item+"</option>";
            });
            $('#NB_AB_NSRSN_VAL').append(html);
            if(billInfo.NB_AB_NSRSN_VAL.split(NSRSN_SEPARATOR).length > 0){
                $('#NB_AB_NSRSN_VAL').val(billInfo.NB_AB_NSRSN_VAL.split(NSRSN_SEPARATOR)[0]);
            }
            
        }
        
    }
    $('#OPEN_AUTOBILL').attr('onclick','openAutoBillPopup(\''+billInfo.NB_AB_VIEW_URI+'\')');

    if(billInfo.NB_AB_EST_SEQ1 != ""){

        $('#EST_SEQ1').val(billInfo.NB_AB_EST_SEQ1);
    }
    if(billInfo.NB_AB_EST_SEQ2 != ""){
        $('#EST_SEQ2').val(billInfo.NB_AB_EST_SEQ2);
    }

    if(billInfo.NB_NEW_INSU_YN == YN_Y){   //신보적용
        $('input[name="NB_NEW_INSU_YN"]').attr('checked',true);
    }

    if(billInfo.NBW_TIME > 0){   // 방청시간
        setReckonTimeTable(billInfo.NBW_TIME); 
    }else{
        setReckonTimeTable(0); 
    }
   
    /** 청구서 기본정보 */
    const garageValue = billInfo.NB_GARAGE_NM+" ["+billInfo.MGG_ID+"]";
    $('#NB_CLAIM_DT').val(billInfo.NB_CLAIM_DT);
    $('#SELECT_NB_GARAGE_NM').val(billInfo.NB_GARAGE_NM);  
    $('#SELECT_NB_GARAGE_ID').val(billInfo.MGG_ID);                           //선택한 데이터 value를 저장할곳(hidden)
    $('#REPAIRE_GARAGE_NM').val(garageValue);                                   // 수리차량정보에 자동입력
    $("#NB_GARAGE_NM").val(garageValue);                             //선택한 주소를 보여줄 input
    $('#workPartsListTable').html(workPartsTableHtml);
    setGaragePartsData(billInfo.MGG_ID, billPartsInfo);             //4.사용항목풍목, 5.작업선택항목 체크
    $('input:radio[name=RLT_ID][value="'+billInfo.RLT_ID+'"]').attr('checked', true);
    $('input:radio[name=RMT_ID][value="'+billInfo.RMT_ID+'"]').attr('checked', true);
    $('#NB_CLAIM_USERID').val(billInfo.NB_CLAIM_USERID);
    $('#WRT_DTHMS').val(billInfo.WRT_DTHMS);
    

    /** 청구서 보험사  */
    selectInsuTable($('input:radio[name=RLT_ID][value="'+billInfo.RLT_ID+'"]').attr('class'), billInfo.RLT_ID); 
   
    billInsuInfo.map((item)=>{
        let idType = '';
        if(item.NBI_RMT_ID == REF_RMT_PI){
            idType = '_';
        } 

        if($('#MII_ID'+idType).is(':visible')){
            $('#NBI_REGI_NUM'+idType).val(item.NBI_REGI_NUM);
            $('#NBI_CHG_NM'+idType).val(item.NBI_CHG_NM);
            $('#MII_ID_UPDATE'+idType).val(item.MII_ID);
            $('#MII_ID'+idType).val(item.MII_ID);
            $('#RICT_ID'+idType).val(item.RICT_ID);  
            $('#RICT_NM'+idType).html(item.RICT_NM); 
            $('#INSU_MANAGE_COUNT'+idType).html(item.CLAIM_COUNT);
            $('#INSU_MANAGE_REGIST_DATE'+idType).html(item.CLAIM_WRT_DTHMS); 
            $('#NBI_FAX_NUM'+idType).val(phoneFormat(item.NBI_FAX_NUM));
            if(String(item.NBI_FAX_NUM).substring(item.NBI_FAX_NUM.length,item.NBI_FAX_NUM.length-4)=='0000' && billInfo.NB_AB_BILL_YN == YN_Y){   //팩스번호 뒷자리가 0000이면 노란음영
                $('#NBI_FAX_NUM'+idType).addClass('chkFax');
            }
            $('#SELECT_NBI_CHG_NM'+idType).val(item.NIC_ID);
            if($('#NBI_RATIO'+idType).is(':visible')){
                $('#NBI_RATIO'+idType).val(item.NBI_RATIO);
                $('#NBI_RATIO_UPDATE_'+idType).val(item.NBI_RATIO);
                $('#NBI_RATIO'+idType).trigger('change');
            }
            $('#NBI_CHG_NM'+idType).attr('disabled', false);
            $('#NBI_FAX_NUM'+idType).attr('readonly', false);
            getInsuManageInfo(idType, item.MGG_ID, false);   // 해당보상담당자 autocomplete
        }   
    });
    $('.MII_REGI_NUM').trigger('change');

    /** 청구서 자동차 */
    $('#NB_CAR_NUM').val(billInfo.NB_CAR_NUM);
    $('#CCC_ID').val(billInfo.CCC_ID);                           //선택한 데이터 value를 저장할곳(hidden)
    $('#RCT_CD').val(billInfo.CCC_CAR_TYPE_CD);
    $("#CCC_NM").val(billInfo.CCC_NM+" ["+billInfo.RCT_NM+"]");                 //선택한 주소를 보여줄 input
    if(billInfo.NB_VAT_DEDUCTION_YN == YN_Y){ // 부가세
        $('#NB_VAT_DEDUCTION_YN').attr('checked',true);
    }

    if(billInfo.NB_INC_DTL_YN == YN_Y){ // 청구서 전부 전송여부
        $('#NB_INC_DTL_YN').attr('checked',true);
    }
    
   /** 청구서 메모 */
    $('#NB_MEMO_TXT').val(billInfo.NB_MEMO_TXT.replace(/<br>/g, '\r\n'));

    /** 청구서팩스전송대상 */
    setFaxTargetRadtioBtn(billInfo.RLT_ID);
    if(billInfo.RLT_ID == RLT_BOTH_TYPE){  // 쌍방일 경우
        billInsuInfo.map((item)=>{
            if(item.NBI_SEND_FAX_YN != YN_Y && item.NBI_RMT_ID == REF_RMT_DI){
                $('#FAX_TARGET_11420').prop('checked',true);
            }else if(item.NBI_SEND_FAX_YN != YN_Y && item.NBI_RMT_ID == REF_RMT_PI){
                $('#FAX_TARGET_11410').prop('checked',true);
            }else{
                $('#FAX_TARGET_11430').attr('checked',true);
            }
        });

    }else if(billInfo.RLT_ID == RLT_MY_CAR_TYPE){
        $('#FAX_TARGET_11410').prop('checked',true);
    }else{
        $('#FAX_TARGET_'+billInfo.RLT_ID).prop('checked',true);
    }

    let updateWorkParts = setInterval(() => {
        if($('#workPartsTable').html().trim() != ''){
            billWorkPartsInfo.map((item)=>{   // 작업선택 항목 체크
                $('#CHECK_'+item.NWP_ID+"_"+item.RRT_ID+"_"+item.RWPP_ID).attr('checked',true);
                onCheckReckonCheckbox(item.NWP_ID,item.RRT_ID,item.RWPP_ID, item.RWT_ID, item.CPP_ID);
            });
            clearInterval(updateWorkParts);
        }
    }, 250);
    
}