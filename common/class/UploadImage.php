<?php
    class UploadImage{
        public $serverPath;    // 디비에 저장될 url로 링크 클릭시 바로 이미지 볼수 있게.
        public $uploadPath;    // 실제 디비 경로(절대경로)
        public $fileName;      // 저장될 파일이름
        public $oriFileName;   // 원래 파일이름
        public $fileInfo;      // 파일정보를 가지고 있는 array
        private $DIR_PATH;     // 폴더경로 (upload/year/month/day)

        /**
         * 생성자
         * @param{$files :  업로드할 파일 정보}
         */
        public function UploadImage($files){
            $dateFolder = date('Y')."/".date('m')."/".date('d');
            $this->DIR_PATH = UPLOAD_URI."/".$dateFolder;
            $this->fileInfo = $files;
            $this->oriFileName = $files['name'];
            $this->fileName = uniqid($_SESSION['USERID']."_");
            $file = $this->fileName.".".end(explode('.', $this->oriFileName));
            $this->uploadPath = $this->DIR_PATH ."/".$file;
            $this->serverPath = SERVER_URI."/upload/".$dateFolder."/".$file;

            $this->setData(); // 업로드
        }
        
        /**
         * 업로드한 파일 정보 얻기
         * @param{$pathKey : 서버에 보낼 서버 경로 키값(string)}
         * @param{$fileNmKey : 서버에 보낼 저장된 파일 이름 키값(string)}
         * @param{$oriFileNmKey : 서버에 보낼 원래 파일 이름 키값(string)}
         */
        function getData($pathKey, $fileNmKey, $oriFileNmKey){
            return array(
                $pathKey=>$this->serverPath ,
                $fileNmKey=>$this->fileName ,
                $oriFileNmKey=>$this->oriFileName ,
            );
        }

        /**
         * $DIR_PATH에 폴더가 존재하는지 확인 후 생성.
         */
        function isExistDir(){
            if(!is_dir($this->DIR_PATH)){
                umask(0); 
                if(!mkdir($this->DIR_PATH  , 0777, true)){
                    die();
                }
            }

            return true;
        }

        /**
         * 이미지 업로드
         */
        function setData(){
            if(!$this->isExistDir()){
                die();
            }

            if(!move_uploaded_file($this->fileInfo['tmp_name'], $this->uploadPath)){
                die();
            }
        }

        /**
         * 업로드한 파일 삭제
         * @param{$fileUrl : 삭제할 업로드 파일 경로}
         * @param{$pathKey : 서버에 보낼 서버 경로 키값(string)}
         * @param{$fileNmKey : 서버에 보낼 저장된 파일 이름 키값(string)}
         * @param{$oriFileNmKey : 서버에 보낼 원래 파일 이름 키값(string)}
         */
        function delData($fileUrl, $pathKey, $fileNmKey, $oriFileNmKey){
            $filePath =  str_replace(SERVER_URI , $_SERVER['DOCUMENT_ROOT'], $fileUrl);
            if(file_exists($filePath)){
                if(!unlink($filePath)){
                    die();
                }
            }
            
            return array(
                $pathKey=>"",
                $fileNmKey=>"" ,
                $oriFileNmKey=>"" ,
            );
        }

        /**
         * 파일 업로드를 위한 배열 정리
         * @param{$filePath : 업로드 파일 경로}
         * @param{$fileNM : 파일 저장된 이름}
         * @param{$fileOriNM : 파일 원래 이름}
         * @param{$pathKey : 서버에 보낼 서버 경로 키값(string)}
         * @param{$fileNmKey : 서버에 보낼 저장된 파일 이름 키값(string)}
         * @param{$oriFileNmKey : 서버에 보낼 원래 파일 이름 키값(string)}
         */
        function setImageValue($filePath, $fileNM, $fileOriNM,  $pathKey, $fileNmKey, $oriFileNmKey){
            if(empty($fileNM)){
                $data = UploadImage::delData(
                            $filePath,
                            $pathKey, 
                            $fileNmKey, 
                            $oriFileNmKey
                        );
            }else{
                $data = array(
                    $pathKey=>$filePath,
                    $fileNmKey=>$fileNM,
                    $oriFileNmKey=>$fileOriNM,
                );
                
            }

            return $data;
        }
    }
?>