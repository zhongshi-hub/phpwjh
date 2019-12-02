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
    .add_template{
        font-size: 12px;margin-left: 20px;color: #097cff;border:1px dashed #097cff;padding: 2px;cursor: pointer;
    }
</style>
<div id="content-container">
    <div id="page-content">
        <form method="post" action="<?php echo U('pay_config');?>" ajax="yes">
            <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title"><?php echo $title;?></h3>
                </div>
                <div class="panel-body">
                    <p style="border-bottom: solid 1px #E4E4E4;font-size: 16px;padding-bottom: 5px;">授权相关(获取支付宝用户UserId)</p>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">支付宝AppId</label>
                                <input class="form-control" name="ali_appid" type="text"
                                       placeholder="用户支付宝支付 获取用户信息" value="<?php echo ($data["ali_appid"]); ?>" required>
                            </div>
                        </div>
                    </div>
                    <p style="color:red;border-bottom: solid 1px #E4E4E4;font-size: 16px;padding-bottom: 5px;">支付宝ISV服务商</p>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">服务商PID</label>
                                <input class="form-control" name="ali_isv_pid" type="text"
                                       placeholder="支付宝ISV服务商PID" value="<?php echo ($data["ali_isv_pid"]); ?>">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">服务商AppId</label>
                                <input class="form-control" name="ali_isv_appid" type="text"
                                       placeholder="支付宝ISV服务商三方应用AppId" value="<?php echo ($data["ali_isv_appid"]); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">应用私钥</label>
                                <textarea style="resize:none"  class="form-control" name="ali_isv_private_key" placeholder="应用私钥PrivateKey"><?php echo ($data["ali_isv_private_key"]); ?></textarea>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">支付宝公钥</label>
                                <textarea style="resize:none"  class="form-control" name="ali_isv_public_key" placeholder="支付宝公钥AlipayPublicKey"><?php echo ($data["ali_isv_public_key"]); ?></textarea>
                            </div>
                        </div>
                    </div>
                    <p style="color:red;border-bottom: solid 1px #E4E4E4;font-size: 16px;padding-bottom: 5px;">微信支付服务商(需在下方配置的支付公众号同级服务商)</p>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">服务商AppId</label>
                                <input class="form-control" name="xun_wxpay_appid" type="text"
                                       placeholder="服务商AppId" value="<?php echo ($data["xun_wxpay_appid"]); ?>">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">服务商商户号</label>
                                <input class="form-control" name="xun_wxpay_mch_id" type="text"
                                       placeholder="微信支付服务商商户号" value="<?php echo ($data["xun_wxpay_mch_id"]); ?>">
                            </div>
                        </div>

                    </div>
                    <div class="row">

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">API密钥</label>
                                <input class="form-control" name="xun_wxpay_key" type="password"
                                       placeholder="服务商交易API密钥" value="<?php echo ($data["xun_wxpay_key"]); ?>">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">APIV3秘钥</label>
                                <input class="form-control" name="xun_wxpay_v3key" type="password"
                                       placeholder="APIV3秘钥" value="<?php echo ($data["xun_wxpay_v3key"]); ?>">
                            </div>
                        </div>
                    </div>
                    <p style="border-bottom: solid 1px #E4E4E4;font-size: 16px;padding-bottom: 5px;">微信支付(基础/获取用户信息)</p>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">支付公众号</label>
                                <select data-placeholder="请选择..." id="pay_wx" name="pay_wxid" class="form-control">
                                    <?php if(is_array($weixin)): foreach($weixin as $key=>$v): ?><option value="<?php echo ($v["id"]); ?>"
                                        <?php if($data["pay_wxid"] == $v['id']): ?>selected<?php endif; ?>
                                        ><?php echo ($v["name"]); ?></option><?php endforeach; endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">商户公众号</label>
                                <select data-placeholder="请选择..." id="mch_wx" name="mch_wxid" class="form-control">
                                    <?php if(is_array($weixin)): foreach($weixin as $key=>$v): ?><option value="<?php echo ($v["id"]); ?>"
                                        <?php if($data["mch_wxid"] == $v['id']): ?>selected<?php endif; ?>
                                        ><?php echo ($v["name"]); ?></option><?php endforeach; endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <p style="color:red;border-bottom: solid 1px #E4E4E4;font-size: 16px;padding-bottom: 5px;">模板消息-付款成功</p>
                    <div class="row">
                        <div class="col-sm-5">
                            <div class="form-group">
                                <label class="control-label">付款成功模板ID<span class="add_template" onclick="add_template('OPENTM402074550');">一键添加模板ID</span></label>
                                <input class="form-control" id="OPENTM402074550" name="pay_template_id" type="text" placeholder="模板消息编号:OPENTM402074550 只针对微信支付通知" value="<?php echo ($data["pay_template_id"]); ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">付款成功头部信息</label>
                                <input class="form-control" name="pay_first" type="text" placeholder="您好,您已付款成功!"
                                       value="<?php echo ($data["pay_first"]); ?>">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">付款成功底部信息</label>
                                <input class="form-control" name="pay_remark" type="text" placeholder="如有疑问,请联系XX"
                                       value="<?php echo ($data["pay_remark"]); ?>">
                            </div>
                        </div>
                    </div>
                    <p style="color:red;border-bottom: solid 1px #E4E4E4;font-size: 16px;padding-bottom: 5px;">模板消息-收款成功</p>
                    <div class="row">
                        <div class="col-sm-5">
                            <div class="form-group">
                                <label class="control-label">收款成功模板ID<span class="add_template" onclick="add_template('OPENTM408239595');">一键添加模板ID</span></label>
                                <input id="OPENTM408239595" class="form-control" name="mch_template_id" type="text"
                                       placeholder="模板消息编号:OPENTM408239595 只针对商家收款通知" value="<?php echo ($data["mch_template_id"]); ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">收款成功头部信息</label>
                                <input class="form-control" name="mch_first" type="text" placeholder="您好,您有一笔新的收款订单!"
                                       value="<?php echo ($data["mch_first"]); ?>">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">收款成功底部信息</label>
                                <input class="form-control" name="mch_remark" type="text" placeholder="如有疑问,请联系XX"
                                       value="<?php echo ($data["mch_remark"]); ?>">
                            </div>
                        </div>
                    </div>
                    <p style="color:red;border-bottom: solid 1px #E4E4E4;font-size: 16px;padding-bottom: 5px;">模板消息-提现成功</p>
                    <div class="row">
                        <div class="col-sm-5">
                            <div class="form-group">
                                <label class="control-label">提现到账通知模板ID <span class="add_template" onclick="add_template('OPENTM407639519');">一键添加模板ID</span></label>
                                <input class="form-control" id="OPENTM407639519" name="tx_template_id" type="text" placeholder="模板消息编号:OPENTM407639519  提现到账通知  只针对微信支付通知" value="<?php echo ($data["tx_template_id"]); ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">提现成功头部信息</label>
                                <input class="form-control" name="tx_first" type="text" placeholder="您好,您的提现已到账"
                                       value="<?php echo ($data["tx_first"]); ?>">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">提现成功底部信息</label>
                                <input class="form-control" name="tx_remark" type="text" placeholder="如有疑问,请联系XX"
                                       value="<?php echo ($data["tx_remark"]); ?>">
                            </div>
                        </div>
                    </div>

                    <p style="color:red;border-bottom: solid 1px #E4E4E4;font-size: 16px;padding-bottom: 5px;">模板消息-商户注册成功</p>
                    <div class="row">
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="control-label">是否开启</label>
                                <select data-placeholder="请选择..." tabindex="2" class="form-control" name="reg_template_status">
                                    <option value="1"
                                    <?php if(($data["reg_template_status"]) == "1"): ?>selected<?php endif; ?>
                                    >开启</option>
                                    <option value="0"
                                    <?php if(($data["reg_template_status"]) == "0"): ?>selected<?php endif; ?>
                                    >关闭</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-5">
                            <div class="form-group">
                                <label class="control-label">商户注册入驻成功模板ID<span class="add_template" onclick="add_template('OPENTM413387262');">一键添加模板ID</span></label>
                                <input class="form-control" id="OPENTM413387262" name="reg_template_id" type="text" placeholder="模板消息编号:OPENTM413387262 入驻成功通知" value="<?php echo ($data["reg_template_id"]); ?>">
                            </div>
                        </div>
                    </div>
                    <p style="color:red;border-bottom: solid 1px #E4E4E4;font-size: 16px;padding-bottom: 5px;">模板消息-商户审核结果</p>
                    <div class="row">
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="control-label">是否开启</label>
                                <select data-placeholder="请选择..." tabindex="2" class="form-control" name="sh_template_status">
                                    <option value="1"
                                    <?php if(($data["sh_template_status"]) == "1"): ?>selected<?php endif; ?>
                                    >开启</option>
                                    <option value="0"
                                    <?php if(($data["sh_template_status"]) == "0"): ?>selected<?php endif; ?>
                                    >关闭</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-5">
                            <div class="form-group">
                                <label class="control-label">审核结果模板ID <span class="add_template" onclick="add_template('OPENTM402193293');">一键添加模板ID</span></label>
                                <input class="form-control" id="OPENTM402193293" name="sh_template_id" type="text" placeholder="模板消息编号:OPENTM402193293 商户审核通知" value="<?php echo ($data["sh_template_id"]); ?>">
                            </div>
                        </div>
                    </div>
                    <!--<p style="color:red;border-bottom: solid 1px #E4E4E4;font-size: 16px;padding-bottom: 5px;">模板消息-商户信用认证(待认证/认证成功)</p>-->
                    <!--<div class="row">-->
                        <!--<div class="col-sm-2">-->
                            <!--<div class="form-group">-->
                                <!--<label class="control-label">是否开启</label>-->
                                <!--<select data-placeholder="请选择..." tabindex="2" class="form-control" name="auth_template_status">-->
                                    <!--<option value="1"-->
                                    <!--<?php if(($data["auth_template_status"]) == "1"): ?>selected<?php endif; ?>-->
                                    <!--&gt;开启</option>-->
                                    <!--<option value="0"-->
                                    <!--<?php if(($data["auth_template_status"]) == "0"): ?>selected<?php endif; ?>-->
                                    <!--&gt;关闭</option>-->
                                <!--</select>-->
                            <!--</div>-->
                        <!--</div>-->
                        <!--<div class="col-sm-5">-->
                            <!--<div class="form-group">-->
                                <!--<label class="control-label">商户信用认证模板ID <span class="add_template" onclick="add_template('OPENTM204559869');">一键添加模板ID</span></label>-->
                                <!--<input class="form-control" id="OPENTM204559869" name="auth_template_id" type="text" placeholder="模板消息编号:OPENTM204559869  认证通知" value="<?php echo ($data["auth_template_id"]); ?>">-->
                            <!--</div>-->
                        <!--</div>-->
                    <!--</div>-->
                    <p style="color:red;border-bottom: solid 1px #E4E4E4;font-size: 16px;padding-bottom: 5px;">模板消息-通道开通成功</p>
                    <div class="row">
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="control-label">是否开启</label>
                                <select data-placeholder="请选择..." tabindex="2" class="form-control" name="alleys_template_status">
                                    <option value="1"
                                    <?php if(($data["alleys_template_status"]) == "1"): ?>selected<?php endif; ?>
                                    >开启</option>
                                    <option value="0"
                                    <?php if(($data["alleys_template_status"]) == "0"): ?>selected<?php endif; ?>
                                    >关闭</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-5">
                            <div class="form-group">
                                <label class="control-label">商户通道开通成功模板ID<span class="add_template" onclick="add_template('OPENTM413424468');">一键添加模板ID</span></label>
                                <input class="form-control" id="OPENTM413424468" name="alleys_template_id" type="text" placeholder="模板消息编号:OPENTM413424468 开通成功通知" value="<?php echo ($data["alleys_template_id"]); ?>">
                            </div>
                        </div>
                    </div>
                    <!--<p style="color:red;border-bottom: solid 1px #E4E4E4;font-size: 16px;padding-bottom: 5px;">模板消息-通道业务变更</p>-->
                    <!--<div class="row">-->
                        <!--<div class="col-sm-2">-->
                            <!--<div class="form-group">-->
                                <!--<label class="control-label">是否开启</label>-->
                                <!--<select data-placeholder="请选择..." tabindex="2" class="form-control" name="alter_template_status">-->
                                    <!--<option value="1"-->
                                    <!--<?php if(($data["alter_template_status"]) == "1"): ?>selected<?php endif; ?>-->
                                    <!--&gt;开启</option>-->
                                    <!--<option value="0"-->
                                    <!--<?php if(($data["alter_template_status"]) == "0"): ?>selected<?php endif; ?>-->
                                    <!--&gt;关闭</option>-->
                                <!--</select>-->
                            <!--</div>-->
                        <!--</div>-->
                        <!--<div class="col-sm-5">-->
                            <!--<div class="form-group">-->
                                <!--<label class="control-label">通道业务变更模板ID<span class="add_template" onclick="add_template('OPENTM406673418');">一键添加模板ID</span></label>-->
                                <!--<input class="form-control" id="OPENTM406673418" name="alter_template_id" type="text" placeholder="模板消息编号:OPENTM406673418 业务状态变更通知" value="<?php echo ($data["alter_template_id"]); ?>">-->
                            <!--</div>-->
                        <!--</div>-->
                    <!--</div>-->
                    <p style="color:red;border-bottom: solid 1px #E4E4E4;font-size: 16px;padding-bottom: 5px;">模板消息-审核员接收商户注册通知</p>
                    <div class="row">
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="control-label">是否开启</label>
                                <select data-placeholder="请选择..." tabindex="2" class="form-control" name="sh_user_template_status">
                                    <option value="1"
                                    <?php if(($data["sh_user_template_status"]) == "1"): ?>selected<?php endif; ?>
                                    >开启</option>
                                    <option value="0"
                                    <?php if(($data["sh_user_template_status"]) == "0"): ?>selected<?php endif; ?>
                                    >关闭</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-5">
                            <div class="form-group">
                                <label class="control-label">审核员接收商户注册通知模板ID<span class="add_template" onclick="add_template('OPENTM207173353');">一键添加模板ID</span></label>
                                <input class="form-control" id="OPENTM207173353" name="sh_user_template_id" type="text" placeholder="模板消息编号:OPENTM207173353 注册提醒" value="<?php echo ($data["sh_user_template_id"]); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label">接收者OPENID(商户详情或微信用户里查看OPENID)</label>
                                <input class="form-control" name="sh_user_data" type="text" placeholder="多个OPENID 请用|区隔 例: XXXX|OOOO|UUUU" value="<?php echo ($data["sh_user_data"]); ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-footer text-right">
                    <button type="submit" class="btn btn-success">提交</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    $(function() {
        $('#pay_wx,#mch_wx').chosen({search_contains: true, no_results_text: '没有找到相关的数据'});
    });

    function add_template(id) {
        layer.alert('一键增加会增加到当前页面配置的商户公众号内,添加成功自动添加模板ID 您确定要一键添加吗?', {
            title:'温馨提示',
            skin: 'layui-layer-molv'
            ,closeBtn: 1
            ,anim: 6 //动画类型
        },function(){
            layer.msg('模板添加中...请等待结果...', {
                icon: 16,
                shade: 0.01,
                time:300000
            });
            var ajax_data ={'template_id':id};
            var actionurl ='<?php echo U("add_template",array("Debug"=>"1"));?>';
            $.post(actionurl, ajax_data, function (data) {
                if (data.status == 1) {
                     layer.closeAll();
                     $('#'+id).val(data.info);
                     $.niftyNoty({
                        type: 'success',
                        message : '<strong>模板增加成功!模板ID已自动填充到数据框内</strong>',
                        container : 'floating',
                        timer : 3000
                     });
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