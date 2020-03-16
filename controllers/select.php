<?php
    require_once('config.php');

    $params = file_get_contents('php://input');
     $array_data = (array) json_decode($params, true);
     $page = $array_data['page'];
     $first = ($page-1)*10;
     $last = $page*10;

    $sql = "SELECT comment,user,id,nick_name,timestamp FROM messageboard  order by id desc limit ".$first.',10';
    $result = $conn->query($sql);

    $sql_all = "SELECT COUNT(*) as all_num FROM messageboard";
    $result_all = mysqli_query( $conn, $sql_all );
    $result_all_count = mysqli_fetch_assoc( $result_all)['all_num'];
    if($result_all_count == 0) {
        echo json_encode(['status'=>200,'data'=>[],'msg'=>"查询成功: ",'total'=>(int)$result_all_count]);
    } else {
        if ($result) {
            while ($row = mysqli_fetch_array($result)) {
                $data[]=[
                    'user'=> $row["user"],
                    'comment'=>$row["comment"],
                    'id'=>$row["id"],
                    'nick_name'=>$row["nick_name"],
                    'timestamp'=>$row["timestamp"]
                ];
            }
            echo json_encode(['status'=>200,'data'=>$data,'msg'=>"查询成功: ",'total'=>(int)$result_all_count]);
        }  else {
            echo json_encode(['status'=>500,'data'=>'','msg'=> "Error: " . $sql . "<br>" . $conn->error]);die;
        }
    }
    $conn->close();

?>