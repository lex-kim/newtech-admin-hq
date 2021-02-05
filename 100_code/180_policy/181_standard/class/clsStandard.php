<?php

class clsStandard extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	// Class Object Constructor
	function clsStandard($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
		$this->DATA_SAVE_PATH = $DATA_SAVE_PATH;
		$this->ALLOW_FILE_EXT = $ALLOW_FILE_EXT;
	}


	/**
	 * 해당데이터 조회 
	 * @param {$ARR}
	 * @param {ALLM_ID : 조회할 해당 데이터 ID}
	 */
	function getData($ARR) {
		$iSQL = "SELECT 
					`PIE_LOW_PRICE`,
					`PIE_HIGH_PRICE`
				FROM `NT_CODE`.`PCY_INSU_ENV`
				"
		;

		$RS= DBManager::getRecordSet($iSQL);

		if(empty($RS)) {
			unset($RS);
			return HAVE_NO_DATA;
		} else {
			return $RS;
		}
		
	}

	/**
	 * 업데이트
	 */
	function setData($ARR) {
		
		$iSQL = "UPDATE  `NT_CODE`.`PCY_INSU_ENV`
					SET 
					`PIE_LOW_PRICE` = '".addslashes($ARR['PIE_LOW_PRICE'])."',
					`PIE_HIGH_PRICE`= '".addslashes($ARR['PIE_HIGH_PRICE'])."',
					`LAST_DTHMS` = now(),
					`WRT_USERID` = '".addslashes($_SESSION['USERID'])."'
				;"
		;
		
		
		return DBManager::execQuery($iSQL);
	}

}
	// 0. 객체 생성
	$clsStandard = new clsStandard(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$clsStandard->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
