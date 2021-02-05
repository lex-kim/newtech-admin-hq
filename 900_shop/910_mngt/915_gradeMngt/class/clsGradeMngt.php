<?php

class clsGradeMngt extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	// Class Object Constructor
	function clsGradeMngt($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
		$this->DATA_SAVE_PATH = $DATA_SAVE_PATH;
		$this->ALLOW_FILE_EXT = $ALLOW_FILE_EXT;
	}

	/**
	 * 전체데이터 가져오는 함수
	*/
	function getTotalList(){
		// 리스트 가져오기
		$iSQL = "SELECT 
					@rownum:=@rownum+1 AS IDX,
					`NPL_LEVEL`.`SLL_ID`,
					`NPL_LEVEL`.`SLL_NM`,
					`NPL_LEVEL`.`SLL_ORDER_NUM`,
					`NPL_LEVEL`.`SLL_BILL_RATE`,
					`NPL_LEVEL`.`SLL_DISCOUNT_RATE`,
					`NPL_LEVEL`.`SLL_BUY_RATE`,
					`NPL_LEVEL`.`SLL_MEMO`,
					`NPL_LEVEL`.`WRT_DTHMS`,
					`NPL_LEVEL`.`LAST_DTHMS`,
					`NPL_LEVEL`.`WRT_USERID`,
					`NPL_LEVEL`.`DEL_YN`
				FROM `NT_POINT`.`NPL_LEVEL`, (SELECT @rownum:=0) IDX
				WHERE `DEL_YN`  = 'N'  
					 $tmpKeyword 
				ORDER BY SLL_ORDER_NUM DESC 
				LIMIT $LimitStart,$LimitEnd"
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
				"SLL_ID"=>$RS['SLL_ID'],
				"SLL_NM"=>$RS['SLL_NM'],
				"SLL_ORDER_NUM"=>$RS['SLL_ORDER_NUM'],
				"SLL_BILL_RATE"=>$RS['SLL_BILL_RATE'],
				"SLL_DISCOUNT_RATE"=>$RS['SLL_DISCOUNT_RATE'],
				"SLL_BUY_RATE"=>$RS['SLL_BUY_RATE'],
				"SLL_MEMO"=>$RS['SLL_MEMO'],
				"WRT_DTHMS"=>$RS['WRT_DTHMS'],
				"LAST_DTHMS"=>$RS['LAST_DTHMS'],
				"WRT_USERID"=>$RS['WRT_USERID'],
				"DEL_YN"=>$RS['DEL_YN']
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
	 * 조회 
	 * @param {$ARR}
	 * @param {CURR_PAGE : 현재페이지, PER_PAGE : 몇개를 조회할 것인지, KEYWORD : 검색조건}
	 */
	function getList($ARR) {
		$LimitStart = ($ARR['CURR_PAGE']-1) * $ARR['PER_PAGE'];
		$LimitEnd   = $ARR['PER_PAGE'];
		$tmpKeyword = "";

	

		/** 기간검색 */
		if($ARR['START_PUR2'] != '') {
			if($ARR['START_PUR'] !='' && $ARR['END_PUR'] !=''){
				$tmpKeyword .= "AND `SLL`.".$ARR['START_PUR2']." BETWEEN '".addslashes($ARR['START_PUR'])."' AND '".addslashes($ARR['END_PUR'])."'";
			}else if($ARR['START_PUR'] !='' && $ARR['END_PUR'] ==''){
				$tmpKeyword .= "AND `SLL`.".$ARR['START_PUR2']." >= '".addslashes($ARR['START_PUR'])."'";
			}else{
				$tmpKeyword .= "AND `SLL`.".$ARR['START_PUR2']." <= '".addslashes($ARR['END_PUR'])."'";
			}
		} 

		// 키필드검색
		if($ARR['CHANGE_KEY_SEARCH'] != ''){
			if($ARR['CHANGE_KEY_SEARCH'] == 'SLL_NM'){
				$tmpKeyword .= "AND INSTR(`SLL`.`SLL_NM`,'".addslashes($ARR['CHANGE_KEYWORD_SEARCH'])."')>0  ";
			}else if($ARR['CHANGE_KEY_SEARCH'] == 'WRT_USERID'){
				$tmpKeyword .= "AND INSTR(`SLL`.`WRT_USERID`,'".addslashes($ARR['CHANGE_KEYWORD_SEARCH'])."')>0  ";
			}
		}

		// 페이징처리를 위해 전체데이터
		$iSQL = "SELECT
					COUNT(*) AS CNT
				FROM
					`NT_POINT`.`NPL_LEVEL` `SLL`
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
					`SLL`.`SLL_ID`,
					`SLL`.`SLL_NM`,
					`SLL`.`SLL_ORDER_NUM`,
					`SLL`.`SLL_BILL_RATE`,
					`SLL`.`SLL_DISCOUNT_RATE`,
					`SLL`.`SLL_BUY_RATE`,
					`SLL`.`SLL_MEMO`,
					IFNULL(`SLL`.`WRT_DTHMS`, '') AS WRT_DTHMS,
					`SLL`.`LAST_DTHMS`,
					IFNULL(`SLL`.`WRT_USERID`, '') AS WRT_USERID
				FROM `NT_POINT`.`NPL_LEVEL` `SLL`
				WHERE `DEL_YN`  = 'N'  
					 $tmpKeyword 
				ORDER BY `SLL`.`SLL_ORDER_NUM` ASC 
				$LIMIT_SQL;
				"
		;
		// echo $iSQL."\n";
		$Result= DBManager::getResult($iSQL);
		///

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
				"IDX"=>$IDX--,
				"SLL_ID"=>$RS['SLL_ID'],
				"SLL_NM"=>$RS['SLL_NM'],
				"SLL_ORDER_NUM"=>$RS['SLL_ORDER_NUM'],
				"SLL_BILL_RATE"=>$RS['SLL_BILL_RATE'],
				"SLL_DISCOUNT_RATE"=>$RS['SLL_DISCOUNT_RATE'],
				"SLL_BUY_RATE"=>$RS['SLL_BUY_RATE'],
				"SLL_MEMO"=>$RS['SLL_MEMO'],
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
	 * @param {SLL_ID : 조회할 해당 데이터 ID}
	 */
	function getData($ARR) {
		$iSQL = "SELECT 
					`NPL_LEVEL`.`SLL_ID`,
					`NPL_LEVEL`.`SLL_NM`,
					`NPL_LEVEL`.`SLL_ORDER_NUM`,
					`NPL_LEVEL`.`SLL_BILL_RATE`,
					`NPL_LEVEL`.`SLL_DISCOUNT_RATE`,
					`NPL_LEVEL`.`SLL_BUY_RATE`,
					`NPL_LEVEL`.`SLL_MEMO`
				FROM `NT_POINT`.`NPL_LEVEL`
				WHERE DEL_YN = 'N'
				AND SLL_ID = '".addslashes($ARR['SLL_ID'])."'
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
			/** 코드 존재여부
			 * 등록일 경우에는 전체에서 존재하는지
			 */
			$iSQL = "SELECT 
							`SLL_ID`
						FROM `NT_POINT`.`NPL_LEVEL`
						WHERE SLL_ID = '".addslashes($ARR['SLL_ID'])."'
						;"
				;
			$RS= DBManager::getRecordSet($iSQL);
			if(!empty($RS)) {
				return CONFLICT;
			}

			if($ARR['IS_EXIST_ORDER_NUM']==''){
				/** 입력요청한 출력순서가 존재하는지 */
				$iSQL = "SELECT 
							`SLL_ORDER_NUM`
						FROM `NT_POINT`.`NPL_LEVEL`
						WHERE SLL_ORDER_NUM = '".addslashes($ARR['SLL_ORDER_NUM'])."' AND
							DEL_YN = 'N'
						;"
				;

				$RS= DBManager::getRecordSet($iSQL);
				if(!empty($RS)) {
					return ERROR;
				}
			}

			/** 출력순서 업데이트 */
			$iSQL = "UPDATE  `NT_POINT`.`NPL_LEVEL` 
					SET 
						`SLL_ORDER_NUM` = `SLL_ORDER_NUM`+1
					WHERE `SLL_ORDER_NUM` IN
						(
							SELECT `SLL_ORDER_NUM` FROM 
							(
								SELECT
									@rownum:=@rownum+1 AS IDX,
									`SLL_ORDER_NUM`,
									`SLL_ORDER_NUM` - @rownum AS GRP
								FROM `NT_POINT`.`NPL_LEVEL`, (SELECT @rownum:=0) IDX
								WHERE `DEL_YN` = 'N' 
								AND `SLL_ORDER_NUM` >= '".addslashes($ARR['SLL_ORDER_NUM'])."' ORDER BY SLL_ORDER_NUM ASC 
								) AS CMNT WHERE GRP = '".addslashes($ARR['SLL_ORDER_NUM'])."'-1
						)
					;"
			;

			$Result = DBManager::execQuery($iSQL);

			/** insert*/
			if($Result){
				
				$iSQL = "INSERT INTO `NT_POINT`.`NPL_LEVEL`
						(
							`SLL_ID`,
		 					`SLL_NM`,
		 					`SLL_ORDER_NUM`,
		 					`SLL_BILL_RATE`,
		 					`SLL_DISCOUNT_RATE`,
		 					`SLL_BUY_RATE`,
		 					`SLL_MEMO`,
		 					`WRT_DTHMS`,
		 					`LAST_DTHMS`,
		 					`WRT_USERID`,
		 					`DEL_YN`
						)
						VALUES
						(
							'".addslashes($ARR['SLL_ID'])."',
		 					'".addslashes($ARR['SLL_NM'])."',
		 					'".addslashes($ARR['SLL_ORDER_NUM'])."',
		 					'".addslashes($ARR['SLL_BILL_RATE'])."',
		 					'".addslashes($ARR['SLL_DISCOUNT_RATE'])."',
		 					'".addslashes($ARR['SLL_BUY_RATE'])."',
		 					'".addslashes($ARR['SLL_MEMO'])."',
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
							`SLL_ORDER_NUM`
						FROM `NT_POINT`.`NPL_LEVEL`
						WHERE SLL_ORDER_NUM = '".addslashes($ARR['SLL_ORDER_NUM'])."' AND
							`SLL_ID` != '".addslashes($ARR['SLL_ID'])."' AND
							DEL_YN = 'N'
						;"
				;

				$RS= DBManager::getRecordSet($iSQL);
				if(!empty($RS)) {
					return ERROR;
				}
			 }
			
			
			$Result = true;
				
			if($ARR['SLL_ORDER_NUM'] != $ARR['ORI_SLL_ORDER_NUM'] ){
				$iSQL = "UPDATE  `NT_POINT`.`NPL_LEVEL` 
						SET 
							`SLL_ORDER_NUM` = `SLL_ORDER_NUM`+1
						WHERE `SLL_ORDER_NUM` IN
							(
								SELECT `SLL_ORDER_NUM` FROM 
								(
									SELECT
										@rownum:=@rownum+1 AS IDX,
										`SLL_ORDER_NUM`,
										`SLL_ORDER_NUM` - @rownum AS GRP
									FROM `NT_POINT`.`NPL_LEVEL`, (SELECT @rownum:=0) IDX
									WHERE `DEL_YN` = 'N' 
									AND `SLL_ORDER_NUM` >= '".addslashes($ARR['SLL_ORDER_NUM'])."' ORDER BY SLL_ORDER_NUM ASC 
									) AS CMNT WHERE GRP = '".addslashes($ARR['SLL_ORDER_NUM'])."'-1
							)
						;"
				;
				$Result = DBManager::execQuery($iSQL);
			}

			if($Result){
				$iSQL = "UPDATE  `NT_POINT`.`NPL_LEVEL`
						 SET 
						 	`SLL_NM` = '".addslashes($ARR['SLL_NM'])."',
		 					`SLL_ORDER_NUM`= '".addslashes($ARR['SLL_ORDER_NUM'])."',
		 					`SLL_BILL_RATE` = '".addslashes($ARR['SLL_BILL_RATE'])."',
		 					`SLL_DISCOUNT_RATE` = '".addslashes($ARR['SLL_DISCOUNT_RATE'])."',
		 					`SLL_BUY_RATE` = '".addslashes($ARR['SLL_BUY_RATE'])."',
		 					`SLL_MEMO` = '".addslashes($ARR['SLL_MEMO'])."',
		 					`LAST_DTHMS` = now(),
		 					`WRT_USERID` = '".addslashes($_SESSION['USERID'])."'
						WHERE `SLL_ID`='".addslashes($ARR['SLL_ID'])."' 
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
	 * @param {ALLM_ID : 삭제할 해당 데이터 ID}
	 */
	function delData($ARR) {
		$iSQL = "UPDATE `NT_POINT`.`NPL_LEVEL`
					SET  
						`LAST_DTHMS`= NOW(), 
						`DEL_YN`	='Y'
					WHERE `SLL_ID`='".addslashes($ARR['SLL_ID'])."'
					;"
		;

		return DBManager::execQuery($iSQL);
	}


}
	// 0. 객체 생성
	$clsGradeMngt = new clsGradeMngt(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$clsGradeMngt->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
