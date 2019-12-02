<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>未关注公众号</title>
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport"/>
    <meta content="yes" name="apple-mobile-web-app-capable"/>
    <meta content="black" name="apple-mobile-web-app-status-bar-style"/>
    <meta content="telephone=no" name="format-detection"/>
    <link rel="stylesheet" href="/Source/mui/css/mui.min.css">
    <link rel="stylesheet" href="/Source/ydui/css/ydui.css"/>
    <link rel="stylesheet" href="/Source/ydui/css/my-app.css"/>
</head>
<body>
<section class="g-flexview">
    <div class="g-scrollview" style="margin-top: 0px">
        <div>
            <img src="<?php echo GetMchQrcode();?>" style="width: 200px;height: 200px;border-radius: 5px;clear: both;display: block;margin: 100px auto 10px auto;border: 2px solid #ffffff">
            <div style="background: #DFE1E2;border-radius: 5px; padding: 5px;margin:20px;color: #696969;text-align: center">
                <p>您未关注官方公众号<br>请长按识别关注公众号后再扫码注册</p>
            </div>
        </div>
        <aside class="demo-tip" style="margin-top: 30px">
            <p>关注说明:</p><br>
            <b>为什么关注公众号?</b><br>
            答: 为了方便接收到交易、提现结果等微信模板消息通知,方便后续使用相关功能!<br>
        </aside>
</section>
<script src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="/Source/ydui/js/ydui.flexible.js"></script>
</body>
</html>