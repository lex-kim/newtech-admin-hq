<!-- <script src="https://cdn.jsdelivr.net/npm/lazyload@2.0.0-rc.2/lazyload.js"></script> -->
<script src="/js/lazyload.js"></script>

<!-- 상품메뉴 -->
<div class="order-menu">
  <ul class="nav navbar-nav">
    <?php
      
      foreach($categoryList as $index => $item){
          if(empty($item['parentCategoryID']) && $item['childCategoryCount'] > 0){
    ?>
      <li class="dropdown">
        <!-- <a class="dropdown-toggle" data-toggle="dropdown" href="#" onclick="getList(null,'<?php echo $item['thisCategoryID'] ?>')"> -->
        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
          <?php echo $item['thisCategory'] ?><span class="caret"></span>
        </a>
        <ul class="dropdown-menu">
          <?php 
            foreach($categoryList as $subIdx => $subItem){
              if($subItem['parentCategoryID'] == $item['thisCategoryID']){
                
          ?>
            <li><a href="javascript:getList(1,'<?php echo $subItem['thisCategoryID'] ?>')"><?php echo $subItem['thisCategory'] ?></a></li>
          <?php }} ?> 
        </ul>
      </li>
    <?php
          }
      }
    ?>
  </ul>
</div>

<!-- 상품검색 -->
<div class="order-search">
    <?php	require $_SERVER['DOCUMENT_ROOT']."/include/listCnt.html"; ?>

  <div>
    <input type="text" class="search-input" id="searchText" placeholder="상품명을 입력해주세요.">
    <button type="button" class="btn btn-primary search-btn" onclick="onSearch()">검색</button>
  </div>
</div>

<script>
    function onSearch(search){
      getList(null, null, $('#searchText').val());
    }
</script>
