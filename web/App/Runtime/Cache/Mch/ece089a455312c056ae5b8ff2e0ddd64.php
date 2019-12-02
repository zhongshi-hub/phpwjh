<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>代理管理中心</title>
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport"/>
    <meta content="yes" name="apple-mobile-web-app-capable"/>
    <meta content="black" name="apple-mobile-web-app-status-bar-style"/>
    <meta content="telephone=no" name="format-detection"/>
    <link rel="stylesheet" href="/Source/mui/css/mui.min.css">
    <link rel="stylesheet" href="/Source/ydui/css/ydui.css"/>
    <link rel="stylesheet" href="/Source/statics/plugins/font-awesome/css/font-awesome.min.css"/>
    <link rel="stylesheet" href="/Source/mui/css/mch.css"/>
    <link rel="stylesheet" href="/Source/iconfont/iconfont.css"/>
    <link href="/Source/center/default.css?t=<?php echo time();?>" rel="stylesheet"/>
    <script src="https://a.alipayobjects.com/g/datavis/g2-mobile-all/2.1.20/index.js"></script>
    <style>
        .G-card p{color: #ffffff}
        .tabbar-item.tabbar-active{color: #108ee9}
    </style>
</head>
<body>
<section class="g-flexview">
    <section class="G-card G-salesService">
        <div class="mod1 _ydf" >
            <div class="txt-t">
                <i class="icon-ucenter" style="font-size: 18px"></i>
                <span class="G-txt17" style="font-size: 16px"><?php echo ($_SESSION['ag']['user_name']); ?></span>
            </div>
            <div class="txt-c">
                <div class="G-layout-box">
                    <div class="G-box-col">
                        <p class="sub-t">今日交易金额</p>
                        <p class="sub-b G-txt25"><?php echo ($Day["sum"]); ?><span class="G-txt14">元</span></p>
                    </div>
                    <div class="G-box-col">
                        <p class="sub-t G-fb">今日交易笔数</p>
                        <p class="sub-b G-txt25"><?php echo ($Day["count"]); ?><span class="G-txt14">笔</span></p>
                    </div>
                    <div class="G-box-col">
                        <p class="sub-t">今日新增商户</p>
                        <p class="sub-b G-txt18"><?php echo ($Day["mch"]); ?></p>
                    </div>
                </div>
            </div>
            <div class="txt-b">
                <div class="G-layout-box">
                    <div class="G-box-col">
                        <p class="sub-t label">昨日交易金额</p>
                        <p class="sub-b G-txt18"><?php echo ($Go["sum"]); ?><span class="G-txt14">元</span></p>
                    </div>
                    <div class="G-box-col">
                        <p class="sub-t">昨日交易笔数</p>
                        <p class="sub-b G-txt18"><?php echo ($Go["count"]); ?><span class="G-txt14">笔</span></p>
                    </div>
                    <div class="G-box-col">
                        <p class="sub-t">昨日新增商户数</p>
                        <p class="sub-b G-txt18"><?php echo ($Go["mch"]); ?></p>
                    </div>
                </div>
            </div>
            <div class="txt-b">
                <div class="G-layout-box">
                    <div class="G-box-col">
                        <p class="sub-t">总交易金额</p>
                        <p class="sub-b G-txt18"><?php echo ($To["sum"]); ?><span class="G-txt14">元</span></p>
                    </div>
                    <div class="G-box-col">
                        <p class="sub-t label">总交易笔数</p>
                        <p class="sub-b G-txt18"><?php echo ($To["count"]); ?><span class="G-txt14">笔</span></p>
                    </div>
                    <div class="G-box-col">
                        <p class="sub-t">累计商户数</p>
                        <p class="sub-b G-txt18"><?php echo ($To["mch"]); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <div class="g-scrollview">


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
            <a  class="grids-item" href="<?php echo U('mch_in');?>">
                <div class="grids-icon" style="height: 1rem">
                    <img src="/Source/Image/mch/store.png">
                </div>
                <div class="grids-txt">录入商户</div>
            </a>
            <!-- <a  class="grids-item" onclick="location.href='<?php echo ($ag_decode); ?>'">

                 <div class="grids-icon" style="height: 1rem">
                     <i class="icon iconfont" style="font-size: 40px">&#xe60f;</i>
                 </div>
                 <div class="grids-txt">邀请码</div>
             </a>
             <a  class="grids-item" onclick="not()">
                 <div class="grids-icon" style="height: 1rem">
                     <i class="icon iconfont" style="font-size: 40px">&#xe50d;</i>
                 </div>
                 <div class="grids-txt">更多功能</div>
             </a>-->
            <?php if (sys_agent_status()==1 ){ ?>
            <a  class="grids-item" onclick="location.href='<?php echo ($ag_decode); ?>'">
                <div class="grids-icon" style="height: 1rem">
                    <img src="/Source/Image/mch/agent_yq.png">
                </div>
                <div class="grids-txt">邀请码</div>
            </a>
            <?php } ?>
            <?php if (sys_ad_data()==1 ){ ?>
            <a onclick="ad(1)" class="grids-item">
                <div class="grids-icon" style="height: 1rem">
                    <img src="/Source/Image/mch/xyk.png">
                </div>
                <div class="grids-txt">信用卡申请</div>
            </a>
            <a onclick="ad(2)" class="grids-item">
                <div class="grids-icon" style="height: 1rem">
                    <img src="/Source/Image/mch/dk.png">
                </div>
                <div class="grids-txt">贷款超市</div>
            </a>
            <?php } ?>

        </div>


        <footer class="m-tabbar tabbar-fixed">
            <a href="<?php echo U('Agent/index');?>" class="tabbar-item tabbar-active">
                <span class="tabbar-icon">
                    <i class="icon-home-outline"></i>
                </span>
                <span class="tabbar-txt">主页</span>
            </a>
            <a href="<?php echo U('Agent/mch_data');?>" class="tabbar-item">
                <span class="tabbar-icon">
                    <i class="icon-discover"></i>
                </span>
                <span class="tabbar-txt">商户</span>
            </a>
            <a href="<?php echo U('Agent/qrcode');?>" class="tabbar-item">
                <span class="tabbar-icon">
                    <i class="icon-qrscan"></i>
                    <span class="tabbar-dot"></span>
                </span>
                <span class="tabbar-txt">收款码</span>
            </a>
            <a href="<?php echo U('Agent/my');?>" class="tabbar-item">
                <span class="tabbar-icon">
                    <i class="icon-ucenter-outline"></i>
                </span>
                <span class="tabbar-txt">我的</span>
            </a>
        </footer>
</section>
<script src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="/Source/ydui/js/ydui.flexible.js"></script>
<script src="/Source/ydui/js/ydui.js"></script>
<script src="/Source/fancybox/jquery.fancybox.js"></script>
<link rel="stylesheet" href="/Source/fancybox/jquery.fancybox.css"/>
<script type="text/javascript">
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
    function not() {
        YDUI.dialog.toast('敬请期待!', 'none');
    }
    function ad(type) {
        if(type==1){
            var url="<?php echo ag_ad_qr($_SESSION['ag']['id'],1) ?>";
            var title="信用卡申请二维码";
        }else{
            var url="<?php echo ag_ad_qr($_SESSION['ag']['id'],2) ?>";
            var title="贷款超市二维码";
        }
        $.fancybox({
            content:'<img src="'+url+'" height="300px" width="300px"><p style="text-align: center">'+title+'</p>'
        })
    }
</script>

</body>
</html>