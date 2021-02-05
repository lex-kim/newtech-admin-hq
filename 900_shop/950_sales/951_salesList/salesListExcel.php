<?php
    header("Content-Type: application/vnd.ms-excel");
    date_default_timezone_set('Asia/Seoul');

    header("Content-Disposition: attachment; filename=".date("YmdH", time())."_일반판매내역관리.xls");
    header("Content-Description:Newtech System");

    require $_SERVER['DOCUMENT_ROOT']."/common/common.php";	
    require "class/SaleList.php";

    $_REQUEST['fullListYN'] = "Y";
    $result = $cSaleList->getList($_REQUEST);

    $excelHtml = "
        <table border='1'>
                <tr class='active'>
                <th class='text-center'>순번</th>
                <th class='text-center'>판매일</th>
                <th class='text-center'>일반판매번호</th>
                <th class='text-center'>구분</th>
                <th class='text-center'>지역</th>
                <th class='text-center'>업체명</th>
                <th class='text-center'>청구식</th>
                <th class='text-center'>거래여부</th>
                <th class='text-center'>담당자</th>
                <th class='text-center'>사무실전화</th>
                <th class='text-center'>담당자휴대폰</th>
                <th class='text-center'>상품명</th>
                <th class='text-center'>공급가액</th>
                <th class='text-center'>세액</th>
                <th class='text-center'>합계금액</th>
                <th class='text-center'>실판매가</th>
                <th class='text-center'>직원명</th>
                <th class='text-center'>입금일</th>
                <th class='text-center'>입금액</th>
                <th class='text-center'>미입금액</th>
                <th class='text-center'>DC율</th>
                <th class='text-center'>입금여부</th>
                <th class='text-center'>결제방법</th>
                <th class='text-center'>메모</th>
                <th class='text-center'>처리일시</th>
                <th class='text-center'>처리자</th>
                <th class='text-center'>지출증빙발행일</th>
                <th class='text-center'>지출증빙발행여부</th>
                <th class='text-center'>지출증빙발행종류</th>
                <th class='text-center'>지출증빙발행취소일시</th>
                <th class='text-center'>지출증빙발행취소자</th>
                </tr>
    ";
    
    if($result['status'] == OK && $result['data'] != HAVE_NO_DATA){ 
        foreach($result['data'] as $key => $item) { 
            $excelHtml .= "<tr>
               <td class='text-center'>".$item['issueCount']."</td>
               <td class='text-center'>".$item['issueDate']."</td>
               <td class='text-center'>".$item['salesID']."</td>
               <td class='text-center'>".$item['divisionName']."</td>
               <td class='text-left'>".$item['regionName']."</td>
               <td class='text-left'>".$item['garageName']."</td>
               <td class='text-center'>".(empty($item['expName']) ? "-" :$item['expName'])."</td>
               <td class='text-center'>".$item['contractStatus']."</td>
               <td class='text-center'>".$item['chargeName']."</td>
               <td class='text-center'>".setPhoneFormat($item['officeTel'])."</td>
               <td class='text-center'>".setPhoneFormat($item['chargePhoneNum'])."</td>
               <td class='text-center'>".$item['salesDetails']."</td>
               <td class='text-center'>".number_format(empty($item['totalPrice']) ? 0 : $item['totalPrice'])."</td>
               <td class='text-center'>".number_format(empty($item['totalVAT']) ? 0 : $item['totalVAT'])."</td>
               <td class='text-center'>".number_format(empty($item['totalSum']) ? 0 : $item['totalSum'])."</td>
               <td class='text-center'>".number_format(empty($item['salePrice']) ? 0 : $item['salePrice'])."</td>
               <td class='text-center'>".(empty($item['employeeName']) ? "-" :$item['employeeName'])."</td>
               <td class='text-center'>".$item['depositDate']."</td>
               <td class='text-center'>".number_format(empty($item['depositPrice']) ? 0 : $item['depositPrice'])."</td>
               <td class='text-center'>".number_format(empty($item['unpaidPrice']) ? 0 : $item['unpaidPrice'])."</td>
               <td class='text-center'>".$item['dcRatio']."</td>
               <td class='text-center'>".$item['depositYN']."</td>
               <td class='text-center'>".$item['paymentName']."</td>
               <td class='text-center'>".$item['memo']."</td>
               <td class='text-center'>".$item['processDatetime']."</td>
               <td class='text-center'>".$item['processUserName']."</td>
               <td class='text-center'>".(empty($item['billPublishDate']) ? "-" : $item['billPublishDate'])."</td>
               <td class='text-center'>".(empty($item['billPublishDate']) ? "N" : "Y")."</td>
               <td class='text-center'>".(empty($item['billType']) ? "-" :$item['billType'])."</td>
               <td class='text-center'>".(empty($item['billCancelDate']) ? "-" : $item['billCancelDate'])."</td>
               <td class='text-center'>".(empty($item['billCancelUserID']) ? "-" : $item['billCancelUserID'])."</td>
               </tr>
            ";
        }   
    }
    echo $excelHtml;
?>