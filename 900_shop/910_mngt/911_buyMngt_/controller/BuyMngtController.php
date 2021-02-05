<?php
    namespace App\Controllers;
    require_once "./model/BuyMngtModel.php";

    class BuyMngtController{

        public function __construct($page,$idx){
            if (!file_exists($_SERVER["DOCUMENT_ROOT"]."/900_shop/910_mngt/911_buyMngt/view/".$page.".php")) {
                echo "잘못된 경로 입니다.!".$page;
                exit();
    
            }
            $this->$page($idx);
        }

        function index($ARR){
            $list = array(
                "name"=>"rrr",
                "id"=>"1111"
            );
            include "view/index.php";
        }        
        
        function write($idx){
            if(!empty($idx)){
                $model = new \App\Models\BuyMngtModel();
                $partnerID = $idx;    
                $buyInfo = array();  
                $result = $model->getData($partnerID);
                print_r( $result );
                exit();

                if($result['status'] == '200'){
                    $buyInfo = $result['data'][0];
                }
                // print_r($idx );
                // exit();
            }
            include "view/write.php";
        }

    }
?>