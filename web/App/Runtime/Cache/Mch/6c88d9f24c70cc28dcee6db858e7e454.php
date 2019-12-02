<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>商户中心</title>
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport"/>
    <meta content="yes" name="apple-mobile-web-app-capable"/>
    <meta content="black" name="apple-mobile-web-app-status-bar-style"/>
    <meta content="telephone=no" name="format-detection"/>
    <meta name="full-screen" content="yes">
    <meta name="x5-fullscreen" content="true">
    <link rel="stylesheet" href="/Source/mui/css/mui.min.css">
    <link rel="stylesheet" href="/Source/ydui/css/ydui.css"/>
    <link rel="stylesheet" href="/Source/statics/plugins/font-awesome/css/font-awesome.min.css"/>
    <script src="/Source/ydui/js/ydui.flexible.js"></script>
    <script src="/Source/ydui/js/ydui.js"></script>
    <link rel="stylesheet" href="/Source/iconfont/iconfont.css"/>
    <link rel="stylesheet" href="/Source/layui/css/xun_push.css" media="all">
    <script src="/Source/layui/layui.js" charset="utf-8"></script>
    <style>
        .m-grids-2 .grids-item:not(:nth-child(2n)):before{border-right: 1px solid #75D7F7;}
        .grids-item:after{border-bottom:0}
        .grids-txt{color: #ffffff}
        .m-celltitle:after{border-bottom: 0px solid #D9D9D9;}
    </style>
</head>
<body>
<section class="g-flexview">
    <div class="g-scrollview" style="margin-bottom: 50px;margin-top: 0px">
        <div class="m-grids-2" style="background: #4282e3;color: #fff;height: 150px;">
            <div style="float: right;margin: 10px;height: 50px;width: 100%;text-align: right"><a class="btn btn-primary" href="<?php echo U('order_data');?>" style="background-color: #5593F1;">筛选</a></div>
            <section class="grids-item" style="height: 85px;font-size: 16px; border: 0px solid rgba(255,255,255,.2);background-color: rgba(255,255,255,.1);">
                <div class="grids-txt" style="margin-top: 2px">总交易额</div>
                <div class="grids-icon" id="order_sum">
                </div>
            </section>
            <section class="grids-item" style="height: 85px;font-size: 16px;border: 0px solid rgba(255,255,255,.2);background-color: rgba(255,255,255,.1);">
                <div class="grids-txt">总笔数</div>
                <div class="grids-icon" id="order_count">
                </div>

            </section>
        </div>
    </div>
        <footer class="m-tabbar tabbar-fixed">
            <a href="<?php echo U('Index/index');?>" class="tabbar-item ">
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

    var url = window.location.search;
    layui.use('flow', function () {
        var flow = layui.flow;
        flow.load({
            elem: '.g-scrollview' //流加载容器
            , mb: '230' //滚动条所在元素，一般不用填，此处只是演示需要。
            , done: function (page, next) { //执行下一页的回调
                var ajax_data = {pages: page};
                var actionurl = '<?php echo U("order_data_json");?>'+url;
                $.get(actionurl, ajax_data, function (res) {
                    var lis = [];
                    layui.each(res.data, function (index, rel) {
                        lis.push('<div class="m-celltitle" style="margin-top: 15px;margin-bottom: 2px;">'+rel.day+'<span style="float: right;font-size: 12px;">'+rel.count+'笔/总:'+rel.sum+'元</span></div>');
                        layui.each(rel.data, function (index2, item) {
                            lis.push('<ul class="mui-table-view"><li class="mui-table-view-cell" style="padding-bottom: 8px">' +
                                    '<a href="javascript:;">' +
                                    '<img class="mui-media-object mui-pull-left"  src="' + item.service + '">' +
                                    '<div class="mui-media-body" onclick="trade_data(\''+item.out_trade_no+'\')">' +
                                    '<span style="font-size: 14px">' + item.store_id + '</span>' +
                                    '<span class="mui-pull-right" style="font-size: 14px;border-radius: 6px;">¥' + item.total_fee + '</span>' +
                                    '<p class="mui-ellipsis" style="margin-top: 3px">' +
                                    '<span style="font-size: 12px"> 订单尾号:' + item.out_end + '</span>' +
                                    '<span class="mui-pull-right" style="font-size: 14px;border-radius: 6px;">' + item.createtime + '</span>' +
                                    '</p></div></a></li></ul>'
                            );
                        });
                    });
                    $('#order_sum').html(res.sum);
                    $('#order_count').html(res.count);
                    next(lis.join(''), page < res.pages);
                }, 'json');

            }
        });
    });

    function trade_data(id) {
        window.location.href='/Mch/Index/trade_data/order_id/'+id;
    }

</script>
</body>
</html>