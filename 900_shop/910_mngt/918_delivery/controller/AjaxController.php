<?php
	require $_SERVER['DOCUMENT_ROOT']."/common/common.php";				// 환경설정파일 로딩 - 데이터변수 등
	require_once "../class/Delivery.php";

	switch( $_REQUEST['REQ_MODE'] ){
		case CASE_LIST:
			$result = $cDelivery->getList($_REQUEST);
			
		
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
			$result = $cDelivery->getList($_REQUEST);
		
			$response['status']  = $result['status'];
			$response['message'] =  $result['result'];
			$response['data'] = $result['data'];

			echo json_encode($response);
		break;

		case CASE_DELETE:
			$result = $cDelivery->delData($_REQUEST);

			$response['status']  = OK;
			$response['message'] = "OK";

			echo json_encode($response);
		break;

		case CASE_READ:
			$result = $cDelivery->getData($_REQUEST);

			$response['status']  = OK;
			$response['message'] = "OK";
			$response['data'] = $result;

			echo json_encode($response);
		break;

		case CASE_CREATE:
			if(!empty($_REQUEST['deliveryFeeFree'])){
				$result = $cDelivery->setDeliveryEnv($_REQUEST);
			}else{
				$result = $cDelivery->setData($_REQUEST, $_FILES);
			}
			

			$response['status']  = $result['status'] ;
			$response['message'] = $result['result'] ;
			$response['data'] = $result['data'] ;

			echo json_encode($response);
		break;

		case CASE_UPDATE:
			$result = $cDelivery->setData($_REQUEST, $_FILES);

			if($result){
				$response['status']  = OK;
				$response['message'] = "OK";
				$response['data'] = $_FILES;
			}else{
				$response['status']  = ERROR;
				$response['message'] = "ERROR";
			}

			echo json_encode($response);
		break;

		case CASE_CONFLICT_CHECK:
			$result = $cDelivery->isCheckDuplicate($_REQUEST);

			$response['status']  = $result['status'];
			if($response['status'] == OK){
				$response['message'] = "사용가능한 아이디입니다.";
			}else if($response['status'] == ERROR){
				$response['message'] = "사용중인 아이디가 존재합니다.";
			}else{
				$response['message'] = $result['result'];
			}

			echo json_encode($response);
		break;
    }

?>