<style>

  .labelText{
    padding-top  : 6px;
  }
</style>

<form class="form-horizontal" name="editForm" id="editForm" method="post">
  <div class="container-fluid mb20px">
    <div class="nwt-policy-top">
      <div>
        <h3 class="nwt-category"><small>코드관리&nbsp;&nbsp;></small>설정</h3>
      </div>  
    </div>
  </div>
    

  <!-- 보상담당분류기준 -->
  <div class="container-fluid policy-body mb20px">


      <div class="policy-body-content">
        
        <!-- 견적서 유효기간 -->
        <div class="row">
          <div class="col-md-3"><label for="ESTIMATE_EXPIRATION_DT">견적서 유효기간 (일)</label></div>
          <div class="col-md-5">
            <input type="text" class="form-control" value="<?php echo $cSetting['quotationLimitDays']?>"
              onkeyup="isNumber(event)" id="ESTIMATE_EXPIRATION_DT" name="ESTIMATE_EXPIRATION_DT" placeholder="숫자만 입력" required />
          </div>
        </div>
        
        <!-- 견적서 발송 메시지 -->
        <div class="row">
          <div class="col-md-3"><label for="ESTIMATE_SEND_MESSAGE">견적서 발송 메세지</label></div>
          <div class="col-md-5">
            <input type="text" class="form-control" value="<?php echo $cSetting['quotationSendMessage']?>"
              id="ESTIMATE_SEND_MESSAGE" name="ESTIMATE_SEND_MESSAGE" placeholder="입력" required/>
          </div>
        </div>
        
        <!-- 결제완료 메시지 -->
        <div class="row">
          <div class="col-md-3"><label for="ESTIMATE_PAY_MESSAGE">결제완료 메시지</label></div>
          <div class="col-md-9">
            <input type="text" class="form-control" value="<?php echo $cSetting['paymentDoneMessage']?>"
              id="ESTIMATE_PAY_MESSAGE" name="ESTIMATE_PAY_MESSAGE" placeholder="입력" required/>
          </div>
        </div>
        
        <!-- 주문완료 메시지 -->
        <div class="row">
          <div class="col-md-3"><label for="ESTIMATE_ORDER_MESSAGE">주문완료 메시지</label></div>
          <div class="col-md-9">
            <input type="text" class="form-control" value="<?php echo $cSetting['orderDoneMessage']?>"
              id="ESTIMATE_ORDER_MESSAGE" name="ESTIMATE_ORDER_MESSAGE" placeholder="입력" required/>
          </div>
        </div>

        <hr>
        
        <!-- 포인트 사용가능 최소금액 -->
        <div class="row">
          <div class="col-md-3"><label for="POINT_USE_PRICE">포인트 사용가능 최소금액</label></div>
          <div class="col-md-5">
            <input type="text" class="form-control price" value="<?php echo $cSetting['pointRedeemMinimumPrice'] ?>"
              id="POINT_USE_PRICE" name="POINT_USE_PRICE" placeholder="숫자만 입력" required/>
          </div>
        </div>
        
        <!-- 포인트 사용 시 적립정책 -->
        <div class="row">
          <div class="col-md-3 labelText"><label for="POINT_USE_POLICY">포인트 사용 시 적립정책</label></div>
          <div class="col-md-8">
            <?php
                $savePointYChecked = "checked";
                $savePointNChecked = "";
  
                if($cSetting['pointUseRedeemYN'] != "Y"){
                  $savePointYChecked = "";
                  $savePointNChecked = "checked";
                }
            ?>
            <label class="radio-inline">
              <input type="radio" name="POINT_USE_POLICY" <?php echo $savePointYChecked  ?>
                  value="Y" required/>포인트 사용 시 미적립
            </label>
            <label class="radio-inline">
                <input type="radio" name="POINT_USE_POLICY" <?php echo $savePointNChecked  ?>
                  value="N" required/>포인트 사용 시 차감금액 중 적립
              </label>
          </div>
        </div>

        <hr>
        <div class="row">
          <div class="col-md-3 labelText"><label for="showStatementPriceYN">거래명세서 금액 노출 여부</label></div>
          <div class="col-md-8">
            <?php
                $showStateYChecked = "checked";
                $showStateNChecked = "";
  
                if($cSetting['showStatementPriceYN'] != "Y"){
                  $showStateYChecked = "";
                  $showStateNChecked = "checked";
                }
            ?>
            <label class="radio-inline">
              <input type="radio" name="showStatementPriceYN" <?php echo $showStateYChecked  ?>
                  value="Y" required/>노출
            </label>
            <label class="radio-inline">
                <input type="radio" name="showStatementPriceYN" <?php echo $showStateNChecked  ?>
                  value="N" required/>미노출
              </label>
          </div>
        </div>

        <hr>
        <div class="row">
          <div class="col-md-3 labelText"><label for="sendSmsOrderYN">주문완료시 문자발송 여부</label></div>
          <div class="col-md-8">
            <?php
                $showStateYChecked = "checked";
                $showStateNChecked = "";
  
                if($cSetting['sendSmsOrderYN'] != "Y"){
                  $showStateYChecked = "";
                  $showStateNChecked = "checked";
                }
            ?>
            <label class="radio-inline">
              <input type="radio" name="sendSmsOrderYN" <?php echo $showStateYChecked  ?>
                  value="Y" required/>발송
            </label>
            <label class="radio-inline">
                <input type="radio" name="sendSmsOrderYN" <?php echo $showStateNChecked  ?>
                  value="N" required/>미발송
              </label>
          </div>
        </div>

        <hr>
        <div class="row">
          <div class="col-md-3 labelText"><label for="sendSmsPaidYN">결제완료시 문자발송 여부</label></div>
          <div class="col-md-8">
            <?php
                $showStateYChecked = "checked";
                $showStateNChecked = "";
  
                if($cSetting['sendSmsPaidYN'] != "Y"){
                  $showStateYChecked = "";
                  $showStateNChecked = "checked";
                }
            ?>
            <label class="radio-inline">
              <input type="radio" name="sendSmsPaidYN" <?php echo $showStateYChecked  ?>
                  value="Y" required/>발송
            </label>
            <label class="radio-inline">
                <input type="radio" name="sendSmsPaidYN" <?php echo $showStateNChecked  ?>
                  value="N" required/>미발송
              </label>
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
          <button type="submit" class="btn btn-primary btn-save">저장</button>
          <!-- 취소btn -->
          <button type="button" class="btn btn-default btn-back" onclick="back()">취소</button>
        </div>
      </div>

    </div>
  </div>
</form>
<!-- footer.html -->
<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/footerShop.html";
?>