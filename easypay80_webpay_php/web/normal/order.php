<?php require_once $_SERVER['DOCUMENT_ROOT']."/common/common.php"; ?>
<?php 
    require_once $_SERVER['DOCUMENT_ROOT']."/600_shop/620_history/class/OrderList.php";

    if(empty($_REQUEST['orderID'])){
        echo "<script>document.write(alert('올바르지 않은 경로 입니다.')); window.close();</script>";
    }

    $cOrder = new OrderList();
    $orderResult = $cOrder->getData($_REQUEST['orderID']);
    
    if($orderResult['status'] == OK){
        if($orderResult['data'] == HAVE_NO_DATA || empty($orderResult['data'] ) ){
            echo "<script>document.write(alert('올바르지 주문번호 입니다.')); window.close();</script>";
        }else{
            $orderInfo = $orderResult['data'];
        }
    }else{
        echo "<script>document.write(alert('올바르지 주문번호 입니다.')); window.close();</script>";
    }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>newtech</title>
<meta name="robots" content="noindex, nofollow">
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="requiresActiveX=true">
<meta http-equiv="cache-control" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
<meta http-equiv="Pragma" content="no-cache"/>
<link href="../css/style.css" rel="stylesheet" type="text/css">
<style>
    #KICC_PAYMENTWINDOW_POPUP{
        top : 0 !important;
        bottom : 0 !important;
    }
    
</style>
<script src="/js/jquery-1.12.4.min.js"></script>
<script src="/js/common.js"></script>
<script language="javascript" src="../js/default.js" type="text/javascript"></script>
<!-- Test -->
<!-- <script type="text/javascript" src="http://testpg.easypay.co.kr/webpay/EasypayCard_Web.js"></script> -->
<!-- Real -->
<script type="text/javascript" src="https://pg.easypay.co.kr/webpay/EasypayCard_Web.js"></script>

<script type="text/javascript">
    /* 입력 자동 Setting */
    function f_init()
    {
        
        var frm_pay = document.frm_pay;
        var today = new Date();
        var year  = today.getFullYear();
        var month = today.getMonth() + 1; 
        var date  = today.getDate();
        var time  = today.getTime();

        if(parseInt(month) < 10)
        {
            month = "0" + month;
        }

        if(parseInt(date) < 10)
        {
            date = "0" + date;
        }


        /*--공통--*/        
        frm_pay.EP_mall_id.value        = "<?php echo $orderInfo['mallPgID'] ?>";                                //가맹점 ID
        frm_pay.EP_mall_nm.value        = "<?php echo $orderInfo['mallCompanyName'] ?>";                                   //가맹점명
        frm_pay.EP_order_no.value       = "<?php echo $orderInfo['orderID'] ?>";     //가맹점 주문번호    
                                                                                     //결제수단(select)
        frm_pay.EP_currency.value       = "00";                                      //통화코드 : 00-원
        frm_pay.EP_product_nm.value     = "<?php echo $orderInfo['orderSummary'] ?>";                                 //상품명
        frm_pay.EP_product_amt.value    = "<?php echo $orderInfo['totalPrice'] ?>";                               //상품금액
                                                                                   //가맹점 return_url(윈도우 타입 선택 시, 분기)
        frm_pay.EP_lang_flag.value      = "KOR"                                    //언어: KOR / ENG
        frm_pay.EP_user_id.value        = "<?php echo $orderInfo['mallCompanyRegID'] ?>";                               //가맹점 고객 ID
        frm_pay.EP_memb_user_no.value   = "";                                        //가맹점 고객 일련번호
        frm_pay.EP_user_nm.value        = "<?php echo $orderInfo['mallCompanyCeoName'] ?>";                                       //가맹점 고객명
        frm_pay.EP_user_mail.value      = "";                            //가맹점 고객 이메일
        frm_pay.EP_user_phone1.value    = "";                            //가맹점 고객 번호1
        frm_pay.EP_user_phone2.value    = "";                           //가맹점 고객 번호2
        frm_pay.EP_user_addr.value      = "";                           //가맹점 고객 주소
        frm_pay.EP_product_type.value   = "0";                                     //상품정보구분 : 0-실물, 1-서비스
        frm_pay.EP_product_expr.value   = "";                              //서비스기간 : YYYYMMDD
        frm_pay.EP_product_expr.value   = "";                                      //서비스기간 : YYYYMMDD
        frm_pay.EP_return_url.value     = "<?php echo SERVER_URI ?>/easypay80_webpay_php/web/normal/order_res.php";      // Return 받을 URL (HTTP부터 입력)
        // frm_pay.EP_window_type.value == "iframe";
        <?php 
            $paymentCode = "";
            if($orderInfo['paymentCode'] == EC_PAYMENT_TYPE_CREDIT){              // 신용카드
                $paymentCode = "11";
            }else if($orderInfo['paymentCode'] == EC_PAYMENT_TYPE_TRANSFER){      // 실시간이체(계좌이체)
                $paymentCode = "21";
            }else{
        ?>    
            alert("올바르지 않은 결제타입 입니다.");
            window.close();
        <?php   
            }
        ?>
           
        frm_pay.EP_pay_type.value = "<?php echo $paymentCode ?>";

        /*--신용카드--*/                    
        frm_pay.EP_usedcard_code.value  = "";                                      //사용가능한 카드 LIST
        frm_pay.EP_quota.value          = "";                                      //할부개월
 
                                                                                   //무이자 여부(Y/N) (select)   
        // frm_pay.EP_noinst_term.value    = "029-02:03";                             //무이자기간
        frm_pay.EP_noinst_term.value    = "";                             //무이자기간
                                                                                   //카드사포인트 사용여부(select) 
        frm_pay.EP_point_card.value     = "";                                //포인트카드 LIST
        // frm_pay.EP_point_card.value     = "029-40";                                //포인트카드 LIST
                                                                                   //조인코드(select)
                                                                                   //국민 앱카드 사용(select)                                                                                  
                                                                                                                   
        /*--가상계좌--*/                        
        frm_pay.EP_vacct_bank.value     = "";                                      //가상계좌 사용가능한 은행 LIST 
        frm_pay.EP_vacct_end_date.value = "";                              //입금 만료 날짜
        frm_pay.EP_vacct_end_time.value = "";                                //입금 만료 시간
        
        setTimeout(() => {
            document.getElementById("waitContainer").style.display = "none";
            f_cert();
        }, 100);

    }

    
    function kicc_popup_close(){
        window.close();
    }

    /* 인증창 호출, 인증 요청 */
    function f_cert()
    {
        var frm_pay = document.frm_pay;

        /* UTF-8 사용가맹점의 경우 EP_charset 값 셋팅 필수 */
        if( frm_pay.EP_charset.value == "UTF-8" )
        {
            // 한글이 들어가는 값은 모두 encoding 필수.
            frm_pay.EP_mall_nm.value        = encodeURIComponent( frm_pay.EP_mall_nm.value );
            frm_pay.EP_product_nm.value     = encodeURIComponent( frm_pay.EP_product_nm.value );
            frm_pay.EP_user_nm.value        = encodeURIComponent( frm_pay.EP_user_nm.value );
            frm_pay.EP_user_addr.value      = encodeURIComponent( frm_pay.EP_user_addr.value );
        }


        /* 가맹점에서 원하는 인증창 호출 방법을 선택 */
        if( frm_pay.EP_window_type.value == "iframe" )
        {
            easypay_webpay(frm_pay,"./iframe_req.php","hiddenifr","","","iframe","");
            if( frm_pay.EP_charset.value == "UTF-8" )
            {
                // encoding 된 값은 모두 decoding 필수.
                frm_pay.EP_mall_nm.value        = decodeURIComponent( frm_pay.EP_mall_nm.value );
                frm_pay.EP_product_nm.value     = decodeURIComponent( frm_pay.EP_product_nm.value );
                frm_pay.EP_user_nm.value        = decodeURIComponent( frm_pay.EP_user_nm.value );
                frm_pay.EP_user_addr.value      = decodeURIComponent( frm_pay.EP_user_addr.value );
            }
        }
        else if( frm_pay.EP_window_type.value == "popup" )
        {
            easypay_webpay(frm_pay,"./popup_req.php","hiddenifr","","","popup","");
            if( frm_pay.EP_charset.value == "UTF-8" )
            {
                // encoding 된 값은 모두 decoding 필수.
                frm_pay.EP_mall_nm.value        = decodeURIComponent( frm_pay.EP_mall_nm.value );
                frm_pay.EP_product_nm.value     = decodeURIComponent( frm_pay.EP_product_nm.value );
                frm_pay.EP_user_nm.value        = decodeURIComponent( frm_pay.EP_user_nm.value );
                frm_pay.EP_user_addr.value      = decodeURIComponent( frm_pay.EP_user_addr.value );
            }
        }
        
        window.resizeTo($('#KICC_PAYMENTWINDOW_POPUP').width(),$('#KICC_PAYMENTWINDOW_POPUP').height()+50);
        popupX = (getWindow().screen.availWidth)/2 - ($('#KICC_PAYMENTWINDOW_POPUP').width())/2;
        popupX += getWindow().window.screenX;
        popupY = (getWindow().window.screen.height / 2.5) - ($('#KICC_PAYMENTWINDOW_POPUP').height() / 2);

        window.moveTo(popupX, popupY);
    }

    function f_submit()
    {
        var frm_pay = document.frm_pay;

        frm_pay.target = "_self";
        frm_pay.action = "../easypay_request.php";
        frm_pay.submit();
    }
    
</script>
</head>
<body onload="f_init();" style="display:flex; height:100vh; justify-content : center; align-items:center">
    <div id="waitContainer" style="flex:1;text-align:center">주문정보를 가져오는중..</div>

    <form name="frm_pay" method="post" action="">
    <!--------------------------->
    <!-- ::: 공통 인증 요청 값 -->
    <!--------------------------->
    <input type="hidden" id="EP_mall_id"        name="EP_mall_id"           value="<?php echo $orderInfo['mallPgID'] ?>">         <!-- 가맹점ID-->
    <input type="hidden" id="EP_mall_nm"        name="EP_mall_nm"           value="">         <!-- 가맹점명-->
    <input type="hidden" id="EP_order_no"        name="EP_order_no"         value="">        <!-- 주문번호-->
    <input type="hidden" id="EP_currency"       name="EP_currency"          value="00">       <!-- 통화코드 // 00 : 원화-->
    <input type="hidden" id="EP_return_url"     name="EP_return_url"        value="">         <!-- 가맹점 CALLBACK URL // -->
    <input type="hidden" id="EP_ci_url"         name="EP_ci_url"            value="">         <!-- CI LOGO URL // -->
    <input type="hidden" id="EP_lang_flag"      name="EP_lang_flag"         value="">         <!-- 언어 // -->
    <input type="hidden" id="EP_charset"        name="EP_charset"           value="UTF-8">   <!-- 가맹점 CharSet // EUC-KR,UTF-8 사용시 대문자 이용-->
    <input type="hidden" id="EP_user_id"        name="EP_user_id"           value="">         <!-- 가맹점 고객ID // -->
    <input type="hidden" id="EP_memb_user_no"   name="EP_memb_user_no"      value="">         <!-- 가맹점 고객일련번호 // -->
    <input type="hidden" id="EP_user_nm"        name="EP_user_nm"           value="">         <!-- 가맹점 고객명 // -->
    <input type="hidden" id="EP_user_mail"      name="EP_user_mail"         value="">         <!-- 가맹점 고객 E-mail // -->
    <input type="hidden" id="EP_user_phone1"    name="EP_user_phone1"       value="">         <!-- 가맹점 고객 연락처1 // -->
    <input type="hidden" id="EP_user_phone2"    name="EP_user_phone2"       value="">         <!-- 가맹점 고객 연락처2 // -->
    <input type="hidden" id="EP_user_addr"      name="EP_user_addr"         value="">         <!-- 가맹점 고객 주소 // -->
    <input type="hidden" id="EP_user_define1"   name="EP_user_define1"      value="">         <!-- 가맹점 필드1 // -->
    <input type="hidden" id="EP_user_define2"   name="EP_user_define2"      value="">         <!-- 가맹점 필드2 // -->
    <input type="hidden" id="EP_user_define3"   name="EP_user_define3"      value="">         <!-- 가맹점 필드3 // -->
    <input type="hidden" id="EP_user_define4"   name="EP_user_define4"      value="">         <!-- 가맹점 필드4 // -->
    <input type="hidden" id="EP_user_define5"   name="EP_user_define5"      value="">         <!-- 가맹점 필드5 // -->
    <input type="hidden" id="EP_user_define6"   name="EP_user_define6"      value="">         <!-- 가맹점 필드6 // -->
    <input type="hidden" id="EP_product_type"   name="EP_product_type"      value="">         <!-- 상품정보구분 // -->
    <input type="hidden" id="EP_product_expr"   name="EP_product_expr"      value="">         <!-- 서비스 기간 // (YYYYMMDD) -->
    <input type="hidden" id="EP_disp_cash_yn"   name="EP_disp_cash_yn"      value="Y">        <!-- 현금영수증 화면표시여부 //미표시 : "N", 그외: DB조회 -->
    <input type="hidden" id="EP_product_nm"     name="EP_product_nm"        value="">         <!-- 상품명 // -->
    <input type="hidden" id="EP_product_amt"    name="EP_product_amt"       value="">         <!-- 상품가격 // -->
    <input type="hidden" id="EP_window_type"    name="EP_window_type"       value="iframe">   <!-- 윈도우타입(iframe, popup) // -->
    <input type="hidden" id="EP_pay_type"       name="EP_pay_type"          value="">         <!-- 결제방벙(신용카드, 휴대폰, 무통장) // -->
    <input type="hidden" id="EP_cert_type"      name="EP_cert_type"         value="">         <!-- 결제방벙(신용카드, 휴대폰, 무통장) // -->

    <!--------------------------->
    <!-- ::: 카드 인증 요청 값 -->
    <!--------------------------->

    <input type="hidden" id="EP_usedcard_code"      name="EP_usedcard_code"     value="">      <!-- 사용가능한 카드 LIST // FORMAT->카드코드:카드코드: ... :카드코드 EXAMPLE->029:027:031 // 빈값 : DB조회-->
    <input type="hidden" id="EP_quota"              name="EP_quota"             value="">      <!-- 할부개월 (카드코드-할부개월) -->
    <input type="hidden" id="EP_os_cert_flag"       name="EP_os_cert_flag"      value="2">     <!-- 해외안심클릭 사용여부(변경불가) // -->
    <input type="hidden" id="EP_noinst_flag"        name="EP_noinst_flag"       value="">      <!-- 무이자 여부 (Y/N) // -->
    <input type="hidden" id="EP_noinst_term"        name="EP_noinst_term"       value="">      <!-- 무이자 기간 (카드코드-더할할부개월) // -->
    <input type="hidden" id="EP_set_point_card_yn"  name="EP_set_point_card_yn" value="">      <!-- 카드사포인트 사용여부 (Y/N) // -->
    <input type="hidden" id="EP_point_card"         name="EP_point_card"        value="">      <!-- 포인트카드 LIST  // -->
    <input type="hidden" id="EP_join_cd"            name="EP_join_cd"           value="">      <!-- 조인코드 // -->
    <input type="hidden" id="EP_kmotion_useyn"      name="EP_kmotion_useyn"     value="Y">     <!-- 국민앱카드 사용유무 (Y/N)// -->

    <!------------------------------->
    <!-- ::: 가상계좌 인증 요청 값 -->
    <!------------------------------->

    <input type="hidden" id="EP_vacct_bank"         name="EP_vacct_bank"        value="">      <!-- 가상계좌 사용가능한 은행 LIST // -->
    <input type="hidden" id="EP_vacct_end_date"     name="EP_vacct_end_date"    value="">      <!-- 입금 만료 날짜 // -->
    <input type="hidden" id="EP_vacct_end_time"     name="EP_vacct_end_time"    value="">      <!-- 입금 만료 시간 // -->

    <!------------------------------->
    <!-- ::: 선불카드 인증 요청 값 -->
    <!------------------------------->

    <input type="hidden" id="EP_prepaid_cp"         name="EP_prepaid_cp"        value="">      <!-- 선불카드 CP // FORMAT->코드:코드: ... :코드 EXAMPLE->CCB:ECB // 빈값 : DB조회-->

    <!--------------------------------->
    <!-- ::: 인증응답용 인증 요청 값 -->
    <!--------------------------------->

    <input type="hidden" id="EP_res_cd"             name="EP_res_cd"            value="">      <!--  응답코드 // -->
    <input type="hidden" id="EP_res_msg"            name="EP_res_msg"           value="">      <!--  응답메세지 // -->
    <input type="hidden" id="EP_tr_cd"              name="EP_tr_cd"             value="">      <!--  결제창 요청구분 // -->
    <input type="hidden" id="EP_ret_pay_type"       name="EP_ret_pay_type"      value="">      <!--  결제수단 // -->
    <input type="hidden" id="EP_ret_complex_yn"     name="EP_ret_complex_yn"    value="">      <!--  복합결제 여부 (Y/N) // -->
    <input type="hidden" id="EP_card_code"          name="EP_card_code"         value="">      <!--  카드코드 (ISP:KVP카드코드 MPI:카드코드) // -->
    <input type="hidden" id="EP_eci_code"           name="EP_eci_code"          value="">      <!--  MPI인 경우 ECI코드 // -->
    <input type="hidden" id="EP_card_req_type"      name="EP_card_req_type"     value="">      <!--  거래구분 // -->
    <input type="hidden" id="EP_save_useyn"         name="EP_save_useyn"        value="">      <!--  카드사 세이브 여부 (Y/N) // -->
    <input type="hidden" id="EP_trace_no"           name="EP_trace_no"          value="">      <!--  추적번호 // -->
    <input type="hidden" id="EP_sessionkey"         name="EP_sessionkey"        value="">      <!--  세션키 // -->
    <input type="hidden" id="EP_encrypt_data"       name="EP_encrypt_data"      value="">      <!--  암호화전문 // -->
    <input type="hidden" id="EP_spay_cp"            name="EP_spay_cp"           value="">      <!--  간편결제 CP 코드 // -->
    <input type="hidden" id="EP_card_prefix"        name="EP_card_prefix"       value="">      <!--  신용카드prefix // -->
    <input type="hidden" id="EP_card_no_7"          name="EP_card_no_7"         value="">      <!--  신용카드번호 앞7자리 // -->

    </form>
</body>
</html>
