<?php
    require_once('config.php');

    $data = file_get_contents('php://input');
    $array_data = (array) json_decode($data, true);

    $sql = "INSERT INTO mini_comment (parent_id,user_name,comment,nick_name,parent_user,reply_whom,timestamp) VALUES ('".$array_data['parent_id']."','".$array_data['user_name']."','".$array_data['comment']."','".$array_data['nick_name']."','".$array_data['parent_user']."','".$array_data['reply_whom']."','".$array_data['timestamp']."')";

    if($conn->query($sql) === TRUE) {
        echo json_encode(['status'=>200,'data'=>'','msg'=>"新记录插入成功: "]);
    } else {
        echo json_encode(['status'=>500,'data'=>'','msg'=> "Error: " . $sql . "<br>" . $conn->error]);
    }
?>