<?php

class clsCarManagement extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	// Class Object Constructor
	function clsCarManagement($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
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
					`CCC_ID`,
					`CCC_CAR_TYPE_CD`,
					`CCC_MANUFACTURE_NM`,
					(SELECT RCT_NM FROM `NT_CODE`.`REF_CAR_TYPE` WHERE RCT_CD = `CCC_CAR_TYPE_CD`)AS RCT_NM,
					`CCC_CAR_NM`,
					`WRT_DTHMS`,
					`LAST_DTHMS`,
					`WRT_USERID`
				FROM `NT_CODE`.`NCC_CAR`, (SELECT @rownum:=0) IDX
				WHERE `DEL_YN`  = 'N'  
					 $tmpKeyword 
				ORDER BY CCC_ID DESC 
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
				"CPP_ID"=>$RS['CPP_ID'],
				"CPP_PART_TYPE"=>$RS['CPP_PART_TYPE'],
				"CPP_NM"=>$RS['CPP_NM'],
				"CPP_NM_SHORT"=>$RS['CPP_NM_SHORT'],
				"CPP_UNIT"=>$RS['CPP_UNIT'],
				"CPP_USE_YN"=>$RS['CPP_USE_YN'],
				"CPP_ORDER_NUM"=>$RS['CPP_ORDER_NUM'],
				"CPPS_COUNT"=>$RS['CPPS_COUNT'],
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
	 * 조회 
	 * @param {$ARR}
	 * @param {CURR_PAGE : 현재페이지, PER_PAGE : 몇개를 조회할 것인지, KEYWORD : 검색조건}
	 */
	function getList($ARR) {
		$LimitStart = ($ARR['CURR_PAGE']-1) * $ARR['PER_PAGE'];
		$LimitEnd   = $ARR['PER_PAGE'];
		$tmpKeyword = "";

		if($ARR['CCC_SEARCH_VALUE'] != '') {
			if($ARR['CCC_SEARCH_VALUE']  == 'CCC_MANUFACTURE_NM'){
				$tmpKeyword .= "AND INSTR(`CCC_MANUFACTURE_NM`,'".addslashes($ARR['CCC_SEARCH_TEXT'])."')>0 ";
			}else{
				$tmpKeyword .= "AND INSTR(`CCC_CAR_NM`,'".addslashes($ARR['CCC_SEARCH_TEXT'])."')>0 ";
			}		
		} 

		// 페이징처리를 위해 전체데이터
		$iSQL = "
				SELECT
					COUNT(*) AS CNT
				FROM
					`NT_CODE`.`NCC_CAR` 
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
					`CCC_ID`,
					`CCC_CAR_TYPE_CD`,
					`CCC_MANUFACTURE_NM`,
					(SELECT RCT_NM FROM `NT_CODE`.`REF_CAR_TYPE` WHERE RCT_CD = `CCC_CAR_TYPE_CD`)AS RCT_NM,
					`CCC_CAR_NM`,
					`WRT_DTHMS`,
					`LAST_DTHMS`,
					`WRT_USERID`
				FROM `NT_CODE`.`NCC_CAR`
				WHERE `DEL_YN`  = 'N'  
					 $tmpKeyword 
				ORDER BY CCC_ID DESC 
				$LIMIT_SQL;
				"
		;
	
		$Result= DBManager::getResult($iSQL);
		///

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
				"IDX"=>$IDX--,
				"CCC_ID"=>$RS['CCC_ID'],
				"RCT_NM"=>$RS['RCT_NM'],
				"CCC_CAR_TYPE_CD"=>$RS['CCC_CAR_TYPE_CD'],
				"CCC_MANUFACTURE_NM"=>$RS['CCC_MANUFACTURE_NM'],
				"CCC_CAR_NM"=>$RS['CCC_CAR_NM'],
				"WRT_DTHMS"=>$RS['WRT_DTHMS'],
				"LAST_DTHMS"=>$RS['LAST_DTHMS'],
				"WRT_USERID"=>$RS['WRT_USERID'],
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
	 * @param {ALLM_ID : 조회할 해당 데이터 ID}
	 */
	function getData($ARR) {
		$iSQL = "SELECT 
					`ALLM_ID`,
					`ALLM_NM`,
					`ALLM_MEMO`,
					`WRT_DTHMS`,
					`LAST_DTHMS`,
					`WRT_USERID`
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
			$SESSION_ID = $_SESSION['USERID'] == null ? 'SYSYTEM' : $_SESSION['USERID'];
			/** insert*/
			$iSQL = "INSERT INTO `NT_CODE`.`NCC_CAR`
					(
						`CCC_CAR_TYPE_CD`,
						`CCC_MANUFACTURE_NM`,
						`CCC_CAR_NM`,
						`WRT_DTHMS`,
						`LAST_DTHMS`,
						`WRT_USERID`,
						`DEL_YN`
					)
					VALUES
					(
						'".addslashes($ARR['CCC_CAR_TYPE_CD'])."',
						'".addslashes($ARR['CCC_MANUFACTURE_NM'])."',
						'".addslashes($ARR['CCC_CAR_NM'])."',
						NOW(),
						NOW(),
						'".$SESSION_ID."',
						'N'
					);"
			;
				
			

		}else{
			$iSQL = "UPDATE  `NT_CODE`.`NCC_CAR`
						SET 
						`CCC_CAR_TYPE_CD` = '".addslashes($ARR['CCC_CAR_TYPE_CD'])."',
						`CCC_MANUFACTURE_NM`= '".addslashes($ARR['CCC_MANUFACTURE_NM'])."',
						`CCC_CAR_NM` = '".addslashes($ARR['CCC_CAR_NM'])."',
						`LAST_DTHMS` = now()
					WHERE `CCC_ID`='".addslashes($ARR['CCC_ID'])."' 
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
		$iSQL = "UPDATE `NT_CODE`.`NCC_CAR`
					SET  
						`LAST_DTHMS`= NOW(), 
						`DEL_YN`	='Y'
					WHERE `CCC_ID`='".addslashes($ARR['CCC_ID'])."'
					;"
		;

		return DBManager::execQuery($iSQL);
	}

	/** 차량타입 가져오기 */
	function getCarType(){
		$iSQL = "SELECT 
					`RCT_CD`,
					`RCT_NM`
				FROM `NT_CODE`.`REF_CAR_TYPE`
				;"
		;
		$Result= DBManager::getResult($iSQL);
		///

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
				"RCT_CD"=>$RS['RCT_CD'],
				"RCT_NM"=>$RS['RCT_NM'],
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
	$clsCarManagement = new clsCarManagement(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$clsCarManagement->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
