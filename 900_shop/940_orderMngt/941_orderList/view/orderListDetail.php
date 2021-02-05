<script src="/js/jquery-1.12.4.min.js"></script>
<script type="text/javascript" src="/js/resultPG.js"></script>

<!-- modal #add-new -->  
<div class="modal-add container-fluid" id="add-new" style="width: 1200px; margin: 0 auto;">
      <input type="hidden" class="form-control" id="REQ_MODE" name="REQ_MODE">

      <!-- modal 상단 -->
      <div class="modal-header">
        <div class="text-left">
          <h4 class="modal-title">주문내역 상세</h4> 
        </div>
        <div class="text-right">
          &nbsp;
        </div>
        <!-- 상단의 기능버튼 -->
        <div class="bill-footer order-detail-bill-footer">
          <div class="text-left">
            <?php 
                $disabledText = "disabled";
                if($orderInfo['orderStatusCode'] != EC_ORDER_STATUS_CANCEL): $disabledText = "";?>
              <button type="button" id="submitBtn" class="btn btn-danger" onclick="onClickOrderCancel(<?php echo $orderInfo['paymentCode'] ?>)">주문취소</button>
            <?php endif; ?>

            <button type="button" id="submitBtn" class="btn btn-default" onclick="onClickBillPrint('<?php echo $orderID ?>')">주문서</button>
          </div>
          <div class="text-center">
            <div class="order-status-area">
              <span>주문상태</span> : <?php echo $orderInfo['orderStatusName'] ?> 
              <?php if($orderInfo['orderStatusCode'] != EC_ORDER_STATUS_CANCEL):?>
                <button type="button" class="btn btn-default" onclick="onchangeOrderStatusReady(<?php echo EC_ORDER_STATUS_ITEM_READY ?>)">상품준비</button>
              <?php endif; ?>
             
            </div>
          </div>
          <div class="text-right">
            <!-- 결제,주문, 닫기,취소 -->
            <?php if($orderInfo['orderStatusCode']  != EC_ORDER_STATUS_CANCEL):?>
              <button type="button" id="submitBtn" class="btn btn-default" onclick="onClickTransaction('<?php echo $orderID ?>')">거래명세서</button>
            <?php endif; ?>
            <button type="button" class="btn btn-default" onclick="window.close();">닫기</button>
          </div>
        </div>  
      </div>
      
      
      <div>
        <!-- 주문정보 -->
        <div class="modal-body"> 
          <div class="gw-info">
            <!-- 왼쪽 -->
            <div>
              <!-- 주문정보 -->
              <h5 class="modal-title" style="margin-bottom: 15px;">주문정보</h5>  
              <table class="table table-bordered">
                <colgroup>
                  <col width="14%">
                </colgroup>
                <tbody>
                  <tr>
                    <td class="active text-center">주문번호</td>
                    <td id="ORDER_NUM text-center">
                      <div class="bill-barcode" id="barcodeContainer" >
            
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td class="active text-center">주문일시</td>
                    <td id="ORDER_NM">
                      <?php echo $orderInfo['writeDatetime'] ?> 
                    </td>
                  </tr>
                  <tr>
                    <td class="active text-center">성명</td>
                    <td id="ORDER_NM">
                      <?php echo $orderInfo['orderName'] ?> 
                    </td>
                  </tr>
                  <tr>
                    <td class="active text-center">주소</td>
                    <td id="ORDER_ADDRESS">
                      <?php echo $orderInfo['zipcode'] ?> <br>
                      <?php echo $orderInfo['address1']." ".$orderInfo['address2']  ?> 
                    </td>
                  </tr>
                  <tr>
                    <td class="active text-center">휴대폰번호</td>
                    <td id="ORDER_PHONE">
                       <?php echo setPhoneFormat($orderInfo['orderPhoneNum']) ?> 
                    </td>
                  </tr>
                </tbody>
              </table>

              <!-- 주문금액 -->
              <h5 class="modal-title" style="margin-bottom: 15px;">주문금액</h5>
              <table class="table table-bordered">
                <colgroup>
                  <col width="2%"/>
                  <col width="8%"/>
                </colgroup>
                <tbody>
                  <tr>
                    <td class="text-center active">주문금액</td>
                    <td class="text-right"><?php echo number_format($orderInfo['orderPrice']) ?>원</td>
                  </tr>
                  <tr>
                    <td class="text-center active">배송비</td>
                    <td class="text-right"><?php echo number_format($orderInfo['deliveryPrice']) ?>원</td>
                  </tr>
                  <tr>
                    <td class="text-center active">포인트</td>
                    <td class="text-right point-area">
                      <?php
                        if($orderInfo['pointRedeem'] > 0){
                          echo "-";
                        }
                      ?>
                      <?php echo number_format($orderInfo['pointRedeem']) ?>P
                    </td>
                  </tr>
                  <tr>
                    <td class="text-center active text-bold">최종결제금액</td>
                    <td class="text-right text-bold"><?php echo number_format($orderInfo['totalPrice']) ?>원</td>
                  </tr>
                  <tr>
                    <td class="text-center active">적립예정포인트</td>
                    <td class="text-right"><?php echo number_format($orderInfo['earnPoint']) ?>P</td>
                  </tr>
                </tbody>
              </table>

              <!-- 결제방법 -->
              <h5 class="modal-title" style="margin-bottom: 15px;">결제정보</h5>
              <table class="table table-bordered">
                <colgroup>
                  <col width="14%">
                </colgroup>
                <tbody>
                  <tr>
                    <td class="active text-center">결제정보</td>
                    <td id="paymentTypeCode" data="<?php echo $orderInfo['paymentCode']?>">
                      <?php echo $orderInfo['payment']?>

                      <!-- 주문취소/결제취소 가 아닌경우 -->
                      <?php if($orderInfo['orderStatusCode'] != EC_ORDER_STATUS_CANCEL && $orderInfo['orderStatusCode'] != EC_ORDER_STATUS_PAY_CANCEL ):?>
                        <!-- 신용카드 결제이면서 주문완료가 아닌 경우는 승인취소가 가능하게.. -->
                        <?php if($orderInfo['paymentCode'] == EC_PAYMENT_TYPE_CREDIT && $orderInfo['orderStatusCode'] != EC_ORDER_STATUS_ORDER_COMPLETE):?>
                          <span>(<?php if(!empty($orderInfo['acquirer_nm'])) echo $orderInfo['acquirer_nm'].", "; ?>  결제일 : <?=$orderInfo['depositDate']?> )</span>
                          <button type="btn" class="btn btn-default text-right" onclick="onConfirmRefund()">결제취소</button>

                        <!-- 실시간 이체 이면서 입금대기가 아닌경우는 이체취소가 가능하게.. -->
                        <?php elseif($orderInfo['paymentCode'] == EC_PAYMENT_TYPE_TRANSFER && ($orderInfo['orderStatusCode'] != EC_ORDER_STATUS_ORDER_COMPLETE)):?>
                            <span>(<?php if(!empty($orderInfo['acquirer_nm'])) echo $orderInfo['acquirer_nm'].", "; ?>  이체일 : <?=$orderInfo['depositDate']?> )</span>
                            <button type="btn" class="btn btn-default text-right" onclick="onConfirmRefund()">이체취소</button>
                        <?php endif;?>

                        <?php elseif($orderInfo['orderStatusCode'] == EC_ORDER_STATUS_CANCEL):?>
                          <span style="color:red ; font-size:11px">(취소완료)</span>
                      <?php endif;?>
                    </td>
                  </tr>

                  <?php if($orderInfo['billTypeCode'] != EC_BILL_TYPE_NO) :?>
                    <tr>
                      <td class="active text-center">지출증빙</td>
                      <?php if($orderInfo['billTypeCode'] == EC_BILL_TYPE_TAX):?>
                        <td>
                          <?php echo $orderInfo['billType']?>
                          <?php if(isset($orderInfo['billRequestDatetime'])) : ?>
                              (발행일 : <?php echo $orderInfo['billRequestDatetime'] ?>)
                              <button type="button"  class="btn btn-default text-right" onclick="onCancelBill('<?php echo $orderID ?>')">세금계산서 취소</button>
                          <?php else :?>
                              <button type="button"  class="btn btn-default text-right" onclick="onPublishBill('<?php echo $orderID ?>')">세금계산서 발행</button>
                              <ul class="tax-list">
                                <li>
                                  <div>
                                    <span>사업자등록번호</span><?php echo bizNoFormat($orderInfo['billCompanyRegID']) ?>
                                  </div>
                                </li>
                                <li>
                                  <div>
                                    <span>상호(법인명)</span><?php echo $orderInfo['billCompanyName']?>
                                  </div>
                                  <div>
                                    <span>대표자명</span><?php echo $orderInfo['billCeoName']?>
                                  </div>
                                </li>
                                <li><span>사업장주소</span><?php echo $orderInfo['billAddress']?></li>
                                <li>
                                  <div>
                                    <span>업태</span><?php echo $orderInfo['billCompanyBiz']?>
                                  </div>
                                  <div>
                                    <span>종목</span><?php echo $orderInfo['billCompanyPart']?>
                                  </div>
                                </li>
                              </ul>
                          <?php endif;?>
                        </td>
                      <?php else:?>
                          <td>
                              <?php echo $orderInfo['billType']?>

                              <!-- 지출증빙 타입이 신청안함이 아닌경우 -->
                              <?php if($orderInfo['billTypeCode'] != EC_BILL_TYPE_NO && !empty($orderInfo['billTypeCode'] )) : ?>
                                <?php if(isset($orderInfo['billRequestDatetime'])) : ?>
                                  (발행일 : <?php echo $orderInfo['billRequestDatetime'] ?>)
                                  <button type="button" class="btn btn-default text-right" onclick="onCancelBill('<?php echo $orderID ?>')">취소</button>
                                <?php else :?>
                                  <?php if(!empty($orderInfo['billPhoneNum'])) : ?>
                                    (개인 : <?php echo setPhoneFormat($orderInfo['billPhoneNum'])?>)
                                  <?php else : ?>
                                    (사업자 : <?php echo bizNoFormat($orderInfo['billCompanyRegID'])?>)
                                  <?php endif; ?>
                                  <button type="button" class="btn btn-default text-right" onclick="onPublishBill('<?php echo $orderID ?>')">발행</button>
                                <?php endif ;?>
                              
                              <?php endif; ?>
                            
                          </td>
                      <?php endif; ?>
                    </tr>
                  <?php endif;?>
                </tbody>
              </table>



              <h5 class="modal-title <?php echo $focusText?>" style="margin-bottom: 15px;">메모</h5>
              <form id="memoForm">
                <table class="table table-bordered" id="memoTable">
                  <colgroup>
                    <col width="14%">
                  </colgroup>
                  <tbody>
                    <tr>
                      <td class="active text-center">
                        메모<br>
                        <button type="submit" class="btn btn-default mt5px">저장</button>
                      </td>
                      <td>
                        <textarea class="form-control memo-textarea" id="memo" name="memo" rows=10 placeholder="메모 입력"><?php echo $orderInfo['memo'] ?> </textarea>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </form>
            </div>

            <!-- 오른쪽 -->
            <div>
              <?php 
                $focusText = "";
                if($orderInfo['orderStatusCode'] == EC_ORDER_STATUS_ORDER_COMPLETE|| $orderInfo['orderStatusCode'] ==EC_ORDER_STATUS_DEPOSIT_WAIT){
                  $focusText = "process_active";
                }
              ?>
              <!-- 입금 -->
              <h5 class="modal-title <?php echo $focusText ?>" style="margin-bottom: 15px;">입금</h5>
              <div id="depositContainer" style="position:absolute; z-index:1;opacity:0.5;background-color:gray"> </div>
              <table class="table table-bordered" id="depositTable">
                <colgroup>
                  <col width="16%">
                </colgroup>
                <tbody>
                  <tr >
                    <td class="active text-center">입금방법</td>
                    <td>
                        <?php echo $orderInfo['payment'] ?>
                    </td>
                  </tr>

                  <form id="depositForm">
                    <tr>
                      <!-- 신용카드일 경우 신용카드정보 금액,날짜 정보가 세팅되어있음. -->
                      <td class="active text-center">
                        입금여부
                        <?php if($orderInfo['paymentCode'] == EC_PAYMENT_TYPE_DEPOSIT){?>
                        <button type="submit" class="btn btn-default mt5px" id="add-new-btn">입금처리</button>
                        <?php } ?>
                      </td>
                      <td>
                        <div class="col-md-12">
                          <?php 
                            $deposiotY_checked = "checked";
                            $deposiotN_checked = "";
                            if($orderInfo['depositYN'] == "N"){
                              $deposiotY_checked = "";
                              $deposiotN_checked = "checked";
                            }
                          ?>
                          <label class="radio-inline"><input type="radio" name="DEPOSIT_YN" value="Y" <?php echo $deposiotY_checked  ?>>입금</label>
                          <label class="radio-inline"><input type="radio" name="DEPOSIT_YN" value="N" <?php echo $deposiotN_checked  ?>>미입금</label>
                        </div>
                        <div class="deposit-y-input">
                          <div class="col-md-4">
                            <input type="text" class="form-control" onkeyup="isNumber(event)" value="<?php echo $orderInfo['depositPrice'] ?>"
                              onchange="onchangeDepositPrice()" name="DEPOSIT_YN_PRICE" id="DEPOSIT_YN_PRICE" placeholder="금액 입력" required/>
                          </div>
                          <div class="col-md-4">
                            <input type="text" class="form-control date" value="<?php echo $orderInfo['depositDate'] ?>"
                              name="DEPOSIT_YN_DATE" id="DEPOSIT_YN_DATE" placeholder="yyyy-mm-dd 입력" required/>
                          </div>
                          <div class="col-md-4">
                            <input type="text" class="form-control" value="<?php echo $orderInfo['depositMemo'] ?>"
                               name="DEPOSIT_YN_ETC" id="DEPOSIT_YN_ETC" placeholder="비고 입력" />
                          </div>
                        </div>
                      </td>
                    </tr>
                  </form>

                </tbody>
              </table>
              <?php
                if(!empty($disabledText)){
              ?>
              <script>
                  $('#depositContainer').width($('#depositTable').width());
                  $('#depositContainer').height($('#depositTable').height());
                  $('#depositContainer *').attr("disabled",true);
              </script>
              <?php 
                }
              ?>

              <!-- 배송관리 -->
              <div style="display:flex; flex-direction:row; margin-bottom:8px">
                <?php 
                  $focusText = "";
                  if($orderInfo['orderStatusCode'] == EC_ORDER_STATUS_ITEM_READY || $orderInfo['orderStatusCode'] == EC_ORDER_STATUS_PAY_COMPLETE){
                    $focusText = "process_active";
                  }
                ?>
                <h5 class="modal-title <?php echo $focusText ?>" style="margin-bottom: 15px;">배송관리</h5>
                <?php
                  if(empty($disabledText)){
                ?>
                  <!-- <select id="deliveryStatusSelector" class="form-control" style="width:30%; margin-left:10px" >
                      <option value="">=배송상태=</option>
                      <option value="81115">배송중</option>
                      <option value="81116">배송완료</option>
                  </select> -->
                  <button type="button" class="btn btn-default" id="add-new-btn" style="margin-left:5px" onclick="onchangeOrderStatusDelivery()">배송완료</button>
                <?php
                  }
                ?>
              </div>
              <div id="deliveryContainer" style="position:absolute; z-index:1;opacity:0.5;background-color:gray"> </div>
              <table class="table table-bordered" id="deliveryTable" style="z-index:2">
                <colgroup>
                  <col width="16%">
                </colgroup>
                <tbody>
                  <form id="deliveryForm">
                    <tr>
                      <td class="active text-center">
                        배송방법
                        <!-- 배송처리, 배송완료:  배송완료 처리 후 누르면 -> alert(배송정보를 변경할 수 없습니다.) -->
                        <button type="submit" class="btn btn-default mt5px" id="add-new-btn">배송처리</button>
                      </td>
                      <td>
                        <div class="col-md-12">
                          <?php echo getRadio($this->optionArray['EC_DELIVERY_METHOD'], "DELIVERY_METHOD", $orderInfo['deliveryMethodCode']) ?>
                        </div>
                        <div class="delivery-input">
                          <div class="col-md-4 mt10">
                            <select class="form-control" id="ORDER_DELIVERY_NM" name="ORDER_DELIVERY_NM"  required>
                              <option value="">=택배사=</option>
                              <?php 
                                  foreach($logsticsInfo  as $index => $item){
                              ?>
                                <option value="<?php echo $item['logisticsID']?>" <?php if($orderInfo['logisticsID'] == $item['logisticsID']){echo "selected";} ?>>
                                    <?php echo $item['logisticsName']?>
                                  </option>
                              <?php
                                }
                              ?>
                            </select> 
                          </div>
                          <div class="col-md-8 mt10">
                            <input type="text" class="form-control"  onkeyup="isNumber(event)" value="<?php echo $orderInfo['logisticsInvoiceNum'] ?>"
                              name="ORDER_DELIVERY_NUM" id="ORDER_DELIVERY_NUM" placeholder="송장번호 -없이 입력" required/>
                          </div>
                        </div>
                      </td>
                    </tr>
                  </form>
                </tbody>
              </table>
              <?php
                if(!empty($disabledText)){
              ?>
              <script>
                  $('#deliveryContainer').width($('#deliveryTable').width());
                  $('#deliveryContainer').height($('#deliveryTable').height());
                  $('#deliveryContainer *').attr("disabled",true);
              </script>
              <?php 
                }
              ?>

              <!-- 교환 및 반품 -->
              <div style="display:flex; flex-direction:row; margin-bottom:8px">
                <?php 
                  $focusText = "";
                  if($orderInfo['orderStatusCode'] == EC_ORDER_STATUS_DELIVERY_ING || $orderInfo['orderStatusCode'] == EC_ORDER_STATUS_DELIVERY_COMPLETE ||
                     $orderInfo['orderStatusCode'] == EC_ORDER_STATUS_CHANGE_RECEIPT|| $orderInfo['orderStatusCode'] == EC_ORDER_STATUS_CANCEL_RECEIPT
                  ){
                    $focusText = "process_active";
                  }
                ?>
                <h5 class="modal-title <?php echo $focusText?>" style="margin-bottom: 15px;">교환 및 반품</h5>

                <?php
                  if(empty($disabledText)){
                ?>
                  <select id="returnStatusSelector" class="form-control" style="width:30%; margin-left:10px">
                      <option value="">=교환/반품상태=</option>
                      <option value="81121" <?php if($orderInfo['orderStatusCode'] == '81121'){echo "selected";} ?>>교환접수</option>
                      <!-- <option value="81122">교환반송중</option> -->
                      <!-- <option value="81123">재배송중</option> -->
                      <option value="81124"  <?php if($orderInfo['orderStatusCode'] == '81124'){echo "selected";} ?>>교환보류</option>
                      <option value="81129"  <?php if($orderInfo['orderStatusCode'] == '81129'){echo "selected";} ?>>교환완료</option>
                      <option value="81131"  <?php if($orderInfo['orderStatusCode'] == '81131'){echo "selected";} ?>>반품접수</option>
                      <!-- <option value="81132">반품반송중</option> -->
                      <option value="81134"  <?php if($orderInfo['orderStatusCode'] == '81134'){echo "selected";} ?>>반품보류</option>
                      <option value="81139"  <?php if($orderInfo['orderStatusCode'] == '81139'){echo "selected";} ?>>반품회수완료</option>
                  </select>
                  <button type="button" class="btn btn-default" id="add-new-btn" style="margin-left:5px" onclick="onchangeOrderStatusRefund()">변경</button>
                <?php
                  }
                ?>
              </div>

              <div id="cancelContainer" style="position:absolute; z-index:1;opacity:0.5;background-color:gray"> </div>
                <table class="table table-bordered" id="cancelTable">
                  <colgroup>
                    <col width="16%">
                  </colgroup>
                  <tbody>
                    <tr>
                      <td class="active text-center">반품구분</td>
                      <td>
                        <div class="col-md-12">
                          <?php 
                            $changeSelectedText = "checked";
                            $refundSelectedText = "";
                            if($orderInfo['orderStatusCode'] == EC_ORDER_STATUS_DELIVERY_COMPLETE){
                              $changeSelectedText = "";
                              $refundSelectedText = "checked";
                            }
                          ?>
                          
                          <?php echo getRadio($this->optionArray['EC_RETURN_TYPE'], "RETURN_REASON", $orderInfo['returnTypeCode']) ?>
                        </div>
                      </td>
                    </tr>

                    <form id="cancelForm">
                      <tr class="return-method-area cancel">
                        <td class="active text-center">
                          반품방법
                          <button type="submit" class="btn btn-default mt5px" id="add-new-btn" >반품처리</button>
                        </td>
                        <td>
                          <div class="col-md-12">
                            <?php echo getRadio($this->optionArray['EC_RETURN_METHOD'], "RETURN_METHOD", $orderInfo['returnDeliveryMethodCode']) ?>
                          </div>
                          <div class="return-delivery-input">
                            <div class="col-md-4 mt10">
                              <select class="form-control RETURN_INPUT RETURN_FEE_INPUT" id="RETURN_DELIVERY_NM" name="RETURN_DELIVERY_NM" required>
                                <option value="">=택배사=</option>
                                <?php 
                                    foreach($logsticsInfo  as $index => $item){
                                ?>
                                  <option value="<?php echo $item['logisticsID']?>" <?php if($orderInfo['returnLogisticsID'] == $item['logisticsID']){echo "selected";} ?>>
                                    <?php echo $item['logisticsName']?>
                                  </option>
                                <?php
                                  }
                                ?>
                              </select> 
                            </div>
                            <div class="col-md-8 mt10">
                              <input type="text" class="form-control RETURN_INPUT RETURN_FEE_INPUT" onkeyup="isNumber(event)"
                                 name="RETURN_DELIVERY_NUM" id="RETURN_DELIVERY_NUM" placeholder="송장번호 입력" value="<?php echo $orderInfo['returnLogisticsInvoiceNum'] ?>" required/>
                            </div>
                            <div class="col-md-12 delivery-fee-area">
                              <span>택배비 :</span>
                              <?php echo getRadio($this->optionArray['EC_RETURN_FEE_TYPE'], "DELIVERY_FEE", $orderInfo['returnDeliveryFeeType']) ?>
                              <div class="col-md-4">
                                <input type="text" class="form-control RETURN_INPUT RETURN_FEE_INPUT" onkeyup="isNumber(event)"
                                name="RETURN_DELIVERY_FEE" id="RETURN_DELIVERY_FEE" placeholder="택배금액 입력" value="<?php echo $orderInfo['returnDeliveryFee'] ?>" required/>
                              </div>
                            </div>
                          </div>
                        </td>
                      </tr>
                    </form>

                    <form id="resendForm">
                      <tr class="return-method-area resend">
                        <td class="active text-center">
                          재배송
                          <button type="submit" class="btn btn-default mt5px" id="add-new-btn">재배송처리</button>
                        </td>
                        <td>
                          <div class="col-md-12">
                            <?php echo getRadio($this->optionArray['EC_RETURN_METHOD'], "REDELIVERY_METHOD", $orderInfo['resendDeliveryMethodCode']) ?>
                          </div>
                          <div class="return-delivery-input">
                            <div class="col-md-4 mt10">
                              <select class="form-control REDELIVERY_INPUT REDELIVERY_FEE_INPUT" id="REDELIVERY_DELIVERY_NM" name="REDELIVERY_DELIVERY_NM" required>
                                <option value="">=택배사=</option>
                                <?php 
                                    foreach($logsticsInfo  as $index => $item){
                                ?>
                                  <option value="<?php echo $item['logisticsID']?>" <?php if($orderInfo['resendLogisticsID'] == $item['logisticsID']){echo "selected";} ?>>
                                    <?php echo $item['logisticsName']?>
                                  </option>
                                <?php
                                  }
                                ?>
                              </select> 
                            </div>
                            <div class="col-md-8 mt10">
                              <input type="text" class="form-control REDELIVERY_INPUT REDELIVERY_FEE_INPUT" onkeyup="isNumber(event)"
                               name="REDELIVERY_DELIVERY_NUM" id="REDELIVERY_DELIVERY_NUM" placeholder="송장번호 입력" value="<?php echo $orderInfo['resendLogisticsInvoiceNum'] ?>" required/>
                            </div>
                            <div class="col-md-12 delivery-fee-area">
                              <span>택배비 :</span>
                              <?php echo getRadio($this->optionArray['EC_RETURN_FEE_TYPE'], "REDELIVERY_FEE", $orderInfo['resendDeliveryFeeType']) ?>
                              <div class="col-md-4">
                                <input type="text" class="form-control REDELIVERY_INPUT REDELIVERY_FEE_INPUT" onkeyup="isNumber(event)"
                                  name="REDELIVERY_DELIVERY_FEE" id="REDELIVERY_DELIVERY_FEE" placeholder="택배금액 입력" value="<?php echo $orderInfo['resendDeliveryFee'] ?>" required/>
                              </div>
                            </div>
                          </div>
                        </td>
                      </tr>
                    </form>
                  </tbody>
                </table>
              
                <?php
                  if(!empty($disabledText)){
                ?>
                <script>
                    $('#cancelContainer').width($('#cancelTable').width());
                    $('#cancelContainer').height($('#cancelTable').height());
                    $('#cancelContainer *').attr("disabled",true);
                </script>
                <?php 
                  }
                ?>

              <!-- 환불처리 -->
              <?php 
                  $focusText = "";
                  if($orderInfo['orderStatusCode'] == EC_ORDER_STATUS_COLLECT_COMPLETE){
                    $focusText = "process_active";
                  }
                ?>
              <h5 class="modal-title <?php echo $focusText?>" style="margin-bottom: 15px;">환불처리</h5>
              <div id="refundContainer" style="position:absolute; z-index:1;opacity:0.5;background-color:gray"> </div>
              <form id="refundForm">
                <table class="table table-bordered" id="refundTable">
                  <colgroup>
                    <col width="16%">
                  </colgroup>
                  <tbody>
                    
                    <tr class="refund-area">
                      <td class="active text-center">
                        환불처리
                        <button type="submit" class="btn btn-default mt5px" id="add-new-btn">환불처리</button>
                      </td>
                      <td>
                        <div class="col-md-4">
                          <input type="text" class="form-control REFUND_INPUT" onkeyup="isNumber(event)" value="<?php echo $orderInfo['refundPrice'] ?>"
                              name="RETURN_DELIVERY_PRICE" id="RETURN_DELIVERY_PRICE" placeholder="금액 입력" required/>
                        </div>
                        <div class="col-md-4">
                          <input type="text" class="form-control REFUND_INPUT date" value="<?php echo $orderInfo['refundDate'] ?>"
                            name="RETURN_DELIVERY_DATE" id="RETURN_DELIVERY_DATE" placeholder="yyyy-mm-dd 입력" required/>
                        </div>
                        <div class="col-md-4">
                          <input type="text" class="form-control REFUND_INPUT" value="<?php echo $orderInfo['refundMemo'] ?>"
                            name="RETURN_DELIVERY_ETC" id="RETURN_DELIVERY_ETC" placeholder="비고 입력" />
                        </div>
                        <div class="col-md-4 mt10">
                          <select class="form-control REFUND_INPUT" id="RETURN_DELIVERY_BANK_NM" name="RETURN_DELIVERY_BANK_NM">
                            <?php echo getOption($this->optionArray['BANK_CD'], "은행" , $orderInfo['refundBankCode']) ?>
                          </select> 
                        </div>
                        <div class="col-md-4 mt10">
                          <input type="text" class="form-control REFUND_INPUT"  value="<?php echo $orderInfo['refundBankNum'] ?>"
                             onkeyup="isNumber(event)"  name="RETURN_DELIVERY_BANK_NUM" id="RETURN_DELIVERY_BANK_NUM" placeholder="계좌 입력"/>
                        </div>
                        <div class="col-md-4 mt10">
                          <input type="text" class="form-control REFUND_INPUT" value="<?php echo $orderInfo['refundBankOwn'] ?>"
                            name="RETURN_DELIVERY_BANK_NUMNM" id="RETURN_DELIVERY_BANK_NUMNM" placeholder="계좌주 입력" />
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </form>
              <?php
                  if(!empty($disabledText)){
                ?>
                <script>
                    $('#refundContainer').width($('#refundTable').width());
                    $('#refundContainer').height($('#refundTable').height());
                    $('#refundContainer *').attr("disabled",true);
                </script>
                <?php 
                  }
                ?>

            </div>
          </div>

          
      
          <div class="borderTop"></div>   
          
          <!-- 주문상품 -->
          <h5 class="modal-title" style="margin-bottom: 15px;">주문상품</h5>
      
            <div class="container-fluid row">
              <table class="table table-bordered">
                <thead>
                  <tr class="active">
                    <th class="text-center">순번</th>
                    <th class="text-center">매입구분</th>
                    <th class="text-center">매입처</th>
                    <th class="text-center">연락처<br>(대표번호전화)</th>
                    <th class="text-center">팩스번호</th>
                    <th class="text-center">담당자</th>
                    <th class="text-center">담당자휴대폰연락처</th>
                    <th class="text-center">품명</th>
                    <th class="text-center">규격</th>
                    <th class="text-center">단가</th>
                    <th class="text-center">수량</th>
                    <th class="text-center">공급가</th>
                    <th class="text-center">세액</th>
                    <th class="text-center">합계</th>
                    <th class="text-center">작성여부</th>
                    <th class="text-center">발주서<br>작성/조회</th>
                    <th class="text-center">배송조회</h>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    $totalUnitPrice = 0;
                    $totalPrice = 0;
                    $totalCnt = 0;
                    $totalVAT = 0;
                    $beforePartnerID = "";

                    foreach($orderInfo['orderDetails'] as $idx => $item){
                      $totalUnitPrice += $item['unitPrice']+$item['optionPrice'];                       // 단가
                      $totalCnt += $item['quantity'];                                                   // 수량
                      $totalVAT += ($item['unitPriceVAT']+$item['optionPriceVAT'])*$item['quantity'];   // 세액
                      $totalPrice += ($item['unitPrice']+$item['optionPrice']) * $item['quantity'];     // 공급가
                  ?>
                    <?php 
                        $trBackgroundClass = "";
                        if($item['partnerBuyerTypeCode'] == EC_BUYER_TYPE_CONSIGN): ?>
                        <?php $trBackgroundClass = "class='consignment_list'"; ?>
                    <?php endif;?>
                    <tr <?php echo $trBackgroundClass ?>>
                      <td class="text-center"><?php echo $idx+1 ?></td>
                      <td class="text-center"><?php echo $item['partnerBuyerType']?></td>
                      <td class="text-center"><?php echo $item['partnerName']?></td>
                      <td class="text-center"><?php echo setPhoneFormat($item['phoneNum'])?></td>
                      <td class="text-center"><?php echo setPhoneFormat($item['faxNum'])?></td>
                      <td class="text-center"><?php echo $item['chargeName']?></td>
                      <td class="text-center">
                          <?php echo setPhoneFormat($item['chargePhoneNum'])?><br>
                          <?php echo setPhoneFormat($item['chargeDirectPhoneNum'])?>
                      </td>
                      <td class="text-left">
                        <?php echo $item['itemName']?>
                        <?php
                            if(!empty($item['optionName'])){
                        ?>
                            <span class="bill-goods-option"> 옵션: <?php echo $item['optionName']?>(<?php echo number_format($item['optionPrice']+$item['optionPriceVAT']) ?>원)</span>
                        <?php
                          }
                        ?>
                      </td>
                      <td class="text-right"><?php echo $item['goodsSpec']?></td>
                      <td class="text-right"><?php echo number_format($item['unitPrice']+($item['optionPrice'])) ?></td>
                      <td class="text-right"><?php echo $item['quantity']?></td>
                      <td class="text-right"><?php echo number_format(($item['unitPrice']+($item['optionPrice']))*$item['quantity']) ?></td>
                      <td class="text-right"><?php echo number_format(($item['unitPriceVAT']+$item['optionPriceVAT'])*$item['quantity']) ?></td>
                      <td class="text-right">
                        <?php echo number_format(($item['unitPrice']+($item['optionPrice']+$item['optionPriceVAT']))*$item['quantity']+($item['unitPriceVAT']*$item['quantity'])) ?>
                      </td>
                      <td class="text-center">
                          <?php if($item['partnerBuyerTypeCode'] == EC_BUYER_TYPE_CONSIGN): ?>
                            <?php if(empty($item['stockOrderID'])): ?>
                              <?php echo "미작성"?>
                            <?php else:?>
                              <?php echo $item['stockOrderDatetime'] ?>
                            <?php endif;?>
                          <?php else:?>
                              <?php echo "-"?>
                          <?php endif;?>
                      </td>

                      <!-- 위탁이며 이전 상품과 매입처과 같으면 rowspan +1 , 아니면 rowspan은 1 -->
                      <?php if($item['partnerBuyerTypeCode'] == EC_BUYER_TYPE_CONSIGN && $item['partnerID'] == $beforePartnerID): ?>
                        <?php $rowSpan += 1; ?>
                      <?php else:?>
                         <?php $rowSpan = 1; ?>
                      <?php endif; $beforePartnerID = $item['partnerID'];?>

                      <!-- rowspan이 1이면 td를 생성하고 1보다 클 경우에는 sciprt를 통해 rowspan을 증가시킨다. -->
                      <?php if($rowSpan == 1): ?>
                        <!-- 발주서 작성/조회 -->
                        <td class="text-center" id="inOutTable_<?php echo $idx ?>">
                          <?php if($item['partnerBuyerTypeCode'] == EC_BUYER_TYPE_CONSIGN): ?>
                              <?php if(empty($item['stockOrderID'])): ?>
                                <button type="button" class="btn btn-default" id="add-new-btn" onclick="onClickConsigmentWrite('<?php echo $item['partnerID'] ?>','<?php echo $orderID ?>','<?php echo $orderInfo['garageID'] ?>')">작성</button>
                              <?php else:?>
                                 <button type="button" class="btn btn-default" id="add-new-btn" onclick='onClickConsigmentView(<?php echo json_encode($item, true)?>)'>조회</button>
                                
                              <?php endif;?>
                            <?php else:?>
                                <?php echo "-"?>
                            <?php endif;?>
                        </td>
                        <td class="text-center" id="delivery_<?php echo $idx ?>">
                          <?php if(isset($item['itemLogisticsURI'])): ?>
                            <button type="button" class="btn btn-default btn-xs" onclick='getOrderDeliveryStatus(<?php echo json_encode($item, true)?>)' >조회</button>
                          <?php else:?>
                            -
                          <?php endif;?>
                        </td>
                      <?php else:?>
                          <!-- 배송지 조회  -->
                          <script>
                              $("#inOutTable_<?php echo $idx - $rowSpan +1 ?>").attr('rowspan', "<?php echo $rowSpan ?>");
                              $("#delivery_<?php echo $idx - $rowSpan +1 ?>").attr('rowspan', "<?php echo $rowSpan ?>");  
                          </script>
                      <?php endif;?>
                    </tr>
                  <?php }?>
                </tbody>
                <tfoot>
                  <tr class="border-double-top">
                    <td class="text-center active" colspan="9">합계</td>
                    <td class="text-right"><?php echo number_format($totalUnitPrice) ?></td>
                    <td class="text-right"><?php echo $totalCnt ?></td>
                    <td class="text-right"><?php echo number_format($totalPrice) ?></td>
                    <td class="text-right"><?php echo number_format($totalVAT)?></td>
                    <td class="text-right"><?php echo number_format($totalVAT + $totalPrice)?></td> 
                    <td class="text-right"></td>
                    <td class="text-right"></td>
                    <td class="text-right"></td>
                  </tr>
                </tfoot>
              </table>
            </div>
          
        </div>
 

      </div>

      <!-- 등록/수정 모달  -->
      <?php	require "orderCancelModal.html"; ?>

      <!-- 실시간이체시 환불요청 모달 -->
      <?php require $_SERVER['DOCUMENT_ROOT']."/easypay80_webpay_php/web/mgr/mgrModal.php";?>
</div>
</body>

<!-- footer.html -->
<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/footerpopup.html";
?>

<script type="text/javascript" src="/js/jquery-barcode.js"></script>
<script>
    let statusText = "";
    let isClickPaymentCancel = false;  // PG취소버튼을 눌렀는지...

    $('#barcodeContainer').barcode("<?php echo $orderID ?>", "code128", {
        barWidth: 1, barHeight: 50, moduleSize: 5, showHRI: true, moduleSize: 5, addQuietZone: true, marginHRI: 5, bgColor: "#FFFFFF", color: "#000000", fontSize: 12, output: "css", posX: 0, posY: 0 
    });   
    
    $('.date').change(function(e){
      var date_pattern = /^(19|20)\d{2}-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[0-1])$/; 
      if(!date_pattern .test($(this).val())){
        alert("yyyy-mm-dd 양식에 올바르지 않습니다.");
        $(this).val("");
        $(this).focus();
        return;
      }
    });

    /** 주문상태 변경 */
    function onchangeOrderStatus(orderStatusCode){
      const data = {
        REQ_MODE : CASE_UPDATE,
        orderID : "<?php echo $orderID ?>",
        orderStatusCode : orderStatusCode
      };
      callAjax(ERP_CONTROLLER_URL, 'POST', data).then((result)=>{
          if(isJson(result)){
              const res = JSON.parse(result);
              if(res.status == OK){
                alert(statusText+ "상태로 변경이 완료되었습니다.");
                window.opener.getList();
                location.reload();
              }else{
                  alert(ERROR_MSG_1100);
              }
          }else{
              alert(ERROR_MSG_1200);
          }
      }); 
    }

     /** 교환 및 반품상태 변경 */
     function onchangeOrderStatusRefund(){
      if(!empty($('#returnStatusSelector').val())){
        confirmSwalTwoButton($('#returnStatusSelector :selected').text()+" 상태로 변경 하시겠습니까?",'확인', '취소', false ,()=>{
          statusText = $('#returnStatusSelector :selected').text();
          onchangeOrderStatus($('#returnStatusSelector').val());
        }, '');
      }else{
        alert("교환 및 반품 상태를 선택해주세요.");
      } 
    }

    /** 배송상태 변경 */
    function onchangeOrderStatusDelivery(){
      confirmSwalTwoButton("배송완료 상태로 변경 하시겠습니까?",'확인', '취소', false ,()=>{
          statusText = "배송완료";
          onchangeOrderStatus(EC_ORDER_STATUS_DELIVERY_COMPLETE);
        }, '');
    }

    /** 상품준비로 변경 */
    function onchangeOrderStatusReady(orderStatusCode){
      confirmSwalTwoButton("상품준비 상태로 변경 하시겠습니까?",'확인', '취소', false ,()=>{
        statusText = "상품준비";
        onchangeOrderStatus(orderStatusCode);
      }, '');
    }

    function onchangeDepositPrice(){
      const totalPrice = "<?php echo $orderInfo['totalPrice'] ?>";
      const depositPrice = $('#DEPOSIT_YN_PRICE').val();

      if(Number(depositPrice) > Number(totalPrice)){
        alert("최종금액 이상보다 입금할 수 없습니다.");
        $('#DEPOSIT_YN_PRICE').val("");
        $('#DEPOSIT_YN_PRICE').focus();
      }
    }

    function updateData(data, statusText){
      callFormAjax(ERP_CONTROLLER_URL, 'POST', data).then((result)=>{
          if(isJson(result)){
              const res = JSON.parse(result);
              if(res.status == OK){
                alert(statusText+" 처리가 완료되었습니다.");
                window.opener.getList();
                location.reload();
              }else{
                  alert(ERROR_MSG_1100);
              }
          }else{
              alert(ERROR_MSG_1200);
          }    
      });  
    }

    $('#memoForm').submit(function(e){
      e.preventDefault();
      
      confirmSwalTwoButton("메모를 저장 하시겠습니까?",'확인', '취소', false ,()=>{
        let form = $("#memoForm")[0];
        let formData = new FormData(form);
        formData.append('REQ_MODE', CASE_UPDATE);
        formData.append('orderID', "<?php echo $orderID ?>");
        updateData(formData, "메모작성");
        
      }, '');
    }); 


    $('#refundModalForm').submit(function(e){
      e.preventDefault();

      if(isClickPaymentCancel){                                       // 결제취소
        onCasePgCancel($('#refundModalForm').serialize());
      }else{
        const orderStatus = "<?=$orderInfo['orderStatusCode']?>";
        if(orderStatus == "<?=EC_ORDER_STATUS_PAY_COMPLETE?>"){       // 결제취소
          onCasePgCancel();
        }else{   
          onCancelOrder();
        }
      }
    }); 

    $('#depositForm').submit(function(e){
      e.preventDefault();

      confirmSwalTwoButton("입금처리 하시겠습니까?",'확인', '취소', false ,()=>{
        let form = $("#depositForm")[0];
        let formData = new FormData(form);
        formData.append('REQ_MODE', CASE_UPDATE);
        formData.append('orderID', "<?php echo $orderID ?>");
        formData.append('paymentCode', "<?php echo $orderInfo['paymentCode']?>");
        updateData(formData, "입금처리");
        
      }, '');
    }); 

    $('#deliveryForm').submit(function(e){
      e.preventDefault();

      confirmSwalTwoButton("배송처리 하시겠습니까?",'확인', '취소', false ,()=>{
        let form = $("#deliveryForm")[0];
        let formData = new FormData(form);
        formData.append('REQ_MODE', CASE_UPDATE);
        formData.append('orderID', "<?php echo $orderID ?>");
        updateData(formData, "배송처리");
      }, '');
    });
    

    $('#resendForm').submit(function(e){
      e.preventDefault();
      
      confirmSwalTwoButton("재배송 처리 하시겠습니까?",'확인', '취소', false ,()=>{
        let form = $("#resendForm")[0];
        let formData = new FormData(form);
        formData.append('REQ_MODE', CASE_UPDATE);
        formData.append('orderID', "<?php echo $orderID ?>");
        formData.append('resendTypeCode', $('input:radio[name= RETURN_REASON]:checked').val());
       
        updateData(formData, "재배송처리");
      }, '');
    }); 

    $('#cancelForm').submit(function(e){
      e.preventDefault();
      confirmSwalTwoButton("반품처리 하시겠습니까?",'확인', '취소', false ,()=>{
        let form = $("#cancelForm")[0];
        let formData = new FormData(form);
        formData.append('REQ_MODE', CASE_UPDATE);
        formData.append('orderID', "<?php echo $orderID ?>");
        formData.append('returnTypeCode', $('input:radio[name= RETURN_REASON]:checked').val());
        updateData(formData, "반품처리");
        
      }, '');
    }); 

    $('#refundForm').submit(function(e){
      e.preventDefault();
      confirmSwalTwoButton("환불처리 하시겠습니까?",'확인', '취소', false ,()=>{
        let form = $("#refundForm")[0];
        let formData = new FormData(form);
        formData.append('REQ_MODE', CASE_UPDATE);
        formData.append('orderID', "<?php echo $orderID ?>");
        updateData(formData, "환불처리");
      }, '입금여부를 확인해주세요.');

    }); 

    function onReloadListDetail(){
      location.reload();
    }

    /** 주문취소 */
    function onCancelOrder(){
      let paymentText = "신용카드 결제 시 승인취소를 확인해주세요.";
      if($('#paymentTypeCode').attr('data') == EC_PAYMENT_TYPE_DEPOSIT){
        paymentText = "입금상태를 확인해주세요.";
      }
      
      confirmSwalTwoButton("주문취소 하시겠습니까?",'확인', '취소', true ,()=>{
        let form = $("#refundModalForm")[0];
        let formData = new FormData(form);
        formData.append('REQ_MODE', CASE_DELETE);
        formData.append('orderID', "<?php echo $orderID ?>");
        updateData(formData, "주문취소");
        
      }, "");  
    }

    /** PG 취소 케이스 */
    function onCasePgCancel(){
      const paymentType = "<?=$orderInfo['paymentCode']?>";
      if(paymentType == "<?=EC_PAYMENT_TYPE_TRANSFER?>"){                 // 계좌이체
          onConfirmRefundTransfer();
      }else if(paymentType == "<?=EC_PAYMENT_TYPE_CREDIT?>"){             // 신용카드
          onConfirmPgRefund('결제',40);
      }
    }

    /** 신용카드 결제/취소 콜백 */
    function onCallbackKICC(resultInfo){
      console.log(resultInfo);
      // const a = '{"res_cd":"0000","res_msg":"계좌이체 취소 성공","cno":"20111916370410576103","amount":"","order_no":"2020OD1119DPQI590553","auth_no":"","tran_date":"20201119163844","escrow_yn":"N","complex_yn":"","stat_cd":"TS06","stat_msg":"부분매입취소","pay_type":"21","mall_id":"","card_no":"","issuer_cd":"","issuer_nm":"","acquirer_cd":"","acquirer_nm":"","install_period":"","noint":"","part_cancel_yn":"","card_gubun":"","card_biz_gubun":"","cpon_flag":"","bank_cd":"","bank_nm":"","account_no":"","deposit_nm":"","expire_date":"","cash_res_cd":"","cash_res_msg":"","cash_auth_no":"","cash_tran_date":"","cash_issue_type":"","cash_auth_type":"","cash_auth_value":"","auth_id":"","billid":"","mobile_no":"","mob_ansim_yn":"","ars_no":"","cp_cd":"","pnt_auth_no":"","pnt_tran_date":"","used_pnt":"","remain_pnt":"0","pay_pnt":"","accrue_pnt":"0","deduct_pnt":"","payback_pnt":"","cpon_auth_no":"","cpon_tran_date":"","cpon_no":"","remain_cpon":"","used_cpon":"","rem_amt":"","bk_pay_yn":"N","canc_acq_date":"","canc_date":"20201119163844","refund_date":"","cancelTypeCode":"81210","cancelDetailTypeCode":""}';
       onResultKICC(resultInfo , ()=>{
           window.location.reload();
          if(!empty(opener)){
            window.opener.getList();
          }
       });
    }

    /** PG 취소 */
    function onConfirmRefund(){
        isClickPaymentCancel = true;

        const paymentType = "<?=$orderInfo['paymentCode']?>";
        if(paymentType == "<?=EC_PAYMENT_TYPE_TRANSFER?>"){                 // 계좌이체
            $('#cancel-modal #submitBtn').html("이체취소");
        }else if(paymentType == "<?=EC_PAYMENT_TYPE_CREDIT?>"){             // 신용카드
            $('#cancel-modal #submitBtn').html("결제취소");
        }
        $('#cancel-modal').modal();
    }

    /** 계좌이체 취소 */
    function onConfirmRefundTransfer(){
      if($('#cancel-modal').is(":visible")){
        $('#cancel-modal').modal('hide');
      }
      $('#transfer_modal').modal();
    }

    /** PG취소 */
    function onConfirmPgRefund(typeText, mgrType, formSerialize){
      confirmSwalTwoButton(typeText+' 취소 하시겠습니까?','확인','취소',true,function(){
          const url = '../../../easypay80_webpay_php/web/mgr/mgr.php?'
          let param = "&orderID=<?=$orderID?>&mgrType="+mgrType;
          param += "&"+$('#refundModalForm').serialize();

          if(!empty(formSerialize)){
            param += "&"+formSerialize;
          }
          openWindowPopup(url+param, 'refundPopup', 1, 1); 
      },typeText+' 취소하시면 자동으로 주문취소 됩니다.'); 
    }
    
    
</script>


