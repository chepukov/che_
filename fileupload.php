<?php


    $fileName = urldecode($_FILES['filedata']['name']);
    $tmpName  = $_FILES['filedata']['tmp_name'];
    $fileSize = $_FILES['filedata']['size'];
    $fileType = $_FILES['filedata']['type'];
    $fp      = fopen($tmpName, 'r');
    $content = fread($fp, filesize($tmpName));
    $content = addslashes($content);
    fclose($fp);
    if(!get_magic_quotes_gpc()){
        $fileName = addslashes($fileName);
    }
    // $query = "INSERT INTO yourdatabasetable (`name`, `size`, `type`, `file`) VALUES ('".$fileName."','".$fileSize."', '".$fileType."', '".$content."')";
    // mysql_query($query); 

    echo '{"success":true}';
?>