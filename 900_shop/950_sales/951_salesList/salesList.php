<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/headerShop.html";
  require "controller/SaleListController.php";
  
  $cSaleListController = new SaleListController(["CONTRACT_TYPE", "EC_BILL_TYPE", "EC_PAYMENT_TYPE"]);
  $cSaleListController->index();
?>
