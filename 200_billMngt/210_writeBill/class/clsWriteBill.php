<?php

class clsWriteBill extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	// Class Object Constructor
	function clsWriteBill($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
		$this->DATA_SAVE_PATH = $DATA_SAVE_PATH;
		$this->ALLOW_FILE_EXT = $ALLOW_FILE_EXT;
	}

	/**
	 * MII_ID가 있을 경우는 중복검사
	 * 없을 경우는 5.작업선택 리스트
	 */
	function getList($ARR) {
		if(!empty($ARR['MII_ID'])){
			$MII_ID = "";
			$NB_REGI_NM = "";
			$BEFORE_MII_ID = "";
			$BEFORE_REGI_NUM = "";
			$LimitStart = ($ARR['CURR_PAGE']-1) * $ARR['PER_PAGE'];
			$LimitEnd   = $ARR['PER_PAGE'];

			$rtnMainArray = array();

			$NB_CAR_NUM = substr($ARR['NB_CAR_NUM'], -4);

			$SUB_SQL = empty($ARR['ORI_NB_ID']) ? '' : "AND NB.NB_ID != '".addslashes($ARR['ORI_NB_ID'])."'";
			foreach($ARR['MII_ID'] as $index => $MII_ITEM){
				if($BEFORE_MII_ID != $MII_ITEM && $BEFORE_REGI_NUM  != $ARR['NBI_REGI_NUM'][$index]){
					$NBI_REGI_NUM = mb_substr($ARR['NBI_REGI_NUM'][$index], -3);
					$iSQL = "SELECT
								COUNT(NBI.NB_ID) AS CNT

							FROM NT_BILL.NBB_BILL_INSU NBI,
								NT_BILL.NBB_BILL NB

							WHERE NBI.NB_ID = NB.NB_ID
							AND NBI.MII_ID = '".addslashes($MII_ITEM)."'
							AND NB.MGG_ID = '".addslashes($ARR['SELECT_NB_GARAGE_ID'])."'
							AND (
								  RIGHT(NBI.NBI_REGI_NUM, 3) = '".addslashes($NBI_REGI_NUM )."'
								OR RIGHT(NB.NB_CAR_NUM, 4) = '".addslashes($NB_CAR_NUM)."'
							 )
							$SUB_SQL
							AND NB.DEL_YN = 'N'
							;"

					;
					// echo $iSQL;
					$RS_T	= DBManager::getRecordSet($iSQL);
					$TOTAL_LIST_COUNT = $RS_T['CNT'];         // 총 몇개인지

					$LIMIT_SQL = "";
					if(!empty($ARR['PER_PAGE'])){
						$LIMIT_SQL = "LIMIT $LimitStart,$LimitEnd";
					}

					$iSQL = "SELECT
								DATE_FORMAT(NB.WRT_DTHMS,'%Y-%m-%d') AS WRT_DTHMS,
								NT_MAIN.GET_INSU_NM(NBI.MII_ID) AS MII_NM,
								NBI.NB_ID,
								NB.NB_INC_DTL_YN,
								NBI.NBI_REGI_NUM,
								NT_CODE.GET_RLT_NM(NB.RLT_ID) AS RLT_NM,
								NB.NB_CAR_NUM,
								NT_MAIN.GET_MGG_NM(NB.MGG_ID) AS MGG_NM,
								NB.NB_TOTAL_PRICE,
								IFNULL(NBI.NBI_INSU_DEPOSIT_DT, '') AS NBI_INSU_DEPOSIT_DT,
								NT_CODE.GET_RDET_NM(IFNULL(NBI.RDET_ID,'40121')) AS RDET_NM

							FROM NT_BILL.NBB_BILL_INSU NBI,
								NT_BILL.NBB_BILL NB

							WHERE NBI.NB_ID = NB.NB_ID
							AND NBI.MII_ID = '".addslashes($MII_ITEM)."'
							AND (
								  RIGHT(NBI.NBI_REGI_NUM, 3) = '".addslashes($NBI_REGI_NUM )."'
								OR RIGHT(NB.NB_CAR_NUM, 4) = '".addslashes($NB_CAR_NUM)."'
							 )
							AND NB.MGG_ID = '".addslashes($ARR['SELECT_NB_GARAGE_ID'])."'
							AND NB.DEL_YN = 'N'
							$SUB_SQL
							$LIMIT_SQL
							;"

					;

					$Result = DBManager::getResult($iSQL);

					$rtnArray = array();
					$rtnArray_ = array();
					while($RS = $Result->fetch_array()){
						array_push($rtnArray,
							array(
								"NB_INC_DTL_YN"=>$RS['NB_INC_DTL_YN'],
								"WRT_DTHMS"=>$RS['WRT_DTHMS'],
								"MII_NM"=>$RS['MII_NM'],
								"NBI_REGI_NUM"=>$RS['NBI_REGI_NUM'],
								"NB_ID"=>$RS['NB_ID'],
								"RLT_NM"=>$RS['RLT_NM'],
								"NB_CAR_NUM"=>$RS['NB_CAR_NUM'],
								"MGG_NM"=>$RS['MGG_NM'],
								"NB_TOTAL_PRICE"=>$RS['NB_TOTAL_PRICE'],
								"NBI_INSU_DEPOSIT_DT"=>$RS['NBI_INSU_DEPOSIT_DT'],
								"RDET_NM"=>$RS['RDET_NM'],
						));
					}

					$Result->close();  // 자원 반납
					unset($RS);
					if(!empty($rtnArray)){
						array_push($rtnArray_, $rtnArray);
						array_push($rtnArray_, $TOTAL_LIST_COUNT);
						array_push($rtnMainArray, $rtnArray_);
					}
				}
				$BEFORE_MII_ID = $MII_ITEM;
				$BEFORE_REGI_NUM = $ARR['NBI_REGI_NUM'][$index];

			}


			return  $rtnMainArray;

		}else{
			$iSQL = "SELECT
						NWP.NWP_ID,
						NWP.RWT_ID,
						NWP.NWP_ORDER_NUM,
						NWP.RWT_ID,
						NWR.FROM_CPP_ID,
						NWR.TO_CPP_ID,
						NWPP.CPP_ID	,
						(SELECT RWP_NM1 FROM NT_CODE.REF_WORK_PART WHERE RWP_ID = NWP.NWP_ID) AS RWP_NM1,
						(SELECT RWP_NM2 FROM NT_CODE.REF_WORK_PART WHERE RWP_ID = NWP.NWP_ID) AS RWP_NM2,
						IFNULL(GROUP_CONCAT(DISTINCT(NBW.RRT_ID)),'') AS CHECK_RRT_ID,
						IFNULL(GROUP_CONCAT(DISTINCT(NBW.RWPP_ID)),'') AS CHECK_RWPP_ID,
						IFNULL(GROUP_CONCAT(DISTINCT(NBP.CPP_ID)), '') AS CHECK_CPP_ID
					FROM
						NT_CODE.NCW_WORK_PART NWP
					LEFT JOIN NT_BILL.NBB_BILL_WORK NBW
						ON NBW.NWP_ID = NWP.NWP_ID
						AND NBW.NB_ID = '".addslashes($ARR['NB_ID'])."'
					LEFT JOIN NT_BILL.NBB_BILL_WORK_PART NBP
						ON NBP.NWP_ID = NWP.NWP_ID
						AND NBP.NB_ID = '".addslashes($ARR['NB_ID'])."'
					LEFT JOIN  (
						SELECT NWP_ID, GROUP_CONCAT(DISTINCT(FROM_CPP_ID)) AS FROM_CPP_ID , GROUP_CONCAT(DISTINCT(TO_CPP_ID)) AS TO_CPP_ID
						FROM NT_CODE.NCW_WORK_REPLACE
						GROUP BY NWP_ID
					) NWR
						ON NWR.NWP_ID = NWP.NWP_ID
					LEFT JOIN  (
						SELECT NWP_ID, GROUP_CONCAT(DISTINCT(CPP_ID)) AS CPP_ID
						FROM NT_CODE.NCW_WORK_PRIORITY
						GROUP BY NWP_ID
					) NWPP
						ON NWPP.NWP_ID = NWP.NWP_ID
					WHERE 1= 1
					GROUP BY NWP.NWP_ID;
					;"

			;

			// echo $iSQL;
			$Result = DBManager::getResult($iSQL);

			$rtnMainArray = array();
			$rtnArray = array();
			$rtnArray_ = array();
			while($RS = $Result->fetch_array()){
				array_push($rtnArray,
					array(
						"NWP_ID"=>$RS['NWP_ID'],
						"NWP_ORDER_NUM"=>$RS['NWP_ORDER_NUM'],
						"RWT_ID"=>$RS['RWT_ID'],
						"RWP_ID"=>$RS['RWP_ID'],
						"RWP_NM1"=>$RS['RWP_NM1'],
						"RWP_NM2"=>$RS['RWP_NM2'],
						"FROM_CPP_ID"=>$RS['FROM_CPP_ID'],
						"TO_CPP_ID"=>$RS['TO_CPP_ID'],
						"CPP_ID"=>$RS['CPP_ID'],
						"CHECK_RRT_ID"=>$RS['CHECK_RRT_ID'],
						"CHECK_CPP_ID"=>$RS['CHECK_CPP_ID'],
						"CHECK_RWPP_ID"=>$RS['CHECK_RWPP_ID'],
				));
			}

			$Result->close();  // 자원 반납
			unset($RS);

			if(empty($rtnArray)) {
				return HAVE_NO_DATA;
			} else {
				$iSQL = "SELECT
							CPP_ID,
							CPP_PART_TYPE,
							CPP_NM,
							CPP_UNIT,
							CPP_NM_SHORT
						FROM NT_CODE.NCP_PART
						WHERE DEL_YN = 'N'
						AND CPP_USE_YN = 'Y'
						ORDER BY CPP_ORDER_NUM ASC
						;"
				;

				$Result= DBManager::getResult($iSQL);

				while($RS = $Result->fetch_array()){
					array_push($rtnArray_,
						array(
						"CPP_ID"=>$RS['CPP_ID'],
						"CPP_UNIT"=>$RS['CPP_UNIT'],
						"CPP_PART_TYPE"=>$RS['CPP_PART_TYPE'],
						"CPP_NM"=>$RS['CPP_NM'],
						"CPP_NM_SHORT"=>$RS['CPP_NM_SHORT'],
					));
				}
				$Result->close();  // 자원 반납
				unset($RS);

				if(empty($rtnArray_)) {
					return HAVE_NO_DATA;
				} else {
					array_push($rtnMainArray, $rtnArray);
					array_push($rtnMainArray, $rtnArray_);
					return  $rtnMainArray;
				}

			}
		}
	}

	function getMaxmiumClaimPrice($ARR){
		$iSQL = "SELECT
					PCE_CURRENT_LIMIT_PRICE
				FROM
					NT_CODE.PCY_CLAIM_ENV
				;"

		;

		$Result = DBManager::getResult($iSQL);
		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"PCE_CURRENT_LIMIT_PRICE"=>$RS['PCE_CURRENT_LIMIT_PRICE']
			));
		}

		return $rtnArray;
	}


	/**
	 * 해당데이터 조회
	 * @param {$ARR}
	 * @param {ALLM_ID : 조회할 해당 데이터 ID}
	 */
	function getData($ARR) {
		$rtnArray = array();   // 우선순위 저장할 배열
		$rtnArray_ = array();  // 대체/체하 저장할 배열
		$jsonArray = array();   // 전체를 저장할 배열

		if(is_array($ARR['RRT_ID'])){
			foreach ($ARR['RRT_ID']as $index => $RRT_ID) {
				$iSQL = "SELECT
							NWP.NWP_ID,
							NWP.CPP_ID,
							NWP.RWPP_ID,
							NCP.CPP_NM_SHORT
						FROM
							NT_CODE.NCW_WORK_PRIORITY NWP,
							NT_CODE.NCP_PART NCP
						WHERE
							NCP.CPP_ID = NWP.CPP_ID
						AND	NWP.NWP_ID = '".addslashes($ARR['NWP_ID'])."'
						AND NWP.RRT_ID = '".$RRT_ID."'
						AND NWP.RWPP_ID = '".addslashes($ARR['RWPP_ID'][$index])."'
						;"

				;
				$Result = DBManager::getResult($iSQL);

				while($RS = $Result->fetch_array()){
					array_push($rtnArray,
						array(
							"NWP_ID"=>$RS['NWP_ID'],
							"RWPP_ID"=>$RS['RWPP_ID'],
							"CPP_ID"=>$RS['CPP_ID'],
							"RRT_ID"=> $RRT_ID,
							"CPP_NM_SHORT"=>$RS['CPP_NM_SHORT']
					));
				}
			}


			foreach ($ARR['RRT_ID']as $index => $RRT_ID) {
				$iSQL = "SELECT
							NWP_ID,
							FROM_CPP_ID,
							TO_CPP_ID,
							NWE_RATIO,
							RRT_ID,
							RWPP_ID

						FROM
							NT_CODE.NCW_WORK_REPLACE
						WHERE
							NWP_ID = '".addslashes($ARR['NWP_ID'])."'
						AND RRT_ID = '".$RRT_ID."'
						AND RWPP_ID = '".addslashes($ARR['RWPP_ID'][$index])."'
						;"

				;
				$Result = DBManager::getResult($iSQL);

				while($RS_ = $Result->fetch_array()){
					array_push($rtnArray_,
						array(
							"NWP_ID"=>$RS_['NWP_ID'],
							"FROM_CPP_ID"=>$RS_['FROM_CPP_ID'],
							"TO_CPP_ID"=>$RS_['TO_CPP_ID'],
							"RWPP_ID"=>$RS_['RWPP_ID'],
							"RRT_ID"=>$RS_['RRT_ID'],
							"NWE_RATIO"=>$RS_['NWE_RATIO']
					));
				}
			}
		}else{
			$iSQL = "SELECT
						NWP.NWP_ID,
						NWP.CPP_ID,
						NWP.RWPP_ID,
						NCP.CPP_NM_SHORT,
						NCP.CPP_PART_TYPE
					FROM
						NT_CODE.NCW_WORK_PRIORITY NWP,
						NT_CODE.NCP_PART NCP
					WHERE
						NCP.CPP_ID = NWP.CPP_ID
					AND	NWP.NWP_ID = '".addslashes($ARR['NWP_ID'])."'
					AND NWP.RRT_ID = '".addslashes($ARR['RRT_ID'])."'
					AND NWP.RWPP_ID = '".addslashes($ARR['RWPP_ID'])."'
					;"

			;

			$Result = DBManager::getResult($iSQL);

			$rtnArray = array();
			while($RS = $Result->fetch_array()){
				array_push($rtnArray,
					array(
						"NWP_ID"=>$RS['NWP_ID'],
						"RWPP_ID"=>$RS['RWPP_ID'],
						"CPP_ID"=>$RS['CPP_ID'],
						"RRT_ID"=>$ARR['RRT_ID'],
						"CPP_NM_SHORT"=>$RS['CPP_NM_SHORT']
				));
			}

			$iSQL = "SELECT
						NWP_ID,
						FROM_CPP_ID,
						TO_CPP_ID,
						NWE_RATIO,
						RRT_ID,
						RWPP_ID

					FROM
						NT_CODE.NCW_WORK_REPLACE
					WHERE
						NWP_ID = '".addslashes($ARR['NWP_ID'])."'
					AND RRT_ID = '".addslashes($ARR['RRT_ID'])."'
					AND RWPP_ID = '".addslashes($ARR['RWPP_ID'])."'
					;"

			;

			// echo $iSQL;
			$Result = DBManager::getResult($iSQL);

			while($RS_ = $Result->fetch_array()){
				array_push($rtnArray_,
					array(
						"NWP_ID"=>$RS_['NWP_ID'],
						"FROM_CPP_ID"=>$RS_['FROM_CPP_ID'],
						"TO_CPP_ID"=>$RS_['TO_CPP_ID'],
						"RWPP_ID"=>$RS_['RWPP_ID'],
						"RRT_ID"=>$RS_['RRT_ID'],
						"NWE_RATIO"=>$RS_['NWE_RATIO']
				));
			}
		}
		array_push($jsonArray,$rtnArray);
		array_push($jsonArray,$rtnArray_);

		$Result->close();  // 자원 반납
		unset($RS);
		return  $jsonArray;

	}

	/** 저장  */
	function insertData($ARR){
		// echo var_dump($ARR)."\n";
		$SESSION_ID = $_SESSION['USERID'] == null ? 'SYSYTEM' : $_SESSION['USERID'];
		$WRT_DTHMS = empty($ARR['WRT_DTHMS']) ? "NOW()" : "'".addslashes($ARR['WRT_DTHMS'])."'";
		$NB_CLAIM_USERID = empty($ARR['NB_CLAIM_USERID']) ? $SESSION_ID  : addslashes($ARR['NB_CLAIM_USERID']);

		$iSQL = "INSERT INTO `NT_BILL`.`NBB_BILL`
				(
					`NB_ID`,
					`MGG_ID`,
					`NB_GARAGE_NM`,
					`NB_CAR_NUM`,
					`NB_CLAIM_DT`,
					`RLT_ID`,
					`RMT_ID`,
					`CCC_ID`,
					`NB_VAT_DEDUCTION_YN`,
					`NB_MEMO_TXT`,
					`NB_INC_DTL_YN`,
					`NB_NEW_INSU_YN`,
					`NB_TOTAL_PRICE`,
					`NB_VAT_PRICE`,
					`NB_CLAIM_USERID`,
					`NB_CLAIM_DTHMS`,
					`NB_DUPLICATED_YN`,
					`WRT_DTHMS`,
					`LAST_DTHMS`,
					`WRT_USERID`,
					`DEL_YN`,
					`MODE_TARGET`
				)
				VALUES
				(
					'".addslashes($ARR['NB_ID'])."',
					'".addslashes($ARR['SELECT_NB_GARAGE_ID'])."',
					'".addslashes($ARR['SELECT_NB_GARAGE_NM'])."',
					'".addslashes($ARR['NB_CAR_NUM'])."',
					'".addslashes($ARR['NB_CLAIM_DT'])."',
					'".addslashes($ARR['RLT_ID'])."',
					'".addslashes($ARR['RMT_ID'])."',
					'".addslashes($ARR['CCC_ID'])."',
					'".addslashes($ARR['NB_VAT_DEDUCTION_YN'])."',
					'".addslashes($ARR['NB_MEMO_TXT'])."',
					'".addslashes($ARR['NB_INC_DTL_YN'])."',
					'".addslashes($ARR['NB_NEW_INSU_YN'])."',
					'".addslashes($ARR['NB_TOTAL_PRICE'])."',
					'".addslashes($ARR['NB_VAT_PRICE'])."',
					'".$NB_CLAIM_USERID."',
					NOW(),
					'".addslashes($ARR['NB_DUPLICATED_YN'])."',
					NOW(),
					NOW(),
					'".$SESSION_ID."',
					'N',
					'".MODE_TARGET_ALL."'
				)
			;"
		;

		if(!empty($ARR['MII_ID'])){
			$RFS_ID  = REF_FAX_NOT_SEND;
			if($ARR['EXCEPT_SEND_TARGET'] == YN_Y){   // 전송제외 일 경우
				$RFS_ID = REF_FAX_SUCC_SEND;
			}
			foreach($ARR['MII_ID'] as $index => $MII_ID){
				$NIC_ID = empty($ARR['SELECT_NBI_CHG_NM'][$index])  ? 'NULL' :  "'".$ARR['SELECT_NBI_CHG_NM'][$index]."'";

				if(empty($ARR['NBI_RATIO'][$index])){
					if($ARR['RLT_ID'] == '11430'){
						$NBI_RATIO = "0";
					}else{
						$NBI_RATIO = "";
					}
				}else{
					$NBI_RATIO = $ARR['NBI_RATIO'][$index];
				}

				if(!empty($MII_ID)){
					$iSQL .= "INSERT INTO `NT_BILL`.`NBB_BILL_INSU`
							(
								`NB_ID`,
								`NBI_RMT_ID`,
								`MII_ID`,
								`NIC_ID`,
								`NBI_CHG_NM`,
								`NBI_FAX_NUM`,
								`NBI_REGI_NUM`,
								`NBI_RATIO`,
								`NBI_SEND_FAX_YN`,
								`RFS_ID`,
								`NBI_PRECLAIM_YN`,
								`NBI_CAROWN_DEPOSIT_YN`,
								`NBI_INSU_DONE_YN`,
								`NBI_REQ_REASON_YN`,
								`WRT_DTHMS`,
								`LAST_DTHMS`,
								`WRT_USERID`
							)
							VALUES
							(
								'".addslashes($ARR['NB_ID'])."',
								'".addslashes($ARR['NBI_RMT_ID'][$index])."',
								'".addslashes($MII_ID)."',
								$NIC_ID,
								'".addslashes($ARR['NBI_CHG_NM'][$index])."',
								'".addslashes($ARR['NBI_FAX_NUM'][$index])."',
								'".addslashes($ARR['NBI_REGI_NUM'][$index])."',
								'".addslashes($NBI_RATIO)."',
								'".addslashes($ARR['NBI_SEND_FAX_YN'][$index])."',
								".$RFS_ID .",
								'".YN_N."',
								'".YN_N."',
								'".YN_N."',
								'".YN_N."',
								NOW(),
								NOW(),
								'".$SESSION_ID."'
							)
						;"
					;
				}

			}
		}


		if(!empty($ARR['CPPS_ID'])){
			foreach($ARR['CPPS_ID'] as $index => $CPPS_ARRAY){
				if(!empty(json_decode($CPPS_ARRAY)->CPPS_ID)){
					$CPPS_ID = json_decode($CPPS_ARRAY)->CPPS_ID;
					$CPP_ID = json_decode($CPPS_ARRAY)->CPP_ID;
					$iSQL .= "INSERT INTO `NT_BILL`.`NBB_BILL_PARTS`
							(
								`NB_ID`,
								`CPPS_ID`,
								`CPP_ID`,
								`NBP_USE_CNT`,
								`NBP_UNIT_PRICE`,
								`WRT_DTHMS`,
								`LAST_DTHMS`,
								`WRT_USERID`
							)
							VALUES
							(
								'".addslashes($ARR['NB_ID'])."',
								'".addslashes($CPPS_ID)."',
								'".addslashes($CPP_ID)."',
								'".addslashes($ARR['NBP_USE_CNT'][$index])."',
								'".addslashes($ARR['NBP_UNIT_PRICE'][$index])."',
								NOW(),
								NOW(),
								'".$SESSION_ID."'
							)
						;"
					;

					$iSQL .= "INSERT INTO `NT_STOCK`.`NSI_IO_HISTORY`
							(
								`MGG_ID`,
								`CPPS_ID`,
								`CPP_ID`,
								`NM_ID`,
								`RST_ID`,
								`NIH_DT`,
								`NIH_CNT`,
								`NIH_APPROVED_YN`,
								`NIH_APPROVED_DTHMS`,
								`NIH_APPROVED_USERID`,
								`WRT_DTHMS`,
								`LAST_DTHMS`,
								`WRT_USERID`
							)
							VALUES
							(
								'".addslashes($ARR['SELECT_NB_GARAGE_ID'])."',
								'".addslashes($CPPS_ID)."',
								'".addslashes($CPP_ID)."',
								'".addslashes($ARR['NB_ID'])."',
								'12130',
								NOW(),
								'".addslashes($ARR['NBP_USE_CNT'][$index])."',
								'Y',
								NOW(),
								'SYSTEM',
								NOW(),
								NOW(),
								'".$SESSION_ID."'
							)
						;"
					;
				}

			}
		}

		if(!empty($ARR['WORK_NWP_ID']) && !empty($ARR['WORK_RWPP_ID']) && !empty($ARR['WORK_RRT_ID']) && !empty($ARR['WORK_NBW_TIME'])){
			foreach($ARR['WORK_NWP_ID'] as $index => $WORK_NWP_ID){
				$iSQL .= "INSERT INTO `NT_BILL`.`NBB_BILL_WORK`
						(
							`NB_ID`,
							`NWP_ID`,
							`RRT_ID`,
							`RWPP_ID`,
							`NBW_YN`,
							`NBW_TIME`,
							`WRT_DTHMS`,
							`LAST_DTHMS`,
							`WRT_USERID`
						)
						VALUES
						(
							'".addslashes($ARR['NB_ID'])."',
							'".addslashes($WORK_NWP_ID)."',
							'".addslashes($ARR['WORK_RRT_ID'][$index])."',
							'".addslashes($ARR['WORK_RWPP_ID'][$index])."',
							'Y',
							'".addslashes($ARR['WORK_NBW_TIME'][$index])."',
							NOW(),
							NOW(),
							'".$SESSION_ID."'
						)
					;"
				;
			}
		}

		if(!empty($ARR['WORK_PART_NWP_ID']) && !empty($ARR['WORK_PART_CPP_ID'])){
			foreach($ARR['WORK_PART_NWP_ID'] as $index => $WORK_NWP_ID){
				$iSQL .= "INSERT INTO `NT_BILL`.`NBB_BILL_WORK_PART`
						(
							`NB_ID`,
							`NWP_ID`,
							`CPP_ID`,
							`WRT_DTHMS`,
							`LAST_DTHMS`,
							`WRT_USERID`
						)
						VALUES
						(
							'".addslashes($ARR['NB_ID'])."',
							'".addslashes($WORK_NWP_ID)."',
							'".addslashes($ARR['WORK_PART_CPP_ID'][$index])."',
							NOW(),
							NOW(),
							'".$SESSION_ID."'
						)
					;"
				;
			}
		}

		$iSQL .= "CALL NT_POINT.POINT_CHECK_SAVE(
						'".addslashes($ARR['SELECT_NB_GARAGE_ID'])."',
						'".addslashes($ARR['NB_TOTAL_PRICE'])."',
						'60210',
						'".addslashes($ARR['NB_ID'])."',
						'',
						'".$SESSION_ID."',
						@v_RESULT
					)
				;"
		;

		// echo $iSQL;
		return DBManager::execMulti($iSQL);

	}


	function deleteData($ARR, $SUB_SQL){
		$TABLE_NM = $ARR['TABLE_NM'];
		$SUB_ = empty($SUB_SQL) ? "WHERE NB_ID = '".addslashes($ARR['NB_ID'])."'" : $SUB_SQL ;
		$iSQL = "DELETE FROM $TABLE_NM
				 $SUB_
			;"
		;
		return $iSQL;
		// return DBManager::execQuery($iSQL);
	}

	/**
	 * 청구서 수정
	 */
	function updateData($ARR){
		// echo var_dump($ARR)."\n";
		$SESSION_ID = $_SESSION['USERID'] == null ? 'SYSYTEM' : $_SESSION['USERID'];

		if(!empty($ARR['NB_AB_NSRSN_VAL'])){  // 미작성여부
			$iSQL = "UPDATE NT_POINT.NPP_POINT_HISTORY
						SET
						NPH_CANCEL_YN = 'Y',
						NPH_CANCEL_DTHMS = now(),
						NPH_CANCEL_MEMO = '자동청구 미작성건:".$ARR['NB_AB_NSRSN_VAL']."',
						NPH_CANCEL_USERID = '".$SESSION_ID."'
						WHERE NB_ID = '".addslashes($ARR['NB_ID'])."'
						AND NPH_CANCEL_YN = 'N'
				;"
			;
			$iSQL .= "UPDATE  `NT_BILL`.`NBB_BILL`
					SET
						`NB_AB_NSRSN_VAL` = '".$ARR['NB_AB_NSRSN_VAL']."',
						NB_AB_NSRSN_DTHMS = NOW(),
						NB_AB_NSRSN_USERID = '".$SESSION_ID."',
						NB_DEL_REASON = '자동청구 미작성건:".$ARR['NB_AB_NSRSN_VAL']."',
						NB_DEL_DTHMS = now(),
						NB_DEL_USERID = '".addslashes($SESSION_ID)."',
						`DEL_YN` = 'Y'
					WHERE NB_ID = '".addslashes($ARR['NB_ID'])."'
				;"
			;
			return DBManager::execMulti($iSQL);
		}else{   // 청구서 수정
			$iSQL = "UPDATE  `NT_BILL`.`NBB_BILL`
					SET
						`MGG_ID` = '".addslashes($ARR['SELECT_NB_GARAGE_ID'])."',
						`NB_GARAGE_NM` = '".addslashes($ARR['SELECT_NB_GARAGE_NM'])."',
						`NB_CAR_NUM` = '".addslashes($ARR['NB_CAR_NUM'])."',
						`NB_CLAIM_DT` = '".addslashes($ARR['NB_CLAIM_DT'])."',
						`RLT_ID` = '".addslashes($ARR['RLT_ID'])."',
						`RMT_ID` = '".addslashes($ARR['RMT_ID'])."',
						`CCC_ID` = '".addslashes($ARR['CCC_ID'])."',
						`NB_VAT_DEDUCTION_YN` = '".addslashes($ARR['NB_VAT_DEDUCTION_YN'])."',
						`NB_MEMO_TXT` = '".addslashes($ARR['NB_MEMO_TXT'])."',
						`NB_INC_DTL_YN` = '".addslashes($ARR['NB_INC_DTL_YN'])."',
						`NB_NEW_INSU_YN` = '".addslashes($ARR['NB_NEW_INSU_YN'])."',
						`NB_TOTAL_PRICE` = '".addslashes($ARR['NB_TOTAL_PRICE'])."',
						`NB_VAT_PRICE` = '".addslashes($ARR['NB_VAT_PRICE'])."',
						`NB_DUPLICATED_YN` = '".addslashes($ARR['NB_DUPLICATED_YN'])."',
						`LAST_DTHMS` = NOW(),
						`WRT_USERID` = '".$SESSION_ID."'
					WHERE NB_ID = '".addslashes($ARR['NB_ID'])."'

				;"
			;

			if(!empty($ARR['MII_ID'])){
				$RFS_SQL = "";
				if($ARR['EXCEPT_SEND_TARGET'] == YN_Y){   // 전송제외 일 경우
					$RFS_SQL = "`RFS_ID` = '".REF_FAX_SUCC_SEND."',";
				}

				foreach($ARR['MII_ID'] as $index => $MII_ID){
					$NIC_ID = empty($ARR['SELECT_NBI_CHG_NM'][$index])  ? 'NULL' :  "'".$ARR['SELECT_NBI_CHG_NM'][$index]."'";
					$NBI_RATIO = "";
					if(empty($ARR['NBI_RATIO'][$index])){
						if($ARR['RLT_ID'] == '11430'){
							$NBI_RATIO = "0";
						}
					}else{
						$NBI_RATIO = $ARR['NBI_RATIO'][$index];
					}

					if(!empty($MII_ID)){
						$iSQL .= "UPDATE  `NT_BILL`.`NBB_BILL_INSU`
								SET
									`MII_ID` = '".addslashes($MII_ID)."',
									`NIC_ID` = ".$NIC_ID .",
									`NBI_CHG_NM` = '".addslashes($ARR['NBI_CHG_NM'][$index])."',
									`NBI_FAX_NUM` = '".addslashes($ARR['NBI_FAX_NUM'][$index])."',
									`NBI_REGI_NUM` = '".addslashes($ARR['NBI_REGI_NUM'][$index])."',
									`NBI_RATIO` = '".addslashes($NBI_RATIO)."',
									`NBI_SEND_FAX_YN` = '".addslashes($ARR['NBI_SEND_FAX_YN'][$index])."',
									$RFS_SQL
									`LAST_DTHMS` = NOW(),
									`WRT_USERID` = '".$SESSION_ID."'
								WHERE NB_ID = '".addslashes($ARR['NB_ID'])."'
								AND `NBI_RMT_ID` = '".addslashes($ARR['NBI_RMT_ID'][$index])."'

							;"
						;
					}
				}
			}

			$ARR['TABLE_NM'] = "`NT_BILL`.`NBB_BILL_PARTS`";
			$iSQL .= $this->deleteData($ARR, '');

			$ARR['TABLE_NM'] = "`NT_STOCK`.`NSI_IO_HISTORY`";
			$SUB = "WHERE NM_ID = '".addslashes($ARR['NB_ID'])."'";
			$iSQL .= $this->deleteData($ARR, $SUB);
			if(!empty($ARR['CPPS_ID'])){
				foreach($ARR['CPPS_ID'] as $index => $CPPS_ARRAY){
					if(!empty(json_decode($CPPS_ARRAY)->CPPS_ID)){
						$CPPS_ID = json_decode($CPPS_ARRAY)->CPPS_ID;
						$CPP_ID = json_decode($CPPS_ARRAY)->CPP_ID;
						$iSQL .= "INSERT INTO `NT_BILL`.`NBB_BILL_PARTS`
								(
									`NB_ID`,
									`CPPS_ID`,
									`CPP_ID`,
									`NBP_USE_CNT`,
									`NBP_UNIT_PRICE`,
									`WRT_DTHMS`,
									`LAST_DTHMS`,
									`WRT_USERID`
								)
								VALUES
								(
									'".addslashes($ARR['NB_ID'])."',
									'".addslashes($CPPS_ID)."',
									'".addslashes($CPP_ID)."',
									'".addslashes($ARR['NBP_USE_CNT'][$index])."',
									'".addslashes($ARR['NBP_UNIT_PRICE'][$index])."',
									NOW(),
									NOW(),
									'".$SESSION_ID."'
								)
							;"
						;

						$iSQL .= "INSERT INTO `NT_STOCK`.`NSI_IO_HISTORY`
								(
									`MGG_ID`,
									`CPPS_ID`,
									`CPP_ID`,
									`NM_ID`,
									`RST_ID`,
									`NIH_DT`,
									`NIH_CNT`,
									`NIH_APPROVED_YN`,
									`NIH_APPROVED_DTHMS`,
									`NIH_APPROVED_USERID`,
									`WRT_DTHMS`,
									`LAST_DTHMS`,
									`WRT_USERID`
								)
								VALUES
								(
									'".addslashes($ARR['SELECT_NB_GARAGE_ID'])."',
									'".addslashes($CPPS_ID)."',
									'".addslashes($CPP_ID)."',
									'".addslashes($ARR['NB_ID'])."',
									'12130',
									NOW(),
									'".addslashes($ARR['NBP_USE_CNT'][$index])."',
									'Y',
									NOW(),
									'SYSTEM',
									NOW(),
									NOW(),
									'".$SESSION_ID."'
								)
							;"
						;
					}
				}
			}

			$ARR['TABLE_NM'] = "`NT_BILL`.`NBB_BILL_WORK`";
			$iSQL .= $this->deleteData($ARR, '');
			if(!empty($ARR['WORK_NWP_ID']) && !empty($ARR['WORK_RWPP_ID']) && !empty($ARR['WORK_RRT_ID']) && !empty($ARR['WORK_NBW_TIME'])){
				foreach($ARR['WORK_NWP_ID'] as $index => $WORK_NWP_ID){
					$iSQL .= "INSERT INTO `NT_BILL`.`NBB_BILL_WORK`
							(
								`NB_ID`,
								`NWP_ID`,
								`RRT_ID`,
								`RWPP_ID`,
								`NBW_YN`,
								`NBW_TIME`,
								`WRT_DTHMS`,
								`LAST_DTHMS`,
								`WRT_USERID`
							)
							VALUES
							(
								'".addslashes($ARR['NB_ID'])."',
								'".addslashes($WORK_NWP_ID)."',
								'".addslashes($ARR['WORK_RRT_ID'][$index])."',
								'".addslashes($ARR['WORK_RWPP_ID'][$index])."',
								'Y',
								'".addslashes($ARR['WORK_NBW_TIME'][$index])."',
								NOW(),
								NOW(),
								'".$SESSION_ID."'
							)
						;"
					;
				}
			}

			$ARR['TABLE_NM'] = "`NT_BILL`.`NBB_BILL_WORK_PART`";
			$iSQL .= $this->deleteData($ARR, '');
			if(!empty($ARR['WORK_PART_NWP_ID']) && !empty($ARR['WORK_PART_CPP_ID'])){

				foreach($ARR['WORK_PART_NWP_ID'] as $index => $WORK_NWP_ID){
					$iSQL .= "INSERT INTO `NT_BILL`.`NBB_BILL_WORK_PART`
							(
								`NB_ID`,
								`NWP_ID`,
								`CPP_ID`,
								`WRT_DTHMS`,
								`LAST_DTHMS`,
								`WRT_USERID`
							)
							VALUES
							(
								'".addslashes($ARR['NB_ID'])."',
								'".addslashes($WORK_NWP_ID)."',
								'".addslashes($ARR['WORK_PART_CPP_ID'][$index])."',
								NOW(),
								NOW(),
								'".$SESSION_ID."'
							)
						;"
					;
				}
			}

			$iSQL .= "UPDATE NT_POINT.NPP_POINT_HISTORY
						SET
						NPH_CANCEL_YN = 'Y',
						NPH_CANCEL_DTHMS = now(),
						NPH_CANCEL_MEMO = '청구서 삭제',
						NPH_CANCEL_USERID = '".$SESSION_ID."'
						WHERE NB_ID = '".addslashes($ARR['NB_ID'])."'
						AND NPH_CANCEL_YN = 'N'
				;"
			;
			$iSQL .= "CALL NT_POINT.POINT_CHECK_SAVE(
						'".addslashes($ARR['SELECT_NB_GARAGE_ID'])."',
						'".addslashes($ARR['NB_TOTAL_PRICE'])."',
						'60210',
						'".addslashes($ARR['NB_ID'])."',
						'',
						'".$SESSION_ID."',
						@v_RESULT
					)
				;"
			;
			
			return DBManager::execMulti($iSQL);
		}

	}

	function updateInsuManage($ARR){
		$SESSION_ID = $_SESSION['USERID'] == null ? 'SYSYTEM' : $_SESSION['USERID'];

		$iSQL = "SELECT
					NIC_ID
				FROM NT_MAIN.NMG_GARAGE_INSU_CHARGE
				WHERE RICT_ID = '".addslashes($ARR['RICT_ID'])."'
				AND MGG_ID = '".addslashes($ARR['MGG_ID'])."'
				AND NIC_ID = '".addslashes($ARR['NIC_ID'])."'
				;"

		;

		$Result = DBManager::getRecordSet($iSQL);

		if(empty($Result)){
			$iSQL = "SELECT
						NIC_ID
					FROM NT_MAIN.NMI_INSU_CHARGE_TYPE
					WHERE RICT_ID = '".addslashes($ARR['RICT_ID'])."'
					AND NIC_ID = '".addslashes($ARR['NIC_ID'])."'
					;"

			;

			if(empty(DBManager::getRecordSet($iSQL))){
				$iSQL = "INSERT INTO `NT_MAIN`.`NMI_INSU_CHARGE_TYPE`
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
								'".$_SESSION['IW_USERID']."'
							)
						;"
				;
			}

			 $iSQL .= "INSERT INTO `NT_MAIN`.`NMG_GARAGE_INSU_CHARGE`
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
							'".$SESSION_ID."'
						)
						;"
			;

			return DBManager::execMulti($iSQL);
		}else{
			return CONFLICT;
		}

	}

	// 해당청구서건이 중복인지 아닌지..
	function isDuplicateBill($ARR){
		$iSQL = "SELECT
					NB_DUPLICATED_YN
				FROM NT_BILL.NBB_BILL
				WHERE DEL_YN =  'N'
				AND NB_ID = '".addslashes($ARR['NB_ID'])."'
				;"
		;

		// echo $iSQL;
		$Result= DBManager::getResult($iSQL);
		$rtnArray = array();

		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"NB_DUPLICATED_YN"=>$RS['NB_DUPLICATED_YN']
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


	/**
	 * update시에슨 조견표 계싼
	 * insert시에는 저장
	 */
	function setData($ARR) {
		if($ARR['REQ_MODE'] == CASE_CREATE){
			if($ARR['DUPLICATED_CLICKED_YN'] == YN_N){   // 중복검사를 했는지 안했는지..
				$result = $this->getList($ARR);
				if(empty($result)){
					$ARR['NB_DUPLICATED_YN'] = YN_N;
				}else{
					$ARR['NB_DUPLICATED_YN'] = YN_Y;
				}

				if(empty($ARR['WRT_DTHMS'])){      // 등록시간이 있으면 수정 아니면 등록
					return $this->insertData($ARR);
				}else{
					return $this->updateData($ARR);
				}

			}else{
				if(empty($ARR['WRT_DTHMS'])){
					return $this->insertData($ARR);
				}else{
					return $this->updateData($ARR);
				}
			}

		}else{
			if(!empty($ARR['RICT_ID'])){
				return $this->updateInsuManage($ARR);
			}else{
				/** 조견표 생성 */
				$rtnArray = array();
				if(!empty($ARR['DATA'])){
					foreach($ARR['DATA'] as $index => $chartArray){
						$RATIO = "";
						if($chartArray['RWPP_ID'] == WORK_PART_INSIDE_TYPE && $chartArray['RRT_ID'] == RACON_CHANGE_CD){ //교환,실내
							$RATIO = "ROUND(NRPS.NRPS_RATIO*(SELECT NWP_FLOOR_CHANGE_IN FROM NT_CODE.NCW_WORK_PART WHERE NWP_ID = '".addslashes($chartArray['NWP_ID'])."'),2)AS NRPS_RATIO";
						}else if($chartArray['RWPP_ID'] == WORK_PART_LOWER_TYPE && $chartArray['RRT_ID'] == RACON_CHANGE_CD){ //교환,하체
							$RATIO = "ROUND(NRPS.NRPS_RATIO*(SELECT NWP_FLOOR_CHANGE_OUT FROM NT_CODE.NCW_WORK_PART WHERE NWP_ID = '".addslashes($chartArray['NWP_ID'])."'),2)AS NRPS_RATIO";
						}else if($chartArray['RWPP_ID'] == WORK_PART_INSIDE_TYPE && $chartArray['RRT_ID'] == RACON_METAL_CD){ //판금,실내
							$RATIO = "ROUND(NRPS.NRPS_RATIO*(SELECT NWP_FLOOR_SHEET_IN FROM NT_CODE.NCW_WORK_PART WHERE NWP_ID = '".addslashes($chartArray['NWP_ID'])."'),2)AS NRPS_RATIO";
						}else if($chartArray['RWPP_ID'] == WORK_PART_LOWER_TYPE && $chartArray['RRT_ID'] == RACON_METAL_CD){  //판금,하체
							$RATIO = "ROUND(NRPS.NRPS_RATIO*(SELECT NWP_FLOOR_SHEET_OUT FROM NT_CODE.NCW_WORK_PART WHERE NWP_ID = '".addslashes($chartArray['NWP_ID'])."'),2)AS NRPS_RATIO";
						}else{
							$RATIO = "ROUND(NRPS.NRPS_RATIO,2)AS NRPS_RATIO";
						}

						$iSQL = "SELECT
										".$RATIO.",
										NRPS.CPP_ID,
										NRPS.CPPS_ID,
										NRT.NRT_TIME_M

								FROM
									NT_CODE.NCT_RECKON_PART_SUB NRPS
								LEFT JOIN  NT_CODE.NCT_RECKON_TIME NRT
								ON NRT.CPP_ID = '".addslashes($chartArray['CPP_ID'])."'
									AND NRT.RWP_ID = '".addslashes($chartArray['NWP_ID'])."'
									AND NRT.RRT_ID = '".addslashes($chartArray['RRT_ID'])."'
									AND NRT.RCT_CD = '".addslashes($ARR['RCT_CD'])."'
								WHERE
									NRPS.CPP_ID = '".addslashes($chartArray['CPP_ID'])."'
								AND NRPS.CPPS_ID = '".addslashes($chartArray['CPPS_ID'])."'
								AND NRPS.RWP_ID = '".addslashes($chartArray['NWP_ID'])."'
								AND NRPS.RRT_ID = '".addslashes($chartArray['RRT_ID'])."'
								AND NRPS.RCT_CD = '".addslashes($ARR['RCT_CD'])."'
								;"

						;
						// echo $iSQL;
						$Result = DBManager::getResult($iSQL);

						while($RS = $Result->fetch_array()){
							array_push($rtnArray,
								array(
									"NWP_ID"=>$chartArray['NWP_ID'],
									"RRT_ID"=>$chartArray['RRT_ID'],
									"RWPP_ID"=>$chartArray['RWPP_ID'],
									"NRPS_RATIO"=>$RS['NRPS_RATIO'],
									"CPP_ID"=>$RS['CPP_ID'],
									"CPPS_ID"=>$RS['CPPS_ID'],
									"NRT_TIME_M"=>$RS['NRT_TIME_M'],
							));
						}
					}
					$Result->close();  // 자원 반납
					unset($RS);
				}

				return  $rtnArray;
			}

		}
	}

	/** 차량정보 */
	function getUUID(){
		$iSQL = "SELECT
					UUID_SHORT() AS UUID;
				;"
		;

		return DBManager::getRecordSet($iSQL);
	}

	/** 차량정보 */
	function getCarInfo(){
		$iSQL = "SELECT
					CCC_ID,
					CCC_CAR_TYPE_CD,
					NT_CODE.GET_CODE_NM(CCC_CAR_TYPE_CD) AS CCC_NM,
					CCC_CAR_NM

				FROM NT_CODE.NCC_CAR NCC
				WHERE DEL_YN = 'N'
				;"
		;

		$Result= DBManager::getResult($iSQL);
		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"CCC_ID"=>$RS['CCC_ID'],
					"CCC_CAR_TYPE_CD"=>$RS['CCC_CAR_TYPE_CD'],
					"CCC_NM"=>$RS['CCC_NM'],
					"CCC_CAR_NM"=>$RS['CCC_CAR_NM']
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


	/** 해당공업사 사용부품 가져오기 */
	function getGaragePartsInfo($ARR){
		$mainRtnArray = array();

		$iSQL = "SELECT
					CPP.CPP_PART_TYPE,
					NGP.NGP_DEFAULT_YN,
					CPP.CPP_ID,
					CPP.CPP_UNIT,
					CPP.CPP_NM,
					CPP.CPP_NM_SHORT,
					CPPS.CPPS_ID,
					CPPS.CPPS_NM,
					CPPS.CPPS_NM_SHORT,
					CPPS.CPPS_PRICE

				FROM NT_MAIN.NMG_GARAGE_PART NGP,
							NT_CODE.NCP_PART CPP,
							NT_CODE.NCP_PART_SUB CPPS
				WHERE CPP.CPP_ID = NGP.CPP_ID
				AND CPPS.CPPS_ID = NGP.CPPS_ID
				AND MGG_ID = '".addslashes($ARR['MGG_ID'])."'
				ORDER BY CPP.CPP_ORDER_NUM ASC
				;"
		;

		$Result= DBManager::getResult($iSQL);
		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"CPP_PART_TYPE"=>$RS['CPP_PART_TYPE'],
					"NGP_DEFAULT_YN"=>$RS['NGP_DEFAULT_YN'],
					"CPP_ID"=>$RS['CPP_ID'],
					"CPP_UNIT"=>$RS['CPP_UNIT'],
					"CPP_NM"=>$RS['CPP_NM'],
					"CPP_NM_SHORT"=>$RS['CPP_NM_SHORT'],
					"CPPS_ID"=>$RS['CPPS_ID'],
					"CPPS_NM"=>$RS['CPPS_NM'],
					"CPPS_NM_SHORT"=>$RS['CPPS_NM_SHORT'],
					"CPPS_PRICE"=>$RS['CPPS_PRICE']
			));
		}

		$Result->close();  // 자원 반납
		unset($RS);

		if(empty($rtnArray)) {
			return HAVE_NO_DATA;
		} else {
			$iSQL = "SELECT
						NG.`MGG_NM`,
						(
							SELECT COUNT(*)
							FROM	`NT_BILL`.`NBB_BILL` INB
							WHERE	INB.`MGG_ID` = NG.MGG_ID
							AND		DEL_YN = 'N'
							AND		DATE_FORMAT(INB.`NB_CLAIM_DT`,'%Y%m') = DATE_FORMAT(DATE_ADD(NOW(), INTERVAL -2 MONTH),'%Y%m')
						) AS 'bBeforeMonthCNT',
						(
							SELECT COUNT(*)
							FROM	`NT_BILL`.`NBB_BILL` INB
							WHERE	INB.`MGG_ID` = NG.MGG_ID
							AND		DEL_YN = 'N'
							AND		DATE_FORMAT(INB.`NB_CLAIM_DT`,'%Y%m') = DATE_FORMAT(DATE_ADD(NOW(), INTERVAL -1 MONTH),'%Y%m')
						) AS 'beforeMonthCNT',
						COUNT(*) AS 'monthCNT',
						SUM(`NB_TOTAL_PRICE`) AS 'monthPRICE',
						(
							SELECT	SUM(NIS.`NIS_AMOUNT` * NPS.`CPPS_PRICE`)
							FROM	`NT_STOCK`.`NSI_IO_SUMMARY` NIS,
									`NT_CODE`.`NCP_PART_SUB` NPS
							WHERE	NIS.CPPS_ID = NPS.CPPS_ID
							AND		NIS.MGG_ID = NG.MGG_ID
						) AS 'stockPRICE'
					FROM
						`NT_MAIN`.`NMG_GARAGE` NG,
						`NT_BILL`.`NBB_BILL` NB
					WHERE
						NG.MGG_ID = NB.MGG_ID
					AND	NG.MGG_ID = '".addslashes($ARR['MGG_ID'])."'
					AND	DATE_FORMAT(NB.`NB_CLAIM_DT` ,'%Y%m') = DATE_FORMAT(NOW(),'%Y%m')
					AND	NB.DEL_YN = 'N'
					;"
			;

			$Result= DBManager::getResult($iSQL);
			$rtnArray_ = array();
			while($RS = $Result->fetch_array()){
				array_push($rtnArray_,
					array(
						"bBeforeMonthCNT"=>$RS['bBeforeMonthCNT'],
						"beforeMonthCNT"=>$RS['beforeMonthCNT'],
						"monthCNT"=>$RS['monthCNT'],
						"monthPRICE"=>$RS['monthPRICE'],
						"stockPRICE"=>$RS['stockPRICE'],
						"MGG_NM"=>$RS['MGG_NM']
				));
			}

			$Result->close();  // 자원 반납
			unset($RS);

			$iSQL = "SELECT
						NP.CPP_NM,
						NPS.CPPS_NM,
						SUM(IF(`RST_ID` = '12110',NIH.`NIH_CNT`,0)) AS 'SUPPLY',
						ROUND(SUM(IF(`RST_ID` = '12130',NIH.`NIH_CNT`,0)),2) AS 'BILL',
						ROUND(IFNULL((
							SELECT SUM(`NIS_AMOUNT`)
							FROM	`NT_STOCK`.`NSI_IO_SUMMARY` INIS
							WHERE	INIS.`CPP_ID` = NIH.CPP_ID
							AND		INIS.MGG_ID = NIH.MGG_ID
							AND	DATE_FORMAT(INIS.`LAST_DTHMS` ,'%Y%m') = DATE_FORMAT(DATE_ADD(NOW(), INTERVAL -1 MONTH),'%Y%m')
						),0),2) AS 'STOCK',
						ROUND(IF(IFNULL((
							SELECT SUM(`NIS_AMOUNT`)
							FROM	`NT_STOCK`.`NSI_IO_SUMMARY` INIS
							WHERE	INIS.`CPP_ID` = NIH.CPP_ID
							AND		INIS.MGG_ID = NIH.MGG_ID
							AND	DATE_FORMAT(INIS.`LAST_DTHMS` ,'%Y%m') = DATE_FORMAT(DATE_ADD(NOW(), INTERVAL -1 MONTH),'%Y%m')
							),0) / SUM(IF(`RST_ID` = '12130',NIH.`NIH_CNT`,0)) > 1.5,
							(
							SELECT SUM(`NIS_AMOUNT`)
							FROM	`NT_STOCK`.`NSI_IO_SUMMARY` INIS
							WHERE	INIS.`CPP_ID` = NIH.CPP_ID
							AND		INIS.MGG_ID = NIH.MGG_ID
							AND	DATE_FORMAT(INIS.`LAST_DTHMS` ,'%Y%m') = DATE_FORMAT(DATE_ADD(NOW(), INTERVAL -1 MONTH),'%Y%m')
						) / SUM(IF(`RST_ID` = '12130',NIH.`NIH_CNT`,0)),
							'-'
						),2) AS 'RATIO'

					FROM
						`NT_STOCK`.`NSI_IO_HISTORY` NIH,
						`NT_CODE`.`NCP_PART_SUB` NPS,
						`NT_CODE`.`NCP_PART` NP
					WHERE
						NIH.`MGG_ID` = '".addslashes($ARR['MGG_ID'])."'
					AND	NIH.CPPS_ID = NPS.CPPS_ID
					AND	NIH.CPP_ID = NP.CPP_ID
					AND	DATE_FORMAT(NIH.`NIH_DT` ,'%Y%m') = DATE_FORMAT(DATE_ADD(NOW(), INTERVAL -1 MONTH),'%Y%m')
					GROUP BY NIH.CPP_ID
                    HAVING RATIO > 1.5
					ORDER BY NP.CPP_ORDER_NUM ASC
					;"
			;

			$Result= DBManager::getResult($iSQL);
			$rtnArray__ = array();
			while($RS = $Result->fetch_array()){
				array_push($rtnArray__,
					array(
						"CPP_NM"=>$RS['CPP_NM'],
						"CPPS_NM"=>$RS['CPPS_NM'],
						"SUPPLY"=>$RS['SUPPLY'],
						"BILL"=>$RS['BILL'],
						"STOCK"=>$RS['STOCK'],
						"RATIO"=>$RS['RATIO'],
				));
			}

			$Result->close();  // 자원 반납
			unset($RS);

			array_push($mainRtnArray, $rtnArray);
			array_push($mainRtnArray, $rtnArray_);
			array_push($mainRtnArray, $rtnArray__);

			return $mainRtnArray;
		}
	}


	/** 부품타입 가져오기 */
	function getGarageInfo(){
		/** 부품타입 가져오기 */
		$iSQL = "SELECT
					CONCAT(`MGG_NM`,'-',RET_NM) AS MGG_NM,
					`MGG_ID`
				FROM `NT_MAIN`.`V_MGG_STATUS`
				WHERE RCT_ID IN ('10610', '10640') -- 거래, 중단예정
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

	function getInsuManageType($ARR){
		if(!empty($ARR['MANAGE_ARRAY'])){
			$rtnArray = array();
			foreach($ARR['MANAGE_ARRAY'] as $index => $MANAGE_ARRAY){
				$MANAGE = json_decode($MANAGE_ARRAY);
				$MII = $MANAGE->MII_ID;
				$RICT_ID = $MANAGE->RICT_ID;
				$RLT_NM = $MANAGE->RLT_NM;
				$RLT_ID = $MANAGE->RLT_ID;

				$iSQL = "SELECT
							CAST(AES_DECRYPT(`NIC`.`NIC_NM_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `NIC_NM_AES`,
							CAST(AES_DECRYPT(`NIC`.`NIC_FAX_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `NIC_FAX_AES`,
							NIC.NIC_ID,
							DATE_FORMAT(NIC.WRT_DTHMS,'%Y-%m-%d')AS WRT_DTHMS,
							NT_CODE.GET_RICT_NM(NGIC.RICT_ID) AS RICT_NM,
							NGIC.RICT_ID,
							NT_BILL.GET_NIC_CLAIM_COUNT(NIC.NIC_ID, '".addslashes($ARR['MGG_ID'])."') AS CLAIM_CNT
						FROM NT_MAIN.NMG_GARAGE_INSU_CHARGE  NGIC,
									NT_MAIN.NMI_INSU_CHARGE NIC
						WHERE  NIC.NIC_ID = NGIC.NIC_ID
						AND NIC.MII_ID = '".addslashes($MII)."'
						AND NGIC.MGG_ID = '".addslashes($ARR['MGG_ID'])."'
						AND NGIC.RICT_ID = '".addslashes($RICT_ID)."'
						ORDER BY NGIC.WRT_DTHMS DESC LIMIT 1
					;"
				;

				$Result= DBManager::getResult($iSQL);

				while($RS = $Result->fetch_array()){
					array_push($rtnArray,
						array(
							"NIC_NM_AES"=>$RS['NIC_NM_AES'],
							"NIC_FAX_AES"=>$RS['NIC_FAX_AES'],
							"RICT_NM"=>$RS['RICT_NM'],
							"NIC_ID"=>$RS['NIC_ID'],
							"RICT_ID"=>$RS['RICT_ID'],
							"WRT_DTHMS"=>$RS['WRT_DTHMS'],
							"CLAIM_CNT"=>$RS['CLAIM_CNT'],
							"RLT_ID"=>$RLT_ID,
							"RLT_NM"=>$RLT_NM ,
					));
				}
				$Result->close();  // 자원 반납
				unset($RS);
			}

			return $rtnArray;
		}else{
			$iSQL = "SELECT
						PIE_LOW_PRICE,
						PIE_HIGH_PRICE
					FROM NT_CODE.PCY_INSU_ENV;
					;"
			;

			$Result= DBManager::getResult($iSQL);
			$rtnArray = array();
			while($RS = $Result->fetch_array()){
				array_push($rtnArray,
					array(
						"PIE_LOW_PRICE"=>$RS['PIE_LOW_PRICE'],
						"PIE_HIGH_PRICE"=>$RS['PIE_HIGH_PRICE'],
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

	function updateFaxStatus($ARR){
		if(!empty($ARR['DATA'])){
			foreach(ARR['DATA'] as $index => $data){
				$iSQL .= "UPDATE
							RFS_ID
						FROM NT_BILL.NBB_BILL_INSU
						WHERE NB_ID = '".addslashes($data['NB_ID'])."'
						AND NBI_RMT_ID = '".addslashes($data['NBI_RMT_ID'])."'
						AND RFS_ID = '90210'
						;"
				;
			}

			return DBManager::execQuery($iSQL);
		}else{
			return false;
		}
	}


}
	// 0. 객체 생성
	$clsWriteBill = new clsWriteBill(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$clsWriteBill->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
