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
    <link rel="stylesheet" href="/Source/iconfont/iconfont.css"/>
</head>
<body>
<section class="g-flexview">
    <header class="m-navbar">
        <a onclick="call_back();" class="navbar-item"><i class="back-ico"></i></a>
        <div class="navbar-center"><span class="navbar-title">店员管理</span></div>
    </header>
    <div class="g-scrollview">
        <div class="m-celltitle" style="margin-top: 15px;">门店名称</div>
        <div class="mui-table-view">
            <div class="mui-card-content">
                <div class="mui-card-content-inner">
                    <?php echo ($name); ?>
                </div>
            </div>
        </div>
        
        <div class="m-button">
            <button  onclick="StoreUserAdd();" type="button" class="btn-block btn-primary">添加店员</button>
        </div>

        <div class="m-celltitle" style="margin-top: 25px;">店员列表</div>
        <div class="mui-table-view" style="margin-bottom: 35px;">
            <ul class="mui-table-view" id="user_list">
                <?php if(is_array($list)): foreach($list as $key=>$v): ?><li class="mui-table-view-cell mui-transitioning">
                        <div class="mui-slider-right mui-disabled">
                            <a class="mui-btn mui-btn-red"   onclick="UserDel(<?php echo ($v["id"]); ?>)" style="transform: translate(0px, 0px);">删除</a>
                        </div>
                        <div class="mui-slider-handle" style="transform: translate(0px, 0px);">
                            <img class="mui-media-object mui-pull-right" src="<?php echo ($v["wx_imgurl"]); ?>">
                            <div class="mui-media-body">
                                <?php echo ($v["username"]); ?>
                                <p class="mui-ellipsis">联系电话:<?php echo ($v["phone"]); ?></p>
                                <p class="mui-ellipsis">微信昵称:<?php echo ($v["wx_name"]); ?> </p>
                                <p class="mui-ellipsis">登录账户:<?php echo ($v["store_id"]); ?>@<?php echo ($v["phone"]); ?> </p>
                            </div>
                        </div>
                    </li><?php endforeach; endif; ?>
            </ul>
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
<script src="/Source/mui/js/mui.min.js"></script>
<script src="/Source/ydui/js/ydui.js"></script>
<script src="/Source/ydui/js/ydui.flexible.js"></script>
<script type="text/javascript" src="/Source/layer/layer.js"></script>
<style>
    .toast-content{color: #fff!important;}
</style>
<script>
   // mui.init();
    function StoreUserAdd() {
        var url="<?php echo ($QrUrl); ?>";
        layer.open({
            type: 1,
            fixed: false, //不固定
            maxmin: false,
            title:false,
            content: "<img src='"+url+"' width='300px' height='300px' style='padding: 10px'/><p style='text-align: center'>将二维码让店员使用微信扫码绑定</p>"
        });
    }

    function UserDel(id) {
        var btnArray = ['确认', '取消'];
        mui.confirm('确认删除该店员吗？', '提示', btnArray, function(e) {
            if (e.index == 0) {
                var ajax_data = {id:id};
                var actionurl = '<?php echo U("store_user_del");?>';
                YDUI.dialog.loading.open('信息提交中...');
                $.post(actionurl, ajax_data, function (data) {
                    YDUI.dialog.loading.close();
                    if (data.status == 1) {
                         YDUI.dialog.toast(data.info, 'none', function () {
                            window.location.reload();
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

            } else {
                setTimeout(function() {
                    $.swipeoutClose(li);
                }, 0);

            }
        });
    }
    function msg(data) {
        YDUI.dialog.toast(data, 'none');
    }

    function call_back() {
        location.href=document.referrer;
    }
</script>
</body>
</html>