<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>打印机配置-<?php echo ($_domain['web_name']); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="<?php echo ($_menu_name); ?>-<?php echo ($_domain['web_name']); ?>" name="description" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="shortcut icon" href="<?php echo GetPico();?>">
    <link href="/Source/amp/plugins/jquery-toastr/jquery.toast.min.css" rel="stylesheet" />
    
    <link href="/Source/amp/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />

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
                            <h4 class="page-title">打印机配置</h4>
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
                <h4 class="m-t-0 header-title">门店打印机</h4>
                <p class="text-muted m-b-30 font-13">
                    打印机为<code class="highlighter-rouge">易联云打印机</code> 可自行采购或联系平台采购
                </p>

                <form method="post" action="<?php echo U('printSave');?>">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="1" class="col-form-label">用户ID</label>
                            <input type="text" class="form-control" id="1" name="print_id" value="<?php echo ($data["print_id"]); ?>" placeholder="" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="2" class="col-form-label">API秘钥</label>
                            <input type="text" class="form-control" id="2" name="print_api" value="<?php echo ($data["print_api"]); ?>" placeholder="" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="3" class="col-form-label">打印机秘钥</label>
                            <input type="text" class="form-control" id="3" name="print_key" value="<?php echo ($data["print_key"]); ?>"  placeholder="" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="4" class="col-form-label">打印机终端号</label>
                            <input type="text" class="form-control" id="4" name="print_zd" value="<?php echo ($data["print_zd"]); ?>" placeholder="" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="5" class="col-form-label">商户名称</label>
                            <input type="text" class="form-control" id="5" name="print_mchname" value="<?php echo ($data["print_mchname"]); ?>" placeholder="" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="6" class="col-form-label">门头信息</label>
                            <input type="text" class="form-control" id="6" name="print_top" value="<?php echo ($data["print_top"]); ?>" placeholder="" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="7" class="col-form-label">底部信息</label>
                            <input type="text" class="form-control" id="7" name="print_footer" value="<?php echo ($data["print_footer"]); ?>" placeholder="" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputAddress" class="col-form-label">打印联数</label>
                        <input  class="form-control" id="inputAddress" type="number" name="print_num" value="<?php echo ($data["print_num"]); ?>" min="1" max="10" required>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">状态</label>
                        <select   data-placeholder="请选择..."  tabindex="2"  class="form-control select2" name="print_status" required="required">
                            <option value="1" <?php if(($data["status"]) == "1"): ?>selected<?php endif; ?> >开启打印</option>
                            <option value="0" <?php if(($data["status"]) == "0"): ?>selected<?php endif; ?> >关闭打印</option>
                        </select>
                    </div>
                    <input type="hidden" name="store_id" value="<?php echo ($_GET['id']); ?>">
                    <button type="submit" class="btn btn-success">保 存</button>
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
    <script>
        jQuery(document).ready(function () {
            $(".select2").select2();
        });
    </script>

<script src="/Source/amp/assets/js/jquery.core.js"></script>
<script src="/Source/amp/assets/js/jquery.app.js"></script>

</body>
</html>