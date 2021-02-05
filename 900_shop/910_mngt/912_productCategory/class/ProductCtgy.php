<?php	
	require_once $_SERVER['DOCUMENT_ROOT']."/common/class/ServerApiClass.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/common/class/UploadImage.php";

	class ProductCtgy extends ServerApiClass {

		function getList($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=>GET_GOODS_CATEGORY_LIST ,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"pageNum"=>$ARR['CURR_PAGE'],
				"pageItemCount"=>$ARR['PER_PAGE'],
				"fullListYN"=>(empty($ARR['fullListYN']) ? "N" : "Y"),
				"startDate"=>$ARR['START_DT'],
				"endDate"=>$ARR['END_DT'],
				"dateField"=>$ARR['period'],
				"exposeYN"=>$ARR['SEARCH_EXPOSURE_YN'],
				"categoryName"=>$ARR['SEARCH_CATEGORY'],
				"baseCategoryYN"=>(empty($ARR['SEARCH_CATEGORY']) ? $ARR['baseCategoryYN'] : "Y"),
				"childCategoryName"=>$ARR['childCategoryName'],
				"parentCategoryID"=>$ARR['parentCategoryID'],
				
				
			);
			
			return $this->get("/ec/Goods/Category", $data);
		}

		function setData($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=> (!empty($ARR['SECTION_CD']) ? POST_GOODS_CATEGORY : PUT_GOODS_CATEGORY),
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],  
				"parentCategoryID"=>$ARR['parentCategoryID'],                
				"thisCategoryID"=> (!empty($ARR['SECTION_CD']) ? $ARR['SECTION_CD'] : $ARR['thisCategoryID']),
				"categoryName"=>$ARR['SECTION'],
				"exposeYN"=>$ARR['EXPOSURE_YN'],
				"orderNum"=>$ARR['EXPOSURE_ORDER'],
			);

			return $this->get("/ec/Goods/Category", $data);
		}

		function getData($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=>GET_GOODS_CATEGORY,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"thisCategoryID"=>$ARR['ctgyID'],
			);

			return $this->get("/ec/Goods/Category", $data);

		}

		function delData($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=>DEL_GOODS_CATEGORY,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"thisCategoryID"=>$ARR['thisCategoryID'],
			);

			return $this->get("/ec/Goods/Category", $data);
		}

		function isCheckDuplicate($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=>CHECK_EXIST_CATEGORY_ID,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"thisCategoryID"=>$ARR['thisCategoryID'],
			);

			return $this->get("/ec/Goods/Category", $data);
		}

	}

	// 0. 객체 생성
	$cProductCtgy= new ProductCtgy();
	// $cBuyMngt->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);
?>
