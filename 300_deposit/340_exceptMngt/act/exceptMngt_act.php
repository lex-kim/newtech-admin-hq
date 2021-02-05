<?php
	require $_SERVER['DOCUMENT_ROOT']."/common/common.php";				// 환경설정파일 로딩 - 데이터변수 등
    require "../class/clsExceptMngt.php";
    
    if(!$_REQUEST['PER_PAGE']) { $PER_PAGE = LIST_PER_PAGE; $_REQUEST['PER_PAGE'] = LIST_PER_PAGE;}
	if(!$_REQUEST['CURR_PAGE']) { $CURR_PAGE = 1;  $_REQUEST['CURR_PAGE'] = 1; }

	switch( $_REQUEST['REQ_MODE'] ){
        
		/** 전송 리스트 조회 */
		case CASE_LIST:
			$data =  $cExcept->getList($_REQUEST);
			
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
        
        /** 저장 */
		case CASE_CREATE:

				$result = $cExcept->setData($_REQUEST);
			
			if($result['status'] === CONFLICT){
				$response['status']  = CONFLICT;
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

		/* 감액 사유 조회 */
		case REF_EXCLUDE_REASON:
			if($data =  $cExcept->getExclude($_REQUEST)) {
				$response['status']  = OK;
				$response['message'] = "OK";
				$response['data'] = $data;		
			} else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}
			echo json_encode($response);
		break;

		/* 입금상세여부 조회 */
		case REF_DEPOSIT_TYPE:
			if($data =  $cExcept->getDepositDetail($_REQUEST)) {
				$response['status']  = OK;
				$response['message'] = "OK";
				$response['data'] = $data;		
			} else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}
			echo json_encode($response);
		break;	

		/* UMS 발송 */
		case CASE_UPDATE:
			$data =  $cExcept->setUMS($_REQUEST);

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

	$cExcept->CloseDB();

?>
