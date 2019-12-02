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
</head>
<body>
<section class="g-flexview">
    <header class="m-navbar">
        <a onclick="call_back()" class="navbar-item"><i class="back-ico"></i></a>
        <div class="navbar-center"><span class="navbar-title">订单详细信息</span></div>
    </header>
    <div class="g-scrollview">
        <div class="m-cell demo-small-pitch">
            <div class="cell-item">
                <div class="cell-left">金额</div>
                <div class="cell-right" style="color: red"><?php echo ($data["total_fee"]); ?></div>
            </div>
            <div class="cell-item">
                <div class="cell-left">支付状态</div>
                <div class="cell-right" style="color: red"><?php echo (pays_status($data["status"])); ?></div>
            </div>
            <div class="cell-item">
                <div class="cell-left">支付类型</div>
                <div class="cell-right"><?php echo (pays_type($data["service"])); ?></div>
            </div>
            <div class="cell-item">
                <div class="cell-left">支付通道</div>
                <div class="cell-right"><?php echo (alleys_name($data["alleys"])); ?></div>
            </div>
        </div>

        <div class="m-celltitle" style="margin-top: 15px;">订单详细信息</div>
        <div class="m-cell demo-small-pitch" style="margin-bottom: 50px">
            <div class="cell-item">
                <div class="cell-left">描述</div>
                <div class="cell-right"><?php echo ($data["body"]); ?></div>
            </div>
            <div class="cell-item">
                <div class="cell-left">订单号</div>
                <div class="cell-right"><?php echo ($data["out_trade_no"]); ?></div>
            </div>
            <?php if(!empty($data["transaction_id"])): ?><div class="cell-item">
                <div class="cell-left">三方订单号</div>
                <div class="cell-right"><?php echo ($data["transaction_id"]); ?></div>
            </div><?php endif; ?>
            <?php if(!empty($data["out_transaction_id"])): ?><div class="cell-item">
                <div class="cell-left">官方订单号</div>
                <div class="cell-right"><?php echo ($data["out_transaction_id"]); ?></div>
            </div><?php endif; ?>
            <div class="cell-item">
                <div class="cell-left">支付时间</div>
                <div class="cell-right"><?php echo (date('Y-m-d H:i:s',$data["createtime"])); ?></div>
            </div>
            <div class="cell-item">
                <div class="cell-left">完成时间</div>
                <div class="cell-right"><?php echo (date('Y-m-d H:i:s',$data["time_end"])); ?></div>
            </div>

        </div>



        <footer class="m-tabbar tabbar-fixed">
            <a href="<?php echo U('Index/index');?>" class="tabbar-item">
                <span class="tabbar-icon">
                    <i class="icon-home-outline"></i>
                </span>
                <span class="tabbar-txt">主页</span>
            </a>
            <a href="<?php echo U('Index/order');?>" class="tabbar-item tabbar-active">
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
<script>
    function call_back() {
        location.href=document.referrer;
    }
</script>
</body>
</html>