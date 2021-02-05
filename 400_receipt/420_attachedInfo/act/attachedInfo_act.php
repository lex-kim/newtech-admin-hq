<?php
	require $_SERVER['DOCUMENT_ROOT']."/common/common.php";				// 환경설정파일 로딩 - 데이터변수 등
    require "../class/clsAttachedInfo.php";
	if(!$_REQUEST['PER_PAGE']) { $PER_PAGE = LIST_PER_PAGE; $_REQUEST['PER_PAGE'] = LIST_PER_PAGE;}
    if(!$_REQUEST['CURR_PAGE']) { $CURR_PAGE = 1;  $_REQUEST['CURR_PAGE'] = 1; }
	
	switch( $_REQUEST['REQ_MODE'] ){
        /** 조회 */
        case CASE_LIST:
            $data =  $cAttached->getList($_REQUEST);
            
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
            $result = $cAttached->setData($_REQUEST);
            
			if($result) {
                $response['status']  = OK;
                $response['message'] = "OK";
            } else {
                $response['status']  = SERVER_ERROR;
                $response['message'] = "";
            }

			echo json_encode($response);
        break;


    }
	
	$cAttached->CloseDB();
?> 