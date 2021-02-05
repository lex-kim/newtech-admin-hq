<?php
	require $_SERVER['DOCUMENT_ROOT']."/common/common.php";				// 환경설정파일 로딩 - 데이터변수 등
	require "../class/clsRegion.php";

	/* AJAX ROUTER */
	switch( $_REQUEST['REQ_MODE'] ){

		case CASE_UPDATE:
			if($data =  $cRegion->setData($_REQUEST)) {
				$response['status']  = OK;
				$response['message'] = "OK";
			} else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}	
			echo json_encode($response);
		break;

		case CASE_LIST:
			if(!$_REQUEST['PER_PAGE']) { $PerPage = LIST_PER_PAGE; $_REQUEST['PER_PAGE'] = LIST_PER_PAGE;}
			if(!$_REQUEST['CURR_PAGE']) { $CurPAGE = 1;  $_REQUEST['CURR_PAGE'] = 1; }

			$data =  $cRegion->getList($_REQUEST);

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

		case CASE_TOTAL_LIST:
			$data =  $cRegion->getList($_REQUEST);
	
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
			if($cRegion->delData($_REQUEST)) {
				$response['status']  = 200;
				$response['message'] = "OK";
			} else {
				$response['status']  = 500;
				$response['message'] = "Internal Server Error";
			}
		break;

	}

	$cRegion->CloseDB();

?>
