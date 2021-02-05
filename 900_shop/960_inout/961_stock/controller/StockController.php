<?
	require_once $_SERVER['DOCUMENT_ROOT']."/900_shop/910_mngt/913_product/class/Product.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/common/class/Options.php";

	class StockController extends Options{
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
			$ARR['fullListYN'] = "Y";
			$ARR['sortAcendentByNameYN'] = "Y";

			return $cProduct->getList($ARR);
		}

		function index(){
			include "view/".$this->getPagNM().".php";	
		}

		function popupIndex(){
			$result = $this->getProductList();
			if($result['status'] == OK){
				if(empty($result['data']) && $result['data'] != HAVE_NO_DATA){
					$productInfo  = array();
				}
				$productInfo = json_encode($result['data'],true);
				
				include "view/".$this->getPagNM().".php";
			}else{
				echo "상품정보를 가져오는데 실패하였습니다.";
				exit();
			}

			
		}
	}
?>