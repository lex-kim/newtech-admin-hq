<?php
	require $_SERVER['DOCUMENT_ROOT']."/common/common.php";				// 환경설정파일 로딩 - 데이터변수 등
    require "../class/clsTransmit.php";
    
    if(!$_REQUEST['PER_PAGE']) { $PER_PAGE = LIST_PER_PAGE; $_REQUEST['PER_PAGE'] = LIST_PER_PAGE;}
	if(!$_REQUEST['CURR_PAGE']) { $CURR_PAGE = 1;  $_REQUEST['CURR_PAGE'] = 1; }

	switch( $_REQUEST['REQ_MODE'] ){
        
		/** 이첩관리 리스트 조회 */
		case CASE_LIST:
			$data =  $cTrans->getList($_REQUEST);
			
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
		
		/** 이첩이력 리스트 조회 */
		case CASE_LIST_SUB:
			$data =  $cTrans->getHistoryList($_REQUEST);
			
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
        
        /** 보상담당 분류 기준  */
        case CASE_READ:
            $data =  $cTrans->getData($_REQUEST);
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

 
        /** 보상담당자 담당구분 저장 */
		case CASE_CREATE:
			$data = $cTrans->setData($_REQUEST);
			
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

	$cTrans->CloseDB();

?>
