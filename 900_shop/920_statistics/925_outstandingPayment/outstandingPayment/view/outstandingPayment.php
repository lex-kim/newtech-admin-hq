

  <!-- 페이지 카테고리 -->
  <div class="container-fluid mb20px">
    <h3 class="nwt-category"><small>현황통계&nbsp;&nbsp;></small>미결제금액 현황</h3>
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
                          <label for="startDate" class="sr-only">기간</label>
                          <!-- 기간선택 -->
                          <select class="form-control input-md" id="dateField" name="dateField">
                            <option value="ES_ISSUE_DT">매입일</option>
                            <option value="ES_DEPOSIT_DT">결제일</option>
                          </select>
                          <!-- 날짜선택 -->
                          <input type="text" class="form-control input-md" name="startDate" id="startDate" autocomplete="off"  placeholder="입력" maxlength="10">
                          <span class="glyphicon glyphicon-calendar search-glypicon"></span>
                          <label for="endDate" class="nwt-form-label">~</label>                            
                          <input type="text" class="form-control" name="endDate" id="endDate" autocomplete="off"  placeholder="입력" maxlength="10">
                          <span class="glyphicon glyphicon-calendar search-glypicon"></span>                            
                        </div>

                        <!-- 업체구분 -->
                        <div class="col-md-1">
                          <label for="businessTypeCode" class="sr-only">업체구분</label>
                          <select id="businessTypeCode"  name="businessTypeCode[]" 
                                  data-style="btn-default" title="=업체구분="
                                  class="form-control selectpicker" multiple
                                  data-live-search="true" data-live-search-placeholder="Search" 
                                  data-selected-text-format="count>3"
                                  data-actions-box="true"
                          >
                            <?php
                              foreach($this->optionArray['EC_BIZ_TYPE'] as $index => $item){
                            ?>
                              <option value="<?=$item['code']?>"><?=$item['domain']?></option>
                            <?php
                              }
                            ?>
                          </select>                            
                        </div>

                        <!-- 거래구분 -->
                        <div class="col-md-1">
                          <label for="partnerTypeCode" class="sr-only">거래구분</label>
                          <select id="partnerTypeCode"  name="partnerTypeCode[]" 
                                  data-style="btn-default" title="=거래구분="
                                  class="form-control selectpicker" multiple
                                  data-live-search="true" data-live-search-placeholder="Search" 
                                  data-selected-text-format="count>3"
                                  data-actions-box="true"
                          >
                           <?php
                              foreach($this->optionArray['EC_PARTNER_TYPE'] as $index => $item){
                            ?>
                              <option value="<?=$item['code']?>"><?=$item['domain']?></option>
                            <?php
                              }
                            ?>
                          </select>                            
                        </div>

                        <!-- 매입처구분코드 -->
                        <div class="col-md-1">
                          <label for="buyTypeCode" class="sr-only">매입처구분코드</label>
                          <select id="buyTypeCode"  name="buyTypeCode[]" 
                                  data-style="btn-default" title="=매입처구분코드="
                                  class="form-control selectpicker" multiple
                                  data-live-search="true" data-live-search-placeholder="Search" 
                                  data-selected-text-format="count>3"
                                  data-actions-box="true"
                          >
                            <?php
                              foreach($this->optionArray['EC_BUYER_TYPE'] as $index => $item){
                            ?>
                              <option value="<?=$item['code']?>"><?=$item['domain']?></option>
                            <?php
                              }
                            ?>
                          </select>                            
                        </div>

                        <!-- 결제여부 -->
                        <div class="col-md-1">
                          <label for="payDoneYN" class="sr-only">결제여부</label>
                          <select class="form-control" id="payDoneYN" name="payDoneYN">
                            <option value="">=결제여부=</option>
                            <option value="Y">결제</option>
                            <option value="N">미결제</option>
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

                         <!-- 키필드 -->
                         <div class="col-md-2 frmGroup2-2">
                            <label for="keyField" class="sr-only">키필드</label>
                            <select class="form-control" id="keyField" name="keyField">
                              <option value="">=키필드=</option>
                              <option value="chargeName">담당자명</option>
                              <option value="chargePhoneNum">담당자 휴대폰</option>
                              <option value="chageTelNum">담당자 직통번호</option>
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
      <!-- <?php	require $_SERVER['DOCUMENT_ROOT']."/900_shop/920_statistics/include/statisticsCnt.html"; ?> -->
      <?php	require $_SERVER['DOCUMENT_ROOT']."/include/listCnt.html"; ?>
      <!-- 테이블 우측 상단 btn -->
      <div class="col-lg-6 clearfix">
        <div class="form-inline f-right">
          <!-- 엑셀다운로드-->
          <button type="button" class="btn btn-success" onclick="excelDownload()"><span class="glyphicon glyphicon-download-alt"></span>&nbsp;엑셀 다운로드</button>
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
          <th class="text-center" rowspan="2">업체구분명</th>
          <th class="text-center" rowspan="2">거래구분명</th>
          <th class="text-center" rowspan="2">매입처구분명</th>
          <th class="text-center" rowspan="2">매입처ID</th>
          <th class="text-center" rowspan="2">매입처 상호명</th>
          <th class="text-center" rowspan="2">매입처 사무실전화</th>
          <th class="text-center" rowspan="2">매입처 사무실팩스</th>
          <th class="text-center" rowspan="2">담당자명</th>
          <th class="text-center" rowspan="2">담당자 휴대폰번호</th>
          <th class="text-center" rowspan="2">담당자 직통번화</th>
          <th class="text-center" rowspan="2">총 주문횟수</th>
          <th class="text-center" colspan="3">총매입</th>
          <th class="text-center" colspan="3">총결제</th>
          <th class="text-center" colspan="3">결제잔액</th>
          <th class="text-center" rowspan="2">상세</th>
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
        <td colspan="22"> 
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
  setAutoComplete('<?=json_encode($buyMngtArray, true)?>');                // 자동완성을 위한 상품 리스트
</script>