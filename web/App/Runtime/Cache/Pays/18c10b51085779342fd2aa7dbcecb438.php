<?php if (!defined('THINK_PATH')) exit(); $ad=ad_time_id('pay_success'); ?>
<?php if($ad['data']['type_show']==1&&$ad['status']==1){ ?>
<meta http-equiv="refresh" content="0; url=<?php echo $ad['data']['url']; ?>">
<?php }else{ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1,user-scalable=0">
    <title>交易详情</title>
    <script type="text/javascript" src="/Source/Css/new_pay/js/fiex.js"></script>
    <link href="/Source/Css/new_pay/css/base_style.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="/Source/Css/new_pay/css/quick_payment.css">
</head>
<body class="paymentResultPage">
<div class='pay_suc'>
    <div class="content">
        <div class="content_head">
            <p>支付金额</p>
            <p>￥<span class="amount"><?php echo ($total); ?></span></p>
        </div>
        <div class="pay_info">
            <ul>
                <li>商户名称</li>
                <li id="good_name"><?php echo ($mch_name); ?></li>
            </ul>
            <ul>
                <li>交易时间</li>
                <li id="date"><?php echo ($time); ?></li>
            </ul>
            <ul>
                <li>交易单号</li>
                <li id="order_id"><?php echo ($order_id); ?></li>
            </ul>
        </div>
    </div>
    <div class="complete_btn">完成</div>
</div>
<div class="pay_fail hide">
    <p>支付失败</p>
    <p id="failure_msg">支付遇到问题，请尝试重新支付</p>
    <div class="repay">重新支付</div>
</div>
<?php if($ad['status']==1&&$ad['data']['type_show']!=1){ ?>
<!--广告代码开始-->
<link rel="stylesheet" type="text/css" href="/Source/Css/ad.css">
<div class="AdData">
    <div class="XunAd XunAd_line"><span class="XunAd__tips">广告</span></div>
    <a href="<?php echo $ad['data']['url']; ?>">
        <img src="<?php echo $ad['data']['img']; ?>"  title="AD1">
    </a>
</div>
<!--广告代码结束-->
<?php } ?>
</body>
<script type="text/javascript" src="/Source/jquery.min.js"></script>
<script type="text/javascript">
    $(function(){
        $(".complete_btn,.repay").on("click",toScanPay);
        var status='<?php echo ($status); ?>';
        if(status=='ok'){
            $('.pay_fail').hide();
            $('.pay_suc').show();
        }else{
            $('.pay_fail').show();
            $('#failure_msg').html(status);
            $('.pay_suc').hide();
        }
    });
    // 到扫码支付页
    function toScanPay(){
        window.location.href='<?php echo ($url); ?>';
    }
</script>
</html>
<?php } ?>