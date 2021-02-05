<?php

    require $_SERVER['DOCUMENT_ROOT']."/common/common.php";	
    require "class/InOutStatus.php";

    header("Content-Type: application/vnd.ms-excel");
    date_default_timezone_set('Asia/Seoul');

    header("Content-Disposition: attachment; filename=".date("YmdH", time())."_입출고관리현황.xls");
    header("Content-Description:Newtech System");

    $_REQUEST['fullListYN'] = "Y";
    $result = $cInOutStatus->getList($_REQUEST);


    $excelHtml = "
        <table border='1'>
            <thead>
                <tr class='active'>
                <th class='text-center' rowspan='3'>순번</th>
                <th class='text-center' rowspan='3'>관리구분</th>
                <th class='text-center' rowspan='3'>카테고리</th>
                <th class='text-center' rowspan='3'>상품명</th>
                <th class='text-center' rowspan='3'>매입단가</th>
                <th class='text-center' colspan='6'>재고</th>
                <th class='text-center' colspan='6'>판매</th>
                <th class='text-center' colspan='2'>분실</th>
                <th class='text-center' colspan='2'>폐기</th>
                <th class='text-center' rowspan='3'>3개월 평균<br>출고수량
                </th>
                <th class='text-center' rowspan='3'>발주수요량
                </th>
                <th class='text-center' rowspan='3'>재고율
                </th>
            </tr>
            <tr class='active'>
                <th class='text-center' colspan='2'>소계</th>
                <th class='text-center' colspan='2'>창고</th>
                <th class='text-center' colspan='2'>차량</th>
                <th class='text-center' colspan='2'>소계</th>
                <th class='text-center' colspan='2'>일반판매</th>
                <th class='text-center' colspan='2'>쇼핑몰</th>
                <th class='text-center' rowspan='2'>수량</th>
                <th class='text-center' rowspan='2'>금액</th>
                <th class='text-center' rowspan='2'>수량</th>
                <th class='text-center' rowspan='2'>금액</th>
            </tr>
            <tr class='active'>
                <th class='text-center'>수량</th>
                <th class='text-center'>금액</th>
                <th class='text-center'>수량</th>
                <th class='text-center'>금액</th>
                <th class='text-center'>수량</th>
                <th class='text-center'>금액</th>
                <th class='text-center'>수량</th>
                <th class='text-center'>금액</th>
                <th class='text-center'>수량</th>
                <th class='text-center'>금액</th>
                <th class='text-center'>수량</th>
                <th class='text-center'>금액</th>
            </tr>
            </thead>
    ";
    
    if($result['status'] == OK && $result['data'] != HAVE_NO_DATA){ 
        foreach($result['data'] as $key => $item) { 
            $stockTotalCnt = $item['itemRemains'] + $item['carRemains'];
            $stockTotalPrice = $item['itemRemains'] * $item['buyPrice'] + $item['carRemains'] * $item['buyPrice'];
            $salesTotalCnt = $item['totalSales'] + $item['totalOrder'];
            $salesTotalPrice = $item['totalSales'] * $item['buyPrice'] + $item['totalOrder'] * $item['buyPrice'];

            $excelHtml .= "<tr>
                <td class='text-center'>".$item['issueCount']."</td>
                <td class='text-center'>".$item['mgmtType']."</td>
                <td class='text-center'>".(empty($item['categoryName']) ? "-" : $item['categoryName'])."</td>
                <td class='text-center'>".$item['goodsName']."</td>
                <td class='text-center'>".number_format($item['buyPrice'])."</td>
                <td class='text-center'>".number_format($stockTotalCnt)."</td>
                <td class='text-center'>".number_format($stockTotalPrice)."</td>
                <td class='text-center'>".number_format($item['itemRemains'])."</td>
                <td class='text-center'>".number_format($item['itemRemains']*$item['buyPrice'])."</td>
                <td class='text-center'>".number_format($item['carRemains'])."</td>
                <td class='text-center'>".number_format($item['carRemains']*$item['buyPrice'])."</td>
                <td class='text-center'>".number_format($salesTotalCnt)."</td>
                <td class='text-center'>".number_format($salesTotalPrice)."</td>
                <td class='text-center'>".number_format($item['totalSales'])."</td>
                <td class='text-center'>".number_format($item['totalSales']*$item['buyPrice'])."</td>
                <td class='text-center'>".number_format($item['totalOrder'])."</td>
                <td class='text-center'>".number_format($item['totalOrder']*$item['buyPrice'])."</td>
                <td class='text-center'>".number_format($item['totalStockLost'])."</td>
                <td class='text-center'>".number_format($item['totalStockLost']*$item['buyPrice'])."</td>
                <td class='text-center'>".number_format($item['totalStockDisposal'])."</td>
                <td class='text-center'>".number_format($item['totalStockDisposal']*$item['buyPrice'])."</td>
                <td class='text-center'>".number_format($item['StockOutAverage3M'])."</td>
                <td class='text-center'>".number_format($item['stockOrderRequire'])."</td>
                <td class='text-center'>".number_format($item['remainRatio'])."</td>

               
            </tr>
            ";
        }   
    }
    echo $excelHtml;
?>