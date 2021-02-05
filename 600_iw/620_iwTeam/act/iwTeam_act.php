<?php
	require $_SERVER['DOCUMENT_ROOT']."/common/common.php";				// 환경설정파일 로딩 - 데이터변수 등
    require "../class/clsIwTeam.php";
    
	/* 전화번호 '-' 제거 */
	$_REQUEST['MIT_TEAM_TEL_AES'] = str_replace("-", "", $_REQUEST['MIT_TEAM_TEL_AES']); 		//검색 키워드	
	$_REQUEST['MIT_TEAM_FAX_AES']= str_replace("-", "", $_REQUEST['MIT_TEAM_FAX_AES']); 	//담당자 사무실번호
	$_REQUEST['MIT_BOSS_TEL_AES'] = str_replace("-", "", $_REQUEST['MIT_BOSS_TEL_AES']); 	//담당자 휴대폰
    $_REQUEST['MIT_CHG_FAX_AES'] = str_replace("-", "", $_REQUEST['MIT_CHG_FAX_AES']); 	//부서장 휴대폰
    $_REQUEST['MIT_CHG_TEL_AES'] = str_replace("-", "", $_REQUEST['MIT_CHG_TEL_AES']); 	//부서장 휴대폰
    
	if(!$_REQUEST['PER_PAGE']) { $PER_PAGE = LIST_PER_PAGE; $_REQUEST['PER_PAGE'] = LIST_PER_PAGE;}
    if(!$_REQUEST['CURR_PAGE']) { $CURR_PAGE = 1;  $_REQUEST['CURR_PAGE'] = 1; }
	
	switch( $_REQUEST['REQ_MODE'] ){
		/** 생성 */
		case CASE_CREATE:
			$result = $cIwTeam->setData($_REQUEST);
			if($result === ERROR){
				$response['status']  = ERROR;
				$response['message'] = "중복된 아이디가 존재합니다.";
			}else{
				if($result) {
					$response['status']  = OK;
					$response['message'] = "OK";
				} else {
					$response['status']  = HAVE_NO_DATA;
					$response['message'] = ERROR_MSG_NO_DATA;
				}
			}	
			echo json_encode($response);
        break;
        
		/** 수정 */
		case CASE_UPDATE:
			$result = $cIwTeam->setData($_REQUEST);
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
        
		/** 조회 */
		case CASE_LIST:
			$data =  $cIwTeam->getList($_REQUEST);
			// echo var_dump($data);
			if($data === HAVE_NO_DATA){
				$response['status']  = HAVE_NO_DATA;
				$response['message'] = ERROR_MSG_NO_DATA;
			}else{
				$response['status']  = OK;
				$response['message'] = "OK";	
				$response['data'] = $data;	
			}	
			
			echo json_encode($response);
		break;

		/** 팀 신규 조회 */
		case CASE_UPDATE_TEAM:
			$data =  $cIwTeam->setTeam($_REQUEST);
			// echo var_dump($data);
			if($data === HAVE_NO_DATA){
				$response['status']  = SERVER_ERROR;
				$response['message'] = ERROR_MSG_NO_DATA;
			}else{
				$response['status']  = OK;
				$response['message'] = "OK";	
				$response['data'] = $data;	
			}	
			
			echo json_encode($response);
		break;

		/** 팀 신규 조회 */
		case CASE_UPDATE_REGION:
			$data =  $cIwTeam->setRegion($_REQUEST);
			// echo var_dump($data);
			if($data === HAVE_NO_DATA){
				$response['status']  = HAVE_NO_DATA;
				$response['message'] = ERROR_MSG_NO_DATA;
			}else{
				$response['status']  = OK;
				$response['message'] = "OK";	
				$response['data'] = $data;	
			}	
			
			echo json_encode($response);
		break;
		
		
		/** 팀 지역 조회 */
		case GET_REQUEST_REGION:
			if($data =  $cIwTeam->getTeamRegion($_REQUEST)) {
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
			if($data =  $cIwTeam->getRegion($_REQUEST)) {
				$response['status']  = OK;
				$response['message'] = "OK";
				$response['data'] = $data;		
			} else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}
			
			echo json_encode($response);
        break;
        
		/** 변경이력 조회 */
		case CASE_LIST_SUB:
			$data =  $cIwTeam->getSubList($_REQUEST);
	
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
			if($data =  $cIwTeam->delData($_REQUEST)) {
				$response['status']  = OK;
				$response['message'] = "OK";
			} else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}
			echo json_encode($response);
        break;
        
		/** 보험사팀조회 */
		case GET_INSU_TEAM:
			if($data =  $cIwTeam->getInsuTeam($_REQUEST)) {
				$response['status']  = OK;
				$response['message'] = "OK";
				$response['data'] = $data;		
			} else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}
			echo json_encode($response);
        break;
        
		default:
			echo 'iwTeam defualt error';
		break;
	}
	$cIwTeam->CloseDB();
?>