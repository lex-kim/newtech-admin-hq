<?php

class cBundle extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	function cBundle($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
		$this->DATA_SAVE_PATH = $DATA_SAVE_PATH;
		$this->ALLOW_FILE_EXT = $ALLOW_FILE_EXT;
	}



	/**
	 * 조회 
	 * @param {Array} $ARR 
	 */
	function getList($ARR) {
		$LimitStart = ($ARR['CURR_PAGE']-1) * $ARR['PER_PAGE'];
		$LimitEnd   = $ARR['PER_PAGE'];

		// 페이징처리를 위해 전체데이터
		$iSQL = "
				SELECT
					COUNT(*) AS CNT
				FROM
					NT_BILL.NBB_BATCH ;
			";
			
		try{
			$RS_T	= DBManager::getRecordSet($iSQL);
		} catch (Exception $e){
			echo "error : ".$e;
			return ERROR_DB_SELECT_DATA;  
		}

		$TOTAL_LIST_COUNT = $RS_T['CNT'];         // 총 몇개인지
		$IDX = $RS_T['CNT']-$LimitStart;          // 해당페이지 index
		unset($RS_T);

		// 리스트 가져오기
		$iSQL = "SELECT
                    `NH_DTHMS_ID`,
					`NH_TOTAL_CNT`,
					`NH_PROC_CNT`,
					`WRT_DTHMS`,
					`LAST_DTHMS`,
					IFNULL(NT_AUTH.GET_USER_NM(WRT_USERID), '') AS `USER_NM`,
					`WRT_USERID`
                FROM NT_BILL.NBB_BATCH
                ORDER BY NH_DTHMS_ID DESC
				LIMIT $LimitStart,$LimitEnd ;
				
		";
	    // echo $iSQL;
		$Result= DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
				"IDX"=>$IDX--,
				"NH_DTHMS_ID"=>$RS['NH_DTHMS_ID'],
				"NH_TOTAL_CNT"=>$RS['NH_TOTAL_CNT'],
				"NH_PROC_CNT"=>$RS['NH_PROC_CNT'],
				"USER_NM"=>$RS['USER_NM'],
				"WRT_USERID"=>$RS['WRT_USERID']
			));
		}
		$Result->close();  
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
    
	function getDetailList($ARR) {
		$LimitStart = ($ARR['CURR_PAGE']-1) * $ARR['PER_PAGE'];
		$LimitEnd   = $ARR['PER_PAGE'];
		/** 전체리스트가 아닌경우에만 limitSql 적용 */
		$LIMIT_SQL = "";
		if($ARR['REQ_MODE'] == CASE_LIST_SUB){
			$LIMIT_SQL = "LIMIT $LimitStart,$LimitEnd";
		}
		
		if($ARR['START_DT_SEARCH'] != '' || $ARR['END_DT_SEARCH'] != '') {
			if($ARR['START_DT_SEARCH'] !='' && $ARR['END_DT_SEARCH'] !=''){
				$subWhere .= "AND DATE(`NB`.`NB_CLAIM_DT`) BETWEEN '".addslashes($ARR['START_DT_SEARCH'])."' AND '".addslashes($ARR['END_DT_SEARCH'])."'";
			}else if($ARR['START_DT_SEARCH'] !='' && $ARR['END_DT_SEARCH'] ==''){
				$subWhere .= "AND DATE(`NB`.`NB_CLAIM_DT`) >= '".addslashes($ARR['START_DT_SEARCH'])."'";
			}else{
				$subWhere .= "AND DATE(`NB`.`NB_CLAIM_DT`) <= '".addslashes($ARR['END_DT_SEARCH'])."'";
			}
		} 

		if($ARR['NBI_REGI_NUM_SEARCH'] != ''){
			$sqlWhere .= "AND INSTR(NBD.`NHD_REGI_NUM`, '".addslashes($ARR['NBI_REGI_NUM_SEARCH'])."' ) > 0\n";
		}
		if($ARR['NB_CAR_NUM_SEARCH'] != ''){ 
			$sqlWhere .= "AND INSTR(`NHD_CAR_NUM`, '".addslashes($ARR['NB_CAR_NUM_SEARCH'])."' ) > 0\n";
		}

		if($ARR['DEPOSIT_YN_SEARCH'] != '') {
			if($ARR['DEPOSIT_YN_SEARCH'] == YN_Y){
				$subWhere .= "AND NBI.RDET_ID IN('40122','40123','40124') ";	
			}else if($ARR['DEPOSIT_YN_SEARCH'] == YN_N){
				$subWhere .= "AND NBI.RDET_ID = '40121' ";	
			}
		} 

		//입금여부상세
		if($ARR['DEPOSIT_DETAIL_YN_SEARCH'] != '') {
			$subWhere .= "AND NBI.RDT_ID IN (";
            foreach ($ARR['DEPOSIT_DETAIL_YN_SEARCH'] as $index => $RDET_ARRAY) {
                $subWhere .=" '".$RDET_ARRAY."',";
            }
            $subWhere=substr($subWhere, 0, -1); // 마지막 콤마제거
            $subWhere .= ") \n";
		} 

		// 페이징처리를 위해 전체데이터
		$iSQL = "
				SELECT
					COUNT(*) AS CNT
				FROM
					NT_BILL.NBB_BATCH_DETAIL
				WHERE NH_DTHMS_ID = '".addslashes($ARR['NH_DTHMS_ID'])."' ;
			";
			
		try{
			$RS_T	= DBManager::getRecordSet($iSQL);
		} catch (Exception $e){
			echo "error : ".$e;
			return ERROR_DB_SELECT_DATA;  
		}

		$TOTAL_LIST_COUNT = $RS_T['CNT'];         // 총 몇개인지
		$IDX = $RS_T['CNT']-$LimitStart;          // 해당페이지 index
		unset($RS_T);

		$NB_RATIO_PRICE = "TRUNCATE(IFNULL(NB.`NB_TOTAL_PRICE`,'0')*IF(NBI.`NBI_RATIO`<>'',CAST(NBI.`NBI_RATIO` AS DECIMAL)/100,1),-1)" ; 

		// 리스트 가져오기
		$iSQL = "SELECT 
					NBD.`NH_DTHMS_ID`,
					NBD.`NHD_INSU_NM`,
					NBD.`NHD_REGI_NUM`,
					NBD.`NHD_CAR_NUM`,
					NBD.`NHD_DEPOSIT_DT`,
					NBD.`NHD_DEPOSIT_PRICE`,
					NBD.`NB_ID`,
					NBD.`NBI_RMT_ID`,
					NBD.`WRT_DTHMS`,
					NBD.`LASR_DTHMS`,
					NBD.`WRT_USERID`,
					(SELECT CONCAT(NB.`NB_ID` , 
							'|', NB.`NB_CLAIM_DT` , 
							'|', NBI.`MII_ID`, 
							'|', `NT_MAIN`.`GET_INSU_NM`(NBI.`MII_ID`) ,
							'|', NBI.`NBI_REGI_NUM`, 
							'|', NB.RLT_ID,
							'|', NT_CODE.GET_CODE_NM(NB.RLT_ID) , 
							'|', NB.`NB_CAR_NUM`,
							'|', $NB_RATIO_PRICE,
							'|', `NT_BILL`.`GET_DEPOSIT_YN`(NBI.RDET_ID), 
							'|', RDET_ID,
							'|', NT_CODE.GET_CODE_NM(NBI.`RDET_ID`), 
							'|', NBI.NBI_RMT_ID,
                            '|',IF(NB.NB_VAT_DEDUCTION_YN = 'N',1,0.9),
                            '|',IF(NB.NB_NEW_INSU_YN = 'N',1,0.8),
							'|',IFNULL(NBI.NBI_DEPOSIT_EXTRA1 ,'')
							) 
						FROM `NT_BILL`.`NBB_BILL` NB,
						`NT_BILL`.`NBB_BILL_INSU` NBI
						WHERE NB.`NB_ID` = NBI.`NB_ID`
						AND NB.DEL_YN = 'N' 
						AND NB.NB_ID = NBD.`NB_ID`
                        AND NBI.NBI_RMT_ID = NBD.`NBI_RMT_ID`
					) AS 'DEPOSIT_NB',
					(SELECT GROUP_CONCAT(NB.`NB_ID` , 
							'|', NB.`NB_CLAIM_DT` , 
							'|', NBI.`MII_ID`, 
							'|', `NT_MAIN`.`GET_INSU_NM`(NBI.`MII_ID`) ,
							'|', NBI.`NBI_REGI_NUM`, 
							'|', NB.RLT_ID,
							'|', NT_CODE.GET_CODE_NM(NB.RLT_ID) , 
							'|', NB.`NB_CAR_NUM`,
							'|', $NB_RATIO_PRICE,
							'|', `NT_BILL`.`GET_DEPOSIT_YN`(NBI.RDET_ID), 
							'|', RDET_ID,
							'|', NT_CODE.GET_CODE_NM(NBI.`RDET_ID`), 
							'|', NBI.NBI_RMT_ID,
                            '|',IF(NB.NB_VAT_DEDUCTION_YN = 'N',1,0.9),
                            '|',IF(NB.NB_NEW_INSU_YN = 'N',1,0.8),
							'|',IFNULL(NBI.NBI_DEPOSIT_EXTRA1 ,'')
							) 
						FROM `NT_BILL`.`NBB_BILL` NB,
						`NT_BILL`.`NBB_BILL_INSU` NBI
						WHERE NB.`NB_ID` = NBI.`NB_ID`
						AND NB.DEL_YN = 'N' 
						AND NBI.RDET_ID = '40121'
						AND RIGHT( NBD.NHD_REGI_NUM, 5) = RIGHT( NBI.`NBI_REGI_NUM`, 5) \n
						AND RIGHT( NBD.`NHD_CAR_NUM`, 4) = RIGHT(NB.`NB_CAR_NUM`, 4)  
						$subWhere
					) AS 'NB_INFO',
					( SELECT
							COUNT(*) AS 'NB_CNT'
						FROM `NT_BILL`.`NBB_BILL` NB,
						`NT_BILL`.`NBB_BILL_INSU` NBI
						WHERE NB.`NB_ID` = NBI.`NB_ID`
						AND NB.DEL_YN = 'N'  
						AND NBI.RDET_ID = '40121'
						AND RIGHT( NBD.NHD_REGI_NUM, 5) = RIGHT( NBI.`NBI_REGI_NUM`, 5) \n
						AND RIGHT( NBD.`NHD_CAR_NUM`, 4) = RIGHT(NB.`NB_CAR_NUM`, 4) 
						$subWhere
					) AS 'NB_CNT'
				FROM `NT_BILL`.`NBB_BATCH_DETAIL` NBD
				WHERE NH_DTHMS_ID = '".addslashes($ARR['NH_DTHMS_ID'])."'
				$sqlWhere 
				ORDER BY NH_DTHMS_ID DESC
				$LIMIT_SQL ;
		";
	    // echo "detailList>\n".$iSQL;
		$Result= DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
				"IDX"=>$IDX--,
				"NH_DTHMS_ID"=>$RS['NH_DTHMS_ID'],
				"NHD_INSU_NM"=>$RS['NHD_INSU_NM'],
				"NHD_REGI_NUM"=>$RS['NHD_REGI_NUM'],
				"NHD_CAR_NUM"=>$RS['NHD_CAR_NUM'],
				"NHD_DEPOSIT_DT"=>$RS['NHD_DEPOSIT_DT'],
				"NHD_DEPOSIT_PRICE"=>$RS['NHD_DEPOSIT_PRICE'],
				"NB_ID"=>$RS['NB_ID'],
				"NBI_RMT_ID"=>$RS['NBI_RMT_ID'],
				"DEPOSIT_NB"=>$RS['DEPOSIT_NB'],
				"NB_INFO"=>$RS['NB_INFO'],
				"NB_CNT"=>$RS['NB_CNT'],
				"WRT_DTHMS"=>$RS['WRT_DTHMS'],
				"LASR_DTHMS"=>$RS['LASR_DTHMS'],
				"WRT_USERID"=>$RS['WRT_USERID']
			));
		}
		$Result->close();  
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
	 * 데이터생성
	 */
	function setData($ARR,$PDS) {
		
		$targetDir = $this->DATA_SAVE_PATH.date("Ymd");
		echo "targetDir".$targetDir;
		if(!is_dir($targetDir)) {	echo "make dir"; mkdir($targetDir);		}
		if($PDS['UPLOAD_CONFIRM']['name'] != "") {
            $ext = pathinfo($PDS['UPLOAD_CONFIRM']['name'], PATHINFO_EXTENSION);
			if(strtolower($ext) == "csv" ) {
				$tmpFilename	= basename($PDS['UPLOAD_CONFIRM']['tmp_name']);	// 파일명 획득
				$upload_Target = $targetDir."/".$tmpFilename;             // 첨부파일 경로 (FULL PATH)
				if(file_exists($upload_Target)) {
					// 기존파일 존재시 삭제
					unlink($upload_Target);
				}
				echo "\n"."upload Target".$upload_Target;
				echo "				>".$PDS['UPLOAD_CONFIRM']['tmp_name'];
				if(!move_uploaded_file($PDS['UPLOAD_CONFIRM']['tmp_name'],$upload_Target)){
					 return ERROR_MOVE_FILE; // 파일카피 실패
				} else {
					chmod($upload_Target, 0777);
					echo "[".date("Y-m-d H:i:s")."] 파일 업로드 완료<br/>";
				}
			} else {
				echo "[".date("Y-m-d H:i:s")."] [오류] 파일 업로드 실패 - 올바르지 않은 확장자 - CSV파일만 업로드 가능합니다<br/>";
				return ERROR_NOT_ALLOWED_FILE;
			}
		} else {
			echo "[".date("Y-m-d H:i:s")."] [오류] 파일 업로드 실패 - 첨부 파일 없음<br/>";
			return ERROR_NO_FILE;
		}
		// echo $this->getFileQuery($ARR, $upload_Target);
		// 빌키정보 DB 저장
		if(DBManager::execMulti($this->getFileQuery($ARR, $upload_Target))) {
			echo "[".date("Y-m-d H:i:s")."] DB 저장 완료<br/>";
			return OK;
		} else {
			// 빌키정보 DB 저장 실패
			echo "[".date("Y-m-d H:i:s")."] [오류] DB 저장 실패<br/>";
			return ERROR_DB_INSERT_DATA;
		}
		
		return $rtnValue;
    }

    function getFileQuery($ARR,$filename) {
        $fp = fopen($filename, "r") or die("파일열기에 실패하였습니다");
		// echo "fp>>"."\n".var_dump($fp);
		$TOTAL_UPLOAD_CNT = 0;
		$DTHMS_ID = date("Y-m-d H:i:s");
		$preSQL = "INSERT INTO `NT_BILL`.`NBB_BATCH_DETAIL`
					(`NH_DTHMS_ID`,
					`NHD_INSU_NM`,
					`NHD_REGI_NUM`,
					`NHD_CAR_NUM`,
					`NHD_DEPOSIT_DT`,
					`NHD_DEPOSIT_PRICE`,
					`WRT_DTHMS`,
					`LASR_DTHMS`,
					`WRT_USERID`)
					VALUES";
        while(!feof($fp)){
			
            $row = fgets($fp);
			$data = explode(",",$row);
			$data[4] = preg_replace("/\s+/", "", $data[4]);
			if($data[4] > 0){
				$TOTAL_UPLOAD_CNT ++ ;
				$iSQL .= $preSQL."('".$DTHMS_ID."','".$data[0]."', '".$data[1]."','".$data[2]."','".$data[3]."','".$data[4]."',NOW(),NOW(),'".addslashes($_SESSION['USERID'])."'); ";
			}
		}
		$mainSQL = "INSERT INTO `NT_BILL`.`NBB_BATCH`
					(`NH_DTHMS_ID`,
					`NH_TOTAL_CNT`,
					`NH_PROC_CNT`,
					`WRT_DTHMS`,
					`LAST_DTHMS`,
					`WRT_USERID`)
					VALUES
					('".$DTHMS_ID."',
					$TOTAL_UPLOAD_CNT,
					0,
					NOW(),
					NOW(),
					'".addslashes($_SESSION['USERID'])."');
					";
        fclose($fp);
        // echo $iSQL;
        return  $mainSQL.$iSQL;
    }
	


	function setDeposit($ARR) {
		// echo var_dump($ARR);
		$index = $ARR['DATA_ARRAY'];
		
		/* 감액여부 확인 */
		$NB_INFO = explode('|',$index['NB_INFO']);
		// echo ">>>>>".var_dump($NB_INFO);
		// echo ">>>>>".$NB_INFO[8]." ".$NB_INFO[13]." ".$NB_INFO[14];
		// echo ">>>>>+++++++++".$NB_INFO[8]*$NB_INFO[13]*$NB_INFO[14];
		
		$BILL_PRICE = $NB_INFO[8]*$NB_INFO[13]*$NB_INFO[14]; // NB_TOTAL_PRICE* 공제 * 신보
		$DEPOSIT_PRICE =  $index['NHD_DEPOSIT_PRICE'];
		$DC_RATIO = ROUND((1-($DEPOSIT_PRICE/$BILL_PRICE))*100,1);

		
		if($index['NB_ID'] != '' && $index['NBI_RMT_ID'] != ''){
			if($DC_RATIO >= $ARR['CUR_DECREMENT_RATIO']){
				// echo "감액사유 요청 입금처리";
				$iSQL = "UPDATE `NT_BILL`.`NBB_BILL_INSU`
						SET `RDT_ID` = '40112',
						`RDET_ID` = '40123',
						`NBI_INSU_DEPOSIT_PRICE` = '".addslashes($index['NHD_DEPOSIT_PRICE'])."',
						`NBI_INSU_DEPOSIT_DT` = '".addslashes($index['NHD_DEPOSIT_DT'])."',	
						`NBI_REQ_REASON_YN` = 'Y',
						`NBI_REQ_REASON_DTHMS` = NOW(),
						`NBI_REQ_REASON_USERID` = 'SYSTEM',
						`NBI_DEPOSIT_EXTRA1` = CONCAT('".$NB_INFO[15]."BUNDLE:".addslashes($index['NHD_DEPOSIT_DT'])." ".addslashes($index['NHD_DEPOSIT_PRICE'])." ".$DC_RATIO."% ',NOW(),' ".$_SESSION['USERID']."', '/ '), 
						`LAST_DTHMS` = NOW(),
						`WRT_USERID` = '".$_SESSION['USERID']."'
						WHERE `NB_ID` = '".addslashes($index['NB_ID'])."' AND `NBI_RMT_ID` = '".addslashes($index['NBI_RMT_ID'])."' ;
				";
			}else {
				// echo "입금처리";
				$iSQL = "UPDATE `NT_BILL`.`NBB_BILL_INSU`
						SET `RDT_ID` = '40111',
						`RDET_ID` = '40123',
						`NBI_INSU_DEPOSIT_PRICE` = '".addslashes($index['NHD_DEPOSIT_PRICE'])."',
						`NBI_INSU_DEPOSIT_DT` = '".addslashes($index['NHD_DEPOSIT_DT'])."',	
						`LAST_DTHMS` = NOW(),
						`WRT_USERID` = '".$_SESSION['USERID']."'
						WHERE `NB_ID` = '".addslashes($index['NB_ID'])."' AND `NBI_RMT_ID` = '".addslashes($index['NBI_RMT_ID'])."' ;
				";
			}
			
			// echo $iSQL;
			if(DBManager::execQuery($iSQL)) {
				$iSQL = "UPDATE `NT_BILL`.`NBB_BATCH`
						SET NH_PROC_CNT = NH_PROC_CNT+1 
						WHERE `NH_DTHMS_ID` = '".addslashes($index['NH_DTHMS_ID'])."' ;
				";

				$iSQL .= "UPDATE `NT_BILL`.`NBB_BATCH_DETAIL`
				SET NB_ID = '".addslashes($index['NB_ID'])."',
					NBI_RMT_ID ='".addslashes($index['NBI_RMT_ID'])."' 
				WHERE `NH_DTHMS_ID` = '".addslashes($index['NH_DTHMS_ID'])."' 
				AND NHD_REGI_NUM = '".addslashes($index['NHD_REGI_NUM'])."';
				";
				// echo $iSQL;
				return DBManager::execMulti($iSQL);
			} else {
				return ERROR_DB_UPDATE_DATA;
			}
		}else {
			return ERROR_DB_UPDATE_DATA;
		}
		
	}
	



}
// 0. 객체 생성
$cBundle = new cBundle(FILE_UPLOAD_PATH."confirm/",ALLOW_FILE_EXT); //서버용
// $cBundle = new cBundle("../../../FILE_DATA/confirm/",ALLOW_FILE_EXT); //로컬용

$cBundle->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
