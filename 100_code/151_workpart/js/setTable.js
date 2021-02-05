
const GREEN_CLASS = 'greentd';    
const BLUE_CLASS = 'bluetd';

/**
 * 교환/판금부분에 값 넣어주기
 * @param {data : 해당 td 데이터}
 * @param {classType : 초록인지 파랑인지(교환/판금)}
 * @param {isChangeParts : 파트가 바뀌었는지..(바뀌었을 때 첫번째 줄 css)}
 */
getPartsTable = (data, classType,  isChangeParts) => {
    let partsHtml = "";
    let colspan = 0;              // 단일일 경우 필요한 colsapn
    let limitCount = 2;           // 좌우 일 경우에는 2번 나머지는 1번
    let borderRno = '';           // 7번째줄에서만 꼭 필요하답니다!
    let rwppIdType = '';          // 단일/좌/우/실내/하체 중 무엇
    let reckonType = '';          // 교환/판금 중 무엇인지
    const rwtId = data.RWT_ID;

    /** 판금인지 교환인지
     * 색깔 클래스로 구별
     */
    if(classType == GREEN_CLASS){
        reckonType = reckonArray[0].ID
    }else{
        reckonType = reckonArray[1].ID
    }
   
    if(rwtId == RWP_ID_1){                 // 단일
       colspan = 2;
       limitCount = 1;
       rwppIdType = rwppArray[0].ID;
    }else if(rwtId == RWP_ID_DOWN){        // 바닥
        rwppIdType = rwppArray[3].ID;
    }else{                                 // 값이 없으면 오류
        rwppIdType = '';
    }

    if(isChangeParts || colspan == 2){
        borderRno = 'borderRno';
    }
   
    /**
     * 설정된 index까지만 반복문을 사용하기 위해 for문 사용(단일인지 좌/우 인지)
     */
    for(let i=0; i<limitCount ; ++i){
        partsHtml += "<td class='text-center "+classType+" "+borderRno+"' colspan='"+colspan+"'>";
        partsHtml += "<button type='button' class='btn btn-default'";
        
        if(colspan == 0){
            if(i ==0){    // 좌/우 이며 i=0인것은 좌
                if(rwtId == RWP_ID_DOWN){
                    rwppIdType = rwppArray[3].ID;
                }else{
                    rwppIdType = rwppArray[1].ID;
                }
                
            }else{       // 좌/우 이며 i=0인것은 우
                if(rwtId == RWP_ID_DOWN){
                    rwppIdType = rwppArray[4].ID;
                }else{
                    rwppIdType = rwppArray[2].ID;
                }
                
            }
           
        }
        getItemCount(data.NWP_ID, reckonType, rwppIdType, PRIORITY, true);   // 우선순위 에 설정값이 있는지
        getItemCount(data.NWP_ID, reckonType, rwppIdType, REPLACE, true);    // 대체/체화 에 설정값이 있는지

        partsHtml += " id='PRIORITY_"+data.NWP_ID+"_"+reckonType+"_"+rwppIdType+"' onclick='showPriorityModal("+JSON.stringify(data)+","+reckonType+","+rwppIdType+")'>우선</button>";
        partsHtml += "<button type='button' id='REPLACE_"+data.NWP_ID+"_"+reckonType+"_"+rwppIdType+"' class='btn btn-default' onclick='showReplaceModal("+data.NWP_ID+","+reckonType+","+rwppIdType+")'>대체/체화</button>";

        /** 바닥있는 경우 */
        if(rwtId == RWP_ID_DOWN){
            getItemCount(data.NWP_ID, reckonType, rwppIdType, FLOOR, true);   //바닥에 설정값이 있는지
            partsHtml += "<button type='button' id='FLOOR_"+data.NWP_ID+"_"+reckonType+"_"+rwppIdType+"' class='btn btn-default' onclick='showPartModal("+JSON.stringify(data)+","+reckonType+","+rwppIdType+")'>바닥</button>";
        }
        partsHtml += '</td>';
    }
    return partsHtml;
}

/**
 * 메인 테이블 동적으로.
 * @param {dataArray : 작업항목 데이터 전체}
 */
setTable = (dataArray) => {
    let tableText = ''; 
    let beforeRWP_NM2 = '';   // 전 파트 이름
    let rwpNm2Array  = [];    // 파트이름과 동일한 아이템이 몇개인지. rowspan
    let trCount = 0;          // 각 파트에 몇번째 줄인지..마지막에 class = borderAll 추가해주기 위해
    let isChangeParts = true;

    dataArray.map((data, index)=>{ 
        if(beforeRWP_NM2 != data.RWP_NM2 ){
            trCount = 0;
            rwpNm2Array = dataArray.filter((rwpNm2, index)=>{
                return rwpNm2.RWP_NM2 == data.RWP_NM2
            });
            isChangeParts = true;
            tableText += "<tr class='borderB' id='"+data.NWP_ID+"' > ";
            tableText += "<td class='text-center active' rowspan='"+rwpNm2Array.length+"'>"+data.RWP_NM2+"</td>";
        }else{
            isChangeParts = false;
            if(trCount == rwpNm2Array.length -1){
                tableText += "<tr class='borderAll' id='"+data.NWP_ID+"'> ";
            }else{
                tableText += "<tr id='"+data.NWP_ID+"'> ";
            }
            
        }
        tableText +=  "<td class='text-center'>"+(index+1)+"</td>";
        tableText +=  "<td class='text-left'>"+data.RWP_NM1+"</td>";
        tableText += getPartsTable(data, GREEN_CLASS, isChangeParts);
        tableText += getPartsTable(data, BLUE_CLASS);
        tableText += "</tr>";

        beforeRWP_NM2 =  data.RWP_NM2;
        trCount++;
        
    });
    $('#dataTable').html(tableText);   
}


/**
 * 우선순위 테이블
 * @param {dataArray : 기존부품 정보}
 */
setPriorityTable = (dataArray) => {
    let priorityHtml = '';
    dataArray.map((data, index)=>{
        priorityHtml +=  "<tr>";
        priorityHtml +=  '<td class="text-center"><input type="checkbox" name="CPP_ID[]" value="'+data.CPP_ID+'"></td>';
        priorityHtml +=  '<td class="text-center">'+data.CPP_PART_TYPE+'</td>';
        priorityHtml +=  '<td class="text-center">'+data.CPP_NM_SHORT+'</td>';
        priorityHtml += "</tr>";
        
    });

    $('#priorityTable').html(priorityHtml);  
}


/**
 * 대체/체화 테이블
 * @param {dataArray : 기존부품 정보}
 */
setReplaceTable = (dataArray) => {
    let replaceHtml = "";     // 대체/체화모달에서 왼쪽 테이블
    let replaceToHtml = '';   // 대체/체화모달에서 오른쪽 테이블
    dataArray.map((data, index)=>{
        replaceHtml +=  "<tr>";
        replaceToHtml +=  "<tr>";
        replaceHtml += '<td style="cursor:pointer" onclick="showReplaceToTable(\''+data.CPP_ID+'\', event);" class="text-center">'+data.CPP_NM_SHORT+'</td>';
        replaceHtml += '<td class="text-center">';
        replaceHtml += '<select disabled id="REPLACE_SELECT_'+data.CPP_ID+'" class="form-control text-center">';
        replaceHtml += ' <option value="Y">Y</option><option value="N" selected>N</option>';
        replaceHtml += '</select></td>';
        replaceToHtml +=  '<td class="text-center"><input type="checkbox" onchange="onChangeReplaceCheckbox(event)" id="REPLACE_CHECK_'+data.CPP_ID+'" name="TO_CPP_ID[]" value="'+data.CPP_ID+'"></td>';
        replaceToHtml +=  '<td class="text-center">'+data.CPP_NM_SHORT+'</td>';
        replaceToHtml += '<td class="text-center"><input type="text" id="REPLACE_RATIO_'+data.CPP_ID+'" readonly onkeypress="return isFloat(event)" class="form-control text-right"></td>';
        replaceHtml += "</tr>";
        replaceToHtml +=  "<tr>";
        
    });
    $('#replaceTable').html(replaceHtml);
    $('#replaceToTbody').html(replaceToHtml);
}


/**
 * 대체/체화 모달에서 부품 눌렀을 때 사용부품 선택하고 배율 선택하는 테이블 보여지게 하는 함수
 * @param {FROM_CPP_ID : 어느 부품을 눌렀는지 클릭한 부품 아이디}
 * @param {evt : 클릭한 부품에 해당된 td(클릭시 백그라운드 css 수정하기 위해)}
 */
showReplaceToTable = (FROM_CPP_ID, evt) => {
    resetReplaceToTable();
    $('#replaceTable select').val(YN_N);

    const objTarget = evt.srcElement || evt.target;
    $(objTarget).css('background-color','#E4F4FA');

    console.log(replaceArray);
    replaceArray.map((item)=>{
        if(item.FROM_CPP_ID == FROM_CPP_ID){
            $('#REPLACE_CHECK_'+item.TO_CPP_ID).prop('checked',true);
            $('#REPLACE_RATIO_'+item.TO_CPP_ID).val(item.NWE_RATIO);
            $('#REPLACE_RATIO_'+item.TO_CPP_ID).attr('readonly',false);
        }
        $('#REPLACE_SELECT_'+item.FROM_CPP_ID).val(YN_Y);
    });
    
    $('#FROM_CPP_ID').val(FROM_CPP_ID);
    $('#replaceToTable').show();
}

/**
 * 작업항목 리스트가져오기
 */
getWorkPartList = () => {
    const data = {
        REQ_MODE: CASE_LIST
    };
    callAjax(ajaxUrl, 'POST', data).then((result)=>{
        if(isJson(result)){
            const data = JSON.parse(result);
            setTable(data.data);
        }else{
            alert(ERROR_MSG_1200);
        }
    });
}