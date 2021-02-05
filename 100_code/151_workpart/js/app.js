const ajaxUrl = 'act/workpart_act.php';
const RWP_ID_1 = '30111';                   //단일
const RWP_ID_BOTH = '30112';                //좌우
const RWP_ID_DOWN = '30113';                //바닥
const PRIORITY = 'PRIORITY';                // 타입이 우선
const FLOOR = 'FLOOR';                      // 타입이 바닥 
const REPLACE = 'REPLACE';                  // 타입이 대체/체화

let stdPartsArray = [];     //기준부품리스트 배열
let rwppArray = [];         //우선순위 배열 (0:단일, 1:좌, 2:우, 3:실내, 4:하체)
let reckonArray = [];       //교환/판금(0:교환, 1:판금)
let replaceArray = [];      // 대체/체화에 대한 정보를 가지고 있는 배열

$(document).ready(()=>{ 
    getRefInfo(GET_REF_WORK_PARIORITY_TYPE);
    getRefInfo(GET_REF_RECKON_TYPE);

    /** 우선에서 저장버튼 눌렀을 때 */
    $("#priorityForm").submit(function(e){
        e.preventDefault();
        addDataPriority();
    });

    /** 대체/체화에서 저장버튼을 눌렀을 때 */
    $("#replaceForm").submit(function(e){
        e.preventDefault();
        addDataReplace();
    });

    /** 대체/체화에서 저장버튼을 눌렀을 때 */
    $("#floorForm").submit(function(e){
        e.preventDefault();
        addDataFloor();
    });
    
    /** 대체/주화 모달이 클로즈 될 때 이벤트 */
    $('#replace-comp').on('hide.bs.modal', function (event) {
        $('#replaceToTable').hide();
        $('#replaceTable select').val(YN_N);
        $('#replaceTable td').css('background-color', 'white');
        replaceArray = [];
    });
    
});

excelDownload = () => {
    location.href="workPartExcel.html";
}

/**
 * 대체/체화모달에서 리셋기능.
 * FROM_TABLE에 셀렉트 초기화 및 TO_TABLE에 체크박스 및 텍스트 초기화
 */
resetReplaceToTable = () => {
    $('#replaceToTbody input[type=checkbox]').prop('checked', false);
    $('#replaceTable td').css('background-color', 'white');
    $('#replaceToTbody input[type=text]').val('');
    $('#replaceToTbody input[type=text]').attr('readonly', true);
}


/**
 * 기준부품리스트
 */
getStdPartsList = () => {
    const data = {
        REQ_MODE: GET_PARTS_INFO
    };
    callAjax(ajaxUrl, 'POST', data).then((result)=>{
        if(isJson(result)){
            const data = JSON.parse(result);
            if(data.status == OK){
                stdPartsArray = data.data;
                setPriorityTable(stdPartsArray);    // 우선순위 테이블 만들기
                setReplaceTable(stdPartsArray);     // 대체/체화 테이블 만들기
            }else{
                alert(ERROR_MSG_1100);
            }
        }else{
            alert(ERROR_MSG_1200);
        }
    });
}

/**
 * 판금/교환 및 우선순위 작업항목 REF 가져오기
 * @param {REQ_MODE : 우선인지 대체/체화인지 바닥인지}
 */
getRefInfo = (REQ_MODE) => {
    const data = {
        REQ_MODE: REQ_MODE
    };
    callAjax(ajaxUrl, 'POST', data).then((result)=>{
        if(isJson(result)){
            const data = JSON.parse(result);
            if(data.status == OK){
                if(REQ_MODE == GET_REF_RECKON_TYPE){     
                    reckonArray = data.data;
                }else{
                    rwppArray = data.data;
                }

                if(reckonArray.length >0 && rwppArray.length > 0){
                    getWorkPartList();     // table 세팅
                    getStdPartsList();     // 기준부품 리스트
                }
                
            }else{
                alert(ERROR_MSG_1100);
            }
            
        }else{
            alert(ERROR_MSG_1200);
        }
    });
}

/**
 * 대체/체화 모달에서 사용부품 체크박스 클릭 이벤트
 * @param {eve : 해당 체크박스}
 */
onChangeReplaceCheckbox = (evt) => {
    const objTarget = evt.srcElement || evt.target;
    const objValue = $(objTarget).val();
    if($(objTarget).is(':checked')){
        $('#REPLACE_RATIO_'+objValue).attr('readonly',false);
    }else{
        $('#REPLACE_RATIO_'+objValue).val('');
        $('#REPLACE_RATIO_'+objValue).attr('readonly',true);
    }
}


/**
 * 바닥산출
 */
addDataFloor = () => {
    let form = $("#floorForm")[0];
    let formData = new FormData(form);
    formData.append('REQ_MODE', CASE_CREATE);
    formData.append('TYPE', FLOOR);

    callFormAjax(ajaxUrl, 'POST', formData).then((result)=>{
        const data = JSON.parse(result);
        if(data.status == OK){
            confirmSwalOneButton("저장에 성공하였습니다.", OK_MSG,()=>{
                getItemCount($('#FLOOR_NWP_ID').val(), $('#FLOOR_RRT_ID').val(), $('#FLOOR_RWPP_ID').val(), FLOOR, true);
                $('#floor-setting').modal('hide');
            });
        }else{
            alert("서버와의 연결이 원활하지 않습니다.");
        } 
    });  
}

/**
 * 우선순위 저장
 */
addDataReplace = () => {
    let isValidate = true;    //체크된 부품들 중 배율이 입력 되었는지를 체그
    $('#replaceForm input[type=text]').removeAttr('name');

    /** 값이 있는 텍스트박스에만 name을 부여해준다. */
    $('#replaceForm input[type=text]').each(function(){
        if(!$(this).attr('readonly')){
            if($(this).val().trim() == ''){
                alert("배율을 입력해주셔야 합니다.");
                $(this).focus();
                isValidate = false;
                return false;
            }else{
                $(this).attr('name','NWE_RATIO[]');
            }
        }
    });

    if(isValidate){
        let form = $("#replaceForm")[0];
        let formData = new FormData(form);
        formData.append('REQ_MODE', CASE_CREATE);
        formData.append('TYPE', REPLACE);
        
        callFormAjax(ajaxUrl, 'POST', formData).then((result)=>{
            const data = JSON.parse(result);
            if(data.status == OK){
                confirmSwalOneButton("저장에 성공하였습니다.", OK_MSG,()=>{
                    getItemCount($('#REPLACE_NWP_ID').val(), $('#REPLACE_RRT_ID').val(), $('#REPLACE_RWPP_ID').val(), REPLACE, true);
                    showReplaceModal();
                });
            }else{
                alert("서버와의 연결이 원활하지 않습니다.");
            } 
        });  
    }
    
    
}

/**
 * 우선순위 저장
 */
addDataPriority = () => {
    let form = $("#priorityForm")[0];
    let formData = new FormData(form);
    formData.append('REQ_MODE', CASE_CREATE);
    formData.append('TYPE', PRIORITY);
     
    callFormAjax(ajaxUrl, 'POST', formData).then((result)=>{
        const data = JSON.parse(result);
        if(data.status == OK){
            confirmSwalOneButton("저장에 성공하였습니다.", OK_MSG,()=>{
                getItemCount($('#NWP_ID').val(), $('#RRT_ID').val(), $('#RWPP_ID').val(), PRIORITY, true);
                $('#priority-comp').modal('hide');
            });
        }else{
            alert("서버와의 연결이 원활하지 않습니다.");
        } 
    });  
    
}

/**
 * 바닥 선택
 * @param {workData : 클릭한 해당라인 작업항목 정보}
 * @param {reckonId : 해당 판금/교환 중 뭐?}
 * @param {rwppId : 단일 인지 좌,우 인지 실내,바닥인지}
 */
showPartModal = (workData, reckonId, rwppId) => {
    getItemCount(workData.NWP_ID, reckonId, rwppId, FLOOR).then((data)=>{   
        if(data[0].NM != null && data[0].NM > 0){
            $('#NWP_FLOOR_DATA').val(data[0].NM );
        }else{
            $('#NWP_FLOOR_DATA').val('');
        }

        $('#FLOOR_NWP_ID').val(workData.NWP_ID);
        $('#FLOOR_RRT_ID').val(reckonId);
        $('#FLOOR_RWPP_ID').val(rwppId);
            
        $('#floor-title').html(workData.RWP_NM2+" : "+workData.RWP_NM1);
        $('#floor-setting').modal();
    });
}


/**
 * 대체/체화 선택
 * @param {nwpId : 클릭한 해당라인 작업항목 정보}
 * @param {reckonId : 해당 판금/교환 중 뭐?}
 * @param {rwppId : 단일 인지 좌,우 인지 실내,바닥인지}
 */
showReplaceModal = (nwpId, reckonId, rwppId) => {
    const NWP_ID = nwpId == undefined ?  $('#REPLACE_NWP_ID').val() : nwpId;
    const RRT_ID = reckonId == undefined ?  $('#REPLACE_RRT_ID').val() : reckonId;
    const RWPP_ID = rwppId == undefined ?  $('#REPLACE_RWPP_ID').val() : rwppId;

    getItemCount(NWP_ID, RRT_ID, RWPP_ID, REPLACE).then((data)=>{
       $('#replaceTable select').val('N');
        if(data.length > 0){    
            data.map((item)=>{
                $('#REPLACE_SELECT_'+item.FROM_CPP_ID).val('Y');
            });
            replaceArray = data;
        }else{
            replaceArray = [];
        }
        
        $('#REPLACE_NWP_ID').val(NWP_ID);
        $('#REPLACE_RRT_ID').val(RRT_ID);
        $('#REPLACE_RWPP_ID').val(RWPP_ID);
          

        $('#replace-comp').modal();
    });
}

/**
 * 우선순위 선택
 * @param {workData : 클릭한 해당라인 작업항목 정보}
 * @param {reckonId : 해당 판금/교환 중 뭐?}
 * @param {rwppId : 단일 인지 좌,우 인지 실내,바닥인지}
 */
showPriorityModal = (workData, reckonId, rwppId) => {
    getItemCount(workData.NWP_ID, reckonId, rwppId, PRIORITY).then((data)=>{
        $('#priorityTable input[type=checkbox]').prop('checked', false);
        if(data.length > 0){
            $('#priorityTable input[type=checkbox]').each(function(){
                data.map((item)=>{
                    if(item.CPP_ID == $(this).val()){
                        $(this).prop('checked',true);
                    }
                });
            });
        }

        $('#RWPP_ID').val(rwppId);
        $('#RRT_ID').val(reckonId);
        $('#NWP_ID').val(workData.NWP_ID);

        $('#priority-title').html(workData.RWP_NM2+" : "+workData.RWP_NM1);
        $('#priority-comp').modal();
    });
}

/**
 * 각 테이블에서 설정된 값을 가져오기
 * @param {NWP_ID : 고유 작업항목 아이디}
 * @param {RRT_ID : 교환/판금}
 * @param {RWPP_ID : 단일/좌,우/실내,하체}
 * @param {TYPE : 우선/대체,체화/바닥}
 * @param {IS_CALLBACK : 콜백없이 데이터만 리턴해줄지 아닐지(undefined시에는 데이터만 리턴, 아니면 함수 실행)}
 */
getItemCount = (NWP_ID, RRT_ID, RWPP_ID, TYPE, IS_CALLBACK) => {
    const data = {
        REQ_MODE: CASE_READ,
        NWP_ID : NWP_ID,
        RRT_ID : RRT_ID,
        RWPP_ID : RWPP_ID,
        TYPE : TYPE

    };
    return callAjax(ajaxUrl, 'POST', data).then((result)=>{
        if(isJson(result)){
            const data = JSON.parse(result);
            if(IS_CALLBACK == undefined){
                return data;
            }else{
                onChangeBackgrounColor(data,NWP_ID, RRT_ID, RWPP_ID, TYPE);   // 각 항목에 설정된 값이 있는지를 체크해서 백그라운드 색깔으 ㄹ바꿔줌
            }
           
        }else{
            alert(ERROR_MSG_1200);
        }
    });
}

let count = 1;   // 설정값 체크가 끝나면 화면을 띄어주기 위한 버튼 설정 카운트
/**
 * 각 항목에 설정된 값이 있을 경우 리스트에서 버튼 칼라를 다르게 설정하기 위해서
 * @param {data : 해당 데이터(데이터 유무판단)}
 * @param {NWP_ID : 해당 버튼 아이디를 위한 부분 중 하나}
 * @param {RRT_ID : 해당 버튼 아이디를 위한 부분 중 하나}
 * @param {RWPP_ID : 해당 버튼 아이디를 위한 부분 중 하나}
 * @param {TYPE : 우선,대체/체화, 바닥인지..}
 */
onChangeBackgrounColor = (data, NWP_ID, RRT_ID, RWPP_ID, TYPE) => {
    const tdCount = $('#dataTable button').length;    // 리스트에 총 버튼 갯수

    if(tdCount == count++){
        $('#workPartsIndicator').hide();
        $('#WorkPartsTable').show();
    }
    if(TYPE == FLOOR){
        if(data[0].NM != null && data[0].NM > 0){
            $('#'+TYPE+"_"+NWP_ID+"_"+RRT_ID+"_"+RWPP_ID).attr('class','btn btn-success');
        }else{
            $('#'+TYPE+"_"+NWP_ID+"_"+RRT_ID+"_"+RWPP_ID).attr('class','btn btn-default');
        }
    }else{
        if(data.length > 0){
            $('#'+TYPE+"_"+NWP_ID+"_"+RRT_ID+"_"+RWPP_ID).attr('class','btn btn-success');
        }else{
            $('#'+TYPE+"_"+NWP_ID+"_"+RRT_ID+"_"+RWPP_ID).attr('class','btn btn-default');
        }
    }
    
}
