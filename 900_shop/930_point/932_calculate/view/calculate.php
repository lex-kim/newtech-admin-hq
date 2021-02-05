

  <!-- 페이지 카테고리 -->
  <div class="container-fluid mb20px">
    <h3 class="nwt-category"><small>쇼핑몰관리&nbsp;&nbsp;>&nbsp;&nbsp;포인트관리&nbsp;&nbsp;></small>포인트현황</h3>
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
                            <label for="startDate" class="sr-only">기간</label>
                            <input type="text" class="form-control" name="startDate" id="startDate" autocomplete="off"  placeholder="입력">
                            <span class="glyphicon glyphicon-calendar search-glypicon"></span>
                            <label for="endDate" class="nwt-form-label">~</label>                            
                            <input type="text" class="form-control" name="endDate" id="endDate" autocomplete="off"  placeholder="입력">
                            <span class="glyphicon glyphicon-calendar search-glypicon"></span>                            
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
        </div>
      </div>

      <!-- 총 발생포인트 잔액-->
      <table class="table table-bordered" id="totalBalanceTable">
          <colgroup>
            <col width="5%"/>
            <col width="5%"/>
            <col width="3%"/>
            <col width="5%"/>
            <col width="3%"/>
            <col width="5%"/>
            <col width="3%"/>
            <col width="5%"/>
            <col width="3%"/>
            <col width="5%"/>
            <col width="3%"/>
            <col width="5%"/>
            <col width="3%"/>
            <col width="5%"/>
          </colgroup>
          <tbody id="mypoint">
            <tr>
              <td class="text-center active">총 발생포인트 잔액</td>
              <td class="text-center" id="totalBalance">0</td>
              <td class="text-center active">적립예정</td>
              <td class="text-center" id="pointEarnExpect">0</td>
              <td class="text-center active">적립확정</td>
              <td class="text-center" id="pointEarn">0</td>
              <td class="text-center active">사용예정</td>
              <td class="text-center" id="pointRedeemExpect">0</td>
              <td class="text-center active">사용확정</td>
              <td class="text-center" id="pointRedeem">0</td>
              <td class="text-center active">소멸예정</td>
              <td class="text-center" id="pointExpireExpect">0</td>
              <td class="text-center active">소멸확정</td>
              <td class="text-center" id="pointExpire">0</td>
            </tr>                            
          </tbody>
      </table>

    </div>
  </div>


  <!-- 테이블 -->
  <div class="container-fluid nwt-table" style="width: 100%;"> 
    <table class="table table-bordered table-striped">
      <colgroup>
        <col width="1%"/>
        <col width="3%"/>
        <col width="5%"/>
        <col width="5%"/>
        <col width="5%"/>
        <col width="5%"/>
        <col width="5%"/>
        <col width="5%"/>
        <col width="5%"/>
      </colgroup>
      <thead>
        <tr class="active">
          <th class="text-center">순번</th>
          <th class="text-center">대상일자</th>
          <th class="text-center">적립예정</th>
          <th class="text-center">적립확정</th>
          <th class="text-center">사용예정</th>
          <th class="text-center">사용확정</th>
          <th class="text-center">소멸예정</th>
          <th class="text-center">소멸확정</th>
        </tr>
      </thead>

      <tbody id="dataTable">                                                                 
      </tbody>

      <tr id="indicatorTable" style="display : none">   
        <td colspan="25"> 
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