<?php	
	require_once $_SERVER['DOCUMENT_ROOT']."/common/class/CommonApi.php";

	class Estimate extends CommonApi {
		function setData($ARR){
			if(isset($ARR['quotationID'])){
				$data = array(
					"accessToken"=>$_SESSION['accessToken'],
					"requestMode"=> PUT_QUOTATION_TRANSFER,
					"hqID"=>HQ_ID,
					"userID"=>$_SESSION['USERID'],
					"quotationID"=>$ARR['quotationID'],
					"sendMethodCode"=>$ARR['sendMethodCode'],
					"sendPhoneNum"=>$ARR['SEND_PHONE_NUM'],
				);

				return $this->get("/ec/Quotation/Transfer", $data);
			}
			
		}

		function getList($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=>GET_QUOTATION_LIST,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"pageNum"=>$ARR['CURR_PAGE'],
				"pageItemCount"=>$ARR['PER_PAGE'],
				"fullListYN"=>(empty($ARR['fullListYN']) ? "N" : "Y"),
				"startDate"=>$ARR['START_DT'],
				"endDate"=>$ARR['END_DT'],
				"dateField"=>$ARR['dateField'],
				"quotationID"=>$ARR['quotationID'],
				"quotationStatusCode"=>$ARR['quotationDoneYN'],
				"contractStatusCode"=>$ARR['contractStatusCode'],
				"divisionID"=>$ARR['divisionID'],
				"regionName"=>$ARR['regionName'],
				"garageName"=>$ARR['garageName'],
				"keyfield"=>$ARR['keyfield'],
				"keyword"=>$ARR['keyword'],
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
				"requestMode"=>DEL_QUOTATION,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"quotationID"=>$ARR['quotationID'],
			);

			return $this->get("/ec/Quotation/Info", $data);
		}

	}

	// 0. 객체 생성
	$cEstimate = new Estimate();
	// $cBuyMngt->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);
?>
