<?php
	require $_SERVER['DOCUMENT_ROOT']."/common/common.php";				// 환경설정파일 로딩 - 데이터변수 등
	require "../class/clsAutoAddress.php";

	switch( $_REQUEST['REQ_MODE'] ){


		case CASE_LIST:
			if(!$_REQUEST['PER_PAGE']) { $PerPage = LIST_PER_PAGE; $_REQUEST['PER_PAGE'] = LIST_PER_PAGE;}
			if(!$_REQUEST['CURR_PAGE']) { $CurPAGE = 1;  $_REQUEST['CURR_PAGE'] = 1; }

			$data =  $cAddress->getList($_REQUEST);

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

	}

	$cAddress->CloseDB();

?>
