<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/headerShop.html";
  require "controller/IntegratedDeliveryController.php";

  $cIntegratedDelivery = new IntegratedDeliveryController();
  $cIntegratedDelivery->index();
?>