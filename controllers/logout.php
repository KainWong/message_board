<?php
    $data = file_get_contents('php://input');
    $array_data = (array) json_decode($data,true);

    if($array_data['user_name']) {
        session_start();
        unset($_SESSION['user_name']);
        unset($_SESSION['user_password']);
        echo json_encode(['status'=>200,'data'=>'','msg'=>"退出成功: "]);
    }
?>