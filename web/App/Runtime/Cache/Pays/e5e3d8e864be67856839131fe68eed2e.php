<?php if (!defined('THINK_PATH')) exit();?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1,user-scalable=0">
    <title>会员卡消费</title>
    <script type="text/javascript" src="/Source/Css/new_pay/js/fiex.js"></script>
    <link href="/Source/Css/new_pay/css/base_style.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="/Source/Css/new_pay/css/quick_payment.css">
</head>
<body class="paymentResultPage">
<div class='pay_suc'>
    <div class="content" style="border-radius: 10px;">
        <div class="content_head">
            <p>会员卡消费金额</p>
            <p><span class="amount"><?php echo ($total); ?></span></p>
        </div>
        <?php $memberTotal=memberOrderTotal($user_id); ?>
        <div style="border-bottom: 1px solid #e7e7e7;padding: 10px 20px;color:#0d88e6">会员卡余额：<span style="float: right" >￥<?php echo ((isset($memberTotal) && ($memberTotal !== ""))?($memberTotal):'0.00'); ?></span></div>
        <div class="pay_info">
            <ul>
                <li>商户名称</li>
                <li id="good_name"><?php $data=Get_Seller($mid); echo $data['mch_name']; ?></li>
            </ul>
            <ul>
                <li>门店名称</li>
                <li ><?php $data=Get_Store($store_id); echo $data['name']; ?></li>
            </ul>
            <ul>
                <li>交易时间</li>
                <li id="date"><?php echo (date('Y-m-d H:i:s',$create_time)); ?></li>
            </ul>
            <ul>
                <li>交易单号</li>
                <li id="order_id"><?php echo ($out_trade_no); ?></li>
            </ul>
        </div>
    </div>
</div>
</body>
<script type="text/javascript" src="/Source/jquery.min.js"></script>
<script type="text/javascript">
    $(function(){
        $(".complete_btn,.repay").on("click",toScanPay);
        var status='ok';
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