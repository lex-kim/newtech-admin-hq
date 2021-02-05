<?php

class cDepositGw extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	// Class Object Constructor
	function cDepositGw($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
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

		if($ARR['REQ_MODE'] == CASE_LIST){
			$LIMIT_SQL = "LIMIT $LimitStart,$LimitEnd";
        }
        
        
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
                    $joinNBI .= "AND DATE_FORMAT(NBI.NBI_INSU_DEPOSIT_DT, '%Y-%m-%d') BETWEEN '".addslashes($ARR['START_DT_SEARCH'])."' AND '".addslashes($ARR['END_DT_SEARCH'])."'";
                }else if($ARR['START_DT_SEARCH'] != '' ){
                    $joinNBI .= "AND DATE_FORMAT(NBI.NBI_INSU_DEPOSIT_DT, '%Y-%m-%d') >= '".$ARR['START_DT_SEARCH']."' \n";
                }else if($ARR['END_DT_SEARCH'] != ''){
                    $joinNBI .= "AND DATE_FORMAT(NBI.NBI_INSU_DEPOSIT_DT, '%Y-%m-%d') <= '".$ARR['END_DT_SEARCH']."' \n";
                }
            }else if($ARR['DT_KEY_SEARCH'] == 'MGG_BIZ_START_DT'){ // 개시일
                if($ARR['START_DT_SEARCH'] != '' && $ARR['END_DT_SEARCH'] != ''){
                    $tmpKeyword .= "AND DATE_FORMAT(NGS.MGG_BIZ_START_DT, '%Y-%m-%d') BETWEEN '".addslashes($ARR['START_DT_SEARCH'])."' AND '".addslashes($ARR['END_DT_SEARCH'])."'";
                }else if($ARR['START_DT_SEARCH'] != '' ){
                    $tmpKeyword .= "AND DATE_FORMAT(NGS.MGG_BIZ_START_DT, '%Y-%m-%d') >= '".$ARR['START_DT_SEARCH']."' \n";
                }else if($ARR['END_DT_SEARCH'] != ''){
                    $tmpKeyword .= "AND DATE_FORMAT(NGS.MGG_BIZ_START_DT, '%Y-%m-%d') <= '".$ARR['END_DT_SEARCH']."' \n";
                }
			}else if($ARR['DT_KEY_SEARCH'] == 'MGG_BIZ_END_DT'){ // 종료일
                if($ARR['START_DT_SEARCH'] != '' && $ARR['END_DT_SEARCH'] != ''){
                    $tmpKeyword .= "AND DATE_FORMAT(NGS.MGG_BIZ_END_DT, '%Y-%m-%d') BETWEEN '".addslashes($ARR['START_DT_SEARCH'])."' AND '".addslashes($ARR['END_DT_SEARCH'])."'";
                }else if($ARR['START_DT_SEARCH'] != '' ){
                    $tmpKeyword .= "AND DATE_FORMAT(NGS.MGG_BIZ_END_DT, '%Y-%m-%d') >= '".$ARR['START_DT_SEARCH']."' \n";
                }else if($ARR['END_DT_SEARCH'] != ''){
                    $tmpKeyword .= "AND DATE_FORMAT(NGS.MGG_BIZ_END_DT, '%Y-%m-%d') <= '".$ARR['END_DT_SEARCH']."' \n";
                }
			}else if($ARR['DT_KEY_SEARCH'] == 'MGG_CONTRACT_START_DT'){ // 계약일
                if($ARR['START_DT_SEARCH'] != '' && $ARR['END_DT_SEARCH'] != ''){
                    $tmpKeyword .= "AND DATE_FORMAT(NGS.MGG_CONTRACT_START_DT, '%Y-%m-%d') BETWEEN '".addslashes($ARR['START_DT_SEARCH'])."' AND '".addslashes($ARR['END_DT_SEARCH'])."'";
                }else if($ARR['START_DT_SEARCH'] != '' ){
                    $tmpKeyword .= "AND DATE_FORMAT(NGS.MGG_CONTRACT_START_DT, '%Y-%m-%d') >= '".$ARR['START_DT_SEARCH']."' \n";
                }else if($ARR['END_DT_SEARCH'] != ''){
                    $tmpKeyword .= "AND DATE_FORMAT(NGS.MGG_CONTRACT_START_DT, '%Y-%m-%d') <= '".$ARR['END_DT_SEARCH']."' \n";
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
			$tmpKeyword .= "AND NB.MGG_ID  = '".$ARR['MGG_NM_SEARCH']."' \n";
		} 

		//청구식검색
		if($ARR['RET_ID_SEARCH'] != '') {
			$tmpKeyword .= "AND NGS.RET_ID IN ( ";
            foreach ($ARR['RET_ID_SEARCH'] as $index => $RET_ID) {
                $tmpKeyword .=" '".$RET_ID."',";
            }
            $tmpKeyword = substr($tmpKeyword, 0, -1); // 마지막 콤마제거
            $tmpKeyword .= ") \n";
		}

		//수탁자
		if($ARR['MGG_BAILEE_NM_SEARCH'] != ''){
			$tmpKeyword .="AND INSTR(NGS.`MGG_BAILEE_NM`, '".addslashes($ARR['MGG_BAILEE_NM_SEARCH'])."' ) > 0 \n";
		}

		//거래여부
		if($ARR['RCT_ID_SEARCH'] != '') {
			$tmpKeyword .= "AND NGS.RCT_ID = '".addslashes($ARR['RCT_ID_SEARCH'])."' \n";
		} 

		//계약여부
		if($ARR['MGG_CONTRACT_YN_SEARCH'] != '') {
			$tmpKeyword .= "AND NGS.MGG_CONTRACT_YN = '".addslashes($ARR['MGG_CONTRACT_YN_SEARCH'])."' \n";
		} 


		// 페이징처리를 위해 전체데이터
		$iSQL = "SELECT COUNT(*) AS 'CNT'
				FROM (SELECT 
						COUNT(*) AS 'CNT'
						FROM  
							`NT_MAIN`.`NMG_GARAGE` NG
						JOIN `NT_MAIN`.`NMG_GARAGE_SUB` NGS
						ON NG.MGG_ID = NGS.MGG_ID AND NG.DEL_YN = 'N'
						LEFT OUTER JOIN `NT_BILL`.`NBB_BILL` NB
						ON NG.MGG_ID = NB.MGG_ID AND NB.DEL_YN = 'N'
						$joinNB
						LEFT OUTER JOIN `NT_BILL`.`NBB_BILL_INSU` NBI
						ON NB.NB_ID = NBI.NB_ID
						$joinNBI
					WHERE 1=1
						$tmpKeyword 
					GROUP BY NG.MGG_ID,  NB.MGG_ID 
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

		$NB_RATIO_PRICE = "TRUNCATE(IFNULL(NB.`NB_TOTAL_PRICE`,'0')*IF(NBI.`NBI_RATIO`<>'',CAST(NBI.`NBI_RATIO` AS DECIMAL)/100,1),-1)" ; 
		$DECREMENT_DEPOSIT = "IFNULL(`NBI_INSU_DEPOSIT_PRICE`,0)+IFNULL(`NBI_INSU_ADD_DEPOSIT_PRICE`,0)+IFNULL(`NBI_OTHER_PRICE`,0)";

		// 리스트 가져오기
		$iSQL = "SELECT 
					`NG`.`MGG_ID`,
					`NG`.`CSD_ID`,
					`NT_CODE`.`GET_CSD_NM`(`NG`.`CSD_ID`) AS 'CSD_NM',
					`NG`.`CRR_1_ID`,
					`NG`.`CRR_2_ID`,
					`NG`.`CRR_3_ID`,
					`NT_CODE`.`GET_CRR_NM`(`NG`.`CRR_1_ID`,`NG`.`CRR_2_ID`,'00000') AS 'CRR_NM',
					
					`NG`.`MGG_BIZ_ID`,
					`NG`.`MGG_NM`,
					`NGS`.`RET_ID`, #청구식
					`NT_CODE`.`GET_CODE_NM`(`NGS`.`RET_ID`) AS 'RET_NM',
					`NGS`.`MGG_BAILEE_NM`, #수탁자
					`NGS`.`RCT_ID`, #거래여부
					`NT_CODE`.`GET_CODE_NM`(`NGS`.`RCT_ID`) AS 'RCT_NM',
					`NGS`.`MGG_BIZ_START_DT`,
					`NGS`.`MGG_BIZ_END_DT`,
					`NGS`.`MGG_CONTRACT_YN`, #계약여부
					`NGS`.`MGG_CONTRACT_START_DT`,
					`NGS`.`MGG_CONTRACT_END_DT`,
					`NGS`.`MGG_RATIO`, #수수료율
					CAST(AES_DECRYPT(`NGS`.`MGG_BANK_NM_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS 'MGG_BANK_NM_AES',
					CAST(AES_DECRYPT(`NGS`.`MGG_BANK_NUM_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS 'MGG_BANK_NUM_AES',
					CAST(AES_DECRYPT(`NGS`.`MGG_BANK_OWN_NM_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS 'MGG_BANK_OWN_NM_AES',
					IFNULL(SUM(IF(NBI.NB_ID IS NOT NULL,1,0)),0) AS 'NB_TOTAL_CNT', #총청구건수
					IFNULL(SUM($NB_RATIO_PRICE),0) AS 'NB_TOTAL_PRICE', #총청구금액
					IFNULL(SUM(IF(NBI.`RDET_ID` <> '40121', 1, 0)),0) AS 'NBI_TOTAL_DEPOSIT_CNT', #입금건수
					IFNULL(SUM(IF(NBI.`RDET_ID` <> '40121', $NB_RATIO_PRICE, 0)),0) AS 'NBI_TOTAL_DEPOSIT_BILL_PRICE', #입금청구액
					IFNULL(SUM(NBI.NBI_INSU_DEPOSIT_PRICE),0) AS 'NBI_TOTAL_DEPOSIT_PRICE', #입금금액
					IFNULL(  
						IFNULL(SUM(IF(NBI.`RDET_ID` <> '40121', $NB_RATIO_PRICE, 0)),0)
                         -
                         ( IFNULL(SUM(IF(NBI.`RDET_ID` <> '40121',NBI.NBI_INSU_DEPOSIT_PRICE,0)),0) + IFNULL(SUM( IF(NBI.`RDET_ID` <> '40121',NBI.NBI_INSU_ADD_DEPOSIT_PRICE,0)),0))
					,0) AS 'DECREMENT_PRICE', #감액금액
					IFNULL(SUM(IF(NBI.`RDET_ID` = '40121',IF(NBI.`NBI_INSU_DONE_YN` = 'Y',1,0),0)),0) AS 'UNPAID_DONE_CNT', #미입금 종결 건수
					IFNULL(SUM(IF(NBI.`RDET_ID` = '40121',IF(NBI.`NBI_INSU_DONE_YN` = 'Y',$NB_RATIO_PRICE,0),0)),0) AS 'UNPAID_DONE_BILL_PRICE', #미입금 종결 청구금액
					IFNULL(SUM(IF(NBI.NB_ID IS NOT NULL,1,0)) - SUM(IF(NBI.`RDET_ID` <> '40121', 1, 0)) - SUM(IF(NBI.`RDET_ID` = '40121',IF(NBI.`NBI_INSU_DONE_YN` = 'Y',1,0),0)) ,0) AS 'UNPAID_CNT', #미입금건수 
					IFNULL(SUM($NB_RATIO_PRICE)  - SUM(IF(NBI.`RDET_ID` <> '40121', $NB_RATIO_PRICE, 0))  - SUM(IF(NBI.`RDET_ID` = '40121',IF(NBI.`NBI_INSU_DONE_YN` = 'Y',$NB_RATIO_PRICE,0),0)) ,0) AS 'UNPAID_PRICE',#미입금금액
					
					IFNULL(ROUND(SUM(IF(NBI.`RDET_ID` <> '40121', 1, 0)) / SUM(IF(NBI.NB_ID IS NOT NULL,1,0)) * 100, 2),0) AS 'DEPOSIT_CNT_RATIO', #입금률 건수
					IFNULL(ROUND(SUM(NBI.NBI_INSU_DEPOSIT_PRICE) / SUM($NB_RATIO_PRICE) * 100, 2),0) AS 'DEPOSIT_PRICE_RATIO', #입금률 금액
					IFNULL(ROUND(SUM(IF(NBI.`RDET_ID` <> '40121' AND $NB_RATIO_PRICE > $DECREMENT_DEPOSIT,1,0)) / SUM(IF(NBI.`RDET_ID` <> '40121',1,0)) * 100, 2),0) AS 'DECREMENT_CNT_RATIO', #감액률 건수
					IFNULL(
                        ROUND(
                            (
                                IFNULL(SUM(IF(NBI.`RDET_ID` <> '40121', $NB_RATIO_PRICE, 0)),0)
                                -
                                SUM(IF(NBI.`RDET_ID` <> '40121' AND $NB_RATIO_PRICE > $DECREMENT_DEPOSIT,$DECREMENT_DEPOSIT,0)) 
                            )
                            / 
                            SUM(IF(NBI.`RDET_ID` <> '40121', $NB_RATIO_PRICE, 0))
                            * 100
                        , 2)
                    ,0) AS 'DECREMENT_PRICE_RATIO', #감액률 금액
					IFNULL(ROUND(SUM(IF(NBI.`RDET_ID` = '40121',IF(NBI.`NBI_INSU_DONE_YN` = 'Y',1,0),0)) / SUM(IF(NBI.NB_ID IS NOT NULL,1,0)) * 100, 2),0) AS 'UNPAID_DONE_CNT_RATIO', #미입금종결률 건수
					IFNULL(ROUND(SUM(IF(NBI.`RDET_ID` = '40121',IF(NBI.`NBI_INSU_DONE_YN` = 'Y',$NB_RATIO_PRICE,0),0)) / SUM($NB_RATIO_PRICE) * 100, 2),0) AS 'UNPAID_DONE_PRICE_RATIO', #미입금종결률 금액
					IFNULL(ROUND((SUM(IF(NBI.NB_ID IS NOT NULL,1,0)) - SUM(IF(NBI.`RDET_ID` <> '40121', 1, 0)) - SUM(IF(NBI.`RDET_ID` = '40121',IF(NBI.`NBI_INSU_DONE_YN` = 'Y',1,0),0)))/SUM(IF(NBI.NB_ID IS NOT NULL,1,0)) * 100, 2),0) AS 'UNPAID_CNT_RATIO', #미입금률 건수
					IFNULL(ROUND((SUM($NB_RATIO_PRICE)  - SUM(IF(NBI.`RDET_ID` <> '40121', $NB_RATIO_PRICE, 0))  - SUM(IF(NBI.`RDET_ID` = '40121',IF(NBI.`NBI_INSU_DONE_YN` = 'Y',$NB_RATIO_PRICE,0),0)))/ SUM($NB_RATIO_PRICE) * 100, 2),0) AS 'UNPAID_PRICE_RATIO', #미입금률 금액
					
					IFNULL(SUM(IF(NBI.`RDET_ID` <> '40121', IF(NBI.`NBI_INSU_DONE_YN` = 'Y',1,0), 0)),0) AS 'PAID_DONE_CNT', #입금종결 건수
					IFNULL(SUM(IF(NBI.`RDET_ID` <> '40121', IF(NBI.`NBI_INSU_DONE_YN` = 'Y',$NB_RATIO_PRICE,0), 0)),0) AS 'PAID_DONE_BILL_PRICE', #입금종결 청구금액
					IFNULL(SUM(IF(NBI.`RDET_ID` <> '40121', IF(NBI.`NBI_INSU_DONE_YN` = 'Y',NBI.NBI_INSU_DEPOSIT_PRICE,0), 0)),0) AS 'PAID_DONE_DEPOSIT_PRICE', #입금종결 입금액
					
					IFNULL(ROUND((SUM(IF(NBI.`RDET_ID` <> '40121', IF(NBI.`NBI_INSU_DONE_YN` = 'Y',1,0), 0))) / SUM(IF(NBI.`RDET_ID` <> '40121', 1, 0)) *100, 2),0) AS 'PAID_DONE_CNT_RATIO', #입금종결률 건수
					IFNULL(ROUND(SUM(IF(NBI.`RDET_ID` <> '40121', IF(NBI.`NBI_INSU_DONE_YN` = 'Y',NBI.NBI_INSU_DEPOSIT_PRICE,0), 0))/ SUM(NBI.NBI_INSU_DEPOSIT_PRICE)  *100, 2),0) AS 'PAID_DONE_PRICE_RATIO' #입금종결률 금액
				
					
				FROM  
					`NT_MAIN`.`NMG_GARAGE` NG
				JOIN `NT_MAIN`.`NMG_GARAGE_SUB` NGS
				ON NG.MGG_ID = NGS.MGG_ID AND NG.DEL_YN = 'N'
				LEFT OUTER JOIN `NT_BILL`.`NBB_BILL` NB
				ON NG.MGG_ID = NB.MGG_ID AND NB.DEL_YN = 'N'
				$joinNB
				LEFT OUTER JOIN `NT_BILL`.`NBB_BILL_INSU` NBI
				ON NB.NB_ID = NBI.NB_ID
				$joinNBI
				WHERE 1=1
				$tmpKeyword 
				GROUP BY  NG.MGG_ID, NB.MGG_ID
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
					"MGG_BAILEE_NM"=>$RS['MGG_BAILEE_NM'],
					"RCT_ID"=>$RS['RCT_ID'],
					"RCT_NM"=>$RS['RCT_NM'],
					"MGG_BIZ_START_DT"=>$RS['MGG_BIZ_START_DT'],
					"MGG_BIZ_END_DT"=>$RS['MGG_BIZ_END_DT'],
					"MGG_CONTRACT_YN"=>$RS['MGG_CONTRACT_YN'],
					"MGG_CONTRACT_START_DT"=>$RS['MGG_CONTRACT_START_DT'],
					"MGG_CONTRACT_END_DT"=>$RS['MGG_CONTRACT_END_DT'],
					"MGG_RATIO"=>$RS['MGG_RATIO'],
					"MGG_BANK_NM_AES"=>$RS['MGG_BANK_NM_AES'],
					"MGG_BANK_NUM_AES"=>$RS['MGG_BANK_NUM_AES'],
					"MGG_BANK_OWN_NM_AES"=>$RS['MGG_BANK_OWN_NM_AES'],
					"NB_TOTAL_CNT"=>$RS['NB_TOTAL_CNT'],
					"NB_TOTAL_PRICE"=>$RS['NB_TOTAL_PRICE'],
					"NBI_TOTAL_DEPOSIT_CNT"=>$RS['NBI_TOTAL_DEPOSIT_CNT'],
					"NBI_TOTAL_DEPOSIT_BILL_PRICE"=>$RS['NBI_TOTAL_DEPOSIT_BILL_PRICE'],
					"NBI_TOTAL_DEPOSIT_PRICE"=>$RS['NBI_TOTAL_DEPOSIT_PRICE'],
					"DECREMENT_PRICE"=>$RS['DECREMENT_PRICE'],
					"UNPAID_DONE_CNT"=>$RS['UNPAID_DONE_CNT'],
					"UNPAID_DONE_BILL_PRICE"=>$RS['UNPAID_DONE_BILL_PRICE'],
					"UNPAID_CNT"=>$RS['UNPAID_CNT'],
					"UNPAID_PRICE"=>$RS['UNPAID_PRICE'],
					"DEPOSIT_CNT_RATIO"=>$RS['DEPOSIT_CNT_RATIO'],
					"DEPOSIT_PRICE_RATIO"=>$RS['DEPOSIT_PRICE_RATIO'],
					"DECREMENT_CNT_RATIO"=>$RS['DECREMENT_CNT_RATIO'],
					"DECREMENT_PRICE_RATIO"=>$RS['DECREMENT_PRICE_RATIO'],
					"UNPAID_DONE_CNT_RATIO"=>$RS['UNPAID_DONE_CNT_RATIO'],
					"UNPAID_DONE_PRICE_RATIO"=>$RS['UNPAID_DONE_PRICE_RATIO'],
					"UNPAID_CNT_RATIO"=>$RS['UNPAID_CNT_RATIO'],
					"UNPAID_PRICE_RATIO"=>$RS['UNPAID_PRICE_RATIO'],
					"PAID_DONE_CNT"=>$RS['PAID_DONE_CNT'],
					"PAID_DONE_BILL_PRICE"=>$RS['PAID_DONE_BILL_PRICE'],
					"PAID_DONE_DEPOSIT_PRICE"=>$RS['PAID_DONE_DEPOSIT_PRICE'],
					"PAID_DONE_CNT_RATIO"=>$RS['PAID_DONE_CNT_RATIO'],
					"PAID_DONE_PRICE_RATIO"=>$RS['PAID_DONE_PRICE_RATIO']
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
	
	/* 데이터생성*/
   function setData($ARR) {
	   
	   	$iSQL = "UPDATE `NT_BILL`.`NBB_BILL_INSU`
				   SET 
				   `RMT_ID` = '11510',
				   `NBI_REASON_DTL_ID` = '".addslashes($ARR['NBI_REASON_DTL_ID'])."',
				   `NBI_REASON_DT` = DATE_FORMAT(NOW(), '%Y-%m-%d'),
				   `NBI_REASON_USERID` = '".$_SESSION['IW_USERID']."', ";
	   
		if(isset($ARR['NBI_OTHER_MII_ID']) && !empty($ARR['NBI_OTHER_MII_ID'])){ //(추가)타보험사
			$iSQL .= "`NBI_OTHER_MII_ID` = '".addslashes($ARR['NBI_OTHER_MII_ID'])."',    ";
		}else{
			$iSQL .= "`NBI_OTHER_MII_ID` = NULL ,    ";
		}
		if(isset($ARR['NBI_OTHER_RATIO']) && !empty($ARR['NBI_OTHER_RATIO'])){ //(추가)과실율
			$iSQL .= "`NBI_OTHER_RATIO` = '".addslashes($ARR['NBI_OTHER_RATIO'])."',    ";
		}else{
			$iSQL .= "`NBI_OTHER_RATIO` = NULL,    ";
		}
		if(isset($ARR['NBI_OTHER_DT']) && !empty($ARR['NBI_OTHER_DT'])){ //(추가)입금일
			$iSQL .= "`NBI_OTHER_DT` = '".addslashes($ARR['NBI_OTHER_DT'])."',    ";
		}else{
			$iSQL .= "`NBI_OTHER_DT` = NULL,    ";
		}
		if(isset($ARR['NBI_OTHER_PRICE']) && !empty($ARR['NBI_OTHER_PRICE'])){ //(추가)입금액
			$iSQL .= "`NBI_OTHER_PRICE` = '".addslashes($ARR['NBI_OTHER_PRICE'])."',    ";
		}else{
			$iSQL .= "`NBI_OTHER_PRICE` = NULL,    ";
		}
		if(isset($ARR['NBI_OTHER_MEMO']) && !empty($ARR['NBI_OTHER_MEMO'])){ //(추가)메모
			$iSQL .= "`NBI_OTHER_MEMO` = '".addslashes($ARR['NBI_OTHER_MEMO'])."',    ";
		}else{
			$iSQL .= "`NBI_OTHER_MEMO` = NULL,    ";
		}
		$iSQL .= "	`LAST_DTHMS` = NOW(),
					`WRT_USERID` = '".$_SESSION['IW_USERID']."'
					WHERE `NB_ID` = '".addslashes($ARR['NB_ID'])."' AND `NBI_RMT_ID` = '".addslashes($ARR['NBI_RMT_ID'])."';
		";		
	   
	//    echo "setData SQL\n".$iSQL."\n";
	   return DBManager::execQuery($iSQL);
			   
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
	
	
	/* 지급제외 조회 */
	function getExclude() {
		$iSQL = "SELECT 
					RER_ID, 
					RER_NM, 
					RER_EXT1_YN, 
					RER_EXT2_YN, 
					RER_EXT3_YN, 
					RER_EXT4_YN, 
					RER_EXT5_YN
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
	$cDepositGw = new cDepositGw(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$cDepositGw->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
