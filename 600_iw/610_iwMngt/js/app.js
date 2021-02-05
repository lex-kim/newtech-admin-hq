const IWMNGT_URL ='act/iwmngt_act.php'
let CURR_PAGE = 1;
$(document).ready(function() {
  /** 검색 */
  $("#searchForm").submit(function(e){
      e.preventDefault();
          getList(1);
  });
  const tdLength = $('table tr th').length;
  setInitTableData(tdLength, 'iwMngtContents');
  setInsuType();
  setIwOptions('SEARCH_INSU_NM','','=보험사=');

    /* datepicker 설정 */
    $( "#SEARCH_START_DT" ).datepicker(
      getDatePickerSetting('SEARCH_END_DT','minDate')
    );
    $( "#SEARCH_END_DT" ).datepicker(
        getDatePickerSetting('SEARCH_START_DT','maxDate')    
    );
    $('#MII_ENROLL_DT').datepicker({
        dateFormat : 'yy-mm-dd',
    });

  $( "#iwMngtForm" ).submit(function( e ) {
    $("#MII_OLD_ADDRESS2").val($("#MII_NEW_ADDRESS2").val());
    e.preventDefault();
    let formData = $("#iwMngtForm").serialize();
    if($('#REQ_MODE').val() == CASE_UPDATE){
      confirmSwalTwoButton('수정 하시겠습니까?','확인', '취소', false ,()=>updateData(formData));
    }else{
        addData(formData);
    }
  });
    
});


/* 보험사분류 동적 생성 */
setInsuType = () =>{
  const data = {
    REQ_MODE: GET_INSU_TYPE,
  };
  callAjax(IWMNGT_URL, 'GET', data).then((result)=>{
      const data = JSON.parse(result);
      if(data.status == OK){
          const dataArray = JSON.parse(data.data); 

          let baseOption = '<option value="">=보험사분류=</option>';
          let optionText = '';
          dataArray.map((item)=>{
              optionText += '<option value="'+item.RIT_ID+'">'+item.RIT_NM+'</option>';
          });
          $('#SEARCH_INSU_TYPE').html(baseOption+optionText);
          $('#RIT_ID').html(optionText);
        
      }else{
          alert("서버와의 연결이 원활하지 않습니다.");
      }
  });
}

/**
 * 주소검색
 * @param {objId : 구주소/신주소 고유 input 아이디(해당 값을 세팅해주기 위해.)}
 */
execPostCode = (objId) => {
  new daum.Postcode({
      oncomplete: function(data) {
        console.log('postcode >> ',data)
        let fullAddr = ''; // 최종 주소 변수
        let extraAddr = ''; // 조합형 주소 변수
        
        fullAddr = data.roadAddress;
        
        if(data.bname !== ''){
            extraAddr += data.bname;
        }
        
        if(data.buildingName !== ''){// 건물명이 있을 경우 추가한다.
            extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
        }
       
        fullAddr += (extraAddr !== '' ? ' ('+ extraAddr +')' : ''); // 조합형주소의 유무에 따라 양쪽에 괄호를 추가하여 최종 주소를 만든다.
       
        $("#MII_OLD_ZIP").val(data.postcode);
        $("#MII_OLD_ADDRESS1").val(data.jibunAddress);

        $("#MII_NEW_ZIP").val(data.zonecode);
        $("#MII_NEW_ADDRESS1").val(fullAddr);
        $("#MII_NEW_ADDRESS2").focus();
        
      }
  }).open();
}



/** 메인 리스트 테이블 동적으로 생성 
 * @param {dataArray : 리스트 데이터, currentPage : 현재페이지(페이징을 위해)} 
*/
setTable = (totalListCount, dataArray, currentPage) => {
  let tableText = '';

  dataArray.map((data, index)=>{
        tableText += "<tr>";
        tableText += "<td class='text-center'>"+data.IDX+"</td>";
        tableText += "<td class='text-center'>"+data.MII_ORDER_NUM+"</td>";
        tableText += "<td class='text-center'>"+data.RIT_NM+"</td>";
        // tableText += "<td class='text-left'>"+data.MII_NM+"</td>";
        tableText += "<td class='text-left'>"+data.MII_NM+"</td>";
        tableText += "<td class='text-right'>"+data.NON_CNT+"</td>";
        tableText += "<td class='text-left'>"+data.MII_NEW_ADDRESS1+" "+data.MII_NEW_ADDRESS2+"</td>";
        tableText += "<td class='text-right'>"+data.MII_DISCOUNT_RATE+"%</td>";
        tableText += "<td class='text-center'>"+data.MII_ENROLL_DT+"</td>";
        tableText += "<td class='text-left'>"+data.MII_CHG_DEPT_NM+"</td>";
        tableText += "<td class='text-center'>"+data.MII_CHG_LVL_NM+"</td>";
        tableText += "<td class='text-center'>"+data.MII_CHG_NM+"</td>";
        tableText += "<td class='text-center'>"+phoneFormat(data.MII_CHG_TEL1)+"</td>";
        tableText += "<td class='text-center'>"+phoneFormat(data.MII_CHG_TEL2)+"</td>";
        tableText += "<td class='text-left'>"+data.MII_CHG_EMAIL+"</td>";
        tableText += "<td class='text-center'>"+data.MII_BOSS_LVL_NM+"</td>";
        tableText += "<td class='text-center'>"+data.MII_BOSS_NM+"</td>";
        tableText += "<td class='text-center'>"+phoneFormat(data.MII_BOSS_TEL)+"</td>";
        tableText += "<td class='text-left'>"+data.MII_MEMO+"</td>";
        tableText += "<td class='text-center'><button type='button' class='btn btn-default' onclick='showTransModal(\""+data.MII_ID+"\",\""+data.MII_NM+"\")'>이관</button></td>";
        tableText += "<td class='text-center'><button type='button' class='btn btn-default' onclick='showAreaModal(\""+data.MII_ID+"\")'>담당지역</button></td>";
        if($("#AUTH_UPDATE").val() == YN_Y){
          tableText += " <td class='text-center'><button type='button' class='btn btn-default' onclick='showUpdateModal("+JSON.stringify(data)+")'>수정</button></td>";
        }
        tableText += "</tr>  ";
      
  });
  paging(totalListCount, currentPage, 'getList'); 
  $('#iwMngtContents').html(tableText);
}

/** 메인리스트 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
  const tableText = "<tr><td class='text-center' colspan='20'>조회요청한 데이터가 존재하지 않습니다.</td></tr>";
  $('.pagination').html('');
  $('#iwMngtContents').html(tableText);
}

/**
 * 청구서 이관 모달
 * @param {String} id 보험사code
 * @param {String} nm 보험사명
 */
showTransModal = (id, nm) => {
  $('#transfer-modal').modal('toggle');
  $('#PRESENT_INSU_ID').val(id);
  $('#PRESENT_INSU_NM').val(nm);
  setIwOptions('TRANSFER_INSU_NM','','선택');
}

setTrans = () => {
  confirmSwalTwoButton('이관을 진행하시겠습니까?','확인', '취소', false ,()=>{
    const param = {
        REQ_MODE: CASE_CONFLICT_CHECK,
        PRESENT_INSU_ID: $("#PRESENT_INSU_ID").val(),
        TRANSFER_INSU_NM: $("#TRANSFER_INSU_NM").val(),
    };
    callAjax(IWMNGT_URL, 'GET', param).then((result)=>{
        const data = JSON.parse(result);
        if(data.status == OK){
            confirmSwalOneButton('이관에 성공하였습니다.','확인',()=>{$("#transfer-modal").modal("toggle"); getList();});
        }else if(data.status == ERROR){
            alert(data.message);
        }else{
            alert("서버와의 연결이 원활하지 않습니다.");
        }   
    });
  });

}

/* 메인 리스트 */
getList = (currentPage) => {
  CURR_PAGE = (currentPage == undefined) ? CURR_PAGE : currentPage;

  const data = {
      REQ_MODE: CASE_LIST,
      PER_PAGE:  $("#perPage").val(),
      CURR_PAGE:  CURR_PAGE,

      /** 검색 */
      SEARCH_START_DT: $('#SEARCH_START_DT').val(),
      SEARCH_END_DT: $('#SEARCH_END_DT').val(),
      SEARCH_INSU_TYPE: $('#SEARCH_INSU_TYPE').val(),
      SEARCH_INSU_NM: $('#SEARCH_INSU_NM').val(),
      CHG_KEYWORD: $('#CHG_KEYWORD').val(),
      SEARCH_CHG: $('#SEARCH_CHG').val()
  };

  callAjax(IWMNGT_URL, 'GET', data).then((result)=>{
    console.log(result);
      const res = JSON.parse(result);
      
      if(res.status == OK){
        $('#indicator').hide();
          const item = res.data;
          setTable(item[1], item[0], CURR_PAGE);
      }else{
          setHaveNoDataTable();
      }
  });
  
}

/* 등록modal */
showAddModal = () => {
  $("#iwMngtForm").each(function() {  
      this.reset();  
  });  

  $('#REQ_MODE').val(CASE_CREATE);
  $('#add-new').modal();
  $('.modal-right').hide();
  $('.modal-dialog').attr('style', "width:400px;");
  $('.modal-left').attr('style', "width:100%; border-right:0px;");

  $('#ORI_MII_ORDER_NUM').val('');
  $('#MII_ID').attr('readonly', false);
  $('#btnDelete').hide();
  $('#submitBtn').html('등록');

}

/* validation  */
validationData = () => {
  if($('#MII_CHG_TEL1').val() != ''){
    if(!validatePhone($('#MII_CHG_TEL1').val())){
      alert('보상지원 담당자의 사무실 전화번호를 확인해주세요.');
      $('#MII_CHG_TEL1').focus();
      return false;
    }
  }
  if($('#MII_CHG_EMAIL').val() != ''){
    if(!validateEmail($('#MII_CHG_EMAIL').val())){
      alert('보상지원 담당자의 이메일을 확인해주세요.');
      $('#MII_CHG_EMAIL').focus();
      return false;
    }
  }

  if (!validatePhone($('#MII_BOSS_TEL').val()) ){
    alert('보상지원 부서장 휴대폰을 확인해주세요.');
    $('#MII_BOSS_TEL').focus();
    return false;
  }else if(!validatePhone($('#MII_CHG_TEL2').val()) ){
    alert('보상지원 담당자의 휴대폰번호를 확인해주세요.');
    $('#MII_CHG_TEL2').focus();
    return false;
  } else {
    return true ;
  }
  
  
  

}

/* 등록 */
addData = (param) =>{
  if(validationData()){
    callAjax(IWMNGT_URL, 'POST', param).then((result)=>{
      console.log('addData result >>.',result);
      const data = JSON.parse(result);
      if(data.status == OK){
          $("#add-new").modal("hide");
          alert("등록에 성공하였습니다.");
          getList();
      }else if(data.status == CONFLICT){
        confirmSwalTwoButton(data.message,'확인', '취소', false ,()=>{
            $('#IS_EXIST_ORDER_NUM').val('false');
            console.log('param::',$("#iwMngtForm").serialize());
            addData($("#iwMngtForm").serialize());
        }, '진행하시겠습니까?');
        
      }else if(data.status == ERROR){
        confirmSwalOneButton(data.message,'확인',()=>{
            $('#MII_ID').focus();
        });
      }else{
          alert("서버와의 연결이 원활하지 않습니다.");
      } 
     
    });
  }
}

/* 업데이트 모달창일경우 */
showUpdateModal = (data) => {
  console.log('showUpdateModal',data);
  $('.modal-dialog').attr('style', " width: 1100px; height: 100%;");
  $('.modal-left').attr('style', "width: 40%;border-right: 1px solid #ddd;");
  $('.modal-right').show();
  $('#REQ_MODE').val(CASE_UPDATE);
  $('#btnDelete').show();
  $('#submitBtn').html('수정');

  /* 보험사/담당자/책임자 정보 */
  $('#MII_ID').val(data.MII_ID);
  $('#MII_ID').attr('readonly', true);
  $('#MII_ORDER_NUM').val(data.MII_ORDER_NUM);
  $('#IS_EXIST_ORDER_NUM').val('');
  $('#ORI_MII_ORDER_NUM').val(data.MII_ORDER_NUM);
  $('#RIT_ID').val(data.RIT_ID);
  $('#MII_NM').val(data.MII_NM);
  $('#MII_NEW_ZIP').val(data.MII_NEW_ZIP);
  $('#MII_NEW_ADDRESS1').val(data.MII_NEW_ADDRESS1);
  $('#MII_NEW_ADDRESS2').val(data.MII_NEW_ADDRESS2);
  $('#MII_DISCOUNT_RATE').val(data.MII_DISCOUNT_RATE);
  $('#MII_ENROLL_DT').val(data.MII_ENROLL_DT);
  $('#MII_CHG_DEPT_NM').val(data.MII_CHG_DEPT_NM);
  $('#MII_CHG_LVL_NM').val(data.MII_CHG_LVL_NM);
  $('#MII_CHG_NM').val(data.MII_CHG_NM);
  $('#MII_CHG_TEL1').val(data.MII_CHG_TEL1);
  $('#MII_CHG_TEL2').val(data.MII_CHG_TEL2);
  $('#MII_CHG_EMAIL').val(data.MII_CHG_EMAIL);
  $('#MII_BOSS_LVL_NM').val(data.MII_BOSS_LVL_NM);
  $('#MII_BOSS_NM').val(data.MII_BOSS_NM);
  $('#MII_BOSS_TEL').val(data.MII_BOSS_TEL);
  $('#MII_MEMO').val(data.MII_MEMO);

  /* 변경이력 */
  $('#DIV_LOG_LIST').hide();
  $('#DIV_LOG_DETAIL').hide();

  $('#add-new').modal();
}

updateData = (param) => {
  validationData();
  callAjax(IWMNGT_URL, 'POST', param).then((result)=>{
    console.log('update SQL >>>',result);
    const data = JSON.parse(result);
    if(data.status == OK){
        $("#add-new").modal("hide");
        alert("수정에 성공하였습니다.");
        getList();
    }else if(data.status == CONFLICT){
      confirmSwalTwoButton(data.message,'확인', '취소', false ,()=>{
          $('#IS_EXIST_ORDER_NUM').val('false');
          console.log('param::',$("#iwMngtForm").serialize());
          updateData($("#iwMngtForm").serialize());
      }, '진행하시겠습니까?');
      
    }else if(data.status == ERROR){
      confirmSwalOneButton(data.message,'확인',()=>{
          $('#MII_ID').focus();
      });
    }else{
        alert("서버와의 연결이 원활하지 않습니다.");
    }   
  });
}


excelDownload = () => {
  location.href="iwMngtExcel.html?"+$("#searchForm").serialize();
}


/** 리스트 테이블 동적으로 생성 
 * @param {dataArray : 리스트 데이터, currentPage : 현재페이지(페이징을 위해)} 
*/
setLogTable = (totalListCount, dataArray, currentPage, perPage) => {
  $('#subPagination li').remove();
  let subTableText = '';
  dataArray.map((data, index)=>{
    subTableText += "<tr onclick='showDetail("+JSON.stringify(data)+")'>";
    subTableText += "<td class='text-center'>"+data.LAST_DTHMS+"</td>";
    subTableText += "<td class='text-center'>"+data.NICL_CHG_NM+"</td>";
    subTableText += "<td class='text-center'>"+phoneFormat(data.NICL_CHG_TEL2)+"</td>";
    subTableText += "<td class='text-center'>"+data.NICL_BOSS_NM+"</td>";
    subTableText += "<td class='text-center'>"+phoneFormat(data.NICL_BOSS_TEL)+"</td>";
    subTableText += "</tr>";
      
  });
  subPaging(totalListCount, currentPage, 'getSubList', perPage); 
  $('#logListContents').html(subTableText);
}

/** 빈값일 때 동적테이블처리 */
setHaveNoDataLogTable = () => {
  const subTableText = "<tr><td class='text-center' colspan='5'>조회요청한 데이터가 존재하지 않습니다.</td></tr>";
  $('#subPagination').html('');
  $('#logListContents').html(subTableText);
}

/* 변경이력 메인 리스트 조회 */
getSubList = (currentPage) => {
  $('#DIV_LOG_LIST').show();
  $('#logListContents').empty();
  const data = {
      REQ_MODE: CASE_LIST_SUB,
      MII_ID: $('#MII_ID').val(),
      PER_PAGE: 5,
      CURR_PAGE:  currentPage,

      /** 변경이력 검색 */
      SEARCH_LOG_CHG_NM: $('#SEARCH_LOG_CHG_NM').val(),
      SEARCH_LOG_CHG_TEL: $('#SEARCH_LOG_CHG_TEL').val(),
      SEARCH_LOG_BOSS_NM: $('#SEARCH_LOG_BOSS_NM').val(),
      SEARCH_LOG_BOSS_TEL: $('#SEARCH_LOG_BOSS_TEL').val(),
  };

  callAjax(IWMNGT_URL, 'GET', data).then((result)=>{
      console.log('getSubList result >> ',result);
      const res = JSON.parse(result);
      console.log(res);
      if(res.status == OK){
          const item = res.data;
          setLogTable(item[1], item[0], currentPage, data.PER_PAGE);
      }else{
          setHaveNoDataLogTable();
      }
  });
}

showDetail = (data) => {
  $('#DIV_LOG_DETAIL').show();

  /*보상담당자 */
  let chgTableText = '';
  chgTableText += "<tr>";
  chgTableText += "<td class='text-center'>"+data.NICL_CHG_DEPT_NM+"</td>";
  chgTableText += "<td class='text-center'>"+data.NICL_CHG_LVL_NM+"</td>";
  chgTableText += "<td class='text-center'>"+data.NICL_CHG_NM+"</td>";
  chgTableText += "<td class='text-center'>"+data.NICL_CHG_TEL1+"</td>";
  chgTableText += "<td class='text-center'>"+data.NICL_CHG_TEL2+"</td>";
  chgTableText += "<td class='text-center'>"+data.NICL_CHG_EMAIL+"</td>";
  chgTableText += "</tr>";
  $('#logChgContents').html(chgTableText);

  /*부서장 */
  let bossTableText = '';
  bossTableText += "<tr>";
  bossTableText += "<td class='text-center'>"+data.NICL_BOSS_LVL_NM+"</td>";
  bossTableText += "<td class='text-center'>"+data.NICL_BOSS_NM+"</td>";
  bossTableText += "<td class='text-center'>"+data.NICL_BOSS_TEL+"</td>";
  bossTableText += "<td class='text-center'>"+data.NICL_MEMO+"</td>";
  bossTableText += "</tr>";
  $('#logBossContents').html(bossTableText);

}

/*변경이력 엑셀 다운로드 */
logExcelDownload = () => {
  const toURL="iwMngtLogExcel.html?" +
  "SEARCH_LOG_CHG_NM=" + $('#SEARCH_LOG_CHG_NM').val() +
  "&SEARCH_LOG_CHG_TEL=" + $('#SEARCH_LOG_CHG_TEL').val() +
  "&SEARCH_LOG_BOSS_NM=" + $('#SEARCH_LOG_BOSS_NM').val() +
  "&SEARCH_LOG_BOSS_TEL=" + $('#SEARCH_LOG_BOSS_TEL').val() +
  "&MII_ID=" + $('#MII_ID').val() ;
  location.href=toURL;
}


/* 데이터 삭제함수 */
delData = () => {
  const data = {
      REQ_MODE: CASE_DELETE,
      MII_ID:   $("#MII_ID").val()
  };
  callAjax(IWMNGT_URL, 'POST', data).then((result)=>{
      const data = JSON.parse(result);
      if(data.status == OK){
          confirmSwalOneButton(
            '삭제에 성공하였습니다.',
            '확인',
            ()=>{
              getList();
              $("#add-new").modal("hide");
            }
          );
      }else{
          const resultText ='서버와의 연결이 원활하지 않습니다.' ;
          alertSwal(resultText);
      } 
  });
}

/* 담당지역 관리 팝업창  */
showAreaModal = (insuId) => {
  $('#area-modal .modal-dialog').attr('style', "width:800px;");
  $('#MII_ID').val(insuId);
  $('#area-modal').modal('toggle');
  setinitAreaTableData();
  setInsuTeam();
}


/** 처음 페이지 들어왔을 때 테이블 내용
 * @param {colspan : colsapn}
 */
setinitAreaTableData = () => {
  const tableText = "<tr><td class='text-center' colspan='5'>전체내용은 검색조건 없이 검색버튼을 누르시면 됩니다.</td></tr>";
  $('#iwTeamContents').html(tableText);
  $('#areaPagination').html('');
  
}


  
/* 담당지역 팝업 > TEAM 리스트 조회 */
getTeamList = (currentPage) => {

  const data = {
      REQ_MODE: CASE_LIST_DETAIL,
      MII_ID: $('#MII_ID').val(),
      PER_PAGE: 10,
      CURR_PAGE:  currentPage,

      /** 변경이력 검색 */
      SEARCH_INSU_TEAM: $('#SEARCH_INSU_TEAM').val(),
      SEARCH_REGION_MEMO: $('#SEARCH_REGION_MEMO').val(),
      
  };

  callAjax(IWMNGT_URL, 'GET', data).then((result)=>{
      console.log('getTEAMList result >> ',result);
      const res = JSON.parse(result);
      console.log('getTeamList json >>>', res);
      if(res.status == OK){
          const item = res.data;
          setTeamTable(item[1], item[0], currentPage, data.PER_PAGE);
      }else{
          setHaveNoDataTeamTable();
      }
  });
}


/** 리스트 테이블 동적으로 생성 
 * @param {dataArray : 리스트 데이터, currentPage : 현재페이지(페이징을 위해)} 
*/
setTeamTable = (totalListCount, dataArray, currentPage, perPage) => {
  $('#subPagination li').remove();
  let teamTableText = '';
  dataArray.map((data, index)=>{
    let btnStyl = "";
    if(data.REGION_CNT >0){
      btnStyl = "<button type='button' class='btn btn-default btn-success' onClick='showRegionModal(\""+data.MIT_ID+"\",\""+data.MIT_NM+"\",\""+data.MII_ID+"\")')'>";
    }else {
      btnStyl = "<button type='button' class='btn btn-default' onClick='showRegionModal(\""+data.MIT_ID+"\",\""+data.MIT_NM+"\",\""+data.MII_ID+"\")')'>";
    }
    teamTableText += "<tr>";
    teamTableText += "<td class='text-center'>"+data.MIT_NM+"</td>";
    teamTableText += "<td class='text-center'>"+data.MIT_REGION_MEMO+"</td>";
    teamTableText += "<td class='text-center'>"+data.MIT_BOSS_NM_AES+"</td>";
    teamTableText += "<td class='text-center'>"+data.MIT_TEAM_TEL_AES+"</td>";
    teamTableText += "<td class='text-center'>"+btnStyl+"선택</button></td>";
    teamTableText += "</tr>";
      
  });
  subPaging(totalListCount, currentPage, 'getTeamList', perPage,'areaPagination'); 
  $('#iwTeamContents').html(teamTableText);
}

/** 빈값일 때 동적테이블처리 */
setHaveNoDataTeamTable = () => {
  const teamTableText = "<tr><td class='text-center' colspan='5'>조회요청한 데이터가 존재하지 않습니다.</td></tr>";
  $('#subPagination').html('');
  $('#iwTeamContents').html(teamTableText);
}


/* 보험사Team 동적 생성 */
setInsuTeam = () =>{
  const data = {
    REQ_MODE: GET_INSU_TEAM,
    MII_ID: $('#MII_ID').val(),
  };
  callAjax(IWMNGT_URL, 'GET', data).then((result)=>{
      const data = JSON.parse(result);
      if(data.status == OK){
          const dataArray = JSON.parse(data.data); 
            
          let optionText = '';
          dataArray.map((item)=>{
              optionText += '<option value="'+item.MIT_ID+'">'+item.MIT_NM+'</option>';
          });
          $('#SEARCH_INSU_TEAM').html(optionText);
          $('#SEARCH_INSU_TEAM').selectpicker('refresh');
        
      }
  });
}

/** 지역등록창
 * @param {teamId: 보험사팀 아이디}
 */
showRegionModal = (teamId,teamName,miiId) => {
  $('#regionForm').each(function(){
    this.reset();
  })
  
  $('#MII_ID').val(miiId);
  $('#MIT_ID').val(teamId);
  $('#MIT_NM').val(teamName);
  getRegion();
  getMiiRegion(miiId);
  $('#area-select-modal').modal("toggle");
}


/* 담당지역 등록 및 수정요청 수락 */
setRegion = () => {
  confirmSwalTwoButton(
    '체크된 지역들을 '+$('#MIT_NM').val()+'팀 담당으로 설정하시겠습니까?',
    '확인', 
    '취소', 
    false,
    ()=>{
        let arrRegionId = $("input[name=regionBox]:checked").map(function(){
          return $(this).attr('id');
        }).get(); 
        const data = {
          REQ_MODE: CASE_UPDATE_REGION,
          MIT_ID:$('#MIT_ID').val(),
          ARR_REGION: arrRegionId,
        }
        callAjax(IWMNGT_URL, 'POST', data).then((result)=>{
          console.log('updateRegion SQL >>>',result);
          const data = JSON.parse(result);
          if(data.status == OK){
              alert("담당지역이 설정되었습니다.");
              $('#area-select-modal').modal('toggle');
              getTeamList();
          }else if(data.status == ERROR){
            alert("선택된 지역이 없습니다. 지역을 선택해주세요");
          }else{
              alert("서버와의 연결이 원활하지 않습니다.");
          } 
        });
    });
  
}

/*팀별 담당지역 조회 */
getRegion = () => {
  $('#ulTeamRegion li').remove(); // li 초기화
  const data = {
    REQ_MODE: GET_REQUEST_REGION,
    MIT_ID:$('#MIT_ID').val(),
  }
  callAjax(IWMNGT_URL, 'POST', data).then((result)=>{
    console.log('getRegion SQL >>>',result);
    const res = JSON.parse(result);
    // console.log('getRegion DATA>>',res);
    if(res.status == OK){
        const dataArray = JSON.parse(res.data);
        let liText ='';
        if(dataArray.length > 0){
          
          dataArray.map((item, index)=>{
            
            liText += '<li><label for="'+item.CRR_1_ID+'-'+item.CRR_2_ID+'-'+item.CRR_3_ID+'-'+item.NITRU_ID+'-'+item.NITRU_DEL_YN+'">';
            if(item.NITRU_MIT_ID != null){
              //변경 내역이 있을때
              if(item.NITR_MIT_ID == item.NITRU_MIT_ID && item.NITR_MIT_ID != null){
                if(item.NITRU_DEL_YN == YN_N ){ 
                  // 1-1 변동없는 담당지역 > 체크 & 검정
                  liText += '<input type="checkbox" checked id="'+item.CRR_1_ID+'-'+item.CRR_2_ID+'-'+item.CRR_3_ID+'-'+item.NITRU_ID+'-'+item.NITRU_DEL_YN+'" name="regionBox">';
                  liText += item.CRR_NM;
                }else{  
                  // 1-2 변동 있는 지역 > 담당지역에서 제외 
                  liText += '<input type="checkbox" checked id="'+item.CRR_1_ID+'-'+item.CRR_2_ID+'-'+item.CRR_3_ID+'-'+item.NITRU_ID+'-'+item.NITRU_DEL_YN+'" name="regionBox">';
                  liText += '<span class="red">'+item.CRR_NM+'</span>';
                }
              }else {
                //1-3 추가된 지역
                liText += '<input type="checkbox" id="'+item.CRR_1_ID+'-'+item.CRR_2_ID+'-'+item.CRR_3_ID+'-'+item.NITRU_ID+'-'+item.NITRU_DEL_YN+'" name="regionBox">';
                liText += '<span class="red">'+item.CRR_NM+'</span>';
              }
            }else{
                //변경 내역이 없을때 (item.NITRU_MIT_ID == null)
                if(item.NITR_MIT_ID != null){
                  // 기존 설정 지역
                  liText += '<input type="checkbox" checked id="'+item.CRR_1_ID+'-'+item.CRR_2_ID+'-'+item.CRR_3_ID+'-'+item.NITRU_ID+'-'+item.NITRU_DEL_YN+'" name="regionBox">';
                  liText += item.CRR_NM;
                }else { //미설정 지역 
                  liText += '<input type="checkbox" id="'+item.CRR_1_ID+'-'+item.CRR_2_ID+'-'+item.CRR_3_ID+'-'+item.NITRU_ID+'-'+item.NITRU_DEL_YN+'" name="regionBox">';
                  liText += item.CRR_NM;
                }
            }
            liText +='</label> </li>';
          });
          $('#ulTeamRegion').append(liText);
        }else{
          $('#ulTeamRegion').append('<li class="form-inline"><label for="area1">선택된 지역이 없습니다.</label></li>');
        }
        $('#indicatorArea').hide();
    }else{
        console.log('err');
    }  
  });

}



/*보험사별 담당지역 조회 */
getMiiRegion = (mii) => {
  let mitId = $('#MIT_ID').val();
  let miiId = mii != undefined ? mii : $('#MII_ID').val();
  $('#ulRegion li').remove(); // li 초기화
  const data = {
    REQ_MODE: CASE_READ,
    MII_ID: miiId,
    SEARCH_AREA_KEYWORD: $('#SEARCH_AREA_KEYWORD').val(),
  }
  callAjax(IWMNGT_URL, 'POST', data).then((result)=>{
    console.log('getMiiRegion SQL >>>',result);
    const res = JSON.parse(result);
    // console.log('getRegion DATA>>',res);
    if(res.status == OK){
        const dataArray = JSON.parse(res.data);
        let liText ='';
        if(dataArray.length > 0){
          
          dataArray.map((item, index)=>{
            
            liText += '<li><label for="'+item.CRR_1_ID+'-'+item.CRR_2_ID+'-'+item.CRR_3_ID+'-'+item.NITRU_ID+'-'+item.NITRU_DEL_YN+'">';
            if(item.NITRU_MIT_ID != null){
              //변경 내역이 있을때
              if(item.NITR_MIT_ID == item.NITRU_MIT_ID && item.NITR_MIT_ID != null){
                if(item.NITRU_DEL_YN == YN_N ){ 
                  // 1-1 변동없는 담당지역 > 체크 & 검정
                  liText += '<input type="checkbox" checked id="'+item.CRR_1_ID+'-'+item.CRR_2_ID+'-'+item.CRR_3_ID+'-'+item.NITRU_ID+'-'+item.NITRU_DEL_YN+'" name="regionBox">';
                  liText += item.CRR_NM;
                }else{  
                  // 1-2 변동 있는 지역 > 담당지역에서 제외 
                  liText += '<input type="checkbox" checked id="'+item.CRR_1_ID+'-'+item.CRR_2_ID+'-'+item.CRR_3_ID+'-'+item.NITRU_ID+'-'+item.NITRU_DEL_YN+'" name="regionBox">';
                  liText += '<span class="red">'+item.CRR_NM+'</span>';
                }
              }else {
                //1-3 추가된 지역
                liText += '<input type="checkbox" id="'+item.CRR_1_ID+'-'+item.CRR_2_ID+'-'+item.CRR_3_ID+'-'+item.NITRU_ID+'-'+item.NITRU_DEL_YN+'" name="regionBox">';
                liText += '<span class="red">'+item.CRR_NM+'</span>';
              }
            }else{
                //변경 내역이 없을때 (item.NITRU_MIT_ID == null)
                if(item.NITR_MIT_ID != null){
                  // 기존 설정 지역
                  liText += '<input type="checkbox" checked id="'+item.CRR_1_ID+'-'+item.CRR_2_ID+'-'+item.CRR_3_ID+'-'+item.NITRU_ID+'-'+item.NITRU_DEL_YN+'" name="regionBox">';
                  liText += item.CRR_NM;
                }else { //미설정 지역 
                  liText += '<input type="checkbox" id="'+item.CRR_1_ID+'-'+item.CRR_2_ID+'-'+item.CRR_3_ID+'-'+item.NITRU_ID+'-'+item.NITRU_DEL_YN+'" name="regionBox">';
                  liText += item.CRR_NM;
                }
            }
            liText +='</label> </li>';
          });
          $('#ulRegion').append(liText);
        }else{
          $('#ulRegion').append('<li class="form-inline"><label for="area1">조회된 지역이 없습니다.</label></li>');
        }
        $('#indicatorArea').hide();
    }else{
        console.log('err');
    }  
  });

}
