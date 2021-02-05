<?php
    $path  = $_SERVER['DOCUMENT_ROOT']."/Popbill/PopbillFax.php";
    //exit;
    require "$path";
    //팝빌 연동에 필요한 부분
    // 링크아이디
    $LinkID = 'DGMIT';
    // 비밀키
    $SecretKey = 'dtn5/HzBAEKJMBWgnsRqrvguJ1IQjcQG1FQ/vEZzMdc=';
    // 통신방식 기본은 CURL , curl 사용에 문제가 있을경우 STREAM 사용가능.
    // STREAM 사용시에는 php.ini의 allow_url_fopen = on 으로 설정해야함.
    define('LINKHUB_COMM_MODE','STREAM');
    $FaxService = new FaxService($LinkID, $SecretKey);
    // 연동환경 설정값, 개발용(true), 상업용(false)
    $FaxService->IsTest(false);
?>