<?php

    require $_SERVER['DOCUMENT_ROOT']."/common/common.php";	
    require "class/Calculate.php";

    $_REQUEST['fullListYN'] = "Y";
    $result = $cCalculate->getList($_REQUEST);


    $excelHtml .= "
        <table border='1'>
            <thead>
                <tr class='active'>
                    <th class='text-center'>순번</th>
                    <th class='text-center'>대상일자</th>
                    <th class='text-center'>적립예정</th>
                    <th class='text-center'>적립확정</th>
                    <th class='text-center'>사용예정</th>
                    <th class='text-center'>사용확정</th>
                    <th class='text-center'>소멸예정</th>
                    <th class='text-center'>소멸확정</th>
                </tr>
            </thead>
    ";
    
    if($result['status'] == OK && $result['data'] != HAVE_NO_DATA){ 
        $totalHtml = "";

        if(!empty($result['data']['summary'])){
            $summaryData = $result['data']['summary'];
            $totalHtml = "<table border='1'>
                            <colgroup>
                                <col width='30%'/>
                                <col width='70%'/>
                            </colgroup>
                            <tbody id='mypoint'>
                                <tr>
                                    <td class='text-center active'>총 발생포인트 잔액</td>
                                    <td class='text-center' id='pointEarnExpect'>".number_format($summaryData['pointBalance'])."</td>
                                    <td class='text-center active'>적립예정</td>
                                    <td class='text-center' id='pointEarnExpect'>".number_format($summaryData['pointEarnExpect'])."</td>
                                    <td class='text-center active'>적립확정</td>
                                    <td class='text-center' id='pointEarn'>".number_format($summaryData['pointEarn'])."</td>
                                    <td class='text-center active'>사용예정</td>
                                    <td class='text-center' id='pointRedeemExpect'>".number_format($summaryData['pointRedeemExpect'])."</td>
                                    <td class='text-center active'>사용확정</td>
                                    <td class='text-center' id='pointRedeem'>".number_format($summaryData['pointRedeem'])."</td>
                                    <td class='text-center active'>소멸예정</td>
                                    <td class='text-center' id='pointExpireExpect'>".number_format($summaryData['pointExpireExpect'])."</td>
                                    <td class='text-center active'>소멸확정</td>
                                    <td class='text-center' id='pointExpire'>".number_format($summaryData['pointExpire'])."</td>
                                </tr>                            
                            </tbody>
                        </table>
            ";
            $totalHtml .= "<table>
                            <tbody>
                                <tr>
                                    <td class='text-center active'></td>
                                </tr>                            
                            </tbody>
                        </table>
            ";
        }

        foreach($result['data']['statusList'] as $key => $item) { 
            $excelHtml .= "<tr>
                <td class='text-center'>".$item['issueCount']."</td>
                <td class='text-center'>".(empty($item['issueDate']) ? "-" : $item['issueDate'])."</td>
                <td class='text-center'>".number_format($item['pointEarnExpect'])."</td>
                <td class='text-center'>".number_format($item['pointEarn'])."</td>
                <td class='text-center'>".number_format($item['pointRedeemExpect'])."</td>
                <td class='text-center'>".number_format($item['pointRedeem'])."</td>
                <td class='text-center'>".number_format($item['pointExpireExpect'])."</td>
                <td class='text-center'>".number_format($item['pointExpire'])."</td> 
            </tr>
            ";
        }   
        header("Content-Type: application/vnd.ms-excel");
        date_default_timezone_set('Asia/Seoul');
    
        header("Content-Disposition: attachment; filename=".date("YmdH", time())."_포인트현황.xls");
        header("Content-Description:Newtech System");

        echo $totalHtml.$excelHtml;
    }else{
        errorAlert("데이터가 존재하지 않습니다.");
    }
    
?>