<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>商户中心</title>
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport"/>
    <meta content="yes" name="apple-mobile-web-app-capable"/>
    <meta content="black" name="apple-mobile-web-app-status-bar-style"/>
    <meta content="telephone=no" name="format-detection"/>
    <link rel="stylesheet" href="/Source/mui/css/mui.min.css">
    <link rel="stylesheet" href="/Source/ydui/css/ydui.css"/>
    <link rel="stylesheet" href="/Source/statics/plugins/font-awesome/css/font-awesome.min.css"/>
    <link rel="stylesheet" href="/Source/mui/css/mch.css"/>
    <script src="/Source/ydui/js/ydui.flexible.js"></script>
    <link rel="stylesheet" href="/Source/iconfont/iconfont.css"/>
    <style>
        .demo-rollnotice {
            -moz-box-align: center;
            align-items: center;
            background-color: #fff;
            display: flex;
            padding: 0 8px;
            margin-top: 5px;
        }
        .demo-rollnotice img {
            height: 16px;
            margin-right: 4px;
            width: 43px;
        }
        .m-rollnotice {
            background-color: #fff;
            overflow: hidden;
            width: 100%;
        }
        .rollnotice-box {
            height: inherit;
        }
        .rollnotice-box.align-left {
            -moz-box-pack: start;
            justify-content: flex-start;
        }
        .rollnotice-box.align-right {
            -moz-box-pack: end;
            justify-content: flex-end;
        }
        .rollnotice-box.align-center {
            -moz-box-pack: center;
            justify-content: center;
        }
        .rollnotice-item {
            -moz-box-align: center;
            -moz-box-pack: inherit;
            align-items: center;
            display: flex;
            height: inherit;
            justify-content: inherit;
        }
        .m-grids-3 .grids-item:not(:nth-child(3n)):before{border-right: 0px solid #D9D9D9;}

        *{line-height: inherit;}
        .wbscms_payimg{top:20px;width:200px;padding:10px;margin:0 auto;border-radius:40px;height:200px; line-height: 235px;position: relative;  text-align: center; }
        .wbscms_payimg img{display:block;text-align:center;width:200px;height:200px; }
        .layui-m-layerchild{padding: 10px;border-radius:10px!important;}
        .layui-m-layerchild h3{display: none}
        .layui-m-layer{z-index: 999!important;}
        .pay_explain{padding-top: 30px;text-align:center;font-size:14px;color:#545454;}
        .spread_scan_title{
            background: linear-gradient(180deg,#ff9a56,#ff3735);
            background-clip: border-box;
            -webkit-background-clip: text;
            color: transparent;
            font-size: 8vw;
            padding: 5vw 0;
            content: "viewport-units-buggyfill; font-size: 8vw; padding: 1.333vw 0";
            text-align: center;
        }
        .spread_scan_tips{
            font-size: 3.733vw;
            color: #666;
            line-height: 6vw;
            content: "viewport-units-buggyfill; font-size: 3.733vw; line-height: 6vw";
            text-align: center;
        }
        .color_2b {
            color: #2b2b2b;
        }
    </style>
</head>
<body>
<section class="g-flexview">
    <header>
        <div class="header-wrapper" style="background: #4282e3;height: 180px;">
            <div class="header-inner">
                <div class="dashboard" style="border: 0px solid #fff;background-color:transparent;top: 60%;font-size: 14px">
                    <div class="tip">今日交易(元)</div>
                    <div class="number" style="margin-top:12px;margin-bottom: 12px;font-size: 40px">
                        <span><?php echo Rand_total($D_sum); ?></span>
                    </div>
                    <div class="tip" style="font-size: 15px">今日笔数 <b><?php echo ($D_count); ?></b> 笔</div>
                </div>
            </div>
        </div>
    </header>

    <div class="g-scrollview" style="margin-top: 0px">
        <div class="m-grids-3" style="background-color:#5593F1;color: #ffffff">
            <section class="grids-item">
                <div class="grids-txt" style="color: #ffffff">微信</div>
                <div class="grids-icon" style="font-size: 16px">
                    <?php echo Rand_total($J_wx); ?>
                </div>
            </section>
            <section class="grids-item">
                <div class="grids-txt" style="color: #ffffff">支付宝</div>
                <div class="grids-icon" style="font-size: 16px">
                    <?php echo Rand_total($J_ali); ?>
                </div>
            </section>
            <section class="grids-item">
                <div class="grids-txt" style="color: #ffffff">银联快捷</div>
                <div class="grids-icon" style="font-size: 16px">
                    <?php echo Rand_total($J_card); ?>
                </div>
            </section>
        </div>
        <!-- <div class="demo-rollnotice">
             <img src="/Source/statics/system-new.jpg">
             <div class="m-rollnotice" style="height: 30px;">
                 <div class="rollnotice-box align-left">
                     <div class="rollnotice-item"><nobr><span style="color: rgb(255, 0, 0);margin-left:3px;margin-right: 10px; "><i class="icon iconfont">&#xe60a;</i></span>银行系统维护 建设银行用户悉知....
                     </nobr>
                     </div>
                 </div>
             </div>
         </div>-->

        <div class="m-celltitle" style="margin-top: 15px;">快捷导航</div>
        <div class="m-grids-3">
            <!--<?php if(($extensionStatus) == "1"): ?>-->
                <!--<a href="javascript:;" class="extension grids-item">-->
                    <!--<div class="grids-icon" style="height: 1rem">-->
                        <!--<img src="/Source/Image/mch/extension.png">-->
                    <!--</div>-->
                    <!--<div class="grids-txt">注册推广</div>-->
                <!--</a>-->
            <!--<?php endif; ?>-->
            <!--<a href="<?php echo U('Repay/index');?>" class="grids-item">-->
                <!--<div class="grids-icon" style="height: 1rem">-->
                    <!--&lt;!&ndash;<i class="icon iconfont" style="font-size: 40px">&#xe628;</i>&ndash;&gt;-->
                    <!--<img src="/Source/Image/mch/zndh.png">-->
                <!--</div>-->
                <!--<div class="grids-txt">智能还款</div>-->
            <!--</a>-->
            <!--<a href="<?php echo U('quick_url');?>" class="grids-item">-->
                <!--<div class="grids-icon" style="height: 1rem">-->
                    <!--&lt;!&ndash;<i class="icon iconfont" style="font-size: 40px">&#xe628;</i>&ndash;&gt;-->
                    <!--<img src="/Source/Image/mch/yl.png">-->
                <!--</div>-->
                <!--<div class="grids-txt">快捷收款</div>-->
            <!--</a>-->

            <a href="<?php echo U('store_data');?>" class="grids-item">
                <div class="grids-icon" style="height: 1rem">
                    <img src="/Source/Image/mch/store.png">
                </div>
                <div class="grids-txt">门店收款码</div>
            </a>
            <?php if(!empty($isStarPos)): ?><a href="<?php echo U('draw/index');?>" class="grids-item">
                <div class="grids-icon" style="height: 1rem">
                    <img src="/Source/Image/mch/tx.png">
                </div>
                <div class="grids-txt">在线提现</div>
            </a><?php endif; ?>
            <!--<a href="<?php echo U('alley_data');?>" class="grids-item">-->
                <!--<div class="grids-icon" style="height: 1rem">-->
                    <!--<img src="/Source/Image/mch/切换.png">-->
                <!--</div>-->
                <!--<div class="grids-txt">结算方式</div>-->
            <!--</a>-->

            <!--<a href="<?php echo U('pay_scan_data',array('type'=>1));?>" class="grids-item">-->
                <!--<div class="grids-icon" style="height: 1rem">-->
                    <!--<img src="/Source/Image/mch/sm.png">-->
                <!--</div>-->
                <!--<div class="grids-txt">扫码收款</div>-->
            <!--</a>-->
            <!--<a href="<?php echo U('pay_scan_data',array('type'=>2));?>" class="grids-item">-->
                    <!--<div class="grids-icon" style="height: 1rem">-->
                        <!--<img src="/Source/Image/mch/sk.png">-->
                    <!--</div>-->
                    <!--<div class="grids-txt">定额收款</div>-->
            <!--</a>-->
            <?php if (sys_ad_data()==1 ){ ?>
            <a href="<?php echo ag_ad_data($_SESSION['mch']['aid'],1) ?>" class="grids-item">
                <div class="grids-icon" style="height: 1rem">
                    <img src="/Source/Image/mch/xyk.png">
                </div>
                <div class="grids-txt">信用卡申请</div>
            </a>
            <a href="<?php echo ag_ad_data($_SESSION['mch']['aid'],2) ?>" class="grids-item">
                <div class="grids-icon" style="height: 1rem">
                    <img src="/Source/Image/mch/dk.png">
                </div>
                <div class="grids-txt">贷款超市</div>
            </a>
            <?php } ?>
            <?php if(!empty($wxXwAuth)): ?><a href="<?php echo U('oauthData',array('type'=>'wx'));?>" class="grids-item">
                    <div class="grids-icon" style="height: 1rem">
                        <img src="/Source/statics/wx.png">
                    </div>
                    <div class="grids-txt">微信签约</div>
                </a><?php endif; ?>
            <?php if(!empty($aliAuth)): ?><a href="<?php echo U('oauthData',array('type'=>'ali'));?>" class="grids-item">
                <div class="grids-icon" style="height: 1rem">
                    <img src="/Source/statics/ali.png">
                </div>
                <div class="grids-txt">支付宝签约</div>
            </a><?php endif; ?>
        </div>


        <footer class="m-tabbar tabbar-fixed">
            <a href="<?php echo U('Index/index');?>" class="tabbar-item tabbar-active">
                <span class="tabbar-icon">
                    <i class="icon-home-outline"></i>
                </span>
                <span class="tabbar-txt">主页</span>
            </a>
            <a href="<?php echo U('Index/order');?>" class="tabbar-item">
                <span class="tabbar-icon">
                    <i class="icon-order"></i>
                    <span class="tabbar-dot"></span>
                </span>
                <span class="tabbar-txt">流水</span>
            </a>
            <a href="<?php echo U('Index/my');?>" class="tabbar-item">
                <span class="tabbar-icon">
                    <i class="icon-ucenter-outline"></i>
                </span>
                <span class="tabbar-txt">我的</span>
            </a>
        </footer>
</section>
<script src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="/Source/ydui/js/ydui.js"></script>
<script type="text/javascript" src="/Source/layer/mobile/layer.js"></script>
<script type="text/javascript" src="http://chencunlong.oss-cn-hangzhou.aliyuncs.com/qrcode.js"></script>
<script type="text/javascript">
    var dialog = YDUI.dialog;
    $(document).ready(function() {
        setInterval('autoScroll(".m-rollnotice")', 2000);
    });
    function autoScroll(obj) {
        $(obj).find(".rollnotice-box:first").animate({
            marginTop: "-30px"
        }, 500, function() {
            $(this).css({marginTop:"0px"}).find("div:first").appendTo(this);
        });
    }


    $('.extension').on('click',function (e) {
        var url='<?php echo U('Extension/qrCode');?>';
        $.ajax({
            type: "POST",
            url: url,
            data: {'api':'extension'},
            success: function(data){
                if(data.status == 1){
                    wbs_qrs(data.url);
                }else{
                    dialog.toast(data.info, 'none', 2000);
                }
            }
        });
    });


    function wbs_qrs(urls) {
        layer.open({
            type: 1,
            title: '推广二维码',
            closeBtn: 0,
            shadeClose: true,
            content: '<div class="spread_scan_title">邀请函</div><div class="color_2b spread_scan_tips">我正在使用<?php echo ($_domain['web_name']); ?></div> <div  class="color_2b spread_scan_tips">一站式智能还款轻松零烦恼</div><div id="pay_qr" class="wbscms_payimg"><img src="'+urls+'" width="130" height="130"></div><div class="pay_explain color_2b"><p>直接出示、截屏保存或长按保存二维码发送</p></div></div>'
        });
    }

</script>
</body>
</html>