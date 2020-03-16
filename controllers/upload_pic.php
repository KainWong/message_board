<?php


    if(isset($_FILES['image'])){
        $errors= array();
        $file_name = $_FILES['image']['name'];
        $file_size = $_FILES['image']['size'];
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_type = $_FILES['image']['type'];
        $name_arr = explode('.',$_FILES['image']['name']);
        $file_ext=strtolower(end($name_arr));
        $extensions= array("jpeg","jpg","png");
        /* 规定可以上传的扩展名文件 */
        if(in_array($file_ext,$extensions)=== false){
                $errors[]="不允许扩展，请选择一个jpeg或png文件。";
        }
        /* 规定可以上传的文件大小 */
        if($file_size > 2097152) {
                $errors[]='文件大小必须不超过2 MB';
        }
        if(empty($errors)==true) {
            /* 把图片从临时文件夹内的文件移动到当前脚本所在的目录 */
            move_uploaded_file($file_tmp,"../statics/img/".$file_name);
            echo json_encode(['status'=>200,'data'=>'/statics/img/'.$file_name,'msg'=>'上传成功！']);
        }else{
            echo json_encode(['status'=>500,'data'=>'','msg'=> "Error: " . $sql . "<br>" .$errors]);die;
        }
    }

?>