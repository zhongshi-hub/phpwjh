<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title;?>-移动支付管理平台</title>
    <link rel="shortcut icon"  href="<?php echo GetPico();?>" />
    <style>
        span{
            -moz-osx-font-smoothing: auto!important;
        }
    </style>
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

                                <div class="profile-wrap">
                                    <div class="pad-btm">
                                        <img class="img-border" src="<?php echo GetPlogo();?>" style="border-radius: 10%;width: 55px;height: 55px;">
                                    </div>
                                </div>
                            </div>

                            <ul id="mainnav-menu" class="list-group">
                                <!--菜单-->

                                <?php if(is_array($menu)): $k = 0; $__LIST__ = $menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?><li <?php if(($rule_name_s) == $vo['name']): ?>class="active-sub active"<?php endif; ?>>
                                    
                                    <a href="<?php if(empty($vo['_data'])): echo U($vo['name']); else: ?>#<?php endif; ?>">
                                        <i class="fa <?php echo $vo['icon'];?>" style="width:20px;text-align: center;font-size: 13px"> </i>
                                        <span class="menu-title"><strong><?php echo $vo['title'];?></strong></span>
                                        <?php if(!empty($vo['_data'])): ?><i class="arrow"></i><?php endif; ?>
                                    </a>
                                    <?php if(!empty($vo['_data'])): ?><!--子菜单-->
                                        <ul class="collapse">
                                        <?php if(is_array($vo['_data'])): $i = 0; $__LIST__ = $vo['_data'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub): $mod = ($i % 2 );++$i;?><li <?php if(($rule_name) == $sub['name']): ?>class="active-link"<?php endif; ?>><a href="<?php echo U($sub['name']);?>"><?php echo $sub['title'];?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
                                        </ul><?php endif; ?>
                                 </li><?php endforeach; endif; else: echo "" ;endif; ?>
                            </ul>

                        </div>
                    </div>
                </div>
            </div>
        </nav>
        <!--头部信息结束-->
<div id="content-container">
    <div id="page-content">
        <div class="panel">
                <div class="panel-body">
                    <p style="border-bottom: solid 1px #E4E4E4;font-size: 16px;padding-bottom: 5px;">商户配置</p>
                    <ul class="list-group">
                        <li class="list-group-item">商户号：<span style="font-size: 15px"><?php echo ($sub_mch_id); ?></span></li>
                        <li class="list-group-item">商户名称:  <span style="font-size: 15px"><?php echo ($mch_name); ?></span></li>
                    </ul>
                    <div class="panel" style="border-radius: 3px;">
                        <div class="panel-heading">
                            <h3 class="panel-title">支付授权目录</h3>
                        </div>
                        <form id="path_form" class="panel-body form-horizontal form-padding" ajax="n">
                            <div class="panel-body">
                                <div class="col-sm-5">
                                    <div class="form-group">
                                        <label class="col-md-3 control-label" for="jsapi_path">授权目录URL</label>
                                        <div class="col-md-9">
                                            <input type="text" name="jsapi_path" id="jsapi_path" class="form-control" value="<?php $port= isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://'; $domain=$port.$_SERVER['HTTP_HOST'].'/Pay/'; echo $domain; ?>" placeholder="" required>
                                            <small class="help-block">API只支持新增配置，不支持修改 每个商户最多配置5个支付目录</small>
                                        </div>
                                    </div>
                                    <p class="text-semibold text-main">已配置授权目录:</p>
                                    <?php if(is_array($jsapi_path_list)): foreach($jsapi_path_list as $key=>$v): ?><p style="border-bottom: 1px solid #e9e9e9;"><?php echo ($v); ?></p><?php endforeach; endif; ?>

                                </div>
                            </div>
                            <div class="panel-footer text-right">
                                <div class="row">
                                    <div class="col-sm-9 col-sm-offset-3">
                                        <input type="hidden" name="api_type" value="path">
                                        <input type="hidden" name="id" value="<?php echo ($id); ?>">
                                        <button class="btn btn-mint" type="button" id="path">提交</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                    <div class="panel" style="border-radius: 3px;">
                        <div class="panel-heading">
                            <h3 class="panel-title">APPID关联</h3>
                        </div>
                        <form id="sub_form" class="panel-body form-horizontal form-padding" ajax="n">
                            <div class="panel-body">
                                <div class="col-sm-5">
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">关联APPID</label>
                                        <div class="col-md-9">
                                            <input type="text" name="sub_appid"  class="form-control" value="" placeholder="" required>
                                            <small class="help-block">可以绑定服务商公司名字相同的公众号、小程序、开放平台应用的APPID;</small>
                                        </div>
                                    </div>
                                    <p class="text-semibold text-main">已配置的关联APPID:</p>
                                    <?php if(is_array($appid_config_list)): foreach($appid_config_list as $key=>$v): ?><p style="border-bottom: 1px solid #e9e9e9;"><?php echo ($v["sub_appid"]); ?></p><?php endforeach; endif; ?>
                                </div>
                            </div>
                            <div class="panel-footer text-right">
                                <div class="row">
                                    <div class="col-sm-9 col-sm-offset-3">
                                        <input type="hidden" name="api_type" value="sub">
                                        <input type="hidden" name="id" value="<?php echo ($id); ?>">
                                        <button class="btn btn-mint" type="button" id="sub">提交</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="panel" style="border-radius: 3px;">
                        <div class="panel-heading">
                            <h3 class="panel-title">关注配置</h3>
                        </div>
                        <form id="appid_form" class="panel-body form-horizontal form-padding" ajax="n">
                        <div class="panel-body">
                            <div class="col-sm-5">
                                    <span>API只支持新增配置，配置后隔30天才能重新修改，所以配置前请确认appid是否正确</span>
                                    <div class="form-group pad-ver">
                                        <label class="col-md-3 control-label">关注类型</label>
                                        <div class="col-md-9">
                                            <div class="radio">
                                                <input id="demo-inline-form-radio" class="magic-radio" type="radio" name="sub_type" value="1" checked="">
                                                <label for="demo-inline-form-radio">公众号</label>
                                                <input id="demo-inline-form-radio-2" class="magic-radio" type="radio" name="sub_type" value="2">
                                                <label for="demo-inline-form-radio-2">小程序</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group" id="subscribe_appid">
                                        <label class="col-md-3 control-label" >推荐关注APPID</label>
                                        <div class="col-md-9">
                                            <input type="text" name="subscribe_appid"  class="form-control" value="<?php echo ($appid); ?>" placeholder="">
                                            <small class="help-block">服务商的公众号APPID</small>
                                        </div>
                                    </div>
                                    <div class="form-group" id="receipt_appid">
                                        <label class="col-md-3 control-label" >推荐小程序APPID</label>
                                        <div class="col-md-9">
                                            <input type="text" name="receipt_appid" class="form-control" placeholder="">
                                            <small class="help-block">需为通过微信认证的小程序appid，且认证主体与服务商主体一致</small>
                                        </div>
                                    </div>
                                <p class="text-semibold text-main">已配置的关注APPID:</p>
                                <?php if(is_array($appid_config_list)): foreach($appid_config_list as $key=>$v): ?><p style="border-bottom: 1px solid #e9e9e9;"><?php echo ($v["subscribe_appid"]); ?></p><?php endforeach; endif; ?>
                            </div>
                        </div>
                        <div class="panel-footer text-right">
                            <div class="row">
                                <div class="col-sm-9 col-sm-offset-3">
                                    <input type="hidden" name="api_type" value="appid">
                                    <input type="hidden" name="id" value="<?php echo ($id); ?>">
                                    <button class="btn btn-mint" type="submit" id="appid">提交</button>
                                </div>
                            </div>
                        </div>
                        </form>
                    </div>

                </div>
        </div>
    </div>
</div>
<script>
    var subscribe_appid=$('#subscribe_appid'),receipt_appid=$('#receipt_appid'),subscribe_appid_name=$("[name='subscribe_appid']"),receipt_appid_name=$("[name='receipt_appid']");
    $('[name="sub_type"]').change(sub_type);
    sub_type();
    function sub_type() {
        var val=$("input[name='sub_type']:checked").val();
        if(val==1){
            subscribe_appid.show();
            receipt_appid.hide();
            subscribe_appid_name.attr('required',true);
            receipt_appid_name.attr('required',false);
        }else{
            subscribe_appid.hide().attr('required',false);
            receipt_appid.show().attr('required',true);
            subscribe_appid_name.attr('required',false);
            receipt_appid_name.attr('required',true);
        }
    }
    $('#path').click(function () {
        loading('支付授权目录配置中...');
        $.post('<?php echo U("mchConfig");?>',  $('#path_form').serialize(), function (data) {
            if (data.status) {
                layer.closeAll();
                layer.alert(data.info, {
                    skin: 'layui-layer-lan'
                    ,title: "请求结果"
                    ,offset: '100px'
                },function (e) {
                    window.location.reload();
                });
            }
            else {
                layer.alert(data.info);
            }
        }, 'json');
    });
    $('#sub').click(function () {
        loading('关联APPID信息配置中...');
        $.post('<?php echo U("mchConfig");?>',  $('#sub_form').serialize(), function (data) {
            if (data.status) {
                layer.closeAll();
                layer.alert(data.info, {
                    skin: 'layui-layer-lan'
                    ,title: "请求结果"
                    ,offset: '100px'
                },function (e) {
                    window.location.reload();
                });
            }
            else {
                layer.alert(data.info);
            }
        }, 'json');
    });
    $('#appid_form').submit(function (e) {
        e.preventDefault();
        loading('推荐关注配置中...');
        $.post('<?php echo U("mchConfig");?>',  $('#appid_form').serialize(), function (data) {
            if (data.status) {
                layer.closeAll();
                layer.alert(data.info, {
                    skin: 'layui-layer-lan'
                    ,title: "请求结果"
                    ,offset: '100px'
                },function (e) {
                    window.location.reload();
                });
            }
            else {
                layer.alert(data.info);
            }
        }, 'json');
    });
    function loading(text) {
        layer.msg(text, {
            icon: 16,
            shade: 0.2,
            time:300000
        });
    }

</script>