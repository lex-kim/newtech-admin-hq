
 <!-- footer.html -->
<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/script.html";
?>
 <div class="modal-add" id="deposit-modify">
    <form name="billForm" id="billForm">
      <!-- modal 상단 -->
      <div class="modal-header">
        <h4 class="modal-title">지출증빙</h4>  
      </div>

      <?php
        require $_SERVER['DOCUMENT_ROOT']."/include/billForm.php";
      ?>

      <!-- modal 하단 -->
      <div class="modal-footer">
        <div class="text-left">
        </div>
        <div class="text-right">
          <!-- 수정btn -->
          <button type="submit" class="btn btn-primary" id="submitBtn" name ="submitBtn">증빙처리</button>
          <!-- 취소btn -->
          <button type="button" class="btn btn-default" onclick="window.close();">닫기</button>
        </div>
      </div>

    </form>
  </div>
</body>
</html>
<script>

  function setBillData(REQ_MODE){
    let form = $("#billForm")[0];
    let formData = new FormData(form);
    formData.append('REQ_MODE', REQ_MODE);
    formData.append('salesID', "<?php echo $_REQUEST['salesID'] ?>");
    
    callFormAjax(ERP_CONTROLLER_URL, 'POST', formData).then((result)=>{
      if(isJson(result)){
        const res = JSON.parse(result);
        if(res.status == OK){
            alert("발행되었습니다.");
            
            window.close();
            window.opener.getList();
        }else{
          alert((empty(res.message) ? ERROR_MSG_1100 : res.message));
        }
      }else{
          alert(ERROR_MSG_1200);
      }
    });  
  }

  $(document).ready(function(){
      $('#billForm').submit(function(e){
          e.preventDefault();
          confirmSwalTwoButton('발행 하시겠습니까?','확인', '취소', false ,()=>{
            setBillData(CASE_CREATE);
          });
      });
  });
</script>