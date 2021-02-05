<?php
	require $_SERVER['DOCUMENT_ROOT']."/common/common.php";				// 환경설정파일 로딩 - 데이터변수 등
	require "../class/clsGwMngt.php";
	require "../../../common/class/clsSearchOptions.php";

	switch( $_REQUEST['REQ_MODE'] ){

		/** 생성 */
		case CASE_CREATE:
			$result = $clsGwManagement->setData($_REQUEST);
			
			if($result['status'] === CONFLICT || $result['status'] === ALREADY_EXISTS){
				$response['status']  = $result['status'] ;
				$response['message'] = $result['msg'];
				$response['col'] = $result['col'];
			}else if($result['status'] == ERROR){
				$response['status']  = ERROR;
				$response['message'] = $result['msg'];
				$response['col'] = $result['col'];
			}else if($result['status'] == SERVER_ERROR){
				$response['status']  = SERVER_ERROR;
				$response['message'] = "";
			}else{
				$response['status']  = OK;
				$response['message'] = "OK";
			}

			echo json_encode($response);
		break;

		/** 수정 */
		case CASE_UPDATE:
			$result = $clsGwManagement->setData($_REQUEST);
			
			if($result['status'] === CONFLICT || $result['status'] === ALREADY_EXISTS){
				$response['status']  = $result['status'] ;
				$response['message'] = $result['msg'];
				$response['col'] = $result['col'];
			}else if($result['status'] == ERROR){
				$response['status']  = ERROR;
				$response['message'] = $result['msg'];
				$response['col'] = $result['col'];
			}else if($result['status'] == SERVER_ERROR){
				$response['status']  = SERVER_ERROR;
				$response['message'] = "";
			}else{
				$response['status']  = OK;
				$response['message'] = "OK";
			}

			echo json_encode($response);
		break;

		/** 조회 */
		case CASE_READ:
			$data =  $clsGwManagement->getData($_REQUEST);

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
			if(!$_REQUEST['PER_PAGE']) { $PER_PAGE = LIST_PER_PAGE; $_REQUEST['PER_PAGE'] = LIST_PER_PAGE;}
			if(!$_REQUEST['CURR_PAGE']) { $CURR_PAGE = 1;  $_REQUEST['CURR_PAGE'] = 1; }

			$data =  $clsGwManagement->getList($_REQUEST);

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

		/** 전체조회(엑셀다운로드용) */
		case CASE_TOTAL_LIST:
			$data =  $clsGwManagement->getTotalList();
	
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

		/** 해당데이터 삭제 */
		case CASE_DELETE:
			if($data =  $clsGwManagement->delData($_REQUEST)) {
				$response['status']  = OK;
				$response['message'] = "OK";
			} else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}
			echo json_encode($response);
		break;

		/** 공금 및 사용부품 조회*/
		case GET_PARTS_TYPE:
			$data = $clsGwManagement->getPartsInfo($_REQUEST);
			
			if($data === HAVE_NO_DATA){
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}else if ($data === ERROR){
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}else{
				$response['status']  = OK;
				$response['message'] = "OK";	
				$response['data'] = $data;		
			}
			
			echo json_encode($response);
		break;

		/** 3개월 거래평균*/
		case GET_PARTS_INFO:
			$data = $clsGwManagement->getStdParts($_REQUEST);
			
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

		/** 기준부품 리스트 */
		case GET_REF_DATA:
			$data = $clsGwManagement->getCodeParts();
			
			echo json_encode($data);
		break;

	}

	$clsGwManagement->CloseDB();

?>
