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
    .state{cursor: pointer}
</style>
<div id="content-container">
    <div id="page-content">
        <div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title"><?php echo ((isset($title) && ($title !== ""))?($title):'小微进件'); ?></h3>
            </div>
            <!--筛选功能开始-->
            <div class="row">
                <div class="col-sm-12">
                    <form method="post" action="<?php echo U('xwList');?>" ajax="n">
                        <div class="panel-body">
                            <div class="row ">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="control-label">所属商户</label>
                                        <input id="dialog_mch_name" class="form-control mid_dialog" type="text"  value="">
                                        <input id="dialog_mid" class="form-control" type="hidden" name="mid" value="">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="control-label">申请结果</label>
                                        <select  class="form-control" name="applyment_state">
                                            <option value="">所有状态</option>
                                            <option value="AUDITING" style="color: #99a79e">审核中</option>
                                            <option value="REJECTED" style="color: #de2f54">已驳回</option>
                                            <option value="FROZEN" style="color: #8651ff">已冻结</option>
                                            <option value="TO_BE_SIGNED" style="color: #1d8be9">待签约</option>
                                            <option value="FINISH" style="color: #06c64c">已完成</option>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <button style="float: right" class="btn btn-success" type="submit">搜索</button>
                                </div>
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
                                <button id="mchIn" class="btn btn-purple"><i class="demo-pli-add icon-fw"></i>小微进件</button>
                            </div>
                        </div>
                        <div class="col-sm-6 text-xs-center text-right">
                        </div>
                    </div>
                </div>
                <table  class="table table-bordered table-hover toggle-circle table-vcenter" data-page-size="7">
                    <thead>
                    <tr>
                        <th class="text-center">微信申请单号</th>
                        <th class="text-center" style="width: 100px">关联商户</th>
                        <th class="text-center">服务商MCHID</th>
                        <th class="text-center">商户号</th>
                        <th class="text-center" style="width: 100px">商户名称</th>
                        <th class="text-center">联系人</th>
                        <th class="text-center">申请时间</th>
                        <th class="text-center">更新时间</th>
                        <th class="text-center">申请状态</th>
                        <th class="text-center">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if(is_array($data)): foreach($data as $key=>$v): ?><tr>
                            <td class="text-center" title="平台申请单号: <?php echo ($v["business_code"]); ?>">
                                <?php echo ($v["applyment_id"]); ?>
                            </td>

                            <td class="text-center"><?php $seller=Get_Seller($v['mid']); echo $seller['mch_name']; ?></td>
                            <td class="text-center">
                                <?php echo ($v["mch_id"]); ?>
                            </td>
                            <td class="text-center">
                                <?php echo ((isset($v["sub_mch_id"]) && ($v["sub_mch_id"] !== ""))?($v["sub_mch_id"]):"---"); ?>
                            </td>
                            <td class="text-center">
                                <?php echo ($v["mch_name"]); ?>
                            </td>
                            <td class="text-center">
                                <?php echo ($v["id_card_name"]); ?>
                            </td>

                            <td class="text-center"><?php echo (date('Y-m-d H:i:s',$v['create_time'])); ?></td>
                            <td class="text-center"><?php echo (date('Y-m-d H:i:s',$v['update_time'])); ?></td>
                            <td class="text-center">
                                <?php switch($v["applyment_state"]): case "AUDITING": ?><span class="state label label-danger" data-id="<?php echo ($v["id"]); ?>" style="background-color:#99a79e;font-size:12px;font-weight:1;border-radius: 5px;">审核中</span><?php break;?>
                                    <?php case "REJECTED": ?><span class="state label label-danger"  data-id="<?php echo ($v["id"]); ?>" style="background-color:#de2f54;font-size:12px;font-weight:1;border-radius: 5px;">已驳回</span><?php break;?>
                                    <?php case "FROZEN": ?><span class="state label label-danger"  data-id="<?php echo ($v["id"]); ?>" style="background-color:#8651ff;font-size:12px;font-weight:1;border-radius: 5px;">已冻结</span><?php break;?>
                                    <?php case "TO_BE_SIGNED": ?><span class="state label label-danger"  data-id="<?php echo ($v["id"]); ?>" style="background-color:#1d8be9;font-size:12px;font-weight:1;border-radius: 5px;">待签约</span><?php break;?>
                                    <?php case "FINISH": ?><span class="state label label-danger"  data-id="<?php echo ($v["id"]); ?>" style="background-color:#06c64c;font-size:12px;font-weight:1;border-radius: 5px;">已完成</span><?php break;?>
                                    <?php default: ?>
                                    <span class="state label label-default"  data-id="<?php echo ($v["id"]); ?>" style="font-size:12px;font-weight:1;border-radius: 5px;">未知状态</span><?php endswitch;?>
                            </td>
                            <td class="text-center">
                                <a href="<?php echo U('info',['applyment_id'=>$v['applyment_id'],'m'=>$v['mid']]);?>" class="btn btn-sm btn-default btn-active-primary">
                                    资料
                                </a>

                                <button onclick="window.location.href='<?php echo U('xwIn',['id'=>$v['mid'],'business_code'=>$v['business_code']]);?>'" class="btn btn-sm btn-default btn-active-primary" type="button" aria-expanded="false" <?php if(($v["applyment_state"]) != "REJECTED"): ?>disabled<?php endif; ?>>
                                    重新入网
                                </button>
                                <button onclick="window.location.href='<?php echo U('upInfo',['id'=>$v['id'],'business_code'=>$v['business_code']]);?>'" class="btn btn-sm btn-default btn-active-primary" type="button" aria-expanded="false" <?php if(($v["applyment_state"]) != "FINISH"): ?>disabled<?php endif; ?>>
                                    升级
                                </button>
                                <div class="btn-group dropup">
                                    <button class="btn btn-sm btn-default btn-active-primary dropdown-toggle dropdown-toggle-icon" data-toggle="dropdown" type="button" aria-expanded="false" <?php if(($v["applyment_state"]) != "FINISH"): ?>disabled<?php endif; ?>>
                                        变更 <i class="dropdown-caret caret-up"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a href="<?php echo U('alterInfo',['id'=>$v['id']]);?>">联系信息</a></li>
                                        <li><a href="<?php echo U('alterInfo',['type'=>'bank','id'=>$v['id']]);?>">结算银行卡</a></li>
                                    </ul>
                                </div>

                                <button onclick="window.location.href='<?php echo U('mchConfig',['id'=>$v['id'],'business_code'=>$v['business_code']]);?>'" class="btn btn-sm btn-default btn-active-primary" type="button" aria-expanded="false" <?php if(($v["applyment_state"]) != "FINISH"): ?>disabled<?php endif; ?>>
                                配置
                                </button>
                            </td>
                        </tr><?php endforeach; endif; ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="18">
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
    $('.state').click(function (e) {
       var id=$(this).data('id');
        loading('请求审核结果中...');
        $.post('<?php echo U("applyState");?>', {id:id}, function (data) {
            if (data.status) {
               layer.closeAll();
               layer.alert(data.info, {
                   skin: 'layui-layer-lan'
                   ,title: "审核结果"
                   ,offset: '100px'
               },function (e) {
                   window.location.reload();
               });
            }
            else {
                layer.msg(data.info, {time:5000,shade: 0.03});
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

    $('#mchIn').click(function () {
       var mid=$('[name="mid"]').val();
       if(mid==''||!mid){
         layer.alert('请先选择所属商户');
       }else {
           window.location.href = "<?php echo U('xwIn');?>/id/" + mid;
       }
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