<?php
	require $_SERVER['DOCUMENT_ROOT']."/common/common.php";				// 환경설정파일 로딩 - 데이터변수 등
	require "../class/clsPurchase.php";

	switch( $_REQUEST['REQ_MODE'] ){

		/** 생성 */
		case CASE_CREATE:
			$result = $cPurchase->setData($_REQUEST);
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
			$result = $cPurchase->setData($_REQUEST);
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

		/* 장비 입고 등록 */
		case CASE_UPDATE_TEAM:
			$result = $cPurchase->addStock($_REQUEST);
			if($result === CONFLICT){
				$response['status']  = CONFLICT;
				$response['message'] = "입고일에 이미 입고된 장비가 존재합니다.";
			}else{
				if($result) {
					$response['status']  = OK;
					$response['message'] = "OK";
				} else {
					$response['status']  = SERVER_ERROR;
					$response['message'] = "SERVER_ERROR";
				}
			}	

			echo json_encode($response);
		break;

		/** 조회 */
		case CASE_LIST:
			if(!$_REQUEST['PER_PAGE']) { $PER_PAGE = LIST_PER_PAGE; $_REQUEST['PER_PAGE'] = LIST_PER_PAGE;}
			if(!$_REQUEST['CURR_PAGE']) { $CURR_PAGE = 1;  $_REQUEST['CURR_PAGE'] = 1; }

			$data =  $cPurchase->getList($_REQUEST);

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

		/** 클릭한 해당 데이터 gET */
		case CASE_READ:
			$data =  $cPurchase->getData($_REQUEST);

			if($data === HAVE_NO_DATA){
				$response['status']  = SERVER_ERROR;
				$response['message'] = "HAVE_NO_DATA";
			}else{
				$response['status']  = OK;
				$response['message'] = "OK";	
				$response['data'] = $data;		
			}
			
			echo json_encode($response);
		break;

		/** 해당데이터 삭제 */
		case CASE_DELETE:
			if($data =  $cPurchase->delData($_REQUEST)) {
				$response['status']  = OK;
				$response['message'] = "OK";
			} else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "HAVE_NO_DATA";
			}
			echo json_encode($response);
		break;


	}

	$cPurchase->CloseDB();

?>
