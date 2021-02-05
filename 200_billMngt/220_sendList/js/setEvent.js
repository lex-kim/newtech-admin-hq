setObjectEventFunction = () => {
    $( "#START_DT" ).datepicker(
        getDatePickerSetting('END_DT','minDate')
    );
    $( "#END_DT" ).datepicker(
        getDatePickerSetting('START_DT','maxDate')    
    );

    /** 버튼 동적으로 세팅(위치) */
    const tableTopEtcPosition = $('#perPageLabel').width() + $('#perPage').width() + 55;
    $('.table-top-etc').css('left',tableTopEtcPosition);

    // 집계보기
    $(".totalbtn").click(function(){
    
        if($("#totalcheckbtn").is(":checked")){
            $(".nwt-total").addClass("total-open");
        }else{
            $(".nwt-total").removeClass("total-open");
        }
    })

    // 전체선택
    $("#allcheck").click(function(){
        if($("#allcheck").prop("checked")){
            $("input[type=checkbox].listcheck").prop("checked", true);
        }else{
            $("input[type=checkbox].listcheck").prop("checked", false);
        }
    })

    //확장하
    $("#extend").click(function(){
        if($("#extend").prop("checked")){
            $(".hidetd").show();
        }else{
            $(".hidetd").hide();
        }
    });

    /** 검색 */
    $("#searchForm").submit(function(e){
        e.preventDefault();
        IS_CLICK_SORT_BTN = false;
        getList(1);
    });

     /** 검색 */
     $("#memo_form").submit(function(e){
        e.preventDefault();
        setInsuTeamMemo();
    });
    
    /** 삭제모달 */
    $("#delForm").submit(function(e){
        e.preventDefault();
        deleteData();
    });
    
     /** 구분 셀렉트 체인지 */
     $('#CSD_ID_SEARCH').change(()=>{
        if($('#CSD_ID_SEARCH').selectpicker('val') != null){
            setBillCRROption();
        }else{
            $('#CSD_ID').selectpicker('val' , '');
            $('#CSD_ID').selectpicker('refresh');
            setBillCRROption();

        }
    });

    $("input[name=DELETE_REASON]").change(()=>{
        let deleteReason = $("input[name=DELETE_REASON]:checked").val();
        if(deleteReason == 'unused'){
            $('#DELETE_REASON_TXT').val('미사용');
        }else if(deleteReason == 'other'){
            $('#DELETE_REASON_TXT').val('타업체사용');
        }else if(deleteReason == 'duplication'){
            $('#DELETE_REASON_TXT').val('중복');
        }else if(deleteReason == 'etc'){
            $('#DELETE_REASON_TXT').val('기타 ');
        }else{
            alert(ERROR_MSG_1400);
        }
    });

    $('#delete-modal').on('hide.bs.modal', function (event) {
        $('#DELETE_REASON_TXT').val('');
        $("input[name=DELETE_REASON]").prop("checked",false);
    });    

    $('#totalcheckbtn').change(function(){
        if($(this).is(':checked')){
            getTotalSummary();
        }
    });

    /** 청구서 다운 모달 닫힐 시 */
    $('#bill-imagedown').on('hide.bs.modal', function (event) {
        clickDownloadIdx = "";
    });

}