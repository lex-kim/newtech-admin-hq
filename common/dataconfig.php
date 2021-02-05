<?php
/* 환경설정 파일 */
define("URL_ROOT", "https://".$_SERVER["HTTP_HOST"]);

define("ERROR_INVALID_APP_KEY",901010);
define("ERROR_DB_ACCESS_FAILURE",902102);
define("ERR_EXPIRED_TOKEN",991110);         // 만료된 토큰
define("ERR_NORIGHT_TOKEN",991120);         // 권한없는 토큰
define("TOKEN_OK", 990000);                 // 정상처리

define("ERR_NOTEXIST_USERID",905110);     // Not exist userid
define("ERR_WRONG_PASSWD",   905120);        // Wrong password

/* 파일업로드 관련 설정 */
define("ALLOW_FILE_EXT","GIF|JPG|JPEG|PNG|PDF|DOC|DOCX|XLS|XLSX|PPT|PPTX|HWP|ZIP");						//	업로드 가능 확장자
// define("FILE_UPLOAD_PATH","D:/Google Drive/Workspace/MYFIT/MYFIT-ADMIN/trunk/FILE_DATA/"); 	//	파일저장경로
define("FILE_UPLOAD_PATH","/service/newtech-hq/FILE_DATA/"); 	//	서버 파일저장경로

/* 리스트 카운트 설정 */
define("LIST_PER_PAGE",10);

/* PG 관련 설정 */
define("KICC_CP_KEY","05535129");   // KICC BATCH 출금 CP 키

/* 권역 옵션리스트 */
define("AREA_OPTION_LIST", "<option value=\"\">= 권&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;역 =</option>
                            <option value=\"11100\">서울(11100)</option>
                            <option value=\"11130\">인천(11130)</option>
                            <option value=\"11140\">대전(11140)</option>
                            <option value=\"11150\">광주(11150)</option>
                            <option value=\"11160\">대구(11160)</option>
                            <option value=\"11170\">울산(11170)</option>
                            <option value=\"11180\">부산(11180)</option>
                            <option value=\"11200\">경기(11200)</option>
                            <option value=\"11311\">충남(11311)</option>
                            <option value=\"11312\">충북(11312)</option>
                            <option value=\"11321\">전남(11321)</option>
                            <option value=\"11322\">전북(11322)</option>
                            <option value=\"11331\">경남(11331)</option>
                            <option value=\"11332\">경북(11332)</option>
                            <option value=\"11340\">강원(11340)</option>
                            <option value=\"11350\">제주(11350)</option>
                            <option value=\"11410\">경기 성남(11410)</option>
                            <option value=\"11420\">경기 수원(11420)</option>
                            <option value=\"11430\">경기 오산(11430)</option>
                            <option value=\"11440\">경기 용인(11440)</option>
                            <option value=\"11450\">경기 평택(11450)</option>
                            <option value=\"11460\">경기 화성(11460)</option>
                            <option value=\"11510\">충남 아산(11510)</option>
                            <option value=\"11520\">충남 천안(11520)</option>
                            <option value=\"11610\">경북 구미(11610)</option>");
/* 제휴기업 구분코드 옵션리스트 */
define("EE_OPTION_LIST", "<option value=\"\">= 제휴기업구분 =</option>
                            <option value=\"EP\">기업(EP)</option>
                            <option value=\"PU\">공기업(PU)</option>
                            <option value=\"GO\">관공서(GO)</option>
                            <option value=\"OT\">기타(OT)</option>");

/* 계약상태 구분코드 */
define("CD_STAT_OPTION_LIST", "<option value=\"\">= 계약상태 =</option>
                            <option value=\"43100\">준비(43100)</option>
                            <option value=\"43200\">정상(43200)</option>
                            <option value=\"43900\">종료(43900)</option>");

/* 멤버십 구분코드 */
define("MGKIND_OPTION_LIST", "<option value=\"\">= 멤버십 =</option>
                            <option value=\"S\">카페블렌드(S)</option>
                            <option value=\"G\">카페골드(G)</option>
                            <option value=\"O\">카페원스토어(O)</option>
                            <option value=\"T\">카페럭셔리(T)</option>");
/* 회원 구분코드 */
define("MEMKIND_OPTION_LIST", "<option value=\"\">= 회원구분 =</option>
                            <option value=\"EM\">임직원</option>
                            <option value=\"FB\">가족(배우자)</option>
                            <option value=\"FC\">가족(자녀)</option>
                            ");
                            // <option value=\"VP\">VIP회원</option>

/* 멤버십상태구분코드 */
define("MEM_STAT_CD_OPTION_LIST","<option value=\"\">= 멤버십상태 =</option>
                                  <option value=\"20100\">준회원(일반)</option>
                                  <option value=\"20120\">준회원(사용대기)</option>
                                  <option value=\"20140\">준회원(멤버십탈퇴)</option>
                                  <option value=\"20160\">준회원(멤버십강퇴)</option>
                                  <!--option value=\"20190\">준회원(탈퇴신청)</option-->
                                  <!--option value=\"20199\">준회원(탈퇴완료)</option-->
                                  <option value=\"20200\">사용(일반)</option>
                                  <option value=\"20220\">사용(일시정지대기)</option>
                                  <option value=\"20240\">사용(멤버십탈퇴대기)</option>
                                  <option value=\"20260\">사용(멤버십강퇴대기)</option>
                                  <option value=\"20420\">정지(일시정지)</option>
                                  <option value=\"20440\">정지(미납정지)</option>
                                  <option value=\"20460\">정지(강제정지)</option>");

/* 재직증빙 상태구분코드 */
define("CERT_STATUS_CD_OPTION_LIST", "<option value=\"\">= 재직증빙 =</option>
                            <option value=\"60100\">미제출(60100)</option>
                            <option value=\"60200\">제출(60200)</option>
                            <option value=\"60300\">확인(60300)</option>");
define("PREPAY_CD_OPTION_LIST",
          " <option value=\"\">=결제결과=</option>
            <option value=\"36320\">결제완료</option>
            <option value=\"36340\">입금대기</option>
            <option value=\"36360\">결제실패</option>
            <option value=\"36380\">결제취소</option>
      ");
/* 은행 리스트  */
// define("BANK_OPTION_LIST", "<option value=\"\">= 은행선택 =</option>
//                             <option value=\"004\">국민은행</option>
//                             <option value=\"039\">경남은행</option>
//                             <option value=\"034\">광주은행</option>
//                             <option value=\"003\">기업은행</option>
//                             <option value=\"011\">농협</option>
//                             <option value=\"031\">대구은행</option>
//                             <option value=\"055\">도이치은행</option>
//                             <option value=\"032\">부산은행</option>
//                             <option value=\"002\">산업은행</option>
//                             <option value=\"050\">상호저축은행</option>
//                             <option value=\"045\">새마을금고</option>
//                             <option value=\"007\">수협중앙회</option>
//                             <option value=\"048\">신협</option>
//                             <option value=\"088\">신한(조흥)은행</option>
//                             <option value=\"020\">우리은행</option>
//                             <option value=\"071\">우체국</option>
//                             <option value=\"037\">전북은행</option>
//                             <option value=\"035\">제주은행</option>
//                             <option value=\"027\">한국씨티은행(한미)</option>
//                             <option value=\"054\">HSBC은행</option>
//                             <option value=\"081\">KEB하나은행</option>
//                             <option value=\"023\">SC은행</option>");
define("BANK_OPTION_LIST", "<option value=\"\">= 은행선택 =</option>
                            <option value=\"001\">한국은행</option>
                            <option value=\"002\">산업은행</option>
                            <option value=\"003\">기업은행</option>
                            <option value=\"004\">국민은행</option>
                            <option value=\"007\">수협중앙회</option>
                            <option value=\"008\">수출입은행</option>
                            <option value=\"011\">농협은행</option>
                            <option value=\"012\">지역농축협</option>
                            <option value=\"020\">우리은행</option>
                            <option value=\"023\">SC제일은행</option>
                            <option value=\"027\">한국씨티은행</option>
                            <option value=\"031\">대구은행</option>
                            <option value=\"032\">부산은행</option>
                            <option value=\"034\">광주은행</option>
                            <option value=\"035\">제주은행</option>
                            <option value=\"037\">전북은행</option>
                            <option value=\"039\">경남은행</option>
                            <option value=\"045\">새마을금고중앙회</option>
                            <option value=\"048\">신협중앙회</option>
                            <option value=\"050\">상호저축은행</option>
                            <option value=\"052\">모건스탠리은행</option>
                            <option value=\"054\">HSBC은행</option>
                            <option value=\"055\">도이치은행</option>
                            <option value=\"057\">제이피모간체이스은행</option>
                            <option value=\"058\">미즈호은행</option>
                            <option value=\"059\">미쓰비시도쿄UFJ은행</option>
                            <option value=\"060\">BOA은행</option>
                            <option value=\"062\">중국공상은행</option>
                            <option value=\"064\">산림조합중앙회</option>
                            <option value=\"071\">우체국</option>
                            <option value=\"076\">신용보증기금</option>
                            <option value=\"077\">기술보증기금</option>
                            <option value=\"081\">KEB하나은행</option>
                            <option value=\"088\">신한(조흥)은행</option>
                            <option value=\"089\">케이뱅크</option>
                            <option value=\"090\">카카오뱅크</option>
                            <option value=\"209\">유안타증권</option>
                            <option value=\"218\">KB증권</option>
                            <option value=\"227\">KTB투자증권</option>
                            <option value=\"238\">미래에셋대우증권</option>
                            <option value=\"240\">삼성증권</option>
                            <option value=\"243\">한국투자증권</option>
                            <option value=\"247\">NH투자증권</option>
                            <option value=\"261\">교보증권</option>
                            <option value=\"262\">하이투자증권</option>
                            <option value=\"263\">현대차투자증권</option>
                            <option value=\"264\">키움증권</option>
                            <option value=\"265\">이베스트투자증권</option>
                            <option value=\"266\">SK증권</option>
                            <option value=\"267\">대신증권</option>
                            <option value=\"269\">한화투자증권</option>
                            <option value=\"270\">하나금융투자</option>
                            <option value=\"278\">신한금융투자</option>
                            <option value=\"279\">동부증권</option>
                            <option value=\"280\">유진투자증권</option>
                            <option value=\"287\">메리츠종합금융증권</option>
                            <option value=\"290\">부국증권</option>
                            <option value=\"291\">신영증권</option>
                            <option value=\"292\">케이프투자증권</option>
                            <option value=\"294\">펀드온라인코리아</option>

                            ");
?>
