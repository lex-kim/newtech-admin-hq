/**
 * table을 동적으로 세팅해주는 js
 *
 */

let visitArray = [];         // 임원방문일을 저장하는 변수
let inputVisitArray = [];    // 새롭게 추가된 임원방문일을 저장하는 변수. 이 부부만 form으로전송해 저장한다.
let PARTS_SHORT_ARR = [];    // 거래중단시 재고부족분에서 보여질 부품이름

onLoadRegisterPopup = () => {
    /** 로딩시 공업사코드값이 빈값이면 등록 아니면 수정 */
    if($('#ORI_MGG_ID').val().trim() != ''){
        getGwInfo();
        $('#btnSubmit').html('수정');
        $('#btnDelete').show();
    }else{
        setPartsTable();
        $('#btnSubmit').html('등록');
        $('#btnDelete').hide();
    }
}

/** 수정일 경우 해당공엊사에 대한 정보를 가져온다. */
getGwInfo = () => {
    const data = {
        REQ_MODE: CASE_READ,
        MGG_ID : $('#ORI_MGG_ID').val(),
        NSU_ID : $('#ORI_NSU_ID').val(),
    };

    callAjax(ajaxUrl, 'GET', data).then((result)=>{
        const data = JSON.parse(result);
        if(data.status == OK){
            getGwUpdateData(data.data[0], data.data[1]);    // 공업사 정보를 가지고 해당 input에 앎자게 넣어준다.(setData.js)
        }else{
            alert("해당 공업사정보를 가져오는데 실패하였습니다.");
        }

    });
}


/** 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
    const tdLength = $('table tr th').length;
    const tableText = "<tr><td class='text-center' colspan='"+tdLength+"'>조회된 데이터가 존재하지 않습니다.</tr></tr>";
    $('.pagination').html('');
    $('#dataTable').html(tableText);
}

function setGaragePartsTable(dataArray){
        if(dataArray == null) return '';
        if(dataArray.split('||').length > 0){
        let text = "";
        dataArray.split('||').map(function(item){
            text += item+'<br>';
        });
        return text;
    }else{
        return '';
    }
}

/** 메인 리스트 테이블 동적으로 생성
 * @param {dataArray : 리스트 데이터, currentPage : 현재페이지(페이징을 위해)}
*/
setTable = (totalListCount, dataArray, currentPage) => {
    let tableText = '';
    dataArray.map((data,i)=>{
        let rowCPP_NM_SHORT = ''; //공급부품
        let rowCPPS_NM_SHORT = ''; //사용부품
        let rowNGP_RATIO = ''; // 배율적용
        let rowNGP_MEMO = ''; //메모

        CODE_PARTS.map((cpp)=>{
            const partsArray = (data.PARTS_INFO).filter(function(parts){
                return cpp.CPP_ID == parts.CPP_ID;
            });
            
            if(partsArray.length > 0){
                partsArray.map((parts,idx)=>{
                    let brText = "";
                    if(idx == partsArray.length-1){
                        brText = "<br>";
                    }
                    rowCPP_NM_SHORT += (cpp.CPP_NM_SHORT+parts.CPP_NM_SHORT+brText);
                    rowCPPS_NM_SHORT += parts.CPPS_NM_SHORT+brText;
                    rowNGP_RATIO += parts.NGP_RATIO+brText;
                    rowNGP_MEMO += parts.NGP_MEMO+brText;  
                });
            }else{
                rowCPP_NM_SHORT += cpp.CPP_NM_SHORT+"<br>";
                rowCPPS_NM_SHORT += ""+"<br>";
                rowNGP_RATIO += ""+"<br>";
                rowNGP_MEMO += ""+"<br>"; 
            }  
        });


        tableText += "<tr>";
        tableText += "<td class='text-center'>"+data.IDX+"</td>";
        tableText += "<td class='text-center'>"+data.CSD_NM+"</td>";
        tableText += "<td class='text-center hidetd'>"+data.CRR_1_NM+"</td>";
        tableText += "<td class='text-center hidetd'>"+data.CRR_2_NM+"</td>";
        tableText += "<td class='text-center hidetd'>"+data.CRR_3_NM+"</td>";
        tableText += "<td class='text-center hidetd'>"+data.MGG_ID+"</td>";
        tableText += "<td class='text-center'>"+data.MGG_USERID+"</td>"
        tableText += "<td class='text-center'>"+bizNoFormat(data.MGG_BIZ_ID)+"</td>"
        tableText += "<td class='text-center'>"+data.MGG_PASSWD_AES+"</td>"
        tableText += "<td class='text-center hidetd'>"+data.MGG_CIRCUIT_ORDER+"</td>"
        tableText += "<td class='text-left' >"+data.MGG_NM+"</td>"
        tableText += "<td class='text-center'>"+data.MGG_BIZ_START_DT+"</td>"
        tableText += "<td class='text-center'>"+(data.MGG_BIZ_END_DT == null ? '' : data.MGG_BIZ_END_DT)+"</td>"
        tableText += "<td class='text-center'>"+(data.RET_NM == null ? '' : data.RET_NM)+"</td>"
        tableText += "<td class='text-center hidetd'>"+(data.MGG_RET_DT == null ? '' : data.MGG_RET_DT)+"</td>"
        tableText += "<td class='text-center hidetd'>"+(data.MGG_RAT_DT == null ? '' : data.MGG_RAT_DT)+"</td>"
        tableText += "<td class='text-center hidetd'>"+(data.RPET_NM == null ? '' : data.RPET_NM)+"</td>"
        tableText += "<td class='text-center hidetd'>"+(data.MGG_RPET_DT == null ? '' : data.MGG_RPET_DT)+"</td>"
        tableText += "<td class='text-center hidetd'>"+(data.MII_NM == null ? '' : data.MII_NM)+"</td>"
        tableText += "<td class='text-center'>"+(data.RAT_NM == null ? '' : data.RAT_NM)+"</td>"
        tableText += "<td class='text-center hidetd'>"+data.MGG_RAT_NM+"</td>"
        tableText += "<td class='text-center hidetd'>"+(data.MGG_BANK_NUM_AES == null ? '' : data.MGG_BANK_NUM_AES)+"</td>"
        tableText += "<td class='text-center'>"+data.MGG_LOGISTICS_YN+"</td>"
        tableText += "<td class='text-left'>"+data.MGGS_ADDR+"</td>"
        tableText += "<td class='text-center'>"+data.MGG_CEO_NM_AES+"</td>"
        tableText += "<td class='text-center'>"+phoneFormat(data.MGG_CEO_TEL_AES)+"</td>"
        tableText += "<td class='text-center hidetd'>"+data.MGG_KEYMAN_NM_AES+"</td>"
        tableText += "<td class='text-center hidetd'>"+phoneFormat(data.MGG_KEYMAN_TEL_AES)+"</td>"
        tableText += "<td class='text-center'>"+data.MGG_CLAIM_NM_AES+"</td>"
        tableText += "<td class='text-center'>"+phoneFormat(data.MGG_CLAIM_TEL_AES)+"</td>"
        tableText += "<td class='text-center'>"+phoneFormat(data.MGG_OFFICE_TEL)+"</td>"
        tableText += "<td class='text-center'>"+phoneFormat(data.MGG_OFFICE_FAX)+"</td>"
        tableText += "<td class='text-center hidetd'>"+data.MGG_PAINTER_NM_AES+"</td>"
        tableText += "<td class='text-center hidetd'>"+phoneFormat(data.MGG_PAINTER_TEL_AES)+"</td>"
        tableText += "<td class='text-center hidetd'>"+data.MGG_METAL_NM_AES+"</td>"
        tableText += "<td class='text-center hidetd'>"+phoneFormat(data.MGG_METAL_TEL_AES)+"</td>"
        tableText += "<td class='text-center hidetd'>"+data.ROT_NM+"</td>"
        tableText += "<td class='text-center'>"+data.MGG_INSU_MEMO+"</td>"
        tableText += "<td class='text-center'>"+data.MGG_MANUFACTURE_MEMO+"</td>"
        tableText += "<td class='text-center hidetd'>"+data.MGG_FORCE_SEND_YN+"</td>"
        tableText += "<td class='text-center hidetd'>"+data.MGG_BILL_ALL_YN+"</td>"
        tableText += "<td class='text-center hidetd'>"+data.MGG_BILL_MONEY_SHOW_YN+"</td>"
        tableText += "<td class='text-center hidetd'>"+data.MGG_WAREHOUSE_CNT+"</td>"
        tableText += "<td class='text-center hidetd'>"+data.RMT_NM+"</td>"
        tableText += "<td class='text-left hidetd'><div class='long-text'>"+data.MGG_BILL_MEMO+"</div></td>"
        tableText += "<td class='text-left hidetd'><div class='long-text'>"+data.MGG_GARAGE_MEMO+"</div></td>"
        tableText += "<td class='text-center'>"+data.RCT_NM+"</td>"
        tableText += "<td class='text-center'>"+data.MGG_FRONTIER_NM+"</td>"
        tableText += "<td class='text-center hidetd'>"+data.MGG_COLLABO_NM+"</td>"
        tableText += "<td class='text-center'>"+data.MGG_BAILEE_NM+"</td>"
        tableText += "<td class='text-center hidetd'>"+data.MGG_BIZ_BEFORE_NM+"</td>"
        tableText += "<td class='text-center hidetd'>"+data.MGG_CONTRACT_YN+"</td>"
        tableText += "<td class='text-center hidetd'>"+(data.MGG_CONTRACT == null ? '' : data.MGG_CONTRACT)+"</td>"
        tableText += "<td class='text-center'>"+(data.RSR_NM == null ? '' : data.RSR_NM)+"</td>"
        tableText += "<td class='text-left'>"+data.MGG_OTHER_BIZ_NM+"</td>"
        tableText += "<td class='text-left'>"+nullChk(data.SLL_NM)+"</td>"
        tableText += "<td class='text-center hidetd'>"+data.MGG_END_AVG_BILL_CNT+"</td>"
        tableText += "<td class='text-center hidetd'>"+data.MGG_END_AVG_BILL_PRICE+"</td>"
        tableText += "<td class='text-left hidetd'>"+data.MGG_END_OUT_OF_STOCK+"</td>"
        tableText += "<td class='text-center hidetd'>"+data.MGG_SALES_NM+"</td>"
        tableText += "<td class='text-center hidetd'>"+data.RST_NM+"</td>"
        tableText += "<td class='text-center hidetd'>"+data.MGG_SALES_MEET_LVL+"</td>"
        tableText += "<td class='text-center hidetd'>"+(data.MGG_SALES_MEET_NM_AES == null ? '' : data.MGG_SALES_MEET_NM_AES)+"</td>"
        tableText += "<td class='text-center hidetd'>"+(data.MGG_SALES_MEET_TEL_AES == null ? '' : phoneFormat(data.MGG_SALES_MEET_TEL_AES))+"</td>"
        tableText += "<td class='text-center hidetd'>"+(data.RRT_NM == null ? '' : data.RRT_NM )+"</td>"
        tableText += "<td class='text-left hidetd'>"+data.MGG_SALES_WHYNOT+"</td>"
        tableText += '<td class="text-left hidetd">'+rowCPP_NM_SHORT+'</td>';
        tableText += '<td class="text-left hidetd">'+rowCPPS_NM_SHORT+'</td>';
        tableText += '<td class="text-left hidetd">'+rowNGP_RATIO+'</td>';
        tableText += '<td class="text-left hidetd">'+rowNGP_MEMO+'</td>';
        // tableText += getPartsTable(dataArray[1].filter((parts)=>{return parts.MGG_ID == data.MGG_ID}));
        if($("#AUTH_UPDATE").val() == YN_Y){
            tableText += " <td class='text-center'><button type='button' class='btn btn-default' onclick=openWindowPopup(\""+data.MGG_ID+"\")>수정</button></td>";
        }
        tableText += "</tr>";
        // console.log(i , ' ', data.MGG_ID , ' ',data.MGG_NM, CODE_PARTS,'>>>>>>>>>', rowPartsInfo);

    });
    paging(totalListCount, currentPage, 'getList');

    $('#dataTable').html(tableText);

    if($('#extend').is(':checked')){
        $(".hidetd").show();
    }else{
        $(".hidetd").hide();
    }

}


/**
 * 메인리스트에서 공급부품
 * @param {partsArray : 해당부품정보들}
 */
getPartsTable = (partsArray) => {
    let partsHtml = '<td class="text-left hidetd">';
    let useHtml = '<td class="text-left hidetd">';
    let ratioHtml = '<td class="text-right hidetd">';
    let memoHtml = '<td class="text-left hidetd">';
    let beforeCPP_ID = '';                              // 이전CPP_ID 이거를 가지고 현재 CPP_ID와 비교하여 줄을 띄울지 말지를 결정
    let getLastIdx = 0;                                 // 공급부품에서 ,를 넣어주기 위해 한 줄에 마지막 인덱스가 몇인지
    let cppsIdx = 0;                                    // 한 줄에 몇번째 인덱스인지

    partsArray.map((parts, index)=>{

        if(beforeCPP_ID != parts.CPP_ID){
            partsHtml += ((index > 0) ? "<br/>" : '') + parts.CPP_NM_SHORT+" : ";
            useHtml += ((index > 0) ? "<br/>" : '');
            ratioHtml += ((index > 0) ? "<br/>" : '') + parts.NGP_RATIO;
            memoHtml += ((index > 0) ? "<br/>" : '') + parts.NGP_MEMO;

            /** 공급부품에서 마지막 행에는 ,를 안넣어주기 위해서 */
            getLastIdx =  Number(partsArray.filter((cpps)=>{
                return cpps.CPP_ID == parts.CPP_ID
            }).length)-1;
            cppsIdx = 0;
        }

        partsHtml += parts.CPPS_NM_SHORT +  (cppsIdx < getLastIdx ? " , " : '') ;
        useHtml += parts.NGP_DEFAULT_YN == 'Y' ? parts.CPPS_NM_SHORT  : (cppsIdx > 0 ? '&nbsp' : '');
        memoHtml += '&nbsp';    //공백이 없으면 라인이 안맞음

        cppsIdx++;
        beforeCPP_ID = parts.CPP_ID;
    });
    partsHtml,useHtml,memoHtml,ratioHtml += "</td>";

    return partsHtml+useHtml+ratioHtml+memoHtml;
}


getPartsChecked = (partsArray) => {
    if(partsArray == undefined){

    }else{

    }
}

/**
 * 사용 및 공급부품 테이블 세팅
 */
setPartsTable = (partsDataArray) => {
    const data = {
        REQ_MODE: GET_PARTS_TYPE,
    };

    callAjax(ajaxUrl, 'GET', data).then((result)=>{
        const data = JSON.parse(result);
        if(data.status == OK){
            const partsArray = data.data;
            let partsTableHtml = '';
            let beforeCPP_ID = '';
            let trCount = 0;

            partsArray.map((parts, index)=>{
                if(beforeCPP_ID != parts.CPP_ID){
                    partsTableHtml += "<tr id='partsInfo_"+trCount+"'>";
                    partsTableHtml += '<td class="text-left">'+parts.CPP_NM+'</td>';
                    partsTableHtml += '<td class="text-left form-inline">';
                    partsArray.map((subParts, subIdx)=>{
                        
                        if(subParts.CPP_ID == parts.CPP_ID){
                            // if(partsDataArray == undefined){
                            //     partsTableHtml += "<label for='CPPS_ID_"+subIdx+"'><input type='checkbox' class='PARTS_CPPS_ID' onchange='onChangeParts("+trCount+")'  id='CPPS_ID_"+subIdx+"' name='CPPS_ID[]' value="+JSON.stringify({CPPS_ID:subParts.CPPS_ID, CPPS_NM : subParts.CPPS_NM, CPP_ID : parts.CPP_ID, IDX : trCount})+">";
                            // }else{
                            //     const data = partsDataArray.find((item)=>{
                            //         return item.CPPS_ID == subParts.CPPS_ID
                            //     });

                            //     if(data){
                            //         partsTableHtml += "<label for='CPPS_ID_"+subIdx+"'><input type='checkbox' checked class='PARTS_CPPS_ID' onchange='onChangeParts("+trCount+")'  id='CPPS_ID_"+subIdx+"' name='CPPS_ID[]' value="+JSON.stringify({CPPS_ID:subParts.CPPS_ID, CPPS_NM : subParts.CPPS_NM, CPP_ID : parts.CPP_ID, IDX : trCount})+">";
                            //     }else{
                            //         partsTableHtml += "<label for='CPPS_ID_"+subIdx+"'><input type='checkbox'  class='PARTS_CPPS_ID' onchange='onChangeParts("+trCount+")'  id='CPPS_ID_"+subIdx+"' name='CPPS_ID[]' value="+JSON.stringify({CPPS_ID:subParts.CPPS_ID, CPPS_NM : subParts.CPPS_NM, CPP_ID : parts.CPP_ID, IDX : trCount})+">";

                            //     }

                            // }
                            partsTableHtml += "<label for='CPPS_ID_"+subIdx+"'><input type='checkbox' class='PARTS_CPPS_ID' onchange='onChangeParts("+trCount+")'  id='CPPS_ID_"+subIdx+"' name='CPPS_ID[]' value='"+JSON.stringify({CPPS_ID:subParts.CPPS_ID, CPPS_NM : subParts.CPPS_NM, CPP_ID : parts.CPP_ID, IDX : trCount})+"'>";

                            partsTableHtml += subParts.CPPS_NM+'</label>';
                        }
                    });
                    partsTableHtml += '</td>';
                    partsTableHtml += '<td class="text-center">';
                    partsTableHtml += '<select class="form-control" class="USE_PARTS" id="NGP_DEFAULT_YN_'+trCount+'" name="NGP_DEFAULT_YN[]">';
                    partsTableHtml += '<option value="">사용안함</option>'
                    partsTableHtml += '</select></td>';
                    partsTableHtml += '<td class="text-center">';
                    partsTableHtml += "<input type='hidden' class='RATIO_PARTS' value='"+JSON.stringify({CPP_ID : parts.CPP_ID, IDX : trCount})+"'>";
                    partsTableHtml += '<input type="text" class="form-control text-right"  onkeypress="return isFloat(event)" name="NGP_RATIO[]" id="NGP_RATIO_'+trCount+'">';
                    partsTableHtml += '</td>';
                    partsTableHtml += '<td class="text-center">';
                    partsTableHtml += "<input type='hidden' class='MEMO_PARTS' value='"+JSON.stringify({CPP_ID : parts.CPP_ID, IDX : trCount})+"'>";
                    partsTableHtml += '<input type="text" class="form-control text-right" name="NGP_MEMO[]" id="NGP_MEMO_'+trCount+'">';
                    partsTableHtml += '</td>';
                    partsTableHtml += "</tr>";
                    trCount++;
                }
                beforeCPP_ID = parts.CPP_ID;
            });
            $('#partsTable').html(partsTableHtml);


            if(partsDataArray != undefined){  // 수정사항시에만 동적으로 생성된 표에서 데이터 넣어주기
                $('.PARTS_CPPS_ID').each((function(){
                    console.log($(this).val());
                    const partsData = JSON.parse($(this).val());
                    const checkResult = partsDataArray.find((parts)=>{
                        return partsData.CPP_ID == parts.CPP_ID && partsData.CPPS_ID == parts.CPPS_ID
                    });

                    if(checkResult != undefined){
                        let usePartsHtml = '<option value='+checkResult.CPPS_ID+'>'+checkResult.CPPS_NM+'</option>';
                        $('#NGP_DEFAULT_YN_'+partsData.IDX).append(usePartsHtml);
                        if(checkResult.NGP_DEFAULT_YN == 'Y'){
                            $('#NGP_DEFAULT_YN_'+partsData.IDX).val(checkResult.CPPS_ID);
                        }

                        $(this).attr('checked',true);
                    }

                }));

                $('.USE_PARTS').each((function(){
                    const partsData = JSON.parse($(this).val());
                    const checkResult = partsDataArray.find((parts)=>{
                        return partsData.CPP_ID == parts.CPP_ID && partsData.CPPS_ID == parts.CPPS_ID
                    });
                    if(checkResult != undefined){
                        $(this).attr('checked',true);
                    }

                }));

                // /** 배율적용 */
                $('.RATIO_PARTS').each((function(){
                    const partsData = JSON.parse($(this).val());
                    const checkResult = partsDataArray.find((parts)=>{
                        return partsData.CPP_ID == parts.CPP_ID;
                    });

                    if(checkResult != undefined){
                        $('#NGP_RATIO_'+partsData.IDX).val(checkResult.NGP_RATIO);
                    }


                }));

                /** 메모 */
                $('.MEMO_PARTS').each((function(){
                    const partsData = JSON.parse($(this).val());
                    const checkResult = partsDataArray.find((parts)=>{
                        return partsData.CPP_ID == parts.CPP_ID;
                    });

                    if(checkResult != undefined){
                        $('#NGP_MEMO_'+partsData.IDX).val(checkResult.NGP_MEMO);
                    }
                }));
            }
        }
    });


}

/**
 * 공급 및 사용부품 클릭시 selectbox에 set
 * @param {idx : 몇번째 인덱스 아이템인지}
 */
onChangeParts = (idx) => {
    let usePartsHtml = '<option value="">사용안함</option>';
    $('#partsInfo_'+idx+' input[type=checkbox]:checked').each(function() {
        
        item = JSON.parse($(this).val());
        usePartsHtml += '<option value='+item.CPPS_ID+'>'+item.CPPS_NM+'</option>';
    });

    const oriOptionValue = $('#NGP_DEFAULT_YN_'+idx).val();
    $('#NGP_DEFAULT_YN_'+idx).html(usePartsHtml);
    $('#NGP_DEFAULT_YN_'+idx).val(oriOptionValue);
    if($('#NGP_DEFAULT_YN_'+idx).val() == null){
        $('#NGP_DEFAULT_YN_'+idx).val('');
    }
}


/**
 * 거래 중단 선택했을 때 재고부족 분 및 3개월 거래 정보 표현
 * @param {isShow : 테이블을 보여줄지 안보여줄지 여부}
 */
showConstractStopTable = (isShow) => {
    if($('#MGG_END_AVG_BILL_CNT').val() != 0 || $('#MGG_END_AVG_BILL_PRICE').val() != 0){
        $("#stopTable").show();
    }else{
        const data = {
            REQ_MODE: GET_PARTS_INFO,
            MGG_ID : $('#ORI_MGG_ID').val()
        };

        callAjax(ajaxUrl, 'GET', data).then((result)=>{
            if(isJson(result)){
                const data = JSON.parse(result);
                if(data.status == OK){
                    setStopTable(data.data[0]);
                }else{
                    alert(ERROR_MSG_1100);
                }
            }else{
                alert(ERROR_MSG_1200);
            }
        });
    }

}


/**
 * 임원방문일 정보 가져오기(수정상태에서..)
 */
getVisitInfo = () => {
    const data = {
        REQ_MODE: GET_REF_OPTION,
        REF_TYPE : type
    };

    callAjax(ajaxUrl, 'GET', data).then((result)=>{
        const data = JSON.parse(result);
        visitArray = data.data;
        setVisitTable();
    });
}



setStopTable = (item) => {
   $('#MGG_END_AVG_BILL_CNT').val(item.AVG_CNT == null ? 0 : item.AVG_CNT);
   $('#MGG_END_AVG_BILL_PRICE').val(item.AVG_PRICE == null ? 0 : item.AVG_PRICE );
   $("#stopTable").show();
}

inputVisitDate = () => {
    if($('#NGV_VISIT_DT').val() != ''){
        const result = visitArray.find((visit, index)=>{
            return visit == $('#NGV_VISIT_DT').val();
        });
        if(result){
            alert("중복된 방문월이 있습니다.");
            $('#NGV_VISIT_DT').focus();
        }else{
            inputVisitArray.push($('#NGV_VISIT_DT').val());
            $('#visitDiv').append('<input type="hidden" name="NGV_VISIT_DT[]" id="NGV_VISIT_DT_'+inputVisitArray.length+'" value="'+$('#NGV_VISIT_DT').val()+'"/>');
            visitArray.push($('#NGV_VISIT_DT').val());
            $('#NGV_VISIT_DT').val('');
            setVisitTable();
        }

    }
}

/**
 * 임원방문일 테이블 세팅
 */
setVisitTable = () => {
    /** 시간순으로 sort */
    const sortVisitArray = visitArray.sort((a,b)=>{
        return (new Date(a).getTime() > new Date(b).getTime()) ? -1 : 1;
    });
    let visitTableHtml = ' <thead><tr class="active">';
    let visitMonthHtml = '<tbody><tr>';
    let beforeYear = '';
    let beforeMonth = '';

    /**
     * 시간순으로 sort된 배열을 통해 중복된 년도수를 가져와 colspan을 설정해준다.(단 달이 다를경우!)
     */
    sortVisitArray.map((visitInfo, index)=>{
        let currVisitYear = visitInfo.split("-")[0];
        let currVisitMonth = visitInfo.split("-")[1];

        let count = 0;
        let month = '';
        $.each(sortVisitArray,(index,info)=>{

            if(info.split("-")[0] == currVisitYear && month != info.split("-")[1]){
                count++;
            }

            if(info.split("-")[0] != currVisitYear){
                month = '';
            }else{
                month = info.split("-")[1];
            }

        });

        /** 중복된 년도는 표시 안되게 */
        if(beforeYear != currVisitYear){
            visitTableHtml += '<th class="text-center" colspan="'+count+'">'+currVisitYear+'</th>';
        }

        /** 중복된 월은 표시안되게..단 년이 다를경우엔 표시된다 */
        if(beforeMonth != currVisitMonth || beforeYear != currVisitYear){
            visitMonthHtml += '<td style="width:150px" class="text-center">'+currVisitMonth+'</td>';
        }

        beforeYear = currVisitYear;
        beforeMonth = currVisitMonth;
    });
    visitMonthHtml += '</tr></tbody>';
    visitTableHtml += '</tr></thead>';

    $('#visitTable').html(visitTableHtml+visitMonthHtml);

}
