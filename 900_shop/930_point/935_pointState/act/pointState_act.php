<?php
	require $_SERVER['DOCUMENT_ROOT']."/common/common.php";				// 환경설정파일 로딩 - 데이터변수 등
	require "../class/clsPointState.php";
	switch( $_REQUEST['REQ_MODE'] ){
		/** 조회 */
		case CASE_LIST:
			$data =  $cPointState->getList($_REQUEST);

			if($data === HAVE_NO_DATA){
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}else{
				$response['status']  = OK;
				$response['message'] = "OK";	
				$response['data'] = $data;	
			}

			echo json_encode($response);
		break;

		/** 등급리스트 조회 */
		case GET_REF_DATA:
			$data =  $cPointState->getData($_REQUEST);

			echo json_encode($data);
		break;

	}
	$cPointState->CloseDB();
?>