<?php

class cCarCompList extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	// Class Object Constructor
	function cCarCompList($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
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
				$tmpKeyword .= " AND DATE(`NQ`.".$ARR['SEARCH_SELECT_DT'].") BETWEEN '".addslashes($ARR['SEARCH_START_DT'])."' AND '".addslashes($ARR['SEARCH_END_DT'])."'";
			}else if($ARR['SEARCH_START_DT'] !='' && $ARR['SEARCH_END_DT'] == ''){
				$tmpKeyword .= " AND DATE(`NQ`.".$ARR['SEARCH_SELECT_DT'].") >= '".addslashes($ARR['SEARCH_START_DT'])."'";
			}else if($ARR['SEARCH_START_DT'] =='' && $ARR['SEARCH_END_DT'] != ''){
				$tmpKeyword .= " AND DATE(`NQ`.".$ARR['SEARCH_SELECT_DT'].") <= '".addslashes($ARR['SEARCH_END_DT'])."'";
			}else{
				$tmpKeyword .= "";
			}
		} 

		// 구분검색
        if($ARR['CSD_ID_SEARCH'] != ''){
			$tmpKeyword .= " AND MGG.CSD_ID IN (";
			foreach ($ARR['CSD_ID_SEARCH'] as $index => $CSD_ARRAY) {
				$tmpKeyword .=" '".$CSD_ARRAY."',";
			}
			$tmpKeyword=substr($tmpKeyword, 0, -1); // 마지막 콤마제거
			$tmpKeyword .= ") \n";
        }
		
		//지역검색
        if($ARR['CRR_ID_SEARCH'] !=  '' || $ARR['CRR_ID_SEARCH'] != null) {

            $tmpKeyword .= " AND (";
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
                    $tmpKeyword .= " AND `MGG`.`CRR_2_ID` = '".$CSD[1]."' \n";
                }
				
				if($CSD[2] != NOT_CRR_3){
					$tmpKeyword .= " AND `MGG`.`CRR_3_ID` = '".$CSD[2]."' \n";
				}
				
				$tmpKeyword .= ") \n";
			}
			$tmpKeyword .= ") \n";
        }
        
		//공업사명검색
		if($ARR['SEARCH_BIZ_NM'] != '') {
			$tmpKeyword .= " AND NQ.MGG_ID  = '".$ARR['SEARCH_BIZ_NM']."' \n";
		} 

		//청구식검색
		if($ARR['SEARCH_RET_ID'] != '') {
			$tmpKeyword .= " AND MGGS.RET_ID IN ( ";
            foreach ($ARR['SEARCH_RET_ID'] as $index => $RET_ID) {
                $tmpKeyword .=" '".$RET_ID."',";
            }
            $tmpKeyword = substr($tmpKeyword, 0, -1); // 마지막 콤마제거
            $tmpKeyword .= ") \n";
		}

		// 보험사 검색
		if($ARR['SEARCH_INSU_NM'] != ''){
			$tmpKeyword .= " AND MII.MII_ID = '".addslashes($ARR['SEARCH_INSU_NM'])."' ";
		}

		// 담보
		if($ARR['SEARCH_RLT_ID'] != '') {
			$tmpKeyword .= " AND NQ.RLT_ID = '".addslashes($ARR['SEARCH_RLT_ID'])."' ";	
		} 

		// 청구여부
		if($ARR['SEARCH_CLAIM_YN'] != '') {
			if($ARR['SEARCH_CLAIM_YN'] == YN_Y){
				$tmpKeyword .= " AND NB.NB_ID IS NOT NULL";
			}else{
				$tmpKeyword .= " AND NB.NB_ID IS NULL";
			}
			
			// $tmpKeyword .= "AND NQ.NQ_INC_DTL_YN = '".addslashes($ARR['SEARCH_CLAIM_YN'])."' ";	
		} 

		// 차량정보
		if($ARR['SEARCH_CAR_INFO'] != ''){
			if($ARR['SEARCH_CAR_INFO'] == 'NQI_REGI_NUM'){
				$tmpKeyword .= " AND INSTR(`NQI`.`NQI_REGI_NUM`,'".addslashes($ARR['KEYWORD_CAR_INFO'])."')>0 ";	
			}else if($ARR['SEARCH_CAR_INFO'] == 'NQ_CAR_NUM'){
				$tmpKeyword .= " AND INSTR(`NQ`.`NQ_CAR_NUM`,'".addslashes($ARR['KEYWORD_CAR_INFO'])."')>0 ";	
			}else if($ARR['SEARCH_CAR_INFO'] == 'CCC_CAR_NM'){
				$tmpKeyword .= " AND INSTR(NT_CODE.GET_CAR_NM(`NQ`.`CCC_ID`), '".addslashes($ARR['KEYWORD_CAR_INFO'])."')>0 ";	
			}else{
				$tmpKeyword .= "";
			}

		}

		// 페이징처리를 위해 전체데이터
		$iSQL = "SELECT
					COUNT(*) AS CNT
				FROM 
					`NT_BILL`.`NBB_QUOTE_INSU` NQI,
					`NT_MAIN`.`NMG_GARAGE` MGG,
					`NT_MAIN`.`NMG_GARAGE_SUB` MGGS,
					`NT_MAIN`.`NMI_INSU` MII,
					`NT_BILL`.`NBB_QUOTE` NQ
				LEFT JOIN  `NT_BILL`.`NBB_BILL` NB
					ON NB.NB_ID = NQ.NQ_ID
				WHERE NQ.`NQ_ID` = NQI.`NQ_ID`
				AND NQ.`MGG_ID` = MGG.`MGG_ID`
				AND NQ.`MGG_ID` = MGGS.`MGG_ID`
				AND NQI.`MII_ID` = MII.`MII_ID`
				AND NQ.DEL_YN = 'N' 
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
					NQ.`NQ_ID`,-- 청구서ID
					NQ.`MGG_ID`,-- 공업사코드
					DATE_FORMAT(NQ.`WRT_DTHMS`, '%Y-%m-%d') AS WRT_DTHMS, -- 견적일
					`NT_CODE`.`GET_CSD_NM`(MGG.`CSD_ID`) AS CSD_NM,-- 구분
					`NT_CODE`.`GET_RGN_NM`(MGG.`CRR_1_ID`,MGG.`CRR_2_ID`,MGG.`CRR_3_ID`,TRUE) AS CRR_NM, -- 지역
					NQ.`NQ_GARAGE_NM`,-- 견적당시 공업사명
					MGG.`MGG_BIZ_ID`,-- 사업자번호
					NT_CODE.GET_CODE_NM(`RET_ID`) AS RET_ID, -- 청구식
					MGGS.`MGG_OFFICE_TEL`, -- 사무실 전화
					MII.`MII_NM`,-- 보험사
					IFNULL(`NT_MAIN`.`GET_INSU_CHG_TEAM_NM`(NQI.NIC_ID), IFNULL(NT_MAIN.GET_MIT_NM_BY_CRR(MII.MII_ID, NQ.MGG_ID),'')) AS MIT_NM,-- 보험사팀
					NQI.`NQI_CHG_NM`,-- 보상담당
					`NT_MAIN`.`GET_INSU_CHG_PHONE`(NQI.`NIC_ID`, '".AES_SALT_KEY."') AS 'NIC_TEL1_AES',
					NQI.`NQI_REGI_NUM`,-- 보험접수번호
					NT_CODE.GET_CODE_NM(NQ.RLT_ID) AS `RLT_NM`, -- 담보
					NQ.`NQ_CAR_NUM`,-- 차량번호
					NT_CODE.GET_CAR_NM(`NQ`.`CCC_ID`) AS `CCC_CAR_NM`,-- 차량명
					NQ.`NQ_INC_DTL_YN`,-- 청구여부 (세부사항 포함 전송여부)
					IF(DATE(NQ.`NQ_CLAIM_DT`) = DATE('0000-00-00') , '-' , NQ.`NQ_CLAIM_DT`) AS NQ_CLAIM_DT , -- 청구일
					NQI.NBI_RMT_ID,
					MGGS.`MGG_INSU_MEMO`, -- 보험사협력업체
					MGGS.`MGG_MANUFACTURE_MEMO`, -- 제조사협력업체
                    NQI.NQI_FAX_NUM,
					IF(NB.NB_ID IS NULL, 'N', 'Y') AS CLAIM_YN,  -- 청구여부
					ROUND(IF(NQI.NQI_RATIO='', NQ.NQ_TOTAL_PRICE, NQ.NQ_TOTAL_PRICE*(NQI.NQI_RATIO/100))) AS NQ_TOTAL_PRICE, -- 견적금액(총 청구금액)
					IF(NQI.NBI_RMT_ID = '11520' , NT_BILL.GET_NQ_OWNER_CLAIM_PRICE(NQ.RLT_ID, NQ.NQ_ID, NQ.NQ_NEW_INSU_YN) , 0)AS OWNER_PRICE -- 차주부담금액(차주분청구액)
				FROM 
					`NT_MAIN`.`NMG_GARAGE` MGG,
					`NT_MAIN`.`NMG_GARAGE_SUB` MGGS,
					`NT_MAIN`.`NMI_INSU` MII,
					`NT_BILL`.`NBB_QUOTE_INSU` NQI,
					`NT_BILL`.`NBB_QUOTE` NQ
				LEFT JOIN  `NT_BILL`.`NBB_BILL` NB
					ON NB.NB_ID = NQ.NQ_ID
				WHERE NQ.`NQ_ID` = NQI.`NQ_ID`
				AND NQ.`MGG_ID` = MGG.`MGG_ID`
				AND NQ.`MGG_ID` = MGGS.`MGG_ID`
				AND NQI.`MII_ID` = MII.`MII_ID`
				AND NQ.DEL_YN = 'N' 
				$tmpKeyword 
				ORDER BY NQ.WRT_DTHMS DESC
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
				"NQ_ID"=>$RS['NQ_ID'],
				"MGG_ID"=>$RS['MGG_ID'],
				"WRT_DTHMS"=>$RS['WRT_DTHMS'],
				"CSD_NM"=>$RS['CSD_NM'],
				"CRR_NM"=>$RS['CRR_NM'],
				"NQ_GARAGE_NM"=>$RS['NQ_GARAGE_NM'],
				"MGG_BIZ_ID"=>$RS['MGG_BIZ_ID'],
				"RET_ID"=>$RS['RET_ID'],
				"MGG_OFFICE_TEL"=>$RS['MGG_OFFICE_TEL'],
				"MII_NM"=>$RS['MII_NM'],
				"MIT_NM"=>$RS['MIT_NM'],
				"NBI_RMT_ID"=>$RS['NBI_RMT_ID'],
				"NQI_CHG_NM"=>$RS['NQI_CHG_NM'],
				"NIC_TEL1"=>$RS['NIC_TEL1_AES'],
				"NQI_REGI_NUM"=>$RS['NQI_REGI_NUM'],
				"RLT_NM"=>$RS['RLT_NM'],
				"NQI_FAX_NUM"=>$RS['NQI_FAX_NUM'],
				"NQ_CAR_NUM"=>$RS['NQ_CAR_NUM'],
				"CCC_CAR_NM"=>$RS['CCC_CAR_NM'],
				"NQ_TOTAL_PRICE"=>$RS['NQ_TOTAL_PRICE'],
				"NQ_CAROWN_TOTAL_PRICE"=>$RS['NQ_CAROWN_TOTAL_PRICE'],
				"NQ_INC_DTL_YN"=>$RS['NQ_INC_DTL_YN'],
				"NQ_CLAIM_DT"=>$RS['NQ_CLAIM_DT'],
				"MGG_INSU_MEMO"=>$RS['MGG_INSU_MEMO'],
				"CLAIM_YN"=>$RS['CLAIM_YN'],
				"OWNER_PRICE"=>$RS['OWNER_PRICE'],
				"MGG_MANUFACTURE_MEMO"=>$RS['MGG_MANUFACTURE_MEMO']
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
	 * data 삭제
	 * @param {$ARR}
	 * @param {ALLM_ID : 삭제할 해당 데이터 ID}
	 */
	function delData($ARR) {
		$iSQL = "UPDATE `NT_BILL`.`NBB_QUOTE`
					SET  
						`LAST_DTHMS`= NOW(), 
						`WRT_USERID` = '".addslashes($_SESSION['USERID'])."',
						`DEL_YN`	='Y'
					WHERE `NQ_ID`='".addslashes($ARR['NQ_ID'])."';
		";
		
		// echo 	$iSQL ;
		return DBManager::execQuery($iSQL);
	}

	/** 청구될 견적서가 NBB_BILL에 이미 존재하는지..존재하면 쌍방건으로 간주하고 패스 */
	function isExistNQ_ID($ARR){

		$iSQL = "SELECT COUNT(NB_ID) AS CNT
				 FROM  NT_BILL.NBB_BILL
				 WHERE NB_ID = '".addslashes($ARR['NQ_ID'])."'
			;"
		;
		// echo $iSQL;
		$Result= DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"CNT"=>$RS['CNT'],
			));
		}
		$Result->close();  // 자원 반납

		if(empty($rtnArray)) {
			true;
		} else {
			return false;
		}
	}


	/**
	 * 견적서 청구
	 */
	function setData($ARR) {
		if($ARR['REQ_MODE'] == CASE_CREATE){
			$iSQL = "INSERT INTO NT_BILL.NBB_BILL
					(
						`NB_ID`,
						`MGG_ID`,
						`NB_GARAGE_NM`,
						`NB_CAR_NUM`,
						`NB_CLAIM_DT`,
						`RLT_ID`,
						`RMT_ID`,
						`CCC_ID`,
						`NB_VAT_DEDUCTION_YN`,
						`NB_MEMO_TXT`,
						`NB_INC_DTL_YN`,
						`NB_NEW_INSU_YN`,
						`NB_TOTAL_PRICE`,
						`NB_VAT_PRICE`,
						`NB_CLAIM_USERID`,
						`NB_CLAIM_DTHMS`,
						`NB_DUPLICATED_YN`,
						`WRT_DTHMS`,
						`LAST_DTHMS`,
						`WRT_USERID`,
						`DEL_YN`,
						`MODE_TARGET`	
					) 
					SELECT 
						`NQ_ID`,
						`MGG_ID`,
						`NQ_GARAGE_NM`,
						`NQ_CAR_NUM`,
						NOW(),
						`RLT_ID`,
						`RMT_ID`,
						`CCC_ID`,
						`NQ_VAT_DEDUCTION_YN`,
						`NQ_MEMO_TXT`,
						`NQ_INC_DTL_YN`,
						`NQ_NEW_INSU_YN`,
						`NQ_TOTAL_PRICE`,
						`NQ_VAT_PRICE`,
						`NQ_CLAIM_USERID`,
						`NQ_CLAIM_DTHMS`,
						`NQ_DUPLICATED_YN`,
						`WRT_DTHMS`,
						NOW(),
						`WRT_USERID`,
						`DEL_YN`,
						'".MODE_TARGET_ALL."'
					FROM `NT_BILL`.`NBB_QUOTE` 
					WHERE NQ_ID = '".addslashes($ARR['NQ_ID'])."'
				;
			";
			$iSQL .= "INSERT INTO NT_BILL.NBB_BILL_PARTS
					SELECT 
						`NQ_ID`,
						`CPPS_ID`,
						`CPP_ID`,
						`NQP_USE_CNT`,
						`NQP_UNIT_PRICE`,
						`WRT_DTHMS`,
						NOW(),
						'".addslashes($_SESSION['USERID'])."'
					FROM `NT_BILL`.`NBB_QUOTE_PARTS` 
					WHERE NQ_ID = '".addslashes($ARR['NQ_ID'])."'
				;
			";
			$iSQL .= "INSERT INTO NT_BILL.NBB_BILL_WORK
					SELECT 
						`NQ_ID`,
						`NWP_ID`,
						`RRT_ID`,
						`RWPP_ID`,
						`NQW_YN`,
						`NQW_TIME`,
						`WRT_DTHMS`,
						NOW(),
						'".addslashes($_SESSION['USERID'])."'
					FROM `NT_BILL`.`NBB_QUOTE_WORK` 
					WHERE NQ_ID = '".addslashes($ARR['NQ_ID'])."'
				;
			";
			$iSQL .= "INSERT INTO NT_BILL.NBB_BILL_WORK_PART
					SELECT 
						`NQ_ID`,
						`NWP_ID`,
						`CPP_ID`,
						`WRT_DTHMS`,
						NOW(),
						'".addslashes($_SESSION['USERID'])."'
					FROM `NT_BILL`.`NBB_QUOTE_WORK_PART` 
					WHERE NQ_ID = '".addslashes($ARR['NQ_ID'])."'
				;
			";
			$iSQL .= "INSERT INTO NT_BILL.NBB_BILL_INSU
					  SELECT 
					 	`NQ_ID`,
						`NBI_RMT_ID`,
						`MII_ID`,
						`NIC_ID`,
						`NQI_CHG_NM`,
						`NQI_FAX_NUM`,
						`NQI_REGI_NUM`,
						`NQI_RATIO`,
						`NQI_SEND_FAX_YN`,
						`NQI_FAX_SEND_DTHMS`,
						`RFS_ID`,
						`NQI_TEAM_MEMO`,
						`NQI_PRECLAIM_YN`,
						`NQI_LAST_PRINT_DTHMS`,
						`NQI_CAROWN_DEPOSIT_PRICE`,
						`NQI_CAROWN_DEPOSIT_DT`,
						`NBI_CAROWN_DEPOSIT_YN`,
						`NBI_INSU_DEPOSIT_PRICE`,
						`NBI_INSU_DEPOSIT_DT`,
						'40121',
						`NBI_INSU_DONE_YN`,
						`NBI_REASON_DT`,
						`RMT_ID`,
						NULL,
						NULL,
						NULL,
						`NQI_REASON_USERID`,
						`NQI_REASON_DTL_ID`,
						`NQI_OTHER_MII_ID`,
						`NQI_OTHER_RATIO`,
						`NQI_OTHER_DT`,
						`NQI_OTHER_PRICE`,
						`NQI_OTHER_MEMO`,
						`NQI_PROC_REASON_DT`,
						`NQI_PROC_USERID`,
						`NQI_REQ_REASON_YN`,
						`NQI_REQ_REASON_DTHMS`,
						`NQI_REQ_REASON_USERID`,
						NULL,
						`WRT_DTHMS`,
						NOW(),
						'".addslashes($_SESSION['USERID'])."'
					FROM `NT_BILL`.`NBB_QUOTE_INSU` 
					WHERE NQ_ID = '".addslashes($ARR['NQ_ID'])."'
					;
			";
			$iSQL .= "UPDATE  NT_BILL.NBB_QUOTE
					SET 
						NQ_CLAIM_DT = DATE_FORMAT(NOW() , '%Y-%m-%d')
					WHERE NQ_ID = '".addslashes($ARR['NQ_ID'])."'
				;
			";
			
		}
		// echo $iSQL;
		return DBManager::execMulti($iSQL);
	}


}
	// 0. 객체 생성
	$cCarCompList = new cCarCompList(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$cCarCompList->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
