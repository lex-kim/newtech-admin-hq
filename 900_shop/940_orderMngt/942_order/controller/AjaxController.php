<?php
	require $_SERVER['DOCUMENT_ROOT']."/common/common.php";				// 환경설정파일 로딩 - 데이터변수 등
	require_once "../class/Order.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/900_shop/940_orderMngt/941_orderList/class/OrderList.php";

	switch( $_REQUEST['REQ_MODE'] ){
		case CASE_READ:  // 해당공업사 기본정보
			$result = $cOrder->getGargeInfo($_REQUEST);

			$response['status']  = $result['status'];
			$response['message'] =  $result['result'];
			$response['data'] = $result['data'];
			
			echo json_encode($response);
		break;
		
		case CASE_LIST:
			$result = $cOrder->getList($_REQUEST);

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
			$result = $cOrder->getList($_REQUEST);
		
			$response['status']  = $result['status'];
			$response['message'] =  $result['result'];
			$response['data'] = $result['data'];

			echo json_encode($response);
		break;

		case CASE_DELETE:
			$result = $cOrder->delData($_REQUEST);

			$response['status']  = OK;
			$response['message'] = "OK";

			echo json_encode($response);
		break;

		case CASE_CREATE:
			if(empty($_REQUEST['SET_TYPE'])){  // 견적서 작성/발행
				$result = $cOrder->setData($_REQUEST);
			}else{
				$result = $cOrderList->setData($_REQUEST, $_FILES);
			}
			

			$response['status']  = $result['status'] ;
			$response['message'] = $result['result'] ;
			$response['data'] = $result['data'] ;

			echo json_encode($response);
		break;

		case CASE_UPDATE:
			$result = $cOrder->setData($_REQUEST);

			$response['status']  = $result['status'] ;
			$response['message'] = $result['result'] ;
			$response['data'] = $result['data'] ;

			echo json_encode($response);
		break;

		case GET_ORDER_ADDRESS_INFO:
			$result = $cOrder->getOrderInfo($_REQUEST);

			$response['status']  = $result['status'] ;
			$response['message'] = $result['result'] ;
			$response['data'] = $result['data'] ;

			echo json_encode($response);
		break;
		
    }

?>
