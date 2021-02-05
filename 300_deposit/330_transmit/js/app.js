const TRANS_URL = 'act/transmit_act.php';
let CURR_PAGE = 1;
let tdLength = 0 ;
let PIE_LOW_PRICE = 0;
let PIE_HIGH_PRICE = 0;
let listArray = [] ;

const registPopupWidth = 515; 
const registPopupHeight = 700; 
let popupX = 0;
let popupY = 0;

$(document).ready(function() {

    popupX = (screen.availWidth - registPopupWidth) / 2;
    if( window.screenLeft < 0){
        popupX += window.screen.width*-1;
    }
    else if ( window.screenLeft > window.screen.width ){
        popupX += window.screen.width;
    }
    popupY= (window.screen.height / 2) - (registPopupHeight / 2);


    tdLength = $('table tr th').length;
    setInitTableData(tdLength);
    /** 검색 */
    $("#searchForm").submit(function(e){
        e.preventDefault();
            getList(1);
    });
    getENV();
    
    $("#START_DT_SEARCH").datepicker(
        getDatePickerSetting('END_DT_SEARCH', 'minDate')
    );
    $("#END_DT_SEARCH").datepicker(
        getDatePickerSetting('START_DT_SEARCH', 'maxDate')
    );


    $("#RICT_START_DT_SEARCH").datepicker(
        getDatePickerSetting('RICT_END_DT_SEARCH', 'minDate')
    );
    $("#RICT_END_DT_SEARCH").datepicker(
        getDatePickerSetting('RICT_START_DT_SEARCH', 'maxDate')
    );

    setIwOptions('NBIT_FROM_MII_ID_SEARCH',undefined,'=보험사(이첩전)=');    //이첩전보험사
    setIwOptions('NBIT_TO_MII_ID_SEARCH',undefined,'=보험사(이첩후)=');      //이첩후보험사
    getDivisionOption('CSD_ID_SEARCH', true);   
    setCRR_ID_Option('CSD_ID_SEARCH', 'CRR_ID_SEARCH', true);
    setGwOptions('MGG_NM_SEARCH', undefined, '=공업사명=');
    getRefOptions(GET_REF_EXP_TYPE, 'RET_ID_SEARCH', true, false);                 //청구식
    getRefOptions(GET_REF_CLAIM_TYPE, 'RLT_ID_SEARCH', false, false);              //담보
    getRefOptions(REF_FAX_STATUS, 'NBI_SEND_FAX_YN_SEARCH', false, false);         //전송여부
    getRefOptions(GET_REF_INSU_CHAGE_TYPE, 'RICT_ID_SEARCH', true, false);         //전송여부
    getRefOptions(GET_REF_INSU_CHAGE_TYPE, 'RICT_ID', false, false);                 //담당구분


     /* 구분에 따른 지역 변경 */
    $('#CSD_ID').change(()=>{
        if($('#CSD_ID').val() != ''){
            setCRR_ID('CSD_ID', 'CRR_ID', 'SELECT_CRR_ID', true);
        }else{
            $('#CRR_ID').val('');
            $('#SELECT_CRR_ID').val('');
            $('#CRR_ID').attr('disabled',true);
        }
    });

    $('#CSD_ID_SEARCH').change(()=>{
        if($('#CSD_ID_SEARCH').selectpicker('val') != null){
            setCRR_ID_Option('CSD_ID_SEARCH', 'CRR_ID_SEARCH', true, true);
        }else{
            $('#CSD_ID').selectpicker('val' , '');
            $('#CSD_ID').selectpicker('refresh');
            setCRR_ID_Option('CSD_ID_SEARCH', 'CRR_ID_SEARCH', true);
        }

    });

    // 전체선택 체크
    $("#allcheck").click(function(){
        if($("#allcheck").prop("checked")){
            $("input[name=multi]").prop("checked", true);
        }else{
            $("input[name=multi]").prop("checked", false);
        }
    });
});


getENV = () => {
    const param = {
        REQ_MODE : CASE_READ
    };
    callAjax(TRANS_URL, 'GET', param).then((result)=>{
        console.log('getENV SQL>>>',result);
        const res = JSON.parse(result);
        console.log('getEN >>>', res);
        if(res.status == OK){
            PIE_LOW_PRICE = Number(res.data.PIE_LOW_PRICE);
            PIE_HIGH_PRICE = Number(res.data.PIE_HIGH_PRICE);
        }else{
            PIE_LOW_PRICE = 0;
            PIE_HIGH_PRICE = 0;
        } 
         
      });

}

/**
 * MAIN LIST 
 * @param {int} currentPage  조회하는 페이지
 */
getList = (currentPage) =>{
    if($('#START_DT_SEARCH').val() !='' || $('#END_DT_SEARCH').val() != '' ){
      if($('#DT_KEY_SEARCH').val() == ''){
        alert('기간 키필드를 선택해주세요.');
        return false;
      }
    }
    
    CURR_PAGE = (currentPage == undefined) ? CURR_PAGE : currentPage;
    let form = $("#searchForm")[0];
    let formData = new FormData(form);
    formData.append('REQ_MODE', CASE_LIST);
    formData.append('PER_PAGE', $("#perPage").val());
    formData.append('CURR_PAGE', CURR_PAGE);
    $(".indecator").show();
    callFormAjax(TRANS_URL, 'POST', formData).then((result)=>{
        console.log('getList SQL >>',result);
        const res = JSON.parse(result);
        console.log( 'getList >> ',res);
        if(res.status == OK){
            $(".indecator").hide();
            const item = res.data;
            setTable(item[1], item[0], CURR_PAGE);  
            
        }else{
            $(".indecator").hide();
            setHaveNoDataTable();

        }
       
    });  
  }

/** 메인 리스트 테이블 동적으로 생성 
 * @param {totalListCount: 총 리스트데이터 수, dataArray : 리스트 데이터, currentPage : 현재페이지(페이징을 위해)} 
*/
setTable = (totalListCount, dataArray, currentPage) => {
    let tableText = '';

    listArray = dataArray;
    dataArray.map((data, index)=>{
        let RICT = null;
        let RICT_BTN = '';
        if(data.RICT_INFO != null){
            RICT = data.RICT_INFO.split('||');
        }else{
            RICT_BTN = "<button type='button' class='btn btn-default' onclick='showModal("+JSON.stringify(data)+")'>담당지정</button>";
        }
        
        tableText += "<tr>";
        // tableText += "  <td class='text-center'><input type='checkbox' name='multi' value='"+index+"'></td>";
        tableText += "  <td class='text-center'>"+data.IDX+"</td>";
        tableText += "  <td class='text-center'>"+data.MGG_ID+"</td>";
        tableText += "  <td class='text-center'>"+data.NB_CLAIM_DT+"</td>";
        tableText += "  <td class='text-left'>"+data.NBIT_FROM_MII_NM+"</td>";
        tableText += "  <td class='text-center'>"+data.NBIT_FROM_NIC_NM_AES+"</td>";
        tableText += "  <td class='text-left'>"+data.NBIT_TO_MII_NM+"</td>";
        tableText += "  <td class='text-center'>"+data.NBIT_TO_NIC_NM_AES+"</td>";
        tableText += "  <td class='text-center'>"+(data.NBIT_TO_NIC_PHONE_AES != null ? phoneFormat(data.NBIT_TO_NIC_PHONE_AES): '-' )+"</td>";
        tableText += "  <td class='text-center'>"+(RICT != null ? RICT[1]: '-')+"</td>";
        tableText += "  <td class='text-center'>"+data.CSD_NM+"</td>";
        tableText += "  <td class='text-center'>"+data.CRR_NM+"</td>";
        tableText += "  <td class='text-center'>"+data.MGG_NM+"</td>";
        tableText += "  <td class='text-center'>"+bizNoFormat(data.MGG_BIZ_ID)+"</td>";
        tableText += "  <td class='text-center'>"+data.RET_NM+"</td>";
        tableText += "  <td class='text-center'>"+data.NBI_REGI_NUM+"</td>";
        tableText += "  <td class='text-center'>"+data.RLT_NM+"</td>";
        tableText += "  <td class='text-center'>"+data.NB_CAR_NUM+"</td>";
        tableText += "  <td class='text-center'>"+data.CCC_NM+"</td>";
        tableText += "  <td class='text-center'>"+data.TRANS_DT+"</td>";
        tableText += "  <td class='text-center'>"+data.NBIT_MEMO+"</td>";
        tableText += "  <td class='text-center'>"+data.RMT_NM+"</td>";
        tableText += "  <td class='text-center'><a href='javascript:showTransHositoryModal("+JSON.stringify(data)+")'>"+data.TRANS_CNT+"</a></td>";
        tableText += "  <td class='text-center'>"+data.RFS_NM+"</td>";
        tableText += "  <td class='text-center'>"+RICT_BTN;
        tableText += "      <button type='button' class='btn btn-default' onclick='showTransitModal("+index+")'>이첩</button>";
        tableText += "  </td>";
        tableText += "  <td class='text-center'>"+(RICT != null ? RICT[2]: '-')+"</td>";
        tableText += "  <td class='text-center'>"+(RICT != null ? RICT[1]: '-')+"</td>";
        tableText += "  <td class='text-center'>"+(RICT != null ? '지정': '미지정')+"</td>";
        tableText += "</tr>       ";
    });
    paging(totalListCount, currentPage, 'getList'); 
    
    $('#dataTable').html(tableText);
}

/** 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
     
    const tableText = "<tr><td class='text-center' colspan='"+tdLength+"'>조회된 데이터가 존재하지 않습니다.</tr></tr>";
    $('.pagination').html('');
    $('#dataTable').html(tableText);
}

/* 이첩이력 모달 */
showTransHositoryModal = (data) =>{
    $('#transHistory').modal('toggle');
    $('#NB_ID').val(data.NB_ID);
    getSubList(data.NB_ID);
}

/* 이첩이력 리스트 조회 */
getSubList = (nb,subPage) => {
    let data = {
        REQ_MODE: CASE_LIST_SUB,
        NB_ID: nb,
        PER_PAGE: 10,
        CURR_PAGE:  subPage == undefined? 1: subPage,
    };
  
    callAjax(TRANS_URL, 'GET', data).then(function(result){
        console.log('getSubList result >> ',result);
        let res = JSON.parse(result);
        console.log(res);
        if(res.status == OK){
            let item = res.data;
            setSubTable(item[1], item[0], subPage, data.PER_PAGE);
        }else{
            setHaveNoDataLogTable();
        }
    });
  }
  
/** 서브 리스트 테이블 동적으로 생성 
 * @param {dataArray : 리스트 데이터, currentPage : 현재페이지(페이징을 위해)} 
*/
setSubTable = (totalListCount, dataArray, currentPage, perPage)=>{
    $('#subPagination li').remove();
    let subTableText = '';
    dataArray.map(function(data, index){
      subTableText += "<tr>";
      subTableText += "<td class='text-center'>"+data.WRT_DTHMS+"</td>";
      subTableText += "<td class='text-center'>"+nullChk(data.NB_CLAIM_DT)+"</td>";
      subTableText += "<td class='text-center'>"+nullChk(data.NBIT_FROM_MII_NM)+"</td>";
      subTableText += "<td class='text-center'>"+nullChk(data.NBIT_FROM_NIC_NM_AES)+"</td>";
      subTableText += "<td class='text-center'>"+nullChk(data.NBIT_TO_MII_NM)+"</td>";
      subTableText += "<td class='text-center'>"+nullChk(data.NBIT_TO_NIC_NM_AES)+"</td>";
      subTableText += "<td class='text-center'>"+data.MGG_NM+"</td>";
      subTableText += "<td class='text-center'>"+nullChk(data.NBI_REGI_NUM)+"</td>";
      subTableText += "<td class='text-center'>"+nullChk(data.NB_CAR_NUM)+"</td>";
      subTableText += "</tr>";
        
    });
    subPaging(totalListCount, currentPage, 'getSubList', perPage); 
    $('#historyDataTable').html(subTableText);
}
  
/** 서브 리스트 빈값일 때 동적테이블처리 */
setHaveNoDataLogTable = () =>{
    let subTableText = "<tr><td class='text-center' colspan='9'>조회요청한 데이터가 존재하지 않습니다.</td></tr>";
    $('#subPagination').html('');
    $('#historyDataTable').html(subTableText);
}


/* 이첩팝업 띄우기 */
showTransitModal = (idx) => { 
    let form = $('#sendTransForm')[0];
    let htmlTxt = ''
    const data = listArray[idx];
    console.log('transData>>>',data);
    htmlTxt += "<input type='hidden' name='NB_ID[]' value="+data.NB_ID+">";
    htmlTxt += "<input type='hidden' name='NBI_RMT_ID[]' value="+data.NBI_RMT_ID+">";
    htmlTxt += "<input type='hidden' name='NIC_ID[]' value="+data.NIC_ID+">";
    htmlTxt += "<input type='hidden' name='MII_ID[]' value="+data.MII_ID+">";
    htmlTxt += "<input type='hidden' name='INSU_TEAM_NM[]' value="+data.INSU_TEAM_NM+">";
    htmlTxt += "<input type='hidden' name='NBI_FAX_NUM[]' value="+data.NBI_FAX_NUM+">";
    htmlTxt += "<input type='hidden' name='NB_CLAIM_DT[]' value="+data.NB_CLAIM_DT+">";
    htmlTxt += "<input type='hidden' name='NBI_REGI_NUM[]' value="+data.NBI_REGI_NUM+">";
    htmlTxt += "<input type='hidden' name='MII_NM[]' value="+data.MII_NM+">";
    htmlTxt += "<input type='hidden' name='NBI_CHG_NM[]' value="+data.NIC_NM+">";
    htmlTxt += "<input type='hidden' name='NB_CAR_NUM[]' value="+data.NB_CAR_NUM+">";
    htmlTxt += "<input type='hidden' name='NB_TOTAL_PRICE[]' value="+data.NB_TOTAL_PRICE+">";

    window.open('', 
        'transwindow', 
        'width='+registPopupWidth+',height='+registPopupHeight+' ,left='+ popupX + ', top='+ popupY); 
    form.action = '/200_billMngt/220_sendList/sendListTransitPopup.html';
    form.target = 'transwindow';
    form.method = 'POST';
    $('#sendTransForm').html(htmlTxt);
    
    form.submit();
}
  
  
/* 담당지정 팝업 */
showModal = (data) =>{
    console.log(data);
    $('#NIC_ID').val(data.NIC_ID);
    $('#MGG_ID').val(data.MGG_ID);
    $('#MGG_NM').val(data.MGG_NM);

    data.NB_TOTAL_PRICE = Number(data.NB_TOTAL_PRICE);
    if(data.CCC_TYPE.substring(0,3) == '1012'){ //외제차
        $('#RICT_ID').val('11340');
    }else {
        if(data.NB_TOTAL_PRICE < PIE_LOW_PRICE){ //소액
            $('#RICT_ID').val('11310');
        }else if(data.NB_TOTAL_PRICE > PIE_HIGH_PRICE ){ // 고액
            $('#RICT_ID').val('11330');
        }else {// 일반
            $('#RICT_ID').val('11320');
        }
    }

    $('#add-new').modal('toggle');



} 

/* 담당지정 confirm */
RICTConfirm = () => {
    confirmSwalTwoButton('담당지정하시겠습니까?',OK_MSG, CANCEL_MSG, false ,()=>{
        const param = {
            REQ_MODE: CASE_CREATE,
            NIC_ID: $('#NIC_ID').val(),
            RICT_ID: $('#RICT_ID').val(),
            MGG_ID: $('#MGG_ID').val(),
        }
        console.log('confirm param >>>',param);
        callAjax(TRANS_URL, 'POST', param).then(function(result){
            console.log('confirm result >> ',result);
            let res = JSON.parse(result);
            console.log('confirm Res>>',res);
            if(res.status == OK){
                confirmSwalOneButton(SAVE_SUCCESS_MSG,OK_MSG,()=>{$('#add-new').modal('toggle');getList();});
            }else{
            }
        });
    });
    
}


excelDownload = () =>{
    location.href="transmitExcel.html?"+$("#searchForm").serialize();
}