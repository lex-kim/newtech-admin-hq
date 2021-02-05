<?php
	require $_SERVER['DOCUMENT_ROOT']."/common/common.php";				// 환경설정파일 로딩 - 데이터변수 등
	require "../class/clsDivision.php";
	
	/* AJAX ROUTER */
	switch( $_REQUEST['REQ_MODE'] ){

		
		/** 생성 */
		case CASE_CREATE:
			$result = $cDivision->setData($_REQUEST);
			if($result === CONFLICT){
				$response['status']  = CONFLICT;
				$response['message'] = "중복된 코드가 존재합니다.";
			}else if($result === ERROR){
				$response['status']  = ERROR;
				$response['message'] = "중복된 출력순서가 존재합니다.";
			}else{
				if($result) {
					$response['status']  = OK;
					$response['message'] = "OK";
				}else {
					$response['status']  = SERVER_ERROR;
					$response['message'] = "";
				}
			}	
			echo json_encode($response);
		break;
		
		/** 수정 */
		case CASE_UPDATE:
			$result = $cDivision->setData($_REQUEST);
			if($result === ERROR){
				$response['status']  = ERROR;
				$response['message'] = "중복된 출력순서가 존재합니다.";
			}else{
				if($result) {
					$response['status']  = OK;
					$response['message'] = "OK";
				} else {
					$response['status']  = SERVER_ERROR;
					$response['message'] = "";
				}
			}	
			echo json_encode($response);
		break;

		case CASE_LIST:
			if(!$_REQUEST['PerPage']) { $PerPage = LIST_PER_PAGE; $_REQUEST['PerPage'] = LIST_PER_PAGE;}
			if(!$_REQUEST['CurPAGE']) { $CurPAGE = 1;  $_REQUEST['CurPAGE'] = 1; }

			$data =  $cDivision->getList($_REQUEST);

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

		case CASE_TOTAL_LIST:
			$data =  $cDivision->getList($_REQUEST);
	
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
			if($cDivision->delData($_REQUEST)) {
				$response['status']  = 200;
				$response['message'] = "OK";
			} else {
				$response['status']  = 500;
				$response['message'] = "Internal Server Error";
			}
			echo json_encode($response);
		break;

	}

	$cDivision->CloseDB();

?>
