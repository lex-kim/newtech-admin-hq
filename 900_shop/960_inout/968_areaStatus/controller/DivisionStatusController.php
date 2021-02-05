<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/class/Options.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/900_shop/960_inout/967_employeeStatus/controller/EmployeeStatusController.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/900_shop/910_mngt/913_product/class/Product.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/900_shop/960_inout/968_areaStatus/class/DivisionStatus.php";

	class DivisionStatusController extends EmployeeStatusController{

		/** 리스트 */
		function index(){
			$cDivisionStatus = new DivisionStatus();
			$cDivisionArray = $cDivisionStatus->getData();

			if(!empty($cDivisionStatus)){
				$categoryArray = $this->getChildCategory();   // 상품카테고리  InOutStatusController 상속받은 클레스에 있는 함수.
				$cProductArray = $this->getProductList();     // 상품 리스트  EmployeeStatusController에 있는 함수
			}

			include "view/".$this->getPagNM().".php";
		}
	}
	
?>