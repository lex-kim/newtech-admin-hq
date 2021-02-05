
  <!-- 페이지 카테고리 -->
  <div class="container-fluid mb20px">
    <h3 class="nwt-category">정산관리</h3>
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
                              <option value="ES_ISSUE_DT">정산년월</option>
                            </select>
                            <!-- 날짜선택 -->
                            <input type="text" class="form-control input-md" name="START_DT" id="START_DT" autocomplete="off"  placeholder="입력" maxlength="10">
                            <span class="glyphicon glyphicon-calendar search-glypicon"></span>
                            <label for="END_DT" class="nwt-form-label">~</label>                            
                            <input type="text" class="form-control" name="END_DT" id="END_DT" autocomplete="off"  placeholder="입력" maxlength="10">
                            <span class="glyphicon glyphicon-calendar search-glypicon"></span>                            
                          </div>


                          <!-- 구분 -->
                          <div class="col-md-1">
                            <label for="DIVISION_SEARCH" class="sr-only">구분</label>
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

                          <!-- 지역 -->
                          <div class="col-md-2">
                            <label for="regionName" class="sr-only">지역</label>
                            <input type="text" class="form-control" name="regionName" id="regionName" placeholder="지역 입력">                            
                          </div>

                          <!-- 거래여부 -->
                          <div class="col-md-1">
                            <label for="contractStatusCode" class="sr-only">거래상태</label>
                            <select class="form-control" id="contractStatusCode" name="contractStatusCode">
                              <?php echo getOption($this->optionArray['EC_USER_STATUS'], "거래상태" ,"") ?>
                            </select>                            
                          </div>


                          <!-- 확정여부 -->
                          <div class="col-md-1">
                            <label for="CONFIRM_SEARCH" class="sr-only">확정여부</label>
                            <select class="form-control" id="CONFIRM_SEARCH" name="CONFIRM_SEARCH">
                              <option value="">=확정여부=</option>
                              <option value="A">전체</option>
                              <option value="N">확정</option>
                              <option value="N">미확정</option>
                            </select>                            
                          </div>


                          <!-- 입금여부 -->
                          <div class="col-md-1">
                            <label for="DEPOSIT_YN_SEARCH" class="sr-only">입금여부</label>
                            <select class="form-control" id="DEPOSIT_YN_SEARCH" name="DEPOSIT_YN_SEARCH">
                              <option value="">=입금여부=</option>
                              <option value="Y">전체</option>
                              <option value="Y">입금</option>
                              <option value="N">미입금</option>
                              <option value="N">부분</option>
                            </select>                            
                          </div>

                          <!-- 가맹점명 -->
                          <div class="col-md-2">
                            <label for="garageName" class="sr-only">가맹점명</label>
                            <input type="text" class="form-control" name="garageName" id="garageName" placeholder="가맹점명 입력">                            
                          </div>

                          <!-- 점주명 -->
                          <div class="col-md-2">
                            <label for="keeperName" class="sr-only">점주명</label>
                            <input type="text" class="form-control" name="keeperName" id="keeperName" placeholder="점주명 입력">                            
                          </div>
                       
                        </div>

                        <div class="row frm-last-row">

                          <!-- 연락처 -->
                          <div class="col-md-2">
                            <label for="keeperPhone" class="sr-only">연락처</label>
                            <input type="text" class="form-control" name="keeperPhone" id="keeperPhone" placeholder="연락처 입력">                            
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
        </div>
      </div>

    </div>
  </div>


  <!-- 배송상태 별 리스트 탭 -->
  <!-- <div class="container-fluid nwt-table" style="width: 100%;"> 
    <ul class="nav nav-tabs" id="orderTab">
      <li data-mode="N" class="active"><a href="#">미처리</a></li>
      <li data-mode="Y"><a href="#">처리</a></li>
      <li data-mode=""><a href="#">전체</a></li>
    </ul>

    <input type="hidden" id="tabValue" value="N"/>
  </div> -->

  <!-- 테이블 -->
  <div class="container-fluid nwt-table" style="width: 100%;"> 
    <table class="table table-bordered table-striped">
    <colgroup>
        <col width="4%"/>
        <col width="3%"/>
        <col width="5%"/>
        <col width="7%"/>
        <col width="3%"/>
        <col width="4%"/>
        <col width="6%"/>
        <col width="6%"/>
        <col width="4%"/>
        <col width="4%"/>
        <col width="4%"/>
        <col width="4%"/>
        <col width="3%"/>
        <col width="3%"/>
        <col width="4%"/>
        <col width="4%"/>
        <col width="4%"/>
        <col width="4%"/>
        <col width="4%"/>
        <col width="9%"/>
      </colgroup>
      <thead>
        <tr class="active">
          <th class="text-center" rowspan="2">정산년월</th>
          <th class="text-center" rowspan="2">구분</th>
          <th class="text-center" rowspan="2">지역</th>
          <th class="text-center" rowspan="2">업체명</th>
          <th class="text-center" rowspan="2">거래여부</th>
          <th class="text-center" rowspan="2">대표자</th>
          <th class="text-center" rowspan="2">연락처</th>
          <th class="text-center" rowspan="2">전월미수누계</th>
          <th class="text-center" rowspan="2">주문횟수</th>
          <th class="text-center" rowspan="2">공급금액</th>
          <th class="text-center" rowspan="2">세액</th>
          <th class="text-center" rowspan="2">합계액</th>
          <th class="text-center" rowspan="2">확정여부</th>
          <th class="text-center" rowspan="2">입금여부</th>
          <th class="text-center" rowspan="2">입금일</th>
          <th class="text-center" rowspan="2">입금액</th>
          <th class="text-center" rowspan="2">미입금액</th>
          <th class="text-center" colspan="2">입금</th>
          <th class="text-center" rowspan="2">기능</th>
        </tr>
        <tr class="active">
          <th class="text-center">처리자</th>
          <th class="text-center">처리일시</th>
        </tr>
      </thead>
      <tbody id="dataTable">                                                                 
      </tbody>

      <tr id="indicatorTable" style="display : none">   
        <td colspan="28"> 
           <?php	require $_SERVER['DOCUMENT_ROOT']."/include/indicator.html"; ?>
        </td>
      </tr> 
    </table>
  </div>

  
  <!-- pagination -->
  <div class="container-fluid">
    <div class="text-center">
      <ul class="pagination">
      </ul>
    </div>
  </div>


  <!-- 결제버튼 모달  -->
  <?php	require "./payDateModal.html"; ?>

<!-- footer.html -->
<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/footerShop.html";
?>