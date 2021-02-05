
<form class="form-horizontal" name="editForm" id="editForm" method="post">
  <div class="container-fluid mb20px">
    <div class="nwt-policy-top">
      <div>
        <h3 class="nwt-category"><small>코드관리&nbsp;&nbsp;></small>쇼핑몰 사업자정보</h3>
      </div>  
    </div>
  </div>
    

  <!-- 보상담당분류기준 -->
  <div class="container-fluid policy-body mb20px">
      <!-- <div class="policy-title">
        <p>보상담당분류기준</p>
        <p>보상담당분류기준은 보험사 보상담당자의 소액/고액을 분류하는 기준금액입니다.</p>
      </div> -->

      <div class="policy-body-content">

        <!-- 사업자등록번호 -->
        <div class="row">
          <div class="col-md-3"><label for="BIZ_NUM">사업자등록번호 <span class="necessary">*</span></label></div>
          <div class="col-md-5">
            <input type="text" class="form-control business" value="<?php echo bizNoFormat($cInfo['companyRegID'])  ?>"
              id="BIZ_NUM" name="BIZ_NUM" placeholder="'-'를 제외한 숫자만 입력" required/>
          </div>
        </div>
        
        <!-- 종사업장번호 -->
        <!-- <div class="row">
          <div class="col-md-3"><label for="STORE_NUM">종사업장번호</label></div>
          <div class="col-md-5">
            <input type="text" class="form-control" id="STORE_NUM" name="STORE_NUM" placeholder="'-'를 제외한 숫자만 입력">
          </div>
        </div> -->

        <!-- 상호 -->
        <div class="row">
          <div class="col-md-3"><label for="GW_STORE_NM">상호 <span class="necessary">*</span></label></div>
          <div class="col-md-5">
            <input type="text" class="form-control" value="<?php echo $cInfo['companyName'] ?>"
              id="GW_STORE_NM" name="GW_STORE_NM" placeholder="입력" required/>
          </div>
        </div>

        <!-- 대표자명 -->
        <div class="row">
          <div class="col-md-3"><label for="GW_NM">대표자명 <span class="necessary">*</span></label></div>
          <div class="col-md-5">
            <input type="text" class="form-control" value="<?php echo $cInfo['ceoName'] ?>"
              id="GW_NM" name="GW_NM" placeholder="입력" required/>
          </div>
        </div>

        <!-- 사업장주소 -->
        <div class="row">
          <div class="col-md-3"><label for="GW_ADDRESS">사업장주소 <span class="necessary">*</span></label></div>
          <div class="col-md-4">
            <input type="text" class="form-control" placeholder='우편번호' value="<?php echo $cInfo['zipCode'] ?>"
              id="MIT_NEW_ZIP" name="MIT_NEW_ZIP" readonly required/>
          </div>
          <button type="button" class="btn btn-default" onclick="isUpdatMode('MIT_NEW')">주소검색</button>
        </div>
        <div class="row">  
          <div class="col-md-3"><label for="MIT_NEW_ADDRESS1"></label></div>
          <div class="col-md-7">
            <input type="text" class="form-control" placeholder='주소' value="<?php echo $cInfo['Address1'] ?>"
              id="MIT_NEW_ADDRESS1_AES" name="MIT_NEW_ADDRESS1_AES" readonly required/>
          </div>
        </div>
        <div class="row">
          <div class="col-md-3"><label for="MIT_NEW_ADDRESS2"></label></div>
          <div class="col-md-7">
            <input type="text" class="form-control" value="<?php echo $cInfo['Address2'] ?>"
              id="MIT_NEW_ADDRESS2_AES" name="MIT_NEW_ADDRESS2_AES" placeholder="나머지 주소"/>
          </div>
        </div>
        
         <!-- 물류창고 주소 -->
         <div class="row">
          <div class="col-md-3"><label for="STOCK_NEW_ADDRESS">물류창고 주소</label></div>
          <div class="col-md-4">
            <input type="text" class="form-control" placeholder='우편번호'
              id="STOCK_NEW_ZIP" name="STOCK_NEW_ZIP"  value="<?php echo $cInfo['stockZipCode'] ?>" readonly/>
          </div>
          <button type="button" class="btn btn-default" onclick="isUpdatMode('STOCK_NEW')">주소검색</button>
        </div>
        <div class="row">  
          <div class="col-md-3"><label for="STOCK_NEW_ADDRESS1"></label></div>
          <div class="col-md-7">
            <input type="text" class="form-control" placeholder='주소' value="<?php echo $cInfo['stockAddress1'] ?>"
              id="STOCK_NEW_ADDRESS1_AES" name="STOCK_NEW_ADDRESS1_AES" readonly/>
          </div>
        </div>
        <div class="row">
          <div class="col-md-3"><label for="STOCK_NEW_ADDRESS2"></label></div>
          <div class="col-md-7">
            <input type="text" class="form-control" value="<?php echo $cInfo['stockAddress2'] ?>"
              id="STOCK_NEW_ADDRESS2_AES" name="STOCK_NEW_ADDRESS2_AES" placeholder="나머지 주소"/>
          </div>
        </div>

        <!-- 업태 -->
        <div class="row">
          <div class="col-md-3"><label for="GW_CONDITION">업태 <span class="necessary">*</span></label></div>
          <div class="col-md-5">
            <input type="text" class="form-control" value="<?php echo $cInfo['companyBiz'] ?>"
              id="GW_CONDITION" name="GW_CONDITION" placeholder="입력" required/>
          </div>
        </div>

        <!-- 종목 -->
        <div class="row">
          <div class="col-md-3"><label for="GW_ITEM">종목 <span class="necessary">*</span></label></div>
          <div class="col-md-5">
            <input type="text" class="form-control" value="<?php echo $cInfo['companyPart'] ?>"
              id="GW_ITEM" name="GW_ITEM" placeholder="입력" required>
          </div>
        </div>

        <!-- 사무실전화 -->
        <div class="row">
          <div class="col-md-3"><label for="GW_TEL">사무실전화 <span class="necessary">*</span></label></div>
          <div class="col-md-5">
            <input type="text" class="form-control phone" value="<?php echo setPhoneFormat($cInfo['phoneNum']) ?>"
               onkeyup="isNumber(event)" id="GW_TEL" name="GW_TEL" placeholder="'-'를 제외한 숫자만 입력" required/>
          </div>
        </div>

        <!-- 사무실팩스 -->
        <div class="row">
          <div class="col-md-3"><label for="GW_FAX">사무실팩스 <span class="necessary">*</span></label></div>
          <div class="col-md-5">
            <input type="text" class="form-control phone" value="<?php echo setPhoneFormat($cInfo['faxNum']) ?>"
              onkeyup="isNumber(event)" id="GW_FAX" name="GW_FAX" placeholder="'-'를 제외한 숫자만 입력" required/>
          </div>
        </div>

        <!-- 이메일 -->
        <div class="row">
          <div class="col-md-3"><label for="GW_EMAIL">이메일 <span class="necessary">*</span></label></div>
          <div class="col-md-5">
            <input type="text" class="form-control" value="<?php echo $cInfo['companyEmail'] ?>"
              id="GW_EMAIL" name="GW_EMAIL" placeholder="id@email.com" required/>
          </div>
        </div>

        <!-- 입금계좌 은행명 -->
        <div class="row">
          <div class="col-md-3"><label for="GW_BANK_NM">입금계좌 은행명 <span class="necessary">*</span></label></div>
          <div class="col-md-5">
            <select class="form-control input-md" id="GW_BANK_NM" name="GW_BANK_NM" required>
               <?php echo getOption($this->optionArray['BANK_CD'], "" , $cInfo['bankCode']) ?>
            </select>
          </div>
        </div>

        <!-- 입금계좌번호 -->
        <div class="row">
          <div class="col-md-3"><label for="GW_BANK_NUM">입금계좌번호 <span class="necessary">*</span></label></div>
          <div class="col-md-5">
            <input type="text" class="form-control" value="<?php echo $cInfo['bankNum'] ?>"  onkeyup="isNumber(event)"
              id="GW_BANK_NUM" name="GW_BANK_NUM" placeholder="'-'를 제외한 숫자만 입력" required/>
            
          </div>
        </div>

        <!-- 계좌주 -->
        <div class="row">
          <div class="col-md-3"><label for="GW_BANK_OWN">계좌주 <span class="necessary">*</span></label></div>
          <div class="col-md-5">
            <input type="text" class="form-control" value="<?php echo $cInfo['bankOwnName'] ?>"
              id="GW_BANK_OWN" name="GW_BANK_OWN" placeholder="입력" required/>
          </div>
        </div>

        <!-- 통신판매업신고번호 -->
        <div class="row">
          <div class="col-md-3"><label for="GW_SALE_NUM">통신판매업신고번호 <span class="necessary">*</span></label></div>
          <div class="col-md-5">
            <input type="text" class="form-control" value="<?php echo $cInfo['ecRegNum'] ?>"
              id="GW_SALE_NUM" name="GW_SALE_NUM" placeholder="'-'를 제외한 숫자만 입력" required/>
          </div>
        </div>

         <!-- 과세형태 -->
         <div class="row">
          <div class="col-md-3"><label for="taxTypeCode">과세형태 <span class="necessary">*</span></label></div>
          <div class="col-md-5">
            <?php echo getRadio($this->optionArray['EC_BILL_TAX_OPTION'], "taxTypeCode", $cInfo['taxTypeCode']) ?>
          </div>
        </div>

         <!-- 담당자명 -->
         <div class="row">
          <div class="col-md-3"><label for="fromChargeName">담당자명 <span class="necessary">*</span></label></div>
          <div class="col-md-5">
            <input type="text" class="form-control" value="<?php echo $cInfo['fromChargeName'] ?>" placeholder="입력"
              id="fromChargeName" name="fromChargeName" required/>
          </div>
        </div>

        <!-- 휴대폰 -->
        <div class="row">
          <div class="col-md-3"><label for="fromChargePhoneNum">담당자 휴대폰 <span class="necessary">*</span></label></div>
          <div class="col-md-5">
            <input type="text" class="form-control" value="<?php echo $cInfo['fromChargePhoneNum'] ?>"  onkeyup="isNumber(event)"
              id="fromChargePhoneNum" name="fromChargePhoneNum" placeholder="'-'를 제외한 숫자만 입력" required/>
          </div>
        </div>

        <!-- 연락처 -->
        <div class="row">
          <div class="col-md-3"><label for="fromChargeTel">담당자 연락처</label></div>
          <div class="col-md-5">
            <input type="text" class="form-control" value="<?php echo $cInfo['fromChargeTel'] ?>"  onkeyup="isNumber(event)"
              id="fromChargeTel" name="fromChargeTel" placeholder="'-'를 제외한 숫자만 입력" />
          </div>
        </div>

        <!-- 부서 -->
        <div class="row">
          <div class="col-md-3"><label for="fromChargeDepartment">담당자 부서</label></div>
          <div class="col-md-5">
            <input type="text" class="form-control" value="<?php echo $cInfo['fromChargeDepartment'] ?>" 
              id="fromChargeDepartment" name="fromChargeDepartment" placeholder="입력" />
          </div>
        </div>

        <!-- 이메일 -->
        <div class="row">
          <div class="col-md-3"><label for="fromChargeEmail">담당자 이메일 <span class="necessary">*</span></label></div>
          <div class="col-md-5">
            <input type="text" class="form-control" value="<?php echo $cInfo['fromChargeEmail'] ?>"
              id="fromChargeEmail" name="fromChargeEmail" placeholder="입력" required/>
          </div>
        </div>

        <!-- 이메일 -->
        <div class="row">
          <div class="col-md-3"><label for="fromChargeEmail">SMS 전송여부 <span class="necessary">*</span></label></div>
          <div class="col-md-5">
              <?php
                $smsY_Checked = "checked";
                $smsN_Checked = "";
                if($cInfo['billSmsYN'] == "N"){
                  $smsY_Checked = "";
                  $smsN_Checked = "checked";
                }
              ?>
              <label class="radio-inline"><input type="radio" name="billSmsYN" value="Y" <?php echo $smsY_Checked ?>>Y</label>
              <label class="radio-inline"><input type="radio" name="billSmsYN"  value="N" <?php echo $smsN_Checked ?>>N</label>
          </div>
        </div>
      </div>
  </div>

 
  <div class="container-fluid mb20r policy-footer">
    <div class="row">
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
<script src="https://t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>