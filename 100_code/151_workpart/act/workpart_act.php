<?php
	require $_SERVER['DOCUMENT_ROOT']."/common/common.php";				// 환경설정파일 로딩 - 데이터변수 등
	require "../class/clsWorkpart.php";

	switch( $_REQUEST['REQ_MODE'] ){

		/** 등록 */
		case CASE_CREATE:
			$result = $clsWorkpart->setData($_REQUEST);
			if($result){
				$response['status']  = OK;
				$response['message'] = "OK";
				
			}else{
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}
			echo json_encode($response);
		break;

		/** 수정 */
		case CASE_UPDATE:
			$result = $clsWorkpart->setData($_REQUEST);
			
			if($result === CONFLICT){
				$response['status']  = CONFLICT;
				$response['message'] = "해당공업사에 등록된 보상담당자가 존재합니다.";
			}else{
				if($result){
					$response['status']  = OK;
					$response['message'] = "OK";
					
				}else{
					$response['status']  = SERVER_ERROR;
					$response['message'] = "Internal Server Error";
				}
			}

			echo json_encode($response);
		break;

		/** 삭제 */
		case CASE_DELETE:
			$result = $clsWorkpart->delData($_REQUEST);
			
			// if($result){
				$response['status']  = OK;
				$response['message'] = "OK";
				
			// }else{
			// 	$response['status']  = SERVER_ERROR;
			// 	$response['message'] = "Internal Server Error";
			// }

			echo json_encode($response);
		break;

		/** 조회 */
		case CASE_LIST:
			$data =  $clsWorkpart->getList($_REQUEST);

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
			$data =  $clsWorkpart->getData($_REQUEST);

			echo json_encode($data);
		break;

		/** 기준부품 조회 */
		case GET_PARTS_INFO:
			$data = $clsWorkpart->getStdParts($_REQUEST);
			
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


		/** 교환/판금 */
		case GET_REF_RECKON_TYPE:
			$data = $clsWorkpart->getRefInfo($_REQUEST);
			
			if($data === HAVE_NO_DATA){
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}else if($data === ERROR){
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}else{
				$response['status']  = OK;
				$response['message'] = "OK";	
				$response['data'] = $data;		
			}
			
			echo json_encode($response);
		break;

		/** 작업항목관리 우선순위 */
		case GET_REF_WORK_PARIORITY_TYPE:
			$data = $clsWorkpart->getRefInfo($_REQUEST);
			
			if($data === HAVE_NO_DATA){
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}else if($data === ERROR){
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

	$clsWorkpart->CloseDB();

?>
