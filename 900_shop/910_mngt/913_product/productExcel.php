<?php
    header("Content-Type: application/vnd.ms-excel");
    date_default_timezone_set('Asia/Seoul');

    header("Content-Disposition: attachment; filename=".date("YmdH", time())."_상품관리.xls");
    header("Content-Description:Newtech System");

    require $_SERVER['DOCUMENT_ROOT']."/common/common.php";	
    require "class/Product.php";

    $_REQUEST['fullListYN'] = "Y";
    $result = $cProduct->getList($_REQUEST);

    $excelHtml = "
        <table border='1'>
            <thead>
            <tr class='active'>
            <th class='text-center'>순번</th>
            <th class='text-center'>관리구분</th>
            <th class='text-center'>카테고리</th>
            <th class='text-center'>매입처구분</th>
            <th class='text-center'>매입처</th>
            <th class='text-center'>상품코드</th>
            <th class='text-center'>상품명</th>
            <th class='text-center'>규격/용량</th>
            <th class='text-center'>포장단위</th>
            <th class='text-center'>최소구매수량</th>
            <th class='text-center'>매입가</th>
            <th class='text-center'>도매가</th>
            <th class='text-center'>소비자가</th>
            <th class='text-center'>판매가</th>
            <th class='text-center'>적립금</th>
            <th class='text-center'>배송정책</th>
            <th class='text-center'>제조사</th>
            <th class='text-center'>원산지</th>
            <th class='text-center'>브랜드</th>
            <th class='text-center'>품절여부</th>
            <th class='text-center'>재고수량</th>
            <th class='text-center'>등록자</th>
            <th class='text-center'>등록일시</th>
            <th class='text-center'>갱신자</th>
            <th class='text-center'>갱신일</th>
            </tr>
        </thead>
    ";
    
    if($result['status'] == OK && $result['data'] != HAVE_NO_DATA){ 
        foreach($result['data'] as $key => $item) { 
            $excelHtml .= "<tr>
                <td class='text-center'>".$item['issueCount']."</td>
                <td class='text-center'>".$item['mgmtType']."</td>
                <td class='text-center'>".$item['categoryName']."</td>
                <td class='text-center'>".$item['buyerType']."</td>
                <td class='text-center'>".$item['partnerName']."</td>
                <td class='text-center'>".$item['goodsCode']."</td>
                <td class='text-center'>".$item['goodsName']."</td>
                <td class='text-center'>".$item['goodsSpec']."</td>
                <td class='text-center'>".$item['unitPerBox']."</td>
                <td class='text-center'>".$item['minBuyCnt']."</td>
                <td class='text-center'>".number_format($item['buyPrice'])."</td>
                <td class='text-center'>".number_format($item['wholeSalePrice'])."</td>
                <td class='text-center'>".number_format($item['retailPrice'])."</td>
                <td class='text-center'>".number_format($item['salePrice'])."</td>
                <td class='text-center'>".number_format($item['earnPoint'])."</td>
                <td class='text-center'>".$item['deliveryPolicyName']."</td>
                <td class='text-center'>".$item['manufacturerName']."</td>
                <td class='text-center'>".$item['originName']."</td>
                <td class='text-center'>".$item['brandName']."</td>
                <td class='text-center'>".$item['soldOutYN']."</td>
                <td class='text-center'>".$item['stockRemains']."</td>
                <td class='text-left'>".$item['writeUser']."</td>
                <td class='text-center'>".$item['writeDatetime']."</td>
                <td class='text-center'>".$item['lastUser']."</td>
                <td class='text-center'>".$item['lastDatetime']."</td>";
            $excelHtml .= "</tr>";
        }   
    }
    echo $excelHtml;
?>