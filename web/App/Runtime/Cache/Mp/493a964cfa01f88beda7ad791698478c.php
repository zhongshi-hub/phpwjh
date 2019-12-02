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
    <link href="/Source/amp/plugins/custombox/css/custombox.min.css" rel="stylesheet">
    <link href="/Source/amp/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="/Source/amp/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" />
    <link href="/Source/amp/plugins/spinkit/spinkit.css" rel="stylesheet" />
    <script type="text/javascript" src="http://chencunlong.oss-cn-hangzhou.aliyuncs.com/qrcode.min.js"></script>
    <style>
        .chen_tip{position: absolute;width: 100%;height: 87%;left: 0;top: 50px;background-color:#FFFFFF;text-align: center;z-index: 9999}
        .chen_tip_msg{font-size: 1.8em;font-weight: bold;}
        #payQrImg img{text-align: center;display: inline!important;}
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
                                <a href="javascript: void(0);"><i class="fi-layers"></i> <span><?php echo ($vo["name"]); ?></span> <span class="menu-arrow"></span></a>
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
                                <a href="javascript: void(0);"><i class="fi-layers"></i> <span> <?php echo ($vo["name"]); ?></span> <span class="menu-arrow"></span></a>
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
                <table class="table text-center">
                    <thead>
                    <tr>
                        <th>流量余额</th>
                        <th>流量费率</th>
                        <th>流量状态</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="table-active">
                        <td><?php echo ($balance); ?>元</td>
                        <td><?php echo ((isset($res["rate"]) && ($res["rate"] !== ""))?($res["rate"]):'0'); ?>‰</td>
                        <td>
                            <?php if(($res["status"]) == "1"): ?><span class="badge label-table badge-success">启用</span>
                                <?php else: ?>
                                <span class="badge label-table badge-dark">禁用</span><?php endif; ?>
                        </td>
                        <td>
                            <a href="#setPay-modal" type="button" class="btn btn-light waves-effect" data-animation="fadein" data-plugin="custommodal"
                               data-overlaySpeed="200" data-overlayColor="#36404a">立即充值</a>
                            <a href="<?php echo U('mp/flow/setList');?>" type="button" class="btn btn-light">充值记录</a>
                            <a href="<?php echo U('mp/flow/payList');?>" type="button" class="btn btn-light">扣费记录</a>
                            <!--<a type="button" class="btn btn-light">流量通道</a>-->
                            <a href="#custom-modal" type="button" class="btn btn-light waves-effect" data-animation="fadein" data-plugin="custommodal"
                            data-overlaySpeed="200" data-overlayColor="#36404a">设置报警</a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <!-- Modal -->
    <div id="custom-modal" class="modal-demo">
        <button type="button" class="close" onclick="Custombox.close();">
            <span>&times;</span><span class="sr-only">关闭</span>
        </button>
        <h4 class="custom-modal-title">报警手机号设置</h4>
        <div class="custom-modal-text">
            <div class="alert alert-info">此手机号码用于接收流量报警短信，短信提醒每天最多发送3次。</div>
            <form class="form-horizontal is_ajax" action="<?php echo U('setPhone');?>">
                <div class="form-group m-b-25">
                    <div class="col-12">
                        <label for="phone">手机号码</label>
                        <input class="form-control" type="text" name="sms_phone" id="phone" value="<?php echo ($res["sms_phone"]); ?>" required="" placeholder="留空则不发送报警信息">
                    </div>
                </div>
                <div class="form-group account-btn text-center m-t-10">
                    <div class="col-12">
                        <button class="btn w-lg btn-rounded btn-custom waves-effect waves-light" type="submit">保存</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- 充值Modal -->
    <div id="setPay-modal" class="modal-demo">
        <button type="button" class="close" onclick="Custombox.close();">
            <span>&times;</span><span class="sr-only">关闭</span>
        </button>
        <h4 class="custom-modal-title">流量充值</h4>

        <div class="custom-modal-text">
            <div class="chen_tip" style="display: none">
                <div id="loading" style="display:none;">
                    <div class="sk-fading-circle">
                        <div class="sk-circle1 sk-circle"></div>
                        <div class="sk-circle2 sk-circle"></div>
                        <div class="sk-circle3 sk-circle"></div>
                        <div class="sk-circle4 sk-circle"></div>
                        <div class="sk-circle5 sk-circle"></div>
                        <div class="sk-circle6 sk-circle"></div>
                        <div class="sk-circle7 sk-circle"></div>
                        <div class="sk-circle8 sk-circle"></div>
                        <div class="sk-circle9 sk-circle"></div>
                        <div class="sk-circle10 sk-circle"></div>
                        <div class="sk-circle11 sk-circle"></div>
                        <div class="sk-circle12 sk-circle"></div>
                    </div>
                    <div class="chen_tip_msg">
                        稍等,系统处理中...
                    </div>
                </div>
                <div id="payQr" style="display: none">
                    <div id="payQrImg" style="height: 280px;padding-top: 15px;"></div>
                    <div class="chen_pay_msg" style="font-size: 1.1em">
                        请使用<span id="payQrType" style="color: #02c0ce;">微信</span>付款,付款成功后系统会自动返回!
                    </div>
                </div>
            </div>
            <div class="alert alert-info">如在线充值无法使用,请联系平台手动充值</div>
            <form class="form-horizontal pay_ajax" action="#">
                <div class="form-group m-b-25">
                    <div class="col-12">
                        <label for="phone">充值金额</label>
                        <input class="form-control" type="number" min="<?php echo ((isset($config["min_total"]) && ($config["min_total"] !== ""))?($config["min_total"]):'1'); ?>" max="<?php echo ((isset($config["max_total"]) && ($config["max_total"] !== ""))?($config["max_total"]):'1'); ?>" id="totalFee" required="" placeholder="充值范围<?php echo ((isset($config["min_total"]) && ($config["min_total"] !== ""))?($config["min_total"]):'1'); ?>-<?php echo ((isset($config["max_total"]) && ($config["max_total"] !== ""))?($config["max_total"]):'1'); ?>元">
                    </div>
                </div>
                <div class="form-group m-b-25">
                    <div class="col-12">
                        <label for="phone">付款方式</label>
                        <select class="selectpicker m-b-0" data-style="btn-light" name="payType">
                            <?php if(!empty($config["pay_wx"])): ?><option data-icon="mdi mdi-qrcode-scan" value="wx">微信支付</option><?php endif; ?>
                            <?php if(!empty($config["pay_ali"])): ?><option data-icon="mdi mdi-qrcode-scan" value="ali">支付宝支付</option><?php endif; ?>
                            <?php if(($config["pay_wx"] != 1) AND ($config["pay_ali"] != 1) ): ?><option data-icon="mdi mdi-qrcode-scan" value="">暂无支付通道</option><?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group account-btn text-center m-t-10">
                    <div class="col-12">
                        <button class="btn w-lg btn-rounded btn-custom waves-effect waves-light" type="submit" <?php if(($config["pay_wx"] != 1) AND ($config["pay_ali"] != 1) ): ?>disabled<?php endif; ?>>立 即 付 款</button>
                    </div>
                </div>
            </form>
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

    <script src="/Source/amp/plugins/custombox/js/custombox.min.js"></script>
    <script src="/Source/amp/plugins/custombox/js/legacy.min.js"></script>
    <script src="/Source/amp/plugins/bootstrap-select/js/bootstrap-select.js" type="text/javascript"></script>
    <script src="/Source/amp/plugins/jquery-toastr/jquery.toast.min.js" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            $(".select2").select2();
        });
        var setIn_data=0;
        $(function () {
            $(".is_ajax").submit(function (e) {
                e.preventDefault(); //阻止自动提交表单
                var ajax_data = $(this).serialize();
                var actionurl = $(this).attr("action");
                Custombox.close('#custom-modal');
                $.post(actionurl, ajax_data, function (data) {
                    if (data.status === 1) {
                        $.toast({
                            heading: '温馨提示',
                            text: data.info,
                            position: 'top-right',
                            loaderBg: '#5ba035',
                            icon: 'success',
                            hideAfter: 3000,
                            stack: 1
                        });
                        setTimeout(function () {
                            window.location.href = data.url
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


        $(".pay_ajax").submit(function (e) {
            e.preventDefault();
            var tip=$('.chen_tip')
                ,loading=$("#loading")
                ,qr=$("#payQr")
                ,loading_text="稍等,系统处理中..."
                ,loading_msg=$(".chen_tip_msg")
                ,action='/Pays/FastApi/gateway?Debug=1'
                ,pid="<?php echo ($config["pay_mid"]); ?>"
                ,randId="<?php echo ($config["pay_rid"]); ?>"
                ,totalFee=$('#totalFee').val()
                ,payType=$('[name="payType"] option:selected') .val()
                ,payQrType=$('#payQrType');
            var data={type:payType,id:randId,sid:pid,total:totalFee,pay_api:'code',flowUid:'<?php echo ($_SESSION['mp']['id']); ?>'};
            tip.show();
            loading_msg.text(loading_text);
            loading.show();
            $.post(action, data, function (data) {
                if (data.status === 1) {
                    loading.hide();
                    payQrType.html(payType==='wx'?'微信':'支付宝');
                    var qrcode = new QRCode('payQrImg', {
                        title:'请扫码付款',
                        width: 245,
                        height: 245,
                        colorDark : '#000000',
                        colorLight : '#ffffff',
                        correctLevel : QRCode.CorrectLevel.H
                    });
                    qrcode.clear();
                    qrcode.makeCode(data.info.qrcode);
                    qr.show();
                    setIn_data= setInterval("getOrderStatus('"+data.info.out_trade_no+"','"+data.info.api+"')",5000);
                }
                else {
                    loading_msg.css('color','red');
                    loading_msg.text(data.info);
                }
            }, 'json');
        });

        //请求支付状态
        function getOrderStatus(oid,api){
            if(oid === ''){
                window.location.reload();
            }
            $.ajax({
                type: "POST",
                url: "/Pays/FastApi/getOrderStatus",
                data: {'api':api,'oid':oid},
                success: function(data){
                    if(data.status === 1){
                        clearInterval(setIn_data);
                        Custombox.close('#setPay-modal');
                        $.toast({
                            heading: '温馨提示',
                            text: '充值成功',
                            position: 'top-right',
                            loaderBg: '#5ba035',
                            icon: 'success',
                            hideAfter: 3000,
                            stack: 1
                        });
                        setTimeout(function () {
                            window.location.href = data.url
                        }, 3000);

                    }else{
                        console.log('等待用户支付中');
                    }
                }
            });
        }
    </script>

<script src="/Source/amp/assets/js/jquery.core.js"></script>
<script src="/Source/amp/assets/js/jquery.app.js"></script>
</body>
</html>