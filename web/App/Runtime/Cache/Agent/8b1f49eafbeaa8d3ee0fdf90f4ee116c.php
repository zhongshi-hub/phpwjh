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
                <h3 class="panel-title">收款码列表</h3>
            </div>
            <!--筛选功能开始-->
            <div class="row">
                <div class="col-sm-12">
                    <form method="post" action="<?php echo U('lists');?>" ajax="n">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="control-label">详细信息</label>
                                        <input class="form-control" type="text" placeholder="收款码ID" name="codes"
                                               value="">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="control-label">所属代理</label>
                                        <input id="dialog_name" class="form-control dialog" type="text" value="">
                                        <input id="dialog_aid" class="form-control" type="hidden" name="aid" value="">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="control-label">绑定状态</label>
                                        <select class="form-control" name="bind">
                                            <option value="">所有</option>
                                            <option value="1">已绑定商户</option>
                                            <option value="2">未绑定商户</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="control-label">状态筛选</label>
                                        <select class="form-control" name="status">
                                            <option value="">所有</option>
                                            <option value="1">开启</option>
                                            <option value="2">禁用</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="control-label">数据条数</label>
                                        <input class="form-control" type="number" min="10" step="10"  name="page_data">
                                    </div>
                                </div>

                            </div>

                            <div class="text-right">
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
                        <div class="col-sm-6 table-toolbar-left">
                            <input type="button" value="全选" class="btn btn-default" id="checkall">
                            <input type="button" value="全不选" class="btn btn-default" id="unSelect">
                            <input type="button" value="反选" class="btn btn-default" id="checkrev">
                            <input type="button" value="导出收款二维码" class="btn btn-danger" id="exp">
                            <input type="button" value="导出素材合成图片" class="btn btn-success" id="expsucai">
                            <input type="button" value="导出二维码数据(制作卡牌使用)" class="btn btn-default" id="kadata">
                        </div>

                       <!-- <div class="col-sm-6 table-toolbar-right">
                            <button id="QrAdd" class="btn btn-purple"><i class="demo-pli-plus"></i>生成收款码</button>
                        </div>-->
                    </div>
                </div>
                <table id="demo-custom-toolbar"
                       class="table table-bordered table-hover toggle-circle table-vcenter demo-add-niftycheck"
                       data-page-size="7">
                    <thead>
                    <tr>
                        <th class="text-center">选择</th>
                        <th>收款码ID</th>
                        <th class="text-center">所属代理</th>
                        <th class="text-center">所属商户</th>
                        <th class="text-center">所属门店</th>
                        <th class="text-center">收款二维码</th>
                        <th class="text-center">创建时间</th>
                        <th class="text-center">状态</th>
                        <!--<th class="text-center">操作</th>-->
                    </tr>
                    </thead>
                    <tbody>
                    <?php if(is_array($list)): foreach($list as $key=>$v): ?><tr>
                            <td class="text-center">
                                <input value="<?php echo ($v["codes"]); ?>" type="checkbox" name="check1" id="exp_codes">
                            </td>
                            <td><?php echo ($v['codes']); ?></td>
                            <td class="text-center">
                                <?php if(!empty($v["aid"])): if(!empty($v["store_id"])): ?><span class="label label-mint" onclick="tradata()"
                                               style="cursor:pointer;font-size:12px;font-weight:1;border-radius: 5px;"><?php echo (agent_name($v['aid'])); ?></span>
                                        <?php else: ?>
                                        <span class="label label-mint" onclick="allot(<?php echo ($v["id"]); ?>)"
                                              style="cursor:pointer;font-size:12px;font-weight:1;border-radius: 5px;"><?php echo (agent_name($v['aid'])); ?></span><?php endif; ?>

                                    <?php else: ?>
                                    <span class="label label-default" onclick="allot(<?php echo ($v["id"]); ?>)"
                                          style="cursor:pointer;font-size:12px;font-weight:1;border-radius: 5px;">未分配</span><?php endif; ?>
                            </td>
                            <td class="text-center"><span class="label label-success"
                                                          style="font-size:12px;font-weight:1;border-radius: 5px;"> <?php $seller=Get_Seller($v['mch_id']);if($seller['mch_name']){ echo $seller['mch_name'];}else{ echo '未绑定';} ?></span>
                            </td>
                            <td class="text-center"><span class="label label-info"
                                                          style="font-size:12px;font-weight:1;border-radius: 5px;"><?php $store=Get_Store($v['store_id']);if($store['name']){ echo $store['name'];}else{ echo '未绑定';} ?></span>
                            </td>
                            <td class="text-center"><img src="<?php echo ($v["code_url"]); ?>" title="点击放大查看" width="30" height="30"
                                                         onclick="IMG('<?php echo ($v["code_url"]); ?>')" style="cursor:pointer"></td>
                            <td class="text-center"><?php echo (date('Y-m-d H:i:s',$v['ctime'])); ?></td>
                            <td class="text-center">
                                <?php if($v['status'] == 1): ?><span class="label label-danger"
                                          style="font-size:12px;font-weight:1;border-radius: 5px;">已开启</span>
                                    <?php else: ?>
                                    <span class="label label-default"
                                          style="font-size:12px;font-weight:1;border-radius: 5px;">未开启</span><?php endif; ?>
                            </td>
                            <!-- <td class="text-center">
                                 <a href="" class="btn btn-danger btn-rounded btn-sm">按钮</a>
                             </td>-->
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
<div class="modal fade" id="QR-add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <i class="pci-cross pci-circle"></i>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    收款码生成
                </h4>
            </div>
            <div class="modal-body">
                <form action="<?php echo U('adds');?>" method="post">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">所属代理</label>
                                    <select data-placeholder="请选择..." id="demo-chosen-select" tabindex="10"
                                            class="form-control" name="aid">
                                        <option value="" style="color: red">-空码(不分配代理)-</option>
                                        <?php if(is_array($agent)): foreach($agent as $key=>$v): ?><option value="<?php echo ($v['id']); ?>"><?php echo ($v['user_name']); ?></option><?php endforeach; endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">收款码数量</label>
                                    <input class="form-control" type="text" name="count"
                                           onkeyup="this.value=this.value.replace(/\D|^0/g,'')"
                                           onafterpaste="this.value=this.value.replace(/\D|^0/g,'')" required>
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
<!-- 模态框关闭 -->


<link href="/Source/statics/plugins/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
<!--Bootstrap Table [ OPTIONAL ]-->
<script src="/Source/statics/plugins/bootstrap-table/bootstrap-table.min.js"></script>
<script>
    $('#QrAdd').click(function () {
        $('.chosen-container').css('width', '250px');
        $('#QR-add').modal('show');
    });
    function IMG(url) {
        $.fancybox.open(url);
    }
    $(function () {
        //全选
        $("#checkall").click(function () {
            $("input[name='check1']:checkbox").prop('checked', true);
        });
        //全不选
        $("#unSelect").click(function () {
            $("input[name='check1']:checkbox").removeAttr("checked");
        });
        //反选
        $("#checkrev").click(function () {
            //实现反选功能
            $('[name=check1]:checkbox').each(function () {
                this.checked = !this.checked;
            });
        });
        //导出图片
        $("#exp").click(function () {
            var exp_codes = $("input[type='checkbox'][name='check1']:checked").map(function () {
                return $(this).val();
            }).get().join(",");
            if (exp_codes == '' || exp_codes == null) {
                layer.msg('请先选择要导出的二维码', function () {
                });
            } else {
                layer.msg('图片正在打包,请不要关闭本窗口!请等待...');
                $('#codexls').val(exp_codes);
                $('#ccl_type').val('exp');
                $('#kadataform').submit();
            }
        });

        //导出素材图片
        $("#expsucai").click(function () {
            var exp_codes = $("input[type='checkbox'][name='check1']:checked").map(function () {
                return $(this).val();
            }).get().join(",");
            if (exp_codes == '' || exp_codes == null) {
                layer.msg('请先选择要导出的二维码', function () {
                });
            } else {
                layer.msg('图片正在打包,请不要关闭本窗口!请等待...');
                $('#codexls').val(exp_codes);
                $('#ccl_type').val('expsucai');
                $('#kadataform').submit();
            }
        });

        //导出xls制作卡牌数据
        $("#kadata").click(function () {
            var exp_codes = $("input[type='checkbox'][name='check1']:checked").map(function () {
                return $(this).val();
            }).get().join(",");
            if (exp_codes == '' || exp_codes == null) {
                layer.msg('请先选择要导出的二维码', function () {
                });
            } else {
                layer.msg('信息已提交服务器处理,请不要关闭本窗口!请等待.');
                $('#codexls').val(exp_codes);
                $('#ccl_type').val('kadata');
                $('#kadataform').submit();
            }
        });
    });


    $('.dialog').click(function () {
        layer.open({
            type: 2,
            title: '检索',
            area: ['700px', '530px'],
            fixed: false, //不固定
            maxmin: false,
            content: "<?php echo U('Agent/dialog');?>"
        });
    });

    //代理过户
    function  tradata() {
        layer.msg('此收款码已绑定商户,无法变更!');
    }
    //代理分配
    function allot(id) {
        layer.prompt({title: '请输入要分配的代理ID或全名', formType: 0}, function (text) {
            var ajax_data = {'id': id, 'agent': text};
            $.post("<?php echo U('allot_agent');?>", ajax_data, function (data) {
                if (data.status == 1) {
                    layer.msg(data.info, function () {

                    });
                    window.location.reload();
                }
                else {
                    layer.msg(data.info, function () {
                    });
                }
            }, 'json');

        });
    }

</script>
<form id="kadataform" method="post" action="<?php echo U('codesdown');?>" ajax="n">
    <input type="hidden" id="ccl_type" name="type" value=""/>
    <input type="hidden" id="codexls" name="code" value=""/>
</form>

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