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
            <div class="panel-heading">
                <h3 class="panel-title">
                    <?php if(isset($_GET['pid'])): ?>子<?php endif; ?>
                    <?php echo $title;?>
                </h3>

            </div>
            <!--筛选功能开始-->
            <div class="row">
                <div class="col-sm-12">
                    <form method="post" action="" ajax="n">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="control-label">详细信息</label>
                                        <input class="form-control" type="text" placeholder="支持 姓名/联系电话111111111111111111111111111111111"
                                               name="search_val" value="">
                                    </div>
                                </div>
                                <div id="SerCh">
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label class="control-label">省级筛选</label>
                                            <select data-placeholder="请选择..." tabindex="10" class="form-control"
                                                    name="sprovince">
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label class="control-label">市级筛选</label>
                                            <select class="form-control" name="scity">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label class="control-label">县级筛选</label>
                                            <select class="form-control" name="sdistrict">
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <div class="form-group">
                                        <label class="control-label">状态筛选</label>
                                        <select class="form-control" name="status">
                                            <option value="3">所有</option>
                                            <option value="1">开启</option>
                                            <option value="2">禁用</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-1" style="display: none">
                                    <div class="form-group">
                                        <label class="control-label">认证费用</label>
                                        <select class="form-control" name="auth_status">
                                            <option value="3">所有</option>
                                            <option value="1">开启</option>
                                            <option value="2">禁用</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="text-right">
                                <input type="hidden" name="pid"
                                       value="<?php if(isset($_GET['pid'])): echo ($_GET['pid']); else: ?> 0<?php endif; ?>">
                                <button class="btn btn-success" type="submit">搜索</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!--筛选功能结束-->


            <div class="panel-body">
                <div class="pad-btm form-inline">
                    <div class="row">
                        <div class="col-sm-6 text-xs-center">
                            <div class="form-group">
                                <?php if(isset($_GET['pid'])): ?><button id="AgentAdd" class="btn btn-info"><i class="demo-pli-add icon-fw"></i>添加子代理
                                    </button>
                                    <?php else: ?>
                                    <button id="AgentAdd" class="btn btn-purple"><i class="demo-pli-add icon-fw"></i>添加代理
                                    </button><?php endif; ?>
                            </div>
                        </div>
                        <div class="col-sm-6 text-xs-center text-right">
                        </div>
                    </div>
                </div>
                <table id="demo-foo-addrow" class="table table-bordered table-hover toggle-circle" data-page-size="7">
                    <thead>
                    <tr>
                        <th class="text-center">代理ID</th>
                        <?php if(isset($_GET['pid'])): ?><th class="text-center">上级代理</th><?php endif; ?>
                        <th class="text-center">代理姓名</th>
                        <th class="text-center">联系电话</th>
                        <th class="text-center">通道费率</th>
                        <th class="text-center">代理状态</th>
                        <!--<th class="text-center">认证费用</th>-->
                        <th class="text-center">加入时间</th>
                        <!--<th class="text-center">邀请码</th>-->
                        <th class="text-center">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if(is_array($data)): foreach($data as $key=>$v): ?><tr>
                            <td class="text-center"><?php echo ($v["id"]); ?></td>
                            <?php if(isset($_GET['pid'])): ?><td class="text-center"><?php echo (agent_name($v['pid'])); ?></td><?php endif; ?>
                            <td class="text-center"><?php echo ($v["user_name"]); ?></td>
                            <td class="text-center"><?php echo ($v["user_phone"]); ?></td>
                            <td class="text-center"><span class="label label-success"
                                      style="cursor:pointer;font-size:12px;font-weight:1;border-radius: 5px;"
                                      onclick="A_Rate(<?php echo ($v['id']); ?>,<?php echo ($v['pid']); ?>,'<?php echo ($v["user_name"]); ?>')">详情</span></td>
                            <td class="text-center">
                                <?php if($v["status"] == 1): ?><span class="label label-danger"
                                          style="font-size:12px;font-weight:1;border-radius: 5px;">开启</span>
                                    <?php else: ?>
                                    <span class="label label-default"
                                          style="font-size:12px;font-weight:1;border-radius: 5px;">禁用</span><?php endif; ?>
                            </td>
                            <!--<td class="text-center">-->
                                <!--<?php if($v["auth_status"] == 1): ?>-->
                                    <!--<span class="label label-danger"-->
                                          <!--style="font-size:12px;font-weight:1;border-radius: 5px;">开启</span>-->
                                    <!--<?php else: ?>-->
                                    <!--<span class="label label-default"-->
                                          <!--style="font-size:12px;font-weight:1;border-radius: 5px;">禁用</span>-->
                                <!--<?php endif; ?>-->
                            <!--</td>-->
                            <td class="text-center"><?php echo (date('Y-m-d H:i:s',$v['ctime'])); ?></td>
                            <!--<td class="text-center" style="cursor: pointer" onclick="agentCode('<?php echo (agentCode($v["id"])); ?>')">点击查看</td>-->
                            <td class="text-center">
                                <div class="btn-group btn-group-sm dropup ">
                                    <button class="btn btn-default btn-active-purple dropdown-toggle" data-toggle="dropdown" type="button">
                                        操作 <i class="dropdown-caret caret-up"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        <li class="dropdown-header">请选择操作</li>
                                        <li><a href="<?php echo U('lists',array('pid'=>$v['id']));?>">子代理</a></li>
                                        <li class="divider"></li>
                                        <li><a href="JavaScript:;" onclick="edit(<?php echo ($v["id"]); ?>)">信息详情</a></li>
                                        <li class="divider"></li>
                                        <li><a href="javaScript:;" class="restPassword" data-id="<?php echo ($v["id"]); ?>" data-name="<?php echo ($v["user_name"]); ?>|<?php echo ($v["user_phone"]); ?>">重置密码</a></li>
                                        <li><a href="<?php echo U('agent_login',array('id'=>$v['id']));?>" target="_blank">登入代理端</a></li>
                                    </ul>
                                </div>


                                <!--<a href="<?php echo U('lists',array('pid'=>$v['id']));?>" class="btn btn-mint btn-rounded btn-sm">子代理</a>-->
                                <!--<button class="btn btn-purple btn-sm" type="submit" onclick="edit(<?php echo ($v["id"]); ?>)">信息详情-->
                                <!--</button>-->
                                <!--<button class="btn btn-success btn-sm" type="submit" onclick="getTree(<?php echo ($v["id"]); ?>)">层次分析</button>-->
                            </td>
                        </tr><?php endforeach; endif; ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="11">
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


<!-- 模态框开始 -->
<div class="modal fade" id="Agent-add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width: 800px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <i class="pci-cross pci-circle"></i>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    添加代理
                </h4>
            </div>
            <div class="modal-body">
                <form action="<?php echo U('adds');?>" method="post">
                    <div class="panel-body">
                        <div id="Set">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="control-label">代理姓名</label>
                                        <input class="form-control" type="text" name="user_name" required>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="control-label">联系电话</label>
                                        <input class="form-control" type="text" name="user_phone" required>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="control-label">所属省份</label>
                                        <select data-placeholder="请选择..." tabindex="10" class="form-control"
                                                name="province" id="province_add" >
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="control-label">所属城市</label>
                                        <select class="form-control" name="city" >
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="control-label">所属县级</label>
                                        <select class="form-control" name="district" >
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="control-label">详细地址</label>
                                        <input class="form-control" type="text" name="address" >
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="control-label">代理状态</label>
                                    <select tabindex="2" class="form-control" name="status" required>
                                        <option value="1">开启</option>
                                        <option value="0">禁用</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3" style="display: none">
                                <div class="form-group">
                                    <label class="control-label">自助发码</label>
                                    <select tabindex="2" class="form-control" name="ma_status" required>
                                        <option value="1">开启</option>
                                        <option value="0" selected>禁用</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="control-label">发展下级</label>
                                    <select tabindex="2" class="input-md form-control" name="x_status" required>
                                        <option value="1">开启</option>
                                        <option value="0">禁用</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3" style="display: none">
                                <div class="form-group">
                                    <label class="control-label">认证费用</label>
                                    <select tabindex="2" class="form-control" name="auth_status" >
                                        <option value="1">开启</option>
                                        <option value="0" selected>禁用</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">结算账户</label>
                                    <input class="form-control" type="text" name="account"
                                           placeholder="结算信息  此处只作为系统记录使用">
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-sm-6" style="display: none">
                                <div class="form-group">
                                    <label class="control-label">认证返佣金额(元)</label>
                                    <input class="form-control" type="text" name="auth_fee" value="0" placeholder="只有开启认证费用才计算"
                                           >
                                </div>
                            </div>


                        </div>
                        <div class="row" style="display: none">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">AD-信用卡链接</label>
                                    <input class="form-control" type="url" name="ad1" placeholder="填写链接地址系统自动生成链接二维码 为空不显示">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">AD-贷款链接</label>
                                    <input class="form-control" type="url" name="ad2" placeholder="填写链接地址系统自动生成链接二维码 为空不显示">
                                </div>
                            </div>
                        </div>
                        <?php if(is_admin()){ ?>
                        <p style="border-bottom: 1px solid #dbdbdb;padding: 5px 0">邀请码关联配置(如下面配置 将参与推荐码分润汇总)</p>
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label">代理级别</label>
                                    <select tabindex="2" class="input-md form-control" name="grade">
                                        <option value="">-请选择-</option>
                                        <option value="1">服务商</option>
                                        <option value="2">省代</option>
                                        <option value="3">市代</option>
                                        <option value="4">区代</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label">缴费状态</label>
                                    <select tabindex="2" class="input-md form-control" name="pay_status">
                                        <option value="0" selected>未交费</option>
                                        <option value="1">已缴费</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label">邀请码</label>
                                    <input class="form-control" type="text" name="invite_code"  placeholder="代理的邀请码 六位字符串">
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="control-label">备注</label>
                                    <input class="form-control" type="text" name="AgentInfo" placeholder="备注信息"
                                           required>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="panel-footer text-right">
                        
                        <input type="hidden" name="pid" value="<?php if(isset($_GET['pid'])): echo ($_GET['pid']); else: ?> 0<?php endif; ?>">
                        <button class="btn btn-success" type="submit">提交</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- 模态框关闭 -->


<!-- 模态框开始 -->
<div class="modal fade" id="Agent-edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width: 800px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <i class="pci-cross pci-circle"></i>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    代理详情
                </h4>
            </div>
            <div class="modal-body">
                <form action="<?php echo U('edits');?>" method="post">
                    <div class="panel-body">
                        <div id="Set_edit">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="control-label">代理姓名</label>
                                        <input class="form-control" type="text" name="user_name" required>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="control-label">联系电话</label>
                                        <input class="form-control" type="text" name="user_phone" required>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="control-label">所属省份</label>
                                        <select class="form-control" name="province" required>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="control-label">所属城市</label>
                                        <select class="form-control" name="city" required>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="control-label">所属县级</label>
                                        <select class="form-control" name="district" required>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="control-label">详细地址</label>
                                        <input class="form-control" type="text" name="address" required>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="control-label">代理状态</label>
                                    <select tabindex="2" class="form-control" name="status" required>
                                        <option value="1">开启</option>
                                        <option value="0">禁用</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="control-label">发展下级</label>
                                    <select tabindex="2" class="input-md form-control" name="x_status" required>
                                        <option value="1">开启</option>
                                        <option value="0">禁用</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">结算账户</label>
                                    <input class="form-control" type="text" name="account"  placeholder="结算信息  此处只作为系统记录使用">
                                </div>
                            </div>
                        </div>
                        <?php if(is_admin()){ ?>
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label">代理级别</label>
                                    <select tabindex="2" class="input-md form-control" name="grade">
                                        <option value="">-请选择-</option>
                                        <option value="1">服务商</option>
                                        <option value="2">省代</option>
                                        <option value="3">市代</option>
                                        <option value="4">区代</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label">缴费状态</label>
                                    <select tabindex="2" class="input-md form-control" name="pay_status">
                                        <option value="1">已缴费</option>
                                        <option value="0" selected>未交费</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label">邀请码</label>
                                    <input class="form-control" type="text" name="invite_code"  placeholder="代理的要求码 六位字符串">
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="control-label">备注</label>
                                    <input class="form-control" type="text" name="AgentInfo" placeholder="备注信息"
                                           required>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="panel-footer text-right">
                        <input type="hidden" name="id" value="">
                        <input type="hidden" name="pid"
                               value="<?php if(isset($_GET['pid'])): echo ($_GET['pid']); else: ?> 0<?php endif; ?>">
                        <button class="btn btn-success" type="button" id="edit_button">编辑</button>
                        <button class="btn btn-info" type="button" id="not_button">取消编辑</button>
                        <button class="btn btn-danger" type="submit" id="sub_button">提交</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!--代理费率配置-->
<!--代理费率结束-->


<!-- 模态框关闭 -->
<script type="text/javascript" src="/Source/plug/CSXClass/distpicker.min.js"></script>
<script>
    $(function () {
        $("#SerCh").distpicker({
            province: "---- 所在省 ----",
            city: "---- 所在市 ----",
            district: "---- 所在区 ----"
        });
        $("#Set").distpicker({placeholder: false});
        //$("#Set_edit").distpicker({placeholder: false});
        $("#province_add").chosen();
        $("#province_edit").chosen();
    });

    $('#AgentAdd').click(function () {
        $('.chosen-container').css('width', '250px');
        $('#Agent-add input').val('');
        $('[name="pid"]').val('<?php echo ($_GET['pid']); ?>');
        $('#Agent-add').modal('show');
    });

    $('.restPassword').click(function () {
        var mchId=$(this).data('id'),mchName=$(this).data('name');
        layer.prompt({title: '重置密码('+mchName+')', formType: 0}, function(text){
            var ajax_data ={'id':mchId,'pass':text};
            $.post("<?php echo U('restPassword');?>", ajax_data, function(data){
                if(data.status == 1){
                    layer.closeAll();
                    layer.alert(data.info);
                }else{
                    layer.msg( data.info, function(){
                    });
                }
            }, 'json');

        });
    });

    function agentCode(data) {
        layer.open({
            type: 2,
            title:'代理邀请码',
            area: ['360px', '600px'],
            fixed: false, //不固定
            maxmin: false,
            content: data
        });
    }

    function edit(id) {
        if (id == '') {
            $.niftyNoty({
                type: 'danger',
                message: '<strong>请刷新后重试</strong>',
                container: 'floating',
                timer: 5000
            });
        }
        actionurl = "<?php echo U('detail');?>";
        ajax_data = {id: id};
        $.post(actionurl, ajax_data, function (data) {
            if (data.status == 1) {
                $('#Agent-edit input ').attr('disabled', true);
                $('#Agent-edit select').attr('disabled', true);
                $('#sub_button,#not_button').hide();
                $("[name='user_name']").val(data.info.user_name);
                $("[name='user_phone']").val(data.info.user_phone);
                $("[name='address']").val(data.info.address);
                $("[name='T1_COST']").val(data.info.t1_cost);
                $("[name='T0_COST']").val(data.info.t0_cost);
                $("[name='T1_TERM']").val(data.info.t1_term);
                $("[name='T0_TERM']").val(data.info.t0_term);
                $("[name='auth_fee']").val(data.info.auth_fee);
                $("[name='account']").val(data.info.account);
                $("[name='AgentInfo']").val(data.info.agentinfo);
                $("[name='pid']").val(data.info.pid);
                $("[name='id']").val(data.info.id);
                $("[name='ad1']").val(data.info.ad1);
                $("[name='ad2']").val(data.info.ad2);
                <?php if(is_admin()){ ?>
                    $("[name='invite_code']").val(data.info.invite_code);
                    $("[name='grade'] option[value='" + data.info.grade + "']").attr("selected", true);
                    $("[name='pay_status'] option[value='" + data.info.pay_status + "']").attr("selected", true);
                <?php } ?>
                $("#Set_edit").distpicker({
                    province: '' + data.info.province + '',
                    city: '' + data.info.city + '',
                    district: '' + data.info.district + ''
                });
                $("[name='status'] option[value='" + data.info.status + "']").attr("selected", true);
                $("[name='ma_status'] option[value='" + data.info.ma_status + "']").attr("selected", true);
                $("[name='x_status'] option[value='" + data.info.x_status + "']").attr("selected", true);
                $("[name='auth_status'] option[value='" + data.info.auth_status + "']").attr("selected", true);
                $('#Agent-edit').modal('show');
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


    $('#edit_button').click(function () {
        $('#Agent-edit input ').attr('disabled', false);
        $('#Agent-edit select').attr('disabled', false);
        $('#sub_button,#not_button').show();
        $('#edit_button').hide();
    });
    $('#not_button').click(function () {
        $('#Agent-edit input ').attr('disabled', true);
        $('#Agent-edit select').attr('disabled', true);
        $('#sub_button,#not_button').hide();
        $('#edit_button').show();
    });

    $('.close').click(function () {
        $('#Agent-edit input ').attr('disabled', true);
        $('#Agent-edit select').attr('disabled', true);
        $('#sub_button,#not_button').hide();
        $('#edit_button').show();
    });


    function A_Rate(id, pid,name) {
        layer.open({
         type: 2,
         title:'代理姓名:'+name+'-通道费率配置',
         area: '500px',
         fixed: true, //不固定
         maxmin: false,
         offset: '50px',
         content: '<?php echo U("A_Rate");?>/id/'+id+'/pid/'+pid,
            success: function(layero, index) {
                layer.iframeAuto(index);
            }
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