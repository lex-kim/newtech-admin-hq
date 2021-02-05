<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/headerpopup.html";
  require "controller/ProductCtgyController.php";
  
  $cProductCtgyController = new ProductCtgyController([]);
  $cProductCtgyController->popupIndex();
?>