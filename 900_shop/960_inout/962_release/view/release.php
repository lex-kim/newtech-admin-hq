
  <!-- 페이지 카테고리 -->
  <div class="container-fluid mb20px">
    <h3 class="nwt-category"><small>입출고 관리&nbsp;&nbsp;></small>출고 관리</h3>
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
                              <option value="">=기간=</option>
                              <option value="ES_ISSUE_DATE">출고일</option>
                              <option value="ES_CANCEL_DTHMS">취소일</option>
                            </select>
                            <!-- 날짜선택 -->
                            <input type="text" class="form-control input-md" name="START_DT" id="START_DT" autocomplete="off"  placeholder="입력" maxlength="10">
                            <span class="glyphicon glyphicon-calendar search-glypicon"></span>
                            <label for="END_DT" class="nwt-form-label">~</label>                            
                            <input type="text" class="form-control" name="END_DT" id="END_DT" autocomplete="off"  placeholder="입력" maxlength="10">
                            <span class="glyphicon glyphicon-calendar search-glypicon"></span>                            
                          </div>

                          <!-- 직원명 입력 -->
                          <!-- <div class="col-md-1">
                            <input type="text" class="form-control" name="SEARCH_SALESMAN" id="SEARCH_SALESMAN" placeholder="직원명 입력">                            
                          </div> -->

                          <!-- 키필드 -->
                          <div class="col-md-2 frmGroup2-2">
                            <label for="keyfield" class="sr-only">키필드</label>
                            <select class="form-control" id="keyfield" name="keyfield">
                              <option value="">=키필드=</option>
                              <option value="EP_NM">매입처</option>
                              <option value="EI_NM">상품명</option>
                              <option value="ISSUE_USER_NAME">직원명</option>
                              <option value="ES_ISSUE_USERID">직원ID</option>
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
          <!-- 엑셀다운로드-->
          <button type="button" class="btn btn-success" onclick="excelDownload()"><span class="glyphicon glyphicon-download-alt"></span>&nbsp;엑셀 다운로드</button>
          <!-- 등록btn / modal handler -->
          <button type="button" class="btn btn-primary" id="add-new-btn" onclick="showAddModal()">등록</button>
        </div>
      </div>

    </div>
  </div>


  <!-- 테이블 -->
  <div class="container-fluid nwt-table" style="width: 100%;"> 
    <table class="table table-bordered table-striped">
      <colgroup>
        <col width="1%">
        <col width="2.5%">
        <col width="3%">
        <col width="2%">
        <col width="5%">
        <col width="2%">
        <col width="2%">
        <col width="3%">
        <col width="3%">
        <col width="6%">
        <col width="3%">
        <col width="3%">
        <col width="2%">
        <col width="3%">
        <col width="2%">
        <col width="1%">
        <col width="1%">
      </colgroup>
      <thead>
        <tr class="active">
          <th class="text-center">순번</th>
          <th class="text-center">출고일</th>
          <th class="text-center">카테고리</th>
          <th class="text-center">매입처</th>
          <th class="text-center">상품</th>
          <th class="text-center">규격/용량</th>
          <th class="text-center">단가</th>
          <th class="text-center">수량</th>
          <th class="text-center">변동일</th>
          <th class="text-center">메모</th>
          <th class="text-center">직원명</th>
          <th class="text-center">작성일시</th>
          <th class="text-center">작성자</th>
          <th class="text-center">취소일시</th>
          <th class="text-center">취소자</th>
          <th class="text-center">취소</th>
          <!-- <th class="text-center">출고증</th> -->
          <th class="text-center">삭제</th>
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
    
<!-- footer.html -->
<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/footerShop.html";
?>