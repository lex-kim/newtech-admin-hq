<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/headerShop.html";
  require "controller/StockController.php";
 
  $cStockController = new StockController([]);
  $cStockController->index();
?>