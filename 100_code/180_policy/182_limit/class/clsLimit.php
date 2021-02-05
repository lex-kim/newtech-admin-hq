<?php

class clsLimit extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	// Class Object Constructor
	function clsLimit($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
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


		/** 전체리스트가 아닌경우에만 limitSql 적용 */
		$LIMIT_SQL = "";
		if($ARR['REQ_MODE'] == CASE_LIST){
			$LIMIT_SQL = "LIMIT $LimitStart,$LimitEnd";
		}
		$tmpKeyword = "";

		if($ARR['SEARCH_START_DT'] != ''){
			$tmpKeyword .= "AND PCH.PCH_SET_DT >= '".$ARR['SEARCH_START_DT']."' ";
		}
		if($ARR['SEARCH_END_DT'] != ''){
			$tmpKeyword .= "AND PCH.PCH_SET_DT <= '".$ARR['SEARCH_END_DT']."' ";
		}

		// 페이징처리를 위해 전체데이터
		$iSQL = "SELECT
					COUNT(*) AS CNT
				FROM
					`NT_CODE`.`PCY_CLAIM_HISTORY` PCH
				WHERE 1=1
				$tmpKeyword
				;"
			;
		// echo $iSQL."\n";
		try{
			$RS_T	= DBManager::getRecordSet($iSQL);
		} catch (Exception $e){
			echo "error : ".$e;
			return ERROR_DB_SELECT_DATA; 
		}

		$TOTAL_LIST_COUNT = $RS_T['CNT'];
		$IDX = $RS_T['CNT']-$LimitStart;
		unset($RS_T);
		///


		// 리스트 가져오기
		$iSQL = "SELECT 
					`PCH_ID`,
					`PCH_SET_DT`,
					`PCH_OLD_LIMIT_PRICE`,
					`PCH_NEW_LIMIT_PRICE`,
					`WRT_DTHMS`,
					`LAST_DTHMS`,
					`WRT_USERID`
				FROM `NT_CODE`.`PCY_CLAIM_HISTORY` PCH
				WHERE 1=1
				$tmpKeyword
				ORDER BY `WRT_DTHMS` DESC
				$LIMIT_SQL
				;
				
				"
		;
		// echo $iSQL."\n";
		$Result= DBManager::getResult($iSQL);
		

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
				"IDX"=>$IDX--,
				"PCH_ID"=>$RS['PCH_ID'],
				"PCH_SET_DT"=>$RS['PCH_SET_DT'],
				"PCH_OLD_LIMIT_PRICE"=>$RS['PCH_OLD_LIMIT_PRICE'],
				"PCH_NEW_LIMIT_PRICE"=>$RS['PCH_NEW_LIMIT_PRICE'],

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

	/**
	 * 해당데이터 조회 
	 * @param {$ARR}
	 * @param 
	 */
	function getData($ARR) {
		$iSQL = "SELECT 
					`PCE_SET_DT`,
					`PCE_CURRENT_LIMIT_PRICE`
				FROM `NT_CODE`.`PCY_CLAIM_ENV`
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
		
		$iSQL = "UPDATE  `NT_CODE`.`PCY_CLAIM_ENV`
					SET 
					`PCE_SET_DT` = '".addslashes($ARR['PCE_SET_DT'])."',
					`PCE_CURRENT_LIMIT_PRICE`= '".addslashes($ARR['PCE_CURRENT_LIMIT_PRICE'])."',
					`LAST_DTHMS` = now(),
					`WRT_USERID` = '".addslashes($_SESSION['USERID'])."'
				;"
		;
		
		return DBManager::execQuery($iSQL);
	}


	/**
	 * 삭제
	 * @param {$ARR}
	 * @param {ALLM_ID : 삭제할 해당 데이터 ID}
	 */
	function delData($ARR) {
		$iSQL = "UPDATE `NT_CODE`.`PCY_CLAIM_ENV`
					SET  
						`LAST_DTHMS`= NOW(), 
						`DEL_YN`	='Y'
					WHERE `CCC_ID`='".addslashes($ARR['CCC_ID'])."'
					;"
		;

		return DBManager::execQuery($iSQL);
	}


}
	// 0. 객체 생성
	$clsLimit = new clsLimit(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$clsLimit->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
