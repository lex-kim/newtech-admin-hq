<?php
    $ext = explode('/', $_REQUEST['url']);
    $fileName = array_pop($ext);
    header("Content-Type: application/octet-stream");
    header("Content-Transfer-Encoding: Binary");
    header("Content-disposition: attachment; filename=".$fileName); 
    
    echo readfile($_REQUEST['url']);
?>