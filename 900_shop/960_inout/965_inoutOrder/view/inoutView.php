<style>
  @page { 
    size: A4 portrait ;
    margin: 5mm 0 0 0;
  }
  @media print {
    html, body {
      width: 210mm;
      height: 292mm;
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
<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/script.html";
?>
<body>
<!-- 뉴텍몰 nav -->


  <!-- 페이지 카테고리 -->
  <div class="container-fluid mb20px">
  </div>

  <!-- 테이블 -->
  
  <div class="container-fluid shop-estimate" style="width: 21cm">
    <div class="estimate-top">
      <h2 class="text-center bill-title consignment-bill-title">발주서</h2>
      <div class="estimate-top-left">발주번호 : <?php echo $stockOrderInfo['stockOrderID'] ?></div>
      <div class="estimate-top-right">발주구분 : <?php echo $stockOrderInfo['stockOrderType'] ?></div>
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
            <div class="recipient-info-biznm"><?php echo $stockOrderInfo['toName'] ?> 귀하</div>
            <div>전화번호 : <?php echo setPhoneFormat($stockOrderInfo['toPhoneNum']) ?></div>
            <div>팩스번호 : <?php echo setPhoneFormat($stockOrderInfo['toFaxNum']) ?></div>
            <div>담당자 : <?php echo $stockOrderInfo['toChargeName'] ?></div>
            <br>
            <div>아래와 같이 발주합니다.</div>
            

          </td>
          
          <td class="text-center active" rowspan="5">발<br>주<br>처</td>
          <td class="text-center active">상호</td>
          <td class="text-center" colspan="3" id="ORDER_NM_VIEW">
             <?php echo $stockOrderInfo['fromName'] ?>
          </td>
        </tr>
        <tr>
          <td class="text-center active">주 소</td>
          <td class="text-center address_area" colspan="3" id="PURCHASE_ADDRESS_VIEW">
            <?php echo $stockOrderInfo['fromAddress'] ?> 
          </td>
        </tr>
        <tr>
          <td class="text-center active">전화번호</td>
          <td class="text-center" id="ORDER_PHONE_VIEW">
            <?php echo setPhoneFormat($stockOrderInfo['fromPhoneNum']) ?> 
          </td>
          <td class="text-center active">팩스번호</td>
          <td class="text-center" id="ORDER_FAX_VIEW">
            <?php echo setPhoneFormat($stockOrderInfo['fromFaxNum']) ?> 
          </td>
        </tr>
        <tr>
          <td class="text-center active">담당자</td>
          <td class="text-center" id="ORDER_FAO_NM_VIEW">
            <?php echo $stockOrderInfo['fromChargeName'] ?> 
          </td>
          <td class="text-center active">담당자<br>휴대폰</td>
          <td class="text-center" id="ORDER_FAO_PHONE_VIEW">
             <?php echo setPhoneFormat($stockOrderInfo['fromChargePhoneNum']) ?> 
          </td>
        </tr>
      </tbody>
    </table>
     
    <table class="table table-bordered" id="estimate-product">
        <colgroup>
            <col width="1.5%"/>
            <col width="4%"/>
            <col width="1.5%"/>
            <col width="4%"/>
          </colgroup>
        <tr>
            <td class="text-center active">발주일</td>
            <td class="text-center calendar-icon" id="ORDER_DT_VIEW">
              <?php echo $stockOrderInfo['issueDate'] ?> 
            </td>
            <td class="text-center active">납기일</td>
            <td class="text-center calendar-icon" id="DELIVERY_DT_VIEW">
              <?php echo $stockOrderInfo['dueDate'] ?> 
            </td>
        </tr>
    </table>

    <!-- 상품정보 -->
    <table class="table table-bordered" id="estimate-product">
      <colgroup>
        <col width="1%"/>
        <col width="10%"/>
        <col width="4%"/>
        <col width="3%"/>
        <col width="3%"/>
        <col width="6%"/>
        <col width="4%"/>
        <col width="7%"/>
        <col width="5%"/>
      </colgroup>
      <thead>
        <tr>
          <th class="text-center active" colspan="2">합계금액</th>
          <th class="text-center all-total-price" colspan="7"><?php echo number2hangul($stockOrderInfo['totalPrice'] - $stockOrderInfo['deliveryPrice'])?>   원 (￦  <?php echo number_format($stockOrderInfo['totalPrice'] - $stockOrderInfo['deliveryPrice']) ?> ) <span style="font-weight: normal;">(VAT포함)</span></th>
        </tr>
      </thead>
      <tbody>
        <tr class="active border-double-top">
          <td class="text-center">#</td>
          <td class="text-center">품명</td>
          <td class="text-center">규격</td>
          <td class="text-center">단가</td>
          <td class="text-center">수량</td>
          <td class="text-center">공급가액</td>
          <td class="text-center">세액</td>
          <td class="text-center">합계</td>
          <td class="text-center">비고</td>
        </tr>
        <?php
          foreach($stockOrderInfo['stockOrderDetails'] as $idx => $item){
        ?>
          <tr>
            <td class="text-center"><?php echo $idx+=1 ?></td>
            <td class="text-left">
              <?php echo $item['itemName'] ?> 
              <?php
                  if(!empty($item['optionName'])){
              ?>
                  <span class="bill-goods-option"> 옵션: <?php echo $item['optionName']?>(+<?php echo number_format($item['optionPrice']+$item['optionPriceVAT']) ?>원)</span>
              <?php
                }
              ?>
            </td>
            <td class="text-center"><?php echo $item['goodsSpec'] ?> </td>
            <td class="text-right"><?php echo number_format($item['unitPrice']+$item['optionPrice']) ?> </td>
            <td class="text-right"><?php echo $item['quantity'] ?> </td>
            <td class="text-right"><?php echo number_format(($item['unitPrice']+$item['optionPrice'])*$item['quantity'] ) ?> </td>
            <td class="text-right"><?php echo number_format(($item['unitPriceVAT']+$item['optionPriceVAT'])*$item['quantity'] ) ?> </td>
            <td class="text-right"><?php echo number_format((($item['unitPrice']+$item['optionPrice'])*$item['quantity']) + (($item['unitPriceVAT']+$item['optionPriceVAT'])*$item['quantity'] ) ) ?> </td>
            <td class="text-left"><?php echo $item['itemMemo'] ?> </td>
          </tr>
        <?php
        }
        ?>
        <?php
          for($i=0; $i<($remainItemCnt) ; ++$i){
        ?>
            <tr>
              <td style="height:34px" class="text-left"></td>
              <td style="height:34px" class="text-center"></td>
              <td style="height:34px" class="text-right"></td>
              <td style="height:34px" class="text-right"></td>
              <td style="height:34px" class="text-right"></td>
              <td style="height:34px" class="text-right"></td>
              <td style="height:34px" class="text-left"></td>
              <td style="height:34px" class="text-center"></td>
              <td style="height:34px" class="text-left"></td>
            </tr>
        <?php
          }
        ?>
      </tfoot>
    </table>


    <table class="table table-bordered" id="estimate-product">
      <colgroup>
        <col width="1.7%"/>
        <col width="4.5%"/>
        <col width="1.63%"/>
        <col width="4%"/>
      </colgroup>
      <tr>
        <td class="text-center active">배송방법</td>
        <td class="text-center" id="IN_DELIVERY_WAY_VIEW">
          <?php echo $stockOrderInfo['deliveryMethod'] ?>
        </td>
        <td class="text-center active">배송비</td>
        <td class="text-right" id="IN_DELIVERY_WAY_VIEW">
          <?php echo number_format($stockOrderInfo['deliveryPrice'])  ?>원
        </td>
      </tr>
      
      <tr>
        <td class="text-center active">비고</td>
        <td class="text-center" id="IN_ETC">
          <?php echo $stockOrderInfo['memo'] ?>
        </td>
        <td class="text-center text-bold active">최종 결제금액</td>
        <td class="text-right text-bold text-fz16 calendar-icon" id="ORDER_DT">
          <?php echo number_format($stockOrderInfo['totalPrice'])  ?>원
        </td>
      </tr>

    </table>

    <table class="table table-bordered" id="estimate-etc">
      <colgroup>
        <col width="1%"/>
        <col width="1.9%"/>
        <col width="10%"/>
        <col width="4%"/>
        <col width="10%"/>
      </colgroup>
      <tbody>
        <tr>
            <td class="text-center active" rowspan="6">입<br>고<br>처</td>
            <td class="text-center active">상호</td>
            <td class="text-left" colspan="3" id="IN_BILLNM_VIEW">
              <?php echo $stockOrderInfo['shipName'] ?>
            </td>
        </tr>
        <tr>
            <td class="text-center active">주소</td>
            <td class="text-left address_area" colspan="3" id="IN_ADDRESS_VIEW">
              <?php echo $stockOrderInfo['shipZipcode']." ".$stockOrderInfo['shipAddress1']." ".$stockOrderInfo['shipAddress2'] ?>
            </td>
        </tr>
        <tr>
            <td class="text-center active">담당자</td>
            <td class="text-center" id="IN_FAO_NM_VIEW">
               <?php echo $stockOrderInfo['shipChargeName'] ?>
            </td>
            <td class="text-center active">담당자 휴대폰</td>
            <td class="text-center" id="IN_FAO_PHONE_VIEW">
               <?php echo setPhoneFormat($stockOrderInfo['shipChargePhoneNum'])  ?>
            </td>
        </tr>
      </tbody>
    </table>


    <table class="table table-bordered bill-footer" id="estimate-product">
      <colgroup>
        <col width="1.7%"/>
        <col width="4%"/>
        <col width="1.63%"/>
        <col width="4%"/>
      </colgroup>
      <tr>
        <td class="text-center active" colspan="4">
          발송
        </td>
      </tr>
      <tr>
        <td class="text-center active">팩스일시</td>
        <td class="text-center calendar-icon" id="ORDER_DT">
           <?php if($stockOrderInfo['sendFaxYN'] == "Y"):?>
             <?php echo $stockOrderInfo['sendFaxDatetime'] ?>
           <?php else:?>
              -
           <?php endif;?>
        </td>
        <td class="text-center active">문자일시</td>
        <td class="text-center calendar-icon" id="DELIVERY_DT">
          <?php if($stockOrderInfo['sendMessageYN'] == "Y"):?>
             <?php echo $stockOrderInfo['sendMessageDatetime'] ?>
           <?php else:?>
              -
           <?php endif;?>
        </td>
      </tr>
    </table>

    <!-- 하단 -->
    <div class="bill-footer">
      <div class="text-left" style="width:30%">
        <!-- 저장/출력/인쇄 -->
        <button type="button" class="btn btn-default estimate-btn" id="consignment-print" onclick="onPrint()">출력</button>
        <button type="button" class="btn btn-default estimate-btn" id="consignment-print" onclick="pdfDownload('<?php echo $stockOrderInfo['stockOrderID']?>')">PDF 다운</button>
      </div>
      <div class="text-center" style="width:40%">
        <!-- 발송 -->
        <button type="button" class="btn btn-primary estimate-btn" id="consignment-sendFax" onclick="onClickSMSSend('FAX','<?php echo FAX ?>')">FAX 발송</button>
        <button type="button" class="btn btn-primary estimate-btn" id="consignment-sendSMS" onclick="onClickSMSSend('SMS','<?php echo LMS ?>')">SMS 발송</button>
      </div>
      <div class="text-right" style="width:30%">
        <!-- 결제,주문, 닫기,취소 -->
        <button type="button" class="btn btn-default estimate-btn" id="consignment-cancel" onclick="window.close();">닫기</button>
      </div>
    </div>

  </div>
  
  <!-- 등록/수정 모달  -->
  <?php	require "phoneNumPopup.php"; ?>

</body>
</html>
<script>
   $(window).resize(function(){
        window.resizeTo(800,860);
    });
</script>