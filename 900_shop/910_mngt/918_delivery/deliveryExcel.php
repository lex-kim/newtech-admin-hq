<?php
    header("Content-Type: application/vnd.ms-excel");
    date_default_timezone_set('Asia/Seoul');

    header("Content-Disposition: attachment; filename=".date("YmdH", time())."_배송정책관리.xls");
    header("Content-Description:Newtech System");

    require $_SERVER['DOCUMENT_ROOT']."/common/common.php";	
    require "class/Delivery.php";

    $_REQUEST['fullListYN'] = "Y";
    $result = $cDelivery->getList($_REQUEST);

    $excelHtml = "
        <table border='1'>
            <thead>
            <tr class='active'>
                <th class='text-center'>순번</th>
                <th class='text-center'>배송정책명</th>
                <th class='text-center'>결제구분</th>
                <th class='text-center'>부과기준</th>
                <th class='text-center'>부과금액(VAT포함)</th>
                <th class='text-center'>등록자</th>
                <th class='text-center'>등록일</th>
                <th class='text-center'>갱신자</th>
                <th class='text-center'>갱신일</th>
            </tr>
        </thead>
    ";
    if($result['status'] == OK && $result['data'] != HAVE_NO_DATA){ 
        foreach($result['data'] as $key => $item) { 
            $excelHtml .= "<tr>
                <td class='text-center'>".$item['issueCount']."</td>
                <td class='text-center'>".$item['deliveryPolicyName']."</td>
                <td class='text-center'>".$item['payType']."</td>
                <td class='text-center'>".$item['priceType']."</td>
                <td class='text-center'>".number_format($item['deliveryPrice'])."</td>
                <td class='text-left'>".$item['writeUser']."</td>
                <td class='text-center'>".$item['writeDatetime']."</td>
                <td class='text-center'>".$item['lastUser']."</td>
                <td class='text-center'>".$item['lastDatetime']."</td>";
            $excelHtml .= "</tr>";
        }   
    }
    echo $excelHtml;
?>