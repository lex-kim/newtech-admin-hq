<?php

class cVisit extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	function cVisit($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
		$this->DATA_SAVE_PATH = $DATA_SAVE_PATH;
		$this->ALLOW_FILE_EXT = $ALLOW_FILE_EXT;
	}



	/**
	 * 조회 
	 * @param {Array} $ARR 
	 */
	function getList($ARR) {
		// 페이징처리를 위해 전체데이터
		$iSQL = "
				SELECT
					COUNT(*) AS CNT
				FROM
                    NT_MAIN.NMG_GARAGE
				WHERE	
					`DEL_YN` = 'N' ;
			";
			
		try{
			$RS_T	= DBManager::getRecordSet($iSQL);
		} catch (Exception $e){
			echo "error : ".$e;
			return ERROR_DB_SELECT_DATA;  
		}

		$IDX = $RS_T['CNT'];
		unset($RS_T);

		// 리스트 가져오기
		$iSQL = "SELECT
                    NT_CODE.GET_CSD_NM(CSD_ID) AS CSD_NM, 
                    MGG_CIRCUIT_ORDER,
                    MGG_ID,
                    MGG_NM,
					'yyyy-mm-dd' AS VISIT_DT
                FROM NT_MAIN.NMG_GARAGE
                WHERE DEL_YN ='N';
				
		";
	    // echo $iSQL;
		$Result= DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
				"IDX"=>$IDX--,
				"CSD_NM"=>$RS['CSD_NM'],
				"MGG_CIRCUIT_ORDER"=>$RS['MGG_CIRCUIT_ORDER'],
				"MGG_ID"=>$RS['MGG_ID'],
				"MGG_NM"=>$RS['MGG_NM'],
				"VISIT_DT"=>$RS['VISIT_DT']
			));
		}
		$Result->close();  
		unset($RS);

		if(empty($rtnArray)) {
			return HAVE_NO_DATA;
		} else {
			$jsonArray = array();
			array_push($jsonArray,$rtnArray);
			array_push($jsonArray,$IDX);
			$rtnJSON = json_encode($jsonArray,JSON_UNESCAPED_UNICODE);

			return  $jsonArray;
		}
    }
    


	/**
	 * 데이터생성
	 */
	function setData($ARR,$PDS) {
		
		$targetDir = $this->DATA_SAVE_PATH.date("Ymd");
	
		if(!is_dir($targetDir)) {			mkdir($targetDir);		}

		if($PDS['UPLOAD_VITIT']['name'] != "") {
            $ext = pathinfo($PDS['UPLOAD_VITIT']['name'], PATHINFO_EXTENSION);
			if(strtolower($ext) == "csv" ) {
				$tmpFilename	= basename($PDS['UPLOAD_VITIT']['tmp_name']);	// 파일명 획득
				$upload_Target = $targetDir."/".$tmpFilename;             // 첨부파일 경로 (FULL PATH)

				if(!move_uploaded_file($PDS['UPLOAD_VITIT']['tmp_name'],$upload_Target)){
					 return ERROR_MOVE_FILE; // 파일카피 실패
				} else {
					chmod($upload_Target, 0777);
					echo "[".date("Y-m-d H:i:s")."] 임원방문월 파일 업로드 완료<br/>";
				}
			} else {
				echo "[".date("Y-m-d H:i:s")."] [오류] 임원방문월 파일 업로드 실패 - 올바르지 않은 확장자 - CSV파일만 업로드 가능합니다<br/>";
				return ERROR_NOT_ALLOWED_FILE;
			}
		} else {
			echo "[".date("Y-m-d H:i:s")."] [오류] 임원방문월 파일 업로드 실패 - 첨부 파일 없음<br/>";
			return ERROR_NO_FILE;
		}

		// 빌키정보 DB 저장
		$iSQL = $this->getFileQuery($ARR, $upload_Target);
		if($iSQL  != ERROR_DB_INSERT_DATA){

			if(DBManager::execMulti($iSQL)) {
				echo "[".date("Y-m-d H:i:s")."] 임원방문월 DB 저장 완료<br/>";
				return OK;
			} else {
				// 빌키정보 DB 저장 실패
				echo "[".date("Y-m-d H:i:s")."] [오류] 임원방문월 DB 저장 실패<br/>";
				return ERROR_DB_INSERT_DATA;
			}
		}else {
			echo "[".date("Y-m-d H:i:s")."] 임원방문월 업로드 데이터 오류<br/>";
			return ERROR_DB_INSERT_DATA;
		}
		
		return $rtnValue;
    }

    function getFileQuery($ARR,$filename) {
        $fp = fopen($filename, "r") or die("파일열기에 실패하였습니다");
		// echo "fp>>"."\n".var_dump($fp);
		$preSQL = "REPLACE INTO `NT_MAIN`.`NMG_GARAGE_VISIT`
					(`NGV_VISIT_DT`,
					`MGG_ID`,
					`WRT_DTHMS`,
					`LAST_DTHMS`,
					`WRT_USERID`)
					VALUES";
		$cnt = 0;
        while(!feof($fp)){
			$cnt++ ;
			$row = fgets($fp);
			$data = explode(",",$row);
			if(count($data) == 6){
				$vistDay= preg_replace("/\s+/", "", $data[5]);
				if(strlen($vistDay) == 10 && $vistDay != 'yyyy-mm-dd'){
					// echo "index:".$cnt." insert >> "."('".preg_replace("/\s+/", "", $data[5])."',".$data[3].", NOW(),NOW(),'".addslashes($_SESSION['USERID'])."'); ";
					$iSQL .= $preSQL."('".preg_replace("/\s+/", "", $data[5])."',".$data[3].", NOW(),NOW(),'".addslashes($_SESSION['USERID'])."'); ";
				}
			}else if(count($data) > 6){		
				echo "index:".$cnt." count:".count($data)."   >> ";
				$vistDay= preg_replace("/\s+/", "", $data[5]);
				echo $data[3]."-".$data[4]." ".$data[5];
				return ERROR_DB_INSERT_DATA;
			}
			
		}
        fclose($fp);
		echo $cnt.$iSQL;
        return  $iSQL;
    }
	
	



}
// 0. 객체 생성
$cVisit = new cVisit(FILE_UPLOAD_PATH."visit/",ALLOW_FILE_EXT); //서버용
// $cVisit = new cVisit("../../../FILE_DATA/visit/",ALLOW_FILE_EXT); //로컬용

$cVisit->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
