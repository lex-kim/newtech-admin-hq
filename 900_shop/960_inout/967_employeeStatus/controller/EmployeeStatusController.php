<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/class/Options.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/900_shop/960_inout/966_inoutStatus/controller/InOutStatusController.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/900_shop/910_mngt/913_product/class/Product.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/900_shop/960_inout/967_employeeStatus/class/EmployeeStatus.php";

	class EmployeeStatusController extends InOutStatusController{

		function getProductList(){
			$cProduct = new Product();
			$ARR['fullListYN'] = "Y";
			$cProductArray = array();
			$cProductResult = $cProduct->getList($ARR);

			if($cProductResult['status'] == OK){
				if(!empty($cProductResult['data']) && $cProductResult['data'] != HAVE_NO_DATA){
					$cProductArray = $cProductResult['data'];
				}
			}else{
				errorAlert("상품 카테고리 정보를 가져오는데 실패하였습니다.");
			}

			return $cProductArray;
		}

		/** 리스트 */
		function index(){
			$cEmployeeStatus = new EmployeeStatus();
			$cUserArray = $cEmployeeStatus->getData();

			if(!empty($cEmployeeStatus)){
				$categoryArray = $this->getChildCategory();   //InOutStatusController 상속받은 클레스에 있는 함수.
				$cProductArray = $this->getProductList();
			}
			
			include "view/".$this->getPagNM().".php";
		}
	}
	
?>