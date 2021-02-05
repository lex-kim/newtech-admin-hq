<?php

    require $_SERVER['DOCUMENT_ROOT']."/common/common.php";	
    require "class/EmployeeStatus.php";

    $cUserArray = $cEmployeeStatus->getData();

    if(empty($cUserArray)){
        errorAlert("등록된 직원이 존재하지 않습니다.");
    }

    header("Content-Type: application/vnd.ms-excel");
    date_default_timezone_set('Asia/Seoul');

    header("Content-Disposition: attachment; filename=".date("YmdH", time())."_직원별수불현황.xls");
    header("Content-Description:Newtech System");

    $_REQUEST['fullListYN'] = "Y";
    $result = $cEmployeeStatus->getList($_REQUEST);

    function getEmployeeHeaderColoum($employeeArray){
        $html  = "";
        foreach($employeeArray as $index => $item){
            $html .= "<th class='text-center'  colspan='10'>".$item['employeeName']."</th>";
        }

        return $html;
    }

    function getEmployeeHeaderSecondColoum($employeeArray){
        $html  = "";

        foreach($employeeArray as $index => $item){
            $html .= "<th class='text-center' colspan='2'>출고</th>";
            $html .= "<th class='text-center' colspan='2'>공급</th>";
            $html .= "<th class='text-center' colspan='2'>회수</th>";
            $html .= "<th class='text-center' colspan='2'>분실</th>";
            $html .= "<th class='text-center' colspan='2'>폐기</th>";
        }

        return $html;
    }

    function getEmployeeHeaderThirdColoum($employeeArray){
        $html  = "";
        foreach($employeeArray as $index => $item){
            $html .= "<th class='text-center'>수량</th>";
            $html .= "<th class='text-center'>금액</th>";
            $html .= "<th class='text-center'>수량</th>";
            $html .= "<th class='text-center'>금액</th>";
            $html .= "<th class='text-center'>수량</th>";
            $html .= "<th class='text-center'>금액</th>";
            $html .= "<th class='text-center'>수량</th>";
            $html .= "<th class='text-center'>금액</th>";
            $html .= "<th class='text-center'>수량</th>";
            $html .= "<th class='text-center'>금액</th>";
        }

        return $html;
    }

    function getEmployeeBodyTable($userArray, $employeeArray, $salePrice){
        $employeeHtml = "";
        foreach($userArray as $index => $item){
            $isExistHeaderData =  false;

            foreach($employeeArray as $sIdx => $subItem){
                if($item['employeeID'] == $subItem['employeeID']){
                    $totalStockOut = empty($subItem['totalStockOut']) ? 0 : $subItem['totalStockOut'];
                    $totalSales = empty($subItem['totalSales']) ? 0 : $subItem['totalSales'];
                    $totalStockReturn = empty($subItem['totalStockReturn']) ? 0 : $subItem['totalStockReturn'];
                    $totalStockLost = empty($subItem['totalStockLost']) ? 0 : $subItem['totalStockLost']; 
                    $totalStockDisposal = empty($subItem['totalStockDisposal']) ? 0 : $subItem['totalStockDisposal'];

                    $employeeHtml .= "<td class='text-right'>".number_format($totalStockOut)."</td>";
                    $employeeHtml .= "<td class='text-right'>".number_format($totalStockOut*$salePrice)."</td>";
                    $employeeHtml .= "<td class='text-right'>".number_format($totalSales)."</td>";
                    $employeeHtml .= "<td class='text-right'>".number_format($totalSales*$salePrice)."</td>";
                    $employeeHtml .= "<td class='text-right'>".number_format($totalStockReturn)."</td>";
                    $employeeHtml .= "<td class='text-right'>".number_format($totalStockReturn*$salePrice)."</td>";
                    $employeeHtml .= "<td class='text-right'>".number_format($totalStockLost)."</td>";
                    $employeeHtml .= "<td class='text-right'>".number_format($totalStockLost*$salePrice)."</td>";
                    $employeeHtml .= "<td class='text-right'>".number_format($totalStockDisposal)."</td>";
                    $employeeHtml .= "<td class='text-right'>".number_format($totalStockDisposal*$salePrice)."</td>";

                    $isExistHeaderData = true;
                }
            }

            if(!$isExistHeaderData){
                for($i = 0; $i<10 ; ++$i){
                    $employeeHtml .= "<td class='text-right'>-</td>";
                }
            }
        }

        return $employeeHtml;
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
            ".getEmployeeHeaderColoum($cUserArray )."
          </tr>
  
          <tr class='active'>
             ".getEmployeeHeaderSecondColoum($cUserArray )."
          </tr>
  
          <tr class='active'>
             ".getEmployeeHeaderThirdColoum($cUserArray )."
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
                ".getEmployeeBodyTable($cUserArray, $item['employeeList'], $item['salePrice'])."
            </tr>
            ";
        }   
    }
    echo $excelHtml;
?>