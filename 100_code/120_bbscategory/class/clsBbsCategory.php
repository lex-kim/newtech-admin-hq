<?php

class cBbsCategory extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	// Class Object Constructor
	function cBbsCategory($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
		$this->DATA_SAVE_PATH = $DATA_SAVE_PATH;
		$this->ALLOW_FILE_EXT = $ALLOW_FILE_EXT;
	}

	/**
	 * 전체데이터 가져오는 함수
	*/
	function getTotalList(){
		$iSQL = "SELECT 
					@rownum:=@rownum+1 AS IDX,
					`NMG_CTGY`.`MGC_ID`,
					`NMG_CTGY`.`MGC_NM`,
					`NMG_CTGY`.`MGC_ORDER_NUM`,
					`NMG_CTGY`.`WRT_DTHMS`,
					`NMG_CTGY`.`LAST_DTHMS`,
					`NMG_CTGY`.`WRT_USERID`
				FROM `NT_COMMUNITY`.`NMG_CTGY`, (SELECT @rownum:=0) IDX
				WHERE DEL_YN = 'N'
				ORDER BY MGC_ORDER_NUM DESC"
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
			"MGC_ID"=>$RS['MGC_ID'],
			"MGC_NM"=>$RS['MGC_NM'],
			"MGC_ORDER_NUM"=>$RS['MGC_ORDER_NUM'],
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
	 * @param {CURR_PAGE : 현재페이지, PER_PAGE : 몇개를 조회할 것인지}
	 */
	function getList($ARR) {
		$LimitStart = ($ARR['CURR_PAGE']-1) * $ARR['PER_PAGE'];
		$LimitEnd   = $ARR['PER_PAGE'];

		// 페이징처리를 위해 전체데이터
		$iSQL = "
				SELECT
					COUNT(*) AS CNT
				FROM
					`NT_COMMUNITY`.`NMG_CTGY` 
				WHERE	`DEL_YN` = 'N'
				
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

		// 리스트 가져오기
		$iSQL = "SELECT 
					@rownum:=@rownum+1 AS IDX,
					`CGC`.`MGC_ID`,
					`CGC`.`MGC_NM`,
					`CGC`.`MGC_ORDER_NUM`,
					`CGC`.`WRT_DTHMS`,
					`CGC`.`LAST_DTHMS`,
					`CGC`.`WRT_USERID`
				FROM `NT_COMMUNITY`.`NMG_CTGY` `CGC`, (SELECT @rownum:=0) IDX
				WHERE DEL_YN = 'N'
				ORDER BY `CGC`.`MGC_ORDER_NUM` ASC 
				LIMIT $LimitStart,$LimitEnd;"
		;
	
		$Result= DBManager::getResult($iSQL);
		///

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
				"IDX"=>$IDX--,
				"MGC_ID"=>$RS['MGC_ID'],
				"MGC_NM"=>$RS['MGC_NM'],
				"MGC_ORDER_NUM"=>$RS['MGC_ORDER_NUM'],
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
	 * @param {MGC_ID : 조회할 해당 데이터 ID}
	 */
	function getData($ARR) {
		$iSQL = "SELECT 
					`NMG_CTGY`.`MGC_ID`,
					`NMG_CTGY`.`MGC_NM`,
					`NMG_CTGY`.`MGC_ORDER_NUM`,
				FROM `NT_COMMUNITY`.`NMG_CTGY`
				WHERE DEL_YN = 'N'
				AND MGC_ID = '".addslashes($ARR['MGC_ID'])."'
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
			/** 부품코드 존재여부
			 * 등록일 경우에는 전체에서 존재하는지
			 */
			$iSQL = "SELECT 
							`MGC_ID`
						FROM `NT_COMMUNITY`.`NMG_CTGY`
						WHERE MGC_ID = '".addslashes($ARR['MGC_ID'])."'
						;"
				;
			$RS= DBManager::getRecordSet($iSQL);
			if(!empty($RS)) {
				return CONFLICT;
			}

			if($ARR['IS_EXIST_ORDER_NUM']==''){
				/** 입력요청한 출력순서가 존재하는지 */
				$iSQL = "SELECT 
							`MGC_ORDER_NUM`
						FROM `NT_COMMUNITY`.`NMG_CTGY`
						WHERE MGC_ORDER_NUM = '".addslashes($ARR['MGC_ORDER_NUM'])."' AND
							DEL_YN = 'N'
						;"
				;

				$RS= DBManager::getRecordSet($iSQL);
				if(!empty($RS)) {
					return ERROR;
				}
			}

			/** 출력순서 업데이트 */
			$iSQL = "UPDATE  `NT_COMMUNITY`.`NMG_CTGY` 
					SET 
						`MGC_ORDER_NUM` = `MGC_ORDER_NUM`+1
					WHERE `MGC_ORDER_NUM` IN
						(
							SELECT `MGC_ORDER_NUM` FROM 
							(
								SELECT
									@rownum:=@rownum+1 AS IDX,
									`MGC_ORDER_NUM`,
									`MGC_ORDER_NUM` - @rownum AS GRP
								FROM `NT_COMMUNITY`.`NMG_CTGY`, (SELECT @rownum:=0) IDX
								WHERE `DEL_YN` = 'N' 
								AND `MGC_ORDER_NUM` >= '".addslashes($ARR['MGC_ORDER_NUM'])."' ORDER BY MGC_ORDER_NUM ASC 
								) AS CMNT WHERE GRP = '".addslashes($ARR['MGC_ORDER_NUM'])."'-1
						)
					;"
			;

			$Result = DBManager::execQuery($iSQL);

			/** insert*/
			if($Result){
				$iSQL = "INSERT INTO `NT_COMMUNITY`.`NMG_CTGY`
						(
							`MGC_ID`,
							`MGC_NM`,
							`MGC_ORDER_NUM`,
							`WRT_DTHMS`,
							`LAST_DTHMS`,
							`WRT_USERID`,
							`DEL_YN`
						)
						VALUES
						(
							'".addslashes($ARR['MGC_ID'])."',
							'".addslashes($ARR['MGC_NM'])."',
							'".addslashes($ARR['MGC_ORDER_NUM'])."',
							NOW(),
							NOW(),
							'".addslashes($_SESSION['USERID'])."', 
							'N'
						);"
				;
				
				return DBManager::execQuery($iSQL);
			}else{
				return SERVER_ERROR;
			}
			

		}else{
			/** 부품코드 존재여부
			 * 수정 경우에는 전체에서 본인제외하고 동일한게 있는지
			 */
			if($ARR['IS_EXIST_ORDER_NUM']==''){
				/** 입력요청한 출력순서가 존재하는지 */
				$iSQL = "SELECT 
							`MGC_ORDER_NUM`
						FROM `NT_COMMUNITY`.`NMG_CTGY`
						WHERE MGC_ORDER_NUM = '".addslashes($ARR['MGC_ORDER_NUM'])."' AND
							`MGC_ID` != '".addslashes($ARR['MGC_ID'])."' AND
							DEL_YN = 'N'
						;"
				;

				$RS= DBManager::getRecordSet($iSQL);
				if(!empty($RS)) {
					return ERROR;
				}
			 }
			
			
			$Result = true;
				
			if($ARR['MGC_ORDER_NUM'] != $ARR['ORI_MGC_ORDER_NUM'] ){
				$iSQL = "UPDATE  `NT_COMMUNITY`.`NMG_CTGY` 
						SET 
							`MGC_ORDER_NUM` = `MGC_ORDER_NUM`+1
						WHERE `MGC_ORDER_NUM` IN
							(
								SELECT `MGC_ORDER_NUM` FROM 
								(
									SELECT
										@rownum:=@rownum+1 AS IDX,
										`MGC_ORDER_NUM`,
										`MGC_ORDER_NUM` - @rownum AS GRP
									FROM `NT_COMMUNITY`.`NMG_CTGY`, (SELECT @rownum:=0) IDX
									WHERE `DEL_YN` = 'N' 
									AND `MGC_ORDER_NUM` >= '".addslashes($ARR['MGC_ORDER_NUM'])."' ORDER BY MGC_ORDER_NUM ASC 
									) AS CMNT WHERE GRP = '".addslashes($ARR['MGC_ORDER_NUM'])."'-1
							)
						;"
				;
				$Result = DBManager::execQuery($iSQL);
			}

			if($Result){
				$iSQL = "UPDATE  `NT_COMMUNITY`.`NMG_CTGY`
						 SET 
							MGC_NM = '".addslashes($ARR['MGC_NM'])."',
							MGC_ID = '".addslashes($ARR['MGC_ID'])."',
							MGC_ORDER_NUM = '".addslashes($ARR['MGC_ORDER_NUM'])."',
							LAST_DTHMS = now()
						 WHERE MGC_ID='".addslashes($ARR['MGC_ID'])."' 
						;"
				;
				return DBManager::execQuery($iSQL);
			}else{
				return SERVER_ERROR;
			}
		
		}	
	}





	/**
	 * 삭제
	 * @param {$ARR}
	 * @param {MGC_ID : 삭제할 해당 데이터 ID}
	 */
	function delData($ARR) {
		$iSQL = "UPDATE `NT_COMMUNITY`.`NMG_CTGY`
					SET  
						`LAST_DTHMS`= NOW(), 
						`DEL_YN`	='Y'
					WHERE `MGC_ID`='".addslashes($ARR['MGC_ID'])."'
					;"
		;

		return DBManager::execQuery($iSQL);
	}


}
	// 0. 객체 생성
	$cBbsCategory = new cBbsCategory(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$cBbsCategory->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
