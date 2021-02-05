<?php 
    $req_ip = $_SERVER['REMOTE_ADDR']; // [필수]요청자 IP
    require_once $_SERVER['DOCUMENT_ROOT']."/common/common.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/900_shop/940_orderMngt/941_orderList/class/OrderList.php";

    if(empty($_REQUEST['orderID'])){
        echo "<script>document.write(alert('올바르지 않은 경로 입니다.')); window.close();</script>";
    }

    $cOrder = new OrderList();
    $orderResult = $cOrder->getData($_REQUEST['orderID']);
    
    if($orderResult['status'] == OK){
        if($orderResult['data'] == HAVE_NO_DATA || empty($orderResult['data'] ) ){
            $orderInfo = array();
        }else{
            $orderInfo = $orderResult['data'];
        }
    }else{
        echo "<script>document.write(alert('올바르지 주문번호 입니다.')); window.close();</script>";
    }
?>
<html>
<head>  
<title>newtech</title>
<meta name="robots" content="noindex, nofollow"> 
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link href="../css/style.css" rel="stylesheet" type="text/css">
<script language="javascript" src="../js/default.js" type="text/javascript"></script>
<script type="text/javascript">
    function f_submit() {
        setTimeout(() => {
            document.getElementById("waitContainer").style.display = "none";

            var frm_mgr = document.frm_mgr;
            frm_mgr.submit();
        }, 100);
    }
</script>
</head>
<body onload="f_submit();" style="display:flex; height:100vh; justify-content : center; align-items:center">
    <div id="waitContainer" style="flex:1;text-align:center">주문정보를 가져오는중..</div>
    
    <form name="frm_mgr" method="post" action="../easypay_request.php">
        <input type="hidden" name="EP_tr_cd"                  value="00201000">           <!-- [필수]거래구분(수정불가) -->
        <input type="hidden" name="req_ip"                    value="<?=$req_ip?>">       <!-- [필수]요청자 IP -->
        <input type="hidden" name="req_id"                    value="">                   <!-- [옵션]요청자 ID -->
        <input type="hidden" name="mgr_telno"                 value="">                   <!-- [옵션]환불계좌 연락처 -->
        <input type="hidden" name="EP_mall_id"                value="<?=$orderInfo['mallPgID']?>"/>
        <input type="hidden" name="mgr_txtype"                value="<?=$_REQUEST['mgrType']?>"/>
        <input type="hidden" name="mgr_subtype"               value="RF01"/>
        <input type="hidden" name="org_cno"                   value="<?=$orderInfo['cno']?>"/>
        <input type="hidden" name="mgr_bank_cd"               value="<?=$_REQUEST['mgr_bank_cd']?>"/>
        <input type="hidden" name="mgr_account"               value="<?=$_REQUEST['mgr_account']?>"/>
        <input type="hidden" name="mgr_depositor"             value="<?=$_REQUEST['mgr_depositor']?>"/>
        <input type="hidden" name="mgr_amt"                   value="<?=$orderInfo['totalPrice']?>"/>
        <input type="hidden" name="cancelTypeCode"            value="<?=$_REQUEST['cancelTypeCode']?>">         <!-- 취소종료-->
        <input type="hidden" name="cancelDetailTypeCode"      value="<?=$_REQUEST['ORDER_DELIVERY_NM']?>">         <!-- 취소사유-->
    </form>
</body>
</html>