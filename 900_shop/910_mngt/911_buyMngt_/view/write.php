<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/headerpopup.html";

  

?>
<style>
   
</style>
<!-- modal #add-new -->  
<div class="modal-content">
    <form method="post" enctype='multipart/form-data' name="editForm" id="editForm">
        <input type="hidden" class="form-control" id="REQ_MODE" name="REQ_MODE">

        <!-- modal 상단 -->
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button> 
            <h4 class="modal-title">거래처 관리</h4>  
        </div>

        <!-- modal 내용 -->
        <div class="modal-body">  
            <div class="modal-top">
                <div class="modal-top-left">
                    <!-- <p>사무실</p> -->
                    <div class="modal-unit">
                        <div class="modal-label"><label for="dealCd">거래구분</label></div>
                        <div class="modal-select" style="width:400px !important;">
                            <!-- <div class="col-md-12"> -->
                                <!-- <select class="form-control" name="dealCd" id="dealCd">
                                    <?php echo setOption($optionsArray['data']['dealTypeOption'], $buyInfo['dealID']) ?>
                                </select> -->
                                <label class="checkbox-inline" for="DEAL_SALES"><input type="checkbox" name="dealCd" id="DEAL_SALES" value="sales">매출</label>
                                <label class="checkbox-inline" for="DEAL_BUY"><input type="checkbox" name="dealCd" id="DEAL_BUY" value="buy">매입(사입)</label>
                                <label class="checkbox-inline" for="DEAL_CONSIGNMENT"><input type="checkbox" name="dealCd" id="DEAL_CONSIGNMENT" value="consigment">매입(위탁)</label>
                                <label class="checkbox-inline" for="DEAL_ETC"><input type="checkbox" name="dealCd" id="DEAL_ETC" value="etc">매입(기타)</label>
                            <!-- </div> -->
                        </div>
                    </div>
                    <div class="modal-unit">
                        <div class="modal-label"><label for="GW_NUM">사업자번호</label></div>
                        <div class="modal-input"><input type="text" class="form-control" value="<?php echo bizNoFormat($buyInfo['businessNum']) ?>" id="GW_NUM" name="GW_NUM" placeholder="입력"></div>
                    </div>
                    <div class="modal-unit">
                        <div class="modal-label"><label for="GW_NM">상호</label></div>
                        <div class="modal-input"><input type="text" class="form-control" value="<?php echo $buyInfo['company'] ?>"  id="GW_NM" name="GW_NM" placeholder="입력"></div>
                    </div>
                    <div class="modal-unit">
                        <div class="modal-label"><label for="GW_TYPE">업태</label></div>
                        <div class="modal-input"><input type="text" class="form-control"  value="<?php echo $buyInfo['store_business'] ?>" id="GW_TYPE" name="GW_TYPE" placeholder="입력"></div>
                    </div>
                    <div class="modal-unit">
                        <div class="modal-label"><label for="GW_EVENT">종목</label></div>
                        <div class="modal-input"><input type="text" class="form-control" value="<?php echo $buyInfo['store_event'] ?>" id="GW_EVENT" name="GW_EVENT" placeholder="입력"></div>
                    </div>
                    <div class="modal-unit">
                        <div class="modal-label"><label for="GW_TAXTION">과세구분</label></div>
                        <div class="modal-select">
                            <!-- <select class="form-control" name="GW_TAXTION" id="GW_TAXTION">
                                <?php echo setOption($optionsArray['data']['taxTypeOption'], $buyInfo['taxID']) ?>
                            </select> -->
                            <label class="radio-inline" for="TAX_SIMPLE"><input type="radio" name="gwTaxtion" id="TAX_SIMPLE" value="simple">간이</label>
                            <label class="radio-inline" for="TAX_NORMAL"><input type="radio" name="gwTaxtion" id="TAX_NORMAL" value="normal">일반</label>
                        </div>
                    </div>
                    <div class="modal-unit">
                        <div class="modal-label"><label for="GW_CEONM">대표자명</label></div>
                        <div class="modal-input"><input type="text" class="form-control" value="<?php echo $buyInfo['ceoNm'] ?>" id="GW_CEONM" name="GW_CEONM" placeholder="입력"></div>
                    </div>
                    <div class="modal-unit">
                        <div class="modal-label"><label for="GW_CEO_PHONE">대표자휴대폰</label></div>
                        <div class="modal-input"><input type="text" class="form-control" value="<?php echo setPhoneFormat($buyInfo['phoneNum']) ?>"id="GW_CEO_PHONE" name="GW_CEO_PHONE" placeholder="입력"></div>
                    </div>
                    <div class="modal-unit">
                        <div class="modal-label"><label for="GW_ADDRESS">주소</label></div>
                        <div class="modal-select frmaddress">
                            <label for="BIZ_BEFORE_ADDRESS" class="sr-only">주소</label>
                            <input type="text" class="form-control" name="BIZ_ZONE_CODE" id="BIZ_ZONE_CODE" placeholder="우편번호" readonly>                            
                            <button type="button" class="btn btn-default" onclick="execPostCode()">주소검색</button>
                            <input type="text" class="form-control" name="BIZ_ADDRESS_1" id="BIZ_ADDRESS_1" placeholder="주소" readonly>                            
                            <input type="text" class="form-control" name="BIZ_ADDRESS_2" id="BIZ_ADDRESS_2" placeholder="주소 상세입력">                            
                        </div>
                    </div>
                    <div class="modal-unit">
                        <div class="modal-label"><label for="GW_PHONE">대표번호</label></div>
                        <div class="modal-input"><input type="text" class="form-control" id="GW_PHONE" name="GW_PHONE" placeholder="입력"></div>
                    </div>
                    <div class="modal-unit">
                        <div class="modal-label"><label for="GW_FAX">팩스번호</label></div>
                        <div class="modal-input"><input type="text" class="form-control" id="GW_FAX" name="GW_FAX" placeholder="입력"></div>
                    </div>
                </div>

                <div class="modal-top-right">
                    <!-- <p>대표자</p> -->
                    <div class="modal-unit">
                        <div class="modal-label"><label for="GW_FAO_NM">담당자명</label></div>
                        <div class="modal-input"><input type="text" class="form-control" id="GW_FAO_NM" name="GW_FAO_NM" placeholder="입력"></div>
                    </div>
                    <div class="modal-unit">
                        <div class="modal-label"><label for="GW_FAO_PHONE">담당자<br>휴대폰</label></div>
                        <div class="modal-input"><input type="text" class="form-control" id="GW_FAO_PHONE" name="GW_FAO_PHONE" placeholder="입력"></div>
                    </div>
                    <div class="modal-unit">
                        <div class="modal-label"><label for="GW_FAO_TEL">담당자<br>직통전화</label></div>
                        <div class="modal-input"><input type="text" class="form-control" id="GW_FAO_TEL" name="GW_FAO_TEL" placeholder="입력"></div>
                    </div>
                    <div class="modal-unit">
                        <div class="modal-label"><label for="GW_FAO_EMAIL">담당자<br>이메일</label></div>
                        <div class="modal-input"><input type="text" class="form-control" id="GW_FAO_EMAIL" name="GW_FAO_EMAIL" placeholder="입력"></div>
                    </div>
                    <div class="modal-unit">
                        <div class="modal-label"><label for="GW_SITEURL">사이트URL</label></div>
                        <div class="modal-input"><input type="text" class="form-control" id="GW_GOODS" name="GW_GOODS" placeholder="입력"></div>
                    </div>
                    <div class="modal-unit">
                        <div class="modal-label"><label for="GW_GOODS">주요품목</label></div>
                        <div class="modal-input"><input type="text" class="form-control" id="GW_GOODS" name="GW_GOODS" placeholder="입력"></div>
                    </div>
                    <div class="modal-unit">
                        <div class="modal-label"><label for="GW_MEMO">메모</label></div>
                        <div class="modal-input"><input type="text" class="form-control" id="GW_MEMO" name="GW_MEMO" placeholder="입력"></div>
                    </div>
                    <div class="modal-unit">
                        <div class="modal-label"><label for="DEAL_STATE">거래상태</label></div>
                        <div class="modal-select">
                            <div class="row">
                                <div class="col-md-12">
                                    <select class="form-control" name="DEAL_STATE" id="DEAL_STATE">
                                         <?php echo setOption($optionsArray['data']['dealStatusOption'], $buyInfo['dealStatusID']) ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-unit">
                        <div class="modal-label"><label for="GW_PAYMENT_DT">결제기일</label></div>
                        <div class="modal-input">
                            <select class="form-control" name="GW_PAYMENT_DT" id="GW_PAYMENT_DT">
                                <option value="" <?php if($buyInfo['paymentDT'] == "") echo "selected" ?>>선택</option>
                                <option value="5" <?php if($buyInfo['paymentDT'] == "5") echo "selected" ?>>5일</option>
                                <option value="10" <?php if($buyInfo['paymentDT'] == "10") echo "selected" ?>>10일</option>
                                <option value="15" <?php if($buyInfo['paymentDT'] == "15") echo "selected" ?>>15일</option>
                                <option value="20" <?php if($buyInfo['paymentDT'] == "20") echo "selected" ?>>20일</option>
                                <option value="25" <?php if($buyInfo['paymentDT'] == "25") echo "selected" ?>>25일</option>
                                <option value="30" <?php if($buyInfo['paymentDT'] == "30") echo "selected" ?>>30일</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-unit">
                        <div class="modal-label"><label for="GW_BANK_NM">거래은행</label></div>
                        <div class="modal-input"><input type="text" class="form-control" id="GW_BANK_NM" name="GW_BANK_NM" placeholder="입력"></div>
                    </div>
                    <div class="modal-unit">
                        <div class="modal-label"><label for="GW_BANK_NUM">계좌번호</label></div>
                        <div class="modal-input"><input type="text" class="form-control" id="GW_BANK_NUM" name="GW_BANK_NUM" placeholder="입력"></div>
                    </div>
                    <div class="modal-unit">
                        <div class="modal-label"><label for="GW_BANK_USER_NM">계좌주명</label></div>
                        <div class="modal-input"><input type="text" class="form-control" id="GW_BANK_USER_NM" name="GW_BANK_USER_NM" placeholder="입력"></div>
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
                                        if(empty($buyInfo['businessFileName'])){
                                            $displayText = "style='display:none'";
                                        }else{
                                            $fileName = $buyInfo['businessFileName'];
                                        }
                                    ?>
                                        <div class="fileContainer">
                                            <div class="fileSelectText"  id="GW_FILE_NM_SELECT_TEXT"><?php echo $fileName ?></div>
                                            <button type='button' id="GW_FILE_NM_DELETE_BTN" class='itemDelBtn' onclick='delUploadItem("GW_FILE_NM")'
                                                 <?php echo $displayText ?>>&times;</button>
                                        </div>
                                        <input id="GW_FILE_NM" name="GW_FILE_NM" type="file" class="form-control input-md">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-unit">
                        <div class="modal-label"><label for="AUTO_ORDER_YN">자동발주여부</label></div>
                        <div class="modal-select">
                            <label class="radio-inline" for="AUTO_ORDER_Y"><input type="radio" name="AUTO_ORDER_YN" id="AUTO_ORDER_Y" value="AUTO_ORDER_Y">자동</label>
                            <label class="radio-inline" for="AUTO_ORDER_N"><input type="radio" name="AUTO_ORDER_YN" id="AUTO_ORDER_N" value="AUTO_ORDER_N">수동</label>
                        </div>
                    </div>
                    <div class="modal-unit">
                        <div class="modal-label"><label for="GW_ID">아이디</label></div>
                        <div class="modal-input"><input type="text" class="form-control" id="GW_ID" name="GW_ID" placeholder="입력"></div>
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
                                        if(empty($buyInfo['bankFileName'])){
                                            $displayText = "style='display:none'";
                                        }else{
                                            $fileName = $buyInfo['bankFileName'];
                                        }
                                    ?>
                                        <div class="fileContainer">
                                            <div class="fileSelectText" id="BANK_FILE_NM_SELECT_TEXT"><?php echo $fileName ?></div>
                                            <button type='button' id="BANK_FILE_NM_DELETE_BTN" class='itemDelBtn' onclick='delUploadItem("BANK_FILE_NM")'
                                                 <?php echo $displayText ?>>&times;</button>
                                        </div>
                                        <input id="BANK_FILE_NM" name="BANK_FILE_NM" type="file" class="form-control input-md">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-unit">
                        <div class="modal-label"><label for="AUTO_ORDER_WAY">자동발주방법</label></div>
                        <div class="modal-select">
                            <label class="radio-inline" for="AUTO_ORDER_LMS"><input type="radio" name="AUTO_ORDER_WAY" id="AUTO_ORDER_LMS" value="AUTO_ORDER_LMS">LMS</label>
                            <label class="radio-inline" for="AUTO_ORDER_FAX"><input type="radio" name="AUTO_ORDER_WAY" id="AUTO_ORDER_FAX" value="AUTO_ORDER_FAX">FAX</label>
                        </div>
                    </div>
                    <div class="modal-unit">
                        <div class="modal-label"><label for="GW_RATING">등급</label></div>
                        <div class="modal-select">
                            <div class="row">
                                <div class="col-md-12">
                                    <select class="form-control" name="GW_RATING" id="GW_RATING">
                                        <?php echo setOption($optionsArray['data']['grageOption'], $buyInfo['gradeID']) ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- modal 하단 -->
        <div class="modal-footer">
            <div class="text-left">
                <?php if(!empty($partnerID)) : ?>
                     <button type="button" class="btn btn-danger" id="btnDelete" onclick="confirmDelete()">삭제</button>
                <?php endif; ?>
                <button type="button" class="btn btn-default" id="btnPwReset">비밀번호 초기화</button>
            </div>
            <div class="text-right">
            <!-- 등록btn -->
                <?php if(empty($partnerID)) : ?>
                    <button type="button" onclick="setData(<?php echo CASE_CREATE ?>)" id="submitBtn" class="btn btn-primary">등록</button>
                <?php else : ?>
                    <button type="button" onclick="setData(<?php echo CASE_UPDATE ?>)" id="submitBtn" class="btn btn-primary">수정</button>
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
</script>
