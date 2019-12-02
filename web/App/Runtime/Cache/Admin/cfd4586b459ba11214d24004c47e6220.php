<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title;?>-移动支付管理平台</title>
    <link rel="shortcut icon"  href="<?php echo GetPico();?>" />
    <link href="/Ext?g=css" rel="stylesheet">
    <link  href="/Source/statics/css/themes/type-b/theme-light.min.css" rel="stylesheet">
    <script type="text/javascript" src="/Source/statics/plugins/pace/pace.min.js"></script>
    <script type="text/javascript" src="/Source/statics/js/jquery-2.2.4.min.js"></script>
    <script type="text/javascript" src="/Source/statics/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="/Source/statics/js/nifty.min.js"></script>
    <script type="text/javascript" src="/Source/artDialog/jquery.artDialog.js?skin=default"></script>
    <script type="text/javascript" src="/Source/layer/layer.js"></script>
    <script type="text/javascript" src="/Source/statics/plugins/bootbox/bootbox.min.js"></script>
    <script src="/Ext?g=default_js"></script>
</head>

<!--全局 CCL-->
<body>
<div id="container" class="effect aside-float aside-bright mainnav-lg mainnav-fixed navbar-fixed">
    <!--头部开始-->
    <header id="navbar">
        <div id="navbar-container" class="boxed">
            <div class="navbar-header">
                <a href="<?php echo U('Admin/Index/index');?>" class="navbar-brand">
                    <img src="/Source/statics/img/logo.png" alt="控制台" class="brand-icon">
                    <div class="brand-title">
                        <span class="brand-text">后台管理</span>
                    </div>
                </a>
            </div>
            <div class="navbar-content clearfix">
                <ul class="nav navbar-top-links pull-left">
                    <li class="tgl-menu-btn">
                        <a class="mainnav-toggle" href="#">
                            <i class="demo-pli-view-list"></i>
                        </a>
                    </li>
                    <!--提醒事项-->
                    <li class="dropdown">
                        <a href="#" data-toggle="dropdown" class="dropdown-toggle">
                            <i class="demo-pli-bell"></i>
                            <span class="badge badge-header badge-danger"></span>
                        </a>
                        <!--事项详情-->
                        <div class="dropdown-menu dropdown-menu-md">
                            <div class="pad-all bord-btm">
                                <p class="text-semibold text-main mar-no">You have 9 notifications.</p>
                            </div>
                            <div class="nano scrollable">
                                <div class="nano-content">
                                    <ul class="head-list">
                                        <li>
                                            <a href="#">
                                                <div class="clearfix">
                                                    <p class="pull-left">Database Repair</p>
                                                    <p class="pull-right">70%</p>
                                                </div>
                                                <div class="progress progress-sm">
                                                    <div style="width: 70%;" class="progress-bar">
                                                        <span class="sr-only">70% Complete</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>

                                        <!-- Dropdown list-->
                                        <li>
                                            <a href="#">
                                                <div class="clearfix">
                                                    <p class="pull-left">Upgrade Progress</p>
                                                    <p class="pull-right">10%</p>
                                                </div>
                                                <div class="progress progress-sm">
                                                    <div style="width: 10%;" class="progress-bar progress-bar-warning">
                                                        <span class="sr-only">10% Complete</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!--底部显示-->
                            <div class="pad-all bord-top">
                                <a href="#" class="btn-link text-dark box-block">
                                    <i class="fa fa-angle-right fa-lg pull-right"></i>显示所有提醒事项
                                </a>
                            </div>
                        </div>
                    </li>
                </ul>
                <ul class="nav navbar-top-links pull-right">
                    <li id="dropdown-user" class="dropdown">

                        <a href="JavaScript:password()" class="dropdown-toggle" title="修改密码">
                            <i class="fa fa-th"></i>
                            <span class="badge badge-header badge-danger"></span>
                        </a>
                        <a href="#" data-toggle="dropdown" class="dropdown-toggle text-right">
                                <span class="pull-right">
                                    <i class="demo-pli-male ic-user"></i>
                                </span>
                            <div class="username hidden-xs"><h4 class="text-main"><p class="text-pink"><?php echo ($_SESSION['user']['name']); ?></p></h4></div>
                        </a>
                        <div class="dropdown-menu  dropdown-menu-right panel-default">

                            <div class="pad-all text-right">
                                <a href="<?php echo U('Admin/Login/out');?>" class="btn btn-primary">
                                    <i class="demo-pli-unlock"></i>退出登录
                                </a>
                            </div>
                        </div>
                    </li>

                </ul>
            </div>
        </div>
    </header>
    <!--头部结束-->

    <div class="boxed">


        <!--MAIN NAVIGATION-->
        <!--===================================================-->
        <nav id="mainnav-container">
            <div id="mainnav">

                <!--Menu-->
                <!--================================-->
                <div id="mainnav-menu-wrap">
                    <div class="nano">
                        <div class="nano-content">
                            <div id="mainnav-profile" class="mainnav-profile">

                                <div class="profile-wrap" style="text-align: center">
                                    <div class="pad-btm">
                                        <img class="img-border" src="<?php echo ($WxData["qrc_img"]); ?>" style="border-radius: 10%;width: 55px;height: 55px;">
                                    </div>
                                    <p class="mnp-name"><?php echo ($WxData["name"]); ?></p>
                                    <a href="#profile-nav" class="box-block collapsed" data-toggle="collapse" aria-expanded="false" style="text-align: left;margin-top: 20px">
                                       <span class="pull-right dropdown-toggle">
                                                <i class="dropdown-caret"></i>
                                        </span>
                                        <span class="mnp-desc">切换公众号</span>
                                    </a>
                                </div>
                                <div id="profile-nav" class="list-group bg-trans collapse" aria-expanded="false" style="height: 2px;">
                                    <?php if(is_array($WxList)): foreach($WxList as $key=>$v): ?><a href="<?php echo U('',array('token'=>$v['token']));?>" class="list-group-item">
                                        <i class="fa  fa-weixin" style="width:20px;text-align: center;font-size: 13px"></i> <?php echo ($v["name"]); ?>
                                    </a><?php endforeach; endif; ?>
                                </div>
                            </div>

                            <ul id="mainnav-menu" class="list-group">
                                <li>
                                    <a href="<?php echo U('Index/index');?>" onclick="if(confirm('确定返回主控制台吗?')==false)return false;">
                                        <i class="fa fa-mail-reply-all" style="width:20px;text-align: center;font-size: 14px"> </i>
                                        <span class="menu-title"><strong>返回主控制台</strong></span>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo U('WeiXin/index',array('token'=>$_GET['token']));?>">
                                        <i class="fa fa-desktop" style="width:20px;text-align: center;font-size: 14px"> </i>
                                        <span class="menu-title"><strong>微信控制台</strong></span>
                                    </a>
                                </li>
                                <!--菜单-->
                                <?php if(is_array($WxMeun)): $k = 0; $__LIST__ = $WxMeun;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?><li <?php if(($rule_name_s) == $vo['name']): ?>class="active-sub active"<?php endif; ?>>
                                    <a href="<?php if(empty($vo['_data'])): echo U($vo['name'],array('token'=>$_GET['token'])); else: ?>#<?php endif; ?>">
                                    <i class="fa <?php echo $vo['icon'];?>" style="width:20px;text-align: center;font-size: 13px"> </i>
                                    <span class="menu-title"><strong><?php echo $vo['title'];?></strong></span>
                                    <?php if(!empty($vo['_data'])): ?><i class="arrow"></i><?php endif; ?>
                                    </a>
                                    <?php if(!empty($vo['_data'])): ?><!--子菜单-->
                                        <ul class="collapse">
                                            <?php if(is_array($vo['_data'])): $i = 0; $__LIST__ = $vo['_data'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub): $mod = ($i % 2 );++$i;?><li <?php if(($rule_name) == $sub['name']): ?>class="active-link"<?php endif; ?>><a href="<?php echo U($sub['name'],array('token'=>$_GET['token']));?>"><?php echo $sub['title'];?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
                                        </ul><?php endif; ?>
                                    </li><?php endforeach; endif; else: echo "" ;endif; ?>
                            </ul>

                        </div>
                    </div>
                </div>
            </div>
        </nav>
        <!--头部信息结束-->
<!--marquee 公告-->
<script type="text/javascript" src="/Source/jquery.marquee.js"></script>
<script type="text/javascript">
    $(function(){
        $("#marquee").marquee({
            yScroll: "bottom"
            , showSpeed: 1000        // 初始下拉速度
            , scrollSpeed: 15       // 滚动速度
            , pauseSpeed: 5000      // 滚动完到下一条的间隔时间
            , pauseOnHover: true    // 鼠标滑向文字时是否停止滚动
        });
    });
</script>
<style type="text/css">
    ul.marquee {display: block;position: relative;overflow: hidden;width: 100%; height: 25px; color: #758697;}
    ul.marquee li {background-color:#ffffff!important;cursor:pointer;position: absolute; top: -999em;left: 0;display: block; white-space: nowrap; padding: 3px 5px;}
</style>
<div id="content-container">
    <div id="page-content">
        <div class="panel">
            <div class="panel-heading">
                <div class="panel-control">
                    <span class="text-muted">

                    </span>
                    <span class="text-muted"><small>(间隔10分钟更新一次数据)最后更新时间:<?php echo ($Time); ?></small></span>
                </div>
                <h3 class="panel-title">数据控制台</h3>

            </div>


            <div class="panel-body">
                <div class="row" style="border: 1px solid #e7ecf3;border-radius: 10px;padding: 2px 5px;">
                    <div class="col-lg-11">
                        <ul class="mail-list marquee" id="marquee" style="margin-bottom: 1px">
                            <?php if(is_array($data)): foreach($data as $key=>$v): ?><li class="mail-starred" onclick="detail(<?php echo ($v["id"]); ?>)">
                                    <div class="mail-star"><i class="fa fa-volume-up" style="color: #ffa100"></i></div>
                                    <div class="mail-from"><?php echo ($v["type"]); ?></div>
                                    <div class="mail-time" style="width: 150px;font-size: 14px"><?php echo ($v["ctime"]); ?></div>
                                    <div class="mail-subject"><?php echo ($v["title"]); ?></div>
                                </li><?php endforeach; endif; ?>
                        </ul>
                    </div>
                    <div class="col-lg-1" style="float: right;text-align: right;right: 20px;line-height: 25px">
                        <a href="<?php echo U('Message/index');?>">查看更多</a>
                    </div>
                </div>

                <div class="row text-center" style="margin-top: 10px;">
                    <div class="col-lg-12">
                        <div class="panel">
                            <div class="panel-body">
                                <div class="col-xs-2" style="width: 12.5%">
                                    <p class="h2 text-thin mar-no pad-all add-tooltip" data-toggle="tooltip"
                                       data-container="body" data-placement="top" data-original-title="¥<?php echo ($Day["sum"]); ?>">
                                        <?php echo (show_total($Day["sum"])); ?></p>
                                    <small class="text-lg">今日交易总额</small>
                                </div>
                                <div class="col-xs-2" style="width: 12.5%;border-left: 1px solid rgba(0, 0, 0, 0.05);">
                                    <p class="h2 text-thin mar-no pad-all"><?php echo ((isset($Day["count"]) && ($Day["count"] !== ""))?($Day["count"]):"0"); ?></p>
                                    <small class="text-lg">今日交易笔数</small>
                                </div>
                                <div class="col-xs-2" style="width: 12.5%;border-left: 1px solid rgba(0, 0, 0, 0.05);">
                                    <p class="h2 text-thin mar-no pad-all add-tooltip" data-toggle="tooltip"
                                       data-container="body" data-placement="top" data-original-title="¥<?php echo ($Day["wxsum"]); ?>">
                                        <?php echo (show_total($Day["wxsum"])); ?></p>
                                    <small class="text-lg">今日微信总额</small>
                                </div>
                                <div class="col-xs-2" style="width: 12.5%;border-left: 1px solid rgba(0, 0, 0, 0.05);">
                                    <p class="h2  text-thin mar-no pad-all"><?php echo ((isset($Day["wxcount"]) && ($Day["wxcount"] !== ""))?($Day["wxcount"]):"0"); ?></p>
                                    <small class="text-lg">今日微信笔数</small>
                                </div>
                                <div class="col-xs-2" style="width: 12.5%;border-left: 1px solid rgba(0, 0, 0, 0.05);">
                                    <p class="h2 text-thin mar-no pad-all add-tooltip" data-toggle="tooltip"
                                       data-container="body" data-placement="top" data-original-title="¥<?php echo ($Day["alisum"]); ?>">
                                        <?php echo (show_total($Day["alisum"])); ?></p>
                                    <small class="text-lg">今日支付宝总额</small>
                                </div>
                                <div class="col-xs-2" style="width: 12.5%;border-left: 1px solid rgba(0, 0, 0, 0.05);">
                                    <p class="h2  text-thin mar-no pad-all"><?php echo ((isset($Day["alicount"]) && ($Day["alicount"] !== ""))?($Day["alicount"]):"0"); ?></p>
                                    <small class="text-lg">今日支付宝笔数</small>
                                </div>
                                <div class="col-xs-2" style="width: 12.5%;border-left: 1px solid rgba(0, 0, 0, 0.05);">
                                    <p class="h2 text-thin mar-no pad-all add-tooltip" data-toggle="tooltip"
                                       data-container="body" data-placement="top" data-original-title="¥<?php echo ($Day["card_sum"]); ?>">
                                        <?php echo (show_total($Day["card_sum"])); ?></p>
                                    <small class="text-lg">今日快捷总额</small>
                                </div>
                                <div class="col-xs-2" style="width: 12.5%;border-left: 1px solid rgba(0, 0, 0, 0.05);">
                                    <p class="h2  text-thin mar-no pad-all"><?php echo ((isset($Day["card_count"]) && ($Day["card_count"] !== ""))?($Day["card_count"]):"0"); ?></p>
                                    <small class="text-lg">今日快捷笔数</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row text-center">
                    <div class="col-sm-2" style="width: 12.5%">
                        <div class="panel media middle">
                            <div class="media-left bg-info pad-all" style="padding: 10px;">
                                <i class="icon-3x iconfont" style="font-size: 3em;">&#xe636;</i>
                            </div>
                            <div class="media-body pad-all" style="padding: 10px;">
                                <p class="h3  text-thin mar-no  add-tooltip" data-toggle="tooltip"
                                   data-container="body" data-placement="top" data-original-title="¥<?php echo ($Go["sum"]); ?>">
                                    <?php echo ($Go["sum"]); ?></p>
                                <p class="text-muted mar-no" style="font-size:13px">昨日交易总额</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2" style="width: 12.5%">
                        <div class="panel media middle">
                            <div class="media-left bg-info pad-all" style="padding: 10px;">
                                <i class="icon-3x iconfont" style="font-size: 3em;">&#xe68e;</i>
                            </div>
                            <div class="media-body pad-all" style="padding: 10px;">
                                <p class="h3  text-thin mar-no"><?php echo ((isset($Go["count"]) && ($Go["count"] !== ""))?($Go["count"]):"0"); ?></p>
                                <p class="text-muted mar-no" style="font-size:13px">昨日交易笔数</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2" style="width: 12.5%">
                        <div class="panel media middle">
                            <div class="media-left bg-info pad-all" style="padding: 10px;">
                                <i class="icon-3x iconfont" style="font-size: 3em;">&#xe611;</i>
                            </div>
                            <div class="media-body pad-all" style="padding: 10px;">
                                <p class="h3  text-thin mar-no add-tooltip" data-toggle="tooltip"
                                   data-container="body" data-placement="top" data-original-title="¥<?php echo ($Go["wxsum"]); ?>">
                                    <?php echo ($Go["wxsum"]); ?></p>
                                <p class="text-muted mar-no" style="font-size:13px">昨日微信总额</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2" style="width: 12.5%">
                        <div class="panel media middle">
                            <div class="media-left bg-info pad-all" style="padding: 10px;">
                                <i class="icon-3x iconfont" style="font-size: 3em;">&#xe66f;</i>
                            </div>
                            <div class="media-body pad-all" style="padding: 10px;">
                                <p class="h3  text-thin mar-no"><?php echo ((isset($Go["wxcount"]) && ($Go["wxcount"] !== ""))?($Go["wxcount"]):"0"); ?></p>
                                <p class="text-muted mar-no" style="font-size:13px">昨日微信笔数</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2" style="width: 12.5%">
                        <div class="panel media middle">
                            <div class="media-left bg-info pad-all" style="padding: 10px;">
                                <i class="icon-3x iconfont" style="font-size: 3em;">&#xe728;</i>
                            </div>
                            <div class="media-body pad-all" style="padding: 10px;">
                                <p class="h3  text-thin mar-no add-tooltip" data-toggle="tooltip"
                                   data-container="body" data-placement="top" data-original-title="¥<?php echo ($Go["alisum"]); ?>">
                                    <?php echo ($Go["alisum"]); ?></p>
                                <p class="text-muted mar-no" style="font-size:13px">昨日支付宝总额</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2" style="width: 12.5%">
                        <div class="panel media middle" >
                            <div class="media-left bg-info pad-all" style="padding: 10px;">
                                <i class="icon-3x iconfont" style="font-size: 3em;">&#xe8d1;</i>
                            </div>
                            <div class="media-body pad-all" style="padding: 10px;">
                                <p class="h3  text-thin mar-no"><?php echo ((isset($Go["alicount"]) && ($Go["alicount"] !== ""))?($Go["alicount"]):"0"); ?></p>
                                <p class="text-muted mar-no" style="font-size:13px">昨日支付宝笔数</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2" style="width: 12.5%">
                        <div class="panel media middle">
                            <div class="media-left bg-info pad-all" style="padding: 10px;">
                                <i class="icon-3x iconfont" style="font-size: 3em;">&#xe606;</i>
                            </div>
                            <div class="media-body pad-all" style="padding: 10px;">
                                <p class="h3  text-thin mar-no add-tooltip" data-toggle="tooltip"
                                   data-container="body" data-placement="top" data-original-title="¥<?php echo ($Go["card_sum"]); ?>">
                                    <?php echo ($Go["card_sum"]); ?></p>
                                <p class="text-muted mar-no" style="font-size:13px">昨日快捷总额</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2" style="width: 12.5%">
                        <div class="panel media middle" >
                            <div class="media-left bg-info pad-all" style="padding: 10px;">
                                <i class="icon-3x iconfont" style="font-size: 3em;">&#xe618;</i>
                            </div>
                            <div class="media-body pad-all" style="padding: 10px;">
                                <p class="h3  text-thin mar-no"><?php echo ((isset($Go["card_count"]) && ($Go["card_count"] !== ""))?($Go["card_count"]):"0"); ?></p>
                                <p class="text-muted mar-no" style="font-size:13px">昨日快捷笔数</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6">
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">数据汇总概览</h3>
                            </div>
                            <div class="panel-body">
                                <div id="order_allchart" style="height:350px"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">商户分析</h3>
                            </div>
                            <div class="panel-body">
                                <div id="order_mchchart" style="height:350px"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">七日交易分析</h3>
                            </div>
                            <div class="panel-body">
                                <div id="order_chart" style="height:500px"></div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>
<style>
    .col-lg-2 {
        width: 20% !important;
    }

</style>
<link href="/Source/iconfont/manage/iconfont.css" rel="stylesheet">
<script src="/Source/plug/echarts.common.min.js"></script>
<script>
    $(document).ready(function () {

        //商户分析
        var MchmyChart = echarts.init(document.getElementById('order_mchchart'));
        var Mch_option = {
            tooltip: {
                trigger: 'item',
                formatter: "{a} <br/>{b}: {c} ({d}%)"
            },
            legend: {
                orient: 'vertical',
                x: 'left',
                data: ['活跃商户数', '沉睡商户数']
            },
            toolbox: {
                show: true,
                feature: {
                    saveAsImage: {show: true}
                }
            },
            series: [
                {
                    name: '汇总统计',
                    type: 'pie',
                    radius: ['50%', '70%'],
                    avoidLabelOverlap: false,
                    label: {
                        normal: {
                            show: false,
                            position: 'center'
                        },
                        emphasis: {
                            show: true,
                            textStyle: {
                                fontSize: '30',
                                fontWeight: 'bold'
                            }
                        }
                    },
                    labelLine: {
                        normal: {
                            show: false
                        }
                    },
                    data: [
                        {value: '<?php echo ($Mch["live"]); ?>', name: '活跃商户数'},
                        {value: '<?php echo ($Mch["bed"]); ?>', name: '沉睡商户数'}
                    ]
                }
            ]
        };
        MchmyChart.setOption(Mch_option);

        //汇总
        var AllmyChart = echarts.init(document.getElementById('order_allchart'));
        var all_option = {

            tooltip: {
                trigger: 'item',
                formatter: "{a} <br/>{b} : {c} ({d}%)"
            },
            legend: {
                orient: 'vertical',
                left: 'left',
                data: ['总交易额', '总笔数', '总商户数', '微信总额', '微信总笔数', '支付宝总额', '支付宝总笔数','快捷总额','快捷总笔数']
            },
            toolbox: {
                show: true,
                feature: {
                    saveAsImage: {show: true}
                }
            },
            series: [
                {
                    name: '数据汇总',
                    type: 'pie',
                    radius: '55%',
                    center: ['50%', '60%'],
                    data: [
                        {value: '<?php echo ($To["sum"]); ?>', name: '总交易额'},
                        {value: '<?php echo ($To["count"]); ?>', name: '总笔数'},
                        {value: '<?php echo ($To["mch"]); ?>', name: '总商户数'},
                        {value: '<?php echo ($To["wxsum"]); ?>', name: '微信总额'},
                        {value: '<?php echo ($To["wxcount"]); ?>', name: '微信总笔数'},
                        {value: '<?php echo ($To["alisum"]); ?>', name: '支付宝总额'},
                        {value: '<?php echo ($To["alicount"]); ?>', name: '支付宝总笔数'},
                        {value: '<?php echo ($To["card_sum"]); ?>', name: '快捷总额'},
                        {value: '<?php echo ($To["card_count"]); ?>', name: '快捷总笔数'}
                    ],
                    itemStyle: {
                        emphasis: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(0, 0, 0, 0.5)'
                        }
                    }
                }
            ]

        };
        AllmyChart.setOption(all_option);
        var myChart = echarts.init(document.getElementById('order_chart'));
        var day = [<?php echo ($Mon["day"]); ?>],
        sum = [<?php echo ($Mon["sum"]); ?>],
        count = [<?php echo ($Mon["count"]); ?>],
        wxsum = [<?php echo ($Mon["wxsum"]); ?>],
        card_sum = [<?php echo ($Mon["card_sum"]); ?>],
        wxcount = [<?php echo ($Mon["wxcount"]); ?>],
        alisum = [<?php echo ($Mon["alisum"]); ?>],
        alicount = [<?php echo ($Mon["alicount"]); ?>],
        card_count = [<?php echo ($Mon["card_count"]); ?>],
        mch = [<?php echo ($Mon["mch"]); ?>]
        var option = {
            tooltip: {
                trigger: 'axis',
                axisPointer: {            // 坐标轴指示器，坐标轴触发有效
                    type: 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
                }
            },
            legend: {
                data: ['交易汇总笔数', '微信支付笔数', '支付宝支付笔数','快捷支付笔数', '交易汇总金额', '微信支付总额', '支付宝支付总额','快捷支付总额','商户数']
            },
            toolbox: {
                show: true,
                feature: {
                    dataView: {show: false, readOnly: false},
                    magicType: {show: true, type: ['line', 'bar']},
                    restore: {show: true},
                    saveAsImage: {show: true}
                }
            },
            grid: {
                left: '0%',
                right: '0%',
                bottom: '3%',
                containLabel: true
            },
            xAxis: [
                {
                    type: 'category',
                    data: day
                }
            ],
            yAxis: [
                {
                    type: 'value'
                }
            ],
            series: [
                {
                    name: '交易汇总笔数',
                    type: 'bar',
                    data: count
                },
                {
                    name: '微信支付笔数',
                    type: 'bar',
                    data: wxcount
                },
                {
                    name: '支付宝支付笔数',
                    type: 'bar',
                    stack: '广告',
                    data: alicount
                },
                {
                    name: '快捷支付笔数',
                    type: 'bar',
                    stack: '快捷支付笔数',
                    data: card_count
                },
                {
                    name: '交易汇总金额',
                    type: 'bar',
                    stack: '交易汇总金额',
                    data: sum,
                    markLine: {
                        lineStyle: {
                            normal: {
                                type: 'dashed'
                            }
                        },
                        data: [
                            [{type: 'min'}, {type: 'max'}]
                        ]
                    }
                },
                {
                    name: '微信支付总额',
                    type: 'bar',
                    data: wxsum,
                    markLine: {
                        lineStyle: {
                            normal: {
                                type: 'dashed'
                            }
                        },
                        data: [
                            [{type: 'min'}, {type: 'max'}]
                        ]
                    }

                },
                {
                    name: '支付宝支付总额',
                    type: 'bar',
                    barWidth: 5,
                    stack: '支付宝支付总额',
                    data: alisum,
                    markLine: {
                        lineStyle: {
                            normal: {
                                type: 'dashed'
                            }
                        },
                        data: [
                            [{type: 'min'}, {type: 'max'}]
                        ]
                    }
                },
                {
                    name: '快捷支付总额',
                    type: 'bar',
                    barWidth: 5,
                    stack: '快捷支付总额',
                    data: card_sum,
                    markLine: {
                        lineStyle: {
                            normal: {
                                type: 'dashed'
                            }
                        },
                        data: [
                            [{type: 'min'}, {type: 'max'}]
                        ]
                    }
                },
                {
                    name: '商户数',
                    type: 'bar',
                    stack: '商户数',
                    data: mch
                }

            ]
        };
        myChart.setOption(option);
    });
    function detail(id) {
        window.location.href="<?php echo U('Message/detail');?>/id/"+id;
    }
</script>
</div>
<footer id="footer">
    <div class="hide-fixed pull-right pad-rgt">
        渠道管理后台
    </div>
    <p class="pad-lft">&#0169; <?php echo date('Y');?> </p>
</footer>
<!-- 返回顶部 -->
<button class="scroll-top btn">
    <i class="pci-chevron chevron-up"></i>
</button>
</div>

<!-- 修改密码模态框开始 -->
<div class="modal fade" id="PassWords" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" >
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <i class="pci-cross pci-circle"></i>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    密码修改
                </h4>
            </div>
            <div class="modal-body">
                <div class="modal-body">
                    <form class="form-horizontal" action="<?php echo U('Admin/User/editpass');?>" method="post">
                        <div class="panel-body">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">旧密码</label>
                                <div class="col-sm-9">
                                    <input class="form-control" name="oldpass" type="text" placeholder="请输入旧密码" required="required">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" >新密码</label>
                                <div class="col-sm-9">
                                    <input class="form-control" name="newpass"  type="text" placeholder="请输入新密码" required="required">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">确认新密码</label>
                                <div class="col-sm-9">
                                    <input class="form-control" name="newspass" type="text" placeholder="请再次输入新密码" required="required">
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <button class="btn btn-success" type="submit">确认修改</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- 修改密码模态框结束 -->


<!-- 修改交易识别码模态框开始 -->
<div class="modal fade" id="IfRaMeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width:65%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <i class="pci-cross pci-circle"></i>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    详细信息
                </h4>
            </div>
            <div class="modal-body">
                <iframe height="500px" src="" frameBorder="0" width="100%"></iframe>
            </div>
        </div>
    </div>
</div>

<style>
    .chosen-container-single .chosen-single{
        height: 35px!important;
    }
    .btn{border-radius: 5px !important;}
    select {
        border: solid 1px #e1e5ea;
        appearance:none;
        -moz-appearance:none;
        -webkit-appearance:none;
        background: url("/Source/plug/arrow.jpg") no-repeat scroll right center transparent !important;
        padding-right: 14px;
    }
    select::-ms-expand { display: none; }

    .magic-radio + label::after {
        left: 2.8px!important;
        top: 2.8px!important;
    }
</style>
<script type="text/javascript">
    var upload_mod='<?php echo MODULE_NAME;?>',upload_type='UEdit',upload_ValName='fw_info';

    $('#FileUpload').on('hidden.bs.modal', function () {
        for (var i = 0; i < uploader.getFiles().length; i++) {
            uploader.removeFile(uploader.getFiles()[i]);
        }
        uploader.reset();
        $('#dataUrl').val('');
    });
    $('#MouthTime').datepicker({
        language: "zh-CN",
        todayHighlight: true,
        format: 'yyyy-mm',
        autoclose: true,
        startView: 1,
        maxViewMode:2,
        minViewMode:1
    });
    $('#MouthDay').datepicker({
        language: "zh-CN",
        todayHighlight: true,
        format: 'yyyy-mm-dd',
        autoclose: true,
        //startView: 2,
        maxViewMode:3
        //minViewMode:1
    });

    $('#STime').datepicker({
        format: "yyyy-mm-dd",
        todayBtn: "linked",
        autoclose: true,
        todayHighlight: true
    });


    //结束时间：
    $('#ETime').datepicker({
        format: "yyyy-mm-dd",
        todayBtn: "linked",
        autoclose: true,
        todayHighlight: true
    });
    

    $('#s_time').datetimepicker({lang:'th'});
    $('#e_time').datetimepicker({lang:'th'});
    $('#sd_time,#ed_time').datetimepicker({
        lang:'th',
        timepicker:false,
        format:'Y-m-d',
        formatDate:'Y-m-d'
    });


    
    $(function() {
        $('.OpenUrl').click(function(){
            var frameSrc = $(this).attr("href");
            $('#IfRaMeModal').on('show.bs.modal',function() {
               $('iframe').attr("src",frameSrc);
            });
            $('#IfRaMeModal').modal({show:true});
            return false;
        });


        $('#demo-chosen-select').chosen();
        $('#demo-chosen-select1').chosen();
        $('#demo-chosen-select2').chosen();
        $('#demo-chosen-select3').chosen();
        $('#demo-chosen-select4').chosen();
        $('#demo-chosen-select5').chosen();

        $("form").submit(function (e) {
            //判断当前form是否要ajax
            var not=$(this).attr("ajax");
            if(not != 'n'){
                e.preventDefault(); //阻止自动提交表单
            }
            var ajax_data = $(this).serialize();
            var actionurl = $(this).attr("action");
            $.post(actionurl, ajax_data, function(data){
                if(data.status == 1){
                    /*如果存在模态框将关闭*/
                    $('.modal').map(function() {
                        $(this).modal('hide');
                    });
                    $.niftyNoty({
                        type: 'success',
                        message : '<strong>'+data.info+'</strong> 3秒后自动跳转!',
                        container : 'floating',
                        timer : 3000
                    });
                    setTimeout(function(){
                        window.location.href=data.url
                    }, 3000);
                }
                else{
                    $.niftyNoty({
                        type: 'danger',
                        message : '<strong>'+data.info+'</strong>',
                        container : 'floating',
                        timer : 5000
                    });
                }
            }, 'json');
        });
    });

    function iFrameHeight() {
        var ifm= document.getElementById("iframepage");
        var subWeb = document.frames ? document.frames["iframepage"].document : ifm.contentDocument;
        if(ifm != null && subWeb != null) {
            ifm.height = subWeb.body.scrollHeight;
            ifm.width = subWeb.body.scrollWidth;
        }
    }

    /*上传modal*/
    function upload_modal(domid){
        art.dialog.data('domid', domid);
        layer.open({
            type: 2,
            title:false,
            area: ['600px', '500px'],
            fixed: false, //不固定
            maxmin: false,
            content: "<?php echo U('Plugs/Upload/index/Mod/Admin');?>"
        });
    }
    //输入框图片预览
    function upload_view(data) {
        var image=$('#'+data).val();
        if(image==''){
            $.niftyNoty({
                type: 'danger',
                message : '<strong>无图片信息,无法发起预览</strong>',
                container : 'floating',
                timer : 5000
            });
        }else {
            $('.upload_view').fancybox({
                href: image
            });
        }
    }
    //img标签 图片预览
    function view_img(data) {
        $('img').fancybox({
            href: $('#'+data).attr('src')
        });
    }

    //修改密码
    function password() {
        $('#PassWords').modal({show:true});
    }


</script>
</body>
</html>