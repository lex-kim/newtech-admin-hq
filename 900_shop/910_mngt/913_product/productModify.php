<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/headerpopup.html";
  require "controller/ProductController.php";
  
 
  $cProductController = new ProductController(["EC_MGMT_TYPE"]);
  $cProductController->popupIndex();
?>
