<style>
.nwt-search-form .bootstrap-select>.dropdown-toggle.bs-placeholder, .bootstrap-select>.dropdown-toggle.bs-placeholder:active, .bootstrap-select>.dropdown-toggle.bs-placeholder:focus, .bootstrap-select>.dropdown-toggle.bs-placeholder:hover {
    height: 34px;
}
.bootstrap-select .btn {
	padding-top: 8px;
    height: 34px !important;
}
</style>
<!-- jQuery v1.12.4 -->
<script src="/js/jquery-1.12.4.min.js"></script>
<!-- Bootstrap v3.3.7 -->
<script src="/bootstrap/js/bootstrap.min.js"></script>
<!-- editor js -->
<script type="text/javascript" src="/summernote/summernote.js"></script>
<!-- multiselect.js -->


<!-- 상수 js -->
<script src="/js/const.js"></script>

<script src="/js/error.js"></script>
<script src="/js/common.js"></script>
<script src="/js/searchOptions.js"></script>
<script src="js/app.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.0/jquery.form.min.js" integrity="sha384-E4RHdVZeKSwHURtFU54q6xQyOpwAhqHxy2xl9NLW9TQIqdNrNh60QVClBRBkjeB8" crossorigin="anonymous"></script>
<script src="/js/sweetalert.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/js/bootstrap-select.min.js"></script>  

  <!-- 페이지 카테고리 -->
  <div class="container-fluid">
    <div class="nwt-policy-top">
      <div>
        <?php if(empty($itemID)) : ?>
            <h3 class="nwt-category"><small>상품 관리&nbsp;&nbsp;></small>상품 등록</h3>
        <?php else : ?>
            <h3 class="nwt-category"><small>상품 관리&nbsp;&nbsp;></small>상품 수정</h3>
        <?php endif; ?>
       
      </div>  
    </div>
  </div>
  
<form class="form-horizontal" name="editForm" id="editForm" method="post">
    <input type="hidden" id="productID" name="productID" value="<?php echo $itemID ?>"/>
   <div class="container-fluid policy-body">
      <div class="policy-body-content">
        
            <!-- modal 내용 -->
            <div class="modal-body">   

                <div class="modal-top">
                    <!-- 상품 왼쪽영역 -->
                    <div class="modal-top-left">
                        <!-- 관리구분 -->
                        <div class="modal-unit radio-modal-unit">
                            <div class="modal-label"><label for="managementDivision">관리구분 <span class="necessary">*</span></label></div>
                            <div class="modal-select">
                                <div class="col-md-12 modal-radio">
                                    <?php echo getRadio($this->optionArray['EC_MGMT_TYPE'], "mgmtTypeCode", $itemInfo['mgmtTypeCode']) ?>
                                </div>
                            </div>
                        </div>

                         <!-- 상품아이디-->
                         <div class="modal-unit">
                            <div class="modal-label"><label for="itemID">상품아이디  <span class="necessary">*</span></label></div>
                            <div class="modal-input">
                                <?php
                                    $readonlyText = "";
                                    if(!empty($itemID)){
                                        $readonlyText = "readonly";
                                    }
                                ?>
                                <input type="text" class="form-control" value="<?php echo $itemID?>"  maxLength="10" minLength="10" 
                                    id="itemID" name="itemID" placeholder="10자 입력" <?php echo $readonlyText?> required/>
                                <?php if(empty($itemID)) : ?>
                                    <button type="button" class="btn btn-default" id="btnDuplicate" onclick="onCheckDuplicate()">중복검사</button>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- 카테고리 -->
                        <div class="modal-unit">
                            <div class="modal-label"><label for="categoryCd">카테고리 <span class="necessary">*</span></label></div>
                            <div class="modal-input"  style="width:70%">
                                <div class="row">
                                    <!-- 상위카테고리 -->
                                    <div class="col-md-6 category-unit">
                                        <select class="form-control" name="categoryCd1" id="categoryCd1" onchange="onChangeParentCtgy()" required>
                                            <option value="">=상위카테고리=</option>
                                            <?php
                                                foreach($categoryArray as $key => $item){
                                                    $isSelectedOption = "";
                                                    if($item['thisCategoryID'] == $itemInfo['parentCategoryID']){
                                                        $isSelectedOption = "selected";
                                                    }
                                            ?>
                                                <option <?php echo $isSelectedOption ?> value="<?php echo $item['thisCategoryID']?>"><?php echo $item['thisCategory']?></option>
                                            <?php
                                                }
                                            ?>
                                        </select>
                                    </div>

                                    <!-- 하위카테고리 -->
                                    <div class="col-md-6 category-unit">
                                        <?php
                                            $disabledText = "disabled";
                                            if(!empty($itemInfo['categoryID'])){
                                                $disabledText = "";
                                            }
                                        ?>
                                        <select class="form-control" name="categoryCd2" id="categoryCd2" <?=$disabledText?> required>
                                            <option value="">=하위카테고리=</option>
                                            <?php
                                                foreach($itemInfo['childCategory']  as $key => $item){
                                                    $isSelectedOption = "";
                                                    if($item['thisCategoryID'] == $itemInfo['categoryID']){
                                                        $isSelectedOption = "selected";
                                                    }
                                            ?>
                                                <option <?php echo $isSelectedOption ?> value="<?php echo $item['thisCategoryID']?>"><?php echo $item['thisCategory']?></option>
                                            <?php
                                                }
                                            ?>
                                        </select>
                                    </div>
                                  </div>
                            </div>
                        </div>
                        
                        <!-- 매입처 -->
                        <div class="modal-unit multi-input">
                            <div class="modal-label"><label for="partnerID">매입처 <span class="necessary">*</span></label></div>
                            <div class="modal-input">
                                <select class="form-control selectpicker"  id="partnerID"  name="partnerID"
                                        data-live-search="true" required
                                >
                                    <option value="">=선택=</option>
                                    <?php
                                        foreach($buyMngtArray as $key => $item){
                                            $isSelectedOption = "";
                                            if($item['partnerID'] == $itemInfo['partnerID']){
                                                $isSelectedOption = "selected";
                                            }
                                    ?>
                                        <option <?php echo $isSelectedOption ?> value="<?php echo $item['partnerID']?>"><?php echo $item['companyName']?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <!-- 판매여부 -->
                        <div class="modal-unit radio-modal-unit">
                            <div class="modal-label"><label for="managementDivision">품절여부</label></div>
                            <div class="modal-select">
                                <?php
                                    $soldOutYN_Y_Text = "";

                                    if($itemInfo['soldOutYN'] == "Y"){
                                        $soldOutYN_Y_Text = "checked";
                                    }
                                ?>
                                <div class="col-md-12 modal-radio">
                                    <label class="checkbox-inline" for="soldout"><input type="checkbox" name="soldOutYN" id="soldout" value="Y" <?php echo $soldOutYN_Y_Text ?>>품절</label> 
                                    <!-- <label class="radio-inline" for="saleY"><input type="radio" name="soldOutYN" id="saleY" value="N" <?php echo $soldOutYN_N_Text ?> >판매중</label>
                                    <label class="radio-inline" for="soldout"><input type="radio" name="soldOutYN" id="soldout" value="Y" <?php echo $soldOutYN_Y_Text ?>>품절</label>   -->
                                </div>
                            </div>
                        </div>

                        <!-- 상품코드 -->
                        <div class="modal-unit">
                            <div class="modal-label"><label for="goodsCode">상품코드</label></div>
                            <div class="modal-input">
                                 <input type="text" class="form-control" value="<?php echo $itemInfo['goodsCode']?>"
                                        id="goodsCode" name="goodsCode" placeholder="영문,숫자 입력" /> 
                            </div>
                        </div>

                        <!-- 상품명 -->
                        <div class="modal-unit">
                            <div class="modal-label"><label for="goodsName">상품명 (기본)  <span class="necessary">*</span></label></div>
                            <div class="modal-input">
                                <input type="text" class="form-control"  value="<?php echo $itemInfo['goodsName']?>"
                                    id="goodsName" name="goodsName" placeholder="리스트에 보이는 상품명" required/>
                            </div>
                        </div>
                        <!-- 상품명 -->
                        <div class="modal-unit">
                            <div class="modal-label"><label for="goodsNameShort">상품명 (짧은)  <span class="necessary">*</span></label></div>
                            <div class="modal-input">
                                <input type="text" class="form-control" value="<?php echo $itemInfo['goodsNameShort']?>"
                                    id="goodsNameShort" name="goodsNameShort" placeholder="주문서에 보이는 상품명" required/>
                            </div>
                        </div>
                        <!-- 상품명 -->
                        <div class="modal-unit">
                            <div class="modal-label"><label for="goodsNameLong">상품명 (긴)  <span class="necessary">*</span></label></div>
                            <div class="modal-input">
                              <input type="text" class="form-control" value="<?php echo $itemInfo['goodsNameLong']?>"
                                    id="goodsNameLong" name="goodsNameLong" placeholder="제품상세화면에 보이는 상품명" required/>
                            </div>
                        </div>

                        <!-- 규격/용량 -->
                        <div class="modal-unit">
                            <div class="modal-label"><label for="goodsSpec">규격/용량 </label></div>
                            <div class="modal-input">
                                <input type="text" class="form-control" value="<?php echo $itemInfo['goodsSpec']?>"
                                     id="goodsSpec" name="goodsSpec" placeholder="입력"/>
                            </div>
                        </div>

                         <!-- 포장단위 -->
                         <div class="modal-unit">
                            <div class="modal-label"><label for="unitPerBox">포장단위</label></div>
                            <div class="modal-input">
                                <input type="text" class="form-control" value="<?php echo $itemInfo['unitPerBox']?>"
                                    onkeyup="isNumber(event)" id="unitPerBox" name="unitPerBox" placeholder="숫자만 입력"/>
                            </div>
                        </div>

                         <!-- 최소구매수량 -->
                         <div class="modal-unit">
                            <div class="modal-label"><label for="minBuyCnt">최소구매수량  <span class="necessary">*</span></label></div>
                            <div class="modal-input">
                                <input type="text" class="form-control" value="<?php echo $itemInfo['minBuyCnt']?>"
                                    onkeyup="isNumber(event)" id="minBuyCnt" name="minBuyCnt" placeholder="숫자만 입력" required/>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 상품정보 오른쪽 영역 -->
                    <div class="modal-top-right">
                        <!-- 매입가 -->
                        <div class="modal-unit">
                            <div class="modal-label"><label for="buyPrice">매입가  <span class="necessary">*</span></label></div>
                            <div class="modal-input">
                                <input type="text" class="form-control"  value="<?php echo  $itemInfo['buyPrice']?>"
                                    onkeyup="isNumber(event)" id="buyPrice" name="buyPrice" placeholder="숫자만 입력" required/>
                            </div>
                        </div>

                        <!-- 도매가 -->
                        <div class="modal-unit">
                            <div class="modal-label"><label for="wSalePrice">도매가  <span class="necessary">*</span></label></div>
                            <div class="modal-input">
                                <input type="text" class="form-control" value="<?php echo  $itemInfo['wSalePrice']?>"
                                    onkeyup="isNumber(event)" id="wSalePrice" name="wSalePrice" placeholder="숫자만 입력" required/>
                            </div>
                        </div>

                        <!-- 소비자가 -->
                        <div class="modal-unit">
                            <div class="modal-label"><label for="retailPrice">소비자가  <span class="necessary">*</span></label></div>
                            <div class="modal-input">
                                <input type="text" class="form-control" value="<?php echo  $itemInfo['retailPrice']?>"
                                    onkeyup="isNumber(event)" id="retailPrice" name="retailPrice" placeholder="숫자만 입력" required/>
                            </div>
                        </div>
                        
                        <!-- 판매가 등급별 금액은 등급관리에 등급과 연동되어야함-->
                        <div class="modal-unit">
                            <div class="modal-label"><label for="price">판매가  <span class="necessary">*</span></label></div>
                            <div class="modal-input">
                                <?php
                                    $readonlyText = "";
                                    $checkboxCheckedText = "";
                                    $salePrice = $itemInfo['salePrice'];
                                    $earnPoint = $itemInfo['earnPoint'];
                                    if($itemInfo['salePriceInquiryYN'] == 'Y'){
                                        $readonlyText = "disabled";
                                        $checkboxCheckedText = "checked";
                                    }
                                ?>
                                <input type="text" class="form-control" value="<?php echo $salePrice  ?>" required onblur="onCheckPriceValue()"
                                    onkeyup="isNumber(event)" id="salePrice" name="salePrice" placeholder="숫자만 입력" <?php echo $readonlyText?>/>
                                <div class="modal-checkbox priceQuestion">
                                    <input type="checkbox" class="checkbox-inline" <?php echo $checkboxCheckedText?>
                                         id="priceQuestionCheckbox" name="priceQuestionCheckbox" value="Y"/>
                                    <label for="priceQuestionCheckbox">문의</label>
                                </div>
                            </div>
                        </div>
                       
                        <!-- 적립금 : 입력시 고정값, 미입력시 등급별차등-->
                        <div class="modal-unit">
                            <div class="modal-label"><label for="earnPoint">과세여부</label></div>
                            <div class="modal-input">
                                <?php
                                    $isYCheckedText = "";
                                    $isNCheckedText = "checked";

                                    if($itemInfo['applyVatYN'] == "Y"){
                                        $isYCheckedText = "checked";
                                        $isNCheckedText = "";
                                    }
                                ?>
                                 <label class='radio-inline' for='applyVatYN_N'>
			                     <input type='radio' name='applyVatYN' id='applyVatYN_N' value='N' <?php echo $isNCheckedText ?>>면세</label>
                                 <label class='radio-inline' for='applyVatYN_Y'>
                                 <input type='radio'  name='applyVatYN' id='applyVatYN_Y' value='Y' <?php echo $isYCheckedText ?>>과세</label>
                                 
                            </div>
                        </div>


                        <!-- 적립금 : 입력시 고정값, 미입력시 등급별차등-->
                        <div class="modal-unit">
                            <div class="modal-label"><label for="earnPoint">적립금  <span class="necessary">*</span></label></div>
                            <div class="modal-input">
                                <input type="text" class="form-control" value="<?php echo $earnPoint ?>" required
                                     onkeyup="isNumber(event)" id="earnPoint" name="earnPoint" placeholder="숫자만 입력" <?php echo $readonlyText?>/>
                            </div>
                        </div>

                        <!-- 배송비 -->
                        <div class="modal-unit">
                            <div class="modal-label"><label for="deliveryPolicyID">배송정책  <span class="necessary">*</span></label></div>
                            <div class="modal-input">
                                <select class="form-control" name="deliveryPolicyID" id="deliveryPolicyID" required>
                                    <option value="">=선택=</option>
                                    <?php
                                        foreach($deliveryArray as $key => $item){
                                            $isSelectedOption = "";
                                            $selectedValue = (empty($itemInfo['deliveryPolicyID']))? EC_DELIVERY_FEE_TYPE_INTEGRATED  : $itemInfo['deliveryPolicyID'];
                                            
                                            if($item['deliveryPolicyID'] == $selectedValue){
                                                $isSelectedOption = "selected";
                                            }
                                    ?>
                                        <option <?php echo $isSelectedOption ?> value="<?php echo $item['deliveryPolicyID']?>"><?php echo $item['deliveryPolicyName']?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <?php if(empty($itemID)) : ?>
                            <!-- 기초수량 -->
                            <div class="modal-unit">
                                <div class="modal-label"><label for="initQuantity">기초수량  <span class="necessary">*</span></label></div>
                                <div class="modal-input">
                                    <input type="text" class="form-control" required
                                        onkeyup="isNumber(event)" id="initQuantity" name="initQuantity" placeholder="숫자만 입력"/>
                                </div>
                            </div>
                        <?php endif ?>
                        
                        <?php if(!empty($itemID)) : ?>
                            <!-- 재고수량 -->
                            <div class="modal-unit">
                                <div class="modal-label"><label for="stockQuantity">재고수량  <span class="necessary">*</span></label></div>
                                <div class="modal-input">
                                    <input type="text" class="form-control" value="<?php echo number_format( $itemInfo['stockQuantity']) ?>"
                                        id="stockQuantity"  readonly required/>
                                </div>
                            </div>
                        <?php endif ?>

                        <!-- 제조사 -->
                        <div class="modal-unit">
                            <div class="modal-label"><label for="manufacturerName">제조사</label></div>
                            <div class="modal-input">
                                <input type="text" class="form-control" value="<?php echo $itemInfo['manufacturerName']?>"
                                     id="manufacturerName" name="manufacturerName" placeholder="입력"/>
                            </div>
                        </div>
                        
                        <!-- 원산지 -->
                        <div class="modal-unit">
                            <div class="modal-label"><label for="originName">원산지</label></div>
                            <div class="modal-input">
                                <input type="text" class="form-control" value="<?php echo $itemInfo['originName']?>"
                                    id="originName" name="originName" placeholder="입력"/>
                            </div>
                        </div>

                        <!-- 브랜드 -->
                        <div class="modal-unit">
                            <div class="modal-label"><label for="brandName">브랜드</label></div>
                            <div class="modal-input">
                                <input type="text" class="form-control" value="<?php echo $itemInfo['brandName']?>"
                                    id="brandName" name="brandName" placeholder="입력"/>
                            </div>
                        </div>
                    </div>
                </div>



                <!-- 하단 상품 이미지 -->
                <div class="modal-bottom">
                      <!-- 상품옵션 -->
                      <!-- <div class="modal-unit">
                        <div class="modal-label"><label for="goodsOption">상품옵션  <span class="necessary">*</span></label></div>
                        <div class="col-md-5 modal-radio">
                            <?php
                                if(empty($itemInfo['goodsOptionUseYN'] )){
                                    $goodsOptionUseY_Checked = "checked";
                                    $goodsOptionUseN_Checked = "";
                                }else{
                                    if($itemInfo['goodsOptionUseYN'] == "Y"){
                                        $goodsOptionUseY_Checked = "checked";
                                        $goodsOptionUseN_Checked = "";
                                    }else{
                                        $goodsOptionUseY_Checked = "";
                                        $goodsOptionUseN_Checked = "checked";
                                    }
                                }
                                
                                
                            ?>
                            <label class="radio-inline" for="option_Y">
                                <input type="radio" id="option_Y" name="goodsOption" value="Y" required <?php echo $goodsOptionUseY_Checked ?>/>사용함</label>
                            <label class="radio-inline" for="option_N">
                                <input type="radio" id="option_N" name="goodsOption" value="N" required <?php echo $goodsOptionUseN_Checked ?>/>사용안함</label>
                            
                        </div>
                      </div> -->

                    
                        <!-- <div class="modal-unit" id="productOptionContainer">
                          <div class="nwt-table modal-table">
                              <div class="text-right"><button type="button" class="btn btn-default option_add_btn" onclick="onClickAddOptionBtn()">옵션추가</button></div>
                              <table class="table table-bordered">
                                  <thead>
                                      <tr class="active">
                                          <th class="text-center">옵션명</th>
                                          <th class="text-center">옵션단가</th>
                                          <th class="text-center">옵션사용여부</th>
                                          <th class="text-center">옵션삭제</th>
                                      </tr>
                                  </thead>
                                  <tbody id="optionTable">
                                      <?php
                                        if(!empty($itemInfo['goodsOptions'])){
                                            foreach($itemInfo['goodsOptions'] as $key => $item){
                                                echo "<script>document.write(getOptionHtmlText(".json_encode($item,true).",".$key."));</script>";
                                            }
                                        }else{
                                            echo "<script>document.write(getOptionHtmlText());</script>";
                                        }
                                      ?>
                                  </tbody>
                              </table>
                            </div>
                      </div> -->



                      <!-- 상품이미지 대표 -->
                      <div class="modal-unit">
                          <div class="modal-label"><label for="GW_FILE_NM">상품이미지<br>대표  <span class="necessary">*</span></label></div>
                          <div class="modal-select">
                          <div class="row">
                                <div class="col-md-12" style="display:flex; flex-direction:row">
                                    <button type="button" id="businessBtn" class="btn btn-default" data-dismiss="modal" onclick="onClickFileBtn('img')">파일선택</button>
                                    <?php 
                                        $displayText = "";
                                        $fileName = NOT_SELECT_FILE_TEXT;

                                        if(empty($itemInfo['imgOriginFilename'])){
                                            $displayText = "style='display:none'";
                                        }else{
                                            $fileName = $itemInfo['imgOriginFilename'];
                                        }
                                    ?>
                                        <div class="fileContainer">
                                                <div class="fileSelectText" id="img_SELECT_TEXT" ><?php echo $fileName ?></div>
                                                <button type='button' id="img_DELETE_BTN" class='itemDelBtn' onclick='delUploadItem("img")'
                                                    <?php echo $displayText ?>>&times;</button>
                                        </div>
                                        <input type="hidden" name="imgURI" id="imgURI" 
                                                  value="<?php echo $itemInfo['imgURI'] ?>"/>
                                        <input type="hidden" name="imgOriginFilename" id="imgOriginFilename" 
                                                  value="<?php echo $itemInfo['imgOriginFilename'] ?>"/>
                                        <input type="hidden" name="imgPhysicalFilename" id="imgPhysicalFilename" 
                                                  value="<?php echo $itemInfo['imgPhysicalFilename'] ?>"/>
                                        <input id="img" name="img" type="file" class="form-control input-md file" accept="image/jpeg,image/png"/>
                                </div>
                            </div>
                          </div>
                      </div>

                      <!-- 상품이미지 1-->
                      <div class="modal-unit">
                          <div class="modal-label"><label for="GW_FILE_NM">상품이미지</label></div>
                          <div class="modal-select">
                              <div class="row">
                                    <div class="col-md-12" style="display:flex; flex-direction:row">
                                        <button type="button"  class="btn btn-default"  onclick="onClickFileBtn('img1')">파일선택</button>
                                        <?php 
                                            $displayText = "";
                                            $fileName = NOT_SELECT_FILE_TEXT;
                                            
                                            if(empty($itemInfo['img1OriginFilename'])){
                                                $displayText = "style='display:none'";
                                            }else{
                                                $fileName = $itemInfo['img1OriginFilename'];
                                            }
                                        ?>
                                            <div class="fileContainer">
                                                    <div class="fileSelectText" id="img1_SELECT_TEXT" ><?php echo $fileName ?></div>
                                                    <button type='button' id="img1_DELETE_BTN" class='itemDelBtn' onclick='delUploadItem("img1")'
                                                        <?php echo $displayText ?>>&times;</button>
                                            </div>
                                            <input type="hidden" name="img1URI" id="img1URI" 
                                                    value="<?php echo $itemInfo['img1URI'] ?>"/>
                                            <input type="hidden" name="img1OriginFilename" id="img1OriginFilename" 
                                                    value="<?php echo $itemInfo['img1OriginFilename'] ?>"/>
                                            <input type="hidden" name="img1PhysicalFilename" id="img1PhysicalFilename" 
                                                    value="<?php echo $itemInfo['img1PhysicalFilename'] ?>"/>
                                            <input id="img1" name="img1" type="file" class="form-control input-md file" accept="image/jpeg,image/png"/>
                                    </div>
                              </div>
                          </div>
                      </div>
                      <!-- 상품이미지 2-->
                      <div class="modal-unit">
                          <div class="modal-label"><label for="GW_FILE_NM"> </label></div>
                          <div class="modal-select">
                              <div class="row">
                                    <div class="col-md-12" style="display:flex; flex-direction:row">
                                        <button type="button" class="btn btn-default"  onclick="onClickFileBtn('img2')">파일선택</button>
                                        <?php 
                                            $displayText = "";
                                            $fileName = NOT_SELECT_FILE_TEXT;
                                            
                                            if(empty($itemInfo['img2OriginFilename'])){
                                                $displayText = "style='display:none'";
                                            }else{
                                                $fileName = $itemInfo['img2OriginFilename'];
                                            }
                                        ?>
                                            <div class="fileContainer">
                                                    <div class="fileSelectText" id="img2_SELECT_TEXT" ><?php echo $fileName ?></div>
                                                    <button type='button' id="img2_DELETE_BTN" class='itemDelBtn' onclick='delUploadItem("img2")'
                                                        <?php echo $displayText ?>>&times;</button>
                                            </div>
                                            <input type="hidden" name="img2URI" id="img2URI" 
                                                    value="<?php echo $itemInfo['img2URI'] ?>"/>
                                            <input type="hidden" name="img2OriginFilename" id="img2OriginFilename" 
                                                    value="<?php echo $itemInfo['img2OriginFilename'] ?>"/>
                                            <input type="hidden" name="img2PhysicalFilename" id="img2PhysicalFilename" 
                                                    value="<?php echo $itemInfo['img2PhysicalFilename'] ?>"/>
                                            <input id="img2" name="img2" type="file" class="form-control input-md file" accept="image/jpeg,image/png"/>
                                    </div>
                              </div>
                          </div>
                      </div>
                      <!-- 상품이미지 3-->
                      <div class="modal-unit">
                          <div class="modal-label"><label for="GW_FILE_NM"> </label></div>
                          <div class="modal-select">
                              <div class="row">
                                    <div class="col-md-12" style="display:flex; flex-direction:row">
                                        <button type="button" class="btn btn-default"  onclick="onClickFileBtn('img3')">파일선택</button>
                                        <?php 
                                            $displayText = "";
                                            $fileName = NOT_SELECT_FILE_TEXT;
                                            
                                            if(empty($itemInfo['img3OriginFilename'])){
                                                $displayText = "style='display:none'";
                                            }else{
                                                $fileName = $itemInfo['img3OriginFilename'];
                                            }
                                        ?>
                                            <div class="fileContainer">
                                                    <div class="fileSelectText" id="img3_SELECT_TEXT" ><?php echo $fileName ?></div>
                                                    <button type='button' id="img3_DELETE_BTN" class='itemDelBtn' onclick='delUploadItem("img3")'
                                                        <?php echo $displayText ?>>&times;</button>
                                            </div>
                                            <input type="hidden" name="img3URI" id="img3URI" 
                                                    value="<?php echo $itemInfo['img3URI'] ?>"/>
                                            <input type="hidden" name="img3OriginFilename" id="img3OriginFilename" 
                                                    value="<?php echo $itemInfo['img3OriginFilename'] ?>"/>
                                            <input type="hidden" name="img3PhysicalFilename" id="img3PhysicalFilename" 
                                                    value="<?php echo $itemInfo['img3PhysicalFilename'] ?>"/>
                                            <input id="img3" name="img3" type="file" class="form-control input-md file" accept="image/jpeg,image/png"/>
                                    </div>
                              </div>
                          </div>
                      </div>

                      <!-- 상품이미지 4-->
                      <div class="modal-unit">
                          <div class="modal-label"><label for="GW_FILE_NM"> </label></div>
                          <div class="modal-select">
                              <div class="row">
                                    <div class="col-md-12" style="display:flex; flex-direction:row">
                                        <button type="button" class="btn btn-default"  onclick="onClickFileBtn('img4')">파일선택</button>
                                        <?php 
                                            $displayText = "";
                                            $fileName = NOT_SELECT_FILE_TEXT;
                                            
                                            if(empty($itemInfo['img4OriginFilename'])){
                                                $displayText = "style='display:none'";
                                            }else{
                                                $fileName = $itemInfo['img4OriginFilename'];
                                            }
                                        ?>
                                            <div class="fileContainer">
                                                    <div class="fileSelectText" id="img4_SELECT_TEXT" ><?php echo $fileName ?></div>
                                                    <button type='button' id="img4_DELETE_BTN" class='itemDelBtn' onclick='delUploadItem("img4")'
                                                        <?php echo $displayText ?>>&times;</button>
                                            </div>
                                            <input type="hidden" name="img4URI" id="img4URI" 
                                                    value="<?php echo $itemInfo['img4URI'] ?>"/>
                                            <input type="hidden" name="img4OriginFilename" id="img4OriginFilename" 
                                                    value="<?php echo $itemInfo['img4OriginFilename'] ?>"/>
                                            <input type="hidden" name="img4PhysicalFilename" id="img4PhysicalFilename" 
                                                    value="<?php echo $itemInfo['img4PhysicalFilename'] ?>"/>
                                            <input id="img4" name="img4" type="file" class="form-control input-md file" accept="image/jpeg,image/png"/>
                                    </div>
                              </div>
                          </div>
                      </div>

                      <hr>

                      <!-- 상품정보 -->
                      <div class="modal-unit">
                          <div class="modal-label"><label for="GW_FILE_NM">상품정보</label></div>
                          <div class="modal-select">
                              <div class="row">
                                  <div class="col-md-12">
                                      <div class="summernote" id="goodsInfo_" name="goodsInfo_">
                                      </div>
                                      <textarea class="summernoteArea" id="goodsInfo" name="goodsInfo" style="display: none;">
                                         <?php echo $itemInfo['goodsInfo'] ?>
                                    </textarea>
                                  </div>

                                    <div class="row">
                                        <div class="col-md-12" style="display:flex; flex-direction:row">
                                            <button type="button" class="btn btn-default"  onclick="onClickFileBtn('goodsInfoImg1')">파일선택</button>
                                            <?php 
                                                $displayText = "";
                                                $fileName = NOT_SELECT_FILE_TEXT;
                                                
                                                if(empty($itemInfo['goodsInfoImg1OriginFilename'])){
                                                    $displayText = "style='display:none'";
                                                }else{
                                                    $fileName = $itemInfo['goodsInfoImg1OriginFilename'];
                                                }
                                            ?>
                                                <div class="fileContainer">
                                                    <div class="fileSelectText" id="goodsInfoImg1_SELECT_TEXT" ><?php echo $fileName ?></div>
                                                    <button type='button' id="goodsInfoImg1_DELETE_BTN" class='itemDelBtn' onclick='delUploadItem("goodsInfoImg1")'
                                                        <?php echo $displayText ?>>&times;</button>
                                                </div>
                                                <input type="hidden" name="goodsInfoImg1URI" id="goodsInfoImg1URI" 
                                                        value="<?php echo $itemInfo['goodsInfoImg1URI'] ?>"/>
                                                <input type="hidden" name="goodsInfoImg1OriginFilename" id="goodsInfoImg1OriginFilename" 
                                                        value="<?php echo $itemInfo['goodsInfoImg1OriginFilename'] ?>"/>
                                                <input type="hidden" name="goodsInfoImg1PhysicalFilename" id="goodsInfoImg1PhysicalFilename" 
                                                        value="<?php echo $itemInfo['goodsInfoImg1PhysicalFilename'] ?>"/>
                                                <input id="goodsInfoImg1" name="goodsInfoImg1" type="file" class="form-control input-md file" accept="image/jpeg,image/png"/>
                                        </div>
                                    </div>

                                    <div class="row" style="margin-top:10px">
                                        <div class="col-md-12" style="display:flex; flex-direction:row">
                                            <button type="button" class="btn btn-default"  onclick="onClickFileBtn('goodsInfoImg2')">파일선택</button>
                                            <?php 
                                                $displayText = "";
                                                $fileName = NOT_SELECT_FILE_TEXT;
                                                
                                                if(empty($itemInfo['goodsInfoImg2OriginFilename'])){
                                                    $displayText = "style='display:none'";
                                                }else{
                                                    $fileName = $itemInfo['goodsInfoImg2OriginFilename'];
                                                }
                                            ?>
                                                <div class="fileContainer">
                                                    <div class="fileSelectText" id="goodsInfoImg2_SELECT_TEXT" ><?php echo $fileName ?></div>
                                                    <button type='button' id="goodsInfoImg2_DELETE_BTN" class='itemDelBtn' onclick='delUploadItem("goodsInfoImg2")'
                                                        <?php echo $displayText ?>>&times;</button>
                                                </div>
                                                <input type="hidden" name="goodsInfoImg2URI" id="goodsInfoImg2URI" 
                                                        value="<?php echo $itemInfo['goodsInfoImg2URI'] ?>"/>
                                                <input type="hidden" name="goodsInfoImg2OriginFilename" id="goodsInfoImg2OriginFilename" 
                                                        value="<?php echo $itemInfo['goodsInfoImg2OriginFilename'] ?>"/>
                                                <input type="hidden" name="goodsInfoImg2PhysicalFilename" id="goodsInfoImg2PhysicalFilename" 
                                                        value="<?php echo $itemInfo['goodsInfoImg2PhysicalFilename'] ?>"/>
                                                <input id="goodsInfoImg2" name="goodsInfoImg2" type="file" class="form-control input-md file" accept="image/jpeg,image/png"/>
                                        </div>
                                    </div>
                              </div>
                          </div>
                      </div>
                      <hr>
                      <!-- 배송정보 -->
                      <div class="modal-unit">
                          <div class="modal-label">
                              <label for="GW_FILE_NM">배송정보</label><br><br>
                              <button type="button" class="btn btn-default" onclick="onLoadRecentInfo('deliveryInfo','<?php echo GET_RECENT_DELIVERY_INFO ?>')">최근정보 불러오기</button>
                            </div>
                          <div class="modal-select">
                              <div class="row deliveryInfo">
                                  <div class="col-md-12">
                                        <div class="summernote" id="deliveryInfo_" name="deliveryInfo_">
                                        </div>
                                        <textarea class="summernoteArea" id="deliveryInfo" name="deliveryInfo" style="display: none;">
                                            <?php echo $itemInfo['deliveryInfo'] ?>
                                        </textarea>
                                  </div>
                                
                                  <div class="col-md-12" style="display:flex; flex-direction:row">
                                        <button type="button" class="btn btn-default"  onclick="onClickFileBtn('deliveryInfoImg1')">파일선택</button>
                                        <?php 
                                            $displayText = "";
                                            $fileName = NOT_SELECT_FILE_TEXT;
                                            
                                            if(empty($itemInfo['deliveryInfoImg1OriginFilename'])){
                                                $displayText = "style='display:none'";
                                            }else{
                                                $fileName = $itemInfo['deliveryInfoImg1OriginFilename'];
                                            }
                                        ?>
                                            <div class="fileContainer">
                                                    <div class="fileSelectText" id="deliveryInfoImg1_SELECT_TEXT" ><?php echo $fileName ?></div>
                                                    <button type='button' id="deliveryInfoImg1_DELETE_BTN" class='itemDelBtn' onclick='delUploadItem("deliveryInfoImg1")'
                                                        <?php echo $displayText ?>>&times;</button>
                                            </div>
                                            <input type="hidden" name="deliveryInfoImg1URI" id="deliveryInfoImg1URI" 
                                                    value="<?php echo $itemInfo['deliveryInfoImg1URI'] ?>"/>
                                            <input type="hidden" name="deliveryInfoImg1OriginFilename" id="deliveryInfoImg1OriginFilename" 
                                                    value="<?php echo $itemInfo['deliveryInfoImg1OriginFilename'] ?>"/>
                                            <input type="hidden" name="deliveryInfoImg1PhysicalFilename" id="deliveryInfoImg1PhysicalFilename" 
                                                    value="<?php echo $itemInfo['deliveryInfoImg1PhysicalFilename'] ?>"/>
                                            <input id="deliveryInfoImg1" name="deliveryInfoImg1" type="file" class="form-control input-md file" accept="image/jpeg,image/png"/>
                                    </div>
                                </div>

                                <div class="row" style="margin-top:10px">
                                    <div class="col-md-12" style="display:flex; flex-direction:row">
                                        <button type="button" class="btn btn-default"  onclick="onClickFileBtn('deliveryInfoImg2')">파일선택</button>
                                        <?php 
                                            $displayText = "";
                                            $fileName = NOT_SELECT_FILE_TEXT;
                                            
                                            if(empty($itemInfo['deliveryInfoImg2OriginFilename'])){
                                                $displayText = "style='display:none'";
                                            }else{
                                                $fileName = $itemInfo['deliveryInfoImg2OriginFilename'];
                                            }
                                        ?>
                                            <div class="fileContainer">
                                                    <div class="fileSelectText" id="deliveryInfoImg2_SELECT_TEXT" ><?php echo $fileName ?></div>
                                                    <button type='button' id="deliveryInfoImg2_DELETE_BTN" class='itemDelBtn' onclick='delUploadItem("deliveryInfoImg2")'
                                                        <?php echo $displayText ?>>&times;</button>
                                            </div>
                                            <input type="hidden" name="deliveryInfoImg2URI" id="deliveryInfoImg2URI" 
                                                    value="<?php echo $itemInfo['deliveryInfoImg2URI'] ?>"/>
                                            <input type="hidden" name="deliveryInfoImg2OriginFilename" id="deliveryInfoImg2OriginFilename" 
                                                    value="<?php echo $itemInfo['deliveryInfoImg2OriginFilename'] ?>"/>
                                            <input type="hidden" name="deliveryInfoImg2PhysicalFilename" id="deliveryInfoImg2PhysicalFilename" 
                                                    value="<?php echo $itemInfo['deliveryInfoImg2PhysicalFilename'] ?>"/>
                                            <input id="deliveryInfoImg2" name="deliveryInfoImg2" type="file" class="form-control input-md file" accept="image/jpeg,image/png"/>
                                    </div>
                                </div>
                              </div>
                          </div>
                      </div>
                      <hr>
                      <!-- 반품정보 -->
                      <div class="modal-unit">
                          <div class="modal-label">
                              <label for="GW_FILE_NM">반품정보<br><br>
                                <button type="button" class="btn btn-default" onclick="onLoadRecentInfo('returnInfo','<?php echo GET_RECENT_RETURN_INFO ?>')">최근정보 불러오기</button>
                              </label>
                          </div>
                          <div class="modal-select">
                              <div class="row returnInfo">
                                  <div class="col-md-12">
                                        <div class="summernote" id="returnInfo_" name="returnInfo_">
                                        </div>
                                        <textarea class="summernoteArea" id="returnInfo" name="returnInfo" style="display: none;">
                                            <?php echo $itemInfo['returnInfo'] ?>
                                        </textarea>
                                  </div>
                                  
                                  <div class="col-md-12" style="display:flex; flex-direction:row">
                                        <button type="button" class="btn btn-default"  onclick="onClickFileBtn('returnInfoImg1')">파일선택</button>
                                        <?php 
                                            $displayText = "";
                                            $fileName = NOT_SELECT_FILE_TEXT;
                                            
                                            if(empty($itemInfo['returnInfoImg1OriginFilename'])){
                                                $displayText = "style='display:none'";
                                            }else{
                                                $fileName = $itemInfo['returnInfoImg1OriginFilename'];
                                            }
                                        ?>
                                            <div class="fileContainer">
                                                    <div class="fileSelectText" id="returnInfoImg1_SELECT_TEXT" ><?php echo $fileName ?></div>
                                                    <button type='button' id="returnInfoImg1_DELETE_BTN" class='itemDelBtn' onclick='delUploadItem("returnInfoImg1")'
                                                        <?php echo $displayText ?>>&times;</button>
                                            </div>
                                            <input type="hidden" name="returnInfoImg1URI" id="returnInfoImg1URI" 
                                                    value="<?php echo $itemInfo['returnInfoImg1URI'] ?>"/>
                                            <input type="hidden" name="returnInfoImg1OriginFilename" id="returnInfoImg1OriginFilename" 
                                                    value="<?php echo $itemInfo['returnInfoImg1OriginFilename'] ?>"/>
                                            <input type="hidden" name="returnInfoImg1PhysicalFilename" id="returnInfoImg1PhysicalFilename" 
                                                    value="<?php echo $itemInfo['returnInfoImg1PhysicalFilename'] ?>"/>
                                            <input id="returnInfoImg1" name="returnInfoImg1" type="file" class="form-control input-md file" accept="image/jpeg,image/png"/>
                                    </div>
                                </div>

                                <div class="row" style="margin-top:10px">
                                    <div class="col-md-12" style="display:flex; flex-direction:row">
                                        <button type="button" class="btn btn-default"  onclick="onClickFileBtn('returnInfoImg2')">파일선택</button>
                                        <?php 
                                            $displayText = "";
                                            $fileName = NOT_SELECT_FILE_TEXT;
                                            
                                            if(empty($itemInfo['returnInfoImg2OriginFilename'])){
                                                $displayText = "style='display:none'";
                                            }else{
                                                $fileName = $itemInfo['returnInfoImg2OriginFilename'];
                                            }
                                        ?>
                                            <div class="fileContainer">
                                                    <div class="fileSelectText" id="returnInfoImg2_SELECT_TEXT" ><?php echo $fileName ?></div>
                                                    <button type='button' id="returnInfoImg2_DELETE_BTN" class='itemDelBtn' onclick='delUploadItem("returnInfoImg2")'
                                                        <?php echo $displayText ?>>&times;</button>
                                            </div>
                                            <input type="hidden" name="returnInfoImg2URI" id="returnInfoImg2URI" 
                                                    value="<?php echo $itemInfo['returnInfoImg2URI'] ?>"/>
                                            <input type="hidden" name="returnInfoImg2OriginFilename" id="returnInfoImg2OriginFilename" 
                                                    value="<?php echo $itemInfo['returnInfoImg2OriginFilename'] ?>"/>
                                            <input type="hidden" name="returnInfoImg2PhysicalFilename" id="returnInfoImg2PhysicalFilename" 
                                                    value="<?php echo $itemInfo['returnInfoImg2PhysicalFilename'] ?>"/>
                                            <input id="returnInfoImg2" name="returnInfoImg2" type="file" class="form-control input-md file" accept="image/jpeg,image/png"/>
                                    </div>
                                </div>
                                 
                              </div>
                          </div>
                      </div>
                      <hr>
                      <!-- 상품카달로그 -->
                      <div class="modal-unit">
                          <div class="modal-label"><label for="GW_FILE_NM">상품카달로그</label></div>
                          <div class="modal-select">
                              <div class="row">
                                    <div class="col-md-12" style="display:flex; flex-direction:row">
                                        <button type="button" id="businessBtn" class="btn btn-default" data-dismiss="modal" onclick="onClickFileBtn('catalog')">파일선택</button>
                                        <?php 
                                            $displayText = "";
                                            $fileName = NOT_SELECT_FILE_TEXT;
                                            
                                            if(empty($itemInfo['catalogOriginFilename'])){
                                                $displayText = "style='display:none'";
                                            }else{
                                                $fileName = $itemInfo['catalogOriginFilename'];
                                            }
                                        ?>
                                            <div class="fileContainer">
                                                    <div class="fileSelectText" id="catalog_SELECT_TEXT" ><?php echo $fileName ?></div>
                                                    <button type='button' id="catalog_DELETE_BTN" class='itemDelBtn' onclick='delUploadItem("catalog")'
                                                        <?php echo $displayText ?>>&times;</button>
                                            </div>
                                            <input type="hidden" name="catalogURI" id="catalogURI" 
                                                    value="<?php echo $itemInfo['catalogURI'] ?>"/>
                                            <input type="hidden" name="catalogOriginFilename" id="catalogOriginFilename" 
                                                    value="<?php echo $itemInfo['catalogOriginFilename'] ?>"/>
                                            <input type="hidden" name="catalogPhysicalFilename" id="catalogPhysicalFilename" 
                                                    value="<?php echo $itemInfo['catalogPhysicalFilename'] ?>"/>
                                            <input id="catalog" name="catalog" type="file" class="form-control input-md file " accept="image/jpeg,image/png">
                                    </div>
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
            <?php if(!empty($itemID)) : ?>
                <button type="button" class="btn btn-danger" id="btnDelete" onclick="confirmDelete()" >삭제</button>
            <?php endif; ?>
            
       
        </div>
        <div class="text-right">
            <?php if(empty($itemID)) : ?>
                <!-- 등록btn -->
                <button type="submit" class="btn btn-primary btn-update">등록</button>
            <?php else : ?>
                <!-- 등록btn -->
                <button type="submit" class="btn btn-primary btn-update">수정</button>
            <?php endif; ?>
            
            <!-- 취소btn -->
            <button type="button" class="btn btn-default btn-back" onclick="window.close();">취소</button>
        </div>
    </div>
</form>
<script>
     let isCheckDuplicate = (!empty($('#productID').val())) ? true : false;

     function onCheckDuplicate(){
        if($('#itemID').val().trim() != ""){
            if($('#itemID').val().length == 10){
                const data = {
                    REQ_MODE: CASE_CONFLICT_CHECK,
                    itemID : $('#itemID').val()
                };
                callAjax(ERP_CONTROLLER_URL,'POST', data).then((result)=>{
                    if(isJson(result)){
                        const res = JSON.parse(result);
                        alert(res.message);
                        if(res.status == OK){
                            isCheckDuplicate = true;
                        }else{
                            isCheckDuplicate = false;
                        }
                    }else{
                        alert(ERROR_MSG_1200);
                    }
                });
            }else{
                alert("상품아이디는 10자를 입력해주셔야 합니다.");
                $('#itemID').focus();
            }
             
        }else{
            alert("상품아이디를 입력해주세요.");
            $('#itemID').focus();
        }
        
    }

    /** 삭제 */
    function confirmDelete(){
        confirmSwalTwoButton(CONFIRM_DELETE_MSG, DEL_MSG, CANCEL_MSG, true,()=>{
            delData("<?php echo $itemID?>")
        }, "");
    }

    /**
     * 첨부파일 삭제시
     * @param{objID : 삭제할 아이디}
     */
    function delUploadItem(objID){
        $('#'+objID).val("");
        $('#'+objID+"OriginFilename").val("");
        $('#'+objID+"PhysicalFilename").val("");

        $('#'+objID+'_DELETE_BTN').hide();
        $('#'+objID+'_SELECT_TEXT').text("<?php echo NOT_SELECT_FILE_TEXT ?>");
    }

    /** 첨부파일 change 이벤트 */
    $('input[type="file"]').change(function(e){
        const fileName = e.target.files[0].name;
        const id = e.target.id;

        $('#'+id+"OriginFilename").val(fileName);
        $('#'+id+'_SELECT_TEXT').text(fileName);
        if(!$('#'+id+'_DELETE_BTN').is(":visible")){
            $('#'+id+'_DELETE_BTN').show();
        }
    });


    /**
     * 첨부파일 선택 시 hidden처리된 파일을 클릭 트리거
     * @param{objID : 삭제할 아이디}
     */
    function onClickFileBtn(objID){
        $('#'+objID).click();
    }

    function validation(){
        if(!isCheckDuplicate){
            alert("상품아이디 중복검사를 해주세요");
            $('#itemID').focus();
            return false;
        }else if(empty($('#imgOriginFilename').val())){
            alert("상품대표이미지를 설정해주셔야 합니다.");
            $('#img').click();
            return false;
        }else{
            return true;
        }
        
    }

    $('#editForm').submit(function(e){
        e.preventDefault();

        if(validation()){
            if(empty($('#productID').val())){
                confirmSwalTwoButton('저장 하시겠습니까?','확인', '취소', false ,()=>setData("<?php echo CASE_CREATE ?>"));
            }else{
                confirmSwalTwoButton('수정 하시겠습니까?','확인', '취소', false ,()=>setData("<?php echo CASE_UPDATE ?>"));

            }
        }
    });

    function onLoadRecentInfo(objID, reqMode){
        const data = {
            REQ_MODE: CASE_READ,
            reqMode : reqMode
        };

        callAjax(ERP_CONTROLLER_URL,'POST', data).then((result)=>{
            if(isJson(result)){
                const res = JSON.parse(result);
                if(res.status == OK){
                    const data = res.data.data;
                    const cnt = $('.'+objID+' input[type=file]').length;
                    $('#'+objID+"_").summernote('code', data[objID]);
                    $('#'+objID).val(data[objID+"Info"]);

                    for(let i=1 ; i<=cnt; ++i){
                        if(!empty(data[objID+"Img"+i+"OriginFilename"])){
                            $('#'+objID+"Img"+i+"OriginFilename").val(data[objID+"Img"+i+"OriginFilename"]);
                            $('#'+objID+"Img"+i+"PhysicalFilename").val(data[objID+"Img"+i+"PhysicalFilename"]);
                            $('#'+objID+"Img"+i+"URI").val(data[objID+"Img"+i+"URI"]);
                            $('#'+objID+"Img"+i+"_SELECT_TEXT").html(data[objID+"Img"+i+"OriginFilename"]);
                            $('#'+objID+"Img"+i+"_DELETE_BTN").show();
                        } 
                    } 
                }else{
                    isCheckDuplicate = false;
                }
            }else{
                alert(ERROR_MSG_1200);
            }
        });
    }
</script>
</html>