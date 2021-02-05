<?php	
	require_once $_SERVER['DOCUMENT_ROOT']."/common/class/ServerApiClass.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/common/class/UploadImage.php";

	class Info extends ServerApiClass {

		function setData($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=> PUT_SHOP_COMPANY_INFO  ,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"companyRegID"=>str_replace("-","",$ARR['BIZ_NUM']), 
				"companyName"=>$ARR['GW_STORE_NM'],                
				"ceoName"=>$ARR['GW_NM'],
				"zipCode"=>$ARR['MIT_NEW_ZIP'],
				"Address1"=>$ARR['MIT_NEW_ADDRESS1_AES'],
				"Address2"=>$ARR['MIT_NEW_ADDRESS2_AES'],
				"companyBiz"=>$ARR['GW_CONDITION'],
				"companyPart"=>$ARR['GW_ITEM'],
				"phoneNum"=>str_replace("-","",$ARR['GW_TEL']),
				"faxNum"=>str_replace("-","",$ARR['GW_FAX']),
				"companyEmail"=>$ARR['GW_EMAIL'],
				"bankCode"=>$ARR['GW_BANK_NM'],
				"bankNum"=>$ARR['GW_BANK_NUM'],
				"bankOwnName"=>$ARR['GW_BANK_OWN'],
				"ecRegNum"=>$ARR['GW_SALE_NUM'],
				"taxTypeCode"=>$ARR['taxTypeCode'],
				"fromChargeName"=>$ARR['fromChargeName'],
				"fromChargePhoneNum"=>$ARR['fromChargePhoneNum'],
				"fromChargeTel"=>$ARR['fromChargeTel'],
				"fromChargeDepartment"=>$ARR['fromChargeDepartment'],
				"fromChargeEmail"=>$ARR['fromChargeEmail'],
				"billSmsYN"=>$ARR['billSmsYN'],
				"stockZipCode"=>$ARR['STOCK_NEW_ZIP'],
				"stockAddress1"=>$ARR['STOCK_NEW_ADDRESS1_AES'],
				"stockAddress2"=>$ARR['STOCK_NEW_ADDRESS2_AES'],	
			);

			return $this->get("/ec/Shop/Company", $data);
		}

		function getData(){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=>GET_SHOP_COMPANY_INFO ,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
			);
			
			return $this->get("/ec/Shop/Company", $data);

		}

	}

	// 0. 객체 생성
	$cInfo = new Info();
	// $cBuyMngt->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);
?>
