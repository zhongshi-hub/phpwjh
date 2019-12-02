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
        .pagination{display:inline-block;padding-left:0;margin:0px 0;border-radius:4px;}
        .pagination>li{display:inline;}
        .pagination>li>a{position:relative;float:left;padding:6px 12px;margin-left:-1px;line-height:1.42857143;color:#337ab7;text-decoration:none;background-color:#fff;border:1px solid #ddd;}
        .pagination>li:first-child>a{margin-left:0;border-top-left-radius:4px;border-bottom-left-radius:4px;}
        .pagination>li:last-child>a{border-top-right-radius:4px;border-bottom-right-radius:4px;}
        .pagination>li>a:focus,.pagination>li>a:hover{z-index:2;color:#23527c;background-color:#eee;border-color:#ddd;}
        .pagination>.active>a,.pagination>.active>a:focus,.pagination>.active>a:hover{z-index:3;color:#fff;cursor:default;background-color:#337ab7;border-color:#337ab7;}
        .pull-right{float:none!important;}
        .pagination>li a:active{box-shadow:inset 0 3px 1px rgba(0,0,0,0.2);}
        .pagination>li a:hover,.pagination>li a:focus{background-color:#fff;border-color:#42a5f5;color:#42a5f5;box-shadow:inset 0 0 1px #42a5f5;z-index:2;transition:border-color,0.3s;}
        .pagination>li>a{color:inherit;border-color:#dcdcdc;transition:border-color,0.3s;}
        .pagination>li:first-child>a{border-top-left-radius:0;border-bottom-left-radius:0;}
        .pagination>li:last-child>a{border-top-right-radius:0;border-bottom-right-radius:0;}
        .pagination>.active>a,.pagination>.active>a:hover,.pagination>.active>a:focus{background-color:#42a5f5;border-color:#42a5f5;}
        .pagination>li>a{background-color:transparent;color:inherit;}
        .pagination>li>a:focus{box-shadow:none;}
        .pagination>li a:hover,.pagination>li a:focus{border-color:#00bcd4;color:#00bcd4;box-shadow:inset 0 0 1px #00bcd4;}
        .pagination>.active>a,.pagination>.active>a:hover,.pagination>.active>a:focus{background-color:#00bcd4;border-color:#00bcd4;}
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
        <div class="col-lg-12">
            <div class="card-box">
                <h4 class="m-t-0 header-title">门店列表</h4>
                <!--<p class="text-muted font-14 m-b-20">-->
                    <!--系统支持多门店,门店-->
                <!--</p>-->

                <table class="table">
                    <thead>
                    <tr>
                        <th>门店ID</th>
                        <th>门店名称</th>
                        <th>负责人</th>
                        <th>电话</th>
                        <th>更新时间</th>
                        <th>门店状态</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "当日暂时没有门店数据" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr>
                            <th scope="row"><?php echo ($v["id"]); ?></th>
                            <td><?php echo ($v["name"]); ?></td>
                            <td><?php echo ($v["per_name"]); ?></td>
                            <td><?php echo ($v["per_phone"]); ?></td>
                            <td><?php echo (date("Y-m-d H:i:s",$v["uptime"])); ?></td>
                            <td>
                                <?php if(($v["status"]) == "1"): ?><span class="badge label-table badge-success">启用</span>
                                    <?php else: ?>
                                    <span class="badge label-table badge-dark">禁用</span><?php endif; ?>
                            </td>
                            <td>
                                <button type="button" class="btn btn-custom waves-effect waves-light btn-sm" data-url="<?php echo U('printConfig',['id'=>$v['id']]);?>">打印机配置</button>
                                <button type="button" class="btn btn-success waves-effect waves-light btn-sm" data-url="<?php echo U('speaker',['id'=>$v['id']]);?>">云喇叭配置</button>
                            </td>
                        </tr><?php endforeach; endif; else: echo "当日暂时没有门店数据" ;endif; ?>
                    </tbody>
                </table>
                <div  style="text-align: right">
                    <?php echo ($page); ?>
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

    <script>
        $('.btn').click(function () {
            window.location.href=$(this).data('url');
        })
    </script>

<script src="/Source/amp/assets/js/jquery.core.js"></script>
<script src="/Source/amp/assets/js/jquery.app.js"></script>

</body>
</html>