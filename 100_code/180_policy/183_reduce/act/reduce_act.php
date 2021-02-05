<?php
	require $_SERVER['DOCUMENT_ROOT']."/common/common.php";				// 환경설정파일 로딩 - 데이터변수 등
	require "../class/clsReduce.php";

	switch( $_REQUEST['REQ_MODE'] ){

		/** 수정 */
		case CASE_UPDATE:
			if($clsReduce->setData($_REQUEST)) {
				$response['status']  = OK;
				$response['message'] = "OK";
			} else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "";
			}	

			echo json_encode($response);
		break;


		/** 데이터 가져오기 */
		case CASE_READ:
			$data =  $clsReduce->getData($_REQUEST);

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

	$clsReduce->CloseDB();

?>
