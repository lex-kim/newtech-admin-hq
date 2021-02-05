const SALES_URL = 'act/policy_act.php';

$(document).ready(function() {
    
    getData();
  
  });
  

  $(function(){
    $(".btn-update").click(function(){
        $("input[type=text]").removeAttr("readonly");
        $(".btn-back").show();
        $(".btn-save").show();
        $(".btn-update").hide();
    });

    $(".btn-back").click(function(){
        $("input[type=text]").attr("readonly", true);
        $(".btn-back").hide();
        $(".btn-save").hide();
        $(".btn-update").show();
    });

    $(".btn-back").hide();
    $(".btn-save").hide();
  })




validationData = () => {
  let num_check=/^\d{2}$/;

  if($('#PPE_BILL_PRICE').val().trim()==''){
      alertSwal('청구기준금액을 입력해주세요.');
      $('#PPE_BILL_PRICE').focus();
      return false;
  }else if($('#PPE_POINT_EXPIRE').val().trim()==''){
      alertSwal('포인트소멸조건을 입력해주세요.');
      $('#PPE_POINT_EXPIRE').focus();
      return false;
  }else if($('#PPE_NOTICE_EXPIRE').val().trim()==''){
      alertSwal('포인트 소멸예정을 입력해주세요.');
      $('#PPE_NOTICE_EXPIRE').focus();
      return false;
  }else {
      return true;
  }
}

/** 데이터 세팅 */
getData = () => {
  const data = {
      REQ_MODE : CASE_READ
  };
  callAjax(SALES_URL, 'GET', data).then((result)=>{
      const data = JSON.parse(result);
      if(data.status == OK){
          const item = data.data;
          $('#PPE_BILL_PRICE').val(item.PPE_BILL_PRICE);
          $('#PPE_POINT_EXPIRE').val(item.PPE_POINT_EXPIRE);    
          $('#PPE_NOTICE_EXPIRE').val(item.PPE_NOTICE_EXPIRE);    

          showUpdate();
      }else{
          const resultText ='서버와의 연결이 원활하지 않습니다.' ;
          alert(resultText);
      } 
  });
}

showUpdate = () => {
  $(".btn-update").html('수정');
  $(".btn-back").hide();
  $(".btn-save").hide();
  $(".btn-save").attr('onclick', 'updateData()');
  $("input[type=text]").attr("readonly", true);

  $(".btn-update").show();
}


/** 권한관리 업데이트 */
updateData = () => {
  if(validationData()){
      const data = {
          REQ_MODE: CASE_UPDATE,

          PPE_BILL_PRICE : $("#PPE_BILL_PRICE").val(),
          PPE_POINT_EXPIRE : $("#PPE_POINT_EXPIRE").val(),
          PPE_NOTICE_EXPIRE : $("#PPE_NOTICE_EXPIRE").val()
      };
      callAjax(SALES_URL, 'POST', data).then((result)=>{
          const data = JSON.parse(result);
          if(data.status == OK){
              confirmSwalOneButton('수정에 성공하였습니다.','확인',()=>getData());
          }else{
              const resultText ='서버와의 연결이 원활하지 않습니다.' ;
              alert(resultText);
          }   
      });
  }
}
