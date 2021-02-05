<form class="form-horizontal" name="editForm" id="editForm" method="post">
  <div class="container-fluid mb20px">
    <div class="nwt-policy-top">
      <div>
        <h3 class="nwt-category"><small>코드관리&nbsp;&nbsp;></small>통합배송정책 관리</h3>
      </div>  
    </div>
  </div>
    

  <!-- 보상담당분류기준 -->
  <div class="container-fluid policy-body mb20px">
      <div class="policy-body-content">
            
            <div class="row">
                <!-- 출고일 -->
                <div class="col-md-3"><label for="deliveryFee">기본 배송비</label></div>
                <div class="col-md-5" >
                    <input type="text" class="form-control text-right price" value="<?php echo number_format($deliveryEnvInfo['deliveryFee']) ?>"
                         onkeyup="isNumber(event)"  id="deliveryFee" name="deliveryFee" placeholder="숫자만 입력" readonly required/>
                </div>
                <div class="col-md-2">
                  <span>원</span>
                </div>
                
                
            </div>    
            <div class="row">
                <!-- 결제구분 -->
                <div class="col-md-3"><label for="deliveryFeeFree">무료배송 기준</label></div>
                <div class="col-md-5">
                    <input type="text" class="form-control price text-right" value="<?php echo number_format($deliveryEnvInfo['deliveryFeeFree']) ?>"
                         onkeyup="isNumber(event)" id="deliveryFeeFree" name="deliveryFeeFree" placeholder="숫자만 입력" readonly required/>
                </div>
                <div class="col-md-2">
                  <span>원 이상 무료배송</span>
                </div>
            </div>  
        </div>
  </div>

 
  <div class="container-fluid mb20r">
    <div class="row policy-footer">
      <div class="col-lg-6">
        <div class="form-inline">
          
        </div>
      </div>
      <!-- 테이블 우측 상단 btn -->
      <div class="col-lg-6 clearfix">
        <div class="form-inline f-right">
          <!-- 수정 btn -->
          <button type="button" class="btn btn-primary btn-update" onclick="update()">수정</button>
          <!-- 수정btn -->
          <button type="submit" class="btn btn-primary btn-save" style="display:none">저장</button>
          <!-- 취소btn -->
          <button type="button" class="btn btn-default btn-back" onclick="back()" style="display:none">취소</button>
        </div>
      </div>

    </div>
  </div>
</form>
<!-- footer.html -->
<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/footerShop.html";
?>
<script>
  function update(){
      $("input[type=text]").removeAttr("readonly");
      $(".btn-back").show();
      $(".btn-save").show();
      $(".btn-update").hide();
  }

  function back(){
     
      $('#deliveryFee').val("<?php echo number_format($deliveryEnvInfo['deliveryFee']) ?>");
      $('#deliveryFeeFree').val("<?php echo number_format($deliveryEnvInfo['deliveryFeeFree']) ?>");

      $("input[type=text]").attr("readonly", true);
      $(".btn-back").hide();
      $(".btn-save").hide();
      $(".btn-update").show();
  }

</script>