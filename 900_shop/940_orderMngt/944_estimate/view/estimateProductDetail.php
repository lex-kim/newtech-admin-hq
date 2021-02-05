
<!-- modal #notice-view -->  
<!-- <body> -->
<div class="modal-add" id="notice-view" role="dialog">
    <!-- <div class="modal-dialog"> -->
        <!-- <div class="modal-content"> -->
        <form method="post" name="view_form" id="view_form">
        <input type="hidden" class="form-control" id="REQ_MODE" name="REQ_MODE">
        <button type="button" class="btn btn-default" id="CLOSE_BTN" onclick="window.close()">닫기</button>
            <div class="goods-top">
                <div class="goods-left">
                    <div class="slider-wrap">
                    <div class="slider slider-for">
                        <div>
                            <?php if(empty($productInfo['imgURI'])):?>
                                <img src="/900_shop/940_orderMngt/944_estimate/img/product.jpg" alt=""/>
                            <?php else : ?>
                                <img src="<?php echo $productInfo['imgURI'] ?>" alt="">
                            <?php endif; ?> 
                        </div>
                        <?php if(!empty($productInfo['img1URI'])):?>
                            <div>
                                <img src="<?php echo $productInfo['img1URI'] ?>" alt="">
                            </div>
                        <?php endif; ?>
                        <?php if(!empty($productInfo['img2URI'])):?>
                            <div>
                                <img src="<?php echo $productInfo['img2URI'] ?>" alt="">
                            </div>
                        <?php endif; ?>
                        <?php if(!empty($productInfo['img3URI'])):?>
                            <div>
                                <img src="<?php echo $productInfo['img3URI'] ?>" alt="">
                            </div>
                        <?php endif; ?>
                        <?php if(!empty($productInfo['img4URI'])):?>
                            <div>
                                <img src="<?php echo $productInfo['img4URI'] ?>" alt="">
                            </div>
                        <?php endif; ?>
                        
                    </div>

                    <div class="slider slider-nav">
                        <div>
                            <?php if(empty($productInfo['imgURI'])):?>
                                <img src="/900_shop/940_orderMngt/944_estimate/img/product.jpg" alt=""/>
                            <?php else : ?>
                                <img src="<?php echo $productInfo['imgURI'] ?>" alt="">
                            <?php endif; ?> 
                        </div>
                        <?php if(!empty($productInfo['img1URI'])):?>
                            <div>
                                <img src="<?php echo $productInfo['img1URI'] ?>" alt="">
                            </div>
                        <?php endif; ?>
                        <?php if(!empty($productInfo['img2URI'])):?>
                            <div>
                                <img src="<?php echo $productInfo['img2URI'] ?>" alt="">
                            </div>
                        <?php endif; ?>
                        <?php if(!empty($productInfo['img3URI'])):?>
                            <div>
                                <img src="<?php echo $productInfo['img3URI'] ?>" alt="">
                            </div>
                        <?php endif; ?>
                        <?php if(!empty($productInfo['img4URI'])):?>
                            <div>
                                <img src="<?php echo $productInfo['img4URI'] ?>" alt="">
                            </div>
                        <?php endif; ?>
                       
                    </div>
                    </div>
                </div>


                <div class="goods-right">
                    <div><?php echo $productInfo['itemID'] ?> 
                    <?php if(!empty($productInfo['goodsCode'])):?>
                        | <?php echo $productInfo['goodsCode'] ?>
                    <?php endif; ?>
                    </div>
                    <div class="goods-top-name"><?php echo $productInfo['goodsName'] ?></div>
                    <div class="goods-top-price"><?php echo number_format($productInfo['salePrice']) ?><span>원</span></div>
                    <div class="goods-top-info">
                        <?php if(empty(trim($productInfo['minBuyCnt'])) && empty($productInfo['goodsSpec']) && empty($productInfo['unitPerBox'])):?>
                            &nbsp;
                        <?php else : ?>
                            <?php if(!empty($productInfo['minBuyCnt'])):?>
                                최소구매수량: <?php echo $productInfo['minBuyCnt'] ?>
                            <?php endif; ?>
                            <?php if(!empty($productInfo['goodsSpec'])):?>
                                <span>|</span> 규격용량: <?php echo $productInfo['goodsSpec'] ?>
                            <?php endif; ?>
                            <?php if(!empty($productInfo['unitPerBox'])):?>
                                <span>|</span> 포장단위: <?php echo $productInfo['unitPerBox'] ?>
                            <?php endif; ?>
                        <?php endif; ?> 
                    </div>
                    <div class="goods-top-info">
                        <?php if(empty(trim($productInfo['manufacturerName'])) && empty($productInfo['originName']) && empty($productInfo['brandName'])):?>
                            &nbsp;
                        <?php else : ?>
                        <table class="table table-bordered" style="width:90%">
                            <colgroup>
                                <col width="2%">
                                <col width="9%">
                            </colgroup>
                            <tbody>
                                <?php if(!empty($productInfo['manufacturerName'])):?>
                                <tr>
                                    <td class="text-center active">제조사</td>
                                    <td><?php echo $productInfo['manufacturerName'] ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if(!empty($productInfo['originName'])):?>
                                <tr>
                                    <td class="text-center active">원산지</td>
                                    <td><?php echo $productInfo['originName'] ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if(!empty($productInfo['brandName'])):?>
                                <tr>
                                    <td class="text-center active">브랜드</td>
                                    <td><?php echo $productInfo['brandName'] ?></td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <?php endif; ?> 
                    </div>
                </div>
            </div>

            <!-- modal 상단 -->
            <div class="modal-header modal-detail-header">
                <ul class="modal-menu-list">
                    <li class="modal-menu"><a href="#productInfo">상품정보</a></li>  
                    <li class="modal-menu"><a href="#deliveryInfo">배송정보</a></li>  
                    <li class="modal-menu"><a href="#returnInfo">반품정보</a></li> 
                </ul>
            </div>


            <!-- modal 내용 -->
            <div class="modal-body"> 
                <h4 class="modal-body-title" id="productInfo">상품정보</h4>  
                <div class="goods-content">
                    <?php if(empty(trim($productInfo['goodsInfo'])) && empty($productInfo['goodsInfoImg1URI']) && empty($productInfo['goodsInfoImg2URI'])):?>
                        정보없음
                    <?php else : ?>
                        <?php echo $productInfo['goodsInfo'] ?>
                        <?php if(!empty($productInfo['goodsInfoImg1URI'])):?>
                            <img src="<?php echo $productInfo['goodsInfoImg1URI'] ?> " alt="">
                        <?php endif; ?>
                        <?php if(!empty($productInfo['goodsInfoImg2URI'])):?>
                            <img src="<?php echo $productInfo['goodsInfoImg2URI'] ?> " alt="">
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <h4 class="modal-body-title" id="deliveryInfo">배송정보</h4>  
                <div class="goods-content">
                    <?php if(empty(trim($productInfo['deliveryInfo'])) && empty($productInfo['deliveryInfoImg1URI']) && empty($productInfo['deliveryInfoImg2URI'])):?>
                        정보없음
                    <?php else : ?>
                        <?php echo $productInfo['deliveryInfo'] ?>
                        <?php if(!empty($productInfo['deliveryInfoImg1URI'])):?>
                            <img src="<?php echo $productInfo['deliveryInfoImg1URI'] ?> " alt="">
                        <?php endif; ?>
                        <?php if(!empty($productInfo['deliveryInfoImg2URI'])):?>
                            <img src="<?php echo $productInfo['deliveryInfoImg2URI'] ?> " alt="">
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <h4 class="modal-body-title" id="returnInfo">반품정보</h4>  
                <div class="goods-content">
                    <?php if(empty(trim($productInfo['returnInfo'])) && empty($productInfo['returnInfoImg1URI']) && empty($productInfo['returnInfoImg2URI'])):?>
                        정보없음
                    <?php else : ?>
                        <?php echo $productInfo['returnInfo'] ?>
                        <?php if(!empty($productInfo['returnInfoImg1URI'])):?>
                            <img src="<?php echo $productInfo['returnInfoImg1URI'] ?> " alt="">
                        <?php endif; ?>
                        <?php if(!empty($productInfo['returnInfoImg2URI'])):?>
                            <img src="<?php echo $productInfo['returnInfoImg2URI'] ?> " alt="">
                        <?php endif; ?>
                    <?php endif; ?>
                    
                </div>
            </div>

            <!-- modal 하단 -->
            <!-- <div class="modal-footer">
                <div class="text-right">
                    <button type="button" class="btn btn-default" onclick="window.close()">닫기</button>
                </div>
            </div> -->

        </form>
        <!-- </div> -->
    <!-- </div> -->
</div>

</body>
<?php
  require $_SERVER['DOCUMENT_ROOT']."/include/footerpopup.html";
?>
<!-- slick slider -->
<script src="js/slick.min.js"></script>   
<link rel="stylesheet" type="text/css" href="css/slick.css"/>