<?
	require_once $_SERVER['DOCUMENT_ROOT']."/900_shop/960_inout/961_stock/controller/StockController.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/900_shop/960_inout/962_release/class/Release.php";

	class ReleaseController extends StockController{

		function popupIndex(){
			$result = $this->getProductList();  //상속으로 상품명 리스트를 받아온다(입고)
			
			if($result['status'] == OK){
				if(empty($result['data']) && $result['data'] != HAVE_NO_DATA){  // 상품명
					$productInfo  = array();
				}
				$productInfo = json_encode($result['data'],true);

				$cRelease = new Release();
				$userResult = $cRelease ->getUserList();

				if($userResult == HAVE_NO_DATA){  // 직원명
					$userInfo  = array();
				}
				$userInfo = json_encode($userResult,true);
			
				include "view/".$this->getPagNM().".php";
			}else{
				errorAlert("상품정보를 가져오는데 실패하였습니다.");
			}

			
		}
	}
?>