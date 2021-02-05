<?php
    require_once $_SERVER['DOCUMENT_ROOT']."/common/class/ServerApiClass.php";

    class Options extends ServerApiClass{
        public $optionArray;
        
        public function __construct(){
            $this->optionArray = array();
            if(func_num_args() > 0){
                $this->getOptions(func_get_arg(func_num_args()-1));
            }
        }

        function getOptions($optionArr){
			foreach($optionArr as $key => $value){
                $data = array(
                    "accessToken"=>$_SESSION['accessToken'],
                    "requestMode"=>GET_CODE_DOMAIN,
                    "hqID"=>HQ_ID,
                    "userID"=>$_SESSION['USERID'],
                    "codeDomain"=>$value
                );
                
                $result = $this->get(EC_CODE, $data);
				if($result['status'] == OK){
                    $this->optionArray += array(
                        $value=>$result['data']
                    );
				}
            }

            if(count($optionArr) != count($this->optionArray)){
                echo "검색조건을 가져오는데 실패하였습니다. 다시 시도해주세요.";
                exit();
            }
        }
    }
?>