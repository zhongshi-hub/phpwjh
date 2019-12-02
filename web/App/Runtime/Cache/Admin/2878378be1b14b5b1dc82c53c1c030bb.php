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
    body{
        -moz-osx-font-smoothing:auto!important;
    }
</style>
<script>
    $(function() {
        $('select').chosen();
    });
</script>
<div id="content-container">
    <div id="page-content">
        <div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title">交易列表</h3>
            </div>
            <!--筛选功能开始-->
            <div class="row">
                <div class="col-sm-12">
                        <form method="post" action="<?php echo U('index');?>">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label class="control-label">交易单号</label>
                                            <input class="form-control" type="text" placeholder="支持订单号泛查询" name="trade_no" value="<?php echo ($data["trade_no"]); ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label class="control-label">所属代理</label>
                                            <input id="dialog_name" class="form-control dialog" type="text"  value="">
                                            <input id="dialog_aid" class="form-control" type="hidden" name="aid" value="">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label class="control-label">所属商户</label>
                                            <input id="dialog_mch_name" class="form-control mid_dialog" type="text"  value="">
                                            <input id="dialog_mid" class="form-control" type="hidden" name="mid" value="">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label class="control-label">门店名称</label>
                                            <input id="dialog_store_name" class="form-control store_dialog" type="text"  value="">
                                            <input id="dialog_store_id" class="form-control" type="hidden" name="store_id" value="">
                                        </div>
                                    </div>
                                    <!--<div class="col-sm-3">
                                        <div class="form-group">
                                            <label class="control-label">支付类型</label>
                                            <select data-placeholder="请选择..." id="paytype" tabindex="2" class="form-control" name="paytype">
                                                <option value="" >--请选择--</option>
                                                <option value="pay.weixin.jspay" <?php if(($data["paytype"]) == "pay.weixin.jspay"): ?>selected="selected"<?php endif; ?>>微信JS支付</option>
                                                <option value="pay.weixin.native" <?php if(($data["paytype"]) == "pay.weixin.native"): ?>selected="selected"<?php endif; ?>>微信扫码支付</option>
                                                <option value="pay.alipay.jspay" <?php if(($data["paytype"]) == "pay.alipay.jspay"): ?>selected="selected"<?php endif; ?>>支付宝JS支付</option>
                                                <option value="pay.alipay.native" <?php if(($data["paytype"]) == "pay.alipay.native"): ?>selected="selected"<?php endif; ?>>支付宝扫码支付</option>
                                                <option value="unified.trade.micropay" <?php if(($data["paytype"]) == "unified.trade.micropay"): ?>selected="selected"<?php endif; ?>>微信/支付宝条码支付</option>
                                            </select>
                                        </div>
                                    </div>-->


                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="control-label">时间范围</label>
                                            <div class="input-daterange input-group" >
                                                <input type="text" class="form-control" id="STime" name="s_time" value="<?php echo ($data["s_time"]); ?>"/>
                                                <span class="input-group-addon">To</span>
                                                <input type="text" class="form-control" id="ETime" name="e_time" value="<?php echo ($data["e_time"]); ?>"/>
                                                <span class="input-group-addon btn"  style="border-width:1px;" onclick="SetTime(1)">昨天</span>
                                                <span class="input-group-addon btn"  style="border-width:1px;" onclick="SetTime(2)">今天</span>
                                                <span class="input-group-addon btn"  style="border-width:1px;" onclick="SetTime(3)">近七天</span>
                                                <span class="input-group-addon btn"  style="border-width:1px;" onclick="SetTime(4)">近一月</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label class="control-label">通道类型</label>
                                            <select data-placeholder="请选择..." id="type" tabindex="2" class="form-control" name="type">
                                                <option value="" >--请选择--</option>
                                                <option value="T1" <?php if(($data["type"]) == "T1"): ?>selected="selected"<?php endif; ?> >T1</option>
                                                <option value="T0" <?php if(($data["type"]) == "T0"): ?>selected="selected"<?php endif; ?>>T0</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label class="control-label">支付通道</label>
                                            <select data-placeholder="请选择..."  tabindex="2" class="form-control" name="alleys">
                                                <option value="" >--请选择--</option>
                                                <?php if(is_array($alleys)): foreach($alleys as $key=>$_v): ?><option value="<?php echo ($_v); ?>" <?php if(($data["alleys"]) == $_v): ?>selected="selected"<?php endif; ?>><?php echo (alleys_name($_v)); ?></option><?php endforeach; endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <button class="btn btn-success" type="submit">搜索</button>
                                    <button class="btn btn-default" value="ccl" name="export" type="submit"> <i class="fa fa-download"></i>导出</button>
                                </div>
                            </div>
                        </form>
                </div>
            </div>
            <!--筛选功能结束-->

            <div class="panel-body">

                <table  class="table  table-bordered table-hover toggle-circle text-center" data-page-size="7">
                    <thead>
                    <tr>
                        <th class="text-center">所属代理</th>
                        <th class="text-center">所属商户</th>
                        <th class="text-center">门店名称</th>
                        <th class="text-center">交易类型</th>
                        <th class="text-center">交易场景</th>
                        <th class="text-center">交易时间</th>
                        <th class="text-center">完成时间</th>
                        <th class="text-center">交易金额</th>
                        <th class="text-center">交易单号</th>
                        <th class="text-center">交易状态</th>
                        <!--<th class="text-center">通道类型</th>-->
                        <th class="text-center">支付通道</th>
                        <!--<th class="text-center">通知状态</th>-->
                        <!--<th class="text-center">通知补发</th>-->
                        <!--<th class="text-center">申请退款</th>-->
                    </tr>
                    </thead>
                    <tbody>
                    <?php if(is_array($list)): foreach($list as $key=>$v): ?><tr>
                            <td><?php $agent=Get_Agent($v['mid']); echo $agent['user_name']; ?></td>
                            <td style="width: 120px"><?php $seller=Get_Seller($v['mid']); echo $seller['mch_name']; ?></td>
                            <td style="width: 120px"><?php $store=Get_Store($v['store_id']); echo $store['name']; ?></td>
                            <td><span class="label label-success" style="background-color:<?php echo pay_type_color($v['service']); ?>;font-size:12px;font-weight:1;border-radius: 5px;" title="<?php echo pays_types($v['service'],true); ?>"><?php echo (pays_types($v['service'])); ?></span></td>
                            <td>
                                <?php $key=explode('_',$v['service']);if($key[1]!='refund'){ ?>
                                <span class="label label-success" style="background-color:#00bcd4;font-size:12px;font-weight:1;border-radius: 5px;">收款</span>
                                <?php }else{ ?>
                                <span class="label label-success" style="background-color:#c16e00;font-size:12px;font-weight:1;border-radius: 5px;cursor: pointer" title="<?php echo ($v["goods_tag"]); ?>">退款</span>
                                <?php } ?>
                            </td>
                            <td><?php echo (date('Y-m-d H:i:s',$v['createtime'])); ?></td>
                            <td><?php echo (date('Y-m-d H:i:s',$v['time_end'])); ?></td>
                            <td><span class="label label-danger" style="font-size:12px;font-weight:1;border-radius: 5px;"><?php echo ($v['total_fee']); ?></span></td>
                            <td><a href="#popover" class="add-popover" data-original-title="订单详情" data-content="<?php if($key[1]=='refund'){echo '原订单号:   '.$v['out_transaction_id']; } ?>    官方订单号: <?php echo ($v['transaction_id']); ?>  " data-placement="top" data-trigger="focus" data-toggle="popover"><?php echo ($v['out_trade_no']); ?></a></td>
                            <td><?php echo (pays_status($v['status'])); ?></td>
                            <!--<td><?php echo ($v['type']); ?></td>-->
                            <td class="text-center"><span class="label label-dark" style="background-color:<?php echo alleys_color($v['alleys']); ?>;font-size:12px;font-weight:1;border-radius: 5px;cursor: pointer;"><?php echo (alleys_name($v["alleys"])); ?></span></td>
                            <!--<td>-->
                                <!--<?php $status=get_api_notify($v['out_trade_no'],'send_status');if($status==1){ ?>-->
                                <!--<span class="label label-success" style="font-size:12px;font-weight:1;border-radius: 5px;">成功</span></td>-->
                                <!--<?php }else{ ?>-->
                                <!--<span class="label label-default" style="font-size:12px;font-weight:1;border-radius: 5px;">失败</span></td>-->
                                <!--<?php } ?>-->
                            <!--<td><button class="label label-mint reload" data-id="<?php echo ($v['out_trade_no']); ?>" style="border: 1px solid transparent;cursor: pointer;font-size:12px;font-weight:1;border-radius: 5px;" >补发</button></td>-->
                            <!--<td><button class="label <?php $r=is_method_refund($v['alleys']);if(false==$r){echo 'label-default';}else{echo 'label-pink';} ?>  refund" data-id="<?php echo ($v['out_trade_no']); ?>" style="border: 1px solid transparent;cursor: pointer;font-size:12px;font-weight:1;border-radius: 5px;" <?php $r=is_method_refund($v['alleys']);if(false==$r){echo 'disabled';} ?>>退款</button></td>-->
                        </tr><?php endforeach; endif; ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="15">
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
<script>
    $(function(){
        $("#demo-foo-addrow").resizableColumns({
            store: window.store
        });

    });
    //补发通知
    $('.reload').click(function () {
        var id =$(this).data('id');
        layer.alert('<p>您确认要补发异步通知吗?</p><p>确认后系统将异步发送给下游支付结果数据!</p>', {
            title:'提示',
            skin: 'layui-layer-molv'
            ,closeBtn: 1
            ,anim: 6 //动画类型
        },function(){
            $.post("<?php echo U('apiReloadNotify');?>?Debug=1", {'id':id}, function(data){
                if(data.status === 1){
                    layer.alert(data.info,{
                        title:'提示',
                        skin: 'layui-layer-molv'
                        ,closeBtn: 1
                        ,anim: 6
                    },function () {
                        window.location.reload();
                    });
                }else{
                    layer.alert(data.info,{
                        title:'提示',
                        skin: 'layui-layer-molv'
                        ,closeBtn: 1
                        ,anim: 6
                    });
                }
            }, 'json');
        });
    });
    //退款操作
    $('.refund').click(function () {
        var id =$(this).data('id');
        layer.alert('<p>您确认要退款吗?</p><p>退款系统只支持全款退款,不支持自定义退款金额!</p>', {
            title:'提示',
            skin: 'layui-layer-molv'
            ,closeBtn: 1
            ,anim: 6 //动画类型
        },function(){
            $.post("<?php echo U('orderRefund');?>", {'id':id}, function(data){
                if(data.status === 1){
                    layer.msg(data.info);
                    window.location.reload();
                }else{
                    layer.msg(data.info);
                }
            }, 'json');
        });
    });

    $('.dialog').click(function () {
        layer.open({
            type: 2,
            title:'代理业务检索',
            area: ['700px', '530px'],
            fixed: false, //不固定
            maxmin: false,
            content: "<?php echo U('Agent/dialog');?>"
        });
    });
    $('.mid_dialog').click(function () {
        layer.open({
            type: 2,
            title:'商户检索',
            area: ['700px', '530px'],
            fixed: false, //不固定
            maxmin: false,
            content: "<?php echo U('Merchant/mch_dialog');?>"
        });
    });
    $('.store_dialog').click(function () {
        layer.open({
            type: 2,
            title:'门店检索',
            area: ['700px', '530px'],
            fixed: false, //不固定
            maxmin: false,
            content: "<?php echo U('Merchant/store_dialog');?>"
        });
    });

    function SetTime(num){
        switch(num){
            case 1:
                s_time=GetDateStr(-1);
                e_time=GetDateStr(0);
                break;
            case 2:
                s_time=GetDateStr(0);
                e_time=GetDateStr(1);
                break;
            case 3:
                s_time=GetDateStr(-6);
                e_time=GetDateStr(1);
                break;
            case 4:
                s_time=GetDateStr(-30);
                e_time=GetDateStr(1);
                break;
        }
        $('[name="s_time"]').val(s_time);
        $('[name="e_time"]').val(e_time);
    }

    function GetDateStr(Day) {
        var dd = new Date();
        dd.setDate(dd.getDate()+Day);
        var y = dd.getFullYear();
        var m = dd.getMonth()+1;
        var d = dd.getDate();
        return y+"-"+m+"-"+d;
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