<?php
	require_once $_SERVER['DOCUMENT_ROOT']."/900_shop/910_mngt/913_product/class/Product.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/900_shop/910_mngt/911_buyMngt/class/BuyMngt.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/common/class/Options.php";
	require_once "class/ProductImage.php";

	class ProductController extends Options{

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

		function getImageArray(){
			$cImage = new ProductImage();
			$imgArray = $cImage->getImgArr();
			$goodsInfoImgArray = $cImage->getGoodsImgArr();
			$deliveryInfoImgArray = $cImage->getDeliveryImgArr();
			$returnInfoImgArray = $cImage->getReturnImgArr();
			$catalogImgArray = $cImage->getCatalogImgArr();

			
		}

		function getCategoryArray($parentCtgy){
			$cPruductCategory = new ProductCtgy();     // 상품카테고리 관련
			$ARR['fullListYN'] = "Y";
			$ARR['baseCategoryYN'] = "Y";
			if(!empty($parentCtgy)){
				$ARR['parentCategoryID'] = $parentCtgy;
				$ARR['baseCategoryYN'] = "N";
			}

			$getCatoryResult = $cPruductCategory->getList($ARR);     // 상품카테고리
			
			$categoryArray = array();

			if($getCatoryResult['status'] == OK){
				if($getCatoryResult['data'] != HAVE_NO_DATA && empty($parentCtgy)){     // 상위카테고리
					foreach($getCatoryResult['data']  as $idx => $item){
						if(!empty($item['childCategoryCount']) && $item['childCategoryCount'] > 0){
							array_push($categoryArray, $item);
						}
					}
				}else if($getCatoryResult['data'] != HAVE_NO_DATA && !empty($parentCtgy)){ //하위카테고리
					$categoryArray = $getCatoryResult['data'] ;
				}
			}else{
				errorAlert("상품카테고리를 가져오는데 실패하였습니다.");
			}
			return $categoryArray;
		}

		/**
		 * 거래처관리 화면 컨트롤러
		 */
		function index(){
			$this->getImageArray();
			$categoryArray = $this->getCategoryArray(null);
			include "view/".$this->getPagNM().".php";
		}

		function popupIndex(){
			$cImage = new ProductImage();
			$imgArray = $cImage->getImgArr();
			$goodsInfoImgArray = $cImage->getGoodsImgArr();
			$deliveryInfoImgArray = $cImage->getDeliveryImgArr();
			$returnInfoImgArray = $cImage->getReturnImgArr();
			$catalogImgArray = $cImage->getCatalogImgArr();
			
			$cDeliveryCategory = new Delivery();
			$cBuyMngtCategory = new BuyMngt();
			$data = new Product();                     // 상품관리 관련

			$itemID = $_REQUEST['itemID'];
			
			$deliveryArray = array();
			$buyMngtArray = array();
			$itemInfo = array();

			$ARR['fullListYN'] = "Y";
			$categoryArray = $this->getCategoryArray(null);
			$getDeliveryResult = $cDeliveryCategory->getList($ARR);  // 배송정책 
			$ARR['deal'] = EC_PARTNER_TYPE_BUY;
			$getBuyMngtResult = $cBuyMngtCategory->getList($ARR);    // 거래처리스트(매입처) 
			

			if($getDeliveryResult['status'] == OK && $getBuyMngtResult['status'] == OK){
				if($getDeliveryResult['data'] != HAVE_NO_DATA ){
					$deliveryArray = $getDeliveryResult['data'] ;
				}
				if($getBuyMngtResult['data'] != HAVE_NO_DATA ){
					$buyMngtArray = $getBuyMngtResult['data'] ;
				}

				if(!empty($_REQUEST['itemID'])){      //수정
					$result = $data->getData($_REQUEST);
				
					if($result['status'] == OK){
						if($result['data']['goodsOptions'] == HAVE_NO_DATA){
							$result['data']['goodsOptions'] = array();
						}
						$result['data']['wSalePrice'] = $result['data']['wholeSalePrice'] ;  // 이유는 모르겠지만 wholeSalePrice <- 이 변수이름이 안먹음

						$itemInfo = $result['data']; 
						$itemInfo['childCategory'] = $this->getCategoryArray($result['data']['parentCategoryID']);
						
						include "view/".$this->getPagNM().".php";
					}else{
						echo "상품관리 정보를 가져오는데 실패하였습니다.";
						exit();
					}
				}else{ // 거래처관리 등록 및 메인리스트
					include "view/".$this->getPagNM().".php";
				}
			}else{
				if($cBuyMngtCategory['status'] != OK){
					echo "매입처정보를 가져오는데 실패하였습니다.";
				}else{
					echo "배송정책을 가져오는데 실패하였습니다.";
				}
				exit();
			}	
		}
	}
?>