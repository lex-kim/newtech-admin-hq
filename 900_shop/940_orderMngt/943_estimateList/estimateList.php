<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/headerShop.html";
  require "controller/EstimateController.php";
  
  $cEstimateController = new EstimateController(["CONTRACT_TYPE", "EC_QUOTATION_STATUS"]);
  $cEstimateController->index();
?>
