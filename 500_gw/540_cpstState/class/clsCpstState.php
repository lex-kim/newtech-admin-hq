<?php

class cCpst extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	function cCpst($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
		$this->DATA_SAVE_PATH = $DATA_SAVE_PATH;
		$this->ALLOW_FILE_EXT = $ALLOW_FILE_EXT;
	}


	/**
	 * 해당데이터 조회 
	 * @param {$ARR}
	 * @param {ALLM_ID : 조회할 해당 데이터 ID}
	 */
	function getHeaderCnt($ARR) {
		$iSQL = "SELECT 
					MII_ID,
					RIT_ID,
					MII_NM
				FROM NT_MAIN.NMI_INSU
				WHERE DEL_YN = 'N'
				ORDER BY RIT_ID, MII_ORDER_NUM; ";

		$Result= DBManager::getResult($iSQL);
			// echo $iSQL;
		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"MII_ID"=>$RS['MII_ID'],
					"RIT_ID"=>$RS['RIT_ID'],
					"MII_NM"=>$RS['MII_NM']
			));
		}

		$Result->close();  // 자원 반납
		unset($RS);

		return  $rtnArray;
	}


	/**
	 * 조회 
	 * @param {Array} $ARR 
	 */
	function getList($ARR) {

		$LimitStart = ($ARR['CURR_PAGE']-1) * $ARR['PER_PAGE'];
		$LimitEnd   = $ARR['PER_PAGE'];

		/** 전체리스트가 아닌경우에만 limitSql 적용 */
		$LIMIT_SQL = "";
		if($ARR['REQ_MODE'] == CASE_LIST){
			$LIMIT_SQL = "LIMIT $LimitStart,$LimitEnd";
		}

		$tmpKeyword = "WHERE 1=1 ";
		/* 구분검색 */
		if($ARR['CSD_ID_SEARCH'] != '') {
			$tmpKeyword .= "AND VMS.`CSD_ID` IN (";
			foreach ($ARR['CSD_ID_SEARCH'] as $index => $CSD_ID) {
				$tmpKeyword .=" '".$CSD_ID."',";
			}
			$tmpKeyword = substr($tmpKeyword, 0, -1); // 마지막 콤마제거
			$tmpKeyword .= ")";
		} 

		/* 지역주소 검색 */
		if($ARR['CRR_ID_SEARCH'] !=  '' || $ARR['CRR_ID_SEARCH'] != null) {
			$tmpKeyword .= "AND (";
			foreach ($ARR['CRR_ID_SEARCH'] as $index => $CRD_ARRAY) {
				$CSD = explode(',', $CRD_ARRAY);
				
				if($index == 0){
					$tmpKeyword .= "(";
					$tmpKeyword .= "`VMS`.`CRR_1_ID` = '".$CSD[0]."' \n";
				}else{
					$tmpKeyword .= " OR(";
					$tmpKeyword .= "  `VMS`.`CRR_1_ID` = '".$CSD[0]."' \n";
                }
                if($CSD[1] != NOT_CRR_2){
                    $tmpKeyword .= "AND `VMS`.`CRR_2_ID` = '".$CSD[1]."' \n";
                }
				
				if($CSD[2] != NOT_CRR_3){
					$tmpKeyword .= "AND `VMS`.`CRR_3_ID` = '".$CSD[2]."' \n";
				}
				
				$tmpKeyword .= ") \n";
			}
			$tmpKeyword .= ") \n";
		}

		//공업사명 검색
		if($ARR['MGG_NM_SEARCH'] != '') {
			$tmpKeyword .= "AND VMS.MGG_ID = '".$ARR['MGG_NM_SEARCH']."' ";	
		}

		//거래여부 검색
		if($ARR['RCT_ID_SEARCH'] != '') {
			$tmpKeyword .= "AND VMS.RCT_ID = '".$ARR['RCT_ID_SEARCH']."' ";	
		}

		$INSU_ARRAY =  $this->getHeaderCnt($_REQUEST);
		$INSU_SQL = "";

		//담당구분 검색
		if($ARR['RICT_ID_SEARCH'] != '') {	
			foreach($INSU_ARRAY as $index => $INSU){
				$INSU_SQL .= ",IFNULL(NT_MAIN.GET_MGG_INSU_MANAGE('".AES_SALT_KEY."',VMS.`MGG_ID`,'".$INSU['MII_ID']."', '".$ARR['RICT_ID_SEARCH']."'),'') as ".$INSU['MII_ID']."_".$index ."\n";
			}	
			$tmpKeyword .= " AND VMS.`MGG_ID` IN (
				SELECT 
								NGIC.MGG_ID
						FROM  
							 NT_MAIN.NMG_GARAGE_INSU_CHARGE NGIC
						WHERE NGIC.RICT_ID = '".$ARR['RICT_ID_SEARCH']."'
						AND NGIC.MGG_ID = VMS.`MGG_ID`)";
		}else{
			foreach($INSU_ARRAY as $index => $INSU){
				$INSU_SQL .= ",IFNULL(NT_MAIN.GET_MGG_INSU_MANAGE('".AES_SALT_KEY."',VMS.`MGG_ID`,'".$INSU['MII_ID']."', NULL),'') as ".$INSU['MII_ID']."_".$index ."\n";
			}
		}

		// 페이징처리를 위해 전체데이터
		$iSQL = "SELECT
					COUNT(*) AS CNT
				FROM `NT_MAIN`.`V_MGG_STATUS` VMS
				$tmpKeyword
				;
		";
		// echo $iSQL;
		try{
			$RS_T	= DBManager::getRecordSet($iSQL);
		} catch (Exception $e){
			echo "error : ".$e;
			return ERROR_DB_SELECT_DATA;  
		}

		$TOTAL_LIST_COUNT = $RS_T['CNT'];         // 총 몇개인지
		$IDX = $RS_T['CNT']-$LimitStart;          // 해당페이지 index
		unset($RS_T);

		

		// 리스트 가져오기
		$iSQL = "SELECT 
					VMS.`MGG_ID`,
					VMS.`MGG_NM`,
					VMS.`CSD_ID`, 
					`NT_CODE`.`GET_CSD_NM`(VMS.`CSD_ID`) AS CSD_NM,
					VMS.`CRR_1_ID`,
					VMS.`CRR_2_ID`,
					VMS.`CRR_3_ID`,
					VMS.`MGG_BIZ_ID`,
					`NT_CODE`.`GET_CRR_NM`(VMS.`CRR_1_ID`, VMS.`CRR_2_ID`, VMS.`CRR_3_ID`) AS CRR_NM,
					VMS.`RCT_ID`,
					SUBSTRING_INDEX( `NT_CODE`.`GET_RCT`(VMS.`MGG_ID`), '-', -1) AS RCT_NM,
					VMS.`MGG_CONTRACT_YN`
					$INSU_SQL 
				FROM `NT_MAIN`.`V_MGG_STATUS` VMS
				$tmpKeyword
				$LIMIT_SQL
				;
	
				
		";
	    // echo $iSQL;
		$Result= DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			$insuArray = array();
			foreach($INSU_ARRAY as $index => $INSU){
				array_push($insuArray, array(
						"RIT_ID"=>$INSU['RIT_ID'], 
						"MII_ID"=>$INSU['MII_ID'],
						"DATA"=>empty($RS[$INSU['MII_ID']."_".$index]) ? '' : $RS[$INSU['MII_ID']."_".$index]));
			}
			array_push($rtnArray,
				array(
				"IDX"=>$IDX--,
				"MGG_ID"=>$RS['MGG_ID'],
				"MGG_NM"=>$RS['MGG_NM'],
				"CSD_NM"=>$RS['CSD_NM'],
				"CSD_ID"=>$RS['CSD_ID'],
				"CRR_1_ID"=>$RS['CRR_1_ID'],
				"CRR_2_ID"=>$RS['CRR_2_ID'],
				"CRR_3_ID"=>$RS['CRR_3_ID'],
				"MGG_BIZ_ID"=>$RS['MGG_BIZ_ID'],
				"CRR_NM"=>$RS['CRR_NM'],
				"RCT_ID"=>$RS['RCT_ID'],
				"RCT_NM"=>$RS['RCT_NM'],
				"INSU_ARRAY" => $insuArray
			));
		}
		$Result->close();  
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
	 * 공업사별 보상담당 조회 
	 * @param {Array} $ARR 
	 */
	function getDetailList($ARR) {
		
		// 리스트 가져오기
		$iSQL = "SELECT 
					NI.MII_ID,
					NI.MII_NM,
					NI.MII_ORDER_NUM,
					NI.RIT_ID,
					CHG.NIC_ID,
					CAST(AES_DECRYPT(CHG.NIC_NM_AES,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `NIC_NM_AES`,
					CHG.WRT_DTHMS
				FROM  `NT_MAIN`.`NMI_INSU` NI
				LEFT OUTER JOIN ( 
					SELECT 
						NGIC.`MGG_ID`,
						NGIC.`NIC_ID`,
						SUBSTRING(NGIC.`WRT_DTHMS`, 1,10) AS WRT_DTHMS,
						NIC.MII_ID,
						NIC.NIC_NM_AES
					FROM `NT_MAIN`.`NMG_GARAGE_INSU_CHARGE` NGIC,
					`NT_MAIN`.`NMI_INSU_CHARGE` NIC
					WHERE NGIC.NIC_ID = NIC.NIC_ID
					AND NGIC.MGG_ID = '".$ARR['MGG_ID']."'
					) AS CHG
					ON CHG.MII_ID = NI.MII_ID
				WHERE NI.DEL_YN= 'N'
				
				ORDER BY NI.RIT_ID , NI.MII_ORDER_NUM;
	
				
		";
	    // echo $iSQL;
		$Result= DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
				"IDX"=>$IDX--,
				"MII_ID"=>$RS['MII_ID'],
				"MII_NM"=>$RS['MII_NM'],
				"MII_ORDER_NUM"=>$RS['MII_ORDER_NUM'],
				"RIT_ID"=>$RS['RIT_ID'],
				"NIC_ID"=>$RS['NIC_ID'],
				"NIC_NM_AES"=>$RS['NIC_NM_AES'],
				"WRT_DTHMS"=>$RS['WRT_DTHMS']
			));
		}
		$Result->close();  
		unset($RS);

		if(empty($rtnArray)) {
			return HAVE_NO_DATA;
		} else {
			return  $rtnArray;
		}
	}
	

    

}
// 0. 객체 생성
$cCpst = new cCpst(FILE_UPLOAD_PATH,ALLOW_FILE_EXT); //서버용
$cCpst->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
