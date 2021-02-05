<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/class/Options.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/900_shop/910_mngt/911_buyMngt/class/BuyMngt.php";

	class OutstandingPaymentStatusController extends Options{

		function getPagNM(){
			if (isset($_REQUEST['ALLS_URI'])) {
				$getUrl = rtrim($_REQUEST['ALLS_URI'], '/');
				$getUrl = filter_var($getUrl, FILTER_SANITIZE_URL);
			}
			$pageNM = explode('.',end(explode('/', $getUrl)))[0];
			return $pageNM;
		}

		/** 리스트 */
		function index(){
			$cBuyMngt = new BuyMngt();
			$ARR['fullListYN'] = "Y";
			$result = $cBuyMngt -> getList($ARR);

			if($result['status'] == OK){
				if(!empty($result['data'] ) && $result['data'] != HAVE_NO_DATA){
					$buyMngtArray = $result['data'];
				}
			}else{
				errorAlert("매입처 정보를 가져오는데 실패하였습니다.");
			}

			include "view/".$this->getPagNM().".php";
		}
	}
	
?>