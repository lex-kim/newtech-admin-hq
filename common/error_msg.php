<?php
/* 에러메시지 정의 파일 */

/*
  @name genErorMsg
  @return JSON;
  @description  메소드 수행결과 AJAX를 위한 에러메시지 및 정상메시지 생성
  @author 권혁태
  @version  1.0
*/
function genErorMsg($resultMsg){
  if($resultMsg !== OK) {
    // ERROR CASE
    switch($resultMsg) {
      case ERROR_NOT_ALLOWED_FILE:
        $message = "ERROR_NOT_ALLOWED_FILE";
        $code = ERROR_NOT_ALLOWED_FILE;
      break;
      case ERROR_MOVE_FILE:
        $message = "ERROR_MOVE_FILE";
        $code = ERROR_MOVE_FILE;
      break;
      case ERROR_NO_FILE:
        $message = "ERROR_NO_FILE";
        $code = ERROR_NO_FILE;
      break;
      case ERROR_DB_INSERT_DATA:
        $message = "ERROR_DB_INSERT_DATA";
        $code = ERROR_DB_INSERT_DATA;
      break;
      case ERROR_DB_INSERT_ITEM_DATA:
        $message = "ERROR_DB_INSERT_ITEM_DATA";
        $code = ERROR_DB_INSERT_ITEM_DATA
        ;
      break;
      case ERROR_DB_UPDATE_DATA:
        $message = "ERROR_DB_UPDATE_DATA";
        $code = ERROR_DB_UPDATE_DATA;
      break;
      case ERROR_DB_DELETE_DATA:
        $message = "ERROR_DB_DELETE_DATA";
        $code = ERROR_DB_DELETE_DATA;
      break;
      case ERROR_DB_DELETE_ITEM_DATA:
        $message = "ERROR_DB_DELETE_ITEM_DATA";
        $code = ERROR_DB_DELETE_ITEM_DATA;
      break;
      case ERROR_DB_SELECT_DATA:
        $message = "ERROR_DB_SELECT_DATA";
        $code = ERROR_DB_SELECT_DATA;
      break;
      case ERROR_DB_GET_MAX_NUM:
          $message = "ERROR_DB_GET_MAX_NUM";
          $code = ERROR_DB_GET_MAX_NUM;
        break;
      default:
        $message = "ERROR";
        $code = 99999;
    }

    header('HTTP/1.1 500');
    header('Content-Type: application/json; charset=UTF-8');

  } else {
    // NORMAL CASE
      $message = "OK";
      $code = "200";
  }
  die(json_encode(array("message" => $message, "code" => $code)));
}
?>
