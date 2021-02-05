<?php
	require $_SERVER['DOCUMENT_ROOT']."/common/common.php";				// 환경설정파일 로딩 - 데이터변수 등
	require_once "../class/IntegratedDelivery.php";

	switch( $_REQUEST['REQ_MODE'] ){

		case CASE_CREATE:
			$result = $cIntegratedDelivery->setData($_REQUEST);

			$response['status']  = $result['status'] ;
			$response['message'] = $result['result'] ;
			$response['data'] = "설정되었습니다.";

			echo json_encode($response);
		break;

		
    }

?>
