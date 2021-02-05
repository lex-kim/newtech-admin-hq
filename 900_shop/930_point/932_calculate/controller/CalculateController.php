<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/class/Options.php";

	class CalculateController extends Options{

		function getPagNM(){
			if (isset($_REQUEST['ALLS_URI'])) {
				$getUrl = rtrim($_REQUEST['ALLS_URI'], '/');
				$getUrl = filter_var($getUrl, FILTER_SANITIZE_URL);
			}
			$pageNM = explode('.',end(explode('/', $getUrl)))[0];
			return $pageNM;
		}

		/** 리스트 */
		function index(){
			include "view/".$this->getPagNM().".php";
		}
	}
	
?>