<?php

class cTrans extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	// Class Object Constructor
	function cTrans($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
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
		$RICT_WHERE  = "";  //  `NT_MAIN`.`NMG_GARAGE_INSU_CHARGE`  관련 쿼리

		$LimitStart = ($ARR['CURR_PAGE']-1) * $ARR['PER_PAGE'];
        $LimitEnd   = $ARR['PER_PAGE'];

		if($ARR['REQ_MODE'] == CASE_LIST){
			$LIMIT_SQL = "LIMIT $LimitStart,$LimitEnd";
        }
        
        
		//기간
		if($ARR['DT_KEY_SEARCH'] != '') {
			if($ARR['DT_KEY_SEARCH'] == 'NB_CLAIM_DT'){// 청구일
                if($ARR['START_DT_SEARCH'] != '' && $ARR['END_DT_SEARCH'] != ''){ 
                    $tmpKeyword .= "AND NB.NB_CLAIM_DT BETWEEN '".addslashes($ARR['START_DT_SEARCH'])."' AND '".addslashes($ARR['END_DT_SEARCH'])."' \n";
                }else if($ARR['START_DT_SEARCH'] != '' ){
                    $tmpKeyword .= "AND NB.NB_CLAIM_DT >= '".$ARR['START_DT_SEARCH']."' \n";
                }else if($ARR['END_DT_SEARCH'] != ''){
                    $tmpKeyword .= "AND NB.NB_CLAIM_DT <= '".$ARR['END_DT_SEARCH']."' \n";
                }
            }else if($ARR['DT_KEY_SEARCH'] == 'NBIT_TRANS_DT'){ // 이첩일
                if($ARR['START_DT_SEARCH'] != '' && $ARR['END_DT_SEARCH'] != ''){
                    $tmpKeyword .= "AND DATE_FORMAT(NBIT.WRT_DTHMS, '%Y-%m-%d') BETWEEN '".addslashes($ARR['START_DT_SEARCH'])."' AND '".addslashes($ARR['END_DT_SEARCH'])."' ";
                }else if($ARR['START_DT_SEARCH'] != '' ){
                    $tmpKeyword .= "AND DATE_FORMAT(NBIT.WRT_DTHMS, '%Y-%m-%d') >= '".$ARR['START_DT_SEARCH']."' \n";
                }else if($ARR['END_DT_SEARCH'] != ''){
                    $tmpKeyword .= "AND DATE_FORMAT(NBIT.WRT_DTHMS, '%Y-%m-%d') <= '".$ARR['END_DT_SEARCH']."' \n";
                }
            }
		} 

		//보험사(이첩전)
		if($ARR['NBIT_FROM_MII_ID_SEARCH'] !=''){
			$tmpKeyword .="AND NBIT_FROM_MII_ID = '".addslashes($ARR['NBIT_FROM_MII_ID_SEARCH'])."' ";
		}

		//보상담당자(이첩전)
		if($ARR['NBIT_FROM_NIC_ID_SEARCH'] != ''){
			$tmpKeyword .="AND INSTR(`NT_MAIN`.`GET_INSU_CHG_NM`(NBIT_FROM_NIC_ID,'".AES_SALT_KEY."'), '".addslashes($ARR['NBIT_FROM_NIC_ID_SEARCH'])."' ) > 0 \n";

		}

		//보험사(이첩후)
		if($ARR['NBIT_TO_MII_ID_SEARCH'] !=''){
			$tmpKeyword .="AND NBIT_TO_MII_ID = '".addslashes($ARR['NBIT_TO_MII_ID_SEARCH'])."'";
		}

		//보상담당자(이첩후)
		if($ARR['NBIT_TO_NIC_ID_SEARCH'] != ''){
			$tmpKeyword .="AND INSTR(`NT_MAIN`.`GET_INSU_CHG_NM`(NBIT_TO_NIC_ID,'".AES_SALT_KEY."'), '".addslashes($ARR['NBIT_TO_NIC_ID_SEARCH'])."' ) > 0 \n";

		}

		//담당구분검색
		if($ARR['RICT_ID_SEARCH'] != '') {
			$tmpKeyword .= " AND `NT_MAIN`.`GET_GARAGE_INSU_CHG`(NBI.NIC_ID,NB.MGG_ID, SUBSTRING(`NT_MAIN`.`GET_RICT_INFO`(NB.NB_TOTAL_PRICE,NB.CCC_ID,NB.MGG_ID,NBIT.NBIT_TO_NIC_ID) ,1,5)) IN ( ";	
			$RICT_WHERE .= " AND NBIC.RICT_ID IN ( ";
			foreach ($ARR['RICT_ID_SEARCH'] as $index => $RICT_ARRAY) {
                $tmpKeyword .=" '".$RICT_ARRAY."',";
                $RICT_WHERE .=" '".$RICT_ARRAY."',";
            }
            $tmpKeyword = substr($tmpKeyword, 0, -1); // 마지막 콤마제거
			$tmpKeyword .= " ) \n";
            $RICT_WHERE = substr($RICT_WHERE, 0, -1); // 마지막 콤마제거
			$RICT_WHERE .= " ) \n";
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
		if($ARR['MGG_NM_SEARCH'] != '') {
			$tmpKeyword .= "AND NB.MGG_ID  = '".$ARR['MGG_NM_SEARCH']."' \n";
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
			$tmpKeyword .= "AND INSTR(`NBI`.`NBI_REGI_NUM`,'".addslashes($ARR['NBI_REGI_NUM_SEARCH'])."')>0  \n";
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
		
		//이첩사유(메모)
		if($ARR['NBIT_MEMO_SEARCH'] != ''){
			$tmpKeyword .= "AND INSTR(NBIT.NBIT_MEMO, '".addslashes($ARR['NBIT_MEMO_SEARCH'])."')>0 \n";
		}
		

		//등록주체
		if($ARR['RMT_ID_SEARCH'] != ''){
			$tmpKeyword .= "AND (SELECT MODE_TARGET FROM `NT_MAIN`.`V_ALL_USER_INFO` WHERE USERID = NB.NB_CLAIM_USERID AND MODE_TARGET = '".addslashes($ARR['RMT_ID_SEARCH'])."') ";
		}
		//이첩횟수
		if($ARR['TRANS_CNT_SEARCH'] != '') {
			$tmpKeyword .= "AND `NT_BILL`.`GET_TRANS_CNT`(NB.NB_ID) = '".addslashes($ARR['TRANS_CNT_SEARCH'])."' \n";
		} 

		//전송여부
		if($ARR['NBI_SEND_FAX_YN_SEARCH'] != '') {
			$tmpKeyword .= " AND `NBI`.`RFS_ID` = '".addslashes($ARR['NBI_SEND_FAX_YN_SEARCH'])."' ";
		} 

		//지정일
		if($ARR['RICT_START_DT_SEARCH'] != '' && $ARR['RICT_END_DT_SEARCH'] != ''){
			$RICT_WHERE .= "AND DATE_FORMAT(NBIC.WRT_DTHMS, '%Y-%m-%d') BETWEEN '".addslashes($ARR['RICT_START_DT_SEARCH'])."' AND '".addslashes($ARR['RICT_END_DT_SEARCH'])."' ";
		}else if($ARR['RICT_START_DT_SEARCH'] != '' ){
			$RICT_WHERE .= "AND DATE_FORMAT(NBIC.WRT_DTHMS, '%Y-%m-%d') >= '".$ARR['RICT_START_DT_SEARCH']."' \n";
		}else if($ARR['RICT_END_DT_SEARCH'] != ''){
			$RICT_WHERE .= "AND DATE_FORMAT(NBIC.WRT_DTHMS, '%Y-%m-%d') <= '".$ARR['RICT_END_DT_SEARCH']."' \n";
		}


		//지정여부
		if($ARR['NGIC_CONFIRM_YN_SEARCH'] == YN_Y) {
			$tmpKeyword .= "AND `NT_MAIN`.`GET_RICT_INFO`(NB.NB_TOTAL_PRICE,NB.CCC_ID,NB.MGG_ID,NBIT.NBIT_TO_NIC_ID) IS NOT NULL \n";
		}else if($ARR['NGIC_CONFIRM_YN_SEARCH'] == YN_N) {
			$tmpKeyword .= "AND `NT_MAIN`.`GET_RICT_INFO`(NB.NB_TOTAL_PRICE,NB.CCC_ID,NB.MGG_ID,NBIT.NBIT_TO_NIC_ID) IS NULL \n";
		}  


		if($RICT_WHERE != ''){
			$JOIN_RICT = "JOIN `NT_MAIN`.`NMG_GARAGE_INSU_CHARGE` NBIC \n";
			$JOIN_RICT .= "ON NBIC.MGG_ID = NB.MGG_ID AND NBIT.NBIT_TO_NIC_ID = NBIC.NIC_ID \n";
			$HAVING_RICT = "HAVING RICT_INFO IS NOT NULL \n";
		}

 
		$mainSQL = $this->getListSQL($JOIN_RICT,$tmpKeyword,$RICT_WHERE,$HAVING_RICT) ; 

		// 페이징처리를 위해 전체데이터
		$iSQL = "SELECT 
					COUNT(*) AS 'CNT'
				FROM ( $mainSQL ) AS C ;
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

		// 리스트 가져오기
		$iSQL = $mainSQL.$LIMIT_SQL.";";

		// echo $iSQL;

		$Result= DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
                    "IDX"=>$IDX--,
					"NBIT_ID"=>$RS['NBIT_ID'],
					"MGG_ID"=>$RS['MGG_ID'],
					"NB_ID"=>$RS['NB_ID'],
					"NBI_RMT_ID"=>$RS['NBI_RMT_ID'],
					"MII_ID"=>$RS['MII_ID'],
					"MII_NM"=>$RS['MII_NM'],
					"NIC_ID"=>$RS['NIC_ID'],
					"NIC_NM"=>$RS['NIC_NM'],
					"NB_CLAIM_DT"=>$RS['NB_CLAIM_DT'],
					"NBIT_FROM_MII_ID"=>$RS['NBIT_FROM_MII_ID'],
					"NBIT_FROM_NIC_ID"=>$RS['NBIT_FROM_NIC_ID'],
					"NBIT_FROM_MII_NM"=>$RS['NBIT_FROM_MII_NM'],
					"NBIT_FROM_NIC_NM_AES"=>$RS['NBIT_FROM_NIC_NM_AES'],
					"NBIT_TO_MII_ID"=>$RS['NBIT_TO_MII_ID'],
					"NBIT_TO_MII_NM"=>$RS['NBIT_TO_MII_NM'],
					"NBIT_TO_NIC_ID"=>$RS['NBIT_TO_NIC_ID'],
					"NBIT_TO_NIC_NM_AES"=>$RS['NBIT_TO_NIC_NM_AES'],
					"NBIT_TO_NIC_PHONE_AES"=>$RS['NBIT_TO_NIC_PHONE_AES'],
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
					"CCC_TYPE"=>$RS['CCC_TYPE'],
					"CCC_ID"=>$RS['CCC_ID'],
					"CCC_NM"=>$RS['CCC_NM'],
					"TRANS_DT"=>$RS['TRANS_DT'],
					"NBIT_MEMO"=>$RS['NBIT_MEMO'],
					"RMT_NM"=>$RS['RMT_NM'],
					"TRANS_CNT"=>$RS['TRANS_CNT'],
					"RFS_ID"=>$RS['RFS_ID'],
					"RFS_NM"=>$RS['RFS_NM'],
					"NB_TOTAL_PRICE"=>$RS['NB_TOTAL_PRICE'],
					"RICT_INFO"=>$RS['RICT_INFO']
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

	function getListSQL($JOIN_RICT,$tmpKeyword,$RICT_WHERE,$HAVING_RICT){
		$iSQL = "SELECT 
					NBIT.NBIT_ID,
					NB.MGG_ID,
					NB.NB_ID,
					NB.NB_CLAIM_DT,
					NBI.NBI_RMT_ID,
					NBI.MII_ID,
					`NT_MAIN`.`GET_INSU_NM`(NBI.MII_ID) AS 'MII_NM',
					NBI.NIC_ID,
					`NT_MAIN`.`GET_INSU_CHG_NM`(NBI.NIC_ID, '".AES_SALT_KEY."') AS 'NIC_NM',
					NBIT.NBIT_FROM_MII_ID,
					`NT_MAIN`.`GET_INSU_NM`(NBIT.NBIT_FROM_MII_ID) AS 'NBIT_FROM_MII_NM',
					NBIT.NBIT_FROM_NIC_ID,
					CAST(AES_DECRYPT(`NBIT_FROM_NIC_NM_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS NBIT_FROM_NIC_NM_AES,
					NBIT.NBIT_TO_MII_ID,
					`NT_MAIN`.`GET_INSU_NM`(NBIT.NBIT_TO_MII_ID) AS 'NBIT_TO_MII_NM' ,
					NBIT.NBIT_TO_NIC_ID,
					CAST(AES_DECRYPT(`NBIT_TO_NIC_NM_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS NBIT_TO_NIC_NM_AES,
					CAST(AES_DECRYPT(`NBIT_TO_NIC_PHONE_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS NBIT_TO_NIC_PHONE_AES,
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
					DATE_FORMAT(NBIT.WRT_DTHMS, '%Y-%m-%d') AS 'TRANS_DT',
					NBIT.NBIT_MEMO,
					IFNULL(SUBSTRING(  IF(NT_CODE.GET_CODE_NM((SELECT MODE_TARGET FROM `NT_MAIN`.`V_ALL_USER_INFO` WHERE USERID = NB.NB_CLAIM_USERID  LIMIT 1))='전체',
														'본',
							NT_CODE.GET_CODE_NM((SELECT MODE_TARGET FROM `NT_MAIN`.`V_ALL_USER_INFO` WHERE USERID = NB.NB_CLAIM_USERID  LIMIT 1))) ,1,1),'') AS 'RMT_NM',
					`NT_BILL`.`GET_TRANS_CNT`(NB.NB_ID) AS 'TRANS_CNT',
					NBI.RFS_ID,
					`NT_CODE`.`GET_CODE_NM`(NBI.RFS_ID) AS RFS_NM,
					NB.NB_TOTAL_PRICE,
					`NT_MAIN`.`GET_RICT_INFO`(NB.NB_TOTAL_PRICE,NB.CCC_ID,NB.MGG_ID,NBIT.NBIT_TO_NIC_ID) AS 'RICT_INFO'
				FROM
					NT_BILL.NBB_BILL_INSU_TRANSFER NBIT
					JOIN NT_BILL.NBB_BILL NB
					ON NB.NB_ID = NBIT.NB_ID AND NB.DEL_YN = 'N'
					JOIN NT_BILL.NBB_BILL_INSU NBI
					ON NBIT.NB_ID = NBI.NB_ID
					JOIN NT_MAIN.V_MGG_STATUS MG
					ON NB.MGG_ID = MG.MGG_ID
					$JOIN_RICT
				WHERE  NBIT_ID IN (
							SELECT MAX(NBIT_ID)
							FROM NT_BILL.NBB_BILL_INSU_TRANSFER
				
							GROUP BY NB_ID
							)
							
				$tmpKeyword 
				$RICT_WHERE
				GROUP BY  NBIT.NB_ID
				$HAVING_RICT
                ORDER BY `NBI`.`LAST_DTHMS` DESC, `NB`.`NB_CLAIM_DTHMS` DESC 
                
		";
		return $iSQL;
	}
	
	/* 데이터생성*/
   function setData($ARR) {
	   
	   $iSQL = "REPLACE INTO `NT_MAIN`.`NMG_GARAGE_INSU_CHARGE`
				   (
							`MGG_ID`,
							`NIC_ID`,
							`RICT_ID`,
							`NGIC_CONFIRM_YN`,
							`WRT_DTHMS`,
							`LAST_DTHMS`,
							`WRT_USERID`
						)
						VALUES
						(
							'".addslashes($ARR['MGG_ID'])."',
							'".addslashes($ARR['NIC_ID'])."',
							'".addslashes($ARR['RICT_ID'])."',
							'".YN_Y."',
							now(),
							now(),
							'".$_SESSION['USERID']."'
						);
	   ";		
	   $iSQL .= "REPLACE INTO `NT_MAIN`.`NMI_INSU_CHARGE_TYPE`
				(
					`NIC_ID`,
					`RICT_ID`,
					`WRT_DTHMS`,
					`LAST_DTHMS`,
					`WRT_USERID`
				)
				VALUES
				(
					'".addslashes($ARR['NIC_ID'])."',
					'".addslashes($ARR['RICT_ID'])."',
					now(),
					now(),
					'".$_SESSION['USERID']."'
				) ;
		";
	   
	//    echo "setData SQL\n".$iSQL."\n";
	   return DBManager::execMulti($iSQL);
			   
   }

	/* 이첩이력 조회 */
	function getHistoryList($ARR){
		$LimitStart = ($ARR['CURR_PAGE']-1) * $ARR['PER_PAGE'];
		$LimitEnd   = $ARR['PER_PAGE'];

		/** 전체리스트가 아닌경우에만 limitSql 적용 */
		$LIMIT_SQL = "";
		if($ARR['REQ_MODE'] == CASE_LIST_SUB){
			$LIMIT_SQL = "LIMIT $LimitStart,$LimitEnd";
		}


		// 페이징처리를 위해 전체데이터
		$iSQL = "SELECT
					COUNT(*) AS CNT
				FROM  NT_BILL.NBB_BILL NB,
					NT_BILL.NBB_BILL_INSU NBI,
					NT_BILL.NBB_BILL_INSU_TRANSFER NBIT
				WHERE NB.DEL_YN = 'N'
				AND NB.NB_ID = NBIT.NB_ID
				AND NB.NB_ID = NBI.NB_ID
				AND NBIT.NB_ID = '".addslashes($ARR['NB_ID'])."' ;
		";
		// echo $iSQL."\n" ;

		try{
			$RS_T	= DBManager::getRecordSet($iSQL);
		} catch (Exception $e){
			echo "error : ".$e;
			return ERROR_DB_SELECT_DATA;   
		}

		$TOTAL_LIST_COUNT = $RS_T['CNT'];
		$IDX = $RS_T['CNT']-$LimitStart;
		unset($RS_T);


		$iSQL = "SELECT 
						`NBIT_ID`,
						NB.`NB_ID`,
						NB.`NB_CLAIM_DT`,
						`NT_MAIN`.`GET_MGG_NM`(NB.`MGG_ID`) AS 'MGG_NM',
						NB.`NB_CAR_NUM`,
						NBI.`NBI_REGI_NUM`,
						`NBIT_FROM_MII_ID`,
						`NT_MAIN`.`GET_INSU_NM`(`NBIT_FROM_MII_ID`) AS 'NBIT_FROM_MII_NM',
						`NBIT_FROM_NIC_ID`,
						CAST(AES_DECRYPT(`NBIT_FROM_NIC_NM_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS 'NBIT_FROM_NIC_NM_AES',
						CAST(AES_DECRYPT(`NBIT_FROM_NIC_FAX_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS 'NBIT_FROM_NIC_FAX_AES',
						`NBIT_TO_MII_ID`,
						`NT_MAIN`.`GET_INSU_NM`(`NBIT_TO_MII_ID`) AS 'NBIT_TO_MII_NM',
						`NBIT_TO_NIC_ID`,
						CAST(AES_DECRYPT(`NBIT_TO_NIC_NM_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS 'NBIT_TO_NIC_NM_AES',
						CAST(AES_DECRYPT(`NBIT_TO_NIC_FAX_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS 'NBIT_TO_NIC_FAX_AES',
						CAST(AES_DECRYPT(`NBIT_TO_NIC_PHONE_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS 'NBIT_TO_NIC_PHONE_AES',
						`NBIT_MEMO`,
						NBIT.`WRT_DTHMS`,
						NBIT.`LAST_DTHMS`,
						NBIT.`WRT_USERID`
				FROM NT_BILL.NBB_BILL NB,
					NT_BILL.NBB_BILL_INSU NBI,
					NT_BILL.NBB_BILL_INSU_TRANSFER NBIT
				WHERE NB.DEL_YN = 'N'
				AND NB.NB_ID = NBIT.NB_ID
				AND NB.NB_ID = NBI.NB_ID
				AND NBIT.NB_ID = '".addslashes($ARR['NB_ID'])."'
				ORDER BY NBIT.WRT_DTHMS DESC
				$LIMIT_SQL
				;
		";
		// echo $iSQL;
		$Result= DBManager::getResult($iSQL);
		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"IDX"=>$IDX--,
					"NBIT_ID"=>$RS['NBIT_ID'],
					"NB_ID"=>$RS['NB_ID'],
					"MGG_NM"=>$RS['MGG_NM'],
					"NB_CAR_NUM"=>$RS['NB_CAR_NUM'],
					"NBI_REGI_NUM"=>$RS['NBI_REGI_NUM'],
					"NB_CLAIM_DT"=>$RS['NB_CLAIM_DT'],
					"NBI_RMT_ID"=>$RS['NBI_RMT_ID'],
					"NBIT_FROM_MII_ID"=>$RS['NBIT_FROM_MII_ID'],
					"NBIT_FROM_MII_NM"=>$RS['NBIT_FROM_MII_NM'],
					"NBIT_FROM_NIC_ID"=>$RS['NBIT_FROM_NIC_ID'],
					"NBIT_FROM_NIC_NM"=>$RS['NBIT_FROM_NIC_NM'],
					"NBIT_FROM_NIC_NM_AES"=>$RS['NBIT_FROM_NIC_NM_AES'],
					"NBIT_FROM_NIC_FAX_AES"=>$RS['NBIT_FROM_NIC_FAX_AES'],
					"NBIT_TO_MII_ID"=>$RS['NBIT_TO_MII_ID'],
					"NBIT_TO_MII_NM"=>$RS['NBIT_TO_MII_NM'],
					"NBIT_TO_NIC_ID"=>$RS['NBIT_TO_NIC_ID'],
					"NBIT_TO_NIC_NM"=>$RS['NBIT_TO_NIC_NM'],
					"NBIT_TO_NIC_NM_AES"=>$RS['NBIT_TO_NIC_NM_AES'],
					"NBIT_TO_NIC_FAX_AES"=>$RS['NBIT_TO_NIC_FAX_AES'],
					"NBIT_TO_NIC_PHONE_AES"=>$RS['NBIT_TO_NIC_PHONE_AES'],
					"NBIT_MEMO"=>$RS['NBIT_MEMO'],
					"WRT_DTHMS"=>$RS['WRT_DTHMS'],
					"LAST_DTHMS"=>$RS['LAST_DTHMS'],
					"WRT_USERID"=>$RS['WRT_USERID']
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


	function getData() {
		$iSQL = "SELECT 
						PIE_LOW_PRICE,
						PIE_HIGH_PRICE 
					FROM NT_CODE.PCY_INSU_ENV;
		";
		return DBManager::getRecordSet($iSQL);
	}


}
	// 0. 객체 생성
	$cTrans = new cTrans(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$cTrans->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
