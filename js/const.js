
/* BASE CODE */
const OK = "200";
const ALREADY_EXISTS = "208";
const SERVER_ERROR = "500";
const NOT_EXIST = "404";
const CONFLICT = "409";
const ERROR = "400";
const HAVE_NO_DATA = '99999';
const YN_Y = 'Y';
const YN_N = 'N';
const NOT_CRR_3 = "00000";   // 지역 구 까지만 검색하겠다는 상수
const NOT_CRR_2 = "000";   // 지역 구 까지만 검색하겠다는 상수
const STOCK_USE = "12130";   // NT_CODE.REF_STOCK_TYPE = '12130' 사용
const XMS = '90101'; //UMS 발송타입 XMS(LMS/SMS)
const LMS = 90101;   //발송타입 LMS
const FAX = 90104;   //발송타입 FAX
const NOT_AUTH_READ_TEXT = "조회할 수 없는 권한을 가진 페이지 입니다.";
const LOGIN = 1000; //로그인
const LOGOUT = 1001; //로그아웃
const NSRSN_SEPARATOR = '/::/';
const DEFAULT_DATE_FORMAT = "0000-00-00";
const DEL_SALES_CASH_BILL =  860219;  // 지출증빙 현금영수증 취소
const DEL_SALES_TAX_BILL =  860229;  // 지출증빙 세금계산서 취소

/* SWITCH CONST */
const CASE_CREATE = 1110;  // 등록, 저장
const CASE_READ = 1120;  // 데이터 조회
const CASE_UPDATE = 1130;  // 데이터 수정
const CASE_UPDATE_TEAM = 1131;  // 보험사팀정보 수정요청 수락
const CASE_UPDATE_REGION = 1132;  // 보험사팀 담당지역 수정요청 수락
const CASE_DELETE = 1140;  // 데이터 삭제
const CASE_LIST = 1150;  // 메인 리스트 조회
const CASE_LIST_SUB = 1151;  // 하위 리스트 조회
const CASE_LIST_DETAIL = 1152;  // 상세 리스트 조회
const CASE_TOTAL_LIST = 1160; //전체리스트 조회
const CASE_DELETE_FILE = 1170; //첨부파일 삭제
const CASE_DOWNLOAD_FILE = 1180; //첨부파일 다운로드
const CASE_CONFLICT_CHECK= 1190; //중복확인

/* CODE_DIC */
const GET_AUTHORITY = 20140;    // 권한코드 조회
const GET_PARTS_TYPE = 20150;   // 부품타입 조회
const GET_PARTS_INFO = 20155;   // 부품구분
const GET_CAR_TYPE = 20160;     // 차량타입조회
const GET_REF_DATA = 52000;     // 해당레퍼런스 data 받기
const GET_REF_CLAIM_TYPE = 51140; // 일방/쌍방/일부

/* OPTION */
const OPTIONS_DIVISION = 1210; // 구분옵션 조회 
const OPTIONS_AREA = 1211;  // 지역 자동완성 
const OPTIONS_IW = 1212;  // 보험사옵션 리스트
const OPTIONS_GW = 1213;  // 공업사옵션 리스트  
const OPTIONS_CRR = 1214;  // 지역 자동완성(아이디포함)
const OPTIONS_CPPS = 1215;  // 취급부품 조회
const OPTIONS_YEAR = 1216;  // 해당년 조회
const OPTIONS_MONTH = 1217;  // 해당월 조회
const OPTIONS_BILL_YEAR =  1218;  // 청구 해당년 조회(가장 최신과 가장 처음)

/* 정산 및 압출고관리 */
const STOCK_BILL = 'BILL';  // 청구서재고
const STOCK_INDE = 'INDEPENDENT';   //독립
const STOCK_SUBST = 'SUBSTITUTION'; //대체
const PART = 'PART' // 공급부품
const PART_DEFAULT = 'PART_DEFAULT' // 사용부품
const GET_STOCK_TYPE = 1400 //입출고 구분(출거/수거/사용) 

/* GW */
const GET_GARGE_INFO = 20170;     // 공업사 정보
const GET_GARGE_PARTS_INFO = 20180;     // 해당공업사 사용부품 정보

/* REF */
const GET_REF_OPTION = 51001;     // 공업사관리 옵션
const GET_REF_EXP_TYPE = 51002;   // 공업사관리 청구식관리
const GET_REF_SCALE_TYPE = 51003;   // 공업사관리 규모
const GET_REF_REACTION_TYPE = 51004;   // 공업사관리 규모
const GET_REF_AOS_TYPE = 51005; //공업사관리 AOS관리
const GET_REF_STOP_REASON_TYPE = 51006; //공업사관리 거래중단사유
const GET_REF_CONTRACT_TYPE = 51007; //공업사관리 거래여부
const GET_REF_MAIN_TARGET_TYPE = 51008; //공업사관리 주요정비
const GET_REF_PAY_EXP_TYPE = 51009; //공업사관리 지급식관리
const GET_REF_NMI_INSU_TYPE = 51010; //공업사관리 지급가능보험사
const GET_REF_OWN_TYPE = 51011; //공업사관리 자가여부
const GET_REF_INSU_CHAGE_TYPE = 51012; //공업사관리 담당여부
const GET_REF_RECKON_TYPE = 51013; //교환/판금
const GET_REF_WORK_PARIORITY_TYPE = 51014; //작업항목관리 우선순위(부위)
const GET_REF_WORK_PART = 51015; //작업항목관리
const GET_REF_CLAIMANT_TYPE = 51016; // 등록주체
const REF_CENTER_CLAIMANT = 51017;  // 등록주체 중 본사
const REF_DEPOSITEE_TYPE = 51018; // 입금여부구분
const REF_FAX_STATUS = 51019; // 팩스전송상태
const REF_DEPOSIT_TYPE = 51020 // 입금여부상세
const REF_EXCLUDE_REASON = 51021 // 지급제외사유
const REF_DECREMENT_REASON = 51022 // 감액사유
const REF_COOP_REACTION = 51023 // 협조반응
const REF_COOP_TYPE = 51024 // 협조구분
const REF_CONSIGN_STATUS = 51025 // 위탁장비상태
const REF_CONSIGN_SPOT = 51026 // 위탁장비위치
const REF_POINT_STATUS = 51027 // 포인트 상태
const REF_POINT_TYPE = 51028 // 포인트종류(수리서/쇼핑몰/관리자)
const GET_REF_CAR_TYPE = 51030;    // 차량타입
const GET_REF_SALES_FILE = 51040;  // 첨부파일

/* 작업구분 */
const WORK_PART_INSIDE_TYPE = '30330';       //실내
const WORK_PART_LOWER_TYPE = '30335';        //하체
const RACON_CHANGE_CD = '11210';             //교환
const RACON_METAL_CD = '11220';             //판금
const RACON_CHANGE_NM = '교환';             //교환
const RACON_METAL_NM = '판금';             //판금
const WORK_TYPE_ONE = 30310;              //단일
const WORK_TYPE_LEFT = 30320;             //좌
const WORK_TYPE_RIGHT = 30325;            //우
const RLT_MY_CAR_TYPE = 11410;            //자차
const RLT_PI_TYPE = 11420;               //대물
const RLT_BOTH_TYPE = 11430;             //쌍방
const RLT_PART_TYPE = 11440;             // 일부

/* IW */
const GET_INSU_TYPE = 1300; // 보험사분류 조회
const GET_INSU_TEAM = 1301; // 보험사팀 조회
const GET_HEADER_CNT = 1302; // 테이블 동적 생성을 위한 CNT 조회
const GET_GARAGE_CLAIM_INFO = 1500;   // 공업사(아이디/이름/청구담당)
const GET_INSU_CHARGE_INFO = 1600;    // 해당보험사 보험사담당자정보
const GET_REQUEST_REGION  = 1216; // 보험사팀 담당지역  수정요청 데이터
const INSU_TYPE_NORMAL = 11110;     // 보험타입-일반
const INSU_TYPE_COOPER = 11111;    // 보험타입-공제
const INSU_TYPE_RENT = 11112;      // 보험타입-렌트

/* 주체 구분 */
const MODE_TARGET_ALL = 10101;    	// 전체
const MODE_TARGET_GW = 10102;    	// 공업사
const MODE_TARGET_IW = 10103;    	// 보험사
const MODE_TARGET_SA = 10104;    	// 영업사원
const MODE_TXT_HQ = "본";    	// 본사
const MODE_TXT_GW = "공";    	// 공업사
const MODE_TXT_IW = "보";    	// 보험사
const MODE_TXT_SA = "영";    	// 영업사원

/* 게시판 관리  */
const TABLE_TYPE_CD_NOTICE = 20110;      // 공지사항
const TABLE_TYPE_CD_BOARD = 20120;      // 사내게시판

/* ERROR CODE */
const ERROR_NOT_ALLOWED_FILE = 901100;
const ERROR_DB_INSERT_DATA = 902100;
const ERROR_DB_INSERT_ITEM_DATA = 902101;
const ERROR_DB_UPDATE_DATA = 902120;
const ERROR_DB_DELETE_DATA = 902140;
const ERROR_DB_DELETE_ITEM_DATA = 902141;
const ERROR_DB_SELECT_DATA = 902200;
const ERROR_DB_GET_MAX_NUM = 902300;


/** ERP url */
const ERP_SHOP_ORDER_URL = "act/index.php";
const ERP_CONTROLLER_URL = "controller/AjaxController.php";
const NOT_SELECT_FILE_TEXT = '선택된 파일 없음';

const EC_BUYER_TYPE_CONSIGN = 80222; //위탁
/** ERP 배송정책 결제구분 */
const EC_DELIVERY_FEE_POST_PAY = 81610; //착불
const EC_DELIVERY_FEE_PRE_PAY= 81612;  //선불
const EC_DELIVERY_FEE_FREE_PAY= 81614; //무료
const EC_DELIVERY_FEE_METHOD = 81616; //통합

/** ERP 배송정책 부가기준  */
const EC_DELIVERY_FEE_TYPE_NUM = 81620; //수량
const EC_DELIVERY_FEE_TYPE_PRRICE = 81622;  //금액
const EC_DELIVERY_FEE_TYPE_FIX= 81624; //고정


const CHECK_EXIST_PARTNER_LOGIN_ID = 810123;  // ERP 거래처 파트너아이디 주복검사
const CHECK_EXIST_PARTNER_ID = 810121;  // ERP 거래처 파트너아이디 주복검사

const NOT_ENOUGH_QUANTITIES = 901210; //재고부족

const EC_QUOTATION_STATUS_ASK = 81640; // 견적서 상태 중 요청
const EC_QUOTATION_STATUS_COMPLETE =  81642;   // 견적서 상태 중 완료 
const EC_QUOTATION_STATUS_EXPIRE =  81649;   // 견적서 상태 중 만료 
const EC_PAYMENT_TYPE_DEPOSIT = 81410;     // 결제수단 무통장 입금

const EC_BILL_TYPE_NO =  81510;   // 지출증빙 신청안함
const EC_BILL_TYPE_CASH =  81511;   // 지출증빙 현금영수증
const EC_BILL_TYPE_TAX =  81512;   // 지출증빙 세금계산서

const EC_BILL_CASH_TYPE_PERSONAL =  81942;   // 지출증빙 개인
const EC_BILL_CASH_TYPE_COMPANY =  81944;   // 지출증빙 사업자

const GET_ORDER_ADDRESS_RECENT = 870612;  // ERP 배송지 정보 요청 최근
const GET_ORDER_ADDRESS_COMPANY = 870712;  // ERP 배송지 정보 요청 사업자
const PUT_ORDER_CANCEL = 870285; //주문취소

const EC_DELIVERY_METHOD = 81631;  //ERP 배송방법 택배
const GET_ORDER_ADDRESS_INFO = 870012;      // API 배송지요청
const EC_ORDER_STATUS_SEND = 81115  // 주문상태 배송중
const EC_ORDER_STATUS_CANCEL =  81148;       // 주문상태 취소
const EC_ORDER_STATUS_CHANGE_RETURN = 81122  // 주문상태 교환 반송중
const EC_ORDER_STATUS_CHANGE_RESEND = 81123  // 주문상태 교환 재배송중
const EC_ORDER_STATUS_CANCEL_RETURN = 81132  // 주문상태 반품 반송중
const EC_RETURN_METHOD_DELIVERY = 81822;     // 반품방법 택배
const EC_RETURN_TYPE_CHANGE = 81812;         // 반품구분 교환

const EC_ORDER_STATUS_DELIVERY_COMPLETE = 81116;  // ERP 주문상태 배송완료
const EC_ORDER_STATUS_CHANGE_COMPLETE = 81129;    // ERP 주문상태 교환완료
const EC_ORDER_STATUS_COLLECT_COMPLETE = 81139;  // ERP 주문상태 반품회수완료

const EC_STOCK_ORDER_TYPE_SHOPPING =  82112;  // 발주구분 쇼핑몰      
const EC_STOCK_ORDER_TYPE =  82110;  // 발주구분 일반

const EC_BILL_RECIPIENT_TYPE_CEO = 81986; // 공급받는자 구분 사업자
const EC_BILL_RECIPIENT_TYPE_PERSONAL = 81987; // 공급받는자 구분 개인
const EC_BILL_RECIPIENT_TYPE_FOREIGNER = 81988; // 공급받는자 구분 외국인
const FORIGN_ID_VALUE =   9999999999999;
const BUYER_TYPE_CODE_CON  = 80222; // 매입처구분 위탁
const INFINIT_CNT = 9999999999;

const ERROR_EC_DELIVERY_VIOLATION = 911100;    //배송비 규정 위반