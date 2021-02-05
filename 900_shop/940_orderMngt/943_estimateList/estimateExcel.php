<?php
    header("Content-Type: application/vnd.ms-excel");
    date_default_timezone_set('Asia/Seoul');

    header("Content-Disposition: attachment; filename=".date("YmdH", time())."_견적서관리.xls");
    header("Content-Description:Newtech System");

    require $_SERVER['DOCUMENT_ROOT']."/common/common.php";	
    require "class/Estimate.php";

    $_REQUEST['fullListYN'] = "Y";
    $result = $cEstimate->getList($_REQUEST);

    $excelHtml = "
        <table border='1'>
                <tr class='active'>
                    <th class='text-center'>순번</th>
                    <th class='text-center'>작성일</th>
                    <th class='text-center'>견적서번호</th>
                    <th class='text-center'>견적상태</th>
                    <th class='text-center'>거래여부</th>
                    <th class='text-center'>구분</th>
                    <th class='text-center'>지역</th>
                    <th class='text-center'>공업사명</th>
                    <th class='text-center'>업체명</th>
                    <th class='text-center'>수신자</th>
                    <th class='text-center'>수신자전화번호</th>
                    <th class='text-center'>수신자이메일</th>
                    <th class='text-center'>품목</th>
                    <th class='text-center'>총 공급가액</th>
                    <th class='text-center'>총 세액</th>
                    <th class='text-center'>합계금액</th>
                    <th class='text-center'>배송방법</th>
                    <th class='text-center'>결제방법</th>
                    <th class='text-center'>요청자</th>
                    <th class='text-center'>요청일</th>
                    <th class='text-center'>작성자</th>
                    <th class='text-center'>발행일</th>
                    <th class='text-center'>팩스발송여부</th>
                    <th class='text-center'>문자발송여부</th>
                    <th class='text-center'>팩스발송일</th>
                    <th class='text-center'>문자발송일</th>
                    <th class='text-center'>열람일</th>
                </tr>
    ";
    
    if($result['status'] == OK && $result['data'] != HAVE_NO_DATA){ 
        foreach($result['data'] as $key => $item) { 
            $excelHtml .= "<tr>
               <td class='text-center'>".$item['issueCount']."</td>
               <td class='text-center'>".$item['issueDate']."</td>
               <td class='text-center'>".$item['quotationID']."</td>
               <td class='text-center'>".($item['quotationDoneYN'] == "Y" ? "완료" : "요청")."</td>
               <td class='text-center'>".(empty($item['contractStatus']) ? "-" :$item['contractStatus'])."</td>
               <td class='text-center'>".(empty($item['divisionName']) ? "-" :$item['divisionName'])."</td>
               <td class='text-center'>".(empty($item['regionName']) ? "-" :$item['regionName'])."</td>
               <td class='text-center'>".(empty($item['garageName']) ? "-" :$item['garageName'])."</td>
               <td class='text-center'>".$item['toName']."</td>
               <td class='text-center'>".$item['toReceiverName']."</td>
               <td class='text-center'>".setPhoneFormat($item['toPhoneNum'])."</td>
               <td class='text-center'>".$item['toEmail']."</td>
               <td class='text-center'>".(empty($item['quotationDetails']) ? "-" :$item['quotationDetails'])."</td>
               <td class='text-center'>".number_format(empty($item['totalPrice']) ? 0 : $item['totalPrice'])."</td>
               <td class='text-center'>".number_format($item['totalVAT'])."</td>
               <td class='text-center'>".number_format($item['totalSum'])."</td>
               <td class='text-center'>".$item['deliveryMethod']."</td>
               <td class='text-center'>".$item['paymentName']."</td>
               <td class='text-center'>".$item['requestUser']."</td>
               <td class='text-center'>".$item['requestDatetime']."</td>
               <td class='text-center'>".(empty($item['publishUser']) ? "-" :$item['publishUser'])."</td>
               <td class='text-center'>".(empty($item['publishDatetime']) ? "-" :$item['publishDatetime'])."</td>
               <td class='text-center'>".(empty($item['sendFaxYN']) ? "-" :$item['sendFaxYN'])."</td>
               <td class='text-center'>".(empty($item['sendMessageYN']) ? "-" :$item['sendMessageYN'])."</td>
               <td class='text-center'>".(empty($item['sendFaxDatetime']) ? "-" :$item['sendFaxDatetime'])."</td>
               <td class='text-center'>".(empty($item['sendMessageDatetime']) ? "-" :$item['sendMessageDatetime'])."</td>
               <td class='text-center'>".(empty($item['requestViewDatetime']) ? "-" :$item['requestViewDatetime'])."</td>
               </tr>
            ";
        }   
    }
    echo $excelHtml;
?>