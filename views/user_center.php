<?php session_start();?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>留言板</title>
    <script src="../statics/js/axios.min.js"></script>
    <script src="../statics/js/jquery-1.11.2.js"></script>
    <script src="../statics/js/vue.min.js"></script>
    <script src="../statics/elementUI/index.js"></script>
    <script src="../statics/js/cookie.js"></script>
    <link rel="stylesheet" href="../statics/elementUI/index.css">
</head>
<style>
    [v-clock] {
        display:none;
    }

    body {
        margin:0;
        width: 100%;
    }

    #app {
        width:1080px;
        margin:0 auto;
    }

    .avatar-uploader {
        display:flex;
        justify-content:center;
        margin:30px 0;
    }
    .avatar-uploader .el-upload {
        border: 1px dashed #d9d9d9;
        border-radius: 6px;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    .avatar-uploader .el-upload:hover {
        border-color: #409EFF;
    }
    .avatar-uploader-icon {
        font-size: 28px;
        color: #8c939d;
        width: 178px;
        height: 178px;
        line-height: 178px;
        text-align: center;
    }
    .avatar {
        width: 178px;
        height: 178px;
        display: block;
    }

    .reply-line1 {
        font-size:14px;
    }

    .reply-line1 .person{
        font-weight:700;
        font-size:16px;
    }

    .reply-line1 .time {
        font-size:13px;
        color:#838a9d;
        text-align:right;
        margin: 0 10px;
    }
</style>
<body>
    <div id="app" v-clock>
        <div style="display:flex;justify-content:flex-end">
            <template v-if="userData.username && userData.password">
                <el-button type="text">{{userData.nickname}}</el-button>
                <el-button type="text" @click="logout">退出</el-button>
            </template>
        </div>
        <h1 style="text-align:center">{{userData.nickname}}的个人中心</h1>
        <div>
            <el-upload
                name="image"
                class="avatar-uploader"
                action="/test/controllers/upload_pic.php"
                :show-file-list="false"
                :on-success="handleAvatarSuccess">
                <img v-if="userData.headPortrait !== ''" :src="userData.headPortrait" class="avatar">
                <i v-else class="el-icon-plus avatar-uploader-icon"></i>
            </el-upload>
            <!-- <form action="/test/controllers/upload_pic.php" method="post" enctype="multipart/form-data">
                <input type="file" name="image">
                <input type="submit" value="提交">
            </form> -->
        </div>
        <el-card class="box-card">
            <h4>近期互动：</h4>
            <div>
                <div v-for="(item,index) in dynimicList" :key="index">
                    <template v-if="item.parent_nick_name">
                        <div class="reply-line1"><span class="person">Ta</span>在<span class="time">{{transTimestanp(Number(item.timestamp)/1000)}} </span><el-button type="text" style="margin-right:10px">{{item.parent_nick_name}}</el-button>的留言下发表评论：</div>
                        <p>{{item.comment}}</p>
                    </template>
                    <template v-else>
                        <div class="reply-line1"><span class="person">Ta</span>在<span class="time">{{transTimestanp(Number(item.timestamp)/1000)}}</span>留言：</div>
                        <p>{{item.comment}}</p>
                    </template>
                    
                    <el-divider></el-divider>
                </div>
            </div>
        </el-card>
    </div>
</body>
<script>
    var sessionUser=<?php
        if(isset($_SESSION['user_name'])) {
            echo '"'.$_SESSION['user_name'].'"';
        } else {
            echo "''";
        }?>;
    var sessionPassword=<?php 
        if(isset($_SESSION['user_password'])) {
            echo '"'.$_SESSION['user_password'].'"';
        } else {
            echo "''";
        }?>;
    var c = new Vue({
        el: '#app',
        data: {
            userData: {
                username: '',
                password: '',
                nickname: '',
                headPortrait: ''
            },
            loginData: {
                user_name: '',
                password: ''
            },
            dynimicList: []
        },
        methods: {
            sureLogin() {
                axios.post('/test/controllers/login.php',this.loginData).then(response=> {
                    if(response.data.status ==200) {
                        this.loginData = {
                            user_name: '',
                            password: ''
                        };
                        this.userData.username = response.data.data.user_name;
                        this.userData.password = response.data.data.password;
                        this.userData.nickname = response.data.data.nick_name;
                        this.userData.headPortrait = window.location.origin + '/test' + response.data.data.head_portrait;
                        this.getDynimicList();
                    } else {
                        this.$message.error(response.data.msg);
                    }
                })
            },
            getDynimicList() {
                axios.post('/test/controllers/user_dyminic_data.php',{
                    user_name: this.userData.username
                }).then(response=> {
                    if(response.data.status ==200) {
                        this.dynimicList = response.data.data;
                    } else {
                        this.$message.error(response.data.msg);
                    }
                })
            },
            handleAvatarSuccess(res,file) {
                var imgUrl = res.data;
                axios.post('/test/controllers/update_user.php',{
                    head_portrait: imgUrl,
                    user_name: this.userData.username
                }).then(response => {
                    if(response.data.status == 200) {
                        window.location.reload();
                    } else {
                        thi.$message.error(response.data.msg);
                    }
                })
            },
            logout() {
                axios.post('/test/controllers/logout.php',{
                    user_name: this.userData.username
                }).then(response=> {
                    if(response.data.status ==200) {
                        this.$message.success('退出成功！');
                        setTimeout(() => {
                            window.location.href = './message_board.php';
                        }, 2000);
                        
                    } else {
                        this.$message.error(response.data.msg);
                    }
                })
            },
            transTimestanp(value) {
                if(!value) {
                    return '';
                }
                let day = new Date(value*1000);
                //利用拼接正则等手段转化为yyyy-MM-dd hh:mm:ss 格式
                return day.toLocaleDateString().replace(/\//g, "-") + " " + day.toTimeString().substr(0, 8);
            },
        },
        mounted() {

        },
        created() {
            if(sessionUser && sessionPassword) {
                this.loginData.user_name = sessionUser;
                this.loginData.password = sessionPassword;
                this.sureLogin();
            }
        }
    })
</script>
</html>