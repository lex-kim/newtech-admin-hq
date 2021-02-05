<?php
    header("Content-Type: application/vnd.ms-excel");
    date_default_timezone_set('Asia/Seoul');

    header("Content-Disposition: attachment; filename=".date("YmdH", time())."_주문관리.xls");
    header("Content-Description:Newtech System");

    require $_SERVER['DOCUMENT_ROOT']."/common/common.php";	
    require "class/OrderList.php";

    $_REQUEST['fullListYN'] = "Y";
    $result = $cOrderList->getList($_REQUEST);

    $excelHtml = "
        <table border='1'>
            <thead>
            <tr class='active'>
                <th>순번</th>
                <th>작성일</th>
                <th>주문번호</th>
                <th>주문상태</th>
                <th>거래여부</th>
                <th>구분</th>
                <th>지역</th>
                <th>공업사</th>
                <th>품목</th>
                <th>결제금액</th>
                <th>배송비</th>
                <th>포인트</th>
                <th>실결제금액</th>
                <th>입금일</th>
                <th>입금액</th>
                <th>미입금액</th>
                <th>DC율</th>
                <th>배송방법</th>
                <th>입금여부</th>
                <th>결제방법</th>
                <th>취소종류</th>
                <th>취소사유</th>
                <th>계산서발행일</th>
                <th>거래명세서출력</th>
                <th>메모</th>
            </tr>
        </thead>
    ";
    
    function getPoint($usePoint){
        if($usePoint == 0){
          return $usePoint;
        }else{
          return "-".setPhoneFormat($usePoint); 
        }
      }
      
      function getOrderPrint($dateTime, $userID){
        if(empty($dateTime)){
          return "-";
        }else{
          return $dateTime."<br>(".$userID.")";
        }
      }

    if($result['status'] == OK && $result['data'] != HAVE_NO_DATA){ 
        foreach($result['data'] as $key => $item) { 
            $depositPrice = empty($item['depositPrice']) ? 0 : $item['depositPrice'];
            $excelHtml .= "<tr>";
            $excelHtml .= "<td class='text-center'>".$item['issueCount']."</td>";
            $excelHtml .= "<td class='text-center'>".$item['writeDatetime']."</td>";
            $excelHtml .= "<td class='text-center'>".$item['orderID']."</td>";
            $excelHtml .= "<td class='text-center'>".$item['orderStatus']."</td>";
            $excelHtml .= "<td class='text-center'>".$item['contractStatus']."</td>";
            $excelHtml .= "<td class='text-center'>".$item['divisionName']."</td>";
            $excelHtml .= "<td class='text-left'>".$item['regionName']."</td>";
            $excelHtml .= "<td class='text-left'>".$item['garageName']."</td>";
            $excelHtml .= "<td class='text-left'>".(empty($item['orderDetails'])?"-":$item['orderDetails'])."</td>";
            $excelHtml .= "<td class='text-right'>".number_format($item['orderPrice'])."</td>";
            $excelHtml .= "<td class='text-right'>".number_format($item['deliveryPrice'])."</td>";
            $excelHtml .= "<td class='text-right'>".getPoint($item['pointRedeem'])."</td>";
            $excelHtml .= "<td class='text-right'>".number_format($item['totalPrice'])."</td>";
            $excelHtml .= "<td class='text-center'>".(empty($item['depositDate'])?"-":$item['depositDate'])."</td>";
            $excelHtml .= "<td class='text-right'>".number_format($depositPrice)."</td>";
            $excelHtml .= "<td class='text-right'>".number_format($item['totalPrice'] - $depositPrice)."</td>";
            $excelHtml .= "<td class='text-right'>".(@is_nan(($depositPrice/$item['totalPrice'])*100) ? 0 : ($depositPrice/$item['totalPrice'])*100) ."%</td>";
            $excelHtml .= "<td class='text-center'>".$item['deliveryMethod'];
            $excelHtml .= "</td>";
            $excelHtml .= "<td class='text-center'>".($item['depositYN'] == "Y" ?"입금":"미입금" )."</td>";
            $excelHtml .= "<td class='text-center'>".$item['payment']."</td>";
            $excelHtml .= "<td class='text-center'>".(empty($item['cancelType'])?"-":$item['cancelType'])."</td>";
            $excelHtml .= "<td class='text-center'>".(empty($item['cancelDetailType'])?"-":$item['cancelDetailType'])."</td>";
            $excelHtml .= "<td class='text-center'>".(($item['paymentCode'] == EC_PAYMENT_TYPE_DEPOSIT && $item['billTypeCode'] != EC_BILL_TYPE_NO) ? (empty($item['billRequestDatetime'])?"-":$item['billRequestDatetime']) : "-")."</td>";
            $excelHtml .= "<td class='text-center'>".getOrderPrint($item['specificationPrintDatetime'], $item['specificationPrintUserName'])."</td>";
            $excelHtml .= "<td class='text-left'>".(empty($item['memo']) ? "-" :$item['memo'])."</td>";
            $excelHtml .= "</tr>";
        }   
    }
    echo $excelHtml;
?>

