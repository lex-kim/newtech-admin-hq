<?php

class clsSaState extends DBManager {

	private $DATA_SAVE_PATH;				// 저장경로.
	private $ALLOW_FILE_EXT;				// 업로드 가능 확장자

	// Class Object Constructor
	function clsSaState($DATA_SAVE_PATH,$ALLOW_FILE_EXT) {
		$this->DATA_SAVE_PATH = $DATA_SAVE_PATH;
		$this->ALLOW_FILE_EXT = $ALLOW_FILE_EXT;
	}

	function getSearchSQL($ARR){
		$tmpKeyword = "";

		/** 기간검색 */
		if(!empty($ARR['SELECT_DT'])){
			$TABLE_NM = explode('_', $ARR['SELECT_DT'])[0];
			if($TABLE_NM == 'MGG'){
				$TABLE_NM = 'MGGS';
			}
			if($ARR['START_DT'] != '' || $ARR['END_DT'] != '') {
				if($ARR['START_DT'] !='' && $ARR['END_DT'] !=''){
					$tmpKeyword .= " AND DATE($TABLE_NM.`".$ARR['SELECT_DT']."`) BETWEEN '".addslashes($ARR['START_DT'])."' AND '".addslashes($ARR['END_DT'])."'";
				}else if($ARR['START_DT'] !='' && $ARR['END_DT'] ==''){
					$tmpKeyword .= " AND DATE($TABLE_NM.`".$ARR['SELECT_DT']."`) >= '".addslashes($ARR['START_DT'])."'";
				}else{
					$tmpKeyword .= " AND DATE($TABLE_NM.`".$ARR['SELECT_DT']."`) <= '".addslashes($ARR['END_DT'])."'";
				}
			} 
		}

		// 구분검색
		if($ARR['CSD_ID_SEARCH'] != ''){
			$tmpKeyword .= " AND MGG.CSD_ID IN (";
			foreach ($ARR['CSD_ID_SEARCH'] as $index => $CSD_ARRAY) {
				$tmpKeyword .=" '".$CSD_ARRAY."',";
			}
			$tmpKeyword=substr($tmpKeyword, 0, -1); // 마지막 콤마제거
			$tmpKeyword .= ") \n";
		}

		//지역검색
		if($ARR['CRR_ID_SEARCH'] !=  '' || $ARR['CRR_ID_SEARCH'] != null) {

			$tmpKeyword .= " AND (";
			foreach ($ARR['CRR_ID_SEARCH'] as $index => $CRD_ARRAY) {
				$CSD = explode(',', $CRD_ARRAY);
				
				if($index == 0){
					$tmpKeyword .= "(";
					$tmpKeyword .= "`MGG`.`CRR_1_ID` = '".$CSD[0]."' \n";
				}else{
					$tmpKeyword .= " OR(";
					$tmpKeyword .= "  `MGG`.`CRR_1_ID` = '".$CSD[0]."' \n";
				}
				if($CSD[1] != NOT_CRR_2){
					$tmpKeyword .= " AND `MGG`.`CRR_2_ID` = '".$CSD[1]."' \n";
				}
				
				if($CSD[2] != NOT_CRR_3){
					$tmpKeyword .= " AND `MGG`.`CRR_3_ID` = '".$CSD[2]."' \n";
				}
				
				$tmpKeyword .= ") \n";
			}
			$tmpKeyword .= ") \n";
		}

		//공업사명검색
		if($ARR['MGG_NM_SEARCH'] != '') {
			$tmpKeyword .= "AND INSTR(MGG.MGG_NM ,'".addslashes($ARR['MGG_NM_SEARCH'])."')>0 ";
		} 

		/** 방문주기 */
		if(!empty($ARR['VISIT_CYCLE'])){
			$CYCLE_TXT = "";
			if($ARR['VISIT_CYCLE'] == "1"){
				$CYCLE_TXT = "월1회";
			}else if($ARR['VISIT_CYCLE'] == "2"){
				$CYCLE_TXT = "월2회";
			}else{
				$CYCLE_TXT = "홀월";
			}
			$tmpKeyword .= " AND NT_MAIN.GET_MGG_VISIT_CYCLE(MGG.MGG_ID) = '".$CYCLE_TXT."'";
		}

		/** 방문여부 */
		if(!empty($ARR['VISIT_YN'])){
			if($ARR['VISIT_YN'] == "Y"){
				$tmpKeyword .= " AND NSU.MGG_ID = MGG.MGG_ID";
			}else{
				$tmpKeyword .= " AND NSU.MGG_ID != MGG.MGG_ID";
			}
		}

		/** 임원방문월-올해 */
		if(!empty($ARR['NGV_VISIT_YEAR'])){
			$tmpKeyword .= " AND INSTR(NT_MAIN.GET_MGG_VISIT_MONTH(YEAR(NOW()), MGG.MGG_ID) , '".$ARR['NGV_VISIT_YEAR']."') >0";
		}

		/** 임원방문월-작년 */
		if(!empty($ARR['NGV_VISIT_LAST_YEAR'])){
			$tmpKeyword .= " AND INSTR(NT_MAIN.GET_MGG_VISIT_MONTH(YEAR(DATE_ADD(NOW(), INTERVAL -1 YEAR)), MGG.MGG_ID) , '".$ARR['NGV_VISIT_LAST_YEAR']."') > 0";
		}

		/** 청구도과일 */
		if($ARR['START_DT2'] != '' || $ARR['END_DT2'] != '') {
			if($ARR['START_DT2'] !='' && $ARR['END_DT2'] !=''){
				$tmpKeyword .= " AND DATEDIFF(DATE_FORMAT(NOW(),'%Y-%m-%d'), (SELECT MAX(NB_CLAIM_DT) FROM NT_BILL.NBB_BILL  WHERE MGG_ID = MGG.MGG_ID)) BETWEEN ".addslashes($ARR['START_DT2'])." AND ".addslashes($ARR['END_DT2'])."";
			}else if($ARR['START_DT2'] !='' && $ARR['END_DT2'] ==''){
				$tmpKeyword .= " AND DATEDIFF(DATE_FORMAT(NOW(),'%Y-%m-%d'), (SELECT MAX(NB_CLAIM_DT) FROM NT_BILL.NBB_BILL  WHERE MGG_ID = MGG.MGG_ID)) >= ".addslashes($ARR['START_DT2'])."";
			}else{
				$tmpKeyword .= " AND DATEDIFF(DATE_FORMAT(NOW(),'%Y-%m-%d'), (SELECT MAX(NB_CLAIM_DT) FROM NT_BILL.NBB_BILL  WHERE MGG_ID = MGG.MGG_ID))<=".addslashes($ARR['END_DT2'])."";
			}
		} 


		/** 평균건수 */
		if($ARR['START_3MONTH_NUM'] != '' || $ARR['END_3MONTH_NUM'] != '') {
			if($ARR['START_3MONTH_NUM'] !='' && $ARR['END_3MONTH_NUM'] !=''){
				$tmpKeyword .= " AND NT_BILL.GET_3MONTH_AVG_BILL_INFO(MGG.MGG_ID, 'C') BETWEEN ".addslashes($ARR['START_3MONTH_NUM'])." AND '".addslashes($ARR['END_3MONTH_NUM'])."'";
			}else if($ARR['START_3MONTH_NUM'] !='' && $ARR['END_3MONTH_NUM'] ==''){
				$tmpKeyword .= " AND NT_BILL.GET_3MONTH_AVG_BILL_INFO(MGG.MGG_ID, 'C') >= ".addslashes($ARR['START_3MONTH_NUM'])."";
			}else{
				$tmpKeyword .= " AND NT_BILL.GET_3MONTH_AVG_BILL_INFO(MGG.MGG_ID, 'C')<= ".addslashes($ARR['END_3MONTH_NUM'])."";
			}
		} 

		/** 평균금액 */
		if($ARR['START_3MONTH_PRICE'] != '' || $ARR['END_3MONTH_PRICE'] != '') {
			if($ARR['START_3MONTH_PRICE'] !='' && $ARR['END_3MONTH_PRICE'] !=''){
				$tmpKeyword .= " AND NT_BILL.GET_3MONTH_AVG_BILL_INFO(MGG.MGG_ID, 'P') BETWEEN ".addslashes($ARR['START_3MONTH_PRICE'])." AND '".addslashes($ARR['END_3MONTH_PRICE'])."'";
			}else if($ARR['START_3MONTH_PRICE'] !='' && $ARR['END_3MONTH_PRICE'] ==''){
				$tmpKeyword .= " AND NT_BILL.GET_3MONTH_AVG_BILL_INFO(MGG.MGG_ID, 'P') >= ".addslashes($ARR['START_3MONTH_PRICE'])."";
			}else{
				$tmpKeyword .= " AND NT_BILL.GET_3MONTH_AVG_BILL_INFO(MGG.MGG_ID, 'P')<= ".addslashes($ARR['END_3MONTH_PRICE'])."";
			}
		} 

		if(!empty($ARR['MGG_BIZ_ID_SEARCH'])) {
			$tmpKeyword .= " AND INSTR(`MGG`.`MGG_BIZ_ID`,'".addslashes($ARR['MGG_BIZ_ID_SEARCH'])."')>0 ";
		}

		//청구식검색
		if($ARR['RET_ID_SEARCH'] != '') {
			$tmpKeyword .= " AND MGGS.RET_ID IN ( ";
			foreach ($ARR['RET_ID_SEARCH'] as $index => $RET_ID) {
				$tmpKeyword .=" '".$RET_ID."',";
			}
			$tmpKeyword = substr($tmpKeyword, 0, -1); // 마지막 콤마제거
			$tmpKeyword .= ") \n";
		}

		/**AOS 권한 */
		if($ARR['RAT_ID_SEARCH'] != '') {
			$tmpKeyword .= " AND `MGGS`.`RAT_ID` = '".addslashes($ARR['RAT_ID_SEARCH'])."' ";
		}

		/** 택배 */
		if($ARR['MGG_LOGISTICS_YN'] != '') {
			$tmpKeyword .= " AND `MGGS`.`MGG_LOGISTICS_YN` = '".addslashes($ARR['MGG_LOGISTICS_YN'])."' ";
		}

		// 주요정비
		if($ARR['RMT_ID_SEARCH'] != '') {
			$tmpKeyword .= " AND `MGGS`.`RMT_ID` = '".addslashes($ARR['RMT_ID_SEARCH'])."' ";
		}

		// 자가여부
		if($ARR['ROT_ID_SEARCH'] != '') {
			$tmpKeyword .= " AND `MGGS`.`ROT_ID` = '".addslashes($ARR['ROT_ID_SEARCH'])."' ";
		}

		// 청구규모
		if($ARR['CLAIM_SCALE'] != '') {
			if($ARR['CLAIM_SCALE'] == "1"){ //대
				$tmpKeyword .=  "AND NT_BILL.GET_3MONTH_AVG_BILL_INFO(MGG.MGG_ID, 'C') >= 40";
			}else if($ARR['CLAIM_SCALE'] == "2"){  //중
				$tmpKeyword .= " AND  (NT_BILL.GET_3MONTH_AVG_BILL_INFO(MGG.MGG_ID, 'C') >= 10";
				$tmpKeyword .= " AND  NT_BILL.GET_3MONTH_AVG_BILL_INFO(MGG.MGG_ID, 'C') < 40)";
			}else{  //소
				$tmpKeyword .= " AND  (NT_BILL.GET_3MONTH_AVG_BILL_INFO(MGG.MGG_ID, 'C') >= 0";
				$tmpKeyword .= " AND  NT_BILL.GET_3MONTH_AVG_BILL_INFO(MGG.MGG_ID, 'C') < 10)";
			}
			
		}

		//주소
		if($ARR['MGG_ADDRESS_SEARCH'] != '') {
			$tmpKeyword .= " AND (INSTR(MGGS.MGG_ADDRESS_OLD_1 ,'".addslashes($ARR['MGG_ADDRESS_SEARCH'])."')>0 ";
			$tmpKeyword .= " OR INSTR(MGGS.MGG_ADDRESS_OLD_2 ,'".addslashes($ARR['MGG_ADDRESS_SEARCH'])."')>0 ";
			$tmpKeyword .= " OR INSTR(MGGS.MGG_ADDRESS_NEW_1 ,'".addslashes($ARR['MGG_ADDRESS_SEARCH'])."')>0 ";
			$tmpKeyword .= " OR INSTR(MGGS.MGG_ADDRESS_NEW_2 ,'".addslashes($ARR['MGG_ADDRESS_SEARCH'])."')>0 ";
			$tmpKeyword .= ")";
		} 

		// 협렵업체-보험사
		if(!empty($ARR['MGG_INSU_SEARCH'])) {
			$tmpKeyword .= " AND INSTR(`MGGS`.`MGG_INSU_MEMO`,'".addslashes($ARR['MGG_INSU_SEARCH'])."')>0 ";
		}

		// 협렵업체-제조사
		if(!empty($ARR['MGG_MANUFACTURE_SEARCH'])) {
			$tmpKeyword .= " AND INSTR(`MGGS`.`MGG_MANUFACTURE_MEMO`,'".addslashes($ARR['MGG_MANUFACTURE_SEARCH'])."')>0 ";
		}

		// 거래여부
		if($ARR['RCT_ID_SEARCH'] != '') {
			$tmpKeyword .= " AND MGGS.RCT_ID IN ( ";
			foreach ($ARR['RCT_ID_SEARCH'] as $index => $RCT_ID) {
				$tmpKeyword .=" '".$RCT_ID."',";
			}
			$tmpKeyword = substr($tmpKeyword, 0, -1); // 마지막 콤마제거
			$tmpKeyword .= ") \n";
		}

		// 개척자
		if(!empty($ARR['MGG_FRONTIER_NM_SEARCH'])) {
			$tmpKeyword .= " AND INSTR(`MGGS`.`MGG_FRONTIER_NM`,'".addslashes($ARR['MGG_FRONTIER_NM_SEARCH'])."')>0 ";
		}

		// 계약여부
		if($ARR['MGG_CONTRACT_YN_SEARCH'] != '') {
			$tmpKeyword .= " AND `MGGS`.`MGG_CONTRACT_YN` = '".addslashes($ARR['MGG_CONTRACT_YN_SEARCH'])."' ";
		}

		// 중단사유
		if($ARR['RSR_ID_SEARCH'] != '') {
			$tmpKeyword .= " AND MGGS.RSR_ID IN ( ";
			foreach ($ARR['RSR_ID_SEARCH'] as $index => $RSR_ID ) {
				$tmpKeyword .=" '".$RSR_ID ."',";
			}
			$tmpKeyword = substr($tmpKeyword, 0, -1); // 마지막 콤마제거
			$tmpKeyword .= ") \n";
		}

		// 타사거래
		if(!empty($ARR['MGG_OTHER_BIZ_NM_SEARCH'])) {
			$tmpKeyword .= " AND INSTR(`MGGS`.`MGG_OTHER_BIZ_NM`,'".addslashes($ARR['MGG_OTHER_BIZ_NM_SEARCH'])."')>0 ";
		}

		// 방문자
		if(!empty($ARR['MGG_SALES_NM_SEARCH'])) {
			$tmpKeyword .= " AND INSTR(`MGGS`.`MGG_SALES_NM`,'".addslashes($ARR['MGG_SALES_NM_SEARCH'])."')>0 ";
		}

		// 규모
		if($ARR['RST_ID_SEARCH'] != '') {
			$tmpKeyword .= " AND `MGGS`.`RST_ID` = '".addslashes($ARR['RST_ID_SEARCH'])."' ";
		}

		// 면담자
		if(!empty($ARR['MGG_SALES_MEET_NM_AES_SEARCH'])) {
			$tmpKeyword .= " AND INSTR(CAST(AES_DECRYPT(`MGGS`.`MGG_SALES_MEET_NM_AES`,UNHEX(SHA2('no1sbxpr!@',256))) AS CHAR),'".addslashes($ARR['MGG_SALES_MEET_NM_AES_SEARCH'])."')>0 ";
		}

		// 영업반응
		if($ARR['RRT_ID_SEARCH'] != '') {
			$tmpKeyword .= " AND `MGGS`.`RRT_ID` = '".addslashes($ARR['RRT_ID_SEARCH'])."' ";
		}

		// 미거래실태
		if(!empty($ARR['MGG_SALES_WHYNOT_SEARCH'])) {
			$tmpKeyword .= " AND INSTR(MGGS.MGG_SALES_WHYNOT,'".addslashes($ARR['MGG_SALES_WHYNOT_SEARCH'])."')>0 ";
		}

		// 최종방문일
		if($ARR['START_LAST_VISIT_DT'] !='' && $ARR['END_LAST_VISIT_DT'] !=''){
			$tmpKeyword .= " AND DATE(NSU.NSU_VISIT_DT) BETWEEN '".addslashes($ARR['START_LAST_VISIT_DT'])."' AND '".addslashes($ARR['END_LAST_VISIT_DT'])."'";
		}else if($ARR['START_LAST_VISIT_DT'] !='' && $ARR['END_LAST_VISIT_DT'] ==''){
			$tmpKeyword .= " AND DATE(NSU.NSU_VISIT_DT) >= '".addslashes($ARR['START_LAST_VISIT_DT'])."'";
		}else if($ARR['START_LAST_VISIT_DT'] =='' && $ARR['END_LAST_VISIT_DT'] !=''){
			$tmpKeyword .= " AND DATE(NSU.NSU_VISIT_DT) <= '".addslashes($ARR['END_LAST_VISIT_DT'])."'";
		}
		return $tmpKeyword ;
	}

	function getHaving($ARR){
		$havingSQL = "HAVING 1=1 ";
		/** 월 건수 */
		if(!empty($ARR['MOTN_AVG_CNT'])) {
			if($ARR['START_AVE'] !='' && $ARR['END_AVE'] !=''){
				$havingSQL .= "AND (`".$ARR['MOTN_AVG_CNT']."` BETWEEN ".addslashes($ARR['START_AVE'])." AND ".addslashes($ARR['END_AVE']).") \n";
			}else if($ARR['START_AVE'] !='' && $ARR['END_AVE'] ==''){
				$havingSQL .= "AND `".$ARR['MOTN_AVG_CNT']."` >= ".addslashes($ARR['START_AVE'])." \n";
			}else if($ARR['START_AVE'] =='' && $ARR['END_AVE'] !=''){
				$havingSQL .= "AND `".$ARR['MOTN_AVG_CNT']."` <= ".addslashes($ARR['END_AVE'])." \n";
			}else{
				$havingSQL .= "AND `".$ARR['MOTN_AVG_CNT']."` >= 0 \n";
			}
		}

		/** 월 금액 */
		if(!empty($ARR['MOTN_AVG_PRICE'])) {
			if($ARR['START_AVE_PRICE'] !='' && $ARR['END_AVE_PRICE'] !=''){
				$havingSQL .= "AND `".$ARR['MOTN_AVG_PRICE']."` BETWEEN ".addslashes($ARR['START_AVE_PRICE'])." AND ".addslashes($ARR['END_AVE_PRICE'])." \n";
			}else if($ARR['START_AVE_PRICE'] !='' && $ARR['END_AVE_PRICE'] ==''){
				$havingSQL .= "AND `".$ARR['MOTN_AVG_PRICE']."` >= ".addslashes($ARR['START_AVE_PRICE'])." \n";
			}else if($ARR['START_AVE_PRICE'] =='' && $ARR['END_AVE_PRICE'] !=''){
				$havingSQL .= "AND `".$ARR['MOTN_AVG_PRICE']."` <= ".addslashes($ARR['END_AVE_PRICE'])." \n";
			}else{
				$havingSQL .= "AND `".$ARR['MOTN_AVG_PRICE']."` >= 0 \n";
			}
		}
		return $havingSQL;
	}

	function getSubList($ARR){
		$tmpKeyword = $this->getSearchSQL($ARR);
		$iSQL = "SELECT 
					CSD.`CSD_ID`, 
					CSD.`CSD_NM` ,
					SUM(IF( NT_MAIN.GET_MGG_VISIT_CYCLE(MGG.MGG_ID)='월2회',1,0)) AS MONTH_2_CNT,
					SUM(IF( NT_MAIN.GET_MGG_VISIT_CYCLE(MGG.MGG_ID)='월1회',1,0)) AS MONTH_1_CNT,
					SUM(IF( NT_MAIN.GET_MGG_VISIT_CYCLE(MGG.MGG_ID)='홀월',1,0)) AS MONTH_ODD_CNT
				FROM 
					`NT_CODE`.`NCS_DIVISION` CSD,
					NT_MAIN.NMG_GARAGE MGG,
					NT_MAIN.NMG_GARAGE_SUB MGGS
				LEFT JOIN (SELECT * FROM NT_SALES.NS_SALES_UPLOAD GROUP BY MGG_ID) NSU
					ON NSU.MGG_ID = MGGS.MGG_ID	
				WHERE CSD.DEL_YN='N'
				AND MGG.DEL_YN = 'N'
				AND CSD.CSD_ID = MGG.CSD_ID 
				AND MGGS.MGG_ID = MGG.MGG_ID
				$tmpKeyword
				GROUP BY MGG.CSD_ID
				ORDER BY CSD.`CSD_ORDER_NUM` 
			;"
		;
		// echo $iSQL;
		$Result = DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			array_push($rtnArray,
				array(
					"CSD_ID"=>$RS['CSD_ID'],
					"CSD_NM"=>$RS['CSD_NM'],
					"MONTH_TOTAL_CNT"=>$RS['MONTH_2_CNT']+$RS['MONTH_1_CNT']+$RS['MONTH_ODD_CNT'],
					"MONTH_2_CNT"=>$RS['MONTH_2_CNT'],
					"MONTH_1_CNT"=>$RS['MONTH_1_CNT'],
					"MONTH_ODD_CNT"=>$RS['MONTH_ODD_CNT'],
				));
			}

		$Result->close();  // 자원 반납
		unset($RS);

		if(empty($rtnArray)){
			return HAVE_NO_DATA;
		}else{
			return $rtnArray;
		}
	}

	/**
	 * 리스트조회
	 */
	function getList($ARR) {
		$LimitStart = ($ARR['CURR_PAGE']-1) * $ARR['PER_PAGE'];
		$LimitEnd   = $ARR['PER_PAGE'];
		$dateSQL = "";
		$tmpKeyword = $this->getSearchSQL($ARR);
		$havingSQL = $this->getHaving($ARR);
		

		// 페이징처리를 위해 전체데이터
		$iSQL = "SELECT COUNT(C.MGG_ID)AS CNT FROM (SELECT 
					MGG.MGG_ID,
					(
						SELECT 
							COUNT(NB.NB_ID)
						FROM NT_BILL.NBB_BILL NB,
						NT_BILL.NBB_BILL_INSU NBI
						WHERE NB.DEL_YN = 'N'
						AND NB.MGG_ID = MGG.MGG_ID
						AND NB.NB_ID = NBI.NB_ID
						AND DATE_FORMAT(NB.NB_CLAIM_DT, '%Y-%m') = DATE_FORMAT(DATE_ADD(NOW(), INTERVAL -1 MONTH), '%Y-%m')
                    )AS D1_BILL_CNT,
                    (
						SELECT 
							IFNULL(SUM(NB_TOTAL_PRICE),0)
						FROM NT_BILL.NBB_BILL
						WHERE DEL_YN = 'N'
						AND MGG_ID =  MGG.MGG_ID
						AND DATE_FORMAT(NB_CLAIM_DT,'%Y-%m') = DATE_FORMAT(DATE_ADD(NOW(), INTERVAL -1 MONTH),'%Y-%m') 
                    )AS D1_BILL_PRICE,
                     (
						SELECT 
							COUNT(NB.NB_ID)
						FROM NT_BILL.NBB_BILL NB,
						NT_BILL.NBB_BILL_INSU NBI
						WHERE NB.DEL_YN = 'N'
						AND NB.MGG_ID = MGG.MGG_ID
						AND NB.NB_ID = NBI.NB_ID
						AND DATE_FORMAT(NB.NB_CLAIM_DT, '%Y-%m') = DATE_FORMAT(DATE_ADD(NOW(), INTERVAL -2 MONTH), '%Y-%m')
                    )AS D2_BILL_CNT,
                    (
						SELECT 
							IFNULL(SUM(NB_TOTAL_PRICE),0)
						FROM NT_BILL.NBB_BILL
						WHERE DEL_YN = 'N'
						AND MGG_ID =  MGG.MGG_ID
						AND DATE_FORMAT(NB_CLAIM_DT,'%Y-%m') = DATE_FORMAT(DATE_ADD(NOW(), INTERVAL -2 MONTH),'%Y-%m') 
                    )AS D2_BILL_PRICE,
                     (
						SELECT 
							COUNT(NB.NB_ID)
						FROM NT_BILL.NBB_BILL NB,
						NT_BILL.NBB_BILL_INSU NBI
						WHERE NB.DEL_YN = 'N'
						AND NB.MGG_ID = MGG.MGG_ID
						AND NB.NB_ID = NBI.NB_ID
						AND DATE_FORMAT(NB.NB_CLAIM_DT, '%Y-%m') = DATE_FORMAT(DATE_ADD(NOW(), INTERVAL -3 MONTH), '%Y-%m')
                    )AS D3_BILL_CNT,
                    (
						SELECT 
							IFNULL(SUM(NB_TOTAL_PRICE),0)
						FROM NT_BILL.NBB_BILL
						WHERE DEL_YN = 'N'
						AND MGG_ID =  MGG.MGG_ID
						AND DATE_FORMAT(NB_CLAIM_DT,'%Y-%m') = DATE_FORMAT(DATE_ADD(NOW(), INTERVAL -3 MONTH),'%Y-%m') 
                    )AS D3_BILL_PRICE,
					(
						SELECT 
							COUNT(NB.NB_ID)
						FROM NT_BILL.NBB_BILL NB,
						NT_BILL.NBB_BILL_INSU NBI
						WHERE NB.DEL_YN = 'N'
						AND NB.MGG_ID = MGG.MGG_ID
						AND NB.NB_ID = NBI.NB_ID
						AND DATE_FORMAT(NB.NB_CLAIM_DT, '%Y-%m') = DATE_FORMAT(DATE_ADD(NOW(), INTERVAL -4 MONTH), '%Y-%m')
                    )AS D4_BILL_CNT,
                    (
						SELECT 
							IFNULL(SUM(NB_TOTAL_PRICE),0)
						FROM NT_BILL.NBB_BILL
						WHERE DEL_YN = 'N'
						AND MGG_ID =  MGG.MGG_ID
						AND DATE_FORMAT(NB_CLAIM_DT,'%Y-%m') = DATE_FORMAT(DATE_ADD(NOW(), INTERVAL -4 MONTH),'%Y-%m') 
                    )AS D4_BILL_PRICE
				FROM NT_MAIN.NMG_GARAGE MGG,
					NT_MAIN.NMG_GARAGE_SUB MGGS
				LEFT JOIN (
					SELECT 
						MGG_ID,
						CONCAT(CPP_NM_SHORT,':' ,GROUP_CONCAT(CPPS_NM_SHORT))AS CPP_NM_SHORT,
						GROUP_CONCAT(IF(NGP_DEFAULT_YN = 'Y' , CPPS_NM_SHORT, '') SEPARATOR '')AS CPPS_NM_SHORT,
						`MGGP`.`NGP_RATIO`
						
					FROM `NT_MAIN`.`NMG_GARAGE_PART` `MGGP`,
						`NT_CODE`.`NCP_PART` `CPP`,
						`NT_CODE`.`NCP_PART_SUB` `CPPS`
					WHERE `MGGP`.`CPP_ID` =  `CPP`.`CPP_ID`
					AND `MGGP`.`CPPS_ID` =  `CPPS`.`CPPS_ID`
					AND CPP.DEL_YN = 'N'
					GROUP BY CPP.CPP_ID, MGGP.MGG_ID
					ORDER BY `CPP`.`CPP_ORDER_NUM` ASC
				)AS MGGP
					ON MGGP.MGG_ID = MGGS.MGG_ID
				LEFT JOIN (SELECT MGG_ID, MAX(NSU_VISIT_DT)AS NSU_VISIT_DT,NSU_SUPPLY_DT FROM NT_SALES.NS_SALES_UPLOAD GROUP BY MGG_ID) NSU
					ON NSU.MGG_ID = MGGP.MGG_ID			
				WHERE MGG.MGG_ID = MGGS.MGG_ID
				AND MGG.DEL_YN = 'N'
				$tmpKeyword 
				GROUP BY MGG_ID
				$havingSQL 
				) AS C
				;"
		;
	
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

		/** 전체리스트가 아닌경우에만 limitSql 적용 */
		$LIMIT_SQL = "";
		if($ARR['REQ_MODE'] == CASE_LIST){
			$LIMIT_SQL = "LIMIT $LimitStart,$LimitEnd";
		}
		
		$iSQL = "SELECT 
					MGG.MGG_ID,
					NT_CODE.GET_CSD_NM(MGG.CSD_ID) AS CSD_NM,
					NT_CODE.GET_CRR_NM(MGG.CRR_1_ID, MGG.CRR_2_ID,'00000') AS CRR_NM,
					MGG.MGG_CIRCUIT_ORDER,
					MGG.MGG_NM,
					NT_MAIN.GET_MGG_VISIT_CYCLE(MGG.MGG_ID) AS VISIT_CYCLE,
                    (
							SELECT 
								IFNULL(GROUP_CONCAT(DATE_FORMAT(NGV_VISIT_DT,'%m')) ,'')
						FROM NT_MAIN.NMG_GARAGE_VISIT 
						WHERE MGG_ID = MGG.MGG_ID
						AND YEAR(NGV_VISIT_DT) = DATE_FORMAT(NOW(),'%Y-%m')
                    )AS THIS_YEAR_VISIT,
                      (
							SELECT 
								IFNULL(GROUP_CONCAT(DATE_FORMAT(NGV_VISIT_DT,'%m')) ,'')
						FROM NT_MAIN.NMG_GARAGE_VISIT 
						WHERE MGG_ID = MGG.MGG_ID
						AND YEAR(NGV_VISIT_DT) = DATE_FORMAT(DATE_ADD(NOW(), INTERVAL -1 YEAR),'%Y-%m')
                    )AS LAST_YEAR_VISIT,
					
                    (
                    SELECT
						COUNT(NSU_ID) 
					FROM NT_SALES.NS_SALES_UPLOAD 
					WHERE MGG_ID = MGG.MGG_ID
					AND DATE_FORMAT(NSU_VISIT_DT,'%Y-%m') = DATE_FORMAT(NOW(),'%Y-%m')
                    )AS THIS_VISIT_CNT,
                    (
						SELECT
							COUNT(NSU_ID) 
						FROM NT_SALES.NS_SALES_UPLOAD 
						WHERE MGG_ID = MGG.MGG_ID
						AND DATE_FORMAT(NSU_SUPPLY_DT,'%Y-%m') = DATE_FORMAT(NOW(),'%Y-%m')
                    )AS THIS_SUPPLY_CNT,
                      (
                    SELECT
						COUNT(NSU_ID) 
					FROM NT_SALES.NS_SALES_UPLOAD 
					WHERE MGG_ID = MGG.MGG_ID
					AND DATE_FORMAT(NSU_VISIT_DT,'%Y-%m') = DATE_FORMAT(DATE_ADD(NOW(), INTERVAL -1 MONTH),'%Y-%m')
                    )AS D1_VISIT_CNT,
                    (
						SELECT
							COUNT(NSU_ID) 
						FROM NT_SALES.NS_SALES_UPLOAD 
						WHERE MGG_ID = MGG.MGG_ID
						AND DATE_FORMAT(NSU_SUPPLY_DT,'%Y-%m') = DATE_FORMAT(DATE_ADD(NOW(), INTERVAL -1 MONTH),'%Y-%m')
                    )AS D1_SUPPLY_CNT,
                     (SELECT
						COUNT(NSU_ID) 
					FROM NT_SALES.NS_SALES_UPLOAD 
					WHERE MGG_ID = MGG.MGG_ID
					AND DATE_FORMAT(NSU_VISIT_DT,'%Y-%m') = DATE_FORMAT(DATE_ADD(NOW(), INTERVAL -2 MONTH),'%Y-%m')
                    )AS D2_VISIT_CNT,
                    (
						SELECT
							COUNT(NSU_ID) 
						FROM NT_SALES.NS_SALES_UPLOAD 
						WHERE MGG_ID = MGG.MGG_ID
						AND DATE_FORMAT(NSU_SUPPLY_DT,'%Y-%m') = DATE_FORMAT(DATE_ADD(NOW(), INTERVAL -2 MONTH),'%Y-%m')
                    )AS D2_SUPPLY_CNT,
					NT_CODE.GET_RCT_NM(MGGS.RCT_ID) AS RCT_NM,
					MGGS.MGG_BIZ_START_DT,
					IFNULL(MGGS.MGG_BIZ_END_DT, '')AS MGG_BIZ_END_DT,
					FORMAT(IFNULL(DATEDIFF(DATE_FORMAT(NOW(),'%Y-%m-%d'), (SELECT MAX(NB_CLAIM_DT) FROM NT_BILL.NBB_BILL  WHERE MGG_ID = MGG.MGG_ID)),'0'),0)  AS DIFF_CLAIM_DT,
					(
						SELECT 
							IFNULL(ROUND(SUM(IF(NB.NB_ID IS NOT NULL , 1,0))/3, 1),0) AS AVG_CNT
						FROM NT_BILL.NBB_BILL NB,
								NT_BILL.NBB_BILL_INSU NBI
						WHERE 
									NB.NB_ID = NBI.NB_ID
						AND	NB.MGG_ID = MGG.MGG_ID
						AND NB.DEL_YN = 'N'
						AND DATE_FORMAT(NB.NB_CLAIM_DT, '%Y-%m' ) 
						BETWEEN DATE_FORMAT(SUBDATE(NOW(), INTERVAL 3 MONTH), '%Y-%m' ) 
						AND  DATE_FORMAT(SUBDATE(NOW(), INTERVAL 1 MONTH), '%Y-%m' )
                    )AS AVG_BILL_CNT,
                    (
						SELECT 
								FORMAT(IFNULL(ROUND(SUM(NB.NB_TOTAL_PRICE)/3, 1),0),0) AVG_PRICE
						FROM NT_BILL.NBB_BILL NB,
								NT_BILL.NBB_BILL_INSU NBI
						WHERE 
								NB.MGG_ID = MGG.MGG_ID
						AND NB.DEL_YN = 'N'
						AND NB.NB_ID = NBI.NB_ID
						AND DATE_FORMAT(NB.NB_CLAIM_DT, '%Y-%m' ) 
						BETWEEN DATE_FORMAT(SUBDATE(NOW(), INTERVAL 3 MONTH), '%Y-%m' ) 
						AND  DATE_FORMAT(SUBDATE(NOW(), INTERVAL 1 MONTH), '%Y-%m' )
                    )AS AVG_BILL_PRICE,
                    (
						SELECT 
							COUNT(NB.NB_ID)
						FROM NT_BILL.NBB_BILL NB,
						NT_BILL.NBB_BILL_INSU NBI
						WHERE NB.DEL_YN = 'N'
						AND NB.MGG_ID = MGG.MGG_ID
						AND NB.NB_ID = NBI.NB_ID
						AND DATE_FORMAT(NB.NB_CLAIM_DT, '%Y-%m') = DATE_FORMAT(DATE_ADD(NOW(), INTERVAL -1 MONTH), '%Y-%m')
                    )AS D1_BILL_CNT,
                    (
						SELECT 
							FORMAT(IFNULL(SUM(NB_TOTAL_PRICE),0),0)
						FROM NT_BILL.NBB_BILL
						WHERE DEL_YN = 'N'
						AND MGG_ID =  MGG.MGG_ID
						AND DATE_FORMAT(NB_CLAIM_DT,'%Y-%m') = DATE_FORMAT(DATE_ADD(NOW(), INTERVAL -1 MONTH),'%Y-%m') 
                    )AS D1_BILL_PRICE,
                     (
						SELECT 
							COUNT(NB.NB_ID)
						FROM NT_BILL.NBB_BILL NB,
						NT_BILL.NBB_BILL_INSU NBI
						WHERE NB.DEL_YN = 'N'
						AND NB.MGG_ID = MGG.MGG_ID
						AND NB.NB_ID = NBI.NB_ID
						AND DATE_FORMAT(NB.NB_CLAIM_DT, '%Y-%m') = DATE_FORMAT(DATE_ADD(NOW(), INTERVAL -2 MONTH), '%Y-%m')
                    )AS D2_BILL_CNT,
                    (
						SELECT 
							FORMAT(IFNULL(SUM(NB_TOTAL_PRICE),0),0)
						FROM NT_BILL.NBB_BILL
						WHERE DEL_YN = 'N'
						AND MGG_ID =  MGG.MGG_ID
						AND DATE_FORMAT(NB_CLAIM_DT,'%Y-%m') = DATE_FORMAT(DATE_ADD(NOW(), INTERVAL -2 MONTH),'%Y-%m') 
                    )AS D2_BILL_PRICE,
                     (
						SELECT 
							COUNT(NB.NB_ID)
						FROM NT_BILL.NBB_BILL NB,
						NT_BILL.NBB_BILL_INSU NBI
						WHERE NB.DEL_YN = 'N'
						AND NB.MGG_ID = MGG.MGG_ID
						AND NB.NB_ID = NBI.NB_ID
						AND DATE_FORMAT(NB.NB_CLAIM_DT, '%Y-%m') = DATE_FORMAT(DATE_ADD(NOW(), INTERVAL -3 MONTH), '%Y-%m')
                    )AS D3_BILL_CNT,
                    (
						SELECT 
							FORMAT(IFNULL(SUM(NB_TOTAL_PRICE),0),0)
						FROM NT_BILL.NBB_BILL
						WHERE DEL_YN = 'N'
						AND MGG_ID =  MGG.MGG_ID
						AND DATE_FORMAT(NB_CLAIM_DT,'%Y-%m') = DATE_FORMAT(DATE_ADD(NOW(), INTERVAL -3 MONTH),'%Y-%m') 
                    )AS D3_BILL_PRICE,
					(
						SELECT 
							COUNT(NB.NB_ID)
						FROM NT_BILL.NBB_BILL NB,
						NT_BILL.NBB_BILL_INSU NBI
						WHERE NB.DEL_YN = 'N'
						AND NB.MGG_ID = MGG.MGG_ID
						AND NB.NB_ID = NBI.NB_ID
						AND DATE_FORMAT(NB.NB_CLAIM_DT, '%Y-%m') = DATE_FORMAT(DATE_ADD(NOW(), INTERVAL -4 MONTH), '%Y-%m')
                    )AS D4_BILL_CNT,
                    (
						SELECT 
							FORMAT(IFNULL(SUM(NB_TOTAL_PRICE),0),0)
						FROM NT_BILL.NBB_BILL
						WHERE DEL_YN = 'N'
						AND MGG_ID =  MGG.MGG_ID
						AND DATE_FORMAT(NB_CLAIM_DT,'%Y-%m') = DATE_FORMAT(DATE_ADD(NOW(), INTERVAL -4 MONTH),'%Y-%m') 
                    )AS D4_BILL_PRICE,
					-- NT_BILL.GET_3MONTH_AVG_BILL_INFO(MGG.MGG_ID, 'C') AS AVG_BILL_CNT,
					-- FORMAT(NT_BILL.GET_3MONTH_AVG_BILL_INFO(MGG.MGG_ID, 'P'),0) AS AVG_BILL_PRICE,
					-- NT_BILL.GET_MONTH_BILL_CNT(MGG.MGG_ID, YEAR(DATE_ADD(NOW(), INTERVAL -1 MONTH)), MONTH(DATE_ADD(NOW(), INTERVAL -1 MONTH)))AS D1_BILL_CNT,
					-- IFNULL(NT_BILL.GET_MONTH_BILL_PRICE(MGG.MGG_ID, DATE_ADD(NOW(), INTERVAL -1 MONTH)),0) AS D1_BILL_PRICE,
					-- NT_BILL.GET_MONTH_BILL_CNT(MGG.MGG_ID, YEAR(DATE_ADD(NOW(), INTERVAL -2 MONTH)), MONTH(DATE_ADD(NOW(), INTERVAL -2 MONTH)))AS D2_BILL_CNT,
					-- IFNULL(NT_BILL.GET_MONTH_BILL_PRICE(MGG.MGG_ID, DATE_ADD(NOW(), INTERVAL -2 MONTH)),0) AS D2_BILL_PRICE,
					-- NT_BILL.GET_MONTH_BILL_CNT(MGG.MGG_ID, YEAR(DATE_ADD(NOW(), INTERVAL -3 MONTH)), MONTH(DATE_ADD(NOW(), INTERVAL -3 MONTH)))AS D3_BILL_CNT,
					-- IFNULL(NT_BILL.GET_MONTH_BILL_PRICE(MGG.MGG_ID, DATE_ADD(NOW(), INTERVAL -3 MONTH)),0) AS D3_BILL_PRICE,
					-- NT_BILL.GET_MONTH_BILL_CNT(MGG.MGG_ID, YEAR(DATE_ADD(NOW(), INTERVAL -4 MONTH)), MONTH(DATE_ADD(NOW(), INTERVAL -4 MONTH)))AS D4_BILL_CNT,
					-- IFNULL(NT_BILL.GET_MONTH_BILL_PRICE(MGG.MGG_ID, DATE_ADD(NOW(), INTERVAL -4 MONTH)),0) AS D4_BILL_PRICE,
					MGG.MGG_BIZ_ID,
					NT_CODE.GET_RET_NM(MGGS.RET_ID) AS RET_NM,
					IFNULL(MGGS.MGG_RET_DT,'') AS MGG_RET_DT,
					IFNULL((SELECT RAT_NM FROM NT_CODE.REF_AOS_TYPE WHERE RAT_ID = MGGS.RAT_ID),'') AS RAT_NM,
					IFNULL(MGG.MGG_BILL_MEMO, '') AS MGG_AOS_MEMO,
					MGGS.MGG_LOGISTICS_YN,
					MGGS.MGG_RAT_NM,
					MGGS.MGG_OFFICE_TEL,
					MGGS.MGG_OFFICE_FAX,
					CONCAT(MGGS.MGG_ADDRESS_NEW_1,'',MGG_ADDRESS_NEW_2) AS MGG_ADDRESS,
					CONCAT(MGGS.MGG_ADDRESS_OLD_1,'',MGG_ADDRESS_OLD_2) AS MGG_ADDRESS_OLD,
					CAST(AES_DECRYPT(MGGS.MGG_CEO_NM_AES,UNHEX(SHA2('no1sbxpr!@',256))) AS CHAR) AS MGG_CEO_NM_AES,
					CAST(AES_DECRYPT(MGGS.MGG_CEO_TEL_AES,UNHEX(SHA2('no1sbxpr!@',256))) AS CHAR) AS MGG_CEO_TEL_AES,
					CAST(AES_DECRYPT(MGGS.MGG_KEYMAN_NM_AES,UNHEX(SHA2('no1sbxpr!@',256))) AS CHAR) AS MGG_KEYMAN_NM_AES,
					CAST(AES_DECRYPT(MGGS.MGG_KEYMAN_TEL_AES,UNHEX(SHA2('no1sbxpr!@',256))) AS CHAR) AS MGG_KEYMAN_TEL_AES,
					CAST(AES_DECRYPT(MGGS.MGG_CLAIM_NM_AES,UNHEX(SHA2('no1sbxpr!@',256))) AS CHAR) AS MGG_CLAIM_NM_AES,
					CAST(AES_DECRYPT(MGGS.MGG_CLAIM_TEL_AES,UNHEX(SHA2('no1sbxpr!@',256))) AS CHAR) AS MGG_CLAIM_TEL_AES,
					CAST(AES_DECRYPT(MGGS.MGG_FACTORY_NM_AES,UNHEX(SHA2('no1sbxpr!@',256))) AS CHAR) AS MGG_FACTORY_NM_AES,
					CAST(AES_DECRYPT(MGGS.MGG_FACTORY_TEL_AES,UNHEX(SHA2('no1sbxpr!@',256))) AS CHAR) AS MGG_FACTORY_TEL_AES,
					CAST(AES_DECRYPT(MGGS.MGG_PAINTER_NM_AES,UNHEX(SHA2('no1sbxpr!@',256))) AS CHAR) AS MGG_PAINTER_NM_AES,
					CAST(AES_DECRYPT(MGGS.MGG_PAINTER_TEL_AES,UNHEX(SHA2('no1sbxpr!@',256))) AS CHAR) AS MGG_PAINTER_TEL_AES,
					CAST(AES_DECRYPT(MGGS.MGG_METAL_NM_AES,UNHEX(SHA2('no1sbxpr!@',256))) AS CHAR) AS MGG_METAL_NM_AES,
					CAST(AES_DECRYPT(MGGS.MGG_METAL_TEL_AES,UNHEX(SHA2('no1sbxpr!@',256))) AS CHAR) AS MGG_METAL_TEL_AES,
					MGGS.MGG_WAREHOUSE_CNT,
					IFNULL((SELECT RMT_NM FROM NT_CODE.REF_MAIN_TARGET WHERE RMT_ID = MGGS.RMT_ID),'') AS RMT_NM,
					(SELECT ROT_NM FROM NT_CODE.REF_OWN_TYPE WHERE ROT_ID = MGGS.ROT_ID) AS ROT_NM,
					MGGS.MGG_INSU_MEMO,
					MGGS.MGG_MANUFACTURE_MEMO,
					MGGS.MGG_BILL_MEMO,
					MGGS.MGG_GARAGE_MEMO,
					MGGS.MGG_FRONTIER_NM,
					MGGS.MGG_COLLABO_NM,
					IFNULL(NT_CODE.GET_RET_NM(MGGS.RET_ID), '') AS RET_NM,
					CAST(AES_DECRYPT(MGGS.MGG_BANK_NUM_AES,UNHEX(SHA2('no1sbxpr!@',256))) AS CHAR) AS MGG_BANK_NUM_AES,
					IFNULL(CONCAT(MGGS.MGG_CONTRACT_START_DT,'~',MGGS.MGG_CONTRACT_END_DT),'') AS MGG_CONTRACT_DT,
					IF(MGGS.RCT_ID = '10620' ,(SELECT RSR_NM FROM NT_CODE.REF_STOP_REASON WHERE RSR_ID = MGGS.RSR_ID) , '-') AS RCT_ID,
					MGGS.MGG_OTHER_BIZ_NM,
					(
						SELECT
						COUNT(NIH.NIH_ID)
					FROM
						`NT_STOCK`.`NSI_IO_HISTORY` NIH
					WHERE
						NIH.`MGG_ID` = MGG.MGG_ID
					AND DATE_FORMAT(NIH.NIH_DT, '%Y-%m') = DATE_FORMAT(DATE_ADD(NOW(), INTERVAL -1 MONTH), '%Y-%m')
					AND NIH.RST_ID = '12110'
                    )AS SUPPLY_PARTS,
                     (
						SELECT
						COUNT(NIH.NIH_ID)
					FROM
						`NT_STOCK`.`NSI_IO_HISTORY` NIH
					WHERE
						NIH.`MGG_ID` = MGG.MGG_ID
					AND DATE_FORMAT(NIH.NIH_DT, '%Y-%m') = DATE_FORMAT(DATE_ADD(NOW(), INTERVAL -1 MONTH), '%Y-%m')
					AND NIH.RST_ID = '12130'
                    )AS CLAIM_PARTS,
                    (
						 SELECT
							IFNULL(SUM(IF(RST_ID = '12110',1,0)),0)-IFNULL(SUM(IF(RST_ID = '12130',1,0)),0)
						FROM
							`NT_STOCK`.`NSI_IO_HISTORY` NIH
						WHERE
							NIH.`MGG_ID` =  MGG.MGG_ID
						AND DATE_FORMAT(NIH.NIH_DT, '%Y-%m') = DATE_FORMAT(DATE_ADD(NOW(), INTERVAL -1 MONTH), '%Y-%m')
                    )AS STOCK_PARTS,
                    (
						SELECT
							IFNULL(SUM(IF(RST_ID = '12110',1,0))- (SUM(IF(RST_ID = '12120',1,0))+SUM(IF(RST_ID = '12130',1,0))),0)
						FROM
							`NT_STOCK`.`NSI_IO_HISTORY` NIH
						WHERE
							NIH.`MGG_ID` =  MGG.MGG_ID
						AND DATE_FORMAT(NIH.NIH_DT, '%Y-%m') = DATE_FORMAT(DATE_ADD(NOW(), INTERVAL -1 MONTH), '%Y-%m')
                    )AS STOCK_PARTS,
                    (
						 SELECT
							IFNULL(SUM(IF(RST_ID = '12110',1,0)),0)-IFNULL(SUM(IF(RST_ID = '12130',1,0)),0)
						FROM
							`NT_STOCK`.`NSI_IO_HISTORY` NIH
						WHERE
							NIH.`MGG_ID` =  MGG.MGG_ID
						AND DATE_FORMAT(NIH.NIH_DT, '%Y-%m') = DATE_FORMAT(DATE_ADD(NOW(), INTERVAL -1 MONTH), '%Y-%m')
                    )AS OVER_STD,
                    (
						SELECT PSE_OVER_CNT FROM NT_CODE.PCY_SALES_ENV
                    )AS OVER_STD_VAL,
					-- NT_STOCK.GET_MONTH_STOCK_CNT(MGG.MGG_ID, DATE_ADD(NOW(), INTERVAL -1 MONTH), 'S') AS SUPPLY_PARTS,
					-- NT_STOCK.GET_MONTH_STOCK_CNT(MGG.MGG_ID, DATE_ADD(NOW(), INTERVAL -1 MONTH), 'C') AS CLAIM_PARTS,
					-- IFNULL(NT_STOCK.GET_MONTH_STOCK_CNT(MGG.MGG_ID, DATE_ADD(NOW(), INTERVAL -1 MONTH), ''),0) AS STOCK_PARTS,
					-- NT_STOCK.GET_MONTH_STOCK_CNT(MGG.MGG_ID, DATE_ADD(NOW(), INTERVAL -1 MONTH), 'O') AS OVER_STD,
					-- (
					-- SELECT IF(PSE_OVER_CNT <  (NT_STOCK.GET_MONTH_STOCK_CNT(MGG.MGG_ID, DATE_ADD(NOW(), INTERVAL -1 MONTH), 'O')), 'Y','N') FROM NT_CODE.PCY_SALES_ENV
					-- )AS OVER_STD_YN,
					IFNULL(GROUP_CONCAT(MGGP.CPP_NM_SHORT SEPARATOR  '||'),'') AS CPP_NM_SHORT,
					IFNULL(GROUP_CONCAT(MGGP.CPPS_NM_SHORT  SEPARATOR  '||'),'') AS CPPS_NM_SHORT,
					IFNULL(GROUP_CONCAT(MGGP.NGP_RATIO SEPARATOR  '||'),'') AS NGP_RATIO,
					MGGS.MGG_SALES_NM,
					(SELECT RST_NM FROM NT_CODE.REF_SCALE_TYPE WHERE RST_ID = MGGS.RST_ID) AS RST_NM,
					MGGS.MGG_SALES_MEET_LVL,
					IFNULL(CAST(AES_DECRYPT(MGGS.MGG_SALES_MEET_NM_AES,UNHEX(SHA2('no1sbxpr!@',256))) AS CHAR),'') AS MGG_SALES_MEET_NM_AES,
					IFNULL(CAST(AES_DECRYPT(MGGS.MGG_SALES_MEET_TEL_AES,UNHEX(SHA2('no1sbxpr!@',256))) AS CHAR),'') AS MGG_SALES_MEET_TEL_AES,
					MGGS.MGG_SALES_WHYNOT,
					IFNULL(NT_CODE.GET_RWP_NM(MGGS.RRT_ID),'') AS RRT_NM,
					IFNULL(MAX(NSU.NSU_VISIT_DT),'')AS LAST_VISIT_DT
				FROM NT_MAIN.NMG_GARAGE MGG,
					NT_MAIN.NMG_GARAGE_SUB MGGS	
				LEFT JOIN (
					SELECT 
						MGG_ID,
						CONCAT(CPP_NM_SHORT,':' ,GROUP_CONCAT(CPPS_NM_SHORT ORDER BY CPPS.`WRT_DTHMS` ASC) )AS CPP_NM_SHORT,
						GROUP_CONCAT(IF(NGP_DEFAULT_YN = 'Y' , CPPS_NM_SHORT, '') ORDER BY CPPS.`WRT_DTHMS` ASC SEPARATOR '')AS CPPS_NM_SHORT,
						`MGGP`.`NGP_RATIO`,
						CPPS.WRT_DTHMS
						
					FROM `NT_MAIN`.`NMG_GARAGE_PART` `MGGP`,
						`NT_CODE`.`NCP_PART` `CPP`,
						`NT_CODE`.`NCP_PART_SUB` `CPPS`
					WHERE `MGGP`.`CPP_ID` =  `CPP`.`CPP_ID`
					AND `MGGP`.`CPPS_ID` =  `CPPS`.`CPPS_ID`
					AND CPP.DEL_YN = 'N'
					GROUP BY CPP.CPP_ID, MGGP.MGG_ID
					ORDER BY `CPP`.`CPP_ORDER_NUM`ASC
				)AS MGGP
				  ON MGGP.MGG_ID = MGGS.MGG_ID
				LEFT JOIN (SELECT MGG_ID, MAX(NSU_VISIT_DT)AS NSU_VISIT_DT,NSU_SUPPLY_DT FROM NT_SALES.NS_SALES_UPLOAD GROUP BY MGG_ID) NSU
					ON NSU.MGG_ID = MGGP.MGG_ID
				WHERE MGG.MGG_ID = MGGS.MGG_ID
				AND MGG.DEL_YN = 'N'
				$tmpKeyword 
				GROUP BY MGG_ID
				$havingSQL 
				ORDER BY MGGS.MGG_BIZ_START_DT DESC
				$LIMIT_SQL 
			;"
		;
		// echo $iSQL;
		$Result = DBManager::getResult($iSQL);

		$rtnArray = array();
		while($RS = $Result->fetch_array()){
			$D1_BILL_BGCOLOR_YN = "N";
			$D2_BILL_BGCOLOR_YN = "N";
			$D3_BILL_BGCOLOR_YN = "N";
			$OVER_STD_YN = "N";
			if($RS['D1_BILL_CNT'] <= $RS['D2_BILL_CNT']*0.7){
				$D1_BILL_BGCOLOR_YN = "Y";
			}
			if($RS['D2_BILL_CNT'] <= $RS['D3_BILL_CNT']*0.7){
				$D2_BILL_BGCOLOR_YN = "Y";
			}
			if($RS['D3_BILL_CNT'] <= $RS['D4_BILL_CNT']*0.7){
				$D3_BILL_BGCOLOR_YN = "Y";
			}
			if($RS['OVER_STD'] < $RS['OVER_STD_VAL']){
				$OVER_STD_YN = "Y";
			}
			array_push($rtnArray,
				array(
					"IDX" => $IDX--,
					"MGG_ID"=>$RS['MGG_ID'],
					"CSD_NM"=>$RS['CSD_NM'],
					"CRR_NM"=>$RS['CRR_NM'],
					"MGG_CIRCUIT_ORDER"=>$RS['MGG_CIRCUIT_ORDER'],
					"MGG_NM"=>$RS['MGG_NM'],
					"THIS_YEAR_VISIT"=>$RS['THIS_YEAR_VISIT'],
					"LAST_YEAR_VISIT"=>$RS['LAST_YEAR_VISIT'],
					"VISIT_CYCLE"=>$RS['VISIT_CYCLE'],
					"THIS_VISIT_CNT"=>$RS['THIS_VISIT_CNT'],
					"THIS_SUPPLY_CNT"=>$RS['THIS_SUPPLY_CNT'],
					"D1_VISIT_CNT"=>$RS['D1_VISIT_CNT'],
					"D1_SUPPLY_CNT"=>$RS['D1_SUPPLY_CNT'],
					"D2_VISIT_CNT"=>$RS['D2_VISIT_CNT'],
					"D2_SUPPLY_CNT"=>$RS['D2_SUPPLY_CNT'],
					"RCT_NM"=>$RS['RCT_NM'],
					"MGG_BIZ_START_DT"=>$RS['MGG_BIZ_START_DT'],
					"MGG_BIZ_END_DT"=>$RS['MGG_BIZ_END_DT'],
					"DIFF_CLAIM_DT"=>$RS['DIFF_CLAIM_DT'],
					"AVG_BILL_CNT"=>$RS['AVG_BILL_CNT'],
					"AVG_BILL_PRICE"=>$RS['AVG_BILL_PRICE'],
					"D1_BILL_CNT"=>$RS['D1_BILL_CNT'],
					"D1_BILL_PRICE"=>$RS['D1_BILL_PRICE'],
					"D1_BILL_BGCOLOR_YN"=>$RS['D1_BILL_BGCOLOR_YN'],
					"D2_BILL_CNT"=>$RS['D2_BILL_CNT'],
					"D2_BILL_PRICE"=>$RS['D2_BILL_PRICE'],
					"D2_BILL_BGCOLOR_YN"=>$RS['D1_BILL_BGCOLOR_YN'],
					"D3_BILL_CNT"=>$RS['D3_BILL_CNT'],
					"D3_BILL_PRICE"=>$RS['D3_BILL_PRICE'],
					"D3_BILL_BGCOLOR_YN"=>$RS['D1_BILL_BGCOLOR_YN'],
					"MGG_BIZ_ID"=>$RS['MGG_BIZ_ID'],
					"RET_NM"=>$RS['RET_NM'],
					"MGG_RET_DT"=>$RS['MGG_RET_DT'],
					"RAT_NM"=>$RS['RAT_NM'],
					"MGG_RAT_NM"=>$RS['MGG_RAT_NM'],
					"MGG_AOS_MEMO"=>$RS['MGG_AOS_MEMO'],
					"MGG_LOGISTICS_YN"=>$RS['MGG_LOGISTICS_YN'],
					"MGG_OFFICE_TEL"=>$RS['MGG_OFFICE_TEL'],
					"MGG_OFFICE_FAX"=>$RS['MGG_OFFICE_FAX'],
					"MGG_ADDRESS"=>$RS['MGG_ADDRESS'],
					"MGG_ADDRESS_OLD"=>$RS['MGG_ADDRESS_OLD'],
					"MGG_CEO_NM_AES"=>$RS['MGG_CEO_NM_AES'],
					"MGG_CEO_TEL_AES"=>$RS['MGG_CEO_TEL_AES'],
					"MGG_KEYMAN_NM_AES"=>$RS['MGG_KEYMAN_NM_AES'],
					"MGG_KEYMAN_TEL_AES"=>$RS['MGG_KEYMAN_TEL_AES'],
					"MGG_CLAIM_NM_AES"=>$RS['MGG_CLAIM_NM_AES'],
					"MGG_CLAIM_TEL_AES"=>$RS['MGG_CLAIM_TEL_AES'],
					"MGG_FACTORY_NM_AES"=>$RS['MGG_FACTORY_NM_AES'],
					"MGG_FACTORY_TEL_AES"=>$RS['MGG_FACTORY_TEL_AES'],
					"MGG_PAINTER_NM_AES"=>$RS['MGG_PAINTER_NM_AES'],
					"MGG_PAINTER_TEL_AES"=>$RS['MGG_PAINTER_TEL_AES'],
					"MGG_METAL_NM_AES"=>$RS['MGG_METAL_NM_AES'],
					"MGG_METAL_TEL_AES"=>$RS['MGG_METAL_TEL_AES'],
					"MGG_WAREHOUSE_CNT"=>$RS['MGG_WAREHOUSE_CNT'],
					"RMT_NM"=>$RS['RMT_NM'],
					"ROT_NM"=>$RS['ROT_NM'],
					"MGG_INSU_MEMO"=>$RS['MGG_INSU_MEMO'],
					"MGG_MANUFACTURE_MEMO"=>$RS['MGG_MANUFACTURE_MEMO'],
					"MGG_BILL_MEMO"=>$RS['MGG_BILL_MEMO'],
					"MGG_GARAGE_MEMO"=>$RS['MGG_GARAGE_MEMO'],
					"MGG_FRONTIER_NM"=>$RS['MGG_FRONTIER_NM'],
					"MGG_COLLABO_NM"=>$RS['MGG_COLLABO_NM'],
					"MGG_CONTRACT_DT"=>$RS['MGG_CONTRACT_DT'],
					"MGG_BANK_NUM_AES"=>$RS['MGG_BANK_NUM_AES'],
					"RCT_ID"=>$RS['RCT_ID'],
					"MGG_OTHER_BIZ_NM"=>$RS['MGG_OTHER_BIZ_NM'],
					"SUPPLY_PARTS"=>$RS['SUPPLY_PARTS'],
					"CLAIM_PARTS"=>$RS['CLAIM_PARTS'],
					"STOCK_PARTS"=>$RS['STOCK_PARTS'],
					"OVER_STD"=>$RS['OVER_STD'],
					"OVER_STD_YN"=>$OVER_STD_YN,
					"CPP_NM_SHORT"=>$RS['CPP_NM_SHORT'],
					"CPPS_NM_SHORT"=>$RS['CPPS_NM_SHORT'],
					"NGP_RATIO"=>$RS['NGP_RATIO'],
					"MGG_SALES_NM"=>$RS['MGG_SALES_NM'],
					"RST_NM"=>$RS['RST_NM'],
					"MGG_SALES_MEET_LVL"=>$RS['MGG_SALES_MEET_LVL'],
					"MGG_SALES_MEET_NM_AES"=>$RS['MGG_SALES_MEET_NM_AES'],
					"MGG_SALES_MEET_TEL_AES"=>$RS['MGG_SALES_MEET_TEL_AES'],
					"MGG_SALES_WHYNOT"=>$RS['MGG_SALES_WHYNOT'],
					"RRT_NM"=>$RS['RRT_NM'],
					"LAST_VISIT_DT"=>$RS['LAST_VISIT_DT'],


			));
		}

		$Result->close();  // 자원 반납
		unset($RS);

		if(empty($rtnArray)){
			return HAVE_NO_DATA;
		}else{
			$jsonArray = array();
			array_push($jsonArray,$rtnArray);
			array_push($jsonArray,$TOTAL_LIST_COUNT);
			return $jsonArray;

		}
	}

	function delData ($ARR) {

		$iSQL = "DELETE FROM NT_SALES.NS_SALES_UPLOAD 
				WHERE NSU_ID = '".addslashes($ARR['NSU_ID'])."'
				;
		";
		return DBManager::execQuery($iSQL);
	}	
}
	// 0. 객체 생성
	$clsSaState = new clsSaState(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
	$clsSaState->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
