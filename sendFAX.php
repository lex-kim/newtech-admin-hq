<?php
  require $_SERVER['DOCUMENT_ROOT']."/common/common.php";	
  require $_SERVER['DOCUMENT_ROOT']."/common/class/clsCommon.php";				// 환경설정파일 로딩 - 데이터변수 등
  require $_SERVER['DOCUMENT_ROOT']."/common/class/clsBillInfo.php";				// 환경설정파일 로딩 - 데이터변수 등

    /**
     * 팩스를 전송합니다. (전송할 파일 개수는 최대 20개까지 가능)
     * - 팩스전송 문서 파일포맷 안내 : http://blog.linkhub.co.kr/2561
     * - 팩스 과금 방식 안내 : https://partner.linkhub.co.kr/Customer/Notice/813
     * - 팝빌에 사전등록되지 않은 발신번호를 기재한 경우 팝빌 지정 발신번호로팩스가 전송 됩니다.
     * - [참고] "[팩스 API 연동매뉴얼] > 1.3. 팩스전송 제한 사유 안내"
     */
    // echo var_dump($_REQUEST);
	include 'common.php';
    // 팝빌 회원 사업자번호
    $testCorpNum = '3798700095';    //newtech

    // 팝빌 회원 아이디
    $testUserID = 'newtech34';

    // 팩스전송 발신번호
    $Sender = '025878488';

    // 팩스전송 발신자명
    $SenderName = 'newtech';

    // 팩스 수신정보 배열, 최대 1000건
    // $Receivers[] = array(
    //     // 팩스 수신번호
    //     'rcv' => '0221799114',
    //     // 수신자명
    //     'rcvnm' => 'dgmit'
    // );
    $Receivers[] = array(
        // 팩스 수신번호
        'rcv' => $_REQUEST['NBI_FAX_NUM'],
        // 수신자명
        'rcvnm' => $_REQUEST['NBI_CHG_NM']
    );
    // $Receivers[] = $faxArray ;
    
   
    $saveFullSRC2 =  $_SERVER['DOCUMENT_ROOT']."/faxSave/".$_REQUEST['NB_ID'].".html";
    $fnSRC =  ($_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ? 'https' : 'http') . '://'.$_SERVER['HTTP_HOST']."/_SLF.html?MGG_ID=".$_REQUEST['MGG_ID']."&NB_ID=".$_REQUEST['NB_ID'];
    // $fnSRC =  ($_SERVER['HTTPS'] == 'https' ? 'https' : 'http') . '://'.$_SERVER['HTTP_HOST']."/_SLF.html?MGG_ID=".$_REQUEST['MGG_ID']."&NB_ID=".$_REQUEST['NB_ID'];

    $fw = fopen($saveFullSRC2,'w');
    $fr = fopen($fnSRC,'r');

    if(!$fr){
        echo ("Can't not Open");
    } else {
        while(!feof($fr)){
            fwrite($fw,iconv("UTF-8","EUC-KR",fgets($fr,2048)));
        }
        fclose($fr);
    }
    fclose($fw);

    // 팩스전송파일, 해당파일에 읽기 권한이 설정되어 있어야 함. 최대 20개.
    $Files = array($saveFullSRC2);

    // 예약전송일시(yyyyMMddHHmmss) ex) 20151212230000, null인경우 즉시전송
    $reserveDT = null;
    
    // 광고팩스 전송여부
    $adsYN = false;

    // 팩스제목
    $title = $_REQUEST['MII_NM']." ".$_REQUEST['CAR_NUM'];

    // 전송요청번호
    // 파트너가 전송 건에 대해 관리번호를 구성하여 관리하는 경우 사용.
    // 1~36자리로 구성. 영문, 숫자, 하이픈(-), 언더바(_)를 조합하여 팝빌 회원별로 중복되지 않도록 할당.
    $requestNum = '';

    try {
        $receiptNum = $FaxService->SendFAX($testCorpNum, $Sender, $Receivers, $Files,
            $reserveDT, $testUserID, $SenderName, $adsYN, $title, $requestNum);
        if ( isset($receiptNum) ) {
            $_REQUEST['RFS_ID'] = '90230';

        }else{
            $_REQUEST['RFS_ID'] = '90290';
        }
        echo $clsBillInfo->updateFaxStatus($_REQUEST);

        if(file_exists($saveFullSRC2)) {
            // 기존파일 존재시 삭제
            unlink($saveFullSRC2);
        }
    }
    catch (PopbillException $pe) {
        $code = $pe->getCode();
        $message = $pe->getMessage();
        echo false;
       
    }
?>