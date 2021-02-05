<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/headerpopup.html";
  require "controller/SaleListController.php";
  
  $cSaleListController = new SaleListController(["EC_BILL_TYPE", "EC_BILL_CASH_TYPE", "EC_BILL_CASH_OPTION", "EC_BILL_ISSUE_TYPE",
            "EC_BILL_TAX_OPTION", "EC_BILL_PURPOSE_TYPE", "EC_BILL_RECIPIENT_TYPE"]);
  $cSaleListController->billPopup();
?>
