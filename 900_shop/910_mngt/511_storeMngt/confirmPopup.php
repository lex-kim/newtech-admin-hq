<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/headerpopup.html";
  require "controller/StoreController.php";
  
  if(!isset($_REQUEST['garageID'])){
    errorAlert("잘못된 접근입니다.");
  }
  $cStoreController = new StoreController(["CONTRACT_TYPE"]);
  $cStoreController->popupIndex();
?>
