<?php
	require $_SERVER['DOCUMENT_ROOT']."/common/common.php";				// 환경설정파일 로딩 - 데이터변수 등
	require "../class/clsGwInventory.php";

	switch( $_REQUEST['REQ_MODE'] ){

		/** 조회 */
		case CASE_LIST:
			$data =  $cInventory->getList($_REQUEST);
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

		
		/** 공업사- 부품별 재고 조회 */
		case CASE_LIST_SUB:
			$data =  $cInventory->getSubList($_REQUEST);
	
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

        case CASE_READ:
            if($_REQUEST['REQ_TARGET'] == STOCK_BILL ){
                $data =  $cInventory->getStock($_REQUEST);
            }else{
                //메인(입출고등록) 데이터 읽어 올때 사용 
            }

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
        
        case CASE_UPDATE:
            if($_REQUEST['REQ_TARGET'] == STOCK_BILL ){
                $data =  $cInventory->setStock($_REQUEST);
            }else{
                //메인(입출고등록) 데이터 저장시
            }
			if($data) {
				$response['status']  = OK;
				$response['message'] = "OK";
			} else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}
			echo json_encode($response);
        break;

        case CASE_DELETE:
            if($_REQUEST['REQ_TARGET'] == STOCK_BILL ){
                $data =  $cInventory->delStock($_REQUEST);
            }else{
                //메인(입출고등록) 데이터 삭제
            }

			if($data) {
				$response['status']  = OK;
				$response['message'] = "OK";
			} else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}
			echo json_encode($response);
		break;
		
		/* 취급부품 */
		case GET_PARTS_INFO:
            $data = $cInventory->getChart($_REQUEST);

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
		
		
		/** 독립정산 */
		case STOCK_INDE:
			$data =  $cInventory->setIndependent($_REQUEST);
			if($data == HAVE_NO_DATA){
				$response['status']  = HAVE_NO_DATA;
				$response['message'] = "HAVE_NO_DATA";
			}else{
				$response['status']  = OK;
				$response['message'] = "OK";	
				$response['data'] = $data;		
			}
			echo json_encode($response);
		break;
		

		/** 대체정산 */
		case STOCK_SUBST:
			
			$data =  $cInventory->setSubstitution($_REQUEST);
			if($data == HAVE_NO_DATA){
				$response['status']  = HAVE_NO_DATA;
				$response['message'] = "HAVE_NO_DATA";
			}else{
				$response['status']  = OK;
				$response['message'] = "OK";	
				$response['data'] = $data;		
			}
			echo json_encode($response);
        break;

	}

	$cInventory->CloseDB();

?>
