

<!-- 페이지 카테고리 -->
<div class="container-fluid mb20px">
  <h3 class="nwt-category"><small>입출고 현황&nbsp;&nbsp;></small>지역별 수불현황</h3>
</div>


<?php if(empty($cDivisionArray)):?>
    <div class="container-fluid nwt-search-form" style="text-align:center; margin-top:120px">
        <h2>등록된 지역 데이터가 존재하지 않습니다.</h2>
        <span style="font-size:13px; color:gray">지역 등록 후 이용 가능한 페이지 입니다.</span>
    </div>
  </body>
  </html>

<?php else:?>
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
                            <label for="dateField" class="sr-only">기간</label>
                            <!-- 기간선택 -->
                            <select class="form-control" id="dateField" name="dateField">
                              <option value="ES_ISSUE_DATE">출고일</option>
                            </select>
                            <!-- 날짜선택 -->
                            <input type="text" class="form-control" name="START_DT" id="START_DT" autocomplete="off" placeholder="입력"/>
                            <span class="glyphicon glyphicon-calendar search-glypicon"></span>
                            <label for="END_DT" class="nwt-form-label">~</label>                            
                            <input type="text" class="form-control" name="END_DT" id="END_DT" autocomplete="off" placeholder="입력"/>
                            <span class="glyphicon glyphicon-calendar search-glypicon"></span>                            
                          </div>

                          <!-- 관리구분 -->
                          <div class="col-md-1">
                            <label for="mgmtTypeCode" class="sr-only">관리구분</label>
                            <select class="form-control" id="mgmtTypeCode" name="mgmtTypeCode">
                              <?php echo getOption($this->optionArray['EC_MGMT_TYPE'], "관리구분" ,"") ?>
                            </select>                            
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


                          <!--상품 -->
                          <div class="col-md-1">
                            <label for="goodsName" class="sr-only">상품</label>
                            <input type="text" class="form-control" name="goodsName" id="goodsName" placeholder="상품명 입력">                            
                          </div>

                          <!--상품ID -->
                          <div class="col-md-2">
                            <label for="itemID" class="sr-only">상품ID</label>
                            <input type="text" class="form-control" onchange="onchangeItemID()" name="selectItemID" id="selectItemID" placeholder="상품ID 입력 및 선택(자동완성)"/>                      
                            <input type="hidden" name="itemID" id="itemID"/>                      
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
          <th class="text-center" rowspan="3">출고일</th>
          <th class="text-center" rowspan="3">카테고리</th>
          <th class="text-center" rowspan="3">상품명</th>
          <th class="text-center" rowspan="3">매입단가</th>
          <th class="text-center" rowspan="3">보험청구단가</th>
          <?php 
            foreach($cDivisionArray as $index => $item){
          ?>
            <th class="text-center" id="<?=$item['divisionID']?>" colspan="4"><?=$item['divisionName']?></th>
          <?php
            }
          
          ?>  
        </tr>

        <tr class="active">
          <?php 
            foreach($cDivisionArray as $index => $item){
          ?>
            <th class="text-center" colspan="2">공급</th>
            <th class="text-center" colspan="2">회수</th>
          <?php
            }
          
          ?>
        </tr>
        <tr class="active">
          <?php 
            foreach($cDivisionArray as $index => $item){
          ?>
            <th class="text-center">수량</th>
            <th class="text-center">금액</th>
            <th class="text-center">수량</th>
            <th class="text-center">금액</th>
          <?php
            }
          
          ?>
          
        </tr>
      </thead>
      <tbody id="dataTable">
      </tbody>

      <tr id="indicatorTable" style="display : none">   
        <td colspan="<?=count($cDivisionArray)*10+6?>"> 
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

  <?php require $_SERVER['DOCUMENT_ROOT']."/include/footerShop.html";?>
  <script>

    function errorAlert(){
      alert("지역 헤더리스트 인코딩에 실패하였습니다.");
      if(opener == null){
        history.back(-1);
      }else{
        window.close();
      }
    }
    
    setAutoComplete('<?=json_encode($cProductArray, true)?>');                // 자동완성을 위한 상품 리스트
    let headerArray = [];
    try{
      headerArray = JSON.parse('<?=json_encode($cDivisionArray, true)?>');        // 동적으로 가져온 직원 리스트
      
      if(headerArray.length == 0){
        errorAlert();
      }
    }catch{
      errorAlert();
    }
    
  </script>

<?php endif;?>