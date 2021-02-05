
<?php
class cDeposit extends DBManager {
	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자
	// Class Object Constructor
	function cDeposit($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
		$this->DATA_SAVE_PATH = $DATA_SAVE_PATH;
		$this->ALLOW_FILE_EXT = $ALLOW_FILE_EXT;
    }


	/* 보험사Team 조회  */
	function getInsuTeam() {
		$iSQL = "SELECT 
					MIT_ID, MIT_NM
				FROM NT_MAIN.NMI_INSU_TEAM;
		";
		// echo $iSQL;
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
			return $rtnArray;
		}
	}
	

	/* 보상담당자 조회  */
	function getFao() {
		$iSQL = "SELECT 
					NIC_ID, 
					CAST(AES_DECRYPT(`NIC_NM_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS NIC_NM_AES
				FROM NT_MAIN.NMI_INSU_CHARGE;
		";
		// echo $iSQL;
		$Result= DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"NIC_ID"=>$RS['NIC_ID'],
					"NIC_NM_AES"=>$RS['NIC_NM_AES']
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

	/* 청구요청자 조회  */
	function getClaimId() {
		$iSQL = "SELECT
					distinct(USERID) AS USERID,
					USER_NM
				FROM NT_MAIN.V_ALL_USER_INFO VAL,
				NT_BILL.NBB_BILL NB
				WHERE VAL.USERID = NB.NB_CLAIM_USERID;
		";
		// echo $iSQL;
		$Result= DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"USERID"=>$RS['USERID'],
					"USER_NM"=>$RS['USER_NM']
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


	/* 개척자 조회  개척자가 많아 선택/입력으로 사용하기 버거워 입력값으로 변경, 검수후 요청 가능성이 있어 쿼리만 주석처리*/
	// function getFortier() {
	// 	$iSQL = "SELECT 
	// 				DISTINCT(MGG_FRONTIER_NM) 
	// 				FROM NT_MAIN.NMG_GARAGE_SUB; 
	// 	";
	// 	// echo $iSQL;
	// 	$Result= DBManager::getResult($iSQL);

	// 	$rtnArray = array();
	// 	while($RS = $Result->fetch_array()){
	// 		array_push($rtnArray,
	// 			array(
	// 				"MGG_FRONTIER_NM"=>$RS['MGG_FRONTIER_NM']
	// 		));
	// 	}
	// 	$Result->close();  // 자원 반납
	// 	unset($RS);

	// 	if(empty($rtnArray)) {
	// 		return HAVE_NO_DATA;
	// 	} else {
	// 		return $rtnArray;
	// 	}
	// }

	/** 차량타입 가져오기 */
	function getCarType(){
		$iSQL = "SELECT 
					`RCT_CD`,
					`RCT_NM`
				FROM `NT_CODE`.`REF_CAR_TYPE`
				;"
		;
		$Result= DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
				"RCT_CD"=>$RS['RCT_CD'],
				"RCT_NM"=>$RS['RCT_NM']
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



	/** 부품타입 가져오기 */
	function getPartsType(){
		$iSQL = "SELECT 
						CPPS.CPPS_ID,
						CPP.CPP_NM_SHORT,
						CPPS.CPPS_NM,
						CPPS.CPPS_NM_SHORT
				FROM 
					NT_CODE.NCP_PART  CPP,
					NT_CODE.NCP_PART_SUB CPPS
				WHERE  CPP.DEL_YN = 'N'
				AND CPP.CPP_USE_YN = 'Y'
				AND CPPS.DEL_YN = 'N'
				AND CPPS.CPP_ID = CPP.CPP_ID
				ORDER BY CPP.CPP_ORDER_NUM ASC , CPPS.CPPS_ORDER_NUM ASC
				;"
		;
		$Result= DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(				
					"CPPS_ID"=>$RS['CPPS_ID'],
					"CPP_NM_SHORT"=>$RS['CPP_NM_SHORT'],
					"CPPS_NM"=>$RS['CPPS_NM'],
					"CPPS_NM_SHORT"=>$RS['CPPS_NM_SHORT']
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


	/* 입금여부상세 조회  */
	function getDepositDetail() {
		$iSQL = "SELECT 
					RDT_ID, RDT_NM, RDT_EXT1_YN, RDT_EXT2_YN
				FROM NT_CODE.REF_DEPOSIT_TYPE;
		";

		$Result= DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"ID"=>$RS['RDT_ID'],
					"NM"=>$RS['RDT_NM'],
					"RDT_EXT1_YN"=>$RS['RDT_EXT1_YN'],
					"RDT_EXT2_YN"=>$RS['RDT_EXT2_YN']
			));
		}
		$Result->close();  // 자원 반납
		unset($RS);

		return $rtnArray;
		
	}

	/* 지급제외 사유 조회 */
	function getExclude() {
		$iSQL = "SELECT 
				RER_ID, RER_NM, RER_EXT1_YN, RER_EXT2_YN, RER_EXT3_YN, RER_EXT4_YN, RER_EXT5_YN
			FROM NT_CODE.REF_EXCLUDE_REASON;
		";
		// echo $iSQL;
		$Result= DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"ID"=>$RS['RER_ID'],
					"NM"=>$RS['RER_NM'],
					"EXT1_YN"=>$RS['RER_EXT1_YN'],
					"EXT2_YN"=>$RS['RER_EXT2_YN'],
					"EXT3_YN"=>$RS['RER_EXT3_YN'],
					"EXT4_YN"=>$RS['RER_EXT4_YN'],
					"EXT5_YN"=>$RS['RER_EXT5_YN']
			));
		}
		$Result->close();  // 자원 반납
		unset($RS);

		return $rtnArray;
		
	}

	/* 감액 사유 조회 */
	function getDecrement() {
		$iSQL = "SELECT 
					RDR_ID, 
					RDR_NM, 
					RDR_EXT1_YN, 
					RDR_EXT2_YN, 
					RDR_EXT3_YN, 
					RDR_EXT4_YN, 
					RDR_EXT5_YN
				FROM NT_CODE.REF_DECREMENT_REASON;
		";

		$Result= DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"ID"=>$RS['RDR_ID'],
					"NM"=>$RS['RDR_NM'],
					"EXT1_YN"=>$RS['RDR_EXT1_YN'],
					"EXT2_YN"=>$RS['RDR_EXT2_YN'],
					"EXT3_YN"=>$RS['RDR_EXT3_YN'],
					"EXT4_YN"=>$RS['RDR_EXT4_YN'],
					"EXT5_YN"=>$RS['RDR_EXT5_YN']
			));
		}
		$Result->close();  // 자원 반납
		unset($RS);

		return $rtnArray;
		
	}

	/* 보험사 보상담당자관리 */
	function getInsuChargeInfo($ARR){
		$iSQL = "SELECT
					NIC.NIC_ID,
					CAST(AES_DECRYPT(NIC.NIC_NM_AES,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `NIC_NM_AES`,
					CAST(AES_DECRYPT(NIC.NIC_FAX_AES,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `NIC_FAX_AES`,
					CAST(AES_DECRYPT(NIC.NIC_TEL1_AES,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `NIC_TEL1_AES`,
					SUBSTR(NIC.WRT_DTHMS,1,10) AS WRT_DTHMS,
                    NICT.RICT_ID,
                    IFNULL(NT_CODE.GET_CODE_NM(NICT.RICT_ID),'-') AS RICT_NM
				FROM NT_MAIN.NMI_INSU_CHARGE NIC
                LEFT OUTER JOIN NT_MAIN.NMI_INSU_CHARGE_TYPE NICT
                ON NIC.NIC_ID = NICT.NIC_ID
				WHERE NIC.MII_ID = '".addslashes($ARR['MII_ID'])."'
				AND DEL_YN = 'N'
				GROUP BY NIC.NIC_ID
				;
		";

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
					"WRT_DTHMS"=>$RS['WRT_DTHMS'],
					"RICT_ID"=>$RS['RICT_ID'],
					"RICT_NM"=>$RS['RICT_NM'],
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
	 * 조회 
	 * @param {$ARR}
	 * @param {CURR_PAGE : 현재페이지, PER_PAGE : 몇개를 조회할 것인지, KEYWORD : 검색조건}
	 */
	function getList($ARR) {
		$LimitStart = ($ARR['CURR_PAGE']-1) * $ARR['PER_PAGE'];
		$LimitEnd   = $ARR['PER_PAGE'];
		
		$tmpKeyword = $this->getSqlWhere($ARR); 
		$subTable =  $this->getSubTable($ARR); 
		$groupSQL =  $this->getGroup($ARR); 

		// DC율 검색
		// $DC_METHOD = "ROUND(( 100 - ( ( IFNULL(NBI.`NBI_INSU_DEPOSIT_PRICE`,0) + IFNULL(`NBI_OTHER_PRICE`,0) + IFNULL(`NBI_CAROWN_DEPOSIT_PRICE`,0)) / ($NB_RATIO_PRICE * IF(NB.`NB_VAT_DEDUCTION_YN` = 'Y', 0.9,1)*IF(NB.`NB_NEW_INSU_YN` = 'Y',0.8,1) )* 100) ),2) ";
		if($ARR['START_DC_SEARCH'] != '' && $ARR['END_DC_SEARCH'] != ''){ // 등록일
			$whereDC = " AND DC_RATIO BETWEEN '".addslashes($ARR['START_DC_SEARCH'])."' AND '".addslashes($ARR['END_DC_SEARCH'])."' \n";
		}else if($ARR['START_DC_SEARCH'] != '' ){
			$whereDC = " AND  DC_RATIO >= '".$ARR['START_DC_SEARCH']."' \n";
		}else if($ARR['END_DC_SEARCH'] != ''){
			$whereDC = " AND DC_RATIO <= '".$ARR['END_DC_SEARCH']."' \n";
		}

		$sqlCnt = $this->getListCnt($subTable,$tmpKeyword,$groupSQL);
		// 페이징처리를 위해 전체데이터
		// if($ARR['WORK_TYPE_SEARCH'] != '' || $whereDC != ''){
			$iSQL = "SELECT COUNT(NB_ID)AS CNT
						FROM ($sqlCnt ) AS C 
						WHERE 1=1 $whereDC;
			";
		// }else{
		// 	$iSQL = $sqlCnt.";";
		// }
		
		// echo "getList CNT >>>".$iSQL;
		try{
			$RS_T	= DBManager::getRecordSet($iSQL);
		} catch (Exception $e){
			echo "error : ".$e;
			return ERROR_DB_SELECT_DATA;   //ERROR_DB_SELECT_DATA
		}
	
		
		$TOTAL_LIST_COUNT = $RS_T['CNT'];         // 총 몇개인지
		$IDX = $RS_T['CNT']-$LimitStart;          // 해당페이지 index
		unset($RS_T);

		/** 전체리스트가 아닌경우에만 limitSql 적용 */
		$LIMIT_SQL = "";
		$ODER_SQL = " ORDER BY `NB_CLAIM_DTHMS` DESC  ";
		if($ARR['REQ_MODE'] == CASE_LIST){
			$LIMIT_SQL = "LIMIT $LimitStart,$LimitEnd";
		}

		
		
		// 리스트 가져오기
		
		if($whereDC != ''){
			$iSQL = $this->getListSql($subTable).$tmpKeyword.$groupSQL;
			$iSQL =  "SELECT * FROM (".$iSQL.") AS TMP WHERE 1=1 ".$whereDC.$ODER_SQL.$LIMIT_SQL;
		}else {
			$iSQL = $this->getListSql($subTable).$tmpKeyword.$groupSQL.$ODER_SQL.$LIMIT_SQL." ";

		}
		// echo $iSQL ;  
		$Result= DBManager::getResult($iSQL);

		$rtnArray = $this->getListColumn($Result, $IDX );
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
	 * getList페이징 처리를 위한 쿼리만 리턴해주는 함수. 공용으로 사용하기 위해
	 */
	function getListCnt($subTable,$tmpKeyword,$groupSQL){
		$iSQL = "SELECT
					NBI.NB_ID,
					ROUND(( 100 - ( ( IFNULL(NBI.`NBI_INSU_DEPOSIT_PRICE`,0) + IFNULL(`NBI_INSU_ADD_DEPOSIT_PRICE`,0) + IFNULL(`NBI_CAROWN_DEPOSIT_PRICE`,0)) / (TRUNCATE(IFNULL(NB.`NB_TOTAL_PRICE`,'0')*IF(NBI.`NBI_RATIO`<>'',CAST(NBI.`NBI_RATIO` AS DECIMAL)/100,1),-1) * IF(NB.`NB_VAT_DEDUCTION_YN` = 'Y', 0.9,1)*IF(NB.`NB_NEW_INSU_YN` = 'Y',0.8,1) )* 100) ),2) AS 'DC_RATIO'
				FROM
					`NT_BILL`.`NBB_BILL` NB,
					`NT_BILL`.`NBB_BILL_INSU` NBI,
					`NT_MAIN`.`NMG_GARAGE` NG,
					`NT_MAIN`.`NMG_GARAGE_SUB` NGS
					$subTable
				WHERE
					NB.`NB_ID` = NBI.`NB_ID`
				AND	NB.`MGG_ID` = NG.`MGG_ID`
				AND	NB.`MGG_ID` = NGS.`MGG_ID`
				AND	NB.DEL_YN = 'N'
				AND NG.DEL_YN = 'N'
				$tmpKeyword
				$groupSQL
		";
		return $iSQL;
	}


    /**
	 * getList 쿼리만 리턴해주는 함수. 공용으로 사용하기 위해
	 */
	function getListSql($subTable){	
		$NB_RATIO_PRICE = "TRUNCATE(IFNULL(NB.`NB_TOTAL_PRICE`,'0')*IF(NBI.`NBI_RATIO`<>'',CAST(NBI.`NBI_RATIO` AS DECIMAL)/100,1),-1)" ; 

		$iSQL ="SELECT
					NB.`NB_ID` ,
					NBI.`NBI_RMT_ID`,
					NG.`MGG_ID` AS 'MGG_ID',
					NB.`NB_CLAIM_DT` AS 'NB_CLAIM_DT',
					`NB`.`NB_CLAIM_DTHMS`,
					NBI.`MII_ID`,
					NBI.`NIC_ID`,
					(SELECT MII_NM FROM `NT_MAIN`.NMI_INSU WHERE MII_ID = NBI.MII_ID)AS `MII_NM`,
					`NT_MAIN`.`GET_INSU_CHG_NM`(NBI.NIC_ID, '".AES_SALT_KEY."') AS 'NIC_NM',
					IFNULL(`NT_MAIN`.`GET_INSU_CHG_TEAM_NM`(NBI.NIC_ID),IFNULL(NT_MAIN.GET_MIT_NM_BY_CRR(NBI.MII_ID, NG.MGG_ID),'-')) AS 'MIT_NM',
					NBI.`NBI_TEAM_MEMO` AS 'NBI_TEAM_MEMO',
					`NT_MAIN`.`GET_CLAIM_USER`(NB.RMT_ID, NB.`NB_CLAIM_USERID`) AS 'NB_CLAIM_USER_NM',
					NB.NB_DUPLICATED_YN AS 'NB_DUPLICATED_YN',
					`NT_CODE`.`GET_CODE_NM`(NB.`RLT_ID`) AS 'RLT_NM',
					NBI.`NBI_FAX_NUM` AS 'NBI_FAX_NUM',
					NBI.NBI_FAX_SEND_DTHMS, 
					`NT_MAIN`.`GET_INSU_CHG_PHONE`(NBI.`NIC_ID`, '".AES_SALT_KEY."') AS 'NIC_TEL1_AES',
					`NT_CODE`.`GET_CSD_NM`(NG.`CSD_ID`) AS 'CSD_NM',
					`NT_CODE`.`GET_RGN_NM`(NG.`CRR_1_ID`,NG.`CRR_2_ID`,NG.`CRR_3_ID`,TRUE) AS 'CRR_NM', 
					NG.`MGG_NM` AS 'MGG_NM',
					NG.`MGG_BIZ_ID` AS 'MGG_BIZ_ID',
					LEFT(IF(NB.`MODE_TARGET` = '10101', '본',NT_CODE.GET_CODE_NM(NB.`MODE_TARGET`)),1) AS 'MODE_TARGET',
					NT_CODE.GET_CODE_NM(`RET_ID`) AS 'RET_NM',
					NT_CODE.GET_CODE_NM(`RPET_ID`) AS 'RPET_NM',
					NGS.MGG_FRONTIER_NM AS 'MGG_FRONTIER_NM',
					NGS.MGG_BIZ_START_DT AS 'MGG_BIZ_START_DT',
					NGS.MGG_BIZ_END_DT AS 'MGG_BIZ_END_DT',
					NT_CODE.GET_CODE_NM(NB.`RMT_ID`) AS 'NB_RMT_NM',
					NBI.NBI_PRECLAIM_YN AS 'NBI_PRECLAIM_YN',
					NBI.NBI_REGI_NUM AS 'NBI_REGI_NUM',
					NT_CODE.GET_CODE_NM(NBI.NBI_RMT_ID) AS 'NBI_RMT_NM',
					NB.`NB_CAR_NUM` AS 'NB_CAR_NUM',
					SUBSTRING_INDEX(`NT_CODE`.`GET_CAR_INFO`(NB.CCC_ID, TRUE),' ',1) AS 'CAR_CORP', 
					`NT_CODE`.`GET_CAR_INFO`(NB.CCC_ID, FALSE) AS 'CAR_NM',
					`NT_BILL`.`GET_BILL_PART_NMS`(NB.NB_ID, FALSE, FALSE) AS 'PART_NM', 
					NT_BILL.`GET_NB_WORK_TIME`(NB.NB_ID) AS 'NBW_TIME',
					$NB_RATIO_PRICE AS NB_TOTAL_PRICE,
					NBI.NBI_RATIO AS 'NBI_RATIO', 
					NBI.`NBI_INSU_DEPOSIT_DT` AS 'NBI_INSU_DEPOSIT_DT',
					NBI.`NBI_INSU_DEPOSIT_PRICE` AS 'NBI_INSU_DEPOSIT_PRICE',
					ROUND(( 100 - ( ( IFNULL(NBI.`NBI_INSU_DEPOSIT_PRICE`,0) + IFNULL(`NBI_INSU_ADD_DEPOSIT_PRICE`,0) + IFNULL(`NBI_CAROWN_DEPOSIT_PRICE`,0)) / ($NB_RATIO_PRICE * IF(NB.`NB_VAT_DEDUCTION_YN` = 'Y', 0.9,1)*IF(NB.`NB_NEW_INSU_YN` = 'Y',0.8,1) )* 100) ),2) AS 'DC_RATIO',
					NB.NB_VAT_DEDUCTION_YN AS 'NB_VAT_DEDUCTION_YN',
					NBI.RDET_ID,
					`NT_CODE`.`GET_CODE_NM`(NBI.RDET_ID) AS 'RDET_NM',
					`NT_BILL`.`GET_DEPOSIT_YN`(NBI.RDET_ID) AS 'NBI_INSU_DEPOSIT_YN', 
					NBI.`NBI_REASON_DT` AS 'NBI_REASON_DT',
					NBI.RDT_ID,
					NT_CODE.GET_CODE_NM(NBI.RDT_ID) AS 'RDT_NM', 
                    NBI.RMT_ID AS 'REASON_RMT_ID',
					NT_CODE.GET_CODE_NM(NBI.RMT_ID) AS 'REASON_RMT_NM', 
					NBI.NBI_REASON_USERID AS 'NBI_REASON_USERID',
					IF(NBI.NBI_REASON_USERID IS NOT NULL,IF(NBI.RMT_ID = '11510',`NT_AUTH`.`GET_USER_NM`(NBI.NBI_REASON_USERID),`NT_MAIN`.`GET_INSU_USER_NM`(NBI.NBI_REASON_USERID)),'-') AS 'NBI_REASON_USER_NM',
					NBI.NBI_REASON_DTL_ID AS 'NBI_REASON_DTL_ID',
					NBI.NBI_INSU_DONE_YN AS 'NBI_INSU_DONE_YN',
					`NT_BILL`.`GET_REASSON_KIND`(NBI.`NBI_REASON_DTL_ID`) AS 'NBI_REASON_NM',
					NT_CODE.GET_CODE_NM(NBI.`NBI_REASON_DTL_ID`) AS 'NBI_REASON_DTL_NM',
					IFNULL(`NT_MAIN`.`GET_INSU_NM`(NBI.NBI_OTHER_MII_ID), '') AS 'NBI_OTHER_MII_NM', 
					IFNULL(NBI.NBI_OTHER_MII_ID, '') AS 'NBI_OTHER_MII_ID',
					IFNULL(NBI.NBI_OTHER_RATIO, '') AS 'NBI_OTHER_RATIO',
					IFNULL(NBI.NBI_OTHER_DT, '') AS 'NBI_OTHER_DT',
					IFNULL(NBI.NBI_OTHER_PRICE, '') AS 'NBI_OTHER_PRICE', 
					IFNULL(NBI.NBI_OTHER_MEMO, '') AS 'NBI_OTHER_MEMO',
					NBI.NBI_PROC_REASON_DT AS 'NBI_PROC_REASON_DT',
					IFNULL(NT_AUTH.GET_USER_NM(NBI.NBI_PROC_USERID), '') AS NBI_PROC_NM,
					NBI.NBI_PROC_USERID AS 'NBI_PROC_USERID', 
					NBI.NBI_REQ_REASON_YN AS 'NBI_REQ_REASON_YN',
					DATE_FORMAT(NBI.NBI_REQ_REASON_DTHMS, '%Y-%m-%d') AS 'NBI_REQ_REASON_DTHMS',
					NBI.NBI_REQ_REASON_USERID AS 'NBI_REQ_REASON_USERID',
					IFNULL(NT_AUTH.GET_USER_NM(NBI.NBI_REQ_REASON_USERID), '') AS NBI_REQ_REASON_USER_NM,
					NBI.NBI_LAST_PRINT_DTHMS AS 'NBI_LAST_PRINT_DTHMS',
					NB.NB_NEW_INSU_YN AS 'NB_NEW_INSU_YN',
					NBI.NBI_INSU_ADD_DEPOSIT_PRICE,
					NBI.NBI_INSU_ADD_DEPOSIT_DT,
					ROUND(IF(NB.`NB_NEW_INSU_YN` = 'Y',`NB_TOTAL_PRICE`*0.2,0),0) AS 'NBI_CAROWN_PRICE',
					NBI.`NBI_CAROWN_DEPOSIT_DT` AS 'NBI_CAROWN_DEPOSIT_DT',
					NBI.`NBI_CAROWN_DEPOSIT_PRICE` AS 'NBI_CAROWN_DEPOSIT_PRICE',
					NBI.`NBI_CAROWN_DEPOSIT_YN` AS 'NBI_CAROWN_DEPOSIT_YN',
					CASE NB.MODE_TARGET
						WHEN '10101' THEN `NT_AUTH`.`GET_USER_NM`(NBI.`WRT_USERID`)
						WHEN '10102' THEN `NT_MAIN`.`GET_GARAGE_USER_NM`(NBI.`WRT_USERID`)
						WHEN '10103' THEN `NT_MAIN`.`GET_INSU_USER_NM`(NBI.NBI_REASON_USERID)
						ELSE '-'
					END 'WRT_USER_NM',
					NBI.NBI_DEPOSIT_EXTRA1,
					NBI.`WRT_USERID` AS 'WRT_USERID'
				FROM
					`NT_BILL`.`NBB_BILL` NB,
					`NT_BILL`.`NBB_BILL_INSU` NBI,
					`NT_MAIN`.`NMG_GARAGE` NG,
					`NT_MAIN`.`NMG_GARAGE_SUB` NGS
					$subTable
				WHERE
					NB.`NB_ID` = NBI.`NB_ID`
				AND	NB.`MGG_ID` = NG.`MGG_ID`
				AND	NB.`MGG_ID` = NGS.`MGG_ID`
				AND	NB.DEL_YN = 'N'
				AND NG.DEL_YN = 'N'
		";
        // echo $iSQL;
		return $iSQL;
    }
	
	/* 검색조건 쿼리 리턴 */
	function getSqlWhere($ARR){
		// echo var_dump($ARR);
		$tmpKeyword = ""; 
		$NB_RATIO_PRICE = "TRUNCATE(IFNULL(NB.`NB_TOTAL_PRICE`,'0')*IF(NBI.`NBI_RATIO`<>'',CAST(NBI.`NBI_RATIO` AS DECIMAL)/100,1),-1)" ; 

		//1차 기간검색 
		if($ARR['DT_KEY_SEARCH'] != '') {
			if($ARR['DT_KEY_SEARCH'] == 'NB_CLAIM_DT'){
                if($ARR['START_DT_SEARCH'] != '' && $ARR['END_DT_SEARCH'] != ''){ // 등록일
                    $tmpKeyword .= " AND NB.NB_CLAIM_DT BETWEEN '".addslashes($ARR['START_DT_SEARCH'])."' AND '".addslashes($ARR['END_DT_SEARCH'])."' \n";
                }else if($ARR['START_DT_SEARCH'] != '' ){
                    $tmpKeyword .= " AND NB.NB_CLAIM_DT >= '".$ARR['START_DT_SEARCH']."' \n";
                }else if($ARR['END_DT_SEARCH'] != ''){
                    $tmpKeyword .= " AND NB.NB_CLAIM_DT <= '".$ARR['END_DT_SEARCH']."' \n";
                }
            }else if($ARR['DT_KEY_SEARCH'] == 'NBI_INSU_DEPOSIT_DT'){ // 처리일
                if($ARR['START_DT_SEARCH'] != '' && $ARR['END_DT_SEARCH'] != ''){
                    $tmpKeyword .= " AND DATE_FORMAT(NBI.NBI_INSU_DEPOSIT_DT, '%Y-%m-%d') BETWEEN '".addslashes($ARR['START_DT_SEARCH'])."' AND '".addslashes($ARR['END_DT_SEARCH'])."'";
                }else if($ARR['START_DT_SEARCH'] != '' ){
                    $tmpKeyword .= " AND DATE_FORMAT(NBI.NBI_INSU_DEPOSIT_DT, '%Y-%m-%d') >= '".$ARR['START_DT_SEARCH']."' \n";
                }else if($ARR['END_DT_SEARCH'] != ''){
                    $tmpKeyword .= " AND DATE_FORMAT(NBI.NBI_INSU_DEPOSIT_DT, '%Y-%m-%d') <= '".$ARR['END_DT_SEARCH']."' \n";
                }
            }else if($ARR['DT_KEY_SEARCH'] == 'NBI_REASON_DT'){ // 처리일
                if($ARR['START_DT_SEARCH'] != '' && $ARR['END_DT_SEARCH'] != ''){
                    $tmpKeyword .= " AND DATE_FORMAT(NBI.NBI_REASON_DT, '%Y-%m-%d') BETWEEN '".addslashes($ARR['START_DT_SEARCH'])."' AND '".addslashes($ARR['END_DT_SEARCH'])."'";
                }else if($ARR['START_DT_SEARCH'] != '' ){
                    $tmpKeyword .= " AND DATE_FORMAT(NBI.NBI_REASON_DT, '%Y-%m-%d') >= '".$ARR['START_DT_SEARCH']."' \n";
                }else if($ARR['END_DT_SEARCH'] != ''){
                    $tmpKeyword .= " AND DATE_FORMAT(NBI.NBI_REASON_DT, '%Y-%m-%d') <= '".$ARR['END_DT_SEARCH']."' \n";
                }
            }else if($ARR['DT_KEY_SEARCH'] == 'NBI_PROC_REASON_DT'){ // 사유처리일
                if($ARR['START_DT_SEARCH'] != '' && $ARR['END_DT_SEARCH'] != ''){
                    $tmpKeyword .= " AND DATE_FORMAT(NBI.NBI_PROC_REASON_DT, '%Y-%m-%d') BETWEEN '".addslashes($ARR['START_DT_SEARCH'])."' AND '".addslashes($ARR['END_DT_SEARCH'])."'";
                }else if($ARR['START_DT_SEARCH'] != '' ){
                    $tmpKeyword .= " AND DATE_FORMAT(NBI.NBI_PROC_REASON_DT, '%Y-%m-%d') >= '".$ARR['START_DT_SEARCH']."' \n";
                }else if($ARR['END_DT_SEARCH'] != ''){
                    $tmpKeyword .= " AND DATE_FORMAT(NBI.NBI_PROC_REASON_DT, '%Y-%m-%d') <= '".$ARR['END_DT_SEARCH']."' \n";
                }
			}else if($ARR['DT_KEY_SEARCH'] == 'NBI_REQ_REASON_DTHMS'){ // 사유입력요청일
                if($ARR['START_DT_SEARCH'] != '' && $ARR['END_DT_SEARCH'] != ''){
                    $tmpKeyword .= " AND DATE_FORMAT(NBI.NBI_REQ_REASON_DTHMS, '%Y-%m-%d') BETWEEN '".addslashes($ARR['START_DT_SEARCH'])."' AND '".addslashes($ARR['END_DT_SEARCH'])."'";
                }else if($ARR['START_DT_SEARCH'] != '' ){
                    $tmpKeyword .= " AND DATE_FORMAT(NBI.NBI_REQ_REASON_DTHMS, '%Y-%m-%d') >= '".$ARR['START_DT_SEARCH']."' \n";
                }else if($ARR['END_DT_SEARCH'] != ''){
                    $tmpKeyword .= " AND DATE_FORMAT(NBI.NBI_REQ_REASON_DTHMS, '%Y-%m-%d') <= '".$ARR['END_DT_SEARCH']."' \n";
                }
            }
		} 

		//2차 기간검색 
		if($ARR['2ND_DT_KEY_SEARCH'] != '') {
			if($ARR['2ND_DT_KEY_SEARCH'] == 'NB_CLAIM_DT'){// 청구일
                if($ARR['2ND_START_DT_SEARCH'] != '' && $ARR['2ND_END_DT_SEARCH'] != ''){ 
                    $tmpKeyword .= " AND NB.NB_CLAIM_DT BETWEEN '".addslashes($ARR['2ND_START_DT_SEARCH'])."' AND '".addslashes($ARR['2ND_END_DT_SEARCH'])."' \n";
                }else if($ARR['2ND_START_DT_SEARCH'] != '' ){
                    $tmpKeyword .= " AND NB.NB_CLAIM_DT >= '".$ARR['2ND_START_DT_SEARCH']."' \n";
                }else if($ARR['2ND_END_DT_SEARCH'] != ''){
                    $tmpKeyword .= " AND NB.NB_CLAIM_DT <= '".$ARR['2ND_END_DT_SEARCH']."' \n";
                }
            }else if($ARR['2ND_DT_KEY_SEARCH'] == 'NBI_INSU_DEPOSIT_DT'){ // 입금일
                if($ARR['2ND_START_DT_SEARCH'] != '' && $ARR['2ND_END_DT_SEARCH'] != ''){
                    $tmpKeyword .= " AND DATE_FORMAT(NBI.NBI_INSU_DEPOSIT_DT, '%Y-%m-%d') BETWEEN '".addslashes($ARR['2ND_START_DT_SEARCH'])."' AND '".addslashes($ARR['2ND_END_DT_SEARCH'])."'";
                }else if($ARR['2ND_START_DT_SEARCH'] != '' ){
                    $tmpKeyword .= " AND DATE_FORMAT(NBI.NBI_INSU_DEPOSIT_DT, '%Y-%m-%d') >= '".$ARR['2ND_START_DT_SEARCH']."' \n";
                }else if($ARR['2ND_END_DT_SEARCH'] != ''){
                    $tmpKeyword .= " AND DATE_FORMAT(NBI.NBI_INSU_DEPOSIT_DT, '%Y-%m-%d') <= '".$ARR['2ND_END_DT_SEARCH']."' \n";
                }
            }else if($ARR['2ND_DT_KEY_SEARCH'] == 'NBI_REASON_DT'){ // 사유입력일
                if($ARR['2ND_START_DT_SEARCH'] != '' && $ARR['2ND_END_DT_SEARCH'] != ''){
                    $tmpKeyword .= " AND DATE_FORMAT(NBI.NBI_REASON_DT, '%Y-%m-%d') BETWEEN '".addslashes($ARR['2ND_START_DT_SEARCH'])."' AND '".addslashes($ARR['2ND_END_DT_SEARCH'])."'";
                }else if($ARR['2ND_START_DT_SEARCH'] != '' ){
                    $tmpKeyword .= " AND DATE_FORMAT(NBI.NBI_REASON_DT, '%Y-%m-%d') >= '".$ARR['2ND_START_DT_SEARCH']."' \n";
                }else if($ARR['2ND_END_DT_SEARCH'] != ''){
                    $tmpKeyword .= " AND DATE_FORMAT(NBI.NBI_REASON_DT, '%Y-%m-%d') <= '".$ARR['2ND_END_DT_SEARCH']."' \n";
                }
            }else if($ARR['2ND_DT_KEY_SEARCH'] == 'NBI_PROC_REASON_DT'){ // 사유처리일
                if($ARR['2ND_START_DT_SEARCH'] != '' && $ARR['2ND_END_DT_SEARCH'] != ''){
                    $tmpKeyword .= " AND DATE_FORMAT(NBI.NBI_PROC_REASON_DT, '%Y-%m-%d') BETWEEN '".addslashes($ARR['2ND_START_DT_SEARCH'])."' AND '".addslashes($ARR['2ND_END_DT_SEARCH'])."'";
                }else if($ARR['2ND_START_DT_SEARCH'] != '' ){
                    $tmpKeyword .= " AND DATE_FORMAT(NBI.NBI_PROC_REASON_DT, '%Y-%m-%d') >= '".$ARR['2ND_START_DT_SEARCH']."' \n";
                }else if($ARR['2ND_END_DT_SEARCH'] != ''){
                    $tmpKeyword .= " AND DATE_FORMAT(NBI.NBI_PROC_REASON_DT, '%Y-%m-%d') <= '".$ARR['2ND_END_DT_SEARCH']."' \n";
                }
            }else if($ARR['2ND_DT_KEY_SEARCH'] == 'NBI_REQ_REASON_DTHMS'){ // 사유입력요청일
                if($ARR['2ND_START_DT_SEARCH'] != '' && $ARR['2ND_END_DT_SEARCH'] != ''){
                    $tmpKeyword .= " AND DATE_FORMAT(NBI.NBI_REQ_REASON_DTHMS, '%Y-%m-%d') BETWEEN '".addslashes($ARR['2ND_START_DT_SEARCH'])."' AND '".addslashes($ARR['2ND_END_DT_SEARCH'])."'";
                }else if($ARR['2ND_START_DT_SEARCH'] != '' ){
                    $tmpKeyword .= " AND DATE_FORMAT(NBI.NBI_REQ_REASON_DTHMS, '%Y-%m-%d') >= '".$ARR['2ND_START_DT_SEARCH']."' \n";
                }else if($ARR['2ND_END_DT_SEARCH'] != ''){
                    $tmpKeyword .= " AND DATE_FORMAT(NBI.NBI_REQ_REASON_DTHMS, '%Y-%m-%d') <= '".$ARR['2ND_END_DT_SEARCH']."' \n";
                }
            }
		} 
		
		//보험사검색
		if($ARR['SEARCH_INSU_NM'] != ''){
            $tmpKeyword .= " AND NBI.MII_ID IN (";
            foreach ($ARR['SEARCH_INSU_NM'] as $index => $MII_ARRAY) {
                $tmpKeyword .=" '".$MII_ARRAY."',";
            }
            $tmpKeyword=substr($tmpKeyword, 0, -1); // 마지막 콤마제거
            $tmpKeyword .= ") \n";
		}

		//보험사팀검색
		if($ARR['SEARCH_INSU_TEAM'] != ''){
			$tmpKeyword .= " AND (CONCAT(NBI.MII_ID,NG.CRR_1_ID,NG.CRR_2_ID,NG.CRR_3_ID) IN 
									(
										SELECT 
											CONCAT(
												`NT_MAIN`.`GET_INSU_ID_BY_MIT`(MIT_ID),
												CRR_1_ID,
												CRR_2_ID,
												CRR_3_ID
											)
										FROM 
											NT_MAIN.NMI_INSU_TEAM_REGION
										WHERE MIT_ID IN (";
            foreach ($ARR['SEARCH_INSU_TEAM'] as $index => $MIT_ARRAY) {
                $tmpKeyword .=" '".$MIT_ARRAY."',";
            }
            $tmpKeyword=substr($tmpKeyword, 0, -1); // 마지막 콤마제거
			$tmpKeyword .= " 	) 
				) OR (`NT_MAIN`.`GET_INSU_CHG_TEAM_ID`(NBI.NIC_ID) IN (";
			foreach ($ARR['SEARCH_INSU_TEAM'] as $index => $MIT_ARRAY) {
				$tmpKeyword .=" '".$MIT_ARRAY."',";
			}
			$tmpKeyword=substr($tmpKeyword, 0, -1); // 마지막 콤마제거
			$tmpKeyword .= ") \n  )\n  )\n";
		}

		//팀메모검색
		if($ARR['MIT_MEMO_SEARCH'] != '') {
			$tmpKeyword .= " AND INSTR(NBI.NBI_TEAM_MEMO,'".addslashes($ARR['MIT_MEMO_SEARCH'])."')>0 ";	
		} 

		//보상담당
		if($ARR['INSU_FAO_SEARCH'] != '') {
			// $tmpKeyword .= " AND INSTR(CAST(AES_DECRYPT(NIC.`NIC_NM_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR),'".addslashes($ARR['INSU_FAO_SEARCH'])."')>0 ";	
			$tmpKeyword .= " AND INSTR(`NT_MAIN`.`GET_INSU_CHG_NM`(NBI.NIC_ID, '".AES_SALT_KEY."') ,'".addslashes($ARR['INSU_FAO_SEARCH'])."')>0 ";	
		} 

		//청구요청자
		if($ARR['NB_CLAIM_USERID_SEARCH'] != '') {
			$tmpKeyword .= " AND INSTR(`NT_MAIN`.`GET_CLAIM_USER`(NB.RMT_ID, NB.`NB_CLAIM_USERID`) ,'".addslashes($ARR['NB_CLAIM_USERID_SEARCH'])."')>0 ";	
		} 
		

		//구분검색
		if($ARR['CSD_ID_SEARCH'] != ''){
            $tmpKeyword .= " AND NG.CSD_ID IN (";
            foreach ($ARR['CSD_ID_SEARCH'] as $index => $CSD_ARRAY) {
                $tmpKeyword .=" '".$CSD_ARRAY."',";
            }
            $tmpKeyword=substr($tmpKeyword, 0, -1); // 마지막 콤마제거
            $tmpKeyword .= ") \n";
        }

		//지역주소검색
		if($ARR['CRR_ID_SEARCH'] !=  '' || $ARR['CRR_ID_SEARCH'] != null) {
            $tmpKeyword .= " AND (";
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
                    $tmpKeyword .= " AND `NG`.`CRR_2_ID` = '".$CSD[1]."' \n";
                }
                
                if($CSD[2] != NOT_CRR_3){
                    $tmpKeyword .= " AND `NG`.`CRR_3_ID` = '".$CSD[2]."' \n";
                }
                
                $tmpKeyword .= ") \n";
            }
            $tmpKeyword .= ") \n";
        }
        

		//공업사명검색
		if($ARR['MGG_NM_SEARCH'] != '') {
			$tmpKeyword .= " AND NB.`MGG_ID` IN ( ";
            foreach ($ARR['MGG_NM_SEARCH'] as $index => $ARR_MGG) {
                $tmpKeyword .=" '".$ARR_MGG."',";
            }
            $tmpKeyword = substr($tmpKeyword, 0, -1); // 마지막 콤마제거
            $tmpKeyword .= ") \n";
		} 
		
		
		//청구식검색
		if($ARR['RET_ID_SEARCH'] != '') {
			$tmpKeyword .= " AND NGS.RET_ID IN ( ";
            foreach ($ARR['RET_ID_SEARCH'] as $index => $RET_ID) {
                $tmpKeyword .=" '".$RET_ID."',";
            }
            $tmpKeyword = substr($tmpKeyword, 0, -1); // 마지막 콤마제거
            $tmpKeyword .= ") \n";
		}

		//지급식 검색
		if($ARR['RPET_ID_SEARCH'] != '') {
			$tmpKeyword .= " AND `NGS`.`RPET_ID` = '".addslashes($ARR['RPET_ID_SEARCH'])."' ";	
		} 

		
		//개척자 검색 
		if($ARR['MGG_FRONTIER_NM_SEARCH'] != '') {
			$tmpKeyword .= " AND INSTR(`NGS`.`MGG_FRONTIER_NM`,'".addslashes($ARR['MGG_FRONTIER_NM_SEARCH'])."')>0 ";	
		} 

		//청구서 작성 주체 
		if($ARR['MODE_SEARCH'] != '') {
			$tmpKeyword .= " AND  NB.MODE_TARGET = '".addslashes($ARR['MODE_SEARCH'])."' ";
		} 
		//선청구여부
		if($ARR['NBI_PRECLAIM_YN_SEARCH'] != '') {
			$tmpKeyword .= " AND NBI.NBI_PRECLAIM_YN = '".addslashes($ARR['NBI_PRECLAIM_YN_SEARCH'])."' ";	
		} 

		//담보
		if($ARR['RLT_ID_SEARCH'] != '') {
			$tmpKeyword .= " AND NB.RLT_ID = '".addslashes($ARR['RLT_ID_SEARCH'])."' ";	
		} 


		//차량
		if($ARR['CCC_ID_SEARCH'] != '') {
			// $subTable .= ", `NT_CODE`.`NCC_CAR` NC";
			$tmpKeyword .= " AND NB.`CCC_ID`= NC.`CCC_ID`" ;
			$tmpKeyword .= " AND NC.CCC_CAR_TYPE_CD= '".addslashes($ARR['CCC_ID_SEARCH'])."' ";	
		} 

		//사용부품
		if($ARR['CPPS_ID_SEARCH'] != '') {
			$tmpKeyword .= " AND INSTR(`NT_BILL`.`GET_BILL_PART_NMS`(NB.NB_ID, FALSE, FALSE) , '".$ARR['CPPS_ID_SEARCH']."') > 0 ";	
		} 


		//방청시간
		if($ARR['START_TIME_SEARCH'] != '' && $ARR['END_TIME_SEARCH'] != ''){ // 등록일
			$tmpKeyword .= " AND NT_BILL.`GET_NB_WORK_TIME`(NB.NB_ID) BETWEEN '".addslashes($ARR['START_TIME_SEARCH'])."' AND '".addslashes($ARR['END_TIME_SEARCH'])."' \n";
		}else if($ARR['START_TIME_SEARCH'] != '' ){
			$tmpKeyword .= " AND NT_BILL.`GET_NB_WORK_TIME`(NB.NB_ID) >= '".$ARR['START_TIME_SEARCH']."' \n";
		}else if($ARR['END_TIME_SEARCH'] != ''){
			$tmpKeyword .= " AND NT_BILL.`GET_NB_WORK_TIME`(NB.NB_ID) <= '".$ARR['END_TIME_SEARCH']."' \n";
		}

		//DC
		$NB_RATIO_PRICE = "TRUNCATE(IFNULL(NB.`NB_TOTAL_PRICE`,'0')*IF(NBI.`NBI_RATIO`<>'',CAST(NBI.`NBI_RATIO` AS DECIMAL)/100,1),-1)" ; 
	
		

		//공제여부
		if($ARR['NB_VAT_DEDUCTION_YN_SEARCH'] != '') {
			$tmpKeyword .= " AND NB.NB_VAT_DEDUCTION_YN = '".addslashes($ARR['NB_VAT_DEDUCTION_YN_SEARCH'])."' ";	
		} 

		//교환판금
		if($ARR['WORK_TYPE_SEARCH'] != '') {
			// $subTable .= ", `NT_BILL`.`NBB_BILL_WORK` NBW ";
			$tmpKeyword .= " AND NBW.NB_ID = NB.NB_ID ";	
			$tmpKeyword .= " AND NBW.RRT_ID = '".addslashes($ARR['WORK_TYPE_SEARCH'])."' ";	
		} 

		//전송여부
		if($ARR['NBI_SEND_FAX_YN_SEARCH'] != '') {
			$tmpKeyword .= " AND `NBI`.`RFS_ID` = '".addslashes($ARR['NBI_SEND_FAX_YN_SEARCH'])."'";
		} 

		//중복여부
		if($ARR['NB_DUPLICATED_YN_SEARCH'] != '') {
			$tmpKeyword .= " AND NB.NB_DUPLICATED_YN = '".addslashes($ARR['NB_DUPLICATED_YN_SEARCH'])."' ";	
		} 

		//입금여부
		if($ARR['DEPOSIT_YN_SEARCH'] != '') {
			if($ARR['DEPOSIT_YN_SEARCH'] == YN_Y){
				$tmpKeyword .= " AND  NBI.RDET_ID IN('40122','40123','40124')   AND ( NBI.`NBI_REASON_DTL_ID` IS NULL  OR  NBI.NBI_INSU_DONE_YN = 'Y' )  ";	
			}else if($ARR['DEPOSIT_YN_SEARCH'] == YN_N){
				$tmpKeyword .= " AND NBI.RDET_ID = '40121' AND ( NBI.`NBI_REASON_DTL_ID` IS NULL  OR  NBI.NBI_INSU_DONE_YN = 'Y' )  ";	
			}else {
				$tmpKeyword .= " AND (NBI.`NBI_REASON_DTL_ID` IS NOT NULL AND NBI.NBI_INSU_DONE_YN = 'N')";	
			}
		} 

		//입금여부상세
		if($ARR['DEPOSIT_DETAIL_YN_SEARCH'] != '') {
			$tmpKeyword .= " AND NBI.RDT_ID IN (";
            foreach ($ARR['DEPOSIT_DETAIL_YN_SEARCH'] as $index => $RDET_ARRAY) {
                $tmpKeyword .=" '".$RDET_ARRAY."',";
            }
            $tmpKeyword=substr($tmpKeyword, 0, -1); // 마지막 콤마제거
            $tmpKeyword .= ") \n";
		} 

		//종결여부
		if($ARR['NBI_INSU_DONE_YN_SEARCH'] != '') {
			$tmpKeyword .= " AND NBI.NBI_INSU_DONE_YN = '".addslashes($ARR['NBI_INSU_DONE_YN_SEARCH'])."' ";	
		} 

		//사유입력요청여부
		if($ARR['NBI_REQ_REASON_YN_SEARCH'] != '') {
			$tmpKeyword .= " AND NBI.NBI_REQ_REASON_YN = '".addslashes($ARR['NBI_REQ_REASON_YN_SEARCH'])."' ";	
		} 

		//사유입력자 검색 
		if($ARR['NBI_REASON_USER_NM_SEARCH'] != '') {
			$tmpKeyword .= " AND INSTR(NT_BILL.GET_REQ_REASON_USER(NBI.RMT_ID, NBI.NBI_REASON_USERID),'".addslashes($ARR['NBI_REASON_USER_NM_SEARCH'])."')>0 ";	
		} 

		//사유처리여부
		if($ARR['NBI_PROC_YN_SEARCH'] != '') {
			if($ARR['NBI_PROC_YN_SEARCH']  == YN_Y){
				$tmpKeyword .= " AND NBI.NBI_PROC_USERID IS NOT NULL ";	
			}else {
				$tmpKeyword .= " AND NBI.NBI_PROC_USERID IS NULL ";	
			}
		} 

		//사유처리자 검색 
		if($ARR['NBI_PROC_USER_NM_SEARCH'] != '') {
			$tmpKeyword .= " AND INSTR( NT_AUTH.GET_USER_NM(NBI.NBI_PROC_USERID),'".addslashes($ARR['NBI_PROC_USER_NM_SEARCH'])."')>0 ";	
		} 

		//사유구분 검색
		if($ARR['NBI_REASON_KIND_SEARCH'] != '') {
			if($ARR['NBI_REASON_KIND_SEARCH'] == '402'){
				$tmpKeyword .= " AND NBI_REASON_DTL_ID IN (SELECT `RER_ID` FROM `NT_CODE`.`REF_EXCLUDE_REASON`) ";	
			}else if($ARR['NBI_REASON_KIND_SEARCH']  == '403'){
				$tmpKeyword .= " AND NBI.NBI_REASON_DTL_ID IN (SELECT `RDR_ID` FROM `NT_CODE`.`REF_DECREMENT_REASON`) ";	
			}
		} 
		
		//사유상세
		if($ARR['NBI_REASON_DTL_ID_SEARCH'] != '') {
			$tmpKeyword .= " AND NBI.`NBI_REASON_DTL_ID` IN ( ";
            foreach ($ARR['NBI_REASON_DTL_ID_SEARCH'] as $index => $DTL_ID) {
                $tmpKeyword .=" '".$DTL_ID."',";
            }
            $tmpKeyword = substr($tmpKeyword, 0, -1); // 마지막 콤마제거
            $tmpKeyword .= ") \n";
		}


		//차주입금여부
		if($ARR['NBI_CAROWN_DEPOSIT_YN_SEARCH'] != '') {
			$tmpKeyword .= " AND NBI.NBI_CAROWN_DEPOSIT_YN = '".addslashes($ARR['NBI_CAROWN_DEPOSIT_YN_SEARCH'])."' ";	
		} 

		//차주차량정보
		if($ARR['CAR_KEY_SEARCH'] != '') {
			if($ARR['CAR_KEY_SEARCH'] == 'NBI_REGI_NUM'){
				$tmpKeyword .= " AND INSTR(NBI.NBI_REGI_NUM, '".addslashes($ARR['CAR_TXT_SEARCH'])."' ) > 0 \n";
            }else if($ARR['CAR_KEY_SEARCH'] == 'NB_CAR_NUM'){ 
				$tmpKeyword .= " AND INSTR(NB.NB_CAR_NUM, '".addslashes($ARR['CAR_TXT_SEARCH'])."' ) > 0 \n";
            }else if($ARR['CAR_KEY_SEARCH'] == 'CCC_NM'){ 
				$tmpKeyword .= " AND INSTR(`NT_CODE`.`GET_CAR_INFO`(NB.CCC_ID, FALSE), '".addslashes($ARR['CAR_TXT_SEARCH'])."' ) > 0\n";
			}
		} 

		//최종갱신자 검색 
		if($ARR['WRT_USERID_SEARCH'] != '') {
			$tmpKeyword .= " AND INSTR(NT_BILL.GET_REQ_REASON_USER('', NBI.WRT_USERID),'".addslashes($ARR['WRT_USERID_SEARCH'])."')>0 ";	
		} 

		return $tmpKeyword ;
	} 

	/* FROM 추가 테이블 */
	function getSubTable($ARR){
		$subTable = "" ;
		//차량
		if($ARR['CCC_ID_SEARCH'] != '') {
			$subTable .= ", `NT_CODE`.`NCC_CAR` NC";
		} 
		//교환판금
		if($ARR['WORK_TYPE_SEARCH'] != '') {
			$subTable .= ", `NT_BILL`.`NBB_BILL_WORK` NBW ";
		} 
		return $subTable;
	}
	/* GROUP BY 추가 */
	function getGroup($ARR){
		$group = "" ;
		//교환판금
		if($ARR['WORK_TYPE_SEARCH'] != '') {
			$group .= " GROUP BY NBW.NB_ID ";
		} 
		return $group;
	}
		

    /**
	 * 쿼리에서 받은 select 결과를 array로 저장하는 함수. 공용으로 사용하기 위해서
	 * (TotalList와 getList)
	 */
	function getListColumn($Result, $IDX){
		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"IDX"=>$IDX--,
					"NB_ID"=>$RS['NB_ID'],
					"NBI_RMT_ID"=>$RS['NBI_RMT_ID'],
					"MGG_ID"=>$RS['MGG_ID'],
					"NB_CLAIM_DT"=>$RS['NB_CLAIM_DT'],
					"MII_ID"=>$RS['MII_ID'],
					"MII_NM"=>$RS['MII_NM'],
					"NIC_ID"=>$RS['NIC_ID'],
					"NIC_NM"=>$RS['NIC_NM'],
					"MIT_NM"=>$RS['MIT_NM'],
					"NBI_TEAM_MEMO"=>$RS['NBI_TEAM_MEMO'],
					"NB_CLAIM_USER_NM"=>$RS['NB_CLAIM_USER_NM'],
					"NB_DUPLICATED_YN"=>$RS['NB_DUPLICATED_YN'],
					"RLT_NM"=>$RS['RLT_NM'],
					"NBI_FAX_NUM"=>$RS['NBI_FAX_NUM'],
					"NIC_TEL1_AES"=>$RS['NIC_TEL1_AES'],
					"CSD_NM"=>$RS['CSD_NM'],
					"CRR_NM"=>$RS['CRR_NM'],
					"MGG_NM"=>$RS['MGG_NM'],
					"MGG_BIZ_ID"=>$RS['MGG_BIZ_ID'],
					"MODE_TARGET"=>$RS['MODE_TARGET'],
					"RMT_NM"=>$RS['RMT_NM'],
					"RET_NM"=>$RS['RET_NM'],
					"RPET_NM"=>$RS['RPET_NM'],
					"MGG_FRONTIER_NM"=>$RS['MGG_FRONTIER_NM'],
					"MGG_BIZ_START_DT"=>$RS['MGG_BIZ_START_DT'],
					"MGG_BIZ_END_DT"=>$RS['MGG_BIZ_END_DT'],
					"NB_RMT_NM"=>$RS['NB_RMT_NM'],
					"NBI_PRECLAIM_YN"=>$RS['NBI_PRECLAIM_YN'],
					"NBI_REGI_NUM"=>$RS['NBI_REGI_NUM'],
					"NBI_RMT_NM"=>$RS['NBI_RMT_NM'],
					"NB_CAR_NUM"=>$RS['NB_CAR_NUM'],
					"CAR_CORP"=>$RS['CAR_CORP'],
					"CAR_NM"=>$RS['CAR_NM'],
					"PART_NM"=>$RS['PART_NM'],
					"NBW_TIME"=>$RS['NBW_TIME'],
					"NB_TOTAL_PRICE"=>$RS['NB_TOTAL_PRICE'],
					"NBI_RATIO"=>$RS['NBI_RATIO'],
					"NBI_INSU_DEPOSIT_DT"=>$RS['NBI_INSU_DEPOSIT_DT'],
					"NBI_INSU_DEPOSIT_PRICE"=>$RS['NBI_INSU_DEPOSIT_PRICE'],
					"DC_RATIO"=>$RS['DC_RATIO'],
					"NB_VAT_DEDUCTION_YN"=>$RS['NB_VAT_DEDUCTION_YN'],
					"RDET_ID"=>$RS['RDET_ID'],
					"RDET_NM"=>$RS['RDET_NM'],
					"NBI_INSU_DEPOSIT_YN"=>$RS['NBI_INSU_DEPOSIT_YN'],
					"NBI_REASON_DT"=>$RS['NBI_REASON_DT'],
					"RDT_ID"=>$RS['RDT_ID'],
					"RDT_NM"=>$RS['RDT_NM'],
					"REASON_RMT_ID"=>$RS['REASON_RMT_ID'],
					"REASON_RMT_NM"=>$RS['REASON_RMT_NM'],
					"NBI_INSU_DONE_YN"=>$RS['NBI_INSU_DONE_YN'],
					"NBI_REASON_USERID"=>$RS['NBI_REASON_USERID'],
					"NBI_REASON_USER_NM"=>$RS['NBI_REASON_USER_NM'],
					"NBI_REASON_DTL_ID"=>$RS['NBI_REASON_DTL_ID'],
					"NBI_REASON_NM"=>$RS['NBI_REASON_NM'],
					"NBI_REQ_REASON_YN"=>$RS['NBI_REQ_REASON_YN'],
					"NBI_REQ_REASON_DTHMS"=>$RS['NBI_REQ_REASON_DTHMS'],
					"NBI_REQ_REASON_USERID"=>$RS['NBI_REQ_REASON_USERID'],
					"NBI_REQ_REASON_USER_NM"=>$RS['NBI_REQ_REASON_USER_NM'],
					"NBI_REASON_DTL_NM"=>$RS['NBI_REASON_DTL_NM'],
					"NBI_OTHER_MII_NM"=>$RS['NBI_OTHER_MII_NM'],
					"NBI_OTHER_MII_ID"=>$RS['NBI_OTHER_MII_ID'],
					"NBI_OTHER_RATIO"=>$RS['NBI_OTHER_RATIO'],
					"NBI_OTHER_DT"=>$RS['NBI_OTHER_DT'],
					"NBI_OTHER_PRICE"=>$RS['NBI_OTHER_PRICE'],
					"NBI_OTHER_MEMO"=>$RS['NBI_OTHER_MEMO'],
					"NBI_PROC_REASON_DT"=>$RS['NBI_PROC_REASON_DT'],
					"NBI_PROC_NM"=>$RS['NBI_PROC_NM'],
					"NBI_PROC_USERID"=>$RS['NBI_PROC_USERID'],
					"NBI_INSU_DEPOSIT_DT"=>$RS['NBI_INSU_DEPOSIT_DT'],
					"NBI_LAST_PRINT_DTHMS"=>$RS['NBI_LAST_PRINT_DTHMS'],
					"NB_NEW_INSU_YN"=>$RS['NB_NEW_INSU_YN'],
					"NBI_INSU_ADD_DEPOSIT_PRICE"=>$RS['NBI_INSU_ADD_DEPOSIT_PRICE'],
					"NBI_INSU_ADD_DEPOSIT_DT"=>$RS['NBI_INSU_ADD_DEPOSIT_DT'],
					"NBI_CAROWN_PRICE"=>$RS['NBI_CAROWN_PRICE'],
					"NBI_CAROWN_DEPOSIT_DT"=>$RS['NBI_CAROWN_DEPOSIT_DT'],
					"NBI_CAROWN_DEPOSIT_PRICE"=>$RS['NBI_CAROWN_DEPOSIT_PRICE'],
					"NBI_CAROWN_DEPOSIT_YN"=>$RS['NBI_CAROWN_DEPOSIT_YN'],
					"NBI_DEPOSIT_EXTRA1"=>$RS['NBI_DEPOSIT_EXTRA1'],
					"WRT_USER_NM"=>$RS['WRT_USER_NM'],
					"WRT_USERID"=>$RS['WRT_USERID']
			));
		}
		return $rtnArray;
		
	}

	function getSummary($ARR){
		/* 검색조건 */
		$tmpKeyword = $this->getSqlWhere($ARR); 
		$subTable =  $this->getSubTable($ARR); 

		$NB_RATIO_PRICE = "TRUNCATE(IFNULL(NB.`NB_TOTAL_PRICE`,'0')*IF(NBI.`NBI_RATIO`<>'',CAST(NBI.`NBI_RATIO` AS DECIMAL)/100,1),-1)" ; 

		$iSQL = "SELECT
					COUNT(NB.NB_ID) AS 'CNT',
					IFNULL(SUM(IF(NB.RLT_ID = '11430',1,0)),0) AS 'CNT_DOUBLE',	# 11430 쌍방
					IFNULL(SUM($NB_RATIO_PRICE),0) AS 'PRICE',
					IFNULL(SUM(IF(`RDET_ID` <> '40121', 1, 0)),0) AS 'CNT_DEPOSIT',
					IFNULL(SUM(IF(NB.RLT_ID = '11430' AND `RDET_ID` <> '40121',1,0)),0) AS 'CNT_DEPOSIT_DOUBLE',	# 11430 쌍방
					IFNULL(SUM(IF(`RDET_ID` <> '40121', 
						(
						IFNULL(NBI.`NBI_INSU_DEPOSIT_PRICE`, 0) + IFNULL(NBI.`NBI_INSU_ADD_DEPOSIT_PRICE`, 0) + IFNULL(NBI.`NBI_CAROWN_DEPOSIT_PRICE`, 0) + IFNULL(NBI.`NBI_OTHER_PRICE`, 0)
						), 0)),0) AS 'DEPOSIT_PRICE',
					IFNULL(ROUND((SUM(IF(`RDET_ID` <> '40121', 1, 0))/COUNT(NB.NB_ID))*100,2),0) AS 'CNT_DEPOSIT_RATIO',
					ROUND(
							(
								SUM(
									IF(`RDET_ID` <> '40121', 
										(IFNULL(NBI.`NBI_INSU_DEPOSIT_PRICE`, 0) + IFNULL(NBI.`NBI_INSU_ADD_DEPOSIT_PRICE`, 0) + IFNULL(NBI.`NBI_CAROWN_DEPOSIT_PRICE`, 0) + IFNULL(NBI.`NBI_OTHER_PRICE`, 0)),
									 	0)
									)
								/SUM($NB_RATIO_PRICE)*100
							)
					,2) AS 'PRICE_DEPOSIT_RATIO',	
					IFNULL(SUM(IF(RFS_ID='90230',1,0)),0) AS 'SEND',
					IFNULL(SUM(IF(RFS_ID='90220',1,0)),0) AS 'SEND_READY',
					IFNULL(SUM(IF(RFS_ID='90210',1,0)),0) AS 'SEND_NOT',
					IFNULL(SUM(IF(RFS_ID='90290',1,0)),0) AS 'SEND_FAIL',
					IFNULL(SUM(IF(`NB_DUPLICATED_YN`='Y',1,0)),0) AS 'DUPLI'
				
				FROM
					`NT_BILL`.`NBB_BILL` NB,
					`NT_BILL`.`NBB_BILL_INSU` NBI,
					`NT_MAIN`.`NMG_GARAGE` NG,
					`NT_MAIN`.`NMG_GARAGE_SUB` NGS
					$subTable
				WHERE
					NB.`NB_ID` = NBI.`NB_ID`
				AND	NB.`MGG_ID` = NG.`MGG_ID`
				AND	NB.`MGG_ID` = NGS.`MGG_ID`
				AND	NB.DEL_YN = 'N'
				AND NG.DEL_YN = 'N'
				$tmpKeyword
		";

		// echo $iSQL;
		$RS= DBManager::getRecordSet($iSQL);	//검색조건 미반영

		$rtnArray = array(
							"CNT"=>$RS['CNT'],
							"CNT_DOUBLE"=>$RS['CNT_DOUBLE'],
							"PRICE"=>$RS['PRICE'],
							"CNT_DEPOSIT"=>$RS['CNT_DEPOSIT'],
							"CNT_DEPOSIT_DOUBLE"=>$RS['CNT_DEPOSIT_DOUBLE'],
							"DEPOSIT_PRICE"=>$RS['DEPOSIT_PRICE'],
							"CNT_DEPOSIT_RATIO"=>$RS['CNT_DEPOSIT_RATIO'],
							"PRICE_DEPOSIT_RATIO"=>$RS['PRICE_DEPOSIT_RATIO'],
							"SEND"=>$RS['SEND'],
							"SEND_READY"=>$RS['SEND_READY'],
							"SEND_NOT"=>$RS['SEND_NOT'],
							"SEND_FAIL"=>$RS['SEND_FAIL'],
							"DUPLI"=>$RS['DUPLI']
						
					);
		unset($RS);
		
		return  $rtnArray;
	}


	/**
	 * 데이터생성
	 */
	function setData($ARR) {
		if( !empty($ARR['NBI_OTHER_MII_ID']) || !empty($ARR['NBI_OTHER_RATIO']) ||
			!empty($ARR['NBI_OTHER_DT']) || !empty($ARR['NBI_OTHER_PRICE']) ||
			!empty($ARR['NBI_OTHER_MEMO']) || !empty($ARR['NBI_REASON_DTL_ID']) ){
				$iSQL .= "UPDATE `NT_BILL`.`NBB_BILL_INSU`
					SET 
						`NBI_OTHER_MII_ID` = NULL ,    
						`NBI_OTHER_RATIO` = NULL ,    
						`NBI_OTHER_DT` = NULL ,    
						`NBI_OTHER_PRICE` = NULL ,    
						`NBI_OTHER_MEMO` = NULL ,    
						`LAST_DTHMS` = NOW(),
						`WRT_USERID` = '".$_SESSION['USERID']."'
					WHERE `NB_ID` = '".addslashes($ARR['NB_ID'])."' AND `NBI_RMT_ID` = '".addslashes($ARR['NBI_RMT_ID'])."';
		";		
		}
		
		$iSQL .= "UPDATE `NT_BILL`.`NBB_BILL_INSU`
					SET ";
		if(isset($ARR['RDET_ID']) && !empty($ARR['RDET_ID']) && $ARR['RDET_ID'] != '40121'){ //입금
			$iSQL .= "`RDET_ID` = '".addslashes($ARR['RDET_ID'])."',
					`NBI_INSU_DEPOSIT_PRICE` = '".addslashes($ARR['NBI_INSU_DEPOSIT_PRICE'])."',
					`NBI_INSU_DEPOSIT_DT` = '".addslashes($ARR['NBI_INSU_DEPOSIT_DT'])."',	";
		}
		if($ARR['RDET_ID'] == '40121'){ //미입금
			$iSQL .= "`RDET_ID` = '".addslashes($ARR['RDET_ID'])."',
						`NBI_INSU_DEPOSIT_PRICE` = NULL ,
						`NBI_INSU_DEPOSIT_DT` = NULL ,	";
		}
		if(isset($ARR['RDT_ID']) && !empty($ARR['RDT_ID'])){ //입금여부상세
			$iSQL .= "`RDT_ID` = '".addslashes($ARR['RDT_ID'])."', ";
			if( $ARR['RDT_ID'] == '40114'){ //감액입금시 
				$iSQL .= "`NBI_INSU_ADD_DEPOSIT_PRICE` = '".addslashes($ARR['NBI_INSU_ADD_DEPOSIT_PRICE'])."',
						  `NBI_INSU_ADD_DEPOSIT_DT` = '".addslashes($ARR['NBI_INSU_ADD_DEPOSIT_DT'])."', 	";
			}
		}
		if(isset($ARR['NBI_INSU_DONE_YN']) && !empty($ARR['NBI_INSU_DONE_YN'])){ //종결여부
			$iSQL .= "`NBI_INSU_DONE_YN` = '".addslashes($ARR['NBI_INSU_DONE_YN'])."', 	";
		}
		if(isset($ARR['NBI_REASON_DTL_ID']) && !empty($ARR['NBI_REASON_DTL_ID'])){ // 사유입력 주체: 본사
			$iSQL .= "`NBI_REASON_DTL_ID` = '".addslashes($ARR['NBI_REASON_DTL_ID'])."',
					`NBI_REASON_DT` = NOW(),
					`RMT_ID` = '11510',
					`NBI_REASON_USERID` = '".$_SESSION['USERID']."',    ";
		}else {
			$iSQL .= "`NBI_REASON_DTL_ID` = NULL,
					`NBI_REASON_DT` = NULL,
					`RMT_ID` = NULL,
					`NBI_REASON_USERID` = NULL,   
					`NBI_OTHER_MII_ID` = NULL ,    
					`NBI_OTHER_RATIO` = NULL ,    
					`NBI_OTHER_DT` = NULL ,    
					`NBI_OTHER_PRICE` = NULL ,    
					`NBI_OTHER_MEMO` = NULL ,     ";
		}
		if(isset($ARR['NBI_OTHER_MII_ID']) && !empty($ARR['NBI_OTHER_MII_ID'])){ //(추가)타보험사
			$iSQL .= "`NBI_OTHER_MII_ID` = '".addslashes($ARR['NBI_OTHER_MII_ID'])."',    ";
		}
		if(isset($ARR['NBI_OTHER_RATIO']) && !empty($ARR['NBI_OTHER_RATIO'])){ //(추가)과실율
			$iSQL .= "`NBI_OTHER_RATIO` = '".addslashes($ARR['NBI_OTHER_RATIO'])."',    ";
		}
		if(isset($ARR['NBI_OTHER_DT']) && !empty($ARR['NBI_OTHER_DT'])){ //(추가)입금일
			$iSQL .= "`NBI_OTHER_DT` = '".addslashes($ARR['NBI_OTHER_DT'])."',    ";
		}
		if(isset($ARR['NBI_OTHER_PRICE']) && !empty($ARR['NBI_OTHER_PRICE'])){ //(추가)입금액
			$iSQL .= "`NBI_OTHER_PRICE` = '".addslashes($ARR['NBI_OTHER_PRICE'])."',    ";
		}
		if(isset($ARR['NBI_OTHER_MEMO']) && !empty($ARR['NBI_OTHER_MEMO'])){ //(추가)메모
			$iSQL .= "`NBI_OTHER_MEMO` = '".addslashes($ARR['NBI_OTHER_MEMO'])."',    ";
		}
		if(isset($ARR['NBI_PROC']) && !empty($ARR['NBI_PROC'])){
			$iSQL .= "`NBI_PROC_REASON_DT` = NOW(),
					`NBI_PROC_USERID` = '".$_SESSION['USERID']."',   ";
		}
		if(isset($ARR['DC_RATIO']) && !empty($ARR['DC_RATIO'])){
			$iSQL .= "`NBI_REQ_REASON_YN` = 'Y',
					`NBI_REQ_REASON_DTHMS` = NOW(),
					`NBI_REQ_REASON_USERID` = 'SYSTEM',
					`NBI_DEPOSIT_EXTRA1` = CONCAT('".addslashes($ARR['NBI_DEPOSIT_EXTRA1'])." DEPOSIT:".addslashes($ARR['NBI_INSU_DEPOSIT_DT'])." ".addslashes($ARR['NBI_INSU_DEPOSIT_PRICE'])." ".$ARR['DC_RATIO']."% ',NOW(),' ".$_SESSION['USERID']."', '/'),  \n";
		}
		$iSQL .= "	`LAST_DTHMS` = NOW(),
					`WRT_USERID` = '".$_SESSION['USERID']."'
					WHERE `NB_ID` = '".addslashes($ARR['NB_ID'])."' AND `NBI_RMT_ID` = '".addslashes($ARR['NBI_RMT_ID'])."';
		";		
		
		// echo "setData SQL\n".$iSQL."\n";
		return DBManager::execMulti($iSQL);
				
	}

	function setTrans($ARR){
		if(is_array($ARR)){
			foreach($ARR['TRANS_NB_ID'] as $index => $NB_ID){
				$iSQL .= "INSERT INTO `NT_BILL`.`NBB_BILL_INSU_TRANSFER`
						(
						`NB_ID`,
						`NBI_RMT_ID`,
						`NBIT_FROM_MII_ID`,
						`NBIT_FROM_NIC_ID`,
						`NBIT_FROM_NIC_NM_AES`,
						`NBIT_FROM_NIC_FAX_AES`,
						`NBIT_TO_MII_ID`,
						`NBIT_TO_NIC_ID`,
						`NBIT_TO_NIC_NM_AES`,
						`NBIT_TO_NIC_FAX_AES`,
						`NBIT_TO_NIC_PHONE_AES`,
						`NBIT_MEMO`,
						`WRT_DTHMS`,
						`LAST_DTHMS`,
						`WRT_USERID`)
						VALUES
						(
						'".addslashes($NB_ID)."',
						'".addslashes($ARR['TRANS_NBI_RMT_ID'][$index])."',
						'".addslashes($ARR['TRANS_MII_ID'][$index])."',
						'".addslashes($ARR['TRANS_NIC_ID'][$index])."',
						(AES_ENCRYPT('".addslashes($ARR['TRANS_NBIT_FROM_NIC_NM_AES'][$index])."',UNHEX(SHA2('".AES_SALT_KEY."',256))) ),
						(AES_ENCRYPT('".addslashes($ARR['TRANS_NBIT_FROM_NIC_FAX_AES'][$index])."',UNHEX(SHA2('".AES_SALT_KEY."',256))) ),
						'".addslashes($ARR['NBIT_TO_MII_ID'])."',
						'".addslashes($ARR['NBIT_TO_NIC_ID'])."',
						(AES_ENCRYPT('".addslashes($ARR['NBIT_TO_NIC_NM_AES'])."',UNHEX(SHA2('".AES_SALT_KEY."',256))) ),
						(AES_ENCRYPT('".addslashes($ARR['NBIT_TO_NIC_FAX_AES'])."',UNHEX(SHA2('".AES_SALT_KEY."',256))) ),
						(AES_ENCRYPT('".addslashes($ARR['NBIT_TO_NIC_PHONE_AES'])."',UNHEX(SHA2('".AES_SALT_KEY."',256))) ),
						'".addslashes($ARR['NBIT_MEMO'])."',
						NOW(),
						NOW(),
						'".$_SESSION['USERID']."');

				";
			}
			foreach($ARR['TRANS_NB_ID'] as $index => $NB_ID){
				$iSQL .= "UPDATE `NT_BILL`.`NBB_BILL_INSU`
							SET MII_ID = '".addslashes($ARR['NBIT_TO_MII_ID'])."',
								NIC_ID = '".addslashes($ARR['NBIT_TO_NIC_ID'])."',
								NBI_CHG_NM = '".addslashes($ARR['NBIT_TO_NIC_NM_AES'])."',
								NBI_FAX_NUM	= '".addslashes($ARR['NBIT_TO_NIC_FAX_AES'])."',
								LAST_DTHMS = NOW(),
								WRT_USERID = '".$_SESSION['USERID']."'
							WHERE NB_ID = '".addslashes($NB_ID)."'
							AND NBI_RMT_ID = '".addslashes($ARR['TRANS_NBI_RMT_ID'][$index])."';
	
				";
			}
			return DBManager::execMulti($iSQL);

		}else{
			$iSQL = "INSERT INTO `NT_BILL`.`NBB_BILL_INSU_TRANSFER`
					(
					`NB_ID`,
					`NBI_RMT_ID`,
					`NBIT_FROM_MII_ID`,
					`NBIT_FROM_NIC_ID`,
					`NBIT_FROM_NIC_NM_AES`,
					`NBIT_FROM_NIC_FAX_AES`,
					`NBIT_TO_MII_ID`,
					`NBIT_TO_NIC_ID`,
					`NBIT_TO_NIC_NM_AES`,
					`NBIT_TO_NIC_FAX_AES`,
					`NBIT_TO_NIC_PHONE_AES`,
					`NBIT_MEMO`,
					`WRT_DTHMS`,
					`LAST_DTHMS`,
					`WRT_USERID`)
					VALUES
					(
					'".addslashes($ARR['TRANS_NB_ID'])."',
					'".addslashes($ARR['TRANS_NBI_RMT_ID'])."',
					'".addslashes($ARR['TRANS_MII_ID'])."',
					'".addslashes($ARR['TRANS_NIC_ID'])."',
					(AES_ENCRYPT('".addslashes($ARR['TRANS_NBIT_FROM_NIC_NM_AES'])."',UNHEX(SHA2('".AES_SALT_KEY."',256))) ),
					(AES_ENCRYPT('".addslashes($ARR['TRANS_NBIT_FROM_NIC_FAX_AES'])."',UNHEX(SHA2('".AES_SALT_KEY."',256))) ),
					'".addslashes($ARR['NBIT_TO_MII_ID'])."',
					'".addslashes($ARR['NBIT_TO_NIC_ID'])."',
					(AES_ENCRYPT('".addslashes($ARR['NBIT_TO_NIC_NM_AES'])."',UNHEX(SHA2('".AES_SALT_KEY."',256))) ),
					(AES_ENCRYPT('".addslashes($ARR['NBIT_TO_NIC_FAX_AES'])."',UNHEX(SHA2('".AES_SALT_KEY."',256))) ),
					(AES_ENCRYPT('".addslashes($ARR['NBIT_TO_NIC_PHONE_AES'])."',UNHEX(SHA2('".AES_SALT_KEY."',256))) ),
					'".addslashes($ARR['NBIT_MEMO'])."',
					NOW(),
					NOW(),
					'".$_SESSION['USERID']."');

			";

			if($DBManager::execQuery($iSQL)){
				$iSQL ="UPDATE `NT_BILL`.`NBB_BILL_INSU`
					SET MII_ID = '".addslashes($ARR['NBIT_TO_MII_ID'])."',
						NIC_ID = '".addslashes($ARR['NBIT_TO_NIC_ID'])."',
						NBI_CHG_NM = '".addslashes($ARR['NBIT_TO_NIC_NM_AES'])."',
						NBI_FAX_NUM	='".addslashes($ARR['NBIT_TO_NIC_FAX_AES'])."',
						LAST_DTHMS = NOW(),
						WRT_USERID = '".$_SESSION['USERID']."'
					WHERE NB_ID = '".addslashes($ARR['TRANS_NB_ID'])."'
					AND NBI_RMT_ID = '".addslashes($ARR['TRANS_NBI_RMT_ID'])."';
	
				";
				return DBManager::execQuery($iSQL);
			}else {
				return ERROR;
			}
		}

		
		
	}

	/* 차주분 입금처리 */
	function setCarown($ARR){
		$iSQL = "UPDATE `NT_BILL`.`NBB_BILL_INSU`
					SET NBI_CAROWN_DEPOSIT_PRICE = '".addslashes($ARR['NBI_CAROWN_DEPOSIT_PRICE'])."',
						NBI_CAROWN_DEPOSIT_DT = '".addslashes($ARR['NBI_CAROWN_DEPOSIT_DT'])."',
						NBI_CAROWN_DEPOSIT_YN = 'Y',
						LAST_DTHMS = NOW(),
						WRT_USERID = '".$_SESSION['USERID']."'
					WHERE NB_ID = '".addslashes($ARR['NB_ID'])."'
					AND NBI_RMT_ID = '".addslashes($ARR['NBI_RMT_ID'])."';
			";
			// echo $iSQL;
		return DBManager::execQuery($iSQL);
	}

	/* 팀재분류 메모 저장*/
	function setTeamMemo($ARR){
		$iSQL = "";
		foreach($ARR['ARR_CHK'] as $index => $CHK_ARRAY){
			$CHK = explode('|', $CHK_ARRAY);
			$iSQL .= "UPDATE `NT_BILL`.`NBB_BILL_INSU`
					SET NBI_TEAM_MEMO = '".addslashes($ARR['NBI_TEAM_MEMO'])."',
						LAST_DTHMS = NOW(),
						WRT_USERID = '".$_SESSION['USERID']."'
					WHERE NB_ID = '".addslashes($CHK[0])."'
					AND NBI_RMT_ID = '".addslashes($CHK[1])."';
			";
		}
		return DBManager::execMulti($iSQL);
	}

	function setDecrement($ARR){
		// echo var_dump($ARR);
		$iSQL = "";
		if($ARR['NB_ID'] != '' && $ARR['NBI_RMT_ID'] != ''){
			$iSQL .= "UPDATE `NT_BILL`.`NBB_BILL_INSU`
						SET NBI_REQ_REASON_YN = 'Y',
							NBI_REQ_REASON_DTHMS = NOW(),
							NBI_REQ_REASON_USERID = '".$_SESSION['USERID']."',
							LAST_DTHMS = NOW(),
							WRT_USERID = '".$_SESSION['USERID']."'
						WHERE NB_ID = '".addslashes($ARR['NB_ID'])."'
						AND NBI_RMT_ID = '".addslashes($ARR['NBI_RMT_ID'])."';
				";
		}else {
			foreach($ARR['ALL_CHK'] as $index => $CHK_ARRAY){
				$CHK = explode('|', $CHK_ARRAY);
				$iSQL .= "UPDATE `NT_BILL`.`NBB_BILL_INSU`
						SET NBI_REQ_REASON_YN = 'Y',
							NBI_REQ_REASON_DTHMS = NOW(),
							NBI_REQ_REASON_USERID = '".$_SESSION['USERID']."',
							LAST_DTHMS = NOW(),
							WRT_USERID = '".$_SESSION['USERID']."'
						WHERE NB_ID = '".addslashes($CHK[0])."'
						AND NBI_RMT_ID = '".addslashes($CHK[1])."';
				";
			}
		}
		// echo $iSQL ;
		return DBManager::execMulti($iSQL);
	}

	/**
	 * data 삭제
	 * @param {$ARR}
	 * @param {NB_ID : 청구서 ID}
	 * @param {NBI_RMT_ID : 청구구분}
	 */
	function delData($ARR) {
		$iSQL = "";
		if($ARR['NB_ID'] != '' && $ARR['NBI_RMT_ID'] != ''){
			$iSQL .= "UPDATE `NT_BILL`.`NBB_BILL`
					SET  
						`LAST_DTHMS`= NOW(), 
						`WRT_USERID` = '".addslashes($_SESSION['USERID'])."',
						`NB_DEL_DTHMS` = NOW(),	
						`DEL_YN`	='Y'
					WHERE `NB_ID`= '".addslashes($ARR['NB_ID'])."';
			";
		}else {
			foreach($ARR['ALL_CHK'] as $index => $CHK_ARRAY){
				$CHK = explode('|', $CHK_ARRAY);
				$iSQL .= "UPDATE `NT_BILL`.`NBB_BILL`
						SET  
							`LAST_DTHMS`= NOW(), 
							`WRT_USERID` = '".addslashes($_SESSION['USERID'])."',
							`NB_DEL_DTHMS` = NOW(),
							`DEL_YN`	='Y'
						WHERE NB_ID = '".addslashes($CHK[0])."';
				";
			}
		}
		
		// echo 	$iSQL ;
		return DBManager::execMulti($iSQL);
	}
	

}
	// 0. 객체 생성
	$cDeposit = new cDeposit(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$cDeposit->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);
?>