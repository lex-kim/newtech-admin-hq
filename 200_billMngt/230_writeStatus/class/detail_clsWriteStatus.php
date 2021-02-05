<?php

class clsWriteStatus extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	// Class Object Constructor
	function clsWriteStatus($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
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
		$DATE_FORMAT = "";

		if($ARR['DATE_FORMAT_SEARCH'] == 'MONTH'){  // 월별
			$DATE_FORMAT =  "DATE_FORMAT(NB_CLAIM_DT,'%Y-%m')";
		}else{ 										// 일별
			$DATE_FORMAT =  "DATE_FORMAT(NB_CLAIM_DT,'%Y-%m-%d')";
		}
		
		if($ARR['START_DT'] !='' && $ARR['END_DT'] !=''){
			$tmpKeyword .= "AND DATE(`NB_CLAIM_DT`) BETWEEN '".addslashes($ARR['START_DT'])."' AND '".addslashes($ARR['END_DT'])."'";
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
				GROUP BY $DATE_FORMAT 
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

		

		// $iSQL = "SELECT 
		// 			$DATE_FORMAT AS NB_CLAIM_DT,
					
		// 			COUNT(DISTINCT MGG_ID) AS MGG_CNT,
		// 			COUNT(DISTINCT IF(NB.MODE_TARGET = '10101', MGG_ID, NULL))AS MGG_11110,
		// 			COUNT(DISTINCT IF(NB.MODE_TARGET = '10102', MGG_ID, NULL))AS MGG_11111,
		// 			COUNT(DISTINCT IF(NB.MODE_TARGET = '10103', MGG_ID, NULL))AS MGG_11112,
					
		// 			COUNT(NB.NB_ID) AS CLAIM_CNT,
		// 			SUM(IF(NB.MODE_TARGET = '10101' , 1 ,0)) AS CLAIM_11110,
		// 			SUM(IF(NB.MODE_TARGET = '10102' , 1 ,0)) AS CLAIM_11111,
		// 			SUM(IF(NB.MODE_TARGET = '10103' , 1 ,0)) AS CLAIM_11112,
					
		// 			-- FORMAT(SUM(NB_TOTAL_PRICE),0) AS NB_TOTAL_PRICE,
		// 			SUM(NB_TOTAL_PRICE) AS NB_TOTAL_PRICE,
		// 			FORMAT(SUM(IF(NB.MODE_TARGET = '10101' , NB_TOTAL_PRICE ,0)),0) AS NB_TOTAL_PRICE_11110,
		// 			FORMAT(SUM(IF(NB.MODE_TARGET = '10102' , NB_TOTAL_PRICE ,0)),0) AS NB_TOTAL_PRICE_11111,
		// 			FORMAT(SUM(IF(NB.MODE_TARGET = '10103' , NB_TOTAL_PRICE ,0)),0) AS NB_TOTAL_PRICE_11112,
					
		// 			-- FORMAT(IFNULL((SUM(IF(NB.MODE_TARGET = '10101' , NB_TOTAL_PRICE ,0))/SUM(IF(NB.MODE_TARGET = '10101' , 1 ,0))),0)+IFNULL(((SUM(IF(NB.MODE_TARGET = '10102' , NB_TOTAL_PRICE ,0)) + SUM(IF(NB.RMT_ID = '11525' , NB_TOTAL_PRICE ,0)))/ ( SUM(IF(NB.MODE_TARGET = '10102' , 1 ,0))+SUM(IF(NB.RMT_ID = '11525' , 1 ,0)))),0)+IFNULL((SUM(IF(NB.MODE_TARGET = '10103' , NB_TOTAL_PRICE ,0))/SUM(IF(NB.MODE_TARGET = '10103' , 1 ,0))),0),0) AS NB_PRICE,
		// 			FORMAT(IFNULL((SUM(IF(NB.MODE_TARGET = '10101' , NB_TOTAL_PRICE ,0))/SUM(IF(NB.MODE_TARGET = '10101' , 1 ,0))),0),0) AS NB_PRICE_11110,
		// 			FORMAT(IFNULL((SUM(IF(NB.MODE_TARGET = '10102' , NB_TOTAL_PRICE ,0))/SUM(IF(NB.MODE_TARGET = '10102' , 1 ,0))),0),0) AS NB_PRICE_11111,
		// 			FORMAT(IFNULL((SUM(IF(NB.MODE_TARGET = '10103' , NB_TOTAL_PRICE ,0))/SUM(IF(NB.MODE_TARGET = '10103' , 1 ,0))),0),0) AS NB_PRICE_11112,
				
		// 			-- ROUND(COUNT(DISTINCT MGG_ID, CASE WHEN NB.MODE_TARGET = '10101' THEN NB.RMT_ID END) /COUNT(DISTINCT MGG_ID),1) *100 AS MGG_PER_11110,
		// 			ROUND(COUNT(DISTINCT IF(NB.MODE_TARGET = '10101', MGG_ID, NULL)) /COUNT(DISTINCT MGG_ID)*100,1)  AS MGG_PER_11110,
		// 			ROUND(COUNT(DISTINCT IF(NB.MODE_TARGET = '10102', MGG_ID, NULL)) /COUNT(DISTINCT MGG_ID)*100,1)  AS MGG_PER_11111,
		// 			ROUND(COUNT(DISTINCT IF(NB.MODE_TARGET = '10103', MGG_ID, NULL)) /COUNT(DISTINCT MGG_ID)*100,1)  AS MGG_PER_11112,
				
		// 			ROUND(SUM(IF(NB.MODE_TARGET = '10101' , 1 ,0)) /COUNT(NB.NB_ID) *100,1) AS TOTAL_PER_11110,
		// 			ROUND(SUM(IF(NB.MODE_TARGET = '10102' , 1 ,0)) /COUNT(NB.NB_ID) *100,1) AS TOTAL_PER_11111,
		// 			ROUND(SUM(IF(NB.MODE_TARGET = '10103' , 1 ,0)) /COUNT(NB.NB_ID) *100,1) AS TOTAL_PER_11112,

		// 			ROUND((SUM(IF(NB.MODE_TARGET = '10101' , NB_TOTAL_PRICE ,0))/SUM(NB_TOTAL_PRICE)*100),1) AS PRICE_PER_11110,
		// 			ROUND((SUM(IF(NB.MODE_TARGET = '10102' , NB_TOTAL_PRICE ,0))/SUM(NB_TOTAL_PRICE)*100),1) AS PRICE_PER_11111,
		// 			ROUND((SUM(IF(NB.MODE_TARGET = '10103' , NB_TOTAL_PRICE ,0))/SUM(NB_TOTAL_PRICE)*100),1) AS PRICE_PER_11112
					
		// 		FROM NT_BILL.NBB_BILL NB
		// 		-- NT_BILL.NBB_BILL_INSU NBI
		// 		WHERE  NB.DEL_YN = 'N'
		// 		-- AND NBI.NB_ID = NB.NB_ID
		// 		$tmpKeyword 
		// 		GROUP BY $DATE_FORMAT 
		// 		ORDER BY NB_CLAIM_DT DESC
		// 		$LIMIT_SQL 
		// 	;"

		// ;
		$iSQL = "SELECT 
				 NB_ID, NB.MGG_ID, NB_GARAGE_NM, NB_CAR_NUM, NB_CLAIM_DT, 
				 NT_CODE.GET_CODE_NM(RLT_ID) as 'RLT_ID', 
				 NT_CODE.GET_CODE_NM(RMT_ID) AS 'RMT_ID', CCC_ID, 
				 NB_VAT_DEDUCTION_YN, NB_INC_DTL_YN, NB_NEW_INSU_YN, 
				 IF(NB.RLT_ID<>'11440', NB_TOTAL_PRICE, NT_MAIN.GET_11440_BILLPRICE(NB.NB_ID)) as 'NB_TOTAL_PRICE', NB_VAT_PRICE, NB_CLAIM_USERID, NB_CLAIM_DTHMS, NB_DUPLICATED_YN, 
				 NB.WRT_DTHMS, NB.LAST_DTHMS, NB.WRT_USERID, NB.DEL_YN, NB_DEL_REASON, NB_DEL_DTHMS, NB_DEL_USERID, NB_AB_EST_SEQ1, NB_AB_EST_SEQ2, NB_AB_VIEW_URI, NB_AB_NSRSN, NB_AB_NSRSN_VAL, NB_AB_BILL_YN, NB_AB_NSRSN_DTHMS, NB_AB_NSRSN_USERID, NT_CODE.GET_CODE_NM(MODE_TARGET) AS MODE_TARGET
				FROM NT_BILL.NBB_BILL NB,
				NT_MAIN.NMG_GARAGE NG
				-- NT_BILL.NBB_BILL_INSU NBI
				WHERE  NB.DEL_YN = 'N'
				AND NB.MGG_ID = NG.MGG_ID
				 AND NG.DEL_YN = 'N'
				-- AND NBI.NB_ID = NB.NB_ID
				$tmpKeyword 
	
				ORDER BY NB_CLAIM_DT DESC
				$LIMIT_SQL ;
			";
		// echo $iSQL;
		$Result = DBManager::getResult($iSQL);

		$rtnArray = array();
		
		$count = 0;
		while($RS = $Result->fetch_array()){
			$MGG_PER_TOTAL = ($RS['MGG_PER_11110']+$RS['MGG_PER_11111']+$RS['MGG_PER_11112']) > 100 ? 100 : ($RS['MGG_PER_11110']+$RS['MGG_PER_11111']+$RS['MGG_PER_11112']);
			$TOTAL_PER_TOTAL = ($RS['TOTAL_PER_11110']+$RS['TOTAL_PER_11111']+$RS['TOTAL_PER_11112']) > 100 ? 100 : ($RS['TOTAL_PER_11110']+$RS['TOTAL_PER_11111']+$RS['TOTAL_PER_11112']);
			$PRICE_PER_TOTAL = ($RS['PRICE_PER_11110']+$RS['PRICE_PER_11111']+$RS['PRICE_PER_11112']) > 100 ? 100 : ($RS['PRICE_PER_11110']+$RS['PRICE_PER_11111']+$RS['PRICE_PER_11112']);
			array_push($rtnArray,
				array(
					"NB_ID"=>$RS['NB_ID'], 
					"MGG_ID"=>$RS['MGG_ID'], 
					"NB_GARAGE_NM"=>$RS['NB_GARAGE_NM'], 
					"NB_CAR_NUM"=>$RS['NB_CAR_NUM'], 
					"NB_CLAIM_DT"=>$RS['NB_CLAIM_DT'], 
					"RLT_ID"=>$RS['RLT_ID'], 
					"RMT_ID"=>$RS['RMT_ID'], 
					"CCC_ID"=>$RS['CCC_ID'], 
					"NB_VAT_DEDUCTION_YN"=>$RS['NB_VAT_DEDUCTION_YN'], 
					"NB_INC_DTL_YN"=>$RS['NB_INC_DTL_YN'], 
					"NB_NEW_INSU_YN"=>$RS['NB_NEW_INSU_YN'], 
					"NB_TOTAL_PRICE"=>$RS['NB_TOTAL_PRICE'], 
					"NB_VAT_PRICE"=>$RS['NB_VAT_PRICE'], 
					"NB_CLAIM_USERID"=>$RS['NB_CLAIM_USERID'], 
					"NB_CLAIM_DTHMS"=>$RS['NB_CLAIM_DTHMS'], 
					"NB_DUPLICATED_YN"=>$RS['NB_DUPLICATED_YN'], 
					"WRT_DTHMS"=>$RS['WRT_DTHMS'], 
					"LAST_DTHMS"=>$RS['LAST_DTHMS'], 
					"WRT_USERID"=>$RS['WRT_USERID'], 
					"DEL_YN"=>$RS['DEL_YN'], 
					"NB_DEL_REASON"=>$RS['NB_DEL_REASON'], 
					"NB_DEL_DTHMS"=>$RS['NB_DEL_DTHMS'], 
					"NB_DEL_USERID"=>$RS['NB_DEL_USERID'], 
					"NB_AB_EST_SEQ1"=>$RS['NB_AB_EST_SEQ1'], 
					"NB_AB_EST_SEQ2"=>$RS['NB_AB_EST_SEQ2'], 
					"NB_AB_VIEW_URI"=>$RS['NB_AB_VIEW_URI'], 
					"NB_AB_NSRSN"=>$RS['NB_AB_NSRSN'], 
					"NB_AB_NSRSN_VAL"=>$RS['NB_AB_NSRSN_VAL'], 
					"NB_AB_BILL_YN"=>$RS['NB_AB_BILL_YN'], 
					"NB_AB_NSRSN_DTHMS"=>$RS['NB_AB_NSRSN_DTHMS'], 
					"NB_AB_NSRSN_USERID"=>$RS['NB_AB_NSRSN_USERID'], 
					"MODE_TARGET"=>$RS['MODE_TARGET'], 
					"NB_CLAIM_DT"=>$RS['NB_CLAIM_DT'],
					"MGG_CNT"=>$RS['MGG_CNT'],
					"MGG_11110"=>$RS['MGG_11110'],
					"MGG_11111"=>$RS['MGG_11111'],
					"MGG_11112"=>$RS['MGG_11112'],
					"CLAIM_CNT"=>$RS['CLAIM_CNT'],
					"CLAIM_11110"=>$RS['CLAIM_11110'],
					"CLAIM_11111"=>$RS['CLAIM_11111'],
					"CLAIM_11112"=>$RS['CLAIM_11112'],
					"NB_TOTAL_PRICE"=>$RS['NB_TOTAL_PRICE'],
					"NB_TOTAL_PRICE_11110"=>$RS['NB_TOTAL_PRICE_11110'],
					"NB_TOTAL_PRICE_11111"=>$RS['NB_TOTAL_PRICE_11111'],
					"NB_TOTAL_PRICE_11112"=>$RS['NB_TOTAL_PRICE_11112'],
					"NB_PRICE"=>$RS['NB_PRICE'],
					"NB_PRICE_11110"=>$RS['NB_PRICE_11110'],
					"NB_PRICE_11111"=>$RS['NB_PRICE_11111'],
					"NB_PRICE_11112"=>$RS['NB_PRICE_11112'],
					"MGG_PER_TOTAL"=>round($MGG_PER_TOTAL),
					"MGG_PER_11110"=>$RS['MGG_PER_11110'],
					"MGG_PER_11111"=>$RS['MGG_PER_11111'],
					// "MGG_PER_11112"=>$MGG_PER_TOTAL-($RS['MGG_PER_11111']+$RS['MGG_PER_11110']),
					"MGG_PER_11112"=>$RS['MGG_PER_11112'],
					"TOTAL_PER_TOTAL"=>round($TOTAL_PER_TOTAL,0),
					"TOTAL_PER_11110"=>$RS['TOTAL_PER_11110'],
					"TOTAL_PER_11111"=>$RS['TOTAL_PER_11111'],
					"TOTAL_PER_11112"=>round($TOTAL_PER_TOTAL-($RS['TOTAL_PER_11111']+$RS['TOTAL_PER_11110']),1),
					// "TOTAL_PER_11112"=>$RS['TOTAL_PER_11112'],
					"PRICE_PER_TOTAL"=>round($PRICE_PER_TOTAL,0),
					"PRICE_PER_11110"=>$RS['PRICE_PER_11110'],
					"PRICE_PER_11111"=>$RS['PRICE_PER_11111'],
					"PRICE_PER_11112"=>round($PRICE_PER_TOTAL-($RS['PRICE_PER_11111']+$RS['PRICE_PER_11110']),1),
					// "PRICE_PER_11112"=>$RS['PRICE_PER_11112'],

			));
		}

		$Result->close();  // 자원 반납
		unset($RS);

		$MGG_PER_TOTAL = ($RS['MGG_PER_11110']+$RS['MGG_PER_11111']+$RS['MGG_PER_11112']) > 100 ? 100 : ($RS['MGG_PER_11110']+$RS['MGG_PER_11111']+$RS['MGG_PER_11112']);
		$TOTAL_PER_TOTAL = ($RS['TOTAL_PER_11110']+$RS['TOTAL_PER_11111']+$RS['TOTAL_PER_11112']) > 100 ? 100 : ($RS['TOTAL_PER_11110']+$RS['TOTAL_PER_11111']+$RS['TOTAL_PER_11112']);
		$PRICE_PER_TOTAL = ($RS['PRICE_PER_11110']+$RS['PRICE_PER_11111']+$RS['PRICE_PER_11112']) > 100 ? 100 : ($RS['PRICE_PER_11110']+$RS['PRICE_PER_11111']+$RS['PRICE_PER_11112']);
		
		$totalSQL = "SELECT 
						SUM(MGG_CNT) AS MGG_CNT,
						SUM(MGG_11110) AS MGG_11110,
						SUM(MGG_11111) AS MGG_11111,
						SUM(MGG_11112) AS MGG_11112,
					
						SUM(CLAIM_CNT) AS CLAIM_CNT,
						SUM(CLAIM_11110) AS CLAIM_11110,
						SUM(CLAIM_11111) AS CLAIM_11111,
						SUM(CLAIM_11112) AS CLAIM_11112,
					
						SUM(NB_TOTAL_PRICE) AS NB_TOTAL_PRICE,
						SUM(NB_TOTAL_PRICE_11110) AS NB_TOTAL_PRICE_11110,
						SUM(NB_TOTAL_PRICE_11111) AS NB_TOTAL_PRICE_11111,
						SUM(NB_TOTAL_PRICE_11112) AS NB_TOTAL_PRICE_11112,
					
					
						SUM(NB_PRICE_11110) AS NB_PRICE_11110,
						SUM(NB_PRICE_11111) AS NB_PRICE_11111,
						SUM(NB_PRICE_11112) AS NB_PRICE_11112,
					
						FORMAT(IF((SUM(MGG_11110)/SUM(MGG_CNT)*100 + SUM(MGG_11111)/SUM(MGG_CNT)*100 + SUM(MGG_11112)/SUM(MGG_CNT)*100) > 100 , 
							100,
							(SUM(MGG_11110)/SUM(MGG_CNT)*100 + SUM(MGG_11111)/SUM(MGG_CNT)*100 + SUM(MGG_11112)/SUM(MGG_CNT)*100)
						),0) AS MGG_PER_TOTAL,
						ROUND(SUM(MGG_11110)/SUM(MGG_CNT)*100,1) AS MGG_PER_11110,
						ROUND(SUM(MGG_11111)/SUM(MGG_CNT)*100,1) AS MGG_PER_11111,
						ROUND(SUM(MGG_11112)/SUM(MGG_CNT)*100,1) AS MGG_PER_11112,

						FORMAT(IF((SUM(CLAIM_11110)/SUM(CLAIM_CNT)*100 + SUM(CLAIM_11111)/SUM(CLAIM_CNT)*100 + SUM(CLAIM_11112)/SUM(CLAIM_CNT)*100) > 100 , 
							100,
							(SUM(CLAIM_11110)/SUM(CLAIM_CNT)*100 + SUM(CLAIM_11111)/SUM(CLAIM_CNT)*100 + SUM(CLAIM_11112)/SUM(CLAIM_CNT)*100)
						),0) AS TOTAL_PER_TOTAL,
						ROUND(SUM(CLAIM_11110)/SUM(CLAIM_CNT)*100,1) AS TOTAL_PER_11110,
						ROUND(SUM(CLAIM_11111)/SUM(CLAIM_CNT)*100,1) AS TOTAL_PER_11111,
						ROUND(SUM(CLAIM_11112)/SUM(CLAIM_CNT)*100,1) AS TOTAL_PER_11112,
					
						FORMAT(IF((SUM(NB_TOTAL_PRICE_11110)/SUM(NB_TOTAL_PRICE)*100 + SUM(NB_TOTAL_PRICE_11111)/SUM(NB_TOTAL_PRICE)*100 + SUM(NB_TOTAL_PRICE_11112)/SUM(NB_TOTAL_PRICE)*100) > 100 , 
							100,
							(SUM(NB_TOTAL_PRICE_11110)/SUM(NB_TOTAL_PRICE)*100 + SUM(NB_TOTAL_PRICE_11111)/SUM(NB_TOTAL_PRICE)*100 + SUM(NB_TOTAL_PRICE_11112)/SUM(NB_TOTAL_PRICE)*100)
						),0) AS PRICE_PER_TOTAL,
						ROUND(SUM(NB_TOTAL_PRICE_11110)/SUM(NB_TOTAL_PRICE)*100,1) AS PRICE_PER_11110,
						ROUND(SUM(NB_TOTAL_PRICE_11111)/SUM(NB_TOTAL_PRICE)*100,1) AS PRICE_PER_11111,
						ROUND(SUM(NB_TOTAL_PRICE_11112)/SUM(NB_TOTAL_PRICE)*100,1) AS PRICE_PER_11112
					FROM (
						SELECT 	
							'TOTAL' AS 'TOTAL',
							
							COUNT(DISTINCT MGG_ID) AS MGG_CNT,
							COUNT(DISTINCT IF(MODE_TARGET = '10101', MGG_ID, NULL))AS MGG_11110,
							COUNT(DISTINCT IF(MODE_TARGET = '10102', MGG_ID, NULL))AS MGG_11111,
							COUNT(DISTINCT IF(MODE_TARGET = '10103', MGG_ID, NULL))AS MGG_11112,
							
							COUNT(NB_ID) AS CLAIM_CNT,
							SUM(IF(MODE_TARGET = '10101' , 1 ,0)) AS CLAIM_11110,
							SUM(IF(MODE_TARGET = '10102' , 1 ,0)) AS CLAIM_11111,
							SUM(IF(MODE_TARGET = '10103' , 1 ,0)) AS CLAIM_11112,
							
							-- FORMAT(SUM(NB_TOTAL_PRICE),0) AS NB_TOTAL_PRICE,
							SUM(NB_TOTAL_PRICE) AS NB_TOTAL_PRICE,
							SUM(IF(MODE_TARGET = '10101' , NB_TOTAL_PRICE ,0)) AS NB_TOTAL_PRICE_11110,
							SUM(IF(MODE_TARGET = '10102' , NB_TOTAL_PRICE ,0)) AS NB_TOTAL_PRICE_11111,
							SUM(IF(MODE_TARGET = '10103' , NB_TOTAL_PRICE ,0)) AS NB_TOTAL_PRICE_11112,
							
							-- FORMAT(IFNULL((SUM(IF(MODE_TARGET = '10101' , NB_TOTAL_PRICE ,0))/SUM(IF(MODE_TARGET = '10101' , 1 ,0))),0)+IFNULL(((SUM(IF(MODE_TARGET = '10102' , NB_TOTAL_PRICE ,0)) + SUM(IF(RMT_ID = '11525' , NB_TOTAL_PRICE ,0)))/ ( SUM(IF(MODE_TARGET = '10102' , 1 ,0))+SUM(IF(RMT_ID = '11525' , 1 ,0)))),0)+IFNULL((SUM(IF(MODE_TARGET = '10103' , NB_TOTAL_PRICE ,0))/SUM(IF(MODE_TARGET = '10103' , 1 ,0))),0),0) AS NB_PRICE,
							IFNULL((SUM(IF(MODE_TARGET = '10101' , NB_TOTAL_PRICE ,0))/SUM(IF(MODE_TARGET = '10101' , 1 ,0))),0) AS NB_PRICE_11110,
							IFNULL((SUM(IF(MODE_TARGET = '10102' , NB_TOTAL_PRICE ,0))/SUM(IF(MODE_TARGET = '10102' , 1 ,0))),0)AS NB_PRICE_11111,
							IFNULL((SUM(IF(MODE_TARGET = '10103' , NB_TOTAL_PRICE ,0))/SUM(IF(MODE_TARGET = '10103' , 1 ,0))),0) AS NB_PRICE_11112,
						
							-- ROUND(COUNT(DISTINCT MGG_ID, CASE WHEN MODE_TARGET = '10101' THEN RMT_ID END) /COUNT(DISTINCT MGG_ID),1) *100 AS MGG_PER_11110,
							ROUND(COUNT(DISTINCT IF(MODE_TARGET = '10101', MGG_ID, NULL)) /COUNT(DISTINCT MGG_ID)*100,1)  AS MGG_PER_11110,
							ROUND(COUNT(DISTINCT IF(MODE_TARGET = '10102', MGG_ID, NULL)) /COUNT(DISTINCT MGG_ID)*100,1)  AS MGG_PER_11111,
							ROUND(COUNT(DISTINCT IF(MODE_TARGET = '10103', MGG_ID, NULL)) /COUNT(DISTINCT MGG_ID)*100,1)  AS MGG_PER_11112,
						
							ROUND(SUM(IF(MODE_TARGET = '10101' , 1 ,0)) /COUNT(NB_ID) *100,1) AS TOTAL_PER_11110,
							ROUND(SUM(IF(MODE_TARGET = '10102' , 1 ,0)) /COUNT(NB_ID) *100,1) AS TOTAL_PER_11111,
							ROUND(SUM(IF(MODE_TARGET = '10103' , 1 ,0)) /COUNT(NB_ID) *100,1) AS TOTAL_PER_11112,

							ROUND((SUM(IF(MODE_TARGET = '10101' , NB_TOTAL_PRICE ,0))/SUM(NB_TOTAL_PRICE)*100),1) AS PRICE_PER_11110,
							ROUND((SUM(IF(MODE_TARGET = '10102' , NB_TOTAL_PRICE ,0))/SUM(NB_TOTAL_PRICE)*100),1) AS PRICE_PER_11111,
							ROUND((SUM(IF(MODE_TARGET = '10103' , NB_TOTAL_PRICE ,0))/SUM(NB_TOTAL_PRICE)*100),1) AS PRICE_PER_11112
							
							
							
						FROM NT_BILL.NBB_BILL
						WHERE  DEL_YN = 'N'
						$tmpKeyword 
						GROUP BY $DATE_FORMAT 
						$LIMIT_SQL 
					)AS TOTAL
					GROUP BY TOTAL ;
		";
		$TOTAL_ROW = DBManager::getRecordSet($totalSQL);
		
		if(empty($rtnArray)){
			return HAVE_NO_DATA;
		}else{
			$jsonArray = array();
			array_push($jsonArray,$rtnArray);
			array_push($jsonArray,$TOTAL_LIST_COUNT);
			array_push($jsonArray,$WORKS_ARRAY);     // 엑셀에서 헤더 동적으로생성하기 위함..
			array_push($jsonArray,$TOTAL_ROW);     // 합계 출력
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
	$clsWriteStatus = new clsWriteStatus(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$clsWriteStatus->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
