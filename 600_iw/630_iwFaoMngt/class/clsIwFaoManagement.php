<?php

class clsIwFaoManagement extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자
	private $AES_KEY = "123";
	private $MGG_BIZ_START_DT = 'MGG_BIZ_START_DT';
	private $MGG_BIZ_END_DT = 'MGG_BIZ_END_DT';

	// Class Object Constructor
	function clsIwFaoManagement($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
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
		$tmpKeyword = "";
		
		//등록기간 검색
		if($ARR['START_DT'] !='' && $ARR['END_DT'] !=''){
			$tmpKeyword .= "AND DATE(`NIC`.`WRT_DTHMS`) BETWEEN '".addslashes($ARR['START_DT'])."' AND '".addslashes($ARR['END_DT'])."'";
		}else if($ARR['START_DT'] !='' && $ARR['END_DT'] ==''){
			$tmpKeyword .= "AND DATE(`NIC`.`WRT_DTHMS`) >= '".addslashes($ARR['START_DT'])."'";
		}else if($ARR['START_DT'] =='' && $ARR['END_DT'] !=''){
			$tmpKeyword .= "AND DATE(`NIC`.`WRT_DTHMS`) <= '".addslashes($ARR['END_DT'])."'";
		}
		
		//보험사 검색
		if($ARR['MII_ID_SEARCH'] !=''){
			$tmpKeyword .= " AND `NIC`.`MII_ID` = '".addslashes($ARR['MII_ID_SEARCH'])."'";
		}

		//보험사정보 검색
		if($ARR['NIC_AES_SEARCH'] !=''){
			$AES_DECRYPT_DATA = "CAST(AES_DECRYPT(`NIC`.`".addslashes($ARR['NIC_AES_SEARCH'])."`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR)";
			$tmpKeyword .= "AND INSTR(".$AES_DECRYPT_DATA.",'".addslashes($ARR['NIC_AES_INPUT_SEARCH'])."')>0 ";
		}

		//미사용사유 검색
		if($ARR['NIC_NOUSE_REASON_SEARCH'] !=''){
			$tmpKeyword .= "AND INSTR(`NIC`.`NIC_NOUSE_REASON`,'".addslashes($ARR['NIC_NOUSE_REASON_SEARCH'])."')>0 ";
		}



		// 페이징처리를 위해 전체데이터
		$iSQL = "SELECT 
						COUNT(`NIC`.`NIC_ID`) AS CNT
						
				FROM `NT_MAIN`.`NMI_INSU_CHARGE` `NIC`
				LEFT OUTER JOIN `NT_MAIN`.`NMI_INSU` `MII`
				ON `NIC`.`MII_ID` = `MII`.`MII_ID` AND MII.DEL_YN = 'N'
				LEFT OUTER JOIN NT_MAIN.NMI_INSU_TEAM MIT
				ON MIT.MIT_ID = NIC.MIT_ID			
				WHERE `NIC`.`DEL_YN` = 'N'
				AND `MII`.`DEL_YN` = 'N'
				$tmpKeyword
				ORDER BY NIC.WRT_DTHMS DESC
				;"
		;

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
		$iSQL = "SELECT 
						`NIC`.`NIC_ID`,
						`NIC`.`MII_ID`,
						IFNULL(MII.MII_NM,'') AS `MII_NM`,
						IFNULL(MIT.MIT_NM,'') AS `MIT_NM`,
						`NIC`.`MIT_ID`,
						CAST(AES_DECRYPT(`NIC`.`NIC_NM_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `NIC_NM_AES`,
						CAST(AES_DECRYPT(`NIC`.`NIC_FAX_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `NIC_FAX_AES`,
						CAST(AES_DECRYPT(`NIC`.`NIC_TEL1_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `NIC_TEL1_AES`,
						CAST(AES_DECRYPT(`NIC`.`NIC_TEL2_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `NIC_TEL2_AES`,
						CAST(AES_DECRYPT(`NIC`.`NIC_EMAIL_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `NIC_EMAIL_AES`,
						`NIC`.`NIC_NOUSE_REASON`,
						`NIC`.`NIC_MEMO`,
						`NIC`.`NIC_CONFIRM_YN`,
						 SUBSTR(`NIC`.`WRT_DTHMS`, 1,10) AS WRT_DTHMS,
						`NIC`.`LAST_DTHMS`,
						`NIC`.`WRT_USERID`

				FROM `NT_MAIN`.`NMI_INSU_CHARGE` `NIC`
				LEFT OUTER JOIN `NT_MAIN`.`NMI_INSU` `MII`
				ON `NIC`.`MII_ID` = `MII`.`MII_ID` AND MII.DEL_YN = 'N'
				LEFT OUTER JOIN NT_MAIN.NMI_INSU_TEAM MIT
				ON MIT.MIT_ID = NIC.MIT_ID			
				WHERE `NIC`.`DEL_YN` = 'N'
				AND `MII`.`DEL_YN` = 'N'
				$tmpKeyword
				ORDER BY NIC.WRT_DTHMS DESC
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
					"NIC_ID"=>$RS['NIC_ID'],
					"MII_ID"=>$RS['MII_ID'],
					"MII_NM"=>$RS['MII_NM'],
					"MIT_ID"=>$RS['MIT_ID'],
					"MIT_NM"=>$RS['MIT_NM'],
					"NIC_NM_AES"=>$RS['NIC_NM_AES'],
					"NIC_FAX_AES"=>$RS['NIC_FAX_AES'],
					"NIC_TEL1_AES"=>$RS['NIC_TEL1_AES'],
					"NIC_TEL2_AES"=>$RS['NIC_TEL2_AES'],
					"NIC_EMAIL_AES"=>$RS['NIC_EMAIL_AES'],
					"NIC_NOUSE_REASON"=>$RS['NIC_NOUSE_REASON'],
					"NIC_MEMO"=>$RS['NIC_MEMO'],
					"WRT_DTHMS"=>$RS['WRT_DTHMS'],
			));
		}

		// $Result->close();  // 자원 반납
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


	/* 보험사Team 조회  */
	function getInsuTeam($ARR) {
		$iSQL = "SELECT 
					MIT_ID, 
					MIT_NM
				FROM NT_MAIN.NMI_INSU_TEAM
				WHERE MII_ID = '".addslashes($ARR['MII_ID'])."'
			;"
		;

		$Result= DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"MII_ID"=>$RS['MIT_ID'],
					"MII_NM"=>$RS['MIT_NM']
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
	 * 필수조건에 중복체크 (isExistData함수와 한 세트)
	 */
	function onCheckRequireData($ARR){
		$COL = $ARR['TARGET_ID'];
		$DATA = "CAST(AES_DECRYPT(".$ARR['TARGET_ID'].",UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR)"; 
	
		$iSQL = "SELECT  $COL
				FROM NT_MAIN.NMI_INSU_CHARGE
				WHERE ".$DATA." = '".$ARR['TARGET_VALUE']."'
				AND MII_ID = '".$ARR['MII_ID']."'
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
	 * 데이터생성
	 */
	function setData($ARR) {
		if($ARR['REQ_MODE'] == CASE_CREATE){
			$MIT_ID = $ARR['MIT_ID'] == '' ? 'NULL' : "'".$ARR['MIT_ID']."'";
			$NIC_NM_AES = "AES_ENCRYPT('".$ARR['NIC_NM_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))";
			$NIC_FAX_AES = "AES_ENCRYPT('".$ARR['NIC_FAX_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))";
			$NIC_TEL1_AES = "AES_ENCRYPT('".$ARR['NIC_TEL1_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))";
			$NIC_TEL2_AES = "AES_ENCRYPT('".$ARR['NIC_TEL2_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))";
			$NIC_EMAIL_AES = "AES_ENCRYPT('".$ARR['NIC_EMAIL_AES']."', UNHEX(SHA2('".AES_SALT_KEY."',256)))";
			$SESSION_ID = $_SESSION['USERID'] == null ? 'SYSYTEM' : $_SESSION['USERID'];

			$iSQL = "INSERT INTO `NT_MAIN`.`NMI_INSU_CHARGE`
					(
						`MII_ID`,
						`MIT_ID`,
						`NIC_NM_AES`,
						`NIC_FAX_AES`,
						`NIC_TEL1_AES`,
						`NIC_TEL2_AES`,
						`NIC_EMAIL_AES`,
						`NIC_NOUSE_REASON`,
						`NIC_MEMO`,
						`NIC_CONFIRM_YN`,
						`NIC_REQUSET_DTHMS`,
						`WRT_DTHMS`,
						`LAST_DTHMS`,
						`WRT_USERID`,
						`DEL_YN`
					)
					VALUES
					(
						'".addslashes($ARR['MII_ID'])."',
						".$MIT_ID.",
						".$NIC_NM_AES.",
						".$NIC_FAX_AES.",
						".$NIC_TEL1_AES.",
						".$NIC_TEL2_AES.",
						".$NIC_EMAIL_AES.",
						'".addslashes($ARR['NIC_NOUSE_REASON'])."',
						'".addslashes($ARR['NIC_MEMO'])."',
						'".YN_Y."',
						NOW(),
						NOW(),
						NOW(),
						'".$SESSION_ID ."',
						'N'
					);"
			;
		}else {
			if($ARR['MIT_ID'] != ''){
				$subSql .= "`MIT_ID` = '".addslashes($ARR['MIT_ID'])."', \n";
			}
			if($ARR['NIC_TEL2_AES'] != ''){
				$subSql .= "`NIC_TEL2_AES` = (AES_ENCRYPT('".addslashes($ARR['NIC_TEL2_AES'])."',UNHEX(SHA2('".AES_SALT_KEY."',256))) ), \n";
			}
			if($ARR['NIC_EMAIL_AES'] != ''){
				$subSql .= "`NIC_EMAIL_AES` = (AES_ENCRYPT('".addslashes($ARR['NIC_EMAIL_AES'])."',UNHEX(SHA2('".AES_SALT_KEY."',256))) ), \n";
			}
			$iSQL = "UPDATE  `NT_MAIN`.`NMI_INSU_CHARGE`
						SET
							`MII_ID` = '".addslashes($ARR['MII_ID'])."',
							`NIC_NM_AES` = (AES_ENCRYPT('".addslashes($ARR['NIC_NM_AES'])."',UNHEX(SHA2('".AES_SALT_KEY."',256))) ),
							`NIC_FAX_AES` = (AES_ENCRYPT('".addslashes($ARR['NIC_FAX_AES'])."',UNHEX(SHA2('".AES_SALT_KEY."',256))) ),
							`NIC_TEL1_AES` = (AES_ENCRYPT('".addslashes($ARR['NIC_TEL1_AES'])."',UNHEX(SHA2('".AES_SALT_KEY."',256))) ),
							$subSql
							`NIC_NOUSE_REASON` = '".addslashes($ARR['NIC_NOUSE_REASON'])."',
							`NIC_MEMO` = '".addslashes($ARR['NIC_MEMO'])."',
							`LAST_DTHMS` = NOW(),
							`WRT_USERID` = '".addslashes($_SESSION['USERID'])."'
						WHERE `NIC_ID` = '".addslashes($ARR['NIC_ID'])."' 
						AND `DEL_YN` = 'N';
					";

		}
		// echo $iSQL."\n";
		return DBManager::execQuery($iSQL);
	}


	/**
	 * data 삭제
	 * @param {$ARR}
	 * @param {ALLM_ID : 삭제할 해당 데이터 ID}
	 */
	function delData($ARR) {
		$iSQL = "UPDATE `NT_MAIN`.`NMI_INSU_CHARGE`
					SET  
						`LAST_DTHMS`= NOW(), 
						`WRT_USERID` = '".addslashes($_SESSION['USERID'])."',
						`DEL_YN`	='Y'
					WHERE `NIC_ID`='".addslashes($ARR['NIC_ID'])."';
		";
		
		// echo 	$iSQL ;
		return DBManager::execQuery($iSQL);
	}

	


}
	// 0. 객체 생성
	$clsIwFaoManagement = new clsIwFaoManagement(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$clsIwFaoManagement->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
