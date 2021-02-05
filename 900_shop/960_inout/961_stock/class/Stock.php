<?php	
	require $_SERVER['DOCUMENT_ROOT']."/common/class/ServerApiClass.php";
	require $_SERVER['DOCUMENT_ROOT']."/common/class/UploadImage.php";

	class Stock extends ServerApiClass {

		function getList($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=>GET_STOCK_IN_LIST ,
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
				"keyfield"=>$ARR['keyField'],
				"keyword"=>$ARR['keyword'],
				
			);

			return $this->get("/ec/Stock/In", $data);
		}

		function setData($ARR, $FILE){
			if(empty($ARR['stockID'])){   
				if(empty($FILE)){ // 입고 생성
					$data = array(
						"accessToken"=>$_SESSION['accessToken'],
						"requestMode"=> POST_STOCK_IN  ,
						"hqID"=>HQ_ID,
						"userID"=>$_SESSION['USERID'],  
						"issueUser"=>$_SESSION['USERID'],                 
						"itemID"=> $ARR['itemID'],
						"unitPrice"=>$ARR['unitPrice'],
						"quantity"=>$ARR['quantity'],
						"issueDate"=>$ARR['issueDate'],
						"memo"=>$ARR['STOCK_MEMO'],
					);
					return $this->get("/ec/Stock/In", $data);
				}else{ // 엑셀 업로드
					$data = array(
						"accessToken"=>$_SESSION['accessToken'],
						"requestMode"=> PUT_STOCK_IN_EXCEL  ,
						"hqID"=>HQ_ID,
						"userID"=>$_SESSION['USERID'],  
						"uploadCSV"=> curl_file_create($FILE['stockCSV']['tmp_name'], $FILE['stockCSV']['type'], $FILE['stockCSV']['name']),
					);
					
				}

				return $this->post("/ec/Stock/In", $data, $FILE);
			}else{    // 입고 취소
				$data = array(
					"accessToken"=>$_SESSION['accessToken'],
					"requestMode"=> PUT_STOCK_IN  ,
					"hqID"=>HQ_ID,
					"userID"=>$_SESSION['USERID'],  
					"stockID"=>$ARR['stockID'],                 
					"itemID"=> $ARR['itemID'],
				);
				
				return $this->get("/ec/Stock/In", $data);
			}
			

		
		}

		function delData($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=>DEL_STOCK_IN,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"stockID"=>$ARR['stockID'],                 
				"itemID"=> $ARR['itemID'],
			);

			return $this->get("/ec/Stock/In", $data);
		}
	}

	// 0. 객체 생성
	$cStock= new Stock();
	// $cBuyMngt->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);
?>
