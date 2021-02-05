<?php

class clsQna extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	// Class Object Constructor
	function clsQna($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
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
				$tmpKeyword .= "AND DATE(`CNV`.".$ARR['SELECT_DT'].") BETWEEN '".addslashes($ARR['START_DT'])."' AND '".addslashes($ARR['END_DT'])."'";
			}else if($ARR['START_DT'] !='' && $ARR['BIZ_END_DT_SEARCH'] ==''){
				$tmpKeyword .= "AND DATE(`CNV`.".$ARR['SELECT_DT'].") >= '".addslashes($ARR['START_DT'])."'";
			}else{
				$tmpKeyword .= "AND DATE(`CNV`.".$ARR['SELECT_DT'].") <= '".addslashes($ARR['END_DT'])."'";
			}
		} 

		//처리여부 검색 
		if($ARR['SEARCH_MVV_PROC_STATUS_CD'] != '') {
			$tmpKeyword .= "AND `CNV`.`MVV_PROC_STATUS_CD` = '".addslashes($ARR['SEARCH_MVV_PROC_STATUS_CD'])."' ";	
		} 

		//공업사명 검색 
		if($ARR['BIZ_NM_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(`MGG`.`MGG_NM`,'".addslashes($ARR['BIZ_NM_SEARCH'])."')>0 ";	
		} 

		//사업자번호 검색 
		if($ARR['BIZ_NUM_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(`MGG`.`MGG_BIZ_ID`,'".addslashes($ARR['BIZ_NUM_SEARCH'])."')>0 ";	
		} 
		//키워드
		if($ARR['KEYWORD_SELECT'] != ''){
			if($ARR['KEYWORD_SELECT'] == 'WRT_USERID'){
				$tmpKeyword .= "AND INSTR(`CNV`.`WRT_USERID`,'".addslashes($ARR['KEYWORD_SEARCH'])."')>0 ";
			}else if($ARR['KEYWORD_SELECT'] == 'MVV_QUESTION'){
				$tmpKeyword .= "AND INSTR(`CNV`.`MVV_QUESTION`,'".addslashes($ARR['KEYWORD_SEARCH'])."')>0 ";
			}else if($ARR['KEYWORD_SELECT'] == 'MVV_ANSWER'){
				$tmpKeyword .= "AND INSTR(`CNV`.`MVV_ANSWER`,'".addslashes($ARR['KEYWORD_SEARCH'])."')>0 ";
			}else if($ARR['KEYWORD_SELECT'] == 'MVV_ANSWER_USERID'){
				$tmpKeyword .= "AND INSTR(`CNV`.`MVV_ANSWER_USERID`,'".addslashes($ARR['KEYWORD_SEARCH'])."')>0 ";
			}else if($ARR['KEYWORD_SELECT'] == 'MVV_ANSWER_USERNM'){
				$tmpKeyword .= "AND INSTR(NT_AUTH.GET_USER_NM(CNV.MVV_ANSWER_USERID),'".addslashes($ARR['KEYWORD_SEARCH'])."')>0 ";
			}

		}

		// 페이징처리를 위해 전체데이터
		$iSQL = "SELECT 
					COUNT(*) AS CNT
						
				FROM `NT_COMMUNITY`.`NMV_VOC` CNV,
					`NT_MAIN`.`NMG_GARAGE` MGG
				WHERE CNV.DEL_YN = 'N'
				AND CNV.MGG_ID = MGG.MGG_ID
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
					CNV.`MVV_ID`,
					CNV.`MGG_ID`,
					MGG.`MGG_NM`,
					NT_CODE.GET_CODE_NM(`MGGS`.`RET_ID`) AS `RET_NM`,
					`MGGS`.`RMT_ID`,
					NT_CODE.GET_CODE_NM(`MGGS`.`RCT_ID`) AS `RCT_NM`,
					MGGS.`RCT_ID`,
					CNV.`MVC_ID`,
					CNC.`MVC_NM`,
					CNV.`MVV_QUESTION`,
					CNV.`MVV_ANSWER`,
					CNV.`MVV_ANSWER_USERID`,
					IFNULL(NT_AUTH.GET_USER_NM(CNV.MVV_ANSWER_USERID), '') AS `MVV_ANSWER_USERNM`,
					CNV.`MVV_DONE_DTHMS`,
					IFNULL(CNV.`MVV_DONE_DTHMS`, '') AS `MVV_DONE_DTHMS`,
					CNV.`MVV_PROC_STATUS_CD`,
					NT_CODE.GET_CODE_NM(CNV.MVV_PROC_STATUS_CD) AS MVV_PROC_STATUS_NM,
					CNV.`WRT_DTHMS`,
					CNV.`LAST_DTHMS`,
					CNV.`WRT_USERID`,

					NT_CODE.GET_CSD_NM (`MGG`.`CSD_ID`) AS `CSD_NM`,
					MGGS.`MGG_FRONTIER_NM`,
					MGGS.`MGG_BIZ_START_DT`,
					MGGS.`MGG_BIZ_END_DT`,
					NT_CODE.GET_CODE_NM(`MGGS`.`RSR_ID`) AS `RSR_NM`,
					MGGS.`MGG_OTHER_BIZ_NM`,
					MGGS.`MGG_OTHER_BIZ_NM`,
					MGG.`MGG_BIZ_ID`,
					CAST(AES_DECRYPT(`MGGS`.`MGG_CEO_NM_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `MGG_CEO_NM_AES`,
					CAST(AES_DECRYPT(`MGGS`.`MGG_CEO_TEL_AES`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `MGG_CEO_TEL_AES`,
					MGGS.`MGG_OFFICE_TEL`,
					MGGS.`MGG_OFFICE_FAX`,
					MGGS.`MGG_INSU_MEMO`,
					MGGS.`MGG_MANUFACTURE_MEMO`		
				FROM 
					`NT_COMMUNITY`.`NMV_VOC` CNV,
					`NT_COMMUNITY`.`NMV_CTGY` CNC,
					`NT_MAIN`.`NMG_GARAGE` MGG,
					`NT_MAIN`.`NMG_GARAGE_SUB` MGGS
				WHERE CNV.MGG_ID = MGG.MGG_ID
				AND MGG.MGG_ID = MGGS.MGG_ID
				AND CNV.MVC_ID = CNC.MVC_ID
				AND CNV.`DEL_YN` ='N'
				$tmpKeyword
				ORDER BY CNV.`WRT_DTHMS` DESC
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
					"MVV_ID"=>$RS['MVV_ID'],
					"MGG_ID"=>$RS['MGG_ID'],
					"MGG_NM"=>$RS['MGG_NM'],
					"RET_NM"=>$RS['RET_NM'],
					"RMT_ID"=>$RS['RMT_ID'],
					"RCT_NM"=>$RS['RCT_NM'],
					"RCT_ID"=>$RS['RCT_ID'],
					"MVC_ID"=>$RS['MVC_ID'],
					"MVC_NM"=>$RS['MVC_NM'],
					"MVV_QUESTION"=>$RS['MVV_QUESTION'],
					"MVV_ANSWER"=>$RS['MVV_ANSWER'],
					"MVV_ANSWER_USERID"=>$RS['MVV_ANSWER_USERID'],
					"MVV_ANSWER_USERNM"=>$RS['MVV_ANSWER_USERNM'],
					"MVV_DONE_DTHMS"=>$RS['MVV_DONE_DTHMS'],
					"MVV_PROC_STATUS_CD"=>$RS['MVV_PROC_STATUS_CD'],
					"MVV_PROC_STATUS_NM"=>$RS['MVV_PROC_STATUS_NM'],
					"WRT_DTHMS"=>$RS['WRT_DTHMS'],
					"LAST_DTHMS"=>$RS['LAST_DTHMS'],
					"WRT_USERID"=>$RS['WRT_USERID'],

					"CSD_NM"=>$RS['CSD_NM'],
					"MGG_FRONTIER_NM"=>$RS['MGG_FRONTIER_NM'],
					"MGG_BIZ_START_DT"=>$RS['MGG_BIZ_START_DT'],
					"MGG_BIZ_END_DT"=>$RS['MGG_BIZ_END_DT'],
					"RSR_NM"=>$RS['RSR_NM'],
					"MGG_OTHER_BIZ_NM"=>$RS['MGG_OTHER_BIZ_NM'],
					"MGG_BIZ_ID"=>$RS['MGG_BIZ_ID'],
					"MGG_CEO_NM"=>$RS['MGG_CEO_NM_AES'],
					"MGG_CEO_TEL"=>$RS['MGG_CEO_TEL_AES'],
					"MGG_OFFICE_TEL"=>$RS['MGG_OFFICE_TEL'],
					"MGG_OFFICE_FAX"=>$RS['MGG_OFFICE_FAX'],
					"MGG_INSU_MEMO"=>$RS['MGG_INSU_MEMO'],
					"MGG_MANUFACTURE_MEMO"=>$RS['MGG_MANUFACTURE_MEMO'],
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
	 * 삭제
	 * @param {$ARR}
	 */
	function delData($ARR) {
		$iSQL = "UPDATE `NT_COMMUNITY`.`NMV_VOC`
					SET  
						`LAST_DTHMS`= NOW(), 
						`DEL_YN`	='Y'
					WHERE `MVV_ID`='".addslashes($ARR['MVV_ID'])."'
					;"
		;
		// echo 	$iSQL ;
		return DBManager::execQuery($iSQL);
	}


	/**
	 * 데이터업데이트
	 */
	function setData($ARR) {
		$iSQL = "UPDATE `NT_COMMUNITY`.`NMV_VOC`
				 	SET
						`MVV_ANSWER` = '".addslashes($ARR['MVV_ANSWER'])."',
						`MVV_ANSWER_USERID` = '".addslashes($_SESSION['USERID'])."',
						`MVV_PROC_STATUS_CD` = '".addslashes($ARR['MVV_PROC_STATUS_CD'])."',
						`MVV_DONE_DTHMS` = NOW()
				WHERE `MVV_ID` = '".addslashes($ARR['MVV_ID'])."'
				;"
		;
		// echo 	$iSQL ;
		return DBManager::execQuery($iSQL);
	}


}
	// 0. 객체 생성
	$clsQna = new clsQna(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$clsQna->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
