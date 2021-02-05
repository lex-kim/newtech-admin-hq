<?php	
	require_once $_SERVER['DOCUMENT_ROOT']."/common/class/CommonApi.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/900_shop/910_mngt/913_product/class/Product.php";

	class Order extends CommonApi {

		function getList($ARR){
			$cProduct = new Product();
			return $cProduct->getList($ARR);
		}

		function setData($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=> (empty($ARR['quotationID']) ? POST_QUOTATION : PUT_QUOTATION),
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"quotationID"=>$ARR['quotationID'],
				"deliveryMethodCode"=>$ARR['deliveryMethodCode'],
				"paymentCode"=>$ARR['paymentCode'],
				"itemID"=>$ARR['itemID'],
				"optionID"=>$ARR['optionID'],
				"unitPrice"=>str_replace(",","",$ARR['unitPrice']) ,
				"optionPrice"=>str_replace(",","",$ARR['optionPrice']) ,
				"earnPoint"=>str_replace(",","",$ARR['earnPoint']) ,
				"quantity"=>$ARR['quantity'],
				"validationYN"=>"N",
				"salePriceInquiryYN"=>"N",    // 발행 N , 요청은 Y
				"onlyOrderYN"=>$ARR['onlyOrderYN'],
				"quotationNote"=>$ARR['quotationNote'],
			);

			if(empty($ARR['quotationID'])){
				$data = array_merge($data,array(
					"garageID"=>$ARR['garageID'],
					"toCompanyName"=>$ARR['ESTIMATE_STORE_NM'],
					"toReceiverName"=>$ARR['ESTIMATE_NM'],
					"toPhoneNum"=>str_replace("-","",$ARR['ESTIMATE_PHONE']) ,
					"toEmail"=>$ARR['ESTIMATE_EMAIL'],
				));
			}

			return $this->get("/ec/Quotation/Info", $data);
		}

		function getData($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=>GET_QUOTATION,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"quotationID"=>$ARR['quotationID'],
			);
			
			return $this->get("/ec/Quotation/Info", $data);

		}

		/** 배송지 정보 얻기 */
		function getOrderInfo($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=>$ARR['reqMode'],
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"garageID"=>$ARR['garageID'],
			);
			
			return $this->get("/ec/Order/Address", $data);

		}

	}

	// 0. 객체 생성
	$cOrder = new Order();
	// $cBuyMngt->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);
?>
