
 <div class="modal modal-add fade" id="transfer_modal" role="dialog">
   <div class="modal-dialog">
    <div class="modal-content">
    <form name="transferForm" id="transferForm">
      <!-- modal 상단 -->
      <div class="modal-header">
          <h4 class="modal-title">환불받을 은행 입력</h4>
      </div>

      <!-- modal 내용 -->
      <div class="modal-body"> 
          <!-- 전화번호 -->
          <div class="row">
            <div class="col-md-3"><label for="mgr_bank_cd">은행명</label></div>
            <div class="col-md-8">
                <select id="mgr_bank_cd" name="mgr_bank_cd" class="form-control" required>
                    <option value="">=선택=</option>
                    <option value="002">산업은행</option>
                    <option value="003">기업은행</option>
                    <option value="004">국민은행</option>
                    <option value="005">외환은행</option>
                    <option value="007">수협은행</option>
                    <option value="011">농협중앙회</option>
                    <option value="012">단위농협</option>
                    <option value="020">우리은행</option>
                    <option value="023">SC제일은행</option>
                    <option value="026">신한은행</option>
                    <option value="027">한국시티은행</option>
                    <option value="031">대구은행</option>
                    <option value="032">부산은행</option>
                    <option value="081">하나은행</option>
                    <option value="088">신한은행</option>
                    <option value="089">K뱅크</option>
                    <option value="090">카카오뱅크</option>
                    <option value="209">동양종금증권</option>
                    <option value="218">현대증권</option>
                    <option value="230">미래에셋</option>
                    <option value="238">대우증권</option>
                    <option value="240">삼성증권</option>
                    <option value="243">한국투자증권</option>
                    <option value="247">우리투자증권</option>
                    <option value="262">하이증권</option>
                    <option value="263">HMC증권</option>
                </select>
            </div>
          </div>

          <!-- 전화번호 -->
          <div class="row">
            <div class="col-md-3"><label for="mgr_account">계좌번호</label></div>
            <div class="col-md-8">
                <input type="text" class="form-control" onkeyup="isNumber(event)"
                    name="mgr_account" id="mgr_account" placeholder="-없이 숫자만 입력" required/>  
            </div>
          </div>

          <!-- 전화번호 -->
          <div class="row">
            <div class="col-md-3"><label for="mgr_depositor">예금주명</label></div>
            <div class="col-md-8">
                <input type="text" class="form-control" 
                    name="mgr_depositor" id="mgr_depositor" placeholder="입력" />  
            </div>
          </div>
      </div>

      <!-- modal 하단 -->
      <div class="modal-footer">
        <div class="text-left">
        </div>
        <div class="text-right">
          <!-- 환불 -->
          <button type="submit" class="btn btn-primary" id="transferRefuBtn" name ="transferRefuBtn">환불요청</button>
          <!-- 취소btn -->
          <button type="button" class="btn btn-default" data-dismiss="modal">닫기</button>
        </div>
      </div>

    </form>
    </div>
  </div>
</div