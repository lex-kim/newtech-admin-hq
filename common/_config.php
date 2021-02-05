<?php
//==========================================================================================
//            COPYRIGHT ⓒ 2009. 디지엠정보기술(주)
//------------------------------------------------------------------------------------------
// This material is proprietary to 디지엠정보기술(주).
//  It contains trade secrets and confidential information which is solely the property of 디지엠정보기술(주).
//  This material is solely for the Client's internal use.
//  This material shall not be used, reproduced, copied, disclosed, transmitted,
//  in whole or in part, without the express consent of 디지엠정보기술(주). ⓒ All rights reserved.
//==========================================================================================
//            OVERVIEW
//------------------------------------------------------------------------------------------
//        PROJECT Name :
//		  Module Name : 환경설정파일
//        Module Version : 1.0.0
//        File Name : _config.php
//        Author Name(email, tel1, cell) : 권혁태
//        Begin Date : .
//        Last Update :
//
//        Module & File Description
//
//==========================================================================================
//            GLOBAL VALUE DESCRIPTION
//------------------------------------------------------------------------------------------
//        VARIABLE NAME   DATATYPE    LENGTH  DESCRIPTION
//------------------------------------------------------------------------------------------
//            .
//
//
//
//            .
//==========================================================================================
//==========================================================================================
//        Version History
//------------------------------------------------------------------------------------------
// ==
// Version 1.0    ()
// ==
// Author Name(email, tel1, cell) :
// New Function Description
//
//
// Error debug Description
//
//
//
//==========================================================================================


	//--------------------------------------------------------------------------------------
	// Database Connection Configuration
	//--------------------------------------------------------------------------------------
		$dbHOST = array_key_exists('HTTP_X_FORWARDED_PROTO', $_SERVER) ? "db-ri-master.cojotvancutn.ap-northeast-2.rds.amazonaws.com" : "1.234.27.90";		// 서버주소 -> IP
		$sslPath = array_key_exists('HTTP_X_FORWARDED_PROTO', $_SERVER) ? $_SERVER['DOCUMENT_ROOT']."/common/ssl/rds-ca-2019-root.pem" : NULL;
		$dbPORT = "3306";					//	포트
		$dbUSER = "newtech";				// 접속계정
		$dbPASS = "sbxpr!@34";					// 계정비밀번호
	//--------------------------------------------------------------------------------------

	define("AES_SALT_KEY","no1sbxpr!@");


?>
