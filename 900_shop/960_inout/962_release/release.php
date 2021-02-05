<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/headerShop.html";
  require "controller/ReleaseController.php";
 
  $cReleaseController = new ReleaseController([]);
  $cReleaseController->index();
?>