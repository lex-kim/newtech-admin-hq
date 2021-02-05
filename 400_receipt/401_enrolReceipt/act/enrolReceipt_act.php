<?php
	require $_SERVER['DOCUMENT_ROOT']."/common/common.php";				// 환경설정파일 로딩 - 데이터변수 등
    require "../class/clsEnrolReceipt.php";
    
	switch( $_REQUEST['REQ_MODE'] ){
        /* 공업사 정보 불러오기 */
        case CASE_READ:
            if($_REQUEST['REQ_TARGET'] == STOCK_BILL ){
                $data =  $cEnrol->getStock($_REQUEST);
            }else{
                $data =  $cEnrol->getData($_REQUEST);
            }
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
		
		case CASE_LIST_DETAIL:
			$data = $cEnrol->getEnrollData($_REQUEST);
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
        
		/** 생성 */
		case CASE_CREATE:
			$result = $cEnrol->setData($_REQUEST);
			if($result === HAVE_NO_DATA){
				$response['status']  = HAVE_NO_DATA;
				$response['message'] = "입출고 등록 권한이 없는 아이디입니다.";
			}else if($result === CONFLICT){
				$response['status']  = CONFLICT;
				$response['message'] = "이미 등록한 내용이 있습니다. 입출고관리에서 수정해주세요.";
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
			if($_REQUEST['UPDATE_KIND'] == PART || $_REQUEST['UPDATE_KIND'] == PART_DEFAULT ){
				$result = $cEnrol->setPart($_REQUEST);
			}else {
				$result = $cEnrol->setData($_REQUEST);
			}
			
			if($result === HAVE_NO_DATA){
				$response['status']  = HAVE_NO_DATA;
				$response['message'] = "입출고 등록 권한이 없는 아이디입니다.";
			}else if($result === CONFLICT){
				$response['status']  = CONFLICT;
				$response['message'] = "이미 등록한 내용이 있습니다. 입출고관리에서 수정해주세요.";
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
        
		
		/** 지역 조회 */
		case GET_REQUEST_REGION:
			if($data =  $cEnrol->getRegion($_REQUEST)) {
				$response['status']  = OK;
				$response['message'] = "OK";
				$response['data'] = $data;		
			} else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}
			
			echo json_encode($response);
        break;
        
		/** 공업사 조회 */
		case GET_GARGE_INFO:
			$data =  $cEnrol->getGw($_REQUEST);
	
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
		
		case OPTIONS_GW :
			$data =  $cEnrol->getOptionGarage($_REQUEST);
	
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
        
		/** 독립정산 */
		case STOCK_INDE:
			if($data =  $cEnrol->setIndependent($_REQUEST)) {
				$response['status']  = OK;
				$response['message'] = "OK";
				$response['data'] = $data;		
			} else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}
			echo json_encode($response);
		break;
		

		/** 대체정산 */
		case STOCK_SUBST:
			if($data =  $cEnrol->setSubstitution($_REQUEST)) {
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
	$cEnrol->CloseDB();
?>