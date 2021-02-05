<?php

    require $_SERVER['DOCUMENT_ROOT']."/common/common.php";	
    require "class/SalesStatus.php";
    require "controller/SalesStatusController.php";

    $cSalesStatusController = new SalesStatusController(['EC_PAYMENT_TYPE']);
    $paymentArray = $cSalesStatusController->optionArray['EC_PAYMENT_TYPE'];

    if(empty($paymentArray)){
        errorAlert("등록된 결제방법이 존재하지 않습니다.");
    }

    header("Content-Type: application/vnd.ms-excel");
    date_default_timezone_set('Asia/Seoul');

    header("Content-Disposition: attachment; filename=".date("YmdH", time())."_일반판매현황.xls");
    header("Content-Description:Newtech System");
    

    $_REQUEST['fullListYN'] = "Y";
    $result = $cSalesStatus->getList($_REQUEST);

    function getPaymentHeaderColumn($paymentArray){
        $html  = "<th class='text-center'>소계</th>";

        foreach($paymentArray as $index => $item){
            $html .= "<th class='text-center'>".$item['domain']."</th>";
        }

        return $html;
    }

    function getBodyTable($paymentArray, $paymentList){
        $html = "";
        foreach($paymentArray as $index => $item){
            $isExistHeaderData =  false;

            foreach($paymentList as $sIdx => $subItem){
                if($item['code'] == $subItem['paymentCode']){
                    $html .= "<td class='text-right'>".number_format($subItem['paymentAmount'])."</td>";
                    $isExistHeaderData = true;
                }
            }

            if(!$isExistHeaderData){
                $html .= "<td class='text-right'>-</td>";
            }
        }

        return $html;
    }

    $excelHtml = "
        <table border='1'>
            <thead>
                <tr class='active'>
                    <th class='text-center' rowspan='2'>순번</th>
                    <th class='text-center' rowspan='2'>구분</th>
                    <th class='text-center' rowspan='2'>지역</th>
                    <th class='text-center' rowspan='2'>업체명</th>
                    <th class='text-center' rowspan='2'>청구식</th>
                    <th class='text-center' rowspan='2'>거래여부</th>
                    <th class='text-center' rowspan='2'>담당자</th>
                    <th class='text-center' rowspan='2'>사무실전화</th>
                    <th class='text-center' rowspan='2'>담당자휴대폰</th>
                    <th class='text-center' rowspan='2'>최근판매일</th>
                    <th class='text-center' rowspan='2'>매출액계</th>
                    <th class='text-center' rowspan='2'>최근입금일</th>
                    <th class='text-center' colspan='".(count($paymentArray)+1)."'>입금액계</th>
                    <th class='text-center' rowspan='2'>결제잔액</th>
                </tr>
    
                <tr class='active'>
                    ".getPaymentHeaderColumn($paymentArray)."
                </tr>
            </thead>
    ";
    
    if($result['status'] == OK && $result['data'] != HAVE_NO_DATA){ 
        foreach($result['data'] as $key => $item) { 
            $excelHtml .= "<tr>
                <td class='text-center'>".$item['issueCount']."</td>
                <td class='text-center'>".(empty($item['divisionName']) ? "-" : $item['divisionName'])."</td>
                <td class='text-center'>".(empty($item['regionName']) ? "-" : $item['regionName'])."</td>
                <td class='text-center'>".(empty($item['garageName']) ? "-" : $item['garageName'])."</td>
                <td class='text-center'>".(empty($item['billFormulaName']) ? "-" : $item['billFormulaName'])."</td>
                <td class='text-center'>".(empty($item['contractStatusName']) ? "-" : $item['contractStatusName'])."</td>
                <td class='text-center'>".(empty($item['chargeName']) ? "-" : $item['chargeName'])."</td>
                <td class='text-center'>".(empty($item['phoneNum']) ? "-" : setPhoneFormat($item['phoneNum']))."</td>
                <td class='text-center'>".(empty($item['chargePhoneNum']) ? "-" : setPhoneFormat($item['chargePhoneNum']))."</td>
                <td class='text-center'>".(empty($item['issueDate']) ? "-" : ($item['issueDate'] == BASE_DATE_TEXT ? "-" : $item['issueDate']))."</td>
                <td class='text-center'>".number_format($item['totalSalesAmount'])."</td>
                <td class='text-center'>".(empty($item['depositDate']) ? "-" : ($item['depositDate'] == BASE_DATE_TEXT ? "-" : $item['depositDate']))."</td>
                <td class='text-center'>".number_format($item['totalDepositAmount'])."</td>
                ".getBodyTable($paymentArray, $item['paymentList'])."
                <td class='text-right'>".number_format($item['totalReceivableAmount'])."</td>
            </tr>
            ";
        }   
    }
    echo $excelHtml;
?>