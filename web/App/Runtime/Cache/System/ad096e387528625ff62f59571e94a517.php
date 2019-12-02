<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="en" class="login-content" >
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="Generator" content="EditPlus®">
    <meta name="Author" content="">
    <meta name="Keywords" content="">
    <meta name="Description" content="">
    <title><?php echo C('WEB_NAME');?>管理系统</title>
    <link rel="shortcut icon"  href="<?php echo GetPico();?>" />
    <link href="/Source/login/css/material-design-iconic-font/css/material-design-iconic-font.min.css" rel="stylesheet" type="text/css">
    <link href="/Source/login/css/app.min.1.css" rel="stylesheet" type="text/css">
    <link href="/Source/statics/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <script type="text/javascript" src="/Source/statics/js/jquery-2.2.4.min.js"></script>
    <script type="text/javascript" src="/Source/layer/layer.js"></script>
</head>
<body class="login-content">

<div class="lc-block toggled" id="l-login">
    <h1 class="lean">登录</h1>
    <form action="" method="post">
    <div class="input-group m-b-20">
    		<span class="input-group-addon">
    			<i class="fa fa-user-md" style="width:20px;font-size: 28px;color: dimgray;"></i>
    		</span>
        <div class="fg-line">
            <input type="text" class="form-control" name="username" placeholder="用户名"/>
        </div>
    </div>
    <div class="input-group m-b-20">
    		<span class="input-group-addon">
    			<i class="fa fa-unlock-alt" style="width:20px;font-size: 28px;color: dimgray;"></i>
    		</span>
        <div class="fg-line">
            <input type="password" class="form-control" name="password" placeholder="密码"/>
        </div>
    </div>
    <div class="input-group m-b-20">
    		<span class="input-group-addon" >
    			<i class="fa fa-shield" style="width:20px;font-size: 28px;color: dimgray;"></i>
    		</span>
        <div class="fg-line">
            <input type="text" class="form-control" name="verify" placeholder="验证码" style="width: 55%;"/>
            <img src="<?php echo U('load_verify');?>" onclick="changeimg()" id="verify" name="verify" title="点击更换验证码" style="margin-top:-15px;width:40%;float:right;height: 50px; " class="hide-password">
        </div>
    </div>
    <div class="clearfix"></div>
    <div style="text-align: center;font-weight: 400;"> 管理后台 <?php echo date('Y');?></div>
    <button type="submit" class="btn btn-login btn-danger btn-float">
        <i class="zmdi zmdi-arrow-forward"></i>
    </button>
    </form>
</div>
<script type="text/javascript">
    /*表单格式全部ajax提交 2017-02-28 CCL*/
    $(function() {
        $("form").submit(function (e) {
            e.preventDefault(); //阻止自动提交表单
            var ajax_data = $(this).serialize();
            var actionurl = $(this).attr("action");
            $.post(actionurl, ajax_data, function(data){
                if(data.status == 1){
                    msgs(data.info);
                    setTimeout(function(){
                        window.location.href=data.url
                    }, 3000);
                }
                else{
                    msgs(data.info);
                }
            }, 'json');
        });
    });
    
    //验证码更新
    function changeimg(){
        var times = new Date().getTime();
        document.getElementById('verify').src="<?php echo U('load_verify');?>/time/"+times;
    }
    //消息提示框
    function msgs(txt){
        layer.msg(txt, {time: 3000,offset: 0,shift: 6});
    }
</script>
</body>
</html>