<style>
table.ui-datepicker-calendar { display:none; }

</style>

  <!-- 페이지 카테고리 -->
  <div class="container-fluid mb20px">
    <h3 class="nwt-category"><small>현황통계&nbsp;&nbsp;></small>제품별 매입 통계</h3>
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
                          <input type="text" class="form-control" name="START_DT" id="START_DT" autocomplete="off"  placeholder="기간" maxlength="10" required/>
                          <span class="glyphicon glyphicon-calendar search-glypicon"></span>
                          <label for="END_DT" class="nwt-form-label">~</label>                            
                          <input type="text" class="form-control" name="END_DT" id="END_DT" autocomplete="off"  placeholder="기간" maxlength="10" required/>
                          <span class="glyphicon glyphicon-calendar search-glypicon"></span>                            
                        </div>

                        <!-- 관리구분 -->
                        <div class="col-md-1">
                          <label for="mgmtTypeCode" class="sr-only">관리구분</label>
                          <select class="form-control" id="mgmtTypeCode" name="mgmtTypeCode">
                            <?php echo getOption($this->optionArray['EC_MGMT_TYPE'], "관리구분" ,"") ?>
                          </select>                            
                        </div>

                        <!-- 매입처ID 입력 -->
                        <div class="col-md-2">
                          <input type="text" class="form-control" name="searchPartnerID" onchange="onchangePartnerID()" id="searchPartnerID" placeholder="매입처ID 입력 및 선택(자동완성)">                            
                          <input type="hidden" name="partnerID" id="partnerID">   
                        </div>

                        <!-- 매입처명 입력 -->
                        <div class="col-md-1">
                          <input type="text" class="form-control" name="searchPartnerName" id="searchPartnerName" placeholder="매입처명 입력">                            
                        </div>

                        <!-- 카테고리 -->
                        <div class="col-md-2">
                          <label for="categoryCode" class="sr-only">상품카테고리</label>
                          <select id="categoryCode"  name="categoryCode[]" 
                                  data-style="btn-default" title="=상품카테고리="
                                  class="form-control selectpicker" multiple
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

                        <!-- 상품명 입력 -->
                        <div class="col-md-1">
                          <input type="text" class="form-control" name="searchGoodsName" id="searchGoodsName" placeholder="상품명 입력">                            
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
          <button type="button" class="btn btn-success" onclick="excelDownload()"><span class="glyphicon glyphicon-download-alt" ></span>&nbsp;엑셀 다운로드</button>
        </div>
      </div>

    </div>
  </div>


  <!-- 테이블 -->
  <div class="container-fluid nwt-table" style="width: 100%;"> 
    <table class="table table-bordered table-striped">
      <colgroup>
        <col width="2%"/>
        <col width="5%"/>
        <col width="8%"/>
        <col width="6%"/>
        <col width="6%"/>
        <col width="5%"/>
        <col width="6%"/>
        <col width="6%"/>
        <col width="6%"/>
        <col width="6%"/>
      </colgroup>
      <thead id="headerTable">
        <tr class="active">
          <th class="text-center" rowspan="2">순번</th>
          <th class="text-center" rowspan="2">매입처ID</th>
          <th class="text-center" rowspan="2">매입처</th>
          <th class="text-center" rowspan="2">사무실전화</th>
          <th class="text-center" rowspan="2">사무실팩스</th>
          <th class="text-center" rowspan="2">담당자</th>
          <th class="text-center" rowspan="2">담당자휴대폰</th>
          <th class="text-center" rowspan="2">카테고리</th>
          <th class="text-center" rowspan="2">상품코드</th>
          <th class="text-center" rowspan="2">상품명</th>
          <?php 
            foreach($headerArray as $index => $item){
          ?>
              <th class="text-center" colspan="4"><?=$item['YYYYMM']?></th>
          <?php
            }
          ?>
        </tr>

        <tr class="active">
          <?php 
            foreach($headerArray as $index => $item){
          ?>
            <th class="text-center">구매수량</th>
            <th class="text-center">구매금액</th>
            <th class="text-center">판매수량</th>
            <th class="text-center">판매금액</th>
          <?php
            }
          ?>
        </tr>
      </thead>
      <tbody id="dataTable">                                                           
      </tbody>

      <tr id="indicatorTable" style="display : none">   
        <td id="indicatorTableTD" colspan="<?=count($headerArray)*4+10?>"> 
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
<script>

  function errorAlert(){
    alert("연도 헤더리스트 인코딩에 실패하였습니다.");
    if(opener == null){
      history.back(-1);
    }else{
      window.close();
    }
  }

  setAutoComplete('<?=json_encode($buyMngtArray, true)?>');                // 자동완성을 위한 상품 리스트
  let headerArray = [];
  try{
    headerArray = JSON.parse('<?=json_encode($headerArray, true)?>'); 
    
    if(headerArray.length == 0){
      errorAlert();
    }
  }catch{
    errorAlert();
  }

</script>