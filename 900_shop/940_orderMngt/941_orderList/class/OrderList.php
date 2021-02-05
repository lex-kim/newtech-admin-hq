<?php	
	require_once $_SERVER['DOCUMENT_ROOT']."/common/class/CommonApi.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/900_shop/910_mngt/913_product/class/Product.php";

	class OrderList extends CommonApi {

		function getList($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=> GET_ORDER_INFO_LIST,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"pageNum"=>$ARR['CURR_PAGE'],
				"pageItemCount"=>$ARR['PER_PAGE'],
				"fullListYN"=>(empty($ARR['fullListYN']) ? "N" : "Y"),
				"startDate"=>$ARR['START_DT_SEARCH'],
				"endDate"=>$ARR['END_DT_SEARCH'],
				"dateField"=>$ARR['dateField'],
				"orderID"=>$ARR['ORDER_ID_SEARCH'],
				"orderStatusCode"=>$ARR['orderStatusCode'],
				"contractStatusCode"=>$ARR['contractStatusCode'],
				"pointUseYN"=>$ARR['POINT_USE_YN_SEARCH'],
				"deliveryMethodCode"=>$ARR['deliveryMethodCode'],
				"depositYN"=>$ARR['DEPOSIT_YN_SEARCH'],
				"paymentCode"=>$ARR['paymentCode'],
				"cancelType"=>$ARR['cancelType'],
				"cancelDetailType"=>$ARR['cancelDetailType'],
				"billTypeCode"=>$ARR['billTypeCode'],
				"printTransStateYN"=>$ARR['printTransStateYN'],
				"divisionID"=>$ARR['DIVISION_ID_SEARCH'],
				"regionName"=>$ARR['regionName'],
				"garageName"=>$ARR['garageName'],
				"garageID"=>$ARR['garageID'],
				"orderStatusCodeList"=>$ARR['orderStatusCodeList']
			);
			
			return $this->get("/ec/Order/Info", $data);
		}

		function setData($ARR, $FILES){
			if(empty($ARR['orderID'])){ // 주문 생성
				if(empty($FILES)){
					$data = array(
						"accessToken"=>$_SESSION['accessToken'],
						"requestMode"=> POST_ORDER_INFO,
						"hqID"=>HQ_ID,
						"userID"=>$_SESSION['USERID'],
						"quotationID"=>$ARR['quotationID'],
						"garageID"=>$ARR['garageID'],
						"orderName"=>$ARR['ORDER_NM'],
						"orderPhoneNum"=>$ARR['ORDER_PHONE'],
						"zipcode"=>$ARR['ORDER_ZIP'],
						"address1"=>$ARR['ORDER_ADDRESS1_AES'],
						"address2"=>$ARR['ORDER_ADDRESS2_AES'],
						"deliveryMethodCode"=>$ARR['deliveryMethodCode'],
						"orderPrice"=>$ARR['orderPrice'],
						"deliveryPrice"=>$ARR['deliveryPrice'],
						"pointRedeem"=>(empty($ARR['pointRedeem']) ? 0 : $ARR['pointRedeem']),
						"totalPrice"=>$ARR['totalPrice'],
						"earnPoint"=>$ARR['earnPoint'],
						"paymentCode"=>$ARR['PAYMENT'],
						"billTypeCode"=>(empty($ARR['EVIDENCE']) ? EC_BILL_TYPE_NO : $ARR['EVIDENCE']),

						"billCashType"=>$ARR['EVIDENCE_CASH'],
						"billCashOption"=>$ARR['CAHS_OPTION'],
						"billApplyVatYN"=>$ARR['billApplyVatYN'],
						"billName"=>$ARR['billName'],
						"billPhoneNum"=>$ARR['EVIDENCE_CASH_PERSONAL'],
						"billEmail"=>$ARR['billEmail'],
						"toCompanyRegID"=>(isset($ARR['EVIDENCE_CASH_CEO']) ? $ARR['EVIDENCE_CASH_CEO'] : $ARR['toCompanyRegID']),
						"toCompanyName"=>(isset($ARR['billCompanyName']) ? $ARR['billCompanyName'] : $ARR['TAX_ORDER_BIZ_NM']),


						"issueTypeCode"=>$ARR['issueTypeCode'],
						"taxTypeCode"=>$ARR['taxTypeCode'],
						"purposeTypeCode"=>$ARR['purposeTypeCode'],
						"recipientTypeCode"=>$ARR['recipientTypeCode'],
						"toCeoName"=>$ARR['TAX_ORDER_BIZ_CEO_NM'],
						"toAddress"=>$ARR['TAX_ORDER_BIZ_ADDRESS'],
						"toCompanyBiz"=>$ARR['TAX_ORDER_BIZ_BIZ'],
						"toCompanyPart"=>$ARR['TAX_ORDER_BIZ_EVENT'],
						"toChargeName1"=>$ARR['toChargeName1'],
						"toChargeDeptName1"=>$ARR['toChargeDeptName1'],
						"toChargeTel1"=>$ARR['toChargeTel1'],
						"toChargePhoneNum1"=>$ARR['toChargePhoneNum1'],
						"toChargeEmail1"=>$ARR['toChargeEmail1'],
						"toChargeName2"=>$ARR['toChargeName2'],
						"toChargeDeptName2"=>$ARR['toChargeDeptName2'],
						"toChargeTel2"=>$ARR['toChargeTel2'],
						"toChargePhoneNum2"=>$ARR['toChargePhoneNum2'],
						"toChargeEmail2"=>$ARR['toChargeEmail2'],
						"memo"=>$ARR['memo'],
					);
				
					return $this->get("/ec/Order/Info", $data);
				}else{
					$data = array(
						"accessToken"=>$_SESSION['accessToken'],
						"requestMode"=> PUT_ORDER_DELIVERY_BATCH,
						"hqID"=>HQ_ID,
						"userID"=>$_SESSION['USERID'],
						"uploadCSV"=> curl_file_create($FILES['deliveryCSV']['tmp_name'], $FILES['deliveryCSV']['type'], $FILES['deliveryCSV']['name']),
					);

					return $this->post("/ec/Order/Delivery", $data, $FILES);
				}
				
			}else{             
				if(!empty($ARR['paymentCode'])){			// 결제정보 전송
					$data = array(
						"accessToken"=>$_SESSION['accessToken'],
						"requestMode"=> POST_ORDER_PAYINFO,
						"hqID"=>HQ_ID,
						"userID"=>$_SESSION['USERID'],
						"orderID"=>$ARR['orderID'],
						"sendPhoneNum"=>$ARR['sendPhoneNum'],
						"paymentCode"=>$ARR['paymentCode']
					);

					return $this->get("/ec/Order/Payinfo", $data);
				}else{										// empty($ARR['urlType'] 이면 거래명세서 출력(출력시간 업데이트), 아니면 지출증빙 발행
					$data = array(
						"accessToken"=>$_SESSION['accessToken'],
						"requestMode"=> (empty($ARR['urlType']) ? PUT_ORDER_PRINT_STATEMENT : PUT_ORDER_EXPENDITURE ) ,
						"hqID"=>HQ_ID,
						"userID"=>$_SESSION['USERID'],
						"orderID"=>$ARR['orderID']
					);
					
					return $this->get("/ec/Order/".(empty($ARR['urlType']) ? "Statement" : $ARR['urlType'] ) , $data);
				}
			}
			
			
		}

		/** 상태변경 */
		function updateData($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"orderID"=>$ARR['orderID'],
			);

			if(!empty($ARR['paymentCode'])){  // 입금처리
				$data = array_merge($data, array(
					"requestMode"=>PUT_ORDER_DEPOSIT,
					"depositYN"=>$ARR['DEPOSIT_YN'],
					"depositPrice"=>$ARR['DEPOSIT_YN_PRICE'],
					"depositDate"=>$ARR['DEPOSIT_YN_DATE'],
					"depositMemo"=>$ARR['DEPOSIT_YN_ETC'],
					"paymentCode"=>$ARR['paymentCode'],
				));

				return $this->get("/ec/Order/Deposit", $data);

			}else if(!empty($ARR['orderStatusCode'])){ // 주문상태변경
				$data = array_merge($data, array(
					"requestMode"=>PUT_ORDER_STATUS,
					"orderStatusCode"=>$ARR['orderStatusCode'],
				));

				return $this->get("/ec/Order/Status", $data);

			}else if(!empty($ARR['DELIVERY_METHOD'])){  // 배송정보 변경
				$data = array_merge($data, array(
					"requestMode"=>PUT_ORDER_DELIVERY,
					"deliveryMethodCode"=>$ARR['DELIVERY_METHOD'],
					"logisticsID"=>$ARR['ORDER_DELIVERY_NM'],
					"logisticsInvoiceNum"=>$ARR['ORDER_DELIVERY_NUM'],
				));

				return $this->get("/ec/Order/Delivery", $data);

			}else if(!empty($ARR['REDELIVERY_METHOD'])){  // 재배송정보
				$data = array_merge($data, array(
					"requestMode"=>PUT_ORDER_RESEND,
					"resendDeliveryMethodCode"=>$ARR['REDELIVERY_METHOD'],
					"resendLogisticsID"=>$ARR['REDELIVERY_DELIVERY_NM'],
					"resendLogisticsInvoiceNum"=>$ARR['REDELIVERY_DELIVERY_NUM'],
					"resendTypeCode"=>$ARR['resendTypeCode'],
					"resendDeliveryFeeType"=>$ARR['REDELIVERY_FEE'],
					"resendDeliveryFee"=>((empty($ARR['REDELIVERY_DELIVERY_FEE']) || $ARR['REDELIVERY_DELIVERY_FEE'] <= 0) ? 0 :$ARR['REDELIVERY_DELIVERY_FEE']),
				));

				return $this->get("/ec/Order/Resend", $data);

			}else if(!empty($ARR['RETURN_METHOD'])){  // 반품배송정보
				$data = array_merge($data, array(
					"requestMode"=>PUT_ORDER_RETURN,
					"returnDeliveryMethodCode"=>$ARR['RETURN_METHOD'],
					"returnLogisticsID"=>$ARR['RETURN_DELIVERY_NM'],
					"returnLogisticsInvoiceNum"=>$ARR['RETURN_DELIVERY_NUM'],
					"returnTypeCode"=>$ARR['returnTypeCode'],
					"returnDeliveryFeeType"=>$ARR['DELIVERY_FEE'],
					"returnDeliveryFee"=>((empty($ARR['RETURN_DELIVERY_FEE']) || $ARR['RETURN_DELIVERY_FEE'] <= 0) ? 0 :$ARR['RETURN_DELIVERY_FEE']),
				));

				return $this->get("/ec/Order/Return", $data);

			}else if(!empty($ARR['RETURN_DELIVERY_PRICE'])){  // 환불정보
				$data = array_merge($data, array(
					"requestMode"=>PUT_ORDER_REFUND,
					"refundPrice"=>$ARR['RETURN_DELIVERY_PRICE'],
					"refundDate"=>$ARR['RETURN_DELIVERY_DATE'],
					"refundMemo"=>$ARR['RETURN_DELIVERY_ETC'],
					"refundBankCode"=>$ARR['RETURN_DELIVERY_BANK_NM'],
					"refundBankNum"=>$ARR['RETURN_DELIVERY_BANK_NUM'],
					"refundBankOwn"=>$ARR['RETURN_DELIVERY_BANK_NUMNM'],
					"refundYN"=>((empty($ARR['RETURN_DELIVERY_PRICE']) || $ARR['RETURN_DELIVERY_PRICE'] <= 0 ) ? "N" : "Y" ),
				));

				return $this->get("/ec/Order/Refund", $data);
			}else if(!empty($ARR['memo'])){  // 환불정보
				$data = array_merge($data, array(
					"requestMode"=>PUT_ORDER_MEMO,
					"memo"=>$ARR['memo'],
				));
			
				return $this->get("/ec/Order/Memo", $data);
			}else {
				return null;
			}
			
		}

		/** 주문상세 */
		function getData($orderID ){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=>GET_ORDER_INFO,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"orderID"=>$orderID,
			);
			
			return $this->get("/ec/Order/Info", $data);

		}

		function delData($ARR){
			if(isset($ARR['cancelTypeCode'])){ // 주문취소
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
			}else{ 				// 지출증빙 취소
				$data = array(
					"accessToken"=>$_SESSION['accessToken'],
					"requestMode"=>DEL_ORDER_EXPENDITURE,
					"hqID"=>HQ_ID,
					"userID"=>$_SESSION['USERID'],
					"orderID"=>$ARR['orderID']
	
				);
				return $this->get("/ec/Order/Expenditure", $data);
			}
		}

	}

	// 0. 객체 생성
	$cOrderList = new OrderList()
?>
