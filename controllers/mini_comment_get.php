<?php
    require_once('config.php');

    $data = file_get_contents('php://input');
    $array_data = (array) json_decode($data,true);
    $comment_id = (int)$array_data['comment_id'];
    $sql = "SELECT user_name,nick_name,comment,reply_whom,timestamp FROM mini_comment WHERE parent_id=".$comment_id." order by id desc";
    
    $result = $conn->query($sql);
    if($result==NULL) {
        echo json_encode(['status'=>200,'data'=>[],'msg'=>'查询成功!']);
    } else if($result) {
        while($row= mysqli_fetch_array($result)) {
            if($row['reply_whom'] != '') {
                $sql_name = "SELECT nick_name FROM user WHERE user_name='".$row['reply_whom']."'";
                $result_name = $conn->query($sql_name);
                $result_nickname = mysqli_fetch_array($result_name)[0];
            } else {
                $result_nickname = '';
            }
            
            $list[] = [
                'user_name'=>$row['user_name'],
                'nick_name'=>$row['nick_name'],
                'comment'=>$row['comment'],
                'reply_whom'=>$row['reply_whom'],
                'timestamp'=>$row['timestamp'],
                'reply_nick_name'=>$result_nickname
            ];
        }

        echo json_encode(['status'=>200,'data'=>$list,'msg'=>"查询成功: "]);
    } else {
        echo json_encode(['status'=>500,'data'=>'','msg'=>"Error:".$sql."<br>".$conn->error]);die;
    }

    $conn->close();
?>