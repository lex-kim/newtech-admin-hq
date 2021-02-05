const STANDARD_URL ='act/stdTable_act.php';

$(window).load(()=>{
    
});

$(document).ready(() =>{
    setColumn('tr_car');
    setRow('table_body');
    /* 기준부품 변경시 */
    $('#SELECT_PART').change(()=>{
        if($('#SELECT_WORK').val() != ''){
            // alert('gkgkgkgkgk');
            getList();
        }else {
            console.log('SELECT_PART change but SELECT_WORK don set ');
        }
    });

    /* 작업 변경시*/
    $('#SELECT_WORK').change(()=>{
        if($('#SELECT_PART').val() != ''){
            // alert('bnbnbnbnbnbnbn');
            getList();
        }else {
            console.log('SELECT_WORK change but SELECT_PART don set ');
        }
    });
    getList();
});

/**
 * 메인 리스트 조회
 * @param {String} type  undefined || CASE_UPDATE
 */
getList = (type) => {
    const param ={
        REQ_MODE: CASE_LIST,
        CPP_ID: $('#SELECT_PART').val(),
        RRT_ID: $('#SELECT_WORK').val()
    }
    console.log('getList param >>', param);

    callAjax(STANDARD_URL, 'GET', param).then((result)=>{
        console.log('getList Query result>>', result);
        const res = JSON.parse(result);
        console.log(res);
        if(res.status == OK){
            setTable(res.data, type);

        }else{
            alert('데이터 조회에 실패하였습니다. 관리자에게 문의해주세요.');
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
    callAjax(STANDARD_URL, 'GET', data).then((result)=>{
        // console.log('table_hedder >>',result)
        const res = JSON.parse(result);
        if(res.status == OK){
            const item = res.data;
            $("#" + objID +" th").remove();  // thead 하위 <tr>개체 초기화
            if(item.length > 0){
                $('#td_part_header').attr("colspan", item.length);
                item.map((item, i)=>{
                    headerTxt += "<th class='text-center'>"+item.RCT_NM+"</th>";
                })

                $("#" + objID).append(headerTxt);  // <SELECT> 객체에 반영

                setPartOptions('SELECT_PART','=기준=');
                setWorkOptions('SELECT_WORK','=작업=');
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
   
    callAjax(STANDARD_URL, 'GET', data).then((result)=>{
        // console.log('tbody>>>>>',result)
        const res = JSON.parse(result);
        if(res.status == OK){
            const item = res.data;
            $("#" + objID +" tr").remove();  // thead 하위 <tr>개체 초기화
            let beforeRWP_NM = '';
            let itemSub = [];
            if(item.length > 0){
                
                item.map((itemRow, i)=>{
                    let rowTxt = '';
                    if(beforeRWP_NM != itemRow.RWP_NM2 ){
                        beforeRWP_NM = itemRow.RWP_NM2 ; 
                        itemSub = item.filter((data) => {
                            return beforeRWP_NM == data.RWP_NM2
                        });
                        rowTxt += "<tr id='tr_part_"+itemRow.RWP_ID+"' class='borderTh'>";
                        rowTxt += "<th class='text-center active' rowspan='"+itemSub.length*2+"'>"+itemRow.RWP_NM2+"</th>";
                        rowTxt += "<th class='text-left active' rowspan='2'>"+itemRow.RWP_NM1+"</td>";
                        rowTxt += "<td class='text-center'>기준부품</td>";
                        rowTxt += "</tr>";
                        rowTxt += "<tr id='tr_time_"+itemRow.RWP_ID+"'>";
                        rowTxt += "<td class='text-center'>소요시간</td>";
                        rowTxt += "</tr>";
                    }else {
                        rowTxt += "<tr id='tr_part_"+itemRow.RWP_ID+"'>";
                        rowTxt += "<th class='text-left active' rowspan='2'>"+itemRow.RWP_NM1+"</td>";
                        rowTxt += "<td class='text-center'>기준부품</td>";
                        rowTxt += "</tr>";
                        rowTxt += "<tr id='tr_time_"+itemRow.RWP_ID+"'>";
                        rowTxt += "<td class='text-center'>소요시간</td>";
                        rowTxt += "</tr>";
                    }
                    $("#" + objID).append(rowTxt);  
                });
            }
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
    let timeTxt ='';
    $('table tr td[name=td_part]').remove(); //기준부품 td 초기화
    $('table tr td[name=td_time]').remove(); //소요시간 td 초기화
    arr.map((data, index)=>{
        // console.log('arr',index,' ',data);
        if(type == CASE_UPDATE){
            partTxt = "<td class='text-right' name='td_part'><input type='text' class='form-control text-right' onkeypress='return isFloat(event)' id='td_part_"+data.RWP_ID+"_"+data.RCT_CD+"' name='td_part_"+data.RWP_ID+"_"+data.RCT_CD+"' value='"+data.NRP_RATIO+"'></td>";
            timeTxt = "<td class='text-right' name='td_time'><input type='text' class='form-control text-right' onkeypress='return isFloat(event)' id='td_time_"+data.RWP_ID+"_"+data.RCT_CD+"' name='td_time_"+data.RWP_ID+"_"+data.RCT_CD+"' value='"+data.NRT_TIME_M+"'></td>";
        }else{
            partTxt = "<td class='text-right' name='td_part'>"+data.NRP_RATIO+"</td>";
            timeTxt = "<td class='text-right' name='td_time'>"+data.NRT_TIME_M+"</td>";
        }
        $('#tr_part_'+data.RWP_ID).append(partTxt);
        $('#tr_time_'+data.RWP_ID).append(timeTxt);
    });
}

/* 검색조건 체크 */
validationData = () => {
    if($('#SELECT_PART').val() == ''){
        alert('기준부품을 선택해주세요.');
        return false;
    }else if($('#SELECT_WORK').val() ==''){
        alert('작업을 선택해주세요.');
        return false;
    }else {
        return true;
    }
}

/* 수정화면용으로 전환 */
setUpdateTable = () => {
    if(validationData()){
        getList(CASE_UPDATE);
        $('#submitBtn').html('저장');
        $('#submitBtn').attr('onclick', 'setConfirm()');
        $('#SELECT_PART').attr('disabled',true);
        $('#SELECT_WORK').attr('disabled',true);
    }
    
}

/* 저장 확인 */
setConfirm = () => {
    confirmSwalTwoButton(' 기준부품 및 방청시간 조견표를 저장하시겠습니까?','확인', '취소', false ,()=>{
        setData();
    });
}

/* 데이터 저장 */
setData = () =>{
    let form = $("#partForm")[0];
    console.log('form>>>',form);
    let formData = new FormData(form);
    formData.append('REQ_MODE', CASE_UPDATE);
    formData.append('CPP_ID', $('#SELECT_PART').val());
    formData.append('RRT_ID', $('#SELECT_WORK').val());
    callFormAjax(STANDARD_URL, 'POST', formData).then((result)=>{
        const res = JSON.parse(result);
        console.log('set>>',res);
        if(res.status == OK){
            alert("저장되었습니다.");
            getList();
            $('#submitBtn').html('수정');
            $('#submitBtn').attr('onclick', 'setUpdateTable()');

            $('#SELECT_PART').attr('disabled',false);
            $('#SELECT_WORK').attr('disabled',false);

        }else{
            alert("서버와의 연결이 원활하지 않습니다.");
        } 
      });
}

/* 기준부품조견표 엑셀 다운로드 */
excelDownloadPart = () => {
    if(validationData()){
        const toURL="stdTableExcel.html?" +
        "REQ_MODE=" + CASE_LIST +
        "&CPP_ID=" + $('#SELECT_PART').val() +
        "&RRT_ID=" + $('#SELECT_WORK').val();
    location.href=toURL;
    }
    
}

/* 방청시간조견표 엑셀 다운로드 */
excelDownloadTime = () => {
    if(validationData()){
        const toURL="timeTableExcel.html?" +
        "REQ_MODE=" + CASE_LIST +
        "&CPP_ID=" + $('#SELECT_PART').val() +
        "&RRT_ID=" + $('#SELECT_WORK').val();
    location.href=toURL;
    }
    
}