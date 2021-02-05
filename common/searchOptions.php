<?php
	require $_SERVER['DOCUMENT_ROOT']."/common/common.php";				// 환경설정파일 로딩 - 데이터변수 등
	require "./class/clsSearchOptions.php";

	/* AJAX ROUTER */
	switch( $_REQUEST['REQ_MODE'] ){

		case OPTIONS_DIVISION : 
            echo $cSearchOptions->getOptionListDivision($_REQUEST);
		break;

		case OPTIONS_AREA :
			echo $cSearchOptions->getOptionArrArea($_REQUEST);
		break;

		case OPTIONS_IW :
			echo $cSearchOptions->getOptionListIw($_REQUEST);
		break;

		case OPTIONS_GW :
			echo $cSearchOptions->getOptionListGw($_REQUEST);
		break;

		case OPTIONS_CRR :
			$data =  $cSearchOptions->getAreaOption($_REQUEST);

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
		
		case GET_GARGE_INFO:
			echo $cSearchOptions->getGarageInfo($_REQUEST);
		break;

		case GET_PARTS_TYPE:
			echo $cSearchOptions->getPartsType($_REQUEST);
		break;

		case OPTIONS_CPPS:
			echo $cSearchOptions->getCpps($_REQUEST);
		break;

		case OPTIONS_YEAR:
			echo $cSearchOptions->getYear($_REQUEST);
		break;

		case OPTIONS_MONTH:
			echo $cSearchOptions->getMonth($_REQUEST);
		break;

		case OPTIONS_BILL_YEAR:
			echo $cSearchOptions->getClaimYearOption($_REQUEST);
		break;

		/** 보험사팀조회 */
		case GET_INSU_TEAM:
			if($data =  $cSearchOptions->getInsuTeam($_REQUEST)) {
				$response['status']  = OK;
				$response['message'] = "OK";
				$response['data'] = $data;		
			} else {
				$response['status']  = SERVER_ERROR;
				$response['message'] = "Internal Server Error";
			}
			echo json_encode($response);
		break;
		
		case REF_POINT_TYPE:
			echo json_encode($cSearchOptions->getGrade($_REQUEST));
		break;

		
		default:
			echo 'in searchOptions.php';
		break;

	}

	$cSearchOptions->CloseDB();

?>
