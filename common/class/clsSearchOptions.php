<?php

class cSearchOptions extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	// Class Object Constructor
	function cSearchOptions($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
		$this->DATA_SAVE_PATH = $DATA_SAVE_PATH;
		$this->ALLOW_FILE_EXT = $ALLOW_FILE_EXT;
	}

	
	/*
		@name	getOptionListDivision
		@param	{Array} $ARR 
		@return	String
		@description	<SELECT>개체를 위한 동적 <OPTION> 반환을 위한 메소드
						구분 OPTION 반환
		@author	이주연
	*/
	function getOptionListDivision ($ARR) {

		$iSQL = "SELECT 
					`CSD_ID`, 
					`CSD_NM` 
				FROM 
					`NT_CODE`.`NCS_DIVISION`
				WHERE DEL_YN='N'
				ORDER BY
					`CSD_ORDER_NUM` 
				;
		";

		try{
			$Result = DBManager::getResult($iSQL);
		} catch (Exception $e){
			return ERROR_DB_SELECT_DATA;
		}

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array("CSD_ID"=>$RS['CSD_ID'], "CSD_NM"=>$RS['CSD_NM']));
		}
		$Result->close();		// 자원 반납
		unset($RS);

		$rtnJSON = json_encode($rtnArray);
		return $rtnJSON;
	}


	/*
		@name	getOptionArrArea
		@param	{Array} $ARR 
				{String} DIVISION 구분
		@return	String
		@description	<SELECT>개체를 위한 동적 <OPTION> 반환을 위한 메소드
						지역 자동완성 Arr반환
		@author	이주연
	*/
	function getOptionArrArea ($ARR) {

		if($ARR['DIVISION'] != '' || $ARR['DIVISION'] != null){
			$subWhere = " AND NR.`CSD_ID`= '".addslashes($ARR['DIVISION'])."' \n" ;
		} else {
			$subWhere = "";
		}
		if($ARR['KEYWORD'] != '' || $ARR['KEYWORD'] != null){
			$subWhere = " AND INSTR(CRR_NM, '".addslashes($ARR['KEYWORD'])."') > 0 \n";
		}

		$iSQL = "SELECT
					RTRIM(NR.CRR_NM) AS 'CRR_NM'
				FROM `NT_CODE`.`NCR_REGION` NR
				WHERE
					NR.`DEL_YN` = 'N' 
					$subWhere
				;
		";
		// echo $iSQL;
		try{
			$Result = DBManager::getResult($iSQL);
		} catch (Exception $e){
			return ERROR_DB_SELECT_DATA;
		}

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				$RS['CRR_NM']);
		}
		$Result->close();		// 자원 반납
		unset($RS);

		return json_encode($rtnArray);

	}

	/*
		@name	getOptionListIw
		@param	{Array} $ARR 
		@return	String
		@description	<SELECT>개체를 위한 동적 <OPTION> 반환을 위한 메소드
						보험사 OPTION 반환
		@author	이주연
	*/
	function getOptionListIw ($ARR) {

		$iSQL = "SELECT 
					NI.`MII_ID`,
					NI.`MII_NM`
				FROM 
					`NT_MAIN`.`NMI_INSU` AS NI
				WHERE 
					NI.`DEL_YN` = 'N'
				ORDER BY 
					NI.`MII_ORDER_NUM`
				;
		";

		try{
			$Result = DBManager::getResult($iSQL);
		} catch (Exception $e){
			return ERROR_DB_SELECT_DATA;
		}

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"MII_ID"=>$RS['MII_ID'], 
					"MII_NM"=>$RS['MII_NM']
				));
		}
		$Result->close();		// 자원 반납
		unset($RS);

		$rtnJSON = json_encode($rtnArray);
		return $rtnJSON;
	}




	/*
		@name	getOptionListIw
		@param	{Array} $ARR 
		@return	String
		@description	<SELECT>개체를 위한 동적 <OPTION> 반환을 위한 메소드
						보험사 OPTION 반환
		@author	이주연
	*/
	function getOptionListGw ($ARR) {

		$iSQL = "SELECT 
					NG.`MGG_ID`,
					NG.`MGG_NM`
				FROM 
					`NT_MAIN`.`NMG_GARAGE` AS NG
				WHERE 
					NG.`DEL_YN` = 'N'
				ORDER BY 
					NG.`MGG_NM`
				;
		";

		try{
			$Result = DBManager::getResult($iSQL);
		} catch (Exception $e){
			return ERROR_DB_SELECT_DATA;
		}

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"MGG_ID"=>$RS['MGG_ID'], 
					"MGG_NM"=>$RS['MGG_NM']
				));
		}
		$Result->close();		// 자원 반납
		unset($RS);

		$rtnJSON = json_encode($rtnArray);
		return $rtnJSON;
	}


	/** 지역구분 */
	function getAreaOption($ARR){
		$SUB_SQL = "";

		if(is_array($ARR['CSD_ID'])){
			$SUB_SQL .= "AND(";

			foreach ($ARR['CSD_ID'] as $index => $ID) {
				if($index == 0){
					$SUB_SQL .=  "`CSD_ID`='".$ID."'";
				}else{
					$SUB_SQL .=  " OR `CSD_ID`='".$ID."'";
				}
			}

			$SUB_SQL .= ")";
			
			if($ARR['IS_TOTAL'] == ''){
				$SUB_SQL .=  "AND `CRR_3_ID`='".NOT_CRR_3."'";
			}
		}else{
			if($ARR['CSD_ID'] != ''){
				$SUB_SQL .=  "AND `CSD_ID`='".addslashes($ARR['CSD_ID'])."'";
				if($ARR['IS_TOTAL'] == ''){
					$SUB_SQL .=  "AND `CRR_3_ID`='".NOT_CRR_3."'";
				}
			}else{
				if($ARR['IS_TOTAL'] == ''){
					$SUB_SQL .=  "AND `CRR_3_ID`='".NOT_CRR_3."'";
				}
			}
			
		}
	
		$iSQL = "
					SELECT 
						`CRR_1_ID`,
						`CRR_2_ID`,
						`CRR_3_ID`,
						`CRR_NM`
					FROM 
						`NT_CODE`.`NCR_REGION`
					WHERE DEL_YN = 'N'
					$SUB_SQL 
				;"
		;
		// echo 'getAreaOption SQL>>'.$iSQL;
		$Result = DBManager::getResult($iSQL);

		if(empty($Result)){
			unset($RS);
			return HAVE_NO_DATA;
		}else{
			$rtnArray = array();
			while($RS = $Result->fetch_array()){
				array_push($rtnArray,
				array(
					"CRR_1_ID"=>$RS['CRR_1_ID'],
					"CRR_2_ID"=>$RS['CRR_2_ID'],
					"CRR_3_ID"=>$RS['CRR_3_ID'],
					"CRR_NM"=>$RS['CRR_NM'],
				));
			}
			$Result->close();		// 자원 반납
			unset($RS);
	
			return $rtnArray;
		}
		

	}



	/** 부품타입 가져오기 */
	function getPartsType(){
		$iSQL = "SELECT 
					CPP_ID,
					CPP_NM,
					CPP_NM_SHORT
				FROM NT_CODE.NCP_PART
				WHERE DEL_YN = 'N'
				AND CPP_USE_YN = 'Y'
				ORDER BY CPP_ORDER_NUM;
		";
		$Result= DBManager::getResult($iSQL);
		///

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
				"CPP_ID"=>$RS['CPP_ID'],
				"CPP_NM"=>$RS['CPP_NM'],
				"CPP_NM_SHOR"=>$RS['CPP_NM_SHOR']
			));
		}
		$Result->close();  // 자원 반납
		unset($RS);

		if(empty($rtnArray)) {
			return HAVE_NO_DATA;
		} else {
			return json_encode($rtnArray);
		}
	}

	/** 취급부품 가져오기 */
	function getCpps(){
		$iSQL = "SELECT 
					CPPS_ID,
					CPPS_NM,
					CPPS_NM_SHORT,
					CPPS_RATIO
				FROM NT_CODE.NCP_PART_SUB
				WHERE DEL_YN = 'N'
				ORDER BY CPPS_ORDER_NUM;
		";
		$Result= DBManager::getResult($iSQL);
		///

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
				"CPPS_ID"=>$RS['CPPS_ID'],
				"CPPS_NM"=>$RS['CPPS_NM'],
				"CPPS_RATIO"=>$RS['CPPS_RATIO'],
				"CPPS_NM_SHORT"=>$RS['CPPS_NM_SHORT']
			));
		}
		$Result->close();  // 자원 반납
		unset($RS);

		if(empty($rtnArray)) {
			return HAVE_NO_DATA;
		} else {
			return json_encode($rtnArray);
		}
	}


	/** NT_STOCK 해당년 가져오기 */
	function getYear(){
		$iSQL = "SELECT 
					NIH_Y
				FROM NT_STOCK.V_STOCK_YEAR ;
		";
		$Result= DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
				"NIH_Y"=>$RS['NIH_Y']
			));
		}
		$Result->close();  // 자원 반납
		unset($RS);

		if(empty($rtnArray)) {
			return HAVE_NO_DATA;
		} else {
			return json_encode($rtnArray);
		}
	}

	/** NT_STOCK 해당년 가져오기 */
	function getMonth(){
		$iSQL = "SELECT 
					NIH_M
				FROM NT_STOCK.V_STOCK_MONTH ;
		";
		$Result= DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
				"NIH_M"=>$RS['NIH_M']
			));
		}
		$Result->close();  // 자원 반납
		unset($RS);

		if(empty($rtnArray)) {
			return HAVE_NO_DATA;
		} else {
			return json_encode($rtnArray);
		}
	}

	/** 공업사 가져오기 */
	function getGarageInfo(){
		$iSQL = "SELECT 
					`MGG_NM`,
					`MGG_ID`
				FROM `NT_MAIN`.`NMG_GARAGE`
				WHERE `DEL_YN` = 'N'
				;"
		;
		// echo $iSQL;

		$Result= DBManager::getResult($iSQL);
		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"MGG_NM"=>$RS['MGG_NM'],
					"MGG_ID"=>$RS['MGG_ID'],
			));
		}

		$Result->close();  // 자원 반납
		unset($RS);

		if(empty($rtnArray)) {
			return HAVE_NO_DATA;
		} else {
			return json_encode($rtnArray);
		}
	}

	/** 청구 해당연도(검색시) */
	function getClaimYearOption($ARR){
		$iSQL = "
				SELECT 
					YEAR(NOW()) AS CURR_YEAR,
					YEAR(MIN(NB_CLAIM_DT))AS MIN_YEAR
				FROM NT_BILL.NBB_BILL
				WHERE DEL_YN = 'N'
				AND NB_CLAIM_DT != '0000-00-00'
				;
		";

		$RS= DBManager::getRecordSet($iSQL);	

		$rtnArray = array(
			"CURR_YEAR"=>$RS['CURR_YEAR'],
			"MIN_YEAR"=>$RS['MIN_YEAR'],			
		);
		unset($RS);
		
		return  json_encode($rtnArray);
	}

	/* 보험사Team 조회  */
	function getInsuTeam() {
		$iSQL = "SELECT 
					MII_ID,
					NT_MAIN.GET_INSU_NM(MII_ID) AS 'MII_NM',
					MIT_ID, MIT_NM
				FROM NT_MAIN.NMI_INSU_TEAM
				WHERE DEL_YN ='N';
		";

		$Result= DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"MII_ID"=>$RS['MII_ID'],
					"MII_NM"=>$RS['MII_NM'],
					"MIT_ID"=>$RS['MIT_ID'],
					"MIT_NM"=>$RS['MIT_NM']
			));
		}
		$Result->close();  // 자원 반납
		unset($RS);

		if(empty($rtnArray)) {
			return HAVE_NO_DATA;
		} else {
			return json_encode($rtnArray);
		}
	}

		/** 쇼핑몰 등급 */
		function getGrade($ARR){
			$iSQL = "SELECT 
						SLL_ID,
						SLL_NM
					FROM NT_POINT.NPL_LEVEL
					WHERE DEL_YN = 'N' ;
			";
			$Result= DBManager::getResult($iSQL);
	
			$rtnArray = array();
			while($RS = $Result->fetch_array()){
				array_push($rtnArray,
					array(
						"SLL_ID"=>$RS['SLL_ID'],
						"SLL_NM"=>$RS['SLL_NM']
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

}
	// 0. 객체 생성
	$cSearchOptions = new cSearchOptions(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$cSearchOptions->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
