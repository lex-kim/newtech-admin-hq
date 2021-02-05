<?php

class clsHandleComponent extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	// Class Object Constructor
	function clsHandleComponent($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
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
		if($ARR['DATE_TYPE_SEARCH'] != '') {
			if($ARR['DATE_START'] !='' && $ARR['DATE_END'] !=''){
				$tmpKeyword .= "AND DATE(`CPPS`.".$ARR['DATE_TYPE_SEARCH'].") BETWEEN '".addslashes($ARR['DATE_START'])."' AND '".addslashes($ARR['DATE_END'])."'";
			}else if($ARR['DATE_START'] !='' && $ARR['DATE_END'] ==''){
				$tmpKeyword .= "AND DATE(`CPPS`.".$ARR['DATE_TYPE_SEARCH'].") >= '".addslashes($ARR['DATE_START'])."'";
			}else{
				$tmpKeyword .= "AND DATE(`CPPS`.".$ARR['DATE_TYPE_SEARCH'].") <= '".addslashes($ARR['DATE_END'])."'";
			}
			
		} 

		if($ARR['CPP_PART_TYPE_SARCH'] != '') {
			$tmpKeyword .= "AND INSTR(`CPP`.`CPP_PART_TYPE`,'".addslashes($ARR['CPP_PART_TYPE_SARCH'])."')>0 ";
		}
		if($ARR['CPP_NM_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(`CPP`.`CPP_ID`,'".addslashes($ARR['CPP_NM_SEARCH'])."')>0 ";
		}	
		if(!empty($ARR['CPPS_ID_SEARCH'])){
			$tmpKeyword .= "AND (";
			foreach ($ARR['CPPS_ID_SEARCH']as $index => $value) {
				if($index < count($ARR['CPPS_ID_SEARCH'])-1){
					$tmpKeyword .= "INSTR(`CPPS`.`CPPS_ID`,'".$value."')>0 OR ";
				}else{
					$tmpKeyword .= "INSTR(`CPPS`.`CPPS_ID`,'".$value."')>0";
				}	
			}
			$tmpKeyword .= ")";
		}
		if(!empty($ARR['CPPS_NM_SEARCH'])){
			$tmpKeyword .= "AND (";
			foreach ($ARR['CPPS_NM_SEARCH']as $index => $value) {
				if($index < count($ARR['CPPS_NM_SEARCH'])-1){
					$tmpKeyword .= "INSTR(`CPPS`.`CPPS_NM`,'".$value."')>0 OR ";
				}else{
					$tmpKeyword .= "INSTR(`CPPS`.`CPPS_NM`,'".$value."')>0";
				}	
			}
			$tmpKeyword .= ")";
		}
		if(!empty($ARR['CPPS_NM_SHORT_SEARCH'])){
			$tmpKeyword .= "AND (";
			foreach ($ARR['CPPS_NM_SHORT_SEARCH']as $index => $value) {
				if($index < count($ARR['CPPS_NM_SHORT_SEARCH'])-1){
					$tmpKeyword .= "INSTR(`CPPS`.`CPPS_NM_SHORT`,'".$value."')>0 OR ";
				}else{
					$tmpKeyword .= "INSTR(`CPPS`.`CPPS_NM_SHORT`,'".$value."')>0";
				}	
			}
			$tmpKeyword .= ")";
		}
		if($ARR['CPPS_RATIO_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(`CPPS`.`CPPS_RATIO`,'".addslashes($ARR['CPPS_RATIO_SEARCH'])."')>0 ";
		}
		if($ARR['CPPS_GENUINE_YN_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(`CPPS`.`CPPS_GENUINE_YN`,'".addslashes($ARR['CPPS_GENUINE_YN_SEARCH'])."')>0 ";
		}
		if($ARR['CPPS_RECKON_YN_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(`CPPS`.`CPPS_RECKON_YN`,'".addslashes($ARR['CPPS_RECKON_YN_SEARCH'])."')>0 ";
		}
		if($ARR['CPPS_INOUT_YN_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(`CPPS`.`CPPS_INOUT_YN`,'".addslashes($ARR['CPPS_INOUT_YN_SEARCH'])."')>0 ";
		}
	
		// 페이징처리를 위해 전체데이터
		$iSQL = "
				SELECT
					COUNT(`CPPS`.`CPPS_ID`) AS CNT
				FROM
					`NT_CODE`.`NCP_PART_SUB` `CPPS`,
					`NT_CODE`.`NCP_PART` `CPP`
				WHERE	`CPPS`.`DEL_YN` = 'N'
				AND `CPP`.`CPP_ID` = `CPPS`.`CPP_ID`
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
					`CPPS`.`CPPS_ID`,
					`CPP`.`CPP_PART_TYPE`,
					`CPP`.`CPP_ID`,
					`CPP`.`CPP_NM`,
					`CPP`.`CPP_NM_SHORT`,
					`CPP`.`CPP_UNIT`,
					`CPPS`.`CPP_ID`,
					`CPPS`.`CPPS_NM`,
					`CPPS`.`CPPS_ORDER_NUM`,
					`CPPS`.`CPPS_NM_SHORT`,
					`CPPS`.`CPPS_UNIT`,
					`CPPS`.`CPPS_RATIO`,
					`CPPS`.`CPPS_GENUINE_YN`,
					`CPPS`.`CPPS_PRICE`,
					`CPPS`.`CPPS_APPLY_DT`,
					`CPPS`.`CPPS_RECKON_YN`,
					`CPPS`.`CPPS_INOUT_YN`,
					DATE_FORMAT(`CPPS`.`WRT_DTHMS`, '%Y-%m-%d')AS 'WRT_DTHMS',
					`CPPS`.`LAST_DTHMS`,
					`CPPS`.`WRT_USERID`
				FROM `NT_CODE`.`NCP_PART_SUB` `CPPS`,
					`NT_CODE`.`NCP_PART` `CPP`
				WHERE `CPPS`.`DEL_YN`  = 'N'  
				AND `CPP`.`CPP_ID` =  `CPPS`.`CPP_ID`
					 $tmpKeyword 
				ORDER BY `CPPS`.`CPPS_ORDER_NUM` ASC 
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
				"CPPS_ID"=>$RS['CPPS_ID'],
				"CPPS_NM"=>$RS['CPPS_NM'],
				"CPP_PART_TYPE"=>$RS['CPP_PART_TYPE'],
				"CPP_ID"=>$RS['CPP_ID'],
				"CPP_NM"=>$RS['CPP_NM'],
				"CPP_NM_SHORT"=>$RS['CPP_NM_SHORT'],
				"CPP_UNIT"=>$RS['CPP_UNIT'],
				"CPPS_ORDER_NUM"=>$RS['CPPS_ORDER_NUM'],
				"CPPS_NM_SHORT"=>$RS['CPPS_NM_SHORT'],
				"CPPS_UNIT"=>$RS['CPPS_UNIT'],
				"CPPS_RATIO"=>$RS['CPPS_RATIO'],
				"CPPS_GENUINE_YN"=>$RS['CPPS_GENUINE_YN'],
				"CPPS_PRICE"=>$RS['CPPS_PRICE'],
				"CPPS_APPLY_DT"=>$RS['CPPS_APPLY_DT'],
				"CPPS_RECKON_YN"=>$RS['CPPS_RECKON_YN'],
				"CPPS_INOUT_YN"=>$RS['CPPS_INOUT_YN'],
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
	function getData($ARR) {
		$iSQL = "SELECT 
					`ALLM_ID`,
					`ALLM_NM`,
					`ALLM_MEMO`,
					`WRT_DTHMS`,
					`LAST_DTHMS`,
					`WRT_USERID`
				FROM `NT_AUTH`.`NAL_LVL_MAIN`
				WHERE DEL_YN = 'N'
				AND ALLM_ID = '".addslashes($ARR['ALLM_ID'])."'
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
			/** 부품코드 존재여부
			 * 등록일 경우에는 전체에서 존재하는지
			 */
			$iSQL = "SELECT 
							`CPPS_ID`
						FROM `NT_CODE`.`NCP_PART_SUB`
						WHERE CPPS_ID = '".addslashes($ARR['CPPS_ID'])."'
						;"
				;
			$RS= DBManager::getRecordSet($iSQL);
			if(!empty($RS)) {
				return CONFLICT;
			}

			if(empty($ARR['IS_EXIST_ORDER_NUM'])){
				/** 입력요청한 출력순서가 존재하는지 */
				$iSQL = "SELECT 
							`CPPS_ORDER_NUM`
						FROM `NT_CODE`.`NCP_PART_SUB`
						WHERE CPPS_ORDER_NUM = '".addslashes($ARR['CPPS_ORDER_NUM'])."' AND
							DEL_YN = 'N'
						;"
				;

				$RS= DBManager::getRecordSet($iSQL);
				if(!empty($RS)) {
					return ERROR;
				}
			}

			/** 출력순서 업데이트 */
			$iSQL = "UPDATE  `NT_CODE`.`NCP_PART_SUB` 
					SET 
						`CPPS_ORDER_NUM` = `CPPS_ORDER_NUM`+1
					WHERE `CPPS_ORDER_NUM` IN
						(
							SELECT `CPPS_ORDER_NUM` FROM 
							(
								SELECT
									@rownum:=@rownum+1 AS IDX,
									`CPPS_ORDER_NUM`,
									`CPPS_ORDER_NUM` - @rownum AS GRP
								FROM `NT_CODE`.`NCP_PART_SUB`, (SELECT @rownum:=0) IDX
								WHERE `DEL_YN` = 'N' 
								AND `CPPS_ORDER_NUM` >= '".addslashes($ARR['CPPS_ORDER_NUM'])."' ORDER BY CPPS_ORDER_NUM ASC 
								) AS CMNT WHERE GRP = '".addslashes($ARR['CPPS_ORDER_NUM'])."'-1
						)
					;"
			;

			$Result = DBManager::execQuery($iSQL);

			/** insert*/
			if($Result){
				$iSQL = "INSERT INTO `NT_CODE`.`NCP_PART_SUB`
						(
							`CPPS_ID`,
							`CPP_ID`,
							`CPPS_NM`,
							`CPPS_ORDER_NUM`,
							`CPPS_NM_SHORT`,
							`CPPS_UNIT`,
							`CPPS_RATIO`,
							`CPPS_GENUINE_YN`,
							`CPPS_PRICE`,
							`CPPS_APPLY_DT`,
							`CPPS_RECKON_YN`,
							`CPPS_INOUT_YN`,
							`WRT_DTHMS`,
							`LAST_DTHMS`,
							`WRT_USERID`,
							`DEL_YN`
						)
						VALUES
						(
							'".addslashes($ARR['CPPS_ID'])."',
							'".addslashes($ARR['CPP_ID'])."',
							'".addslashes($ARR['CPPS_NM'])."',
							'".addslashes($ARR['CPPS_ORDER_NUM'])."',
							'".addslashes($ARR['CPPS_NM_SHORT'])."',
							'".addslashes($ARR['CPPS_UNIT'])."',
							'".addslashes($ARR['CPPS_RATIO'])."',
							'".addslashes($ARR['CPPS_GENUINE_YN'])."',
							'".addslashes($ARR['CPPS_PRICE'])."',
							'".addslashes($ARR['CPPS_APPLY_DT'])."',
							'".addslashes($ARR['CPPS_RECKON_YN'])."',
							'".addslashes($ARR['CPPS_INOUT_YN'])."',
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
			if($ARR['ORI_CPPS_ID'] != $ARR['CPPS_ID'] ){
				$iSQL = "SELECT 
							`CPPS_ID`
						FROM `NT_CODE`.`NCP_PART_SUB`
						AND CPPS_ID = '".addslashes($ARR['CPPS_ID'])."'
						;"
				;
		
				$RS= DBManager::getRecordSet($iSQL);
				if(!empty($RS)) {
					return CONFLICT;
				}
			}

			if(empty($ARR['IS_EXIST_ORDER_NUM'])){
				/** 입력요청한 출력순서가 존재하는지 */
				$iSQL = "SELECT 
							`CPPS_ORDER_NUM`
						FROM `NT_CODE`.`NCP_PART_SUB`
						WHERE CPPS_ORDER_NUM = '".addslashes($ARR['CPPS_ORDER_NUM'])."' AND
							`CPPS_ID` != '".addslashes($ARR['ORI_CPPS_ID'])."' AND
							DEL_YN = 'N'
						;"
				;
	
				$RS= DBManager::getRecordSet($iSQL);
				if(!empty($RS)) {
					return ERROR;
				}
			 }
			
			
			$Result = true;
				
			if($ARR['CPPS_ORDER_NUM'] != $ARR['ORI_CPPS_ORDER_NUM'] ){
				$iSQL = "UPDATE  `NT_CODE`.`NCP_PART_SUB` 
						SET 
							`CPPS_ORDER_NUM` = `CPPS_ORDER_NUM`+1
						WHERE `CPPS_ORDER_NUM` IN
							(
								SELECT `CPPS_ORDER_NUM` FROM 
								(
									SELECT
										@rownum:=@rownum+1 AS IDX,
										`CPPS_ORDER_NUM`,
										`CPPS_ORDER_NUM` - @rownum AS GRP
									FROM `NT_CODE`.`NCP_PART_SUB`, (SELECT @rownum:=0) IDX
									WHERE `DEL_YN` = 'N' 
									AND `CPPS_ORDER_NUM` >= '".addslashes($ARR['CPPS_ORDER_NUM'])."' ORDER BY CPPS_ORDER_NUM ASC 
									) AS CMNT WHERE GRP = '".addslashes($ARR['CPPS_ORDER_NUM'])."'-1
							)
						;"
				;
				$Result = DBManager::execQuery($iSQL);
			}

			if($Result){
				$iSQL = "UPDATE  `NT_CODE`.`NCP_PART_SUB`
						 SET 
							`CPPS_ID` = '".addslashes($ARR['CPPS_ID'])."',
							`CPP_ID` = '".addslashes($ARR['CPP_ID'])."',
							`CPPS_NM`= '".addslashes($ARR['CPPS_NM'])."',
							`CPPS_ORDER_NUM` = '".addslashes($ARR['CPPS_ORDER_NUM'])."',
							`CPPS_NM_SHORT` = '".addslashes($ARR['CPPS_NM_SHORT'])."',
							`CPPS_UNIT` = '".addslashes($ARR['CPPS_UNIT'])."',
							`CPPS_RATIO` = '".addslashes($ARR['CPPS_RATIO'])."',
							`CPPS_GENUINE_YN` = '".addslashes($ARR['CPPS_GENUINE_YN'])."',
							`CPPS_PRICE` = '".addslashes($ARR['CPPS_PRICE'])."',
							`CPPS_APPLY_DT` = '".addslashes($ARR['CPPS_APPLY_DT'])."',
							`CPPS_RECKON_YN` = '".addslashes($ARR['CPPS_RECKON_YN'])."',
							`CPPS_INOUT_YN` = '".addslashes($ARR['CPPS_INOUT_YN'])."',
							`LAST_DTHMS` = now()
						WHERE `CPPS_ID`='".addslashes($ARR['ORI_CPPS_ID'])."' 
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
		$iSQL = "UPDATE `NT_CODE`.`NCP_PART_SUB`
					SET  
						`LAST_DTHMS`= NOW(), 
						`DEL_YN`	='Y'
					WHERE `CPPS_ID`='".addslashes($ARR['CPPS_ID'])."'
					;"
		;

		return DBManager::execQuery($iSQL);
	}

	/** 부품타입 가져오기 */
	function getPartsType(){
		/** 부품타입 가져오기 */
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

		$jsonArray = array();

		if(empty($rtnArray)) {
			return HAVE_NO_DATA;
		} else {
			array_push($jsonArray,$rtnArray);
		}


		/** 기준부품 가져오기 */
		$iSQL = "SELECT 
					`CPP_ID`,
					`CPP_NM`,
					`CPP_UNIT`
				FROM `NT_CODE`.`NCP_PART`
				WHERE `DEL_YN`='N'
				AND `CPP_USE_YN` = 'Y'
				ORDER BY `CPP_ORDER_NUM` ASC
				;"
		;
 
		$Result= DBManager::getResult($iSQL);
		///

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"CPP_ID"=>$RS['CPP_ID'],
					"CPP_UNIT"=>$RS['CPP_UNIT'],
					"CPP_NM"=>$RS['CPP_NM']
			));
		}
		$Result->close();  // 자원 반납
		unset($RS);

	
		array_push($jsonArray,$rtnArray);

		/** 기준부품 가져오기 */
		$iSQL = "SELECT 
					`CPPS_ID`,
					`CPPS_NM`,
					`CPPS_NM_SHORT`
				FROM `NT_CODE`.`NCP_PART_SUB`
				WHERE `DEL_YN`='N'
				ORDER BY CPPS_ORDER_NUM ASC
				;"
		;
		$Result= DBManager::getResult($iSQL);
		///

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"CPPS_ID"=>$RS['CPPS_ID'],
					"CPPS_NM"=>$RS['CPPS_NM'],
					"CPPS_NM_SHORT"=>$RS['CPPS_NM_SHORT'],
			));
		}
		$Result->close();  // 자원 반납
		unset($RS);

	
		array_push($jsonArray,$rtnArray);
		return $jsonArray;
	}


}
	// 0. 객체 생성
	$clsHandleComponent = new clsHandleComponent(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$clsHandleComponent->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
