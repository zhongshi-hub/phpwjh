<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <?php if(isset($_GET['uid'])): ?><title>加入门店成功</title>
        <?php else: ?>
        <?php if(($info['subscribe']) != "1"): ?><title>未关注公众号</title>
            <?php else: ?>
            <title>店员入驻门店</title><?php endif; endif; ?>
    <meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1,user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <!--标准mui.css-->
    <link rel="stylesheet" href="/Source/mui/css/mui.min.css">
    <link rel="stylesheet" type="text/css" href="/Source/mui/css/icons-extra.css" />
</head>

<body>
<?php if(isset($_GET['uid'])): ?><div class="mui-content">
        <div class="mui-card" style="margin-top: 20%;border-radius: 10px">
            <div class="mui-content-padded" style="text-align: center">
                <span class="mui-icon-extra mui-icon-extra-holiday" style="font-size: 80px;color: #3c3f41"></span>
            </div>
            <div class="mui-card-content">
                <div class="mui-card-content-inner">
                    <p>温馨提示:</p>
                    <p style="font-size: 25px;margin: 28px;color: red;text-align: center">门店:<?php echo ($user["name"]); ?></p>
                    <p style="font-size: 18px;margin: 28px;color: #00D062;text-align: center">状态: 加入成功</p>
                    <p style="color: #333;text-align: left;padding-top:20px;">
                        注意:绑定门店后,如您取消关注了官方公众号!您将无法进行相关操作和接收付款提醒!</p>
                    <p style="color: #333;text-align: left"> 每个微信只能绑定一个门店,如已绑定其它门店,请登录微信端解绑后再进行新的门店绑定操作!</p>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <?php if(($info['subscribe']) != "1"): ?><div class="mui-content">
            <div class="mui-card" style="margin-top: 20%;border-radius: 10px">
                <div class="mui-card-content" style="text-align: center">
                    <img src="<?php echo ($weixin["qrc_img"]); ?>" style="width: 300px;height: 300px"/>
                </div>
                <div class="mui-card-content">
                    <div class="mui-card-content-inner">
                        <p>温馨提示:</p>
                        <p style="color: #333;text-align: center">识别二维码 或 微信添加公众号 <?php echo ($weixin["name"]); ?> </p>
                        <p style="color: red;text-align: center">您未关注公众号!请关注后再试! (长按二维码识别)</p>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="mui-content" style="margin-top: 20%;">
            <div class="mui-content-padded" style="text-align: center">
                <span class="mui-icon mui-icon-home" style="font-size: 50px"></span>
                <p style="font-size: 25px;margin-top: 20px;"><?php echo ($store["name"]); ?></p>
            </div>
            <form action="<?php echo U('store_user_save');?>" style="margin-top: 50px;">
                <div class="mui-input-group">
                    <div class="mui-input-row">
                        <label>姓名</label>
                        <input name="username" type="text" class="mui-input-clear mui-input" placeholder="请输入您的姓名"
                               required>
                    </div>
                    <div class="mui-input-row">
                        <label>联系电话</label>
                        <input name="phone" type="text" class="mui-input-clear mui-input" placeholder="请输入手机号" required>
                    </div>
                    <input type="hidden" name="wx_name" value="<?php echo ($info["nickname"]); ?>">
                    <input type="hidden" name="wx_openid" value="<?php echo ($info["openid"]); ?>">
                    <input type="hidden" name="wx_imgurl" value="<?php echo ($info["headimgurl"]); ?>">
                    <input type="hidden" name="store_id" value="<?php echo ($store["id"]); ?>">
                    <input type="hidden" name="sid" value="<?php echo ($store["sid"]); ?>">
                </div>
                <div class="mui-content-padded">
                    <button type="submit" class="mui-btn mui-btn-success mui-btn-block">确认加入</button>
                </div>
            </form>

            <div class="mui-content-padded oauth-area">
            </div>
        </div><?php endif; endif; ?>
</body>
<script type="text/javascript" src="/Source/mui/js/mui.min.js"></script>
<script type="text/javascript" src="/Source/statics/js/jquery-2.2.4.min.js"></script>
<script>
    $("form").submit(function (e) {
        var telNo = $('[name="phone"]').val();
        if (telNo == "") {
            mui.alert('请输入手机号');
            return false;
        }
        var telReg = !!telNo.match(/^1[3|4|5|7|8][0-9]{9}$/);
        if (telReg == false) {
            mui.alert('请输入正确的手机号');
            return false;
        }

        e.preventDefault(); //阻止自动提交表单
        var ajax_data = $(this).serialize();
        var actionurl = $(this).attr("action");
        $.post(actionurl, ajax_data, function (data) {
            if (data.status == 1) {
                mui.alert(data.info, '提示', function () {
                    window.location.href = data.url;
                });
            } else {
                mui.alert(data.info);
            }
        }, 'json');
    });
</script>
</html>