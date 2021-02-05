<?php
	require $_SERVER['DOCUMENT_ROOT']."/common/common.php";				// 환경설정파일 로딩 - 데이터변수 등
    require "../class/clsSpecification.php";

	if(!$_REQUEST['PER_PAGE']) { $PER_PAGE = LIST_PER_PAGE; $_REQUEST['PER_PAGE'] = LIST_PER_PAGE;}
    if(!$_REQUEST['CURR_PAGE']) { $CURR_PAGE = 1;  $_REQUEST['CURR_PAGE'] = 1; }
	
	switch( $_REQUEST['REQ_MODE'] ){

        /* 테이블 동적 생성을 위한 헤더 조회 */
		case GET_HEADER_CNT:
			$data =  $cSpecification->getData($_REQUEST);

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
        
		/** 수정 */
		case CASE_UPDATE:
			$result = $cSpecification->setData($_REQUEST);
			if($result) {
                $response['status']  = OK;
                $response['message'] = "OK";
            } else {
                $response['status']  = SERVER_ERROR;
                $response['message'] = "";
            }
			echo json_encode($response);
        break;
        
		/** 조회 */
		case CASE_LIST:
			$data =  $cSpecification->getList($_REQUEST);
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

		
		/** 공업사- 변경일자별 입출고기록 조회 */
		case CASE_LIST_SUB:
			$data =  $cSpecification->getSubList($_REQUEST);
	
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
			if($data =  $cSpecification->delData($_REQUEST)) {
				$response['status']  = OK;
				$response['message'] = "OK";
			} else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}
			echo json_encode($response);
        break;
        
		default:
			echo 'iwTeam defualt error';
		break;

		/** 조회 */
		case CASE_LIST:
			$data =  $cSpecification->getList($_REQUEST);
			
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

		case CASE_UPDATE_TEAM:
            $result = $cSpecification->setProc($_REQUEST);
            
			if($result) {
                $response['status']  = OK;
                $response['message'] = "OK";
            } else {
                $response['status']  = SERVER_ERROR;
                $response['message'] = "";
            }
			echo json_encode($response);
        break;


	}
	
	$cSpecification->CloseDB();
?>