<?php

class cBillBin extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	// Class Object Constructor
	function cBillBin($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
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
		
		/** 기간검색 */
		if($ARR['SEARCH_SELECT_DT'] != '') {
			if($ARR['SEARCH_START_DT'] !='' && $ARR['SEARCH_END_DT'] !=''){
				$tmpKeyword .= "AND DATE(`BNB`.".$ARR['SEARCH_SELECT_DT'].") BETWEEN '".addslashes($ARR['SEARCH_START_DT'])."' AND '".addslashes($ARR['SEARCH_END_DT'])."'";
			}else if($ARR['SEARCH_START_DT'] !='' && $ARR['SEARCH_END_DT'] ==''){
				$tmpKeyword .= "AND DATE(`BNB`.".$ARR['SEARCH_SELECT_DT'].") >= '".addslashes($ARR['SEARCH_START_DT'])."'";
			}else{
				$tmpKeyword .= "AND DATE(`BNB`.".$ARR['SEARCH_SELECT_DT'].") <= '".addslashes($ARR['SEARCH_END_DT'])."'";
			}
		} 

		// 보험사 검색
		if($ARR['SEARCH_INSU_NM'] != ''){
			$tmpKeyword .= "AND MNI.MII_ID IN (";
			foreach ($ARR['SEARCH_INSU_NM'] as $index => $MII_ARRAY) {
				$tmpKeyword .=" '".$MII_ARRAY."',";
			}
			$tmpKeyword=substr($tmpKeyword, 0, -1); // 마지막 콤마제거
			$tmpKeyword .= ") ";
		}
		// 보험사팀 검색
		if($ARR['SEARCH_INSU_TEAM'] != ''){
			$tmpKeyword .= " AND (CONCAT(MNI.MII_ID,MNG.CRR_1_ID,MNG.CRR_2_ID,MNG.CRR_3_ID) IN 
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
				) OR (`NT_MAIN`.`GET_INSU_CHG_TEAM_ID`(BNBI.NIC_ID) IN (";
			foreach ($ARR['SEARCH_INSU_TEAM'] as $index => $MIT_ARRAY) {
				$tmpKeyword .=" '".$MIT_ARRAY."',";
			}
			$tmpKeyword=substr($tmpKeyword, 0, -1); // 마지막 콤마제거
			$tmpKeyword .= ") \n  )\n  )\n";
		}

		//보상담당
		if($ARR['SEARCH_NIC_NM'] != '') {	
			$tmpKeyword .= "AND INSTR(`NT_MAIN`.`GET_INSU_CHG_NM`(BNBI.NIC_ID, '".AES_SALT_KEY."') ,'".addslashes($ARR['SEARCH_NIC_NM'])."')>0 ";	
		} 

		
		// 구분검색
        if($ARR['CSD_ID_SEARCH'] != ''){
			$tmpKeyword .= "AND MNG.CSD_ID IN (";
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
					$tmpKeyword .= "`MNG`.`CRR_1_ID` = '".$CSD[0]."' \n";
				}else{
					$tmpKeyword .= " OR(";
					$tmpKeyword .= "  `MNG`.`CRR_1_ID` = '".$CSD[0]."' \n";
                }
                if($CSD[1] != NOT_CRR_2){
                    $tmpKeyword .= "AND `MNG`.`CRR_2_ID` = '".$CSD[1]."' \n";
                }
				
				if($CSD[2] != NOT_CRR_3){
					$tmpKeyword .= "AND `MNG`.`CRR_3_ID` = '".$CSD[2]."' \n";
				}
				
				$tmpKeyword .= ") \n";
			}
			$tmpKeyword .= ") \n";
        }
        
		//공업사명검색
		if($ARR['SEARCH_BIZ_NM'] != '') {
			$tmpKeyword .= "AND BNB.MGG_ID  = '".$ARR['SEARCH_BIZ_NM']."' \n";
		} 
		
		
		//청구식검색
		if($ARR['SEARCH_RET_ID'] != '') {
			$tmpKeyword .= "AND MNGS.RET_ID IN ( ";
            foreach ($ARR['SEARCH_RET_ID'] as $index => $RET_ID) {
                $tmpKeyword .=" '".$RET_ID."',";
            }
            $tmpKeyword = substr($tmpKeyword, 0, -1); // 마지막 콤마제거
            $tmpKeyword .= ") \n";
		}

		//작성 주체 
		if($ARR['SEARCH_RMT_ID'] != '') {
			// $tmpKeyword .= "AND BNB.RMT_ID = '".addslashes($ARR['SEARCH_RMT_ID'])."' ";	
			$tmpKeyword .= " AND   (SELECT MODE_TARGET FROM `NT_MAIN`.`V_ALL_USER_INFO` WHERE USERID = BNB.NB_CLAIM_USERID)  = '".addslashes($ARR['SEARCH_RMT_ID'])."'";
		} 

		// 작성아이디
		if($ARR['SEARCH_CLAIM_USERID'] != '') {
			$tmpKeyword .= "AND INSTR(`BNB`.`NB_CLAIM_USERID`,'".addslashes($ARR['SEARCH_CLAIM_USERID'])."')>0 ";	
		} 

		// 담보
		if($ARR['SEARCH_RLT_ID'] != '') {
			$tmpKeyword .= "AND BNB.RLT_ID = '".addslashes($ARR['SEARCH_RLT_ID'])."' ";	
		} 

		//차량급
		if($ARR['SEARCH_CAR_TYPE'] != '') {
			$subTable .= ", `NT_CODE`.`NCC_CAR` NC";
			$tmpKeyword .= "AND BNB.`CCC_ID`= NC.`CCC_ID`" ;
			$tmpKeyword .= "AND NC.CCC_CAR_TYPE_CD= '".addslashes($ARR['SEARCH_CAR_TYPE'])."' ";	
		} 

		//사용부품
		if($ARR['SEARCH_USE_COMP'] != '') {
			$tmpKeyword .= "AND INSTR(`NT_BILL`.`GET_BILL_PART_NMS`(BNB.NB_ID, FALSE, FALSE) , '".$ARR['SEARCH_USE_COMP']."') > 0 ";	
		} 

		// 차량정보
		if($ARR['SEARCH_CAR_INFO'] != ''){
			if($ARR['SEARCH_CAR_INFO'] == 'NB_CAR_NUM'){
				$tmpKeyword .= "AND INSTR(`BNB`.`NB_CAR_NUM`,'".addslashes($ARR['KEYWORD_CAR_INFO'])."')>0 ";	
			}else if($ARR['SEARCH_CAR_INFO'] == 'CCC_CAR_NM'){
				$tmpKeyword .= "AND INSTR(NT_CODE.GET_CAR_NM(`BNB`.`CCC_ID`), '".addslashes($ARR['KEYWORD_CAR_INFO'])."')>0 ";	
			}

		}

		// 삭제사유
		if($ARR['SEARCH_DEL_REASON'] != '') {
			$tmpKeyword .= "AND INSTR(`BNB`.`NB_DEL_REASON`,'".addslashes($ARR['SEARCH_DEL_REASON'])."')>0 ";	
		} 


		// 페이징처리를 위해 전체데이터
		$iSQL = "SELECT
					COUNT(*) AS CNT
				FROM 
					`NT_BILL`.`NBB_BILL` BNB,
					`NT_BILL`.`NBB_BILL_INSU` BNBI,
					`NT_MAIN`.`NMG_GARAGE` MNG,
					`NT_MAIN`.`NMG_GARAGE_SUB` MNGS,
					`NT_MAIN`.`NMI_INSU` MNI
					$subTable
				WHERE BNB.`NB_ID` = BNBI.`NB_ID`
				AND BNB.`MGG_ID` = MNG.`MGG_ID`
				AND BNB.`MGG_ID` = MNGS.`MGG_ID`
				AND BNBI.`MII_ID` = MNI.`MII_ID`
				AND BNB.DEL_YN = 'Y' 
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
					BNB.`NB_ID`,-- 청구서ID
					MNG.`MGG_ID`,-- 공업사코드
					BNB.`NB_CLAIM_DT`,-- 청구일
					MNI.`MII_NM`,-- 보험사
					IFNULL(`NT_MAIN`.`GET_INSU_CHG_TEAM_NM`(BNBI.NIC_ID), IFNULL(NT_MAIN.GET_MIT_NM_BY_CRR(MNI.MII_ID, MNG.MGG_ID),'')) AS MIT_NM,-- 보험사팀
					IFNULL(BNBI.`NBI_TEAM_MEMO`, '') AS NBI_TEAM_MEMO,-- 팀메모
					BNBI.`NBI_CHG_NM`,-- 보상담당
					BNBI.`NBI_FAX_NUM`,-- 팩스번호
					IFNULL(`NT_MAIN`.`GET_INSU_CHG_PHONE`(BNBI.`NIC_ID`, '".AES_SALT_KEY."'),'') AS 'NIC_TEL1_AES',
					(SELECT `CSD_NM` FROM NT_CODE.NCS_DIVISION WHERE `CSD_ID` = `MNG`.`CSD_ID`)AS CSD_NM,-- 구분
					`NT_CODE`.`GET_RGN_NM`(MNG.`CRR_1_ID`,MNG.`CRR_2_ID`,MNG.`CRR_3_ID`,TRUE) AS `RGN_NM`,
					BNB.`NB_GARAGE_NM`,-- 공업사
					NT_MAIN.GET_MGG_NM(BNB.MGG_ID)AS MGG_NM,
					MNG.`MGG_BIZ_ID`,-- 사업자번호
					IFNULL(NT_CODE.GET_CODE_NM(`MNGS`.`RET_ID`), '') AS `RET_NM`,-- 청구식
					NT_CODE.GET_CODE_NM(`MNGS`.`RPET_ID`) AS `RPET_ID`, -- 지급식
					MNGS.`MGG_FRONTIER_NM`,-- 개척자
					MNGS.`MGG_BIZ_START_DT`,-- 개시일
					IFNULL(MNGS.`MGG_BIZ_END_DT`, '') AS MGG_BIZ_END_DT,-- 종료일
					-- NT_CODE.GET_CODE_NM(BNB.`RMT_ID`) AS NB_WRT_RMT,-- 작성
					IFNULL(SUBSTRING(  IF(NT_CODE.GET_CODE_NM((SELECT MODE_TARGET FROM `NT_MAIN`.`V_ALL_USER_INFO` WHERE USERID = BNB.NB_CLAIM_USERID  LIMIT 1))='전체',
                                                '본',
					NT_CODE.GET_CODE_NM((SELECT MODE_TARGET FROM `NT_MAIN`.`V_ALL_USER_INFO` WHERE USERID = BNB.NB_CLAIM_USERID  LIMIT 1))) ,1,1),'') AS 'NB_WRT_RMT',
					BNB.`NB_CLAIM_USERID`,-- 작성아이디 : 최초청구자 USERID
					IFNULL(NT_BILL.`GET_BILL_BOTH_WRITER`(BNB.RLT_ID,BNB.RMT_ID,BNB.NB_ID), '') AS INSU_BOTH_WRITER, -- 쌍방작성사
					BNBI.`NBI_PRECLAIM_YN`,-- 선청구
					BNBI.`NBI_REGI_NUM`,-- 보험접수번호
					NT_CODE.GET_CODE_NM(BNB.RLT_ID) AS `RLT_NM`, -- 담보
					BNB.`NB_CAR_NUM`,-- 차량번호
					NT_CODE.GET_CAR_NM(`BNB`.`CCC_ID`) AS `CCC_CAR_NM`,-- 차량명
					NT_CODE.GET_CAR_TYPE_NM(`BNB`.`CCC_ID`) AS `RCT_NM`,-- 차량급
					IFNULL(`NT_BILL`.`GET_BILL_PART_NMS`(BNB.NB_ID, TRUE, FALSE), '') AS `BILL_PART_NMS`, -- 사용부품 청구서ID, TRUE 기준부품 FALSE 취급부품, TRUE 풀네임 FALSE 약어
					IFNULL(`NT_BILL`.`GET_NB_WORK_TIME`(BNB.NB_ID), '') AS NB_WORK_TIME, -- 방청시간
					BNB.`NB_TOTAL_PRICE`,-- 총 청구금액
					IFNULL(BNBI.`NBI_INSU_DEPOSIT_DT`, '') AS NBI_INSU_DEPOSIT_DT,-- 입금일
					IFNULL(BNBI.`NBI_INSU_DEPOSIT_PRICE`, '') AS NBI_INSU_DEPOSIT_PRICE,-- 입금액
					`NT_BILL`.`GET_DEPOSIT_YN`(RDET_ID) AS NBI_INSU_DEPOSIT_YN,-- BNBI.`NBI_INSU_DEPOSIT_YN`,-- 입금여부
					BNB.`NB_DEL_DTHMS`,-- 삭제일
					NT_BILL.GET_RFS_NM(BNBI.RFS_ID) AS RFS_NM,
					IFNULL(BNBI.`NBI_FAX_SEND_DTHMS`, '') AS NBI_FAX_SEND_DTHMS,-- 전송일시
					ROUND(IF(BNB.`NB_NEW_INSU_YN` = 'Y',`NB_TOTAL_PRICE`*0.2,0),0) AS NB_CAROWN_TOTAL_PRICE, -- 차주분청구액
					IFNULL(BNBI.`NBI_CAROWN_DEPOSIT_DT`, '') AS NBI_CAROWN_DEPOSIT_DT,-- 차주분입금일
					IFNULL(BNBI.`NBI_CAROWN_DEPOSIT_PRICE`, '') AS NBI_CAROWN_DEPOSIT_PRICE,-- 차주분입금액
					IFNULL(BNBI.`NBI_CAROWN_DEPOSIT_YN`, '') AS NBI_CAROWN_DEPOSIT_YN, -- 차주분입금여부
					BNB.`NB_DEL_REASON`-- 삭제사유
				FROM 
					`NT_BILL`.`NBB_BILL` BNB,
					`NT_BILL`.`NBB_BILL_INSU` BNBI,
					`NT_MAIN`.`NMG_GARAGE` MNG,
					`NT_MAIN`.`NMG_GARAGE_SUB` MNGS,
					`NT_MAIN`.`NMI_INSU` MNI
					$subTable
				WHERE BNB.`NB_ID` = BNBI.`NB_ID`
				AND BNB.`MGG_ID` = MNG.`MGG_ID`
				AND BNB.`MGG_ID` = MNGS.`MGG_ID`
				AND BNBI.`MII_ID` = MNI.`MII_ID`
				AND BNB.DEL_YN = 'Y' 
					 $tmpKeyword 
				ORDER BY NB_DEL_DTHMS DESC
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
				"NB_ID"=>$RS['NB_ID'],
				"MGG_ID"=>$RS['MGG_ID'],
				"MGG_NM"=>$RS['MGG_NM'],
				"NB_CLAIM_DT"=>$RS['NB_CLAIM_DT'],
				"MII_NM"=>$RS['MII_NM'],
				"MIT_NM"=>$RS['MIT_NM'],
				"NBI_TEAM_MEMO"=>$RS['NBI_TEAM_MEMO'],
				"NBI_CHG_NM"=>$RS['NBI_CHG_NM'],
				"NBI_FAX_NUM"=>$RS['NBI_FAX_NUM'],
				"NIC_TEL1"=>$RS['NIC_TEL1_AES'],
				"CSD_NM"=>$RS['CSD_NM'],
				"RGN_NM"=>$RS['RGN_NM'],
				"NB_GARAGE_NM"=>$RS['NB_GARAGE_NM'],
				"MGG_BIZ_ID"=>$RS['MGG_BIZ_ID'],
				"RET_NM"=>$RS['RET_NM'],
				"RPET_ID"=>$RS['RPET_ID'],
				"MGG_FRONTIER_NM"=>$RS['MGG_FRONTIER_NM'],
				"MGG_BIZ_START_DT"=>$RS['MGG_BIZ_START_DT'],
				"MGG_BIZ_END_DT"=>$RS['MGG_BIZ_END_DT'],
				"NB_WRT_RMT"=>$RS['NB_WRT_RMT'],
				"NB_CLAIM_USERID"=>$RS['NB_CLAIM_USERID'],
				"INSU_BOTH_WRITER"=>$RS['INSU_BOTH_WRITER'],
				"NBI_PRECLAIM_YN"=>$RS['NBI_PRECLAIM_YN'],
				"NBI_REGI_NUM"=>$RS['NBI_REGI_NUM'],
				"RLT_NM"=>$RS['RLT_NM'],
				"NB_CAR_NUM"=>$RS['NB_CAR_NUM'],
				"CCC_CAR_NM"=>$RS['CCC_CAR_NM'],
				"RCT_NM"=>$RS['RCT_NM'],
				"BILL_PART_NMS"=>$RS['BILL_PART_NMS'],
				"NB_WORK_TIME"=>$RS['NB_WORK_TIME'],
				"NB_TOTAL_PRICE"=>$RS['NB_TOTAL_PRICE'],
				"NBI_INSU_DEPOSIT_DT"=>$RS['NBI_INSU_DEPOSIT_DT'],
				"NBI_INSU_DEPOSIT_PRICE"=>$RS['NBI_INSU_DEPOSIT_PRICE'],
				"NBI_INSU_DEPOSIT_YN"=>$RS['NBI_INSU_DEPOSIT_YN'],
				"NB_DEL_DTHMS"=>$RS['NB_DEL_DTHMS'],
				"RFS_NM"=>$RS['RFS_NM'],
				"NBI_FAX_SEND_DTHMS"=>$RS['NBI_FAX_SEND_DTHMS'],
				"NB_CAROWN_TOTAL_PRICE"=>$RS['NB_CAROWN_TOTAL_PRICE'],
				"NBI_CAROWN_DEPOSIT_DT"=>$RS['NBI_CAROWN_DEPOSIT_DT'],
				"NBI_CAROWN_DEPOSIT_PRICE"=>$RS['NBI_CAROWN_DEPOSIT_PRICE'],
				"NBI_CAROWN_DEPOSIT_YN"=>$RS['NBI_CAROWN_DEPOSIT_YN'],
				"NB_DEL_REASON"=>$RS['NB_DEL_REASON']
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
	 * 데이터업데이트
	 */
	function setData($ARR) {
		$iSQL = "";
		if($ARR['ALL_CHK'] != ''){
			foreach($ARR['ALL_CHK'] as $index => $CHK_ARRAY){
				$iSQL .= "UPDATE `NT_BILL`.`NBB_BILL`
						SET
							-- `LAST_DTHMS`= NOW(),
							`WRT_USERID`= '".addslashes($_SESSION['USERID'])."',
							`DEL_YN`	='N'
						WHERE `NB_ID` = '".addslashes($CHK_ARRAY)."';
				";
			}

		} else if ($ARR['NB_ID'] != ''){
				$iSQL = "UPDATE `NT_BILL`.`NBB_BILL`
						SET
							-- `LAST_DTHMS`= NOW(),
							`WRT_USERID`= '".addslashes($_SESSION['USERID'])."',
							`DEL_YN`	='N'
						WHERE `NB_ID` = '".addslashes($ARR['NB_ID'])."';
				";
		}
		
		// echo 	$iSQL ;
		return DBManager::execMulti($iSQL);
	}


	function delData($ARR) {
		$iSQL = "";
		if($ARR['ALL_CHK'] != ''){
			foreach($ARR['ALL_CHK'] as $index => $CHK_ARRAY){
				$chkId .=" '".$CHK_ARRAY."',";
            }
            $chkId=substr($chkId, 0, -1); // 마지막 콤마제거
			
			$iSQL .= "DELETE FROM `NT_BILL`.`NBB_BILL`
						WHERE `NB_ID` IN (".$chkId.");
				";

		} else if ($ARR['NB_ID'] != ''){
				$iSQL = "DELETE FROM  `NT_BILL`.`NBB_BILL`
						WHERE `NB_ID` = '".addslashes($ARR['NB_ID'])."';
				";
		}
		
		// echo 	$iSQL ;
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




	/** 차량타입 가져오기 */
	function getCarType(){
		$iSQL = "SELECT 
					`RCT_CD`,
					`RCT_NM`
				FROM `NT_CODE`.`REF_CAR_TYPE`
				;"
		;
		// echo $iSQL."\n";
		$Result= DBManager::getResult($iSQL);
		///

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
				"RCT_CD"=>$RS['RCT_CD'],
				"RCT_NM"=>$RS['RCT_NM'],
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

	

	



}
	// 0. 객체 생성
	$cBillBin = new cBillBin(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$cBillBin->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
