<?php

class cReceipt extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	// Class Object Constructor
	function cReceipt($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
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

		if($ARR['REQ_MODE'] == CASE_LIST){
			$LIMIT_SQL = "LIMIT $LimitStart,$LimitEnd ;";
        }else {
			$LIMIT_SQL = " ;";
		}

	
		if($ARR['DT_KEY_SEARCH'] != '') {
			if($ARR['DT_KEY_SEARCH'] == 'NCPD_STOCK_DT'){ // 입고일
                if($ARR['START_DT_SEARCH'] != '' && $ARR['END_DT_SEARCH'] != ''){ 
                    $tmpKeyword .= "AND NCPD.NCPD_STOCK_DT BETWEEN '".addslashes($ARR['START_DT_SEARCH'])."' AND '".addslashes($ARR['END_DT_SEARCH'])."' \n";
                }else if($ARR['START_DT_SEARCH'] != '' ){
                    $tmpKeyword .= "AND NCPD.NCPD_STOCK_DT >= '".$ARR['START_DT_SEARCH']."' \n";
                }else if($ARR['END_DT_SEARCH'] != ''){
                    $tmpKeyword .= "AND NCPD.NCPD_STOCK_DT <= '".$ARR['END_DT_SEARCH']."' \n";
                }
            }else if($ARR['DT_KEY_SEARCH'] == 'NCPD_CHANGE_DT'){ // 변동일
                if($ARR['START_DT_SEARCH'] != '' && $ARR['END_DT_SEARCH'] != ''){
                    $tmpKeyword .= "AND DATE_FORMAT(NCPD.NCPD_CHANGE_DT, '%Y-%m-%d') BETWEEN '".addslashes($ARR['START_DT_SEARCH'])."' AND '".addslashes($ARR['END_DT_SEARCH'])."'";
                }else if($ARR['START_DT_SEARCH'] != '' ){
                    $tmpKeyword .= "AND DATE_FORMAT(NCPD.NCPD_CHANGE_DT, '%Y-%m-%d') >= '".$ARR['START_DT_SEARCH']."' \n";
                }else if($ARR['END_DT_SEARCH'] != ''){
                    $tmpKeyword .= "AND DATE_FORMAT(NCPD.NCPD_CHANGE_DT, '%Y-%m-%d') <= '".$ARR['END_DT_SEARCH']."' \n";
                }
            }
		}

		//구분검색
		if($ARR['CSD_ID_SEARCH'] != ''){
            $tmpKeyword .= "AND NG.CSD_ID IN (";
            foreach ($ARR['CSD_ID_SEARCH'] as $index => $CSD_ARRAY) {
                $tmpKeyword .=" '".$CSD_ARRAY."',";
            }
            $tmpKeyword=substr($tmpKeyword, 0, -1); // 마지막 콤마제거
            $tmpKeyword .= ") \n";
        }



		//지역검색
        if($ARR['CRR_ID_SEARCH'] !=  '' || $ARR['CRR_ID_SEARCH'] != null) {

            $tmpKeyword .= "AND (";
			foreach ($ARR['CRR_ID_SEARCH'] as $index => $CRD_ARRAY) {
				$CSD = explode(',', $CRD_ARRAY);
				
				if($index == 0){
					$tmpKeyword .= "(";
					$tmpKeyword .= "`NG`.`CRR_1_ID` = '".$CSD[0]."' \n";
				}else{
					$tmpKeyword .= " OR(";
					$tmpKeyword .= "  `NG`.`CRR_1_ID` = '".$CSD[0]."' \n";
                }
                if($CSD[1] != NOT_CRR_2){
                    $tmpKeyword .= "AND `NG`.`CRR_2_ID` = '".$CSD[1]."' \n";
                }
				
				if($CSD[2] != NOT_CRR_3){
					$tmpKeyword .= "AND `NG`.`CRR_3_ID` = '".$CSD[2]."' \n";
				}
				
				$tmpKeyword .= ") \n";
			}
			$tmpKeyword .= ") \n";
		}
		
		//공업사명검색
		if($ARR['MGG_NM_SEARCH'] != '') {
			$tmpKeyword .= "AND NCPD.MGG_ID  = '".$ARR['MGG_NM_SEARCH']."' \n";
		} 

		//영업담당검색
		if($ARR['WRT_USERID_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(`NT_AUTH`.`GET_AUU_NM`(NCPD.`AUU_ID`), '".$ARR['WRT_USERID_SEARCH']."' ) > 0 \n";
		} 


		//장비구분 검색
		if($ARR['NCC_ID_SEARCH'] !=''){
			$tmpKeyword = "AND NCP.NCC_ID = '".addslashes($ARR['NCC_ID_SEARCH'])."' \n";
		}

		//장비명 검색
		if($ARR['NCP_NM_SEARCH'] !=''){
			$tmpKeyword = "AND `NCPD`.`NCP_ID` = '".addslashes($ARR['NCP_NM_SEARCH'])."' \n";
		}

		//거래여부
		if($ARR['RCT_ID_SEARCH'] != '') {
			$tmpKeyword .= "AND NG.RCT_ID = '".addslashes($ARR['RCT_ID_SEARCH'])."' \n";
		} 

		//상태 검색
		if($ARR['RCS_ID_SEARCH'] !=''){
			$tmpKeyword = "AND NCPD.RCS_ID = '".addslashes($ARR['RCS_ID_SEARCH'])."' \n";
		}



		// 장소 검색
        if($ARR['RCP_ID_SEARCH'] != '') {
            $tmpKeyword .= "AND NCPD.RCP_ID = '".addslashes($ARR['RCP_ID_SEARCH'])."' \n";	
        } 

		// 페이징처리를 위해 전체데이터
		$iSQL = "SELECT
					COUNT(*) AS CNT
				FROM `NT_SALES`.`NSC_CONSIGN_PARTS_DTL` NCPD
				JOIN `NT_SALES`.`NSC_CONSIGN_PARTS` NCP
				ON NCPD.NCP_ID = NCP.NCP_ID 
				LEFT OUTER JOIN `NT_MAIN`.`V_MGG_STATUS` NG 
				ON NG.MGG_ID = NCPD.MGG_ID
				WHERE 1=1
				$tmpKeyword ;
			";
	
		try{
			$RS_T	= DBManager::getRecordSet($iSQL);
		} catch (Exception $e){
			echo "error : ".$e;
			return ERROR_DB_SELECT_DATA;   //ERROR_DB_SELECT_DATA
		}

		$TOTAL_LIST_COUNT = $RS_T['CNT'];
		$IDX = $RS_T['CNT']-$LimitStart;
		unset($RS_T);
		
		// ORDER_SQL
		if(empty($ARR['IS_SORT'])){
			$ORDERBY_SQL = " ORDER BY NCP.NCP_ORDER_NUM, NCPD.NCPD_ID ";   
		}else{
			$ORDERBY_SQL = " ORDER BY ".addslashes($ARR['ORDER_KEY'])." ".addslashes($ARR['IS_SORT'])." "; 
		}

	
		// 리스트 가져오기
		$iSQL = "SELECT 
					NG.CSD_NM,
					`NT_CODE`.`GET_CRR_NM`(NG.CRR_1_ID, NG.CRR_2_ID, NG.CRR_3_ID) AS 'CRR_NM',
					NCPD.`MGG_ID`,
					NG.`MGG_NM`,
					NCPD.`AUU_ID`,
					`NT_AUTH`.`GET_AUU_NM`(NCPD.`AUU_ID`) AS 'AUU_NM',
					NG.RCT_NM,
					NG.MGG_BIZ_START_DT,
					NG.MGG_BIZ_END_DT,
					NCPD.`NCPD_STOCK_DT`,
					NCP.NCC_ID,
					`NT_SALES`.`GET_CONSIGN_TYPE_NM`(NCP.NCC_ID) AS 'NCC_NM',
					NCPD.`NCP_ID`,
					NCP.`NCP_NM`, 
					NCPD.`NCPD_ID`,
					NCP.NCP_MANUFACTURER,
					NCP.NCP_PRICE,
					NCPD.`NCPD_CHANGE_DT`,
					NCPD.`RCS_ID`,
					`NT_CODE`.`GET_CODE_NM`(NCPD.RCS_ID) AS 'RCS_NM', 
					NCPD.`RCP_ID`,
					`NT_CODE`.`GET_CODE_NM`(NCPD.RCP_ID) AS 'RCP_NM', 
					NCPD.`NCPD_MEMO_TXT`,
					DATE_FORMAT(NCPD.`LAST_DTHMS`, '%Y-%m-%d') AS 'LAST_DTHMS',
					NCPD.`WRT_USERID`,
					`NT_AUTH`.`GET_USER_NM`(NCPD.WRT_USERID) AS 'WRT_USER_NM'
				FROM `NT_SALES`.`NSC_CONSIGN_PARTS_DTL` NCPD
				JOIN `NT_SALES`.`NSC_CONSIGN_PARTS` NCP
				ON NCPD.NCP_ID = NCP.NCP_ID 
				LEFT OUTER JOIN `NT_MAIN`.`V_MGG_STATUS` NG 
				ON NG.MGG_ID = NCPD.MGG_ID
				WHERE 1=1
					 $tmpKeyword 
				$ORDERBY_SQL
				$LIMIT_SQL
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
				"CSD_NM"=>$RS['CSD_NM'],
				"CRR_NM"=>$RS['CRR_NM'],
				"MGG_ID"=>$RS['MGG_ID'],
				"MGG_NM"=>$RS['MGG_NM'],
				"AUU_ID"=>$RS['AUU_ID'],
				"AUU_NM"=>$RS['AUU_NM'],
				"RCT_NM"=>$RS['RCT_NM'],
				"MGG_BIZ_START_DT"=>$RS['MGG_BIZ_START_DT'],
				"MGG_BIZ_END_DT"=>$RS['MGG_BIZ_END_DT'],
				"NCPD_STOCK_DT"=>$RS['NCPD_STOCK_DT'],
				"NCC_ID"=>$RS['NCC_ID'],
				"NCC_NM"=>$RS['NCC_NM'],
				"NCP_ID"=>$RS['NCP_ID'],
				"NCP_NM"=>$RS['NCP_NM'], 
				"NCPD_ID"=>$RS['NCPD_ID'],
				"NCP_MANUFACTURER"=>$RS['NCP_MANUFACTURER'],
				"NCP_PRICE"=>$RS['NCP_PRICE'],
				"NCPD_CHANGE_DT"=>$RS['NCPD_CHANGE_DT'],
				"RCS_ID"=>$RS['RCS_ID'],
				"RCS_NM"=>$RS['RCS_NM'], 
				"RCP_ID"=>$RS['RCP_ID'],
				"RCP_NM"=>$RS['RCP_NM'], 
				"NCPD_MEMO_TXT"=>$RS['NCPD_MEMO_TXT'],
				"LAST_DTHMS"=>$RS['LAST_DTHMS'],
				"WRT_USERID"=>$RS['WRT_USERID'],
				"WRT_USER_NM"=>$RS['WRT_USER_NM']
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
					NG.CSD_NM,
					`NT_CODE`.`GET_CRR_NM`(NG.CRR_1_ID, NG.CRR_2_ID, NG.CRR_3_ID) AS 'CRR_NM',
					IFNULL(NCPD.`MGG_ID`,'') AS MGG_ID,
					IFNULL(NG.`MGG_NM`,'') AS MGG_NM,
					IFNULL(NCPD.`AUU_ID`,'') AS AUU_ID,
					IFNULL(`NT_AUTH`.`GET_AUU_NM`(NCPD.`AUU_ID`),'') AS 'AUU_NM',
					NG.RCT_NM,
					NG.MGG_BIZ_START_DT,
					NG.MGG_BIZ_END_DT,
					NCPD.`NCPD_STOCK_DT`,
					NCP.NCC_ID,
					`NT_SALES`.`GET_CONSIGN_TYPE_NM`(NCP.NCC_ID) AS 'NCC_NM',
					NCPD.`NCP_ID`,
					NCP.`NCP_NM`, 
					NCPD.`NCPD_ID`,
					NCP.NCP_MANUFACTURER,
					NCP.NCP_PRICE,
					NCPD.`NCPD_CHANGE_DT`,
					NCPD.`RCS_ID`,
					`NT_CODE`.`GET_CODE_NM`(NCPD.RCS_ID) AS 'RCS_NM', 
					NCPD.`RCP_ID`,
					`NT_CODE`.`GET_CODE_NM`(NCPD.RCP_ID) AS 'RCP_NM', 
					NCPD.`NCPD_MEMO_TXT`,
					DATE_FORMAT(NCPD.`LAST_DTHMS`, '%Y-%m-%d') AS 'LAST_DTHMS',
					NCPD.`WRT_USERID`,
					`NT_AUTH`.`GET_USER_NM`(NCPD.WRT_USERID) AS 'WRT_USER_NM'
				FROM `NT_SALES`.`NSC_CONSIGN_PARTS_DTL` NCPD
				JOIN `NT_SALES`.`NSC_CONSIGN_PARTS` NCP
				ON NCPD.NCP_ID = NCP.NCP_ID 
				LEFT OUTER JOIN `NT_MAIN`.`V_MGG_STATUS` NG 
				ON NG.MGG_ID = NCPD.MGG_ID
				WHERE NCPD.NCP_ID = '".addslashes($ARR['NCP_ID'])."'
				AND NCPD.NCPD_ID = '".addslashes($ARR['NCPD_ID'])."' ;
				";
		// echo $iSQL;
		return DBManager::getRecordSet($iSQL);
		
	}
	




	/**
	 * 데이터생성
	 */
	function setData($ARR) {
		
		if($ARR['MGG_ID'] == ''){
			$MGG = " `MGG_ID` = NULL , "; 
		}else {
			$MGG = " `MGG_ID` = '".addslashes($ARR['MGG_ID'])."', "; 
		}
		if($ARR['AUU_ID'] == ''){
			$AUU = " `AUU_ID` = NULL , "; 
		}else {
			$AUU = " `AUU_ID` = '".addslashes($ARR['AUU_ID'])."', "; 
		}
		if(is_array($ARR)){
			foreach($ARR['NCP_ID'] as $index => $NCP_ID){
				
				$iSQL .= "UPDATE  `NT_SALES`.`NSC_CONSIGN_PARTS_DTL`
							SET 
								$MGG
								$AUU
								`NCPD_CHANGE_DT` = '".addslashes($ARR['NCPD_CHANGE_DT'])."',
								`RCS_ID` = '".addslashes($ARR['RCS_ID'])."',
								`RCP_ID` = '".addslashes($ARR['RCP_ID'])."',
								`NCPD_MEMO_TXT` = '".addslashes($ARR['NCPD_MEMO_TXT'])."',
								`LAST_DTHMS` = now(),
								`WRT_USERID` = '".addslashes($_SESSION['USERID'])."'
							WHERE `NCP_ID`='".addslashes($ARR['NCP_ID'][$index])."' 
							AND `NCPD_ID`='".addslashes($ARR['NCPD_ID'][$index])."'  ;
					";

			}
			// echo $iSQL;
			return DBManager::execMulti($iSQL);
		}

	}

	/** 공업사정보(청구담당자 정보도 포함) */
	function getGarageInfo(){
		$iSQL = "SELECT 
					MGG.MGG_NM,
					MGG.MGG_ID,
					MGGS.MGG_OFFICE_TEL
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
				"MGG_OFFICE_TEL"=>$RS['MGG_OFFICE_TEL']
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

	/** 영업사원 정보 */
	function getSa(){
		$iSQL = "SELECT 
					`AUU_ID`,
					`AUU_NM`,
					`AUU_PHONE_NUM`
				FROM `NT_AUTH`.`NAU_USER`
				WHERE DEL_YN = 'N';
			";

		$Result= DBManager::getResult($iSQL);
		///

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
				"AUU_ID"=>$RS['AUU_ID'],
				"AUU_NM"=>$RS['AUU_NM'],
				"AUU_PHONE_NUM"=>$RS['AUU_PHONE_NUM']
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
	$cReceipt = new cReceipt(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$cReceipt->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
