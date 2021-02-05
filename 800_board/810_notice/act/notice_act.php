<?php
	require $_SERVER['DOCUMENT_ROOT']."/common/common.php";				// 환경설정파일 로딩 - 데이터변수 등
	require "../class/clsNotice.php";

	switch( $_REQUEST['REQ_MODE'] ){

		/** 생성 */
		case CASE_CREATE:
			$resultMsg = $clsNotice->setData($_REQUEST,$_FILES);
			genErorMsg($resultMsg);
		break;

		/** 수정 */
		case CASE_UPDATE:
			$resultMsg = $clsNotice->setData($_REQUEST,$_FILES);
			genErorMsg($resultMsg);
		break;

		/** 조회 */
		case CASE_LIST:
			if(!$_REQUEST['PER_PAGE']) { $PER_PAGE = LIST_PER_PAGE; $_REQUEST['PER_PAGE'] = LIST_PER_PAGE;}
			if(!$_REQUEST['CURR_PAGE']) { $CURR_PAGE = 1;  $_REQUEST['CURR_PAGE'] = 1; }

			$data =  $clsNotice->getList($_REQUEST);

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

		/** 특정 공지사항 조회 */
		case CASE_TOTAL_LIST:
			$data =  $clsNotice->getNotice($_REQUEST);
	
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

		/** 해당데이터 삭제 */
		case CASE_DELETE:
			if($data =  $clsNotice->delData($_REQUEST)) {
				$response['status']  = OK;
				$response['message'] = "OK";
			} else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}
			echo json_encode($response);
		break;

		/** 해당데이터 삭제 */
		case CASE_DELETE_FILE:
			if($data =  $clsNotice->delFile($_REQUEST)) {
				$response['status']  = OK;
				$response['message'] = "OK";
			} else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}
			echo json_encode($response);
		break;

		/** 해당데이터 삭제 */
		case CASE_DOWNLOAD_FILE:
			if($data =  $clsNotice->downFile($_REQUEST)) {
				$response['status']  = OK;
				$response['message'] = "OK";
			} else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}
			echo json_encode($response);
		break;

		/* VIEW_CNT +1 */
		case CASE_READ:
			$data =  $clsNotice->getData($_REQUEST);
	
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

		/* 모드 불러오기 */
		case GET_REF_MAIN_TARGET_TYPE:
			$data =  $clsNotice->getMode($_REQUEST);
	
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


		default:
		echo 'defualt error';
		break;

	}

	$clsNotice->CloseDB();

?>
