  
  
<style>
.nwt-search-form .bootstrap-select>.dropdown-toggle.bs-placeholder, .bootstrap-select>.dropdown-toggle.bs-placeholder:active, .bootstrap-select>.dropdown-toggle.bs-placeholder:focus, .bootstrap-select>.dropdown-toggle.bs-placeholder:hover {
    height: 28px;
}
.bootstrap-select .btn {
	padding-top: 8px;
    height: 28px !important;
}
</style>
  <!-- 페이지 카테고리 -->
  <div class="container-fluid mb20px">
    <h3 class="nwt-category"><small>상품관리&nbsp;&nbsp;></small>상품관리</h3>
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
                          <div class="col-md-3 frmGroup3">
                            <label for="START_DT" class="sr-only">기간</label>
                            <!-- 기간선택 -->
                            <select class="form-control input-md" id="dateField" name="dateField">
                              <option value="EI.WRT_DTHMS">등록일</option>
                            </select>
                            <!-- 날짜선택 -->
                            <input type="text" class="form-control input-md" name="START_DT" id="START_DT" autocomplete="off"  placeholder="입력" maxlength="10">
                            <span class="glyphicon glyphicon-calendar search-glypicon"></span>
                            <label for="END_DT" class="nwt-form-label">~</label>                            
                            <input type="text" class="form-control" name="END_DT" id="END_DT" autocomplete="off"  placeholder="입력" maxlength="10">
                            <span class="glyphicon glyphicon-calendar search-glypicon"></span>                            
                          </div>
                          
                          <!-- 관리구분 -->
                          <div class="col-md-1">
                            <label for="SEARCH_MANAGEMENT_DIVISION" class="sr-only">관리구분</label>
                            <select class="form-control" id="SEARCH_MANAGEMENT_DIVISION" name="SEARCH_MANAGEMENT_DIVISION">
                               <?php echo getOption($this->optionArray['EC_MGMT_TYPE'], "관리구분" ,"") ?>
                            </select>                            
                          </div>

                          <!-- 카테고리구분 -->
                          <div class="col-md-1">
                            <label for="searchCategoryType" class="sr-only">카테고리구분</label>
                            <input type="text" class="form-control" name="categoryCd" id="categoryCd" placeholder="카테고리 입력">  
                            <!-- <select name="categoryID" id="categoryID"
                                   class="form-control selectpicker"  data-live-search="true"
                            >
                                <option value="">=카테고리=</option> -->
                                <?php
                                   // foreach($categoryArray as $key => $item){
                                ?>
                                    <!-- <option value="<?php echo $item['thisCategoryID']?>"><?php echo $item['thisCategory']?></option> -->
                                <?php
                                   // }
                                ?>
                            <!-- </select>                            -->
                          </div>

                          <!-- 거래구분 -->
                          <div class="col-md-1">
                            <label for="buyerTypeCode" class="sr-only">매입처구분</label>
                            <select class="form-control" id="buyerTypeCode" name="buyerTypeCode">
                               <?php echo getOption($this->optionArray['EC_BUYER_TYPE'], "매입처구분" ,"") ?>
                            </select>                            
                          </div>

                          <!-- 매입처 입력 -->
                          <div class="col-md-1">
                            <input type="text" class="form-control" name="partnerName" id="partnerName" placeholder="매입처 입력">                            
                          </div>

                          <!-- 상품명 입력 -->
                          <div class="col-md-1">
                            <input type="text" class="form-control" name="SEARCH_PRODUCT_NM" id="SEARCH_PRODUCT_NM" placeholder="상품명 입력">                            
                          </div>

                          <!-- 가격 -->
                          <div class="col-md-3 frmGroup3">
                            <label for="START_PRICE" class="sr-only">가격</label>
                            <!-- 기준 -->
                            <select class="form-control input-md" id="priceField" name="priceField">
                              <option value="">=가격=</option>
                              <option value="EI.EI_BUY_PRICE">매입가</option>
                              <option value="EI.EI_WHOLESALE_PRICE">도매가</option>
                              <option value="EI.EI_RETAIL_PRICE">소비자가</option>
                              <option value="EI.EI_SALE_PRICE">판매가</option>
                            </select>
                            <!-- 금액범위 -->
                            <input type="text" class="form-control input-md" name="START_PRICE" id="START_PRICE" placeholder="입력" maxlength="10">
                            <label for="END_PRICE" class="nwt-form-label">~</label>                            
                            <input type="text" class="form-control" name="END_PRICE" id="END_PRICE" placeholder="입력" maxlength="10">                      
                          </div>
                          

                        </div>
                        <div class="row frm-last-row">

                          <div class="col-md-2 frmGroup2-2">
                            <label for="keyField" class="sr-only">키필드</label>
                            <select class="form-control" id="keyField" name="keyField">
                            <option value="">=키필드=</option>
                            <option value="EI.EI_MANUFACTURER_NM">제조사</option>
                            <option value="EI.EI_ORIGIN_NM">원산지</option>
                            <option value="EI.EI_BRAND_NM">브랜드</option>
                            </select>
                            <input type="text" class="form-control" name="keyword" id="keyword" placeholder="입력">                            
                          </div>
                        
                         <!-- 품절여부 -->
                         <div class="col-md-1">
                            <label for="searchSoldout" class="sr-only">품절여부</label>
                            <select class="form-control" id="searchSoldout" name="searchSoldout">
                               <option value="">=품절여부=</option>
                               <option value="Y">품절</option>
                               <option value="N">판매중</option>
                            </select>                            
                          </div>

                          <div class="col-md-3 frmGroup3">
                            <label for="stockRemainsStart" class="sr-only">재고수량</label>
                            <input type="text" class="form-control input-md" onkeyup="isNumber(event)"
                                name="stockRemainsStart" id="stockRemainsStart" autocomplete="off"  placeholder="재고수량 시작" >
                            <label for="stockRemainsEnd" class="nwt-form-label">~</label>                            
                            <input type="text" class="form-control" onkeyup="isNumber(event)"
                              name="stockRemainsEnd" id="stockRemainsEnd" autocomplete="off"  placeholder="재고수량 종료" >                    
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
        <div class="form-inline f-right table-top-uplode">
          <div>
            <input type="hidden" id="MFF_ID" name="MFF_ID">
            <input type="hidden" id="FILE_DEL_YN" name="FILE_DEL_YN" value="N">
            <input id="BANK_FILE_NM" name="BANK_FILE_NM" type="file" class="form-control input-md">
            <div class="excel-down-file">
              <div class="excel-down">
                <!-- * 아래 업로드파일 양식에 맞춰 <br>&nbsp;&nbsp;&nbsp;모든 서식을 제거한csv파일로 업로드 가능합니다. -->
                <a href="exampleCsv.html">
                  <!-- <img src="/images/excelicon.png" alt=""> -->
                  <div>* 상품관리 업로드파일(CSV) 양식 다운로드</div>
                </a>
              </div>
            </div>
          </div>
          <div>
            <button type="button" class="btn btn-success" onclick="showAddModal()">엑셀 업로드</button>
            <!-- 엑셀다운로드-->
            <button type="button" class="btn btn-success" onclick="excelDownload()"><span class="glyphicon glyphicon-download-alt"></span>&nbsp;엑셀 다운로드</button>
            <!-- 등록btn / modal handler -->
            <button type="button" class="btn btn-primary" id="add-new-btn" onclick="showAddModify()">등록</button>
          </div>
    
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
          <th class="text-center">관리구분</th>
          <th class="text-center">카테고리</th>
          <th class="text-center">매입처구분</th>
          <th class="text-center">매입처</th>
          <th class="text-center">상품이미지</th>
          <th class="text-center">상품코드</th>
          <th class="text-center">상품명</th>
          <th class="text-center">규격/용량</th>
          <th class="text-center">포장단위</th>
          <th class="text-center">최소구매수량</th>
          <th class="text-center">매입가</th>
          <th class="text-center">도매가</th>
          <th class="text-center">소비자가</th>
          <th class="text-center">판매가</th>
          <th class="text-center">적립금</th>
          <th class="text-center">배송정책</th>
          <th class="text-center">제조사</th>
          <th class="text-center">원산지</th>
          <th class="text-center">브랜드</th>
          <th class="text-center">카달로그</th>
          <th class="text-center">품절여부</th>
          <th class="text-center">재고수량</th>
          <th class="text-center">등록자</th>
          <th class="text-center">등록일시</th>
          <th class="text-center">갱신자</th>
          <th class="text-center">갱신일</th>
          <th class="text-center">발주</th>
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