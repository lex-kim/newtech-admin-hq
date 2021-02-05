<?php
	require $_SERVER['DOCUMENT_ROOT']."/common/common.php";				// 환경설정파일 로딩 - 데이터변수 등
	require "../model/BuyMngtModel.php";


	switch( $_REQUEST['REQ_MODE'] ){
		case CASE_LIST:
			$result = $cBuyMngt->getList($_REQUEST);
			
			$response['status']  = OK;
			$response['message'] = "OK";
			$response['totalCnt'] = empty(count($result)) ? 0 : count($result);
			$response['data'] = $result;

			echo json_encode($response);
		break;

		case CASE_DELETE:
			$result = $cBuyMngt->delData($_REQUEST);

			$response['status']  = OK;
			$response['message'] = "OK";
			$response['data'] = $result;

			echo json_encode($response);
		break;

		case CASE_READ:
			$result = $cBuyMngt->getData($_REQUEST);

			$response['status']  = OK;
			$response['message'] = "OK";
			$response['data'] = $result;

			echo json_encode($response);
		break;

		case CASE_CREATE:
			$result = $cBuyMngt->setData($_REQUEST, $_FILES);

			if($result){
				$response['status']  = OK;
				$response['message'] = "OK";
				$response['data'] = $_FILES;
			}else{
				$response['status']  = ERROR;
				$response['message'] = "ERROR";
			}

			echo json_encode($response);
		break;

		case CASE_UPDATE:
			$result = $cBuyMngt->setData($_REQUEST, $_FILES);

			if($result){
				$response['status']  = OK;
				$response['message'] = "OK";
				$response['data'] = $_FILES;
			}else{
				$response['status']  = ERROR;
				$response['message'] = "ERROR";
			}

			echo json_encode($response);
		break;

		case GET_OPTIONS:
			$result = $cBuyMngt->getOptions();

			$response['status']  = OK;
			$response['message'] = "OK";
			$response['data'] = $result;

			echo json_encode($response);
		break;
    }

?>
