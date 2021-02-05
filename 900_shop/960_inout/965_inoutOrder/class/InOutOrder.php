<?php	
	require_once $_SERVER['DOCUMENT_ROOT']."/common/class/CommonApi.php";

	class InOutOrder extends CommonApi {

		function getList($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=> GET_STOCK_ORDER_LIST,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"pageNum"=>$ARR['CURR_PAGE'],
				"pageItemCount"=>$ARR['PER_PAGE'],
				"fullListYN"=>(empty($ARR['fullListYN']) ? "N" : "Y"),
				"startDate"=>$ARR['START_DT'],
				"endDate"=>$ARR['END_DT'],
				"dateField"=>$ARR['dateField'],
				"stockOrderID"=>$ARR['ORDER_NUM_SEARCH'],
				"orderID"=>$ARR['ORDER_NUMBER_SEARCH'],
				"stockOrderTypeCode"=>$ARR['ORDER_DIVISION_SEARCH'],
				"toKeyfield"=>$ARR['PURCHASE_SEARCH'],
				"toKeyword"=>$ARR['PURCHASE_KEYWORD_SEARCH'],
				"shipKeyfield"=>$ARR['IN_INFO_SEARCH'],
				"shipKeyword"=>$ARR['IN_INFO_KEYWORD_SEARCH'],
				"depositYN"=>$ARR['PAYMENT_YN_SEARCH'],
				"stockInYN"=>$ARR['IN_YN_SEARCH'],
				"itemKeyfield"=>$ARR['ORDER_INFO_SEARCH'],
				"itemKeyword"=>$ARR['ORDER_INFO_KEYWORD_SEARCH'],
				"memo"=>$ARR[''],

			);

			return $this->get("/ec/StockOrder/Info", $data);
		}

		function setData($ARR){
			if(empty($ARR['stockOrderID'])){					// 발주생성
				$data = array(								
					"accessToken"=>$_SESSION['accessToken'],
					"requestMode"=> POST_STOCK_ORDER,
					"hqID"=>HQ_ID,
					"userID"=>$_SESSION['USERID'],
					"orderID"=>$ARR['orderID'],
					"fromChargeName"=>$ARR['fromChargeName'],
					"fromChargePhoneNum"=>$ARR['fromChargePhoneNum'],
					"stockOrderType"=>$ARR['stockOrderType'],
					"partnerID"=>$ARR['partnerID'],
					"issueDate"=>$ARR['ORDER_DT'],
					"dueDate"=>$ARR['DELIVERY_DT'],
					"deliveryMethodCode"=>$ARR['IN_DELIVERY_WAY'],
					"deliveryPrice"=>$ARR['deliveryFeePrice'],
					"memo"=>$ARR['IN_ETC'],
					"totalPrice"=>str_replace(",","",$ARR['PAYMENT_PRICE_WRITE']) ,
					"shipName"=>$ARR['IN_NAME'],
					"shipZipcode"=>$ARR['IN_ZIP'],
					"shipAddress1"=>$ARR['IN_ADDRESS1_AES'],
					"shipAddress2"=>$ARR['IN_ADDRESS2_AES'],
					"shipChargeName"=>$ARR['IN_FAO_NM'],
					"shipChargePhoneNum"=>$ARR['IN_FAO_PHONE'],
					"itemID"=>$ARR['itemID'],
					"optionID"=>$ARR['optionID'],
					"unitPrice"=>$ARR['unitPrice'],
					"optionPrice"=>$ARR['optionPrice'],
					"quantity"=>$ARR['quantity'],
					"itemMemo"=>$ARR['itemMemo'],
					"garageID"=>$ARR['garageID'],
	
				);
	
				return $this->get("/ec/StockOrder/Info", $data);
			}else{													// SMS/FAX 발송
				$data = array(						
					"accessToken"=>$_SESSION['accessToken'],
					"requestMode"=> PUT_STOCK_ORDER_TRANSFER,
					"hqID"=>HQ_ID,
					"userID"=>$_SESSION['USERID'],
					"stockOrderID"=>$ARR['stockOrderID'],
					"sendMethodCode"=>$ARR['sendMethodCode'],
					"sendPhoneNum"=>$ARR['SEND_PHONE_NUM'],
	
				);

				return $this->get("/ec/StockOrder/Transfer", $data);
			}
			
		}

		/** 상태변경 */
		function updateData($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
			);
			
			if(is_array($ARR['stockOrderID'])){     						//일괄입고
				$data = array_merge($data, array(
					"requestMode"=>PUT_STOCK_ORDER_IN_MULTI,
					"stockOrderIdList"=>$ARR['stockOrderID'],
					"issueDate"=>$ARR['issueDate'],
				)); 

				return $this->get("/ec/StockOrder/InMulti", $data);
			}else{
				$data = array_merge($data, array(
					"stockOrderID"=>$ARR['stockOrderID'],
				)); 
				
				if($ARR['DEPOSIT_YN'] ==  "Y" || $ARR['DEPOSIT_YN'] ==  "N"){  // 입금요청
					$data = array_merge($data, array(
						"requestMode"=>PUT_STOCK_ORDER_DEPOSIT,
						"depositYN"=>$ARR['DEPOSIT_YN'],
						"depositPrice"=>$ARR['MODAL_PAY_PRICE'],
						"depositDate"=>$ARR['MODAL_PAY_DT'],
						"depositMemo"=>$ARR['MODAL_PAY_MEMO'],
					));
					
					return $this->get("/ec/StockOrder/Deposit", $data);
					
				}else if(!empty($ARR['DELIVERY_METHOD'])){               // 배송정보
					$data = array_merge($data, array(
						"requestMode"=>PUT_STOCK_ORDER_DELIVERY,
						"deliveryMethodCode"=>$ARR['DELIVERY_METHOD'],
						"logisticsID"=>$ARR['ORDER_DELIVERY_NM'],
						"logisticsInvoiceNum"=>$ARR['MODAL_LOGISTICS_NUM'],
					));

					return $this->get("/ec/StockOrder/Delivery", $data);
				}else{     													// 입고
					$data = array_merge($data, array(
						"requestMode"=>PUT_STOCK_ORDER_IN,
						"issueDate"=>$ARR['issueDate'],
						
					));                
					
					return $this->get("/ec/StockOrder/In", $data);
				}
			}
			
			
		}

		/** 발주상세 */
		function getData($stockOrderID){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=>GET_STOCK_ORDER,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"stockOrderID"=>$stockOrderID,
			);
			
			return $this->get("/ec/StockOrder/Info", $data);

		}

		function delData($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=>PUT_ORDER_CANCEL,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"orderID"=>$ARR['orderID'],
				"cancelTypeCode"=>$ARR['cancelTypeCode'],
				"cancelDetailTypeCode"=>$ARR['ORDER_DELIVERY_NM']

			);
		
			return $this->get("/ec/Order/Cancel", $data);

		}

		function getDeliveryFeePrice($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=>GET_STOCK_ORDER_DELIVERY,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"deliveryMethodCode"=>$ARR['deliveryMethodCode'],
				"itemID"=>$ARR['itemID'],
				"quantity"=>$ARR['quantity'],

			);
			return $this->get("/ec/StockOrder/Delivery", $data);
		}

	}

	// 0. 객체 생성
	$cInOutOrder = new InOutOrder()
?>
