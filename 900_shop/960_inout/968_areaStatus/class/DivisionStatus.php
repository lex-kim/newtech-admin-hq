<?php	
	require_once $_SERVER['DOCUMENT_ROOT']."/common/class/CommonApi.php";

	class DivisionStatus extends CommonApi {

		function getList($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=> GET_STOCK_STATUS_REGION_LIST ,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"pageNum"=>$ARR['CURR_PAGE'],
				"pageItemCount"=>$ARR['PER_PAGE'],
				"fullListYN"=>(empty($ARR['fullListYN']) ? "N" : "Y"),
				"startDate"=>$ARR['START_DT'],
				"endDate"=>$ARR['END_DT'],
				"dateField"=>$ARR['dateField'],
				"mgmtTypeCode"=>$ARR['mgmtTypeCode'],
				"categoryCode"=>$ARR['categoryCode'],
				"goodsName"=>$ARR['goodsName'],
				"itemID"=>$ARR['itemID'],
			);

			return $this->get("/ec/Stock/StatusRegion", $data);
		}

		/** 해당 지역 가져오기 */
		function getData(){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=> GET_STOCK_STATUS_REGION_HEADER  ,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
			);

			$result =  $this->get("/ec/Stock/StatusRegion", $data);

			if($result['status'] == OK){
				if(empty($result['data']) || $result['data'] == HAVE_NO_DATA){
					return null;
				}else{
					return $result['data'];
				}
			}else{
				errorAlert("지역데이터를 가져오는데 실패하였습니다.".$result['result']);
			}
		}
	}

	// 0. 객체 생성
	$cDivisionStatus = new DivisionStatus();
?>
