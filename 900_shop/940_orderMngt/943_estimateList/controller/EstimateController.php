<?
	require_once $_SERVER['DOCUMENT_ROOT']."/900_shop/940_orderMngt/943_estimateList/class/Estimate.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/common/class/Options.php";

	class EstimateController extends Options{

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

		function index(){
			$cEstimate = new Estimate();
			$divisionArray = $cEstimate ->getDivisionList();
			if(empty($divisionArray) || $divisionArray  == HAVE_NO_DATA){
				$divisionArray = array();
			}
			
			include "view/".$this->getPagNM().".php";	
		}

		function  popupIndex(){
			$cEstimate = new Estimate();
			$result = $cEstimate->getData($_REQUEST);
			$remainItemCnt = 0;
			if($result['status'] == OK){
				if(empty($result['data']) || $result['data'] == HAVE_NO_DATA){
					$estimateInfo = array();
				}else{
					$estimateInfo = $result['data'];
				}

			
				if(empty($estimateInfo['quotationDetails']) || $estimateInfo['quotationDetails'] == HAVE_NO_DATA){
					$estimateInfo['quotationDetails']  = array();
				}
				
				if(count($estimateInfo['quotationDetails'])> 13){
					$remainItemCnt = 0;
				}else{
					$remainItemCnt = 10 - count($estimateInfo['quotationDetails']);
				}
				
				include "view/".$this->getPagNM().".php";	
			}else{
				echo "견적서 정보를 가져오는데 실패하였습니다.";
				exit();
			}
		}
	}
?>