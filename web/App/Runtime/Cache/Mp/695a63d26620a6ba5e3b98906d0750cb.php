<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>云喇叭配置-<?php echo ($_domain['web_name']); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="<?php echo ($_menu_name); ?>-<?php echo ($_domain['web_name']); ?>" name="description" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="shortcut icon" href="<?php echo GetPico();?>">
    <link href="/Source/amp/plugins/jquery-toastr/jquery.toast.min.css" rel="stylesheet" />
    
    <link href="/Source/amp/plugins/ion-rangeslider/ion.rangeSlider.css" rel="stylesheet" type="text/css"/>
    <link href="/Source/amp/plugins/ion-rangeslider/ion.rangeSlider.skinModern.css" rel="stylesheet" type="text/css"/>
    <link href="/Source/amp/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="/Source/amp/plugins/sweet-alert/sweetalert2.min.css" rel="stylesheet" type="text/css" />

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
                            <h4 class="page-title">云喇叭配置</h4>
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
                <h4 class="m-t-0 header-title">云喇叭<code class="highlighter-rouge"><?php echo ($ylbState); ?></code></h4>
                <p class="text-muted m-b-30 font-13">
                   云喇叭用来播报<code class="highlighter-rouge">支付成功</code>后的订单信息 如微信收款1元
                </p>
                <form method="post" action="<?php echo U('speakerSave');?>">
                    <div class="form-group">
                        <label for="id" class="col-form-label">设备ID</label>
                        <input  class="form-control" id="id" type="text" name="vid" value="<?php echo ($data["vid"]); ?>"  required>
                    </div>
                    <div class="form-group">
                        <label for="num" class="col-form-label">设备音量</label>
                        <div class="col-sm-12 col-xs-12">
                            <input type="text" id="num" name="num" value="<?php echo ((isset($data["num"]) && ($data["num"] !== ""))?($data["num"]):'100'); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">状态</label>
                        <select   data-placeholder="请选择..."  tabindex="2"  class="form-control select2" name="status" required="required">
                            <option value="1" <?php if(($data["status"]) == "1"): ?>selected<?php endif; ?> >开启播报</option>
                            <option value="0" <?php if(($data["status"]) == "0"): ?>selected<?php endif; ?> >关闭播报</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="5" class="col-form-label">AppId</label>
                            <input type="text" class="form-control" id="5" name="appid" value="<?php echo ($data["appid"]); ?>" placeholder="">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="6" class="col-form-label">AppKey</label>
                            <input type="text" class="form-control" id="6" name="appkey" value="<?php echo ($data["appkey"]); ?>" placeholder="">
                        </div>
                    </div>
                    <p class="text-muted m-b-30 font-13">
                        如自购云喇叭需填写AppId和AppKey  <code class="highlighter-rouge">使用平台发配的无需填写AppId和AppKey</code>
                    </p>

                    <input type="hidden" name="sid" value="<?php echo ($_GET['id']); ?>">
                    <button type="submit" class="btn btn-success">保 存</button>
                    <button type="button" class="btn btn-success" id="testSp">测试播报</button>
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

    <script src="/Source/amp/plugins/select2/js/select2.min.js" type="text/javascript"></script>
    <script src="/Source/amp/plugins/ion-rangeslider/ion.rangeSlider.min.js"></script>
    <script src="/Source/amp/plugins/sweet-alert/sweetalert2.min.js"></script>
    <script>
        jQuery(document).ready(function () {
            $(".select2").select2();
            $("#num").ionRangeSlider({
                grid: true,
                min: 0,
                max: 100,
                from: "<?php echo ((isset($data["num"]) && ($data["num"] !== ""))?($data["num"]):'100'); ?>",
                step: 1,
                prettify_enabled: true
            });
            $('#testSp').click(function () {
                swal({
                    title: '请输入测试金额,默认为微信收款方式',
                    input: 'text',
                    showCancelButton: true,
                    confirmButtonText: '提交',
                    cancelButtonText:'取消',
                    showLoaderOnConfirm: true,
                    confirmButtonClass: 'btn btn-confirm mt-2',
                    cancelButtonClass: 'btn btn-cancel ml-2 mt-2',
                    inputValue:'0.01',
                    preConfirm: function (text) {
                        return new Promise(function (resolve, reject) {
                            setTimeout(function () {
                                if (isNaN(text)) {
                                    reject('请输入正确的数字.')
                                } else {
                                    resolve()
                                }
                            }, 2000);
                        })
                    },
                    allowOutsideClick: false
                }).then(function (text) {
                    $.post('<?php echo U("testSpeaker");?>',{id:<?php echo ($_GET['id']); ?>,total:text}, function (data) {
                        if (data.status === 1) {
                            swal({
                                type: 'success',
                                title: '处理成功',
                                html: data.info,
                                confirmButtonClass: 'btn btn-confirm mt-2'
                            })
                        }
                        else {
                            swal({
                                type: 'warning',
                                title: '测试信息发送失败',
                                html: data.info,
                                confirmButtonClass: 'btn btn-confirm mt-2'
                            })
                        }
                    }, 'json');
                })
            });
        });
    </script>

<script src="/Source/amp/assets/js/jquery.core.js"></script>
<script src="/Source/amp/assets/js/jquery.app.js"></script>

</body>
</html>