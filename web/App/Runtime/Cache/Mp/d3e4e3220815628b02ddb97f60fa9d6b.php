<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>模板设置-<?php echo ($_domain['web_name']); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="<?php echo ($_menu_name); ?>-<?php echo ($_domain['web_name']); ?>" name="description" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="shortcut icon" href="<?php echo GetPico();?>">
    <link href="/Source/amp/plugins/jquery-toastr/jquery.toast.min.css" rel="stylesheet" />
    
    <style>
        ol,ul{margin-top:0;margin-bottom:10px;}
        ol,li,ul{list-style-type:none;padding:0;margin:0;}
        .fl{float:left;display:inline-block;line-height:16px;}
        .likePhone{position:relative;width:318px;border:1px solid #e7e7eb;height:460px;}
        .phone_title{position:absolute;top:30px;color:#fff;text-align:center;font-size:16px;width:100%;}
        .top-card-style{padding-top:25px;text-align:center;}
        .card-preview-box{position:relative;width:250px;height:146px;margin:0 auto;padding-top:10px;padding-left:23px;padding-right:23px;-webkit-border-radius:4px;-moz-border-radius:4px;border-radius:4px;text-align:left;}
        #card_bg{background:url("/Source/amp/member/card_bg/0.png") no-repeat;background-size:100% 100%;}
        .card-userinfo-pre{position:absolute;right:2px;bottom:4px;}
        .remaining-box{margin:0 0 4px;padding-right:12px;text-align:right;font-size:30px;}
        .member-card-no{margin:0 5px;font-size:12px;}
        .member-wallet{display:inline-block;width:24px;height:24px;margin-right:6px;}
        #card_bg .member-wallet{background:url("/Source/amp/member/m_ye.png") no-repeat;background-size:100% 100%;}
        .card-name-pre{margin:0;font-size:18px;font-weight:600;}
        .card-slogan-pre{margin:6px 0 0;font-size:14px;}
        .activity-pre{margin-top:14px;font-size:12px;line-height:1.2;}
        .activity-pre .item{margin-bottom:4px;}
        .activity-pre .item:before{list-style-type:georgian;background-color:#fff;display:inline-block;width:10px;height:10px;margin-right:10px;-webkit-border-radius:40%;-moz-border-radius:40%;border-radius:40%;}
        .main-handle-box{margin-top:14px;text-align:center;}
        .main-handle-pre{display:inline-block;padding:10px 69px;background-color:#2493f0;-webkit-border-radius:4px;-moz-border-radius:4px;border-radius:4px;-webkit-box-shadow:0 4px 1px 0 rgba(255,69,82,.12);-moz-box-shadow:0 4px 1px 0 rgba(255,69,82,.12);box-shadow:0 4px 1px 0 rgba(255,69,82,.12);font-size:16px;color:#fff;cursor:pointer;}
        .main-handle-pre:hover{background:#1a85df;}
        .secondary-handle-box{margin-top:42px;text-align:center;}
        .secondary-handle-pre{display:inline-block;padding:10px 80px;border:1px solid #1a85df;-webkit-border-radius:4px;-moz-border-radius:4px;border-radius:4px;color:#1a85df;cursor:pointer;}
        .go-link-list-box{margin-top:16px;}
        .go-link-item{position:relative;padding:10px 0 10px 16px;border-bottom:1px solid #ececec;background-color:#fcfcfc;font-size:14px;cursor:pointer;}
        .go-link-item:nth-child(1){border-top:1px solid #ececec;}
        .go-link-item:before{margin-right:6px;vertical-align:middle;}
        .go-link-item:nth-child(1):before{display:inline-block;content:'';width:16px;height:17px;background:url("/Source/amp/member/m_cz.png") no-repeat;background-size:100% 100%;}
        .go-link-item:nth-child(2):before{display:inline-block;content:'';width:16px;height:17px;background:url("/Source/amp/member/m_xf.png") no-repeat;background-size:100% 100%;}
        .go-link-item:nth-child(3):before{display:inline-block;content:'';width:17px;height:17px;background:url("/Source/amp/member/m_sm.png") no-repeat;background-size:100% 100%;}
        .go-link-item:after{position:absolute;right:14px;top:14px;display:inline-block;content:'';width:10px;height:10px;border-top:1px solid #1a85df;border-right:1px solid #1a85df;-webkit-transform:rotate(45deg);-moz-transform:rotate(45deg);-ms-transform:rotate(45deg);-o-transform:rotate(45deg);transform:rotate(45deg);}

        .likePhone .phone_title{margin:0 0 10px;}

        .template-list-box{display:inline-block;}
        .template-item{position:relative;display:inline-block;width:60px;height:38px;margin-right:10px;cursor:pointer;}
        .template-item:after{position:absolute;bottom:-20px;left:24px;display:inline-block;content:'';width:16px;height:16px;background:url("https://e.51fubei.com/Public/img/newMemberE/select_icon.png") no-repeat;background-size:100% 100%;-webkit-border-radius:50%;-moz-border-radius:50%;border-radius:50%;}
        .template-item.checked:after{background:url("https://e.51fubei.com/Public/img/newMemberE/select_icon_selected.png") no-repeat;background-size:100% 100%;}
        .template-item#red{background-color:#ff4552;}
        .template-item#gold{background-color:#cfb988;}
        .template-item#purple{background-color:black;}
        .template-item#blue{background-color:#3a4862;}
        .zzxunlong-card-temp{margin-bottom: 10px}
        .zzxunlong-card-temp .radio-info{margin-left: 35px}
        .card_0{color: #96d6ff}
        .card_1{color: #f8ffff}
        .card_2{color: #cfd0c2}
        .card_3{color: #f9bdb4}
        .card_4{color: #aaa}
        .card_5{color: #eafdc0}
    </style>
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
                            <h4 class="page-title">
    模板设置
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
                <ul class="nav nav-tabs tabs-bordered">
                    <li class="nav-item">
                        <a href="<?php echo U('index');?>"  class="nav-link ">
                            <i class="fi-monitor mr-2"></i>所有会员
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo U('userCz');?>"  class="nav-link">
                            <i class="fi-head mr-2"></i>充值记录
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo U('userXf');?>"  class="nav-link">
                            <i class="fi-mail mr-2"></i>消费记录
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo U('userTemp');?>"  class="nav-link active">
                            <i class="fi-cog mr-2"></i>模板设置
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo U('userConf');?>"  class="nav-link">
                            <i class="fi-cog mr-2"></i>充值配置
                        </a>
                    </li>
                </ul>
                <div class="row" style="margin-top: 20px">
                    <div class="col-xl-4">
                        <div class="likePhone fl" style="height: 530px;">
                            <img src="/Source/amp/member/temp_top.jpg">
                            <p class="phone_title">我的会员卡</p>
                            <div class="top-card-style">
                                <div id="card_bg" class="card-preview-box  card_0">
                                    <div class="card-name-pre" id="card_name_pre"><?php echo ((isset($data["name"]) && ($data["name"] !== ""))?($data["name"]):"至尊VIP"); ?></div>
                                    <div class="card-slogan-pre" id="card_slogan_pre"><?php echo ((isset($data["xc"]) && ($data["xc"] !== ""))?($data["xc"]):"欢迎使用本店会员卡"); ?></div>
                                    <ul class="activity-pre">
                                        <li class="item">会员活动</li>
                                        <li class="item">会员活动</li>
                                        <li class="item">会员活动</li>
                                        <li class="item">会员活动</li>
                                    </ul>
                                    <div class="card-userinfo-pre">
                                        <p class="remaining-box">￥0.00</p>
                                        <p class="member-card-no">卡号：123456789000000</p>
                                    </div>
                                </div>
                            </div>
                            <div class="main-handle-box">
                                <span class="main-handle-pre">去充值</span>
                            </div>
                            <div class="go-link-list-box">
                                <ul class="go-link-list">
                                    <li class="go-link-item">充值记录</li>
                                    <li class="go-link-item">消费记录</li>
                                    <li class="go-link-item">使用说明</li>
                                </ul>
                            </div>
                            <div class="secondary-handle-box">
                                <span class="secondary-handle-pre">去买单</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-7">
                        <form role="form">
                            <h4 class="m-t-0  header-title">基本信息</h4>
                            <div class="row">
                                <div class="col-xl-4">
                                    <div class="form-group" style="margin-bottom:0">
                                        <label>会员卡名称</label>
                                        <input type="text" class="form-control"  name="name" value="<?php echo ($data["name"]); ?>" placeholder="最多8个汉字" required>

                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="form-group" style="margin-bottom:0">
                                        <label>宣传语</label>
                                        <input name="xc" type="text" class="form-control" placeholder="最多12个汉字" value="<?php echo ($data["xc"]); ?>" required>
                                        <small  class="form-text text-muted">最多设置15个汉字</small>
                                    </div>
                                </div>
                                <div class="col-xl-2" style="padding: 0">
                                    <button type="button" class="xc_btn btn btn-light waves-effect" style="margin-top: 30px;">默认宣传语</button>
                                </div>
                            </div>
                            <h4 class="m-t-0  header-title">会员卡模板</h4>
                            <div class="row zzxunlong-card-temp">
                                <div class="col-xl-2">
                                    <img src="/Source/amp/member/card_bg/0.png" height="50">
                                    <div class="radio radio-info form-check-inline">
                                        <input type="radio" id="r0" value="0" name="bg"  <?php if(empty($data["bg"])): ?>checked=""<?php endif; ?> >
                                        <label for="r0"></label>
                                    </div>
                                </div>
                                <div class="col-xl-2" style="text-align: center">
                                    <img src="/Source/amp/member/card_bg/1.png" height="50">
                                    <div class="radio radio-info form-check-inline">
                                        <input type="radio" id="r1" value="1" name="bg" <?php if(($data["bg"]) == "1"): ?>checked=""<?php endif; ?>>
                                        <label for="r1"></label>
                                    </div>
                                </div>
                                <div class="col-xl-2" style="text-align: center">
                                    <img src="/Source/amp/member/card_bg/2.png" height="50">
                                    <div class="radio radio-info form-check-inline">
                                        <input type="radio" id="r2" value="2" name="bg" <?php if(($data["bg"]) == "2"): ?>checked=""<?php endif; ?>>
                                        <label for="r2"></label>
                                    </div>
                                </div>
                                <div class="col-xl-2" style="text-align: center">
                                    <img src="/Source/amp/member/card_bg/3.png" height="50">
                                    <div class="radio radio-info form-check-inline">
                                        <input type="radio" id="r3" value="3" name="bg" <?php if(($data["bg"]) == "3"): ?>checked=""<?php endif; ?>>
                                        <label for="r3"></label>
                                    </div>
                                </div>
                                <div class="col-xl-2" style="text-align: center">
                                    <img src="/Source/amp/member/card_bg/4.png" height="50">
                                    <div class="radio radio-info form-check-inline">
                                        <input type="radio" id="r4" value="4" name="bg" <?php if(($data["bg"]) == "4"): ?>checked=""<?php endif; ?>>
                                        <label for="r4"></label>
                                    </div>
                                </div>
                                <div class="col-xl-2" style="text-align: center">
                                    <img src="/Source/amp/member/card_bg/5.png" height="50">
                                    <div class="radio radio-info form-check-inline">
                                        <input type="radio" id="r5" value="5" name="bg" <?php if(($data["bg"]) == "5"): ?>checked=""<?php endif; ?>>
                                        <label for="r5"></label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xl-3">
                                    <div class="form-group">
                                        <label>卡片库存</label>
                                        <input id="c1" type="number" min="0" name="number" class="form-control" value="<?php echo ($data["number"]); ?>" placeholder="不限库存请填写0">
                                    </div>
                                </div>
                                <div class="col-xl-2" style="padding: 0">
                                    <button type="button" class="number_btn btn btn-light waves-effect" style="margin-top: 30px;">不限库存</button>
                                </div>
                                <!--<div class="col-xl-2" style="padding: 0">-->
                                    <!--<div class="radio radio-info form-check-inline" style="margin-top: 35px;">-->
                                        <!--<input type="radio" id="no_number" value="5" name="no_number" checked>-->
                                        <!--<label for="no_number" >不限库存</label>-->
                                    <!--</div>-->
                                <!--</div>-->
                                <div class="col-xl-4">
                                    <div class="form-group">
                                        <label>联系电话</label>
                                        <input type="text" name="phone" class="form-control"  value="<?php echo ($data["phone"]); ?>" placeholder="" required>
                                    </div>
                                </div>
                                <div class="col-xl-3">
                                    <div class="form-group">
                                        <label>状态</label>
                                        <select data-placeholder="请选择..."  class="form-control select2" name="status">
                                            <option value="1" <?php if(($data["status"]) == "1"): ?>selected<?php endif; ?>>启用</option>
                                            <option value="0" <?php if(($data["status"]) == "0"): ?>selected<?php endif; ?> >禁用</option>
                                        </select>
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="form-group">
                                        <label>使用说明</label>
                                        <textarea class="form-control" name="help" rows="5" placeholder="说明内容最多为1000个汉字" style="resize:none" required><?php echo ($data["help"]); ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">保存</button>
                        </form>
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

    <script src="/Source/amp/plugins/select2/js/select2.min.js" type="text/javascript"></script>
    <script>
        var bg=$('[name="bg"]'),bg_path_source='/Source/amp/member/card_bg/';
        $(function(){
            $(".select2").select2();
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
                            window.location.reload(true);
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
            bg_check();
            bg.change(bg_check);

            //标题
            $("[name='name']").on('input',function(){
                if($(this).val().trim().length > 8) {
                    toast('error','名称最多设置8个字');
                    var value = $(this).val();
                    $(this).focus();
                    $(this).val(value.substr(0, 10));
                }
                $('#card_name_pre').text($(this).val());
            });
            // 宣传语
            $("[name='xc']").on('input', function () {
                if($(this).val().trim().length > 15) {
                    toast('error','宣传语最多设置15个字');
                    var value = $(this).val();
                    $(this).focus();
                    $(this).val(value.substr(0, 14));
                }
                // 设置预览区域文字
                $('#card_slogan_pre').text($(this).val());
            });
            $('.xc_btn').click(function () {
                var text='欢迎使用本店会员卡';
                $("[name='xc']").val(text);
                $('#card_slogan_pre').text(text);
            });
            $('.number_btn').click(function () {
                $("#c1").val("0");
            })
        });
        function bg_check() {
            var bg_val=$("[name='bg']:checked").val();
            var bg_path=bg_path_source+bg_val+'.png';
            console.log(bg_val);
            $('#card_bg').css({"background":"url("+bg_path+")","background-repeat":"no-repeat","background-size":"100% 100%"}).removeClass().addClass("card-preview-box  card_"+bg_val);
        }
    </script>

<script src="/Source/amp/assets/js/jquery.core.js"></script>
<script src="/Source/amp/assets/js/jquery.app.js"></script>

</body>
</html>