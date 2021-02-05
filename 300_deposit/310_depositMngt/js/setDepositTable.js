/** 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
    const tdLength = $('table tr th').length;
    const tableText = "<tr><td class='text-center' colspan='"+tdLength+"'>조회된 데이터가 존재하지 않습니다.</tr></tr>";
    $('.pagination').html('');
    $('#dataTable').html(tableText);
}

function confirmReleaseDuplicate(NB_ID){
    confirmSwalTwoButton(
        '중복해제 하시겠습니까?','확인', '취소', false ,function(){
            setReleaseDuplicateBill(NB_ID);
        }
    );
}

/** 메인 리스트 테이블 동적으로 생성 
 * @param {totalListCount: 총 리스트데이터 수, dataArray : 리스트 데이터, currentPage : 현재페이지(페이징을 위해)} 
*/
setTable = (totalListCount, dataArray, currentPage) => {
    let tableText = '';
    listArray = dataArray;
    dataArray.map((data, index)=>{
        let cssDupli = ''
        let txtDupli = ''
        if(data.NB_DUPLICATED_YN == YN_Y){ // 중복일때 
            cssDupli = "style='background-color: #ffdddd;'";
            txtDupli = "<span class='red'>중복 </span>";
            txtDupli += "<a href='javascript:confirmReleaseDuplicate(\""+data.NB_ID+"\");'><span style='font-size:11px'>중복해제</span></a></br>";

        }else{
            if(data.RDET_ID == '40121'){ //입금여부 미입금일때 
                cssDupli = "style='background-color: #cfecbb;'";
            }else if(data.RFS_ID == '90210'){ //전송여부 미전송일때
                cssDupli = "style='background-color: #b1dcb6;'";
            }
        }


        let  depositTxt = ""; //입금여부 표기
        if(data.NBI_REASON_DTL_ID == null){
            if(data.NBI_INSU_DEPOSIT_YN == YN_N){
                depositTxt = '미입금';
            }else if(data.NBI_INSU_DEPOSIT_YN == YN_Y){
                depositTxt = '입금';
            }else{
                depositTxt = '-';
            }
        }else{
            console.log('미처리!!!!!!!!',data.NBI_REASON_DTL_ID,'/',data.NBI_PROC_USERID);
            if(data.NBI_REASON_DTL_ID.length == 5 && data.NBI_INSU_DONE_YN == YN_N){
                depositTxt = '<span class="green">미</span>';
            }else if(data.NBI_INSU_DONE_YN == YN_Y ){
                if(data.RDET_ID == '40121'){
                    depositTxt = '미입금';
                }else {
                    depositTxt = '입금';
                }
            }else{
                depositTxt = '1-';
            }
            
        }

        let workHour =''; //방청시간 분 표기 -> 시 표기
        if(parseInt(data.NBW_TIME/60) > 0){
            workHour = parseInt(data.NBW_TIME/60)+"시간 "+(data.NBW_TIME%60)+"분 ";
        }else if(data.NBW_TIME == null){
            workHour = '-';
        }else{
            workHour = (data.NBW_TIME%60)+"분 ";
        }

        let btnCarown = '-'; //차주입금분 처리버튼
        if(data.NBI_CAROWN_PRICE > 0 ){
            btnCarown = "<button type='button' class='btn btn-default' onclick='showCarownModal("+JSON.stringify(data)+")'>처리</button>";
        }

        let chkVal = data.NB_ID+'|'+data.NBI_RMT_ID+'|'+data.NIC_ID+'|'+data.MII_ID+'|'+data.NIC_NM+'|'+data.NIC_FAX_AES;
        tableText += "";
        tableText += "<tr "+cssDupli+">";
        tableText += "<td class='text-center'><input type='checkbox' class='listcheck' name='multi' id='"+ data.NB_ID+'|'+data.NBI_RMT_ID+"' value='"+index+"'></td>";
        tableText += "<td class='text-center'>"+data.IDX+"</td>";
        tableText += "<td class='text-center hidetd'>"+data.MGG_ID+"</td>";
        tableText += "<td class='text-center'>"+data.NB_CLAIM_DT+"</td>";
        tableText += "<td class='text-left'>"+(data.MII_NM != null ? data.MII_NM : '-')+"</td>";
        tableText += "<td class='text-left'>"+(data.MIT_NM != null ? data.MIT_NM : '-')+"</td>";
        tableText += "<td class='text-left hidetd'>"+(data.NBI_TEAM_MEMO != null ? data.NBI_TEAM_MEMO : '-')+"</td>";
        tableText += "<td class='text-center'>"+(data.NIC_NM != null ? data.NIC_NM : '-')+"</td>";
        tableText += "<td class='text-center hidetd'>"+nullChk(data.NB_RMT_NM)+"</td>";
        tableText += "<td class='text-center'>"+(data.NBI_FAX_NUM!= null? phoneFormat(data.NBI_FAX_NUM) : '-')+"</td>";

        tableText += "<td class='text-center'>"+(data.NIC_TEL1_AES != null? phoneFormat(data.NIC_TEL1_AES) : '-')+"</td>";
        tableText += "<td class='text-center'>"+data.CSD_NM+"</td>";
        tableText += "<td class='text-left hidetd'>"+data.CRR_NM+"</td>";
        tableText += "<td class='text-left'>"+data.MGG_NM+"</td>";
        tableText += "<td class='text-center hidetd'>"+bizNoFormat(data.MGG_BIZ_ID)+"</td>";
        tableText += "<td class='text-center'>"+(data.RET_NM != null ? data.RET_NM : '-')+"</td>";
        tableText += "<td class='text-center hidetd'>"+data.RPET_NM+"</td>";
        tableText += "<td class='text-center hidetd'>"+data.MGG_FRONTIER_NM+"</td>";
        tableText += "<td class='text-center hidetd'>"+data.MGG_BIZ_START_DT+"</td>";
        tableText += "<td class='text-center hidetd'>"+(data.MGG_BIZ_END_DT != null ? data.MGG_BIZ_END_DT : '-')+"</td>";

        tableText += "<td class='text-center hidetd'>"+data.MODE_TARGET+"</td>"; //작성주체
        tableText += "<td class='text-center hidetd'>"+data.NBI_PRECLAIM_YN+"</td>";
        tableText += "<td class='text-center'>"+data.NBI_REGI_NUM+"</td>"; //접수번호
        tableText += "<td class='text-center'>"+data.RLT_NM+"</td>"; //담보(자차/대물/쌍방)
        tableText += "<td class='text-center'>"+data.NB_CAR_NUM+"</td>";
        tableText += "<td class='text-center hidetd'>"+data.CAR_CORP+"</td>"; //제조사
        tableText += "<td class='text-center'>"+data.CAR_NM+"</td>"; //차량명
        tableText += "<td class='text-left'>"+(data.PART_NM != null? data.PART_NM : '-')+"</td>"; //사용부품
        tableText += "<td class='text-right hidetd'>"+workHour+"</td>"; //방청시간
        tableText += "<td class='text-right' style='color: blue;'>"+numberWithCommas(Number(data.NB_TOTAL_PRICE))+"</td>";//청구금액

        tableText += "<td class='text-right hidetd'>"+(data.NBI_RATIO == '' ? '0' :data.NBI_RATIO ) +"%</td>";//담보비율
        tableText += "<td class='text-right hidetd'>"+(data.NBI_PRICE != null? numberWithCommas(Number(data.NBI_PRICE)) : '-')+"</td>"; //담보금액
        tableText += "<td class='text-center'>"+(data.NBI_INSU_DEPOSIT_DT != null ?(data.NBI_INSU_DEPOSIT_DT !='0000-00-00' ? data.NBI_INSU_DEPOSIT_DT: '-') : '-' )+"</td>";
        tableText += "<td class='text-right' style='color: blue;'>"+(data.NBI_INSU_DEPOSIT_PRICE != null? numberWithCommas(Number(data.NBI_INSU_DEPOSIT_PRICE)) : '-')+"</td>";
        tableText += "<td class='text-center hidetd'>"+(data.NBI_INSU_ADD_DEPOSIT_DT != null ? data.NBI_INSU_ADD_DEPOSIT_DT : '-')+"</td>"; //감액입금일
        tableText += "<td class='text-right hidetd'>"+(data.NBI_INSU_ADD_DEPOSIT_PRICE != null? numberWithCommas(Number(data.NBI_INSU_ADD_DEPOSIT_PRICE)) : '-')+"</td>";//감액입금액
        tableText += "<td class='text-right'>"+(data.DC_RATIO != null ? data.DC_RATIO+'%' : '-')+"</td>";
        tableText += "<td class='text-center hidetd'>"+(data.NB_VAT_DEDUCTION_YN == YN_Y? '공제' : '미공제')+"</td>";
        tableText += "<td class='text-center'>"+depositTxt+"</td>";
        
        tableText += "<td class='text-center hidetd'>"+(data.RDT_NM != null ? data.RDT_NM : '-')+"</td>"; //입금여부 상세
        tableText += "<td class='text-center'>"+(data.NBI_INSU_DONE_YN == YN_Y? '종결' : '미결')+"</td>";
        tableText += "<td class='text-center hidetd'>"+(data.NBI_REASON_DT != null ? data.NBI_REASON_DT : '-')+"</td>";
        tableText += "<td class='text-center hidetd'>"+(data.REASON_RMT_NM != null ? data.REASON_RMT_NM : '-')+"</td>";
        tableText += "<td class='text-center hidetd'>"+(data.NBI_REASON_USERID != null ? nullChk(data.NBI_REASON_USER_NM,'')+'('+data.NBI_REASON_USERID+')' : '-')+"</td>"; //압력아이디
        
        tableText += "<td class='text-center hidetd'>"+(data.NBI_REQ_REASON_YN != null ? data.NBI_REQ_REASON_YN == YN_Y ? '요청': '미요청' : '-')+"</td>"; //사유요청여부
        tableText += "<td class='text-center hidetd'>"+(data.NBI_REQ_REASON_DTHMS != null ? data.NBI_REQ_REASON_DTHMS : '-')+"</td>"; //사유요청일
        tableText += "<td class='text-center hidetd'>"+(data.NBI_REQ_REASON_USERID != null ? nullChk(data.NBI_REQ_REASON_USER_NM,'')+'('+data.NBI_REQ_REASON_USERID+')' : '-')+"</td>"; //사유요청자
        tableText += "<td class='text-center hidetd'>"+(data.NBI_REASON_NM != null ? data.NBI_REASON_NM : '-')+"</td>"; //사유구분
        tableText += "<td class='text-left'>"+(data.NBI_REASON_DTL_NM != null ? data.NBI_REASON_DTL_NM : '-')+"</td>"; //사유상세
        tableText += "<td class='text-center hidetd'>"+(data.NBI_PROC_REASON_DT != null ? data.NBI_PROC_REASON_DT : '-')+"</td>";
        tableText += "<td class='text-center hidetd'>"+(data.NBI_PROC_USERID != null ? nullChk(data.NBI_PROC_NM,'')+"("+data.NBI_PROC_USERID +')': '-')+"</td>"; //처리자
        tableText += "<td class='text-center hidetd'>"+(data.NBI_INSU_DEPOSIT_DT != null ? (data.NBI_INSU_DEPOSIT_DT !='0000-00-00' ? data.NBI_INSU_DEPOSIT_DT: '-') : '-')+"</td>";// 보험사입금일
        tableText += "<td class='text-center'>"+txtDupli;
        tableText += "  <button type='button' class='btn btn-default' onclick='openBillPopup(\""+data.NB_ID+"\",\""+data.MGG_ID+"\")'>청구</button>";
        tableText += "  <button type='button' class='btn btn-default' onclick='showDepositModal("+JSON.stringify(data)+")'>입금</button>";
        tableText += "  <button type='button' class='btn btn-default' onclick='showTransitModal("+index+")'>이첩</button>";
        tableText += "</td>";

        tableText += "<td class='text-center hidetd'>"+(data.NBI_LAST_PRINT_DTHMS != null ? data.NBI_LAST_PRINT_DTHMS : '미출력')+"</td>";
        tableText += "<td class='text-right hidetd'>"+(data.NBI_CAROWN_PRICE != null ? numberWithCommas(Number(data.NBI_CAROWN_PRICE)) : '-')+"</td>";
        tableText += "<td class='text-center hidetd'>"+(data.NBI_CAROWN_DEPOSIT_DT != null ? data.NBI_CAROWN_DEPOSIT_DT : '-')+"</td>";
        tableText += "<td class='text-right hidetd'>"+(data.NBI_CAROWN_DEPOSIT_PRICE != null? numberWithCommas(Number(data.NBI_CAROWN_DEPOSIT_PRICE)): '-')+"</td>";
        tableText += "<td class='text-center hidetd'>"+data.NBI_CAROWN_DEPOSIT_YN+"</td>";
        tableText += "<td class='text-center hidetd'>"+btnCarown+"</td>"; // 처리버튼
        tableText += "<td class='text-center'>"+(data.WRT_USERID != null ? nullChk(data.WRT_USER_NM)+"("+data.WRT_USERID+")":'-')+"</td>";
        tableText += "<td class='text-center'>";
        if($("#AUTH_DELETE").val() == YN_Y){
            tableText += "  <button type='button' class='btn btn-danger' onclick='openDeleteModal("+index+")'>삭제</button>";
        }
        tableText += "  <button type='button' class='btn btn-default' onclick='reSendFax("+index+")'>재전송</button>";
        tableText += "  <button type='button' class='btn btn-default' onclick='billPrint("+index+")'>인쇄</button>";
        tableText += "</td>";
        tableText += "<td class='text-center'>";
        tableText += "  <button type='button' class='btn btn-default' onclick='downloadFile("+index+",\"PDF\")'>PDF</button>";
        tableText += "  <button type='button' class='btn btn-default' onclick='downloadFile("+index+",\"JPEG\")'>JPEG</button>";
        tableText += "</td>";
        tableText += "</tr>      ";
        
    });
    paging(totalListCount, currentPage, 'getList'); 
    
    $('#dataTable').html(tableText);
    
    if($('#extend').is(':checked')){
        $(".hidetd").show();
    }else{
        $(".hidetd").hide();
    }
    
}

