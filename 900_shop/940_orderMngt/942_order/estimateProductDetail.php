<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/headerpopup.html";
  require "controller/OrderController.php";
  
  $cOrderController = new OrderController([""]);
  $cOrderController->detailPopupIndex();
?>
