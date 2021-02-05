setTableHeaderInfo = () => {
    const data = {
        REQ_MODE: GET_HEADER_CNT,
      }
      callAjax(CAPST_URL, 'GET', data).then((result)=>{
          if(isJson(result)){
              const data = JSON.parse(result);
              if(data.status == OK){
                 setTableHeader(data.data);
              }else{
                  alert(ERROR_MSG_1100);
              }
          }else{
              alert(ERROR_MSG_1200);
          }
      });
}