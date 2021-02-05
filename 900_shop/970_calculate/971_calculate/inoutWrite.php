<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/headerpopup.html";
  require "controller/InOutOrderController.php";
  
  $cInOutOrderController = new InOutOrderController(["EC_DELIVERY_METHOD"]);
  $cInOutOrderController->popupIndex();
?>
