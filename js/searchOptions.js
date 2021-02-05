/* 동적 <SELECT> 개체 <OPTION> LIST 생성 */
const AJAX_URL = '/common/searchOptions.php';
/**
 * getDivisionOptions 검색조건의 구분 <SELECT> 안의 <OPTION> 동적생성
 * @param {String} objID option을 붙일 객체
 * @param {String} pText 초기화 기본 텍스트
 * @param {String} pREQ_MODE 호출할 req_mode
 * @param {String} pSelectedVal 선택된 값
 */
getDivisionOptions = (objID, pText, pREQ_MODE, pSelectedVal) => {
    const data = {
        REQ_MODE: pREQ_MODE,
    };
    callAjax(AJAX_URL, 'GET', data).then((result)=>{
        const res = JSON.parse(result);        
        let objOptionList = [];
        $("#" + objID + " option").remove();  // SELECT 개체 초기화
        objOptionList.push($("<option>", { text: pText }).attr("value","")); //최초조건 추가
        res.map((item, i)=>{
            objOptionList.push($("<option>", { text: item.CSD_NM }).attr("value",item.CSD_ID));
        })
        $("#" + objID).append(objOptionList);  // <SELECT> 객체에 반영

        if(pSelectedVal != undefined) $("#" + objID).val(pSelectedVal);
        
    
    });
}


/**
 * setSearchArea 지역검색 조건의  자동완성
 * @param {String} objID option을 붙일 객체
 */
setSearchArea = (objID) => {
    $("#" + objID).val('');  // 초기화

    const data = {
        REQ_MODE: OPTIONS_AREA,
        DIVISION: $("#SEARCH_DIVISION").val()
    };
    callAjax(AJAX_URL, 'GET', data).then((result)=>{
        const res = JSON.parse(result);
        $("#" + objID).autocomplete({
            minLength : 2,
            source: res,
            select: function(event, ui) {
                let strArea = ui.item.value;
                $("#" + objID).val(strArea);
            },
            focus: function(event, ui) {
                return false;
            }
        });
    
    });
};


setIwOptions = (objID, pSelectedVal, initialValue) => {
    $("#" + objID).val('');  // 초기화

    const data = {
        REQ_MODE: OPTIONS_IW,
    };
    callAjax(AJAX_URL, 'GET', data).then((result)=>{
        const res = JSON.parse(result);
      
        let objOptionList = [];

        $("#" + objID + " option").remove();  // SELECT 개체 초기화
        res.map((item, i)=>{
            objOptionList.push($("<option>", { text: item.MII_NM }).attr("value",item.MII_ID));
        });

        if(initialValue != undefined){
            $("#" + objID).append("<option value=''>"+initialValue+"</option>");
        }
        $("#" + objID).append(objOptionList);  // <SELECT> 객체에 반영
        $("#" + objID).selectpicker('refresh');;  // <SELECT> 객체에 반영

        if(pSelectedVal != undefined) {
            $("#" + objID).val(pSelectedVal);
            $("#" + objID).selectpicker('refresh');

        }
        
    
    });
}


setGwOptions = (objID, pSelectedVal, initialValue) => {
    $("#" + objID).val('');  // 초기화
    const data = {
        REQ_MODE: OPTIONS_GW,
    };
    callAjax(AJAX_URL, 'GET', data).then((result)=>{
        const res = JSON.parse(result);
        if(res.length > 0 ){
            let objOptionList = [];

            $("#" + objID + " option").remove();  // SELECT 개체 초기화
            res.map((item, i)=>{
                objOptionList.push($("<option>", { text: item.MGG_NM }).attr("value",item.MGG_ID));
            })
            if(initialValue != undefined){
                $("#" + objID).append("<option value=''>"+initialValue+"</option>");
            }
            $("#" + objID).append(objOptionList);  // <SELECT> 객체에 반영
            $("#" + objID).selectpicker('refresh');

            if(pSelectedVal != undefined) {
                $("#" + objID).val(pSelectedVal);
                $("#" + objID).selectpicker('refresh');
            }
        }else{
            alert("서버와의 연결이 원활하지 않습니다.");
        }
    });
}


/*등록주체 본사, 보험사, 공업사, 영업사원 */
setReqMode = (objID, pSelectedVal ) => {
    $("#" + objID + " option").remove();  // SELECT 개체 초기화
    $("#" + objID).append("<option value=''>=등록주체=</option>");
    $("#" + objID).append("<option value='"+MODE_TARGET_ALL+"'>"+MODE_TXT_HQ+"</option>");
    $("#" + objID).append("<option value='"+MODE_TARGET_GW+"'>"+MODE_TXT_GW+"</option>");
    $("#" + objID).append("<option value='"+MODE_TARGET_IW+"'>"+MODE_TXT_IW+"</option>");
    $("#" + objID).append("<option value='"+MODE_TARGET_SA+"'>"+MODE_TXT_SA+"</option>");
}


/**
 * 동적으로 보험사 selectOPtion 세팅
 * @param {dataArray : option 데이터들}
 * @param {objId : 타겟 select}
 * @param {isMultiSelect : 멀티셀렉트인지}
 * @param {defaultText : 기본 옵션 텍스트. 없으면 선택이라고 나옴}
 */
setSelectOption = (dataArray, objID, isMultiSelect, defaultText, defaultValue) => {      
    let optionHtml = "<option value=''>선택</option>";   
    if(defaultText != undefined  && defaultText != ''){
        optionHtml = "<option value=''>"+defaultText+"</option>";
    }else if(defaultText==''){
        optionHtml = '';
    }
    dataArray.map((item, index)=>{
        optionHtml += "<option value="+item.MII_ID+">"+item.MII_NM+"</option>";
    });
    
    $("#" + objID).html(optionHtml); 

    if(isMultiSelect){
        if(defaultValue != undefined){
            $("#" + objID).selectpicker('val', defaultValue);
        }
        $("#" + objID).selectpicker('refresh');
    }else{
        if(defaultValue != undefined){
            $("#" + objID).val(defaultValue);
        }
    }

    
}

/**
 * 지역 셀렉트
 * @param {targetID : 옵션을 저장할 타겟 셀력트}
 * @param {isMultiSelector : 멀티셀렉트인지}
 * @param {isTotal : 읍/면/동 까지 전체를 보여 줄 것인지 아니면 시/군/구까지만 보여줄 것이지 (true가 아니면 시/군/구 까지만)}
 */
setCRRSelectOption = (targetID, isMultiSelector, isTotal, defaultText) => {
    getCRRInfo($('#CSD_ID_SEARCH').selectpicker('val'), isTotal).then((result)=>{
        console.log(result);
        if(result.status == OK){
            const dataArray = result.data;
            if(dataArray.length > 0){
                let optionText = '';
        
                if(defaultText != undefined){
                    optionText = '<option value="">'+defaultText+'</option>';
                }

                dataArray.map((item, index)=>{
                    let ITEM_ID = item.CRR_1_ID+","+item.CRR_2_ID+","+item.CRR_3_ID;
                    optionText += '<option value="'+ITEM_ID+'">'+item.CRR_NM+'</option>';
                });
    
                $('#'+targetID).html(optionText);
                
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
  /** GET CPP_ID - CPP_NM OPTION LIST 
   * @param {String} objID option을 붙일 객체
   * @param {String} pText 초기화 기본 텍스트
   * @param {String} pSelectedVal 선택된 값
  */
  setPartOptions = (objID, pText, pSelectedVal) => {
    const data = {
        REQ_MODE: GET_PARTS_TYPE 
    };
    callAjax(AJAX_URL, 'GET', data).then((result)=>{
        const res = JSON.parse(result);        
        let objOptionList = [];

        $("#" + objID + " option").remove();  // SELECT 개체 초기화
        objOptionList.push($("<option>", { text: pText }).attr("value","")); //최초조건 추가
        res.map((item, i)=>{
            objOptionList.push($("<option>", { text: item.CPP_NM }).attr("value",item.CPP_ID));
        })
        $("#" + objID).append(objOptionList);  // <SELECT> 객체에 반영

        if(pSelectedVal != undefined) $("#" + objID).val(pSelectedVal);
        
    
    });
}


  /** GET REF_WORK_PART LIST 
   * @param {String} objID option을 붙일 객체
   * @param {String} pText 초기화 기본 텍스트
   * @param {String} pSelectedVal 선택된 값
  */
  setWorkOptions = (objID, pText, pSelectedVal) => {
    $("#" + objID + " option").remove();  // SELECT 개체 초기화
    let objOptionList = [];
    objOptionList.push($("<option>", { text: pText }).attr("value","")); //최초조건 추가
    objOptionList.push($("<option>", { text: RACON_CHANGE_NM }).attr("value",RACON_CHANGE_CD));
    objOptionList.push($("<option>", { text: RACON_METAL_NM }).attr("value",RACON_METAL_CD));

    $("#" + objID).append(objOptionList);  // <SELECT> 객체에 반영

    if(pSelectedVal != undefined) $("#" + objID).val(pSelectedVal);

  }

  /** setPartOptions
   * @param {String} objID option을 붙일 객체
   * @param {String} pText 초기화 기본 텍스트
   * @param {String} pREQ_MODE 호출할 req_mode 
   *        undefined: CPP_ID 기준부품
   *        GET_PARTS_TYPE: CPP_ID 기준부품
   *        OPTIONS_CPPS: CPPS_ID 취급부품
   * @param {String} pSelectedVal 선택된 값
  */
 setCPPSOptions = (objID, pText, pREQ_MODE, pSelectedVal) => {
    const data = {
        REQ_MODE: pREQ_MODE 
    };
    // console.log('setPartOptions >> ', data);
    callAjax(AJAX_URL, 'GET', data).then((result)=>{
        // console.log('partQuery>>',result);
        const res = JSON.parse(result);
        let objOptionList = [];

        $("#" + objID + " option").remove();  // SELECT 개체 초기화
        objOptionList.push($("<option>", { text: pText }).attr("value","").attr("selected","selected")); //최초조건 추가
       
        res.map((item, i)=>{
            objOptionList.push($("<option>", { text: item.CPPS_NM }).attr("value",item.CPPS_ID));
        })
        $("#" + objID).append(objOptionList);  // <SELECT> 객체에 반영

        if(pSelectedVal != undefined) $("#" + objID).val(pSelectedVal);
        
    
    });
  }

 /** GET NT_STOCK.HISTORY YEAR OF 'NIH_DT' OR 'NIH_APPROVE_DTHMS' 
   * @param {String} objID option을 붙일 객체
   * @param {String} pText 초기화 기본 텍스트
   * @param {String} pSelectedVal 선택된 값
  */
setStockYear = (objID, pText, pSelectedVal) => {
    const data = {
        REQ_MODE: OPTIONS_YEAR 
    };
    // console.log('setPartOptions >> ', data);
    callAjax(AJAX_URL, 'GET', data).then((result)=>{
        console.log('partQuery>>',result);
        const res = JSON.parse(result);
        let objOptionList = [];

        $("#" + objID + " option").remove();  // SELECT 개체 초기화
        objOptionList.push($("<option>", { text: pText }).attr("value","").attr("selected","selected")); //최초조건 추가
       
        res.map((item, i)=>{
            if(item.NIH_Y != null){
                objOptionList.push($("<option>", { text: item.NIH_Y }).attr("value",item.NIH_Y));
            }
        })
        $("#" + objID).append(objOptionList);  // <SELECT> 객체에 반영

        if(pSelectedVal != undefined) $("#" + objID).val(pSelectedVal);
        
    
    });
}

/** GET NT_STOCK.HISTORY MONTH OF 'NIH_DT' OR 'NIH_APPROVE_DTHMS' 
   * @param {String} objID option을 붙일 객체
   * @param {String} pText 초기화 기본 텍스트
   * @param {String} pSelectedVal 선택된 값
  */
 setStockMonth = (objID, pText, pSelectedVal) => {
    const data = {
        REQ_MODE: OPTIONS_MONTH
    };
    // console.log('setPartOptions >> ', data);
    callAjax(AJAX_URL, 'GET', data).then((result)=>{
        // console.log('partQuery>>',result);
        const res = JSON.parse(result);
        let objOptionList = [];

        $("#" + objID + " option").remove();  // SELECT 개체 초기화
        objOptionList.push($("<option>", { text: pText }).attr("value","").attr("selected","selected")); //최초조건 추가
       
        res.map((item, i)=>{
            if(item.NIH_M != null){
                objOptionList.push($("<option>", { text: item.NIH_M }).attr("value",item.NIH_M));
            }
        })
        $("#" + objID).append(objOptionList);  // <SELECT> 객체에 반영

        if(pSelectedVal != undefined) $("#" + objID).val(pSelectedVal);
        
    });
}

/** 공업사정보 */
getGarageInfoOption = (objID, isMultiSelector, initialValue) => {
    const data = {
        REQ_MODE: GET_GARGE_INFO,
    };
    callAjax(AJAX_URL, 'GET', data).then((result)=>{
        if(isJson(result)){
            const dataArray = JSON.parse(result);

            if(dataArray.length > 0){
                let optionText = '';
        
                if(initialValue != undefined){
                    optionText = '<option value="">'+initialValue+'</option>'
                }
                dataArray.map((item, index)=>{
                    optionText += '<option value="'+item.MGG_ID+'">'+item.MGG_NM+'</option>';
                });

                console.log(optionText);
                $('#'+objID).append(optionText);
                
                if(isMultiSelector){
                    $('#'+objID).selectpicker('refresh');
                }
            }
        }else{
            alert(ERROR_MSG_1200);
        }
       
    });
}

let INSUTEAM_LIST = [] ;
let INSUTEAM_ALL = [] ;
/* 보험사Team 동적 생성 */
setInsuTeam = (objID) =>{
    objID = objID == undefined ? 'SEARCH_INSU_TEAM' : objID;
    const data = {
      REQ_MODE: GET_INSU_TEAM,
    };
    callAjax(AJAX_URL, 'GET', data).then((result)=>{
        const data = JSON.parse(result);
        if(data.status == OK){
            const dataArray = JSON.parse(data.data); 
            INSUTEAM_ALL =   dataArray ;
            let optionText = '';
            dataArray.map((item)=>{
                optionText += '<option value="'+item.MIT_ID+'">'+item.MII_NM+'-'+item.MIT_NM+'</option>';
            });
            $('#'+objID).html(optionText);
            $('#'+objID).selectpicker('refresh');
          
        }else{
            alert("서버와의 연결이 원활하지 않습니다.");
        }
    });
}

selectInsu = (dataArray , teamTagId) =>{
    let tagID = teamTagId == undefined ? 'SEARCH_INSU_TEAM' : teamTagId;
    let optionText = '';
      dataArray.map((data)=>{
         data.map((item)=>{
           optionText += '<option value="'+item.MIT_ID+'">'+item.MII_NM+'-'+item.MIT_NM+'</option>';
         })
  
      });
      $('#'+tagID).html(optionText);
      $('#'+tagID).selectpicker('refresh');
  
}
  

getPointGrade =  (objID, initialValue, pSelectedVal) => {
    const data = {
        REQ_MODE: REF_POINT_TYPE,
    };
    callAjax(AJAX_URL, 'GET', data).then((result)=>{
        const res = JSON.parse(result);
        console.log('getPointGrade>>>',res);
        let objOptionList = [];

        $("#" + objID + " option").remove();  // SELECT 개체 초기화
        if(res.le){

        }
        res.map((item, i)=>{
            objOptionList.push($("<option>", { text: item.SLL_NM }).attr("value",item.SLL_ID));
        });

        if(initialValue != undefined){
            $("#" + objID).html("<option value=''>"+initialValue+"</option>");
        }
        $("#" + objID).append(objOptionList);  // <SELECT> 객체에 반영
       
        if(pSelectedVal != undefined) {
            $("#" + objID).val(pSelectedVal);


        }
        
    
    });
}