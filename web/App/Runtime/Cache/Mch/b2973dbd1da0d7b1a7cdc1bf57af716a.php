<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>代理管理中心</title>
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport"/>
    <meta content="yes" name="apple-mobile-web-app-capable"/>
    <meta content="black" name="apple-mobile-web-app-status-bar-style"/>
    <meta content="telephone=no" name="format-detection"/>
    <link rel="stylesheet" href="/Source/mui/css/mui.min.css">
    <link rel="stylesheet" href="/Source/ydui/css/ydui.css"/>
    <link rel="stylesheet" href="/Source/statics/plugins/font-awesome/css/font-awesome.min.css"/>
    <script src="/Source/ydui/js/ydui.flexible.js"></script>
    <script src="/Source/ydui/js/ydui.js"></script>
    <link rel="stylesheet" href="/Source/iconfont/iconfont.css"/>
    <style>
        .mui-table-view{margin-top: 20px;font-size: 17px}
        .user {
            background: #fff none repeat scroll 0 0;
            height: 158px;
            position: relative;
            width: 100%;
        }
        .user_bg {
            background-color:#108ee9;
            height: 106px;
            position: absolute;
            width: 100%;
        }
        .user .photo {
            border-radius: 50%;
            display: block;
            height: 64px;
            left: 32px;
            position: absolute;
            top: 72px;
            width: 64px;
            z-index: 10;
        }
        .user_info {
            bottom: 4px;
            color: #fff;
            font-size: 12px;
            left: 112px;
            position: absolute;
        }
        .user_info .name {
            display: block;
            font-size: 13px;
            letter-spacing: 0.65px;
            line-height: 15px;
        }
        .user_info .grade {
            background: rgba(255, 255, 255, 0.2) none repeat scroll 0 0;
            border-radius: 100px;
            letter-spacing: 0.65px;
            line-height: 18px;
            padding: 5px 8px;
            font-size: 16px;
        }
        .user .icons {
            bottom: 2px;
            font-size: 12px;
            height: 50px;
            position: absolute;
            right: 0;
            text-align: center;
            width: 155px;
        }
        .user .icons .icon {
            background-repeat: no-repeat;
            background-size: 20px 20px;
            height: 20px;
            margin: 6px auto;
            width: 20px;
        }
        .user .onsale, .user .wishlist {
            display: block;
            float: left;
            height: 50px;
            width: 76px;
        }
        .user .wishlist .icon {
            background-image: url("/Source/ydui/my/fav.png");
        }
        .user .onsale .icon {
            background-image: url("/Source/ydui/my/onsale.png");
        }
        .tabbar-item.tabbar-active{color: #108ee9}
    </style>
</head>
<body>
<section class="g-flexview">
    <div class="g-scrollview" style="margin-top: 0px!important;">
        <div class="user" style="height: 106px;color: #ffffff">
            <img src="/Source/ydui/my/default.png?t=rest" style="top: 30px;border-radius: 20%;" class="photo">
            <div class="user_bg">
                <div class="user_info" style="bottom: 15px;left: 110px">
                    <span class="grade" style="border-radius: 10%;"><?php echo ($_SESSION['ag']['user_name']); ?></span>
                </div>
            </div>
            <div class="icons" style="width: 76px;bottom: 15px;">
                <a class="onsale" href="<?php echo U('Login/agent_pass');?>">
                    <div class="icon"></div>
                    <span>修改密码</span>
                </a>
            </div>
        </div>


        <ul class="mui-table-view mui-table-view-chevron">
            <li class="mui-table-view-cell">
                <a href="<?php echo U('person');?>" class="mui-navigate-right">个人信息</a>
            </li>
        </ul>

        <ul class="mui-table-view mui-table-view-chevron">
            <li class="mui-table-view-cell">
                <a href="#account" class="mui-navigate-right">公告中心</a>
            </li>
        </ul>

        <ul class="mui-table-view mui-table-view-chevron">
            <li class="mui-table-view-cell">
                <a href="#notifications" class="mui-navigate-right">使用帮助</a>
            </li>
            <li class="mui-table-view-cell">
                <a href="#privacy" class="mui-navigate-right">关于我们</a>
            </li>
        </ul>

        <ul class="mui-table-view">
            <li class="mui-table-view-cell" style="text-align: center;">
                <a href="<?php echo U('Login/agent_quit');?>">退出登录</a>
            </li>
        </ul>
        <footer class="m-tabbar tabbar-fixed">
            <a href="<?php echo U('Agent/index');?>" class="tabbar-item ">
                <span class="tabbar-icon">
                    <i class="icon-home-outline"></i>
                </span>
                <span class="tabbar-txt">主页</span>
            </a>
            <a href="<?php echo U('Agent/mch_data');?>" class="tabbar-item">
                <span class="tabbar-icon">
                    <i class="icon-discover"></i>
                </span>
                <span class="tabbar-txt">商户</span>
            </a>
            <a href="<?php echo U('Agent/qrcode');?>" class="tabbar-item">
                <span class="tabbar-icon">
                    <i class="icon-qrscan"></i>
                    <span class="tabbar-dot"></span>
                </span>
                <span class="tabbar-txt">收款码</span>
            </a>
            <a href="<?php echo U('Agent/my');?>" class="tabbar-item tabbar-active">
                <span class="tabbar-icon">
                    <i class="icon-ucenter-outline"></i>
                </span>
                <span class="tabbar-txt">我的</span>
            </a>
        </footer>
</section>
<script src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
</body>
</html>