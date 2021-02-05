<?php

class cPurchase extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	// Class Object Constructor
	function cPurchase($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
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

		if($ARR['REQ_MODE'] == CASE_LIST){
			$LIMIT_SQL = "LIMIT $LimitStart,$LimitEnd ;";
        }else {
			$LIMIT_SQL = " ;";
		}

	

		/** 기간검색 */
		if($ARR['START_DT_SEARCH'] != '' && $ARR['END_DT_SEARCH'] != ''){ 
			$tmpKeyword .= "AND DATE_FORMAT(NCP.WRT_DTHMS, '%Y-%m-%d') BETWEEN '".addslashes($ARR['START_DT_SEARCH'])."' AND '".addslashes($ARR['END_DT_SEARCH'])."' \n";
		}else if($ARR['START_DT_SEARCH'] != '' ){
			$tmpKeyword .= "AND DATE_FORMAT(NCP.WRT_DTHMS, '%Y-%m-%d') >= '".$ARR['START_DT_SEARCH']."' \n";
		}else if($ARR['END_DT_SEARCH'] != ''){
			$tmpKeyword .= "AND DATE_FORMAT(NCP.WRT_DTHMS, '%Y-%m-%d') <= '".$ARR['END_DT_SEARCH']."' \n";
		}

		//장비구분 검색
		if($ARR['NCC_ID_SEARCH'] !=''){
			$tmpKeyword = "AND NCP.NCC_ID = '".addslashes($ARR['NCC_ID_SEARCH'])."' \n";
		}

		//장비명 검색
		if($ARR['NCP_NM_SEARCH'] !=''){
			$tmpKeyword = "AND `NCP`.`NCP_ID` = '".addslashes($ARR['NCP_NM_SEARCH'])."' \n";
		}

		//장비번호 검색
		// if($ARR['NCC_ID_SEARCH'] !=''){
		// 	$tmpKeyword = "AND NCP.NCC_ID = '".addslashes($ARR['NCC_ID_SEARCH'])."' ";
		// }

		//제조사 검색
		if($ARR['NCP_MANUFACTURER_SEARCH'] !=''){
			$tmpKeyword = "AND INSTR(`NCP`.`NCP_MANUFACTURER`,'".addslashes($ARR['NCP_MANUFACTURER_SEARCH'])."')>0 \n";
		}



		// 등록자 검색
        if($ARR['WRT_USERID_SEARCH'] != '') {
            $tmpKeyword .= "AND INSTR(NT_AUTH.GET_USER_NM(NCP.WRT_USERID),'".addslashes($ARR['WRT_USERID_SEARCH'])."')>0 \n";	
        } 

		// 페이징처리를 위해 전체데이터
		$iSQL = "SELECT
					COUNT(*) AS CNT
				FROM
					`NT_SALES`.`NSC_CONSIGN_PARTS` `NCP`
				WHERE	1=1
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
					`NCP_ID`,
					`NCC_ID`,
					`NT_SALES`.`GET_CONSIGN_TYPE_NM`(NCP.NCC_ID) AS 'NCC_NM',
					`NCP_NM`,
					`NCP_MANUFACTURER`,
					`NCP_PRICE`,
					IFNULL(`NT_SALES`.`GET_NCP_CNT`(NCP.NCP_ID),0) AS 'NCP_CNT',
					`NCP_USE_YN`,
					`NCP_ORDER_NUM`,
					DATE_FORMAT(`WRT_DTHMS`, '%Y-%m-%d') AS 'WRT_DTHMS',
					`LAST_DTHMS`,
					`WRT_USERID`,
					`NT_AUTH`.`GET_USER_NM`(NCP.WRT_USERID) AS 'WRT_USER_NM'
				FROM `NT_SALES`.`NSC_CONSIGN_PARTS` `NCP`
				WHERE 1=1  
					 $tmpKeyword 
				ORDER BY `NCP`.`NCP_ORDER_NUM` ASC 
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
				"NCP_ID"=>$RS['NCP_ID'],
				"NCC_ID"=>$RS['NCC_ID'],
				"NCC_NM"=>$RS['NCC_NM'],
				"NCP_NM"=>$RS['NCP_NM'],
				"NCP_MANUFACTURER"=>$RS['NCP_MANUFACTURER'],
				"NCP_PRICE"=>$RS['NCP_PRICE'],
				"NCP_CNT"=>$RS['NCP_CNT'],
				"NCP_USE_YN"=>$RS['NCP_USE_YN'],
				"NCP_ORDER_NUM"=>$RS['NCP_ORDER_NUM'],
				"WRT_DTHMS"=>$RS['WRT_DTHMS'],
				"LAST_DTHMS"=>$RS['LAST_DTHMS'],
				"WRT_USERID"=>$RS['WRT_USERID'],
				"WRT_USER_NM"=>$RS['WRT_USER_NM']
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
	// function getData($ARR) {
	// 	$iSQL = "SELECT 
	// 				`NSC_CONSIGN_PARTS`.`NCC_ID`,
	// 				`NSC_CONSIGN_PARTS`.`NCC_NM`,
	// 				`NSC_CONSIGN_PARTS`.`NCP_ORDER_NUM`,
	// 				`NSC_CONSIGN_PARTS`.`NCC_USE_YN`,
	// 				`NSC_CONSIGN_PARTS`.`WRT_DTHMS`,
	// 				`NSC_CONSIGN_PARTS`.`LAST_DTHMS`,
	// 				`NSC_CONSIGN_PARTS`.`WRT_USERID`
	// 			FROM `NT_SALES`.`NSC_CONSIGN_PARTS`
	// 			WHERE NCC_ID = '".addslashes($ARR['NCC_ID'])."'
	// 			"
	// 	;

	// 	$RS= DBManager::getRecordSet($iSQL);

	// 	if(empty($RS)) {
	// 		unset($RS);
	// 		return HAVE_NO_DATA;
	// 	} else {
	// 		return $RS;
	// 	}
		
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
							`NCP_ID`
						FROM `NT_SALES`.`NSC_CONSIGN_PARTS`
						WHERE NCP_ID = '".addslashes($ARR['NCP_ID'])."'		;
			";
			$RS= DBManager::getRecordSet($iSQL);
			if(!empty($RS)) {
				return CONFLICT;
			}

			if($ARR['IS_EXIST_ORDER_NUM']==''){
				/** 입력요청한 출력순서가 존재하는지 */
				$iSQL = "SELECT 
							`NCP_ORDER_NUM`
						FROM `NT_SALES`.`NSC_CONSIGN_PARTS`
						WHERE NCP_ORDER_NUM = '".addslashes($ARR['NCP_ORDER_NUM'])."' 
						;"
				;

				$RS= DBManager::getRecordSet($iSQL);
				if(!empty($RS)) {
					return ERROR;
				}
			}

			/** 출력순서 업데이트 */
			$iSQL = "UPDATE  `NT_SALES`.`NSC_CONSIGN_PARTS` 
					SET 
						`NCP_ORDER_NUM` = `NCP_ORDER_NUM`+1
					WHERE `NCP_ORDER_NUM` IN
						(
							SELECT `NCP_ORDER_NUM` FROM 
							(
								SELECT
									@rownum:=@rownum+1 AS IDX,
									`NCP_ORDER_NUM`,
									`NCP_ORDER_NUM` - @rownum AS GRP
								FROM `NT_SALES`.`NSC_CONSIGN_PARTS`, (SELECT @rownum:=0) IDX
								WHERE 1=1
								AND `NCP_ORDER_NUM` >= '".addslashes($ARR['NCP_ORDER_NUM'])."' ORDER BY NCP_ORDER_NUM ASC 
								) AS CMNT WHERE GRP = '".addslashes($ARR['NCP_ORDER_NUM'])."'-1
						)
					;"
			;

			$Result = DBManager::execQuery($iSQL);

			/** insert*/
			if($Result){
				
				$iSQL = "INSERT INTO `NT_SALES`.`NSC_CONSIGN_PARTS`
						(`NCP_ID`,
						`NCC_ID`,
						`NCP_NM`,
						`NCP_MANUFACTURER`,
						`NCP_PRICE`,
						`NCP_USE_YN`,
						`NCP_ORDER_NUM`,
						`WRT_DTHMS`,
						`LAST_DTHMS`,
						`WRT_USERID`)
						VALUES
						(
							'".addslashes($ARR['NCP_ID'])."',
							'".addslashes($ARR['NCC_ID'])."',
		 					'".addslashes($ARR['NCP_NM'])."',
		 					'".addslashes($ARR['NCP_MANUFACTURER'])."',
		 					'".addslashes($ARR['NCP_PRICE'])."',
		 					'".addslashes($ARR['NCP_USE_YN'])."',
		 					'".addslashes($ARR['NCP_ORDER_NUM'])."',
		 					NOW(),
		 					NOW(),
		 					'".addslashes($_SESSION['USERID'])."'
						);
				";
				return DBManager::execQuery($iSQL);
			}else{
				return SERVER_ERROR;
			}
			

		}else{
			/** 코드 존재여부
			 * 등록일 경우에는 전체에서 존재하는지
			 */
			$iSQL = "SELECT 
							`NCP_ID`
						FROM `NT_SALES`.`NSC_CONSIGN_PARTS`
						WHERE NCP_ID = '".addslashes($ARR['NCP_ID'])."'	
						AND `NCP_ID` != '".addslashes($ARR['ORI_NCP_ID'])."' 	;
			";
			$RS= DBManager::getRecordSet($iSQL);
			if(!empty($RS)) {
				return CONFLICT;
			}

			/** 부품코드 존재여부
			 * 수정 경우에는 전체에서 본인제외하고 동일한게 있는지
			 */
			if($ARR['IS_EXIST_ORDER_NUM']==''){
				/** 입력요청한 출력순서가 존재하는지 */
				$iSQL = "SELECT 
							`NCP_ORDER_NUM`
						FROM `NT_SALES`.`NSC_CONSIGN_PARTS`
						WHERE NCP_ORDER_NUM = '".addslashes($ARR['NCP_ORDER_NUM'])."' 
						AND `NCP_ID` != '".addslashes($ARR['ORI_NCP_ID'])."' 
						;"
				;
				// echo $iSQL;
				$RS= DBManager::getRecordSet($iSQL);
				if(!empty($RS)) {
					return ERROR;
				}
			 }
			
			
			$Result = true;
				
			if($ARR['NCP_ORDER_NUM'] != $ARR['ORI_NCP_ORDER_NUM'] ){
				$iSQL = "UPDATE  `NT_SALES`.`NSC_CONSIGN_PARTS` 
						SET 
							`NCP_ORDER_NUM` = `NCP_ORDER_NUM`+1
						WHERE `NCP_ORDER_NUM` IN
							(
								SELECT `NCP_ORDER_NUM` FROM 
								(
									SELECT
										@rownum:=@rownum+1 AS IDX,
										`NCP_ORDER_NUM`,
										`NCP_ORDER_NUM` - @rownum AS GRP
									FROM `NT_SALES`.`NSC_CONSIGN_PARTS`, (SELECT @rownum:=0) IDX
									WHERE 1=1
									AND `NCP_ORDER_NUM` >= '".addslashes($ARR['NCP_ORDER_NUM'])."' ORDER BY NCP_ORDER_NUM ASC 
									) AS CMNT WHERE GRP = '".addslashes($ARR['NCP_ORDER_NUM'])."'-1
							)
						;"
				;
				$Result = DBManager::execQuery($iSQL);
			}

			if($Result){
				$iSQL = "UPDATE  `NT_SALES`.`NSC_CONSIGN_PARTS`
						 SET 
						 	`NCP_ID` = '".addslashes($ARR['NCP_ID'])."',
						 	`NCC_ID` = '".addslashes($ARR['NCC_ID'])."',
						 	`NCP_NM` = '".addslashes($ARR['NCP_NM'])."',
						 	`NCP_MANUFACTURER` = '".addslashes($ARR['NCP_MANUFACTURER'])."',
						 	`NCP_PRICE` = '".addslashes($ARR['NCP_PRICE'])."',
		 					`NCP_USE_YN` = '".addslashes($ARR['NCP_USE_YN'])."',
		 					`NCP_ORDER_NUM`= '".addslashes($ARR['NCP_ORDER_NUM'])."',
		 					`LAST_DTHMS` = now(),
		 					`WRT_USERID` = '".addslashes($_SESSION['USERID'])."'
						WHERE `NCP_ID`='".addslashes($ARR['ORI_NCP_ID'])."' 
						;"
				;
				return DBManager::execQuery($iSQL);
			}else{
				return SERVER_ERROR;
			}
		
		}	
	}

	function addStock($ARR){
		$iSQL = "INSERT INTO `NT_SALES`.`NSC_CONSIGN_PARTS_DTL`
					(`NCP_ID`,
					`NCPD_ID`,
					`NCPD_STOCK_DT`,
					`RCS_ID`,
					`RCP_ID`,
					`WRT_DTHMS`,
					`LAST_DTHMS`,
					`WRT_USERID`)
					VALUES ";
	

		/* 입고여부 확인 */
		$chSQL = "SELECT 
						`NCP_ID`
					FROM `NT_SALES`.`NSC_CONSIGN_PARTS_DTL`
					WHERE NCP_ID = '".addslashes($ARR['STOCK_NCP_ID'])."'	
					AND  NCPD_STOCK_DT = '".addslashes($ARR['NCPD_STOCK_DT'])."'	
					AND	NCPD_ID =  ".((int)addslashes($ARR['STOCK_NCP_CNT'])+1)."	;
		";
		// echo $chSQL;
		$RS= DBManager::getRecordSet($chSQL);
		if(!empty($RS)) {
			return CONFLICT;
		}
		$iNCPD_ID = (int)$ARR['STOCK_NCP_CNT']+1 ; 
		for ($i=1; $i<=$ARR['NCPD_CNT']; $i++) {
			$dataSQL .= "\n(
						'".addslashes($ARR['STOCK_NCP_ID'])."',
						$iNCPD_ID,
						'".addslashes($ARR['NCPD_STOCK_DT'])."',
						'70110',
						'70210',
						NOW(),
						NOW(),
						'".addslashes($_SESSION['USERID'])."'
					),";
			$iNCPD_ID++;
		}
		
		// echo $iSQL.substr($dataSQL, 0, -1).";";
		return DBManager::execQuery($iSQL.substr($dataSQL, 0, -1).";");
		
			
	}



}
	// 0. 객체 생성
	$cPurchase = new cPurchase(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$cPurchase->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
