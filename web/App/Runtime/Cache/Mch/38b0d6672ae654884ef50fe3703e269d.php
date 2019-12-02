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
    <link rel="stylesheet" href="/Source/mui/css/mch.css"/>
    <script src="/Source/ydui/js/ydui.flexible.js"></script>
    <script src="/Source/ydui/js/ydui.js"></script>
    <link rel="stylesheet" href="/Source/iconfont/iconfont.css"/>
    <script src="/Source/layui/layui.js" charset="utf-8"></script>
    <style>
        .m-grids-2 .grids-item:not(:nth-child(2n)):before{border-right: 0px solid #75D7F7;}
        .grids-item:after{border-bottom:0}
        .grids-txt{color: #ffffff}
        .tabbar-item.tabbar-active{color: #108ee9}
    </style>
</head>
<body>
<section class="g-flexview">
    <div class="g-scrollview" style="margin-bottom: 50px;margin-top: 0px">
        <div class="m-grids-2" style="background-color:#108ee9;color: #fff;height: 150px;">
            <div style="float: right;margin: 10px"><a class="btn btn-primary" href="<?php echo U('mch_data_seach');?>">筛选</a></div>
            <section class="grids-item" style="width:48%;margin-left:5px;margin-right:7px;margin-top: 55px;font-size: 16px; border: 1px solid rgba(255,255,255,.2);background-color: rgba(255,255,255,.1);">
                <div class="grids-icon" id="order_sum">
                </div>
                <div class="grids-txt">总商户数</div>
            </section>
            <section class="grids-item" style="width:48%;font-size: 16px;border: 1px solid rgba(255,255,255,.2);background-color: rgba(255,255,255,.1);">
                <div class="grids-icon" id="order_count">
                </div>
                <div class="grids-txt">活跃商户</div>
            </section>
        </div>
        <div style="color: red;text-align: center;width: 100%">商户门店及店员更多配置,请联系商户登录商户端操作</div>
    </div>
    <footer class="m-tabbar tabbar-fixed">
        <a href="<?php echo U('Agent/index');?>" class="tabbar-item">
                <span class="tabbar-icon">
                    <i class="icon-home-outline"></i>
                </span>
            <span class="tabbar-txt">主页</span>
        </a>
        <a href="<?php echo U('Agent/mch_data');?>" class="tabbar-item  tabbar-active">
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
        <a href="<?php echo U('Agent/my');?>" class="tabbar-item">
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
<link rel="stylesheet" href="/Source/layui/css/xun_push.css" media="all">
<style>
    .toast-content{color: #fff!important;}
</style>
<script>
    var url = window.location.search;
    layui.use('flow', function () {
        var flow = layui.flow;
        flow.load({
            elem: '.g-scrollview' //流加载容器
            ,mb: '50' //滚动条所在元素，一般不用填，此处只是演示需要。
            ,isLazyimg:false
            , done: function (page, next) { //执行下一页的回调
                var ajax_data = {pages: page};
                var actionurl = '<?php echo U("mch_data_json",array("Debug"=>1));?>'+url;
                $.get(actionurl, ajax_data, function (res) {
                    var lis = [];
                    layui.each(res.data, function (index, rel) {
                        lis.push(' <div class="mui-table-view" style="margin-top: 10px">' +
                                '<div class="mui-card-header">'+rel.mch_name+'<a class="mui-card-link"><span class="mui-badge mui-badge-success">'+rel.status+'</span></a></div>' +
                                '<div class="mui-card-content"><div class="mui-card-content-inner" style="padding-top: 5px;padding-bottom: 5px">' +
                                '<p style="font-size: 14px">负责人: '+rel.card_name+'</p> ' +
                                '<p style="font-size: 14px;padding-top: 5px" onclick="location.href=\'tel:'+rel.mch_tel+'\'">联系电话: '+rel.mch_tel+'</p>' +
                                '<p style="font-size: 14px;padding-top: 5px">注册时间: '+rel.ctime+'</p> ' +
                                '</div></div><div class="mui-card-footer">' +
                                '<a class="mui-card-link">代理:'+rel.aid+'</a><a class="mui-card-link">' +
                                //'<span class="mui-badge mui-badge-primary" onclick="store_list('+rel.id+')" style="background-color:'+rel.auth_color+';padding:5px 10px;border-radius: 5px">'+rel.auth_status+'</span>' +
                                '</a> </div></div>'
                        );
                    });
                    $('#order_sum').html(res.sum);
                    $('#order_count').html(res.count);
                    next(lis.join(''), page < res.pages);
                }, 'json');

            }
        });
    });

    function store_list(id) {
       /* window.location.href = '/Mch/Agent/store_list/mid/' + id;*/
        YDUI.dialog.toast('敬请期待!', 'none');
    }
</script>
</body>
</html>