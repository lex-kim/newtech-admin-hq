<?php
	require $_SERVER['DOCUMENT_ROOT']."/common/common.php";				// 환경설정파일 로딩 - 데이터변수 등
	require_once "../class/ProductCtgy.php";

	switch( $_REQUEST['REQ_MODE'] ){
		case CASE_LIST:
			$result = $cProductCtgy->getList($_REQUEST);
		
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
			$result = $cProductCtgy->getList($_REQUEST);
		
			$response['status']  = $result['status'];
			$response['message'] =  $result['result'];
			$response['data'] = $result['data'];

			echo json_encode($response);
		break;

		case CASE_DELETE:
			$result = $cProductCtgy->delData($_REQUEST);

			$response['status']  = OK;
			$response['message'] = "OK";

			echo json_encode($response);
		break;

		case CASE_READ:
			$result = $cProductCtgy->getData($_REQUEST);

			$response['status']  = OK;
			$response['message'] = "OK";
			$response['data'] = $result;

			echo json_encode($response);
		break;

		case CASE_CREATE:
			$result = $cProductCtgy->setData($_REQUEST);

			$response['status']  = $result['status'] ;
			$response['message'] = $result['result'] ;
			$response['data'] = $result['data'] ;

			echo json_encode($response);
		break;

		case CASE_UPDATE:
			$result = $cProductCtgy->setData($_REQUEST);

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
			$result = $cProductCtgy->isCheckDuplicate($_REQUEST);

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
