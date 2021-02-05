<?php
	require $_SERVER['DOCUMENT_ROOT']."/common/common.php";				// 환경설정파일 로딩 - 데이터변수 등
	require "../class/clsGwCompensation.php";

	switch( $_REQUEST['REQ_MODE'] ){

		/** 등록 */
		case CASE_CREATE:
			$result = $clsGwCompensation->setData($_REQUEST);
			
			if($result === CONFLICT){
				$response['status']  = CONFLICT;
				$response['message'] = "해당공업사에 등록된 보상담당자가 존재합니다.";
			}else{
				if($result){
					$response['status']  = OK;
					$response['message'] = "OK";
					
				}else{
					$response['status']  = SERVER_ERROR;
					$response['message'] = "Internal Server Error";
				}
			}
			echo json_encode($response);
		break;

		/** 수정 */
		case CASE_UPDATE:
			$result = $clsGwCompensation->setData($_REQUEST);
			
			if($result === CONFLICT){
				$response['status']  = CONFLICT;
				$response['message'] = "해당공업사에 등록된 보상담당자가 존재합니다.";
			}else{
				if($result){
					$response['status']  = OK;
					$response['message'] = "OK";
					
				}else{
					$response['status']  = SERVER_ERROR;
					$response['message'] = "Internal Server Error";
				}
			}

			echo json_encode($response);
		break;

		/** 삭제 */
		case CASE_DELETE:
			$result = $clsGwCompensation->delData($_REQUEST);
			
			if($result){
				$response['status']  = OK;
				$response['message'] = "OK";
				
			}else{
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}

			echo json_encode($response);
		break;

		/** 조회 */
		case CASE_LIST:
			if(!$_REQUEST['PER_PAGE']) { $PER_PAGE = LIST_PER_PAGE; $_REQUEST['PER_PAGE'] = LIST_PER_PAGE;}
			if(!$_REQUEST['CURR_PAGE']) { $CURR_PAGE = 1;  $_REQUEST['CURR_PAGE'] = 1; }

			$data =  $clsGwCompensation->getList($_REQUEST);
			// echo var_dump(json_encode($data));
			// echo $data;
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

		/** 공급사 정보조회(청구담당자 조회)*/
		case GET_GARAGE_CLAIM_INFO:
			$data = $clsGwCompensation->getGarageInfo($_REQUEST);
			
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


		/** 해당보험사 보상담당관리 */
		case GET_INSU_CHARGE_INFO:
			$data = $clsGwCompensation->getInsuChargeInfo($_REQUEST);
			
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

	$clsGwCompensation->CloseDB();

?>
