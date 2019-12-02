/*2018-04-13 NEW*/
var setIn_data=0;
$("#amount").text("询问商家后输入").on("click", callKeyboard);
$("#keyboard td").on("touchstart", keyboardInput);
$(".method").on('click', choicePayMethod);
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
    } else if ($(this).hasClass("complete")) {
        toPay();
        return;
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
        $("#pay").val(method);
    }
}

// 支付
function toPay() {
    var temp = $("#amount").text();
    if (temp.indexOf('|') != -1) {
        $('#trink').remove();
    }
    defaultPay();
}

//默认支付
function defaultPay() {
    var pay=$('#pay').val();
    if (xun_data[0] == 1) {
        scan_pay(pay);
    } else if (xun_data[0] == 2) {
        pay_put(pay);
    } else {
        show('未知付款方式');
    }
}
function pay_put(type) {
    var total = $("#amount").html();
    out_status();
    $.ajax({
        type: "POST",
        url: "/Pays/FastApi/gateway?Debug=1",
        data: {'type': type, 'id': xun_data[2], 'sid': xun_data[3],'total': total,'pay_api':'code'},
        beforeSend: function (XMLHttpRequest) {
            dialog.loading.open('支付数据提交中...');
        },
        success: function (data) {
            YDUI.dialog.loading.close();
            settime();
            if (data.status == 1&&data.info.qrcode) {
                wbs_qrs(data.info.qrcode);
                setIn_data= setInterval("getOrderStatus('"+data.info.out_trade_no+"','"+data.info.api+"')",5000);
            } else { //支付失败
                show(data.info);
            }
        }
    });
}

function scan_pay(type) {
    var total = $("#amount").html();
    out_status();
    wx.scanQRCode({
        desc: 'scanQRCode desc',
        needResult: 1, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
        scanType: ["qrCode"], // 可以指定扫二维码还是一维码，默认二者都有
        success: function (res) {
            var code_data=res.resultStr;
            $.ajax({
                type: "POST",
                url: "/Pays/FastApi/gateway?Debug=1",
                data: {'type': type, 'id': xun_data[2], 'sid': xun_data[3],'total': total,'pay_api':'scan','code_data':code_data},
                beforeSend: function (XMLHttpRequest) {
                    dialog.loading.open('支付数据提交中...');
                },
                success: function (data) {
                    YDUI.dialog.loading.close();
                    if (data.status == 1) {
                        if (data.info.type == 'success') {//支付成功
                            $('#order_status').html('支付成功');
                            clearInterval(setIn_data);
                            layer.open({
                                content:data.info.msg
                                ,btn: '我知道了'
                                ,shadeClose: false
                                ,yes:function () {
                                    window.location.reload();
                                }
                            });
                        }else{//支付中 轮询查询结果
                            $('#order_status').html('等待用户支付中');
                            setIn_data=setInterval("getOrderStatus('"+data.info.out_trade_no+"','"+data.info.api+"')",5000);
                            dialog.loading.open(data.info.msg);
                        }
                    } else { //支付失败
                        show(data.info);
                    }
                }
            });

        },
        error: function(res){
            if(res.errMsg.indexOf('function_not_exist') > 0){
                alert('版本过低请升级')
            }
        }
    });
}

//请求支付状态
function getOrderStatus(oid,api){
    if(oid == ''){
        window.location.reload();
    }
    $.ajax({
        type: "POST",
        url: "/Pays/FastApi/getOrderStatus",
        data: {'api':api,'oid':oid,'test':window.test_data},
        success: function(data){
            if(data.status == 1){//只有成功的才提示 其他不提示
                $('#order_status').html('支付成功');
                clearInterval(setIn_data);
                layer.open({
                    content:data.info.msg
                    ,btn: '我知道了'
                    ,shadeClose: false
                    ,yes:function () {
                        window.location.reload();
                    }
                });
            }else{
                $('#order_status').html('等待用户支付中');
            }
        }
    });
}

//获取结果计时
var countdown = 0;
function settime() {
    if (countdown >= 300) {
        $("#order_status").text("订单已超过10分钟,请确认客户是否支付");
    }
    $("#order_time").html(countdown + "s");
    countdown++;
    setTimeout(function () {
        settime()
    }, 1000)
}


function wbs_qrs(urls) {
    layer.open({
        type: 1,
        title: '支付二维码',
        closeBtn: 0,
        shadeClose: false,
        content: '<div id="pay_qr" class="wbscms_payimg">二维码区域</div><div class="pay_explain"><p>请将支付二维码出示给客户扫描</p><p style="text-align: center;color: red;">支付结果<span id="order_time"></span></p><p style="text-align: center;color: red;" id="order_status">等待支付中...</p><p>用户支付成功后此页面自动关闭</p></div></div>'
    });
    var url = urls;
    var qr = qrcode(10, 'M');
    qr.addData(url);
    qr.make();
    $('#pay_qr').html(qr.createImgTag());
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
    $('#trink').remove();
    document.getElementById('amount').click();
}

function show(data) {
    dialog.toast(data, 'none', 2000);
}

function total_init() {
    //callKeyboard();
    document.getElementById('amount').click();   //获取元素div
}