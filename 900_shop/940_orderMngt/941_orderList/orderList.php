<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/headerShop.html";
  require "controller/OrderListController.php";
  
  $cOrderListController = new OrderListController(["EC_ORDER_STATUS" , "EC_DELIVERY_METHOD", "CONTRACT_TYPE", "EC_PAYMENT_TYPE", "EC_ORDER_CANCEL", "EC_ORDER_CANCEL_TYPE", "EC_BILL_TYPE"]);
  $cOrderListController->index();
?>
