<?php
   require $_SERVER['DOCUMENT_ROOT']."/include/headerpopup.html";
  require "controller/StockController.php";
 
  $cStockController = new StockController([]);
  $cStockController->popupIndex();
?>