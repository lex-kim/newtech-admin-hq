<?php
	require $_SERVER['DOCUMENT_ROOT']."/common/common.php";	
	require $_SERVER['DOCUMENT_ROOT']."/common/class/clsCommon.php";				// 환경설정파일 로딩 - 데이터변수 등
	require "../class/clsVisitingGw.php";
	

	switch( $_REQUEST['REQ_MODE'] ){

		/** 생성 */
		case CASE_CREATE:
			$result = $clsVisitingGw->setData($_REQUEST);
			if($result) {
				$response['status']  = OK;
				$response['message'] = "OK";
			}else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "";
			}

			echo json_encode($response);
		break;

		case CASE_UPDATE:
			$data =  $clsVisitingGw->setData($_REQUEST);

			if($data === HAVE_NO_DATA){
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}else if($data === CONFLICT){
				$response['status']  = CONFLICT;
				$response['message'] = "해당보상담당지정을 가진 담당자가 존재합니다.";
			}else{
				$response['status']  = OK;
				$response['message'] = "OK";	
				$response['data'] = $data;
					
			}

			echo json_encode($response);
		break;

		/** 조회 */
		case CASE_LIST:
			$data =  $clsVisitingGw->getList($_REQUEST);

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

		case CASE_DELETE:
			if($clsVisitingGw->delData($_REQUEST)) {
				$response['status']  = 200;
				$response['message'] = "OK";
			} else {
				$response['status']  = 500;
				$response['message'] = "Internal Server Error";
			}
			
			echo json_encode($response);
		break;

		/** 조회 */
		case CASE_READ:
			$data =  $clsVisitingGw->getData($_REQUEST);

			echo json_encode($data);
		break;

		/** 보험사 리스트 가져오기(order순으로) */
		case GET_INSU_TYPE:
			$data =  $clsVisitingGw->getWorks();

			if($data){
				$response['status']  = OK;
				$response['message'] = "OK";
				$response['data'] = $data;	
			}else{
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";		
			}
			
			echo json_encode($response);
		break;
		
		/** 공업사 정보 가져오기 (이름/아이디)*/
		case GET_GARGE_INFO:
			$data =  $clsVisitingGw->getGarageInfo();

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

	}

	$clsVisitingGw->CloseDB();

?>
