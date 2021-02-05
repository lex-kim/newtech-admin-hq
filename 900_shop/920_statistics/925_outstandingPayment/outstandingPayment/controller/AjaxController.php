<?php
	require $_SERVER['DOCUMENT_ROOT']."/common/common.php";				// 환경설정파일 로딩 - 데이터변수 등
	require_once "../class/OutstandingPaymentStatus.php";
	
	switch( $_REQUEST['REQ_MODE'] ){
		
		case CASE_LIST:
			$result = $cOutstandingPaymentStatus->getList($_REQUEST);

			$response['status']  = $result['status'];
			$response['message'] =  $result['result'];
			$response['data'] = $result['data'];
			
			if($response['status'] == OK){
				$response['total_item_cnt'] = $result['total_item_cnt'];
			}
			echo json_encode($response);
		break;

		case CASE_READ:
			$result = $cOutstandingPaymentStatus->getData($_REQUEST);

			$response['status']  = $result['status'];
			$response['message'] =  $result['result'];
			$response['data'] = $result['data'];
			
			echo json_encode($response);
		break;
		
    }

?>
