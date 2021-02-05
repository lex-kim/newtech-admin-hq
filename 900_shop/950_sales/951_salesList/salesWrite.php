<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/headerpopup.html";
  require $_SERVER['DOCUMENT_ROOT']."/common/indicator.php";
  require "controller/SaleListController.php";
  
  $cSaleListController = new SaleListController(["EC_PAYMENT_TYPE"]);
  $cSaleListController->popupIndex();
?>
