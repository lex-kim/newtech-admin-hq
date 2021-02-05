<style type="text/css" media="print">
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
<body>
  <div class="container-fluid shop-estimate"style="width: 21cm">
    <div id="estimateDetailContainer" >
    
    <div class="estimate-top">
     
      <div class="estimate-top-left">
        <div class="bill-barcode" id="barcodeContainer">
            
        </div>  
        견적서번호 : <?php echo $estimateInfo['quotationID']?></div>
        <div><h2 class="text-center bill-title">견적서</h2></div>
      <div class="estimate-top-right">작성일 : <?php echo $estimateInfo['issueDate']?></div>
    </div>

    <table class="table table-bordered">
      <colgroup>
        <col width="4%"/>
        <col width="5%"/>
        <col width="4%"/>
        <col width="5%"/>
        <col width="4%"/>
        <col width="5%"/>
        <col width="4%"/>
        <col width="5%"/>
      </colgroup>
      <tr>
        <td class="active text-center">상태</td>
        <td class="text-center"><?php echo $estimateInfo['quotationStatus'] ?></td>
        <td class="text-center active">거래상태</td>
        <td class="text-center"><?php echo $estimateInfo['contractStatus']?></td>
        <td class="text-center active">구분</td>
        <td class="text-center"><?php echo $estimateInfo['divisionName']?></td>
        <td class="text-center active">지역</td>
        <td class="text-center"><?php echo $estimateInfo['regionName']?></td>
      </tr>
    </table>

    <!-- 견적사업자정보 -->
    <table class="table table-bordered" id="estimate-bizinfo">
      <colgroup>
        <col width="10%"/>
        <col width="1%"/>
        <col width="3%"/>
        <col width="5%"/>
        <col width="3%"/>
        <col width="5%"/>
      </colgroup>
      <tbody>
        <tr>
          <td class="text-left recipient-info" rowspan="5">
            발행일자 : <?php echo $estimateInfo['publishDatetime']?><br><br>
            <span class="toBizNm"><?php echo $estimateInfo['toName']?> 귀하<br></span>
            수신자 : <?php echo $estimateInfo['toReceiverName']?><br>
            수신자 연락처 : <?php echo setPhoneFormat($estimateInfo['toPhoneNum'])?><br>
            이메일 : <?php echo $estimateInfo['toEmail']?><br><br>아래와 같이 견적합니다.</td>
          <td class="text-center active" rowspan="5">공급자</td>
          <td class="text-center active">등록번호</td>
          <td class="text-center" colspan="3"><?php echo bizNoFormat($estimateInfo['fromRegID'])?></td>
        </tr>
        <tr>
          <td class="text-center active">상 호 명</td>
          <td class="text-center"><?php echo $estimateInfo['fromName']?></td>
          <td class="text-center active">대 표 자</td>
          <td class="text-center"><?php echo $estimateInfo['fromCeoName']?></td>
        </tr>
        <tr>
          <td class="text-center active">주 소</td>
          <td class="text-center" colspan="3"><?php echo $estimateInfo['fromAddress']?></td>
        </tr>
        <tr>
          <td class="text-center active">업 태</td>
          <td class="text-center"><?php echo $estimateInfo['fromBizName']?></td>
          <td class="text-center active">종 목</td>
          <td class="text-center"><?php echo $estimateInfo['fromPartName']?></td>
        </tr>
        <tr>
          <td class="text-center active">전화번호</td>
          <td class="text-center"><?php echo setPhoneFormat($estimateInfo['fromPhoneNum'])?></td>
          <td class="text-center active">팩  스</td>
          <td class="text-center"><?php echo setPhoneFormat($estimateInfo['fromFaxNum'])?></td>
        </tr>
      </tbody>
    </table>

    <!-- 상품정보 -->
    <table class="table table-bordered" id="estimate-product">
      <colgroup>
        <!-- <col width="1%"/>
        <col width="10%"/>
        <col width="5%"/>
        <col width="3%"/>
        <col width="5%"/> -->
      </colgroup>
      <thead>
        <tr>
          <th class="text-center text-bold active">합계금액</th>
          <th class="text-center text-bold text-fz16 all-total-price" colspan="5">
            <?php echo number2hangul($estimateInfo['totalSum']+$estimateInfo['deliveryFee'])?> 원 
            (￦<?php echo number_format($estimateInfo['totalSum']+$estimateInfo['deliveryFee'])?>) <span style="font-weight: normal;">(배송비,VAT포함)</span></th>
        </tr>
      </thead>
      <tbody>
        <tr class="active border-double-top">
          <td class="text-center">품명</td>
          <td class="text-center">규격</td>
          <td class="text-center">단가(원)</td>
          <td class="text-center">수량(개)</td>
          <td class="text-center">공급가액(원)</td>
          <td class="text-center">세액(원)</td>
        </tr>
        <?php
          foreach($estimateInfo['quotationDetails'] as $idx => $item){
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
            <td class="text-left" <?php if($isSoldout == "Y") echo "style=color:red"; ?>><?php echo $item['itemName']?>
             <?php
                  if($isSoldout == "Y"){
              ?>
                  <span class="bill-footer"><img src="../../../images/soldout.png"/></span>
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
            <td class="text-center"><?php echo $item['goodsSpec']?></td>
            <td class="text-right"><?php echo number_format($item['unitPrice']+$item['optionPrice']) ?></td>
            <td class="text-right"><?php echo $item['quantity']?></td>
            <td class="text-right"><?php echo number_format(($item['unitPrice']+$item['optionPrice'])*$item['quantity']) ?></td>
            <td class="text-right"><?php echo number_format(($item['optionPriceVAT']+$item['unitPriceVAT'])*$item['quantity']) ?></td>
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
            </tr>
        <?php
          }
        ?>
      </tbody>
      <tfoot>
        <tr class="border-double-top">
          <td class="text-center active" colspan="4">총계</td>
          <td class="text-right"><?php echo number_format(empty($estimateInfo['totalPrice'])?0:$estimateInfo['totalPrice'])?></td>
          <td class="text-right"><?php echo number_format($estimateInfo['totalVAT'])?></td>
        </tr>
        <!-- <tr>
          <td class="text-center active" colspan="6">특 이 사 항</td>
        </tr>
        <tr>
          <td class="text-left" colspan="6" id="etcarea">본 견적서는 견적일로부터 30일간 유효합니다.</td>
        </tr> -->
      </tfoot>
    </table>

    <table class="table table-bordered" id="estimate-etc">
      <colgroup>
        <col width="6%"/>
        <col width="10%"/>
        <col width="6%"/>
        <col width="10%"/>
      </colgroup>
      <tbody>
        <tr>
          <td class="text-center active">수령방식</td>
          <td class="text-center"><?php echo $estimateInfo['deliveryMethod']?> ( <?php echo number_format($estimateInfo['deliveryFee']) ?>원 )</td>
          <td class="text-center active">결제방법</td>
          <td class="text-center"><?php echo $estimateInfo['paymentName']?></td>
        </tr>
        <tr>
          <td class="text-center active">요청자</td>
          <td class="text-center"><?php echo $estimateInfo['requestUser']?></td>
          <td class="text-center active">요청일시</td>
          <td class="text-center"><?php echo $estimateInfo['requestDatetime']?></td>
        </tr>
        <tr>
          <td class="text-center active">작성자</td>
          <td class="text-center"><?php echo $estimateInfo['publishUser']?></td>
          <td class="text-center active">견적서발행일시</td>
          <td class="text-center"><?php echo $estimateInfo['publishDatetime']?></td>
        </tr>
        <tr>
          <td class="text-center active etc-singularity">특 이 사 항</td>
          <td class="text-center" colspan="3"><?php echo str_replace("\r\n","<br>", $estimateInfo['quotationNote']) ?></td>
        </tr>
      </tbody>
    </table>
        </div>
    <!-- 하단 -->
    <div class="bill-footer">
      <div class="text-left" style="width:30%">
        <!-- 저장/출력/인쇄 -->
        <button type="button" class="btn btn-default estimate-btn" id="estimate-print" onclick="onPrint()">출력</button>
      </div>
      <div class="text-center" style="width:40%">
        <!-- 발송 -->
        <button type="button" class="btn btn-primary estimate-btn" id="estimate-sendFax" onclick="onClickSMSSend('FAX','<?php echo FAX ?>')">팩스발송</button>
        <button type="button" class="btn btn-primary estimate-btn" id="estimate-sendSMS"  onclick="onClickSMSSend('SMS','<?php echo LMS ?>')">SMS발송</button>
      </div>
      <div class="text-right" style="width:30%">
        <!-- 결제,주문, 닫기,취소 -->
        <?php if($estimateInfo['quotationStatusCode'] == EC_QUOTATION_STATUS_ASK || $estimateInfo['quotationStatusCode'] == EC_QUOTATION_STATUS_COMPLETE) : ?>
          <button type="button" class="btn btn-primary estimate-btn" id="estimate-order" onclick="onClickOrder('<?php echo $estimateInfo['quotationID']?>','<?php echo $estimateInfo['garageID']?>')">주문</button>
        <?php endif; ?>
        <button type="button" class="btn btn-default estimate-btn" id="estimate-cancel" onclick="window.close();">닫기</button>
      </div>
    </div>

  </div>

  <?php
    if($estimateInfo['quotationStatusCode'] == EC_QUOTATION_STATUS_EXPIRE){
  ?>
    <div class="expiration_area">
        <div class="expiration_text">만료</div>
    </div>
  <?php
  }
  ?>

  <!-- 등록/수정 모달  -->
  <?php	require "phoneNumPopup.php"; ?>
   

</body>
<!-- footer.html -->
<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/footerpopup.html";
?>
<script type="text/javascript" src="/js/jquery-barcode.js"></script>
<script>
    $('#barcodeContainer').barcode("<?php echo $estimateInfo['quotationID']?>", "code128", {
        barWidth: 1, barHeight: 50, moduleSize: 5, showHRI: false, moduleSize: 5, addQuietZone: true, marginHRI: 5, bgColor: "#FFFFFF", color: "#000000", fontSize: 10, output: "css", posX: 0, posY: 0 
    });    
</script>