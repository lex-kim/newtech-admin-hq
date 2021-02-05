
<form method="post" name="grantForm" id="grantForm" >
      <!-- modal 상단 -->
      <div class="modal-header">
        <h4 class="modal-title">업체 승인</h4>  
      </div>

      <!-- modal 내용 -->
      <div class="modal-body"> 
          <!-- 구분 -->
          <div class="row">
            <div class="col-md-3"><label for="divisionID">구분 <span class="necessary">*</span></label></div>
            <div class="col-md-7">
                <select class="form-control require" id="divisionID" name="divisionID" required>
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

          <!-- 전화쇼핑몰등급번호 -->
          <div class="row">
            <div class="col-md-3"><label for="SLL_ID">쇼핑몰등급 <span class="necessary">*</span></label></div>
            <div class="col-md-7">
              <select id="rankID"  name="rankID" class="form-control" required>
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

          <!-- 거래여부 -->
          <div class="row">
            <div class="col-md-3"><label for="RCT_ID">거래상태 <span class="necessary">*</span></label></div>
            <div class="col-md-7">
                <select id="contractStatusCode"  name="contractStatusCode"class="form-control" required>
                    <?php echo getOption($this->optionArray['CONTRACT_TYPE'], "" , $garageArray['contractStatusID']) ?>
                </select>   
            </div>
          </div>

          <!-- 메모 -->
          <div class="row">
            <div class="col-md-3"><label for="RCT_ID">메모 <span class="necessary">*</span></label></div>
            <div class="col-md-7">
              <textarea class="form-control memo-textarea" rows="8"  name="memo" id="memo"  placeholder="입력" style="width:100%"><?=$garageArray['memo']?></textarea>
            </div>
          </div>

      </div>

      <!-- modal 하단 -->
      <div class="modal-footer">
        <div class="text-left">
        </div>
        <div class="text-right">
          <!-- 수정btn -->
          <button type="submit" class="btn btn-primary" >변경</button>
          <!-- 취소btn -->
          <button type="button" onclick="onClose()" class="btn btn-default" data-dismiss="modal">닫기</button>
        </div>
      </div>

</form>

<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/footerpopup.html";
?>
<script>
    $(document).ready(function(){
        $('#grantForm').submit(function(e){
            e.preventDefault();

            confirmSwalTwoButton('변경 하시겠습니까?','확인', '취소', false ,()=>updateData("<?=$_REQUEST['garageID']?>"));
        });
    });
</script>