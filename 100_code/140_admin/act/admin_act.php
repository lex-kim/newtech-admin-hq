<?php
	require $_SERVER['DOCUMENT_ROOT']."/common/common.php";				// 환경설정파일 로딩 - 데이터변수 등
	require "../class/clsAdmin.php";
	

	switch( $_REQUEST['REQ_MODE'] ){

		/* 시스템관리자 생성 */
		case CASE_CREATE:

			$result =  $cAdmin->setData($_REQUEST);
			if($result === ERROR){
				$response['status']  = ERROR;
				$response['message'] = "중복된 아이디가 존재합니다.";
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

		/* 시스템관리자 수정 */
		case CASE_UPDATE:
			if($data =  $cAdmin->setData($_REQUEST)) {
				$response['status']  = OK;
				$response['message'] = "OK";
			} else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}	
			echo json_encode($response);
		break;

		/* 시스템관리자 조회 */
		case CASE_LIST:
			if(!$_REQUEST['PER_PAGE']) { $PER_PAGE = LIST_PER_PAGE; $_REQUEST['PER_PAGE'] = LIST_PER_PAGE;}
			if(!$_REQUEST['CURR_PAGE']) { $CURR_PAGE = 1;  $_REQUEST['CURR_PAGE'] = 1; }

			$data =  $cAdmin->getList($_REQUEST);

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

		/* 시스템관리자 전체조회(엑셀다운로드용) */
		case CASE_TOTAL_LIST:
			$data =  $cAdmin->getList($_REQUEST);
	
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

		/* 시스템관리자 해당데이터 삭제 */
		case CASE_DELETE:
			if($data =  $cAdmin->delData($_REQUEST)) {
				$response['status']  = OK;
				$response['message'] = "OK";
			} else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}
			echo json_encode($response);
		break;

	}

	$cAdmin->CloseDB();

?>
