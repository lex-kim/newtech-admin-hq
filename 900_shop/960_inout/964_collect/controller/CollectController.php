<?
	require_once $_SERVER['DOCUMENT_ROOT']."/900_shop/960_inout/961_stock/controller/StockController.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/900_shop/960_inout/964_collect/class/Collect.php";
	
	class CollectController extends StockController{

		function index(){
			$cCollect =  new Collect();
			$ARR['fullListYN'] = 'Y';
			$garageArray = $cCollect->getGarageList($ARR);
			$divisionArray = $cCollect->getDivisionList();
			
			include "view/".$this->getPagNM().".php";
		}

		function popupIndex(){
			$result = $this->getProductList();  //상속으로 상품명 리스트를 받아온다(입고)
			
			if($result['status'] == OK){
				if(empty($result['data']) && $result['data'] != HAVE_NO_DATA){  // 상품명
					$productInfo  = array();
				}
				$productInfo = json_encode($result['data'],true);

				$cCollect =  new Collect();
				$ARR['fullListYN'] = 'Y';
				$garageArray = $cCollect->getGarageList($ARR);
				$userResult = $cCollect->getUserList();

				
				if(empty($garageArray) && $garageArray == HAVE_NO_DATA){  // 공업사
					$garageInfo  = array();
				}else{
					$garageInfo = json_encode($garageArray, true);
				}

				if(empty($userResult) && $userResult == HAVE_NO_DATA){  // 직원명
					$userInfo  = array();
				}else{
					$userInfo = json_encode($userResult, true);
				}
			
				include "view/".$this->getPagNM().".php";
			}else{
				echo "상품정보를 가져오는데 실패하였습니다.";
				exit();
			}

			
		}
	}
	
?>