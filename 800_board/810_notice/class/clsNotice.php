<?php

class clsNotice extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	// Class Object Constructor
	function clsNotice($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
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

		if($ARR['DT_KEY_SEARCH'] != '') {
			if($ARR['DT_KEY_SEARCH'] == 'WRT_DTHMS'){  // 등록일
                if($ARR['START_DT_SEARCH'] != '' && $ARR['END_DT_SEARCH'] != ''){
                    $tmpKeyword .= "AND NN.WRT_DTHMS BETWEEN '".addslashes($ARR['START_DT_SEARCH'])."' AND '".addslashes($ARR['END_DT_SEARCH'])."' \n";
                }else if($ARR['START_DT_SEARCH'] != '' ){
                    $tmpKeyword .= "AND NN.WRT_DTHMS >= '".$ARR['START_DT_SEARCH']."' \n";
                }else if($ARR['END_DT_SEARCH'] != ''){
                    $tmpKeyword .= "AND NN.WRT_DTHMS <= '".$ARR['END_DT_SEARCH']."' \n";
                }
			}else if($ARR['DT_KEY_SEARCH'] == 'NOTICE_DT'){ // 게시일
				// echo 'notice Dt search!!';
                if($ARR['START_DT_SEARCH'] != '' && $ARR['END_DT_SEARCH'] != ''){
                    $tmpKeyword .= "AND (DATE_FORMAT(NN.MMN_START_DT, '%Y-%m-%d') BETWEEN '".addslashes($ARR['START_DT_SEARCH'])."' AND '".addslashes($ARR['END_DT_SEARCH'])."' AND DATE_FORMAT(NN.MMN_END_DT, '%Y-%m-%d') BETWEEN '".addslashes($ARR['START_DT_SEARCH'])."' AND '".addslashes($ARR['END_DT_SEARCH'])."' )";
                }else if($ARR['START_DT_SEARCH'] != '' ){
                    $tmpKeyword .= "AND DATE_FORMAT(NN.MMN_START_DT, '%Y-%m-%d') >= '".$ARR['START_DT_SEARCH']."' \n";
                }else if($ARR['END_DT_SEARCH'] != ''){
                    $tmpKeyword .= "AND  DATE_FORMAT(NN.MMN_END_DT, '%Y-%m-%d') <= '".$ARR['END_DT_SEARCH']."' \n";
                }
            }
		} 

		if($ARR['SEARCH_MNN_MODE_TGT'] != ''){
			$tmpKeyword .= "AND NN.MNN_MODE_TGT = '".$ARR['SEARCH_MNN_MODE_TGT']."' ";
		}
		if($ARR['SEARCH_TITLE'] != ''){
			$tmpKeyword .= "AND INSTR(NN.MNN_TITLE,'".addslashes($ARR['SEARCH_TITLE'])."')>0 ";
		}
		if($ARR['SEARCH_WRITER'] != ''){
			$tmpKeyword .= "AND INSTR(NN.WRT_USERID,'".addslashes($ARR['SEARCH_WRITER'])."')>0 ";

		}
		if($ARR['SEARCH_FILE'] != ''){
			$tmpKeyword .= "AND INSTR(NF.MFF_ORG_NM ,'".addslashes($ARR['SEARCH_FILE'])."')>0 ";

		}
		if($ARR['SEARCH_GW'] != ''){
			$tmpKeyword .= "AND NG.MGG_ID = '".addslashes($ARR['SEARCH_GW'])."' ";

		}
		if($ARR['SEARCH_IW'] != ''){
			$tmpKeyword .= "AND NI.MII_ID = '".addslashes($ARR['SEARCH_IW'])."' ";

		}
 

		// 페이징처리를 위해 전체데이터
		$iSQL = "SELECT
					COUNT(*) AS CNT
				FROM 
					`NT_COMMUNITY`.`NMN_NOTICE` NN
				LEFT OUTER JOIN  `NT_COMMUNITY`.`NMF_FILE` NF 
					ON MFF_TBL_CD='20110' AND NF.MFF_BOARD_ID= NN.MNN_ID AND NF.DEL_YN ='N'
				LEFT OUTER JOIN `NT_MAIN`.`NMG_GARAGE` NG
					ON NN.MNN_MODE_TGT = '10102' AND NG.MGG_ID = NN.MNN_TGT_ID AND NG.DEL_YN ='N'
                LEFT OUTER JOIN `NT_MAIN`.`NMI_INSU` NI    
					ON NN.MNN_MODE_TGT = '10103' AND NI.MII_ID=  NN.MNN_TGT_ID AND NI.DEL_YN ='N'
				
				WHERE NN.`DEL_YN`='N'
				$tmpKeyword
		";
		// echo $iSQL ;

		try{
			$RS_T	= DBManager::getRecordSet($iSQL);
		} catch (Exception $e){
			echo "error : ".$e;
			return ERROR_DB_SELECT_DATA;   //ERROR_DB_SELECT_DATA
		}

		$TOTAL_LIST_COUNT = $RS_T['CNT'];
		$IDX = $RS_T['CNT']-$LimitStart;
		unset($RS_T);

		// 리스트 가져오기
		$iSQL = "SELECT 
					CONCAT(NN.MMN_START_DT,' ~ ' ,NN.MMN_END_DT) AS 'NOTICE_DT',
					NN.MNN_ORDER_NUM,
					NN.`MNN_ID`,
					NN.`MNN_MODE_TGT` AS 'MNN_MODE_TGT_CD' ,
					NT_CODE.GET_CODE_NM( NN.`MNN_MODE_TGT`) AS 'MNN_MODE_TGT_NM',
					IFNULL(NI.`MII_ID`, '-') AS 'MII_ID',
                    IFNULL(NI.`MII_NM`, '-') AS 'MII_NM',
                    IFNULL(NG.`MGG_ID`, '-') AS 'MGG_ID',
                    IFNULL(NG.`MGG_NM`, '-') AS 'MGG_NM',
                    IFNULL(NG.`MGG_BIZ_ID`, '-') AS 'MGG_BIZ_ID',
					NN.`MNN_TITLE`,
					NN.`MNN_TGT_ID`,
					NN.`MNN_ORDER_NUM`,
					NN.`MNN_TXT_DTL`,
					NN.`MNN_VIEW_CNT`,
					NN.`MNN_MARK_YN`,
					NN.`MMN_START_DT`,
					NN.`MMN_END_DT`,
					NN.`WRT_DTHMS`,
					NN.`LAST_DTHMS`,
					NN.`WRT_USERID`,
					NN.`DEL_YN`,
					IFNULL(NF.MFF_ID, 0) AS 'MFF_ID',
					IFNULL(NF.MFF_ORG_NM, 0) AS 'MFF_ORG_NM',
                    IFNULL(NF.MFF_FILE_URI, 0) AS 'MFF_FILE_URI',
                    IFNULL(NF.MFF_FILE_NM, 0) AS 'MFF_FILE_NM',
    				IF(ISNULL(NF.MFF_ID), 'N', 'Y') AS 'FILE_YN'
				FROM `NT_COMMUNITY`.`NMN_NOTICE` NN
				LEFT OUTER JOIN  `NT_COMMUNITY`.`NMF_FILE` NF 
					ON MFF_TBL_CD='20110' AND NF.MFF_BOARD_ID= NN.MNN_ID AND NF.DEL_YN ='N'
				LEFT OUTER JOIN `NT_MAIN`.`NMG_GARAGE` NG
					ON NN.MNN_MODE_TGT = '10102' AND NG.MGG_ID = NN.MNN_TGT_ID AND NG.DEL_YN ='N'
                LEFT OUTER JOIN `NT_MAIN`.`NMI_INSU` NI    
					ON NN.MNN_MODE_TGT = '10103' AND NI.MII_ID=  NN.MNN_TGT_ID AND NI.DEL_YN ='N'
				WHERE NN.`DEL_YN`='N'
					 $tmpKeyword 
				ORDER BY MNN_ORDER_NUM IS NULL ASC,MNN_ORDER_NUM, WRT_DTHMS DESC
				LIMIT $LimitStart,$LimitEnd
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
			$jsonArray = array();
			array_push($jsonArray,$rtnArray);
			array_push($jsonArray,$TOTAL_LIST_COUNT);

			return  $jsonArray;
		}
		
	}

	/**
	 * 특정 공지사항 조회 
	 * @param {$ARR}
	 * @param {CURR_PAGE : 현재페이지, PER_PAGE : 몇개를 조회할 것인지, KEYWORD : 검색조건}
	 */
	function getNotice($ARR) {

		// 리스트 가져오기
		$iSQL = "SELECT 
					CONCAT(NN.MMN_START_DT,' ~ ' ,NN.MMN_END_DT) AS 'NOTICE_DT',
					NN.MNN_ORDER_NUM,
					NN.`MNN_ID`,
					NN.`MNN_MODE_TGT` AS 'MNN_MODE_TGT_CD' ,
					NT_CODE.GET_CODE_NM( NN.`MNN_MODE_TGT`) AS 'MNN_MODE_TGT_NM',
					IFNULL(NI.`MII_ID`, '-') AS 'MII_ID',
                    IFNULL(NI.`MII_NM`, '-') AS 'MII_NM',
                    IFNULL(NG.`MGG_ID`, '-') AS 'MGG_ID',
                    IFNULL(NG.`MGG_NM`, '-') AS 'MGG_NM',
                    IFNULL(NG.`MGG_BIZ_ID`, '-') AS 'MGG_BIZ_ID',
					NN.`MNN_TITLE`,
					NN.`MNN_TGT_ID`,
					NN.`MNN_ORDER_NUM`,
					NN.`MNN_TXT_DTL`,
					NN.`MNN_VIEW_CNT`,
					NN.`MNN_MARK_YN`,
					NN.`MMN_START_DT`,
					NN.`MMN_END_DT`,
					NN.`WRT_DTHMS`,
					NN.`LAST_DTHMS`,
					NN.`WRT_USERID`,
					NN.`DEL_YN`,
					IFNULL(NF.MFF_ID, 0) AS 'MFF_ID',
					IFNULL(NF.MFF_ORG_NM, 0) AS 'MFF_ORG_NM',
                    IFNULL(NF.MFF_FILE_URI, 0) AS 'MFF_FILE_URI',
                    IFNULL(NF.MFF_FILE_NM, 0) AS 'MFF_FILE_NM',
    				IF(ISNULL(NF.MFF_ID), 'N', 'Y') AS 'FILE_YN'
				FROM `NT_COMMUNITY`.`NMN_NOTICE` NN
				LEFT OUTER JOIN  `NT_COMMUNITY`.`NMF_FILE` NF 
					ON MFF_TBL_CD='20110' AND NF.MFF_BOARD_ID= NN.MNN_ID AND NF.DEL_YN ='N'
				LEFT OUTER JOIN `NT_MAIN`.`NMG_GARAGE` NG
					ON NN.MNN_MODE_TGT = '10102' AND NG.MGG_ID = NN.MNN_TGT_ID AND NG.DEL_YN ='N'
                LEFT OUTER JOIN `NT_MAIN`.`NMI_INSU` NI    
					ON NN.MNN_MODE_TGT = '10103' AND NI.MII_ID=  NN.MNN_TGT_ID AND NI.DEL_YN ='N'
				WHERE NN.`DEL_YN`='N'
				AND NN.MNN_ID = '".addslashes($ARR['MNN_ID'])."' ; 
				
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

	

	/**
	 * 데이터생성
	 */
	function setData($ARR,$PDS) {
		
		if(empty($ARR['MNN_ORDER_NUM']) || $ARR['MNN_ORDER_NUM'] == 0){
			$ARR['MNN_ORDER_NUM'] = NULL ;
			$ARR['MNN_MARK_YN'] = 'N';
		} else {
			$ARR['MNN_MARK_YN'] = 'Y';
			/** 출력순서 업데이트 */
			$iSQL = "UPDATE  `NT_COMMUNITY`.`NMN_NOTICE` 
					SET 
						`MNN_ORDER_NUM` = `MNN_ORDER_NUM`+1
					WHERE `MNN_ORDER_NUM` IN
						(
							SELECT `MNN_ORDER_NUM` FROM 
							(
								SELECT
									@rownum:=@rownum+1 AS IDX,
									`MNN_ORDER_NUM`,
									`MNN_ORDER_NUM` - @rownum AS GRP
								FROM `NT_COMMUNITY`.`NMN_NOTICE`, (SELECT @rownum:=0) IDX
								WHERE `DEL_YN` = 'N' 
								AND `MNN_ORDER_NUM` >= '".addslashes($ARR['MNN_ORDER_NUM'])."' ORDER BY MNN_ORDER_NUM ASC 
								) AS CMNT WHERE GRP = '".addslashes($ARR['MNN_ORDER_NUM'])."'-1
						)
					;"
			;
			DBManager::execQuery($iSQL);
		}

		/* 파일업로드 처리 */
		if(trim($PDS['MFF_FILE_NM']['name']) !== "") {
			$FILE_SVR_URL = "notice/";
			$ORG_NM = $PDS['MFF_FILE_NM']['name'];
			$tmpFILENAME = md5(addslashes($_SESSION['USERID']).microtime()).".".$this->getFileEXT($PDS['MFF_FILE_NM']['name']);
			$tmpFilename = $this->getFile($PDS, "notice/".$tmpFILENAME);
			if(!$tmpFilename) {
					return ERROR_NOT_ALLOWED_FILE;	// 파일 업로드 실패
			}
		} else { $FILE_SVR_URL=""; $tmpFILENAME = ""; }
		
		$rtnValue = ERROR_DB_UPDATE_DATA;
		
		/* 게시글 저장 */
		if($ARR['REQ_MODE'] == CASE_CREATE) {
			$rtnValue = ERROR_DB_INSERT_DATA;
			$iSQL = "INSERT INTO `NT_COMMUNITY`.`NMN_NOTICE`
									(
									`MNN_MODE_TGT`,
									`MNN_TITLE`,
									`MNN_TGT_ID`,
									`MNN_ORDER_NUM`,
									`MNN_TXT_DTL`,
									`MNN_VIEW_CNT`,
									`MNN_MARK_YN`,
									`MMN_START_DT`,
									`MMN_END_DT`,
									`WRT_DTHMS`,
									`LAST_DTHMS`,
									`WRT_USERID`,
									`DEL_YN`
									)
									VALUES
									(
									'".addslashes($ARR['MNN_MODE_TGT'])."',
									'".addslashes($ARR['MNN_TITLE'])."',
									'".addslashes($ARR['MNN_TGT_ID'])."',
									".(empty($ARR['MNN_ORDER_NUM']) ? 'null': $ARR['MNN_ORDER_NUM']).",
									'".addslashes($ARR['MNN_TXT_DTL'])."',
									0,
									'".addslashes($ARR['MNN_MARK_YN'])."',
									'".addslashes($ARR['MMN_START_DT'])."',
									'".addslashes($ARR['MMN_END_DT'])."',
									NOW(),
									NOW(),
									'".addslashes($_SESSION['USERID'])."',
									'N'
									);
						";

		} else {
			$rtnValue = ERROR_DB_UPDATE_DATA;
			/* 수정모드 */
			$iSQL = "UPDATE  `NT_COMMUNITY`.`NMN_NOTICE`
									SET
									`MNN_MODE_TGT` = '".addslashes($ARR['MNN_MODE_TGT'])."',
									`MNN_TITLE` = '".addslashes($ARR['MNN_TITLE'])."',
									`MNN_TGT_ID` = '".addslashes($ARR['MNN_TGT_ID'])."',
									`MNN_ORDER_NUM` = '".addslashes($ARR['MNN_ORDER_NUM'])."',
									`MNN_TXT_DTL` = '".addslashes($ARR['MNN_TXT_DTL'])."',
									`MNN_MARK_YN` = '".addslashes($ARR['MNN_MARK_YN'])."',
									`MMN_START_DT` = '".addslashes($ARR['MMN_START_DT'])."',
									`MMN_END_DT` = '".addslashes($ARR['MMN_END_DT'])."',
									`LAST_DTHMS` = NOW(),
									`WRT_USERID` = '".addslashes($_SESSION['USERID'])."'
									
								WHERE
									`MNN_ID` = '".addslashes($ARR['MNN_ID'])."' ;

					";

		}
		// echo $iSQL."\n";
		if(DBManager::execQuery($iSQL)) {
			$rtnValue = OK;
		}


		if($rtnValue == OK){ //게시글이 정상 등록 후 
			$MNN_ID = $ARR['MNN_ID'] ;
			/* 첨부파일 존재시 첨부파일 저장 */
			if(trim($PDS['MFF_FILE_NM']['name']) !== "" || $ARR['FILE_DEL_YN'] == YN_Y){
				/* 등록일 경우 등록된 MNN_ID 불러오기 */
				if($ARR['REQ_MODE'] == CASE_CREATE){
					$iSQL = "
						SELECT
							MAX(MNN_ID) AS 'MAX_ID'
						FROM 
							`NT_COMMUNITY`.`NMN_NOTICE` NN
						WHERE NN.`DEL_YN`='N'
						$tmpKeyword
					";
					try{
						$RS_T	= DBManager::getRecordSet($iSQL);
					} catch (Exception $e){
						echo "error : ".$e;
						return ERROR_DB_SELECT_DATA;   //ERROR_DB_SELECT_DATA
					}
		
					$MNN_ID = $RS_T['MAX_ID'];
					unset($RS_T);
				}else {
					$MNN_ID = $ARR['MNN_ID'] ;
				} 
				
				$iSQL = "CALL `NT_COMMUNITY`.`SAVE_NOTICE_FILE` 
							(
								'".addslashes($ARR['MFF_ID'])."',
								$MNN_ID,
								'".$ORG_NM."',
								'".$tmpFILENAME."',
								'".$FILE_SVR_URL."',
								'".addslashes($ARR['FILE_DEL_YN'])."',
								'".addslashes($_SESSION['USERID'])."'
							);

				 ";
				 if(DBManager::execQuery($iSQL)) {
					$rtnValue = OK;
				}
				 

			}
			
			
	
		}

		return $rtnValue;
	}



	/* 파일 다운 */
	function getFile($PDS,$targetFilename) {
		$File_date   = date("Ymdhms");
		if($PDS['MFF_FILE_NM']['name'] != "") {
			
			$tmpFilename	= basename($PDS['MFF_FILE_NM']['name']);	// 파일명 획득
			$tmpEXT = $this->getFileEXT($tmpFilename);

			if(!preg_match("/".$this->ALLOW_FILE_EXT."/", $tmpEXT)) {
				return false;		//업로드 불가
			}

			/*서버용 업로드 경로 start*/
			// $upload_Target = $this->DATA_SAVE_PATH.$targetFilename;             // 서버용 첨부파일 경로 (FULL PATH)
			// echo $upload_Target."\n";echo $PDS['MFF_FILE_NM']['tmp_name'];exit();
			// if(file_exists($upload_Target)) {
			// 	// 기존파일 존재시 삭제
			// 	unlink($upload_Target);
			// }
			// $FileCopy = move_uploaded_file($PDS['MFF_FILE_NM']['tmp_name'],$upload_Target);       // 파일 복사하기
			
			// chmod($upload_Target, 0777);
			/*서버용 업로드 경로 end*/

			/* 로컬용 업로드 경로 start  */
			$target_path = $_SERVER['DOCUMENT_ROOT']."/FILE_DATA/";           // 서버용 첨부파일 경로 (FULL PATH)
			$local_target = $target_path.$targetFilename; 
			if(file_exists($local_target)) {
				// 기존파일 존재시 삭제
				unlink($local_target);
			}
			$FileCopy = move_uploaded_file($PDS['MFF_FILE_NM']['tmp_name'],$local_target);       // 파일 복사하기
			chmod($local_target, 0777);
			/* 로컬용 업로드 경로 end  */

			if(!$FileCopy){
			   return false; // 파일카피 실패
			}
		} else {
			/* 이미지 업로드 수정 없는 경우 */
			$tmpFilename = "";
		}

		return true;	// 수정이 있으면 파일이름을 ,없으면 ""를 반환
	}


	/**
	 * data 삭제
	 * @param {$ARR}
	 * @param {ALLM_ID : 삭제할 해당 데이터 ID}
	 */
	function delData($ARR) {
		$iSQL = "UPDATE `NT_COMMUNITY`.`NMN_NOTICE`
					SET  
						`LAST_DTHMS`= NOW(), 
						`DEL_YN`	='Y'
					WHERE `MNN_ID`='".addslashes($ARR['MNN_ID'])."'
		";
// echo 	$iSQL ;
		return DBManager::execQuery($iSQL);
	}


	/**
	 * 첨부파일 다운로드
	 * @param {$ARR}
	 * @param {FILE_NM : 다운받을 파일명,ORG_NM : 원본파일명}
	 */
	function downFile($ARR) {
		// echo "downFile \n";
		$this->pdsDownload($ARR['FILE_NM'],$ARR['ORG_NM'],"/FILE_DATA/notice/");
	}


	/* VIEW_CNT +1 */
	function getData($ARR) {
		$iSQL = "UPDATE `NT_COMMUNITY`.`NMN_NOTICE`
					SET	`MNN_VIEW_CNT` = `MNN_VIEW_CNT`+1
					WHERE DEL_YN = 'N'
					AND `MNN_ID` = '".addslashes($ARR['MNN_ID'])."'
					"
			;

			$return = DBManager::execQuery($iSQL);
	}

	function getMode(){
		$iSQL = "SELECT 
					CONCAT(CCD_PREFIX_CD,CCD_VAL_CD) AS 'CCD_ID',
					CCD_NM
				FROM NT_CODE.NCL_CODE_DIC
				WHERE CCD_DOMAIN = 'MODE_TARGET' 
				ORDER BY CCD_ID ;
		";


		$Result= DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
				"CCD_ID"=>$RS['CCD_ID'],
				"CCD_NM"=>$RS['CCD_NM']
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
	$clsNotice = new clsNotice(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$clsNotice->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
