<?php
	require $_SERVER['DOCUMENT_ROOT']."/common/common.php";				// 환경설정파일 로딩 - 데이터변수 등
	require "../class/clsExchTable.php";

	switch( $_REQUEST['REQ_MODE'] ){
		/* 취급부품 조회 */
        case GET_PARTS_TYPE:
            if($data =  $cExchange->getCpps($_REQUEST)) {
				$response['status']  = OK;
				$response['message'] = "OK";
				$response['data'] = $data;		
			} else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}
			echo json_encode($response);
		break;
		
		/** 수정 */
		case CASE_UPDATE:
			$result = $cExchange->setData($_REQUEST);
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
			$data =  $cExchange->getList($_REQUEST, $initData);
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

		/* CAR TYPE 조회 */
		case GET_CAR_TYPE: 
			if($data =  $cExchange->getCarList($_REQUEST)) {
				$response['status']  = OK;
				$response['message'] = "OK";
				$response['data'] = $data;		
			} else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}
			echo json_encode($response);
		break;

		/* 작업항목 조회 */
		case GET_REF_WORK_PART: 
			if($data =  $cExchange->getWorkList($_REQUEST)) {
				$response['status']  = OK;
				$response['message'] = "OK";
				$response['data'] = $data;		
			} else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}
			echo json_encode($response);
		break;

		/* 환산소요량 계산(리셋)*/
		case CASE_TOTAL_LIST: 
			if($data =  $cExchange->resetData($_REQUEST)) {
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
            echo 'in clsExchTable.php !!!!!';
        break;



    }
	$cExchange->CloseDB();
?>