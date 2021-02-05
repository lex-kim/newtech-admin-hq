<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/headerShop.html";
  require "controller/DeliveryController.php";
  
  $cDeliveryController = new DeliveryController([]);
  $cDeliveryController->index();
?>
