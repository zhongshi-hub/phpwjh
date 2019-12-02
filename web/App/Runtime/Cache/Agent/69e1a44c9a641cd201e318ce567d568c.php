<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html id="myhtml" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title><?php echo ($_domain['web_name']); ?>移动支付业务管理平台</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta content="移动支付,聚合支付,支付宝ISV,批量创建收款码,公众号支付,收款码,银行通道,第三方通道,微信支付,微信服务商,支付宝服务商" name="keywords"/>
    <link rel="shortcut icon" href="<?php echo GetPico();?>"/>
    <link href="/Source/agent/css/login-all-min.css" rel="stylesheet" type="text/css"/>
</head>
<body>
<div id="login-container">
    <div class="ibg-bg index-banner-0"></div>
    <div class="header-container">

    </div>
    <div class="login-main">
        <div class="all-index-banner "></div>
        <div id="login-middle">
            <div class="header-login">
                <div class="login-header-title">
                    <h3>业务管理系统</h3>
                </div>
                <div class="tang-pass-login" style="margin-top: 28PX;">
                    <form class="pass-form pass-form-normal" method="POST" autocomplete="off" action="<?php echo U('index');?>">

                        <p class="pass-form-item pass-form-item-userName">
                            <label class="pass-label pass-label-userName">手机号</label>
                            <input name="username" class="pass-text-input pass-text-input-userName" autocomplete="off"
                                   placeholder="请输入手机号码" type="text" required>
                        </p>
                        <p class="pass-form-item pass-form-item-password"
                           style="margin-top: 10px;border: 1px solid #e7e7e7;">
                            <label class="pass-label pass-label-password">密码</label>
                            <input class="pass-text-input pass-text-input-password" placeholder="密码" type="password"
                                   name="password" required>
                        </p>
                        <p class="pass-form-item pass-form-item-verifyCode">
                            <label class="pass-label pass-label-verifyCode">验证码</label>
                            <input name="verify" class="pass-text-input pass-text-input-verifyCode" maxlength="6"
                                   placeholder="验证码" type="text" style="width: 140px;" required>
                            <span class="pass-verifyCodeImgParent">
                                <img class="pass-verifyCode" id="verify" src="<?php echo U('load_verify');?>">
                            </span>
                            <a onclick="changeimg()" class="pass-change-verifyCode">换一张</a>
                            <span class="pass-error pass-error-verifyCode"></span>
                        </p>
                        <p class="pass-form-item pass-form-item-memberPass" style="text-align: right">
                            <label class="edit_pass" style="color: #1183ff;cursor: pointer">忘记密码</label>
                        </p>
                        <p class="pass-form-item pass-form-item-submit">
                            <input value="登录" class="pass-button pass-button-submit" type="submit">
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="login-download">
    <ul class="tab-download clearfix" id="tab-download">
        <li><a class="windows download-target" hidefocus="true">微信支付</a></li>
        <li><a class="android" hidefocus="true">支付宝</a></li>
        <li><a class="iphone" hidefocus="true">QQ钱包</a></li>
        <li><a class="ipad download-open" hidefocus="true">京东钱包</a></li>
        <li><a class="tongbupan download-target" hidefocus="true">银联钱包</a></li>

    </ul>
</div>
<div class="footer">
    <div xmlns="http://www.w3.org/1999/xhtml">©2017 <a class="b-lnk-gy"> <?php echo ($_domain['web_name']); ?>
        <?php echo ($_domain['web_domain']); ?> </a> | <a class="b-lnk-gy">移动支付</a> | <a class="b-lnk-gy">聚合支付</a> | <a
            class="b-lnk-gy">微信服务商</a> | <a class="b-lnk-gy">支付宝ISV</a> | <a class="b-lnk-gy">多通道支付体系银行直清</a></div>
</div>

<div class="canvas-bg" style="display: none;"></div>
<div class="qrcode-dialog" style="display: none;">
    <div class="dialog-title">
        <span>重置密码</span>
        <a class="close" href="javascript:;"></a>
    </div>
    <img class="tang-pass-qrcode-img" src="<?php echo GetMchQrcode();?>" width="220"
         style="border: 1px solid #ececec;display: block;margin: 24px auto 15px;">
    <div class="addon-content" style="margin-top: 10px">进入公众号-菜单-业务(代理)登录-忘记密码重置</div>
</div>
<script type="text/javascript" src="/Source/statics/js/jquery-2.2.4.min.js"></script>
<script type="text/javascript" src="/Source/layer/layer.js"></script>
<script type="text/javascript">
    $(function () {
        $("form").submit(function (e) {
            e.preventDefault(); 
            var ajax_data = $(this).serialize();
            var actionurl = $(this).attr("action");
            $.post(actionurl, ajax_data, function (data) {
                if (data.status == 1) {
                    msgs(data.info);
                    setTimeout(function () {
                        window.location.href = data.url
                    }, 3000);
                }
                else {
                    msgs(data.info);
                }
            }, 'json');
        });
    });

    //验证码更新
    function changeimg() {
        var times = new Date().getTime();
        document.getElementById('verify').src = "<?php echo U('load_verify');?>/time/" + times;
    }
    //消息提示框
    function msgs(txt) {
        layer.msg(txt, {time: 3000, offset: 0, shift: 6});
    }

    $('.close').click(function () {
        $('.canvas-bg,.qrcode-dialog').hide();
    });

    $('.edit_pass').click(function () {
        $('.canvas-bg,.qrcode-dialog').show();
    });


</script>

</body>
</html>