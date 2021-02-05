<?php

class cIwmngt extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	// Class Object Constructor
	function cIwmngt($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
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

		/** 전체리스트가 아닌경우에만 limitSql 적용 */
		$LIMIT_SQL = "";
		if($ARR['REQ_MODE'] == CASE_LIST){
			$LIMIT_SQL = "LIMIT $LimitStart,$LimitEnd";
		}
		$tmpKeyword = "";

		if($ARR['SEARCH_START_DT'] != ''){
			$tmpKeyword .= "AND NI.MII_ENROLL_DT >= '".$ARR['SEARCH_START_DT']."' ";
		}
		if($ARR['SEARCH_END_DT'] != ''){
			$tmpKeyword .= "AND NI.MII_ENROLL_DT <= '".$ARR['SEARCH_END_DT']."' ";
		}
		if($ARR['SEARCH_INSU_TYPE'] != ''){
			$tmpKeyword .= "AND NI.RIT_ID = '".$ARR['SEARCH_INSU_TYPE']."' ";
		}
		if($ARR['SEARCH_INSU_NM'] != ''){
			$tmpKeyword .= "AND NI.MII_ID = '".addslashes($ARR['SEARCH_INSU_NM'])."' ";
		}
		if($ARR['SEARCH_CHG'] != ''){
			if($ARR['SEARCH_CHG'] == 'MII_CHG_NM'){
				$tmpKeyword .= "AND INSTR(CAST(AES_DECRYPT(NIS.`MII_CHG_NM_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR),'".addslashes($ARR['CHG_KEYWORD'])."')>0 ";

			}else if($ARR['SEARCH_CHG'] == 'MII_CHG_TEL'){
			$tmpKeyword .= "AND ( INSTR(CAST(AES_DECRYPT(NIS.`MII_CHG_TEL1_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR),'".addslashes($ARR['CHG_KEYWORD'])."')>0 
								OR INSTR(CAST(AES_DECRYPT(NIS.`MII_CHG_TEL2_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR),'".addslashes($ARR['CHG_KEYWORD'])."')>0 ) ";

			}

		}

		// 페이징처리를 위해 전체데이터
		$iSQL = "SELECT
					COUNT(*) AS CNT
				FROM 
					`NT_MAIN`.`NMI_INSU` NI,
					`NT_MAIN`.`NMI_INSU_SUB` NIS
				WHERE NIS.MII_ID = NI.MII_ID 
                AND NIS.DEL_YN = 'N'
                AND NI.DEL_YN = 'N'
				$tmpKeyword
				;
		";
		// echo $iSQL."\n";
		try{
			$RS_T	= DBManager::getRecordSet($iSQL);
		} catch (Exception $e){
			echo "error : ".$e;
			return ERROR_DB_SELECT_DATA;   //ERROR_DB_SELECT_DATA
		}

		$TOTAL_LIST_COUNT = $RS_T['CNT'];
		$IDX = $RS_T['CNT']-$LimitStart;
		unset($RS_T);

		// 리스트 가져오기
		$iSQL = "SELECT 
					NI.`MII_ID`,
					NI.`MII_ORDER_NUM`,
					NI.`RIT_ID`,
					NT_CODE.GET_CODE_NM(NI.RIT_ID) AS RIT_NM,
					NI.`MII_NM`,
					(
						SELECT COUNT(NBI.NB_ID) 
						FROM NT_BILL.NBB_BILL_INSU NBI,
							 NT_BILL.NBB_BILL NB
						WHERE `NT_MAIN`.`GET_INSU_CHG_MII_ID`(NBI.NIC_ID) = NI.`MII_ID` AND NBI.NBI_INSU_DONE_YN = 'N' AND NB.DEL_YN = 'N' AND NB.NB_ID = NBI.NB_ID
					) AS NON_CNT,
					NI.`MII_ENROLL_DT`,
					NI.`MII_DISCOUNT_RATE`,
					IFNULL(NIS.`MII_CHG_DEPT_NM`, '') AS MII_CHG_DEPT_NM,
					IFNULL(NIS.`MII_CHG_LVL_NM`,'') AS MII_CHG_LVL_NM, 
					CAST(AES_DECRYPT(NIS.`MII_CHG_NM_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS MII_CHG_NM_AES,
					IFNULL(CAST(AES_DECRYPT(NIS.`MII_CHG_TEL1_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR), '') AS MII_CHG_TEL1_AES,
					CAST(AES_DECRYPT(NIS.`MII_CHG_TEL2_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS MII_CHG_TEL2_AES,
					IFNULL(CAST(AES_DECRYPT(NIS.`MII_CHG_EMAIL_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR),'') AS MII_CHG_EMAIL_AES,
					IFNULL(NIS.`MII_BOSS_LVL_NM`,'') AS MII_BOSS_LVL_NM,
					CAST(AES_DECRYPT(NIS.`MII_BOSS_NM_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS MII_BOSS_NM_AES,
					CAST(AES_DECRYPT(NIS.`MII_BOSS_TEL_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS MII_BOSS_TEL_AES,	
					IFNULL(NIS.`MII_MEMO`, '') AS MII_MEMO,
					CAST(AES_DECRYPT(NIS.`MII_NEW_ADDRESS1_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS MII_NEW_ADDRESS1_AES,
					CAST(AES_DECRYPT(NIS.`MII_NEW_ADDRESS2_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS MII_NEW_ADDRESS2_AES,
					NIS.`MII_NEW_ZIP`,
					NI.`WRT_DTHMS`,
					NI.`LAST_DTHMS`,
					NI.`WRT_USERID`
				FROM 
					`NT_MAIN`.`NMI_INSU` NI,
					`NT_MAIN`.`NMI_INSU_SUB` NIS
				WHERE NIS.MII_ID = NI.MII_ID 
                AND NIS.DEL_YN = 'N'
                AND NI.DEL_YN = 'N'
					 $tmpKeyword 
				ORDER BY MII_ORDER_NUM
				$LIMIT_SQL 
				;
		";
		// echo $iSQL."\n";
		$Result= DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
				"IDX"=>$IDX--,
				"MII_ID"=>$RS['MII_ID'],
				"MII_ORDER_NUM"=>$RS['MII_ORDER_NUM'],
				"RIT_ID"=>$RS['RIT_ID'],
				"RIT_NM"=>$RS['RIT_NM'],
				"MII_NM"=>$RS['MII_NM'],
				"NON_CNT"=>$RS['NON_CNT'],
				"MII_ENROLL_DT"=>$RS['MII_ENROLL_DT'],
				"MII_DISCOUNT_RATE"=>$RS['MII_DISCOUNT_RATE'],
				"MII_CHG_DEPT_NM"=>$RS['MII_CHG_DEPT_NM'],
				"MII_CHG_LVL_NM"=>$RS['MII_CHG_LVL_NM'],
				"MII_CHG_NM"=>$RS['MII_CHG_NM_AES'],
				// "MII_CHG_TEL1"=>cUtil::Tel_Format($RS['MII_CHG_TEL1_AES'],"-","-"),
				// "MII_CHG_TEL2"=>cUtil::Tel_Format($RS['MII_CHG_TEL2_AES'],"-","-"),
				"MII_CHG_TEL1"=>$RS['MII_CHG_TEL1_AES'],
				"MII_CHG_TEL2"=>$RS['MII_CHG_TEL2_AES'],
				"MII_CHG_EMAIL"=>$RS['MII_CHG_EMAIL_AES'],
				"MII_BOSS_LVL_NM"=>$RS['MII_BOSS_LVL_NM'],
				"MII_BOSS_NM"=>$RS['MII_BOSS_NM_AES'],
				"MII_BOSS_TEL"=>$RS['MII_BOSS_TEL_AES'],
				// "MII_BOSS_TEL"=>cUtil::Tel_Format($RS['MII_BOSS_TEL_AES'],"-","-"),
				"MII_MEMO"=>$RS['MII_MEMO'],
				"MII_NEW_ADDRESS1"=>$RS['MII_NEW_ADDRESS1_AES'],
				"MII_NEW_ADDRESS2"=>$RS['MII_NEW_ADDRESS2_AES'],
				"MII_NEW_ZIP"=>$RS['MII_NEW_ZIP'],
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
	 * 데이터생성
	 */
	function setData($ARR) {
		if($ARR['REQ_MODE'] == CASE_CREATE) {

			/* 아이디 존재여부 등록일 경우에는 전체에서 존재하는지 */
			$iSQL = "SELECT 
						`MII_ID`
					FROM `NT_MAIN`.`NMI_INSU`
					WHERE MII_ID = '".addslashes($ARR['MII_ID'])."' 
					AND DEL_YN = 'N';
			";
			// echo $iSQL;
			$RS= DBManager::getRecordSet($iSQL);
			if(!empty($RS)) {
				return ERROR;
			}
			unset($RS);

			
			if($ARR['IS_EXIST_ORDER_NUM']==''){
				/** 입력요청한 출력순서가 존재하는지 */
				$iSQL = "SELECT 
							`MII_ORDER_NUM`
						FROM `NT_MAIN`.`NMI_INSU`
						WHERE MII_ORDER_NUM = '".addslashes($ARR['MII_ORDER_NUM'])."' 
						AND DEL_YN = 'N'	;
				";

				$RS= DBManager::getRecordSet($iSQL);
				if(!empty($RS)) {
					return CONFLICT;
				}
			}
			unset($RS);

			/** 출력순서 업데이트 */
			$iSQL = "UPDATE  `NT_MAIN`.`NMI_INSU`
					SET 
						`MII_ORDER_NUM` = `MII_ORDER_NUM`+1
					WHERE `MII_ORDER_NUM` IN
						(
							SELECT `MII_ORDER_NUM` FROM 
							(
								SELECT
									@rownum:=@rownum+1 AS IDX,
									`MII_ORDER_NUM`,
									`MII_ORDER_NUM` - @rownum AS GRP
								FROM `NT_MAIN`.`NMI_INSU`, (SELECT @rownum:=0) IDX
								WHERE `DEL_YN` = 'N' 
								AND `MII_ORDER_NUM` >= '".addslashes($ARR['MII_ORDER_NUM'])."' ORDER BY MII_ORDER_NUM ASC 
								) AS CMNT WHERE GRP = '".addslashes($ARR['MII_ORDER_NUM'])."'-1
						)
					;"
			;

			$Result = DBManager::execQuery($iSQL);

			if($Result){
				$iSQL = "INSERT INTO `NT_MAIN`.`NMI_INSU`
								(
								`MII_ID`,
								`MII_ORDER_NUM`,
								`RIT_ID`,
								`MII_NM`,
								`MII_ENROLL_DT`,
								`MII_DISCOUNT_RATE`,
								`WRT_DTHMS`,
								`LAST_DTHMS`,
								`WRT_USERID`,
								`DEL_YN`
								)
								VALUES
								(
								'".addslashes($ARR['MII_ID'])."',
								'".addslashes($ARR['MII_ORDER_NUM'])."',
								'".addslashes($ARR['RIT_ID'])."',
								'".addslashes($ARR['MII_NM'])."',
								'".addslashes($ARR['MII_ENROLL_DT'])."',
								".addslashes($ARR['MII_DISCOUNT_RATE']).",
								NOW(),
								NOW(),
								'".addslashes($_SESSION['USERID'])."',
								'N'
								);
					";
				$iSQL .= "INSERT INTO `NT_MAIN`.`NMI_INSU_SUB`
								(
								`MII_ID`,
								`MII_CHG_DEPT_NM`,
								`MII_CHG_LVL_NM`,
								`MII_CHG_NM_AES`,
								`MII_CHG_TEL1_AES`,
								`MII_CHG_TEL2_AES`,
								`MII_CHG_EMAIL_AES`,
								`MII_BOSS_LVL_NM`,
								`MII_BOSS_NM_AES`,
								`MII_BOSS_TEL_AES`,
								`MII_MEMO`,
								`MII_OLD_ADDRESS1_AES`,
								`MII_OLD_ADDRESS2_AES`,
								`MII_OLD_ZIP`,
								`MII_NEW_ADDRESS1_AES`,
								`MII_NEW_ADDRESS2_AES`,
								`MII_NEW_ZIP`,
								`WRT_DTHMS`,
								`LAST_DTHMS`,
								`WRT_USERID`,
								`DEL_YN`
								)
								VALUES
								(
								'".addslashes($ARR['MII_ID'])."',
								'".addslashes($ARR['MII_CHG_DEPT_NM'])."',
								'".addslashes($ARR['MII_CHG_LVL_NM'])."',
								(AES_ENCRYPT('".addslashes($ARR['MII_CHG_NM'])."',UNHEX(SHA2('".AES_SALT_KEY."',256))) ),
								(AES_ENCRYPT('".addslashes($ARR['MII_CHG_TEL1'])."',UNHEX(SHA2('".AES_SALT_KEY."',256))) ),
								(AES_ENCRYPT('".addslashes($ARR['MII_CHG_TEL2'])."',UNHEX(SHA2('".AES_SALT_KEY."',256))) ),
								(AES_ENCRYPT('".addslashes($ARR['MII_CHG_EMAIL'])."',UNHEX(SHA2('".AES_SALT_KEY."',256))) ),
								'".addslashes($ARR['MII_BOSS_LVL_NM'])."',
								(AES_ENCRYPT('".addslashes($ARR['MII_BOSS_NM'])."',UNHEX(SHA2('".AES_SALT_KEY."',256))) ),
								(AES_ENCRYPT('".addslashes($ARR['MII_BOSS_TEL'])."',UNHEX(SHA2('".AES_SALT_KEY."',256))) ),
								'".addslashes($ARR['MII_MEMO'])."',
								(AES_ENCRYPT('".addslashes($ARR['MII_OLD_ADDRESS1'])."',UNHEX(SHA2('".AES_SALT_KEY."',256))) ),
								(AES_ENCRYPT('".addslashes($ARR['MII_OLD_ADDRESS2'])."',UNHEX(SHA2('".AES_SALT_KEY."',256))) ),
								'".addslashes($ARR['MII_OLD_ZIP'])."',
								(AES_ENCRYPT('".addslashes($ARR['MII_NEW_ADDRESS1'])."',UNHEX(SHA2('".AES_SALT_KEY."',256))) ),
								(AES_ENCRYPT('".addslashes($ARR['MII_NEW_ADDRESS2'])."',UNHEX(SHA2('".AES_SALT_KEY."',256))) ),
								'".addslashes($ARR['MII_NEW_ZIP'])."',									
								NOW(),
								NOW(),
								'".addslashes($_SESSION['USERID'])."',
								'N'
								);
				";
				// echo $iSQL;
				return DBManager::execMulti($iSQL);
			}else{
				return SERVER_ERROR;
			}
			

			

		} else {
			if($ARR['IS_EXIST_ORDER_NUM']==''){
				/** 입력요청한 출력순서가 존재하는지 */
				$iSQL = "SELECT 
							`MII_ORDER_NUM`
						FROM `NT_MAIN`.`NMI_INSU`
						WHERE MII_ORDER_NUM = '".addslashes($ARR['MII_ORDER_NUM'])."' 
						AND DEL_YN = 'N';
				";

				$RS= DBManager::getRecordSet($iSQL);
				if(!empty($RS)) {
					return CONFLICT;
				}
			 }
			
			
			$Result = true;
				
			if($ARR['MII_ORDER_NUM'] != $ARR['ORI_MII_ORDER_NUM'] ){
				$iSQL = "UPDATE  `NT_MAIN`.`NMI_INSU` 
						SET 
							`MII_ORDER_NUM` = `MII_ORDER_NUM`+1
						WHERE `MII_ORDER_NUM` IN
							(
								SELECT `MII_ORDER_NUM` FROM 
								(
									SELECT
										@rownum:=@rownum+1 AS IDX,
										`MII_ORDER_NUM`,
										`MII_ORDER_NUM` - @rownum AS GRP
									FROM `NT_MAIN`.`NMI_INSU`, (SELECT @rownum:=0) IDX
									WHERE `DEL_YN` = 'N' 
									AND `MII_ORDER_NUM` >= '".addslashes($ARR['MII_ORDER_NUM'])."' ORDER BY MII_ORDER_NUM ASC 
									) AS CMNT WHERE GRP = '".addslashes($ARR['MII_ORDER_NUM'])."'-1
							)
						;"
				;
				$Result = DBManager::execQuery($iSQL);
			}

			if($Result){
				$iSQL = "UPDATE  `NT_MAIN`.`NMI_INSU`
							SET
								`MII_ORDER_NUM` = '".addslashes($ARR['MII_ORDER_NUM'])."',
								`RIT_ID` = '".addslashes($ARR['RIT_ID'])."',
								`MII_NM` = '".addslashes($ARR['MII_NM'])."',
								`MII_ENROLL_DT` = '".addslashes($ARR['MII_ENROLL_DT'])."',
								`MII_DISCOUNT_RATE` = ".addslashes($ARR['MII_DISCOUNT_RATE']).",
								`LAST_DTHMS` = NOW(),
								`WRT_USERID` = '".addslashes($_SESSION['USERID'])."'
							WHERE
								`MII_ID` = '".addslashes($ARR['MII_ID'])."' 
							AND `DEL_YN` = 'N';

						";

				$iSQL .= "UPDATE  `NT_MAIN`.`NMI_INSU_SUB`
							SET
								`MII_CHG_DEPT_NM` = '".addslashes($ARR['MII_CHG_DEPT_NM'])."',
								`MII_CHG_LVL_NM` = '".addslashes($ARR['MII_CHG_LVL_NM'])."',
								`MII_CHG_NM_AES` = (AES_ENCRYPT('".addslashes($ARR['MII_CHG_NM'])."',UNHEX(SHA2('".AES_SALT_KEY."',256))) ),
								`MII_CHG_TEL1_AES` = (AES_ENCRYPT('".addslashes($ARR['MII_CHG_TEL1'])."',UNHEX(SHA2('".AES_SALT_KEY."',256))) ),
								`MII_CHG_TEL2_AES` = (AES_ENCRYPT('".addslashes($ARR['MII_CHG_TEL2'])."',UNHEX(SHA2('".AES_SALT_KEY."',256))) ),
								`MII_CHG_EMAIL_AES` = (AES_ENCRYPT('".addslashes($ARR['MII_CHG_EMAIL'])."',UNHEX(SHA2('".AES_SALT_KEY."',256))) ),
								`MII_BOSS_LVL_NM` = '".addslashes($ARR['MII_BOSS_LVL_NM'])."',
								`MII_BOSS_NM_AES` = (AES_ENCRYPT('".addslashes($ARR['MII_BOSS_NM'])."',UNHEX(SHA2('".AES_SALT_KEY."',256))) ),
								`MII_BOSS_TEL_AES` = (AES_ENCRYPT('".addslashes($ARR['MII_BOSS_TEL'])."',UNHEX(SHA2('".AES_SALT_KEY."',256))) ),
								`MII_MEMO` = '".addslashes($ARR['MII_MEMO'])."',
								`MII_OLD_ADDRESS1_AES` = (AES_ENCRYPT('".addslashes($ARR['MII_OLD_ADDRESS1'])."',UNHEX(SHA2('".AES_SALT_KEY."',256))) ),
								`MII_OLD_ADDRESS2_AES` = (AES_ENCRYPT('".addslashes($ARR['MII_OLD_ADDRESS2'])."',UNHEX(SHA2('".AES_SALT_KEY."',256))) ),
								`MII_OLD_ZIP` = '".addslashes($ARR['MII_OLD_ZIP'])."',
								`MII_NEW_ADDRESS1_AES` = (AES_ENCRYPT('".addslashes($ARR['MII_NEW_ADDRESS1'])."',UNHEX(SHA2('".AES_SALT_KEY."',256))) ),
								`MII_NEW_ADDRESS2_AES` = (AES_ENCRYPT('".addslashes($ARR['MII_NEW_ADDRESS2'])."',UNHEX(SHA2('".AES_SALT_KEY."',256))) ),
								`MII_NEW_ZIP` = '".addslashes($ARR['MII_NEW_ZIP'])."',
								`LAST_DTHMS` = NOW(),
								`WRT_USERID` = '".addslashes($_SESSION['USERID'])."'
								
							WHERE
								`MII_ID` = '".addslashes($ARR['MII_ID'])."' 
							AND `DEL_YN` = 'N';

			";		
				return DBManager::execMulti($iSQL);
			}else{
				return SERVER_ERROR;
			}
	
			

		}
	}



	


	/**
	 * data 삭제
	 * @param {$ARR}
	 * @param {ALLM_ID : 삭제할 해당 데이터 ID}
	 */
	function delData($ARR) {
		$iSQL = "UPDATE `NT_MAIN`.`NMI_INSU`
					SET  
						`LAST_DTHMS`= NOW(), 
						`DEL_YN`	='Y'
					WHERE `MII_ID`='".addslashes($ARR['MII_ID'])."';
		";
		$iSQL .= "UPDATE `NT_MAIN`.`NMI_INSU_SUB`
					SET  
						`LAST_DTHMS`= NOW(), 
						`DEL_YN`	='Y'
					WHERE `MII_ID`='".addslashes($ARR['MII_ID'])."' ;
		";
		// echo 	$iSQL ;
		return DBManager::execMulti($iSQL);
	}


	/* 보험사분류 조회  */
	function getInsuType() {
		$iSQL = "SELECT 
					RIT_ID, RIT_NM
				FROM NT_CODE.REF_INSU_TYPE;
		";

		$Result= DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"RIT_ID"=>$RS['RIT_ID'],
					"RIT_NM"=>$RS['RIT_NM']
			));
		}
		$Result->close();  // 자원 반납
		unset($RS);

		if(empty($rtnArray)) {
			return HAVE_NO_DATA;
		} else {
			return json_encode($rtnArray);
		}
	}

	/* 변경이력 조회 */
	function getSubList($ARR) {
		$LimitStart = ($ARR['CURR_PAGE']-1) * $ARR['PER_PAGE'];
		$LimitEnd   = $ARR['PER_PAGE'];

		/** 전체리스트가 아닌경우에만 limitSql 적용 */
		$LIMIT_SQL = "";
		if($ARR['REQ_MODE'] == CASE_LIST_SUB){
			$LIMIT_SQL = "LIMIT $LimitStart,$LimitEnd";
		}

		if($ARR['SEARCH_LOG_CHG_NM'] != ''){
			$tmpKeyword .= "AND INSTR(CAST(AES_DECRYPT(NICL.`NICL_OLD_CHG_NM_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR),'".addslashes($ARR['SEARCH_LOG_CHG_NM'])."')>0 ";
		}
		if($ARR['SEARCH_LOG_CHG_TEL'] != ''){
			$tmpKeyword .= "AND ( INSTR(CAST(AES_DECRYPT(NICL.`NICL_OLD_CHG_TEL1_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR),'".addslashes($ARR['SEARCH_LOG_CHG_TEL'])."')>0 
							OR INSTR(CAST(AES_DECRYPT(NICL.`NICL_OLD_CHG_TEL2_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR),'".addslashes($ARR['SEARCH_LOG_CHG_TEL'])."')>0 ) ";
		}
		if($ARR['SEARCH_LOG_BOSS_NM'] != ''){
			$tmpKeyword .= "AND INSTR(CAST(AES_DECRYPT(NICL.`NICL_OLD_BOSS_NM_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR),'".addslashes($ARR['SEARCH_LOG_BOSS_NM'])."')>0 ";
		}
		if($ARR['SEARCH_LOG_BOSS_TEL'] != ''){
			$tmpKeyword .= "AND ( INSTR(CAST(AES_DECRYPT(NICL.`NICL_OLD_BOSS_TEL_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR),'".addslashes($ARR['SEARCH_LOG_BOSS_TEL'])."')>0  ) ";
		}

		// 페이징처리를 위해 전체데이터
		$iSQL = "SELECT
					COUNT(*) AS CNT
				FROM `NT_MAIN`.`NMI_INSU_CHG_LOG` NICL
				JOIN `NT_MAIN`.`NMI_INSU` NI
					ON NI.MII_ID = NICL.MII_ID AND NI.DEL_YN = 'N'
				JOIN `NT_MAIN`.`NMI_INSU_SUB` NIS
					ON NIS.MII_ID = NICL.MII_ID AND NIS.DEL_YN = 'N'
				WHERE  NI.DEL_YN = 'N'
				AND NICL.`MII_ID`='".addslashes($ARR['MII_ID'])."'
					 $tmpKeyword 
				;
		";
		// echo $iSQL."\n" ;

		try{
			$RS_T	= DBManager::getRecordSet($iSQL);
		} catch (Exception $e){
			echo "error : ".$e;
			return ERROR_DB_SELECT_DATA;   
		}

		$TOTAL_LIST_COUNT = $RS_T['CNT'];
		$IDX = $RS_T['CNT']-$LimitStart;
		unset($RS_T);

		
		// 리스트 가져오기
		$iSQL = "SELECT 
					NICL.`MICL_ID`,
					NICL.`MII_ID`,
					NI.`MII_NM`,
					NI.`RIT_ID`,
					NT_CODE.GET_CODE_NM(NI.RIT_ID) AS RIT_NM,
					CAST(AES_DECRYPT(NIS.`MII_NEW_ADDRESS1_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS MII_NEW_ADDRESS1_AES,
					CAST(AES_DECRYPT(NIS.`MII_NEW_ADDRESS2_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS MII_NEW_ADDRESS2_AES,
					NI.`MII_ENROLL_DT`,
					NICL.`NICL_OLD_CHG_DEPT_NM`,
					NICL.`NICL_OLD_CHG_LVL_NM`,
					CAST(AES_DECRYPT(NICL.`NICL_OLD_CHG_NM_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS NICL_OLD_CHG_NM_AES,
					IFNULL(CAST(AES_DECRYPT(NICL.`NICL_OLD_CHG_TEL1_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR), '') AS NICL_OLD_CHG_TEL1_AES,
					CAST(AES_DECRYPT(NICL.`NICL_OLD_CHG_TEL2_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS NICL_OLD_CHG_TEL2_AES,
					IFNULL(CAST(AES_DECRYPT(NICL.`NICL_OLD_CHG_EMAIL_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR),'') AS NICL_OLD_CHG_EMAIL_AES,
					NICL.`NICL_NEW_CHG_DEPT_NM`,
					NICL.`NICL_NEW_CHG_LVL_NM`,
					CAST(AES_DECRYPT(NICL.`NICL_NEW_CHG_NM_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS NICL_NEW_CHG_NM_AES,
					IFNULL(CAST(AES_DECRYPT(NICL.`NICL_NEW_CHG_TEL1_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR), '') AS NICL_NEW_CHG_TEL1_AES,
					CAST(AES_DECRYPT(NICL.`NICL_NEW_CHG_TEL2_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS NICL_NEW_CHG_TEL2_AES,
					IFNULL(CAST(AES_DECRYPT(NICL.`NICL_NEW_CHG_EMAIL_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR),'') AS NICL_NEW_CHG_EMAIL_AES,
					NICL.`NICL_OLD_BOSS_LVL_NM`,
					CAST(AES_DECRYPT(NICL.`NICL_OLD_BOSS_NM_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS NICL_OLD_BOSS_NM_AES,
					CAST(AES_DECRYPT(NICL.`NICL_OLD_BOSS_TEL_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS NICL_OLD_BOSS_TEL_AES,	
					NICL.`NICL_OLD_BOSS_MEMO`,
					NICL.`NICL_NEW_BOSS_LVL_NM`,
					CAST(AES_DECRYPT(NICL.`NICL_NEW_BOSS_NM_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS NICL_NEW_BOSS_NM_AES,
					CAST(AES_DECRYPT(NICL.`NICL_NEW_BOSS_TEL_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS NICL_NEW_BOSS_TEL_AES,	
					IFNULL(NICL.`NICL_OLD_BOSS_MEMO`, '') AS NICL_OLD_BOSS_MEMO,
					NICL.`WRT_DTHMS`,
					NICL.`LAST_DTHMS`,
					NICL.`WRT_USERID`
				FROM `NT_MAIN`.`NMI_INSU_CHG_LOG` NICL
				JOIN `NT_MAIN`.`NMI_INSU` NI
					ON NI.MII_ID = NICL.MII_ID AND NI.DEL_YN = 'N'
				JOIN `NT_MAIN`.`NMI_INSU_SUB` NIS
					ON NIS.MII_ID = NICL.MII_ID AND NIS.DEL_YN = 'N'
				WHERE  NI.DEL_YN = 'N'
				AND  NICL.`MII_ID`='".addslashes($ARR['MII_ID'])."'
					 $tmpKeyword 
				ORDER BY NICL.MICL_ID DESC 
				$LIMIT_SQL
				;
		";
		// echo $iSQL."\n";
		$Result= DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
				"MICL_ID"=>$RS['MICL_ID'],
				"MII_ID"=>$RS['MII_ID'],
				"MII_NM"=>$RS['MII_NM'],
				"RIT_ID"=>$RS['RIT_ID'],
				"RIT_NM"=>$RS['RIT_NM'],
				"NICL_NEW_ADDRESS1"=>$RS['MII_NEW_ADDRESS1_AES'],
				"NICL_NEW_ADDRESS2"=>$RS['MII_NEW_ADDRESS2_AES'],
				"NICL_ENROLL_DT"=>$RS['MII_ENROLL_DT'],
				"NICL_CHG_DEPT_NM"=>$RS['NICL_OLD_CHG_DEPT_NM'],
				"NICL_CHG_LVL_NM"=>$RS['NICL_OLD_CHG_LVL_NM'],
				"NICL_CHG_NM"=>$RS['NICL_OLD_CHG_NM_AES'],
				"NICL_CHG_TEL1"=>$RS['NICL_OLD_CHG_TEL1_AES'],
				"NICL_CHG_TEL2"=>$RS['NICL_OLD_CHG_TEL2_AES'],
				"NICL_CHG_EMAIL"=>$RS['NICL_OLD_CHG_EMAIL_AES'],
				"NICL_BOSS_LVL_NM"=>$RS['NICL_OLD_BOSS_LVL_NM'],
				"NICL_BOSS_NM"=>$RS['NICL_OLD_BOSS_NM_AES'],
				"NICL_BOSS_TEL"=>$RS['NICL_OLD_BOSS_TEL_AES'],
				"NICL_MEMO"=>$RS['NICL_OLD_BOSS_MEMO'],
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

	/* 보험사 팀 리스트 조회 */
	function getTeamList($ARR) {
		$LimitStart = ($ARR['CURR_PAGE']-1) * $ARR['PER_PAGE'];
		$LimitEnd   = $ARR['PER_PAGE'];
		// echo var_dump($ARR);
		
		$LIMIT_SQL = "LIMIT $LimitStart,$LimitEnd";

		if($ARR['SEARCH_INSU_TEAM'] != ''){
			$tmpKeyword .= "AND NIT.MIT_ID IN (";
			foreach ($ARR['SEARCH_INSU_TEAM'] as $index => $MIT_ARRAY) {
				$tmpKeyword .=" '".$MIT_ARRAY."',";
			}
			$tmpKeyword=substr($tmpKeyword, 0, -1); // 마지막 콤마제거
			$tmpKeyword .= ") ";		
		}
		if($ARR['SEARCH_REGION_MEMO'] != ''){
			$tmpKeyword .= "AND INSTR(NIT.`MIT_REGION_MEMO`,'".addslashes($ARR['SEARCH_REGION_MEMO'])."')>0  ";
		}

		// 페이징처리를 위해 전체데이터
		$iSQL = "SELECT
					COUNT(*) AS CNT
				FROM 
					`NT_MAIN`.`NMI_INSU_TEAM` NIT
				WHERE
					 NIT.DEL_YN='N'
				AND  NIT.MII_ID ='".addslashes($ARR['MII_ID'])."'
				$tmpKeyword 
				;
		";
			// echo $iSQL."\n" ;

		try{
			$RS_T	= DBManager::getRecordSet($iSQL);
		} catch (Exception $e){
			echo "error : ".$e;
			return ERROR_DB_SELECT_DATA;   
		}

		$TOTAL_LIST_COUNT = $RS_T['CNT'];
		$IDX = $RS_T['CNT']-$LimitStart;
		unset($RS_T);

		
		// 리스트 가져오기
		$iSQL = "SELECT 
					NIT.`MIT_ID`,
					NIT.`MII_ID`,
					NIT.`MIT_NM`,
					NIT.`MIT_REGION_MEMO`,
					IFNULL(CAST(AES_DECRYPT(NIT.`MIT_TEAM_TEL_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR), '') AS MIT_TEAM_TEL_AES,
					CAST(AES_DECRYPT(NIT.`MIT_BOSS_NM_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS MIT_BOSS_NM_AES,
					(
						SELECT 
							COUNT(*) 
						FROM `NT_MAIN`.`NMI_INSU_TEAM_REGION`  NITR
						WHERE NITR.MIT_ID = NIT.MIT_ID
					) AS REGION_CNT
				FROM `NT_MAIN`.`NMI_INSU_TEAM` NIT
				WHERE NIT.DEL_YN='N'
				AND  NIT.MII_ID ='".addslashes($ARR['MII_ID'])."'
				$tmpKeyword 
				$LIMIT_SQL
				;
		";
		// echo "getInsuTeam\n".$iSQL."\n";
		$Result= DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
				"MIT_ID"=>$RS['MIT_ID'],
				"MII_ID"=>$RS['MII_ID'],
				"MIT_NM"=>$RS['MIT_NM'],
				"MIT_REGION_MEMO"=>$RS['MIT_REGION_MEMO'],
				"MIT_TEAM_TEL_AES"=>$RS['MIT_TEAM_TEL_AES'],
				"MIT_BOSS_NM_AES"=>$RS['MIT_BOSS_NM_AES'],
				"REGION_CNT"=>$RS['REGION_CNT']
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


	/* 보험사Team 조회  */
	function getInsuTeam($ARR) {
		$iSQL = "SELECT 
					MIT_ID, MIT_NM
				FROM NT_MAIN.NMI_INSU_TEAM
				WHERE DEL_YN='N'
				AND  MII_ID ='".addslashes($ARR['MII_ID'])."'
		";
		// echo "getInsuTeam\n".$iSQL."\n";
		$Result= DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"MIT_ID"=>$RS['MIT_ID'],
					"MIT_NM"=>$RS['MIT_NM']
			));
		}
		$Result->close();  // 자원 반납
		unset($RS);

		if(empty($rtnArray)) {
			return HAVE_NO_DATA;
		} else {
			return json_encode($rtnArray);
		}
	}




	/* 보험사 팀 담당지역 등록 및 수정요청 수락 */
	function setRegion($ARR) {
		if( !empty($ARR['ARR_REGION']) || $ARR['ARR_REGION'] != null){
			$iSQL = "DELETE FROM `NT_MAIN`.`NMI_INSU_TEAM_REGION`
							WHERE MIT_ID = '".addslashes($ARR['MIT_ID'])."' ;
				";
			foreach ($ARR['ARR_REGION'] as $index => $MII_ARRAY) {
				$crrId = explode("-",$MII_ARRAY); // 지역구분별 아이디 분리
				
				$iSQL .= "INSERT  INTO `NT_MAIN`.`NMI_INSU_TEAM_REGION`
								(
								`MIT_ID`,
								`CRR_1_ID`,
								`CRR_2_ID`,
								`CRR_3_ID`,
								`WRT_DTHMS`,
								`LAST_DTHMS`,
								`WRT_USERID`
								)
								VALUES
								(
									'".addslashes($ARR['MIT_ID'])."',
									'".$crrId[0]."',
									'".$crrId[1]."',
									'".$crrId[2]."',
								NOW(),
								NOW(),
								'".addslashes($_SESSION['USERID'])."' 
								);
				";
			}

		}else {
			$iSQL = "DELETE FROM `NT_MAIN`.`NMI_INSU_TEAM_REGION`
							WHERE MIT_ID = '".addslashes($ARR['MIT_ID'])."' ;
			";
		}
		// echo "setRegion\n".$iSQL."\n";
		return DBManager::execMulti($iSQL);
	}
	
	/* 보험사 팀 지역 정보 조회 */
	// function getTeamRegion($ARR){
	// 	$iSQL = "SELECT 
	// 				VGR.CRR_1_ID,
	// 				VGR.CRR_2_ID,
	// 				VGR.CRR_3_ID,
	// 				VGR.CRR_NM,
	// 				(SELECT 
	// 					NITR.MIT_ID
	// 				FROM `NT_MAIN`.`NMI_INSU_TEAM_REGION` NITR
	// 				WHERE VGR.CRR_1_ID = NITR.CRR_1_ID AND VGR.CRR_2_ID = NITR.CRR_2_ID AND VGR.CRR_3_ID = NITR.CRR_3_ID
	// 				AND MIT_ID = '".addslashes($ARR['MIT_ID'])."'
	// 				)AS MIT_ID
	// 			FROM NT_MAIN.V_GW_REGION VGR;
	// 	";
	// 	echo "getTeamRegion\n".$iSQL."\n";
	// 	$Result= DBManager::getResult($iSQL);

	// 	$rtnArray = array();
	// 	while($RS = $Result->fetch_array()){
	// 		array_push($rtnArray,
	// 			array(
	// 				"CRR_1_ID"=>$RS['CRR_1_ID'],
	// 				"CRR_2_ID"=>$RS['CRR_2_ID'],
	// 				"CRR_3_ID"=>$RS['CRR_3_ID'],
	// 				"CRR_NM"=>$RS['CRR_NM'],
	// 				"MIT_ID"=>$RS['MIT_ID']
	// 		));
	// 	}
	// 	$Result->close();  // 자원 반납
	// 	unset($RS);

	// 	if(empty($rtnArray)) {
	// 		return HAVE_NO_DATA;
	// 	} else {
	// 		return json_encode($rtnArray);
	// 	}
	// }


	/* 보험사 팀 지역 정보 조회 */
	function getTeamRegion($ARR){
		$iSQL = "SELECT 
					TR.NITR_MIT_ID,
					VGR.CRR_NM,
					IFNULL(TR.CRR_1_ID, VGR.CRR_1_ID) AS CRR_1_ID,
					IFNULL(TR.CRR_2_ID, VGR.CRR_2_ID) AS CRR_2_ID,
					IFNULL(TR.CRR_3_ID, VGR.CRR_3_ID) AS CRR_3_ID,
					TR.NITRU_DEL_YN,
					TR.NITR_CRR_1_ID,
					TR.NITR_CRR_2_ID,
					TR.NITR_CRR_3_ID,
					TR.NITR_WRT_DTHMS,
					TR.NITR_LAST_DTHMS,
					TR.NITR_WRT_USERID,
					TR.NITRU_ID,
					TR.NITRU_MIT_ID,
					TR.NITRU_CRR_1_ID,
					TR.NITRU_CRR_2_ID,
					TR.NITRU_CRR_3_ID,
					TR.NITRU_REQUEST_MODE,
					TR.NITRU_REQUEST_DTHMS,
					TR.NITRU_REQUEST_USERID,
					TR.NITRU_APPLY_YN,
					TR.NITRU_APPY_DTHMS,
					TR.NITRU_APPLY_USERID,
					TR.NITRU_WRT_DTHMS,
					TR.NITRU_LAST_DTHMS,
					TR.NITRU_WRT_USERID
				FROM
					(
						SELECT 
							NITR.`MIT_ID` AS `NITR_MIT_ID`,
							IFNULL(NITR.`CRR_1_ID`, NITRU.`CRR_1_ID`) AS `CRR_1_ID`,
							IFNULL(NITR.`CRR_2_ID`, NITRU.`CRR_2_ID`) AS `CRR_2_ID`,
							IFNULL(NITR.`CRR_3_ID`, NITRU.`CRR_3_ID`) AS `CRR_3_ID`,
							NITR.`CRR_1_ID` AS `NITR_CRR_1_ID`,
							NITR.`CRR_2_ID` AS `NITR_CRR_2_ID`,
							NITR.`CRR_3_ID` AS `NITR_CRR_3_ID`,
							NITR.`WRT_DTHMS` AS `NITR_WRT_DTHMS`,
							NITR.`LAST_DTHMS` AS `NITR_LAST_DTHMS`,
							NITR.`WRT_USERID` AS `NITR_WRT_USERID`,
							NITRU.`NITRU_ID` AS `NITRU_ID`,
							NITRU.`MIT_ID` AS `NITRU_MIT_ID`,
							NITRU.`CRR_1_ID` AS `NITRU_CRR_1_ID`,
							NITRU.`CRR_2_ID` AS `NITRU_CRR_2_ID`,
							NITRU.`CRR_3_ID` AS `NITRU_CRR_3_ID`,
							NITRU.`NITRU_DEL_YN` AS `NITRU_DEL_YN`,
							NITRU.`NITRU_REQUEST_MODE` AS `NITRU_REQUEST_MODE`,
							NITRU.`NITRU_REQUEST_DTHMS` AS `NITRU_REQUEST_DTHMS`,
							NITRU.`NITRU_REQUEST_USERID` AS `NITRU_REQUEST_USERID`,
							NITRU.`NITRU_APPLY_YN` AS `NITRU_APPLY_YN`,
							NITRU.`NITRU_APPY_DTHMS` AS `NITRU_APPY_DTHMS`,
							NITRU.`NITRU_APPLY_USERID` AS `NITRU_APPLY_USERID`,
							NITRU.`WRT_DTHMS` AS `NITRU_WRT_DTHMS`,
							NITRU.`LAST_DTHMS` AS `NITRU_LAST_DTHMS`,
							NITRU.`WRT_USERID` AS `NITRU_WRT_USERID`
						FROM NT_MAIN.NMI_INSU_TEAM_REGION NITR  
						LEFT JOIN NT_MAIN.NMI_INSU_TEAM_REGION_UPDATE NITRU
							ON NITRU.MIT_ID = NITR.MIT_ID AND NITRU.NITRU_APPLY_YN ='N'
							AND NITRU.CRR_1_ID = NITR.CRR_1_ID AND NITRU.CRR_2_ID = NITR.CRR_2_ID AND NITRU.CRR_3_ID = NITR.CRR_3_ID
						WHERE NITR.MIT_ID='".addslashes($ARR['MIT_ID'])."' 
						UNION
						SELECT 
							NITR.`MIT_ID` AS `NITR_MIT_ID`,
							IFNULL(NITR.`CRR_1_ID`, NITRU.`CRR_1_ID`) AS `CRR_1_ID`,
							IFNULL(NITR.`CRR_2_ID`, NITRU.`CRR_2_ID`) AS `CRR_2_ID`,
							IFNULL(NITR.`CRR_3_ID`, NITRU.`CRR_3_ID`) AS `CRR_3_ID`,
							NITR.`CRR_1_ID` AS `NITR_CRR_1_ID`,
							NITR.`CRR_2_ID` AS `NITR_CRR_2_ID`,
							NITR.`CRR_3_ID` AS `NITR_CRR_3_ID`,
							NITR.`WRT_DTHMS` AS `NITR_WRT_DTHMS`,
							NITR.`LAST_DTHMS` AS `NITR_LAST_DTHMS`,
							NITR.`WRT_USERID` AS `NITR_WRT_USERID`,
							NITRU.`NITRU_ID` AS `NITRU_ID`,
							NITRU.`MIT_ID` AS `NITRU_MIT_ID`,
							NITRU.`CRR_1_ID` AS `NITRU_CRR_1_ID`,
							NITRU.`CRR_2_ID` AS `NITRU_CRR_2_ID`,
							NITRU.`CRR_3_ID` AS `NITRU_CRR_3_ID`,
							NITRU.`NITRU_DEL_YN` AS `NITRU_DEL_YN`,
							NITRU.`NITRU_REQUEST_MODE` AS `NITRU_REQUEST_MODE`,
							NITRU.`NITRU_REQUEST_DTHMS` AS `NITRU_REQUEST_DTHMS`,
							NITRU.`NITRU_REQUEST_USERID` AS `NITRU_REQUEST_USERID`,
							NITRU.`NITRU_APPLY_YN` AS `NITRU_APPLY_YN`,
							NITRU.`NITRU_APPY_DTHMS` AS `NITRU_APPY_DTHMS`,
							NITRU.`NITRU_APPLY_USERID` AS `NITRU_APPLY_USERID`,
							NITRU.`WRT_DTHMS` AS `NITRU_WRT_DTHMS`,
							NITRU.`LAST_DTHMS` AS `NITRU_LAST_DTHMS`,
							NITRU.`WRT_USERID` AS `NITRU_WRT_USERID`
						FROM NT_MAIN.NMI_INSU_TEAM_REGION_UPDATE NITRU
						LEFT JOIN NT_MAIN.NMI_INSU_TEAM_REGION NITR  
							ON NITRU.MIT_ID = NITR.MIT_ID  
							AND NITRU.CRR_1_ID = NITR.CRR_1_ID AND NITRU.CRR_2_ID = NITR.CRR_2_ID AND NITRU.CRR_3_ID = NITR.CRR_3_ID
						WHERE NITRU.NITRU_APPLY_YN ='N' AND NITRU.MIT_ID='".addslashes($ARR['MIT_ID'])."' 
					) TR
				JOIN `NT_MAIN`.`V_GW_REGION` VGR
				ON TR.CRR_1_ID = VGR.CRR_1_ID AND  TR.CRR_2_ID = VGR.CRR_2_ID AND  TR.CRR_3_ID = VGR.CRR_3_ID  
				WHERE VGR.CRR_2_ID <> '000' AND VGR.CRR_3_ID <>'00000'
				ORDER BY NITRU_ID DESC, TR.NITR_MIT_ID DESC, CRR_1_ID, CRR_2_ID, CRR_3_ID
		;

		";
		// echo "getTeamRegion".$iSQL."\n";
		$Result= DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"NITR_MIT_ID"=>$RS['NITR_MIT_ID'],
					"CRR_NM"=>$RS['CRR_NM'],
					"CRR_1_ID"=>$RS['CRR_1_ID'],
					"CRR_2_ID"=>$RS['CRR_2_ID'],
					"CRR_3_ID"=>$RS['CRR_3_ID'],
					"NITRU_DEL_YN"=>$RS['NITRU_DEL_YN'],
					"NITR_CRR_1_ID"=>$RS['NITR_CRR_1_ID'],
					"NITR_CRR_2_ID"=>$RS['NITR_CRR_2_ID'],
					"NITR_CRR_3_ID"=>$RS['NITR_CRR_3_ID'],
					"NITR_WRT_DTHMS"=>$RS['NITR_WRT_DTHMS'],
					"NITR_LAST_DTHMS"=>$RS['NITR_LAST_DTHMS'],
					"NITR_WRT_USERID"=>$RS['NITR_WRT_USERID'],
					"NITRU_ID"=>$RS['NITRU_ID'],
					"NITRU_MIT_ID"=>$RS['NITRU_MIT_ID'],
					"NITRU_CRR_1_ID"=>$RS['NITRU_CRR_1_ID'],
					"NITRU_CRR_2_ID"=>$RS['NITRU_CRR_2_ID'],
					"NITRU_CRR_3_ID"=>$RS['NITRU_CRR_3_ID'],
					"NITRU_REQUEST_MODE"=>$RS['NITRU_REQUEST_MODE'],
					"NITRU_REQUEST_DTHMS"=>$RS['NITRU_REQUEST_DTHMS'],
					"NITRU_REQUEST_USERID"=>$RS['NITRU_REQUEST_USERID'],
					"NITRU_APPLY_YN"=>$RS['NITRU_APPLY_YN'],
					"NITRU_APPY_DTHMS"=>$RS['NITRU_APPY_DTHMS'],
					"NITRU_APPLY_USERID"=>$RS['NITRU_APPLY_USERID'],
					"NITRU_WRT_DTHMS"=>$RS['NITRU_WRT_DTHMS'],
					"NITRU_LAST_DTHMS"=>$RS['NITRU_LAST_DTHMS'],
					"NITRU_WRT_USERID"=>$RS['NITRU_WRT_USERID']
			));
		}
		$Result->close();  // 자원 반납
		unset($RS);

		if(empty($rtnArray)) {
			return HAVE_NO_DATA;
		} else {
			return json_encode($rtnArray);
		}
	}



	/* 보험사 지역 정보 조회 */
	function getRegion($ARR){
		if($ARR['SEARCH_AREA_KEYWORD'] != ''){
			$tmpKeyword = "AND INSTR(VGR.CRR_NM, '".addslashes($ARR['SEARCH_AREA_KEYWORD'])."') > 0 \n";
		}
		$iSQL = "SELECT 
					TR.NITR_MIT_ID,
					VGR.CRR_NM,
					IFNULL(TR.CRR_1_ID, VGR.CRR_1_ID) AS CRR_1_ID,
					IFNULL(TR.CRR_2_ID, VGR.CRR_2_ID) AS CRR_2_ID,
					IFNULL(TR.CRR_3_ID, VGR.CRR_3_ID) AS CRR_3_ID,
					TR.NITRU_DEL_YN,
					TR.NITR_CRR_1_ID,
					TR.NITR_CRR_2_ID,
					TR.NITR_CRR_3_ID,
					TR.NITR_WRT_DTHMS,
					TR.NITR_LAST_DTHMS,
					TR.NITR_WRT_USERID,
					TR.NITRU_ID,
					TR.NITRU_MIT_ID,
					TR.NITRU_CRR_1_ID,
					TR.NITRU_CRR_2_ID,
					TR.NITRU_CRR_3_ID,
					TR.NITRU_REQUEST_MODE,
					TR.NITRU_REQUEST_DTHMS,
					TR.NITRU_REQUEST_USERID,
					TR.NITRU_APPLY_YN,
					TR.NITRU_APPY_DTHMS,
					TR.NITRU_APPLY_USERID,
					TR.NITRU_WRT_DTHMS,
					TR.NITRU_LAST_DTHMS,
					TR.NITRU_WRT_USERID
				FROM
					(
						SELECT 
							NITR.`MIT_ID` AS `NITR_MIT_ID`,
							IFNULL(NITR.`CRR_1_ID`, NITRU.`CRR_1_ID`) AS `CRR_1_ID`,
							IFNULL(NITR.`CRR_2_ID`, NITRU.`CRR_2_ID`) AS `CRR_2_ID`,
							IFNULL(NITR.`CRR_3_ID`, NITRU.`CRR_3_ID`) AS `CRR_3_ID`,
							NITR.`CRR_1_ID` AS `NITR_CRR_1_ID`,
							NITR.`CRR_2_ID` AS `NITR_CRR_2_ID`,
							NITR.`CRR_3_ID` AS `NITR_CRR_3_ID`,
							NITR.`WRT_DTHMS` AS `NITR_WRT_DTHMS`,
							NITR.`LAST_DTHMS` AS `NITR_LAST_DTHMS`,
							NITR.`WRT_USERID` AS `NITR_WRT_USERID`,
							NITRU.`NITRU_ID` AS `NITRU_ID`,
							NITRU.`MIT_ID` AS `NITRU_MIT_ID`,
							NITRU.`CRR_1_ID` AS `NITRU_CRR_1_ID`,
							NITRU.`CRR_2_ID` AS `NITRU_CRR_2_ID`,
							NITRU.`CRR_3_ID` AS `NITRU_CRR_3_ID`,
							NITRU.`NITRU_DEL_YN` AS `NITRU_DEL_YN`,
							NITRU.`NITRU_REQUEST_MODE` AS `NITRU_REQUEST_MODE`,
							NITRU.`NITRU_REQUEST_DTHMS` AS `NITRU_REQUEST_DTHMS`,
							NITRU.`NITRU_REQUEST_USERID` AS `NITRU_REQUEST_USERID`,
							NITRU.`NITRU_APPLY_YN` AS `NITRU_APPLY_YN`,
							NITRU.`NITRU_APPY_DTHMS` AS `NITRU_APPY_DTHMS`,
							NITRU.`NITRU_APPLY_USERID` AS `NITRU_APPLY_USERID`,
							NITRU.`WRT_DTHMS` AS `NITRU_WRT_DTHMS`,
							NITRU.`LAST_DTHMS` AS `NITRU_LAST_DTHMS`,
							NITRU.`WRT_USERID` AS `NITRU_WRT_USERID`
						FROM NT_MAIN.NMI_INSU_TEAM_REGION NITR  
						LEFT JOIN NT_MAIN.NMI_INSU_TEAM_REGION_UPDATE NITRU
							ON NITRU.MIT_ID = NITR.MIT_ID AND NITRU.NITRU_APPLY_YN ='N'
							AND NITRU.CRR_1_ID = NITR.CRR_1_ID AND NITRU.CRR_2_ID = NITR.CRR_2_ID AND NITRU.CRR_3_ID = NITR.CRR_3_ID
						WHERE NT_MAIN.GET_INSU_ID_BY_MIT(NITR.`MIT_ID`) ='".addslashes($ARR['MII_ID'])."' 
						UNION
						SELECT 
							NITR.`MIT_ID` AS `NITR_MIT_ID`,
							IFNULL(NITR.`CRR_1_ID`, NITRU.`CRR_1_ID`) AS `CRR_1_ID`,
							IFNULL(NITR.`CRR_2_ID`, NITRU.`CRR_2_ID`) AS `CRR_2_ID`,
							IFNULL(NITR.`CRR_3_ID`, NITRU.`CRR_3_ID`) AS `CRR_3_ID`,
							NITR.`CRR_1_ID` AS `NITR_CRR_1_ID`,
							NITR.`CRR_2_ID` AS `NITR_CRR_2_ID`,
							NITR.`CRR_3_ID` AS `NITR_CRR_3_ID`,
							NITR.`WRT_DTHMS` AS `NITR_WRT_DTHMS`,
							NITR.`LAST_DTHMS` AS `NITR_LAST_DTHMS`,
							NITR.`WRT_USERID` AS `NITR_WRT_USERID`,
							NITRU.`NITRU_ID` AS `NITRU_ID`,
							NITRU.`MIT_ID` AS `NITRU_MIT_ID`,
							NITRU.`CRR_1_ID` AS `NITRU_CRR_1_ID`,
							NITRU.`CRR_2_ID` AS `NITRU_CRR_2_ID`,
							NITRU.`CRR_3_ID` AS `NITRU_CRR_3_ID`,
							NITRU.`NITRU_DEL_YN` AS `NITRU_DEL_YN`,
							NITRU.`NITRU_REQUEST_MODE` AS `NITRU_REQUEST_MODE`,
							NITRU.`NITRU_REQUEST_DTHMS` AS `NITRU_REQUEST_DTHMS`,
							NITRU.`NITRU_REQUEST_USERID` AS `NITRU_REQUEST_USERID`,
							NITRU.`NITRU_APPLY_YN` AS `NITRU_APPLY_YN`,
							NITRU.`NITRU_APPY_DTHMS` AS `NITRU_APPY_DTHMS`,
							NITRU.`NITRU_APPLY_USERID` AS `NITRU_APPLY_USERID`,
							NITRU.`WRT_DTHMS` AS `NITRU_WRT_DTHMS`,
							NITRU.`LAST_DTHMS` AS `NITRU_LAST_DTHMS`,
							NITRU.`WRT_USERID` AS `NITRU_WRT_USERID`
						FROM NT_MAIN.NMI_INSU_TEAM_REGION_UPDATE NITRU
						LEFT JOIN NT_MAIN.NMI_INSU_TEAM_REGION NITR  
							ON NITRU.MIT_ID = NITR.MIT_ID  
							AND NITRU.CRR_1_ID = NITR.CRR_1_ID AND NITRU.CRR_2_ID = NITR.CRR_2_ID AND NITRU.CRR_3_ID = NITR.CRR_3_ID
						WHERE NITRU.NITRU_APPLY_YN ='N' 
						AND NT_MAIN.GET_INSU_ID_BY_MIT(NITR.`MIT_ID`) ='".addslashes($ARR['MII_ID'])."' 
					) TR
				RIGHT OUTER JOIN `NT_MAIN`.`V_GW_REGION` VGR
				ON TR.CRR_1_ID = VGR.CRR_1_ID AND  TR.CRR_2_ID = VGR.CRR_2_ID AND  TR.CRR_3_ID = VGR.CRR_3_ID  
				WHERE VGR.CRR_2_ID <> '000' AND VGR.CRR_3_ID <>'00000'
				AND NT_MAIN.GET_INSU_ID_BY_MIT(TR.NITR_MIT_ID)  IS NULL
				AND TR.NITR_MIT_ID IS NULL
				$tmpKeyword 
				ORDER BY NITRU_ID DESC, TR.NITR_MIT_ID DESC, CRR_1_ID, CRR_2_ID, CRR_3_ID
		;

		";
		// echo "getRegion".$iSQL."\n";
		$Result= DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"NITR_MIT_ID"=>$RS['NITR_MIT_ID'],
					"CRR_NM"=>$RS['CRR_NM'],
					"CRR_1_ID"=>$RS['CRR_1_ID'],
					"CRR_2_ID"=>$RS['CRR_2_ID'],
					"CRR_3_ID"=>$RS['CRR_3_ID'],
					"NITRU_DEL_YN"=>$RS['NITRU_DEL_YN'],
					"NITR_CRR_1_ID"=>$RS['NITR_CRR_1_ID'],
					"NITR_CRR_2_ID"=>$RS['NITR_CRR_2_ID'],
					"NITR_CRR_3_ID"=>$RS['NITR_CRR_3_ID'],
					"NITR_WRT_DTHMS"=>$RS['NITR_WRT_DTHMS'],
					"NITR_LAST_DTHMS"=>$RS['NITR_LAST_DTHMS'],
					"NITR_WRT_USERID"=>$RS['NITR_WRT_USERID'],
					"NITRU_ID"=>$RS['NITRU_ID'],
					"NITRU_MIT_ID"=>$RS['NITRU_MIT_ID'],
					"NITRU_CRR_1_ID"=>$RS['NITRU_CRR_1_ID'],
					"NITRU_CRR_2_ID"=>$RS['NITRU_CRR_2_ID'],
					"NITRU_CRR_3_ID"=>$RS['NITRU_CRR_3_ID'],
					"NITRU_REQUEST_MODE"=>$RS['NITRU_REQUEST_MODE'],
					"NITRU_REQUEST_DTHMS"=>$RS['NITRU_REQUEST_DTHMS'],
					"NITRU_REQUEST_USERID"=>$RS['NITRU_REQUEST_USERID'],
					"NITRU_APPLY_YN"=>$RS['NITRU_APPLY_YN'],
					"NITRU_APPY_DTHMS"=>$RS['NITRU_APPY_DTHMS'],
					"NITRU_APPLY_USERID"=>$RS['NITRU_APPLY_USERID'],
					"NITRU_WRT_DTHMS"=>$RS['NITRU_WRT_DTHMS'],
					"NITRU_LAST_DTHMS"=>$RS['NITRU_LAST_DTHMS'],
					"NITRU_WRT_USERID"=>$RS['NITRU_WRT_USERID']
			));
		}
		$Result->close();  // 자원 반납
		unset($RS);

		if(empty($rtnArray)) {
			return HAVE_NO_DATA;
		} else {
			return json_encode($rtnArray);
		}
	}


	function setTrans($ARR) {

		$iSQL = "UPDATE  `NT_BILL`.`NBB_BILL_INSU`
					SET
						`MII_ID` = '".addslashes($ARR['TRANSFER_INSU_NM'])."',
						`LAST_DTHMS` = NOW(),
						`WRT_USERID` = '".addslashes($_SESSION['USERID'])."'
					WHERE
						`MII_ID` = '".addslashes($ARR['PRESENT_INSU_ID'])."' 
					AND `NBI_INSU_DONE_YN` = 'N';
		";		

		
		// echo $iSQL."\n";
		return DBManager::execQuery($iSQL);
	}


}
	// 0. 객체 생성
	$cIwmngt = new cIwmngt(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$cIwmngt->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
