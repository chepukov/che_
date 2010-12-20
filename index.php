<?php
    if (isset($_GET['adm'])){
        include('views/admin.phtml'); 
    }else{
        include('views/index.phtml'); 
    }
    
?>
