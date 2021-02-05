<?php
class clsPolicy extends DBManager {
	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자
	// Class Object Constructor
	function clsPolicy($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
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
					`PPE_BILL_PRICE`,
					`PPE_POINT_EXPIRE`,
					`PPE_NOTICE_EXPIRE`
				FROM `NT_POINT`.`PCY_POINT_ENV`
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
		
		$iSQL = "UPDATE  `NT_POINT`.`PCY_POINT_ENV`
					SET 
					`PPE_BILL_PRICE` = '".addslashes($ARR['PPE_BILL_PRICE'])."',
					`PPE_POINT_EXPIRE`= '".addslashes($ARR['PPE_POINT_EXPIRE'])."',
					`PPE_NOTICE_EXPIRE`= '".addslashes($ARR['PPE_NOTICE_EXPIRE'])."',
					`LAST_DTHMS` = now(),
					`WRT_USERID` = '".addslashes($_SESSION['USERID'])."'
				;"
		;
		
		
		return DBManager::execQuery($iSQL);
	}
}
	// 0. 객체 생성
	$clsPolicy = new clsPolicy(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$clsPolicy->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);
?>