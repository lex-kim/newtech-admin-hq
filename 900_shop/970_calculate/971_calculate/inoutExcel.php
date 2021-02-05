<?php
    header("Content-Type: application/vnd.ms-excel");
    date_default_timezone_set('Asia/Seoul');

    header("Content-Disposition: attachment; filename=".date("YmdH", time())."_정산관리.xls");
    header("Content-Description:spns");

    require $_SERVER['DOCUMENT_ROOT']."/common/common.php";	
    require "class/InOutOrder.php";

    $_REQUEST['fullListYN'] = "Y";
    $result = $cInOutOrder->getList($_REQUEST);

    $excelHtml = "
        <table border='1'>
            <tr class='active'>
                <th class='text-center'>정산년월</th>
                <th class='text-center'>구분</th>
                <th class='text-center'>지역</th>
                <th class='text-center'>업체명</th>
                <th class='text-center'>거래여부</th>
                <th class='text-center'>대표자</th>
                <th class='text-center'>연락처</th>
                <th class='text-center'>전월미수누계</th>
                <th class='text-center'>주문횟수</th>
                <th class='text-center'>공급금액</th>
                <th class='text-center'>세액</th>
                <th class='text-center'>합계액</th>
                <th class='text-center'>확정여부</th>
                <th class='text-center'>확정일시</th>
                <th class='text-center'>확정자</th>
                <th class='text-center'>입금여부</th>
                <th class='text-center'>입금일</th>
                <th class='text-center'>입금액</th>
                <th class='text-center'>미입금액</th>
                <th class='text-center'>입금처리자</th>
                <th class='text-center'>입금처리일시</th>
            </tr>
    ";
    
    if($result['status'] == OK && $result['data'] != HAVE_NO_DATA){ 
        foreach($result['data'] as $key => $item) { 
            $excelHtml .= "<tr>
               <td class='text-center'>".$item['issueCount']."</td>
               <td class='text-center'>구분</td>
               <td class='text-center'>지역</td>
               <td class='text-center'>".$item['shipName']."</td>
               <td class='text-center'>거래여부</td>
               <td class='text-center'>".$item['shipChargeName']."</td>
               <td class='text-center'>".setPhoneFormat($item['shipChargePhoneNum'])."</td>
               <td class='text-right'>전월미수누계</td>
               <td class='text-right'>주문횟수</td>
               <td class='text-right'>공급금액</td>
               <td class='text-right'>세액</td>
               <td class='text-center'>".number_format(empty($item['totalPrice']) ? 0 : $item['totalPrice'])."</td>
               <td class='text-center'>확정여부</td>
               <td class='text-center'>확정일시</td>
               <td class='text-center'>확정자</td>
               <td class='text-center'>".$item['depositYN']."</td>
               <td class='text-center'>".(empty($item['depositDate']) ? "-" :$item['depositDate'])."</td>
               <td class='text-center'>".(empty($item['depositPrice']) ? "-" :number_format($item['depositPrice']))."</td>
               <td class='text-center'>미입금액</td>
               <td class='text-center'>입금처리자</td>
               <td class='text-center'>입금처리일시</td>
               </tr>
            ";
        }   
    }
    echo $excelHtml;
?>