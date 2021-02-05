<?php
/*
 ==========================================================================================
 COPYRIGHT ⓒ 2009. 디지엠정보기술(주)
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
 Module Name : 유틸리티 클래스
 Module Version : 1.0.0
 File Name : clsUtil.php
 Author Name(email, tel1, cell) : 권혁태
 Begin Date : 2007.03.08
 Last Update : 2007.03.08

 Module & File Description


 ==========================================================================================
 GLOBAL VALUE DESCRIPTION
 ------------------------------------------------------------------------------------------
 VARIABLE NAME   DATATYPE    LENGTH  DESCRIPTION
 ------------------------------------------------------------------------------------------


 ==========================================================================================*/

error_reporting(E_ERROR | E_WARNING);




class cUtil {

	/* -=-=-=- The Top of cUtil Class object -=-=-=- */

	/*
	 ==========================================================================================
	 Procedure Name			: setFileSize($tmpFilename)
	 Procedure Description	: $tmpFilename 읽어들여 크기를 반환한다
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
	function setFileSize($tmpFilename) {

		$tmpSIZE = filesize($tmpFilename); // bytes
		$rstSIZE = "";		$tmpNAME = " bytes";

		switch($tmpSIZE) {

			case $tmpSIZE < 1024:					// bytes
				$rstSIZE	= $tmpSIZE;
				$tmpNAME	= " B";
				break;
					
			case $tmpSIZE < (1024*1024):			// Kbytes
				$rstSIZE	= $tmpSIZE / 1024;
				$tmpNAME	= " KB";
				break;

			case $tmpSIZE < (1024*1024*1024):		// Mbytes
				$rstSIZE	= $tmpSIZE / (1024*1024);
				$tmpNAME	= " MB";
				break;

			case $tmpSIZE < (1024*1024*1024*1024):	// Gbytes
				$rstSIZE	= $tmpSIZE / (1024*1024*1024);
				$tmpNAME	= " GB";
				break;

			default:
				$rstSIZE	= $tmpSIZE;
				$tmpNAME	= " B";
		}

		return round($rstSIZE,2).$tmpNAME;
	}



	/*
	 ==========================================================================================
	 Procedure Name			: pdsDownload($orgname, $tgtFileName, $tgtPath)
	 Procedure Description	: 파일 다운로드
	 Logic Description		:
	 Result Type			:
	 ------------------------------------------------------------------------------------------
	 ## Parameter Description
	 $orgname = `MFF_ORG_NM`, $tgtFileName = `MFF_FILE_NM`, $tgtPath = NOTICE_UPLOAD_PATH
	 ==========================================================================================
	 USAGE
	 ------------------------------------------------------------------------------------------
	 Code  '

	 Result '
	 ==========================================================================================*/
	 function pdsDownload($tgtFileName, $orgname, $tgtPath) {
		$filename = $tgtFileName;
		$target_path = $_SERVER['DOCUMENT_ROOT'].$tgtPath;  
		if (!is_file($target_path.$filename)) { 
			// die('The file appears to be invalid.'); 
		    //header("HTTP/1.0 404 Not Found");
			echo $orgname."/".$filename."\n";
			echo "<script>alert('파일을 찾을 수 없습니다.');history.go(-1);</script>";
			exit;	
		}		

		$filepath = $target_path.$filename ;
		$filesize = filesize($filepath);
		$filename = substr(strrchr('/'.$filepath, '/'), 1);
		$extension = strtolower(substr(strrchr($filepath, '.'), 1));

		//IE인가 HTTP_USER_AGENT로 확인
		$ie= isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false;
		$outname= "";
		//IE인경우 한글파일명이 깨지는 경우를 방지하기 위한 코드
		if( $ie ){ 
			$outname = iconv('utf-8', 'euc-kr', $orgname); 
		} else {
			$outname = $orgname;
		}

		
		//기본 헤더 적용
		header('Content-Description: File Transfer');
		header('Content-Disposition: attachment; filename="'.$outname.'"');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: '.sprintf('%d', $filesize));
		header('Expires: 0');
		
		// IE를 위한 헤더 적용
		if( $ie ){
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Type: file/unknown');
		} else {
			header('Content-Type: application/octet-stream');
			header('Pragma: no-cache');
		}
		
		//해당 파일을 binary로 읽어와 출력
		$handle = fopen($filepath, 'rb');
		fpassthru($handle);
		fclose($handle);
	}
	/*
	 ----------------------------------------------------------------------------------------------
	 Procedure Name			: makeFolder($chkPATH)
	 Procedure Description	: 해당하는 폴더가 존재여부 검사 후 존재하지 않을 경우 해당 폴더 생성
	 Logic Description		: Fullpath를 검사하여 해당 폴더가 존재하지 않을 경우
	 앞쪽부터 검사하여 폴더가 존재하지 않을 경우 폴더를 한단계씩 생성한다.
	 Result Type			: BOOLEAN
	 ------------------------------------------------------------------------------------------
	 USAGE
	 ------------------------------------------------------------------------------------------
	 $chkPATH				: 생성할 폴더의 full-path
	 ----------------------------------------------------------------------------------------------*/
	function makeFolder($chkPATH) {

		$tmpARR  = explode("/", $chkPATH);	// /를 기준으로 폴더명 분리

		if(!is_dir($chkPATH)) { // 해당 폴더가 존재하는지 검사 - 없는 경우 탐색
			for($i = 0; $i < sizeof($tmpARR); $i++) {	// 최상위 폴더부터 차례로 검색
				$tmpTARGET = "";
				for($j = 0; $j < $i; $j++) { $tmpTARGET = $tmpTARGET.$tmpARR[$j]."/"; }	//echo "<BR>$j:".$tmpTARGET;
				if(!is_dir($tmpTARGET."/")) { $RTN = mkdir($tmpTARGET,0755); }	// 해당 레벨의 폴더가 존재하지 않을 경우 생성
			}
		} else {
			$RTN = 1;
		}

		return $RTN;
	}


	/*
	 ------------------------------------------------------------------------------------------
	 Procedure Name			: getContext($fnSRC)
	 Procedure Description	: 신규보드 등록
	 Logic Description		:
	  
	 Result Type				: Boolean
	 ------------------------------------------------------------------------------------------
	 ## Parameter Description
	 ------------------------------------------------------------------------------------------
	 $fnSRC					: 읽어올 파일이름 (Full-Path)
	 ------------------------------------------------------------------------------------------*/
	function getContext($fnSRC) {
		// 파일을 열어 해당 값을 문자열로 리턴하는 함수
		
		if(!$file=fopen($fnSRC,"r")) {
			echo("$fnSRC를 오픈하는데 실패하였습니다.<BR>");

			return 0;
		} else {
			while(!feof($file)) {	$char = $char.fgetc($file);	}
			fclose($file);

			return $char;
		}

	}


	/*
	 ------------------------------------------------------------------------------------------
	 Procedure Name			: removeDir($path)
	 Procedure Description	: 폴더삭제. 하위폴더 및 파일까지 일괄 삭제
	 Logic Description		:
	 Result Type			: ""
	 ------------------------------------------------------------------------------------------
	 ## Parameter Description
	 ------------------------------------------------------------------------------------------
	 $path			:	삭제할 폴더경로
	 ------------------------------------------------------------------------------------------
	 USAGE
	 ------------------------------------------------------------------------------------------
	 Code  '

	 Result '

	 ------------------------------------------------------------------------------------------*/
	function removeDir($path) {
		// Add trailing slash to $path if one is not there
		if (substr($path, -1, 1) != "/") {
			$path .= "/";
		}

		$normal_files = glob($path . "*");
		$hidden_files = glob($path . "\.?*");
		$all_files = array_merge($normal_files, $hidden_files);

		foreach ($all_files as $file) {
			# Skip pseudo links to current and parent dirs (./ and ../).
			if (preg_match("/(\.|\.\.)$/", $file))
			{
				continue;
			}

			if (is_file($file) === TRUE) {
				// Remove each file in this Directory
				unlink($file);
				echo "Removed File: " . $file . "<br>";
			}
			else if (is_dir($file) === TRUE) {
				// If this Directory contains a Subdirectory, run this Function on it
				removeDir($file);
			}
		}
		// Remove Directory once Files have been removed (If Exists)
		if (is_dir($path) === TRUE) {
			rmdir($path);
			echo "<br>Removed Directory: " . $path . "<br><br>";
		}
	}

	/*
	 ------------------------------------------------------------------------------------------
	 Procedure Name			: isSame($src,$tgt,$STR_RTN)
	 Procedure Description	: $src와 $tgt가 동일하면 "Checked" 리턴 그렇지 않을 경우 ""
	 Logic Description		:
	 Result Type				: ""
	 ------------------------------------------------------------------------------------------
	 ## Parameter Description
	 ------------------------------------------------------------------------------------------
	 $src		:	비교 원본값
		$tgt		:	데이터 실제값
		$STR_RTN	:	두 값이 같은 경우 리턴될 STRING
		------------------------------------------------------------------------------------------
		USAGE
		------------------------------------------------------------------------------------------
		Code  '

		Result '

		------------------------------------------------------------------------------------------*/
	function isSame($src,$tgt,$STR_RTN) { if($src == $tgt ) { return $STR_RTN; } else { return ""; } }



	/*
	 ------------------------------------------------------------------------------------------
	 Procedure Name			: getEXT($FN)
	 Procedure Description	: $FN의 확장자를 반환한다.
	 Logic Description		:
	 Result Type			: String
	 ------------------------------------------------------------------------------------------
	 ## Parameter Description
	 ------------------------------------------------------------------------------------------
	 $FN		:	확장자를 확인할 파일명(Fullpath)
	 ------------------------------------------------------------------------------------------
	 USAGE
	 ------------------------------------------------------------------------------------------
	 Code  '

	 Result '

	 ------------------------------------------------------------------------------------------*/
	function getFileEXT($FN) { $tmpFN = explode(".",$FN); return strtoupper($tmpFN[sizeof($tmpFN)-1]); }




	/*
	 ------------------------------------------------------------------------------------------
	 Procedure Name			: chkValidEXT($chkFN, $chkLST)
	 Procedure Description	: $chkFN이 $chkLST에 포함되어있는지 검사하여 포함된 경우 1 반환
	 Logic Description		:
	 Result Type			: boolean
	 ------------------------------------------------------------------------------------------
	 ## Parameter Description
	 ------------------------------------------------------------------------------------------
	 $chkFN		:	체크할 대상 string
	 $chkLST		:	체크할 목록
	 ------------------------------------------------------------------------------------------
	 USAGE
	 ------------------------------------------------------------------------------------------
	 Code  '

	 Result '

	 ------------------------------------------------------------------------------------------*/
	function chkValidEXT($chkFN, $chkLST) { if(eregi($chkLST,$chkFN)) { return 1; } else { return 0; } }



	/*
	 ------------------------------------------------------------------------------------------
	 Procedure Name			: cutstr($str, $maxlen)
	 Procedure Description	: 원하는 길이만큼 글자를 자른 후 리턴
	 Logic Description		:
	 Result Type			: string
	 ------------------------------------------------------------------------------------------
	 ## Parameter Description
	 ------------------------------------------------------------------------------------------
	 $str			:	대상 string
	 $maxlen		:	자를 길이
	 ------------------------------------------------------------------------------------------
	 USAGE
	 ------------------------------------------------------------------------------------------
	 Code  '

	 Result '

	 ------------------------------------------------------------------------------------------*/
	function cutstr($str, $maxlen) {
		/*
		 if( strlen($str) <= $maxlen ) { $retStr=$str; return $retStr; }
		 $effective_max = $maxlen - 3;  $remained_byte = $effective_max;
		 $retStr=""; $hanStart=0;
		 for ( $i=0; $i < $effective_max; $i++ ) {
			$char=substr($str,$i,1);
			if( ord($char) <= 127 ) { $retStr .= $char; $remained_byte--; continue; }
			if( !$hanStart && $remained_byte > 1 ) { $hanStart = true; $retStr .= $char; $remained_byte--; continue; }
			if( $hanStart ) { $hanStart = false; $retStr .= $char; $remained_byte--; }
			}
			return $retStr .= "..."; */
		return $this->cut_str($str, $maxlen, "...");
	}

	function cut_str($str, $len, $suffix="…")  /* UTF8 지원 */
	{
		global $g4;

		$s = substr($str, 0, $len);
		$cnt = 0;    // 마지막 글자에서 잘린 후 남겨진 바이트 수

		$s_len = strlen($s);

		// UTF-8 마지막 글자 깨짐 처리
		/*
		if (strtoupper($g4['charset']) == 'UTF-8') {
		for ($i=0; $i<$s_len; $i++) {

		$oc = ord($s[$i]);

		if ( ($oc & 0xF8) == 0xF0 )        // 4byte
		{
		if ($i+4 >= $s_len) { $cnt = ($s_len-$i)%4;    break; }
		else $i+=3;
		}
		else if ( ($oc & 0xF0) == 0xE0 )    // 3byte
		{
		if ($i+3 >= $s_len) { $cnt = ($s_len-$i)%3;    break; }
		else $i+=2;
		}
		else if ( ($oc & 0xE0) == 0xC0 )    // 2byte
		{
		if ($i+2 >= $s_len) { $cnt = ($s_len-$i)%2;    break; }
		else $i++;
		}
		else                                    // 1byte
		$cnt = 0;
		}
		if ($cnt)
		$s = substr($s, 0, $s_len - $cnt);
		}
		else    // EUC-KR, 마지막 글자 깨짐 처리
		{
		for ($i=0; $i<$s_len; $i++) {

		$oc = ord($s[$i]);
		if ( $oc > 0x7F )
		{
		if ($i+2 >= $s_len ) { $cnt = ($s_len-$i)%2;    break; }
		else $i++;
		}
		else
		$cnt = 0;
		}
		if ($cnt)
		$s = substr($s, 0, $s_len - $cnt);
		}
		*/
		/* UTF 8 전용 */
		for ($i=0; $i<$s_len; $i++) {

			$oc = ord($s[$i]);

			if ( ($oc & 0xF8) == 0xF0 )        // 4byte
			{
				if ($i+4 >= $s_len) { $cnt = ($s_len-$i)%4;    break; }
				else $i+=3;
			}
			else if ( ($oc & 0xF0) == 0xE0 )    // 3byte
			{
				if ($i+3 >= $s_len) { $cnt = ($s_len-$i)%3;    break; }
				else $i+=2;
			}
			else if ( ($oc & 0xE0) == 0xC0 )    // 2byte
			{
				if ($i+2 >= $s_len) { $cnt = ($s_len-$i)%2;    break; }
				else $i++;
			}
			else                                    // 1byte
			$cnt = 0;
		}
		if ($cnt)
		$s = substr($s, 0, $s_len - $cnt);
		if (strlen($s) >= strlen($str))
		$suffix = "";
		return $s . $suffix;
	}


	//------------------------------------------------------------------------------------------
	// Procedure Name : filldata($strDATA, $totalLength)
	// Procedure Description : $strDATA를 좌측정렬한 후 $totalLength에서 모자른 만큼 공백을
	//							더하여 리턴한다.
	//------------------------------------------------------------------------------------------
	// ## Parameter Description
	//  $strDATA	 : 데이터 값
	//	$totalLength : 리턴할 총 길이
	//------------------------------------------------------------------------------------------
	//            USAGE
	//------------------------------------------------------------------------------------------
	//  Code  '//
	//     echo filldata("TEST", 10);
	//
	//  Result '//
	//     TEST______
	//------------------------------------------------------------------------------------------
	function FillData($strDATA, $totalLength) {

		$blankNUM = $totalLength - strlen($strDATA);
		return substr($strDATA.str_repeat(" ",$blankNUM),0,$totalLength);

	}



	/*
		초를 시 : 분 : 초  로 반환한다
		*/
	function getTimeFormat($tmpSECOND) {
		$tmpSEC = $tmpSECOND % 60;				if($tmpSEC < 10)	{ $tmpSEC = "0".$tmpSEC; }
		$tmpMIN_ORG = floor($tmpSECOND / 60);
		$tmpMINUTE = $tmpMIN_ORG % 60;	if($tmpMINUTE < 10) { $tmpMINUTE = "0".$tmpMINUTE; }
		$tmpHOUR = floor($tmpMIN_ORG / 60);		if($tmpHOUR < 10)	{ $tmpHOUR = "0".$tmpHOUR; }

		return $tmpHOUR.":".$tmpMINUTE.":".$tmpSEC;
	}

	/*
	 ------------------------------------------------------------------------------------------
	 Procedure Name			: ProgressBar($pKind,$pValue,$pWidth)
	 Procedure Description	: 진행 그래프를 출력한다.
	 Logic Description		:
	 Result Type			: string
	 ------------------------------------------------------------------------------------------
	 ## Parameter Description
	 ------------------------------------------------------------------------------------------
	 $pKind		:	그래프 종류(색)		1 ~ 3
	 $pValue		:	그래프 값 %로 표기
	 $pWidth		:	그래프의 총 너비
	 ------------------------------------------------------------------------------------------
	 USAGE
	 ------------------------------------------------------------------------------------------
	 Code  '

	 Result '

	 ------------------------------------------------------------------------------------------*/
	function ProgressBar($pKind,$pValue,$pWidth) {
		$tmpProgress =  "
				<!--TABLE width=\"".$pWidth."\" height=\"12\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
					<TR>
						<TD width=\"".$pValue."%\" align=center background=\"images/graph_bar".$pKind.".gif\"></TD>
						<TD width=\"".(100-$pValue)."%\" align=left>".$pValue." %</TD>
					</TR>
				</TABLE-->
				<TABLE width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
					<TR>
						<TD width=\"".($pValue-10)."%\">	
							<TABLE width=\"100%\" height=\"12\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
								<TR>
									<TD width=\"".$pValue."%\" align=center background=\"images/graph_bar".$pKind.".gif\"></TD>
								</TR>
							</TABLE>				
						</TD>
						<TD width=\"".(100-$pValue+10)."%\" align=left>
							<TABLE  width=\"100%\" height=\"12\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
								<TR>
									<TD align=left>".$pValue." %</TD>
								</TR>
							</TABLE>		
						</TD>
					</TR>
				</TABLE>
			";
		return $tmpProgress;
	}

	/* 파일로그 쌓기 */
	function writeLog($strLOG) {
		$fp = fopen("[".DATE('Y-m-d')."] SQL_LOG.log", "a");
		fwrite($fp, "[".DATE('Y-m-d H:i:s')."] ".str_replace("\r\n","",$strLOG)."\r\n");
		fclose($fp);
	}

	/*
	 ------------------------------------------------------------------------------------------
	 Procedure Name			: Tel_format($str,$str1,$str2)
	 Procedure Description	: 전화번호 서식출력
	 Logic Description		:
	 Result Type				: string
	 ------------------------------------------------------------------------------------------
	 ## Parameter Description
	 ------------------------------------------------------------------------------------------
	 $str		:	전화번호
		$str1		:	구분자 1
		$str2		:	구분자 2
		------------------------------------------------------------------------------------------
		USAGE
		------------------------------------------------------------------------------------------
		Code  '
		echo Tel_format("01112345678",")","-");

		Result '
		011)1234-5678
		------------------------------------------------------------------------------------------*/
	function Tel_format($str,$str1,$str2){

		$str = str_replace("(","",str_replace(")","",str_replace("-","",trim($str))));

		if(strlen(trim($str)) < 7) {
			return $str;
			exit;
		}

		if(substr($str,0,2) == "02") {
			// 서울인 경우
			$tmpPRE = "02";
		} else {
			//기타지역 및 휴대폰인 경우
			$tmpPRE = substr($str,0,3);
		}


		if( (strlen($str) - strlen($tmpPRE)) == 8 ) {
			// 국번이 4자리인 경우
			$tmpMID = substr($str, strlen($tmpPRE), 4);
			$tmpPOST = substr($str,strlen($str)-4, 4);
		} else {
			// 국번이 3자리인 경우
			$tmpMID = substr($str, strlen($tmpPRE), 3);
			$tmpPOST = substr($str,strlen($str)-4, 4);
		}

		return $tmpPRE.$str1.$tmpMID.$str2.$tmpPOST;

	}

	/*
	 ------------------------------------------------------------------------------------------
	 Procedure Name			: POP_ALERT($vTEXT,$mode)
	 Procedure Description	: ALERT 메시지 출력
	 Logic Description		:
	 Result Type				: ""
	 ------------------------------------------------------------------------------------------
	 ## Parameter Description
	 ------------------------------------------------------------------------------------------
	 $vTEXT				: 팝업할 내용
		$mode				: 이전화면여부. -1이면 이전화면으로
		------------------------------------------------------------------------------------------*/
	function POP_ALERT($vTEXT,$mode) {
		if($mode == false ) { $tmpBACK = "history.go(-1);"; }
		echo "<SCRIPT>alert('".$vTEXT."'); ".$tmpBACK." </SCRIPT>"; 
	}

		
	function phone_format($phone){
		$phone = preg_replace("/[^0-9]/", "", $phone);
		$length = strlen($phone);

		switch($length){
		case 11 :
			return preg_replace("/([0-9]{3})([0-9]{4})([0-9]{4})/", "$1-$2-$3", $phone);
			break;
		case 10: 
			return preg_replace("/(^02.{0}|^01.{1}|[0-9]{3})([0-9]+)([0-9]{4})/","$1-$2-$3",$phone);
			break;
		case 9:
			return preg_replace("/([0-9]{2})([0-9]{3})([0-9]{4})/", "$1-$2-$3", $phone);
			break;
		default :
			return $phone;
			break;
		}
	}

	function nullChk($val,$rep){
        return $val != null ? $val : $rep;
	}
	
	function biz_format($bizNo){
		$bizNo = preg_replace("/[^0-9]/", "", $bizNo);

		return preg_replace("/([0-9]{3})([0-9]{2})([0-9]{5})/", "$1-$2-$3", $bizNo);
	}

	function date_format($date){
		$date = preg_replace("/[^0-9]/", "", $date);

		return preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})/", "$1-$2-$3", $date);
	}
		/* -=-=-=- The Bottom of cUtil Class object -=-=-=- */
}

?>