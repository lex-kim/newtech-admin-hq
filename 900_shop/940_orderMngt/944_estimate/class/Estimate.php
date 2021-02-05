<?php	
	require_once $_SERVER['DOCUMENT_ROOT']."/common/class/CommonApi.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/900_shop/910_mngt/913_product/class/Product.php";

	class Order extends CommonApi {

		function getList($ARR){
			$cProduct = new Product();
			return $cProduct->getList($ARR);
		}

		function setData($ARR, $FILES){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=> (!empty($ARR['PARTNER_ID']) ? PUT_PARTNER_INFO  : POST_PARTNER_INFO),
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"bizTypeCode"=>$ARR['bizTypeCode'],   
				"partnerID"=>$ARR['partnerID'],                
				"partnerTypeCode"=>$ARR['dealCd'],
				"partnerBuyerTypeCode"=>$ARR['EC_BUYER_TYPE'],
				"companyRegID"=>str_replace("-","",$ARR['GW_NUM']),
				"companyName"=>$ARR['GW_NM'],
				"companyBiz"=>$ARR['GW_TYPE'],
				"companyPart"=>$ARR['GW_EVENT'],
				"zipCode"=>$ARR['BIZ_ZONE_CODE'],
				"Address1"=>$ARR['BIZ_ADDRESS_1'],
				"Address2"=>$ARR['BIZ_ADDRESS_2'],
				"phoneNum"=>str_replace("-","",$ARR['GW_PHONE']),
				"faxNum"=>str_replace("-","",$ARR['GW_FAX']),
				"ceoName"=>$ARR['GW_CEONM'],
				"ceoPhoneNum"=>str_replace("-","",$ARR['GW_CEO_PHONE']),
				"chargeName"=>$ARR['GW_FAO_NM'],
				"chargePhoneNum"=>str_replace("-","",$ARR['GW_FAO_PHONE']),
				"chargeDirectPhoneNum"=>str_replace("-","",$ARR['GW_FAO_TEL']),
				"chargeEmail"=>$ARR['GW_FAO_EMAIL'],
				"homepage"=>$ARR['GW_HOMEPAGE'],
				"mainGoods"=>$ARR['GW_GOODS'],
				"memo"=>$ARR['GW_MEMO'],
				"contractStatusCode"=>$ARR['DEAL_STATE'],
				"paymentDueDate"=>$ARR['GW_PAYMENT_DT'],
				"bankCode"=>$ARR['GW_BANK_NM'],
				"bankNum"=>$ARR['GW_BANK_NUM'],
				"bankOwnName"=>$ARR['GW_BANK_USER_NM'],
				"autoOrderYN"=>$ARR['AUTO_ORDER_YN'],
				"autoOrderMethod"=>$ARR['AUTO_ORDER_WAY'],
				"partnerLevelCode"=>$ARR['GW_RATING'],
				"partnerLoginID"=>$ARR['GW_ID'],
				"partnerPasswd"=>$ARR['partnerPasswd'],
				"bizTaxTypeCode"=>$ARR['gwTaxtion'],
				"deliveryFee"=> str_replace(",","",$ARR['deliveryFee']),
				"deliveryFeeFree"=> str_replace(",","",$ARR['deliveryFeeFree']),

			);

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

		function delData($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=>DEL_PARTNER_INFO,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"partnerID"=>$ARR['partnerID'],
			);
			return $this->get("/ec/Quotation/Info", $data);
		}

		function getRankOption(){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=>GET_PARTNER_LEVEL_LIST,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
			);

			$result = $this->get("/ec/Partner/Level", $data);

			if($result['status'] == OK){
				return $result['data'];
			}else{
				return array();
			}
		}

		function isCheckDuplicate($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=>CHECK_EXIST_PARTNER_ID,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"partnerID"=>$ARR['partnerID'],
			);

			return $this->get("/ec/Quotation/Info", $data);
		}

	}

	// 0. 객체 생성
	$cOrder = new Order();
	// $cBuyMngt->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);
?>
