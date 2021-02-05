<div class="modal-content">
    <form method="post" enctype='multipart/form-data' name="editForm" id="editForm">
        <input type="hidden" class="form-control" id="PARTNER_ID" name="PARTNER_ID" value="<?php echo $partnerID ?>">

        <!-- modal 상단 -->
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button> 
            <h4 class="modal-title">거래처 관리</h4>  
        </div>

        <!-- modal 내용 -->
        <div class="modal-body">  
            <div class="modal-top">
                <div class="modal-top-left">
                    <div class="modal-unit">
                        <div class="modal-label"><label for="GW_TAXTION">업체구분 <span class="necessary">*</span></label></div>
                        <div class="modal-select">
                            <?php echo getRadio($this->optionArray['EC_BIZ_TYPE'], "bizTypeCode", $buyInfo['bizTypeCode']) ?>
                        </div>
                    </div>
                    <div class="modal-unit">
                        <div class="modal-label"><label for="dealCd">거래구분 <span class="necessary">*</span></label></div>
                        <div class="modal-select">
                            <!-- <div class="col-md-12"> -->
                                <!-- <select class="form-control" name="dealCd" id="dealCd">
                                </select> -->
                                <?php
                                    foreach($this->optionArray['EC_PARTNER_TYPE'] as $key => $value){
                                        $partnerTypeChecked = "";
                                        if($value['code'] == $buyInfo['partnerTypeCode']){
                                            $partnerTypeChecked = "checked";
                                        }
                                ?>
                                    <label class="radio-inline" for="EC_PARTNER_TYPE_<?php echo $key?>">
                                        <input type="radio" name="dealCd" required <?php echo $partnerTypeChecked ?>
                                            id="EC_PARTNER_TYPE_<?php echo $key?>" value="<?php echo $value['code']?>"/>
                                        <?php echo $value['domain']?>
                                    </label>
                                <?php   
                                    }
                                ?>
                                (
                                <?php 
                                    $partnerBuyerTypeDisabled = empty($buyInfo['partnerBuyerTypeCode']) ? "disabled" : "";
                                    foreach($this->optionArray['EC_BUYER_TYPE'] as $key => $value){
                                        $partnerBuyerTypeChecked = "";
                                        if($buyInfo['partnerBuyerTypeCode'] == $value['code']){
                                            $partnerBuyerTypeChecked = "checked";
                                        }
                                ?>
                                    <label class="radio-inline" for="EC_BUYER_TYPE_<?php echo $key?>">
                                        <input type="radio" name="EC_BUYER_TYPE" <?php echo $partnerBuyerTypeDisabled  ?> <?php echo $partnerBuyerTypeChecked  ?>
                                            id="EC_BUYER_TYPE_<?php echo $key?>" value="<?php echo $value['code']?>" required/><?php echo $value['domain']?>
                                    </label>
                                <?php   
                                    }
                                ?>)
                                
                            <!-- </div> -->
                        </div>
                    </div>
                    
                   <!-- 파트너아이디-->
                   <div class="modal-unit">
                        <div class="modal-label"><label for="itemID">파트너아이디  <span class="necessary">*</span></label></div>
                        <div class="modal-input frmaddress">
                            <?php
                                $readonlyText = "";
                                if(!empty($partnerID)){
                                    $readonlyText = "readonly";
                                }
                            ?>
                             <input type="text" class="form-control" value="<?php echo $buyInfo['partnerID'] ?>"  maxLength="10" minLength="10" 
                                    id="partnerID" name="partnerID" placeholder="10자 입력" <?php echo $readonlyText?> required/>
                            <?php if(empty($partnerID)) : ?>
                                <button type="button" class="btn btn-default" id="btnDuplicate" onclick="onCheckDuplicate()">중복검사</button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="modal-unit">
                        <div class="modal-label"><label for="GW_NUM">사업자번호 <span class="necessary">*</span></label></div>
                        <div class="modal-input">
                            <input type="text" onkeyup="isNumber(event)" class="form-control business" 
                                value="<?php echo bizNoFormat($buyInfo['companyRegID']) ?>" id="GW_NUM" name="GW_NUM" placeholder="'-' 제외한 숫자만 입력" required/>
                        </div>
                    </div>
                    <div class="modal-unit">
                        <div class="modal-label"><label for="GW_NM">상호 <span class="necessary">*</span></label></div>
                        <div class="modal-input">
                            <input type="text" class="form-control" value="<?php echo $buyInfo['companyName'] ?>"  id="GW_NM" name="GW_NM" placeholder="입력" required/>
                        </div>
                    </div>
                    <div class="modal-unit">
                        <div class="modal-label"><label for="GW_TYPE">업태</label></div>
                        <div class="modal-input"><input type="text" class="form-control"  value="<?php echo $buyInfo['companyBiz'] ?>" id="GW_TYPE" name="GW_TYPE" placeholder="입력"></div>
                    </div>
                    <div class="modal-unit">
                        <div class="modal-label"><label for="GW_EVENT">종목</label></div>
                        <div class="modal-input"><input type="text" class="form-control" value="<?php echo $buyInfo['companyPart'] ?>" id="GW_EVENT" name="GW_EVENT" placeholder="입력"></div>
                    </div>
                    <div class="modal-unit">
                        <div class="modal-label"><label for="GW_TAXTION">과세구분 <span class="necessary">*</span></label></div>
                        <div class="modal-select">
                            <?php echo getRadio($this->optionArray['EC_USER_TAX_TYPE'], "gwTaxtion", $buyInfo['bizTaxTypeCode']) ?>
                        </div>
                    </div>
                    <div class="modal-unit">
                        <div class="modal-label"><label for="GW_CEONM">대표자명 <span class="necessary">*</span></label></div>
                        <div class="modal-input">
                            <input type="text" class="form-control" value="<?php echo $buyInfo['ceoName'] ?>" 
                                id="GW_CEONM" name="GW_CEONM" placeholder="입력" required/>
                        </div>
                    </div>
                    <div class="modal-unit">
                        <div class="modal-label"><label for="GW_CEO_PHONE">대표자휴대폰</label></div>
                        <div class="modal-input"><input type="text" class="form-control phone" onkeyup="isNumber(event)"  value="<?php echo $buyInfo['ceoPhoneNum'] ?>"id="GW_CEO_PHONE" name="GW_CEO_PHONE" placeholder="'-' 제외한 숫자만 입력"></div>
                    </div>
                    <div class="modal-unit">
                        <div class="modal-label"><label for="GW_ADDRESS">주소</label></div>
                        <div class="modal-input frmaddress">
                            <label for="BIZ_BEFORE_ADDRESS" class="sr-only">주소</label>
                            <input type="text" class="form-control" name="BIZ_ZONE_CODE" id="BIZ_ZONE_CODE" placeholder="우편번호" 
                                value="<?php echo $buyInfo['zipCode'] ?>" readonly>                            
                            <button type="button" class="btn btn-default" onclick="execPostCode()">주소검색</button>
                            <input type="text" class="form-control" name="BIZ_ADDRESS_1" id="BIZ_ADDRESS_1" 
                                 value="<?php echo $buyInfo['Address1'] ?>"placeholder="주소" readonly>                            
                            <input type="text" class="form-control" name="BIZ_ADDRESS_2" id="BIZ_ADDRESS_2" 
                                value="<?php echo $buyInfo['Address2'] ?>"placeholder="주소 상세입력">                            
                        </div>
                    </div>
                    <div class="modal-unit">
                        <div class="modal-label"><label for="GW_PHONE">대표번호 <span class="necessary">*</span></label></div>
                        <div class="modal-input">
                            <input type="text" class="form-control phone" id="GW_PHONE" name="GW_PHONE" required
                             onkeyup="isNumber(event)" value="<?php echo $buyInfo['phoneNum'] ?>" placeholder="'-' 제외한 숫자만 입력"/>
                        </div>
                    </div>
                    <div class="modal-unit">
                        <div class="modal-label"><label for="GW_FAX">팩스번호</label></div>
                        <div class="modal-input">
                            <input type="text" class="form-control phone" id="GW_FAX" name="GW_FAX" 
                                 onkeyup="isNumber(event)" value="<?php echo $buyInfo['faxNum'] ?>" placeholder="'-' 제외한 숫자만 입력">
                        </div>
                    </div>
                </div>

                <div class="modal-top-right">
                    <!-- <p>대표자</p> -->
                    <div class="modal-unit">
                        <div class="modal-label"><label for="GW_FAO_NM">담당자명 <span class="necessary">*</span></label></div>
                        <div class="modal-input">
                            <input type="text" class="form-control" id="GW_FAO_NM" name="GW_FAO_NM" 
                                value="<?php echo $buyInfo['chargeName'] ?>" placeholder="입력" required/>
                        </div>
                    </div>
                    <div class="modal-unit">
                        <div class="modal-label"><label for="GW_FAO_PHONE">담당자<br>휴대폰 <span class="necessary">*</span></label></div>
                        <div class="modal-input">
                            <input type="text" class="form-control phone" id="GW_FAO_PHONE" name="GW_FAO_PHONE" 
                                onkeyup="isNumber(event)" value="<?php echo $buyInfo['chargePhoneNum'] ?>" placeholder="'-' 제외한 숫자만 입력" required/>
                        </div>
                    </div>
                    <div class="modal-unit">
                        <div class="modal-label"><label for="GW_FAO_TEL">담당자<br>직통전화 <span class="necessary">*</span></label></div>
                        <div class="modal-input">
                            <input type="text" class="form-control phone" id="GW_FAO_TEL" name="GW_FAO_TEL" 
                                onkeyup="isNumber(event)" value="<?php echo $buyInfo['chargeDirectPhoneNum'] ?>" placeholder="'-' 제외한 숫자만 입력" required/>
                        </div>
                    </div>
                    <div class="modal-unit">
                        <div class="modal-label"><label for="GW_FAO_EMAIL">담당자<br>이메일 <span class="necessary">*</span></label></div>
                        <div class="modal-input">
                            <input type="text" class="form-control" id="GW_FAO_EMAIL" name="GW_FAO_EMAIL" 
                              value="<?php echo $buyInfo['chargeEmail'] ?>" placeholder="id@email.com" required/>
                        </div>
                    </div>
                    <div class="modal-unit">
                        <div class="modal-label"><label for="GW_SITEURL">사이트URL</label></div>
                        <div class="modal-input">
                            <input type="text" class="form-control" id="GW_HOMEPAGE" name="GW_HOMEPAGE" 
                                value="<?php echo $buyInfo['homepage'] ?>" placeholder="ex) https://www.sitename.com">
                        </div>
                    </div>
                    <div class="modal-unit">
                        <div class="modal-label"><label for="GW_GOODS">주요품목</label></div>
                        <div class="modal-input">
                            <input type="text" class="form-control" id="GW_GOODS" name="GW_GOODS" 
                                 value="<?php echo $buyInfo['mainGoods'] ?>" placeholder="입력">
                        </div>
                    </div>
                    <div class="modal-unit">
                        <div class="modal-label"><label for="GW_MEMO">메모</label></div>
                        <div class="modal-input">
                            <input type="text" class="form-control" id="GW_MEMO" name="GW_MEMO" 
                                value="<?php echo $buyInfo['memo'] ?>"  placeholder="입력">
                        </div>
                    </div>
                    <div class="modal-unit">
                        <div class="modal-label"><label for="DEAL_STATE">거래상태 <span class="necessary">*</span></label></div>
                        <div class="modal-input">
                            <div class="row">
                                <div class="col-md-12">
                                    <select class="form-control" name="DEAL_STATE" id="DEAL_STATE" required>
                                         <?php echo getOption($this->optionArray['EC_USER_STATUS'], "" , $buyInfo['contractStatusCode']) ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-unit integrated-setting">
                        <div class="modal-label"><label for="GW_PAYMENT_DT">결제기일</label></div>
                        <div class="modal-input">
                            <input type="text" class="form-control" id="GW_PAYMENT_DT" name="GW_PAYMENT_DT" style="width:40px"
                                onchange="onchangePaymentText()" onkeyup="isNumber(event)" value="<?php echo $buyInfo['paymentDueDate'] ?>"/>
                            <span>일</span>
                        </div>
                    </div>
                    <div class="modal-unit">
                        <div class="modal-label"><label for="GW_BANK_NM">거래은행</label></div>
                        <div class="modal-input">
                            <select class="form-control" id="GW_BANK_NM" name="GW_BANK_NM" >
                                <?php echo getOption($this->optionArray['BANK_CD'], "" , $buyInfo['bankCode']) ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-unit">
                        <div class="modal-label"><label for="GW_BANK_NUM">계좌번호</label></div>
                        <div class="modal-input">
                            <input type="text" class="form-control" id="GW_BANK_NUM" name="GW_BANK_NUM" 
                                 onkeyup="isNumber(event)" value="<?php echo $buyInfo['bankNum'] ?>" placeholder="'-' 제외한 숫자만 입력">
                        </div>
                    </div>
                    <div class="modal-unit">
                        <div class="modal-label"><label for="GW_BANK_USER_NM">계좌주명</label></div>
                        <div class="modal-input">
                            <input type="text" class="form-control" id="GW_BANK_USER_NM" name="GW_BANK_USER_NM"
                                 value="<?php echo $buyInfo['bankOwnName'] ?>" placeholder="입력">
                        </div>
                    </div>

                    <?php
                        $isNecessaryText = "style='display:none'";
                        $isRequiredText = "";
                        if($buyInfo['partnerBuyerTypeCode'] == EC_BUYER_TYPE_CONSIGN){
                            $isNecessaryText = "";
                            $isRequiredText  = "required";
                        }
                    ?>
                    
                    <div class="modal-unit integrated-setting">
                        <div class="modal-label"><label for="deliveryFee">기본배송비 <span class="necessary deliveryFeeNecessary" <?php echo  $isNecessaryText?>>*</span></label></div>
                        <div class="modal-input">
                            <input type="text" class="form-control text-right deliveryFee" id="deliveryFee" name="deliveryFee" value="<?php echo $buyInfo['deliveryFee']?>"
                                 onkeyup="isNumber(event)" placeholder="숫자만 입력" <?php echo  $isRequiredText?> />
                            <span>원</span>
                        </div>
                    </div>
                    <div class="modal-unit integrated-setting">
                        <div class="modal-label"><label for="deliveryFeeFree">무료배송 기준 <span class="necessary deliveryFeeNecessary" <?php echo  $isNecessaryText?>>*</span></label></div>
                        <div class="modal-input">
                            <input type="text" class="form-control text-right deliveryFee" id="deliveryFeeFree" name="deliveryFeeFree" value="<?php echo $buyInfo['deliveryFeeFree']  ?>"
                                onkeyup="isNumber(event)"   placeholder="숫자만 입력" <?php echo  $isRequiredText?> />
                            <span>원 이상 무료배송</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-top modal-bottom">
                <div class="modal-top-left">
                    <div class="modal-unit">
                        <div class="modal-label"><label for="GW_FILE_NM">사업자등록증<br>첨부</label></div>
                        <div class="modal-select">
                            <div class="row">
                                <div class="col-md-12" style="display:flex; flex-direction:row">
                                    <button type="button" id="businessBtn" class="btn btn-default" data-dismiss="modal" onclick="onClickFileBtn('GW_FILE_NM')">파일선택</button>
                                    <?php 
                                        $displayText = "";
                                        $fileName = NOT_SELECT_FILE_TEXT;
                                        if(empty($buyInfo['companyCertOriginFilename'])){
                                            $displayText = "style='display:none'";
                                        }else{
                                            $fileName = $buyInfo['companyCertOriginFilename'];
                                        }
                                    ?>
                                        <div class="fileContainer">
                                            <div class="fileSelectText"  id="GW_FILE_NM_SELECT_TEXT"><?php echo $fileName ?></div>
                                            <button type='button' id="GW_FILE_NM_DELETE_BTN" class='itemDelBtn' onclick='delUploadItem("GW_FILE_NM")'
                                                 <?php echo $displayText ?>>&times;</button>
                                        </div>
                                        <input type="hidden" name="GW_FILE_NM_URI_ORI" id="GW_FILE_NM_URI_ORI" 
                                                  value="<?php echo $buyInfo['companyCertURI'] ?>"/>
                                        <input type="hidden" name="GW_FILE_NM_ORI" id="GW_FILE_NM_ORI" 
                                                  value="<?php echo $buyInfo['companyCertOriginFilename'] ?>"/>
                                        <input type="hidden" name="GW_FILE_NM_PHYSICAL_ORI" id="GW_FILE_NM_PHYSICAL_ORI" 
                                                  value="<?php echo $buyInfo['companyCertPhysicalFilename'] ?>"/>
                                        <input id="GW_FILE_NM" name="GW_FILE_NM" type="file" class="form-control input-md">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-unit">
                        <div class="modal-label"><label for="AUTO_ORDER_YN">자동발주여부</label></div>
                        <div class="modal-select">
                            <?php echo getRadio(array(
                               array(
                                    "code"=>"Y",
                                    "domain"=>"자동"
                                ),
                                array(
                                    "code"=>"N",
                                    "domain"=>"수동"
                                ),
                            ), "AUTO_ORDER_YN", $buyInfo['autoOrderYN']) ?>
                        </div>
                    </div>
                    <div class="modal-unit">
                        <div class="modal-label"><label for="GW_ID">아이디 <span class="necessary">*</span></label></div>
                        <div class="modal-input frmaddress">
                            <input type="text" class="form-control"  value="<?php echo $buyInfo['partnerLoginID'] ?>" id="GW_ID" name="GW_ID" placeholder="입력" required/>
                            <button type="button" class="btn btn-default" id="btnGwIdDuplicate" onclick="onCheckLoginDuplicate()">중복검사</button>
                        </div>
                    </div>
                    <div class="modal-unit">
                        <div class="modal-label"><label for="partnerPasswd">비밀번호
                            <?php 
                                $requireText = "";  
                                $displayText = "style='display:none'";
                                if(empty($partnerID)){
                                    $requireText = "required"; 
                                    $displayText = "";
                                }
                            ?>
                                <span class="necessary required" <?=$displayText?>>*</span>    
                           </label>
                        </div>
                        <div class="modal-input">
                            <input type="password" class="form-control pwdRequired" id="partnerPasswd" name="partnerPasswd" onkeyup="onchangePassword()"
                                 value="<?php echo $buyInfo['partnerPasswd'] ?>" placeholder="변경하실 비밀번호 입력" <?php echo $requireText ?> />
                        </div>
                        
                    </div>
                   
                    <div class="modal-unit" id="confirmPasswordContainer" <?=$displayText?>>
                        <div class="modal-label"><label for="partnerPasswdConfirm">비밀번호확인 
                            <?php 
                                $requireText = "";  
                                if(empty($partnerID)){
                                    $requireText = "required"; 
                                }?>
                                <span class="necessary required" <?=$displayText?>>*</span> 
                           </label>
                        </div>
                        <div class="modal-input">
                            <input type="password" class="form-control pwdRequired" id="partnerPasswdConfirm" name="partnerPasswdConfirm" 
                                 value="<?php echo $buyInfo['partnerPasswd'] ?>" placeholder="입력" <?php echo $requireText ?> />
                        </div>
                    </div>
                </div>
                <div class="modal-top-right">
                    <div class="modal-unit">
                        <div class="modal-label"><label for="BANK_FILE_NM">통장사본첨부</label></div>
                        <div class="modal-select">
                            <div class="row">
                                <div class="col-md-12" style="display:flex; flex-direction:row">
                                    <button type="button" id="businessBtn" class="btn btn-default" data-dismiss="modal" onclick="onClickFileBtn('BANK_FILE_NM')">파일선택</button>
                                    <?php 
                                        $displayText = "";
                                        $fileName = NOT_SELECT_FILE_TEXT;
                                        
                                        if(empty($buyInfo['companyBankOriginFilename'])){
                                            $displayText = "style='display:none'";
                                        }else{
                                            $fileName = $buyInfo['companyBankOriginFilename'];
                                        }
                                    ?>
                                        <div class="fileContainer">
                                            <div class="fileSelectText" id="BANK_FILE_NM_SELECT_TEXT"><?php echo $fileName ?></div>
                                            <button type='button' id="BANK_FILE_NM_DELETE_BTN" class='itemDelBtn' onclick='delUploadItem("BANK_FILE_NM")'
                                                 <?php echo $displayText ?>>&times;</button>
                                        </div>
                                        <input type="hidden" name="BANK_FILE_NM_URI_ORI" id="BANK_FILE_NM_URI_ORI" 
                                                  value="<?php echo $buyInfo['companyBankURI'] ?>"/>
                                        <input type="hidden" name="BANK_FILE_NM_ORI" id="BANK_FILE_NM_ORI" 
                                                  value="<?php echo $buyInfo['companyBankOriginFilename'] ?>"/>
                                        <input type="hidden" name="BANK_FILE_NM_PHYSICAL_ORI" id="BANK_FILE_NM_PHYSICAL_ORI" 
                                                  value="<?php echo $buyInfo['companyBankPhysicalFilename'] ?>"/>
                                        <input id="BANK_FILE_NM" name="BANK_FILE_NM" type="file" class="form-control input-md">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-unit">
                        <div class="modal-label"><label for="AUTO_ORDER_WAY">자동발주방법</label></div>
                        <div class="modal-select">
                            <?php echo getRadio($this->optionArray['EC_AUTO_ORDER_METHOD'], "AUTO_ORDER_WAY", $buyInfo['autoOrderMethodCode']) ?>
                        </div>
                    </div>
                    <!-- <div class="modal-unit">
                        <div class="modal-label"><label for="GW_RATING">등급 <span class="necessary">*</span></label></div>
                        <div class="modal-input">
                            <div class="row">
                                <div class="col-md-12">
                                    <select class="form-control" name="GW_RATING" id="GW_RATING" required>
                                        <?php
                                            $rankArray = $data->getRankOption();
                                            foreach($rankArray  as $key => $value){
                                               
                                                if($value['rankID'] == $buyInfo['partnerLevelCode']){
                                                    $rankSelectedText = "selected";
                                                }
                                        ?>
                                            <option value="<?php echo $value['rankID']?>" <?php echo $rankSelectedText ?>><?php echo $value['rankName']?></option>
                                        <?php
                                            }
                                        ?>
                                     </select>
                                </div>
                            </div>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>


        <!-- modal 하단 -->
        <div class="modal-footer">
            <div class="text-left">
                <?php if(!empty($partnerID)) : ?>
                     <button type="button" class="btn btn-danger" id="btnDelete" onclick="confirmDelete()">삭제</button>
                     <button type="button" class="btn btn-default" id="btnPwReset">비밀번호 초기화</button>
                <?php endif; ?>
            </div>
            <div class="text-right">
            <!-- 등록btn -->
                <?php if(empty($partnerID)) : ?>
                    <button type="submit"  id="submitBtn" class="btn btn-primary">등록</button>
                <?php else : ?>
                    <button type="submit"  id="submitBtn" class="btn btn-primary">수정</button>
                <?php endif; ?>
            <!-- 취소btn -->
            <button type="button" class="btn btn-default" data-dismiss="modal" onclick="onClose()">취소</button>
            </div>
        </div>

    </form>
</div>

<!-- footer.html -->
<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/footerpopup.html";
?>
<script src="https://t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script>
    let isCheckDuplicate = ($('#PARTNER_ID').val() != "") ? true : false;
    let isCheckLoginIdDuplicate = ($('#PARTNER_ID').val() != "") ? true : false;

    $('#editForm').submit(function(e){
        e.preventDefault();

        if(setDataValidation()){
            if($('#PARTNER_ID').val() != ""){
                confirmSwalTwoButton('수정 하시겠습니까?','확인', '취소', false ,()=>setData("<?php echo CASE_UPDATE ?>"));
            }else{
                confirmSwalTwoButton('저장 하시겠습니까?','확인', '취소', false ,()=>setData("<?php echo CASE_CREATE ?>"));
            }
        }
    });

    $('#btnPwReset').click(function(){
        if(setDataValidation(true)){
            confirmSwalTwoButton(
                '비밀번호를 초기화 하시겠습니까?','확인', '취소', false ,
                ()=>{
                    const resetValue = "<?php echo $buyInfo['companyRegID']?>";
                    $('#partnerPasswd').val(resetValue);
                    $('#partnerPasswdConfirm').val(resetValue);
                    
                    setData("<?php echo CASE_UPDATE ?>")
                }, '해당 사업자번호로 비밀번호가 초기화 됩니다.'
            );
        }
    });

    $('input[name=dealCd]').click(function(){
        const buyCode = "<?php echo EC_PARTNER_TYPE_BUY?>";
        if(buyCode != $(this).val() ){
            $('input[name=EC_BUYER_TYPE]').prop("checked", false);   
            $('input[name=EC_BUYER_TYPE]').prop("disabled", true);  
        }else{
            $('input[name=EC_BUYER_TYPE]').prop("disabled", false);   
        }
    });

    $('input[name=EC_BUYER_TYPE]').change(function(){
        if($(this).val() == EC_BUYER_TYPE_CONSIGN){
            $('.deliveryFeeNecessary').show();
            $('.deliveryFee').attr('required', true);
        }else{
            $('.deliveryFeeNecessary').hide();
            $('.deliveryFee').attr('required', false);
        }
    });

    function setDataValidation(isChangePassword){
        if(!isCheckDuplicate){
            alert("파트너아이디 중복검사를 해주세요.");
            $('#partnerID').focus();
            return false;
        }else if(!isCheckLoginIdDuplicate){
            alert("로그인아이디 중복검사를 해주세요.");
            $('#GW_ID').focus();
            return false;
        }else{
            if(empty(isChangePassword)){
                if($('#partnerPasswd').val() != $('#partnerPasswdConfirm').val() ){
                    alert("비밀번호가 일치하지 않습니다.");
                    if(!$('#partnerPasswd').val() && $('#partnerPasswdConfirm').val()){
                        $('#partnerPasswd').focus();
                    }else{
                        $('#partnerPasswdConfirm').focus();
                    }
                    return false;
                }else{
                    if($('.deliveryFeeNecessary').is(":visible")){
                        if(empty($('#deliveryFee').val())){
                            alert("기본배송비를 입력해주셔야 합니다.");
                            $('#deliveryFee').focus();
                            return false;
                        }else if(empty($('#deliveryFeeFree').val())){
                            alert("무료배송기준을 입력해주셔야 합니다.");
                            $('#deliveryFeeFree').focus();
                            return false;
                        }else {
                            return true;
                        }
                    }else{
                        return true;
                    }
                    
                }
            }else{
                $('#partnerPasswd').val(''); 
                onchangePassword();
                return true;
            }
          
        }
    }

    /** 삭제 */
    function confirmDelete(){
        confirmSwalTwoButton(CONFIRM_DELETE_MSG, DEL_MSG, CANCEL_MSG, true,()=>{
            delData("<?php echo $partnerID?>")
        }, "");
    }

    /** 취소 */
    function onClose(){
        window.close();
    }

    /** 첨부파일 change 이벤트 */
    $('input[type="file"]').change(function(e){
        const fileName = e.target.files[0].name;
        const id = e.target.id;

        $('#'+id+"_ORI").val(fileName);
        $('#'+id+'_SELECT_TEXT').text(fileName);
        if(!$('#'+id+'_DELETE_BTN').is(":visible")){
            $('#'+id+'_DELETE_BTN').show();
        }
    });

    /**
     * 첨부파일 삭제시
     * @param{objID : 삭제할 아이디}
     */
    function delUploadItem(objID){
        $('#'+objID).val("");
        $('#'+objID+"_ORI").val("")
        $('#'+objID+"_PHYSICAL_ORI").val("");

        $('#'+objID+'_DELETE_BTN').hide();
        $('#'+objID+'_SELECT_TEXT').text("<?php echo NOT_SELECT_FILE_TEXT ?>");
    }

    /**
     * 첨부파일 선택 시 hidden처리된 파일을 클릭 트리거
     * @param{objID : 삭제할 아이디}
     */
    function onClickFileBtn(objID){
        $('#'+objID).click();
    }

    /**
     * 주소검색
     * @param {objId : 구주소/신주소 고유 input 아이디(해당 값을 세팅해주기 위해.)}
     */
    execPostCode = (objId) => {
        new daum.Postcode({
            oncomplete: function(data) {
                console.log('postcode >> ',data);
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
            
                $('#BIZ_ZONE_CODE').val(data.zonecode);
                $('#BIZ_ADDRESS_1').val(fullAddr);
            }
        }).open();
    }

    function onCheckDuplicate(){
        if($('#partnerID').val().trim() != ""){
            const data = {
                REQ_MODE: CASE_CONFLICT_CHECK,
                reqMode : CHECK_EXIST_PARTNER_ID,
                partnerID : $('#partnerID').val()
            };
            callAjax(ERP_CONTROLLER_URL,'POST', data).then((result)=>{
                if(isJson(result)){
                    const res = JSON.parse(result);
                    alert(res.message);
                    if(res.status == OK){
                        isCheckDuplicate =  true;
                    }else{
                        isCheckDuplicate =  false;
                    }
                }else{
                    alert(ERROR_MSG_1200);
                    isCheckDuplicate =  false;
                }
            });     
        }else{
            alert("파트너 아이디를 입력해주세요.");
            $('#partnerID').focus();
        }  
    }

    function onCheckLoginDuplicate(){
        if($('#GW_ID').val().trim() != ""){
            const data = {
                REQ_MODE: CASE_CONFLICT_CHECK,
                reqMode : CHECK_EXIST_PARTNER_LOGIN_ID,
                partnerLoginID : $('#GW_ID').val()
            };
            callAjax(ERP_CONTROLLER_URL,'POST', data).then((result)=>{
                if(isJson(result)){
                    const res = JSON.parse(result);
                    alert(res.message);
                    if(res.status == OK){
                        isCheckLoginIdDuplicate =  true;
                    }else{
                        isCheckLoginIdDuplicate =  false;
                    }
                }else{
                    alert(ERROR_MSG_1200);
                    isCheckLoginIdDuplicate =  false;
                }
            });     
        }else{
            alert("아이디를 입력해주세요.");
            $('#GW_ID').focus();
        }  
    }
</script>
