<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/headerShop.html";
  require "controller/ProductController.php";
  
  $cProductController = new ProductController(["EC_MGMT_TYPE", "EC_BUYER_TYPE"]);
  $cProductController->index();
?>
