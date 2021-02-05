const registPopupWidth = 500;            // 팝업 width 값
let popupX = 0;
let popupY = 0;
$(document).ready(function() {


    /*로그인 팝업창 X,Y설정 */
    popupX = (screen.availWidth - registPopupWidth) / 13;
    if( window.screenLeft < 0){
        popupX += window.screen.width*-1;
    }
    else if ( window.screenLeft > window.screen.width ){
        popupX += window.screen.width;
    }
    popupY= (window.screen.height / 2.5) - (400 / 2);




    $("#userid").focus();

    $("#REQ_MODE").val(LOGIN);
    remCookie();

    /* 복사하기  */
    $("#btnCopy").on("click", function(){
        let data = 'nt34@daum.net';
        $("#btnCopy").data("copy", true);
        $("#btnCopy").data("val", data);
        document.execCommand('copy'); 
        alert('복사되었습니다');
    });
    
    window.addEventListener('paste', function (e){
        let data = e.clipboardData.getData('text/plain');
        
     });

     $("body").bind('copy', function (e) {
        if($("#btnCopy").data("copy") === undefined){
            return;
        }
        $("#btnCopy").removeData("copy");
        let selection = window.getSelection();
        // body 돔에 div 엘리먼트를 생성하고 그 안에 pre 태그를 사용해서 html 형식으로 밀어 넣는다.
        // 복사할 임시 엘리먼트는 화면상에 보이면 안되기 때문에 absolute형식으로 좌로 -99999로 넣는다.
        // 참고: display:none을 하면 복사가 되지 않습니다.
        let body_element = document.getElementsByTagName('body')[0];
        let newdiv = document.createElement('div');
        newdiv.style.position = 'absolute';
        newdiv.style.left = '-99999px';
        body_element.appendChild(newdiv);
        // data를 넣는다.
        let data = $('#btnCopy').data('val');
        newdiv.innerHTML = data;
        $("#btnCopy").removeData('val');
        // 생성한 div를 셀렉트한다.
        selection.selectAllChildren(newdiv);
        // 약간의 딜레이가 필요하기 때문에 setTimeout으로 제어한다.
        window.setTimeout(function () {
            body_element.removeChild(newdiv);
        }, 1);
    });

       
    getNotice();

});


saveLogin = () => {
    if($("#checker").is(":checked")){
        confirmSwalTwoButton(
            '저장하시겠습니까?','확인', '취소', false ,
            ()=>{$('#checker').prop("checked", true)}, '비밀번호를 저장할 경우 정보 누출의 위험이 있습니다.'
        );
    }

    
}
/**
 * 쿠키 저장하기
 * @param {string} name 쿠키이름
 * @param {string} value 쿠키값
 * @param {int} exdays 쿠키저장 일수
 * 
 */
setCookie = (name,value,exdays) => {
    if(exdays == undefined ){
        exdays = 365;  
    }
    let exdate = new Date();
    exdate.setDate(exdate.getDate() + exdays);
    let cookieValue = escape(value) + ((exdays==null) ? "" : "; expires=" + exdate.toGMTString());

    document.cookie = name+"="+cookieValue; 
}
    
/**
 * 쿠키값 불러오기
 * @param {string} name 쿠키이름
 *  */  
readCookie = (name) => {
    name = name + '=';
    var cookieData = document.cookie;
    var start = cookieData.indexOf(name);
    var cookieValue = '';
    if(start != -1){
        start += name.length;
        var end = cookieData.indexOf(';', start);
        if(end == -1)end = cookieData.length;
        cookieValue = cookieData.substring(start, end);
    }
    return unescape(cookieValue);

}

/**
 * 쿠키 삭제하기
 * @param {string} name 쿠키이름
 *  */  
deleteCookie = (name) => {
    let expireDate = new Date();
    expireDate.setDate(expireDate.getDate() - 1); //어제날짜를 쿠키 소멸날짜로 설정
    document.cookie = name + "= " + "; expires=" + expireDate.toGMTString();

}

/* 아이디/ 비밀번호 쿠키 저장하기*/
toMem = () => {
    if($("#checker").is(":checked")){   
        setCookie('theName', $('#userid').val());    
        setCookie('thePasswd', $('#passwd').val());  
    }else{
        deleteCookie("theName");
        deleteCookie("thePasswd");
    }

} 

/* 쿠키값 넣어주기 */
remCookie = () => {
    $('#userid').val(readCookie("theName"));
    $('#passwd').val(readCookie("thePasswd"));
    if(readCookie("theName") != '' && readCookie("thePasswd") != ''){
        $('#checker').prop("checked", true)
    }
   
}

/* 공지사항 조회 */
function getNotice(){
    var param = {
        REQ_MODE: TABLE_TYPE_CD_NOTICE
    }
    callAjax('act/login_act.php','GET', param).then(function(result){
        var data = JSON.parse(result);
        console.log('getNotice >>',data);
        if(data.status == OK && !empty(data.data)){
            window.open('./loginPopup.html', 
                    'loginNoticePopup', 
                    'width='+registPopupWidth+',height=400, left='+ popupX + ', top='+ popupY+',toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no'); 
        }
    });
}