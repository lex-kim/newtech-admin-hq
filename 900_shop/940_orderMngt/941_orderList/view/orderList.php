
  
  <!-- 페이지 카테고리 -->
  <div class="container-fluid mb20px">
    <h3 class="nwt-category"><small>주문관리&nbsp;&nbsp;></small>주문관리</h3>
  </div>


  <!-- 검색바(폼) 시작 -->
  <div class="container-fluid nwt-search-form">
      <div class="panel panel-default">
          <div class="panel-body">
              <form class="form-inline" id="searchForm">
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
                            <select class="form-control" id="dateField" name="dateField">
                              <option value="">=기간=</option>
                              <!-- <option value="NB_CLAIM_DT">판매일</option> -->
                              <option value="EO.WRT_DTHMS">작성일</option>
                              <option value="EO.EO_DEPOSIT_DT">입금일</option>
                            </select>
                            <!-- 날짜선택 -->
                            <input type="text" class="form-control" name="START_DT_SEARCH" id="START_DT_SEARCH" placeholder="입력">
                            <span class="glyphicon glyphicon-calendar search-glypicon"></span>
                            <label for="END_DT" class="nwt-form-label">~</label>                            
                            <input type="text" class="form-control" name="END_DT_SEARCH" id="END_DT_SEARCH" placeholder="입력">
                            <span class="glyphicon glyphicon-calendar search-glypicon"></span>                            
                          </div>

                          <!-- 주문번호 -->
                          <div class="col-md-2">
                            <label for="ORDER_ID_SEARCH" class="sr-only">주문번호</label>
                            <input type="text" class="form-control" name="ORDER_ID_SEARCH" id="ORDER_ID_SEARCH" placeholder="주문번호 입력">                            
                          </div>

                          <!-- 주문상태 -->
                          <div class="col-md-1">
                            <label for="orderStatusCode" class="sr-only">주문상태</label>
                            <select class="form-control" id="orderStatusCode" name="orderStatusCode">
                              <?php echo getOption($this->optionArray['EC_ORDER_STATUS'], "주문상태" ,"") ?>
                            </select>                            
                          </div>

                          <!-- 거래여부 -->
                          <div class="col-md-1">
                            <label for="contractStatusCode" class="sr-only">거래여부</label>
                            <select class="form-control" id="contractStatusCode" name="contractStatusCode">
                                <?php echo getOption($this->optionArray['CONTRACT_TYPE'], "거래여부" ,"") ?>
                            </select>                            
                          </div>

                          <!-- 포인트 사용여부  -->
                          <div class="col-md-1">
                            <label for="POINT_USE_YN_SEARCH" class="sr-only">포인트 사용여부</label>
                            <select class="form-control" id="POINT_USE_YN_SEARCH" name="POINT_USE_YN_SEARCH">
                              <option value="">=포인트 사용여부=</option>
                              <option value="Y">사용</option>
                              <option value="N">미사용</option>
                            </select>                            
                          </div>

                          <!-- 배송방법 -->
                          <div class="col-md-1">
                            <label for="deliveryMethodCode" class="sr-only">배송방법</label>
                            <select class="form-control" id="deliveryMethodCode" name="deliveryMethodCode">
                                <?php echo getOption($this->optionArray['EC_DELIVERY_METHOD'], "배송방법" ,"") ?>
                            </select>                            
                          </div>

                          <!-- 입금여부 -->
                          <div class="col-md-1">
                            <label for="DEPOSIT_YN_SEARCH" class="sr-only">입금여부</label>
                            <select class="form-control" id="DEPOSIT_YN_SEARCH" name="DEPOSIT_YN_SEARCH">
                              <option value="">=입금여부=</option>
                              <option value="Y">입금</option>
                              <option value="N">미입금</option>
                            </select>                            
                          </div>

                           <!-- 결제수단 -->
                           <div class="col-md-1">
                            <label for="paymentCode" class="sr-only">결제수단</label>
                            <select class="form-control" id="paymentCode" name="paymentCode">
                                <?php echo getOption($this->optionArray['EC_PAYMENT_TYPE'], "결제수단" ,"") ?>
                            </select>                            
                          </div>

                          <!-- 취소중료 -->
                          <div class="col-md-1">
                            <label for="cancelType" class="sr-only">취소종류</label>
                            <select class="form-control" id="cancelType" name="cancelType">
                                <?php echo getOption($this->optionArray['EC_ORDER_CANCEL'], "취소종류" ,"") ?>
                            </select>                            
                          </div>

                          <!-- 취소사유 -->
                          <div class="col-md-1">
                            <label for="cancelDetailType" class="sr-only">취소사유</label>
                            <select class="form-control" id="cancelDetailType" name="cancelDetailType">
                                <?php echo getOption($this->optionArray['EC_ORDER_CANCEL_TYPE'], "취소사유" ,"") ?>
                            </select>                            
                          </div>
                        </div>


                        <!-- 2nd row -->
                        <div class="row frm-last-row">

                        <!-- 지출증빙 -->
                        <div class="col-md-1">
                            <label for="billTypeCode" class="sr-only">지출증빙</label>
                            <select class="form-control" id="billTypeCode" name="billTypeCode">
                                <?php echo getOption($this->optionArray['EC_BILL_TYPE'], "지출증빙" ,"") ?>
                            </select>                            
                          </div>
                            
                          <!-- 거래명세서출력여부  -->
                          <div class="col-md-1">
                            <label for="printTransStateYN" class="sr-only">거래명세서출력여부</label>
                            <select class="form-control" id="printTransStateYN" name="printTransStateYN">
                              <option value="">=거래명세서출력여부=</option>
                              <option value="Y">출력</option>
                              <option value="N">미출력</option>
                            </select>                            
                          </div>

                          <div class="col-md-1">
                            <label for="DIVISION_ID_SEARCH" class="sr-only">구분</label>
                            <select class="form-control" id="DIVISION_ID_SEARCH" name="DIVISION_ID_SEARCH">
                              <option value="">=구분=</option>
                                <?php
                                  foreach($divisionArray as $idx => $item){
                                ?>
                                  <option value="<?php echo $item['divisionID']?>"><?php echo $item['divisionName']?></option>
                                <?php
                                  }
                                ?>
                            </select>                            
                          </div>

                          <!-- 지역 -->
                          <div class="col-md-2">
                            <label for="regionName" class="sr-only">지역</label>
                            <input type="text" class="form-control" name="regionName" id="regionName" placeholder="지역 입력">                            
                          </div>

                          <!-- 업체명 -->
                          <div class="col-md-2">
                            <label for="garageName" class="sr-only">업체명</label>
                            <input type="text" class="form-control" name="garageName" id="garageName" placeholder="업체명 입력">                            
                          </div>
                        
                          <!-- 공업사아이디 -->
                          <div class="col-md-2">
                            <label for="garageID" class="sr-only">공업사아이디</label>
                            <input type="text" class="form-control" name="garageID" id="garageID" placeholder="공업사ID 입력">                            
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
      <!-- 집계보기, 확장하기 -->
      <!-- <div class="table-top-etc">
        <label class="totalbtn" for="totalcheckbtn">집계보기 <input id="totalcheckbtn" type="checkbox"></label>
      </div> -->
      
      <!-- 테이블 우측 상단 btn -->
      <div class="col-lg-9">
        <div class="form-inline f-right table-top-uplode">
          <div>
            <form name="csvUploadForm" id="csvUploadForm" method="post" enctype="multipart/form-data">
              <div class="form-inline f-right excel-down-btn">  
                  <input id="deliveryCSV" name="deliveryCSV" type="file" class="form-control" accept=".csv">
                  <button type="submit" class="btn btn-success" data="orderID" data-text="주문번호"><span class="glyphicon glyphicon-download-alt"></span>&nbsp;일괄배송처리 업로드</button>
                  <div class="excel-down-file">
                    <div class="excel-down">
                      <!-- * 아래 업로드파일 양식에 맞춰 <br>&nbsp;&nbsp;&nbsp;모든 서식을 제거한csv파일로 업로드 가능합니다. -->
                      <a href="javascript:onDownloadDeliveryFormat();">
                        <!-- <img src="/images/excelicon.png" alt=""> -->
                        <div>* 일괄배송처리 업로드파일(CSV) 양식 다운로드</div>
                      </a>
                    </div>
                  </div>
                  
              </div>
            </form>
          </div>
          <div>
            <button type="button" class="btn btn-success" onclick="excelDownload()"><span class="glyphicon glyphicon-download-alt"></span>&nbsp;엑셀 다운로드</button>
            <!-- <button type="button" class="btn btn-default">공업사신규등록</button> -->
            <button type="button" class="btn btn-primary" onclick='onClickOrder("+data.idx+")'>주문서작성</button>
          </div>
        </div>
      </div>

    </div>
  </div>

  <div class="container-fluid nwt-table-top" style="margin-bottom:0px">
    <ul class="nav nav-tabs" id="orderTab">
      <li data-mode="" class="active"><a href="#">전체</a></li>
      <li data-mode="<?=TAB_ORDER?>"><a href="#">주문</a></li>
      <li data-mode="<?=TAB_SEND?>"><a href="#">배송</a></li>
      <li data-mode="<?=TAB_COMPLETE?>"><a href="#">구매확정</a></li>
      <li data-mode="<?=TAB_CANCEL?>"><a href="#">교환&취소</a></li>
    </ul>

    <input type="hidden" id="tabValue" value=""/>
  </div>

  <!-- 테이블 -->
  <div class="container-fluid nwt-table">
    <table class="table table-bordered table-striped">
      <thead>
        <tr class="active">
          <th class="text-center">순번</th>
          <th class="text-center">주문일시</th>
          <th class="text-center">주문번호</th>
          <th class="text-center">주문상태</th>
          <th class="text-center">거래여부</th>
          <th class="text-center">구분</th>
          <th class="text-center">지역</th>
          <th class="text-center">공업사</th>
          <th class="text-center">품목</th>
          <th class="text-center">결제금액</th>
          <th class="text-center">배송비</th>
          <th class="text-center">포인트</th>
          <th class="text-center">실결제금액</th>
          <th class="text-center">입금일</th>
          <th class="text-center">입금액</th>
          <th class="text-center">미입금액</th>
          <th class="text-center">DC율</th>
          <th class="text-center">배송방법</th>
          <th class="text-center">입금여부</th>
          <th class="text-center">결제방법</th>
          <th class="text-center">취소종류</th>
          <th class="text-center">취소사유</th>
          <th class="text-center">계산서발행일</th>
          <th class="text-center">거래명세서출력</th>
          <th class="text-center">메모</th>
          <th class="text-center">지출증빙</th>
          <th class="text-center">지출증빙<br>발행</th>
          <th class="text-center">기능</th>
        </tr>
      </thead>
      <tbody id="dataTable">
      </tbody>
    </table>
  </div>

 <!-- indicator -->
 <!-- <?php	require $_SERVER['DOCUMENT_ROOT']."/include/indicator.html"; ?> -->
  
  <!-- pagination -->
  <div class="container-fluid">
    <div class="text-center">
      <ul class="pagination">
      </ul>
    </div>
  </div>

  <form id="sendTransForm" name="sendTransForm">
  </form>
    
  <?php	require $_SERVER['DOCUMENT_ROOT']."/include/uploadCSVResultModal.html"; ?>
<!-- footer.html -->
<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/footerShop.html";
?>
<script src="/js/uploadCSV.js"></script>