<?php

class cStatReduce extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	// Class Object Constructor
	function cStatReduce($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
		$this->DATA_SAVE_PATH = $DATA_SAVE_PATH;
		$this->ALLOW_FILE_EXT = $ALLOW_FILE_EXT;
	}


	/**
	 * 조회 
	 * @param {$ARR}
	 * @param {CURR_PAGE : 현재페이지, PER_PAGE : 몇개를 조회할 것인지, KEYWORD : 검색조건}
	 */
	function getList($ARR) {
		// echo var_dump($ARR);
		$LimitStart = ($ARR['CURR_PAGE']-1) * $ARR['PER_PAGE'];
        $LimitEnd   = $ARR['PER_PAGE'];

		if($ARR['REQ_MODE'] == CASE_LIST){
			$LIMIT_SQL = "LIMIT $LimitStart,$LimitEnd ;";
        }else {
			$LIMIT_SQL = " ;";
		}
        
        
		//구분검색
		if($ARR['CSD_ID_SEARCH'] != ''){
            $tmpKeyword .= "AND MG.CSD_ID IN (";
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
					$tmpKeyword .= "`MG`.`CRR_1_ID` = '".$CSD[0]."' \n";
				}else{
					$tmpKeyword .= " OR(";
					$tmpKeyword .= "  `MG`.`CRR_1_ID` = '".$CSD[0]."' \n";
                }
                if($CSD[1] != NOT_CRR_2){
                    $tmpKeyword .= "AND `MG`.`CRR_2_ID` = '".$CSD[1]."' \n";
                }
				
				if($CSD[2] != NOT_CRR_3){
					$tmpKeyword .= "AND `MG`.`CRR_3_ID` = '".$CSD[2]."' \n";
				}
				
				$tmpKeyword .= ") \n";
			}
			$tmpKeyword .= ") \n";
		}
		
		//접수번호
		if($ARR['NBI_REGI_NUM_SEARCH'] != ''){
			$tmpKeyword .= "AND INSTR(`NBI`.`NBI_REGI_NUM`,'".addslashes($ARR['NBI_REGI_NUM_SEARCH'])."')>0 \n";
		}

		//담보
		if($ARR['RLT_ID_SEARCH'] != '') {
			$tmpKeyword .= "AND NB.RLT_ID = '".addslashes($ARR['RLT_ID_SEARCH'])."' \n";
		}

		//차량번호
		if($ARR['NB_CAR_NUM_SEARCH'] != ''){
			$tmpKeyword .= "AND INSTR(`NB`.`NB_CAR_NUM`,'".addslashes($ARR['NB_CAR_NUM_SEARCH'])."')>0 \n";	
		}

		//감액률
		if($ARR['START_RATIO_SEARCH'] != '' && $ARR['END_RATIO_SEARCH'] != ''){
			$outerWhere .= "AND DECREMENT_RATIO BETWEEN '".addslashes($ARR['START_RATIO_SEARCH'])."' AND '".addslashes($ARR['END_RATIO_SEARCH'])."'";
		}else if($ARR['START_RATIO_SEARCH'] != '' ){
			$outerWhere .= "AND DECREMENT_RATIO >= '".$ARR['START_RATIO_SEARCH']."' \n";
		}else if($ARR['END_RATIO_SEARCH'] != ''){
			$outerWhere .= "AND DECREMENT_RATIO <= '".$ARR['END_RATIO_SEARCH']."' \n";
		}

		//감액미입금
		if($ARR['DECREMENT_UNPAID_SERARCH'] != ''){
			if($ARR['DECREMENT_UNPAID_SERARCH'] == YN_Y) {
				$tmpKeyword .= " AND `NBI`.`RDT_ID` = '40113' ";
			}else if($ARR['DECREMENT_UNPAID_SERARCH'] == YN_N) {
				$tmpKeyword .= " AND `NBI`.`RDT_ID` <> '40113' ";
			}  
		}


		//입금여부
		if($ARR['DEPOSIT_YN_SEARCH'] != '') {
			if($ARR['DEPOSIT_YN_SEARCH'] == YN_Y){
				$tmpKeyword .= "AND NBI.RDET_ID IN('40122','40123','40124') ";	
			}else if($ARR['DEPOSIT_YN_SEARCH'] == YN_N){
				$tmpKeyword .= "AND NBI.RDET_ID = '40121' ";	
			}
		} 

		//입금여부상세
		if($ARR['REASON_SELECT'] != '') {
			$tmpKeyword .= "AND NBI.`NBI_REASON_DTL_ID` = '".$ARR['REASON_SELECT'] ."' ";
           
		}

		//공업사명검색
		if($ARR['MGG_NM_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(`MG`.`MGG_NM`,'".addslashes($ARR['MGG_NM_SEARCH'])."')>0 ";	
		} 

		
		// echo "where >>>".$tmpKeyword;
		if($ARR['START_RATIO_SEARCH'] != '' || $ARR['END_RATIO_SEARCH'] != ''){
			$mainSQL = $this->getListSql($ARR,$tmpKeyword);
			$mainSQL = "SELECT * FROM ($mainSQL) AS tmp WHERE 1=1 $outerWhere ";
		}else{
			$mainSQL = $this->getListSql($ARR,$tmpKeyword);
		}


		// 페이징처리를 위해 전체데이터
		$iSQL = "SELECT COUNT(*) AS 'CNT'
				FROM (
					$mainSQL
				) AS C ;
		";
        // echo $iSQL ;

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
			$ORDERBY_SQL = " ORDER BY NB_CLAIM_DT  ";   
		}else{
			$ORDERBY_SQL = " ORDER BY ".addslashes($ARR['ORDER_KEY'])." ".addslashes($ARR['IS_SORT'])." "; 
		}

		// 리스트 가져오기

		$iSQL = $mainSQL.$ORDERBY_SQL.$LIMIT_SQL ;

		// echo $iSQL;

		$Result= DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
                    "IDX"=>$IDX--,
					"MGG_ID"=>$RS['MGG_ID'],
					"NB_ID"=>$RS['NB_ID'],
					"NB_CLAIM_DT"=>$RS['NB_CLAIM_DT'],
					"NBI_RMT_ID"=>$RS['NBI_RMT_ID'],
					"MII_ID"=>$RS['MII_ID'],
					"MII_NM"=>$RS['MII_NM'],
					"NIC_ID"=>$RS['NIC_ID'],
					"NIC_NM"=>$RS['NIC_NM'],
					"NIC_TEL1_AES"=>$RS['NIC_TEL1_AES'],
					"MIT_NM"=>$RS['MIT_NM'],
					"CSD_NM"=>$RS['CSD_NM'],
					"CRR_NM"=>$RS['CRR_NM'],
					"MGG_NM"=>$RS['MGG_NM'],
					"MGG_BIZ_ID"=>$RS['MGG_BIZ_ID'],
					"RET_ID"=>$RS['RET_ID'],
					"RET_NM"=>$RS['RET_NM'],
					"NBI_REGI_NUM"=>$RS['NBI_REGI_NUM'],
					"RLT_ID"=>$RS['RLT_ID'],
					"RLT_NM"=>$RS['RLT_NM'],
					"NB_CAR_NUM"=>$RS['NB_CAR_NUM'],
					"CCC_ID"=>$RS['CCC_ID'],
					"CCC_TYPE"=>$RS['CCC_TYPE'],
					"CCC_NM"=>$RS['CCC_NM'],
					"NB_TOTAL_PRICE"=>$RS['NB_TOTAL_PRICE'],
					"NBI_REASON_DT"=>$RS['NBI_REASON_DT'],
					"NBI_REASON_USERID"=>$RS['NBI_REASON_USERID'],
					"REASON_RMT_ID"=>$RS['REASON_RMT_ID'],
					"REASON_RMT_NM"=>$RS['REASON_RMT_NM'],
					"NBI_REASON_USER_NM"=>$RS['NBI_REASON_USER_NM'],
					"RDET_ID"=>$RS['RDET_ID'],
					"RDET_NM"=>$RS['RDET_NM'],
					"RDT_ID"=>$RS['RDT_ID'],
					"RDT_NM"=>$RS['RDT_NM'],
					"NBI_REASON_DTL_ID"=>$RS['NBI_REASON_DTL_ID'],
					"NBI_INSU_DONE_YN"=>$RS['NBI_INSU_DONE_YN'],
					"NBI_REASON_NM"=>$RS['NBI_REASON_NM'],
					"NBI_REASON_DTL_NM"=>$RS['NBI_REASON_DTL_NM'],
					"NBI_OTHER_MII_NM"=>$RS['NBI_OTHER_MII_NM'],
					"NBI_OTHER_MII_ID"=>$RS['NBI_OTHER_MII_ID'],
					"NBI_OTHER_RATIO"=>$RS['NBI_OTHER_RATIO'],
					"NBI_OTHER_DT"=>$RS['NBI_OTHER_DT'],
					"NBI_OTHER_PRICE"=>$RS['NBI_OTHER_PRICE'],
					"NBI_OTHER_MEMO"=>$RS['NBI_OTHER_MEMO'],
					"NBI_PROC_REASON_DT"=>$RS['NBI_PROC_REASON_DT'],
					"NBI_REQ_REASON_YN"=>$RS['NBI_REQ_REASON_YN'],
					"RFS_ID"=>$RS['RFS_ID'],
					"RFS_NM"=>$RS['RFS_NM'],
					"NBI_INSU_DEPOSIT_DT"=>$RS['NBI_INSU_DEPOSIT_DT'],
					"NBI_INSU_DEPOSIT_PRICE"=>$RS['NBI_INSU_DEPOSIT_PRICE'],
					"NBI_INSU_DEPOSIT_YN"=>$RS['NBI_INSU_DEPOSIT_YN'],
					"DECREMENT_PRICE"=>$RS['DECREMENT_PRICE'],
					"DC_UNPAID_YN"=>$RS['DC_UNPAID_YN'],
					"NBI_INSU_ADD_DEPOSIT_DT"=>$RS['NBI_INSU_ADD_DEPOSIT_DT'],
					"NBI_INSU_ADD_DEPOSIT_PRICE"=>$RS['NBI_INSU_ADD_DEPOSIT_PRICE'],
					"DECREMENT_RATIO"=>$RS['DECREMENT_RATIO']
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

	/* getList 쿼리만 리턴해주는 함수. 공용으로 사용하기 위해 */
	function getListSql($ARR,$tmpKeyword){	
		$NB_RATIO_PRICE = "TRUNCATE(IFNULL(NB.`NB_TOTAL_PRICE`,'0')*IF(NBI.`NBI_RATIO`<>'',CAST(NBI.`NBI_RATIO` AS DECIMAL)/100,1),-1)" ; 
		$DECREMENT_DEPOSIT = "IFNULL(`NBI_INSU_DEPOSIT_PRICE`,0)+IFNULL(`NBI_INSU_ADD_DEPOSIT_PRICE`,0)+IFNULL(`NBI_OTHER_PRICE`,0)";

		$iSQL = "SELECT * FROM (SELECT 
					NB.MGG_ID,
					NB.NB_ID,
					NB.NB_CLAIM_DT,
					NBI.NBI_RMT_ID,
					NBI.MII_ID,
					`NT_MAIN`.`GET_INSU_NM`(NBI.MII_ID) AS 'MII_NM',
					NBI.NIC_ID,
					`NT_MAIN`.`GET_INSU_CHG_NM`(NBI.NIC_ID, '".AES_SALT_KEY."') AS 'NIC_NM',
					`NT_MAIN`.`GET_INSU_CHG_PHONE`(NBI.`NIC_ID`, '".AES_SALT_KEY."') AS 'NIC_TEL1_AES',
					IFNULL(`NT_MAIN`.`GET_INSU_CHG_TEAM_NM`(NBI.NIC_ID),IFNULL(NT_MAIN.GET_MIT_NM_BY_CRR(NBI.MII_ID, NB.MGG_ID),'')) AS 'MIT_NM',
					`NT_CODE`.`GET_CSD_NM`(MG.CSD_ID) AS 'CSD_NM',
					`NT_CODE`.`GET_CRR_NM`(MG.CRR_1_ID,MG.CRR_2_ID,MG.CRR_3_ID) AS 'CRR_NM',
					MG.MGG_NM,
					MG.MGG_BIZ_ID,
					MG.RET_ID,
					`NT_CODE`.`GET_CODE_NM`(MG.RET_ID) AS 'RET_NM',
					NBI.NBI_REGI_NUM,
					NB.RLT_ID,
					`NT_CODE`.`GET_CODE_NM`(NB.RLT_ID) AS 'RLT_NM',
					NB.NB_CAR_NUM,
					NB.CCC_ID,
					`NT_CODE`.`GET_CAR_TYPE`(NB.CCC_ID) AS 'CCC_TYPE',
					`NT_CODE`.`GET_CAR_NM`(NB.CCC_ID) AS 'CCC_NM',
					$NB_RATIO_PRICE AS NB_TOTAL_PRICE,
					NBI.NBI_REASON_DT,
					NBI.NBI_REASON_USERID,
					NBI.RMT_ID AS 'REASON_RMT_ID',
					NT_CODE.GET_CODE_NM(NBI.RMT_ID) AS 'REASON_RMT_NM', # 사유입력(주체)
					(SELECT USER_NM FROM `NT_MAIN`.`V_ALL_USER_INFO` WHERE USERID = NBI.NBI_REASON_USERID LIMIT 1) AS 'NBI_REASON_USER_NM',
					NBI.RDET_ID,
                    NT_CODE.GET_CODE_NM(NBI.RDET_ID) AS 'RDET_NM',
					NBI.RDT_ID,
                    NT_CODE.GET_CODE_NM(NBI.RDT_ID ) AS 'RDT_NM',
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
					NBI.NBI_REQ_REASON_YN AS 'NBI_REQ_REASON_YN',
					NBI.RFS_ID,
					`NT_CODE`.`GET_CODE_NM`(NBI.RFS_ID) AS RFS_NM,
					NBI.`NBI_INSU_DEPOSIT_DT` AS 'NBI_INSU_DEPOSIT_DT',
					NBI.`NBI_INSU_DEPOSIT_PRICE` AS 'NBI_INSU_DEPOSIT_PRICE',
					`NT_BILL`.`GET_DEPOSIT_YN`(NBI.RDET_ID) AS 'NBI_INSU_DEPOSIT_YN',
					($NB_RATIO_PRICE - $DECREMENT_DEPOSIT) AS 'DECREMENT_PRICE',
					IF(NBI.RDT_ID = '40113', 'Y','N') AS 'DC_UNPAID_YN',
					NBI_INSU_ADD_DEPOSIT_DT,
					NBI_INSU_ADD_DEPOSIT_PRICE,
					ROUND(
						($NB_RATIO_PRICE - $DECREMENT_DEPOSIT) 
						/ 
						IF(`RDET_ID` <> '40121', $NB_RATIO_PRICE, 0) 
						*
						100
					,2) AS 'DECREMENT_RATIO'
				FROM
					NT_BILL.NBB_BILL NB,
					NT_BILL.NBB_BILL_INSU NBI,
					NT_MAIN.V_MGG_STATUS MG
				WHERE  
					NB.NB_ID = NBI.NB_ID
				AND NB.MGG_ID = MG.MGG_ID	
				AND NB.DEL_YN = 'N'
				AND NBI.RDET_ID NOT IN('40121')
				AND NBI.NIC_ID = '".addslashes($ARR['NIC_ID'])."'
				$tmpKeyword
				)AS C
				WHERE C.NB_TOTAL_PRICE > (IFNULL(C.NBI_INSU_DEPOSIT_PRICE,0) + IFNULL(C.NBI_INSU_ADD_DEPOSIT_PRICE,0)) 
				";
		return $iSQL;
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



}
	// 0. 객체 생성
	$cStatReduce = new cStatReduce(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$cStatReduce->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>