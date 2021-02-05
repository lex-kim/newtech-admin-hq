<?php

    header("Content-Type: application/vnd.ms-excel");
    date_default_timezone_set('Asia/Seoul');

    header("Content-Disposition: attachment; filename=".date("YmdH", time())."_미수금액현황.xls");
    header("Content-Description: NewtechSystem");

    require $_SERVER['DOCUMENT_ROOT']."/common/common.php";	
    require "class/NotReceivedStatus.php";

    $_REQUEST['fullListYN'] = "Y";

    $result =  $cNotReceivedStatus->getList($_REQUEST);

    $excelHtml = "
        <table border='1'>
            <thead>
              <tr class='active'>
                <th class='text-center' rowspan='2'>순번</th>
                <th class='text-center' rowspan='2'>구분명</th>
                <th class='text-center' rowspan='2'>지역명</th>
                <th class='text-center' rowspan='2'>업체ID</th>
                <th class='text-center' rowspan='2'>업체명</th>
                <th class='text-center' rowspan='2'>청구식명</th>
                <th class='text-center' rowspan='2'>거래상태명</th>
                <th class='text-center' rowspan='2'>담당자성명</th>
                <th class='text-center' rowspan='2'>담당자연락처</th>
                <th class='text-center' rowspan='2'>사무실전화</th>
                <th class='text-center' rowspan='2'>팩스번호</th>
                <th class='text-center' rowspan='2'>총주문횟수</th>
                <th class='text-center' colspan='3'>매출</th>
                <th class='text-center' colspan='3'>결제</th>
                <th class='text-center' colspan='3'>결제잔액</th>
              </tr>
              <tr class='active'>
                <th class='text-center'>공급가액</th>
                <th class='text-center'>세액</th>
                <th class='text-center'>합계금액</th>
                <th class='text-center'>공급가액</th>
                <th class='text-center'>세액</th>
                <th class='text-center'>합계금액</th>
                <th class='text-center'>공급가액</th>
                <th class='text-center'>세액</th>
                <th class='text-center'>합계금액</th>
              </tr>
               
            </thead>
    ";

    if($result['status'] == OK && $result['data'] != HAVE_NO_DATA){ 
        foreach($result['data'] as $key => $item) { 
            $excelHtml .= "<tr>";
            $excelHtml .= "<td class='text-center'>".$item['issueCount']."</td>";
            $excelHtml .= "<td class='text-center'>".(empty($item['divisionName']) ? "-":$item['divisionName'])."</td>";
            $excelHtml .= "<td class='text-center'>".(empty($item['regionName']) ? "-":$item['regionName'])."</td>";
            $excelHtml .= "<td class='text-center'>".$item['garageID']."</td>";
            $excelHtml .= "<td class='text-center'>".$item['garageName']."</td>";
            $excelHtml .= "<td class='text-center'>".(empty($item['billFormulaName']) ? "-":$item['billFormulaName'])."</td>";
            $excelHtml .= "<td class='text-center'>".$item['contractStatusName']."</td>";
            $excelHtml .= "<td class='text-center'>".$item['chargeName']."</td>";
            $excelHtml .= "<td class='text-center'>".setPhoneFormat($item['chargePhoneNum'])."</td>";
            $excelHtml .= "<td class='text-center'>".setPhoneFormat($item['phoneNum'])."</td>";
            $excelHtml .= "<td class='text-center'>".setPhoneFormat($item['faxNum'])."</td>";
            $excelHtml .= "<td class='text-right'>".number_format($item['totalSellCount'])."</td>";
            $excelHtml .= "<td class='text-right'>".number_format($item['totalSellAmountWithoutVAT'])."</td>";
            $excelHtml .= "<td class='text-right'>".number_format($item['totalSellAmountVAT'])."</td>";
            $excelHtml .= "<td class='text-right'>".number_format($item['totalSellAmount'])."</td>";
            $excelHtml .= "<td class='text-right'>".number_format($item['totalPayAmountWithoutVAT'])."</td>";
            $excelHtml .= "<td class='text-right'>".number_format($item['totalPayAmountVAT'])."</td>";
            $excelHtml .= "<td class='text-right'>".number_format($item['totalPayAmount'])."</td>";
            $excelHtml .= "<td class='text-right'>".number_format($item['totalReceivableAmountNowWithoutVAT'])."</td>";
            $excelHtml .= "<td class='text-right'>".number_format($item['totalReceivableAmountNowVAT'])."</td>";
            $excelHtml .= "<td class='text-right'>".number_format($item['totalReceivableAmountNow'])."</td>";
            $excelHtml .= "</tr>";
        }   
    }
    echo $excelHtml ;

?>