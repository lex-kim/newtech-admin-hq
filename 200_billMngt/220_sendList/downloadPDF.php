<?

    error_reporting(E_ERROR | E_PARSE);

    // 파일 Path를 지정합니다.
    $FILE_PATH = $_SERVER['DOCUMENT_ROOT']."/faxSave/".$_REQUEST['NB_ID']."_".time();
    $saveFullSRC2 =  $FILE_PATH.".html";
    $fnSRC =  ($_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ? 'https' : 'http') . '://'.$_SERVER['HTTP_HOST']."/PDF_VIEW.html?MGG_ID=".$_REQUEST['MGG_ID']."&NB_ID=".$_REQUEST['NB_ID']."&TYPE=".$_REQUEST['TYPE'];

    $fw = fopen($saveFullSRC2,'w');
    $fr = fopen($fnSRC,'r');

    if(!$fr){
        echo ("Can't not Open");
    } else {
        while(!feof($fr)){
            fwrite($fw,fgets($fr,2048));
        }
        fclose($fr);
    }
    fclose($fw);

    require_once($_SERVER['DOCUMENT_ROOT'].'/html2pdf/html2pdf.class.php');
   
    ob_start();
    $outputString = file_get_contents($saveFullSRC2);
  

    try{
        $html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8', array(0, 0, 0, 0));
        // init HTML2PDF    
        $html2pdf->addFont('malgungothicbi', '',dirname('__FILE__').'html2pdf/_tcpdf_5.0.002/malgungothicbi.php');
   
        // display the full page
        $html2pdf->pdf->SetDisplayMode('fullpage');
   
        // $html2pdf->pdf->Image("https://nt-hq.nt34.net/images/sign.png",10,10,300);
        // convert
        $html2pdf->writeHTML($outputString);

        if($_REQUEST['TYPE'] == 'PDF'){   //PDF
            // send the PDF
            $html2pdf->Output(date("YmdH", time()).'_'.$_REQUEST['NB_ID'].'.pdf','D');
            unlink($saveFullSRC2);

        }else{ // JPEG
            $IMG_ARRAY = [];
            $html2pdf->Output($FILE_PATH .'.pdf','F');
            $im = new imagick($FILE_PATH .'.pdf');

            foreach($im as $index =>$image){     
                $img_name = $FILE_PATH ."_".$index.'.jpeg';
                $image->setImageResolution(72,72);
                $image->resampleImage(72,72,imagick::FILTER_UNDEFINED,1);
                // $image->setImageCompression(Imagick::COMPRESSION_JPEG);
                $image->setImageCompressionQuality(100);
                $image->setImageFormat('jpg');
                $image->stripImage();
                $image->writeImage($img_name);
                array_push($IMG_ARRAY, $img_name);      
            }
            $all = new Imagick();
            foreach($IMG_ARRAY as $index =>$file){     
                $im_ = new imagick($file);       
                $all->addImage($im_);      
            }
            $all->resetIterator();
            $combined = $all->appendImages(true);
            $combined->setImageFormat("jpeg");
            $combined->writeImage($FILE_PATH .'.jpeg');
            $im->clear();
            $im->destroy();

            header('Content-type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.date("YmdH", time()).'_'.$_REQUEST['NB_ID'].'.jpeg');
            header('Content-Transfer-Encoding: binary');
            header('Content-length: ' . filesize($FILE_PATH .'.jpeg'));
            header('Expires: 0');
            header("Pragma: public");
            
            $fp = fopen($FILE_PATH .'.jpeg', 'rb');
            fpassthru($fp);
            fclose($fp);

            unlink($FILE_PATH.'.pdf');
            unlink($FILE_PATH.'.jpeg');
            unlink($saveFullSRC2);
            foreach($IMG_ARRAY as $index =>$file){     
                unlink($file);     
            }
            
        }
        
    }catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
    }
?>