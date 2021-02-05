<?php
	require $_SERVER['DOCUMENT_ROOT']."/common/common.php";				// 환경설정파일 로딩 - 데이터변수 등
	require "../class/clsIwmngt.php";

	/* 전화번호 '-' 제거 */
	$_REQUEST['CHG_KEYWORD'] = str_replace("-", "", $_REQUEST['CHG_KEYWORD']); 		//검색 키워드	
	$_REQUEST['MII_CHG_TEL1']= str_replace("-", "", $_REQUEST['MII_CHG_TEL1']); 	//담당자 사무실번호
	$_REQUEST['MII_CHG_TEL2'] = str_replace("-", "", $_REQUEST['MII_CHG_TEL2']); 	//담당자 휴대폰
	$_REQUEST['MII_BOSS_TEL'] = str_replace("-", "", $_REQUEST['MII_BOSS_TEL']); 	//부서장 휴대폰

	if(!$_REQUEST['PER_PAGE']) { $PER_PAGE = LIST_PER_PAGE; $_REQUEST['PER_PAGE'] = LIST_PER_PAGE;}
	if(!$_REQUEST['CURR_PAGE']) { $CURR_PAGE = 1;  $_REQUEST['CURR_PAGE'] = 1; }

	switch( $_REQUEST['REQ_MODE'] ){

		/** 생성 */
		case CASE_CREATE:
			$result = $cIwmngt->setData($_REQUEST);
			if($result === ERROR){
				$response['status']  = ERROR;
				$response['message'] = "중복된 아이디가 존재합니다.";
			}else if($result === CONFLICT){
				$response['status']  = CONFLICT;
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

		/** 수정 */
		case CASE_UPDATE:
			$result = $cIwmngt->setData($_REQUEST);
			if($result === ERROR){
				$response['status']  = ERROR;
				$response['message'] = "중복된 아이디가 존재합니다.";
			}else if($result === CONFLICT){
				$response['status']  = CONFLICT;
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

		/** 조회 */
		case CASE_LIST:
			$data =  $cIwmngt->getList($_REQUEST);
			
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

		/** 변경이력 조회 */
		case CASE_LIST_SUB:
			$data =  $cIwmngt->getSubList($_REQUEST);
	
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

		
		/** 보험사 > 팀 List  조회 */
		case CASE_LIST_DETAIL:
			$data =  $cIwmngt->getTeamList($_REQUEST);
	
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
			if($data =  $cIwmngt->delData($_REQUEST)) {
				$response['status']  = OK;
				$response['message'] = "OK";
			} else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}
			echo json_encode($response);
		break;

		/** 보험사분류 조회 */
		case GET_INSU_TYPE:
			if($data =  $cIwmngt->getInsuType($_REQUEST)) {
				$response['status']  = OK;
				$response['message'] = "OK";
				$response['data'] = $data;		
			} else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}
			echo json_encode($response);
		break;

		/** 보험사팀조회 */
		case GET_INSU_TEAM:
			$data =  $cIwmngt->getInsuTeam($_REQUEST);
	
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
		
		/** 담당지역 업데이트 */
		case CASE_UPDATE_REGION:
			$data =  $cIwmngt->setRegion($_REQUEST);
			// echo var_dump($data);
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
		
		
		/** 보험사팀 지역 조회 */
		case GET_REQUEST_REGION:
			if($data =  $cIwmngt->getTeamRegion($_REQUEST)) {
				$response['status']  = OK;
				$response['message'] = "OK";
				$response['data'] = $data;		
			} else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}
			
			echo json_encode($response);
		break;
		
		/** 보험사 지역 조회 */
		case CASE_READ:
			if($data =  $cIwmngt->getRegion($_REQUEST)) {
				$response['status']  = OK;
				$response['message'] = "OK";
				$response['data'] = $data;		
			} else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}
			
			echo json_encode($response);
		break;
		
		case CASE_CONFLICT_CHECK:
			$result = $cIwmngt->setTrans($_REQUEST);
			if($result) {
				$response['status']  = OK;
				$response['message'] = "OK";
			} else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "이관에 실패하였습니다. 관리자 문의바랍니다.";
			}
			echo json_encode($response);
		break;


		default:
		echo 'defualt error';
		break;

	}

	$cIwmngt->CloseDB();

?>
