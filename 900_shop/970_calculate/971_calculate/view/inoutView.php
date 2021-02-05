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
      <h2 class="text-center bill-title consignment-bill-title">정산서</h2>
      <div class="estimate-top-left">정산년월 : <?php echo $stockOrderInfo['stockOrderID'] ?></div>
      <div class="estimate-top-right">정산일 : <?php echo $stockOrderInfo['stockOrderType'] ?></div>
    </div>


    <!-- 견적사업자정보 -->
    <table class="table table-bordered" id="estimate-bizinfo">
      <colgroup>
        <col width="1%"/>
        <col width="14%"/>
        <col width="1%"/>
        <col width="4%"/>
        <col width="6%"/>
        <col width="3%"/>
        <col width="6%"/>
      </colgroup>
      <tbody>
        <tr>
          <td class="text-center active" rowspan="4">매<br>입<br>처</td>
          <td class="text-left recipient-info" rowspan="4">
            <div class="recipient-info-biznm"><?php echo $stockOrderInfo['toName'] ?> 귀하</div>
            <div>사업자번호 : <?php echo setPhoneFormat($stockOrderInfo['toPhoneNum']) ?></div>
            <div>대표자 : <?php echo setPhoneFormat($stockOrderInfo['toPhoneNum']) ?></div>
            <div>주소 : <?php echo setPhoneFormat($stockOrderInfo['toPhoneNum']) ?></div>
            <div>전화번호 : <?php echo setPhoneFormat($stockOrderInfo['toPhoneNum']) ?></div>
            <div>이메일 : <?php echo setPhoneFormat($stockOrderInfo['toEmail']) ?></div>
            <br>
            <div>아래와 같이 발주합니다.</div>
            

          </td>
          
          <td class="text-center active" rowspan="4">발<br>주<br>처</td>
          <td class="text-center active">사업자번호</td>
          <td class="text-center" colspan="3" id="ORDER_REGID_VIEW">
             <?php echo $stockOrderInfo['fromRegId'] ?>
          </td>
        </tr>
        <tr>
          <td class="text-center active">상호명</td>
          <td class="text-center" id="ORDER_NM_VIEW">
             <?php echo $stockOrderInfo['fromName'] ?>
          </td>
          <td class="text-center active">대표자</td>
          <td class="text-center" id="ORDER_CEONM_VIEW">
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
          <td class="text-center active">이메일</td>
          <td class="text-center" id="ORDER_FAX_VIEW">
            <?php echo setPhoneFormat($stockOrderInfo['fromFaxNum']) ?> 
          </td>
        </tr>
      </tbody>
    </table>
     
    <!-- 정산&은행정보 -->
    <table class="table table-bordered">
      <colgroup>
        <col width="7%"/>
        <col width="19%"/>
        <col width="7%"/>
        <col width="10%"/>
        <col width="7%"/>
        <col width="4%"/>
        <col width="6%"/>
        <col width="2%"/>
        <col width="9%"/>
        <col width="3%"/>
      </colgroup>
      <tbody>
        <tr>
          <td class="text-center active">정산기간</td>
          <td class="text-center">2020-11-01 ~ 2020-11-30</td>
          <td class="text-center active">청구일</td>
          <td class="text-center">2020-12-12</td>
          <td class="text-center active">청구금액</td>
          <td class="text-center">300,000</td>
          <td class="text-center active">세액</td>
          <td class="text-center">10,000</td>
          <td class="text-center active">합계</td>
          <td class="text-center">5,310,000</td>
        </tr>
        <tr>
          <td class="text-center active">은행명</td>
          <td class="text-center">우리은행</td>
          <td class="text-center active">계좌번호</td>
          <td class="text-center" colspan="3">351-123-1234-2</td>
          <td class="text-center active" colspan="1">계좌주</td>
          <td class="text-center" colspan="1">홍길동</td>
          <td class="text-center text-red active" colspan="1">전월미수누계</td>
          <td class="text-center text-red text-bold" colspan="1">20,000원</td>
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
          <th class="text-center active" colspan="2">합계금액</th>
          <th class="text-center all-total-price" colspan="8"><?php echo number2hangul($stockOrderInfo['totalPrice'] - $stockOrderInfo['deliveryPrice'])?>   원 (￦  <?php echo number_format($stockOrderInfo['totalPrice'] - $stockOrderInfo['deliveryPrice']) ?> ) <span style="font-weight: normal;">(VAT포함)</span></th>
        </tr>
      </thead>
      <tbody>
        <tr class="active border-double-top">
          <td class="text-center">#</td>
          <td class="text-center">주문서</td>
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
              <td style="height:34px" class="text-center"></td>
              <td style="height:34px" class="text-center"></td>
            </tr>
        <?php
          }
        ?>
      </tfoot>
    </table>

    <!-- 확정여부 -->
    <table class="table table-bordered">
      <colgroup>
        <col width="4.3%"/>
        <col width="5%"/>
        <col width="3%"/>
        <col width="7%"/>
        <col width="3%"/>
        <col width="5%"/>
      </colgroup>
      <tbody>
        <tr>
          <td class="text-center active">확정여부</td>
          <td class="text-center">확정</td>
          <td class="text-center active">확정일시</td>
          <td class="text-center">2020-12-12</td>
          <td class="text-center active">확정자</td>
          <td class="text-center">ㅇㅇㅇ</td>
        </tr>
      </tbody>
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
        <!-- <button type="button" class="btn btn-primary estimate-btn" id="consignment-sendFax" onclick="onClickSMSSend('FAX','<?php echo FAX ?>')">FAX 발송</button> -->
        <!-- <button type="button" class="btn btn-primary estimate-btn" id="consignment-sendSMS" onclick="onClickSMSSend('SMS','<?php echo LMS ?>')">SMS 발송</button> -->
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