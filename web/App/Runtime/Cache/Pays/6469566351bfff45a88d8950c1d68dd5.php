<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no, minimal-ui">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <title>商户入网</title>
    <!-- Path to Framework7 Library CSS-->
    <link rel="stylesheet" href="/Source/Framework7/css/framework7.ios.min.css">
    <link rel="stylesheet" href="/Source/Framework7/css/framework7.ios.colors.min.css">
    <link rel="stylesheet" href="/Source/Framework7/css/framework7-icons.css">
    <!-- Path to your custom app styles-->
    <link rel="stylesheet" href="/Source/Framework7/css/my-app.css">
</head>
<body>
<div class="views">
    <div class="view view-main">
        <div class="navbar">
            <div class="navbar-inner">
                <div class="center sliding">入网类型</div>
            </div>
        </div>
        <div class="pages navbar-through toolbar-through">
            <div  class="page">
                <div class="page-content">
                    <div class="card" style="margin: 0px;">
                        <div class="card-content">
                            <div class="list-block">
                                <ul>
                                    <li class="item-content">
                                        <div class="item-media"><img src="<?php echo ($_SESSION['Reg']['user_info']['headimgurl']); ?>" width="80" style="border-radius: 10px"></div>
                                        <div class="item-inner">
                                            <div class="item-title-row">
                                                <div class="item-title"><?php echo ($_SESSION['Reg']['user_info']['nickname']); ?></div>
                                            </div>
                                        </div>
                                    </li>

                                </ul>
                            </div>
                        </div>
                        <div class="card-footer"> <span><?php echo ($_SESSION['Reg']['user_info']['province']); ?>   <?php echo ($_SESSION['Reg']['user_info']['city']); ?></span><span>码ID:<?php echo ($_SESSION['Reg']['codes']); ?></span></div>
                    </div>
                    <div class="content-block-title">选择类型</div>
                    <!--<div class="list-block">-->
                        <!--<ul>-->
                            <!--<li><a href="#" onclick="location.href='<?php echo U('MchSole');?>'" class="item-link" target="_self">-->
                                <!--<div class="item-content">-->
                                    <!--<div class="item-inner">-->
                                        <!--<div class="item-title">快速入网</div>-->
                                    <!--</div>-->
                                <!--</div></a>-->
                            <!--</li>-->
                        <!--</ul>-->
                    <!--</div>-->


                    <div class="list-block"  style="margin-top: 0px">
                        <ul>
                            <li><a href="#" onclick="location.href='<?php echo U('MchPar');?>'" class="item-link">
                                <div class="item-content">
                                    <div class="item-inner">
                                        <div class="item-title">小微入网</div>
                                    </div>
                                </div></a>
                            </li>
                        </ul>
                    </div>
                    <div class="list-block"  style="margin-top: 0px">
                        <ul>
                            <li><a href="#" onclick="location.href='<?php echo U('MchPer');?>'" class="item-link">
                                <div class="item-content">
                                    <div class="item-inner">
                                        <div class="item-title">企业入网</div>
                                    </div>
                                </div></a>
                            </li>
                        </ul>
                    </div>




                    <div class="list-block"  style="margin-top: 0px">
                        <ul>
                            <li><a href="#" onclick="mch_status()" class="item-link">
                                <div class="item-content">
                                    <div class="item-inner">
                                        <div class="item-title">申请进度查询</div>
                                    </div>
                                </div></a>
                            </li>
                        </ul>
                    </div>

                    <div class="content-block-title">温馨提示</div>
                    <div class="list-block">
                        <ul>
                           <!-- <li class="swipeout demo-remove-callback">
                                <div class="item-content swipeout-content">
                                    <div class="item-inner">
                                        <div class="item-text" style="height: auto">个人入网:无营业执照 每个通道额度有限制</div>
                                    </div>
                                </div>
                            </li>
                            <li class="swipeout demo-remove-callback">
                                <div class="item-content swipeout-content">
                                    <div class="item-inner">
                                        <div class="item-text" style="height: auto">商户入网:有营业执照 结算卡必须为法人,额度高</div>
                                    </div>
                                </div>
                            </li>-->
                            <li>
                                <div class="item-content">
                                    <div class="item-inner">
                                        <div class="item-text" style="height: auto">
                                            <!--<p style="margin: 0px">快速入网: 单笔1千元 单日1万元</p>-->
                                            <!--<p style="margin: 0px">小微入网: 单笔2万元 单日20万元</p>-->
                                            <!--<p style="margin: 0px">企业入网: 单笔2万元 单日对私20万 对公30万元</p>-->
                                            <p style="margin: 0px">小微入网: 无营业执照入网</p>
                                            <p style="margin: 0px">企业入网: 有营业执照入网</p>
                                            <p style="margin: 0px">为保证商户开通申请可以正常通过审核,请在开通过程中提供真实有效的商户资料!</p>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>


                    <div class="content-block xun_footer">
                        Copyright © <?php echo ($_domain['web_name']); ?> 2015-<?php echo date('Y');?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script type="text/javascript" src="/Source/Framework7/js/framework7.min.js"></script>
<script type="text/javascript" src="http://apps.bdimg.com/libs/jquery/2.1.1/jquery.min.js"></script>
<script>
    var myApp = new Framework7( {
        modalTitle: '提示',
        modalButtonOk: '确定',
        modalButtonCancel: '取消',
        modalPreloaderTitle: '加载中...请稍等...',
        animateNavBackIcon: true,
        materialRipple: true
    });
    function mch_status() {
        myApp.prompt('请输入商户注册的手机号', function (value) {
            var telReg = !!value.match(/^1[3|4|5|7|8][0-9]{9}$/);
            if (telReg == false) {
                myApp.alert('请输入正确的手机号');
                return;
            }

            $.ajax({
                data: {tel:value},
                url: "/Pays/MchReg/mch_reg_status/Debug/1",
                type: "POST",
                dataType: "json",
                success: function (data) {
                    if(data.status==1){
                     window.location.href=data.info;
                    }else{
                        myApp.alert(data.info);
                        return;
                    }
                }
            });
        });
    }
</script>
</body>
</html>