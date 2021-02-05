<?php
    require $_SERVER['DOCUMENT_ROOT']."/common/common.php";	
    require "class/NotReceivedDetail.php";

    $_REQUEST['fullListYN'] = "Y";

    $result =  $cNotReceivedDetail->getList($_REQUEST);

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
                <th class='text-center' rowspan='2'>가맹본부ID</th>
                <th class='text-center' rowspan='2'>주문ID</th>
                <th class='text-center' rowspan='2'>견적ID</th>
                <th class='text-center' rowspan='2'>매출상품(대표)</th>
                <th class='text-center' colspan='3'>매출</th>
                <th class='text-center' rowspan='2'>결제일</th>
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
        foreach($result['data']['data'] as $key => $item) { 
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
            $excelHtml .= "<td class='text-center'>".$item['hqID']."</td>";
            $excelHtml .= "<td class='text-center'>".$item['orderID']."</td>";
            $excelHtml .= "<td class='text-center'>".$item['quotationID']."</td>";
            $excelHtml .= "<td class='text-center'>".$item['orderDetails']."</td>";
            $excelHtml .= "<td class='text-right'>".number_format($item['sellAmountWithoutVAT'])."</td>";
            $excelHtml .= "<td class='text-right'>".number_format($item['sellAmountVAT'])."</td>";
            $excelHtml .= "<td class='text-right'>".number_format($item['sellAmount'])."</td>";
            $excelHtml .= "<td class='text-center'>".(empty($item['payDate']) ? "-":$item['payDate'])."</td>";
            $excelHtml .= "<td class='text-right'>".number_format($item['payAmountWithoutVAT'])."</td>";
            $excelHtml .= "<td class='text-right'>".number_format($item['payAmountVAT'])."</td>";
            $excelHtml .= "<td class='text-right'>".number_format($item['payAmount'])."</td>";
            $excelHtml .= "<td class='text-right'>".number_format($item['receivableAmountWithoutVAT'])."</td>";
            $excelHtml .= "<td class='text-right'>".number_format($item['receivableAmountVAT'])."</td>";
            $excelHtml .= "<td class='text-right'>".number_format($item['receivableAmount'])."</td>";
            $excelHtml .= "</tr>";
        }   
        header("Content-Type: application/vnd.ms-excel");
        date_default_timezone_set('Asia/Seoul');

        header("Content-Disposition: attachment; filename=".date("YmdH", time())."_미수금액상세현황.xls");
        header("Content-Description: NewtechSystem");

        echo $excelHtml ;
    }else{
      errorAlert("데이터가 존재하지 않습니다.");
    }
   

?>