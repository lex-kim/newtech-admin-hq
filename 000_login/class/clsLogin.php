<?php
class cLogin extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	private $valKeyword;

	// Class Object Constructor
	function cLogin($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
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
			$id = addslashes($ARR['userid']);
			$pw = addslashes($ARR['passwd']);
		}
		else {
			$id = $ARR['userid'];
			$pw = $ARR['passwd'];
		}

		$iSQL = "SELECT 
					`UID`, `USERID`, `USER_NAME`, `AUTH_LEVEL`, `AUTH_ID` 
		 		FROM `NT_AUTH`.V_AUTH_LOGIN
				   WHERE USERID = '".$id."'	\n
					   AND `PASSWD` = PASSWORD('".$pw."')	\n
				";
		return DBManager::getRecordSet($iSQL);
	}


	/* 노출 로그인팝업 조회 */
	function getNotice(){
		$iSQL = "SELECT 
					CNP.`NP_ID`,
					CNP.`NP_TITLE`,
					CNP.`NP_DTL`,
					CNP.`NP_SHOW_YN`,
					CNP.`WRT_DTHMS`,
					CNP.`LAST_DTHMS`,
					CNP.`WRT_USERID`
				FROM 
					`NT_COMMUNITY`.`NMP_POPUP` CNP
				WHERE CNP.`NP_SHOW_YN` = 'Y' ; 
		";

			return DBManager::getRecordSet($iSQL);
			// $Result= DBManager::getRecordSet($iSQL);
			// $rtnArray = array();
			// while($RS = $Result->fetch_array()){
			// 	array_push($rtnArray,
			// 		array(
			// 			"NP_ID"=>$RS['NP_ID'],
			// 		"NP_TITLE"=>$RS['NP_TITLE'],
			// 		"NP_DTL"=>$RS['NP_DTL'],
			// 		"NP_SHOW_YN"=>$RS['NP_SHOW_YN'],
			// 		"WRT_DTHMS"=>$RS['WRT_DTHMS'],
			// 		"LAST_DTHMS"=>$RS['LAST_DTHMS'],
			// 		"WRT_USERID"=>$RS['WRT_USERID']
			// 	));
			// }

			// $Result->close();  // 자원 반납
			// unset($RS);

			// if(empty($rtnArray)) {
			// 	return HAVE_NO_DATA;
			// } else {
			// 	return $rtnArray;
			// }
		
	}


	// End of Class Object
}
	// 0. 객체 생성
	$cLogin = new cLogin(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$cLogin->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
