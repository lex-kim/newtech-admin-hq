<?php
	require $_SERVER['DOCUMENT_ROOT']."/common/common.php";				// 환경설정파일 로딩 - 데이터변수 등
	require "../class/clsPointMngt.php";
	switch( $_REQUEST['REQ_MODE'] ){
		/** 저장(적립 및 사용등록) */
		case CASE_CREATE:
			$result = $cPointMngt->setAddPoint($_REQUEST);
			if($result) {
				$response['status']  = OK;
				$response['message'] = "OK";
			}else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}

			echo json_encode($response);
		break;	


		/** 수정 */
		case CASE_UPDATE:
			if($cPointMngt->setData($_REQUEST)) {
				$response['status']  = OK;
				$response['message'] = "OK";
			} else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}	
			echo json_encode($response);
		break;

		case CASE_LIST:
			$data = $cPointMngt->getList($_REQUEST);
			if($data === HAVE_NO_DATA){
				$response['status']  = HAVE_NO_DATA;
				$response['message'] = "HAVE_NO_DATA";
			}else{
				$response['status']  = OK;
				$response['message'] = "OK";	
				$response['data'] = $data;		
			}
			
			echo json_encode($response);
		break;

		case CASE_DELETE:
			if($data =  $cPointMngt->delData($_REQUEST)) {
				$response['status']  = OK;
				$response['message'] = "OK";
			} else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}
			echo json_encode($response);
        break;

		/*업체명 조회 */
		case GET_GARAGE_CLAIM_INFO:
			$data = $cPointMngt->getGarageInfo($_REQUEST);
			
			if($data === HAVE_NO_DATA){
				$response['status']  = HAVE_NO_DATA;
				$response['message'] = "HAVE_NO_DATA";
			}else{
				$response['status']  = OK;
				$response['message'] = "OK";	
				$response['data'] = $data;		
			}
			
			echo json_encode($response);
		break;
		
		/*적립,사용등록창 구분값 조회 */
		case REF_POINT_STATUS:
			$data = $cPointMngt->getPointStatus($_REQUEST);
			if($data === HAVE_NO_DATA){
				$response['status']  = HAVE_NO_DATA;
				$response['message'] = "HAVE_NO_DATA";
			}else{
				$response['status']  = OK;
				$response['message'] = "OK";	
				$response['data'] = $data;		
			}
			
			echo json_encode($response);
		break;
		
	}
	$cPointMngt->CloseDB();
?>