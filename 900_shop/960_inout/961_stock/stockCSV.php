<?php
    $csv_dump = "입고일(필수),메모(선택),상품아이디(필수),상품명(선택),단가(필수),수량(필수)";
    $csv_dump .= "\r\n";
    $csv_dump .= "YYYY-MM-DD,텍스트,코드,텍스트,숫자,숫자";

    header( "Content-type: application/csv" );
    header("Content-Disposition: attachment; filename=stockInCSV.csv");
    header('Pragma: no-cache');
    
    echo "\xEF\xBB\xBF";
    echo $csv_dump;

?>