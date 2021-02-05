<?php

class clsFlucStatus extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	// Class Object Constructor
	function clsFlucStatus($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
		$this->DATA_SAVE_PATH = $DATA_SAVE_PATH;
		$this->ALLOW_FILE_EXT = $ALLOW_FILE_EXT;
	}

	/**
	 * 리스트조회
	 */
	function getList($ARR) {
		$LimitStart = ($ARR['CURR_PAGE']-1) * $ARR['PER_PAGE'];
		$LimitEnd   = $ARR['PER_PAGE'];
		$dateSQL = "";
		$tmpKeyword = "";
		
		/** 기간검색 */
		if($ARR['START_YEAR'] != '' || $ARR['END_YEAR'] != '') {
			if($ARR['START_YEAR'] !='' && $ARR['END_YEAR'] !=''){
				$tmpKeyword .= " AND DATE_FORMAT(`NB`.`NB_CLAIM_DT`, '%Y') BETWEEN '".addslashes($ARR['START_YEAR'])."' AND '".addslashes($ARR['END_YEAR'])."'";
			}else if($ARR['START_YEAR'] !='' && $ARR['END_YEAR'] ==''){
				$tmpKeyword .= " AND DATE_FORMAT(`NB`.`NB_CLAIM_DT`, '%Y') >= '".addslashes($ARR['START_YEAR'])."'";
			}else{
				$tmpKeyword .= " AND DATE_FORMAT(`NB`.`NB_CLAIM_DT`, '%Y') <= '".addslashes($ARR['END_YEAR'])."'";
			}
		} 

		// 페이징처리를 위해 전체데이터
		$iSQL = "SELECT COUNT(*) AS CNT FROM (
					SELECT 
						CONCAT(YEAR(NB.NB_CLAIM_DT),'/' ,MONTH(NB.NB_CLAIM_DT) )AS CLAIM_DT,
						COUNT(DISTINCT(NB.MGG_ID)) AS MGG_CNT,
						COUNT(NB.NB_ID) AS NB_CNT,
						FORMAT(SUM(NB.NB_TOTAL_PRICE),0) AS NB_PRICE
					FROM NT_BILL.NBB_BILL NB
					WHERE NB.NB_CLAIM_DT != '0000-00-00'
					$tmpKeyword 
					GROUP BY YEAR(NB.NB_CLAIM_DT), MONTH(NB.NB_CLAIM_DT) ASC
					ORDER BY NB.NB_CLAIM_DT DESC
				) AS C
				;"
		;
	
		// echo $iSQL;
		try{
			$RS_T	= DBManager::getRecordSet($iSQL);
		} catch (Exception $e){
			echo "error : ".$e;
			return ERROR_DB_SELECT_DATA;   //ERROR_DB_SELECT_DATA
		}
		$TOTAL_LIST_COUNT = $RS_T['CNT'];
		$IDX = $RS_T['CNT']-$LimitStart;
		unset($RS_T);

		/** 전체리스트가 아닌경우에만 limitSql 적용 */
		$LIMIT_SQL = "";
		if($ARR['REQ_MODE'] == CASE_LIST){
			$LIMIT_SQL = "LIMIT $LimitStart,$LimitEnd";
		}

		$CSD_ARRAY = $this->getOptionListDivision($ARR);
		$CSD_SQL = "";
		foreach($CSD_ARRAY as $index => $CSD_ITEM){
			$CSD_SQL .= " (SELECT 
							CONCAT(IFNULL(NSG_DT1_CNT,'-'),'||',IFNULL(NSG_DT2_CNT,'-'),'||',IFNULL(NSG_NEW_CNT,'-'),'||',IFNULL(NSG_END_CNT,'-')) 
							FROM
								NT_MAIN.NMG_STAT_GARAGE NSG
							WHERE  DATE_FORMAT(NB.NB_CLAIM_DT, '%Y%m')  = NSG.NSG_ID
							AND CSD_ID='".$CSD_ITEM['CSD_ID']."'
						) AS 'CSD_".$CSD_ITEM['CSD_ID']."',";
		}
		$CSD_SQL = substr($CSD_SQL, 0, -1); // 마지막 콤마제거

		$iSQL = "SELECT 
					DATE_FORMAT(NB.NB_CLAIM_DT, '%Y-%m') AS CLAIM_DT,
					COUNT(DISTINCT(NB.MGG_ID)) AS MGG_CNT,
					COUNT(NB.NB_ID) AS NB_CNT,
					FORMAT(SUM(NB.NB_TOTAL_PRICE),0) AS NB_PRICE, 
					$CSD_SQL
				FROM NT_BILL.NBB_BILL NB
				WHERE NB.NB_CLAIM_DT != '0000-00-00'
				AND NB.DEL_YN = 'N'
				AND DATE_FORMAT(NB.NB_CLAIM_DT, '%Y%m') <> DATE_FORMAT( NOW(), '%Y%m')  
				$tmpKeyword 
				GROUP BY YEAR(NB.NB_CLAIM_DT), MONTH(NB.NB_CLAIM_DT) ASC
				ORDER BY NB.NB_CLAIM_DT DESC
				$LIMIT_SQL 
			;"

		;
		// echo $iSQL;
		$Result = DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			$TOTAL_CNT = 0;
			$TOTAL_ING = 0;
			$TOTAL_NEW = 0;
			$TOTAL_END = 0;
			$CNT_ARRAY = array();
			foreach($CSD_ARRAY as $index => $CSD_ITEM){
				$TEMP_ARRAY = array();
				if(!empty($RS['CSD_'.$CSD_ITEM['CSD_ID']])){
					$csdData = explode('||',$RS['CSD_'.$CSD_ITEM['CSD_ID']] );
					$TOTAL_CNT += (int)$csdData[0];
					$TOTAL_ING += (int)$csdData[1];
					$TOTAL_NEW += (int)$csdData[2];
					$TOTAL_END += (int)$csdData[3];
					array_push($TEMP_ARRAY, $csdData[0] );
					array_push($TEMP_ARRAY, $csdData[1] );
					array_push($TEMP_ARRAY, $csdData[2] );
					array_push($TEMP_ARRAY, $csdData[3] );
				}else {
					array_push($TEMP_ARRAY, '-' );
					array_push($TEMP_ARRAY, '-' );
					array_push($TEMP_ARRAY, '-' );
					array_push($TEMP_ARRAY, '-' );
				}
				array_push($CNT_ARRAY, $TEMP_ARRAY);
			}
			
			array_push($rtnArray,
				array(
					"CLAIM_DT"=>$RS['CLAIM_DT'],
					"MGG_CNT"=>$RS['MGG_CNT'],
					"NB_CNT"=>$RS['NB_CNT'],
					"NB_PRICE"=>$RS['NB_PRICE'],
					"TOTAL_CNT"=>$TOTAL_CNT,
					"TOTAL_ING"=>$TOTAL_ING,
					"TOTAL_NEW"=>$TOTAL_NEW,
					"TOTAL_END"=>$TOTAL_END,
					"DATA"=>$CNT_ARRAY,
			));
		}

		$Result->close();  // 자원 반납
		unset($RS);

		if(empty($rtnArray)){
			return HAVE_NO_DATA;
		}else{
			$jsonArray = array();
			array_push($jsonArray,$rtnArray);
			array_push($jsonArray,$TOTAL_LIST_COUNT);
			return $jsonArray;

		}
	}

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
		return $rtnArray;
	}

}
	// 0. 객체 생성
	$clsFlucStatus = new clsFlucStatus(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$clsFlucStatus->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
