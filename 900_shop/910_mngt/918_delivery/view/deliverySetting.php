<!-- jQuery v1.12.4 -->
<script src="/js/jquery-1.12.4.min.js"></script>
<!-- Bootstrap v3.3.7 -->
<script src="/bootstrap/js/bootstrap.min.js"></script>
<!-- editor js -->
<script type="text/javascript" src="/summernote/summernote.js"></script>
<!-- multiselect.js -->


<!-- 상수 js -->
<script src="/js/const.js"></script>

<!-- 에러 js -->
<script src="/js/error.js"></script>

<!-- 공통 js -->

<!-- 공통옵션 js -->
<script src="/js/searchOptions.js"></script>
<!-- 각 페이지별 js -->
<script src="js/app.js"></script>


<!-- jquery-ui calendar 때문? -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
 

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.0/jquery.form.min.js" integrity="sha384-E4RHdVZeKSwHURtFU54q6xQyOpwAhqHxy2xl9NLW9TQIqdNrNh60QVClBRBkjeB8" crossorigin="anonymous"></script>
<!-- sweetalert -->
<script src="/js/sweetalert.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/js/bootstrap-select.min.js"></script>

<div class="modal-add" id="add-new">
    <form class="form-horizontal" name="editForm" id="editForm" method="post">
        <!-- modal 상단 -->
        <div class="modal-header">
            <?php if(empty($deliveryID)) : ?>
                <h4 class="modal-title">배송정책 등록</h4>
            <?php else : ?>
                <h4 class="modal-title">배송정책 수정</h4>
            <?php endif; ?>
        </div>

        <!-- modal 내용 -->
        <div class="modal-body integrated-setting">   
            
            <div class="row">
                <!-- 출고일 -->
                <div class="col-md-3"><label for="deliveryFee">기본 배송비</label></div>
                <div class="col-md-7">
                    <input type="text" class="form-control text-right price" value="<?php echo number_format($deliveryEnvInfo['deliveryFee']) ?>"
                         onkeyup="isNumber(event)"  id="deliveryFee" name="deliveryFee" placeholder="숫자만 입력" required/><span>원</span>
                </div>
                
                
            </div>    
            <div class="row">
                <!-- 결제구분 -->
                <div class="col-md-3"><label for="deliveryFeeFree">무료배송 기준</label></div>
                <div class="col-md-7">
                    <input type="text" class="form-control price text-right" value="<?php echo number_format($deliveryEnvInfo['deliveryFeeFree']) ?>"
                         onkeyup="isNumber(event)" id="deliveryFeeFree" name="deliveryFeeFree" placeholder="숫자만 입력" required/><span>원 이상 무료배송</span>
                </div>

            </div>  
            
        </div>


        <!-- modal 하단 -->
        <div class="modal-footer">
            <div class="text-left">
            </div>
            <div class="text-right">
                <?php if(empty($deliveryEnvInfo)) : ?>
                <!-- 등록btn -->
                    <button type="submit" class="btn btn-primary" id="submitBtn">저장</button>
                <?php else : ?>
                    <button type="submit" class="btn btn-primary" id="btnUpdate">수정</button>
                <?php endif; ?>

                <!-- 취소btn -->
                <button type="button" class="btn btn-default" onclick="window.close();">취소</button>
            </div>
        </div>

    </form>

</div>
</html>
<script src="/js/common.js"></script>
<script>

     $('#editForm').submit(function(e){
        e.preventDefault();
        confirmSwalTwoButton('저장 하시겠습니까?','확인', '취소', false ,()=>setData("<?php echo CASE_CREATE ?>"));
    });

   
    
</script>