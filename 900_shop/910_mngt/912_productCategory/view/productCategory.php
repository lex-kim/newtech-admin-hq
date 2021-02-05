

  <!-- 페이지 카테고리 -->
  <div class="container-fluid mb20px">
    <h3 class="nwt-category"><small>상품관리&nbsp;&nbsp;></small>상품 카테고리 관리</h3>
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
                        <div class="row frm-last-row">


                          <!-- 기간 -->
                          <div class="col-md-2 frmGroup3">
                            <label for="START_DT" class="sr-only">기간</label>
                            <!-- 기간선택 -->
                            <select class="form-control input-md" id="period " name="period">
                              <option value="WRT_DTHMS">등록일</option>
                            </select>
                            <!-- 날짜선택 -->
                            <input type="text" class="form-control" name="START_DT" id="START_DT" autocomplete="off"  placeholder="입력" maxlength="10">
                            <span class="glyphicon glyphicon-calendar search-glypicon"></span>
                            <label for="END_DT" class="nwt-form-label">~</label>                            
                            <input type="text" class="form-control" name="END_DT" id="END_DT" autocomplete="off"  placeholder="입력" maxlength="10">
                            <span class="glyphicon glyphicon-calendar search-glypicon"></span>                            
                          </div>

                        
                          <!-- 노출여부 -->
                          <div class="col-md-1">
                              <label for="SEARCH_EXPOSURE_YN" class="sr-only">노출여부</label>
                              <select class="form-control" id="SEARCH_EXPOSURE_YN" name="SEARCH_EXPOSURE_YN">
                                <option value="">=노출여부=</option>
                                <option value="Y">노출</option>
                                <option value="N">미노출</option>
                              </select>                            
                          </div>

                          <!-- 상위 카테고리명 입력 -->
                          <div class="col-md-1">
                            <input type="text" class="form-control" name="SEARCH_CATEGORY" id="SEARCH_CATEGORY" placeholder="상위카테고리명 입력">                            
                          </div>

                           <!-- 하위 카테고리명 입력 -->
                           <div class="col-md-1">
                            <input type="text" class="form-control" name="childCategoryName" id="childCategoryName" placeholder="하위카테고리명 입력">                            
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
    <div class="row" style="position:relative">
      
      <!-- 리스트개수 -->
      <?php	require $_SERVER['DOCUMENT_ROOT']."/include/listCnt.html"; ?>
      <div class="table-top-etc">
        <input type="checkbox" id="extend" name="extend"/>
        <label for="extend" class="extend" style="cursor:pointer">상위 카테고리만 보기</label>
      </div>
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
      <thead>
        <tr class="active">
          <th class="text-center">순번</th>
          <th class="text-center">상위카테고리명 (코드)</th>
          <th class="text-center">카테고리 추가</th>
          <th class="text-center">카테고리명 (코드)</th>
          <th class="text-center">노출여부</th>
          <th class="text-center">노출순서</th>
          <th class="text-center">등록자</th>
          <th class="text-center">등록일시</th>
          <th class="text-center">수정</th>
        </tr>
      </thead>
      <tbody id="productList">                                                                 
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