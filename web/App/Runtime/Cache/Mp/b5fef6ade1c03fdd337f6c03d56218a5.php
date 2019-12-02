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
            <div class="card-box">
                <h4 class="m-t-0 header-title">注册信息</h4>
                <p class="text-muted m-b-30 font-13">
                    <code class="highlighter-rouge">提示:</code>当前仅显示入网时的信息 系统为多个通道,默认按照入网信息进件,如果过程中更新单个通道的信息及结算信息 请以更新后的信息为主,此处不显示更新后的信息。
                </p>

                <form>
                    <div class="form-group">
                        <label for="1" class="col-form-label">商户名称</label>
                        <input type="text" class="form-control" id="1" value="<?php echo ($data["mch_name"]); ?>" disabled>
                    </div>

                    <div class="form-row">

                        <div class="form-group col-md-4">
                            <label for="3" class="col-form-label">申请人姓名</label>
                            <input type="text" class="form-control" id="3" value="<?php echo ($data["mch_card_name"]); ?>" disabled>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="2" class="col-form-label">联系电话</label>
                            <input type="text" class="form-control" id="2" value="<?php echo (tel_replace($data["mch_tel"])); ?>" disabled>
                        </div>

                        <div class="form-group col-md-4">
                            <label  for="4" class="col-form-label">注册时间</label>
                            <input type="text" class="form-control" id="4" value="<?php echo (date('Y-m-d H:i:s',$data["ctime"])); ?>" disabled>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="5" class="col-form-label">所属省</label>
                            <input type="text" class="form-control" id="5" value="<?php echo ($data["mch_provice"]); ?>" disabled>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="6" class="col-form-label">所属市</label>
                            <input type="text" class="form-control" id="6" value="<?php echo ($data["mch_citys"]); ?>" disabled>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="7" class="col-form-label">所属区</label>
                            <input type="text" class="form-control" id="7" value="<?php echo ($data["mch_district"]); ?>" disabled>
                        </div>

                    </div>
                    <div class="form-group">
                        <label  for="8" class="col-form-label">详细地址</label>
                        <input type="text" class="form-control" id="8" value="<?php echo ($data["mch_address"]); ?>" disabled>
                    </div>
                    <p class="text-muted m-b-25 font-13">结算信息</p>
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="9" class="col-form-label">开户银行</label>
                            <input type="text" class="form-control" id="9" value="<?php echo (reload_bank($data["mch_bank_list"])); ?>" disabled>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="00" class="col-form-label">开户名称</label>
                            <input type="text" class="form-control" id="00" value="<?php echo ($data["mch_bank_name"]); ?>" disabled>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="01" class="col-form-label">开户省</label>
                            <input type="text" class="form-control" id="01" value="<?php echo ($data["mch_bank_provice"]); ?>" disabled>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="02" class="col-form-label">开户市</label>
                            <input type="text" class="form-control" id="02" value="<?php echo ($data["mch_bank_citys"]); ?>" disabled>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="03" class="col-form-label">联行号</label>
                            <input type="text" class="form-control" id="03" value="<?php echo ($data["mch_linkbnk"]); ?>" disabled>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="04" class="col-form-label">开户支行</label>
                            <input type="text" class="form-control" id="04" value="<?php echo (reload_banks($data["mch_linkbnk"])); ?>" disabled>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="05" class="col-form-label">银行卡号</label>
                            <input type="text" class="form-control" id="05" value="<?php echo ($data["mch_bank_cid"]); ?>" disabled>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="06" class="col-form-label">预留手机号码</label>
                            <input type="text" class="form-control" id="06" value="<?php echo (tel_replace($data["mch_bank_tel"])); ?>" disabled>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3">
            <div class="card-box ribbon-box">
                <div class="ribbon ribbon-custom">身份证正面</div>
                <p class="m-b-0">
                    <img class="img-fluid" src="<?php echo ($data["mch_img_z"]); ?>" style="height: 200px;width: 100%">
                </p>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="card-box ribbon-box">
                <div class="ribbon ribbon-primary">身份证反面</div>
                <p class="m-b-0">
                    <img class="img-fluid" src="<?php echo ($data["mch_img_p"]); ?>" style="height: 200px;width: 100%">
                </p>            </div>
        </div>

        <div class="col-lg-3">
            <div class="card-box ribbon-box">
                <div class="ribbon ribbon-success">结算银行卡</div>
                <p class="m-b-0">
                    <img class="img-fluid" src="<?php echo ($data["mch_img_bank"]); ?>" style="height: 200px;width: 100%">
                </p>            </div>
        </div>
        <div class="col-lg-3">
            <div class="card-box ribbon-box">
                <div class="ribbon ribbon-success">手持照</div>
                <p class="m-b-0">
                    <img class="img-fluid" src="<?php echo ($data["mch_img_s"]); ?>" style="height: 200px;width: 100%">
                </p>            </div>
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

<script src="/Source/amp/assets/js/jquery.core.js"></script>
<script src="/Source/amp/assets/js/jquery.app.js"></script>

</body>
</html>