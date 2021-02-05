<?php
     echo "<div id='pageIndicator' style='display:flex ; flex:1 ; width:100%; align-items: center; justify-content: center; '>
                <div class='lds-ring'>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                </div> 
            </div>";
     if (ob_get_length()) {
         ob_end_flush();
         flush();
     }
     sleep(1);
?>