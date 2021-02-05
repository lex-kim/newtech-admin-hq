<?php	
	require_once $_SERVER['DOCUMENT_ROOT']."/common/class/CommonApi.php";

	class SaleList extends CommonApi {

		function getList($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=>GET_SALES_INFO_LIST,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"pageNum"=>$ARR['CURR_PAGE'],
				"pageItemCount"=>$ARR['PER_PAGE'],
				"fullListYN"=>(empty($ARR['fullListYN']) ? "N" : "Y"),
				"startDate"=>$ARR['START_DT'],
				"endDate"=>$ARR['END_DT'],
				"dateField"=>$ARR['period'],
				"salesID"=>$ARR['salesID'],
				"contractStatusCode"=>$ARR['contractStatusCode'],
				"divisionID"=>$ARR['divisionID'],
				"regionName"=>$ARR['regionName'],
				"garageName"=>$ARR['garageName'],
				"garageID"=>$ARR[''],
				"depositYN"=>$ARR['DEPOSIT_YN_SEARCH'],
				"billPublishYN"=>$ARR['billPublishYN'],
				"billTypeCode"=>$ARR['billTypeCode'],
				"paymentCode"=>$ARR['paymentCode'],
				"employeeName"=>$ARR['SALER_NM_SEARCH'],
				"memo"=>$ARR['searchMemo'],
				"startTotalPrice"=>$ARR['START_SALE_PRICE_SEARCH'],
				"endTotalPrice"=>$ARR['END_SALE_PRICE_SEARCH'],
			);

			return $this->get("/ec/Sales/Info", $data);
		}
		
		function setData($ARR){
			if(empty($ARR['EVIDENCE'])){       // 일반판매 생성/수정
				$data = array(
					"accessToken"=>$_SESSION['accessToken'],
					"requestMode"=> (empty($ARR['salesID']) ? POST_SALES_INFO  : PUT_SALES_INFO),
					"hqID"=>HQ_ID,
					"userID"=>$_SESSION['USERID'],
					"salesID"=>$ARR['salesID'],
					"issueDate"=>$ARR['issueDate'],
					"employeeID"=>$ARR['userNameCode'],
					"garageID"=>$ARR['garageNameCode'],
					"paymentCode"=>$ARR['paymentCode'],
					"memo"=>$ARR['memo'],
					"totalPrice"=>$ARR['PAYMENT_PRICE_WRITE'],
					"depositPrice"=>$ARR['depositPrice'],
					"depositDate"=>$ARR['depositDate'],
					"depositYN"=>$ARR['DEPOSIT_YN'],
					"itemID"=>$ARR['itemID'],
					"unitPrice"=>$ARR['unitPrice'],
					"quantity"=>$ARR['quantity'],
					"salePrice"=>$ARR['salePrice'],
					"itemMemo"=>$ARR['itemMemo'],
	
				);
				return $this->get("/ec/Sales/Info", $data);
			}else{   // 지출증빙
				$data = array(
					"accessToken"=>$_SESSION['accessToken'],
					"hqID"=>HQ_ID,
					"userID"=>$_SESSION['USERID'],
					"salesID"=>$ARR['salesID'],
				);
				

				if(isset($ARR['EVIDENCE_CASH'])){             // 현금영수증
					$urlType = "CashBill";
					$data = array_merge($data , array(
						"requestMode"=> PUT_SALES_CASH_BILL,
						"billCashType"=>$ARR['EVIDENCE_CASH'],
						"billCashOption"=>$ARR['CAHS_OPTION'],
						"billApplyVatYN"=>$ARR['billApplyVatYN'],
						"billName"=>$ARR['billName'],
						"billPhoneNum"=>$ARR['EVIDENCE_CASH_PERSONAL'],
						"billEmail"=>$ARR['billEmail'],
						"billCompanyName"=>$ARR['billCompanyName'],
						"billCompanyRegID"=>$ARR['EVIDENCE_CASH_CEO']
					));
					
				}else if(isset($ARR['recipientTypeCode'])){                 						 // 세금계산서
					$urlType = "TaxBill";
					$data = array_merge($data, array(
						"requestMode"=> PUT_SALES_TAX_BILL,
						"issueTypeCode"=>$ARR['issueTypeCode'],
						"taxTypeCode"=>$ARR['taxTypeCode'],
						"purposeTypeCode"=>$ARR['purposeTypeCode'],
						"recipientTypeCode"=>$ARR['recipientTypeCode'],
						"toCompanyRegID"=>$ARR['toCompanyRegID'],
						"toCompanyName"=>$ARR['TAX_ORDER_BIZ_NM'],
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
		
					));
				}else{															// 신청안함
					$urlType = "NoBill";                          
					$data = array_merge($data, array(
						"requestMode"=> PUT_SALES_NO_BILL,
		
					)) ;
				}

				return $this->get("/ec/Sales/".$urlType, $data);
			}
			
		}

		function getData($salesID){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=>GET_SALES_INFO,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"salesID"=>$salesID,
			);
			
			return $this->get("/ec/Sales/Info", $data);

		}

		function delData($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=>empty($ARR['reqMode']) ? DEL_SALES_INFO : $ARR['reqMode'],
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"salesID"=>$ARR['salesID'],
			);

			return $this->get("/ec/Sales/".$ARR['urlType'], $data);
		}

	}

	// 0. 객체 생성
	$cSaleList = new SaleList();
	// $cBuyMngt->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);
?>
