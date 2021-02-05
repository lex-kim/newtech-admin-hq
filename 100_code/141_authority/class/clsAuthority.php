<?php

class cAuthority extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	// Class Object Constructor
	function cAuthority($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
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

		if($ARR['KEYWORD'] != '') {
			$tmpKeyword = "AND INSTR(`NAL_LVL_MAIN`.`ALLM_NM`,'".addslashes($ARR['KEYWORD'])."')>0 ";
		}

	
		// 페이징처리를 위해 전체데이터
		$iSQL = "
				SELECT
					COUNT(*) AS CNT
				FROM
					`NT_AUTH`.`NAL_LVL_MAIN` 
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
		unset($RS_T);

		// 리스트 가져오기
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
				$tmpKeyword 
				ORDER BY IDX DESC 
				$LIMIT_SQL 
				;
		";
	
		$Result= DBManager::getResult($iSQL);

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
					NUI.NUI_1ST,
					IFNULL(NUI.NUI_2ND,'') AS NUI_2ND,
					IFNULL(NUI.NUI_3RD,'') AS NUI_3RD,
					IFNULL(NUI.NUI_4TH,'') AS NUI_4TH,
					NUI.NUI_URI,
					NUI.WRT_DTHMS,
					NUI.LAST_DTHMS,
					NUI.WRT_USER_ID,
					ALLS.ALLS_CREATE_YN,
					ALLS.ALLS_READ_YN,
					ALLS.ALLS_UPDATE_YN,
					ALLS.ALLS_DELETE_YN,
					ALLS.ALLS_EXCEL_YN,
					ALLS.ALLM_ID,
					ALLM.ALLM_NM,
					ALLM.ALLM_MEMO
				FROM `NT_AUTH`.`NAL_URI_INFO`NUI
				LEFT JOIN	NT_AUTH.NAL_LVL_SUB ALLS
					ON ALLS.NUI_ID = NUI.NUI_ID
					AND ALLS.DEL_YN = 'N'
					AND ALLS.ALLM_ID = '".addslashes($ARR['ALLM_ID'])."'
				LEFT JOIN	 NT_AUTH.NAL_LVL_MAIN ALLM	
					ON ALLM.ALLM_ID = ALLS.ALLM_ID
				WHERE NUI_SHOW_YN = 'Y'
				ORDER BY NUI.NUI_ID ASC 
				;"
		;
		// echo $iSQL;
		$Result= DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
		array_push($rtnArray,
			array(
				"ALLM_NM"=>$RS['ALLM_NM'],
				"ALLM_MEMO"=>$RS['ALLM_MEMO'],
				"ALLM_ID"=>$RS['ALLM_ID'],
				"ALLS_URI"=>$RS['ALLS_URI'],
				"NUI_ID"=>$RS['NUI_ID'],
				"NUI_1ST"=>$RS['NUI_1ST'],
				"NUI_2ND"=>$RS['NUI_2ND'],
				"NUI_3RD"=>$RS['NUI_3RD'],
				"NUI_4TH"=>$RS['NUI_4TH'],
				"NUI_URI"=>$RS['NUI_URI'],
				"ALLS_CREATE_YN"=>$RS['ALLS_CREATE_YN'],
				"ALLS_READ_YN"=>$RS['ALLS_READ_YN'],
				"ALLS_UPDATE_YN"=>$RS['ALLS_UPDATE_YN'],
				"ALLS_DELETE_YN"=>$RS['ALLS_DELETE_YN'],
				"ALLS_EXCEL_YN"=>$RS['ALLS_EXCEL_YN'],
				"WRT_DTHMS"=>$RS['WRT_DTHMS'],
				"LAST_DTHMS"=>$RS['LAST_DTHMS']
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



	function getCheckedSQL($ARR){
		$iSQL = "";
		$TOTAL_ARRAY = $this->getSubList();
		foreach($TOTAL_ARRAY as $tIdx => $T_ITEM){
			$NUI_URI = $T_ITEM['NUI_URI'];
			$CRATE_YN = "N";
			$READ_YN = "N";
			$UPDATE_YN = "N";
			$DELETE_YN = "N";
			$EXCEL_YN = "N";
			$NUI_ID  = $T_ITEM['NUI_ID'];

			foreach($ARR['DATA'] as $index => $ITEM){
				$COL_NM = json_decode($ITEM)->TYPE;
				$URI = json_decode($ITEM)->URI;		

				if($NUI_URI == $URI){
					if($COL_NM == "CREATE"){
						$CRATE_YN = "Y";
					}
					if($COL_NM == "READ"){
						$READ_YN = "Y";
					}
					if($COL_NM == "UPDATE"){
						$UPDATE_YN = "Y";
					}
					if($COL_NM == "DELETE"){
						$DELETE_YN = "Y";
					}
					if($COL_NM == "EXCEL"){
						$EXCEL_YN = "Y";
					}
				}
			}
			$iSQL .= "INSERT INTO `NT_AUTH`.`NAL_LVL_SUB` 
					(
						`NUI_ID`,
						`ALLM_ID`,
						`ALLS_URI`,
						`ALLS_CREATE_YN`,
						`ALLS_READ_YN`,
						`ALLS_UPDATE_YN`,
						`ALLS_DELETE_YN`,
						`ALLS_EXCEL_YN`,
						`WRT_DTHMS`,
						`LAST_DTHMS`,
						`WRT_USERID`,
						`DEL_YN`
					)
					 VALUES
					 (
						 '".$NUI_ID."',
						'".addslashes($ARR['ALLM_ID'])."',
						'".$NUI_URI."',
						 '".$CRATE_YN."' ,
						 '".$READ_YN."',
						'".$UPDATE_YN."' ,
						 '".$DELETE_YN."',
						'".$EXCEL_YN."' ,
						 NOW(),
						 NOW(),
						 '".addslashes($_SESSION['USERID'])."',
						 'N'
					 );
					 "
			 ;

		}
		return $iSQL;
	}

	/**
	 * 데이터생성
	 */
	function setData($ARR) {
		$iSQL = "";

		if($ARR['REQ_MODE'] == CASE_CREATE){
			$iSQL = "INSERT INTO `NT_AUTH`.`NAL_LVL_MAIN` 
					VALUES
					(
						'".addslashes($ARR['ALLM_ID'])."',
						'".addslashes($ARR['ALLM_NM'])."',
						'".addslashes($ARR['ALLM_MEMO'])."',
						NOW(),
						NOW(),
						'".addslashes($_SESSION['USERID'])."',
						'N'
					);
					"
			;

			$iSQL .= $this->getCheckedSQL($ARR);
			// echo $iSQL;
			return DBManager::execMulti($iSQL);
		}else{
			$iSQL = "UPDATE  `NT_AUTH`.`NAL_LVL_MAIN`
						SET ALLM_NM = '".addslashes($ARR['ALLM_NM'])."',
							ALLM_ID = '".addslashes($ARR['ALLM_ID'])."',
							ALLM_MEMO = '".addslashes($ARR['ALLM_MEMO'])."',
							LAST_DTHMS = now()
						WHERE ALLM_ID='".addslashes($ARR['ORI_ALLM_ID'])."' 
					 ;"
			;
			$iSQL .= "DELETE FROM `NT_AUTH`.`NAL_LVL_SUB` WHERE ALLM_ID='".addslashes($ARR['ORI_ALLM_ID'])."';";
			$iSQL .= $this->getCheckedSQL($ARR);
			// echo $iSQL;
			return DBManager::execMulti($iSQL);
		}
	}

	function getSubList(){
		$iSQL = "SELECT 
					NUI_ID,
					NUI_1ST,
					IFNULL(NUI_2ND,'') AS NUI_2ND,
					IFNULL(NUI_3RD,'') AS NUI_3RD,
					IFNULL(NUI_4TH,'') AS NUI_4TH,
					NUI_URI,
					WRT_DTHMS,
					LAST_DTHMS,
					WRT_USER_ID
				FROM `NT_AUTH`.`NAL_URI_INFO`
				WHERE NUI_SHOW_YN = 'Y' 
				ORDER BY NUI_ID ASC 
			;
		";

		$Result= DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
		array_push($rtnArray,
			array(
				"NUI_ID"=>$RS['NUI_ID'],
				"NUI_1ST"=>$RS['NUI_1ST'],
				"NUI_2ND"=>$RS['NUI_2ND'],
				"NUI_3RD"=>$RS['NUI_3RD'],
				"NUI_4TH"=>$RS['NUI_4TH'],
				"NUI_URI"=>$RS['NUI_URI'],
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

	function onCheckConflicID($ARR){
		$SUB_SQL = empty($ARR['ORI_ALLM_ID']) ? "" : " AND ALLM_ID !='".addslashes($ARR['ORI_ALLM_ID'])."'";
		$iSQL = "SELECT 
					ALLM_ID
				FROM NT_AUTH.NAL_LVL_MAIN
				WHERE ALLM_ID = '".addslashes($ARR['ALLM_ID'])."'
				$SUB_SQL 
			;
		";
	
		$Result= DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
		array_push($rtnArray,
			array(
				"ALLM_ID"=>$RS['ALLM_ID']
			));
		}
		$Result->close();  // 자원 반납
		unset($RS);

		if(empty($rtnArray)) {
			return OK;
		} else {
			return CONFLICT;
		}
	}

	/**
	 * 삭제
	 * @param {$ARR}
	 * @param {ALLM_ID : 삭제할 해당 데이터 ID}
	 */
	function delData($ARR) {
		$iSQL = "UPDATE `NT_AUTH`.`NAL_LVL_MAIN`
				SET  
					`LAST_DTHMS`= NOW(), 
					`DEL_YN`	='Y'
				WHERE `ALLM_ID`='".addslashes($ARR['ALLM_ID'])."'
				;"
		;

		$iSQL .= "UPDATE `NT_AUTH`.`NAL_LVL_SUB`
				SET  
					`LAST_DTHMS`= NOW(), 
					`DEL_YN`	= 'Y'
				WHERE `ALLM_ID`='".addslashes($ARR['ALLM_ID'])."'
				;"
		;
		return DBManager::execMulti($iSQL);
	}


}
	// 0. 객체 생성
	$cAuthority = new cAuthority(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$cAuthority->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
