<?php	
	require_once $_SERVER['DOCUMENT_ROOT']."/common/class/CommonApi.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/common/class/UploadImage.php";

	class Release extends CommonApi {

		function getList($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=>GET_STOCK_OUT_LIST ,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"pageNum"=>$ARR['CURR_PAGE'],
				"pageItemCount"=>$ARR['PER_PAGE'],
				"fullListYN"=>(empty($ARR['fullListYN']) ? "N" : "Y"),
				"startDate"=>$ARR['START_DT'],
				"endDate"=>$ARR['END_DT'],
				"dateField"=>$ARR['dateField'],
				"memo"=>$ARR['SEARCH_MEMO'],
				"categoryName"=>$ARR['SEARCH_CATEGORY'],
				"keyfield"=>$ARR['keyfield'],
				"keyword"=>$ARR['keyword'],
				
			);
			
			return $this->get("/ec/Stock/Out", $data);
		}

		function setData($ARR){
			if(empty($ARR['stockID'])){   // 출고생성
				$data = array(
					"accessToken"=>$_SESSION['accessToken'],
					"requestMode"=> POST_STOCK_OUT  ,
					"hqID"=>HQ_ID,
					"userID"=>$_SESSION['USERID'],  
					"issueUser"=>$ARR['issueUser'],                 
					"itemID"=> $ARR['itemID'],
					"unitPrice"=>$ARR['unitPrice'],
					"quantity"=>$ARR['quantity'],
					"issueDate"=>$ARR['issueDate'],
					"memo"=>$ARR['RELEASE_MEMO'],
				);
			}else{    // 출고 취소
				$data = array(
					"accessToken"=>$_SESSION['accessToken'],
					"requestMode"=> PUT_STOCK_OUT  ,
					"hqID"=>HQ_ID,
					"userID"=>$_SESSION['USERID'],  
					"stockID"=>$ARR['stockID'],                 
					"itemID"=> $ARR['itemID'],
				);
			}

			return $this->get("/ec/Stock/Out", $data);
		}

		function delData($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=>DEL_STOCK_OUT,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"stockID"=>$ARR['stockID'],                 
				"itemID"=> $ARR['itemID'],
			);

			return $this->get("/ec/Stock/Out", $data);
		}
	}

	// 0. 객체 생성
	$cRelease= new Release();
	// $cBuyMngt->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);
?>
