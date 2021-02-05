<?php

class cExcept extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	// Class Object Constructor
	function cExcept($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
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
                    $tmpKeyword .= "AND NB.NB_CLAIM_DT BETWEEN '".addslashes($ARR['START_DT_SEARCH'])."' AND '".addslashes($ARR['END_DT_SEARCH'])."' \n";
                }else if($ARR['START_DT_SEARCH'] != '' ){
                    $tmpKeyword .= "AND NB.NB_CLAIM_DT >= '".$ARR['START_DT_SEARCH']."' \n";
                }else if($ARR['END_DT_SEARCH'] != ''){
                    $tmpKeyword .= "AND NB.NB_CLAIM_DT <= '".$ARR['END_DT_SEARCH']."' \n";
                }
            }else if($ARR['DT_KEY_SEARCH'] == 'NBI_REASON_DT'){ // 사유입력일
                if($ARR['START_DT_SEARCH'] != '' && $ARR['END_DT_SEARCH'] != ''){
                    $tmpKeyword .= "AND DATE_FORMAT(NBI.NBI_REASON_DT, '%Y-%m-%d') BETWEEN '".addslashes($ARR['START_DT_SEARCH'])."' AND '".addslashes($ARR['END_DT_SEARCH'])."'";
                }else if($ARR['START_DT_SEARCH'] != '' ){
                    $tmpKeyword .= "AND DATE_FORMAT(NBI.NBI_REASON_DT, '%Y-%m-%d') >= '".$ARR['START_DT_SEARCH']."' \n";
                }else if($ARR['END_DT_SEARCH'] != ''){
                    $tmpKeyword .= "AND DATE_FORMAT(NBI.NBI_REASON_DT, '%Y-%m-%d') <= '".$ARR['END_DT_SEARCH']."' \n";
                }
            }else if($ARR['DT_KEY_SEARCH'] == 'NBI_PROC_REASON_DT'){ // 사유처리일
                if($ARR['START_DT_SEARCH'] != '' && $ARR['END_DT_SEARCH'] != ''){
                    $tmpKeyword .= "AND DATE_FORMAT(NBI.NBI_PROC_REASON_DT, '%Y-%m-%d') BETWEEN '".addslashes($ARR['START_DT_SEARCH'])."' AND '".addslashes($ARR['END_DT_SEARCH'])."'";
                }else if($ARR['START_DT_SEARCH'] != '' ){
                    $tmpKeyword .= "AND DATE_FORMAT(NBI.NBI_PROC_REASON_DT, '%Y-%m-%d') >= '".$ARR['START_DT_SEARCH']."' \n";
                }else if($ARR['END_DT_SEARCH'] != ''){
                    $tmpKeyword .= "AND DATE_FORMAT(NBI.NBI_PROC_REASON_DT, '%Y-%m-%d') <= '".$ARR['END_DT_SEARCH']."' \n";
                }
			}
		}

		//보험사검색
		if($ARR['SEARCH_INSU_NM'] != ''){
            $tmpKeyword .= "AND NBI.MII_ID = '".($ARR['SEARCH_INSU_NM'])."' \n";
		}

		//보상담당자
		if($ARR['INSU_FAO_SEARCH'] != ''){
			$tmpKeyword .="AND INSTR(`NT_MAIN`.`GET_INSU_CHG_NM`(NBI.NIC_ID,'".AES_SALT_KEY."'), '".addslashes($ARR['INSU_FAO_SEARCH'])."' ) > 0 \n";
		}

		//구분검색
		if($ARR['CSD_ID_SEARCH'] != ''){
            $tmpKeyword .= "AND MG.CSD_ID IN (";
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
					$tmpKeyword .= "`MG`.`CRR_1_ID` = '".$CSD[0]."' \n";
				}else{
					$tmpKeyword .= " OR(";
					$tmpKeyword .= "  `MG`.`CRR_1_ID` = '".$CSD[0]."' \n";
                }
                if($CSD[1] != NOT_CRR_2){
                    $tmpKeyword .= "AND `MG`.`CRR_2_ID` = '".$CSD[1]."' \n";
                }
				
				if($CSD[2] != NOT_CRR_3){
					$tmpKeyword .= "AND `MG`.`CRR_3_ID` = '".$CSD[2]."' \n";
				}
				
				$tmpKeyword .= ") \n";
			}
			$tmpKeyword .= ") \n";
		}
		
		//공업사명검색
		if($ARR['SEARCH_BIZ_NM'] != '') {
			$tmpKeyword .= "AND NB.MGG_ID  = '".$ARR['SEARCH_BIZ_NM']."' \n";
		} 

		//청구식검색
		if($ARR['RET_ID_SEARCH'] != '') {
			$tmpKeyword .= "AND MG.RET_ID IN ( ";
            foreach ($ARR['RET_ID_SEARCH'] as $index => $RET_ID) {
                $tmpKeyword .=" '".$RET_ID."',";
            }
            $tmpKeyword = substr($tmpKeyword, 0, -1); // 마지막 콤마제거
            $tmpKeyword .= ") \n";
		}

		
		//접수번호
		if($ARR['NBI_REGI_NUM_SEARCH'] != ''){
			$tmpKeyword .= "AND INSTR(`NBI`.`NBI_REGI_NUM`,'".addslashes($ARR['NBI_REGI_NUM_SEARCH'])."')>0 \n";
		}

		//담보
		if($ARR['RLT_ID_SEARCH'] != '') {
			$tmpKeyword .= "AND NB.RLT_ID = '".addslashes($ARR['RLT_ID_SEARCH'])."' \n";
		} 

		//차량번호
		if($ARR['NB_CAR_NUM_SEARCH'] != ''){
			$tmpKeyword .= "AND INSTR(`NB`.`NB_CAR_NUM`,'".addslashes($ARR['NB_CAR_NUM_SEARCH'])."')>0 \n";	
		}


		//차량명
		if($ARR['CCC_NM_SEARCH'] != ''){
			$tmpKeyword .= "AND INSTR(NT_CODE.GET_CAR_NM(`NB`.`CCC_ID`), '".addslashes($ARR['CCC_NM_SEARCH'])."')>0 \n";
		}

		//청구금액
		if($ARR['PRICE_START_SEARCH'] != '' && $ARR['PRICE_END_SEARCH'] != ''){
			$tmpKeyword .= "AND NB.NB_TOTAL_PRICE BETWEEN '".addslashes($ARR['PRICE_START_SEARCH'])."' AND '".addslashes($ARR['PRICE_END_SEARCH'])."'";
		}else if($ARR['PRICE_START_SEARCH'] != '' ){
			$tmpKeyword .= "AND NB.NB_TOTAL_PRICE >= '".$ARR['PRICE_START_SEARCH']."' \n";
		}else if($ARR['PRICE_END_SEARCH'] != ''){
			$tmpKeyword .= "AND NB.NB_TOTAL_PRICE <= '".$ARR['PRICE_END_SEARCH']."' \n";
		}

		//사유상세
		if($ARR['NBI_REASON_DTL_ID_SEARCH'] != '') {
			$tmpKeyword .= "AND NBI.`NBI_REASON_DTL_ID` IN ( ";
            foreach ($ARR['NBI_REASON_DTL_ID_SEARCH'] as $index => $DTL_ID) {
                $tmpKeyword .=" '".$DTL_ID."',";
            }
            $tmpKeyword = substr($tmpKeyword, 0, -1); // 마지막 콤마제거
            $tmpKeyword .= ") \n";
		}

		//전송여부
		if($ARR['NBI_SEND_FAX_YN_SEARCH'] != '') {
			$tmpKeyword .= " AND `NBI`.`RFS_ID` = '".addslashes($ARR['NBI_SEND_FAX_YN_SEARCH'])."'";
		} 

		//종결여부
		if($ARR['NBI_INSU_DONE_YN_SEARCH'] != '') {
			$tmpKeyword .= "AND NBI.NBI_INSU_DONE_YN = '".addslashes($ARR['NBI_INSU_DONE_YN_SEARCH'])."' ";	
		} 

		//사유입력자 검색 
		if($ARR['NBI_REASON_USER_NM_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(NT_BILL.GET_REQ_REASON_USER(NBI.RMT_ID, NBI.NBI_REASON_USERID),'".addslashes($ARR['NBI_REASON_USER_NM_SEARCH'])."')>0 ";	
		} 

		//사유처리여부
		if($ARR['NBI_PROC_YN_SEARCH'] != '') {
			if($ARR['NBI_PROC_YN_SEARCH']  == YN_Y){
				$tmpKeyword .= "AND NBI.NBI_PROC_USERID IS NOT NULL ";	
			}else {
				$tmpKeyword .= "AND NBI.NBI_PROC_USERID IS NULL ";	
			}
		} 

		//사유처리자 검색 
		if($ARR['NBI_PROC_USER_NM_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR( NT_AUTH.GET_USER_NM(NBI.NBI_PROC_USERID),'".addslashes($ARR['NBI_PROC_USER_NM_SEARCH'])."')>0 ";	
		} 

 

		// 페이징처리를 위해 전체데이터
		$iSQL = "SELECT 
                    COUNT(*) AS 'CNT'
				FROM
					NT_BILL.NBB_BILL NB,
					NT_BILL.NBB_BILL_INSU NBI,
					NT_MAIN.V_MGG_STATUS MG
				WHERE  
					NB.NB_ID = NBI.NB_ID
				AND NB.MGG_ID = MG.MGG_ID	
				AND NB.DEL_YN = 'N'
				AND NBI.RDET_ID NOT IN('40122', '40123', '40144')
					$tmpKeyword 
                    ;
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

		// 리스트 가져오기
		$iSQL = "SELECT 
					NB.MGG_ID,
					NB.NB_ID,
					NB.NB_CLAIM_DT,
					NBI.NBI_RMT_ID,
					NBI.MII_ID,
					`NT_MAIN`.`GET_INSU_NM`(NBI.MII_ID) AS 'MII_NM',
					NBI.NIC_ID,
					`NT_MAIN`.`GET_INSU_CHG_NM`(NBI.NIC_ID, '".AES_SALT_KEY."') AS 'NIC_NM',
					`NT_MAIN`.`GET_INSU_CHG_PHONE`(NBI.`NIC_ID`, '".AES_SALT_KEY."') AS 'NIC_TEL1_AES',
					IFNULL(`NT_MAIN`.`GET_INSU_CHG_TEAM_NM`(NBI.NIC_ID),IFNULL(NT_MAIN.GET_MIT_NM_BY_CRR(NBI.MII_ID, NB.MGG_ID),'')) AS 'MIT_NM',
					`NT_CODE`.`GET_CSD_NM`(MG.CSD_ID) AS 'CSD_NM',
					`NT_CODE`.`GET_CRR_NM`(MG.CRR_1_ID,MG.CRR_2_ID,MG.CRR_3_ID) AS 'CRR_NM',
					MG.MGG_NM,
					MG.MGG_BIZ_ID,
					MG.RET_ID,
					`NT_CODE`.`GET_CODE_NM`(MG.RET_ID) AS 'RET_NM',
					NBI.NBI_REGI_NUM,
					NB.RLT_ID,
					`NT_CODE`.`GET_CODE_NM`(NB.RLT_ID) AS 'RLT_NM',
					NB.NB_CAR_NUM,
					NB.CCC_ID,
					`NT_CODE`.`GET_CAR_TYPE`(NB.CCC_ID) AS 'CCC_TYPE',
					`NT_CODE`.`GET_CAR_NM`(NB.CCC_ID) AS 'CCC_NM',
					$NB_RATIO_PRICE AS NB_TOTAL_PRICE,
					NBI.NBI_REASON_DT,
					NBI.NBI_REASON_USERID,
					NBI.RMT_ID AS 'REASON_RMT_ID',
					NT_CODE.GET_CODE_NM(NBI.RMT_ID) AS 'REASON_RMT_NM', # 사유입력(주체)
					(SELECT USER_NM FROM `NT_MAIN`.`V_ALL_USER_INFO` WHERE USERID = NBI.NBI_REASON_USERID LIMIT 1) AS 'NBI_REASON_USER_NM',
					NBI.RDET_ID,
					NBI.NBI_REASON_DTL_ID AS 'NBI_REASON_DTL_ID',
					NBI.NBI_INSU_DONE_YN AS 'NBI_INSU_DONE_YN',
					`NT_BILL`.`GET_REASSON_KIND`(NBI.`NBI_REASON_DTL_ID`) AS 'NBI_REASON_NM',
					NT_CODE.GET_CODE_NM(NBI.`NBI_REASON_DTL_ID`) AS 'NBI_REASON_DTL_NM',
					IFNULL(`NT_MAIN`.`GET_INSU_NM`(NBI.NBI_OTHER_MII_ID), '') AS 'NBI_OTHER_MII_NM', #타보험사
					IFNULL(NBI.NBI_OTHER_MII_ID, '') AS 'NBI_OTHER_MII_ID',#
					IFNULL(NBI.NBI_OTHER_RATIO, '') AS 'NBI_OTHER_RATIO',#과실율
					IFNULL(NBI.NBI_OTHER_DT, '') AS 'NBI_OTHER_DT',#날짜
					IFNULL(NBI.NBI_OTHER_PRICE, '') AS 'NBI_OTHER_PRICE', #금액
					IFNULL(NBI.NBI_OTHER_MEMO, '') AS 'NBI_OTHER_MEMO', #금액
					`NT_BILL`.`GET_NBI_OTHER_MEMO`(NB.NB_ID,NBI.NBI_RMT_ID,TRUE) AS 'HQ_MEMO',#메모
                    `NT_BILL`.`GET_NBI_OTHER_MEMO`(NB.NB_ID,NBI.NBI_RMT_ID,FALSE) AS 'IW_MEMO',#메모
					NBI.NBI_PROC_REASON_DT AS 'NBI_PROC_REASON_DT',

					IFNULL(NT_AUTH.GET_USER_NM(NBI.NBI_PROC_USERID), '') AS NBI_PROC_NM,
					NBI.NBI_PROC_USERID AS 'NBI_PROC_USERID', #사유처리(주체)
					NBI.NBI_REQ_REASON_YN AS 'NBI_REQ_REASON_YN',
					NBI.RFS_ID,
					`NT_CODE`.`GET_CODE_NM`(NBI.RFS_ID) AS RFS_NM,
					NBI.`NBI_INSU_DEPOSIT_DT` AS 'NBI_INSU_DEPOSIT_DT',
					NBI.`NBI_INSU_DEPOSIT_PRICE` AS 'NBI_INSU_DEPOSIT_PRICE',
					`NT_BILL`.`GET_DEPOSIT_YN`(NBI.RDET_ID) AS 'NBI_INSU_DEPOSIT_YN'
				FROM
					NT_BILL.NBB_BILL NB,
					NT_BILL.NBB_BILL_INSU NBI,
					NT_MAIN.V_MGG_STATUS MG
				WHERE  
					NB.NB_ID = NBI.NB_ID
				AND NB.MGG_ID = MG.MGG_ID	
				AND NB.DEL_YN = 'N'
				AND NBI.RDET_ID NOT IN('40122', '40123', '40144')
				$tmpKeyword 
				ORDER BY `NBI`.`NBI_REASON_DT` DESC, `NB`.`WRT_DTHMS` DESC 
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
					"NB_ID"=>$RS['NB_ID'],
					"NB_CLAIM_DT"=>$RS['NB_CLAIM_DT'],
					"NBI_RMT_ID"=>$RS['NBI_RMT_ID'],
					"MII_ID"=>$RS['MII_ID'],
					"MII_NM"=>$RS['MII_NM'],
					"NIC_ID"=>$RS['NIC_ID'],
					"NIC_TEL1_AES"=>$RS['NIC_TEL1_AES'],
					"NIC_NM"=>$RS['NIC_NM'],
					"MIT_NM"=>$RS['MIT_NM'],
					"CSD_NM"=>$RS['CSD_NM'],
					"CRR_NM"=>$RS['CRR_NM'],
					"MGG_NM"=>$RS['MGG_NM'],
					"MGG_BIZ_ID"=>$RS['MGG_BIZ_ID'],
					"RET_ID"=>$RS['RET_ID'],
					"RET_NM"=>$RS['RET_NM'],
					"NBI_REGI_NUM"=>$RS['NBI_REGI_NUM'],
					"RLT_ID"=>$RS['RLT_ID'],
					"RLT_NM"=>$RS['RLT_NM'],
					"NB_CAR_NUM"=>$RS['NB_CAR_NUM'],
					"CCC_ID"=>$RS['CCC_ID'],
					"CCC_TYPE"=>$RS['CCC_TYPE'],
					"CCC_NM"=>$RS['CCC_NM'],
					"NB_TOTAL_PRICE"=>$RS['NB_TOTAL_PRICE'],
					"NBI_REASON_DT"=>$RS['NBI_REASON_DT'],
					"NBI_REASON_USERID"=>$RS['NBI_REASON_USERID'],
					"REASON_RMT_ID"=>$RS['REASON_RMT_ID'],
					"REASON_RMT_NM"=>$RS['REASON_RMT_NM'],
					"NBI_REASON_USER_NM"=>$RS['NBI_REASON_USER_NM'],
					"RDET_ID"=>$RS['RDET_ID'],
					"NBI_REASON_DTL_ID"=>$RS['NBI_REASON_DTL_ID'],
					"NBI_INSU_DONE_YN"=>$RS['NBI_INSU_DONE_YN'],
					"NBI_REASON_NM"=>$RS['NBI_REASON_NM'],
					"NBI_REASON_DTL_NM"=>$RS['NBI_REASON_DTL_NM'],
					"NBI_OTHER_MII_NM"=>$RS['NBI_OTHER_MII_NM'],
					"NBI_OTHER_MII_ID"=>$RS['NBI_OTHER_MII_ID'],
					"NBI_OTHER_RATIO"=>$RS['NBI_OTHER_RATIO'],
					"NBI_OTHER_DT"=>$RS['NBI_OTHER_DT'],
					"NBI_OTHER_PRICE"=>$RS['NBI_OTHER_PRICE'],
					"NBI_OTHER_MEMO"=>$RS['NBI_OTHER_MEMO'],
					"HQ_MEMO"=>$RS['HQ_MEMO'],
					"IW_MEMO"=>$RS['IW_MEMO'],
					"NBI_PROC_REASON_DT"=>$RS['NBI_PROC_REASON_DT'],
					"NBI_PROC_NM"=>$RS['NBI_PROC_NM'],
					"NBI_PROC_USERID"=>$RS['NBI_PROC_USERID'],
					"NBI_REQ_REASON_YN"=>$RS['NBI_REQ_REASON_YN'],
					"RFS_ID"=>$RS['RFS_ID'],
					"RFS_NM"=>$RS['RFS_NM'],
					"NBI_INSU_DEPOSIT_DT"=>$RS['NBI_INSU_DEPOSIT_DT'],
					"NBI_INSU_DEPOSIT_PRICE"=>$RS['NBI_INSU_DEPOSIT_PRICE'],
					"NBI_INSU_DEPOSIT_YN"=>$RS['NBI_INSU_DEPOSIT_YN']
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
				   `NBI_REASON_USERID` = '".$_SESSION['USERID']."', ";
	   
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
					`WRT_USERID` = '".$_SESSION['USERID']."'
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

	function setUMS($ARR){
		$iSQL = "INSERT INTO `NT_SEND`.`NSS_SEND`
					(`NS_ID`,
					`NS_SEND_TYPE`,
					`NS_SENDER`,
					`NS_RECEIVER`,
					`NS_RECEIVER_NM`,
					`NS_SUBJECT`,
					`NS_CONTENT`,
					`NS_REQ_SEND_DTHMS`,
					`NS_ADS_YN`,
					`LAST_DTHMS`,
					`WRT_DTHMS`,
					`WRT_USERID`)
					VALUES
					(
					'".addslashes($ARR['SEND_ID'])."',
					'".addslashes($ARR['SEND_TYPE'])."',
					'".addslashes($ARR['NS_SENDER'])."',
					'".addslashes($ARR['NS_RECEIVER'])."',
					'".addslashes($ARR['NS_RECEIVER_NM'])."',
					'".addslashes($ARR['NS_SUBJECT'])."',
					'".addslashes($ARR['NS_CONTENT'])."',
					null,
					'N',
					NOW(),
					NOW(),
					'".$_SESSION['USERID']."'
					);
		
		";

		// echo "setUMS>>>".$iSQL;
		return DBManager::execQuery($iSQL);
	}



}
	// 0. 객체 생성
	$cExcept = new cExcept(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$cExcept->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
