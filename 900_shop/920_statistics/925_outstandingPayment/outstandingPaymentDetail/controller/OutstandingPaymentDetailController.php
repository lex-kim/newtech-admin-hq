<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/class/Options.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/900_shop/910_mngt/911_buyMngt/class/BuyMngt.php";

	class OutstandingPaymentDetailController extends Options{

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
			if(empty($_REQUEST['partnerID'])){
				errorAlert("올바르지 않은 접근입니다.");
			}

			include "view/".$this->getPagNM().".php";
		}
	}
	
?>