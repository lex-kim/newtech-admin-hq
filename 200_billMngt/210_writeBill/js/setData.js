/**
 * 각 데이터를 가져오고 세팅하는 js
 */
const CA_CPP_ID = 'CART123457';   // 카트리지실런트 아이디
const REF_BOTH_TYPE = 11430;      // 쌍방 ref
const NSRN_ID = "nthqcb";
const NSRN_PWD = "eios03l2109akjfo6928734eo5ewldha";

setData_BILL = () => {
    getGarageInfo();            // 공업사정보 가져오기
    getCarInfo();               // 차량정보
    getRefClaimData();
    getRefClaimentData();
    setInsuSelect();            // 보험사
    if(MAXIMUM_CLAIM_PRICE == 0){
        getMaxmiumClaimPrice();     //청구금액한도
    }
}


/**
 * 해당 청구서 정보 가져오기(수정모드)
 */
getBillInfo = () => {
    const data = {
        REQ_MODE: CASE_TOTAL_LIST,
        NB_ID : $('#INPUT_NB_ID').val(),
        MGG_ID : $('#INPUT_MGG_ID').val()
    };
    callAjax(ajaxUrl, 'POST', data).then((result)=>{
        if(isJson(result)){
            const data = JSON.parse(result);
            setUpdateData(data.data);
        }else{
            alert(ERROR_MSG_1200);
        }
    });
}

getMaxmiumClaimPrice = () => {  // 청구금액 한도 가져오기
    const data = {
        REQ_MODE: CASE_DELETE_FILE,
    };
    callAjax(ajaxUrl, 'GET', data).then((result)=>{
        if(isJson(result)){
            const data = JSON.parse(result);

            if(data.status == OK){
                MAXIMUM_CLAIM_PRICE = data.data[0].PCE_CURRENT_LIMIT_PRICE;
            }else{
                alert(ERROR_MSG_1100);
            }
        }else{
            alert(ERROR_MSG_1200);
        }

    });
}

/** 공업사정보 */
getGarageInfo = () => {
    const data = {
        REQ_MODE: GET_GARGE_INFO,
    };
    callAjax(ajaxUrl, 'GET', data).then((result)=>{
        if(isJson(result)){
            const data = JSON.parse(result);
            if(data.status == OK){
                autoCompleteGarageInfo(data.data);
            }else{
                alert(ERROR_MSG_1100);
            }
        }else{
            alert(ERROR_MSG_1200);
        }

    });
}


getCarInfo = () => {
    const data = {
        REQ_MODE: GET_CAR_TYPE,
    };
    callAjax(ajaxUrl, 'GET', data).then((result)=>{
        if(isJson(result)){
            const data = JSON.parse(result);
            if(data.status == OK){
                autoCompleteCarInfo(data.data);
            }else{
                alert(ERROR_MSG_1100);
            }
        }else{
            alert(ERROR_MSG_1200);
        }

    });
}

/**
 * 일방/쌍방/일부
 */
getRefClaimData = () => {
    getRefTypeData('CLAIM', 'RLT').then((result)=>{
       const dataArray = result.data;
       dataArray.map((data)=>{
           $('#CLAIM_TYPE_'+data.ID).val(data.ID);

       });
    })
}

/**
 * 청구주체
 */
getRefClaimentData = () => {
    getRefTypeData('CLAIMANT', 'RMT').then((result)=>{
        const dataArray = result.data;
        dataArray.map((data)=>{
            $('#CLAIMENT_TYPE_'+data.ID).val(data.ID);
        });
     });
}

/**
 * 해당 공업사에 사용부품 정보 가져오기
 * @param {MGG_ID : 해당 공업사 아이디}
 */
setGaragePartsData = (MGG_ID, PARTS_ARRAY) => {
    const data = {
        REQ_MODE: GET_GARGE_PARTS_INFO,
        MGG_ID : MGG_ID
    };
    callAjax(ajaxUrl, 'GET', data).then((result)=>{
        if(isJson(result)){
            const data = JSON.parse(result);
            if(data.status == OK){
                setGaragePartsTable(data.data[0], PARTS_ARRAY);          // 4.1 사용부품
                setMonthBillStockTable(data.data[1]);                    // 당월청구 및 재고금액현황
                setBeforeMonthBillStockTable(data.data[2]);              // 전년도 청구 대비 재고현황
            }else{
                alert(ERROR_MSG_1100);
            }
        }else{
            alert(ERROR_MSG_1200);
        }

    });
}

/**
 * 기준부품가져오기
 */
// getStdPartsList = () => {
//     const data = {
//         REQ_MODE: GET_PARTS_INFO
//     };
//     callAjax(ajaxUrl, 'POST', data).then((result)=>{
//         if(isJson(result)){
//             const data = JSON.parse(result);
//             AtypePartsArray = getTypePartsArray(data.data, 'A');
//             BtypePartsArray = getTypePartsArray(data.data, 'B');

//             setWorkPartsHeaderTable(data.data);     // 5.작업항목 헤더
//         }else{
//             alert(ERROR_MSG_1200);
//         }
//     });
// }

/**
 * 작업항목 리스트가져오기
 */
getWorkPartList = () => {
    const data = {
        REQ_MODE: CASE_LIST,
        NB_ID : $('#INPUT_NB_ID').val()
    };
    callAjax(ajaxUrl, 'POST', data).then((result)=>{
        if(isJson(result)){
            const data = JSON.parse(result);
            // getStdPartsList();  // 기준부품
            AtypePartsArray = getTypePartsArray(data.data[1], 'A');
            BtypePartsArray = getTypePartsArray(data.data[1], 'B');
            setWorkPartsHeaderTable(data.data[1]);     // 5.작업항목 헤더
            setWorkPartsTable(data.data[0]);

        }else{
            alert(ERROR_MSG_1200);
        }
    });
}

/**
 * 보험사 정보를 서버에서 가져온다.
 */
setInsuSelect = () => {
    $('.MII_ID_Table select').each(function(){
        getInsuInfo($(this).attr('id'), false);         // 보험사 정보
    });

}

/**
 * 각 테이블에서 설정된 값을 가져오기
 * @param {NWP_ID : 고유 작업항목 아이디}
 * @param {RRT_ID : 교환/판금}
 * @param {RWPP_ID : 단일/좌,우/실내,하체}
 */
getWorkPartsInfo = (NWP_ID, RRT_ID, RWPP_ID, IS_ASYNC) => {
    const data = {
        REQ_MODE: CASE_READ,
        NWP_ID : NWP_ID,
        RRT_ID : RRT_ID,
        RWPP_ID : RWPP_ID

    };
    if(IS_ASYNC == undefined){
        return callAjax(ajaxUrl, 'POST', data).then((result)=>{
            if(isJson(result)){
                const dataArray = JSON.parse(result);
                return dataArray;
            }else{
                alert(ERROR_MSG_1200);
            }
        });
    }else{
        return callAsyncAjax(ajaxUrl, 'POST', data).then((result)=>{
            if(isJson(result)){
                const dataArray = JSON.parse(result);
                return dataArray;
            }else{
                alert(ERROR_MSG_1200);
            }
        });
    }

}



/**  청구금액 */
setBillTable = () => {
    let billPartsValue = 0;                  // 부품금액
    $('.NBP_TOTAL_PRICE').each(function(){
        const value = Number($(this).val().replace(/,/g,''));
        billPartsValue += value;
    });
    billPartsValue = cuttingMoney(billPartsValue);
    const billDutyValue = (billPartsValue/10);
    const totlaValue = cuttingMoney(billPartsValue+billDutyValue);
    const billTotalValue = totlaValue > MAXIMUM_CLAIM_PRICE ? MAXIMUM_CLAIM_PRICE : totlaValue;

    $('#billReckonTable').html(numberWithCommas(billPartsValue));
    $('#billDutyTable').html(numberWithCommas(billDutyValue));
    $('#billTotalTable').html(numberWithCommas(billTotalValue));

    const NB_NEW_INSU_YN = $('input[name="NB_NEW_INSU_YN"]:checked').val();   // 신보적용여부
    if(NB_NEW_INSU_YN == YN_Y){
        const CLAIM_TYPE = $('input[name="RLT_ID"]:checked').val();  // 쌍방일 경우 차주 부담금액 자차과실비율 세팅
        if(CLAIM_TYPE == REF_BOTH_TYPE){
            const billOwnerPrice = cuttingMoney(billTotalValue*0.2);
            $('#ownerAccountTable').html(numberWithCommas(billOwnerPrice));
            if($('#OWNER_INSU_PERCENT').val()!=''){
                const perValue = Number($('#OWNER_INSU_PERCENT').val());
                const price = (billTotalValue*0.2)*(perValue/100);
                const ownerTotal = price - price%10;
                $('#ownerTotalTable').html(numberWithCommas(ownerTotal));
            }
        }else{
            $('#ownerAccountTable').html(0);
            $('#ownerTotslTable').html(0);
        }
    }else{
        $('#ownerAccountTable').html(0);
        $('#ownerTotalTable').html(0);
    }

}

/**
 * 방청시간 테이블 값
 * @param {reckonTime : 방청시간(단위:분)}
 *
 */
setReckonTimeTable = (reckonTime) => {
    let reckonText = '';
    if(Math.floor(reckonTime/60) >= 1){
        reckonText = Math.floor(reckonTime/60)+"시간 "+reckonTime%60+"분";
    }else{
        reckonText = reckonTime+"분";
    }
    $('#reckonTimeTable').html(reckonText);
}

/**
 * 4.1사용부품항목 수량/단가/금액
 * @param {CA_CPP_ID_ARRAY : 카트리지실런트 데이터}
 * @param {ANOTHER_CPP_ID_ARRAY : 카트리지실런트를 제외한 데이터}
 * @param {IS_SUBIT : 저장(계산)일때 인지 아닌지. undefined일경우는 단순 조견표 계산}
 */
setUsePartsTable = (CA_CPP_ID_ARRAY, ANOTHER_CPP_ID_ARRAY, IS_SUBIT) => {
    let reckonTime = 0;    //방청시간

    garageUsePartsList.map((item)=>{
        const CPP_ID = item.CPP_ID;
        $('#NBP_USE_CNT_'+CPP_ID).val(0);
        $('#NBP_TOTAL_PRICE_'+CPP_ID).val(0);
    });

    /** 카트리즈 실런트를 제외한 다른 기준부품들 */
    if(ANOTHER_CPP_ID_ARRAY.length > 0){
        ANOTHER_CPP_ID_ARRAY.map((item)=>{
            const CPP_ID = item.CPP_ID;
            const NRPS_RATIO = item.NRPS_RATIO;
            const NBP_USE_CNT = Number($('#NBP_USE_CNT_'+CPP_ID).val());
            const CNT = +(NBP_USE_CNT + Number(NRPS_RATIO)).toFixed(2);;
    
            const TOTAL_CNT = cuttingMoney($('#NBP_UNIT_PRICE_'+CPP_ID).val().replace(/,/g,"")*CNT);
    
            $('#NBP_USE_CNT_'+CPP_ID).val(CNT);
            $('#NBP_TOTAL_PRICE_'+CPP_ID).val(numberWithCommas(TOTAL_CNT));
    
            reckonTime += Number(item.NRT_TIME_M);  //방청시간
        });
    }
   

    /** 카트리지 실린더 같은경우에는 0.5 초과 1 미만일 경우에는 1로 표기해주기 위해 */
    if(CA_CPP_ID_ARRAY.length > 0){
        let CA_CPP_SUM = 0.0;                                   // 카트리지실런트 수량 더할 값(조건에 맞는지 비교하기 위해)
        let CA_USE_CNT = 0.0;                                   // 조건에 맞게 설정된 카트리지 실런트 수량값(최종)
        CA_CPP_ID_ARRAY.map((item)=>{
            reckonTime += Number(item.NRT_TIME_M);              // 방청시간
            CA_CPP_SUM = +(CA_CPP_SUM + Number(item.NRPS_RATIO));
        });

        if(CA_CPP_SUM < 1 && CA_CPP_SUM > 0.5){   // 뉴텍에서 설장한 기준
            CA_USE_CNT = 1;
        }else{
            CA_USE_CNT = (CA_CPP_SUM == 0 )? 0 : CA_CPP_SUM.toFixed(2);
        }

        const TOTAL_CA_CNT = cuttingMoney($('#NBP_UNIT_PRICE_'+CA_CPP_ID).val().replace(/,/g,"")*CA_USE_CNT);
        $('#NBP_USE_CNT_'+CA_CPP_ID).val(CA_USE_CNT);
        $('#NBP_TOTAL_PRICE_'+CA_CPP_ID).val(numberWithCommas(TOTAL_CA_CNT));
    }
    
    setBillTable();                        // 청구금액 표
    getManagePriceType(IS_SUBIT);            // 총 청구금액에 맞는 보상담당자 세팅


    if(ANOTHER_CPP_ID_ARRAY.length > 0 || CA_CPP_ID_ARRAY.length > 0){
        setReckonTimeTable(reckonTime);  // 방청시간
    }

    // /** undefinded가 아니고 true일 경우에는 저장(계산) 일 경우. 계산 다 된뒤에 addData전송 */
    // if(IS_SUBIT != undefined && IS_SUBIT){
    //     addData();
    // }
}

/**
 * 4.1 사용부품항목에 리턴받은 조견표 값을 더해준다.
 * @param {dataArray : 해당 기준부품과 조견표값}
 * @param {IS_SUBIT : 저장(계산)일때 인지 아닌지. undefined일경우는 단순 조견표 계산}
 */
setChartData = (dataArray, IS_SUBIT) => {
    partsChartArray = dataArray;
    const CA_CPP_ID_ARRAY = dataArray.filter((item)=>{
        return item.CPP_ID == CA_CPP_ID;
    });

    const ANOTHER_CPP_ID_ARRAY =  dataArray.filter((item)=>{
        return item.CPP_ID != CA_CPP_ID;
    });

    setUsePartsTable(CA_CPP_ID_ARRAY, ANOTHER_CPP_ID_ARRAY, IS_SUBIT);
}


/**
 * 조견표 정보 가져오기
 * @param {chartArray : 5.작업항목 선택에서 클릭된 데이터들}
 * @param {IS_SUBIT : 저장(계산)일때 인지 아닌지. undefined일경우는 단순 조견표 계산}
*/
getChartData = (chartArray, IS_SUBIT) => {
    const data = {
        REQ_MODE: CASE_UPDATE,
        RCT_CD : $('#RCT_CD').val(), // 차량정보 아이디
        DATA : chartArray
    };

    callAjax(ajaxUrl, 'POST', data).then((result)=>{
        if(isJson(result)){
            const data = JSON.parse(result);
            if(data.status == OK){
                setChartData(data.data, IS_SUBIT);
            }else{
                alert(ERROR_MSG_1100);
            }
        }else{
            alert(ERROR_MSG_1200);
        }
    });
}

/** 조견표에서 체크된 부품들을 리턴해주는 함수 */
getCheckArray = async() => {
    let chartArray = [];
    await $('.RRT_TD').each(async function(){
        if($(this).is(':checked')){
            const data = JSON.parse($(this).val());
            const RRT_ID = data.RRT_ID;
            const NWP_ID = data.NWP_ID;
            const RWPP_ID = data.RWPP_ID;

            // const checkArray =  await getWorkPartsInfo(NWP_ID, RRT_ID, RWPP_ID, true);
            await $('.PARTS_CHECK_'+NWP_ID).each(async function(){
                if($(this).is(':checked')){
                    const CPP_ID = $(this).val();
                    const CPPS_ID = JSON.parse($('#CPPS_ID_'+CPP_ID).val()).CPPS_ID;
                    var jsonData = {                   // 체크된 기준부품과 해당기준부품에 사용부품아이디
                        CPP_ID : CPP_ID,
                        CPPS_ID : CPPS_ID,
                        RWPP_ID : RWPP_ID,
                        RRT_ID : data.RRT_ID,
                        NWP_ID : data.NWP_ID
                    };
                    chartArray.push(jsonData);
                    // let isExistFROM_CPP_ID = false;       // 대체 기준부품
                    // let isExistPRIORITY_CPP_ID  = false;  //우선순위 기준부품

                    // checkArray[0].map((item)=>{   // 우선순위
                    //     if( item.CPP_ID == CPP_ID && item.RRT_ID == RRT_ID && item.NWP_ID == NWP_ID){
                    //             isExistPRIORITY_CPP_ID = true;
                    //             chartArray.push(Object.assign(item, jsonData));
                    //     }
                    // });

                    // if(!isExistPRIORITY_CPP_ID){     // 해당기준부품이 우선순위에 데이터가 없을 경우에마 대체.체화 확인
                    //     checkArray[1].map((item)=>{
                    //         if(item.FROM_CPP_ID == CPP_ID && item.RRT_ID == RRT_ID && item.NWP_ID == NWP_ID){
                    //                 const isExistToCppId = checkArray[1].find((reData)=>{ return reData.TO_CPP_ID == item.FROM_CPP_ID});
                    //                 if(!isExistToCppId){
                    //                     isExistFROM_CPP_ID = true;
                    //                     chartArray.push(Object.assign(item, jsonData));
                    //                 }
                    //         }
                    //         });

                    //         if(!isExistFROM_CPP_ID){
                    //             checkArray[1].map((item)=>{
                    //                 if(item.TO_CPP_ID == CPP_ID && item.RRT_ID == RRT_ID && item.NWP_ID == NWP_ID){
                    //                     chartArray.push(Object.assign(item, jsonData));
                    //             }
                    //             });
                    //         }
                    // }
                }
            });

        }
    });
    return chartArray;
}

/**
 * 5. 작업항목 선택
 * 조견표 계산
 * @param {IS_SUBIT : 저장(계산)일때 인지 아닌지. undefined일경우는 단순 조견표 계산}
 */
accountReckonParts = async(IS_SUBIT) => {
    if($('#RCT_CD').val() == ''){
        alert("차량을 선택해주세요.");
        $('#CCC_NM').focus();
    }else if($('#SELECT_NB_GARAGE_ID').val().trim() == ''){
        alert('공업사를 입력/선택 입력해주세요.');
        $('#NB_GARAGE_NM').focus();
    }else{
        await getCheckArray().then((result)=>{
            if(result.length == 0){
                $('#reckonTimeTable').html('');
            }
            getChartData(result, IS_SUBIT);
        })
    }

}

/**
 * 2.보험사 선택
 * 보상담장지정 모달에서 담당지정등록 버튼을 눌렀을 때
 */
function setInsuManageData(){
    const SELECT_RLT_ID = $('input[name=INSU_MANAGER]:checked').val();
    const NIC_ID = $('#MII_ID_LIST_'+SELECT_RLT_ID+' .SELECT_CHG_NM').val();
    const data = {
        REQ_MODE: CASE_UPDATE,
        NIC_ID : NIC_ID,
        MGG_ID : $('#SELECT_NB_GARAGE_ID').val(),
        RICT_ID : $('#RLT_ID_SELECT').val()
    };

    callAjax(ajaxUrl, 'POST', data).then((result)=>{
        if(isJson(result)){
            const data = JSON.parse(result);
            if(data.status == OK){
                var type = (SELECT_RLT_ID == RLT_MY_CAR_TYPE) ? '' : '_';    // 자차보험사 인지 대물보험사 인지 구분

                getInsuManageInfo(type, $('#RLT_ID_SELECT').val());     // 해당보험사 담당자 정보
                $('#RICT_NM'+type).html($('#RLT_ID_SELECT option:selected').text(), false);
                $('#cpst-select').modal('hide');
            }else if(data.status == CONFLICT){
                alert(data.message);
            }else{
                alert(ERROR_MSG_1100);
            }
        }else{
            alert(ERROR_MSG_1200);
        }
    });
}

/**
 * 3. 수리차량정보
 * 중복검사 버튼클릭
 */
function getOverlapCarInfo(MII_ID_ARR, REGI_NUM_ARR, IS_CALLBACK){
    const data = {
        REQ_MODE: CASE_LIST,
        MII_ID : MII_ID_ARR,
        NBI_REGI_NUM : REGI_NUM_ARR,
        SELECT_NB_GARAGE_ID : $('#SELECT_NB_GARAGE_ID').val(),
        NB_CAR_NUM : $('#NB_CAR_NUM').val(),
        ORI_NB_ID : $('#INPUT_NB_ID').val()
        // NB_CAR_NUM : $('#NB_CAR_NUM').val().substring($('#NB_CAR_NUM').val().length , $('#NB_CAR_NUM').val().length-4),
    };

    return callAjax(ajaxUrl, 'POST', data).then((result)=>{
        if(isJson(result)){
            const data = JSON.parse(result);
            if(data.status == OK){
                if(IS_CALLBACK == undefined){
                    setInsuOverlapTable(data.data);
                }else{
                    return data.data
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
 * 해당차량타입과 맞는 보상담당자가 있는지.
 * @param {*} manageArray
 */
function getInsuManageType(manageArray){
    const data = {
        REQ_MODE: CASE_DELETE,
        MANAGE_ARRAY : manageArray,
        MGG_ID : $('#SELECT_NB_GARAGE_ID').val()
    };

    return callAjax(ajaxUrl, 'POST', data).then((result)=>{
        if(isJson(result)){
            const data = JSON.parse(result);
            if(data.status == OK){
                return data.data
            }else{
                alert(ERROR_MSG_1100);
            }
        }else{
            alert(ERROR_MSG_1200);
        }
    });
}

/**
 * 소액/고액을 결정하기 위해 금액 가져오기
 */
function getManagePriceType(IS_SUBMIT){
    const data = {
        REQ_MODE: CASE_DELETE
    };
    return callAsyncAjax(ajaxUrl, 'POST', data).then((result)=>{
        if(isJson(result)){
            const data = JSON.parse(result)
            if(data.status == OK){
                setInsuManagerTable(data.data, IS_SUBMIT);
                // return data;
            }else{
                alert(ERROR_MSG_1100);
            }
        }else{
            alert(ERROR_MSG_1200);
        }
    });
}

/**
 * 저장할 때 uuid값 가져오기
 */
function getBillUUID(){
    const data = {
        REQ_MODE: GET_AUTHORITY
    };
    return callAsyncAjax(ajaxUrl, 'POST', data).then((result)=>{
        if(isJson(result)){
            const data = JSON.parse(result);
            if(data.status == OK){
                return data.data;
            }else{
                alert(ERROR_MSG_1100);
            }
        }else{
            alert(ERROR_MSG_1200);
        }
    });
}

/**
 * 저장된 청구서 건이 중복인지 아닌지..
 */
function isDuplicateBill(NB_ID){
    const data = {
        REQ_MODE: CASE_LIST_SUB,
        NB_ID : NB_ID
    };

    return callAsyncAjax(ajaxUrl, 'POST', data).then((result)=>{
        if(isJson(result)){
            const data = JSON.parse(result);
            if(data.status == OK){
                return data.data;
            }else{
                alert(ERROR_MSG_1100);
            }
        }else{
            alert(ERROR_MSG_1200);
        }
    });
}

/**
 * 팩스전송상태 업데이트
 * @param {} dataArray
 */
function setFaxStatus(dataArray){
    const data = {
        REQ_MODE: CASE_LIST_DETAIL,
        DATA : dataArray
    };
    return callAsyncAjax(ajaxUrl, 'POST', data).then((result)=>{
        if(isJson(result)){
            const data = JSON.parse(result);
            if(data.status == OK){
                return data.data;
            }else{
                alert(ERROR_MSG_1100);
            }
        }else{
            alert(ERROR_MSG_1200);
        }
    });
}

function sendNSRSN(){
    const EST_TEXT = $('#EST_SEQ2').val() == '' ? $('#EST_SEQ1').val()  : $('#EST_SEQ1').val() +","+$('#EST_SEQ2').val() ;
    const nsrsnURL = "https://autobill.nt34.net/dgm/nsrsn_set";
    // const nsrsnURL = "http://125.141.139.36/dgm/nsrsn_set";
    const params = {
        id : NSRN_ID,
        pwd : NSRN_PWD,
        login_id : $('#USER_ID').val(),
        est_seq : EST_TEXT,
        nsrsn : $('#NB_AB_NSRSN_VAL').val()

    }
    console.log(nsrsnURL,"==",params);
    callAsyncAjax(nsrsnURL, 'POST', params).then((result_)=>{
        console.log(nsrsnURL," Result==>",params);
        const data_ = JSON.parse(result_);
        if(data_.status == OK){
            const data = {
                REQ_MODE: CASE_UPDATE,
                NB_ID : $('#INPUT_NB_ID').val(),
                NB_AB_NSRSN_VAL : $('#NB_AB_NSRSN_VAL').val()
            };
            callAsyncAjax(ajaxUrl, 'POST', data).then((result)=>{
                if(isJson(result)){
                    const data = JSON.parse(result);
                    if(data.status == OK){
                        confirmSwalOneButton('미작성사유 전송에 성공하였습니다.','확인', ()=>{resultSetData();});
                    }else{
                        alert(ERROR_MSG_1100);
                    }
                }else{
                    alert(ERROR_MSG_1200);
                }
            });
        }else{
            alert(ERROR_MSG_1100);
        }
    });

}
