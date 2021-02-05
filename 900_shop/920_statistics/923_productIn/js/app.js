let currPage = 1;
let beforeStartDT = "";
let beforeEndDT = "";

$(document).ready(function() {

  if($('#pageIndicator').is(":visible")){
    $('#pageIndicator').hide();
  }

  $('#categoryCode').selectpicker();
  $('.bs-select-all').detach();
  $(".bs-deselect-all").css("float", "right");
  $(".bs-deselect-all").width("78%");
  $(".bs-deselect-all").html("전체 선택해제");

  const tdLength = $('table th').length;
  setInitTableData(tdLength);

  $("#START_DT").datepicker(
    monthDatePickerOption("maxDate", "END_DT")
  ); 

  $("#END_DT").datepicker(
    monthDatePickerOption("minDate", "START_DT")
  ); 

  $('#START_DT').val(getBeforeDate(6));
  $('#END_DT').val(getCunrrentDate());
  
  beforeStartDT = $('#START_DT').val();
  beforeEndDT = $('#END_DT').val();

  $('#searchForm').submit(function(e){
    e.preventDefault();

    if(beforeStartDT != $('#START_DT').val() || beforeEndDT != $('#END_DT').val()){
      getHeaderData();
    }else{
      getList(1);
    }
    
  });
});

function monthDatePickerOption(optionType, objID){
  const options = {
    closeText: '선택',
    prevText: '이전달',
    nextText: '다음달',
    currentText: '오늘',
    dateFormat: 'yy-mm', 
    changeMonth: true, 
    changeYear: true, 
    showButtonPanel: true, 
    showOtherMonths: true ,
    showMonthAfterYear: true,
    monthNamesShort: ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'] ,
    yearSuffix: "년",
    beforeShow:function(e){
      const year = e.value.split("-")[0];
      const month = e.value.split("-")[1];

      const endYear = $('#'+objID).val().split("-")[0];
      const endMonth = $('#'+objID).val().split("-")[1];

      $(this).datepicker('option', 'defaultDate', new Date(year, month, 0));
      $(this).datepicker('setDate', new Date(year, month, 0));
      $(this).datepicker("option", optionType, new Date(endYear, endMonth-1, 1));
   },
   onClose: function(dateText, inst) { 
      $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1)); 
      $(".ui-datepicker-calendar").css("display","none"); 
   } 
  };

  return options;
}

function getCunrrentDate(){
  const date = new Date();
  const year = date.getFullYear();
  const month = ("0"+(date.getMonth()+1)).slice(-2);
  
  return year+"-"+month;
}

function getBeforeDate(bDate){
  const date = new Date();
	const monthOfYear = date.getMonth();
	date.setMonth(monthOfYear - bDate);

  const bYear = date.getFullYear();
  const bMonth = ("0"+(date.getMonth()+1)).slice(-2);

  return bYear+"-"+bMonth;
}

/** 빈값일 때 동적테이블처리 */
setHaveNoDataTable = () => {
  const tdLength = $('table th').length;
  const tableText = "<tr><td class='text-center' colspan='"+tdLength+"'>조회된 데이터가 존재하지 않습니다.</tr></tr>";
  $('.pagination').html('');
  $('#dataTable').html(tableText);
}

setTable = (dataList, totalListCount, cPage) => {
  let html = "";
  dataList.map((item, idx)=>{
    html += "<tr>";
    html += "<td class='text-center'>"+item.issueCount+"</td>";
    html += "<td class='text-center'>"+item.partnerID+"</td>";
    html += "<td class='text-center'>"+item.partnerName+"</td>";
    html += "<td class='text-center'>"+phoneFormat(item.phoneNum)+"</td>";
    html += "<td class='text-center'>"+phoneFormat(item.faxNum)+"</td>";
    html += "<td class='text-center'>"+(empty(item.chargeName) ? "-" : item.chargeName)+"</td>";
    html += "<td class='text-center'>"+phoneFormat(item.chargePhoneNum)+"</td>";
    html += "<td class='text-center'>"+item.categoryName+"</td>";
    html += "<td class='text-center'>"+item.goodsCode+"</td>";
    html += "<td class='text-center'>"+item.goodsName+"</td>";
   
    headerArray.map(function(headerItem){
      if(item['buyCount_'+headerItem.YYYYMM] == undefined){
        html += "<td class='text-center'>-</td>";
      }else{
        html += "<td class='text-center'>"+numberWithCommas(item['buyCount_'+headerItem.YYYYMM])+"</td>";
      }
      
      if(item['buyPrice_'+headerItem.YYYYMM] == undefined){
        html += "<td class='text-center'>-</td>";
      }else{
        html += "<td class='text-center'>"+numberWithCommas(item['buyPrice_'+headerItem.YYYYMM])+"</td>";
      }

      if(item['sellCount_'+headerItem.YYYYMM] == undefined){
        html += "<td class='text-center'>-</td>";
      }else{
        html += "<td class='text-center'>"+numberWithCommas(item['sellCount_'+headerItem.YYYYMM])+"</td>";
      }

      if(item['sellPrice_'+headerItem.YYYYMM] == undefined){
        html += "<td class='text-center'>-</td>";
      }else{
        html += "<td class='text-center'>"+numberWithCommas(item['sellPrice_'+headerItem.YYYYMM])+"</td>";
      }
    });
    html += "</tr>"
  });

  paging(totalListCount, cPage, 'getList'); 
  $('#dataTable').html(html);
  $('#dataTable').show();
}

getList = (page) => {
  let time = 1;
  currPage = (page == undefined ) ? currPage : page;
  let form = $("#searchForm")[0];
  let formData = new FormData(form);
  formData.append('REQ_MODE', CASE_LIST);
  formData.append('PER_PAGE', $("#perPage").val());
  formData.append('CURR_PAGE', currPage);

  $('#indicatorTableTD').attr('colspan',(headerArray.length*4)+10);
  
  let ajaxTimeInterver = setInterval(function(){
      time += 1;
      if(time > 2){
        $('#dataTable').hide();
        $('#indicator').show();
        $('#indicatorTable').show();

        clearInterval(ajaxTimeInterver);
      }
  },150);
 

  callFormAjax(ERP_CONTROLLER_URL, 'POST', formData).then((result)=>{
      if(isJson(result)){
        const res = JSON.parse(result);
        if(res.status == OK){
          if(res.data == HAVE_NO_DATA || res.data.length == 0){
            setHaveNoDataTable();
          }else{
            setTable(res.data, res.total_item_cnt, currPage);
          } 
        }else{
          alert((empty(res.message) ? ERROR_MSG_1100 : res.message));
        }
      }else{
          alert(ERROR_MSG_1200);
      }

      clearInterval(ajaxTimeInterver);
      if($('#indicatorTable').is(":visible")){
        $('#indicatorTable').hide();
      }
      $('#dataTable').show();
     
  });  
}

function setHeaderTable(){
  let html = "";
  html += '<tr>';
  html += '<th class="text-center" rowspan="2">순번</th>';
  html += '<th class="text-center" rowspan="2">매입처ID</th>';
  html += '<th class="text-center" rowspan="2">매입처</th>';
  html += '<th class="text-center" rowspan="2">사무실전화</th>';
  html += '<th class="text-center" rowspan="2">사무실팩스</th>';
  html += '<th class="text-center" rowspan="2">담당자</th>';
  html += '<th class="text-center" rowspan="2">담당자휴대폰</th>';
  html += '<th class="text-center" rowspan="2">카테고리</th>';
  html += '<th class="text-center" rowspan="2">상품코드</th>';
  html += '<th class="text-center" rowspan="2">상품명</th>';
  headerArray.map(function(item){
    const date = item.YYYYMM;
    // html += '<th class="text-center" colspan="4">'+(date.substr(0,4)+"-"+date.substr(4,2))+'</th>';
    html += '<th class="text-center" colspan="4">'+item.YYYYMM+'</th>';
  });
  html += '</tr>';
  html += '<tr class="active">';
  for(let i = 0 ; i<headerArray.length ; ++i){
    html += ' <th class="text-center">구매수량</th>';
    html += ' <th class="text-center">구매금액</th>';
    html += ' <th class="text-center">판매수량</th>';
    html += ' <th class="text-center">판매금액</th>';
  }
  html += '</tr>';

  $('#headerTable').html(html);
}

function getHeaderData(){
  const data = {
    REQ_MODE : CASE_READ,
    START_DT : $('#START_DT').val().replace(/-/g, ""),
    END_DT : $('#END_DT').val().replace(/-/g, "")
  };

  callAjax(ERP_CONTROLLER_URL, 'POST', data).then((result)=>{
    if(isJson(result)){
      const res = JSON.parse(result);
      if(res.status == OK){
        headerArray = res.data;
        beforeStartDT = $('#START_DT').val();
        beforeEndDT = $('#END_DT').val();

        setHeaderTable();
        getList(1);
      }else{
        alert((empty(res.message) ? ERROR_MSG_1100 : res.message));
      }
    }else{
        alert(ERROR_MSG_1200);
    }
  }); 
}

function onchangePartnerID(){
  if(empty($('#selectPartnerID').val())){
    $('#partnerID').val("");
  }  
}

function setAutoComplete(dataArr){
  try{
    const dataArray = JSON.parse(dataArr);
    $("#searchPartnerID").autocomplete({
        minLength : 1,
        source: dataArray.map((data, index) => {
            return {
                id : data.partnerID,
                value : data.partnerID
                // value : data.goodsName +" ["+data.itemID+"]"
            };
        }),
        change: function(event, ui){
          if(!empty(ui.item)){
            $('#partnerID').val(ui.item.id);
          }
          return false;
        },
        select: function(event, ui) {
            let strArea = ui.item.value;
            $("#searchPartnerID").val(strArea);
            $('#partnerID').val(ui.item.id);

            return false;
        }
    }).on('focus', function(){ 
        $(this).autocomplete("search"); 
    });
  }catch{
    alert("자동완성 설정에 실패하였습니다. 관라자에게 문의해주세요.")
  }  
}

function excelDownload(){
  location.href="productInExcel.php?"+$("#searchForm").serialize();
}
   