<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/headerpopup.html";
  require "controller/DeliveryController.php";
  
  $cDeliveryController = new DeliveryController([]);
  $cDeliveryController->setting();
  
?>
