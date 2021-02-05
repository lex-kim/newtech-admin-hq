<?php

class cStandard extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	// Class Object Constructor
	function cStandard($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
		$this->DATA_SAVE_PATH = $DATA_SAVE_PATH;
		$this->ALLOW_FILE_EXT = $ALLOW_FILE_EXT;
	}


	/**
	 * 조회 
	 * @param {$ARR}
	 * @param {CURR_PAGE : 현재페이지, PER_PAGE : 몇개를 조회할 것인지, KEYWORD : 검색조건}
	 */
	function getList($ARR,$init) {
		// 리스트 가져오기
		$iSQL = "SELECT 
                    NRP.RWP_ID,
                    NRP.RCT_CD,
                    NRP.NRP_RATIO,
                    IFNULL(NRT.NRT_TIME_M, 0) AS NRT_TIME_M
                FROM NT_CODE.NCT_RECKON_PART NRP
                LEFT OUTER JOIN NT_CODE.NCT_RECKON_TIME NRT
                    ON NRP.CPP_ID = NRT.CPP_ID 
                    AND NRP.RCT_CD = NRT.RCT_CD 
                    AND NRP.RWP_ID = NRT.RWP_ID 
                    AND NRP.RRT_ID = NRT.RRT_ID
                WHERE NRP.CPP_ID = '".addslashes($ARR['CPP_ID'])."' 
                AND NRP.RRT_ID ='".addslashes($ARR['RRT_ID'])."'
                ORDER BY NRP.RWP_ID, NRP.RCT_CD ;

                ";
		$Result= DBManager::getResult($iSQL);
		///

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
				"RWP_ID"=>$RS['RWP_ID'],
				"RCT_CD"=>$RS['RCT_CD'],
				"NRP_RATIO"=>$RS['NRP_RATIO'],
				"NRT_TIME_M"=>$RS['NRT_TIME_M']
			));
		}
		$Result->close();  // 자원 반납
		unset($RS);

		if(empty($rtnArray)) {
            $arrWork = $this->getWorkList();
            $arrCar = $this->getCarList();
            $initArray = array();
            foreach($arrWork as $key=>$work){
                foreach($arrCar as $key=>$car){
                    array_push($initArray,
                    array(
                    "RWP_ID"=>$work['RWP_ID'],
                    "RCT_CD"=>$car['RCT_CD'],
                    "NRP_RATIO"=>0,
                    "NRT_TIME_M"=>0
                ));
                }
            }
			return $initArray;
		} else {
			return  $rtnArray;
		}
		
    }

	/**
	 * 데이터생성
	 */
	function setData($ARR,$init) {

        $orgData = $this->getList($ARR,$init);
        $arrPart = array();
        $arrTime = array();

        foreach($orgData as $key=>$value){
            array_push($arrPart, 'td_part_'.$value['RWP_ID']."_".$value['RCT_CD']);
            array_push($arrTime, 'td_time_'.$value['RWP_ID']."_".$value['RCT_CD']);
        }
       
        /* 기준부품 */
        $partSQL = "";
        foreach($arrPart as $key=>$value){
            $splitPart = explode('_', $value);
            $partSQL .=   "('".$ARR['CPP_ID']."', 
                            '".$splitPart[3]."', 
                            '".$splitPart[2]."', 
                            '".$ARR['RRT_ID']."', 
                            ".$ARR[$value].",
                            NOW(),NOW(), '".$_SESSION['USERID']."' ),";
        }
        $partSQL = substr($partSQL, 0, -1); // 마지막 콤마제거
        $partSQL =  "REPLACE INTO `NT_CODE`.`NCT_RECKON_PART`
                                (`CPP_ID`,
                                `RCT_CD`,
                                `RWP_ID`,
                                `RRT_ID`,
                                `NRP_RATIO`,
                                `WRT_DTHMS`,
                                `LAST_DTHMS`,
                                `WRT_USERID`)
                                VALUES".$partSQL.";";
        
        /* 방청시간 */
        foreach($arrTime as $key=>$value){
            $splitTime = explode('_', $value);
            // echo var_dump($splitTime);
            $timeSQL .=   "('".$ARR['CPP_ID']."', 
                            '".$splitTime[3]."', 
                            '".$splitTime[2]."', 
                            '".$ARR['RRT_ID']."', 
                            ".$ARR[$value].",
                            NOW(),NOW(), '".$_SESSION['USERID']."' ),";
        }
        $timeSQL = substr($timeSQL, 0, -1); // 마지막 콤마제거
        $timeSQL =  "REPLACE INTO `NT_CODE`.`NCT_RECKON_TIME`
                                (`CPP_ID`,
                                `RCT_CD`,
                                `RWP_ID`,
                                `RRT_ID`,
                                `NRT_TIME_M`,
                                `WRT_DTHMS`,
                                `LAST_DTHMS`,
                                `WRT_USERID`)
                                VALUES".$timeSQL.";";
        
        $iSQL = $partSQL."   ".$timeSQL;
        return DBManager::execMulti($iSQL);
	}

    /**
     * 차량타입 배열로 가져오기
     */
    function getCarList(){
        $iSQL = "SELECT `RCT_CD`, `RCT_NM`
                FROM `NT_CODE`.`REF_CAR_TYPE`;
                ";
         $Result= DBManager::getResult($iSQL);
         ///
        // echo $iSQL;
         $rtnArray = array();
         while($RS = $Result->fetch_array()){
             array_push($rtnArray,
                 array(
                     "RCT_CD"=>$RS['RCT_CD'],
                     "RCT_NM"=>$RS['RCT_NM']
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
     * 작업항목 배열로 가져오기
     */
    function getWorkList(){
        $iSQL = "SELECT `RWP_ID`, `RWP_NM1`, `RWP_NM2`
                FROM `NT_CODE`.`REF_WORK_PART`;
                ";
         $Result= DBManager::getResult($iSQL);
         ///
 
         $rtnArray = array();
         while($RS = $Result->fetch_array()){
             array_push($rtnArray,
                 array(
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
     * 기준부품조견표 엑셀다운로드
     */
    function getStandardExcel($ARR){
        // 리스트 가져오기
		$iSQL = "SELECT 
                    NRP.`NRP_RATIO`
                FROM 
                    NT_CODE.NCT_RECKON_PART NRP
                WHERE  
                    NRP.CPP_ID = '".addslashes($ARR['CPP_ID'])."' 
                AND NRP.RRT_ID ='".addslashes($ARR['RRT_ID'])."'
                AND NRP.RWP_ID ='".addslashes($ARR['RWP_ID'])."'
                ORDER BY NRP.RWP_ID, NRP.RCT_CD ;
        ";
        $Result= DBManager::getResult($iSQL);

        $rtnArray = array();
        while($RS = $Result->fetch_array()){
            array_push($rtnArray,
                array(
                    "NRP_RATIO"=>$RS['NRP_RATIO']
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
     * 방청시간조견표 엑셀다운로드
     */
    function getTimeExcel($ARR){
        // 리스트 가져오기
		$iSQL = "SELECT 
                    NRT.`NRT_TIME_M`
                FROM 
                    NT_CODE.NCT_RECKON_TIME NRT
                WHERE  
                    NRT.CPP_ID = '".addslashes($ARR['CPP_ID'])."' 
                AND NRT.RRT_ID ='".addslashes($ARR['RRT_ID'])."'
                AND NRT.RWP_ID ='".addslashes($ARR['RWP_ID'])."'
       
                ORDER BY NRT.RWP_ID, NRT.RCT_CD ;
        ";
        $Result= DBManager::getResult($iSQL);

        $rtnArray = array();
        while($RS = $Result->fetch_array()){
            array_push($rtnArray,
                array(
                    "NRT_TIME_M"=>$RS['NRT_TIME_M']
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
$cStandard = new cStandard(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
$cStandard->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
