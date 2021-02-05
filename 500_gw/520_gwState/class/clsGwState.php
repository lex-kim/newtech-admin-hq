<?php

class clsGwState extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자


	// Class Object Constructor
	function clsGwState($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
		$this->DATA_SAVE_PATH = $DATA_SAVE_PATH;
		$this->ALLOW_FILE_EXT = $ALLOW_FILE_EXT;
	}

	/**
	 * 전체데이터 가져오는 함수
	*/
	function getTotalList(){
		$iSQL = $this->getListSql('');

		try{
			$Result= DBManager::getResult($iSQL);
		} catch (Exception $e){
			echo "error : ".$e;
			return ERROR_DB_SELECT_DATA;   //ERROR_DB_SELECT_DATA
		}

		$Result->close();  // 자원 반납
		unset($RS);

		if(empty($rtnArray)) {
			return HAVE_NO_DATA;
		} else {
			$iSQL = "SELECT 
						`CPP`.`CPP_NM_SHORT`,
						`CPPS`.`CPPS_NM_SHORT`,
						`CPPS`.`CPPS_NM`,
						`MGGP`.`MGG_ID`,
						`MGGP`.`CPP_ID`,
						`MGGP`.`CPPS_ID`,
						`MGGP`.`NGP_DEFAULT_YN`,
						`MGGP`.`NGP_RATIO`,
						`MGGP`.`NGP_MEMO`
					FROM `NT_MAIN`.`NMG_GARAGE_PART` `MGGP`,
						`NT_CODE`.`NCP_PART` `CPP`,
						`NT_CODE`.`NCP_PART_SUB` `CPPS`
					WHERE `MGGP`.`CPP_ID` =  `CPP`.`CPP_ID`
					AND `MGGP`.`CPPS_ID` =  `CPPS`.`CPPS_ID`
					ORDER BY `MGGP`.`CPP_ID` DESC
					;"
			;

			$Result= DBManager::getResult($iSQL);

			$partsArray = array();
			while($RS = $Result->fetch_array()){
				array_push($partsArray,
					array(
						"CPP_NM_SHORT"=>$RS['CPP_NM_SHORT'],
						"CPPS_NM"=>$RS['CPPS_NM'],
						"CPPS_NM_SHORT"=>$RS['CPPS_NM_SHORT'],
						"MGG_ID"=>$RS['MGG_ID'],
						"CPP_ID"=>$RS['CPP_ID'],
						"CPPS_ID"=>$RS['CPPS_ID'],
						"NGP_DEFAULT_YN"=>$RS['NGP_DEFAULT_YN'],
						"NGP_RATIO"=>$RS['NGP_RATIO'],
						"NGP_MEMO"=>$RS['NGP_MEMO'],
				));
			}
			$Result->close();  // 자원 반납
			unset($RS);

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
		
		

		//구분검색
		if($ARR['CSD_ID_SEARCH'] != '') {
			$tmpKeyword .= "AND (";
			foreach ($ARR['CSD_ID_SEARCH'] as $index => $CSD_ID) {
				if($index == 0){
					$tmpKeyword .= "(";
					$tmpKeyword .= "INSTR(`VMS`.`CSD_ID`,'".$CSD_ID."')>0 ";
				}else{
					$tmpKeyword .= " OR(";
					$tmpKeyword .= "  INSTR(`VMS`.`CSD_ID`,'".$CSD_ID."')>0 ";
				}
				$tmpKeyword .= ")";
			}
			$tmpKeyword .= ")";
		} 

		//지역주소검색
		if($ARR['CRR_ID_SEARCH'] != '') {
			$tmpKeyword .= "AND (";
			foreach ($ARR['CRR_ID_SEARCH'] as $index => $CRD_ARRAY) {
				$CSD = explode(',', $CRD_ARRAY);
				
				if($index == 0){
					$tmpKeyword .= "(";
					$tmpKeyword .= "INSTR(`VMS`.`CRR_1_ID`,'".$CSD[0]."')>0 ";
				}else{
					$tmpKeyword .= " OR(";
					$tmpKeyword .= "  INSTR(`VMS`.`CRR_1_ID`,'".$CSD[0]."')>0 ";
				}
				$tmpKeyword .= "AND INSTR(`VMS`.`CRR_2_ID`,'".$CSD[1]."')>0 ";
				if($CSD[2] != NOT_CRR_3){
					$tmpKeyword .= "AND INSTR(`VMS`.`CRR_3_ID`,'".$CSD[2]."')>0 ";
				}
				
				$tmpKeyword .= ")";
			}
			$tmpKeyword .= ")";
		} 

		$iSQL = "";
		//거래여부 검색
		if($ARR['RCT_ID_SERACH'] != '') {
			// $tmpKeyword .= "AND INSTR(`VMS`.`RCT_ID`,'".addslashes($ARR['RCT_ID_SERACH'])."')>0 ";	
			$iSQL .= "SELECT * FROM(";
		} 

		// 페이징처리를 위해 전체데이터
		$iSQL .= "
				SELECT COUNT(*) AS CNT FROM (
					SELECT 	
						`VMS`.`CSD_ID`,
						`VMS`.`CRR_1_ID`,
						`VMS`.`CRR_2_ID`,
						SUM(IF(RCT_ID='10610',1,0) ) AS VMS_10610,
						SUM(IF(RCT_ID='10640',1,0) ) AS VMS_10640,
						SUM(IF(RCT_ID='10620',1,0) ) AS VMS_10620,
						SUM(IF(RCT_ID='10650',1,0) ) AS VMS_10650,
						SUM(IF(RCT_ID='10630',1,0) ) AS VMS_10630
					FROM NT_MAIN.V_MGG_STATUS `VMS`,
						NT_CODE.NCS_DIVISION `CSD`,
						NT_CODE.NCR_REGION `CNR`
					WHERE `VMS`.`CSD_ID` = `CSD`.`CSD_ID`
					AND `CNR`.CRR_1_ID = `VMS`.`CRR_1_ID`
					AND `CNR`.CRR_2_ID = `VMS`.`CRR_2_ID`
					AND `CNR`.CRR_3_ID = '00000'
					$tmpKeyword
					GROUP BY  `VMS`.`CSD_ID`, `VMS`.`CRR_1_ID` , `VMS`.`CRR_2_ID`) AS VMS
				"
		;

		//거래여부 검색
		if($ARR['RCT_ID_SERACH'] != '') {	
			$iSQL .= " WHERE VMS.VMS_".$ARR['RCT_ID_SERACH'].") AS VVMS;";
		}else{
			$iSQL .= ";";
		}

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

		
		$iSQL = "";
		//거래여부 검색
		if($ARR['RCT_ID_SERACH'] != '') {
			// $tmpKeyword .= "AND INSTR(`VMS`.`RCT_ID`,'".addslashes($ARR['RCT_ID_SERACH'])."')>0 ";	
			$iSQL .= "SELECT * FROM(";
		} 
		// 리스트 가져오기
		$iSQL .= "SELECT 
						(SELECT `CSD_NM` FROM NT_CODE.NCS_DIVISION WHERE `CSD_ID` = `VMS`.`CSD_ID`)AS VMS_CSD_NM,
						CONCAT(`CNR`.CRR_1_NM,' ',`CNR`.CRR_2_NM) AS VMS_CRR_NM,
						COUNT(`VMS`.`RCT_ID`) AS VMS_TOTAL,
						
						(SUM(IF(RCT_ID='10610',1,0)) + IF(RCT_ID='10640',1,0)) AS VMS_DEAL_TOTAL,
						SUM(IF(RCT_ID='10610',1,0) ) AS VMS_10610,
						SUM(IF(RCT_ID='10640',1,0) ) AS VMS_10640,
						
						SUM(IF(RCT_ID='10620',1,0) ) AS VMS_10620,
						SUM(IF(RSR_ID='10510',1,0) ) AS VMS_STOP_10,
						SUM(IF(RSR_ID='10520',1,0) ) AS VMS_STOP_20,
						SUM(IF(RSR_ID='10530',1,0) ) AS VMS_STOP_30,
						SUM(IF(RSR_ID='10540',1,0) ) AS VMS_STOP_40,
						SUM(IF(RSR_ID='10550',1,0) ) AS VMS_STOP_50,
						SUM(IF(RSR_ID='10560',1,0) ) AS VMS_STOP_60,
						SUM(IF(RSR_ID='10570',1,0) ) AS VMS_STOP_70,
						SUM(IF(RSR_ID='10580',1,0) ) AS VMS_STOP_80,
						SUM(IF(RSR_ID='10590',1,0) ) AS VMS_STOP_90,
						
						(SUM(IF(RCT_ID='10650',1,0)) + IF(RCT_ID='10630',1,0)) AS VMS_UNTRADED_TOTAL,
						SUM(IF(RCT_ID='10650',1,0) ) AS VMS_10650,
						SUM(IF(RCT_ID='10630',1,0) ) AS VMS_10630
					
					FROM NT_MAIN.V_MGG_STATUS `VMS`,
						NT_CODE.NCS_DIVISION `CSD`,
						NT_CODE.NCR_REGION `CNR`
					WHERE `VMS`.`CSD_ID` = `CSD`.`CSD_ID`
					AND `CNR`.CRR_1_ID = `VMS`.`CRR_1_ID`
					AND `CNR`.CRR_2_ID = `VMS`.`CRR_2_ID`
					AND `CNR`.CRR_3_ID = '00000'
					$tmpKeyword
					GROUP BY  `VMS`.`CSD_ID`, `VMS`.`CRR_1_ID` , `VMS`.`CRR_2_ID`
					$LIMIT_SQL
					
				"
		;
		//거래여부 검색
		if($ARR['RCT_ID_SERACH'] != '') {
			// $tmpKeyword .= "AND INSTR(`VMS`.`RCT_ID`,'".addslashes($ARR['RCT_ID_SERACH'])."')>0 ";	
			$iSQL .= ") AS VMS WHERE VMS.VMS_".$ARR['RCT_ID_SERACH'].";";
		}else{
			$iSQL .= ";";
		}

	
		$Result = DBManager::getResult($iSQL);

		///

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"VMS_CSD_NM"=>$RS['VMS_CSD_NM'],
					"VMS_CRR_NM"=>$RS['VMS_CRR_NM'],
					"VMS_TOTAL"=>$RS['VMS_TOTAL'],
					"VMS_DEAL_TOTAL"=>$RS['VMS_DEAL_TOTAL'],
					"VMS_10610"=>$RS['VMS_10610'],
					"VMS_10640"=>$RS['VMS_10640'],
					"VMS_10620"=>$RS['VMS_10620'],
					"VMS_STOP_10"=>$RS['VMS_STOP_10'],
					"VMS_STOP_20"=>$RS['VMS_STOP_20'],
					"VMS_STOP_30"=>$RS['VMS_STOP_30'],
					"VMS_STOP_40"=>$RS['VMS_STOP_40'],
					"VMS_STOP_50"=>$RS['VMS_STOP_50'],
					"VMS_STOP_60"=>$RS['VMS_STOP_60'],
					"VMS_STOP_70"=>$RS['VMS_STOP_70'],
					"VMS_STOP_80"=>$RS['VMS_STOP_80'],
					"VMS_STOP_90"=>$RS['VMS_STOP_90'],
					"VMS_UNTRADED_TOTAL"=>$RS['VMS_UNTRADED_TOTAL'],
					"VMS_10650"=>$RS['VMS_10650'],
					"VMS_10630"=>$RS['VMS_10630'],
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


}
	// 0. 객체 생성
	$clsGwState = new clsGwState(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$clsGwState->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
