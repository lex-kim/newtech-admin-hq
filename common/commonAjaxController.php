<?php
	require_once $_SERVER['DOCUMENT_ROOT']."/common/common.php";				// 환경설정파일 로딩 - 데이터변수 등
	require_once $_SERVER['DOCUMENT_ROOT']."/common/class/clsCommon.php";
	
	switch( $_REQUEST['REQ_MODE'] ){

		case CASE_CREATE:
			$result = $clsCommon->setData($_REQUEST);

			$response['status']  = $result['status'] ;
			$response['message'] = $result['result'] ;
			$response['data'] = $result['data'] ;

			echo json_encode($response);
		break;
		
    }

?>
