<?php

class clsOperation extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자
	private const WRT_DTHMS_SEARCH = "WRT_DTHMS_SEARCH";          //검색기간타입중 최종적용일
	private const CPPS_APPLY_DT_SEARCH = "CPPS_APPLY_DT_SEARCH";   //검색기산타입중 변동적용일

	// Class Object Constructor
	function clsOperation($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
		$this->DATA_SAVE_PATH = $DATA_SAVE_PATH;
		$this->ALLOW_FILE_EXT = $ALLOW_FILE_EXT;
	}

	/**
	 * 전체데이터 가져오는 함수
	*/
	function getTotalList(){
		$iSQL = "SELECT 
					@rownum:=@rownum+1 AS IDX,
					`CPP_ID`,
					`CPP_PART_TYPE`,
					`CPP_NM`,
					`CPP_NM_SHORT`,
					`CPP_UNIT`,
					`CPP_USE_YN`,
					`CPP_ORDER_NUM`,
					(SELECT COUNT(*) FROM `NT_CODE`.`NCP_PART_SUB` WHERE `CPP_ID`= `CPP_ID`) AS `CPPS_COUNT`,
					`WRT_DTHMS`,
					`LAST_DTHMS`,
					`WRT_USERID`
				FROM `NT_CODE`.`NCP_PART`, (SELECT @rownum:=0) IDX
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
	
		if($ARR['DATE_START'] !='' || $ARR['DATE_END'] !='') {
			if($ARR['DATE_START'] !='' && $ARR['DATE_END'] !=''){
				$tmpKeyword .= " AND DATE(`NCB`.`WRT_DTHMS`) BETWEEN '".addslashes($ARR['DATE_START'])."' AND '".addslashes($ARR['DATE_END'])."'";
			}else if($ARR['DATE_START'] !='' && $ARR['DATE_END'] ==''){
				$tmpKeyword .= " AND DATE(`NCB`.`WRT_DTHMS`) >= '".addslashes($ARR['DATE_START'])."'";
			}else{
				$tmpKeyword .= " AND DATE(`NCB`.`WRT_DTHMS`) <= '".addslashes($ARR['DATE_END'])."'";
			}
			
			
		} 

		if($ARR['NMG_CTGY_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(`NCB`.`MCG_ID`,'".addslashes($ARR['NMG_CTGY_SEARCH'])."')>0 ";
		}
		if($ARR['MGB_TITLE_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(`NCB`.`MGB_TITLE`,'".addslashes($ARR['MGB_TITLE_SEARCH'])."')>0 ";
		} 

		if($ARR['WRITER_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(`NCB`.`WRT_USERID`,'".addslashes($ARR['WRITER_SEARCH'])."')>0 ";
		} 

		// 페이징처리를 위해 전체데이터
		$iSQL = "
				SELECT
					COUNT(`NCB`.`MGB_ID`) AS CNT
				FROM
					`NT_COMMUNITY`.`NMG_BOARD` `NCB`
				WHERE `NCB`.`DEL_YN` = 'N'
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

		// 리스트 가져오기
		$iSQL = "SELECT 
					`NCB`.`MGB_ID`,
					`NCB`.`MCG_ID`,
					`NCC`.`MGC_NM`,
					`NCB`.`MGB_TITLE`,
					`NCB`.`MGB_TXT_DTL`,
					(SELECT COUNT(`MFF_ID`) FROM `NT_COMMUNITY`.`NMF_FILE` WHERE `MFF_BOARD_ID` =  `NCB`.`MGB_ID` AND `DEL_YN`='N') FILE_COUNT,
					`NCB`.`MGB_VIEW_CNT`,
					`NCB`.`WRT_DTHMS`,
					`NCB`.`LAST_DTHMS`,
					`NCB`.`WRT_USERID`
				FROM `NT_COMMUNITY`.`NMG_BOARD` `NCB`,
				    `NT_COMMUNITY`.`NMG_CTGY` `NCC`
				WHERE `NCB`.`DEL_YN` = 'N'
				AND `NCB`.`MCG_ID` = `NCC`.`MGC_ID`
				$tmpKeyword
				ORDER BY `NCB`.`MGB_ID` DESC 
				LIMIT $LimitStart,$LimitEnd;"
		;

		$Result= DBManager::getResult($iSQL);
		///

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
				"IDX"=>$IDX--,
				"MGB_ID"=>$RS['MGB_ID'],
				"MCG_ID"=>$RS['MCG_ID'],
				"MGC_NM"=>$RS['MGC_NM'],
				"MGB_TITLE"=>$RS['MGB_TITLE'],
				"MGB_TXT_DTL"=>$RS['MGB_TXT_DTL'],
				"MGB_VIEW_CNT"=>$RS['MGB_VIEW_CNT'],
				"FILE_COUNT"=>$RS['FILE_COUNT'],
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
	 * 해당데이터 조회 
	 * @param {$ARR}
	 * @param {ALLM_ID : 조회할 해당 데이터 ID}
	 */
	function getData($ARR) {
		/** 조회수 1 업 */
		if($ARR['IS_VIEW'] != '') {
			$iSQL = "UPDATE `NT_COMMUNITY`.`NMG_BOARD`
					SET	`MGB_VIEW_CNT` = `MGB_VIEW_CNT`+1
					WHERE DEL_YN = 'N'
					AND `MGB_ID` = '".addslashes($ARR['MGB_ID'])."'
					"
			;

			$Result = DBManager::execQuery($iSQL);

			if(!$Result){
				return false;
			}
		} 

		$iSQL = "SELECT 
					`NCB`.`MGB_ID`,
					`NCB`.`MCG_ID`,
					`NCB`.`MGB_TITLE`,
					`NCC`.`MGC_NM`,
					`NCB`.`MGB_TXT_DTL`
				FROM `NT_COMMUNITY`.`NMG_BOARD` `NCB`, 
					`NT_COMMUNITY`.`NMG_CTGY` `NCC`
				WHERE `NCB`.DEL_YN = 'N'
				AND `NCB`.`MGB_ID` = '".addslashes($ARR['MGB_ID'])."'
				AND `NCB`.`MCG_ID` = `NCC`.`MGC_ID`
				"
		;

		$RS= DBManager::getRecordSet($iSQL);

		if(empty($RS)) {
			unset($RS);
			return HAVE_NO_DATA;
		} else {
			/** 해당첨부파일 정보 가져오기 */
			$iSQL = "SELECT 
						`MFF_ID`,
						`MFF_ORG_NM`,
						`MFF_FILE_NM`
					FROM `NT_COMMUNITY`.`NMF_FILE`
					WHERE DEL_YN = 'N'
					AND `MFF_BOARD_ID` = '".addslashes($ARR['MGB_ID'])."'
					"
			;
			$Result= DBManager::getResult($iSQL);

			$rtnArray = array();
			while($RS_ = $Result->fetch_array()){
				array_push($rtnArray,
					array(
					"MFF_ID"=>$RS_['MFF_ID'],
					"MFF_FILE_NM"=>$RS_['MFF_FILE_NM'],
					"MFF_ORG_NM"=>$RS_['MFF_ORG_NM']
				));
			}
			$Result->close();  // 자원 반납

			if(empty($rtnArray)) {
				return $RS;
			} else {
				$jsonArray = array();
				array_push($jsonArray,$RS);
				array_push($jsonArray,$rtnArray);
	
				return  $jsonArray;
			}
			
		}
		
	}

	/**
	 * 파일데이터에 insert 해주는 쿼리
	 * @param {$ARR : 게시판 기본정보, $PDS : 파일업로드 정보}
	 */
	function setSqlFileUpload($ARR, $PDS){
		$iSQL = '';
		$FILE_SVR_URL = "/FILE_DATA/board/";
		$SESSION_ID = $_SESSION['USERID'] == null ? 'SYSYTEM' : $_SESSION['USERID'] ;

		$MFF_BOARD_ID =  "";
		if($ARR['REQ_MODE'] == CASE_CREATE){
			$iSQL .= 'SET @last_insert_id = LAST_INSERT_ID();';
			$MFF_BOARD_ID = '@last_insert_id';
		}else{
			$MFF_BOARD_ID = addslashes($ARR['MGB_ID']);
		}

		foreach ($PDS['CR_FILE_FN']['name'] as $index => $file) {
			/** 리스트에서 삭제 안한것만 파일 업로드 가능하게 */
			if($ARR['IS_DEL_FILE'][$index] =='N' && $file != ""){
				$tmpFILENAME = md5(addslashes($SESSION_ID).microtime()).".".$this->getFileEXT($file);
				$tmpFilename = $this->getFile($file, $PDS['CR_FILE_FN']['tmp_name'][$index], "board/".$tmpFILENAME);

				if(!$tmpFilename) {
					return ERROR_NOT_ALLOWED_FILE;	// 파일 업로드 실패
				}else{
					// /** 파일첨부 insert */
					$iSQL .= "INSERT INTO `NT_COMMUNITY`.`NMF_FILE`
							(
								`MFF_TBL_CD`,
								`MFF_BOARD_ID`,
								`MFF_ORG_NM`,
								`MFF_FILE_NM`,
								`MFF_FILE_URI`,
								`WRT_DTHMS`,
								`LAST_DTHMS`,
								`WRT_USERID`,
								`DEL_YN`
							)
							VALUES
							(
								'".TABLE_TYPE_CD_BOARD."',
								".$MFF_BOARD_ID.",
								'".addslashes($file)."',
								'".addslashes($tmpFILENAME)."',
								'board/',
								NOW(),
								NOW(),
								'".addslashes($_SESSION['USERID'])."',
								'N'
							);"
					;
				}
			}
		}

		return $iSQL;
	}

	/**
	 * 데이터생성
	 */
	function setData($ARR, $PDS) {
		
		if($ARR['REQ_MODE'] == CASE_CREATE){
			$iSQL = "INSERT INTO `NT_COMMUNITY`.`NMG_BOARD`
					(
						`MCG_ID`,
						`MGB_TITLE`,
						`MGB_TXT_DTL`,
						`MGB_VIEW_CNT`,
						`WRT_DTHMS`,
						`LAST_DTHMS`,
						`WRT_USERID`,
						`DEL_YN`
					)
					VALUES
					(
						'".addslashes($ARR['MCG_ID'])."',
						'".addslashes($ARR['MGB_TITLE'])."',
						'".addslashes($ARR['MGB_TXT_DTL'])."',
						'".addslashes($ARR['MGB_VIEW_CNT'])."',
						NOW(),
						NOW(),
						'".addslashes($_SESSION['USERID'])."',
						'N'
					);"
			;
			$iSQL .= $this->setSqlFileUpload($ARR, $PDS);    // 첨부파일이 추가로 있는경우 

			
		}else{
			$iSQL = "UPDATE `NT_COMMUNITY`.`NMG_BOARD`
					SET
						`MCG_ID` = '".addslashes($ARR['MCG_ID'])."',
						`MGB_TITLE` = '".addslashes($ARR['MGB_TITLE'])."',
						`MGB_TXT_DTL` = '".addslashes($ARR['MGB_TXT_DTL'])."',
						`LAST_DTHMS` = now(),
						`WRT_USERID` = '".addslashes($_SESSION['USERID'])."'
					WHERE `MGB_ID` = '".addslashes($ARR['MGB_ID'])."'
					;"
			;

			
			$iSQL .= $this->setSqlFileUpload($ARR, $PDS);    // 첨부파일이 추가로 있는경우 

			/** 기존 첨부파일 수정여부 */
			if($ARR['DEL_INDEX'] != null){
				foreach ($ARR['DEL_INDEX'] as $index => $IDX) {
					/** 기존 첨부파일 삭제 여부 */
					if($ARR['IS_DEL_FILE'][$index] =='Y' && $IDX != ''){
						$iSQL .= "UPDATE `NT_COMMUNITY`.`NMF_FILE`
								SET
									`DEL_YN` = 'Y'
								WHERE `MFF_ID` = '".$IDX."'
								;"
						;
					}
				}
			}
			

		}
		return DBManager::execMulti($iSQL);
	}

	function getFile($fileName, $fileTypeName , $targetFilename) {
		if($fileName!= "") {
		 
			$tmpFilename = basename($fileName); // 파일명 획득
			$tmpEXT = $this->getFileEXT($tmpFilename);
		
			if(!preg_match("/".$this->ALLOW_FILE_EXT."/", $tmpEXT)) {
				return false;  //업로드 불가
			}
			/*서버용 업로드 경로 start*/
			$upload_Target = $this->DATA_SAVE_PATH.$targetFilename;             // 서버용 첨부파일 경로 (FULL PATH)
			if(file_exists($upload_Target)) {
			 // 기존파일 존재시 삭제
			 unlink($upload_Target);
			}
			$FileCopy = move_uploaded_file($fileTypeName,$upload_Target);       // 파일 복사하기
			chmod($upload_Target, 0777);
			/*서버용 업로드 경로 end*/
		
			/* 로컬용 업로드 경로 start  */
			// $target_path = "../../../FILE_DATA/";           // 서버용 첨부파일 경로 (FULL PATH)
			// $local_target = $target_path.$targetFilename; 

			// if(file_exists($local_target)) {
			// // 기존파일 존재시 삭제
			// unlink($local_target);
			// }
			// $FileCopy = move_uploaded_file($fileTypeName,$local_target);       // 파일 복사하기
			// chmod($local_target, 0777);
			/* 로컬용 업로드 경로 end  */
		
			if(!$FileCopy){
				return false; // 파일카피 실패
			}
		} else {
			 /* 이미지 업로드 수정 없는 경우 */
			 $tmpFilename = "";
		}
	  
		return true; // 수정이 있으면 파일이름을 ,없으면 ""를 반환
	}

	/**
	 * 삭제
	 * @param {$ARR}
	 * @param {ALLM_ID : 삭제할 해당 데이터 ID}
	 */
	function delData($ARR) {
		$iSQL = "UPDATE `NT_COMMUNITY`.`NMG_BOARD`
					SET  
						`LAST_DTHMS`= NOW(), 
						`DEL_YN`	='Y',
						`WRT_USERID` = '".addslashes($_SESSION['USERID'])."'
					WHERE `MGB_ID`='".addslashes($ARR['MGB_ID'])."'
					;"
		;

		$Result =DBManager::execQuery($iSQL);

		if($Result){
			$iSQL = "UPDATE `NT_COMMUNITY`.`NMF_FILE`
						SET  
							`LAST_DTHMS`= NOW(), 
							`DEL_YN`	='Y',
							`WRT_USERID` = '".addslashes($_SESSION['USERID'])."'
						WHERE `MFF_BOARD_ID`='".addslashes($ARR['MGB_ID'])."'
						;"
			;
			return DBManager::execQuery($iSQL);
		}else{
			return false;
		}
		
	}

	/** 구분타입 가져오기 */
	function getType(){
		/** 구분타입 가져오기 */
		$iSQL = "SELECT 
					`MGC_ID`,
					`MGC_NM`
				FROM `NT_COMMUNITY`.`NMG_CTGY`
				WHERE `DEL_YN` = 'N'
				;"
		;
		// echo $iSQL;
		$Result= DBManager::getResult($iSQL);
		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"MGC_ID"=>$RS['MGC_ID'],
					"MGC_NM"=>$RS['MGC_NM']
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

	/**
	 * 첨부파일 다운로드
	 * @param {$ARR}
	 * @param {FILE_NM : 다운받을 파일명,ORG_NM : 원본파일명}
	 */
	function downFile($ARR) {
		// echo "downFile \n";
		$this->pdsDownload($ARR['FILE_NM'],$ARR['ORG_NM'],"/FILE_DATA/board/");
	}

	/**
	 * 특정 게시글 조회 
	 * @param {$ARR}
	 * @param {CURR_PAGE : 현재페이지, PER_PAGE : 몇개를 조회할 것인지, KEYWORD : 검색조건}
	 */
	function getBoard($ARR) {

		// 리스트 가져오기
		$iSQL = "SELECT 
					`NCB`.`MGB_ID`,
					`NCB`.`MCG_ID`,
					`NCC`.`MGC_NM`,
					`NCB`.`MGB_TITLE`,
					`NCB`.`MGB_TXT_DTL`,
					(SELECT COUNT(`MFF_ID`) FROM `NT_COMMUNITY`.`NMF_FILE` WHERE `MFF_BOARD_ID` =  `NCB`.`MGB_ID` AND `DEL_YN`='N') FILE_COUNT,
					`NCB`.`MGB_VIEW_CNT`,
					`NCB`.`WRT_DTHMS`,
					`NCB`.`LAST_DTHMS`,
					`NCB`.`WRT_USERID`
				FROM `NT_COMMUNITY`.`NMG_BOARD` `NCB`,
					`NT_COMMUNITY`.`NMG_CTGY` `NCC`
				WHERE `NCB`.`DEL_YN` = 'N'
				AND `NCB`.`MCG_ID` = `NCC`.`MGC_ID`
				AND NN.MGB_ID = '".addslashes($ARR['MGB_ID'])."' ; 
				
		";
		// echo $iSQL;
		$Result= DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
				"IDX"=>$IDX--,
				"MNN_ID"=>$RS['MNN_ID'],
				"NOTICE_DT"=>$RS['NOTICE_DT'],
				"MNN_MODE_TGT_CD"=>$RS['MNN_MODE_TGT_CD'],
				"MNN_MODE_TGT_NM"=>$RS['MNN_MODE_TGT_NM'],
				"MII_ID"=>$RS['MII_ID'],
				"MII_NM"=>$RS['MII_NM'],
				"MGG_ID"=>$RS['MGG_ID'],
				"MGG_NM"=>$RS['MGG_NM'],
				"MGG_BIZ_ID"=>$RS['MGG_BIZ_ID'],
				"MNN_TITLE"=>$RS['MNN_TITLE'],
				"MNN_TGT_ID"=>$RS['MNN_TGT_ID'],
				"MNN_ORDER_NUM"=>$RS['MNN_ORDER_NUM'],
				"MNN_TXT_DTL"=>$RS['MNN_TXT_DTL'],
				"MNN_VIEW_CNT"=>$RS['MNN_VIEW_CNT'],
				"MNN_MARK_YN"=>$RS['MNN_MARK_YN'],
				"MMN_START_DT"=>$RS['MMN_START_DT'],
				"MMN_END_DT"=>$RS['MMN_END_DT'],
				"MFF_ID"=>$RS['MFF_ID'],
				"MFF_ORG_NM"=>$RS['MFF_ORG_NM'],
				"MFF_FILE_URI"=>$RS['MFF_FILE_URI'],
				"MFF_FILE_NM"=>$RS['MFF_FILE_NM'],
				"FILE_YN"=>$RS['FILE_YN'],
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
			return  $rtnArray;
		}
		
	}


}
	// 0. 객체 생성
	$clsOperation = new clsOperation(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$clsOperation->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
