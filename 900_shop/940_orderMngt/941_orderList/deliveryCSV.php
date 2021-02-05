<?php
    $csv_dump = "주문번호(필수),택배사명(선택),송장번호(필수)";

    header("Content-type: application/csv" );
    header("Content-Disposition: attachment; filename=deliveryCSV.csv");
    header('Pragma: no-cache');
    
    echo "\xEF\xBB\xBF";
    echo $csv_dump;

?>