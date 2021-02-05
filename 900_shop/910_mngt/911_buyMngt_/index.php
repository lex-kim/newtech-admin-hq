<?php
    // require $_SERVER['DOCUMENT_ROOT']."/include/headerpopup.html";
    require_once "controller/BuyMngtController.php";

    $getUrl = '';
    if (isset($_GET['url'])) {
        $getUrl = rtrim($_GET['url'], '/');
        $getUrl = filter_var($getUrl, FILTER_SANITIZE_URL);
    }
    $getParams = explode('/', $getUrl);

    
    $params['action']   = isset($getParams[0]) && $getParams[0] != '' ? $getParams[0] : 'index';
    $params['idx']      = isset($getParams[1]) && $getParams[1] != '' ? $getParams[1] : null;

    $controller = new \App\Controllers\BuyMngtController($params['action'] , $params['idx'] );
?>