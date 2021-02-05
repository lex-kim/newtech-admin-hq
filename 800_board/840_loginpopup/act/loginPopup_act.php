<?php
	require $_SERVER['DOCUMENT_ROOT']."/common/common.php";				// 환경설정파일 로딩 - 데이터변수 등
	require "../class/clsLoginPopup.php";
	switch( $_REQUEST['REQ_MODE'] ){
		/** 생성 */
		case CASE_CREATE:
			if($clsLoginPopup->setData($_REQUEST)) {
				$response['status']  = OK;
				$response['message'] = "OK";
			} else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "";
			}	

			echo json_encode($response);
		break;
		/** 수정 */
		case CASE_UPDATE:
			if($data =  $clsLoginPopup->setData($_REQUEST)) {
				$response['status']  = OK;
				$response['message'] = "OK";
			} else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}	
			echo json_encode($response);
		break;
		/** 조회 */
		case CASE_LIST:
			if(!$_REQUEST['PER_PAGE']) { $PER_PAGE = LIST_PER_PAGE; $_REQUEST['PER_PAGE'] = LIST_PER_PAGE;}
			if(!$_REQUEST['CURR_PAGE']) { $CURR_PAGE = 1;  $_REQUEST['CURR_PAGE'] = 1; }
			$data =  $clsLoginPopup->getList($_REQUEST);
			
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
		/** 삭제 */
		case CASE_DELETE:
			$result = $clsLoginPopup->delData($_REQUEST);
			
			if($result){
				$response['status']  = OK;
				$response['message'] = "OK";
				
			}else{
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}
			echo json_encode($response);
		break;
		
		/** 클릭한 해당 데이터 get */
		case CASE_READ:
			$data =  $clsLoginPopup->getData($_REQUEST);

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
	$clsLoginPopup->CloseDB();
?>