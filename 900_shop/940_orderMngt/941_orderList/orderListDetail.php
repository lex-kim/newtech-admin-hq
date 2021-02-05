<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/headerpopup.html";
  require "controller/OrderListController.php";
  
  $cOrderListController = new OrderListController(["BANK_CD", "EC_DELIVERY_METHOD", "EC_RETURN_METHOD", "EC_RETURN_FEE_TYPE", "EC_RETURN_TYPE", "EC_ORDER_CANCEL", "EC_ORDER_CANCEL_TYPE"]);
  $cOrderListController->popupIndex();
?>
