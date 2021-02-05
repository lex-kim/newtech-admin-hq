<?php

class cClaimGw extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	// Class Object Constructor
	function cClaimGw($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
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
		
		//구분검색
		if($ARR['CSD_ID_SEARCH'] != ''){
            $tmpKeyword .= "AND MGG.CSD_ID IN (";
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
		
		//공업사명검색
		if($ARR['MGG_ID_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(`MGG`.`MGG_ID`,'".addslashes($ARR['MGG_ID_SEARCH'])."')>0 ";
		}

		//청구식검색
		if($ARR['RET_ID_SEARCH'] != '') {
			$tmpKeyword .= "AND MGGS.RET_ID IN ( ";
            foreach ($ARR['RET_ID_SEARCH'] as $index => $RET_ID) {
                $tmpKeyword .=" '".$RET_ID."',";
            }
            $tmpKeyword = substr($tmpKeyword, 0, -1); // 마지막 콤마제거
            $tmpKeyword .= ") \n";
		}

		
		//개척자
		if($ARR['MGG_FRONTIER_NM_SEARCH'] != ''){
			$tmpKeyword .="AND INSTR(MGGS.`MGG_FRONTIER_NM`, '".addslashes($ARR['MGG_FRONTIER_NM_SEARCH'])."' ) > 0 \n";
		}
		
		//협조자
		if($ARR['MGG_COLLABO_NM_SEARCH'] != ''){
			$tmpKeyword .="AND INSTR(MGGS.`MGG_COLLABO_NM`, '".addslashes($ARR['MGG_COLLABO_NM_SEARCH'])."' ) > 0 \n";
		}
		
		//수탁자
		if($ARR['MGG_BAILEE_NM_SEARCH'] != ''){
			$tmpKeyword .="AND INSTR(MGGS.`MGG_BAILEE_NM`, '".addslashes($ARR['MGG_BAILEE_NM_SEARCH'])."' ) > 0 \n";
		}

		//AOS
		if(!empty($ARR['RAT_ID_SEARCH'])) {
			$tmpKeyword .= "AND INSTR(`MGGS`.`RAT_ID`,'".addslashes($ARR['RAT_ID_SEARCH'])."')>0 ";	
		} 

		//거래여부
		if($ARR['RCT_ID_SEARCH'] != '') {
			$tmpKeyword .= "AND MGGS.RCT_ID = '".addslashes($ARR['RCT_ID_SEARCH'])."' \n";
		} 

		//계약여부
		if($ARR['MGG_CONTRACT_YN_SEARCH'] != '') {
			$tmpKeyword .= "AND MGGS.MGG_CONTRACT_YN = '".addslashes($ARR['MGG_CONTRACT_YN_SEARCH'])."' \n";
		} 
		
		// 페이징처리를 위해 전체데이터
		$iSQL = "SELECT COUNT(*) AS CNT 
				FROM (SELECT 
					COUNT(MGG.MGG_ID)
				FROM 
					NT_MAIN.NMG_GARAGE_SUB MGGS, 
					NT_MAIN.NMG_GARAGE MGG
				LEFT JOIN NT_BILL.NBB_BILL NB
					ON NB.MGG_ID = MGG.MGG_ID
					AND NB.DEL_YN = 'N' 
					AND YEAR(NB.NB_CLAIM_DT) = '".addslashes($ARR['YEAR_SEARCH'])."'
				WHERE MGG.DEL_YN = 'N'
				AND MGGS.MGG_ID = MGG.MGG_ID
				$tmpKeyword
				GROUP BY MGG.MGG_ID
				
				) AS C ;
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

		/** 전체리스트가 아닌경우에만 limitSql 적용 */
		$LIMIT_SQL = "";
		if($ARR['REQ_MODE'] == CASE_LIST){
			$LIMIT_SQL = "LIMIT $LimitStart,$LimitEnd";
		}

		if(date("Y") == $ARR['YEAR_SEARCH']){
			$AVG_SQL = "LEFT JOIN (SELECT 
							C.MGG_ID,
							COUNT(C.MONTH) AS 'CNT',
							ROUND((SUM(C.CNT) / TIMESTAMPDIFF(MONTH, DATE_FORMAT(NOW(),'%Y-01-01'), NOW())), 2) AS 'AVG_CNT',
							ROUND((SUM(ROUND(C.PRICE,-1)) / TIMESTAMPDIFF(MONTH, DATE_FORMAT(NOW(),'%Y-01-01'), NOW())), 2) AS 'AVG_PRICE',
							SUM(C.CNT) AS 'SUM_CNT',
							SUM(ROUND(C.PRICE,-1))  AS 'SUM_PRICE'
							FROM 
								(
								SELECT 
								INB.MGG_ID,
								MONTH(INB.NB_CLAIM_DT) AS 'MONTH',
								COUNT(INB.NB_ID) AS 'CNT',
								SUM(INB.NB_TOTAL_PRICE) AS 'PRICE'
								FROM NT_BILL.NBB_BILL INB
								WHERE 
									INB.DEL_YN='N'
								AND YEAR(INB.NB_CLAIM_DT) = '".addslashes($ARR['YEAR_SEARCH'])."'
								AND DATE_FORMAT(INB.NB_CLAIM_DT,'%Y%m')  < DATE_FORMAT(NOW(),'%Y%m')
								GROUP BY INB.MGG_ID,MONTH
								) AS C 
								GROUP BY C.MGG_ID
						) STATIC
					ON STATIC.MGG_ID = NB.MGG_ID";
		}else{
			$AVG_SQL = "LEFT JOIN (SELECT 
						C.MGG_ID,
						COUNT(C.MONTH) AS 'CNT',
						ROUND((SUM(C.CNT) / 12),2) AS 'AVG_CNT',
						ROUND((SUM(ROUND(C.PRICE,-1)) / 12 ),2) AS 'AVG_PRICE',
						SUM(C.CNT) AS 'SUM_CNT',
						SUM(ROUND(C.PRICE,-1))  AS 'SUM_PRICE'
						FROM 
							(
							SELECT 
							INB.MGG_ID,
							MONTH(INB.NB_CLAIM_DT) AS 'MONTH',
							COUNT(INB.NB_ID) AS 'CNT',
							SUM(INB.NB_TOTAL_PRICE) AS 'PRICE'
							FROM NT_BILL.NBB_BILL INB
							WHERE 
								INB.DEL_YN='N'
							AND YEAR(INB.NB_CLAIM_DT) = '".addslashes($ARR['YEAR_SEARCH'])."'
							AND DATE_FORMAT(INB.NB_CLAIM_DT,'%Y%m')  < DATE_FORMAT(NOW(),'%Y%m')
							GROUP BY INB.MGG_ID,MONTH
							) AS C 
							GROUP BY C.MGG_ID
					) STATIC
				ON STATIC.MGG_ID = NB.MGG_ID";
		}
		
	
		$iSQL = "SELECT 
					MGG.MGG_ID,
					NT_CODE.GET_CSD_NM(MGG.CSD_ID) AS CSD_NM,
					NT_CODE.GET_CRR_NM(MGG.CRR_1_ID, MGG.CRR_2_ID,'00000') AS CRR_NM,
					MGG.MGG_NM,
					MGG.MGG_BIZ_ID,
					IFNULL( NT_CODE.GET_RET_NM(MGGS.RET_ID),'') AS RET_NM,
					MGGS.MGG_FRONTIER_NM, #개척자
					MGGS.MGG_COLLABO_NM, #협조자
					MGGS.MGG_BAILEE_NM, #수탁자
					MGGS.RCT_ID, #거래여부
					NT_CODE.GET_CODE_NM(MGGS.RCT_ID) AS 'RCT_NM',
					MGGS.MGG_BIZ_START_DT,
					IFNULL(MGGS.MGG_BIZ_END_DT,'') AS MGG_BIZ_END_DT,
					(SELECT MAX(NB_CLAIM_DT) FROM NT_BILL.NBB_BILL  WHERE MGG_ID = MGG.MGG_ID)AS MAX_CLAIM_DT,
					-- IFNULL(DATEDIFF(DATE_FORMAT(NOW(),'%Y-%m-%d'), (SELECT MAX(NB_CLAIM_DT) FROM NT_BILL.NBB_BILL  WHERE MGG_ID = MGG.MGG_ID)),'-')  AS NB_CLAIM_DT, #청구도과일
					(SELECT `RAT_NM` FROM `NT_CODE`.`REF_AOS_TYPE` WHERE `RAT_ID` = `MGGS`.`RAT_ID`) AS `RAT_NM`,
					MGGS.MGG_BIZ_BEFORE_NM, #전거래처
					MGGS.RSR_ID, #중단사유
					NT_CODE.GET_CODE_NM(MGGS.RSR_ID) AS 'RSR_NM', 
					MGGS.MGG_OTHER_BIZ_NM, #타거래처
					MGGS.MGG_CONTRACT_YN, #계약여부
					MGGS.MGG_CONTRACT_START_DT,
					MGGS.MGG_CONTRACT_END_DT,
					MGGS.MGG_RATIO, #수수료율
					CAST(AES_DECRYPT(MGGS.MGG_BANK_NM_AES,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS 'MGG_BANK_NM_AES',
					CAST(AES_DECRYPT(MGGS.MGG_BANK_NUM_AES,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS 'MGG_BANK_NUM_AES',
					CAST(AES_DECRYPT(MGGS.MGG_BANK_OWN_NM_AES,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS 'MGG_BANK_OWN_NM_AES',
					-- CONCAT(IFNULL(MAX(NB.NB_CLAIM_DT) ,''), ' ~ ' , DATE_FORMAT(NOW(),'%Y-%m-%d') )  AS NB_CLAIM_DT,

					IFNULL(FORMAT(NT_STOCK.GET_MGG_STOCK_PRICE(MGG.MGG_ID),0),0) AS STOCK_PRICE,
					-- ROUND(IFNULL(NT_STOCK.GET_STOCK_DATA(MGG.MGG_ID,'R'),0),2) AS STOCK,
					IFNULL(
						IF(
							NT_BILL.GET_MONTH_BILL_PRICE(MGG.MGG_ID, DATE_FORMAT(DATE_SUB(NOW(),INTERVAL 1 MONTH),'%Y-%m-%d'))>0,
							FORMAT(ROUND(NT_STOCK.GET_MGG_STOCK_PRICE(MGG.MGG_ID)/NT_BILL.GET_MONTH_BILL_PRICE(MGG.MGG_ID, DATE_FORMAT(DATE_SUB(NOW(),INTERVAL 1 MONTH),'%Y-%m-%d'))*100,2),0),
							0
						)
					,0) AS STOCK,
					
					IFNULL(ROUND(STATIC.AVG_CNT,0),0) AS 'AVG_CNT', #월평균청구건수
                    IFNULL(ROUND(STATIC.AVG_PRICE,0),0) AS 'AVG_PRICE', #월평균청구금액
					IFNULL(ROUND(STATIC.SUM_CNT,0),0) AS 'SUM_CNT', # 합계 청구건수
                    IFNULL(ROUND(STATIC.SUM_PRICE,0),0) AS 'SUM_PRICE', # 합계 청구금액 

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

					SUM(IF(MONTH(NB.NB_CLAIM_DT) = '12' , NB.NB_TOTAL_PRICE , 0)) AS CLAIM_PRICE_12,
					SUM(IF(MONTH(NB.NB_CLAIM_DT) = '11' , NB.NB_TOTAL_PRICE , 0)) AS CLAIM_PRICE_11,
					SUM(IF(MONTH(NB.NB_CLAIM_DT) = '10' , NB.NB_TOTAL_PRICE , 0)) AS CLAIM_PRICE_10,
					SUM(IF(MONTH(NB.NB_CLAIM_DT) = '9' , NB.NB_TOTAL_PRICE , 0)) AS CLAIM_PRICE_9,
					SUM(IF(MONTH(NB.NB_CLAIM_DT) = '8' , NB.NB_TOTAL_PRICE , 0)) AS CLAIM_PRICE_8,
					SUM(IF(MONTH(NB.NB_CLAIM_DT) = '7' , NB.NB_TOTAL_PRICE , 0)) AS CLAIM_PRICE_7,
					SUM(IF(MONTH(NB.NB_CLAIM_DT) = '6' , NB.NB_TOTAL_PRICE , 0)) AS CLAIM_PRICE_6,
					SUM(IF(MONTH(NB.NB_CLAIM_DT) = '5' , NB.NB_TOTAL_PRICE , 0)) AS CLAIM_PRICE_5,
					SUM(IF(MONTH(NB.NB_CLAIM_DT) = '4' , NB.NB_TOTAL_PRICE , 0)) AS CLAIM_PRICE_4,
					SUM(IF(MONTH(NB.NB_CLAIM_DT) = '3' , NB.NB_TOTAL_PRICE , 0)) AS CLAIM_PRICE_3,
					SUM(IF(MONTH(NB.NB_CLAIM_DT) = '2' , NB.NB_TOTAL_PRICE , 0)) AS CLAIM_PRICE_2,
					SUM(IF(MONTH(NB.NB_CLAIM_DT) = '1' , NB.NB_TOTAL_PRICE , 0)) AS CLAIM_PRICE_1,
					MGGS.MGG_INSU_MEMO,
					MGGS.MGG_MANUFACTURE_MEMO
					
				FROM 
					NT_MAIN.NMG_GARAGE_SUB MGGS, 
					NT_MAIN.NMG_GARAGE MGG
				LEFT JOIN NT_BILL.NBB_BILL NB
					ON NB.MGG_ID = MGG.MGG_ID
					AND NB.DEL_YN = 'N' 
					AND YEAR(NB.NB_CLAIM_DT) = '".addslashes($ARR['YEAR_SEARCH'])."'
				$AVG_SQL			
				WHERE MGG.DEL_YN = 'N'
				AND MGGS.MGG_ID = MGG.MGG_ID
				$tmpKeyword 
				GROUP BY MGG.MGG_ID
				ORDER BY MGGS.MGG_BIZ_START_DT DESC
				$LIMIT_SQL  ;
			";
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
					"RAT_NM"=>$RS['RAT_NM'],
					"MGG_FRONTIER_NM"=>$RS['MGG_FRONTIER_NM'],
					"MGG_COLLABO_NM"=>$RS['MGG_COLLABO_NM'],
					"MGG_BAILEE_NM"=>$RS['MGG_BAILEE_NM'],
					"RCT_ID"=>$RS['RCT_ID'],
					"RCT_NM"=>$RS['RCT_NM'],
					"MGG_BIZ_START_DT"=>$RS['MGG_BIZ_START_DT'],
					"MGG_BIZ_END_DT"=>$RS['MGG_BIZ_END_DT'],
					"MAX_CLAIM_DT"=>$RS['MAX_CLAIM_DT'],
					"MGG_BIZ_BEFORE_NM"=>$RS['MGG_BIZ_BEFORE_NM'],
					"RSR_ID"=>$RS['RSR_ID'],
					"RSR_NM"=>$RS['RSR_NM'], 
					"MGG_OTHER_BIZ_NM"=>$RS['MGG_OTHER_BIZ_NM'],
					"MGG_CONTRACT_YN"=>$RS['MGG_CONTRACT_YN'],
					"MGG_CONTRACT_START_DT"=>$RS['MGG_CONTRACT_START_DT'],
					"MGG_CONTRACT_END_DT"=>$RS['MGG_CONTRACT_END_DT'],
					"MGG_RATIO"=>$RS['MGG_RATIO'],
					"MGG_BANK_NM_AES"=>$RS['MGG_BANK_NM_AES'],
					"MGG_BANK_NUM_AES"=>$RS['MGG_BANK_NUM_AES'],
					"MGG_BANK_OWN_NM_AES"=>$RS['MGG_BANK_OWN_NM_AES'],
					"STOCK_PRICE"=>$RS['STOCK_PRICE'],
					"STOCK"=>$RS['STOCK'],
					"AVG_CNT"=>$RS['AVG_CNT'],
					"AVG_PRICE"=>$RS['AVG_PRICE'],
					"SUM_CNT"=>$RS['SUM_CNT'],
					"SUM_PRICE"=>$RS['SUM_PRICE'],
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
					"CLAIM_PRICE_12"=>$RS['CLAIM_PRICE_12'],
					"CLAIM_PRICE_11"=>$RS['CLAIM_PRICE_11'],
					"CLAIM_PRICE_10"=>$RS['CLAIM_PRICE_10'],
					"CLAIM_PRICE_9"=>$RS['CLAIM_PRICE_9'],
					"CLAIM_PRICE_8"=>$RS['CLAIM_PRICE_8'],
					"CLAIM_PRICE_7"=>$RS['CLAIM_PRICE_7'],
					"CLAIM_PRICE_6"=>$RS['CLAIM_PRICE_6'],
					"CLAIM_PRICE_5"=>$RS['CLAIM_PRICE_5'],
					"CLAIM_PRICE_4"=>$RS['CLAIM_PRICE_4'],
					"CLAIM_PRICE_3"=>$RS['CLAIM_PRICE_3'],
					"CLAIM_PRICE_2"=>$RS['CLAIM_PRICE_2'],
					"CLAIM_PRICE_1"=>$RS['CLAIM_PRICE_1'],
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


	function getYear() {
		$iSQL = "SELECT 
					YEAR(NOW()) AS CURR_YEAR,
					YEAR(MIN(NB_CLAIM_DT))AS MIN_YEAR
				FROM NT_BILL.NBB_BILL
				WHERE DEL_YN = 'N'
				AND NB_CLAIM_DT != '0000-00-00';
		";

		return DBManager::getRecordSet($iSQL);	
	}

}
	// 0. 객체 생성
	$cClaimGw = new cClaimGw(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$cClaimGw->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
