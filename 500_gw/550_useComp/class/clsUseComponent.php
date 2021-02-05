<?php

class clsUseComponent extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자
	private $AES_KEY = "123";
	private $SEARCH_COL = ['CEO','KEYMAN', 'CLAIM', 'FACTORY', 'PAINTER', 'METAL'];

	// Class Object Constructor
	function clsUseComponent($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
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
		$tmpKeyword = "";      // 일반검색
		$tmpKeywordSub = "";   // 취급부품검색시(group_concat 검색용으로)

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

				if($CSD[1] != NOT_CRR_3){
					$tmpKeyword .= "AND INSTR(`MGG`.`CRR_2_ID`,'".$CSD[1]."')>0 ";
				}
				if($CSD[2] != NOT_CRR_3){
					$tmpKeyword .= "AND INSTR(`MGG`.`CRR_3_ID`,'".$CSD[2]."')>0 ";
				}
				
				$tmpKeyword .= ")";
			}
			$tmpKeyword .= ")";
		} 
		

		//공업사명
		if($ARR['MGG_NM_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(`MGG`.`MGG_NM`,'".$ARR['MGG_NM_SEARCH']."')>0 ";
		} 

		//거래여부 검색
		if($ARR['RCT_ID_SERACH'] != '') {
			$tmpKeyword .= "AND INSTR(`MGGS`.`RCT_ID`,'".addslashes($ARR['RCT_ID_SERACH'])."')>0 ";	
		}
		

		//기준부품
		if($ARR['CPP_ID_SEARCH'] != '') {
			$tmpKeywordSub .= "Having (";
			foreach ($ARR['CPP_ID_SEARCH'] as $index => $CPP_ID) {
				if($index == 0){
					$tmpKeywordSub .= "Find_In_Set('".$CPP_ID."',CPP_ID) > 0";
				}else{
					$tmpKeywordSub .= " OR Find_In_Set('".$CPP_ID."',CPP_ID) > 0";
				}
			}
			$tmpKeywordSub .= ")";
		} 

		//취급부품
		if($ARR['CPPS_ID_SEARCH'] != '') {
			if($tmpKeywordSub != ''){
				$tmpKeywordSub .= " AND (";
			}else{
				$tmpKeywordSub .= "Having (";
			}
			
			foreach ($ARR['CPPS_ID_SEARCH'] as $index => $CPPS_ID) {
				if($index == 0){
					$tmpKeywordSub .= "Find_In_Set('".$CPPS_ID."',CPPS_ID) > 0";
				}else{
					$tmpKeywordSub .= " OR Find_In_Set('".$CPPS_ID."',CPPS_ID) > 0";
				}
			}
			$tmpKeywordSub .= ")";
		} 

		$RATIO_SUB_SQL = "";
		//배율
		if($ARR['RATIO_YN'] != '') {
			if($tmpKeywordSub != ''){
				$tmpKeywordSub .= " AND (";
			}else{
				$tmpKeywordSub .= "Having (";
			}

			if($ARR['RATIO_YN'] == YN_Y) {
				// $tmpKeywordSub .= "NGP_RATIO > 1";
				$tmpKeywordSub .= "NGP_RATIO > 0";
			}else{
				$tmpKeywordSub .= "NGP_RATIO = 0";
				// $tmpKeywordSub .= "NGP_RATIO = 1";
			}
			
			$tmpKeywordSub .= ")";
		} 
		
		// 페이징처리를 위해 전체데이터
		$iSQL = "SELECT COUNT(*)AS CNT FROM (
					SELECT 
						GROUP_CONCAT(CPP.CPP_ID)AS CPP_ID,
						GROUP_CONCAT(CPPS.CPPS_ID)AS CPPS_ID,
						SUM(IF(NGP.NGP_RATIO != 1, 1,0)) >1 AS NGP_RATIO,
						GROUP_CONCAT(IF(NGP.NGP_DEFAULT_YN = '".YN_Y."', NGP.NGP_RATIO, '%%'))AS NGP_RATIO_DEFAULT_YN,
						(
						SELECT COUNT(NGP_RATIO)
                        FROM NT_MAIN.NMG_GARAGE_PART 
                        WHERE NGP_DEFAULT_YN='Y' 
                        AND MGG_ID = MGG.MGG_ID
                        AND NGP_RATIO = '1'
						) AS APPLY_RATIO,
						(
							SELECT COUNT(NGP_RATIO)
							FROM NT_MAIN.NMG_GARAGE_PART 
							WHERE NGP_DEFAULT_YN='Y' 
							AND MGG_ID = MGG.MGG_ID
						) AS TOTAL_RATIO

					FROM NT_MAIN.NMG_GARAGE MGG,
								NT_MAIN.NMG_GARAGE_SUB MGGS,
								NT_MAIN.NMG_GARAGE_PART NGP,
								NT_CODE.NCS_DIVISION CSD,
								NT_CODE.NCR_REGION CSR,
								NT_CODE.NCP_PART CPP,
								NT_CODE.NCP_PART_SUB CPPS
								
					WHERE MGG.MGG_ID = NGP.MGG_ID
					AND MGG.CSD_ID = CSD.CSD_ID
					AND MGGS.MGG_ID = MGG.MGG_ID
					AND CSR.CRR_1_ID = MGG.CRR_1_ID
					AND CSR.CRR_2_ID = MGG.CRR_2_ID
					AND CSR.CRR_3_ID = '".NOT_CRR_3."'
					AND CPP.CPP_ID = NGP.CPP_ID
					AND CPPS.CPP_ID = CPP.CPP_ID
					AND NGP.CPPS_ID = CPPS.CPPS_ID
					AND MGG.DEL_YN = 'N'
					AND MGGS.DEL_YN = 'N'
					$tmpKeyword
					GROUP BY   MGG.MGG_ID
					$tmpKeywordSub
				
				)AS CNT
				"
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

		$PARTS_ARRAY = $this->getPartsType();
		$USE_PARTS_SQL = "";
		$SUPP_PARTS_SQL = "";

		foreach($PARTS_ARRAY  as $index => $PARTS){
			$USE_PARTS_SQL .= " IFNULL((
				SELECT CONCAT(NT_CODE.GET_CPPS_NM(CPPS_ID),' [',NGP_RATIO,']') 
				FROM NT_MAIN.NMG_GARAGE_PART 
				WHERE MGG_ID = MGG.MGG_ID 
				AND CPP_ID = '".$PARTS['CPP_ID']."' 
				AND NGP_DEFAULT_YN = 'Y'
			),'')AS 'USE_".$PARTS['CPP_ID']."',";

			$SUPP_PARTS_SQL .= " IFNULL((
				SELECT GROUP_CONCAT(NT_CODE.GET_CPPS_NM(CPPS_ID)) 
				FROM NT_MAIN.NMG_GARAGE_PART 
				WHERE MGG_ID = MGG.MGG_ID 
				AND CPP_ID = '".$PARTS['CPP_ID']."' 
			),'')AS 'SUPP_".$PARTS['CPP_ID']."',";
		}

		// 리스트 가져오기
		$iSQL = "SELECT 
					CSD.CSD_NM,
					CSR.CRR_NM,
					MGG.MGG_ID,
					MGG.MGG_NM,
					MGG.MGG_BIZ_ID,
					MGGS.MGG_BIZ_START_DT,
					(
						SELECT COUNT(NB_ID) FROM NT_BILL.NBB_BILL 
						WHERE MGG_ID = MGG.MGG_ID AND YEAR(NB_CLAIM_DT) = YEAR(NOW()) AND  MONTH(NB_CLAIM_DT) = MONTH(NOW())
						AND DEL_YN = 'N'
                    )AS CLAIM_CNT,
                    (
						SELECT FORMAT(SUM(NB_TOTAL_PRICE),0) 
						FROM NT_BILL.NBB_BILL WHERE MGG_ID = MGG.MGG_ID AND YEAR(NB_CLAIM_DT) = YEAR(NOW()) AND  MONTH(NB_CLAIM_DT) = MONTH(NOW())
						AND DEL_YN = 'N'
                    )AS CLAIM_PRICE,
                    NT_STOCK.GET_STOCK_DATA(MGG.MGG_ID,'P') AS STOCK_PRICE,
					SUM(IF(NGP.NGP_RATIO != 1, 1,0)) >1 AS NGP_RATIO,
					IFNULL(MGGS.MGG_BIZ_END_DT,'') AS MGG_BIZ_END_DT,
					$USE_PARTS_SQL 
					$SUPP_PARTS_SQL
					GROUP_CONCAT(CPP.CPP_ID)AS CPP_ID,
					GROUP_CONCAT(CPPS.CPPS_ID)AS CPPS_ID,
					MGGS.MGG_INSU_MEMO,
					MGGS.MGG_MANUFACTURE_MEMO
					
				FROM NT_MAIN.NMG_GARAGE MGG,
							NT_MAIN.NMG_GARAGE_SUB MGGS,
							NT_MAIN.NMG_GARAGE_PART NGP,
							NT_CODE.NCS_DIVISION CSD,
							NT_CODE.NCR_REGION CSR,
							NT_CODE.NCP_PART CPP,
							NT_CODE.NCP_PART_SUB CPPS
							
				WHERE MGG.MGG_ID = NGP.MGG_ID
				AND MGG.CSD_ID = CSD.CSD_ID
				AND MGGS.MGG_ID = MGG.MGG_ID
				AND CSR.CRR_1_ID = MGG.CRR_1_ID
				AND CSR.CRR_2_ID = MGG.CRR_2_ID
				AND CSR.CRR_3_ID = '00000'
				AND CPP.CPP_ID = NGP.CPP_ID
				AND CPPS.CPP_ID = CPP.CPP_ID
				AND NGP.CPPS_ID = CPPS.CPPS_ID
				AND MGG.DEL_YN = 'N'
				AND MGGS.DEL_YN = 'N'
				$tmpKeyword
				GROUP BY   MGG.MGG_ID
				$tmpKeywordSub
				ORDER BY MGG.WRT_DTHMS DESC
				$LIMIT_SQL
				;"
		
		;

		// echo $iSQL;
		$Result = DBManager::getResult($iSQL);

		///
		

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			$USE_PARTS_ARRAY = array();
			$SUPP_PARTS_ARRAY = array();
			foreach($PARTS_ARRAY as $index => $PARTS){
				array_push($USE_PARTS_ARRAY,$RS["USE_".$PARTS['CPP_ID']]);
				array_push($SUPP_PARTS_ARRAY,$RS["SUPP_".$PARTS['CPP_ID']]);
			}
			array_push($rtnArray,
				array(
					"IDX"=>$IDX--,
					"CSD_NM"=>$RS['CSD_NM'],
					"CRR_NM"=>$RS['CRR_NM'],
					"MGG_ID"=>$RS['MGG_ID'],
					"MGG_NM"=>$RS['MGG_NM'],
					"CPP_ORDER_NUM"=>$RS['CPP_ORDER_NUM'],
					"MGG_BIZ_ID"=>$RS['MGG_BIZ_ID'],
					"MGG_BIZ_START_DT"=>$RS['MGG_BIZ_START_DT'],
					"MGG_BIZ_END_DT"=>$RS['MGG_BIZ_END_DT'],
					"CLAIM_CNT"=>$RS['CLAIM_CNT'],
					"CLAIM_PRICE"=>$RS['CLAIM_PRICE'],
					"STOCK_PRICE"=>$RS['STOCK_PRICE'],
					"CPP_ID"=>$RS['CPP_ID'],
					"CPPS_ID"=>$RS['CPPS_ID'],
					"MGG_INSU_MEMO"=>$RS['MGG_INSU_MEMO'],
					"MGG_MANUFACTURE_MEMO"=>$RS['MGG_MANUFACTURE_MEMO'],
					"USE_PARTS_ARRAY" => $USE_PARTS_ARRAY ,
					"SUPP_PARTS_ARRAY" => $SUPP_PARTS_ARRAY 
					
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

	/** 부품타입 가져오기 */
	function getPartsType(){
		$iSQL = "SELECT 
						CCD.`CCD_PREFIX_CD`,
						CCD.`CCD_VAL_CD`,
						CCD.`CCD_NM`,
						CPP.CPP_ID,
						CPP.CPP_NM,
						CPP.CPP_ORDER_NUM,
						CPP.CPP_NM_SHORT,
						GROUP_CONCAT(CPPS.CPPS_ID) AS CPPS_ID,
                        GROUP_CONCAT(CPPS.CPPS_NM) AS CPPS_NM

				FROM `NT_CODE`.`NCL_CODE_DIC` CCD,
					 NT_CODE.NCP_PART  CPP,
					 NT_CODE.NCP_PART_SUB CPPS
				WHERE `CCD_DOMAIN` = 'PART_TYPE'
				AND CPP.CPP_PART_TYPE = CCD.CCD_NM
				AND CPP.DEL_YN = '".YN_N."'
				AND CPP.CPP_USE_YN = '".YN_Y."'
				AND CPPS.DEL_YN = '".YN_N."'
                AND CPPS.CPP_ID = CPP.CPP_ID
				GROUP BY CPP.CPP_ID
				ORDER BY CCD.`CCD_VAL_CD`, CPP.CPP_ORDER_NUM ASC 
				;"
		;
		$Result= DBManager::getResult($iSQL);
		///

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"CCD_PREFIX_CD"=>$RS['CCD_PREFIX_CD'],
					"CCD_VAL_CD"=>$RS['CCD_VAL_CD'],
					"CPP_ID"=>$RS['CPP_ID'],
					"CCD_NM"=>$RS['CCD_NM'],
					"CPP_NM"=>$RS['CPP_NM'],
					"CPP_ORDER_NUM"=>$RS['CPP_ORDER_NUM'],
					"CPPS_ID"=>$RS['CPPS_ID'],
					"CPPS_NM"=>$RS['CPPS_NM'],
					"CPP_NM_SHORT"=>$RS['CPP_NM_SHORT']
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
	$clsUseComponent = new clsUseComponent(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$clsUseComponent->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
