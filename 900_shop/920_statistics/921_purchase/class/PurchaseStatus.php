<?php	
	require_once $_SERVER['DOCUMENT_ROOT']."/common/class/CommonApi.php";

	class PurchaseStatus extends CommonApi {

		function getList($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=> GET_STATISTICS_GOODS_LIST,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"pageNum"=>$ARR['CURR_PAGE'],
				"pageItemCount"=>$ARR['PER_PAGE'],
				"fullListYN"=>(empty($ARR['fullListYN']) ? "N" : "Y"),
				"startMonth"=>$ARR['START_DT'],
				"endMonth"=>$ARR['END_DT'],
				"mgmtTypeCode"=>$ARR['mgmtTypeCode'],
				"partnerID"=>$ARR['partnerID'],
				"partnerName"=>$ARR['searchPartnerName'],
				"categoryID"=>$ARR['categoryCode'],
				"goodsName"=>$ARR['searchGoodsName'],
			);
			
			return $this->get("/ec/Statistics/Goods", $data);
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
	$cPurchaseStatus = new PurchaseStatus()
?>
