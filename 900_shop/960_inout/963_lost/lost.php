<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/headerShop.html";
  require "controller/LostController.php";
 
  $cLostController = new LostController(['EC_DISPOSAL_STATUS']);
  $cLostController->index();
?>