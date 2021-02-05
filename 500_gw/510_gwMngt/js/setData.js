/**
 * 공업사관리 date값 세팅 및 
 * 등록창 업데이트 값 세팅
 */

setDate = () => {
     /* datepicker 설정 */
    // 계약기간
    $( "#MGG_CONTRACT_START_DT" ).datepicker(
        getDatePickerSetting('MGG_CONTRACT_END_DT','minDate')
    );
    $( "#MGG_CONTRACT_END_DT" ).datepicker(
        getDatePickerSetting('MGG_CONTRACT_START_DT','maxDate')
            
    );

    $( "#BIZ_START_DT_SEARCH" ).datepicker(
        getDatePickerSetting('BIZ_END_DT_SEARCH','minDate')
    );
    $( "#BIZ_END_DT_SEARCH" ).datepicker(
        getDatePickerSetting('BIZ_START_DT_SEARCH','maxDate')
            
    );

    if($('#LEFT_TABLE').is(':visible')){
        $( "#BIZ_VISIT_DATE" ).datepicker(
            getDatePickerSetting()
        );
        $( "#BIZ_SUPPLY_DATE" ).datepicker(
            getDatePickerSetting()        
        );
    }
    $('.calenderInput').datepicker(getDatePickerSetting());
    
}

getDatePickerSetting = (objId, type) => {
    const data = {
        dateFormat: 'yy-mm-dd',
        prevText: '이전 달',
        nextText: '다음 달',
        monthNames: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
        monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
        dayNames: ['일','월','화','수','목','금','토'],
        dayNamesShort: ['일','월','화','수','목','금','토'],
        dayNamesMin: ['일','월','화','수','목','금','토'],
        showMonthAfterYear: true,
        changeMonth: true,
        changeYear: true,
        yearSuffix: '년',
        yearRange: 'c-50:c+50'
    }
    if(type != undefined){
        Object.assign(data, {
            onClose : (selectedDate) => {
                $('#'+objId).datepicker("option", type, selectedDate);
            }
         });
    }

    return data;
}

/**
 * 등록창 업데이트 값 세팅
 * @param {gwInfo : 해당 공업사 정보}
 */
getGwUpdateData = (gwInfo, partsInfo) => {
    $('#CSD_ID').val(gwInfo.CSD_ID);
    setCRR_ID('CSD_ID', 'CRR_ID', 'SELECT_CRR_ID', true);
    $('#SELECT_CRR_ID').val(gwInfo.CRR_1_ID+","+gwInfo.CRR_2_ID+","+gwInfo.CRR_3_ID);
    $('#CRR_ID').val(gwInfo.CRR_1_NM+" "+gwInfo.CRR_2_NM+" "+gwInfo.CRR_3_NM);
    $('#CRR_ID').attr('disabled',false);
    $('#MGG_USERID').val(gwInfo.MGG_USERID);
    $('#MGG_ID').attr('readonly',true);
    $('#MGG_BIZ_ID').val(gwInfo.MGG_BIZ_ID);
    $('#MGG_PASSWD_AES').val(gwInfo.MGG_PASSWD_AES);
    $('#MGG_CIRCUIT_ORDER').val(gwInfo.MGG_CIRCUIT_ORDER);
    $('#MGG_NM').val(gwInfo.MGG_NM);
    $('#RET_ID').val(gwInfo.RET_ID);
    $('#MGG_RET_DT').val(gwInfo.MGG_RET_DT);
    $('#RPET_ID').val(gwInfo.RPET_ID);
    $('#MGG_RPET_DT').val(gwInfo.MGG_RPET_DT);
    if(gwInfo.MII_ID != null){
        $('#MII_ID').selectpicker('val', (gwInfo.MII_ID).split(","));
        $('#MII_ID').selectpicker('refresh');
    }
    $('#RAT_ID').val(gwInfo.RAT_ID);
    $('#MGG_RAT_DT').val(gwInfo.MGG_RAT_DT);
    $('#MGG_RAT_NM').val(gwInfo.MGG_RAT_NM);
    $('#MGG_AOS_MEMO').val(gwInfo.MGG_AOS_MEMO);
    $('#MGG_LOGISTICS_YN').val(gwInfo.MGG_LOGISTICS_YN);
    $('#MGG_ZIP_OLD').val(gwInfo.MGG_ZIP_OLD);
    $('#MGG_ADDRESS_OLD_1').val(gwInfo.MGG_ADDRESS_OLD_1);
    $('#MGG_ADDRESS_OLD_2').val(gwInfo.MGG_ADDRESS_OLD_2);
    $('#MGG_ZIP_NEW').val(gwInfo.MGG_ZIP_NEW);
    $('#MGG_ADDRESS_NEW_1').val(gwInfo.MGG_ADDRESS_NEW_1);
    $('#MGG_ADDRESS_NEW_2').val(gwInfo.MGG_ADDRESS_NEW_2);
    if(gwInfo.MGG_ZIP_OLD.length >= 5 || gwInfo.MGG_ZIP_NEW.length >= 5 ){
        let addr = '';
        if(gwInfo.MGG_ADDRESS_OLD_1.length > 0 ){
            addr = gwInfo.MGG_ADDRESS_OLD_1;
        }else {
            addr = gwInfo.MGG_ADDRESS_NEW_1;
        }
        addr = replaceAll(addr, ' ', '+');
        $('#mapLinkBtn').show();
        $('#mapLinkBtn').attr('onclick', 'mapLink(\''+addr+'\')');
    }
    $('#MGG_OFFICE_TEL').val(gwInfo.MGG_OFFICE_TEL);
    $('#MGG_OFFICE_FAX').val(gwInfo.MGG_OFFICE_FAX);
    $('#MGG_WAREHOUSE_CNT').val(gwInfo.MGG_WAREHOUSE_CNT);
    $('#RMT_ID').val(gwInfo.RMT_ID);
    $('#ROT_ID').val(gwInfo.ROT_ID);
    $('#MGG_INSU_MEMO').val(gwInfo.MGG_INSU_MEMO);
    $('#MGG_MANUFACTURE_MEMO').val(gwInfo.MGG_MANUFACTURE_MEMO);
    $('#SLL_ID').val(gwInfo.SLL_ID);
    $('#MGG_GPS_LATITUDE').val(gwInfo.MGG_GPS_LATITUDE);
    $('#MGG_GPS_LONGITUDE').val(gwInfo.MGG_GPS_LONGITUDE);
    
    $('#MGG_CEO_NM_AES').val(gwInfo.MGG_CEO_NM_AES);
    $('#MGG_CEO_TEL_AES').val(gwInfo.MGG_CEO_TEL_AES);
    $('#MGG_KEYMAN_NM_AES').val(gwInfo.MGG_KEYMAN_NM_AES);
    $('#MGG_KEYMAN_TEL_AES').val(gwInfo.MGG_KEYMAN_TEL_AES);
    $('#MGG_CLAIM_NM_AES').val(gwInfo.MGG_CLAIM_NM_AES);
    $('#MGG_CLAIM_TEL_AES').val(gwInfo.MGG_CLAIM_TEL_AES);
    $('#MGG_FACTORY_NM_AES').val(gwInfo.MGG_FACTORY_NM_AES);
    $('#MGG_FACTORY_TEL_AES').val(gwInfo.MGG_FACTORY_TEL_AES);
    $('#MGG_PAINTER_NM_AES').val(gwInfo.MGG_PAINTER_NM_AES);
    $('#MGG_PAINTER_TEL_AES').val(gwInfo.MGG_PAINTER_TEL_AES);
    $('#MGG_METAL_NM_AES').val(gwInfo.MGG_METAL_NM_AES);
    $('#MGG_METAL_TEL_AES').val(gwInfo.MGG_METAL_TEL_AES);
    $('#MGG_FORCE_SEND_YN').val(gwInfo.MGG_FORCE_SEND_YN);
    $('#MGG_BILL_ALL_YN').val(gwInfo.MGG_BILL_ALL_YN);
    $('#MGG_BILL_MONEY_SHOW_YN').val(gwInfo.MGG_BILL_MONEY_SHOW_YN);
    $('#MGG_BILL_MEMO').val(gwInfo.MGG_BILL_MEMO);
    $('#MGG_GARAGE_MEMO').val(gwInfo.MGG_GARAGE_MEMO);
    $('#RCT_ID').val(gwInfo.RCT_ID);
    $('#MGG_BIZ_START_DT').val(gwInfo.MGG_BIZ_START_DT);
    $('#MGG_FRONTIER_NM').val(gwInfo.MGG_FRONTIER_NM);
    $('#MGG_COLLABO_NM').val(gwInfo.MGG_COLLABO_NM);
    $('#MGG_BAILEE_NM').val(gwInfo.MGG_BAILEE_NM);
    $('#MGG_BIZ_BEFORE_NM').val(gwInfo.MGG_BIZ_BEFORE_NM);
    $('#MGG_CONTRACT_YN').val(gwInfo.MGG_CONTRACT_YN);
    $('#MGG_CONTRACT_START_DT').val(gwInfo.MGG_CONTRACT_START_DT);
    $('#MGG_CONTRACT_END_DT').val(gwInfo.MGG_CONTRACT_END_DT);
    $('#MGG_RATIO').val(gwInfo.MGG_RATIO);
    $('#MGG_BANK_NM_AES').val(gwInfo.MGG_BANK_NM_AES);
    $('#MGG_BANK_NUM_AES').val(gwInfo.MGG_BANK_NUM_AES);
    $('#MGG_BANK_OWN_NM_AES').val(gwInfo.MGG_BANK_OWN_NM_AES);
    $('#MGG_BIZ_END_DT').val(gwInfo.MGG_BIZ_END_DT);
    $('#RSR_ID').val(gwInfo.RSR_ID);
    
    if(gwInfo.RCT_ID == CONTRACT_STOP){
        $('#RSR_ID').attr('disabled', false);
        $('#MGG_END_AVG_BILL_CNT').val(gwInfo.MGG_END_AVG_BILL_CNT);
        $('#MGG_END_AVG_BILL_PRICE').val(numberWithCommas(gwInfo.MGG_END_AVG_BILL_PRICE));
        $('#MGG_END_OUT_OF_STOCK').val(gwInfo.MGG_END_OUT_OF_STOCK.replace(/<br>/g, '\r\n'));
        $('#stopTable').show();
    }else{
        $('#RSR_ID').attr('disabled', true);
    }
    $('#RST_ID').val(gwInfo.RST_ID);
    $('#MGG_OTHER_BIZ_NM').val(gwInfo.MGG_OTHER_BIZ_NM);
    $('#MGG_SALES_NM').val(gwInfo.MGG_SALES_NM);
    $('#MGG_SALES_MEET_LVL').val(gwInfo.MGG_SALES_MEET_LVL);
    $('#MGG_SALES_MEET_NM_AES').val(gwInfo.MGG_SALES_MEET_NM_AES);
    $('#MGG_SALES_MEET_TEL_AES').val(gwInfo.MGG_SALES_MEET_TEL_AES);
    $('#RRT_ID').val(gwInfo.RRT_ID);
    $('#MGG_SALES_WHYNOT').val(gwInfo.MGG_SALES_WHYNOT);

    /** 임원방문월 */
    if(gwInfo.NGV_VISIT_DT != null){
        visitArray = (gwInfo.NGV_VISIT_DT).split(",");
        setVisitTable();
    }
    
    if($('#LEFT_TABLE').is(':visible')){
        $('#NSU_FILE_IMG').attr('src',gwInfo.NSU_FILE_IMG);
        $('#NSU_SIGN_IMG').attr('src',gwInfo.NSU_SIGN_IMG);
        $('#BIZ_VISIT_DATE').val(gwInfo.NSU_VISIT_DT);
        $('#BIZ_SUPPLY_DATE').val(gwInfo.NSU_SUPPLY_DT);

        /*확대용 데이터 */
        $('#POPUP_IMG').val(gwInfo.NSU_FILE_IMG);
        $('#POPUP_SIGN').val(gwInfo.NSU_SIGN_IMG);
    }
    setPartsTable(partsInfo);  // 공급/사용부품

}