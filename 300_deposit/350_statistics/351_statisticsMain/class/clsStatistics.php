<?php

class cStat extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	// Class Object Constructor
	function cStat($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
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
			$LIMIT_SQL = "LIMIT $LimitStart,$LimitEnd ;";
        }else {
			$LIMIT_SQL = " ;";
		}
        
		//보험사검색
		if($ARR['SEARCH_INSU_NM'] != ''){
            $tmpKeyword .= "AND NI.MII_ID IN (";
            foreach ($ARR['SEARCH_INSU_NM'] as $index => $MII_ARRAY) {
                $tmpKeyword .=" '".$MII_ARRAY."',";
            }
            $tmpKeyword=substr($tmpKeyword, 0, -1); // 마지막 콤마제거
            $tmpKeyword .= ") \n";
		}

		//보험사팀검색
		if($ARR['SEARCH_INSU_TEAM'] != ''){
            $tmpKeyword .= "AND `NT_MAIN`.`GET_INSU_CHG_TEAM_ID`(NIC.NIC_ID) IN (";
            foreach ($ARR['SEARCH_INSU_TEAM'] as $index => $MIT_ARRAY) {
                $tmpKeyword .=" '".$MIT_ARRAY."',";
            }
            $tmpKeyword=substr($tmpKeyword, 0, -1); // 마지막 콤마제거
            $tmpKeyword .= ") \n";
		}


		//보상담당
		if($ARR['INSU_FAO_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(`NT_MAIN`.`GET_INSU_CHG_NM`(NIC.NIC_ID, '".AES_SALT_KEY."') ,'".addslashes($ARR['INSU_FAO_SEARCH'])."')>0 ";	
		} 

		//통계구분 & 협조반응
		if($ARR['RPT_ID_SEARCH'] != '' || $ARR['RPR_ID_SEARCH'] != '') {
			if($ARR['RPT_ID_SEARCH'] != '' && $ARR['RPR_ID_SEARCH'] != ''){
				$joinNICC .= "AND ( NICCI.RPT_ID= '".addslashes($ARR['RPT_ID_SEARCH'])."' \n";
				$joinNICC .= "AND NICCI.RPR_ID= '".addslashes($ARR['RPR_ID_SEARCH'])."' ) \n";
				$tmpKeyword .= "AND NT_MAIN.GET_INSU_CHG_COOP(NIC.NIC_ID, '".addslashes($ARR['RPT_ID_SEARCH'])."','".addslashes($ARR['RPR_ID_SEARCH'])."') IS NOT NULL \n";
			}else if($ARR['RPT_ID_SEARCH'] != ''){
				$joinNICC .= "AND  NICCI.RPT_ID= '".addslashes($ARR['RPT_ID_SEARCH'])."' \n";
				$tmpKeyword .= "AND NT_MAIN.GET_INSU_CHG_COOP(NIC.NIC_ID, '".addslashes($ARR['RPT_ID_SEARCH'])."' , '') IS NOT NULL  \n";
			}else if($ARR['RPR_ID_SEARCH'] != ''){
				$joinNICC .= "AND  NICCI.RPR_ID= '".addslashes($ARR['RPR_ID_SEARCH'])."' \n";
				$tmpKeyword .= "AND NT_MAIN.GET_INSU_CHG_COOP(NIC.NIC_ID, '', '".addslashes($ARR['RPR_ID_SEARCH'])."') IS NOT NULL ";
			}

		}

		//협조반응
		// if($ARR['RPR_ID_SEARCH'] != '') {
		// 	$joinNICC .= "AND  NICCI.RPR_ID= '".addslashes($ARR['RPR_ID_SEARCH'])."' \n";
		// 	$tmpKeyword .= "AND NT_MAIN.GET_INSU_CHG_COOP_REACTION(NIC.NIC_ID, '".addslashes($ARR['RPR_ID_SEARCH'])."') = '".addslashes($ARR['RPR_ID_SEARCH'])."'  \n";
		// }


		//감액률
		if($ARR['RATIO_KEY_SEARCH'] != '') {
			if($ARR['RATIO_KEY_SEARCH'] == 'CNT'){ // 건수
                if($ARR['START_RATIO_SEARCH'] != '' && $ARR['END_RATIO_SEARCH'] != ''){ 
                    $outerWhere .= "AND DECREMENT_DC_RATIO BETWEEN '".addslashes($ARR['START_RATIO_SEARCH'])."' AND '".addslashes($ARR['END_RATIO_SEARCH'])."' \n";
                }else if($ARR['START_RATIO_SEARCH'] != '' ){
                    $outerWhere .= "AND DECREMENT_DC_RATIO >= '".$ARR['START_RATIO_SEARCH']."' \n";
                }else if($ARR['END_RATIO_SEARCH'] != ''){
                    $outerWhere .= "AND DECREMENT_DC_RATIO <= '".$ARR['END_RATIO_SEARCH']."' \n";
                }
            }else if($ARR['RATIO_KEY_SEARCH'] == 'TOTAL'){ // 전체
                if($ARR['START_RATIO_SEARCH'] != '' && $ARR['END_RATIO_SEARCH'] != ''){
                    $outerWhere .= "AND TOTAL_DECREMENT_RATIO BETWEEN '".addslashes($ARR['START_RATIO_SEARCH'])."' AND '".addslashes($ARR['END_RATIO_SEARCH'])."'";
                }else if($ARR['START_RATIO_SEARCH'] != '' ){
                    $outerWhere .= "AND TOTAL_DECREMENT_RATIO >= '".$ARR['START_RATIO_SEARCH']."' \n";
                }else if($ARR['END_RATIO_SEARCH'] != ''){
                    $outerWhere .= "AND TOTAL_DECREMENT_RATIO <= '".$ARR['END_RATIO_SEARCH']."' \n";
                }
            }else if($ARR['RATIO_KEY_SEARCH'] == 'UNPAID'){ // 미지급
                if($ARR['START_RATIO_SEARCH'] != '' && $ARR['END_RATIO_SEARCH'] != ''){
                    $outerWhere .= "AND UNPAID_CNT_RATIO BETWEEN '".addslashes($ARR['START_RATIO_SEARCH'])."' AND '".addslashes($ARR['END_RATIO_SEARCH'])."'";
                }else if($ARR['START_RATIO_SEARCH'] != '' ){
                    $outerWhere .= "AND UNPAID_CNT_RATIO >= '".$ARR['START_RATIO_SEARCH']."' \n";
                }else if($ARR['END_RATIO_SEARCH'] != ''){
                    $outerWhere .= "AND UNPAID_CNT_RATIO <= '".$ARR['END_RATIO_SEARCH']."' \n";
                }
			}
		}

		//협조요청일
		if($ARR['START_DT_SEARCH'] != '' && $ARR['END_DT_SEARCH'] != ''){ 
			$joinNICC .= "AND NICCI.NICC_DT BETWEEN '".addslashes($ARR['START_DT_SEARCH'])."' AND '".addslashes($ARR['END_DT_SEARCH'])."' \n";
			$tmpKeyword .= "AND NICC.NICC_DT BETWEEN '".addslashes($ARR['START_DT_SEARCH'])."' AND '".addslashes($ARR['END_DT_SEARCH'])."' \n";
		}else if($ARR['START_DT_SEARCH'] != '' ){
			$joinNICC .= "AND NICCI.NICC_DT >= '".$ARR['START_DT_SEARCH']."' \n";
			$tmpKeyword .= "AND NICC.NICC_DT >= '".$ARR['START_DT_SEARCH']."' \n";
		}else if($ARR['END_DT_SEARCH'] != ''){
			$joinNICC .= "AND NICCI.NICC_DT <= '".$ARR['END_DT_SEARCH']."' \n";
			$tmpKeyword .= "AND NICC.NICC_DT <= '".$ARR['END_DT_SEARCH']."' \n";
		}


		

		// echo "where >>>".$tmpKeyword;
		if($ARR['RATIO_KEY_SEARCH'] != ''){
			$mainSQL = $this->getListSql($joinNICC,$tmpKeyword);
			$mainSQL = "SELECT * FROM ($mainSQL) AS tmp WHERE 1=1 $outerWhere ";
		}else{
			$mainSQL = $this->getListSql($joinNICC,$tmpKeyword);

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
			$ORDERBY_SQL = " ORDER BY MII_ORDER_NUM, NIC_NM  ";   
		}else{
			$ORDERBY_SQL = " ORDER BY ".addslashes($ARR['ORDER_KEY'])." ".addslashes($ARR['IS_SORT'])." , NIC_NM "; 
		}
		
		$iSQL = $mainSQL.$ORDERBY_SQL.$LIMIT_SQL ;

		// echo $iSQL ;
		$Result= DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
                    "IDX"=>$IDX--,
					"MII_ID"=>$RS['MII_ID'],
					"MII_NM"=>$RS['MII_NM'],
					"MIT_ID"=>$RS['MIT_ID'],
					"MIT_NM"=>$RS['MIT_NM'],
					"NIC_ID"=>$RS['NIC_ID'],
					"NIC_NM"=>$RS['NIC_NM'],
					"NIC_PHONE"=>$RS['NIC_PHONE'],
					"NB_TOTAL_CNT"=>$RS['NB_TOTAL_CNT'],
					"NB_TOTAL_PRICE"=>$RS['NB_TOTAL_PRICE'],
					"NBI_TOTAL_DEPOSIT_CNT"=>$RS['NBI_TOTAL_DEPOSIT_CNT'],
					"NBI_TOTAL_DEPOSIT_BILL_PRICE"=>$RS['NBI_TOTAL_DEPOSIT_BILL_PRICE'],
					"NBI_TOTAL_DEPOSIT_PRICE"=>$RS['NBI_TOTAL_DEPOSIT_PRICE'],
					"DECREMENT_PRICE"=>$RS['DECREMENT_PRICE'],
					"DECREMENT_CNT"=>$RS['DECREMENT_CNT'],
					"DECREMENT_BILL_PRICE"=>$RS['DECREMENT_BILL_PRICE'],
					"DECREMENT_DEPOSIT_PRICE"=>$RS['DECREMENT_DEPOSIT_PRICE'],
					"DECREMENT_DECREMENT_PRICE"=>$RS['DECREMENT_DECREMENT_PRICE'],
					"DECREMENT_DEPOSIT_DT"=>$RS['DECREMENT_DEPOSIT_DT'],
					"DECREMENT_ADD_DEPOSIT_PRICE"=>$RS['DECREMENT_ADD_DEPOSIT_PRICE'],
					"DECREMENT_DC_RATIO"=>$RS['DECREMENT_DC_RATIO'],
					"UNPAID_DONE_CNT"=>$RS['UNPAID_DONE_CNT'],
					"UNPAID_DONE_PRICE"=>$RS['UNPAID_DONE_PRICE'],
					"UNPAID_CNT"=>$RS['UNPAID_CNT'],
					"UNPAID_PRICE"=>$RS['UNPAID_PRICE'],
					"DEPOSIT_CNT_RATIO"=>$RS['DEPOSIT_CNT_RATIO'],
					"DEPOSIT_PRICE_RATIO"=>$RS['DEPOSIT_PRICE_RATIO'],
					"TOTAL_DECREMENT_RATIO"=>$RS['TOTAL_DECREMENT_RATIO'],
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
					"PAID_DONE_PRICE_RATIO"=>$RS['PAID_DONE_PRICE_RATIO'],
					"COOP_INFO"=>$RS['COOP_INFO']
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
	function getListSql($joinNICC,$tmpKeyword){	
			$NB_RATIO_PRICE = "TRUNCATE(IFNULL(NBI.`NB_TOTAL_PRICE`,'0')*IF(NBI.`NBI_RATIO`<>'',CAST(NBI.`NBI_RATIO` AS DECIMAL)/100,1),-1)" ; 
			$DECREMENT_DEPOSIT = "IFNULL(`NBI_INSU_DEPOSIT_PRICE`,0)+IFNULL(`NBI_INSU_ADD_DEPOSIT_PRICE`,0)+IFNULL(`NBI_OTHER_PRICE`,0)";

		$iSQL = "SELECT 
					NIC.MII_ID,
					NT_MAIN.GET_INSU_NM(NIC.MII_ID) AS 'MII_NM',
					NI.MII_ORDER_NUM,
					NT_MAIN.GET_INSU_CHG_TEAM_ID(NIC.NIC_ID) AS 'MIT_ID',
					NT_MAIN.GET_INSU_CHG_TEAM_NM(NIC.NIC_ID) AS 'MIT_NM',
					NT_MAIN.GET_INSU_CHG_PHONE(NIC.NIC_ID,'".AES_SALT_KEY."') AS 'NIC_PHONE',
					NIC.NIC_ID,
					NT_MAIN.GET_INSU_CHG_NM(NIC.NIC_ID,'".AES_SALT_KEY."') AS 'NIC_NM',
					FORMAT(SUM(IF(NBI.NB_ID IS NOT NULL,1,0)),0) AS 'NB_TOTAL_CNT', #총청구건수
					FORMAT(SUM($NB_RATIO_PRICE),0) AS 'NB_TOTAL_PRICE', #총청구금액
					FORMAT(SUM(IF(`RDET_ID` <> '40121', 1, 0)),0) AS 'NBI_TOTAL_DEPOSIT_CNT', #지급건수
					FORMAT(SUM(IF(`RDET_ID` <> '40121', $NB_RATIO_PRICE, 0)),0) AS 'NBI_TOTAL_DEPOSIT_BILL_PRICE', #지급건 청구액
					FORMAT(SUM(IFNULL(NBI.NBI_INSU_DEPOSIT_PRICE,0)),0) AS 'NBI_TOTAL_DEPOSIT_PRICE', #지급금액				
					FORMAT(    
						SUM(IF(`RDET_ID` <> '40121', $NB_RATIO_PRICE, 0))
						-
						SUM(IFNULL(NBI.NBI_INSU_DEPOSIT_PRICE,0))
					,0) AS 'DECREMENT_PRICE', #감액금액
					FORMAT(SUM(IF(NBI.`RDET_ID` <> '40121', IF($NB_RATIO_PRICE > $DECREMENT_DEPOSIT,1,0),0)),0) AS 'DECREMENT_CNT', #감액건 지급건수
					FORMAT(SUM(IF(NBI.`RDET_ID` <> '40121', IF($NB_RATIO_PRICE > $DECREMENT_DEPOSIT,$NB_RATIO_PRICE,0),0)),0) AS 'DECREMENT_BILL_PRICE', #감액건 청구금액
					FORMAT(SUM(IF(NBI.`RDET_ID` <> '40121', IF($NB_RATIO_PRICE > $DECREMENT_DEPOSIT,$DECREMENT_DEPOSIT,0),0)),0) AS 'DECREMENT_DEPOSIT_PRICE', #감액건 지급금액
					FORMAT(    
						SUM(IF(NBI.`RDET_ID` <> '40121', IF($NB_RATIO_PRICE > $DECREMENT_DEPOSIT,$NB_RATIO_PRICE - $DECREMENT_DEPOSIT,0),0))
					,0) AS 'DECREMENT_DECREMENT_PRICE', #감액건 감액금액
					IF(NBI.`RDET_ID` <> '40121', IF(NBI.`RDT_ID` <> '40111',MAX(`NBI_INSU_DEPOSIT_DT`),'-'),'-') AS 'DECREMENT_DEPOSIT_DT', #감액건 감액입금일
					FORMAT(SUM(IF(NBI.`RDET_ID` <> '40121', IF($NB_RATIO_PRICE > $DECREMENT_DEPOSIT,IFNULL(NBI.`NBI_INSU_ADD_DEPOSIT_PRICE`,0),0),0)),0) AS 'DECREMENT_ADD_DEPOSIT_PRICE', #감액건 추가입금액
					IFNULL(ROUND( 100-
						(
						  (
							SUM(IF(NBI.`RDET_ID` <> '40121', IF($NB_RATIO_PRICE > $DECREMENT_DEPOSIT,IFNULL(NBI.`NBI_INSU_DEPOSIT_PRICE`,0),0),0))
							+
							SUM(IF(NBI.`RDET_ID` <> '40121', IF($NB_RATIO_PRICE > $DECREMENT_DEPOSIT,IFNULL(NBI.`NBI_INSU_ADD_DEPOSIT_PRICE`,0),0),0))
							+
							SUM(IF(NBI.`RDET_ID` <> '40121', IF($NB_RATIO_PRICE > $DECREMENT_DEPOSIT,IFNULL(NBI.`NBI_OTHER_PRICE`,0),0),0))
						  )
						  /
						  IFNULL(SUM($NB_RATIO_PRICE),0)
						  *
						  100
						)
					,2),0) AS 'DECREMENT_DC_RATIO', #감액건 감액률
				
					FORMAT(SUM(IF(NBI.`RDET_ID` = '40121',IF(NBI.`NBI_INSU_DONE_YN` = 'Y',1,0),0)),0) AS 'UNPAID_DONE_CNT', #미입급 종결 건수
					FORMAT(SUM(IF(NBI.`RDET_ID` = '40121',IF(NBI.`NBI_INSU_DONE_YN` = 'Y',$NB_RATIO_PRICE,0),0)),0) AS 'UNPAID_DONE_PRICE', #미입급 종결금액
				
					FORMAT(SUM(IF(NBI.NB_ID IS NOT NULL,1,0)) - SUM(IF(`RDET_ID` <> '40121', 1, 0)) - SUM(IF(NBI.`RDET_ID` = '40121',IF(NBI.`NBI_INSU_DONE_YN` = 'Y',1,0),0)) ,0) AS 'UNPAID_CNT', #미지급건수 
					FORMAT(SUM($NB_RATIO_PRICE)  - SUM(IF(`RDET_ID` <> '40121', $NB_RATIO_PRICE, 0))  - SUM(IF(NBI.`RDET_ID` = '40121',IF(NBI.`NBI_INSU_DONE_YN` = 'Y',$NB_RATIO_PRICE,0),0)) ,0) AS 'UNPAID_PRICE',#미지급금액
					
					IFNULL(ROUND(SUM(IF(`RDET_ID` <> '40121', 1, 0)) / SUM(IF(NBI.NB_ID IS NOT NULL,1,0)) * 100, 2),0) AS 'DEPOSIT_CNT_RATIO', #지급률 건수
					IFNULL(ROUND(SUM(IFNULL(NBI.NBI_INSU_DEPOSIT_PRICE,0)) / SUM($NB_RATIO_PRICE) * 100, 2),0) AS 'DEPOSIT_PRICE_RATIO', #지급률 금액
					IFNULL(ROUND(
							(    
								SUM(IF(`RDET_ID` <> '40121', $NB_RATIO_PRICE, 0))
								-
								SUM(IFNULL(NBI.NBI_INSU_DEPOSIT_PRICE,0))
							)
							/
							SUM(IF(`RDET_ID` <> '40121', $NB_RATIO_PRICE, 0))
							*100
						,2)   
					,0) AS 'TOTAL_DECREMENT_RATIO',#전체감액률
					-- IFNULL(ROUND(SUM(IF(NBI.`RDT_ID` <> '40111',1,0)) / SUM(IF(NBI.NB_ID IS NOT NULL,1,0)) * 100, 2),0) AS 'DECREMENT_CNT_RATIO', #감액률 건수
					-- IFNULL(ROUND(SUM(IF(NBI.`RDT_ID` <> '40111',NBI.NBI_INSU_DEPOSIT_PRICE,0)) / SUM($NB_RATIO_PRICE) * 100, 2),0) AS 'DECREMENT_PRICE_RATIO', #감액률 금액
					IFNULL(
							ROUND(
									SUM(
								IF(NBI.`RDET_ID` <> '40121' AND NBI.`RDT_ID` <> '40111',1,0)
							) /SUM(IF(NBI.`RDET_ID` <> '40121',1,0 ))*100
							, 2)
					,0) AS 'DECREMENT_CNT_RATIO', #감액률 건수
					IFNULL(
							ROUND(
									SUM(
								IF(NBI.`RDET_ID` <> '40121' AND NBI.`RDT_ID` <> '40111',($NB_RATIO_PRICE - NBI.NBI_INSU_DEPOSIT_PRICE),0)
							) /SUM(IF(NBI.`RDET_ID` <> '40121', $NB_RATIO_PRICE, 0))*100
							, 2)
					,0) AS 'DECREMENT_PRICE_RATIO', #감액률 금액
					IFNULL(ROUND(SUM(IF(NBI.`RDET_ID` = '40121',IF(NBI.`NBI_INSU_DONE_YN` = 'Y',1,0),0)) / SUM(IF(NBI.NB_ID IS NOT NULL,1,0)) * 100, 2),0) AS 'UNPAID_DONE_CNT_RATIO', #미입금종결률 건수
					IFNULL(ROUND(SUM(IF(NBI.`RDET_ID` = '40121',IF(NBI.`NBI_INSU_DONE_YN` = 'Y',$NB_RATIO_PRICE,0),0)) / SUM($NB_RATIO_PRICE) * 100, 2),0) AS 'UNPAID_DONE_PRICE_RATIO', #미입금종결률 금액
					IFNULL(ROUND((SUM(IF(NBI.NB_ID IS NOT NULL,1,0)) - SUM(IF(`RDET_ID` <> '40121', 1, 0)) - SUM(IF(NBI.`RDET_ID` = '40121',IF(NBI.`NBI_INSU_DONE_YN` = 'Y',1,0),0)))/SUM(IF(NBI.NB_ID IS NOT NULL,1,0)) * 100, 2),0) AS 'UNPAID_CNT_RATIO', #미지급률 건수
					IFNULL(ROUND((SUM($NB_RATIO_PRICE)  - SUM(IF(`RDET_ID` <> '40121', $NB_RATIO_PRICE, 0))  - SUM(IF(NBI.`RDET_ID` = '40121',IF(NBI.`NBI_INSU_DONE_YN` = 'Y',$NB_RATIO_PRICE,0),0)))/ SUM($NB_RATIO_PRICE) * 100, 2),0) AS 'UNPAID_PRICE_RATIO', #미지급률 금액
					
					FORMAT(SUM(IF(`RDET_ID` <> '40121', IF(NBI.`NBI_INSU_DONE_YN` = 'Y',1,0), 0)),0) AS 'PAID_DONE_CNT', #지급종결 건수
					FORMAT(SUM(IF(`RDET_ID` <> '40121', IF(NBI.`NBI_INSU_DONE_YN` = 'Y',$NB_RATIO_PRICE,0), 0)),0) AS 'PAID_DONE_BILL_PRICE', #지급종결 청구금액
					FORMAT(SUM(IF(`RDET_ID` <> '40121', IF(NBI.`NBI_INSU_DONE_YN` = 'Y',NBI.NBI_INSU_DEPOSIT_PRICE,0), 0)),0) AS 'PAID_DONE_DEPOSIT_PRICE', #지급종결 입금액
					
					IFNULL(ROUND((SUM(IF(`RDET_ID` <> '40121', IF(NBI.`NBI_INSU_DONE_YN` = 'Y',1,0), 0))) / SUM(IF(`RDET_ID` <> '40121', 1, 0)) *100, 2),0) AS 'PAID_DONE_CNT_RATIO', #지급종결률 건수
					IFNULL(ROUND(SUM(IF(`RDET_ID` <> '40121', IF(NBI.`NBI_INSU_DONE_YN` = 'Y',NBI.NBI_INSU_DEPOSIT_PRICE,0), 0))/ SUM(IFNULL(NBI.NBI_INSU_DEPOSIT_PRICE,0))  *100, 2),0) AS 'PAID_DONE_PRICE_RATIO', #지급종결률 금액
					(SELECT CONCAT(
								`NT_CODE`.`GET_CODE_NM`(`RPT_ID`),'|',
								`NICC_DT`,'|',
								`NT_CODE`.`GET_CODE_NM`(`RPR_ID`)				
							)
					FROM	`NT_MAIN`.`NMI_INSU_CHARGE_COOP` NICCI
					WHERE	NICCI.`NIC_ID` = NIC.`NIC_ID`
					$joinNICC 
					ORDER BY NICCI.`NICC_ID` DESC
					LIMIT 1) AS `COOP_INFO`
					
				FROM  
					`NT_MAIN`.`NMI_INSU_CHARGE` NIC
				JOIN `NT_MAIN`.`NMI_INSU` NI
				ON NIC.MII_ID = NI.MII_ID
				LEFT OUTER JOIN `NT_BILL`.`V_BILL_INFO` NBI
				ON NI.MII_ID = NBI.MII_ID AND NIC.NIC_ID = NBI.NIC_ID
				
				LEFT OUTER JOIN `NT_MAIN`.`NMI_INSU_CHARGE_COOP` NICC
                ON NICC.NIC_ID = NIC.NIC_ID 
				WHERE NIC.DEL_YN = 'N' 
				AND NI.DEL_YN = 'N'
				$tmpKeyword 

				GROUP BY  NIC.NIC_ID 
				

		";
		return $iSQL;
	}


	/* 보험사Team 조회  */
	function getInsuTeam() {
		$iSQL = "SELECT 
					MIT_ID, MIT_NM
				FROM NT_MAIN.NMI_INSU_TEAM
				WHERE DEL_YN ='N';
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
	

	/* 협조요청 리스트*/
	function getHelpList($ARR) {

		$LimitStart = ($ARR['CURR_PAGE']-1) * $ARR['PER_PAGE'];
		$LimitEnd   = $ARR['PER_PAGE'];
		/** 전체리스트가 아닌경우에만 limitSql 적용 */
		$LIMIT_SQL = "";
		if($ARR['REQ_MODE'] == CASE_LIST_SUB){
			$LIMIT_SQL = "LIMIT $LimitStart,$LimitEnd";
		}

		
		// 리스트 가져오기
		$iSQL = "SELECT 
					NICC_ID,
					RPT_ID,
					NT_CODE.GET_CODE_NM(RPT_ID) AS 'RPT_NM',
					RPR_ID,
					NT_CODE.GET_CODE_NM(RPR_ID) AS 'RPR_NM',
					NICC_DT,
					NICC_USERID,
					NT_AUTH.GET_USER_NM(NICC_USERID) AS 'NICC_USERNM',
					NICC_MEMO
				FROM NT_MAIN.NMI_INSU_CHARGE_COOP
				WHERE 
					`NIC_ID`='".addslashes($ARR['NIC_ID'])."'
				ORDER BY NICC_DT DESC 
				$LIMIT_SQL ;
		";
	
		// echo $iSQL."\n";
		$Result= DBManager::getResult($iSQL);
		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
				"NICC_ID"=>$RS['NICC_ID'],
				"RPT_NM"=>$RS['RPT_NM'],
				"RPR_NM"=>$RS['RPR_NM'],
				"NICC_DT"=>$RS['NICC_DT'],
				"NICC_USERID"=>$RS['NICC_USERID'],
				"NICC_USERNM"=>$RS['NICC_USERNM'],
				"NICC_MEMO"=>$RS['NICC_MEMO']
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
	   
	   	$iSQL = "INSERT INTO `NT_MAIN`.`NMI_INSU_CHARGE_COOP`
					(
					`NIC_ID`,
					`MII_ID`,
					`RPT_ID`,
					`RPR_ID`,
					`NICC_DT`,
					`NICC_USERID`,
					`NICC_MEMO`,
					`WRT_DTHMS`,
					`LAST_DTHMS`,
					`WRT_USERID`)
					VALUES
					(
					'".addslashes($ARR['NIC_ID'])."',
					'".addslashes($ARR['MII_ID'])."',
					'".addslashes($ARR['RPT_ID'])."',
					'".addslashes($ARR['RPR_ID'])."',
					'".addslashes($ARR['NICC_DT'])."',
					'".addslashes($ARR['NICC_USERID'])."',
					'".addslashes($ARR['NICC_MEMO'])."',
					NOW(),
					NOW(),
					'".$_SESSION['USERID']."');
		";		
				
	//    echo "setData SQL\n".$iSQL."\n";
	   return DBManager::execQuery($iSQL);
			   
   }

   /**
	 * data 삭제
	 */
	function delData($ARR) {
		$iSQL = "DELETE FROM  `NT_MAIN`.`NMI_INSU_CHARGE_COOP`
					WHERE `NICC_ID`='".addslashes($ARR['NICC_ID'])."';
		";
		
		// echo 	$iSQL ;
		return DBManager::execQuery($iSQL);
	}



}
	// 0. 객체 생성
	$cStat = new cStat(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$cStat->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
