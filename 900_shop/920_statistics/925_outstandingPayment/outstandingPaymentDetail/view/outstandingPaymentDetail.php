
  <!-- 페이지 카테고리 -->
  <div class="container-fluid mb20px">
      <h3 class="nwt-category categorypopup"><small>현황통계&nbsp;&nbsp;>&nbsp;&nbsp;미결제금액 현황&nbsp;&nbsp;></small>상세리스트</h3>
    </div>
  
  
    <!-- 검색바(폼) 시작 -->
    <div class="container-fluid nwt-search-form">
        <div class="panel panel-default">
            <div class="panel-body">
                <form class="form-inline" id="searchForm">
                    <input type="hidden" class="form-control" name="partnerID" id="partnerID" value="<?php echo $_REQUEST['partnerID']?>">                      
                    <fieldset>
                        <legend>조건검색</legend>
  
                        <!-- 검색버튼 제외한 폼 wrap -->
                        <div class="frm-wrap">
                          <!-- 1st row -->
                          <div class="row frm-last-row">
                              
                            <!-- 기간 -->
                            <div class="col-md-4 frmGroup3">
                              <label for="dateField" class="sr-only">기간</label>
                              <!-- 기간선택 -->
                              <select class="form-control input-md" id="dateField" name="dateField">
                                <option value="ES_ISSUE_DT">매입일</option>
                                <option value="ES_DEPOSIT_DT">결제일</option>
                              </select>
                              <!-- 날짜선택 -->
                              <input type="text" class="form-control input-md" name="startDate" id="startDate" autocomplete="off"  placeholder="입력" maxlength="10">
                              <span class="glyphicon glyphicon-calendar search-glypicon"></span>
                              <label for="END_DT" class="nwt-form-label">~</label>                            
                              <input type="text" class="form-control" name="endDate" id="endDate" autocomplete="off"  placeholder="입력" maxlength="10">
                              <span class="glyphicon glyphicon-calendar search-glypicon"></span>                            
                            </div>

                            <!-- 결제여부 -->
                            <div class="col-md-2">
                              <label for="payDoneYN" class="sr-only">결제여부</label>
                              <select class="form-control" id="payDoneYN" name="payDoneYN">
                                <option value="">=결제여부=</option>
                                <option value="Y">결제</option>
                                <option value="N">미결제</option>
                              </select>                            
                            </div>
                          </div>
  
                        </div>
                        
  
                        <!-- 검색버튼 wrap -->
                        <div class="frm-search-wrap">
                          <!-- 검색btn -->
                          <div class="row">
                              <div class="col-md-12 text-right">
                                <button type="submit" class="btn btn-primary" id="searchForm" style="margin-top: -10px"><span class="glyphicon glyphicon-search"></span>&nbsp;검색</button>
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

          </div>
        </div>
  
      </div>
    </div>
  
  
  
    <!-- 테이블 -->
    <div class="container-fluid nwt-table">
      <table class="table table-bordered table-striped">
        <thead>
        <tr class="active">
          <th class="text-center" rowspan="2">순번</th>
          <th class="text-center" rowspan="2">매입일</th>
          <th class="text-center" rowspan="2">업체구분명</th>
          <th class="text-center" rowspan="2">거래구분명</th>
          <th class="text-center" rowspan="2">매입처구분명</th>
          <th class="text-center" rowspan="2">매입처ID</th>
          <th class="text-center" rowspan="2">매입처 상호명</th>
          <th class="text-center" rowspan="2">매입처 사무실전화</th>
          <th class="text-center" rowspan="2">매입처 사무실팩스</th>
          <th class="text-center" rowspan="2">담당자명</th>
          <th class="text-center" rowspan="2">담당자 휴대폰번호</th>
          <th class="text-center" rowspan="2">담당자 직통번호</th>
          <th class="text-center" rowspan="2">매입상품(대표)</th>
          <th class="text-center" colspan="3">매입</th>
          <th class="text-center" rowspan="2">결제일</th>
          <th class="text-center" colspan="3">결제</th>
          <th class="text-center" colspan="3">결제잔액</th>
          <th class="text-center" rowspan="2">결제</th>
        </tr>
        <tr class="active">
          <th class="text-center">공급가액</th>
          <th class="text-center">세액</th>
          <th class="text-center">합계금액</th>
          <th class="text-center">공급가액</th>
          <th class="text-center">세액</th>
          <th class="text-center">합계금액</th>
          <th class="text-center">공급가액</th>
          <th class="text-center">세액</th>
          <th class="text-center">합계금액</th>
        </tr>
        </thead>
        <tbody id="dataTable">
        </tbody>

        <tr id="indicatorTable" style="display : none">   
          <td colspan="24"> 
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
  <?php	require "./inoutPayDateModal.html"; ?>
  
<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/footerpopup.html";
?>