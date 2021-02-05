const ajaxUrl = 'act/writeBill_act.php';
const sendListPathName = '/200_billMngt/220_sendList/sendList.html'; // 청구서 작성을 오픈한 부모창이 내역창인지.
const popupWidth = 1500;
const CL_TYPE_ONE = 'ONE_SIDE';     // 일방
const CL_TYPE_BOTH = 'BOTH';        // 쌍방
const CL_TYPE_PART = 'PARTS';       // 부분
const MAXIMUM_BYTE = 600;           //기타메모 최대 바이트수 (한글 300자)
const REF_RMT_DI = '11520'              // 보험사(자차)
const REF_RMT_PI = '11525'              // 보험사(대물)
const CAR_TYPE_FOREIGN = '1012';       //외제차타입
const RICT_TYPE_FOREIGN = 11340;     // 보상담당자 외제차 타입
const FAX_STATUS_COMPLETE = 90230;   //팩스 성공상태
const FAX_STATUS_FAIL = 90290;       // 팩스 실패 상태

let UUID = '';
let isClickFaxBtn = false;
let isSave = false;                  // 저장여부(true상태여야 발송이 가능)
let isClickOverlapBtn = false;       // 중복검사 버튼을 클릭했는지.
let isOverlapBill = false;           // 이 청구서는 중복건입니까?
let CURR_PAGE = 1;
let workPartsTableHtml = '';
let workPartsList = [];             // 작업항목 리스트 저장 배열
let stdPartsList = [];
let garageUsePartsList = [];        // 헤더에 공업사에서 사용가능한 사용부품을 저장할 배열
let priorityPartsList = [];         // 우선순위 저장하는 배열
let replacePartsPlst = [];          // 대체/체화 저장하는 배열
let partsChartArray = [];           // 조견표 계산 데이터를 저장하는 배열
let form = [];
let formData = [];
let formInsuArray = [];
let MAXIMUM_CLAIM_PRICE = 0;       // 청구금액한도


$(document).ready(()=>{
    onLoadingSetting();

    $(window).keydown(function(event){
        if(event.target.id != 'NB_MEMO_TXT'){
            if(event.keyCode == 13) {
                event.preventDefault();
                return false;
              }
        }
       
    });
  
});

function onLoadingSetting(){
    $( "#NB_CLAIM_DT" ).datepicker(
        getDatePickerSetting()
    );

    if($("#INPUT_NB_ID" ).val() == ''){
        $( "#NB_CLAIM_DT" ).datepicker('setDate', 'today');      // 기본 현재 날짜로
    }
    
       
    // 과실여부에 따른 보험사선택
    $("input:radio[name=RLT_ID]").click(function(e){
        if($("#INPUT_NB_ID" ).val() == ''){
            $('#regiNumTable').html('');
        
            selectInsuTable($(this).attr('class'), $(this).val());
            $('.MII_REGI_NUM').trigger('change');

            if($(this).val() == RLT_BOTH_TYPE){
                $('.ratioTable select').trigger('change');
            }
        }else{
            e.preventDefault();
            alert("수정상태에서는 담보상태를 수정하실 수 없습니다.");

        }
        
    });

    $('#claimForm').submit(function(e){
        e.preventDefault()

        if(this.submited == 'SAVE_BTN'){
            if(validation()){
                if($('#billTotalTable').html() == '' || $('#billTotalTable').html() == 0){
                    confirmSwalTwoButton('사용된 부품이 없습니다. 저장하시겠습니까?','확인', '취소', false ,()=>{
                        addData();
                    });
                }else{
                    addData();
                }
            }
        }else if(this.submited ==  'SEND_FAX'){
            sendFax();
        }else if(this.submited ==  'SAVE_NSRSN'){
            sendNSRSN();
        }else{
            accountReckonParts(true);
        }
        
    });

    getRefOptions(GET_REF_INSU_CHAGE_TYPE, 'RLT_ID_SELECT', false, false);                 //청구식
    setTable_BILL();   // 청구서작성페이지세어 테이블 동적 세팅 해주는 함수 (setTable.js)
    setData_BILL();    // 청구서작성페이지에서 값 동적으로 가져오는 함수 (setData.js)

    if($('#INPUT_NB_ID').val() != ''){   // 수정모드일 경우
        getBillInfo();
        $('.faxTarget').show();
    }else{
        $('.faxTarget').hide();
    }
    /** 4.2 기타부품선택 보이기/숨기기 버튼 이벤트 */
    $('#etcShowBtn').click(function(){
        if($('#etcPartsTable').css('display') == 'none'){
            $('#etcPartsTable').show();
            $('#etcShowBtn').html('숨기기');
        }else{
            $('#etcPartsTable').hide();
            $('#etcShowBtn').html('보이기');
        }
    });
}

function setFaxTargetRadtioBtn(RLT_ID){
    $('input:radio[name=FAX]').attr('checked',false);
    $('input:radio[name=FAX]').attr('disabled',true);

    if(RLT_ID == RLT_PART_TYPE){ //일부
        $('#FAX_TARGET_'+RLT_ID).prop('checked',true); 
        $('#FAX_TARGET_'+RLT_ID).prop('disabled',false); 
    }else if(RLT_ID == RLT_BOTH_TYPE){ //쌍방 
        $('#FAX_TARGET_11410').prop('disabled',false); 
        $('#FAX_TARGET_11420').prop('disabled',false); 
        $('#FAX_TARGET_11430').prop('checked',true); 
        $('#FAX_TARGET_11430').prop('disabled',false); 
    }else if(RLT_ID == RLT_MY_CAR_TYPE){ //자차
        $('#FAX_TARGET_11410').prop('checked',true); 
        $('#FAX_TARGET_11410').prop('disabled',false); 
    }else if(RLT_ID == RLT_PI_TYPE){ //대물
        $('#FAX_TARGET_11420').prop('checked',true); 
        $('#FAX_TARGET_11420').prop('disabled',false); 
    }
}
/**
 * 2. 보험사 선텍
 * 보상담당안내 모달에서 변경 눌렀을 때
 */
function onchangeInsuManage(){
    $('.insuManager').each(function(){
        if($(this).is(':checked')){
            const data = JSON.parse($(this).val()).data;

            let id = '';
            if(data.RLT_ID == RLT_PI_TYPE){
                id = '_';
            }
            $('#SELECT_NBI_CHG_NM'+id).val(data.NIC_ID);    
            $('#RICT_ID'+id).val(data.RICT_ID);  
            $('#RICT_NM'+id).html(data.RICT_NM);    
            $('#INSU_MANAGE_COUNT'+id).html(data.CLAIM_CNT);      // 2.보험사 선택 당월청구건수                
            $('#NBI_CHG_NM'+id).val(data.NIC_NM_AES);                                         
            $("#NBI_FAX_NUM"+id).val(phoneFormat(data.NIC_FAX_AES));                  // 2.보험사 선택 담당자 팩스
            $('#INSU_MANAGE_REGIST_DATE'+id).html(data.WRT_DTHMS);    // 2.보험사 선택 담당등록일 
        }
        
    });
    $('#insuManagerModal').modal('hide');
}

/** 취소버튼 눌렀을 때 리셋 */
function onReset(){
    confirmSwalTwoButton('작성된 데이터가 초기화됩니다.\n취소하시겠습니까?','확인', '취소', false ,()=>{
        location.reload();
    });
   
}

/**
 * 3. 수리차량정보
 * 중복검사 버튼클릭
 */
function onclickOverlapModal(IS_CALLBACK){
    let validation = true;
    let MII_ID_ARRAY = [];
    let MII_REGI_NUM_ARRAY = [];

    $('.MII_ID_SELECT').each(function(){
        if($(this).is(":visible")){
            if($(this).val() == ''){
                alert('보험사를 선택해주세요.');
                $(this).focus();
                validation = false;
                return false;
            }else{
                MII_ID_ARRAY.push($(this).val());
            }
        }
       
    });

    if(validation){
        $('.MII_REGI_NUM').each(function(){
            if($(this).is(":visible")){
                if($(this).val() == ''){
                    alert('접수번호를 입력해주세요.');
                    $(this).focus();
                    validation = false;
                    return false;
                }else{
                    MII_REGI_NUM_ARRAY.push($(this).val());
                }
            }
        });
    }
    
    if(validation){
        if($('#NB_CAR_NUM').val().trim() == ''){
            alert('차량번호를 입력해주세요.');
            $('#NB_CAR_NUM').focus();
            validation = false;
            return false;
        }
    }
    
    if(validation){
        if($('#SELECT_NB_GARAGE_ID').val().trim() == ''){
            alert('공업사를 입력/선택 입력해주세요.');
            $('#NB_GARAGE_NM').focus();
            validation = false;
            return false;
        }
    }
    
    if(validation){
        // if(IS_CALLBACK == undefined){
        //     getOverlapCarInfo(MII_ID_ARRAY, MII_REGI_NUM_ARRAY);
        // }else{
        //     return getOverlapCarInfo(MII_ID_ARRAY, MII_REGI_NUM_ARRAY, IS_CALLBACK);
        // }
        getOverlapCarInfo(MII_ID_ARRAY, MII_REGI_NUM_ARRAY);
       
    }
}

/** 보상담당자 신규등록 팝업 */
openNewPopup = () => {
    const popupUrl = '/600_iw/630_iwFaoMngt/iwFaoMngtModal.html';
    window.open(popupUrl, 
                     'iwFaoMngtModalwindow', 
                     'width=460,height=600, left='+(window.screenLeft +popupWidth)+',toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no'); 
}

/** 보험 담당자 신규 등록 후 콜백함수 */
callbackAddData = () => {
    /** 보험사 셀렉트 체인지 이벤트 */
    $('.MII_ID_Table select').each(function(){
        const id = $(this).attr('id') == 'MII_ID' ? '' : '_';    // 자차보험사 인지 대물보험사 인지 구분
        getInsuManageInfo(id);     // 해당보험사 담당자 정보
    });
}

/** 2. 보험사 선택 select 체인지 이벤트 */
function addInsuTableEvent(){
   
    $('.MII_REGI_NUM').change(function(){        // 접수번호 change 이벤트  
        let text = '';
        $('.MII_REGI_NUM').each(function(){
            if($(this).val().trim() != ''){
                const type = $(this).attr('id').split("_").length == 3 ? '자차' :  '대물';
                const idType = $(this).attr('id').split("_").length == 3 ? '' :  '_';
                if($('#MII_ID'+idType).is(':visible')){
                    text += type + " " +$(this).val()+"<br>";
                }
               
            }
        });
        $('#regiNumTable').html(text);
    });

    /** 과실율 셀렉트 체인지 이벤트 */
    $('.ratioTable select').change(function(){
        const selectId = $(this).attr('id');     // 자차/대물중에 어떤 과실율인지
        const selectVal = $(this).val();
        $('.ratioTable select').each(function(){
            if($(this).attr('id') != selectId){     // 선택한 과실율 제외하고 나머지 셀렉트가 존재하면 100-선택된 과실율 값을 적용
                $(this).val(100-Number(selectVal));
            }

            if($('#OWNER_INSU_PERCENT').is(':visible') && $(this).attr('id')  == 'NBI_RATIO'){
                $('#OWNER_INSU_PERCENT').val($('#NBI_RATIO').val());
            }
        });  
        
        
    });

    /** 보험사 셀렉트 체인지 이벤트 */
    $('.MII_ID_Table select').change(function(){
        if($(this).is(':visible')){
            const id = $(this).attr('id') == 'MII_ID' ? '' : '_';    // 자차보험사 인지 대물보험사 인지 구분
            $('#INSU_MANAGE_REGIST_DATE'+id).html('');    // 2.보험사 선택 담당등록일 
            $('#INSU_MANAGE_COUNT'+id).html('');   
            $('#RICT_NM'+id).html('');  
            $('#SELECT_NBI_CHG_NM'+id).val('');                          
            $('#NBI_CHG_NM'+id).val('');
            $('#NBI_FAX_NUM'+id).val('');
            isClickOverlapBtn = false;
    
            if($(this).val() == ''){
                $("#NBI_CHG_NM"+id).attr('disabled',true);
                $("#NBI_CHG_NM"+id).attr('placeholder', '보험사를 선택해주세요.');
            }else{
                getInsuManageInfo(id, $('#SELECT_NB_GARAGE_ID').val());     // 해당보험사 담당자 정보
            }
        }
       
    });
}


/** 사용된 작업항목이 있는지 확인해준 뒤 addData 시작 */
oncheckFormData = () => {
    if($('#billTotalTable').html() == '' || $('#billTotalTable').html() == 0){
        confirmSwalTwoButton('사용된 부품이 없습니다. 저장하시겠습니까?','확인', '취소', false ,()=>{
            addData(); 
        });
    }else{
        addData();  
    }
}

function validation(){
    let isValidate = true;
    formInsuArray = [];
    $('.MII_ID_LIST').each(function(){
        if($(this).is(':visible')){
            const id = $(this).attr('id').split("_")[3];
            const type = (id == RLT_MY_CAR_TYPE) ? "" : "_";

            if($('#MII_ID'+type).val()==''){
                alert('보험사를 선택해주세요');
                isValidate = false;
                $('#MII_ID'+type).focus();

                return false;
            }else if($('#NBI_REGI_NUM'+type).val() == ''){
                alert('접수번호를 입력해주세요');
                isValidate = false;
                $('#NBI_REGI_NUM'+type).focus();

                return false;
            }else if($('#NBI_FAX_NUM'+type).val() == ''){
                alert('팩스를 입력해주세요');
                isValidate = false;
                $('#NBI_FAX_NUM'+type).focus();

                return false;
            }else if($('#NBI_RATIO'+type).is(':visible') && $('#NBI_RATIO'+type).val() == ''){
                alert('과실율을 선택해주세요');
                isValidate = false;
                $('#NBI_RATIO'+type).focus();

                return false;
            }else{
                formInsuArray.push({
                    MII_ID : $('#MII_ID'+type).val(),
                    NBI_REGI_NUM : $('#NBI_REGI_NUM'+type).val(),
                    NBI_CHG_NM : $('#NBI_CHG_NM'+type).val(),
                    SELECT_NBI_CHG_NM : $('#SELECT_NBI_CHG_NM'+type).val(),
                    NBI_FAX_NUM : $('#NBI_FAX_NUM'+type).val().replace(/-/g,''),
                    NBI_RATIO : $('#NBI_RATIO'+type).is(':visible') ? ($('#NBI_RATIO'+type).val()== null ? '' : $('#NBI_RATIO'+type).val()) : '',
                    RICT_ID : $('#RICT_ID'+type).val(),
                    NBI_RMT_ID : $('#NBI_RMT_ID'+type).val() 
                });
                isValidate = true;
                
            }
        }
    });

   

    if(isValidate){
        if($('input:radio[name=RLT_ID]:checked').attr('class') != CL_TYPE_ONE){
            $('.insuRatio').each(function(){
                if($(this).val() == ''){
                    isValidate = false;
                    alert('과실률을 입력해주세요.');
                    $(this).focus();
    
                    return false;
                }
            });
        }
    }
    return isValidate;
}


async function sendFormData(MODE, NB_ID) {
    form = $("#claimForm")[0];
    formData = new FormData(form);
    formData.append('REQ_MODE', MODE);
    formInsuArray.map((item)=>{
        formData.append('MII_ID[]', item.MII_ID);
        formData.append('NBI_REGI_NUM[]', item.NBI_REGI_NUM);
        formData.append('NBI_CHG_NM[]', item.NBI_CHG_NM);
        formData.append('SELECT_NBI_CHG_NM[]', item.SELECT_NBI_CHG_NM);
        formData.append('NBI_FAX_NUM[]', item.NBI_FAX_NUM.replace(/-/g,''));
        formData.append('NBI_RATIO[]', item.NBI_RATIO);
        formData.append('RICT_ID[]', item.RICT_ID);
        formData.append('NBI_RMT_ID[]', item.NBI_RMT_ID);
    });
    formData.append('NB_ID', NB_ID);
    formData.append('NB_TOTAL_PRICE', $('#billTotalTable').html().replace(/,/g, ''));
    formData.append('NB_VAT_PRICE', $('#billDutyTable').html().replace(/,/g, ''));
    formData.append('NB_VAT_DEDUCTION_YN', $('#NB_VAT_DEDUCTION_YN').is(':checked') ? YN_Y : YN_N);
    formData.append('NB_INC_DTL_YN', $('#NB_INC_DTL_YN').is(':checked') ? YN_Y : YN_N);
    formData.append('EXCEPT_SEND_TARGET', $('#EXCEPT_SEND_TARGET').is(':checked') ? YN_Y : YN_N);
    formData.append('NB_DUPLICATED_YN', isOverlapBill ? YN_Y : YN_N);
    formData.append('DUPLICATED_CLICKED_YN', isClickOverlapBtn ? YN_Y : YN_N);
    if($('#NB_MEMO_TXT').val() != ''){
        formData.append('NB_MEMO_TXT', $('#NB_MEMO_TXT').val().replace(/(?:\r\n|\r|\n)/g, '<br>'));
    }
   
    

    /** 방청시간(작업항목/수리부위 별로)  -> work 테이블 */
    $('input[name=RRT_TD_CHECKBOX]:checked').each(function(){
        let isExistChartArray = false;
        const data = JSON.parse($(this).val());
        const NWP_ID = data.NWP_ID;
        const RWPP_ID = data.RWPP_ID;
        const RRT_ID = data.RRT_ID;

        const partsArray = partsChartArray.find((item)=>{
            return NWP_ID == item.NWP_ID && RWPP_ID == item.RWPP_ID && RRT_ID == item.RRT_ID;
        });
 
        if(partsArray){
            const WORK_PARTS_ARRAY = partsChartArray.filter((data)=>{
                return NWP_ID == data.NWP_ID && RWPP_ID == data.RWPP_ID && RRT_ID == data.RRT_ID ;
            });
                
            let ratio = 0;
            WORK_PARTS_ARRAY.map((work)=>{
                ratio += Number(work.NRT_TIME_M);
            });
            formData.append('WORK_NWP_ID[]', NWP_ID);
            formData.append('WORK_RWPP_ID[]', RWPP_ID);
            formData.append('WORK_RRT_ID[]', RRT_ID);
            formData.append('WORK_NBW_TIME[]', ratio);
            isExistChartArray = true;
        }

        if(!isExistChartArray){
            formData.append('WORK_NWP_ID[]', NWP_ID);
            formData.append('WORK_RWPP_ID[]', RWPP_ID);
            formData.append('WORK_RRT_ID[]', RRT_ID);
            formData.append('WORK_NBW_TIME[]', null);
        }
    });

    let checkWorkPartsArray = [];
    /** bill_work_part에 들어갈 체크된 사용부품  */
    $('input[name=USE_PARTS_CHECK]:checked').each(function(){
        const NWP_ID = $(this).attr('id').split("_")[2];
        const CPP_ID = $(this).val() ;
        let isExistCPP_ID = false;

        formData.append('WORK_PART_NWP_ID[]', NWP_ID);
        formData.append('WORK_PART_CPP_ID[]', CPP_ID);

        checkWorkPartsArray.map(function(item){
            if(item == CPP_ID){
                isExistCPP_ID = true;
            }
        });

        if(!isExistCPP_ID){
            checkWorkPartsArray.push(CPP_ID);
        }
    });

    /** 사용부품 */
    $('.NBP_TOTAL_PRICE').each(function(){
        if(isJson($(this).attr('name'))){
            let data = JSON.parse($(this).attr('name'));
            if(data.TYPE == "ETC"){       //기타사항
                if($(this).val() != '' && $(this).val().replace(/,/g,'') > 0){
                    let UP_DOWN = $(this).attr('id').split("_")[3];
                    let LEFT_RIGHT = $(this).attr('id').split("_")[4];
 
                    formData.append('NBP_UNIT_PRICE[]', $('#NBP_UNIT_PRICE_'+UP_DOWN+"_"+LEFT_RIGHT).val().replace(/,/g,''));
                    formData.append('NBP_USE_CNT[]', $('#NBP_USE_CNT_'+UP_DOWN+"_"+LEFT_RIGHT).val());
                    formData.append('CPPS_ID[]', $('#CPPS_ID_'+UP_DOWN+"_"+LEFT_RIGHT).val());

                }
            }else{   // 사용부품 A,B
                // if($(this).val().replace(/,/g,'') > 0){
                //     let CPP_ID = data.CPP_ID;
                   
                //     let isExist = checkWorkPartsArray.find(function(item){
                //         return item == CPP_ID;
                //     });
                //     console.log(isExist,"==",$('#NBP_UNIT_PRICE_'+CPP_ID).val(),"==",CPP_ID);
                //     console.log("checkWorkPartsArray===>",checkWorkPartsArray);
                //     if(isExist == undefined){
                //         if($('#NBP_UNIT_PRICE_'+CPP_ID).val().replace(/,/g,'') > 0){
                //         // if($(this).val().replace(/,/g,'') > 0){
                //             formData.append('NBP_UNIT_PRICE[]', $('#NBP_UNIT_PRICE_'+CPP_ID).val().replace(/,/g,''));
                //             formData.append('NBP_USE_CNT[]', $('#NBP_USE_CNT_'+CPP_ID).val());
                //             formData.append('CPPS_ID[]', $('#CPPS_ID_'+CPP_ID).val());
                //         }
                //     }else{
                //         checkWorkPartsArray.map(function(item){
                //             if(item == CPP_ID){
                //                 formData.append('NBP_UNIT_PRICE[]', $('#NBP_UNIT_PRICE_'+CPP_ID).val().replace(/,/g,''));
                //                 formData.append('NBP_USE_CNT[]', $('#NBP_USE_CNT_'+CPP_ID).val());
                //                 formData.append('CPPS_ID[]', $('#CPPS_ID_'+CPP_ID).val());
                //             }
                //         });
                //     }
                // }
                let CPP_ID = data.CPP_ID;
                let isExist = checkWorkPartsArray.find(function(item){
                    return item == CPP_ID;
                });

                if(isExist == undefined){
                    if($('#NBP_UNIT_PRICE_'+CPP_ID).val().replace(/,/g,'') > 0){
                    // if($(this).val().replace(/,/g,'') > 0){
                        formData.append('NBP_UNIT_PRICE[]', $('#NBP_UNIT_PRICE_'+CPP_ID).val().replace(/,/g,''));
                        formData.append('NBP_USE_CNT[]', $('#NBP_USE_CNT_'+CPP_ID).val());
                        formData.append('CPPS_ID[]', $('#CPPS_ID_'+CPP_ID).val());
                    }
                }else{
                    checkWorkPartsArray.map(function(item){
                        if(item == CPP_ID){
                            formData.append('NBP_UNIT_PRICE[]', $('#NBP_UNIT_PRICE_'+CPP_ID).val().replace(/,/g,''));
                            formData.append('NBP_USE_CNT[]', $('#NBP_USE_CNT_'+CPP_ID).val());
                            formData.append('CPPS_ID[]', $('#CPPS_ID_'+CPP_ID).val());
                        }
                    });
                }
            }
        }   
    });
  
    let isSendFaxArray = [];
    /** 팩스전송대상 선택에 따라 팩스 대장 YN세팅 */
    $('.NBI_RMT_ID').each(function(){
        let faxNum = '';  //팩스번호
        let chgNm = '';   //담당자
        let miiNm = '';   //보험사
        if($(this).val() == REF_RMT_DI){  // 자차
            faxNum = $('#NBI_FAX_NUM').val().replace(/-/g,'');
            chgNm = $('#NBI_CHG_NM').val();
            miiNm = $('#MII_ID option:selected').text()
        }else{ //대물
            faxNum = $('#NBI_FAX_NUM_').val().replace(/-/g,'');
            chgNm = $('#NBI_CHG_NM_').val();
            miiNm = $('#MII_ID_ option:selected').text()
        }
        let carNum = $('#NB_CAR_NUM').val();
        if($('.faxTarget').is(':visible')){
            if($('#EXCEPT_SEND_TARGET').is(':checked') == YN_Y){       // 전송제외일 경우. 팩스전송대상이 아님
                formData.append('NBI_SEND_FAX_YN[]',  YN_N);
            }else{
                const checkedFaxTarget = $('input:radio[name=FAX]:checked').val();
                if(checkedFaxTarget == RLT_BOTH_TYPE){                 // 쌍방일 경우 (자차/대물 전송대상) 
                    formData.append('NBI_SEND_FAX_YN[]',  YN_Y);
                    isSendFaxArray.push({NBI_RMT_ID : $(this).val(), IS_SEND : YN_Y, NBI_FAX_NUM:faxNum, NBI_CHG_NM:chgNm, CAR_NUM:carNum, MII_NM:miiNm});
                }else if(checkedFaxTarget == RLT_PART_TYPE ){          // 일부일 경우 (대물 전송대상) 
                    if($(this).val() == REF_RMT_DI){
                        formData.append('NBI_SEND_FAX_YN[]',  YN_N);   // 자차
                        isSendFaxArray.push({NBI_RMT_ID : $(this).val(), IS_SEND : YN_N, NBI_FAX_NUM:faxNum, NBI_CHG_NM:chgNm, CAR_NUM:carNum, MII_NM:miiNm});
                    }else{
                        formData.append('NBI_SEND_FAX_YN[]',  YN_Y);   // 대물
                        isSendFaxArray.push({NBI_RMT_ID : $(this).val(), IS_SEND : YN_Y, NBI_FAX_NUM:faxNum, NBI_CHG_NM:chgNm, CAR_NUM:carNum, MII_NM:miiNm});
                    }
                }else if(checkedFaxTarget == RLT_PI_TYPE){             // 대물일 경우 (대물 전송대상) 
                    if($(this).val() == REF_RMT_DI){  
                        formData.append('NBI_SEND_FAX_YN[]',  YN_N);   // 자차
                        isSendFaxArray.push({NBI_RMT_ID : $(this).val(), IS_SEND : YN_N, NBI_FAX_NUM:faxNum, NBI_CHG_NM:chgNm, CAR_NUM:carNum, MII_NM:miiNm});
                    }else{
                        formData.append('NBI_SEND_FAX_YN[]',  YN_Y);   // 대물
                        isSendFaxArray.push({NBI_RMT_ID : $(this).val(), IS_SEND : YN_Y, NBI_FAX_NUM:faxNum, NBI_CHG_NM:chgNm, CAR_NUM:carNum, MII_NM:miiNm});
                    }
                }else{
                    if($(this).val() == REF_RMT_PI){                   // 자차일 경우 (자차 전송대상) 
                        formData.append('NBI_SEND_FAX_YN[]',  YN_N);   // 대물
                        isSendFaxArray.push({NBI_RMT_ID : $(this).val(), IS_SEND : YN_N, NBI_FAX_NUM:faxNum, NBI_CHG_NM:chgNm, CAR_NUM:carNum, MII_NM:miiNm});
                    }else{
                        formData.append('NBI_SEND_FAX_YN[]',  YN_Y);   // 자차
                        isSendFaxArray.push({NBI_RMT_ID : $(this).val(), IS_SEND : YN_Y, NBI_FAX_NUM:faxNum, NBI_CHG_NM:chgNm, CAR_NUM:carNum, MII_NM:miiNm});
                    }
                }
            }
        }else{
            if(faxNum != ""){
                formData.append('NBI_SEND_FAX_YN[]',  YN_Y);
                isSendFaxArray.push({NBI_RMT_ID : $(this).val(), IS_SEND : YN_Y, NBI_FAX_NUM:faxNum, NBI_CHG_NM:chgNm, CAR_NUM:carNum, MII_NM:miiNm});
            }
            
        }
        
        
    });
   
    await callFormAjax(ajaxUrl, 'POST', formData, false).then(async(result)=>{
        const data = JSON.parse(result);
        if(data.status == OK){
            if(!isClickFaxBtn){
                confirmSwalOneButton("저장에 성공하였습니다.", OK_MSG,()=>{
                    resultSetData();
                });
            }else{
                $('#sendFaxModal').modal('show');
                $('#SEND_FAX').hide();
                setTimeout(async function(){
                    await isDuplicateBill(NB_ID).then((result)=>{  
                        if(result[0].NB_DUPLICATED_YN == YN_Y){
                            confirmSwalOneButton("중복 청구서입니다. 저장만 되었습니다.", OK_MSG,()=>{
                                resultSetData();
                            });
                        }else{
                         setTimeout(function(){
                                let faxForm = $("#faxForm")[0];
                                let faxFormData = new FormData(faxForm);
        
                                faxFormData.append('NB_ID', NB_ID);
                                faxFormData.append('MGG_ID', $('#SELECT_NB_GARAGE_ID').val());
                                
                                isSendFaxArray.map((item)=>{
                                    faxFormData.append('NBI_CHG_NM', item.NBI_CHG_NM);
                                    faxFormData.append('NBI_FAX_NUM', item.NBI_FAX_NUM.replace(/-/g, ''));
                                    faxFormData.append('NBI_FAX_RMT_ID', item.NBI_RMT_ID);
                                    faxFormData.append('MII_NM', item.MII_NM);
                                    faxFormData.append('CAR_NUM', item.CAR_NUM);
        
                                    if(item.IS_SEND == YN_Y){
                                        callFormAjax('../../../sendFAX.php', 'POST', faxFormData).then((result)=>{
                                            $('#sendFaxModal').modal('hide');
                                            $('#SEND_FAX').show();
                                            if(result){
                                                confirmSwalOneButton("발송 되었습니다.", OK_MSG,()=>{
                                                    resultSetData();   
                                                });
                                            }else{
                                                confirmSwalOneButton("발송 실패하였습니다..", OK_MSG,()=>{
                                                    resultSetData();
                                                });
                                            }
                                        });
                                    }
                                    
                                });
                            },250);
                        }
                    });
                },3000);
                
            }
        }else{
            alert("서버와의 연결이 원활하지 않습니다.");
        } 
    }); 
}

resultSetData = () => {
    if(opener.location.pathname == sendListPathName){
        window.opener.getList();
    }

    if($('#NB_NSRSN_TABLE').is(":visible")){
        window.open('', 'autoBill', 'status=1,width=350,height=150').focus();
        window.open('', 'autoBill', 'status=1,width=350,height=150').close();
    }
    window.close();
}

addData = () => {
    if($('#INPUT_NB_ID').val() == ''){
        getBillUUID().then((result)=>{    // 저장할 uuid값 가져오기
            UUID =  result.UUID;
            sendFormData(CASE_CREATE, result.UUID);
        });  
    }else{
        sendFormData(CASE_CREATE, $('#INPUT_NB_ID').val());
    }
        
}

/**
 * 기타사항 메모 바이트 체크
 * @param {obj : textarea}
 */
onCheckByte = (obj) => {
    const maxByte = MAXIMUM_BYTE; //최대 입력 바이트 수
    const str = obj.value;
    const str_len = str.length;
 
    let rbyte = 0;
    let rlen = 0;
    let one_char = "";
    let str2 = "";

    for (let i = 0; i < str_len; i++){
        one_char = str.charAt(i);
        if (escape(one_char).length > 4) {
            rbyte += 2; //한글2Byte
        } else {
            rbyte++; //영문 등 나머지 1Byte
        }
 
        if (rbyte <= maxByte){
            rlen = i + 1; //return할 문자열 갯수
        }
    }
 
    if (rbyte > maxByte) {
        alert("한글 " + (maxByte / 2) + "자 / 영문 " + maxByte + "자를 초과 입력할 수 없습니다.");
        str2 = str.substr(0, rlen); //문자열 자르기
        obj.value = str2;
        onCheckByte(obj, maxByte);
    } 
}



/**
 * 4.2 기타부품선택 수량 변경 이벤트
 * @param {trIdx : 몇번째 줄 기타부품인지}
 * @param {tdIdx : 왼쪽 오른쪽}
 */
onchangeEtcPartsCNT = (trIdx, tdIdx) => {
    let cnt = $('#NBP_USE_CNT_'+trIdx+'_'+tdIdx).val();
    if(cnt == ''){
        $('#NBP_USE_CNT_'+trIdx+'_'+tdIdx).val(0);
        $('#CPPS_ID_'+trIdx+'_'+tdIdx).trigger('change');  
    }

    const price = $('#CPPS_ID_'+trIdx+'_'+tdIdx+' option:selected').attr('name');
    if($('#CPPS_ID_'+trIdx+'_'+tdIdx).val() != ''){
        $('#NBP_UNIT_PRICE_'+trIdx+'_'+tdIdx).val(numberWithCommas(price));
        if(cnt != '' && cnt != 0){
            const totalPrice = Number(price.replace(/,/g,'')) * Number(cnt);
            $('#NBP_TOTAL_PRICE_'+trIdx+'_'+tdIdx).val(numberWithCommas(totalPrice));
           
        }else{
            $('#NBP_TOTAL_PRICE_'+trIdx+'_'+tdIdx).val(0);
        }
        setBillTable();
    }   
}

/**
 * 4.2 기타부품선택 기준부품 변경 이벤트
 * @param {trIdx : 몇번째 줄 기타부품인지}
 * @param {tdIdx : 왼쪽 오른쪽}
 */
onchangeEtcPartsSelect = (trIdx, tdIdx) => {
    let selectArray = [];

    if($('#CPPS_ID_'+trIdx+'_'+tdIdx).val()!=''){
        const price = $('#CPPS_ID_'+trIdx+'_'+tdIdx+' option:selected').attr('name');
        const cnt = $('#NBP_USE_CNT_'+trIdx+'_'+tdIdx).val();
       
        $('#NBP_UNIT_PRICE_'+trIdx+'_'+tdIdx).val(numberWithCommas(price));
        const totalPrice = Number(price.replace(/,/g,'')) * Number(cnt);
        $('#NBP_TOTAL_PRICE_'+trIdx+'_'+tdIdx).val(numberWithCommas(totalPrice));  
    }else{
        $('#NBP_TOTAL_PRICE_'+trIdx+'_'+tdIdx).val(0);
        $('#NBP_UNIT_PRICE_'+trIdx+'_'+tdIdx).val(0);
        $('#NBP_USE_CNT_'+trIdx+'_'+tdIdx).val(0);
        
    }
    setBillTable();

    $('.ETC_PARTS_SELECT').each(function(){  // 기타부품선택된 값 저장
        const id = $(this).attr('id');
        if($(this).val()!=''){
            selectArray.push({
                id : id,
                value : $(this).val()
            });
        }
    });

    $('.ETC_PARTS_SELECT option').attr('disabled',false);
    $('.ETC_PARTS_SELECT').each(function(){  // 선택된 값은 다른곳에서 선택 안되게
        const id = $(this).attr('id');
        selectArray.map((item)=>{
            if(id != item.id){   // 선택된 select는 제외
                $("#"+id+" option[value="+JSON.stringify(item.value)+"]").attr('disabled',true);
            }
            
        })
    });

   
    
}

/**
 * 4.1 해당 사용부품 select변경 이벤트
 * @param {CPP_ID : 변경한 기준부품 아이디}
 */
onchangePartsSubSelect = (CPP_ID) => {
    if($('#CPPS_ID_'+CPP_ID).val() == ''){   // 사용안함일 때..
        const unCheckedCPP_ID = $('#CPPS_ID_'+CPP_ID+' option:selected').attr('name');
        $('#NBP_UNIT_PRICE_'+unCheckedCPP_ID).val(0);
        $('#NBP_UNIT_PRICE_'+unCheckedCPP_ID).trigger('change');
        $('#NBP_USE_CNT_'+unCheckedCPP_ID).val(0);

        garageUsePartsList = garageUsePartsList.filter((item)=>{
            return unCheckedCPP_ID != item.CPP_ID;
        });

    }else{
        const partsSubPrice = $('#CPPS_ID_'+CPP_ID+' option:selected').attr('name');
        $('#NBP_UNIT_PRICE_'+CPP_ID).val(numberWithCommas(partsSubPrice));

        const selectValue = JSON.parse($('#CPPS_ID_'+CPP_ID).val());

        stdPartsList.map((item)=>{
            if(item.CPP_ID == selectValue.CPP_ID){
                garageUsePartsList.push(item);
            }
        });
        const checkedWorkPartsCheckboxCNT = $('input[name=RRT_TD_CHECKBOX]:checked').length;
        if(checkedWorkPartsCheckboxCNT > 0){
            if($('#RCT_CD').val() == ''){
                alert("차량을 선택해주세요.");
                $('#CCC_NM').focus();
                $('.NBP_USE_CNT').val('0');
                $('.NBP_TOTAL_PRICE').val('0');
            }else{
                onChangeUseCntOrPrice(CPP_ID);
                // accountReckonParts();
            }
            
        }else{
            $('.NBP_USE_CNT').val('0');
            $('.NBP_TOTAL_PRICE').val('0');
        }
        
    }

    setWorkPartsUsePartsTable(CPP_ID, $('#CPPS_ID_'+CPP_ID).val());
}

/** 
 * 2.보험사선택 
 * 담당자 키보드 이벤트
 * @param {selectId : 클릭했을 떄 클릭된 아이디 값을 저장해줄 input 해당 아이디}
 */
function onchangeInsuManager(type, evt){
    const charCode = (evt.which) ? evt.which : event.keyCode;  
    
    if(charCode != 13){
        $('#SELECT_NBI_CHG_NM'+type).val('');
        $('#RICT_ID'+type).val('');
    }
   
}
onblurInsuManager = (type) => {
    if($('#SELECT_NBI_CHG_NM'+type).val() == ''){
        $('#INSU_MANAGE_COUNT'+type).html('');
        $('#INSU_MANAGE_REGIST_DATE'+type).html('');  
        $('#RICT_ID'+type).val('');  
        $('#RICT_NM'+type).html('');     
        
    }
}


/** 
 * 2.보험사선택 
 * 보상담당지정 버튼 이벤트
 */
function openInsuManageModal(){
    let isValidate = true;

    
    $('.MII_ID_LIST').each(function(){
        if($(this).is(':visible')){
           const idType = ($(this).attr('id').split("_")[3] == RLT_MY_CAR_TYPE) ? '' : '_';
           if(!$('#SELECT_NB_GARAGE_ID').val()){
                isValidate = false;
                alert("공업사를 선택해주세요.");
                $('#NB_GARAGE_NM').focus();
                return false;
           }else if($('#MII_ID'+idType).val() == ''){
                 isValidate = false;
                alert("보험사를 선택해주세요.");
                $('#MII_ID'+idType).focus();
                return false;
           }else if($('#NBI_CHG_NM'+idType).val() == ''){
                isValidate = false;
                alert("담당자를 입력/선택해주세요.");
                $('#NBI_CHG_NM'+idType).focus();
                return false;
           }else if($('#SELECT_NBI_CHG_NM'+idType).val() == ''){
                isValidate = false;
                alert("등록되지 않은 담당자 혹은 선택되지 않은 담당자 입니다.\n신규담당자일 경우 담당자 신규등록 후 보상담당지정이 가능합니다.");
                $('#SELECT_NBI_CHG_NM'+idType).focus();
                return false;
           }else {
                isValidate = true;
           }
        }
       
    });

    if(isValidate){
        $('#cpst-select').modal('show');
    }


}

/** 접수번호 변동시 중복검사 여부 false로 */
onchangeRegiNumber = (type) => {
    isClickOverlapBtn = false;
}

/** 수리공장명을 입력할때는 선택 value를 초기화. 입력하고 선택할수 있게 유도 */
onchangeGarageNM = (evt) => {
    const charCode = (evt.which) ? evt.which : event.keyCode;  
    
    if(charCode != 13){
        isClickOverlapBtn = false;
        $('#SELECT_NB_GARAGE_NM').val('');
        $('#SELECT_NB_GARAGE_ID').val('');
    }   
}

/** 차량번호 변동시 중복검사 여부 false로  */
onchangeCarNumber = () => {
    isClickOverlapBtn = false;
}

/** 차량명 선택입력할수 있게 값 초기화 */
onchangeCarType = (evt) => {
    const charCode = (evt.which) ? evt.which : event.keyCode;  
    
    if(charCode != 13){
        $('#CCC_ID').val('');                         
        $('#RCT_CD').val('');
    }   
    
}

/**
 * 사용부품B 해제 버튼 클릭시
 * @param {CPP_ID : 해제누른 사용부품B 아이디}
 */
onclickBtypeReleaseBtn = (CPP_ID) => {
    $('.B_'+CPP_ID).prop('checked',false);
}


/**
 * 좌츣/우측해제 버튼 클릭시
 * @param {RRT_ID : 교환인지 판금인지}
 * @param {RWPP_ID : 좌인지 우인지}
 */
onclickReleaseBtn = (RRT_ID , RWPP_ID) => {
    if(RWPP_ID == WORK_TYPE_LEFT){ 
        $('.'+RRT_ID+"_"+WORK_PART_INSIDE_TYPE).prop('checked',false);  
        $('.'+RRT_ID+"_"+WORK_PART_LOWER_TYPE).prop('checked',false);    
        $('.'+RRT_ID+"_"+WORK_PART_INSIDE_TYPE).trigger('change');
        $('.'+RRT_ID+"_"+WORK_PART_LOWER_TYPE).trigger('change');
    }else{
        $('.'+RRT_ID+"_"+WORK_PART_LOWER_TYPE).prop('checked',false);   
        $('.'+RRT_ID+"_"+WORK_PART_LOWER_TYPE).trigger('change');
    }
    $('.'+RRT_ID+"_"+RWPP_ID).prop('checked',false);  
    $('.'+RRT_ID+"_"+WORK_TYPE_ONE).prop('checked',false);  
    $('.'+RRT_ID+"_"+RWPP_ID).trigger('change');
    $('.'+RRT_ID+"_"+WORK_TYPE_ONE).trigger('change');
}


onCheckPartsType = (CPP_ID) => {
    const partsType = stdPartsList.find((item)=>{
        return item.CPP_ID == CPP_ID;
    });

    return partsType.CPP_PART_TYPE 
}

/**
 *  5. 작업항목선택 교환/판금 클릭/해제시 사용가능한 사용부품 백그라운드 색깔 세팅
 * @param {RRT_ID : 교환인지 판금인지}
 * @param {NWP_ID : 해당 작업항목}
 * @param {CPP_ID : 사용가능한 부품 아이디}
 */
setPartsTableBackground = (RRT_ID, NWP_ID, CPP_ID) => {
    let bgColor = '';
    if(RRT_ID == RACON_CHANGE_CD){
        bgColor = DEEP_GREEN_CLASS;
    }else{
        bgColor = DEEP_BLUE_CLASS;    
    }

    $('#PARTS_TD_'+NWP_ID+"_"+CPP_ID).css('background-color', bgColor);
}

/**
 *  5. 작업항목선택 교환/판금 클릭/해제시 사용가능한 사용부품 백그라운드 색깔 세팅
 * @param {RRT_ID : 교환인지 판금인지}
 * @param {NWP_ID : 해당 작업항목}
 * @param {CPP_ID : 사용가능한 부품 아이디}
 */
resetPartsTableBackground = (NWP_ID, CPP_ID) => {
    let bgColor = $('#PARTS_TD_'+NWP_ID+"_"+CPP_ID).css('background-color');
    let setBgColor = '';

    if(bgColor != 'rgba(0, 0, 0, 0)'){
        if(onCheckPartsType(CPP_ID) == 'A'){
            setBgColor = '#b2dbb3';
        }else{
            setBgColor = '#CCE9FF';
        }    
    }else{
        setBgColor = 'transparent'; 
    }
    
    $('#PARTS_TD_'+NWP_ID+"_"+CPP_ID).css('background-color', setBgColor);
}

/**
 * 5.작업항목 선택 각 작업항목에 클릭한 수리부위에 대체/체화
 */
setReplacePartsTableDisabled = (checkArray, NWP_ID) => {
    checkArray.map((data)=>{
        let CPP_ID = '';
        
        garageUsePartsList.map((item)=>{
           
            if(data.FROM_CPP_ID == item.CPP_ID){
                CPP_ID = data.FROM_CPP_ID ;
                $('#PARTS_CHECK_'+NWP_ID+"_"+CPP_ID).attr('disabled',false);  
                setPartsTableBackground(data.RRT_ID, NWP_ID, CPP_ID) ;
            }

            if(data.TO_CPP_ID == item.CPP_ID){
                CPP_ID = data.TO_CPP_ID ;
                $('#PARTS_CHECK_'+NWP_ID+"_"+CPP_ID).attr('disabled',false);
                setPartsTableBackground(data.RRT_ID, NWP_ID, CPP_ID) ;
                
            }
           
        });
        
    });
}




setPriorityUsePartsCheckbox = (NWP_ID, CPP_ID, RRT_ID, replaceArray) => {
    $('#PARTS_CHECK_'+NWP_ID+"_"+CPP_ID).prop('disabled',false);
    $('#PARTS_CHECK_'+NWP_ID+"_"+CPP_ID).prop('checked',true); 
    onChangePartsCheckbox(NWP_ID, CPP_ID, replaceArray); 
    setPartsTableBackground(RRT_ID, NWP_ID, CPP_ID) ;
}
/**
 * 각 작업항목에 클릭한 수리부위에 우선순위
 */
setPartsTableDisabled = (priorityArray, replaceArray, NWP_ID, CPP_ID) => {
    priorityArray.map((data)=>{    // 우선순위 체크
        let isCheck = false;
        if(CPP_ID == undefined){
            garageUsePartsList.map((item)=>{
                if(data.CPP_ID == item.CPP_ID){
                    setPriorityUsePartsCheckbox(NWP_ID, item.CPP_ID, data.RRT_ID, replacePartsPlst) ;
                    isCheck = true;
                }
            });
        }else{
            if(data.CPP_ID == CPP_ID){
                setPriorityUsePartsCheckbox(NWP_ID, CPP_ID, data.RRT_ID, replacePartsPlst) ;
                isCheck = true;
            }else{
                const IS_EXIST = isExistUseWorkPartsList(data.NWP_ID, data.RRT_ID, data.CPP_ID);
                if(IS_EXIST != undefined){
                    setPartsTableBackground(data.RRT_ID, data.NWP_ID, data.CPP_ID) ;
                    $('#PARTS_CHECK_'+data.NWP_ID+"_"+data.CPP_ID).prop('disabled',false);
                }
            }
        }
        

        if(!isCheck){     // 우선순위 중 공업사에서 사용안할경우 대체 체크
            replaceArray.map((replaceData)=>{
                if(replaceData.NWP_ID == NWP_ID){
                    if(data.CPP_ID  == replaceData.FROM_CPP_ID){
                        if(CPP_ID == undefined){
                            garageUsePartsList.map((item)=>{    // 대체/체화 중에서도 공업사에서 사용가능한 사용부품인지를 체크
                                if(replaceData.TO_CPP_ID == item.CPP_ID){
                                    $('#PARTS_CHECK_'+NWP_ID+"_"+replaceData.TO_CPP_ID).prop('disabled',false);
                                    $('#PARTS_CHECK_'+NWP_ID+"_"+replaceData.TO_CPP_ID).prop('checked',true);
                                    setPartsTableBackground(replaceData.RRT_ID, replaceData.NWP_ID, replaceData.TO_CPP_ID) ;
                                }else{
                                    const IS_EXIST = isExistUseWorkPartsList(replaceData.NWP_ID, replaceData.RRT_ID, replaceData.TO_CPP_ID);
                                    if(IS_EXIST != undefined){
                                        setPartsTableBackground(replaceData.RRT_ID, replaceData.NWP_ID, replaceData.TO_CPP_ID) ;
                                        $('#PARTS_CHECK_'+replaceData.NWP_ID+"_"+replaceData.TO_CPP_ID).prop('disabled',false);
                                    }
                                }
                            });
                        }else{
                            if(replaceData.TO_CPP_ID == CPP_ID){
                                $('#PARTS_CHECK_'+NWP_ID+"_"+replaceData.TO_CPP_ID).prop('disabled',false);
                                $('#PARTS_CHECK_'+NWP_ID+"_"+replaceData.TO_CPP_ID).prop('checked',true);
                                setPartsTableBackground(replaceData.RRT_ID, replaceData.NWP_ID, replaceData.TO_CPP_ID) ;
                            }else{
                                const IS_EXIST = isExistUseWorkPartsList(replaceData.NWP_ID, replaceData.RRT_ID, replaceData.FROM_CPP_ID);
                                if(IS_EXIST != undefined){
                                    setPartsTableBackground(replaceData.RRT_ID, replaceData.NWP_ID, replaceData.FROM_CPP_ID) ;
                                    $('#PARTS_CHECK_'+replaceData.NWP_ID+"_"+replaceData.FROM_CPP_ID).prop('disabled',false);
                                }
                            }
                        }
                    }      
                } 
            });
        }     
    });
}

/**
 * 공업사에서 사용가능한 부품에 클릭한 사용부품이 존재하는지..
 * @param {CPP_ID :  선택한 사용부품 아이디}
 */
onCheckUseParts = (CPP_ID) => {
    let isExistCPP_ID = false;  
    garageUsePartsList.map((item)=>{
        if(CPP_ID == item.CPP_ID){
            isExistCPP_ID = true;
        }
    });
    return isExistCPP_ID;
}


/**
 * 사용부품 대체/체화 체크해주기
 * @param {dataArray : 대체/체화 데이터}
 * @param {NWP_ID : 해당 작업항목 아이디}
 * @param {CPP_ID : 해당 사용부품 아이디}
 * @param {TYPE : FROM 인지 TO 인지}
 */
setCheckReplaceData = (dataArray, NWP_ID, CPP_ID, TYPE) => {
    dataArray.map((item)=>{
        let replaceData = (TYPE == 'FROM') ? item.TO_CPP_ID : item.FROM_CPP_ID;
        if(onCheckUseParts(replaceData)){  // 그 대체 상품이 공업사에서 사용가능한 상품인지..
            $('#PARTS_CHECK_'+NWP_ID+"_"+replaceData).prop('disabled',false);
            if($('#PARTS_CHECK_'+NWP_ID+"_"+CPP_ID).is(':checked')){
                $('#PARTS_CHECK_'+NWP_ID+"_"+replaceData).prop('checked',false);
            }else{
                $('#PARTS_CHECK_'+NWP_ID+"_"+replaceData).prop('checked',true);
            } 
        }
    });
}

/**
 * 사용부품 체크박스 (우선순위 체크 및 대체/체화 유무 확인 후 체크)
 * @param {NWP_ID : 해당 작업항목}
 * @param {CPP_ID : 선택하 사용부품아이디}
 */
onChangePartsCheckbox = (NWP_ID, CPP_ID, replaceList) => {
    /** 대체/체하 리스트가 없으면 그냥 단순 체크/해제가 이루어지면 된다.
     * 하지만 대체/체화 리스트가 있는 경우에는 우선부품을 해당 공업사가 사용을 안할경우 그 대체 부품이 클릭되어야 한다.
     */
    const replacePartsList = replaceList == undefined ? replacePartsPlst : replaceList;

    const FROM_CPP_ID_ARRAY = replacePartsList.filter((item)=>{
        return item.NWP_ID == NWP_ID && item.FROM_CPP_ID == CPP_ID; 
    });
    const TO_CPP_ID_ARRAY = replacePartsList.filter((item)=>{
        return item.NWP_ID == NWP_ID && item.TO_CPP_ID == CPP_ID; 
    });

    if(FROM_CPP_ID_ARRAY.length > 0){
        setCheckReplaceData(FROM_CPP_ID_ARRAY, NWP_ID, CPP_ID, 'FROM');
    }else if(TO_CPP_ID_ARRAY.length > 0){
        setCheckReplaceData(TO_CPP_ID_ARRAY, NWP_ID, CPP_ID, 'TO');
    }
}

resetCheckBox = (NWP_ID, CPP_ID) => {
    $('#PARTS_CHECK_'+NWP_ID+"_"+CPP_ID).prop('disabled',true);
    $('#PARTS_CHECK_'+NWP_ID+"_"+CPP_ID).prop('checked',false);
 
    resetPartsTableBackground(NWP_ID, CPP_ID);
}

/**
 * 교환/판금 수리부위체크여부
 * @param {NWP_ID : 작업항목}
 * @param {RRT_ID : 교환인지 판금인지}
 * @param {RWPP_ID : 작업부위}
 * @param {RWT_ID : 바닥인지 아닌지}
 * @param {CPP_ID : 수정모드에 필요한 데이터로 선택된 사용부품값}
 */
onCheckReckonCheckbox = async(NWP_ID, RRT_ID, RWPP_ID, RWT_ID, CPP_ID) => {
    if($('#SELECT_NB_GARAGE_NM').val() != ''){
        /**
         * 교환/판금이 클릭이 된 상태일 경우에만!!
         * 클릭된 작업항목을 체크하면서 체크된 반대쪽의 체크를 풀어준다
         */
        if($('#CHECK_'+NWP_ID+'_'+RRT_ID+'_'+RWPP_ID).is(':checked')){
            $('.CHECK_'+NWP_ID).each(function(){
                const rrtId = JSON.parse($(this).val()).RRT_ID;
                const rwppId = JSON.parse($(this).val()).RWPP_ID;
                
                if(rrtId != RRT_ID && rwppId == RWPP_ID){
                    if($('#CHECK_'+NWP_ID+'_'+rrtId+'_'+rwppId).is(':checked')){
                        $('#CHECK_'+NWP_ID+'_'+rrtId+'_'+rwppId).prop('checked', false);
                        $('#CHECK_'+NWP_ID+'_'+rrtId+'_'+rwppId).trigger('change');
                    }
                }
            });
        }

        if(RWT_ID == RWP_ID_DOWN){ //바닥
            if(RWPP_ID == WORK_PART_INSIDE_TYPE){ // 실내일경우
                const isChecked = $('#CHECK_'+NWP_ID+'_'+RRT_ID+'_'+WORK_PART_INSIDE_TYPE).is(':checked');
                $('#CHECK_'+NWP_ID+'_'+RRT_ID+'_'+WORK_PART_INSIDE_TYPE).prop('checked', isChecked);
                $('#CHECK_'+NWP_ID+'_'+RRT_ID+'_'+WORK_PART_LOWER_TYPE).prop('checked', isChecked);
                onChangeCheckbox(NWP_ID, RRT_ID, WORK_PART_LOWER_TYPE, CPP_ID) ;
            }
        }
      
        await onChangeCheckbox(NWP_ID, RRT_ID, RWPP_ID, CPP_ID) ;
    }else{
        alert("수리공장을 입력후 선택해주세요.");
        $('#CHECK_'+NWP_ID+'_'+RRT_ID+'_'+RWPP_ID).attr('checked', false);
    }
}
/**
 * 수리부위 checkbox 이벤트 (교환/판금)
 * @param {NWP_ID : 작업항목}
 * @param {RRT_ID : 교환인지 판금인지}
 * @param {RWPP_ID : 작업부위}
 * @param {CPP_ID : 수정모드에 필요한 데이터로 선택된 사용부품값}
 */
onChangeCheckbox = async(NWP_ID, RRT_ID, RWPP_ID, CPP_ID) => {
    if($('#SELECT_NB_GARAGE_NM').val() != ''){
        if(!$('#CHECK_'+NWP_ID+'_'+RRT_ID+'_'+RWPP_ID).is(':checked')){
            let p = [];
             /** 우선순위 제거 */
             priorityPartsList.map((item)=>{
                if(item.RRT_ID == RRT_ID && item.RWPP_ID == RWPP_ID && item.NWP_ID == NWP_ID){
                    resetCheckBox(item.NWP_ID, item.CPP_ID);
                }else{
                    p.push(item);
                }
            });
            priorityPartsList = p;

            /** 대체/체화 배열에 클릭해제된 아이템들은 제거(교환/판금 아이디로 구분) */
            let r = [];
            replacePartsPlst.map((item)=>{
               if(item.RRT_ID == RRT_ID && item.RWPP_ID == RWPP_ID && item.NWP_ID == NWP_ID){
                    resetCheckBox(item.NWP_ID, item.TO_CPP_ID);
                    resetCheckBox(item.NWP_ID, item.FROM_CPP_ID);
               }else{
                   r.push(item);
               }
           });

           replacePartsPlst = r;
           setPartsTableDisabled(priorityPartsList.filter((item)=>{return item.NWP_ID == NWP_ID}), replacePartsPlst,  NWP_ID);
           setReplacePartsTableDisabled(replacePartsPlst.filter((item)=>{return item.NWP_ID == NWP_ID}), NWP_ID);
        }else{
            /** 체크됐을 때는 단순히 체크가능한 체크박스들 disabled를 풀어준다. */
            const checkArray = await getWorkPartsInfo(NWP_ID, RRT_ID, RWPP_ID);
            console.log("checkArray===>",checkArray);
            if(checkArray.length > 0){
                if(priorityPartsList.length == 0){
                    priorityPartsList = checkArray[0];
                }else{
                    checkArray[0].map((checkItem)=>{
                        let IS_EXIST = priorityPartsList.find(function(priorityItem){
                            return priorityItem.NWP_ID == checkItem.NWP_ID && priorityItem.RRT_ID == checkItem.RRT_ID 
                            && priorityItem.RWPP_ID == checkItem.RWPP_ID && priorityItem.CPP_ID == checkItem.CPP_ID;
                        });
                        if(IS_EXIST == undefined){
                            priorityPartsList.push(checkItem);
                        }

                    });  
                }

                if(replacePartsPlst.length == 0){
                    replacePartsPlst = checkArray[1];
                }else{
                    checkArray[1].map((checkItem)=>{
                        let IS_EXIST = replacePartsPlst.find(function(replaceO){
                            return replaceO.NWP_ID == checkItem.NWP_ID && replaceO.RRT_ID == checkItem.RRT_ID 
                            && replaceO.RWPP_ID == checkItem.RWPP_ID && (replaceO.TO_CPP_ID == checkItem.TO_CPP_ID || replaceO.FROM_CPP_ID == checkItem.FROM_CPP_ID);
                        });
                        if(IS_EXIST == undefined){
                            replacePartsPlst.push(checkItem);
                        }
                       
                    });  
                }

                setPartsTableDisabled(checkArray[0], checkArray[1], NWP_ID, CPP_ID);
                setReplacePartsTableDisabled(checkArray[1], NWP_ID);
            }
        }
    }else{
        alert("수리공장을 입력후 선택해주세요.");
        $('#CHECK_'+NWP_ID+'_'+RRT_ID+'_'+RWPP_ID).attr('checked', false);
    }
    
}

/**
 * 우선,대체/체화 리스트에 해당 사용부품이 있는지..
 * 있다면 그 사용부품은 공업사에사 사용가능한 부품인지 여부를 판단후 리턴
 */
function isExistUseWorkPartsList(NWP_ID, RRT_ID, CPP_ID){
    let CPP_ITEM = priorityPartsList.find(function(item){
        return item.NWP_ID ==  NWP_ID && item.RRT_ID == RRT_ID && item.CPP_ID == CPP_ID;
    });

    if(CPP_ITEM == undefined){
        CPP_ITEM = replacePartsPlst.find(function(item){
            return item.NWP_ID ==  NWP_ID && item.RRT_ID == RRT_ID && item.CPP_ID == CPP_ID;
        });
    }

    let IS_EXIST_CPP_ID  = undefined;
    if(CPP_ITEM != undefined){
        IS_EXIST_CPP_ID = garageUsePartsList.find(function(item){
            return item.CPP_ID == CPP_ITEM.CPP_ID
        });
    }
   
    return IS_EXIST_CPP_ID;
}

function sendFax(){
    let text =   "";

    if(isOverlapBill && isClickOverlapBtn){
        text = "중복건은 발송되지 않습니다. 저장만 진행하시겠습니까?";
        isClickFaxBtn = false;
    }else if($('#EXCEPT_SEND_TARGET').is(":checked")){
        text = "전송제외건은 발송되지 않습니다. 저장만 진행하시겠습니까?";
        isClickFaxBtn = false;
    }else{
        isClickFaxBtn = true;
        
    }

    if(isClickFaxBtn){
        // $('#claimForm').submit();
        accountReckonParts(true);
    }else{
        confirmSwalTwoButton(text,'확인', '취소', false ,()=>{
            // $('#claimForm').submit();
            accountReckonParts(true);
        });
    }
}

function removeHyphen(evt){
    const _value = event.srcElement.value;  
    const objTarget = evt.srcElement || evt.target;
    objTarget.value = replaceAll(_value,'-','');
    objTarget.focus();
} 