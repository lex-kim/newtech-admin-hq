<?php

class clsGwBillNumber extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	// Class Object Constructor
	function clsGwBillNumber($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
		$this->DATA_SAVE_PATH = $DATA_SAVE_PATH;
		$this->ALLOW_FILE_EXT = $ALLOW_FILE_EXT;
	}

	/**
	 * 리스트조회
	 */
	function getList($ARR) {
		$LimitStart = ($ARR['CURR_PAGE']-1) * $ARR['PER_PAGE'];
		$LimitEnd   = $ARR['PER_PAGE'];
		$tmpKeyword = "";
		
		if($ARR['CSD_ID_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(`MGG`.`CSD_ID`,'".addslashes($ARR['CSD_ID_SEARCH'])."')>0 ";
		}
		if($ARR['CRR_ID_SEARCH'] != '') {
			$CRR_ID = str_replace(",",'',$ARR['CRR_ID_SEARCH']);

			$tmpKeyword .= "AND  CONCAT(MGG.CRR_1_ID, MGG.CRR_2_ID,'00000') = '".$CRR_ID."'";
		}
		if($ARR['MGG_ID_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(`MGG`.`MGG_ID`,'".addslashes($ARR['MGG_ID_SEARCH'])."')>0 ";
		}
		if($ARR['RET_ID_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(`MGGS`.`RET_ID`,'".addslashes($ARR['RET_ID_SEARCH'])."')>0 ";
		}
		if($ARR['RCT_ID_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(`MGGS`.`RCT_ID`,'".addslashes($ARR['RCT_ID_SEARCH'])."')>0 ";
		}
		
		// 페이징처리를 위해 전체데이터
		$iSQL = "SELECT COUNT(*) AS CNT FROM (
					SELECT 
						COUNT(MGG.MGG_ID)
					FROM 
						NT_MAIN.NMG_GARAGE_SUB MGGS, 
						NT_MAIN.NMG_GARAGE MGG
					LEFT JOIN NT_BILL.NBB_BILL NB
						ON NB.MGG_ID = MGG.MGG_ID
						AND NB.DEL_YN = 'N' 
						AND YEAR(NB.NB_CLAIM_DT) = '".addslashes($ARR['CLAIM_YEAR'])."'
					WHERE MGG.DEL_YN = 'N'
					AND MGGS.MGG_ID = MGG.MGG_ID
					$tmpKeyword 
					GROUP BY MGG.MGG_ID
				) AS C
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

		/** 전체리스트가 아닌경우에만 limitSql 적용 */
		$LIMIT_SQL = "";
		if($ARR['REQ_MODE'] == CASE_LIST){
			$LIMIT_SQL = "LIMIT $LimitStart,$LimitEnd";
		}

		
		if(empty($ARR['IS_SORT'])){
			$ORDERBY_SQL = "ORDER BY MGGS.MGG_BIZ_START_DT DESC, MGG.MGG_ID";   
		}else{
			if($ARR['IS_SORT'] === '2'){
				if(empty($ARR['MONTH'])){
					$ORDERBY_SQL = "ORDER BY DATEDIFF(DATE_FORMAT(NOW(),'%Y-%m-%d'), MAX_CLAIM_DT) DESC";
				}else{
					$ORDERBY_SQL = "ORDER BY CLAIM_CNT_".$ARR['MONTH']." DESC";
				}
				
			}else{
				if(empty($ARR['MONTH'])){
					$ORDERBY_SQL = "ORDER BY DATEDIFF(DATE_FORMAT(NOW(),'%Y-%m-%d'), MAX_CLAIM_DT) ASC";
				}else{
					$ORDERBY_SQL = "ORDER BY CLAIM_CNT_".$ARR['MONTH']." ASC";
				}
			}
		}

		$iSQL = "SELECT 
					MGG.MGG_ID,
					NT_CODE.GET_CSD_NM(MGG.CSD_ID) AS CSD_NM,
					NT_CODE.GET_CRR_NM(MGG.CRR_1_ID, MGG.CRR_2_ID,'00000') AS CRR_NM,
					MGG.MGG_NM,
					MGG.MGG_BIZ_ID,
					IFNULL( NT_CODE.GET_RET_NM(MGGS.RET_ID),'') AS RET_NM,
					NT_CODE.GET_RCT_NM(MGGS.RCT_ID) AS RCT_NM,
					MGGS.MGG_BIZ_START_DT,
					IFNULL(MGGS.MGG_BIZ_END_DT,'') AS MGG_BIZ_END_DT,
					(SELECT MAX(NB_CLAIM_DT) FROM NT_BILL.NBB_BILL  WHERE MGG_ID = MGG.MGG_ID)AS MAX_CLAIM_DT,
					IFNULL(DATEDIFF(DATE_FORMAT(NOW(),'%Y-%m-%d'), (SELECT MAX(NB_CLAIM_DT) FROM NT_BILL.NBB_BILL  WHERE MGG_ID = MGG.MGG_ID AND DEL_YN = 'N') ),'-')  AS NB_CLAIM_DT,
					-- CONCAT(IFNULL(MAX(NB.NB_CLAIM_DT) ,''), ' ~ ' , DATE_FORMAT(NOW(),'%Y-%m-%d') )  AS NB_CLAIM_DT,
					MGGS.MGG_OFFICE_FAX,
					CAST(AES_DECRYPT(MGGS.MGG_CLAIM_NM_AES, UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `MGG_CLAIM_NM_AES`, 
					CAST(AES_DECRYPT(MGGS.MGG_CLAIM_TEL_AES, UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `MGG_CLAIM_TEL_AES`, 
					IFNULL(NT_STOCK.GET_STOCK_DATA(MGG.MGG_ID,'P'),0) AS STOCK_PRICE,
					ROUND(IFNULL(NT_STOCK.GET_STOCK_DATA(MGG.MGG_ID,'R'),0),2) AS STOCK,
					ROUND((COUNT(NB.NB_ID)/12),2) AS CLAIM_AVG,
					COUNT(NB.NB_ID) AS CLAIM_CNT,
					SUM(IF(MONTH(NB.NB_CLAIM_DT) = '12' , 1 , 0)) AS CLAIM_CNT_12,
					SUM(IF(MONTH(NB.NB_CLAIM_DT) = '11' , 1 , 0)) AS CLAIM_CNT_11,
					SUM(IF(MONTH(NB.NB_CLAIM_DT) = '10' , 1 , 0)) AS CLAIM_CNT_10,
					SUM(IF(MONTH(NB.NB_CLAIM_DT) = '9' , 1 , 0)) AS CLAIM_CNT_9,
					SUM(IF(MONTH(NB.NB_CLAIM_DT) = '8' , 1 , 0)) AS CLAIM_CNT_8,
					SUM(IF(MONTH(NB.NB_CLAIM_DT) = '7' , 1 , 0)) AS CLAIM_CNT_7,
					SUM(IF(MONTH(NB.NB_CLAIM_DT) = '6' , 1 , 0)) AS CLAIM_CNT_6,
					SUM(IF(MONTH(NB.NB_CLAIM_DT) = '5' , 1 , 0)) AS CLAIM_CNT_5,
					SUM(IF(MONTH(NB.NB_CLAIM_DT) = '4' , 1 , 0)) AS CLAIM_CNT_4,
					SUM(IF(MONTH(NB.NB_CLAIM_DT) = '3' , 1 , 0)) AS CLAIM_CNT_3,
					SUM(IF(MONTH(NB.NB_CLAIM_DT) = '2' , 1 , 0)) AS CLAIM_CNT_2,
					SUM(IF(MONTH(NB.NB_CLAIM_DT) = '1' , 1 , 0)) AS CLAIM_CNT_1,
					MGGS.MGG_INSU_MEMO,
					MGGS.MGG_MANUFACTURE_MEMO
					
				FROM 
					NT_MAIN.NMG_GARAGE_SUB MGGS, 
					NT_MAIN.NMG_GARAGE MGG
				LEFT JOIN NT_BILL.NBB_BILL NB
					ON NB.MGG_ID = MGG.MGG_ID
					AND NB.DEL_YN = 'N' 
					AND YEAR(NB.NB_CLAIM_DT) = '".addslashes($ARR['CLAIM_YEAR'])."'
				WHERE MGG.DEL_YN = 'N'
				AND MGGS.MGG_ID = MGG.MGG_ID
				$tmpKeyword 
				GROUP BY MGG.MGG_ID
				$ORDERBY_SQL
				$LIMIT_SQL 
			;"

		;
		// echo $iSQL;
		$Result = DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
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
					"NB_CLAIM_DT"=>$RS['NB_CLAIM_DT'],
					"MGG_OFFICE_FAX"=>$RS['MGG_OFFICE_FAX'],
					"MGG_CLAIM_NM_AES"=>$RS['MGG_CLAIM_NM_AES'],
					"MGG_CLAIM_TEL_AES"=>$RS['MGG_CLAIM_TEL_AES'],
					"STOCK_PRICE"=>$RS['STOCK_PRICE'],
					"STOCK"=>$RS['STOCK'],
					"CLAIM_AVG"=>$RS['CLAIM_AVG'],
					"CLAIM_CNT"=>$RS['CLAIM_CNT'],
					"CLAIM_CNT_12"=>$RS['CLAIM_CNT_12'],
					"CLAIM_CNT_11"=>$RS['CLAIM_CNT_11'],
					"CLAIM_CNT_10"=>$RS['CLAIM_CNT_10'],
					"CLAIM_CNT_9"=>$RS['CLAIM_CNT_9'],
					"CLAIM_CNT_8"=>$RS['CLAIM_CNT_8'],
					"CLAIM_CNT_7"=>$RS['CLAIM_CNT_7'],
					"CLAIM_CNT_6"=>$RS['CLAIM_CNT_6'],
					"CLAIM_CNT_5"=>$RS['CLAIM_CNT_5'],
					"CLAIM_CNT_4"=>$RS['CLAIM_CNT_4'],
					"CLAIM_CNT_3"=>$RS['CLAIM_CNT_3'],
					"CLAIM_CNT_2"=>$RS['CLAIM_CNT_2'],
					"CLAIM_CNT_1"=>$RS['CLAIM_CNT_1'],
					"MGG_INSU_MEMO"=>$RS['MGG_INSU_MEMO'],
					"MGG_MANUFACTURE_MEMO"=>$RS['MGG_MANUFACTURE_MEMO']
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

	function delData($ARR){
		if(!empty($ARR['NB_DEL_REASON'])){
			foreach($ARR['NB_ID'] as $index => $NB_ID){
				$SESSION_ID = $_SESSION['USERID'] == null ? 'SYSYTEM' : $_SESSION['USERID'];
				$iSQL .= "UPDATE NT_BILL.NBB_BILL
						  SET 
						  	DEL_YN = 'Y',
							NB_DEL_REASON = '".addslashes($ARR['NB_DEL_REASON'])."',
							NB_DEL_DTHMS = now(),
							NB_DEL_USERID = '".addslashes($SESSION_ID)."'
						  WHERE NB_ID = '".addslashes($NB_ID)."'
						;"
				;
				$iSQL .= "UPDATE NT_POINT.NPP_POINT_HISTORY
						SET 
						NPH_CANCEL_YN = 'Y',
						NPH_CANCEL_DTHMS = now(),
						NPH_CANCEL_MEMO = '청구서 삭제',
						NPH_CANCEL_USERID = '".addslashes($SESSION_ID)."'
						WHERE NB_ID = '".addslashes($NB_ID)."'
					;"
				;
			}
			
			return DBManager::execMulti($iSQL);
		}
	}

	/**
	 * 해당데이터 조회 
	 * @param {$ARR}
	 * @param {ALLM_ID : 조회할 해당 데이터 ID}
	 */
	function getData($ARR) {
		
	
	}



	/**
	 * 
	 */
	function setData($ARR) {
		if($ARR['REQ_MODE'] == CASE_CREATE){
			
		}else{
			
		}	
	}

	/** 팀재분류메모 */
	function setInsuTeamMemo($ARR){
		if(!empty($ARR['NB_ID'])){
			foreach($ARR['NB_ID'] as $index =>$NB_ID){
				$iSQL .= " UPDATE NT_BILL.NBB_BILL_INSU 
						   SET NBI_TEAM_MEMO = '".addslashes($ARR['NBI_TEAM_MEMO'])."'
						   WHERE NB_ID = '".addslashes($NB_ID)."'
						;"
				;
			}
		}

		return DBManager::execMulti($iSQL);
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
	$clsGwBillNumber = new clsGwBillNumber(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$clsGwBillNumber->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
