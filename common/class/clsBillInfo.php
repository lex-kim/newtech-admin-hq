<?php

class clsBillInfo extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
    private $ALLOW_FILE_EXT;				// 업로드 가능 확장

	// Class Object Constructor
	function clsBillInfo($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
		$this->DATA_SAVE_PATH = $DATA_SAVE_PATH;
        $this->ALLOW_FILE_EXT = $ALLOW_FILE_EXT;
	}

    function getBillWorkPartInfo($ARR){
            $iSQL = "SELECT 
                        NT_CODE.GET_NRPS_RATIO(NBW.RWPP_ID, NBW.RRT_ID,NBW.NWP_ID,NRPS.NRPS_RATIO) AS NRPS_RATIO,
                        NBW.NWP_ID,
                        NBW.RRT_ID,
                        NBW.RWPP_ID,  
                        NBW.RRT_ID,
                        NBP.CPP_ID,
                        IF(NBW.".$ARR['SQL_TYPE']."W_TIME != 0 , NRT.NRT_TIME_M,0)AS NRT_TIME_M,
                        NBPS.CPPS_ID

                    FROM 
                        NT_BILL.NBB_". $ARR['TABLE_NM']."_WORK NBW
                    RIGHT JOIN NT_BILL.NBB_". $ARR['TABLE_NM']."_WORK_PART NBP
                        ON NBP.".$ARR['SQL_TYPE']."_ID = NBW.".$ARR['SQL_TYPE']."_ID AND NBW.NWP_ID = NBP.NWP_ID
                    LEFT JOIN NT_BILL.NBB_". $ARR['TABLE_NM']."_PARTS NBPS 
                    ON NBPS.".$ARR['SQL_TYPE']."_ID = NBW.".$ARR['SQL_TYPE']."_ID
                    AND NBPS.CPP_ID = NBP.CPP_ID
                    LEFT JOIN NT_CODE.NCT_RECKON_TIME NRT
                        ON NRT.CPP_ID =  NBP.CPP_ID
                        AND NRT.RWP_ID = NBW.NWP_ID
                        AND NRT.RRT_ID = NBW.RRT_ID
                        AND NRT.RCT_CD = '".addslashes($ARR['CCC_CAR_TYPE_CD'])."' 
                    LEFT JOIN NT_CODE.NCT_RECKON_PART_SUB NRPS
                        ON NRPS.CPP_ID =  NBP.CPP_ID
                        AND NRPS.RWP_ID = NBW.NWP_ID
                        AND NRPS.RRT_ID = NBW.RRT_ID
                        AND NRPS.RCT_CD = '".addslashes($ARR['CCC_CAR_TYPE_CD'])."' 
                        AND NRPS.CPPS_ID = NBPS.CPPS_ID
                    WHERE NBW.".$ARR['SQL_TYPE']."_ID = '".addslashes($ARR['NB_ID'])."'
                    ;"
            ;
            $Result = DBManager::getResult($iSQL);
            // echo $iSQL;
            $rtnArray = array();
            while($RS = $Result->fetch_array()){
            array_push($rtnArray,
                array(
                    "NWP_ID"=>$RS['NWP_ID'],
                    "RRT_ID"=>$RS['RRT_ID'],
                    "RWPP_ID"=>$RS['RWPP_ID'],
                    "RRT_ID"=>$RS['RRT_ID'],
                    "CPP_ID"=>$RS['CPP_ID'],
                    "CPPS_ID"=>$RS['CPPS_ID'],
                    "NRPS_RATIO"=>$RS['NRPS_RATIO'],
                    "NRT_TIME_M"=>$RS['NRT_TIME_M'],
                 ));
            }

            $Result->close();  // 자원 반납
            unset($RS);

            return  $rtnArray;
    }

    function getBillPartsInfo($ARR){
        
        $iSQL = "SELECT 
                    CPP.CPP_ID,
                    CPP.CPP_UNIT,
                    CPP.CPP_NM,
                    CPP.CPP_PART_TYPE,
                    CPP.CPP_NM_SHORT,
                    IFNULL(NBP.CPPS_ID,'미사용') AS CPPS_ID,
                    IFNULL(NBP.CPP_ID,'미사용') AS CPP_ID,
                    IFNULL(NT_CODE.GET_CPPS_NM(NBP.CPPS_ID),'미사용') AS CPPS_NM,
                    IFNULL(NBP.".$ARR['SQL_TYPE']."P_USE_CNT, '0') AS NBP_USE_CNT,
                    FORMAT(IFNULL(NBP.".$ARR['SQL_TYPE']."P_UNIT_PRICE,0),0) AS NBP_UNIT_PRICE,
                    FORMAT(IFNULL(ROUND(NBP.".$ARR['SQL_TYPE']."P_USE_CNT*NBP.".$ARR['SQL_TYPE']."P_UNIT_PRICE-(NBP.".$ARR['SQL_TYPE']."P_USE_CNT*NBP.".$ARR['SQL_TYPE']."P_UNIT_PRICE%10)),0),0)AS NBP_TOTAL_PRICE
                FROM 
                    NT_CODE.NCP_PART CPP
                LEFT JOIN NT_BILL.NBB_". $ARR['TABLE_NM']."_PARTS NBP
                ON CPP.CPP_ID = NBP.CPP_ID    AND  NBP.".$ARR['SQL_TYPE']."_ID = '".addslashes($ARR['NB_ID'])."'
                LEFT JOIN NT_MAIN.NMG_GARAGE_PART NGP
                ON  NGP.MGG_ID = '".addslashes($ARR['MGG_ID'])."'
                WHERE CPP.CPP_USE_YN = 'Y'
                AND CPP.DEL_YN = 'N'
                GROUP BY NBP.CPPS_ID, NBP.".$ARR['SQL_TYPE']."_ID, CPP.CPP_ID
                ORDER BY CPP.CPP_ORDER_NUM ASC;
                ;"
        ;
       
        $Result = DBManager::getResult($iSQL);
        // echo $iSQL;
        $rtnArray = array();
        while($RS = $Result->fetch_array()){
            array_push($rtnArray,
                array(
                    "CPPS_ID"=>$RS['CPPS_ID'],
                     "CPP_UNIT"=>$RS['CPP_UNIT'],
                    "CPP_NM"=>$RS['CPP_NM'],
                    "CPP_PART_TYPE"=>$RS['CPP_PART_TYPE'],
                    "CPP_NM_SHORT"=>$RS['CPP_NM_SHORT'],
                    "CPP_ID"=>$RS['CPP_ID'],
                    "CPPS_NM"=>$RS['CPPS_NM'],
                    "NBP_USE_CNT"=>$RS['NBP_USE_CNT'],
                    "NBP_UNIT_PRICE"=>$RS['NBP_UNIT_PRICE'],
                    "NBP_TOTAL_PRICE"=>$RS['NBP_TOTAL_PRICE'],

            ));
        }

        $Result->close();  // 자원 반납
        unset($RS);

        return  $rtnArray;
    }

    function getBillWorkInfo($ARR){
        $iSQL = "SELECT 
                        NBW.RRT_ID,
                        NBW.RWPP_ID,
                        NBW.NWP_ID,
						NBP.CPPS_ID,
                        NT_CODE.GET_RRT_NM(NBW.RRT_ID) AS RRT_NM,
                        NT_CODE.GET_RWP_NM(NBW.NWP_ID) AS RWP_NM,
                        NT_CODE.GET_RWPP_NM(NBW.RWPP_ID) AS RWPP_NM,
                        NWP.CPP_ID,
                        (SELECT CPP_NM FROM NT_CODE.NCP_PART WHERE CPP_ID = NWP.CPP_ID) AS CPP_NM,
                        NT_CODE.GET_CPPS_NM(NBP.CPPS_ID)AS CPPS_NM,
                        FORMAT(NBP.".$ARR['SQL_TYPE']."P_UNIT_PRICE,0) AS NBP_UNIT_PRICE ,
                        NBP.".$ARR['SQL_TYPE']."P_USE_CNT,
						FORMAT(ROUND((NBP.".$ARR['SQL_TYPE']."P_UNIT_PRICE*NBP.".$ARR['SQL_TYPE']."P_USE_CNT)),0) AS NBP_TOTAL_PRICE,
                        NPS.CPPS_UNIT,
                        (
							SELECT COUNT(CPP_ID) 
                            FROM NT_BILL.NBB_". $ARR['TABLE_NM']."_WORK_PART
                            WHERE NP.CPP_ID = NWP.CPP_ID 
								AND ".$ARR['SQL_TYPE']."_ID = '".addslashes($ARR['NB_ID'])."' 
								AND CPP_ID = NWP.CPP_ID
								AND NWP.NWP_ID NOT IN (SELECT NWP_ID
										FROM 
											NT_BILL.NBB_". $ARR['TABLE_NM']."_WORK WHERE ".$ARR['SQL_TYPE']."W_TIME = 0   AND ".$ARR['SQL_TYPE']."_ID = '".addslashes($ARR['NB_ID'])."' ) 
						) AS ROW_SPAN,
                        NP.CPP_ORDER_NUM,
                        RWP.RWT_ID,
                        (NT_BILL.GET_".$ARR['SQL_TYPE']."_TIME_M('".addslashes($ARR['NB_ID'])."' ,'".addslashes($ARR['CCC_CAR_TYPE_CD'])."' , NWP.CPP_ID, NBW.NWP_ID, NBW.RRT_ID, NBW.RWPP_ID))AS NRT_TIME_M,
                        (SELECT CPP_PART_TYPE FROM NT_CODE.NCP_PART WHERE CPP_ID = NWP.CPP_ID) AS CPP_PART_TYPE,
                        (
							SELECT ROUND(NRPS_RATIO ,2)
                            FROM NT_CODE.NCT_RECKON_PART_SUB
                            WHERE CPP_ID = NWP.CPP_ID 
                            AND CPPS_ID = NBP.CPPS_ID
                            AND RCT_CD = '".addslashes($ARR['CCC_CAR_TYPE_CD'])."' 
                            AND RWP_ID = NRT.RWP_ID
                            AND RRT_ID = NBW.RRT_ID
						) AS NRPS_RATIO
                FROM 
                    NT_CODE.NCT_RECKON_TIME NRT,
                    NT_CODE.NCT_RECKON_PART NRP,
                    NT_CODE.NCW_WORK_PART RWP,
                    NT_BILL.NBB_". $ARR['TABLE_NM']."_PARTS NBP,
                    NT_CODE.NCP_PART NP,
					NT_CODE.NCP_PART_SUB NPS,
                    NT_BILL.NBB_". $ARR['TABLE_NM']."_WORK  NBW,
                    NT_BILL.NBB_". $ARR['TABLE_NM']."_WORK_PART  NWP
				WHERE NBW.".$ARR['SQL_TYPE']."_ID = NWP.".$ARR['SQL_TYPE']."_ID 
					AND NBW.".$ARR['SQL_TYPE']."_ID = '".addslashes($ARR['NB_ID'])."' 
					AND NWP.".$ARR['SQL_TYPE']."_ID  = '".addslashes($ARR['NB_ID'])."'  
					AND NBP.".$ARR['SQL_TYPE']."_ID  = '".addslashes($ARR['NB_ID'])."'  
					AND NBW.NWP_ID = NWP.NWP_ID
					AND RWP.NWP_ID = NBW.NWP_ID
					AND NWP.CPP_ID = NBP.CPP_ID
					AND NRT.CPP_ID =  NWP.CPP_ID 
					AND NRT.RWP_ID = NWP.NWP_ID
					AND NRT.RRT_ID = NBW.RRT_ID
					AND NRT.RCT_CD = '".addslashes($ARR['CCC_CAR_TYPE_CD'])."' 
					AND NRP.CPP_ID =  NWP.CPP_ID 
					AND NRP.RWP_ID = NWP.NWP_ID
					AND NRP.RRT_ID = NBW.RRT_ID
					AND NRP.RCT_CD = '".addslashes($ARR['CCC_CAR_TYPE_CD'])."'   
					AND NBP.CPP_ID = NWP.CPP_ID 
					AND NP.CPP_ID = NWP.CPP_ID 
					AND NPS.CPPS_ID = NBP.CPPS_ID
                GROUP BY NBW.RRT_ID, NBW.RWPP_ID ,NBW.NWP_ID, CPP_PART_TYPE, CPP_ID
                ORDER BY NBW.NWP_ID ASC
                ;"
        ;
        $Result = DBManager::getResult($iSQL);
                            // echo $iSQL;
        
        $rtnArray = array();
        while($RS = $Result->fetch_array()){
            array_push($rtnArray,
                array(
                    "CPP_PART_TYPE"=>$RS['CPP_PART_TYPE'],
                    "RWPP_ID"=>$RS['RWPP_ID'],
                    "CPP_ORDER_NUM"=>$RS['CPP_ORDER_NUM'],
                    "NBP_USE_CNT"=>$RS['NBP_USE_CNT'],
                    "NBP_UNIT_PRICE"=>$RS['NBP_UNIT_PRICE'],
                    "NBP_TOTAL_PRICE"=>$RS['NBP_TOTAL_PRICE'],
                    "ROW_SPAN"=>$RS['ROW_SPAN'],
                    "CPPS_UNIT"=>$RS['CPPS_UNIT'],
                    "CPP_NM"=>$RS['CPP_NM'],
                    "CPPS_NM"=>$RS['CPPS_NM'],
                    "CPP_ID"=>$RS['CPP_ID'],
                    "RWT_ID"=>$RS['RWT_ID'],
                    "NWP_ID"=>$RS['NWP_ID'],
                    "RRT_ID"=>$RS['RRT_ID'],
                    "RRT_NM"=>$RS['RRT_NM'],
                    "RWP_NM"=>$RS['RWP_NM'],
                    "RWPP_NM"=>$RS['RWPP_NM'],
                    "NRT_TIME_M"=>$RS['NRT_TIME_M'],
                    "NRPS_RATIO"=>$RS['NRPS_RATIO'],

            ));
        }
        $Result->close();  // 자원 반납
        unset($RS);
        return  $rtnArray;
    }


    function getBillInfo($ARR){
        // FORMAT((NB.".$ARR['SQL_TYPE']."_TOTAL_PRICE- NB.".$ARR['SQL_TYPE']."_VAT_PRICE)-MOD(NB.".$ARR['SQL_TYPE']."_TOTAL_PRICE- NB.".$ARR['SQL_TYPE']."_VAT_PRICE,10),0) AS NB_CLAIM_PRICE,
        $SUB_SQL = "";
        if($ARR['SQL_TYPE'] == 'NB'){
            $SUB_SQL .= "\n IF(NB.NB_AB_BILL_YN IS NULL , 'N','Y')AS NB_AB_BILL_YN,";
            $SUB_SQL .= "\n IFNULL(NB.NB_AB_NSRSN,'')AS NB_AB_NSRSN,";
            $SUB_SQL .= "\n IFNULL(NB.NB_AB_NSRSN_VAL,'')AS NB_AB_NSRSN_VAL,";
            $SUB_SQL .= "\n IF(NB.NB_AB_EST_SEQ2 IS NOT NULL , CONCAT(NB.NB_AB_EST_SEQ1, ',',  NB.NB_AB_EST_SEQ2) , NB.NB_AB_EST_SEQ1)AS NB_AB_EST_SEQ,";
            $SUB_SQL .= "\n IFNULL(NB.NB_AB_EST_SEQ1,'')AS NB_AB_EST_SEQ1,";
            $SUB_SQL .= "\n IFNULL(NB.NB_AB_EST_SEQ2,'')AS NB_AB_EST_SEQ2,";
            $SUB_SQL .= "\n NB.NB_AB_VIEW_URI,";
        }
        $iSQL = "SELECT 
                    NB.".$ARR['SQL_TYPE']."_GARAGE_NM,
                    NT_MAIN.GET_MGG_NM(NB.MGG_ID)AS MGG_NM,
                    NB.".$ARR['SQL_TYPE']."_CAR_NUM,
                    NB.".$ARR['SQL_TYPE']."_NEW_INSU_YN,
                    NB.".$ARR['SQL_TYPE']."_INC_DTL_YN,
                    NT_BILL.GET_".$ARR['SQL_TYPE']."_WORK_TIME(NB.".$ARR['SQL_TYPE']."_ID)AS NBW_TIME,
                    NT_MAIN.GET_INSU_CHG_TYPE_ID(NBI.NIC_ID)AS RICT_ID,
                    NT_CODE.GET_RICT_NM(NT_MAIN.GET_INSU_CHG_TYPE_ID(NBI.NIC_ID)) AS RICT_NM,
                    NT_BILL.GET_NIC_CLAIM_COUNT(NBI.NIC_ID, NB.MGG_ID) AS CLAIM_COUNT,
                    DATE_FORMAT((SELECT WRT_DTHMS FROM NT_MAIN.NMI_INSU_CHARGE WHERE NIC_ID = NBI.NIC_ID),'%Y-%m-%d') AS CLAIM_WRT_DTHMS,
                    NB.RLT_ID,
                    NB.RMT_ID,
                    NB.MGG_ID,
                    NB.".$ARR['SQL_TYPE']."_VAT_DEDUCTION_YN,
                    NB.CCC_ID,
                    NT_CODE.GET_CAR_NM(NB.CCC_ID) AS CCC_NM,
                    NT_CODE.GET_CAR_TYPE_NM(NB.CCC_ID) AS RCT_NM,
                    NCC.CCC_CAR_TYPE_CD,
                    NB.".$ARR['SQL_TYPE']."_CLAIM_DT,
                    @MAX_PRICE := (SELECT PCE_CURRENT_LIMIT_PRICE FROM NT_CODE.PCY_CLAIM_ENV),
                    FORMAT(ROUND(NB.".$ARR['SQL_TYPE']."_TOTAL_PRICE- NB.".$ARR['SQL_TYPE']."_VAT_PRICE,-1),0) AS NB_CLAIM_PRICE,
                    FORMAT(IF(NB.".$ARR['SQL_TYPE']."_TOTAL_PRICE > @MAX_PRICE , @MAX_PRICE , NB.".$ARR['SQL_TYPE']."_TOTAL_PRICE ) ,0)AS NB_TOTAL_PRICE,
                    FORMAT(NB.".$ARR['SQL_TYPE']."_VAT_PRICE,0) AS NB_VAT_PRICE,
                    ROUND(IF(NB.".$ARR['SQL_TYPE']."_NEW_INSU_YN='Y',IF(NB.RLT_ID='11430', ".$ARR['SQL_TYPE']."_TOTAL_PRICE*0.2,0),0)) AS OWNER_CLAIM_PRICE,
                    FORMAT(ROUND(IF(NB.".$ARR['SQL_TYPE']."_NEW_INSU_YN='Y',IF(NB.RLT_ID='11430', ".$ARR['SQL_TYPE']."_TOTAL_PRICE*0.2,0),0)),0) AS OWNER_CLAIM_FORMAT,
                    NBI.".$ARR['SQL_TYPE']."I_RATIO,
                    NBI.".$ARR['SQL_TYPE']."I_SEND_FAX_YN,
                    NBI.MII_ID,
                    NT_CODE.GET_RMT_NM(NBI.NBI_RMT_ID, true) AS NBI_RMT_NM,
                    NBI.".$ARR['SQL_TYPE']."I_REGI_NUM,
                    NBI.".$ARR['SQL_TYPE']."I_CHG_NM,
                    NBI.".$ARR['SQL_TYPE']."I_FAX_NUM,
                    NBI.NBI_RMT_ID,
                    NT_MAIN.GET_INSU_NM(NBI.MII_ID) AS MII_NM,
                    NBI.".$ARR['SQL_TYPE']."I_RATIO,
                    NBI.NIC_ID,
                    NB.".$ARR['SQL_TYPE']."_MEMO_TXT,
                    NB.".$ARR['SQL_TYPE']."_CLAIM_USERID,
                    $SUB_SQL
                    NB.WRT_DTHMS
                        
                FROM 
                    NT_CODE.NCC_CAR NCC,
                    NT_BILL.NBB_". $ARR['TABLE_NM']." NB
                RIGHT JOIN NT_BILL.NBB_".$ARR['TABLE_NM']."_INSU NBI
                ON NB.".$ARR['SQL_TYPE']."_ID = NBI.".$ARR['SQL_TYPE']."_ID
                WHERE  NB.".$ARR['SQL_TYPE']."_ID = '".addslashes($ARR['NB_ID'])."'
                AND NCC.CCC_ID = NB.CCC_ID
                ;"
        ;
        // echo $iSQL;
        $Result = DBManager::getResult($iSQL);

        $rtnArray = array();
        while($RS = $Result->fetch_array()){
            array_push($rtnArray,
                array(
                    "NIC_ID"=>$RS['NIC_ID'],
                    "NBI_REGI_NUM"=>$RS[$ARR['SQL_TYPE']."I_REGI_NUM"],
                    "NB_GARAGE_NM"=>$RS[$ARR['SQL_TYPE'].'_GARAGE_NM'],
                    "MGG_NM"=>$RS['MGG_NM'],
                    "NBI_RMT_ID"=>$RS['NBI_RMT_ID'],
                    "NBI_SEND_FAX_YN"=>$RS[$ARR['SQL_TYPE'].'I_SEND_FAX_YN'],
                    "NB_INC_DTL_YN"=>$RS[$ARR['SQL_TYPE'].'_INC_DTL_YN'],
                    "NBW_TIME"=>$RS['NBW_TIME'],
                    "NB_NEW_INSU_YN"=>$RS[$ARR['SQL_TYPE'].'_NEW_INSU_YN'],
                    "NB_CAR_NUM"=>$RS[$ARR['SQL_TYPE'].'_CAR_NUM'],
                    "WRT_DTHMS"=>$RS['WRT_DTHMS'],
                    "NB_CLAIM_USERID"=>$RS[$ARR['SQL_TYPE'].'_CLAIM_USERID'],
                    "CLAIM_COUNT"=>$RS['CLAIM_COUNT'],
                    "CLAIM_WRT_DTHMS"=>$RS['CLAIM_WRT_DTHMS'],
                    "RICT_ID"=>$RS['RICT_ID'],
                    "RICT_NM"=>$RS['RICT_NM'],
                    "CCC_NM"=>$RS['CCC_NM'],
                    "CCC_ID"=>$RS['CCC_ID'],
                    "MGG_ID"=>$RS['MGG_ID'],
                    "RLT_ID"=>$RS['RLT_ID'],
                    "MII_ID"=>$RS['MII_ID'],
                    "CCC_CAR_TYPE_CD"=>$RS['CCC_CAR_TYPE_CD'],
                    "NB_VAT_DEDUCTION_YN"=>$RS[$ARR['SQL_TYPE'].'_VAT_DEDUCTION_YN'],
                    "RCT_NM"=>$RS['RCT_NM'],
                    "RMT_ID"=>$RS['RMT_ID'],
                    "NB_CLAIM_DT"=>$RS[$ARR['SQL_TYPE'].'_CLAIM_DT'],
                    "NB_CLAIM_PRICE"=>$RS['NB_CLAIM_PRICE'],
                    "NB_TOTAL_PRICE"=>$RS['NB_TOTAL_PRICE'],
                    "NB_VAT_PRICE"=>$RS['NB_VAT_PRICE'],
                    "OWNER_CLAIM_PRICE"=>$RS['OWNER_CLAIM_PRICE'],
                    "OWNER_CLAIM_FORMAT"=>$RS['OWNER_CLAIM_FORMAT'],
                    "NBI_RATIO"=>$RS[$ARR['SQL_TYPE'].'I_RATIO'],
                    "NBI_RMT_NM"=>$RS['NBI_RMT_NM'],
                    "NBI_CHG_NM"=>$RS[$ARR['SQL_TYPE'].'I_CHG_NM'],
                    "NBI_FAX_NUM"=>$RS[$ARR['SQL_TYPE'].'I_FAX_NUM'],
                    "MII_NM"=>$RS['MII_NM'],
                    "NB_MEMO_TXT"=>$RS[$ARR['SQL_TYPE'].'_MEMO_TXT'],
                    "NB_AB_BILL_YN"=>$RS['NB_AB_BILL_YN'],
                    "NB_AB_NSRSN"=>$RS['NB_AB_NSRSN'],
                    "NB_AB_NSRSN_VAL"=>$RS['NB_AB_NSRSN_VAL'],
                    "NB_AB_VIEW_URI"=>$RS['NB_AB_VIEW_URI'],
                    "NB_AB_EST_SEQ"=>$RS['NB_AB_EST_SEQ'],
                    "NB_AB_EST_SEQ1"=>$RS['NB_AB_EST_SEQ1'],
                    "NB_AB_EST_SEQ2"=>$RS['NB_AB_EST_SEQ2'],
            ));
        }

        $Result->close();  // 자원 반납
        unset($RS);
        return  $rtnArray;
    }

    function getList($ARR){
        $rtnArray = array();

        if($ARR['SQL_TYPE'] == 'NQ'){
            $ARR['TABLE_NM'] = "QUOTE";
            $ARR['SQL_TYPE'] = "NQ";
        }else{
            $ARR['TABLE_NM'] = "BILL";
            $ARR['SQL_TYPE'] = "NB";
        }

        $billResult = $this->getBillInfo($ARR);                            // 해당청구서정보(보험사정보 포함)
        if(!empty($billResult)){   
            $ARR['CCC_CAR_TYPE_CD'] = $billResult[0]['CCC_CAR_TYPE_CD'];
            $billWorkResult = $this->getBillWorkInfo($ARR);                // 해당청구서 작업시간/작업현황(계산된부분!)
            // echo "11111===>".count( $billWorkResult )."<br>";
            $billPartsResult = $this->getBillPartsInfo($ARR);              // 사용부품 사용량
            // echo "22222===>".count( $billPartsResult )."<br>";
            // $billWorkPartsResult = $this->getBillWorkPartInfo($ARR);
            $billWorkPartsDetailResult  = $this->getWorkPartsDetail($ARR); // 작업현황 체크된 부품(계산안된것도 포함)

            array_push($rtnArray,$billResult);
            array_push($rtnArray,$billWorkResult);
            array_push($rtnArray,$billPartsResult);
            // array_push($rtnArray,$billWorkPartsResult);
            array_push($rtnArray,$billWorkPartsDetailResult);

            return $rtnArray;
        }else{
            return HAVE_NO_DATA;
        }
        
    }


    function getWorkPartsDetail($ARR){
        $iSQL = "SELECT 
                    NBP.".$ARR['SQL_TYPE']."_ID,
                    NBP.NWP_ID,
                    NBW.RRT_ID,
                    NBW.RWPP_ID,
                    NBP.CPP_ID
                FROM NT_BILL.NBB_". $ARR['TABLE_NM']."_WORK_PART  NBP,
                    NT_BILL.NBB_". $ARR['TABLE_NM']."_WORK NBW
                WHERE NBP.".$ARR['SQL_TYPE']."_ID = '".addslashes($ARR['NB_ID'])."'
                AND NBP.".$ARR['SQL_TYPE']."_ID = NBW.".$ARR['SQL_TYPE']."_ID 
                AND NBP.NWP_ID = NBW.NWP_ID 
                
            ;"
        ;

        $Result = DBManager::getResult($iSQL);

        $rtnArray = array();
        while($RS = $Result->fetch_array()){
        array_push($rtnArray,
             array(
                "NB_ID"=>$RS['NB_ID'],
                "NWP_ID"=>$RS['NWP_ID'],
                 "RRT_ID"=>$RS['RRT_ID'],
                "RWPP_ID"=>$RS['RWPP_ID'],
                "CPP_ID"=>$RS['CPP_ID']

            ));
        }

        $Result->close();  // 자원 반납
        unset($RS);

        return  $rtnArray;
    }

    /** 팩스상태업데이트 */
    function updateFaxStatus($ARR){
        $FAX_RFS_ID = empty($ARR['FAX_RFS_ID']) ? '90210' :  "'".addslashes($ARR['FAX_RFS_ID'])."'";

        $iSQL = "UPDATE NT_BILL.NBB_BILL_INSU
                  SET  RFS_ID = '".addslashes($ARR['RFS_ID'])."',
                  NBI_FAX_SEND_DTHMS = now()
                WHERE NB_ID = '".addslashes($ARR['NB_ID'])."'
                AND NBI_RMT_ID = '".addslashes($ARR['NBI_FAX_RMT_ID'])."'
                ;"
        ;
        // echo $iSQL;
        return DBManager::execQuery($iSQL); 
	}
    


}
	// 0. 객체 생성
	$clsBillInfo = new clsBillInfo(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$clsBillInfo->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
