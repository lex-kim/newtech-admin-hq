<?php
class cPointMngt extends DBManager {
	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자
	// Class Object Constructor
	function cPointMngt($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
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
		
		/** 기간검색 */
		if($ARR['DT_KEY_SEARCH'] != '') {
			if($ARR['DT_KEY_SEARCH'] == 'NPH_ISSUE_DT'){ // 변동일
                if($ARR['START_DT_SEARCH'] != '' && $ARR['END_DT_SEARCH'] != ''){ 
					$tmpKeyword .= "AND DATE_FORMAT(NPH.NPH_ISSUE_DT, '%Y-%m-%d') BETWEEN '".addslashes($ARR['START_DT_SEARCH'])."' AND '".addslashes($ARR['END_DT_SEARCH'])."' \n";
				}else if($ARR['START_DT_SEARCH'] != '' ){
					$tmpKeyword .= "AND DATE_FORMAT(NPH.NPH_ISSUE_DT, '%Y-%m-%d') >= '".$ARR['START_DT_SEARCH']."' \n";
				}else if($ARR['END_DT_SEARCH'] != ''){
					$tmpKeyword .= "AND DATE_FORMAT(NPH.NPH_ISSUE_DT, '%Y-%m-%d') <= '".$ARR['END_DT_SEARCH']."' \n";
				}
			}else if($ARR['DT_KEY_SEARCH'] == 'LAST_DTHMS'){ // 최종변동일
                if($ARR['START_DT_SEARCH'] != '' && $ARR['END_DT_SEARCH'] != ''){ 
					$tmpKeyword .= "AND DATE_FORMAT(NPH.LAST_DTHMS, '%Y-%m-%d') BETWEEN '".addslashes($ARR['START_DT_SEARCH'])."' AND '".addslashes($ARR['END_DT_SEARCH'])."' \n";
				}else if($ARR['START_DT_SEARCH'] != '' ){
					$tmpKeyword .= "AND DATE_FORMAT(NPH.LAST_DTHMS, '%Y-%m-%d') >= '".$ARR['START_DT_SEARCH']."' \n";
				}else if($ARR['END_DT_SEARCH'] != ''){
					$tmpKeyword .= "AND DATE_FORMAT(NPH.LAST_DTHMS, '%Y-%m-%d') <= '".$ARR['END_DT_SEARCH']."' \n";
				}
			}
		}
		


		//공업사 검색
		if($ARR['MGG_NM_SEARCH'] != '') {
			$tmpKeyword .= "AND MGG.MGG_ID = '".addslashes($ARR['MGG_NM_SEARCH'])."'  \n";	
		}

		// 거래여부
		if($ARR['RCT_ID_SEARCH'] != '') {
			$tmpKeyword .= " AND MGGS.RCT_ID = '".addslashes($ARR['RCT_ID_SEARCH'])."' \n";
			
		}

		//구분검색
		if($ARR['CSD_ID_SEARCH'] != ''){
            $tmpKeyword .= " AND MGG.CSD_ID IN (";
            foreach ($ARR['CSD_ID_SEARCH'] as $index => $CSD_ARRAY) {
                $tmpKeyword .=" '".$CSD_ARRAY."',";
            }
            $tmpKeyword=substr($tmpKeyword, 0, -1); // 마지막 콤마제거
            $tmpKeyword .= ") \n";
        }

		//지역주소검색
		if($ARR['CRR_ID_SEARCH'] !=  '' || $ARR['CRR_ID_SEARCH'] != null) {
            $tmpKeyword .= " AND (";
            foreach ($ARR['CRR_ID_SEARCH'] as $index => $CRD_ARRAY) {
                $CSD = explode(',', $CRD_ARRAY);
                
                if($index == 0){
                    $tmpKeyword .= "(";
                    $tmpKeyword .= "`MGG`.`CRR_1_ID` = '".$CSD[0]."' \n";
                }else{
                    $tmpKeyword .= " OR(";
                    $tmpKeyword .= "  `MGG`.`CRR_1_ID` = '".$CSD[0]."' \n";
                }
                if($CSD[1] != NOT_CRR_2){
                    $tmpKeyword .= " AND `MGG`.`CRR_2_ID` = '".$CSD[1]."' \n";
                }
                
                if($CSD[2] != NOT_CRR_3){
                    $tmpKeyword .= " AND `MGG`.`CRR_3_ID` = '".$CSD[2]."' \n";
                }
                
                $tmpKeyword .= ") \n";
            }
            $tmpKeyword .= ") \n";
        }

		//포인트구분
		if($ARR['RPT_ID_SEARCH'] != ''){
			$tmpKeyword .= " AND NPH.RPT_ID = '".addslashes($ARR['RPT_ID_SEARCH'])."' \n";
		}

		//포인트상태
		if($ARR['RPS_ID_SEARCH'] != ''){
			$tmpKeyword .= " AND NPH.RPS_ID = '".addslashes($ARR['RPS_ID_SEARCH'])."' \n";
		}

		//취소여부
		if($ARR['CANCEL_SEARCH'] != ''){
			$tmpKeyword .= " AND NPH.NPH_CANCEL_YN = '".addslashes($ARR['CANCEL_SEARCH'])."' \n";
		}

		if(!empty($ARR['MGG_BIZ_ID_SEARCH'])) {
			$tmpKeyword .= " AND INSTR(`MGG`.`MGG_BIZ_ID`,'".addslashes($ARR['MGG_BIZ_ID_SEARCH'])."')>0 \n";
		}
		
		//품명
		if(!empty($ARR['NPH_PARTS_MEMO_SEARCH'])) {
			$tmpKeyword .= " AND INSTR(`NPH`.`NPH_PARTS_MEMO`,'".addslashes($ARR['NPH_PARTS_MEMO_SEARCH'])."')>0 \n";
		}
		

		// 페이징처리를 위해 전체데이터
		$iSQL = "SELECT 
					COUNT(*) AS CNT	
				FROM `NT_POINT`.`NPP_POINT_HISTORY` NPH,
					`NT_MAIN`.`NMG_GARAGE` MGG,
					`NT_MAIN`.`NMG_GARAGE_SUB` MGGS
				WHERE NPH.MGG_ID = MGG.MGG_ID
				AND MGG.MGG_ID = MGGS.MGG_ID
				AND MGG.DEL_YN='N'
				$tmpKeyword
				;"
		;
		// echo $iSQL;
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

		/** 전체리스트가 아닌경우에만 limitSql 적용 */
		$LIMIT_SQL = "";
		if($ARR['REQ_MODE'] == CASE_LIST){
			$LIMIT_SQL = "LIMIT $LimitStart,$LimitEnd";
		}

		
		// 리스트 가져오기
		$iSQL = "SELECT 
					NPH.NPH_ID,
					NPH.MGG_ID,
					MGGS.RCT_ID,
					NT_CODE.`GET_CODE_NM`(MGGS.RCT_ID) AS 'RCT_NM',
					NT_CODE.GET_CSD_NM(`MGG`.`CSD_ID`) AS 'CSD_NM',
					NT_CODE.`GET_CRR_NM`(MGG.CRR_1_ID, MGG.CRR_2_ID, '00000') AS 'CRR_NM',
					MGG.MGG_NM,
					MGG.MGG_BIZ_ID,
					CONCAT(`MGG_ADDRESS_NEW_1`, '  ', MGG_ADDRESS_NEW_2) MGGS_ADDR,
					MGGS.MGG_OFFICE_TEL,
					MGGS.MGG_OFFICE_FAX,
					NT_CODE.GET_CODE_NM(MGGS.RET_ID) AS RET_NM,
					MGGS.RET_ID AS RET_ID,
					NPH.RPT_ID,
					NT_CODE.`GET_CODE_NM`(NPH.RPT_ID) AS 'RPT_NM',
					IFNULL(NPH.NB_ID, '') AS 'NB_ID',
					NPH.RPS_ID,
					NT_CODE.`GET_CODE_NM`(NPH.RPS_ID) AS 'RPS_NM',
					FORMAT(NPH.NPH_POINT,0) AS 'NPH_POINT',
					IFNULL((SELECT FORMAT(NPB_POINT,0) FROM NT_POINT.NPP_POINT_BALANCE WHERE MGG_ID = NPH.MGG_ID),0) AS 'BALANCE',
					NPH.NPH_CANCEL_YN,
					NPH.NPH_CANCEL_MEMO,
					NPH.NPH_PARTS_MEMO,
					NPH.NPH_MEMO,
					NPH.WRT_USERID,
					NT_AUTH.`GET_USER_NM`(NPH.WRT_USERID) AS 'WRT_USER_NM',
					NPH.NPH_ISSUE_DT,
					NPH.NPH_ISSUE_DTHMS,
					NPH.LAST_DTHMS
				FROM 
					NT_POINT.NPP_POINT_HISTORY NPH,
					NT_MAIN.NMG_GARAGE MGG,
					NT_MAIN.NMG_GARAGE_SUB MGGS
				WHERE NPH.MGG_ID = MGG.MGG_ID
				AND MGG.MGG_ID = MGGS.MGG_ID
				AND MGG.DEL_YN='N'
				$tmpKeyword
				ORDER BY NPH.NPH_ISSUE_DTHMS DESC
				$LIMIT_SQL ;
		";
		// echo $iSQL;
		$Result = DBManager::getResult($iSQL);

		///

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"IDX"=>$IDX--,
					"NPH_ID"=>$RS['NPH_ID'],
					"MGG_ID"=>$RS['MGG_ID'],
					"RCT_ID"=>$RS['RCT_ID'],
					"RCT_NM"=>$RS['RCT_NM'],
					"CSD_NM"=>$RS['CSD_NM'],
					"CRR_NM"=>$RS['CRR_NM'],
					"MGG_NM"=>$RS['MGG_NM'],
					"MGG_BIZ_ID"=>$RS['MGG_BIZ_ID'],
					"MGGS_ADDR"=>$RS['MGGS_ADDR'],
					"MGG_OFFICE_TEL"=>$RS['MGG_OFFICE_TEL'],
					"MGG_OFFICE_FAX"=>$RS['MGG_OFFICE_FAX'],
					"RET_NM"=>$RS['RET_NM'],
					"RPT_ID"=>$RS['RPT_ID'],
					"RPT_NM"=>$RS['RPT_NM'],
					"NB_ID"=>$RS['NB_ID'],
					"RPS_ID"=>$RS['RPS_ID'],
					"RPS_NM"=>$RS['RPS_NM'],
					"NPH_POINT"=>$RS['NPH_POINT'],
					"BALANCE"=>$RS['BALANCE'],
					"NPH_CANCEL_YN"=>$RS['NPH_CANCEL_YN'],
					"NPH_CANCEL_MEMO"=>$RS['NPH_CANCEL_MEMO'],
					"NPH_PARTS_MEMO"=>$RS['NPH_PARTS_MEMO'],
					"NPH_MEMO"=>$RS['NPH_MEMO'],
					"WRT_USERID"=>$RS['WRT_USERID'],
					"WRT_USER_NM"=>$RS['WRT_USER_NM'],
					"NPH_ISSUE_DT"=>$RS['NPH_ISSUE_DT'],
					"NPH_ISSUE_DTHMS"=>$RS['NPH_ISSUE_DTHMS'],
					"LAST_DTHMS"=>$RS['LAST_DTHMS']
			));
		}

		// $Result->close();  // 자원 반납
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
	 * 업데이트
	 */
	function setData($ARR) {

		if(is_array($ARR)){
			foreach($ARR['NPH_ID'] as $index => $NPH_ID){
				
				$iSQL .= "UPDATE  `NT_POINT`.`NPP_POINT_HISTORY`
							SET 
							`RPS_ID` = '60119',
							`NPH_MEMO` = '".addslashes($ARR['NPH_MEMO'])."',
							`LAST_DTHMS` = NOW(),
							`WRT_USERID` = '".addslashes($_SESSION['USERID'])."'
						WHERE NPH_ID = '".addslashes($ARR['NPH_ID'][$index])."' 
						AND `NPH_CANCEL_YN` = 'N'
						AND `RPS_ID` = '60110';
				";

			}
			// echo $iSQL;
			return DBManager::execMulti($iSQL);
		}
	}

	/**
	 * data 삭제
	 * @param {$ARR}
	 * @param {NB_ID : 청구서 ID}
	 * @param {NBI_RMT_ID : 청구구분}
	 */
	function delData($ARR) {
		$iSQL = "";
		if($ARR['NPH_ID'] != '' ){
			$iSQL = "UPDATE  `NT_POINT`.`NPP_POINT_HISTORY`
						SET 
						`NPH_CANCEL_YN` = 'Y',
						`NPH_CANCEL_DTHMS`= NOW(),
						`NPH_CANCEL_USERID`= '".addslashes($_SESSION['USERID'])."',
						`NPH_CANCEL_MEMO` = '".addslashes($ARR['NPH_CANCEL_MEMO'])."',
						`LAST_DTHMS` = NOW(),
						`WRT_USERID` = '".addslashes($_SESSION['USERID'])."'
					WHERE NPH_ID = '".addslashes($ARR['NPH_ID'])."' ;
			";
		}
		
		// echo 	$iSQL ;
		return DBManager::execQuery($iSQL);
	}

	/** 공업사정보 */
	function getGarageInfo($ARR){
		$iSQL = "SELECT 
					MGG.MGG_NM,
					MGG.MGG_ID,
					MGG.MGG_BIZ_ID
				FROM NT_MAIN.NMG_GARAGE MGG
				WHERE MGG.DEL_YN = 'N';
		";
		
		$Result= DBManager::getResult($iSQL);
		///

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
				"MGG_NM"=>$RS['MGG_NM'],
				"MGG_ID"=>$RS['MGG_ID'],
				"MGG_BIZ_ID"=>$RS['MGG_BIZ_ID']
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


	/** 적립/사용등록 창 구분값 조회 */
	function getPointStatus(){
		$iSQL = "SELECT RPS_ID, RPS_NM 
				FROM NT_CODE.REF_POINT_STATUS 
				WHERE RPS_ID IN ('60119','60129') ;
		";
		$Result= DBManager::getResult($iSQL);
		///

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
				"RPS_ID"=>$RS['RPS_ID'],
				"RPS_NM"=>$RS['RPS_NM']
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


	/** 적립/ 사용등록 */
	function setAddPoint($ARR){
		$iSQL = "INSERT INTO	`NT_POINT`.`NPP_POINT_HISTORY`
				(
					`MGG_ID`,
					`RPT_ID`,
					`RPS_ID`,
					`NB_ID`,
					`NO_ID`,
					`NPH_POINT`,
					`NPH_ISSUE_DT`,
					`NPH_ISSUE_DTHMS`,
					`NPH_PARTS_MEMO`,
					`NPH_MEMO`,
					`WRT_DTHMS`,
					`LAST_DTHMS`,
					`WRT_USERID`
				) VALUES (
					'".addslashes($ARR['ADD_MGG_ID'])."',
					'".addslashes($ARR['ADD_RPT_ID'])."',
					'".addslashes($ARR['ADD_RPS_ID'])."',
					NULL,
					NULL,
					'".addslashes($ARR['ADD_NPH_POINT'])."',
					'".addslashes($ARR['UPDATE_DTHMS'])."',
					NOW(),
					'".addslashes($ARR['ADD_NPH_PARTS_MEMO'])."',
					'".addslashes($ARR['ADD_NPH_MEMO'])."',
					NOW(),
					NOW(),
					'".addslashes($_SESSION['USERID'])."'
				);
		";
		// echo $iSQL;
		return DBManager::execQuery($iSQL);

	}
}
	// 0. 객체 생성
	$cPointMngt = new cPointMngt(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$cPointMngt->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);
?>