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
    YDUI.dialog.loading.open('数据请求中...');
    var total=$('#key-input').val()*100;
    var url='/Mch/Index/alley_list/total/'+total;
    out_status();
    location.href=url;
}

function out_status() {
    $('.all-pay').addClass('od').removeAttr('onclick');
    total(null);
}

function total(total) {
    $('#key-span').html(total);
    $('#key-input').val(total);
}




