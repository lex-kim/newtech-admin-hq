<?php
	require $_SERVER['DOCUMENT_ROOT']."/common/common.php";	
	require $_SERVER['DOCUMENT_ROOT']."/common/class/clsCommon.php";				// 환경설정파일 로딩 - 데이터변수 등
	require "../class/clsSendList.php";
	

	switch( $_REQUEST['REQ_MODE'] ){

		/** 생성 */
		case CASE_CREATE:
			$result = $clsSendList->setData($_REQUEST);
			if($result) {
				$response['status']  = OK;
				$response['message'] = "OK";
			}else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "";
			}

			echo json_encode($response);
		break;

		case CASE_UPDATE:
			$data =  $clsSendList->setData($_REQUEST);

			if($data === HAVE_NO_DATA){
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}else if($data === CONFLICT){
				$response['status']  = CONFLICT;
				$response['message'] = "해당보상담당지정을 가진 담당자가 존재합니다.";
			}else{
				$response['status']  = OK;
				$response['message'] = "OK";	
				$response['data'] = $data;
					
			}

			echo json_encode($response);
		break;

		/** 엑셀다운로드 */
		case CASE_TOTAL_LIST:
			$data =  $clsSendList->getList($_REQUEST);

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
			$data =  $clsSendList->getList($_REQUEST);

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

		/** 집계 조회 */
		case CASE_LIST_SUB:
			$data =  $clsSendList->getSummary($_REQUEST) ;
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
			$data =  $clsSendList->getData($_REQUEST);

			echo json_encode($data);
		break;


		case CASE_DELETE:
			$data =  $clsSendList->delData($_REQUEST);

			if($data){
				$response['status']  = OK;
				$response['message'] = "OK";
			}else{
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";		
			}
			
			echo json_encode($response);
		break;

		case CASE_CONFLICT_CHECK:
			$data =  $clsSendList->onReleaseConfilctBill($_REQUEST);

			if($data){
				$response['status']  = OK;
				$response['message'] = "OK";
			}else{
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";		
			}
			
			echo json_encode($response);
		break;
		
		/** 팀재분류메모 */
		case CASE_UPDATE_TEAM : 
			$data =  $clsSendList->setInsuTeamMemo($_REQUEST);

			if($data){
				$response['status']  = OK;
				$response['message'] = "OK";
			}else{
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
				
			}
			
			echo json_encode($response);
		break;

		case GET_INSU_TEAM : 
			$data =  $clsSendList->getInsuTeam($_REQUEST);

			if($data){
				$response['status']  = OK;
				$response['message'] = "OK";	
				$response['data'] = $data;	
			}else{
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
				
			}
			
			echo json_encode($response);
		break;

		
		/** 공업사 정보 가져오기 (이름/아이디)*/
		case GET_GARGE_INFO:
			$data =  $clsSendList->getGarageInfo();

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

		case CASE_UPDATE_REGION:
			$data =  $clsSendList->setGwFax($_REQUEST);

			if($data){
				$response['status']  = OK;
				$response['message'] = "OK";
			}else{
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}

			echo json_encode($response);
		break;

	}

	$clsSendList->CloseDB();

?>
