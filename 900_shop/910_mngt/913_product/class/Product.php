<?php	
	require_once $_SERVER['DOCUMENT_ROOT']."/900_shop/910_mngt/912_productCategory/class/ProductCtgy.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/900_shop/910_mngt/918_delivery/class/Delivery.php";
	require_once "ProductImage.php";

	class Product extends ServerApiClass {

		function getUploadImageArray($FILES, $ARR, $imageArray){
			$data = array();

			foreach($imageArray as $key => $value){
				if(!empty($FILES[$value]['name'])){
					$cImage = new UploadImage($FILES[$value]);
					$data = array_merge($data, 
									   $cImage->getData(
											$value."URI", 
											$value."PhysicalFilename", 
											$value."OriginFilename"
										));
				}else{
					$data = array_merge($data,
						 UploadImage::setImageValue($ARR[$value.'URI'],  $ARR[$value.'PhysicalFilename'], $ARR[$value.'OriginFilename'], 
							 $value."URI", $value."PhysicalFilename", $value."OriginFilename"));

				}
			}

			return $data;
		}

		function getList($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=>GET_GOODS_ITEM_LIST,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"pageNum"=>$ARR['CURR_PAGE'],
				"pageItemCount"=>$ARR['PER_PAGE'],
				"categoryName"=>$ARR['categoryCd'],
				"categoryID"=>$ARR['categoryID'],
				"fullListYN"=>(empty($ARR['fullListYN']) ? "N" : "Y"),
				"startDate"=>$ARR['START_DT'],
				"endDate"=>$ARR['END_DT'],
				"dateField"=>$ARR['dateField'],
				"mgmtTypeCode"=>$ARR['SEARCH_MANAGEMENT_DIVISION'],
				"buyerTypeCode"=>$ARR['buyerTypeCode'],
				"partnerName"=>$ARR['partnerName'],
				"partnerID"=>$ARR['partnerID'],
				"goodsName"=>$ARR['SEARCH_PRODUCT_NM'],
				"startPrice"=>$ARR['START_PRICE'],
				"endPrice"=>$ARR['END_PRICE'],
				"priceField"=>$ARR['priceField'],
				"keyfield"=>$ARR['keyField'],
				"keyword"=>$ARR['keyword'],
				"garageID"=>$ARR['garageID'],
				"soldOutYN"=>$ARR['searchSoldout'],
				"stockRemainsStart"=>$ARR['stockRemainsStart'],
				"stockRemainsEnd"=>$ARR['stockRemainsEnd'],
				"sortAcendentByNameYN"=>(empty($ARR['sortAcendentByNameYN']) ? "N" : $ARR['sortAcendentByNameYN']),
				
				
			);

			return $this->get("/ec/Goods/Items", $data);
		}

		function setData($ARR, $FILES){
			$optionUseYN = "";

			if(!empty($ARR['optionUseYN'])){
				$optionUseYN = array();
				foreach($ARR['optionUseYN'] as $key => $item){
					array_push($optionUseYN, $item);
				}
			}

			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=> (!empty($ARR['productID']) ? PUT_GOODS_ITEM  : POST_GOODS_ITEM ),
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"itemID"=>$ARR['itemID'],   
				"mgmtTypeCode"=>$ARR['mgmtTypeCode'],                
				"categoryID"=>$ARR['categoryCd2'],
				"partnerID"=>$ARR['partnerID'],
				"goodsCode"=>$ARR['goodsCode'],
				"goodsName"=>$ARR['goodsName'],
				"goodsNameShort"=>$ARR['goodsNameShort'],
				"goodsNameLong"=>$ARR['goodsNameLong'],
				"goodsSpec"=>$ARR['goodsSpec'],
				"unitPerBox"=>str_replace(",","",$ARR['unitPerBox']),
				"minBuyCnt"=>str_replace(",","",$ARR['minBuyCnt']),
				"buyPrice"=>str_replace(",","",$ARR['buyPrice']),
				"wholeSalePrice"=>str_replace(",","",$ARR['wSalePrice']),
				"retailPrice"=>str_replace(",","",$ARR['retailPrice']),
				"salePrice"=>str_replace(",","",$ARR['salePrice']),
				"buyPrice"=>str_replace(",","",$ARR['buyPrice']),
				"applyVatYN"=>$ARR['applyVatYN'],
				"salePriceInquiryYN"=>(empty($ARR['priceQuestionCheckbox'])?"N":$ARR['priceQuestionCheckbox']),
				"earnPoint"=>str_replace(",","",$ARR['earnPoint']),
				"deliveryPolicyID"=>$ARR['deliveryPolicyID'],
				"initQuantity"=>str_replace(",","",$ARR['initQuantity']),
				"manufacturerName"=>$ARR['manufacturerName'],
				"originName"=>$ARR['originName'],
				"brandName"=>$ARR['brandName'],
				"goodsInfo"=>$ARR['goodsInfo'],
				"deliveryInfo"=>$ARR['deliveryInfo'],
				"returnInfo"=>$ARR['returnInfo'],
				// "goodsOptionUseYN"=>$ARR['goodsOption'],
				"goodsOptionUseYN"=>"N",
				"optionName"=>$ARR['optionName'],
				"optionPrice"=>$ARR['optionPrice'],
				"soldOutYN"=>(empty($ARR['soldOutYN'])?"N":$ARR['soldOutYN']),
				"optionUseYN"=>$optionUseYN,
			);
			$cImage = new ProductImage();
			$imgArray = $cImage->getImgArr();
			$goodsInfoImgArray = $cImage->getGoodsImgArr();
			$deliveryInfoImgArray = $cImage->getDeliveryImgArr();
			$returnInfoImgArray = $cImage->getReturnImgArr();
			$catalogImgArray = $cImage->getCatalogImgArr();

			$data = array_merge($data, $this->getUploadImageArray($FILES, $ARR, $imgArray));
			$data = array_merge($data, $this->getUploadImageArray($FILES, $ARR, $goodsInfoImgArray));
			$data = array_merge($data, $this->getUploadImageArray($FILES, $ARR, $deliveryInfoImgArray));
			$data = array_merge($data, $this->getUploadImageArray($FILES, $ARR, $returnInfoImgArray));
			$data = array_merge($data, $this->getUploadImageArray($FILES, $ARR, $catalogImgArray));
			
		
			return $this->post("/ec/Goods/Items", $data, $FILES);
		}

		function getData($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=>GET_GOODS_ITEM ,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"itemID"=>$ARR['itemID'],
				"garageID"=>$ARR['garageID'],
			);
			return $this->get("/ec/Goods/Items", $data);

		}

		function delData($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=>DEL_GOODS_ITEM,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"itemID"=>$ARR['itemID'],
			);
			return $this->get("/ec/Goods/Items", $data);
		}

		function isCheckDuplicate($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=>CHECK_EXIST_ITEM_ID,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"itemID"=>$ARR['itemID'],
			);

			return $this->get("/ec/Goods/Items", $data);
		}

		function getRecentData($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=>$ARR['reqMode'] ,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID']
			);
			
			return $this->get("/ec/Goods/Recent", $data);

		}

	}

	// 0. 객체 생성
	$cProduct = new Product();
	// $cBuyMngt->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);
?>
