<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <META HTTP-EQUIV="Expires" CONTENT="0">
    <meta name="screen-orientation" content="portrait">
    <meta name="x5-orientation" content="portrait">
    <meta name="format-detection" content="telephone=no"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1,user-scalable=0"/>
    <title><?php echo ($title); ?></title>
    <link rel="stylesheet" href="/Ext?g=pay_new_css">
    <style>*{line-height: inherit;}
    .wbscms_payimg{top:20px;width:250px;padding:10px;margin:0 auto;border-radius:40px;height:250px; border: 1px dashed #797979;line-height: 235px;position: relative;  text-align: center; }
    .wbscms_payimg img{display:block;text-align:center;width:250px;height:250px; }
    .layui-m-layerchild{padding: 10px;border-radius:10px!important;}
    .layui-m-layerchild h3{display: none}
    .layui-m-layer{z-index: 999!important;}
    .pay_explain{padding-top: 30px;text-align:center;font-size:14px;color:#545454;}
    </style>
</head>
<body class="pay_page" onload="total_init()">
<!-- 头部信息 -->
<div class="head">
    <ul class="merchant_info" >
        <li><img src="/Source/Css/new_pay/img/mch_log.png"></li>
        <li><?php echo ($store["name"]); ?></li>
    </ul>
    <div class="amount_box">
        <div class="amount_input">
            <span class="fl">支付金额：</span><span class="amount fr " id="amount" style="position: relative;"></span>
        </div>
    </div>
</div>
<div class="border_line"></div>

<!-- 支付方式 -->
<div class="pay_method">
    <div class="pxline"></div>
    <!-- default -->
    <ul id="defaultPay" class="method" method='wx'>
        <li class="wx"></li>
        <li class="method_tip">
            <div>
                微信收款<br><span>输入金额点击收款</span>
            </div>
        </li>
        <li class="select_box selected"></li>
    </ul>

    <!-- 快捷 -->
    <ul id="quickPay" class="method" method='ali'>
        <li class="zfb"></li>
        <li class="method_tip">
            <div>
                支付宝收款<br><span>输入金额点击收款</span>
            </div>
        </li>
        <li class="select_box"></li>
    </ul>

    <!-- 更多 -->
    <div class="more_method hide">
        <ul>
            <li>选择更多支付方式</li>
            <li></li>
        </ul>
    </div>

</div>
<input type="hidden" id="pay" value="wx">
<p class="showTips" style="bottom: 6.4rem;">由<?php echo DomainAuthData('web_name'); ?>提供技术支持</p>
<div id="fullbg"></div>
<!--  键盘  -->
<table id="keyboard">
    <tr>
        <td class="num">1</td>
        <td class="num">2</td>
        <td class="num">3</td>
        <td rowspan="2" class="del"></td>
    </tr>
    <tr>
        <td class="num">4</td>
        <td class="num">5</td>
        <td class="num">6</td>
    </tr>
    <tr>
        <td class="num">7</td>
        <td class="num">8</td>
        <td class="num">9</td>
        <td rowspan="2" class="complete">收款</td>
    </tr>
    <tr>
        <td class="num dot">.</td>
        <td class="num" colspan="2">0</td>
        <!--<td class="key"></td>-->
    </tr>
</table>

<script type="text/javascript" src='/Source/jquery.min.js'></script>
<script type="text/javascript" src="/Source/Css/new_pay/js/fiex.js"></script>
<script type="text/javascript" src="/Source/ydui/js/ydui.js"></script>
<script type="text/javascript" src="/Source/layer/mobile/layer.js"></script>
<script type="text/javascript" src="/Source/Css/new_pay/js/xun_scan_vendor.js?v=<?php echo time();?>"></script>
<script type="text/javascript" src="http://chencunlong.oss-cn-hangzhou.aliyuncs.com/qrcode.js"></script>
<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script type="text/javascript">
    <?php if(($data["type"]) == "1"): ?>wx.config(<?php echo (json_encode($jsapi)); ?>);<?php endif; ?>
    var xun_data=['<?php echo ($data["type"]); ?>','','<?php echo ($store["id"]); ?>','<?php echo ($store["sid"]); ?>'];
    var dialog = YDUI.dialog;
</script>
</body>
</html>