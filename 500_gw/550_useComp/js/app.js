const ajaxUrl = 'act/gwuseComp_act.php';
let PARTS_TABLE_INFO = [];
let CURR_PAGE = 1; 

$(document).ready(function() {
    $('#indicator').hide();
    getDivisionOption('CSD_ID_SEARCH', true);                   // 지역구분
    setCRRSelect('CRR_ID_SEARCH', true);
    getRefOptions(GET_REF_CONTRACT_TYPE, 'RCT_ID_SERACH', false, false);                       // 거래여부  

    /** 구분 멀티셀렉 */
    $('#CSD_ID_SEARCH').change(()=>{
        if($('#CSD_ID_SEARCH').selectpicker('val') != null){
            setCRRSelect('CRR_ID_SEARCH', true);
        }else{
            $('#CSD_ID').selectpicker('val' , '');
            $('#CSD_ID').selectpicker('refresh');
            setCRRSelect('CRR_ID_SEARCH', true);

        }
    });

    /** 기준부품 멀티셀렉 */
    $('#CPP_ID_SEARCH').change(()=>{
        if($('#CPP_ID_SEARCH').selectpicker('val') != null){
            const stdSelectPats = $('#CPP_ID_SEARCH').selectpicker('val');
            let usePartsArray = [];
            stdSelectPats.map((parts)=>{
                usePartsArray.push(PARTS_TABLE_INFO.filter((item, index)=>{
                    return item.CPP_ID == parts;
                }));
            });
           
            setUsePartsOption(usePartsArray);
        }else{
            $('#CPPS_ID_SEARCH').html('');
            $('#CPPS_ID_SEARCH').selectpicker('refresh');
        }
       
        
    });
   
    /** 검색버튼 */
    $('#searchForm').submit(function(e){
        e.preventDefault();

        // if(isSearchValidate()){
          getList(1);
        // }
       
    });

    setPartsTypeSelect();
});

setCRRSelect = (targetID, isMultiSelector, isTotal) => {
    getCRRInfo($('#CSD_ID_SEARCH').selectpicker('val'), isTotal).then((result)=>{
        console.log(result);
        if(result.status == OK){
            const dataArray = result.data;
            if(dataArray.length > 0){
                let optionText = '';
        
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

/**취급부품명 멀티셀렉 동적옵션 
 * @param {dataArray : 선택한 취급부품들 정보}
*/
setUsePartsOption = (dataArray) => {
    let optionText = '';
    dataArray.map((data)=>{
        const partsNm = (data[0].CPPS_NM).split(",");
        const partsId = (data[0].CPPS_ID).split(",");
        partsNm.map((item, index)=>{
            optionText += '<option value="'+partsId[index]+'">'+item+'</option>';
        });
    });
    $('#CPPS_ID_SEARCH').html(optionText);
    $('#CPPS_ID_SEARCH').selectpicker('refresh');
}

/**기준부품명 멀티셀렉 동적옵션 */
setStdPartsOption = () => {
    let optionText = '';
        
    PARTS_TABLE_INFO.map((item, index)=>{
        optionText += '<option value="'+item.CPP_ID+'">'+item.CPP_NM+'</option>';
    });

    $('#CPP_ID_SEARCH').html(optionText);
    $('#CPP_ID_SEARCH').selectpicker('refresh');
}

/** 부품타입 */
setPartsTypeSelect = () => {
  const data = {
      REQ_MODE: GET_PARTS_TYPE,
  };

  callAjax(ajaxUrl, 'GET', data).then((result)=>{
      if(isJson(result)){
        const data = JSON.parse(result);
        console.log(data);
        if(data.status == OK){
            PARTS_TABLE_INFO = data.data;
            setTableHeader(data.data);
            setStdPartsOption();
        }else{
            alert("서버와의 연결이 원활하지 않습니다.");
        }
      }else{
            alert(ERROR_MSG_1200);
        }
     
  });
}

excelDownload = () => {
    location.href="useCompExcel.html?"+$("#searchForm").serialize()+"&REQ_MODE="+CASE_TOTAL_LIST;
  }

/** 리스트 */
getList = (currentPage) => {
    CURR_PAGE = (currentPage == undefined) ? CURR_PAGE : currentPage;
    let form = $("#searchForm")[0];
    let formData = new FormData(form);
    formData.append('REQ_MODE', CASE_LIST);
    formData.append('PER_PAGE', $("#perPage").val());
    formData.append('CURR_PAGE', CURR_PAGE);
  
    callFormAjax(ajaxUrl, 'POST', formData).then((result)=>{
        if(isJson(result)){
            const data = JSON.parse(result);
            console.log(data);
            if(data.status == OK){
                const item = data.data;
                setTable(item[1], item[0], CURR_PAGE);
            }else{
                setHaveNoDataTable();
            }
        }else{
            alert(ERROR_MSG_1200);
        }
    });  
  }