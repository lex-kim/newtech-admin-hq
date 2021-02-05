<?
	require_once $_SERVER['DOCUMENT_ROOT']."/900_shop/910_mngt/918_delivery/class/Delivery.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/900_shop/910_mngt/917_setting/class/Setting.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/common/class/Options.php";

	class DeliveryController extends Options{

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

		function index(){
			$deliveryInfo = array();
			$deliveryInfo['deliveryPolicy'] = array();
			include "view/".$this->getPagNM().".php";
		}

		function popupIndex(){
			$deliveryID = $_REQUEST['deliveryID'];
			$data = new Delivery();
			$result = $data->getData($_REQUEST);
			$deliveryInfo = array();

			if($result['status'] == OK){
				if($result['data']['deliveryPolicy'] == HAVE_NO_DATA){
					$result['data']['deliveryPolicy'] = array();
				}

				$deliveryInfo = $result['data']; 
				
				include "view/".$this->getPagNM().".php";
			}else{
				echo "다시 시도해주세요.";
				exit();
			}
		}

		function setting(){
			$cSetting = new Setting();
			$result = $cSetting->getData();
			
			if($result['status'] == OK){
				if(empty($result['data']) ||  $result['data'] == HAVE_NO_DATA){
					echo "코드관리 > 설정에서 값을 저장하셔야 사용하실 수 있습니다.";
					exit();
				}
				$cDelivery = new Delivery();
				$deliveryEnvResult = $cDelivery->getDeliveryEnv();

				if($deliveryEnvResult['status'] == OK){
					$deliveryEnvInfo = array();
					if(!empty($deliveryEnvResult['data']) || $deliveryEnvResult['data'] != HAVE_NO_DATA){
						$deliveryEnvInfo = $deliveryEnvResult['data'];
					}
					include "view/".$this->getPagNM().".php";
				}else{
					echo "통합배송비 정보를 가져오는데 실패하였습니다.";
					exit();
				}
				
			}else{
				echo "설정정보를 가져오는데 실패하였습니다.";
				exit();
			}
			
		}
	}
?>