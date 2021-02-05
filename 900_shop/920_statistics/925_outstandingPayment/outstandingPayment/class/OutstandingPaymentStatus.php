<?php	
	require_once $_SERVER['DOCUMENT_ROOT']."/common/class/CommonApi.php";

	class OutstandingPaymentStatus extends CommonApi {

		function getList($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=> GET_STATISTICS_STOCK_ORDER_LIST,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"pageNum"=>$ARR['CURR_PAGE'],
				"pageItemCount"=>$ARR['PER_PAGE'],
				"fullListYN"=>(empty($ARR['fullListYN']) ? "N" : "Y"),
				"startDate"=>$ARR['startDate'],
				"endDate"=>$ARR['endDate'],
				"dateField"=>$ARR['dateField'],
				"businessTypeCode"=>$ARR['businessTypeCode'],
				"partnerTypeCode"=>$ARR['partnerTypeCode'],
				"buyTypeCode"=>$ARR['buyTypeCode'],
				"payDoneYN"=>$ARR['payDoneYN'],
				"partnerID"=>$ARR['partnerID'],
				"partnerName"=>$ARR['searchPartnerName'],
				"keyfield"=>$ARR['keyField'],
				"keyword"=>$ARR['keyword'],
			);

			return $this->get("/ec/Statistics/StockOrder", $data);
		}

		function getData($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=> GET_STATISTICS_GOODS_HEADER,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"startMonth"=>$ARR['startMonth'],
				"endMonth"=>$ARR['endMonth'],
			);

			return $this->get("/ec/Statistics/Goods", $data);
		}
	}

	// 0. 객체 생성
	$cOutstandingPaymentStatus = new OutstandingPaymentStatus()
?>
