<?php
	require $_SERVER['DOCUMENT_ROOT']."/common/common.php";				// 환경설정파일 로딩 - 데이터변수 등
    require "../class/clsDepositIw.php";
    
    if(!$_REQUEST['PER_PAGE']) { $PER_PAGE = LIST_PER_PAGE; $_REQUEST['PER_PAGE'] = LIST_PER_PAGE;}
	if(!$_REQUEST['CURR_PAGE']) { $CURR_PAGE = 1;  $_REQUEST['CURR_PAGE'] = 1; }

	switch( $_REQUEST['REQ_MODE'] ){
        
		/** 전송 리스트 조회 */
		case CASE_LIST:
			$data =  $cDepositIw->getList($_REQUEST);
			
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

		/* 해당년 조회 */
		case OPTIONS_YEAR:
			$data =  $cDepositIw->getYear($_REQUEST);
			if($data === HAVE_NO_DATA){
				$response['status']  = HAVE_NO_DATA;
				$response['message'] = ERROR_MSG_NO_DATA;
			}else{
				$response['status']  = OK;
				$response['message'] = "OK";	
				$response['data'] = $data;		
			}
			echo json_encode($response);
		break;

	}

	$cDepositIw->CloseDB();

?>
