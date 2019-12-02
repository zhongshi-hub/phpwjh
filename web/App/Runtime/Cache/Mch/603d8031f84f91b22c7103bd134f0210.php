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
    <div class="g-scrollview">
        <div class="m-celltitle" style="margin-top: 15px;">商户信息</div>
        <div class="m-cell demo-small-pitch">
            <div class="cell-item">
                <div class="cell-left">商户名称</div>
                <div class="cell-right"><?php echo ($_seller["mch_name"]); ?></div>
            </div>
            <div class="cell-item">
                <div class="cell-left">联系电话</div>
                <div class="cell-right"><?php echo ($_seller["mch_tel"]); ?></div>
            </div>
            <div class="cell-item">
                <div class="cell-left">所属省份</div>
                <div class="cell-right"><?php echo ($_seller["mch_provice"]); ?></div>
            </div>
            <div class="cell-item">
                <div class="cell-left">所属城市</div>
                <div class="cell-right"><?php echo ($_seller["mch_citys"]); ?></div>
            </div>
            <div class="cell-item">
                <div class="cell-left">所属区县</div>
                <div class="cell-right"><?php echo ($_seller["mch_district"]); ?></div>
            </div>
            <div class="cell-item">
                <div class="cell-left">详细地址</div>
                <div class="cell-right"><?php echo ($_seller["mch_address"]); ?></div>
            </div>
            <div class="cell-item">
                <div class="cell-left">负责人姓名</div>
                <div class="cell-right"><?php echo ($_seller["mch_card_name"]); ?></div>
            </div>
        </div>

        <div class="m-celltitle" style="margin-top: 15px;">结算银行卡信息</div>
        <div class="m-cell demo-small-pitch" style="margin-bottom: 50px">
            <div class="cell-item">
                <div class="cell-left">开户银行</div>
                <div class="cell-right"><?php echo (reload_bank($_seller["mch_bank_list"])); ?></div>
            </div>
            <div class="cell-item">
                <div class="cell-left">银行卡号</div>
                <div class="cell-right"><?php echo ($_seller["mch_bank_cid"]); ?></div>
            </div>
            <div class="cell-item">
                <div class="cell-left">开户名称</div>
                <div class="cell-right"><?php echo ($_seller["mch_bank_name"]); ?></div>
            </div>
            <div class="cell-item">
                <div class="cell-left">开户省份</div>
                <div class="cell-right"><?php echo ($_seller["mch_bank_provice"]); ?></div>
            </div>
            <div class="cell-item">
                <div class="cell-left">开户城市</div>
                <div class="cell-right"><?php echo ($_seller["mch_bank_citys"]); ?></div>
            </div>
            <div class="cell-item">
                <div class="cell-left">开户支行</div>
                <div class="cell-right"><?php echo (reload_banks($_seller["mch_linkbnk"])); ?></div>
            </div>
        </div>

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