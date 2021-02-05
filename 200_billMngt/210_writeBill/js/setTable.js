/**
 * 테이블 세팅해주는 js
 */
const MYCAR_ONE_SIDE =  11410;  //일방(자차)

const RWP_ID_1 = '30111';                   //단일
const RWP_ID_BOTH = '30112';                //좌우
const RWP_ID_DOWN = '30113';                //바닥
const GREEN_CLASS = 'greentd_';    
const BLUE_CLASS = 'bluetd_';
const DEEP_GREEN_CLASS = '#a9d15e';    // 사용부품테이블에 세팅된 값이 있을 경우 진한 값을 넣어주기위해
const DEEP_BLUE_CLASS = '#82C4EC';     // 사용부품테이블에 세팅된 값이 있을 경우 진한 값을 넣어주기위해
let AtypePartsArray = [];
let BtypePartsArray = [];

setTable_BILL = async() => { 
    const date = new Date();
    const year = date.getMonth() == 0 ? date.getFullYear()-1 : date.getFullYear();
    const month = date.getMonth() == 0 ? 12 : date.getMonth();
    let beforeMonthBillTable = year+"년 "+month+"월 청구 대비 재고 현황";
    beforeMonthBillTable += " <p>[재고비율(=재고/청구)이 1.5를 초과한 경우에만 표시]</p>";
    $('#beforeMonthBillTable').html(beforeMonthBillTable);

    selectInsuTable(CL_TYPE_ONE, MYCAR_ONE_SIDE);   //2. 보험사 선택 테이블
    getWorkPartList();  // 헤더 테이블 세팅후 작업항목 리스트 동적으로 세팅
    //  getStdPartsList();  // 기준부품
  
}

/**
 * 2. 보험사 선택
 * 보상담당지정 모달에서 자차인지 대물인지 선택해줄 수 있게..
 */
setInsuManagerModal = (CLAIM_VALUE) => {
    const tdText = (CLAIM_VALUE != MYCAR_ONE_SIDE && CLAIM_VALUE != undefined)? '대물' : '자차';
    const value = (CLAIM_VALUE != MYCAR_ONE_SIDE && CLAIM_VALUE != undefined)? RLT_PI_TYPE : RLT_MY_CAR_TYPE;
    let insuManagerHtml = '';
    insuManagerHtml += '<label class="radio-inline" style="padding-right: 15px;">';
    insuManagerHtml += '<input type="radio" name="INSU_MANAGER" id="INSU_MANAGER_TARGET_'+value+'" value="'+value+'"  checked>'+tdText+'</label>';

    return insuManagerHtml;
}

/**
 * 선택한 청구구분에 따라 보여지는 보험사 선택 부분이 달라진다.
 * @param {CLASS_TYPE : 선택한 청구구분이 일방인지 쌍방인지 일부인지}
 * @param {CLAIM_VALUE : 해당 청구구분에서도 자차/대물을 비교해주기 위해}
 */
selectInsuTable = (CLASS_TYPE, CLAIM_VALUE) => {
    $('#insuManagerTable').css('width','500px');
    // $('#insuManagerTable').css('margin-left','20px');
    $('#insuManagerCheckTable').html(setInsuManagerModal(CLAIM_VALUE));  // 보상담당등록 모달에서 자차/대물
    if(CLASS_TYPE == CL_TYPE_ONE){    // 일방
        if(CLAIM_VALUE != MYCAR_ONE_SIDE && CLAIM_VALUE != undefined){  //자차
            $('#MII_ID_LIST_11420').show();
            $('#MII_ID_LIST_11410').hide();
            $('#NIC_INFO_11420').show();
            $('#NIC_INFO_11410').hide();
        }else{  //대물
            $('#MII_ID_LIST_11420').hide();
            $('#MII_ID_LIST_11410').show();
            $('#NIC_INFO_11420').hide();
            $('#NIC_INFO_11410').show();
        }
        $('.ratioTable select').hide();
        $('#MII_ID_LIST_11440').hide();
        $('#ownerPercentTable').html('');         
    }else if(CLASS_TYPE == CL_TYPE_BOTH){   //쌍방
        $('#MII_ID_LIST_11410').show();
        $('#MII_ID_LIST_11420').show();
        $('#MII_ID_LIST_11440').hide();
        $('#NIC_INFO_11420').show();
        $('#NIC_INFO_11410').show();
        $('#insuManagerCheckTable').html(setInsuManagerModal()+setInsuManagerModal(CLAIM_VALUE));
        $('#insuManagerTable').css('width','68%');
        // $('#insuManagerTable').css('margin-left','10px');
        $('.ratioTable select').show();
        $('.ratioTable select').attr('disabled',false);     // 과실율 선택 가능하게
        $('.ratioTable select').html(setRatioOption(""));
        $('#ownerPercentTable').html('<select class="form-control" id="OWNER_INSU_PERCENT">'+setRatioOption()+'</select>');
        $('.ratioTable select').val(50);
        $('#OWNER_INSU_PERCENT').change(function(){
            $('#NBI_RATIO').val($(this).val());
            $('.ratioTable select').trigger('change');
        });
    }else{   // 일부
        $('#MII_ID_LIST_11410').hide();
        $('#MII_ID_LIST_11420').show();
        $('#MII_ID_LIST_11440').show();
        $('#NIC_INFO_11420').show();
        $('#NIC_INFO_11410').hide();
        $('.ratioTable select').show();
        $('.ratioTable select').attr('disabled',false);     // 과실율 선택 가능하게
        $('.ratioTable select').html(setRatioOption("_"));
        $('#ownerPercentTable').html('');
    }
   
    addInsuTableEvent();   // 보험사 select 이벤트 추가
}


//////////////////// 4-1 사용부품항목 테이블 시작   /////////////////

/**
 * 해당 사용부품 수량 및 단가 수정 이벤트
 * @param {CPP_ID : 변경한 기준부품 아이디}
 */
onChangeUseCntOrPrice=  (CPP_ID) => {
    const cntValue = $('#NBP_USE_CNT_'+CPP_ID).val();
    const unitPricecValue = $('#NBP_UNIT_PRICE_'+CPP_ID).val().replace(/,/g, '');
    const totalPrice = cuttingMoney(Number(cntValue)*Number(unitPricecValue));
    if(cntValue != '0' && unitPricecValue != ''){
        $('#NBP_TOTAL_PRICE_'+CPP_ID).val(numberWithCommas(totalPrice));
    }else{
        $('#NBP_TOTAL_PRICE_'+CPP_ID).val(0);
    }
    setBillTable();
}

/**
 * 사용부품 헤더
 * @param {typeArray : 사용부품 데이터 배열}
 */
getUsePartsShortNM = (typeArray) => {
    let partsShortNM = '';
    let beforePartID = '';

    typeArray.map((type, index)=>{
        if(beforePartID != type.CPP_ID){
            if(index > 0){
                partsShortNM +=", ";
            }
            partsShortNM += type.CPP_NM_SHORT;
        }
        beforePartID = type.CPP_ID;
    });
    return partsShortNM;
}

/**
 * 기준부품명에 따른 사용부품(select)
 * @param {dataArray : 사용부품 데이터 배열}
 * @param {CPP_ID : 기준부품 아이디}
 */
setUsePartsSubTable = (dataArray, CPP_ID, useCNT, totalPrice, cppsID) => {
    let partsSubHtml = '<select class="form-control" onchange="onchangePartsSubSelect(\''+CPP_ID+'\')" id="CPPS_ID_'+CPP_ID+'">';
    partsSubHtml += '<option name="'+CPP_ID+'" value="">사용안함</option>';
    let cppsPrice = 0;

    dataArray.map((data)=>{
        if(data.NGP_DEFAULT_YN  != undefined){
            if(data.CPP_ID == CPP_ID){
                let selectedHtml = "";
                if(cppsID != undefined){
                    if(cppsID == data.CPPS_ID){
                        cppsPrice = data.CPPS_PRICE;
                        selectedHtml = "selected";
                    }    
                }else{
                    if(data.NGP_DEFAULT_YN == YN_Y){
                        cppsPrice = data.CPPS_PRICE;
                        selectedHtml = "selected";
                    }
                }   
                partsSubHtml += '<option name="'+data.CPPS_PRICE+'" value='+JSON.stringify({CPPS_ID : data.CPPS_ID, CPP_ID : data.CPP_ID})+' '+selectedHtml+'>'+data.CPPS_NM+'</option>';
            }
        }
       
    });

    partsSubHtml += '</select>';
    partsSubHtml += '<td><input type="text" class="form-control text-right NBP_USE_CNT"   onchange="onChangeUseCntOrPrice(\''+CPP_ID+'\')"   id="NBP_USE_CNT_'+CPP_ID+'" value='+useCNT+'>';
    partsSubHtml += '<td><input type="text" class="form-control text-right"   onchange="onChangeUseCntOrPrice(\''+CPP_ID+'\')" id="NBP_UNIT_PRICE_'+CPP_ID+'" readonly value='+numberWithCommas(cppsPrice)+'>';
    partsSubHtml += '<td><input type="text" style="width:80px" class="form-control text-right NBP_TOTAL_PRICE" name='+JSON.stringify({TYPE : "PARTS" , CPP_ID :CPP_ID })+' id="NBP_TOTAL_PRICE_'+CPP_ID+'" readonly value="'+totalPrice+'">';

    return partsSubHtml;
}

getPartsBodyTable = (dataArray, type ,PARTS_ARRAY) => {
    let bodyTableHTml = '';
    let beforePartID = '';
    let useCNT = 0;
    let partsArray = [];
    let totalPrice = 0;
    let CPPS_ID = undefined;

    dataArray.map((data)=>{
        if(beforePartID != data.CPP_ID){
            if(PARTS_ARRAY != undefined){
                partsArray = PARTS_ARRAY.find((item)=>{
                    return (type == item.CPP_PART_TYPE) && (item.CPP_ID == data.CPP_ID);
                });
                if(partsArray != undefined){
                    useCNT = partsArray.NBP_USE_CNT;
                    totalPrice = partsArray.NBP_TOTAL_PRICE;
                    CPPS_ID = partsArray.CPPS_ID;
                }else{
                    useCNT = 0;
                    totalPrice = 0;
                    CPPS_ID = undefined;
                }
            }
        
            bodyTableHTml += '<tr><td>'+data.CPP_NM+'<br>(<span style="font-size:11px">'+data.CPP_UNIT+'</span>)</td>';
            bodyTableHTml += '<td class="USE_PARTS_TD">';
            bodyTableHTml += setUsePartsSubTable(dataArray, data.CPP_ID, useCNT, totalPrice, CPPS_ID);
            bodyTableHTml += '</td>';
            bodyTableHTml += '</tr>';
        }
        beforePartID = data.CPP_ID;
    });
    bodyTableHTml += '</table>';
    return bodyTableHTml;
}

/**
 * 4-1 사용부품항목 테이블 헤더 부분
 * @param {typeArray : 해당 타입 데이터}
 * @param {typeNm : A타입인지 B}
 */
getPartsHeaderTable = (typeArray, typeNm) =>{
    let partHtml = '<table class="table table-bordered">';
    partHtml += '<tbody><tr>';
    partHtml += '<td class="text-center active" colspan="5"><span class="red">';
    partHtml += '사용부품 '+typeNm+' (약칭 : '+getUsePartsShortNM(typeArray)+')';
    partHtml += '</span></td></tr><tr>';
    partHtml += '<td class="text-center active" style="width:23%">기준부품명<br><span style="font-size:11px">(용량/길이)</span></td>' 
    partHtml += '<td class="text-center active" style="width:40%">사용부품</td>';
    partHtml += '<td class="text-center active" style="width:10%">수량</td>';
    partHtml += '<td class="text-center active" style="width:15%">단가</td>';
    partHtml += '<td class="text-center active" style="width:15%">금액</td></tr>';

    return partHtml;
}


/** 4-2. 기타부품 헤더테이블 */
getEtcPartsHeaderTable = () => {
    let partsHtml = '<table class="table table-bordered">';
    partsHtml += '<tbody><tr>';
    partsHtml += '<td class="text-center active" style="width:250px">기타부품 선택</td>';
    partsHtml += '<td class="text-center active">수량</td>';
    partsHtml += '<td class="text-center active">단가</td>';
    partsHtml += '<td class="text-center active">금액</td>' ;
    partsHtml += '<td class="text-center active" style="width:250px">기타부품 선택</td>';
    partsHtml += '<td class="text-center active">수량</td>';
    partsHtml += '<td class="text-center active">단가</td>';
    partsHtml += '<td class="text-center active">금액</td>' ;
    partsHtml += '</tr></tbody>';

    return partsHtml;
}

/** 4-2. 기타부품 바디테이블 */
getEtcPartsBodyTable = (dataArray, PARTS_ARRAY) => {
    let partsHtml = '';
    let etcCount = 0;
    let useCNT = 0;
    let selectedValue = "";
    let partsArray = [];

    if(PARTS_ARRAY != undefined){
        partsArray = PARTS_ARRAY.filter((item)=>{
            return item.CPP_PART_TYPE == '기타';
        });
    }

    if(dataArray.length > 0){
        for(let i = 0 ; i<2; ++i){    // 기타부품선택 두줄
            partsHtml += '<tr>';
            for(let j = 0 ; j<2; ++j){   // 기준부품선택
                if(PARTS_ARRAY != undefined){
                    if(etcCount < partsArray.length){
                        useCNT = partsArray[etcCount].NBP_USE_CNT;
                        selectedValue = partsArray[etcCount].CPPS_ID;
                    }else{
                        useCNT = 0;
                        selectedValue = '';
                    }
                    etcCount ++;
                }
                partsHtml += '<td class="text-center"><select class="form-control ETC_PARTS_SELECT"  id="CPPS_ID_'+i+'_'+j+'" onchange="onchangeEtcPartsSelect('+i+','+j+');">';
                partsHtml += getEtcPartsOption(dataArray, selectedValue)+'</select></td>';
                partsHtml += '<td><input type="text" class="form-control text-right" onchange="onchangeEtcPartsCNT('+i+','+j+');" id="NBP_USE_CNT_'+i+'_'+j+'"   value="'+useCNT+'">';
                partsHtml += '<td><input type="text" class="form-control text-right"  id="NBP_UNIT_PRICE_'+i+'_'+j+'"   value="0" readonly>';
                partsHtml += '<td><input type="text" class="form-control text-right NBP_TOTAL_PRICE" name='+JSON.stringify({TYPE : "ETC"})+' id="NBP_TOTAL_PRICE_'+i+'_'+j+'"  value="0" readonly>';
            }  
            partsHtml += '</tr>';
        }
    }else{
        partsHtml += '<tr>';
        partsHtml += '<td class="text-center" colspan="8">해당수리공장에 등록된 기타부품이 존재하지 않습니다.</td>';
        partsHtml += '</tr>';
    }
   
    return partsHtml;

}

/**
 * 사용부품항목 전체 테이블 동적 세팅
 * @param {dataArray : 해당공업사 사용부품 데이터}
 */
setGaragePartsTable  = (dataArray, PARTS_ARRAY) => {
    const aPartsArray = dataArray.filter((item)=>{
        return item.CPP_PART_TYPE == 'A';
    });
    const bPartsArray = dataArray.filter((item)=>{
        return item.CPP_PART_TYPE == 'B';
    });
    const etcPartsArray = dataArray.filter((item)=>{
        return item.CPP_PART_TYPE == '기타';
    });

    const aTypeTableHeaderHtml = getPartsHeaderTable(aPartsArray, 'A');
    const aTypeTableBodyHtml = getPartsBodyTable(aPartsArray, 'A', PARTS_ARRAY);
    const bTypeTableHeaderHtml = getPartsHeaderTable(bPartsArray, 'B');
    const bTypeTableBodyHtml = getPartsBodyTable(bPartsArray, 'B', PARTS_ARRAY);
    const etcTypeTableHeaderHTml = getEtcPartsHeaderTable();
    const etcTypeTableBodyHTml = getEtcPartsBodyTable(etcPartsArray, PARTS_ARRAY);

    garageUsePartsList = dataArray.filter((item)=>{
        return item.NGP_DEFAULT_YN == YN_Y;
    })


    if(PARTS_ARRAY != undefined){
        PARTS_ARRAY.map(function(item){
            let isExistCPP_ID = false;
            garageUsePartsList.map(function(parts){
                if(item.CPP_ID == parts.CPP_ID){
                    isExistCPP_ID = true;
                    
                }
            });

            if(!isExistCPP_ID){
                garageUsePartsList.push(item);
            }
        });
    }

    setWorkPartsUsePartsTable();
    $('#billListTable').html(aTypeTableHeaderHtml+aTypeTableBodyHtml+bTypeTableHeaderHtml+bTypeTableBodyHtml);
    $('#etcPartsTable').html(etcTypeTableHeaderHTml+etcTypeTableBodyHTml);

    if(PARTS_ARRAY != undefined){   // PARTS_ARRAY 가 있다는 것은 수정!!!
        let notEtcPartsArray = PARTS_ARRAY.filter(function(item){
            return item.CPP_PART_TYPE != '기타';
        });
        let etcPartsArray = PARTS_ARRAY.filter(function(item){
            return item.CPP_PART_TYPE == '기타';
        });
        let etcCount = 0;
        for(let i = 0 ; i<2; ++i){    // 기타부품선택 두줄
            for(let j = 0 ; j<2; ++j){   // 기준부품선택
               if(etcCount < etcPartsArray.length){
                    onchangeEtcPartsSelect(i,j);
                    etcCount++;
               }
            }  
        }
        notEtcPartsArray.map(function(item){
            // onchangePartsSubSelect(item.CPP_ID);
            onChangeUseCntOrPrice(item.CPP_ID);
        });

        PARTS_ARRAY.map(function(item){   // 수정일 경우 원래 공업사에서 등록하지 않은 사용부품이라도 사용흔적이 있으면 사용가능한 리스트에 넣어준다.
            let isExistCPP_ID = false;
            garageUsePartsList.map(function(parts){
                if(item.CPP_ID == parts.CPP_ID){
                    isExistCPP_ID = true;       
                }
            });

            if(!isExistCPP_ID){
                garageUsePartsList.push(item);
            }
        });

        // partsChartArray.map(function(item){   // 작업선택 항목 체크
        //     $('#CHECK_'+item.NWP_ID+"_"+item.RRT_ID+"_"+item.RWPP_ID).attr('checked',true);
        //     onCheckReckonCheckbox(item.NWP_ID,item.RRT_ID,item.RWPP_ID, item.RWT_ID, item.CPP_ID);
        // });
       
    }

    $('#etcShowBtnTable').show();
    if(etcPartsArray.length > 0){ 
        $('#etcShowBtn').html('숨기기');
        $('#etcShowTable').show();
    }else{
        $('#etcShowBtn').html('보이기');
        $('#etcPartsTable').hide();
    }
   
}
//////////////////// 4-1 사용부품항목 테이블 끝   /////////////////



//////////////////// 5 작업항목선택 테이블 시작   /////////////////
/**
 * 4.1 사용부품에서 사용부품 사용안함<->사용 상태로 변경될 때
 * @param {CPP_ID : 변경할 기준부품 아이디}
 */
setWorkPartsUsePartsTable = (CPP_ID, selectValue) => {
    $('.release').text('');

     garageUsePartsList.map((item)=>{
        $('#USE_PARTS_HEADER_'+item.CPP_ID).text('가능');  
    });

    if(CPP_ID != undefined){
        let beforeNWP_ID= '';
        $('.RRT_TD').each(function(){ 
            if($(this).is(':checked')){
                const data = JSON.parse($(this).val());
                const checkdID = "PARTS_CHECK_"+data.NWP_ID+"_"+CPP_ID;

                if(beforeNWP_ID!= data.NWP_ID){
                    if(selectValue == ''){
                        $('#'+checkdID).prop('disabled', true);
                        $('#'+checkdID).prop('checked', false);

                        resetPartsTableBackground(data.NWP_ID, CPP_ID);
                    }else{
                        const priorityList = isExistUseWorkPartsList(data.NWP_ID ,  data.RRT_ID, CPP_ID);
                        if(priorityList != undefined){
                            $('#'+checkdID).prop('disabled', false);
                            $('#'+checkdID).prop('checked',  priorityList);
                            setPartsTableBackground(data.RRT_ID, data.NWP_ID, CPP_ID) ;
                        } 
                        // const isExistPriority = priorityPartsList.find((item)=>{
                        //     return item.CPP_ID == CPP_ID && item.NWP_ID == data.NWP_ID && item.RRT_ID == data.RRT_ID && item.RWPP_ID == data.RWPP_ID;
                        // });
                        // $('#'+checkdID).prop('disabled', false);
                        // $('#'+checkdID).prop('checked',  isExistPriority);
                        // setPartsTableBackground(data.RRT_ID, data.NWP_ID, CPP_ID) ;
                    }
                    onChangePartsCheckbox(data.NWP_ID, CPP_ID);   
                } 
                beforeNWP_ID = data.NWP_ID;
            }
        });
    }
}

/**
 * 메인 테이블 동적으로.
 * @param {dataArray : 작업항목 데이터 전체}
 */
getTypePartsArray = (dataArray, type) => {
    let beforeCPP_ID = '';
    let typeArray = [];
    dataArray.map((item)=>{
        if(item.CPP_PART_TYPE == type && beforeCPP_ID != item.CPP_ID){
            typeArray.push(item);
        }
        beforeCPP_ID = item.CPP_ID;
    });
    return typeArray;
}

/**
 * 테이블 헤더 세팅
 */
setWorkPartsHeaderTable = (dataArray) => {
    stdPartsList = dataArray;
    setGaragePartsTable(dataArray);            // 기본으로 4-1,4-2 테이블에 전체 테이블 뿌려주기
    
    let releaseBtnHtml = '<tr class="borderAll">';
    releaseBtnHtml += '<th class="text-center active" style="width:50px"><button type="button" class="btn btn-default" onclick="onclickReleaseBtn('+RACON_CHANGE_CD+","+WORK_TYPE_LEFT+')">좌측</button></th>';
    releaseBtnHtml += '<th class="text-center active" style="width:50px"><button type="button" class="btn btn-default" onclick="onclickReleaseBtn('+RACON_CHANGE_CD+","+WORK_TYPE_RIGHT+')">우측</button></th>';
    releaseBtnHtml += '<th class="text-center active" style="width:50px"><button type="button" class="btn btn-default" onclick="onclickReleaseBtn('+RACON_METAL_CD+","+WORK_TYPE_LEFT+')">좌측</button></th>';
    releaseBtnHtml += '<th class="text-center active borderR" style="width:50px"><button type="button" class="btn btn-default" onclick="onclickReleaseBtn('+RACON_METAL_CD+","+WORK_TYPE_RIGHT+')">우측</button></th>';

    let headerTableHtml = '<tr class="borderB"><th class="text-center active" rowspan="3">파트</th>';
    headerTableHtml += '<th class="text-center active tableRightBorder" rowspan="3" colspan="2">작업항목</th> ';
    headerTableHtml += '<th class="text-center active borderR" colspan="4">수리부위</th>';
    headerTableHtml += '<th class="text-center active tableLeftBorder" colspan="'+AtypePartsArray.length+'">사용부품 A</th>';
    headerTableHtml += '<th class="text-center active tableLeftBorder" colspan="'+BtypePartsArray.length+'">사용부품 B</th></tr>';
    headerTableHtml += '<tr><th class="text-center greentd_" colspan="2">교환</th>';
    headerTableHtml += '<th class="text-center bluetd_ borderR" colspan="2">판금</th>';

    AtypePartsArray.map((data, index)=>{
        let classHtml = "";
        if(index == 0){
            classHtml = "tableLeftBorder";
        }
        headerTableHtml += '<th class="text-center '+data.CPP_ID+" "+classHtml+'">'+data.CPP_NM_SHORT+'</th>';
        releaseBtnHtml += '<th class="text-center release'+" "+classHtml+'" id="USE_PARTS_HEADER_'+data.CPP_ID+'"></th>';
    });
    BtypePartsArray.map((data, index)=>{
        let classHtml = "";
        if(index == 0){
            classHtml = "tableLeftBorder";
        }
        headerTableHtml += '<th class="text-center '+data.CPP_ID+" "+classHtml+'">'+data.CPP_NM_SHORT+'</th>';
        releaseBtnHtml += '<th class="text-center release'+" "+classHtml+'" id="USE_PARTS_HEADER_'+data.CPP_ID+'"></th>';
    });
    headerTableHtml += '</tr>';
    releaseBtnHtml += '</tr>';

    $('#workPartsHeaderTable').html(headerTableHtml+releaseBtnHtml);  
}

/**
 * 사용부품A,B 테이블 동적으로 세팅
 * 해당 작업항목에 우선/대체,체화 값이 있으면 백그라운드 색깔 변경
 * @param {data : 해당 작업항목 데이터}
 * @param {dataArray : 사용부품 A 혹은 B 아이템 리스트}
 * @param {classType : 백그라운드 색깔을 위한 클래스}
 */
getUsePartsTable = (data, dataArray, classType) =>  {
    let tableText = '';

    /** 우선순위 값을 확인하고 있으면 바로 리턴 없으면 그 다음 대체/체화 값들이 있는지 확인한다. */
    dataArray.map((item, index)=>{
        let isSetColor = false;   // 해당 작업항목에 설정값이 있는지..
        if(data.CPP_ID != null){  // 우선순위 확인
            isSetColor = data.CPP_ID.split(",").find((CPP_ID, index)=>{
                return item.CPP_ID == CPP_ID;
            })
        }

        if(isSetColor == undefined){ // FROM_CPP_ID 확인
            if(data.FROM_CPP_ID != null){
                isSetColor = data.FROM_CPP_ID.split(",").find((FROM_CPP_ID, index)=>{
                    return item.CPP_ID == FROM_CPP_ID;
                })
            }
        }
        
        if(isSetColor == undefined){ //TO_CPP_ID 확인
            if(data.TO_CPP_ID != null){
                isSetColor = data.TO_CPP_ID.split(",").find((TO_CPP_ID, index)=>{
                    return item.CPP_ID == TO_CPP_ID;
                })
            }
        }

        let classHtml = "";
        if(index == 0){
            classHtml = "tableLeftBorder";
        }

        if(isSetColor){
            tableText +=  "<td class='text-center "+classType+" "+classHtml+" PARTS_TD_"+data.NWP_ID+" CPP_ID_TD_"+item.CPP_ID+"' id='PARTS_TD_"+data.NWP_ID+"_"+item.CPP_ID+"'>";
        }else{
            tableText +=  "<td class='text-center PARTS_TD_"+data.NWP_ID+" CPP_ID_TD_"+item.CPP_ID+" "+classHtml+"' id='PARTS_TD_"+data.NWP_ID+"_"+item.CPP_ID+"'>";
        }
        
        tableText += "<input type='checkbox' name='USE_PARTS_CHECK' onchange='onChangePartsCheckbox(\""+data.NWP_ID+"\",\""+item.CPP_ID+"\")' class='PARTS_CHECK_"+data.NWP_ID+" CPP_ID_CHECK_"+item.CPP_ID+"'' id='PARTS_CHECK_"+data.NWP_ID+"_"+item.CPP_ID+"' value='"+item.CPP_ID+"' disabled></td>";
      
    });

    return tableText;
}
/**
 * 사용부품 테이블 세팅
 * @param {dataArray}
 */
setWorkPartsTable = (dataArray) => {  
    let tableText = ''; 
    let beforeRWP_NM2 = '';   // 전 파트 이름
    let rwpNm2Array  = [];    // 파트이름과 동일한 아이템이 몇개인지. rowspan
    let trCount = 0;          // 각 파트에 몇번째 줄인지..마지막에 class = borderAll 추가해주기 위해
    workPartsList = dataArray;

    dataArray.map((data, index)=>{ 
        if(beforeRWP_NM2 != data.RWP_NM2 ){
            trCount = 0;
            rwpNm2Array = dataArray.filter((rwpNm2, index)=>{
                return rwpNm2.RWP_NM2 == data.RWP_NM2
            });
            tableText += "<tr class='borderB' id='"+data.NWP_ID+"' > ";
            tableText += "<td class='text-center borderTable active' rowspan='"+rwpNm2Array.length+"'>"+data.RWP_NM2+"</td>";
        }else{
            if(trCount == rwpNm2Array.length -1){
                tableText += "<tr class='borderAll' id='"+data.NWP_ID+"'> ";
            }else{
                tableText += "<tr id='"+data.NWP_ID+"'> ";
            }
            
        }
        tableText +=  "<td class='text-center borderTable'>"+(index+1)+"</td>";
        tableText +=  "<td class='text-left borderTable tableRightBorder'>"+data.RWP_NM1+"</td>";
        tableText += getPartsTable(data, GREEN_CLASS);
        tableText += getPartsTable(data, BLUE_CLASS);
        tableText += getUsePartsTable(data, AtypePartsArray, GREEN_CLASS);
        tableText += getUsePartsTable(data, BtypePartsArray, BLUE_CLASS);
        tableText += "</tr>";

        beforeRWP_NM2 =  data.RWP_NM2;
        trCount++;
        
    });
    $('#workPartsTable').html(tableText); 
    $('#LAODING_CTN').hide();
    $('#MAIN_CTN').show();  
    workPartsTableHtml = $('#workPartsListTable').html();
}

/**
 * 교환/판금부분에 값 넣어주기
 * @param {data : 해당 td 데이터}
 * @param {classType : 초록인지 파랑인지(교환/판금)}
 * @param {isChangeParts : 파트가 바뀌었는지..(바뀌었을 때 첫번째 줄 css)}
 */
getPartsTable = (data, classType) => {  
    let colspan = 0;                  
    let limitCount = 2;              // 좌/우인지 단일인지
    let rrtId = '';                  // 교환/판금
    let rwppId = '';                 // 단일/대체,체화

    if(data.RWT_ID == RWP_ID_1){                 // 단일
        colspan = 2;
        limitCount = 1;
        rwppId = WORK_TYPE_ONE;
     }
     
    if(classType == GREEN_CLASS){
        rrtId = RACON_CHANGE_CD;
    }else{
        rrtId = RACON_METAL_CD;
    }

    let partsHtml = "";
    for(let i=1 ; i<=limitCount ; ++i){
        let checkBoxHtml = '';
        if(colspan == 0){
            let labelText = '';
            if(i ==1){    // 좌/우 이며 i=0인것은 좌
                
                if(data.RWT_ID == RWP_ID_DOWN){
                    rwppId = WORK_PART_INSIDE_TYPE;
                    labelText = '실내'
                }else{
                    rwppId = WORK_TYPE_LEFT;
                    labelText = '좌';
                }
            }else{       // 좌/우 이며 i=0인것은 우
               
                if(data.RWT_ID == RWP_ID_DOWN){
                    rwppId = WORK_PART_LOWER_TYPE;
                    labelText = '하체'
                }else{
                    rwppId = WORK_TYPE_RIGHT;
                    labelText = '우';
                }
            }
            checkBoxHtml = '<label for="CHECK_'+data.NWP_ID+'_'+rrtId+'_'+rwppId+'">'+labelText+'</label>';
        }
        checkBoxHtml += '<input type="checkbox" name="RRT_TD_CHECKBOX" class="borderTable CHECK_'+data.NWP_ID+" "+rrtId+"_"+rwppId+' RRT_TD" id="CHECK_'+data.NWP_ID+'_'+rrtId+'_'+rwppId+'"'; 
        checkBoxHtml += 'onchange="onCheckReckonCheckbox('+data.NWP_ID+','+rrtId+','+rwppId+','+data.RWT_ID+')" value='+JSON.stringify({RRT_ID : rrtId, RWPP_ID :rwppId, NWP_ID : data.NWP_ID})+'>';
        partsHtml += "<td colspan='"+colspan+"' class='text-center NWP_ID_TR_"+data.NWP_ID+" "+classType+"'>"+checkBoxHtml+"</td>";
    }
    return partsHtml;
}

//////////////////// 5 작업항목선택 테이블 끝   /////////////////

function setInsuOverlapTable(insuArray, currPage){
    const currentPage = currPage == undefined  ? 1 : currPage;
    let html = '';
    let totalListCount = 0;
    if(insuArray.length > 0){
        insuArray.map((insuItem)=>{
            insuItem[0].map((item)=>{
                html += "<tr>";
                html += "<td>"+item.WRT_DTHMS+"</td>";
                html += "<td>"+item.MII_NM+"</td>";
                html += "<td>"+item.NBI_REGI_NUM+"</td>";
                html += "<td>"+item.RLT_NM+"</td>";
                html += "<td>"+item.NB_CAR_NUM+"</td>";
                html += "<td>"+item.MGG_NM+"</td>";
                html += "<td>"+numberWithCommas(item.NB_TOTAL_PRICE)+"</td>";
                html += "<td>"+item.NBI_INSU_DEPOSIT_DT+"</td>";
                html += "<td>"+item.RDET_NM+"</td>";
                html += "</tr>";
            });
            totalListCount += Number(insuItem[1]);
        });
        subPaging(totalListCount, currentPage, 'setInsuOverlapTable', 5, 'subPagination'); 
        isOverlapBill = true;
    }else{
        html = '<tr><td colspan="9">중복된 정보가 없습니다.</td></tr>';  
        isOverlapBill = false;
    }
    isClickOverlapBtn = true;
    $('#insuOverlapTable').html(html);
    $('#overlapcheck').modal('show');
}

/**
 * 계산 후 해당보험사에 보상담당기준이 존재하는 담당자가 있으면 그사람으로 변경
 * @param {data : 고액/소액 기준}
 * @param {IS_SUBMIT : True일 경우에는 저장/계산 , 아닐경우에는단순 조견표계산}
 */
setInsuManagerTable = (data, IS_SUBMIT) => {
    const selectCarType = $('#RCT_CD').val();
    const LOW_PRICE = data[0].PIE_LOW_PRICE;
    const HIGH_PRICE = data[0].PIE_HIGH_PRICE;
    let insuManagementArray = [];

    $('.RICT_ID').each(function(){
        let manageType = '';
        let MII_ID = '';
        let RLT_NM = '';
        let RLT_ID = '';
        let isValidation = true;

        if($(this).attr('id').split("_").length == 2){
            MII_ID = 'MII_ID';
            RLT_NM = '자차';
            RLT_ID = RLT_MY_CAR_TYPE;
        }else{
            MII_ID = 'MII_ID_';
            RLT_NM = '대물';
            RLT_ID = RLT_PI_TYPE;
        }

        if($('#MII_ID_LIST_'+RLT_ID).is(':visible')){
            if(selectCarType.startsWith(CAR_TYPE_FOREIGN)){     // 외제차
                if($(this).val() != RICT_TYPE_FOREIGN){        // 보상담당자가 외제차 타입인지 
                    manageType = RICT_TYPE_FOREIGN;
                    isValidation = false;
                }       
            }else{
                const BILL_TOTAL_PRICE = Number($('#billTotalTable').html().replace(/,/g, ''));
           
                if(BILL_TOTAL_PRICE > HIGH_PRICE ){      // 고액
                    manageType = 11330;
                }else if(BILL_TOTAL_PRICE < LOW_PRICE){  // 소액
                    manageType = 11310;
                }else{                                   // 일반
                    manageType = 11320;
                }

                if($(this).val() != manageType){   
                    isValidation = false;
                }
            }

            if(!isValidation){     
                insuManagementArray.push(JSON.stringify({
                    MII_ID : $('#'+MII_ID).val(),
                    RICT_ID : manageType,
                    RLT_NM : RLT_NM,
                    RLT_ID : RLT_ID
                }));
            }
        }
        
    })
  
    if(insuManagementArray.length > 0){
        getInsuManageType(insuManagementArray).then((result)=>{
            if(result.length > 0){                              // 해당보험사에 타입이 맞는 보상담당자가 있을 경우
                result.map((item)=>{
                    let idType = '';
                    if(item.RLT_ID != RLT_MY_CAR_TYPE){
                        idType = '_';
                    }
                    $('#SELECT_NBI_CHG_NM'+idType).val(item.NIC_ID);    
                    $('#RICT_ID'+idType).val(item.RICT_ID);  
                    $('#RICT_NM'+idType).html(item.RICT_NM);    
                    $('#INSU_MANAGE_COUNT'+idType).html(item.CLAIM_CNT);      // 2.보험사 선택 당월청구건수                
                    $('#NBI_CHG_NM'+idType).val(item.NIC_NM_AES);                                         
                    $("#NBI_FAX_NUM"+idType).val(phoneFormat(item.NIC_FAX_AES));                  // 2.보험사 선택 담당자 팩스
                    $('#INSU_MANAGE_REGIST_DATE'+idType).html(item.WRT_DTHMS);    // 2.보험사 선택 담당등록일 

                });
            }
            if(IS_SUBMIT != undefined && IS_SUBMIT){
                if(validation()){
                    addData();
                }        
            }    
        });
    }else{
        /** undefinded가 아니고 true일 경우에는 저장(계산) 일 경우. 계산 다 된뒤에 addData전송 */
        if(IS_SUBMIT != undefined && IS_SUBMIT){
            if(validation()){
                addData();
            }
        }
    }  
}

/**
 * 당월청구 및 재고금액
 */
setMonthBillStockTable = (dataArray) => {
    if(dataArray.length > 0){
        $('#monthMGG_NM').html(dataArray[0].MGG_NM);
        $('#month2BEFORE_CNT').html(dataArray[0].bBeforeMonthCNT);
        $('#monthBEFORE_CNT').html(dataArray[0].beforeMonthCNT);
        $('#month_CNT').html(dataArray[0].monthCNT);
        $('#month_PRICE').html(numberWithCommas(Number(dataArray[0].monthPRICE)));
        $('#monthSTOCK_PRICE').html(numberWithCommas(Number(dataArray[0].stockPRICE)));
    }else{
        $('#monthMGG_NM').html('');
        $('#month2BEFORE_CNT').html('');
        $('#monthBEFORE_CNT').html('');
        $('#month_CNT').html('');
        $('#month_PRICE').html('');
        $('#monthSTOCK_PRICE').html('');
    }
}

/**
 * 작년청구대비재고대비 형황
 */
setBeforeMonthBillStockTable = (dataArray) => {
    let stockHtml = "";
    if(dataArray.length > 0){
        dataArray.map((item)=>{
            stockHtml += "<tr>";
            stockHtml += "<td class='text-center'>"+item.CPP_NM+"</td>";
            stockHtml += "<td class='text-center'>"+item.SUPPLY+"</td>";
            stockHtml += "<td class='text-center'>"+item.BILL+"</td>";
            stockHtml += "<td class='text-center'>"+item.STOCK+"</td>";
            stockHtml += "<td class='text-center'>"+item.RATIO+"</td>";
            stockHtml += "</tr>";
        });
       
    }else{
        stockHtml += "<tr>";
        stockHtml += "<td class='text-center'></td>";
        stockHtml += "<td class='text-center'></td>";
        stockHtml += "<td class='text-center'></td>";
        stockHtml += "<td class='text-center'></td>";
        stockHtml += "<td class='text-center'></td>";
        stockHtml += "</tr>";
    }
    $('#BEFORE_MONTH_STOCK_TABLE').html(stockHtml);
}