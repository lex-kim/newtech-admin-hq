<?php
class cExchange extends DBManager {
	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

    function cExchange($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
		$this->DATA_SAVE_PATH = $DATA_SAVE_PATH;
		$this->ALLOW_FILE_EXT = $ALLOW_FILE_EXT;
    }
    
    /* 취급부품 조회  */
	function getCpps($ARR) {
		$iSQL = "SELECT 
                    CPPS_ID,
                    CPPS_NM,
                    CPPS_NM_SHORT,
                    CPPS_RATIO
                FROM NT_CODE.NCP_PART_SUB
                WHERE CPP_ID ='".$ARR['CPP_ID']."'
                AND DEL_YN='N'
                AND CPPS_RECKON_YN ='Y';
		";
		$Result= DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"CPPS_ID"=>$RS['CPPS_ID'],
					"CPPS_NM"=>$RS['CPPS_NM'],
					"CPPS_NM_SHORT"=>$RS['CPPS_NM_SHORT'],
					"CPPS_RATIO"=>$RS['CPPS_RATIO']
			));
		}
		$Result->close();  // 자원 반납
		unset($RS);

		if(empty($rtnArray)) {
			return HAVE_NO_DATA;
		} else {
			return json_encode($rtnArray);
		}
    }
    


    /**
	 * 조회 
	 * @param {$ARR}
	 * @param {CPP_ID: 기준부품, CPPS_ID: 취급부품 RRT_ID: 작업(교환/판금)}
	 */
	function getList($ARR) {
		// 리스트 가져오기
		$iSQL = "SELECT 
                    NRPS.RWP_ID,
                    NRPS.RCT_CD,
                    NRPS.NRPS_RATIO
                FROM NT_CODE.NCT_RECKON_PART_SUB NRPS
                WHERE 
                     NRPS.CPP_ID = '".addslashes($ARR['CPP_ID'])."' 
                AND NRPS.CPPS_ID = '".addslashes($ARR['CPPS_ID'])."' 
                AND NRPS.RRT_ID ='".addslashes($ARR['RRT_ID'])."'
                ORDER BY NRPS.RWP_ID, NRPS.RCT_CD ;
                ";
		$Result= DBManager::getResult($iSQL);
		// echo $iSQL;
		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
				"RWP_ID"=>$RS['RWP_ID'],
				"RCT_CD"=>$RS['RCT_CD'],
				"NRPS_RATIO"=>round($RS['NRPS_RATIO'],2)
			));
        }
		$Result->close();  // 자원 반납
        unset($RS);
        
		if(empty($rtnArray)) {
            $iSQL ="SELECT 
                    NRP.RCT_CD,
                    NRP.RWP_ID,
                    ROUND(NRP.NRP_RATIO*NPS.CPPS_RATIO ,2) AS NRPS_RATIO
                FROM 
                    NT_CODE.NCT_RECKON_PART NRP,
                    NT_CODE.NCP_PART_SUB NPS
                WHERE NRP.CPP_ID = NPS.CPP_ID AND NPS.DEL_YN = 'N'
                AND CPPS_ID='".addslashes($ARR['CPPS_ID'])."'
                AND NRP.RRT_ID ='".addslashes($ARR['RRT_ID'])."'
                ORDER BY NRP.RWP_ID, NRP.RCT_CD ;
            ";
            // echo '92'.$iSQL;
            $initResult= DBManager::getResult($iSQL);

            $initArray = array();
            while($RS = $initResult->fetch_array()){
                array_push($initArray,
                    array(
                    "RWP_ID"=>$RS['RWP_ID'],
                    "RCT_CD"=>$RS['RCT_CD'],
                    "NRPS_RATIO"=>$RS['NRPS_RATIO']
                ));
            }
            $initResult->close();  // 자원 반납
            unset($RS);
            if(empty($initArray)){
                $arrWork = $this->getWorkList();
                $arrCar = $this->getCarList();
                $initBaseArray = array();
                foreach($arrWork as $key=>$work){
                    foreach($arrCar as $key=>$car){
                        array_push($initBaseArray,
                        array(
                        "RWP_ID"=>$work['RWP_ID'],
                        "RCT_CD"=>$car['RCT_CD'],
                        "NRPS_RATIO"=>0
                    ));
                    }
                }
                $jsonBase = array();
                array_push($jsonBase,$initBaseArray);
                array_push($jsonBase,NOT_EXIST); //기준부품등록 안되어 있음
                return $jsonBase;
            }else {
                $jsonInit = array();
                array_push($jsonInit,$initArray);
                array_push($jsonInit,HAVE_NO_DATA); //저장된데이터가 없음.
                return $jsonInit;
            }
		} else {
            $jsonArr = array();
                array_push($jsonArr,$rtnArray);
                array_push($jsonArr,OK); //저장된데이터가 없음.
			return  $jsonArr;
		}
		
    }

    /**
	 * 데이터생성
	 */
	function setData($ARR) {
        // echo var_dump($ARR)."\n";
        $orgData = $this->getList($ARR);
        $arrPart = array();
        // echo '147>>>>>>>>>>>>>>'."\n";
        // echo var_dump( $orgData[0]);
        foreach($orgData[0] as $key=>$value){
            array_push($arrPart, 'td_part_'.$value['RWP_ID']."_".$value['RCT_CD']);
        }
        // echo "arrPart\n".var_dump($arrPart);
       
        /* 기준부품 */
        $iSQL = "";
        foreach($arrPart as $key=>$value){
            $splitPart = explode('_', $value);
      
            $iSQL .=   "('".$ARR['CPP_ID']."', 
                            '".$ARR['CPPS_ID']."', 
                            '".$splitPart[3]."', 
                            '".$splitPart[2]."', 
                            '".$ARR['RRT_ID']."', 
                            ".$ARR[$value].",
                            NOW(),NOW(), '".$_SESSION['USERID']."' ),";
        }
        $iSQL = substr($iSQL, 0, -1); // 마지막 콤마제거
        $iSQL =  "REPLACE INTO `NT_CODE`.`NCT_RECKON_PART_SUB`
                                (`CPP_ID`,
                                `CPPS_ID`,
                                `RCT_CD`,
                                `RWP_ID`,
                                `RRT_ID`,
                                `NRPS_RATIO`,
                                `WRT_DTHMS`,
                                `LAST_DTHMS`,
                                `WRT_USERID`)
                                VALUES".$iSQL.";
        ";
        // echo $iSQL;
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
        $iSQL = "SELECT `RWP_ID`, `RWP_NM1`, `RWP_NM2`, 0 AS 'RWP_RATIO'
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
                     "RWP_NM2"=>$RS['RWP_NM2'],
                     "RWP_RATIO"=>$RS['RWP_RATIO']
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


    function resetData($ARR){
        $iSQL = "SELECT 
                    NRP.RCT_CD,
                    NRP.RWP_ID,
                    ROUND(NRP.NRP_RATIO*NPS.CPPS_RATIO ,2) AS NRPS_RATIO
                FROM 
                    NT_CODE.NCT_RECKON_PART NRP,
                    NT_CODE.NCP_PART_SUB NPS
                WHERE NRP.CPP_ID = NPS.CPP_ID AND NPS.DEL_YN = 'N'
                AND CPPS_ID='".addslashes($ARR['CPPS_ID'])."'
                AND NRP.RRT_ID ='".addslashes($ARR['RRT_ID'])."'
                ORDER BY NRP.RWP_ID, NRP.RCT_CD ;
        ";
        // echo $iSQL;
        $initResult= DBManager::getResult($iSQL);

        $initArray = array();
        while($RS = $initResult->fetch_array()){
            array_push($initArray,
                array(
                "RWP_ID"=>$RS['RWP_ID'],
                "RCT_CD"=>$RS['RCT_CD'],
                "NRPS_RATIO"=>$RS['NRPS_RATIO']
            ));
        }
        $initResult->close();  // 자원 반납
        unset($RS);
        if(empty($initArray)){
            $arrWork = $this->getWorkList();
            $arrCar = $this->getCarList();
            $initBaseArray = array();
            foreach($arrWork as $key=>$work){
                foreach($arrCar as $key=>$car){
                    array_push($initBaseArray,
                    array(
                    "RWP_ID"=>$work['RWP_ID'],
                    "RCT_CD"=>$car['RCT_CD'],
                    "NRPS_RATIO"=>0
                ));
                }
            }
            $jsonBase = array();
            array_push($jsonBase,$initBaseArray);
            array_push($jsonBase,NOT_EXIST); //기준부품등록 안되어 있음
            return $jsonBase;
        }else {
            $jsonInit = array();
            array_push($jsonInit,$initArray);
            array_push($jsonInit,HAVE_NO_DATA); //저장된데이터가 없음.
            return $jsonInit;
        }
    }

    /**
     * 환산부품견표 엑셀다운로드
     */
    function getTotalList($ARR){
        // 리스트 가져오기
		$iSQL = "SELECT 
                    NRPS.`NRPS_RATIO`
                FROM 
                    NT_CODE.NCT_RECKON_PART_SUB NRPS
                WHERE  
                    NRPS.CPP_ID = '".addslashes($ARR['CPP_ID'])."' 
                AND NRPS.RRT_ID ='".addslashes($ARR['RRT_ID'])."'
                AND NRPS.RWP_ID ='".addslashes($ARR['RWP_ID'])."'
                AND NRPS.CPPS_ID ='".addslashes($ARR['CPPS_ID'])."'
                
                ORDER BY NRPS.RWP_ID, NRPS.RCT_CD ;
        ";
        $Result= DBManager::getResult($iSQL);
        $rtnArray = array();
        while($RS = $Result->fetch_array()){
            array_push($rtnArray,
                array(
                    "NRPS_RATIO"=>$RS['NRPS_RATIO']
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
$cExchange = new cExchange(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
$cExchange->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);
?>