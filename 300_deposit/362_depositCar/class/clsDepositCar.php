<?php

class cDepositCar extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	// Class Object Constructor
	function cDepositCar($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
		$this->DATA_SAVE_PATH = $DATA_SAVE_PATH;
		$this->ALLOW_FILE_EXT = $ALLOW_FILE_EXT;
	}


	/**
	 * 조회 
	 * @param {$ARR}
	 * @param {CURR_PAGE : 현재페이지, PER_PAGE : 몇개를 조회할 것인지, KEYWORD : 검색조건}
	 */
	function getList($ARR) {
        
        $LIMIT_SQL = "";

		$LimitStart = ($ARR['CURR_PAGE']-1) * $ARR['PER_PAGE'];
        $LimitEnd   = $ARR['PER_PAGE'];

		if($ARR['REQ_MODE'] == CASE_LIST){
			$LIMIT_SQL = "LIMIT $LimitStart,$LimitEnd";
        }
        
		$where = $this->getListWhere($ARR);
		$where = explode("|+|",$where);
		$tmpKeyword = $where[0];
		$joinNB = $where[1];
		$joinNBI = $where[2];

		// 페이징처리를 위해 전체데이터
		$iSQL = "SELECT COUNT(MGG_ID) AS 'CNT'
					FROM (SELECT 
							NG.MGG_ID ,COUNT(*) AS 'CNT'
							FROM 
								`NT_MAIN`.`V_MGG_STATUS` NG
							LEFT OUTER JOIN `NT_BILL`.`NBB_BILL` NB
							ON NG.MGG_ID = NB.MGG_ID AND NB.DEL_YN = 'N' AND NB.NB_NEW_INSU_YN ='Y'
							$joinNB
							LEFT OUTER JOIN `NT_BILL`.`NBB_BILL_INSU` NBI
							ON NB.NB_ID = NBI.NB_ID
							$joinNBI
							WHERE 1=1 
							$tmpKeyword
						GROUP BY NG.MGG_ID, NB.MGG_ID
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

		// 리스트 가져오기
		$iSQL = "SELECT 
					`NG`.`MGG_ID`,
					`NG`.`CSD_ID`,
					`NT_CODE`.`GET_CSD_NM`(`NG`.`CSD_ID`) AS 'CSD_NM',
					`NG`.`CRR_1_ID`,
					`NG`.`CRR_2_ID`,
					`NG`.`CRR_3_ID`,
					`NT_CODE`.`GET_CRR_NM`(`NG`.`CRR_1_ID`,`NG`.`CRR_2_ID`,`NG`.`CRR_3_ID`) AS 'CRR_NM',
					
					`NG`.`MGG_BIZ_ID`,
					`NG`.`MGG_NM`,
					`NG`.`RET_ID`, #청구식
					`NT_CODE`.`GET_CODE_NM`(`NG`.`RET_ID`)AS 'RET_NM',

					IFNULL(SUM(IF(NB.NB_NEW_INSU_YN='Y',1,0)),0) AS 'TOTAL_CNT', #차주분총청구건수
					IFNULL(ROUND(SUM(IF(NB.NB_NEW_INSU_YN='Y',NB.NB_TOTAL_PRICE*0.2,0)),-1),0) AS 'TOTAL_BILL_PRICE', #차주분 총청구건금액 
					IFNULL(SUM(IF(NBI.NBI_CAROWN_DEPOSIT_YN = 'Y',1,0)),0) AS 'DEPOSIT_CNT', #차주분 입금건수
					IFNULL(ROUND(SUM(IF(NBI.NBI_CAROWN_DEPOSIT_YN = 'Y',NB.NB_TOTAL_PRICE*0.2,0)),-1),0) AS 'DEPOSIT_BILL_PRICE', #차주분 입금 청구액
					IFNULL(ROUND(SUM(IF(NBI.NBI_CAROWN_DEPOSIT_YN = 'Y',NBI.NBI_CAROWN_DEPOSIT_PRICE,0)),-1),0) AS 'DEPOSIT_PRICE', #차주분 입금액
					IFNULL((
						ROUND(SUM(IF(NBI.NBI_CAROWN_DEPOSIT_YN = 'Y',NB.NB_TOTAL_PRICE*0.2,0)),-1)  
						- 
						ROUND(SUM(IF(NBI.NBI_CAROWN_DEPOSIT_YN = 'Y',NBI.NBI_CAROWN_DEPOSIT_PRICE,0)),-1)
					),0) AS 'DECREMENT_PRICE', #감액금액 = 입금청구액 - 입금액
					IFNULL((
						ROUND((
							ROUND(SUM(IF(NBI.NBI_CAROWN_DEPOSIT_YN = 'Y',NB.NB_TOTAL_PRICE*0.2,0)),-1)  
							- 
							ROUND(SUM(IF(NBI.NBI_CAROWN_DEPOSIT_YN = 'Y',NBI.NBI_CAROWN_DEPOSIT_PRICE,0)),-1) 
						) / ROUND(SUM(IF(NBI.NBI_CAROWN_DEPOSIT_YN = 'Y',NB.NB_TOTAL_PRICE*0.2,0)),-1)  *100,2)
					),0) AS 'DECREMENT_RATIO', #감액율 = 감액금액 / 입금청구금액 * 100
					IFNULL(SUM(IF(NBI.NBI_CAROWN_DEPOSIT_YN = 'N',1,0)),0) AS 'UNPAID_CNT', #차주분 미입금건수
					IFNULL(ROUND(SUM(IF(NBI.NBI_CAROWN_DEPOSIT_YN = 'N',NB.NB_TOTAL_PRICE*0.2,0)),-1),0) AS 'UNPAID_PRICE', #차주분 미입금액
					IFNULL(ROUND(SUM(IF(NBI.NBI_CAROWN_DEPOSIT_YN = 'Y',1,0))  
							/ SUM(IF(NB.NB_NEW_INSU_YN='Y',1,0)) * 100, 2),0) AS 'PAID_CNT_RATIO', #입금률 건수
					IFNULL(ROUND(
						ROUND(SUM(IF(NBI.NBI_CAROWN_DEPOSIT_YN = 'Y',NBI.NBI_CAROWN_DEPOSIT_PRICE,0)),-1) 
						/ ROUND(SUM(IF(NB.NB_NEW_INSU_YN='Y',NB.NB_TOTAL_PRICE*0.2,0)),-1) * 100, 2),0) AS 'PAID_PRICE_RATIO' #입금률 금액
				
				FROM 
					`NT_MAIN`.`V_MGG_STATUS` NG
				LEFT OUTER JOIN `NT_BILL`.`NBB_BILL` NB
				ON NG.MGG_ID = NB.MGG_ID AND NB.DEL_YN = 'N' AND NB.NB_NEW_INSU_YN ='Y'
				$joinNB
				LEFT OUTER JOIN `NT_BILL`.`NBB_BILL_INSU` NBI
				ON NB.NB_ID = NBI.NB_ID
				$joinNBI
				WHERE 1=1
				$tmpKeyword 
				GROUP BY NG.MGG_ID, NB.MGG_ID
				ORDER BY MGG_NM
                $LIMIT_SQL ;
		";

		// echo $iSQL;

		$Result= DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
                    "IDX"=>$IDX--,
					"MGG_ID"=>$RS['MGG_ID'],
					"CSD_ID"=>$RS['CSD_ID'],
					"CSD_NM"=>$RS['CSD_NM'],
					"CRR_1_ID"=>$RS['CRR_1_ID'],
					"CRR_2_ID"=>$RS['CRR_2_ID'],
					"CRR_3_ID"=>$RS['CRR_3_ID'],
					"CRR_NM"=>$RS['CRR_NM'],					
					"MGG_BIZ_ID"=>$RS['MGG_BIZ_ID'],
					"MGG_NM"=>$RS['MGG_NM'],
					"RET_ID"=>$RS['RET_ID'],
					"RET_NM"=>$RS['RET_NM'],
					"TOTAL_CNT"=>$RS['TOTAL_CNT'],
					"TOTAL_BILL_PRICE"=>$RS['TOTAL_BILL_PRICE'],
					"DEPOSIT_CNT"=>$RS['DEPOSIT_CNT'],
					"DEPOSIT_BILL_PRICE"=>$RS['DEPOSIT_BILL_PRICE'],
					"DEPOSIT_PRICE"=>$RS['DEPOSIT_PRICE'],
					"DECREMENT_PRICE"=>$RS['DECREMENT_PRICE'],
					"DECREMENT_RATIO"=>$RS['DECREMENT_RATIO'],
					"UNPAID_CNT"=>$RS['UNPAID_CNT'],
					"UNPAID_PRICE"=>$RS['UNPAID_PRICE'],
					"PAID_CNT_RATIO"=>$RS['PAID_CNT_RATIO'],
					"PAID_PRICE_RATIO"=>$RS['PAID_PRICE_RATIO']
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

	function getListWhere($ARR){
		if($ARR['DT_KEY_SEARCH'] != '') {
			if($ARR['DT_KEY_SEARCH'] == 'NB_CLAIM_DT'){ // 청구일
                if($ARR['START_DT_SEARCH'] != '' && $ARR['END_DT_SEARCH'] != ''){ 
                    $joinNB .= "AND NB.NB_CLAIM_DT BETWEEN '".addslashes($ARR['START_DT_SEARCH'])."' AND '".addslashes($ARR['END_DT_SEARCH'])."' \n";
                }else if($ARR['START_DT_SEARCH'] != '' ){
                    $joinNB .= "AND NB.NB_CLAIM_DT >= '".$ARR['START_DT_SEARCH']."' \n";
                }else if($ARR['END_DT_SEARCH'] != ''){
                    $joinNB .= "AND NB.NB_CLAIM_DT <= '".$ARR['END_DT_SEARCH']."' \n";
                }
            }else if($ARR['DT_KEY_SEARCH'] == 'NBI_INSU_DEPOSIT_DT'){ // 입금일
                if($ARR['START_DT_SEARCH'] != '' && $ARR['END_DT_SEARCH'] != ''){
                    $joinNBI .= "AND DATE_FORMAT(NBI.NBI_CAROWN_DEPOSIT_DT, '%Y-%m-%d') BETWEEN '".addslashes($ARR['START_DT_SEARCH'])."' AND '".addslashes($ARR['END_DT_SEARCH'])."'";
                }else if($ARR['START_DT_SEARCH'] != '' ){
                    $joinNBI .= "AND DATE_FORMAT(NBI.NBI_CAROWN_DEPOSIT_DT, '%Y-%m-%d') >= '".$ARR['START_DT_SEARCH']."' \n";
                }else if($ARR['END_DT_SEARCH'] != ''){
                    $joinNBI .= "AND DATE_FORMAT(NBI.NBI_CAROWN_DEPOSIT_DT, '%Y-%m-%d') <= '".$ARR['END_DT_SEARCH']."' \n";
                }
            }
		}

		//쌍방여부
		if($ARR['BOTH_YN'] == YN_Y){
            $joinNB .= "AND NB.RLT_ID =  '11430' \n";
		}else if($ARR['BOTH_YN'] == YN_N){
			$joinNB .= "AND NB.RLT_ID <>  '11430' \n";
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
			$tmpKeyword .= "AND NG.MGG_ID  = '".$ARR['MGG_NM_SEARCH']."' \n";
		} 

		//청구식검색
		if($ARR['RET_ID_SEARCH'] != '') {
			$tmpKeyword .= "AND NG.RET_ID IN ( ";
            foreach ($ARR['RET_ID_SEARCH'] as $index => $RET_ID) {
                $tmpKeyword .=" '".$RET_ID."',";
            }
            $tmpKeyword = substr($tmpKeyword, 0, -1); // 마지막 콤마제거
            $tmpKeyword .= ") \n";
		}

		//수탁자
		if($ARR['MGG_BAILEE_NM_SEARCH'] != ''){
			$tmpKeyword .="AND INSTR(NG.`MGG_BAILEE_NM`, '".addslashes($ARR['MGG_BAILEE_NM_SEARCH'])."' ) > 0 \n";
		}

		//거래여부
		if($ARR['RCT_ID_SEARCH'] != '') {
			$tmpKeyword .= "AND NG.RLT_ID = '".addslashes($ARR['RCT_ID_SEARCH'])."' \n";
		} 

		//계약여부
		if($ARR['MGG_CONTRACT_YN_SEARCH'] != '') {
			$tmpKeyword .= "AND NG.MGG_CONTRACT_YN = '".addslashes($ARR['MGG_CONTRACT_YN_SEARCH'])."' \n";
		}
		
		return $tmpKeyword."|+|".$joinNB."|+|".$joinNBI;
	}
	


	function getSummary($ARR){
		$where = $this->getListWhere($ARR);
		$where = explode("|+|",$where);
		$tmpKeyword = $where[0];
		$joinNB = $where[1];
		$joinNBI = $where[2];
			
		$iSQL = "SELECT
					COUNT(NB.NB_ID) AS 'CNT',
					IFNULL(ROUND(SUM(NB.NB_TOTAL_PRICE*0.2),-1),0) AS 'BILL_PRICE',
					SUM(IF(NBI.NBI_CAROWN_DEPOSIT_YN = 'Y',1,0)) AS 'PAID_CNT',
					SUM(IF(NBI.NBI_CAROWN_DEPOSIT_YN = 'Y',ROUND(NBI.NBI_CAROWN_DEPOSIT_PRICE,-1),0)) AS 'PAID_PRICE',
					ROUND(SUM(IF(NBI.NBI_CAROWN_DEPOSIT_YN = 'Y',ROUND(NBI.NBI_CAROWN_DEPOSIT_PRICE,-1),0))
						/ROUND(SUM(NB.NB_TOTAL_PRICE*0.2)-1) * 100,2) AS 'PAID_RATIO',
					( 
						ROUND(SUM(IF(NBI.NBI_CAROWN_DEPOSIT_YN = 'Y',NB.NB_TOTAL_PRICE*0.2,0)),-1)
						- ROUND(SUM(IF(NBI.NBI_CAROWN_DEPOSIT_YN = 'Y',NBI.NBI_CAROWN_DEPOSIT_PRICE,0)) ,-1) 
					) AS 'DECREMENT_PRICE',
					ROUND((
						(	ROUND(SUM(IF(NBI.NBI_CAROWN_DEPOSIT_YN = 'Y',NB.NB_TOTAL_PRICE*0.2,0)),-1)
							- ROUND(SUM(IF(NBI.NBI_CAROWN_DEPOSIT_YN = 'Y',NBI.NBI_CAROWN_DEPOSIT_PRICE,0)),-1) 	)
						/ROUND(SUM(NB.NB_TOTAL_PRICE*0.2),-1) * 100 
					),2) AS 'DECREMENT_RATIO',
					SUM(IF(NBI.NBI_CAROWN_DEPOSIT_YN = 'N',1,0)) AS 'UNPAID_CNT',
					SUM(IF(NBI.NBI_CAROWN_DEPOSIT_YN = 'N',ROUND(NB.NB_TOTAL_PRICE*0.2,-1),0)) AS 'UNPAID_PRICE'
				FROM
					`NT_BILL`.`NBB_BILL` NB,
					`NT_BILL`.`NBB_BILL_INSU` NBI,
					`NT_MAIN`.`V_MGG_STATUS` NG
				WHERE
					NB.`NB_ID` = NBI.`NB_ID`
				AND NB.MGG_ID = NG.MGG_ID
					$joinNB
					$joinNBI
				AND	NB.`DEL_YN` = 'N'
				AND NB.`NB_NEW_INSU_YN` = 'Y'
				$tmpKeyword
				;
		";
		// echo $iSQL;
		return DBManager::getRecordSet($iSQL);	//검색조건 미반영
		
	}

	function getYear() {
		$iSQL = "SELECT 
					YEAR(NOW()) AS CURR_YEAR,
					YEAR(MIN(MGG_BIZ_START_DT))AS MIN_YEAR
				FROM NT_MAIN.NMG_GARAGE_SUB
				WHERE DEL_YN = 'N';
		";

		return DBManager::getRecordSet($iSQL);	
	}




}
	// 0. 객체 생성
	$cDepositCar = new cDepositCar(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$cDepositCar->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
