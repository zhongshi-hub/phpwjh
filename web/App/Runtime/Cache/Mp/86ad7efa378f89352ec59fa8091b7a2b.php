<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>创建充值送活动-<?php echo ($_domain['web_name']); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="<?php echo ($_menu_name); ?>-<?php echo ($_domain['web_name']); ?>" name="description" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="shortcut icon" href="<?php echo GetPico();?>">
    <link href="/Source/amp/plugins/jquery-toastr/jquery.toast.min.css" rel="stylesheet" />
    
        <link href="/Source/amp/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="/Source/amp/plugins/sweet-alert/sweetalert2.min.css" rel="stylesheet" type="text/css" />
        <link href="/Source/amp/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet">
        <link href="/Source/amp/plugins/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
        <link href="/Source/amp/plugins/spinkit/spinkit.css" rel="stylesheet" />
        <link href="/Source/amp/plugins/jquery-toastr/jquery.toast.min.css" rel="stylesheet" />

    <link href="/Source/amp/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="/Source/amp/assets/css/icons.css" rel="stylesheet" type="text/css" />
    <link href="/Source/amp/assets/css/metismenu.min.css" rel="stylesheet" type="text/css" />
    <link href="/Source/amp/assets/css/style.css" rel="stylesheet" type="text/css" />
    
    <script src="/Source/amp/assets/js/modernizr.min.js"></script>

</head>
<body>

<div id="wrapper">

    <div class="left side-menu">
        <div class="slimscroll-menu" id="remove-scroll">
            <div class="topbar-left">
                <a href="<?php echo U('mp/index/index');?>" class="logo">
                            <span>
                                <img src="/Source/amp/assets/images/logo.png" alt="" height="22">
                            </span>
                    <i>
                        <img src="/Source/amp/assets/images/logo_sm.png" alt="" height="28">
                    </i>
                </a>
            </div>
            <div class="user-box">
                <div class="user-img">
                    <img src="<?php echo ((isset($_domain['brand_logo']) && ($_domain['brand_logo'] !== ""))?($_domain['brand_logo']):'/Source/amp/assets/images/user.png'); ?>" alt="user-img" title="<?php echo ($_domain['web_name']); ?>" class="rounded-circle img-fluid">
                </div>
                <h5><a><?php echo ($_SESSION['mp']['mch_name']); ?></a> </h5>
                <p class="text-muted"><?php echo (tel_replace($_SESSION['mp']['phone'])); ?></p>
            </div>
            <div id="sidebar-menu">

                <ul class="metismenu" id="side-menu">
                    <?php if(is_array($_menu["default"])): $k = 0; $__LIST__ = $_menu["default"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k; if(!empty($vo['list'])): ?><li class="active">
                                <a href="javascript: void(0);"><i class="<?php echo ((isset($vo["ico"]) && ($vo["ico"] !== ""))?($vo["ico"]):'fi-layers'); ?>"></i> <span><?php echo ($vo["name"]); ?></span> <span class="menu-arrow"></span></a>
                                <ul class="nav-second-level" aria-expanded="true">
                                    <?php if(is_array($vo['list'])): $i = 0; $__LIST__ = $vo['list'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub): $mod = ($i % 2 );++$i;?><li><a <?php if(($_rule_name) == $vo['url']): ?>class="active"<?php endif; ?> href="<?php echo U($sub['url']);?>"><?php echo ($sub["name"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
                                </ul>
                            </li>
                            <?php else: ?>
                            <li>
                                <a <?php if(($_rule_name) == $vo['url']): ?>class="active"<?php endif; ?>  href="<?php echo U($vo['url']);?>"><i class="<?php echo ($vo["ico"]); ?>"></i> <span> <?php echo ($vo["name"]); ?> </span></a>
                            </li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                    <li class="menu-title">扩展</li>
                    <?php if(is_array($_menu["plug"])): $k = 0; $__LIST__ = $_menu["plug"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k; if(!empty($vo['list'])): ?><li class="active">
                                <a href="javascript: void(0);"><i class="<?php echo ((isset($vo["ico"]) && ($vo["ico"] !== ""))?($vo["ico"]):'fi-layers'); ?>"></i> <span> <?php echo ($vo["name"]); ?></span> <span class="menu-arrow"></span></a>
                                <ul class="nav-second-level" aria-expanded="true">
                                    <?php if(is_array($vo['list'])): $i = 0; $__LIST__ = $vo['list'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub): $mod = ($i % 2 );++$i;?><li><a href="<?php echo U($sub['url']);?>"><?php echo ($sub["name"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
                                </ul>
                            </li>
                            <?php else: ?>
                            <li>
                                <a <?php if(($_rule_name) == $vo['url']): ?>class="active"<?php endif; ?>  href="<?php echo U($vo['url']);?>"><i class="<?php echo ($vo["ico"]); ?>"></i> <span> <?php echo ($vo["name"]); ?> </span></a>
                            </li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                </ul>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="content-page">
        <div class="topbar">
            <nav class="navbar-custom">
                <ul class="list-unstyled topbar-right-menu float-right mb-0">
                    <li class="dropdown notification-list">
                        <a class="nav-link dropdown-toggle nav-user" data-toggle="dropdown" href="#" role="button"
                           aria-haspopup="false" aria-expanded="false">
                            <img src="/Source/amp/assets/images/user.png" alt="user" class="rounded-circle"> <span class="ml-1"><?php echo ($_SESSION['mp']['mch_name']); ?> <i class="mdi mdi-chevron-down"></i> </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-animated profile-dropdown">
                            <!-- item-->
                            <div class="dropdown-item noti-title">
                                <h6 class="text-overflow m-0">欢迎使用!</h6>
                            </div>
                            <!-- item-->
                            <a href="<?php echo U('user/index');?>" class="dropdown-item notify-item">
                                <i class="fi-head"></i> <span>我的资料</span>
                            </a>
                            <!-- item-->
                            <a href="<?php echo U('user/rate');?>" class="dropdown-item notify-item">
                                <i class="fi-help"></i> <span>我的费率</span>
                            </a>
                            <!-- item-->
                            <a href="<?php echo U('user/pass');?>" class="dropdown-item notify-item">
                                <i class="fi-lock"></i> <span>修改密码</span>
                            </a>
                            <!-- item-->
                            <a href="<?php echo U('login/out');?>" class="dropdown-item notify-item">
                                <i class="fi-power"></i> <span>退出登录</span>
                            </a>

                        </div>
                    </li>

                </ul>
                <ul class="list-inline menu-left mb-0">
                    <li class="float-left">
                        <button class="button-menu-mobile open-left disable-btn">
                            <i class="dripicons-menu"></i>
                        </button>
                    </li>
                    <li>
                        <div class="page-title-box">
                            <h4 class="page-title">
    创建充值送活动
</h4>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item active">商户管理系统</li>
                            </ol>
                        </div>
                    </li>

                </ul>
            </nav>
        </div>

        <div class="content">
            <div class="container-fluid">
                
    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <h4 class="m-t-0 m-b-30 header-title">创建活动</h4>
                <form role="form">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>活动名称</label>
                                <input type="text" class="form-control" name="name" placeholder="最多10个汉字" required>
                                <small  class="form-text text-muted">设置会员活动后，活动内容将展示在用户支付后页面</small>
                            </div>
                        </div>
                    </div>
                    <div id="acItem">
                        <div class="row acList">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>活动规则</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">充值</span>
                                        </div>
                                        <input type="number" min="0.01" class="form-control" name="cz_money[]"  step="0.01" placeholder="" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text"> 元,送</span>
                                        </div>
                                        <input type="number" min="0.01" class="form-control" name="cz_total[]" step="0.01" placeholder="" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text"> 元</span>
                                        </div>
                                        <div class="input-group-append">
                                            <button type="button" class="ac-add btn waves-effect waves-light btn-success"> <i class="fa fa-plus"></i>添加</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row" >
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>时间规则</label>
                                <div class="form-control" style="border:0">
                                <div class="radio radio-info form-check-inline">
                                    <input type="radio"  value="1" name="rule_type" checked="">
                                    <label > 指定时间 </label>
                                </div>
                                <div class="radio radio-info form-check-inline">
                                    <input type="radio"  value="2" name="rule_type">
                                    <label > 不限时间</label>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="rule_time">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>时间范围</label>
                                <input type="text" id="date" name="rule_time" class="form-control"  placeholder="请选择开始至结束时间">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>所属门店</label>
                                <select data-placeholder="请选择门店,支持多选..."  class="form-control select2 select2-multiple" multiple="multiple"  name="store_id[]" required>
                                    <?php if(is_array($store)): foreach($store as $key=>$v): ?><option value="<?php echo ($v["id"]); ?>"><?php echo ($v["name"]); ?></option><?php endforeach; endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">提交</button>
                </form>
            </div>
        </div>
    </div>

            </div> <!-- 主区域 -->
        </div>
        <footer class="footer">
            2018 ©<?php echo ($_domain['web_name']); ?>. -  <?php echo ($_domain['web_domain']); ?>
        </footer>
    </div>
</div>

<script src="/Source/amp/assets/js/jquery.min.js"></script>
<script src="/Source/amp/assets/js/bootstrap.bundle.min.js"></script>
<script src="/Source/amp/assets/js/metisMenu.min.js"></script>
<script src="/Source/amp/assets/js/waves.js"></script>
<script src="/Source/amp/assets/js/jquery.slimscroll.js"></script>
<script src="/Source/amp/plugins/jquery-toastr/jquery.toast.min.js" type="text/javascript"></script>
<script>
    function toast(icon,text) {
        $.toast({
            heading: '温馨提示',
            text: text,
            position: 'top-right',
            loaderBg: icon=='error'?'#bf441d':'#5ba035',
            icon: icon?icon:'error',
            hideAfter: 3000,
            stack: 1
        });
    }
</script>

    <script src="/Source/amp/plugins/moment/moment.js"></script>
    <script src="/Source/amp/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
    <script src="/Source/amp/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
    <script src="/Source/amp/plugins/jquery-toastr/jquery.toast.min.js" type="text/javascript"></script>
    <script src="/Source/amp/plugins/select2/js/select2.min.js" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            $(".select2").select2();
            $('#date').daterangepicker({
                opens: 'left',
                drops: 'down',
                buttonClasses: ['btn', 'btn-sm'],
                applyClass: 'btn-success',
                cancelClass: 'btn-light',
                separator: ' to ',
                locale: {
                    applyLabel: '确定',
                    cancelLabel: '取消',
                    fromLabel: '起始时间',
                    toLabel: '结束时间',
                    customRangeLabel: '自定义',
                    daysOfWeek: ["日", "一", "二", "三", "四", "五", "六"],
                    monthNames: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
                    firstDay: 1
                }
            });
            $("[name='rule_type']").change(ruleType);
        });
        $('.ac-add').click(function (e) {
            var num = $('.acList').length;
            var html= " <div class=\"row acList item"+new Date().getTime()+"\">\n" +
                "                        <div class=\"col-md-5\">\n" +
                "                            <div class=\"form-group\">\n" +
                "                                <div class=\"input-group\">\n" +
                "                                    <div class=\"input-group-prepend\">\n" +
                "                                        <span class=\"input-group-text\">充值</span>\n" +
                "                                    </div>\n" +
                "                                    <input type=\"number\" min=\"0.01\" class=\"form-control\" name=\"cz_money[]\" step=\"0.01\" placeholder=\"\" required>\n" +
                "                                    <div class=\"input-group-append\">\n" +
                "                                        <span class=\"input-group-text\"> 元,送</span>\n" +
                "                                    </div>\n" +
                "                                    <input type=\"number\" min=\"0.01\" class=\"form-control\" name=\"cz_total[]\" step=\"0.01\" placeholder=\"\" required>\n" +
                "                                    <div class=\"input-group-append\">\n" +
                "                                        <span class=\"input-group-text\"> 元</span>\n" +
                "                                    </div>\n" +
                "                                    <div class=\"input-group-append\">\n" +
                "                                        <button type=\"button\" class=\"delete btn waves-effect waves-light btn-danger\" onclick=\"ac_delete('item"+new Date().getTime()+"')\"> <i class=\"fa fa-times\"></i>删除</button>\n" +
                "                                    </div>\n" +
                "                                </div>\n" +
                "                            </div>\n" +
                "                        </div>\n" +
                "                    </div>";
            if (num <= 2){
                $("#acItem").append(html);
            } else{
                toast('error',"规则最多只能添加3个");
            }
        });
        function ac_delete(e) {
            $('.'+e).remove();
        }

        function ruleType() {
            var val=$("[name='rule_type']:checked").val(),rule_time=$('#rule_time');
            console.log(val);
            if(val==1){
                rule_time.show();
            }else{
                rule_time.hide();
            }
        }


        $(function() {
            $("form").submit(function (e) {
                e.preventDefault();
                var ajax_data = $(this).serialize();
                var actionurl = $(this).attr("action");
                $.post(actionurl, ajax_data, function (data) {
                    if (data.status === 1) {
                        $.toast({
                            heading: '温馨提示!',
                            text: data.info,
                            position: 'top-right',
                            loaderBg: '#5ba035',
                            icon: 'success',
                            hideAfter: 3000,
                            stack: 1
                        });
                        setTimeout(function () {
                            window.location.href="<?php echo U('activity');?>";
                        }, 3000);
                    }
                    else {
                        $.toast({
                            heading: '温馨提示',
                            text: data.info,
                            position: 'top-right',
                            loaderBg: '#bf441d',
                            icon: 'error',
                            hideAfter: 3000,
                            stack: 1
                        });
                    }
                }, 'json');
            });
        });



    </script>

<script src="/Source/amp/assets/js/jquery.core.js"></script>
<script src="/Source/amp/assets/js/jquery.app.js"></script>

</body>
</html>