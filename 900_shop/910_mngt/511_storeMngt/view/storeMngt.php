
  <!-- 페이지 카테고리 -->
  <div class="container-fluid mb20px">
    <h3 class="nwt-category"><small>거래처관리&nbsp;&nbsp;></small>업체관리</h3>
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
                        <div class="row">
                          
                          <!-- 기간 -->
                          <div class="col-md-3 frmGroup3">
                            <label for="START_DT" class="sr-only">기간</label>
                            <!-- 기간선택 -->
                            <select class="form-control input-md" id="dateField" name="dateField">
                              <option value="">=선택=</option>
                              <option value="NGS.WRT_DTHMS">가입일</option>
                              <option value="NGS.MGG_BIZ_START_DT">거래개시일</option>
                            </select>
                            <!-- 날짜선택 -->
                            <input type="text" class="form-control calenderInput" name="startDate" id="startDate" autocomplete="off" placeholder="yyyy-mm-dd"  maxlength="10">
                            <span class="glyphicon glyphicon-calendar search-glypicon"></span>
                            <label for="BIZ_END_DT_SEARCH" class="nwt-form-label">~</label>                            
                            <input type="text" class="form-control calenderInput" name="endDate" id="endDate" autocomplete="off" placeholder="yyyy-mm-dd"  maxlength="10">
                            <span class="glyphicon glyphicon-calendar search-glypicon"></span>                            
                          </div>
                    
                          <!-- 구분 -->
                          <div class="col-md-1">
                            <label for="divisionID" class="sr-only">구분</label>
                            <select id="divisionID"  name="divisionID" class="form-control">
                                <option value="">=구분=</option>
                                <?php
                                  foreach($divisionArray as $idx => $item){
                                ?>
                                  <option value="<?php echo $item['divisionID']?>"><?php echo $item['divisionName']?></option>
                                <?php
                                  }
                                ?>
                           </select>                             
                          </div>
                        
                          <!-- 주소 -->
                          <div class="col-md-2">
                            <label for="addressName" class="sr-only">주소</label>
                            <input type="text" class="form-control" name="addressName" id="addressName" placeholder="주소 입력"/>  
                          </div>

                          <!-- 업체명 -->
                          <div class="col-md-2">
                            <label for="garageName" class="sr-only">업체명</label>
                            <input type="text" class="form-control" name="garageName" id="garageName" placeholder="업체명 입력"/>                            
                          </div>

                          <!-- 사업자번호 -->
                          <div class="col-md-1">
                            <label for="companyRegID" class="sr-only">사업자번호</label>
                            <input type="text" class="form-control" onkeyup="isNumber(event)" maxlength="10"
                               name="companyRegID" id="companyRegID" placeholder="사업자번호 입력"/>                            
                          </div>

                          <!-- 로그인ID -->
                          <div class="col-md-1">
                            <label for="loginID" class="sr-only">로그인ID</label>
                            <input type="text" class="form-control" name="loginID" id="loginID" placeholder="로그인ID 입력"/>                            
                          </div>

                          <!-- 거래여부 -->
                          <div class="col-md-1">
                            <label for="RCT_ID_SEARCH" class="sr-only">거래여부</label>
                            <select id="contractStatusCode"  name="contractStatusCode[]" title="=거래상태="
                                    class="form-control selectpicker" multiple data-selected-text-format="count>3" >
                               <?php echo getOption($this->optionArray['CONTRACT_TYPE'], "거래상태" ,"") ?>
                            </select>                           
                          </div>
                        </div>

                        <div class="row frm-last-row">

                          <!-- 쇼핑몰 등급 -->
                          <div class="col-md-1">
                            <label for="rankID" class="sr-only">쇼핑몰 등급</label>
                            <select id="rankID"  name="rankID" class="form-control">
                                <option value="">=쇼핑몰 등급=</option>
                                <?php
                                  foreach($rankArray as $idx => $item){
                                ?>
                                  <option value="<?php echo $item['rankID']?>"><?php echo $item['rankName']?></option>
                                <?php
                                  }
                                ?>
                           </select>                             
                          </div>

                          <!-- 키필드 -->
                          <div class="col-md-2 frmGroup2-2">
                            <label for="keyField" class="sr-only">키필드</label>
                            <select class="form-control" id="keyField" name="keyField">
                              <option value="">=키필드=</option>
                              <option value="NGS.MGG_CEO_NM_AES">대표자명</option>
                              <option value="NGS.MGG_CLAIM_NM_AES">담당자명</option>
                              <option value="NGS.MGG_OFFICE_TEL">사무실연락처</option>
                              <option value="NGS.MGG_OFFICE_FAX">사무실FAX</option>
                              <option value="NGS.MGG_CEO_TEL_AES">대표자전화</option>
                              <option value="NGS.MGG_CLAIM_TEL_AES">담당자전화</option>
                              <option value="NGS.MGG_MEMO">메모</option>
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
                            <button type="submit"  class="btn btn-primary" id="frmSearch" style="margin-top: -10px"><span class="glyphicon glyphicon-search"></span>&nbsp;검색</button>
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

      <!-- 확장하기 -->
      <div class="table-top-etc">
        <!-- <label for="extend" class="extend">확장하기</label>
        <input type="checkbox" id="extend" name="extend"> -->
      </div>
      
      <!-- 테이블 우측 상단 btn -->
      <div class="col-lg-6 clearfix">
        <div class="form-inline f-right">

          <!-- 등록btn / modal handler -->
          <form method="GET" id="excelForm" >
           </form>
     
          <button type="button" class="btn btn-success" onclick="excelDownload();"><span class="glyphicon glyphicon-download-alt"></span>&nbsp;엑셀 다운로드</button>
          
          <a href="javascript:openNewPopup('')" >
            <button type="button" class="btn btn-default" onclick="addStore()">업체등록</button>
          </a>
          <input type="hidden" class="form-control" name="AUTH_UPDATE" id="AUTH_UPDATE" value="<?php echo $AUTH_UPDATE; ?>">


          <form method="POST" action="gwRegister.html" id="gwForm" target="newwindow">
             <input type="hidden" id="MGG_VALUE" name="MGG_VALUE"/>
          </form>

        </div>
      </div>

    </div>
  </div>


  <!-- 테이블 -->
  <div class="container-fluid nwt-table" style="width: 100%;"> 
    <table class="table table-bordered table-striped">
      <colgroup>
        <col width="2%">
        <col width="3%">
        <col width="2%">
        <col width="3%">
        <col width="4%">
        <col width="5%">
        <col width="5%">
        <col width="8%">
        <col width="4%">
        <col width="4%">
        <col width="4%">
        <col width="3%">
        <col width="4%">
        <col width="3%">
        <col width="4%">
        <col width="2%">
        <col width="4%">
        <col width="2.5%">
        <col width="3%">
        <col width="4%">
        <col width="2%">
      </colgroup>
      <thead>
        <tr class="active">
          <th class="text-center" rowspan="2">순번</th>
          <th class="text-center" rowspan="2">업체ID</th>
          <th class="text-center" rowspan="2">구분</th>
          <th class="text-center" rowspan="2">쇼핑몰<br>등급</th>
          <th class="text-center" rowspan="2">로그인ID</th>
          <th class="text-center" rowspan="2">사업자번호</th>
          <th class="text-center" rowspan="2">업체명</th>
          <th class="text-center" rowspan="2">주소</th>
          <th class="text-center" colspan="3">사무실</th>
          <th class="text-center" colspan="2">대표자</th>
          <th class="text-center" colspan="2">담당자</th>
          <th class="text-center" rowspan="2">거래상태</th>
          <th class="text-center" rowspan="2">거래개시일</th>
          <th class="text-center" rowspan="2">사업자<br>등록증</th>
          <th class="text-center" rowspan="2">작성자</th>
          <th class="text-center" rowspan="2">작성일시</th>
          <th class="text-center" rowspan="2">기능</th>
        </tr>

        <tr class="active">
          <th class="text-center">연락처</th>
          <th class="text-center">FAX</th>
          <th class="text-center">이메일</th>
          <th class="text-center hidetd">성명</th>
          <th class="text-center hidetd">연락처</th>
          <th class="text-center hidetd">성명</th>
          <th class="text-center hidetd">연락처</th>
        </tr>
      </thead>
      <tbody id="dataTable">
                                                                      
      </tbody>

      <tr id="indicatorTable" style="display : none">   
        <td colspan="26"> 
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