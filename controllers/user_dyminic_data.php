<?php
    require_once('config.php');

    $params = file_get_contents('php://input');
    $array_data = (array) json_decode($params,true);
    $page = $array_data['user_name'];

    $sql = "SELECT comment,user,nick_name,timestamp FROM messageboard WHERE user='".$array_data['user_name']."';";
    $result = $conn->query($sql);

    if($result) {
        while($row = mysqli_fetch_array($result)) {
            
            $data[]=[
                'user_name'=>$row['user'],
                'comment'=>$row['comment'],
                'nick_name'=>$row['nick_name'],
                'timestamp'=>$row['timestamp']
            ];
        }
        
    } else {
        $data = [];
    }

    $sql_mini = "SELECT user_name,comment,nick_name,timestamp,parent_user FROM mini_comment WHERE user_name='".$array_data['user_name']."'";
    $result_mini = $conn->query($sql_mini);

    if($result_mini) {
        while($row_mini = mysqli_fetch_array($result_mini)) {
            $parent_user = $row_mini['parent_user'];
            $sql_parent = "SELECT nick_name FROM user WHERE user_name='".$parent_user."'";
            $parent_result = $conn->query($sql_parent);
            $parent_nick_name = mysqli_fetch_array($parent_result)[0];
            $data_mini[] = [
                'user_name'=>$row_mini['user_name'],
                'comment'=>$row_mini['comment'],
                'nick_name'=>$row_mini['nick_name'],
                'timestamp'=>$row_mini['timestamp'],
                'parent_nick_name'=>$parent_nick_name
            ];
        }
    } else {
        $data_mini = [];
    }
    
    if(empty($data)){
        $data = [];
        
    }

    if(empty($data_mini)) {
        $data_mini = [];
    }

    $sum_array = array_merge($data,$data_mini);

    $timeKey =  array_column( $sum_array, 'timestamp');

    array_multisort($timeKey, SORT_DESC, $sum_array);

    echo json_encode(['status'=>200,'data'=>$sum_array,'msg'=>'查询成功']);die;
     
    echo json_encode(['status'=>500,'data'=>'','msg'=> "Error: " . $sql . "<br>" . $conn->error]);die;
    $conn->close();
?>