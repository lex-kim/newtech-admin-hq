let arrDepotitDetail = [];
let arrExclude = [];
let arrDecrement = [];

/* 보험사Team 동적 생성 */
setInsuTeam = () =>{
    const data = {
      REQ_MODE: GET_INSU_TEAM,
    };
    callAjax(DEPOSIT_URL, 'GET', data).then((result)=>{
        const res = JSON.parse(result);
        if(res.status == OK){
            const dataArray = res.data; 
              
            let optionText = '';
            dataArray.map((item)=>{
                optionText += '<option value="'+item.MIT_ID+'">'+item.MIT_NM+'</option>';
            });
            $('#SEARCH_INSU_TEAM').html(optionText);
            $('#SEARCH_INSU_TEAM').selectpicker('refresh');
          
        }else{
            alert("서버와의 연결이 원활하지 않습니다.");
        }
    });
}


/* 보상담당 동적 생성 */
setInsuFao = () =>{
    const data = {
      REQ_MODE: GET_INSU_TYPE,
    };
    callAjax(DEPOSIT_URL, 'GET', data).then((result)=>{
        const res = JSON.parse(result);
        // console.log('FAO result>>',res);
        if(res.status == OK){
            const dataArray = res.data; 
              
            let optionText = '';
            dataArray.map((item)=>{
                optionText += '<option value="'+item.NIC_ID+'">'+item.NIC_NM_AES+'</option>';
            });
            $('#INSU_FAO_SEARCH').html(optionText);
            $('#INSU_FAO_SEARCH').selectpicker('refresh');
          
        }else{
            alert("서버와의 연결이 원활하지 않습니다.");
        }
    });
}


/* 청구요청자 동적 생성 */
setClaimId = () =>{
    const data = {
      REQ_MODE: GET_REF_CLAIMANT_TYPE,
    };
    callAjax(DEPOSIT_URL, 'GET', data).then((result)=>{
        const res = JSON.parse(result);
        // console.log('FAO result>>',res);
        if(res.status == OK){
            const dataArray = res.data; 
              
            let optionText = '';
            dataArray.map((item)=>{
                optionText += '<option value="'+item.NIC_ID+'">'+item.NIC_NM_AES+'</option>';
            });
            $('#INSU_FAO_SEARCH').html(optionText);
          
        }else{
            alert("서버와의 연결이 원활하지 않습니다.");
        }
    });
}


/* CAR TYPE SELECT */
setCarType = () => {
    const data = {
        REQ_MODE: GET_CAR_TYPE
    };
    let headerTxt = '';
    callAjax(DEPOSIT_URL, 'GET', data).then((result)=>{
        // console.log('table_hedder >>',result)
        const res = JSON.parse(result);
        if(res.status == OK){
            const item = res.data;
            $("#CCC_ID_SEARCH option").remove();  // thead 하위 <tr>개체 초기화
            if(item.length > 0){
                item.map((item, i)=>{
                    headerTxt += '<option value="'+item.RCT_CD+'">'+item.RCT_NM+'</option>';
                })

                $("#CCC_ID_SEARCH").html('<option value="">=차량급=</option>'+headerTxt);  // <SELECT> 객체에 반영

                
            
            }
        }
    });
}



/* 사용부품 */
setPart = () => {
    const data = {
        REQ_MODE: GET_PARTS_TYPE
    };
    let headerTxt = '';
    callAjax(DEPOSIT_URL, 'GET', data).then((result)=>{
        // console.log('table_hedder >>',result)
        const res = JSON.parse(result);
        if(res.status == OK){
            const item = res.data;
            $("#CPPS_ID_SEARCH option").remove();  // thead 하위 <tr>개체 초기화
            if(item.length > 0){
                item.map((item, i)=>{
                    headerTxt += '<option value="'+item.CPPS_NM_SHORT+'">'+item.CPPS_NM_SHORT+'</option>';
                })
                $("#CPPS_ID_SEARCH").html('<option value="">=사용부품=</option>'+headerTxt);  // <SELECT> 객체에 반영            
            }
        }
    });
}



/**
 * 입금여부 상세 동적생성
 */
setDepoitSearch = () => {
    const param = {
        REQ_MODE : REF_DEPOSIT_TYPE
    }
    callAjax(DEPOSIT_URL, 'GET', param).then((result)=>{
        // console.log('getDepoitDetail SQL >>',result);
        const res = JSON.parse(result);
        // console.log('getDepoitDetail>>',res);
        if(res.status == OK){
            arrDepotitDetail = res.data; 
            let optionText = '';
            $('#DEPOSIT_DETAIL_YN_SEARCH option').remove();
            arrDepotitDetail.map((item)=>{
                optionText += '<option value="'+item.ID+'">'+item.NM+'</option>';
            });
            $("#DEPOSIT_DETAIL_YN_SEARCH").append(optionText);  // <SELECT> 객체에 반영            
            $("#DEPOSIT_DETAIL_YN_SEARCH").selectpicker('refresh');
            
        }else{
            alert(ERROR_MSG_1100);
        }
    });
}

let arrReason = [];
/* 사유입력 검색 */
setReasonSearch = async () => {
    
    getExcludeList();
    getDecrementList();

    setTimeout(function() {
        console.log('arrReason 1>' ,arrExclude);
        arrExclude.push({
            ID: '',
            NM: '---------------------------------------------------',
            EXT1_YN: 'N',
            EXT2_YN: 'N',
            EXT3_YN: 'N',
            EXT4_YN: 'N',
            EXT5_YN: 'N'
        });
        arrReason = arrExclude.concat(arrDecrement);
        console.log('arrReason 2>' ,arrReason);
        let optionText = '';
        $('#NBI_REASON_DTL_ID_SEARCH option').remove();
        arrReason.map((item,index)=>{
            optionText += '<option value="'+item.ID+'">'+item.NM+'</option>';
        })
        $('#NBI_REASON_DTL_ID_SEARCH').append(optionText);
        $("#NBI_REASON_DTL_ID_SEARCH").selectpicker('refresh');

    }, 1500)
    
}


/**
 * 입금여부 동적생성
 * @param {String} objId select tag id
 * @param {String} pSelectedVal select tag value 
 */
getDepoiteeType = (objId, pSelectedVal) => {
    const param = {
        REQ_MODE: GET_REF_OPTION,
        REF_TYPE : REF_DEPOSITEE_TYPE
    }
    callAjax('/common/gwReference.php', 'GET', param).then((result)=>{
        // console.log('getDepositeee SQL >>',result);
        const res = JSON.parse(result);
        // console.log('getDepositee>>',res);
        if(res.status == OK){
            const dataArray = res.data; 
            //   console.log('getDepositType >>',dataArray) ;
            let optionText = '';
            dataArray.map((item)=>{
                optionText += '<label class="radio-inline"><input type="radio" name="RADIO_DEPOSIT" onclick="showDepositDetail()" value="'+item.ID+'" >'+item.NM+'</label>';
            });
            $('#'+objId).html(optionText);

            if(pSelectedVal != undefined || pSelectedVal != null) {
                $("input[value='"+pSelectedVal+"']").attr('checked', true);
            }
        }else{
            alert(ERROR_MSG_1100);
        }
    });
}

/**
 * 입금여부 상세 동적생성
 * @param {String} objId select tag id
 * @param {String} pSelectedVal select tag value 
 */
getDepoitDetail = (objId, pSelectedVal) => {
    // console.log('getDepoitDetail>>' ,pSelectedVal);
    const param = {
        REQ_MODE : REF_DEPOSIT_TYPE
    }
    callAjax(DEPOSIT_URL, 'GET', param).then((result)=>{
        // console.log('getDepoitDetail SQL >>',result);
        const res = JSON.parse(result);
        // console.log('getDepoitDetail>>',res);
        if(res.status == OK){
            arrDepotitDetail = res.data; 
            let optionText = '';
            arrDepotitDetail.map((item)=>{
                optionText += '<label class="radio-inline"><input type="radio" name="RADIO_DEPOSIT_DETAIL" onclick="changeDepositDetail()" value="'+item.ID+'" readonly>'+item.NM+'</label>';
            });
            $('#'+objId).html(optionText);

            if(pSelectedVal != undefined) {
                $("input[value='"+pSelectedVal+"']").attr('checked', true);
            }else{
                $("input[value='40111']").attr('checked', true);
            }
        }else{
            alert(ERROR_MSG_1100);
        }
    });
}


/**
 * 지급제외 사유  
 * @param {String} pSelectedVal select tag value 
 */
getExcludeList = (pSelectedVal) => {
    const param = {
        REQ_MODE : REF_EXCLUDE_REASON
    }
    callAjax(DEPOSIT_URL, 'GET', param).then((result)=>{
        // console.log('getExcludeList SQL >>',result);
        const res = JSON.parse(result);
        // console.log('getExcludeList>>',res);
        if(res.status == OK){
            arrExclude = res.data; 
            let optionText = '';
            $('#REASON_SELECT option').remove();
            arrExclude.map((item)=>{
                optionText += '<option value="'+item.ID+'" >'+item.NM+'</option>';
            });
            $('#REASON_SELECT').append('<option value="" selected>=사유입력=</option>'+optionText);
            // $('#SEARCH_INSU_TEAM').selectpicker('refresh');
            if(pSelectedVal != undefined) {
                $("#REASON_SELECT").val(pSelectedVal);
                
            }
        }else{
            alert(ERROR_MSG_1100);
        }
    });
}


/**
 * 감액 사유
 * @param {String} pSelectedVal select tag value 
 */
getDecrementList = (pSelectedVal) => {
    const param = {
        REQ_MODE : REF_DECREMENT_REASON
    }
    callAjax(DEPOSIT_URL, 'GET', param).then((result)=>{
        // console.log('getExcludeList SQL >>',result);
        const res = JSON.parse(result);
        // console.log('getExcludeList>>',res);
        if(res.status == OK){
            arrDecrement = res.data; 
            let optionText = '';
            $('#REASON_SELECT option').remove();
            arrDecrement.map((item)=>{
                optionText += '<option value="'+item.ID+'" >'+item.NM+'</option>';
            });
            $('#REASON_SELECT').append('<option value="" selected>=사유입력=</option>'+optionText);
            // $('#SEARCH_INSU_TEAM').selectpicker('refresh');
            if(pSelectedVal != undefined) {
                $("#REASON_SELECT").val(pSelectedVal);
            }
        }else{
            alert(ERROR_MSG_1100);
        }
    });
}

/** 타보험사
 * @param {String} objId select tag id
 * @param {String} pSelectedVal select tag value 
 */
setOtherIw = (objID, pSelectedVal) =>{
    const data = {
        REQ_MODE: OPTIONS_IW,
    };
    callAjax('/common/searchOptions.php', 'GET', data).then((result)=>{
        const res = JSON.parse(result);
        let optionHtml = '';

        $('#'+objID+' option').remove();
        if(res != undefined || res.length > 0){
            res.map((item, index)=>{
                optionHtml += "<option value="+item.MII_ID+">"+item.MII_NM+"</option>";
            });
        }
        
        $("#" + objID).append('<option value="" selected>=선택=</option>'+optionHtml); 

        if(pSelectedVal != undefined) {
            $("#" + objID).val(pSelectedVal);
        }
    });
}

/**
 * 판금/교환 및 우선순위 작업항목 REF 가져오기
 * @param {REQ_MODE : 우선인지 대체/체화인지 바닥인지}
 * @param {objID : select tag id}
 */
getRefInfo = (REQ_MODE, objID) => {
    const data = {
        REQ_MODE: REQ_MODE
    };
    callAjax('/100_code/151_workpart/act/workpart_act.php', 'POST', data).then((result)=>{
        let optionText = '';
       
        const data = JSON.parse(result);
        if(data.status == OK){
            const  itemArray = data.data;
            itemArray.map((item)=>{
                optionText += '<option value="'+item.ID+'" >'+item.NM+'</option>';
            });
            $("#" + objID).append('<option value="" selected>=교환/판금=</option>'+optionText); 
            
        }else{
            alert('교환/판금 데이터를 불러오는데 실패했습니다.');
        }
            
    
    });
}



/** 중복해제 */
function setReleaseDuplicateBill(NB_ID){
    const data = {
        REQ_MODE: CASE_CONFLICT_CHECK,
        NB_ID : NB_ID
    };
  
    callAjax('/200_billMngt/220_sendList/act/sendList_act.php', 'POST', data).then((result)=>{
        if(isJson(result)){
            const data = JSON.parse(result);
            if(data.status == OK){
                getList();
            }else{
                alert(ERROR_MSG_1100);
            }
        }else{
            alert(ERROR_MSG_1200);
        }
    });
}