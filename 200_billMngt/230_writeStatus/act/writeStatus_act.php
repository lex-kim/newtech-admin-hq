<?php
	require $_SERVER['DOCUMENT_ROOT']."/common/common.php";	
	require $_SERVER['DOCUMENT_ROOT']."/common/class/clsCommon.php";				// 환경설정파일 로딩 - 데이터변수 등
	require "../class/clsWriteStatus.php";
	

	switch( $_REQUEST['REQ_MODE'] ){

		/** 생성 */
		case CASE_CREATE:
			$result = $clsWriteStatus->setData($_REQUEST);
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
			$data =  $clsWriteStatus->setData($_REQUEST);

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
			$data =  $clsWriteStatus->getList($_REQUEST);

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

		/** 해당년도 */
		case CASE_LIST_SUB:
			$data =  $clsWriteStatus->getClaimYearOption($_REQUEST);
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

		/** 조회 */
		case CASE_READ:
			$data =  $clsWriteStatus->getData($_REQUEST);

			echo json_encode($data);
		break;

		/** 보험사 리스트 가져오기(order순으로) */
		case GET_INSU_TYPE:
			$data =  $clsWriteStatus->getWorks();

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
			$data =  $clsWriteStatus->getGarageInfo();

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

	$clsWriteStatus->CloseDB();

?>
