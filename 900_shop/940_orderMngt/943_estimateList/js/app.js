
let CURR_PAGE = 1;
let sendTitle = "";

$(document).ready(function() {
    const tdLength = $('table th').length;
    setInitTableData(tdLength);

    $('#searchForm').submit(function(e){
        e.preventDefault();
        getList(1);
    });

    /** FAX, SMS 발송 시 */
    $('#phoneForm').submit(function(e){
        e.preventDefault();

        confirmSwalTwoButton(''+phoneFormat($('#SEND_PHONE_NUM').val())+'로 '+sendTitle+' 발송 하시겠습니까?','확인','취소',false,function(){
            onClickSendSmsBtn();
        }); 
    });

    /** 발송 모달 닫기 이벤트 */
    $('#phoneNum-modal').on('hidden.bs.modal', function () {
        $('#SEND_PHONE_NUM').val("");
        sendTitle = "";
    });

    $( "#START_DT" ).datepicker(
        getDatePickerSetting('END_DT','minDate')
    );
    $( "#END_DT" ).datepicker(
        getDatePickerSetting('START_DT','maxDate')    
    );
});

/** 발송버튼 */
onClickSMSSend = (title, code) => {
    sendTitle = title;
    $('#sendTitle').html(title+" 발송");
    $('#sendMethodCode').val(code);
    $('#phoneNum-modal').modal();

    setTimeout(()=>{
        $('#SEND_PHONE_NUM').focus();
    },200);

}

/** 발송버튼 */
onClickSendSmsBtn = () => {
    let form = $("#phoneForm")[0];
    let formData = new FormData(form);
    formData.append('REQ_MODE', CASE_CREATE);

    callFormAjax(ERP_CONTROLLER_URL, 'POST', formData).then((result)=>{
        if(isJson(result)){
            const res = JSON.parse(result);
            if(res.status == OK){
                alert(sendTitle+"가 발송되었습니다.");
                window.location.reload();
                window.opener.getList();
            }else{
                alert(ERROR_MSG_1100);
            }
        }else{
            alert(ERROR_MSG_1200);
        }
        
    }); 
}









/** 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
    const tdLength = $('table th').length;
    const tableText = "<tr><td class='text-center' colspan='"+tdLength+"'>조회된 데이터가 존재하지 않습니다.</tr></tr>";
    $('.pagination').html('');
    $('#dataTable').html(tableText);
}
  
  
setTable = (dataList, totalListCount, cPage) => {
    let html = "";
    dataList.map((data)=>{
        html += "<tr>";
        html += "<td class='text-center'>"+data.issueCount+"</td>";
        html += "<td class='text-center'>"+data.issueDate+"</td>";
        html += "<td class='text-center'>"+data.quotationID+"</td>";
        html += "<td class='text-center'>"+data.quotationStatus+"</td>";
        html += "<td class='text-center'>"+(empty(data.contractStatus) ? "-" :data.contractStatus)+"</td>";
        html += "<td class='text-left'>"+(empty(data.divisionName) ? "-" :data.divisionName)+"</td>";
        html += "<td class='text-left'>"+(empty(data.regionName) ? "-" :data.regionName)+"</td>";
        html += "<td class='text-left'>"+(empty(data.garageName) ? "-" :data.garageName)+"</td>";
        html += "<td class='text-left'>"+data.toName+"</td>";
        html += "<td class='text-center'>"+data.toReceiverName+"</td>";
        html += "<td class='text-center'>"+phoneFormat(data.toPhoneNum) +"</td>";
        html += "<td class='text-left'>"+data.toEmail+"</td>";
        html += "<td class='text-left'>"+(empty(data.quotationDetails) ? "-" :data.quotationDetails)+"</td>";
        html += "<td class='text-right'>"+numberWithCommas(data.totalPrice)+"</td>";
        html += "<td class='text-right'>"+numberWithCommas(data.totalVAT)+"</td>";
        html += "<td class='text-right'>"+numberWithCommas(data.totalSum)+"</td>";
        html += "<td class='text-center'>"+data.deliveryMethod+"</td>";
        html += "<td class='text-center'>"+data.paymentName+"</td>";
        html += "<td class='text-center'>"+data.requestUser+"</td>";
        html += "<td class='text-center'>"+data.requestDatetime+"</td>";
        html += "<td class='text-center'>"+(empty(data.publishUser)?"-":data.publishUser)+"</td>";
        html += "<td class='text-center'>"+(empty(data.publishDatetime)?"-":data.publishDatetime)+"</td>";
        html += "<td class='text-center'>"+(empty(data.sendFaxYN)?"-":data.sendFaxYN)+"</td>";
        html += "<td class='text-center'>"+(empty(data.sendMessageYN)?"-":data.sendMessageYN)+"</td>";
        html += "<td class='text-center'>"+(empty(data.sendFaxDatetime)?"-":data.sendFaxDatetime)+"</td>";
        html += "<td class='text-center'>"+(empty(data.sendMessageDatetime)?"-":data.sendMessageDatetime)+"</td>";
        html += "<td class='text-center'>"+(empty(data.requestViewDatetime)?"-":data.requestViewDatetime)+"</td>";
        html += "<td class='text-center listbtn'>";
        if(data.quotationStatusCode == EC_QUOTATION_STATUS_ASK){
            html += "<a><button type='button' class='btn btn-default' onclick='onEstimate(\""+data.quotationID+"\")'>견적</button></a>";
        }else{
            html += "<a onclick='openDetailPopup("+JSON.stringify(data)+")'><button type='button' class='btn btn-default'>조회</button></a>";
        }

        if(data.quotationStatusCode == EC_QUOTATION_STATUS_COMPLETE || data.quotationStatusCode == EC_QUOTATION_STATUS_ASK){
            html += "<a><button type='button' class='btn btn-primary' onclick='onClickOrder(\""+data.quotationID+"\",\""+data.garageID+"\")'>주문</button></a></td>";
        }
        
        html += "<td><button type='button' class='btn btn-danger option_subtract_btn' onclick='confirmDelete("+JSON.stringify(data)+")'>삭제</button></td>";
        html += "</tr>"
    });
    paging(totalListCount, cPage, 'getList'); 
    $('#dataTable').html(html);
}
  
excelDownload = () => {
    location.href="estimateExcel.php?"+$("#searchForm").serialize()+"&REQ_MODE="+CASE_TOTAL_LIST;
}
  
getList = (page) => {
    CURR_PAGE = (page == undefined ) ? CURR_PAGE : page;
    let form = $("#searchForm")[0];
    let formData = new FormData(form);
    formData.append('REQ_MODE', CASE_LIST);
    formData.append('PER_PAGE', $("#perPage").val());
    formData.append('CURR_PAGE', CURR_PAGE);

    callFormAjax(ERP_CONTROLLER_URL, 'POST', formData).then((result)=>{
        if(isJson(result)){
            const res = JSON.parse(result);
            if(res.status == OK){
                if(res.data == HAVE_NO_DATA){
                    setHaveNoDataTable();
                }else{
                    setTable(res.data, res.total_item_cnt, CURR_PAGE);
                } 
            }else{
                 alert(ERROR_MSG_1100);
            }
        }else{
            alert(ERROR_MSG_1200);
        }
        
    });  
}

 /** 삭제 */
 function confirmDelete(data){
    confirmSwalTwoButton(CONFIRM_DELETE_MSG, DEL_MSG, CANCEL_MSG, true,()=>{
        delData(data.quotationID)
    }, "");
}

function delData(quotationID) {
    
    const data = {
        REQ_MODE: CASE_DELETE,
        quotationID:   quotationID
    };

    callAjax(ERP_CONTROLLER_URL,'POST', data).then((result)=>{
        const data = JSON.parse(result);
        if(data.status == OK){
            alert("삭제에 성공하였습니다.");
            getList();
        }else{
            alert(ERROR_MSG_1200);
        } 
    });
  
}

function onPrint(){
    $('.bill-footer').hide();
    window.print();
    window.close();
    $('.bill-footer').show();
}

clickPhoneNum = () =>{
    $('#phoneNum-modal').modal();
}

// 리스트 주문 버튼
function openDetailPopup(data){
    const url = '/900_shop/940_orderMngt/943_estimateList/estimateListDetail.php?quotationID='+data.quotationID;
    openWindowPopup(url, 'estimateListDetail', 995, 860);
}

// 주문서작성 버튼 클릭
onClickEstimate = () => {
    const url = '/900_shop/940_orderMngt/942_order/estimatePopup.php';
    openWindowPopup(url, 'estimate', 1320, 913);
}

// 리스트의 견적 버튼을 클릭
onEstimate = (quotationID) => {
    const url = '/900_shop/940_orderMngt/942_order/estimatePopup.php?quotationID='+quotationID;
    openWindowPopup(url, 'estimatePopup', 1320, 913);
}

function isHaveSoldOutItem(){
    let isHaveSolodOut = false;

    $('.itemText').each(function(item){
        if($(this).val() == "Y"){
        isHaveSolodOut = true;
        }
    });
    return isHaveSolodOut;
}
  
//리스트의 주문버튼, 견적서 조회의 주문버튼
onClickOrder = (quotationID, garageID) =>{
    if(isHaveSoldOutItem()){
        alert("품절된 상품이 존재합니다.");
    }else{
        const url = '/900_shop/940_orderMngt/942_order/orderSheetPopup.php?quotationID='+quotationID+"&garageID="+garageID;
        openWindowPopup(url, 'estimatePopup', 1420, 860);
    }
    
}

