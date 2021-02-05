<?php
class cAttached extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
    private $ALLOW_FILE_EXT;				// 업로드 가능 확장자
    
	function cAttached($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
		$this->DATA_SAVE_PATH = $DATA_SAVE_PATH;
		$this->ALLOW_FILE_EXT = $ALLOW_FILE_EXT;
    }
    /* 데이터 조회 */
    function getList($ARR){
        $LimitStart = ($ARR['CURR_PAGE']-1) * $ARR['PER_PAGE'];
        $LimitEnd   = $ARR['PER_PAGE'];
        $LIMIT_SQL = "";
        if($ARR['REQ_MODE'] == CASE_LIST){
            $LIMIT_SQL = "LIMIT $LimitStart,$LimitEnd";
        }
        
        $tmpKeyword = "";
    
        if($ARR['DT_KEY_SEARCH'] != '') {
            if($ARR['DT_KEY_SEARCH'] == 'NSU_TAKE_DTHMS'){
                if($ARR['START_DT_SEARCH'] != '' && $ARR['END_DT_SEARCH'] != ''){ // 등록일
                    $tmpKeyword .= "AND NSU.NSU_TAKE_DTHMS BETWEEN '".addslashes($ARR['START_DT_SEARCH'])."' AND '".addslashes($ARR['END_DT_SEARCH'])."' \n";
                }else if($ARR['START_DT_SEARCH'] != '' ){
                    $tmpKeyword .= "AND NSU.NSU_TAKE_DTHMS >= '".$ARR['START_DT_SEARCH']."' \n";
                }else if($ARR['END_DT_SEARCH'] != ''){
                    $tmpKeyword .= "AND NSU.NSU_TAKE_DTHMS <= '".$ARR['END_DT_SEARCH']."' \n";
                }
            }else if($ARR['DT_KEY_SEARCH'] == 'NSU_PROC_DTHMS'){ // 처리일
                if($ARR['START_DT_SEARCH'] != '' && $ARR['END_DT_SEARCH'] != ''){
                    $tmpKeyword .= "AND DATE_FORMAT(NSU.NSU_PROC_DTHMS, '%Y-%m-%d') BETWEEN '".addslashes($ARR['START_DT_SEARCH'])."' AND '".addslashes($ARR['END_DT_SEARCH'])."'";
                }else if($ARR['START_DT_SEARCH'] != '' ){
                    $tmpKeyword .= "AND DATE_FORMAT(NSU.NSU_PROC_DTHMS, '%Y-%m-%d') >= '".$ARR['START_DT_SEARCH']."' \n";
                }else if($ARR['END_DT_SEARCH'] != ''){
                    $tmpKeyword .= "AND DATE_FORMAT(NSU.NSU_PROC_DTHMS, '%Y-%m-%d') <= '".$ARR['END_DT_SEARCH']."' \n";
                }
            }
        } 
        if($ARR['RSF_ID_SEARCH'] != '') {
            $tmpKeyword .= "AND NSU.RSF_ID = '".addslashes($ARR['RSF_ID_SEARCH'])."' ";
        } 
        
        if($ARR['CSD_ID_SEARCH'] != ''){
            $tmpKeyword .= "AND NG.CSD_ID IN (";
            foreach ($ARR['CSD_ID_SEARCH'] as $index => $CSD_ARRAY) {
                $tmpKeyword .=" '".$CSD_ARRAY."',";
            }
            $tmpKeyword=substr($tmpKeyword, 0, -1); // 마지막 콤마제거
            $tmpKeyword .= ") \n";
        }
        
        if($ARR['CRR_ID_SEARCH'] !=  '' || $ARR['CRR_ID_SEARCH'] != null) {
            $tmpKeyword .= "AND (";
            foreach ($ARR['CRR_ID_SEARCH'] as $index => $CRD_ARRAY) {
                $CSD = explode(',', $CRD_ARRAY);
                
                if($index == 0){
                    $tmpKeyword .= "(";
                    $tmpKeyword .= "`NG`.`CRR_1_ID` = '".$CSD[0]."' \n";
                }else{
                    $tmpKeyword .= " OR(";
                    $tmpKeyword .= "  `NG`.`CRR_1_ID` = '".$CSD[0]."' \n";
                }
                if($CSD[1] != NOT_CRR_2){
                    $tmpKeyword .= "AND `NG`.`CRR_2_ID` = '".$CSD[1]."' \n";
                }
                
                if($CSD[2] != NOT_CRR_3){
                    $tmpKeyword .= "AND `NG`.`CRR_3_ID` = '".$CSD[2]."' \n";
                }
                
                $tmpKeyword .= ") \n";
            }
            $tmpKeyword .= ") \n";
        }
        
        //공업사명 검색
        if($ARR['MGG_NM_SEARCH'] != '') {
            $tmpKeyword .= "AND NG.MGG_ID = '".$ARR['MGG_NM_SEARCH']."' \n";	
        }
        
        // 청구식 검색
        if($ARR['RET_ID_SEARCH'] !=  '' || $ARR['RET_ID_SEARCH'] != null) {
            $tmpKeyword .= "AND NGS.RET_ID IN ( ";
            foreach ($ARR['RET_ID_SEARCH'] as $index => $RET_ID) {
                $tmpKeyword .=" '".$RET_ID."',";
            }
            $tmpKeyword = substr($tmpKeyword, 0, -1); // 마지막 콤마제거
            $tmpKeyword .= ") \n";
        }
        //거래여부 검색
        if($ARR['RCT_ID_SEARCH'] != '') {
            $tmpKeyword .= "AND NGS.RCT_ID = '".$ARR['RCT_ID_SEARCH']."' \n";	
        }
        
        // 등록자 검색
        if($ARR['NSU_TAKE_USERID_SEARCH'] != '') {
            $tmpKeyword .= "AND INSTR( NT_AUTH.GET_USER_NM(NSU.NSU_TAKE_USERID),'".addslashes($ARR['NSU_TAKE_USERID_SEARCH'])."')>0 ";	
        } 

        // 처리자 검색
        if($ARR['NSU_PROC_USERID_SEARCH'] != '') {
            $tmpKeyword .= "AND INSTR( NT_AUTH.GET_USER_NM(NSU.NSU_PROC_USERID),'".addslashes($ARR['NSU_PROC_USERID_SEARCH'])."')>0 ";	
        } 
        // 페이징처리를 위해 전체데이터
        $iSQL = "SELECT 
                    COUNT(*) AS CNT
                FROM 
                    `NT_MAIN`.`NMG_GARAGE` NG,
                    `NT_MAIN`.`NMG_GARAGE_SUB` NGS,
                    `NT_SALES`.`NS_SALES_UPLOAD` NSU
                WHERE  NG.MGG_ID = NGS.MGG_ID
                AND NG.MGG_ID = NSU.MGG_ID
                AND NGS.DEL_YN ='N'
                AND NG.DEL_YN='N'
                AND NSU.RSF_ID NOT IN ('50110', '50120')
                $tmpKeyword ;
        ";
        // echo $iSQL;
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
                    NSU.`NSU_ID`,
                    NG.`MGG_ID`,
                    NG.`MGG_NM`,
                    NG.`CSD_ID`,
                    NT_CODE.GET_CSD_NM( NG.`CSD_ID`) AS CSD_NM,
                    NG.`CRR_1_ID`,
                    NG.`CRR_2_ID`,
                    NG.`CRR_3_ID`,
                    NT_CODE.GET_CRR_NM(NG.`CRR_1_ID`,NG.`CRR_2_ID`,NG.`CRR_3_ID`) AS CRR_NM,
                    NG.`MGG_BIZ_ID`,
                    IFNULL(NGS.`RET_ID`,'') AS RET_ID,
                    IFNULL(NT_CODE.GET_CODE_NM( NGS.`RET_ID`),'') AS RET_NM,
                    NGS.`RCT_ID`,
                    NT_CODE.GET_CODE_NM( NGS.`RCT_ID`) AS RCT_NM,
                    NSU.NSU_TAKE_DTHMS,
                    NSU.NSU_TAKE_USERID,
                    IFNULL( NT_AUTH.GET_USER_NM(NSU.NSU_TAKE_USERID),'-') AS TAKE_USER_NAME,
                    NSU.NSU_PROC_YN,
                    IFNULL(NSU.NSU_PROC_DTHMS,'') AS NSU_PROC_DTHMS,
                    IFNULL(NSU.NSU_PROC_USERID,'') AS NSU_PROC_USERID,
                    IFNULL( NT_AUTH.GET_USER_NM(NSU.NSU_PROC_USERID),'-') AS PROC_USER_NAME,
                    NT_CODE.GET_CODE_NM( NSU.`RSF_ID`) AS RSF_ID,
                    NSU_FILE_IMG
                FROM 
                    `NT_MAIN`.`NMG_GARAGE` NG,
                    `NT_MAIN`.`NMG_GARAGE_SUB` NGS,
                    `NT_SALES`.`NS_SALES_UPLOAD` NSU
                WHERE  NG.MGG_ID = NGS.MGG_ID
                AND NG.MGG_ID = NSU.MGG_ID
                AND NGS.DEL_YN ='N'
                AND NG.DEL_YN='N'
                AND NSU.RSF_ID NOT IN ('50110', '50120')
                $tmpKeyword
                ORDER BY NSU.NSU_TAKE_DTHMS DESC 
                $LIMIT_SQL ;
        ";
        // echo $iSQL;
        $Result= DBManager::getResult($iSQL);
        $rtnArray = array();
        while($RS = $Result->fetch_array()){
            array_push($rtnArray,
                array(
                "IDX"=>$IDX--,
                "NSU_ID"=>$RS['NSU_ID'],
                "MGG_ID"=>$RS['MGG_ID'],
                "MGG_NM"=>$RS['MGG_NM'],
                "CSD_ID"=>$RS['CSD_ID'],
                "CSD_NM"=>$RS['CSD_NM'],
                "CRR_1_ID"=>$RS['CRR_1_ID'],
                "CRR_2_ID"=>$RS['CRR_2_ID'],
                "CRR_3_ID"=>$RS['CRR_3_ID'],
                "CRR_NM"=>$RS['CRR_NM'],
                "RET_NM"=>$RS['RET_NM'],
                "RCT_NM"=>$RS['RCT_NM'],
                "NSU_TAKE_DTHMS"=>$RS['NSU_TAKE_DTHMS'],
                "NSU_TAKE_USERID"=>$RS['NSU_TAKE_USERID'],
                "TAKE_USER_NAME"=>$RS['TAKE_USER_NAME'],
                "NSU_PROC_YN"=>$RS['NSU_PROC_YN'],
                "NSU_PROC_DTHMS"=>$RS['NSU_PROC_DTHMS'],
                "NSU_PROC_USERID"=>$RS['NSU_PROC_USERID'],
                "PROC_USER_NAME"=>$RS['PROC_USER_NAME'],
                "RSF_ID"=>$RS['RSF_ID'],
                "NSU_FILE_IMG"=>$RS['NSU_FILE_IMG']
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


        /* 승인 */
    function setData($ARR){
        $iSQL ="UPDATE `NT_SALES`.`NS_SALES_UPLOAD`
                    SET
                    `NSU_PROC_YN` = 'Y',
                    `NSU_PROC_DTHMS` = NOW(),
                    `NSU_PROC_USERID` = '".$_SESSION['USERID']."'
                    WHERE `NSU_ID` = '".addslashes($ARR['NSU_ID'])."';
        ";
        return DBManager::execQuery($iSQL);
    }


}
// 0. 객체 생성
$cAttached = new cAttached(FILE_UPLOAD_PATH,ALLOW_FILE_EXT); //서버용
$cAttached->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);
?>