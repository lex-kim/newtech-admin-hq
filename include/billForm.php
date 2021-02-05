<div class="modal-body"> 
    <!-- 입금일자 -->
    <div class="row">
    <div class="col-md-2"><label for="EVIDENCE">지출증빙</label></div>
    <div class="col-md-10">
        <?php echo getRadio($this->optionArray['EC_BILL_TYPE'], "EVIDENCE", "") ?>
        <table class="table table-bordered cEVIDENCE" id="EVIDENCE_CASH" style="display:none">
        <colgroup>
            <col width="3%"/>
            <col width="7%"/>
        </colgroup>
        <tr>
            <td class="text-center active">구분 <span class="necessary">*</span></td>
            <td>
                <?php echo getRadio($this->optionArray['EC_BILL_CASH_TYPE'], "EVIDENCE_CASH", "") ?>
            </td>
        </tr>
        <tr class="hiddenTrack">
            <td class="text-center active">거래유형 <span class="necessary">*</span></td>
            <td>
                <?php echo getRadio($this->optionArray['EC_BILL_CASH_OPTION'], "CAHS_OPTION", "") ?>
            </td>
        </tr>
        <tr class="hiddenTrack">
            <td class="text-center active">과세여부 <span class="necessary">*</span></td>
            <td>
                <label class="radio-inline" style="margin-left:6px;"><input type="radio"  name="billApplyVatYN"  value="Y" checked>Y</label>
                <label class="radio-inline"><input type="radio"  name="billApplyVatYN"  value="N">N</label>
            </td>
        </tr>
        <tr>
            <td class="text-center active">이메일</td>
            <td>
                <input type="text" class="form-control notRequired" value="<?php echo $garageInfo['billEmail'] ?>"
                    name="billEmail" id="billEmail" placeholder="입력"/>
            </td>
        </tr>
        <tr class="CASH_PERSONAL">
            <td class="text-center active">담당자명</td>
            <td>
                <input type="text" class="form-control CASH_PERSONAL_notRequired" value="<?php echo $garageInfo['billName'] ?>"
                    name="billName" id="billName" placeholder="입력"/>
            </td>
        </tr>
        <tr class="CASH_CEO">
            <td class="text-center active">상호명</td>
            <td>
                <input type="text" class="form-control CASH_CEO_notRequired" value="<?php echo $garageInfo['billCompanyName'] ?>"
                    name="billCompanyName" id="billCompanyName" placeholder="입력"/>
            </td>
        </tr>
        <tr>
            <td class="text-center active" ><span id="EVIDENCE_CASH_TITLE">핸드폰 번호</span> <span class="necessary">*</span></td>
            <td>
            <input type="text" class="form-control CASH_PERSONAL" onkeyup="isNumber(event)" value="<?php echo $garageInfo['billPhoneNum'] ?>"
                name="EVIDENCE_CASH_PERSONAL" id="EVIDENCE_CASH_PERSONAL" placeholder="'-'를 제외한 숫자만 입력"/>
            <input type="text" class="form-control CASH_CEO" onkeyup="isNumber(event)" value="<?php echo $garageInfo['billCompanyRegID'] ?>"
                name="EVIDENCE_CASH_CEO" id="EVIDENCE_CASH_CEO" placeholder="'-'를 제외한 숫자만 입력"/>
            </td>
        </tr>
        </table>
        <div id="EVIDENCE_TAX" class="cEVIDENCE" style="display:none">
        <table class="table table-bordered">
            <colgroup>
            <col width="3.5%"/>
            <col width="6.5%"/>
            </colgroup>
           
            <tr class="hiddenTrack">
                <td class="text-center active">발행형태</td>
                <td>
                    <?php echo getRadio($this->optionArray['EC_BILL_ISSUE_TYPE'], "issueTypeCode", "") ?>
                </td>
            </tr>
            <tr class="hiddenTrack">
                <td class="text-center active">과세형태 <span class="necessary">*</span></td>
                <td>
                    <?php echo getRadio($this->optionArray['EC_BILL_TAX_OPTION'], "taxTypeCode", (empty($cShoppingInfo)? "" : $cShoppingInfo['taxTypeCode'])) ?>
                </td>
            </tr>
            <tr class="hiddenTrack">
                <td class="text-center active">청구구분 <span class="necessary">*</span></td>
                <td>
                    <?php echo getRadio($this->optionArray['EC_BILL_PURPOSE_TYPE'], "purposeTypeCode", "") ?>
                </td>
            </tr>
            <tr class="hiddenTrack">
                <td class="text-center active">구분 <span class="necessary">*</span></td>
                <td>
                    <?php echo getRadio($this->optionArray['EC_BILL_RECIPIENT_TYPE'], "recipientTypeCode", "") ?>
                </td>
            </tr>
            <tr>
                <td class="text-center active"><span id="recipientTitle">사업자등록번호</span> <span class="necessary">*</span></td>
                <td>
                    <input type="text" class="form-control" onkeyup="isNumber(event)" value="<?php echo $garageInfo['billCompanyRegID'] ?>"
                        name="toCompanyRegID" id="toCompanyRegID" placeholder="'-'를 제외한 숫자만 입력" onchange="onChangeRecipientValue()"/>
                </td>
            </tr>
            <tr>
                <td class="text-center active">상호(법인명) <span class="necessary">*</span></td>
                <td>
                    <input type="text" class="form-control" value="<?php echo $garageInfo['billCompanyName'] ?>"
                        name="TAX_ORDER_BIZ_NM" id="TAX_ORDER_BIZ_NM" placeholder="입력"/>
                </td>
            </tr>
            <tr>
            <td class="text-center active">대표자명 <span class="necessary">*</span></td>
            <td>
                <input type="text" class="form-control" value="<?php echo $garageInfo['billCeoName'] ?>"
                    name="TAX_ORDER_BIZ_CEO_NM" id="TAX_ORDER_BIZ_CEO_NM" placeholder="입력"/>
            </td>
            </tr>
            <tr>
            <td class="text-center active">주소</td>
                <td>
                    <input type="text" class="form-control notRequired" value="<?php echo $garageInfo['billAddress'] ?>"
                         name="TAX_ORDER_BIZ_ADDRESS" id="TAX_ORDER_BIZ_ADDRESS" placeholder="입력"/>
                </td>
            </tr>
            <tr>
            <td class="text-center active">업태</td>
                <td>
                    <input type="text" class="form-control notRequired" value="<?php echo $garageInfo['billCompanyBiz'] ?>"
                    name="TAX_ORDER_BIZ_BIZ" id="TAX_ORDER_BIZ_BIZ" placeholder="입력"/>
                </td>
            </tr>
            <tr>
                <td class="text-center active">종목</td>
                <td>
                    <input type="text" class="form-control notRequired" value="<?php echo $garageInfo['billCompanyPart'] ?>"
                    name="TAX_ORDER_BIZ_EVENT" id="TAX_ORDER_BIZ_EVENT" placeholder="입력"/>
                </td>
            </tr>
            
            <tr>
                <td class="text-center active">담당자명1 <span class="necessary">*</span></td>
                <td>
                    <input type="text" class="form-control" value="<?php echo $garageInfo['billName'] ?>"
                        name="toChargeName1" id="toChargeName1" placeholder="입력"/>
                </td>
            </tr>
            <tr>
                <td class="text-center active">담당자부서1</td>
                <td>
                    <input type="text" class="form-control notRequired" value="<?php echo $garageInfo['fromChargeDepartment'] ?>"
                        name="toChargeDeptName1" id="toChargeDeptName1" placeholder="입력"/>
                </td>
            </tr>
            <tr>
                <td class="text-center active">담당자 연락처1</td>
                <td>
                    <input type="text" class="form-control notRequired" onkeyup="isNumber(event)" 
                        name="toChargeTel1" id="toChargeTel1" placeholder="입력"/>
                </td>
            </tr>
            <tr>
                <td class="text-center active">담당자 휴대폰1 <span class="necessary">*</span></td>
                <td>
                    <input type="text" class="form-control" onkeyup="isNumber(event)" value="<?php echo $garageInfo['billPhoneNum'] ?>"
                        name="toChargePhoneNum1" id="toChargePhoneNum1" placeholder="입력"/>
                </td>
            </tr>
            <tr>
                <td class="text-center active">담당자 이메일1 <span class="necessary">*</span></td>
                <td>
                    <input type="text" class="form-control"  value="<?php echo $garageInfo['billEmail'] ?>"
                        name="toChargeEmail1" id="toChargeEmail1" placeholder="입력"/>
                </td>
            </tr>
            
            <tr>
                <td class="text-center active">담당자명2</td>
                <td>
                    <input type="text" class="form-control notRequired" 
                        name="toChargeName2" id="toChargeName2" placeholder="입력"/>
                </td>
            </tr>
            <tr>
                <td class="text-center active">담당자부서2</td>
                <td>
                    <input type="text" class="form-control notRequired" 
                        name="toChargeDeptName2" id="toChargeDeptName2" placeholder="입력"/>
                </td>
            </tr>
            <tr>
                <td class="text-center active">담당자 연락처2</td>
                <td>
                    <input type="text" class="form-control notRequired" onkeyup="isNumber(event)" 
                        name="toChargeTel2" id="toChargeTel2" placeholder="입력"/>
                </td>
            </tr>
            <tr>
                <td class="text-center active">담당자 휴대폰2</td>
                <td>
                    <input type="text" class="form-control notRequired" onkeyup="isNumber(event)"
                        name="toChargePhoneNum2" id="toChargePhoneNum2" placeholder="입력"/>
                </td>
            </tr>
            <tr>
                <td class="text-center active">담당자 이메일2</td>
                <td>
                    <input type="text" class="form-control notRequired" 
                        name="toChargeEmail2" id="toChargeEmail2" placeholder="입력"/>
                </td>
            </tr>
            <tr>
                <td class="text-center active">메모</td>
                <td>
                    <input type="text" class="form-control notRequired" name="memo" id="memo" placeholder="입력"/>
                </td>
            </tr>
        </table>
        </div>
    </div>
</div>

<script>
    let regIdValue = "<?php echo $garageInfo['companyRegID'] ?>";
    let personIdValue = "";
    let forignIdValue = FORIGN_ID_VALUE;

    (function ($) {
        $.each(['show', 'hide'], function (i, ev) {
        var el = $.fn[ev];
        $.fn[ev] = function () {
            this.trigger(ev);
            return el.apply(this, arguments);
        };
        });
    })(jQuery);

    $(".cEVIDENCE").bind("hide", function(){
        const id = $(this).attr("id");
        $("#"+id+" input").prop("required",false);
        $("#"+id+" input").attr("disabled",true);
    });

    $(".cEVIDENCE").bind("show", function(){
        const id = $(this).attr("id");
        $("#"+id+" input").prop("required",true);
        $("#"+id+" input").attr("disabled",false); 

        if($("#"+id+" .notRequired").length > 0){
            $("#"+id+" .notRequired").prop("required",false);
        }
    });

    function onChangeRecipientValue(){
        const value = $(':input:radio[name=recipientTypeCode]:checked').val();
        if(value == EC_BILL_RECIPIENT_TYPE_CEO){                 // 사업자
            regIdValue = $('#toCompanyRegID').val();
        }else if(value == EC_BILL_RECIPIENT_TYPE_PERSONAL){      // 개인
            personIdValue = $('#toCompanyRegID').val();
        }else{   
            forignIdValue = FORIGN_ID_VALUE;                                         
        }
    }

    function onChangeRecipientTypeCode(recipientTypeCode){
        $('#toCompanyRegID').attr("readonly", false);
        if(recipientTypeCode == EC_BILL_RECIPIENT_TYPE_CEO){                 // 사업자
            $('#recipientTitle').html("사업자등록번호");
            $('#toCompanyRegID').val(regIdValue);
        }else if(recipientTypeCode == EC_BILL_RECIPIENT_TYPE_PERSONAL){      // 개인
            $('#recipientTitle').html("개인주민번호");
            $('#toCompanyRegID').val(personIdValue);
        }else{                                                               // 외국인
            $('#recipientTitle').html("사업자등록번호");
            $('#toCompanyRegID').val(forignIdValue);
            $('#toCompanyRegID').attr("readonly", true);
        }
    }

    $(document).ready(function(){
      /**
       * 현금영수증 인지 세금계산서 인지 이벤트..
       */
      $(":input:radio[name=EVIDENCE]").click(function(){
        var EVIDENCE = $(":input:radio[name=EVIDENCE]:checked").val();
        $('#billTypeCode').val(EVIDENCE);
        
        if(EVIDENCE == EC_BILL_TYPE_TAX){        // 세금계산서
          $("#EVIDENCE_CASH").hide();
          $("#EVIDENCE_TAX").show();
          $(':input:radio[name=recipientTypeCode]').change(function(){
              onChangeRecipientTypeCode($(this).val());
          });
        }else if(EVIDENCE == EC_BILL_TYPE_CASH){ // 현금영수증
          $("#EVIDENCE_TAX").hide();
          $("#EVIDENCE_CASH").show();
          $(":input:radio[name=EVIDENCE_CASH]:checked").trigger("click");
        }else{                                   // 신청안함
          $("#EVIDENCE_TAX").hide();
          $("#EVIDENCE_CASH").hide();
        }
      });

      /**
       * 현금영수증에서 개인/사업자 라디오 버튼 이벤트
       */
      $(":input:radio[name=EVIDENCE_CASH]").click(function(){
        var selectedValue = $(":input:radio[name=EVIDENCE_CASH]:checked").val();
       
        if(selectedValue == EC_BILL_CASH_TYPE_PERSONAL){  // 개인사업자
          $("#EVIDENCE_CASH_TITLE").html("핸드폰 번호");
          $(".CASH_CEO").hide();
          $(".CASH_PERSONAL").show();

          $(".CASH_CEO").prop("required" , false);
          $(".CASH_CEO").prop("disabled" , true);
          $(".CASH_PERSONAL_notRequired").prop("required" , false);
          $(".CASH_CEO_notRequired").prop("disabled" , true);
        }else{                                          // 사업자
          $("#EVIDENCE_CASH_TITLE").html("사업자 번호");
          $(".CASH_PERSONAL").hide();
          $(".CASH_CEO").show();

          $(".CASH_PERSONAL").prop("required" , false);
          $(".CASH_PERSONAL").prop("disabled" , true);
          $(".CASH_CEO_notRequired").prop("required" , false);
          $(".CASH_PERSONAL_notRequired").prop("disabled" , true);
        }
        $('.notRequired').prop("required", false);
      });

      $(":input:radio[name=EVIDENCE]:checked").trigger("click");
  });
</script>