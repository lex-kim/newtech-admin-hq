<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/headerpopup.html";
  require "controller/EstimateController.php";
  
  $cEstimateController = new EstimateController([""]);
  $cEstimateController->detailPopup();
?>
