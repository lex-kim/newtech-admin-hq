<?php

class clsGetWorks extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	// Class Object Constructor
	function clsGetWorks($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
		$this->DATA_SAVE_PATH = $DATA_SAVE_PATH;
		$this->ALLOW_FILE_EXT = $ALLOW_FILE_EXT;
	}

	/**
	 * 리스트조회 (공업사별 보험사 청구건수)
	 */
	function getSubList($ARR) {
		$iSQL = "SELECT 
					NMI.MII_NM,
					(SELECT RIT_NM FROM NT_CODE.REF_INSU_TYPE WHERE RIT_ID = NMI.RIT_ID) AS RIT_NM,
					(
						SELECT
							COUNT(NBI.NB_ID) 
						FROM 
							NT_BILL.NBB_BILL  NB,
							NT_BILL.NBB_BILL_INSU  NBI
						WHERE NBI.MII_ID = NMI.MII_ID
						AND NB.NB_ID = NBI.NB_ID
						AND NB.MGG_ID = '".addslashes($ARR['MGG_ID'])."'
						AND YEAR(NB.NB_CLAIM_DT) = '".addslashes($ARR['CLAIM_YEAR'])."'
					) AS INSU_CLAIM_CNT
				FROM 
					NT_MAIN.NMI_INSU NMI
				WHERE NMI.DEL_YN = 'N'
				GROUP BY NMI.MII_ID
				ORDER BY NMI.MII_ORDER_NUM ASC
			;"

		;
		// echo $iSQL;
		$Result = DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"MII_NM"=>$RS['MII_NM'],
					"RIT_NM"=>$RS['RIT_NM'],
					"INSU_CLAIM_CNT"=>$RS['INSU_CLAIM_CNT'],
			));
		}

		$Result->close();  // 자원 반납
		unset($RS);

		if(empty($rtnArray)){
			return HAVE_NO_DATA;
		}else{
			return $rtnArray;

		}
	}

	/**
	 * 리스트조회
	 */
	function getList($ARR) {
		$LimitStart = ($ARR['CURR_PAGE']-1) * $ARR['PER_PAGE'];
		$LimitEnd   = $ARR['PER_PAGE'];
		$dateSQL = "";
		$tmpKeyword = "";
		
		if($ARR['START_DT'] !='' && $ARR['END_DT'] !=''){
			$tmpKeyword .= "AND DATE(`NB`.`NB_CLAIM_DT`) BETWEEN '".addslashes($ARR['START_DT'])."' AND '".addslashes($ARR['END_DT'])."'";
		}

		if($ARR['CSD_ID_SEARCH'] != '') {
			$tmpKeyword .= "AND MGG.CSD_ID IN ( ";
            foreach ($ARR['CSD_ID_SEARCH'] as $index => $CSD_ID) {
                $tmpKeyword .=" '".$CSD_ID."',";
            }
            $tmpKeyword = substr($tmpKeyword, 0, -1); // 마지막 콤마제거
            $tmpKeyword .= ") \n";
		}
		if($ARR['CRR_ID_SEARCH'] != '') {
			$tmpKeyword .= "AND (";
			foreach ($ARR['CRR_ID_SEARCH'] as $index => $CRD_ARRAY) {
				$CSD = explode(',', $CRD_ARRAY);
				
				if($index == 0){
					$tmpKeyword .= "(";
					$tmpKeyword .= "`MGG`.`CRR_1_ID` = '".$CSD[0]."' \n";
				}else{
					$tmpKeyword .= " OR(";
					$tmpKeyword .= "  `MGG`.`CRR_1_ID` = '".$CSD[0]."' \n";
				}
				if($CSD[1] != NOT_CRR_2){
					$tmpKeyword .= "AND `MGG`.`CRR_2_ID` = '".$CSD[1]."' \n";
				}
				
				if($CSD[2] != NOT_CRR_3){
					$tmpKeyword .= "AND `MGG`.`CRR_3_ID` = '".$CSD[2]."' \n";
				}
				
				$tmpKeyword .= ") \n";
			}
			$tmpKeyword .= ") \n";
		}
		if($ARR['MGG_ID_SEARCH'] != '') {
			$tmpKeyword .= "AND MGG.MGG_ID IN ( ";
            foreach ($ARR['MGG_ID_SEARCH'] as $index => $MGG_ID) {
                $tmpKeyword .=" '".$MGG_ID."',";
            }
            $tmpKeyword = substr($tmpKeyword, 0, -1); // 마지막 콤마제거
            $tmpKeyword .= ") \n";
		}
		if($ARR['RET_ID_SEARCH'] != '') {
			$tmpKeyword .= "AND MGGS.RET_ID IN ( ";
            foreach ($ARR['RET_ID_SEARCH'] as $index => $RET_ID) {
                $tmpKeyword .=" '".$RET_ID."',";
            }
            $tmpKeyword = substr($tmpKeyword, 0, -1); // 마지막 콤마제거
            $tmpKeyword .= ") \n";
		}
		
		if(!empty($ARR['RCT_ID_SEARCH'])){
			$tmpKeyword .= "AND INSTR(`MGGS`.`RCT_ID`,'".addslashes($ARR['RCT_ID_SEARCH'])."')>0 ";
		}

		// 페이징처리를 위해 전체데이터
		$iSQL = "SELECT COUNT(*) AS CNT FROM (SELECT 
					COUNT(MGG.MGG_ID)
				FROM 
					NT_MAIN.NMG_GARAGE_SUB MGGS, 
					NT_MAIN.NMG_GARAGE MGG
				LEFT JOIN NT_BILL.NBB_BILL NB
					ON NB.MGG_ID = MGG.MGG_ID
					AND NB.DEL_YN = 'N' 
				WHERE MGG.DEL_YN = 'N'
				AND MGGS.MGG_ID = MGG.MGG_ID
				$tmpKeyword
				GROUP BY MGG.MGG_ID
				) AS C
				;"
		;
	
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

		$WORKS_ARRAY = $this->getWorks();
		$WORKS_SQL = "";     // 동적으로 보험사
		$SUM_SQL = "";      // 동적으로 생성된 보험사 청구서 건의 총 합

		foreach($WORKS_ARRAY as $index => $WORKS){
			$SUM_SQL .= "+SUM(IF(NBW.NWP_ID = '".$WORKS['RWP_ID']."' , 1 ,0))"."\n";
			$WORKS_SQL .= ",  SUM(IF(NBW.NWP_ID = '".$WORKS['RWP_ID']."' , 1 , 0)) AS WORKS_".$index."\n";
		}

		$iSQL = "SELECT 
					MGG.MGG_ID,
					NT_CODE.GET_CSD_NM(MGG.CSD_ID) AS CSD_NM,
					NT_CODE.GET_CRR_NM(MGG.CRR_1_ID, MGG.CRR_2_ID,'00000') AS CRR_NM,
					MGG.MGG_NM,
					MGG.MGG_BIZ_ID,
					IFNULL( NT_CODE.GET_RCT_NM(MGGS.RCT_ID),'') AS RCT_NM,
					IFNULL( NT_CODE.GET_RET_NM(MGGS.RET_ID),'') AS RET_NM,
					NT_CODE.GET_RCT_NM(MGGS.RCT_ID) AS RCT_NM,
					MGGS.MGG_BIZ_START_DT,
					IFNULL(MGGS.MGG_BIZ_END_DT,'') AS MGG_BIZ_END_DT,
					MGGS.MGG_INSU_MEMO,
					MGGS.MGG_MANUFACTURE_MEMO,
					($SUM_SQL)AS CLAIM_CNT
					$WORKS_SQL
				FROM 
					NT_MAIN.NMG_GARAGE_SUB MGGS, 
					NT_MAIN.NMG_GARAGE MGG
				LEFT JOIN NT_BILL.NBB_BILL NB
					ON NB.MGG_ID = MGG.MGG_ID
					AND NB.DEL_YN = 'N' 
				LEFT JOIN NT_BILL.NBB_BILL_WORK NBW
					ON NBW.NB_ID = NB.NB_ID
				WHERE MGG.DEL_YN = 'N'
				AND MGGS.MGG_ID = MGG.MGG_ID
				$tmpKeyword 
				GROUP BY MGG.MGG_ID
				ORDER BY MGG.MGG_NM ASC
				$LIMIT_SQL 
			;"

		;
		// echo $iSQL;
		$Result = DBManager::getResult($iSQL);

		$rtnArray = array();
		
		$count = 0;
		while($RS = $Result->fetch_array()){
			$worksArray = array();
			foreach($WORKS_ARRAY as $index => $INSU){
				array_push($worksArray,$RS["WORKS_".$index]);
			}
			array_push($rtnArray,
				array(
					"IDX"=>$IDX--,
					"MGG_ID"=>$RS['MGG_ID'],
					"CSD_NM"=>$RS['CSD_NM'],
					"CRR_NM"=>$RS['CRR_NM'],
					"MGG_NM"=>$RS['MGG_NM'],
					"MGG_BIZ_ID"=>$RS['MGG_BIZ_ID'],
					"RET_NM"=>$RS['RET_NM'],
					"RCT_NM"=>$RS['RCT_NM'],
					"MGG_BIZ_START_DT"=>$RS['MGG_BIZ_START_DT'],
					"MGG_BIZ_END_DT"=>$RS['MGG_BIZ_END_DT'],
					"CLAIM_CNT"=>$RS['CLAIM_CNT'],
					"MGG_INSU_MEMO"=>$RS['MGG_INSU_MEMO'],
					"MGG_MANUFACTURE_MEMO"=>$RS['MGG_MANUFACTURE_MEMO'],
					"WORKS_ARRAY" => $worksArray 
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
			array_push($jsonArray,$WORKS_ARRAY);     // 엑셀에서 헤더 동적으로생성하기 위함..
			return $jsonArray;

		}
	}

	/** 해당연도 */
	function getClaimYearOption($ARR){
		$iSQL = "
				SELECT 
					YEAR(NOW()) AS CURR_YEAR,
					YEAR(MIN(NB_CLAIM_DT))AS MIN_YEAR
				FROM NT_BILL.NBB_BILL
				WHERE DEL_YN = 'N'
				AND NB_CLAIM_DT != '0000-00-00'
				;
		";

		$RS= DBManager::getRecordSet($iSQL);	

		$rtnArray = array(
			"CURR_YEAR"=>$RS['CURR_YEAR'],
			"MIN_YEAR"=>$RS['MIN_YEAR'],			
		);
		unset($RS);
		
		return  $rtnArray;
	}

	/** 사용부품 리스트 */
	function getWorks(){
		$iSQL = "SELECT 
					RWP_ID,
					RWP_NM1
				FROM 
					NT_CODE.REF_WORK_PART
				ORDER BY RWP_ID ASC
			;"
		;

		$Result = DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"RWP_ID"=>$RS['RWP_ID'],
					"RWP_NM1"=>$RS['RWP_NM1'],
			));
		}

		return $rtnArray;
	}

	/* 보험사Team 조회  */
	function getInsuTeam() {
		$iSQL = "SELECT 
					MIT_ID, MIT_NM
				FROM NT_MAIN.NMI_INSU_TEAM;
		";

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

	/** 공업사 가져오기 */
	function getGarageInfo(){
		$iSQL = "SELECT 
					`MGG_NM`,
					`MGG_ID`
				FROM `NT_MAIN`.`NMG_GARAGE`
				WHERE `DEL_YN` = 'N'
				ORDER BY MGG_NM
				;"
		;

		$Result= DBManager::getResult($iSQL);
		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"MGG_NM"=>$RS['MGG_NM'],
					"MGG_ID"=>$RS['MGG_ID'],
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

}
	// 0. 객체 생성
	$clsGetWorks = new clsGetWorks(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$clsGetWorks->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
