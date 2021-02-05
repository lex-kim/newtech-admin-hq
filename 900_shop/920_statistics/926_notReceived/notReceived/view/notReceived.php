
<!-- footer.html -->
<?php require $_SERVER['DOCUMENT_ROOT']."/include/script.html";?>
  
  
  <!-- 페이지 카테고리 -->
  <div class="container-fluid mb20px">
    <h3 class="nwt-category"><small>현황통계&nbsp;&nbsp;></small>미수금액 현황</h3>
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
                            <option value="EO.WRT_DTHMS">매입일</option>
                            <option value="EO_DEPOSIT_DT">결제일</option>
                          </select>
                          <!-- 날짜선택 -->
                          <input type="text" class="form-control input-md" name="startDate" id="startDate" autocomplete="off"  placeholder="입력" maxlength="10">
                          <span class="glyphicon glyphicon-calendar search-glypicon"></span>
                          <label for="endDate" class="nwt-form-label">~</label>                            
                          <input type="text" class="form-control" name="endDate" id="endDate" autocomplete="off"  placeholder="입력" maxlength="10">
                          <span class="glyphicon glyphicon-calendar search-glypicon"></span>                            
                        </div>

                        <!-- 구분  -->
                        <div class="col-md-1">
                            <label for="divisionID" class="sr-only">구분</label>
                            <select id="divisionID"  name="divisionID[]" 
                                    data-style="btn-default" title="=구분="
                                    class="form-control selectpicker" multiple
                                    data-selected-text-format="count>2"
                                    data-actions-box="true"
                            >
                              <?php
                                foreach($divisionArray as $index => $item){
                              ?>
                                <option value="<?=$item['divisionID']?>"><?=$item['divisionName']?></option>
                              <?php
                                }
                              ?>
                            </select>                           
                        </div>

                        <!-- 지역명 입력 -->
                        <div class="col-md-1">
                          <input type="text" class="form-control" name="regionName" id="regionName" placeholder="지역명 입력">       
                        </div>

                        <!-- 공업사ID -->
                        <?php if(count($garageArray) > 0):?>
                          <div class="col-md-2">
                            <label for="searchGarageID" class="sr-only">공업사ID</label>
                            <input type="text" class="form-control" onchange="onchangeGarageID()" name="searchGarageID" id="searchGarageID" placeholder="공업사ID 입력 및 선택(자동완성)">    
                            <input type="hidden"  id="garageID" name="garageID" value=""/>
                            <script>
                              setAutoComplete('<?=json_encode($garageArray, true)?>');                // 자동완성을 위한 상품 리스트
                            </script>
                          </div>
                        <?php endif;?>
                        
                        <!-- 공업사 -->
                        <div class="col-md-1">
                          <label for="garageName" class="sr-only">공업사</label>
                          <input type="text" class="form-control" name="garageName" id="garageName" placeholder="공업사 입력">                            
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

                        <!-- 구매여부 -->
                        <div class="col-md-1">
                          <label for="buyYN" class="sr-only">구매여부</label>
                          <select class="form-control" id="buyYN" name="buyYN">
                            <option value="">=구매여부=</option>
                            <option value="Y">구매</option>
                            <option value="N">미구매</option>
                          </select>                            
                        </div>

                        <!-- 거래상태 -->
                        <div class="col-md-1">
                            <label for="contractStatusID" class="sr-only">거래상태</label>
                            <select id="contractStatusID"  name="contractStatusID[]" 
                                    data-style="btn-default" title="=거래상태="
                                    class="form-control selectpicker" multiple
                                    data-selected-text-format="count>3"
                                    data-actions-box="true"
                            >  
                              <?php
                                foreach($this->optionArray['CONTRACT_TYPE'] as $index => $item){
                              ?>
                                <option value="<?=$item['code']?>"><?=$item['domain']?></option>
                              <?php
                                }
                              ?>
                            </select>
                        </div>

                         <!-- 키필드 -->
                         <div class="col-md-2 frmGroup2-2">
                            <label for="keyField" class="sr-only">키필드</label>
                            <select class="form-control" id="keyField" name="keyField">
                              <option value="chargeName">담당자명</option>
                              <option value="chargePhoneNum">담당자 휴대폰</option>
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
          <th class="text-center" rowspan="2">구분명</th>
          <th class="text-center" rowspan="2">지역명</th>
          <th class="text-center" rowspan="2">업체ID</th>
          <th class="text-center" rowspan="2">업체명</th>
          <th class="text-center" rowspan="2">청구식명</th>
          <th class="text-center" rowspan="2">거래상태</th>
          <th class="text-center" rowspan="2">담당자명</th>
          <th class="text-center" rowspan="2">담당자연락처</th>
          <th class="text-center" rowspan="2">사무실전화</th>
          <th class="text-center" rowspan="2">팩스번호</th>
          <th class="text-center" rowspan="2">총 주문횟수</th>
          <th class="text-center" colspan="3">총매출</th>
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
</body>
</html>