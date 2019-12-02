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
    <link rel="stylesheet" href="/Source/mui/css/mch.css"/>
    <script src="/Source/ydui/js/ydui.flexible.js"></script>
    <script src="/Source/ydui/js/ydui.js"></script>
    <link rel="stylesheet" href="/Source/iconfont/iconfont.css"/>
    <style>
        .tab-nav-item:not(:last-child):after{top: 0px;height: 100%;}
    </style>
</head>
<body>
<section class="g-flexview">
    <div class="g-scrollview">
        <div class="m-celltitle" style="margin-top: 15px;">移动支付费率</div>
        <div class="m-cell demo-small-pitch">
            <ul class="tab-nav">
                <li class="tab-nav-item">通道名称</li>
                <li class="tab-nav-item">费率(千分比)</li>
            </ul>
            <?php if(is_array($data["pay"])): $i = 0; $__LIST__ = $data["pay"];if( count($__LIST__)==0 ) : echo "暂没有开通的通道信息" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><ul class="tab-nav">
                    <li class="tab-nav-item" ><?php echo ($vo["alleys"]); ?></li>
                    <li class="tab-nav-item" ><?php echo ($vo["rate"]); ?>‰</li>
                </ul><?php endforeach; endif; else: echo "暂没有开通的通道信息" ;endif; ?>
        </div>

        <!--<div class="m-celltitle" style="margin-top: 15px;">无卡快捷费率</div>-->
        <!--<div class="m-cell demo-small-pitch" style="margin-bottom: 50px">-->
            <!--<ul class="tab-nav">-->
                <!--<li class="tab-nav-item">通道名称</li>-->
                <!--<li class="tab-nav-item">费率(千分比)</li>-->
            <!--</ul>-->
            <!--<?php if(is_array($data["card"])): $i = 0; $__LIST__ = $data["card"];if( count($__LIST__)==0 ) : echo "暂没有开通的通道信息" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>-->
                <!--<ul class="tab-nav">-->
                    <!--<li class="tab-nav-item" ><?php echo ($v["alleys"]); ?></li>-->
                    <!--<li class="tab-nav-item" ><?php echo ($v["rate"]); ?>‰</li>-->
                <!--</ul>-->
            <!--<?php endforeach; endif; else: echo "暂没有开通的通道信息" ;endif; ?>-->
        <!--</div>-->

        <footer class="m-tabbar tabbar-fixed">
            <a href="<?php echo U('Index/index');?>" class="tabbar-item tabbar-active">
                <span class="tabbar-icon">
                    <i class="icon-home-outline"></i>
                </span>
                <span class="tabbar-txt">主页</span>
            </a>
            <a href="<?php echo U('Index/order');?>" class="tabbar-item">
                <span class="tabbar-icon">
                    <i class="icon-order"></i>
                    <span class="tabbar-dot"></span>
                </span>
                <span class="tabbar-txt">流水</span>
            </a>
            <a href="<?php echo U('Index/my');?>" class="tabbar-item">
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