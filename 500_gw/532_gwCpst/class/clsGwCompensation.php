<?php

class clsGwCompensation extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	// Class Object Constructor
	function clsGwCompensation($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
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
		if($ARR['SELECT_DT'] != '') {
			if($ARR['START_DT'] !='' && $ARR['END_DT'] !=''){
				$tmpKeyword .= "AND DATE(`NGIC`.".$ARR['SELECT_DT'].") BETWEEN '".addslashes($ARR['START_DT'])."' AND '".addslashes($ARR['END_DT'])."'";
			}else if($ARR['START_DT'] !='' && $ARR['END_DT'] ==''){
				$tmpKeyword .= "AND DATE(`NGIC`.".$ARR['SELECT_DT'].") >= '".addslashes($ARR['START_DT'])."'";
			}else{
				$tmpKeyword .= "AND DATE(`NGIC`.".$ARR['SELECT_DT'].") <= '".addslashes($ARR['END_DT'])."'";
			}
		} 

		//구분검색
		if($ARR['CSD_ID_SEARCH'] != '') {
			$tmpKeyword .= "AND (";
			foreach ($ARR['CSD_ID_SEARCH'] as $index => $CSD_ID) {
				if($index == 0){
					$tmpKeyword .= "(";
					$tmpKeyword .= "INSTR(`MGG`.`CSD_ID`,'".$CSD_ID."')>0 ";
				}else{
					$tmpKeyword .= " OR(";
					$tmpKeyword .= "  INSTR(`MGG`.`CSD_ID`,'".$CSD_ID."')>0 ";
				}
				$tmpKeyword .= ")";
			}
			$tmpKeyword .= ")";
		} 

		//지역주소검색
		if($ARR['CRR_ID_SEARCH'] != '') {
			$tmpKeyword .= "AND (";
			foreach ($ARR['CRR_ID_SEARCH'] as $index => $CRD_ARRAY) {
				$CSD = explode(',', $CRD_ARRAY);
				
				if($index == 0){
					$tmpKeyword .= "(";
					$tmpKeyword .= "INSTR(`MGG`.`CRR_1_ID`,'".$CSD[0]."')>0 ";
				}else{
					$tmpKeyword .= " OR(";
					$tmpKeyword .= "  INSTR(`MGG`.`CRR_1_ID`,'".$CSD[0]."')>0 ";
				}
				$tmpKeyword .= "AND INSTR(`MGG`.`CRR_2_ID`,'".$CSD[1]."')>0 ";
				if($CSD[2] != NOT_CRR_3){
					$tmpKeyword .= "AND INSTR(`MGG`.`CRR_3_ID`,'".$CSD[2]."')>0 ";
				}
				
				$tmpKeyword .= ")";
			}
			$tmpKeyword .= ")";
		} 

		//공업사 검색
		if($ARR['MGG_ID_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(MGG.MGG_NM ,'".addslashes($ARR['MGG_ID_SEARCH'])."')>0 ";	
		}

		//청구담당 검색
		if($ARR['MGG_CLAIM_NM_AES_SEARCH'] != '') {
			$AES_COL = "CAST(AES_DECRYPT(MGGS.MGG_CLAIM_NM_AES,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR)";
			$tmpKeyword .= "AND INSTR(".$AES_COL.",'".addslashes($ARR['MGG_CLAIM_NM_AES_SEARCH'])."')>0 ";
		}

		//보험사 검색
		if($ARR['MII_ID_SEARCH'] != '') {
			$tmpKeyword .= "AND NT_MAIN.GET_INSU_CHG_MII_ID(NIC.NIC_ID)  = '".addslashes($ARR['MII_ID_SEARCH'])."' ";	
		}

		//보상담당자 검색
		if($ARR['NIC_SEARCH'] !=''){
			if($ARR['NIC_SEARCH'] == 'NM'){
				$AES_DECRYPT_DATA = "CAST(AES_DECRYPT(`NIC`.`NIC_NM_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR)";
				$tmpKeyword .= "AND INSTR(".$AES_DECRYPT_DATA.",'".addslashes($ARR['NIC_SEARCH_TEXT'])."')>0 ";
			}else{
				$AES_DECRYPT_FAX_DATA = "CAST(AES_DECRYPT(`NIC`.`NIC_FAX_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR)";
				$AES_DECRYPT_TEL1_DATA = "CAST(AES_DECRYPT(`NIC`.`NIC_TEL1_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR)";
				$tmpKeyword .= "AND (INSTR(".$AES_DECRYPT_FAX_DATA.",'".addslashes($ARR['NIC_SEARCH_TEXT'])."')>0 ";
				$tmpKeyword .= "OR INSTR(".$AES_DECRYPT_TEL1_DATA.",'".addslashes($ARR['NIC_SEARCH_TEXT'])."')>0)";

			}
			$AES_DECRYPT_DATA = "CAST(AES_DECRYPT(`NIC`.`".addslashes($ARR['NIC_AES_SEARCH'])."`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR)";
			
		}

		//담닫구분검색
		if($ARR['RICT_ID_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(NGIC.RICT_ID ,'".addslashes($ARR['RICT_ID_SEARCH'])."')>0 ";	
		}


		if($ARR['RMT_ID_SEARCH'] != '') {
			$tmpKeyword .= " AND  '".addslashes($ARR['RMT_ID_SEARCH'])."' IN (SELECT MODE_TARGET FROM `NT_MAIN`.`V_ALL_USER_INFO` WHERE USERID = NGIC.WRT_USERID)";
		} 

		//확인여부 검색
		if($ARR['NGIC_CONFIRM_YN_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(NGIC.NGIC_CONFIRM_YN ,'".addslashes($ARR['NGIC_CONFIRM_YN_SEARCH'])."')>0 ";	
		}
		

		// 페이징처리를 위해 전체데이터
		$iSQL = "SELECT 
					COUNT(*) AS CNT
										
				FROM NT_MAIN.NMG_GARAGE_INSU_CHARGE NGIC,
					NT_MAIN.NMG_GARAGE MGG,
					NT_MAIN.NMG_GARAGE_SUB MGGS,
					NT_MAIN.NMI_INSU NII,
					NT_MAIN.NMI_INSU_CHARGE NIC
						
				WHERE 
					 NGIC.MGG_ID = MGG.MGG_ID
				AND MGGS.MGG_ID = MGG.MGG_ID
				AND NIC.NIC_ID = NGIC.NIC_ID
				AND NII.MII_ID = NIC.MII_ID
				AND  MGG.DEL_YN = 'N'
				AND  MGGS.DEL_YN = 'N'
				AND  NII.DEL_YN = 'N'
				AND  NIC.DEL_YN = 'N'
				$tmpKeyword
				ORDER BY NGIC.WRT_DTHMS DESC
				;"
		;
		// echo $iSQL;
		try{
			$RS_T	= DBManager::getRecordSet($iSQL);
		} catch (Exception $e){
			echo "error : ".$e;
			return ERROR_DB_SELECT_DATA;   //ERROR_DB_SELECT_DATA
		}
	
		
		$TOTAL_LIST_COUNT = $RS_T['CNT'];         // 총 몇개인지
		$IDX = $RS_T['CNT']-$LimitStart;          // 해당페이지 index
		unset($RS_T);
		///

		/** 전체리스트가 아닌경우에만 limitSql 적용 */
		$LIMIT_SQL = "";
		if($ARR['REQ_MODE'] == CASE_LIST){
			$LIMIT_SQL = "LIMIT $LimitStart,$LimitEnd";
		}

		
		// 리스트 가져오기
		$iSQL = "SELECT 
					NT_CODE.GET_CSD_NM(MGG.CSD_ID)AS CSD_NM,
					NGIC.MGG_ID,
					MGG.MGG_NM,
					NT_CODE.GET_CRR_NM(MGG.CRR_1_ID, MGG.CRR_2_ID, MGG.CRR_3_ID)AS CRR_NM,
					MGG.MGG_BIZ_ID,
					(
						SELECT COUNT(NBI.NB_ID) 
						FROM NT_BILL.NBB_BILL_INSU NBI,
							 NT_BILL.NBB_BILL NB
						WHERE NBI.NIC_ID = NIC.NIC_ID AND NBI.NBI_INSU_DONE_YN = 'N' 
						AND NB.DEL_YN = 'N' AND NB.NB_ID = NBI.NB_ID AND NB.MGG_ID = NGIC.MGG_ID
					)AS NOT_CNT,
					CAST(AES_DECRYPT(MGGS.MGG_CLAIM_NM_AES,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `MGG_CLAIM_NM_AES`,
					MGGS.MGG_OFFICE_TEL,
					CAST(AES_DECRYPT(MGGS.MGG_CLAIM_TEL_AES,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `MGG_CLAIM_TEL_AES`,
					NT_MAIN.GET_INSU_CHG_MII_ID(NIC.NIC_ID) AS 'MII_ID',
					NT_MAIN.GET_INSU_NM(NT_MAIN.GET_INSU_CHG_MII_ID(NIC.NIC_ID)) AS 'MII_NM',
					DATE_FORMAT(NGIC.WRT_DTHMS, '%Y-%m-%d') AS WRT_DTHMS,
					DATE_FORMAT(NGIC.LAST_DTHMS,'%Y-%m-%d') AS LAST_DTHMS,
					CAST(AES_DECRYPT(NIC.NIC_FAX_AES,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `NIC_FAX_AES`,
					CAST(AES_DECRYPT(NIC.NIC_NM_AES,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `NIC_NM_AES`,
					CAST(AES_DECRYPT(NIC.NIC_TEL2_AES,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `NIC_TEL2_AES`,
					CAST(AES_DECRYPT(NIC.NIC_TEL1_AES,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `NIC_TEL1_AES`,
					-- IFNULL(RMT.RMT_NM,'')AS RMT_NM,
					NIC.NIC_ID,
					NGIC.NGIC_CONFIRM_YN,
					NGIC.RICT_ID,
					IFNULL(SUBSTRING(  IF(NT_CODE.GET_CODE_NM((SELECT MODE_TARGET FROM `NT_MAIN`.`V_ALL_USER_INFO` WHERE USERID = NGIC.WRT_USERID  LIMIT 1))='전체',
                                                '본',
					NT_CODE.GET_CODE_NM((SELECT MODE_TARGET FROM `NT_MAIN`.`V_ALL_USER_INFO` WHERE USERID = NGIC.WRT_USERID LIMIT 1))) ,1,1),'') AS 'RMT_NM',
					NT_CODE.GET_CODE_NM(NGIC.RICT_ID)	 AS 'RICT_NM'
					
				FROM NT_MAIN.NMG_GARAGE_INSU_CHARGE NGIC,
					NT_MAIN.NMG_GARAGE MGG,
					NT_MAIN.NMG_GARAGE_SUB MGGS,
					NT_MAIN.NMI_INSU_CHARGE NIC
						
				
				WHERE 
					 NGIC.MGG_ID = MGG.MGG_ID
				AND MGGS.MGG_ID = MGG.MGG_ID
				AND NIC.NIC_ID = NGIC.NIC_ID
				AND  MGG.DEL_YN = 'N'
				AND  MGGS.DEL_YN = 'N'
				AND  NIC.DEL_YN = 'N'
				$tmpKeyword
				ORDER BY NGIC.WRT_DTHMS DESC
				$LIMIT_SQL
				;"
		
		;
		// echo $iSQL;
		$Result = DBManager::getResult($iSQL);

		///

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"IDX"=>$IDX--,
					"CSD_NM"=>$RS['CSD_NM'],
					"MGG_ID"=>$RS['MGG_ID'],
					"MGG_NM"=>$RS['MGG_NM'],
					"CRR_NM"=>$RS['CRR_NM'],
					"NOT_CNT"=>$RS['NOT_CNT'],
					"RICT_ID"=>$RS['RICT_ID'],
					"RICT_NM"=>$RS['RICT_NM'],
					"MGG_BIZ_ID"=>$RS['MGG_BIZ_ID'],
					"MGG_CLAIM_NM_AES"=>$RS['MGG_CLAIM_NM_AES'],
					"MGG_OFFICE_TEL"=>$RS['MGG_OFFICE_TEL'],
					"MGG_CLAIM_TEL_AES"=>$RS['MGG_CLAIM_TEL_AES'],
					"MII_ID"=>$RS['MII_ID'],
					"NIC_ID"=>$RS['NIC_ID'],
					"MII_NM"=>$RS['MII_NM'],
					"WRT_DTHMS"=>$RS['WRT_DTHMS'],
					"LAST_DTHMS"=>$RS['LAST_DTHMS'],
					"NIC_NM_AES"=>$RS['NIC_NM_AES'],
					"NIC_FAX_AES"=>$RS['NIC_FAX_AES'],
					"NIC_TEL2_AES"=>$RS['NIC_TEL2_AES'],
					"NIC_TEL1_AES"=>$RS['NIC_TEL1_AES'],
					"NGIC_CONFIRM_YN"=>$RS['NGIC_CONFIRM_YN'],
					"RMT_NM"=>$RS['RMT_NM']
			));
		}

		// $Result->close();  // 자원 반납
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
		if($ARR['REQ_MODE'] == CASE_CREATE){
			$MGG_ID = json_decode($ARR['MGG_ID'])->MGG_ID;

			$iSQL = "SELECT
						MGG_ID
					FROM NT_MAIN.NMG_GARAGE_INSU_CHARGE 
					WHERE MGG_ID = '".addslashes($ARR['MGG_ID'])."'
					AND NIC_ID = '".addslashes($ARR['NIC_ID'])."'
					AND `RICT_ID` = '".addslashes($ARR['RICT_ID'])."'
					;"
			;
			// echo $iSQL;
			$Result = DBManager::getRecordSet($iSQL);

			if(!empty($Result)){
				return CONFLICT;
			}else{
				$SESSION_ID = $_SESSION['USERID'] == null ? 'SYSYTEM' : $_SESSION['USERID'];
			
				$iSQL = "INSERT INTO `NT_MAIN`.`NMG_GARAGE_INSU_CHARGE`
						(
							`MGG_ID`,
							`NIC_ID`,
							`NGIC_CONFIRM_YN`,
							`RICT_ID`,
							`WRT_DTHMS`,
							`LAST_DTHMS`,
							`WRT_USERID`
						)
						VALUES
						(
							'".addslashes($ARR['MGG_ID'])."',
							'".addslashes($ARR['NIC_ID'])."',
							'".YN_Y."',
							'".addslashes($ARR['RICT_ID'])."',
							now(),
							now(),
							'".$SESSION_ID."'
						)
						;"
				;

				$iSQL .= "INSERT INTO `NT_MAIN`.`NMI_INSU_CHARGE_TYPE`
						(
							`NIC_ID`,
							`RICT_ID`,
							`WRT_DTHMS`,
							`LAST_DTHMS`,
							`WRT_USERID`
						)
						VALUES
						(
							'".addslashes($ARR['NIC_ID'])."',
							'".addslashes($ARR['RICT_ID'])."',
							now(),
							now(),
							'".$SESSION_ID."'
						)
						;"
				;
				// echo $iSQL;
				return DBManager::execMulti($iSQL);
			}
		}else{
			/** 담당지역 확인 업데이트 */
			if(!empty($ARR['TYPE'])){
				foreach ($ARR['ID_ARRAY'] as $index => $ID_ARRAY) {
					$ITEM = json_decode($ID_ARRAY);
					$MGG_ID = $ITEM->MGG_ID;
					$NIC_ID = $ITEM->NIC_ID;
					$RICT_ID = $ITEM->RICT_ID;

					$iSQL .= "UPDATE `NT_MAIN`.`NMG_GARAGE_INSU_CHARGE`
							SET
								`NGIC_CONFIRM_YN` = '".YN_Y ."',
								`LAST_DTHMS` = now()
							WHERE `MGG_ID` = '".$MGG_ID."'
							AND `NIC_ID` = '".$NIC_ID."'
							AND `RICT_ID` = '".$RICT_ID."'
							;"
					;
				}
				
				return DBManager::execMulti($iSQL);
			}else{
				$MGG_ID = json_decode($ARR['MGG_ID'])->MGG_ID;
				$RESULT_SQL = true;
				if($ARR['ORI_MGG_ID'] != $MGG_ID  || $ARR['ORI_NIC_ID'] != $ARR['NIC_ID'] || $ARR['ORI_RICT_ID'] != $ARR['RICT_ID']){
					$iSQL = "SELECT
								MGG_ID
							FROM NT_MAIN.NMG_GARAGE_INSU_CHARGE 
							WHERE MGG_ID = '".addslashes($ARR['MGG_ID'])."'
							AND NIC_ID = '".addslashes($ARR['NIC_ID'])."'
							AND `RICT_ID` = '".addslashes($ARR['RICT_ID'])."'
							;"
					;
					$Result = DBManager::getRecordSet($iSQL);
					if(!empty($Result)){
						$RESULT_SQL  = false;
						return CONFLICT;
					}
				}
				

				if($RESULT_SQL){
					$SESSION_ID = $_SESSION['USERID'] == null ? 'SYSYTEM' : $_SESSION['USERID'];
					/** 해당 공업사 정보 업데이트 */
					$iSQL = "UPDATE `NT_MAIN`.`NMG_GARAGE_INSU_CHARGE`
								SET
									`MGG_ID` = '".addslashes($ARR['MGG_ID'])."',
									`NIC_ID`= '".addslashes($ARR['NIC_ID'])."',
									`RICT_ID`= '".addslashes($ARR['RICT_ID'])."',
									`LAST_DTHMS` = now(),
									`WRT_USERID` = '".$SESSION_ID."'
								WHERE `MGG_ID` = '".addslashes($ARR['ORI_MGG_ID'])."'
								AND `NIC_ID` = '".addslashes($ARR['ORI_NIC_ID'])."'
								AND `RICT_ID` = '".addslashes($ARR['ORI_RICT_ID'])."'
								;"
					;
					$iSQL .= "UPDATE `NT_MAIN`.`NMI_INSU_CHARGE_TYPE`
								SET
									`NIC_ID`= '".addslashes($ARR['NIC_ID'])."',
									`RICT_ID` = '".addslashes($ARR['RICT_ID'])."',
									`LAST_DTHMS` = now(),
									`WRT_USERID` = '".$SESSION_ID."'
								WHERE `NIC_ID` = '".addslashes($ARR['ORI_NIC_ID'])."'
								AND `RICT_ID` = '".addslashes($ARR['ORI_RICT_ID'])."'
								;"
					;
					// echo $iSQL;
					return DBManager::execMulti($iSQL);
				}
			}
	
			
		}
		
	}

	/**
	 * 삭제
	 */
	function delData($ARR) {
		foreach ($ARR['ID_ARRAY'] as $index => $ID_ARRAY) {
			$ITEM = json_decode($ID_ARRAY);
			$MGG_ID = $ITEM->MGG_ID;
			$NIC_ID = $ITEM->NIC_ID;
			$RICT_ID = $ITEM->RICT_ID;

			$iSQL .= "DELETE 
						FROM `NT_MAIN`.`NMG_GARAGE_INSU_CHARGE`
					WHERE `MGG_ID`='".$MGG_ID."'
					AND `NIC_ID`='".$NIC_ID."'
					AND `RICT_ID`='".$RICT_ID."'
					;"
			;
		}

		return DBManager::execMulti($iSQL);
	}

	/** 공업사정보(청구담당자 정보도 포함) */
	function getData($ARR){
		$iSQL = "SELECT 
					MGG.MGG_NM,
					MGG.MGG_ID,
					MGGS.MGG_OFFICE_TEL,
					CAST(AES_DECRYPT(MGGS.MGG_CLAIM_NM_AES,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS MGG_CLAIM_NM_AES,
					CAST(AES_DECRYPT(MGGS.MGG_CLAIM_TEL_AES,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS MGG_CLAIM_TEL_AES,
					NIC.MII_ID,
					CAST(AES_DECRYPT(NIC.NIC_NM_AES,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS NIC_NM_AES,
					CAST(AES_DECRYPT(NIC.NIC_FAX_AES,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS NIC_FAX_AES,
					CAST(AES_DECRYPT(NIC.NIC_TEL1_AES,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS NIC_TEL1_AES,
					NGIC.NIC_ID, 
					NGIC.RICT_ID 
				FROM NT_MAIN.NMG_GARAGE MGG,
					NT_MAIN.NMG_GARAGE_SUB MGGS,
					NT_MAIN.NMG_GARAGE_INSU_CHARGE  NGIC,
					NT_MAIN.NMI_INSU_CHARGE NIC
				WHERE MGG.MGG_ID = MGGS.MGG_ID
				AND NGIC.MGG_ID = MGG.MGG_ID
				AND NGIC.NIC_ID = NIC.NIC_ID
				AND MGG.MGG_ID = '".addslashes($ARR['MGG_ID'])."'
				AND NGIC.RICT_ID = '".addslashes($ARR['RICT_ID'])."'
				AND NIC.MII_ID = '".addslashes($ARR['MII_ID'])."'
				AND MGG.DEL_YN = 'N'
				;"
		;
		// echo $iSQL;
		$Result= DBManager::getResult($iSQL);
		///

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"MGG_NM"=>$RS['MGG_NM'],
					"MGG_ID"=>$RS['MGG_ID'],
					"NIC_ID"=>$RS['NIC_ID'],
					"MGG_OFFICE_TEL"=>$RS['MGG_OFFICE_TEL'],
					"MGG_CLAIM_NM_AES"=>$RS['MGG_CLAIM_NM_AES'],
					"MGG_CLAIM_TEL_AES"=>$RS['MGG_CLAIM_TEL_AES'],
					"MII_ID"=>$RS['MII_ID'],
					"NIC_NM_AES"=>$RS['NIC_NM_AES'],
					"NIC_FAX_AES"=>$RS['NIC_FAX_AES'],
					"NIC_TEL1_AES"=>$RS['NIC_TEL1_AES'],
					"RICT_ID"=>$RS['RICT_ID'],

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

	/** 공업사정보(청구담당자 정보도 포함) */
	function getGarageInfo($ARR){
		$iSQL = "SELECT 
					MGG.MGG_NM,
					MGG.MGG_ID,
					MGGS.MGG_OFFICE_TEL,
					CAST(AES_DECRYPT(MGGS.MGG_CLAIM_NM_AES,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS MGG_CLAIM_NM_AES,
					CAST(AES_DECRYPT(MGGS.MGG_CLAIM_TEL_AES,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS MGG_CLAIM_TEL_AES
					
				FROM NT_MAIN.NMG_GARAGE MGG,
				     NT_MAIN.NMG_GARAGE_SUB MGGS
				WHERE MGG.MGG_ID = MGGS.MGG_ID
				AND MGG.DEL_YN = 'N'
				;"
		;

		$Result= DBManager::getResult($iSQL);
		///

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
				"MGG_NM"=>$RS['MGG_NM'],
				"MGG_ID"=>$RS['MGG_ID'],
				"MGG_OFFICE_TEL"=>$RS['MGG_OFFICE_TEL'],
				"MGG_CLAIM_NM_AES"=>$RS['MGG_CLAIM_NM_AES'],
				"MGG_CLAIM_TEL_AES"=>$RS['MGG_CLAIM_TEL_AES']

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

	/** 
	 * 보험사 보상담당자관리 
	 */
	function getInsuChargeInfo($ARR){
		$iSQL = "SELECT
					NIC.NIC_ID,
					CAST(AES_DECRYPT(NIC.NIC_NM_AES,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `NIC_NM_AES`,
					CAST(AES_DECRYPT(NIC.NIC_FAX_AES,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `NIC_FAX_AES`,
					CAST(AES_DECRYPT(NIC.NIC_TEL1_AES,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `NIC_TEL1_AES`,
					SUBSTR(NIC.WRT_DTHMS,1,10) AS WRT_DTHMS,
					IFNULL(NICP.RICT_ID,'') AS RICT_ID,
                    IFNULL(RICP.RICT_NM,'')AS RICT_NM,
					NGIC.NGIC_CONFIRM_YN,
					NT_BILL.GET_NIC_CLAIM_COUNT(NIC.NIC_ID,'".$ARR['MGG_ID']."') AS CLAIM_CNT


				FROM NT_MAIN.NMI_INSU_CHARGE  NIC
				LEFT JOIN NT_MAIN.NMI_INSU_CHARGE_TYPE NICP
                ON NIC.NIC_ID = NICP.NIC_ID
                LEFT JOIN NT_CODE.REF_INSU_CHARGE_TYPE RICP
                ON RICP.RICT_ID = NICP.RICT_ID
				LEFT JOIN NT_MAIN.NMG_GARAGE_INSU_CHARGE NGIC
                ON NGIC.MGG_ID = '".addslashes($ARR['MGG_ID'])."'
                AND NGIC.NIC_ID = NIC.NIC_ID 
				WHERE MII_ID = '".addslashes($ARR['MII_ID'])."'
				AND NIC.DEL_YN = 'N'
				GROUP BY NIC.NIC_ID
				;"
		;

		$Result= DBManager::getResult($iSQL);
		// echo $iSQL;
		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"NIC_ID"=>$RS['NIC_ID'],
					"NIC_NM_AES"=>$RS['NIC_NM_AES'],
					"NIC_FAX_AES"=>$RS['NIC_FAX_AES'],
					"NIC_TEL1_AES"=>$RS['NIC_TEL1_AES'],
					"RICT_ID"=>$RS['RICT_ID'],
					"CLAIM_CNT"=>$RS['CLAIM_CNT'],
					"NGIC_CONFIRM_YN"=>$RS['NGIC_CONFIRM_YN'],
					"RICT_NM"=>$RS['RICT_NM'],
					"WRT_DTHMS"=>$RS['WRT_DTHMS'],
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
	$clsGwCompensation = new clsGwCompensation(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$clsGwCompensation->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
