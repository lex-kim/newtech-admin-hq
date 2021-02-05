<?php

    require $_SERVER['DOCUMENT_ROOT']."/common/common.php";	
    require "class/OutstandingPaymentStatus.php";
    
    header("Content-Type: application/vnd.ms-excel");
    date_default_timezone_set('Asia/Seoul');

    header("Content-Disposition: attachment; filename=".date("YmdH", time())."_미결제금액현황.xls");
    header("Content-Description:Newtech System");

    $_REQUEST['fullListYN'] = "Y";

    $result = $cOutstandingPaymentStatus->getList($_REQUEST);

    $excelHtml = "
        <table border='1'>
            <thead>
                <tr class='active'>
                    <th class='text-center' rowspan='2'>순번</th>
                    <th class='text-center' rowspan='2'>업체구분명</th>
                    <th class='text-center' rowspan='2'>거래구분명</th>
                    <th class='text-center' rowspan='2'>매입처구분명</th>
                    <th class='text-center' rowspan='2'>매입처ID</th>
                    <th class='text-center' rowspan='2'>매입처 상호명</th>
                    <th class='text-center' rowspan='2'>매입처 사무실전화</th>
                    <th class='text-center' rowspan='2'>매입처 사무실팩스</th>
                    <th class='text-center' rowspan='2'>담당자명</th>
                    <th class='text-center' rowspan='2'>담당자 휴대폰번호</th>
                    <th class='text-center' rowspan='2'>담당자 직통번화</th>
                    <th class='text-center' rowspan='2'>총 주문횟수</th>
                    <th class='text-center' colspan='3'>총매입</th>
                    <th class='text-center' colspan='3'>총결제</th>
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
            $excelHtml .= "<td class='text-center'>".$item['businessType']."</td>";
            $excelHtml .= "<td class='text-center'>".$item['partnerType']."</td>";
            $excelHtml .= "<td class='text-center'>".$item['buyType']."</td>";
            $excelHtml .= "<td class='text-center'>".$item['partnerID']."</td>";
            $excelHtml .= "<td class='text-center'>".$item['partnerName']."</td>";
            $excelHtml .= "<td class='text-center'>".setPhoneFormat($item['phoneNum'])."</td>";
            $excelHtml .= "<td class='text-center'>".setPhoneFormat($item['faxNum'])."</td>";
            $excelHtml .= "<td class='text-center'>".$item['chargeName']."</td>";
            $excelHtml .= "<td class='text-center'>".setPhoneFormat($item['chargePhoneNum'])."</td>";
            $excelHtml .= "<td class='text-center'>".setPhoneFormat($item['chargeTelNum'])."</td>";
            $excelHtml .= "<td class='text-right'>".number_format($item['totalBuyCount'])."</td>";
            $excelHtml .= "<td class='text-right'>".number_format($item['totalBuyAmountWithoutVAT'])."</td>";
            $excelHtml .= "<td class='text-right'>".number_format($item['totalBuyAmountVAT'])."</td>";
            $excelHtml .= "<td class='text-right'>".number_format($item['totalBuyAmount'])."</td>";
            $excelHtml .= "<td class='text-right'>".number_format($item['totalPayAmountWithoutVAT'])."</td>";
            $excelHtml .= "<td class='text-right'>".number_format($item['totalPayAmountVAT'])."</td>";
            $excelHtml .= "<td class='text-right'>".number_format($item['totalPayAmount'])."</td>";
            $excelHtml .= "<td class='text-right'>".number_format($item['outstandAmountNowWithoutVAT'])."</td>";
            $excelHtml .= "<td class='text-right'>".number_format($item['outstandAmountNowVAT'])."</td>";
            $excelHtml .= "<td class='text-right'>".number_format($item['outstandAmountNow'])."</td>";
            $excelHtml .= "</tr>";
        }   
    }
    echo $excelHtml;
?>