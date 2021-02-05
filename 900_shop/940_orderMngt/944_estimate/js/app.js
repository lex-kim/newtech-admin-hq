
function showPopup(){
    window.open('/900_shop/940_orderMngt/944_estimate/estimateProductDetail.html',  'goodsdetail', 'width=800,height=700,scrollbars=yes');
}

$(document).ready(function(){

    $('.slider-for').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        fade: true,
        asNavFor: '.slider-nav'
    });
    $('.slider-nav').slick({
        slidesToShow: 5,
        slidesToScroll: 1,
        asNavFor: '.slider-for',
        centerMode: true,
        focusOnSelect: true,
        dots: false,
        arrows: false,
    });

})