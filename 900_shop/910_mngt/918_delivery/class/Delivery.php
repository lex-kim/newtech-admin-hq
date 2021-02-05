<?php	
	require_once $_SERVER['DOCUMENT_ROOT']."/common/class/ServerApiClass.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/common/class/UploadImage.php";

	class Delivery extends ServerApiClass {

		function getList($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=>GET_GOODS_DELIVERY_LIST,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"pageNum"=>$ARR['CURR_PAGE'],
				"pageItemCount"=>$ARR['PER_PAGE'],
				"fullListYN"=>(empty($ARR['fullListYN']) ? "N" : "Y"),
				"startDate"=>$ARR['START_DT'],
				"endDate"=>$ARR['END_DT'],
				"dateField"=>$ARR['period'],
				"deliveryPolicyName"=>$ARR['SEARCH_DELIVERY_NM'],
			);

			return $this->get("/ec/Goods/Delivery", $data);
		}

		function setData($ARR, $FILES){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=> (!empty($ARR['deliveryID']) ? PUT_GOODS_DELIVERY  : POST_GOODS_DELIVERY),
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"deliveryPolicyID"=>$ARR['deliveryPolicyID'],   
				"deliveryPolicyName"=>$ARR['deliveryPolicyName'],                
				"payTypeCode"=>$ARR['EC_DELIVERY_FEE'],
				"priceTypeCode"=>$ARR['EC_DELIVERY_FEE_TYPE'],
				"deliveryMethodCode"=>$ARR['EC_DELIVERY_METHOD'],
				"deliveryPrice"=>str_replace(",","",$ARR['deliveryPrice']),
				"startNumber"=>$ARR['startNumber'],
				"endNumber"=>$ARR['endNumber'],
				"sectionPrice"=>$ARR['sectionPrice'],
				"moreThanYN"=>$ARR['moreThanYN'],
			);

		
			return $this->get("/ec/Goods/Delivery", $data);
		}

		function getData($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=>GET_GOODS_DELIVERY,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"deliveryPolicyID"=>$ARR['deliveryID'],
			);
			
			return $this->get("/ec/Goods/Delivery", $data);

		}

		function delData($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=>DEL_GOODS_DELIVERY,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"deliveryPolicyID"=>$ARR['deliveryPolicyID'],
			);

			return $this->get("/ec/Goods/Delivery", $data);
		}

		function setDeliveryEnv($ARR){
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

		function getDeliveryEnv(){
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
	$cDelivery = new Delivery();
	// $cBuyMngt->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);
?>
