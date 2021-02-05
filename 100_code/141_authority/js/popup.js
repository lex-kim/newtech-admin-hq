let IS_DUPLICATE = true;
let confirmText = "수정";

function onLoadPopup(){
    if($('#ORI_ALLM_ID').val() != ''){  // 수정모드일 경우
        getData();
    }else{
        getAuthorityList();
    }
    
   

    $("#setForm").submit(function(e){
        e.preventDefault();

        if(validation()){
          
            if($('#ORI_ALLM_ID').val() == ''){
                confirmText = "저장";
            }
            confirmSwalTwoButton(confirmText+' 하시겠습니까?','확인', '취소', false ,()=>{
                if($('#ORI_ALLM_ID').val() == ''){
                    setData(CASE_CREATE, confirmText);
                }else{
                    setData(CASE_UPDATE, confirmText);
                }
                
            });
        }
    });
}

function setAuthorityTitleHeader(dataArray){
    let titleArray = [];
    dataArray.map(function(item){
        titleArray.push(item.NUI_1ST);
    });

    let titleHtml = "";
    Array.from(new Set(titleArray)).map(function(title, index){
        titleHtml += '<div class="authority billmngt">';
        titleHtml += '<div class="container-fluid table-title title-html">'+title+'</div>';
        titleHtml += '<div class="container-fluid table-content">';
        titleHtml += '<table class="table table-bordered table-striped">';
        titleHtml += '<thead>';
        titleHtml += '<tr class="active">';
        titleHtml += '<th class="text-center">메뉴</th>';
        titleHtml += '<th class="text-center">';
        titleHtml += '<label for="codeadd">등록 <input type="checkbox" id="TOTAL_CODE_CREATE_'+index+'" name="TOTAL_CODE_CREATE_'+index+'"></label>';
        titleHtml += '</th>';
        titleHtml += '<th class="text-center">';
        titleHtml += '<label for="codeview">조회 <input type="checkbox" id="TOTAL_CODE_READ_'+index+'" name="TOTAL_CODE_READ_'+index+'"></label>';
        titleHtml += '</th>';
        titleHtml += '<th class="text-center">';
        titleHtml += '<label for="codemodify">수정 <input type="checkbox" id="TOTAL_CODE_UPDATE_'+index+'" name="TOTAL_CODE_UPDATE_'+index+'"></label>';
        titleHtml += '</th>';
        titleHtml += '<th class="text-center">';
        titleHtml += '<label for="codedelete">삭제 <input type="checkbox" id="TOTAL_CODE_DELETE_'+index+'" name="TOTAL_CODE_DELETE_'+index+'"></label>';
        titleHtml += '</th>';
        titleHtml += '<th class="text-center">';
        titleHtml += '<label for="codeexcel">엑셀 <input type="checkbox" id="TOTAL_CODE_EXCEL_'+index+'" name="TOTAL_CODE_EXCEL_'+index+'"></label>';
        titleHtml += '</th>';
        titleHtml += '</tr>';
        titleHtml += '</thead>';
        titleHtml += '<tbody id="TOTAL_CODE_'+index+'_TABLE">';
        titleHtml += '</tbody>';
        titleHtml += '</table>';
        titleHtml += '</div>';
        titleHtml += '</div>';
    });
    $('#authorityTotalTable').html(titleHtml);
    setAutorityTable(dataArray);
}

function validation(){
    if($('#ORI_ALLM_ID').val() == $('#ALLM_ID').val()){
        return true;
    }else{
        if(IS_DUPLICATE){
            alert("중복된 권한코드 입니다.");
            $('#ALLM_ID').focus();
            return false;
        }else{
            return true;
        }
    }  
}

function delData(){
    setData(CASE_DELETE, "삭제");
}

/** 리스트에서 클릭해서 디테일 페이지 들어갔을 때 데이터 세팅 */
function getData(){
    const data = {
        REQ_MODE : CASE_READ,
        ALLM_ID : $('#ORI_ALLM_ID').val()
    };
    callAjax(AUTHORITY_URL, 'GET', data).then((result)=>{
        const data = JSON.parse(result);
        if(data.status == OK){
            const item = data.data;
            $('#ALLM_NM').val(item[0].ALLM_NM);
            $('#ALLM_ID').val(item[0].ALLM_ID);
            $('#ALLM_ID').trigger('change');
            setAuthorityTitleHeader(data.data);
            // setAutorityTable(data.data);
        }else{
            const resultText ='서버와의 연결이 원활하지 않습니다.' ;
            alert(resultText);
        } 
    });
}

function setData(REQ_MODE, CONFIRM_TXT){
    let form = $("#setForm")[0];
    let formData = new FormData(form);
    formData.append('REQ_MODE', REQ_MODE);
    $('input[type=checkbox]').each(function(){
        if($(this).is(':checked') && isJson($(this).val())){
            formData.append('DATA[]', $(this).val() );
        }
    });

    callFormAjax(AUTHORITY_URL, 'POST', formData).then(async(result)=>{
        const data = JSON.parse(result);
        if(data.status == OK){
            confirmSwalOneButton(CONFIRM_TXT+' 성공하였습니다.','확인',()=>{
                if(REQ_MODE == CASE_UPDATE){
                    window.opener.getList();
                }else{
                    window.opener.getList(1);
                }
                self.close();
            });
        }else{
            alert("서버와의 연결이 원활하지 않습니다.");
        } 
    });
}

function oncheckDuplicateID(){
    if($('#ALLM_ID').val() != ""){
        const data = {
            REQ_MODE: CASE_CONFLICT_CHECK,
            ORI_ALLM_ID : $('#ORI_ALLM_ID').val(),
            ALLM_ID : $('#ALLM_ID').val()
        };

        callAjax(AUTHORITY_URL, 'GET', data).then((result)=>{
            const data = JSON.parse(result);
            if(data.status == OK){
                IS_DUPLICATE = false;
                $('#DUPICATE_SPAN').hide();
            }else{
                IS_DUPLICATE = true;
                $('#DUPICATE_SPAN').show();
            }
        });
    }   
}


function setCheckboxEvent(idx){
    $('#TOTAL_CODE_CREATE_'+idx).click(function(){
        $('input[type=checkbox][name=CODE_CREATE_'+idx+']').prop('checked',$(this).is(':checked')) ;
    });
    $('#TOTAL_CODE_UPDATE_'+idx).click(function(){
        $('input[type=checkbox][name=CODE_UPDATE_'+idx+']').prop('checked',$(this).is(':checked')) ;
    });
    $('#TOTAL_CODE_READ_'+idx).click(function(){
        $('input[type=checkbox][name=CODE_READ_'+idx+']').prop('checked',$(this).is(':checked')) ;
    });
    $('#TOTAL_CODE_DELETE_'+idx).click(function(){
        $('input[type=checkbox][name=CODE_DELETE_'+idx+']').prop('checked',$(this).is(':checked')) ;
    });
    $('#TOTAL_CODE_EXCEL_'+idx).click(function(){
        $('input[type=checkbox][name=CODE_EXCEL_'+idx+']').prop('checked',$(this).is(':checked')) ;
    });

}

function setAutorityTable(authorityList){
    let tableArray = [];
    $('.title-html').each(function(){   // 타이틀이같은 아이들끼리 정렬
        let title = $(this).html();
        const array = authorityList.filter(function(item){
            return title == item.NUI_1ST;
        });
        tableArray.push(array);  
    });

    tableArray.map(function(TABLE_ITEM, index){
        setCheckboxEvent(index);
        let tableHtml = "";
        TABLE_ITEM.map(function(item, idx){
            let CREATE_CHECKED = "";
            let READ_CHECKED = "";
            let UPDATE_CHECKED = "";
            let DELETE_CHECKED = "";
            let EXCEL_CHECKED = "";

            tableHtml += '<tr>';
            tableHtml += '<td class="text-left">'+item.NUI_2ND;
            if(item.NUI_3RD != ''){
                tableHtml += ' > '+item.NUI_3RD;
            }
            if(item.NUI_4TH != ''){
                tableHtml += ' > '+item.NUI_4TH;
            }
            
            /** 수정모드시 체크 여부 데이터가 있으면.. */
            if(item.ALLS_CREATE_YN != undefined){
                CREATE_CHECKED = (item.ALLS_CREATE_YN == YN_Y) ? 'checked' : '';
            }
            if(item.ALLS_READ_YN != undefined){
                READ_CHECKED = (item.ALLS_READ_YN == YN_Y) ? 'checked' : '';
            }
            if(item.ALLS_UPDATE_YN != undefined){
                UPDATE_CHECKED = (item.ALLS_UPDATE_YN == YN_Y) ? 'checked' : '';
            }
            if(item.ALLS_DELETE_YN != undefined){
                DELETE_CHECKED = (item.ALLS_DELETE_YN == YN_Y) ? 'checked' : '';
            }
            if(item.ALLS_EXCEL_YN != undefined){
                EXCEL_CHECKED = (item.ALLS_EXCEL_YN == YN_Y) ? 'checked' : '';
            }

            tableHtml += '</td>';
            tableHtml += '<td class="text-center"><input type="checkbox" id="CODE_CREATE_'+idx+'" name="CODE_CREATE_'+index+'"';
            tableHtml += ' '+CREATE_CHECKED+' value='+JSON.stringify({TYPE:'CREATE', URI:item.NUI_URI, NUI_ID : item.NUI_ID})+'></td>'
            tableHtml += '<td class="text-center"><input type="checkbox" id="CODE_READ_'+idx+'" name="CODE_READ_'+index+'"';
            tableHtml += ' '+READ_CHECKED+' value='+JSON.stringify({TYPE:'READ', URI:item.NUI_URI, NUI_ID : item.NUI_ID})+'></td>'
            tableHtml += '<td class="text-center"><input type="checkbox" id="CODE_UPDATE_'+idx+'" name="CODE_UPDATE_'+index+'"';
            tableHtml += ' '+UPDATE_CHECKED+' value='+JSON.stringify({TYPE:'UPDATE', URI:item.NUI_URI, NUI_ID : item.NUI_ID})+'></td>'
            tableHtml += '<td class="text-center"><input type="checkbox" id="CODE_DELETE_'+idx+'" name="CODE_DELETE_'+index+'"';
            tableHtml += ' '+DELETE_CHECKED+' value='+JSON.stringify({TYPE:'DELETE', URI:item.NUI_URI, NUI_ID : item.NUI_ID})+'></td>'
            tableHtml += '<td class="text-center"><input type="checkbox" id="CODE_EXCEL_'+idx+'" name="CODE_EXCEL_'+index+'"';
            tableHtml += ''+EXCEL_CHECKED+' value='+JSON.stringify({TYPE:'EXCEL', URI:item.NUI_URI, NUI_ID : item.NUI_ID})+'></td>'
            tableHtml += '</tr>'; 
        });
        $('#TOTAL_CODE_'+index+'_TABLE').html(tableHtml);
    });

    $('input[type=checkbox]').change(function(){
        const NUI_NM = $(this).attr('name');
        const NUI_TOTAL_CNT = $('input[type=checkbox][name='+NUI_NM+']').length;
        const NUI_CHECKED_CNT = $('input[type=checkbox][name='+NUI_NM+']:checked').length;
        if(NUI_TOTAL_CNT == NUI_CHECKED_CNT){
            $('#TOTAL_'+NUI_NM).prop('checked',true);
        }else{
            $('#TOTAL_'+NUI_NM).prop('checked',false);
        }
    });

    if($('#ORI_ALLM_ID').val() != ''){  // 수정모드일 경우
        $('input[type=checkbox]').trigger('change');
    }
   
}

function getAuthorityList(){
    const data = {
        REQ_MODE: CASE_LIST_SUB
    };
    callAjax(AUTHORITY_URL, 'GET', data).then((result)=>{
        const data = JSON.parse(result);
        if(data.status == OK){
            setAuthorityTitleHeader(data.data);
        }else{
            setHaveNoDataTable();
        }
    });
}