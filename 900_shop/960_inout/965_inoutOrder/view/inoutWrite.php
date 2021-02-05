
<style>
    /* .selectpicker{

    }
    .abc {max-width : 100% !important}
    .bootstrap-select  { max-width: 90% !important; margin : 0 !important; padding : 0 !important; }
    .bootstrap-select.btn-group .dropdown-toggle .filter-option{
      width : 88%;
    }
    .bootstrap-select>.dropdown-toggle{
      padding-right:0px !important
    } */
    /* .bootstrap-select  { max-width: 200px; }
    .productNameColumn{
      max-width: 200px;
    } */
</style>

<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/script.html";
?>
<script src="/js/setProduct.js"></script>
<form id="editForm">
  <input type="hidden" name="orderID" value="<?php echo $orderID?>"/>
  <input type="hidden" name="stockOrderType" value="<?php echo $stockOrderTypeCode?>"/>
  
<!-- 테이블 -->
  <div class="container-fluid shop-estimate" style="width: 794px">
    <div class="estimate-top">
      <h2 class="text-center bill-title consignment-bill-title">발주서</h2>
      <div class="estimate-top-left"></div>
      <div class="estimate-top-right">
        <input type="hidden" id="stockOrderTypeCode" value="<?php echo $stockOrderTypeCode ?>"/>
        발주구분 : <?php echo $stockOrderTypeName ?>
      </div>
    </div>


    <!-- 견적사업자정보 -->
    <table class="table table-bordered" id="estimate-bizinfo">
      <colgroup>
        <col width="1%"/>
        <col width="15%"/>
        <col width="1%"/>
        <col width="3%"/>
        <col width="6%"/>
        <col width="3%"/>
        <col width="6%"/>
      </colgroup>
      <tbody>
        <tr>
          <td class="text-center active" rowspan="5">매<br>입<br>처</td>
          <td class="text-left recipient-info" rowspan="5">
            <div>
              <span>상호 :</span> 
              <?php if(empty($orderID)):?>
                <input type="text" class="form-control" id="PURCHASE_NM" name="PURCHASE_NM" value="<?php echo $partnerArray['companyName'] ?>" placeholder="입력 후 선택"/>
                
                <script>
                  const partnerListArray = <?php echo json_encode($partnerListArray) ?>;
                  
                  $("#PURCHASE_NM").autocomplete({
                      minLength : 0,
                      source: partnerListArray.map((data, index) => {
                          return {
                              id : data.partnerID,
                              value : data.companyName
                          };
                      }),
                      focus: function(event, ui){ 
                          return false;
                      },
                      select: function(event, ui) {
                          $('#PURCHASE_NM').val(ui.item.value);
                          location.replace("?partnerID="+ui.item.id);
                          return false;
                      }
                  }).on('focus', function(){ 
                      $(this).autocomplete("search"); 
                  });
                </script>
              <?php else:?>
                <?php echo $partnerArray['companyName'] ?>
              <?php endif;?>
            </div>

            <?php if(!empty($partnerID)):?>
              <input type="hidden" id="partnerID" name="partnerID" value="<?php echo $partnerID ?>"/>
              <div>
                <span>전화번호 :</span> 
                <?php echo setPhoneFormat($partnerArray['phoneNum'])  ?>
              </div>
              <div>
                <span>팩스번호 :</span> 
                <?php echo empty($partnerArray['faxNum']) ? "-" :  setPhoneFormat($partnerArray['faxNum']) ?>
                
              </div>
              <div>
                <span>담당자 :</span> 
                <?php echo $partnerArray['chargeName'] ?>
                
                
              </div>
              <br>
              <div>아래와 같이 발주합니다.</div>
            <?php endif;?>
           
          </td>
          <td class="text-center active" rowspan="5">발<br>주<br>처</td>
          <td class="text-center active">상호</td>
          <td class="text-center" colspan="3">
            <?php echo $companyInfo['companyName']?>
          </td>
        </tr>
        <tr>
          <td class="text-center active">주 소</td>
          <td class="text-center address_area" colspan="3">
            <?php echo $companyInfo['Address1']." ".$companyInfo['Address2']?>
          </td>
        </tr>
        <tr>
          <td class="text-center active">전화번호</td>
          <td class="text-center">
            <?php echo setPhoneFormat($companyInfo['phoneNum'])?>
          </td>
          <td class="text-center active">팩스번호</td>
          <td class="text-center">
            <?php echo setPhoneFormat($companyInfo['faxNum'])?>
          </td>
        </tr>
        <tr>
          <td class="text-center active">담당자</td>
          <td class="text-center">
            <input type="text" class="form-control" id="fromChargeName" name="fromChargeName" placeholder="입력" value="<?php echo  $companyInfo['fromChargeName'] ?>" required />
          </td>
          <td class="text-center active">담당자<br>휴대폰</td>
          <td class="text-center">
          <input type="text" class="form-control"  onkeyup="isNumber(event)"
              id="fromChargePhoneNum" name="fromChargePhoneNum" placeholder="-없이 입력" value="<?php echo  $companyInfo['fromChargePhoneNum'] ?>" required />
          </td>
        </tr>
      </tbody>
    </table>

    <?php if(empty($partnerID)):?>
      <div class="inout-text">해당 매입처를 먼저 선택해주세요.</div>
    <?php elseif(empty($productArray)):?>
      <div class="inout-text">해당 매입처에 등록된 상품이 없습니다.</div>
    <?php else:?>
      <table class="table table-bordered">
          <colgroup>
              <col width="1.4%"/>
              <col width="4%"/>
              <col width="1.5%"/>
              <col width="4%"/>
            </colgroup>
          <tr>
              <td class="text-center active">발주일</td>
              <td class="text-center calendar-icon">
                  <input type="text" class="form-control" id="ORDER_DT" name="ORDER_DT" autocomplete="off" placeholder="0000-00-00" required>
                  <span class="glyphicon glyphicon-calendar search-glypicon"></span> 
                
              </td>
              <td class="text-center active">납기일</td>
              <td class="text-center calendar-icon">
                  <input type="text" class="form-control" id="DELIVERY_DT" name="DELIVERY_DT" autocomplete="off" placeholder="0000-00-00" required>
                  <span class="glyphicon glyphicon-calendar search-glypicon"></span> 
                  
              </td>
          </tr>
      </table>

      
        
      
      <!-- 상품정보 -->
      <?php if(empty($orderID)):?>
        <div class="text-right"><button type="button" class="btn btn-default option_add_btn" onclick="addItemTable()">행 추가</button></div>
      <?php endif;?>
      
      <table class="table table-bordered" id="estimate-product">
        <colgroup>
          <col width="23.5%"/>
          <col width="8.5%"/>
          <col width="8.5%"/>
          <col width="7%"/>
          <col width="12.7%"/>
          <col width="11.2%"/>
          <col width="12.5%"/>
          <col width="10.1%"/>
          <col width="6%"/>

        </colgroup>
        <thead>
          <thead>
            <tr>
              <th class="text-center active" colspan="2">합계금액</th>
              <th class="text-center all-total-price" colspan="8" ><div id="hangleTotalPrice"></div><span style="font-weight: normal;">(VAT포함)</span></th>
            </tr>
          </thead>
        </thead>
        <tbody id="itemDataTable">
          <tr class="active">
            <td class="text-center" id="productTable">품명</td>
            <td class="text-center" id="unitTable">규격</td>
            <td class="text-center">단가</td>
            <td class="text-center">수량</td>
            <td class="text-center">공급가액</td>
            <td class="text-center">세액</td>
            <td class="text-center">합계액</td>
            <td class="text-center">비고</td>
            <td class="text-center">삭제</td>
          </tr>
          <?php if(empty($orderID)):?>
            <?php 
              echo "<script>
                      let productTableIdx = 0;
                      productList = ".json_encode($productArray).";
                      document.write(setProductTable());
                    </script>"; 
            ?>
            <!-- <script>
                $('.productColumn').css('max-width',210);
                $('.productColumn').css('width',210);
            </script> -->
          <?php else:?>
            <?php if(empty($orderInfo)):?>
              해당 매입처에서 주문한 상품이 존재하지 않습니다.
            <?php else:?>
              <?php
                echo "<script>let productTableIdx = ".count($orderInfo).";</script>"; 
                $totalPriceWithoutVAT = 0;
                $totalPrice = 0;
                $totalCnt = 0;
                $totalUnitPrice = 0;
                $totalVAT = 0;
                foreach($orderInfo as $index => $item){
                  $totalPriceWithoutVAT += $item['unitPrice']+$item['optionPrice'];
                  $totalVAT += ($item['unitPriceVAT']+$item['optionPriceVAT'])*$item['quantity'];
                  $totalUnitPrice += ($item['unitPrice']+$item['optionPrice'])*$item['quantity'];
                  $totalCnt += $item['quantity'];
                  $totalPrice += ($item['unitPriceVAT']+$item['optionPriceVAT'])*$item['quantity'] + ($item['unitPrice']+$item['optionPrice'])*$item['quantity'];
              ?>
              <tr>
                  <td class="text-left">
                  <?php echo $item['itemName']?>
                  <?php
                      if(!empty($item['optionName'])){
                  ?>
                      <span class="bill-goods-option"> 옵션: <?php echo $item['optionName']?>(<?php echo number_format($item['optionPrice']+$item['optionPriceVAT']) ?>원)</span>
                  <?php
                    }
                  ?>
                    <input type="hidden" id="item_<?php echo $index ?>" name="itemID[]" value="<?php echo $item['itemID'] ?>"/>
                  </td>
                  <td class="text-center">
                    <?php echo $item['goodsSpec'] ?>
                  </td>
                  <td class="text-right">
                    <?php echo number_format($item['unitPrice']+$item['optionPrice'])  ?>
                    <input type="hidden" name="unitPrice[]" value="<?php echo $item['unitPriceVAT']+$item['unitPrice']?>"/>
                    <input type="hidden" name="optionPrice[]" value="<?php echo $item['optionPrice']+$item['optionPriceVAT']?>"/>
                    <input type="hidden" name="optionID[]" value="<?php echo $item['optionID']?>"/>
                  </td>
                  <td class="text-right">
                    <?php echo $item['quantity'] ?>
                    <input type="hidden" id="cnt_<?php echo $index ?>" name="quantity[]" value="<?php echo $item['quantity']?>"/>
                  </td>
                  <td class="text-right">
                    <?php echo number_format(($item['unitPrice']+$item['optionPrice'])*$item['quantity'])?>
                  </td>
                  <td class="text-right">
                     <?php echo number_format(($item['unitPriceVAT']+$item['optionPriceVAT'])*$item['quantity']) ?>
                  </td>
                  <td class="text-right">
                      <?php echo number_format(($item['unitPriceVAT']+$item['optionPriceVAT'])*$item['quantity'] + ($item['unitPrice']+$item['optionPrice'])*$item['quantity']) ?>
                  </td>
                  <td class="text-left">
                     <input type="hidden" name="itemMemo[]" value="<?php echo ""?>"/>
                  </td>
                  <td class="text-center">
                  </td>
                </tr>
              <?php
                }  
              ?>
              <script>
                  const hangelPrice = "<?php echo number2hangul($totalPrice)?>" ;
                  $('#hangleTotalPrice').html(hangelPrice+" 원 (₩<?php echo number_format($totalPrice)?>) ");
              </script>
            <?php endif;?>
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

      <?php if(!empty($_REQUEST['itemID'])):?>
        <script>
          $('select[name^=itemID]').val('<?php echo $_REQUEST['itemID'] ?>');
          $('select[name^=itemID]').trigger('change');
        </script>
      <?php endif;?>

      <table class="table table-bordered" id="estimate-product">
        <colgroup>
            <col width="1.5%"/>
            <col width="4%"/>
            <col width="1.63%"/>
            <col width="4%"/>
        </colgroup>
        
        <tr>
          <td class="text-center active">배송방법</td>
            <td class="text-center">
              <select class="form-control" id="IN_DELIVERY_WAY" name="IN_DELIVERY_WAY" required>
                <?php echo getOption($this->optionArray['EC_DELIVERY_METHOD'], "" , "") ?>
              </select> 
          </td>
          <td class="text-center active">배송비</td>
          <td class="text-center calendar-icon">
                <div id="anotherContainer" >
                  <span id="deliveryFeePriceText">0</span>원
                  <input type="hidden" class="form-control" id="deliveryFeePrice" name="deliveryFeePrice" value="0" />
              </div>
          </td>
        </tr>
        <tr>
          <td class="text-center active">비고</td>
          <td class="text-center">
            <textarea class="form-control" name="IN_ETC" id="IN_ETC" cols="30" rows="4"></textarea>
          </td>
          <td class="text-center active">최종 결제금액</td>
          <td class="text-center calendar-icon" >
            <span id="PAYMENT_PRICE_WRITE"><?php if(!empty($totalPrice))  echo number_format($totalPrice); ?></span>
          </td>
        </tr>
      </table>

      <table class="table table-bordered" id="estimate-etc">
        <colgroup>
          <col width="1%"/>
          <col width="2%"/>
          <col width="10%"/>
          <col width="4%"/>
          <col width="10%"/>
        </colgroup>
        <tbody>
          <tr>
              <td class="text-center active" rowspan="6">입<br>고<br>처</td>
              <td class="text-center active">상호</td>
              <td class="text-center" colspan="3">
                <?php if(empty($orderID)):?>
                  <input type="text" class="form-control" id="IN_NAME" name="IN_NAME" placeholder="입력" value="<?php echo  $shipName ?>" required />
                <?php else:?>
                    <?php echo  $shipName ?>
                    <input type="hidden" name="IN_NAME" value="<?php echo  $shipName ?>" />
                <?php endif;?> 
              </td>
          </tr>
          <tr>
              <td class="text-center active">주소</td>
              <td class="text-center address_area" colspan="3">
                <?php if(empty($orderID)):?>
                  <div class="row">
                      <div class="col-md-6">
                        <input type="text" class="form-control" id="IN_ZIP" name="IN_ZIP" value="<?php echo  $shipZipCode ?>" readonly required />
                      </div>
                      <button type="button" class="btn btn-default" onclick="execPostCode()">주소검색</button>
                  </div>
                  <div class="row">  
                      <div class="col-md-12">
                        <input type="text" class="form-control" id="IN_ADDRESS1_AES" name="IN_ADDRESS1_AES" value="<?php echo  $shipAddr1 ?>" readonly required/>
                    </div>
                  </div>
                  <div class="row">
                      <div class="col-md-12">
                        <input type="text" class="form-control" id="IN_ADDRESS2_AES" name="IN_ADDRESS2_AES" value="<?php echo  $shipAddr2 ?>" placeholder="상세주소"/>
                    </div>
                  </div>
                <?php else:?>
                  <?php echo  $shipAddr1." ".$shipAddr2 ?>
                  <input type="hidden" name="IN_ZIP" value="<?php echo  $shipZipCode ?>" />
                  <input type="hidden" name="IN_ADDRESS1_AES" value="<?php echo  $shipAddr1 ?>" />
                  <input type="hidden" name="IN_ADDRESS2_AES" value="<?php echo  $shipAddr2 ?>" />
                <?php endif;?>
                
              </td>
          </tr>
          <tr>
              <td class="text-center active">담당자</td>
              <td class="text-center">
                <?php if(empty($orderID)):?>
                  <input type="text" class="form-control" id="IN_FAO_NM" name="IN_FAO_NM" placeholder="입력" value="<?php echo  $shipChargeName ?>" required />
                <?php else:?>
                  <?php echo  $shipChargeName ?>
                  <input type="hidden" name="IN_FAO_NM" value="<?php echo  $shipChargeName ?>" />
                <?php endif;?> 
                  
              </td>
              <td class="text-center active">담당자 휴대폰</td>
              <td class="text-center">
                <?php if(empty($orderID)):?>
                  <input type="text" class="form-control" onkeyup="isNumber(event)" value="<?php echo  $shipChargePhoneNum ?>"
                    id="IN_FAO_PHONE" name="IN_FAO_PHONE" placeholder="'-'를 제외한 숫자만 입력" required/>
                <?php else:?>
                  <?php echo  $shipChargePhoneNum ?>
                  <input type="hidden" name="IN_FAO_PHONE" value="<?php echo  $shipChargePhoneNum ?>" />
                <?php endif;?> 
                  
              </td>
          </tr>
          
        </tbody>
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
          <button type="submit" id="submitBtn" class="btn btn-primary">작성</button>
          <button type="button" class="btn btn-default estimate-btn" id="consignment-cancel" onclick="window.close();">닫기</button>
        </div>
      </div>

    </div>
  </form>
  <form id="deliveryFeeForm"></form>
<?php endif;?> 
    
</body>
<script src="https://t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script>
    function getDeliveryFeePrice(selectedVal){
      let deliveryPrice = 0;
      let isSelectedItem = true;
      
      $('[name^=itemID]').each(function(){
          if(empty($(this).val())){
            isSelectedItem = false;
          }
      });

      if(isSelectedItem){
        let form = $("#deliveryFeeForm")[0];
        let formData = new FormData(form);
        formData.append('REQ_MODE', CASE_READ);
        formData.append('deliveryMethodCode', EC_DELIVERY_METHOD);

        $('[name^=itemID]').each(function(){
          const id = $(this).attr("id").split("_")[1];
          formData.append('itemID[]', $(this).val());
          formData.append('quantity[]', $('#cnt_'+id).val());
          
        });
        
        callFormAjax(ERP_CONTROLLER_URL, 'POST', formData).then((result)=>{
            if(isJson(result)){
              const res = JSON.parse(result);
             
              if(res.status == OK){
                $('#deliveryFeePriceText').html(res.data.deliveryPrice);
                $('#deliveryFeePrice').val(res.data.deliveryPrice);
              }else{
                alert(ERROR_MSG_1100);
                $('#deliveryFeePrice').val(0);
              }
            }else{
                alert(ERROR_MSG_1200);
                $('#deliveryFeePrice').val(0);
            }
            setTotalOrderPrice();
          }); 
      }  
    }

    function isValidation(){

      if(($('#IN_ZIP').is(":visible") && empty($('#IN_ZIP').val())) || ($('#IN_ADDRESS1_AES').is(":visible") && empty($('#IN_ADDRESS1_AES').val()))){
        alert("주소를 입력해주세요");
        execPostCode();

        return false;
      }else{
        return true;
      }  
    }

    function setTotalOrderPrice(){
      let deliveryPrice = Number($('#totalItemPriceTable').html().replace(/,/g,''));
      $('#PAYMENT_PRICE_WRITE').html(numberWithCommas(deliveryPrice+Number($('#deliveryFeePrice').val())));
    }

    $(document).ready(function(){
      $('#editForm').submit(function(e){
          e.preventDefault();

          if(isValidation()){
            confirmSwalTwoButton("발주 하시겠습니까?",'확인', '취소', false ,()=>{
              setData(CASE_CREATE);
            }, '');
          }  
      });

      $('#IN_DELIVERY_WAY').change(function(e){
          if($(this).val() == EC_DELIVERY_METHOD){
            getDeliveryFeePrice();
          }else{
            $('#deliveryFeePriceText').html(0);
            $('#deliveryFeePrice').val(0);

            setTotalOrderPrice();
          }  
      });
    });
   
    execPostCode = () => {
      new daum.Postcode({
          oncomplete: function(data) {
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

            $("#IN_ZIP").val(data.zonecode);
            $("#IN_ADDRESS1_AES").val(fullAddr);
            $("#IN_ADDRESS2_AES").val('');
            $("#IN_ADDRESS2_AES").focus();
            
          }
      }).open();
    }
</script>
</html>