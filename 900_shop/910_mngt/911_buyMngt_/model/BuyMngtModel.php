<?php
    namespace App\Models;

    class BuyMngtModel{

        function getList($ARR){
            $list = array(
                array(
                    "idx"=>"182",
                    "dealCd"=>"consignment",
                    "dealID" => "1" ,
                    "deal" => "위탁" ,
                    "company" => "김제 신일공업사-ST",
                    "businessNum" => "113-83-002993",
                    "store_business"=>"업태",
                    "store_event"=>"종목",
                    "address" => "서울 서대문구 연희동 724-9",
                    "phoneNum" => "010-4447-1689",
                    "faxNum"=>"02-123-1234",
                    "ceoNm" => "김용규",
                    "ceoPhone"=>"010-1234-1234",
                    "faoNm" => "박용규",
                    "faoPhone" => "010-4448-2222",
                    "event"=>"종목",
                    "faoTel" => "02-123-1234" ,
                    "faoEmail" => "kim@naver.com" ,
                    "siteUrl" => "http://naver.com",
                    "goods" => "주요품목",
                    "memo" => "남긴 메모입니다.",
                    "dealStateCd"=>"normal",
                    "dealStatusID" => "22",
                    "dealState" => "일반",
                    "deal_DT"=>"20-03-12",
                    "dealBankNm"=>"우리은행",
                    "bankNum"=>"12-3212-3212",
                    "bankUserNm"=>"김용규",
                    "businessPaper" => "",
                    "gwPaper_YN"=>"N",
                    "bank"=>"",
                    "bank_YN"=>"Y",
                    "WRT_USERID"=>"관리자",
                    "WRT_DTHMS"=>"2017-04-12",
                    "paymentDT" => "5",
                    "businessFileName"=>"2012-203-201-203231"
                ),
                array(
                    "idx"=>"181",
                    "dealCd"=>"consignment",
                    "dealID" => "2" ,
                    "deal" => "사입" ,
                    "company" => "서울 땡구공업사-ST",
                    "businessNum" => "135-83-1111111",
                    "store_business"=>"업태",
                    "store_event"=>"종목",
                    "address" => "서울 영등포구 무슨동 11-9",
                    "phoneNum" => "010-6234-4888",
                    "faxNum"=>"02-123-1234",
                    "ceoNm" => "김장고",
                    "ceoPhone"=>"010-2222-3333",
                    "faoNm" => "장고고",
                    "faoPhone" => "010-4448-2222",
                    "event"=>"종목",
                    "faoTel" => "02-123-1234" ,
                    "faoEmail" => "kim@naver.com" ,
                    "siteUrl" => "http://naver.com",
                    "goods" => "주요품목",
                    "memo" => "",
                    "dealStateCd"=>"normal",
                    "dealStatusID" => "23",
                    "dealState" => "정지",
                    "deal_DT"=>"20-03-12",
                    "dealBankNm"=>"우리은행",
                    "bankNum"=>"12-3212-3212",
                    "bankUserNm"=>"김용규",
                    "businessPaper" => "",
                    "gwPaper_YN"=>"N",
                    "bank"=>"",
                    "bank_YN"=>"Y",
                    "WRT_USERID"=>"관리자",
                    "WRT_DTHMS"=>"2017-04-12",
                )
            );
            return $list;
        }
        
        function getData($ARR){
            $list = $this->getList($ARR);
            $data = array();
    
            if(!empty($list)){
                foreach($list as $idx => $item){
                    if($item['idx'] == $ARR){
                        array_push($data, $item);
                    }
                }
            }
            return $data;
        }

    }
    $cBuyMngt = new BuyMngtModel();
?>