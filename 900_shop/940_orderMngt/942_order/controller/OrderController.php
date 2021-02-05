<?
	require_once $_SERVER['DOCUMENT_ROOT']."/900_shop/940_orderMngt/942_order/class/Order.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/900_shop/910_mngt/912_productCategory/class/ProductCtgy.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/900_shop/910_mngt/916_info/class/Info.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/900_shop/940_orderMngt/943_estimateList/class/Estimate.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/common/class/Options.php";

	class OrderController extends Options{

		function getPagNM(){
			if (isset($_REQUEST['ALLS_URI'])) {
				$getUrl = rtrim($_REQUEST['ALLS_URI'], '/');
				$getUrl = filter_var($getUrl, FILTER_SANITIZE_URL);
			}
			$pageNM = explode('.',end(explode('/', $getUrl)))[0];
			return $pageNM;
		}
		
		function index(){
			include "view/".$this->getPagNM().".php";
		}

		function popupIndex(){
			$quotationInfo = array();
			if(!empty($_REQUEST['quotationID'])){
				$quotationID = $_REQUEST['quotationID'];
				$cEstimate = new Estimate();
				$estimateResult = $cEstimate->getData($_REQUEST);

				if($estimateResult['status'] == OK){
					if(!empty($estimateResult['data']) && $estimateResult['data'] != HAVE_NO_DATA){
						$quotationInfo  = $estimateResult['data'];
						if($quotationInfo['quotationDetails'] == HAVE_NO_DATA){
							$quotationInfo['quotationDetails'] = array();
						}
					}
					
				}else{
					echo "해당 견적서 정보를 가져오는데 실패하였습니다.";
					exit();
				}
			}

			
			$cProductCtgy = new ProductCtgy();
			$cOrder = new Order();
			$ARR['fullListYN'] = "Y";
			$ARR['SEARCH_EXPOSURE_YN'] = "Y";

			$cProductCtgyResult = $cProductCtgy ->getList($ARR);

			if($cProductCtgyResult['status'] == OK) {
				if(empty($cProductCtgyResult['data']) || $cProductCtgyResult['data'] == HAVE_NO_DATA){
					$categoryList = array();
				}else{
					$categoryList = $cProductCtgyResult['data'];
				}
				
				$ARR['fullListYN'] = 'Y';
				$garageArray = $cOrder->getGarageList($ARR);
				if(empty($garageArray) && $garageArray == HAVE_NO_DATA){  // 공업사
					$garageInfo  = array();
				}else{
					$garageInfo = $garageArray;
				}

				include "view/".$this->getPagNM().".php";
			}else{
				echo "상품카테고리 정보를 가져오는데 실패하였습니다.";
				exit();
			}	
		}

		/** 주문서 작성 */
		function orderPopupIndex(){
			if(empty($_REQUEST['quotationID']) || empty($_REQUEST['garageID'])){
				echo "잘못된 접근입니다.";
				exit();
			}
			$quotationID = $_REQUEST['quotationID'];
			$garageID = $_REQUEST['garageID'];

			$cInfo = new Info();
			$cInfoResult = $cInfo -> getData();
			if($cInfoResult['status'] == OK){
				if($cInfoResult['data'] == HAVE_NO_DATA || empty($cInfoResult['data'] ) ){
					$cShoppingInfo = array();
				}else{
					$cShoppingInfo = $cInfoResult['data'];
				}
			}else{
				echo "공업사 정보를 가져오는데 실패하였습니다.";
				exit();
			}

			$cOrder = new Order();
			$pointInfo = $cOrder->getPointInfo($garageID);
			$ARR['garageID'] = $garageID;
			$garageResult = $cOrder->getGargeInfo($ARR);
			if($garageResult['status'] == OK){
				if($garageResult['data'] == HAVE_NO_DATA || empty($garageResult['data'] ) ){
					$garageInfo = array();
				}else{
					$garageInfo = $garageResult['data'];
				}
			}else{
				echo "공업사 정보를 가져오는데 실패하였습니다.";
				exit();
			}

			$ARR['quotationID'] = $quotationID;
			$orderResult = $cOrder->getData($ARR);
			if($orderResult['status'] == OK){
				if($orderResult['data'] == HAVE_NO_DATA || empty($orderResult['data'] ) ){
					$orderInfo = array();
				}else{
					$orderInfo = $orderResult['data'];
				}
			}else{
				echo "견적서 상품 정보를 가져오는데 실패하였습니다.";
				exit();
			}

			$savePoint = 0;
			if($pointInfo['pointUseRedeemYN'] == "Y"){
				$savePoint = $orderInfo['totalPoint'];
			}

			include "view/".$this->getPagNM().".php";
		}
	}
	
?>