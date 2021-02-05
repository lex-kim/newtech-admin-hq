<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/headerpopup.html";
  require "controller/OrderListController.php";
  
  $cOrderListController = new OrderListController([""]);
  $cOrderListController->popupIndex();
?>
