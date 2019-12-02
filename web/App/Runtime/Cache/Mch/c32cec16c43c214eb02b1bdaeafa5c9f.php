<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>代理信息</title>
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport"/>
    <meta content="yes" name="apple-mobile-web-app-capable"/>
    <meta content="black" name="apple-mobile-web-app-status-bar-style"/>
    <meta content="telephone=no" name="format-detection"/>
    <link rel="stylesheet" href="/Source/ydui/css/ydui.css"/>
    <link rel="stylesheet" href="/Source/ydui/css/my-app.css"/>
    <style>
        .tabbar-item.tabbar-active{color: #108ee9}
    </style>
</head>
<body>
<section class="g-flexview">
    <header class="m-navbar">
        <a onclick="call_back()" class="navbar-item"><i class="back-ico"></i></a>
        <div class="navbar-center"><span class="navbar-title">个人详细信息</span></div>
    </header>
    <div class="g-scrollview" style="margin-bottom: 50px">
        <div class="m-cell">
            <div class="cell-item">
                <div class="cell-left">代理姓名</div>
                <div class="cell-right"><?php echo ($data["user_name"]); ?></div>
            </div>
            <div class="cell-item">
                <div class="cell-left">联系方式</div>
                <div class="cell-right"><?php echo ($data["user_phone"]); ?></div>
            </div>
            <div class="cell-item">
                <div class="cell-left">加入时间</div>
                <div class="cell-right"> <?php echo (date('Y-m-d H:i:s',$data["ctime"])); ?></div>
            </div>
        </div>
        <?php if(is_array($api)): foreach($api as $k=>$v): ?><div class="m-celltitle" style="margin-top: 15px;"><?php echo ($v["alleys"]); ?>通道费率</div>
            <div class="m-cell">
                <div class="cell-item">
                    <div class="cell-left">成本费率</div>
                    <div class="cell-right">
                        <?php $type=$v['alleys_type'].'_cost'; echo $rate[$type]; ?>
                        ‰
                    </div>
                </div>
                <div class="cell-item">
                    <div class="cell-left">终端费率</div>
                    <div class="cell-right">
                        <?php $type=$v['alleys_type'].'_term'; echo $rate[$type]; ?>
                        ‰
                    </div>
                </div>
            </div><?php endforeach; endif; ?>
    </div>
        <footer class="m-tabbar tabbar-fixed">
            <a href="<?php echo U('Agent/index');?>" class="tabbar-item">
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
<script src="/Source/ydui/js/ydui.js"></script>
<script src="/Source/ydui/js/ydui.flexible.js"></script>
<script>
    function call_back() {
        location.href=document.referrer;
    }
</script>

</body>
</html>