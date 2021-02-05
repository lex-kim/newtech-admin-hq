<?php

class clsGwManagement extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자
	private $AES_KEY = "123";
	private $MGG_BIZ_START_DT = 'MGG_BIZ_START_DT';
	private $MGG_BIZ_END_DT = 'MGG_BIZ_END_DT';

	// Class Object Constructor
	function clsGwManagement($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
		$this->DATA_SAVE_PATH = $DATA_SAVE_PATH;
		$this->ALLOW_FILE_EXT = $ALLOW_FILE_EXT;
	}

	/**
	 * 전체데이터 가져오는 함수
	*/
	function getTotalList(){
		$iSQL = $this->getListSql('',$ARR);

		try{
			$Result= DBManager::getResult($iSQL);
		} catch (Exception $e){
			echo "error : ".$e;
			return ERROR_DB_SELECT_DATA;   //ERROR_DB_SELECT_DATA
		}

		$rtnArray = $this->getListColumn($Result, '');

		$Result->close();  // 자원 반납
		unset($RS);

		if(empty($rtnArray)) {
			return HAVE_NO_DATA;
		} else {
			$iSQL = "SELECT 
						`CPP`.`CPP_NM_SHORT`,
						`CPPS`.`CPPS_NM_SHORT`,
						`CPPS`.`CPPS_NM`,
						`MGGP`.`MGG_ID`,
						`MGGP`.`CPP_ID`,
						`MGGP`.`CPPS_ID`,
						`MGGP`.`NGP_DEFAULT_YN`,
						`MGGP`.`NGP_RATIO`,
						`MGGP`.`NGP_MEMO`
					FROM `NT_MAIN`.`NMG_GARAGE_PART` `MGGP`,
						`NT_CODE`.`NCP_PART` `CPP`,
						`NT_CODE`.`NCP_PART_SUB` `CPPS`
					WHERE `MGGP`.`CPP_ID` =  `CPP`.`CPP_ID`
					AND `MGGP`.`CPPS_ID` =  `CPPS`.`CPPS_ID`
					ORDER BY `MGGP`.`CPP_ID` DESC
					;"
			;

			$Result= DBManager::getResult($iSQL);

			$partsArray = array();
			while($RS = $Result->fetch_array()){
				array_push($partsArray,
					array(
						"CPP_NM_SHORT"=>$RS['CPP_NM_SHORT'],
						"CPPS_NM"=>$RS['CPPS_NM'],
						"CPPS_NM_SHORT"=>$RS['CPPS_NM_SHORT'],
						"MGG_ID"=>$RS['MGG_ID'],
						"CPP_ID"=>$RS['CPP_ID'],
						"CPPS_ID"=>$RS['CPPS_ID'],
						"NGP_DEFAULT_YN"=>$RS['NGP_DEFAULT_YN'],
						"NGP_RATIO"=>$RS['NGP_RATIO'],
						"NGP_MEMO"=>$RS['NGP_MEMO'],
				));
			}
			$Result->close();  // 자원 반납
			unset($RS);

			$jsonArray = array();
			array_push($jsonArray,$rtnArray);
			array_push($jsonArray,$partsArray);
			array_push($jsonArray,$TOTAL_LIST_COUNT);

			return  $jsonArray;
		}
	}


	/**
	 * 쿼리에서 받은 select 결과를 array로 저장하는 함수. 공용으로 사용하기 위해서
	 * (TotalList와 getList)
	 */
	function getListColumn($Result, $IDX){
		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			$partsInfoArray = array();
			$iSQL = "SELECT CPP_ID,
						GROUP_CONCAT(NT_CODE.GET_CPPS_NM_SHORT(CPPS_ID)) AS CPP_NM_SHORT ,
						GROUP_CONCAT(IF(NGP_DEFAULT_YN = 'Y' , NT_CODE.GET_CPPS_NM_SHORT(CPPS_ID), '') SEPARATOR '')AS CPPS_NM_SHORT,
						`NGP_RATIO`,
                        GROUP_CONCAT(NGP_MEMO SEPARATOR  '') AS NGP_MEMO
					FROM NT_MAIN.NMG_GARAGE_PART MGP
					WHERE MGG_ID = '".$RS['MGG_ID']."'
					GROUP BY CPP_ID ;
			";
			// echo $iSQL;
			$partsResult= DBManager::getResult($iSQL);

			while($PR = $partsResult->fetch_array()){
				array_push($partsInfoArray,
					array(
						"CPP_ID"=>$PR['CPP_ID'],
						"CPP_NM_SHORT"=>$PR['CPP_NM_SHORT'],
						"CPPS_NM_SHORT"=>$PR['CPPS_NM_SHORT'],
						"NGP_RATIO"=>$PR['NGP_RATIO'],
						"NGP_MEMO"=>$PR['NGP_MEMO'],
				));
			}
			$partsResult->close();  // 자원 반납
			unset($PR);
			array_push($rtnArray,
				array(
					"IDX"=>$IDX--,
					"MGG_ID"=>$RS['MGG_ID'],
					"CSD_ID"=>$RS['CSD_ID'],
					"CSD_NM"=>$RS['CSD_NM'],
					"CRR_1_NM"=>$RS['CRR_1_NM'],
					"CRR_2_NM"=>$RS['CRR_2_NM'],
					"CRR_3_NM"=>$RS['CRR_3_NM'],
					"MGG_ID"=>$RS['MGG_ID'],
					"MGG_USERID"=>$RS['MGG_USERID'],
					"MGG_BIZ_ID"=>$RS['MGG_BIZ_ID'],
					"MGG_PASSWD_AES"=>$RS['MGG_PASSWD_AES'],
					"MGG_CIRCUIT_ORDER"=>$RS['MGG_CIRCUIT_ORDER'],
					"MGG_NM"=>$RS['MGG_NM'],
					"MGG_BIZ_START_DT"=>$RS['MGG_BIZ_START_DT'],
					"MGG_BIZ_END_DT"=>$RS['MGG_BIZ_END_DT'],
					"RET_NM"=>$RS['RET_NM'],
					"MGG_RET_DT"=>$RS['MGG_RET_DT'],
					"MGG_RAT_DT"=>$RS['MGG_RAT_DT'],
					"RPET_NM"=>$RS['RPET_NM'],
					"MGG_RPET_DT"=>$RS['MGG_RPET_DT'],
					"MII_NM"=>$RS['MII_NM'],
					"MII_ID"=>$RS['MII_ID'],
					"RAT_NM"=>$RS['RAT_NM'],
					"MGG_RATIO"=>$RS['MGG_RATIO'],
					"MGG_RAT_NM"=>$RS['MGG_RAT_NM'],
					"MGG_OFFICE_TEL"=>$RS['MGG_OFFICE_TEL'],
					"MGG_OFFICE_FAX"=>$RS['MGG_OFFICE_FAX'],
					"MGG_BANK_NM_AES"=>$RS['MGG_BANK_NM_AES'],
					"MGG_BANK_NUM_AES"=>$RS['MGG_BANK_NUM_AES'],
					"MGG_LOGISTICS_YN"=>$RS['MGG_LOGISTICS_YN'],
					"MGGS_ADDR"=>$RS['MGGS_ADDR'],
					"MGG_CEO_NM_AES"=>$RS['MGG_CEO_NM_AES'],
					"MGG_CEO_TEL_AES"=>$RS['MGG_CEO_TEL_AES'],
					"MGG_KEYMAN_NM_AES"=>$RS['MGG_KEYMAN_NM_AES'],
					"MGG_KEYMAN_TEL_AES"=>$RS['MGG_KEYMAN_TEL_AES'],
					"MGG_CLAIM_NM_AES"=>$RS['MGG_CLAIM_NM_AES'],
					"MGG_CLAIM_TEL_AES"=>$RS['MGG_CLAIM_TEL_AES'],
					"MGG_FACTORY_NM_AES"=>$RS['MGG_FACTORY_NM_AES'],
					"MGG_FACTORY_TEL_AES"=>$RS['MGG_FACTORY_TEL_AES'],
					"MGG_PAINTER_NM_AES"=>$RS['MGG_PAINTER_NM_AES'],
					"MGG_PAINTER_TEL_AES"=>$RS['MGG_PAINTER_TEL_AES'],
					"MGG_METAL_NM_AES"=>$RS['MGG_METAL_NM_AES'],
					"MGG_METAL_TEL_AES"=>$RS['MGG_METAL_TEL_AES'],
					"ROT_NM"=>$RS['ROT_NM'],
					"MGG_INSU_MEMO"=>$RS['MGG_INSU_MEMO'],
					"MGG_MANUFACTURE_MEMO"=>$RS['MGG_MANUFACTURE_MEMO'],
					"SLL_ID"=>$RS['SLL_ID'],
					"SLL_NM"=>$RS['SLL_NM'],
					"MGG_FORCE_SEND_YN"=>$RS['MGG_FORCE_SEND_YN'],
					"MGG_BILL_ALL_YN"=>$RS['MGG_BILL_ALL_YN'],
					"MGG_BILL_MONEY_SHOW_YN"=>$RS['MGG_BILL_MONEY_SHOW_YN'],
					"MGG_WAREHOUSE_CNT"=>$RS['MGG_WAREHOUSE_CNT'],
					"RMT_NM"=>$RS['RMT_NM'],
					"MGG_BILL_MEMO"=>$RS['MGG_BILL_MEMO'],
					"MGG_GARAGE_MEMO"=>$RS['MGG_GARAGE_MEMO'],
					"RCT_NM"=>$RS['RCT_NM'],
					"MGG_FRONTIER_NM"=>$RS['MGG_FRONTIER_NM'],
					"MGG_COLLABO_NM"=>$RS['MGG_COLLABO_NM'],
					"MGG_BAILEE_NM"=>$RS['MGG_BAILEE_NM'],
					"MGG_BIZ_BEFORE_NM"=>$RS['MGG_BIZ_BEFORE_NM'],
					"MGG_CONTRACT_YN"=>$RS['MGG_CONTRACT_YN'],
					"MGG_CONTRACT"=>$RS['MGG_CONTRACT'],
					"MGG_END_AVG_BILL_CNT"=>$RS['MGG_END_AVG_BILL_CNT'],
					"MGG_END_AVG_BILL_PRICE"=>$RS['MGG_END_AVG_BILL_PRICE'],
					"MGG_END_OUT_OF_STOCK"=>$RS['MGG_END_OUT_OF_STOCK'],
					"MGG_GPS_LATITUDE"=>$RS['MGG_GPS_LATITUDE'],
					"MGG_GPS_LONGITUDE"=>$RS['MGG_GPS_LONGITUDE'],
					"RSR_NM"=>$RS['RSR_NM'],
					"MGG_OTHER_BIZ_NM"=>$RS['MGG_OTHER_BIZ_NM'],
					"MGG_SALES_NM"=>$RS['MGG_SALES_NM'],
					"RST_NM"=>$RS['RST_NM'],
					"MGG_SALES_MEET_LVL"=>$RS['MGG_SALES_MEET_LVL'],
					"MGG_SALES_MEET_NM_AES"=>$RS['MGG_SALES_MEET_NM_AES'],
					"MGG_SALES_MEET_TEL_AES"=>$RS['MGG_SALES_MEET_TEL_AES'],
					"RRT_NM"=>$RS['RRT_NM'],
					"MGG_SALES_WHYNOT"=>$RS['MGG_SALES_WHYNOT'],
					"NGV_VISIT_DT"=>$RS['NGV_VISIT_DT'],
					"NGV_VISIT_DT"=>$RS['NGV_VISIT_DT'],
					"NSU_SIGN_IMG"=>$RS['NSU_SIGN_IMG'],
					"NSU_SUPPLY_DT"=>$RS['NSU_SUPPLY_DT'],
					"NSU_FILE_IMG"=>$RS['NSU_FILE_IMG'],
					"NSU_VISIT_DT"=>$RS['NSU_VISIT_DT'],
					// "CPP_NM_SHORT"=>$RS['CPP_NM_SHORT'],
					// "CPPS_NM_SHORT"=>$RS['CPPS_NM_SHORT'],
					// "NGP_RATIO"=>$RS['NGP_RATIO'],
					// "NGP_MEMO"=>$RS['NGP_MEMO'],
					"PARTS_INFO"=>$partsInfoArray,
			));
		}
		return $rtnArray;
	}

	/**
	 * getList 쿼리만 리턴해주는 함수. 공용으로 사용하기 위해
	 */
	function getListSql($subSQL, $ARR){
		$NSU_SQL = "";
		$NSU_TABLE = "";
		if(!empty($ARR['NSU_ID'])){
			$NSU_SQL = ",NSU.NSU_SIGN_IMG \n,NSU.NSU_SUPPLY_DT\n,NSU.NSU_FILE_IMG\n,NSU.NSU_VISIT_DT";
			$NSU_TABLE = "LEFT JOIN NT_SALES.NS_SALES_UPLOAD NSU \n ON NSU.MGG_ID =  MGG.MGG_ID";
		}
		$iSQL = "SELECT 
					`CND`.`CSD_ID`,
					`CND`.`CSD_NM`,
					`CNR`.`CRR_1_ID`,
					`CNR`.`CRR_2_ID`,
				    `CNR`.`CRR_3_ID`,
					`CNR`.`CRR_1_NM`,
					`CNR`.`CRR_2_NM`,
					`CNR`.`CRR_3_NM`,
					`MGG`.`MGG_ID`,
					`MGG`.`MGG_USERID`,
					`MGG`.`MGG_BIZ_ID`,
					CAST(AES_DECRYPT(`MGG`.`MGG_PASSWD_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `MGG_PASSWD_AES`,
					`MGG`.`MGG_CIRCUIT_ORDER`,
					`MGG`.`MGG_NM`,
					`MGG`.`MGG_BILL_MEMO` AS 'MGG_AOS_MEMO',
					`MGG`.`SLL_ID`,
					NT_POINT.GET_LVL_NM(`MGG`.`SLL_ID`) AS 'SLL_NM',
					`MGGS`.`MGG_BIZ_START_DT`,
					`MGGS`.`MGG_BIZ_END_DT`,
					(SELECT `RET_NM` FROM `NT_CODE`.`REF_EXP_TYPE` WHERE `RET_ID` = `MGGS`.`RET_ID`) AS `RET_NM`,
					(SELECT `RET_ID` FROM `NT_CODE`.`REF_EXP_TYPE` WHERE `RET_ID` = `MGGS`.`RET_ID`) AS `RET_ID`,
					`MGGS`.`MGG_RET_DT`,
					`MGGS`.`MGG_RAT_DT`,
					(SELECT `RPET_NM` FROM `NT_CODE`.`REF_PAY_EXP_TYPE` WHERE `RPET_ID` = `MGGS`.`RPET_ID`) AS `RPET_NM`,
					(SELECT `RPET_ID` FROM `NT_CODE`.`REF_PAY_EXP_TYPE` WHERE `RPET_ID` = `MGGS`.`RPET_ID`) AS `RPET_ID`,
					`MGGS`.`MGG_RPET_DT`,
					(
						SELECT GROUP_CONCAT(`MII`.`MII_NM`) 
							FROM `NT_MAIN`.`NMG_GARAGE_PAYEXP_INSU` `MGI`,
										`NT_MAIN`.`NMI_INSU` `MII`
							WHERE `MGI`.`MGG_ID` = `MGGS`.`MGG_ID`
							AND `MGI`.`MII_ID` =  `MII`.`MII_ID`
					) AS `MII_NM` ,
					(
						SELECT GROUP_CONCAT(`MII`.`MII_ID`) 
							FROM `NT_MAIN`.`NMG_GARAGE_PAYEXP_INSU` `MGI`,
										`NT_MAIN`.`NMI_INSU` `MII`
							WHERE `MGI`.`MGG_ID` = `MGGS`.`MGG_ID`
							AND `MGI`.`MII_ID` =  `MII`.`MII_ID`
					) AS `MII_ID` ,
					(SELECT `RAT_NM` FROM `NT_CODE`.`REF_AOS_TYPE` WHERE `RAT_ID` = `MGGS`.`RAT_ID`) AS `RAT_NM`,
					(SELECT `RAT_ID` FROM `NT_CODE`.`REF_AOS_TYPE` WHERE `RAT_ID` = `MGGS`.`RAT_ID`) AS `RAT_ID`,
					`MGGS`.`MGG_RAT_NM`,
					CAST(AES_DECRYPT(`MGGS`.`MGG_BANK_OWN_NM_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `MGG_BANK_OWN_NM_AES`,
					CAST(AES_DECRYPT(`MGGS`.`MGG_BANK_NM_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `MGG_BANK_NM_AES`,
					CAST(AES_DECRYPT(`MGGS`.`MGG_BANK_NUM_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `MGG_BANK_NUM_AES`,
					IFNULL(IF(MGGS.RCT_ID = '10620' , MGG_END_AVG_BILL_CNT, ''),'') AS MGG_END_AVG_BILL_CNT,
					IFNULL(IF(MGGS.RCT_ID = '10620' , FORMAT(MGG_END_AVG_BILL_PRICE,0), ''),'') AS MGG_END_AVG_BILL_PRICE,
					IFNULL(IF(MGGS.RCT_ID = '10620' , MGG_END_OUT_OF_STOCK, ''),'') AS MGG_END_OUT_OF_STOCK,
					`MGGS`.`MGG_GPS_LATITUDE`,
					`MGGS`.`MGG_GPS_LONGITUDE`,
					`MGGS`.`MGG_LOGISTICS_YN`,
					`MGGS`.`MGG_ZIP_OLD`,
					`MGGS`.`MGG_ADDRESS_OLD_1`,
					`MGGS`.`MGG_ADDRESS_OLD_2`,
					`MGGS`.`MGG_ZIP_NEW`,
					`MGGS`.`MGG_ADDRESS_NEW_1`,
					`MGGS`.`MGG_ADDRESS_NEW_2`,
					`MGGS`.`MGG_OFFICE_TEL`,
					`MGGS`.`MGG_OFFICE_FAX`,
					CONCAT(`MGG_ADDRESS_NEW_1`, '  ', `MGG_ADDRESS_NEW_2`) `MGGS_ADDR`,
					CAST(AES_DECRYPT(`MGGS`.`MGG_CEO_NM_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `MGG_CEO_NM_AES`,
					CAST(AES_DECRYPT(`MGGS`.`MGG_CEO_TEL_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `MGG_CEO_TEL_AES`,
					CAST(AES_DECRYPT(`MGGS`.`MGG_KEYMAN_NM_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `MGG_KEYMAN_NM_AES`,
					CAST(AES_DECRYPT(`MGGS`.`MGG_KEYMAN_TEL_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `MGG_KEYMAN_TEL_AES`,
					CAST(AES_DECRYPT(`MGGS`.`MGG_CLAIM_NM_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `MGG_CLAIM_NM_AES`,
					CAST(AES_DECRYPT(`MGGS`.`MGG_CLAIM_TEL_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `MGG_CLAIM_TEL_AES`,
					CAST(AES_DECRYPT(`MGGS`.`MGG_FACTORY_NM_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `MGG_FACTORY_NM_AES`,
					CAST(AES_DECRYPT(`MGGS`.`MGG_FACTORY_TEL_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `MGG_FACTORY_TEL_AES`,
					CAST(AES_DECRYPT(`MGGS`.`MGG_PAINTER_NM_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `MGG_PAINTER_NM_AES`,
					CAST(AES_DECRYPT(`MGGS`.`MGG_PAINTER_TEL_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `MGG_PAINTER_TEL_AES`,
					CAST(AES_DECRYPT(`MGGS`.`MGG_METAL_NM_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `MGG_METAL_NM_AES`,
					CAST(AES_DECRYPT(`MGGS`.`MGG_METAL_TEL_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `MGG_METAL_TEL_AES`,
					(SELECT `ROT_NM` FROM `NT_CODE`.`REF_OWN_TYPE` WHERE `ROT_ID` = `MGGS`.`ROT_ID`) AS `ROT_NM`,
					(SELECT `ROT_ID` FROM `NT_CODE`.`REF_OWN_TYPE` WHERE `ROT_ID` = `MGGS`.`ROT_ID`) AS `ROT_ID`,
					`MGGS`.`MGG_INSU_MEMO`,
					`MGGS`.`MGG_MANUFACTURE_MEMO`,
					`MGG`.`MGG_FORCE_SEND_YN`,
					`MGG`.`MGG_BILL_ALL_YN`,
					`MGG`.`MGG_BILL_MONEY_SHOW_YN`,
					`MGGS`.`MGG_WAREHOUSE_CNT`,
					(SELECT `RMT_NM` FROM `NT_CODE`.`REF_MAIN_TARGET` WHERE `RMT_ID` = `MGGS`.`RMT_ID`) AS `RMT_NM`,
					(SELECT `RMT_ID` FROM `NT_CODE`.`REF_MAIN_TARGET` WHERE `RMT_ID` = `MGGS`.`RMT_ID`) AS `RMT_ID`,
					`MGGS`.`MGG_BILL_MEMO`,
					`MGGS`.`MGG_GARAGE_MEMO`,
					(SELECT `RCT_NM` FROM `NT_CODE`.`REF_CONTRACT_TYPE` WHERE `RCT_ID` = `MGGS`.`RCT_ID`) AS `RCT_NM`,
					(SELECT `RCT_ID` FROM `NT_CODE`.`REF_CONTRACT_TYPE` WHERE `RCT_ID` = `MGGS`.`RCT_ID`) AS `RCT_ID`,
					`MGGS`.`MGG_FRONTIER_NM`,
					`MGGS`.`MGG_COLLABO_NM`,
					`MGGS`.`MGG_BAILEE_NM`,
					`MGGS`.`MGG_BIZ_BEFORE_NM`,
					`MGGS`.`MGG_CONTRACT_YN`,
					`MGGS`.`MGG_CONTRACT_START_DT`,
					`MGGS`.`MGG_CONTRACT_END_DT`,
					`MGGS`.`MGG_RATIO`,
					CONCAT(`MGGS`.`MGG_CONTRACT_START_DT`, ' ~ ', `MGGS`.`MGG_CONTRACT_END_DT`) AS `MGG_CONTRACT`,
					(SELECT `RSR_NM` FROM `NT_CODE`.`REF_STOP_REASON` WHERE `RSR_ID` = `MGGS`.`RSR_ID`) AS `RSR_NM`,
					(SELECT `RSR_ID` FROM `NT_CODE`.`REF_STOP_REASON` WHERE `RSR_ID` = `MGGS`.`RSR_ID`) AS `RSR_ID`,
					`MGGS`.`MGG_OTHER_BIZ_NM`,
					`MGGS`.`MGG_SALES_NM`,
					(SELECT `RST_NM` FROM `NT_CODE`.`REF_SCALE_TYPE` WHERE `RST_ID` = `MGGS`.`RST_ID`) AS `RST_NM`,
					(SELECT `RST_ID` FROM `NT_CODE`.`REF_SCALE_TYPE` WHERE `RST_ID` = `MGGS`.`RST_ID`) AS `RST_ID`,
					`MGGS`.`MGG_SALES_MEET_LVL`,
					CAST(AES_DECRYPT(`MGGS`.`MGG_SALES_MEET_NM_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `MGG_SALES_MEET_NM_AES`,
					CAST(AES_DECRYPT(`MGGS`.`MGG_SALES_MEET_TEL_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `MGG_SALES_MEET_TEL_AES`,
					(SELECT `RRT_NM` FROM `NT_CODE`.`REF_REACTION_TYPE` WHERE `RRT_ID` = `MGGS`.`RRT_ID`) AS `RRT_NM`,
					(SELECT `RRT_ID` FROM `NT_CODE`.`REF_REACTION_TYPE` WHERE `RRT_ID` = `MGGS`.`RRT_ID`) AS `RRT_ID`,
					`MGGS`.`MGG_SALES_WHYNOT`,
					GROUP_CONCAT(MGGP.CPP_NM_SHORT SEPARATOR  '||') AS CPP_NM_SHORT,
					GROUP_CONCAT(MGGP.CPPS_NM_SHORT SEPARATOR  '||') AS CPPS_NM_SHORT,
					GROUP_CONCAT(MGGP.NGP_RATIO SEPARATOR  '||') AS NGP_RATIO,
                    GROUP_CONCAT(MGGP.NGP_MEMO SEPARATOR  '||') AS NGP_MEMO,
					(
						SELECT GROUP_CONCAT(`NGV_VISIT_DT`) 
							FROM `NT_MAIN`.`NMG_GARAGE_VISIT`
                            WHERE `MGG_ID` = `MGGS`.`MGG_ID`
					 ) AS `NGV_VISIT_DT` 
					 $NSU_SQL
				FROM 
					 `NT_MAIN`.`NMG_GARAGE_SUB` `MGGS`,
					 `NT_CODE`.`NCR_REGION` `CNR`,
					 `NT_CODE`.`NCS_DIVISION` `CND`,
					 `NT_MAIN`.`NMG_GARAGE` `MGG`
				LEFT JOIN (
						SELECT 
							MGG_ID,
							CONCAT(CPP_NM_SHORT,':' ,GROUP_CONCAT(CPPS_NM_SHORT))AS CPP_NM_SHORT,
							GROUP_CONCAT(IF(NGP_DEFAULT_YN = 'Y' , CPPS_NM_SHORT, '') SEPARATOR '')AS CPPS_NM_SHORT,
							`MGGP`.`NGP_RATIO`,
							MGGP.NGP_MEMO
							
						FROM `NT_MAIN`.`NMG_GARAGE_PART` `MGGP`,
							`NT_CODE`.`NCP_PART` `CPP`,
							`NT_CODE`.`NCP_PART_SUB` `CPPS`
						WHERE `MGGP`.`CPP_ID` =  `CPP`.`CPP_ID`
						AND `MGGP`.`CPPS_ID` =  `CPPS`.`CPPS_ID`
						AND CPP.DEL_YN = 'N'
						GROUP BY CPP.CPP_ID, MGGP.MGG_ID
						ORDER BY `CPP`.`CPP_ORDER_NUM` ASC
					)AS MGGP
				ON MGGP.MGG_ID = MGG.MGG_ID
				$NSU_TABLE 
				WHERE `CND`.`CSD_ID` = `MGG`.`CSD_ID`
				AND `CNR`.`CRR_1_ID` = `MGG`.`CRR_1_ID`
				AND `CNR`.`CRR_2_ID` = `MGG`.`CRR_2_ID`
				AND `CNR`.`CRR_3_ID` = `MGG`.`CRR_3_ID`
				AND `MGGS`.`MGG_ID` = `MGG`.`MGG_ID`
				AND `MGG`.`DEL_YN` = 'N'
				$subSQL 
				"
		;
		// echo $iSQL;
		return $iSQL;
	}

	/**
	 * 조회 
	 * @param {$ARR}
	 * @param {CURR_PAGE : 현재페이지, PER_PAGE : 몇개를 조회할 것인지, KEYWORD : 검색조건}
	 */
	function getList($ARR) {
		$LimitStart = ($ARR['CURR_PAGE']-1) * $ARR['PER_PAGE'];
		$LimitEnd   = $ARR['PER_PAGE'];
		$tmpKeyword = "";
		
		/** 기간검색 */
		if($ARR['MY_BIZ_DT_SEARCH'] != '') {
			if($ARR['BIZ_START_DT_SEARCH'] !='' && $ARR['BIZ_END_DT_SEARCH'] !=''){
				$tmpKeyword .= "AND DATE(`MGGS`.".$ARR['MY_BIZ_DT_SEARCH'].") BETWEEN '".addslashes($ARR['BIZ_START_DT_SEARCH'])."' AND '".addslashes($ARR['BIZ_END_DT_SEARCH'])."'";
			}else if($ARR['BIZ_START_DT_SEARCH'] !='' && $ARR['BIZ_END_DT_SEARCH'] ==''){
				$tmpKeyword .= "AND DATE(`MGGS`.".$ARR['MY_BIZ_DT_SEARCH'].") >= '".addslashes($ARR['BIZ_START_DT_SEARCH'])."'";
			}else{
				$tmpKeyword .= "AND DATE(`MGGS`.".$ARR['MY_BIZ_DT_SEARCH'].") <= '".addslashes($ARR['BIZ_END_DT_SEARCH'])."'";
			}
		} 
		
		//구분검색
		if($ARR['CSD_ID_SEARCH'] != '') {
			$tmpKeyword .= "AND (";
			foreach ($ARR['CSD_ID_SEARCH'] as $index => $CSD_ID) {
				if($index == 0){
					$tmpKeyword .= "(";
					$tmpKeyword .= "INSTR(`MGG`.`CSD_ID`,'".$CSD_ID."')>0 ";
				}else{
					$tmpKeyword .= " OR(";
					$tmpKeyword .= "  INSTR(`MGG`.`CSD_ID`,'".$CSD_ID."')>0 ";
				}
				$tmpKeyword .= ")";
			}
			$tmpKeyword .= ")";
		} 

		//지역주소검색
		if($ARR['CRR_ID_SEARCH'] !=  '' || $ARR['CRR_ID_SEARCH'] != null) {
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

		//공업사명검색
		if($ARR['MGG_NM_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(`MGG`.`MGG_NM`,'".addslashes($ARR['MGG_NM_SEARCH'])."')>0 ";	
		} 
		
		//사업자번호검색
		if($ARR['MGG_BIZ_ID_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(`MGG`.`MGG_BIZ_ID`,'".addslashes($ARR['MGG_BIZ_ID_SEARCH'])."')>0 ";	
		} 

		//청구식검색
		if($ARR['RET_ID_SEARCH'] != '') {
			$tmpKeyword .= "AND (";
			foreach ($ARR['RET_ID_SEARCH'] as $index => $RET_ID) {
				if($index == 0){
					$tmpKeyword .= "(";
					$tmpKeyword .= "INSTR(`MGGS`.`RET_ID`,'".$RET_ID."')>0 ";
				}else{
					$tmpKeyword .= " OR(";
					$tmpKeyword .= "  INSTR(`MGGS`.`RET_ID`,'".$RET_ID."')>0 ";
				}
				$tmpKeyword .= ")";
			}
			$tmpKeyword .= ")";
		}

		//지급식 검색
		if($ARR['RPET_ID_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(`MGGS`.`RPET_ID`,'".addslashes($ARR['RPET_ID_SEARCH'])."')>0 ";	
		} 

		//AOS
		if($ARR['RAT_ID_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(`MGGS`.`RAT_ID`,'".addslashes($ARR['RAT_ID_SEARCH'])."')>0 ";	
		} 

		//택배여부 검색
		if($ARR['MGG_LOGISTICS_YN_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(`MGGS`.`MGG_LOGISTICS_YN`,'".addslashes($ARR['MGG_LOGISTICS_YN_SEARCH'])."')>0 ";	
		} 

		//부품장치장소개수 검색
		if($ARR['MGG_WAREHOUSE_CNT_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(`MGGS`.`MGG_WAREHOUSE_CNT`,'".addslashes($ARR['MGG_WAREHOUSE_CNT_SEARCH'])."')>0 ";	
		} 

		//주요장비 검색
		if($ARR['RMT_ID_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(`MGGS`.`RMT_ID`,'".addslashes($ARR['RMT_ID_SEARCH'])."')>0 ";	
		} 

		//자가여부 검색
		if($ARR['ROT_ID_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(`MGGS`.`ROT_ID`,'".addslashes($ARR['ROT_ID_SEARCH'])."')>0 ";	
		} 

		//보험사협력업체 검색
		if($ARR['MGG_INSU_MEMO_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(`MGGS`.`MGG_INSU_MEMO`,'".addslashes($ARR['MGG_INSU_MEMO_SEARCH'])."')>0 ";	
		} 

		//제조사협력업체 검색
		if($ARR['MGG_MANUFACTURE_MEMO_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(`MGGS`.`MGG_MANUFACTURE_MEMO`,'".addslashes($ARR['MGG_MANUFACTURE_MEMO_SEARCH'])."')>0 ";	
		} 

		//강제전송여부 검색
		if($ARR['MGG_FORCE_SEND_YN_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(`MGG`.`MGG_FORCE_SEND_YN`,'".addslashes($ARR['MGG_FORCE_SEND_YN_SEARCH'])."')>0 ";
		} 

		//청구서전부전송여부 검색 
		if($ARR['MGG_BILL_ALL_YN_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(`MGG`.`MGG_BILL_ALL_YN`,'".addslashes($ARR['MGG_BILL_ALL_YN_SEARCH'])."')>0 ";	
		} 

		//청구금액표시여부 검색 
		if($ARR['MGG_BILL_MONEY_SHOW_YN_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(`MGG`.`MGG_BILL_MONEY_SHOW_YN`,'".addslashes($ARR['MGG_BILL_MONEY_SHOW_YN_SEARCH'])."')>0 ";	
		} 

		//거래여부 검색 
		if($ARR['RCT_ID_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(`MGGS`.`RCT_ID`,'".addslashes($ARR['RCT_ID_SEARCH'])."')>0 ";	
		} 

		//개척자 검색 
		if($ARR['MGG_FRONTIER_NM_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(`MGGS`.`MGG_FRONTIER_NM`,'".addslashes($ARR['MGG_FRONTIER_NM_SEARCH'])."')>0 ";	
		} 

		//협조자 검색 
		if($ARR['MGG_COLLABO_NM_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(`MGGS`.`MGG_COLLABO_NM`,'".addslashes($ARR['MGG_COLLABO_NM_SEARCH'])."')>0 ";	
		} 

		//수탁자 검색 
		if($ARR['MGG_BAILEE_NM_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(`MGGS`.`MGG_BAILEE_NM`,'".addslashes($ARR['MGG_BAILEE_NM_SEARCH'])."')>0 ";	
		} 

		//전거래처 검색 
		if($ARR['MGG_BIZ_BEFORE_NM_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(`MGGS`.`MGG_BIZ_BEFORE_NM`,'".addslashes($ARR['MGG_BIZ_BEFORE_NM_SEARCH'])."')>0 ";	
		} 

		//계약여부
		if($ARR['MGG_CONTRACT_YN_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(`MGGS`.`MGG_CONTRACT_YN`,'".addslashes($ARR['MGG_CONTRACT_YN_SEARCH'])."')>0 ";	
		} 

		//거래중단사유 검색 
		if($ARR['RSR_ID_SEARCH'] != '') {
			$tmpKeyword .= "AND (";
			foreach ($ARR['RSR_ID_SEARCH'] as $index => $RSR_ID) {
				if($index == 0){
					$tmpKeyword .= "(";
					$tmpKeyword .= "INSTR(`MGGS`.`RSR_ID`,'".$RSR_ID."')>0 ";
				}else{
					$tmpKeyword .= " OR(";
					$tmpKeyword .= "  INSTR(`MGGS`.`RSR_ID`,'".$RERSR_IDT_ID."')>0 ";
				}
				$tmpKeyword .= ")";
			}
			$tmpKeyword .= ")";	
		} 

		//타사거래 검색 
		if($ARR['MGG_OTHER_BIZ_NM_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(`MGGS`.`MGG_OTHER_BIZ_NM`,'".addslashes($ARR['MGG_OTHER_BIZ_NM_SEARCH'])."')>0 ";	
		} 



		// 페이징처리를 위해 전체데이터
		$iSQL = "SELECT COUNT(*)AS CNT FROM (
					SELECT
						MGG.MGG_ID 
					FROM
						`NT_MAIN`.`NMG_GARAGE` `MGG`,
						`NT_MAIN`.`NMG_GARAGE_SUB` `MGGS`
					LEFT JOIN (
						SELECT 
							MGG_ID,
							CONCAT(CPP_NM_SHORT,':' ,GROUP_CONCAT(CPPS_NM_SHORT))AS CPP_NM_SHORT,
							GROUP_CONCAT(IF(NGP_DEFAULT_YN = 'Y' , CPPS_NM_SHORT, '') SEPARATOR '')AS CPPS_NM_SHORT,
							`MGGP`.`NGP_RATIO`,
							MGGP.NGP_MEMO
							
						FROM `NT_MAIN`.`NMG_GARAGE_PART` `MGGP`,
							`NT_CODE`.`NCP_PART` `CPP`,
							`NT_CODE`.`NCP_PART_SUB` `CPPS`
						WHERE `MGGP`.`CPP_ID` =  `CPP`.`CPP_ID`
						AND `MGGP`.`CPPS_ID` =  `CPPS`.`CPPS_ID`
						AND CPP.DEL_YN = 'N'
						GROUP BY CPP.CPP_ID, MGGP.MGG_ID
						ORDER BY `CPP`.`CPP_ORDER_NUM` ASC
					)AS MGGP
					ON MGGP.MGG_ID = MGGS.MGG_ID
					WHERE  `MGG`.`DEL_YN` = 'N'
					AND `MGGS`.`MGG_ID` = `MGG`.`MGG_ID`
					$tmpKeyword
					GROUP BY MGG.MGG_ID
				) AS C
				;"
		;
		// echo $iSQL;
		try{
			$RS_T	= DBManager::getRecordSet($iSQL);
		} catch (Exception $e){
			echo "error : ".$e;
			return ERROR_DB_SELECT_DATA;   //ERROR_DB_SELECT_DATA
		}
	
		
		$TOTAL_LIST_COUNT = $RS_T['CNT'];         // 총 몇개인지
		$IDX = $RS_T['CNT']-$LimitStart;          // 해당페이지 index
		unset($RS_T);
		///

		/** 전체리스트가 아닌경우에만 limitSql 적용 */
		$LIMIT_SQL = "";
		if($ARR['REQ_MODE'] == CASE_LIST){
			$LIMIT_SQL = "LIMIT $LimitStart,$LimitEnd";
		}

		
		// 리스트 가져오기
		$ODER_SQL = "ORDER BY `MGGS`.`MGG_BIZ_START_DT` DESC ";
		$iSQL = $this->getListSql('', $ARR).$tmpKeyword." 	GROUP BY MGG.MGG_ID ".$ODER_SQL.$LIMIT_SQL.";";
	
		
		$Result= DBManager::getResult($iSQL);
		///

		$rtnArray = $this->getListColumn($Result, $IDX );
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

	/**
	 * 해당데이터 조회 
	 * @param {$ARR}
	 * @param {ALLM_ID : 조회할 해당 데이터 ID}
	 */
	function getData($ARR) {
		// 리스트 가져오기
		$subSQL = " AND `MGGS`.`MGG_ID`='".addslashes($ARR['MGG_ID'])."'";
		if(!empty($ARR['NSU_ID'])){
			$subSQL .= " AND `NSU`.`NSU_ID`='".addslashes($ARR['NSU_ID'])."'";
		}
		
		$iSQL = $this->getListSql($subSQL, $ARR).";";
		// echo $iSQL;
		$RS= DBManager::getRecordSet($iSQL);

		if(empty($RS)) {
			unset($RS);
			return HAVE_NO_DATA;
		}else{
			$iSQL = "SELECT 
						`CPP`.`CPP_NM_SHORT`,
						`CPPS`.`CPPS_NM_SHORT`,
						`CPPS`.`CPPS_NM`,
						`MGGP`.`MGG_ID`,
						`MGGP`.`CPP_ID`,
						`MGGP`.`CPPS_ID`,
						`MGGP`.`NGP_DEFAULT_YN`,
						`MGGP`.`NGP_RATIO`,
						`MGGP`.`NGP_MEMO`
					FROM `NT_MAIN`.`NMG_GARAGE_PART` `MGGP`,
						`NT_CODE`.`NCP_PART` `CPP`,
						`NT_CODE`.`NCP_PART_SUB` `CPPS`
					WHERE `MGGP`.`CPP_ID` =  `CPP`.`CPP_ID`
					AND `MGGP`.`CPPS_ID` =  `CPPS`.`CPPS_ID`
					AND `MGGP`.`MGG_ID` = '".addslashes($ARR['MGG_ID'])."'
					ORDER BY `MGGP`.`CPP_ID` DESC
					;"
			;

			$Result= DBManager::getResult($iSQL);
			
			$partsArray = array();
			while($RS_ = $Result->fetch_array()){
				array_push($partsArray,
					array(
						"CPP_NM_SHORT"=>$RS_['CPP_NM_SHORT'],
						"CPPS_NM_SHORT"=>$RS_['CPPS_NM_SHORT'],
						"CPPS_NM"=>$RS_['CPPS_NM'],
						"MGG_ID"=>$RS_['MGG_ID'],
						"CPP_ID"=>$RS_['CPP_ID'],
						"CPPS_ID"=>$RS_['CPPS_ID'],
						"NGP_DEFAULT_YN"=>$RS_['NGP_DEFAULT_YN'],
						"NGP_RATIO"=>$RS_['NGP_RATIO'],
						"NGP_MEMO"=>$RS_['NGP_MEMO'],
				));
			}

			$jsonArray = array();
			array_push($jsonArray,$RS);
			array_push($jsonArray,$partsArray);

			return  $jsonArray;
			
		}
		
		
	}


	/**
	 * 보험사 테이블 insert (insert/update시 사용)
	 */
	function insertMII($ARR){
		$iSQL = '';
		foreach ($ARR['MII_ID'] as $index => $miiId) {
			$iSQL .= "INSERT INTO `NT_MAIN`.`NMG_GARAGE_PAYEXP_INSU`
					(
						`MGG_ID`,
						`MII_ID`,
						`WRT_DTHMS`,
						`LAST_DTHMS`,
						`WRT_USERID`
					)
					VALUES
					(
						'".addslashes($ARR['MGG_ID'])."',
						'".addslashes($miiId)."',
						NOW(),
						NOW(),
						'".$_SESSION['USERID']."'
					);"
			;
		}
		return $iSQL ;
	}

	/**
	 * 공급 및 사용부품 테이블 insert (insert/update시 사용)
	 */
	function insertPARTS($ARR){
		$iSQL = '';
		foreach ($ARR['CPPS_ID'] as $index => $PARTS) {
			$ITEM = json_decode($PARTS);
			$DEFAULT_YN = ($ITEM->CPPS_ID === $ARR['NGP_DEFAULT_YN'][$ITEM->IDX] ? "Y" : "N");
			$NGP_RATIO = (empty($ARR['NGP_RATIO'][$ITEM->IDX]) ? 1 : $ARR['NGP_RATIO'][$ITEM->IDX]);

			$iSQL .= "INSERT INTO `NT_MAIN`.`NMG_GARAGE_PART`
					(
						`MGG_ID`,
						`CPP_ID`,
						`CPPS_ID`,
						`NGP_DEFAULT_YN`,
						`NGP_RATIO`,
						`NGP_MEMO`,
						`WRT_DTHMS`,
						`LAST_DTHMS`
					)
					VALUES
					(
						'".addslashes($ARR['MGG_ID'])."',
						'".addslashes($ITEM->CPP_ID)."',
						'".addslashes($ITEM->CPPS_ID)."',
						'".addslashes($DEFAULT_YN)."',
						'".addslashes($NGP_RATIO)."',
						'".addslashes($ARR['NGP_MEMO'][$ITEM->IDX])."',
						NOW(),
						NOW()
					);"
			;
		}
		return $iSQL ;
	}

	/**
	 * 임원방문테이블 insert (insert/update시 사용)
	 */
	function insertVISIT($ARR){
		
		$iSQL = '';
		foreach ($ARR['NGV_VISIT_DT'] as $index => $VISIT) {
			$iSQL .= "INSERT INTO `NT_MAIN`.`NMG_GARAGE_VISIT`
					(
						`NGV_VISIT_DT`,
						`MGG_ID`,
						`WRT_DTHMS`,
						`LAST_DTHMS`,
						`WRT_USERID`
					)
					VALUES
					(
						'".addslashes($VISIT)."',
						'".addslashes($ARR['MGG_ID'])."',
						NOW(),
						NOW(),
						'".$_SESSION['USERID']."'
					);"
			;
		}
		return $iSQL ;
	}

	/**
	 * insert 쿼리 리턴
	 * @param {$ARR : request 데이터}
	 */
	function insertData($ARR){
		
		$iSQL = '';

		if(!empty($ARR['IS_EXIST_CIRCUIT_ORDER'])){
			$iSQL .= "UPDATE  `NT_MAIN`.`NMG_GARAGE` 
					SET 
						`MGG_CIRCUIT_ORDER` = `MGG_CIRCUIT_ORDER`+1
					WHERE `MGG_CIRCUIT_ORDER` IN
						(
							SELECT `MGG_CIRCUIT_ORDER` FROM 
							(
								SELECT
									@rownum:=@rownum+1 AS IDX,
									`MGG_CIRCUIT_ORDER`,
									`MGG_CIRCUIT_ORDER` - @rownum AS GRP
								FROM `NT_MAIN`.`NMG_GARAGE`, (SELECT @rownum:=0) IDX
								WHERE `DEL_YN` = 'N' 
								AND `CSD_ID` = '".addslashes($ARR['CSD_ID'])."'
								AND `MGG_CIRCUIT_ORDER` >= '".addslashes($ARR['MGG_CIRCUIT_ORDER'])."' ORDER BY MGG_CIRCUIT_ORDER ASC 
								) AS CMNT WHERE GRP = '".addslashes($ARR['MGG_CIRCUIT_ORDER'])."'-1
						)
					;"
			;
		}

		$CRR = explode(',', $ARR['SELECT_CRR_ID']);
		$PWD_AES = "AES_ENCRYPT('".$ARR['MGG_PASSWD_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))" ;
		$MGG_FORCE_SEND_YN = (empty($ARR['MGG_FORCE_SEND_YN']) ? 'N' : $ARR['MGG_FORCE_SEND_YN']); 
		$MGG_BILL_MONEY_SHOW_YN = (empty($ARR['MGG_BILL_MONEY_SHOW_YN']) ? 'N' : $ARR['MGG_BILL_MONEY_SHOW_YN']); 
		$MGG_BILL_ALL_YN = (empty($ARR['MGG_BILL_ALL_YN']) ? 'N' : $ARR['MGG_BILL_ALL_YN']); 
		$SLL_ID = (empty($ARR['SLL_ID']) ? "NULL" : "'".addslashes($ARR['SLL_ID'])."'"); 
		
		$iSQL .= "INSERT INTO `NT_MAIN`.`NMG_GARAGE`
				(
					`MGG_ID`,
					`CSD_ID`,
					`CRR_1_ID`,
					`CRR_2_ID`,
					`CRR_3_ID`,
					`MGG_USERID`,
					`MGG_PASSWD_AES`,
					`MGG_BIZ_ID`,
					`MGG_CIRCUIT_ORDER`,
					`MGG_NM`,
					`MGG_FORCE_SEND_YN`,
					`MGG_BILL_ALL_YN`,
					`MGG_BILL_MONEY_SHOW_YN`,
					`SLL_ID`,
					`MGG_BILL_MEMO`,
					`WRT_DTHMS`,
					`LAST_DTHMS`,
					`WRT_USERID`,
					`DEL_YN`
				)
				VALUES
				(
					'".addslashes($ARR['MGG_ID'])."',
					'".addslashes($ARR['CSD_ID'])."',
					'".$CRR[0]."',
					'".$CRR[1]."',
					'".$CRR[2]."',
					'".addslashes($ARR['MGG_USERID'])."',
					".$PWD_AES.",
					'".addslashes($ARR['MGG_BIZ_ID'])."',
					'".addslashes($ARR['MGG_CIRCUIT_ORDER'])."',
					'".addslashes($ARR['MGG_NM'])."',
					'".$MGG_FORCE_SEND_YN."',
					'".$MGG_BILL_ALL_YN."',
					'".$MGG_BILL_MONEY_SHOW_YN."',
					".$SLL_ID.",
					'".addslashes($ARR['MGG_AOS_MEMO'])."',
					NOW(),
					NOW(),
					'".$_SESSION['USERID']."' ,
					'N'
				);"
		;

		$RET_ID = ($ARR['RET_ID'] == '' ? "NULL" : "'".$ARR['RET_ID']."'");
		$RPET_ID = ($ARR['RPET_ID'] == '' ? "NULL" : "'".$ARR['RPET_ID']."'");
		$RAT_ID = ($ARR['RAT_ID'] == '' ? "NULL" : "'".$ARR['RAT_ID']."'");
		$RMT_ID = ($ARR['RMT_ID'] == '' ? "NULL" : "'".$ARR['RMT_ID']."'");
		$ROT_ID = ($ARR['ROT_ID'] == '' ? "NULL" : "'".$ARR['ROT_ID']."'");
		$RCT_ID = ($ARR['RCT_ID'] == '' ? "NULL" : "'".$ARR['RCT_ID']."'");
		$RSR_ID = ($ARR['RSR_ID'] == '' ? "NULL" : "'".$ARR['RSR_ID']."'");
		$RST_ID = ($ARR['RST_ID'] == '' ? "NULL" : "'".$ARR['RST_ID']."'");
		$RRT_ID = ($ARR['RRT_ID'] == '' ? "NULL" : "'".$ARR['RRT_ID']."'");
		$MGG_RAT_DT = ($ARR['MGG_RAT_DT'] == '' ? "NULL" : "'".$ARR['MGG_RAT_DT']."'");
		$MGG_RET_DT = ($ARR['MGG_RET_DT'] == '' ? "NULL" : "'".$ARR['MGG_RET_DT']."'");
		$MGG_RPET_DT = ($ARR['MGG_RPET_DT'] == '' ? "NULL" : "'".$ARR['MGG_RPET_DT']."'");
		$MGG_BIZ_START_DT = ($ARR['MGG_BIZ_START_DT'] == '' ? "NULL" : "'".$ARR['MGG_BIZ_START_DT']."'");
		$MGG_CONTRACT_START_DT = ($ARR['MGG_CONTRACT_START_DT'] == '' ? "NULL" : "'".$ARR['MGG_CONTRACT_START_DT']."'");
		$MGG_CONTRACT_END_DT = ($ARR['MGG_CONTRACT_END_DT'] == '' ? "NULL" : "'".$ARR['MGG_CONTRACT_END_DT']."'");
		$MGG_BIZ_END_DT = ($ARR['MGG_BIZ_END_DT'] == '' ? "NULL" : "'".$ARR['MGG_BIZ_END_DT']."'");
		$MGG_CONTRACT_YN = (empty($ARR['MGG_CONTRACT_YN']) ? 'N' : $ARR['MGG_CONTRACT_YN']); 
		$MGG_LOGISTICS_YN = (empty($ARR['MGG_LOGISTICS_YN']) ? 'N' : $ARR['MGG_LOGISTICS_YN']); 
		$MGG_END_AVG_BILL_CNT = empty($ARR['MGG_END_AVG_BILL_CNT']) ? "NULL" : ROUND($ARR['MGG_END_AVG_BILL_CNT'],0);
		$MGG_END_AVG_BILL_PRICE = empty($ARR['MGG_END_AVG_BILL_PRICE']) ? "NULL" : $ARR['MGG_END_AVG_BILL_PRICE'];
		$MGG_END_OUT_OF_STOCK = empty($ARR['MGG_END_OUT_OF_STOCK']) ? "NULL" : "'".$ARR['MGG_END_OUT_OF_STOCK']."'";
		$MGG_GPS_LATITUDE = empty($ARR['MGG_GPS_LATITUDE']) ? "NULL" : $ARR['MGG_GPS_LATITUDE'];
		$MGG_GPS_LONGITUDE = empty($ARR['MGG_GPS_LONGITUDE']) ? "NULL" : "'".$ARR['MGG_GPS_LONGITUDE']."'";

		$MGG_CEO_NM_AES = "AES_ENCRYPT('".$ARR['MGG_CEO_NM_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))" ;
		$MGG_CEO_TEL_AES = "AES_ENCRYPT('".$ARR['MGG_CEO_TEL_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))" ;
		$MGG_KEYMAN_NM_AES = "AES_ENCRYPT('".$ARR['MGG_KEYMAN_NM_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))" ;
		$MGG_KEYMAN_TEL_AES = "AES_ENCRYPT('".$ARR['MGG_KEYMAN_TEL_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))" ;
		$MGG_CLAIM_NM_AES = "AES_ENCRYPT('".$ARR['MGG_CLAIM_NM_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))" ;
		$MGG_CLAIM_TEL_AES = "AES_ENCRYPT('".$ARR['MGG_CLAIM_TEL_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))" ;
		$MGG_FACTORY_NM_AES = "AES_ENCRYPT('".$ARR['MGG_FACTORY_NM_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))" ;
		$MGG_FACTORY_TEL_AES = "AES_ENCRYPT('".$ARR['MGG_FACTORY_TEL_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))" ;
		$MGG_PAINTER_NM_AES = "AES_ENCRYPT('".$ARR['MGG_PAINTER_NM_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))" ;
		$MGG_PAINTER_TEL_AES = "AES_ENCRYPT('".$ARR['MGG_PAINTER_TEL_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))" ;
		$MGG_METAL_NM_AES = "AES_ENCRYPT('".$ARR['MGG_METAL_NM_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))" ;
		$MGG_METAL_TEL_AES = "AES_ENCRYPT('".$ARR['MGG_METAL_TEL_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))" ;
		$MGG_BANK_NM_AES = "AES_ENCRYPT('".$ARR['MGG_BANK_NM_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))" ;
		$MGG_BANK_NUM_AES = "AES_ENCRYPT('".$ARR['MGG_BANK_NUM_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))" ;
		$MGG_BANK_OWN_NM_AES = "AES_ENCRYPT('".$ARR['MGG_BANK_OWN_NM_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))" ;
		$MGG_SALES_MEET_NM_AES = "AES_ENCRYPT('".$ARR['MGG_SALES_MEET_NM_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))" ;
		$MGG_SALES_MEET_TEL_AES = "AES_ENCRYPT('".$ARR['MGG_SALES_MEET_TEL_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))" ;

		
		$iSQL .= "INSERT INTO `NT_MAIN`.`NMG_GARAGE_SUB`
				(
					`MGG_ID`,
					`RET_ID`,
					`MGG_RET_DT`,
					`RPET_ID`,
					`MGG_RPET_DT`,
					`RAT_ID`,
					`MGG_RAT_DT`,
					`MGG_RAT_NM`,
					`MGG_LOGISTICS_YN`,
					`MGG_ZIP_OLD`,
					`MGG_ADDRESS_OLD_1`,
					`MGG_ADDRESS_OLD_2`,
					`MGG_ZIP_NEW`,
					`MGG_ADDRESS_NEW_1`,
					`MGG_ADDRESS_NEW_2`,
					`MGG_OFFICE_TEL`,
					`MGG_OFFICE_FAX`,
					`MGG_CEO_NM_AES`,
					`MGG_CEO_TEL_AES`,
					`MGG_KEYMAN_NM_AES`,
					`MGG_KEYMAN_TEL_AES`,
					`MGG_CLAIM_NM_AES`,
					`MGG_CLAIM_TEL_AES`,
					`MGG_FACTORY_NM_AES`,
					`MGG_FACTORY_TEL_AES`,
					`MGG_PAINTER_NM_AES`,
					`MGG_PAINTER_TEL_AES`,
					`MGG_METAL_NM_AES`,
					`MGG_METAL_TEL_AES`,
					`MGG_WAREHOUSE_CNT`,
					`RMT_ID`,
					`ROT_ID`,
					`MGG_INSU_MEMO`,
					`MGG_MANUFACTURE_MEMO`,
					`MGG_BILL_MEMO`,
					`MGG_GARAGE_MEMO`,
					`RCT_ID`,
					`MGG_BIZ_START_DT`,
					`MGG_FRONTIER_NM`,
					`MGG_COLLABO_NM`,
					`MGG_BAILEE_NM`,
					`MGG_BIZ_BEFORE_NM`,
					`MGG_CONTRACT_YN`,
					`MGG_CONTRACT_START_DT`,
					`MGG_CONTRACT_END_DT`,
					`MGG_RATIO`,
					`MGG_BANK_NM_AES`,
					`MGG_BANK_NUM_AES`,
					`MGG_BANK_OWN_NM_AES`,
					`MGG_BIZ_END_DT`,
					`RSR_ID`,
					`MGG_OTHER_BIZ_NM`,
					`MGG_SALES_NM`,
					`RST_ID`,
					`MGG_SALES_MEET_LVL`,
					`MGG_SALES_MEET_NM_AES`,
					`MGG_SALES_MEET_TEL_AES`,
					`RRT_ID`,
					`MGG_SALES_WHYNOT`,
					`MGG_END_AVG_BILL_CNT`,
					`MGG_END_AVG_BILL_PRICE`,
					`MGG_END_OUT_OF_STOCK`,
					`MGG_GPS_LATITUDE`,
					`MGG_GPS_LONGITUDE`,
					`WRT_DTHMS`,
					`LAST_DTHMS`,
					`WRT_USERID`,
					`DEL_YN`
				)
				VALUES
				(
					'".addslashes($ARR['MGG_ID'])."',
					".$RET_ID.",
					".$MGG_RET_DT.",
					".$RPET_ID.",
					".$MGG_RPET_DT.",
					".$RAT_ID.",
					".$MGG_RAT_DT.",
					'".addslashes($ARR['MGG_RAT_NM'])."',
					'".$MGG_LOGISTICS_YN."',
					'".addslashes($ARR['MGG_ZIP_OLD'])."',
					'".addslashes($ARR['MGG_ADDRESS_OLD_1'])."',
					'".addslashes($ARR['MGG_ADDRESS_OLD_2'])."',
					'".addslashes($ARR['MGG_ZIP_NEW'])."',
					'".addslashes($ARR['MGG_ADDRESS_NEW_1'])."',
					'".addslashes($ARR['MGG_ADDRESS_NEW_2'])."',
					'".addslashes($ARR['MGG_OFFICE_TEL'])."',
					'".addslashes($ARR['MGG_OFFICE_FAX'])."',
					".$MGG_CEO_NM_AES.",
					".$MGG_CEO_TEL_AES.",
					".$MGG_KEYMAN_NM_AES.",
					".$MGG_KEYMAN_TEL_AES.",
					".$MGG_CLAIM_NM_AES.",
					".$MGG_CLAIM_TEL_AES.",
					".$MGG_FACTORY_NM_AES.",
					".$MGG_FACTORY_TEL_AES.",
					".$MGG_PAINTER_NM_AES.",
					".$MGG_PAINTER_TEL_AES.",
					".$MGG_METAL_NM_AES.",
					".$MGG_METAL_TEL_AES.",
					'".addslashes($ARR['MGG_WAREHOUSE_CNT'])."',
					".$RMT_ID.",
					".$ROT_ID.",
					'".addslashes($ARR['MGG_INSU_MEMO'])."',
					'".addslashes($ARR['MGG_MANUFACTURE_MEMO'])."',
					'".addslashes($ARR['MGG_BILL_MEMO'])."',
					'".addslashes($ARR['MGG_GARAGE_MEMO'])."',
					".$RCT_ID.",
					".$MGG_BIZ_START_DT.",
					'".addslashes($ARR['MGG_FRONTIER_NM'])."',
					'".addslashes($ARR['MGG_COLLABO_NM'])."',
					'".addslashes($ARR['MGG_BAILEE_NM'])."',
					'".addslashes($ARR['MGG_BIZ_BEFORE_NM'])."',
					'".$MGG_CONTRACT_YN."',
					".$MGG_CONTRACT_START_DT.",
					".$MGG_CONTRACT_END_DT.",
					'".addslashes($ARR['MGG_RATIO'])."',
					".$MGG_BANK_NM_AES.",
					".$MGG_BANK_NUM_AES.",
					".$MGG_BANK_OWN_NM_AES.",
					".$MGG_BIZ_END_DT.",
					".$RSR_ID.",
					'".addslashes($ARR['MGG_OTHER_BIZ_NM'])."',
					'".addslashes($ARR['MGG_SALES_NM'])."',
					".$RST_ID.",
					'".addslashes($ARR['MGG_SALES_MEET_LVL'])."',
					".$MGG_SALES_MEET_NM_AES.",
					".$MGG_SALES_MEET_TEL_AES.",
					".$RRT_ID.",
					'".addslashes($ARR['MGG_SALES_WHYNOT'])."',
					".$MGG_END_AVG_BILL_CNT.",
					".$MGG_END_AVG_BILL_PRICE.",
					".$MGG_END_OUT_OF_STOCK.",
					".$MGG_GPS_LATITUDE.",
					".$MGG_GPS_LONGITUDE.",
					NOW(),
					NOW(),
					'".$_SESSION['USERID']."' ,
					'N'
				);"
		;
		// echo $iSQL;
		if(!empty($ARR['MII_ID'])){
			$iSQL .= $this->insertMII($ARR);
		}
		
		if(!empty($ARR['CPPS_ID'])){
			$iSQL .= $this->insertPARTS($ARR);
		}
	
		if(!empty($ARR['NGV_VISIT_DT'])){
			$iSQL .= $this->insertVISIT($ARR);
		}
		return DBManager::execMulti($iSQL);
	}

	/**
	 * update 쿼리 리턴
	 * @param {$ARR : request 데이터}
	 */
	function updateData($ARR){
		$iSQL = '';

		if(!empty($ARR['IS_EXIST_CIRCUIT_ORDER'])){
			$iSQL .= "UPDATE  `NT_MAIN`.`NMG_GARAGE` 
					SET 
						`MGG_CIRCUIT_ORDER` = `MGG_CIRCUIT_ORDER`+1
					WHERE `MGG_CIRCUIT_ORDER` IN
						(
							SELECT `MGG_CIRCUIT_ORDER` FROM 
							(
								SELECT
									@rownum:=@rownum+1 AS IDX,
									`MGG_CIRCUIT_ORDER`,
									`MGG_CIRCUIT_ORDER` - @rownum AS GRP
								FROM `NT_MAIN`.`NMG_GARAGE`, (SELECT @rownum:=0) IDX
								WHERE `DEL_YN` = 'N' 
								AND `CSD_ID` = '".addslashes($ARR['CSD_ID'])."'
								AND `MGG_ID` != '".addslashes($ARR['ORI_MGG_ID'])."'
								AND `MGG_CIRCUIT_ORDER` >= '".addslashes($ARR['MGG_CIRCUIT_ORDER'])."' ORDER BY MGG_CIRCUIT_ORDER ASC 
								) AS CMNT WHERE GRP = '".addslashes($ARR['MGG_CIRCUIT_ORDER'])."'-1
						)
					;"
			;
		}

		$CRR = explode(',', $ARR['SELECT_CRR_ID']);
		$PWD_AES = "AES_ENCRYPT('".$ARR['MGG_PASSWD_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))" ;
		$MGG_FORCE_SEND_YN = (empty($ARR['MGG_FORCE_SEND_YN']) ? 'N' : $ARR['MGG_FORCE_SEND_YN']); 
		$MGG_BILL_MONEY_SHOW_YN = (empty($ARR['MGG_BILL_MONEY_SHOW_YN']) ? 'N' : $ARR['MGG_BILL_MONEY_SHOW_YN']); 
		$MGG_BILL_ALL_YN = (empty($ARR['MGG_BILL_ALL_YN']) ? 'N' : $ARR['MGG_BILL_ALL_YN']); 
		$SLL_ID = (empty($ARR['SLL_ID']) ? "NULL" : "'".addslashes($ARR['SLL_ID'])."'"); 

		$iSQL .= "UPDATE `NT_MAIN`.`NMG_GARAGE`
				  SET
					`CSD_ID` = '".addslashes($ARR['CSD_ID'])."',
					`CRR_1_ID` = '".$CRR[0]."',
					`CRR_2_ID` = '".$CRR[1]."',
					`CRR_3_ID` = '".$CRR[2]."',
					`MGG_USERID` = '".addslashes($ARR['MGG_USERID'])."',
					`MGG_PASSWD_AES` = ".$PWD_AES.",
					`MGG_BIZ_ID` = '".addslashes($ARR['MGG_BIZ_ID'])."',
					`MGG_CIRCUIT_ORDER` = '".addslashes($ARR['MGG_CIRCUIT_ORDER'])."',
					`MGG_NM` = '".addslashes($ARR['MGG_NM'])."',
					`MGG_FORCE_SEND_YN` = '".$MGG_FORCE_SEND_YN."',
					`MGG_BILL_ALL_YN` = '".$MGG_BILL_ALL_YN."',
					`MGG_BILL_MONEY_SHOW_YN` = '".$MGG_BILL_MONEY_SHOW_YN."',
					`SLL_ID` = ".$SLL_ID.",
					`MGG_BILL_MEMO` = '".addslashes($ARR['MGG_AOS_MEMO'])."',
					`LAST_DTHMS` = now()
				WHERE `MGG_ID` = '".addslashes($ARR['ORI_MGG_ID'])."'
				;"
		;


		$RET_ID = ($ARR['RET_ID'] == '' ? "NULL" : "'".$ARR['RET_ID']."'");
		$RPET_ID = ($ARR['RPET_ID'] == '' ? "NULL" : "'".$ARR['RPET_ID']."'");
		$RAT_ID = ($ARR['RAT_ID'] == '' ? "NULL" : "'".$ARR['RAT_ID']."'");
		$RMT_ID = ($ARR['RMT_ID'] == '' ? "NULL" : "'".$ARR['RMT_ID']."'");
		$ROT_ID = ($ARR['ROT_ID'] == '' ? "NULL" : "'".$ARR['ROT_ID']."'");
		$RCT_ID = ($ARR['RCT_ID'] == '' ? "NULL" : "'".$ARR['RCT_ID']."'");
		$RSR_ID = ($ARR['RSR_ID'] == '' ? "NULL" : "'".$ARR['RSR_ID']."'");
		$RST_ID = ($ARR['RST_ID'] == '' ? "NULL" : "'".$ARR['RST_ID']."'");
		$RRT_ID = ($ARR['RRT_ID'] == '' ? "NULL" : "'".$ARR['RRT_ID']."'");
		$MGG_RAT_DT = ($ARR['MGG_RAT_DT'] == '' ? "NULL" : "'".$ARR['MGG_RAT_DT']."'");
		$MGG_RET_DT = ($ARR['MGG_RET_DT'] == '' ? "NULL" : "'".$ARR['MGG_RET_DT']."'");
		$MGG_RPET_DT = ($ARR['MGG_RPET_DT'] == '' ? "NULL" : "'".$ARR['MGG_RPET_DT']."'");
		$MGG_BIZ_START_DT = ($ARR['MGG_BIZ_START_DT'] == '' ? "NULL" : "'".$ARR['MGG_BIZ_START_DT']."'");
		$MGG_CONTRACT_START_DT = ($ARR['MGG_CONTRACT_START_DT'] == '' ? "NULL" : "'".$ARR['MGG_CONTRACT_START_DT']."'");
		$MGG_CONTRACT_END_DT = ($ARR['MGG_CONTRACT_END_DT'] == '' ? "NULL" : "'".$ARR['MGG_CONTRACT_END_DT']."'");
		$MGG_BIZ_END_DT = ($ARR['MGG_BIZ_END_DT'] == '' ? "NULL" : "'".$ARR['MGG_BIZ_END_DT']."'");
		$MGG_CONTRACT_YN = (empty($ARR['MGG_CONTRACT_YN']) ? 'N' : $ARR['MGG_CONTRACT_YN']); 
		$MGG_LOGISTICS_YN = (empty($ARR['MGG_LOGISTICS_YN']) ? 'N' : $ARR['MGG_LOGISTICS_YN']); 
		$MGG_END_AVG_BILL_CNT = empty($ARR['MGG_END_AVG_BILL_CNT']) ? "NULL" : ROUND($ARR['MGG_END_AVG_BILL_CNT'],0);
		$MGG_END_AVG_BILL_PRICE = empty($ARR['MGG_END_AVG_BILL_PRICE']) ? "NULL" : $ARR['MGG_END_AVG_BILL_PRICE'];
		$MGG_END_OUT_OF_STOCK = empty($ARR['MGG_END_OUT_OF_STOCK']) ? "NULL" : "'".$ARR['MGG_END_OUT_OF_STOCK']."'";
		$MGG_GPS_LATITUDE = empty($ARR['MGG_GPS_LATITUDE']) ? "NULL" : $ARR['MGG_GPS_LATITUDE'];
		$MGG_GPS_LONGITUDE = empty($ARR['MGG_GPS_LONGITUDE']) ? "NULL" : "'".$ARR['MGG_GPS_LONGITUDE']."'";

		$MGG_CEO_NM_AES = "AES_ENCRYPT('".$ARR['MGG_CEO_NM_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))" ;
		$MGG_CEO_TEL_AES = "AES_ENCRYPT('".$ARR['MGG_CEO_TEL_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))" ;
		$MGG_KEYMAN_NM_AES = "AES_ENCRYPT('".$ARR['MGG_KEYMAN_NM_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))" ;
		$MGG_KEYMAN_TEL_AES = "AES_ENCRYPT('".$ARR['MGG_KEYMAN_TEL_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))" ;
		$MGG_CLAIM_NM_AES = "AES_ENCRYPT('".$ARR['MGG_CLAIM_NM_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))" ;
		$MGG_CLAIM_TEL_AES = "AES_ENCRYPT('".$ARR['MGG_CLAIM_TEL_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))" ;
		$MGG_FACTORY_NM_AES = "AES_ENCRYPT('".$ARR['MGG_FACTORY_NM_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))" ;
		$MGG_FACTORY_TEL_AES = "AES_ENCRYPT('".$ARR['MGG_FACTORY_TEL_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))" ;
		$MGG_PAINTER_NM_AES = "AES_ENCRYPT('".$ARR['MGG_PAINTER_NM_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))" ;
		$MGG_PAINTER_TEL_AES = "AES_ENCRYPT('".$ARR['MGG_PAINTER_TEL_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))" ;
		$MGG_METAL_NM_AES = "AES_ENCRYPT('".$ARR['MGG_METAL_NM_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))" ;
		$MGG_METAL_TEL_AES = "AES_ENCRYPT('".$ARR['MGG_METAL_TEL_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))" ;
		$MGG_BANK_NM_AES = "AES_ENCRYPT('".$ARR['MGG_BANK_NM_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))" ;
		$MGG_BANK_NUM_AES = "AES_ENCRYPT('".$ARR['MGG_BANK_NUM_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))" ;
		$MGG_BANK_OWN_NM_AES = "AES_ENCRYPT('".$ARR['MGG_BANK_OWN_NM_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))" ;
		$MGG_SALES_MEET_NM_AES = "AES_ENCRYPT('".$ARR['MGG_SALES_MEET_NM_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))" ;
		$MGG_SALES_MEET_TEL_AES = "AES_ENCRYPT('".$ARR['MGG_SALES_MEET_TEL_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))" ;
		

		$iSQL .= "UPDATE `NT_MAIN`.`NMG_GARAGE_SUB`
				SET
					`MGG_ID` = '".addslashes($ARR['MGG_ID'])."',
					`RET_ID` = ".$RET_ID.",
					`MGG_RET_DT` = ".$MGG_RET_DT.",
					`RPET_ID` = ".$RPET_ID.",
					`MGG_RPET_DT` = ".$MGG_RPET_DT.",
					`RAT_ID` = ".$RAT_ID.",
					`MGG_RAT_DT` = ".$MGG_RAT_DT.",
					`MGG_RAT_NM` = '".addslashes($ARR['MGG_RAT_NM'])."',
					`MGG_LOGISTICS_YN` = '".addslashes($MGG_LOGISTICS_YN)."',
					`MGG_ZIP_OLD` = '".addslashes($ARR['MGG_ZIP_OLD'])."',
					`MGG_ADDRESS_OLD_1` = '".addslashes($ARR['MGG_ADDRESS_OLD_1'])."',
					`MGG_ADDRESS_OLD_2` = '".addslashes($ARR['MGG_ADDRESS_OLD_2'])."',
					`MGG_ZIP_NEW` = '".addslashes($ARR['MGG_ZIP_NEW'])."',
					`MGG_ADDRESS_NEW_1` = '".addslashes($ARR['MGG_ADDRESS_NEW_1'])."',
					`MGG_ADDRESS_NEW_2` = '".addslashes($ARR['MGG_ADDRESS_NEW_2'])."',
					`MGG_OFFICE_TEL` = '".addslashes($ARR['MGG_OFFICE_TEL'])."',
					`MGG_OFFICE_FAX` = '".addslashes($ARR['MGG_OFFICE_FAX'])."',
					`MGG_CEO_NM_AES` = ".$MGG_CEO_NM_AES.",
					`MGG_CEO_TEL_AES` = ".$MGG_CEO_TEL_AES.",
					`MGG_KEYMAN_NM_AES` = ".$MGG_KEYMAN_NM_AES.",
					`MGG_KEYMAN_TEL_AES` = ".$MGG_KEYMAN_TEL_AES.",
					`MGG_CLAIM_NM_AES` = ".$MGG_CLAIM_NM_AES.",
					`MGG_CLAIM_TEL_AES` = ".$MGG_CLAIM_TEL_AES.",
					`MGG_FACTORY_NM_AES` = ".$MGG_FACTORY_NM_AES.",
					`MGG_FACTORY_TEL_AES` = ".$MGG_FACTORY_TEL_AES.",
					`MGG_PAINTER_NM_AES` = ".$MGG_PAINTER_NM_AES.",
					`MGG_PAINTER_TEL_AES` = ".$MGG_PAINTER_TEL_AES.",
					`MGG_METAL_NM_AES` = ".$MGG_METAL_NM_AES.",
					`MGG_METAL_TEL_AES` = ".$MGG_METAL_TEL_AES.",
					`MGG_WAREHOUSE_CNT` = '".addslashes($ARR['MGG_WAREHOUSE_CNT'])."',
					`RMT_ID` = ".$RMT_ID.",
					`ROT_ID` = ".$ROT_ID.",
					`MGG_INSU_MEMO` = '".addslashes($ARR['MGG_INSU_MEMO'])."',
					`MGG_MANUFACTURE_MEMO` = '".addslashes($ARR['MGG_MANUFACTURE_MEMO'])."',
					`MGG_BILL_MEMO` = '".addslashes($ARR['MGG_BILL_MEMO'])."',
					`MGG_GARAGE_MEMO` = '".addslashes($ARR['MGG_GARAGE_MEMO'])."',
					`RCT_ID` = ".$RCT_ID.",
					`MGG_BIZ_START_DT` = ".$MGG_BIZ_START_DT.",
					`MGG_FRONTIER_NM` = '".addslashes($ARR['MGG_FRONTIER_NM'])."',
					`MGG_COLLABO_NM` = '".addslashes($ARR['MGG_COLLABO_NM'])."',
					`MGG_BAILEE_NM` = '".addslashes($ARR['MGG_BAILEE_NM'])."',
					`MGG_BIZ_BEFORE_NM` = '".addslashes($ARR['MGG_BIZ_BEFORE_NM'])."',
					`MGG_CONTRACT_YN` = '".addslashes($MGG_CONTRACT_YN)."',
					`MGG_CONTRACT_START_DT` = ".$MGG_CONTRACT_START_DT.",
					`MGG_CONTRACT_END_DT` = ".$MGG_CONTRACT_END_DT.",
					`MGG_RATIO` = '".addslashes($ARR['MGG_RATIO'])."',
					`MGG_BANK_NM_AES` = ".$MGG_BANK_NM_AES.",
					`MGG_BANK_NUM_AES` = ".$MGG_BANK_NUM_AES.",
					`MGG_BANK_OWN_NM_AES` = ".$MGG_BANK_OWN_NM_AES.",
					`MGG_BIZ_END_DT` = ".$MGG_BIZ_END_DT.",
					`RSR_ID` = ".$RSR_ID.",
					`MGG_OTHER_BIZ_NM` = '".addslashes($ARR['MGG_OTHER_BIZ_NM'])."',
					`MGG_SALES_NM` = '".addslashes($ARR['MGG_SALES_NM'])."',
					`RST_ID` = ".$RST_ID.",
					`MGG_SALES_MEET_LVL` = '".addslashes($ARR['MGG_SALES_MEET_LVL'])."',
					`MGG_SALES_MEET_NM_AES` = ".$MGG_SALES_MEET_NM_AES.",
					`MGG_SALES_MEET_TEL_AES` = ".$MGG_SALES_MEET_TEL_AES.",
					`RRT_ID` = ".$RRT_ID.",
					`MGG_SALES_WHYNOT` = '".addslashes($ARR['MGG_SALES_WHYNOT'])."',
					`MGG_END_AVG_BILL_CNT` = ".$MGG_END_AVG_BILL_CNT.",
					`MGG_END_AVG_BILL_PRICE` = ".$MGG_END_AVG_BILL_PRICE.",
					`MGG_END_OUT_OF_STOCK` = ".$MGG_END_OUT_OF_STOCK.",
					`MGG_GPS_LATITUDE` = ".$MGG_GPS_LATITUDE.",
					`MGG_GPS_LONGITUDE` = ".$MGG_GPS_LONGITUDE.",
					`LAST_DTHMS` = now()
				WHERE `MGG_ID` = '".addslashes($ARR['ORI_MGG_ID'])."'
				;"
		;

		if(!empty($ARR['MII_ID'])){
			$iSQL .= "DELETE FROM `NT_MAIN`.`NMG_GARAGE_PAYEXP_INSU`
					  WHERE `MGG_ID` = '".addslashes($ARR['ORI_MGG_ID'])."'
					;"
			;
			$iSQL .= $this->insertMII($ARR);
		}
		
		if(!empty($ARR['CPPS_ID'])){
			$iSQL .= "DELETE FROM `NT_MAIN`.`NMG_GARAGE_PART`
					  WHERE `MGG_ID` = '".addslashes($ARR['ORI_MGG_ID'])."'
					;"
			;

			$iSQL .= $this->insertPARTS($ARR);
		}
	
		if(!empty($ARR['NGV_VISIT_DT'])){
			$iSQL .= $this->insertVISIT($ARR);
		}

		if(!empty($ARR['BIZ_VISIT_DATE'])){
			$iSQL .= "UPDATE NT_SALES.NS_SALES_UPLOAD
				SET
					`NSU_VISIT_DT` = '".addslashes($ARR['BIZ_VISIT_DATE'])."',
					`LAST_DTHMS` = now()
				WHERE `NSU_ID` = '".addslashes($ARR['ORI_NSU_ID'])."'
				;"
			;
		}

		if(!empty($ARR['BIZ_SUPPLY_DATE'])){
			$iSQL .= "UPDATE NT_SALES.NS_SALES_UPLOAD
				SET
					`NSU_SUPPLY_DT` = '".addslashes($ARR['BIZ_SUPPLY_DATE'])."',
					`LAST_DTHMS` = now()
				WHERE `NSU_ID` = '".addslashes($ARR['ORI_NSU_ID'])."'
				;"
			;
		}
		// echo $iSQL;
		return DBManager::execMulti($iSQL);
	}

	/**
	 * 중복쿼리(onCheckRequireData 한 세트)
	 * @param {$COL : sql에서 해당정보를 가져올 컬럼}
	 * @param {$ARR : request 데이터}
	 * @param {$DATA : sql에 비교 할 데이터}
	 * @param {$AND_SQL : sub 쿼리}
	 */
	function isExistData($COL, $ARR, $DATA, $AND_SQL){
		$subSQL == '';
		if($ARR['REQ_MODE'] == CASE_UPDATE){
			$subSQL = "AND `MGG_ID` != '".addslashes($ARR['ORI_MGG_ID'])."'";
		}

		$iSQL = "SELECT  $COL
					FROM NT_MAIN.NMG_GARAGE
					WHERE $COL = '".$DATA."'
					$AND_SQL
					$subSQL
				;"
		;

		$RS = DBManager::getRecordSet($iSQL);

		if(!empty($RS)) {
			unset($RS);
			return false;
		}else{
			unset($RS);
			return true;
		}
	}

	/**
	 * 필수조건에 중복체크 (isExistData함수와 한 세트)
	 */
	function onCheckRequireData($ARR){
		$EXIST_MGG_ID = ($ARR['REQ_MODE'] == CASE_UPDATE) ? true : $this->isExistData('MGG_ID', $ARR , $ARR['MGG_ID'], '');
		// $EXIST_MGG_USERID = ($ARR['REQ_MODE'] == CASE_UPDATE) ? true : $this->isExistData('MGG_USERID', $ARR , $ARR['MGG_USERID'], '');
		
		if(!$EXIST_MGG_ID){
			return array(
				"status"=> CONFLICT,
				"msg"=> '중복된 공업사코드가 존재합니다.',
				"col"=>'MGG_ID'
			);
		}else if(!$this->isExistData('MGG_USERID', $ARR , $ARR['MGG_USERID'], '')){
			return array(
				"status"=> CONFLICT,
				"msg"=> '중복된 공업사 아이디 존재합니다.',
				"col"=>'MGG_USERID'
			);
		}else if(!$this->isExistData('MGG_NM',$ARR , $ARR['MGG_NM'], '')){
			return array(
				"status"=> CONFLICT,
				"msg"=> '중복된 공업사명이 존재합니다.',
				"col"=>'MGG_NM'
			);
		}else{
			if(empty($ARR['IS_EXIST_CIRCUIT_ORDER'])){
				$subSQL = "AND `CSD_ID` = '".addslashes($ARR['CSD_ID'])."'";
				$MGG_CIRCUIT_ORDER = (empty($ARR['MGG_CIRCUIT_ORDER']) ? '0' : $ARR['MGG_CIRCUIT_ORDER']);
				if(!$this->isExistData('MGG_CIRCUIT_ORDER', $ARR , $MGG_CIRCUIT_ORDER, $subSQL)){
					return array(
						"status"=> ERROR,
						"msg"=> '중복된 순회순서가 존재합니다.',
						"col"=>'MGG_CIRCUIT_ORDER'
					);
				}
			}
			
			if(empty($ARR['IS_EXIST_MGG_BIZ_ID'])){
				if(!$this->isExistData('MGG_BIZ_ID', $ARR , $ARR['MGG_BIZ_ID'], '')){
					return array(
						"status"=> ALREADY_EXISTS,
						"msg"=> '중복된 사업자번호가 존재합니다.',
						"col"=>'MGG_BIZ_ID'
					);
				}
			}

			return array(
				"status"=> OK
			);
			
		}
	}

	/**
	 * 데이터생성
	 */
	function setData($ARR) {
		// return $this->insertData($ARR);
		$result = $this->onCheckRequireData($ARR);
		if($result['status'] === OK){
			$RESULT_SQL = ($ARR['REQ_MODE'] == CASE_CREATE) ? $this->insertData($ARR) : $this->updateData($ARR);
			if($RESULT_SQL ){
				return array(
					"status"=> OK,
				);
			}else{
				return array(
					"status"=> SERVER_ERROR,
				);
			}
		}else{
			return $result;
		}
	}

	/**
	 * 삭제쿼리 리턴
	 */
	function deleteSQL($TABLE, $MGG_ID){
		$iSQL = "UPDATE `NT_MAIN`.".$TABLE."
					SET  
						`LAST_DTHMS`= NOW(), 
						`DEL_YN`	='Y'
					WHERE `MGG_ID`='".$MGG_ID."'
				;"
		;

		return $iSQL;
	}
	/**
	 * 삭제
	 * @param {$ARR}
	 */
	function delData($ARR) {

		$iSQL = $this->deleteSQL('NMG_GARAGE', $ARR['MGG_ID']);
		$iSQL .= $this->deleteSQL('NMG_GARAGE_SUB', $ARR['MGG_ID']);

		if(!empty($ARR['NSU_ID'])){
			$iSQL .= "DELETE FROM NT_SALES.NS_SALES_UPLOAD
					WHERE `NSU_ID` = '".addslashes($ARR['NSU_ID'])."'
				;"
			;
		}
		return DBManager::execMulti($iSQL);		
	}

	/**
	 * 거래중단 시 3개월 거래 평균
	 */
	function getStdParts($ARR){
		$iSQL = "SELECT 
					ROUND(SUM(IF(NB_ID IS NOT NULL , 1,0))/3, 0) AS AVG_CNT,
					FORMAT(ROUND(SUM(NB_TOTAL_PRICE)/3, 0),0) AVG_PRICE
				FROM NT_BILL.NBB_BILL 
				WHERE 
					MGG_ID = '".addslashes($ARR['MGG_ID'])."'
				AND DEL_YN = 'N'
				AND DATE_FORMAT(NB_CLAIM_DT, '%Y-%m' ) 
					BETWEEN DATE_FORMAT(SUBDATE(NOW(), INTERVAL 3 MONTH), '%Y-%m' ) 
					AND  DATE_FORMAT(SUBDATE(NOW(), INTERVAL 1 MONTH), '%Y-%m' ) 
			;"
		;

		$Result= DBManager::getResult($iSQL);
		///

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
				"AVG_CNT"=>$RS['AVG_CNT'],
				"AVG_PRICE"=>$RS['AVG_PRICE'],
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

	/**부품구분과 공급부품을 가져온다. */
	function getPartsInfo($ARR){
		$iSQL = "SELECT 
					`CPPS`.`CPPS_ID`,
					`CPP`.`CPP_ID`,
					`CPP`.`CPP_NM`,
					`CPPS`.`CPP_ID`,
					`CPPS`.`CPPS_NM`,
					`CPPS`.`CPPS_UNIT`
				FROM `NT_CODE`.`NCP_PART_SUB` `CPPS`,
					`NT_CODE`.`NCP_PART` `CPP`
				WHERE `CPPS`.`DEL_YN`  = 'N'  
				AND `CPP`.`DEL_YN`  = 'N'  
				AND `CPP`.`CPP_USE_YN`  = 'Y'  
				AND `CPP`.`CPP_ID` =  `CPPS`.`CPP_ID`
				ORDER BY `CPP`.`CPP_ORDER_NUM` ASC
				;"
		;

		$Result= DBManager::getResult($iSQL);
		///

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
				"CPPS_ID"=>$RS['CPPS_ID'],
				"CPP_ID"=>$RS['CPP_ID'],
				"CPP_NM"=>$RS['CPP_NM'],
				"CPP_ID"=>$RS['CPP_ID'],
				"CPPS_NM"=>$RS['CPPS_NM'],
				"CPPS_UNIT"=>$RS['CPPS_UNIT'],

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

	/** 기준부품 리스트 조회 */
	function getCodeParts(){
		$iSQL = "SELECT CPP_ID, 
					CONCAT (CPP_NM_SHORT, ':') AS CPP_NM_SHORT,
					'' AS CPPS_NM_SHORT,
					'' AS NGP_RATIO,
					'' AS NGP_MEMO
				FROM NT_CODE.NCP_PART
				WHERE CPP_USE_YN ='Y'
				AND DEL_YN = 'N'
				ORDER BY CPP_ORDER_NUM;
		";

		$Result= DBManager::getResult($iSQL);
		///

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
				"CPP_ID"=>$RS['CPP_ID'],
				"CPP_NM_SHORT"=>$RS['CPP_NM_SHORT'],
				"CPPS_NM_SHORT"=>$RS['CPPS_NM_SHORT'],
				"NGP_RATIO"=>$RS['NGP_RATIO'],
				"NGP_MEMO"=>$RS['NGP_MEMO'],
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
	


}
	// 0. 객체 생성
	$clsGwManagement = new clsGwManagement(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$clsGwManagement->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
