
  <!-- 페이지 카테고리 -->
  <div class="container-fluid mb20px">
    <h3 class="nwt-category"><small>주문 관리&nbsp;&nbsp;></small>발주 관리</h3>
  </div>

  <!-- 검색바(폼) 시작 -->
  <div class="container-fluid nwt-search-form">
      <div class="panel panel-default">
          <div class="panel-body">
              <form class="form-inline" id="searchForm" action="#">
                  <fieldset>
                      <legend>조건검색</legend>

                      <!-- 검색버튼 제외한 폼 wrap -->
                      <div class="frm-wrap">
                        <!-- 1st row -->
                        <div class="row">
                          
                          <!-- 기간 -->
                          <div class="col-md-2 frmGroup3">
                            <label for="dateField" class="sr-only">기간</label>
                            <!-- 기간선택 -->
                            <select class="form-control input-md" id="dateField" name="dateField">
                              <option value="">=기간=</option>
                              <option value="ES_ISSUE_DT">발주일</option>
                              <option value="ES_DUE_DT">납기일</option>
                              <option value="ES_DEPOSIT_DT">결제일</option>
                              <option value="ES_STOCK_IN_DTHMS">입고처리일</option>
                            </select>
                            <!-- 날짜선택 -->
                            <input type="text" class="form-control input-md" name="START_DT" id="START_DT" autocomplete="off"  placeholder="입력" maxlength="10">
                            <span class="glyphicon glyphicon-calendar search-glypicon"></span>
                            <label for="END_DT" class="nwt-form-label">~</label>                            
                            <input type="text" class="form-control" name="END_DT" id="END_DT" autocomplete="off"  placeholder="입력" maxlength="10">
                            <span class="glyphicon glyphicon-calendar search-glypicon"></span>                            
                          </div>

                          <!--발주번호 -->
                          <div class="col-md-1">
                            <label for="ORDER_NUM_SEARCH" class="sr-only">발주번호</label>
                            <input type="text" class="form-control" name="ORDER_NUM_SEARCH" id="ORDER_NUM_SEARCH" placeholder="발주번호 입력">                            
                          </div>

                           <!--주문번호 -->
                           <div class="col-md-1">
                            <label for="ORDER_NUMBER_SEARCH" class="sr-only">주문번호</label>
                            <input type="text" class="form-control" name="ORDER_NUMBER_SEARCH" id="ORDER_NUMBER_SEARCH" placeholder="주문번호 입력">                            
                          </div>

                          <!-- 발주구분 -->
                          <div class="col-md-1">
                            <label for="ORDER_DIVISION_SEARCH" class="sr-only">발주구분</label>
                            <select class="form-control" id="ORDER_DIVISION_SEARCH" name="ORDER_DIVISION_SEARCH">
                              <?php echo getOption($this->optionArray['EC_STOCK_ORDER_TYPE'], "발주구분" ,"") ?>
                            </select>                            
                          </div>

                          <!-- 매입처 -->
                          <div class="col-md-2 frmGroup2-2">
                            <label for="PURCHASE_SEARCH" class="sr-only">매입처</label>
                            <select class="form-control" id="PURCHASE_SEARCH" name="PURCHASE_SEARCH">
                            <option value="">=매입처=</option>
                            <option value="ES_TO_NM_AES">상호</option>
                            <option value="ES_TO_PHONENUM_AES">전화번호</option>
                            <option value="ES_TO_FAXNUM_AES">팩스번호</option>
                            </select>
                            <input type="text" class="form-control" name="PURCHASE_KEYWORD_SEARCH" id="PURCHASE_KEYWORD_SEARCH" placeholder="입력">                            
                          </div>

                           <!-- 입고처정보 -->
                           <div class="col-md-2 frmGroup2-2">
                            <label for="IN_INFO_SEARCH" class="sr-only">입고처정보</label>
                            <select class="form-control" id="IN_INFO_SEARCH" name="IN_INFO_SEARCH">
                              <option value="">=입고처정보=</option>
                              <option value="ES_SHIP_NM_AES">상호</option>
                              <option value="ES_SHIP_CHG_PHONENUM_AES">연락처</option>
                              <option value="ES_SHIP_CHG_NM_AES">담당자</option>
                            </select>
                            <input type="text" class="form-control" name="IN_INFO_KEYWORD_SEARCH" id="IN_INFO_KEYWORD_SEARCH" placeholder="입력">                            
                          </div>
                          
                          <!-- 주문정보 -->
                          <div class="col-md-2 frmGroup2-2">
                            <label for="ORDER_INFO_SEARCH" class="sr-only">주문정보</label>
                            <select class="form-control" id="ORDER_INFO_SEARCH" name="ORDER_INFO_SEARCH">
                              <option value="">=상품=</option>
                              <option value="itemName">상품명</option>
                              <option value="itemID">상품ID</option>
                            </select>
                            <input type="text" class="form-control" name="ORDER_INFO_KEYWORD_SEARCH" id="ORDER_INFO_KEYWORD_SEARCH" placeholder="입력">                            
                          </div>

                         

                        </div>

                        <div class="row frm-last-row">

                          <!-- 결제여부 -->
                          <div class="col-md-1">
                            <label for="PAYMENT_YN_SEARCH" class="sr-only">결제여부</label>
                            <select class="form-control" id="PAYMENT_YN_SEARCH" name="PAYMENT_YN_SEARCH">
                              <option value="">=결제여부=</option>
                              <option value="Y">결제</option>
                              <option value="N">미결제</option>
                            </select>                            
                          </div>

                          <!-- 입고여부 -->
                          <div class="col-md-1">
                            <label for="IN_YN_SEARCH" class="sr-only">입고여부</label>
                            <select class="form-control" id="IN_YN_SEARCH" name="IN_YN_SEARCH">
                              <option value="">=입고여부=</option>
                              <option value="Y">처리</option>
                              <option value="N">미처리</option>
                            </select>                            
                          </div>

                        </div>
                      </div> 
                      
                      <!-- 검색버튼 wrap -->
                      <div class="frm-search-wrap">
                        <!-- 검색btn -->
                        <div class="row">

                          <div class="col-md-12 text-right">
                            <button type="submit" class="btn btn-primary" id="frmSearch" style="margin-top: -10px"><span class="glyphicon glyphicon-search"></span>&nbsp;검색</button>
                          </div>
                        </div> 
                      </div>

                  </fieldset>
              </form>
          </div>
      </div>
  </div>
  <!-- 검색바(폼) 끝 -->


 
  <!-- 테이블 상단 #perPage, btn 영역 -->
  <div class="container-fluid nwt-table-top mb20r">
    <div class="row">
      
      <!-- 리스트개수 -->
      <?php	require $_SERVER['DOCUMENT_ROOT']."/include/listCnt.html"; ?>
      
      <!-- 테이블 우측 상단 btn -->
      <div class="col-lg-6 clearfix">
        <div class="form-inline f-right">
          <!-- 엑셀다운로드-->
          <button type="button" class="btn btn-success" onclick="excelDownload()"><span class="glyphicon glyphicon-download-alt"></span>&nbsp;엑셀 다운로드</button>
          <!-- 등록btn / modal handler -->
          <button type="button" class="btn btn-default" onclick="openArrayDepositDate()">일괄입고</button>
          <button type="button" class="btn btn-primary" id="add-new-btn" onclick="onClickInOutWrite()">발주</button>
        </div>
      </div>

    </div>
  </div>


  <!-- 테이블 -->
  <div class="container-fluid nwt-table" style="width: 100%;"> 
    <table class="table table-bordered table-striped">
      <thead>
        <tr class="active">
          <th class="text-center" rowspan="2">선택</th>
          <th class="text-center" rowspan="2">발주일</th>
          <th class="text-center" rowspan="2">납기일</th>
          <th class="text-center" rowspan="2">발주번호</th>
          <th class="text-center" rowspan="2">발주구분</th>
          <th class="text-center" rowspan="2">원거래<br>(주문서아이디)</th>
          <th class="text-center" colspan="3">매입처</th>
          <th class="text-center" rowspan="2">품목</th>
          <th class="text-center" rowspan="2">합계액</th>
          <th class="text-center" colspan="4">입고처</th>
          <th class="text-center" rowspan="2">배송방법</th>
          <th class="text-center" rowspan="2">비고</th>
          <th class="text-center" colspan="2">발송</th>
          <th class="text-center" rowspan="2">결제일</th>
          <th class="text-center" rowspan="2">결제금액</th>
          <th class="text-center" rowspan="2">결제여부</th>
          <th class="text-center" rowspan="2">은행명</th>
          <th class="text-center" rowspan="2">사업용계좌</th>
          <th class="text-center" rowspan="2">계좌주</th>
          <th class="text-center" rowspan="2">입고여부</th>
          <th class="text-center" rowspan="2">입고처리일</th>
          <th class="text-center" rowspan="2">기능</th>
        </tr>
        <tr class="active">
          <th class="text-center">상호</th>
          <th class="text-center">전화번호</th>
          <th class="text-center">팩스번호</th>
        
          <th class="text-center">상호</th>
          <th class="text-center">주소</th>
          <th class="text-center">담당자</th>
          <th class="text-center">연락처</th>
          <th class="text-center">팩스일시</th>
          <th class="text-center">문자일시</th>
        </tr>
      </thead>
      <tbody id="dataTable">                                                                 
      </tbody>
    </table>
  </div>

  
  <!-- pagination -->
  <div class="container-fluid">
    <div class="text-center">
      <ul class="pagination">
      </ul>
    </div>
  </div>


  <!-- 입고버튼 모달  -->
  <?php	require "./inoutDateModal.html"; ?>

  <!-- 일괄입고버튼 모달  -->
  <?php	require "./packageDepositDateModal.html"; ?>

  <!-- 결제버튼 모달  -->
  <?php	require "./inoutPayDateModal.html"; ?>
  <!-- 배달 모달  -->
  <?php	require "./inoutDeliveryModal.php"; ?>
    
<!-- footer.html -->
<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/footerShop.html";
?>