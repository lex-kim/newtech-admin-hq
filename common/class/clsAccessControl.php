<?php
error_reporting(E_ERROR | E_WARNING);

class cAccessControl extends DBManager {
  /* -=-=-=- The Top of  Class object -=-=-=- */

  /*
		@name	makeResult
		@return JSON
    @param  $pStatus  결과 상태값
            $pResult  결과 택스트
            $pTimestamp timestamp
            $pData    응답 데이터
		@author 권혁태
		@version 1.0	*/
  function makeResult($pStatus, $pResult, $pTimestamp, $pData) {
    return "{
      \"status\":\"$pStatus\",
      \"result\":\"$pResult\",
      \"timestamp\":\"$pTimestamp\",
      \"data\":$pData
    }";
  }
  /*
		@name	makeListResult
		@return JSON
    @param  $pStatus  결과 상태값
            $pResult  결과 택스트
            $pTimestamp timestamp
            $pData    응답 데이터
		@author 권혁태
		@version 1.0	*/
  function makeListResult($pStatus, $pResult, $pTimestamp, $pTotal_item_cnt, $pTotal_page_cnt, $pData) {
    return "{
      \"status\":\"$pStatus\",
      \"result\":\"$pResult\",
      \"timestamp\":\"$pTimestamp\",
      \"total_item_cnt\":\"$pTotal_item_cnt\",
      \"total_page_cnt\":\"$pTotal_page_cnt\",
      \"data\":$pData
    }";
  }
  /*
		@name	makeApprovalResult
		@return JSON
    @param  $pStatus  결과 상태값
            $pResult  결과 택스트
            $pTimestamp timestamp
            $pData    응답 데이터
		@author 권혁태
		@version 1.0	*/
  function makeApprovalResult($pStatus, $pResult, $pTimestamp, $pMM_ID, $pApprovalCd, $pData) {
    return "{
      \"status\":\"$pStatus\",
      \"result\":\"$pResult\",
      \"timestamp\":\"$pTimestamp\",
      \"member_id\":\"$pMM_ID\",
      \"req_approval_cd\":\"$pApprovalCd\",
      \"data\":$pData
    }";
  }

  /* 토큰의 유효성 검증 */
  function validateToken($ARR) {
    $iSQL = "SELECT
								`SGN_ACCESS_CONTROL`.`VALIDATE_TOKEN`(
									'".$ARR['acc_token']."',
									'".addslashes($ARR['login_id'])."'
									) AS TOKEN_RESULT
							FROM
								DUAL
				";
    $RS = DBManager::getRecordSet($iSQL);
    $TOKEN_RESULT = $RS['TOKEN_RESULT'];
    unset($RS);

    return $TOKEN_RESULT;
  }

  /* -=-=-=- The Bottom of cUtil Class object -=-=-=- */
}

$cAccessControl = new cAccessControl(FILE_UPLOAD_PATH,ALLOW_FILE_EXT);
$cAccessControl->ConnectDB($dbHOST, $dbUSER, $dbPASS, $dbNAME, $dbPORT, $sslPath);

?>
