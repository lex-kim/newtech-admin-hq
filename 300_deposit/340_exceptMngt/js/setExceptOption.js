/**
 * 등록/수정 팝업에서 select 옵션 설정해주는 js
 * 
 */
var crrArray = [];

 /**
  * body onload 이벤트로 인해 가장 먼저 호출
  */



/** 지역정보 값 초기화 (해당지여구분 데이터가 존재하지 않을경우)
 * @param {targetObj : 조회한 지역구분에 데이터가 있을 경우 disable 해제시켜줄 타겟 인풋}
 * @param {selectObj : autocomplete에서 선택한 값들을 저장할 변수}
*/
function resetCRRValue(targetObj, selectObj){
    $('#'+targetObj).attr('disabled',true);
    $('#'+targetObj).val('');
    $('#'+selectObj).val('');
    crrArray = [];
}



/** 지역 구분 클릭시 데이터가 있으면 disabled이 false 처리되고 검색 가능 
 * @param {obj : 선택한 지역구분 데이터 }
 * @param {targetObj : 조회한 지역구분에 데이터가 있을 경우 disable 해제시켜줄 타겟 인풋}
 * @param {selectObj : autocomplete에서 선택한 값들을 저장할 변수}
*/
function setCRR_ID(obj, targetObj ,selectObj, isTotal){
    getCRRInfo($('#'+obj).val(), isTotal).then(function(data){
        if(data.status == OK){
            var dataArray = data.data;
            crrArray = dataArray;
            if(dataArray.length > 0){
                $('#'+targetObj).attr('disabled',false);
                autoCompleteCRR(targetObj, selectObj, dataArray);
            }else{
                resetCRRValue(targetObj, selectObj);
            }
        } else{
            resetCRRValue();
        }  
    });
     
}

/**
 * 지역구분에 해다되는 주소 옵션
 * @param {objID : 조회할 지역구분 정보}
 * @param {targetID : 조회한 구분을 넣어줄 select ID}
 * @param {isMultiSelector : 멀티셀렉터 인지 아닌지}
 */
function setCRR_ID_Option(objId, targetID, isMultiSelector, isTotal){
    getCRRInfo($('#'+objId).val(), isTotal).then(function(data){
        if(data.status == OK){
            var dataArray = data.data;
            if(dataArray.length > 0){
                var optionText = '';
        
                dataArray.map(function(item, index){
                    var ITEM_ID = item.CRR_1_ID+","+item.CRR_2_ID+","+item.CRR_3_ID;
                    optionText += '<option value="'+ITEM_ID+'">'+item.CRR_NM+'</option>';
                });
    
                $('#'+targetID).html(optionText);
                console.log("optionTextoptionText==>",optionText);
                console.log("optionTextoptionText==>",targetID);
                
                if(isMultiSelector){
                    $('#'+targetID).selectpicker('refresh');
                }
            }else{
                $('#'+targetID).html('');
                
                if(isMultiSelector){
                    $('#'+targetID).selectpicker('refresh');
                }
            }
        }
    });
}



var arrDepotitDetail = [];


/* 보상담당 동적 생성 */
function setInsuFao(){
    var data = {
      REQ_MODE: GET_INSU_TYPE,
    };
    callAjax(EXCEPT_URL, 'GET', data).then(function(result){
        var res = JSON.parse(result);
        // console.log('FAO result>>',res);
        if(res.status == OK){
            var dataArray = res.data; 
              
            var optionText = '';
            dataArray.map(function(item){
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
function setClaimId(){
    var data = {
      REQ_MODE: GET_REF_CLAIMANT_TYPE,
    };
    callAjax(EXCEPT_URL, 'GET', data).then(function(result){
        var res = JSON.parse(result);
        // console.log('FAO result>>',res);
        if(res.status == OK){
            var dataArray = res.data; 
              
            var optionText = '';
            dataArray.map(function(item){
                optionText += '<option value="'+item.NIC_ID+'">'+item.NIC_NM_AES+'</option>';
            });
            $('#INSU_FAO_SEARCH').html(optionText);
          
        }else{
            alert("서버와의 연결이 원활하지 않습니다.");
        }
    });
}


/* CAR TYPE SELECT */
function setCarType(){
    var data = {
        REQ_MODE: GET_CAR_TYPE
    };
    var headerTxt = '';
    callAjax(EXCEPT_URL, 'GET', data).then(function(result){
        // console.log('table_hedder >>',result)
        var res = JSON.parse(result);
        if(res.status == OK){
            var item = res.data;
            $("#CCC_ID_SEARCH option").remove();  // thead 하위 <tr>개체 초기화
            if(item.length > 0){
                item.map(function(item, i){
                    headerTxt += '<option value="'+item.RCT_CD+'">'+item.RCT_NM+'</option>';
                })

                $("#CCC_ID_SEARCH").html('<option value="">=차량급=</option>'+headerTxt);  // <SELECT> 객체에 반영

                
            
            }
        }
    });
}



/* 사용부품 */
function setPart(){
    var data = {
        REQ_MODE: GET_PARTS_TYPE
    };
    var headerTxt = '';
    callAjax(EXCEPT_URL, 'GET', data).then(function(result){
        // console.log('table_hedder >>',result)
        var res = JSON.parse(result);
        if(res.status == OK){
            var item = res.data;
            $("#CPPS_ID_SEARCH option").remove();  // thead 하위 <tr>개체 초기화
            if(item.length > 0){
                item.map(function(item, i){
                    headerTxt += '<option value="'+item.CPPS_NM_SHORT+'">'+item.CPP_NM_SHORT+'-'+item.CPPS_NM+'</option>';
                })
                $("#CPPS_ID_SEARCH").html('<option value="">=사용부품=</option>'+headerTxt);  // <SELECT> 객체에 반영            
            }
        }
    });
}



/**
 * 입금여부 상세 동적생성
 */
function setDepoitSearch(){
    var param = {
        REQ_MODE : REF_DEPOSIT_TYPE
    }
    callAjax(EXCEPT_URL, 'GET', param).then(function(result){
        // console.log('getDepoitDetail SQL >>',result);
        var res = JSON.parse(result);
        // console.log('getDepoitDetail>>',res);
        if(res.status == OK){
            arrDepotitDetail = res.data; 
            var optionText = '';
            $('#DEPOSIT_DETAIL_YN_SEARCH option').remove();
            arrDepotitDetail.map(function(item){
                optionText += '<option value="'+item.ID+'">'+item.NM+'</option>';
            });
            $("#DEPOSIT_DETAIL_YN_SEARCH").append('<option >=입금여부상세=</option>'+optionText);  // <SELECT> 객체에 반영            
            $("#DEPOSIT_DETAIL_YN_SEARCH").selectpicker('refresh');
            
        }else{
            alert(ERROR_MSG_1100);
        }
    });
}




/**
 * 입금여부 동적생성
 * @param {String} objId select tag id
 * @param {String} pSelectedVal select tag value 
 */
function getDepoiteeType(objId, pSelectedVal){
    var param = {
        REQ_MODE: GET_REF_OPTION,
        REF_TYPE : REF_DEPOSITEE_TYPE
    }
    callAjax('/common/gwReference.php', 'GET', param).then(function(result){
        // console.log('getDepositeee SQL >>',result);
        var res = JSON.parse(result);
        // console.log('getDepositee>>',res);
        if(res.status == OK){
            var dataArray = res.data; 
            //   console.log('getDepositType >>',dataArray) ;
            var optionText = '';
            dataArray.map(function(item){
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
function getDepoitDetail(objId, pSelectedVal){
    // console.log('getDepoitDetail>>' ,pSelectedVal);
    var param = {
        REQ_MODE : REF_DEPOSIT_TYPE
    }
    callAjax(EXCEPT_URL, 'GET', param).then(function(result){
        // console.log('getDepoitDetail SQL >>',result);
        var res = JSON.parse(result);
        // console.log('getDepoitDetail>>',res);
        if(res.status == OK){
            arrDepotitDetail = res.data; 
            var optionText = '';
            arrDepotitDetail.map(function(item){
                optionText += '<label class="radio-inline"><input type="radio" name="RADIO_DEPOSIT_DETAIL" onclick="changeDepositDetail()" value="'+item.ID+'" readonly>'+item.NM+'</label>';
            });
            $('#'+objId).html(optionText);

            if(pSelectedVal != undefined) {
                $("input[value='"+pSelectedVal+"']").attr('checked', true);
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
function setOtherIw(objID, pSelectedVal){
    var data = {
        REQ_MODE: OPTIONS_IW,
    };
    callAjax('/common/searchOptions.php', 'GET', data).then(function(result){
        var res = JSON.parse(result);
        var optionHtml = '';

        $('#'+objID+' option').remove();
        if(res != undefined || res.length > 0){
            res.map(function(item, index){
                optionHtml += "<option value="+item.MII_ID+">"+item.MII_NM+"</option>";
            });
        }
        
        $("#" + objID).append('<option value="" selected>=선택=</option>'+optionHtml); 

        if(pSelectedVal != undefined) {
            $("#" + objID).val(pSelectedVal);
        }
    });
}




  /* 보험사Team 동적 생성 */
//   function setMiiInsuTeam(){
//     var data = {
//       REQ_MODE: GET_INSU_TEAM,
//     };
//     callAjax(EXCEPT_URL, 'GET', data).then(function(result){
//         // console.log('setInsuTeam SQL>>>', result);
//         var data = JSON.parse(result);
//         if(data.status == OK){
//             var dataArray = JSON.parse(data.data); 
              
//             var optionText = '';
//             dataArray.map(function(item){
//                 optionText += '<option value="'+item.MIT_ID+'">'+item.MIT_NM+'</option>';
//             });
//             $('#SEARCH_INSU_TEAM').html('<option>=보상팀=</option>'+optionText);
//             $('#SEARCH_INSU_TEAM').selectpicker('refresh');
          
//         }
//     });
//   }


// let arrExclude = [];

// /**
//  * 지급제외 사유  
//  * @param {String} objId select tag id 
//  * @param {String} pSelectedVal select tag value 
//  */
// // getExcludeList = (objId,pSelectedVal) => {
//   const param = {
//       REQ_MODE : REF_EXCLUDE_REASON
//   }
//   callAjax(EXCEPT_URL, 'GET', param).then((result)=>{
//       // console.log('getExcludeList SQL >>',result);
//       const res = JSON.parse(result);
//       // console.log('getExcludeList>>',res);
//       if(res.status == OK){
//           arrExclude = res.data; 
//           let optionText = '';
//           $('#'+objId+' option').remove();
//           arrExclude.map((item)=>{
//               optionText += '<option value="'+item.ID+'" >'+item.NM+'</option>';
//           });
//           $('#'+objId).append('<option value="" >=지급제외사유=</option>'+optionText);
//           // $('#SEARCH_INSU_TEAM').selectpicker('refresh');
//           if(pSelectedVal != undefined) {
//               $("#"+objId).val(pSelectedVal);
//           }
//           $("#"+objId).selectpicker('refresh');
//       }else{
//           alert(ERROR_MSG_1100);
//       }
//   });
// }


var arrDecrement = [];
/**
 * 지급제외 사유
 * @param {String} objId select tag id 
 * @param {String} pSelectedVal select tag value 
 */
function getExcludeList(objId,pSelectedVal){
    var param = {
        REQ_MODE : REF_EXCLUDE_REASON
    }
    callAjax(EXCEPT_URL, 'GET', param).then(function(result){
        // console.log('getExcludeList SQL >>',result);
        var res = JSON.parse(result);
        console.log('getExcludeList>>',res);
        if(res.status == OK){
            arrDecrement = res.data; 
            var optionText = '';
            $('#'+objId+' option').remove();
            arrDecrement.map(function(item){
                optionText += '<option value="'+item.ID+'" >'+item.NM+'</option>';
            });
            $('#'+objId).append(optionText);
            if(pSelectedVal != undefined) {
                $('#'+objId).val(pSelectedVal);
            }
            $("#"+objId).selectpicker('refresh');
        }else{
            alert(ERROR_MSG_1100);
        }
    });
}
