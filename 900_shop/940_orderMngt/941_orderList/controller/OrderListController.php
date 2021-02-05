<?
	require_once $_SERVER['DOCUMENT_ROOT']."/900_shop/940_orderMngt/941_orderList/class/OrderList.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/900_shop/910_mngt/912_productCategory/class/ProductCtgy.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/900_shop/940_orderMngt/943_estimateList/class/Estimate.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/common/class/Options.php";

	class OrderListController extends Options{

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
			$cEstimate = new Estimate();
			$divisionArray = $cEstimate ->getDivisionList();
			if(empty($divisionArray) || $divisionArray  == HAVE_NO_DATA){
				$divisionArray = array();
			}

			include "view/".$this->getPagNM().".php";
		}


		/** 주문서 조회 */
		function popupIndex(){
			$cOrderList = new OrderList();
			if(empty($_REQUEST['orderID'])){
				echo "잘못된 접근입니다.";
				exit();
			}

			$orderID = $_REQUEST['orderID'];
			$orderResult = $cOrderList->getData($orderID);

			if($orderResult['status'] == OK){
				if($orderResult['data'] == HAVE_NO_DATA || empty($orderResult['data'] ) ){
					$orderInfo = array();
				}else{
					$orderInfo = $orderResult['data'];
				}
			}else{
				echo "주문 정보를 가져오는데 실패하였습니다.";
				exit();
			}
			if(empty($orderInfo['orderDetails']) || $orderInfo['orderDetails'] == HAVE_NO_DATA){
				$orderInfo['orderDetails']  = array();
			}
			
			$remainItemCnt = 8 - count($orderInfo['orderDetails']);

			if($this->getPagNM() == "orderListDetail"){
				$logsticsInfo = $cOrderList->getLogsticsInfo();
			}

			include "view/".$this->getPagNM().".php";
		}

	}
	
?>