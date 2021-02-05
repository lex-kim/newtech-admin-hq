
<?php
	require $_SERVER['DOCUMENT_ROOT']."/common/common.php";				// 환경설정파일 로딩 - 데이터변수 등
	require "../class/clsStdTable.php";
	require "./initData.php";

	switch( $_REQUEST['REQ_MODE'] ){

		/** 수정 */
		case CASE_UPDATE:
			$result = $cStandard->setData($_REQUEST,$initData);
			if($result === ERROR){
				$response['status']  = ERROR;
				$response['message'] = "중복된 출력순서가 존재합니다.";
			}else{
				if($result) {
					$response['status']  = OK;
					$response['message'] = "OK";
				} else {
					$response['status']  = SERVER_ERROR;
					$response['message'] = "";
				}
			}	

			echo json_encode($response);
		break;

		/** 조회 */
		case CASE_LIST:
			$data =  $cStandard->getList($_REQUEST,$initData);

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
			if($data =  $cStandard->getCarList($_REQUEST)) {
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
			if($data =  $cStandard->getWorkList($_REQUEST)) {
				$response['status']  = OK;
				$response['message'] = "OK";
				$response['data'] = $data;		
			} else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}
			echo json_encode($response);
		break;

	}

	$cStandard->CloseDB();

?>
