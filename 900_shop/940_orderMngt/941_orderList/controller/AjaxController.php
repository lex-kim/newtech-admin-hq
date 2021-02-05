<?php
	require $_SERVER['DOCUMENT_ROOT']."/common/common.php";				// 환경설정파일 로딩 - 데이터변수 등
	require_once "../class/OrderList.php";
	
	switch( $_REQUEST['REQ_MODE'] ){
		case CASE_READ:  // 해당공업사 기본정보
			$result = $cOrderList->getGargeInfo($_REQUEST);

			$response['status']  = $result['status'];
			$response['message'] =  $result['result'];
			$response['data'] = $result['data'];
			
			echo json_encode($response);
		break;
		
		case CASE_LIST:
			$_REQUEST['orderStatusCodeList'] = "";
			if(!empty($_REQUEST['TAB_MODE'])){
				if($_REQUEST['TAB_MODE'] == TAB_ORDER){
					$_REQUEST['orderStatusCodeList'] = [
														EC_ORDER_STATUS_ORDER_COMPLETE, EC_ORDER_STATUS_DEPOSIT_WAIT, 
														EC_ORDER_STATUS_PAY_COMPLETE, EC_ORDER_STATUS_ITEM_READY
													];
				}else if($_REQUEST['TAB_MODE'] == TAB_SEND){
					$_REQUEST['orderStatusCodeList'] = [EC_ORDER_STATUS_DELIVERY_ING, EC_ORDER_STATUS_DELIVERY_COMPLETE];
				}else if($_REQUEST['TAB_MODE'] == TAB_COMPLETE){
					$_REQUEST['orderStatusCodeList'] = [EC_ORDER_STATUS_END];
				}else if($_REQUEST['TAB_MODE'] == TAB_CANCEL){
					$_REQUEST['orderStatusCodeList'] = [
														EC_ORDER_STATUS_CHANGE_RECEIPT, EC_ORDER_STATUS_REFUND_ING, EC_ORDER_STATUS_CHANGE_ING,
														EC_ORDER_STATUS_RESEND, EC_ORDER_STATUS_CHANGE_COMPLETE, EC_ORDER_STATUS_CANCEL_RECEIPT,
														EC_ORDER_STATUS_COLLECT_COMPLETE, EC_ORDER_STATUS_CANCEL, EC_ORDER_STATUS_PAY_CANCEL
													];
				}
			}
			$result = $cOrderList->getList($_REQUEST);

			$response['status']  = $result['status'];
			$response['message'] =  $result['result'];
			$response['data'] = $result['data'];
			if($response['status'] == OK){
				$response['total_item_cnt'] = $result['total_item_cnt'];
			}
			echo json_encode($response);
		break;

		case CASE_TOTAL_LIST:
			$_REQUEST['fullListYN'] = "Y";
			$result = $cOrderList->getList($_REQUEST);
		
			$response['status']  = $result['status'];
			$response['message'] =  $result['result'];
			$response['data'] = $result['data'];

			echo json_encode($response);
		break;

		case CASE_DELETE:
			$result = $cOrderList->delData($_REQUEST);

			$response['status']  = OK;
			$response['message'] = "OK";

			echo json_encode($response);
		break;

		case CASE_CREATE:
			$result = $cOrderList->setData($_REQUEST, $_FILES);

			$response['status']  = $result['status'] ;
			$response['message'] = $result['result'] ;
			$response['data'] = $result['data'] ;

			echo json_encode($response);
		break;

		case CASE_UPDATE:
			$result = $cOrderList->updateData($_REQUEST);

			$response['status']  = $result['status'] ;
			$response['message'] = $result['result'] ;
			$response['data'] = $result['data'] ;

			echo json_encode($response);
		break;
		
    }

?>
