<?php
	function session_register($name) {
		if(isset($GLOBALS[$name])) $_SESSION[$name] = $GLOBALS[$name];
		$GLOBALS[$name] = &$_SESSION[$name];
	}
	function session_unregister($name) {
		unset($_SESSION[$name]);
	}

	function getRadio($radioArr,  $radioNM, $selectValue){
		$radioText = "";
		foreach($radioArr as $idx => $item){
			$radioID = $radioNM."_".$idx;
			$selectedText = "";
			if(empty($selectValue) && $idx == 0){
				$selectedText = "checked";
			}else{
				if($item['code'] == $selectValue){
					$selectedText = "checked";
				}
			}
			$radioText .= "<label class='radio-inline' for='".$radioID."'>";
			$radioText .= "<input type='radio' ".$selectedText." name='".$radioNM."' id='".$radioID."' value='".$item['code']."'>".$item['domain']."</label>";
		}

		return $radioText;
	}

	function getOption($optionArr, $baseText, $selectValue){   // 동적으로 select에 값을 세팅해주는 함수
		$baseStr = empty($baseText) ? "선택" : $baseText;
		$optionText = "<option value=''>=".$baseStr."=</option>";
		foreach($optionArr as $idx => $item){
			$selectedText = "";
			if($item['code'] == $selectValue){
				$selectedText = "selected";
			}
			$optionText  .= "<option value='".$item['code']."' ".$selectedText.">".$item['domain']."</option>";
		}
		return $optionText;
	}

	function setOption($optionArr, $selectValue){   // 동적으로 select에 값을 세팅해주는 함수
		$optionText = "<option value=''>선택</option>";
		foreach($optionArr as $idx => $item){
			$selectedText = "";
			if($item['id'] == $selectValue){
				$selectedText = "selected";
			}
			$optionText  .= "<option value='".$item['code']."' ".$selectedText.">".$item['domain']."</option>";
		}
		return $optionText;
	}

	function bizNoFormat($bizValue){
		return preg_replace("/(\d{3})(\d{2})(\d{5})/", "$1-$2-$3", $bizValue);
	}

	function setPhoneFormat($phone){
		if (substr($phone,0,2)=='02'){
			return preg_replace("/([0-9]{2})([0-9]{3,4})([0-9]{4})$/", "\\1-\\2-\\3", $phone);
		}else if(strlen($phone)=='8' && (substr($phone,0,2)=='15' || substr($phone,0,2)=='16' || substr($phone,0,2)=='18')){
			return preg_replace("/([0-9]{4})([0-9]{4})$/", "\\1-\\2", $phone);
		}else{
			return preg_replace("/([0-9]{3})([0-9]{3,4})([0-9]{4})$/", "\\1-\\2-\\3", $phone);
		}
	}
	
	function number2hangul($number){
        $num = array('', '일', '이', '삼', '사', '오', '육', '칠', '팔', '구');
        $unit4 = array('', '만', '억', '조', '경');
        $unit1 = array('', '십', '백', '천');

        $res = array();

        $number = str_replace(',','',$number);
        $split4 = str_split(strrev((string)$number),4);

        for($i=0;$i<count($split4);$i++){
                $temp = array();
                $split1 = str_split((string)$split4[$i], 1);
                for($j=0;$j<count($split1);$j++){
                        $u = (int)$split1[$j];
                        if($u > 0) $temp[] = $num[$u].$unit1[$j];
                }
                if(count($temp) > 0) $res[] = implode('', array_reverse($temp)).$unit4[$i];
        }
        return implode('', array_reverse($res));
	}

	function getProtocol(){
		$https = !empty($_SERVER['HTTPS']) && strcasecmp($_SERVER['HTTPS'], 'on') === 0 ||
        !empty($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
            strcasecmp($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') === 0;

		return ($https) ? 'https://' : 'http://';
	}

	function errorAlert($msg){
		echo "<script>alert('".$msg."');
		if(opener == null){
			history.back(-1);
		}else{
			window.close();
		}</script>";
		exit;
	}
	
	session_start();
	error_reporting(E_ALL);
	date_default_timezone_set('Asia/Seoul');

	require $_SERVER['DOCUMENT_ROOT']."/common/dataconfig.php";				// 환경설정파일 로딩 - 데이터변수 등
	require $_SERVER['DOCUMENT_ROOT']."/common/var_const.php";				// 환경설정파일 로딩 - 데이터변수 등
	require $_SERVER['DOCUMENT_ROOT']."/common/error_msg.php";				// 환경설정파일 로딩 - 데이터변수 등
	require $_SERVER['DOCUMENT_ROOT']."/common/_config.php";				// 환경설정파일 로딩
	require $_SERVER['DOCUMENT_ROOT']."/common/class/clsDBManager.php";		// DB CLASS INCLUDE
?>
