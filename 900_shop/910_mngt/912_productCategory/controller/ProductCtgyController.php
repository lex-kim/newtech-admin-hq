<?
	require_once $_SERVER['DOCUMENT_ROOT']."/900_shop/910_mngt/912_productCategory/class/ProductCtgy.php";

	class ProductCtgyController{
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

		/**
		 * 거래처관리 화면 컨트롤러
		 */
		function index(){
			$ctgyID = $_REQUEST['ctgyID'];
			$data = new ProductCtgy();
			include "view/".$this->getPagNM().".php";	
		}

		function popupIndex(){
			$data = new ProductCtgy();
			$ctgyID = $_REQUEST['ctgyID'];
			$ARR['fullListYN'] = "Y";
			$result = $data->getList($ARR);
			$categoryArray = array();
			$ctgyInfo = array();

			if($result['status'] == OK){
				$categoryArray = $result['data'];

				if(!empty($_REQUEST['ctgyID'])){      //수정
					$result = $data->getData($_REQUEST);

					if($result['status'] == OK){
						$ctgyInfo = $result['data'];
						include "view/".$this->getPagNM().".php";
					}else{
						echo "다시 시도해주세요.";
						exit();
					}
				}else{ // 거래처관리 등록 및 메인리스트
					if(!empty($_REQUEST['pCtgyID'])){
						$ctgyInfo['parentCategoryID'] = $_REQUEST['pCtgyID'];
					}
					include "view/".$this->getPagNM().".php";
				}
			}else{
				echo "상위카테고리 가져오는데 실패하였습니다.";
				exit();
			}

			
		}
	}
?>