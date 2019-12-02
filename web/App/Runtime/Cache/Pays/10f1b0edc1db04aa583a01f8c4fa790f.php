<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <?php $temp=memberTemp($session_member['mid']);$activity=R('Common/MemberActivity/getListApi',[$session_member['mid'],$session_member['store_id']]); ?>
    <!--Time:2019年08月27日20:56:40  DevAuth:郑州讯龙软件科技有限公司 会员卡项目-->
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>激活会员卡</title>
    <link type="text/css" rel="stylesheet" href="/Source/hui/css/hui.css" />
    <style>
        body{background-color: #FFF}
        .card_bg{height:200px;margin: 40px 30px;background: rgba(0, 0, 0, 0) url('/Source/amp/member/card_bg/<?php echo ((isset($temp['bg']) && ($temp['bg'] !== ""))?($temp['bg']):'0'); ?>.png') no-repeat scroll 0% 0% / 100% 100%;position: relative;}
        .card_body{padding:20px 23px;}
        .card_name{font-size: 20px;font-weight: 600;}
        .card_slogan{margin: 6px 0 0;font-size: 15px;overflow: hidden;text-overflow:ellipsis;white-space: nowrap;}
        .card_ul{margin-top: 14px;font-size: 12px;line-height: 1.2;}
        .card_ul .item{margin-bottom: 5px;overflow: hidden;text-overflow:ellipsis;white-space: nowrap;font-size: 13px}
        .card_ul .item:before {display: inline-block;content: '';width: 10px;height: 10px;margin-right: 5px;-webkit-border-radius: 40%;-moz-border-radius: 40%;border-radius: 40%;}
        .card_0 .card_ul .item:before {background-color: #96d6ff;}
        .card_1 .card_ul .item:before {background-color: #f8ffff;}
        .card_2 .card_ul .item:before {background-color: #cfd0c2;}
        .card_3 .card_ul .item:before {background-color: #f9bdb4;}
        .card_4 .card_ul .item:before {background-color: #aaa;}
        .card_5 .card_ul .item:before {background-color: #eafdc0;}
        .card_0 *{color: #96d6ff;}
        .card_1 *{color: #f8ffff}
        .card_2 *{color: #cfd0c2}
        .card_3 *{color: #f9bdb4}
        .card_4 *{color: #aaa}
        .card_5 *{color: #eafdc0}
        input{text-align: right}
        .pay_btn_div{margin: 20px 10px}
        .pay_btn{height: 46px;line-height: 46px}
        .hui-primary{background: #1296db!important;}
        .hui-primary:active{background:#20a9f1 !important;}
        #reg{margin: 0 30px}
        .sms_btn{width:100px;border: 1px solid #1296db;color: #1296db;font-size: 12px;border-radius: 5px;padding: 3px;text-align: center}
        .sms{width:150px;border: 1px solid #b9b7c1;color: #b9b7c1;}
        .tj_btn{width:100px;border: 1px solid #1296db;color: #1296db;font-size: 12px;border-radius: 5px;padding: 6px 3px;text-align: center;float: right;margin: 0 30px 10px 30px;}
    </style>
</head>
<body>

<div class="card_bg card_<?php echo ((isset($temp['bg']) && ($temp['bg'] !== ""))?($temp['bg']):'0'); ?>" style="">
    <div class="card_body">
        <div class="card_name"><?php echo ((isset($temp['name']) && ($temp['name'] !== ""))?($temp['name']):'高级会员卡'); ?></div>
        <div class="card_slogan"><?php echo ((isset($temp['xc']) && ($temp['xc'] !== ""))?($temp['xc']):'欢迎使用本店会员卡'); ?></div>
        <ul class="card_ul card_<?php echo ((isset($temp['bg']) && ($temp['bg'] !== ""))?($temp['bg']):'0'); ?>">
            <?php if(is_array($activity)): foreach($activity as $key=>$v): ?><li class="item"><?php echo ($v["list_desc"]); ?></li><?php endforeach; endif; ?>
        </ul>
    </div>

</div>
<div class="tj_btn">
    填写推荐人
</div>
<div class="hui-wrap">

<form  class="hui-form" id="reg">

    <div class="hui-form-items">
        <div class="hui-form-items-title">会员名</div>
        <input type="text" class="hui-input hui-input-clear" name="name" placeholder="请输入您的会员名" required>
    </div>
    <div class="hui-form-items">
        <div class="hui-form-items-title">您的生日</div>
        <input type="text" id="sr" class="hui-input" placeholder="选填" name="birthday" >
    </div>
    <div class="hui-form-items">
        <div class="hui-form-items-title">手机号码</div>
        <input type="tel" class="hui-input" placeholder="请输入您的手机号" name="phone" required>
    </div>
    <div class="hui-form-items">
        <div class="hui-form-items-title">验证码</div>
        <input type="number" class="hui-input" name="verify" style="text-align: left" required>
        <div id="send_sms" class="sms_btn">
            点击获取
        </div>
    </div>
    <div class="pay_btn_div">
        <input type="hidden" name="tjr" value="">
        <input type="hidden" name="mid" value="<?php echo ($session_member['mid']); ?>">
        <input type="hidden" name="store_id" value="<?php echo ($session_member['store_id']); ?>">
        <input type="hidden" name="openid" value="<?php echo ($openid); ?>">
        <input type="hidden" name="openid_type" value="<?php echo ($openid_type); ?>_id">
        <button type="submit" class="hui-button hui-button-large hui-primary pay_btn">立即激活</button>
    </div>
</form>
</div>
<style>
    .tjr_div{overflow: inherit;display: none}
    .tjr_div_content{background: #FFFFFF;height: 6.9rem;width: 80%;border-radius: 5px;overflow: inherit;}
    .tjr_ico{position: fixed;z-index: 999;left: 33%;top: -50px;overflow: inherit;}
    .tjr_ico img{width: 100px}
    .tjr_msg{display:none;position: absolute;color: red;z-index: 99;font-size: 12px;text-align: center;margin-top: 35px;right: 15px;}
    .tjr_title{text-align: center;font-size: .5rem}
    .tjr_title p{font-size: .33rem;color: #0a6aa1}
    .tjr_form_div{text-align:center; color:#FFFFFF; line-height:60px;margin: 20px 5px}
    .tjr_form_btn{margin: 30px 10px 10px 10px;}
</style>
<div id="hui-black-mask" class="tjr_div">
    <div id="hui-black-mask-content" class="tjr_div_content">
        <div class="tjr_ico"><img  src="/Source/amp/member/yaoqing.png"></div>
        <div id="hui-black-action" style="top: 0;">
            <div id="hui-black-close" style="color: #000000;"></div>
        </div>
        <div class="tjr_title">填写推荐人<p>填写成功后完成首笔充值即可获得奖励</p></div>
        <div class="tjr_form_div">
            <div class="tjr_msg"></div>
            <form  class="hui-form" id="tjr">
                <div class="hui-form-items">
                    <div class="hui-form-items-title">手机号码</div>
                    <input type="tel" class="hui-input" placeholder="推荐人的手机号" id="tjrPhone" required>
                </div>
                <div class="tjr_form_btn">
                    <button type="submit" class="hui-button hui-button-large hui-primary pay_btn">确认提交</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript" src="/Source/hui/js/hui.js"></script>
<script type="text/javascript" src="/Source/jquery.min.js"></script>
<script type="text/javascript" src="/Source/Css/new_pay/js/fiex.js"></script>
<script type="text/javascript" src="/Source/amp/member/js/rolldate.min.js"></script>
<script type="text/javascript" src="/Source/assets/libs/jquery.cookie/jquery.cookie.js"></script>
<script type="text/javascript">
    $('#hui-black-close').click(function () {
        $('#hui-black-mask').hide();
    });
    $('.tj_btn').click(function () {
        $('#hui-black-mask').show();
    });
    new Rolldate({
        el: '#sr',
        format: 'MM/DD',
        init: function() {
            document.activeElement.blur();
        }
    });
    $('input').on('blur', function () {
        setTimeout(function(){
            var scrollHeight = document.documentElement.scrollTop || document.body.scrollTop || 0;
            window.scrollTo(0, Math.max(scrollHeight - 1, 0));
        }, 100);
    });
    var sms_btn=$('#send_sms');
    sms_btn.click(function (e) {
        var hasSms=sms_btn.hasClass('sms'),phone=$("[name='phone']").val();
        var telReg = !!phone.match(/^1[3|4|5|6|7|8|9][0-9]{9}$/);
        if (telReg == false) {
            hui.toast('请输入正确的手机号');
            return;
        }
        if(true != hasSms) {
            hui.loading('短信发送中');
            $.post('<?php echo U('sendSms');?>', {phone:phone}, function (e) {
                if (e.status == 1) {
                    sendSmsTime();
                }else {
                    sendSmsTime('rest');
                    hui.toast(e.info);
                }
            }, 'json');
        }
    });

    if($.cookie("captcha")){
        var count = $.cookie("captcha");
        sms_btn.html("重新获取("+count+")").addClass('sms');
        var resend = setInterval(function(){
            count--;
            if (count > 0){
                sms_btn.html("重新获取("+count+")").addClass('sms');
                $.cookie("captcha", count, {path: '/', expires: (1/86400)*count});
            }else {
                clearInterval(resend);
                sms_btn.html("点击获取").removeClass('sms');
            }
            hui.closeLoading();
        }, 1000);
    }

    /**
     * 倒计时
     * @param t 倒计时开始/rest 恢复
     */
    function sendSmsTime(t) {
        if(t=='rest'){
            sms_btn.html("点击获取").removeClass('sms');
            hui.closeLoading();
        }else {
            var count = 60;
            var resend = setInterval(function () {
                count--;
                if (count > 0) {
                    sms_btn.html("重新获取(" + count + ")").addClass('sms');
                    $.cookie("captcha", count, {path: '/', expires: (1 / 86400) * count});
                } else {
                    clearInterval(resend);
                    sms_btn.html("点击获取").removeClass('sms');
                }
                hui.closeLoading();
            }, 1000);
        }
    }
    $('#reg').submit(function(e){
        e.preventDefault(); //阻止自动提交表单
        var name=$('[name="name"]').val(),verify=$('[name="verify"]').val(),phone=$("[name='phone']").val();
        var telReg = !!phone.match(/^1[3|4|5|6|7|8|9][0-9]{9}$/);
        if(name==''){
            hui.toast('请输入会员名');
            return;
        }
        if (telReg == false) {
            hui.toast('请输入正确的手机号');
            return;
        }
        if(verify==''){
            hui.toast('请输入验证码');
            return;
        }
        var data=$('#reg').serialize();  //表单数据
        //提交数据
        $.post('<?php echo U('reg');?>', data, function (e) {
            if (e.status == 1) {
                window.location.href=e.url;
            }else {
                hui.toast(e.info);
            }
        }, 'json');
    });
    $('#tjr').submit(function (e) {
        e.preventDefault(); //阻止自动提交表单
        var phone=$('#tjrPhone').val(),mid=$('[name="mid"]').val();
        var telReg = !!phone.match(/^1[3|4|5|6|7|8|9][0-9]{9}$/);
        if (telReg == false) {
            $('.tjr_msg').text('请输入正确的手机号').show();
            setTimeout(function () {
                $('.tjr_msg').hide();
            },3000);
            return;
        }
        $.post('<?php echo U('tjrCheck');?>', {phone:phone,mid:mid}, function (e) {
            if (e.status == 1) {
                $('[name="tjr"]').val(phone);
                $('#hui-black-mask').hide();
            }else {
                $('.tjr_msg').text(e.info).show();
            }
        }, 'json');
    })
</script>
</body>
</html>