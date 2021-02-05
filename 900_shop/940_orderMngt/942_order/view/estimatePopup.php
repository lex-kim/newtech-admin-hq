
  <!-- jQuery v1.12.4 -->
<script src="/js/jquery-1.12.4.min.js"></script>
<!-- Bootstrap v3.3.7 -->
<script src="/bootstrap/js/bootstrap.min.js"></script>
<!-- editor js -->
<script type="text/javascript" src="/summernote/summernote.js"></script>
<!-- multiselect.js -->


<!-- 상수 js -->
<script src="/js/const.js"></script>

<script src="/js/error.js"></script>
<script src="/js/common.js"></script>
<script src="/js/searchOptions.js"></script>
<script src="js/app.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.0/jquery.form.min.js" integrity="sha384-E4RHdVZeKSwHURtFU54q6xQyOpwAhqHxy2xl9NLW9TQIqdNrNh60QVClBRBkjeB8" crossorigin="anonymous"></script>
<script src="/js/sweetalert.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/js/bootstrap-select.min.js"></script> 


  <div class="container-fluid mb20px">
    <h3 class="nwt-category">견적서 작성</h3>
  <!-- <img data-src="https://ik.imagekit.io/demo/default-image.jpg" class="aa" style="width:100px; height:100px"/><br/> -->


  <div class="order-content">
    
    <div class="order-left">
      <!-- 상품메뉴&검색 -->
      <?php	require "../944_estimate/estimateLeftTop.php"; ?>
      

      <!-- 상품리스트 -->
      <div class="nwt-table order-list" style="width: 100%;">
  
        <?php	require "../944_estimate/estimateLeft.html"; ?>
        
      </div>

       <!-- pagination -->
       <div class="container-fluid pagination_area">
        <div class="text-center">
          <ul class="pagination">
          </ul>
        </div>
      </div>

    </div>

    <div class="order-right">
          <!-- 오른쪽영역  -->
          <?php	require "../944_estimate/estimateRight.html"; ?> 
    </div>
  </div>
  <!-- 부모창 정보 -->
  <input type="hidden" id="parentUrl" value=""/>  




  <!-- indicator -->
  <!-- <?php	require $_SERVER['DOCUMENT_ROOT']."/include/indicator.html"; ?> -->

  
    <!-- 등록/수정 모달  -->
    <?php	require "./view/estimateInfoPopup.html"; ?>

    <!-- footer.html -->
    <?php require $_SERVER['DOCUMENT_ROOT']."/include/footerInfo.html"; ?>


</body>
<script>
  if ('loading' in HTMLImageElement.prototype) {
    const images = document.querySelectorAll("img.lazyload");  
    images.forEach(img => {
      img.src = img.dataset.src;
    });
  } else {
    let script = document.createElement("script");
    script.async = true;
    script.src = "https://cdnjs.cloudflare.com/ajax/libs/lazysizes/4.1.8/lazysizes.min.js";
    document.body.appendChild(script);
  }
</script>
<script>
   let shoppingSerilize = "";
   $('#editForm').submit(function(e){
        e.preventDefault();

        if(empty($('#productID').val())){
            shoppingSerilize = $(this).serialize()+"&"+shoppingSerilize+"&REQ_MODE=<?php echo CASE_CREATE ?>";
            confirmSwalTwoButton('저장 하시겠습니까?','확인', '취소', false ,()=>setData());
        }else{
            shoppingSerilize = $(this).serialize()+"&"+shoppingSerilize+"&REQ_MODE=<?php echo CASE_UPDATE ?>";
            confirmSwalTwoButton('수정 하시겠습니까?','확인', '취소', false ,()=>setData());

        }
    });

    function getGarageInfo(){
      const data = {
        REQ_MODE: CASE_READ,
        garageID : $('#garageID').selectpicker('val') 
      }
      callAjax(ERP_CONTROLLER_URL, 'POST', data).then(function(result){
          if(isJson(result)){
            const res = JSON.parse(result);
            if(res.status == OK){
              const data = res.data;
              confirmSwalTwoButton('견적서 작성하시겠습니까?','확인','취소',false,function(){
                $("#ESTIMATE_STORE_NM").val(data.garageName);
                $('#ESTIMATE_NM').val(data.ceoName);
                $('#ESTIMATE_PHONE').val(data.phoneNum);
                $('#estimate-info').modal('show');
                $("#ESTIMATE_EMAIL").focus();
              }); 
            }else{
              alert(ERROR_MSG_1100);
            }
          }else{
              alert(ERROR_MSG_1200);
          }
      });
    }

    $('#shoppingForm').submit(function(e){
        e.preventDefault();

        if($('#orderTable .order-item').length == 0){
          alert("견적 품목 리스트에 담은 상품이 없습니다.");
        }else{
          shoppingSerilize = $(this).serialize();
          if(empty(this.submited) ){ // 견적서 작성
            if(!empty($('#garageID').selectpicker('val') )){
              getGarageInfo();
            }else{
              alert("공업사를 선택해주세요."); 
              $('#garageID').focus();
            } 
          }else{  // 주문
            if(!empty($('#garageID').selectpicker('val') )){
                
                if(isHaveSoldOutItem()){
                  alert("품절된 상품이 존재합니다.");
                }else{
                  confirmSwalTwoButton('주문하시겠습니까?','확인','취소',false,function(){
                    onClickOrderEstimate();
                  }); 
                }
                
              
            }else{
              alert("공업사를 선택해주세요.");
              $('#garageID').focus();
            }
           
          }

          
        }
       
    });

    
</script>

</html>