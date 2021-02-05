<?php
	require $_SERVER['DOCUMENT_ROOT']."/common/common.php";				// 환경설정파일 로딩 - 데이터변수 등
	require "../class/clsCpstState.php";
    
	switch( $_REQUEST['REQ_MODE'] ){

		/* 테이블 동적 생성을 위한 헤더 CNT 조회 */
		case GET_HEADER_CNT:
			$data =  $cCpst->getHeaderCnt($_REQUEST);

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
		 case CASE_LIST:
			$data =  $cCpst->getList($_REQUEST);

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

		/** 공업사별 보험사 리스트 */
		case CASE_LIST_DETAIL:
			$data =  $cCpst->getDetailList($_REQUEST);

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

		default:
			echo 'clsCpstState default in';	
		break;	


    }

	$cCpst->CloseDB();

?>
