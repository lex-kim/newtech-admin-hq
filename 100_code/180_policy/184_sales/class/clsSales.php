<?php
class clsSales extends DBManager {
	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자
	// Class Object Constructor
	function clsSales($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
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
					`PSE_SUPPLY_RATIO`,
					`PSE_STOCK_RATIO`,
					`PSE_OVER_CNT`
				FROM `NT_CODE`.`PCY_SALES_ENV`
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
		
		$iSQL = "UPDATE  `NT_CODE`.`PCY_SALES_ENV`
					SET 
					`PSE_SUPPLY_RATIO` = '".addslashes($ARR['PSE_SUPPLY_RATIO'])."',
					`PSE_STOCK_RATIO`= '".addslashes($ARR['PSE_STOCK_RATIO'])."',
					`PSE_OVER_CNT`= '".addslashes($ARR['PSE_OVER_CNT'])."',
					`LAST_DTHMS` = now(),
					`WRT_USERID` = '".addslashes($_SESSION['USERID'])."'
				;"
		;
		
		
		return DBManager::execQuery($iSQL);
	}
}
	// 0. 객체 생성
	$clsSales = new clsSales(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$clsSales->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);
?>