<?php
class cPointState extends DBManager {
	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자
	// Class Object Constructor
	function cPointState($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
		$this->DATA_SAVE_PATH = $DATA_SAVE_PATH;
		$this->ALLOW_FILE_EXT = $ALLOW_FILE_EXT;
	}
	

	/**
	 * 리스트조회 
	 */
	function getList($ARR) {
		$LimitStart = ($ARR['CURR_PAGE']-1) * $ARR['PER_PAGE'];
		$LimitEnd   = $ARR['PER_PAGE'];


		if($ARR['DT_KEY_SEARCH'] != '') {
			if($ARR['DT_KEY_SEARCH'] == 'MGG_BIZ_START_DT'){ // 개시일
                if($ARR['START_DT_SEARCH'] != '' && $ARR['END_DT_SEARCH'] != ''){
                    $tmpKeyword .= "AND DATE_FORMAT(NGS.MGG_BIZ_START_DT, '%Y-%m-%d') BETWEEN '".addslashes($ARR['START_DT_SEARCH'])."' AND '".addslashes($ARR['END_DT_SEARCH'])."'";
                }else if($ARR['START_DT_SEARCH'] != '' ){
                    $tmpKeyword .= "AND DATE_FORMAT(NGS.MGG_BIZ_START_DT, '%Y-%m-%d') >= '".$ARR['START_DT_SEARCH']."' \n";
                }else if($ARR['END_DT_SEARCH'] != ''){
                    $tmpKeyword .= "AND DATE_FORMAT(NGS.MGG_BIZ_START_DT, '%Y-%m-%d') <= '".$ARR['END_DT_SEARCH']."' \n";
                }
			}else if($ARR['DT_KEY_SEARCH'] == 'MGG_BIZ_END_DT'){ // 종료일
                if($ARR['START_DT_SEARCH'] != '' && $ARR['END_DT_SEARCH'] != ''){
                    $tmpKeyword .= "AND DATE_FORMAT(NGS.MGG_BIZ_END_DT, '%Y-%m-%d') BETWEEN '".addslashes($ARR['START_DT_SEARCH'])."' AND '".addslashes($ARR['END_DT_SEARCH'])."'";
                }else if($ARR['START_DT_SEARCH'] != '' ){
                    $tmpKeyword .= "AND DATE_FORMAT(NGS.MGG_BIZ_END_DT, '%Y-%m-%d') >= '".$ARR['START_DT_SEARCH']."' \n";
                }else if($ARR['END_DT_SEARCH'] != ''){
                    $tmpKeyword .= "AND DATE_FORMAT(NGS.MGG_BIZ_END_DT, '%Y-%m-%d') <= '".$ARR['END_DT_SEARCH']."' \n";
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

		//청구식검색
		if($ARR['RET_ID_SEARCH'] != '') {
			$tmpKeyword .= "AND NGS.RET_ID IN ( ";
            foreach ($ARR['RET_ID_SEARCH'] as $index => $RET_ID) {
                $tmpKeyword .=" '".$RET_ID."',";
            }
            $tmpKeyword = substr($tmpKeyword, 0, -1); // 마지막 콤마제거
            $tmpKeyword .= ") \n";
		}

		if(!empty($ARR['RCT_ID_SEARCH'])){
			$tmpKeyword .= "AND INSTR(`NGS`.`RCT_ID`,'".addslashes($ARR['RCT_ID_SEARCH'])."')>0 ";
		}

		//등급명
		if($ARR['SLL_ID_SEARCH'] != ''){
			$tmpKeyword .="AND NG.SLL_ID = '".addslashes($ARR['SLL_ID_SEARCH'])."' \n";
		}

		//AOS
		if($ARR['RAT_ID_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(`NGS`.`RAT_ID`,'".addslashes($ARR['RAT_ID_SEARCH'])."')>0 ";	
		} 

		//수탁자
		if($ARR['MGG_BAILEE_NM_SEARCH'] != ''){
			$tmpKeyword .="AND INSTR(NGS.`MGG_BAILEE_NM`, '".addslashes($ARR['MGG_BAILEE_NM_SEARCH'])."' ) > 0 \n";
		}

		//공업사 검색
		if($ARR['MGG_NM_SEARCH'] != '') {
			$tmpKeyword .= "AND NG.MGG_ID = '".addslashes($ARR['MGG_NM_SEARCH'])."'  \n";	
		}

		// 페이징처리를 위해 전체데이터
		$iSQL = "SELECT 
					COUNT(*) AS CNT 
				FROM
					`NT_MAIN`.`NMG_GARAGE` NG,
					`NT_MAIN`.`NMG_GARAGE_SUB` NGS,
					`NT_CODE`.`NCS_DIVISION` ND,
					`NT_CODE`.`NCR_REGION` NR,
					`NT_POINT`.`NPP_POINT_BALANCE` NPB
				WHERE
					NG.MGG_ID = NGS.MGG_ID
				AND	NG.CSD_ID = ND.CSD_ID
				AND	NG.`CRR_1_ID` = NR.`CRR_1_ID`
				AND	NG.`CRR_2_ID` = NR.`CRR_2_ID`
				AND	NR.`CRR_3_ID` = '00000'
				AND NG.DEL_YN= 'N'
				AND	NG.MGG_ID = NPB.MGG_ID
				$tmpKeyword ;
		";
	
		// echo $iSQL;
		try{
			$RS_T	= DBManager::getRecordSet($iSQL);
		} catch (Exception $e){
			echo "error : ".$e;
			return ERROR_DB_SELECT_DATA;   //ERROR_DB_SELECT_DATA
		}
		$TOTAL_LIST_COUNT = $RS_T['CNT'];
		$IDX = $RS_T['CNT']-$LimitStart;
		unset($RS_T);

		/** 전체리스트가 아닌경우에만 limitSql 적용 */
		$LIMIT_SQL = "";
		if($ARR['REQ_MODE'] == CASE_LIST){
			$LIMIT_SQL = "LIMIT $LimitStart,$LimitEnd";
		}


		$iSQL = "SELECT
					`CSD_NM` AS `CSD_NM`,
					`CRR_NM` AS `CRR_NM`,
					`MGG_NM` AS `MGG_NM`,
					`MGG_BIZ_ID` AS `MGG_BIZ_ID`,
					`MGG_BIZ_START_DT` AS `MGG_BIZ_START_DT`,
					`MGG_BIZ_END_DT` AS `MGG_BIZ_END_DT`,
					NT_CODE.GET_CODE_NM(`RET_ID`) AS `RET_NM`,
					NT_CODE.GET_RCT_NM(RCT_ID) AS RCT_NM,
					(SELECT `RAT_NM` FROM `NT_CODE`.`REF_AOS_TYPE` WHERE `RAT_ID` = `NGS`.`RAT_ID`) AS `RAT_NM`,
					`MGG_BAILEE_NM`,
					CAST(AES_DECRYPT(`MGG_CEO_NM_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `MGG_CEO_NM_AES`,
					CAST(AES_DECRYPT(`MGG_CEO_TEL_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `MGG_CEO_TEL_AES`,
					CONCAT(`MGG_ADDRESS_NEW_1`, '  ', `MGG_ADDRESS_NEW_2`) AS `MGGS_ADDR`,
					`MGG_OFFICE_TEL` AS `MGG_OFFICE_TEL`,
					`MGG_OFFICE_FAX` AS `MGG_OFFICE_FAX`,
					FORMAT(`NT_STOCK`.`GET_MGG_STOCK_PRICE`(NG.MGG_ID),0) AS `STOCK_PRICE`,
					(
						SELECT FORMAT(IFNULL(SUM(`NPH_POINT`),0),0)
						FROM	`NT_POINT`.`NPP_POINT_HISTORY`	INPH
						WHERE	INPH.MGG_ID = NG.MGG_ID
						AND		`NPH_CANCEL_YN` = 'N'
						AND		RPS_ID = '60110'
					) AS `POINT_60110`,
					(
						SELECT FORMAT(IFNULL(SUM(`NPH_POINT`),0),0)
						FROM	`NT_POINT`.`NPP_POINT_HISTORY`	INPH
						WHERE	INPH.MGG_ID = NG.MGG_ID
						AND		`NPH_CANCEL_YN` = 'Y'
						AND		RPS_ID = '60110'
					) AS `POINT_60110_CANCEL`,
					(
						SELECT FORMAT(IFNULL(SUM(`NPH_POINT`),0),0)
						FROM	`NT_POINT`.`NPP_POINT_HISTORY`	INPH
						WHERE	INPH.MGG_ID = NG.MGG_ID
						AND		`NPH_CANCEL_YN` = 'N'
						AND		RPS_ID = '60119'
					) AS `POINT_60119`,
					(
						SELECT FORMAT(IFNULL(SUM(`NPH_POINT`),0),0)
						FROM	`NT_POINT`.`NPP_POINT_HISTORY`	INPH
						WHERE	INPH.MGG_ID = NG.MGG_ID
						AND		`NPH_CANCEL_YN` = 'Y'
						AND		RPS_ID = '60119'
					) AS `POINT_60119_CANCEL`,
					(
						SELECT FORMAT(IFNULL(SUM(`NPH_POINT`),0),0)
						FROM	`NT_POINT`.`NPP_POINT_HISTORY`	INPH
						WHERE	INPH.MGG_ID = NG.MGG_ID
						AND		`NPH_CANCEL_YN` = 'N'
						AND		RPS_ID = '60120'
					) AS `POINT_60120`,
					(
						SELECT FORMAT(IFNULL(SUM(`NPH_POINT`),0),0)
						FROM	`NT_POINT`.`NPP_POINT_HISTORY`	INPH
						WHERE	INPH.MGG_ID = NG.MGG_ID
						AND		`NPH_CANCEL_YN` = 'Y'
						AND		RPS_ID = '60120'
					) AS `POINT_60120_CANCEL`,
					(
						SELECT FORMAT(IFNULL(SUM(`NPH_POINT`),0),0)
						FROM	`NT_POINT`.`NPP_POINT_HISTORY`	INPH
						WHERE	INPH.MGG_ID = NG.MGG_ID
						AND		`NPH_CANCEL_YN` = 'N'
						AND		RPS_ID = '60129'
					) AS `POINT_60129`,
					(
						SELECT FORMAT(IFNULL(SUM(`NPH_POINT`),0),0)
						FROM	`NT_POINT`.`NPP_POINT_HISTORY`	INPH
						WHERE	INPH.MGG_ID = NG.MGG_ID
						AND		`NPH_CANCEL_YN` = 'Y'
						AND		RPS_ID = '60129'
					) AS `POINT_60129_CANCEL`,
					(
						SELECT FORMAT(IFNULL(SUM(`NPH_POINT`),0),0)
						FROM	`NT_POINT`.`NPP_POINT_HISTORY`	INPH
						WHERE	INPH.MGG_ID = NG.MGG_ID
						AND		`NPH_CANCEL_YN` = 'N'
						AND		RPS_ID = '60190'
					) AS `POINT_60190`,
					(
						SELECT FORMAT(IFNULL(SUM(`NPH_POINT`),0),0)
						FROM	`NT_POINT`.`NPP_POINT_HISTORY`	INPH
						WHERE	INPH.MGG_ID = NG.MGG_ID
						AND		`NPH_CANCEL_YN` = 'Y'
						AND		RPS_ID = '60190'
					) AS `POINT_60190_CANCEL`,
					(
						SELECT FORMAT(IFNULL(SUM(`NPH_POINT`),0),0)
						FROM	`NT_POINT`.`NPP_POINT_HISTORY`	INPH
						WHERE	INPH.MGG_ID = NG.MGG_ID
						AND		`NPH_CANCEL_YN` = 'N'
						AND		RPS_ID = '60199'
					) AS `POINT_60199`,
					(
						SELECT FORMAT(IFNULL(SUM(`NPH_POINT`),0),0)
						FROM	`NT_POINT`.`NPP_POINT_HISTORY`	INPH
						WHERE	INPH.MGG_ID = NG.MGG_ID
						AND		`NPH_CANCEL_YN` = 'Y'
						AND		RPS_ID = '60199'
					) AS `POINT_60199_CANCEL`,
					FORMAT(`NPB_POINT`,0) AS `NPB_POINT`,
					NG.SLL_ID,
					NT_POINT.GET_LVL_NM(NG.SLL_ID) AS 'SLL_NM'
				FROM
					`NT_MAIN`.`NMG_GARAGE` NG,
					`NT_MAIN`.`NMG_GARAGE_SUB` NGS,
					`NT_CODE`.`NCS_DIVISION` ND,
					`NT_CODE`.`NCR_REGION` NR,
					`NT_POINT`.`NPP_POINT_BALANCE` NPB
				WHERE
					NG.MGG_ID = NGS.MGG_ID
				AND	NG.CSD_ID = ND.CSD_ID
				AND	NG.`CRR_1_ID` = NR.`CRR_1_ID`
				AND	NG.`CRR_2_ID` = NR.`CRR_2_ID`
				AND	NR.`CRR_3_ID` = '00000'
				AND	NG.MGG_ID = NPB.MGG_ID
				AND NG.DEL_YN= 'N'
				$tmpKeyword
				ORDER BY `MGG_NM` ASC
				$LIMIT_SQL ;
		";
		// echo $iSQL;
		$Result = DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"IDX"=>$IDX--,
					"CSD_NM"=>$RS['CSD_NM'], 
					"CRR_NM"=>$RS['CRR_NM'], 
					"MGG_NM"=>$RS['MGG_NM'], 
					"MGG_BIZ_ID"=>$RS['MGG_BIZ_ID'], 
					"MGG_BIZ_START_DT"=>$RS['MGG_BIZ_START_DT'], 
					"MGG_BIZ_END_DT"=>$RS['MGG_BIZ_END_DT'], 
					"RET_NM"=>$RS['RET_NM'], 
					"RCT_NM"=>$RS['RCT_NM'], 
					"RAT_NM"=>$RS['RAT_NM'], 
					"MGG_BAILEE_NM"=>$RS['MGG_BAILEE_NM'], 
					"MGG_CEO_NM_AES"=>$RS['MGG_CEO_NM_AES'], 
					"MGG_CEO_TEL_AES"=>$RS['MGG_CEO_TEL_AES'], 
					"MGGS_ADDR"=>$RS['MGGS_ADDR'], 
					"MGG_OFFICE_TEL"=>$RS['MGG_OFFICE_TEL'], 
					"MGG_OFFICE_FAX"=>$RS['MGG_OFFICE_FAX'], 
					"STOCK_PRICE"=>$RS['STOCK_PRICE'], 
					"POINT_60110"=>$RS['POINT_60110'], 
					"POINT_60110_CANCEL"=>$RS['POINT_60110_CANCEL'], 
					"POINT_60119"=>$RS['POINT_60119'], 
					"POINT_60119_CANCEL"=>$RS['POINT_60119_CANCEL'], 
					"POINT_60120"=>$RS['POINT_60120'], 
					"POINT_60120_CANCEL"=>$RS['POINT_60120_CANCEL'], 
					"POINT_60129"=>$RS['POINT_60129'], 
					"POINT_60129_CANCEL"=>$RS['POINT_60129_CANCEL'], 
					"POINT_60190"=>$RS['POINT_60190'], 
					"POINT_60190_CANCEL"=>$RS['POINT_60190_CANCEL'], 
					"POINT_60199"=>$RS['POINT_60199'], 
					"POINT_60199_CANCEL"=>$RS['POINT_60199_CANCEL'], 
					"NPB_POINT"=>$RS['NPB_POINT'],
					"SLL_ID"=>$RS['SLL_ID'],
					"SLL_NM"=>$RS['SLL_NM']
			));
		}

		$Result->close();  // 자원 반납
		unset($RS);

		if(empty($rtnArray)){
			return HAVE_NO_DATA;
		}else{
			$jsonArray = array();
			array_push($jsonArray,$rtnArray);
			array_push($jsonArray,$TOTAL_LIST_COUNT);
			return $jsonArray;

		}
	}


	/** 등급검색 */
	function getData($ARR) {
		$iSQL = "SELECT SLL_ID, SLL_NM FROM NT_POINT.NPL_LEVEL;
		";

		try{
			$Result = DBManager::getResult($iSQL);
		} catch (Exception $e){
			return ERROR_DB_SELECT_DATA;
		}

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"SLL_ID"=>$RS['SLL_ID'], 
					"SLL_NM"=>$RS['SLL_NM']
				));
		}
		$Result->close();		// 자원 반납
		unset($RS);

		// $rtnJSON = json_encode($rtnArray);
		return $rtnArray;
	}

}
	// 0. 객체 생성
	$cPointState = new cPointState(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$cPointState->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);
?>