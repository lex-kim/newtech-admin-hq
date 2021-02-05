<?php
	require_once $_SERVER['DOCUMENT_ROOT']."/900_shop/950_sales/951_salesList/class/SaleList.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/900_shop/910_mngt/911_buyMngt/class/BuyMngt.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/900_shop/910_mngt/511_storeMngt/class/Store.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/common/class/Options.php";

	class StoreController extends Options{

		function getPagNM(){
			if (isset($_REQUEST['ALLS_URI'])) {
				$getUrl = rtrim($_REQUEST['ALLS_URI'], '/');
				$getUrl = filter_var($getUrl, FILTER_SANITIZE_URL);
			}
			$pageNM = explode('.',end(explode('/', $getUrl)))[0];
			return $pageNM;
		}

		
		function index(){
			$cSaleList = new SaleList();
			$divisionArray = $cSaleList ->getDivisionList();
			if(empty($divisionArray) || $divisionArray  == HAVE_NO_DATA){
				$divisionArray = array();
			}

			$cBuyMngt = new BuyMngt();
			$rankArray = $cBuyMngt ->getRankOption();
			if(empty($rankArray) || $rankArray  == HAVE_NO_DATA){
				$rankArray = array();
			}
			include "view/".$this->getPagNM().".php";
		}

		function popupIndex(){
			$garageArray = array();

			if(isset($_REQUEST['garageID'])){
				$cStore = new Store();
				$result = $cStore -> getData($_REQUEST['garageID']);
				
				if($result['status'] == OK){
					if(!empty($result['data']) && $result['data'] != HAVE_NO_DATA){
						$garageArray = $result['data'];
					}
				}else{
					errorAlert("해당 업체정보를 가져오는데 실패하였습니다.");
				}
			}
			
			
			$cBuyMngt = new BuyMngt();
			$rankArray = $cBuyMngt ->getRankOption();
			if(empty($rankArray) || $rankArray  == HAVE_NO_DATA){
				$rankArray = array();
			}

			$cSaleList = new SaleList();
			$divisionArray = $cSaleList ->getDivisionList();
			if(empty($divisionArray) || $divisionArray  == HAVE_NO_DATA){
				$divisionArray = array();
			}

			include "view/".$this->getPagNM().".php";
		}

		function grantPopup(){
			$cBuyMngt = new BuyMngt();
			$rankArray = $cBuyMngt ->getRankOption();
			if(empty($rankArray) || $rankArray  == HAVE_NO_DATA){
				$rankArray = array();
			}

			$cSaleList = new SaleList();
			$divisionArray = $cSaleList ->getDivisionList();
			if(empty($divisionArray) || $divisionArray  == HAVE_NO_DATA){
				$divisionArray = array();
			}

			include "view/".$this->getPagNM().".php";
		}
	}
?>