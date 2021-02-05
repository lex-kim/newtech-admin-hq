<?php
    header("Content-Type: application/vnd.ms-excel");
    date_default_timezone_set('Asia/Seoul');

    header("Content-Disposition: attachment; filename=".date("YmdH", time())."_거래처관리.xls");
    header("Content-Description:Newtech System");

    require $_SERVER['DOCUMENT_ROOT']."/common/common.php";	
    require "class/BuyMngt.php";

    $_REQUEST['fullListYN'] = "Y";
    $result = $cBuyMngt->getList($_REQUEST);

    $excelHtml = "
        <table border='1'>
            <thead>
            <tr class='active'>
                <th class='text-center' rowspan='2'>순번</th>
                <th class='text-center' rowspan='2'>업체구분</th>
                <th class='text-center' rowspan='2'>과세구분</th>
                <th class='text-center' rowspan='2'>거래구분</th>
                <th class='text-center' rowspan='2'>사업자번호</th>
                <th class='text-center' rowspan='2'>상호</th>
                <th class='text-center' rowspan='2'>업태</th>
                <th class='text-center' rowspan='2'>종목</th>
                <th class='text-center' rowspan='2'>주소</th>
                <th class='text-center' colspan='2'>사무실</th>
                <th class='text-center' colspan='2'>대표자</th>
                <th class='text-center' colspan='4'>담당자휴대폰</th>
                <th class='text-center' rowspan='2'>사이트URL</th>
                <th class='text-center' rowspan='2'>주요품목</th>
                <th class='text-center' rowspan='2'>메모</th>
                <th class='text-center' rowspan='2'>거래상태</th>
                <th class='text-center' rowspan='2'>결제기일</th>
                <th class='text-center' rowspan='2'>거래은행</th>
                <th class='text-center' rowspan='2'>계좌번호</th>
                <th class='text-center' rowspan='2'>계좌주명</th>
                <th class='text-center' rowspan='2'>자동발주</th>
                <th class='text-center' rowspan='2'>자동발주방법</th>
                <th class='text-center' rowspan='2'>등록자</th>
                <th class='text-center' rowspan='2'>등록일시</th>
            </tr>
            <tr class='active'>
                <th class='text-center'>대표번호</th>
                <th class='text-center'>팩스번호</th>
                <th class='text-center'>대표자명</th>
                <th class='text-center'>휴대폰</th>
                <th class='text-center'>담당자명</th>
                <th class='text-center'>휴대폰</th>
                <th class='text-center'>직통전화</th>
                <th class='text-center'>이메일</th>
            </tr>
        </thead>
    ";
    if($result['status'] == OK && $result['data'] != HAVE_NO_DATA){ 
        foreach($result['data'] as $key => $item) { 
            $excelHtml .= "<tr>
                <td class='text-center'>".$item['issueCount']."</td>
                <td class='text-center'>".$item['bizType']."</td>
                <td class='text-center'>".$item['bizTaxType']."</td>
                <td class='text-center'>".$item['partnerType']."</td>
                <td class='text-center'>".bizNoFormat($item['companyRegID'])."</td>
                <td class='text-left'>".$item['companyName']."</td>
                <td class='text-center'>".$item['companyBiz']."</td>
                <td class='text-center'>".$item['companyPart']."</td>
                <td class='text-left'>".($item['Address1']." ".$item['Address2'])."</td>
                <td class='text-center'>".setPhoneFormat($item['phoneNum'])."</td>
                <td class='text-center'>".setPhoneFormat($item['faxNum'])."</td>
                <td class='text-center'>".$item['ceoName']."</td>
                <td class='text-center'>".setPhoneFormat($item['ceoPhoneNum'])."</td>
                <td class='text-center'>".$item['chargeName']."</td>
                <td class='text-center'>".setPhoneFormat($item['chargePhoneNum'])."</td>
                <td class='text-center'>".setPhoneFormat($item['chargeDirectPhoneNum'])."</td>
                <td class='text-left'>".$item['chargeEmail']."</td>
                <td class='text-left'>".$item['homepage']."</td>
                <td class='text-left'>".$item['mainGoods']."</td>
                <td class='text-left'>".$item['memo']."</td>
                <td class='text-center'>".$item['contractStatus']."</td>";
            if(!empty($item['paymentDueDate'])){
                $excelHtml .= "<td class='text-center'>".$item['paymentDueDate']."일</td>";
            }else{
                $excelHtml .= "<td class='text-center'></td>"; 
            }
            $excelHtml .= "<td class='text-center'>".$item['bankName']."</td>";
            $excelHtml .= "<td class='text-center' style=mso-number-format:'\@'>".$item['bankNum']."</td>";
            $excelHtml .= "<td class='text-center'>".$item['bankOwnName']."</td>";

            if($item['autoOrderYN'] == "Y"){//자동발주
                $excelHtml .= "<td class='text-center'>자동</td>";
            }else{
                $excelHtml .= "<td class='text-center'>수동</td>";
            }
            $excelHtml .= "<td class='text-center'>".$item['autoOrderMethod']."</td>";
            $excelHtml .= "<td class='text-center'>".$item['writeUser']."</td>";
            $excelHtml .= "<td class='text-center'>".$item['writeDatetime']."</td>";
            $excelHtml .= "</tr>";
        }   
    }
    echo $excelHtml;
?>