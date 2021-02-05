<?php
	require $_SERVER['DOCUMENT_ROOT']."/common/common.php";				// 환경설정파일 로딩 - 데이터변수 등
	require "../class/clsConfirmBundle.php";

	switch( $_REQUEST['REQ_MODE'] ){
        /** 조회 */
		case CASE_LIST:
			$data =  $cBundle->getList($_REQUEST);

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

		case CASE_LIST_SUB:
			$data =  $cBundle->getDetailList($_REQUEST);

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

        /** UPLOAD */
		case CASE_CREATE:
			$result = $cBundle->setData($_REQUEST,$_FILES);
			if($result == OK) {
				echo "<script>alert('파일 업로드가 완료되었습니다.'); location.href='/300_deposit/320_confirmBundle/confirmBundle.html';</script>";
			}else if ($result == ERROR_DB_INSERT_DATA){
				echo "<script>alert('파일 데이터 저장에 실패하였습니다. \n재시도 바랍니다.'); location.href='/300_deposit/320_confirmBundle/confirmBundle.html';</script>";
			}else if ($result == ERROR_NO_FILE){
				echo "<script>alert('파일 업로드가 실패하였습니다. - 첨부 파일 없음  \n재시도 바랍니다.'); location.href='/300_deposit/320_confirmBundle/confirmBundle.html';</script>";
			}else if ($result == ERROR_NOT_ALLOWED_FILE){
				echo "<script>alert('파일 업로드가 실패하였습니다. - CSV파일만 업로드 가능합니다\n재시도 바랍니다.'); location.href='/300_deposit/320_confirmBundle/confirmBundle.html';</script>";
			}else if ($result == ERROR_MOVE_FILE){
				echo "<script>alert('업로드 파일 복사 실패하였습니다. \n관리자 문의해주세요.'); location.href='/300_deposit/320_confirmBundle/confirmBundle.html';</script>";
			}
		break;

		/* 입금처리 */
		case CASE_UPDATE:
			$data =  $cBundle->setDeposit($_REQUEST);
			if($data === ERROR_DB_UPDATE_DATA){
				$response['status']  = ERROR_DB_UPDATE_DATA;
				$response['message'] = "ERROR_DB_UPDATE_DATA";
			}else{
				$response['status']  = OK;
				$response['message'] = "OK";	
				$response['data'] = $data;	
			}	
			
			echo json_encode($response);
		break;


	}

	$cBundle->CloseDB();

?>
