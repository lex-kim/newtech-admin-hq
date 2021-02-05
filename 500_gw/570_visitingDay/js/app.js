excelDownload = () =>{
    location.href='visitingGwCsv.html';
}

upload = () => {
  if(uploadForm.UPLOAD_VITIT.value=="") {
    alert('업로드할 임원방문일 파일을 선택하세요.');
  } else {
    $("#uploadForm").attr("action", "act/visitingDay_act.php");
    $('#REQ_MODE').val(CASE_UPDATE);
    confirmSwalTwoButton(
      '파일을 업로드 하시겠습니까?',
      '확인',
      '취소',
      false,
      ()=>{
        uploadForm.submit();
      }
  );
  }
}