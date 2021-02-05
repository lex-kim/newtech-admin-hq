<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/headerpopup.html";
  require $_SERVER['DOCUMENT_ROOT']."/common/indicator.php";
?>

<?php
  require "controller/CollectController.php";

  $cCollectController = new CollectController(['']);
  $cCollectController->popupIndex();
?>