<?php
	require $_SERVER['DOCUMENT_ROOT']."/common/common.php";				// 환경설정파일 로딩 - 데이터변수 등
	require "../class/clsBillBin.php";

	if(!$_REQUEST['PER_PAGE']) { $PER_PAGE = LIST_PER_PAGE; $_REQUEST['PER_PAGE'] = LIST_PER_PAGE;}
	if(!$_REQUEST['CURR_PAGE']) { $CURR_PAGE = 1;  $_REQUEST['CURR_PAGE'] = 1; }

	switch( $_REQUEST['REQ_MODE'] ){


		/** 조회 */
		case CASE_LIST:
			$data =  $cBillBin->getList($_REQUEST);
			
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
			if($data =  $cBillBin->setData($_REQUEST)) {
				$response['status']  = OK;
				$response['message'] = "OK";
			} else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}	
			echo json_encode($response);
		break;

		case CASE_DELETE:
			$data =  $cBillBin->delData($_REQUEST);

			if($data){
				$response['status']  = OK;
				$response['message'] = "OK";
			}else{
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";		
			}
			
			echo json_encode($response);
		break;


		/** 보험사팀조회 */
		case GET_INSU_TEAM:
			if($data =  $cBillBin->getInsuTeam($_REQUEST)) {
				$response['status']  = OK;
				$response['message'] = "OK";
				$response['data'] = $data;		
			} else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}
			echo json_encode($response);
		break;
		

		/* 사용부품 조회 */
		case GET_PARTS_TYPE:
			$data =  $cBillBin->getPartsType($_REQUEST);

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


		/** 자동차 등급 받기 */
		case GET_CAR_TYPE:
			$data =  $cBillBin->getCarType($_REQUEST);

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


		default:
		echo 'defualt error';
		break;

	}

	$cBillBin->CloseDB();

?>
