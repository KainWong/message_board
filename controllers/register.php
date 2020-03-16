<?php
    require_once('config.php');


    $params = file_get_contents('php://input');
    $array_data = (array) json_decode($params,true);
    $user_name = $array_data['user_name'];
    $nick_name = $array_data['nick_name'];
    $user_password = $array_data['password'];
    $head_portrait = $array_data['headPortrait'];

    
    if(!$user_name) {
        echo json_encode(['status'=>404,'data'=>'','msg'=>'请输入用户名']);
        exit;
    }
    if(!$nick_name) {
        echo json_encode(['status'=>404,'data'=>'','msg'=>'请输入昵称']);
        exit;
    }
    if(!$user_password) {
        echo json_encode(['status'=>404,'data'=>'','msg'=>'请输入密码']);
        exit;
    }

    $sql_is_registered = "SELECT COUNT(user.user_name) as all_num FROM user where user.user_name='".$user_name."'";
    $register_array_count = mysqli_query($conn, $sql_is_registered);
    $register_count = mysqli_fetch_assoc($register_array_count)['all_num'];

    if($register_count >0) {
        echo json_encode(['status'=>404,'data'=>'','msg'=>"用户名已注册"]);
        exit;
    }

    $sql = "INSERT INTO user (nick_name,user_name,password,head_portrait) VALUES ('".$nick_name."','".$user_name."','".$user_password."','".$head_portrait."')";

    if($conn->query($sql) === TRUE) {
        echo json_encode(['status'=>200,'data'=>'','msg'=>"注册成功！"]);
    } else {
        echo json_encode(['status'=>500,'data'=>'','msg'=>"Error:".$sql."br>".$conn->error]);die;
    }

    $conn->close();
?>