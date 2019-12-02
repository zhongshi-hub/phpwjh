<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title;?>-移动支付管理平台</title>
    <link rel="shortcut icon"  href="<?php echo GetPico();?>" />
    <link href="/Ext?g=css" rel="stylesheet">
    <link  href="/Source/statics/css/themes/type-b/theme-navy.min.css" rel="stylesheet">
    <script type="text/javascript" src="/Source/statics/plugins/pace/pace.min.js"></script>
    <script type="text/javascript" src="/Source/statics/js/jquery-2.2.4.min.js"></script>
    <script type="text/javascript" src="/Source/statics/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="/Source/statics/js/nifty.min.js"></script>
    <script type="text/javascript" src="/Source/artDialog/jquery.artDialog.js?skin=simple"></script>
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
                <a href="<?php echo U('System/Index/index');?>" class="navbar-brand">
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
                            <div class="username hidden-xs"><h4 class="text-main"><p class="text-pink"><?php echo ($_SESSION['system']['name']); ?></p></h4></div>
                        </a>
                        <div class="dropdown-menu  dropdown-menu-right panel-default">

                            <div class="pad-all text-right">
                                <a href="<?php echo U('System/Login/out');?>" class="btn btn-primary">
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

                                <?php if(is_array($menu)): $k = 0; $__LIST__ = $menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?><li <?php if(($rule_name) == $vo['name']): ?>class="active-link"<?php endif; ?>>

                                    <!--<?php $_RANGE_VAR_=is_array($vo['name']['_data'])?$vo['name']['_data']:explode(',',$vo['name']['_data']);if($rule_name>= $_RANGE_VAR_[0] && $rule_name<= $_RANGE_VAR_[1]):?>000<?php endif; ?>-->
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
                <div class="pad-btm form-inline">
                    <div class="row">
                        <div class="col-sm-6 text-xs-center">
                            <div class="form-group">
                                <button id="add_brand" class="btn btn-purple"><i class="demo-pli-plus"></i>添加品牌</button>
                            </div>
                        </div>
                        <div class="col-sm-6 text-xs-center text-right">
                        </div>
                    </div>
                </div>
                <table id="demo-foo-addrow" class="table table-bordered table-hover toggle-circle" data-page-size="7">
                    <thead>
                    <tr>
                        <th class="text-center">品牌LOGO</th>
                        <th class="text-center">所属公司</th>
                        <th class="text-center" data-sort-initial="true" data-toggle="true">品牌名称</th>
                        <th class="text-center">品牌主域名</th>
                        <th class="text-center">品牌授权码</th>
                        <th class="text-center">品牌模板主题</th>
                        <th class="text-center">加入时间</th>
                        <th class="text-center">品牌状态</th>
                        <th class="text-center">鉴权次数/已用次数</th>
                        <th class="text-center">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if(is_array($data)): foreach($data as $key=>$v): ?><tr>
                            <td class="text-center"><img src="<?php echo ($v["brand_logo"]); ?>" title="点击放大查看" width="30" height="30" onclick="$.fancybox.open('<?php echo ($v["brand_logo"]); ?>')" style="cursor:pointer"></td>
                            <td class="text-center"><?php echo ($v['web_doname']); ?></td>
                            <td class="text-center"><?php echo ($v['web_name']); ?></td>
                            <td class="text-center"><?php echo ($v['web_domain']); ?></td>
                            <td class="text-center"><?php echo ($v['web_authcode']); ?></td>
                            <td class="text-center"><?php echo ($v['theme']); ?></td>
                            <td class="text-center"><?php echo (date('Y-m-d H:i:s',$v['web_ctime'])); ?></td>
                            <td class="text-center">
                                <input id="status-switch-<?php echo ($v['id']); ?>" value="<?php echo ($v['id']); ?>"
                                       class="status_switch toggle-switch switchery-default"
                                <?php if(($v["status"]) == "1"): ?>checked=""<?php endif; ?>
                                type="checkbox" name="status">
                                <label id="status_switch" for="status-switch-<?php echo ($v['id']); ?>"></label>
                            </td>
                            <td class="text-center" style="cursor: pointer;" onclick="card_add(<?php echo ($v["id"]); ?>)"><span class="badge badge-warning"><?php echo ($v["auth_card"]); ?>/<?php echo auth_card_count($v['web_authcode']) ?></span></td>
                            <td class="text-center">
                                <button class="btn btn-info btn-rounded btn-xs" web_id="<?php echo ($v['id']); ?>"
                                        web_doname="<?php echo ($v['web_doname']); ?>" theme="<?php echo ($v['theme']); ?>"
                                        web_name="<?php echo ($v['web_name']); ?>" web_domain="<?php echo ($v['web_domain']); ?>"
                                        oss_bucket="<?php echo ($v['oss_bucket']); ?>" oss_domain="<?php echo ($v['oss_domain']); ?>"
                                        users_domain="<?php echo ($v['users_domain']); ?>" agent_domain="<?php echo ($v['agent_domain']); ?>"
                                        main_domain="<?php echo ($v['main_domain']); ?>" brand_ico="<?php echo ($v['brand_ico']); ?>" brand_logo="<?php echo ($v['brand_logo']); ?>"
                                        onclick="edit_brand(this)">编辑
                                </button>
                                <button class="btn btn-info btn-success btn-xs" onclick="Users('<?php echo ($v['web_authcode']); ?>')">
                                    平台管理员
                                </button>
                                <a href="<?php echo U('api',array('id'=>$v['id']));?>" class="btn btn-info btn-pink btn-xs api">接口配置</a>
                            </td>
                        </tr><?php endforeach; endif; ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="12">
                            <div class="text-right">
                                <ul class="pagination"><?php echo ($page); ?></ul>
                            </div>
                        </td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>


<!-- 添加模态框开始 -->
<div class="modal fade" id="add_brand_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" style="width: 800px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <i class="pci-cross pci-circle"></i>
                </button>
                <h4 class="modal-title" >
                    添加品牌
                </h4>
            </div>
            <div class="modal-body">
                <form class="form" action="<?php echo U('System/Extend/add_brand');?>" method="post">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label">品牌名称</label>
                                    <input class="form-control" type="text" name="web_name" placeholder="如:讯码付" required>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label">品牌域名</label>
                                    <input class="form-control" type="text" name="web_domain" placeholder="主域名 如 xunmafu.com" required>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label">品牌模板主题</label>
                                    <input class="form-control" type="text" name="theme" placeholder="用于多模板 如 xunmafu " required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label">品牌公司</label>
                                    <input class="form-control" type="text" name="web_doname" placeholder="公司全称" required>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label">OSS Bucket</label>
                                    <input class="form-control" type="text" name="oss_bucket" placeholder="品牌英文全称 如xunmafu" required>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label">OSS 域名</label>
                                    <input class="form-control" type="text" name="oss_domain" placeholder="用于图片解析 如:img.xunmafu.com" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label">网站主域名</label>
                                    <input class="form-control" type="text" name="main_domain" placeholder="商用 如www.xunmafu.com" required>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label">代理端域名</label>
                                    <input class="form-control" type="text" name="agent_domain" placeholder="商用 如a.xunmafu.com" required>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label">商户端域名</label>
                                    <input class="form-control" type="text" name="users_domain" placeholder="商用 如u.xunmafu.com" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">品牌LOGO(130*130)</label>
                                    <?php echo uploads_map('brand_logo','',1);?>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">备注描述</label>
                                    <label class="control-label">品牌Icon图标(格式:.ico)</label>
                                    <?php echo uploads_map('brand_ico','',1);?>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="panel-footer text-right">
                        <button class="btn btn-success" type="submit">提交</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- 模态框开始 -->
<div class="modal fade" id="auth-card-add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <i class="pci-cross pci-circle"></i>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    鉴权系统次数新增
                </h4>
            </div>
            <div class="modal-body">
                <form action="<?php echo U('auth_card_adds',array('Debug'=>1));?>" method="post">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="control-label">新增次数</label>
                                    <input class="form-control" type="tel" name="auth_card_count" onkeyup="this.value=this.value.replace(/\D/g,'')"  onafterpaste="this.value=this.value.replace(/\D/g,'')" maxlength="5" required>
                                </div>
                                <input type="hidden" id="auth_card_id" name="auth_card_id" value="">
                            </div>
                        </div>
                    </div>

                    <div class="panel-footer text-right">
                        <button class="btn btn-success" type="submit">提交</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- 模态框关闭 -->


<!-- 编辑模态框开始 -->
<div class="modal fade" id="edit_brand_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" style="width: 800px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <i class="pci-cross pci-circle"></i>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    编辑品牌
                </h4>
            </div>
            <div class="modal-body">
                <form class="edit_form" action="<?php echo U('System/Extend/edit_brand');?>" method="post">
                    <input type="hidden" name="id" value="">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label">品牌名称</label>
                                    <input class="form-control" type="text" name="web_name" required>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label">品牌域名</label>
                                    <input class="form-control" type="text" name="web_domain" required>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label">品牌模板主题</label>
                                    <input class="form-control" type="text" name="theme" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label">品牌公司</label>
                                    <input class="form-control" type="text" name="web_doname" required>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label">OSS Bucket</label>
                                    <input class="form-control" type="text" name="oss_bucket" placeholder="品牌英文全称 如xunmafu" required>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label">OSS 域名</label>
                                    <input class="form-control" type="text" name="oss_domain" placeholder="用于图片解析 如:img.xunmafu.com" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label">网站主域名</label>
                                    <input class="form-control" type="text" name="main_domain" placeholder="商用 如www.xunmafu.com" required>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label">代理端域名</label>
                                    <input class="form-control" type="text" name="agent_domain" placeholder="商用 如a.xunmafu.com" required>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label">商户端域名</label>
                                    <input class="form-control" type="text" name="users_domain" placeholder="商用 如u.xunmafu.com" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">品牌LOGO(130*130)</label>
                                    <?php echo uploads_map('ebrand_logo','',1);?>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">备注描述</label>
                                    <label class="control-label">品牌Icon图标(格式:.ico)</label>
                                    <?php echo uploads_map('ebrand_ico','',1);?>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="panel-footer text-right">
                        <button class="btn btn-success" type="submit">提交</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- 默认管理员 -->
<div class="modal fade" id="brand_users_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <i class="pci-cross pci-circle"></i>
                </button>
                <h4 class="modal-title">
                    默认管理员账户
                </h4>
            </div>
            <div class="modal-body">
                <form class="users_form" action="<?php echo U('System/Extend/brand_users');?>" method="post">

                    <div class="panel-body">
                        <div class="alert alert-danger">
                            <strong>提示</strong> <span id="users_msg"></span>
                        </div>
                        <input type="hidden" name="codes" value="">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">账户名称</label>
                                    <input class="form-control" type="text" name="username" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">账户密码</label>
                                    <input class="form-control" type="password" name="password" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">账户姓名</label>
                                    <input class="form-control" type="text" name="name" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">账户状态</label>
                                    <select id="u_status" tabindex="2" class="form-control" name="status">
                                        <option value="1">开启</option>
                                        <option value="0">禁用</option>
                                    </select>
                                </div>


                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">联系电话</label>
                                    <input class="form-control" type="text" name="phone" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">联系邮箱</label>
                                    <input class="form-control" type="email" name="email" required>
                                </div>
                            </div>
                        </div>

                    </div>


                    <div class="panel-footer text-right">
                        <button class="btn btn-info text-left edits" type="button" onclick="edit_users()">编辑</button>
                        <button class="btn btn-success " type="submit">提交</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    $('#api_data,#api_data_e').chosen({width: '100%'});

    $('#add_brand').click(function () {
        $('#add_brand_modal').modal('show');
    });
    function  card_add(id) {
        $('[name="auth_card_count"]').val('');
        $('#auth_card_id').val(id);
        $('#auth-card-add').modal('show');
    }



    /*接口状态更新*/
    $(".status_switch").change(function () {
        var status = $("#" + this.id).is(':checked');
        var ajax_data = {id: this.value, status: status, type: 'status'};
        var actionurl = "<?php echo U('brand_status');?>";
        $.post(actionurl, ajax_data, function (data) {
            if (data.status == 1) {
                $.niftyNoty({
                    type: 'success',
                    message: '<strong>' + data.info + '</strong> 2秒后自动刷新当前页面!',
                    container: 'floating',
                    timer: 2000
                });
                setTimeout(function () {
                    window.location.reload();
                }, 2000);
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

    });

    //编辑信息
    function edit_brand(data) {

        var web_name = $(data).attr('web_name');
        var web_domain = $(data).attr('web_domain');
        var web_doname = $(data).attr('web_doname');
        var id = $(data).attr('web_id');
        var theme = $(data).attr('theme');
        var oss_bucket = $(data).attr('oss_bucket');
        var oss_domain = $(data).attr('oss_domain');
        var main_domain = $(data).attr('main_domain');
        var agent_domain = $(data).attr('agent_domain');
        var users_domain = $(data).attr('users_domain');
        var brand_logo = $(data).attr('brand_logo');
        var brand_ico = $(data).attr('brand_ico');

        $(".edit_form  [name='theme']").val(theme);
        $(".edit_form  [name='web_doname']").val(web_doname);
        $(".edit_form  [name='web_domain']").val(web_domain);
        $(".edit_form  [name='web_name']").val(web_name);
        $(".edit_form  [name='id']").val(id);
        $(".edit_form  [name='oss_bucket']").val(oss_bucket);
        $(".edit_form  [name='oss_domain']").val(oss_domain);
        $(".edit_form  [name='main_domain']").val(main_domain);
        $(".edit_form  [name='agent_domain']").val(agent_domain);
        $(".edit_form  [name='users_domain']").val(users_domain);

        $(".edit_form  [name='ebrand_logo']").val(brand_logo);
        $(".edit_form  [name='ebrand_ico']").val(brand_ico);


        $('#edit_brand_modal').modal('show');
    }

    //管理员账户配置
    function Users(codes) {
        var ajax_data = {codes: codes, type: 'status'};
        var actionurl = "<?php echo U('brand_users');?>";
        $.post(actionurl, ajax_data, function (data) {
            if (data.status == 1) {
                if (data.info.is_data == 1) {
                    //有
                    $('#users_msg').html('如需修改管理员信息,请点击编辑后再修改提交!密码留空即为不修改密码');
                    $(".users_form  [name='username']").val(data.info.username);
                    $(".users_form  [name='name']").val(data.info.name);
                    $(".users_form  [name='email']").val(data.info.email);
                    $(".users_form  [name='phone']").val(data.info.phone);
                    if (data.info.status == 1) {
                        $("#u_status").find("option[value='1']").attr("selected", true);
                    } else {
                        $("#u_status").find("option[value='0']").attr("selected", true);
                    }
                    $('.users_form input').attr('disabled', 'true');
                    $('.users_form select').attr('disabled', 'true');
                    $('.edits').show();

                } else {
                    $(".users_form  [name='username']").val('');
                    $(".users_form  [name='name']").val('');
                    $(".users_form  [name='email']").val('');
                    $(".users_form  [name='phone']").val('');
                    $(".users_form  [name='password']").val('');
                    $(".users_form  [name='password']").attr('required', 'true');

                    $('#users_msg').html('系统暂未监测到当前品牌商家无管理员信息,请首次配置!');
                    $('.users_form input').removeAttr('disabled');
                    $('.users_form select').removeAttr('disabled');
                    $('.edits').hide();
                }
                $(".users_form  [name='codes']").val(data.info.codes);
                $('#brand_users_modal').modal('show');
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

    //编辑账户
    function edit_users() {
        $('.users_form input').removeAttr('disabled');
        $('.users_form select').removeAttr('disabled');
        $(".users_form  [name='password']").removeAttr('required');
    }




</script>

</div>
<footer id="footer">
    <div class="hide-fixed pull-right pad-rgt">
        总管理后台
    </div>
    <p class="pad-lft">&#0169; <?php echo C('WEB_COPY');?></p>
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
                    <form class="form-horizontal" action="<?php echo U('System/User/editpass');?>" method="post">
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
<!-- 修改交易识别码模态框结束 -->

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
</style>
<script type="text/javascript">
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
            content: "<?php echo U('Plugs/Upload/index/Mod/System');?>"
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