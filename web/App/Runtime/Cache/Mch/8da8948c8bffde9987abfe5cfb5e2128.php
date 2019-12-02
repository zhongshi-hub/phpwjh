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
        <a onclick="call_back();" class="navbar-item"><i class="back-ico"></i></a>
        <div class="navbar-center"><span class="navbar-title">门店列表</span></div>
    </header>
    <div class="g-scrollview" style="margin-bottom: 50px;">
        <?php if(is_array($list)): foreach($list as $key=>$v): ?><div class="mui-table-view" style="margin-top: 10px">
                <div class="mui-card-header">
                    <?php echo ($v["name"]); ?>
                    <a class="mui-card-link">
                        <?php if($v["status"] == 1): ?><span class="mui-badge mui-badge-success">正常</span>
                        <?php else: ?>
                            <span class="mui-badge">禁止</span><?php endif; ?>
                    </a>
                </div>
                <div class="mui-card-content">
                    <div class="mui-card-content-inner" style="padding-top: 5px;padding-bottom: 5px">
                        <p style="font-size: 16px">负责人: <?php echo ($v["per_name"]); ?></p>
                        <p style="font-size: 16px;padding-top: 5px">联系电话: <?php echo ($v["per_phone"]); ?></p>
                    </div>
                </div>
                <div class="mui-card-footer">
                    <a class="mui-card-link" href="<?php echo U('store_user',array('id'=>$v['sid'],'store_id'=>$v['id']));?>" target="_self"><span class="mui-badge mui-badge-primary" style="padding:5px 10px;border-radius: 5px">店员管理</span> </a>
                    <a class="mui-card-link" onclick="Open_Codes('<?php $code=GetStoreCode($v['id']);echo sc_codes($code); ?>')" ><i class="icon iconfont" style="font-size:25px;color: #229cff">&#xe6c1;</i></a>
                </div>
            </div><?php endforeach; endif; ?>

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
<script src="/Source/fancybox/jquery.fancybox.js"></script>
<link rel="stylesheet" href="/Source/fancybox/jquery.fancybox.css"/>
<script src="/Source/ydui/js/ydui.js"></script>
<script src="/Source/ydui/js/ydui.flexible.js"></script>
<style>
    .toast-content{color: #fff!important;}
</style>
<script>
    function Open_Codes(url) {
        if(url) {
            $.fancybox.open(url);
        }else{
            YDUI.dialog.toast('当前门店未绑定收款码', 'none');
        }
    }
    function call_back() {
        location.href=document.referrer;
    }
</script>
</body>
</html>