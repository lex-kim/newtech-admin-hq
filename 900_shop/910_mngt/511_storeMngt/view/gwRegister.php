<style>
  input[type='file']{
    display:none
  }
  .fileContainer{
    display:flex;flex:1; overflow:hidden;
    align-self: center; flex-direction:row; margin-left:4px
  }
  .fileSelectText{
    flex:1 ; overflow:hidden
  }
  .itemDelBtn{
    width:20px; margin-left:8px
  }
</style>

<script src="https://t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>

<body>
  <div class="popup-dialog">
    <form method="post" name="garageForm" id="garageForm" enctype="multipart/form-data">
    <div class="container-fluid popup-content">
      
          <?php if(!empty($_REQUEST['garageID'])):?>
            <input type="hidden" id="garageID" name="garageID" value="<?=$_REQUEST['garageID']?>"/>
          <?php endif;?>
          
              <!-- 업체등록 업체영역 -->
              <div class="popup-top">
              
                <!-- popup 상단 -->
                <div class="popup-header">

                  <?php if(empty($_REQUEST['garageID'])) : ?>
                    <h3 class="popup-title">업체 등록</h3>
                    <?php else : ?>
                    <h3 class="popup-title">업체 수정</h3>
                    <?php endif; ?>	
                </div>

                  <!-- modal 내용 -->
                  <div class="modal-body register-css"> 
                    <div class="modal-body-left">
                       <!-- 구분 -->
                       <div class="row">
                        <div class="col-md-3"><label for="CSD_ID">구분 <span class="necessary">*</span></label> </div>
                        <div class="col-md-7">
                          <select class="form-control require" id="CSD_ID" name="CSD_ID" required>
                            <option value="">=구분=</option>
                            <?php
                              foreach($divisionArray as $idx => $item){
                                $selectedText = "";
                                if($item['divisionID'] == $garageArray['divisionID']){
                                  $selectedText = "selected";
                                }
                            ?>
                              <option value="<?php echo $item['divisionID']?>" <?=$selectedText?>><?php echo $item['divisionName']?></option>
                            <?php
                              }
                            ?>
                          </select>   
                        </div>
                      </div>

                      <!-- 업체아이디 -->
                      <div class="row">
                          <?php 
                            $diabledText = "";
                            $requiredText = "required";
                            if(!empty($_REQUEST['garageID'])){
                              $diabledText = "disabled";
                              $requiredText = "";
                            }
                          ?>
                        <div class="col-md-3"><label for="MGG_USERID">업체아이디 <span class="necessary"><?=(empty($requiredText) ? "&nbsp;" : "*")?></span></label></div>
                        <div class="col-md-7">
                          
                          <input type="text" class="form-control require" value="<?=$garageArray['loginID']?>" onchange="onChangeUserID()"
                            name="MGG_USERID" id="MGG_USERID" placeholder="업체아이디 입력" <?=$diabledText?> <?=$requiredText?>/>
                          
                          <?php if(empty($_REQUEST['garageID'])):?>
                            <span id="guideUserIdText">&nbsp;</span>
                          <?php endif;?>
                            
                        </div>
                      </div>

                      <!-- 사업자번호 -->
                      <div class="row">
                        <div class="col-md-3"> <label for="MGG_BIZ_ID">사업자번호 <span class="necessary">*</span></label></div>
                        <div class="col-md-7">
                          <input type="hidden" id="IS_EXIST_MGG_BIZ_ID" name="IS_EXIST_MGG_BIZ_ID" />   
                          <input type="text" class="form-control require" name="MGG_BIZ_ID" id="MGG_BIZ_ID"  value="<?=$garageArray['companyRegID']?>"
                                onkeyup="isNumber(event)" placeholder="사업자번호 -없이 입력" required maxlength="10">  
                        </div>
                      </div>


                      <?php 
                            $requiredText = "required";
                            $placeHolderText = "비밀번호 입력";
                            if(!empty($_REQUEST['garageID'])){
                              $requiredText = "";
                              $placeHolderText = "변경할 비밀번호 입력";
                            }
                          ?>
                      <!-- 비밀번호 -->
                      <div class="row">
                        <div class="col-md-3"> <label for="MGG_PASSWD_AES">비밀번호 <span class="necessary"><?=(empty($requiredText) ? "&nbsp;" : "*")?></span></label></div>
                        <div class="col-md-7">
                          <input type="password" class="form-control require"
                             name="MGG_PASSWD_AES" id="MGG_PASSWD_AES" placeholder="<?=$placeHolderText?>" <?=$requiredText?>/> 
                        </div>
                      </div>

                      <!-- 업체명 -->
                      <div class="row">
                        <div class="col-md-3"><label for="MGG_NM">업체명 <span class="necessary">*</span></label></div>
                        <div class="col-md-7">
                          <input type="text" class="form-control require" value="<?=$garageArray['garageName']?>"
                             name="MGG_NM" id="MGG_NM" placeholder="업체명 입력" required/>          
                        </div>
                      </div>

                      <!-- 신주소 -->
                      <div class="row">
                        <div class="col-md-3"><label for="MGG_ZIP_NEW">신주소 <span class="necessary">*</span></label></div>
                        <div class="col-md-7 frmaddress">
                          <input type="text" class="form-control require" value="<?=$garageArray['zipCode']?>"
                             name="MGG_ZIP_NEW" id="MGG_ZIP_NEW" placeholder="신주소" readonly required>     
                          <button type="button" class="btn btn-default addrBtn" onclick="execPostCode('NEW')">주소검색</button>                       
                          <input type="text" class="form-control" value="<?=$garageArray['address1']?>"
                            name="MGG_ADDRESS_NEW_1" id="MGG_ADDRESS_NEW_1" placeholder="신주소" readonly required/>                            
                          <input type="text" class="form-control"value="<?=$garageArray['address2']?>"
                             name="MGG_ADDRESS_NEW_2" id="MGG_ADDRESS_NEW_2" placeholder="신주소 상세입력">     
                        </div>
                      </div>

                      <!-- 사무실 전화 -->
                      <div class="row">
                        <div class="col-md-3"><label for="MGG_OFFICE_TEL">사무실 전화 <span class="necessary">*</span></label></div>
                        <div class="col-md-7 phone-area">
                          <input type="text" class="form-control phone" name="MGG_OFFICE_TEL" id="MGG_OFFICE_TEL" placeholder="사무실 전화 -없이 입력"
                            value="<?=$garageArray['phoneNum']?>"   onkeyup="isNumber(event)" required/>  
                        </div>
                      </div>
                            
                      <!-- 사무실 팩스 -->
                      <div class="row">
                        <div class="col-md-3"><label for="MGG_OFFICE_FAX">사무실 팩스</label></div>
                        <div class="col-md-7">
                          <input type="text" class="form-control phone" name="MGG_OFFICE_FAX" id="MGG_OFFICE_FAX" value="<?=$garageArray['faxNum']?>"
                             placeholder="사무실 팩스 -없이 입력" onkeyup="isNumber(event)" />    
                        </div>
                      </div>

                      <!-- 이메일 -->
                      <div class="row">
                        <div class="col-md-3"><label for="MGG_OFFICE_EMAIL">이메일 <span class="necessary">*</span></label></div>
                        <div class="col-md-7">
                          <input type="text" class="form-control phone" name="MGG_OFFICE_EMAIL" id="MGG_OFFICE_EMAIL" placeholder="이메일 입력"
                            value="<?=$garageArray['companyEmail']?>"  required/>   
                        </div>
                      </div>

                    </div>

                    <div class="modal-body-right">

                      <!-- 대표자 성명 -->
                      <div class="row">
                        <div class="col-md-3"><label for="MGG_CEO_NM_AES" style="width: 100%;">대표자 성명 <span class="necessary">*</span></label></div>
                        <div class="col-md-7">
                          <input type="text" class="form-control require" value="<?=$garageArray['ceoName']?>"
                            name="MGG_CEO_NM_AES" id="MGG_CEO_NM_AES" placeholder="성명 입력" required>  
                        </div>
                      </div>

                      <!-- 대표자 연락처 -->
                      <div class="row">
                        <div class="col-md-3"><label for="MGG_CEO_TEL_AES" style="width: 100%;">대표자 연락처</label></div>
                        <div class="col-md-7">
                          <input type="text" class="form-control phone require" value="<?=$garageArray['ceoPhoneNum']?>"
                            name="MGG_CEO_TEL_AES" id="MGG_CEO_TEL_AES" placeholder="연락처 -없이 입력" onkeyup="isNumber(event)">
                        </div>
                      </div>

                      <!-- 담당자 성명-->
                      <div class="row">
                        <div class="col-md-3"><label for="MGG_CLAIM_NM_AES">담당자 성명 <span class="necessary">*</span></label></div>
                        <div class="col-md-7">
                          <input type="text" class="form-control" value="<?=$garageArray['chargeName']?>"
                            name="MGG_CLAIM_NM_AES" id="MGG_CLAIM_NM_AES" placeholder="성명 입력" required/>    
                        </div>
                      </div>

                      <!-- 담당자 연락처 -->
                      <div class="row">
                        <div class="col-md-3"><label for="MGG_CLAIM_TEL_AES">담당자 연락처 <span class="necessary">*</span></label></div>
                        <div class="col-md-7">
                          <input type="text" class="form-control phone"  value="<?=$garageArray['chargePhoneNum']?>"
                            name="MGG_CLAIM_TEL_AES" id="MGG_CLAIM_TEL_AES" placeholder="연락처 -없이 입력" onkeyup="isNumber(event)" required/>         
                        </div>
                      </div>


                      <!-- 쇼핑몰등급 -->
                      <div class="row">
                        <div class="col-md-3"><label for="SLL_ID">쇼핑몰등급 <span class="necessary">*</span></label></div>
                        <div class="col-md-7">
                          <select type="text" class="form-control" name="SLL_ID" id="SLL_ID" required>
                            <option value="">=쇼핑몰등급=</option>
                            <?php
                              foreach($rankArray as $idx => $item){
                                $selectedText = "";
                                if($item['rankID'] == $garageArray['rankID']){
                                  $selectedText = "selected";
                                }
                            ?>
                              <option value="<?php echo $item['rankID']?>" <?=$selectedText ?>><?php echo $item['rankName']?></option>
                            <?php
                              }
                            ?>
                          </select>
                        </div>
                      </div>


                      <!-- 위도/경도 -->
                      <div class="row">
                        <div class="col-md-3"><label for="MGG_GPS_LATITUDE">위도/경도</label></div>
                        <div class="col-md-7 frmmap">
                          <input type="text" class="form-control" value="<?=$garageArray['gpsLatitude']?>"
                             name="MGG_GPS_LATITUDE" id="MGG_GPS_LATITUDE" placeholder="위도 입력"/>  

                          <input type="text" class="form-control" value="<?=$garageArray['gpsLongitude']?>"
                             name="MGG_GPS_LONGITUDE" id="MGG_GPS_LONGITUDE" placeholder="경도 입력"> 
                          <button type="button"  onclick="mapLink('<?=$garageArray['address1'].' '.$garageArray['address2']?>')"
                            class="btn btn-default addrBtn" id="mapLinkBtn">확인</button>
                        </div>
                      </div>

                      <!-- 메모 -->
                      <div class="row">
                        <div class="col-md-3"><label for="memo">메모 </label></div>
                        <div class="col-md-7">      
                          <textarea class="form-control memo-textarea" rows="8"  name="memo" id="memo"  placeholder="입력" style="width:100%"><?=$garageArray['memo']?></textarea>  
                        </div>
                      </div>

                    </div>
                    
                             
                    <!-- 사업자등록증 첨부 -->
                    <div class="row" style="width: 100%;">
                      <div style="width: 106px; padding: 0px 15px;"><label for="MGG_BANK_OWN_NM_AES">사업자등록증<br>첨부 <span class="necessary">*</span></label></div>
                      <div class="col-md-7">
                        <div style="display:flex; flex-direction:row">
                        <button type="button" id="businessBtn" class="btn btn-sm btn-default" data-dismiss="modal" onclick="onClickFileBtn('GW_FILE_NM')">파일선택</button>
                        <?php 
                            $displayText = "";
                            $fileName = NOT_SELECT_FILE_TEXT;
                            if(empty($garageArray['imgOriginFilename'])){
                                $displayText = "style='display:none'";
                            }else{
                                $fileName = $garageArray['imgOriginFilename'];
                            }
                        ?>
                            <div class="fileContainer">
                                <div class="fileSelectText"  id="GW_FILE_NM_SELECT_TEXT"><?php echo $fileName ?></div>
                                <!-- <button type='button' id="GW_FILE_NM_DELETE_BTN" class='itemDelBtn' onclick='delUploadItem("GW_FILE_NM")'
                                      <?php echo $displayText ?>>&times;</button> -->
                            </div>
                            <input type="hidden" name="GW_FILE_NM_URI_ORI" id="GW_FILE_NM_URI_ORI" 
                                      value="<?php echo $garageArray['imgURI'] ?>"/>
                            <input type="hidden" name="GW_FILE_NM_ORI" id="GW_FILE_NM_ORI" 
                                      value="<?php echo $garageArray['imgOriginFilename'] ?>"/>
                            <input type="hidden" name="GW_FILE_NM_PHYSICAL_ORI" id="GW_FILE_NM_PHYSICAL_ORI" 
                                      value="<?php echo $garageArray['imgPhysicalFilename'] ?>"/>
                            <input id="GW_FILE_NM" name="GW_FILE_NM" type="file" class="form-control input-md"/>
                        </div>  
                      </div>
                    </div>

                  </div>

              </div>

    </div>
    
    <div class="modal-footer" style="margin: 20px;">
        <div class="text-left">
          <?php if(!empty($_REQUEST['garageID'])):?>                
            <button type="button" class="btn btn-danger" id="btnDelete" onclick="onclickDelBtn('<?=$_REQUEST['garageID']?>')">삭제</button>
          <?php endif;?>
        </div>
        <div class="text-right">
          <?php if(!empty($_REQUEST['garageID'])):?>                
            <button type="submit" class="btn btn-primary" id="btnSubmit">수정</button>
          <?php else:?>
            <button type="submit" class="btn btn-primary" id="btnSubmit">등록</button>
          <?php endif;?>
            <button type="button" class="btn btn-default" data-dismiss="modal" onclick="window.close();">취소</button>
        </div>
    </div>

    </form>
  </div>
</body>



<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/footer.html";
?>
<script src="/js/selectFile.js"></script>
<script>
   
  /** 위도경도 맵 링크  */
  function mapLink(val){
      let addr = replaceAll(val, ' ', '+');
      const popupUrl = 'https://www.google.com/maps/search/'+addr;
      window.open(popupUrl, 
                      'mapwindow', 
                      'width=900,height=580'); 
  }
  /**
   * 주소검색
   * @param {objId : 구주소/신주소 고유 input 아이디(해당 값을 세팅해주기 위해.)}
   */
  function execPostCode(objId){
      new daum.Postcode({
          oncomplete: function(data) {
            let fullAddr = ''; // 최종 주소 변수
            let extraAddr = ''; // 조합형 주소 변수
            // 사용자가 선택한 주소 타입에 따라 해당 주소 값을 가져온다.
            if (data.userSelectedType === 'R') { // 사용자가 도로명 주소를 선택했을 경우
                fullAddr = data.roadAddress;
            } else { // 사용자가 지번 주소를 선택했을 경우(J)
                fullAddr = data.jibunAddress;
            }
            // 사용자가 선택한 주소가 도로명 타입일때 조합한다.
            if(data.userSelectedType === 'R'){
                //법정동명이 있을 경우 추가한다.
                if(data.bname !== ''){
                    extraAddr += data.bname;
                }
                // 건물명이 있을 경우 추가한다.
                if(data.buildingName !== ''){
                    extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
                }
                // 조합형주소의 유무에 따라 양쪽에 괄호를 추가하여 최종 주소를 만든다.
                fullAddr += (extraAddr !== '' ? ' ('+ extraAddr +')' : '');
            }
            // 우편번호와 주소 정보를 해당 필드에 넣는다.
            $("#MGG_ZIP_"+objId).val(data.zonecode);
            $("#MGG_ADDRESS_"+objId+"_1").val(fullAddr);
            $("#MGG_ADDRESS_"+objId+"_2").val("");
            $("#MGG_ADDRESS_"+objId+"_2").focus();
            
            $('#mapLinkBtn').attr('onclick', 'mapLink(\''+data.roadAddress+'\')');
          }
      }).open();
  }

  function isValidation(){
   

    if(empty($('#GW_FILE_NM_ORI').val())){
      alert("사업자등록증을 첨부해주세요");
      $('#GW_FILE_NM').click();
      return false;
    }else{
      if(empty("<?=$_REQUEST['garageID']?>")){
        if(!isAvailabledID){
          $('#MGG_USERID').focus();
          return false;
        }
      }
      return true;
    }
  }

  
  $(document).ready(function(){
    $('#garageForm').submit(function(e){
      e.preventDefault();

      if(isValidation()){
        if(empty("<?=$_REQUEST['garageID']?>")){
          confirmSwalTwoButton('저장 하시겠습니까?','확인', '취소', false ,()=>setData("<?php echo CASE_CREATE ?>"));
        }else{
          confirmSwalTwoButton('수정 하시겠습니까?','확인', '취소', false ,()=>setData("<?php echo CASE_UPDATE ?>"));
        }  
      }
    });
  });
</script>