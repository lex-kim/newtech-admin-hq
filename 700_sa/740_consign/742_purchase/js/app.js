let CURR_PAGE = 1;
const BUY_URL = 'act/purchase_act.php';
let tdLength = 0 ;
let ctgyArray = [];


$(document).ready(() =>{
    tdLength = $('table th').length;
    setInitTableData(tdLength);

    /** 검색 */
    $("#searchForm").submit(function(e){
        e.preventDefault();
            getList(1);
    });

    /* datepicker 설정 */
    $("#START_DT_SEARCH").datepicker(
        getDatePickerSetting('END_DT_SEARCH', 'minDate')
    );
    $("#END_DT_SEARCH").datepicker(
        getDatePickerSetting('START_DT_SEARCH', 'maxDate')
    );
   
    setConsignType('NCC_ID_SEARCH','=장비구분=');
    setConsign('NCP_NM_SEARCH','=장비=',undefined ,true)

    /* 위탁장비 등록/수정 */
    $('#purchaseForm').submit(function(e){
        e.preventDefault();
        if($('#REQ_MODE').val() == CASE_UPDATE){
          confirmSwalTwoButton('수정 하시겠습니까?','확인', '취소', false ,()=>setData());
        }else{
          confirmSwalTwoButton('저장 하시겠습니까?','확인', '취소', false ,()=>setData());
        }
    });

    /* 입고 등록 */
    $('#stockForm').submit(function(e){
        e.preventDefault();
          confirmSwalTwoButton('저장 하시겠습니까?','확인', '취소', false ,()=>addStock());
    });
});


showAddModal = () =>{
    $('#purchaseForm').each(function(){
        // console.log('reset ID >>',$(this).attr('id'));
        this.reset();
    })
    console.log('add>>', $("#purchaseForm")[0]);

    $('#REQ_MODE').val(CASE_CREATE);
    $('#IS_EXIST_ORDER_NUM').val('');
    $('#ORI_NCP_ID').val('');

    setConsignType('NCC_ID','=선택=');
    $('#add-new').modal('toggle');
}

/** 등록/수정시 데이터 set 해주는 함수 */
setData = () => {
    console.log('set data>>>>',$('#IS_EXIST_ORDER_NUM').val(), ' ', $('#ORI_NCP_ID').val());
    const form = $("#purchaseForm")[0];
    const formData = new FormData(form);
    
    callFormAjax(BUY_URL, 'POST', formData).then((result)=>{
        console.log('setData SQL>>',result);
        const data = JSON.parse(result);
        console.log('res>>',data);
        if(data.status == OK){
            let confirmText = "등록에 성공하였습니다.";
            if($('#REQ_MODE').val() == CASE_UPDATE){
                confirmText = '수정에 성공하였습니다.';
            }

            confirmSwalOneButton(confirmText,'확인',()=>{
                getList();
                // if($('#REQ_MODE').val() == CASE_UPDATE){
                $('#add-new').modal('hide');
                // }
            });
        }else if(data.status == ERROR){
            confirmSwalTwoButton(data.message,'확인', '취소', false ,()=>{
              $('#IS_EXIST_ORDER_NUM').val('false');
              setData();
          }, '진행하시겠습니까?');
        }else{
            alert(data.message);
        } 
    });  
}

getList = (currentPage) => {
    CURR_PAGE = (currentPage == undefined) ? CURR_PAGE : currentPage;
    let form = $("#searchForm")[0];
    let formData = new FormData(form);
    formData.append('REQ_MODE', CASE_LIST);
    formData.append('PER_PAGE', $("#perPage").val());
    formData.append('CURR_PAGE', CURR_PAGE);

    callFormAjax(BUY_URL, 'POST', formData).then((result)=>{
        console.log('getList SQL >>',result);
        const res = JSON.parse(result);
        console.log( 'getList >> ',res);
        if(res.status == OK){
            const item = res.data;
            setTable(item[1], item[0], CURR_PAGE);  
        }else{
            setHaveNoDataTable();
        }
    });  
}

/** 메인 리스트 테이블 동적으로 생성 
 * @param {totalListCount: 총 리스트데이터 수, dataArray : 리스트 데이터, currentPage : 현재페이지(페이징을 위해)} 
*/
setTable = (totalListCount, dataArray, currentPage) => {
    let tableText = '';
    dataArray.map((data, index)=>{
        console.log(index,' ',data);
        tableText += "<tr>";
        tableText += "  <td class='text-center'>"+data.IDX+"</td>";
        tableText += "  <td class='text-center'>"+data.NCP_ORDER_NUM+"</td>";
        tableText += "  <td class='text-center'>"+data.NCC_NM+"</td>";
        tableText += "  <td class='text-center'>"+data.NCP_NM+"</td>";
        tableText += "  <td class='text-center'>"+data.NCP_ID+"</td>";
        tableText += "  <td class='text-center'>"+data.NCP_MANUFACTURER+"</td>";
        tableText += "  <td class='text-center'>"+numberWithCommas(data.NCP_PRICE)+"</td>";
        tableText += "  <td class='text-center'>"+numberWithCommas(data.NCP_CNT)+"</td>";
        tableText += "  <td class='text-center'>"+(data.NCP_USE_YN == YN_Y ? '사용' : '미사용')+"</td>";
        tableText += "  <td class='text-center'>"+(data.WRT_USERID == null ? '-' : data.WRT_USER_NM+'('+data.WRT_USERID+')')+"</td>";
        tableText += "  <td class='text-center'>"+data.WRT_DTHMS+"</td>";
        tableText += "  <td class='text-center'><button type='button' class='btn btn-default' onclick='showStockModal(\""+data.NCP_ID+"\",\""+data.NCP_CNT+"\")'>입고</button></td>";
        if($("#AUTH_UPDATE").val() == YN_Y){
            tableText += "  <td class='text-center'><button type='button' class='btn btn-default' onclick='showUpdateModal("+JSON.stringify(data)+")'>수정</button></td>";
        }

       
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

showUpdateModal = (data) => {
    $('#REQ_MODE').val(CASE_UPDATE);
    $('#IS_EXIST_ORDER_NUM').val('');
    $('#ORI_NCP_ID').val(data.NCP_ID);
    $('#NCP_ORDER_NUM').val(data.NCP_ORDER_NUM);
    setConsignType('NCC_ID','=선택=',data.NCC_ID);
    $('#NCP_NM').val(data.NCP_NM);
    $('#NCP_ID').val(data.NCP_ID);
    $('#NCP_MANUFACTURER').val(data.NCP_MANUFACTURER);
    $('#NCP_PRICE').val(data.NCP_PRICE);
    $('#NCP_USE_YN').val(data.NCP_USE_YN);
    $('#submitBtn').html('수정');
    $('#add-new').modal('toggle');

}

showStockModal = (pk,cnt) =>{
    console.log('stockId ', pk);
    $('#stockForm').each(function(){
        this.reset();
    })
     /* datepicker 설정 */
     $("#NCPD_STOCK_DT").datepicker(
        getDatePickerSetting()
     );
     $('#STOCK_NCP_ID').val(pk);
     $('#STOCK_NCP_CNT').val(cnt);
    
    $('#stock-modal').modal('toggle');
}

addStock = () => {
    const form = $("#stockForm")[0];
    const formData = new FormData(form);
    formData.append('REQ_MODE',CASE_UPDATE_TEAM);
    callFormAjax(BUY_URL, 'POST', formData).then((result)=>{
        console.log('setData SQL>>',result);
        const data = JSON.parse(result);
        if(data.status == OK){
            let confirmText = "등록에 성공하였습니다.";
            confirmSwalOneButton(confirmText,'확인',()=>{
                getList();
                // if($('#REQ_MODE').val() == CASE_UPDATE){
                $('#stock-modal').modal('hide');
                // }
            });
        }else{
            alert(data.message);
            $('#stock-modal').modal('hide');
            getList();
        } 
    });  
}

excelDownload = () => {
    location.href="purchaseExcel.html?"+$("#searchForm").serialize();

}

