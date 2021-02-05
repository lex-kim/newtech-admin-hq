const IWTEAM_URL = 'act/iwTeam_act.php';
let CURR_PAGE = 1; 
let tdLength = 0;
$(document).ready(function() {
  tdLength = $('.nwt-table table th').length;
  setInitTableData(tdLength, 'iwTeamContents');
  /** 검색 */
  $("#searchForm").submit(function(e){
      e.preventDefault();
          getList(1);
  });

  /* calender*/
  $('#SEARCH_START_DT').datepicker( 
    getDatePickerSetting('SEARCH_END_DT','minDate')
  );
  $('#SEARCH_END_DT').datepicker(
    getDatePickerSetting('SEARCH_START_DT','maxDate')
  );
  $('#SEARCH_LOG_START_DT').datepicker( 
    getDatePickerSetting('SEARCH_LOG_END_DT','minDate')
  );
  $('#SEARCH_LOG_END_DT').datepicker(
    getDatePickerSetting('SEARCH_LOG_START_DT','maxDate')
  );
  
  setIwOptions('SEARCH_INSU_NM'); 
  setInsuTeam();
  setReqMode('SEARCH_MIT_REQUEST_MODE');

  
  // 전체선택 체크
  $("#allcheck").click(function(){
      if($("#allcheck").prop("checked")){
          $("input[name=teamChkBox]").prop("checked", true);
      }else{
          $("input[name=teamChkBox]").prop("checked", false);
      }
  });

  /* 보험사 변경에 따른 보험사 팀 동적 필터 */
  $('#SEARCH_INSU_NM').change(()=>{
      if($('#SEARCH_INSU_NM').selectpicker('val') != null){
          const selectInsuVal = $('#SEARCH_INSU_NM').selectpicker('val');
          console.log('selectInsuVal',selectInsuVal);
          INSUTEAM_LIST = [];
          selectInsuVal.map((insu)=>{
            console.log();
            INSUTEAM_LIST.push(INSUTEAM_ALL.filter((item, index)=>{
                  return item.MII_ID == insu;
              }));
          });
          selectInsu(INSUTEAM_LIST);
      }else {
        setInsuTeam();
      }
  });

  
  /* 동륵/수정  */
  $( "#iwTeamForm" ).submit(function(e) {
    $("#MIT_OLD_ADDRESS2_AES").val($("#MIT_NEW_ADDRESS2_AES").val());
    e.preventDefault();
    let formData = $("#iwTeamForm").serialize();
    if($('#REQ_MODE').val() == CASE_UPDATE){
      confirmSwalTwoButton('수정 하시겠습니까?','확인', '취소', false ,()=>updateData(formData));
    }else{
        addData(formData);
    }
  });

});

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
        callAjax(IWTEAM_URL, 'POST', data).then((result)=>{
          const data = JSON.parse(result);
          if(data.status == OK){
              $('#area-select-modal').modal('toggle');
              getList();
              alert("담당지역이 설정되었습니다.");
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
  callAjax(IWTEAM_URL, 'POST', data).then((result)=>{
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


/*팀별 담당지역 조회 */
getMiiRegion = (mii) => {
  $('#ulRegion li').remove(); // li 초기화
  let miiId = mii != undefined ? mii : $('#REGION_MII_ID').val()
  const data = {
    REQ_MODE: CASE_READ,
    MII_ID: miiId,
    SEARCH_AREA_KEYWORD: $('#SEARCH_AREA_KEYWORD').val(),
  }
  console.log('getMiiRegion param>>',data);
  callAjax(IWTEAM_URL, 'POST', data).then((result)=>{
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


/* 팀정보 일괄확인  */
allUpdate = () =>{
  let arrTeamId = $("input[name=teamChkBox]:checked").map(function(){
    return $(this).attr('id');
  }).get(); 
  // console.log('appUpdate >>>',arrTeamId);
  let updateCnt = 0;
  if(arrTeamId.length>0){
    arrTeamId.map((item)=>{
      let strItemSplit = item.split('-');
      let mitId = strItemSplit[0];
      let nituId = strItemSplit.length > 1 ? '' : strItemSplit[1];
      // console.log(mitId , ' / ',nituId);
      if(nituId != '' ){
        const data = {
            REQ_MODE: CASE_UPDATE_TEAM,
            MIT_ID: mitId,
            NITU_ID: nituId
        };
      
        callAjax(IWTEAM_URL, 'GET', data).then((result)=>{
            const data = JSON.parse(result);
            if(data.status == OK){
              updateCnt ++;
            }else{
                const resultText ='서버와의 연결이 원활하지 않습니다.' ;
                alertSwal(resultText);
            } 
        });
        if(updateCnt > 0){
          confirmSwalOneButton(updateCnt+'팀 정보가 수정되었습니다.','확인',()=>{getList();});
        }
      }
    })
  }else {
    alert('선택된 보험사팀이 존재하지 않습니다.');
  }


  
  
}

/** 팀 정보 수정요청 수락 
 * @param {params : main List row data}
 */
setReqTeam = (params) => {
  confirmSwalTwoButton(params.MIT_NM+'팀 수정요청을 수락하시겠습니까?','확인', '취소', false ,()=>{
    const data = {
        REQ_MODE: CASE_UPDATE_TEAM,
        MIT_ID: params.MIT_ID,
        NITU_ID: params.NITU_NITU_ID
    };
   
    callAjax(IWTEAM_URL, 'GET', data).then((result)=>{
        const data = JSON.parse(result);
        if(data.status == OK){
            confirmSwalOneButton('수정요청을 수락하였습니다.', '확인',()=>{getList();});
        }else{
            const resultText ='서버와의 연결이 원활하지 않습니다.' ;
            alertSwal(resultText);
        } 
    });
  });
}



/* validation  */
validationData = () => {
  console.log('team tel' , $('#MIT_TEAM_TEL_AES').val());
  if($('#REQ_MODE').val() == CASE_CREATE){
    if($('#MIT_USER_ID').val() != '' && $('#MIT_USER_PASSWD_AES').val() == ''){
      alert('비밀번호를 입력해주세요.');
      $('#MIT_USER_PASSWD_AES').focus();
      return false;
  }
  }
  if($('#MIT_USER_ID').val() == '' && $('#MIT_USER_PASSWD_AES').val() != ''){
    alert('아이디를 입력해주세요.');
    $('#MIT_USER_ID').focus();
    return false;
}

  if($('#MIT_BOSS_EMAIL_AES').val() != '' ){
    if(!validateEmail($('#MIT_BOSS_EMAIL_AES').val())){
      alert('팀장 이메일을 확인해주세요.');
      $('#MIT_BOSS_EMAIL_AES').focus();
      return false;
    }
  }else if($('#MIT_CHG_EMAIL_AES').val() != '' ){
    if(!validateEmail($('#MIT_CHG_EMAIL_AES').val())){
      alert('총무 이메일을 확인해주세요.');
      $('#MIT_CHG_EMAIL_AES').focus();
      return false;
    }
  }else if($('#MIT_TEAM_FAX_AES').val() != ''){
    if(!validatePhone($('#MIT_TEAM_FAX_AES').val())){
      alert('팀 팩스번호를 확인해주세요.');
      $('#MIT_TEAM_FAX_AES').focus();
      return false;
    }
  }else if($('#MIT_CHG_FAX_AES').val() != ''){
    if(!validatePhone($('#MIT_CHG_FAX_AES').val())){
      alert('총무 팩스번호를 확인해주세요.');
      $('#MIT_CHG_FAX_AES').focus();
      return false;
    }
  }else if($('#MIT_CHG_TEL_AES').val() != ''){
    if(!validatePhone($('#MIT_CHG_TEL_AES').val())){
      alert('총무 휴대폰번호를 확인해주세요.');
      $('#MIT_CHG_TEL_AES').focus();
      return false;
    }
  }

  if(!validatePhone($('#MIT_TEAM_TEL_AES').val())){
    alert('대표번호를 확인해주세요.');
    $('#MIT_TEAM_TEL_AES').focus();
    return false;
  }else if(!validatePhone($('#MIT_BOSS_TEL_AES').val())){
    alert('팀장 휴대폰번호를 확인해주세요.');
    $('#MIT_BOSS_TEL_AES').focus();
    return false;
  } else {
    return true;
  }

}
  

/* 등록modal */
showAddModal = () => {
  $("#iwTeamForm").each(function() {  
      this.reset();  
  });  

  $('#REQ_MODE').val(CASE_CREATE);
  $('#add-new').modal();
  $('.modal-right').hide();
  $('.modal-dialog').attr('style', "width:400px;");
  $('.modal-left').attr('style', "width:100%; border-right:0px;");
  setIwOptions('MII_ID'); 
  $('#MII_ID').attr('disabled',false);
  $('#btnDelete').hide();
  $('#submitBtn').html('등록');

  $('#MIT_ENROLL_DT').val(dateFormat(new Date()));
}


/* 등록 */
addData = (data) =>{
  if(validationData()){
    callAjax(IWTEAM_URL, 'POST', data).then((result)=>{
      const data = JSON.parse(result);
      if(data.status == OK){
          $("#add-new").modal("hide");
          alert("등록에 성공하였습니다.");
          getList();
      }else if(data.status == ERROR){
          alert("존재하는 아이디가 있습니다.");
          $('#MIT_USER_ID').focus();
      }else{
          alert("서버와의 연결이 원활하지 않습니다.");
      } 
    });
  }
}


/** 메인 리스트 테이블 동적으로 생성 
 * @param {dataArray : 리스트 데이터, currentPage : 현재페이지(페이징을 위해)} 
*/
setTable = (totalListCount, dataArray, currentPage) => {
    let tableText = '';

    dataArray.map((data, index)=>{
      let chkReqTeam = "";
      let chkRegion = "";
      if(data.NITU_APPLY_YN == YN_N ){
        chkReqTeam ="<button type='button' class='btn btn-default' id='btnRequeset' onclick='setReqTeam("+JSON.stringify(data)+")'>미확인</button>";
      } else {
        chkReqTeam ="확인";
      }

      if(data.MIT_REGION_DT == '' && data.NITRU_APPLY_YN == YN_Y){
        chkRegion ="등록";
      } else {
        if(data.NITRU_APPLY_YN == YN_N){
          chkRegion ="미확인";
        }else if(data.MIT_REGION_DT == '' || data.MIT_REGION_DT == undefined){
          chkRegion ="등록";
        }else{
          chkRegion ="확인";
        }
      }
      
          tableText += "<tr>";
          tableText += "<td class='text-center'><input type='checkbox' name='teamChkBox' id='"+data.MIT_ID+"-"+data.NITRU_ID+"' value=''></td>";
          tableText += "<td class='text-center'>"+data.IDX+"</td>";
          tableText += "<td class='text-center'>"+data.MII_NM+"</td>";
          tableText += "<td class='text-center'>"+nullChk(data.MIT_USER_ID)+"</td>";
          tableText += "<td class='text-center'>"+nullChk(data.MIT_USER_PASSWD_AES)+"</td>";
          tableText += "<td class='text-left'>"+chkRed(data.MIT_NM, data.NITU_MIT_NM, data.NITU_APPLY_YN)+"</td>";
          tableText += "<td class='text-right'>"+data.NON_CNT+"</td>";
          tableText += "<td class='text-center'>"+data.RIT_NM+"</td>";
          tableText += "<td class='text-left'>"
                        +chkRed(data.MIT_NEW_ADDRESS1_AES, data.NITU_MIT_NEW_ADDRESS1_AES, data.NITU_APPLY_YN)+" "
                        +chkRed(data.MIT_NEW_ADDRESS2_AES, data.NITU_MIT_NEW_ADDRESS2_AES, data.NITU_APPLY_YN) +"</td>";
          tableText += "<td class='text-center'>"+data.MIT_REGION_DT+"</td>";
          tableText += "<td class='text-left'>"+chkRed(data.MIT_REGION_MEMO, data.NITU_MIT_REGION_MEMO, data.NITU_APPLY_YN)+"</td>";
          tableText += "<td class='text-center'>"+data.MIT_ENROLL_DT+"</td>";
          tableText += "<td class='text-center'>"+chkRed(data.MIT_BOSS_NM_AES, data.NITU_MIT_BOSS_NM_AES, data.NITU_APPLY_YN)+"</td>";
          tableText += "<td class='text-center'>"+phoneFormat(chkRed(data.MIT_TEAM_TEL_AES, data.NITU_MIT_TEAM_TEL_AES, data.NITU_APPLY_YN))+"</td>";
          tableText += "<td class='text-center'>"+phoneFormat(chkRed(data.MIT_TEAM_FAX_AES, data.NITU_MIT_TEAM_FAX_AES, data.NITU_APPLY_YN))+"</td>";
          tableText += "<td class='text-center'>"+phoneFormat(chkRed(data.MIT_BOSS_TEL_AES, data.NITU_MIT_BOSS_TEL_AES, data.NITU_APPLY_YN))+"</td>";
          tableText += "<td class='text-left'>"+chkRed(data.MIT_BOSS_EMAIL_AES, data.NITU_MIT_BOSS_EMAIL_AES, data.NITU_APPLY_YN)+"</td>";
          tableText += "<td class='text-center'>"+chkRed(data.MIT_CHG_LVL, data.NITU_MIT_CHG_LVL, data.NITU_APPLY_YN)+"</td>";
          tableText += "<td class='text-center'>"+chkRed(data.MIT_CHG_NM_AES, data.NITU_MIT_CHG_NM_AES, data.NITU_APPLY_YN)+"</td>";
          tableText += "<td class='text-center'>"+phoneFormat(chkRed(data.MIT_CHG_FAX_AES, data.NITU_MIT_CHG_FAX_AES, data.NITU_APPLY_YN))+"</td>";
          tableText += "<td class='text-center'>"+phoneFormat(chkRed(data.MIT_CHG_TEL_AES, data.NITU_MIT_CHG_TEL_AES, data.NITU_APPLY_YN))+"</td>";
          tableText += "<td class='text-left'>"+chkRed(data.MIT_CHG_EMAIL_AES, data.NITU_MIT_CHG_EMAIL_AES, data.NITU_APPLY_YN)+"</td>";
          tableText += "<td class='text-center'>"+data.NITU_NITU_REQUEST_DTHMS+"</td>";
          tableText += "<td class='text-center'>"+data.MIT_REQUEST_MODE+"</td>";
          tableText += "<td class='text-center'>"+chkReqTeam+"</td>";
          tableText += "<td class='text-center'><button type='button' class='btn btn-default' onclick='showAreaModal("+JSON.stringify(data)+")'>"+chkRegion+"</button></td>";
          tableText += "<td class='text-left'>"+chkRed(data.MIT_MEMO, data.NITU_MIT_MEMO,data.NITU_APPLY_YN)+"</td>";
          tableText += "<td class='text-center'><button type='button' class='btn btn-danger' onclick='showTeamClose("+data.MIT_ID+")'>팀해제</button></td>";
          if($("#AUTH_UPDATE").val() == YN_Y){
            tableText += "<td class='text-center'><button type='button' class='btn btn-default' onclick='showUpdateModal("+JSON.stringify(data)+")'>수정</button></td>";
          }
          tableText += "</tr>         ";
  
    });
    paging(totalListCount, currentPage, 'getList'); 
    $('#iwTeamContents').html(tableText);
}

/** 수정요청 정보 빨간표기
 * @param {oldData: 현 정보, newData: 변경될 정도  , applyYn: 변경요청 여부(Y: 미요청, N:요청)}
 */
chkRed = (oldData, newData, applyYn) => {
  if(applyYn == YN_N){ //빨간아이
    if(newData != ''){
      return "<span class='red'>"+newData+"</span>";
    }else {
      return oldData;
    }
  } else {
    return oldData;
  }
}
  
/** 메인리스트 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
    const tableText = "<tr><td class='text-center' colspan='"+tdLength+"'>조회요청한 데이터가 존재하지 않습니다.</td></tr>";
    $('.pagination').html('');
    $('#iwTeamContents').html(tableText);
}

/* 엑셀 다운로드 */
excelDownload = () => {
  location.href="iwTeamExcel.html?"+$("#searchForm").serialize();
}

  
/** 지역등록창
 * @param {teamId: 보험사팀 아이디}
 */
showAreaModal = (team) => {
  console.log('showAreaModal>>>', team);
  $('#regionForm').each(function(){
    this.reset();
  })
  $('.modal-dialog').attr('style', "width:250px;");
  $('.modal-left').attr('style', "width:100%; border-right:0px;");
  $('#REGION_MII_ID').val(team.MII_ID);
  $('#MIT_ID').val(team.MIT_ID);
  $('#MIT_NM').val(team.MIT_NM);
  $('#indicatorArea').show();
  getRegion();
  getMiiRegion(team.MII_ID);
  $('#area-select-modal').modal();
}

/* 팀해체 */
showTeamClose = (targetId) => {
  confirmSwalTwoButton('삭제하시겠습니까?','삭제','취소', true,()=>{delData(targetId)} ) ;
}
chkKey = () =>{
  if($('#SEARCH_DT_KEY').val() != '' && $('#SEARCH_START_DT').val () == '' && $('#SEARCH_END_DT').val () == ''){
    alert('기간검색을 확인해주세요.');
    return false;
  }else if($('#SEARCH_BOSS_KEY').val() != ''  && $('#SEARCH_BOSS_KEYWORD').val() == ''){
    alert('팀장검색을 확인해주세요.');
    return false;
  }else if($('#SEARCH_KEY').val() != ''  && $('#SEARCH_KEYWORD').val() == ''){
    alert('키워드검색을 확인해주세요.');
    return false;
  }else{
    return true;
  }
}

/* 메인 리스트 */
getList = (currentPage) => {
  CURR_PAGE = (currentPage==undefined) ? 1 : currentPage;
  $("#MIT_ID").val(''); 
  if(chkKey()){
    let form = $("#searchForm")[0];
    let formData = new FormData(form);
    formData.append('REQ_MODE', CASE_LIST);
    formData.append('PER_PAGE', $("#perPage").val());
    formData.append('CURR_PAGE', CURR_PAGE);
    // formData.append('DIVISION', $('#SEARCH_DIVISION').val(),);
    // formData.append('STR_AREA', $('#SELECT_CRR_ID').val());
    
    callFormAjax(IWTEAM_URL, 'POST', formData).then((result)=>{
        console.log('getList result >>',result);
        const res = JSON.parse(result);
        // console.log('getList json>>>',JSON.stringify( res));
        if(res.status == OK){
            const item = res.data;
            setTable(item[1], item[0], CURR_PAGE);
        }else{
            setHaveNoDataTable();
        }
    });
  }

}


/* 업데이트 모달창일경우 */
showUpdateModal = (data) => {
  console.log('showUpdateModal',data);
  $('.modal-dialog').attr('style', " width: 1200px; height: 100%;");
  $('.modal-left').attr('style', "width: 30%;border-right: 1px solid #ddd;");
  $('.modal-right').show();
  $('#REQ_MODE').val(CASE_UPDATE);
  $('#btnDelete').show();
  $('#submitBtn').html('수정');
  
  /* 보험사회사 정보 */
  setIwOptions('MII_ID',data.MII_ID);
  $('#MII_ID').attr('disabled',true);
  $('#MIT_USER_ID').val(data.MIT_USER_ID);
  $('#MIT_USER_PASSWD_AES').val(data.MIT_USER_PASSWD_AES);
  $('#MIT_USER_PASSWD_AES').attr('placeholder','비밀번호 입력시 비밀번호가 변경됩니다.');
  /* 보험사팀 정보 */
  $('#MIT_ID').val(data.MIT_ID);
  $('#MIT_NM').val(data.MIT_NM);
  $('#MIT_BOSS_NM_AES').val(data.MIT_BOSS_NM_AES);
  $('#MIT_REGION_MEMO').val(data.MIT_REGION_MEMO);
  $('#MIT_NEW_ADDRESS1_AES').val(data.MIT_NEW_ADDRESS1_AES);
  $('#MIT_NEW_ADDRESS2_AES').val(data.MIT_NEW_ADDRESS2_AES);
  $('#MIT_NEW_ZIP').val(data.MIT_NEW_ZIP);
  $('#MIT_TEAM_TEL_AES').val(data.MIT_TEAM_TEL_AES);
  $('#MIT_TEAM_FAX_AES').val(data.MIT_TEAM_FAX_AES);
  $('#MIT_BOSS_TEL_AES').val(data.MIT_BOSS_TEL_AES);
  $('#MIT_BOSS_EMAIL_AES').val(data.MIT_BOSS_EMAIL_AES);
  /* 총무 정보 */
  $('#MIT_CHG_LVL').val(data.MIT_CHG_LVL);
  $('#MIT_CHG_NM_AES').val(data.MIT_CHG_NM_AES);
  $('#MIT_CHG_FAX_AES').val(data.MIT_CHG_FAX_AES);
  $('#MIT_CHG_TEL_AES').val(data.MIT_CHG_TEL_AES);
  $('#MIT_CHG_EMAIL_AES').val(data.MIT_CHG_EMAIL_AES);
  $('#MIT_CHG_MEMO').val(data.MIT_CHG_MEMO);

  $('#MIT_MEMO').val(data.MIT_MEMO);
  $('#MIT_ENROLL_DT').val(data.MIT_ENROLL_DT);

  /* 변경이력 */
  setIwOptions('SEARCH_LOG_INSU_NM');
  $('#DIV_LOG_LIST').hide();

  $('#add-new').modal();
}

/* 수정 */
updateData = (data) =>{
  if(validationData()) {
    callAjax(IWTEAM_URL, 'POST', data).then((result)=>{
      const data = JSON.parse(result);
      if(data.status == OK){
          $("#add-new").modal("hide");
          alert("수정에 성공하였습니다.");
          getList();
      }else if(data.status == ERROR){
          alert("존재하는 아이디가 있습니다.");
          $('#MIT_USER_ID').focus();
      }else{
          alert("서버와의 연결이 원활하지 않습니다.");
      } 
  });
  }
  
  
}

/* 데이터 삭제함수 */
delData = (targetId) => {
  const data = {
      REQ_MODE: CASE_DELETE,
      MIT_ID:   targetId != undefined ? targetId : $("#MIT_ID").val() 
  };
  callAjax(IWTEAM_URL, 'POST', data).then((result)=>{
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

  

/** 리스트 테이블 동적으로 생성 
 * @param {dataArray : 리스트 데이터, currentPage : 현재페이지(페이징을 위해)} 
*/
setLogTable = (totalListCount, dataArray, currentPage, perPage) => {
  let subTableText = '';
  dataArray.map((data, index)=>{
    subTableText += "<tr>";
    subTableText += "<td class='text-center'>"+data.LAST_DTHMS.substring(0,10)+"</td>";
    subTableText += "<td class='text-center'>"+data.MII_NM+"</td>";
    subTableText += "<td class='text-center'>"+data.NITCL_OLD_NM+"</td>";
    subTableText += "<td class='text-center'>"+data.NITCL_OLD_BOSS_NM_AES+"</td>";
    subTableText += "<td class='text-center'>"+phoneFormat(data.NITCL_OLD_TEAM_TEL_AES)+"</td>";
    subTableText += "<td class='text-center'>"+phoneFormat(data.NITCL_OLD_TEAM_FAX_AES)+"</td>";
    subTableText += "<td class='text-center'>"+phoneFormat(data.NITCL_OLD_BOSS_TEL_AES)+"</td>";
    subTableText += "<td class='text-center'>"+data.NITCL_OLD_BOSS_EMAIL_AES+"</td>";
    subTableText += "</tr>";
      
  });
  subPaging(totalListCount, currentPage, 'getSubList', perPage); 
  $('#logListContents').html(subTableText);
}

/** 빈값일 때 동적테이블처리 */
setHaveNoDataLogTable = () => {
  const tdSubLength = $('.modal-table table th').length;
  const subTableText = "<tr><td class='text-center' colspan='"+tdSubLength+"'>조회요청한 데이터가 존재하지 않습니다.</td></tr>";
  $('#subPagination').html('');
  $('#logListContents').html(subTableText);
}


/* 변경이력 메인 리스트 조회 */
getSubList = (currentPage) => {
  $('#DIV_LOG_LIST').show();
  $('#logListContents').empty();
  const data = {
      REQ_MODE: CASE_LIST_SUB,
      MIT_ID: $('#MIT_ID').val(),
      MII_ID: $('#MII_ID').val(),
      PER_PAGE: 10,
      CURR_PAGE:  currentPage,

      /** 변경이력 검색 */
      SEARCH_LOG_START_DT: $('#SEARCH_LOG_START_DT').val(),
      SEARCH_LOG_END_DT: $('#SEARCH_LOG_END_DT').val(),
      SEARCH_LOG_INSU_NM: $('#SEARCH_LOG_INSU_NM').val(),
      SEARCH_LOG_TEAM_NM: $('#SEARCH_LOG_TEAM_NM').val(),
      SEARCH_LOG_KEY: $('#SEARCH_LOG_KEY').val(),
      SEARCH_LOG_KEYWORD: $('#SEARCH_LOG_KEYWORD').val(),
  };

  callAjax(IWTEAM_URL, 'GET', data).then((result)=>{
      // console.log('getSubList result >> ',result);
      const res = JSON.parse(result);
      // console.log(res);
      if(res.status == OK){
          const item = res.data;
          setLogTable(item[1], item[0], currentPage, data.PER_PAGE);
      }else{
          setHaveNoDataLogTable();
      }
  });
}



/*변경이력 엑셀 다운로드 */
logExcelDownload = () => {
  const toURL="iwTeamLogExcel.html?" +
  "SEARCH_LOG_START_DT=" + $('#SEARCH_LOG_START_DT').val() +
  "&SEARCH_LOG_END_DT=" + $('#SEARCH_LOG_END_DT').val() +
  "&SEARCH_LOG_INSU_NM=" + $('#SEARCH_LOG_INSU_NM').val() +
  "&SEARCH_LOG_TEAM_NM=" + $('#SEARCH_LOG_TEAM_NM').val() +
  "&SEARCH_LOG_KEY=" + $('#SEARCH_LOG_KEY').val() +
  "&SEARCH_LOG_KEYWORD=" + $('#SEARCH_LOG_KEYWORD').val() +
  "&MIT_ID=" + $('#MIT_ID').val() ;
  location.href=toURL;
}