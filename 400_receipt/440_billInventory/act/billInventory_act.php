<?php
	require $_SERVER['DOCUMENT_ROOT']."/common/common.php";				// 환경설정파일 로딩 - 데이터변수 등
	require "../class/clsBillInventory.php";

	switch( $_REQUEST['REQ_MODE'] ){
		/* 재고정산 정보 조회 */
        case CASE_READ:
			$data =  $cBillInventory->getStock($_REQUEST);

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
		
        /* 재고정산 정보 변경 */
        case CASE_UPDATE:
            $data =  $cBillInventory->setStock($_REQUEST);
			if($data) {
				$response['status']  = OK;
				$response['message'] = "OK";
			} else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}
			echo json_encode($response);
		break;
		
		/* 재고정산 정보 삭제 */
        case CASE_DELETE:
			$data =  $cBillInventory->delStock($_REQUEST);
			if($data) {
				$response['status']  = OK;
				$response['message'] = "OK";
			} else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}
			echo json_encode($response);
		break;

		/** 공업사별 집계 조회 */
		case CASE_LIST_SUB:
			$data =  $cBillInventory->getSubList($_REQUEST);
	
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
			$data =  $cBillInventory->getList($_REQUEST);
			
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

		/* 취급부품 */
		case GET_PARTS_INFO:
            $data = $cBillInventory->getChart($_REQUEST);

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
		
		/* 해당년 조회 */
		case OPTIONS_YEAR:
			$data =  $cBillInventory->getYear($_REQUEST);
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

	}

	$cBillInventory->CloseDB();

?>
