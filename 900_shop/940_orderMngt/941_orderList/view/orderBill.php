
<style type="text/css" media="print">
  @page { 
    size: A4 landscape ;
    margin: 5mm 0 0 0;
  }
  @media print {
    body { 
      width: 29.7cm; height:205mm
    }
    .page {
      margin: 0;
      border: initial;
      width: initial;
      min-height: initial;
      box-shadow: initial;
      background: initial;
      page-break-after: always;

    }
  }
</style>
<body style="width: 29.7cm">
  <input type="hidden" id="printMode" value="l"/>
  <div class="container-fluid shop-estimate" >
    
    <div class="estimate-top">
      <div class="estimate-top-left">
        <div class="bill-barcode" id="barcodeContainer" >
          
        </div>
      </div>
      <div class="text-center" style="margin: 0 auto;">
        <h2 class="text-center bill-title consignment-bill-title">주문서</h2>
      </div>
      <div class="estimate-top-right" style="font-size:14px">주문일시 : <?php echo $orderInfo['writeDatetime'] ?></div>
    </div>

    <div class="half-area">
      <div class="half-left">
     
        <table class="table table-bordered">
          <colgroup>
            <col width="1%">
            <col width="5%">
          </colgroup>
          <tbody>
            <tr>
              <td class="text-center active">성명</td>
              <td class="text-left"><?php echo $orderInfo['orderName']?></td>
            </tr>
            <tr>
              <td class="text-center active">주소</td>
              <td class="text-left"><?php echo $orderInfo['address1']." ".$orderInfo['address2'] ?></td>
            </tr>
            <tr>
              <td class="text-center active">휴대폰번호</td>
              <td class="text-left"><?php echo setPhoneFormat($orderInfo['orderPhoneNum'])?></td>
            </tr>
            <tr>
            </tr>
          </tbody>
        </table>
        <table class="table table-bordered payment-table">
          <colgroup>
            <col width="1.1%">
            <col width="5%">
          </colgroup>
          <tbody>
            <tr>
              <td class="text-center active">결제방법</td>
              <td class="text-left">
                <?php echo $orderInfo['payment']?>
              </td>
            </tr>

            <?php if($orderInfo['billTypeCode'] != EC_BILL_TYPE_NO):?>
              <tr>
                <td class="text-center active">지출증빙</td>
                <td class="text-left">
                  <?php echo $orderInfo['billType'] ?>
                  <?php if($orderInfo['billTypeCode'] != EC_BILL_TYPE_NO && !empty($orderInfo['billTypeCode'] )) : ?>
                    <?php if(empty($orderInfo['billCashType'])) : ?>
                      (상호 : <?php echo $orderInfo['billCompanyName']?>, 사업자번호 : <?php echo bizNoFormat($orderInfo['billCompanyRegID'])?>)
                    <?php else : ?>
                      <?php if(!empty($orderInfo['billPhoneNum'])) : ?>
                        (개인 : <?php echo setPhoneFormat($orderInfo['billPhoneNum'])?>)
                      <?php else : ?>
                        (사업자 : <?php echo bizNoFormat($orderInfo['billCompanyRegID'])?>)
                      <?php endif; ?>
                    <?php endif; ?>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endif;?>

          </tbody>
        </table>
      </div>


      <div class="half-right">
        <table class="table table-bordered total-payment-table">
          <colgroup>
            <col width="2%">
            <col width="5%">
          </colgroup>
          <tbody>
            <tr>
              <td class="text-center active">주문금액</td>
              <td class="text-right"><?php echo number_format($orderInfo['orderPrice']) ?></td>
            </tr>
            <tr>
              <td class="text-center active">배송비</td>
              <td class="text-right"><?php echo number_format($orderInfo['deliveryPrice']) ?>원</td>
            </tr>
            <tr>
              <td class="text-center active">포인트사용금액</td>
              <td class="text-right">
                <?php
                  if($orderInfo['pointRedeem'] > 0){
                    echo "-";
                  }
                ?>
                <?php echo number_format($orderInfo['pointRedeem']) ?>P
              </td>
            </tr>
            <tr>
              <td class="text-center text-bold active">최종결제금액</td>
              <td class="text-right text-bold text-fz16"><?php echo number_format($orderInfo['totalPrice']) ?>원</td>
            </tr>
            <tr>
              <td class="text-center active">적립예정포인트</td>
              <td class="text-right"><?php echo number_format($orderInfo['earnPoint']) ?>P</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    
    
    <!-- 상품정보 -->
    <table class="table table-bordered" id="orderBill-product">
      <colgroup>
        <!-- <col width="1%"/>
        <col width="10%"/>
        <col width="5%"/>
        <col width="3%"/>
        <col width="5%"/> -->
      </colgroup>
      <thead>
        <tr class="border-double-bottom">
          <th class="text-center text-bold active" colspan="3">합계금액</th>
          <th class="text-center text-bold all-total-price" colspan="11"><?php echo number2hangul($orderInfo['totalPrice']) ?> 원 (￦ <?php echo number_format($orderInfo['totalPrice']) ?>) <span style="font-weight: normal;">(배송비,VAT포함)</span></th>
        </tr>
      </thead>
      <tbody>
        <tr class="active">
          <th class="text-center">순번</th>
          <th class="text-center">매입구분</th>
          <th class="text-center">매입처</th>
          <th class="text-center">연락처<br>(대표번호전화)</th>
          <th class="text-center">팩스번호</th>
          <th class="text-center">담당자</th>
          <th class="text-center">담당자휴대폰<br>연락처</th>
          <th class="text-center">품명</th>
          <th class="text-center">규격</th>
          <th class="text-center">단가</th>
          <th class="text-center">수량</th>
          <th class="text-center">공급가</th>
          <th class="text-center">세액</th>
          <th class="text-center">합계</th>
        </tr>
        <?php
          $totalUnitPrice = 0;
          $totalPrice = 0;
          $totalCnt = 0;
          $totalVAT = 0;
          foreach($orderInfo['orderDetails'] as $idx => $item){
            $totalUnitPrice += $item['unitPrice']+$item['optionPrice'];                       // 단가
            $totalCnt += $item['quantity'];                                                   // 수량
            $totalVAT += ($item['unitPriceVAT']+$item['optionPriceVAT'])*$item['quantity'];   // 세액
            $totalPrice += ($item['unitPrice']+$item['optionPrice']) * $item['quantity'];     // 공급가
        ?>
          <tr>
            <td class="text-center"><?php echo $idx+1 ?></td>
            <td class="text-center"><?php echo $item['partnerBuyerType']?></td>
            <td class="text-center"><?php echo $item['partnerName']?></td>
            <td class="text-center"><?php echo setPhoneFormat($item['phoneNum'])?></td>
            <td class="text-center"><?php echo setPhoneFormat($item['faxNum'])?></td>
            <td class="text-center"><?php echo $item['chargeName']?></td>
            <td class="text-center">
                <?php echo setPhoneFormat($item['chargePhoneNum'])?><br>
                <?php echo setPhoneFormat($item['chargeDirectPhoneNum'])?>
            </td>
            <td class="text-left">
              <?php echo $item['itemName']?>
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
            <td class="text-right"><?php echo number_format(($item['unitPrice']+($item['optionPrice']))*$item['quantity']) ?></td>
            <td class="text-right"><?php echo number_format(($item['unitPriceVAT']+$item['optionPriceVAT'])*$item['quantity']) ?></td>
            <td class="text-right">
              <?php echo number_format(($item['unitPrice']+($item['optionPrice']+$item['optionPriceVAT']))*$item['quantity']+($item['unitPriceVAT']*$item['quantity'])) ?>
            </td>
          </tr>
        <?php
          }
          for($i=0; $i<$remainItemCnt ; ++$i){
        ?>
            <tr>
              <td class="text-left"></td>
              <td class="text-center"></td>
              <td class="text-right"></td>
              <td class="text-right"></td>
              <td class="text-right"></td>
              <td class="text-right"></td>
              <td class="text-left"></td>
              <td class="text-center"></td>
              <td class="text-right"></td>
              <td class="text-right"></td>
              <td class="text-right"></td>
              <td class="text-right"></td>
              <td class="text-right"></td>
              <td class="text-right"></td>
            </tr>
        <?php
          }
        ?>
      </tbody>
      <tfoot>
        <tr class="border-double-top">
          <td class="text-center active" colspan="9">합계</td>
          <td class="text-right"><?php echo number_format($totalUnitPrice) ?></td>
          <td class="text-right"><?php echo $totalCnt ?></td>
          <td class="text-right"><?php echo number_format($totalPrice) ?></td>
          <td class="text-right"><?php echo number_format($totalVAT)?></td>
          <td class="text-right"><?php echo number_format($totalVAT + $totalPrice)?></td>
        </tr>
      </tfoot>
    </table>

    <!-- 하단 -->
    <!-- 하단 -->
    <div class="bill-footer">
      <div class="text-left" style="width:30%">
        <!-- 저장/출력/인쇄 -->
        <button type="button" class="btn btn-default" onclick="onPrint()">출력</button>
        <button type="button" class="btn btn-default" onclick="pdfDownload('<?php echo $orderID ?>')">PDF다운로드</button>
      </div>
      <div class="text-center" style="width:40%">
        <!-- 발송 -->
        &nbsp;
        <?php if($orderInfo['paymentCode'] == EC_PAYMENT_TYPE_CREDIT || $orderInfo['paymentCode'] == EC_PAYMENT_TYPE_TRANSFER):?>
          <button type="button" class="btn btn-primary" onclick="onClickSMSSend()">결제정보 전송</button>
        <?php endif;?>
      </div>
      <div class="text-right" style="width:30%">
        <!-- 결제,주문, 닫기,취소 -->
        <button type="button" class="btn btn-default" onclick="window.close();">닫기</button>
      </div>
    </div>

    
    <!-- 등록/수정 모달  -->
    <?php	require "./phoneNumModal.html"; ?>

  </div>
  
<!-- footer.html -->
<?php require $_SERVER['DOCUMENT_ROOT']."/include/footerInfo.html"; ?>
</body>
<!-- footer.html -->
<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/footerpopup.html";
?>
<script type="text/javascript" src="/js/jquery-barcode.js"></script>
<script>
    $(window).resize(function(){
        window.resizeTo(1200,860);
    });

    $(document).ready(function(){
        $('#phoneForm').submit(function(e){
            e.preventDefault();

            confirmSwalTwoButton(phoneFormat($('#SEND_PHONE_NUM').val())+'로  발송 하시겠습니까?','확인','취소',false,function(){
              const data = {
                REQ_MODE : CASE_CREATE,
                orderID : "<?=$orderID ?>",
                paymentCode : "<?=$orderInfo['paymentCode']?>",
                sendPhoneNum : $('#SEND_PHONE_NUM').val()
              }
              callAjax(ERP_CONTROLLER_URL, 'POST', data).then((result)=>{
                  if(isJson(result)){
                      const res = JSON.parse(result);
                      if(res.status == OK){
                        alert("발송되었습니다.");
                        $('#phoneNum-modal').modal('hide');
                      }else{
                        if(!empty(res.result)){
                          alert(res.result);
                        }else{
                          alert(ERROR_MSG_1100);
                        }  
                      }
                  }else{
                      alert(ERROR_MSG_1200);
                  } 
              }); 
            }); 
        });
    });

    $('#barcodeContainer').barcode("<?php echo $orderID ?>", "code128", {
        barWidth: 1, barHeight: 50, moduleSize: 5, showHRI: true, moduleSize: 5, addQuietZone: true, marginHRI: 5, bgColor: "#FFFFFF", color: "#000000", fontSize: 12, output: "css", posX: 0, posY: 0 
    });    
</script>