<?php
    header("Content-Type: application/vnd.ms-excel");
    date_default_timezone_set('Asia/Seoul');

    header("Content-Disposition: attachment; filename=".date("YmdH", time())."_입고관리.xls");
    header("Content-Description:Newtech System");

    require $_SERVER['DOCUMENT_ROOT']."/common/common.php";	
    require "class/Stock.php";

    $_REQUEST['fullListYN'] = "Y";
    $result = $cStock->getList($_REQUEST);

    $excelHtml = "
        <table border='1'>
            <thead>
            <tr class='active'>
                <th class='text-center'>순번</th>
                <th class='text-center'>입고일</th>
                <th class='text-center'>카테고리</th>
                <th class='text-center'>매입처</th>
                <th class='text-center'>상품코드</th>
                <th class='text-center'>상품명</th>
                <th class='text-center'>규격/용량</th>
                <th class='text-center'>포장단위</th>
                <th class='text-center'>단가</th>
                <th class='text-center'>수량</th>
                <th class='text-center'>금액</th>
                <th class='text-center'>메모</th>
                <th class='text-center'>등록일시</th>
                <th class='text-center'>등록자</th>
                <th class='text-center'>취소일시</th>
                <th class='text-center'>취소자</th>
            </tr>
        </thead>
    ";
    
    if($result['status'] == OK && $result['data'] != HAVE_NO_DATA){ 
        foreach($result['data'] as $key => $item) { 
            $excelHtml .= "<tr>
                <td class='text-center'>".$item['issueCount']."</td>
                <td class='text-center'>".$item['issueDate']."</td>
                <td class='text-center'>".$item['categoryName']."</td>
                <td class='text-center'>".$item['partnerName']."</td>
                <td class='text-center' style='mso-number-format:\@;'>".$item['goodsCode']."</td>
                <td class='text-center'>".$item['goodsName']."</td>
                <td class='text-center'>".$item['goodsSpec']."</td>
                <td class='text-center'>".$item['unitPerBox']."</td>
                <td class='text-center'>".number_format($item['unitPrice'])."</td>
                <td class='text-center'>".number_format($item['quantity'])."</td>
                <td class='text-center'>".number_format($item['sumPrice'])."</td>
                <td class='text-center'>".$item['memo']."</td>
                <td class='text-center'>".$item['writeDatetime']."</td>
                <td class='text-center'>".$item['writeUser']."</td>
                <td class='text-center'>".(empty($item['cancelDatetime']) ? "-" : $item['cancelDatetime'])."</td>
                <td class='text-center'>".(empty($item['cancelUser']) ? "-" : $item['cancelUser'])."</td>
            ";
        }   
    }
    echo $excelHtml;
?>