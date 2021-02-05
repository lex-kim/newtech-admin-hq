
//scroll시 header 높이 변경 
$(function(){
    $(window).scroll(function(){
        if($(this).scrollTop() > 100){
            $('header').addClass('hScrolling');
            $('#SHOP_HEADER').removeClass('hScrolling')
        }else{
            $('header').removeClass('hScrolling');
        }
    }
);
});


/* 로그아웃 컨펌 */
function confirmLogout() {
    confirmSwalTwoButton('로그아웃 하시겠습니까?','확인', '취소', false ,()=>{
        location.href='/000_login/act/login_act.php?REQ_MODE='+LOGOUT;
    });
}