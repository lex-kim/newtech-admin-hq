<?
	require_once $_SERVER['DOCUMENT_ROOT']."/900_shop/910_mngt/919_integratedDelivery/class/IntegratedDelivery.php";

	class IntegratedDeliveryController{

		/**
		 * 리퀘스트로 얻어온 url을 통해 이동할 페이지값을 가져온다.
		 */
		function getPagNM(){
			if (isset($_REQUEST['ALLS_URI'])) {
				$getUrl = rtrim($_REQUEST['ALLS_URI'], '/');
				$getUrl = filter_var($getUrl, FILTER_SANITIZE_URL);
			}
			$pageNM = explode('.',end(explode('/', $getUrl)))[0];
			return $pageNM;
		}

		/**
		 * 거래처관리 화면 컨트롤러
		 */
		function index(){
			$cIntegratedDelivery = new IntegratedDelivery();
			$result = $cIntegratedDelivery->getData();
			$deliveryEnvInfo = array();

			if($result['status'] == OK){
				if($result['data'] != HAVE_NO_DATA){
					$deliveryEnvInfo = $result['data'];
				}
				include "view/".$this->getPagNM().".php";
			}else{
				echo "설정 정보를 가져오는데 실패하였습니다.";
				exit();
			}
			
			
		}
	}
?>