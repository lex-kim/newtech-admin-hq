<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/headerpopup.html";
  require $_SERVER['DOCUMENT_ROOT']."/common/indicator.php";
?>


<?php
  require "controller/OrderController.php";
  
  $cOrderController = new OrderController(["EC_DELIVERY_METHOD","EC_PAYMENT_TYPE"]);
  $cOrderController->popupIndex();
?>
