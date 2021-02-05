
<!-- jQuery v1.12.4 -->
<script src="/js/jquery-1.12.4.min.js"></script>
<!-- Bootstrap v3.3.7 -->
<script src="/bootstrap/js/bootstrap.min.js"></script>
<!-- editor js -->
<script type="text/javascript" src="/summernote/summernote.js"></script>
<!-- multiselect.js -->


<!-- 상수 js -->
<script src="/js/const.js"></script>

<script src="/js/error.js"></script>
<script src="/js/common.js"></script>
<script src="/js/searchOptions.js"></script>
<script src="js/app.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.0/jquery.form.min.js" integrity="sha384-E4RHdVZeKSwHURtFU54q6xQyOpwAhqHxy2xl9NLW9TQIqdNrNh60QVClBRBkjeB8" crossorigin="anonymous"></script>
<script src="/js/sweetalert.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/js/bootstrap-select.min.js"></script>  
  
  <!-- 페이지 카테고리 -->
  <div class="container-fluid mb20px">
    <h3 class="nwt-category"><small>입출고 관리&nbsp;&nbsp;></small>회수 관리</h3>
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
                            <label for="dateField" class="sr-only">기간</label>
                            <!-- 기간선택 -->
                            <select class="form-control input-md" id="dateField" name="dateField">
                              <option value="">=기간=</option>
                              <option value="ES_ISSUE_DATE">회수일</option>
                              <option value="ES_CANCEL_DTHMS">취소일</option>
                            </select>

                            <!-- 날짜선택 -->
                            <input type="text" class="form-control input-md" name="START_DT" id="START_DT" autocomplete="off"  placeholder="입력" maxlength="10">
                            <span class="glyphicon glyphicon-calendar search-glypicon"></span>
                            <label for="END_DT" class="nwt-form-label">~</label>                            
                            <input type="text" class="form-control" name="END_DT" id="END_DT" autocomplete="off"  placeholder="입력" maxlength="10">
                            <span class="glyphicon glyphicon-calendar search-glypicon"></span>                            
                          </div>

                          <!-- 구분  -->
                          <div class="col-md-1">
                            <label for="DIVISION_SEARCH" class="sr-only">구분</label>
                            <select id="divisionID"  name="divisionID[]" 
                                      data-style="btn-default" title="=구분="
                                      class="form-control selectpicker" multiple
                                      data-live-search="true" data-live-search-placeholder="Search" >
                                <option value="">=선택=</option>
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
                          <!-- <div class="col-md-2">
                            <label for="CRR_ID_SEARCH" class="sr-only mr10r">지역검색</label>
                            <select id="CRR_ID_SEARCH"  name="CRR_ID_SEARCH[]" 
                                      data-style="btn-default" title="지역검색(띄어쓰기로 시/도, 시/군/구 구분)"
                                      class="form-control selectpicker" multiple
                                      data-live-search="true" data-live-search-placeholder="Search" >
                            </select>                          
                          </div> -->

                          <!-- 지역 -->
                          <div class="col-md-2">
                            <label for="regionName" class="sr-only">지역</label>
                            <input type="text" class="form-control" name="regionName" id="regionName" placeholder="지역 입력">                            
                          </div>


                          <!-- 공업사 -->
                          <div class="col-md-2">
                            <label for="garageID" class="sr-only">공업사</label>
                            <select id="garageID"  name="garageID[]" 
                                      data-style="btn-default" title="=공업사="
                                      class="form-control selectpicker" multiple
                                      data-live-search="true" data-live-search-placeholder="Search" >
                                <option value="">=선택=</option>
                                <?php
                                  foreach($garageArray as $idx => $item){
                                ?>
                                  <option value="<?php echo $item['garageID']?>"><?php echo $item['garageName']?></option>
                                <?php
                                  }
                                ?>
                            </select>          
                          </div>

                          <!-- 거래여부 -->
                          <div class="col-md-1">
                            <label for="contractStatusID" class="sr-only">거래여부</label>
                            <select id="contractStatusID"  name="contractStatusID[]" 
                                      data-style="btn-default" title="=거래상태="
                                      class="form-control selectpicker" multiple
                                      data-live-search="true" data-live-search-placeholder="Search" >
                                  <?php echo getOption($this->optionArray['CONTRACT_TYPE'], "" ,"") ?>
                            </select>                            
                          </div>

                          <!-- 판매금액 -->
                          <!-- <div class="col-md-2 frmGroup2">
                            <label for="START_SALE_PRICE_SEARCH" class="sr-only">판매금액</label>
                            <input type="text" class="form-control" name="START_SALE_PRICE_SEARCH" id="START_SALE_PRICE_SEARCH" placeholder="판매금액 입력">
                            <label for="END_SALE_PRICE_SEARCH" class="nwt-form-label">~</label>                            
                            <input type="text" class="form-control" name="END_SALE_PRICE_SEARCH" id="END_SALE_PRICE_SEARCH" placeholder=" 판매금액 입력">                           
                          </div> -->


                          <!-- 메모 입력 -->
                          <div class="col-md-1">
                            <input type="text" class="form-control" name="memo" id="memo" placeholder="메모 입력">                            
                          </div>


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
        <col width="3%">
        <col width="2%">
        <col width="8%">
        <col width="6%">
        <col width="2%">
        <col width="4%">
        <col width="4%">
        <col width="4%">
        <col width="2%">
        <col width="4%">
        <col width="2%">
        <col width="4%">
        <col width="8%">
        <col width="3%">
        <col width="4%">
        <col width="3%">
        <col width="4%">
        <col width="3%">
        <col width="1%">
        <col width="1%">
      </colgroup>
      <thead>
        <tr class="active">
          <th class="text-center">순번</th>
          <th class="text-center">회수일</th>
          <th class="text-center">구분</th>
          <th class="text-center">지역</th>
          <th class="text-center">공업사</th>
          <th class="text-center">거래여부</th>
          <th class="text-center">카테고리</th>
          <th class="text-center">매입처</th>
          <th class="text-center">상품</th>
          <th class="text-center">규격/용량</th>
          <th class="text-center">단가</th>
          <th class="text-center">수량</th>
          <th class="text-center">변동일</th>
          <th class="text-center">메모</th>
          <th class="text-center">직원명(아이디)</th>
          <th class="text-center">작성일시</th>
          <th class="text-center">작성자</th>
          <th class="text-center">취소일시</th>
          <th class="text-center">취소자</th>
          <th class="text-center">취소</th>
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

</html>