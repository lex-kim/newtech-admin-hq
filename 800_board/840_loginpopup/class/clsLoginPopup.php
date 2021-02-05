<?php
class clsLoginPopup extends DBManager {
	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자
	// Class Object Constructor
	function clsLoginPopup($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
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
		/** 전체리스트가 아닌경우에만 limitSql 적용 */
		$LIMIT_SQL = "";
		if($ARR['REQ_MODE'] == CASE_LIST){
			$LIMIT_SQL = "LIMIT $LimitStart,$LimitEnd";
		}
		/** 기간검색 */
		if($ARR['SELECT_DT'] != '') {
			if($ARR['START_DT'] !='' && $ARR['END_DT'] !=''){
				$tmpKeyword .= "AND DATE(`CNP`.".$ARR['SELECT_DT'].") BETWEEN '".addslashes($ARR['START_DT'])."' AND '".addslashes($ARR['END_DT'])."'";
			}else if($ARR['START_DT'] !='' && $ARR['BIZ_END_DT_SEARCH'] ==''){
				$tmpKeyword .= "AND DATE(`CNP`.".$ARR['SELECT_DT'].") >= '".addslashes($ARR['START_DT'])."'";
			}else{
				$tmpKeyword .= "AND DATE(`CNP`.".$ARR['SELECT_DT'].") <= '".addslashes($ARR['END_DT'])."'";
			}
		} 
		//제목 검색 
		if($ARR['TITLE_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(`CNP`.`NP_TITLE`,'".addslashes($ARR['TITLE_SEARCH'])."')>0 ";	
		} 
		//내용 검색 
		if($ARR['CONTENT_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(`CNP`.`NP_DTL`,'".addslashes($ARR['CONTENT_SEARCH'])."')>0 ";	
		} 
		//노출 검색 
		if($ARR['EXPOSURE_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(`CNP`.`NP_SHOW_YN`,'".addslashes($ARR['EXPOSURE_SEARCH'])."')>0 ";	
		} 


		// 페이징처리를 위해 전체데이터
		$iSQL = "SELECT 
					COUNT(*) AS CNT
						
				FROM `NT_COMMUNITY`.`NMP_POPUP` CNP
				WHERE 1=1
				$tmpKeyword
				"
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
		// 리스트 가져오기
		$iSQL = "SELECT 
					CNP.`NP_ID`,
					CNP.`NP_TITLE`,
					CNP.`NP_DTL`,
					CNP.`NP_SHOW_YN`,
					CNP.`WRT_DTHMS`,
					CNP.`LAST_DTHMS`,
					CNP.`WRT_USERID`
				FROM 
					`NT_COMMUNITY`.`NMP_POPUP` CNP
				WHERE 1=1
				$tmpKeyword
				ORDER BY CNP.`WRT_DTHMS` DESC 
				$LIMIT_SQL
				;"
		
		;
		// echo $iSQL."\n";
		$Result = DBManager::getResult($iSQL);
		///
		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"IDX"=>$IDX--,
					"NP_ID"=>$RS['NP_ID'],
					"NP_TITLE"=>$RS['NP_TITLE'],
					"NP_DTL"=>$RS['NP_DTL'],
					"NP_SHOW_YN"=>$RS['NP_SHOW_YN'],
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
	 * 데이터업데이트
	 */

	function setData($ARR) {
		$iSQL = "";
		if($ARR['NP_SHOW_YN'] == 'Y'){
			$iSQL .= "CALL NT_COMMUNITY.CHK_POPUP_SHOW();";
		}

		
		if($ARR['REQ_MODE'] == CASE_CREATE){

			/** insert*/
			$iSQL .= "INSERT INTO `NT_COMMUNITY`.`NMP_POPUP`
					(
						`NP_TITLE`,
						`NP_DTL`,
						`NP_SHOW_YN`,
						`WRT_DTHMS`,
						`LAST_DTHMS`,
						`WRT_USERID`
					)
					VALUES
					(
						'".addslashes($ARR['NP_TITLE'])."',
						'".addslashes($ARR['NP_DTL'])."',
						'".addslashes($ARR['NP_SHOW_YN'])."',
						NOW(),
						NOW(),
						'".addslashes($_SESSION['USERID'])."'
					);"
			;
				
			

		}else{
			$iSQL .= "UPDATE `NT_COMMUNITY`.`NMP_POPUP`
						SET
								`NP_TITLE` = '".addslashes($ARR['NP_TITLE'])."',
								`NP_DTL` = '".addslashes($ARR['NP_DTL'])."',
								`NP_SHOW_YN` = '".addslashes($ARR['NP_SHOW_YN'])."',
								`LAST_DTHMS` = NOW(),
								`WRT_USERID` = '".addslashes($_SESSION['USERID'])."'
						WHERE `NP_ID` = '".addslashes($ARR['NP_ID'])."'
						;"
			;
		
		}	

		// echo $iSQL."\n";
		return DBManager::execMulti($iSQL);
	}




	/**
	 * 삭제
	 * @param {$ARR}
	 */
	function delData($ARR) {
		$iSQL = "DELETE FROM `NT_COMMUNITY`.`NMP_POPUP`
					WHERE `NP_ID`='".addslashes($ARR['NP_ID'])."'
					;"
		;
		// echo 	$iSQL ;
		return DBManager::execQuery($iSQL);
	}


	/**
	 * 해당데이터 조회 
	 * @param {$ARR}
	 * @param {NP_ID : 조회할 해당 데이터 ID}
	 */
	function getData($ARR) {
		$iSQL = "SELECT
					CNP.`NP_ID`,
					CNP.`NP_TITLE`,
					CNP.`NP_DTL`,
					CNP.`NP_SHOW_YN`,
					CNP.`WRT_DTHMS`,
					CNP.`LAST_DTHMS`,
					CNP.`WRT_USERID`
				FROM 
					`NT_COMMUNITY`.`NMP_POPUP` CNP
				WHERE 
					NP_ID = '".addslashes($ARR['NP_ID'])."'
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
}
	// 0. 객체 생성
	$clsLoginPopup = new clsLoginPopup(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$clsLoginPopup->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);
?>