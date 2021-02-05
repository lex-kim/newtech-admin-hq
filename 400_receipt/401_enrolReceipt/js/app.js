ENROLL_URL = 'act/enrolReceipt_act.php';
$(document).ready(function() { 
    getRegion(); // 지역 검색
    getGarage(); //공업사 명칭 검색
    getSettingData(); //  정산정보 설정
   
    $('#NIH_DT').datepicker({
        dateFormat : 'yy-mm-dd',
    });

    $('#DIV_MGG_NM').show();
    setInitTable();
    $('#submitBtn').hide();

    if( $('#type').val() == 'update'){
        $('#REQ_MODE').val(CASE_UPDATE);
        $('#NIH_DT').val($('#nih').val());
        $('#RST_ID').val($('#rst').val());
        getMGGData($('#mgg').val(), $('#nm').val());
    }else if($('#type').val() == 'trans'){
        $('#REQ_MODE').val(CASE_CREATE);
        $('#NIH_DT').val(dateFormat(new Date()));
        getMGGData($('#mgg').val(), $('#nm').val());
    }else {
        $('#REQ_MODE').val(CASE_CREATE);
        $('#NIH_DT').val(dateFormat(new Date()));
    }

    $("#enrollForm").submit(function(e){
        e.preventDefault();
        
        let formData = $('#enrollForm').serialize();
        confirmSwalTwoButton('입출고 저장 하시겠습니까?','확인', '취소', false ,()=>addData(formData));
    });
});


setInitTable = () => {
    const tableText = "<tr><td class='text-center' colspan='8'>좌측 검색을 이용하여 공업사를 선택해주세요.</td></tr>";
    $('#dataTable').html(tableText);
}

/* 지역조회*/
getGarage = () => {
    
    const data = {
      REQ_MODE: OPTIONS_GW
    }
    
    callAjax(ENROLL_URL, 'POST', data).then((result)=>{
        console.log('getGarage SQL >',result);
        const res = JSON.parse(result);
        console.log('getGarage DATA>>',res);
        if(res.status == OK){
            const dataArray = res.data;
            let optionText ='';
            if(dataArray.length > 0){
            
                // dataArray.map((item, index)=>{
                //     optionText += '<option value="'+MGG_ID+'">'+item.MGG_NM+'</option>';
               
                // });
                // autoCompleteCRR('MGG_NM_SEARCH', 'SELECT_MGG_ID', dataArray);  
                $("#MGG_NM_SEARCH").autocomplete({
                    minLength : 1,
                    source: dataArray.map((data, index) => {
                        return {
                            lable : data.MGG_ID,
                            value : data.MGG_NM
                        };
                    }),
                    focus: function(event, ui){ 
                        return false;
                    },
                    select: function(event, ui) {
                        let strArea = ui.item.value;
                        $('#SELECT_MGG_ID').val(ui.item.lable);    //선택한 데이터 value를 저장할곳(hidden)
                        $("#MGG_NM_SEARCH").val(strArea);          //선택한 주소를 보여줄 input
                        return false;
                    }
                }).on('focus', function(){ 
                    $(this).autocomplete("search"); 
                });

            }else{
                resetCRRValue('MGG_NM_SEARCH', 'SELECT_MGG_ID');
            }
        }else{
            console.log('err');
        }  
    });
  
}


/* 지역조회*/
getRegion = () => {
    
    const data = {
      REQ_MODE: GET_REQUEST_REGION
    }
    callAjax(ENROLL_URL, 'POST', data).then((result)=>{
        // console.log('getRegion SQL >',result);
        const res = JSON.parse(result);
        // console.log('getRegion DATA>>',res);
        if(res.status == OK){
            const dataArray = JSON.parse(res.data);
            let optionText ='';
            if(dataArray.length > 0){
            
                dataArray.map((item, index)=>{
                    let ITEM_ID = item.CRR_1_ID+","+item.CRR_2_ID+","+item.CRR_3_ID;
                    optionText += '<option value="'+ITEM_ID+'">'+item.CRR_NM+'</option>';
               
                });
                autoCompleteCRR('SEARCH_REGION', 'SELECT_CRR_ID', dataArray);  

            }else{
                resetCRRValue('SEARCH_REGION', 'SELECT_CRR_ID');
            }
        }else{
            console.log('err');
        }  
    });
  
}

/* 정산정보 */
getSettingData = () => {
    const param = {
        REQ_MODE:  CASE_READ,
        REQ_TARGET:  STOCK_BILL,
    }
    callAjax(ENROLL_URL, 'GET', param).then((result)=>{
        // console.log('getSettingDATA SQL >> ');
        // console.log(result);
        const res = JSON.parse(result);
        if(res.status == OK){
            // console.log('getSettingData res >',res);
            const resData = res.data;
            $('#NIE_BILL_VS_STOCK_RATIO').val(resData[0]);
            $('#NIE_INDEPENDENT_STOCK_RATIO').val(resData[1]);
            getSubstitution(resData[2]);
            getRST(resData[3]);

        }else{
            alert(ERROR_MSG_1200);
        } 

    });
}

/** 
 * 대체정산식 출력 
 * @param {array} arrData : 대체정산식 list 
*/
getSubstitution = (arrData) => {
    let tableTxt = '';
    $('#SELECT_SUBSTITUTION option').remove();
    arrData.map((arr, i)=>{
        // console.log(i, 'getSubstitution>>',arr);
        tableTxt += "<option value='"+arr.NIS_FROM_CPPS_ID+'_'+arr.NIS_TO_CPPS_ID+'_'+arr.NIS_RATIO+"'>"
        tableTxt += arr.NIS_FROM_CPPS_NM+' 대체정산식: ';
        tableTxt += arr.NIS_TO_CPPS_NM+' = '+arr.NIS_TO_CPPS_NM+" + ";
        tableTxt += "("+arr.NIS_FROM_CPPS_NM+" X "+arr.NIS_RATIO+")";
        tableTxt += "</option>";
        
    })
    $('#SELECT_SUBSTITUTION').html(tableTxt);
}

/** 
 * 출거/수거 출력
 * @param {array} resData : 출거 / 수거  list 
*/
getRST = (resData) => {

    let optionTxt = '';
    // $('#RST_ID option').remove();
    resData.map((data, i)=>{
        optionTxt += "<option value='"+data.RST_ID+"'>"+data.RST_NM+"</option>";
    })
    $('#RST_ID').html(optionTxt);
}

/* 공업사 검색 */
searchGW = () => {
    $('#DIV_MGG_NM').hide();

    const data = {
        REQ_MODE: GET_GARGE_INFO,
        SELECT_CRR_ID: $('#SELECT_CRR_ID').val(),
        SEARCH_REGION: $('#SEARCH_REGION').val(),
        MGG_NM_SEARCH: $('#MGG_NM_SEARCH').val()
    }
    callAjax(ENROLL_URL, 'POST', data).then((result)=>{
        // console.log('getRegion SQL >',result);
        const res = JSON.parse(result);
        // console.log('getRegion DATA>>',res);
        $('#optionGw li').remove();
        if(res.status == OK){
            const dataArray = res.data;
            let optionText ='';
            if(dataArray.length > 0){
            
                dataArray.map((item, index)=>{
                    // console.log( index , 'gw >',item);
                    let ITEM_ID = item.CRR_1_ID+","+item.CRR_2_ID+","+item.CRR_3_ID;
                    optionText += "<li onclick='getMGGData(\""+item.MGG_ID+"\",\""+item.MGG_NM+"\")'>"+item.MGG_NM+"</li>";
                
                });
                $('#optionGw').append(optionText);
            }else{
                $('#optionGw').append('<li class="form-inline"><label for="area1">조회된 공업사가 없습니다.</label></li>');
            }
        }else{
            // console.log('err');
            $('#optionGw').append('<li class="form-inline"><label for="area1">조회된 공업사가 없습니다.</label></li>');

        }  
    });
}

/**
 * 공업사 정보조회
 * @param {String} mggId : 공업사 ID
 * @param {String} mggNm : 공업사 NM
 */
getMGGData = (mggId, mggNm) => {
    $('#MGG_ID').val(mggId);
    $('#MGG_NM').val(mggNm);
    $('#optionGw li').remove();
    $('#optionGw').append('<li class="form-inline"><label for="area1">'+mggNm+'</label></li>');
    const data = {
        REQ_MODE: CASE_READ,
        MGG_ID: mggId
      }
      callAjax(ENROLL_URL, 'POST', data).then((result)=>{
        //   console.log('getMGGData SQL >',result);
          const res = JSON.parse(result);
        //   console.log('getMGGData DATA>>',res);
          $('#dataTable tr').remove();
          let tableText = "<tr><td class='text-center' colspan='8'>해당 공업사에 대한 정보가 없습니다.</td></tr>";
          if(res.status == OK){
                const dataArray = res.data;
                if(dataArray.length > 0){
                    setTable(dataArray);           
                }else{
                        // console.log('res.data is null');
                      $('#dataTable').append(tableText);
                }
          }else{
            // console.log('res is not OK');
            $('#dataTable').append(tableText);
          }  
      });
}

/**
 * 테이블 출력
 * @param {array} dataArray 
 */
setTable = (dataArray)=>{
    let tableTxt = '';
    let beforeCPP_ID = '';
    let itemSub = [];
    let btnPart ='';
    let btnUse ='';
    let colorStyl = '';
    let inputStyl = '';
    dataArray.map((item, index)=>{
        // console.log('dataArr > ',item);
        if(item.PART_YN == YN_Y){
            btnPart = "<button type='button' tabindex='100'  class='btn btn-success' onclick='partSetting(\""+PART+"\","+JSON.stringify(item)+")'>설정</button>";
            colorStyl = "style='background-color: #dff0d8;'";
            inputStyl = "";
        }else {
            colorStyl = "";
            btnPart = "<button type='button' tabindex='100'  class='btn btn-default' onclick='partSetting(\""+PART+"\","+JSON.stringify(item)+")'>설정</button>";
            inputStyl = "disabled";
        }
        if(item.NGP_DEFAULT_YN == YN_Y){
            btnUse = "<button type='button' tabindex='200'  class='btn btn-success' onclick='partSetting(\""+PART_DEFAULT+"\","+JSON.stringify(item)+")'>설정</button>";
        }else {
            btnUse ="<button type='button' tabindex='200'  class='btn btn-default' onclick='partSetting(\""+PART_DEFAULT+"\","+JSON.stringify(item)+")'>설정</button>";
        }

        // console.log('beforeCPP_ID:',beforeCPP_ID,' nm:', item.CPP_ID);
        if(beforeCPP_ID != item.CPP_ID ){
            beforeCPP_ID = item.CPP_ID ; 
            // console.log('change',beforeCPP_ID) ; 
            itemSub = dataArray.filter((data) => {
                // console.log('filter'data.CPP_ID);
                return beforeCPP_ID == data.CPP_ID
            });
            tableTxt += "<tr>";
            tableTxt += "<td rowspan='"+itemSub.length+"'>"+item.CPP_NM_SHORT+"</td>";
            tableTxt += "<td>"+item.CPPS_NM+"</td>";
            tableTxt += "<td class='text-left'>"+item.NIS_AMOUNT+"</td>";
            tableTxt += "<td class='text-center'>"+btnPart+"</td>";
            tableTxt += "<td class='text-center'>"+btnUse+"</td>";
            tableTxt += "<td rowspan='"+itemSub.length+"'>"+item.CPP_NM_SHORT+"</td>";
            tableTxt += "<td "+colorStyl+">"+item.CPPS_NM+"</td>";
            tableTxt += "<td><input type='text' tabindex=0  class='form-control' id='"+item.CPPS_ID+"' name='"+item.CPPS_ID+"' onkeypress='return isFloat(event)'"+inputStyl+"></td>";
            tableTxt += "</tr>";
        }else {
            tableTxt += "<tr>";
            tableTxt += "<td>"+item.CPPS_NM+"</td>";
            tableTxt += "<td class='text-left'>"+item.NIS_AMOUNT+"</td>";
            tableTxt += "<td class='text-center'>"+btnPart+"</td>";
            tableTxt += "<td class='text-center'>"+btnUse+"</td>";
            tableTxt += "<td "+colorStyl+">"+item.CPPS_NM+"</td>";
            tableTxt += "<td><input type='text' tabindex=0 class='form-control' id='"+item.CPPS_ID+"' name='"+item.CPPS_ID+"' onkeypress='return isFloat(event)'"+inputStyl+"></td>";
            tableTxt += "</tr>";
        }
       
    
    });
    $('#dataTable').append(tableTxt);
    $('#submitBtn').show();

    if($('#REQ_MODE').val() == CASE_UPDATE){
        $('#submitBtn').html('수정');
        getData($('#MGG_ID').val(), $('#RST_ID').val(), $('#NIH_DT').val());
    }

} 

/**
 * 공급부품 설정 및 사용부품 설정
 * @param {String} type: part-공급부품 / default-사용부품
 * @param {array} item: 선택한 행 데이터
 */
partSetting = (type, item) => {
   
    /* 입력된 수량이 있는지 확인 */
    let arrAmount = [];
    let fileValue = $("input[name='inputAmount']").length;
    let fileData = new Array(fileValue);
    for(let i=0; i<fileValue; i++){                          
         fileData[i] = $("input[name='inputAmount']")[i].value;
    }
    arrAmount = fileData.filter((data) => {
        return data != ''
    });
    

    if(arrAmount.length > 0){
        alert('입력하신 수량을 저장하신 후 설정해주세요.');
        return false;
    }else {
        let partNM = '';
        let settingVal = '';
        if(type == PART){
            partNM = '공급부품';
            if(item.PART_YN == YN_Y){
                settingVal = YN_N;
            }else {
                settingVal =YN_Y;
            }
        }else if(type == PART_DEFAULT){
            partNM = '사용부품';
            if(item.NGP_DEFAULT_YN == YN_Y){
                settingVal = YN_N;
            }else {
                if(item.PART_YN == YN_N){
                    alert('공급부품 설정이 되어있지 않습니다.');
                    return false ;
                }else {
                    settingVal = YN_Y;
                }
            }
        }else {
            alert(ERROR_MSG_1400);
        }
        
        const param ={
            REQ_MODE: CASE_UPDATE,
            MGG_ID: $('#MGG_ID').val(),
            UPDATE_KIND: type,
            SET_VALUE: settingVal,
            CPP_ID: item.CPP_ID,
            CPPS_ID: item.CPPS_ID,
            TYPE: $('#type').val(),
        }
        confirmSwalTwoButton(
            item.CPP_NM_SHORT+' - '+item.CPPS_NM+'의 '+partNM+' \n 설정을 변경 하시겠습니까?',
            '확인', 
            '취소', 
            false,
            ()=>{
                callAjax(ENROLL_URL, 'post', param).then((result)=>{
                    //   console.log('getMGGData SQL >',result);
                    const res = JSON.parse(result);
                    //   console.log('getMGGData DATA>>',res);
                    if(res.status == OK){   
                        alert('설정이 변경되었습니다.');
                        getMGGData($('#MGG_ID').val(), $('#MGG_NM').val());
                    }else{
                        alert('설정 변경에 실패하였습니다. 재시도해주세요.');
                    }  
                });
            },
            type == PART_DEFAULT ? '기존 사용부품 존재시 해제되고 해당부품으로 설정됩니다.' : ''
        );
    }

}


/**
 * 등록
 * @param {array} param formData
 */
addData = (param) => {
    callAjax(ENROLL_URL, 'post', param).then((result)=>{
        console.log('addData SQL', result);
        const res = JSON.parse(result);
        if(res.status == OK){   
            alert(SAVE_SUCCESS_MSG);
            self.close();
            window.opener.getList();
        }else if(res.status == CONFLICT){
            alert('이미 등록한 데이터가 존재합니다. \n공업사입출고관리 > 수정에서 진행해주세요.');
            self.close();
            opener.location.replace('../450_mngtReceipt/mngtReceipt.html');
        }else{
            alert(res.message);
            
        }  
    });

}

/* 독립정산 적용 */
setIndependent = () => {
    const param ={
        REQ_MODE: STOCK_INDE,
        MGG_ID: $('#MGG_ID').val(),
        NIE_INDEPENDENT_STOCK_RATIO: $('#NIE_INDEPENDENT_STOCK_RATIO').val()
    }
    confirmSwalTwoButton(
        $('#MGG_NM').val()+' 독립정산을 진행 하시겠습니까?',
        '확인', 
        '취소', 
        false,
        ()=>{
            callAjax(ENROLL_URL, 'post', param).then((result)=>{
                //   console.log('getMGGData SQL >',result);
                const res = JSON.parse(result);
                //   console.log('getMGGData DATA>>',res);
                if(res.status == OK){   
                    alert('정산이 적용되었습니다.');
                    getMGGData($('#MGG_ID').val(), $('#MGG_NM').val());
                }else{
                    alert('정산에 실패하였습니다. 재시도해주세요.');
                    
                }  
            });
        }
    );
}

/* 대체정산 적용 */
setSubstitution = () => {
    const param ={
        REQ_MODE: STOCK_SUBST,
        MGG_ID: $('#MGG_ID').val(),
        NIE_INDEPENDENT_STOCK_RATIO: $('#NIE_INDEPENDENT_STOCK_RATIO').val(),
        NIE_SUBSTITUTION: $('#SELECT_SUBSTITUTION').val()
    }
    console.log('param>>', param);
    confirmSwalTwoButton(
        $('#MGG_NM').val()+' 대체정산을 진행 하시겠습니까?',
        '확인', 
        '취소', 
        false,
        ()=>{
            callAjax(ENROLL_URL, 'post', param).then((result)=>{
                  console.log('getMGGData SQL >',result);
                const res = JSON.parse(result);
                //   console.log('getMGGData DATA>>',res);
                if(res.status == OK){   
                    alert('정산이 적용되었습니다.');
                    getMGGData($('#MGG_ID').val(), $('#MGG_NM').val());
                }else{
                    alert('정산 변경에 실패하였습니다. 재시도해주세요.');
                }  
            });
        }
    );
}



/** 수정할때 정보 가져오기 */
getData = (mgg, rst, nih) => {
    const param = {
        REQ_MODE : CASE_LIST_DETAIL,
        MGG_ID : mgg,
        RST_ID : rst,
        NIH_DT : nih,
    };
    console.log('getdata param>>',param);
    callAjax(ENROLL_URL, 'GET', param).then((result)=>{
        const res = JSON.parse(result);
        if(res.status == OK){
            const item = res.data;
            item.map((data, index)=>{
                // console.log('data>>',data);
                $('#'+data.CPPS_ID).val(data.NIS_AMOUNT);
            })
        }else{
            alert(ERROR_MSG_1100);
        } 
    });
}

