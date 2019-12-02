<?php if (!defined('THINK_PATH')) exit();?><!--<!doctype html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    <title>温馨提示</title>

    <style>
       .button { display: inline-block; padding: 0px 8px; border: 0px solid; height: 40px; overflow: hidden; text-align: center; vertical-align: middle; background-image: none; outline: medium none; -moz-user-select: none; box-sizing: border-box; }
       .button { line-height: 40px; }
       .button.block { display: block; }
       .xunmafu-error{height: 250px; width: 250px;margin: 0px auto; background-size: contain;background-image: url("/Source/svg/error.svg")}
       .xunmafu-success{height: 250px; width: 250px;margin: 0px auto; background-size: contain;background-image: url("/Source/svg/success.svg")}
       .error{ color: #888; text-align: center; margin-top: 20px; margin-bottom: 30px; }
    </style>
</head>
<body>
<div>
    <div style="width:300px;margin:0px auto;margin-top: 44px;">
        <?php if(isset($message)) {?>
        <div class="xunmafu-success"></div>
        <p class="error"><?php echo($message); ?></p>
        <?php }else{?>
        <div class="xunmafu-error"></div>
        <p class="error"><?php echo($error); ?></p>
        <?php }?>
        <?php if($waitSecond!=888) {?>
        <a id="href" href="<?php echo($jumpUrl); ?>" class="button submit block" style="text-decoration: none;margin:50px 10px 100px 10px;background-color: #88d038;color: #fff;border-radius: 20px;" >页面自动跳转 等待时间： <b id="wait" style="font-size: 20px"><?php echo($waitSecond); ?></b> 秒</a>
        <?php }?>
    </div>
</div>
<?php if($waitSecond!=888) {?>
<script type="text/javascript">
    (function(){
        var wait = document.getElementById('wait'),href = document.getElementById('href').href;
        var interval = setInterval(function(){
            var time = --wait.innerHTML;
            if(time <= 0) {
                location.href = href;
                clearInterval(interval);
            }
        }, 1000);
    })();
</script>
<?php }?>
</body>
</html>

-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1,user-scalable=0">
    <title>温馨提示</title>
    <script type="text/javascript" src="/Source/Css/new_pay/js/fiex.js"></script>
    <link href="/Source/Css/new_pay/css/base_style.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="/Source/Css/new_pay/css/quick_payment.css">
    <?php if(isset($message)) {?>
    <style>
        .paymentResultPage .pay_fail .repay{
            line-height: 0.9rem;
            height: 0.9rem;
            color: #1296db;
            width: 6rem;
            background-color: transparent;
            border: 1px solid #1296db;
            font-size: 0.3rem;
            padding: 10px;
            margin-top: 10px;
        }
    </style>
    <?php }else{?>
    <style>
        .paymentResultPage .pay_fail .repay{
            line-height: 0.9rem;
            height: 0.9rem;
            color: #fc485a;
            width: 6rem;
            background-color: transparent;
            border: 1px solid #fc485a;
            font-size: 0.3rem;
            padding: 10px;
            margin-top: 10px;
        }
    </style>
    <?php }?>
</head>
<body class="paymentResultPage" style="background-color:#ffffff">
<?php if(isset($message)) {?>
<div class="pay_fail" style="padding-top: 6rem;background: url('/Source/suc.png') no-repeat center 1.5rem">
    <p style="font-size: 0.53rem">温馨提示</p>
    <p id="failure_msg"><?php echo($message); ?></p>
    <?php }else{?>
    <div class="pay_fail" style="padding-top: 6rem;background: url('/Source/err.png') no-repeat center 1.5rem">
        <p style="font-size: 0.53rem">温馨提示</p>
        <p id="failure_msg"><?php echo($error); ?></p>
        <?php }?>
        <?php if($waitSecond!=888) {?>
        <br>
        <a class="repay" id="href" href="<?php echo($jumpUrl); ?>">页面自动跳转 等待时间： <b id="wait" style="font-size:18px"><?php echo($waitSecond); ?></b> 秒</a>
        <?php }?>
    </div>
</body>
<script type="text/javascript" src="/Source/jquery.min.js"></script>
<?php if($waitSecond!=888) {?>
<script type="text/javascript">
    (function(){
        var wait = document.getElementById('wait'),href = document.getElementById('href').href;
        var interval = setInterval(function(){
            var time = --wait.innerHTML;
            if(time <= 0) {
                location.href = href;
                clearInterval(interval);
            }
        }, 1000);
    })();
</script>
<?php }?>
</html>