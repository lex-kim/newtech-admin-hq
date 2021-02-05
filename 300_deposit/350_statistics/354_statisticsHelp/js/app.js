let STAT_URL = '/300_deposit/350_statistics/351_statisticsMain/act/statistics_act.php'; 
let CURR_PAGE = 1;
let tdLength = 0;
$(document).ready(function() {
    tdLength = $('table th').length;

    setInitTableData(tdLength,'helpTable');
    getRefOptions(REF_COOP_TYPE, 'HELP_RPT_ID', false, false);           // 협조구분 
    getRefOptions(REF_COOP_REACTION, 'HELP_RPR_ID', false, false);           // 협조반응 

    //  협조요청
    $("#HELP_DT").datepicker({
        dateFormat: 'yy-mm-dd',
    });

    getHelpList();

});


/**
 * MAIN LIST 
 * @param {int} currentPage  조회하는 페이지
 */
getHelpList = (currentPage) =>{
    
    CURR_PAGE = (currentPage == undefined) ? CURR_PAGE : currentPage;
    const param = {
        REQ_MODE: CASE_LIST_SUB,
        PER_PAGE: 10,
        CURR_PAGE: CURR_PAGE,
        NIC_ID: $('#HELP_NIC_ID').val()
    }
    callAjax(STAT_URL, 'POST', param).then((result)=>{
        console.log('getList SQL >>',result);
        const res = JSON.parse(result);
        console.log( 'getList >> ',res);
        if(res.status == OK){
            $(".indecator").hide();
            const item = res.data;
            let subTableText = '';
            res.data[0].map((data, index)=>{
                console.log('help ',index,data);
                subTableText += "<tr>";
                subTableText += "<td class='text-center'>"+data.RPT_NM+"</td>";
                subTableText += "<td class='text-center'>"+data.NICC_DT+"</td>";
                subTableText += "<td class='text-center'>"+data.NICC_USERNM+"<br>("+data.NICC_USERID+")</td>";
                subTableText += "<td class='text-center'>"+data.RPR_NM+"</td>";
                subTableText += "<td class='text-left'>"+data.NICC_MEMO+"</td>";
                subTableText += "<td class='text-center'><button type='button' class='btn btn-danger' onclick='delData("+data.NICC_ID+")'>삭제</button></td>";
                subTableText += "</tr>";
                  
              });
              subPaging(item[1], CURR_PAGE, 'getHeplList', 10); 
              $('#helpTable').html(subTableText);
            
        }else{
            $(".indecator").hide();
            setHaveNoDataLogTable();

        }
       
    });  
  }

  /** 빈값일 때 동적테이블처리 */
setHaveNoDataLogTable = () => {
    const subTableText = "<tr><td class='text-center' colspan='"+tdLength+"'>조회요청한 데이터가 존재하지 않습니다.</td></tr>";
    $('#subPagination').html('');
    $('#helpTable').html(subTableText);
  }
  

addHelp = () => {
    confirmSwalTwoButton('협조요청을 추가하시겠습니까?',OK_MSG, CANCEL_MSG, false ,()=>{
        const param = {
            REQ_MODE: CASE_CREATE,
            NIC_ID: $('#HELP_NIC_ID').val(),
            MII_ID: $('#HELP_MII_ID').val(),
            RPT_ID: $('#HELP_RPT_ID').val(),
            RPR_ID: $('#HELP_RPR_ID').val(),
            NICC_DT: $('#HELP_DT').val(),
            NICC_USERID: $('#HELP_ID').val(),
            NICC_MEMO: $('#HELP_MEMO').val()
        }
        console.log('confirm param >>>',param);
        callAjax(STAT_URL, 'POST', param).then(function(result){
            console.log('confirm result >> ',result);
            let res = JSON.parse(result);
            console.log('confirm Res>>',res);
            if(res.status == OK){
                confirmSwalOneButton(
                    SAVE_SUCCESS_MSG,
                    OK_MSG,
                    ()=>{
                        $('#helpForm').each(function(){
                            this.reset();
                        })
                        getHelpList();
                        window.opener.getList();
                    });
            }else{
                confirmSwalOneButton(
                    '협조요청에 실패하였습니다. 재시도 바랍니다.',
                    OK_MSG,
                    ()=>{
                        console.log(res.message);
                    });
            }
        });
    });
}


/* 데이터 삭제함수 */
delData = (targetId) => {
    confirmSwalTwoButton('삭제하시겠습니까?','삭제','취소', true,()=>{
        const data = {
            REQ_MODE: CASE_DELETE,
            NICC_ID:   targetId
        };
        callAjax(STAT_URL, 'POST', data).then((result)=>{
            const data = JSON.parse(result);
            if(data.status == OK){
                confirmSwalOneButton(
                  '삭제에 성공하였습니다.',
                  '확인',
                  ()=>{
                    getHelpList();
                  }
                );
            }else{
                const resultText ='서버와의 연결이 원활하지 않습니다.' ;
                alertSwal(resultText);
            } 
        });
    }) ;

  }