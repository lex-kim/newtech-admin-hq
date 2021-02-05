<?php
	require $_SERVER['DOCUMENT_ROOT']."/common/common.php";				// 환경설정파일 로딩 - 데이터변수 등
	require "../class/clsIwFaoManagement.php";
	require "../../../common/class/clsSearchOptions.php";

	switch( $_REQUEST['REQ_MODE'] ){

		/** 생성 */
		case CASE_CREATE:
			$result = $clsIwFaoManagement->setData($_REQUEST);

			if($data === HAVE_NO_DATA){
				$response['status']  = HAVE_NO_DATA;
				$response['message'] = ERROR_MSG_NO_DATA;
			}else{
				$response['status']  = OK;
				$response['message'] = "OK";
			}

			echo json_encode($response);
		break;

		/** 수정 */
		case CASE_UPDATE:
			$result = $clsIwFaoManagement->setData($_REQUEST);

			if($data === HAVE_NO_DATA){
				$response['status']  = HAVE_NO_DATA;
				$response['message'] = ERROR_MSG_NO_DATA;
			}else{
				$response['status']  = OK;
				$response['message'] = "OK";
			}

			echo json_encode($response);
		break;


		/** 조회 */
		case CASE_LIST:
			if(!$_REQUEST['PER_PAGE']) { $PER_PAGE = LIST_PER_PAGE; $_REQUEST['PER_PAGE'] = LIST_PER_PAGE;}
			if(!$_REQUEST['CURR_PAGE']) { $CURR_PAGE = 1;  $_REQUEST['CURR_PAGE'] = 1; }

			$data =  $clsIwFaoManagement->getList($_REQUEST);

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
			$data =  $clsIwFaoManagement->getTotalList();
	
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
			if($data =  $clsIwFaoManagement->delData($_REQUEST)) {
				$response['status']  = OK;
				$response['message'] = "OK";
			} else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}
			echo json_encode($response);
		break;

		/** 공급사 정보조회(청구담당자 조회)*/
		case GET_GARAGE_CLAIM_INFO:
			$data = $clsIwFaoManagement->getPartsInfo($_REQUEST);
			
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

		/** 중복체크 */
		case CASE_CONFLICT_CHECK:
			$data = $clsIwFaoManagement->onCheckRequireData($_REQUEST);
			
			if($data){
				$response['status']  = OK;
				$response['message'] = "OK";	
			}else{
				$response['status']  = CONFLICT;
				$response['message'] = "CONFLICT";
			}
			
			echo json_encode($response);
		break;

		/** 보험사팀조회 */
		case GET_INSU_TEAM:
			$data =  $clsIwFaoManagement->getInsuTeam($_REQUEST);

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

	$clsIwFaoManagement->CloseDB();

?>
