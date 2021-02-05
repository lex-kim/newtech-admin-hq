<?php
    header("Content-Type: application/vnd.ms-excel");
    date_default_timezone_set('Asia/Seoul');

    header("Content-Disposition: attachment; filename=".date("YmdH", time())."_상품카테고리관리.xls");
    header("Content-Description:Newtech System");

    require $_SERVER['DOCUMENT_ROOT']."/common/common.php";	
    require "class/ProductCtgy.php";

    $_REQUEST['fullListYN'] = "Y";
    $result = $cProductCtgy->getList($_REQUEST);

    $excelHtml = "
        <table border='1'>
            <thead>
            <tr class='active'>
                <th class='text-center'>순번</th>
                <th class='text-center'>상위카테고리명(코드)</th>
                <th class='text-center'>카테고리명(코드)</th>
                <th class='text-center'>노출여부</th>
                <th class='text-center'>노출순서</th>
                <th class='text-center'>등록자</th>
                <th class='text-center'>등록일시</th>
            </tr>
        </thead>
    ";
    
    if($result['status'] == OK && $result['data'] != HAVE_NO_DATA){ 
        foreach($result['data'] as $key => $item) { 
            $excelHtml .= "<tr>
                <td class='text-center'>".$item['issueCount']."</td>
                <td class='text-center'>".(!empty($item['parentCategory']) ? $item['parentCategory'] : "");
            if(!empty($item['parentCategoryID'])){
                $excelHtml .= " (".$item['parentCategoryID'].")";
            }
            $excelHtml .= "</td>";
            $excelHtml .=  "<td class='text-center'>".$item['thisCategory'];
            $excelHtml .= " (".$item['thisCategoryID'].")";
            $excelHtml .= "</td>";
            $excelHtml .= "
               <td class='text-center'>".$item['exposeYN']."</td>
               <td class='text-center'>".$item['orderNum']."</td>
               <td class='text-center'>".$item['writeUser']."</td>
               <td class='text-center'>".$item['writeDatetime']."</td>
            ";
        }   
    }
    echo $excelHtml;
?>