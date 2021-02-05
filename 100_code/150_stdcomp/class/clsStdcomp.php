<?php

class clsStandardComponent extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	// Class Object Constructor
	function clsStandardComponent($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
		$this->DATA_SAVE_PATH = $DATA_SAVE_PATH;
		$this->ALLOW_FILE_EXT = $ALLOW_FILE_EXT;
	}

	/**
	 * 전체데이터 가져오는 함수
	*/
	function getTotalList(){
		$iSQL = "SELECT 
					@rownum:=@rownum+1 AS IDX,
					`CPP_ID`,
					`CPP_PART_TYPE`,
					`CPP_NM`,
					`CPP_NM_SHORT`,
					`CPP_UNIT`,
					`CPP_USE_YN`,
					`CPP_ORDER_NUM`,
					(SELECT COUNT(*) FROM `NT_CODE`.`NCP_PART_SUB` WHERE `CPP_ID`= `CPP_ID`) AS `CPPS_COUNT`,
					`WRT_DTHMS`,
					`LAST_DTHMS`,
					`WRT_USERID`
				FROM `NT_CODE`.`NCP_PART`, (SELECT @rownum:=0) IDX
				WHERE DEL_YN = 'N'
				ORDER BY IDX DESC"
		;

		try{
			$Result= DBManager::getResult($iSQL);
		} catch (Exception $e){
			echo "error : ".$e;
			return ERROR_DB_SELECT_DATA;   //ERROR_DB_SELECT_DATA
		}
		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
				"IDX"=>$RS['IDX'],
				"CPP_ID"=>$RS['CPP_ID'],
				"CPP_PART_TYPE"=>$RS['CPP_PART_TYPE'],
				"CPP_NM"=>$RS['CPP_NM'],
				"CPP_NM_SHORT"=>$RS['CPP_NM_SHORT'],
				"CPP_UNIT"=>$RS['CPP_UNIT'],
				"CPP_USE_YN"=>$RS['CPP_USE_YN'],
				"CPP_ORDER_NUM"=>$RS['CPP_ORDER_NUM'],
				"CPPS_COUNT"=>$RS['CPPS_COUNT'],
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
	 * 조회 
	 * @param {$ARR}
	 * @param {CURR_PAGE : 현재페이지, PER_PAGE : 몇개를 조회할 것인지, KEYWORD : 검색조건}
	 */
	function getList($ARR) {
		$LimitStart = ($ARR['CURR_PAGE']-1) * $ARR['PER_PAGE'];
		$LimitEnd   = $ARR['PER_PAGE'];
		$tmpKeyword = "";
		
		if($ARR['COMP_TYPE_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(`CPP`.`CPP_PART_TYPE`,'".addslashes($ARR['COMP_TYPE_SEARCH'])."')>0 ";
		} 
	
		// 페이징처리를 위해 전체데이터
		$iSQL = "
				SELECT
					COUNT(*) AS CNT
				FROM
					`NT_CODE`.`NCP_PART` `CPP`
				WHERE	`CPP`.`DEL_YN` = 'N'
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
					`CPP`.`CPP_ID`,
					`CPP`.`CPP_PART_TYPE`,
					`CPP`.`CPP_NM`,
					`CPP`.`CPP_NM_SHORT`,
					`CPP`.`CPP_UNIT`,
					`CPP`.`CPP_USE_YN`,
					`CPP`.`CPP_ORDER_NUM`,
					(SELECT COUNT(*) FROM `NT_CODE`.`NCP_PART_SUB` WHERE `CPP_ID`= `CPP`.`CPP_ID`) AS `CPPS_COUNT`,
					`CPP`.`WRT_DTHMS`,
					`CPP`.`LAST_DTHMS`,
					`CPP`.`WRT_USERID`
				FROM `NT_CODE`.`NCP_PART` `CPP`
				WHERE `CPP`.`DEL_YN`  = 'N'  
					 $tmpKeyword 
				ORDER BY `CPP`.CPP_ORDER_NUM ASC 
				$LIMIT_SQL
				;"
		;

		$Result= DBManager::getResult($iSQL);
		///

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
				"IDX"=>$IDX--,
				"CPP_ID"=>$RS['CPP_ID'],
				"CPP_PART_TYPE"=>$RS['CPP_PART_TYPE'],
				"CPP_NM"=>$RS['CPP_NM'],
				"CPP_NM_SHORT"=>$RS['CPP_NM_SHORT'],
				"CPP_UNIT"=>$RS['CPP_UNIT'],
				"CPP_USE_YN"=>$RS['CPP_USE_YN'],
				"CPP_ORDER_NUM"=>$RS['CPP_ORDER_NUM'],
				"CPPS_COUNT"=>$RS['CPPS_COUNT'],
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
	 * @param {ALLM_ID : 조회할 해당 데이터 ID}
	 */
	function getData($CPP_ID) {
		$iSQL = "SELECT 
					COUNT(*) 
				FROM `NT_CODE`.`NCP_PART_SUB` 
				WHERE `CPP_ID`= '$CPP_ID'
				"
		;

		$RS= DBManager::getRecordSet($iSQL);

		if(empty($RS)) {
			unset($RS);
			return false;
		} else {
			unset($RS);
			return true;
		}
		
	}

	/**
	 * 데이터생성
	 */
	function setData($ARR) {
		
		if($ARR['REQ_MODE'] == CASE_CREATE){
			/** 부품코드 존재여부
			 * 등록일 경우에는 전체에서 존재하는지
			 */
			$iSQL = "SELECT 
							`CPP_ID`
						FROM `NT_CODE`.`NCP_PART`
						WHERE CPP_ID = '".addslashes($ARR['CPP_ID'])."'
						;"
				;
			$RS= DBManager::getRecordSet($iSQL);
			if(!empty($RS)) {
				return CONFLICT;
			}

			if($ARR['IS_EXIST_ORDER_NUM']==''){
				/** 입력요청한 출력순서가 존재하는지 */
				$iSQL = "SELECT 
							`CPP_ORDER_NUM`
						FROM `NT_CODE`.`NCP_PART`
						WHERE CPP_ORDER_NUM = '".addslashes($ARR['CPP_ORDER_NUM'])."' AND
							DEL_YN = 'N'
						;"
				;

				$RS= DBManager::getRecordSet($iSQL);
				if(!empty($RS)) {
					return ERROR;
				}
			}

			/** 출력순서 업데이트 */
			$iSQL = "UPDATE  `NT_CODE`.`NCP_PART` 
					SET 
						`CPP_ORDER_NUM` = `CPP_ORDER_NUM`+1
					WHERE `CPP_ORDER_NUM` IN
						(
							SELECT `CPP_ORDER_NUM` FROM 
							(
								SELECT
									@rownum:=@rownum+1 AS IDX,
									`CPP_ORDER_NUM`,
									`CPP_ORDER_NUM` - @rownum AS GRP
								FROM `NT_CODE`.`NCP_PART`, (SELECT @rownum:=0) IDX
								WHERE `DEL_YN` = 'N' 
								AND `CPP_ORDER_NUM` >= '".addslashes($ARR['CPP_ORDER_NUM'])."' ORDER BY CPP_ORDER_NUM ASC 
								) AS CMNT WHERE GRP = '".addslashes($ARR['CPP_ORDER_NUM'])."'-1
						)
					;"
			;

			$Result = DBManager::execQuery($iSQL);

			/** insert*/
			if($Result){
				$SESSION_ID = $_SESSION['USERID'] == null ? 'SYSYTEM' : $_SESSION['USERID'];
				$iSQL = "INSERT INTO `NT_CODE`.`NCP_PART`
						(
							`CPP_ID`,
							`CPP_PART_TYPE`,
							`CPP_NM`,
							`CPP_NM_SHORT`,
							`CPP_UNIT`,
							`CPP_USE_YN`,
							`CPP_ORDER_NUM`,
							`WRT_DTHMS`,
							`LAST_DTHMS`,
							`WRT_USERID`,
							`DEL_YN`
						)
						VALUES
						(
							'".addslashes($ARR['CPP_ID'])."',
							'".addslashes($ARR['CPP_PART_TYPE'])."',
							'".addslashes($ARR['CPP_NM'])."',
							'".addslashes($ARR['CPP_NM_SHORT'])."',
							'".addslashes($ARR['CPP_UNIT'])."',
							'".addslashes($ARR['CPP_USE_YN'])."',
							'".addslashes($ARR['CPP_ORDER_NUM'])."',
							NOW(),
							NOW(),
							'".$SESSION_ID."',
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
							`CPP_ORDER_NUM`
						FROM `NT_CODE`.`NCP_PART`
						WHERE CPP_ORDER_NUM = '".addslashes($ARR['CPP_ORDER_NUM'])."' AND
							`CPP_ID` != '".addslashes($ARR['CPP_ID'])."' AND
							DEL_YN = 'N'
						;"
				;

				$RS= DBManager::getRecordSet($iSQL);
				if(!empty($RS)) {
					return ERROR;
				}
			 }
			
			
			$Result = true;
				
			if($ARR['CPP_ORDER_NUM'] != $ARR['ORI_CPP_ORDER_NUM'] ){
				$iSQL = "UPDATE  `NT_CODE`.`NCP_PART` 
						SET 
							`CPP_ORDER_NUM` = `CPP_ORDER_NUM`+1
						WHERE `CPP_ORDER_NUM` IN
							(
								SELECT `CPP_ORDER_NUM` FROM 
								(
									SELECT
										@rownum:=@rownum+1 AS IDX,
										`CPP_ORDER_NUM`,
										`CPP_ORDER_NUM` - @rownum AS GRP
									FROM `NT_CODE`.`NCP_PART`, (SELECT @rownum:=0) IDX
									WHERE `DEL_YN` = 'N' 
									AND `CPP_ORDER_NUM` >= '".addslashes($ARR['CPP_ORDER_NUM'])."' ORDER BY CPP_ORDER_NUM ASC 
									) AS CMNT WHERE GRP = '".addslashes($ARR['CPP_ORDER_NUM'])."'-1
							)
						;"
				;
				$Result = DBManager::execQuery($iSQL);
			}

			if($Result){
				$iSQL = "UPDATE  `NT_CODE`.`NCP_PART`
						 SET 
							`CPP_ID` = '".addslashes($ARR['CPP_ID'])."',
							`CPP_PART_TYPE` = '".addslashes($ARR['CPP_PART_TYPE'])."',
							`CPP_NM`= '".addslashes($ARR['CPP_NM'])."',
							`CPP_NM_SHORT` = '".addslashes($ARR['CPP_NM_SHORT'])."',
							`CPP_UNIT` = '".addslashes($ARR['CPP_UNIT'])."',
							`CPP_USE_YN` = '".addslashes($ARR['CPP_USE_YN'])."',
							`CPP_ORDER_NUM` = '".addslashes($ARR['CPP_ORDER_NUM'])."',
							`LAST_DTHMS` = now()
						WHERE `CPP_ID`='".addslashes($ARR['CPP_ID'])."' 
						;"
				;
				return DBManager::execQuery($iSQL);
			}else{
				return SERVER_ERROR;
			}
		
		}	
	}

	/**
	 * 삭제
	 * @param {$ARR}
	 * @param {ALLM_ID : 삭제할 해당 데이터 ID}
	 */
	function delData($ARR) {

		$iSQL = "UPDATE `NT_CODE`.`NCP_PART`
					SET  
						`LAST_DTHMS`= NOW(), 
						`DEL_YN`	='Y'
					WHERE `CPP_ID`='".addslashes($ARR['CPP_ID'])."'
				;"
		;

		$Result= DBManager::execQuery($iSQL);
		
		/** PARTS update결과에 따라 */
		if($Result){
			unset($Result);
			$Result = $this->getData($ARR['CPP_ID']);

			if($Result){
				/**취급부품이 있는경우 해당 부품들도 DELETE 처리 */
				$iSQL = "UPDATE `NT_CODE`.`NCP_PART_SUB`
						SET  
							`LAST_DTHMS`= NOW(), 
							`DEL_YN`	='Y'
						WHERE `CPP_ID`='".addslashes($ARR['CPP_ID'])."'
						;"
				;
				return DBManager::execQuery($iSQL);
			}else{
				/** 없으면 true 리턴 */
				return true;
			}
			
		}else{
			return $Result;
		}
		
	}

	/** 부품타입 가져오기 */
	function getPartsType(){
		$iSQL = "SELECT 
					`CCD_DOMAIN`,
					`CCD_PREFIX_CD`,
					`CCD_VAL_CD`,
					`CCD_NM`,
					`CCD_TXT`,
					`CCD_MEMO_TXT`
				FROM `NT_CODE`.`NCL_CODE_DIC`
				WHERE `CCD_DOMAIN` = 'PART_TYPE'
				;"
		;
		$Result= DBManager::getResult($iSQL);
		///

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
				"CCD_DOMAIN"=>$RS['CCD_DOMAIN'],
				"CCD_PREFIX_CD"=>$RS['CCD_PREFIX_CD'],
				"CCD_VAL_CD"=>$RS['CCD_VAL_CD'],
				"CCD_NM"=>$RS['CCD_NM'],
				"CCD_TXT"=>$RS['CCD_TXT'],
				"CCD_MEMO_TXT"=>$RS['CCD_MEMO_TXT']
			));
		}
		$Result->close();  // 자원 반납
		unset($RS);

		if(empty($rtnArray)) {
			return HAVE_NO_DATA;
		} else {
			return  $rtnArray;
		}
	}


}
	// 0. 객체 생성
	$clsStandardComponent = new clsStandardComponent(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$clsStandardComponent->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
