<!-- header.html -->
  <!-- 페이지 카테고리 -->
  <div class="container-fluid mb20px">
    <h3 class="nwt-category"><small>쇼핑몰관리&nbsp;&nbsp;></small>매입처 관리</h3>
  </div>


  <!-- 검색바(폼) 시작 -->
  <div class="container-fluid nwt-search-form">
      <div class="panel panel-default">
          <div class="panel-body">
              <form class="form-inline" id="searchForm" action="#" >
                  <fieldset>
                      <legend>조건검색</legend>

                      <!-- 검색버튼 제외한 폼 wrap -->
                      <div class="frm-wrap">
                        <!-- 1st row -->
                        <div class="row">
                          
                          <!-- 기간 -->
                          <div class="col-md-2 frmGroup3">
                            <label for="START_DT" class="sr-only">기간</label>
                            <!-- 기간선택 -->
                            <select class="form-control input-md" id="period " name="period">
                              <option value="WRT_DTHMS">등록일</option>
                            </select>
                            <!-- 날짜선택 -->
                            <input type="text" class="form-control" name="START_DT" id="START_DT" autocomplete="off"  placeholder="입력" maxlength="10">
                            <span class="glyphicon glyphicon-calendar search-glypicon"></span>
                            <label for="END_DT" class="nwt-form-label">~</label>                            
                            <input type="text" class="form-control" name="END_DT" id="END_DT" autocomplete="off"  placeholder="입력" maxlength="10">
                            <span class="glyphicon glyphicon-calendar search-glypicon"></span>                            
                          </div>

                          <!-- 업체구분 -->
                          <div class="col-md-1">
                              <label for="businessDivision" class="sr-only">업체구분</label>
                              <select class="form-control" id="businessDivision" name="businessDivision">
                                 <?php echo getOption($this->optionArray['EC_BIZ_TYPE'], "업체구분" ,"") ?>
                              </select>                            
                          </div>

                          <!-- 과세구분 -->
                          <div class="col-md-1">
                              <label for="taxationDivision" class="sr-only">과세구분</label>
                              <select class="form-control" id="taxationDivision" name="taxationDivision">
                                <?php echo getOption($this->optionArray['EC_USER_TAX_TYPE'], "과세구분" ,"") ?>
                              </select>                            
                          </div>

                          <!-- 거래구분 -->
                          <div class="col-md-1">
                              <label for="deal" class="sr-only">거래구분</label>
                              <select class="form-control" id="deal" name="deal">
                                <?php echo getOption($this->optionArray['EC_PARTNER_TYPE'], "거래구분" ,"") ?>
                              </select>                            
                          </div>

                          <!-- 결제기일 -->
                          <div class="col-md-1 frmGroup1"> 
                            <select class="form-control" name="SEARCH_PAYMENT_DATE" id="SEARCH_PAYMENT_DATE" placeholder="결제기일 입력">
                                <option value="">=결제기일=</option>
                                <option value="5">5일</option>
                                <option value="10">10일</option>
                                <option value="15">15일</option>
                                <option value="20">20일</option>
                                <option value="25">25일</option>
                                <option value="30">30일</option>
                            </select>                         
                          </div>

                          <!-- 업태 -->
                          <div class="col-md-1">
                            <input type="text" class="form-control" name="SEARCH_STORE_BUSINESS" id="SEARCH_STORE_BUSINESS" placeholder="업태 입력">                            
                          </div>

                          <!-- 종목 -->
                          <div class="col-md-1">
                            <input type="text" class="form-control" name="SEARCH_STORE_EVENT" id="SEARCH_STORE_EVENT" placeholder="종목 입력">                            
                          </div>


                          <!-- 거래상태 -->
                          <div class="col-md-1">
                              <label for="EC_USER_STATUS" class="sr-only">거래상태</label>
                              <select class="form-control" id="EC_USER_STATUS" name="EC_USER_STATUS">
                                <?php echo getOption($this->optionArray['EC_USER_STATUS'], "거래상태" ,"") ?>
                              </select>                            
                          </div>

                          <!-- 계좌여부 -->
                          <div class="col-md-1">
                              <label for="companyBankApplyYN" class="sr-only">계좌여부</label>
                              <select class="form-control" id="companyBankApplyYN" name="companyBankApplyYN">
                                <option value="">=계좌여부=</option>
                                <option value="Y">등록</option>
                                <option value="N">미등록</option>
                              </select>                            
                          </div>

                          

                          <!-- 자동발주 -->
                          <div class="col-md-1">
                              <label for="SEARCH_AUTO_ORDERING" class="sr-only">자동발주</label>
                              <select class="form-control" id="SEARCH_AUTO_ORDERING" name="SEARCH_AUTO_ORDERING">
                                <option value="">=자동발주=</option>
                                <option value="Y">자동</option>
                                <option value="N">수동</option>
                              </select>                            
                          </div>

                          <!-- 자동발주방법 -->
                          <div class="col-md-1">
                              <label for="SEARCH_AUTO_ORDERING_WAY" class="sr-only">자동발주방법</label>
                              <select class="form-control" id="SEARCH_AUTO_ORDERING_WAY" name="SEARCH_AUTO_ORDERING_WAY">
                                 <?php echo getOption($this->optionArray['EC_AUTO_ORDER_METHOD'], "자동발주방법" ,"") ?>
                              </select>                            
                          </div>


                        </div>

                        <div class="row frm-last-row">

                          <!-- 지역 -->
                          <div class="col-md-1">
                            <input type="text" class="form-control" name="SEARCH_AREA" id="SEARCH_AREA" placeholder="지역 입력">                            
                          </div>

                          <!-- 업체명 -->
                          <div class="col-md-1">
                            <input type="text" class="form-control" name="SEARCH_STORE_NM" id="SEARCH_STORE_NM" placeholder="업체명 입력">                            
                          </div>

                          <!-- 사업자등록번호 -->
                          <div class="col-md-1">
                            <input type="text" class="form-control" name="SEARCH_BUSINESS_NUM" id="SEARCH_BUSINESS_NUM" placeholder="사업자등록번호 입력">                            
                          </div>

                          <!-- 키필드 -->
                          <div class="col-md-2 frmGroup2-2">
                            <label for="NIC_AES_SEARCH" class="sr-only">키필드</label>
                            <select class="form-control" id="NIC_AES_SEARCH" name="NIC_AES_SEARCH">
                              <option value="">=선택=</option>
                              <option value="EP_GOODS">주요품목</option>
                              <option value="EP_NAMES">이름</option>
                              <option value="EP_PHONES">휴대폰</option>
                              <option value="EP_HOMEPAGE">사이트URL</option>
                              <option value="EP_MEMO">메모</option>
                              <option value="EP_USERID">아이디</option>
                            </select>
                            <input type="text" class="form-control" name="NIC_AES_INPUT_SEARCH" id="NIC_AES_INPUT_SEARCH" placeholder="입력">                            
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
          <button type="button" class="btn btn-primary" id="add-new-btn" onclick="onClickEdit()">등록</button>
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
          <th class="text-center" rowspan="2">업체구분</th>
          <th class="text-center" rowspan="2">과세구분</th>
          <th class="text-center" rowspan="2">거래구분</th>
          <th class="text-center" rowspan="2">사업자번호</th>
          <th class="text-center" rowspan="2">상호</th>
          <th class="text-center" rowspan="2">업태</th>
          <th class="text-center" rowspan="2">종목</th>
          <th class="text-center" rowspan="2">주소</th>
          <th class="text-center" colspan="2">사무실</th>
          <!-- <th class="text-center" rowspan="2">대표번호</th>
            <th class="text-center" rowspan="2">팩스번호</th> -->
          <th class="text-center" colspan="2">대표자</th>
          <!-- <th class="text-center" rowspan="2">대표자명</th>
          <th class="text-center" rowspan="2">대표자휴대폰</th> -->
          <!-- <th class="text-center" rowspan="2">담당자명</th> -->
          <th class="text-center" colspan="4">담당자휴대폰</th>
          <th class="text-center" rowspan="2">사이트URL</th>
          <th class="text-center" rowspan="2">주요품목</th>
          <th class="text-center" rowspan="2">메모</th>
          <th class="text-center" rowspan="2">거래상태</th>
          <th class="text-center" rowspan="2">결제기일</th>
          <th class="text-center" rowspan="2">거래은행</th>
          <th class="text-center" rowspan="2">계좌번호</th>
          <th class="text-center" rowspan="2">계좌주명</th>
          <th class="text-center" rowspan="2">사업자<br>등록증</th>
          <th class="text-center" rowspan="2">통장</th>
          <th class="text-center" rowspan="2">자동발주</th>
          <th class="text-center" rowspan="2">자동발주방법</th>
          <th class="text-center" rowspan="2">등록자</th>
          <th class="text-center" rowspan="2">등록일시</th>
          <th class="text-center" rowspan="2">수정</th>
        </tr>
        <tr class="active">
          <th class="text-center">대표번호</th>
          <th class="text-center">팩스번호</th>
          <th class="text-center">대표자명</th>
          <th class="text-center">휴대폰</th>
          <th class="text-center">담당자명</th>
          <th class="text-center">휴대폰</th>
          <th class="text-center">직통전화</th>
          <th class="text-center">이메일</th>
        </tr>
      </thead>
      <tbody id="buyMngtList">                                                                 
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