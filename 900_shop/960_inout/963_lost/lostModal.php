<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/headerpopup.html";
  require "controller/LostController.php";

  $cLostController = new LostController(['EC_DISPOSAL_STATUS']);
  $cLostController->popupIndex();
?>