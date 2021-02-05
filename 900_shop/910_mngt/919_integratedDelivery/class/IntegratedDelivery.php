<?php	
	require_once $_SERVER['DOCUMENT_ROOT']."/common/class/ServerApiClass.php";

	class IntegratedDelivery extends ServerApiClass {

		function setData($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=>PUT_DELIVERY_ENV_INFO,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"deliveryFee"=>str_replace(",","",$ARR['deliveryFee']) ,
				"deliveryFeeFree"=>str_replace(",","",$ARR['deliveryFeeFree'])
			);

			
			return $this->get("/ec/Shop/Delivery", $data);
		}

		function getData(){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=>GET_DELIVERY_ENV_INFO,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
			);

			return $this->get("/ec/Shop/Delivery", $data);

		}

	}

	// 0. 객체 생성
	$cIntegratedDelivery = new IntegratedDelivery();
?>
