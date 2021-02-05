<?php	
	require_once $_SERVER['DOCUMENT_ROOT']."/common/class/CommonApi.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/common/class/UploadImage.php";

	class Store extends CommonApi {
		
		function getList($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=> GET_GARAGE_INFO_LIST,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"pageNum"=>$ARR['CURR_PAGE'],
				"pageItemCount"=>$ARR['PER_PAGE'],
				"fullListYN"=>(empty($ARR['fullListYN']) ? "N" : "Y"),
				"startDate"=>$ARR['startDate'],
				"endDate"=>$ARR['endDate'],
				"dateField"=>$ARR['dateField'],
				"divisionID"=>$ARR['divisionID'],
				"garageName"=>$ARR['garageName'],
				"companyRegID"=>$ARR['companyRegID'],
				"loginID"=>$ARR['loginID'],
				"rankID"=>$ARR['rankID'],
				"addressName"=>$ARR['addressName'],
				"contractStatusID"=>$ARR['contractStatusCode'],
				"keyfield"=>$ARR['keyField'],
				"keyword"=>$ARR['keyword'],
			);

			return $this->get("/ec/Garage/Info", $data);
		}

		function getData($garageID){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=> GET_GARAGE_INFO,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"garageID"=>$garageID,
			);

			return $this->get("/ec/Garage/Info", $data);
		}

		function setData($ARR, $FILES){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"password"=>$ARR['MGG_PASSWD_AES'],
				"companyRegID"=>$ARR['MGG_BIZ_ID'],
				"companyName"=>$ARR['MGG_NM'],
				"zipCode"=>$ARR['MGG_ZIP_NEW'],
				"Address1"=>$ARR['MGG_ADDRESS_NEW_1'],
				"Address2"=>$ARR['MGG_ADDRESS_NEW_2'],
				"phoneNum"=>$ARR['MGG_OFFICE_TEL'],
				"faxNum"=>$ARR['MGG_OFFICE_FAX'],
				"companyEmail"=>$ARR['MGG_OFFICE_EMAIL'],
				"ceoName"=>$ARR['MGG_CEO_NM_AES'],
				"ceoPhoneNum"=>$ARR['MGG_CEO_TEL_AES'],
				"chargeName"=>$ARR['MGG_CLAIM_NM_AES'],
				"chargePhoneNum"=>$ARR['MGG_CLAIM_TEL_AES'],
				"rankID"=>$ARR['SLL_ID'],
				"divisionID"=>$ARR['CSD_ID'],
				"gpsLatitude"=>$ARR['MGG_GPS_LATITUDE'],
				"gpsLongitude"=>$ARR['MGG_GPS_LONGITUDE'],
				"memo"=>$ARR['memo'],
			);

			if(empty($ARR['garageID'])){
				$data = array_merge($data, array(
					"requestMode"=> POST_GARAGE_INFO ,
					"loginID" => $ARR['MGG_USERID']
				));
			}else{
				$data = array_merge($data, array(
					"requestMode"=> PUT_GARAGE_INFO ,
					"garageID" => $ARR['garageID']
				));
			}

			if(!empty($FILES['GW_FILE_NM']['name'])){
				$cImage = new UploadImage($FILES['GW_FILE_NM']);
				$data = array_merge($data, 
								   $cImage->getData(
										"imgURI", 
										"imgPhysicalFilename", 
										"imgOriginFilename"
									));
			}else{
				$data = array_merge($data,
					 UploadImage::setImageValue($ARR['GW_FILE_NM_URI_ORI'],  $ARR['GW_FILE_NM_PHYSICAL_ORI'], $ARR['GW_FILE_NM_ORI'], 
						 "imgURI", "imgPhysicalFilename", "imgOriginFilename"));
			}

			return $this->post("/ec/Garage/Info", $data, $FILES);
			
		}

		  
		function delData($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=> DEL_GARAGE_INFO,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"garageID"=>$ARR['garageID'],
			);

			return $this->get("/ec/Garage/Info", $data);
		}

		function updateGrant($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=> PUT_GARAGE_GRANT,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"garageID"=>$ARR['garageID'],
				"divisionID"=>$ARR['divisionID'],
				"rankID"=>$ARR['rankID'],
				"contractStatusID"=>$ARR['contractStatusCode'],
				"memo"=>$ARR['memo'],
			);

			return $this->get("/ec/Garage/Grant", $data);
		}
		function isCheckDuplicate($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=>CHECK_EXIST_GARAGE_LOGIN_ID ,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"loginID"=>$ARR['loginID'],
			);

			return $this->get("/ec/Garage/Info", $data);
		}

	}

	// 0. 객체 생성
	$cStore = new Store();
?>
