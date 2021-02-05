
<?php
class cEnrol extends DBManager {
	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자
	// Class Object Constructor
	function cEnrol($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
		$this->DATA_SAVE_PATH = $DATA_SAVE_PATH;
		$this->ALLOW_FILE_EXT = $ALLOW_FILE_EXT;
	}

    /* 보험사 팀 지역 정보 조회 */
	function getRegion($ARR){
		$iSQL = "SELECT 
					VGR.CRR_1_ID,
					VGR.CRR_2_ID,
					VGR.CRR_3_ID,
					VGR.CRR_NM
				FROM NT_MAIN.V_GW_REGION VGR;
		";
		// echo "getTeamRegion\n".$iSQL."\n";
		$Result= DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"CRR_1_ID"=>$RS['CRR_1_ID'],
					"CRR_2_ID"=>$RS['CRR_2_ID'],
					"CRR_3_ID"=>$RS['CRR_3_ID'],
					"CRR_NM"=>$RS['CRR_NM']
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
    
    /* 검색에 따른 공업사리스트 */
    function getGw($ARR){
        $tmpKeyword = "";
        if($ARR['SELECT_CRR_ID'] != ''){
            $CRR_ID = explode(",", $ARR['SELECT_CRR_ID']);
            $tmpKeyword .="AND CRR_1_ID = '".$CRR_ID[0]."'
                            AND CRR_2_ID = '".$CRR_ID[1]."'
                            AND CRR_3_ID = '".$CRR_ID[2]."'
                        ";
		}
		
        if($ARR['SELECT_CRR_ID'] == '' AND $ARR['SEARCH_REGION'] != ''){
            $tmpKeyword .= "AND INSTR(`NT_CODE`.`GET_CRR_NM`(CRR_1_ID,CRR_2_ID,CRR_3_ID), '".addslashes($ARR['SEARCH_REGION'])."' ) > 0 ";
		}
		
		
        if($ARR['MGG_NM_SEARCH'] != ''){
            $tmpKeyword .="AND INSTR(`MGG_NM`,'".addslashes($ARR['MGG_NM_SEARCH'])."')>0 ";
        }
        $iSQL = "SELECT 
					MGG_ID,
                    MGG_NM
				FROM NT_MAIN.NMG_GARAGE
                WHERE DEL_YN = 'N'
                $tmpKeyword
				ORDER BY MGG_NM 
                ;
		";
		// echo "getGw\n".$iSQL."\n";
		$Result= DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"MGG_ID"=>$RS['MGG_ID'],
					"MGG_NM"=>$RS['MGG_NM']
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
	
	function getOptionGarage($ARR){
		$tmpKeyword = "";
        
        $iSQL = "SELECT 
					MGG_ID,
                    MGG_NM
				FROM NT_MAIN.NMG_GARAGE
                WHERE DEL_YN = 'N'   
				ORDER BY MGG_NM ;
		";
		// echo "getGw\n".$iSQL."\n";
		$Result= DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"MGG_ID"=>$RS['MGG_ID'],
					"MGG_NM"=>$RS['MGG_NM']
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


    /* 재고설정 데이터 조회 */
    function getStock($ARR){
        /* 청구대비재고비율설정 및 독립정산 기준 */
        $iSQL = "SELECT 
                    NIE.`NIE_BILL_VS_STOCK_RATIO`,
                    NIE.`NIE_INDEPENDENT_STOCK_RATIO`
                FROM `NT_STOCK`.`NSP_IO_ENV` NIE ;
        ";
        $RS	= DBManager::getRecordSet($iSQL);
		
		$BILL	= $RS['NIE_BILL_VS_STOCK_RATIO'];
        $INDEPENDENT	= $RS['NIE_INDEPENDENT_STOCK_RATIO'];
        unset($RS);

        /* 대체정산식 */
        $iSQL = "SELECT 
                NIS.`NIS_FROM_CPPS_ID`,
                -- `NT_CODE`.`GET_CPPS_NM_SHORT`(NIS.`NIS_FROM_CPPS_ID`) AS NIS_FROM_CPPS_NM,
                `NT_CODE`.`GET_CPPS_NM`(NIS.`NIS_FROM_CPPS_ID`) AS NIS_FROM_CPPS_NM,
                NIS.`NIS_TO_CPPS_ID`,
                -- `NT_CODE`.`GET_CPPS_NM_SHORT`(NIS.`NIS_TO_CPPS_ID`) AS NIS_TO_CPPS_NM,
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
        
        /* 출거/수거 */
        $iSQL = "SELECT RST_ID, RST_NM 
                    FROM NT_CODE.REF_STOCK_TYPE
					WHERE RST_ID != '12130' # 사용제외 
				;";

		$Result= DBManager::getResult($iSQL);

		$RSTArray = array();
		while($RS = $Result->fetch_array()){
			array_push($RSTArray,
				array(
					"RST_ID"=>$RS['RST_ID'],
					"RST_NM"=>$RS['RST_NM']
			));
		}
		$Result->close();  // 자원 반납
		unset($RS);
        
        $jsonArray = array();
        array_push($jsonArray,$BILL);
        array_push($jsonArray,$INDEPENDENT);
        array_push($jsonArray,$rtnArray);
        array_push($jsonArray,$RSTArray);
        return  $jsonArray;
       
    }


     /* 공업사 정보들  */
     function getData($ARR){
        /* 청구대비재고비율설정 및 독립정산 기준 */
        $iSQL = "SELECT 
                    NPS.CPP_ID,
                    NP.CPP_NM_SHORT,
                    NPS.CPPS_ID,
                    NPS.CPPS_NM,
                    IFNULL(ROUND(NIS_AMOUNT,2), 0) AS NIS_AMOUNT,
                    IF(IFNULL(NGP.CPPS_ID, 'N')='N', 'N', 'Y') AS PART_YN,
                    IFNULL(NGP.NGP_DEFAULT_YN, 'N') AS  NGP_DEFAULT_YN,
					NGS.MGG_MEMO
                FROM 
                    NT_CODE.NCP_PART_SUB NPS
                JOIN NT_CODE.NCP_PART NP
                    ON NP.CPP_ID = NPS.CPP_ID AND NP.DEL_YN ='N' AND NP.CPP_USE_YN='Y'
                LEFT OUTER JOIN NT_STOCK.NSI_IO_SUMMARY NIS
                    ON NIS.CPPS_ID = NPS.CPPS_ID AND NIS.CPP_ID = NPS.CPP_ID 
                    AND NIS.MGG_ID ='".$ARR['MGG_ID']."'
                LEFT OUTER JOIN NT_MAIN.NMG_GARAGE_PART NGP
                    ON NGP.CPPS_ID = NPS.CPPS_ID AND NPS.CPP_ID = NGP.CPP_ID
                    AND NGP.MGG_ID ='".$ARR['MGG_ID']."'
				LEFT OUTER JOIN NT_MAIN.NMG_GARAGE_SUB NGS
					ON NGP.MGG_ID = NGS.MGG_ID  AND  NGS.MGG_ID ='".$ARR['MGG_ID']."'
                WHERE NPS.DEL_YN='N' 
                
                ORDER BY NP.CPP_ORDER_NUM, NPS.CPPS_ORDER_NUM
                ;
        ";
        $Result	= DBManager::getResult($iSQL);
		// echo $iSQL; 
        $rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"CPP_ID"=>$RS['CPP_ID'],
					"CPP_NM_SHORT"=>$RS['CPP_NM_SHORT'],
					"CPPS_ID"=>$RS['CPPS_ID'],
					"CPPS_NM"=>$RS['CPPS_NM'],
					"NIS_AMOUNT"=>$RS['NIS_AMOUNT'],
					"PART_YN"=>$RS['PART_YN'],
					"NGP_DEFAULT_YN"=>$RS['NGP_DEFAULT_YN']
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


	/* 부품 설정 변경  */
	function setPart($ARR) {
		$iSQL = "CALL NT_MAIN.SET_GARAGE_PART(
					'".$ARR['MGG_ID']."', 
					'".$ARR['UPDATE_KIND']."', 
					'".$ARR['SET_VALUE']."', 
					'".$ARR['CPP_ID']."', 
					'".$ARR['CPPS_ID']."'
				);";

		if(DBManager::execQuery($iSQL)) {
			$rtnValue = true;
		}else{
			$rtnValue = false;
		}
		return $rtnValue;
	}

	/* 출거/수거 등록 */
	function setData($ARR) {
		// 권한확인
		// if($_SESSION['AUTH_LEVEL'] != ''){
		// 	return HAVE_NO_DATA;
		// }

		if($ARR['REQ_MODE'] == CASE_CREATE){
			$iSQL = "SELECT 
							COUNT(*) CNT 
					FROM `NT_STOCK`.`NSI_IO_HISTORY`
					WHERE MGG_ID = '".addslashes($ARR['MGG_ID'])."'
					AND RST_ID = '".addslashes($ARR['RST_ID'])."'
					AND NIH_DT = '".addslashes($ARR['NIH_DT'])."'
			";
			// echo '245'.$iSQL;
			try{
				$RS_T	= DBManager::getRecordSet($iSQL);
			} catch (Exception $e){
				echo "error : ".$e;
				return ERROR_DB_SELECT_DATA;   //ERROR_DB_SELECT_DATA
			}
			$NIH_COUNT = $RS_T['CNT'];
			unset($RS_T);
 
			if($NIH_COUNT > 0){	
				return CONFLICT;
			}else {
				$iSQL = $this->setValue($ARR);
			}
			
		}else {
			$iSQL = "DELETE FROM `NT_STOCK`.`NSI_IO_HISTORY`
						WHERE MGG_ID = '".addslashes($ARR['MGG_ID'])."'
						AND RST_ID = '".addslashes($ARR['RST_ID'])."'
						AND NIH_DT = '".addslashes($ARR['NIH_DT'])."'	;
			";
			$iSQL .= $this->setValue($ARR);

		}
		if($ARR['TYPE'] = 'trans' ){
			$iSQL .= "UPDATE NT_SALES.NS_SALES_UPLOAD
			SET NSU_PROC_YN = 'Y',
				NSU_PROC_DTHMS = NOW() ,
				NSU_PROC_USERID = '".$_SESSION['USERID']."'
			where MGG_ID = '".addslashes($ARR['MGG_ID'])."';
			";
		}
		// echo $iSQL;
		return DBManager::execMulti($iSQL);
	}

	function setValue($ARR){
		$tmpArray = $this->getData($ARR);
		$cppsArray = array();
		foreach($tmpArray as $key=>$value){
			array_push($cppsArray, $value['CPPS_ID']."||".$value['CPP_ID']);
		}
		foreach($cppsArray as $key=>$value){
			$splitPart = explode('||', $value);
			// echo $splitPart[0].">>>".$ARR[$splitPart[0]];
			if($ARR[$splitPart[0]] > 0){
				// echo '				 QUERY IN'."\n";
				$iSQL .= "INSERT INTO `NT_STOCK`.`NSI_IO_HISTORY`
							(
							`MGG_ID`,
							`CPPS_ID`,
							`CPP_ID`,
							`RST_ID`,
							`NIH_DT`,
							`NIH_CNT`,
							`NIH_APPROVED_YN`,
							`WRT_DTHMS`,
							`LAST_DTHMS`,
							`WRT_USERID`)
							VALUES
							(
							'".addslashes($ARR['MGG_ID'])."',
							'".addslashes($splitPart[0])."',
							'".addslashes($splitPart[1])."',
							'".addslashes($ARR['RST_ID'])."',
							'".addslashes($ARR['NIH_DT'])."',
							'".addslashes($ARR[$splitPart[0]])."',
							'N',
							NOW(),
							NOW(),
							'".$_SESSION['USERID']."'
							);
				";
			}
			
		}
		return $iSQL;
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
    
        if(empty($rtnArray)) {
            return HAVE_NO_DATA;
        } else {
            return $rtnArray;
        }
       
	}
	

	/*독립정산 */
	function setIndependent($ARR){
		$orgData = $this->getSummaryData($ARR);
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
			}
		}
		// echo $iSQL;
		return DBManager::execMulti($iSQL);
	}



	/* 대체정산 */
	function setSubstitution($ARR){
		$splitTmp = explode('_', $ARR['NIE_SUBSTITUTION']);
		$fromId = $splitTmp[0]; 
		$toId = $splitTmp[1]; 
		$ratio = $splitTmp[2]; 
		$orgData = $this->getSummaryData($ARR);
		// echo 'setSubstitution>>'.$fromId." ".$toId." ".$ratio."\n";
		
		foreach($orgData as $row){
			// echo  $row['CPPS_ID']."/".$row['NIS_AMOUNT']."\n";
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
				}
			}
			
		}
		// echo $iSQL;
		return DBManager::execMulti($iSQL);
	}


	/* 수정할 정보 불러오기 */
	function getEnrollData($ARR){
		$iSQL = "SELECT 
					NPS.CPP_ID,
					NP.CPP_NM_SHORT,
					NPS.CPPS_ID,
					NPS.CPPS_NM,
					NIH.NIH_CNT AS NIS_AMOUNT,
					IF(IFNULL(NGP.CPPS_ID, 'N')='N', 'N', 'Y') AS PART_YN,
					IFNULL(NGP.NGP_DEFAULT_YN, 'N') AS  NGP_DEFAULT_YN,
					NGS.MGG_MEMO
				FROM 
					NT_CODE.NCP_PART_SUB NPS
				JOIN NT_CODE.NCP_PART NP
					ON NP.CPP_ID = NPS.CPP_ID AND NP.DEL_YN ='N' AND NP.CPP_USE_YN='Y'
				LEFT OUTER JOIN NT_STOCK.NSI_IO_HISTORY NIH
					ON NIH.CPPS_ID = NPS.CPPS_ID AND NIH.CPP_ID = NPS.CPP_ID 
					AND NIH.MGG_ID ='".$ARR['MGG_ID']."' AND NIH.NIH_DT='".$ARR['NIH_DT']."'
					AND NIH.RST_ID='".$ARR['RST_ID']."'
				LEFT OUTER JOIN NT_MAIN.NMG_GARAGE_PART NGP
					ON NGP.CPPS_ID = NPS.CPPS_ID AND NPS.CPP_ID = NGP.CPP_ID
					AND NGP.MGG_ID ='".$ARR['MGG_ID']."'
				LEFT OUTER JOIN NT_MAIN.NMG_GARAGE_SUB NGS
					ON NGP.MGG_ID = NGS.MGG_ID  AND  NGS.MGG_ID ='".$ARR['MGG_ID']."'
				WHERE NPS.DEL_YN='N' 
				
				ORDER BY NP.CPP_ORDER_NUM, NPS.CPPS_ORDER_NUM
				;
        ";
        $Result	= DBManager::getResult($iSQL);
		// echo $iSQL; 
        $rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"CPP_ID"=>$RS['CPP_ID'],
					"CPP_NM_SHORT"=>$RS['CPP_NM_SHORT'],
					"CPPS_ID"=>$RS['CPPS_ID'],
					"CPPS_NM"=>$RS['CPPS_NM'],
					"NIS_AMOUNT"=>$RS['NIS_AMOUNT'],
					"PART_YN"=>$RS['PART_YN'],
					"NGP_DEFAULT_YN"=>$RS['NGP_DEFAULT_YN']
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
	

}
    // 0. 객체 생성
    $cEnrol = new cEnrol(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
    $cEnrol->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);
?>