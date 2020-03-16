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

    .mini_comment_item {
        cursor: pointer;
    }

    .mini_comment_item:hover {
        text-decoration:underline;
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

    .time-date {
        font-size:12px;
        color:#838a9d;
        text-align:right;
    }
</style>
<body>
    <div id="app" v-clock>
        <h1 style="text-align:center">Bullshit论坛</h1>
        <div style="display:flex;justify-content:flex-end">
            <template v-if="userData.username && userData.password">
                <el-button type="text" @click="toUserCenter">{{userData.nickname}}</el-button>
                <el-button type="text" @click="logout">退出</el-button>
            </template>
            <template v-else>
                <el-button type="text" @click="login">登录</el-button>
                <el-button type="text" @click="signIn">注册</el-button>
            </template>
        </div>
        <div>
            <el-input
                type="textarea"
                :rows="3"
                placeholder="请输入留言内容"
                v-model="commentValue">
            </el-input>
        </div>
        <div style="display:flex;justify-content:center;margin-top:20px;">
            <el-button @click="submitData" type="primary">提交</el-button>
        </div>
        <div>
            <h3>留言板</h3>
            <el-card class='box-card'>
                <div v-for="(item,index) in commentList" :key="index">
                    <p>
                        <el-button type="text" size="middle">{{item.nick_name}}</el-button>
                        <span> ：</span>
                        <span class="mini_comment_item" @click="addOneComment(item,index)">{{item.comment}}</span>
                    </p>
                    <p class="time-date">{{transTimestanp(Number(item.timestamp)/1000)}}</p>
                    <div style="display:flex;justify-content:flex-end">
                        <el-button @click="addComment(item,index)" size="small" type="text">回复</el-button>
                    </div>
                    <div v-if="item.show">
                        <div v-for="(it,inx) in item.miniCommentList" :key="inx">
                            <p style="padding-left:20px;font-size:12px">
                                <template v-if="it.reply_whom">
                                    <el-button type="text" size="mini">{{it.nick_name}}</el-button>
                                    <span>回复</span>
                                    <el-button type="text" size="mini">{{it.reply_nick_name}}</el-button>
                                </template>
                                <el-button type="text" size="mini" v-else>{{it.nick_name}}</el-button>
                                <span> : <span>
                                <span @click="replayTo(item,it,index)" class="mini_comment_item">{{it.comment}}</span>
                            </p>
                            <p class="time-date" style="padding-right:30px;font-size:10px;">{{transTimestanp(Number(it.timestamp)/1000)}}</p>
                        </div>
                        <el-input
                            type="textarea"
                            :rows="2"
                            :placeholder="item.placeholder"
                            v-model="commentList[index]['miniComment']">
                        </el-input>
                        <div style="display:flex;justify-content:flex-end;padding-top: 15px">
                            <el-button type="primary" size="mini" @click="sureAddMini(item,index)">确定</el-button>
                        </div>
                    </div>
                    <el-divider></el-divider>
                </div>
            </el-card>
        </div>
        <div style="display:flex;justify-content:center;margin:10px 0">
            <el-pagination
                small="true"
                layout="prev, pager, next"
                :total="totalCount"
                :page-sizes="pageSize"
                :current-page="currentPage"
                @current-change="changePage">
            </el-pagination>
        </div>
        <el-dialog
            title="注册"
            :visible.sync="dialog.register"
            width="450px">
            <div>
                <div>
                    <el-upload
                        name="image"
                        class="avatar-uploader"
                        action="/test/controllers/upload_pic.php"
                        :show-file-list="false"
                        :on-success="handleAvatarSuccess">
                        <img v-if="registerData.headPortrait !== ''" :src="window.location.origin + '/test'+registerData.headPortrait" class="avatar">
                    <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                </el-upload>
                </div>
                <div>
                    <span class="demonstration">用户名：</span>
                    <el-input v-model="registerData.user_name"></in-put>
                </div>
                <div>
                    <span class="demonstration">昵称：</span>
                    <el-input v-model="registerData.nick_name"></in-put>
                </div>
                <div>
                    <span class="demonstration">密码：</span>
                    <el-input type="password" v-model="registerData.password"></in-put>
                </div>
            </div>
            <span slot="footer" class="dialog-footer">
                <el-button @click="cancleRegister">取 消</el-button>
                <el-button type="primary" @click="sureRegister">确 定</el-button>
            </span>
        </el-dialog>
        <el-dialog
            title="登录"
            :visible.sync="dialog.login"
            width="300px">
            <div>
                <div>
                    <span class="demonstration">用户名：</span>
                    <el-input v-model="loginData.user_name"></in-put>
                </div>
                <div>
                    <span class="demonstration">密码：</span>
                    <el-input type="password" v-model="loginData.password"></in-put>
                </div>
            </div>
            <span slot="footer" class="dialog-footer">
                <el-button @click="cancleLogin">取 消</el-button>
                <el-button type="primary" @click="sureLogin">确 定</el-button>
            </span>
        </el-dialog>
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
        el:'#app',
        data: {
            commentValue: '',
            commentList: [],
            currentPage: 1,
            pageSize:10,
            totalCount:100,
            registerData: {
                nick_name: '',
                user_name: '',
                password: '',
                headPortrait: ''
            },
            loginData: {
                user_name: '',
                password: ''
            },
            dialog: {
                register: false,
                login: false
            },
            userData: {
                username: '',
                password: '',
                nickname: '',
                headPortrait: ''
            }
        },
        methods: {
            submitData() {
                if(!this.commentValue) {
                    this.$message.error('请输入内容！');
                    return;
                }
                if(!this.userData.username) {
                    this.$message.error('请先登录！');
                    return;
                }
                axios.post('/test/controllers/add.php',{
                    comment: this.commentValue,
                    user_name: this.userData.username,
                    nick_name: this.userData.nickname,
                    timestamp: Date.parse(new Date())
                }).then(response=> {
                    if(response.data.status == 200) {
                        this.$message.success('留言成功!');
                        this.currentPage = 1;
                        this.getList();
                        this.commentValue = '';
                    } else {
                        this.$message.error(response.data.msg);
                    }
                })
            },
            getList() {
                axios.post('/test/controllers/select.php',{
                    page: this.currentPage
                }).then(response=> {
                    if(response.data.status ==200) {
                        this.commentList = response.data.data;
                        this.totalCount = response.data.total;
                        this.commentList.forEach(element => {
                            this.$set(element,'show',false);
                            this.$set(element,'miniComment','');
                            this.$set(element,'miniCommentList',[]);
                            this.$set(element,'toWhom','');
                            this.$set(element,'placeholder','请输入留言');
                        });
                    } else {
                        this.$message.error(response.data.msg);
                    }
                })
            },
            changePage(val) {
                this.currentPage = val;
                this.getList();
            },
            login() {
                this.dialog.login = true;
            },
            signIn() {
                this.dialog.register = true;
            },
            cancleRegister() {
                this.dialog.register = false;
                this.registerData = {
                    nick_name: '',
                    user_name: '',
                    password: ''
                };
            },
            sureRegister() {
                axios.post('/test/controllers/register.php',this.registerData).then(response=> {
                    if(response.data.status ==200) {
                        this.$message.success('注册成功！');
                        this.dialog.register = false;
                        this.loginData.user_name = this.registerData.user_name;
                        this.loginData.password = this.registerData.password;
                        this.registerData = {
                            nick_name: '',
                            user_name: '',
                            password: ''
                        };
                        this.sureLogin();
                    } else {
                        this.$message.error(response.data.msg);
                    }
                })
            },
            cancleLogin() {
                this.dialog.login = false;
                this.loginData = {
                    user_name: '',
                    password: ''
                };
            },
            sureLogin() {
                axios.post('/test/controllers/login.php',this.loginData).then(response=> {
                    if(response.data.status ==200) {
                        // this.$message.success('登录成功！');
                        this.dialog.login = false;
                        this.loginData = {
                            user_name: '',
                            password: ''
                        };
                        this.userData.username = response.data.data.user_name;
                        this.userData.password = response.data.data.password;
                        this.userData.nickname = response.data.data.nick_name;
                        this.userData.headPortrait = response.data.data.head_portrait;
                    } else {
                        this.$message.error(response.data.msg);
                    }
                })
            },
            addComment(item,index) {
                if(item.show === false) {
                    this.commentList[index]['show'] = !this.commentList[index]['show'];
                    this.getMiniComment(item,index);
                } else {
                    this.commentList[index]['show'] = !this.commentList[index]['show'];
                }
                this.$set(this.commentList[index],'placeholder','请输入留言');
                this.$set(this.commentList[index],'toWhom','');
            },
            addOneComment(item,index) {
                this.commentList[index]['show'] = true;
                this.getMiniComment(item,index);
                this.$set(this.commentList[index],'placeholder','请输入留言');
                this.$set(this.commentList[index],'toWhom','');
            },
            sureAddMini(item,index) {
                if(!this.userData.username) {
                    this.$message.error('请先登录！');
                    return;
                }
                axios.post('/test/controllers/mini_comment_add.php',{
                    parent_id: item.id,
                    user_name: this.userData.username,
                    comment:item.miniComment,
                    nick_name: this.userData.nickname,
                    parent_user: item.user,
                    reply_whom: item.toWhom,
                    timestamp: Date.parse(new Date())
                }).then(response=> {
                    if(response.data.status ==200) {
                        this.$message.success('回复成功！');
                        this.commentList[index]['miniComment'] = '';
                        this.getMiniComment(item,index);
                    } else {
                        this.$message.error(response.data.msg);
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
                            window.location.reload();
                        }, 2000);
                        
                    } else {
                        this.$message.error(response.data.msg);
                    }
                })
            },
            getMiniComment(item,index) {
                axios.post('/test/controllers/mini_comment_get.php',{
                    comment_id: item.id
                }).then(response=> {
                    if(response.data.status == 200) {
                        this.commentList[index]['miniCommentList'] = response.data.data;
                    }
                })
            },
            replayTo(item,it,index) {
                this.commentList[index]['toWhom'] = it.user_name;
                this.commentList[index]['placeholder'] = '回复@'+it.nick_name+': ';
            },
            toUserCenter() {
                window.location.href = './user_center.php';
            },
            handleAvatarSuccess(res,file) {
                this.registerData.headPortrait = res.data;
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
        mounted(){
            this.getList();
        },
        created() {
            if(sessionUser && sessionPassword) {
                this.loginData.user_name = sessionUser;
                this.loginData.password = sessionPassword;
                this.sureLogin();
            }
        }
    });
    
</script>
</html>



