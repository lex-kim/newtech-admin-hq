<?php

    require $_SERVER['DOCUMENT_ROOT']."/common/common.php";	
    require "class/OutstandingPaymentDetail.php";

    $_REQUEST['fullListYN'] = "Y";

    $result = $cOutstandingPaymentDetail->getList($_REQUEST);

    $excelHtml = "
        <table border='1'>
            <thead>
                <tr class='active'>
                    <th class='text-center' rowspan='2'>순번</th>
                    <th class='text-center' rowspan='2'>매입일</th>
                    <th class='text-center' rowspan='2'>업체구분명</th>
                    <th class='text-center' rowspan='2'>거래구분명</th>
                    <th class='text-center' rowspan='2'>매입처구분명</th>
                    <th class='text-center' rowspan='2'>매입처ID</th>
                    <th class='text-center' rowspan='2'>매입처 상호명</th>
                    <th class='text-center' rowspan='2'>매입처 사무실전화</th>
                    <th class='text-center' rowspan='2'>매입처 사무실팩스</th>
                    <th class='text-center' rowspan='2'>담당자명</th>
                    <th class='text-center' rowspan='2'>담당자 휴대폰번호</th>
                    <th class='text-center' rowspan='2'>담당자 직통번호</th>
                    <th class='text-center' rowspan='2'>매입상품(대표)</th>
                    <th class='text-center' colspan='3'>매입</th>
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
            $excelHtml .= "<td class='text-center'>".(empty($item['buyDate']) ? "-":$item['buyDate'])."</td>";
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
            $excelHtml .= "<td class='text-center'>".(empty($item['orderDetails']) ? "-":$item['orderDetails'])."</td>";
            $excelHtml .= "<td class='text-right'>".number_format($item['amountWithoutVAT'])."</td>";
            $excelHtml .= "<td class='text-right'>".number_format($item['amountVAT'])."</td>";
            $excelHtml .= "<td class='text-right'>".number_format($item['amount'])."</td>";
            $excelHtml .= "<td class='text-center'>".(empty($item['payDate']) ? "-":$item['payDate'])."</td>";
            $excelHtml .= "<td class='text-right'>".number_format($item['payWithoutVAT'])."</td>";
            $excelHtml .= "<td class='text-right'>".number_format($item['payVAT'])."</td>";
            $excelHtml .= "<td class='text-right'>".number_format($item['pay'])."</td>";
            $excelHtml .= "<td class='text-right'>".number_format($item['outstandAmountWithoutVAT'])."</td>";
            $excelHtml .= "<td class='text-right'>".number_format($item['outstandAmountVAT'])."</td>";
            $excelHtml .= "<td class='text-right'>".number_format($item['outstandAmount'])."</td>";
            $excelHtml .= "</tr>";
        }   
        header("Content-Type: application/vnd.ms-excel");
        date_default_timezone_set('Asia/Seoul');
    
        header("Content-Disposition: attachment; filename=".date("YmdH", time())."_미결제금액상세현황.xls");
        header("Content-Description:Newtech System");

        echo $excelHtml;
    }else{
        errorAlert("데이터가 존재하지 않습니다.");
    }
   
?>