<?php

class cQna extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	// Class Object Constructor
	function cQna($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
		$this->DATA_SAVE_PATH = $DATA_SAVE_PATH;
		$this->ALLOW_FILE_EXT = $ALLOW_FILE_EXT;
	}

	/**
	 * 전체데이터 가져오는 함수
	*/
	function getTotalList(){
		$iSQL = "SELECT 
					@rownum:=@rownum+1 AS IDX,
					`NMV_CTGY`.`MVC_ID`,
					`NMV_CTGY`.`MVC_NM`,
					`NMV_CTGY`.`MVC_ORDER_NUM`,
					`NMV_CTGY`.`WRT_DTHMS`,
					`NMV_CTGY`.`LAST_DTHMS`,
					`NMV_CTGY`.`WRT_USERID`
				FROM `NT_COMMUNITY`.`NMV_CTGY`, (SELECT @rownum:=0) IDX
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
			"MVC_ID"=>$RS['MVC_ID'],
			"MVC_NM"=>$RS['MVC_NM'],
			"MVC_ORDER_NUM"=>$RS['MVC_ORDER_NUM'],
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
					`NT_COMMUNITY`.`NMV_CTGY` 
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
					`CVC`.`MVC_ID`,
					`CVC`.`MVC_NM`,
					`CVC`.`MVC_ORDER_NUM`,
					`CVC`.`WRT_DTHMS`,
					`CVC`.`LAST_DTHMS`,
					`CVC`.`WRT_USERID`
				FROM `NT_COMMUNITY`.`NMV_CTGY` `CVC`, (SELECT @rownum:=0) IDX
				WHERE DEL_YN = 'N'
				ORDER BY `CVC`.`MVC_ORDER_NUM` ASC 
				LIMIT $LimitStart,$LimitEnd;"
		;
	
		$Result= DBManager::getResult($iSQL);
		///

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
				"IDX"=>$IDX--,
				"MVC_ID"=>$RS['MVC_ID'],
				"MVC_NM"=>$RS['MVC_NM'],
				"MVC_ORDER_NUM"=>$RS['MVC_ORDER_NUM'],
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
	 * @param {MVC_ID : 조회할 해당 데이터 ID}
	 */
	function getData($ARR) {
		$iSQL = "SELECT 
					`NMV_CTGY`.`MVC_ID`,
					`NMV_CTGY`.`MVC_NM`,
					`NMV_CTGY`.`MVC_ORDER_NUM`,
				FROM `NT_COMMUNITY`.`NMV_CTGY`
				WHERE DEL_YN = 'N'
				AND MVC_ID = '".addslashes($ARR['MVC_ID'])."'
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
							`MVC_ID`
						FROM `NT_COMMUNITY`.`NMV_CTGY`
						WHERE MVC_ID = '".addslashes($ARR['MVC_ID'])."'
						;"
				;
			$RS= DBManager::getRecordSet($iSQL);
			if(!empty($RS)) {
				return CONFLICT;
			}

			if($ARR['IS_EXIST_ORDER_NUM']==''){
				/** 입력요청한 출력순서가 존재하는지 */
				$iSQL = "SELECT 
							`MVC_ORDER_NUM`
						FROM `NT_COMMUNITY`.`NMV_CTGY`
						WHERE MVC_ORDER_NUM = '".addslashes($ARR['MVC_ORDER_NUM'])."' AND
							DEL_YN = 'N'
						;"
				;

				$RS= DBManager::getRecordSet($iSQL);
				if(!empty($RS)) {
					return ERROR;
				}
			}

			/** 출력순서 업데이트 */
			$iSQL = "UPDATE  `NT_COMMUNITY`.`NMV_CTGY` 
					SET 
						`MVC_ORDER_NUM` = `MVC_ORDER_NUM`+1
					WHERE `MVC_ORDER_NUM` IN
						(
							SELECT `MVC_ORDER_NUM` FROM 
							(
								SELECT
									@rownum:=@rownum+1 AS IDX,
									`MVC_ORDER_NUM`,
									`MVC_ORDER_NUM` - @rownum AS GRP
								FROM `NT_COMMUNITY`.`NMV_CTGY`, (SELECT @rownum:=0) IDX
								WHERE `DEL_YN` = 'N' 
								AND `MVC_ORDER_NUM` >= '".addslashes($ARR['MVC_ORDER_NUM'])."' ORDER BY MVC_ORDER_NUM ASC 
								) AS CMNT WHERE GRP = '".addslashes($ARR['MVC_ORDER_NUM'])."'-1
						)
					;"
			;

			$Result = DBManager::execQuery($iSQL);

			/** insert*/
			if($Result){
				$iSQL = "INSERT INTO `NT_COMMUNITY`.`NMV_CTGY`
						(
							`MVC_ID`,
							`MVC_NM`,
							`MVC_ORDER_NUM`,
							`WRT_DTHMS`,
							`LAST_DTHMS`,
							`WRT_USERID`,
							`DEL_YN`
						)
						VALUES
						(
							'".addslashes($ARR['MVC_ID'])."',
							'".addslashes($ARR['MVC_NM'])."',
							'".addslashes($ARR['MVC_ORDER_NUM'])."',
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
							`MVC_ORDER_NUM`
						FROM `NT_COMMUNITY`.`NMV_CTGY`
						WHERE MVC_ORDER_NUM = '".addslashes($ARR['MVC_ORDER_NUM'])."' AND
							`MVC_ID` != '".addslashes($ARR['MVC_ID'])."' AND
							DEL_YN = 'N'
						;"
				;

				$RS= DBManager::getRecordSet($iSQL);
				if(!empty($RS)) {
					return ERROR;
				}
			 }
			
			
			$Result = true;
				
			if($ARR['MVC_ORDER_NUM'] != $ARR['ORI_MVC_ORDER_NUM'] ){
				$iSQL = "UPDATE  `NT_COMMUNITY`.`NMV_CTGY` 
						SET 
							`MVC_ORDER_NUM` = `MVC_ORDER_NUM`+1
						WHERE `MVC_ORDER_NUM` IN
							(
								SELECT `MVC_ORDER_NUM` FROM 
								(
									SELECT
										@rownum:=@rownum+1 AS IDX,
										`MVC_ORDER_NUM`,
										`MVC_ORDER_NUM` - @rownum AS GRP
									FROM `NT_COMMUNITY`.`NMV_CTGY`, (SELECT @rownum:=0) IDX
									WHERE `DEL_YN` = 'N' 
									AND `MVC_ORDER_NUM` >= '".addslashes($ARR['MVC_ORDER_NUM'])."' ORDER BY MVC_ORDER_NUM ASC 
									) AS CMNT WHERE GRP = '".addslashes($ARR['MVC_ORDER_NUM'])."'-1
							)
						;"
				;
				$Result = DBManager::execQuery($iSQL);
			}

			if($Result){
				$iSQL = "UPDATE  `NT_COMMUNITY`.`NMV_CTGY`
						 SET 
							MVC_NM = '".addslashes($ARR['MVC_NM'])."',
							MVC_ID = '".addslashes($ARR['MVC_ID'])."',
							MVC_ORDER_NUM = '".addslashes($ARR['MVC_ORDER_NUM'])."',
							LAST_DTHMS = now()
						 WHERE MVC_ID='".addslashes($ARR['MVC_ID'])."' 
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
	 * @param {MVC_ID : 삭제할 해당 데이터 ID}
	 */
	function delData($ARR) {
		$iSQL = "UPDATE `NT_COMMUNITY`.`NMV_CTGY`
					SET  
						`LAST_DTHMS`= NOW(), 
						`DEL_YN`	='Y'
					WHERE `MVC_ID`='".addslashes($ARR['MVC_ID'])."'
					;"
		;

		return DBManager::execQuery($iSQL);
	}


}
	// 0. 객체 생성
	$cQna = new cQna(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$cQna->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
