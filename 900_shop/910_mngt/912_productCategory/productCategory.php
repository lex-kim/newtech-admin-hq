<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/headerShop.html";
  require "controller/ProductCtgyController.php";
 
  $cProductCtgyController = new ProductCtgyController([]);
  $cProductCtgyController->index();
?>