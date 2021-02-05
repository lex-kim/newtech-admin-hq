<?php
     class ServerApiClass extends DBManager{

        /**
         * 토큰 재발행
         * @param{$data : 권한없는 토큰이 없다는 이슈 전에 가지고 있떤 데이터}
         * @param{$type : post 인지 get인지}
         */
        function getToken($url, $data, $type){
            $params = http_build_query(
                array(
                    "applicationKey"=>APP_KEY,
                    "requestMode"=>GET_EC_ACCESS_TOKEN,
                    "hqID"=>HQ_ID,
                    "userID"=>$_SESSION['USERID']
                )
            );
            
            $ch=curl_init();
            curl_setopt($ch, CURLOPT_URL, ERP_API_URI."/ec/Auth/?".$params);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = json_decode(curl_exec($ch),true);

            if($result['status'] == OK){
                $apiType = $type."";
                $data['accessToken'] = $result['data']['accessToken'];
                $_SESSION['accessToken'] = $result['data']['accessToken'];
                return $this->{$apiType }($url, $data);
            }else{
                $_SESSION['USERID']		=	"";
                session_unregister("USERID");
                
                echo "<script>alert('토큰 재발행에 실패하였습니다. 다시 로그인 주세요.');if(opener != null){opener.location.replace('/');window.close();}else{location.replace('/')};</script>";
                exit();
            }
        }

        /**
         * curl post 방식
         * @param{$urlID : api url}
         * @param{$data : 파라메터}
         */
        function post($urlID, $data, $FILES){
            $ch=curl_init();
            curl_setopt($ch, CURLOPT_URL, ERP_API_URI.$urlID."/index.php");
            curl_setopt($ch, CURLOPT_POST, true);
            if(!empty($data)){
                $params = (empty($FILES) ? http_build_query($data) : $data);
                curl_setopt ($ch, CURLOPT_POSTFIELDS, $params);
            }

            if(!empty($FILES)){
                curl_setopt($ch,CURLOPT_HTTPHEADER, array("Content-Type:multipart/form-data"));
            }
            
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
           
            $response = json_decode(curl_exec($ch), true);
            curl_close($ch);
            
            if($response['status'] == ERROR && $response['data'] == ERR_NORIGHT_TOKEN){
                return $this->getToken($urlID, $param, "post");
            }else{
                return $response;
            }
        }
        
        /**
         * curl get 방식
         * @param{$urlID : api url}
         * @param{$data : 파라메터}
         */
        function get($urlID, $param){  // api와 통신하기 위한 함수
            $ch=curl_init();
            curl_setopt($ch, CURLOPT_URL, ERP_API_URI.$urlID."/?".http_build_query($param));
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = json_decode(curl_exec($ch),true);
            curl_close($ch);

            if($response['status'] == ERROR && $response['data'] == ERR_NORIGHT_TOKEN){
                return $this->getToken($urlID, $param, "get");
            }else{
                return $response;
            }  
        } 

        function get_($urlID, $param){  // api와 통신하기 위한 함수
            $ch=curl_init();
            curl_setopt($ch, CURLOPT_URL, ERP_API_URI.$urlID."/?".http_build_query($param));
            echo ERP_API_URI.$urlID."/?".http_build_query($param);
            exit();
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = json_decode(curl_exec($ch),true);
            curl_close($ch);

            if($response['status'] == ERROR && $response['data'] == ERR_NORIGHT_TOKEN){
                return $this->getToken($urlID, $param, "get");
            }else{
                return $response;
            }  
        } 
        
    }
?>