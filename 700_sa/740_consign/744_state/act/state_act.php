<?php
	require $_SERVER['DOCUMENT_ROOT']."/common/common.php";				// 환경설정파일 로딩 - 데이터변수 등
	require "../class/clsReceipt.php";

	switch( $_REQUEST['REQ_MODE'] ){

		/** 수정 */
		case CASE_UPDATE:
			$result = $cReceipt->setData($_REQUEST);

			if($result) {
				$response['status']  = OK;
				$response['message'] = "OK";
			}else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "";
			}

			echo json_encode($response);
		break;

		/** 조회 */
		case CASE_LIST:
			if(!$_REQUEST['PER_PAGE']) { $PER_PAGE = LIST_PER_PAGE; $_REQUEST['PER_PAGE'] = LIST_PER_PAGE;}
			if(!$_REQUEST['CURR_PAGE']) { $CURR_PAGE = 1;  $_REQUEST['CURR_PAGE'] = 1; }

			$data =  $cReceipt->getList($_REQUEST);

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

		/** 클릭한 해당 데이터 gET */
		case CASE_READ:
			$data =  $cReceipt->getData($_REQUEST);

			if($data === HAVE_NO_DATA){
				$response['status']  = SERVER_ERROR;
				$response['message'] = "HAVE_NO_DATA";
			}else{
				$response['status']  = OK;
				$response['message'] = "OK";	
				$response['data'] = $data;		
			}
			
			echo json_encode($response);
		break;


		/** 공급사 정보조회(청구담당자 조회)*/
		case GET_GARAGE_CLAIM_INFO:
			$data = $cReceipt->getGarageInfo($_REQUEST);
			
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

		/** 영업사원 조회*/
		case MODE_TARGET_SA:
			$data = $cReceipt->getSa($_REQUEST);
			
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

	$cReceipt->CloseDB();

?>
