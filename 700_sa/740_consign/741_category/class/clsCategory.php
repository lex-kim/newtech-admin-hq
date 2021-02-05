<?php

class clsCategory extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	// Class Object Constructor
	function clsCategory($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
		$this->DATA_SAVE_PATH = $DATA_SAVE_PATH;
		$this->ALLOW_FILE_EXT = $ALLOW_FILE_EXT;
	}


	/**
	 * 조회 
	 * @param {$ARR}
	 * @param {CURR_PAGE : 현재페이지, PER_PAGE : 몇개를 조회할 것인지, KEYWORD : 검색조건}
	 */
	function getList($ARR) {
		$LimitStart = ($ARR['CURR_PAGE']-1) * $ARR['PER_PAGE'];
		$LimitEnd   = $ARR['PER_PAGE'];
		$tmpKeyword = "";

	

		/** 기간검색 */
		if($ARR['START_DT'] !='' && $ARR['END_DT'] !=''){
			$tmpKeyword .= "AND DATE(`NCC`.`WRT_DTHMS`) BETWEEN '".addslashes($ARR['START_DT'])."' AND '".addslashes($ARR['END_DT'])."'";
		}else if($ARR['START_DT'] !='' && $ARR['END_DT'] ==''){
			$tmpKeyword .= "AND DATE(`NCC`.`WRT_DTHMS`) >= '".addslashes($ARR['START_DT'])."'";
		}else if($ARR['START_DT'] =='' && $ARR['END_DT'] !=''){
			$tmpKeyword .= "AND DATE(`NCC`.`WRT_DTHMS`) <= '".addslashes($ARR['END_DT'])."'";
		}

		//장비구분 검색
		if($ARR['NCC_NM_SEARCH'] !=''){
			$tmpKeyword .= "AND INSTR(`NCC`.`NCC_NM`,'".addslashes($ARR['NCC_NM_SEARCH'])."')>0 ";
		}

		// 사용여부
		if($ARR['NCC_USE_YN_SEARCH'] != ''){
			// if($ARR['NCC_USE_YN_SEARCH'] == YN_Y){
			// 	$tmpKeyword .= " AND  `NCC`.`NCC_USE_YN` = 'Y' ";
			// }else {
				$tmpKeyword .= "AND `NCC`.`NCC_USE_YN` ='".$ARR['NCC_USE_YN_SEARCH']."'";
			// }
		}

		// 페이징처리를 위해 전체데이터
		$iSQL = "SELECT
					COUNT(*) AS CNT
				FROM
					`NT_SALES`.`NSC_CONSIGN_CTGY` `NCC`
				WHERE DEL_YN = 'N'
				$tmpKeyword
				 
				;"
			;
	
		try{
			$RS_T	= DBManager::getRecordSet($iSQL);
		} catch (Exception $e){
			echo "error : ".$e;
			return ERROR_DB_SELECT_DATA;   //ERROR_DB_SELECT_DATA
		}

		$TOTAL_LIST_COUNT = $RS_T['CNT'];
		$IDX = $RS_T['CNT']-$LimitStart;
		unset($RS_T);
		///

		/** 전체리스트가 아닌경우에만 limitSql 적용 */
		$LIMIT_SQL = "";
		if($ARR['REQ_MODE'] == CASE_LIST){
			$LIMIT_SQL = "LIMIT $LimitStart,$LimitEnd";
		}

		// 리스트 가져오기
		$iSQL = "SELECT 
					`NCC`.`NCC_ID`,
					`NCC`.`NCC_NM`,
					`NCC`.`NCC_ORDER_NUM`,
					`NCC`.`NCC_USE_YN`,
					`NCC`.`WRT_DTHMS`,
					`NCC`.`LAST_DTHMS`,
					`NCC`.`WRT_USERID`
				FROM `NT_SALES`.`NSC_CONSIGN_CTGY` `NCC`
				WHERE DEL_YN = 'N'
				$tmpKeyword 
				ORDER BY `NCC`.`NCC_ORDER_NUM` ASC 
				$LIMIT_SQL;
				"
		;
		// echo $iSQL."\n";
		$Result= DBManager::getResult($iSQL);
		///

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
				"IDX"=>$IDX--,
				"NCC_ID"=>$RS['NCC_ID'],
				"NCC_NM"=>$RS['NCC_NM'],
				"NCC_ORDER_NUM"=>$RS['NCC_ORDER_NUM'],
				"NCC_USE_YN"=>$RS['NCC_USE_YN'],
				"WRT_DTHMS"=>$RS['WRT_DTHMS'],
				"LAST_DTHMS"=>$RS['LAST_DTHMS'],
				"WRT_USERID"=>$RS['WRT_USERID']
			));
		}
		$Result->close();  // 자원 반납
		unset($RS);

		if(empty($rtnArray)) {
			return HAVE_NO_DATA;
		} else {
			$jsonArray = array();
			array_push($jsonArray,$rtnArray);
			array_push($jsonArray,$TOTAL_LIST_COUNT);

			return  $jsonArray;
		}
		
	}

	/**
	 * 해당데이터 조회 
	 * @param {$ARR}
	 * @param {SLL_ID : 조회할 해당 데이터 ID}
	 */
	function getData($ARR) {
		$iSQL = "SELECT 
					`NSC_CONSIGN_CTGY`.`NCC_ID`,
					`NSC_CONSIGN_CTGY`.`NCC_NM`,
					`NSC_CONSIGN_CTGY`.`NCC_ORDER_NUM`,
					`NSC_CONSIGN_CTGY`.`NCC_USE_YN`,
					`NSC_CONSIGN_CTGY`.`WRT_DTHMS`,
					`NSC_CONSIGN_CTGY`.`LAST_DTHMS`,
					`NSC_CONSIGN_CTGY`.`WRT_USERID`
				FROM `NT_SALES`.`NSC_CONSIGN_CTGY`
				WHERE NCC_ID = '".addslashes($ARR['NCC_ID'])."'
				AND DEL_YN = 'N'
				"
		;

		$RS= DBManager::getRecordSet($iSQL);

		if(empty($RS)) {
			unset($RS);
			return HAVE_NO_DATA;
		} else {
			return $RS;
		}
		
	}




	/**
	 * 데이터생성
	 */
	function setData($ARR) {
		
		if($ARR['REQ_MODE'] == CASE_CREATE){
			/** 코드 존재여부
			 * 등록일 경우에는 전체에서 존재하는지
			 */
			$iSQL = "SELECT 
							`NCC_ID`
						FROM `NT_SALES`.`NSC_CONSIGN_CTGY`
						WHERE NCC_ID = '".addslashes($ARR['NCC_ID'])."'
						;"
				;
			$RS= DBManager::getRecordSet($iSQL);
			if(!empty($RS)) {
				return CONFLICT;
			}

			if($ARR['IS_EXIST_ORDER_NUM']==''){
				/** 입력요청한 출력순서가 존재하는지 */
				$iSQL = "SELECT 
							`NCC_ORDER_NUM`
						FROM `NT_SALES`.`NSC_CONSIGN_CTGY`
						WHERE NCC_ORDER_NUM = '".addslashes($ARR['NCC_ORDER_NUM'])."' 
						;"
				;

				$RS= DBManager::getRecordSet($iSQL);
				if(!empty($RS)) {
					return ERROR;
				}
			}

			/** 출력순서 업데이트 */
			$iSQL = "UPDATE  `NT_SALES`.`NSC_CONSIGN_CTGY` 
					SET 
						`NCC_ORDER_NUM` = `NCC_ORDER_NUM`+1
					WHERE `NCC_ORDER_NUM` IN
						(
							SELECT `NCC_ORDER_NUM` FROM 
							(
								SELECT
									@rownum:=@rownum+1 AS IDX,
									`NCC_ORDER_NUM`,
									`NCC_ORDER_NUM` - @rownum AS GRP
								FROM `NT_SALES`.`NSC_CONSIGN_CTGY`, (SELECT @rownum:=0) IDX
								WHERE 1=1
								AND `NCC_ORDER_NUM` >= '".addslashes($ARR['NCC_ORDER_NUM'])."' ORDER BY NCC_ORDER_NUM ASC 
								) AS CMNT WHERE GRP = '".addslashes($ARR['NCC_ORDER_NUM'])."'-1
						)
					;"
			;

			$Result = DBManager::execQuery($iSQL);

			/** insert*/
			if($Result){
				
				$iSQL = "INSERT INTO `NT_SALES`.`NSC_CONSIGN_CTGY`
						(
							`NCC_ID`,
		 					`NCC_NM`,
		 					`NCC_ORDER_NUM`,
		 					`NCC_USE_YN`,
		 					`WRT_DTHMS`,
		 					`LAST_DTHMS`,
		 					`WRT_USERID`,
							`DEL_YN`
						)
						VALUES
						(
							'".addslashes($ARR['NCC_ID'])."',
		 					'".addslashes($ARR['NCC_NM'])."',
		 					'".addslashes($ARR['NCC_ORDER_NUM'])."',
		 					'".addslashes($ARR['NCC_USE_YN'])."',
		 					NOW(),
		 					NOW(),
		 					'".addslashes($_SESSION['USERID'])."',
							'N'
						);"
				;
				
				return DBManager::execQuery($iSQL);
			}else{
				return SERVER_ERROR;
			}
			

		}else{
			/** 부품코드 존재여부
			 * 수정 경우에는 전체에서 본인제외하고 동일한게 있는지
			 */
			if($ARR['IS_EXIST_ORDER_NUM']==''){
				/** 입력요청한 출력순서가 존재하는지 */
				$iSQL = "SELECT 
							`NCC_ORDER_NUM`
						FROM `NT_SALES`.`NSC_CONSIGN_CTGY`
						WHERE NCC_ORDER_NUM = '".addslashes($ARR['NCC_ORDER_NUM'])."' AND
							`NCC_ID` != '".addslashes($ARR['NCC_ID'])."' 
						;"
				;

				$RS= DBManager::getRecordSet($iSQL);
				if(!empty($RS)) {
					return ERROR;
				}
			 }
			
			
			$Result = true;
				
			if($ARR['NCC_ORDER_NUM'] != $ARR['ORI_NCC_ORDER_NUM'] ){
				$iSQL = "UPDATE  `NT_SALES`.`NSC_CONSIGN_CTGY` 
						SET 
							`NCC_ORDER_NUM` = `NCC_ORDER_NUM`+1
						WHERE `NCC_ORDER_NUM` IN
							(
								SELECT `NCC_ORDER_NUM` FROM 
								(
									SELECT
										@rownum:=@rownum+1 AS IDX,
										`NCC_ORDER_NUM`,
										`NCC_ORDER_NUM` - @rownum AS GRP
									FROM `NT_SALES`.`NSC_CONSIGN_CTGY`, (SELECT @rownum:=0) IDX
									WHERE 1=1
									AND `NCC_ORDER_NUM` >= '".addslashes($ARR['NCC_ORDER_NUM'])."' ORDER BY NCC_ORDER_NUM ASC 
									) AS CMNT WHERE GRP = '".addslashes($ARR['NCC_ORDER_NUM'])."'-1
							)
						;"
				;
				$Result = DBManager::execQuery($iSQL);
			}

			if($Result){
				$iSQL = "UPDATE  `NT_SALES`.`NSC_CONSIGN_CTGY`
						 SET 
						 	`NCC_NM` = '".addslashes($ARR['NCC_NM'])."',
		 					`NCC_ORDER_NUM`= '".addslashes($ARR['NCC_ORDER_NUM'])."',
		 					`NCC_USE_YN` = '".addslashes($ARR['NCC_USE_YN'])."',
		 					`LAST_DTHMS` = now(),
		 					`WRT_USERID` = '".addslashes($_SESSION['USERID'])."'
						WHERE `NCC_ID`='".addslashes($ARR['NCC_ID'])."' 
						;"
				;
				return DBManager::execQuery($iSQL);
			}else{
				return SERVER_ERROR;
			}
		
		}	
	}

	/* 위탁장비 장비구분 조회  */
	function getType($ARR) {
		$iSQL = "SELECT `NCC_ID`,
						`NCC_NM`,
						`NCC_ORDER_NUM`,
						`NCC_USE_YN`,
						`WRT_DTHMS`,
						`LAST_DTHMS`,
						`WRT_USERID`
				FROM `NT_SALES`.`NSC_CONSIGN_CTGY`
				WHERE NCC_USE_YN = 'Y'
				AND DEL_YN = 'N'
				ORDER BY NCC_ORDER_NUM ;
		";

		$Result= DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
				"ID"=>$RS['NCC_ID'],
				"NM"=>$RS['NCC_NM']
			));
		}
		$Result->close();  // 자원 반납
		unset($RS);
		if(empty($rtnArray)) {
			return HAVE_NO_DATA;
		} else {
			return $rtnArray;
		}
		
	}

	/* 위탁장비 조회  */
	function getConsign($ARR) {
		$iSQL = "SELECT `NCP_ID`,
						`NCP_NM`
				FROM `NT_SALES`.`NSC_CONSIGN_PARTS`
				WHERE NCP_USE_YN = 'Y'
				ORDER BY NCP_ORDER_NUM ;
		";

		$Result= DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
				"ID"=>$RS['NCP_ID'],
				"NM"=>$RS['NCP_NM']
			));
		}
		$Result->close();  // 자원 반납
		unset($RS);
		if(empty($rtnArray)) {
			return HAVE_NO_DATA;
		} else {
			return $rtnArray;
		}
		
	}



	/**
	 * 삭제
	 * @param {$ARR}
	 * @param {MGC_ID : 삭제할 해당 데이터 ID}
	 */
	function delData($ARR) {
		$iSQL = "UPDATE `NT_SALES`.`NSC_CONSIGN_CTGY`
					SET  
						`LAST_DTHMS`= NOW(), 
						`DEL_YN`	='Y'
					WHERE `NCC_ID`='".addslashes($ARR['NCC_ID'])."'
					;"
		;

		return DBManager::execQuery($iSQL);
	}




}
	// 0. 객체 생성
	$clsCategory = new clsCategory(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$clsCategory->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
