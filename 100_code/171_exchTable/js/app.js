const EXCHANGE_URL ='act/exchTable_act.php';
let cntCar = 0 ; // COUNT OF CAR TYPE
let cntWork = 0 ; // COUNT OF WORK PART
let itemCar = [];
$(document).ready(() =>{
    setPartOptions('CPP_ID','=선택=', $('#P_CPP_ID').val());
    setWorkOptions('RRT_ID','=선택=', $('#P_RRT_ID').val());
    setColumn('tr_car');
    setRow('table_body');
    $('#resetBtn').hide();
    
    if($('#P_CPP_ID').val() != ''){
        setCPPOptions('CPPS_ID','=선택=');
    }

    if($('#P_CPP_ID').val() != '' && $('#P_CPPS_ID').val() != '' && $('#P_RRT_ID').val() != ''){
        getList();
    }

    /* 기준부품 변경시 */
    $('#CPP_ID').change(()=>{
        setCPPOptions('CPPS_ID','=선택=');
    });

    /* 환산부품 변경시*/
    $('#RRT_ID').change(()=>{
        if($('#CPPS_ID').val() != '' && $('#CPP_ID').val() != ''){
            getList();
        }
    });

    /* 작업 변경시*/
    $('#CPPS_ID').change(()=>{
        if($('#CPP_ID').val() != '' && $('#RRT_ID').val() != ''){
            getList();
        }
    });
    
});


/**
 * 환산부품 SELECT 
 * @param {String} objID 부모 테그 ID
 * @param {String} pText 초기 텍스트
 * @param {String} pSelectedVal 선택한 값 
 */
setCPPOptions = (objID, pText, pSelectedVal) => {
    const data = {
        REQ_MODE: GET_PARTS_TYPE,
        CPP_ID: ($('#P_CPP_ID').val() == '') ? $('#CPP_ID').val() : $('#P_CPP_ID').val() ,
    };

    let objOptionList = [];
    callAjax(EXCHANGE_URL, 'GET', data).then((result)=>{
        const res = JSON.parse(result);
        if(res.status == OK){
            const item = JSON.parse(res.data);
            $("#" + objID + " option").remove();  // SELECT 개체 초기화
            if(item.length > 0){
                objOptionList.push($("<option >", { text: pText }).attr("value","")); //최초조건 추가
                item.map((item, i)=>{
                    objOptionList.push($("<option>", { text: item.CPPS_NM }).attr("value",item.CPPS_ID));
                })
                $("#" + objID).append(objOptionList);  // <SELECT> 객체에 반영
                
                if($('#P_CPPS_ID').val() == ''){
                    if(pSelectedVal != undefined) {
                        $("#" + objID).val(pSelectedVal);
                    }
                }else{
                    $("#" + objID).val($('#P_CPPS_ID').val());
                }
                
            }else {
                alert('해당 기준부품에 설정된 환산부품이 없습니다.');
                objOptionList.push($("<option>", { text: pText }).attr("value","")); //최초조건 추가
                $("#" + objID).append(objOptionList);  // <SELECT> 객체에 반영
            }
        }else {
            console.log('조회된 데이터가 존재하지 않습니다.');
        }
    });
}



/**
 * CAR TYPE SELECT 
 * @param {String} objID 부모 테그 ID
 */
setColumn = (objID) => {
    const data = {
        REQ_MODE: GET_CAR_TYPE
    };
    let headerTxt = '';
    callAjax(EXCHANGE_URL, 'GET', data).then((result)=>{
        // console.log('table_hedder >>',result)
        const res = JSON.parse(result);
        if(res.status == OK){
            itemCar = res.data;
            cntCar = itemCar.length;
            $("#" + objID +" th").remove();  // thead 하위 <tr>개체 초기화
            if(itemCar.length > 0){
                $('#td_part_header').attr("colspan", cntCar);
                itemCar.map((item, i)=>{
                    headerTxt += "<th class='text-center'>"+item.RCT_NM+"</th>";
                })

                $("#" + objID).append(headerTxt);  // <SELECT> 객체에 반영

                
            
            }
        }
    });
}


/**
 * WORK TYPE SELECT 
 * @param {String} objID 부모 테그 ID
 */
setRow = (objID) => {
    const data = {
        REQ_MODE: GET_REF_WORK_PART
    };

    callAjax(EXCHANGE_URL, 'GET', data).then((result)=>{
        // console.log('tbody>>>>>',result)
        const res = JSON.parse(result);
        if(res.status == OK){
            const item = res.data;
            $("#" + objID +" tr").remove();  // thead 하위 <tr>개체 초기화
            let beforeRWP_NM = '';
            let itemSub = [];
            cntWork = item.length;
            if(cntWork > 0){
                
                item.map((itemRow, i)=>{
                    let rowTxt = '';
                    if(beforeRWP_NM != itemRow.RWP_NM2 ){
                        beforeRWP_NM = itemRow.RWP_NM2 ; 
                        itemSub = item.filter((data) => {
                            return beforeRWP_NM == data.RWP_NM2
                        });
                        rowTxt += "<tr id='tr_part_"+itemRow.RWP_ID+"' class='borderTh'>";
                        rowTxt += "<th class='text-center active' rowspan='"+itemSub.length+"'>"+itemRow.RWP_NM2+"</th>";
                        rowTxt += "<td class='text-left'>"+itemRow.RWP_NM1+"</td>";
                        rowTxt += "</tr>";
                    }else {
                        rowTxt += "<tr id='tr_part_"+itemRow.RWP_ID+"'>";
                        rowTxt += "<td class='text-left'>"+itemRow.RWP_NM1+"</td>";
                        rowTxt += "</tr>";
                    }
                    $("#" + objID).append(rowTxt);  
                    if(cntWork-1 == i){
                        setTable(item,CASE_CREATE);
                    }
                });
            }
        }
    });
}

/**
 * 메인 리스트 조회
 * @param {String} type  undefined || CASE_UPDATE
 */
getList = (type,req) => {
    let reqMode = req == undefined ? CASE_LIST : req ;
    const param ={
        REQ_MODE: reqMode,
        CPP_ID: ($('#P_CPP_ID').val() == '') ? $('#CPP_ID').val() : $('#P_CPP_ID').val() ,
        CPPS_ID: ($('#P_CPPS_ID').val() == '') ? $('#CPPS_ID').val() : $('#P_CPPS_ID').val() ,
        RRT_ID: ($('#P_RRT_ID').val() == '') ? $('#RRT_ID').val() : $('#P_RRT_ID').val() ,
    }

    callAjax(EXCHANGE_URL, 'GET', param).then((result)=>{
        const res = JSON.parse(result);
        if(res.status == OK){
            if($('#submitBtn').html()== '수정'){
                if(res.data[1] == NOT_EXIST) { //기준부품 없음
                    alert('저장된 기준부품 데이터가 없습니다. \n기준부품조견표를 등록해주세요.');
                    location.replace('../170_stdTable/stdTable.html');
                }else if(res.data[1] == HAVE_NO_DATA){//저장된데이터 없음
                    alert('저장된 환산부품조견표 데이터가 없습니다. \n수정을 통해 데이터를 저장해주세요.');
                }else {
                    console.log('ok or ......');
                }
            }
            setTable(res.data[0], type);
        }else{
            alert('기준부품조견표을 먼저 설정해주세요.');
        }
    });
}

/**
 *  테이블 셋팅 
 * @param {Array} arr 데이터 배열
 * @param {String} type undefined || CASE_UPDATE
 * */
setTable = (arr, type) => {
    let partTxt ='';
    $('table tr td[name=td_part]').remove(); //부품 td 초기화
    arr.map((data, index)=>{
        if(type == CASE_UPDATE){
            partTxt = "<td class='text-right' name='td_part'><input type='text' class='form-control text-right' id='td_part_"+data.RWP_ID+"_"+data.RCT_CD+"' name='td_part_"+data.RWP_ID+"_"+data.RCT_CD+"' value='"+data.NRPS_RATIO+"'></td>";
        }else if(data.RWP_RATIO == 0){
            partTxt = "<td class='text-right' name='td_part'>"+data.RWP_RATIO+"</td>";
            partTxt = partTxt.repeat( cntCar );
        }else {
            partTxt = "<td class='text-right' name='td_part'>"+data.NRPS_RATIO+"</td>";
        }
        $('#tr_part_'+data.RWP_ID).append(partTxt);
    });

   
}

/* 검색조건 체크 */
validationData = () => {
    if($('#CPP_ID').val() == ''){
        alert('기준부품을 선택해주세요.');
        return false;
    }else if($('#RRT_ID').val() ==''){
        alert('작업을 선택해주세요.');
        return false;
    }else if($('#CPPS_ID').val() ==''){
        alert('환산부품을 선택해주세요.');
        return false;
    }else {
        return true;
    }
}

/* 수정화면용으로 전환 */
setUpdateTable = () => {
    if(validationData()){
        getList(CASE_UPDATE);
        $('#resetBtn').show();
        $('#resetBtn').attr('onclick','getList('+CASE_UPDATE+','+CASE_TOTAL_LIST+')');
        $('#submitBtn').html('저장');
        $('#submitBtn').attr('onclick', 'setConfirm()');
        $('#CPP_ID').attr('disabled',true);
        $('#CPPS_ID').attr('disabled',true);
        $('#RRT_ID').attr('disabled',true);
    }

}

// resetTable = () => {
//     const param ={
//         REQ_MODE: CASE_TOTAL_LIST,
//         CPP_ID: $('#CPP_ID').val(), 
//         CPPS_ID: $('#CPPS_ID').val(), 
//         RRT_ID: $('#RRT_ID').val(), 
//     }
// }

/* 저장 확인 */
setConfirm = () => {
    confirmSwalTwoButton('환산부품조견표를 저장하시겠습니까?','확인', '취소', false ,()=>{
        setData();
    });
}

/* 데이터 저장 */
setData = () =>{
    let form = $("#partForm")[0];
    // console.log('form>>>',form);
    let formData = new FormData(form);
    formData.append('REQ_MODE', CASE_UPDATE);
    formData.append('CPP_ID', $('#CPP_ID').val());
    formData.append('CPPS_ID', $('#CPPS_ID').val());
    formData.append('RRT_ID', $('#RRT_ID').val());
    callFormAjax(EXCHANGE_URL, 'POST', formData).then((result)=>{
        console.log('setdata List>>',result);
        const res = JSON.parse(result);
        // console.log('set>>',res);
        if(res.status == OK){
            alert("저장되었습니다.");
            getList();
            $('#submitBtn').html('수정');
            $('#submitBtn').attr('onclick', 'setUpdateTable()');

            $('#CPP_ID').attr('disabled',false);
            $('#CPPS_ID').attr('disabled',false);
            $('#RRT_ID').attr('disabled',false);

        }else{
            alert("서버와의 연결이 원활하지 않습니다.");
        } 
      });
}

/* 엑셀 다운로드 */
excelDownload = () => {
    if(validationData()){
        const toURL="exchExcel.html?" +
        "REQ_MODE=" + CASE_LIST +
        "&CPP_ID=" + $('#CPP_ID').val() +
        "&CPPS_ID=" + $('#CPPS_ID').val() +
        "&RRT_ID=" + $('#RRT_ID').val() +
        "&CPP_NM=" + $('#CPP_ID option:selected').text() +
        "&CPPS_NM=" + $('#CPPS_ID option:selected').text() +
        "&RRT_NM=" + $('#RRT_ID option:selected').text();
    location.href=toURL;
    }
}
