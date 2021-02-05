<?php
class cRegion extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	// Class Object Constructor
	function cRegion($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
		$this->DATA_SAVE_PATH = $DATA_SAVE_PATH;
		$this->ALLOW_FILE_EXT = $ALLOW_FILE_EXT;
	}

	/*
	==========================================================================================
	 Procedure Name			: getData($ARR)
	 Procedure Description	: 정보가져오기
	 Logic Description		:
	 Result Type			: RecordSet
	==========================================================================================
	 ## Parameter Description
	------------------------------------------------------------------------------------------
	========================================================================================== */
	function getData($ARR) {
		if(!get_magic_quotes_gpc()) { // PHP 5.4 ~
			$CSD_ID = addslashes($ARR['CSD_ID']);
		}
		else {
			$CSD_ID = $ARR['CSD_ID'];
		}

		$iSQL = "SELECT 
					`CSD_ID`,
					`CSD_NM`,
					`CSD_ORDER_NUM`
				FROM `NT_CODE`.`NCS_DIVISION`
				WHERE `CSD_ID` = '".$CSD_ID."';
				";
		
		return json_encode(DBManager::getRecordSet($iSQL));
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
	function setData($ARR) {
		/* 검색조건 항목조합 처리 */
		$tmpCRP_2 = "	AND `CRR_2_ID` = '".addslashes($ARR['CRR_2_ID'])."' ";
		$tmpCRP_3 = "	AND `CRR_3_ID` = '".addslashes($ARR['CRR_3_ID'])."' ";
		$subWehre = "";
		if($ARR['CRR_2_ALL'] ==  'Y' ) {
			$subWehre = "";
		} else if($ARR['CRR_3_ALL'] ==  'Y' ) {
			$subWhere = $tmpCRP_2 ;
		} else {
			$subWhere = $tmpCRP_2.$tmpCRP_3 ; 
		}

		$iSQL = "	UPDATE `NT_CODE`.`NCR_REGION`
					SET 
						`CSD_ID` = '".addslashes($ARR['SELECT_DIVISION'])."',
						`LAST_DTHMS` = NOW(),
						`WRT_USERID` ='".addslashes($_SESSION['USERID'])."'
														
					WHERE 
						`CRR_1_ID` = '".addslashes($ARR['CRR_1_ID'])."' 
					$subWhere
					;
				";

		// echo $iSQL;
		return DBManager::execQuery($iSQL);
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
		$iSQL = "UPDATE `NT_CODE`.`NCR_REGION`
					SET  
						`LAST_DTHMS`= NOW(), 
						`DEL_YN`	='Y',
						`WRT_USERID` ='".addslashes($_SESSION['USERID'])."'
					WHERE 
						`CRR_1_ID` = '".addslashes($ARR['CRR_1_ID'])."' 
					AND `CRR_2_ID` = '".addslashes($ARR['CRR_2_ID'])."' 
					AND `CRR_3_ID` = '".addslashes($ARR['CRR_2_ID'])."';
					";
		// echo $iSQL;
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

		/* 검색조건 항목조합 처리 */
		if($ARR['DIVISION'] !=  '' || $ARR['DIVISION'] != null) {
			$tmpCSD_ID = "	AND NR.CSD_ID = '".$ARR['DIVISION']."' ";
		} else { $tmpCSD_ID = ""; }
		
		if($ARR['STR_AREA'] !=  '' || $ARR['STR_AREA'] != null) {
			$subArea = "AND INSTR(NR.CRR_NM, RTRIM('".addslashes($ARR['STR_AREA'])."')) >0 ";
		} else {$subArea = ""; }

		
		/* PAGEING 처리를 위한 사전쿼리 */
		$iSQL = "
			SELECT
				COUNT(*) AS CNT
			FROM
				`NT_CODE`.`NCR_REGION` NR
			WHERE	NR.`DEL_YN` = 'N'
				$tmpCSD_ID
				$subArea
			;
		";
		// echo $iSQL;
		$RS_T	= DBManager::getRecordSet($iSQL);
		$TotalRecord	= $RS_T['CNT'];
		unset($RS_T);
		


		// /* Page Limit */
		$LimitStart = ($ARR['CURR_PAGE']-1) * $ARR['PER_PAGE'];
		$LimitEnd   = $ARR['PER_PAGE'];

		/** 전체리스트가 아닌경우에만 limitSql 적용 */
		$LIMIT_SQL = "";
		if($ARR['REQ_MODE'] == CASE_LIST){
			$LIMIT_SQL = "LIMIT $LimitStart,$LimitEnd";
		}

		// 2. make query
		$iSQL = "
			SELECT 
				NR.`CRR_1_ID`,
				NR.`CRR_2_ID`,
				NR.`CRR_3_ID`,
				IFNULL(NR.`CSD_ID`, '') AS `CSD_ID`,
				IFNULL(ND.`CSD_NM`, '') AS `CSD_NM`,
				NR.`CRR_1_NM`,
				NR.`CRR_2_NM`,
				NR.`CRR_3_NM`,
				NR.`CRR_NM`,
				NR.`CRR_APPLY_DT`,
				NR.`CRR_EXPIRE_DT`,
				NR.`DEL_YN`,
				`NT_MAIN`.`GET_GARAGE_CNT`(NR.`CRR_1_ID`,NR.`CRR_2_ID`, NR.`CRR_3_ID`) AS 'CO_CNT',
				NR.`WRT_DTHMS`,
				NR.`LAST_DTHMS`,
				NR.`WRT_USERID`
			FROM 
				`NT_CODE`.`NCR_REGION` NR
			LEFT OUTER JOIN 
				`NT_CODE`.`NCS_DIVISION` ND 
				ON ND.CSD_ID = NR.CSD_ID
			WHERE	
				NR.`DEL_YN` = 'N'
				$tmpCSD_ID
				$subArea
			ORDER BY
				NR.`CRR_1_ID`, NR.`CRR_2_ID`, NR.`CRR_3_ID`
			$LIMIT_SQL 
			;
		";
		// echo $iSQL;
		// 3. execute query
		try{
			$Result = DBManager::getResult($iSQL);

			$i = $TotalRecord - ( ceil($ARR['PerPage'] * ($ARR['CurPAGE']-1) ) ) + 1;
		} catch (Exception $e){
			echo $e;
			return false;
		}

		// 4. fetchResult
		//print_r($Result);
		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			$i--;	// 글번호 처리
			array_push($rtnArray,
				array(
					"CRR_1_ID"=>$RS['CRR_1_ID'],
					"CRR_2_ID"=>$RS['CRR_2_ID'],
					"CRR_3_ID"=>$RS['CRR_3_ID'],
					"CSD_ID"=>$RS['CSD_ID'],
					"CSD_NM"=>$RS['CSD_NM'],
					"CRR_1_NM"=>$RS['CRR_1_NM'],
					"CRR_2_NM"=>$RS['CRR_2_NM'],
					"CRR_3_NM"=>$RS['CRR_3_NM'],
					"CO_CNT"=>$RS['CO_CNT'],
					"CRR_NM"=>$RS['CRR_NM'],
					"CRR_APPLY_DT"=>$RS['CRR_APPLY_DT'],
					"CRR_EXPIRE_DT"=>$RS['CRR_EXPIRE_DT'],
					"DEL_YN"=>$RS['DEL_YN'],
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
	$cRegion = new cRegion(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$cRegion->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
