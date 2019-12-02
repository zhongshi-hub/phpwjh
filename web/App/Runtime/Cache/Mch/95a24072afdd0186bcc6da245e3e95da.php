<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>商户中心</title>
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
            background: rgba(0, 0, 0, 0) radial-gradient(circle at left top , #d1f0d0, #c3edda 10%, #84c4d4 40%, #a1bbdf) repeat scroll 0 0;
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
            bottom: 5px;
            font-size: 12px;
            height: 50px;
            position: absolute;
            right: 0;
            text-align: center;
            width: 180px;
        }
        .user .icons .icon {
            background-repeat: no-repeat;
            background-size: 20px 20px;
            height: 20px;
            margin: 6px auto;
            width: 20px;
        }
        .user .out, .user .onsales,.user .wishlist {
            display: block;
            float: left;
            height: 50px;
            width: 60px;
        }
        .user .wishlist .icon {
            background-image: url("/Source/ydui/my/kf.png");
        }
        .user .out .icon {
            background-image: url("/Source/ydui/my/out.png");
        }
        .user .onsales .icon {
            background-image: url("/Source/ydui/my/new_pass.png");
        }

    </style>
</head>
<body>
<section class="g-flexview" style="margin-bottom: 50px">
    <div class="g-scrollview" style="margin-top: 0px">
        <div class="user" style="background: #4282e3">
            <?php if(empty($_seller['mch_wx_img'])): ?><img src="/Source/ydui/my/my.jpg" alt="" class="photo" style="top: 50px;border: 1px solid #ffffff;border-radius: 15px;left: 15px">
            <?php else: ?>
                <img src="<?php echo ($_seller["mch_wx_img"]); ?>" alt="" class="photo" style="top: 50px;border: 1px solid #ffffff;border-radius: 15px;left: 15px"><?php endif; ?>

            <div class="user_bg" style="background: #4282e3;height: 120px">
                <div class="user_info" style="left: 92px">
                    <span class="grade" style="border-radius: 5px;"><?php echo ($_seller["mch_name"]); ?></span>
                    <p style="margin-top: 15px;font-size: 14px;color: #ffffff"><?php echo ($_seller["mch_tel"]); ?></p>
                </div>

            </div>
            <div class="icons" style="color: #ffffff">
                <a class="wishlist" href="tel: <?php echo ($atel); ?>">
                    <div class="icon"></div>
                    <span>联系专员</span>
                </a>
                <a class="onsales" href="<?php echo U('Login/r_pass');?>">
                    <div class="icon"></div>
                    <span>修改密码</span>
                </a>
                <a class="out" href="<?php echo U('Login/mch_quit');?>">
                    <div class="icon"></div>
                    <span>退出登录</span>
                </a>
            </div>
        </div>
        <ul class="mui-table-view mui-table-view-chevron">
            <li class="mui-table-view-cell">
                <a href="<?php echo U('mch_data');?>" class="mui-navigate-right">商户信息</a>
            </li>
            <li class="mui-table-view-cell">
                <a href="<?php echo U('mch_rate_list');?>" class="mui-navigate-right">费率信息</a>
            </li>
            <!--<li class="mui-table-view-cell">-->
                <!--<a href="<?php echo U('mch_auth');?>" class="mui-navigate-right">实名认证</a>-->
            <!--</li>-->
            <!--<li class="mui-table-view-cell">-->
                <!--<a href="<?php echo U('bank_card_list');?>" class="mui-navigate-right">我的信用卡</a>-->
            <!--</li>-->
        </ul>
        <!--<?php if(($extensionStatus) == "1"): ?>-->
        <!--<ul class="mui-table-view mui-table-view-chevron">-->
            <!--<li class="mui-table-view-cell">-->
                <!--<a href="<?php echo U('Extension/myGrade');?>" class="mui-navigate-right">我的等级</a>-->
            <!--</li>-->
            <!--<li class="mui-table-view-cell">-->
                <!--<a href="<?php echo U('Extension/grade');?>" class="mui-navigate-right">升级等级</a>-->
            <!--</li>-->
            <!--<li class="mui-table-view-cell">-->
                <!--<a href="<?php echo U('Extension/benefit');?>" class="mui-navigate-right">我的分润</a>-->
            <!--</li>-->
        <!--</ul>-->
        <!--<?php endif; ?>-->
        <!--<ul class="mui-table-view mui-table-view-chevron">
            <li class="mui-table-view-cell">
                <a href="#account" class="mui-navigate-right">公告中心</a>
            </li>
            <li class="mui-table-view-cell">
                <a href="#notifications" class="mui-navigate-right">使用帮助</a>
            </li>
            <li class="mui-table-view-cell">
                <a href="#privacy" class="mui-navigate-right">关于我们</a>
            </li>
        </ul>-->

        <?php if(($sys_xy['fw_status'] == 1) or ($sys_xy['bm_status'] == 1)): ?><ul class="mui-table-view mui-table-view-chevron">
            <?php if(($sys_xy["fw_status"]) == "1"): ?><li class="mui-table-view-cell">
                <a href="<?php echo U('Index/fw');?>" class="mui-navigate-right"><?php echo ((isset($sys_xy["fw_name"]) && ($sys_xy["fw_name"] !== ""))?($sys_xy["fw_name"]):'服务协议'); ?></a>
            </li><?php endif; ?>
            <?php if(($sys_xy["bm_status"]) == "1"): ?><li class="mui-table-view-cell">
                <a href="<?php echo U('Index/bm');?>" class="mui-navigate-right"><?php echo ((isset($sys_xy["bm_name"]) && ($sys_xy["bm_name"] !== ""))?($sys_xy["bm_name"]):'保密协议'); ?></a>
            </li><?php endif; ?>
        </ul><?php endif; ?>
        <!--<ul class="mui-table-view">
            <li class="mui-table-view-cell" style="text-align: center;">
                <a href="<?php echo U('Login/mch_quit');?>">退出登录</a>
            </li>
        </ul>-->

</section>
<footer class="m-tabbar tabbar-fixed">
    <a href="<?php echo U('Index/index');?>" class="tabbar-item ">
                <span class="tabbar-icon">
                    <i class="icon-home-outline"></i>
                </span>
        <span class="tabbar-txt">主页</span>
    </a>
    <a href="<?php echo U('Index/order');?>" class="tabbar-item ">
                <span class="tabbar-icon">
                    <i class="icon-order"></i>
                    <span class="tabbar-dot"></span>
                </span>
        <span class="tabbar-txt">流水</span>
    </a>
    <a href="<?php echo U('Index/my');?>" class="tabbar-item tabbar-active">
                <span class="tabbar-icon">
                    <i class="icon-ucenter-outline"></i>
                </span>
        <span class="tabbar-txt">我的</span>
    </a>
</footer>
<script src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
</body>
</html>