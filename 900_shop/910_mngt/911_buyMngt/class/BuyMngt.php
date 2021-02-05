<?php	
	require_once $_SERVER['DOCUMENT_ROOT']."/common/class/ServerApiClass.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/common/class/UploadImage.php";

	class BuyMngt extends ServerApiClass {

		function getList($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=>GET_PARTNER_INFO_LIST,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"pageNum"=>$ARR['CURR_PAGE'],
				"pageItemCount"=>$ARR['PER_PAGE'],
				"fullListYN"=>(empty($ARR['fullListYN']) ? "N" : "Y"),
				"startDate"=>$ARR['START_DT'],
				"endDate"=>$ARR['END_DT'],
				"dateField"=>$ARR['period'],
				"bizTypeCode"=>$ARR['businessDivision'],
				"bizTaxTypeCode"=>$ARR['taxationDivision'],
				"partnerTypeCode"=>$ARR['deal'],
				"paymentDueDate"=>$ARR['SEARCH_PAYMENT_DATE'],
				"companyBiz"=>$ARR['SEARCH_STORE_BUSINESS'],
				"companyPart"=>$ARR['SEARCH_STORE_EVENT'],
				"companyBankApplyYN"=>$ARR['companyBankApplyYN'],
				"contractStatusCode"=>$ARR['EC_USER_STATUS'],
				"autoOrderYN"=>$ARR['SEARCH_AUTO_ORDERING'],
				"autoOrderMethod"=>$ARR['SEARCH_AUTO_ORDERING_WAY'],
				"AddressText"=>$ARR['SEARCH_AREA'],
				"companyName"=>$ARR['SEARCH_STORE_NM'],
				"companyRegID"=>$ARR['SEARCH_BUSINESS_NUM'],
				"keyfield"=>$ARR['NIC_AES_SEARCH'],
				"keyword"=>$ARR['NIC_AES_INPUT_SEARCH'],
			);

			return $this->get(EC_PARTNER_INFO, $data);
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

			if(!empty($FILES['GW_FILE_NM']['name'])){
				$cImage = new UploadImage($FILES['GW_FILE_NM']);
				$data = array_merge($data, 
								   $cImage->getData(
										"companyCertURI", 
										"companyCertPhysicalFilename", 
										"companyCertOriginFilename"
									));
			}else{
				$data = array_merge($data,
					 UploadImage::setImageValue($ARR['GW_FILE_NM_URI_ORI'],  $ARR['GW_FILE_NM_PHYSICAL_ORI'], $ARR['GW_FILE_NM_ORI'], 
						 "companyCertURI", "companyCertPhysicalFilename", "companyCertOriginFilename"));
			}

			
			if(!empty($FILES['BANK_FILE_NM']['name'])){
				$cImage = new UploadImage($FILES['BANK_FILE_NM']);
				$data = array_merge($data, 
									$cImage->getData(
										"companyBankURI", 
										"companyBankPhysicalFilename", 
										"companyBankOriginFilename"
									));
			}else{
				$data = array_merge($data, 
					UploadImage::setImageValue($ARR['BANK_FILE_NM_URI_ORI'], $ARR['BANK_FILE_NM_PHYSICAL_ORI'], $ARR['BANK_FILE_NM_ORI'], 
						 "companyBankURI", "companyBankPhysicalFilename", "companyBankOriginFilename"));
			}

			return $this->get(EC_PARTNER_INFO, $data);
		}

		function getData($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=>GET_PARTNER_INFO,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"partnerID"=>$ARR['partnerID'],
			);
			
			return $this->get(EC_PARTNER_INFO, $data);

		}

		function delData($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=>DEL_PARTNER_INFO,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"partnerID"=>$ARR['partnerID'],
			);
			return $this->get(EC_PARTNER_INFO, $data);
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
				"requestMode"=>$ARR['reqMode'],
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				
			);

			if(!empty($ARR['partnerID'])){
				$data = array_merge($data , array(
					"partnerID"=>$ARR['partnerID'],
				));
			}else{
				$data = array_merge($data , array(
					"partnerLoginID"=>$ARR['partnerLoginID'],
				));
			}
			return $this->get(EC_PARTNER_INFO, $data);
		}

	}

	// 0. 객체 생성
	$cBuyMngt = new BuyMngt();
	// $cBuyMngt->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);
?>
