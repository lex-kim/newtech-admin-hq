<?php
	require $_SERVER['DOCUMENT_ROOT']."/common/common.php";				// 환경설정파일 로딩 - 데이터변수 등
    require "../class/clsDepositMngt.php";
    
	if(!$_REQUEST['PER_PAGE']) { $PER_PAGE = LIST_PER_PAGE; $_REQUEST['PER_PAGE'] = LIST_PER_PAGE;}
    if(!$_REQUEST['CURR_PAGE']) { $CURR_PAGE = 1;  $_REQUEST['CURR_PAGE'] = 1; }
	
	switch( $_REQUEST['REQ_MODE'] ){
        
		/** 보험사팀조회 */
		case GET_INSU_TEAM:
			if($data =  $cDeposit->getInsuTeam()) {
				$response['status']  = OK;
				$response['message'] = "OK";
				$response['data'] = $data;		
			} else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}
			echo json_encode($response);
		break;

		
		/** 보상담당자 조회 */
		case GET_INSU_TYPE:
			if($data =  $cDeposit->getFao()) {
				$response['status']  = OK;
				$response['message'] = "OK";
				$response['data'] = $data;		
			} else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}
			echo json_encode($response);
		break;

		
		/** 청구요청자 조회 */
		case GET_REF_CLAIMANT_TYPE:
			if($data =  $cDeposit->getClaimId()) {
				$response['status']  = OK;
				$response['message'] = "OK";
				$response['data'] = $data;		
			} else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}
			echo json_encode($response);
		break;

		/* CAR TYPE 조회 */
		case GET_CAR_TYPE: 
			if($data =  $cDeposit->getCarType($_REQUEST)) {
				$response['status']  = OK;
				$response['message'] = "OK";
				$response['data'] = $data;		
			} else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}
			echo json_encode($response);
		break;

		/* 사용부품 조회 */
		case GET_PARTS_TYPE:
			$data =  $cDeposit->getPartsType($_REQUEST);

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

		
		/* 입금상세여부 조회 */
		case REF_DEPOSIT_TYPE:
			if($data =  $cDeposit->getDepositDetail($_REQUEST)) {
				$response['status']  = OK;
				$response['message'] = "OK";
				$response['data'] = $data;		
			} else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}
			echo json_encode($response);
		break;

		/* 지급제외 사유 조회 */
		case REF_EXCLUDE_REASON:
			if($data =  $cDeposit->getExclude($_REQUEST)) {
				$response['status']  = OK;
				$response['message'] = "OK";
				$response['data'] = $data;		
			} else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}
			echo json_encode($response);
		break;

		/* 감액 사유 조회 */
		case REF_DECREMENT_REASON:
			if($data =  $cDeposit->getDecrement($_REQUEST)) {
				$response['status']  = OK;
				$response['message'] = "OK";
				$response['data'] = $data;		
			} else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}
			echo json_encode($response);
		break;

		/** 해당보험사 보상담당관리 */
		case GET_INSU_CHARGE_INFO:
			$data = $cDeposit->getInsuChargeInfo($_REQUEST);
			
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
		
		/** 저장 */
		case CASE_CREATE:
			if($_REQUEST['REQ_TARGET'] == MODE_TARGET_IW){
				$result = $cDeposit->setTrans($_REQUEST);
			}else {
				$result = $cDeposit->setData($_REQUEST);
			}
			
			if($result['status'] === CONFLICT){
				$response['status']  = CONFLICT;
				$response['message'] = $result['msg'];
				$response['col'] = $result['col'];
			}else if($result['status'] == ERROR){
				$response['status']  = ERROR;
				$response['message'] = $result['msg'];
				$response['col'] = $result['col'];
			}else if($result['status'] == SERVER_ERROR){
				$response['status']  = SERVER_ERROR;
				$response['message'] = "";
			}else{
				$response['status']  = OK;
				$response['message'] = "OK";
			}

			echo json_encode($response);
		break;


		/** 차주분 입금처리 */
		case CASE_UPDATE:
			$data =  $cDeposit->setCarown($_REQUEST);
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

		/** 팀 재분류 메모 저장 */
		case CASE_UPDATE_TEAM:
			$data =  $cDeposit->setTeamMemo($_REQUEST);
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
		

		/** 감액 요청 */
		case CASE_UPDATE_REGION:
			$data =  $cDeposit->setDecrement($_REQUEST);
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


		/** 조회 */
		case CASE_LIST:
			$data =  $cDeposit->getList($_REQUEST);
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

		/** 잡계 조회 */
		case CASE_LIST_SUB:
			$data =  $cDeposit->getSummary($_REQUEST);
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

		/** 해당데이터 삭제 */
		case CASE_DELETE:
			if($data =  $cDeposit->delData($_REQUEST)) {
				$response['status']  = OK;
				$response['message'] = "OK";
			} else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}
			echo json_encode($response);
        break;


		default:
			echo 'cDeposit defualt error';
		break;
	}
	$cDeposit->CloseDB();
?>