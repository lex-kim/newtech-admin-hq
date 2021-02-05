<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/script.html";
?>
<div class="modal-add container-fluid" id="add-new">

  <form method="post" name="faoForm" id="faoForm">
      <!-- modal 상단 -->
      <div class="modal-header">
        <h4 class="modal-title">주문서 작성</h4>  
      </div>

      <!-- ?php <	require "../941_orderList/orderSheet.html"; ?> 요걸로 불러와서 쓰셔도됩니다 ! 
            아래는 뉴텍 퍼블 확인용... 복붙입니다. 발주서 작성/조회가 안나오면되서 ....-->
      <div class="modal-body"> 
        <!-- 공업사담당 -->
        <div class="gw-info">
          <div class="gw-info-left">
            <h5 class="modal-title" style="margin-bottom: 15px;">주문정보</h5>  

            <!-- 배송방법 -->
            <div class="row">
              <div class="col-md-2"><label for="PAYMENT">배송방법</label></div>
              <div class="col-md-8">
                <?php echo getRadio($this->optionArray['EC_DELIVERY_METHOD'], "deliveryMethodCode", $orderInfo['deliveryMethodCode']) ?>
              </div>
            </div>
      
            <!-- 배송지정보 -->
            <div class="row">
              <div class="col-md-2"><label for="DELIVERY">배송지정보</label></div>
              <div class="form-inline col-md-8">
                <label class="radio-inline" for="delivery-new"><input type="radio" name="DELIVERY" id="delivery-new" value="" checked>새로입력</label>
                <label class="radio-inline" for="delivery-recent"><input type="radio" name="DELIVERY" id="delivery-recent"  value="<?php echo GET_ORDER_ADDRESS_RECENT ?>">최근사용</label>
                <label class="radio-inline" for="delivery-biz-info"><input type="radio" name="DELIVERY" id="delivery-biz-info" value="<?php echo GET_ORDER_ADDRESS_COMPANY ?>">사업자정보</label>
                
              </div>
            </div>

            <!-- 성명 -->
            <div class="row">
            <div class="col-md-2"><label for="ORDER_NM">성명</label></div>
            <div class="col-md-5">
              <input type="text" class="form-control insuCharge" id="ORDER_NM" name="ORDER_NM" placeholder="입력" required />
            </div>
            </div>

            <!-- 주소 -->
            <div class="row">
              <div class="col-md-2"><label for="ORDER_ADDRESS">주소</label></div>
              <div class="col-md-5">
                <input type="text" class="form-control" id="ORDER_ZIP" name="ORDER_ZIP" readonly required />
              </div>
              <button type="button" class="btn btn-default" onclick="execNewPostCode('MIT')">주소검색</button>
            </div>
            <div class="row">  
              <div class="col-md-2"><label for="ORDER_ADDRESS1"></label></div>
              <div class="col-md-8">
                <input type="text" class="form-control" id="ORDER_ADDRESS1_AES" name="ORDER_ADDRESS1_AES" readonly required/>
              </div>
            </div>
            <div class="row">
              <div class="col-md-2"><label for="ORDER_ADDRESS2"></label></div>
              <div class="col-md-8">
                <input type="text" class="form-control" id="ORDER_ADDRESS2_AES" name="ORDER_ADDRESS2_AES" placeholder="나머지 주소" />
              </div>
            </div>

            <!-- 휴대폰번호 -->
            <div class="row">
              <div class="col-md-2"><label for="ORDER_PHONE">휴대폰번호</label></div>
              <div class="col-md-5">
                <input type="text" class="form-control insuCharge" 
                  onkeyup="isNumber(event)" id="ORDER_PHONE" name="ORDER_PHONE" placeholder="'-'를 제외한 숫자만 입력" required/>
              </div>
            </div>


          </div>

          <div class="gw-info-right">
            <h5 class="modal-title" style="margin-bottom: 15px;">공업사정보</h5>  

            <div class="container-fluid row">
              <table class="table table-bordered">
                <colgroup>
                  <col width="2%"/>
                  <col width="8%"/>
                </colgroup>
                <tbody>
                  <tr>
                    <td class="text-center active">공업사명</td>
                    <td class="text-left"><?php echo $garageInfo['garageName'] ?></td>
                  </tr>
                  <tr>
                    <td class="text-center active">사업자번호</td>
                    <td class="text-left"><?php echo bizNoFormat($garageInfo['companyRegID']) ?></td>
                  </tr>
                  <tr>
                    <td class="text-center active">대표자명</td>
                    <td class="text-left"><?php echo $garageInfo['ceoName'] ?></td>
                  </tr>
                  <tr>
                    <td class="text-center active">전화번호</td>
                    <td class="text-left"><?php echo setPhoneFormat($garageInfo['phoneNum'])?></td>
                  </tr>
                </tbody>
              </table>
            </div>

          </div>
        
        </div>
    
    
        <div class="borderTop"></div>   
        
        <h5 class="modal-title" style="margin-bottom: 15px;">주문상품</h5>
    
          <div class="container-fluid row">
            <table class="table table-bordered">
              <thead>
                <tr class="active">
                  <th class="text-center">순번</th>
                  <th class="text-center">매입구분</th>
                  <th class="text-center">매입처</th>
                  <th class="text-center">연락처<br>(대표번호전화)</th>
                  <th class="text-center">팩스번호</th>
                  <th class="text-center">담당자</th>
                  <th class="text-center">담당자휴대폰연락처</th>
                  <th class="text-center">품명</th>
                  <th class="text-center">규격</th>
                  <th class="text-center">단가</th>
                  <th class="text-center">수량</th>
                  <th class="text-center">공급가</th>
                  <th class="text-center">세액</th>
                  <th class="text-center">합계</th>
                </tr>
              </thead>
              <tbody id="dataTable">
                  <?php
                    foreach($orderInfo['quotationDetails'] as $idx => $item){
                      if($item['soldOutYN'] == "Y"){
                        $isSoldout = "Y";
                      }else{
                        if($item['partnerBuyerTypeCode'] == BUYER_TYPE_CODE_CON){                 // 위탁상품
                          $isSoldout = "N";
                        }else{
                          if($item['stockRemains'] >= $item['quantity']){
                            $isSoldout = "N";
                          }else{
                            $isSoldout = "Y";
                          }
                        }
                      } 
                  ?>
                    <input type="hidden" class="itemText" value="<?php echo $isSoldout?>" />
                    <tr>
                      <td class="text-center"><?php echo count($orderInfo['quotationDetails'])-$idx?></td>
                      <td class="text-center"><?php echo $item['partnerBuyerType']?></td>
                      <td class="text-center"><?php echo $item['partnerName']?></td>
                      <td class="text-center"><?php echo setPhoneFormat($item['phoneNum'])?></td>
                      <td class="text-center"><?php echo setPhoneFormat($item['faxNum'])?></td>
                      <td class="text-center"><?php echo $item['chargeName']?></td>
                      <td class="text-center">
                          <?php echo setPhoneFormat($item['chargePhoneNum'])?><br>
                          <?php echo setPhoneFormat($item['chargeDirectPhoneNum'])?>
                      </td>
                      <td class="text-left" <?php if($isSoldout == "Y") echo "style=color:red"; ?>>
                        <span class="cItemName" id="itemName_<?php echo $idx?>"><?php echo $item['itemName']?></span>
                        <?php
                            if($isSoldout == "Y"){
                        ?>
                            <span><img src="../../../images/soldout.png"/></span>
                        <?php
                          }
                        ?>
                        <?php
                            if(!empty($item['optionName'])){
                        ?>
                            <span class="bill-goods-option"> 옵션: <?php echo $item['optionName']?>(<?php echo number_format($item['optionPrice']+$item['optionPriceVAT']) ?>원)</span>
                        <?php
                          }
                        ?>
                      </td>
                      <td class="text-right"><?php echo $item['goodsSpec']?></td>
                      <td class="text-right"><?php echo number_format($item['unitPrice']+($item['optionPrice'])) ?></td>
                      <td class="text-right"><?php echo $item['quantity']?></td>
                      <td class="text-right"><?php echo number_format(($item['unitPrice']+$item['optionPrice'])*$item['quantity']) ?></td>
                      <td class="text-right"><?php echo number_format(($item['optionPriceVAT']+$item['unitPriceVAT'])*$item['quantity']) ?></td>
                      <td class="text-right">
                        <?php echo number_format(($item['unitPrice']+$item['optionPrice'])*$item['quantity']+(($item['optionPriceVAT']+$item['unitPriceVAT'])*$item['quantity'])) ?>
                      </td>
                    </tr>
                  <?php
                    }
                  ?>
              </tbody>
            </table>
          </div>
         
          <div class="borderTop"></div>   
    
          <div class="payment_area">
            <div class="payment_left">
    
              <h5 class="modal-title" style="margin-bottom: 15px;">결제방법</h5>
              <!-- 결제방법 -->
              <div class="row">
                <div class="col-md-2"><label for="PAYMENT">결제방법</label></div>
                <div class="col-md-8">
                  <?php echo getRadio($this->optionArray['EC_PAYMENT_TYPE'], "PAYMENT", $orderInfo['paymentCode']) ?>
                </div>
              </div>
              <!-- 배송지정보 -->
              <div class="row">
                <?php
                  require $_SERVER['DOCUMENT_ROOT']."/include/billForm.php";
                ?>
                <script>
                    $('.hiddenTrack').hide();
                </script>
                <?php
                      if(($orderInfo['paymentCode'] != EC_PAYMENT_TYPE_DEPOSIT) && !empty($orderInfo['paymentCode'])){
                ?>
                    <script>
                        var radioBoxes = document.getElementsByName('EVIDENCE');
                        for(var i=0; i<radioBoxes.length; i++){
                              radioBoxes[i].disabled = true;
                        }
                       
                    </script>
                <?php
                  }
                ?>
                </div>
                
              </div>
    
            </div>
            <div class="payment_right">
              <h5 class="modal-title" style="margin-bottom: 15px;">주문금액</h5>
                <div class="container-fluid row">
                  <table class="table table-bordered">
                    <colgroup>
                      <col width="2%"/>
                      <col width="8%"/>
                    </colgroup>
                    <tbody>
                      <tr>
                        <td class="text-center active">주문금액</td>
                        <td class="text-right" >
                          <span id="orderPrice"><?php echo number_format($orderInfo['totalSum']) ?></span>원</td>
                      </tr>
                      <tr>
                        <td class="text-center active">배송비</td>
                        <td class="text-right" >
                          <span id="deliveryPrice"><?php echo number_format($orderInfo['deliveryFee']) ?></span>원</td>
                      </tr>
                      <tr>
                        <td class="text-center active">포인트</td>
                        <td class="text-right point-area">
                          <div>사용가능 포인트 : <?php echo number_format($pointInfo['pointBalance'])  ?> P</div>
                          <div>* <?php echo number_format($pointInfo['pointRedeemMin'])  ?>  이상부터 사용가능</div>
                          <div class="point-input">
                            <div>
                              <input type="text" onkeyup="isNumber(event)"
                                id="usePoint" name="usePoint" class="form-control text-right" placeholder="사용할 포인트 입력"/>
                            </div>
                            <div>
                              <span>P</span>
                            </div>
                          </div>
                          
                        </td>
                      </tr>
                      <tr>
                        <td class="text-center active text-bold">최종결제금액</td>
                        <td class="text-right text-bold" >
                          <span id="totalPrice"><?php echo number_format($orderInfo['totalSum']+$orderInfo['deliveryFee']) ?></span>원</td>
                      </tr>
                      <tr>
                        <td class="text-center active">적립예정포인트</td>
                        <td class="text-right" ><span id="totalSavePoint"><?php echo number_format($savePoint)?></span> P</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
            </div>
    
          </div>
        
        
    
      </div>

      <!-- 하단 -->
      <div class="modal-footer bill-footer">
        <div class="text-left" style="width:30%">
          <!-- 저장/출력/인쇄 -->
        </div>
        <div class="text-center" style="width:40%">
          <!-- 발송 -->
          &nbsp;
          <!-- <button type="submit" id="submitBtn" class="btn btn-default">결제정보 전송</button> -->
        </div>
        <div class="text-right" style="width:30%">
          <!-- 결제,주문, 닫기,취소 -->
          <button type="submit" id="submitBtn" class="btn btn-primary">주문하기</button>
          <button type="button" class="btn btn-default" onclick="window.close();">닫기</button>
        </div>
      </div>

  </form>
</div>
<!-- footer.html -->
<?php require $_SERVER['DOCUMENT_ROOT']."/include/footerInfo.html"; ?>
</body>
</html>
<script src="https://t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script>
    $('#usePoint').change(function(e){
       const pointRedeemMinValue = Number("<?php echo $pointInfo['pointRedeemMin'] ?>");
       const pointValue = Number("<?php echo $pointInfo['pointBalance'] ?>");
       const usePointValue = Number(empty($(this).val()) ? 0 : $(this).val());
       const totalPriceValue = Number("<?php echo $orderInfo['totalSum'] ?>");

       if((usePointValue < pointRedeemMinValue) && usePointValue > 0 ){
          alert(numberWithCommas(pointRedeemMinValue) +"원 이상부터 사용가능합니다.");
          $(this).val("");
          $(this).focus();
       }else if((usePointValue > pointValue) && usePointValue > 0){
          alert("보유포인트 이상을 사용하실 수 없습니다.");
          $(this).val("");
          $(this).focus();
       }else if(totalPriceValue < usePointValue){
          alert("결제금액 이상을 사용하실 수 없습니다.");
          $(this).val("");
          $(this).focus();
       }else{
           setTotal();
       }
    });

    
    /** 배송방법 체인지 이벤트 */  
    $('input:radio[name=deliveryMethodCode]').change(function(e){
        if($(this).val() == EC_DELIVERY_METHOD){
          $('#deliveryPrice').html("<?=number_format($orderInfo['deliveryFee']) ?>");
        }else{
          $('#deliveryPrice').html(0);
        }  
        setTotal();
    });

    $('input:radio[name=PAYMENT]').change(function(e){
        if($(this).val() == EC_PAYMENT_TYPE_DEPOSIT){
          $('input:radio[name=EVIDENCE]').attr("disabled",false);
        }else{
          $('input:radio[name=EVIDENCE][value='+EC_BILL_TYPE_NO+']').prop("checked",true);
          $('input:radio[name=EVIDENCE]').attr("disabled",true);
          $(".cEVIDENCE").hide();
        }
        $('input:radio[name=EVIDENCE]:checked').trigger('click');
    });
    
    function setTotal(){
        var totalPriceValue = Number("<?php echo $orderInfo['totalSum'] ?>");
        var usePointValue = Number(empty($('#usePoint').val()) ? 0 : $('#usePoint').val());
        var savePointValue = Number("<?php echo $savePoint ?>");
        var usePriceValue = totalPriceValue - usePointValue;
        var resultSavePoint = Math.floor(usePriceValue*savePointValue/totalPriceValue);
        var deliveryFee = Number($('#deliveryPrice').html().replace(/,/g,""));

        $('#totalPrice').html(numberWithCommas(usePriceValue+deliveryFee));
        $('#totalSavePoint').html(numberWithCommas(resultSavePoint));
    }

    function resetOrderInfo(){
        $('#ORDER_NM').val("");
        $('#ORDER_ZIP').val("");
        $('#ORDER_ADDRESS1_AES').val("");
        $('#ORDER_ADDRESS2_AES').val("");
        $('#ORDER_PHONE').val("");
    }

    $('input[name=DELIVERY]').change(function(e){
        const value = $(this).val();
        
        if(empty(value)){ // 새로입력
          resetOrderInfo();
        }else{
          const data  = {
            REQ_MODE : GET_ORDER_ADDRESS_INFO,
            garageID : "<?php echo $garageID ?>",
            reqMode : value
          };
          callAjax(ERP_CONTROLLER_URL, 'POST', data).then((result)=>{
              if(isJson(result)){
                const res = JSON.parse(result);
                if(res.status == OK){
                  const data = res.data;
                  if(data != HAVE_NO_DATA){
                    $('#ORDER_NM').val(data.orderName);
                    $('#ORDER_ZIP').val(data.zipcode);
                    $('#ORDER_ADDRESS1_AES').val(data.address1);
                    $('#ORDER_ADDRESS2_AES').val(data.address2);
                    $('#ORDER_PHONE').val(data.orderPhoneNum);
                  }else{
                    resetOrderInfo();
                  }
                }else{
                  alert(ERROR_MSG_1100);
                }
              }else{
                  alert(ERROR_MSG_1200);
              } 
          }); 
        }
    });

    execNewPostCode = (objId) => {
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

            $("#ORDER_ZIP").val(data.zonecode);
            $("#ORDER_ADDRESS1_AES").val(fullAddr);
            $("#ORDER_ADDRESS2_AES").focus();
            
          }
      }).open();
    }

    function setOrderData(){
      let form = $("#faoForm")[0];
      let formData = new FormData(form);
      formData.append('REQ_MODE', CASE_CREATE);
      formData.append('SET_TYPE',"Order");
      formData.append('quotationID', "<?php echo $quotationID ?>");
      formData.append('garageID', "<?php echo $garageID ?>");
      formData.append('orderPrice', $('#orderPrice').html().replace(/,/g,""));
      formData.append('deliveryPrice', $('#deliveryPrice').html().replace(/,/g,""));
      formData.append('pointRedeem', $('#usePoint').val().replace(/,/g,""));
      formData.append('totalPrice', $('#totalPrice').html().replace(/,/g,""));
      formData.append('earnPoint', $('#totalSavePoint').html().replace(/,/g,""));

      callFormAjax(ERP_CONTROLLER_URL, 'POST', formData).then((result)=>{
          if(isJson(result)){
            const res = JSON.parse(result);
            const data = res.data;
            if(res.status == OK){
              const orderID = data.orderID;
              
              alert("주문이 완료되었습니다.");
              if(!empty(opener.document.getElementById("parentUrl"))){
                if(opener.document.getElementById("parentUrl").value == "orderList.php"){
                  window.opener.getParentList();
                  opener.window.close();
                }
              }
              window.close();
              
              const url = '/900_shop/940_orderMngt/941_orderList/orderBill.php?orderID='+orderID;
              openWindowPopup(url, 'orderDetail', 1320, 860);
              
            }else{
              if(data.errorCode == ERROR_EC_DELIVERY_VIOLATION){
                alert(data.errorMsg);
              }else{
                alert(ERROR_MSG_1100);
              } 
            }
          }else{
              alert(ERROR_MSG_1200);
          }
      });
    }

    $('#faoForm').submit(function(e){
        e.preventDefault();

        if(isHaveSoldOutItem()){
          alert("품절된 상품이 존재합니다.");
        }else{
          confirmSwalTwoButton('주문서를 작성하시겠습니까?','확인','취소',false,function(){
            setOrderData();
          }); 
        }
         
    });

</script>

