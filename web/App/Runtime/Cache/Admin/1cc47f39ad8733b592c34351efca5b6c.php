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
<style>
    .form-control {
        width: 300px !important;
    }

    .imgborder {
        width: 300px;
        border: solid 1px #E4E4E4;
        height: 220px;
        padding: 5px;
    }

    .imgborder img {
        width: 100%;
        display: block;
        height: 100%;
    }
</style>
<div id="content-container">
    <div id="page-content">
        <div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title">商户详情 --(商户名称:<?php echo ($data["mch_name"]); ?>)</h3>
            </div>
            <div class="panel-body">
                <p style="border-bottom: solid 1px #E4E4E4;font-size: 16px;padding-bottom: 5px;">基本信息</p>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">收款码ID</label>
                            <input class="form-control" type="text" value="<?php echo ($data["codes"]); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">所属代理</label>
                            <input class="form-control" type="text" value="<?php echo (agent_name($data["agent_id"])); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">商户名称</label>
                            <input class="form-control" type="text" value="<?php echo ($data["mch_name"]); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">商户类型</label>
                            <input class="form-control" type="text" value="<?php echo ($data["mch_bus_type"]); ?>" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">行业类别</label>
                            <input class="form-control" type="text" value="<?php echo (Industrid($data["mch_industry"])); ?>" readonly>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">省份</label>
                            <input class="form-control" type="email" value="<?php echo ($data["mch_provice"]); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">城市</label>
                            <input class="form-control" type="text" value="<?php echo ($data["mch_citys"]); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">区县</label>
                            <input class="form-control" type="text" value="<?php echo ($data["mch_district"]); ?>" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">地址</label>
                            <input class="form-control" type="text" value="<?php echo ($data["mch_address"]); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">电话</label>
                            <input class="form-control" type="text" value="<?php echo ($data["mch_tel"]); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">负责人</label>
                            <input class="form-control" type="text" value="<?php echo ($data["mch_card_name"]); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">负责人身份证</label>
                            <input class="form-control" type="text" value="<?php echo ($data["mch_card_id"]); ?>" readonly>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">申请时间</label>
                            <input class="form-control" type="text" value="<?php echo (date('Y-m-d H:i:s',$data["ctime"])); ?>"
                                   readonly>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">微信OpenId</label>
                            <input class="form-control" type="text" value="<?php echo ($data["mch_wx_openid"]); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">微信名称</label>
                            <input class="form-control" type="text" value="<?php echo ($data["mch_wx_name"]); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <img src="<?php echo ($data["mch_wx_img"]); ?>" style="width: 50px;">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <?php if(!empty($data["qy_name"])): ?><div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">企业名称</label>
                            <input class="form-control" type="text" value="<?php echo ($data["qy_name"]); ?>"
                                   readonly>
                        </div>
                    </div><?php endif; ?>
                    <?php if(!empty($data["qy_cid"])): ?><div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">工商注册号</label>
                            <input class="form-control" type="text" value="<?php echo ($data["qy_cid"]); ?>" readonly>
                        </div>
                    </div><?php endif; ?>
                    <?php if(!empty($data["qy_time"])): ?><div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">执照有效期</label>
                                <input class="form-control" type="text" value="<?php echo ($data["qy_time"]); ?>"
                                       readonly>
                            </div>
                        </div><?php endif; ?>
                    <?php if(!empty($data["qy_fr_name"])): ?><div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">法人姓名</label>
                                <input class="form-control" type="text" value="<?php echo ($data["qy_fr_name"]); ?>"
                                       readonly>
                            </div>
                        </div><?php endif; ?>

                    <?php if(!empty($data["qy_fr_cid"])): ?><div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">法人身份证号</label>
                                <input class="form-control" type="text" value="<?php echo ($data["qy_fr_cid"]); ?>" readonly>
                            </div>
                        </div><?php endif; ?>
                    <?php if(!empty($data["card_time"])): ?><div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">证件有效期</label>
                                <input class="form-control" type="text" value="<?php echo ($data["card_time"]); ?>"
                                       readonly>
                            </div>
                        </div><?php endif; ?>
                    <?php if(!empty($data["pos_mcc"])): ?><div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">新大陆MCC码</label>
                            <input class="form-control" type="text" value="<?php echo ($data["pos_mcc"]); ?>"
                                   readonly>
                        </div>
                    </div><?php endif; ?>
                </div>
                <p style="border-bottom: solid 1px #E4E4E4;font-size: 16px;padding-bottom: 5px;">结算信息</p>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">开户银行</label>
                            <input class="form-control" type="text" value="<?php echo (reload_bank($data["mch_bank_list"])); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">银行卡号</label>
                            <input class="form-control" type="text" value="<?php echo ($data["mch_bank_cid"]); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">开户名称</label>
                            <input class="form-control" type="text" value="<?php echo ($data["mch_bank_name"]); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">帐户类型</label>
                            <input class="form-control" type="text" value="<?php echo ($data["mch_bank_type"]); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">结算类型</label>
                            <input class="form-control" type="text" value="<?php if(($data["mch_bank_type_s"]) == "1"): ?>非法人结算<?php else: ?>法人结算<?php endif; ?>" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">

                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">开户省份</label>
                            <input class="form-control" type="text" value="<?php echo ($data["mch_bank_provice"]); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group" id="depositCity">
                            <label class="control-label">开户城市</label>
                            <input class="form-control" type="text" value="<?php echo ($data["mch_bank_citys"]); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group" id="depositLBnk">
                            <label class="control-label">开户支行</label>
                            <input class="form-control" type="text" value="<?php echo (reload_banks($data["mch_linkbnk"])); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">联行号</label>
                            <input class="form-control" type="text" value="<?php echo ($data["mch_linkbnk"]); ?>" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">预留手机号码</label>
                            <input class="form-control" type="text" value="<?php echo ($data["mch_bank_tel"]); ?>" readonly>
                        </div>
                    </div>
                </div>
                <p style="border-bottom: solid 1px #E4E4E4;font-size: 16px;padding-bottom: 5px;">证件信息</p>
                <div class="row">

                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label"><span
                                    style="color: red">身份证正面(Size:<?php echo (img_size($data["mch_img_z"])); ?>)</span></label>
                            <div class="imgborder">
                                <img class="image-responsive" id="S1" src="<?php echo ($data["mch_img_z"]); ?>" onclick="view_img('S1')">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label"><span
                                    style="color: red">身份证反面(Size:<?php echo (img_size($data["mch_img_p"])); ?>)</span></label>
                            <div class="imgborder">
                                <img class="image-responsive" id="S2" src="<?php echo ($data["mch_img_p"]); ?>" onclick="view_img('S2')">
                            </div>
                        </div>
                    </div>
                    <?php if(!empty($data["mch_img_bank"])): ?><div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label"><span
                                    style="color: red">结算银行卡(Size:<?php echo (img_size($data["mch_img_bank"])); ?>)</span></label>
                            <div class="imgborder">
                                <img class="image-responsive" id="S6" src="<?php echo ($data["mch_img_bank"]); ?>" onclick="view_img('S6')">
                            </div>
                        </div>
                    </div><?php endif; ?>
                    <?php if(!empty($data["mch_img_s"])): ?><div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label"><span
                                    style="color: red">手持照(Size:<?php echo (img_size($data["mch_img_s"])); ?>)</span></label>
                            <div class="imgborder">
                                <img class="image-responsive" id="S3" src="<?php echo ($data["mch_img_s"]); ?>" onclick="view_img('S3')">
                            </div>
                        </div>
                    </div><?php endif; ?>
                    <?php if(!empty($data["mch_img_yyzz"])): ?><div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span style="color: red">营业执照(Size:<?php echo (img_size($data["mch_img_yyzz"])); ?>)</span></label>
                                <div class="imgborder">
                                    <img class="image-responsive" id="S4" src="<?php echo ($data["mch_img_yyzz"]); ?>"
                                         onclick="view_img('S4')">
                                </div>
                            </div>
                        </div><?php endif; ?>
                    <?php if(!empty($data["mch_img_sqh"])): ?><div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span style="color: red">收款人授权函(Size:<?php echo (img_size($data["mch_img_sqh"])); ?>)</span></label>
                                <div class="imgborder">
                                    <img class="image-responsive" id="S5" src="<?php echo ($data["mch_img_sqh"]); ?>"
                                         onclick="view_img('S5')">
                                </div>
                            </div>
                        </div><?php endif; ?>
                    <?php if(!empty($data["mch_img_auth_z"])): ?><div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span style="color: red">身份证正面(授权人)(Size:<?php echo (img_size($data["mch_img_auth_z"])); ?>)</span></label>
                                <div class="imgborder">
                                    <img class="image-responsive" id="mch_img_auth_z" src="<?php echo ($data["mch_img_auth_z"]); ?>"
                                         onclick="view_img('mch_img_auth_z')">
                                </div>
                            </div>
                        </div><?php endif; ?>
                    <?php if(!empty($data["mch_img_auth_p"])): ?><div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span style="color: red">身份证反面(授权人)(Size:<?php echo (img_size($data["mch_img_auth_p"])); ?>)</span></label>
                                <div class="imgborder">
                                    <img class="image-responsive" id="mch_img_auth_p" src="<?php echo ($data["mch_img_auth_p"]); ?>"
                                         onclick="view_img('mch_img_auth_p')">
                                </div>
                            </div>
                        </div><?php endif; ?>

                    <?php if(!empty($data["mch_img_m1"])): ?><div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span
                                        style="color: red">门头照片(Size:<?php echo (img_size($data["mch_img_m1"])); ?>)</span></label>
                                <div class="imgborder">
                                    <img class="image-responsive" id="S7" src="<?php echo ($data["mch_img_m1"]); ?>" onclick="view_img('S7')">
                                </div>
                            </div>
                        </div><?php endif; ?>
                    <?php if(!empty($data["mch_img_m2"])): ?><div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span
                                        style="color: red">门店内景(Size:<?php echo (img_size($data["mch_img_m2"])); ?>)</span></label>
                                <div class="imgborder">
                                    <img class="image-responsive" id="S8" src="<?php echo ($data["mch_img_m2"]); ?>" onclick="view_img('S8')">
                                </div>
                            </div>
                        </div><?php endif; ?>
                    <?php if(!empty($data["mch_img_m3"])): ?><div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span
                                        style="color: red">收银台照(Size:<?php echo (img_size($data["mch_img_m3"])); ?>)</span></label>
                                <div class="imgborder">
                                    <img class="image-responsive" id="S9" src="<?php echo ($data["mch_img_m3"]); ?>" onclick="view_img('S9')">
                                </div>
                            </div>
                        </div><?php endif; ?>
                    <?php if(!empty($data["mch_img_m4"])): ?><div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span
                                        style="color: red">商户协议(Size:<?php echo (img_size($data["mch_img_m4"])); ?>)</span></label>
                                <div class="imgborder">
                                    <img class="image-responsive" id="S10" src="<?php echo ($data["mch_img_m4"]); ?>" onclick="view_img('S10')">
                                </div>
                            </div>
                        </div><?php endif; ?>
                    <?php if(!empty($data["mch_img_m5"])): ?><div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span
                                        style="color: red">商户信息(Size:<?php echo (img_size($data["mch_img_m5"])); ?>)</span></label>
                                <div class="imgborder">
                                    <img class="image-responsive" id="S11" src="<?php echo ($data["mch_img_m5"]); ?>" onclick="view_img('S11')">
                                </div>
                            </div>
                        </div><?php endif; ?>

                </div>
            </div>
            <div class="panel-footer text-justify">
                <a class="btn btn-default" href="<?php echo U('Auditing');?>">返回审核列表</a>
                <a class="btn btn-info shenhe" data-id="<?php echo ($data["id"]); ?>">审核结果</a>
                <a class="btn btn-warning" href="<?php echo U('Auditing_edits',array('id'=>$data['id']));?>">编辑资料</a>
            </div>
        </div>
    </div>
</div>


<!-- 模态框开始 -->
<div class="modal fade" id="ShenHe-add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width: 370px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <i class="pci-cross pci-circle"></i>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    审核商户结果
                </h4>
            </div>
            <form action="<?php echo U('Auditing_status');?>" method="post">
            <div class="modal-body">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="control-label">审核状态</label>
                                    <select class="form-control" name="status" required>
                                        <option value="0">待审核</option>
                                        <option value="1">审核通过</option>
                                        <option value="2">审核拒绝</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="control-label">状态信息</label>
                                    <textarea class="form-control" name="info" placeholder="审核拒绝原因 如开启模板消息 将会发送给商户 或扫码显示拒绝原因..."></textarea>
                                </div>
                            </div>
                        </div>
                        <!--<div class="row">
                            <div class="col-sm-12">
                                <div class="checkbox pad-btm text-left">
                                    <input id="demo-form-checkbox" class="magic-checkbox" type="checkbox">
                                    <label for="demo-form-checkbox">发送审核结果模板消息给商户</label>
                                </div>
                            </div>
                        </div>-->


                        <!--<div class="panel-footer text-right">
                            <input type="hidden" name="id" value="<?php echo ($data["id"]); ?>">
                            <button class="btn btn-success" type="submit">提交</button>
                        </div>-->

                    </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="id" value="<?php echo ($data["id"]); ?>">
                <button class="btn btn-success">提交</button>
            </div>
            </form>

        </div>
    </div>
</div>
<!-- 模态框关闭 -->
<script>
    $('.shenhe').click(function () {
        $('#ShenHe-add').modal('show');
    });
    $(function() {
        $('[name="status"]').change(mch_status);
        mch_status();
    });
    function mch_status() {
        var  type= $('[name="status"] option:selected').val();
        var  status=$('[name="info"]');
        if(type==2){
            status.attr("disabled",false);
            status.attr("required",true);
            status.val('');
        }else{
            status.attr("disabled",true);
            status.attr("required",false);
            if(type==1){
                status.val('信息正确,审核通过');
            }else{
                status.val('待审核');
            }
        }
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