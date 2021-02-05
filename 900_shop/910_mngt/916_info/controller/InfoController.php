<?
	require_once $_SERVER['DOCUMENT_ROOT']."/900_shop/910_mngt/916_info/class/Info.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/common/class/Options.php";

	class InfoController extends Options{

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
			$data = new Info();
			$cInfo = array();
			$result = $data->getData();
			
			if($result['status'] == OK){
				// $cInfo = json_decode($result['data'], true);

				// if(json_last_error() == 3){ //Control character error, possibly incorrectly encoded  // 0이 정상
				// 	$jsonData = preg_replace('/[[:cntrl:]]/', '', $result['data']);
				// 	$cInfo = json_decode($jsonData, true);
				// }
				$cInfo = $result['data'];
				include "view/".$this->getPagNM().".php";
			}else{
				echo "쇼핑몰 정보를 가져오는데 실패하였습니다.";
				exit();
			}
			
			
		}
	}
?>