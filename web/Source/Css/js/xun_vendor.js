$(function () {
    $(".key[data-code]").bind("touchstart", pressKey);
});
$(document).on('touchmove', function (e) {
    e.preventDefault();
},false);
function pressKey() {
    event.preventDefault();
    var v = $(this).attr("data-code");
    var amount = show_total(v);
    var currentKey = $(this);
    currentKey.addClass("down");
    setTimeout(function () {
        currentKey.removeClass("down");
    }, 100);
    total(amount);
}
function show_total(mount) {
    var total=$('#key-span');
    var t_data=total.html();
    if(mount=='backspace'){
        if(t_data==null||t_data==''){
            return;
        }else{
            new_data =t_data.substring(0,t_data.length-1);
        }
    }else if(mount=='xc'){
        new_data='';
    }else{
        if((t_data==null||t_data=='')&&mount=='.'){
            new_data = "0"+mount;
        }else if(t_data==null||t_data==''){
            new_data = mount;
        }else if(mount=='.'&&t_data.indexOf(".")>0){
            new_data = t_data;
        }else if(mount=='0'&&t_data.indexOf(".")<0&&t_data.indexOf("0")==0){
            new_data = t_data;
        }else if(mount=='.'&&t_data.indexOf(".")<0 ){
            new_data = t_data+mount;
        }else if(t_data.indexOf(".")>0&&(t_data.length-t_data.indexOf("."))>2){
            new_data = t_data;
        }else if(t_data.length>8){
            new_data = t_data;
        }else if(t_data.indexOf(".")<0&&islessZero(t_data)&&mount!='.'){
            new_data = mount;
        }else if(t_data.indexOf(".")<0&&t_data.length>4){
            new_data=t_data;
        }else{
            new_data =t_data+mount;
        }
    }
    if(islessZero(new_data)){
        $('.all-pay').addClass('od').removeAttr('onclick');
    }else{
        $('.all-pay').attr('onclick','xun_put()').removeClass('od');
    }
    return new_data;
}

function show(data) {
    YDUI.dialog.toast(data, 'none', 2000);
}
function islessZero(tar){
    if(tar=='undefind'||tar==null||tar==''){
        return true;
    }
    return parseFloat(tar)<=0;
}
function xun_put() {

    if(xun_data[0]=='wx'){
        wx_put();
    }else if(xun_data[0]=='ali'){
        ali_put();
    }else{
        show('系统未开通当前通道');
    }
}

function out_status() {
    $('.all-pay').addClass('od').removeAttr('onclick');
    total(null);
}

function total(total) {
    $('#key-span').html(total);
    $('#key-input').val(total);
}


//微信支付
function  wx_put() {
    var total=$('#key-input').val();
    out_status();
    $.ajax({
        type: "POST",
        url: "/Pays/Apis/gateway",
        data: {'type':xun_data[0],'id':xun_data[2],'sid':xun_data[3],'openid':xun_data[1],'total':total},
        beforeSend:function(XMLHttpRequest){
            YDUI.dialog.loading.open('支付数据提交中...');
        },
        success: function(data){
            YDUI.dialog.loading.close();
            if(data.status==1){
                if(data.info.type=='js'){
                    WeixinJSBridge.invoke('getBrandWCPayRequest', {
                        'appId': data.info.pay_info.appId,
                        'timeStamp': data.info.pay_info.timeStamp,
                        'nonceStr': data.info.pay_info.nonceStr,
                        'package': data.info.pay_info.package,
                        'signType':data.info.pay_info.signType,
                        'paySign': data.info.pay_info.paySign
                    }, function (res) {
                        if (res.err_msg == 'get_brand_wcpay_request:ok') {
                            if(data.info.result){
                                location.href = data.info.result;
                            }else {
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
                }else if(data.info.type=='form'){
                    Xun_submitForm(data.info.url,data.info.data);
                }else{
                    location.href =data.info.localurl;
                }
            }else{
                show(data.info);
            }
        }
    });
}

//支付宝支付
function  ali_put() {
    var total=$('#key-input').val();
    out_status();
    $.ajax({
        type: "POST",
        url: "/Pays/Apis/gateway",
        data: {'type':xun_data[0],'id':xun_data[2],'sid':xun_data[3],'openid':xun_data[1],'total':total},
        beforeSend:function(XMLHttpRequest){
            YDUI.dialog.loading.open('支付数据提交中...');
        },
        success: function(data){
            YDUI.dialog.loading.close();
            if(data.status==1){
                if(data.info.type=='js'){
                    var options = {
                        "tradeNO" : data.info.pay_info.tradeNO
                    };
                    AlipayJSBridge.call('tradePay', options ,function(result){
                        if(result.resultCode==9000){ //支付成功
                            if(data.info.result){
                                location.href = data.info.result;
                            }else {
                                location.href = "/Api/result/out_trade_no/" + data.info.out_trade_no;
                            }
                        }else{
                            show('支付失败');
                        }
                    });
                }else if(data.info.type=='form'){
                    Xun_submitForm(data.info.url,data.info.data);
                }else{
                    location.href =data.info.localurl;
                }
            }else{
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
    for(var i=0 ; i < data.length;i ++){
        var input1 = $("<input type='hidden' name='"+data[i].name+"' />");
        input1.attr('value', data[i].val);
        form.append(input1);

    }
    form.appendTo("body");
    form.css('display', 'none');
    form.submit();
}



