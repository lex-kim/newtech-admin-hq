<?php
    require $_SERVER['DOCUMENT_ROOT']."/common/common.php";	
    require "class/Store.php";

    $_REQUEST['fullListYN'] = "Y";
    $result = $cStore->getList($_REQUEST);
 
    $excelHtml = "
        <table border='1'>
            <thead>
            <tr class='active'>
            <th class='text-center' rowspan='2'>순번</th>
            <th class='text-center' rowspan='2'>업체ID</th>
            <th class='text-center' rowspan='2'>구분</th>
            <th class='text-center' rowspan='2'>쇼핑몰등급</th>
            <th class='text-center' rowspan='2'>로그인ID</th>
            <th class='text-center' rowspan='2'>사업자번호</th>
            <th class='text-center' rowspan='2'>업체명</th>
            <th class='text-center' rowspan='2'>주소</th>
            <th class='text-center' colspan='3'>사무실</th>
            <th class='text-center' colspan='2'>대표자</th>
            <th class='text-center' colspan='2'>담당자</th>
            <th class='text-center' rowspan='2'>거래상태</th>
            <th class='text-center' rowspan='2'>거래개시일</th>
            <th class='text-center' rowspan='2'>작성자</th>
            <th class='text-center' rowspan='2'>작성일시</th>
          </tr>
  
          <tr class='active'>
            <th class='text-center'>연락처</th>
            <th class='text-center'>FAX</th>
            <th class='text-center'>이메일</th>
            <th class='text-center'>성명</th>
            <th class='text-center'>연락처</th>
            <th class='text-center'>성명</th>
            <th class='text-center'>연락처</th>
          </tr>
        </thead>
    ";
    
    if($result['status'] == OK && $result['data']!= HAVE_NO_DATA){ 
        foreach($result['data']['data'] as $key => $item) { 
            $excelHtml .= "<tr>
                <td class='text-center'>".$item['issueCount']."</td>
                <td class='text-center'>".$item['garageID']."</td>
                <td class='text-center'>".(empty($item['divisionName']) ? "-" : $item['divisionName'] )."</td>
                <td class='text-center'>".$item['rankName']."</td>
                <td class='text-center'>".$item['loginID']."</td>
                <td class='text-center' style='mso-number-format:\@;'>".bizNoFormat($item['companyRegID'])."</td>
                <td class='text-center'>".$item['garageName']."</td>
                <td class='text-center'>".$item['address1'].' '.$item['address2']."</td>
                <td class='text-center' style='mso-number-format:\@;'>".setPhoneFormat($item['phoneNum'])."</td>
                <td class='text-center' style='mso-number-format:\@;'>".setPhoneFormat($item['faxNum'])."</td>
                <td class='text-center'>".$item['companyEmail']."</td>
                <td class='text-center'>".$item['ceoName']."</td>
                <td class='text-center' style='mso-number-format:\@;'>".setPhoneFormat($item['ceoPhoneNum'])."</td>
                <td class='text-center'>".$item['chargeName']."</td>
                <td class='text-center' style='mso-number-format:\@;'>".setPhoneFormat($item['chargePhoneNum'])."</td>
                <td class='text-left'>".$item['contractStatusName']."</td>
                <td class='text-center'>".(empty($item['contractStartDate']) ? "-" : $item['contractStartDate'] )."</td>
                <td class='text-center'>".$item['writeUser']."</td>
                <td class='text-center'>".$item['writeDatetime']."</td>";
            $excelHtml .= "</tr>";
        }  
        header("Content-Type: application/vnd.ms-excel");
        date_default_timezone_set('Asia/Seoul');

        header("Content-Disposition: attachment; filename=".date("YmdH", time())."_업체관리.xls");
        header("Content-Description:Newtech System");

        echo $excelHtml;
    }else{
        errorAlert("데이터가 존재하지 않습니다.");
    }
   
?>