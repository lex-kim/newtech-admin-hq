<?php

    require $_SERVER['DOCUMENT_ROOT']."/common/common.php";	
    require "class/Extinction.php";

    $_REQUEST['fullListYN'] = "Y";
    $result = $cExtinction->getList($_REQUEST);

    $excelHtml = "
        <table border='1'>
            <thead>
                <tr class='active'>
                    <th class='text-center'>순번</th>
                    <th class='text-center'>소멸예정일</th>
                    <th class='text-center'>소멸에정포인트</th>
                </tr>
            </thead>
    ";
    
    if($result['status'] == OK && $result['data']!= HAVE_NO_DATA){ 
        foreach($result['data']['data'] as $key => $item) { 
            $excelHtml .= "<tr>
                <td class='text-center'>".$item['issueCount']."</td>
                <td class='text-center'>".(empty($item['expireDate']) ? "-" : $item['expireDate'])."</td>
                <td class='text-center'>".number_format($item['expirePointAmount'])."</td  
            </tr>
            ";
        }   
        header("Content-Type: application/vnd.ms-excel");
        date_default_timezone_set('Asia/Seoul');
    
        header("Content-Disposition: attachment; filename=".date("YmdH", time())."_소멸예정포인트현황.xls");
        header("Content-Description:Newtech System");

        echo $excelHtml;
    }else{
        errorAlert("데이터가 존재하지 않습니다.");
    }

    
?>