<?php

class cAdmin extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	function cAdmin($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
		$this->DATA_SAVE_PATH = $DATA_SAVE_PATH;
		$this->ALLOW_FILE_EXT = $ALLOW_FILE_EXT;
	}



	/**
	 * 조회 
	 * @param {Array} $ARR 
	 * 			{int} CURR_PAGE  	현재페이지
	 * 			{int} PER_PAGE  	몇개를 조회할 것인지
	 * 			{String} KEYWORD  	검색조건
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

		if($ARR['SEARCH_ASA_USERID'] != '') {
			$tmpKeyword = "AND INSTR(NA.`ASA_USERID`,'".addslashes($ARR['SEARCH_ASA_USERID'])."')>0 ";
		}
	
		// 페이징처리를 위해 전체데이터
		$iSQL = "
				SELECT
					COUNT(*) AS CNT
				FROM
					`NT_AUTH`.`NAS_ADMIN` NA
				WHERE	
					NA.`DEL_YN` = 'N'
					$tmpKeyword
				;
			";
			
		try{
			$RS_T	= DBManager::getRecordSet($iSQL);
		} catch (Exception $e){
			echo "error : ".$e;
			return ERROR_DB_SELECT_DATA;  
		}

		$TOTAL_LIST_COUNT = $RS_T['CNT'];
		$IDX = $RS_T['CNT']-$LimitStart;
		unset($RS_T);

		
		// 리스트 가져오기
		$iSQL = "SELECT 
					NA.`ASA_ID`,
					NA.`ASA_USERID`,
					NA.`ASA_NM`,
					NA.`WRT_DTHMS`,
					NA.`LAST_DTHMS`,
					NA.`WRT_USERID`,
					NA.`DEL_YN`
				FROM `NT_AUTH`.`NAS_ADMIN` NA
				WHERE  NA.`DEL_YN` ='N'
					$tmpKeyword 
				ORDER BY NA.`WRT_DTHMS` DESC
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
				"ASA_ID"=>$RS['ASA_ID'],
				"ASA_USERID"=>$RS['ASA_USERID'],
				"ASA_NM"=>$RS['ASA_NM'],
				"WRT_DTHMS"=>$RS['WRT_DTHMS'],
				"LAST_DTHMS"=>$RS['LAST_DTHMS'],
				"WRT_USERID"=>$RS['WRT_USERID']
			));
		}
		$Result->close();  
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
	 * 데이터생성
	 */
	/**
	 * 데이터생성 
	 * @param {Array} $ARR 
	 * 			{String} ASA_USERID  	관리자 ID
	 * 			{String} ASA_PASSWD  	관리자 패스워드
	 * 			{String} ASA_NM  		관리자명
	 * 			{String} USERID  	등록자
	 */
	function setData($ARR) {
		if($ARR['REQ_MODE'] == CASE_CREATE){
			/** 아이디 존재여부
			 * 등록일 경우에는 전체에서 존재하는지
			 */
			$iSQL = "SELECT 
						`USERID`
					FROM NT_AUTH.V_AUTH_LOGIN
					WHERE USERID = '".addslashes($ARR['ASA_USERID'])."' ;
			";
			// echo $iSQL;
			$RS= DBManager::getRecordSet($iSQL);
			if(!empty($RS)) {
				return ERROR;
			}
			unset($RS);

			$iSQL = "INSERT INTO `NT_AUTH`.`NAS_ADMIN`
							(
								`ASA_USERID`,
								`ASA_PASSWD`,
								`ASA_NM`,
								`WRT_DTHMS`,
								`LAST_DTHMS`,
								`WRT_USERID`,
								`DEL_YN`
							)
							VALUES
							(
								'".addslashes($ARR['ASA_USERID'])."',	
								PASSWORD('".addslashes($ARR['ASA_PASSWD'])."'),	
								'".addslashes($ARR['ASA_NM'])."',
								NOW(),
								NOW(),
								'".addslashes($_SESSION['USERID'])."',
								'N'
							);
			";
		} else {
			if(isset($ARR['ASA_PASSWD']) && !empty($ARR['ASA_PASSWD']) ){
				$passwd ="  `ASA_PASSWD` = PASSWORD('".addslashes($ARR['ASA_PASSWD'])."'),	";
			}
			$iSQL = "UPDATE `NT_AUTH`.`NAS_ADMIN`
						SET $passwd
							`ASA_NM` = '".addslashes($ARR['ASA_NM'])."',
							`LAST_DTHMS` = 	NOW(),
							`WRT_USERID` = '".addslashes($_SESSION['USERID'])."'
						WHERE 
							`ASA_ID` =	'".addslashes($ARR['ASA_ID'])."'
						AND `ASA_USERID` = '".addslashes($ARR['ASA_USERID'])."' ;	
			";
		}
		return DBManager::execQuery($iSQL);
	}

	/**
	 * 삭제
	 * @param {$ARR}
	 * @param {ALLM_ID : 삭제할 해당 데이터 ID}
	 */
	function delData($ARR) {
		$iSQL = "UPDATE `NT_AUTH`.`NAS_ADMIN`
					SET  
						`LAST_DTHMS` = NOW(), 
						`WRT_USERID` = '".addslashes($_SESSION['USERID'])."', 
						`DEL_YN`	='Y'
					WHERE `ASA_USERID`='".addslashes($ARR['ASA_USERID'])."'
					;"
		;
		return DBManager::execQuery($iSQL);
	}
	

}
	// 0. 객체 생성
	$cAdmin = new cAdmin(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$cAdmin->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
