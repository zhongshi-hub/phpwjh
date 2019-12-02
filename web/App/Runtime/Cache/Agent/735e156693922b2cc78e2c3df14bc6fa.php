<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>移动支付业务管理平台</title>
    <link rel="shortcut icon" href="<?php echo GetPico();?>"/>
    <link href="/Ext?g=css" rel="stylesheet">
    <link href="/Source/statics/css/themes/type-b/theme-navy.min.css" rel="stylesheet">
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
                <a href="<?php echo U('Agent/Index/index');?>" class="navbar-brand">
                    <img src="/Source/statics/img/logo.png" alt="控制台" class="brand-icon">
                    <div class="brand-title">
                        <span class="brand-text">业务管理平台</span>
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
                            <div class="username hidden-xs">
                                <h4 class="text-main"><?php echo ($_SESSION['agent']['user_name']); ?></h4>
                            </div>
                        </a>
                        <div class="dropdown-menu  dropdown-menu-right panel-default">

                            <div class="pad-all text-right">
                                <a href="<?php echo U('Agent/Login/out');?>" class="btn btn-primary">
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
        <nav id="mainnav-container">
            <div id="mainnav">
                <div id="mainnav-menu-wrap">
                    <div class="nano">
                        <div class="nano-content">
                            <div id="mainnav-profile" class="mainnav-profile">

                                <div class="profile-wrap">
                                    <div class="pad-btm">
                                        <img class="img-border" src="<?php echo GetPlogo();?>"
                                             style="border-radius: 10%;width: 55px;height: 55px;">
                                    </div>
                                </div>
                            </div>
                            <ul id="mainnav-menu" class="list-group">
                                <li <?php if(($rule) == "Index/index"): ?>class="active-link"<?php endif; ?>>
                                    <a href="<?php echo U('Index/index');?>">
                                        <i class="fa fa-dashboard"
                                           style="width:20px;text-align: center;font-size: 13px"> </i>
                                        <span class="menu-title"><strong>控制台</strong></span>
                                    </a>
                                </li>
                                <li <?php if(($rule) == "Merchant/index"): ?>class="active-link active"<?php endif; ?>>
                                    <a href="#">
                                        <i class="fa fa-send-o" style="width:20px;text-align: center;font-size: 13px"> </i>
                                        <span class="menu-title"><strong>商户管理</strong></span>
                                        <i class="arrow"></i>
                                    </a>
                                    <ul class="collapse" aria-expanded="false">
                                        <li><a href="<?php echo U('Merchant/index');?>">商户列表</a></li>
                                    </ul>
                                </li>
                                <li <?php if(($rule) == "Agent/lists"): ?>class="active-link active"<?php endif; ?>>
                                    <a href="#">
                                        <i class="fa fa-users" style="width:20px;text-align: center;font-size: 13px"> </i>
                                        <span class="menu-title"><strong>代理管理</strong></span>
                                        <i class="arrow"></i>
                                    </a>
                                    <ul class="collapse" aria-expanded="false">
                                        <li><a href="<?php echo U('Agent/lists');?>">子代理列表</a></li>
                                        <li><a href="<?php echo U('Agent/benefit_count');?>">子代理分润</a></li>
                                    </ul>
                                </li>

                                <li <?php if(($rule) == "Orders/index"): ?>class="active-link"<?php endif; ?>>
                                    <a href="<?php echo U('Orders/index');?>">
                                        <i class="fa fa-bar-chart"
                                           style="width:20px;text-align: center;font-size: 13px"> </i>
                                        <span class="menu-title"><strong>交易管理</strong></span>
                                    </a>
                                </li>
                                <li  <?php if(($rule) == "Qrcode/lists"): ?>class="active-link"<?php endif; ?>>
                                    <a href="<?php echo U('Qrcode/lists');?>">
                                        <i class="fa fa-qrcode" style="width:20px;text-align: center;font-size: 13px"> </i>
                                        <span class="menu-title"><strong>收款码管理</strong></span>
                                    </a>
                                </li>

                                <li <?php if(($rule) == "Users/index"): ?>class="active-link active"<?php endif; ?>>
                                <a href="#">
                                    <i class="fa fa-user" style="width:20px;text-align: center;font-size: 13px"> </i>
                                    <span class="menu-title"><strong>个人信息</strong></span>
                                    <i class="arrow"></i>
                                </a>
                                <ul class="collapse" aria-expanded="false">
                                    <li><a href="<?php echo U('Partner/benefit_count');?>">分润信息</a></li>
                                    <li><a href="JavaScript:password()">修改密码</a></li>
                                </ul>
                                </li>
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
                <h3 class="panel-title">店员管理-<span style="color: red"><?php $seller=Get_Seller($_GET['id']); echo $seller['mch_name']; ?>--<?php $store=Get_Store($_GET['store_id']); echo $store['name']; ?></span></h3>
            </div>
            <!--筛选功能开始-->
            <div class="row">
                <div class="col-sm-12">
                    <form method="post" action="" ajax="n">
                        <div class="panel-body">
                            <div class="row ">
                                <div>
                                    <button type="button" style="float: left" id="StoreAdd" class="btn btn-purple"><i class="demo-pli-add icon-fw"></i>添加店员</button>
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
                        <th class="text-center">所属商户</th>
                        <th class="text-center">门店名称</th>
                        <th class="text-center">店员姓名</th>
                        <th class="text-center">店员手机</th>
                        <th class="text-center">店员微信</th>
                        <th class="text-center">店员状态</th>
                        <th class="text-center">加入时间</th>
                        <th class="text-center">订单通知权限</th>
                        <th class="text-center">流水账单权限</th>
                        <th class="text-center">登录账户</th>
                        <th class="text-center">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if(is_array($data)): foreach($data as $key=>$v): ?><tr>
                            <td class="text-center"><?php $seller=Get_Seller($v['sid']); echo $seller['mch_name']; ?></td>
                            <td class="text-center"><?php $store=Get_Store($v['store_id']); echo $store['name']; ?></td>
                            <td class="text-center"><?php echo ($v["username"]); ?></td>
                            <td class="text-center"><?php echo ($v["phone"]); ?></td>
                            <td class="text-left"><img src="<?php echo ($v['wx_imgurl']); ?>" style="width: 30px"/> <?php echo ($v['wx_name']); ?></td>
                            <td class="text-center">
                                <?php if($v['status'] == 1): ?><span class="label label-danger"
                                          style="font-size:12px;font-weight:1;border-radius: 5px;">开启</span>
                                    <?php else: ?>
                                    <span class="label label-default"
                                          style="font-size:12px;font-weight:1;border-radius: 5px;">禁用</span><?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php echo (date('Y-m-d H:i:s',$v["ctime"])); ?>
                            </td>
                            <td class="text-center">
                                <?php if($v['role_wx_temp'] == 1): ?><span class="label label-danger"
                                          style="font-size:12px;font-weight:1;border-radius: 5px;">开启</span>
                                    <?php else: ?>
                                    <span class="label label-default"
                                          style="font-size:12px;font-weight:1;border-radius: 5px;">禁用</span><?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if($v['role_order'] == 1): ?><span class="label label-danger"
                                          style="font-size:12px;font-weight:1;border-radius: 5px;">开启</span>
                                    <?php else: ?>
                                    <span class="label label-default"
                                          style="font-size:12px;font-weight:1;border-radius: 5px;">禁用</span><?php endif; ?>
                            </td>
                            <td class="text-center"><?php echo ($v["store_id"]); ?>@<?php echo ($v["phone"]); ?></td>
                            <td class="text-center">
                                <button onclick="Store_edit(<?php echo ($v["sid"]); ?>,<?php echo ($v["id"]); ?>)" class="btn btn-info btn-sm" type="button">编辑</button>
                                <button onclick="Store_del(<?php echo ($v["sid"]); ?>,<?php echo ($v["id"]); ?>)" class="btn btn-danger btn-sm" type="button">删除</button>
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
<div class="modal fade" id="Store-add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width: 800px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <i class="pci-cross pci-circle"></i>
                </button>
                <h4 class="modal-title" id="S_title">
                    编辑店员
                </h4>
            </div>
            <div class="modal-body">
                <form action="<?php echo U('store_user_save');?>" method="post">
                    <div class="panel-body">

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">店员姓名</label>
                                    <input class="form-control" type="text" name="username" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">店员电话</label>
                                    <input class="form-control" type="text" name="phone" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-1">
                                <div class="form-group">
                                    <img class="form-control" id="wx_img" src="" style="height: 60px!important;width: 60px;padding: 0px">
                                </div>
                            </div>
                            <div class="col-sm-5">
                                <div class="form-group">
                                    <label class="control-label">微信名称</label>
                                    <input class="form-control" type="text" name="wx_name" disabled>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">店员状态</label>
                                    <select   class="form-control" name="status" required>
                                        <option value="1">开启</option>
                                        <option value="0">禁用</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="control-label"> 登录密码</label>
                                    <input class="form-control" type="text" name="password" placeholder="为空不修改密码">
                                </div>
                            </div>
                        </div>
                        <p style="border-bottom: solid 1px #E4E4E4;font-size: 16px;padding-bottom: 5px;">店员权限</p>
                        <div class="row">
                            <div class="col-md-9">
                                <div class="checkbox">
                                    <input name="role_wx_temp" id="role_wx_temp" class="magic-checkbox" type="checkbox">
                                    <label for="role_wx_temp">订单通知(微信)</label>
                                    <input name="role_order" id="role_order" class="magic-checkbox" type="checkbox">
                                    <label for="role_order">流水账单</label>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="panel-footer text-right">
                        <input type="hidden" name="sid" value="">
                        <input type="hidden" name="store_id" value="">
                        <input type="hidden" name="id" value="">
                        <button class="btn btn-success" type="submit">提交</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- 模态框关闭 -->
<script>
    $('#StoreAdd').click(function () {
        var url="<?php echo ($QrUrl); ?>";
        layer.open({
            type: 1,
            fixed: false, //不固定
            maxmin: false,
            title:false,
            content: "<img src='"+url+"' width='300px' height='300px' style='padding: 10px'/><p style='text-align: center'>将二维码让店员使用微信扫码绑定</p>"
        });
    });

    function Store_edit(sid,id) {
        var ajax_data ={'id':id,'sid':sid};
        $.post("<?php echo U('store_user_data');?>", ajax_data, function(data){
            if(data.status == 1){
                $("[name='id']").val(data.info.id);
                $("[name='sid']").val(data.info.sid);
                $("[name='store_id']").val(data.info.store_id);
                $("[name='username']").val(data.info.username);
                $("[name='phone']").val(data.info.phone);
                $("[name='wx_name']").val(data.info.wx_name);
                $("[name='status'] option[value='"+data.info.status+"']").attr("selected", true);
                $("#wx_img").attr('src',data.info.wx_imgurl);
                if(data.info.role_wx_temp == '0'){
                    document.getElementById('role_wx_temp').checked = false;
                }else{
                    document.getElementById('role_wx_temp').checked = true;
                }
                if(data.info.role_order == '0'){
                    document.getElementById('role_order').checked = false;
                }else{
                    document.getElementById('role_order').checked = true;
                }
                $('#Store-add').modal('show');
            }
            else{
                layer.msg( data.info, function(){
                });
            }
        }, 'json');
    }

    function Store_del(sid,id) {
        var ajax_data ={'id':id,'sid':sid};
        layer.msg('你确认删除当前店员吗?', {
            time: 0 //不自动关闭
            ,btn: ['确认删除', '我再想想']
            ,yes: function(index){
                layer.close(index);
                $.post("<?php echo U('store_user_del');?>", ajax_data, function(data){
                    if(data.status == 1){
                        $.niftyNoty({
                            type: 'success',
                            message : '<strong>'+data.info+'</strong> 3秒后自动跳转!',
                            container : 'floating',
                            timer : 3000
                        });
                        setTimeout(function(){
                            window.location.href=data.url
                        }, 3000);
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
        });
    }


</script>


</div>
<footer id="footer">
    <div class="hide-fixed pull-right pad-rgt">
        业务管理后台
    </div>
    <p class="pad-lft">&#0169; 2017 </p>
</footer>
<!-- 返回顶部 -->
<button class="scroll-top btn">
    <i class="pci-chevron chevron-up"></i>
</button>
</div>

<?php if($_SESSION['agent']['pass'] == 'no'): ?><!--首次初始密码修改-->
    <div class="modal fade" id="Pass" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" >
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        <i class="pci-cross pci-circle"></i>
                    </button>
                    <h4 class="modal-title">
                        修改密码
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="modal-body">
                        <form class="form-horizontal" action="<?php echo U('Index/pass_data');?>" method="post">
                            <div class="alert alert-mint" style="margin: 9px;">
                                <strong>您当前账户使用初始密码登录!为了您的账户安全!请重新修改密码!</strong>
                            </div>
                            <div class="panel-body">
                                <div class="input-group mar-btm">
                                    <input placeholder="请输入验证码" class="form-control" type="text" name="verify" required>
                                    <span class="input-group-btn">
					                 <button class="btn btn-info" type="button" onclick="toGetValiNum();" id="validBtn">获取验证码</button>
					                </span>
                                </div>
                                <div class="input-group mar-btm">
					                        <span class="input-group-btn">
					                            <button class="btn btn-mint" type="button">新密码</button>
					                        </span>
                                    <input placeholder="请输入新密码" name="new_pass" class="form-control" type="password" required>
                                </div>
                                <div class="input-group mar-btm">
					                        <span class="input-group-btn">
					                            <button class="btn btn-mint" type="button">确认新密码</button>
					                        </span>
                                    <input placeholder="请再次输入新密码" name="news_pass" class="form-control" type="password" required>
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
    <script>
        $('#Pass').modal({show:true,backdrop: 'static', keyboard: false});

        function toGetValiNum() {
            $("#validBtn").attr("disabled", "disabled");
            //调用获取验证码接口
            $.ajax({
                data: {type:'verify'},
                url: "<?php echo U('Index/sms_check');?>",
                type: "POST",
                dataType: "json",
                success: function (data) {
                    if (data.status == 1) {
                        settime();
                    } else {
                        alert(data.info);
                        $("#validBtn").removeAttr("disabled");
                    }
                },
                error: function (data) {
                    $("#validBtn").removeAttr("disabled");
                    alert('获取验证码失败!');
                }
            });

        }
        var countdown = 60;
        //获取验证码60秒倒计时
        function settime() {
            if (countdown == 0) {
                $("#validBtn").removeAttr("disabled");
                $("#validBtn").text("获取验证码");
                countdown = 60;
                return;
            } else {
                $("#validBtn").attr("disabled", "disabled");
                $("#validBtn").text(countdown + "秒后重新获取");
                countdown--;
            }
            setTimeout(function () {
                settime()
            }, 1000)
        }



    </script>
<?php else: ?>
<!-- 修改密码模态框开始 -->
<div class="modal fade" id="PassWords" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" >
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <i class="pci-cross pci-circle"></i>
                </button>
                <h4 class="modal-title" >
                    修改密码
                </h4>
            </div>
            <div class="modal-body">
                <div class="modal-body">
                    <form class="form-horizontal" action="<?php echo U('Index/edit_pass');?>" method="post">
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
<!-- 修改密码模态框结束 --><?php endif; ?>

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

    .magic-radio + label::after {
        left: 2.8px!important;
        top: 2.8px!important;
    }
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
        $('#PassWords').modal({show:true,backdrop: 'static', keyboard: false});
        //$(".modal-backdrop").remove();
    }


</script>
<style>
    .modal-backdrop.in{
        opacity: .1!important;
    }
    .modal-content{border: 1px solid #ffffff;}
</style>
</body>
</html>