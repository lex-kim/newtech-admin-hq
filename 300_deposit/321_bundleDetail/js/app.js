const CONFIRM_URL = 'act/comfirmBundle_act.php';
let CURR_PAGE = 1; 
let listArray = [];
let selectCarNum = '';
let selectIndex = 0;
let modalArray = [];

$(document).ready(function() {
  $("#START_DT_SEARCH").datepicker(
      getDatePickerSetting('END_DT_SEARCH', 'minDate')
  );
  $("#END_DT_SEARCH").datepicker(
      getDatePickerSetting('START_DT_SEARCH', 'maxDate')
  );
  getDecrementRation();
  setDepoitSearch();
  getList();
  // 전체선택
  $("#allcheck").click(function(){
    if($("#allcheck").prop("checked")){
        $("input[type=checkbox].listcheck").prop("checked", true);
    }else{
        $("input[type=checkbox].listcheck").prop("checked", false);
    }
  });



});

/** 감액사유 자동요청 값  */
getDecrementRation = () => {
  const data = {
      REQ_MODE : CASE_READ
  };
  callAjax('../../../100_code/180_policy/183_reduce/act/reduce_act.php', 'GET', data).then((result)=>{
      const data = JSON.parse(result);
      if(data.status == OK){
          const item = data.data;
          $('#PIE_CUR_DECREMENT_RATIO').val(item.PIE_CUR_DECREMENT_RATIO); 
          // console.log('decrement_ration >>',$('#PIE_CUR_DECREMENT_RATIO').val());
      }else{
          console.log('PIE_CUR_DECREMENT_RATIO not exist');
      } 
  });
}

/**
 * 입금여부 상세 동적생성
 */
setDepoitSearch = () => {
  const param = {
      REQ_MODE : REF_DEPOSIT_TYPE
  }
  callAjax('/300_deposit/310_depositMngt/act/depositMngt_act.php', 'GET', param).then((result)=>{
      console.log('getDepoitDetail SQL >>',result);
      const res = JSON.parse(result);
      // console.log('getDepoitDetail>>',res);
      if(res.status == OK){
          arrDepotitDetail = res.data; 
          let optionText = '';
          $('#DEPOSIT_DETAIL_YN_SEARCH option').remove();
          arrDepotitDetail.map((item)=>{
              optionText += '<option value="'+item.ID+'">'+item.NM+'</option>';
          });
          $("#DEPOSIT_DETAIL_YN_SEARCH").append(optionText);  // <SELECT> 객체에 반영            
          $("#DEPOSIT_DETAIL_YN_SEARCH").selectpicker('refresh');
          
      }else{
          alert(ERROR_MSG_1100);
      }
  });
}

/* 일괄입금확인 상세리스트 조회 */
getList = (currentPage) => {
  CURR_PAGE = (currentPage == undefined) ? CURR_PAGE : currentPage;
  const data = {
    REQ_MODE: CASE_LIST_SUB,
    PER_PAGE:  $("#perPage").val(),
    CURR_PAGE:  CURR_PAGE,
    NH_DTHMS_ID: $('#NH_DTHMS_ID').val(),

    /* 검색조건 */
    START_DT_SEARCH: $('#START_DT_SEARCH').val(),
    END_DT_SEARCH: $('#END_DT_SEARCH').val(),
    NBI_REGI_NUM_SEARCH: $('#NBI_REGI_NUM_SEARCH').val(),
    NB_CAR_NUM_SEARCH: $('#NB_CAR_NUM_SEARCH').val(),
    DEPOSIT_YN_SEARCH: $('#DEPOSIT_YN_SEARCH').val(),
    DEPOSIT_DETAIL_YN_SEARCH: $('#DEPOSIT_DETAIL_YN_SEARCH').val(),
  };

  callAjax(CONFIRM_URL,'GET', data).then((result)=>{
    // console.log('getList SQL>>',result);
      const data = JSON.parse(result);
      if(data.status == OK){
        $('#indicator').hide();
        const item = data.data;
        setDetailTable(item[1], item[0], CURR_PAGE);
      }else{
        setHaveNoDataDetailTable();
      }
  });

}



/** 일괄입금확인 상세 페이지의 메인리스트
* @param {dataArray : 리스트 데이터, currentPage : 현재페이지(페이징을 위해)} 
*/
setDetailTable = (totalListCount, dataArray, currentPage) => {
  let tableText = '';
  listArray = dataArray;

  dataArray.map((data, index)=>{
    // console.log('init',index, data );
    let nbData = [];
    let matchData = [];
    let nb = [];
    // if(data.NB_CNT > 0){
      
      // 

      if(data.NB_ID != null){ //처리된 아이
        nbData = data.DEPOSIT_NB.split('|');
        // console.log(index, nbData);
      }else if(data.NB_CNT == 0){
        nbData= null;
        matchData= null;

      }else{
        if(data.NB_INFO != null){
          
          nb = data.NB_INFO.split(',');
          nb.map((row,index)=>{
            matchData.push(row.split('|'));
          })
          nbData = matchData[0];
        } 
        
      }       
    
      // console.log('nb [0]>>>', nbData[0]);
     
    // }
    let chk = '';
    let matchLink = '';
    if( data.NB_ID == null){
      if(data.NB_CNT == 1){
        listArray[index]["NB_ID"] = nbData[0];
        listArray[index]["NBI_RMT_ID"] = nbData[12];
      }
      if(data.NB_CNT >= 2){
        matchLink = "<a href='javascript:showDetailModal("+index+",\""+data.NHD_CAR_NUM+"\","+JSON.stringify(matchData)+")'>"+data.NB_CNT+"</a>";
      }else{
        matchLink = data.NB_CNT;
      }
      chk = "<input type='checkbox' class='listcheck' name='multi'value='"+index+"'>";
    }else {
      matchLink= '처리완료';
    }

    

    // console.log(index, nbData);
      tableText += "<tr id='"+data.NHD_CAR_NUM+"'>";  
      tableText += "<td class='text-center'>"+chk+"</td>";
      // tableText += "<td class='text-left'><a href='confirmBundleDetail.html?idx="+data.NH_DTHMS_ID+";'>"+data.NH_DTHMS_ID+"</td>";
      tableText += "<td class='text-center' id='"+index+"bill_dt'>"+(nbData!= null ? nbData[1] : '-' )+"</td>";
      tableText += "<td class='text-center' id='"+index+"bill_insu'>"+(nbData!= null ? nbData[3] : '-' )+"</td>";
      tableText += "<td class='text-center' id='"+index+"bill_regi'>"+(nbData!= null ? nbData[4] : '-' )+"</td>";
      tableText += "<td class='text-center' id='"+index+"bill_rmt'>"+(nbData!= null ? nbData[6] : '-' )+"</td>";
      tableText += "<td class='text-center' id='"+index+"bill_car'>"+(nbData!= null ? nbData[7] : '-' )+"</td>";
      tableText += "<td class='text-center' id='"+index+"bill_price'>"+(nbData!= null ? numberWithCommas(nbData[8]) : '-' )+"</td>";
      tableText += "<td class='text-center' id='"+index+"bill_deposit'>"+(nbData!= null ? nbData[9] == YN_Y? '입금': '미입금' : '-' )+"</td>";
      tableText += "<td class='text-center' id='"+index+"bill_detail'>"+(nbData!= null ? nbData[11] : '-' )+"</td>";
      tableText += "<td class='text-center'>"+matchLink+"</td>";
      tableText += "<td class='text-center'>"+data.NHD_INSU_NM+"</td>";
      tableText += "<td class='text-center'>"+data.NHD_REGI_NUM+"</td>";
      tableText += "<td class='text-center'>"+data.NHD_CAR_NUM+"</td>";
      tableText += "<td class='text-center'>"+data.NHD_DEPOSIT_DT+"</td>";
      tableText += "<td class='text-center'>"+numberWithCommas(data.NHD_DEPOSIT_PRICE)+"</td>";
      tableText += "</tr>"
      
  });
  paging(totalListCount, currentPage, 'getList'); 
  $('#dataTable').html(tableText);
}

/** 빈값일 때 동적테이블처리 */
setHaveNoDataDetailTable = () => {
  const tableText = "<tr><td class='text-center' colspan='15'>조회요청한 데이터가 존재하지 않습니다.</td></tr>";
  $('.pagination').html('');
  $('#dataTable').html(tableText);
}


/**
 * 일괄입금 매칭 갯수 모달 창 오픈
 * @param {int} idx 선택한 리스트의 인덱스
 * @param {String} carNum 선택한 리스트의 차량번호
 * @param {Array} dataArray 선택한 행의 데이터
 */
showDetailModal = (idx, carNum, dataArray) => {
  selectIndex = idx;
  selectCarNum = carNum;
  modalArray = dataArray;
  setModalTable(dataArray);
  $('#setBillBtn').attr('onclick','setNb('+idx+')');
  $('#add-new').modal('toggle');
}

/* 일괄입금 매칭 갯수 모달 창- 리스트구현 */
setModalTable = (dataArray) =>{
  let tbText = '';
  
  dataArray.map((data, index)=>{
    // console.log(index , data );
    tbText += '<tr>';
    tbText += '<td class="text-center">'+data[1]+'</td>';
    tbText += '<td class="text-center">'+data[3]+'</td>';
    tbText += '<td class="text-center">'+data[4]+'</td>';
    tbText += '<td class="text-center">'+data[6]+'</td>';
    tbText += '<td class="text-center">'+data[7]+'</td>';
    tbText += '<td class="text-right">'+numberWithCommas(nullChk(data[8],0))+'</td>';
    tbText += '<td class="text-center">'+(data[9] == YN_N ? '미입금': '입금')+'</td>';
    tbText += '<td class="text-center"><input type="radio" name="RADIO_MODAL" id="'+data[12]+'" value="'+index+'"></td>';
    tbText += '</tr> ';
  })
  $('#matchingTable').html(tbText);
};


/* 일괄입금 매칭 갯수 모달 창- 적용 이벤트 */
setNb = (idx) =>{
  modalIdx = $('input:radio[name=RADIO_MODAL]:checked').val();
  // console.log('select MODAL DATA >>>',modalArray[modalIdx]);
  // console.log('setNB>>',listArray[selectIndex]);
  if($('input:radio[name=RADIO_MODAL]:checked').val() == '' || $('input:radio[name=RADIO_MODAL]:checked').val() == undefined){
    alert('선택된 청구정보가 없습니다.');
  }else{
    listArray[selectIndex]["NB_ID"] = modalArray[modalIdx][0]
    listArray[selectIndex]["NBI_RMT_ID"] = modalArray[modalIdx][12];
    $('#'+selectIndex+'bill_dt').html(modalArray[modalIdx][1]);
    $('#'+selectIndex+'bill_insu').html(modalArray[modalIdx][3]);
    $('#'+selectIndex+'bill_regi').html(modalArray[modalIdx][4]);
    $('#'+selectIndex+'bill_rmt').html(modalArray[modalIdx][6]);
    $('#'+selectIndex+'bill_car').html(modalArray[modalIdx][7]);
    $('#'+selectIndex+'bill_price').html(numberWithCommas(modalArray[modalIdx][8]));
    $('#'+selectIndex+'bill_deposit').html(modalArray[modalIdx][9]== YN_N ? '미입금': '입금');
    $('#'+selectIndex+'bill_detail').html('');
    $("input[name=multi][value="+selectIndex+"]").prop("checked",true);
    alert('청구정보가 선택되었습니다.');
  }
  $('#add-new').modal('toggle');

}

excelDownload = () =>{
  $('#searchForm').attr("method","GET");
  $('#searchForm').attr("action","confrimBundleExcel.html");
  $('#searchForm').submit();
}


/** 일괄입금 */
setDepositData = () => {
  let setIdxArray = [];
  let succCnt = 0;
  if($('input[name=multi]:checked').length > 0){
    $('input[name=multi]:checked').each(function(){
        if($(this).val() != ''){
          setIdxArray.push($(this).val());
        }
    });
    
    if(setIdxArray.length > 0 ){
      setIdxArray.map((idx,index)=>{
        const param = {
          DATA_ARRAY : listArray[idx],
          REQ_MODE: CASE_UPDATE,
          CUR_DECREMENT_RATIO : $('#PIE_CUR_DECREMENT_RATIO').val(),
        }
        callAjax(CONFIRM_URL,'GET', param).then((result)=>{
          console.log('setDeposit>>',result);
            const data = JSON.parse(result);
            // console.log(data.status);
            if(data.status == OK){
              succCnt ++;
            }
            if(index == setIdxArray.length-1){
              // console.log('getDetailList() succCnt',succCnt);
              getList();
              alert('일괄 입금처리가 완료되었습니다.');
              
            } 
        });
      });
    }
  }else {
    alert('선택된 청구정보가 없습니다.');
  }
  

}