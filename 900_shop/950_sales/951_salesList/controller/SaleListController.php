<?php
	require_once $_SERVER['DOCUMENT_ROOT']."/900_shop/950_sales/951_salesList/class/SaleList.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/900_shop/910_mngt/913_product/class/Product.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/common/class/Options.php";

	class SaleListController extends Options{

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

		function getProductList(){
			$cProduct = new Product();
			$ARR['fullListYN'] = 'Y';
			$productResult = $cProduct->getList($ARR);
			if($productResult['status'] == OK){                     // 상품리스트
				if($productResult['data'] == HAVE_NO_DATA || empty($productResult['data']) ){
					return array();
				}else{
					return $productResult['data'];
				}
			}else{
				echo "상품정보를 가져오는데 실패하였습니다.";
				exit();
			}
		}

		function getSalesInfo(){
			$cSaleList = new SaleList();
			$cSaleResult = $cSaleList->getData($_REQUEST['salesID']);

			if($cSaleResult['status'] == OK){
				if($cSaleResult['data'] == HAVE_NO_DATA || empty($cSaleResult['data']) ){
					return array();
				}else{
					return $cSaleResult['data'];
				}
			}else{
				echo "해당 일반판매정보를 가져오는데 실패하였습니다.";
				exit();
			}
		}

		function getGarageInfo($ARR){
			$cSaleList = new SaleList();
			$garageResult = $cSaleList->getGargeInfo($ARR);
			
			if($garageResult['status'] == OK){
				if(empty($garageResult['data']) || $garageResult['data'] == HAVE_NO_DATA){
					return array();
				}else{
					return $garageResult['data'];
				}
			}else{
				echo "해당 공업사 정보를 가져오는데 실패하였습니다.";
				exit();
			}
		}
		
		function index(){
			$cSaleList = new SaleList();
			$divisionArray = $cSaleList ->getDivisionList();
			if(empty($divisionArray) || $divisionArray  == HAVE_NO_DATA){
				$divisionArray = array();
			}
			include "view/".$this->getPagNM().".php";
		}

		function popupIndex(){
			$salesArray = array();

			$cSaleList = new SaleList();
			$ARR['fullListYN'] = 'Y';
			$garageArray = $cSaleList->getGarageList($ARR);
			if(empty($garageArray) && $garageArray == HAVE_NO_DATA){  // 공업사
				$garageInfo  = array();
			}else{
				$garageInfo = json_encode($garageArray,true);
			}

			$userResult = $cSaleList ->getUserList();
			if(empty($userResult) && $userResult == HAVE_NO_DATA){  // 직원
				$userInfo  = array();
			}else{
				$userInfo = json_encode($userResult,true);
			}

			
			$productInfo = $this->getProductList();  // 상품

			if(!empty($_REQUEST['salesID'])){        // 수정
				$cSalesInfo = $this->getSalesInfo();
			}

			include "view/".$this->getPagNM().".php";
		}

		function billPopup(){
			if(empty($_REQUEST['garageID']) || empty($_REQUEST['salesID'])){
				echo "잘못된 접근 입니다.";
				exit();
			}else{
				$cSaleList = new SaleList();
				$ARR['garageID'] = $_REQUEST['garageID'];

				$garageInfo = $this->getGarageInfo($ARR);

				include "view/".$this->getPagNM().".php";
			}
			
		}
	}
?>