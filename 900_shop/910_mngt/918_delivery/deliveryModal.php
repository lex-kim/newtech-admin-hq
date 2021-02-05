<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/headerpopup.html";
  require "controller/DeliveryController.php";
  
  $cDeliveryController = new DeliveryController(['EC_DELIVERY_FEE', 'EC_DELIVERY_FEE_TYPE', 'EC_DELIVERY_METHOD']);

  if(empty($_REQUEST['deliveryID'])){
    $cDeliveryController->index();
  }else{
    $cDeliveryController->popupIndex();
  }
  
?>
