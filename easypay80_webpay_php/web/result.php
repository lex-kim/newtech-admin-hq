<html>
<title>newtech</title>
<meta name="robots" content="noindex, nofollow"> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="./css/style.css" rel="stylesheet" type="text/css">
<script src="/js/jquery-1.12.4.min.js"></script>
<script language="javascript" src="./js/default.js" type="text/javascript"></script>

<?
    // CA : 카드승인, CAO : 카드승인옵션 
    // CC : 카드승인취소, CCO : 카드승인취소옵션, CPC : 카드매입취소 
    $res_cd               = $_POST["res_cd"];           // 응답코드          (CA, CAO, CC, CCO, CPC)
    $res_msg              = $_POST["res_msg"];          // 응답메시지        (CA, CAO, CC, CCO, CPC)
    $cno                  = $_POST["cno"];              // PG거래번호        (CA, CAO, CC, CCO, CPC)
    $amount               = $_POST["amount"];           // 총 결제금액       (CA,                  )
    $order_no             = $_POST["order_no"];         // 주문번호          (CA,                  )
    $auth_no              = $_POST["auth_no"];          // 승인번호          (CA,                  )
    $tran_date            = $_POST["tran_date"];        // 승인일시          (CA,      CC,      CPC)
    $escrow_yn            = $_POST["escrow_yn"];        // 에스크로 사용유무 (CA,                  )
    $complex_yn           = $_POST["complex_yn"];       // 복합결제 유무     (CA,                  )
    $stat_cd              = $_POST["stat_cd"];          // 상태코드          (CA,      CC,      CPC)
    $stat_msg             = $_POST["stat_msg"];         // 상태메시지        (CA,      CC,      CPC)
    $pay_type             = $_POST["pay_type"];         // 결제수단          (CA,                  )
    $mall_id              = $_POST["mall_id"];          // 가맹점 Mall ID    (CA                   )
    $card_no              = $_POST["card_no"];          // 카드번호          (CA,          CCO     )
    $issuer_cd            = $_POST["issuer_cd"];        // 발급사코드        (CA,          CCO     )
    $issuer_nm            = $_POST["issuer_nm"];        // 발급사명          (CA,          CCO     )
    $acquirer_cd          = $_POST["acquirer_cd"];      // 매입사코드        (CA,          CCO     )
    $acquirer_nm          = $_POST["acquirer_nm"];      // 매입사명          (CA,          CCO     )
    $install_period       = $_POST["install_period"];   // 할부개월          (CA,          CCO     )
    $noint                = $_POST["noint"];            // 무이자여부        (CA                   )
    $part_cancel_yn       = $_POST["part_cancel_yn"];   // 부분취소 가능여부 (CA                   )
    $card_gubun           = $_POST["card_gubun"];       // 신용카드 종류     (CA                   )
    $card_biz_gubun       = $_POST["card_biz_gubun"];   // 신용카드 구분     (CA                   )
    $cpon_flag            = $_POST["cpon_flag"];        // 쿠폰 사용유무     (    CAO,     CCO     )
    $used_cpon            = $_POST["used_cpon"];        // 쿠폰 사용금액     (    CAO              )
    $canc_acq_date        = $_POST["canc_acq_date"];    // 매입취소일시      (                  CPC)
    $canc_date            = $_POST["canc_date"];        // 취소일시          (CC,               CPC)
    $account_no           = $_POST["account_no"];       // 계좌번호          (CC,                  )

?>
<script>
    window.close();
    if("<?=$_POST["res_cd"]?>" == "0000"){
        alert("<?=$_POST['res_msg']?>"+" 되었습니다.");
        window.opener.onCallbackKICC('<?=json_encode($_POST) ?>');
    }else{
        window.opener.alert("<?=$_POST["res_msg"]?>");
    }
</script>
</html>
