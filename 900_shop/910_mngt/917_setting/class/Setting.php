<?php	
	require_once $_SERVER['DOCUMENT_ROOT']."/common/class/ServerApiClass.php";

	class Setting extends ServerApiClass {

		function setData($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=> PUT_SHOP_ENV_INFO    ,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"quotationLimitDays"=>$ARR['ESTIMATE_EXPIRATION_DT'], 
				"quotationSendMessage"=>$ARR['ESTIMATE_SEND_MESSAGE'],                
				"paymentDoneMessage"=>$ARR['ESTIMATE_PAY_MESSAGE'],
				"orderDoneMessage"=>$ARR['ESTIMATE_ORDER_MESSAGE'],
				"pointRedeemMinimumPrice"=>str_replace(",","",$ARR['POINT_USE_PRICE']), 
				"pointUseRedeemYN"=>$ARR['POINT_USE_POLICY'],
				"showStatementPriceYN"=>$ARR['showStatementPriceYN'],
				"sendSmsOrderYN"=>$ARR['sendSmsOrderYN'],
				"sendSmsPaidYN"=>$ARR['sendSmsPaidYN']
			);

			return $this->get("/ec/Shop/Setting", $data);
		}

		function getData(){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=>GET_SHOP_ENV_INFO ,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
			);
			
			return $this->get("/ec/Shop/Setting", $data);

		}

	}

	// 0. 객체 생성
	$cSetting = new Setting();
	// $cBuyMngt->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);
?>
