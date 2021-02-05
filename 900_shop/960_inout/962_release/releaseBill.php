<!-- header.html -->
<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/headerpopup.html";
?>
<body>

  <!-- 테이블 -->
  
  <div class="container-fluid consignment-bill-area" style="width: 21cm">
    <div class="estimate-top">
      <h2 class="text-center bill-title consignment-bill-title">제품출고증</h2>
      <!-- <div class="estimate-top-left">발주번호 : 2020-QT-0214-0001</div> -->
      <!-- <div class="estimate-top-right">작성일 : 2020-02-22</div> -->
    </div>


    <!-- 견적사업자정보 -->
    <table class="table table-bordered" id="estimate-bizinfo">
      <colgroup>
        <col width="1%"/>
        <col width="2%"/>
        <col width="11%"/>
      </colgroup>
      <tbody>
        <tr>
          <td class="text-center recipient-info active" rowspan="2">출고처</td>
          <td class="text-center active">상호</td>
          <td class="text-left" id="PURCHASE_NM_VIEW">
            ㅇㅇㅇ매입처
          </td>
        </tr>
        <tr>
          <td class="text-center active">일자</td>
          <td class="text-left" id="PURCHASE_DT_VIEW">
            2019년 03월 19일
          </td>
        </tr>
        <tr>
          <td class="text-center recipient-info active" rowspan="2">인수처</td>
          <td class="text-center active">상호</td>
          <td class="text-left" id="CONSINGEE_NM_VIEW">
            11번가
          </td>
        </tr>
        <tr>
          <td class="text-center active">일자</td>
          <td class="text-left" id="CONSINGEE_NM_VIEW">
            2019년 03월 19일
          </td>
        </tr>
      </tbody>
    </table>


    <!-- 상품정보 -->
    <table class="table table-bordered" id="estimate-product">
      <colgroup>
        <col width="10%"/>
        <col width="3%"/>
        <col width="3%"/>
        <col width="6%"/>
      </colgroup>
      <thead>
        
      </thead>
      <tbody>
        <tr class="active">
          <td class="text-center">형태</td>
          <td class="text-center">수량</td>
          <td class="text-center">박스</td>
          <td class="text-center">비고</td>
        </tr>
        <tr>
          <td class="text-center">잘 팔리는 가방 빨간색</td>
          <td class="text-right">100</td>
          <td class="text-right">
            <div>10box</div>
            <div>0ea</div>
          </td>
          <td class="text-center"></td>
        </tr>
        <tr>
          <td class="text-center">잘 팔리는 가방 노란색</td>
          <td class="text-right">200</td>
          <td class="text-right">
            <div>50box</div>
            <div>0ea</div>
          </td>
          <td class="text-center"></td>
        </tr>
        <tr>
          <td class="text-center">잘 팔리는 가방 연두색</td>
          <td class="text-right">300</td>
          <td class="text-right">
            <div>0box</div>
            <div>300ea</div>
          </td>
          <td class="text-center"></td>
        </tr>
      </tbody>
      <tfoot>
        <tr class="border-double-top">
          <td class="text-center active text-bold">합계수량</td>
          <td class="text-center text-bold" colspan="3">600 ( 60box / 300ea )</td>
          <!-- <td class="text-right text-bold">60box / 300ea</td>
          <td class="text-right text-bold"></td> -->
        </tr>
        <!-- <tr>
          <td class="text-center active" colspan="6">특 이 사 항</td>
        </tr>
        <tr>
          <td class="text-left" colspan="6" id="etcarea">본 견적서는 견적일로부터 30일간 유효합니다.</td>
        </tr> -->
      </tfoot>
    </table>

    <div class="consignee-area">
      <div class="consignee-sign">
        <p>인수자 : </p>
        <p>(인)</p>
      </div>

      <!-- 견적사업자정보 -->
      <table class="table table-bordered" id="estimate-bizinfo">
        <colgroup>
          <col width="1%"/>
          <col width="2%"/>
          <col width="11%"/>
        </colgroup>
        <tbody>
          <tr>
            <td class="text-center recipient-info active" rowspan="3">출고처</td>
            <td class="text-center active">팩스</td>
            <td class="text-left" id="PURCHASE_FAX_VIEW">
              02-830-1619
            </td>
          </tr>
          <tr>
            <td class="text-center active">주소</td>
            <td class="text-left" id="PURCHASE_ADDRESS_VIEW">
              서울 영등포구 당산동6가 5층
            </td>
          </tr>
          <tr>
            <td class="text-center active">담당</td>
            <td class="text-left" id="PURCHASE_FAO_VIEW">
              홍길동
            </td>
          </tr>
          <tr>
            <td class="text-center recipient-info active" rowspan="3">인수처</td>
            <td class="text-center active">팩스</td>
            <td class="text-left" id="CONSINGEE_FAX_VIEW">
              02-123-1234
            </td>
          </tr>
          <tr>
            <td class="text-center active">주소</td>
            <td class="text-left" id="CONSINGEE_ADDRESS_VIEW">
              가산디지털2로 156
            </td>
          </tr>
          <tr>
            <td class="text-center active">담당</td>
            <td class="text-left" id="CONSINGEE_FAO_VIEW">
              개발자 1544-2333
            </td>
          </tr>
          
        </tbody>
      </table>
    </div>

    <!-- 하단 -->
    <div class="modal-footer bill-footer">
      <div class="text-left">
        <!-- 저장/출력/인쇄 -->
        <button type="button" class="btn btn-primary estimate-btn" id="consignment-print">출력</button>
        <button type="button" class="btn btn-default estimate-btn" id="consignment-print">PDF 다운</button>
      </div>
      <div class="text-center">
        <!-- 발송 -->
        &nbsp;
        <!-- <button type="submit" id="submitBtn" class="btn btn-default">결제정보 전송</button> -->
      </div>
      <div class="text-right">
        <!-- 결제,주문, 닫기,취소 -->
        <button type="button" class="btn btn-default estimate-btn" id="consignment-cancel" onclick="window.close();">닫기</button>
      </div>
    </div>

  </div>
  

</body>
<!-- footer.html -->
<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/footerpopup.html";
?>