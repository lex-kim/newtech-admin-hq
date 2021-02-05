<!-- 페이지 카테고리 -->
  <div class="container-fluid mb20px">
    <h3 class="nwt-category"><small>견적서관리&nbsp;&nbsp;></small>견적서 관리</h3>
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
                        <div class="row frm-last-row">
                          
                          <!-- 기간 -->
                          <div class="col-md-2 frmGroup3">
                            <label for="START_DT" class="sr-only">기간</label>
                              <!-- 기간선택 -->
                              <select class="form-control input-md" id="dateField" name="dateField">
                                  <option value="EQ_ISSUE_DATE">작성일</option>
                                  <option value="EQ_REQ_DTHMS">요청일</option>
                                  <option value="EQ_PUBLISHED_DATE">발행일</option>
                                  <option value="EQ_SEND_FAX_DTHMS">팩스전송일</option>
                                  <option value="EQ_SEND_LMS_DTHMS">문자전송일</option>
                              </select>
                              <input type="text" class="form-control input-md" autocomplete="off" name="START_DT" id="START_DT" placeholder="작성일" maxlength="10">
                              <span class="glyphicon glyphicon-calendar search-glypicon"></span>
                              <label for="END_DT" class="nwt-form-label">~</label>                            
                              <input type="text" class="form-control" name="END_DT"  autocomplete="off" id="END_DT" placeholder="작성일" maxlength="10">
                              <span class="glyphicon glyphicon-calendar search-glypicon"></span>                            
                          </div>

                          <!-- 견적서번호 -->
                          <div class="col-md-1">
                            <label for="quotationID" class="sr-only">견적서번호</label>
                            <input type="text" class="form-control" name="quotationID" id="quotationID" placeholder="견적서번호 입력">                            
                          </div>

                          <!-- 상태 -->
                          <div class="col-md-1">
                            <label for="quotationDoneYN" class="sr-only">견적상태</label>
                            <select class="form-control" id="quotationDoneYN" name="quotationDoneYN">
                              <?php echo getOption($this->optionArray['EC_QUOTATION_STATUS'], "견적상태" ,"") ?>
                            </select>                            
                          </div>

                          <!-- 거래여부 -->
                          <div class="col-md-1">
                            <label for="contractStatusCode" class="sr-only">거래여부</label>
                            <select class="form-control" id="contractStatusCode" name="contractStatusCode">
                            <?php echo getOption($this->optionArray['CONTRACT_TYPE'], "거래여부" ,"") ?>
                            </select>                            
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

                          <!-- 업체명 -->
                          <div class="col-md-2">
                            <label for="garageName" class="sr-only">업체명</label>
                            <input type="text" class="form-control" name="garageName" id="garageName" placeholder="업체명 입력">                            
                          </div>

                          <!-- 키필드 -->
                          <div class="col-md-2 frmGroup2-2">
                            <label for="keyfield" class="sr-only">수신자</label>
                            <select class="form-control" id="keyfield" name="keyfield">
                              <option value="">=수신자=</option>
                              <option value="TO_NAME">수신자성명</option>
                              <option value="TO_TEL">수신자전화번호</option>
                              <option value="FROM_ID">작성자ID</option>
                              <option value="NOTE_TXT">특이사항</option>
                            </select>
                            <input type="text" class="form-control" name="keyword" id="keyword" placeholder="입력">                            
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
          <button type="button" class="btn btn-success" onclick="excelDownload()"><span class="glyphicon glyphicon-download-alt"></span>&nbsp;엑셀 다운로드</button>
          <button type="button" class="btn btn-primary" onclick="onClickEstimate()">견적서작성</button>
        </div>
      </div>

    </div>
  </div>


  <!-- 테이블 -->
  <div class="container-fluid nwt-table" style="width: 100%;">
    <table class="table table-bordered table-striped">
  
      <thead>
        <tr class="active">
          <th class="text-center" rowspan="2">순번</th>
          <th class="text-center" rowspan="2">작성일</th>
          <th class="text-center" rowspan="2">견적서번호</th>
          <th class="text-center" rowspan="2">견적상태</th>
          <th class="text-center" rowspan="2">거래여부</th>
          <th class="text-center" rowspan="2">구분</th>
          <th class="text-center" rowspan="2">지역</th>
          <th class="text-center" rowspan="2">공업사명</th>
          <th class="text-center" rowspan="2">업체명</th>
          <th class="text-center" rowspan="2">수신자</th>
          <th class="text-center" rowspan="2">수신자전화번호</th>
          <th class="text-center" rowspan="2">수신자이메일</th>
          <!-- <th class="text-center" colspan="6">품목</th> -->
          <th class="text-center" rowspan="2">품목</th>
          <th class="text-center" rowspan="2">총 공급가액</th>
          <th class="text-center" rowspan="2">총 세액</th>
          <th class="text-center" rowspan="2">합계금액</th>
          <th class="text-center" rowspan="2">배송방법</th>
          <th class="text-center" rowspan="2">결제방법</th>
          <th class="text-center" rowspan="2">요청자</th>
          <th class="text-center" rowspan="2">요청일</th>
          <th class="text-center" rowspan="2">작성자</th>
          <th class="text-center" rowspan="2">발행일</th>
          <th class="text-center" rowspan="2">팩스발송여부</th>
          <th class="text-center" rowspan="2">문자발송여부</th>
          <th class="text-center" rowspan="2">팩스발송일</th>
          <th class="text-center" rowspan="2">문자발송일</th>
          <th class="text-center" rowspan="2">열람일</th>
          <th class="text-center" rowspan="2">기능</th>
          <th class="text-center" rowspan="2">삭제</th>
        </tr>
        <!-- <tr class="active">
          <th class="text-center">품목</th>
          <th class="text-center">규격</th>
          <th class="text-center">단가</th>
          <th class="text-center">수량</th>
          <th class="text-center">공급가</th>
          <th class="text-center">세액</th>
        </tr> -->
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

</body>
<!-- footer.html -->
<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/footerShop.html";
?>