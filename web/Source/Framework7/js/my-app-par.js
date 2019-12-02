//Xun Chen new_mch_reg
var myApp = new Framework7(
    {
        modalTitle: '提示',
        modalButtonOk: '确定',
        modalButtonCancel: '取消',
        modalPreloaderTitle: '加载中...请稍等...',
        animateNavBackIcon: true,
        materialRipple: true
    }
);

var $$ = Dom7;

$$(document).on('ajaxStart', function (e) {
    myApp.showIndicator();
});
$$(document).on('ajaxComplete', function (e) {
    myApp.hideIndicator();
});

$$('form.ajax-submit').on('submitted', function (e) {
    var data = JSON.parse(e.detail.data);
    if (data.status == 1) { //成功
        myApp.alert(data.info, function () {
            location.href = data.url;
        });
    } else { //失败
        myApp.alert(data.info);
    }
});

$$('#selectBnk').change(bankChange); //监听银行信息变动
$$('#picker-bank-city').change(bankChange); //监听省份信息变动


function ToDates() {
    $$('[name="card_etime"]').val('2999-12-31');
}
/*初始化已上传图片*/
function UpImage_Val() {
    var array = ['z', 'p', 's', 'm1', 'm2','bank','yyzz','auth-z','auth-p','sqh'];
    var storedData = myApp.formGetData("form-"+code_id+"-img-par");
    var url='http://file.xunmafu.com/Upload/attachment/2017-11-23/';
    if(storedData) {
        for (var k = 0, length = array.length; k < length; k++) {
            src = storedData['img-' + array[k]];
            var src_demo = ['WJEPKIUJE6NG0FKO698A.jpg','E03139IEHKB9BVHKIL5D.jpg','A33ENAI6K0936LOHRHMH.jpg','mentou.jpg','neijing.jpg','F889PLCX93NGO5FAJEMM.jpg','geti.jpg','WJEPKIUJE6NG0FKO698A.jpg','E03139IEHKB9BVHKIL5D.jpg','sqh.jpg'];
            if (src != null && src != "" && typeof(src) != 'undefined') {
                $("[name='img-" + array[k] + "']").val(src);
                $("#img-" + array[k]).css("background-image", "url(" + src + ")");
            }else{
                if (src_demo[k] != null && src_demo[k] != "" && typeof(src_demo[k]) != 'undefined') {
                    $("#img-" + array[k]).css("background-image", "url(" +url+ src_demo[k] + ")");
                }
            }
        }
    }else{
        var src =  ['WJEPKIUJE6NG0FKO698A.jpg','E03139IEHKB9BVHKIL5D.jpg','A33ENAI6K0936LOHRHMH.jpg','mentou.jpg','neijing.jpg','F889PLCX93NGO5FAJEMM.jpg','geti.jpg','WJEPKIUJE6NG0FKO698A.jpg','E03139IEHKB9BVHKIL5D.jpg','sqh.jpg'];
        for (var k = 0, length = array.length; k < length; k++) {
            if (src[k] != null && src[k] != "" && typeof(src[k]) != 'undefined') {
                $("#img-" + array[k]).css("background-image", "url(" +url+ src[k] + ")");
            }
        }
    }

}


function xy_status() {
    var xy=document.getElementById('box');
    if(xy.checked === true){
        $('#xun_sub_button').attr('disabled',false);
    }else{
        $('#xun_sub_button').attr('disabled',true);
    }
}

// Add view
var mainView = myApp.addView('.view-main', {
    dynamicNavbar: true,
    domCache:false
});




var  pro=["请选择","北京","天津","上海","河北省","河南省","山西省","辽宁省","吉林省","黑龙江省","江苏省","浙江省","安徽省","福建省","江西省","山东省","湖北省","湖南省","广东省","海南省","重庆","四川省","贵州省","云南省","西藏自治区","陕西省","甘肃省","青海省","广西壮族自治区","内蒙古自治区","宁夏回族自治区","新疆维吾尔自治区"];
myApp.picker({
    input: '#picker-city',
    toolbarCloseText: '关闭',
    rotateEffect: false,
    momentumRatio:2,
    cols: [
        {
            textAlign: 'left',
            values: pro,
            width: 160,
            onChange: function (picker, country) {

                if (picker.cols[1].replaceValues) {
                    $.ajax({
                        url: "/Pays/Reg/area_city",
                        type: "POST",
                        dataType: "json",
                        data: 'name=' + country,
                        success: function (data) {
                            picker.cols[1].replaceValues(data);
                            picker.cols[2].replaceValues(["请选择"]);
                        }
                    });

                }
            }
        },
        {
            textAlign: 'center',
            values: ["请选择"], //默认
            width: 160,
            onChange: function (picker, country) {
                if (picker.cols[2].replaceValues) {
                    $.ajax({
                        url: "/Pays/Reg/area_disc",
                        type: "POST",
                        dataType: "json",
                        data: 'name=' + country,
                        success: function (data) {
                            picker.cols[2].replaceValues(data);
                        }
                    });

                }
            }
        },
        {
            textAlign: 'right',
            values: ["请选择"], //默认
            width: 160
        }

    ]
});

function bankChange() {
    var cityId = $('#selectBnk option:selected').val();
    var Bank_city = $('#picker-bank-city').val();
    var _city = Bank_city.split(' ');
    var city = _city[1];
    if (city != null && city != "" && typeof(city) != 'undefined' && city != '请选择' && cityId != null && cityId != "" && typeof(cityId) != 'undefined') {
        setLBnk(cityId, city);
    }
}

//当开户市改变且开户行已选择时， 选择开户支行
function setLBnk(cityId, city) {
    var params = {city: city, cityId: cityId};
    var linkBnkStr = "<option value=''>请选择支行</option>";
    $.ajax({
        data: params,
        url: "/Pays/Reg/bnkLink",
        type: "POST",
        dataType: "json",
        success: function (data) {
            if (data.list != '' && data.list != null) {
                for (var i = 0; i < data.list.length; i++) {
                    linkBnkStr += "<option value='" + data.list[i].banking + "'>" + data.list[i].address + "</option>";
                }
            } else {

                linkBnkStr = "<option value=''>未找到分行信息</option>";
            }
            $("#selectLBnk").find("option").remove();
            myApp.smartSelectAddOption('#depositLBnk select', linkBnkStr);

        }
    });
}

myApp.picker({
    input: '#picker-bank-city',
    toolbarCloseText: '关闭',
    rotateEffect: false,
    momentumRatio:2,
    cols: [
        {
            textAlign: 'left',
            values: pro,
            width: 160,
            onChange: function (picker, country) {

                if (picker.cols[1].replaceValues) {
                    $.ajax({
                        url: "/Pays/Reg/area_city",
                        type: "POST",
                        dataType: "json",
                        data: 'name=' + country,
                        success: function (data) {
                            picker.cols[1].replaceValues(data);
                        }
                    });

                }
            }
        },
        {
            textAlign: 'right',
            values: ["请选择"], //默认
            width: 160
        }

    ]
});

//获取验证码
var countdown = 60;
function toGetValiNum() {
    $("#validBtn").attr("disabled", "disabled");
    var telNo = $('[name="telNo"]').val();
    var Codes = code_id;
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
    var telReg = !!telNo.match(/^1[3|4|5|6|7|8|9][0-9]{9}$/);

    if (telReg == false) {
        myApp.alert('请输入正确的手机号');
        $("#validBtn").removeAttr("disabled");
        return;
    }
    //调用获取验证码接口
    $.ajax({
        data: {tel: telNo, cardSn: Codes},
        url: "/Pays/Reg/sms_check",
        type: "POST",
        dataType: "json",
        success: function (data) {
            if (data.status == 1) {
                settime();
            } else {
                myApp.alert(data.info);
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
        countdown = 60;
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


function IMG_UPLOAD(ids, mod) {
    wx.chooseImage({
        count: 1,
        sizeType: ['compressed'],
        sourceType: ['album', 'camera'],
        success: function (res) {
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
                    $.ajax({
                        url: '/Pays/Reg/upload_disc',
                        data: {
                            media_id: res.serverId,
                            type: 'Images'
                        },
                        type: "POST",
                        dataType: "json",
                        success: function (res) {
                            myApp.hideIndicator();
                            if (res.status == 1) {
                                $("#img-" + ids).css("background-image","url("+res.url+")");
                                $("[name='img-" + ids + "']").val(res.url);
                                myApp.alert('上传成功', '提示', function () {
                                    setForm();
                                });
                            } else {
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


$$('.xun-menu').on('click', function () {
    var xun_my = [
        {
            text: '清空缓存',
            onClick: function () {
                myApp.confirm('缓存清空后需要重新填写!您确定要清空本页缓存数据吗?', function () {
                    myApp.formDeleteData('form-'+code_id+'-par');
                    myApp.formDeleteData('form-'+code_id+'-img-par');
                    window.location.reload();
                });
            }
        }

    ];
    var xun_colse = [
        {
            text: '关闭',
            color: 'red'
        }
    ];

    var groups = [xun_my, xun_colse];
    myApp.actions(groups);
});

function setForm() {
    var yyzz = $("[name='img-yyzz']").val();
    var z = $("[name='img-z']").val();
    var p = $("[name='img-p']").val();
    var s = $("[name='img-s']").val();
    var bank = $("[name='img-bank']").val();
    var m1 = $("[name='img-m1']").val();
    var m2 = $("[name='img-m2']").val();

    var authz = $("[name='img-auth-z']").val();
    var authp = $("[name='img-auth-p']").val();
    var sqh = $("[name='img-sqh']").val();



    var formData = {
        'img-yyzz': yyzz,
        'img-z': z,
        'img-p': p,
        'img-s': s,
        'img-bank': bank,
        'img-m1': m1,
        'img-m2': m2,
        'img-auth-z':authz,
        'img-auth-p':authp,
        'img-sqh':sqh
    };
    myApp.formStoreData("form-"+code_id+"-img-par", formData);
}

