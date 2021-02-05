<?php
require_once $_SERVER['DOCUMENT_ROOT']."/common/class/CommonApi.php";
class clsCommon extends CommonApi {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	// Class Object Constructor
	function clsCommon($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
		$this->DATA_SAVE_PATH = $DATA_SAVE_PATH;
		$this->ALLOW_FILE_EXT = $ALLOW_FILE_EXT;
	}



    function getUserAuthLevel($ARR){
        $AUTH_ID = $_SESSION['AUTH_ID'];
        $SUPER_ID = false;
        $iSQL = "SELECT 
                    ASA_ID
                FROM 
                    NT_AUTH.NAS_ADMIN
                WHERE ASA_USERID= '".addslashes($_SESSION['USERID'])."'
            ;"
        ;

        if(!empty(DBManager::getRecordSet($iSQL))){
            $SUPER_ID = true;
        }
        $iSQL = "SELECT 
                    NUI.NUI_1ST,
                    IFNULL(NUI.NUI_2ND,'')AS NUI_2ND,
                    IFNULL(NUI.NUI_3RD,'')AS NUI_3RD,
                    IFNULL(NUI.NUI_4TH,'')AS NUI_4TH,
                    IFNULL(ALLS.ALLS_CREATE_YN,'N') AS ALLS_CREATE_YN,
                    IFNULL(ALLS.ALLS_READ_YN,'N') AS ALLS_READ_YN,
                    IFNULL(ALLS.ALLS_UPDATE_YN,'N') AS ALLS_UPDATE_YN,
                    IFNULL(ALLS.ALLS_DELETE_YN,'N') AS ALLS_DELETE_YN,
                    IFNULL(ALLS.ALLS_EXCEL_YN,'N') AS ALLS_EXCEL_YN
                FROM 
                    NT_AUTH.NAL_URI_INFO NUI
                LEFT JOIN NT_AUTH.NAL_LVL_SUB ALLS
                    ON ALLS.NUI_ID = NUI.NUI_ID
                    AND ALLS.ALLM_ID = '".$AUTH_ID."' 
                    AND ALLS.DEL_YN = 'N'
                WHERE NUI.NUI_URI = '".addslashes($ARR['ALLS_URI'])."'
            ;"
        ;
        $Result= DBManager::getResult($iSQL);

        $rtnArray = array();
        while($RS = $Result->fetch_array()){
            $ALLS_CREATE_YN = "";
            $ALLS_READ_YN = "";
            $ALLS_UPDATE_YN = "";
            $ALLS_DELETE_YN = "";
            $ALLS_EXCEL_YN = "";

            if($SUPER_ID){
                $ALLS_CREATE_YN = "Y";
                $ALLS_READ_YN = "Y";
                $ALLS_UPDATE_YN = "Y";
                $ALLS_DELETE_YN = "Y";
                $ALLS_EXCEL_YN = "Y";
            }else{
                $ALLS_CREATE_YN = $RS['ALLS_CREATE_YN'];
                $ALLS_READ_YN = $RS['ALLS_READ_YN'];
                $ALLS_UPDATE_YN = $RS['ALLS_UPDATE_YN'];
                $ALLS_DELETE_YN = $RS['ALLS_DELETE_YN'];
                $ALLS_EXCEL_YN = $RS['ALLS_EXCEL_YN'];;
            }
              
            array_push($rtnArray,
                array(
                    "NUI_1ST"=>$RS['NUI_1ST'],
                    "NUI_2ND"=>$RS['NUI_2ND'],
                    "NUI_3RD"=>$RS['NUI_3RD'],
                    "NUI_4TH"=>$RS['NUI_4TH'],
                    "ALLS_CREATE_YN"=>$ALLS_CREATE_YN,
                    "ALLS_READ_YN"=>$ALLS_READ_YN,
                    "ALLS_UPDATE_YN"=>$ALLS_UPDATE_YN,
                    "ALLS_DELETE_YN"=>$ALLS_DELETE_YN,
                    "ALLS_EXCEL_YN"=>$ALLS_EXCEL_YN,     
                    
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
     * 기준부품 가져오기
     */
    function getStdParts($ARR){
        $iSQL = "SELECT 
                    CPP_ID,
                    CPP_PART_TYPE,
                    CPP_NM,
                    CPP_NM_SHORT 
                FROM NT_CODE.NCP_PART
                WHERE DEL_YN = 'N'
                AND CPP_USE_YN = 'Y'
                ORDER BY CPP_ORDER_NUM ASC
                ;"
        ;
   
        $Result= DBManager::getResult($iSQL);

        $rtnArray = array();
        while($RS = $Result->fetch_array()){
            array_push($rtnArray,
                array(
                "CPP_ID"=>$RS['CPP_ID'],
                "CPP_PART_TYPE"=>$RS['CPP_PART_TYPE'],
                "CPP_NM"=>$RS['CPP_NM'],
                "CPP_NM_SHORT"=>$RS['CPP_NM_SHORT'],
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

    function setData($ARR){
        if(isset($ARR['pg_result'])){                          //PG결과요청
            $data = array(
                "accessToken"=>$_SESSION['accessToken'],
                "requestMode"=> PUT_ORDER_PG,
                "hqID"=>HQ_ID,
                "userID"=>$_SESSION['USERID'],
            );
            if(is_array(json_decode($ARR['pg_result'],true))){
                $result = json_decode($ARR['pg_result'],true);
                $data = array_merge($data, json_decode($ARR['pg_result'],true));
                $data = array_merge($data, array(
                    "orderID"=>$result['order_no'],
                    "cancelIssueYN"=>(empty($result['cancelTypeCode']) ? "N" : "Y"),
                    "cancelTypeCode"=>$result['cancelTypeCode'],
                    "cancelDetailTypeCode"=>$result['cancelDetailTypeCode'],

                ));
                return $this->get("/ec/Order/PG", $data);
            }else{
                return ERROR;
            }   
        }
        
    }


}
	// 0. 객체 생성
	$clsCommon = new clsCommon(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$clsCommon->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>

