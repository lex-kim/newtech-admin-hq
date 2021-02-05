<?php

class cAccount extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	// Class Object Constructor
	function cAccount($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
		$this->DATA_SAVE_PATH = $DATA_SAVE_PATH;
		$this->ALLOW_FILE_EXT = $ALLOW_FILE_EXT;
	}

	/**
	 * 전체데이터 가져오는 함수
	*/
	function getTotalList(){
		$iSQL = "SELECT 
					@rownum:=@rownum+1 AS IDX,
					`NAL_LVL_MAIN`.`ALLM_ID`,
					`NAL_LVL_MAIN`.`ALLM_NM`,
					`NAL_LVL_MAIN`.`ALLM_MEMO`,
					`NAL_LVL_MAIN`.`WRT_DTHMS`,
					`NAL_LVL_MAIN`.`LAST_DTHMS`,
					`NAL_LVL_MAIN`.`WRT_USERID`
				FROM `NT_AUTH`.`NAL_LVL_MAIN`, (SELECT @rownum:=0) IDX
				WHERE DEL_YN = 'N'
				ORDER BY IDX DESC"
		;

		try{
			$Result= DBManager::getResult($iSQL);
		} catch (Exception $e){
			echo "error : ".$e;
			return ERROR_DB_SELECT_DATA;   //ERROR_DB_SELECT_DATA
		}
		$rtnArray = array();
		while($RS = $Result->fetch_array()){
		array_push($rtnArray,
			array(
			"IDX"=>$RS['IDX'],
			"ALLM_ID"=>$RS['ALLM_ID'],
			"ALLM_NM"=>$RS['ALLM_NM'],
			"ALLM_MEMO"=>$RS['ALLM_MEMO'],
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
			$rtnJSON = json_encode($jsonArray,JSON_UNESCAPED_UNICODE);

			return  $rtnJSON;
		}
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
		
		if($ARR['ALLM_ID_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(`NAU_USER`.`ALLM_ID`,'".addslashes($ARR['ALLM_ID_SEARCH'])."')>0 ";
		} 
		if($ARR['SEARCH_ACCOUNT'] != '') {
			if($ARR['SEARCH_ACCOUNT'] == 'AUU_LVL_NM'){
				$tmpKeyword .= "AND INSTR(`NAU_USER`.`AUU_LVL_NM`,'".addslashes($ARR['ACCOUNT_KEYWORD'])."')>0 ";
			}else if($ARR['SEARCH_ACCOUNT'] == 'AUU_NM'){
				$tmpKeyword .= "AND INSTR(`NAU_USER`.`AUU_NM`,'".addslashes($ARR['ACCOUNT_KEYWORD'])."')>0 ";
			}else if($ARR['SEARCH_ACCOUNT'] == 'AUU_EMAIL'){
				$tmpKeyword .= "AND INSTR(`NAU_USER`.`AUU_EMAIL`,'".addslashes($ARR['ACCOUNT_KEYWORD'])."')>0 ";
			}else if($ARR['SEARCH_ACCOUNT'] == 'AUU_PHONE_NUM'){
				$tmpKeyword .= "AND INSTR(`NAU_USER`.`AUU_PHONE_NUM`,'".addslashes($ARR['ACCOUNT_KEYWORD'])."')>0 ";
			}
		} 
	
		// 페이징처리를 위해 전체데이터
		$iSQL = "
				SELECT
					COUNT(*) AS CNT
				FROM
					`NT_AUTH`.`NAU_USER` 
				WHERE	`DEL_YN` = 'N'
				$tmpKeyword
				;"
			;
	
		try{
			$RS_T	= DBManager::getRecordSet($iSQL);
		} catch (Exception $e){
			echo "error : ".$e;
			return ERROR_DB_SELECT_DATA;   //ERROR_DB_SELECT_DATA
		}

		$TOTAL_LIST_COUNT = $RS_T['CNT'];
		$IDX = $RS_T['CNT']-$LimitStart;
		unset($RS_T);
		///

		/** 전체리스트가 아닌경우에만 limitSql 적용 */
		$LIMIT_SQL = "";
		if($ARR['REQ_MODE'] == CASE_LIST){
			$LIMIT_SQL = "LIMIT $LimitStart,$LimitEnd";
		}

		// 리스트 가져오기
		$iSQL = "SELECT 
					`NAU_USER`.`AUU_ID`,
					`NAU_USER`.`AUU_USERID`,
					`NAU_USER`.`ALLM_ID`,
					`NAL_LVL_MAIN`.`ALLM_NM`,
					`NAU_USER`.`AUU_LVL_NM`,
					`NAU_USER`.`AUU_NM`,
					`NAU_USER`.`AUU_EMAIL`,
					`NAU_USER`.`AUU_PHONE_NUM`,
					`NAU_USER`.`WRT_DTHMS`,
					`NAU_USER`.`LAST_DTHMS`,
					`NAU_USER`.`WRT_USERID`
				FROM `NT_AUTH`.`NAU_USER`, 
					 `NT_AUTH`.`NAL_LVL_MAIN`
				WHERE `NAU_USER`.`DEL_YN`  = 'N' AND 
					 `NAL_LVL_MAIN`. `ALLM_ID` = `NAU_USER`.`ALLM_ID`
					 $tmpKeyword 
				ORDER BY AUU_ID DESC 
				$LIMIT_SQL;"
		;
	
		$Result= DBManager::getResult($iSQL);
		///

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
				"IDX"=>$IDX--,
				"AUU_ID"=>$RS['AUU_ID'],
				"AUU_USERID"=>$RS['AUU_USERID'],
				"AUU_PASSWD"=>$RS['AUU_PASSWD'],
				"ALLM_ID"=>$RS['ALLM_ID'],
				"ALLM_NM"=>$RS['ALLM_NM'],
				"AUU_LVL_NM"=>$RS['AUU_LVL_NM'],
				"AUU_NM"=>$RS['AUU_NM'],
				"AUU_EMAIL"=>$RS['AUU_EMAIL'],
				"AUU_PHONE_NUM"=>$RS['AUU_PHONE_NUM'],
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
			$rtnJSON = json_encode($jsonArray,JSON_UNESCAPED_UNICODE);

			return  $jsonArray;
		}
		
	}

	/**
	 * 해당데이터 조회 
	 * @param {$ARR}
	 * @param {ALLM_ID : 조회할 해당 데이터 ID}
	 */
	function getData($ARR) {
		$iSQL = "SELECT 
					`NAL_LVL_MAIN`.`ALLM_ID`,
					`NAL_LVL_MAIN`.`ALLM_NM`,
					`NAL_LVL_MAIN`.`ALLM_MEMO`,
					`NAL_LVL_MAIN`.`WRT_DTHMS`,
					`NAL_LVL_MAIN`.`LAST_DTHMS`,
					`NAL_LVL_MAIN`.`WRT_USERID`
				FROM `NT_AUTH`.`NAL_LVL_MAIN`
				WHERE DEL_YN = 'N'
				AND ALLM_ID = '".addslashes($ARR['ALLM_ID'])."'
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
	 * 데이터생성
	 */
	function setData($ARR) {
		if($ARR['REQ_MODE'] == CASE_CREATE){
			/** 아이디 존재여부
			 * 등록일 경우에는 전체에서 존재하는지
			 */
			$iSQL = "SELECT 
						`USERID`
					FROM NT_AUTH.V_AUTH_LOGIN
					WHERE USERID = '".addslashes($ARR['AUU_USERID'])."' 
					;
					"
			;
			$RS= DBManager::getRecordSet($iSQL);
			if(!empty($RS)) {
				return ERROR;
			}


			$iSQL = "INSERT INTO `NT_AUTH`.`NAU_USER`
					(
						`AUU_USERID`,
						`AUU_PASSWD`,
						`ALLM_ID`,
						`AUU_LVL_NM`,
						`AUU_NM`,
						`AUU_EMAIL`,
						`AUU_PHONE_NUM`,
						`WRT_DTHMS`,
						`LAST_DTHMS`,
						`WRT_USERID`,
						`DEL_YN`
					)
					VALUES
					(
						'".addslashes($ARR['AUU_USERID'])."',
						PASSWORD('".addslashes($ARR['AUU_PASSWD'])."'),
						'".addslashes($ARR['ALLM_ID'])."',
						'".addslashes($ARR['AUU_LVL_NM'])."',
						'".addslashes($ARR['AUU_NM'])."',
						'".addslashes($ARR['AUU_EMAIL'])."',
						'".addslashes($ARR['AUU_PHONE_NUM'])."',
						NOW(),
						NOW(),
						'".addslashes($_SESSION['USERID'])."',
						'N'
					);"
			;
		}else{
			/** 아이디 존재여부
			 * 수정 경우에는 전체에서 본인제외하고 동일한게 있는지
			 */
			$iSQL = "SELECT 
						`AUU_USERID`
					FROM `NT_AUTH`.`NAU_USER`
					WHERE AUU_USERID = '".addslashes($ARR['AUU_USERID'])."' AND
					      AUU_ID != '".addslashes($ARR['AUU_ID'])."' AND
						  DEL_YN = 'N'
					;"
			;
			$RS= DBManager::getRecordSet($iSQL);
			if(!empty($RS)) {
				return ERROR;
			}


			$passwordSQL = '';
			if($ARR['AUU_PASSWD']!=''){
				$passwordSQL = "`AUU_PASSWD` = PASSWORD('".addslashes($ARR['AUU_PASSWD'])."'),";
			}

			$iSQL = "UPDATE  `NT_AUTH`.`NAU_USER`
					 SET 
						`AUU_USERID` = '".addslashes($ARR['AUU_USERID'])."',
						`ALLM_ID` = '".addslashes($ARR['ALLM_ID'])."',
						$passwordSQL 
						`AUU_LVL_NM`= '".addslashes($ARR['AUU_LVL_NM'])."',
						`AUU_NM` = '".addslashes($ARR['AUU_NM'])."',
						`AUU_EMAIL` = '".addslashes($ARR['AUU_EMAIL'])."',
						`AUU_PHONE_NUM` = '".addslashes($ARR['AUU_PHONE_NUM'])."',
						`LAST_DTHMS` = now()
						WHERE `AUU_ID`='".addslashes($ARR['AUU_ID'])."' 
					 ;"
			;
		
		}
		return DBManager::execQuery($iSQL);
	
	}

	/**
	 * 삭제
	 * @param {$ARR}
	 * @param {ALLM_ID : 삭제할 해당 데이터 ID}
	 */
	function delData($ARR) {
		$iSQL = "UPDATE `NT_AUTH`.`NAU_USER`
					SET  
						`LAST_DTHMS`= NOW(), 
						`DEL_YN`	='Y'
					WHERE `AUU_ID`='".addslashes($ARR['AUU_ID'])."'
					;"
		;

		return DBManager::execQuery($iSQL);
	}

	/** 등록모달에서 권한명 가져오기 위해 */
	function getAuthorityCode(){
		$iSQL = "SELECT 
					`NAL_LVL_MAIN`.`ALLM_ID`,
					`NAL_LVL_MAIN`.`ALLM_NM`
				FROM `NT_AUTH`.`NAL_LVL_MAIN`
				WHERE DEL_YN = 'N';
				"
		;

		$Result= DBManager::getResult($iSQL);
		///

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
				"IDX"=>$RS['IDX'],
				"ALLM_ID"=>$RS['ALLM_ID'],
				"ALLM_NM"=>$RS['ALLM_NM'],
				"ALLM_MEMO"=>$RS['ALLM_MEMO'],
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
			return  $rtnArray;
		}
	}


}
	// 0. 객체 생성
	$cAccount = new cAccount(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$cAccount->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
