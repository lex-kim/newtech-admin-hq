<?
	require_once $_SERVER['DOCUMENT_ROOT']."/900_shop/910_mngt/911_buyMngt/class/BuyMngt.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/common/class/Options.php";

	class BuyMngtController extends Options{

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
			$partnerID = $_REQUEST['partnerID'];
			$data = new BuyMngt();

			if(!empty($_REQUEST['partnerID'])){      //수정
				$result = $data->getData($_REQUEST);
				$buyInfo = array();
				if($result['status'] == OK){
					$buyInfo = $result['data']; 
					include "view/".$this->getPagNM().".php";
				}else{
					echo "다시 시도해주세요.";
					exit();
				}
			}else{ // 거래처관리 등록 및 메인리스트
				include "view/".$this->getPagNM().".php";
			}
			
		}
	}
?>