<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/class/Options.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/900_shop/910_mngt/912_productCategory/class/ProductCtgy.php";

	class InOutStatusController extends Options{

		function getPagNM(){
			if (isset($_REQUEST['ALLS_URI'])) {
				$getUrl = rtrim($_REQUEST['ALLS_URI'], '/');
				$getUrl = filter_var($getUrl, FILTER_SANITIZE_URL);
			}
			$pageNM = explode('.',end(explode('/', $getUrl)))[0];
			return $pageNM;
		}
		
		function getChildCategory(){
			$cProductCtgy = new ProductCtgy();
			$ARR['fullListYN'] = "Y";
			$categoryResult = $cProductCtgy->getList($ARR);
			$childCategory = array();

			if($categoryResult['status'] == OK){
				if(!empty($categoryResult['data']) && $categoryResult['data'] != HAVE_NO_DATA){
					foreach($categoryResult['data'] as $index => $item){
						if(!empty($item['parentCategoryID'])){
							array_push($childCategory, $item); 
						}
					}
					usort($childCategory, function($a, $b) {
						return $a['thisCategory'] > $b['thisCategory'] ? 1 : -1;
					});  
				}
			}else{
				errorAlert("상품 카테고리 정보를 가져오는데 실패하였습니다.");
			}
			return $childCategory;
		}

		/** 리스트 */
		function index(){
			$categoryArray = $this->getChildCategory();
			include "view/".$this->getPagNM().".php";
		}
	}
	
?>