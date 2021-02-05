<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/headerShop.html";
  require "controller/BuyMngtController.php";
  
  $cBuyMngtController = new BuyMngtController(["EC_BIZ_TYPE", "EC_USER_TAX_TYPE", "EC_PARTNER_TYPE", "EC_BUYER_TYPE" ,"EC_USER_STATUS", "EC_AUTO_ORDER_METHOD"]);
  $cBuyMngtController->index();
?>
