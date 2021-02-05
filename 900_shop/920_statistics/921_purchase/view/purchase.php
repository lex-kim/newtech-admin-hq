
<!-- 페이지 카테고리 -->
<div class="container-fluid mb20px">
  <h3 class="nwt-category"><small>현황통계&nbsp;&nbsp;></small>매입처별 제품거래 통계</h3>
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
                      <div class="col-md-2 frmGroup2">
                        <label for="START_DT" class="sr-only">기간</label>
                        <input type="text" class="form-control" name="START_DT" id="START_DT" autocomplete="off"  placeholder="기간" maxlength="10">
                        <span class="glyphicon glyphicon-calendar search-glypicon"></span>
                        <label for="END_DT" class="nwt-form-label">~</label>                            
                        <input type="text" class="form-control" name="END_DT" id="END_DT" autocomplete="off"  placeholder="기간" maxlength="10">
                        <span class="glyphicon glyphicon-calendar search-glypicon"></span>                            
                      </div>

                      <!-- 카테고리명 입력 -->
                      <div class="col-md-1">
                        <input type="text" class="form-control" name="SEARCH_CATEGORY" id="SEARCH_CATEGORY" placeholder="매입처명 입력">                            
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
    <!-- <?php	require $_SERVER['DOCUMENT_ROOT']."/900_shop/920_statistics/include/statisticsCnt.html"; ?> -->
    <?php	require $_SERVER['DOCUMENT_ROOT']."/include/listCnt.html"; ?>
    <!-- 테이블 우측 상단 btn -->
    <div class="col-lg-6 clearfix">
      <div class="form-inline f-right">
        <!-- 엑셀다운로드-->
        <button type="button" class="btn btn-success"><span class="glyphicon glyphicon-download-alt"></span>&nbsp;엑셀 다운로드</button>
      </div>
    </div>

  </div>
</div>


<!-- 테이블 -->
<div class="container-fluid nwt-table" style="width: 100%;"> 
  <table class="table table-bordered table-striped">
    <colgroup>
      <col width="10%"/>
      <col width="30%"/>
      <col width="15%"/>
      <col width="15%"/>
    </colgroup>
    <thead>
      <tr class="active">
        <th class="text-center">순번</th>
        <th class="text-center">매입처명</th>
        <th class="text-center">총 수량</th>
        <th class="text-center">총 금액</th>
      </tr>
    </thead>
    <tbody id="dataTable">                                                                 
    </tbody>

    <tr id="indicatorTable" style="display : none">   
        <td colspan="16"> 
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

<!-- footer.html -->
<?php
require $_SERVER['DOCUMENT_ROOT']."/include/footerShop.html";
?>