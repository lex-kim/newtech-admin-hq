/** 메세지 */
const SAVE_SUCCESS_MSG = '저장되었습니다.';  
const ADD_SUCCESS_MSG = '등록에 성공하였습니다.';  
const UPDATE_SUCCESS_MSG= '수정에 성공하였습니다.';
const DELETE_SUCCESS_MSG= '삭제에 성공하였습니다.';
const CONFIRM_DELETE_MSG= '삭제하시겠습니까?';
const OK_MSG = '확인';
const DEL_MSG = '삭제';
const CANCEL_MSG = '취소';

/** 중복관련 */
const CONFLICT_MSG_1100 = '중복된 아이디가 존재합니다.';  
const CONFLICT_MSG_1200 = '중복된 출력순서가 존재합니다.';  
const CONFLICT_MSG_1300 = '중복된 이름이 존재합니다.';  

/** 에러관련 */
const ERROR_MSG_NO_DATA = '조회된 데이터가 없습니다.';              // 데이터 미조회
const ERROR_MSG_1100 = '서버와의 연결이 원활하지 않습니다.';          // 서버와의 쿼리연결에는 이상이 없는 문제 (예. HAVE_NO_DATA, SERVER_ERROR 같은 오류)
const ERROR_MSG_1200 = '서버와의 통신오류 입니다';                  // 서버와의 쿼리자체에 문제가 있는경우 (리턴이 json형식이 아니고 오류 메세지를 받을 경우)
const ERROR_MSG_1300 = '오류가 발생했습니다. 관리자에게 문의해주세요.';  // ajax 통신 자체에서 오류일 경우
const ERROR_MSG_1400 = '정상적인 접근이 아닙니다. 다시 시도해주세요.';  

/** validation  */
const ERROR_VALIDATE_PHONE = '번호양식이 올바르지 않습니다.';
const ERROR_VALIDATE_EMAIL = '이메일양식이 올바르지 않습니다.';

/** 보험사 */
const ERROR_INSU_NO_DATA = '조회된 보험사 정보가 없습니다.';
