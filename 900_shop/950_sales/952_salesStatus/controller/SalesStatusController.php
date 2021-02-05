<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/class/Options.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/900_shop/950_sales/952_salesStatus/class/SalesStatus.php";

	class SalesStatusController extends Options{

		function getPagNM(){
			if (isset($_REQUEST['ALLS_URI'])) {
				$getUrl = rtrim($_REQUEST['ALLS_URI'], '/');
				$getUrl = filter_var($getUrl, FILTER_SANITIZE_URL);
			}
			$pageNM = explode('.',end(explode('/', $getUrl)))[0];
			return $pageNM;
		}

		function getDivisionArray(){
			$cSalesStatus = new SalesStatus();
			$result = $cSalesStatus->getDivisionList();

			if(empty($result) || $result == HAVE_NO_DATA){
				return array();
			}else{
				return $result;
			}
		}	

		function getRegionArray(){
			$cSalesStatus = new SalesStatus();
			$result = $cSalesStatus->getRegionList();
			if($result['status'] == OK){
				if(empty($result['data']) || $result['data'] == HAVE_NO_DATA){
					return array();
				}else{
					return $result['data'];
				}
			}else{
				errorAlert("지역정보를 가져오는데 실패하였습니다.");
			}	
		}

		/** 리스트 */
		function index(){
			$regionArray = array();
			$paymentArray = array();
			$garageArray = array();
			
			if(!empty($this->optionArray['EC_PAYMENT_TYPE']) && count($this->optionArray['EC_PAYMENT_TYPE'])){
				$paymentArray = $this->optionArray['EC_PAYMENT_TYPE'];
				$cSalesStatus = new SalesStatus();
				$divisionArray = $this->getDivisionArray();
				$ARR['fullListYN'] = "Y";
				$garageArray = $cSalesStatus->getGarageList($ARR);
				// $regionArray = $this->getRegionArray();
			}
			
			include "view/".$this->getPagNM().".php";
		}
	}
	
?>