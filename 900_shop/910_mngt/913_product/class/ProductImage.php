<?php	

	class ProductImage{
        public $imgArr;
        public $returnImgArr;
        public $deliveryImgArr;
        public $goodsImgArr;
        public $catalogImgArray;

		public function __construct(){
            $this->imgArr = array("img","img1","img2","img3","img4");
			$this->goodsImgArr = array("goodsInfoImg1","goodsInfoImg2");
			$this->deliveryImgArr = array("deliveryInfoImg1","deliveryInfoImg2");
			$this->returnImgArr = array("returnInfoImg1","returnInfoImg2");
			$this->catalogImgArray = array("catalog");
        }

        function getImgArr(){
            return  $this->imgArr;
        }

        function getReturnImgArr(){
            return $this->returnImgArr;
        }

        function getDeliveryImgArr(){
            return $this->deliveryImgArr;
        }

        function getGoodsImgArr(){
            return $this->goodsImgArr;
        }   

        function getCatalogImgArr(){
            return $this->catalogImgArray;
        }

        

	}
?>
