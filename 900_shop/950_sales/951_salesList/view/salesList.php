
  <!-- 페이지 카테고리 -->
  <div class="container-fluid mb20px">
    <h3 class="nwt-category"><small>일반판매관리&nbsp;&nbsp;></small>일반판매 내역</h3>
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
                            <label for="period" class="sr-only">기간</label>
                            <!-- 기간선택 -->
                            <select class="form-control" id="period" name="period">
                              <option value="">=기간=</option>
                              <option value="ESS_ISSUE_DT">판매일</option>
                              <option value="ESS_DEPOSIT_DT">입금일</option>
                              <option value="ESS_PROC_DTHMS">처리일</option>
                              <option value="ESS_BILL_PUBLISH_DTHMS">계산서발행일</option>
                            </select>

                            <!-- 날짜선택 -->
                            <input type="text" class="form-control date" name="START_DT" autocomplete="off" id="START_DT" placeholder="입력"/>
                            <span class="glyphicon glyphicon-calendar search-glypicon"></span>
                            <label for="END_DT" class="nwt-form-label">~</label>                            
                            <input type="text" class="form-control date" name="END_DT" id="END_DT" autocomplete="off" placeholder="입력"/>
                            <span class="glyphicon glyphicon-calendar search-glypicon"></span>                            
                          </div>

                          <div class="col-md-2">
                            <label for="salesID" class="sr-only">일반판매번호</label>
                            <input type="text" class="form-control" name="salesID" id="salesID" placeholder="일반판매번호 입력">                            
                          </div>

                          <!-- 거래여부 -->
                          <div class="col-md-1">
                            <label for="contractStatusCode" class="sr-only">거래여부</label>
                            <select class="form-control" id="contractStatusCode" name="contractStatusCode">
                               <?php echo getOption($this->optionArray['CONTRACT_TYPE'], "거래여부" ,"") ?>
                            </select>                         
                          </div>

                          <!-- 구분  -->
                          <div class="col-md-1">
                            <label for="divisionID" class="sr-only">구분</label>
                            <select id="divisionID"  name="divisionID" class="form-control" >
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

                          <!-- 지역-->
                          <div class="col-md-2">
                            <label for="regionName" class="sr-only">지역</label>
                            <input type="text" class="form-control" name="regionName" id="regionName" placeholder="지역 입력">                            
                          </div>

                          <!-- 공업사(자동완성) -->
                          <div class="col-md-2">
                            <label for="garageName" class="sr-only">공업사</label>
                            <input type="text" class="form-control" name="garageName" id="garageName" placeholder="공업사 입력">                            
                          </div>

                           <!-- 입금여부 > MULTISELECT -->
                           <div class="col-md-1">
                            <label for="DEPOSIT_YN_SEARCH" class="sr-only">입금여부</label>
                            <select class="form-control" id="DEPOSIT_YN_SEARCH" name="DEPOSIT_YN_SEARCH">
                              <option value="">=입금여부=</option>
                              <option value="Y">입금</option>
                              <option value="N">미입금</option>
                            </select>                            
                          </div>
                        </div>

                        <div class="row frm-last-row">   
                          <!-- 지출증빙 발행여부 -->
                          <div class="col-md-1">
                            <label for="billPublishYN" class="sr-only">지출증빙 발행여부</label>
                            <select class="form-control" id="billPublishYN" name="billPublishYN">
                              <option value="">=지출증빙 발행여부=</option>
                              <option value="Y">발행</option>
                              <option value="N">미발행</option>
                            </select>                            
                          </div>

                          <!-- 지출증빙 종류 -->
                          <div class="col-md-1">
                            <label for="billTypeCode" class="sr-only">지출증빙 종류</label>
                            <select class="form-control" id="billTypeCode" name="billTypeCode">
                                <?php echo getOption($this->optionArray['EC_BILL_TYPE'], "지출증빙 종류" ,"") ?>
                            </select>                            
                          </div>
                          
                          <!-- 결제방법 -->
                          <div class="col-md-1">
                            <label for="paymentCode" class="sr-only">결제방법</label>
                            <select class="form-control" id="paymentCode" name="paymentCode">
                                <?php echo getOption($this->optionArray['EC_PAYMENT_TYPE'], "결제방법" ,"") ?>
                            </select>                            
                          </div>

                          <!--직원명 -->
                          <div class="col-md-1">
                            <label for="SALER_NM_SEARCH" class="sr-only">직원명</label>
                            <input type="text" class="form-control" name="SALER_NM_SEARCH" id="SALER_NM_SEARCH" placeholder="직원명 입력">                            
                          </div>

                          <div class="col-md-2">
                            <label for="searchMemo" class="sr-only">메모</label>
                            <input type="text" class="form-control" name="searchMemo" id="searchMemo" placeholder="메모 입력">                            
                          </div>

                          <!-- 판매금액 -->
                          <div class="col-md-2 frmGroup2">
                            <label for="START_SALE_PRICE_SEARCH" class="sr-only">판매금액</label>
                            <input type="text" class="form-control" name="START_SALE_PRICE_SEARCH" id="START_SALE_PRICE_SEARCH" placeholder="판매금액 입력">
                            <label for="END_SALE_PRICE_SEARCH" class="nwt-form-label">~</label>                            
                            <input type="text" class="form-control" name="END_SALE_PRICE_SEARCH" id="END_SALE_PRICE_SEARCH" placeholder=" 판매금액 입력">                           
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
      <div class="col-lg-9">
        <div class="form-inline f-right">
          <button type="button" class="btn btn-success" onclick="excelDownload()"><span class="glyphicon glyphicon-download-alt"></span>&nbsp;엑셀 다운로드</button>
          <button type="button" class="btn btn-primary" id="add-new-btn" onclick="onClickWrite()">등록</button>
        </div>
      </div>

    </div>
  </div>

  <!-- 테이블 -->
  <div class="container-fluid nwt-table">
    <table class="table table-bordered table-striped">
      <thead>
        <tr class="active">
          <th class="text-center">순번</th>
          <th class="text-center">판매일</th>
          <th class="text-center">일반판매번호</th>
          <th class="text-center">구분</th>
          <th class="text-center">지역</th>
          <th class="text-center">업체명</th>
          <th class="text-center">청구식</th>
          <th class="text-center">거래여부</th>
          <th class="text-center">담당자</th>
          <th class="text-center">사무실전화</th>
          <th class="text-center">담당자휴대폰</th>
          <th class="text-center">상품명</th>
          <th class="text-center">공급가액</th>
          <th class="text-center">세액</th>
          <th class="text-center">합계금액</th>
          <th class="text-center">실판매가</th>
          <th class="text-center">직원명</th>
          <th class="text-center">입금일</th>
          <th class="text-center">입금액</th>
          <th class="text-center">미입금액</th>
          <th class="text-center">DC율</th>
          <th class="text-center">입금여부</th>
          <th class="text-center">결제방법</th>
          <th class="text-center">메모</th>
          <th class="text-center">처리일시</th>
          <th class="text-center">처리자</th>
          <th class="text-center">지출증빙발행일</th>
          <th class="text-center">지출증빙발행여부</th>
          <th class="text-center">지출증빙발행종류</th>
          <th class="text-center">지출증빙발행 취소일</th>
          <th class="text-center">지출증빙발행 취소자</th>
          <th class="text-center">지출증빙</th>
          <th class="text-center">기능</th>
        </tr>
      </thead>
      <tbody id="dataTable">
      </tbody>
      <tbody id="indicatorTable" style="display:none">
          <tr>
            <td colspan="32"><?php	require $_SERVER['DOCUMENT_ROOT']."/include/indicator.html"; ?> </td>
          <tr>
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
    

<!-- footer.html -->
<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/footerShop.html";
?>