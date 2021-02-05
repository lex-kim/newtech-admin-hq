<?php

class cInventory extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	// Class Object Constructor
	function cInventory($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
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
        if($ARR['NIE_INDEPENDENT_STOCK_RATIO'] != ''){
            $iSQL = "DELETE FROM NT_STOCK.NSP_IO_ENV;
                    INSERT INTO `NT_STOCK`.`NSP_IO_ENV`
                    (
                    `NIE_INDEPENDENT_STOCK_RATIO`,
                    `WRT_DTHMS`,
                    `LAST_DTHMS`,
                    `WRT_USERID`)
                    VALUES
                    (
                    ".$ARR['NIE_INDEPENDENT_STOCK_RATIO'].",
                    NOW(),
                    NOW(),
                    '".$_SESSION['USERID']."' 
                    );
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
    

    /* 통계자료 조회 */
    function getChart($ARR){
        $tmpKeyword = $this->getWhere($ARR);

        $iSQL = "SELECT
                    NPS.CPPS_NM_SHORT,
                    NPS.CPPS_ID,
                    ROUND(SUM(IFNULL(NIS.NIS_AMOUNT, 0 )),2) AS AMOUNT
                FROM 
                    NT_CODE.NCP_PART_SUB NPS
                LEFT OUTER JOIN NT_MAIN.NMG_GARAGE_PART NGP
                ON NGP.CPPS_ID = NPS.CPPS_ID AND NGP.CPP_ID = NPS.CPP_ID AND NPS.DEL_YN = 'N' 
                LEFT OUTER JOIN NT_MAIN.V_MGG_STATUS NG
                ON NG.MGG_ID = NGP.MGG_ID $tmpKeyword
                LEFT OUTER JOIN NT_STOCK.NSI_IO_SUMMARY NIS 
                ON NPS.CPPS_ID = NIS.CPPS_ID AND NG.MGG_ID = NIS.MGG_ID 
                WHERE NPS.DEL_YN ='N'
                    
                GROUP BY NPS.CPPS_ID
                ORDER BY NPS.CPPS_ORDER_NUM;
       
        ";
        // echo $iSQL;
        $Result	= DBManager::getResult($iSQL);

        $rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"CPPS_ID"=>$RS['CPPS_ID'],
					"CPPS_NM_SHORT"=>$RS['CPPS_NM_SHORT'],
					"AMOUNT"=>$RS['AMOUNT']
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
		if($ARR['REQ_MODE'] == CASE_LIST && $ARR['CPPS_ID_SEARCH'] == ''){
			$LIMIT_SQL = "LIMIT $LimitStart,$LimitEnd";
        }
        
		$tmpKeyword =$this->getWhere($ARR);

		// 페이징처리를 위해 전체데이터
		$iSQL = "SELECT 
                    COUNT(*) AS CNT
                FROM 
                    (
                        SELECT 
                            NG.MGG_ID,
                            NG.MGG_NM,
                            NG.CSD_ID,
                            NG.CSD_NM,
                            NG.CRR_1_ID,
                            NG.CRR_2_ID,
                            NG.CRR_3_ID,
                            NT_CODE.GET_CRR_NM(NG.CRR_1_ID,NG.CRR_2_ID,NG.CRR_3_ID) AS CRR_NM,
                            NG.MGG_BIZ_ID,
                            NG.RET_ID,
                            NG.RET_NM,
                            NG.RCT_ID,
                            NG.RCT_NM,
                            NG.MGG_BIZ_START_DT,
                            NG.MGG_BIZ_END_DT,
                            IFNULL(NB_CNT, 0) AS NB_CNT,
                            IFNULL(NB_TOTAL_PRICE, 0) AS NB_TOTAL_PRICE,
                            NG.MGG_INSU_MEMO ,
                            NG.MGG_MANUFACTURE_MEMO 
                        FROM 
                            NT_MAIN.V_MGG_STATUS NG
                            JOIN NT_MAIN.NMG_GARAGE_PART NGP
                            ON NGP.MGG_ID = NG.MGG_ID  
                            LEFT OUTER JOIN (SELECT 
                                        MGG_ID,
                                        COUNT(NB_TOTAL_PRICE) AS NB_CNT,
                                        SUM(NB_TOTAL_PRICE) AS NB_TOTAL_PRICE
                                    FROM NT_BILL.NBB_BILL
                                    WHERE DATE_FORMAT(NB_CLAIM_DT,'%Y-%m') = DATE_FORMAT(NOW(),'%Y-%m')
                                    GROUP BY MGG_ID ) NB
                            ON NB.MGG_ID = NG.MGG_ID
                        WHERE
                            1=1
                            $tmpKeyword
                        GROUP BY NGP.MGG_ID
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
		
         //부품 검색
		if($ARR['CPPS_ID_SEARCH'] != '') {
			$TOTAL_LIST_COUNT = 0;
        }
		// 리스트 가져오기
		$iSQL = "SELECT 
                    NG.MGG_ID,
                    NG.MGG_NM,
                    NG.CSD_ID,
                    NG.CSD_NM,
                    NG.CRR_1_ID,
                    NG.CRR_2_ID,
                    NG.CRR_3_ID,
                    NT_CODE.GET_CRR_NM(NG.CRR_1_ID,NG.CRR_2_ID,NG.CRR_3_ID) AS CRR_NM,
                    NG.MGG_BIZ_ID,
                    NG.RET_ID,
                    NG.RET_NM,
                    NG.RCT_ID,
                    NG.RCT_NM,
                    NG.MGG_BIZ_START_DT,
                    IFNULL(NG.MGG_BIZ_END_DT, '') AS MGG_BIZ_END_DT,
                    IFNULL(NB_CNT, 0) AS NB_CNT,
                    IFNULL(NB_TOTAL_PRICE, 0) AS NB_TOTAL_PRICE,
                    NG.MGG_INSU_MEMO ,
                    NG.MGG_MANUFACTURE_MEMO 
                FROM 
                    NT_MAIN.V_MGG_STATUS NG
                    JOIN NT_MAIN.NMG_GARAGE_PART NGP
                    ON NGP.MGG_ID = NG.MGG_ID 
                    LEFT OUTER JOIN (SELECT 
                                MGG_ID,
                                COUNT(NB_TOTAL_PRICE) AS NB_CNT,
                                SUM(NB_TOTAL_PRICE) AS NB_TOTAL_PRICE
                            FROM NT_BILL.NBB_BILL
                            WHERE DATE_FORMAT(NB_CLAIM_DT,'%Y-%m') = DATE_FORMAT(NOW(),'%Y-%m')
                            GROUP BY MGG_ID ) NB
                    ON NB.MGG_ID = NG.MGG_ID
                WHERE
                    1=1
                    $tmpKeyword
                GROUP BY NGP.MGG_ID
                ORDER BY NG.MGG_BIZ_START_DT DESC
                $LIMIT_SQL ;
        ";
        // echo $iSQL;
		$Result= DBManager::getResult($iSQL);

        $rtnArray = array();
        // echo 'totalCnt>>>'.$TOTAL_LIST_COUNT."\n" ;
		while($RS = $Result->fetch_array()){

            if($ARR['CPPS_ID_SEARCH'] != ''){
                $iSQL ="SELECT
                    COUNT(*) AS 'CNT'
                FROM 
                    NT_CODE.NCP_PART_SUB NPS
                LEFT OUTER JOIN NT_MAIN.NMG_GARAGE_PART NGP
                ON NGP.CPPS_ID = NPS.CPPS_ID AND NGP.CPP_ID = NPS.CPP_ID AND NPS.DEL_YN = 'N'      AND NGP.MGG_ID = '".$RS['MGG_ID']."'
                LEFT OUTER JOIN NT_STOCK.NSI_IO_SUMMARY NIS
                ON NPS.CPPS_ID = NIS.CPPS_ID AND NGP.MGG_ID = NIS.MGG_ID
                WHERE NPS.DEL_YN ='N'
                AND NIS.CPPS_ID = '".addslashes($ARR['CPPS_ID_SEARCH'])."'  AND NIS.NIS_AMOUNT > 0
                ORDER BY NPS.CPPS_ORDER_NUM;
                ";
    
                try{
                    // echo $iSQL;
                    $RS_S = DBManager::getRecordSet($iSQL);
                } catch (Exception $e){
                    echo "error : ".$e;
                    return ERROR_DB_SELECT_DATA;   //ERROR_DB_SELECT_DATA
                }
                $CPPS_COUNT = $RS_S['CNT'];
                unset($RS_S);

                if($CPPS_COUNT > 0){
                    $TOTAL_LIST_COUNT ++;
                    $summayArray = $this->getSubList($RS); // 공업사별 부품현황배열
                }else {
                    $summayArray = HAVE_NO_DATA;
                }
            }else {
                $summayArray = $this->getSubList($RS); // 공업사별 부품현황배열
            }
            
            if($summayArray != HAVE_NO_DATA){
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
                    "NB_CNT"=>$RS['NB_CNT'],
                    "NB_TOTAL_PRICE"=>$RS['NB_TOTAL_PRICE'],
                    "MGG_INSU_MEMO"=>$RS['MGG_INSU_MEMO'],
                    "MGG_MANUFACTURE_MEMO"=>$RS['MGG_MANUFACTURE_MEMO'],
                    "SUMMARY"=>$summayArray
                ));
            }
			
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


    function getWhere($ARR){
        $tmpKeyword = "";
	
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
			$tmpKeyword .= " AND `NG`.`MGG_ID` IN ( ";
            foreach ($ARR['MGG_NM_SEARCH'] as $index => $ARR_MGG) {
                $tmpKeyword .=" '".$ARR_MGG."',";
            }
            $tmpKeyword = substr($tmpKeyword, 0, -1); // 마지막 콤마제거
            $tmpKeyword .= ") \n";
		}
        
        // 청구식 검색
		if($ARR['RET_ID_SEARCH'] !=  '' || $ARR['RET_ID_SEARCH'] != null) {
			$tmpKeyword .= "AND NG.RET_ID IN ( ";
			foreach ($ARR['RET_ID_SEARCH'] as $index => $RET_ID) {
				$tmpKeyword .=" '".$RET_ID."',";
			}
			$tmpKeyword = substr($tmpKeyword, 0, -1); // 마지막 콤마제거
			$tmpKeyword .= ") \n";
        }

		//거래여부 검색
		if($ARR['RCT_ID_SEARCH'] != '') {
			$tmpKeyword .= "AND NG.RCT_ID = '".$ARR['RCT_ID_SEARCH']."' \n";	
        }
        
        
        //부품 검색
		if($ARR['CPPS_ID_SEARCH'] != '') {
			$subCpps = "AND NGP.CPPS_ID = '".$ARR['CPPS_ID_SEARCH']."'  \n";	
        }

        return $tmpKeyword;
    }

    /* 부품별 수량 조회 */
	function getSubList($ARR) {

		// 리스트 가져오기
		$iSQL = "SELECT
                    NPS.CPPS_ID,
                    IFNULL(ROUND(NIS.NIS_AMOUNT,2), 0 ) AS NIS_AMOUNT,
                    TRUNCATE((IFNULL(ROUND(NIS.NIS_AMOUNT,2), 0 ) * NPS.CPPS_PRICE),-1) AS NPS_PRICE
                FROM 
                    NT_CODE.NCP_PART_SUB NPS
                LEFT OUTER JOIN NT_MAIN.NMG_GARAGE_PART NGP
                ON NGP.CPPS_ID = NPS.CPPS_ID AND NGP.CPP_ID = NPS.CPP_ID AND NPS.DEL_YN = 'N'     AND NGP.MGG_ID = '".$ARR['MGG_ID']."'
                LEFT OUTER JOIN NT_STOCK.NSI_IO_SUMMARY NIS
                ON NPS.CPPS_ID = NIS.CPPS_ID AND NGP.MGG_ID = NIS.MGG_ID
                WHERE NPS.DEL_YN ='N'
                ORDER BY NPS.CPPS_ORDER_NUM;
		";
		// echo $iSQL."\n";
		$Result= DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
				"CPPS_ID"=>$RS['CPPS_ID'],
				"NIS_AMOUNT"=>$RS['NIS_AMOUNT'],
				"NPS_PRICE"=>$RS['NPS_PRICE']
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


	/* SUMMARY DATA  */
	function getSummaryData($ARR){
        $iSQL = "SELECT 
					`MGG_ID`,
					`CPPS_ID`,
					`CPP_ID`,
					`NIS_AMOUNT`
				FROM `NT_STOCK`.`NSI_IO_SUMMARY`
				WHERE MGG_ID ='".$ARR['MGG_ID']."';
        ";
        $Result	= DBManager::getResult($iSQL);
		// echo $iSQL; 
        $rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"MGG_ID"=>$RS['MGG_ID'],
					"CPPS_ID"=>$RS['CPPS_ID'],
					"CPP_ID"=>$RS['CPP_ID'],
					"NIS_AMOUNT"=>$RS['NIS_AMOUNT']
				));
        }
        $Result->close();		
        unset($RS);
    
        return $rtnArray;
	}
	


	/*독립정산 */
	function setIndependent($ARR){
        $orgData = $this->getSummaryData($ARR);
        // echo var_dump($orgData);
        $orgCNT = 0 ; 
		foreach($orgData as $row){
			$amount = $row['NIS_AMOUNT'];
			if($amount < $ARR['NIE_INDEPENDENT_STOCK_RATIO']){
				// echo $amount."<".$ARR['NIE_INDEPENDENT_STOCK_RATIO'] ; 
				$iSQL .= "UPDATE `NT_STOCK`.`NSI_IO_SUMMARY`
							SET
								`NIS_AMOUNT` = 0,
								`LAST_DTHMS` = NOW(),
								`WRT_USERID` = '"."INDE_".$_SESSION['USERID']."'
							WHERE 
								`MGG_ID` = '".addslashes($row['MGG_ID'])."'
							AND
								`CPPS_ID` = '".addslashes($row['CPPS_ID'])."'
							AND
								`CPP_ID` = '".addslashes($row['CPP_ID'])."' ;
                        ";
                $orgCNT++ ;
			}
        }
        
        if($orgCNT == 0){
            return HAVE_NO_DATA ;
        }else {
            DBManager::execMulti($iSQL);
            return OK;
        }
	}



	/* 대체정산 */
	function setSubstitution($ARR){
		$splitTmp = explode('_', $ARR['NIE_SUBSTITUTION']);
		$fromId = $splitTmp[0]; 
		$toId = $splitTmp[1]; 
		$ratio = $splitTmp[2]; 
		$orgData = $this->getSummaryData($ARR);
		$orgCNT = 0 ; 
		foreach($orgData as $row){
			$amount = $row['NIS_AMOUNT'];
			if($fromId == $row['CPPS_ID']){
				if($amount < $ARR['NIE_INDEPENDENT_STOCK_RATIO']){
					$iSQL .= "UPDATE `NT_STOCK`.`NSI_IO_SUMMARY`
								SET
									`NIS_AMOUNT` = `NIS_AMOUNT` + ($amount*$ratio),
									`LAST_DTHMS` = NOW(),
									`WRT_USERID` = '"."INDE_".$_SESSION['USERID']."'
								WHERE 
									`MGG_ID` = '".addslashes($row['MGG_ID'])."'
								AND
									`CPPS_ID` = '".addslashes($toId)."'
								;
							";

					$iSQL .= "UPDATE `NT_STOCK`.`NSI_IO_SUMMARY`
								SET
									`NIS_AMOUNT` = 0,
									`LAST_DTHMS` = NOW(),
									`WRT_USERID` = '"."INDE_".$_SESSION['USERID']."'
								WHERE 
									`MGG_ID` = '".addslashes($row['MGG_ID'])."'
								AND
									`CPPS_ID` = '".$row['CPPS_ID']."'
								;
                    ";
                     ++$orgCNT ;
				}
			}
			
        }
		if($orgCNT == 0){
            return HAVE_NO_DATA ;
        }else {
            DBManager::execMulti($iSQL);
            return OK;
        }
	}

}
// 0. 객체 생성
$cInventory = new cInventory(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
$cInventory->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
