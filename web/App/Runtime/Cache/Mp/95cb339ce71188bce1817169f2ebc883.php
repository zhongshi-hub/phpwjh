<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title><?php echo ($_menu_name); ?>-<?php echo ($_domain['web_name']); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="<?php echo ($_menu_name); ?>-<?php echo ($_domain['web_name']); ?>" name="description" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="shortcut icon" href="<?php echo GetPico();?>">
    <link href="/Source/amp/plugins/jquery-toastr/jquery.toast.min.css" rel="stylesheet" />
    
    <style>
        .border-left{border-left:1px solid #dcdcdc;}
        .margin-left-30{margin-left:30px!important;}
        .margin-right-54{margin-right:54px;}
        .float-left{float:left;}
        .zzxunlong-activeity{position:relative;display:inline-block;width:300px;height:320px;margin-bottom:36px;border:1px solid #dcdcdc;text-align:center;}
        .zzxunlong-title{padding-top:30px;font-size:20px;color:#333;}
        .zzxunlong-alert{padding:20px 0 27px 0px;font-size:16px;color:#999;}
        .zzxunlong-img{display:block;width:117px;height:115px;margin:auto;margin-bottom:30px;}
        .zzxunlong-activeity a{display:inline-block;width:120px;height:30px;line-height:30px;border-radius:6px;background:#ff5e5f;color:#fff;font-size:14px;}
        .zzxunlong_logo{position:absolute;top:-1px;left:-1px;width:62px;height:62px;}
        .zzxunlong-top-alert{position:absolute;top:5px;left:8px;font-size:18px;color:#fff;}
        .zzxunlong-right{display:inline-block;width:480px;height:305px;margin-left:48px;background:url("/Source/amp/member/info-bg.png") no-repeat;background-position:center;background-size:cover;}
        .zzxunlong-right-width{width:440px;padding-left:40px;}
        .zzxunlong-right-title{padding-top:40px;padding-bottom:30px;font-size:16px;color:#333;}
        .zzxunlong-right-title span{font-size:28px;color:#fb7632;}
        .zzxunlong-right-alert{font-size:16px;color:#7d7d7d;}
        .zzxunlong-right-alert span{display:inline-block;padding-top:17px;}
        .zzxunlong-activity-bg{width:420px;margin-top:20px;font-size:0;}
        .zzxunlong-activity-display{display:inline-block;width:33.3%;font-size:16px;color:#666;text-align:center;}
        .zzxunlong-activity-display span{display:inline-block;margin-left:-14px;padding-top:5px;color:#fe898a;}
        .other-activity-bg{width:270px;height:320px;border:1px solid #dcdcdc;text-align:center;}
        .other-activity-bg p{padding-top:27px;font-size:16px;color:#333;}
        .other-activity-bg h3{padding-bottom:27px;font-size:14px;color:#999;font-weight:normal!important;}
        .other-activity-bg a{display:inline-block;width:110px;height:30px;line-height:30px;border-radius:6px;background:#ff5e5f;color:#fff;font-size:14px;}
        .consume-img{display:block;margin:auto;width:88px;height:88px;margin-bottom:36px;}
        .active-img{display:block;margin:auto;width:88px;height:88px;margin-bottom:36px;}
        .zzxunlong-activity-display p{margin-bottom:5px;}
        .zzxunlong-activity-display p:nth-child(2){color: #02c0ce}
    </style>

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
                            <h4 class="page-title"><?php echo ($_menu_name); ?></h4>
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
            <div class="card-box" style="padding: 50px">
                <button type="button" class="btn btn-pink btn-rounded w-md waves-effect waves-light float-right btn-sm" data-url="<?php echo U('lists');?>"><i class="mdi mdi-account-convert"></i> 活动列表</button>
                <h4 class="header-title m-b-30">创建活动</h4>
                <div class="row">
                    <div class="col-md-12">
                        <div class="zzxunlong-activeity float-left">
                            <img class="zzxunlong_logo" src="/Source/amp/member/hot.png" alt="">
                            <span class="zzxunlong-top-alert">荐</span>
                            <div class="zzxunlong-title">充值送</div>
                            <div class="zzxunlong-alert">充值即收益，快速回笼资金</div>
                            <img class="zzxunlong-img" src="/Source/amp/member/cz.png" alt="">
                            <button type="button" class="btn btn-custom waves-effect waves-light btn-sm" data-url="<?php echo U('activity_cz');?>">立即使用</button>
                        </div>
                        <div class="zzxunlong-right float-left">
                            <div class="margin-left-30 zzxunlong-right-width zzxunlong-right-title"><span>90%</span> 商家的选择，让散客变会员，锁住老顾客。</div>
                            <div class="margin-left-30 zzxunlong-right-width zzxunlong-right-alert">
                                建议充值送比例为10%～20%，各级别依次增高，<br>
                                <span style="color: #686868">例如：</span>
                            </div>
                            <div class="margin-left-30 zzxunlong-activity-bg">
                                <div class="zzxunlong-activity-display ">
                                    <p>充100元</p>
                                    <p>送10元</p>
                                </div>
                                <div class="zzxunlong-activity-display  border-left">
                                    <p>充200元</p>
                                    <p>送30元</p>
                                </div>
                                <div class="zzxunlong-activity-display  border-left">
                                    <p>充500元</p>
                                    <p>送100元</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="margin-right-54 other-activity-bg float-left">
                            <p>消费返</p>
                            <h3>消费返余额，引导二次购买</h3>
                            <img class="consume-img" src="/Source/amp/member/xf.png" alt="">
                            <button type="button" class="btn btn-custom waves-effect waves-light btn-sm" data-url="<?php echo U('activity_xf');?>">立即使用</button>
                        </div>
                        <div class="margin-right-54 other-activity-bg float-left">
                            <p>激活送</p>
                            <h3>激活赠余额，快速发展会员</h3>
                            <img class="active-img" src="/Source/amp/member/jh.png" alt="">
                            <button type="button" class="btn btn-custom waves-effect waves-light btn-sm" data-url="<?php echo U('activity_jh');?>">立即使用</button>
                        </div>
                        <div class="other-activity-bg float-left">
                            <p>推荐送</p>
                            <h3>推荐送余额，扩大会员传播</h3>
                            <img class="active-img" src="/Source/amp/member/tj.png" alt="">
                            <button type="button" class="btn btn-custom waves-effect waves-light btn-sm" data-url="<?php echo U('activity_tj');?>">立即使用</button>
                        </div>
                    </div>
                </div>
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

<script type="application/javascript">
    $('.btn').click(function (e) {
        var url=$(this).data('url');
        console.log(url);
        if(url!==''){
            window.location.href=url;
        }
    })
</script>

<script src="/Source/amp/assets/js/jquery.core.js"></script>
<script src="/Source/amp/assets/js/jquery.app.js"></script>

</body>
</html>