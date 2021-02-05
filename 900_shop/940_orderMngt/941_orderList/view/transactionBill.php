<!-- footer.html -->
<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/footerpopup.html";
  if($orderInfo['orderStatusCode'] == EC_ORDER_STATUS_CANCEL){
    echo "<script>alert('취소된 주문건입니다.');
          if(opener == null){
            history.back(-1);
          }else{
            window.close();
          }</script>";
    exit;
  }
  
?>

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
   <input type="hidden" id="printMode" value="p"/>
   <div class="container-fluid shop-estimate" style="width: 21cm;">
    
    <div class="estimate-top">
      <div class="estimate-top-left bill-barcode" id="barcodeContainer">

      </div>
      <div>
        <h2 class="text-center bill-title">거래명세서</h2>
      </div>
      <div class="estimate-top-right">작성일 : <?php echo $orderInfo['orderDate'] ?></div>
    </div>


    <!-- 견적사업자정보 -->
    <table class="table table-bordered" id="estimate-bizinfo">
      <colgroup>
        <col width="1%"/>
        <col width="19%"/>
        <col width="1%"/>
        <col width="6%"/>
        <col width="7%"/>
        <col width="3%"/>
        <col width="7%"/>
      </colgroup>
      <tbody>
        <tr>
          <td class="text-center active" rowspan="5">수<br>취<br>자</td>
          <td class="text-left recipient-info" rowspan="5">
            <!-- <div>일자 : 2020년 02월 10일</div>
            <br> -->
            <div class="recipient-info-biznm"><?php echo $orderInfo['garageName']?> 귀하</div>
            <div>성명 : <?php echo $orderInfo['orderName']?><br>
            연락처 : <?php echo setPhoneFormat($orderInfo['orderPhoneNum'])?></div>
            <div>주소 : <?php echo $orderInfo['address1']." ".$orderInfo['address2']?></div>
            <br>
            <div>아래와 같이 계산합니다.</div>
          </td>
          <td class="text-center active" rowspan="5">공<br>급<br>자</td>
          <td class="text-center active">사업자등록번호</td>
          <td class="text-center" colspan="3"><?php echo bizNoFormat($orderInfo['fromRegID'])?></td>
        </tr>
        <tr>
          <td class="text-center active">상 호 명</td>
          <td class="text-center"><?php echo $orderInfo['fromName']?></td>
          <td class="text-center active">대 표 자</td>
          <td class="text-center"><?php echo $orderInfo['fromCeoName']?></td>
        </tr>
        <tr>
          <td class="text-center active">주 소</td>
          <td class="text-center" colspan="3"><?php echo $orderInfo['fromAddress']?></td>
        </tr>
        <tr>
          <td class="text-center active">업 태</td>
          <td class="text-center"><?php echo $orderInfo['fromBizName']?></td>
          <td class="text-center active">종 목</td>
          <td class="text-center"><?php echo $orderInfo['fromPartName']?></td>
        </tr>
        <tr>
          <td class="text-center active">전화번호</td>
          <td class="text-center" style="word-break : break-all; width:85px"><?php echo setPhoneFormat($orderInfo['fromPhoneNum'])?></td>
          <td class="text-center active" >팩  스</td>
          <td class="text-center" style="word-break : break-all;  width:85px"><?php echo setPhoneFormat($orderInfo['fromFaxNum'])?></td>
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
        <tr class="border-double-bottom">
          <th class="text-center text-bold active" colspan="2">합계금액</th>
          <th class="text-center text-bold text-fz16 all-total-price" colspan="7"><?php echo number2hangul($orderInfo['totalPrice']) ?> 원 (￦ <?php echo number_format($orderInfo['totalPrice']) ?>) <span style="font-weight: normal;">(배송비,VAT포함)</span></th>
        </tr>
      </thead>
      <tbody>
        <tr class="active">
          <td class="text-center">#</td>
          <td class="text-center">품명</td>
          <td class="text-center">규격</td>
          <td class="text-center">단가</td>
          <td class="text-center">수량</td>
          <td class="text-center">공급가액</td>
          <td class="text-center">세액</td>
          <td class="text-center">비고</td>
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
             
            </td>
          </tr>
        <?php
          }
          for($i=0; $i<($remainItemCnt+9) ; ++$i){
        ?>
            <t>
              <td class="text-left"></td>
              <td class="text-center"></td>
              <td class="text-right"></td>
              <td class="text-right"></td>
              <td class="text-right"></td>
              <td class="text-right"></td>
              <td class="text-left"></td>
              <td class="text-center"></td>
            </tr>
        <?php
          }
        ?>
      </tbody>
      <tfoot>
        <tr class="border-double-top">
          <td class="text-center active" colspan="3">합계</td>
          <td class="text-right"><?php echo number_format($totalUnitPrice) ?></td>
          <td class="text-right"><?php echo $totalCnt ?></td>
          <td class="text-right"><?php echo number_format($totalPrice) ?></td>
          <td class="text-right"><?php echo number_format($totalVAT)?></td>
          <td class="text-right"></td>
        </tr>
        <!-- <tr>
          <td class="text-center active" colspan="6">특 이 사 항</td>
        </tr>
        <tr>
          <td class="text-left" colspan="6" id="etcarea">본 견적서는 견적일로부터 30일간 유효합니다.</td>
        </tr> -->
      </tfoot>
    </table>

    <div class="half-area">
      <div class="half-left">
        <table class="table table-bordered" id="estimate-etc">
          <colgroup>
            <col width="4%"/>
            <col width="10%"/>
            <col width="4%"/>
            <col width="10%"/>
          </colgroup>
          <tbody>
            <tr>
              <td class="text-center active" style="height: 38px;">비고</td>
              <td class="text-center"></td>
            </tr>
            <tr>
              <td class="text-center active">인수자</td>
              <td class="text-center"></td>
            </tr>
            <tr>
              <td class="text-center active">최종 출력일시</td>
              <td class="text-center"><?php echo $orderInfo['specificationPrintDatetime'] ?></td>
            </tr>
            <tr>
              <td class="text-center active">최종 출력자</td>
              <td class="text-center"><?php echo $orderInfo['specificationPrintUserName'] ?></td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="half-right">
        <table class="table table-bordered" id="estimate-product">
          <colgroup>
            <col width="2%">
            <col width="5%">
          </colgroup>
          <tbody>
            <tr>
              <td class="text-center active">주문금액</td>
              <td class="text-right"><?php echo number_format($orderInfo['orderPrice']) ?>원</td>
            </tr>
            <tr>
              <td class="text-center active">배송비</td>
              <td class="text-right"><?php echo number_format($orderInfo['deliveryPrice']) ?>원</td>
            </tr>
            <tr>
              <td class="text-center active">포인트사용금액</td>
              <td class="text-right"><?php
                  if($orderInfo['pointRedeem'] > 0){
                    echo "-";
                  }
                ?>
                <?php echo number_format($orderInfo['pointRedeem']) ?>P
              </td>
            </tr>
            <tr>
              <td class="text-center text-bold active">최종결제금액</td>
              <td class="text-right text-bold"><?php echo number_format($orderInfo['totalPrice']) ?>원</td>
            </tr>
            <tr>
              <td class="text-center active">적립예정포인트</td>
              <td class="text-right"><?php echo number_format($orderInfo['earnPoint']) ?>P</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- 하단 -->
    <div class="bill-footer">
      <div class="text-left" style="width:30%">
        <!-- 저장/출력/인쇄 -->
        <button type="button" class="btn btn-default" onclick="onPrint('<?php echo $orderID ?>')">출력</button>
        <button type="button" class="btn btn-default" onclick="pdfDownload('<?php echo $orderID ?>')">PDF다운로드</button>
      </div>
      <div class="text-center" style="width:40%">
        <!-- 발송 -->
        &nbsp;
        <!-- <button type="submit" id="submitBtn" class="btn btn-default">결제정보 전송</button> -->
      </div>
      <div class="text-right" style="width:30%">
        <!-- 결제,주문, 닫기,취소 -->
        <button type="button" class="btn btn-default" onclick="window.close();">닫기</button>
      </div>
    </div>

  </div>
</body>
<script type="text/javascript" src="/js/jquery-barcode.js"></script>
<script>
    $(window).resize(function(){
        window.resizeTo(800,860);
    });

    $('#barcodeContainer').barcode("<?php echo $orderID ?>", "code128", {
        barWidth: 1, barHeight: 50, moduleSize: 5, showHRI: true, moduleSize: 5, addQuietZone: true, marginHRI: 5, bgColor: "#FFFFFF", color: "#000000", fontSize: 12, output: "css", posX: 0, posY: 0 
    });    
</script>