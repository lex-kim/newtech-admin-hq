<?php	
	require $_SERVER['DOCUMENT_ROOT']."/common/class/ServerApiClass.php";
	require $_SERVER['DOCUMENT_ROOT']."/common/class/UploadImage.php";

	class Lost extends ServerApiClass {

		function getList($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=>GET_STOCK_DISPOSAL_LIST ,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"pageNum"=>$ARR['CURR_PAGE'],
				"pageItemCount"=>$ARR['PER_PAGE'],
				"fullListYN"=>(empty($ARR['fullListYN']) ? "N" : "Y"),
				"startDate"=>$ARR['START_DT'],
				"endDate"=>$ARR['END_DT'],
				"dateField"=>$ARR['dateField'],
				"lostDisposalID"=>$ARR['lostDisposalID'],
				"memo"=>$ARR['SEARCH_MEMO'],
				"keyfield"=>$ARR['keyfield'],
				"keyword"=>$ARR['keyword'],
				
			);

			return $this->get("/ec/Stock/Disposal", $data);
		}

		function setData($ARR){
			if(empty($ARR['stockID'])){   // 입고 생성
				$data = array(
					"accessToken"=>$_SESSION['accessToken'],
					"requestMode"=> POST_STOCK_DISPOSAL  ,
					"hqID"=>HQ_ID,
					"userID"=>$_SESSION['USERID'],  
					"issueUser"=>$ARR['issueUser'],  
					"lostDisposalID"=> $ARR['lostDisposalID'],          
					"itemID"=> $ARR['itemID'],
					"unitPrice"=>$ARR['unitPrice'],
					"quantity"=>$ARR['quantity'],
					"issueDate"=>$ARR['issueDate'],
					"memo"=>$ARR['SALESMAN_MEMO'],
				);
			}else{    // 입고 취소
				$data = array(
					"accessToken"=>$_SESSION['accessToken'],
					"requestMode"=> PUT_STOCK_DISPOSAL    ,
					"hqID"=>HQ_ID,
					"userID"=>$_SESSION['USERID'],  
					"stockID"=>$ARR['stockID'],                 
					"itemID"=> $ARR['itemID'],
				);
			}


			return $this->get("/ec/Stock/Disposal", $data);
		}

		function delData($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=>DEL_STOCK_DISPOSAL,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"stockID"=>$ARR['stockID'],                 
				"itemID"=> $ARR['itemID'],
			);

			return $this->get("/ec/Stock/Disposal", $data);
		}
	}

	// 0. 객체 생성
	$cLost= new Lost();
	// $cBuyMngt->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);
?>
