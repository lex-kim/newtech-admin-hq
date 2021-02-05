<?php
/* ----------------- 기본코드 -------------------------------------------*/
define("OK",200);
define("ALREADY_EXISTS",208);
define("ERROR",400);
define("NOT_EXIST",404);
define("CONFLICT",409);
define("SERVER_ERROR",500);
define("HAVE_NO_DATA",99999);
define("YN_Y","Y");
define("YN_N","N");
define("NOT_CRR_3","00000");   // 지역 구 까지만 검색하겠다는 상수
define("NOT_CRR_2","000");   // 지역 구 까지만 검색하겠다는 상수
define("STOCK_USE","12130");   // 지역 구 까지만 검색하겠다는 상수
define("NOT_AUTH_READ_TEXT","조회할 수 없는 권한을 가진 페이지 입니다.");   
define("NSRSN_SEPARATOR","/::/");  
define("LOGIN",1000); //로그인
define("LOGOUT",1001); //로그아웃


define("TAB_TOTAL",2000);           //전체
define("TAB_ORDER",2100);           //주문
define("TAB_SEND",2200);            //배송
define("TAB_COMPLETE",2300);        //구매확정
define("TAB_CANCEL",2400);          //교환&취소



define("BASE_DATE_TEXT", "0000-00-00"); //기본날짜
define("LMS",90101); //발송타입 LMS
define("FAX",90104); //발송타입 FAX

/** ERP API CODE */
define("EC_PARTNER_INFO", "/ec/Partner/Info");  // ERP 거래처
define("EC_AUTH", "/ec/Auth");  // ERP 접속인증
define("EC_CODE", "/ec/Code");  // ERP 코드도메인
define("HQ_ID", "newtech");  // ERP 접속인증

define("BUYER_TYPE_CODE_CON", 80222);  // ERP 매입처구분 위탁

/** 현황통계 */
define("GET_STATISTICS_GOODS_HEADER", 899217);  // ERP 제품별 매입매출 헤더리스트
define("GET_STATISTICS_GOODS_LIST", 899210);  // ERP 제품별 매입매출 리스트조회
define("GET_STATISTICS_STOCK_ORDER_LIST", 899110);  // ERP 제품별 미결제리스트 조회
define("GET_STATISTICS_STOCK_ORDER_DETAIL_LIST", 899111);  // ERP 제품별 미결제 상세 리스트 조회
define("GET_STATISTICS_ORDER_LIST", 899120);  // ERP 제품별 미수금액현황 리스트 조회
define("GET_STATISTICS_ORDER_DETAIL_LIST", 899121);  // ERP 제품별 미수금액상세현황 리스트 조회
define("GET_POIN_STATUS_LIST", 880140);  // ERP 포인트현황
define("GET_POINT_EXPIRE_LIST", 880130);  // ERP 포인트소멸예정포인트
            

/** 입출고현황 */
define("GET_STOCK_STATUS_IO_LIST", 830510);          // ERP 입출고현황 리스트 조회
define("GET_STOCK_STATUS_EMPLOYEE_LIST", 830610);    // ERP 입출고현황 직원별 수불현황 리스트
define("GET_STOCK_STATUS_REGION_LIST", 830710);    // ERP 입출고현황 지역별 수불현황 리스트
define("GET_STOCK_STATUS_EMPLOYEE_HEADER", 830611);    // ERP 입출고현황 직원별 헤더리스트
define("GET_STOCK_STATUS_REGION_HEADER", 830711);    // ERP 입출고현황 지역별 헤더리스트
            

/** 업체관리 */
define("GET_GARAGE_INFO_LIST", 803210);  // ERP 업체관리 리스트 조회
define("GET_GARAGE_INFO", 803212);  // ERP 업체관리 조회
define("PUT_GARAGE_INFO", 803225);  // ERP 업체관리 수정
define("POST_GARAGE_INFO", 803224);  // ERP 업체관리 등록
define("CHECK_EXIST_GARAGE_LOGIN_ID", 803223);  // ERP 업체관리 아이디 중복검사
define("PUT_GARAGE_GRANT", 803232);  // ERP 업체관리 승인처리
define("DEL_GARAGE_INFO", 803229);  // ERP 업체관리 삭제
            
        
/** API Request Parmeter */
define("APP_KEY", "dc3836f1b15b45907358d623dae3244b");  // 앱키
define("GET_USER_LIST",802110);  // ERP 직원리스트요청 
define("GET_EC_ACCESS_TOKEN", 801110);  // ERP 토큰인증 요청
define("PUT_PARTNER_INFO",810125);   // ERP 거래처 관리 저장
define("POST_PARTNER_INFO",810124);  // ERP 거래처 관리 수정
define("GET_CODE_DOMAIN",800100);  // ERP 코드도메인
define("GET_PARTNER_INFO_LIST",810120);  // ERP 거래처리스트조회
define("GET_PARTNER_INFO",810122);  // ERP 거래처조회
define("DEL_PARTNER_INFO",810129);  // ERP 거래처삭제
define("GET_PARTNER_LEVEL_LIST",810110);  // ERP 거래처등급조회
define("CHECK_EXIST_PARTNER_ID",810121);  // ERP 거래처 파트너아이디 중복검사
define("GET_POINT_INFO",880112);  // ERP 포인트 잔액 및 정책
define("GET_ORDER_ADDRESS_RECENT",870612);  // ERP 배송지 정보 요청 최근
define("GET_ORDER_ADDRESS_COMPANY",870712);  // ERP 배송지 정보 요청 사업자
define("GET_ORDER_ADDRESS_INFO",870012);  // ERP 배송지 정보 요청 api

define("GET_GOODS_CATEGORY_LIST",820110);  // ERP 상품 카테고리 리스트조회
define("POST_GOODS_CATEGORY",820114);  // ERP 상품 카테고리 등록
define("PUT_GOODS_CATEGORY",820115);  // ERP 상품 카테고리 수정
define("GET_GOODS_CATEGORY",820112);  // ERP 상품 카테고리조회
define("CHECK_EXIST_CATEGORY_ID",820111);  // ERP 상품 카테고리아이디 중복검사
define("DEL_GOODS_CATEGORY",820119);  // ERP 상품 카테고리 삭제    
        
define("GET_SHOP_COMPANY_INFO",890112);  // ERP 코드관리 쇼핑몰 사업자 정보 조회
define("PUT_SHOP_COMPANY_INFO",890115);  // ERP 코드관리 쇼핑몰 사업자 수정/등록 
     
define("GET_SHOP_ENV_INFO",890122);  // ERP 코드관리 설정 조회
define("PUT_SHOP_ENV_INFO",890125);  // ERP 코드관리 설정 갱신
   
define("GET_GOODS_DELIVERY_LIST",820130);  // ERP 배송정책 리스트 조회
define("POST_GOODS_DELIVERY",820134);  // ERP 배송정책 생성
define("PUT_GOODS_DELIVERY",820135);  // ERP 배송정책 수정   
define("GET_GOODS_DELIVERY",820132);  // ERP 배송정책 조회
define("DEL_GOODS_DELIVERY",820139);  // ERP 배송정책 조회
define("EC_DELIVERY_FEE_TYPE_FIX",81624);  // ERP 고정 
define("EC_DELIVERY_FEE_TYPE_NUM",81620);  // ERP 수량 
define("EC_DELIVERY_FEE_TYPE_PRRICE",81622);  // ERP 금액 
define("EC_DELIVERY_FEE_FREE_PAY",81614);  // ERP 무료배송 
define("EC_DELIVERY_FEE_TYPE_INTEGRATED","xhdgkq1234");  // ERP 통합 
define("EC_PAYMENT_TYPE_DEPOSIT",81410);  // ERP 결제수단 무통장 
define("EC_PAYMENT_TYPE_CREDIT",81420);  // ERP 결제수단 신용카드 
define("EC_PAYMENT_TYPE_PHONE",81430);  // ERP 결제수단 휴대폰 
define("EC_PAYMENT_TYPE_TRANSFER",81440);  // ERP 결제수단 실시간이체 

define("EC_BILL_TYPE_NO",81510);  // ERP 지출증빙 신청안함 
define("EC_BILL_TYPE_CASH",81511);  // ERP 지출증빙 현금영수증 
define("EC_BILL_TYPE_TAX",81512);  // ERP 지출증빙 세금계산서
define("PUT_SALES_NO_BILL",860235);  // ERP 지출증빙 세금계산서
        

define("EC_BILL_CASH_TYPE_PERSONAL",81942);  // ERP 출증빙 개인
define("EC_BILL_CASH_TYPE_COMPANY",81944);  // ERP 지출증빙 사업자 
define("GET_ORDER_LOGISTICS_LIST",870810);  // ERP 택배사정보리스ㅡ트
define("PUT_ORDER_DEPOSIT",870225);  // ERP 주문관리 입금상태변경
define("PUT_ORDER_STATUS",870215);  // ERP 주문상태변경
define("PUT_ORDER_REFUND",870265);  // ERP 주문상태 환불정보 변경
define("PUT_ORDER_PG",870305);  // ERP PG결과 처리 요청 

define("PUT_ORDER_DELIVERY",870235);  // ERP 주문관리 배송정보 변경  
define("PUT_ORDER_RESEND",870255);  // ERP 주문관리 재배송정보 변경 요청 
define("PUT_ORDER_RETURN",870245);  // ERP 주문관리 반품정보 변경 요청 
define("PUT_ORDER_MEMO",870275);  // ERP 주문관리 메모 작성 요청 
define("PUT_ORDER_PRINT_STATEMENT",870295);  // ERP 거래명세서 출력
        

define("EC_ORDER_STATUS_END",81119);  // ERP 주문상태 구매확정
define("EC_ORDER_STATUS_REFUND_ING",81132);  // ERP 주문상태 반품 반송중
define("EC_ORDER_STATUS_CHANGE_ING",81122);  // ERP 주문상태 교환 반송중
define("EC_ORDER_STATUS_CHANGE_COMPLETE",81129);  // ERP 주문상태 교환완료
define("EC_ORDER_STATUS_RESEND",81123);  // ERP 주문상태 재배송중
define("EC_ORDER_STATUS_CANCEL",81148);  // ERP 주문상태 주문취소
define("EC_ORDER_STATUS_COLLECT_COMPLETE",81139);  // ERP 주문상태 반품회수완료
define("EC_ORDER_STATUS_CHANGE_RECEIPT",81121);  // ERP 주문상태 교환접수
define("EC_ORDER_STATUS_CANCEL_RECEIPT",81131);  // ERP 주문상태 반품접수
define("EC_ORDER_STATUS_DELIVERY_ING",81115);  // ERP 주문상태 배송중
define("EC_ORDER_STATUS_DELIVERY_COMPLETE",81116);  // ERP 주문상태 배송완료
define("EC_ORDER_STATUS_ITEM_READY",81114);  // ERP 주문상태 상품준비
define("EC_ORDER_STATUS_DEPOSIT_WAIT",81112);  // ERP 주문상태 입금대기
define("EC_ORDER_STATUS_ORDER_COMPLETE",81111);  // ERP 주문상태 주문완료
define("EC_ORDER_STATUS_PAY_COMPLETE",81113);  // ERP 주문상태 결제완료
define("EC_ORDER_STATUS_PAY_CANCEL",81147);  // ERP 주문상태 결제취소
define("EC_ORDER_STATUS_REFUND_RECEIPT",81141);  // ERP 주문상태 환불접수
define("EC_ORDER_STATUS_REFUND_COMPLETE",81149);  // ERP 주문상태 환불완료
define("PUT_ORDER_CANCEL",870285);  // ERP 주문상태 주문취소
define("GET_ORDER_INFO_LIST",870110);  // ERP 주문관리 리스트
    
define("POST_ORDER_PAYINFO",870415);  // ERP 주문관리 결제정보 전송 요청

define("POST_GOODS_ITEM",820124);  // ERP 상품관리 등록 
define("PUT_GOODS_ITEM",820125);  // ERP 상품관리 수정 
define("CHECK_EXIST_ITEM_ID",820121);  // ERP 상품아이디 중복검사
define("GET_GOODS_ITEM_LIST",820120);  // ERP 상품리스트 조회
define("GET_GOODS_ITEM",820122);  // ERP 상품 조회      
define("DEL_GOODS_ITEM",820129);  // ERP 상품 삭제
define("GET_RECENT_DELIVERY_INFO",820142);  // ERP 최근 배송 정보 조회    
define("GET_RECENT_RETURN_INFO",820152);  // ERP 최근 반품 정보 조회
        
define("GET_STOCK_IN_LIST",830110);  // ERP 입고관리리스트조회
define("POST_STOCK_IN",830114);  // ERP 입고관리 생성(입고처리)
define("PUT_STOCK_IN",830115);  // ERP 입고관리 수정(취소처리) 
define("DEL_STOCK_IN",830119);  // ERP 입고관리 삭제       
     
define("GET_STOCK_OUT_LIST",830210);  // ERP 출고관리 리스트조회 
define("POST_STOCK_OUT",830214);  // ERP 출고관리 생성 
define("PUT_STOCK_OUT",830215);  // ERP 출고관리 생성 
define("DEL_STOCK_OUT",830219);  // ERP 출고관리 삭제      
        
  
define("GET_STOCK_DISPOSAL_LIST",830310);  // ERP 분실/폐기 리스트 조회
define("POST_STOCK_DISPOSAL",830314);  // ERP 분실/폐기 등록
define("PUT_STOCK_DISPOSAL",830315);  // ERP 분실/폐기 취소
define("DEL_STOCK_DISPOSAL",830319);  // ERP 분실/폐기 삭제         
    
define("GET_STOCK_RETURN_LIST",830410);  // ERP 회수리스트 조회 
define("POST_STOCK_RETURN",830414);  // ERP 회수내역 생성              
define("PUT_STOCK_RETURN",830415);  // ERP 회수내역 취소            
define("DEL_STOCK_RETURN",830419);  // ERP 회수내역 삭제            
        
define("GET_DELIVERY_ENV_INFO",890132);  // ERP 통합배송비정보 조회   
define("PUT_DELIVERY_ENV_INFO",890135);  // ERP 통합배송비정보 갱신   
      
define("GET_QUOTATION_LIST",850110);  // ERP 견적서 리스트조회 
define("GET_QUOTATION",850112);  // ERP 견적서 조회 
define("POST_QUOTATION",850114);  // ERP 견적서 생성 
define("PUT_QUOTATION",850115);  // ERP 견적서 수정 - 요청->확정발행 
define("DEL_QUOTATION",850119);  // ERP 견적서 삭제 
   
define("CHECK_EXIST_PARTNER_LOGIN_ID",810123);  // ERP 거래처 파트너아이디 주복검사

define("EC_QUOTATION_STATUS_ASK",81640);  // 견적서 상태 코드 중 요청
define("EC_QUOTATION_STATUS_COMPLETE",81642);  // 견적서 상태 코드 중 완료
define("EC_QUOTATION_STATUS_EXPIRE",81649);  // 견적서 상태 코드 중 만료
define("GET_GARAGE",830112);  // 공업사정보요청

define("POST_ORDER_INFO",870114);  // ERP 주문서 작성    
define("GET_ORDER_INFO",870112);  // ERP 주문서 조회    

define("POST_STOCK_ORDER", 840114);  // ERP 발주작성
define("GET_STOCK_ORDER_LIST", 840110);  // ERP 발주리스트 조회
define("GET_STOCK_ORDER", 840112);  // ERP 발주 조회
define("GET_STOCK_ORDER_DELIVERY", 840232);  // ERP 배송비 조회
define("PUT_STOCK_ORDER_DEPOSIT", 840265);  // ERP 발주관리 입금정보 변경 요청 
define("PUT_STOCK_ORDER_DELIVERY", 840235);  // ERP 발주관리 배송정보 변경 요청 
define("PUT_STOCK_ORDER_IN", 840215);  // ERP 발주관리 입고처리 
define("PUT_STOCK_IN_EXCEL", 830113);  // ERP 입고관리 엑셀업로드
define("PUT_ORDER_DELIVERY_BATCH", 870313);  // ERP 주문관리 엑셀업로드
define("PUT_STOCK_ORDER_IN_MULTI", 840245);  // ERP 뱔주관리 일괄입고
        
        
define("POST_SALES_INFO", 860114);  // ERP 일반판매 생성
define("PUT_SALES_INFO", 860115);  // ERP 일반판매 생성
define("GET_SALES_INFO_LIST", 860110);  // ERP 일반판매 리스트 조회
define("DEL_SALES_INFO", 860119);  // ERP 일반판매 삭제
define("GET_SALES_STATUS_LIST", 860120);  // ERP 일반판매 현황
        
define("GET_SALES_INFO", 860112);  // ERP 지출증빙 조회
   
define("PUT_SALES_CASH_BILL", 860215);  // ERP 현금영수증 발행
define("DEL_SALES_CASH_BILL", 860219);  // ERP 현금영수증 취소
define("PUT_SALES_TAX_BILL", 860225);  // ERP 세금계산서 발행
define("DEL_SALES_TAX_BILL", 860229);  // ERP 세금계산서 취소
define("PUT_ORDER_EXPENDITURE", 870515);  // ERP 주문관리 지출증빙 발행
define("DEL_ORDER_EXPENDITURE", 870519);  // ERP 주문관리 지출증빙 취소
define("PUT_STOCK_ORDER_TRANSFER", 840515);  // ERP 발주관리 문자밠송  
define("PUT_QUOTATION_TRANSFER", 850117);  // ERP 견적서관리 문자밠송  
        
define("EC_DELIVERY_METHOD",81631);  // ERP 배송방법 택배  
 
 /** 코드도메인 */ 
define("EC_PARTNER_TYPE_BUY",80212);  // 매입처
define("GET_GARAGE_LIST",830110);  // 공업사 리스트 요청
define("GET_REGION_LIST",800210);  // 지역 구분 리스트 요청
define("GET_DIVISION_LIST",800220);  // 영업 구분 리스트 요청
define("EC_BUYER_TYPE_CONSIGN",80222);  // 위탁
define("EC_STOCK_ORDER_TYPE_SHOPPING", 82112);  // 발주구분 쇼핑몰      
define("EC_STOCK_ORDER_TYPE", 82110);  // 발주구분 일반

        
        


/* SWITCH CONST */
define("CASE_CREATE",1110);  // 등록, 저장
define("CASE_READ",1120);  // 데이터 조회
define("CASE_UPDATE", 1130);  // 데이터 수정
define("CASE_UPDATE_TEAM",1131); // 보험사팀정보 수정요청 수락
define("CASE_UPDATE_REGION",1132); // 보험사팀 담당지역 수정요청 수락
define("CASE_DELETE",1140);  // 데이터 삭제
define("CASE_LIST",1150);  // 메인 리스트 조회
define("CASE_LIST_SUB",1151);  // 하위 리스트 조회
define("CASE_LIST_DETAIL",1152);  // 상세 리스트 조회
define("CASE_TOTAL_LIST",1160);  // 전체 리스트 조회
define("CASE_DELETE_FILE",1170);  // 첨부파일 삭제
define("CASE_DOWNLOAD_FILE",1180);  // 첨부파일 다운로드
define("CASE_CONFLICT_CHECK",1190);  // 중복

/* CODE_DIC CONST */
define("GET_AUTHORITY",20140);  // 권한코드 조회
define("GET_PARTS_TYPE",20150);  // 부품타입 조회
define("GET_PARTS_INFO",20155); // 부품구분
define("GET_CAR_TYPE",20160);  // 차량타입 조회
define("GET_REF_DATA",52000);  // 해당레퍼런스 data 받기
define("GET_REF_CLAIM_TYPE",51140); 
 
/* OPTIONS */
define("OPTIONS_DIVISION",1210); // 구분 옵션 조회
define("OPTIONS_AREA",1211);  // 지역 자동완성 
define("OPTIONS_IW",1212);  // 보험사옵션 리스트
define("OPTIONS_GW",1213);  // 공업사옵션 리스트  
define("OPTIONS_CRR",1214);  // 지역 자동완성(아이디포함)
define("OPTIONS_CPPS",1215);  // 취급부품 조회
define("OPTIONS_YEAR", 1216);  // 해당년 조회
define("OPTIONS_MONTH", 1217);  // 해당월 조회
define("OPTIONS_BILL_YEAR", 1218);  // 청구 해당년 조회(가장 최신과 가장 처음)
define("GET_OPTIONS", 1219);  // 동적으로 세팅 될 옵션

/* 정산 */
define("STOCK_BILL","BILL");  // 청구서재고
define("STOCK_INDE","INDEPENDENT");   //독립
define("STOCK_SUBST","SUBSTITUTION"); //대체
define("GET_STOCK_TYPE",1400); //입출고 구분(출거/수거/사용) 
define("PART", "PART"); // 공급부품
define("PART_DEFAULT", "PART_DEFAULT"); // 사용부품

/* GW */
define("GET_GARGE_PARTS_INFO",20180);  // 해당공업사 사용부품정보
define("GET_GARGE_INFO",20170);  // 공업사관리 청구식관리

/* REF */
define("GET_REF_OPTION",51001);  // 공업사관리 청구식관리
define("GET_REF_EXP_TYPE",51002);  // 공업사관리 청구식관리
define("GET_REF_SCALE_TYPE",51003);  // 공업사관리 규모
define("GET_REF_REACTION_TYPE",51004);  // 공업사관리 규모
define("GET_REF_AOS_TYPE",51005);  // 공업사관리 AOS관리
define("GET_REF_STOP_REASON_TYPE",51006);  // 공업사관리 거래중단사유
define("GET_REF_CONTRACT_TYPE",51007);  // 공업사관리 거래여부
define("GET_REF_MAIN_TARGET_TYPE",51008);  // 공업사관리 주요정비
define("GET_REF_PAY_EXP_TYPE",51009);  // 공업사관리 지급식관리
define("GET_REF_NMI_INSU_TYPE",51010);  //공업사관리 지급가능보험사
define("GET_REF_OWN_TYPE",51011); //공업사관리 자가여부 
define("GET_REF_INSU_CHAGE_TYPE",51012); //공업사관리 담당여부 
define("GET_REF_RECKON_TYPE",51013); //교환/판금
define("GET_REF_WORK_PARIORITY_TYPE",51014); //작업항목관리 우선순위(부위)
define("GET_REF_WORK_PART",51015); //작업항목관리
define("GET_REF_CLAIMANT_TYPE",51016); //등록주체
define("REF_CENTER_CLAIMANT",51017);  // 등록주체 중 본사
define("REF_DEPOSITEE_TYPE",51018);  // 입금여부구분
define("REF_FAX_STATUS",51019);  // 팩스전송상태
define("REF_DEPOSIT_TYPE",51020);  // 입금여부상세
define("REF_EXCLUDE_REASON",51021);  // 지급제외사유
define("REF_DECREMENT_REASON",51022);  // 감액사유
define("REF_COOP_REACTION",51023); // 협조반응
define("REF_COOP_TYPE",51024); // 협조구분
define("REF_CONSIGN_STATUS",51025); // 위탁장비상태
define("REF_CONSIGN_SPOT",51026); // 위탁장비위치
define("REF_POINT_STATUS",51027); // 포인트 상태
define("REF_POINT_TYPE",51028); // 포인트종류
define("GET_REF_CAR_TYPE",51030);
define("GET_REF_SALES_FILE" ,51040);  // 첨부파일

/* 작업구분 */
define("WORK_PART_INSIDE_TYPE", 30330);       //실내
define("WORK_PART_LOWER_TYPE", 30335);        //하체
define("RACON_CHANGE_CD", 11210);             //교환
define("RACON_METAL_CD", 11220);             //판금
define("RACON_CHANGE_NM", "교환");             //교환
define("RACON_METAL_NM", "판금");             //판금
define("RLT_MY_CAR_TYPE ", "11410");            //자차
define("RLT_PI_TYPE ", "11420");               //대물
define("RLT_BOTH_TYPE ", "11430");             //쌍방
define("RLT_PART_TYPE ", "11440");             // 일부

/* IW */
define("GET_INSU_TYPE",1300);  // 보험사분류 조회
define("GET_INSU_TEAM",1301);  // 보험사팀 조회
define("GET_HEADER_CNT",1302);  // 테이블 동적 생성을 위한 CNT 조회
define("GET_GARAGE_CLAIM_INFO",1500);  // 공업사 청구담당자 관리 정보
define("GET_INSU_CHARGE_INFO",1600);  // 해당보험사 보상담당자
define("GET_REQUEST_REGION",1216); // 보험사팀 담당지역  수정요청 데이터
define("INSU_TYPE_NORMAL", 11110);     //보험타입-일반
define("INSU_TYPE_COOPER", 11111);    //보험타입-공제
define("INSU_TYPE_RENT", 11112);      //보험타입-렌트

/* 주체 구분 */
define("MODE_TARGET_ALL", 10101);       // 전체
define("MODE_TARGET_GW", 10102);       // 공업사
define("MODE_TARGET_IW", 10103);       // 보험사
define("MODE_TARGET_SA", 10104);       // 영업사원
define("MODE_TXT_HQ", "본");       // 전체
define("MODE_TXT_GW", "공");       // 공업사
define("MODE_TXT_IW", "보");       // 보험사
define("MODE_TXT_SA", "영");       // 영업사원

define("REF_FAX_NOT_SEND", 90210);    // 팩스 미전송 상태 
define("REF_FAX_WAIT_SEND", 90220);   // 팩스 젆송대기 상태
define("REF_FAX_SUCC_SEND", 90230);   // 팩스 전송 성공
define("REF_FAX_FAIL_SEND", 90290);   // 팩스 전송 실패

/* 게시판 구분*/
define("TABLE_TYPE_CD_NOTICE",	20110);         // 공지사항
define("TABLE_TYPE_CD_BOARD",	20120);         // 사내게시판

/* ----------------- 에러코드 -------------------------------------------*/
define("ERROR_NOT_ALLOWED_FILE",901100);
define("ERROR_DB_INSERT_DATA",902100);
define("ERROR_DB_INSERT_ITEM_DATA",902101);
define("ERROR_DB_UPDATE_DATA",902120);
define("ERROR_DB_DELETE_DATA",902140);
define("ERROR_DB_DELETE_ITEM_DATA",902141);
define("ERROR_DB_SELECT_DATA",902200);
define("ERROR_DB_GET_MAX_NUM",902300);

define("ERROR_MSG_NO_DATA",'조회된 데이터가 없습니다.');
define("NOT_SELECT_FILE_TEXT",'선택된 파일 없음');
if (!defined('SERVER_URI')) define("SERVER_URI" , getProtocol().$_SERVER['HTTP_HOST']);   
if (!defined('ERP_API_URI')) define("ERP_API_URI" ,($_SERVER['HTTP_HOST'] == "nt-hq.nt34.net" ? "https://api.nt34.net" : "http://nt-api.dgmit.net:4500"));
if (!defined('UPLOAD_URI')) define("UPLOAD_URI" , $_SERVER['DOCUMENT_ROOT']."/upload");
?>
