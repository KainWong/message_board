<?php
    require_once('config.php');

    $params = file_get_contents('php://input');
    $array_data = (array) json_decode($params,true);
    $user_name = $array_data['user_name'];
    $user_password = $array_data['password'];

    if(!$user_name) {
        echo json_encode(['status'=>404,'data'=>'','msg'=>'请输入用户名']);
        exit;
    }
    if(!$user_password) {
        echo json_encode(['status'=>404,'data'=>'','msg'=>'请输入密码']);
        exit;
    }

    $sql = "SELECT user_name,password,nick_name,head_portrait FROM user where user_name = '".$user_name."'";

    $result = $conn->query($sql);
    $row = mysqli_fetch_array($result);
    if($row) {
        $data[]=[
            'user_name'=> $row["user_name"],
            'password'=>$row["password"],
            'nick_name'=>$row["nick_name"],
            'head_portrait'=>$row["head_portrait"]
        ];
        $real_password = $data[0]['password'];
        $nick_name = $data[0]['nick_name'];
        $head_portrait = $data[0]['head_portrait'];
        if($user_password !== $real_password) {
            echo json_encode(['status'=>404,'data'=>'','msg'=> "密码或用户不匹配"]);die;
        } else {
            session_start();
            // 存储 session 数据
            $_SESSION['user_name']= $user_name;
            $_SESSION['user_password']= $user_password;
            echo json_encode(['status'=>200,'data'=>['user_name'=>$user_name,'password'=>$user_password,'nick_name'=>$nick_name,'head_portrait'=>$head_portrait],'msg'=> "登陆成功"]);die;
        }
    } else if($row === NULL) {
        echo json_encode(['status'=>404,'data'=>'','msg'=> "没有此用户"]);die;
    } else {
        echo json_encode(['status'=>500,'data'=>'','msg'=> "Error: " . $sql . "<br>" . $conn->error]);die;
    }
?>