<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title;?>-移动支付管理平台</title>
    <link rel="shortcut icon"  href="<?php echo GetPico();?>" />
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

                                <div class="profile-wrap" style="text-align: center">
                                    <div class="pad-btm">
                                        <img class="img-border" src="<?php echo ($WxData["qrc_img"]); ?>" style="border-radius: 10%;width: 55px;height: 55px;">
                                    </div>
                                    <p class="mnp-name"><?php echo ($WxData["name"]); ?></p>
                                    <a href="#profile-nav" class="box-block collapsed" data-toggle="collapse" aria-expanded="false" style="text-align: left;margin-top: 20px">
                                       <span class="pull-right dropdown-toggle">
                                                <i class="dropdown-caret"></i>
                                        </span>
                                        <span class="mnp-desc">切换公众号</span>
                                    </a>
                                </div>
                                <div id="profile-nav" class="list-group bg-trans collapse" aria-expanded="false" style="height: 2px;">
                                    <?php if(is_array($WxList)): foreach($WxList as $key=>$v): ?><a href="<?php echo U('',array('token'=>$v['token']));?>" class="list-group-item">
                                        <i class="fa  fa-weixin" style="width:20px;text-align: center;font-size: 13px"></i> <?php echo ($v["name"]); ?>
                                    </a><?php endforeach; endif; ?>
                                </div>
                            </div>

                            <ul id="mainnav-menu" class="list-group">
                                <li>
                                    <a href="<?php echo U('Index/index');?>" onclick="if(confirm('确定返回主控制台吗?')==false)return false;">
                                        <i class="fa fa-mail-reply-all" style="width:20px;text-align: center;font-size: 14px"> </i>
                                        <span class="menu-title"><strong>返回主控制台</strong></span>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo U('WeiXin/index',array('token'=>$_GET['token']));?>">
                                        <i class="fa fa-desktop" style="width:20px;text-align: center;font-size: 14px"> </i>
                                        <span class="menu-title"><strong>微信控制台</strong></span>
                                    </a>
                                </li>
                                <!--菜单-->
                                <?php if(is_array($WxMeun)): $k = 0; $__LIST__ = $WxMeun;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?><li <?php if(($rule_name_s) == $vo['name']): ?>class="active-sub active"<?php endif; ?>>
                                    <a href="<?php if(empty($vo['_data'])): echo U($vo['name'],array('token'=>$_GET['token'])); else: ?>#<?php endif; ?>">
                                    <i class="fa <?php echo $vo['icon'];?>" style="width:20px;text-align: center;font-size: 13px"> </i>
                                    <span class="menu-title"><strong><?php echo $vo['title'];?></strong></span>
                                    <?php if(!empty($vo['_data'])): ?><i class="arrow"></i><?php endif; ?>
                                    </a>
                                    <?php if(!empty($vo['_data'])): ?><!--子菜单-->
                                        <ul class="collapse">
                                            <?php if(is_array($vo['_data'])): $i = 0; $__LIST__ = $vo['_data'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub): $mod = ($i % 2 );++$i;?><li <?php if(($rule_name) == $sub['name']): ?>class="active-link"<?php endif; ?>><a href="<?php echo U($sub['name'],array('token'=>$_GET['token']));?>"><?php echo $sub['title'];?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
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
                <div class="panel-control">
                    <span class="text-muted">

                    </span>
                    <span class="text-muted"><small></small></span>
                </div>
                <h3 class="panel-title"><?php echo $title;?></h3>
            </div>
            <link href="/Source/Css/wechat/menu.css" rel="stylesheet">
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="mobile-menu-preview" style="margin-right: 20px;background-color: #ececec">
                            <div class="mobile-head-title" style="margin:0px"><?php echo ($WxData["name"]); ?></div>
                            <section class="xun-chat">
                                <div class="xun-chat-header"><span class="time">01:21</span></div>
                                <div class="xun-chat-item xun-chat-left">
                                    <div class="xun-chat-media">
                                        <img src="<?php echo ($WxData["qrc_img"]); ?>">
                                    </div>
                                    <div class="xun-chat-inner">
                                        <div  class="xun-chat-content bg-white">
                                            <div class="xun-chat-arrow one"></div>
                                            <div class="xun-chat-arrow two"></div>
                                            <p id="view_cheat" style="font-size: 14px;color: #525252;"></p>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="panel" style="border-radius: 1px;height:550px">
                            <div class="panel-heading">
                                <h3 class="panel-title"><?php echo $title;?>详细规则</h3>
                            </div>
                            <form action="<?php echo U('AutoReply');?>"  method="post">
                                <div class="panel-body">
                                    <div class="form-group pad-ver">
                                        <label class="col-md-3 control-label">规则状态</label>
                                        <div class="col-md-9">
                                            <div class="radio" style="margin-top: 0px;">
                                                <input id="radio" class="magic-radio" name="status" value="1" <?php if(($data["status"]) == "1"): ?>checked=""<?php endif; ?>  type="radio">
                                                <label for="radio">启用</label>
                                                <input id="radio-2" class="magic-radio" name="status" value="0" type="radio" <?php if(($data["status"]) != "1"): ?>checked=""<?php endif; ?> >
                                                <label for="radio-2">禁用</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group pad-ver">
                                        <label class="col-md-3 control-label">消息类型</label>
                                        <div class="col-md-9">
                                            <div class="radio" style="margin-top: 0px;">
                                                <input id="radio3" class="magic-radio" name="type" value="text" <?php if(($data["type"]) != "image"): ?>checked=""<?php endif; ?> type="radio">
                                                <label for="radio3">文字</label>
                                                <input id="radio-4" class="magic-radio" name="type" value="image" type="radio" <?php if(($data["type"]) == "image"): ?>checked=""<?php endif; ?>>
                                                <label for="radio-4">图片</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group" id="text">
                                        <label class="col-md-3 control-label">回复内容</label>
                                        <div class="col-md-9">
                                            <textarea name="basic" id="basic" rows="13"  class="form-control" style="resize: none"><?php echo ($data["reply_text"]); ?></textarea>
                                            <div class="help-block"> 您还可以使用表情和链接。
                                                <a class="emotion-triggers emoji_show" value_id="basic" href="javascript:;"><i class="fa fa-github-alt"></i> 表情</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group" id="image">
                                        <label class="col-md-3 control-label">回复图片</label>
                                        <div class="col-md-9">
                                            <?php echo loadImage('','image_url');?>
                                            <input id="image_url" type="url" name="image_url" value="<?php echo ($data["reply_image_url"]); ?>"  class="form-control" onchange="$('#reply_image').attr('src', this.value);">
                                            <div class="thumbnail" style="width: 160px;height: 160px;margin-top: 2px">
                                                <div class="mail-file-img">
                                                    <img id="reply_image" class="image-responsive" src="" style="width: 150px;height: 150px;border: medium none;display: inline-block;">
                                                </div>
                                                <div class="caption" style="cursor: pointer;text-align: center;display: block" onclick="edit_upload_modal('reply_image');">
                                                    <p class="text-primary mar-no" style="font-size: 14px">上传图片</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="panel-footer" style="background-color:transparent;border: 0">
                                    <div class="row">
                                        <div class="col-sm-9 col-sm-offset-5">
                                            <button class="btn btn-success" type="submit">保 存 规 则</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<link type="text/css" rel="stylesheet" href="/Source/emoji/emotions.css">
<script type="text/javascript"  src="/Source/emoji/emoji.js"></script>
<script type="text/javascript"  src="/Source/emoji/reply.js"></script>
</div>
<footer id="footer">
    <div class="hide-fixed pull-right pad-rgt">
        渠道管理后台
    </div>
    <p class="pad-lft">&#0169; 2017 </p>
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
                <h4 class="modal-title">
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


        $('select').chosen();

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