<?php
    require_once('config.php');

    $params = file_get_contents('php://input');
    $array_data = (array) json_decode($params,true);

    $user_name = $array_data['user_name'];
    $head_portrait = $array_data['head_portrait'];
    // $nick_name = $array_data['nick_name'];

    $sql = "UPDATE user SET head_portrait='".$head_portrait."' WHERE user_name='".$user_name."'";

    $result = $conn->query($sql);

    // var_dump($result === TRUE);
    if($result === TRUE) {
        echo json_encode(['status'=>200,'data'=>'','msg'=>"修改成功: "]);
    } else {
        echo json_encode(['status'=>500,'data'=>'','msg'=> "Error: " . $sql . "<br>" . $conn->error]);die;
    }
?>