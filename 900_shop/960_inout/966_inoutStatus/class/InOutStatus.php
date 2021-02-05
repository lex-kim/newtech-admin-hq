<?php	
	require_once $_SERVER['DOCUMENT_ROOT']."/common/class/CommonApi.php";

	class InOutStatus extends CommonApi {

		function getList($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=> GET_STOCK_STATUS_IO_LIST,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"pageNum"=>$ARR['CURR_PAGE'],
				"pageItemCount"=>$ARR['PER_PAGE'],
				"fullListYN"=>(empty($ARR['fullListYN']) ? "N" : "Y"),
				"mgmtTypeCode"=>$ARR['mgmtTypeCode'],
				"categoryCode"=>$ARR['categoryCode'],
				"sortAsc"=>$ARR['sortAsc'],
				"sortField"=>$ARR['sortField'],
			);

			return $this->get("/ec/Stock/StatusIO", $data);
		}
	}

	// 0. 객체 생성
	$cInOutStatus = new InOutStatus()
?>
