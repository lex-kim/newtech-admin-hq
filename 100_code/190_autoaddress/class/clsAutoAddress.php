<?php
class cAddress extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	// Class Object Constructor
	function cAddress($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
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

		//성명
		if($ARR['NAA_NM_SEARCH'] !=''){
			$tmpKeyword .= "AND INSTR(CAST(AES_DECRYPT(`NAA_NM`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR),'".addslashes($ARR['NAA_NM_SEARCH'])."')>0 \n";
		}

		//연락처
		if($ARR['NAA_PHONE_SEARCH'] !=''){
			$tmpKeyword .= "AND INSTR(CAST(AES_DECRYPT(`NAA_PHONE`,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR),'".addslashes($ARR['NAA_PHONE_SEARCH'])."')>0 \n";
		}

		// 공업사 검색
        if($ARR['NAA_CPY_SEARCH'] != '') {
            $tmpKeyword .= "AND INSTR(`NAA_CPY`,'".addslashes($ARR['NAA_CPY_SEARCH'])."')>0 \n";	
        } 

		// 역할 검색
        if($ARR['NAA_ROLE_SEARCH'] != '') {
            $tmpKeyword .= "AND INSTR(`NAA_ROLE`,'".addslashes($ARR['NAA_ROLE_SEARCH'])."')>0 \n";	
        } 

		// 페이징처리를 위해 전체데이터
		$iSQL = "SELECT
					COUNT(*) AS CNT
				FROM
					NT_ADDRESS.NA_ADDRESS
				WHERE	
					CAST(AES_DECRYPT(NAA_NM,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) <> ''
				$tmpKeyword ;
		";
	
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

		// 리스트 가져오기
		$iSQL = "SELECT 
					CAST(AES_DECRYPT(NAA_NM,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `NAA_NM`,
					CAST(AES_DECRYPT(NAA_PHONE,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) AS `NAA_PHONE`,
					NAA_CPY,
					NAA_ROLE,
					`LAST_DTHMS`,
					`WRT_DTHMS`
				FROM NT_ADDRESS.NA_ADDRESS
				WHERE 
					CAST(AES_DECRYPT(NAA_NM,UNHEX(SHA2('".AES_SALT_KEY."',256))) AS CHAR) <> ''
					$tmpKeyword 
				ORDER BY NAA_ID DESC 
				$LIMIT_SQL;
				"
		;
		// echo $iSQL."\n";
		$Result= DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
				"IDX"=>$IDX--,
				"NAA_NM"=>$RS['NAA_NM'],
				"NAA_PHONE"=>$RS['NAA_PHONE'],
				"NAA_CPY"=>$RS['NAA_CPY'],
				"NAA_ROLE"=>$RS['NAA_ROLE'],
				"WRT_DTHMS"=>$RS['WRT_DTHMS'],
				"LAST_DTHMS"=>$RS['LAST_DTHMS']
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


}
	// 0. 객체 생성
	$cAddress = new cAddress(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$cAddress->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
