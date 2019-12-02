<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>会员卡充值</title>
    <link rel="stylesheet" type="text/css" href="/Source/hui/css/hui.css" />
    <style>
        body{background-color: #f7f7f7}
        .card_list img{width: 90%;margin: 5px;}
        .hui-media-list .hui-media-list-img {width: 16%;}
        .hui-media-content {margin-left:10px;width: 45%;padding-top: 5px}
        .hui-media-content p{margin-top: 5px}
        .card_total{width: 39%;text-align: right;font-size: 18px;padding-top: 10px;padding-right: 5px}
        .hui-media-list li{border-radius: 5px}
        .card_total p{color: #e26954;font-size: 13px}
        .card_total p img{width: 16px;margin-bottom: -2px;margin-right: 0;}
    </style>
</head>
<body>

<div class="hui-wrap">
    <div class="hui-media-list card_list" style="padding:10px;">
        <div id="refreshContainer" class="hui-refresh">
            <div class="hui-refresh-icon"></div>
            <div class="hui-refresh-content">
                <ul id="list"></ul>
            </div>
        </div>

        <!--<ul>-->
            <!--<li>-->
                <!--<a href="javascript:hui.toast('会员卡充值详情');">-->
                    <!--<div class="hui-media-list-img"><img src="/Source/amp/member/m_cz_logo.png"/></div>-->
                    <!--<div class="hui-media-content">-->
                        <!--<h1>小孩子玩具店</h1>-->
                        <!--<p><?php echo date('Y-m-d ');?></p>-->
                    <!--</div>-->
                    <!--<div class="card_total">-->
                        <!--+2.55元-->
                    <!--</div>-->
                <!--</a>-->
            <!--</li>-->
            <!--<li>-->
                <!--<a href="javascript:;" data-id="">-->
                    <!--<div class="hui-media-list-img"><img src="/Source/amp/member/m_cz_logo.png"/></div>-->
                    <!--<div class="hui-media-content">-->
                        <!--<h1>小孩子玩具店</h1>-->
                        <!--<p><?php echo date('Y-m-d ');?></p>-->
                    <!--</div>-->
                    <!--<div class="card_total">-->
                        <!--+2.55元-->
                        <!--<p><img src="/Source/amp/member/m_song_logo.png"/> 充值送2.55元</p>-->
                    <!--</div>-->
                <!--</a>-->
            <!--</li>-->
            <!--<li>-->
                <!--<a href="javascript:hui.toast('会员卡充值详情');">-->
                    <!--<div class="hui-media-list-img"><img src="/Source/amp/member/m_cz_logo.png"/></div>-->
                    <!--<div class="hui-media-content">-->
                        <!--<h1>小孩子玩具店</h1>-->
                        <!--<p><?php echo date('Y-m-d ');?></p>-->
                    <!--</div>-->
                    <!--<div class="card_total">-->
                        <!--+5元-->
                        <!--<p><img src="/Source/amp/member/m_song_logo.png"/> 激活送5.00元</p>-->
                    <!--</div>-->
                <!--</a>-->
            <!--</li>-->
        <!--</ul>-->
    </div>
</div>
<script  src="/Source/hui/js/hui.js" type="text/javascript" charset="utf-8"></script>
<script  src="/Source/hui/js/hui-refresh-load-more.js" type="text/javascript"></script>
<script type="text/javascript">
    var page = 1;
    hui.refresh('#refreshContainer', refresh);
    hui.loadMore(getMore);
    //加载更多
    function getMore(){
        hui.get(
            '<?php echo U("getXfList");?>/page/'+page,
            function(res){
                //判断加载完毕
                if(res == 'null'){
                    hui.endLoadMore(true, '已经到头了...');
                    return false;
                }

                for(var i = 0; i < res.length; i++){
                    html += '<li>\n' +
                        '                <a href="javascript:;" data-id="'+res[i]['id']+'">\n' +
                        '                    <div class="hui-media-list-img"><img src="/Source/amp/member/m_cz_logo.png"/></div>\n' +
                        '                    <div class="hui-media-content">\n' +
                        '                        <h1>'+res[i]['store_id']+'</h1>\n' +
                        '                        <p>'+res[i]['create_time']+'</p>\n' +
                        '                    </div>\n' +
                        '                    <div class="card_total">\n' +
                        '                        +'+res[i]['total']+'\n' +
                        '                        <p><img src="/Source/amp/member/m_song_logo.png"/> '+res[i]['desc']+'</p>\n' +
                        '                    </div>\n' +
                        '                </a>\n' +
                        '            </li>';
                    hui('#list').appendTo(html);
                }

                // var data = res.split('--hcSplitor--');
                // for(var i = 0; i < data.length; i++){
                //     var li = document.createElement('li');
                //     li.innerHTML = '<div class="hui-list-text">'+data[i]+'</div>';
                //     hui(li).appendTo('#list');
                // }
                page++;
                hui.endLoadMore();
            }
        );
    }

    //下拉刷新
    function refresh(){
        hui.loading('加载中...');
        hui.getJSON(
            '<?php echo U("getXfList");?>/page/1',
            function(res){
                hui.closeLoading();
                console.log(res.length);
                var html = '';
                for(var i = 0; i < res.length; i++){
                    html += '<li>\n' +
                        '                <a href="javascript:;" data-id="'+res[i]['id']+'">\n' +
                        '                    <div class="hui-media-list-img"><img src="/Source/amp/member/m_cz_logo.png"/></div>\n' +
                        '                    <div class="hui-media-content">\n' +
                        '                        <h1>'+res[i]['store_id']+'</h1>\n' +
                        '                        <p>'+res[i]['create_time']+'</p>\n' +
                        '                    </div>\n' +
                        '                    <div class="card_total">\n' +
                        '                        +'+res[i]['total']+'\n' +
                        '                        <p><img src="/Source/amp/member/m_song_logo.png"/> '+res[i]['desc']+'</p>\n' +
                        '                    </div>\n' +
                        '                </a>\n' +
                        '            </li>';
                }
                page = 2;
                hui('#list').html(html);
                //结束刷新
                hui.endRefresh();
                //重置加载更多状态
                hui.resetLoadMore();
            },
            function(){
                hui.closeLoading();
                hui.upToast('连接服务器失败！');
                hui.endRefresh();
            }
        );
    }
</script>

</body>
</html>