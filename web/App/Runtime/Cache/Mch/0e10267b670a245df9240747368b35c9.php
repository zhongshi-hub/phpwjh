<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>代理管理中心</title>
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport"/>
    <meta content="yes" name="apple-mobile-web-app-capable"/>
    <meta content="black" name="apple-mobile-web-app-status-bar-style"/>
    <meta content="telephone=no" name="format-detection"/>
    <link rel="stylesheet" href="/Source/ydui/css/ydui.css"/>
    <link rel="stylesheet" href="/Source/ydui/css/my-app.css"/>
    <script src="/Source/layui/layui.js" charset="utf-8"></script>
    <style>
        .tabbar-item.tabbar-active{color: #108ee9}
    </style>
</head>
<body>
<section class="g-flexview">
    <header class="m-navbar">
        <a onclick="call_back()" class="navbar-item"><i class="back-ico"></i></a>
        <div class="navbar-center"><span class="navbar-title">录入商户</span></div>
    </header>
    <div class="g-scrollview" style="margin-bottom: 50px">
        <aside class="demo-tip">
           选择对应收款码点击进入录入商户
        </aside>
        <article class="m-list list-theme1 data_code" >
        </article>
    </div>
    <footer class="m-tabbar tabbar-fixed">
        <a href="<?php echo U('Agent/index');?>" class="tabbar-item ">
                <span class="tabbar-icon">
                    <i class="icon-home-outline"></i>
                </span>
            <span class="tabbar-txt">主页</span>
        </a>
        <a href="<?php echo U('Agent/mch_data');?>" class="tabbar-item tabbar-active">
                <span class="tabbar-icon">
                    <i class="icon-discover"></i>
                </span>
            <span class="tabbar-txt">商户</span>
        </a>
        <a href="<?php echo U('Agent/qrcode');?>" class="tabbar-item  ">
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
<script src="/Source/ydui/js/ydui.js"></script>
<script src="/Source/ydui/js/ydui.flexible.js"></script>
<link rel="stylesheet" href="/Source/layui/css/xun_push.css" media="all">
<script src="/Source/fancybox/jquery.fancybox.js"></script>
<link rel="stylesheet" href="/Source/fancybox/jquery.fancybox.css"/>
<script>

    var url = window.location.search;
    layui.use('flow', function () {
        var flow = layui.flow;
        flow.load({
            elem: '.g-scrollview .data_code' //流加载容器
            ,mb: '50' //滚动条所在元素，一般不用填，此处只是演示需要。
            ,isLazyimg:false
            , done: function (page, next) { //执行下一页的回调
                var ajax_data = {pages: page};
                var actionurl = '<?php echo U("qrcode_data_json",array("Debug"=>1));?>'+url;
                $.get(actionurl, ajax_data, function (res) {
                    var lis = [];
                    layui.each(res.data, function (index, rel) {
                        lis.push('<a href="javascript:;Open_Codes(\''+rel.codes+'\')" class="list-item" style="margin-bottom: 10px"><div class="list-img" style="padding-bottom: 45%">' +
                            '<img src="'+rel.code_url+'">' +
                            '</div><div class="list-mes">' +
                            '<div class="list-mes-item"><div><span class="list-price"><em>'+rel.codes+'</em></span></div>' +
                            '<div>录入商户</div>' +
                            '</div></div></a>');
                    });
                    next(lis.join(''), page < res.pages);
                }, 'json');

            }
        });
    });

    function call_back() {
        location.href=document.referrer;
    }

    function Open_Codes(url) {
        window.location.href="<?php echo U('mch_code_in');?>/code/"+url;
    }
</script>

</body>
</html>