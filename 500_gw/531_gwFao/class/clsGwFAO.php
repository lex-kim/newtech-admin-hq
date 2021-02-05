<?php

class clsGwFAO extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자
	private $AES_KEY = "123";
	private $SEARCH_COL = ['CEO','KEYMAN', 'CLAIM', 'FACTORY', 'PAINTER', 'METAL'];

	// Class Object Constructor
	function clsGwFAO($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
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

		//공업사 검색
		if($ARR['MGG_ID_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(MGG.MGG_NM ,'".addslashes($ARR['MGG_ID_SEARCH'])."')>0 ";	
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

		//거래여부 검색
		if($ARR['RCT_ID_SERACH'] != '') {
			$tmpKeyword .= "AND INSTR(`MGGS`.`RCT_ID`,'".addslashes($ARR['RCT_ID_SERACH'])."')>0 ";	
		}

		//직원 이름/연락처 검색
		if($ARR['MGG_NM_TEL_SERACH'] != '') {
			$tmpKeyword .= "AND (";
			foreach ($this->SEARCH_COL as $index => $COL) {
				$AES_COL = "CAST(AES_DECRYPT(MGGS.MGG_".$COL."_".$ARR['MGG_NM_TEL_SERACH']."_AES,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR)";
				if($index == 0){
					$tmpKeyword .= "(";
					$tmpKeyword .= "INSTR(".$AES_COL.",'".$ARR['MGG_NM_TEL_INPUT']."')>0 ";
				}else{
					$tmpKeyword .= " OR(";
					$tmpKeyword .= " INSTR(".$AES_COL.",'".$ARR['MGG_NM_TEL_INPUT']."')>0 ";
				}
				
				$tmpKeyword .= ")";
			}
			$tmpKeyword .= ")";
		}
		

		// 페이징처리를 위해 전체데이터
		$iSQL = "SELECT 
					COUNT(CSD.CSD_NM) AS CNT
						
				FROM NT_MAIN.NMG_GARAGE MGG,
							NT_MAIN.NMG_GARAGE_SUB MGGS,
							NT_CODE.NCS_DIVISION CSD,
							NT_CODE.NCR_REGION CSR
				WHERE MGG.MGG_ID = MGGS.MGG_ID
				AND MGG.DEL_YN = 'N'
				AND MGGS.DEL_YN = 'N'
				AND MGG.CSD_ID = CSD.CSD_ID
				AND CSR.CRR_1_ID = MGG.CRR_1_ID
				AND CSR.CRR_2_ID = MGG.CRR_2_ID
				AND CSR.CRR_3_ID = '".NOT_CRR_3."'
				$tmpKeyword
				;"
		;

		
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
					CSD.CSD_NM ,
					CSR.CRR_NM,
					MGG.MGG_NM,
					MGG.MGG_BIZ_ID,
					MGGS.MGG_ID,
					(SELECT RCT_NM FROM NT_CODE.REF_CONTRACT_TYPE WHERE RCT_ID = MGGS.RCT_ID) AS RCT_NM,
					MGGS.MGG_BIZ_START_DT,
					IFNULL(MGGS.MGG_BIZ_END_DT,'') AS MGG_BIZ_END_DT,
					SUBSTRING(MGGS.LAST_DTHMS, 1, 10) AS LAST_DT,
					IFNULL((
						SELECT 
							ROUND(SUM(IF(NB_ID IS NOT NULL , 1,0))/3, 2) AS AVG_CNT
						FROM NT_BILL.NBB_BILL 
						WHERE 
							MGG_ID = MGG.MGG_ID
						AND DEL_YN = 'N'
						AND DATE_FORMAT(NB_CLAIM_DT, '%Y-%m' ) 
							BETWEEN DATE_FORMAT(SUBDATE(NOW(), INTERVAL 3 MONTH), '%Y-%m' ) 
							AND  DATE_FORMAT(SUBDATE(NOW(), INTERVAL 1 MONTH), '%Y-%m' ) 
					),'-') AS AVG_CNT,
					IFNULL((
						SELECT 
							FORMAT(ROUND(SUM(NB_TOTAL_PRICE)/3, 0),0) AVG_PRICE
						FROM NT_BILL.NBB_BILL 
						WHERE 
							MGG_ID = MGG.MGG_ID
						AND DEL_YN = 'N'
						AND DATE_FORMAT(NB_CLAIM_DT, '%Y-%m' ) 
							BETWEEN DATE_FORMAT(SUBDATE(NOW(), INTERVAL 3 MONTH), '%Y-%m' ) 
							AND  DATE_FORMAT(SUBDATE(NOW(), INTERVAL 1 MONTH), '%Y-%m' ) 
					),'-') AS AVG_PRICE,
					MGGS.MGG_OFFICE_TEL,
					MGGS.MGG_OFFICE_FAX,
					CAST(AES_DECRYPT(MGGS.MGG_CEO_NM_AES,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `MGG_CEO_NM_AES`,
					CAST(AES_DECRYPT(MGGS.MGG_CEO_TEL_AES,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `MGG_CEO_TEL_AES`,
					CAST(AES_DECRYPT(MGGS.MGG_KEYMAN_NM_AES,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `MGG_KEYMAN_NM_AES`, 
					CAST(AES_DECRYPT(MGGS.MGG_KEYMAN_TEL_AES,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `MGG_KEYMAN_TEL_AES`,
					CAST(AES_DECRYPT(MGGS.MGG_CLAIM_NM_AES,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `MGG_CLAIM_NM_AES`, 
					CAST(AES_DECRYPT(MGGS.MGG_CLAIM_TEL_AES,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `MGG_CLAIM_TEL_AES`,
					CAST(AES_DECRYPT(MGGS.MGG_FACTORY_NM_AES,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `MGG_FACTORY_NM_AES`, 
					CAST(AES_DECRYPT(MGGS.MGG_FACTORY_TEL_AES,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `MGG_FACTORY_TEL_AES`,
					CAST(AES_DECRYPT(MGGS.MGG_PAINTER_NM_AES,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `MGG_PAINTER_NM_AES`, 
					CAST(AES_DECRYPT(MGGS.MGG_PAINTER_TEL_AES,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `MGG_PAINTER_TEL_AES`,
					CAST(AES_DECRYPT(MGGS.MGG_METAL_NM_AES,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `MGG_METAL_NM_AES`, 
					CAST(AES_DECRYPT(MGGS.MGG_METAL_TEL_AES,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `MGG_METAL_TEL_AES`
						
				FROM NT_MAIN.NMG_GARAGE MGG,
						NT_MAIN.NMG_GARAGE_SUB MGGS,
						NT_CODE.NCS_DIVISION CSD,
						NT_CODE.NCR_REGION CSR
				WHERE MGG.MGG_ID = MGGS.MGG_ID
				AND MGG.DEL_YN = 'N'
				AND MGGS.DEL_YN = 'N'
				AND MGG.CSD_ID = CSD.CSD_ID
				AND CSR.CRR_1_ID = MGG.CRR_1_ID
				AND CSR.CRR_2_ID = MGG.CRR_2_ID
				AND CSR.CRR_3_ID = '".NOT_CRR_3."'
				$tmpKeyword
				$LIMIT_SQL
				;"
		
		;

		$Result = DBManager::getResult($iSQL);

		///

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"IDX"=>$IDX--,
					"CSD_NM"=>$RS['CSD_NM'],
					"CRR_NM"=>$RS['CRR_NM'],
					"MGG_ID"=>$RS['MGG_ID'],
					"MGG_NM"=>$RS['MGG_NM'],
					"MGG_BIZ_ID"=>$RS['MGG_BIZ_ID'],
					"RCT_NM"=>$RS['RCT_NM'],
					"MGG_BIZ_START_DT"=>$RS['MGG_BIZ_START_DT'],
					"MGG_BIZ_END_DT"=>$RS['MGG_BIZ_END_DT'],
					"LAST_DT"=>$RS['LAST_DT'],
					"AVG_CNT"=>$RS['AVG_CNT'],
					"AVG_PRICE"=>$RS['AVG_PRICE'],
					"MGG_OFFICE_TEL"=>$RS['MGG_OFFICE_TEL'],
					"MGG_OFFICE_FAX"=>$RS['MGG_OFFICE_FAX'],
					"MGG_CEO_NM_AES"=>$RS['MGG_CEO_NM_AES'],
					"MGG_CEO_TEL_AES"=>$RS['MGG_CEO_TEL_AES'],
					"MGG_KEYMAN_NM_AES"=>$RS['MGG_KEYMAN_NM_AES'],
					"MGG_KEYMAN_TEL_AES"=>$RS['MGG_KEYMAN_TEL_AES'],
					"MGG_CLAIM_NM_AES"=>$RS['MGG_CLAIM_NM_AES'],
					"MGG_CLAIM_TEL_AES"=>$RS['MGG_CLAIM_TEL_AES'],
					"MGG_FACTORY_NM_AES"=>$RS['MGG_FACTORY_NM_AES'],
					"MGG_FACTORY_TEL_AES"=>$RS['MGG_FACTORY_TEL_AES'],
					"MGG_PAINTER_NM_AES"=>$RS['MGG_PAINTER_NM_AES'],
					"MGG_PAINTER_TEL_AES"=>$RS['MGG_PAINTER_TEL_AES'],
					"MGG_METAL_NM_AES"=>$RS['MGG_METAL_NM_AES'],
					"MGG_METAL_TEL_AES"=>$RS['MGG_METAL_TEL_AES']
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
		$MGG_CEO_NM_AES = "AES_ENCRYPT('".$ARR['MGG_CEO_NM_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))" ;
		$MGG_CEO_TEL_AES = "AES_ENCRYPT('".$ARR['MGG_CEO_TEL_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))" ;
		$MGG_KEYMAN_NM_AES = "AES_ENCRYPT('".$ARR['MGG_KEYMAN_NM_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))" ;
		$MGG_KEYMAN_TEL_AES = "AES_ENCRYPT('".$ARR['MGG_KEYMAN_TEL_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))" ;
		$MGG_CLAIM_NM_AES = "AES_ENCRYPT('".$ARR['MGG_CLAIM_NM_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))" ;
		$MGG_CLAIM_TEL_AES = "AES_ENCRYPT('".$ARR['MGG_CLAIM_TEL_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))" ;
		$MGG_FACTORY_NM_AES = "AES_ENCRYPT('".$ARR['MGG_FACTORY_NM_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))" ;
		$MGG_FACTORY_TEL_AES = "AES_ENCRYPT('".$ARR['MGG_FACTORY_TEL_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))" ;
		$MGG_PAINTER_NM_AES = "AES_ENCRYPT('".$ARR['MGG_PAINTER_NM_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))" ;
		$MGG_PAINTER_TEL_AES = "AES_ENCRYPT('".$ARR['MGG_PAINTER_TEL_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))" ;
		$MGG_METAL_NM_AES = "AES_ENCRYPT('".$ARR['MGG_METAL_NM_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))" ;
		$MGG_METAL_TEL_AES = "AES_ENCRYPT('".$ARR['MGG_METAL_TEL_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))" ;

		$iSQL = "UPDATE `NT_MAIN`.`NMG_GARAGE_SUB`
				  SET
					`MGG_OFFICE_TEL` = '".$ARR['MGG_OFFICE_TEL']."',
					`MGG_OFFICE_FAX` = '".$ARR['MGG_OFFICE_FAX']."',
					`MGG_CEO_NM_AES` = ".$MGG_CEO_NM_AES.",
					`MGG_CEO_TEL_AES` = ".$MGG_CEO_TEL_AES.",
					`MGG_KEYMAN_NM_AES` = ".$MGG_KEYMAN_NM_AES.",
					`MGG_KEYMAN_TEL_AES` = ".$MGG_KEYMAN_TEL_AES.",
					`MGG_CLAIM_NM_AES` = ".$MGG_CLAIM_NM_AES.",
					`MGG_CLAIM_TEL_AES` = ".$MGG_CLAIM_TEL_AES.",
					`MGG_FACTORY_NM_AES` = ".$MGG_FACTORY_NM_AES.",
					`MGG_FACTORY_TEL_AES` = ".$MGG_FACTORY_TEL_AES.",
					`MGG_PAINTER_NM_AES` = ".$MGG_PAINTER_NM_AES.",
					`MGG_PAINTER_TEL_AES` = ".$MGG_PAINTER_TEL_AES.",
					`MGG_METAL_NM_AES` = ".$MGG_METAL_NM_AES.",
					`MGG_METAL_TEL_AES` = ".$MGG_METAL_TEL_AES.",
					`LAST_DTHMS` = now()
				WHERE `MGG_ID` = '".addslashes($ARR['MGG_ID'])."'
				;"
		;
		return DBManager::execQuery($iSQL);
	}


}
	// 0. 객체 생성
	$clsGwFAO = new clsGwFAO(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$clsGwFAO->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT);

?>
