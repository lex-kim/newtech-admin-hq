<?php
	require $_SERVER['DOCUMENT_ROOT']."/common/common.php";				// 환경설정파일 로딩 - 데이터변수 등
	require "../class/clsVisitingDay.php";

	switch( $_REQUEST['REQ_MODE'] ){
        /** 조회 */
		case CASE_LIST:
			$data =  $cVisit->getList($_REQUEST);

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

        /** UPLOAD */
		case CASE_UPDATE:
			$result = $cVisit->setData($_REQUEST,$_FILES);
			if($result == OK) {
				echo "<script>alert('임원방문월 파일 업로드가 완료되었습니다.'); location.href='/500_gw/570_visitingDay/visitingDay.html';</script>";
			}else if ($result == ERROR_DB_INSERT_DATA){
				echo "<script>alert('임원방문월 파일 데이터 저장에 실패하였습니다. \n재시도 바랍니다.'); location.href='/500_gw/570_visitingDay/visitingDay.html';</script>";
			}else if ($result == ERROR_NO_FILE){
				echo "<script>alert('임원방문월 파일 업로드가 실패하였습니다. - 첨부 파일 없음  \n재시도 바랍니다.'); location.href='/500_gw/570_visitingDay/visitingDay.html';</script>";
			}else if ($result == ERROR_NOT_ALLOWED_FILE){
				echo "<script>alert('임원방문월 파일 업로드가 실패하였습니다. - CSV파일만 업로드 가능합니다\n재시도 바랍니다.'); location.href='/500_gw/570_visitingDay/visitingDay.html';</script>";
			}else if ($result == ERROR_MOVE_FILE){
				echo "<script>alert('업로드 파일 복사 실패하였습니다. \n관리자 문의해주세요.'); location.href='/500_gw/570_visitingDay/visitingDay.html';</script>";
			}
		break;

	}

	$cVisit->CloseDB();

?>
