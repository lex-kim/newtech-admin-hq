
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
<script src="/js/setProduct.js"></script>
<script>
  const productList = JSON.parse('<?php echo json_encode($productInfo)?>')
</script>
<style>
    .payment-way label:nth-child(4){
      margin:0px;
    }
</style>
<body>
  <form id="salesForm">
  <input type="hidden" id="salesID" name="salesID" value="<?php echo $_REQUEST['salesID'] ?>">
  <div class="container-fluid shop-estimate" style="width: 21cm">
    <div class="estimate-top">
      <h2 class="text-center bill-title consignment-bill-title">일반판매</h2>
      <div class="estimate-top-left">
        <div class="calendar-label">판매일 :</div>
        <div class="calendar-icon">
          
          <input type="text"class="form-control date" value="<?php echo $cSalesInfo['issueDate'] ?>"
            id="issueDate" name="issueDate" autocomplete="off" placeholder="yyyy-mm-dd" required/>
          <span class="glyphicon glyphicon-calendar search-glypicon"></span>   
        </div>
      </div>
      <div class="estimate-top-right"></div>
    </div>


    <table class="table table-bordered" id="estimate-bizinfo">
      <colgroup>
        <col width="2%"/>
        <col width="6%"/>
        <col width="2%"/>
        <col width="6%"/>
      </colgroup>
      <tbody>
        <tr>
          <td class="text-center active">공업사</td>
          <td class="text-left">
            <input type="hidden" id="garageNameCode" name="garageNameCode" value="<?php echo $cSalesInfo['garageID']?>"/>
            <input type="text" class="form-control" id="garageName" name="garageName" value="<?php echo $cSalesInfo['garageName']?>"  placeholder="입력 후 선택 (자동완성)"/>
            <?php echo "<script>setGarageAutocomplete('".$garageInfo."');</script>";?>
          </td>
          <td class="text-center active">판매자</td>
          <td class="text-left">
            <input type="hidden" id="userNameCode" name="userNameCode" value="<?php echo $cSalesInfo['employeeID']?>"/>
            <input type="text" class="form-control" id="userName" name="userName" value="<?php echo $cSalesInfo['employeeName']?>"  placeholder="입력 후 선택 (자동완성)"/>
            <?php echo "<script>setUserAutocomplete('".$userInfo."');</script>";?>
          </td>
        </tr>
      </tbody>
    </table>
  

    <!-- 상품정보 -->
    <div class="text-right"><button type="button" class="btn btn-default option_add_btn" onclick="addItemTable()">행 추가</button></div>
    <table class="table table-bordered" id="estimate-product" style="margin-top:5px">
      <colgroup>
        <col width="3%"/>
        <col width="4%"/>
        <col width="3%"/>
        <col width="4%"/>
        <col width="5%"/>
        <col width="4%"/>
        <col width="5%"/>
        <col width="5%"/>
        <col width="3%"/>
      </colgroup>
      <thead>
        <tr>
          <th class="text-center text-bold active" colspan="2">판매총계</th>
          <th class="text-center text-bold all-total-price" colspan="8">
            <input type="text" class="form-control text-right" onkeyup="isNumber(event)" value="<?php echo $cSalesInfo['salePrice']?>"
              style="width:75%" id="PAYMENT_PRICE_WRITE" name="PAYMENT_PRICE_WRITE" placeholder="입력" required/>
            <span style="font-weight: normal;">(판매총계 수정가능)</span></th>
        </tr>
      </thead>
      <tbody id="itemDataTable">
        <tr class="active">
          <td class="text-center">품명</td>
          <td class="text-center">규격</td>
          <td class="text-center">단가</td>
          <td class="text-center">수량</td>
          <td class="text-center">공급가액</td>
          <td class="text-center">세액</td>
          <td class="text-center">합계액</td>
          <td class="text-center">비고</td>
          <td class="text-center">행 삭제</td>
        </tr>
          <?php if(empty($cSalesInfo['salesDetails'])):?>
            <?php 
              echo "<script>
                      let productTableIdx = 0;
                      document.write(setProductTable());
                    </script>"; 
            ?>
          <?php else:?>
            <?php
              echo "<script> let productTableIdx = ".count($cSalesInfo['salesDetails']).";</script>"; 
              $totalPriceWithoutVAT = 0;
              $totalPrice = 0;
              $totalCnt = 0;
              $totalUnitPrice = 0;
              $totalVAT = 0;

              foreach($cSalesInfo['salesDetails'] as $index => $item){
                $tableIndex = $index++;
                $totalPriceWithoutVAT += $item['unitPrice']+$item['optionPrice'];
                $totalVAT += ($item['unitPriceVAT']+$item['optionPriceVAT'])*$item['quantity'];
                $totalUnitPrice += ($item['unitPrice']+$item['optionPrice'])*$item['quantity'];
                $totalCnt += $item['quantity'];
                $totalPrice += ($item['unitPriceVAT']+$item['optionPriceVAT'])*$item['quantity'] + ($item['unitPrice']+$item['optionPrice'])*$item['quantity'];
            ?>
              <tr class='product' id='productTable_<?php echo $tableIndex?>'>
                <td class="text-center">
                  <select onchange="onSelectItem('<?php echo $tableIndex?>')" id="item_<?php echo $tableIndex?>" name="itemID[]" 
                    data-style="btn-default" class="form-control selectpicker"
                    data-live-search="true" data-live-search-placeholder="Search" required
                  >
                  <option value="">선택</option>
                    <?php foreach($productInfo as $subIdx => $subItem) {
                        $selectedText = "";
                        if($item['itemID'] == $subItem['itemID']){
                          $selectedText = "selected";
                        }
                    ?>
                      <option value="<?php echo $subItem['itemID']?>" <?php echo $selectedText?>><?php echo $subItem['goodsName'] ?></option>
                    <?php } ?>
                  </select>
                </td>
                <td class="text-center">
                  <span id='unit_<?php echo $tableIndex ?>'><?php echo $item['goodsSpec'] ?></span>
                </td>
                <td class="text-right">
                  <input type="hidden" id="itemPrice_<?php echo $tableIndex ?>" value="<?php echo $item['unitPrice'] ?>" name="unitPrice[]"/>
                  <span class='unitPriceClass' id='price_<?php echo $tableIndex ?>'><?php echo number_format($item['unitPrice']) ?></span>
                </td>
                <td class="text-right">
                <input onchange='onchangeCnt("<?php echo $tableIndex ?>")' class='form-control cntClass' name='quantity[]' id='cnt_<?php echo $tableIndex ?>'
                    type='text' onkeyup='isNumber(event)' value='<?php echo $item['quantity'] ?>' placeholder="입력"/>
                </td>
                <td class="text-right">
                  <span class='priceClass' id='unitPrice_<?php echo $tableIndex ?>'><?php echo number_format($item['quantity'] * $item['unitPrice']) ?></span>
                </td>
                <td class="text-right">
                  <span class='vatPriceClass' id='vat_<?php echo $tableIndex ?>'><?php echo number_format($item['unitPriceVAT']*$item['quantity']) ?></span>
                </td>
                <td class="text-right">
                  <span class='totalPriceClass' id='totalPrice_<?php echo $tableIndex ?>'><?php echo number_format(($item['quantity'] * $item['unitPrice'])+($item['unitPriceVAT']*$item['quantity'])) ?></span>
                </td>
                <td class="text-left">
                  <input type="text" value="<?php echo $item['itemMemo'] ?>" class="form-control" id="IN_ETC" name="IN_ETC"/>
                </td>
                <td class='text-center' id='del_<?php echo $tableIndex ?>'>
                  <?php if( $index > 1):?>

                    <button type='button' class='btn btn-danger btn-sm' onclick='onDeleteTable("<?php echo $tableIndex ?>")'>ㅡ</button>
                  <?php endif; ?>
                </td>
              </tr>
            <?php } ?>
          <?php endif;?>
      </tbody>
      <tfoot>
        <tr>
            <td class="text-center active" colspan="2">합계</td>
            <td class="text-right" id="totalUnitPriceTable"><?php echo $totalPriceWithoutVAT?></td>
            <td class="text-right" id="totalCntTable"><?php echo $totalCnt?></td>
            <td class="text-right" id="totalPriceTable"><?php if(!empty($totalUnitPrice)) echo number_format($totalUnitPrice); ?></td>
            <td class="text-right" id="totalVatPriceTable"><?php if(!empty($totalVAT))  echo number_format($totalVAT); ?></td>
            <td class="text-right" id="totalItemPriceTable"><?php if(!empty($totalPrice))  echo number_format($totalPrice); ?></td>
            <td class="text-right"></td>
            <td class="text-right"></td>
        </tr>
      </tfoot>
    </table>

    <table class="table table-bordered">
      <colgroup>
          <col width="1.2%"/>
          <col width="2%"/>
          <col width="1.5%"/>
          <col width="4%"/>
        </colgroup>
      <tr>
        <td class="text-center active">입금여부</td>
        <td class="text-left">
          <?php 
              $checkedY = "checked";
              $checkedN = "";
              if($cSalesInfo['depositYN'] == "N"){
                $checkedY = "";
                $checkedN = "checked";
              }
          ?>
          <label class="radio-inline" for="DEPOSIT_Y"><input type="radio" name="DEPOSIT_YN" id="DEPOSIT_Y" <?php echo $checkedY ?> value="Y"/>입금</label>
          <label class="radio-inline" for="DEPOSIT_N"><input type="radio" name="DEPOSIT_YN" id="DEPOSIT_N"<?php echo $checkedN ?> value="N"/>미입금</label>
        </td>
        <td class="text-center active">결제방법</td>
        <td class="text-left payment-way">
          <?php echo getRadio($this->optionArray['EC_PAYMENT_TYPE'], "paymentCode", $cSalesInfo['paymentCode']) ?>
        </td>
      </tr>
      <tr>
          <td class="text-center active">입금액</td>
          <td class="text-center calendar-icon">
              <input type="text" class="form-control text-right deposit" onkeyup="isNumber(event)" 
                value="<?php echo number_format($cSalesInfo['depositPrice']) == 0 ? "" : $cSalesInfo['depositPrice'] ?>"
                id="depositPrice" name="depositPrice" placeholder="숫자만 입력" required/>
          </td>
          <td class="text-center active">입금일</td>
          <td class="text-center calendar-icon">
              <input type="text" class="form-control date deposit" 
                value="<?php echo $cSalesInfo['depositDate'] == BASE_DATE_TEXT ? "" : $cSalesInfo['depositDate'] ?>"
                id="depositDate" name="depositDate" autocomplete="off" placeholder="yyyy-mm-dd" required/>
              <span class="glyphicon glyphicon-calendar search-glypicon"></span> 
              
          </td>
      </tr>
      <tr>
        <td class="text-center active">지출증빙<br>발행여부</td>
        <td colspan="3">
          <?php if(empty($cSalesInfo['billPublishDate'] )):?>
            <label class="radio-inline" for="BBILL_Y"><input type="radio" name="BBILL_YN" id="BBILL_Y" checked value="N"/>미발행</label>
            <label class="radio-inline" for="BBILL_N"><input type="radio" name="BBILL_YN" id="BBILL_N" value="Y"/>발행</label>
          <?php else:?>
            <span>발행일시 : <?php echo $cSalesInfo['billPublishDate'] ?></span>
            <button type="button" class="btn btn-default estimate-btn" id="consignment-save" onclick="onCancelBill('<?php echo $_REQUEST['salesID'] ?>')">취소</button> 
          <?php endif;?>
        </td>
      </tr>
    </table>


    <table class="table table-bordered" id="estimate-product">
      <colgroup>
          <col width="1.5%"/>
          <col width="12%"/>
      </colgroup>
      
      <tr>
        <td class="text-center active">거래메모</td>
        <td class="text-center">
          <textarea class="form-control" name="memo" id="memo" cols="30" rows="4"><?php echo $cSalesInfo['memo'] ?></textarea>
        </td>
      </tr>
    </table>

    <!-- 하단 -->
    <div class="bill-footer">
      <div class="text-left" style="width:30%">
        <!-- 저장/출력/인쇄 -->
        <!-- <button type="button" class="btn btn-default estimate-btn" id="consignment-save">저장</button> -->
      </div>
      <div class="text-center" style="width:40%">
        <!-- 발송 -->
        &nbsp;
      </div>
      <div class="text-right" style="width:30%">
        <!-- 결제,주문, 닫기,취소 -->
        <?php if(empty($_REQUEST['salesID'])):?>
           <button type="submit" id="submitBtn" class="btn btn-primary">등록</button>
        <?php else:?>
           <button type="submit" id="submitBtn" class="btn btn-primary">수정</button>
        <?php endif;?>
        
        <button type="button" class="btn btn-default estimate-btn" id="consignment-cancel" onclick="window.close();">닫기</button>
      </div>
    </div>

  </div>
</form>
</body>
</html>