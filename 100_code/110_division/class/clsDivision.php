<?php
class cDivision extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	// Class Object Constructor
	function cDivision($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
		$this->DATA_SAVE_PATH = $DATA_SAVE_PATH;
		$this->ALLOW_FILE_EXT = $ALLOW_FILE_EXT;
	}

	

	/*
	----------------------------------------------------------------------------------------------
	 Procedure Name			: setData($ARR,$PDS)
	 Procedure Description	:  저장
	 Logic Description		:
	 Result Type			: BOOLEAN
	------------------------------------------------------------------------------------------
							USAGE
	------------------------------------------------------------------------------------------
	----------------------------------------------------------------------------------------------*/
	// function setData($ARR,$PDS) {

	// 		$iSQL = "CALL `NT_CODE`.`NCS_DIVISION`
	// 						('".addslashes($ARR['CSD_ID'])."',
	// 						 '".addslashes($ARR['CSD_NM'])."', 
	// 						 ".addslashes($ARR['CSD_ORDER_NUM']).", 
	// 						 '".addslashes($_SESSION['USERID'])."', 
	// 						 'N', 
	// 						 '".addslashes($ARR['REQ_MODE'])."');
	// 				";
					
	// 		// echo $iSQL ; 

	// return DBManager::execQuery($iSQL);

	// }


	/**
	 * 데이터생성
	 */
	function setData($ARR) {
		
		if($ARR['REQ_MODE'] == CASE_CREATE){
			/** 코드 존재여부
			 * 등록일 경우에는 전체에서 존재하는지
			 */
			$iSQL = "SELECT 
							`CSD_ID`
						FROM `NT_CODE`.`NCS_DIVISION`
						WHERE CSD_ID = '".addslashes($ARR['CSD_ID'])."'
						;"
				;
			$RS= DBManager::getRecordSet($iSQL);
			if(!empty($RS)) {
				return CONFLICT;
			}
			if($ARR['IS_EXIST_ORDER_NUM']==''){
				/** 입력요청한 출력순서가 존재하는지 */
				$iSQL = "SELECT 
							`CSD_ORDER_NUM`
						FROM `NT_CODE`.`NCS_DIVISION`
						WHERE CSD_ORDER_NUM = '".addslashes($ARR['CSD_ORDER_NUM'])."' AND
							DEL_YN = 'N'
						;"
				;
				$RS= DBManager::getRecordSet($iSQL);
				if(!empty($RS)) {
					return ERROR;
				}
			}
			/** 출력순서 업데이트 */
			$iSQL = "UPDATE  `NT_CODE`.`NCS_DIVISION` 
					SET 
						`CSD_ORDER_NUM` = `CSD_ORDER_NUM`+1
					WHERE `CSD_ORDER_NUM` IN
						(
							SELECT `CSD_ORDER_NUM` FROM 
							(
								SELECT
									@rownum:=@rownum+1 AS IDX,
									`CSD_ORDER_NUM`,
									`CSD_ORDER_NUM` - @rownum AS GRP
								FROM `NT_CODE`.`NCS_DIVISION`, (SELECT @rownum:=0) IDX
								WHERE `DEL_YN` = 'N' 
								AND `CSD_ORDER_NUM` >= '".addslashes($ARR['CSD_ORDER_NUM'])."' ORDER BY CSD_ORDER_NUM ASC 
								) AS CMNT WHERE GRP = '".addslashes($ARR['CSD_ORDER_NUM'])."'-1
						)
					;"
			;
			$Result = DBManager::execQuery($iSQL);
			/** insert*/
			if($Result){
				$iSQL = "INSERT INTO `NT_CODE`.`NCS_DIVISION`
						(
							`CSD_ID`,
							`CSD_NM`,
							`CSD_ORDER_NUM`,
							`WRT_DTHMS`,
							`LAST_DTHMS`,
							`WRT_USERID`,
							`DEL_YN`
						)
						VALUES
						(
							'".addslashes($ARR['CSD_ID'])."',
							'".addslashes($ARR['CSD_NM'])."',
							'".addslashes($ARR['CSD_ORDER_NUM'])."',
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
			/** 코드 존재여부
			 * 수정 경우에는 전체에서 본인제외하고 동일한게 있는지
			 */
			if($ARR['IS_EXIST_ORDER_NUM']==''){
				/** 입력요청한 출력순서가 존재하는지 */
				$iSQL = "SELECT 
							`CSD_ORDER_NUM`
						FROM `NT_CODE`.`NCS_DIVISION`
						WHERE CSD_ORDER_NUM = '".addslashes($ARR['CSD_ORDER_NUM'])."' AND
							`CSD_ID` != '".addslashes($ARR['CSD_ID'])."' AND
							DEL_YN = 'N'
						;"
				;
				// echo $iSQL ; 
				$RS= DBManager::getRecordSet($iSQL);
				if(!empty($RS)) {
					return ERROR;
				}
			 }
			
			
			$Result = true;
				
			if($ARR['CSD_ORDER_NUM'] != $ARR['ORI_CSD_ORDER_NUM'] ){
				$iSQL = "UPDATE  `NT_CODE`.`NCS_DIVISION` 
						SET 
							`CSD_ORDER_NUM` = `CSD_ORDER_NUM`+1
						WHERE `CSD_ORDER_NUM` IN
							(
								SELECT `CSD_ORDER_NUM` FROM 
								(
									SELECT
										@rownum:=@rownum+1 AS IDX,
										`CSD_ORDER_NUM`,
										`CSD_ORDER_NUM` - @rownum AS GRP
									FROM `NT_CODE`.`NCS_DIVISION`, (SELECT @rownum:=0) IDX
									WHERE `DEL_YN` = 'N' 
									AND `CSD_ORDER_NUM` >= '".addslashes($ARR['CSD_ORDER_NUM'])."' ORDER BY CSD_ORDER_NUM ASC 
									) AS CMNT WHERE GRP = '".addslashes($ARR['CSD_ORDER_NUM'])."'-1
							)
						;"
				;
				$Result = DBManager::execQuery($iSQL);
			}
			if($Result){
				$iSQL = "UPDATE  `NT_CODE`.`NCS_DIVISION`
						 SET 
							CSD_NM = '".addslashes($ARR['CSD_NM'])."',
							CSD_ID = '".addslashes($ARR['CSD_ID'])."',
							CSD_ORDER_NUM = '".addslashes($ARR['CSD_ORDER_NUM'])."',
							LAST_DTHMS = now()
						 WHERE CSD_ID='".addslashes($ARR['CSD_ID'])."' 
						;"
				;
				return DBManager::execQuery($iSQL);
			}else{
				return SERVER_ERROR;
			}
		
		}	
	}


	/*
	------------------------------------------------------------------------------------------
	 Procedure Name			: delDATA($ARR)
	 Procedure Description	: 정보삭제
	 Logic Description		:
	 Result Type			: Boolean
	------------------------------------------------------------------------------------------
	            USAGE
	------------------------------------------------------------------------------------------
	  $ME_ID					: ME_ID PK
	------------------------------------------------------------------------------------------*/
	function delData($ARR) {
		$iSQL = "UPDATE `NT_CODE`.`NCS_DIVISION`
					SET  
						`LAST_DTHMS`= NOW(), 
						`DEL_YN`	='Y'
					WHERE `CSD_ID`='".addslashes($ARR['CSD_ID'])."'
					;
				";

		return DBManager::execQuery($iSQL);
	}

	

	/*
	----------------------------------------------------------------------------------------------
	 Procedure Name			: getList($ARR)
	 Procedure Description	:  리스트 반환
	 Logic Description		:
	 Result Type			: String
	------------------------------------------------------------------------------------------
	            USAGE
	------------------------------------------------------------------------------------------
	----------------------------------------------------------------------------------------------*/
	function getList ($ARR) {

		/* PAGEING 처리를 위한 사전쿼리 */
		$iSQL = "
			SELECT
				COUNT(*) AS CNT
			FROM
				`NT_CODE`.`NCS_DIVISION` 
			WHERE	`DEL_YN` = 'N'
			;";

		$RS_T	= DBManager::getRecordSet($iSQL);
		$TotalRecord	= $RS_T['CNT'];
		unset($RS_T);
		// echo $iSQL."\n";


		// /* Page Limit */
		$LimitStart = ($ARR['CurPAGE']-1) * $ARR['PerPage'];
		$LimitEnd   = $ARR['PerPage'];

		/** 전체리스트가 아닌경우에만 limitSql 적용 */
		$LIMIT_SQL = "";
		if($ARR['REQ_MODE'] == CASE_LIST){
			$LIMIT_SQL = "LIMIT $LimitStart,$LimitEnd";
		}

		// 2. make query
		$iSQL = "
			SELECT 	
				`CSD_ID`,
				`CSD_NM`,
				`CSD_ORDER_NUM`,
				`WRT_DTHMS`,
				`LAST_DTHMS`,
				`WRT_USERID`
			FROM
				`NT_CODE`.`NCS_DIVISION` 
				WHERE	`DEL_YN` = 'N'

	 		ORDER BY
						`CSD_ORDER_NUM` 
			$LIMIT_SQL 
			;
		";
		// echo $iSQL."\n";
		// 3. execute query
		try{
			$Result = DBManager::getResult($iSQL);

			$i = $TotalRecord - ( ceil($ARR['PerPage'] * ($ARR['CurPAGE']-1) ) ) + 1;
		} catch (Exception $e){
			echo $e;
			return -1;
		}

		// 4. fetchResult
		//print_r($Result);
		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			$i--;	// 글번호 처리
			array_push($rtnArray,
				array(
					"CSD_ID"=>$RS['CSD_ID'],
					"CSD_NM"=>$RS['CSD_NM'],
					"CSD_ORDER_NUM"=>$RS['CSD_ORDER_NUM'],
					"WRT_DTHMS"=>$RS['WRT_DTHMS'],
					"LAST_DTHMS"=>$RS['LAST_DTHMS'],
					"WRT_USERID"=>$RS['WRT_USERID'],
					"CSD_IDX"=>$i,
				));
		}

		$Result->close();		
		unset($RS);
	
		// 5. return
		if(empty($rtnArray)) {
			return HAVE_NO_DATA;
		} else {
			$jsonArray = array();
			array_push($jsonArray,$rtnArray);
			array_push($jsonArray,$TotalRecord);

			return  $jsonArray;

		}
	}


}
	// 0. 객체 생성
	$cDivision = new cDivision(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$cDivision->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
