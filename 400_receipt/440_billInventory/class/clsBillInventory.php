<?php

class cBillInventory extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	// Class Object Constructor
	function cBillInventory($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
		$this->DATA_SAVE_PATH = $DATA_SAVE_PATH;
		$this->ALLOW_FILE_EXT = $ALLOW_FILE_EXT;
    }
    
    /* 재고설정 데이터 조회 */
    function getStock($ARR){
        $iSQL = "SELECT 
                    NIE.`NIE_BILL_VS_STOCK_RATIO`,
                    NIE.`NIE_INDEPENDENT_STOCK_RATIO`
                FROM `NT_STOCK`.`NSP_IO_ENV` NIE ;
        ";
        $RS	= DBManager::getRecordSet($iSQL);
		
		$BILL	= $RS['NIE_BILL_VS_STOCK_RATIO'];
        $INDEPENDENT	= $RS['NIE_INDEPENDENT_STOCK_RATIO'];
        unset($RS);

        $iSQL = "SELECT 
                NIS.`NIS_FROM_CPPS_ID`,
                `NT_CODE`.`GET_CPPS_NM`(NIS.`NIS_FROM_CPPS_ID`) AS NIS_FROM_CPPS_NM,
                NIS.`NIS_TO_CPPS_ID`,
                `NT_CODE`.`GET_CPPS_NM`(NIS.`NIS_TO_CPPS_ID`) AS NIS_TO_CPPS_NM,
                NIS.`NIS_RATIO`
            FROM `NT_STOCK`.`NSP_IO_SUBSTITUTION` NIS;
        ";
        $Result	= DBManager::getResult($iSQL);

        $rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"NIS_FROM_CPPS_ID"=>$RS['NIS_FROM_CPPS_ID'],
					"NIS_TO_CPPS_ID"=>$RS['NIS_TO_CPPS_ID'],
					"NIS_FROM_CPPS_NM"=>$RS['NIS_FROM_CPPS_NM'],
					"NIS_TO_CPPS_NM"=>$RS['NIS_TO_CPPS_NM'],
					"NIS_RATIO"=>$RS['NIS_RATIO']
				));
        }
        $Result->close();		
		unset($RS);
        
        $jsonArray = array();
        array_push($jsonArray,$BILL);
        array_push($jsonArray,$INDEPENDENT);
        array_push($jsonArray,$rtnArray);
        return  $jsonArray;

       
    }

    /* 재고정산 환경설정 저장 */
    function setStock($ARR) {
        if($ARR['NIE_BILL_VS_STOCK_RATIO'] != ''){
            $iSQL = "SET SQL_SAFE_UPDATES =0 ;
                    UPDATE `NT_STOCK`.`NSP_IO_ENV`
                    SET
                    `NIE_BILL_VS_STOCK_RATIO` = ".$ARR['NIE_BILL_VS_STOCK_RATIO'].",
                    `WRT_DTHMS` = NOW(),
                    `LAST_DTHMS` =NOW(),
                    `WRT_USERID` = '".$_SESSION['USERID']."' ;
                    
                    SET SQL_SAFE_UPDATES =1 ;
            ";
        }else if($ARR['NIE_INDEPENDENT_STOCK_RATIO'] != ''){
            $iSQL = "SET SQL_SAFE_UPDATES =0 ;
                    UPDATE `NT_STOCK`.`NSP_IO_ENV`
                    SET
                    `NIE_INDEPENDENT_STOCK_RATIO` = ".$ARR['NIE_INDEPENDENT_STOCK_RATIO'].",
                    `WRT_DTHMS` = NOW(),
                    `LAST_DTHMS` =NOW(),
                    `WRT_USERID` = '".$_SESSION['USERID']."' ;
                     
                    SET SQL_SAFE_UPDATES =1 ;
            ";

        }else {
            $iSQL = "INSERT INTO `NT_STOCK`.`NSP_IO_SUBSTITUTION`
                    (`NIS_FROM_CPPS_ID`,
                    `NIS_TO_CPPS_ID`,
                    `NIS_RATIO`,
                    `WRT_DTHMS`,
                    `LAST_DTHMS`,
                    `WRT_USERID`)
                    VALUES
                    (
                    '".$ARR['NIS_FROM_CPPS_ID']."',
                    '".$ARR['NIS_TO_CPPS_ID']."',
                    ".$ARR['NIS_RATIO'].",
                    NOW(),
                    NOW(),
                    '".$_SESSION['USERID']."');
            ";
        }
        // echo $iSQL;
		return DBManager::execMulti($iSQL);
    }
    
    /**
	 * 대체정산 삭제
	 * @param {$ARR}
	 * @param {ALLM_ID : 삭제할 해당 데이터 ID}
	 */
	function delStock($ARR) {
		$iSQL = "DELETE FROM `NT_STOCK`.`NSP_IO_SUBSTITUTION`
					WHERE `NIS_FROM_CPPS_ID`='".addslashes($ARR['NIS_FROM_CPPS_ID'])."'
                    AND `NIS_TO_CPPS_ID`='".addslashes($ARR['NIS_TO_CPPS_ID'])."';
		";
		return DBManager::execQuery($iSQL);
    }
    
    /* 헤더 조회 */
    function getChart($ARR){
        /* 청구대비재고비율설정 및 독립정산 기준 */
        $iSQL = "SELECT 
                    NP.CPP_ID,
                    NP.CPP_NM_SHORT
                FROM 
                    NT_CODE.NCP_PART NP
                WHERE NP.DEL_YN='N' 
                AND NP.CPP_USE_YN = 'Y'
                ORDER BY NP.CPP_ORDER_NUM ;
        ";
        $Result	= DBManager::getResult($iSQL);
        // echo $iSQL; 
        
        $rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"CPP_ID"=>$RS['CPP_ID'],
					"CPP_NM_SHORT"=>$RS['CPP_NM_SHORT']
				));
        }
        $Result->close();		
        unset($RS);
    
        if(empty($rtnArray)) {
            return HAVE_NO_DATA;
        } else {
            return $rtnArray;
        }
    }


     /* 데이터 조회 */
     function getList($ARR){

		$LimitStart = ($ARR['CURR_PAGE']-1) * $ARR['PER_PAGE'];
        $LimitEnd   = $ARR['PER_PAGE'];

        $LIMIT_SQL = "";
		if($ARR['REQ_MODE'] == CASE_LIST){
			$LIMIT_SQL = "LIMIT $LimitStart,$LimitEnd";
        }
        
        $tmpKeyword = "";
        
        $ARR['START_DT_SEARCH'] = str_replace("-", "", $ARR['START_DT_SEARCH']); 			
        $ARR['END_DT_SEARCH']= str_replace("-", "", $ARR['END_DT_SEARCH']); 
        
        if($ARR['START_DT_SEARCH'] != '' && $ARR['END_DT_SEARCH'] != ''){
            $tmpKeyword .= "AND NSB.NSB_ID BETWEEN '".addslashes($ARR['START_DT_SEARCH'])."' AND '".addslashes($ARR['END_DT_SEARCH'])."' \n";
        }else if($ARR['START_DT_SEARCH'] != '' ){
            $tmpKeyword .= "AND NSB.NSB_ID >= '".$ARR['START_DT_SEARCH']."' \n";
        }else if($ARR['END_DT_SEARCH'] != ''){
            $tmpKeyword .= "AND NSB.NSB_ID <= '".$ARR['END_DT_SEARCH']."' \n";
        }
        
        if($ARR['CSD_ID_SEARCH'] != ''){
			$tmpKeyword .= "AND NG.CSD_ID IN (";
			foreach ($ARR['CSD_ID_SEARCH'] as $index => $CSD_ARRAY) {
				$tmpKeyword .=" '".$CSD_ARRAY."',";
			}
			$tmpKeyword=substr($tmpKeyword, 0, -1); // 마지막 콤마제거
			$tmpKeyword .= ") \n";
        }
        
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
        
        //공업사명 검색
		if($ARR['MGG_NM_SEARCH'] != '') {
            $tmpKeyword .= "AND NG.MGG_ID = '".$ARR['MGG_NM_SEARCH']."' \n";	
        }
        
        // 청구식 검색
		if($ARR['RET_ID_SEARCH'] !=  '' || $ARR['RET_ID_SEARCH'] != null) {
			$tmpKeyword .= "AND NGS.RET_ID IN ( ";
			foreach ($ARR['RET_ID_SEARCH'] as $index => $RET_ID) {
				$tmpKeyword .=" '".$RET_ID."',";
			}
			$tmpKeyword = substr($tmpKeyword, 0, -1); // 마지막 콤마제거
			$tmpKeyword .= ") \n";
        }

		//거래여부 검색
		if($ARR['RCT_ID_SEARCH'] != '') {
			$tmpKeyword .= "AND NGS.RCT_ID = '".$ARR['RCT_ID_SEARCH']."' \n";	
        }
        
        
		// 페이징처리를 위해 전체데이터
		$iSQL = "SELECT 
                    COUNT(*) AS CNT
                FROM 
                    (
                        SELECT 
                            NG.MGG_ID,
                            NG.MGG_NM,
                            NG.CSD_ID,
                            NT_CODE.GET_CSD_NM(NG.CSD_ID) AS CSD_NM,
                            NG.CRR_1_ID,
                            NG.CRR_2_ID,
                            NG.CRR_3_ID,
                            NT_CODE.GET_CRR_NM(NG.CRR_1_ID,NG.CRR_2_ID,NG.CRR_3_ID) AS CRR_NM,
                            NG.MGG_BIZ_ID,
                            NGS.RET_ID,
                            IFNULL(NT_CODE.GET_CODE_NM(NGS.RET_ID), '') AS RET_NM,
                            NGS.RCT_ID,
                            NT_CODE.GET_CODE_NM(NGS.RCT_ID) AS RCT_NM,
                            NGS.MGG_BIZ_START_DT,
                            NGS.MGG_BIZ_END_DT,
                            IFNULL(NB_CNT, 0) AS NB_CNT,
                            IFNULL(NB_TOTAL_PRICE, 0) AS NB_TOTAL_PRICE,
                            NGS.MGG_INSU_MEMO ,
                            NGS.MGG_MANUFACTURE_MEMO 
                        FROM 
                            (SELECT 
                                        MGG_ID,
                                        COUNT(NB_TOTAL_PRICE) AS NB_CNT,
                                        SUM(NB_TOTAL_PRICE) AS NB_TOTAL_PRICE
                                    FROM NT_BILL.NBB_BILL
                                    WHERE DEL_YN = 'N'
                                    GROUP BY MGG_ID ) NB
                            JOIN NT_MAIN.NMG_GARAGE NG
                            ON NB.MGG_ID = NG.MGG_ID  AND NG.DEL_YN ='N'
                            JOIN NT_MAIN.NMG_GARAGE_SUB NGS
                            ON NB.MGG_ID = NGS.MGG_ID
                            LEFT OUTER JOIN NT_STOCK.NSS_STAT_BILLSTOCK NSB
					        ON NB.MGG_ID = NSB.MGG_ID
                        WHERE
                            1=1
                            $tmpKeyword
                    ) CNT;
        ";
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
		///

		// 리스트 가져오기
		$iSQL = "SELECT 
                    NG.MGG_ID,
                    NG.MGG_NM,
                    NG.CSD_ID,
                    NT_CODE.GET_CSD_NM(NG.CSD_ID) AS CSD_NM,
                    NG.CRR_1_ID,
                    NG.CRR_2_ID,
                    NG.CRR_3_ID,
                    NT_CODE.GET_CRR_NM(NG.CRR_1_ID,NG.CRR_2_ID,NG.CRR_3_ID) AS CRR_NM,
                    NG.MGG_BIZ_ID,
                    NGS.RET_ID,
                    IFNULL(NT_CODE.GET_CODE_NM(NGS.RET_ID), '') AS RET_NM,
                    NGS.RCT_ID,
                    NT_CODE.GET_CODE_NM(NGS.RCT_ID) AS RCT_NM,
                    NGS.MGG_BIZ_START_DT,
                    IFNULL(NGS.MGG_BIZ_END_DT, '') AS MGG_BIZ_END_DT,
                    -- IFNULL(CONCAT(SUBSTRING(NSB.`NSB_ID`,1, 4),'-',CONCAT(NSB.`NSB_ID`,5,6),'-',CONCAT(NSB.`NSB_ID`,7,8) ), '') AS NSB_ID,
                    IFNULL(NSB.`NSB_ID`, '') AS NSB_ID,
                    IFNULL(NSB.`NSB_BILL_CNT`, 0) AS NSB_BILL_CNT,
                    IFNULL(NSB.`NSB_BILL_AMOUNT`, 0) AS NSB_BILL_AMOUNT,
                    IFNULL(NSB.`NSB_SUPPLY_AMOUNT`, 0) AS NSB_SUPPLY_AMOUNT, 
                    IFNULL(NSB.`NSB_STOCK_AMOUNT`, 0) AS NSB_STOCK_AMOUNT,
                    NGS.MGG_INSU_MEMO, -- 보험사협력업체
					NGS.MGG_MANUFACTURE_MEMO -- 제조사협력업체
                FROM 
                    ( SELECT 
                        MGG_ID,
                        COUNT(NB_TOTAL_PRICE) AS NB_CNT,
                        SUM(NB_TOTAL_PRICE) AS NB_TOTAL_PRICE
                     FROM NT_BILL.NBB_BILL
                     WHERE DEL_YN = 'N'
                     GROUP BY MGG_ID ) NB
                    JOIN NT_MAIN.NMG_GARAGE NG
                    ON NB.MGG_ID = NG.MGG_ID  AND NG.DEL_YN ='N'
                    JOIN NT_MAIN.NMG_GARAGE_SUB NGS
                    ON NB.MGG_ID = NGS.MGG_ID
					LEFT OUTER JOIN NT_STOCK.NSS_STAT_BILLSTOCK NSB
					ON NB.MGG_ID = NSB.MGG_ID
                WHERE
                    1=1
                    $tmpKeyword
                ORDER BY NGS.MGG_BIZ_START_DT DESC
                $LIMIT_SQL ;
        ";
        // echo $iSQL;
		$Result= DBManager::getResult($iSQL);

        $rtnArray = array();
        $summayArray = array();
		while($RS = $Result->fetch_array()){
            $summayArray = $this->getSubList($RS);

			array_push($rtnArray,
				array(
				"IDX"=>$IDX--,
				"MGG_ID"=>$RS['MGG_ID'],
				"MGG_NM"=>$RS['MGG_NM'],
				"RET_ID"=>$RS['RET_ID'],
				"RET_NM"=>$RS['RET_NM'],
				"CSD_ID"=>$RS['CSD_ID'],
				"CSD_NM"=>$RS['CSD_NM'],
				"CRR_1_ID"=>$RS['CRR_1_ID'],
				"CRR_2_ID"=>$RS['CRR_2_ID'],
				"CRR_3_ID"=>$RS['CRR_3_ID'],
				"CRR_NM"=>$RS['CRR_NM'],
				"MGG_BIZ_ID"=>$RS['MGG_BIZ_ID'],
				"RCT_ID"=>$RS['RCT_ID'],
				"RCT_NM"=>$RS['RCT_NM'],
                "MGG_BIZ_START_DT"=>$RS['MGG_BIZ_START_DT'],
                "MGG_BIZ_END_DT"=>$RS['MGG_BIZ_END_DT'],
                "NSB_ID"=>$RS['NSB_ID'],
                "NSB_BILL_CNT"=>$RS['NSB_BILL_CNT'],
                "NSB_BILL_AMOUNT"=>$RS['NSB_BILL_AMOUNT'],
                "NSB_SUPPLY_AMOUNT"=>$RS['NSB_SUPPLY_AMOUNT'],
                "NSB_STOCK_AMOUNT"=>$RS['NSB_STOCK_AMOUNT'],
                "MGG_INSU_MEMO"=>$RS['MGG_INSU_MEMO'],
                "MGG_MANUFACTURE_MEMO"=>$RS['MGG_MANUFACTURE_MEMO'],
                "SUMMARY"=>$summayArray
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



    /* 공업사별 집계 조회 */
	function getSubList($ARR) {

		// 리스트 가져오기
		$iSQL = "SELECT 
                    NSBP.`NSB_ID`,
                    NSBP.`MGG_ID`,
                    NP.`CPP_ID`,
                    IFNULL(NSBP.NSBP_SUPPLY_CNT, 0 ) AS NSBP_SUPPLY_CNT,
                    IFNULL(NSBP.NSBP_BILL_CNT, 0 ) AS NSBP_BILL_CNT,
                    IFNULL(NSBP.NSBP_STOCK_CNT, 0 ) AS NSBP_STOCK_CNT,
                    round(if(IFNULL(NSBP.NSBP_BILL_CNT, 0 ) <> 0 , IFNULL(NSBP.NSBP_STOCK_CNT, 0 ) /IFNULL(NSBP.NSBP_BILL_CNT, 0 ) ,0),2) AS NSBP_RATIO
                FROM `NT_CODE`.`NCP_PART` NP
                LEFT OUTER JOIN (
                    SELECT 
                        NSBP.`CPP_ID`,
                        NSBP.`NSB_ID`,
                        NSBP.`MGG_ID`,
                        SUM(IFNULL(NSBP.`NSBP_SUPPLY_CNT`, 0)) AS NSBP_SUPPLY_CNT,
                        SUM(IFNULL(NSBP.`NSBP_BILL_CNT`, 0)) AS NSBP_BILL_CNT,
                        SUM(IFNULL(NSBP.`NSBP_STOCK_CNT`, 0)) AS NSBP_STOCK_CNT,
                        NSBP.`WRT_DTHMS`,
                        NSBP.`LAST_DTHMS`,
                        NSBP.`WRT_USERID`
                    FROM `NT_STOCK`.`NSS_STAT_BILLSTOCK_PARTS` NSBP
                    WHERE MGG_ID = '".addslashes($ARR['MGG_ID'])."'
                    AND NSB_ID = '".addslashes($ARR['NSB_ID'])."'
                    GROUP BY NSBP.`CPP_ID` 
                ) NSBP
                ON NSBP.CPP_ID = NP.CPP_ID
                WHERE NP.DEL_YN = 'N'
                AND NP.CPP_USE_YN = 'Y'
                ORDER BY NP.CPP_ORDER_NUM ;
		";
		// echo $iSQL."\n";
		$Result= DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
				"NSB_ID"=>$RS['NSB_ID'],
                "MGG_ID"=>$RS['MGG_ID'],
                "CPP_ID"=>$RS['CPP_ID'],
				"NSBP_SUPPLY_CNT"=>$RS['NSBP_SUPPLY_CNT'],
				"NSBP_BILL_CNT"=>$RS['NSBP_BILL_CNT'],
				"NSBP_STOCK_CNT"=>$RS['NSBP_STOCK_CNT'],
				"NSBP_RATIO"=>$RS['NSBP_RATIO']
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



	/** NT_STOCK.NSS_STAT_BILLSTOCK 해당 년/월 가져오기 */
	function getYear(){
		$iSQL = "SELECT 
					SUBSTRING(NSB_ID,1,4) AS NSB_YEAR
                 FROM NT_STOCK.NSS_STAT_BILLSTOCK 
                 GROUP BY NSB_YEAR
                 ORDER BY NSB_YEAR DESC;
		";
		$Result= DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
				"NSB_YEAR"=>$RS['NSB_YEAR']
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
$cBillInventory = new cBillInventory(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
$cBillInventory->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
