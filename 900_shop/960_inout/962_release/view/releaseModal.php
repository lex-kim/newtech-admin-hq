<style>
.nwt-search-form .bootstrap-select>.dropdown-toggle.bs-placeholder, .bootstrap-select>.dropdown-toggle.bs-placeholder:active, .bootstrap-select>.dropdown-toggle.bs-placeholder:focus, .bootstrap-select>.dropdown-toggle.bs-placeholder:hover {
    height: 32px;
}
.bootstrap-select .btn {
    height: 32px !important;
}
</style>

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

<div class="modal-add" id="add-new">
    <form class="form-horizontal" name="editForm" id="editForm" method="post">
        <input type="hidden" name="REQ_MODE" id="REQ_MODE"/>
        <!-- modal 상단 -->
        <div class="modal-header">
        <h4 class="modal-title">출고등록</h4>  
        </div>


        <!-- modal 내용 -->
        <div class="modal-body">   
            
            
            <div class="row">
                <!-- 출고일 -->
                <div class="col-md-1"><label for="issueDate">출고일</label></div>
                <div class="col-md-3">
                    <input type="text" class="form-control" id="issueDate" name="issueDate" autocomplete="off" placeholder="현재일-변경가능" required/>
                    <span class="glyphicon glyphicon-calendar search-glypicon"></span>
                </div>
                <!-- 직원명 -->
                <div class="col-md-1"><label for="issueUser">직원명</label></div>
                <div class="col-md-3">
                     <input type="text" required  class="form-control autocomplete" id="issueUser" placeholder="입력 후 선택"/>
                     <input type="hidden"  name="issueUser" id="issueUserHidden"/>
                     <?php
                        echo "<script>setUserAutocomplete('".$userInfo."');</script>";
                     ?>
                </div>
                    
            </div>    
            <div class="row">
                <!-- 메모 -->
                <div class="col-md-1"><label for="RELEASE_MEMO">메모</label></div>
                <div class="col-md-7"><input type="text" class="form-control" id="RELEASE_MEMO" name="RELEASE_MEMO" placeholder="입력"></div>
            </div>  
            
            <hr>
            <div class="text-right"><button type="button" class="btn btn-default option_add_btn" onclick="onclickAddStockBtn()">행 추가</button></div>
            <div class="nwt-table modal-table">
                <table class="table table-bordered">
                    <colgroup>
                        <col width="3%">
                        <col width="3%">
                        <col width="3%">
                        <col width="2%">
                        <col width="2%">
                        <col width="2%">
                        <col width="2%">
                        <col width="1%">
                    </colgroup>
                    <thead>
                        <tr class="active">
                            <th class="text-center">카테고리</th>
                            <th class="text-center">매입처</th>
                            <th class="text-center">상품명</th>
                            <th class="text-center">규격/용량</th>
                            <th class="text-center">단가</th>
                            <th class="text-center">수량</th>
                            <th class="text-center">금액</th>
                            <th class="text-center">행 삭제</th>
                        </tr>
                    </thead>
                    <tbody id="releaseTable">
                        <?php
                            echo "<script>
                                    productItems = ".$productInfo.";
                                    document.write(getStockInputTableHtml());</script>";
                        ?>
                    </tbody>
                </table>
            </div>
            


        </div>


        <!-- modal 하단 -->
        <div class="modal-footer">
            <div class="text-left">
                <!-- <button type="button" class="btn btn-danger" id="btnDelete" onclick="confirmDelSwal()">삭제</button> -->
            </div>
            <div class="text-right">
                <!-- 등록btn -->
                <button type="submit" class="btn btn-primary" id="submitBtn">등록</button>
                <!-- 취소btn -->
                <button type="button" class="btn btn-default" onclick="window.close();">취소</button>
            </div>
        </div>

    </form>

</div>
<script>
    $("#issueDate").datepicker({
        dateFormat: 'yy-mm-dd',
    });
    $("#issueDate" ).datepicker('setDate', 'today');      // 기본 현재 날짜로
    $('#editForm').submit(function(e){
        e.preventDefault();

        if(empty($('#issueUserHidden').val())){
            alert("직원명을 입력 후 선택해주셔야 합니다.");
            $('#issueUser').focus();
        }else{
            confirmSwalTwoButton('저장 하시겠습니까?','확인', '취소', false ,()=>setData("<?php echo CASE_CREATE ?>"));
        }

        
    });
</script>
</html>