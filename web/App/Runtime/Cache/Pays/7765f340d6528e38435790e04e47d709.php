<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <META HTTP-EQUIV="Expires" CONTENT="0">
    <meta name="screen-orientation" content="portrait">
    <meta name="x5-orientation" content="portrait">
    <meta name="format-detection" content="telephone=no"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1,user-scalable=0,viewport-fit=cover"/>
    <title>向商家付款</title>
    <link rel="stylesheet" href="/Ext?g=pay_new_css">
    <style>
        /*html{font-weight: bold}*/
        .clearfix:after{
            content:'';
            visibility:hidden;
            font-size:0;
            height:0;
            display:block;
            clear:both;
        }

        .container{
            width:100%;
            /*text-align:center;*/
        }

        ul{
            display:inline-block;
        }
        .xun_remark.m-cell .cell-left{font-size: 14px!important;margin: 10px} .xun_remark.m-cell:after{border-bottom: 1px solid #ebebeb;}</style>

</head>
<body class="pay_page" onload="total_init()">
<!-- 头部信息 -->
<div class="head">
    <div class="container">
        <ul class="merchant_info clearfix">
            <li><img src="/Source/Css/new_pay/img/mch_log.png"></li>
            <li <?php $name=(string)$store['name'];$len=strlen($name); if($len>40){echo 'style="font-size: 0.4rem;"';}; ?>><?php echo ($store["name"]); ?></li>
        </ul>
    </div>

    <div class="amount_box">
        <div class="amount_input">
            <span class="fl">支付金额：</span><span class="amount fr " id="amount" style="position: relative;"></span>
        </div>
    </div>
</div>
<div class="border_line"></div>
<div class="xun_remark m-cell">
    <div class="cell-item">
        <div class="cell-left">备注信息：</div>
        <div class="cell-right"><input type="text" id="remark" name="remark" class="cell-input" placeholder="备注信息最长30字 选填" autocomplete="off" /></div>
    </div>
</div>

<p class="showTips" style="bottom: 7.4rem;">由<?php echo DomainAuthData('web_name'); ?>提供技术支持</p>
<div id="fullbg"></div>
<!--  键盘  -->
<table id="keyboard" style="height: 7.4rem">
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
        <td rowspan="2" class="complete">支付</td>
    </tr>
    <tr>
        <td class="num dot">.</td>
        <td class="num" colspan="2">0</td>
        <!--<td class="key"></td>-->
    </tr>
</table>


<script type="text/javascript" src="/Ext?g=pay_new_remark_js"></script>
<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script type="text/javascript">
    var xun_data=['<?php echo ($user_agent); ?>','<?php echo ($openid); ?>','<?php echo ($store["id"]); ?>','<?php echo ($store["sid"]); ?>'];
    var dialog = YDUI.dialog;
    // if(xun_data[0]=='ali'){
    //     $('.wx').addClass('zfb').siblings('.method_tip').children('div').html("支付宝支付<br><span>小额支付点这里</span>");
    // }
    // var mch_alley='<?php echo ($mch_alley); ?>';
    // if(mch_alley==0){
    //     $("#pay").attr('method', 'quick');
    //     $("#defaultPay").hide().children(".select_box").removeClass('selected');
    //     $('#quickPay').children(".select_box").addClass('selected');
    // }else{
    //     $("#pay").attr('method', 'default');
    //     $("#defaultPay").show().children(".select_box").addClass('selected');
    //     $('#quickPay').children(".select_box").removeClass('selected');
    // }
</script>
</body>
</html>