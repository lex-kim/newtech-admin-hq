<?php

class cMngtReceipt extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	function cMngtReceipt($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
		$this->DATA_SAVE_PATH = $DATA_SAVE_PATH;
		$this->ALLOW_FILE_EXT = $ALLOW_FILE_EXT;
    }
    
    /* 공업사 정보들  */
    function getData($ARR){
        /* 청구대비재고비율설정 및 독립정산 기준 */
        $iSQL = "SELECT 
                    NPS.CPPS_ID,
                    NPS.CPPS_NM_SHORT
                FROM 
                    NT_CODE.NCP_PART_SUB NPS
                WHERE NPS.DEL_YN='N' 
                ORDER BY NPS.CPPS_ORDER_NUM ;
        ";
        $Result	= DBManager::getResult($iSQL);
        // echo $iSQL; 
        
        $rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"CPPS_ID"=>$RS['CPPS_ID'],
					"CPPS_NM_SHORT"=>$RS['CPPS_NM_SHORT']
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
	
		if($ARR['DT_KEY_SEARCH'] != '') {
            if($ARR['DT_KEY_SEARCH'] == 'NIH_DT'){
                if($ARR['START_DT_SEARCH'] != '' && $ARR['END_DT_SEARCH'] != ''){
                    $tmpKeyword .= "AND NIH.NIH_DT BETWEEN '".addslashes($ARR['START_DT_SEARCH'])."' AND '".addslashes($ARR['END_DT_SEARCH'])."' \n";
                }else if($ARR['START_DT_SEARCH'] != '' ){
                    $tmpKeyword .= "AND NIH.NIH_DT >= '".$ARR['START_DT_SEARCH']."' \n";
                }else if($ARR['END_DT_SEARCH'] != ''){
                    $tmpKeyword .= "AND NIH.NIH_DT <= '".$ARR['END_DT_SEARCH']."' \n";
                }
            }else if($ARR['DT_KEY_SEARCH'] == 'NIH_APPROVED_DTHMS'){
                if($ARR['START_DT_SEARCH'] != '' && $ARR['END_DT_SEARCH'] != ''){
                    $tmpKeyword .= "AND DATE_FORMAT(NIH.NIH_DT, '%Y-%m-%d') BETWEEN '".addslashes($ARR['START_DT_SEARCH'])."' AND '".addslashes($ARR['END_DT_SEARCH'])."'";
                }else if($ARR['START_DT_SEARCH'] != '' ){
                    $tmpKeyword .= "AND DATE_FORMAT(NIH.NIH_DT, '%Y-%m-%d') >= '".$ARR['START_DT_SEARCH']."' \n";
                }else if($ARR['END_DT_SEARCH'] != ''){
                    $tmpKeyword .= "AND DATE_FORMAT(NIH.NIH_DT, '%Y-%m-%d') <= '".$ARR['END_DT_SEARCH']."' \n";
                }
            }else if($ARR['DT_KEY_SEARCH'] == 'NB_CLAIM_DT'){
                $joinNB = ", `NT_BILL`.`NBB_BILL` NB ";
                $tmpKeyword .= " AND NIH.NM_ID = NB.NB_ID AND NB.DEL_YN = 'N' ";
                if($ARR['START_DT_SEARCH'] != '' && $ARR['END_DT_SEARCH'] != ''){
                    $tmpKeyword .= "AND DATE_FORMAT(NB.NB_CLAIM_DT, '%Y-%m-%d') BETWEEN '".addslashes($ARR['START_DT_SEARCH'])."' AND '".addslashes($ARR['END_DT_SEARCH'])."'";
                }else if($ARR['START_DT_SEARCH'] != '' ){
                    $tmpKeyword .= "AND DATE_FORMAT(NB.NB_CLAIM_DT, '%Y-%m-%d') >= '".$ARR['START_DT_SEARCH']."' \n";
                }else if($ARR['END_DT_SEARCH'] != ''){
                    $tmpKeyword .= "AND DATE_FORMAT(NB.NB_CLAIM_DT, '%Y-%m-%d') <= '".$ARR['END_DT_SEARCH']."' \n";
                }
            }
		} 

		// if($ARR['CSD_ID_SEARCH'] != '') {
		// 	$tmpKeyword .= "AND INSTR(`NCB`.`MGB_TITLE`,'".addslashes($ARR['MGB_TITLE_SEARCH'])."')>0 ";
        // } 
        
        if($ARR['CSD_ID_SEARCH'] != ''){
			$tmpKeyword .= "AND NG.CSD_ID IN (";
			foreach ($ARR['CSD_ID_SEARCH'] as $index => $CSD_ARRAY) {
				$tmpKeyword .=" '".$CSD_ARRAY."',";
			}
			$tmpKeyword=substr($tmpKeyword, 0, -1); // 마지막 콤마제거
			$tmpKeyword .= ") \n";
        }
        
        if($ARR['CRR_ID_SEARCH'] !=  '' || $ARR['CRR_ID_SEARCH'] != null) {
			// $tmpKeyword .= "AND CONCAT(NG.CRR_1_ID,',',NG.CRR_2_ID,',',NG.CRR_3_ID) IN ( \n";
			// foreach ($ARR['CRR_ID_SEARCH'] as $index => $CRR_ID) {
			// 	$tmpKeyword .=" '".$CRR_ID."',";
			// }
			// $tmpKeyword = substr($tmpKeyword, 0, -1); // 마지막 콤마제거
            // $tmpKeyword .= ")";

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
        
        //사업자번호 검색
        if($ARR['MGG_BIZ_ID_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(NG.MGG_BIZ_ID,'".addslashes($ARR['MGG_BIZ_ID_SEARCH'])."')>0 ";	
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

		//변동원인 검색
		if($ARR['RST_ID_SEARCH'] != '') {
			$tmpKeyword .= "AND NIH.RST_ID = '".$ARR['RST_ID_SEARCH']."' \n";	
        }
        $isTrue=filter_var($ARR['EXCETION_SEARCH'], FILTER_VALIDATE_BOOLEAN);
		if($isTrue) {
			$tmpKeyword .= "AND NIH.RST_ID != '".STOCK_USE."' \n";
		} 

		// 페이징처리를 위해 전체데이터
		$iSQL = "SELECT 
                    COUNT(*) AS CNT
                FROM 
                    (
                    SELECT NIH.`MGG_ID`, NIH.`NIH_DT` ,NIH.`RST_ID`
                    FROM 
                        `NT_STOCK`.`NSI_IO_HISTORY` NIH,
                        `NT_MAIN`.`NMG_GARAGE` NG,
                        `NT_MAIN`.`NMG_GARAGE_SUB` NGS
                        $joinNB
                    WHERE NIH.MGG_ID = NG.MGG_ID 
                    AND NG.MGG_ID = NGS.MGG_ID
                    AND NGS.DEL_YN ='N'
                    AND NG.DEL_YN='N'
                    $tmpKeyword
                    GROUP BY NIH.`MGG_ID`, NIH.`NIH_DT` ,NIH.`RST_ID`, NIH.`NM_ID`
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
                    NIH.`NIH_ID`,
                    NIH.`MGG_ID`,
                    NIH.`NIH_DT`,
                    NIH.`NM_ID`,
                    NG.`MGG_NM`,
                    NG.`CSD_ID`,
                    NT_CODE.GET_CSD_NM( NG.`CSD_ID`) AS CSD_NM,
                    NG.`CRR_1_ID`,
                    NG.`CRR_2_ID`,
                    NG.`CRR_3_ID`,
                    NT_CODE.GET_CRR_NM(NG.`CRR_1_ID`,NG.`CRR_2_ID`,NG.`CRR_3_ID`) AS CRR_NM,
                    NG.`MGG_BIZ_ID`,
                    NGS.`RET_ID`,
                    NT_CODE.GET_CODE_NM( NGS.`RET_ID`) AS RET_NM,
                    NIH.`RST_ID`,
                    NT_CODE.GET_CODE_NM(NIH.`RST_ID`) AS RST_NM,
                    IFNULL(NIH.`NIH_APPROVED_YN` , 'N') AS NIH_APPROVED_YN,
                    IFNULL(SUBSTRING(NIH.`NIH_APPROVED_DTHMS`, 1, 10), '') AS NIH_APPROVED_DTHMS
                FROM 
                    `NT_STOCK`.`NSI_IO_HISTORY` NIH,
                    `NT_MAIN`.`NMG_GARAGE` NG,
                    `NT_MAIN`.`NMG_GARAGE_SUB` NGS
                    $joinNB
                WHERE NIH.MGG_ID = NG.MGG_ID 
                AND NG.MGG_ID = NGS.MGG_ID
                AND NGS.DEL_YN ='N'
                AND NG.DEL_YN='N'
                $tmpKeyword
                GROUP BY NIH.`MGG_ID`, NIH.`NIH_DT` ,NIH.`RST_ID`, NIH.`NM_ID`
				ORDER BY NIH.LAST_DTHMS DESC, NIH.`NIH_DT` DESC 
                $LIMIT_SQL ;
        ";
        // echo $iSQL;
		$Result= DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
            $summayArray = $this->getSubList($RS);

			array_push($rtnArray,
				array(
				"IDX"=>$IDX--,
				"NIH_ID"=>$RS['NIH_ID'],
				"MGG_ID"=>$RS['MGG_ID'],
				"NIH_DT"=>$RS['NIH_DT'],
				"NM_ID"=>$RS['NM_ID'],
				"MGG_NM"=>$RS['MGG_NM'],
				"CSD_ID"=>$RS['CSD_ID'],
				"CSD_NM"=>$RS['CSD_NM'],
				"CRR_1_ID"=>$RS['CRR_1_ID'],
				"CRR_2_ID"=>$RS['CRR_2_ID'],
				"CRR_3_ID"=>$RS['CRR_3_ID'],
				"CRR_NM"=>$RS['CRR_NM'],
				"MGG_BIZ_ID"=>$RS['MGG_BIZ_ID'],
				"RET_NM"=>$RS['RET_NM'],
				"RST_ID"=>$RS['RST_ID'],
                "RST_NM"=>$RS['RST_NM'],
                "NIH_APPROVED_YN"=>$RS['NIH_APPROVED_YN'],
                "NIH_APPROVED_DTHMS"=>$RS['NIH_APPROVED_DTHMS'],
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

    /* 부품별 수량 조회 */
	function getSubList($ARR) {
       
        $NM_ID = $ARR['NM_ID'] == '' ? '' :  "   AND NIH.NM_ID = '".$ARR['NM_ID']."' ";
		// 리스트 가져오기
		$iSQL = "SELECT 
                    NPS.CPPS_ID,
                    NPS.CPPS_NM_SHORT,
                    IFNULL(NIH.NIH_CNT, 0) AS NIS_AMOUNT
                FROM 
                    `NT_CODE`.`NCP_PART_SUB` NPS
                JOIN `NT_CODE`.`NCP_PART` NP
                ON NP.CPP_ID = NPS.CPP_ID AND NP.CPP_USE_YN ='Y' AND NP.DEL_YN='N'
                LEFT OUTER JOIN `NT_STOCK`.`NSI_IO_HISTORY` NIH
                ON NPS.CPPS_ID = NIH.CPPS_ID  
                    AND NIH.MGG_ID = '".$ARR['MGG_ID']."'
                    AND NIH.NIH_DT = '".$ARR['NIH_DT']."'
                    AND NIH.RST_ID = '".$ARR['RST_ID']."' 
                    $NM_ID
                WHERE NPS.DEL_YN='N' 
               
                ORDER BY NPS.CPPS_ORDER_NUM;
		";
		// echo $iSQL."\n";
		$Result= DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
				"CPPS_ID"=>$RS['CPPS_ID'],
				"CPPS_NM_SHORT"=>$RS['CPPS_NM_SHORT'],
				"NIS_AMOUNT"=>$RS['NIS_AMOUNT']
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
    

    /* 삭제 */
    function delData($ARR) {
		$iSQL = "DELETE FROM `NT_STOCK`.`NSI_IO_HISTORY`
					WHERE `MGG_ID` = '".addslashes($ARR['MGG_ID'])."'
                    AND `NIH_DT` = '".addslashes($ARR['NIH_DT'])."'
                    AND `RST_ID` = '".addslashes($ARR['RST_ID'])."';
        ";
       
		return DBManager::execQuery($iSQL);
    }
    

    /* 승인 */
    function setData($ARR){
        $iSQL ="UPDATE `NT_STOCK`.`NSI_IO_HISTORY`
                    SET
                    `NIH_APPROVED_YN` = 'Y',
                    `NIH_APPROVED_DTHMS` = NOW(),
                    `NIH_APPROVED_USERID` = '".$_SESSION['USERID']."',
                    `LAST_DTHMS` = NOW()
                    WHERE `MGG_ID` = '".addslashes($ARR['MGG_ID'])."'
                    AND `NIH_DT` = '".addslashes($ARR['NIH_DT'])."'
                    AND `RST_ID` = '".addslashes($ARR['RST_ID'])."' ;
        ";
        return DBManager::execQuery($iSQL);
    }

}
// 0. 객체 생성
$cMngtReceipt = new cMngtReceipt(FILE_UPLOAD_PATH,ALLOW_FILE_EXT); //서버용
$cMngtReceipt->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
