<?
	require_once $_SERVER['DOCUMENT_ROOT']."/900_shop/970_calculate/971_calculate/class/InOutOrder.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/900_shop/910_mngt/913_product/class/Product.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/common/class/Options.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/900_shop/910_mngt/911_buyMngt/class/BuyMngt.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/900_shop/940_orderMngt/941_orderList/class/OrderList.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/900_shop/910_mngt/916_info/class/Info.php";

	class InOutOrderController extends Options{

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
			$cOrderList = new OrderList();
			$logsticsInfo = $cOrderList->getLogsticsInfo();
			include "view/".$this->getPagNM().".php";
		}

		/** 발주주회 */
		function detailIndex(){
			if(empty($_REQUEST['stockOrderID'])){
				echo "잘못된 접근입니다.";
				exit();
			}

			$cInOutOrder = new InOutOrder();
			$result = $cInOutOrder->getData($_REQUEST['stockOrderID']);
			$stockOrderInfo = array();

			if($result['status'] == OK){
				if(!empty($result['data']) && $result['data'] != HAVE_NO_DATA){
					$stockOrderInfo = $result['data'] ;
				}
			}else{
				echo "발주 정보를 가져오는데 실패하였습니다.";
				exit();
			}

			if(empty($stockOrderInfo['stockOrderDetails']) || $stockOrderInfo['stockOrderDetails'] == HAVE_NO_DATA){
				$stockOrderInfo['stockOrderDetails']  = array();
			}
			
			$remainItemCnt = 13 - count($stockOrderInfo['stockOrderDetails']);
			include "view/".$this->getPagNM().".php";
		}

		/**  발주생성 */
		function popupIndex(){
			$stockOrderTypeName = "일반";
			$stockOrderTypeCode = EC_STOCK_ORDER_TYPE;
			$garageID = $_REQUEST['garageID'];
			$partnerID = $_REQUEST['partnerID'];
			$orderID = "";                     // 원주문아이디
			$partnerListArray = array();       // 매입처들 정보(자동완성을 위해)
			$productArray = array();           // 상품정보
			$partnerArray = array();           // 해당 매입처정보
			$orderInfo = array();              // 해당 원주문 정보
			$companyInfo = array();            // 발주처 정보 

			$cInfo = new Info();
			$companyResult = $cInfo->getData();
			if($companyResult['status'] == OK){
				if(!empty($companyResult['data']) && $companyResult['data'] != HAVE_NO_DATA){
					$companyInfo = $companyResult['data'] ;
				}
			}else{
				echo "발주처 정보를 가져오는데 실패하였습니다.";
				exit();
			}

			$ARR['fullListYN'] = "Y";
			$cBuyMngt = new BuyMngt();
			$partnerListResult = $cBuyMngt->getList($ARR);

			if($partnerListResult['status'] == OK){
				if(!empty($partnerListResult['data']) && $partnerListResult['data'] != HAVE_NO_DATA){
					$partnerListArray = $partnerListResult['data'] ;
				}
			}else{
				echo "매입처정보를 가져오는데 실패하였습니다.";
				exit();
			}
			
			if(!empty($_REQUEST['partnerID'])){
				
				$ARR['partnerID'] = $_REQUEST['partnerID'];
				$partnerResult = $cBuyMngt->getData($ARR);
				if($partnerResult['status'] == OK){
					if(!empty($partnerResult['data']) && $partnerResult['data'] != HAVE_NO_DATA){
						$partnerArray = $partnerResult['data'];
					}
				}else{
					echo "매입처정보를 가져오는데 실패하였습니다.";
					exit();
				}

				$cProduct = new Product();
				$productResult = $cProduct->getList($ARR);
				if($productResult['status'] == OK){
					if(!empty($productResult['data']) && $productResult['data'] != HAVE_NO_DATA){
						$productArray = $productResult['data'];
					}
				}else{
					echo "상품정보를 가져오는데 실패하였습니다.";
					exit();
				}
			}
			
			if(!empty($_REQUEST['orderID'])){
				$stockOrderTypeName = "쇼핑몰";
				$stockOrderTypeCode = EC_STOCK_ORDER_TYPE_SHOPPING;
				$orderID = $_REQUEST['orderID'];

				$cOrderList = new OrderList();
				$orderResult = $cOrderList->getData($orderID);
				if($orderResult['status'] == OK){
					if(!empty($orderResult['data']) && $orderResult['data'] != HAVE_NO_DATA){
						if(!empty($orderResult['data']['orderDetails']) && $orderResult['data']['orderDetails'] != HAVE_NO_DATA){
							foreach($orderResult['data']['orderDetails'] as $index => $item){
								if($item['partnerID'] == $partnerID){
									array_push($orderInfo, $item);
								}
							}
						}
						
					}
				}else{
					echo "원주문 정보를 가져오는데 실패하였습니다.";
					exit();
				}

				$shipName = $orderResult['data']['garageName'];
				$shipZipCode = $orderResult['data']['zipcode'];
				$shipAddr1 = $orderResult['data']['address1'];
				$shipAddr2 = $orderResult['data']['address2'];
				$shipChargeName = $orderResult['data']['orderName'];
				$shipChargePhoneNum = $orderResult['data']['orderPhoneNum'];
			}else{
				$ARR['partnerTypeCode'] = EC_PARTNER_TYPE_BUY;
				$shipName = $companyInfo['companyName'];
				$shipZipCode = $companyInfo['zipCode'];
				$shipAddr1 = $companyInfo['Address1'];
				$shipAddr2 = $companyInfo['Address2'];
				$shipChargeName = $companyInfo['fromChargeName'];
				$shipChargePhoneNum = $companyInfo['fromChargePhoneNum'];
			}

			include "view/".$this->getPagNM().".php";
		}

	}
	
?>