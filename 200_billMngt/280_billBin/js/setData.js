printBill = (idx) => {
    let printHtml = '';

    const popupUrl = '../../_SLF.html?NB_ID='+listArray[idx].NB_ID+'&MGG_ID='+listArray[idx].MGG_ID;
    const data = {
        REQ_MODE: CASE_LIST_SUB
    };
    
    callAjax(popupUrl, 'GET', data).then((result)=>{
        printHtml += result;  

        const strFeature = "width=1000, height=1200, all=no";
        let objWin = window.open('', 'print', strFeature);
        objWin.document.write(printHtml);
        setTimeout(function() {
            objWin.focus();
            objWin.print();
            objWin.close();
        }, 250);
    });
    
}