
<div class="modal-content">
        <form class="form-horizontal" name="editForm" id="editForm" method="post">
            <input type="hidden" name="thisCategoryID" id="thisCategoryID" value="<?php echo $ctgyID?>"/>
            <!-- modal 상단 -->
            <div class="modal-header">
            <h4 class="modal-title">상품 카테고리 관리</h4>  
            </div>


            <!-- modal 내용 -->
            <div class="modal-body">         

                <!-- 상위카테고리 -->
                <div class="row">
                    <div class="col-md-3"><label for="CATEGORY_CD">상위카테고리</label></div>
                    <div class="col-md-5">
                        <select class="form-control selectpicker"  id="parentCategoryID" 
                                data-live-search="true"
                        >
                            <option value="">=없음=</option>
                            <?php
                                foreach($categoryArray as $key => $item){
                                    $isSelectedOption = "";
                                    if($item['thisCategoryID'] == $ctgyInfo['parentCategoryID']){
                                        $isSelectedOption = "selected";
                                    }

                                    if($item['thisCategoryID'] != $ctgyID){
                            ?>
                                <option <?php echo $isSelectedOption ?> value="<?php echo $item['thisCategoryID']?>"><?php echo $item['thisCategory']?></option>
                            <?php
                                }}
                            ?>
                        </select>
                    </div>
                </div>

                <!-- 카테고리코드 -->
                <div class="row">
                    <?php
                        if(empty($ctgyID)){
                    ?>
                        <div class="col-md-3"><label for="SECTION_CD">카테고리코드 <span class="necessary">*</span></label></div>
                        <div class="col-md-5">
                            <input type="text" class="form-control" 
                                id="SECTION_CD" name="SECTION_CD" placeholder="입력" required/>
                            
                        </div>
                        <div class="col-md-2">
                        <button type="button" onclick="onCheckDuplicateCtgyID()" class="btn btn-default">중복검사</button>
                        </div>
                    <?php
                        }
                    ?>
                      
                   
                </div>

                <!-- 카테고리명 -->
                <div class="row">
                    <div class="col-md-3"><label for="SECTION">카테고리명 <span class="necessary">*</span></label></div>
                    <div class="col-md-5">
                        <input type="text" class="form-control" value="<?php echo $ctgyInfo['thisCategory'] ?>" 
                            id="SECTION" name="SECTION" placeholder="입력" required/>
                    </div>
                </div>

                <!-- 노출여부 -->
                <div class="row">
                    <div class="col-md-3"><label for="EXPOSURE_YN">노출여부 <span class="necessary">*</span></label></div>
                    <div class="col-md-5">
                        <label class="radio-inline" for="EXPOSURE_Y">
                            <?php
                                $isExposureYChecked = "checked";
                                if($ctgyInfo['exposeYN'] != "Y"){
                                    $isExposureYChecked = "";
                                }
                            ?>
                            <input <?php echo $isExposureYChecked ?> required type="radio" name="EXPOSURE_YN" id="EXPOSURE_Y" value="Y"/>노출
                        </label>
                        <label class="radio-inline" for="EXPOSURE_N">
                        <?php
                                $isExposureNChecked = "";
                                if($ctgyInfo['exposeYN'] == "N"){
                                    $isExposureNChecked = "checked";
                                }
                            ?>
                            <input <?php echo $isExposureNChecked ?>  required type="radio" name="EXPOSURE_YN" id="EXPOSURE_N" value="N">미노출
                        </label>
                    </div>
                </div>

                <!-- 노출순서 -->
                <div class="row">
                    <div class="col-md-3"><label for="EXPOSURE_ORDER">노출순서 <span class="necessary">*</span></label></div>
                    <div class="col-md-5">
                        <input type="text" class="form-control" required value="<?php echo $ctgyInfo['orderNum'] ?>"
                            onkeyup="isNumber(event)" id="EXPOSURE_ORDER" name="EXPOSURE_ORDER" placeholder="숫자 입력"/>
                        </div>
                </div>
            </div>


            <!-- modal 하단 -->
            <div class="modal-footer">
                <div class="text-left">
                    <?php if(!empty($ctgyID)) : ?>
                        <button type="button" class="btn btn-danger" id="btnDelete" onclick="confirmDelete()">삭제</button>
                    <?php endif; ?>
                </div>
                <div class="text-right">
                    <?php if(empty($ctgyID)) : ?>
                        <button type="submit"  id="submitBtn" class="btn btn-primary">등록</button>
                    <?php else : ?>
                        <button type="submit"  id="submitBtn" class="btn btn-primary">수정</button>
                    <?php endif; ?>
                    <!-- 취소btn -->
                    <button type="button" onclick="onClose()" class="btn btn-default" data-dismiss="modal">취소</button>
                </div>
            </div>

        </form>
</div>
<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/footerpopup.html";
?>

<script>
    let isCtgyDuplicateID = ($('#thisCategoryID').val() != "") ? true : false;

    $('#editForm').submit(function(e){
        e.preventDefault();

        if(isCtgyDuplicateID){
            if($('#thisCategoryID').val() != ""){
                confirmSwalTwoButton('수정 하시겠습니까?','확인', '취소', false ,()=>setData("<?php echo CASE_UPDATE ?>"));
            }else{
                confirmSwalTwoButton('저장 하시겠습니까?','확인', '취소', false ,()=>setData("<?php echo CASE_CREATE ?>"));
            }
        }else{
            alert("카테고리 아이디 중복검사가 필요합니다.");
        }
        
    });

    /** 삭제 */
    function confirmDelete(){
        confirmSwalTwoButton(CONFIRM_DELETE_MSG, DEL_MSG, CANCEL_MSG, true,()=>{
            delData("<?php echo $ctgyID?>")
        }, "");
    }

    function onClose(){
        window.close();
    }

    function onCheckDuplicateCtgyID(){
        if($('#SECTION_CD').val().trim() != ""){
            const data = {
                REQ_MODE: CASE_CONFLICT_CHECK,
                thisCategoryID : $('#SECTION_CD').val()
            };
            callAjax(ERP_CONTROLLER_URL,'POST', data).then((result)=>{
                if(isJson(result)){
                    const res = JSON.parse(result);
                    alert(res.message);
                    if(res.status == OK){
                        isCtgyDuplicateID = true;
                    }else{
                        isCtgyDuplicateID = false;
                    }
                }else{
                    alert(ERROR_MSG_1200);
                }
            }); 
        }else{
            alert("상품카테고리 아이디를 입력해주세요.");
            $('#SECTION_CD').focus();
        }
    }
</script>
