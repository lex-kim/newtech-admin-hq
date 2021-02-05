// $('.phone').focus(function(e){
//   $(this).val($(this).val().replace(/-/g, ''));
// });

// $('.phone').blur(function(e){
//   $(this).val(phoneFormat($(this).val()));
// });

$('.business').focus(function(e){
  $(this).val($(this).val().replace(/-/g, ''));
});

$('.business').blur(function(e){
  $(this).val(bizNoFormat($(this).val()));
});

$('.price').focus(function(e){
  $(this).val($(this).val().replace(/,/g, ''));
});

$('.price').blur(function(e){
  $(this).val(numberWithCommas($(this).val()));
});

/* validation 정규식 */
var regExpPhone = /^\d{2,3}-\d{3,4}-\d{4}$/;
var regExpNum =/^[0-9]*$/;
var regExpEmail = /[0-9a-zA-Z][_0-9a-zA-Z-]*@[_0-9a-zA-Z-]+(\.[_0-9a-zA-Z-]+){1,2}$/;
var regExpURL = /^((http(s?))\:\/\/)([0-9a-zA-Z\-]+\.)+[a-zA-Z]{2,6}(\:[0-9]+)?(\/\S*)?$/;


/* timestamp 생성 -> SEND_ID 사용 */
function getTimeStamp() {
	return (String(new Date().getTime()) +Math.random().toString(36).substring(2,13));
}
/**
 * 페이징 처리 공용함수 
 * @using  paging(totalCount, p_CurPAGE+1, 'getList');
 * @param {totalCount : 총 데이터 갯수, currentPage : 현재 페이지, callbackFunc : 클릭스 콜백함수 }
 */
paging = (totalCount, currentPage, callbackFunc) => {
	if(currentPage == 0 || currentPage == undefined){
		currentPage = 1;
	}
    const pageBlock = 5;                                               // 한 화면에 보여줄 페이지 수 
    const totalBlock = Math.ceil(totalCount/($('#perPage').val()*pageBlock));                // 총 페이지 그룹
    const currentBlock = Math.ceil(currentPage/pageBlock);             // 현재 페이지 그룹
    const lastPage = Math.ceil(totalCount/$('#perPage').val());                  // 마지막 페이지
                               
    const first = ((currentBlock-1) * pageBlock)+1;                    // 화면에 보여질 첫번째 페이지 번호
    let last = currentBlock * pageBlock;                               // 화면에 보여질 마지막 페이지 번호

	let html = '';                                                     // 페이징 html
    if(currentBlock > 1){
        html += '<li><a href="javascript:'+callbackFunc+'('+(((currentBlock-2) * pageBlock)+1)+');"><<</a></li>';
    }
    
    if(currentPage > 1){
        html +=  '<li><a href="javascript:'+callbackFunc+'('+(currentPage-1)+');"><</a></li>';
    }

    for(let i=first; i <= last; i++){
        if(i > lastPage) break;
        if(i == currentPage){
            html +=  '<li class="active"><a href="javascript:void(0);">'+i+'</a></li>';
        }else{
            html +=  '<li><a href="javascript:'+callbackFunc+'('+i+');">'+i+'</a></li>';
        }
       
    }

    if(currentPage < lastPage){
        html +=  '<li><a href="javascript:'+callbackFunc+'('+(currentPage+1)+');">></a></li>';
    }

    if(currentBlock < totalBlock){
        html += '<li><a href="javascript:'+callbackFunc+'('+((currentBlock*pageBlock)+1)+');">>></a></li>';
    }
    
    $('.pagination').html(html);

}


  /**
 * 서브 테이블 페이징 처리 공용함수 
 * @using  paging(totalCount, p_CurPAGE+1, 'getList');
 * @param {totalCount : 총 데이터 갯수, currentPage : 현재 페이지, callbackFunc : 클릭스 콜백함수 }
 */
subPaging = (totalCount, currentPage, callbackFunc, perPage, objId) => {
	if(currentPage == 0 || currentPage == undefined){
		currentPage = 1;
	}
    const pageBlock = 5;                                               // 한 화면에 보여줄 페이지 수 
    const totalBlock = Math.ceil(totalCount/(perPage*pageBlock));                // 총 페이지 그룹
    const currentBlock = Math.ceil(currentPage/pageBlock);             // 현재 페이지 그룹
    const lastPage = Math.ceil(totalCount/perPage);                  // 마지막 페이지
                               
    const first = ((currentBlock-1) * pageBlock)+1;                    // 화면에 보여질 첫번째 페이지 번호
    let last = currentBlock * pageBlock;                               // 화면에 보여질 마지막 페이지 번호

	let html = '';                                                     // 페이징 html
    if(currentBlock > 1){
        html += '<li><a href="javascript:'+callbackFunc+'('+(((currentBlock-2) * pageBlock)+1)+');"><<</a></li>';
    }
    
    if(currentPage > 1){
        html +=  '<li><a href="javascript:'+callbackFunc+'('+(currentPage-1)+');"><</a></li>';
    }

    for(let i=first; i <= last; i++){
        if(i > lastPage) break;
        if(i == currentPage){
            html +=  '<li class="active"><a href="javascript:void(0);">'+i+'</a></li>';
        }else{
            html +=  '<li><a href="javascript:'+callbackFunc+'('+i+');">'+i+'</a></li>';
        }
       
    }

    if(currentPage < lastPage){
        html +=  '<li><a href="javascript:'+callbackFunc+'('+(currentPage+1)+');">></a></li>';
    }

    if(currentBlock < totalBlock){
        html += '<li><a href="javascript:'+callbackFunc+'('+((currentBlock*pageBlock)+1)+');">>></a></li>';
    }
    if(objId != undefined){
      $('#'+objId).html(html);
    }else {
      $('#subPagination').html(html);
    }
    

}

/** PDF 팝업창 닫기 */
pdfPopupClose = () => {
  if(pdfPopup != null){
      pdfPopup.close();
      pdfPopup = null;
      doc = null;
  }
}

/** PDF 다운로드 
 * @param {canvas : 이미지 캡쳐}
 * @param {fileNM : 저장할 파일 이름}
 * @param {isMultiPage : 멀티페이지 인지 아닌지}
*/
pdfDownload = (canvas, fileNM, isMultiPage) => {
  if(doc == null){
    doc = new jsPDF('p', 'mm', 'a4'); // html2canvas의 canvas를 png로 바꿔준다. 
  }
 
  if(isMultiPage){
    doc.addPage();
  }

  var imgData = canvas.toDataURL('image/png'); //Image 코드로 뽑아내기 // image 추가 
  var width = doc.internal.pageSize.getWidth();
  var height = doc.internal.pageSize.getHeight()
  doc.addImage(imgData, 'PNG', 0, 0, width, height);
  
  if(fileNM != undefined){
    doc.save(fileNM+'.pdf');
  }
  
}


/**일의자리 절삭 */
cuttingMoney = (money) => {
  return money - (money%10);
}

/**
 * 실수인지 아닌지 또한 소수점이 한자리인지 두자리인지
 * @param {evt: 키 이벤트}
 */
isFloat = (evt) => { // 숫자를 제외한 값을 입력하지 못하게 한다. 
  const objTarget = evt.srcElement || evt.target;
  const charCode = (evt.which) ? evt.which : event.keyCode;     
  const _value = event.srcElement.value;   

  if (event.keyCode != 13){  // 엔터키 제외
    if (event.keyCode < 48 || event.keyCode > 57) { 
      if (event.keyCode != 46) { //숫자와 . 만 입력가능하도록함
        confirmSwalOneButton('정수 혹은 실수만 입력가능합니다.', '확인', ()=>{
          objTarget.focus();
          return false; 
        });
      } 
    } 

    // 소수점(.)이 두번 이상 나오지 못하게
    const _pattern0 = /^\d*[.]\d*$/; // 현재 value값에 소수점(.) 이 있으면 . 입력불가
    if (_pattern0.test(_value)) {
        if (charCode == 46) {
          confirmSwalOneButton('소수점은 한번만 입력 가능합니다.', '확인', ()=>{
            objTarget.focus();
            return false; 
          });
        }
    }
      
    const _pattern2 = /^\d*[.]\d{2}$/; // 현재 value값이 소수점 둘째짜리 숫자이면 더이상 입력 불가
    if (_pattern2.test(_value)) {
        confirmSwalOneButton('소수점 둘째자리까지만 입력가능합니다.', '확인', ()=>{
          objTarget.focus();
          return false; 
        });
        return false;
    }
  }
  

  return true;
}

let beforeValue = 1;
function getBeforeNumberText(evt){
  const objTarget = evt.srcElement || evt.target;
  beforeValue = $(objTarget).val();  
}

is_number = (value) => {
  const regexp = /^[0-9]*$/;
  return regexp.test(value);
}

/**
 * 숫자인지 아닌지 판별해주는 함수
 * @param {eve: 키 이벤트}
 */
isNumber = (evt) => {
  const objTarget = evt.srcElement || evt.target;
  const _value = $(objTarget).val();  

  if(!is_number(_value)) {
    confirmSwalOneButton('0~9까지 정수만 입력하실수 있습니다.', '확인', ()=>{
      objTarget.value = _value.replace(/[^0-9]/g, "");
      objTarget.focus();
    });
    
    return null;
  }
  
}

 //문자열을 금액format에 맞게
 numberWithCommas = (x) =>  {
    if(empty(x))
      return 0;
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  }

  phoneFormat = (num) => {
    if(empty(num)){
        return "-";
    }
    if(num.length == 9){
      return num.replace(/([0-9]{2})([0-9]+)([0-9]{4})/,"$1-$2-$3");
    }else {
      return num.replace(/(^02.{0}|^01.{1}|[0-9]{3})([0-9]+)([0-9]{4})/,"$1-$2-$3");
    }
  }

  // 사업자번호
  bizNoFormat = (num) => {
    return num.replace(/(\d{3})(\d{2})(\d{5})/, '$1-$2-$3');
  }

  validatePhone = (phone) => {
    const re = /^\d{2,3}\d{3,4}\d{4}$/;
      return re.test(phone);
  }

  validateEmail = (email) => {
    const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      return re.test(email);
  }

  validatePasswd = (passwd) => {
    const re = /^.*(?=.{6,20})(?=.*[0-9])(?=.*[a-zA-Z]).*$/;
      return re.test(passwd);
  }

 
  
/**
 * 자리수만큼 0으로 채워주는 함수
 * @using fillZero(CSD_ID, 3);
 * @param {number} num 데이터 값
 * @param {number} width 총 자리수
 */
fillZero = (num, width) => {
	if (num.length >= width) {
		num= num;
	} else {
		num= new Array(width - num.length + 1).join('0') + num ;
    };

    return num;
}


/** 처음 페이지 들어왔을 때 테이블 내용
 * @param {colspan : colsapn}
 */
setInitTableData = (colspan,objId ) => {
  const tableText = "<tr><td class='text-center' colspan='"+colspan+"'>전체내용은 검색조건 없이 검색버튼을 누르시면 됩니다.</td></tr>";
  $('.pagination').html('');
  if(objId == undefined){
    $('#dataTable').html(tableText);
  }else {
    $('#'+objId).html(tableText);
  }
  
}

/**
 * 삭제 컨펌창
 * @param {detailText : 삭제 시 필요한 설명이 있을 경우에 보내준다.}
 */
confirmDelSwal = (detailText) => {
  confirmSwalTwoButton(CONFIRM_DELETE_MSG, DEL_MSG, CANCEL_MSG, true,()=>{delData()}, detailText);
}

/**
 * sweetAlert(alert 대체용.)
 * @param {title : alert 메세지}
  */
alertSwal = (title)=> {
  swal({
    title: title,
    button: OK_MSG ,
  });
}

/**
 * sweetAlert(확인 버튼만 있는)
 * @param {title : alert 메세지, btnText:왼쪽 버튼 텍스트, callback}
  */
 confirmSwalOneButton = (title, btnText, callback, text) =>{

  swal({
    title: title,
    text : text != undefined ? text :'',
    buttons: {   
        confirm: btnText,
    },
  })
  .then((willDelete) => {
    if (willDelete) {
      callback();
    }
  });
}

/**
 * sweetAlert(버튼 2개 있는)
 * @param {title : alert 메세지, leftBtnText:왼쪽 버튼 텍스트, rightBtnText : 오른쪽 버튼 텍스트
 *        isDanger : true일 경우 왼쪽 버튼 칼라가 빨간색/ false면 파란색, callback : confirm 시 작동될 콜백}
 */
confirmSwalTwoButton = (title ,leftBtnText, rightBtnText, isDanger ,callback, text, cancelCallback) =>{
  swal({
    title: title,
    text : text != undefined ? text :'',
    buttons: {   
        confirm: leftBtnText,
        cancel: rightBtnText,
    },
    dangerMode: isDanger,
  })
  .then((willDelete) => {
    if (willDelete) {
      callback();
    } else {

      if( cancelCallback != undefined ){
        cancelCallback();
      }
    }
  });
}

/**
 * php서버와 form통신
 * @param {url : 이동경로, method : get/post, data : 파라메터}
 */
callFormAjax = (url, method, data) => {
  $('#indicator').show();
   return new Promise(function (resolve, reject) {
      $.ajax({
          url:url,
          method: method,
          data: data,
          processData: false,
          contentType: false,
          success: (res) => {
              console.log('res callAjax >>'+res);
              $('#indicator').hide();
              if($('#indicatorTable').is(":visible")){
                $('#indicatorTable').hide();
              }
              resolve(res);
          },
          error : (request, err) => {
              $('#indicator').hide();
              if($('#indicatorTable').is(":visible")){
                $('#indicatorTable').hide();
              }
              console.log('callAjax error ===> \n code:'+request.status+'\n'+'message:'+request.responseText+'\n'+'error:'+err);
              alert(ERROR_MSG_1300)
              return;
          }
      })
  });
}

/**
 * php서버와 form아닌 통신
 * @param {url : 이동경로, method : get/post, data : 파라메터}
 */
callAjax = (url, method, data) => {
  $('#indicator').show();
   return new Promise(function (resolve, reject) {
      $.ajax({
          url:url,
          method: method,
          data: data,
          success: (res) => {
              console.log('res callAjax >>'+res);
              $('#indicator').hide();
              if($('#indicatorTable').is(":visible")){
                $('#indicatorTable').hide();
              }
              resolve(res);
          },
          error : (request, err) => {
              $('#indicator').hide();
              if($('#indicatorTable').is(":visible")){
                $('#indicatorTable').hide();
              }
              
              console.log('callAjax error ===> \n code:'+request.status+'\n'+'message:'+request.responseText+'\n'+'error:'+err);
              alert(ERROR_MSG_1300)
              return;
          }
      })
  });
}

/**
 * php서버와 form아닌 통신
 * @param {url : 이동경로, method : get/post, data : 파라메터}
 */
callAsyncAjax = (url, method, data) => {
  $('#indicator').show();
   return new Promise(function (resolve, reject) {
      $.ajax({
          url:url,
          method: method,
          data: data,
          async : false,
          success: (res) => {
              console.log('res callAjax >>'+res);
              $('#indicator').hide();
              resolve(res);
          },
          error : (request, err) => {
              $('#indicator').hide();
              console.log('callAjax error ===> \n code:'+request.status+'\n'+'message:'+request.responseText+'\n'+'error:'+err);
              alert(ERROR_MSG_1300)
              return;
          }
      })
  });
}

/**
 * php서버와 통신
 * @param {url : 이동경로, method:post, data : 파라메터}
 */
callAjaxFile = (url, data) => {
  $('#indicator').show();
   return new Promise(function (resolve, reject) {
      $.ajax({
        url:         url,
        type:        'POST',
        data:        data,
        enctype:     'multipart/form-data',
        contentType: false,     // 파일전송시 반드시 기술
        processData: false,     // 파일전송시 반드시 기술
        success: (res) => {
          console.log('callAjaxFile res>>'+res);
          resolve(res);
        },
        error: (request, status, error) => {
          console.log('callAjaxFile error req>>>',request,'\n status>>', status,'\n error >>', error);
          alert(ERROR_MSG_1300);
          return;
        }
      });

  });
}

/**
 * 지역구분 옵션
 * @param {objID : 조회한 구분을 넣어줄 select ID}
 * @param {isMultiSelector : 멀티셀렉터 인지 아닌지}
 */
getDivisionOption = (objID, isMultiSelector, initialValue) => {
  $.get('/common/searchOptions.php', {
      REQ_MODE: OPTIONS_DIVISION
  }, function(data, status) {
      const result = JSON.parse(data);

      let optionText = "";
      if(initialValue != undefined){
        optionText = "<option value=''>"+initialValue+"</option>";
      }
      if(result.length > 0){
          result.map((item, index)=>{
              optionText += '<option value="'+item.CSD_ID+'">'+item.CSD_NM+'</option>';
          });
      }
      $('#'+objID).append(optionText);
      if(isMultiSelector){
          $('#'+objID).selectpicker('refresh');
      }

  });
}

/**
 * 선택한 지역구분에 대한 주소정보를 가져온다.
 * @param {val : 선택한 지역구분 값}
 * @param {IS_TOTAL : 값이 없을 경우 전체를 가져올 것인지 구 까지만 가져올것이닞..}
 */
getCRRInfo = (val, isTotal) => {
  const data = {
      REQ_MODE: OPTIONS_CRR,
      CSD_ID : val,
      IS_TOTAL : (isTotal == undefined ? '' : '1')  
  };


  return callAjax('/common/searchOptions.php', 'GET', data).then((result)=>{
      const data = JSON.parse(result);
      return data; 
  });
}

/** 지역구분 조회시 데이터가 있을 경우 해당 Input autocomplete에서
 * @param {targetObj : 조회한 지역구분에 데이터가 있을 경우 disable 해제시켜줄 타겟 인풋}
 * @param {selectObj : autocomplete에서 선택한 값들을 저장할 변수}
 * @param {crrArray : autocomplete를 할 리스트들}
 * @param {minLength : 몇글자에 검색 시작하게 할것 인지}
*/
autoCompleteCRR = (targetObj, selectObj, crrArray, minLength) => {
  $("#"+targetObj).autocomplete({
      minLength : (minLength == undefined ? 0 : 1),
      source: crrArray.map((data, index) => {
          return {
              lable : data.CRR_1_ID+","+data.CRR_2_ID+","+data.CRR_3_ID,
              value : data.CRR_NM
          };
      }),
      focus: function(event, ui){ 
          return false;
      },
      select: function(event, ui) {
          let strArea = ui.item.value;
          $('#'+selectObj).val(ui.item.lable);    //선택한 데이터 value를 저장할곳(hidden)
          $("#"+targetObj).val(strArea);          //선택한 주소를 보여줄 input
          return false;
      }
  }).on('focus', function(){ 
      $(this).autocomplete("search"); 
  });
}

/**
 * 관련된 레퍼런스들을 DB에서 받아서 설정해주는 
 * @param {type : 어떤 레퍼런스를 참조할지}
 * @param {objId : option을 넣어줄 select id}
 * @param {isMultiSelector : 멀티셀렉터 인지 아닌지}
 * @param {isExistBaseValue : 기본값 세팅이 필요한 select인지 아닌지}
 */
getRefOptions = (type, objId, isMultiSelector, isExistBaseValue, initialValue, pSelectedVal) => {
  const data = {
      REQ_MODE: GET_REF_OPTION,
      REF_TYPE : type
  };

  const ajaxUrl = '/common/gwReference.php';
  callAjax(ajaxUrl, 'GET', data).then((result)=>{
      const data = JSON.parse(result);
      if(data.status == OK){
          const dataArray = data.data;

          if(dataArray.length > 0){
              let optionText = '';
      
              if(initialValue != undefined){
                optionText = '<option value="">'+initialValue+'</option>'
              }
              dataArray.map((item, index)=>{
                  if(index == 0 && isExistBaseValue){
                      optionText += '<option value="'+item.ID+'" selected>'+item.NM+'</option>';
                  }else{
                      optionText += '<option value="'+item.ID+'">'+item.NM+'</option>';
                  }
                 
              });
                            
              $('#'+objId).append(optionText);
              if(pSelectedVal != undefined){
                $("#" + objId).val(pSelectedVal);
              }
              if(isMultiSelector){
                  $('#'+objId).selectpicker('refresh');
              }
          }
      } 
     
  });
}

/**
 * 해당 레퍼런스 데이터들을 리턴해주는 함수
 * @param {REF_TYPE : 테이블 REF_***_TYPE 가운데 이름}
 * @param {COL_TYPE : ***_ID 컬럼 앞에 이름} 
 */
getRefTypeData = (REF_TYPE, COL_TYPE) => {
    const data = {
        REQ_MODE: GET_REF_DATA,
        REF_TYPE : REF_TYPE,
        COL_TYPE : COL_TYPE
    };

    const ajaxUrl = '/common/gwReference.php';
    return callAjax(ajaxUrl, 'GET', data).then((result)=>{
              if(isJson(result)){
                  const data = JSON.parse(result);
                  if(data.status == OK){
                      return data;
                  }else{
                    alert(ERROR_MSG_1100);
                  } 
              }else{
                alert(ERROR_MSG_1200);
              }         
          });
}

/**
 * 데이트 포멧
 * @param {data} date: 일자
 */
dateFormat = (date) => {
  var yyyy = date.getFullYear().toString();
  var mm = (date.getMonth() + 1).toString();
  var dd = date.getDate().toString();
  return yyyy + '-' + (mm[1] ? mm : '0'+mm[0]) + '-'+ (dd[1] ? dd : '0'+dd[0]) ;
}

dateYYYYMMDD = (yyyymmdd) => {
  let mm = yyyymmdd.substring(4,6); 
  let dd = yyyymmdd.substring(6,8);
  return yyyymmdd.substring(0,4) + '-' + (mm[1] ? mm : '0'+mm[0]) + '-'+ (dd[1] ? dd : '0'+dd[0]) ; 

}

/**
 * 주소검색
 * @param {objId : 구주소/신주소 고유 input 아이디(해당 값을 세팅해주기 위해.)}
 */
execNewPostCode = (objId) => {
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
       
        $("#"+objId+"_OLD_ZIP").val(data.postcode);
        $("#"+objId+"_OLD_ADDRESS1_AES").val(data.jibunAddress);

        $("#"+objId+"_NEW_ZIP").val(data.zonecode);
        $("#"+objId+"_NEW_ADDRESS1_AES").val(fullAddr);
        $("#"+objId+"_NEW_ADDRESS2_AES").focus();
        
      }
  }).open();
}


/**
 * 기간 설정
 * @param {objId : 태그 id , type:(mindate/maxdate)
 */
getDatePickerSetting = (objId, type) => {
  const data = {
      dateFormat: 'yy-mm-dd',
      prevText: '이전 달',
      nextText: '다음 달',
      monthNames: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
      monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
      dayNames: ['일','월','화','수','목','금','토'],
      dayNamesShort: ['일','월','화','수','목','금','토'],
      dayNamesMin: ['일','월','화','수','목','금','토'],
      showMonthAfterYear: true,
      changeMonth: true,
      changeYear: true,
      yearSuffix: '년',
      yearRange: 'c-50:c+50'
  }
  if(type != undefined){
      $.extend(data, {
          onClose : (selectedDate) => {
              $('#'+objId).datepicker("option", type, selectedDate);
          }
      });
  }
  return data;
}

/**
 * 문자 치환
 * @param {str: 문자열,  searchStr: 변경전 문자열, replaceStr: 변경후 문자열}
 */
replaceAll = (str, searchStr, replaceStr) => {
  return str.split(searchStr).join(replaceStr);
}

/**
 * json형식인지 아닌지
 * @param {str : json인지 판다해줄 문자열} 
 */
isJson = (str) => {
  try {
      JSON.parse(str);
  } catch (e) {
      return false;
  }
  return true;
}


/**
 * 데이터 널 체크 
 * @param {String} val 값
 * @param {String} rep 대체값 없으면 '-'
 */
nullChk = (val, rep) => {
  return val != null ? val : (rep == undefined? '-': rep);
}

/** SEND_ID 생성 */

getSendID = () =>{
  let date = new Date();
  let yyyy = date.getFullYear().toString();
  let mm = (date.getMonth() + 1).toString();
  let dd = date.getDate().toString();
  let hh = date.getHours().toString();
  let min = date.getMinutes().toString();
  let ss = date.getMilliseconds().toString();
  let strTime = yyyy + (mm[1] ? mm : '0'+mm[0])+ (dd[1] ? dd : '0'+dd[0]) + hh + min + ss;
  return  strTime + Math.random().toString(36).substring(2,11);
}

/* 금액 천단위 자르기 */
cutThousand = (money) => {
  money = replaceAll(money,',','');
  let mlen = money.length;
  return mlen < 4 ? 0 : money.substring(0,mlen-3);
}

const empty = (value) => { 
  if (value === null) 
    return true ;
  if (value == "N") 
    return true ;
  if (typeof value === 'undefined') 
    return true ;
  if (typeof value === 'string' && value === '') 
    return true ;
  if (Array.isArray(value) && value.length < 1) 
    return true ;
  if (typeof value === 'object' && value.constructor.name === 'Object' && Object.keys(value).length < 1 && Object.getOwnPropertyNames(value) < 1) 
    return true ;
  if (typeof value === 'object' && value.constructor.name === 'String' && Object.keys(value).length < 1) 
  return true; // new String() return false }

  return false;
}

function getWindow(){
  var opener_window = null; 
  if(opener == null){
    opener_window = window;
  }else if(opener != null && opener.opener != null){
    opener_window = opener.opener;
  }else if(opener != null && opener.opener == null){
    opener_window = opener;
  }

  return opener_window;
}

/**
 * 현재 모니터에 중앙 사이즈를 가져온다.
 * @param {팝업 가로 길이} nWidth 
 * @param {팝업 세로 길이} nHeight 
 */
function getPopupPosition(nWidth, nHeight){
  popupX = (getWindow().screen.availWidth)/2 - (nWidth)/2;
  popupX += getWindow().screenX;
  popupY = (getWindow().screen.height / 2.5) - (nHeight / 2);

  return {
    popupX : popupX,
    popupY : popupY
  };
}

/**
 * 팝업 오픈
 * @param {오픈할 경로} url 
 * @param {오픈할 팝업 이름} pName 
 * @param {오픈할 팝업 가로} nWidth 
 * @param {오픈할 팝업 세로} nHeight 
 */
function openWindowPopup(url, pName ,nWidth, nHeight){
    const position = getPopupPosition(nWidth, nHeight);
    
    window.open(url, pName, 
    'width='+nWidth+',height='+nHeight+', left='+ position.popupX + ', top='+ position.popupY+',toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no');
}

function download(url){
  location.href=location.protocol + '//' + location.host+"/common/download.php?url="+url;
}

function viewKorean(number) { 
  const num = number.toString();
  var hanA = new Array("","일","이","삼","사","오","육","칠","팔","구","십"); 
  var danA = new Array("","십","백","천","","십","백","천","","십","백","천","","십","백","천"); 
  var result = ""; 
  for(i=0; i<num.length; i++) { 
    str = ""; 
    han = hanA[num.charAt(num.length-(i+1))]; 
    if(han != "") 
      str += han+danA[i]; 
      
    if(i == 4) str += "만"; 
    if(i == 8) str += "억"; 
    if(i == 12) str += "조"; 
    result = str + result; 
  } 
  if(num != 0) result = result + " 원"; 
  return result ; 
}


