<?
	require_once $_SERVER['DOCUMENT_ROOT']."/900_shop/910_mngt/913_product/class/Product.php";
	
	
	class EstimateController{

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

		function detailPopup(){
			$cProduct = new Product();
			$result = $cProduct -> getData($_REQUEST);

			if($result['status'] == OK){
				$productInfo = $result['data'];
				
				include "view/".$this->getPagNM().".php";
			}else{
				echo "해당 상품정보를 가져오는데 실패하였습니다.";
				exit();
			}
			
		}
		
	}
	
?>