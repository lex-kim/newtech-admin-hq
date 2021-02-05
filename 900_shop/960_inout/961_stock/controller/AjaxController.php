<?php
	require $_SERVER['DOCUMENT_ROOT']."/common/common.php";				// 환경설정파일 로딩 - 데이터변수 등
	require_once "../class/Stock.php";

	switch( $_REQUEST['REQ_MODE'] ){
		case CASE_LIST:
			$result = $cStock->getList($_REQUEST);
		
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
			$result = $cStock->getList($_REQUEST);
		
			$response['status']  = $result['status'];
			$response['message'] =  $result['result'];
			$response['data'] = $result['data'];

			echo json_encode($response);
		break;

		case CASE_DELETE:
			$result = $cStock->delData($_REQUEST);

			$response['status']  = OK;
			$response['message'] = "OK";

			echo json_encode($response);
		break;

		case CASE_CREATE:
			$result = $cStock->setData($_REQUEST, $_FILES);

			$response['status']  = $result['status'] ;
			$response['message'] = $result['result'] ;
			$response['data'] = $result['data'] ;

			echo json_encode($response);
		break;

		case CASE_UPDATE:
			$result = $cStock->setData($_REQUEST, $_FILES);

			$response['status']  = $result['status'] ;
			$response['message'] = $result['result'] ;
			$response['data'] = $result['data'] ;

			echo json_encode($response);
		break;

		
    }

?>
