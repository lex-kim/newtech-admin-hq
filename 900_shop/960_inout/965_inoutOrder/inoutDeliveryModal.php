<!-- 결제일지정 modal -->  
  <div class="modal fade modal-add" id="LOGISTICS_MODAL" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="post" class="stockOrderForm" name="deliveryForm" id="deliveryForm">
            

        <!-- modal 상단 -->
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button> 
          <h4 class="modal-title">배송정보</h4>  
        </div>

        
        <!-- modal 내용 -->
        <div class="modal-body">  

          <!-- 배송번호 -->
          <div class="row">
            <div class="col-md-3"><label for="MODAL_LOGISTICS_NUM">배송방법</label></div>
            <div class="col-md-8">
              <?php echo getRadio($this->optionArray['EC_DELIVERY_METHOD'], "DELIVERY_METHOD", "") ?>
            </div>
          </div>

            <!-- 배송사 -->
            <div class="row">
              <div class="col-md-3"><label for="MODAL_LOGISTICS">배송사</label></div>
              <div class="col-md-8">
                <select class="form-control delivery" id="ORDER_DELIVERY_NM" name="ORDER_DELIVERY_NM"  required>
                  <option value="">=택배사=</option>
                  <?php 
                      foreach($logsticsInfo  as $index => $item){
                  ?>
                    <option value="<?php echo $item['logisticsID']?>">
                        <?php echo $item['logisticsName']?>
                      </option>
                  <?php
                    }
                  ?>
                </select> 
              </div>
            </div>

            <!-- 배송번호 -->
            <div class="row">
              <div class="col-md-3"><label for="MODAL_LOGISTICS_NUM">배송번호</label></div>
              <div class="col-md-8">
                <input type="text" class="form-control delivery" name="MODAL_LOGISTICS_NUM" id="MODAL_LOGISTICS_NUM" placeholder="입력" required/>
              </div>
            </div>

        </div>

        <!-- modal 하단 -->
        <div class="modal-footer">
            <div class="text-left">
            </div>
            <div class="text-right">
                <!-- 등록btn -->
                <button type="submit" class="btn btn-primary">등록</button>
                <!-- 취소btn -->
                <button type="button" class="btn btn-default" data-dismiss="modal">취소</button>
            </div>
        </div>

        </form>
        </div>
    </div>
  </div>