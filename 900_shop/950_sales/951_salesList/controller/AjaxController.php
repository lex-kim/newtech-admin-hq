<?php
	require $_SERVER['DOCUMENT_ROOT']."/common/common.php";				// 환경설정파일 로딩 - 데이터변수 등
	require_once "../class/SaleList.php";

	switch( $_REQUEST['REQ_MODE'] ){
		case CASE_LIST:
			$result = $cSaleList->getList($_REQUEST);
		
			$response['status']  = $result['status'];
			$response['message'] =  $result['result'];
			$response['data'] = $result['data'];
			if($response['status'] == OK){
				$response['total_item_cnt'] = $result['total_item_cnt'];
			}
			echo json_encode($response);
		break;

		case CASE_TOTAL_LIST:
			$_REQUEST['fullListYN'] = "Y";
			$result = $cSaleList->getList($_REQUEST);
		
			$response['status']  = $result['status'];
			$response['message'] =  $result['result'];
			$response['data'] = $result['data'];

			echo json_encode($response);
		break;

		case CASE_DELETE:
			$result = $cSaleList->delData($_REQUEST);

			$response['status']  = $result['status'];
			$response['message'] =  $result['result'];

			echo json_encode($response);
		break;

		case CASE_READ:
			$result = array();
			if(empty($_REQUEST['itemID'])){
				$result = $cSaleList->getRecentData($_REQUEST);
			}else{
				$result = $cSaleList->getData($_REQUEST);
			}
			

			$response['status']  = $result['status'];
			$response['message'] =  $result['result'];
			$response['data'] = $result['data'];

			echo json_encode($response);
		break;

		case CASE_CREATE:
			$result = $cSaleList->setData($_REQUEST);

			$response['status']  = $result['status'] ;
			$response['message'] = $result['result'] ;
			$response['data'] = $result['data'] ;

			echo json_encode($response);
		break;

		case CASE_UPDATE:
			$result = $cSaleList->setData($_REQUEST);

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
    }

?>
