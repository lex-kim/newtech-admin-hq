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
<script src="/js/common.js"></script>
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
        <input type="hidden" name="deliveryID" id="deliveryID" value="<?php echo $deliveryID?>"/>
        <!-- modal 상단 -->
        <div class="modal-header">
            <?php if(empty($deliveryID)) : ?>
                <h4 class="modal-title">배송정책 등록</h4>
            <?php else : ?>
                <h4 class="modal-title">배송정책 수정</h4>
            <?php endif; ?>
        </div>

        <!-- modal 내용 -->
        <div class="modal-body">   
            
            <div class="row">
                <!-- 출고일 -->
                <div class="col-md-1"><label for="deliveryPolicyID">배송정책ID</label></div>
                <div class="col-md-3">
                    <?php
                        $diabledText = "";
                        if(!empty($deliveryID)){
                            $diabledText = "readonly";
                        }
                    ?>
                    <input type="text" class="form-control" maxlength="10" minlength="10" value="<?php echo $deliveryInfo['deliveryPolicyID'] ?>"
                        id="deliveryPolicyID" name="deliveryPolicyID" placeholder="10자리 입력" <?php echo $diabledText?> required/>
                </div>
                <!-- 배송비정책명 -->
                <div class="col-md-1"><label for="deliveryPolicyName">배송비정책명</label></div>
                <div class="col-md-3">
                    <input type="text" class="form-control"  value="<?php echo $deliveryInfo['deliveryPolicyName'] ?>"
                        id="deliveryPolicyName" name="deliveryPolicyName" placeholder="입력" required/>
                </div>

                <!-- 배송방법 -->
                <div class="col-md-1"><label for="deliveryPolicyName">배송방법</label></div>
                <div class="col-md-3">
                     <?php echo getRadio($this->optionArray['EC_DELIVERY_METHOD'], "EC_DELIVERY_METHOD", $deliveryInfo['deliveryMethodCode']) ?>
                </div>
                
                
            </div>    
            <div class="row">
                <!-- 결제구분 -->
                <div class="col-md-1"><label for="EC_DELIVERY_FEE">결제구분</label></div>
                <div class="col-md-3">
                    <?php echo getRadio($this->optionArray['EC_DELIVERY_FEE'], "EC_DELIVERY_FEE", $deliveryInfo['payTypeCode']) ?>
                </div>

                <!-- 부과기준 -->
                <div class="col-md-1"><label for="EC_DELIVERY_FEE_TYPE">부과기준</label></div>
                <div class="col-md-3">
                    <?php echo getRadio($this->optionArray['EC_DELIVERY_FEE_TYPE'], "EC_DELIVERY_FEE_TYPE", $deliveryInfo['priceTypeCode']) ?>
                </div>

                <?php
                    $isPayTypeFixed = false; 
                    $isShowStandardText = "style = display:none";
                    $starndtHtml = '<script>document.write(getFixDeliveryPriceHtml())</script>';
                    if($deliveryInfo['priceTypeCode'] == EC_DELIVERY_FEE_TYPE_FIX){
                        $isPayTypeFixed  = true;
                        $starndtHtml = '<script type="text/javascript">document.write(getFixDeliveryPriceHtml('.$deliveryInfo['deliveryPrice'].'))</script>';
                        $isShowStandardText = "";
                    }
                ?>
                <div id="81624_container" <?php echo $isShowStandardText ?>>
                    <?php echo $starndtHtml  ?> 
                </div>   
            </div>  
            
            
            <?php
                $feeTypeShowText = "style='display:block'";
                if($isPayTypeFixed){
                    $feeTypeShowText = "style='display:none'";
                }
            ?>
            <div id="STANDARD_AREA" <?php echo $feeTypeShowText ?> >
                <hr>
                <div class="text-right">
                    <button type="button" class="btn btn-default option_add_btn" id="ROW_ADD_BTN" onclick="addFeeTypeRow()">행 추가</button>
                </div>
                <div class="nwt-table modal-table">
                    <table class="table table-bordered">
                        <colgroup>
                            <col width="7%">
                            <col width="2%">
                            <col width="0.5%">
                        </colgroup>
                        <thead>
                            <tr class="active">
                                <th class="text-center" id="STD_TEXT">
                                    수량
                                </th>
                                <th class="text-center">적용배송비</th>
                                <th class="text-center">삭제</th>
                            </tr>
                        </thead>

                        <?php
                            $cntDisplayText = "style='display:none'";
                            $priceDisplayText = "style='display:none'";
                            if(empty($deliveryInfo['deliveryPolicy'])){
                                $html = "<script>document.write(getFreeTypeHtmlText(".true."))</script>";
                            }else{
                                foreach($deliveryInfo['deliveryPolicy'] as $key => $item){                            
                                    if($key == 0){
                                        $html .=  '<script type="text/javascript">
                                        document.write(getFreeTypeHtmlText('.true.','.json_encode($item,true).'));
                                        </script>'; 
                                    }else{
                                        $html .=  '<script type="text/javascript">
                                        document.write(getFreeTypeHtmlText(" ",'.json_encode($item,true).'));
                                        </script>'; 
                                    }
                                      
                                }
                            }
                            if(empty($deliveryInfo['deliveryPolicy']) || $deliveryInfo['priceTypeCode'] == EC_DELIVERY_FEE_TYPE_NUM){
                                $cntDisplayText = "";
                                $cntHtmlText = $html;
                                $priceHtmlText = "<script>document.write(getFreeTypeHtmlText(".true."))</script>";
                            }else{
                                $priceDisplayText = "";
                                $cntHtmlText = "<script>document.write(getFreeTypeHtmlText(".true."))</script>";
                                $priceHtmlText = $html;
                            }
                        ?>

                        <tbody id="81620_container" <?php echo $cntDisplayText?>>
                             <?php echo $cntHtmlText ?>
                        </tbody>
                        <tbody id="81622_container" <?php echo $priceDisplayText?>>
                            <?php echo $priceHtmlText ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <!-- modal 하단 -->
        <div class="modal-footer">
            <div class="text-left">
                <?php if(!empty($deliveryID)) : ?>
                    <button type="button" class="btn btn-danger" id="btnDelete" onclick="confirmDelete()">삭제</button>
                <?php endif; ?>
            </div>
            <div class="text-right">
                <?php if(empty($deliveryID)) : ?>
                <!-- 등록btn -->
                    <button type="submit" class="btn btn-primary" id="submitBtn">등록</button>
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
<script>
     selectPayTypeValue = "<?php echo $deliveryInfo['priceTypeCode'] ?>";
     let deliveryPolicyHtml = oriPayTypeHtml;

     /** 전송 name 세팅 */
     function setFormName(objID){
        $('#'+objID+"_container input").each(function(){ 
            if($(this).attr('type') != "checkbox"){
                const id = $(this).attr("id").split("_")[0];
                if(objID == EC_DELIVERY_FEE_TYPE_FIX){
                    $(this).attr("name",$(this).attr("id"));
                }else{
                    $(this).attr("name",id+"[]");
                } 
              
            }
        });
     }

     /** 시작수량/금액 과 종료수량/금액을 비교하는 validation */
     function setDataValidation(){
        let isValidation = true;
        $("input:radio[name=EC_DELIVERY_FEE_TYPE]").each(function(){
           
            if($(this).is(":checked")){
                setFormName($(this).val());
            }else{
                $('#'+$(this).val()+"_container input").each(function(){ 
                    $(this).attr("name","");
                });
            }
        });

        if($("#STANDARD_AREA").is(":visible")){ // 부과기준이 수량/금액인 경우(필수값 체크)
            const selectedValue =  $("input:radio[name=EC_DELIVERY_FEE_TYPE]:checked").val();
            $('#'+selectedValue+"_container tr").each(function(){ 
                const idx = $(this).attr('id').split("_")[1];
                const startValue = $('#startNumber_'+idx).val();
                const endValue = $('#endNumber_'+idx).val();
                const typeText = $('#STD_TEXT').html();

                if(empty(startValue)){
                    alert("시작"+typeText+"을 입력해주세요.");
                    $('#startNumber_'+idx).focus();
                    isValidation =  false;
                }else if(!$('#endNumber_'+idx).attr('disabled') && empty(endValue)){
                    alert("종료"+typeText+"을 입력해주세요.");
                    $('#endNumber_'+idx).focus();
                    isValidation =  false;
                }else if(empty($('#sectionPrice_'+idx).val())){
                    alert("적용배송비를 입력해주세요.");
                    $('#sectionPrice_'+idx).focus();
                    isValidation =  false;
                }else{
                    if(!$('#endNumber_'+idx).attr('disabled')){
                        if(Number(startValue) > Number(endValue) ){
                            const text = "시작"+typeText+"이 종료"+typeText+" 보다 클 수 없습니다.";
                            alert(text);
                            $('#startNumber_'+idx).focus();
                            isValidation = false;
                        }
                    }
                }
                
            });
        }else{ // 부과기준이 고정인 경우
            if($('#deliveryPrice').is(":visible")){
                if($('#deliveryPrice').val().trim() == ""){
                    alert("부과금액을 입력해주세요.");
                    $('#deliveryPrice').focus();
                    isValidation = false
                }
            }
        }
        return isValidation;
    }

      /** 삭제 */
    function confirmDelete(){
        confirmSwalTwoButton(CONFIRM_DELETE_MSG, DEL_MSG, CANCEL_MSG, true,()=>{
            delData("<?php echo $deliveryID?>")
        }, "");
    }

     $('#editForm').submit(function(e){
        e.preventDefault();

        if(setDataValidation()){
            if($('#deliveryID').val() != ""){
                confirmSwalTwoButton('수정 하시겠습니까?','확인', '취소', false ,()=>setData("<?php echo CASE_UPDATE ?>"));
            }else{
                confirmSwalTwoButton('저장 하시겠습니까?','확인', '취소', false ,()=>setData("<?php echo CASE_CREATE ?>"));
            }
        }
    });

   
    
</script>