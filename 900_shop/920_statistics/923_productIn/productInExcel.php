<?php

    require $_SERVER['DOCUMENT_ROOT']."/common/common.php";	
    require "class/ProductInStatus.php";

    $headerResult = $cProductInStatus->getData($_REQUEST);
    $headerArray = array();

    if($headerResult['status'] == OK){
        if(!empty($headerResult['data'] ) && $headerResult['data'] != HAVE_NO_DATA){
            $headerArray = $headerResult['data'];
        }
    }else{
        errorAlert("통게연월 정보를 가져오는데 실패하였습니다.");
    }


    $_REQUEST['fullListYN'] = "Y";
    $result = $cProductInStatus->getList($_REQUEST);

    function getHeaderTable($headerArray){
        $html = "";
        foreach($headerArray as $index => $item){
            $html .= '<th class="text-center" colspan="4">'.$item['YYYYMM'].'</th>';
        }
        return $html;
    }

    function getHeaderSubTable($headerArray){
        $html = "";
        foreach($headerArray as $index => $item){
            $html .= ' <th class="text-center">구매수량</th>';
            $html .= ' <th class="text-center">구매금액</th>';
            $html .= ' <th class="text-center">판매수량</th>';
            $html .= ' <th class="text-center">판매금액</th>';
        }
        return $html;
    }

    $excelHtml = "
        <table border='1'>
            <thead>
             <tr>
             <th class='text-center' rowspan='2'>순번</th>
             <th class='text-center' rowspan='2'>매입처ID</th>
             <th class='text-center' rowspan='2'>매입처</th>
             <th class='text-center' rowspan='2'>사무실전화</th>
             <th class='text-center' rowspan='2'>사무실팩스</th>
             <th class='text-center' rowspan='2'>담당자</th>
             <th class='text-center' rowspan='2'>담당자휴대폰</th>
             <th class='text-center' rowspan='2'>카테고리</th>
             <th class='text-center' rowspan='2'>상품코드</th>
             <th class='text-center' rowspan='2'>상품명</th>
             ".getHeaderTable($headerArray)."
             </tr>
             <tr class='active'>
             ".getHeaderSubTable($headerArray)."
             </tr>
            </thead>
    ";
    
    if($result['status'] == OK && $result['data'] != HAVE_NO_DATA){ 
        foreach($result['data'] as $key => $item) { 
            $excelHtml .=  "<tr>";
            $excelHtml .=  "<td class='text-center'>".$item['issueCount']."</td>";
            $excelHtml .=  "<td class='text-center'>".$item['partnerID']."</td>";
            $excelHtml .=  "<td class='text-center'>".$item['partnerName']."</td>";
            $excelHtml .=  "<td class='text-center'>".setPhoneFormat($item['phoneNum'])."</td>";
            $excelHtml .=  "<td class='text-center'>".(empty($item['faxNum']) ? "-" : setPhoneFormat($item['faxNum']))."</td>";
            $excelHtml .=  "<td class='text-center'>".(empty($item['chargeName']) ? "-" : $item['chargeName'])."</td>";
            $excelHtml .=  "<td class='text-center'>".(empty($item['chargePhoneNum']) ? "-" : setPhoneFormat($item['chargePhoneNum']))."</td>";
            $excelHtml .=  "<td class='text-center'>".$item['categoryName']."</td>";
            $excelHtml .=  "<td class='text-center'>".$item['goodsCode']."</td>";
            $excelHtml .=  "<td class='text-center'>".$item['goodsName']."</td>";
        
            foreach($headerArray as $index => $headerItem){
                if($item['buyCount_'.$headerItem['YYYYMM']] == "undefined"){
                    $excelHtml .=  "<td class='text-center'>-</td>";
                }else{
                    $excelHtml .=  "<td class='text-center'>".number_format($item['buyCount_'.$headerItem['YYYYMM']])."</td>";
                }
                
                if($item['buyPrice_'.$headerItem['YYYYMM']] == "undefined"){
                    $excelHtml .=  "<td class='text-center'>-</td>";
                }else{
                    $excelHtml .=  "<td class='text-center'>".number_format($item['buyPrice_'.$headerItem['YYYYMM']])."</td>";
                }

                if($item['sellCount_'.$headerItem['YYYYMM']] == "undefined"){
                    $excelHtml .=  "<td class='text-center'>-</td>";
                }else{
                    $excelHtml .=  "<td class='text-center'>".number_format($item['sellCount_'.$headerItem['YYYYMM']])."</td>";
                }

                if($item['sellPrice_'.$headerItem['YYYYMM']] == "undefined"){
                    $excelHtml .=  "<td class='text-center'>-</td>";
                }else{
                    $excelHtml .=  "<td class='text-center'>".number_format($item['sellPrice_'.$headerItem['YYYYMM']])."</td>";
                }
            }
            $excelHtml .=  "</tr>";
        }   
        header("Content-Type: application/vnd.ms-excel");
        date_default_timezone_set('Asia/Seoul');

        header("Content-Disposition: attachment; filename=".date("YmdH", time())."_제품별매입/매출통계현황.xls");
        header("Content-Description:Newtech System");

        echo $excelHtml;
    }else{
        errorAlert("데이터가 존재하지 않습니다.");
    }
   
?>