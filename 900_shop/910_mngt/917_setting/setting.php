<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/headerShop.html";
  require "controller/SettingController.php";

  $cSetting = new SettingController(['BANK_CD']);
  $cSetting->index();
?>