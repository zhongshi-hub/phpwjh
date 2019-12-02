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
    .padding_data {
        padding: 7px;
        border-radius: 5px;
    }
    .send_template{
        font-size: 12px;padding: 5px;border: 1px dashed #3ccf05!important;background-color: antiquewhite;color: #2a9303;
    }
</style>
<div id="content-container">
    <div id="page-content">
        <div class="panel" style="margin: 9px;">
            <div class="panel-heading">
                <h3 class="panel-title">通道列表 - <span style="color:red;font-size: 20px;"><?php echo ($mch_name); ?></span></h3>
            </div>

        </div>
        <?php if(is_array($data)): $k = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($k % 2 );++$k; if(($v["alleys_type"]) == "Aliisv"): ?><div class="col-lg-3 eq-box-lg">
                    <div class="panel">
                        <div class="panel-heading">
                            <!--<?php if(($v['rest_in']) == "1"): if(!empty($v["mch_id"])): if(($v["load_status"]) == "1"): ?><div class="panel-control">
                                          <span class="badge badge-purple padding_data"  style="cursor: pointer" onclick="sin('<?php echo ($v["alleys"]); ?>','<?php echo ($v["alleys_type"]); ?>',<?php echo ($v["cid"]); ?>)">重新进件</span>
                                       </div><?php endif; endif; endif; ?>-->

                            <div class="panel-control">
                                <?php if(($v['appid_status']) == "1"): ?><button onclick="AppId('<?php echo ($v["alleys_type"]); ?>',<?php echo ($v["cid"]); ?>)" class="btn btn-rounded send_template">商户支付配置</button><?php endif; ?>
                                <button onclick="send_success_template('<?php echo ($v["alleys_type"]); ?>',<?php echo ($v["cid"]); ?>)" class="btn btn-rounded send_template" <?php if(($v['mch_id'] == '') or ($v['send_success'] == 1)): ?>disabled<?php endif; ?> >发送通道开通提醒</button>
                            </div>
                            <h3 class="panel-title"><?php echo ($v["alleys"]); ?></h3>
                        </div>
                        <div class="panel-body">
                            <p style="border-bottom: solid 1px #E4E4E4;font-size: 16px;padding-bottom: 5px;">基本信息</p>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">授权参数</label>
                                        <div class="form-control" style="border:none;padding: 0;">
                                            <?php $isToken=aliIsvToken($v['cid']);if(!empty($isToken)){ ?>
                                            <span onclick="ali_isv(<?php echo ($v["cid"]); ?>,1)"
                                                  style="cursor: pointer" class="badge badge-info padding_data"><?php echo aliIsvToken($v['cid'],'user_id');?></span>
                                            <?php }else{ ?>
                                                <span onclick="ali_isv(<?php echo ($v["cid"]); ?>,0)"
                                                      style="cursor: pointer;background-color: #808080"
                                                      class="badge badge-mint padding_data add-tooltip"
                                                      data-toggle="tooltip" data-container="body" data-placement="bottom"
                                                      data-original-title="点击授权配置">未授权</span>
                                           <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6  text-right">
                                    <div class="form-group">
                                        <label class="control-label">商户费率</label>
                                        <div class="form-control" style="border:none;padding: 0;">
                                            <?php if(!empty($v["rate"])): ?><span style="cursor: pointer" id="setRate" class="badge badge-info padding_data"><?php echo ($v["rate"]); ?> %.</span>
                                                <?php else: ?>
                                                <span id="setRate" class="badge badge-default padding_data"
                                                      style="color: #666;cursor: pointer">Null %.</span><?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">商户状态</label>
                                        <div class="form-control" style="border:none;padding: 0;">
                                            <?php switch($v["status"]): case "1": ?><span class="badge badge-success padding_data">审核通过</span><?php break;?>
                                                <?php case "2": ?><span class="badge badge-default padding_data"
                                                      style="color: #666;">拒绝驳回</span><?php break;?>
                                                <?php case "3": ?><span class="badge badge-purple padding_data">审核中</span><?php break;?>
                                                <?php default: ?>
                                                <span class="badge badge-dark padding_data" style="background-color: #6f7274">未授权</span><?php endswitch;?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 text-right">
                                    <div class="form-group">
                                        <label class="control-label">通道状态</label>
                                        <div class="form-control" style="border:none;padding: 0;">
                                            <?php switch($v["load_status"]): case "1": ?><span class="badge badge-success padding_data" >激活成功</span><?php break;?>
                                                <?php case "2": ?><span class="badge badge-default padding_data"
                                                      style="color: #666;">已冻结</span><?php break;?>
                                                <?php case "3": ?><span class="badge badge-danger padding_data"
                                                      style="cursor:pointer;" onclick="mch_loading('<?php echo ($v["mch_id"]); ?>','<?php echo ($v["alleys_type"]); ?>')">信息被驳回</span><?php break;?>
                                                <?php default: ?>
                                                <span class="badge badge-mint padding_data" style="cursor: pointer" >待激活</span><?php endswitch;?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p style="border-bottom: solid 1px #E4E4E4;font-size: 16px;padding-bottom: 5px;">信息操作</p>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <div class="form-control" style="border:none;padding: 0;cursor: pointer">
                                        <span class="badge badge-primary padding_data"
                                              onclick="m_data('<?php echo ($v["alleys"]); ?>','<?php echo ($v["alleys_type"]); ?>',<?php echo ($v["cid"]); ?>)">商户信息</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <p style="border-bottom: solid 1px #E4E4E4;font-size: 16px;padding-bottom: 5px;">通道简介</p>
                            <dl>
                                <dd>
                                    <textarea style="resize:none" class="form-control" disabled><?php if(!empty($v["make"])): echo ($v["make"]); else: ?>无描述<?php endif; ?></textarea>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            <?php else: ?>
            <div class="col-lg-3 eq-box-lg">
                <div class="panel">
                    <div class="panel-heading">
                        <!--<?php if(($v['rest_in']) == "1"): if(!empty($v["mch_id"])): if(($v["load_status"]) == "1"): ?><div class="panel-control">
                                      <span class="badge badge-purple padding_data"  style="cursor: pointer" onclick="sin('<?php echo ($v["alleys"]); ?>','<?php echo ($v["alleys_type"]); ?>',<?php echo ($v["cid"]); ?>)">重新进件</span>
                                   </div><?php endif; endif; endif; ?>-->

                        <div class="panel-control">
                            <?php if(($v['appid_status']) == "1"): ?><button onclick="AppId('<?php echo ($v["alleys_type"]); ?>',<?php echo ($v["cid"]); ?>)" class="btn btn-rounded send_template">商户支付配置</button><?php endif; ?>
                            <button onclick="send_success_template('<?php echo ($v["alleys_type"]); ?>',<?php echo ($v["cid"]); ?>)" class="btn btn-rounded send_template" <?php if(($v['mch_id'] == '') or ($v['send_success'] == 1)): ?>disabled<?php endif; ?> >发送通道开通提醒</button>
                        </div>
                        <h3 class="panel-title"><?php echo ($v["alleys"]); ?></h3>
                    </div>
                    <div class="panel-body">
                        <p style="border-bottom: solid 1px #E4E4E4;font-size: 16px;padding-bottom: 5px;">基本信息</p>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">商户编号</label>
                                    <div class="form-control" style="border:none;padding: 0;">
                                        <?php if(!empty($v["mch_id"])): ?><span onclick="mch_data('<?php echo ($v["alleys_type"]); ?>',<?php echo ($v["cid"]); ?>)"
                                                  style="cursor: pointer" class="badge badge-info padding_data"><?php echo ($v["mch_id"]); ?></span>
                                            <?php else: ?>
                                            <span onclick="sin('<?php echo ($v["alleys"]); ?>','<?php echo ($v["alleys_type"]); ?>',<?php echo ($v["cid"]); ?>)"
                                                  style="cursor: pointer"
                                                  class="badge badge-mint padding_data add-tooltip"
                                                  data-toggle="tooltip" data-container="body" data-placement="bottom"
                                                  data-original-title="点击进件">此通道未进件</span><?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6  text-right">
                                <div class="form-group">
                                    <label class="control-label">商户费率</label>
                                    <div class="form-control" style="border:none;padding: 0;">
                                        <?php if(!empty($v["rate"])): ?><span style="cursor: pointer" data-name="<?php echo ($v["alleys"]); ?>" data-alley="<?php echo ($v["alleys_type"]); ?>"  data-cid="<?php echo ($v["cid"]); ?>" class="rest_rate badge badge-info padding_data"><?php echo ($v["rate"]); ?> %.</span>
                                            <?php else: ?>
                                            <span class="badge badge-default padding_data"
                                                  style="color: #666;">Null %.</span><?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">商户状态</label>
                                    <div class="form-control" style="border:none;padding: 0;">
                                        <?php switch($v["status"]): case "1": ?><span class="badge badge-success padding_data">审核通过</span><?php break;?>
                                            <?php case "2": ?><span class="badge badge-default padding_data"
                                                      style="color: #666;">拒绝驳回</span><?php break;?>
                                            <?php case "3": ?><span class="badge badge-purple padding_data">审核中</span><?php break;?>
                                            <?php default: ?>
                                            <span class="badge badge-dark padding_data">未进件</span><?php endswitch;?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 text-right">
                                <div class="form-group">
                                    <label class="control-label">通道状态</label>
                                    <div class="form-control" style="border:none;padding: 0;">
                                        <?php switch($v["load_status"]): case "1": ?><span class="badge badge-success padding_data" onclick="mch_status('<?php echo ($v["mch_id"]); ?>','<?php echo ($v["alleys_type"]); ?>')">激活成功</span><?php break;?>
                                            <?php case "2": ?><span class="badge badge-default padding_data"
                                                      style="color: #666;">已冻结</span><?php break;?>
                                            <?php case "3": ?><span class="badge badge-danger padding_data"
                                                      style="cursor:pointer;" onclick="mch_loading('<?php echo ($v["mch_id"]); ?>','<?php echo ($v["alleys_type"]); ?>')">信息被驳回</span><?php break;?>
                                            <?php default: ?>
                                            <span class="badge badge-mint padding_data" style="cursor: pointer" onclick="mch_status('<?php echo ($v["mch_id"]); ?>','<?php echo ($v["alleys_type"]); ?>')">审核中</span><?php endswitch;?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p style="border-bottom: solid 1px #E4E4E4;font-size: 16px;padding-bottom: 5px;">信息操作</p>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="form-control" style="border:none;padding: 0;cursor: pointer">
                                        <span class="badge badge-primary padding_data"
                                              onclick="m_data('<?php echo ($v["alleys"]); ?>','<?php echo ($v["alleys_type"]); ?>',<?php echo ($v["cid"]); ?>)">商户信息</span>
                                    </div>
                                </div>
                            </div>
                            <?php if(($v['alter_status']) == "1"): if(!empty($v["mch_id"])): if(($v["load_status"]) == "1"): ?><div class="col-sm-6 text-right">
                                            <div class="form-group">
                                                <div class="form-control" style="border:none;padding: 0;cursor: pointer">
                                                <span class="badge badge-danger padding_data"
                                                      onclick="alter('<?php echo ($v["alleys"]); ?>','<?php echo ($v["alleys_type"]); ?>',<?php echo ($v["cid"]); ?>)">变更信息</span>
                                                </div>
                                            </div>
                                        </div><?php endif; endif; endif; ?>

                        </div>

                        <p style="border-bottom: solid 1px #E4E4E4;font-size: 16px;padding-bottom: 5px;">通道简介</p>
                        <dl>
                            <dd>
                                <textarea style="resize:none" class="form-control" disabled><?php if(!empty($v["make"])): echo ($v["make"]); else: ?>无描述<?php endif; ?></textarea>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div><?php endif; endforeach; endif; else: echo "" ;endif; ?>
    </div>
</div>

<!-- 支付宝ISV配置参数开始 -->
<div class="modal fade" id="ali_isv" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width: 400px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <i class="pci-cross pci-circle"></i>
                </button>
                <h4 class="modal-title">
                    授权配置  <span  id="s1" class="badge badge-info" style="border-radius: 5px;font-weight: 200;font-size:.8em;display: none">已授权</span> <span id="s0" class="badge badge-default" style="border-radius: 5px;font-weight: 200;font-size:.8em;color: #000000;display: none">待授权</span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="tab-bases">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a data-toggle="tab" href="#tab-1" aria-expanded="true">授权二维码</a>
                        </li>
                        <li class="">
                            <a data-toggle="tab" href="#tab-2" aria-expanded="false">手动配置</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div id="tab-1" class="tab-pane fade active in" style="text-align: center">
                            <?php $url='http://'.$_SERVER["HTTP_HOST"].'/aliOauthUrl?id='.$_GET['id'];$url=Xencode($url); ?>
                            <img src="/Pays/Mch/QrData/url/<?php echo ($url); ?>"  width="300px" height="300px">
                            <div class="panel-footer text-center">
                                请将此二维码出示给商户使用支付宝扫码授权
                            </div>
                        </div>
                        <div id="tab-2" class="tab-pane fade">
                            <form action="<?php echo U('Isv/setToken');?>" method="post" >
                                <div class="panel-body">
                                    <div class="row" style="margin: 26px 0">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label class="control-label">商户ID</label>
                                                <input class="form-control" type="text" name="user_id" placeholder="商户ID" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label class="control-label">授权Token</label>
                                                <input class="form-control" type="text" name="app_auth_token" placeholder="授权Token">
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label class="control-label">授权AppId</label>
                                                <input class="form-control" type="text" name="appid" value="<?php echo GetPayConfigs('ali_isv_appid');?>" placeholder="ISV的应用APPID 引用总配置" readonly="">
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="mid" value="<?php echo ($_GET['id']); ?>">
                                    <button class="btn btn-success" type="submit">提交</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- 模态框关闭 -->


<!-- 模态框开始 -->
<div class="modal fade" id="alleys-data" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width: 370px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <i class="pci-cross pci-circle"></i>
                </button>
                <h4 class="modal-title">
                    通道参数配置
                </h4>
            </div>
            <div class="modal-body">
                <form action="<?php echo U('alley_mch_data');?>" method="post">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="control-label">商户编号</label>
                                    <input class="form-control" type="text" name="mch_id" placeholder="商户编号" required>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="control-label">商户密钥</label>
                                    <input class="form-control" type="text" name="mch_key" placeholder="如有请填写">
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="control-label">附加参数</label>
                                    <input class="form-control" type="text" name="mch_appid" placeholder="如通道独立Appid及附加参数">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <input type="hidden" name="id" value="<?php echo ($data["id"]); ?>">
                        <button class="btn btn-success" type="submit">提交</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- 模态框关闭 -->


<!-- 模态框开始 商户独立Appid发起支付配置 -->
<div class="modal fade" id="appid-data" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width: 370px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <i class="pci-cross pci-circle"></i>
                </button>
                <h4 class="modal-title">
                    商户独立公众号支付配置
                </h4>
            </div>
            <div class="modal-body">
                <form action="<?php echo U('mch_appid_data');?>" method="post">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <p>说明:</p>
                                    <p>默认支付使用平台配置的默认公众号支付，如支付需要使用商户自己的公众号支付，此处选择商户所使用的公众号!
                                    支付公众号需要拥有独立权(AppId/AppSecret没有在其他平台使用) 请在使用前确保当前配置的公众号授权域名及JS域名为: http(s)://<?php echo $_SERVER['HTTP_HOST']; ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="control-label">商户支付公众号</label>
                                    <select data-placeholder="请选择..." id="pay_wx" name="pay_wxid" class="form-control" style="width: 300px;">
                                        <option value="0">-平台公众号-</option>
                                        <?php if(is_array($weixin)): foreach($weixin as $key=>$v): ?><option value="<?php echo ($v["id"]); ?>"><?php echo ($v["name"]); ?></option><?php endforeach; endif; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <input type="hidden" name="appid_alleys" value="">
                        <input type="hidden" name="appid_mch_id" value="">
                        <button class="btn btn-success" type="submit">提交</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- 模态框关闭 -->
<script>
    $('#pay_wx').chosen({search_contains:true,no_results_text: '没有找到相关的数据'});
    $(".chosen-container").css("width","100%");

    $('.rest_rate').click(function () {
        var cid=$(this).data('cid'),alley=$(this).data('alley');
        layer.prompt({title: '请输入商户'+$(this).data('name')+'费率(千分比)', formType: 0}, function(text){
            var ajax_data ={id:cid,type:alley,rate:text};
            $.post("<?php echo U('setAlleyRate');?>", ajax_data, function(data){
                if(data.status == 1){
                    layer.msg( data.info, function(){
                        window.location.reload();
                    });
                }
                else{
                    layer.msg( data.info);
                }
            }, 'json');

        });
    });
    $('#setRate').click(function () {
        layer.prompt({title: '请输入商户费率(千分比)', formType: 0}, function(text){
            var ajax_data ={id:'<?php echo ($_GET['id']); ?>',rate:text};
            $.post("<?php echo U('Isv/setRate');?>", ajax_data, function(data){
                if(data.status == 1){
                    layer.msg( data.info, function(){
                        window.location.reload();
                    });
                }
                else{
                    layer.msg( data.info);
                }
            }, 'json');

        });
    });
    function ali_isv(id,type) {
        $.post("<?php echo U('Isv/getToken');?>", {id:id}, function (data) {
            if (data.status == 1) {
                if(data.info.user_id){
                    $('#s1').show();
                }else{
                    $('#s0').show();
                }
                $('[name="mid"]').val(data.info.mid);
                $('[name="user_id"]').val(data.info.user_id);
                $('[name="app_auth_token"]').val(data.info.app_auth_token);
                $('#ali_isv').modal('show');
            }else{
                $.niftyNoty({
                    type: 'danger',
                    message: '<strong>' + data.info + '</strong>',
                    container: 'floating',
                    timer: 5000
                });
            }
        }, 'json');
    }

    //支付配置
    function AppId(type,id){
        $('[name="appid_alleys"]').val(type);
        $('[name="appid_mch_id"]').val(id);
        var actionurl = "<?php echo U('mch_appid_data');?>";
        var ajax_data = {'appid_mch_id': id, 'appid_alleys': type ,'type':'getData'};
        $.post(actionurl, ajax_data, function (data) {
             if (data.status == 1) {
                 var wx_id=data.info;
             }else{
                 var wx_id=0;
             }
             $("#pay_wx option[value="+wx_id+"]").attr("selected",true);
             $('#appid-data').modal('show');
             $("#pay_wx").trigger("chosen:updated")
        }, 'json');
    }

    function m_data(api, type, id) {
        layer.open({
            type: 2,
            title: api + '通道(商户信息)',
            area: ['80%', '85%'],
            fixed: false, //不固定
            maxmin: false,
            content: "<?php echo U('mdata');?>/type/" + type + "/id/" + id
        });
    }

    function alter(api, type, id) {
        window.location.href="<?php echo U('alter');?>/type/" + type + "/id/" + id+"?Debug=1";
    }


    function sin(api, type, id) {
        layer.open({
            type: 2,
            title: api + '通道进件',
            area: ['80%', '85%'],
            fixed: false, //不固定
            maxmin: true,
            content: "<?php echo U('mdata');?>/type/" + type + "/id/" + id + "/mch/sin"
        });
    }
    function mch_data(type, cid) {
        var actionurl = "<?php echo U('mch_alleys_getapi');?>";
        var ajax_data = {'cid': cid, 'type': type};
        $.post(actionurl, ajax_data, function (data) {
            if (data.status == 1) {
                $('[name="mch_id"]').val(data.info.mch_id);
                $('[name="id"]').val(data.info.id);
                $('[name="mch_key"]').val(data.info.mch_key);
                $('[name="mch_appid"]').val(data.info.mch_appid);
                $('#alleys-data').modal('show');
            }
            else {
                $.niftyNoty({
                    type: 'danger',
                    message: '<strong>' + data.info + '</strong>',
                    container: 'floating',
                    timer: 5000
                });
            }
        }, 'json');
    }

    //通道激活状态查询
    function  mch_status(mch,type) {
        var actionurl = "<?php echo U('Pays/MchApi/gateway',array('Debug'=>1));?>";
        var ajax_data = {'mch_id': mch, 'mch_type': type};
        $.post(actionurl, ajax_data, function (data) {
            if (data.status == 1) {
                $.niftyNoty({
                    type: 'success',
                    message: '<strong>' + data.info + '</strong>',
                    container: 'floating',
                    timer: 5000
                });
                window.location.reload();
            }
            else {
                $.niftyNoty({
                    type: 'danger',
                    message: '<strong>' + data.info + '</strong>',
                    container: 'floating',
                    timer: 5000
                });
            }
        }, 'json');
    }

    //通道驳回详细
    function mch_loading(mch,type) {
        var actionurl = "<?php echo U('mch_loading',array('Debug'=>1));?>";
        var ajax_data = {'mch_id': mch, 'mch_type': type};
        $.post(actionurl, ajax_data, function (data) {
            $.niftyNoty({
                type: 'danger',
                message: '<strong>' + data.info + '</strong>',
                container: 'floating',
                timer: 5000
            });
        });
    }


    //发送通道开通成功模板消息提醒
    function send_success_template(type,id) {
        layer.alert('每个商户每个通道只能发送一次开通提醒,发送一次后当前通道不可再次发送开通提醒!您确定要发送当前通道开通提醒吗?', {
            title:'温馨提示',
            skin: 'layui-layer-molv'
            ,closeBtn: 1
            ,anim: 6 //动画类型
        },function(){
            layer.msg('提醒发送中...请等待结果...', {
                icon: 16,
                shade: 0.01,
                time:300000
            });
            var ajax_data ={type:type,id:id,is_type:'pay'};
            var actionurl ='<?php echo U("SendAlleysTemplate",array("Debug"=>"1"));?>';
            $.post(actionurl, ajax_data, function (data) {
                if (data.status == 1) {
                    layer.closeAll();
                    $.niftyNoty({
                        type: 'success',
                        message : '<strong>'+data.info+'</strong>',
                        container : 'floating',
                        timer : 3000
                    });
                    setTimeout(function(){
                        window.location.reload();
                    }, 3000);
                }
                else {
                    layer.closeAll();
                    $.niftyNoty({
                        type: 'danger',
                        message: '<strong>' + data.info + '</strong>',
                        container: 'floating',
                        timer: 5000
                    });
                }
            }, 'json');

        });
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