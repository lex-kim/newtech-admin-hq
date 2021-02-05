<?php

class cDepositIw extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	// Class Object Constructor
	function cDepositIw($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
		$this->DATA_SAVE_PATH = $DATA_SAVE_PATH;
		$this->ALLOW_FILE_EXT = $ALLOW_FILE_EXT;
	}


	/**
	 * 조회 
	 * @param {$ARR}
	 * @param {CURR_PAGE : 현재페이지, PER_PAGE : 몇개를 조회할 것인지, KEYWORD : 검색조건}
	 */
	function getList($ARR) {
        $tmpKeyword = "";
        $LIMIT_SQL = "";

		$LimitStart = ($ARR['CURR_PAGE']-1) * $ARR['PER_PAGE'];
        $LimitEnd   = $ARR['PER_PAGE'];

		if($ARR['REQ_MODE'] == CASE_LIST){
			$LIMIT_SQL = "LIMIT $LimitStart,$LimitEnd";
        }
        
        
		if($ARR['DT_KEY_SEARCH'] != '') {
			if($ARR['DT_KEY_SEARCH'] == 'NB_CLAIM_DT'){ // 청구일
                if($ARR['START_DT_SEARCH'] != '' && $ARR['END_DT_SEARCH'] != ''){ 
                    $tmpKeyword .= "AND NBI.NB_CLAIM_DT BETWEEN '".addslashes($ARR['START_DT_SEARCH'])."' AND '".addslashes($ARR['END_DT_SEARCH'])."' \n";
                }else if($ARR['START_DT_SEARCH'] != '' ){
                    $tmpKeyword .= "AND NBI.NB_CLAIM_DT >= '".$ARR['START_DT_SEARCH']."' \n";
                }else if($ARR['END_DT_SEARCH'] != ''){
                    $tmpKeyword .= "AND NBI.NB_CLAIM_DT <= '".$ARR['END_DT_SEARCH']."' \n";
                }
            }else if($ARR['DT_KEY_SEARCH'] == 'NBI_INSU_DEPOSIT_DT'){ // 입금일
                if($ARR['START_DT_SEARCH'] != '' && $ARR['END_DT_SEARCH'] != ''){
                    $tmpKeyword .= "AND DATE_FORMAT(NBI.NBI_INSU_DEPOSIT_DT, '%Y-%m-%d') BETWEEN '".addslashes($ARR['START_DT_SEARCH'])."' AND '".addslashes($ARR['END_DT_SEARCH'])."'";
                }else if($ARR['START_DT_SEARCH'] != '' ){
                    $tmpKeyword .= "AND DATE_FORMAT(NBI.NBI_INSU_DEPOSIT_DT, '%Y-%m-%d') >= '".$ARR['START_DT_SEARCH']."' \n";
                }else if($ARR['END_DT_SEARCH'] != ''){
                    $tmpKeyword .= "AND DATE_FORMAT(NBI.NBI_INSU_DEPOSIT_DT, '%Y-%m-%d') <= '".$ARR['END_DT_SEARCH']."' \n";
                }
            }
		}

		//쌍방여부
		if($ARR['BOTH_YN'] == YN_Y){
            $tmpKeyword .= "AND NBI.RLT_ID =  '11430' \n";
		}else if($ARR['BOTH_YN'] == YN_N){
			$tmpKeyword .= "AND NBI.RLT_ID <>  '11430' \n";
		}



		// 페이징처리를 위해 전체데이터
		$iSQL = "SELECT COUNT(*) AS 'CNT'
				FROM (SELECT  
						NI.MII_ID,
						NI.MII_NM
						FROM  
							`NT_MAIN`.`NMI_INSU` NI
						LEFT OUTER JOIN `NT_BILL`.`V_BILL_INFO` NBI
						ON NI.MII_ID = NBI.MII_ID
						$tmpKeyword 
						WHERE NI.DEL_YN = 'N'
						GROUP BY  NI.MII_ID, NBI.MII_ID
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

		$NB_RATIO_PRICE = "TRUNCATE(IFNULL(NBI.`NB_TOTAL_PRICE`,'0')*IF(NBI.`NBI_RATIO`<>'',CAST(NBI.`NBI_RATIO` AS DECIMAL)/100,1),-1)" ; 
		$DECREMENT_DEPOSIT = "IFNULL(`NBI_INSU_DEPOSIT_PRICE`,0)+IFNULL(`NBI_INSU_ADD_DEPOSIT_PRICE`,0)+IFNULL(`NBI_OTHER_PRICE`,0)";

		// 리스트 가져오기
		$iSQL = "SELECT 
					NI.MII_ID,
					NI.MII_NM,
					NI.RIT_ID,
					NT_CODE.GET_CODE_NM(NI.RIT_ID) AS 'RIT_NM',
					IFNULL(SUM(IF(NBI.NB_ID IS NOT NULL,1,0)),0) AS 'NB_TOTAL_CNT', #총청구건수
					IFNULL(SUM($NB_RATIO_PRICE),0) AS 'NB_TOTAL_PRICE', #총청구금액
					IFNULL(SUM(IF(`RDET_ID` <> '40121', 1, 0)),0) AS 'NBI_TOTAL_DEPOSIT_CNT', #입금건수
					IFNULL(SUM(IF(`RDET_ID` <> '40121', $NB_RATIO_PRICE, 0)),0) AS 'NBI_TOTAL_DEPOSIT_BILL_PRICE', #입금청구액
					IFNULL(SUM(IF(`RDET_ID` <> '40121', 
						(
						IFNULL(NBI.`NBI_INSU_DEPOSIT_PRICE`, 0) + IFNULL(NBI.`NBI_INSU_ADD_DEPOSIT_PRICE`, 0) + IFNULL(NBI.`NBI_CAROWN_DEPOSIT_PRICE`, 0) + IFNULL(NBI.`NBI_OTHER_PRICE`, 0)
						), 0)),0) AS 'NBI_TOTAL_DEPOSIT_PRICE',
					IFNULL(  
						IFNULL(SUM(IF(NBI.`RDET_ID` <> '40121', $NB_RATIO_PRICE , 0)),0)
                         -
                         ( IFNULL(SUM(IF(NBI.`RDET_ID` <> '40121',NBI.NBI_INSU_DEPOSIT_PRICE,0)),0) + IFNULL(SUM( IF(NBI.`RDET_ID` <> '40121',NBI.NBI_INSU_ADD_DEPOSIT_PRICE,0)),0))
					,0) AS 'DECREMENT_PRICE', #감액금액
					IFNULL(SUM(IF(NBI.`RDET_ID` = '40121',IF(NBI.`NBI_INSU_DONE_YN` = 'Y',1,0),0)),0) AS 'UNPAID_DONE_CNT', #미입금 종결 건수
					IFNULL(SUM(IF(NBI.`RDET_ID` = '40121',IF(NBI.`NBI_INSU_DONE_YN` = 'Y',$NB_RATIO_PRICE,0),0)),0) AS 'UNPAID_DONE_BILL_PRICE', #미입금 종결 청구금액
					IFNULL(SUM(IF(`RDET_ID` = '40121', 1, 0)) ,0) AS 'UNPAID_CNT', #미입금건수 
					IFNULL(SUM(IF(`RDET_ID` = '40121', $NB_RATIO_PRICE, 0)) ,0) AS 'UNPAID_PRICE',#미입금금액
					
					IFNULL(ROUND(SUM(IF(`RDET_ID` <> '40121', 1, 0)) / SUM(IF(NBI.NB_ID IS NOT NULL,1,0)) * 100, 2),0) AS 'DEPOSIT_CNT_RATIO', #입금률 건수
					IFNULL(ROUND(SUM(NBI.NBI_INSU_DEPOSIT_PRICE) / SUM($NB_RATIO_PRICE) * 100, 2),0) AS 'DEPOSIT_PRICE_RATIO', #입금률 금액
					IFNULL(ROUND(SUM(IF(NBI.`RDET_ID` <> '40121' AND $NB_RATIO_PRICE > $DECREMENT_DEPOSIT,1,0)) / SUM(IF(NBI.`RDET_ID` <> '40121',1,0)) * 100, 2),0) AS 'DECREMENT_CNT_RATIO', #감액률 건수
					IFNULL(
                        ROUND(
                            (
                                IFNULL(SUM(IF(NBI.`RDET_ID` <> '40121', $NB_RATIO_PRICE, 0)),0)
                                -
                                SUM(IF(NBI.`RDET_ID` <> '40121',$DECREMENT_DEPOSIT,0)) 
                            )
                            / 
                            SUM(IF(NBI.`RDET_ID` <> '40121', $NB_RATIO_PRICE, 0))
                            * 100
                        , 2)
                    ,0) AS 'DECREMENT_PRICE_RATIO', #감액률 금액
					IFNULL(ROUND(SUM(IF(NBI.`RDET_ID` = '40121',IF(NBI.`NBI_INSU_DONE_YN` = 'Y',1,0),0)) / SUM(IF(NBI.NB_ID IS NOT NULL,1,0)) * 100, 2),0) AS 'UNPAID_DONE_CNT_RATIO', #미입금종결률 건수
					IFNULL(ROUND(SUM(IF(NBI.`RDET_ID` = '40121',IF(NBI.`NBI_INSU_DONE_YN` = 'Y',$NB_RATIO_PRICE,0),0)) / SUM($NB_RATIO_PRICE) * 100, 2),0) AS 'UNPAID_DONE_PRICE_RATIO', #미입금종결률 금액
					IFNULL(ROUND((SUM(IF(NBI.NB_ID IS NOT NULL,1,0)) - SUM(IF(`RDET_ID` <> '40121', 1, 0)) - SUM(IF(NBI.`RDET_ID` = '40121',IF(NBI.`NBI_INSU_DONE_YN` = 'Y',1,0),0)))/SUM(IF(NBI.NB_ID IS NOT NULL,1,0)) * 100, 2),0) AS 'UNPAID_CNT_RATIO', #미입금률 건수
					IFNULL(ROUND((SUM($NB_RATIO_PRICE)  - SUM(IF(`RDET_ID` <> '40121', $NB_RATIO_PRICE, 0))  - SUM(IF(NBI.`RDET_ID` = '40121',IF(NBI.`NBI_INSU_DONE_YN` = 'Y',$NB_RATIO_PRICE,0),0)))/ SUM($NB_RATIO_PRICE) * 100, 2),0) AS 'UNPAID_PRICE_RATIO', #미입금률 금액
					
					IFNULL(SUM(IF(`RDET_ID` <> '40121', IF(NBI.`NBI_INSU_DONE_YN` = 'Y',1,0), 0)),0) AS 'PAID_DONE_CNT', #입금종결 건수
					IFNULL(SUM(IF(`RDET_ID` <> '40121', IF(NBI.`NBI_INSU_DONE_YN` = 'Y',$NB_RATIO_PRICE,0), 0)),0) AS 'PAID_DONE_BILL_PRICE', #입금종결 청구금액
					IFNULL(SUM(IF(`RDET_ID` <> '40121', IF(NBI.`NBI_INSU_DONE_YN` = 'Y',NBI.NBI_INSU_DEPOSIT_PRICE,0), 0)),0) AS 'PAID_DONE_DEPOSIT_PRICE', #입금종결 입금액
					
					IFNULL(ROUND((SUM(IF(`RDET_ID` <> '40121', IF(NBI.`NBI_INSU_DONE_YN` = 'Y',1,0), 0))) / SUM(IF(`RDET_ID` <> '40121', 1, 0)) *100, 2),0) AS 'PAID_DONE_CNT_RATIO', #입금종결률 건수
					IFNULL(ROUND(SUM(IF(`RDET_ID` <> '40121', IF(NBI.`NBI_INSU_DONE_YN` = 'Y',NBI.NBI_INSU_DEPOSIT_PRICE,0), 0))/ SUM(NBI.NBI_INSU_DEPOSIT_PRICE)  *100, 2),0) AS 'PAID_DONE_PRICE_RATIO' #입금종결률 금액
				
					
				FROM  
					`NT_MAIN`.`NMI_INSU` NI
				LEFT OUTER JOIN `NT_BILL`.`V_BILL_INFO` NBI
				ON NI.MII_ID = NBI.MII_ID
				$tmpKeyword 
				WHERE NI.DEL_YN = 'N'
				
				GROUP BY  NI.MII_ID, NBI.MII_ID

				ORDER BY NI.MII_ORDER_NUM
                $LIMIT_SQL ;
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
					"RIT_ID"=>$RS['RIT_ID'],
					"RIT_NM"=>$RS['RIT_NM'],
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
					YEAR(MIN(NB_CLAIM_DT))AS MIN_YEAR
				FROM NT_BILL.NBB_BILL
				WHERE DEL_YN = 'N';
		";

		return DBManager::getRecordSet($iSQL);	
	}




}
	// 0. 객체 생성
	$cDepositIw = new cDepositIw(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$cDepositIw->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
