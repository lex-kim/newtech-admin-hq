<?php
    header("Content-Type: application/vnd.ms-excel");
    date_default_timezone_set('Asia/Seoul');

    header("Content-Disposition: attachment; filename=".date("YmdH", time())."_발주관리.xls");
    header("Content-Description:Newtech System");

    require $_SERVER['DOCUMENT_ROOT']."/common/common.php";	
    require "class/InOutOrder.php";

    $_REQUEST['fullListYN'] = "Y";
    $result = $cInOutOrder->getList($_REQUEST);

    $excelHtml = "
        <table border='1'>
            <tr class='active'>
                <th class='text-center' rowspan='2'>발주일</th>
                <th class='text-center' rowspan='2'>납기일</th>
                <th class='text-center' rowspan='2'>발주번호</th>
                <th class='text-center' rowspan='2'>발주구분</th>
                <th class='text-center' rowspan='2'>원거래<br>(주문서아이디)</th>
                <th class='text-center' colspan='3'>매입처</th>
                <th class='text-center' rowspan='2'>품목</th>
                <th class='text-center' rowspan='2'>합계액</th>
                <th class='text-center' colspan='4'>입고처</th>
                <th class='text-center' rowspan='2'>배송방법</th>
                <th class='text-center' rowspan='2'>비고</th>
                <th class='text-center' colspan='2'>발송</th>
                <th class='text-center' rowspan='2'>결제일</th>
                <th class='text-center' rowspan='2'>결제금액</th>
                <th class='text-center' rowspan='2'>결제여부</th>
                <th class='text-center' rowspan='2'>은행명</th>
                <th class='text-center' rowspan='2'>사업용계좌</th>
                <th class='text-center' rowspan='2'>계좌주</th>
                <th class='text-center' rowspan='2'>입고여부</th>
                <th class='text-center' rowspan='2'>입고처리일</th>
            </tr>
            <tr class='active'>
                <th class='text-center'>상호</th>
                <th class='text-center'>전화번호</th>
                <th class='text-center'>팩스번호</th>
            
                <th class='text-center'>상호</th>
                <th class='text-center'>주소</th>
                <th class='text-center'>담당자</th>
                <th class='text-center'>연락처</th>
                <th class='text-center'>팩스일시</th>
                <th class='text-center'>문자일시</th>
            </tr>
    ";
    
    if($result['status'] == OK && $result['data'] != HAVE_NO_DATA){ 
        foreach($result['data'] as $key => $item) { 
            $excelHtml .= "<tr>
                <td class='text-center'>".(empty($item['issueDate']) ? "-" :$item['issueDate'])."</td>
                <td class='text-center'>".(empty($item['dueDate']) ? "-" :$item['dueDate'])."</td>
                <td class='text-center'>".$item['stockOrderID']."</td>
                <td class='text-center'>".$item['stockOrderType']."</td>
                <td class='text-center'>".$item['orderID']."</td>
                <td class='text-center'>".$item['toName']."</td>
                <td class='text-center'>".setPhoneFormat($item['toPhoneNum'])."</td>
                <td class='text-center'>".setPhoneFormat($item['toFaxNum'])."</td>
                <td class='text-center'>".$item['stockOrderDetails']."</td>
                <td class='text-center'>".number_format(empty($item['totalPrice']) ? 0 : $item['totalPrice'])."</td>
                <td class='text-center'>".$item['shipName']."</td>
                <td class='text-left'>".($item['shipAddress1']." ".$item['shipAddress2'])."</td>
                <td class='text-center'>".$item['shipChargeName']."</td>
                <td class='text-center'>".setPhoneFormat($item['shipChargePhoneNum'])."</td>
                <td class='text-center'>".$item['deliveryMethod']."</td>
                <td class='text-center'>".$item['memo']."</td>
                <td class='text-center'>".(empty($item['sendFaxDatetime']) ? "-" : $item['sendFaxDatetime'])."</td>
                <td class='text-center'>".(empty($item['sendMessageDatetime']) ? "-" : $item['sendMessageDatetime'])."</td>
                <td class='text-center'>".(empty($item['depositDate']) ? "-" : $item['depositDate'])."</td>
                <td class='text-center'>".number_format(empty($item['depositPrice']) ? 0 : $item['depositPrice'])."</td>
                <td class='text-center'>".$item['depositYN']."</td>
                <td class='text-center'>".(empty($item['depositBankName']) ? "-" : $item['depositBankName'])."</td>
                <td class='text-center'>".(empty($item['depositBankNum']) ? "-" : $item['depositBankNum'])."</td>
                <td class='text-center'>".(empty($item['depositBankOwn']) ? "-" : $item['depositBankOwn'])."</td>
                <td class='text-center'>".$item['stockInYN']."</td>
                <td class='text-center'>".(empty($item['stockInDatetime']) ? "-" : $item['stockInDatetime'])."</td>
               </tr>
            ";
        }   
    }
    echo $excelHtml;
?>