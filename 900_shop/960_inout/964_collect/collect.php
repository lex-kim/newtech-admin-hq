<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/headerShop.html";
  require $_SERVER['DOCUMENT_ROOT']."/common/indicator.php";
  require "controller/CollectController.php";
 
  $cCollectController = new CollectController(['CONTRACT_TYPE']);
  $cCollectController->index();
?>