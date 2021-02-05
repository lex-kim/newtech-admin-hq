<?php

class clsVisitingGw extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	// Class Object Constructor
	function clsVisitingGw($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
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
		if(!empty($ARR['SELECT_DT'])){
			if($ARR['START_DT'] != '' || $ARR['END_DT'] != '') {
				if($ARR['START_DT'] !='' && $ARR['END_DT'] !=''){
					$tmpKeyword .= " AND DATE_FORMAT(`NSU`.`".$ARR['SELECT_DT']."`, '%Y-%m-%d') BETWEEN '".addslashes($ARR['START_DT'])."' AND '".addslashes($ARR['END_DT'])."'";
				}else if($ARR['START_DT'] !='' && $ARR['END_DT'] ==''){
					$tmpKeyword .= " AND DATE_FORMAT(`NSU`.`".$ARR['SELECT_DT']."`, '%Y-%m-%d') >= '".addslashes($ARR['START_DT'])."'";
				}else{
					$tmpKeyword .= " AND DATE_FORMAT(`NSU`.`".$ARR['SELECT_DT']."`, '%Y-%m-%d') <= '".addslashes($ARR['END_DT'])."'";
				}
			} 
		}else{
			if($ARR['START_DT'] != '' || $ARR['END_DT'] != '') {
				if($ARR['START_DT'] !='' && $ARR['END_DT'] !=''){
					$tmpKeyword .= " AND ( DATE_FORMAT(`NSU`.`NSU_VISIT_DT`, '%Y-%m-%d') BETWEEN '".addslashes($ARR['START_DT'])."' AND '".addslashes($ARR['END_DT'])."'";
					$tmpKeyword .= " OR DATE_FORMAT(`NSU`.`NSU_SUPPLY_DT`, '%Y-%m-%d') BETWEEN '".addslashes($ARR['START_DT'])."' AND '".addslashes($ARR['END_DT'])."' )";
				}else if($ARR['START_DT'] !='' && $ARR['END_DT'] ==''){
					$tmpKeyword .= " AND ( DATE_FORMAT(`NSU`.`NSU_VISIT_DT`, '%Y-%m-%d') >= '".addslashes($ARR['START_DT'])."'";
					$tmpKeyword .= " OR DATE_FORMAT(`NSU`.`NSU_SUPPLY_DT` >= '".addslashes($ARR['START_DT'])."' )";
				}else if($ARR['START_DT'] =='' && $ARR['END_DT'] !=''){
					$tmpKeyword .= " AND ( DATE_FORMAT(`NSU`.`NSU_VISIT_DT`, '%Y-%m-%d') <= '".addslashes($ARR['END_DT'])."'";
					$tmpKeyword .= " OR DATE_FORMAT(`NSU`.`NSU_SUPPLY_DT`, '%Y-%m-%d') <= '".addslashes($ARR['END_DT'])."' )";
				}else{
					$tmpKeyword = "";
				}
			} 
		}

		// 구분검색
        if($ARR['CSD_ID_SEARCH'] != ''){
			$tmpKeyword .= " AND MGG.CSD_ID IN (";
			foreach ($ARR['CSD_ID_SEARCH'] as $index => $CSD_ARRAY) {
				$tmpKeyword .=" '".$CSD_ARRAY."',";
			}
			$tmpKeyword=substr($tmpKeyword, 0, -1); // 마지막 콤마제거
			$tmpKeyword .= ") \n";
        }
		
		//지역검색
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
        
		//공업사명검색
		if($ARR['MGG_NM_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(MGG.MGG_NM ,'".addslashes($ARR['MGG_NM_SEARCH'])."')>0 ";
		} 

		if($ARR['RCT_ID_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(`MGGS`.`RCT_ID`,'".addslashes($ARR['RCT_ID_SEARCH'])."')>0 ";
		}

		//방문자
		if($ARR['VISITANT_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(NSU.WRT_USERID ,'".addslashes($ARR['VISITANT_SEARCH'])."')>0 ";
		} 

		//처리자
		if($ARR['DISPOSER_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(NSU.NSU_PROC_USERID ,'".addslashes($ARR['DISPOSER_SEARCH'])."')>0 ";
		} 
		
		//첨부파일
		if($ARR['RSF_ID_SELECT'] != '') {
			$tmpKeyword .= " AND NSU.RSF_ID  = '".$ARR['RSF_ID_SELECT']."' \n";
		} 

		// 페이징처리를 위해 전체데이터
		$iSQL = "SELECT
					COUNT(NSU.NSU_ID) AS CNT
					
				FROM NT_SALES.NS_SALES_UPLOAD NSU,
					NT_MAIN.NMG_GARAGE MGG,
					NT_MAIN.NMG_GARAGE_SUB MGGS
							
				WHERE MGG.MGG_ID = NSU.MGG_ID
				AND MGG.MGG_ID = MGGS.MGG_ID
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
		$TOTAL_LIST_COUNT = $RS_T['CNT'];
		$IDX = $RS_T['CNT']-$LimitStart;
		unset($RS_T);

		/** 전체리스트가 아닌경우에만 limitSql 적용 */
		$LIMIT_SQL = "";
		if($ARR['REQ_MODE'] == CASE_LIST){
			$LIMIT_SQL = "LIMIT $LimitStart,$LimitEnd";
		}

		$ORDERBY_SQL = "ORDER BY NSU.NSU_ID DESC";
		if(!empty($ARR['SORT_TYPE'])){
			if($ARR['SORT_TYPE'] === '2'){
				$ORDERBY_SQL = "ORDER BY MGG.MGG_CIRCUIT_ORDER DESC";
			}else{
				$ORDERBY_SQL = "ORDER BY MGG.MGG_CIRCUIT_ORDER ASC";
			}  
		}
		
		$iSQL = "SELECT
					NSU.NSU_ID,
					MGG.MGG_ID,
					NT_CODE.GET_CSD_NM(MGG.CSD_ID) AS CSD_NM,
					NT_CODE.GET_CRR_NM(MGG.CRR_1_ID, MGG.CRR_2_ID,'00000') AS CRR_NM,
					MGG.MGG_CIRCUIT_ORDER,
					MGG.MGG_NM,
					IFNULL( NT_CODE.GET_RET_NM(MGGS.RET_ID),'') AS RET_NM,
					NT_CODE.GET_RCT_NM(MGGS.RCT_ID) AS RCT_NM,
					NSU.WRT_USERID,
					NSU.NSU_VISIT_DT,
					NSU.NSU_LATITUDE,
					CONCAT(NSU.NSU_LATITUDE,',',NSU.NSU_LONGITUDE)AS NSU_ADDR,
					NSU.NSU_LONGITUDE,
					IFNULL(NSU.NSU_SUPPLY_DT,'') AS NSU_SUPPLY_DT,
					NSU.NSU_PROC_YN,
					IFNULL(NSU.NSU_PROC_USERID,'') AS NSU_PROC_USERID,
					IFNULL(NSU.NSU_PROC_DTHMS,'') AS NSU_PROC_DTHMS,
					NSU.NSU_SIGN_IMG,
					NSU.NSU_SIGN_CONTENTTYPE,
					NSU.NSU_FILE_IMG,
					NSU.NSU_FILE_CONTENTTYPE,
					(
						SELECT RSF_NM FROM NT_CODE.REF_SALES_FILE WHERE RSF_ID = NSU.RSF_ID
					)AS RSF_NM
					
				FROM NT_SALES.NS_SALES_UPLOAD NSU,
					NT_MAIN.NMG_GARAGE MGG,
					NT_MAIN.NMG_GARAGE_SUB MGGS
							
				WHERE MGG.MGG_ID = NSU.MGG_ID
				AND MGG.MGG_ID = MGGS.MGG_ID
				$tmpKeyword 
				$ORDERBY_SQL 
				$LIMIT_SQL 
			;"
		;
		// echo $iSQL;
		$Result = DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"IDX" => $IDX--,
					"NSU_ID"=>$RS['NSU_ID'],
					"MGG_ID"=>$RS['MGG_ID'],
					"CSD_NM"=>$RS['CSD_NM'],
					"CRR_NM"=>$RS['CRR_NM'],
					"NSU_SIGN_IMG"=>$RS['NSU_SIGN_IMG'],
					"NSU_FILE_IMG"=>$RS['NSU_FILE_IMG'],
					"NSU_FILE_CONTENTTYPE"=>$RS['NSU_FILE_CONTENTTYPE'],
					"NSU_SIGN_CONTENTTYPE"=>$RS['NSU_SIGN_CONTENTTYPE'],
					"MGG_CIRCUIT_ORDER"=>$RS['MGG_CIRCUIT_ORDER'],
					"MGG_NM"=>$RS['MGG_NM'],
					"RET_NM"=>$RS['RET_NM'],
					"RCT_NM"=>$RS['RCT_NM'],
					"NSU_ADDR"=>$RS['NSU_ADDR'],
					"WRT_USERID"=>$RS['WRT_USERID'],
					"NSU_VISIT_DT"=>$RS['NSU_VISIT_DT'],
					"NSU_SUPPLY_DT"=>$RS['NSU_SUPPLY_DT'],
					"NSU_PROC_YN"=>$RS['NSU_PROC_YN'],
					"NSU_PROC_USERID"=>$RS['NSU_PROC_USERID'],
					"NSU_PROC_DTHMS"=>$RS['NSU_PROC_DTHMS'],
					"RSF_NM"=>$RS['RSF_NM'],
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

	function delData ($ARR) {
		foreach($ARR['NSU_ID'] as $index => $NSU_ID){
			$iSQL .= "DELETE FROM NT_SALES.NS_SALES_UPLOAD 
					WHERE NSU_ID = '".addslashes($NSU_ID)."'
					;
			";
		
		}
		return DBManager::execMulti($iSQL);
	}

}
	// 0. 객체 생성
	$clsVisitingGw = new clsVisitingGw(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$clsVisitingGw->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
