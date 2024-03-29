<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html style="height: auto">
<head>
    <meta charset="utf-8">
    <meta name="viewport"
          content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no"/>
    <title><?php echo ($_domain['web_name']); ?>商户登录</title>

    <link rel="stylesheet" href="/Source/ydui/css/ydui.css"/>
    <link rel="stylesheet" href="/Source/ydui/css/my-app.css"/>
    <style>
        .logo{
            width:100%;
            height: 160px;
            text-align: center;
            margin-top: 18%;

        }
        .logo img {
            width: 80px;
            height: 80px;
            border: 1px solid #ffffff;
            border-radius: 10px;
            background-color: #ffffff;
            padding: 2px;
        }
        .btn-primary{
            background-color:#4282e3;
        }
        .btn-primary:active{
            background-color:#5593F1;
        }
    </style>

</head>
<body style="background-color: #F7F7F7!important;">
<section class="g-flexview">
    <!--<header class="m-navbar">
        <div class="navbar-center"><span class="navbar-title">商户登录</span></div>
    </header>-->
    <section class="g-scrollview">
        <!--<aside class="demo-tip">
            请正确输入商户手机号码 如忘记密码 点击忘记密码进行修改
        </aside>-->
        <div class="logo">
           <center><img src="<?php echo GetPlogo();?>"></center>
           <p style="margin-top: 15px;color: #4C4C4C;font-size: 16px"><?php echo ($_domain['web_name']); ?></p>
        </div>
        <form action="<?php echo U('index');?>">
        <div class="m-cell" style="margin: 15px">
            <div class="cell-item">
                <div class="cell-right"><input class="cell-input" placeholder="请输入手机号" autocomplete="off" type="text" name="user_tel" required>
                </div>
            </div>
            <div class="cell-item">
                <div class="cell-right"><input class="cell-input" placeholder="请输入登录密码" autocomplete="off" type="password" name="user_pass" required>
                </div>
            </div>
        </div>

        <div class="m-button">
            <div style="text-align: right;font-size: 14px;margin-right: 5px"><a href="<?php echo U('r_pass');?>" style="color: #097cff"><i class="icon-question"></i>忘记密码</a></div>
            <input type="hidden" name="quick_data" value="<?php echo ($_GET['quick_data']); ?>">
            <button type="submit" class="btn-block btn-primary"> 登 录 </button>
        </div>
        </form>
    </section>
   <!-- <section class="with-line xun_footer">
        <?php echo ($_domain['web_name']); ?>&copy;版权所有
    </section>-->
</section>
<script src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="/Source/ydui/js/ydui.js"></script>
<script src="/Source/ydui/js/ydui.flexible.js"></script>
<script>
    $(function () {
        $("form").submit(function (e) {
            e.preventDefault(); //阻止自动提交表单
            var ajax_data = $(this).serialize();
            var actionurl = $(this).attr("action");
            YDUI.dialog.loading.open('登录中...');
            $.post(actionurl, ajax_data, function (data) {
                YDUI.dialog.loading.close();
                if (data.status == 1) {
                    YDUI.dialog.toast(data.info, 'none', function(){
                        window.location.href = data.url
                    });
                }
                else {
                    if(data.url){
                        YDUI.dialog.alert(data.info, function(){
                            window.location.href = data.url
                        });
                    }else {
                        msg(data.info);
                    }
                }
            }, 'json');
        });

        function msg(data) {
            YDUI.dialog.toast(data, 'none');
        }

    });
</script>
</body>

</html>