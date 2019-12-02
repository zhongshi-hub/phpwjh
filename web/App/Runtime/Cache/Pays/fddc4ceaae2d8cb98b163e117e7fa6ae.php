<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <?php $memberTemp=memberTemp($store['sid']);$memberUser=memberUser($store['sid'],$openid);$isMember=$memberUser['num'];$memberTotal=memberOrderTotal($memberUser['id']);$cz=R('Common/MemberActivity/getDataArr',[$store['sid'],$store['id'],'cz']); $rule_desc=explode(';',$cz['rule_desc']); ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <META HTTP-EQUIV="Expires" CONTENT="0">
    <meta name="screen-orientation" content="portrait">
    <meta name="x5-orientation" content="portrait">
    <meta name="format-detection" content="telephone=no"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1,user-scalable=0,viewport-fit=cover"/>
    <title>向商家付款</title>
    <link rel="stylesheet" href="/Ext?g=pay_new_css">
    <style>
        .clearfix:after{content:'';visibility:hidden;font-size:0;height:0;display:block;clear:both;}
        .container{width:100%;}
        .xun_remark.m-cell .cell-left{font-size: 14px!important;margin: 10px} .xun_remark.m-cell:after{border-bottom: 1px solid #ebebeb;}
        .pay_method .method {line-height: 1.2rem;height: 1.2rem;}
        .pay_method .method .method_tip div{height: 1.2rem;font-size: 0.4rem;}
        .pay_method .method li:nth-of-type(1) {width: 1.4rem;}
        .pay_method .method .qucik,.pay_method .method .wx{background-size: 0.6rem auto;}
        .pay_method .method .select_box{background-size: 0.5rem;width: 1.5rem;}
        .pay_method .method .method_tip {width: 2.5rem;}
        .pay_method ul{display: block}
        .pay_method .method .selected{background: url(/Source/amp/member/selected.png?t=1) no-repeat center;background-size: 0.5rem;}
    </style>

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
<?php if(($memberTemp["status"]) == "1"): ?><!-- 支付方式 -->
<div class="pay_method">
    <div class="pxline"></div>
    <ul id="defaultPay" class="method" method='default'>
        <li class="wx"></li>
        <li class="method_tip">
            <div>
                微信支付
            </div>
        </li>
        <li class="select_box selected" data-type="default"></li>
    </ul>

    <ul id="quickPay" class="method" method='quick'>
        <li class="qucik"></li>
        <li class="method_tip">
            <div>
                会员卡支付
            </div>
        </li>
        <li style="height: 1.2rem;font-size: 0.33rem;line-height: 1.2rem;color: #6f6f6f;"><?php echo ($rule_desc[0]); ?></li>
        <?php if(empty($isMember)): ?><li class="card_reg" style="float: right;line-height: 1.2rem;color:#4270d2;margin-right: 0.5rem">激活</li>
            <?php else: ?>
            <li class="select_box" data-type="card"></li><?php endif; ?>
    </ul>
    <?php if(!empty($isMember)): ?><ul  class="method">
        <li style="height: 1.2rem;font-size: 0.36rem;line-height: 1.2rem;color: #6f6f6f;width: auto;margin-left: 1.4rem;">会员卡余额:<?php echo ((isset($memberTotal) && ($memberTotal !== ""))?($memberTotal):'0.00'); ?>元</li>
        <li class="card_pay" style="float: right;line-height: 1.2rem;color:#4270d2;margin-right: 0.5rem" data-uid="<?php echo ($memberUser['id']); ?>" data-num="<?php echo ($memberUser['num']); ?>">充值</li>
    </ul><?php endif; ?>
</div><?php endif; ?>
<p class="showTips" style="bottom: 6.8rem;">由<?php echo DomainAuthData('web_name'); ?>提供技术支持</p>
<div id="fullbg"></div>
<!--  键盘  -->
<table id="keyboard" style="height: 6.8rem">
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


<script type="text/javascript" src="/Source/jquery.min.js"></script>
<script type="text/javascript" src="/Source/Css/new_pay/js/fiex.js"></script>
<script type="text/javascript" src="/Source/ydui/js/ydui.js"></script>
<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script type="text/javascript">
    var xun_data=['<?php echo ($user_agent); ?>','<?php echo ($openid); ?>','<?php echo ($store["id"]); ?>','<?php echo ($store["sid"]); ?>'];
    var dialog = YDUI.dialog;
    var box=$('.select_box');
    box.click(function (e) {
        $('.pay_method ul li').removeClass('selected');
        $(this).addClass('selected');
    });
    if('<?php echo ($user_agent); ?>'=='ali'){
        $('.wx').addClass('zfb').siblings('.method_tip').children('div').html("支付宝支付");
    }
    //激活
    $('.card_reg').click(function () {
        window.location.href="<?php echo U('pays/member/reg');?>";
    });
    //充值
    $('.card_pay').click(function () {
        window.location.href="<?php echo U('pays/member/card');?>";
    });
    $("#amount").text("询问商家后输入").on("click", callKeyboard);
    $("#keyboard td").on("touchstart", keyboardInput);
    function callKeyboard() {
        if ($("#trink").length == 0) {
            var temp = $("#amount").html();
            var hstr = '<span id="trink">|</span>';
            if (temp.indexOf("询问商家后输入") != -1) {
                $(this).html(hstr).addClass("rmb_icon")
            } else {
                $(this).html(temp + hstr);
            }
            $("#keyboard").slideDown("fast");
        }
    }
    function keyboardInput(e) {
        e.preventDefault(); //阻止浏览器默认行为
        var temp2 = $("#amount").text();//文字或金额
        if (temp2.indexOf('|') != -1) {
            temp2 = temp2.substring(0, temp2.length - 1);
        }
        var hstr = '<span id="trink">|</span>';
        var addnum = $(this).html();
        if ($(this).hasClass("del")) {
            if (temp2 != "") {
                var new_num = temp2.slice(0, temp2.length - 1);
                $("#amount").html(new_num + hstr);
            }
        } else if ($(this).hasClass("complete")) {
            if($(this).hasClass("input")) {
                toPay();
                return;
            }
        } else if ($(this).hasClass("dot")) {
            if (temp2 == "") {
                $("#amount").html("0" + addnum + hstr);
            } else if (temp2.indexOf(".") != "-1") {
            } else {
                $("#amount").html(temp2 + addnum + hstr);
            }
        } else {
            if (temp2 > 100000) {
                alert("支付超限");
                return;
            }
            if (temp2.indexOf(".") != "-1") {
                var s = temp2.split(".");
                if (s[1].length >= 2) {
                    console.log(s[1]);
                    return;
                }
            }
            if (temp2[0] == 0 && temp2[1] != ".") {
                addnum = '';
            }
            if ((temp2 + addnum) > 100000) {
                return;
            }
            $("#amount").html(temp2 + addnum + hstr);
        }
        changeColor();
    }


    // 根据输入内容改变style
    function changeColor() {
        var temp = $("#amount").text();
        if (temp.indexOf("|") != -1) {
            temp = temp.substring(0, temp.length - 1)
        }
        if (temp.indexOf("询问商家后输入") == -1 && temp > 0) {
            $(".complete").addClass("input");
        } else {
            $(".complete").removeClass("input");
        }
    }



    // 支付
    function toPay() {
        var temp = $("#amount").text();
        if (temp.indexOf('|') != -1) {
            $('#trink').remove();
        }
        //是否会员卡支付
        var type=$('.selected').data('type');
        if(type=='card'){
            memberPay();
        }else {
            defaultPay()
        }
    }


    function memberPay() {
        var total = $("#amount").html(),memberId="<?php echo ($memberUser["id"]); ?>",remark = $("#remark").val();
        out_status();
        if(total==''){show('金额不能为空');}
        $.ajax({
            type: "POST",
            url: "<?php echo U('pays/member/pays');?>",
            data: {'member':memberId, 'type': xun_data[0], 'store_id': xun_data[2], 'mid': xun_data[3], 'openid': xun_data[1], 'total': total ,'remark':remark},
            beforeSend: function (XMLHttpRequest) {
                dialog.loading.open('会员卡支付处理中...');
            },
            success: function (data) {
                YDUI.dialog.loading.close();
                if (data.status == 1) {
                    window.location.href=data.url;
                } else {
                    show(data.info);
                }
            }
        });
    }

    //默认支付
    function defaultPay() {
        if (xun_data[0] == 'wx') {
            wx_put();
        } else if (xun_data[0] == 'ali') {
            ali_put();
        } else {
            show('系统未开通当前通道');
        }
    }


    //微信支付
    function wx_put() {
        var total = $("#amount").html();
        var remark = $("#remark").val();
        out_status();
        $.ajax({
            type: "POST",
            url: "/Pays/Apis/gateway",
            data: {'type': xun_data[0], 'id': xun_data[2], 'sid': xun_data[3], 'openid': xun_data[1], 'total': total ,'remark':remark},
            beforeSend: function (XMLHttpRequest) {
                dialog.loading.open('支付数据提交中...');
            },
            success: function (data) {
                YDUI.dialog.loading.close();
                if (data.status == 1) {
                    if (data.info.type == 'js') {
                        WeixinJSBridge.invoke('getBrandWCPayRequest', {
                            'appId': data.info.pay_info.appId,
                            'timeStamp': data.info.pay_info.timeStamp,
                            'nonceStr': data.info.pay_info.nonceStr,
                            'package': data.info.pay_info.package,
                            'signType': data.info.pay_info.signType,
                            'paySign': data.info.pay_info.paySign
                        }, function (res) {
                            if (res.err_msg == 'get_brand_wcpay_request:ok') {
                                if (data.info.result) {
                                    location.href = data.info.result;
                                } else {
                                    location.href = "/Api/result/out_trade_no/" + data.info.out_trade_no;
                                }
                            } else {
                                if (res.err_msg == 'get_brand_wcpay_request:cancel') {
                                    show("您取消的支付,本订单未支付成功");
                                } else {
                                    show('支付失败');
                                }
                            }
                        });
                    }  else {
                        location.href = data.info.localurl;
                    }
                } else {
                    show(data.info);
                }
            }
        });
    }

    //支付宝支付
    function ali_put() {
        var total = $("#amount").html();
        var remark = $("#remark").val();
        out_status();
        if(total==''){show('金额不能为空');}
        $.ajax({
            type: "POST",
            url: "/Pays/Apis/gateway",
            data: {'type': xun_data[0], 'id': xun_data[2], 'sid': xun_data[3], 'openid': xun_data[1], 'total': total,'remark':remark},
            beforeSend: function (XMLHttpRequest) {
                YDUI.dialog.loading.open('支付数据提交中...');
            },
            success: function (data) {
                YDUI.dialog.loading.close();
                if (data.status == 1) {
                    if (data.info.type == 'js') {
                        var options = {
                            "tradeNO": data.info.pay_info.tradeNO
                        };
                        AlipayJSBridge.call('tradePay', options, function (result) {
                            if (result.resultCode == 9000) { //支付成功
                                if (data.info.result) {
                                    location.href = data.info.result;
                                } else {
                                    location.href = "/Api/result/out_trade_no/" + data.info.out_trade_no;
                                }
                            } else {
                                show('支付失败');
                            }
                        });
                    }else {
                        location.href = data.info.localurl;
                    }
                } else {
                    show(data.info);
                }
            }
        });
    }




    function out_status() {
        $('#trink').remove();
        document.getElementById('amount').click();
    }

    function show(data) {
        dialog.toast(data, 'none', 2000);
    }


    function total_init() {
        //callKeyboard();
        document.getElementById('amount').click();
    }

</script>
</body>
</html>