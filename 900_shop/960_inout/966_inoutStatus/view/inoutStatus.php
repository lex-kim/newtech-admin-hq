
<!-- 페이지 카테고리 -->
<div class="container-fluid mb20px">
  <h3 class="nwt-category"><small>입출고 현황&nbsp;&nbsp;></small>입출고 현황</h3>
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

                        <!-- 관리구분 -->
                        <div class="col-md-1">
                          <label for="mgmtTypeCode" class="sr-only">관리구분</label>
                          <select class="form-control" id="mgmtTypeCode" name="mgmtTypeCode">
                            <?php echo getOption($this->optionArray['EC_MGMT_TYPE'], "관리구분" ,"") ?>
                          </select>                            
                        </div>

                        <!-- 관리구분 -->
                        <div class="col-md-1">
                          <label for="categoryCode" class="sr-only">상품카테고리</label>
                          <select id="categoryCode"  name="categoryCode[]" 
                                  data-style="btn-default" title="=상품카테고리="
                                  class="selectpicker" multiple
                                  data-live-search="true" data-live-search-placeholder="Search" 
                                  data-selected-text-format="count>3"
                                  data-actions-box="true"
                          >
                            <?php
                              foreach($categoryArray as $index => $item){
                            ?>
                              <option value="<?=$item['thisCategoryID']?>"><?=$item['thisCategory']?></option>
                            <?php
                              }
                            ?>
                          </select>                            
                        </div>
                  
                      </div>


                    </div> 

                    <!-- 검색버튼 wrap -->
                    <div class="frm-search-wrap">
                      <!-- 검색btn -->
                      <div class="row">
                          <div class="col-md-12 text-right" >
                            <button type="submit" class="btn btn-primary" id="frmSearch" style="margin-top: -10px"><span class="glyphicon glyphicon-search"></span>&nbsp;검색</button>
                          </div>
                      </div> 
                    </div>

                    <input type="hidden" id="sortAsc" name="sortAsc" value=""/>
                    <input type="hidden" id="sortField" name="sortField" value=""/>

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
      </div>
    </div>

  </div>
</div>

<!-- 테이블 -->
<div class="container-fluid nwt-table">
  <table class="table table-bordered table-striped">
    <thead>
      <tr class="active">
        <th class="text-center" rowspan="3">순번</th>
        <th class="text-center" rowspan="3">관리구분</th>
        <th class="text-center" rowspan="3">카테고리</th>
        <th class="text-center" rowspan="3">상품명</th>
        <th class="text-center" rowspan="3">매입단가</th>
        <th class="text-center" colspan="6">재고</th>
        <th class="text-center" colspan="6">판매</th>
        <th class="text-center" colspan="2">분실</th>
        <th class="text-center" colspan="2">폐기</th>
        <th class="text-center table-array" rowspan="3">3개월 평균<br>출고수량
          <div class="arrayicon">
            <span class="glyphicon glyphicon-chevron-up" onclick="getSortList('Y', 'StockOutAverage3M');"></span>
            <span class="glyphicon glyphicon-chevron-down" onclick="getSortList('N', 'StockOutAverage3M');"></span>
          </div>
        </th>
        <th class="text-center table-array" rowspan="3">발주수요량
          <div class="arrayicon">
            <span class="glyphicon glyphicon-chevron-up" onclick="getSortList('Y', 'stockOrderRequire');"></span>
            <span class="glyphicon glyphicon-chevron-down" onclick="getSortList('N', 'stockOrderRequire');"></span>
          </div>
        </th>
        <th class="text-center table-array" rowspan="3">재고율
          <div class="arrayicon">
            <span class="glyphicon glyphicon-chevron-up" onclick="getSortList('Y', 'remainRatio');"></span>
            <span class="glyphicon glyphicon-chevron-down" onclick="getSortList('N', 'remainRatio');"></span>
          </div>
        </th>
        <th class="text-center" rowspan="3">발주</th>
      </tr>
      <tr class="active">
        <th class="text-center" colspan="2">소계</th>
        <th class="text-center" colspan="2">창고</th>
        <th class="text-center" colspan="2">차량</th>
        <th class="text-center" colspan="2">소계</th>
        <th class="text-center" colspan="2">일반판매</th>
        <th class="text-center" colspan="2">쇼핑몰</th>
        <th class="text-center" rowspan="2">수량</th>
        <th class="text-center" rowspan="2">금액</th>
        <th class="text-center" rowspan="2">수량</th>
        <th class="text-center" rowspan="2">금액</th>
      </tr>
      <tr class="active">
        <th class="text-center">수량</th>
        <th class="text-center">금액</th>
        <th class="text-center">수량</th>
        <th class="text-center">금액</th>
        <th class="text-center">수량</th>
        <th class="text-center">금액</th>
        <th class="text-center">수량</th>
        <th class="text-center">금액</th>
        <th class="text-center">수량</th>
        <th class="text-center">금액</th>
        <th class="text-center">수량</th>
        <th class="text-center">금액</th>
      </tr>
    </thead>
    <tbody id="dataTable">
    </tbody>

    <tr id="indicatorTable" style="display : none">   
      <td colspan="25"> 
          <?php	require $_SERVER['DOCUMENT_ROOT']."/include/indicator.html"; ?>
      </td>
    </tr> 
  </table>
</div>

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