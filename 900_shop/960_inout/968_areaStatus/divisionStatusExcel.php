<?php

    require $_SERVER['DOCUMENT_ROOT']."/common/common.php";	
    require "class/DivisionStatus.php";

    $cDivisionArray = $cDivisionStatus->getData();

    if(empty($cDivisionArray)){
        errorAlert("등록된 지역이 존재하지 않습니다.");
    }

    header("Content-Type: application/vnd.ms-excel");
    date_default_timezone_set('Asia/Seoul');

    header("Content-Disposition: attachment; filename=".date("YmdH", time())."_지역별수불현황.xls");
    header("Content-Description:Newtech System");

    $_REQUEST['fullListYN'] = "Y";
    $result = $cDivisionStatus->getList($_REQUEST);

    function getDivisionHeaderColoum($divisionArray){
        $html  = "";
        foreach($divisionArray as $index => $item){
            $html .= "<th class='text-center'  colspan='4'>".$item['divisionName']."</th>";
        }

        return $html;
    }

    function getDivisionHeaderSecondColoum($divisionArray){
        $html  = "";

        foreach($divisionArray as $index => $item){
            $html .= "<th class='text-center' colspan='2'>공급</th>";
            $html .= "<th class='text-center' colspan='2'>회수</th>";
        }

        return $html;
    }

    function getDivisionHeaderThirdColoum($divisionArray){
        $html  = "";
        foreach($divisionArray as $index => $item){
            $html .= "<th class='text-center'>수량</th>";
            $html .= "<th class='text-center'>금액</th>";
            $html .= "<th class='text-center'>수량</th>";
            $html .= "<th class='text-center'>금액</th>";
        }

        return $html;
    }

    function getDivisionBodyTable($userArray, $divisionArray, $salePrice){
        $divisionHtml = "";
        foreach($userArray as $index => $item){
            $isExistHeaderData =  false;

            foreach($divisionArray as $sIdx => $subItem){
                if($item['divisionID'] == $subItem['divisionID']){
                    $totalStockOut = empty($subItem['totalStockOut']) ? 0 : $subItem['totalStockOut'];
                    $totalSales = empty($subItem['totalSales']) ? 0 : $subItem['totalSales'];

                    $divisionHtml .= "<td class='text-right'>".number_format($totalStockOut)."</td>";
                    $divisionHtml .= "<td class='text-right'>".number_format($totalStockOut*$salePrice)."</td>";
                    $divisionHtml .= "<td class='text-right'>".number_format($totalSales)."</td>";
                    $divisionHtml .= "<td class='text-right'>".number_format($totalSales*$salePrice)."</td>";

                    $isExistHeaderData = true;
                }
            }

            if(!$isExistHeaderData){
                for($i = 0; $i<4 ; ++$i){
                    $divisionHtml .= "<td class='text-right'>-</td>";
                }
            }
        }

        return $divisionHtml;
    }

    $excelHtml = "
        <table border='1'>
            <thead>
            <tr class='active'>
            <th class='text-center' rowspan='3'>순번</th>
            <th class='text-center' rowspan='3'>관리구분명</th>
            <th class='text-center' rowspan='3'>카테고리</th>
            <th class='text-center' rowspan='3'>상품명</th>
            <th class='text-center' rowspan='3'>매입단가</th>
            <th class='text-center' rowspan='3'>보험청구단가</th>
            ".getDivisionHeaderColoum($cDivisionArray )."
          </tr>
  
          <tr class='active'>
             ".getDivisionHeaderSecondColoum($cDivisionArray )."
          </tr>
  
          <tr class='active'>
             ".getDivisionHeaderThirdColoum($cDivisionArray )."
          </tr>
            </thead>
    ";
    
    if($result['status'] == OK && $result['data'] != HAVE_NO_DATA){ 
        foreach($result['data'] as $key => $item) { 
            $excelHtml .= "<tr>
                <td class='text-center'>".$item['issueCount']."</td>
                <td class='text-center'>".$item['mgmtType']."</td>
                <td class='text-center'>".(empty($item['categoryName']) ? "-" : $item['categoryName'])."</td>
                <td class='text-center'>".$item['goodsName']."</td>
                <td class='text-center'>".number_format($item['buyPrice'])."</td>
                <td class='text-center'>".number_format($item['salePrice'])."</td>
                ".getDivisionBodyTable($cDivisionArray, $item['divisionList'], $item['salePrice'])."
            </tr>
            ";
        }   
    }
    echo $excelHtml;
?>