<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/html">
<head>
    <title>商户状态</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <link rel="stylesheet" href="/Source/RegUI/lib/weui.min.css">
</head>

<body ontouchstart>

<div class="weui-msg" style="margin-top: 2%">

    <?php if($seller['status'] == 1): ?><div class="weui-msg__icon-area"><i class="weui-icon-success weui-icon_msg"></i></div>
        <h2 class="weui-msg__title">审核通过</h2>
        <div class="weui-loadmore weui-loadmore_line">
            <span class="weui-loadmore__tips"><?php echo ($seller["mch_name"]); ?>(<?php echo ($codes); ?>)</span>
        </div>
        <p class="weui-msg__desc" style="text-indent:20px;text-align: center;margin: 20px">您提交的商户信息已通过审核!</p>
        <?php elseif($seller['status'] == 2): ?>
        <div class="weui-msg__icon-area"><i class="weui-icon-warn weui-icon_msg"></i></div>
        <h2 class="weui-msg__title">未通过!被拒绝!</h2>
        <div class="weui-loadmore weui-loadmore_line">
            <span class="weui-loadmore__tips"><?php echo ($seller["mch_name"]); ?>(<?php echo ($codes); ?>)</span>
        </div>
        <p class="weui-msg__desc" style="text-indent:20px;text-align: center;margin: 20px;color: red;"> <?php echo ($seller["info"]); ?></p>
        <?php elseif($seller['status'] == 3): ?>
        <div class="weui-msg__icon-area"><i class="weui-icon-warn weui-icon_msg"></i></div>
        <h2 class="weui-msg__title">资料不符合</h2>
        <div class="weui-loadmore weui-loadmore_line">
            <span class="weui-loadmore__tips"><?php echo ($seller["mch_name"]); ?>(<?php echo ($codes); ?>)</span>
        </div>
        <p class="weui-msg__desc" style="text-indent:20px;text-align: center;margin: 20px;color: red;"><?php echo ($seller["info"]); ?></p>
        <p class="weui-msg__desc" style="text-indent:20px;text-align: center;margin: 20px;color: red;">
            <a href="<?php echo U('EditData',array('id'=>$seller['id']));?>" style="background-color: #4282e3;color: #fff;padding: 5px 10px;font-size:12px;border-radius: 5px;">点击修改资料</a>
        </p>
        <?php else: ?>
        <div class="weui-msg__icon-area"><i class="weui-icon-waiting weui-icon_msg"></i></div>
        <h2 class="weui-msg__title">审核中</h2>
        <div class="weui-loadmore weui-loadmore_line">
            <span class="weui-loadmore__tips"><?php echo ($seller["mch_name"]); ?>(<?php echo ($codes); ?>)</span>
        </div>
        <p class="weui-msg__desc" style="text-indent:20px;text-align: left;margin: 20px">您提交的商户信息正在审核!审核通过后我们会已短信形式告知!</p><?php endif; ?>
    


    <!--<div class="weui-loadmore weui-loadmore_line">
        <span class="weui-loadmore__tips">收款码ID:<?php echo ($codes); ?></span>
    </div>-->
    <div class="weui-uploader__bd">
        <div style="width: 45%;float: left;">
            <img src="/Source/RegUI/qrcode.png" width="100px" onclick="Open_Codes('<?php echo sc_codes($codes);?>')">
            <p style="font-size: 14px;color: #999;">我的收款码</p>
        </div>
        <div style="width: 45%;float: left;margin-left: 10px">
            <img src="<?php echo GetMchQrcode();?>" width="100px" onclick="Open_Codes('<?php echo GetMchQrcode();?>')">
            <p style="font-size: 14px;color: #999">关注我们查流水</p>
        </div>
    </div>

    <div class="weui-msg__extra-area">
        <div class="weui-footer">
            <p class="weui-footer__text">Copyright © <?php echo ($_domain['web_name']); ?> 2016-2017</p>
        </div>
    </div>
</div>
<script src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="/Source/fancybox/jquery.fancybox.js"></script>
<link rel="stylesheet" href="/Source/fancybox/jquery.fancybox.css"/>
<script type="text/javascript">
    function Open_Codes(url) {
        if (url) {
            $.fancybox.open(url);
        } else {
            alert('获取收款码失败');
        }
    }
</script>
</body>
</html>