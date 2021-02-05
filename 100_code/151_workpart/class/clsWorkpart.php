<?php

class clsWorkpart extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	// Class Object Constructor
	function clsWorkpart($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
		$this->DATA_SAVE_PATH = $DATA_SAVE_PATH;
		$this->ALLOW_FILE_EXT = $ALLOW_FILE_EXT;
	}

	

	/**
	 * 조회 
	 * @param {$ARR}
	 */
	function getList($ARR) {
		
		// 리스트 가져오기
		$iSQL = "SELECT
					*
				FROM
					NT_CODE.NCW_WORK_PART NWP,
					NT_CODE.REF_WORK_PART RWP
				WHERE
					NWP.NWP_ID = RWP.RWP_ID;
				;"
		
		;

		$Result = DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"NWP_ID"=>$RS['NWP_ID'],
					"NWP_ORDER_NUM"=>$RS['NWP_ORDER_NUM'],
					"RWT_ID"=>$RS['RWT_ID'],
					"RWP_ID"=>$RS['RWP_ID'],
					"RWP_NM1"=>$RS['RWP_NM1'],
					"RWP_NM2"=>$RS['RWP_NM2']
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
	function setData($ARR) {
		$SESSION_ID = $_SESSION['USERID'] == null ? 'SYSYTEM' : $_SESSION['USERID'];

		if($ARR['TYPE'] == 'PRIORITY'){
			//  우선순위
			$iSQL = "DELETE 
						FROM `NT_CODE`.`NCW_WORK_PRIORITY`
					WHERE NWP_ID = '".addslashes($ARR['NWP_ID'])."'
					AND RRT_ID = '".addslashes($ARR['RRT_ID'])."'
					AND RWPP_ID = '".addslashes($ARR['RWPP_ID'])."'
					;"
			;
			if($ARR['CPP_ID'] != ''){
				foreach ($ARR['CPP_ID'] as $index => $CPP_ID) {
					$iSQL .= "INSERT INTO `NT_CODE`.`NCW_WORK_PRIORITY`
							(
								`NWP_ID`,
								`CPP_ID`,
								`RRT_ID`,
								`RWPP_ID`,
								`WRT_DTHMS`,
								`LAST_DTHMS`,
								`WRT_USERID`
							)
							VALUES
							(
								'".addslashes($ARR['NWP_ID'])."',
								'".$CPP_ID."',
								'".addslashes($ARR['RRT_ID'])."',
								'".addslashes($ARR['RWPP_ID'])."',
								now(),
								now(),
								'".$SESSION_ID."'
							)
							;"
					;
				}
			}
		}else if(($ARR['TYPE'] == 'REPLACE')){
			// 대체/체화
			$iSQL = "DELETE 
						FROM `NT_CODE`.`NCW_WORK_REPLACE`
					WHERE NWP_ID = '".addslashes($ARR['REPLACE_NWP_ID'])."'
					AND RRT_ID = '".addslashes($ARR['REPLACE_RRT_ID'])."'
					AND RWPP_ID = '".addslashes($ARR['REPLACE_RWPP_ID'])."'
					AND FROM_CPP_ID = '".addslashes($ARR['FROM_CPP_ID'])."'
					;"
			;
			if($ARR['TO_CPP_ID'] != ''){
				foreach ($ARR['TO_CPP_ID'] as $index => $CPP_ID) {
					$iSQL .= "INSERT INTO `NT_CODE`.`NCW_WORK_REPLACE`
							(
								`NWP_ID`,
								`TO_CPP_ID`,
								`FROM_CPP_ID`,
								`RRT_ID`,
								`RWPP_ID`,
								`NWE_RATIO`,
								`WRT_DTHMS`,
								`LAST_DTHMS`,
								`WRT_USERID`
							)
							VALUES
							(
								'".addslashes($ARR['REPLACE_NWP_ID'])."',
								'".$CPP_ID."',
								'".addslashes($ARR['FROM_CPP_ID'])."',
								'".addslashes($ARR['REPLACE_RRT_ID'])."',
								'".addslashes($ARR['REPLACE_RWPP_ID'])."',
								'".addslashes($ARR['NWE_RATIO'][$index])."',
								now(),
								now(),
								'".$SESSION_ID."'
							)
							;"
					;
				}
			}
		}else{
			$COL_NM = '';
			if($ARR['FLOOR_RRT_ID'] == RACON_CHANGE_CD){  //교환
				if($ARR['FLOOR_RWPP_ID'] == WORK_PART_INSIDE_TYPE){  //실내
					$COL_NM = 'NWP_FLOOR_CHANGE_IN';
				}else{    //하체
					$COL_NM = 'NWP_FLOOR_CHANGE_OUT';
				}
			}else{  //판금
				if($ARR['FLOOR_RWPP_ID'] == WORK_PART_INSIDE_TYPE){  //실내
					$COL_NM = 'NWP_FLOOR_SHEET_IN';
				}else{    //하체
					$COL_NM = 'NWP_FLOOR_SHEET_OUT';
				}
			}
			
			$iSQL = "UPDATE `NT_CODE`.`NCW_WORK_PART`
					SET $COL_NM = '".$ARR['NWP_FLOOR_DATA']  ."'
					WHERE NWP_ID = '".addslashes($ARR['FLOOR_NWP_ID'])."'
					;"
			;
		}
		// echo $iSQL;
		return DBManager::execMulti($iSQL);
	}

	/**
	 * 정보가져오기
	 */
	function getData($ARR) {
		// 리스트 가져오기
		if($ARR['TYPE'] == 'PRIORITY'){ // 해당작업항목에 대한 우선 데이터 가져오기
			$iSQL = "SELECT
						NWP.CPP_ID,
						NCP.CPP_NM_SHORT
					FROM
						NT_CODE.NCW_WORK_PRIORITY NWP,
						NT_CODE.NCP_PART NCP
					WHERE
						NCP.CPP_ID = NWP.CPP_ID
					AND	NWP.NWP_ID = '".addslashes($ARR['NWP_ID'])."'
					AND NWP.RRT_ID = '".addslashes($ARR['RRT_ID'])."'
					AND NWP.RWPP_ID = '".addslashes($ARR['RWPP_ID'])."'
					;"

			;

			$Result = DBManager::getResult($iSQL);

			$rtnArray = array();
			while($RS = $Result->fetch_array()){
				array_push($rtnArray,
					array(
						"CPP_ID"=>$RS['CPP_ID'],
						"CPP_NM_SHORT"=>$RS['CPP_NM_SHORT']
				));
			}

			$Result->close();  // 자원 반납
			unset($RS);
			return  $rtnArray;
		}else if($ARR['TYPE'] == 'FLOOR'){  // 해당작업항목에 대한 바닥 데이터 가져오기
			$COL_NM = '';
			if($ARR['RRT_ID'] == RACON_CHANGE_CD){  //교환
				if($ARR['RWPP_ID'] == WORK_PART_INSIDE_TYPE){  //실내
					$COL_NM = 'NWP_FLOOR_CHANGE_IN';
				}else{    //하체
					$COL_NM = 'NWP_FLOOR_CHANGE_OUT';
				}
			}else{  //판금
				if($ARR['RWPP_ID'] == WORK_PART_INSIDE_TYPE){  //실내
					$COL_NM = 'NWP_FLOOR_SHEET_IN';
				}else{    //하체
					$COL_NM = 'NWP_FLOOR_SHEET_OUT';
				}
			}
			$iSQL = "SELECT
						$COL_NM 
					FROM
						NT_CODE.NCW_WORK_PART 
					WHERE
						NWP_ID = '".addslashes($ARR['NWP_ID'])."'
					;"

			;

			$Result = DBManager::getResult($iSQL);
			// echo $Result;
			$rtnArray = array();
			while($RS = $Result->fetch_array()){
				array_push($rtnArray,
					array(
						"NM"=>$RS[$COL_NM ]
				));
			}

			$Result->close();  // 자원 반납
			unset($RS);
			return  $rtnArray;
		}else{                               // 해당 작업항목에 대한 대체/체화 데이터 가져오기
			$iSQL = "SELECT 
						NWR.FROM_CPP_ID, 
						NWR.TO_CPP_ID, 
						NWR.NWE_RATIO, 
						(SELECT CPP_NM_SHORT FROM NT_CODE.NCP_PART WHERE CPP_ID = NWR.FROM_CPP_ID ) AS FROM_CPP_NM_SHORT,
						(SELECT CPP_NM_SHORT FROM NT_CODE.NCP_PART WHERE CPP_ID = NWR.TO_CPP_ID ) AS TO_CPP_NM_SHORT
					FROM NT_CODE.NCW_WORK_REPLACE NWR
					WHERE
						NWR.NWP_ID = '".addslashes($ARR['NWP_ID'])."'
					AND NWR.RRT_ID = '".addslashes($ARR['RRT_ID'])."'
					AND NWR.RWPP_ID = '".addslashes($ARR['RWPP_ID'])."'
					;"

			;
			$Result = DBManager::getResult($iSQL);

			$rtnArray = array();
			while($RS = $Result->fetch_array()){
				array_push($rtnArray,
					array(
						"FROM_CPP_ID"=>$RS['FROM_CPP_ID'],
						"TO_CPP_ID"=>$RS['TO_CPP_ID'],
						"NWE_RATIO"=>$RS['NWE_RATIO'],
						"FROM_CPP_NM_SHORT"=>$RS['FROM_CPP_NM_SHORT'],
						"TO_CPP_NM_SHORT"=>$RS['TO_CPP_NM_SHORT'],
						
				));
			}

			$Result->close();  // 자원 반납
			unset($RS);
			return  $rtnArray;
		}
		
	}

	/**  
	 * 기존부품 정보를 가져온다.
	*/
	function getStdParts($ARR){
		$iSQL = "SELECT 
				`CPP_ID`,
				`CPP_NM`,
				`CPP_NM_SHORT`,
				`CPP_PART_TYPE`

			FROM `NT_CODE`.`NCP_PART` 
			WHERE `DEL_YN`  = 'N' 
			AND `CPP_USE_YN` = '".YN_Y."' 
			AND `CPP_PART_TYPE` != '기타'
			ORDER BY CPP_ORDER_NUM ASC
			;"
		;

		$Result= DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"CPP_ID"=>$RS['CPP_ID'],
					"CPP_NM"=>$RS['CPP_NM'],
					"CPP_NM_SHORT"=>$RS['CPP_NM_SHORT'],
					"CPP_PART_TYPE"=>$RS['CPP_PART_TYPE'],
					
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
	 * 해당 ref 정보를 가져온다.
	*/
	function getRefInfo($ARR){
		$COL_ID = '';
		$COL_NM = '';
		$TABL_NM = '';

		if($ARR['REQ_MODE'] == GET_REF_WORK_PARIORITY_TYPE){
			$TABL_NM = 'NT_CODE.REF_WORK_PART_PRIORITY';
			$COL_ID = 'RWPP_ID';
			$COL_NM = 'RWPP_NM';
		}else if($ARR['REQ_MODE'] == GET_REF_RECKON_TYPE){
			$TABL_NM = 'NT_CODE.REF_RECKON_TYPE';
			$COL_ID = 'RRT_ID';
			$COL_NM = 'RRT_NM';
		}else{
			return ERROR;
		}

		$iSQL = "SELECT 
					$COL_ID,
					$COL_NM
				FROM $TABL_NM
			;"
		;

		$Result= DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"ID"=>$RS[$COL_ID],
					"NM"=>$RS[$COL_NM ],
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
	$clsWorkpart = new clsWorkpart(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$clsWorkpart->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
