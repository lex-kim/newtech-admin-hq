<?php	
	require_once $_SERVER['DOCUMENT_ROOT']."/common/class/CommonApi.php";

	class Collect extends CommonApi {

		function getList($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=>GET_STOCK_RETURN_LIST,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"pageNum"=>$ARR['CURR_PAGE'],
				"pageItemCount"=>$ARR['PER_PAGE'],
				"fullListYN"=>(empty($ARR['fullListYN']) ? "N" : "Y"),
				"startDate"=>$ARR['START_DT'],
				"endDate"=>$ARR['END_DT'],
				"dateField"=>$ARR['dateField'],
				"divisionID"=>$ARR['divisionID'],
				"regionName"=>$ARR['regionName'],
				"garageID"=>$ARR['garageID'],
				"contractStatusID"=>$ARR['contractStatusID'],
				"memo"=>$ARR['memo'],
				"keyfield"=>$ARR['keyfield'],
				"keyword"=>$ARR['keyword'],
				
			);

			return $this->get("/ec/Stock/Return", $data);
		}

		function setData($ARR){
			if(empty($ARR['stockID'])){   // 회수생성
				$data = array(
					"accessToken"=>$_SESSION['accessToken'],
					"requestMode"=> POST_STOCK_RETURN  ,
					"hqID"=>HQ_ID,
					"userID"=>$_SESSION['USERID'],  
					"issueUser"=>$ARR['issueUser'],  
					"garageID"=>$ARR['garageID'],                  
					"itemID"=> $ARR['itemID'],
					"unitPrice"=>$ARR['unitPrice'],
					"quantity"=>$ARR['quantity'],
					"issueDate"=>$ARR['issueDate'],
					"memo"=>$ARR['COLLECT_MEMO'],
				);
			}else{    // 회수 취소
				$data = array(
					"accessToken"=>$_SESSION['accessToken'],
					"requestMode"=> PUT_STOCK_RETURN  ,
					"hqID"=>HQ_ID,
					"userID"=>$_SESSION['USERID'],  
					"stockID"=>$ARR['stockID'],                 
					"itemID"=> $ARR['itemID'],
				);
			}

			return $this->get("/ec/Stock/Return", $data);
		}

		function delData($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=>DEL_STOCK_RETURN  ,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"stockID"=>$ARR['stockID'],                 
				"itemID"=> $ARR['itemID'],
			);

			return $this->get("/ec/Stock/Return", $data);
		}
	}

	// 0. 객체 생성
	$cCollect= new Collect();
	// $cBuyMngt->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);
?>
