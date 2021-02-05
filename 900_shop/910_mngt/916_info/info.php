<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/headerShop.html";
  require "controller/InfoController.php";

  $cInfo = new InfoController(['BANK_CD', "EC_BILL_TAX_OPTION"]);
  $cInfo->index();
?>