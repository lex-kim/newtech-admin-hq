<?php

class clsSendList extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	// Class Object Constructor
	function clsSendList($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
		$this->DATA_SAVE_PATH = $DATA_SAVE_PATH;
		$this->ALLOW_FILE_EXT = $ALLOW_FILE_EXT;
	}

	function getSearchSQL($ARR){
		$tmpKeyword = "";

		/** 기간검색 */
		if($ARR['DT_KEY_SEARCH'] != '') {
			if($ARR['DT_KEY_SEARCH'] == 'NB_CLAIM_DT'){ //청구일
				if($ARR['START_DT'] != '' || $ARR['END_DT'] != '') {
					if($ARR['START_DT'] !='' && $ARR['END_DT'] !=''){
						$tmpKeyword .= " AND `NB`.`NB_CLAIM_DT` BETWEEN '".addslashes($ARR['START_DT'])."' AND '".addslashes($ARR['END_DT'])."'";
					}else if($ARR['START_DT'] !='' && $ARR['END_DT'] ==''){
						$tmpKeyword .= " AND `NB`.`NB_CLAIM_DT` >= '".addslashes($ARR['START_DT'])."'";
					}else{
						$tmpKeyword .= " AND `NB`.`NB_CLAIM_DT` <= '".addslashes($ARR['END_DT'])."'";
					}
				}
            }else if($ARR['DT_KEY_SEARCH'] == 'NB_AB_REG_DATE'){ // 수집일
                if($ARR['START_DT'] != '' || $ARR['END_DT'] != '') {
					if($ARR['START_DT'] !='' && $ARR['END_DT'] !=''){
						$tmpKeyword .= " AND `NB`.`NB_AB_REG_DATE` BETWEEN '".addslashes($ARR['START_DT'])."' AND '".addslashes($ARR['END_DT'])."'";
					}else if($ARR['START_DT'] !='' && $ARR['END_DT'] ==''){
						$tmpKeyword .= " AND `NB`.`NB_AB_REG_DATE` >= '".addslashes($ARR['START_DT'])."'";
					}else{
						$tmpKeyword .= " AND `NB`.`NB_AB_REG_DATE` <= '".addslashes($ARR['END_DT'])."'";
					}
				}
            }
		} 
		

		if($ARR['MGG_ID_SEARCH'] != '') {
			$tmpKeyword .= " AND INSTR(`NB`.`MGG_ID`,'".addslashes($ARR['MGG_ID_SEARCH'])."')>0 ";
		}
		if($ARR['MII_ID_SEARCH'] != '') {
			$tmpKeyword .= " AND `NBI`.`MII_ID` IN (";
            foreach ($ARR['MII_ID_SEARCH'] as $index => $MII_ARRAY) {
                $tmpKeyword .=" '".$MII_ARRAY."',";
            }
            $tmpKeyword=substr($tmpKeyword, 0, -1); // 마지막 콤마제거
            $tmpKeyword .= ") \n";
		}
		if($ARR['MII_ID_TEAM_SEARCH'] != '') {
			$tmpKeyword .= " AND (CONCAT(`NBI`.`MII_ID`,MGG.CRR_1_ID,MGG.CRR_2_ID,MGG.CRR_3_ID) IN 
									(
										SELECT 
											CONCAT(
												`NT_MAIN`.`GET_INSU_ID_BY_MIT`(MIT_ID),
												CRR_1_ID,
												CRR_2_ID,
												CRR_3_ID
											)
										FROM 
											NT_MAIN.NMI_INSU_TEAM_REGION
										WHERE MIT_ID IN (";
            foreach ($ARR['MII_ID_TEAM_SEARCH'] as $index => $MIT_ARRAY) {
                $tmpKeyword .=" '".$MIT_ARRAY."',";
            }
            $tmpKeyword=substr($tmpKeyword, 0, -1); // 마지막 콤마제거
			$tmpKeyword .= " 	) 
				) OR (`NT_MAIN`.`GET_INSU_CHG_TEAM_ID`(NBI.NIC_ID) IN (";
			foreach ($ARR['MII_ID_TEAM_SEARCH'] as $index => $MIT_ARRAY) {
				$tmpKeyword .=" '".$MIT_ARRAY."',";
			}
			$tmpKeyword=substr($tmpKeyword, 0, -1); // 마지막 콤마제거
			$tmpKeyword .= ") \n  )\n  )\n";
		}

		if($ARR['NB_AB_BILL_SEARCH'] != ''){
			if($ARR['NB_AB_BILL_SEARCH'] == 'Y' || $ARR['NB_AB_BILL_SEARCH'] == 'N'){
				$tmpKeyword .= " AND NB.NB_AB_BILL_YN = '".addslashes($ARR['NB_AB_BILL_SEARCH'])."'";
			}else{
				$tmpKeyword .= " AND NB.NB_AB_BILL_YN IS NULL";
			}

		}

		if($ARR['MIT_MEMO_SEARCH'] != '') {
			$tmpKeyword .= " AND INSTR(`NBI`.`NBI_TEAM_MEMO`,'".addslashes($ARR['MIT_MEMO_SEARCH'])."')>0 ";
		}
		if($ARR['NBI_CHG_NM_SEARCH'] != '') {
			$tmpKeyword .= " AND INSTR(`NBI`.`NBI_CHG_NM`,'".addslashes($ARR['NBI_CHG_NM_SEARCH'])."')>0 ";
		}
		if($ARR['NB_CLAIM_USERID_SEARCH'] != '') {
			$tmpKeyword .= " AND INSTR(`NB`.`RMT_ID`,'".addslashes($ARR['NB_CLAIM_USERID_SEARCH'])."')>0";
			// if($ARR['NB_CLAIM_USERID_SEARCH'] == MODE_TARGET_ALL){ //본사
			// 	$tmpKeyword .= "AND  (SELECT MODE_TARGET FROM `NT_MAIN`.`V_ALL_USER_INFO` WHERE USERID = NB.NB_CLAIM_USERID) IS NULL";
			// }else {
			// 	$tmpKeyword .= "AND  (SELECT MODE_TARGET FROM `NT_MAIN`.`V_ALL_USER_INFO` WHERE USERID = NB.NB_CLAIM_USERID) = '".addslashes($ARR['NB_CLAIM_USERID_SEARCH'])."' ";

			// }
			// $tmpKeyword .= "AND INSTR(`NB`.`NB_CLAIM_USERID`,'".addslashes($ARR['NB_CLAIM_USERID_SEARCH'])."')>0 ";
		}
		if($ARR['CSD_ID_SEARCH'] != '') {
			$tmpKeyword .= " AND MGG.CSD_ID IN (";
            foreach ($ARR['CSD_ID_SEARCH'] as $index => $CSD_ARRAY) {
                $tmpKeyword .=" '".$CSD_ARRAY."',";
            }
            $tmpKeyword=substr($tmpKeyword, 0, -1); // 마지막 콤마제거
            $tmpKeyword .= ") \n";
		}
		if($ARR['CRR_ID_SEARCH'] != '') {
			$tmpKeyword .= " AND (";
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
                    $tmpKeyword .= " AND `MGG`.`CRR_2_ID` = '".$CSD[1]."' \n";
                }

                if($CSD[2] != NOT_CRR_3){
                    $tmpKeyword .= " AND `MGG`.`CRR_3_ID` = '".$CSD[2]."' \n";
                }

                $tmpKeyword .= ") \n";
            }
            $tmpKeyword .= ") \n";
		}
		if($ARR['MGG_NM_SEARCH'] != '') {
			// $tmpKeyword .= "AND INSTR(`MGG`.`MGG_ID`,'".addslashes($ARR['MGG_NM_SEARCH'])."')>0";
			$tmpKeyword .= " AND `MGG`.`MGG_ID` IN ( ";
            foreach ($ARR['MGG_NM_SEARCH'] as $index => $ARR_MGG) {
                $tmpKeyword .=" '".$ARR_MGG."',";
            }
            $tmpKeyword = substr($tmpKeyword, 0, -1); // 마지막 콤마제거
            $tmpKeyword .= ") \n";
		}
		if($ARR['RET_ID_SEARCH'] != '') {
			$tmpKeyword .= " AND MGGS.RET_ID IN ( ";
            foreach ($ARR['RET_ID_SEARCH'] as $index => $RET_ID) {
                $tmpKeyword .=" '".$RET_ID."',";
            }
            $tmpKeyword = substr($tmpKeyword, 0, -1); // 마지막 콤마제거
            $tmpKeyword .= ") \n";
		}
		if($ARR['RPET_ID_SEARCH'] != '') {
			$tmpKeyword .= " AND INSTR(`MGGS`.`RPET_ID`,'".addslashes($ARR['RPET_ID_SEARCH'])."')>0";
		}
		if($ARR['MGG_FRONTIER_NM_SEARCH'] != '') {
			$tmpKeyword .= " AND INSTR(`MGGS`.`MGG_FRONTIER_NM`,'".addslashes($ARR['MGG_FRONTIER_NM_SEARCH'])."')>0";
		}
		if($ARR['RMT_ID_SEARCH'] != '') {
			$tmpKeyword .= " AND `NB`.`MODE_TARGET`  = '".addslashes($ARR['RMT_ID_SEARCH'])."'";
			// $tmpKeyword .= " AND  '".addslashes($ARR['RMT_ID_SEARCH'])."' IN (SELECT MODE_TARGET FROM `NT_MAIN`.`V_ALL_USER_INFO` WHERE USERID = NB.NB_CLAIM_USERID)";
			// $tmpKeyword .= " AND   (SELECT MODE_TARGET FROM `NT_MAIN`.`V_ALL_USER_INFO` WHERE USERID = NB.NB_CLAIM_USERID)  = '".addslashes($ARR['RMT_ID_SEARCH'])."'";
		}
		if($ARR['WRT_USERID_SEARCH'] != '') {
			$tmpKeyword .= " AND INSTR(`NB`.`WRT_USERID`,'".addslashes($ARR['WRT_USERID_SEARCH'])."')>0";

		}
		if($ARR['BOTH_INSU_SEARCH'] != '') {
			$tmpKeyword .= " AND `NT_BILL`.`GET_INSU_BOTH_WRITER`(NB.RMT_ID, NB.RLT_ID , NBI.NIC_ID, NB.NB_ID) = '".addslashes($ARR['BOTH_INSU_SEARCH'])."'";
		}
		if($ARR['NBI_PRECLAIM_YN_SEARCH'] != '') {
			$tmpKeyword .= " AND INSTR(`NBI`.`NBI_PRECLAIM_YN`,'".addslashes($ARR['NBI_PRECLAIM_YN_SEARCH'])."')>0";
		}
		if($ARR['RLT_ID_SEARCH'] != '') {
			$tmpKeyword .= " AND INSTR(`NB`.`RLT_ID`,'".addslashes($ARR['RLT_ID_SEARCH'])."')>0";
		}
		if($ARR['RCT_CD_SEARCH'] != '') {
			$tmpKeyword .= " AND (SELECT CCC_CAR_TYPE_CD FROM NT_CODE.NCC_CAR WHERE CCC_ID = NB.CCC_ID) = '".addslashes($ARR['RCT_CD_SEARCH'])."'";
		}
		if($ARR['CPPS_ID_SEARCH'] != '') {
			$tmpKeyword .= " AND INSTR(NT_BILL.GET_BILL_PART_ID(NB.NB_ID, false), '".$ARR['CPPS_ID_SEARCH']."') > 0 ";
		}

		if($ARR['START_WORK_TIME_SEARCH'] != '' || $ARR['END_WORK_TIME_SEARCH'] != '') {
			if($ARR['START_WORK_TIME_SEARCH'] !='' && $ARR['END_WORK_TIME_SEARCH'] !=''){
				$tmpKeyword .= " AND IFNULL(NT_BILL.GET_NB_WORK_TIME(NB.NB_ID),0) BETWEEN '".addslashes($ARR['START_WORK_TIME_SEARCH'])."' AND '".addslashes($ARR['END_WORK_TIME_SEARCH'])."'";
			}else if($ARR['START_WORK_TIME_SEARCH'] !='' && $ARR['END_WORK_TIME_SEARCH'] ==''){
				$tmpKeyword .= " AND IFNULL(NT_BILL.GET_NB_WORK_TIME(NB.NB_ID),0) >= '".addslashes($ARR['START_WORK_TIME_SEARCH'])."'";
			}else{
				$tmpKeyword .= " AND IFNULL(NT_BILL.GET_NB_WORK_TIME(NB.NB_ID),0) <= '".addslashes($ARR['END_WORK_TIME_SEARCH'])."'";
			}

		}

		if($ARR['RFS_ID_SEARCH'] != '') {
			$tmpKeyword .= " AND `NBI`.`RFS_ID` = '".addslashes($ARR['RFS_ID_SEARCH'])."'";
		}
		if($ARR['NB_DUPLICATED_YN'] != '') {
			$tmpKeyword .= " AND `NB`.`NB_DUPLICATED_YN` = '".addslashes($ARR['NB_DUPLICATED_YN'])."'";
		}
		if($ARR['RDET_ID'] != '') {
			if($ARR['RDET_ID'] == YN_Y){
				$tmpKeyword .= " AND NBI.RDET_ID IN ('40122','40123','40124')";
			}else{
				$tmpKeyword .= " AND NBI.RDET_ID  = '40121'";
			}
		}
		if($ARR['NBI_INSU_DONE_YN'] != '') {
			$tmpKeyword .= " AND `NBI`.`NBI_INSU_DONE_YN` = '".addslashes($ARR['NBI_INSU_DONE_YN'])."'";
		}
		if($ARR['NB_MEMO_TXT_SELECT'] != ''){
			if($ARR['NB_MEMO_TXT_SELECT']  == YN_Y){
				if($ARR['NB_MEMO_TXT'] != '') {
					$tmpKeyword .= " AND INSTR(`NB`.`NB_MEMO_TXT`,'".addslashes($ARR['NB_MEMO_TXT'])."')>0";
				}else {
					$tmpKeyword .= " AND (`NB`.`NB_MEMO_TXT` IS NOT NULL   AND `NB`.`NB_MEMO_TXT` <> '') ";

				}
			}else {
				$tmpKeyword .= " AND (`NB`.`NB_MEMO_TXT` IS NULL   OR `NB`.`NB_MEMO_TXT` = '') ";

			}

		}

		if($ARR['NBI_CAROWN_DEPOSIT_YN'] != '') {
			$tmpKeyword .= " AND `NBI`.`NBI_CAROWN_DEPOSIT_YN` = '".addslashes($ARR['NBI_CAROWN_DEPOSIT_YN'])."'";
		}
		if($ARR['CARINFO_SEARCH'] != '') {
			if($ARR['CARINFO_SEARCH']=='NBI_REGI_NUM'){
				$tmpKeyword .= " AND INSTR(`NBI`.`NBI_REGI_NUM`,'".addslashes($ARR['CARINFO_TEXT_SEARCH'])."')>0";
			}else if($ARR['CARINFO_SEARCH']=='NB_CAR_NUM'){
				$tmpKeyword .= " AND INSTR(`NB`.`NB_CAR_NUM`,'".addslashes($ARR['CARINFO_TEXT_SEARCH'])."')>0";
			}else if($ARR['CARINFO_SEARCH']=='CCC_ID'){
				$tmpKeyword .= " AND INSTR(`NT_CODE`.`GET_CAR_NM`(NB.CCC_ID) , '".addslashes($ARR['CARINFO_TEXT_SEARCH'])."')>0";
			}else{
				return ERROR;
			}
		}

		return $tmpKeyword ;
	}

	/**
	 * 리스트조회
	 */
	function getList($ARR) {
		$LimitStart = ($ARR['CURR_PAGE']-1) * $ARR['PER_PAGE'];
		$LimitEnd   = $ARR['PER_PAGE'];

		$tmpKeyword = $this->getSearchSQL($ARR);

		// 페이징처리를 위해 전체데이터
		$iSQL = "SELECT
					COUNT(MGG.MGG_ID) AS CNT

				FROM
						NT_MAIN.NMG_GARAGE MGG,
						NT_MAIN.NMG_GARAGE_SUB MGGS,
						NT_BILL.NBB_BILL NB,
						NT_BILL.NBB_BILL_INSU NBI

				WHERE NB.NB_ID = NBI.NB_ID
				AND NB.DEL_YN = 'N'
				AND MGG.DEL_YN = 'N'
				AND MGG.MGG_ID = NB.MGG_ID
				AND MGGS.MGG_ID = NB.MGG_ID
				$tmpKeyword
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


		if(!empty($ARR['SORT_TYPE'])){
			$ORDERBY_SQL = "ORDER BY INSU_TEAM_NM ".$ARR['SORT_TYPE'];
		}else{
			$ORDERBY_SQL = "ORDER BY NB.NB_CLAIM_DTHMS DESC, NB.LAST_DTHMS DESC, NB_DUPLICATED_YN";
			// $ORDERBY_SQL = "ORDER BY NB.LAST_DTHMS DESC";
		}
		$NB_RATIO_PRICE = "TRUNCATE(IFNULL(NB.`NB_TOTAL_PRICE`,'0')*IF(NBI.`NBI_RATIO`<>'',CAST(NBI.`NBI_RATIO` AS DECIMAL)/100,1),-1)" ;

		$iSQL = "SELECT
					MGG.MGG_ID,
					MGGS.`MGG_OFFICE_FAX`,
					NB.NB_ID,
					NB.NB_CLAIM_DT,
					IFNULL(NB.NB_AB_REG_DATE,'-') AS NB_AB_REG_DATE,
					NB.NB_INC_DTL_YN,
					NB.NB_MEMO_TXT,
					NBI.NBI_RMT_ID,
					NB.RLT_ID,
					IFNULL(NBI.NIC_ID, '') AS NIC_ID,
					NBI.MII_ID,
					NT_MAIN.GET_INSU_NM(NBI.MII_ID) AS MII_NM,
					IFNULL(NT_MAIN.GET_INSU_CHG_TEAM_NM(NBI.NIC_ID),IFNULL(NT_MAIN.GET_MIT_NM_BY_CRR(NBI.MII_ID, MGG.MGG_ID),'')) AS INSU_TEAM_NM,
					IFNULL(NT_MAIN.GET_INSU_CHG_TEAM_MEMO(NBI.NIC_ID),'') AS INSU_TEAM_MEMO,
					IFNULL(NBI.NBI_TEAM_MEMO,'')AS NBI_TEAM_MEMO,
					NBI.NBI_CHG_NM,
					NB.NB_CLAIM_USERID,
					IFNULL(`NT_MAIN`.`GET_CLAIM_USER`(NB.RMT_ID, NB.`WRT_USERID`),'') AS 'NB_CLAIM_USER_NM',
					(SELECT RMT_NM FROM NT_CODE.REF_CLAIMANT_TYPE WHERE RMT_ID = NB.RMT_ID)AS CLAIM_TYPE,
					NB.NB_DUPLICATED_YN,
					NBI.NBI_FAX_NUM,
					IFNULL(NT_MAIN.GET_INSU_CHG_PHONE(NBI.NIC_ID, '".AES_SALT_KEY."'),'') AS INSU_CHG_PHONE,
					NT_CODE.GET_CSD_NM(MGG.CSD_ID) AS CSD_NM,
					NT_CODE.GET_CRR_NM(MGG.CRR_1_ID,MGG.CRR_2_ID,'00000') AS CRR_NM,
					NT_MAIN.GET_MGG_NM(NB.MGG_ID)AS MGG_NM,
					-- NB.NB_GARAGE_NM AS MGG_NM,
					MGG.MGG_BIZ_ID,
					IFNULL(NT_CODE.GET_RET_NM(MGGS.RET_ID),'') AS RET_NM,
					NT_CODE.GET_RPET_NM(MGGS.RPET_ID) AS RPET_NM,
					MGGS.MGG_FRONTIER_NM,
					MGGS.MGG_BIZ_START_DT,
					IFNULL(MGGS.MGG_BIZ_END_DT,'') AS MGG_BIZ_END_DT,
					CASE NB.MODE_TARGET
						WHEN '10102'
						THEN '공'
						WHEN '10103'
						THEN '보'
						ELSE 
							'본'
					END AS RMT_NM,
					-- IFNULL(SUBSTRING(  IF(NT_CODE.GET_CODE_NM((SELECT MODE_TARGET FROM `NT_MAIN`.`V_ALL_USER_INFO` WHERE USERID = NB.NB_CLAIM_USERID  LIMIT 1))='전체',
                    --                             '본',
					-- NT_CODE.GET_CODE_NM((SELECT MODE_TARGET FROM `NT_MAIN`.`V_ALL_USER_INFO` WHERE USERID = NB.NB_CLAIM_USERID  LIMIT 1))) ,1,1),'') AS 'RMT_NM',
					NB.WRT_USERID,
					IFNULL(NT_MAIN.GET_INSU_NM(NT_BILL.GET_INSU_BOTH_WRITER(NB.RMT_ID, NB.RLT_ID , NBI.NIC_ID, NB.NB_ID)),'-') AS BOTH_INSU_NM,
					NBI.NBI_PRECLAIM_YN,
					NBI.NBI_REGI_NUM,
					NT_CODE.GET_RLT_NM(NB.RLT_ID) AS RLT_NM,
					NB.NB_CAR_NUM,
					NT_CODE.GET_CAR_NM(NB.CCC_ID) AS CCC_NM,
					NT_CODE.GET_CAR_TYPE_NM(NB.CCC_ID) AS CAR_TYPE_NM,
					IFNULL(NT_BILL.GET_BILL_PART_NMS(NB.NB_ID,false,false),'') AS PARTS_NM,
					IFNULL(NT_BILL.GET_NB_WORK_TIME(NB.NB_ID),0) AS WORK_TIME,
					$NB_RATIO_PRICE  AS NB_TOTAL_PRICE,
					IFNULL(NBI.NBI_INSU_DEPOSIT_DT,'-') AS NBI_INSU_DEPOSIT_DT,
					IFNULL(NBI.NBI_INSU_DEPOSIT_PRICE,0) AS NBI_INSU_DEPOSIT_PRICE,
					IFNULL(NBI.NBI_FAX_SEND_DTHMS,'-') AS NBI_FAX_SEND_DTHMS,
					IFNULL(NBI.NBI_GW_FAX_DTHMS,'-') AS NBI_GW_FAX_DTHMS,
					IF(NBI.NBI_RMT_ID = '11520' , NT_BILL.GET_OWNER_CLAIM_PRICE(NB.RLT_ID, NB.NB_ID, NB.NB_NEW_INSU_YN), 0) AS OWNER_PRICE,
					IFNULL(NBI.NBI_CAROWN_DEPOSIT_DT,'-')AS NBI_CAROWN_DEPOSIT_DT,
					IFNULL(NBI.NBI_CAROWN_DEPOSIT_PRICE,0) AS NBI_CAROWN_DEPOSIT_PRICE,
					ROUND(( 100 - ( ( IFNULL(NBI.`NBI_INSU_DEPOSIT_PRICE`,0) + IFNULL(NBI.`NBI_INSU_ADD_DEPOSIT_PRICE`,0) + IFNULL(NBI.`NBI_CAROWN_DEPOSIT_PRICE`,0)) / ($NB_RATIO_PRICE * IF(NB.`NB_VAT_DEDUCTION_YN` = 'Y', 0.9,1)*IF(NB.`NB_NEW_INSU_YN` = 'Y',0.8,1) )* 100) ),2) AS 'DC_RATIO',
					IFNULL(NT_CODE.GET_RDET_NM(NBI.RDET_ID),'-')AS RDET_NM,
					NBI.RFS_ID,
					NB.NB_AB_VIEW_URI,
					NT_BILL.GET_RFS_NM(NBI.RFS_ID) AS RFS_NM,
					NBI.NBI_CAROWN_DEPOSIT_YN,
					CASE
						WHEN NB.NB_AB_BILL_YN = 'Y'
							THEN '청구'
						WHEN NB.NB_AB_BILL_YN = 'N'
							THEN '미청구'
						ELSE
							 '미구분'
					END AS NB_AB_BILL_TYPE,
					NB.LAST_DTHMS
			FROM
					NT_MAIN.NMG_GARAGE MGG,
					NT_MAIN.NMG_GARAGE_SUB MGGS,
					NT_BILL.NBB_BILL NB,
					NT_BILL.NBB_BILL_INSU NBI

			WHERE NB.NB_ID = NBI.NB_ID
			AND NB.DEL_YN = 'N'
			AND MGG.DEL_YN = 'N'
			AND MGG.MGG_ID = NB.MGG_ID
			AND MGGS.MGG_ID = NB.MGG_ID
			$tmpKeyword
			$ORDERBY_SQL
			$LIMIT_SQL
			;"

		;
		// echo $iSQL;
		$Result = DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"IDX"=>$IDX--,
					"NB_ID"=>$RS['NB_ID'],
					"MII_ID"=>$RS['MII_ID'],
					"NIC_ID"=>$RS['NIC_ID'],
					"MGG_ID"=>$RS['MGG_ID'],
					"MGG_OFFICE_FAX"=>$RS['MGG_OFFICE_FAX'],
					"NB_INC_DTL_YN"=>$RS['NB_INC_DTL_YN'],
					"RLT_ID"=>$RS['RLT_ID'],
					"NBI_RMT_ID"=>$RS['NBI_RMT_ID'],
					"NB_CLAIM_DT"=>$RS['NB_CLAIM_DT'],
					"NB_AB_REG_DATE"=>$RS['NB_AB_REG_DATE'],
					"NB_MEMO_TXT"=>$RS['NB_MEMO_TXT'],
					"MII_NM"=>$RS['MII_NM'],
					"INSU_TEAM_NM"=>$RS['INSU_TEAM_NM'],
					"NBI_TEAM_MEMO"=>$RS['NBI_TEAM_MEMO'],
					"NBI_CHG_NM"=>$RS['NBI_CHG_NM'],
					"CLAIM_TYPE"=>$RS['CLAIM_TYPE'],
					"NB_CLAIM_USER_NM"=>$RS['NB_CLAIM_USER_NM'],
					"NB_CLAIM_USERID"=>$RS['NB_CLAIM_USERID'],
					"NB_DUPLICATED_YN"=>$RS['NB_DUPLICATED_YN'],
					"NBI_FAX_NUM"=>$RS['NBI_FAX_NUM'],
					"INSU_CHG_PHONE"=>$RS['INSU_CHG_PHONE'],
					"CSD_NM"=>$RS['CSD_NM'],
					"CRR_NM"=>$RS['CRR_NM'],
					"MGG_NM"=>$RS['MGG_NM'],
					"MGG_BIZ_ID"=>$RS['MGG_BIZ_ID'],
					"RET_NM"=>$RS['RET_NM'],
					"RPET_NM"=>$RS['RPET_NM'],
					"MGG_FRONTIER_NM"=>$RS['MGG_FRONTIER_NM'],
					"MGG_BIZ_START_DT"=>$RS['MGG_BIZ_START_DT'],
					"MGG_BIZ_END_DT"=>$RS['MGG_BIZ_END_DT'],
					"RMT_NM"=>$RS['RMT_NM'],
					"WRT_USERID"=>$RS['WRT_USERID'],
					"BOTH_INSU_NM"=>$RS['BOTH_INSU_NM'],
					"NBI_PRECLAIM_YN"=>$RS['NBI_PRECLAIM_YN'],
					"NBI_REGI_NUM"=>$RS['NBI_REGI_NUM'],
					"RLT_NM"=>$RS['RLT_NM'],
					"NB_CAR_NUM"=>$RS['NB_CAR_NUM'],
					"CCC_NM"=>$RS['CCC_NM'],
					"CAR_TYPE_NM"=>$RS['CAR_TYPE_NM'],
					"PARTS_NM"=>$RS['PARTS_NM'],
					"WORK_TIME"=>$RS['WORK_TIME'],
					"NB_TOTAL_PRICE"=>$RS['NB_TOTAL_PRICE'],
					"NBI_INSU_DEPOSIT_DT"=>$RS['NBI_INSU_DEPOSIT_DT'],
					"NBI_INSU_DEPOSIT_PRICE"=>$RS['NBI_INSU_DEPOSIT_PRICE'],
					"DC_RATIO"=>$RS['DC_RATIO'],
					"NBI_FAX_SEND_DTHMS"=>$RS['NBI_FAX_SEND_DTHMS'],
					"NBI_GW_FAX_DTHMS"=>$RS['NBI_GW_FAX_DTHMS'],
					"OWNER_PRICE"=>$RS['OWNER_PRICE'],
					"NBI_CAROWN_DEPOSIT_DT"=>$RS['NBI_CAROWN_DEPOSIT_DT'],
					"NBI_CAROWN_DEPOSIT_PRICE"=>$RS['NBI_CAROWN_DEPOSIT_PRICE'],
					"RDET_NM"=>$RS['RDET_NM'],
					"RFS_NM"=>$RS['RFS_NM'],
					"RFS_ID"=>$RS['RFS_ID'],
					"NBI_CAROWN_DEPOSIT_YN"=>$RS['NBI_CAROWN_DEPOSIT_YN'],
					"NB_AB_BILL_TYPE"=>$RS['NB_AB_BILL_TYPE'],
					"NB_AB_VIEW_URI"=>$RS['NB_AB_VIEW_URI'],
					"LAST_DTHMS"=>$RS['LAST_DTHMS'],
			));
		}

		$Result->close();  // 자원 반납
		unset($RS);

		if(empty($rtnArray)){
			return HAVE_NO_DATA;
		}else{
			$jsonArray = array();
			array_push($jsonArray,$rtnArray);
			array_push($jsonArray,$TOTAL_LIST_COUNT);
			return $jsonArray;

		}
	}

	function onReleaseConfilctBill($ARR){
		$iSQL .= "UPDATE NT_BILL.NBB_BILL
				SET
					NB_DUPLICATED_YN = 'N',
					LAST_DTHMS = NOW()
				WHERE NB_ID = '".addslashes($ARR['NB_ID'])."'
				;"
		;
		// echo $iSQL;
		return DBManager::execQuery($iSQL);
	}

	function getSummary($ARR){
		$tmpKeyword = $this->getSearchSQL($ARR);
		$NB_RATIO_PRICE = "TRUNCATE(IFNULL(NB.`NB_TOTAL_PRICE`,'0')*IF(NBI.`NBI_RATIO`<>'',CAST(NBI.`NBI_RATIO` AS DECIMAL)/100,1),-1)" ;

		$iSQL = "SELECT
					COUNT(NB.NB_ID) AS 'CNT',
					ROUND(SUM(IF(NB.RLT_ID = '11430',1,0))) AS 'CNT_DOUBLE',	# 11430 쌍방
					SUM($NB_RATIO_PRICE ) AS 'PRICE',
					TRUNCATE(SUM(IFNULL(NB.`NB_TOTAL_PRICE`,'0')*IF(NBI.`NBI_RATIO`<>'',CAST(NBI.`NBI_RATIO` AS DECIMAL)/100,1)),-1) AS PRICE,
					SUM(IF(RFS_ID='90230',1,0)) AS 'SEND',
					SUM(IF(RFS_ID='90220',1,0)) AS 'SEND_READY',
					SUM(IF(RFS_ID='90210',1,0)) AS 'SEND_NOT',
					SUM(IF(RFS_ID='90290',1,0)) AS 'SEND_FAIL',
					SUM(IF(`NB_DUPLICATED_YN`='Y',1,0)) AS 'DUPLI'

				FROM
					`NT_BILL`.`NBB_BILL` NB,
					`NT_BILL`.`NBB_BILL_INSU` NBI,
					NT_MAIN.NMG_GARAGE MGG,
					NT_MAIN.NMG_GARAGE_SUB MGGS
				WHERE
					NB.`NB_ID` = NBI.`NB_ID`
				AND NB.DEL_YN = 'N'
				AND MGG.DEL_YN = 'N'
				AND MGG.MGG_ID = NB.MGG_ID
				AND MGGS.MGG_ID = NB.MGG_ID
				$tmpKeyword
		";
		// echo $iSQL;
		$RS= DBManager::getRecordSet($iSQL);	//검색조건 미반영

		if(!empty($RS['CNT'])){
			$rtnArray = array(
					"CNT"=>$RS['CNT'],
					"CNT_DOUBLE"=>$RS['CNT_DOUBLE'],
					"PRICE"=>$RS['PRICE'],
					"SEND"=>$RS['SEND'],
					"SEND_READY"=>$RS['SEND_READY'],
					"SEND_NOT"=>$RS['SEND_NOT'],
					"SEND_FAIL"=>$RS['SEND_FAIL'],
					"DUPLI"=>$RS['DUPLI']

			);
		}else{
			$rtnArray = array(
					"CNT"=>0,
					"CNT_DOUBLE"=>0,
					"PRICE"=>0,
					"SEND"=>0,
					"SEND_READY"=>0,
					"SEND_NOT"=>0,
					"SEND_FAIL"=>0,
					"DUPLI"=>0

			);
		}
		
		unset($RS);

		return  $rtnArray;
	}

	function delData($ARR){
		if(!empty($ARR['NB_DEL_REASON'])){
			foreach($ARR['NB_ID'] as $index => $NB_ID){
				$SESSION_ID = $_SESSION['USERID'] == null ? 'SYSYTEM' : $_SESSION['USERID'];
				$iSQL .= "UPDATE NT_BILL.NBB_BILL
						  SET
						  	DEL_YN = 'Y',
							NB_DEL_REASON = '".addslashes($ARR['NB_DEL_REASON'])."',
							NB_DEL_DTHMS = now(),
							NB_DEL_USERID = '".addslashes($SESSION_ID)."'
						  WHERE NB_ID = '".addslashes($NB_ID)."'
						;"
				;
				$iSQL .= "UPDATE NT_POINT.NPP_POINT_HISTORY
						SET
						NPH_CANCEL_YN = 'Y',
						NPH_CANCEL_DTHMS = now(),
						NPH_CANCEL_MEMO = '청구서 삭제',
						NPH_CANCEL_USERID = '".addslashes($SESSION_ID)."'
						WHERE NB_ID = '".addslashes($NB_ID)."'
					;"
				;
				$iSQL .= "DELETE FROM `NT_STOCK`.`NSI_IO_HISTORY`
						 WHERE NM_ID = '".addslashes($NB_ID)."'
						;"
				;
			}

			return DBManager::execMulti($iSQL);
		}
	}

	/**
	 * 해당데이터 조회
	 * @param {$ARR}
	 * @param {ALLM_ID : 조회할 해당 데이터 ID}
	 */
	function getData($ARR) {


	}



	/**
	 *
	 */
	function setData($ARR) {
		if($ARR['REQ_MODE'] == CASE_CREATE){

		}else{

		}
	}

	/** 팀재분류메모 */
	function setInsuTeamMemo($ARR){
		if(!empty($ARR['NB_ID'])){
			foreach($ARR['NB_ID'] as $index =>$NB_ID){
				$iSQL .= " UPDATE NT_BILL.NBB_BILL_INSU
						   SET NBI_TEAM_MEMO = '".addslashes($ARR['NBI_TEAM_MEMO'])."'
						   WHERE NB_ID = '".addslashes($NB_ID)."'
						;"
				;
			}
		}

		return DBManager::execMulti($iSQL);
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

	/** 공업사 발송 일시 저장 */
	function setGwFax($ARR){
		$iSQL = " UPDATE NT_BILL.NBB_BILL_INSU
				SET NBI_GW_FAX_DTHMS = NOW(),
					LAST_DTHMS = NOW()
				WHERE NB_ID = '".addslashes($ARR['NB_ID'])."' ;
		";

		// echo $iSQL;
		return DBManager::execQuery($iSQL);
	}

}
	// 0. 객체 생성
	$clsSendList = new clsSendList(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$clsSendList->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
