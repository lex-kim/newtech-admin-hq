function setEvent(){
    // 검색기간
    $( "#START_DT" ).datepicker(
        getDatePickerSetting('END_DT', 'minDate')
    );
    $( "#END_DT" ).datepicker(
        getDatePickerSetting('START_DT', 'maxDate')
    );

    // 최종방문일
    $( "#START_LAST_VISIT_DT" ).datepicker(
        getDatePickerSetting('END_DT', 'minDate')
    );
    $( "#END_LAST_VISIT_DT" ).datepicker(
        getDatePickerSetting('START_DT', 'maxDate')
    );

    // 전체선택 체크
    $("#allcheck").click(function(){
        if($("#allcheck").prop("checked")){
            $("input[type=checkbox]").prop("checked", true);
        }else{
            $("input[type=checkbox]").prop("checked", false);
        }
    })

    //공업사방문주기 보이기, 숨기기
    $(".subtable-btn").click(function(){
        $(".salestate-subtable").toggleClass("subtable-open");
          if($(".salestate-subtable").hasClass("subtable-open") === true) {
              if(IS_CLICK_SEARCH_BTN || $('#VISIT_CYCLE_TABLE').html().trim() == ''){
                IS_CLICK_SEARCH_BTN  = false;
                getVisitCycleData();
              }
            $(".subtable-btn").html("숨기기");
          }else{
            $(".subtable-btn").html("보이기");
          }
    })

    //확장하기
    $("#extend").click(function(){
        if($("#extend").prop("checked")){
            $(".hidetd").show();
             $(".changecol").attr("colspan", 7);
        }else{
            $(".hidetd").hide();
            $(".changecol").attr("colspan", 2);
        }
    });

    

    /** 구분 셀렉트 체인지 */
    $('#CSD_ID_SEARCH').change(()=>{
        if($('#CSD_ID_SEARCH').selectpicker('val') != null){
            setCRRSelectOption('CRR_ID_SEARCH', true, undefined);
        }else{
            $('#CSD_ID').selectpicker('val' , '');
            $('#CSD_ID').selectpicker('refresh');
            setCRRSelectOption('CRR_ID_SEARCH', true, undefined);

        }
    });

    $('#SELECT_DT').change(function(){
        if($(this).val() == ''){
            $('#START_DT').val('');
            $('#END_DT').val('');
            $('#START_DT').attr('disabled',true);
            $('#END_DT').attr('disabled',true);
        }else{
            $('#START_DT').attr('disabled',false);
            $('#END_DT').attr('disabled',false);
        }
       
    });

      /** 검색 */
    $("#searchForm").submit(function(e){
        e.preventDefault();
        IS_CLICK_SEARCH_BTN = true;
        getList(1);
            
    });
    setVisitDTSelectbox();   // 임원방문월 selectbox 세팅
    setAvgSelectbox('CNT');       // 월평균건수 현재 기준으로 D-1, D-2 세팅
    setAvgSelectbox('PRICE');       // 월평균금액현재 기준으로 D-1, D-2 세팅

}

function setVisitDTSelectbox(){
    let option = "";
    for(let i=1; i<=12; ++i){
        option += "<option value='"+fillZero(i+'',2)+"'>"+fillZero(i+'',2)+"월</option>";
    }
    $('.VISIT_SELECT').append(option);
}

function setAvgSelectbox(type){
    const date =  new Date();
    const year = date.getFullYear();
    const month = date.getMonth()+1;

    let option = "<option value=''>=월평균=</option>";
    let lastCnt = 0;
    for(let i=1; i<=3; ++i){
        let d = month-i;
        let y = year;
        if(month-i < 1){
            d = 12-lastCnt;
            y = year-1;
            lastCnt++;
        }
        option += "<option value='D"+i+"_BILL_"+type+"'>"+y+"-"+fillZero(d+'',2)+"</option>";
    }
    $('#MOTN_AVG_'+type).html(option);
}