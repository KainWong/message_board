<?php
        $servername_config = "127.0.0.1";
        $username_config = "root";
        $password_config = "root";
        $dbname_config = "mydb";
    
        $conn = new mysqli($servername_config, $username_config, $password_config, $dbname_config);
    
        if($conn->connect_error) {
            die('连接失败：'.$conn->connect_error);
        }
?>