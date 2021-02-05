<?php	
	require_once $_SERVER['DOCUMENT_ROOT']."/common/class/ServerApiClass.php";

	class CommonApi extends ServerApiClass {

        /** 직원리스트 */
        function getUserList(){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=>GET_USER_LIST ,
				"hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],
				"userName"=>"",
				"fullListYN"=>"Y"
				
			);

            $result =  $this->get("/ec/User", $data);	
            
            if($result['status'] == OK){
				return $result['data']['data'];
			}else{
				echo "직원 정보를 가져오는데 실패하였습니다.";
				exit();
			}
        }
        
        /** 공업사 리스트 */
		function getGarageList($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=>GET_GARAGE_LIST ,
				"hqID"=>HQ_ID,
                "userID"=>$_SESSION['USERID'],
                "garageName"=>"",
				"pageNum"=>$ARR['CURR_PAGE'],
				"pageItemCount"=>$ARR['PER_PAGE'],
				"fullListYN"=>(empty($ARR['fullListYN']) ? "N" : "Y"),
				"startDate"=>$ARR['START_DT'],
				"endDate"=>$ARR['END_DT'],
				"dateField"=>$ARR['dateField'],
				"memo"=>$ARR['SEARCH_MEMO'],
				"categoryName"=>$ARR['SEARCH_CATEGORY'],
				"keyfield"=>$ARR['keyfield'],
				"keyword"=>$ARR['keyword'],
				
            );

            $result =  $this->get("/ec/Garage", $data);	
            
            if($result['status'] == OK){
				return $result['data']['data'];
			}else{
				echo "공업사 정보를 가져오는데 실패하였습니다.";
				exit();
			}
        }
        
        /** 지역리스트 */
        function getRegionList(){
			$data = array(
                "regionName"=>"",
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=>GET_REGION_LIST ,
                "hqID"=>HQ_ID,
                "userID"=>$_SESSION['USERID'],	
            );

			return $this->get("/ec/Code", $data);	
        }
        
        /** 영업구분 맇스트 */
        function getDivisionList(){
			$data = array(
                "divisionName"=>"",
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=>GET_DIVISION_LIST    ,
                "hqID"=>HQ_ID,
                "userID"=>$_SESSION['USERID'],	
            );

            $result =  $this->get("/ec/Code", $data);	

            if($result['status'] == OK){
				return $result['data'];
			}else{
				echo "영업구분 정보를 가져오는데 실패하였습니다.";
				exit();
			}
		}

		/** 해당공업사 정보 */
		function getGargeInfo($ARR){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=>GET_GARAGE    ,
                "hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],	
				"garageID"=>$ARR['garageID'],	
				
            );

            $result =  $this->get("/ec/Garage", $data);	

            return $result;
		}

		/** 포인트 정책 */
		function getPointInfo($garageID){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=>GET_POINT_INFO    ,
                "hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID'],	
				"garageID"=>$garageID,	
            );

            $result =  $this->get("/ec/Point/Info", $data);	

            if($result['status'] == OK){
				return $result['data'];
			}else{
				echo "포인트 정보를 가져오는데 실패하였습니다.";
				exit();
			}
		}

		function getLogsticsInfo(){
			$data = array(
				"accessToken"=>$_SESSION['accessToken'],
				"requestMode"=>GET_ORDER_LOGISTICS_LIST    ,
                "hqID"=>HQ_ID,
				"userID"=>$_SESSION['USERID']
            );

            $result =  $this->get("/ec/Order/Logistics", $data);	
            if($result['status'] == OK){
				return $result['data'];
			}else{
				echo "택배사 정보를 가져오는데 실패하였습니다.";
				exit();
			}
		}

		
	}

	// 0. 객체 생성
	$cCommonApi= new CommonApi()
?>
