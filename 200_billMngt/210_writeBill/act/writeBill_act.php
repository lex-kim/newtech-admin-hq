<?php
	require $_SERVER['DOCUMENT_ROOT']."/common/common.php";	
	require $_SERVER['DOCUMENT_ROOT']."/common/class/clsCommon.php";				// 환경설정파일 로딩 - 데이터변수 등
	require $_SERVER['DOCUMENT_ROOT']."/common/class/clsBillInfo.php";
	require "../class/clsWriteBill.php";
	

	switch( $_REQUEST['REQ_MODE'] ){

		/** 생성 */
		case CASE_CREATE:
			$result = $clsWriteBill->setData($_REQUEST);
			if($result) {
				$response['status']  = OK;
				$response['message'] = "OK";
			}else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "";
			}

			echo json_encode($response);
		break;

		/** 해당청구서 건이 중복인지 아닌지 */
		case CASE_LIST_SUB:
			$data = $clsWriteBill->isDuplicateBill($_REQUEST);

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

		/** 청구금액 한도 */
		case CASE_DELETE_FILE:
			$data = $clsWriteBill->getMaxmiumClaimPrice($_REQUEST);

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

		/** 수정모드(해당청구서 정보 가져오기) */
		case CASE_TOTAL_LIST:
			$data = $clsBillInfo->getList($_REQUEST);

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

		
		case CASE_LIST_DETAIL:
			$result = $clsWriteBill->updateFaxStatus($_REQUEST);
			if($result) {
				$response['status']  = OK;
				$response['message'] = "OK";
			}else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "";
			}

			echo json_encode($response);
		break;
		
		
		/** 조견표계산 */
		case CASE_UPDATE:
			if(!empty($_REQUEST['NB_AB_NSRSN_VAL'])){
				$data =  $clsWriteBill->updateData($_REQUEST);
			}else{
				$data =  $clsWriteBill->setData($_REQUEST);
			}
			

			if($data === HAVE_NO_DATA){
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}else if($data === CONFLICT){
				$response['status']  = CONFLICT;
				$response['message'] = "해당보상담당지정을 가진 담당자가 존재합니다.";
			}else{
				$response['status']  = OK;
				$response['message'] = "OK";	
				$response['data'] = $data;
					
			}

			echo json_encode($response);
		break;

		/** 조회 */
		case CASE_LIST:
			$data =  $clsWriteBill->getList($_REQUEST);

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
		case CASE_READ:
			$data =  $clsWriteBill->getData($_REQUEST);

			echo json_encode($data);
		break;

		case GET_AUTHORITY : 
			$data =  $clsWriteBill->getUUID($_REQUEST);

			if($data){
				$response['status']  = OK;
				$response['message'] = "OK";	
				$response['data'] = $data;	
			}else{
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
				
			}
			
			echo json_encode($response);
		break;

		/** 수리차량타입에 맞는 보상담당자 확인 */
		case CASE_DELETE:
			$data =  $clsWriteBill->getInsuManageType($_REQUEST);

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

		

		/** 차량정보 */
		case GET_CAR_TYPE:
			$data =  $clsWriteBill->getCarInfo();

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

		/** 기준부품 */
		case GET_PARTS_INFO:
			$data =  $clsCommon->getStdParts($_REQUEST);

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

		/** 해당공업사 사용부품 정보 조회 */
		case GET_GARGE_PARTS_INFO:
			$data =  $clsWriteBill->getGaragePartsInfo($_REQUEST);

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


		/** 공업사 정보 가져오기 (이름/아이디)*/
		case GET_GARGE_INFO:
			$data =  $clsWriteBill->getGarageInfo();

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

	$clsWriteBill->CloseDB();

?>
