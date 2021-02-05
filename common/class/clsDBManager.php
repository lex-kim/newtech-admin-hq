<?php
/*
==========================================================================================
            COPYRIGHT ⓒ 2017. 디지엠정보기술(주)
------------------------------------------------------------------------------------------
 This material is proprietary to 디지엠정보기술(주).
  It contains trade secrets and confidential information which is solely the property of 디지엠정보기술(주).
  This material is solely for the Client's internal use.
  This material shall not be used, reproduced, copied, disclosed, transmitted,
  in whole or in part, without the express consent of 디지엠정보기술(주). ⓒ All rights reserved.
==========================================================================================
            OVERVIEW
------------------------------------------------------------------------------------------
        PROJECT Name :
		  Module Name : 데이터베이스 연동 클래스(MySQL PHP 7이상)
        Module Version : 2.0.0
        File Name : clsDBManager.php
        Author Name(email, tel1, cell) : 권혁태
        Begin Date : 2006.01.17
        Last Update : 2017.03.24

        Module & File Description
			트랜잭션 사용시, 클래스 자체에서 판별하여 에러발생시에도 별도의 ROLLBACK처리가
			필요없다. 사용자는 트랜잭션의 모든 작업이 완료되면 COMMIT을 호출하여야 한다.
==========================================================================================
	Version history
------------------------------------------------------------------------------------------
	1.0.0	2006.01.17	php4기반의 기본형 DBManager Class 개발												권혁태
	1.1.0	2016.06.10	PHP7에서 deprecate 된 함수들 변경												  		권혁태
	2.0.0	2017.03.24	PHP7.1에서 사용할 수 있는 mysqli 방식으로 전체 개발							권혁태
	2.0.0	2017.03.25	갱신 쿼리 메소드 추가(execQuery), 조회메소드(getRecordSet)수정	권혁태
	2.1.0 2017.04.11		function execMulti($iSQL) 메소드 추가. 동시 여러쿼리 실행 필요시 사용	권혁태
			 							getEscapeStr() 메소드 추가																		 홍태경
										ConnectDB dbport 속성 파라메터 추가														홍태경
	2.2.0 2018.06.20  setPAGING 메소드 복원																				권혁태
	2.3.0 2020.02.04	ssl 접속 추가
==========================================================================================
            GLOBAL VALUE DESCRIPTION
------------------------------------------------------------------------------------------
        VARIABLE NAME   DATATYPE    LENGTH  DESCRIPTION
------------------------------------------------------------------------------------------
		$conn			Connection				데이터베이스 Connection handler
		$Transaction	트랜잭션 사용여부		0 : 사용안함		1 : 사용함
		$QryResult		실행된 쿼리의 핸들러

==========================================================================================*/
/**
*	DBManager for PHP 7.1.x

*	@package	DBManager
* @param
* @return
* @author		권혁태 <ceo@dgmit.com>
* @version	2.0.1
* @access		private
* @abstract	PHP 7.1.X의 mysqli OOP 방식의 클래스로 변경개발
*/

include_once $_SERVER['DOCUMENT_ROOT']."/common/class/clsUtil.php";



class DBManager extends cUtil {



	private $conn;					// Database Connection
	private $Transaction;			// Transaction 사용여부			0 : 사용안함(default) 1 : 사용함
	private $QryResult;				// mysqli_query를 통해 실행한 쿼리핸들러




	// Class Object Instructor
	// function DBManager() { $this->Transaction = 0;	}
	function __construct() {
		$this->Transaction = 0;
	}
	function __destruct() { 	}


	/*
	------------------------------------------------------------------------------------------
	 Procedure Name			: ConnectDB($host,$dbuser,$dbpass)
	 Procedure Description	: 데이터베이스 연결시도
	 Logic Description		: $dbuser와 $dbpass를 이용하여 $host(IP + PORT, default IP)에 접속
	 Result Type				: BOOLEAN
	------------------------------------------------------------------------------------------
	 ## Parameter Description
		$host	: MySQL Server IP  default : IP, including port number : IP:PORT
		$dbuser : DB접속 계정
		$dbpass	: 해당 계정의 비밀번호
	------------------------------------------------------------------------------------------
	            USAGE
	------------------------------------------------------------------------------------------
	  Code  '
	     if(ConnectDB("192.168.1.250:3306","user","passwd")) {
				echo "연결성공";
		   } else {
				echo "연결실패";
		   }

	  Result '
	     true or false
	------------------------------------------------------------------------------------------*/
/*******************************************************************************
* @name			ConnectDB
* @param		$host : IP Address
						$dbuser : DB접속 계정
						$dbpass : DB 비번
						$dbname : 기본접속 DB명
* @return		boolean
* @author		권혁태 <ceo@dgmit.com>
* @version	2.1.0
* @abstract	mysqli 접속
* 					sql_exception 추가
********************************************************************************/
function ConnectDB($host,$dbuser,$dbpass,$dbname,$dbport = null) {
		try {
			$this->conn = mysqli_init();

			if(!is_null($sslPath)) {
				// SSL 인증서 기반의 Connection
				$this->conn->ssl_set(NULL,NULL,$sslPath,NULL,NULL);
				$this->conn->real_connect($host,$dbuser,$dbpass,$dbname,$dbport,NULL,MYSQLI_CLIENT_SSL);
			} else {
				// 일반 connection
				$this->conn->real_connect($host,$dbuser,$dbpass,$dbname,$dbport);
			}
			$this->conn->query("set names utf8;");
			// to check SSL connection
			// $res = $this->conn->query("SHOW STATUS LIKE 'ssl_cipher';");
			// print_r(mysqli_fetch_row($res));

			$this->conn->autocommit(true);
			return $this->conn;
		} catch (mysqli_sql_exception $e) {
			 throw $e;
		}
	}

	/*
	------------------------------------------------------------------------------------------
	 Procedure Name			: CloseDB()
	 Procedure Description	: 데이터베이스 연결해제
	 Logic Description		: 현 클래스의 DB연결을 해제한다.
	 Result Type				: BOOLEAN
	------------------------------------------------------------------------------------------
	 ## Parameter Description

	------------------------------------------------------------------------------------------
	            USAGE
	------------------------------------------------------------------------------------------
	  Code  '
	     MyConn->CloseDB();

	  Result '
	     true or false
	------------------------------------------------------------------------------------------*/
	/*****************************************************************************
	* @name			CloseDB
	* @param		-
	* @return		boolean
	* @author		권혁태 <ceo@dgmit.com>
	* @version	2.0.0
	* @abstract	DB 접속 link 해제 (반납)
	*****************************************************************************/
	function CloseDB() { return $this->conn->close(); }

	/*
	------------------------------------------------------------------------------------------
	 Procedure Name			: SelectDB($dbname)
	 Procedure Description	: 사용할데이터베이스 선택
	 Logic Description		: $dbname의 데이터베이스를 선택
	 Result Type				: BOOLEAN
	------------------------------------------------------------------------------------------
	 ## Parameter Description
		$dbname	: 사용할 데이터베이스명
	------------------------------------------------------------------------------------------
	            USAGE
	------------------------------------------------------------------------------------------
	  Code  '
	     if(SelectDB("Test")) {
				echo "선택성공";
		   } else {
				echo "선택실패";
		   }

	  Result '
	     true or false
	------------------------------------------------------------------------------------------*/
	/*****************************************************************************
	* @name			SelectDB
	* @param		$dbname : 선택할 DB명
	* @return		boolean
	* @author		권혁태 <ceo@dgmit.com>
	* @version	2.0.0
	* @abstract	쿼리 대상 DB 선택
	*****************************************************************************/
	function SelectDB($dbname) {
		return $this->conn->select_db($dbname);
	}


	/*
	------------------------------------------------------------------------------------------
	 Procedure Name			: BeginTransjaction()
	 Procedure Description	: 트랜잭션 시작
	 Logic Description		: 트랜잭션을 이용한 처리를 위해 사용
	 Result Type				: none
	------------------------------------------------------------------------------------------
	 ## Parameter Description

	------------------------------------------------------------------------------------------
	            USAGE
	------------------------------------------------------------------------------------------
	  Code  '
	     MyConn->BeginTransjaction();

	  Result '
	     none
	------------------------------------------------------------------------------------------*/
	/*****************************************************************************
	* @name			BeginTransaction
	* @param		-
	* @return		boolean
	* @author		권혁태 <ceo@dgmit.com>
	* @version	2.0.0
	* @abstract	트랜잭션 처리시 begenTransact
	*****************************************************************************/
	function BeginTransaction() {

		// MySQL Default가 Auto Commit. Manual Commit으로 변경
		//mysqli_query($this->conn, "SET AUTOCOMMIT = 0");
		$this->conn->autocommit(false);
		$this->Transaction = 1;

		// 트랜잭션 시작
		return $this->conn->begin_transaction(MYSQLI_TRANS_START_READ_ONLY);

	}


	/*
	------------------------------------------------------------------------------------------
	 Procedure Name			: EndTransaction()
	 Procedure Description	: 트랜잭션 종료
	 Logic Description		: Auto commit을 원래대로 설정
	 Result Type				: none
	------------------------------------------------------------------------------------------
	 ## Parameter Description

	------------------------------------------------------------------------------------------
	            USAGE
	------------------------------------------------------------------------------------------
	  Code  '
	     MyConn->EndTransaction();

	  Result '
	     none
	------------------------------------------------------------------------------------------*/
	/*****************************************************************************
	* @name			EndTransaction
	* @param		-
	* @return		boolean
	* @author		권혁태 <ceo@dgmit.com>
	* @version	2.0.0
	* @abstract	트랜잭션 처리시 종료
	*****************************************************************************/
	function EndTransaction() {

		$this->Transaction = 0;
		// MySQL Default인 Auto Commit으로 변경
		//return mysqli_query($this->conn, "SET AUTOCOMMIT = 1");

		return $this->conn->$this->conn->autocommit(true);

	}


	/*
	------------------------------------------------------------------------------------------
	 Procedure Name			: ROLLBACK()
	 Procedure Description	: 트랜잭션 중 오류발생시 ROLLBACK
	 Logic Description		: 트랜잭션 취소
	 Result Type				: none
	------------------------------------------------------------------------------------------
	 ## Parameter Description
		$dbname	: 사용할 데이터베이스명
	------------------------------------------------------------------------------------------
	            USAGE
	------------------------------------------------------------------------------------------
	  Code  '
	     MyConn->ROLLBACK();

	  Result '
	     true or false
	------------------------------------------------------------------------------------------*/
	/*****************************************************************************
	* @name			ROLLBACK
	* @param		-
	* @return		boolean
	* @author		권혁태 <ceo@dgmit.com>
	* @version	2.0.0
	* @abstract	트랜잭션 처리시 롤백
	*****************************************************************************/
	function ROLLBACK() { return $this->conn->rollback(); }


	/*
	------------------------------------------------------------------------------------------
	 Procedure Name			: COMMIT()
	 Procedure Description	: 트랜잭션 완료
	 Logic Description		: 트랜잭션의 완료를 요청하여 최종적으로 DB가 저장되도록.
	 Result Type				: none
	------------------------------------------------------------------------------------------
	 ## Parameter Description
		$dbname	: 사용할 데이터베이스명
	------------------------------------------------------------------------------------------
	            USAGE
	------------------------------------------------------------------------------------------
	  Code  '
	     MyConn->COMMIT();

	  Result '
	     true or false
	------------------------------------------------------------------------------------------*/
	/*****************************************************************************
	* @name			COMMIT
	* @param		-
	* @return		boolean
	* @author		권혁태 <ceo@dgmit.com>
	* @version	2.0.0
	* @abstract	트랜잭션 처리시 커밋
	*****************************************************************************/
	function COMMIT() { return $this->conn->commit(); }


	/*
	------------------------------------------------------------------------------------------
	 Procedure Name			: execQuery($iSQL)
	 Procedure Description	: 쿼리실행
	 Logic Description		: $iSQL에 들어오는 쿼리종류를 분석하여 SELECT는 Recordset을,
								  INSERT,UPDATE,DELETE는 결과값을 BOOLEAN 형태로 Return한다.
	 Result Type				: Recordset(SELECT), BOOLEAN(INSERT,UPDATE,DELETE)
	------------------------------------------------------------------------------------------
	 ## Parameter Description
		$iSQL	: 실행할 쿼리
	------------------------------------------------------------------------------------------
	            USAGE
	------------------------------------------------------------------------------------------
	  Code  '
	     MyConn->execQuery("SELECT * FROM TEST");

	  Result '
	     true or false
	------------------------------------------------------------------------------------------*/
	/*****************************************************************************
	* @name			getRecordSet
	* @param		$iSQL	String
	* @return		ResultSet
	* @author		권혁태 <ceo@dgmit.com>
	* @version	2.0.0
	* @abstract	쿼리를 실행하고 단일 튜플 값을 반환한다
	*****************************************************************************/
	function getRecordSet($iSQL) {
		$this->QryResult = $this->conn->query($iSQL);

		if($this->QryResult) {
		// 성공
		} else {
		// 실패
			if($this->Transaction == 1) { $this->ROLLBACK(); }
		}

		$RS = $this->QryResult->fetch_array();
		$this->QryResult->close();
		return $RS;
	}
	/*****************************************************************************
	* @name			execQuery
	* @param		$iSQL	String
	* @return		ResultSet
	* @author		권혁태 <ceo@dgmit.com>
	* @version	2.0.0
	* @abstract	쿼리를 실행하고 단일 튜플 값을 반환한다
	*****************************************************************************/
	function execQuery($iSQL) {
		try {
			$result = ($this->conn->query($iSQL)) or die($this->conn->error);
			if($result) {
				if($this->Transaction == 1) { $this->COMMIT(); }
				$rtnVal = true;
			} else {
				$rtnVal = false;
			}
		} catch(Exception $e) {
			if($this->Transaction == 1) { $this->ROLLBACK(); }
			echo $e;
			$rtnVal = false;
		}
		return $rtnVal;
	}
	/*****************************************************************************
	* @name			execMulti
	* @param		$iSQL	String
	* @return		ResultSet
	* @author		권혁태 <ceo@dgmit.com>
	* @version	2.0.0
	* @abstract	여러개의 쿼리를 실행하고 값을 반환한다
	*****************************************************************************/
	function execMulti($iSQL) {

		try {
			$result = ($this->conn->multi_query($iSQL)) or die($this->conn->error);
			if($this->Transaction == 1) { $this->COMMIT(); }
			$rtnVal = true;
		} catch(Exception $e) {
			if($this->Transaction == 1) { $this->ROLLBACK(); }
			echo $e;
			$rtnVal = false;
		}
		return $rtnVal;
	}
	/*****************************************************************************
	* @name			getResult
	* @param		$iSQL	String
	* @return		ResultSet
	* @author		권혁태 <ceo@dgmit.com>
	* @version	2.0.0
	* @abstract	쿼리를 실행하고 단일 튜플 값을 반환한다
	*****************************************************************************/
	function getResult($iSQL) {
		$result = $this->conn->query($iSQL);

		return $result;
	}

	/*
		@name	getMaxNum
		@return integer
		@author 권혁태
		@version 1.0	*/
	function getMaxNum($ARR) {
		$iSQL = "SELECT 	IFNULL(MAX(".addslashes($ARR['REQ_FIELD']).")+1,'".$ARR['DEFAULT_VAL']."') AS MAX_NUM
							 FROM
										".addslashes($ARR['REQ_TABLE'])."
						".($ARR['REQ_WHERE'])."
				";

		return json_encode(DBManager::getRecordSet($iSQL));
	}

	/**
	 * @name getEscapeStr
	 * @param  string $STR
	 * @author 홍태경
	 * @return string espcaped 된 스트링
	 */
	function getEscapeStr($STR){
		return mysqli_real_escape_string($this->conn, $STR);
	}


	/*
	 ==========================================================================================
	 Procedure Name			: setPAGING($linkURL,$ICON_ARROWS,$CurPAGE,$TotalPage,$PerPage)
	 Procedure Description	: 게시판 목록의 하단 페이징을 적용한다.
	 Logic Description		:
	 Result Type			: STRING
	 ------------------------------------------------------------------------------------------
	 ## Parameter Description


	 ==========================================================================================
	 USAGE
	 ------------------------------------------------------------------------------------------
	 Code  '

	 Result '
	 ==========================================================================================*/
	function setPAGING($linkURL,$CurPAGE,$TotalPage,$PerPage) {

		$BOARD_PAGING_TO = "";
		$tmpCurPAGE = $CurPAGE - 1;				// 실질적인 시작인덱스

		$PAGECOUNT = 10;

		$tmpSTARTPAGE = (floor($tmpCurPAGE / $PAGECOUNT) * $PAGECOUNT)+1;		// 시작페이지 번호 설정
		$tmpENDPAGE   = $tmpSTARTPAGE + $PAGECOUNT - 1;							// 종료페이지 번호 설정
		if($tmpENDPAGE > $TotalPage) { $tmpENDPAGE = $TotalPage; }				// 종료페이지가 총페이지보다 클경우

		$tmpPREVPAGE  = $PAGECOUNT + $tmpSTARTPAGE - $PAGECOUNT - 1; 			// 이전 $PAGECOUNT 개 페이지 구하기
		if( $tmpPREVPAGE < 1) { $tmpPREVPAGE = 1; }
		$tmpNEXTPAGE  = $tmpENDPAGE + 1;										// 이후 $PAGECOUNT 개 페이지 구하기
		if( $tmpNEXTPAGE > $TotalPage) { $tmpNEXTPAGE = $TotalPage; }


		// 링크될 GET 변수리스트
		$tmpLINK = $linkURL;

		$tmpPAGING_PREV = "
		<div class=\"row\">
			<div class=\"col-md-12\">
				<ul class=\"pagination pagination-sm pull-left\">
		";
		$BOARD_PAGING_TO = $tmpPAGING_PREV."<li onclick=\"getList(".$tmpPREVPAGE.")\"><a href=\"#\">«</a></li>";
	/* ".$tmpLINK."&CurPAGE=1&PerPage=".$PerPage." */

		for($i=$tmpSTARTPAGE;$i<=$tmpENDPAGE;$i++) {							// 페이징 링크 생성
				if($i == $CurPAGE) {
					$tmpNUM = "<li class=\"active\"><a href=\"javascript:void(0)\">".$i." <span class=\"sr-only\">(current)</span></a></li>";
					$BOARD_PAGING_TO = $BOARD_PAGING_TO.$tmpNUM;
				} else {
					$tmpNUM = $i;
					$BOARD_PAGING_TO = $BOARD_PAGING_TO."<li onclick=\"getList(".$i.")\"><a href=\"#\">".$i."</a></li>";
				}
		}
		$BOARD_PAGING_TO = $BOARD_PAGING_TO."<li onclick=\"getList(".$tmpNEXTPAGE.")\"><a href=\"#\">»</a></li>";
		$tmpPAGING_NEXT = "
				</ul>
			</div>
		</div>
		";

		return $BOARD_PAGING_TO.$tmpPAGING_NEXT;

	}

}

?>
