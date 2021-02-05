<!-- footer.html -->
<?php require $_SERVER['DOCUMENT_ROOT']."/include/script.html";?>

<!-- 페이지 카테고리 -->
<div class="container-fluid mb20px">
  <h3 class="nwt-category"><small>일반판매관리&nbsp;&nbsp;></small>일반판매 현황</h3>
</div>

<?php if(empty($paymentArray)):?>
    <div class="container-fluid nwt-search-form" style="text-align:center; margin-top:120px">
        <h2>등록된 결제방법 데이터가 존재하지 않습니다.</h2>
        <span style="font-size:13px; color:gray">결제방법 등록 후 이용 가능한 페이지 입니다.</span>
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
                              <option value="ESS_ISSUE_DT">판매일</option>
                              <option value="ESS_DEPOSIT_DT">입금일</option>
                            </select>
                            <!-- 날짜선택 -->
                            <input type="text" class="form-control" name="START_DT" id="START_DT" placeholder="입력">
                            <span class="glyphicon glyphicon-calendar search-glypicon"></span>
                            <label for="END_DT" class="nwt-form-label">~</label>                            
                            <input type="text" class="form-control" name="END_DT" id="END_DT" placeholder="입력">
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

                          <!-- 지역 -->
                          <div class="col-md-2">
                            <label for="regionID" class="sr-only">지역명</label>
                            <input type="text" class="form-control" name="regionName" id="regionName" placeholder="지역명 입력">                            
                            <!-- <select id="regionID"  name="regionID[]" 
                                    data-style="btn-default" title="=지역검색(띄어쓰기로 시/도, 시/군/구 구분)="
                                    class="form-control selectpicker" multiple
                                    data-live-search="true" data-live-search-placeholder="Search" 
                                    data-selected-text-format="count>3"
                                    data-actions-box="true"
                            >
                              <?php
                                foreach($regionArray as $index => $item){
                              ?>
                                <option value="<?=$item['regionID']?>"><?=$item['regionName']?></option>
                              <?php
                                }
                              ?>
                            </select>                           -->
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

                          <!-- 거래여부 -->
                          <div class="col-md-1">
                            <label for="contractStatusID" class="sr-only">거래여부</label>
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
          <th class="text-center" rowspan="2">순번</th>
          <th class="text-center" rowspan="2">구분</th>
          <th class="text-center" rowspan="2">지역</th>
          <th class="text-center" rowspan="2">업체명</th>
          <th class="text-center" rowspan="2">청구식</th>
          <th class="text-center" rowspan="2">거래여부</th>
          <th class="text-center" rowspan="2">담당자</th>
          <th class="text-center" rowspan="2">사무실전화</th>
          <th class="text-center" rowspan="2">담당자휴대폰</th>
          <th class="text-center" rowspan="2">최근판매일</th>
          <th class="text-center" rowspan="2">매출액계</th>
          <th class="text-center" rowspan="2">최근입금일</th>
          <th class="text-center" colspan="<?=count($paymentArray)+1?>">입금액계</th>
          <th class="text-center" rowspan="2">결제잔액</th>
        </tr>
        <tr class="active">
          <?php if(!empty($paymentArray)):?>
              <th class="text-center">소계</th>
              <?php foreach($paymentArray as $index => $item){?>
                <th class="text-center"><?=$item['domain']?></th>
              <?php }?>
          <?php endif;?>
          
        </tr>
      </thead>

      <tbody id="dataTable">
      </tbody>

      <tr id="indicatorTable" style="display : none">   
        <td colspan="<?=count($paymentArray)*14?>"> 
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
  <script>
    function errorAlert(){
      alert("결제정보 헤더리스트 인코딩에 실패하였습니다.");
      if(opener == null){
        history.back(-1);
      }else{
        window.close();
      }
    }

    let headerArray = [];
    try{
      headerArray = JSON.parse('<?=json_encode($paymentArray, true)?>');        // 동적으로 가져온 직원 리스트
    }catch{
      errorAlert();
    }

  </script>
<?php endif;?>

