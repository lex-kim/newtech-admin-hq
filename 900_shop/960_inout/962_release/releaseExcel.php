<?php
    header("Content-Type: application/vnd.ms-excel");
    date_default_timezone_set('Asia/Seoul');

    header("Content-Disposition: attachment; filename=".date("YmdH", time())."_출고관리.xls");
    header("Content-Description:Newtech System");

    require $_SERVER['DOCUMENT_ROOT']."/common/common.php";	
    require "class/Release.php";

    $_REQUEST['fullListYN'] = "Y";
    $result = $cRelease->getList($_REQUEST);

    $excelHtml = "
        <table border='1'>
            <thead>
            <tr class='active'>
                <th class='text-center'>순번</th>
                <th class='text-center'>출고일</th>
                <th class='text-center'>카테고리</th>
                <th class='text-center'>매입처</th>
                <th class='text-center'>상품</th>
                <th class='text-center'>규격/용량</th>
                <th class='text-center'>단가</th>
                <th class='text-center'>수량</th>
                <th class='text-center'>변동일</th>
                <th class='text-center'>메모</th>
                <th class='text-center'>직원명</th>
                <th class='text-center'>작성일시</th>
                <th class='text-center'>작성자</th>
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
                <td class='text-center'>".$item['goodsName']."</td>
                <td class='text-center'>".$item['goodsSpec']."</td>
                <td class='text-center'>".number_format($item['unitPrice'])."</td>
                <td class='text-center'>".number_format($item['quantity'])."</td>
                <td class='text-center'>".(empty($item['lastDatetime']) ? "-" : $item['lastDatetime'])."</td>
                <td class='text-center'>".$item['memo']."</td>
                <td class='text-center'>".(empty($item['issueUserName']) ? "-" : $item['issueUserName'])."</td>
                <td class='text-center'>".$item['writeUser']."</td>
                <td class='text-center'>".$item['writeDatetime']."</td>
                <td class='text-center'>".(empty($item['cancelDatetime']) ? "-" : $item['cancelDatetime'])."</td>
                <td class='text-center'>".(empty($item['cancelUser']) ? "-" : $item['cancelUser'])."</td>
            ";
        }   
    }
    echo $excelHtml;
?>