<?php

class clsGwReference extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	// Class Object Constructor
	function clsGwReference($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
		$this->DATA_SAVE_PATH = $DATA_SAVE_PATH;
		$this->ALLOW_FILE_EXT = $ALLOW_FILE_EXT;
	}


    /**
     * 해당 레퍼런스 데이터 가져오기
     */
    function getRefData($ARR){
        $tableNm = "NT_CODE.REF_".$ARR['REF_TYPE']."_TYPE";
        $COL_ID = $ARR['COL_TYPE']."_ID";
        $COL_NM = $ARR['COL_TYPE']."_NM";

        $iSQL = "SELECT 
                    $COL_ID,
                    $COL_NM 
                FROM $tableNm
                ;"
        ;
   
        $Result= DBManager::getResult($iSQL);

        $rtnArray = array();
        while($RS = $Result->fetch_array()){
            array_push($rtnArray,
                array(
                "ID"=>$RS[$COL_ID],
                "NM"=>$RS[$COL_NM]
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

    /** 공급사 해당 ref 가져오기
     * 
    */
    function getRefOption($ARR){
        $tableNm = '';
        $getColumn_1 = '';
        $getColumn_2 = '';
        $subSQL = '';
        if($ARR['REF_TYPE'] == GET_REF_EXP_TYPE){
            $tableNm = 'NT_CODE.REF_EXP_TYPE';
            $getColumn_1 = 'RET_ID';
            $getColumn_2 = 'RET_NM';
        }else if($ARR['REF_TYPE'] == GET_REF_PAY_EXP_TYPE){
            $tableNm = 'NT_CODE.REF_PAY_EXP_TYPE';
            $getColumn_1 = 'RPET_ID';
            $getColumn_2 = 'RPET_NM';
        }else if($ARR['REF_TYPE'] == GET_REF_NMI_INSU_TYPE){
            $tableNm = 'NT_MAIN.NMI_INSU';
            $getColumn_1 = 'MII_ID';
            $getColumn_2 = 'MII_NM';
            $subSQL = "WHERE DEL_YN = 'N'";
        }else if($ARR['REF_TYPE'] == GET_REF_AOS_TYPE){
            $tableNm = 'NT_CODE.REF_AOS_TYPE';
            $getColumn_1 = 'RAT_ID';
            $getColumn_2 = 'RAT_NM';
        }else if($ARR['REF_TYPE'] == GET_REF_MAIN_TARGET_TYPE){
            $tableNm = 'NT_CODE.REF_MAIN_TARGET';
            $getColumn_1 = 'RMT_ID';
            $getColumn_2 = 'RMT_NM';
        }else if($ARR['REF_TYPE'] == GET_REF_OWN_TYPE){
            $tableNm = 'NT_CODE.REF_OWN_TYPE';
            $getColumn_1 = 'ROT_ID';
            $getColumn_2 = 'ROT_NM';
        }else if($ARR['REF_TYPE'] == GET_REF_CONTRACT_TYPE){
            $tableNm = 'NT_CODE.REF_CONTRACT_TYPE';
            $getColumn_1 = 'RCT_ID';
            $getColumn_2 = 'RCT_NM';
        }else if($ARR['REF_TYPE'] == GET_REF_STOP_REASON_TYPE){
            $tableNm = 'NT_CODE.REF_STOP_REASON';
            $getColumn_1 = 'RSR_ID';
            $getColumn_2 = 'RSR_NM';
        }else if($ARR['REF_TYPE'] == GET_REF_SCALE_TYPE){
            $tableNm = 'NT_CODE.REF_SCALE_TYPE';
            $getColumn_1 = 'RST_ID';
            $getColumn_2 = 'RST_NM';
        }else if($ARR['REF_TYPE'] == GET_REF_REACTION_TYPE){
            $tableNm = 'NT_CODE.REF_REACTION_TYPE';
            $getColumn_1 = 'RRT_ID';
            $getColumn_2 = 'RRT_NM';
        }else if($ARR['REF_TYPE'] == GET_REF_INSU_CHAGE_TYPE){
            $tableNm = 'NT_CODE.REF_INSU_CHARGE_TYPE';
            $getColumn_1 = 'RICT_ID';
            $getColumn_2 = 'RICT_NM';
        }else if($ARR['REF_TYPE'] == GET_REF_CLAIMANT_TYPE){
            $tableNm = 'NT_CODE.REF_CLAIMANT_TYPE';
            $getColumn_1 = 'RMT_ID';
            $getColumn_2 = 'RMT_NM';
        }else if($ARR['REF_TYPE'] == GET_REF_CLAIM_TYPE){
            $tableNm = 'NT_CODE.REF_CLAIM_TYPE';
            $getColumn_1 = 'RLT_ID';
            $getColumn_2 = 'RLT_NM';
        }else if($ARR['REF_TYPE'] == REF_FAX_STATUS){
            $tableNm = 'NT_CODE.REF_FAX_STATUS';
            $getColumn_1 = 'RFS_ID';
            $getColumn_2 = 'RFS_NM';
        }else if($ARR['REF_TYPE'] == REF_DEPOSITEE_TYPE){
            $tableNm = 'NT_CODE.REF_DEPOSITEE_TYPE';
            $getColumn_1 = 'RDET_ID';
            $getColumn_2 = 'RDET_NM';
        }else if($ARR['REF_TYPE'] == GET_REF_CAR_TYPE){
            $tableNm = 'NT_CODE.REF_CAR_TYPE';
            $getColumn_1 = 'RCT_CD';
            $getColumn_2 = 'RCT_NM';
        }else if($ARR['REF_TYPE'] == GET_STOCK_TYPE){
            $tableNm = 'NT_CODE.REF_STOCK_TYPE';
            $getColumn_1 = 'RST_ID';
            $getColumn_2 = 'RST_NM';
        }else if($ARR['REF_TYPE'] == GET_REF_SALES_FILE){
            $tableNm = 'NT_CODE.REF_SALES_FILE';
            $getColumn_1 = 'RSF_ID';
            $getColumn_2 = 'RSF_NM';
        }else if($ARR['REF_TYPE'] == REF_COOP_REACTION){
            $tableNm = 'NT_CODE.REF_COOP_REACTION';
            $getColumn_1 = 'RPR_ID';
            $getColumn_2 = 'RPR_NM';
        }else if($ARR['REF_TYPE'] == REF_COOP_TYPE){
            $tableNm = 'NT_CODE.REF_COOP_TYPE';
            $getColumn_1 = 'RPT_ID';
            $getColumn_2 = 'RPT_NM';
        }else if($ARR['REF_TYPE'] == REF_CONSIGN_STATUS){
            $tableNm = 'NT_CODE.REF_CONSIGN_STATUS';
            $getColumn_1 = 'RCS_ID';
            $getColumn_2 = 'RCS_NM';
        }else if($ARR['REF_TYPE'] == REF_CONSIGN_SPOT){
            $tableNm = 'NT_CODE.REF_CONSIGN_SPOT';
            $getColumn_1 = 'RCP_ID';
            $getColumn_2 = 'RCP_NM';
        }else if($ARR['REF_TYPE'] == REF_POINT_STATUS){
            $tableNm = 'NT_CODE.REF_POINT_STATUS';
            $getColumn_1 = 'RPS_ID';
            $getColumn_2 = 'RPS_NM';
        }else if($ARR['REF_TYPE'] == REF_POINT_TYPE){
            $tableNm = 'NT_CODE.REF_POINT_TYPE';
            $getColumn_1 = 'RPT_ID';
            $getColumn_2 = 'RPT_NM';
        }else{
            return ERROR;
        }		
        
        $iSQL = "SELECT 
                    $getColumn_1,
                    $getColumn_2 
                FROM $tableNm
                $subSQL 
                ;"
        ;

        $Result= DBManager::getResult($iSQL);
        ///

        $rtnArray = array();
        while($RS = $Result->fetch_array()){
            array_push($rtnArray,
                array(
                "ID"=>$RS[$getColumn_1],
                "NM"=>$RS[$getColumn_2]
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
	$clsGwReference = new clsGwReference(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$clsGwReference->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
