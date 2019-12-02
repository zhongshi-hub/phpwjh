




$$(document).on('ajaxStart', function (e) {
    myApp.showIndicator();
});
$$(document).on('ajaxComplete', function (e) {
    myApp.hideIndicator();
});

$$('#bank_card').on('click', function () {
    mainView.router.loadContent($('#myPage').html());
});

function card_back() {
    mainView.router.back();
}


$(function () {
    /*初始化*/
    $('#JS,#JS_Data,#Per,#Bus,#Bus_title').hide();
    $$('#picker-bus-type').change(BUS_TYPE);
    $$('#bank_type').change(BANK_TYPE);
    BUS_TYPE();
    BANK_TYPE();
    card_img();


    $.ajax({
        url: "{wbs::U('city_val')}",
        type: "POST",
        dataType: "json",
        data: 'name=新疆',
        success: function (data) {
            defult_val = data;
        }
    });
    $('#bank_val').on('keyup mouseout input', function () {
        var $this = $(this);
        var v = $this.val();
        /\S{5}/.test(v) && $this.val(v.replace(/\s/g, '').replace(/(.{4})/g, "$1 "));
    });

});

$$('form.ajax-submit').on('submitted', function (e) {
    var data = JSON.parse(e.detail.data);
    if (data.status == 1) { //成功

    } else { //失败
        myApp.alert(data.info);
    }
});

/*身份验证背景*/
function card_img() {
    //$("#img-z").attr("src","../styles/center/img/z.png");
    //$("#img-p").attr('src',img_val[1]);
    //$("#img-s").attr('src',img_val[2]);

    // alert(z);

}


/*身份信息验证*/
function BUS_TYPE() {
    BANK_TYPE();
    var BUS_TYPES = $('#picker-bus-type').val();
    //alert(BUS_TYPES);
    if (BUS_TYPES == '有营业执照') {
        $('#JS,#JS_Data,#Bus').show();
        $('#Per').hide();
    } else {
        $('#Bus').hide();
        $('#JS,#JS_Data,#Per').show();
        $('#Bus_Up').show();
        $('#Bus_title').hide();
        $("#bank_val").attr("placeholder", "请点击拍照自动识别银行卡号...");
        $$('#bank_val').attr("readonly", "readonly");
    }

}

/*账户类型*/
function BANK_TYPE() {
    var BANK_TYPES = $('#bank_type option:selected').val();
    var BUS_TYPES = $('#picker-bus-type').val();
    //只有营业执照才可以判断 无营业执照无需判断
    if (BUS_TYPES == '有营业执照') {
        if (BANK_TYPES == '企业账户') {
            $('#Bus_title').show();
            $('#Bus_Up').hide();
            $("#bank_val").attr("placeholder", "请输入银行卡号...");
            $$('#bank_val').removeAttr("readonly");
        } else {
            $('#Bus_Up').show();
            $('#Bus_title').hide();
            $("#bank_val").attr("placeholder", "请点击拍照自动识别银行卡号...");
            $$('#bank_val').attr("readonly", "readonly");
        }
    }

}

myApp.picker({
    input: '#picker-banks-type',
    rotateEffect: true,
    toolbarCloseText: '关闭',
    cols: [
        {
            textAlign: 'center',
            values: ['个人账户', '企业账户']
        }
    ]
});
myApp.picker({
    input: '#picker-bus-type',
    rotateEffect: true,
    toolbarCloseText: '关闭',
    cols: [
        {
            textAlign: 'center',
            values: ['无营业执照', '有营业执照']
        }
    ]
});

myApp.picker({
    input: '#picker-bank-city',
    toolbarCloseText: '关闭',
    rotateEffect: true,
    cols: [
        {
            textAlign: 'left',
            values: ['请选择','北京','天津','河北','山西','内蒙古','辽宁','吉林','黑龙江','上海','江苏','浙江','安徽','福建','江西','山东','河南','湖北','湖南','广东','广西','海南','重庆','四川','贵州','云南','西藏','陕西','甘肃','青海','宁夏','新疆','香港'],
            width: 160,
            onChange: function (picker, country) {
                if (picker.cols[1].replaceValues) {
                    $.ajax({
                        url: "{wbs::U('city_val')}",
                        type: "POST",
                        dataType: "json",
                        data: 'name=' + country,
                        success: function (data) {
                            if (picker.cols[1].replaceValues) {
                                picker.cols[1].replaceValues(data);
                            }
                        }
                    });

                }
            }
        },
        {
            textAlign: 'center',
            values: ["请选择"], //默认
            width: 160
        }

    ]
});


myApp.picker({
    input: '#picker-city',
    toolbarCloseText: '关闭',
    rotateEffect: true,
    cols: [
        {
            textAlign: 'left',
            values: ['请选择','北京','天津','河北','山西','内蒙古','辽宁','吉林','黑龙江','上海','江苏','浙江','安徽','福建','江西','山东','河南','湖北','湖南','广东','广西','海南','重庆','四川','贵州','云南','西藏','陕西','甘肃','青海','宁夏','新疆','香港'],
            width: 160,
            onChange: function (picker, country) {
                if (picker.cols[1].replaceValues) {
                    $.ajax({
                        url: "{wbs::U('city_val')}",
                        type: "POST",
                        dataType: "json",
                        data: 'name=' + country,
                        success: function (data) {
                            if (picker.cols[1].replaceValues) {
                                picker.cols[1].replaceValues(data);
                            }
                        }
                    });

                }
            }
        },
        {
            textAlign: 'center',
            values: ["请选择"], //默认
            width: 160
        }

    ]
});
$$('.ydf-help').on('click', function () {
    myApp.addNotification({
        title: '温馨提示',
        message: '请确保门店和结算信息的正确性,手机号均填写门店负责人手机号!如有疑问!请联系平台工作人员!'
    });
});


/*解析一点付下所有的收款码ID*/
function qrcode() {
    var strs = new Array();
    wx.scanQRCode({
        needResult: 1,
        scanType: "qrCode",
        success: function (res) {
            var result = res.resultStr;
            strs = result.split("/");
            $codes = strs[strs.length - 1];
            //识别后 将收款码发送给服务端效验
            $.ajax({
                url: "{wbs::U('codes_status')}",
                type: "POST",
                dataType: "json",
                data: 'codes=' + $codes,
                success: function (data) {
                    if (data.status == 1) {
                        $('[name="codes"]').val($codes);
                    } else {
                        $('[name="codes"]').val('');
                        myApp.alert(data.info);
                    }
                }
            });
        }
    });
}

//获取验证码
var countdown = 30;
function toGetValiNum() {
    $("#validBtn").attr("disabled", "disabled");
    var telNo = $('[name="telNo"]').val();
    var Codes = $('[name="codes"]').val();
    if (telNo == "") {
        myApp.alert('请输入手机号');
        $("#validBtn").removeAttr("disabled");
        return;
    }
    if (Codes == "") {
        myApp.alert('请输入或扫描收款码ID');
        $("#validBtn").removeAttr("disabled");
        return;
    }
    var telReg = !!telNo.match(/^1[3|4|5|7|8][0-9]{9}$/);

    if (telReg == false) {
        myApp.alert('请输入正确的手机号');
        $("#validBtn").removeAttr("disabled");
        return;
    }
    //调用获取验证码接口
    $.ajax({
        data: {tel: telNo, cardSn: Codes},
        url: "{wbs::U('Wap/wbs_mcash/sms_check')}",
        type: "POST",
        dataType: "json",
        success: function (data) {
            if (data.retCode == '0') {
                settime();
            }
            if (data.retCode == '1') {
                myApp.alert(data.msg);
                $("#validBtn").removeAttr("disabled");
                return;
            }
        },
        error: function (data) {
            myApp.alert('获取验证码失败!');
            $("#validBtn").removeAttr("disabled");
        }
    });

}
//获取验证码60秒倒计时
function settime() {
    if (countdown == 0) {
        $("#validBtn").removeAttr("disabled");
        $("#validBtn").text("获取验证码");
        countdown = 30; //代理或业务员录入 30秒
        return;
    } else {
        $("#validBtn").attr("disabled", "disabled");
        $("#validBtn").text(countdown + "s");
        countdown--;
    }
    setTimeout(function () {
        settime()
    }, 1000)
}
function banks() {
    myApp.popup('.banks-popup');
}

/*识别银行卡号*/
function banks_set() {
    myApp.closeModal('.banks-popup');
    wx.chooseImage({
        count: 1, // 只允许1张图
        sizeType: 'compressed', // 可以指定是原图还是压缩图，默认二者都有
        sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
        success: function (res) {
            //uploadImage(res.localIds);
            //SDK 上传图片
            if (res.localIds.length == 0) {
                myApp.alert('请先使用 chooseImage 接口选择图片');
                return;
            }
            wx.uploadImage({
                localId: '' + res.localIds,
                isShowProgressTips: 1,
                success: function (res) {
                    myApp.showIndicator();
                    //SDK上传成功传至服务器上传
                    $.ajax({
                        url: '{wbs::U("upload_disc")}',
                        data: {
                            media_id: res.serverId
                        },
                        type: "POST",
                        dataType: "json",
                        success: function (res) {
                            myApp.hideIndicator();
                            if (res.status == 1) {//上传成功操作
                                var New_data = res.number;
                                value = New_data.replace(/\s/g, ' ').replace(/(\d{4})(?=\d)/g, "$1 ");
                                $('[name="bank_cid"]').val(value);
                                $('[name="bank_cid"]').removeAttr("readonly");
                                myApp.addNotification({
                                    title: '温馨提示',
                                    message: '银行卡信息识别成功!请仔细核对是否正确!'
                                });
                            } else {//上传失败操作
                                myApp.alert(res.info);
                            }
                        },
                        beforeSend: function (XMLHttpRequest) {
                            myApp.showIndicator();
                        },
                        error: function (res) {
                            myApp.alert(res.info);
                        }
                    })

                },
                beforeSend: function (XMLHttpRequest) {
                    myApp.showIndicator();
                },
                fail: function (res) {
                    myApp.alert(JSON.stringify(res));
                }
            })

        }
    });
}


function loading() {
    myApp.showIndicator();
    setTimeout(function () {
        myApp.hideIndicator();
    }, 2000);
}