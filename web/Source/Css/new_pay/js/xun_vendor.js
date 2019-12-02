/*2017-10-20 NEW*/
$("#amount").text("询问商家后输入").on("click", callKeyboard);
$("#keyboard td").on("touchstart", keyboardInput);
$(".method").on('click', choicePayMethod);
$("#pay").on("click", toPay);
// 唤起键盘
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
        //按退格删除键
        if (temp2 != "") {
            var new_num = temp2.slice(0, temp2.length - 1);
            $("#amount").html(new_num + hstr);
        }
    } else if ($(this).hasClass("complete") || $(this).hasClass("key")) {
        //按下完成或者键盘图标键
        $("#keyboard").slideUp();
        $('#trink').remove();
        if (temp2 == "" || temp2 == 0) {
            $("#amount").removeClass("rmb_icon").text("请询问商家后输入");
        }
    } else if ($(this).hasClass("dot")) {
        //按下小数点
        if (temp2 == "") {
            $("#amount").html("0" + addnum + hstr);
        } else if (temp2.indexOf(".") != "-1") {
            //已有小数点
        } else {
            $("#amount").html(temp2 + addnum + hstr);
        }
    } else {
        //数字键
        if (temp2 > 100000) {
            alert("支付超限");
            return;
        }
        if (temp2.indexOf(".") != "-1") {
            //如果有2位小数
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
        $(".pay_btn").addClass("on_click");
    } else {
        $(".complete").removeClass("input");
        $(".pay_btn").removeClass("on_click");
    }
}

// 选择支付方式
function choicePayMethod() {
    var flag = $(this).children(".select_box").hasClass('selected');
    var method = $(this).attr('method');
    if (!flag) {
        if (method == 'realName') {
            return;
        }
        $(this).children(".select_box").addClass('selected').parent().siblings('.method').children('.select_box').removeClass('selected');
        $("#pay").attr('method', method);
    }
}

// 支付
function toPay() {
    var temp = $("#amount").text();
    if ($(this).hasClass("on_click")) {
        if (temp.indexOf('|') != -1) {
            $('#trink').remove();
        }
        var method = $(this).attr("method");
        PayMethod(method);
    }
}

// 判断支付方式
function PayMethod(method) {
    switch (method) {
        case "default":
            defaultPay();
            break;
        case "quick":
            quickPay();
            break;
    }
}

//银联快捷支付
function quickPay() {
    var total = $("#amount").html();
    out_status();
    $.ajax({
        type: "POST",
        url: "/Pays/Qrcode/quick_gateway",
        data: {'type': xun_data[0], 'id': xun_data[2], 'sid': xun_data[3], 'openid': xun_data[1], 'total': total},
        beforeSend: function (XMLHttpRequest) {
            YDUI.dialog.loading.open('数据提交中...');
        },
        success: function (data) {
            YDUI.dialog.loading.close();
            if (data.status == 1) {
                location.href = data.url;
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
    out_status();
    $.ajax({
        type: "POST",
        url: "/Pays/Apis/gateway",
        data: {'type': xun_data[0], 'id': xun_data[2], 'sid': xun_data[3], 'openid': xun_data[1], 'total': total},
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
                } else if (data.info.type == 'form') {
                    Xun_submitForm(data.info.url, data.info.data);
                } else {
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
    out_status();
    $.ajax({
        type: "POST",
        url: "/Pays/Apis/gateway",
        data: {'type': xun_data[0], 'id': xun_data[2], 'sid': xun_data[3], 'openid': xun_data[1], 'total': total},
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
                } else if (data.info.type == 'form') {
                    Xun_submitForm(data.info.url, data.info.data);
                } else {
                    location.href = data.info.localurl;
                }
            } else {
                show(data.info);
            }
        }
    });
}


//创建form
function Xun_submitForm(action, params) {
    var data = JSON.parse(params);
    var form = $("<form></form>");
    form.attr('action', action);
    form.attr('method', 'post');
    //form.attr('target', '_self');
    for (var i = 0; i < data.length; i++) {
        var input1 = $("<input type='hidden' name='" + data[i].name + "' />");
        input1.attr('value', data[i].val);
        form.append(input1);
    }
    form.appendTo("body");
    form.css('display', 'none');
    form.submit();
}

function out_status() {
    $("#keyboard").slideUp();
    $('#trink').remove();
    $("#amount").removeClass("rmb_icon").text("请询问商家后输入");
    $(".pay_btn").removeClass("on_click");
}

function show(data) {
    dialog.toast(data, 'none', 2000);
}