<?php
	require $_SERVER['DOCUMENT_ROOT']."/common/common.php";
	require $_SERVER['DOCUMENT_ROOT']."/000_login/class/clsLogin.php";


	switch( $_REQUEST['REQ_MODE'] ){

		case LOGIN:
			$RS = $cLogin->getData($_REQUEST);

			if($RS) {
				$_SESSION['USERID']			=		$RS['USERID'];
				$_SESSION['USER_NAME']		=   	$RS['USER_NAME'];
				$_SESSION['AUTH_LEVEL']		=   	$RS['AUTH_LEVEL'];
				$_SESSION['AUTH_ID']		=   	$RS['AUTH_ID'];
				
				session_register("USERID");
				session_register("USER_NAME");
				session_register("AUTH_LEVEL");
				session_register("AUTH_ID");

				unset($RS);
				echo "<SCRIPT>location.href='/200_billMngt/220_sendList/sendList.html';</SCRIPT>";
			} else {
				$cLogin->POP_ALERT("아이디나 비밀번호를 다시 확인하십시오.",false);
			}

			break;

			case LOGOUT:	// 로그아웃
				$_SESSION['USERID']		=	"";
				$_SESSION['USER_NAME']		=	"";
				$_SESSION['AUTH_LEVEL']		= 	"";
				$_SESSION['AUTH_ID']		=   "";

				session_unregister("USERID");
				session_unregister("USER_NAME");
				session_unregister("AUTH_LEVEL");
				session_unregister("AUTH_ID");

				echo "<HTML><SCRIPT>location.href='/';</SCRIPT></HTML>";

			break;


			/* 공지사항 조회 */
			case TABLE_TYPE_CD_NOTICE:
				$data =  $cLogin->getNotice();
				// if($data === HAVE_NO_DATA){
				// 	$response['status']  = SERVER_ERROR;
				// 	$response['message'] = "Internal Server Error";
				// }else{
					$response['status']  = OK;
					$response['message'] = "OK";	
					$response['data'] = $data;	
				// }
	
				echo json_encode($response);
			break;

			default : 
				echo "<SCRIPT>location.href='/';</SCRIPT>";
			break;


	}
	$cLogin->CloseDB();
?>
