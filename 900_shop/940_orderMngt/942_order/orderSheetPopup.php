<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/headerpopup.html";
  require "controller/OrderController.php";
  
  $cOrderController = new OrderController(["EC_PAYMENT_TYPE","","EC_BILL_TYPE", "EC_DELIVERY_METHOD" ,"EC_BILL_CASH_TYPE", "EC_BILL_CASH_OPTION", "EC_BILL_ISSUE_TYPE",
  "EC_BILL_TAX_OPTION", "EC_BILL_PURPOSE_TYPE", "EC_BILL_RECIPIENT_TYPE"]);
  $cOrderController->orderPopupIndex();
?>
