<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/headerShop.html";
  require "controller/InOutOrderController.php";
 
  $cInOutOrderController = new InOutOrderController(["EC_USER_STATUS", "EC_STOCK_ORDER_TYPE", "EC_DELIVERY_METHOD"]);
  $cInOutOrderController->index();
?>