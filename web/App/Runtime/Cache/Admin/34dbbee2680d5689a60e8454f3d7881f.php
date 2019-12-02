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
                <h3 class="panel-title"><?php echo $title;?></h3>

            </div>
            <!--筛选功能开始-->
            <div class="row">
                <div class="col-sm-12">
                    <form method="post" action="" ajax="n">
                        <div class="panel-body">
                            <div class="row ">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="control-label">详细信息</label>
                                        <input class="form-control" type="text" placeholder="支持 申请人姓名/联系电话/商户名称" name="search_val" value="">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="control-label">所属代理</label>
                                        <input id="dialog_name" class="form-control dialog" type="text"  value="">
                                        <input id="dialog_aid" class="form-control" type="hidden" name="aid" value="">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="control-label">信用认证</label>
                                        <select  class="form-control" name="auth_status" >
                                            <option value="">所有类型</option>
                                            <option value="1">已认证</option>
                                            <option value="2">未认证</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="control-label">商户类型</label>
                                        <select  class="form-control" name="bus_type" >
                                            <option value="">所有类型</option>
                                            <option value="个人">个人</option>
                                            <option value="企业">企业</option>
                                            <option value="个体户">个体户</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <div class="form-group">
                                        <label class="control-label">微信通道配置</label>
                                        <select  class="form-control" name="wx_alleys">
                                            <option value="">所有类型</option>
                                            <option value="1">已配置通道</option>
                                            <option value="2">未配置通道</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <div class="form-group">
                                        <label class="control-label">支付宝通道配置</label>
                                        <select  class="form-control" name="ali_alleys">
                                            <option value="">所有类型</option>
                                            <option value="1">已配置通道</option>
                                            <option value="2">未配置通道</option>
                                        </select>
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
                <table  class="table table-bordered table-hover toggle-circle table-vcenter" data-page-size="7">
                    <thead>
                    <tr>
                        <th>所属代理</th>
                        <th style="width: 100px">商户名称</th>
                        <th class="text-center">联系电话</th>
                        <th class="text-center">申请人姓名</th>
                        <th class="text-center">类型</th>
                        <th class="text-center">加入时间</th>
                        <!--<th class="text-center">更新时间</th>-->
                        <th class="text-center">微信通道</th>
                        <th class="text-center">支付宝通道</th>
                        <!--<th class="text-center">信用认证</th>-->
                        <th class="text-center">审核短信</th>
                        <th class="text-center">商户状态</th>
                        <th class="text-center">通道配置</th>
                        <th class="text-center">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if(is_array($data)): foreach($data as $key=>$v): ?><tr>
                            <td><?php echo (agent_name($v["agent_id"])); ?></td>
                            <td><?php echo ($v["mch_name"]); ?></td>
                            <td class="text-center"><?php echo ($v["mch_tel"]); ?></td>
                            <td class="text-center"><?php echo ($v["mch_card_name"]); ?></td>
                            <td class="text-center"> <span  class="label label-default" style="color:#999;font-size:12px;font-weight:1;border-radius: 5px;cursor: pointer;"><?php echo ($v["mch_bus_type"]); ?></span></td>
                            <td class="text-center"><?php echo (date('Y-m-d H:i:s',$v['ctime'])); ?></td>
                            <td class="text-center"><span onclick="Alleys_data(<?php echo ($v["id"]); ?>,'wx');" class="label <?php if(alleys_name($v['wx_alleys'])!='未配置通道'){ echo 'label-danger';}else{ echo 'label-dark';} ?>" style="background-color:<?php echo alleys_color($v['wx_alleys']); ?>;font-size:12px;font-weight:1;border-radius: 5px;cursor: pointer;"><?php echo (alleys_name($v["wx_alleys"])); ?></span></td>
                            <td class="text-center"><span onclick="Alleys_data(<?php echo ($v["id"]); ?>,'ali');" class="label <?php if(alleys_name($v['ali_alleys'])!='未配置通道'){ echo 'label-danger';}else{ echo 'label-dark';} ?>" style="background-color:<?php echo alleys_color($v['ali_alleys']); ?>;font-size:12px;font-weight:1;border-radius: 5px;cursor: pointer;"><?php echo (alleys_name($v["ali_alleys"])); ?></span></td>
                            <!--<td class="text-center">-->
                                <!--<?php if($v['auth_status'] == 1): ?>-->
                                    <!--<span class="label label-info" style="font-size:12px;font-weight:1;border-radius: 5px;">已认证</span>-->
                                    <!--<?php else: ?>-->
                                    <!--<span class="label label-default" style="cursor:pointer;font-size:12px;font-weight:1;border-radius: 5px;" onclick="SendAuth(<?php echo ($v["id"]); ?>);">未认证</span>-->
                                <!--<?php endif; ?>-->
                            <!--</td>-->
                            <td class="text-center">
                                <?php if(($v["sms_status"]) == "1"): ?><span  class="label label-default" style="font-size:12px;font-weight:1;border-radius: 5px;cursor: pointer;">已发送</span>
                                    <?php else: ?>
                                    <span onclick="mch_sms(<?php echo ($v["id"]); ?>);" class="label label-primary" style="font-size:12px;font-weight:1;border-radius: 5px;cursor: pointer;">未发送</span><?php endif; ?>

                            </td>
                            <td class="text-center">
                                <?php if(($v["status"]) == "1"): ?><span  onclick="mch_status(<?php echo ($v["id"]); ?>);" class="label label-default" style="font-size:12px;font-weight:1;border-radius: 5px;cursor: pointer;">已通过</span><?php endif; ?>

                            </td>
                            <td class="text-center">
                                <a href="<?php echo U('api_way',array('id'=>$v['id']));?>" class="btn btn-default btn-sm" type="button"  >支付通道</a>
                                <!--<a href="<?php echo U('card_api_way',array('id'=>$v['id']));?>" class="btn btn-primary btn-sm" type="button"  >无卡通道</a>-->

                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm dropup ">
                                    <button class="btn btn-mint btn-active-purple dropdown-toggle" data-toggle="dropdown" type="button">
                                        操作 <i class="dropdown-caret caret-up"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        <li class="dropdown-header">请选择操作</li>
                                        <li><a href="<?php echo U('mch_detail',array('id'=>$v['id']));?>">基本信息</a></li>
                                        <li><a href="<?php echo U('store',array('id'=>$v['id']));?>" >门店列表</a></li>
                                        <li><a href="JavaScript:;" onclick="Transfer(<?php echo ($v["id"]); ?>);">过户代理</a></li>
                                        <li class="divider"></li>
                                        <li><a href="<?php echo U('terminal',array('id'=>$v['id']));?>" >终端管理</a></li>
                                        <!--<li><a href="<?php echo U('flow/index',array('id'=>$v['id']));?>" >流量管理</a></li>-->
                                        <!--<li><a href="<?php echo U('flow/payPoll',array('id'=>$v['id']));?>" >商户轮询</a></li>-->
                                        <li class="divider"></li>
                                        <li><a href="javaScript:;" class="restMchPassword" data-id="<?php echo ($v["id"]); ?>" data-name="<?php echo ($v["mch_name"]); ?>">重置密码</a></li>
                                        <li><a href="<?php echo U('mp_login',array('id'=>$v['id']));?>" target="_blank">登入商户端</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr><?php endforeach; endif; ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="20">
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
<div class="modal fade" id="alleys-data" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <i class="pci-cross pci-circle"></i>
                </button>
                <h4 class="modal-title" id="S_title">
                    通道选择
                </h4>
            </div>
            <div class="modal-body">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="panel-body demo-nifty-btn" style="text-align: left">
                                    <label class="control-label">请选择支付通道,未开通的通道无法切换!如需使用!请开通对应通道后切换!</label>
                                    <input id="alleys_id" name="alleys_id" type="hidden">
                                    <input id="alleys_type" name="alleys_type" type="hidden">
                                    <?php if(is_array($api)): foreach($api as $k=>$v): ?><button id="<?php echo ($v["alleys_type"]); ?>" onclick="alleys_submit('<?php echo ($v["alleys_type"]); ?>')" class="btn btn-info btn-rounded" style="border-color:<?php echo alleys_color($v['alleys_type']); ?>;background-color:<?php echo alleys_color($v['alleys_type']); ?>;" disabled><?php echo ($v["alleys"]); ?></button><?php endforeach; endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- 模态框关闭 -->
<script>
    $('.dialog').click(function () {
        layer.open({
            type: 2,
            title:'检索',
            area: ['700px', '530px'],
            fixed: false, //不固定
            maxmin: false,
            content: "<?php echo U('Agent/dialog');?>"
        });
    });
    //切换通道
    function Alleys_data(id,type) {
        $('.demo-nifty-btn button').attr('disabled',true);
        //获取已开通的通道
        var actionurl="<?php echo U('mch_alleys_data');?>";
        var ajax_data={id:id};
        $.post(actionurl, ajax_data, function(data){
            if(data.status == 1){
                var  array=data.info.type;
                for (var k = 0, length = array.length; k < length; k++) {
                    $('#'+array[k]).attr('disabled',false);
                }
                $('#alleys_type').val(type);
                $('#alleys_id').val(data.info.cid);
                $('#alleys-data').modal('show');
            }else{
                $.niftyNoty({
                    type: 'danger',
                    message : '<strong>'+data.info+'</strong>',
                    container : 'floating',
                    timer : 5000
                });
            }
        }, 'json');
    }
    //提交
    function alleys_submit(type) {
        var cid=$('#alleys_id').val();
        var alleys_type= $('#alleys_type').val();
        var actionurl="<?php echo U('mch_alleys_saves');?>";
        var ajax_data={'cid':cid,'type':type,'alleys_type':alleys_type};
        $.post(actionurl, ajax_data, function(data){
            if(data.status == 1){
                $('#alleys-data').modal('hide');
                $.niftyNoty({
                    type: 'success',
                    message : '<strong>'+data.info+'</strong>',
                    container : 'floating',
                    timer : 5000
                });
                setTimeout(function(){
                    window.location.reload();
                }, 2000);

            }else{
                $.niftyNoty({
                    type: 'danger',
                    message : '<strong>'+data.info+'</strong>',
                    container : 'floating',
                    timer : 5000
                });
            }
        }, 'json');
        //alert(type+id);
    }


    $('.restMchPassword').click(function () {
       var mchId=$(this).data('id'),mchName=$(this).data('name');
        layer.prompt({title: '重置密码('+mchName+')', formType: 0}, function(text){
            var ajax_data ={'id':mchId,'pass':text};
            $.post("<?php echo U('restMchPassword');?>", ajax_data, function(data){
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

    function Transfer(id) {
        layer.prompt({title: '请输入要过户的代理姓名', formType: 0}, function(text){
            var ajax_data ={'id':id,'agent':text};
            $.post("<?php echo U('transfer_agent');?>", ajax_data, function(data){
                if(data.status == 1){
                    layer.msg( data.info, function(){

                    });
                    window.location.reload();
                }
                else{
                    layer.msg( data.info, function(){
                    });
                }
            }, 'json');

        });
    }


    //发送商户审核通过短信
    function mch_sms(id) {
        layer.alert('请确保已经给商户配置好支付通道,未配置好,请先配置!如已配置,可点击确定进行审核通过短信给商户!切记!没有配置支付通道不要发送短信哦!  ( ^_^ )', {
            title:'短信提示',
            skin: 'layui-layer-molv'
            ,closeBtn: 1
            ,anim: 6 //动画类型
        },function(){
            var ajax_data ={'id':id};
            $.post("<?php echo U('mch_sms');?>", ajax_data, function(data){
                if(data.status == 1){
                    layer.msg( data.info, function(){

                    });
                    window.location.reload();
                }
                else{
                    layer.msg( data.info, function(){
                    });
                }
            }, 'json');
        });
    }

    function mch_status(id) {
        layer.alert('此操作 直接将商户信息退回到审核状态,如无此需求,请勿点击确认!切记!点击后就变为审核状态啦,审核状态,审核状态啦!重要的事说三遍! ( ^_^ )', {
            title:'温馨提示',
            skin: 'layui-layer-molv'
            ,closeBtn: 1
            ,anim: 6 //动画类型
        },function(){
            var ajax_data ={'id':id};
            $.post("<?php echo U('mch_status');?>", ajax_data, function(data){
                if(data.status == 1){
                    layer.msg( data.info, function(){

                    });
                    window.location.reload();
                }
                else{
                    layer.msg( data.info, function(){
                    });
                }
            }, 'json');
        });
    }


    //发送未认证商户模板消息提醒
    function SendAuth(id) {
        layer.alert('切记!同一个商户不要多次发送未认证消息提醒!合理安排模板消息用途!你确定要给此商户发送未认证的模板消息提醒吗?', {
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
            var ajax_data ={id:id};
            var actionurl ='<?php echo U("SendAuthTemplate",array("Debug"=>"1"));?>';
            $.post(actionurl, ajax_data, function (data) {
                if (data.status == 1) {
                    layer.closeAll();
                    $.niftyNoty({
                        type: 'success',
                        message : '<strong>'+data.info+'</strong>',
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