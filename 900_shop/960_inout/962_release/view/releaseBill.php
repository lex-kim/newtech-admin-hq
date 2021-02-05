<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/headerpopup.html";
  require "controller/ReleaseController.php";

  $cReleaseController = new ReleaseController([]);
  $cReleaseController->popupIndex();
?>