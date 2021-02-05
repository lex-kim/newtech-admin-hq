<?php	
	require_once $_SERVER['DOCUMENT_ROOT']."/common/class/CommonApi.php";

	class SalesStatus extends CommonApi {

		function getList($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=> GET_SALES_STATUS_LIST,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"pageNum"=>$ARR['CURR_PAGE'],
				"pageItemCount"=>$ARR['PER_PAGE'],
				"fullListYN"=>(empty($ARR['fullListYN']) ? "N" : "Y"),
				"startDate"=>$ARR['START_DT'],
				"endDate"=>$ARR['END_DT'],
				"dateField"=>$ARR['dateField'],
				"divisionID"=>$ARR['divisionID'],
				"regionID"=>$ARR['regionID'],
				"regionName"=>$ARR['regionName'],
				"garageID"=>$ARR['garageID'],
				"garageName"=>$ARR['garageName'],
				"contractStatusID"=>$ARR['contractStatusID'],
			);

			return $this->get("/ec/Sales/Status", $data);
		}
	}

	// 0. 객체 생성
	$cSalesStatus = new SalesStatus();
?>
