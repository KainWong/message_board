<?php
    require_once('config.php');
    //$comment = $_POST["comment"];
     $data = file_get_contents('php://input');
     $array_data = (array) json_decode($data, true);


    $sql = "INSERT INTO MessageBoard (nick_name,user,comment,timestamp)
    VALUES ('".$array_data['nick_name']."','".$array_data['user_name']."','".$array_data['comment']."','".$array_data['timestamp']."')";
    
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['status'=>200,'data'=>'','msg'=>"新记录插入成功: "]);
    } else {
        echo json_encode(['status'=>500,'data'=>'','msg'=> "Error: " . $sql . "<br>" . $conn->error]);die;
    }
    // 创建数据库
    // $sql = "CREATE DATABASE myDB";
    // if ($conn->query($sql) === TRUE) {
    //     echo "数据库创建成功";
    // } else {
    //     echo "Error creating database: " . $conn->error;
    // }
    
    $conn->close();
?>